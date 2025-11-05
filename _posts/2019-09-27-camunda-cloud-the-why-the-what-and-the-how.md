---
layout: post
title: "Camunda Cloud: The why, the what and the how"
date: 2019-09-27 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "event-driven", "process-automation"]
author: Bernd Ruecker
excerpt: "I personally liked Forget monoliths vs. microservices. Cognitive load is what mattershttps://techbeacon.com/app-dev-testing/forget-monoliths-vs-microservices..."
---
### Camunda Cloud: The why, the what and the how

#### Exploring sample use cases around microservices orchestration and serverless function orchestration
[Camunda Cloud](https://camunda.com/products/cloud/) was [announced](http://x) at the recent [CamundaCon in Berlin](https://www.camundacon.com/). It provides Workflow as a Service based on the open source project [Zeebe.io](https://zeebe.io/). In this post I want to quickly explain **why** I think cloud is here to stay, but foremost look into two **sample use cases** and **how **you can leverage Camunda Cloud to solve them: **microservices orchestration** and **serverless function orchestration**.

### My take on WHY cloud
I don’t have to reiterate over all the existing writing why cloud will be inevitable. You could, for example, look into Simon Wardley’s work like [Containers won the battle, but will lose the war to serverless](https://read.acloud.guru/simon-wardley-is-a-big-fan-of-containers-despite-what-you-might-think-18c9f5352147), or tons of other resources (e.g. [Sam Newmann: Confusion in the Land of the Serverless](https://www.youtube.com/watch?v=Y6B3Eqlj9Fw)) telling you, that you need to get rid of “undifferentiated heavy lifting” to be able to focus on things that really differentiates your company — which is typically business logic.

I personally liked [Forget monoliths vs. microservices. Cognitive load is what matters](https://techbeacon.com/app-dev-testing/forget-monoliths-vs-microservices-cognitive-load-what-matters), and used that for [my keynote](https://www.slideshare.net/BerndRuecker/camunda-con-2019-keynote-i-want-my-process-back-microservices-serverless) at the above-mentioned CamundaCon. Let’s assume your team is capable of doing 10 units of work (whatever that unit is). This, then, is your maximum capacity:

![](https://cdn-images-1.medium.com/max/800/1*Q9mqQWSYLG67E-aAgYi-mQ.png)

In a typical environment, a lot of time will go into the undifferentiated heavy lifting to get your infrastructure right. Tasks like creating deployments, images or containers, environment specific configurations, deployment scripts, and so forth. Very often, these tasks take up to much more than 50% of the time, leaving the team little time to do business logic:

![](https://cdn-images-1.medium.com/max/800/1*0JPI-ejLaxyvcxlYkbLJNw.png)

![](https://cdn-images-1.medium.com/max/800/1*S_vAxH3LQbu-ygX2f9_zjA.png)

Undifferentiated heavy lifting to provision components yourselfMy personal *aha* moment occured when I was full of excitement around Kubernetes and wanted to leverage it to do a proper benchmark and load test on Zeebe. What followed was a painful process of creating the right Docker images, understanding Kubernetes specifics, Helm charts and some shell scripting which even led me to get to know the Linux subsystem of my Windows 10 machine (OK — I am actually grateful for that — but it took quite some time). OK, that all was long before there was [zeebe-kubernetes](https://github.com/zeebe-io/zeebe-kubernetes/) or [zeebe-helm](https://github.com/zeebe-io/zeebe-helm), so it would be much easier today. But the core problem remains: too much undifferentiated heavy lifting and you are now in charge of components you don’t care about (like e.g. the operating system of your docker image).

This is exactly what we *don’t* want. What we *do *want is to concentrate on business logic *most of the time*:

![](https://cdn-images-1.medium.com/max/800/1*Ix8YC3LcpHdVpXu4E-Vi_Q.png)

That’s why I want a picture vastly simpler than the one above. And here is one:

![](https://cdn-images-1.medium.com/max/800/1*LgqEvqxrcNQjiWVkElAd1w.png)

### What is Camunda Cloud
Camunda Cloud is the umbrella for a couple of Camunda products provided as a Service, think of it as WaaS (Workflow as a Service —but be sure this will not be a term though ;-)). At CamundaCon, my favorite CTO [Daniel Meyer](https://twitter.com/meyerdan/) sketched this vision:

![](https://cdn-images-1.medium.com/max/800/1*2ISPem3o2qRmclEimQllEQ.png)

As always, Camunda takes an incremental approach. In the first iteration, you get Zeebe as a workflow engine. More concretely, you can log into your cloud console and create a new Zeebe cluster:

![](https://cdn-images-1.medium.com/max/1200/1*IKU7wnGk-Y4Iotr61yPzVw.png)

You can see the health of your cluster and all endpoint information in this console (including necessary security tokens) — allowing you to start developing right away.

In order to work with the workflow engine you can:

- build applications in the programming language of your choice (Zeebe provides language clients in e.g. [Java](https://docs.zeebe.io/java-client/README.html), [Node.js](https://creditsenseau.github.io/zeebe-client-node-js/), [C#](https://github.com/zeebe-io/zeebe-client-csharp), [Go ](https://docs.zeebe.io/go-client/README.html)or [Rust](https://github.com/zeebe-io/zeebe-client-rust)),
- use the command line tool to deploy workflows, start instances or create workers,
- use the existing HTTP worker to call REST APIs.

![](https://cdn-images-1.medium.com/max/800/1*K3xdqm_QwhG4mzJm1J8PTA.png)

Of course, you can also leverage other components of the Zeebe ecosystem, for example the [Kafka Connector](https://github.com/zeebe-io/kafka-connect-zeebe).

Best follow this [Getting Started Guide](https://zeebe.io/blog/2019/09/getting-started-camunda-cloud/) right away.

### Use case: microservices orchestration
A common use-case for a workflow engine is orchestrating microservices to fulfill a business capability. I often use a well-known domain to visualize this: order fulfillment. You can imagine that various microservices are required to fulfill a customer order, connected via Zeebe:

![](https://cdn-images-1.medium.com/max/800/1*DkHWnqNThVVbJUBAzc4p9g.png)

Of course, you are not forced to use Zeebe as the transport between your microservices — you might want to leverage your existing communication transports — like REST, Kafka or messaging. In this case, the workflow looks more or less the same, but only one microservice knows of Zeebe, and has some code to translate between workflow tasks and Kafka or the like:

![](https://cdn-images-1.medium.com/max/800/1*i1sj1uvnVmYV7VZsX0uf0Q.png)

To play around with it, you can use this code on GitHub:

[**berndruecker/flowing-retail**
*This folder contains services that leverage the horizontally scalable workflow engine Zeebe for work distribution and…*github.com](https://github.com/berndruecker/flowing-retail/tree/master/zeebe)You just have to exchange the configuration of the Zeebe client, here is an example:

Here is a screen cast walking you through it:

### Use case: serverless function orchestration
If you are serverless you might build a lot of functions. A key question will be how to coordinate functions that depend on each other. Let’s do THE classical example for the [Saga Pattern](https://blog.bernd-ruecker.com/saga-how-to-implement-complex-business-transactions-without-two-phase-commit-e00aa41a1b1b) here: You have one function to book a rental car, one function to book a hotel and one to book flights.

Now you want to provide a function to book whole trips, which need to use the other functions. Instead of hard coding the function calls in your trip booking function, you can leverage a workflow to do this. I talk about this at the upcoming [AWS Community Summit in London](https://www.comsum.co.uk/).

There are two possibilities to connect Camunda Cloud to your favorite cloud providers functions like AWS Lambda, Azure Functions or GCP Functions. Let’s assume you do AWS for the moment to ease wording in this article. Then one possibility is to use the built-in HTTP connector of Camunda Cloud and call Lambdas via the API gateway. The workflow is like any other external client in this case.

![](https://cdn-images-1.medium.com/max/800/1*R2fgIa0JiI2Mo4PNjB3k-A.png)

The **advantage **of this approach is the simplicity — and you are ready to go immediately. You don’t have to think about deploying or scaling any component yourself.

The **disadvantage **is that you have to expose all your lambdas via the API Gateway. As you can secure them easily (or probably use some VPN down the road), this should not be too big of a problem. Another disadvantage is that you have to pay for the API Gateway on top of your Lambda and live with smaller timeouts of your function calls (probably Kinesis would help?). Probably the biggest downside is that you are limited to the possibilities of the existing HTTP worker.

You can find some sample code on GitHub:

[**berndruecker/trip-booking-saga-serverless**
*This demo implements the classical trip booking Saga with a managed Zeebe cluster in Camunda Cloud.*github.com](https://github.com/berndruecker/trip-booking-saga-serverless/tree/master/zeebe)You can also see this in action:

The second possibility is to provide a [Zeebe Worker](https://docs.zeebe.io/basics/job-workers.html) that can natively invoke your function. This worker is deployed into your own cloud account as a container and therefore is an internal component for you. No need to expose your functions to the outside world.

![](https://cdn-images-1.medium.com/max/800/1*XknCqF-fs7LVA4I2IcFUsw.png)

The **advantage **of this solution is that functions are called in a totally native way; and you have full control over the feature set of your worker. You could provide different workers for different cloud environments, without the workflow needing to know anything about it. The whole environment configuration for your cloud account is now moved to the worker, where you can easily control it. Additionally, you don’t have to pay for API Gateway.

The clear **disadvantage **is that you need another component, one that you have to maintain, deploy and scale by yourself. And, it needs to be always-on and therefore always costs, which means you need quite some throughput to reach the break-even compared to the invocation costs of the API Gateway. But if you are more deep into Kubernetes there are some tricks to scale your worker to zero to optimize costs (comparable to e.g. [Osiris](https://github.com/deislabs/osiris)).

### Summary
Using cloud services removes the burden of undifferentiated heavy lifting — freeing you to focus on business logic instead. This applies equally to workflow automation.

Today, I touched on how to leverage a managed Zeebe workflow engine in Camunda Cloud for both microservices orchestration and serverless function orchestration. We see both use cases happening already and anticipate a growing demand in the near future.

In short: I am excited! Request [beta access now](https://camunda.com/products/cloud/), [get going](https://zeebe.io/blog/2019/09/getting-started-camunda-cloud/) and [give us feedback](https://forum.zeebe.io/)!
