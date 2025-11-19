---
layout: post
title: "Exploring the Process Automation Map"
date: 2021-11-23 12:00:00 +0000
categories: [blog]
tags: ["camunda", "process-automation"]
author: Bernd Ruecker
excerpt: "I recommend reviewing the introduction to the process automation maphttps://techspective.net/2021/11/22/the-process-automation-map/ first. As a quick recap, ..."
canonical: https://blog.bernd-ruecker.com/exploring-the-process-automation-map-7d9aa181a747
---
### Exploring the Process Automation Map
Earlier this year, I introduced the idea of the [process automation map](https://techspective.net/2021/11/22/the-process-automation-map/). Over time, it has proven useful in several customer scenarios. In today’s post, I’ll dive deeper into the dimensions of the map to help you rate your processes.

I recommend reviewing the [introduction to the process automation map](https://techspective.net/2021/11/22/the-process-automation-map/) first. As a quick recap, the map defines five dimensions on which you can rate processes you plan to automate. This rating will help you select the right solution approach.

![](https://cdn-images-1.medium.com/max/800/0*AzmswYgcY3mJ6rUZ)

Let’s explore these dimensions one by one.

### Standard processes vs. unique processes
Every organization has standard processes. For example, around payrolls, tax statements, and absence management. These processes are the same in every company, which is why you can simply buy standard software automating them. For instance, in my own company [Camunda](https://camunda.com/), we use spenddesk.com to manage expenses, automating much of the processes around expense management (e.g. payments, receipts collection, approval, reimbursement, etc.).

In contrast, there are likely processes very unique to your company; they require tailor-made solutions. A good example is [NASA and their Mars robot](https://camunda.com/customer/nasa/). The process to process data from the robot and calculation of the robot’s movements are pretty unique; very few organizations across the planet do this. In this case, uniqueness is rooted in the fact that NASA has a very unique** business model.**

But more often, the uniqueness simply comes from a **unique set of IT systems**, typically because of existing **legacy systems**. Take, for example, the customer onboarding process in a bank. Even if much of the required functionality is available in the core banking system, a unique set of integration requirements (for example, with your legacy mainframe system), makes the process very unique.

Now, these three use cases are rated differently on the map. Please note, that the exact point on the map is not so important, it is simply a visual aid to discuss direction:

![](https://cdn-images-1.medium.com/max/800/1*jlvuStnY5EFbnKlozbOODA.png)

The tool categories to use are also indicated. For standard processes, you buy standard software. For unique processes, you need tailor-made solutions.

As a rule of thumb, deviations from the standard are more often the case with core processes, like in the customer onboarding or NASA case, than with support processes, like absence management. The latter are seldom unique enough to justify tailor-made solutions, as deviations rarely make the business more successful (exceptions confirm the rule of course).

But core processes also don’t have to be unique by default. Imagine a small webshop selling sustainable bike helmets made out of coconut fibers (no need to Google, I just made this up.) The product is super innovative, but the core order fulfillment process can be standard; an off-the-shelf Shopify account might be all the company needs.

### Tailor-made solutions need a more precise rating
For standard software, you can probably ignore the other dimensions of the map, and you are done. In our example, there is no need to think further about absence management.

But for tailor-made solutions, you must understand the other four dimensions to select a solution approach for the process at hand. As introduced in the [Process Automation Map](https://docs.google.com/document/d/1IKudqsx4PpYqoEFa6_vNL93JzvHmZfAvTw4kouol3BI/edit), the two main solution categories for tailor-made processes are low code or pro code (developer-friendly) tools.

Low code means non-developers are enabled to build the solution, which is typically reached by a mixture of abstractions of technical details, pre-built components, and graphical wizards. Pro code means software developers are accelerated by tools that solve all problems related to process automation, in addition to proven software engineering best practices. You can read more about tool categories in [understanding the process automation landscape](https://blog.bernd-ruecker.com/understanding-the-process-automation-landscape-9406fe019d93).

The following image gives you a sneak preview of which solutions have which sweet spot. The following discussion of the remaining dimensions will explore this in more detail.

![](https://cdn-images-1.medium.com/max/800/0*YrurY7UEeemjbXfv)

### Process complexity: simple vs. complex
Processes vary in complexity. For example, I run a personal process around speaking at conferences. Conferences are maintained in Airtable, and some additional Zaps (integration flows in Zapier) automate important parts of my call-for-paper processes. For example, to remind me on Slack when a call-for-papers is about to expire. These processes are relatively simple and deal only with a very limited set of applications, all of them with well-known cloud connectors.

Compare this to an end to end business process, like a tariff change for a telecommunication customer that not only needs to take complex pricing rules into account, but also talk to many different bespoke IT systems. For example, to enter the changes into CRM or billing systems, or to provision changes to the telecommunication infrastructure.

![](https://cdn-images-1.medium.com/max/800/0*mdHgACFQE-oVtoPe)

Generally speaking, there are different drivers of complexity:

- The **number and nature of involved applications or people**. For applications, their own complexity and ability to integrate is especially important. It is a big difference to connect to a well-known cloud tool like Salesforce, than to a legacy mainframe application which is a black box.
- The **number of developers** required to work on a project.
- The **number of departments** or people involved in discussing how a process is implemented.
- The **number of users **that do operational work as part of the process instances, e.g. via human tasks.
- The **complexity of the user interface**, as some processes might not need any UI, some only simple forms, and in other cases, you might even need a fully-fledged, single-page application to support the users.
- **Compliance** requirements. For example, financial processes often need to comply with many legal requirements. Auditors might ask not only about how processes are implemented in general, but also want to look into specific instances from history to understand what happened in certain situations.

The more complex requirements are, the more best practices from software engineering you will need to handle them. In contrast, simple processes can also be handled by low code tooling where you simply drag together a process from standard bricks.

### Scale: small vs. big
The dimension scale can relate to various things. To avoid any confusion, I limit “scale” to “**load” **in the context of the map, so essentially the **number of process instances** in a certain timeframe. Some consider the number of systems or teams involved as part of “scale”; I explicitly put these factors into complexity.

For example, one of our customers implements [a process that must be able to process two million payments per hour](https://page.camunda.com/cclive-2021-goldman-sachs). This is definitely a big scale and poses different requirements compared to the management of my handful of talks a month. Foremost, the chosen technology must be able to handle the targeted scale and also help you navigate failure scenarios at scale; for example, if a core system faces an outage and thousands of process instances need to be retriggered once it comes up again.

![](https://cdn-images-1.medium.com/max/800/0*zrFVe_djnSuJD3fl)

**Volatile loads** might further lead to requirements around elasticity, so you need to keep changes to the scale in mind. For example, if you provide some service via the internet and run a super successful ad, you want your delivery process to be ready to scale to the increased demand without interruptions.

### Scope: task vs. process
Here comes my favorite dimension. I just understood last year how big of a source of confusion this is for a lot of people, maybe even the largest source of confusion in the process automation space: the difference between task and process automation.

A process consists of multiple steps in some logical correlation, like a sequence, probably branched by some decisions, maybe looping back under some circumstances, and so forth. This is well explained by [process models in BPMN](https://camunda.com/bpmn/).

Processes are typically long-running, as they might need to wait at any point in the process (e.g. for humans to do some work, for a service to become available, for customer responses to arrive, or simply for time to pass). This is why solutions need to be able to persist their state durably when waiting.

In contrast, a task is one step in the overall process. Tasks are typically atomic and can be executed in one go. There is typically no need to wait for something and persist state, thus I consider tasks rather stateless.

You might automate a process, but still, tasks are completed by humans. This can be achieved using task list user interfaces. A workflow engine can keep track of the state of the overall process, measure cycle times, or escalate if process instances take too long. However, the real work is done by the humans.

You can also automate tasks, usually using robotic process automation (RPA), decision management (DMN), machine learning (ML/AI), or simply software in the widest sense. You might do this without looking at the overall process. In [Process Automation in Harmony with RPA](https://blog.bernd-ruecker.com/process-automation-in-harmony-with-rpa-720effdb0513), I describe the journey of Deutsche Telekom and how they started with task automation using RPA. They did not look at the process layer, and finally ended up with what they call “spaghetti bot automation”. Telekom then introduced a separated process layer that resolved these problems.

To generalize, process and task automation are both useful, best in combination, but also addressed by different tools. I always like to emphasize that RPA is not at all meant to automate processes. [I found a lot of confusion](https://blog.bernd-ruecker.com/how-to-benefit-from-robotic-process-automation-rpa-9edc04430afa) around robotic *process automation*, which in reality is *task automation*.

Hence, it is important to be clear about what you want to automate. A multi-step process or a single task? This has a big influence on your tooling requirements and the solution you choose.

![](https://cdn-images-1.medium.com/max/800/0*C-HjlagBcKnV12E8)

### Project setup: ad-hoc or temporary vs. strategic
As part of the overall Camunda journey (Camunda is the company I co-founded), our marketing teams grew quite a bit over the last few years. We hired more people, introduced new functions, and explored a heck of a lot of new ideas about what to do. Many of those ideas required some IT support. During exploration, you have no idea yet what idea makes the most sense and how the process will exactly look like later on. Thus, we did a lot of manual work but applied low code tooling for areas that could simply be automated. We did not aim for a very stable solution that could run for years, we just needed something to explore or validate an idea. We were fine with the fact that sometimes only the original creator really understood the temporary solution.

Only when we took some of these ideas to the next level and scaled their usage could their importance grow to a point where it became quite strategic and required a sustainable and maintainable solution.

Other examples are most of the core processes among our customers. These processes are very strategic, so the organizations even have their own departments responsible for operating and maintaining a single process. Think of processing two million payments per hour, as mentioned earlier.

Part of the project setup dimension is therefore also the criticality of the process. If a process is critical for your company to survive, you need to make sure it runs smoothly and is stable. If you can lose real money on process failures, you need operation capabilities that prevent failures from happening or going undetected.

![](https://cdn-images-1.medium.com/max/800/0*6hG6t5QOt8sQP-gY)

### Conclusion
Understanding these dimensions is a good exercise to help you decide what kind of process automation solution fits into your use case. Generally, you want to rate your process on this map as described in [the process automation map](https://techspective.net/2021/11/22/the-process-automation-map/). Then, look at typical sweet spots of solution categories.

I hope this is helpful to you and I recommend trying it out in your own projects. To help you create your own map, I uploaded a template slide [here](https://docs.google.com/presentation/d/1sh6tsSp-q2uz4pmUmAmPDiGo-UUFGBlsANOahCGGqmA/edit#slide=id.p). Feel free to use and distribute it at your own discretion. As always, I’m happy to receive any feedback or especially copies of your own processes rated on the map.
