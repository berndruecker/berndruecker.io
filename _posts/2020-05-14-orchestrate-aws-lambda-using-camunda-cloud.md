---
layout: post
title: "Orchestrate AWS Lambda using Camunda Cloud"
date: 2020-05-14 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "process-automation"]
author: Bernd Ruecker
excerpt: "!https://cdn-images-1.medium.com/max/800/1uMXbfqZ7-wOqCH9ZKNhQvg.png"
---
### Orchestrate AWS Lambda using Camunda Cloud

#### Powerful Serverless Function Orchestration using BPMN and Cloud-Native Workflow Technology
Assume you want to coordinate multiple Lambdas to achieve a bigger goal. The example I use at my upcoming [AWS Community Summit](https://www.comsum.co.uk/) talk is a trip booking, composed of a hotel booking, a rental car booking and a flight booking:

![](https://cdn-images-1.medium.com/max/800/1*uMXbfqZ7-wOqCH9ZKNhQvg.png)

This raises a lot of questions about **how** the functions are coordinated to achieve this goal.

In this post I will describe **why orchestration** is a good choice — and **how you can use BPMN and Camunda Cloud** to orchestrate these three AWS Lambdas and provide an additional trip booking Lambda. I will also tackle why you might want to **prefer BPMN over AWS Step Functions**. All sources and a step-by-step tutorial are available on GitHub: [https://github.com/berndruecker/trip-booking-saga-serverless/tree/master/zeebe/aws](https://github.com/berndruecker/trip-booking-saga-serverless/tree/master/zeebe/aws).

### Why Orchestration?
In the talk I discuss alternative approaches, for example to chain the lambdas by using events in between. This would be a so-called choreography which tends to become hard to understand and change. I illustrated this in my slides:

So ideally you want to express the orchestration logic somewhere. But these orchestrations are seldom simple. For example, you have to deal with flight bookings that don’t go through. In this case you cannot simply rollback the transaction of the hotel or rental car booking. Instead, you have to cancel or undo these bookings in your business logic. This is known as the [Saga Pattern](https://blog.bernd-ruecker.com/saga-how-to-implement-complex-business-transactions-without-two-phase-commit-e00aa41a1b1b) and can be expressed in [BPMN](https://camunda.com/bpmn/) relatively easy:

![](https://cdn-images-1.medium.com/max/800/0*b-gd7z5tO95mvVzC.png)

### Orchestrating Lambdas With BPMN
In BPMN you can have service tasks where you can execute logic. This is a great place to invokes Lambdas. I will use [Camunda Cloud](https://camunda.com/de/products/cloud/) as managed workflow engine (based on [Zeebe](http://zeebe.io/)) that can execute such BPMN models. Workflow engine clusters can be created in self-service ([register for a free trial](https://camunda.com/de/products/cloud/)):

![](https://cdn-images-1.medium.com/max/800/1*S-28pmkkef1D6Vo7OxIaCg.png)

In order to connect Zeebe to AWS Lambdas you can use the [Zeebe Lambda Worker](https://github.com/zeebe-io/zeebe-lambda-worker), which is available as community extension (early stage at the time or writing). This allows you to wire your Lambda invocations directly within your BPMN:

![](https://cdn-images-1.medium.com/max/800/0*e1CcFvSZJfP5w8HI.png)

You can use workflow variables to store data related to your workflow instance, for example the result of the hotel booking. These variables can be used at other places, e.g. to make decisions:

![](https://cdn-images-1.medium.com/max/800/1*eTF6wBLasT_7gc2qJPiQtA.png)

The Zeebe Lambda worker itself can be operated as Docker image. It subscribes to Zeebe and invokes Lambdas. It can run within your AWS account, so you don’t need to expose your Lambdas to the outside world. You could simply operate the worker e.g. via AWS Fargate. [Refer to the docs for details](https://github.com/zeebe-io/zeebe-lambda-worker).

![](https://cdn-images-1.medium.com/max/800/0*Revilk4guFa28Gfj.png)

We currently spike [AWS EventBridge](https://aws.amazon.com/de/eventbridge/) support as an alternative to this worker, which could even ease the integration further.

Lambdas can also talk to the Zeebe workflow engine via [existing client libraries](https://docs.zeebe.io/clients/index.html), e.g. to trigger new workflows from within the trip booking function:

### How to run?
Follow the read-me of [https://github.com/berndruecker/trip-booking-saga-serverless/tree/master/zeebe/aws](https://github.com/berndruecker/trip-booking-saga-serverless/tree/master/zeebe/aws). It will walk you through the steps:

- [Deploy the functions to book or cancel a hotel, car and flight](https://github.com/berndruecker/trip-booking-saga-serverless/tree/master/functions/aws). The example leverages the [Serverless Framework](https://www.serverless.com/) for this (but you could also deploy in your favorite way).
- [Sign up for a Camunda Cloud account](https://camunda.com/products/cloud/) and create a Zeebe cluster.
- [Deploy the workflow model](https://github.com/berndruecker/trip-booking-saga-serverless/tree/master/zeebe/aws)
- [Run the Zeebe Lambda Worker](https://github.com/zeebe-io/zeebe-lambda-worker)
- [Deploy the function to book a trip](https://github.com/berndruecker/trip-booking-saga-serverless/tree/master/zeebe/aws#deploy-serverless-stuff)
- [Start using your function, e.g. via CURL](https://github.com/berndruecker/trip-booking-saga-serverless/tree/master/zeebe/aws#call-trip-booking-via-rest), and inspect what is going on in Operate.

A complete walk-through recording is available here:

### Why BPMN for Lambda Orchestration?
You might wonder why you should use BPMN instead of simply going for AWS Step Functions, which is available directly within the AWS universe? Good question!

I would name three main reasons:

- BPMN is a mature and feature-rich language, that is well-known and adopted in the industry. It is also an ISO standard.
- The visualization matters. Graphical models that can be understood by different stakeholders are super important.
- Lambda orchestration is often only one piece of a broader orchestration story we see at customers.

Let’s elaborate.

#### The BPMN language
I can simply [repeat myself](https://forum.camunda.org/t/bpmn-vs-aws-step-function/5460):

> BPMN is much more powerful. You are missing a lot of concepts in the AWS State Language, like timers (step functions is reduced for waiting with timeout), compensation or scoping (subprocesses). Of course step functions can eventually implement these concepts, but keep in mind that this also means: reinventing them. What I personally like about BPMN is that it is an ISO standard, so a lot of important players in the industry agreed on it and it is well discussed — so you can be sure important concepts are tackled and got right. I know that standards overall don’t have the best reputation at the moment, but I am convinced that they are very useful given a wide adoption like BPMN. A lot of people know it, so it is basically about proprietary vs. standard flow languages.

#### Visualization

> With Camunda you get BPMN models, which are considerably known around the globe by various stakeholders. You can model your flows graphically, but you are not forced to do it by the way, you can also define your flow in Java DSL. In this case you still have a BPMN visualization (auto-layout). BPMN models enable a BizDevOps mindset as these models can be read by business people as well as developers as well as in operations. This is not the case with Step Function visualizations.

> Talking about operations: Have a look at Camunda Cockpit and compare this to the AWS tooling, which I think speaks for itself.

Some comparison can also be found in [BPMN and Microservices Orchestration, Part 2 of 2: Graphical Models, Simplified Sagas, and Cross-functional Collaboration](https://zeebe.io/blog/2018/08/bpmn-microservices-orchestration-part-2-graphical-models/):

![](https://cdn-images-1.medium.com/max/800/0*LpTM0fiCD0AukII4.jpeg)

I wrote about that in length in [BizDevOps — the true value proposition of workflow engines](https://blog.bernd-ruecker.com/bizdevops-the-true-value-proposition-of-workflow-engines-f342509ba8bb).

#### Orchestrate anything
Customers typically not only orchestrate Lambdas, but they have a zoo of things they need to orchestrate, e.g. Legacy Systems (often on-prem), their monolith, microservices, [RPA Bots](https://blog.bernd-ruecker.com/how-to-benefit-from-robotic-process-automation-rpa-9edc04430afa), Functions, External Services, [Business Rules probably expressed in DMN](https://camunda.com/dmn/) and much more.

Step Functions can’t deliver this, as they are cloud only and, in fact, AWS only. While they integrate into the AWS universe natively, they are a stranger in the outside world or on-prem.

Camunda on the other hand is super flexible in this regard. We have customers running it on-prem, in their own cloud environments or in the public cloud of different vendors. You can easily orchestrate Azure Functions, even within the same workflow. The publish/subscribe concept of the workflow engines makes it super easy, to run on-prem workers in combination with a managed workflow engine.

### Conclusion
This post quickly walked you through Lambda orchestration with BPMN and Camunda Cloud. This should give you some starting point for own endeavors. While Step Functions might be the tool of choice for easy orchestrations, you might soon want to switch to a standardized, mature and widespread workflow language (BPMN), where the visualization can be understood by anyone. It will allow you to [orchestrate anything, anywhere](https://www.camundacon.com/live/hub/).
