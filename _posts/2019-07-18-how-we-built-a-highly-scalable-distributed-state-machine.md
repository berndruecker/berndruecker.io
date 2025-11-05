---
layout: post
title: "How we built a highly scalable distributed state machine"
date: 2019-07-18 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "event-driven", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "!https://cdn-images-1.medium.com/max/800/1ZDy0wSgUZL3rIsAK_j-ZGQ.png"
---
### How we built a highly scalable distributed state machine

#### Say hello to cloud-native workflow automation — part 2

![](https://cdn-images-1.medium.com/max/800/1*ZDy0wSgUZL3rIsAK_j-ZGQ.png)

In [Zeebe.io — a horizontally scalable distributed workflow engine](https://blog.bernd-ruecker.com/zeebe-io-a-highly-scalable-distributed-workflow-engine-45788a90d549) I explained that [Zebee](http://zeebe.io/) is a super performant, highly scalable and resilient cloud-native workflow engine (yeah — buzzwords checked!). I showed how this allows you to [leverage workflow automation in a lot more use cases](https://blog.bernd-ruecker.com/5-workflow-automation-use-cases-you-might-not-have-thought-of-9bdeb0e71996), also in low latency, high-throughput scenarios. I revealed that Zeebe plays in the same league as e.g. Apache Kafka. I hinted at Zeebe’s key ingredients: a truly distributed system without any central component, designed according to top-notch distributed computing concepts, in line with the [reactive manifesto](https://www.reactivemanifesto.org/), applying techniques from high performance computing.

In this article I want to go deeper. I will go over important concepts used in Zeebe and explain decisions we made on the way. This should give you a proper idea of how we entered the new era of cloud-scale workflow automation, which my [co-founder named “big workflow”](https://blog.camunda.com/post/2017/12/camunda-year-in-review/).

But I want to give kudos to the [Zeebe team](https://github.com/orgs/zeebe-io/people) first. Folks —you do truly awesome work and will change the (workflow automation) world! Rock on!

### Event sourcing
Zeebe is based on ideas from [**event sourcing**](https://martinfowler.com/eaaDev/EventSourcing.html). This means that all changes to the workflow state are captured as *events *and these events are stored in an event log alongside *commands. *Both are considered to be *records *in the log. *Quick hint for *[*DDD*](https://en.wikipedia.org/wiki/Domain-driven_design)* enthusiasts: These events are Zeebe internal and related to the workflow state. If you run your own event sourced system within your domain you typically run your own event store for your *[*domain events*](https://martinfowler.com/eaaDev/DomainEvent.html)*.*

I [contrasted this to traditional workflow engine architectures using a RDMS in part 1 of this blog post](https://blog.bernd-ruecker.com/zeebe-io-a-horizontally-scalable-distributed-workflow-engine-45788a90d549).

![](https://cdn-images-1.medium.com/max/800/1*hUckhSMMQdw-vrIysoFJxw.png)

[Events are immutable](https://vimeo.com/52831373) and therefore this event log is **append-only**. Nothing will ever be changed once its written — like a journal in accounting. Append-only logs are relatively easy to handle, because:

- As there are no updates, you cannot have multiple conflicting updates in parallel. Conflicting changes to the state are always captured as two immutable events in a clear sequence, so that the event sourced application can decide how to resolve that conflict deterministically. The counter example is a RDMS: if multiple nodes update the same data in parallel, the updates overwrite each other. This situation must be recognized and avoided. The typical strategy is optimistic or pessimistic locking combined with ACID guarantees of the database. This is not needed with append-only logs.
- There are known strategies to replicate append-only logs.
- It is very efficient to persist these logs, as you always write ahead. Your hard disk simply performs better if you do sequential writes instead of random ones.

![](https://cdn-images-1.medium.com/max/800/1*ISoTuuY1qqmX4ch7sFEDcQ.png)

The current state of a workflow can always be derived from these events. This is known as **projection**. A projection in Zeebe is saved internally as a **snapshot** leveraging [RocksDB](https://rocksdb.org/), a very fast key-value store. This also allows Zeebe to get data via keys. A pure log would not even allow for simple queries like “give me the current state for workflow instance number 2”.

![](https://cdn-images-1.medium.com/max/800/1*4LCppmmxDYCaZDNruQUaHg.png)

#### Log compaction
As the log grows over time, you have to think about deleting old data from it, which is called [**log compaction**](https://dzone.com/articles/kafka-architecture-log-compaction)**.** In an ideal world we could, for example, remove events for all ended workflow instances. Unfortunately this is really complex to do, as events from a single workflow instance might be scattered all over the place — especially if you keep in mind that workflow instances can run for days or even months. Our experiments clearly showed, that it is not only inefficient to do log compaction, but also, the resulting log becomes very fragmented.

We decided to do things differently. As soon as we have completely processed an event and applied it to the snapshot, we delete it right away. I’ll come back to “completely processed” later on. This allows us to keep the log clean and tidy at all times, without losing the benefits of an append-only log and stream processing — as described in a minute.

**Storage**Zeebe **writes the log to disk **and RocksDB also flushes its state to disk. Currently this is the only supported option. We regularly discuss making storage logic pluggable — for example support Cassandra — but so far we’ve focused on file system and it might even be the best choice for most use cases, as it is simply the fastest and most reliable option.

### The single writer principle
When you have multiple clients accessing one workflow instance at the same time, you need to have some kind of conflict detection and resolution. When you use a RDMS this is often implemented via [optimistic locking](https://en.wikibooks.org/wiki/Java_Persistence/Locking#Optimistic_Locking) or some database magic. With Zeebe we solve this by using the [Single Writer Principle](http://mechanical-sympathy.blogspot.co.uk/2011/09/single-writer-principle.html). As [Martin Thompson wrote](https://github.com/real-logic/aeron/wiki/Design-Principles):

> Contended access to mutable state requires mutual exclusion or conditional update protection. Either of these protection mechanisms cause queues to form as contended updates are applied. To avoid this contention and associated queueing effects all state should be owned by a single writer for mutation purposes, thus following the [Single Writer Principle](http://mechanical-sympathy.blogspot.co.uk/2011/09/single-writer-principle.html).

So independent of the number of threads on our machine, or the overall size of the Zeebe cluster, there is always **exactly one thread** that writes to a certain log. This is great: the ordering is clear, no locking is needed and no deadlock can occur. You do not waste time managing contention but can do real work all the time.

If you wonder if that means that Zeebe only leverage one thread to do the workflow logic, you are right so far! I will talk about scaling Zeebe later on.

#### The event handling loop
In order to understand a bit better what that single thread is doing, let’s have a look at what happens if a client wants to complete a task within a workflow:

zeebe.newCompleteCommand(someTaskId).send()

![](https://cdn-images-1.medium.com/max/800/1*wW-UR3BIE4uGqknqxpVyrg.png)

- The client sends the command to Zeebe, this is a non-blocking call, but you get a *Future *to receive a response later if you like.
- Zeebe appends the command to its log.
- The log is stored on disk (and replicated — I tackle this later).
- Zeebe checks some invariants (“Can I really process this command now?”), alters the snapshot and creates new events to be written to the log.
- Immediately after the invariants are checked the response to the client is sent, even if the new events are not yet written to the log. This is safe because even if the system crashes now, we can always replay the command and get exactly the same result again.
- Resulting events are appended to the event log.
- The log is stored on disk and replicated.

If you are deep in transactional thinking you might ask one question: “Nice — but what if we alter the RocksDB state (step 4) and the system crashes before we write the events to the log (step 6&7)?” Great question! Zeebe only validates a snapshot once all events are processed. In any other case an older snapshot is used and events are re-processed.

### Stream processing and exporters
I was talking about event sourcing earlier. Actually, there is a related concept that is important: [**stream processing**](https://en.wikipedia.org/wiki/Stream_processing). The append-only log consisting of events (or records to be precise) is a constant stream of events. Zeebe internally is built upon the concept of processors, each of them being a single thread (as described above). The most important processor is actually implementing the BPMN workflow engine part, so it understands commands and events semantically and knows what to do next. It is also responsible for rejecting invalid commands.

![](https://cdn-images-1.medium.com/max/800/1*asS6WoX1wLmQqDjJMyrcdw.png)

But there are more stream processors, most importantly [exporters](https://docs.zeebe.io/basics/exporters.html). These exporters also process every event of the stream. One [out-of-the-box exporter is writing all data to Elasticsearch,](https://github.com/zeebe-io/zeebe/tree/master/exporters/elasticsearch-exporter) where it can be retained for the future and also queried. For example, the Zeebe operation tool [Operate](https://docs.zeebe.io/operate-user-guide/README.html) is leveraging this data to visualize the state of running workflow instances, incidents, etc.

But you could have other exporters as well. The [Zeebe Community](https://github.com/zeebe-io/awesome-zeebe) already came up with various exporters, for example, to [Apache Kafka](https://github.com/zeebe-io/zeebe-kafka-exporter), [Hazelcast](https://github.com/zeebe-io/zeebe-hazelcast-exporter) or [Event Store](https://github.com/jwulf/zeebe-eventstore-exporter).

Every exporter knows to which log position it has read the data. Data will be deleted, as described in log compaction above, as soon as all stream processors have successfully processed it. The trade-off here is, that you can’t add a new stream processor later on and let it replay all events from history, like you could do in Apache Kafka.

### Peer-to-peer clusters
To provide fault-tolerance and resilience you run multiple Zeebe brokers which form a peer-to-peer cluster. We designed this in a way that it does not need any central component or coordinator, hence there is no single point of failure.

![](https://cdn-images-1.medium.com/max/800/1*TiKv6M45ZZX9PWvE7xmzlA.png)

To form a cluster you need to configure at least one other broker as a known contact point in your broker. During the startup of a broker, it talks to this other broker and fetches the current cluster topology. Afterwards the [Gossip protocol](https://en.wikipedia.org/wiki/Gossip_protocol) is used to keep the cluster view up-to-date and in-sync.

### Replication using the Raft Consensus Algorithm
Now the event log must be replicated to other nodes in the network. Zeebe uses distributed consensus — more specifically the [Raft Consensus Algorithm](https://raft.github.io/) — to replicate the event-log between brokers. [Atomix](https://atomix.io/) is used as implementation. There is an awesome [visual explanation of the Raft Consensus Algorithm](http://thesecretlivesofdata.com/raft/) available online, so I will not go into all the details here.

![](https://cdn-images-1.medium.com/max/800/1*Nqptv7Els4Jlf0xAaDV0IQ.png)

[http://thesecretlivesofdata.com/raft/](http://thesecretlivesofdata.com/raft/)The basic idea is that there is a **single leader** and **a set of followers**. When the Zeeber brokers start up they will elect a leader. As the cluster constantly gossips, the brokers recognize if a leader has gone down or disconnected and try to elect a new leader.

![](https://cdn-images-1.medium.com/max/800/1*QxkXQwLt0ad2kTsc5KilWw.png)

Only the leader is allowed write access to the data. The data written by the leader is replicated to all followers. Only after a successful replication are the events (or commands) processed within the Zeebe broker. If you are familiar with the [CAP theorem](https://en.wikipedia.org/wiki/CAP_theorem), it means that we decided for consistency and not for availability, so **Zeebe is a CP system. **(I apologize to [Martin Kleppmann](https://twitter.com/martinkl) who wrote [Please stop calling databases CP or AP,](https://martin.kleppmann.com/2015/05/11/please-stop-calling-databases-cp-or-ap.html) but I think it helps in understanding the architecture of Zeebe).

We tolerate partitioning of the network as we have to tolerate partitioning in every distributed system, you simply have no influence on this (see [http://blog.cloudera.com/blog/2010/04/cap-confusion-problems-with-partition-tolerance/](http://blog.cloudera.com/blog/2010/04/cap-confusion-problems-with-partition-tolerance/) and [https://aphyr.com/posts/293-jepsen-kafka](https://aphyr.com/posts/293-jepsen-kafka)). We decided for consistency instead of availability, as consistency is one of the promises for use cases of workflow automation.

An important configuration option is the **replication group size**. In order to elect a leader, or to successfully replicate data, you need a so called **quorum**, which means a certain number of acknowledgements of other Raft members. Because we want to guarantee consistency, Zeebe requires a quorum ≥(replication group size / 2) + 1. Let’s make a simple example:

- Zeebe nodes: 5
- Replication group size: 5
- Quorum: 3

![](https://cdn-images-1.medium.com/max/800/1*x3lT8w4R0p2TN6JYnD9PnQ.png)

So we can still work if there are **3 nodes** reachable. In case of a partition like the one sketched above, only one network segment can reach quorum and continue to work — the other two nodes will not be able to do anything. So if you are a client in the network segment with these two nodes, you cannot work anymore, thus availability is not guaranteed. A CP system.

This **avoids **the so called **split-brain phenomena**, as you cannot end up with two network segments doing conflicting work in parallel. A [good in-depth discussion can be found in the forum](https://forum.zeebe.io/t/split-brain-possibility/48).

**Replication**When log entries are written by the leader they are first replicated to the followers before they will be executed.

![](https://cdn-images-1.medium.com/max/800/1*wW-UR3BIE4uGqknqxpVyrg.png)

That means every log entry that gets processed is guaranteed to be correctly replicated. And the replication guarantees that no committed log entry is ever lost. Larger replication group sizes allow a higher fault tolerance but increase the traffic on your network. As replication to multiple nodes is done in parallel, it might actually not have a big influence on latency. Also the broker itself is not blocked by replication, as this can be efficiently processed (as I describe further down when talking about ring buffers).

Replication is also the strategy to overcome challenges around writing to disk in virtualized and containerized environments. Because in these environments you have no control when data is really physically written on disk. Even if you call *fsync *and it tells you that the data is safe, it might not be. But we prefer to have the data in the memory of a couple of servers rather than on the disk of one of them.

While replication might add latency to the processing of a command within Zeebe, it does not affect throughput much. The stream processors within Zeebe are not blocked by waiting for the answer of a follower. So Zeebe can continue processing at a fast pace — but the client waiting for his response might need to wait a bit longer.

### The gateway
In order to start a new workflow instance or complete a task, you need to talk to Zeebe. The easiest way to do this is to leverage one of the ready-to-use language clients, e.g. in [Java](https://docs.zeebe.io/java-client/README.html), [NodeJs](https://creditsenseau.github.io/zeebe-client-node-js/), [C#](https://github.com/zeebe-io/zeebe-client-csharp), [Go](https://docs.zeebe.io/go-client/README.html), [Rust](https://github.com/zeebe-io/zeebe-client-rust) or [Ruby](https://github.com/zeebe-io/zeebe-client-ruby). And thanks to [gRPC](https://grpc.io/) it is easy to use almost any programming language, as [described in this post on how to use Python](https://zeebe.io/blog/2018/11/grpc-generating-a-zeebe-python-client/). In [part 1 of this blog post I showed client code examples for different languages](https://blog.bernd-ruecker.com/zeebe-io-a-horizontally-scalable-distributed-workflow-engine-45788a90d549).

![](https://cdn-images-1.medium.com/max/800/1*Y-sfHlwof7N_CGTPz2ZKrg.png)

The client talks to the Zeebe gateway, which knows the Zeebe broker cluster topology and routes the request to the correct leader for that request. This design makes it also very easy to run Zeebe in the cloud or also in Kubernetes, as only the Gateway needs to be accessible from the outside.

**Scale out by partitioning**So far we talked about having exactly one thread processing all work. If you want to leverage more than one thread you have to create **partitions**. Every partition represents a separate physical append-only log.

![](https://cdn-images-1.medium.com/max/800/1*nOmrKz8fgHaGUkwmbxhsKA.png)

Every partition has its own single writer, which means you can use partitions to scale. The partitions can be assigned to

- different threads on a single machine or
- different broker nodes.

Every partition forms an own Raft group, hence every partition has its own leader. If you run a Zeebe cluster, one node can be a leader for one partition and a follower of others. This might be a very efficient way to run your cluster.

![](https://cdn-images-1.medium.com/max/800/1*XMGTYlDN7LLrJXEA2guaZw.png)

All events related to one workflow instance must go onto the same partition, otherwise we would violate the single writer principle and also make it impossible to recreate the current state in a broker node locally.

One challenge is how to decide which workflow instance goes onto which partition. Currently this is a simple round robin mechanism. When you start a workflow instance, the gateway will put it into one partition. The partition id will even get part of the workflow instance id, making it very easy for every part of the system to know for every single workflow instance the partition it is in.

![](https://cdn-images-1.medium.com/max/800/1*mzNCWpQ7LDuvubwxi2b7Sw.png)

One interesting use case is **message correlation**. [A workflow instance might wait for messages (or events) to arrive](https://docs.zeebe.io/bpmn-workflows/message-events.html). Typically that message does not know the workflow instance id, but correlates to other information, let’s say an order-id. So Zeebe needs to find out if any of the workflow instances are waiting for a message with that order-id. How to make that efficiently and horizontally scalable?

![](https://cdn-images-1.medium.com/max/800/1*CaNmes4xooO7S4rHseokNg.png)

Zeebe simply creates a message subscription which lives on one partition that might be different to the one of the workflow instance. The partition is determined by a hash function on the correlation identifier and thus can easily be found either by a client handing in the message, or by a workflow instance arriving at the point where it needs to wait for that message. It does not even matter which order this happens in (see [message buffering](https://docs.zeebe.io/reference/message-correlation.html#message-buffering)) as there can’t be conflicts thanks to the single writer. The message subscription always links back to the waiting workflow instance — probably living on another partition.

Please note, that the number of partitions is static in the current Zeebe version. You can’t change it once your broker cluster is in production. While this might change in future versions of Zeebe, it is definitely important to plan for a sensible number of partitions for your use case right from the beginning. There is a [production guide](https://tinyurl.com/zeebe-prg) helping you on core decisions.

#### Multi data-center replication
Users often ask for **multi data-center** replication. Currently there is no special support (yet). A Zeebe cluster can technically span multiple data-centers, but you have to prepare for increased latency. If you set up your cluster in a way that quorum can only be reached by nodes from both data centers you will survive even epic disasters, at the cost of latency.

### Why not leverage Kafka or Zookeeper?
A lot of people ask why we write all of the above ourselves and do not simply leverage a cluster manager like Apache Zookeeper or even a fully fledged Apache Kafka. Here are the main reasons for this decisions:

- Ease of use and **ease of getting started**. We want to **avoid third-party dependencies** that need to be installed and operated before Zeebe can be used. And Apache Zookeeper or Apache Kafka are not easy to operate. We strive for a very simple getting started experience (run a docker image or unzip the distro and run one script), even if we do envision sophisticated Zeebe deployments processing very high loads.
- **Efficiency**. Having the cluster management in the core broker allows us to optimize it for our concrete use case, which is workflow automation. A couple of features would be harder if build around an existing generic cluster manager.
- **Support and control**. In our long experience as an open source vendor we’ve learned that it is really hard to support third-party dependencies at this core level. Of course we could start hiring core Zookeeper contributors, but it will still be hard as there are multiple parties at the table, so the direction of these projects is not under our own control. With Zeebe we invest in having control over the full stack, allowing us to drive full speed into the direction we envision.

There is also a [FAQ on the Zeebe homepage](https://zeebe.io/faq/#is-zeebe-built-on-kafka) on this.

### Design for performance
Apart from scalability, Zeebe is also built for high performance on a single node from ground up.

So for example we always strive to **reduce garbage**. Zeebe is written in Java. Java has so called [garbage collection](https://en.wikipedia.org/wiki/Garbage_collection_(computer_science)) which cannot be turned off. The garbage collector regularly kicks in and checks for objects that it can remove from memory. During garbage collection your system is paused — and the duration depends on the amount of objects checked or removed. This pause can add noticeable latency to your processing, especially if you process millions of messages per second. So Zeebe is programmed in a way to **reduce garbage**.

Another strategy is to use [**ring buffers**](https://en.wikipedia.org/wiki/Circular_buffer) and taking advantage of **batching** statements wherever possible. This also allows you to use multiple threads without violating the single writer principle described above. So whenever you send an event to Zeebe, the receiver will add the data to a buffer. From there another thread will actually take over and process the data. Another buffer is used for bytes that need to be written to disk.

This approach enables batch operations. Zeebe can write a pile of events to disk at one go; or send a couple of events in one network roundtrip to a follower.

Remote communication is done very efficiently using **binary protocols** like [gRPC](https://grpc.io/) to the client and a simple binary protocol internally.

### Get going!
Feel free to dive into the code if you like!

[**zeebe-io/zeebe**
*Distributed Workflow Engine for Microservices Orchestration - zeebe-io/zeebe*github.com](https://github.com/zeebe-io/zeebe)Of course you don’t need to understand the code to [get started](https://docs.zeebe.io/introduction/quickstart.html) and use Zeebe for your own use cases!

### Summary
Zeebe is a completely new class of workflow/orchestration engine for cloud-native and cloud-scale applications. What sets Zeebe apart from all other orchestration/workflow engines is its performance and the fact that it is designed as a truly scalable and resilient system without any central component, or the need for a database.

Zeebe does not follow the traditional idea of the transactional workflow engine where state is stored in a shared database and updated as it moves from one step in the workflow to the next. Instead, Zeebe works as an event sourced system on top of replicated, append-only logs. So Zeebe has a lot in common with systems like Apache Kafka. Zeebe clients can pub/sub to execute work thus being fully reactive.

Contrary to other microservice orchestration engines on the market, Zeebe puts a strong focus on visual workflows as we believe that visual workflows are key for providing visibility into asynchronous interactions, at [design time, runtime and during operations](https://blog.bernd-ruecker.com/bizdevops-the-true-value-proposition-of-workflow-engines-f342509ba8bb).

With this article I’ve hopefully given you a good introduction to Zeebe, not just from the user perspective, but also a deeper dive into relevant concepts. I hope you enjoyed that as much as I did.
