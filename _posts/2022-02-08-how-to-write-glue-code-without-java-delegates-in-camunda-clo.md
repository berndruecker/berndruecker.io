---
layout: post
title: "How to write glue code without Java Delegates in Camunda Cloud"
date: 2022-02-08 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "spring", "event-driven", "architecture"]
author: Bernd Ruecker
excerpt: "!https://cdn-images-1.medium.com/max/800/0WtK_PfIw0fz737Sy"
---
### How to write glue code without Java Delegates in Camunda Cloud
Introduced in 2015, the [external task pattern](https://camunda.com/blog/2015/11/external-tasks/) is on the rise. Instead of the workflow engine actively calling some code (push), the external task pattern adds the work in a sort of queue and lets workers pull for it. This method is also known as publish/subscribe. The workflow engine publishes work, and workers subscribe to be able to do it.

![](https://cdn-images-1.medium.com/max/800/0*WtK_PfIw0fz737Sy)

Within Camunda Platform 7, we work on making the external task pattern the default recommendation, and for Camunda Cloud, this is the only way of writing glue code. Specifically, Java Delegates are not possible in Camunda Cloud anymore. This sometimes seems to leave people puzzled, so this blog post will answer why this is not a problem, and dive into the benefits you can gain from external tasks. This blog post will also debunk some myths about this pattern and clarify that:

- You can still call your service endpoints via any protocol (e.g. REST, AMQP, Kafka).
- You can have all worker code in one Java application.
- The programming model looks surprisingly similar to JavaDelegates when using Spring.
- Exception handling can still be delegated to the workflow engine.
- The performance overhead in terms of latency is small.

### Architecture considerations
Let’s debunk two architectural myths of external tasks.

First, applying external tasks does not necessarily mean services you formerly called via REST now need to fetch their work themselves. While this is an architectural option, it is not the typical case. Let’s look at an example:

![](https://cdn-images-1.medium.com/max/800/0*RB4DrW9m3pvG2t1o)

You likely will implement a worker that still does the REST call towards the payment microservice (left side of the illustration above). This is the API the microservice exposes and should be used. The worker is in the scope of the order fulfillment process solution or microservice. Nobody outside of the order fulfillment team even needs to know that Camunda or an external task worker is used at all.

Compare that to the solution on the right side of the example, where the payment microservice directly fetches its work from Camunda. In this case, Camunda is the middleware used for various microservices to communicate amongst each other. While this is feasible and has its upsides, I have rarely seen it in the wild. Read more on [further discussion of the differences](https://github.com/berndruecker/flowing-retail/tree/master/zeebe#does-zeebe-complement-or-replace-middleware).

The second myth is that you have to write multiple applications if you have multiple service tasks, one for every external task worker. While you can separate workers into multiple applications, it is rare. It is much more common to run all (or at least most) of your workers in one application.

![](https://cdn-images-1.medium.com/max/800/0*zloqy51QyBtBz5uS)

This application belongs logically to the process solution and registers a worker for every external task. This process solution can also auto-deploy the process model.

### Writing glue code
This leads us to the question of how to write glue code. Within that realm, there is another myth: it must be complicated because there is remote communication involved. The good news is that this is not necessarily true for Camunda Cloud, as there are programming language clients that provide a great developer experience. For example, using the [Spring integration](https://github.com/zeebe-io/spring-zeebe/), you can write worker code like this:

If you compare this code to a JavaDelegate, it looks surprisingly similar. We even created a [community extension](https://github.com/camunda-community-hub/camunda-platform-to-cloud-migration/blob/main/camunda-platform-to-cloud-adapter/readme.md) containing an adapter to reuse existing JavaDelegates for Camunda Cloud. While I would not necessarily recommend doing this as it’s better to migrate your classes manually, it nicely shows that this is conceptually not too hard.

That said, there are some things you can do in JavaDelegates that are no longer possible in external task workers:

- Access workflow engine internals
- Influence the workflow engine behavior
- Integrate with thread pools or transactions of the workflow engine
- Dirty hacks using Threadlocals or the like

In general, I feel it’s a good thing you cannot do these things anymore, as they regularly lead teams into trouble.

Note that we are also working on increasing the convenience of external tasks with Camunda Platform 7, and just started [this community extension](https://github.com/camunda-community-hub/camunda-engine-rest-client-java/).

### Handling exceptions
When writing glue code, you can also pass problems within your worker to the workflow engine to handle them. For example, the workflow engine can trigger retrying or raising an incident in the operations tooling. The code is pretty straightforward, and yet again quite comparable to JavaDelegates:

[Read more on this in the spring-zeebe docs](https://github.com/camunda-community-hub/spring-zeebe#completing-the-job).

However, there is one important failure case not yet sufficiently handled: what if a worker crashes and does not fetch any work anymore? Currently, Camunda Cloud recognizes this indirectly by service tasks not being processed for too long. Ideally, the workflow engine itself should recognize that work is no longer fetched. Then, it could indicate this in the operations tooling. We are currently looking into this feature. So long you can rely on typical systems monitoring to detect a crashed Java worker application.

### And transactions?
Similarly, how do you achieve consistency between your business code and the workflow engine? What if any of the components fail? With JavaDelegates, many users delegated these problems to either transaction manager, often without knowing it. Please refer to the blog post about [achieving consistency without transaction managers](https://blog.bernd-ruecker.com/achieving-consistency-without-transaction-managers-7cb480bd08c) for how to handle this with external tasks, but also to understand why this is a preferable mental model today.

### Latency of remote communication
One last myth I want to address in this post is that remote workers need to be “slow.” Often in such discussions, slow is not further defined, but looking at Camunda Platform 7, depending on the configuration of the [job executor](https://docs.camunda.org/manual/latest/user-guide/process-engine/the-job-executor/), it really can take seconds for an external task to be picked up (which can be optimized by the way!) In Camunda Cloud, the whole interaction is optimized from the ground up so that only a bit of latency for the remote communication is added. In a recent experiment, I [measured the overhead of a remote worker to be roughly 50ms](https://github.com/berndruecker/camunda-cloud-documentation/blob/best-practices/docs/components/best-practices/architecture/sizing-your-environment.md#latency-and-cycle-time):

![](https://cdn-images-1.medium.com/max/800/0*C9Ki2bvK5vPZ0DnM)

In most projects, this is not a problem at all, especially as it does not affect the throughput of the workflow engine. In other words, you can still process the same number of process instances, they simply require 50ms longer per service task. Note that we are further optimizing this number for low latency scenarios we are seeing among customers.

### Summary
As you can see, you have a programming model that is as convenient as JavaDelegates. At the same time, you have code that is properly isolated from the workflow engine ([moving from embedded to remote workflow engines](https://blog.bernd-ruecker.com/moving-from-embedded-to-remote-workflow-engines-8472992cc371) dives into all advantages of the remote engine setup).

This is why I am personally so excited about switching to external tasks as our default glue code pattern. If you are not convinced or still have questions, please reach out to me or [ask in the forum](https://forum.camunda.io/) at any time.
