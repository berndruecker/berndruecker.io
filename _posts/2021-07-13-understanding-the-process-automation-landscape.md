---
layout: post
title: "Understanding the process automation landscape"
date: 2021-07-13 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "spring", "event-driven", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "In 2015, Deutsche Telekom started to apply robotic process automation RPA, one of many tools in the whole process automation space. Over time the company dev..."
canonical: https://blog.bernd-ruecker.com/understanding-the-process-automation-landscape-9406fe019d93
---
### Understanding the process automation landscape

#### Processes and process automation take many forms. Here’s how to navigate the growing ecosystem of tools for automating everything from simple repetitive tasks to complex custom workflows.
*This article was first published on InfoWorld: **https://www.infoworld.com/article/3617928/understanding-the-process-automation-landscape.html*

In 2015, Deutsche Telekom started to apply robotic process automation (RPA), one of many tools in the whole process automation space. Over time the company developed [an army of more than 2,500 RPA “bots”](https://blog.bernd-ruecker.com/process-automation-in-harmony-with-rpa-720effdb0513) in a huge success story. But they also had to learn that even if RPA has “process automation” in its name, it does not really automate processes, but tasks.

This is a common misunderstanding that is rooted in the complexity of the process automation landscape, where tool categories are multidimensional and difficult to capture. In this article I will answer the question I get asked almost every day (what is process automation?) and provide an overview of the process automation space.

For the sake of brevity, I will narrow the scope of process automation to the following:

- **Business processes and digital processes**: These are the typical business processes you know from most companies (like customer onboarding, claim settlement, loan origination, order fulfillment), often spanning a couple of different systems end to end. The term “digital process” seems favorable nowadays, because the term “business process” is often considered old school.
- **Integration processes**: Processes that focus on the integration of systems or services, for example to orchestrate microservices or guarantee consistency when doing remote communication.

Other process automation use cases are explicitly **out of scope**:

- **Processes between untrusted participants** (such as separate companies): This is a [potential setting for blockchain](https://blog.bernd-ruecker.com/how-blockchain-can-drastically-simplify-business-processes-cc0828918b85).
- **Infrastructure provisioning or IT automation **(e.g. Ansible, Terraform): This is a domain on its own with specialized tools.
- **Continuous integration/continuous delivery** (e.g. Jenkins, GitHub Workflows): CI/CD build pipelines are standard processes in software engineering that are automated by standard software.
- **Internet of things **(e.g. Node Red): IoT use cases are often tackled with dedicated tooling that I would categorize as task automation. For simplicity, I will leave this discussion out of scope for this article.

![](https://cdn-images-1.medium.com/max/800/0*cvDvRi8kwNNpfXRi.jpg)

Now, there are two very different types of digital or integration processes:

- **Standard processes**: Whenever your company doesn’t want to differentiate via the process you can buy commercial off-the-shelf software (COTS) like ERP, CRM, or HR systems. In this case, you typically adapt your working procedures to the software.
- **Tailor-made processes**: Some processes are unique to an organization and because of that need to be tailor-made to the organization’s needs. While these processes might be the same across different organizations (e.g. customer on-boarding, order management, claim settlement), the way the organization designs and implements them is unique and that can help differentiate them in their market. This enables organizations to be more competitive, conduct their business more efficiently, reduce costs, increase revenue, and transform into a more digital business.

There is some gray area in between these two categories when you customize your standard software. But companies have become more and more cautious about doing this because of bad experiences in the past.

The decision needs to be made separately for every process you have in the company. And please note: There is no right or wrong decision, except that your choice should reflect your business strategy. This article will focus on tailor-made processes.

### Software to automate tailor-made processes
Tailor-made processes involve building software for process automation. This is “software to build software” and can be roughly categorized in terms of two dimensions (the nature of the tools, and the nature of the automation), as illustrated in the figure below:

- **Process automation** cares about automating the control flow of the process. It focuses on the sequence of tasks, not a single task as such. **Task automation** automates single tasks in a process, e.g. by integrating with some system.
- **Developer-friendly tools** integrate frictionlessly in typical developer tool stacks and journeys, but solve certain problems for the developer that are specific for process automation (e.g. providing persistence of the process state, graphical process models, versioning of process models). Developer-friendly tools require software development to build a solution. **Low-code tools** allow non-developers to implement automation logic by providing sophisticated graphical user interfaces and wizards, hiding technical details. This allows different roles to build solutions, but also limits possibilities and requires proprietary know-how.

![](https://cdn-images-1.medium.com/max/800/0*QkZ92yG45C1EWZwX.jpg)

With these two dimensions, you can sort tools into the four major buckets described in the next sections.

### Low-code task automation
Typical examples of low-code task automation include application integration tools and RPA:

- **Application integration tools** (e.g. [Zapier](https://zapier.com/), [IFTTT](https://ifttt.com/), [Tray.io](https://tray.io/), [Integromat](https://www.integromat.com/en/)): Application integration tools can execute actions when some event happens, for example inserting new data into [Airtable](https://airtable.com/) when a [Trello](https://trello.com/) card is completed. Some of these tools extend beyond the boundary of task automation, also providing basic process automation capabilities (e.g. [tray.io](https://tray.io/).
- **Robotic process automation (RPA) tools** (e.g. [UiPath](https://www.uipath.com/)): [RPA tools](https://blog.bernd-ruecker.com/how-to-benefit-from-robotic-process-automation-rpa-9edc04430afa) can automate tasks within legacy systems that don’t provide any API. This is about screen scraping and simulating mouse or keyboard actions — kind of like the Microsoft Office macro recorder on steroids.

Low-code task automation tools are *great for solving simple integration problems in isolation* and help to remove manual integration work, such as copying data over from system A to system B. The immediate business value is the reason that RPA is such a huge success.

However, the automation scope must be simple enough. And note that resulting solutions are often *untested, naive, and hard to maintain*. Many solutions focus on the happy case and forget exceptional situations, which then hit unexpectedly in production, often going unnoticed. This can make the solutions brittle.

### Developer-friendly task automation
Automating single tasks in a developer-friendly way typically means leveraging not only software development but also the following:

- **Integration frameworks** (e.g. [Apache Camel](https://camel.apache.org/)): Integration frameworks ease the job of a developer for certain tasks like communication with the file system, messaging middleware, and other interface technologies.
- **Batch processing**: The classical way to automate single tasks is with batch jobs that apply this task to every row in a certain dataset.
- **Event-driven architecture (EDA)**: A component can react to data in a stream, without knowing where this data is coming from. Common tooling includes event brokers like [Apache Kafka](https://kafka.apache.org/).

In contrast to low-code solutions, developer-friendly solutions *require software developers* to be involved. On the other hand, these developers are generally very productive, as they can work in the stack known to them. Also, the resulting solution is *typically more stable* and *can solve more complex problems*.

### Chaining task automations
Task automation tools *do not implement business process flows*. However, a series of RPA bots, integration tasks, or event subscriptions might form a logical chain that implements a business process. This comes with two challenges. First, the process flow does not have its own persistence, making it hard to determine the current state of any instance. Second, the [*control flow logic is nowhere visible*](https://martinfowler.com/articles/201701-event-driven.html), making these architectures hard to understand and maintain. (This is further described in my talk, “[Complex event flows in distributed systems](https://berndruecker.io/complex-event-flows-in-distributed-systems/).”)

There are two categories of tools that focus on providing visibility into these chains of tasks:

- **Process mining tools**: These products can help you understand how processes are actually automated using a bunch of legacy tools. Typically, this involves loading and analyzing a bunch of log files from these systems, discovering correlations, and mapping the process flows.
- **Process events monitoring tools**: These tools allow users to map events to a process model that is either provided or discovered on the fly. In contrast to process mining, which is typically based on log file analysis, process events monitoring focuses on ingesting live event streams, probably produced by your event-driven architecture.

### Low-code process automation
Process automation tools automate the control flow of multi-step processes. Their focus is less on the single task and more on the interplay between tasks. Processes are typically long-running in nature, which leads to their own requirements for the tool (persistence, operations tooling, etc).

Low-code tools aim to allow non-developers to implement these processes. Typical tool categories include the following:

- **Traditional business process management suites (BPMS)**: Now called “intelligent” BPMS ([iBPMS](https://www.gartner.com/reviews/market/intelligent-business-process-management-suites)) by Gartner, tools in this space include [Pega](https://www.pega.com/business-process-integration) and [Appian](https://www.appian.com/platform/bpm-suite/).
- Integration platform as a service (iPaaS) tools: [iPaaS offerings](https://www.gartner.com/reviews/market/enterprise-integration-platform-as-a-service) provide basic possibilities to implement process flow logic. Examples include [Tray.io](https://tray.io/) and [Process Street](https://www.process.st/).
- **Robotic process automation (RPA) tools:** RPA tools are sometimes abused to automate processes. I strongly advise [against doing this](https://blog.bernd-ruecker.com/how-to-benefit-from-robotic-process-automation-rpa-9edc04430afa), but I wanted to include this option here for completeness.

Some of these tools can really *help to automate simple processes*. If you are a startup, you might get along with a typical set of SaaS applications and then connect them using iPaaS solutions sufficiently. But these approaches *fall short on complex business processes or integration scenarios*.

What I have regularly found is that low-code products do not deliver on their promise and less tech-savvy [citizen developers](https://www.google.com/url?q=https://www.gartner.com/en/information-technology/glossary/citizen-developer&sa=D&source=editors&ust=1613474743172000&usg=AOvVaw3PQbdptRxO1mA0jX6tNUL9) cannot implement core processes themselves. As a result, companies have to revert back to their IT departments and ask them to assign professional software developers to finish the job, which in turn are not productive with proprietary low-code stacks.

### Developer-friendly process automation
There are tools that allow software developers to productively work on process automation projects:

- **Developer-friendly workflow engines, process orchestrators, or microservice orchestrators**, which come in three forms:
- **a) Open source product**: Lightweight tools with enterprise editions being available from a vendor, such as [Camunda](https://camunda.com/), [JBoss jBPM](https://www.jbpm.org/), or [Flowable](https://flowable.com/). Having a lively open source project and community plus the guarantees from a vendor that depends on the revenue stream are a great combination.
- **b) SaaS**: Many tools are provided as a managed service, either SaaS-only, such as [AWS Step Functions](https://aws.amazon.com/step-functions/) or [Google Workflow](https://cloud.google.com/workflows), or as a managed version of an existing open source product, like [Camunda Cloud](https://camunda.com/products/cloud/). Note that most of the cloud providers currently focus more on integration and less on business processes.
- **c) Open source project**: Bigger organizations often develop their own homegrown stack of tools, including a workflow engine. Some of these tools are made available under an open source license, but without any possibilities for support, guarantees, or possibilities to influence the roadmap. These tools are opinionated about the environment, as they are built for one specific organization rather than the overall market. Well-known examples are [Netflix Conductor](https://github.com/Netflix/conductor) and [Uber Cadence](https://github.com/uber/cadence), the latter being the foundation of the relatively new startup [Temporal](https://www.temporal.io/).
- **Digital process automation (DPA)**: A category that basically extends the BPMS category to concentrate on digital end-to-end processes in the context of digital transformation. The boundaries of this broad category are not sharp at all. Many vendors from all categories sketched here claim DPA for marketing reasons. Given that digitalization and end-to-end process automation are complex in nature, I place this category into developer-friendly process automation.

Sometimes, tool categories without specific support for process automation are also evaluated in the context of process automation projects. Data pipelines are a case in point. Because they can often be modeled graphically, people are tempted to use them for process automation.

- **Data pipelines** (e.g. [Apache Airflow](https://airflow.apache.org/), [Spring Cloud Data Flow](https://dataflow.spring.io/)): These tools have a different focus and thus lack important features for the process automation use case, such as support for control flow constructs like loops. Additionally, these tools don’t have their own implementation of persistence, so the state of a process instance is the data item flowing through the pipe.

Of course, one could also simply *hard code everything* to automate a process, yielding a bespoke workflow engine, which you should definitely avoid.

### Summary and conclusion
With the thoughts above in mind, I sketched the landscape shown in the figure below.

![](https://cdn-images-1.medium.com/max/800/0*nb-8I-31DXrXvoMr.jpg)

And the following table lists the main tool categories described in this article, along with some exemplary tools. (I apologize in advance for all the tools I missed!) You can find a [table in text format with working links here](https://www.infoworld.com/article/3617928/understanding-the-process-automation-landscape.html?page=2).

![](https://cdn-images-1.medium.com/max/800/1*vGRw15slrahGGoLIfyArVg.png)

I see developer-friendly workflow engines as the sweet spot to automate complex tailor-made processes. Low-code approaches also have their merits, typically when automating single tasks or simple processes in an environment that does not require much governance.

I plan to write a post on what I call [The Process Automation Map](https://www.slideshare.net/BerndRuecker/process-automation-forum-april-2021-practical-process-automation/15) soon, so follow me on social to keep up-to-date.
