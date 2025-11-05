---
layout: post
title: "3 common pitfalls in microservice integration — and how to avoid them"
date: 2018-03-06 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "!https://cdn-images-1.medium.com/max/800/1d-haRzQvmVaFfcpASdnqcg.png"
---
### 3 common pitfalls in microservice integration — and how to avoid them
*This blog post was *[*originally published at InfoWorld*](https://www.infoworld.com/article/3254777/application-development/3-common-pitfalls-of-microservices-integrationand-how-to-avoid-them.html)* two weeks ago. I also gave a couple of talks on this topic, e.g. at *[*QCon London*](https://qconlondon.com/london2018/presentation/3-common-pitfalls-microservice-integration)* (*[*Slides,*](https://www.slideshare.net/BerndRuecker/qcon-london-2018-3-common-pitfalls-in-microservice-integration-and-how-to-avoid-them)* *[*Recording*](https://youtu.be/O2-NHptllKQ)*).*

![](https://cdn-images-1.medium.com/max/800/1*d-haRzQvmVaFfcpASdnqcg.png)

Microservices are all the rage. They have an interesting value proposition, which is getting software to market fast while developing with multiple software development teams. So, microservices are about scaling your development force while maintaining high agility and a rapid development pace.

In a nutshell, you decompose a system into microservices. Decomposition is nothing new, but with microservices you give the teams developing services as much autonomy as possible.

For example, a dedicated team fully owns the service and can deploy or redeploy whenever they want to. They typically also do devops to be able to control the whole service. They can make rather autonomous technology decisions and run their own infrastructure, e.g. databases. Being forced to operate the software typically limits the number of wired technology choices, as people tend to choose [boring technology ](http://mcfunley.com/choose-boring-technology)much more often when they know they will have to operate it later on.

![](https://cdn-images-1.medium.com/max/800/1*8P3zJlGloCiEKjk6cKbjJA.png)

Microservices are about decomposition, but giving each component a high degree of autonomy and isolationA fundamental result of microservices architecture is that every microservice is a separate application communicating remotely with other microservices. This makes microservice environments *highly distributed systems*.** **Distributed systems have their own challenges. In this article, I’ll walk you through the three most common pitfalls I have seen in recent projects.

### 1. Communication is complex
Remote communication inevitably must respect the [8 fallacies of distributed programming](https://en.wikipedia.org/wiki/Fallacies_of_distributed_computing). It is not possible to hide the complexity, and many efforts to do so (e.g. [Corba](https://en.wikipedia.org/wiki/Common_Object_Request_Broker_Architecture) or [RMI](https://en.wikipedia.org/wiki/Java_remote_method_invocation)) have failed miserably. One important reason is that you have to *design for failure* within your services to be successful in an environment where failure is the new normal. But there are common patterns and frameworks helping you out. Let’s start with an example — a real situation I experience quite regularly.

I wanted to fly to London. When I got the check-in invitation I went to the website of the airline, selected my seat, and hit the button to retrieve my boarding pass. It gave me the following response:

![](https://cdn-images-1.medium.com/max/800/1*NnkQ5jY1yUYBBeYqHoJbsQ.png)

Let’s assume for a moment that the airline uses microservices (which might not be the case, but I know of other airlines that do).

![](https://cdn-images-1.medium.com/max/800/1*y56SFVubhRysmof9a93RJg.png)

The first thing I noticed: The error returned rather quickly, and other parts of the website behaved normally. So they used the important *fail fast *pattern. An error in the barcode generation didn’t affect the whole website. I could do everything else; I just could not get the boarding pass. Fail fast is super important as it prevents local errors from bringing down the whole system. The well-known patterns in this area are [circuit breaker](https://martinfowler.com/bliki/CircuitBreaker.html), [bulkhead](https://docs.microsoft.com/en-us/azure/architecture/patterns/bulkhead), and [service mesh](http://philcalcado.com/2017/08/03/pattern_service_mesh.html). These patterns are vital for the survival of distributed systems.

#### Failing fast is not enough
But failing fast is not enough. It offloads failure handling to clients. In this case I personally had to do the retry. In the above situation I even had to wait till the next day until the problems were resolved and I could get my boarding pass! For me that meant I had to use my own tooling to persist the retry (my calendar) to make sure I did not forget.

![](https://cdn-images-1.medium.com/max/800/1*L6Bq5SlYQQO7s_XhFdbgNQ.png)

Why doesn’t the airline just do the retrying themselves? They know my contact data and could send me the boarding pass asynchronously whenever ready. The better response would have been:

![](https://cdn-images-1.medium.com/max/800/1*jXoZWY6WDWEFGn-ZCTa4KA.png)

That would not only be much more convenient but also reduce the overall complexity as the number of components that need to see the failure is reduced:

![](https://cdn-images-1.medium.com/max/800/1*-b4J5bA77rFYiXFf6_enpQ.png)

You can transfer the same principle to service-to-service communication. **Whenever a service can resolve failures itself, it encapsulates important behavior.** This makes the life of all clients much easier and the API much cleaner. Resolving failures might be stateful (some call it long-running). I consider state handling a key question for failure handling in microservices.

Of course, the behavior described above is not always what you want and handing over the failure to the client can be just fine. But this should be a conscious decision that is made according to business requirements.

I have observed that most of the time another reason causes one to avoid stateful retrying: It comes with the complexity of state handling. The service has to retry for minutes, hours, or days. It has to do this reliably (remember: I want my boarding pass even if there is a system restart in between), and that involves handling persistent state.

#### How to manage persistent state?
I see two typical ways of handling persistent state:

- **Persistent things like entities stored in a database**. While this starts very straightforward it typically leads to a lot of **accidental complexity**. You not only need the database table but also some scheduler component to do the retry. You probably need some monitoring component to see or edit waiting jobs. And you need to take care of versioning if the overall business logic changes while you still want to do the retry. And so on and so on.

This line of thinking leads a lot of developers to just skip a proper failure handling as described above, leading to an increased complexity of the whole architecture — and poor customer experience.

Instead, I recommend leveraging

- **Lightweight workflow engines or state machines**. These engines are built to keep persistent state and handle subsequent requirements around the flow language, monitoring and operations, scaling to handle high volumes, and so on.

There are a couple of lightweight workflow engines on the market. Many of them use the ISO standard [BPMN](https://www.amazon.com/Real-Life-BPMN-introductions-CMMN-DMN/dp/1541163443/) to define flows and many of them are open source. Here I will use the open source workflow engine from [Camunda](https://camunda.com/) to illustrate the basic principle (quick disclaimer: as co-founder of the company behind this project I am obviously biased in my tool selection, but this is the workflow engine I know best). For the simple use case previously described, a workflow can be easily created using a Java DSL:

Another option is to [graphically model the workflow](https://camunda.com/download/modeler/) in BPMN:

![](https://cdn-images-1.medium.com/max/800/1*sb6SIgoeqK3P6pIDoSQoOA.png)

These workflow engines are very flexible in terms of architecture. Many developers believe that a workflow engine is a centralized component, but this is not true. There is no need to introduce a centralized component! If different services require a workflow engine, each service can run its own engine to maintain the autonomy and isolation of the services. This is discussed in more details in this [blog post about architecture options](https://blog.bernd-ruecker.com/architecture-options-to-run-a-workflow-engine-6c2419902d91).

![](https://cdn-images-1.medium.com/max/800/1*dtW3t5ytwhmYgdRecHW1Gg.png)

Another misconception is that workflows force developers to switch to asynchronous processing. This is also not true. In the above example the check-in component can return the boarding pass synchronously when everything runs smoothly. Only if there is an error will you fall back to asynchronous processing. This can be easily reflected as HTTP return code, 200 means “All OK, here is your result” and 202 means “Got it, I’ll call you back.” There is some concrete [sample code to handle this](https://github.com/berndruecker/flowing-retail/blob/master/rest/java/payment-camunda/src/main/java/io/flowing/retail/payment/resthacks/PaymentRestHacksControllerV4.java#L84), which leverages a simple semaphore.

![](https://cdn-images-1.medium.com/max/800/1*gQVSHWTMN6qmQjmZNXzJfQ.png)

I see a workflow engine as vital part of the toolbox for proper failure handling, which often involves long-running behavior like stateful retrying.

### 2. Asynchronicity requires attention

![](https://cdn-images-1.medium.com/max/800/1*ijRtrcTp5q2IilLgJLPTGQ.png)

This leads us to asynchronous communication, which most often means messaging. Asynchronicity is often advocated as the best default in distributed systems as it provides de-coupling, especially temporal de-coupling, because any message can be sent independently of the availability of the receiver. The message will get delivered as soon as the service provider is available without additional magic.

So, the problem of retrying is obsolete, but a comparable problem arises: You have to worry about timeouts. Assume that the airline uses asynchronous communication in the check-in scenario. The check-in component sends a message to the barcode generation service and then waits for the response. You do not have to care about the availability of the barcode generator as the message bus will deliver the message whenever appropriate.

But what if the request or the response becomes lost for whatever reason? Do you get stuck in the check-in forever, failing to send the boarding pass to the customer without noticing it? I bet a lot of companies do so, which again leads to me, the customer monitoring the response and taking action if no boarding pass arrives within a timeout. Again, I have to leverage my personal scheduling infrastructure (the calendar).

![](https://cdn-images-1.medium.com/max/800/1*KAEZMNMbwkO5NkiTdqLUPA.png)

The better approach is having the service monitor the timeout itself and execute a fallback whenever the barcode fails to arrive in time. A possible fallback is to resend the message, which is essentially retrying again.

You can leverage workflow automation technology for this use case too. A workflow in [BPMN](https://docs.camunda.org/manual/latest/reference/bpmn20/) might look like this:

![](https://cdn-images-1.medium.com/max/800/1*uZKnfDE7pVlJiwZvgsprVQ.png)

As a bonus, you get free reporting of the number of retries, the typical response times, and the number of workflows that could not be processed in time. Operators can easily inspect and repair failed workflow instances by having a lot of context available, e.g. the data that was included in a message and when exactly the message was sent. This level of visibility and operational control is typically missed in pure message-based solutions.

I have even seen companies going one step further and using a workflow engine instead of messaging middleware to distribute work among microservices. This is possible if the workflow engine does not actively call a service or send a message (called the push principle) but relies on the workers to ask for work (called the [pull principle](https://blog.camunda.com/post/2015/11/external-tasks/)). Now the work queue within the workflow engine behaves like a message queue. When I asked why they preferred a workflow engine, they said that messaging solutions lack the same quality of visibility and tooling and they wanted to avoid building their own operating tool.

### 3. Distributed transactions are hard

![](https://cdn-images-1.medium.com/max/800/1*_mwgclQwNtJI09vt_JuaTg.png)

A transaction is a series of operations performed in an all-or-nothing manner. We all know this from databases. You begin a transaction, do a couple of things, and then either commit or rollback the transaction. These transactions are called [ACID](https://en.wikipedia.org/wiki/ACID): atomic, consistent, isolated, and durable.

In distributed systems you cannot count on ACID transactions. Yes, there are protocols like XA that implement a so-called two-phase commit. Or [WS-AtomicTransaction](https://en.wikipedia.org/wiki/WS-Atomic_Transaction). Or sophisticated implementations like [Google Spanner](https://cloud.google.com/spanner/). But the current consensus is that these protocols are too expensive, or too complicated, or simply do not scale. A good background read is Pat Helland’s “[Life Beyond Distributed Transactions: An Apostate’s Opinion](https://cs.brown.edu/courses/cs227/archives/2012/papers/weaker/cidr07p15.pdf).”

But of course, the requirement for business transactions does not go away. The common trick to solving business transactions without ACID is to use compensation. This means that you execute undo activities for all activities improperly executed in the past. BPMN has this built-in, so you can define these undo activities and a workflow engine takes care of executing them reliably in the right order. This time I will use an example from ticket booking:

![](https://cdn-images-1.medium.com/max/1200/1*uSkoV6cru_WP0rbhMUXZvA.png)

This is often also known as the Saga pattern, which has become very popular recently. I wrote about it in “[Saga: How to implement complex business transactions without two phase commit](https://blog.bernd-ruecker.com/saga-how-to-implement-complex-business-transactions-without-two-phase-commit-e00aa41a1b1b),” where I also linked additional sources and some code.

Note that this approach is different from ACID transactions, as you can have intermediate states that are not strongly consistent. So, I could have a seat reserved, but not yet a valid ticket booked. Or I could have a ticket without having paid for it yet. The reality is that it is often OK to live with these temporary inconsistencies, as long as you make sure to clean them up eventually and put the system back into a consistent state. This is called *eventual consistency*, which is an important concept in distributed systems. “[Embracing eventual consistency in SoA networking](https://blog.envoyproxy.io/embracing-eventual-consistency-in-soa-networking-32a5ee5d443d)” nails it pretty good:

> Eventual consistency typically yields better performance, easier operation, and better scalability while requiring programmers to understand a more complicated data model.

The great news is that workflow automation eases the handling of compensation. This is because the workflow engine can take care of invoking all necessary compensating activities reliably.

### Service providers — do your homework!
Thus far I have proposed three easy remedies to typical challenges in distributed systems:

- Retries
- Timeouts
- Compensation

All of these can be implemented using lightweight workflow automation technology. But in order to leverage these recipes, each and every service provider must do its homework. That means

- offering compensation activities and
- implementing idempotency.

![](https://cdn-images-1.medium.com/max/800/1*dJg7lJQuQDCdKeje37DhLw.png)

While the first requirement should be obvious (I can only cancel a ticket if there is a service to cancel the ticket), the second — idempotency — calls for more explanation.

#### Idempotency
I talked a lot about retries. A common question is, what if I call a service twice by retries? This is a very good question!

First make sure you understand that you will have this problem with every form of remote communication! Whenever you communicate over a network, you cannot differentiate between three failure scenarios:

- The request hasn’t reached the provider
- The request has reached the provider, but it blew up during processing
- The provider processed the request, but the response was lost

![](https://cdn-images-1.medium.com/max/800/1*HUMK-BfuJTafqXAUIE537Q.png)

One possibility would be to ask the service provider if it already saw this request. But the more common approach is to use retrying and implement the service provider in a way that it allows for duplicate calls. This is simply easier to set up .

I see two easy ways of mastering idempotency:

- Natural idempotency. Some methods can be executed as often as you want because they just flip some state. Example: `confirmCustomer()`
- Business idempotency. Sometimes you have business identifiers that allow you to detect duplicate calls. Example: `createCustomer(email)`

If these approaches will not work, you will need to add your own idempotency handling:

- Unique ID. You can generate a unique identifier and add it to the call. This way a duplicate call can be easily spotted if you store that ID on the service provider side. If you leverage a workflow engine you probably can let it do the heavy lifting (e.g. as [Camunda allows for duplicate checks on keys during startup)](https://docs.camunda.org/manual/latest/user-guide/process-engine/database/#business-key). Example: *charge(transactionId, amount)*
- Request hash. If you use messaging you can do the same thing by storing hashes of your messages. You could again leverage the workflow engine for that, or you could use a database with built-in lease capabilities (like [Redis](https://redis.io/)).

Long story short: Take care of idempotency within your services. This will pay off big time.

### Show me code
You can find source code implementing the patterns I described here using BPMN and the open source Camunda engine in

- [Java](https://github.com/flowing/flowing-retail/tree/master/rest/java/payment) or
- [C#](https://github.com/flowing/flowing-retail/tree/master/rest/csharp/payment).

### Summary
In this article, I covered three common pitfalls I see customers stepping in when integrating microservices: underestimating the complexity of remote communication, ignoring the challenges of asynchronicity, and forgetting about business transactions.

Introducing capabilities to handle these situations with stateful patterns around retries, timeouts, and compensation activities can reduce the overall complexity of your microservices infrastructure and increase its resilience. It also helps to:

- Encapsulate important failure handling and transaction behavior where it belongs: within the context of the service itself.
- Reduce the effort of failure or timeout handling to a much smaller scope, reducing overall complexity.
- Simplify the API of services, only handing out failures that really matter for clients.
- Improve the client experience, where a client might be another service, an internal employee, or even the customer.

Using a lightweight workflow engine allows you to handle stateful patterns without investing a lot of effort or risking accidental complexity by applying homegrown solutions. The [accompanying source code](https://github.com/flowing/flowing-retail/tree/master/rest) offers concrete examples.

*This blog post was *[*originally published at InfoWorld*](https://www.infoworld.com/article/3254777/application-development/3-common-pitfalls-of-microservices-integrationand-how-to-avoid-them.html)* two weeks ago.*

As always, I love getting your feedback. Comment below or [send me an email](mailto:mail@bernd-ruecker.com).

*Stay up to date with my *[*newsletter*](http://eepurl.com/cDXPRj)*.*
