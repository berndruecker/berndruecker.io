---
layout: post
title: "How to implement long running flows, sagas, business processes or similar"
date: 2017-03-22 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "architecture"]
author: Bernd Ruecker
excerpt: "!/assets/images/how-to-implement-long-running-flows-sagas-business-processes-1.jpeg"
canonical: https://blog.bernd-ruecker.com/how-to-implement-long-running-flows-sagas-business-processes-or-similar-3c870a1b95a8
---
### How to implement long running flows, sagas, business processes or similar

![](/assets/images/how-to-implement-long-running-flows-sagas-business-processes-1.jpeg)

Long running flows can span from a few milliseconds up to several months or years (see [What are long running processes?](https://blog.bernd-ruecker.com/what-are-long-running-processes-b3ee769f0a27) for details). Note that after some discussions I switched my wording from “*long running processes*” to “*long running flows*”.

When implementing these flows within your business application (or microservice architecture) you have to think about certain requirements, especially **state handling** and subsequent challenges like monitoring and versioning. Visualization of flows might come in handy. How can you tackle these requirements? I want to discuss the basic alternatives in this article:

- Storing state in domain entities
- Storing state in separate state entities
- Using a state machine
- Using a routing slip to avoid any central state

### Storing state in domain entities
The simplest option to get started is to add some state information to already existing entities. Think of a simple order application, then it might look like this:

Advantages:

- Easy to setup
- Easy to query for state and entity information (“all orders containing blue umbrellas for male customers, which are payed but not yet delivered”). This also holds true for reporting.

Disadvantages:

- State handling is hard coded. It is not easy to understand when and why state changes are triggered, which makes changing the flow hard.
- All subsequent requirements have to be self-coded. If you need monitoring for stuck processes or timeouts/escalations to happen, you need to do it yourself. Another big topic for you to solve is versioning of the flow because whenever changing the flow you will have entities in some intermediary state to handle.

Once bitten, twice shy! Hence I am personally not a fan of this alternative, as I have seen it too often growing uncontrollably into a home-grown state machine (I have described this in [the 7 sins of workflow](https://blog.bernd-ruecker.com/the-7-sins-of-workflow-b3641736bf5c)).

However, if your requirements are simple enough, and you are sure it will stay like this for a while, you can go down this route. Just be honest with yourself — don’t do it just because you have a bad feeling about existing tools without concrete reasons. You may have based this on misconceptions which I plan to describe to you below. Sorry — once bitten…

### Storing state in separate state entities
A somehow similar concept is to store the state in entities but separated from your core domain entities. So you could have the following entity for the order:

This alternative has the advantage that the state is now clearly separated from your core domain entities, keeping the concepts pretty clear. And it does not introduce big downsides, all other forces are comparable to saving the state in the domain entities.

### Using a state machine
You can leverage existing state machines, often named **workflow engine, process engine, orchestration engine** or the like. Using for example the open source Camunda library you could express the flow like this:

As you can see there are some classes containing logic attached. Afterwards this flow can be executed on the engine directly. The engine will take care of state handling and can even visualize the flow graphically, e.g. for later monitoring (as it also records a lot of audit data):

![](/assets/images/how-to-implement-long-running-flows-sagas-business-processes-2.png)

You could also use a graphical modeler to create the flow which is especially helpful if the flow gets more complex than our simple example. The good thing is: it is totally up to you to decide if you want to use code or graphics. Either way you can leverage visualization at least in operation.

The graphical flow looks like this, Camunda uses the ISO standard BPMN for modeling and visualization:

![](/assets/images/how-to-implement-long-running-flows-sagas-business-processes-3.png)

Advantages:

- The engine does all state handling and provides the required features for long running flows (monitoring, timers, versioning, …).
- You gain visibility of the flow which might be interesting during requirements engineering (how to implement this?), development (what do I have to implement right here?) and operations (is everything running smooth? where exactly do we have problems?). Recommended read: [BizDevOps — the true value proposition of workflow engines](https://blog.bernd-ruecker.com/bizdevops-the-true-value-proposition-of-workflow-engines-f342509ba8bb)
- You also get additional tools for advanced use cases. As an operator you could for example adjust the state for a certain flow in case of errors.

Disadvantages:

- You introduce a new component to your stack.
- A lot of state machine vendors still follow a “zero-code idea” (also described in [the 7 sins of workflow](https://blog.bernd-ruecker.com/the-7-sins-of-workflow-b3641736bf5c)) typically leading to inflexible architectures and frustrated developers. So you have to be careful *to pick the right tool*.
- Many people think about enterprise wide central BPM approaches when saying “BPMN”. Doing so might end up in a BPM monolith (see [the 7 sins of workflow](https://blog.bernd-ruecker.com/the-7-sins-of-workflow-b3641736bf5c)). So you have *to apply the tool right*.

Rejecting state machines or appropriate engines is often done as a result of misconceptions — what a bummer! When selecting the right product and applying it properly later on, you can leverage the advantages without realizing the risks mentioned.

The state machine market is cluttered making the process of product selection challenging. Quick research reveals already 5 market categories:

- Lightweight workflow engines. Examples: [Camunda](http://camunda.org/), [Activiti](http://activiti.org/), [JBoss jBPM](http://jbpm.jboss.org/).
- BPM suites following the “zero-code” approach. Examples: IBM, Pega, Software AG.
- Pure state machines with DSL. Examples: [Amazon Simple Workflow](https://aws.amazon.com/swf/) (cloud only), [Netflix conductor](https://netflix.github.io/conductor/).
- Simple “event reaction machines”. Examples: [IFTTT](https://ifttt.com/), [Zapier](https://zapier.com/), [Microsoft Flow](https://flow.microsoft.com/en-us/).
- Data flow frameworks for big data or ETL. Examples: [Spring Cloud Data Flow](https://cloud.spring.io/spring-cloud-dataflow/), [Apache Airflow](https://airflow.incubator.apache.org/).

To make the decision possible, you must first think about your use case. The order example in this post is best solved with a tool from the first category because you can easily leverage a lightweight engine but do complex flows with it. Additionally that provides you with a lot of additional features like visualization.

Sample code for the order process using Camunda is part of [the flowing retail example](https://github.com/flowing/flowing-retail).

### No central state, but a routing slip
I recently had an interesting discussion about using [Routing Slips](http://www.enterpriseintegrationpatterns.com/patterns/messaging/RoutingTable.html). I want to include it in this post as the pattern is relatively unknown but could also provide a solution.

Let’s assume our order process is handled by multiple microservices which communicate via messages or events. You can [see this in action in the flowing retail example](https://github.com/flowing/flowing-retail). Using a routing slip, the steps to process an order are:

- Process event “order created” by a very thin order service and create the proper routing slip for this order (e.g. “do payment”, “pick goods” and “ship goods” — the commands from the flowing retail example). Now pass the event onto the bus.
- A thin layer between the event bus and the client can read the routing slip so the payment service will recognize it is next in the routing slip and do the payment. It marks the “do payment” as done or removes it from the slip.
- The payment sends the “payment received” event including the routing slip and inventory recognizes it is next on the routing slip. The big difference now is: There was no central component required to say what is next, everything was written in the routing slip at the beginning!
- And so on.

Advantages:

- No central state handling required

Disadvantages:

- Hard to investigate the current order state as you have to find the latest version of the routing slip. This might be solved by some [CQRS](https://martinfowler.com/bliki/CQRS.html) query component investigating all routing slips and gather information.
- No easy way of changing the route when the message is on its way.
- I do not know any out-of-the-box components solving this so it always involves coding

So far, I have not found a very good reason to prefer the routing slip over the engine approach for the kind of flow shown in this example but I hope this post might trigger some discussions or comments.

### Conclusion
There are different approaches to handle the state for long running flows. When researching on the web you get the impression that only state in entities is really known, especially if you face Microservices or Sagas (from the Domain Driven Design Community).

Personally, I think the state machine approach is most often better suited which is not that surprising: You have state, use a state machine. But in the past most existing tools were too complicated and scared developers away. That was the reason why we created the open source platform [Camunda](http://camunda.org/) in the first place. And it has really changed in the recent years, there are now lightweight engines available which deserve a honest look at.

As for the question what approach to use: as always — it depends.
