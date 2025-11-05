---
layout: post
title: "Zeebe.io — a horizontally scalable distributed workflow engine"
date: 2019-07-17 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "event-driven", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "!https://cdn-images-1.medium.com/max/800/1ZDy0wSgUZL3rIsAK_j-ZGQ.png"
---
### Zeebe.io — a horizontally scalable distributed workflow engine

#### Say hello to cloud-native workflow automation — part 1
There are [many use cases for workflow automation out there](https://thenewstack.io/5-workflow-automation-use-cases-you-might-not-have-considered/). Many people think that workflow automation is only used for slow and low frequency use cases like human task management. Despite the fact that this is not true (see e.g. [24 Hour Fitness](https://blog.camunda.com/post/2017/11/community-day-san-francisco/) or [Zalando](https://twitter.com/berndruecker/status/910203686793699328)) I do see limitations of current workflow technology in terms of scalability, but on a very different order of magnitude. As traditional engines are based on relational databases they are naturally limited in scale to what that database can handle. Even if this is sufficient for most companies, I know there are definitely interesting use cases requiring more performance and scalability, e.g. to process financial trades which need soft real-time guarantees under a very high load.

![](https://cdn-images-1.medium.com/max/800/1*ZDy0wSgUZL3rIsAK_j-ZGQ.png)

Over the last few years a lot of smart folks at [Camunda](http://camunda.com/) dived deep into the question of how to scale a workflow engine beyond smaller iterative improvements. The result of this thinking lead us to the source-available project [Zeebe.io](https://zeebe.io/). And we’ve [just released the first production-ready version of it](https://zeebe.io/blog/2019/07/announcing-zeebe-0-20-production-ready/)!

Zeebe will push the frontiers of what workflow automation can do as it provides true horizontal scalability. That means that adding nodes to the system will result in being able to process more load — and this increase is linear.

![](https://cdn-images-1.medium.com/max/800/1*xtwqo_eQHXbFzyI6BFvcig.png)

The key ingredients to achieve this are:

- Zeebe is a truly distributed system without any central component, leveraging concepts like [Raft Consensus Algorithm](https://raft.github.io/) for scalability and resilience.
- Zeebe uses event sourcing and event streaming concepts as well as replicated append-only log. Partitioning allows for scaling out.
- It is designed as reactive system according to the [reactive manifesto](https://www.reactivemanifesto.org/).

As a result, Zeebe is in the same class of systems like [Apache Kafka](https://kafka.apache.org/). In early attempts we could process roughly the number of events per second as Kafka, which was a few hundred times faster than [Camunda](http://camunda.com/) 7.8 (which is an example for a traditional workflow engine, and actually even the fastest open source one according to [a study by the university of Lugano in May 2016](http://www.bpm-guide.de/2016/06/12/scientific-performance-benchmark-of-open-source-bpmn-engines/)):

![](https://cdn-images-1.medium.com/max/800/1*MneH_JzZvyoyee2H_-HclA.png)

So how could we achieve this? One important idea is to build an event sourced system.

### An event sourced workflow engine
Traditional workflow engines capture the** current state** of a workflow instance in a database table. If the state changes the database table is updated. Simplified, it looks like this:

![](https://cdn-images-1.medium.com/max/800/1*h92buKDp91nBA32bgPJdzg.png)

Using this approach the workflow engine can leverage a lot of guarantees from the [relational database (RDMS)](https://en.wikipedia.org/wiki/Relational_database_management_system), e.g. [ACID transactions](https://en.wikipedia.org/wiki/ACID).

Zeebe works very differently and leverages [**event sourcing**](https://martinfowler.com/eaaDev/EventSourcing.html). That means that all changes to the workflow state are captured as *events *and these events are stored in an event log alongside *commands. *Both are considered to be *records *in the log. *Quick hint for *[*DDD*](https://en.wikipedia.org/wiki/Domain-driven_design)* enthusiasts: These events are Zeebe internal and related to the workflow state. If you run your own event sourced system within your domain you typically run your own event store for your *[*domain events*](https://martinfowler.com/eaaDev/DomainEvent.html)*.*

![](https://cdn-images-1.medium.com/max/800/1*hUckhSMMQdw-vrIysoFJxw.png)

Records are immutable and therefore the log is **append-only**. Nothing will ever be changed once it is written, it is like a journal in accounting. Append-only logs can be handled and scaled very efficiently, something we will dive deeper into in [part two of this article](https://blog.bernd-ruecker.com/how-we-built-a-highly-scalable-distributed-state-machine-f2595e3c0422).

![](https://cdn-images-1.medium.com/max/800/1*ISoTuuY1qqmX4ch7sFEDcQ.png)

The current state of a workflow can always be derived from these events. This is known as **projection**. A projection in Zeebe is saved internally as snapshot leveraging [RocksDB](https://rocksdb.org/), a very fast key-value store. RocksDB allows Zeebe internally to find certain objects by key, as a pure log would not even allow for simple queries like “give me the current state for workflow instance 2”.

![](https://cdn-images-1.medium.com/max/800/1*4LCppmmxDYCaZDNruQUaHg.png)

Zeebe **stores the log on disk**. Currently this is the only supported storage option (other options like e.g. [Apache Cassandra](http://cassandra.apache.org/) are regularly discussed, but not on the roadmap so far). RocksDB also flushes the snapshot state to disk, which not only creates much faster start-up times, but also allows Zeebe to delete processed records from the log, keeping it quite compact (something we will dive deeper into in [part two of this article](https://blog.bernd-ruecker.com/how-we-built-a-highly-scalable-distributed-state-machine-f2595e3c0422)).

In order to achieve **performance, resilience and scalability **we applied the following distributed computing concepts:

- Peer-to-peer clusters, [Gossip](https://en.wikipedia.org/wiki/Gossip_protocol),
- [Raft Consensus Algorithm](https://raft.github.io/),
- Partitions,
- High-performance computing concepts and the [high-performance protocol gRPC](https://grpc.io/).

I cover this in-depth in part two of this post: [how we built a highly scalable distributed state machine](https://blog.bernd-ruecker.com/how-we-built-a-highly-scalable-distributed-state-machine-f2595e3c0422).

### Zeebe architecture and usage example
Zeebe runs as an own program on a Java Virtual Machine (JVM). Relating to [architecture options to run a workflow engine](https://blog.bernd-ruecker.com/architecture-options-to-run-a-workflow-engine-6c2419902d91) this is the **remote engine** approach, as the application using Zeebe talks remotely with it. But as we leverage **streaming **into the client and use **binary communication protocol** this is very efficient and performant. Its huge advantage is that the broker has a defined setup and environment and cannot be influenced by your application code. So this design decision provides proper isolation, we learned the importance of that in years of experience supporting a workflow engine.

#### Visual workflows
Zeebe uses **visual workflow definitions** in the [ISO standard BPMN](https://camunda.com/bpmn/), which can be modeled graphically with the free [Zeebe Modeler](https://github.com/zeebe-io/zeebe-modeler/).

![](https://cdn-images-1.medium.com/max/800/0*Hmcj6fx4-gRxV8-A.png)

If you prefer you can also use a [YAML to describe workflows,](https://docs.zeebe.io/yaml-workflows/README.html) e.g.:

Please note, that not all language constructs are currently supported in YAML.

#### Native language clients supporting reactive programming, streaming and back-pressure
A workflow can include so called *service tasks*. When an instance reaches these tasks some of your code needs to be executed. This is done by creating *Jobs* which are fetched by *JobWorkers *in your application. Zeebe provides native language clients, e.g. in [Java](https://docs.zeebe.io/java-client/README.html):

or in [NodeJs](https://creditsenseau.github.io/zeebe-client-node-js/):

or in [C#](https://github.com/zeebe-io/zeebe-client-csharp):

or in [Go](https://docs.zeebe.io/go-client/README.html):

Or in [Rust](https://github.com/zeebe-io/zeebe-client-rust) or [Ruby](https://github.com/zeebe-io/zeebe-client-ruby). More languages will follow. And thanks to [gRPC](https://grpc.io/) it is easy to use almost any programming language, as [described in this post of how to use Python](https://zeebe.io/blog/2018/11/grpc-generating-a-zeebe-python-client/).

As you might have spotted in the code, you can use a reactive programming model in your application.

![](https://cdn-images-1.medium.com/max/800/1*Gno2migrFvSmwV_iXbuX7g.png)

You can connect as many clients to Zeebe as you want to and the Jobs will be distributed (currently in a round-robin fashion) allowing for flexible scalability of the workers (up and down). Zeebe will soon support [back-pressure](https://www.reactivemanifesto.org/glossary#Back-Pressure), so making sure that jobs are provided only in a rate a client can process them. No clients can be overwhelmed with work. If in doubt the jobs are saved in Zeebe until new clients connect.

Clients are [competing consumers](http://www.enterpriseintegrationpatterns.com/patterns/messaging/CompetingConsumers.html) which means that one job will only be executed by exactly one of the clients. This is implemented using a lock-event which needs to be written to Zeebe before a job can be executed. Only one client can write that lock-event, other clients trying to do so get an error message. A lock is held for a configurable amount of time before being removed automatically, as Zeebe assumes that the client has died unexpectedly in this case.

#### Transaction and at-least once semantics
It is important to note that Zebee Clients do **not **implement any form of [ACID transaction protocols](https://en.wikipedia.org/wiki/ACID). This means that in case of failures no transaction will be rolled back. With this setup you have two design alternatives:

- You commit the transaction to your domain and afterwards notify Zeebe of the completion of the job. Now your app could crash in between the commit and the complete. So Zeebe would not know that the job is completed and hand it over to another client after the lock timeout. The job will be executed again. The semantic is “**at least once**”.
- You complete the job first and afterwards commit your transaction. If the app crashes in between you probably have completed the job but not committed the transaction. The workflow will have moved on. The semantic is “**at most once**”.

![](https://cdn-images-1.medium.com/max/800/1*rJ5Jda_O5zVbI2CvwTaHYw.png)

Most of the time you will decide to go for “at least once”, as it makes the most sense in the majority of use cases.

As your code might be called multiple times you have to make your application logic **idempotent**. This might be natural in your domain or you might think of other strategies and create an [Idempotent Receiver](http://www.enterpriseintegrationpatterns.com/patterns/messaging/IdempotentReceiver.html) (see e.g. [Spring Integration](https://docs.spring.io/spring-integration/reference/html/messaging-endpoints-chapter.html#idempotent-receiver)). I tackled idempotency briefly in [3 common pitfalls of microservices integration — and how to avoid them](https://www.infoworld.com/article/3254777/application-development/3-common-pitfalls-of-microservices-integrationand-how-to-avoid-them.html) and plan an extended article on it.

### Queries via CQRS
The Zeebe broker is responsible for executing running workflows. It is optimized to apply new commands to the current state in the way to reach the performance and scalability goals mentioned in the beginning. But the broker cannot serve any queries like “what workflow instances were started this morning between 8 and 9 but haven’t finished yet?”. As we are not using a relational database anymore we cannot do simple SELECT statements. We do need a different way to handle the so-called query model in this case.

This way of separating command and query model is known as [Command Query Responsibility Segregation (CQRS)](https://martinfowler.com/bliki/CQRS.html) with big advantages:

> CQRS allows you to separate the load from reads and writes allowing you to scale each independently. […] you can apply different optimization strategies to the two sides. An example of this is using different database access techniques for read and update.

This is exactly what we do with Zeebe. The Broker leverages event streaming and optimizes for high throughput and low latency. But it does not provide query capabilities. That’s why Zeebe provides so called [Exporters](https://docs.zeebe.io/basics/exporters.html) which can access the whole event stream. One [out-of-the-box exporter is for Elasticsearch](https://github.com/zeebe-io/zeebe/tree/master/exporters/elasticsearch-exporter). By using it all events are written to Elastic and stored there, ready to be queried.

Zeebe now comes with an operation tool you can use to look into the workflow engine: [Operate](https://docs.zeebe.io/operate-user-guide/install-and-start.html). You [can see what’s going on, recognize problems (so called incidents) as well as root-causing and fixing incidents](https://zeebe.io/blog/2019/04/announcing-operate-visibility-and-problem-solving/).

![](https://cdn-images-1.medium.com/max/800/0*aADL2jp5tNkGFq2f.png)

Screenshot of OperateOperate is also built to scale and uses its own optimized indices on [Elasticsearch](https://www.elastic.co/de/products/elasticsearch):

![](https://cdn-images-1.medium.com/max/800/0*9rnNlobQvfSAenkz.png)

### Source-available license and open source
One interesting side note goes on open source. You might follow the [latest development around source-available licenses](https://techcrunch.com/2019/05/30/lack-of-leadership-in-open-source-results-in-source-available-licenses/) (e.g. from [Cockroach Labs](https://www.cockroachlabs.com/blog/oss-relicensing-cockroachdb/), [Confluent](https://www.confluent.io/blog/license-changes-confluent-platform), [MongoDB](https://www.mongodb.com/blog/post/mongodb-now-released-under-the-server-side-public-license), [Redis](https://redislabs.com/blog/redis-labs-modules-license-changes/), [Timescale](https://blog.timescale.com/how-we-are-building-an-open-source-business-a7701516a480/)). The background is that cloud vendors can simply take existing open source projects and provide a managed service offering, without paying anything back to the community. And big cloud vendors can typically leverage their market position to compete easily with managed service offerings of the open source companies themselves. This could drain the communities, but also turn into an existential threat for companies with the core developers on the payroll. In the long run this could kill a lot of innovation. Source-available licenses protect open source companies from that threat, even if the [Open Source Initiative (OSI)](https://opensource.org/) doesn’t acknowledge these licenses as open source, hence the clumsy name.

[Zeebe is distributed under The Zeebe Community License](https://zeebe.io/blog/2019/07/introducing-zeebe-community-license-1-0/), a comparable source-available license. It

- Allows what the [MIT license](https://opensource.org/licenses/MIT) allows (basically everything), **except**
- it does **not **allow you to offer a commercial workflow service that uses Zeebe

This license allows for all intended use cases of existing users and customers. It actually “feels” like MIT. You can download, modify, and redistribute Zeebe code. You can include Zeebe in commercial products and services. As long as you don’t offer a generic workflow service.

### Summary
Zeebe is designed as a truly scalable and resilient system without a central database. It is very performant. It can be used together with almost any programming language. It uses visual workflows in BPMN, that allow for true [BizDevOps](https://blog.bernd-ruecker.com/bizdevops-the-true-value-proposition-of-workflow-engines-f342509ba8bb). This combination sets it apart from any orchestration or workflow engine I know of.

It is Open Source (or source-available to be precise) and the usage is pretty simple. So there are no barriers to [get started](https://docs.zeebe.io/introduction/install.html).

Got an appetite to learn more about the distributed computing concepts we used to build Zeebe? Move on to my deep dive article [how we built a highly scalable distributed state machine](https://blog.bernd-ruecker.com/how-we-built-a-highly-scalable-distributed-state-machine-f2595e3c0422).
