---
layout: post
title: "A Technical Sneak Peek into Camunda’s Connector Architecture"
date: 2022-08-03 12:00:00 +0000
categories: [blog]
tags: ["camunda", "orchestration", "spring", "event-driven", "architecture"]
author: Bernd Ruecker
excerpt: "Since then, many people have asked us what a connector is, how such a connector is developed, and how it can be used in Self-Managed. We haven’t yet publishe..."
canonical: https://camunda.com/blog/2022/07/a-technical-sneak-peek-into-camundas-connector-architecture/
---
### A Technical Sneak Peek into Camunda’s Connector Architecture

#### What is a connector? How does the code for a connector look like? And how can connectors be operated in various scenarios?
When Camunda Platform 8 launched earlier this year, we announced connectors and provided some preview connectors available in our SaaS offering, such as [sending an email using SendGrid](https://docs.camunda.io/docs/components/modeler/web-modeler/connectors/available-connectors/sendgrid/), [invoking a REST API](https://docs.camunda.io/docs/components/modeler/web-modeler/connectors/available-connectors/rest/), or [sending a message to ](https://docs.camunda.io/docs/components/modeler/web-modeler/connectors/available-connectors/slack/)[Slack](https://docs.camunda.io/docs/components/modeler/web-modeler/connectors/available-connectors/slack/).

Since then, many people have asked us what a connector is, how such a connector is developed, and how it can be used in Self-Managed. We haven’t yet published much information on the technical architecture of connectors as it is still under development, but at the same time, I totally understand that perhaps you want to know more to feel as excited as me about connectors.

In this blog post, I’ll briefly share what a connector is made of, how the code for a connector roughly looks, and how connectors can be operated in various scenarios. Note that the information is a preview, and details are subject to change.

### What is a connector?
A connector is a component that talks to a third-party system via an API and thus allows orchestrating that system via Camunda (or let that system influence Camunda’s orchestration).

![](https://cdn-images-1.medium.com/max/800/0*Gd46_wJBm4m3oRnz.png)

The connector consists of a bit of programming code needed to talk to the third-party system and some UI parts hooked into Camunda Modeler.

This is pretty generic, I know. Let’s get a bit more concrete and differentiate types of connectors:

- **Outbound connectors**: Something needs to happen in the third-party system if a process reaches a service task. For example, calling a REST endpoint or publishing some message to Slack.
- **Inbound connectors**: Something needs to happen within the workflow engine because of an external event in the third-party system. For example, because a Slack message was published or a REST endpoint is called. Inbound connectors now can be of three different kinds:

- **Webhook**: An HTTP endpoint is made available to the outside, which when called, can start a process instance, for example.
- **Subscription**: A subscription is opened on the third-party system, like messaging or Apache Kafka, and new entries are then received and correlated to a waiting process instance in Camunda, for example.
- **Polling**: Some external API needs to be regularly queried for new entries, such as a drop folder on Google Drive or FTP.

### Outbound example
Let’s briefly look at one outbound connector: [the REST connector](https://docs.camunda.io/docs/components/modeler/web-modeler/connectors/available-connectors/rest/). You can define a couple of properties, like which URL to invoke using which HTTP method. This is configured via Web Modeler, which basically means those properties end up in the XML of the BPMN process model. The translation of the UI to the XML is done by the [element template mechanism](https://docs.camunda.io/docs/components/modeler/desktop-modeler/element-templates/about-templates/). This makes connectors convenient to use.

![](https://cdn-images-1.medium.com/max/800/0*D_QzJibIqs0LWe9m.png)

Now there is also code required to really do the outbound call. The overall Camunda Platform 8 integration framework provides a software development kit (SDK) to program such a connector against. Simplified, an outbound REST connector provides an execute method that is called whenever a process instance needs to invoke the connector, and a context is provided with all input data, configuration, and abstraction for the secret store.

Now there needs to be some glue code calling this function whenever a process instance reaches the respective service task. This is the job of the connector runtime. This runtime registers [job workers](https://docs.camunda.io/docs/components/concepts/job-workers/) with Zeebe and calls the outbound connector function whenever there are new jobs.

![](https://cdn-images-1.medium.com/max/800/0*ZVVn4M0SDa2Cq6pL.png)

This connector runtime is independent of the concrete connector code executed. In fact, a connector runtime can handle multiple connectors at the same time. Therefore, a connector brings its own metadata:

With this, we’ve built a Spring Boot-based runtime that can discover all outbound connectors on the classpath and register the required job workers. This makes it super easy to test a single connector, as you can run it locally, but you can also stitch together a Spring Boot application with all the connectors you want to run in your Camunda Platform 8 Self-Managed installation.

At the same time, we have also built a connector runtime for our own SaaS offering, running in Google Cloud. While we also run a generic, Java-based connector runtime, all outbound connectors themselves are deployed as Google Functions. Secrets are handled by the Google Cloud Security Manager in this case.

![](https://cdn-images-1.medium.com/max/800/0*zPpJuqZo_SF9ULOS.png)

The great thing here is that the connector code itself does not know anything about the environment it runs in, making connectors available in the whole Camunda Platform 8 ecosystem.

### Inbound example
Having talked about outbound, inbound is a very different beast. An inbound connector either needs to open up an HTTP endpoint, a subscription, or start polling. It might even require some kind of state, for example, to remember what was already polled. Exceptions in a connector should be visible to an operator, even if there is no process instance to pinpoint it to.

We are currently designing and validating architecture on this end, so consider it in flux. Still, some of the primitives from inbound connectors will also be true:

- Parameters can be configured via the Modeler UI and stored in the BPMN process.
- The core connector code will be runnable in different environments.
- Metadata will be provided so that the connector runtime can easily pick up new connectors.

A prototypical connector receiving AMQP messages (e.g., from RabbitMQ) looks like this:

And here is the related visualization:

![](https://cdn-images-1.medium.com/max/800/0*bD7c8nTSoZveRt9w.png)

### Status and next steps
Currently, only a fraction of what we work on is publicly visible. Therefore, there are currently some limitations on connectors in Camunda Platform version 8.0, mainly:

- The SDK for connectors is not open to the public simply because we need to finalize some things first, as we want to avoid people building connectors that need to be changed later on.
- The code of [existing connectors (REST, SendGrid, and Slack)](https://docs.camunda.io/docs/components/modeler/web-modeler/connectors/available-connectors/) is not available and cannot be run on Self-Managed environments yet.
- The UI support is only available within Web Modeler, not yet within Desktop Modeler.

We are working on all of these areas and plan to release the connector SDK later this year. We can then provide sources and binaries to existing connectors to run them in Self-Managed environments or to understand their inner workings. Along with the SDK, we plan to release connector templates that allow you to easily design the UI attributes and parameters required for your connector and provide you with the ability to share the connector template with your project team.

At the same time, we are also working on providing more out-of-the-box connectors (like the [Slack connector](https://docs.camunda.io/docs/components/modeler/web-modeler/connectors/available-connectors/slack/) that was just released last week) and making them available open source. We are also in touch with partners who are eager to provide connectors to the Camunda ecosystem. As a result, we plan to offer some kind of exchange where you can easily see which connectors are available, their guarantees, and their limitations.

Still, the whole connector architecture is built to allow everybody to build their own connectors. This especially also enables you to build private connectors for your own legacy systems that can be reused across your organization.

### Summary
The main building block to implementing connectors is our SDK for inbound and outbound connectors, whereas outbound connectors can be based on webhooks, subscriptions, or polling. This allows writing connector code that is independent of the connector runtime so that you can leverage connectors in the Camunda SaaS offering and your own Self-Managed environment.

At the same time, connector templates will allow a great modeling experience when using connectors within your own models. We are making great progress, and you can expect to see more later this year. Exciting times ahead!
