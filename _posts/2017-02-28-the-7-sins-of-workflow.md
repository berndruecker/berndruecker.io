---
layout: post
title: "The 7 sins of workflow"
date: 2017-02-28 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "!/assets/images/the-7-sins-of-workflow-1.png"
---
### The 7 sins of workflow

![](/assets/images/the-7-sins-of-workflow-1.png)

Over the past 15 years while doing hundreds of workflow projects I found the following 7 sins as typical glitches in workflow related projects.

So let’s go over these sins one by one.

Side note: When I say workflow, I refer to (potentially long running) business processes, controlled by a workflow engine. This typically involves the use cases *Human Task Management* as well as *Service Orchestration/Invocation*.

### 1. No engine

![](/assets/images/the-7-sins-of-workflow-2.jpeg)

There are still many projects not using a workflow engine at all — I see two reasons for this:

- Projects are not considering an engine out of ignorance.
- Projects are rejecting an engine for questionable reasons like “We only have simple requirements, a workflow engine is overkill” or “There is no product fitting for our very special requirements.” I already [blogged about this phenomena in 2009](http://www.bpm-guide.de/2009/06/22/workflow-engine-die-bauen-wir-selbst/) (sorry: only in German at that time) — unfortunately it is still out there.

The thing is: Engines got lightweight and flexible. You can easily get a powerful workflow engine Open Source and run it as a library in any environment. Learning curves also got easy to conquer. The investment in running a workflow engine isn’t very big. Just make sure to pick the right engine.

The gains are definitely worth it in the whole DevOps life cycle. My favorite example is the anti-pattern I named *alarming by management*, which gave this sin the illustration. In one of my first customer assignments we experienced a *head-of-party*: All line-managers of the order management team up to the CTO met in our office because sales figures went down to zero over the weekend. This was caused by a bug in a service we called from our order process. But we didn’t recognize this, as we didn’t have any technology in place to monitor SLAs or KPIs. Something you get for free with workflow technology!

### 2. Zero-code suites

![](/assets/images/the-7-sins-of-workflow-3.png)

I want to quote the analyst Sandy Kemsley’s [report written in 2014](https://network.camunda.org/whitepaper/1) — as it summarizes this sin quite nicely:

> “Many BPM experts today will tell you that the key to business agility is business-led, zero-code, model-driven development. In other words, a non-technical analyst creates a graphical process model that generates an executable process application without writing code. In fact, for the past 15 years, vendors have attempted to make business process management systems (BPMS) more user-friendly and analyst-friendly through the use of model-driven design and development. These zero-code paradigms allow non-technical participants to create their own executable process models, typically using the Business Process Model & Notation (BPMN) standard.

> **Beyond simple, non-integrated processes, however, the reality is that most BPMS projects involve technical developers as well as non-technical analysts**. Unfortunately, the proprietary model-driven environments that provide a simple interface to analysts can provide barriers to developer efficiency and code transparency. [This paper](https://network.camunda.org/whitepaper/1) explores the myth of zero-code BPM in complex projects, and how proprietary design and development tools can hinder — rather than help — skilled enterprise developers. It considers how using standard development tools and coding languages in conjunction with a BPMS can provide a bridge between business and IT without forcing either to use tools that are inappropriate for their needs: business-led functional design paired with efficient development in corporate-standard application development environments”

In my words: If you want to automate your core-processes you have individual requirements — otherwise, buy standard software! These requirements have to be implemented. Zero-code platforms force you to use their proprietary way of coding. It may be graphical and wizard based, but it is coding. It is typically too complicated for non-technical people. And of course the zero-code suite will only let you write code the way they want you to write code. Very often that is their own weird scripting language in tiny property panels which leads to the anti-pattern *death-by-properties-panel. *You have to do back-flips to allow for the authoring of “normal code”, e.g. in Java.

### 3. Homegrown engine

![](/assets/images/the-7-sins-of-workflow-4.png)

Throughout my career I saw hundreds, if not thousands of home-grown engines. It literally always happens when you start with *no-engine. *After a while, projects start to recognize they are missing out on something. They introduce a first small status flag in an order table. But hey, you also need a timeout if customer payments are not received in time. Let’s quickly implement this. After a while projects introduce a small XML or JSON dialect to make processes more flexible. Congratulations, you have built your own engine!

All companies I know want to get rid of this engine. It is a nightmare to maintain. It is not rare to have 1–4 people working full time on this. And it will always lack behind professional engines in terms of features and maturity, especially in the fast-changing IT world.

*EDIT**: Because I discuss it so often I just added a list of typical subsequent requirements when implementing an engine:*

- **Monitoring & operations**: How can you keep an overview of all running instances or failures and provide options to intervene?
- **Visibility & domain language**: How can you make status and models visible to all stakeholders including domain experts? How can these models be easily changed? Do you develop your own language probably including a graphical representation?
- **Versioning**: Workflow instances are typically long running so you always have running instances when changing a workflow model. How to deal with this?
- **Performance & scalability**: What happens if the engine should not only run hundreds but millions of instances a day?
- **Time & Timeouts**: Whenever you have to keep track of time you need to introduce a scheduler. This is an active component which is not only complex terrain on itself but also influences scalability, testability and resource management.

### 4. BPM monolith

![](/assets/images/the-7-sins-of-workflow-5.png)

Whenever I mention the BPM monolith, there are three associations people typically come up with:

- Huge BPM suites as a monolith on their own,
- Waterfall projects with big bang go-lives,
- Central BPM systems claiming to execute all business processes centrally, which violates responsibilities of parts of your system (e.g. [bounded contexts](https://martinfowler.com/bliki/BoundedContext.html)).

All are correct!

**Huge BPM suites** do indeed feel like a monolith in itself. They are assembled of various components, often acquired by the BPM vendor. Due to the zero-code nature, the tools must provide countless features to fulfill certain requirements — as you cannot simply code your way to the required solution. These tools try to push a complete development stack into the customers’ environment, including own versioning, deployment or testing tools. I’ll tell you what: Software engineering already *has *very good answers to these questions. Customers already *have *tools in place, for example for user management. Hence a best-of-breed approach is much more likely to be successful — but not many BPM vendors embrace this flexibility. You might get the feeling the vendors try to maximize vendor-lock-in — not customer success.

**Big bang** go-lives seem to be very popular in the BPM world. This is about business analysts documenting each and every business process to “not miss anything”, about drawing beautiful “process landscapes” and about IT departments putting a lot of effort into “service maps”, which categorize all services to be reused later in some kind of SOA. A lot of companies still do this before even starting the first automation project. This is a big mistake. Pick a process and get going! In our Best Practices we recommend the following:

> “Use a relevant process where you can show benefits of BPM including a return on invest (ROI) calculation. But avoid too big or too political processes to minimize the risk of failure due to avoidable reasons.”

**Do small projects end-to-end instead**: Setup the architecture, setup your technical stack, model the processes, automate the processes, add user forms, add a tasklist, invoke services; and get it live! You will learn so much while doing so, which you can already apply when doing the next project. Skip whatever is not really necessary in the first step — you can often sacrifice nice-to-have features for speed. Keep in mind that there are disruptive competitors around the corner for basically every industry today, and they follow the lean-startup approach building “[minimal viable products](https://en.wikipedia.org/wiki/Minimum_viable_product)” and pivot very fast.

The **central BPM service** was often explicitly targeted in the “old days” doing BPM+SOA. A central BPM service executes the overall business processes which orchestrate other services. However, this view is outdated as microservices and the “domain driven design” discipline get adopted. These methodologies respect the [bounded context](https://martinfowler.com/bliki/BoundedContext.html), in short: implement business logic in the component responsible for it — and keep every component as independent as possible. Different services might implement small portions of an overall process and the end-to-end process results from the collaboration of these services. This is a very powerful approach with its own challenges though, which I will dedicate individual articles to in the coming weeks.

*EDIT**: I recently dedicated an own blog post to the topic of *[*avoiding the BPM monolith when using bounded contexts*](https://blog.bernd-ruecker.com/avoiding-the-bpm-monolith-when-using-bounded-contexts-d86be6308d8)*.*

### 5. Granularity bloopers

![](/assets/images/the-7-sins-of-workflow-6.png)

There is so much that you can do wrong in terms of granularity. To scratch the surface, I start with some examples from real-life (sorry if you recognize your own work).

![](/assets/images/the-7-sins-of-workflow-7.png)

These models show much too much details graphically. Some start with activities like *init variables* or *load configuration*. These are not business motivated aspects! And now you cannot recognize the real business process any more. What happened is pure *graphical programming* — where I see little value. The sweet spot is to have *business driven* aspects of the process in the graphical model — and everything else in code. As a rule of thumb all elements in the graphical model should be understandable by domain experts! Needless to say that of course you should also *use *the language of the domain experts then.

By the way: The reason this is happening so often is that some modeling languages and most zero-code tools force engineers to model each and everything, as programming is not “en vogue” (remember the 2nd sin).

Just to come full circle: I also saw it the other way around where important business relevant aspects were buried in code. This is also not a good idea.

### 6. Violate stakeholders’ habitats

![](/assets/images/the-7-sins-of-workflow-8.png)

There are two misconceptions out there, I call them *IT follows business* and *IT ignores business*.

Business stakeholders often still think they are in total command and can just throw some requirements over the fence for IT to implement. This did not work in the last decade — and will definitely not work in future. As digitization teaches us: almost all areas of business models are influenced by technology. You cannot lock out IT if you want to be successful! Tech startups will not do that. They will work together closely with IT to understand opportunities and limitations of technology and to align that with their domain and business understanding. Working **together**, that is the key!

However, I also observed the other extreme, especially within tech startups: Ignoring business stakeholders. When looking at workflow engines of hip tech companies you will see low-level JSON files and no business friendly visualizations of the process. Established standards like BPMN are ditched to have a “cool” product. This leaves big opportunities on the table!

So what should you do? Use BPMN models to speak with your business stakeholders, make exactly these models executable and get the granularity right. Therefor also move aspects in code, which don’t belong in the graphical model. Speak with each other, and respect each other’s roles. This is no rocket science!

### 7. Over Engineering

![](/assets/images/the-7-sins-of-workflow-9.png)

Everybody knows over engineering, so let me just put that into the context of workflows.

First, there seems to be a reflex to build an *in-house BPM platform* on top of the workflow engine purchased from a vendor. Goals are abstraction of the engine to gain vendor independence, enforcement of central rules and the re-use of commonly required functionality. In reality none of these initiatives really work out. BPM and workflow are often so diverse, that a central platform cannot easily help different projects, resulting in refusal to use it within the company. It costs effort and slows down adoption of new software releases of the workflow product. And total vendor independence is an illusion anyway (have you tried with SQL databases once?).

Secondly, the internal platform often gets very complex, assembled out of multiple components (e.g. a workflow engine, a reporting engine, a decision or business rule engine, a search engine, a document management system, a portal, a service bus and maybe even some ERP). “Hey — you only need to write some glue code!” What might work on a project-level to define a best-of-breed architecture will not result in an integrated product!

So you better start small, work in iterations, be agile and of course build as little as possible to achieve what you need. Remember: Your disruptive competitor will do so.

### Conclusion
I collected hundreds of stories backing these 7 sins of workflow in the last years while working for [Camunda](http://camunda.com/), the company I co-founded. It was those stories that motivated us to create our [own BPM platform](http://camunda.org) in the first place. As a founder I am of course happy if you want to use our technology but as a BPM enthusiast and developer my primary goal was to share these stories to help you avoid these sins. Workflow can be really cool and deliver great value to your users — if applied correctly!

As always, I love getting your feedback. Comment below or [send me an email](mailto:mail@bernd-ruecker.com).

*Stay up to date with my *[*newsletter*](http://eepurl.com/cDXPRj)*.*
