---
layout: post
title: "Fail fast is not enough!"
date: 2018-03-26 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "architecture"]
author: Bernd Ruecker
excerpt: "Remote communication inevitably must respect the 8 fallacies of distributed programminghttps://en.wikipedia.org/wiki/Fallacies_of_distributed_computing. So y..."
canonical: https://blog.bernd-ruecker.com/fail-fast-is-not-enough-84645d6864d3
---
### Fail fast is not enough!

#### Why you need stateful resilience patterns in distributed systems.
*This blog post digs deeper into one aspect of the *[*3 common pitfalls in microservice integration *](https://blog.bernd-ruecker.com/3-common-pitfalls-in-microservice-integration-and-how-to-avoid-them-3f27a442cd07)*— an article originally published at *[*InfoWorld*](https://www.infoworld.com/article/3254777/application-development/3-common-pitfalls-of-microservices-integrationand-how-to-avoid-them.html)*.*

Remote communication inevitably must respect the [8 fallacies of distributed programming](https://en.wikipedia.org/wiki/Fallacies_of_distributed_computing). So you have to **design for failure** within your services to be successful in an environment where failure is the new normal. To prevent local errors from bringing down the whole system the most common strategy is to fail fast. The well-known patterns in this area are [circuit breaker](https://martinfowler.com/bliki/CircuitBreaker.html), [bulkhead](https://docs.microsoft.com/en-us/azure/architecture/patterns/bulkhead), and [service mesh](http://philcalcado.com/2017/08/03/pattern_service_mesh.html). These patterns are vital for the survival of distributed systems.

**But fail fast is not enough!**

Let’s start with an example. I wanted to fly London. When I got the check-in invitation I went to the website of the airline, selected my seat, and hit the button to retrieve my boarding pass. It gave me the following response:

![](https://cdn-images-1.medium.com/max/800/1*FNhJJn9xEWvrXcQQpcg-YA.png)

Let’s assume for a moment that the airline uses microservices (which might not be the case, but I know of other airlines that do).

#### Fail fast

![](https://cdn-images-1.medium.com/max/800/1*y56SFVubhRysmof9a93RJg.png)

The first thing I noticed: The error returned rather quickly, and other parts of the website behaved normally. So they used the important *fail fast *pattern, most probably a [circuit breaker](https://martinfowler.com/bliki/CircuitBreaker.html). An error in the barcode generation didn’t affect the whole website. I could do everything else; I just could not get the boarding pass. Fail fast is super important as it prevents local errors from bringing down the whole system.

#### Fail fast is not enough!
But failing fast is not enough. It offloads failure handling to clients. On my flight back from London I got the same problem again — but this time the airline even gave me the work instructions:

![](https://cdn-images-1.medium.com/max/800/1*NnkQ5jY1yUYBBeYqHoJbsQ.png)

I personally had to do the retry. In the above situation I even had to wait till the next day until the problems were resolved and I could get my boarding pass! For me that meant I had to use my own tooling to persist the retry (my calendar) to make sure I did not forget.

![](https://cdn-images-1.medium.com/max/800/1*L6Bq5SlYQQO7s_XhFdbgNQ.png)

Why doesn’t the airline just do the retrying themselves? They know my contact data and could send me the boarding pass asynchronously whenever ready. The better response would have been:

![](https://cdn-images-1.medium.com/max/800/1*jXoZWY6WDWEFGn-ZCTa4KA.png)

That would not only be much more convenient but also reduce the overall complexity as the number of components that need to see the failure is reduced:

![](https://cdn-images-1.medium.com/max/800/1*-b4J5bA77rFYiXFf6_enpQ.png)

You can transfer the same principle to service-to-service communication. Whenever a service can resolve failures itself, it encapsulates important behavior. This makes the life of all clients much easier and the API much cleaner. Resolving failures might be stateful (some call it long-running). I consider state handling as a key question for failure handling in microservices.

Of course, the behavior described above is not always what you want and handing over the failure to the client can be just fine. But this should be a conscious decision that is made according to business requirements.

I have observed that most of the time another reason causes one to avoid stateful retrying: It comes with the complexity of state handling. The service has to retry for minutes, hours, or days. It has to do this reliably (remember: I want my boarding pass even if there is a system restart in between), and that involves handling persistent state.

#### How to manage persistent state?
I see two typical ways of handling persistent state:

- **Persistent things like entities stored in a database**. While this starts very straightforward it typically leads to a lot of **accidental complexity**. You not only need the database table but also some scheduler component to do the retry. You probably need some monitoring component to see or edit waiting jobs. And you need to take care of versioning if the overall business logic changes while you still want to do the retry. And so on and so on.

This line of thinking leads a lot of developers to just skip a proper failure handling as described above, leading to an increased complexity of the whole architecture — and a poor customer experience.

Instead, I recommend leveraging

- **Lightweight workflow engines or state machines**. These engines are built to keep persistent state and handle subsequent requirements around the flow language, monitoring and operations, scaling to handle high volumes, and so on.

There are a couple of lightweight workflow engines on the market. Many of them use the ISO standard [BPMN](https://www.amazon.com/Real-Life-BPMN-introductions-CMMN-DMN/dp/1541163443/) to define flows and many of them are open source. Here I will use the open source workflow engine from [Camunda](https://camunda.com/) to illustrate the basic principle (quick disclaimer: as co-founder of the company behind this project I am obviously biased in my tool selection, but this is the workflow engine I know best). For the simple use case sketched above, a workflow can be easily created using a Java DSL:

Another option is to [graphically model the workflow](https://camunda.com/download/modeler/) in BPMN:

![](https://cdn-images-1.medium.com/max/800/1*sb6SIgoeqK3P6pIDoSQoOA.png)

These workflow engines are very flexible in terms of architecture. Many developers believe that a workflow engine is a centralized component, but this is not true. There is no need to introduce a centralized component! If different services require a workflow engine, each service can run its own engine to maintain the autonomy and isolation of the services. This is discussed in more details in this [blog post about architecture options](https://blog.bernd-ruecker.com/architecture-options-to-run-a-workflow-engine-6c2419902d91).

![](https://cdn-images-1.medium.com/max/800/1*dtW3t5ytwhmYgdRecHW1Gg.png)

To get an idea how you could leverage Camunda e.g. by a couple of REST calls you might dive into [Use Camunda without touching Java and get an easy-to-use REST-based orchestration and workflow engine](https://blog.bernd-ruecker.com/use-camunda-without-touching-java-and-get-an-easy-to-use-rest-based-orchestration-and-workflow-7bdf25ac198e). Or if you are using Java the following code is sufficient to configure and startup the whole engine: Of course you could also use Spring, Spring Boot or a container of your choice to control startup and the lifecycle.

I see a workflow engine as vital part of the toolbox for proper failure handling, which often involves long-running behavior like stateful retrying.

#### A workflow engine doesn’t force asynchronicity on you
A common misconception is that workflows force developers to switch to asynchronous processing. This is also not true. In the above example the check-in component can return the boarding pass synchronously when everything runs smoothly. Only if there is an error will you fall back to asynchronous processing. This can be easily reflected as HTTP return code, 200 means “All OK, here is your result” and 202 means “Got it, I’ll call you back.” There is some concrete [sample code to handle this](https://github.com/flowing/flowing-retail/blob/master/rest/java/payment/src/main/java/io/flowing/retail/payment/port/resthacks/PaymentRestHacksControllerV4.java#L83), which leverages a simple semaphore.

![](https://cdn-images-1.medium.com/max/800/1*gQVSHWTMN6qmQjmZNXzJfQ.png)

### Show me code
A complete example showcasing

- fail fast,
- stateful retry,
- having a synchronous facade in front of a potentially asynchronous workflow and
- compensation

is available on GitHub: [https://github.com/flowing/flowing-retail/tree/master/rest](https://github.com/flowing/flowing-retail/tree/master/rest) in Java **and **C#. It shows a simple payment service which needs to call an upstream credit card service.

![](https://cdn-images-1.medium.com/max/800/0*gCTX0PUcJZ84ji_s.png)

### Another stateful resilience pattern: Human intervention
While stateful retrying is by far the most obvious example, there are other examples of stateful behavior to sort out failures, especially to **ask humans.**

Assume you build an order fulfillment system. It might retrieve payments at some point which probably involves charging a credit card. Assume the card cannot be charged. Instead of raising a payment failure the payment service might itself ask the customer to update the card details. This might take some days, for example GitHub gives you two weeks to do that.

![](https://cdn-images-1.medium.com/max/800/1*hmKUDJt9GrWAYhbyNhLvrg.png)

Having the workflow engine and persistent state allows you to easily support this behavior, making the live of your clients much easier. And with client I do not only mean the customer, but also an order service requesting the payment.

Another quite frequent example is to ask an operator to resolve a failure manually. This could be modeled like this:

![](https://cdn-images-1.medium.com/max/800/1*Gtk0wSQBQzPtHB7uVo26Zg.png)

But as this is such a frequent example that you don’t even have to model it. At least in Camunda exactly this functionality is built into every service task as incident resolving is build into the [operator tool Camunda Cockpit](https://docs.camunda.org/manual/latest/webapps/cockpit/bpmn/failed-jobs/). This eases the modeling:

![](https://cdn-images-1.medium.com/max/800/1*B4rZ2WB4Ua0ixnOrGH442g.png)

Retry, skip or cancel are simply available to the operator:

![](https://cdn-images-1.medium.com/max/800/1*SSikas_u48IkabThEFfySA.png)

### Summary
In this post I highlighted the importance of the fail fast pattern for distributed systems. But fail fast is simply not enough. In a lot of situations stateful resilience patterns like stateful retry are an important addition to your architecture to

- Encapsulate important failure handling and transaction behavior where it belongs: within the context of the service itself.
- Reduce the effort of failure or timeout handling to a much smaller scope, reducing overall complexity.
- Simplify the API of services, only handing out failures that really matter for clients.
- Improve the client experience, where a client might be another service, an internal employee, or even the customer.

Using a lightweight workflow engine allows you to handle stateful patterns without investing a lot of effort or risking accidental complexity by applying homegrown solutions. The [accompanying source code](https://github.com/flowing/flowing-retail/tree/master/rest) offers concrete examples.

*This blog post digged deeper into one aspect of the *[*3 common pitfalls in microservice integration *](http://XXX)*— an article originally published at *[*InfoWorld*](https://www.infoworld.com/article/3254777/application-development/3-common-pitfalls-of-microservices-integrationand-how-to-avoid-them.html)*.*

As always, I love getting your feedback. Comment below or [send me an email](mailto:mail@bernd-ruecker.com).

*Stay up to date with my *[*newsletter*](http://eepurl.com/cDXPRj)*.*
