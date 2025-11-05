---
layout: post
title: "Orchestrating Azure Functions using BPMN and Camunda — a case study"
date: 2018-01-08 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "For most use cases just calling one simple function is not enough, you have to call multiple functions in the right order to implement a proper end-to-end us..."
---
### Orchestrating Azure Functions using BPMN and Camunda — a case study
Serverless functions are all the hype at the moment whether it be AWS Lambda or Azure Functions. I am happy that I am allowed to share a case study from [NexxBiz](https://www.nexxbiz.io/) to show you how Azure functions and BPMN work together. For a customer in the insurance field they set up an architecture around the Microsoft Cloud (Azure). They have build a very interesting tool-chain to master this environment.

### The need for orchestration
NexxBiz run multiple customers (tenants) on their platform. They need to implement dedicated business processes or customize standard ones for every tenant. Business functionality is available as dedicated serverless functions. As they are using the Microsoft .NET stack, [Azure Functions](https://azure.microsoft.com/en-us/services/functions/) are the way to go.

For most use cases just calling one simple function is not enough, you have to call multiple functions in the right order to implement a proper end-to-end use case or business process. This is referred to as **orchestration**. And it is pretty typical as functions intend to cut overall business logic into rather small and stateless pieces. I want to spare any discussion on orchestration vs. choreography in this blog post but can recommend [Events, Flows and Long-Running Services: A Modern Approach to Workflow Automation](https://www.infoq.com/articles/events-workflow-automation) if you are interested).

![](https://cdn-images-1.medium.com/max/800/1*4a408RZeoOtMfMdim6xEGQ.png)

Azure does not offer complex orchestration capabilities, you can only do very simple flows within [Azure Logic Apps](https://azure.microsoft.com/en-us/services/logic-apps/) (which is basically [Microsoft Flow](https://flow.microsoft.com/)). Logic apps follow a[ low code approach which is typically not suitable for complex flows](https://blog.bernd-ruecker.com/the-flow-is-for-bpm-what-microservices-are-for-soa-5225c7908bae). That’s why they chose Camunda.

To give you a quick first impression, this is a simple but real-life orchestration flow from their system checking if a prospect is already known in the CRM, showing some runtime statistics as heatmap on top of the BPMN process model (using [Camunda Cockpit](https://camunda.com/products/cockpit/)).

![](https://cdn-images-1.medium.com/max/800/1*4eSw-CJdPo3D_uOaJEb3ag.png)

Orchestration flow expressed in BPMN and executed by Camunda (real-life example, therefore unreadable)
### Connect Camunda and Azure Functions
There are multiple ways to run the Camunda Workflow Engine (see [Architecture options to run a workflow engine](https://blog.bernd-ruecker.com/architecture-options-to-run-a-workflow-engine-6c2419902d91)). NexxBiz decided to minimize contact with Java and therefore,

- Run the engine in Tomcat on a Docker container in the [Azure Container Services](https://azure.microsoft.com/en-us/services/container-service/) (basically Kubernetes)
- Communicate via [Camunda REST API](https://docs.camunda.org/manual/7.8/reference/rest/)
- Leverage the [pull-prinicple for service tasks](https://docs.camunda.org/manual/7.8/user-guide/process-engine/external-tasks/). So they build small workers that pull work via the Camunda REST API and trigger Azure Functions.

See [Use Camunda without touching Java and get an easy-to-use REST-based orchestration and workflow engine](https://blog.bernd-ruecker.com/use-camunda-without-touching-java-and-get-an-easy-to-use-rest-based-orchestration-and-workflow-7bdf25ac198e) for more details on how to run Camunda in context of .NET.

In order to call the Azure Function they used the [Azure Service Bus](https://azure.microsoft.com/en-us/services/service-bus/). Every Azure Function can be naturally triggered by a message on this bus.

![](https://cdn-images-1.medium.com/max/800/1*XVAlXSfu1z4TC0arsOcHhQ.png)

To connect Camunda to the Azure Service Bus they decided to build small connectors consisting of three Azure Functions on their own:

- Fetcher: This periodically triggered function polls work from Camunda.
- Completer: This function is triggered by response messages and completes task in Camunda.
- FailedCompleter: This function is triggered by failure messages and completes the task in Camunda but indicates a failure.

The cool thing is, these functions are auto-generated and automatically provisioned during the deployment. No need to do anything manually.

![](https://cdn-images-1.medium.com/max/800/1*qXB5G1d745hP8bzwcPts8Q.png)

Any configuration they need is done via configuration of the external tasks or input/output mappings in the BPMN process.

![](https://cdn-images-1.medium.com/max/800/1*2al4gOfGeNkkYQoggz7BMw.png)

### Testing
An interesting detail I want to highlight is that they wrote Java Unit tests for their BPMN processes. But wait, didn’t they want to avoid contact with Java? Indeed. But our consultant onsite talked them into writing the unit tests in Java anyway — as we have so much support for doing this ([Camunda test support](https://docs.camunda.org/manual/latest/user-guide/testing/#junit-4), [assertions](https://github.com/camunda/camunda-bpm-assert/), [scenario tests](https://github.com/camunda/camunda-bpm-assert-scenario/) and [visualizations of test runs](https://github.com/camunda/camunda-bpm-process-test-coverage)). And this was a great idea as they told me:

> Thank god Niall (Note: [The guy with the hawk](https://camunda.com/learn/videos/)) talked us into sticking with Java Unit Tests, they are so powerful and easy to write. And as we can program in C# it is not hard to do that in Java.

### The build pipeline
To get this architecture off the ground they automated the build and deployment pipeline. And as they wanted to have much more control over these pipelines than they could get by [Visual Studio Team Services](https://www.visualstudio.com/team-services/) out-of-the-box, they “drunk their own champagne” and used Camunda to control this. So their tool stack looks like this:

![](https://cdn-images-1.medium.com/max/800/1*dmYJ1e7WX-oNVAsVOvYtiQ.png)

### User journey of their process engineers
Another interesting aspect was that they use [Cawemo](http://cawemo.com/) to capture and discuss business processes and their requirements in the first stage. It saves them lots of paper for specification. They told me that they just conduct a one day workshop instead which is sufficient to get a solid basis for a first working increment.

Whenever a BPMN model is mature enough it is exported into the developers workspace and placed into normal version control. Now the Camunda Modeler is used to add properties like expressions or input/output mappings for external tasks. A unit test for the process is also written.

When everything is executable the workflow can be deployed onto Azure as described above with all glue code being generated automatically.

### Why is this cool?
I asked the NexxBiz guys what they like about their approach and architecture. This is what they told me:

- BPMN helps a lot in communication and requirements engineering.
- The visibility of processes not only during design time but also during runtime is very powerful and provides insights they never had before.
- With BPMN they can provide powerful orchestration options, not only simple flows or hard wired peer-to-peer call chains.
- They gained a lot of overview and control. As all communication goes through a process somewhere, one workflow engine does always knows the context and keeps track of the flow. If a message gets lost or screwed up, there will be a workflow waiting in Camunda — and with relatively simple SLA monitoring these flaws can be found and easily fixed (by e.g. resending the message).

Thanks to NexxBiz for walking me through their architecture and allowing me to share this information. Please not that this blog posts reflects their architectural decisions and not expresses a recommendation from me or Camunda.

As always, I love getting your feedback. Comment below or [send me an email](mailto:mail@bernd-ruecker.com).

*Stay up to date with my *[*newsletter*](http://eepurl.com/cDXPRj)*.*
