---
layout: post
title: "“The flow” is for BPM what microservices are for SOA"
date: 2017-08-08 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring"]
author: Bernd Ruecker
excerpt: "I quickly scanned through the linked blog posthttp://redmonk.com/sogrady/2017/07/20/soa-microservices/ and couldn’t agree more even if I think there are more..."
canonical: https://blog.bernd-ruecker.com/the-flow-is-for-bpm-what-microservices-are-for-soa-5225c7908bae
---
### “The flow” is for BPM what microservices are for SOA
This tweet from Martin Fowler got my attention today:

> 

I quickly scanned through [the linked blog post](http://redmonk.com/sogrady/2017/07/20/soa-microservices/) and couldn’t agree more (even if I think there are more differences like built-for-reuse vs. built-for-replacement or centralized vs. decentralized):

> The SOA-driven world originally envisioned by large vendors, one in which services were built out upon a byzantine framework of complex (and frequently political) “standards” never came to pass** for the simple reason that developers wanted no part of it.**

> […]

> But the most important takeaway from SOA was arguably that developers would play a decisive — and in many cases, deciding — role on what would get used and what would not.

The same is true for BPM. BPM tooling was dominated for a long time by big vendors going for a **zero- or low-code approach** which is basically about building process applications without involving any developers so business folks can do that themselves. This is the **zero-code lie** (see the [7 sins of workflow](https://blog.bernd-ruecker.com/the-7-sins-of-workflow-b3641736bf5c)). It is often sold because it sounds good for the business — no dependency on IT. But it is a lie —it never works out apart from simplistic cases delivering **zero or low business value**.

![](/assets/images/the-flow-is-for-bpm-what-microservices-are-for-soa-1.png)

I hate this lie as it burned the BPM term at least for developers but most often for everybody as big BPM initiatives in the past failed. To make things worse BPM is often connected to SOA with the problems mentioned above. Bummer! But there are so many treasures for everybody within BPM methodologies and workflow engines! But you do have to choose **developer friendly BPM**. Whenever people ask me what developer friendly BPM is I write the following lines of code:

This is all you need to startup a process engine, deploy a process definition and start a new process instance using a [lightweight open source engine](http://camunda.org/)! You get persistence for the state in a database, failure handling support, [Saga pattern support with compensation](https://blog.bernd-ruecker.com/saga-how-to-implement-complex-business-transactions-without-two-phase-commit-e00aa41a1b1b), time and event handling, human task management, visibility of the flow in BPMN to discuss it with other stakeholders (yes, also if you define the process by code as shown above), operating tools and more. You only need a couple of lines of readable code. The process engine is a library! You can run it embedded in a Java microservice like in this [Spring Boot example](https://github.com/berndruecker/camunda-spring-boot-amqp-microservice-cloud-example/). You can also [use other languages than Java](https://blog.bernd-ruecker.com/use-camunda-without-touching-java-and-get-an-easy-to-use-rest-based-orchestration-and-workflow-7bdf25ac198e) and you are [not forced to violate microservice boundaries and introduce some central BPM thing](https://blog.bernd-ruecker.com/why-service-collaboration-needs-choreography-and-orchestration-239c4f9700fa). Just do it right and it can be super cool, as we see at our [customers](https://camunda.com/customers/).

With the learning from SOA and microservice we might need a new name though. Unfortunately microprocesses is overloaded and not really a good fit. I haven’t had the flash of inspiration yet (let me know if you have). I talk about “**flows**” when interacting with developers which already works much better. Sometimes I use the term “**workflow**” — but it is often connected too much with human tasks and task lists. Sometimes I use the term “**state machine**” as this is the basis underneath. Or I still use “**business process**” or **BPM **— but only if I sense it it does not have a negative connotation. You might do the same when introducing lightweight and developer friendly BPM in your company — it helps!

I want to finish with another well fitting tweet here:

> 

As always, I love getting your feedback. Comment below or [send me an email](mailto:mail@bernd-ruecker.com).

*Stay up to date with my *[*newsletter*](http://eepurl.com/cDXPRj)*.*
