---
layout: post
title: "5 Workflow Automation Use Cases You Might Not Have Thought Of"
date: 2018-04-23 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "event-driven", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "I regularly hear opinions like:"
---
### 5 Workflow Automation Use Cases You Might Not Have Thought Of

#### Because workflow automation is so much more than human task management!
*This article was *[*originally published at The New Stack*](https://thenewstack.io/5-workflow-automation-use-cases-you-might-not-have-considered/)*.*

I regularly hear opinions like:

> Workflow automation? This is not for us! We do not have human task management and task lists. We fully automate our core business. And we have such a huge load that cannot be handled by workflow engines anyway.

This is so wrong! That’s why I want to elaborate on my view on use cases for workflow automation today. I hope it is useful for your reference and probably even gives you some new thoughts.

T*L;DR: **I see five clusters of use cases for workflow automation technology, ranging from very technical use cases (like stateful retries in case a remote service is not available) to typical business processes (like order-to-cash). Modern workflow engines are lightweight and can also operate high volumes at low latency making them applicable for every problem that requires a state machine.*

To avoid any confusion because of overloaded terms: I do not talk about automating the developer workflow or deployment pipelines. This article is about leveraging workflow engines to solve requirements in the resulting application or system.

### Use cases for workflow automation
Workflow automation unifies multiple use cases around [long-running behavior](https://blog.bernd-ruecker.com/what-are-long-running-processes-b3ee769f0a27). I drew a picture which is my personal opinionated visualization:

![](https://cdn-images-1.medium.com/max/800/1*xsRiJtJhmlXK155Yx9r9MA.png)

I sorted the use cases into five clusters. I will go over these clusters in the rest of the article. I rated them on two dimensions:

- **Business **or **IT**, meaning the main driver of the requirement at hand. This can be business departments because they want to implement core business capabilities. But it might as well be IT because they want to solve technical challenges. Most use cases meet somehow in the middle anyway.
- The duration of a workflow instance, which can range from very **short running **to very **long running. **The latter might mean that workflow instances take hours, days or even weeks to complete. Long *running *is misleading by the way, as it basically means that the workflow instance is *not *running but *waiting *most of the time. So that’s why you need to handle persistent state the further right you move on the scale.

One important side remark: Just because a use case is more technical doesn’t mean it is not relevant for the business. It is very relevant to decide if you want to retry a failed service call (e.g. customer rating) or just return a default value (green). It is relevant to decide with which inconsistencies you can live or which you have to resolve.

Just in case you wonder why to use a workflow engine at all, there are two main value propositions:

- It is a **persistent state machine **that also solves subsequent requirements like versioning of process models, scheduling mechanisms, operational control and much more.
- For long-running workflows, the **graphical visibility **is vital for [successful BizDevOps](https://blog.bernd-ruecker.com/bizdevops-as-avalue-proposition-of-workflow-engines-f342509ba8bb).

So let’s get started and go through the five clusters one by one. I will also give links to source code examples for each use case in order to make it more concrete (at least for developers). These examples are directly runnable on the [open source workflow engine](https://thenewstack.io/camunda-offers-a-microservices-workflow-engine-built-on-bpmn/) from [Camunda](https://camunda.com/) (hint: I am biased in tool selection as I co-founded Camunda and therefore naturally have examples for this one at hand).

![](https://cdn-images-1.medium.com/max/800/1*ER6XvcEBkOeEr5_f5RNdTA.png)

### 1. Business process automation
Business processes implement important core capabilities of a company like delivering goods or services a customer ordered (“order-to-cash”). Business processes are often long running in nature. They might involve:

- Straight through processing/service orchestration
- Waiting for internal or external messages, timers (e.g. the promised delivery date) or other events
- Human task management

**Typical examples**

- Order fulfillment
- Application management
- Invoice management/billing
- Inbound/outbound management (often named input/output management)
- Approval of various things (orders, purchase orders, travel expenses, invoices, etc…)
- Stock trading
- Content preparation and delivery

![](https://cdn-images-1.medium.com/max/800/1*1J4XxvYKU2AhJOBWbMaDDA.png)

Real-life examples of workflow models implementing end-to-end business processes (credit card application, 2-factor authentication, claim handling)**Code example**

[**camunda-consulting/camunda-showcase-insurance-application**
*camunda-showcase-insurance-application - Showcases BPMN and DMN on Camunda by processign a car insurance application*github.com](https://github.com/camunda-consulting/camunda-showcase-insurance-application)
### 2. Communication in distributes systems
Distributed systems become the new normal in IT. Distributed systems are complicated because of [the eight fallacies of distributed computing](https://en.wikipedia.org/wiki/Fallacies_of_distributed_computing). Most developers I know are not yet aware of the magnitude of changes coming due to the fact that remote communication is unreliable, that faults have to be accepted and that you exchange your transactional guarantees with eventual consistency. We as developers really have to adjust our toolboxes in order to cope with these new challenges. I wrote about some examples in [three common pitfalls in microservice integration — and how to avoid them](https://blog.bernd-ruecker.com/3-common-pitfalls-in-microservice-integration-and-how-to-avoid-them-3f27a442cd07).

Workflow engines are an important ingredient to solve a couple of challenges you will run into.

**Typical examples**

- Retrying services invocations if the services are not available or not responding. Retrying might be done for several hours or even days. I call this stateful retry.
- Wait for messages (e.g. an asynchronous response or an event).
- Timeout when waiting for messages.
- Correlate several messages (e.g. something happens only if 3 messages all arrive).

![](https://cdn-images-1.medium.com/max/800/1*Q_8g6DVtSC0yR34zGS1IRg.png)

Visualization of use cases using the BPMN notationWhen serving this use case you might get very small workflow models which is perfectly fine. These models feel like integration flows you probably also know from ESB-like tools.

These workflows are often *potentially *long-running — as you get synchronous results in milliseconds if everything is good but you might need seconds, minutes or much longer to resolve failure situations.

**Code example**

[**flowing/flowing-retail**
*flowing-retail - Event- and domain-driven order fulfilment using Kafka or Rabbit as Event Bus and Java, Spring Boot &…*github.com](https://github.com/flowing/flowing-retail/tree/master/payment-rest)
### 3. Distributed Transactions
As mentioned you cannot rely on ACID transactions in distributed scenarios. ACID stands for atomicity, consistency, isolation and durability and is what you have experience from working with a typical relational database (begin transaction, do some stuff, commit or rollback). Attempts like 2-phase-commit (XA) bring ACID to distributed scenarios but are not really used much in real-life as they do not scale. But you still have to solve the business requirements of having a one-or-nothing semantic for multiple activities.

This is typically addressed by remembering which activities were already executed and invoke so-called compensation activities whenever the business transaction fails. A compensation activity semantically undoes the original activity (e.g. refund money you have taken from a credit card). It is important to note that this model accepts to have temporarily inconsistent states, but makes sure everything gets consistent in the end. This relaxed view on consistency is known as **eventual consistency **and sufficient for most real-life use cases.

![](https://cdn-images-1.medium.com/max/800/1*dB4VjBZeG4nwInk27kLKwA.png)

The compensation in BPMN is a way to implement Sagas / distributed transactionsThis is also known as the Saga-Pattern. I plan a dedicated more in-depth article on this soon.

**Typical examples**

All activity chains that care about consistency in distributed systems. The classical example is booking a trip where you book a hotel, car and flight one after the other — and need to cancel bookings if something goes wrong. Actual real-life use cases are often even much more trivial.

**Code example**

[**flowing/flowing-trip-booking-saga**
*flowing-trip-booking-saga - Example implementation of the Saga pattern for the classic trip booking example using the…*github.com](https://github.com/flowing/flowing-trip-booking-saga)
### 4. Orchestration
Modern architectures are all about decomposition, e.g. into microservices or serverless functions. When you have many small components doing one thing well you are forced to connect the dots to implement real use cases. This is where orchestration plays a big role (see for example [Orchestrating Azure Functions using BPMN and Camunda — a case study](https://blog.bernd-ruecker.com/orchestrating-azure-functions-using-bpmn-and-camunda-a-case-study-ff71264cfad6)). It basically allows invoking components (or services, activities, functions) in a certain sequence.

![](https://cdn-images-1.medium.com/max/800/1*J2m4TccnY9FiKYNBOVKyUg.png)

**Typical examples**

- One microservice invokes three others in a sequence
- Multiple serverless functions need to be executed in order

**Code example**

[**flowing/flowing-retail**
*flowing-retail - Sample application demonstrating an order fulfillment system decomposed into multiple independant…*github.com](https://github.com/flowing/flowing-retail/tree/master/zeebe)
### 5. Decision Automation
Being a workflow guy decision management for me is “the wingman” of workflow automation. Of course, it is a discipline on its own and deserves its own article, but I will only look at it from the workflow automation perspective today. And then it is a great tool to extract business decisions and separate them from routing decisions:

![](https://cdn-images-1.medium.com/max/800/1*jIPp9PB1NzVjhac4xtV9Ug.png)

Image taken from [Real-Life BPMN](https://www.amazon.de/dp/B01NAL67J8/)**Typical examples**

- Automated evaluation of eligibility or approval rules
- Validation of data
- Fraud detection or risk rating
- Calculation of derived values (e.g. discount, shipping costs)
- Determine assignees, e.g. who should best work on a human task

**Code example**

[**camunda-consulting/camunda-showcase-insurance-application**
*camunda-showcase-insurance-application - Showcases BPMN and DMN on Camunda by processign a car insurance application*github.com](https://github.com/camunda-consulting/camunda-showcase-insurance-application)
### Use cases get mixed in real-life
In real-life scenarios the use cases are often combined. So, for example, you might want to fully automate your application processing (*classical business process automation* with *straight through processing*). In order to do this you might have to invoke (*orchestrate*) several web services which means you do *communication in distributed systems*. Business rules could automatically decide the risk of fraud (*decision management*). If there is a case of suspected fraud, it is routed to a clerk (*human task management*).

**Flexible architectures**At this point I want to add a very quick side note: There are manifold options to setup your architecture to use a workflow engine. It does **not** mean that you have to introduce some central component which forces a low-code approach on you.

You might be interested in [Architecture options to run a workflow engine](https://blog.bernd-ruecker.com/architecture-options-to-run-a-workflow-engine-6c2419902d91) or [Avoiding the “BPM monolith” when using bounded contexts](https://blog.bernd-ruecker.com/avoiding-the-bpm-monolith-when-using-bounded-contexts-d86be6308d8) for more details.

### Performance and scalability
Opening up the use cases for workflow automation typically raises the question: Can the respective tools really handle the load we will get if we use it for each and every service invocation? This also includes use cases which are known as “low latency high throughput.” Hence the engines have to be really fast even under very high loads.

![](https://cdn-images-1.medium.com/max/800/1*Utp9bNQHo7eq4cDExbVFYA.png)

And yes, we start to see tools around that are able to handle this, [Zeebe.io](http://zeebe.io/) is one example and [AWS Step Functions](https://aws.amazon.com/step-functions/) another.

Zeebe, for example, reaches these new horizons of scalability by completely changing its internal architecture. A Zeebe broker is a distributed system on its own to handle replication and scale efficiently. It uses algorithms like [event sourcing](https://martinfowler.com/eaaDev/EventSourcing.html), [append-only logs](http://cidrdb.org/cidr2015/Papers/CIDR15_Paper16.pdf), [single writer principle](https://mechanical-sympathy.blogspot.de/2011/09/single-writer-principle.html) and [Raft consensus](https://raft.github.io/). Don’t worry if you don’t understand these concepts right away, that’s why there are middleware or cloud providers taking care of it. But it also gives you a taste of what I meant earlier in terms of changes coming to the way we develop software.

All of this allows for horizontal scalability never seen before in a workflow engine. My [co-founder names this “**big workflow**](https://blog.camunda.com/post/2017/12/camunda-year-in-review/)”:

> The next logical, inevitable stage is the “big workflow problem”: How can we handle activity chains that are complex, distributed, long-running and mission-critical? How can we handle them on a *massive* scale? This problem has three critical dimensions: Software development, technical operations and business visibility.

Currently, I am discussing use cases with customers that involve hundreds of thousands of instances per second. We are getting there!

### Summary
Workflow automation unifies multiple use cases around long-running behavior. In this article, I have named and clustered them. I showed that all are valid use cases and that modern technology can be a great help for all of these problems and related requirements.

*This article was *[*originally published at The New Stack*](https://thenewstack.io/5-workflow-automation-use-cases-you-might-not-have-considered/)*.*

As always, I love getting your feedback. Comment below or [send me an email](mailto:mail@bernd-ruecker.com).

*Stay up to date with my *[*newsletter*](http://eepurl.com/cDXPRj)*.*
