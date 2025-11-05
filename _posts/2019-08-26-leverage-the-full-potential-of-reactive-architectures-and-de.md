---
layout: post
title: "Leverage the full potential of reactive architectures and design reactive business processes"
date: 2019-08-26 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "event-driven", "architecture"]
author: Bernd Ruecker
excerpt: "Unfortunately I see many companies that don’t adjust their business processes to this new world. In this post I want to give one example and discuss the cons..."
---
### Leverage the full potential of reactive architectures and design reactive business processes

#### Why you should get your business to rethink business processes in order to improve customer experience
There is a lot of buzz around reactive architectures. Take for example event-driven architecture, event-streaming, reactive programming and so forth. I believe that these architectures will dominate in the future, as we build more complex systems, connect more distributed components and slice systems into smaller autonomous pieces.

Unfortunately I see many companies that don’t adjust their business processes to this new world. In this post I want to give one example and discuss the consequences, hopefully motivating you to advocate for a redesign of your business processes. Because a lot of the attention gravitates towards the technical side of reactive, without thinking too much about the business side. And as [Thorsten Dirks (CEO Telefonica)](https://www.computerwoche.de/g/die-besten-it-sprueche-2015,106507,3) said:

> *“When you digitize a shitty process, you have a shitty digital process!”*

#### What do you mean by reactive?

![](https://cdn-images-1.medium.com/max/800/1*9aPkPB_L83mrzdVnF96-yg.png)

But first we have to clarify terminology. I refer to reactive as defined in [the reactive manifesto](https://www.reactivemanifesto.org/): a system that is responsive, resilient (failure-tolerant) and elastic (scale-up/out and down). This is achieved by asynchronous communication like messaging. There is [a good take on the different meanings of reactive by Gernot Starke](https://www.innoq.com/en/blog/reactivity-whats-in-a-name/), if you want to dive into the different perceptions around the term reactive.

#### An example
Assume you want to book a train ticket. As I live in Germany, that will most probably done at Deutsche Bahn. Disclaimer: I haven’t worked on that use case with the specific company named here, but worked on similar use cases with comparable clients.

For the sake of the argument let’s assume Deutsche Bahn runs the following components to achieve this:

![](https://cdn-images-1.medium.com/max/800/1*LJ5bJjWg1UJxX03R40EuKw.png)

So far this is not surprising, multiple components need to interact to book a ticket. So what? Let’s look at the user experience. Imagine you book a ticket:

![](https://cdn-images-1.medium.com/max/800/1*q_gAswS56E3SrNK9oxBMNA.png)

You select a train connection, select a seat for the reservation, choose the ticket type and fare and finally provide your personal details together with a payment method.

After you have entered all data you hit the “Proceed” button. Happily you watch some animated gif looking forward to printing your ticket that will be opened as PDF directly afterwards.

Hang on —you wait for the PDF to show up? Yes, you do, and you better not close your browser in the meanwhile. **The business process is not reactive**. **It is completely synchronous**. You will wait until all processing finishes. This model visualizes the behavior:

![](https://cdn-images-1.medium.com/max/800/1*WQNEERhGGRJItyzaD5v1Tg.png)

But whats the problem with that?

The first problem is, that **this behavior is actually quite hard to implement**. The booking service needs to reach out to some other services in a defined sequence, always waiting for the result. It has to handle a couple of remote communications, some of them being asynchronous. And, as remote connections are [unreliable by definition](https://en.wikipedia.org/wiki/Fallacies_of_distributed_computing), it has to solve a lot of different failure scenarios. This is tricky. Luckily I know some great workflow engine that can help you out on these problems ;) My colleague Josh just blogged about [Workflows Inside a REST Request/Response](https://zeebe.io/blog/2019/08/zeebe-rest-affinity/) for example.

The second problem is **latency creep**. In order to complete a booking, the service has to call other services. Every call has latency and that simply builds up, especially if you have to do things sequentially. So the final ticket booking can easily take up to multiple seconds. And it might even be impossible to outsource certain services (or move them to the cloud) because of latency constraints. So that **limits possibilities**.

The third problem is that this design **ignores some very likely failure scenarios**. Services do have outages and networks have hiccups, so even when you try really hard, the availability of a single component can’t exceed a certain percentage. And now the outage risk builds up — making your booking service more flaky than you want it do be.

And whenever some service is not available (or hits a problem) the booking can’t complete and will fail, **leaving you as the customer alone with that error message,** so the failure solving is delegated to you. I wrote about why this leads to **bad user experience **in the [3 pitfalls of microservice integration](https://blog.bernd-ruecker.com/3-common-pitfalls-in-microservice-integration-and-how-to-avoid-them-3f27a442cd07).

But before the booking can fail, the booking service actually must take care to clean up: free every reservation probably already made, cancel charges on your credit card and so on. I wrote about this [Saga: How to implement complex business transactions without two phase commit](https://blog.bernd-ruecker.com/saga-how-to-implement-complex-business-transactions-without-two-phase-commit-e00aa41a1b1b).

#### And the business?
The process design will abort a booking and probably even cancel credit card charges if only the PDF generation doesn’t work. This means you s**et some revenue at risk unnecessarily**.

More seriously the resulting error scenarios are often not understood by business stakeholders, which leads to **developers making a lot of crucial decisions around reactions on failures**.

When proposing a more reactive, asynchronous way of doing things, the answer is regularly: But the business process has to be this way — the user needs to have the PDF right away in order to print it.

I tell you what: **User expectations changed quite a while ago**. True, some people still want a PDF to print out, right away, now in the browser. But honestly, most people want the ticket electronically on their mobile phone or in the app. Or the PDF could be sent via email later on. This could even be an option if you use a ticket machine that has problems with its printer! Modern apps are reactive and asynchronous — and people get used to it. They even enjoy it!

![](https://cdn-images-1.medium.com/max/800/1*LUn0r4bNu71BUFKFe2QuSw.png)

Yes, this will change the business process and yes, it will include customer touch points and affect the customer experience. But instead of seeing this as a thread it is actually a great opportunity!

I always ask: “In case of a failure, do you want to have a synchronous failure page shown to your customer — or the ticket being booked correctly later on and asynchronously sent via email?”

#### The reactive version
The reactive version of this business process is not that different, you simply don’t wait synchronously for the PDF anymore:

![](https://cdn-images-1.medium.com/max/800/1*Adr4xMCsFMH2iyiP59qEuA.png)

What looks like a small change has big impact.

First of all you start to **discuss the right questions**: “What if the reservation can’t be made, but we only figure this out after the customer left the booking process?” or “What if we can’t generate the ticket and the customer is not doing the retry, who does that now?” “How long can we try to retrieve the payment in case of an outage and is there a plan B instead of aborting the booking?” So the business truly has to think of various failure scenarios and define the reaction.

Secondly you gain a lot of **freedom in the implementation**. You no longer have to block a REST call, rendering latency irrelevant. I mean, if you send that email, do you care if that is a couple of seconds later? You actually don’t.

#### The benefit for the user
You **decrease failure rates**, as the design allows for more possibilities to resolve failures (e.g. [stateful retries](https://blog.bernd-ruecker.com/fail-fast-is-not-enough-84645d6864d3) or calling backup services). This also **increases customer satisfaction** as I am no longer seeing error pages in case of internal technical problems, but simply submit my ticket request and can be sure I will receive it eventually.

To quote [Eliyahu Goldratt](https://en.wikipedia.org/wiki/Eliyahu_M._Goldratt):

> “You’ve deployed an *amazing technology*, *but* because you haven’t changed the way you work, you haven’t actually diminished a limitation”

Or in the words of my colleague Mike Winters:

> Look, what you’re *really* implementing is a more resilient (and profitable and sustainable) user experience. This isn’t some hipster architecture for its own sake, it’s a better product.

#### Hear more about it at CamundaCon
Reactive architectures will become the new normal. In order to leverage their potential of being responsive, resilient and elastic you have to also design reactive business processes.

I will talk about this in my keynote on day two of our upcoming [CamundaCon in Berlin](https://www.camundacon.com/agenda/session/94323). Hope to CU there and discuss afterwards!

#### And your story?
I am currently searching for more stories around that topic. If you have anything to share don’t hesitate to [send me an email](mailto:bernd.ruecker@camunda.com)!
