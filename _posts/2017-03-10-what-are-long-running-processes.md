---
layout: post
title: "What are long running processes?"
date: 2017-03-10 12:00:00 +0000
categories: [blog]
tags: ["camunda", "orchestration", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "Hint: My goal is not to give a scientific definition. I just want to write down my current thoughts as an anchor for potential discussions, so I’m looking fo..."
---
### What are long running processes?
Some communities have big reservations when using terms like **workflow **(overloaded), **Business Process Management** or BPM (automatically considered to be a BPM monolith, see [The 7 sins of workflow](https://blog.bernd-ruecker.com/the-7-sins-of-workflow-b3641736bf5c)) or **orchestration** (reminds people of old SOA days, smart pipes or central engines). So I started using the term **long running process**, but now people are asking me: “Is this a long running process?” In this article I list typical use cases and clarify terminology.

*Hint: My goal is not to give a scientific definition. I just want to write down my current thoughts as an anchor for potential discussions, so I’m looking forward to get your feedback!*

### Sprint or Marathon? About short, long and very long running processes.

![](/assets/images/what-are-long-running-processes-1.png)

Must a long running process be a business process spanning days, weeks, months or even years? Or can it solely be an automated processes typically finished within milliseconds, but potentially waiting for system availability for a few seconds, minutes or sometimes hours?

Well, both! But doesn’t this pose different requirements on the solution? Only to a minor extent. Let’s dive into more details.

### Use Cases
*Edit: **I published *[*5 Workflow Automation Use Cases You Might Not Have Considered*](https://thenewstack.io/5-workflow-automation-use-cases-you-might-not-have-considered/)* on TheNewStack in the meanwhile, which gives an improved overview on use cases.*

**Straight through processing (STP)** refers to processes that are fully automated. There are no human steps involved (in the best case). You might also call this **service orchestration** (German insurance companies even have another nice word for this: “Dunkelverarbeitung” —translated with “processing in the dark”).

Why do I consider these processes to be long running? Well, STP normally involves some kind of service invocation. As soon as we invoke services, there is the risk of waiting, either because we use an asynchronous channel (e.g. messaging) or we run into a service outage and have to incorporate some retry mechanism. Waiting immediately means long running, I’ll come back to the question of “how long?” in a minute.

**Human task management** means to push tasks on a todo list of some user. I never know when he is going to pick it up, so we have to wait and voila: long running. For the sake of simplicity I consider **case management** to be part of this category in this post.

The term **workflow **is typically used for a STP *and *human task management. Very often these use cases are mixed anyway. The most common example is that you might involve the human whenever STP hits an undefined problem, hence: long running.

**Sagas **are a quite old but a relatively unknown concept (except in the Domain Driven Design community). Sagas solve the problem that technical two-phase commit does not scale. Therefor

> “a saga is a **long lived transaction** that can be broken up into a collection of sub-transactions that can be interleaved. All transactions in the sequence complete successfully or compensating transactions are ran to amend a partial execution.”

[*Quoted from Caitie McCaffrey*](http://sssslide.com/speakerdeck.com/caitiem20/applying-the-saga-pattern). Long lived = long running.

### Long running basically means waiting!
*Edit**: I added this section as I get this question very often.*

![](/assets/images/what-are-long-running-processes-2.png)

Long running doesn’t mean there is real action all the time. Typically long running means that the process is waiting most of the time. This is not done by blocking any threads but typically by persisting state to some storage mechanism. So the term “long lived” might be even easier to understand for some readers.

So don’t get confused: long running basically means waiting most of the time.

### Requirements
For all use cases you have to solve the same basic problems:

**State handling & persistence**: Whenever you have to wait, you need some kind of state handling. Typically you do not want to lose this state during a system hiccup — so you make it persistent. This might not be true for some systems only handling short-lived sagas within milliseconds where you trade performance for consistency. But most often, the duration of waiting doesn’t make a big difference; as soon as you wait, you persist.

When persisting state, you have to handle situations when the process does not progress or recover, so you have to handle **timeouts & escalations**. This is also true for STP when a bug in a third party system causes a missing response message. And as soon as you have processes waiting, you typically want to see what is going on, so you need some **monitoring**.

**Versioning**: Whenever you store state, you might run into situations where you develop a new version of your process and put it live, but still have instances of the old model running. Of course this is a much more pressing issue with very long running processes, but you normally also have to tackle it with shorter long running processes.

If you handle processes spanning **huge time frames**, things start to change. Think of life insurance; the process from applying for the contract till the payment (when you are dead) might (hopefully) span decades. Within this time frame you will definitely experience some major paradigm shifts in the IT industry (e.g. Mainframe to 3-tier-architectures to serverless, nosql and cloud). So I typically recommend to save state in long periods of inactivity as simple as possible. This will make it easier to transition to these new paradigms. Independent of the concrete product used, the simpler the state can be extracted, the simpler the migration will be.

By the way: I am still searching for a name for these very long processes. For now I have put them into a category called “entity life-cycle related business processes” —I don’t like this for several reasons so if you have a good idea, please let me know!

These very long running processes typically have a high level of activity only within shorter periods. So we can perfectly slice the overall process into pieces of activity (long running processes) and waiting (simple state). Typically the phases of activity do not extend **6 months**.

But up to this time frame, the requirements are identical for all durations, ranging from milliseconds to months. That also means, that you can apply the same solution for a big variety of problems.

### Implementing long running processes

![](/assets/images/what-are-long-running-processes-3.png)

There are various solutions to tackle long running processes. I want to dedicate a later blog post on these, including forces and consequences. As a teaser I highlight three options:

- Save state within your own **domain entities.**
- Do not save any central state but add information about it to all messages passed around ([pattern: **routing slip**](http://www.enterpriseintegrationpatterns.com/patterns/messaging/RoutingTable.html)).
- Use an **engine **([pattern: process manager](http://www.enterpriseintegrationpatterns.com/patterns/messaging/ProcessManager.html)), which might be a workflow engine, a process engine, an orchestration engine or even a homegrown custom engine.

Having co-founded an [Open Source Process Automation vendor](http://camunda.org/), I only want to note in this post that a proper workflow engine works for all use cases explained above :-)

### Summary
A long running process can **span from a few milliseconds up to decades**. So duration is less important than the fact that you *potentially *have to wait, which is the case in almost any situation **when humans or remote communication is involved**.

Let me know what you think!
