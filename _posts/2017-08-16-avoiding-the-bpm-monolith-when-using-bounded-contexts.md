---
layout: post
title: "Avoiding the “BPM monolith” when using bounded contexts"
date: 2017-08-16 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "architecture"]
author: Bernd Ruecker
excerpt: "- The domain
- The organizational structure, including the development teams."
canonical: https://blog.bernd-ruecker.com/avoiding-the-bpm-monolith-when-using-bounded-contexts-d86be6308d8
---
### Avoiding the “BPM monolith” when using bounded contexts

#### For example in microservice architectures or Domain-Driven Design (DDD)
The microservice movement [picked up an idea from the Domain-Driven Design](https://www.infoq.com/presentations/ddd-microservices-2016) community called [bounded context](https://martinfowler.com/bliki/BoundedContext.html). In a nutshell it divides the system into smaller contexts driven by the domain. Within every bounded context you harmonize wording ([ubiquitous language](https://martinfowler.com/bliki/UbiquitousLanguage.html)) and domain concepts. When using the [microservices architectural style](https://martinfowler.com/articles/microservices.html) you create at least one microservice per bounded context. One important goal of microservices is to improve scalability and speed of the software development itself. Hence it is common sense that one microservice needs to be owned by exactly one development team. So setting the boundary of the bounded context is driven by two factors:

- The domain
- The organizational structure, including the development teams.

Now there is BPM (business process management) which is about business processes. If you look at end-to-end business processes they start from the original customer need and result in some meaningful end-result for him. In order to achieve it** the process often stretches across the boundary of one single bounded context **and **involves multiple microservices**.

Let’s do an easy example (sorry if this is heavily overused lately) — order fulfillment in a very easy form:

- Retrieve the payment which is handled by the Payment Context,
- Fetch the goods from stock which is handled by the Inventory Context,
- Ship the goods to the customer which his handled by the Shipping Context.

### Anti-Pattern “BPM monolith”
In [the 7 sins of workflow](https://blog.bernd-ruecker.com/the-7-sins-of-workflow-b3641736bf5c) I describe the anti-pattern called the BPM monolith. For the order fulfillment a motivated BPM professional might draw the following end-to-end process in one big model. To be honest, I was guilty of doing this myself!

![](/assets/images/avoiding-the-bpm-monolith-when-using-bounded-contexts-1.png)

This model **violates bounded contexts** and ownerships of the involved microservices. It shows details of different contexts which should never be combined within one model. As a result **you do not find a single person in your organization which can own the whole model** (if you do, you may not be doing DDD or microservices correctly ;-)). Additionally, you now face a situation where you have to **update your microservices in-sync with the process model** in case you make changes. This introduces a coupling you don’t want to have.

### Distributed engines and local orchestration
A much better approach is to “cut” the end-to-end process into appropriate “pieces” which fit into the bounded contexts. For our example we might have a process for the overall order and one for details of the payment but not any mixed one. Both processes can be fully owned by the respective microservice teams.

![](/assets/images/avoiding-the-bpm-monolith-when-using-bounded-contexts-2.png)

The consequence is that you run multiple process engines in your environment as every microservice needs its own. But actually this is a benefit here as this makes the teams very flexible in their implementation choices. Some teams might not use any engine, some might use a lightweight embeddable engine like [Camunda](https://camunda.org/) and others might use a microservice orchestration engine like [Zeebe ](https://zeebe.io/)or [Orchestrator](https://github.com/Netflix/conductor).

![](/assets/images/avoiding-the-bpm-monolith-when-using-bounded-contexts-3.png)

Microservices are about autonomy which means that every team should decide on its own. At least in theory as most companies I work with still have policies for allowed tooling in place.

If you are a Java developer you might directly dive into a [code example using Spring Boot, Camunda and RabbitMQ](https://github.com/berndruecker/camunda-spring-boot-amqp-microservice-cloud-example/) for a single microservice.

### Anti-pattern “No engine”
A quick word on another anti-pattern I referenced [in the 7 sins of workflow](https://blog.bernd-ruecker.com/the-7-sins-of-workflow-b3641736bf5c): No engine. Because of the fear of the BPM monolith I saw a lot of microservice teams avoiding any BPM, workflow or orchestration engine. This is not going to work especially in distributes systems — like microservice architectures or many DDD applications — you need to solve a lot of problems around retrying, waiting, timeouts, failure handling or compensation (e.g. [Saga-Pattern](https://blog.bernd-ruecker.com/saga-how-to-implement-complex-business-transactions-without-two-phase-commit-e00aa41a1b1b)). An appropriate engine will be a big help! In [“The flow” is for BPM what microservices are for SOA](https://blog.bernd-ruecker.com/the-flow-is-for-bpm-what-microservices-are-for-soa-5225c7908bae) I showed how to use lightweight engines in a couple of lines of code — so it is really easy. Don’t miss this opportunity!

### Monolith first?
I want to note that I don’t think your company has to follow the microservice approach. [Monolith First](https://martinfowler.com/bliki/MonolithFirst.html) is a useful pattern. So you might be totally happy with the monolithic BPM process —** if you have a single process owner** and probably a monolithic system. **If this works in your organization it is the right way to go — keep doing it!** It is a scenario we successfully helped customers with numerous times in the past.

But whenever your organization grows too big or too complex to handle this, you should adapt. You normally recognize it if you cannot find the person owning a process. In a recent project the customer named 24 (in words: twenty-four!) process owners for a single process — this is not going to work — it means there is no owner!

As always, I love getting your feedback. Comment below or [send me an email](mailto:mail@bernd-ruecker.com).

*Stay up to date with my *[*newsletter*](http://eepurl.com/cDXPRj)*.*
