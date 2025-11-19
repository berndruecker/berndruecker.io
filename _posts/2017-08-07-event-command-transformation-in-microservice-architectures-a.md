---
layout: post
title: "Event command transformation in microservice architectures and DDD"
date: 2017-08-07 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "event-driven", "architecture"]
author: Bernd Ruecker
excerpt: "!/assets/images/event-command-transformation-in-microservice-architectures-a-1.png"
canonical: https://blog.bernd-ruecker.com/event-command-transformation-in-microservice-architectures-and-ddd-dd07d5eb9656
---
### Event command transformation in microservice architectures and DDD
In the articles [*Why service collaboration needs choreography AND orchestration*](https://blog.bernd-ruecker.com/why-service-collaboration-needs-choreography-and-orchestration-239c4f9700fa)* *and [Know the Flow! Microservices and Event Choreographies (InfoQ)](https://www.infoq.com/articles/microservice-event-choreographies) there was already a lot of information on event command transformation. Today I want to reiterate the problems it solves to make it crystal clear. This time I use concrete Java source code (simplified and reduced to the minimum) and again an order fulfillment business example (copied from [the flowing retail example](https://blog.bernd-ruecker.com/flowing-retail-demonstrating-aspects-of-microservices-events-and-their-flow-with-concrete-source-7f3abdd40e53)). The pure event flow looks like this:

![](/assets/images/event-command-transformation-in-microservice-architectures-a-1.png)

A flow of pure event notification can be easily implemented. The following code shows a simplified version in Java, working source code for all examples is available on [https://github.com/flowing/flowing-retail-concept-java](https://github.com/flowing/flowing-retail-concept-java):

Now let’s assume that you need to implement a new business requirement: “*VIP customers (or public institutions or …) can pay later via invoice*”.

![](/assets/images/event-command-transformation-in-microservice-architectures-a-2.jpeg)

[Three-lagged-race [CC licensed — taken from https://www.flickr.com/photos/12567713@N00/310639290]](https://www.flickr.com/photos/12567713@N00/310639290)Let’s go but wait, oh no, now we have to make changes to two different microservices: Payment & Inventory, see the adjusted code below. This is exactly what we do not want to do! This makes your microservice endeavor a three-legged-race! You tie together two teams to coordinate their development and deployment. This slows down your software development!

To make things worse, the two microservices also need to know about VIP customers—something they should not care about.

The following code clearly indicates these problems:

To avoid these problems you have to introduce the notion of a Command:

![](/assets/images/event-command-transformation-in-microservice-architectures-a-3.png)

In this scenario, the transformation should be made by an own bounded context or microservice, which cares about order fulfillment. So this is **not** about introducing some central orchestrator, but a clean context taking the responsibility of the flow. You could simply type:

I personally think that this is one aspect [Martin Fowler had in mind when he wrote](https://martinfowler.com/articles/201701-event-driven.html):

> Event notification is nice because it implies a low level of coupling, and is pretty simple to set up. It can become problematic, however, if there really is a logical flow that runs over various event notifications. The problem is that it can be hard to see such a flow as it’s not explicit in any program text. Often the only way to figure out this flow is from monitoring a live system. This can make it hard to debug and modify such a flow. The danger is that it’s very easy to make nicely decoupled systems with event notification, without realizing that you’re losing sight of that larger-scale flow, and thus set yourself up for trouble in future years. The pattern is still very useful, but you have to be careful of the trap.

Of course the event command transformation is no silver bullet and there are plenty of examples where pure event notification is the better choice. A service for customer retention might just listen to the order canceled event — without the order flow knowing anything about it. That’s perfect! But have the event command transformation pattern in mind and apply it every time it is necessary!

### Bonus
That’s it. You can stop reading, that was what I wanted to write!

But I can’t stop coding and if you can’t stop reading I added another alternative solution to implement the flow within the order service, this time using a lightweight workflow engine ([Camunda](http://camunda.org/)):

As you can see, this is also very easy to setup and not much more code than before. However you gain additional advantages because of advanced features of a workflow engine.

In the following example I extend the flow and give the shipment 5 minutes to respond otherwise I cancel the whole order and refund the payment by the use of compensation. This is an implementation of the [Saga pattern](https://blog.bernd-ruecker.com/saga-how-to-implement-complex-business-transactions-without-two-phase-commit-e00aa41a1b1b). The flow will only refund the payment if it was retrieved earlier (which was only done for non VIP customers):

With the growing complexity you might even prefer a graphical view of the flow. With Camunda you can either code the flows and still see an auto-generated graphical representation — or you can also graphically model the flow using the free [Camunda Modeler](https://camunda.org/download/modeler/). This is totally up to you. By the way, Camunda uses the [BPMN 2.0 ISO standard](https://www.amazon.com/Real-Life-BPMN-introductions-CMMN-DMN/dp/1541163443/) here:

![](/assets/images/event-command-transformation-in-microservice-architectures-a-4.png)

Note that this flow contains only logic important for the order microservice itself. Just because I use BPMN doesn’t mean I introduce any central orchestrator! See [Why service collaboration needs choreography AND orchestration](https://blog.bernd-ruecker.com/why-service-collaboration-needs-choreography-and-orchestration-239c4f9700fa) for details on this.

The content of this blog post is also available as a YouTube video:

As always, I love getting your feedback. Comment below or [send me an email](mailto:mail@bernd-ruecker.com).

*Stay up to date with my *[*newsletter*](http://eepurl.com/cDXPRj)*.*
