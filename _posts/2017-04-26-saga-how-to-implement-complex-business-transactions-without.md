---
layout: post
title: "Saga: How to implement complex business transactions without two phase commit."
date: 2017-04-26 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "architecture"]
author: Bernd Ruecker
excerpt: "A good overview on Sagas can be found by watching the talk by Caitie McCaffrey:"
canonical: https://blog.bernd-ruecker.com/saga-how-to-implement-complex-business-transactions-without-two-phase-commit-e00aa41a1b1b
---
### Saga: How to implement complex business transactions without two phase commit.
I talked about Sagas more than 3 times this week so it is time to write a blog post about it.

### What is a Saga?
The Saga pattern describes how to solve distributed (business) transactions without two-phase-commit as this does not scale in distributed systems. The basic idea is to break the overall transaction into multiple steps or activities. Only the steps internally can be performed in atomic transactions but the overall consistency is taken care of by the Saga. The Saga has the responsibility to either get the overall business transaction completed or to leave the system in a known termination state. So in case of errors a business rollback procedure is applied which occurs by calling compensation steps or activities in reverse order.

A good overview on Sagas can be found by watching the talk by Caitie McCaffrey:

A classic example is a trip booking:

![](/assets/images/saga-how-to-implement-complex-business-transactions-without-1.png)

