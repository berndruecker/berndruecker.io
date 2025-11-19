---
layout: post
title: "Why service collaboration needs choreography AND orchestration"
date: 2017-04-13 12:00:00 +0000
categories: [blog]
tags: ["microservices", "orchestration"]
author: Bernd Ruecker
excerpt: "- Payment
- Inventory
- Shipping"
canonical: https://blog.bernd-ruecker.com/why-service-collaboration-needs-choreography-and-orchestration-239c4f9700fa
---
### Why service collaboration needs choreography AND orchestration
Let’s assume you want to build a simple order system covering the whole order fulfillment process with the following three microservices involved (you might name it services, components or aggregates if you prefer):

- *Payment*
- *Inventory*
- *Shipping*

The overall order fulfillment definitely requires these three services to collaborate. Currently there is a big fight going on as to which collaboration style to use — orchestration or choreography (for an introduction on the basic styles see [Orchestration or Choreography](http://plexiti.com/de/blog/2017/03/microservices-orchestration-or-choreography/)). But is this an either or decision?

I strongly believe that in most real-life scenarios you need a clever mix of both collaboration styles so let’s go over that.

### Choreography
Assume that we want to use event-based choreography as much as possible. This is in sync with the typical recommendation among microservice publications, e.g. “Building Microservices” from Sam Newman:

> In general, I have found that **systems that tend more toward the choreographed approach are more loosely coupled, and are more flexible and amenable to change** […].

> […], **asynchronous event collaboration helps us adopt a choreographed approach** which can yield significantly more decoupled services — something we want to strive for to ensure our services are independently releasable.

And I agree that this make sense on a large scale. Doing so results in the following event flow for our order application:

![](/assets/images/why-service-collaboration-needs-choreography-and-orchestrati-1.png)

So far so good, no component is talking directly to each other and only events are used to communicate. Very de-coupled — isn’t it?

No.

### Event command transformation

![](/assets/images/why-service-collaboration-needs-choreography-and-orchestrati-2.png)

The problem is that the *payment service* has to react to the *order placed *event. It has to know its consumer! As soon as other services need payment, it has to be adjusted. This is exactly what we don’t want, at least in the case of the *payment*.

![](/assets/images/why-service-collaboration-needs-choreography-and-orchestrati-3.png)

Another example: You want to implement the new business requirement that VIP customers can pay later by invoice. Now you have to change multiple components: The *payment* needs to know only to execute payments for non VIP customers. The *inventory *has to know that it reacts also on *order placed* but only for VIP customers. So you already had to tell two services about your VIP customer even though they should never know about it.

That’s why an additional component that decides that you have to *do payment* whenever an *order was created *improves de-coupling of the payment service from its concrete consumer. My friend [Martin Schimak](https://twitter.com/martinschimak) an I call that **event command transformation**:

![](/assets/images/why-service-collaboration-needs-choreography-and-orchestrati-4.png)

**Event command transformation** patternIn our example this might be done by an *order service*.

### Orchestration
As soon as you issue commands, you’ve taken the first step towards orchestration as the transformer automatically is some kind of conductor commanding another component. However, the command is still a message and can be sent over the normal bus. So I see good reasons to still summarize that as choreography without remorse. As wording is very important in the current discussions I think this helps to remove misconceptions that orchestration has to be avoided at any price; as you can see that the event command transformation is actually essential to build a nicely de-coupled event-based system!

### State Handling
We have a second challenge. The payment might take quite long to complete. Assume we want a call center to call the customer to clear problems during payment. That might take days to weeks. So now you get a [long running flow](https://blog.bernd-ruecker.com/what-are-long-running-processes-b3ee769f0a27) in your order service and you have to track the state. And you might face timing requirements, for example the pricing in the order might only be fixed for 7 days so you want to take action if the order is delayed too long.

I have described basic implementation approaches in [How to implement long running flows, sagas, business processes or similar](https://blog.bernd-ruecker.com/how-to-implement-long-running-flows-sagas-business-processes-or-similar-3c870a1b95a8). A typical approach is to use a state machine to do this. This finally means you are using an orchestrated style of collaboration within your order service. It is important to notice the small word *within *here.

### “Overall choregraphy and local orchestration”
This yields the following approach:

- **Orchestration **takes place **locally **in the order service. It is an implementation decision of that service to use a (local) brain to coordinate the flow it has to implement. There is no central conductor steering different services, no central spider in a giant spiderweb. That’s the typical misconception with orchestration engines. But there is a proper event command transformation so that the order service can command the payment service in order to improve de-coupling.
- The end-to-end business process of ordering goods can be implemented as **overall choreography**. Only messages (events and commands) are sent over the wire, there is no central conductor controlling everything.

![](/assets/images/why-service-collaboration-needs-choreography-and-orchestrati-5.png)

This brings the best of both worlds: Handling state and mastering control over the flow happens in a context you can easily control — one service. Choreography is used on the larger scale of collaboration of various services to improve flexibility.

### Conclusion
For real-life use cases services need to collaborate. You need both: *A choreographed event-based style* as well as an *orchestrated command-based style* depending on the situation at hand. With a clever mix you reach the level of de-coupling you aim for especially when using microservices.

If you are interested in concrete code examples for the concepts discussed, there is the [flowing retail sample application](https://blog.bernd-ruecker.com/flowing-retail-demonstrating-aspects-of-microservices-events-and-their-flow-with-concrete-source-7f3abdd40e53) showing all these aspects in action.

As always, I love getting your feedback. Comment below or [send me a mail](mailto:mail@bernd-ruecker.com).

*Stay up to date with my *[*newsletter*](http://eepurl.com/cDXPRj)*.*
