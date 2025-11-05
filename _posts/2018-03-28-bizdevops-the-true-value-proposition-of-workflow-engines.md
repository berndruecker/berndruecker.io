---
layout: post
title: "BizDevOps — the true value proposition of workflow engines"
date: 2018-03-28 12:00:00 +0000
categories: [blog]
tags: ["camunda", "process-automation"]
author: Bernd Ruecker
excerpt: "BPMN is an ISO standardhttp://www.bpmn.org/ and its beauty is that it defines a business readable graphical visualization that is also directly executable on..."
---
### BizDevOps — the true value proposition of workflow engines

#### Looking beyond the low-code myth
Whenever you implement core business logic you have to discuss it with a lot of stakeholders and having a graphical representation is a huge help in doing so. This is why there are a lot of methodologies out there to capture business logic visually, like [Event Storming](https://en.wikipedia.org/wiki/Event_storming), [domain modeling](https://en.wikipedia.org/wiki/Domain_model) and of course [process modeling with BPMN](https://camunda.com/bpmn/).

[BPMN is an ISO standard](http://www.bpmn.org/) and its beauty is that it defines a business readable graphical visualization that is also directly executable on a workflow engine. A BPMN model therefore is also an XML file a workflow engine can understand.

![](https://cdn-images-1.medium.com/max/800/1*z2imoSr7Qrafg-_QCxwUhA.png)

Compared to other languages, BPMN is a good choice as it is really widespread and very mature. I will not dive into [details of the BPMN elements](https://docs.camunda.org/manual/7.8/reference/bpmn20/) but I can report that it was sufficient for all real-life use cases we saw in the last years — which are a lot.

**It is about living documentation — not low code!**The benefit of directly executable models is what [Cyrille Martraire](https://twitter.com/cyriux) named [living documentation](https://www.infoq.com/news/2015/06/ddd-living-documentation). The model is not an input of the requirement phase that will be outdated once the first line of code is written. No, **the model will be executed and is the code. It is always in-sync with the implementation** during the whole life-cycle.

Unfortunately this is often connected with zero or low code approaches which try to get rid of the developer. In our experience [these approaches do not work well](https://camunda.com/learn/whitepapers/developer-friendly-bpm/), at least not in non-trivial use cases. I see BPMN models simply as a different way of writing code which should still be done by a developer. Of course he/she communicates with business stakeholders to do the right thing. And the true benefit of using BPMN and a workflow engine is to **facilitate the collaboration between the different roles**!

### BizDevOps
Having an executable, visual and understandable model has benefits for business stakeholders, developers and operators. It also improves the communication and collaboration between them big time.

Let’s dive into that:

![](https://cdn-images-1.medium.com/max/800/1*jDKtKKqp3xtcUaHQiiY0ag.png)

The business can understand visual workflows and discuss the underlying business process and related requirements.

![](https://cdn-images-1.medium.com/max/800/0*YIS4d167aZOAtxLP.jpg)

Graphical report for one scenario run as automated unit test (here: [Camunda Test Coverage](https://github.com/camunda/camunda-bpm-process-test-coverage)). The payment workflow is taken from the [flowing retail example](https://github.com/flowing/flowing-retail). Use cases for workflows can be found in any industry.Developers get a workflow engine which is a persistent state machine with a lot of additional features. On top of that they can benefit from the visual model as they gain more context into what they are actually working on. For example, some tools can create graphical reports for test scenarios.

![](https://cdn-images-1.medium.com/max/800/1*F5Kh9RMh6wpljWJKUP9bjQ.png)

Exemplary operations tool from a workflow engine (here: [Camunda Cockpit](https://camunda.com/products/cockpit/))Another huge gain lies within operations as a workflow engine provides a lot of context to understand and resolve problems or failures. The possibilities to directly intervene in real-time (e.g. repair failed workflow instances or correct data) help keep everything up and running.

![](https://cdn-images-1.medium.com/max/800/0*dK8u2-MIeI1r-Mwk.png)

Audit data can be leveraged to optimize workflows (here: [Camunda Optimize](https://camunda.com/products/optimize/))And as workflow engines write a lot of audit data, one can leverage this historical data to find bottlenecks and possibilities for improvement for your workflows. This might be done by any stakeholder as everybody can understand the BPMN workflow models.

### Summary
In this short post I wanted to highlight the value of graphical but directly executable models in BPMN for business stakeholders, developers and operators (BizDevOps). This is not about getting rid of IT by following a low code approach but rather provide benefits to each of the roles and facilitate collaboration between them.

PS: Screenshots are taken from [Camunda](http://camunda.com/), an open source platform for workflow automation. As co-founder of Camunda I simply know this tool the best.