The pattern gains more traction recently as systems get more and more complex, distributed and remote — so the “good old” ACID transactions have finally reached their limit. Say hello to eventual consistency. The original Saga pattern by the way pretty is old ([https://www.cs.cornell.edu/andru/cs711/2002fa/reading/sagas.pdf](https://www.cs.cornell.edu/andru/cs711/2002fa/reading/sagas.pdf)).

You find Sagas in different communities often with slightly different wording. Pat Helland from Amazon states this in his famous “**Life beyond Distributed Transactions**” paper:

> The assumptions that lead us to entities and messages, lead us to the conclusion that the scale-agnostic application must manage uncertainty itself using workflow if it needs to reach agreement across multiple entities.

In Domain Driven Design (**DDD**) the pattern is well known as you need to apply it as soon as you have use cases involving multiple bounded contexts to collaborate. In the **microservice **community it is less known but necessary whenever an overall flow involves multiple services.

### How to implement a Saga?
A Saga includes state handling as you need to remember what you already did in order to potentially execute compensation activities. So a Saga is a long running flow and you can use all variants from [How to implement long running flows](https://blog.bernd-ruecker.com/how-to-implement-long-running-flows-sagas-business-processes-or-similar-3c870a1b95a8).

But I want to highlight one pretty cool approach here: Flow engines based on BPMN (Business Process Model and Notation) can directly execute compensation out of the box as it is provided by the BPMN language itself. The example from above can be modeled like this:

![](/assets/images/saga-how-to-implement-complex-business-transactions-without-2.png)

The model can be directly executed by a corresponding engine. The compensating activities are connected to the original activity and the engine takes care of the rest. So a workflow engine capable of BPMN can act as Saga Coordinator!

If you feel uneasy with graphical notations (as you might suspect something is magically hidden in the model or you just hate graphical modelers and the “death-by-properties-panel” phenomena) you can also express the same Saga in code. The following code uses the Model API of [Camunda](http://camunda.org/) in the background. Camunda is a lightweight open source engine which is able to execute BPMN natively. Camunda (as from version 7.7 on) can also generate the graphical representation for flows defined in code. This makes it easy to define a very readable Saga (here using Java code):

You can find all the details in the [working example application on GitHub](https://github.com/flowing/flowing-trip-booking-saga). *Edit*: I recently also uploaded the same [example using C# code on GitHub](https://github.com/flowing/flowing-trip-booking-saga-c-sharp).

The graphical representation will shown up in monitoring as it is auto-generated (there are minor glitches in the graphics we will resolve soon — but it is already pretty cool):

![](/assets/images/saga-how-to-implement-complex-business-transactions-without-3.png)

Let’s quickly recap what Caitie mentioned you need for Sagas:

- **Durable Saga log** — this is implemented by the workflow engine
- **SEC Process** (Execution Coordinator) — this is implemented by the workflow engine
- **Idempotence of compensating actions** — this is up to you in this scenario as it has to be implemented by the services you call in your Saga (e.g. cancelling the car booking).

Checked :-)

### A workflow engine for Saga?
I sometimes get objections when proposing a workflow engine for Sagas — too complicated, too heavyweight, low performance, or does not scale well. But that is just a matter of picking the right tool. Avoid [the 7 sins of workflow](https://blog.bernd-ruecker.com/the-7-sins-of-workflow-b3641736bf5c)— use **lightweight engines **(like Camunda) which can even be embedded as simple library as you can see in the source code or [the flowing retail example](https://blog.bernd-ruecker.com/flowing-retail-demonstrating-aspects-of-microservices-events-and-their-flow-with-concrete-source-7f3abdd40e53). So I see no reason not to use such an engine for this problem at hand.

### Process Manager (Orchestration) or Choreography
The solution I propose is to use a [process manager](http://www.enterpriseintegrationpatterns.com/patterns/messaging/ProcessManager.html) for the Saga pattern. There is quite some discussion going on if a Saga can be implemented by this. It is often argued that this is not a good idea as it introduces a single point of failure and doesn’t scale well. The alternative is a choreographed approach as for example the [routing slip](http://www.enterpriseintegrationpatterns.com/patterns/messaging/RoutingTable.html). A good overview on this is given [by Clemens Vasters](http://vasters.com/archive/Sagas.html).

To be clear: All are valid implementations for the Saga pattern, compare it for example to [Arnon Rotem-Gal-Oz](http://arnon.me/2013/01/saga-pattern-architecture-design/):

![](/assets/images/saga-how-to-implement-complex-business-transactions-without-4.jpeg)

> “I see sagas as the notion of getting distributed agreement of a process with reduced guarantees […] — So, basically, a Saga is loose transaction-like flow where, in case of failures, involved services perform compensation steps […]

> **Under this definition both centrally managed processes and a “choreographed” processes are Sagas **— as long as the semantics and intent mentioned above are kept. The centrally managed orchestration provides visibility of processes, ease of management etc; The cooperative event based, context shared sagas provide flexibility and allow serendipity of new processes; Both have merit and both have a place, at least in my opinion :)

So for me the choice is not so religious but very practical: Which solution can meet my requirements?

Let’s assume the reasons for not using a process manager can be addressed than this solution has advantages: You can easily inspect the state of your Saga in the process manager and you can leverage additional features of corresponding tools like visibility of the flow and versioning of your Saga definition.

And so far, I saw typical customer requirements being addressable with lightweight workflow engines perfectly. One important thing to keep in mind is: You do not have to run some central single-point-of-failure-workflow-engine but you can embed these engines within your component at hand implementing the Saga. So for the above example you could have a “trip booking” bounded context or microservice:

![](/assets/images/saga-how-to-implement-complex-business-transactions-without-5.png)

The engine is one library you leverage within that service to implement the Saga easier (than coding it yourself). Similar thoughts can be found in [the flowing retail example](https://blog.bernd-ruecker.com/flowing-retail-demonstrating-aspects-of-microservices-events-and-their-flow-with-concrete-source-7f3abdd40e53) and the blog post on [service collaboration with choreography and orchestration](https://blog.bernd-ruecker.com/why-service-collaboration-needs-choreography-and-orchestration-239c4f9700fa).

Within Camunda we are by the way working on true horizontal scalability for this engine which will then be based on append-only logs. When having that it becomes also be thinkable to have a scalable and resilient “Saga infrastructure” for every environment. But I’m just thinking out loud ;-)

### Conclusion
Saga is an important pattern to be known in distributed systems. The architectural pattern doesn’t imply a specific implementation, my take would be a lightweight workflow engine capable of BPMN like I showed with the [Camunda](https://camunda.org/) example above. This has already been proven to work in a lot in a lot of real-life projects.

As always, I am happy about feedback and discussion.

*Stay up to date with my *[*newsletter*](http://eepurl.com/cDXPRj)*.*
