---
layout: post
title: "Why Process Orchestration Needs Advanced Workflow Patterns"
date: 2022-08-01 12:00:00 +0000
categories: [blog]
tags: ["camunda", "orchestration"]
author: Bernd Ruecker
excerpt: "!https://cdn-images-1.medium.com/max/800/0nv9u3COivmUgd6-y"
canonical: https://camunda.com/blog/2022/07/why-process-orchestration-needs-advanced-workflow-patterns/
---
### Why Process Orchestration Needs Advanced Workflow Patterns
Life is seldom a straight line, and the same is true for processes. Therefore, you must be able to accurately express all the things happening in your business processes for proper end-to-end process orchestration. This requires [workflow patterns](https://docs.camunda.io/docs/components/concepts/workflow-patterns/) that go beyond basic control flow patterns (like sequence or condition). If your orchestration tool does not provide those advanced workflow patterns, you will experience confusion amongst developers, you will need to implement time-consuming workarounds, and you will end up with confusing models. Let’s explore this by examining an example of why these advanced workflow patterns matter in today’s blog post.

### Initial process example
Let’s assume you’re processing incoming orders of hand-crafted goods to be shipped individually. Each order consists of many different order positions, which you want to work on in parallel with your team to save time and deliver quicker. However, while your team is working on the order, the customer is still able to cancel, and in that case, you need to be able to revoke any deliveries that have been scheduled already. A quick drawing on the whiteboard yields the following sketch of this example:

![](https://cdn-images-1.medium.com/max/800/0*nv9u3COivmUgd6-y)

Let’s create an executable process model for this use case. I will first show you a possible process using [ASL (Amazon States Language)](https://docs.aws.amazon.com/step-functions/latest/dg/concepts-amazon-states-language.html) and AWS Step Functions, and secondly with Camunda Platform and [BPMN (Business Process Model and Notation)](https://camunda.com/bpmn/) to illustrate the differences between these underlying workflow languages.

### Modeling using AWS Step Functions
The following model is created using ASL, which is part of AWS Step Functions and, as such, a bespoke language. Let’s look at the resulting diagram:

![](https://cdn-images-1.medium.com/max/800/0*fMwSEFYnpCBB57JR)

To discuss it, I will use [workflow patterns](https://docs.camunda.io/docs/components/concepts/workflow-patterns/), which are a proven set of patterns you will need to express any workflow.

The good news is that ASL can execute a workflow pattern called “[dynamic parallel branches](https://docs.camunda.io/docs/components/concepts/workflow-patterns/#dynamic-parallel-branches),” which allows parallelizing execution of the order positions. This is good; otherwise, we would need to start multiple workflow instances for the order positions and do all synchronizations by hand.

But this is where things get complicated. ASL does not offer [reactions to external messages](https://docs.camunda.io/docs/components/concepts/workflow-patterns/#external-messagesevents); thus, you cannot interrupt your running workflow instance if an external event happens, like the customer cancels their order. Therefore, you need a workaround. One possibility is to use a parallel branch that waits for the cancellation event in parallel to execute the multiple instance tasks, marked with (1) in the illustration above.

When implementing that wait state around cancelation, you will undoubtedly miss a proper [correlation mechanism](https://docs.camunda.io/docs/components/concepts/workflow-patterns/#correlation-mechanisms), as you cannot easily correlate events from the outside to the running workflow instance. Instead, you could leverage the [task token](https://docs.aws.amazon.com/step-functions/latest/dg/connect-to-resource.html#connect-wait-example) generated from AWS and keep it in an external data store so that you can locate the correct task token for a given order id. This means you have to implement a bespoke message correlation mechanism yourself, including persistence as [described in Integrating AWS Step Functions callbacks and external systems](https://aws.amazon.com/blogs/compute/integrating-aws-step-functions-callbacks-and-external-systems/).

When the cancelation message comes in, the workflow advances in that workaround path and needs to raise an error so all order delivery tasks are canceled, and the process can directly move on to cancelation, marked with (2) in the above illustration.

But even in the desired case that the order does not get canceled; you need to leverage an error. This is marked with (3) in the illustration above. This is necessary to interrupt the task of waiting for the cancelation message.

You need to use a similar workaround again when you want to wait for payment, but stop this waiting after a specified timeout. Therefore, you will start a timer in parallel, marked with (4), and use an error to stop it later, marked with (5).

Note that when you configure the wait state, you might assume you may misuse Step Functions here, as you configure the time in seconds, meaning you have to enter a big number (864,000 seconds) to wait ten days.

Of course, you could also implement your requirements differently. For example, you might implement all order cancelation logic entirely outside of the process model and just terminate the running order fulfillment instance via API. But note that by doing so, you will lose a lot of visibility around what happens in your process, not only during design time but also during operations or improvement endeavors.

Additionally, you distribute logic that belongs together all over the place (step function, code, etc.) For example, a change in order fulfillment might mean you have to rethink your cancelation procedure, which is obvious if cancelation is part of the model.

To summarize, the lack of advanced workflow patterns requires workarounds, which are not only hard to do but also make the model hard to understand and thus weakens the value proposition of an orchestration engine.

### Modeling with BPMN
Now let’s contrast this with modeling using the ISO standard [BPMN](https://camunda.com/bpmn/) within Camunda:

![](https://cdn-images-1.medium.com/max/800/0*TOBpVRbQLddwstAT)

This model is directly executable on engines that support BPMN, like Camunda. As you can see, BPMN supports all required advanced workflow patterns to make it not only easy to model this process but also yields a very understandable model.

Let’s briefly call out the workflow patterns (besides the basics like [sequence](https://docs.camunda.io/docs/components/concepts/workflow-patterns/#sequence), [condition](https://docs.camunda.io/docs/components/concepts/workflow-patterns/#conditions-ifthen), and [wait](https://docs.camunda.io/docs/components/concepts/workflow-patterns/#wait)) that helped to make this process so easy to implement:

- [Dynamic parallel branches](https://docs.camunda.io/docs/components/concepts/workflow-patterns/#dynamic-parallel-branches)
- [Reacting to external message events](https://docs.camunda.io/docs/components/concepts/workflow-patterns/#external-messagesevents) with [correlation mechanisms](https://docs.camunda.io/docs/components/concepts/workflow-patterns/#correlation-mechanisms)
- [Reacting to time-based events](https://docs.camunda.io/docs/components/concepts/workflow-patterns/#time-based)

This model can be perfectly used to discuss the process with various stakeholders, and can further be shown in [technical operations](https://camunda.com/platform/operate/) (e.g., if some process instance gets stuck) or [business analysis](https://camunda.com/platform/optimize/) (e.g., to understand which orders are canceled most and in which state of the process execution). Below is a sample screenshot of the operations tooling showing a process instance with six order items, where one raised an incident. You can see how easy it gets to dive into potential operational problems.

![](https://cdn-images-1.medium.com/max/800/0*iNhmDAAP3Zevndet)

### Let’s not let history repeat itself!
I remember one of my projects using the workflow engine JBoss jBPM 3.x back in 2009. I was in Switzerland for a couple of weeks, sorting out exception scenarios and describing patterns on how to deal with those. Looking back, this was hard because jBPM 3 lacked a lot of essential workflow patterns, especially around the [reaction to events](https://docs.camunda.io/docs/components/concepts/workflow-patterns/) or [error scopes](https://docs.camunda.io/docs/components/concepts/workflow-patterns/#error-scopes), which I did not know back then. In case you enjoy nostalgic pictures as much as I do, this is a model from back then:

![](https://cdn-images-1.medium.com/max/800/0*MCZWGd96sxir5YM2)

I’m happy to see BPMN removed the need for all of those workarounds necessary, creating a lot of frustration among developers. Additionally, the improved visualization really allowed me to discuss process models with a larger group of people with various experience levels and backgrounds in process orchestration.

Interestingly enough, many modern workflow or orchestration engines lack the advanced workflow patterns described above. Often, this comes with the promise of being simpler than BPMN. But in reality, claims of simplicity mean they lack essential patterns. Hence, if you follow the development of these modeling languages over time, you will see that they add patterns once in a while, and whenever such a tool is successful, it almost inevitably ends up with a language complexity comparable to BPMN but in a proprietary way. As a result, process models in those languages are typically harder to understand.

At the same time, developing a workflow language is very hard, so chances are high that vendors will take a long time to develop proper pattern support. I personally don’t understand this motivation, as the knowledge about workflow patterns is available, and BPMN implements it in an industry-proven way, even as an ISO standard.

### Conclusion
The reality of a business process requires advanced workflow patterns. If a product does not natively support them, its users will need to create technical workarounds, as you could see in the example earlier:

- ASL lacked pattern and required complex workarounds.
- BPMN supports all required patterns and produces a very comprehensible model.

Emulating advanced patterns with basic constructs and/or programming code, as necessary for ASL, means:

- Your development takes longer.
- Your solution might come with technical weaknesses, like limited scalability or observability.
- You cannot use the executable process model as a communication vehicle for business and IT.

To summarize, ensure you use an orchestration product supporting all-important workflow patterns, such as Camunda, which uses BPMN as workflow language.
