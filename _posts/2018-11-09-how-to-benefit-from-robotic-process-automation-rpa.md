---
layout: post
title: "How to benefit from robotic process automation (RPA)"
date: 2018-11-09 12:00:00 +0000
categories: [blog]
tags: ["camunda", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "!https://cdn-images-1.medium.com/max/800/0vP-4O8o9xKsqeB1_."
canonical: https://blog.bernd-ruecker.com/how-to-benefit-from-robotic-process-automation-rpa-9edc04430afa
---
### How to benefit from robotic process automation (RPA)

#### Applying RPA should not be an unattended short-term painkiller therapy but embedded in a proper strategy to modernize your IT landscape.

![](https://cdn-images-1.medium.com/max/800/0*vP-4O8o9xKsqeB1_.)

“A robot named Pepper holding an iPad” by [Alex Knight](https://unsplash.com/@agkdesign?utm_source=medium&utm_medium=referral) on [Unsplash](https://unsplash.com?utm_source=medium&utm_medium=referral)RPA (robotic process automation) is hype at the moment. The sweet spot** **of RPA is the **automated control of existing graphical user interfaces **(UI)** when the application does not offer an API**. This is a lot about screen scraping, image processing, OCR and robots steering graphical user interfaces.

The RPA tool market is very diverse. You have everything from vendors selling low-code solutions (not again! I comment on this later) to code-heavy approaches like “[Automate the Boring Stuff with Python](https://automatetheboringstuff.com/).” Also a lot of UI automation tools originally built to automate test cases qualify for RPA. Some of our partners report that these tools — that are often not even sold under the RPA umbrella— deliver the same value for much less costs.

All of these tools allow to integrate legacy applications in automation endeavors. And a lot of companies struggle with legacy because of their own history: Off-the-shelf software was bought that never provided proper APIs and custom solutions where build as silos in a three-tier-architecture where the UI was the primary or only interface. System integration was often done on a database level or with clumsy enterprise application integration (EAI) tools or batch jobs. This environment needs severe modernization, but RPA can serve as a short term painkiller along the way.

This article is two fold:

- I describe the very **valid use case of RPA **as a wrapper to applications without an API in more details in the second part. Feel free to scroll down to it if you want to directly dive into a meaningful way to get a lot of value out of RPA.
- But before looking at the positive side of RPA, I summarize** typical challenges** and especially highlight the **danger of abusing RPA** for the wrong reasons. This warning is very important given the alarming feedback I hear from customers. The main motivations to go for RPA are most often either the shortage of IT resources, which leads business departments to build things on their own (hoping to get along without IT) or the refusal to invest in a proper modernization strategy of the enterprise IT. While this might be understandable, it is very dangerous.

### Challenges when applying RPA
RPA is often introduced by business units themselves. And “*when a business unit configures RPA without involving IT, it risks crossing wires when it comes to IT architecture, infrastructure and security*” ([Overcoming the Challenges of Robotic Process Automation](http://www.isg-one.com/related-case-studies-detail/overcoming-the-challenges-of-robotic-process-automation)). There are multiple concrete examples.

RPA tools must to be **operated **by someone. Most solutions are installed on-premise, so you not only need your own servers but also to install the RPA tool and take care of patches and so on. Agents need to be installed or rolled out on end-user computers. This can seldom be done by business departments themselves. So IT jumps in and often finds a very alien tool to be integrated into their landscape.

**Security **might also be an issue. Which login is used to steer the UI? Some clerk or technical user? One problem is that the authorization schema of the legacy apps were typically not meant to be used by a robot and thus do not support the required granularity. Do you want your robot to have “god-user” access? You might even have to solve problems around auditing in order to know which robot did what.

Once in production you have to organize the **deployment of changes**. If your RPA flows are [long running](https://blog.bernd-ruecker.com/what-are-long-running-processes-b3ee769f0a27) in nature and need days or weeks to complete you even have to plan for **versioning **of flows and/or[ **migration **of running instances](https://docs.camunda.org/manual/7.7/webapps/cockpit/bpmn/process-instance-migration/).

But there are other more trivial problems to address first. You have to answer the question of **ownership**: Is the business department responsible for the RPA flow? Or IT? This is especially “*difficult if multiple applications are used in the process and any change in the front end UI, even though not impacting the processing procedures, will impact the RPA script hence impact the outcome*” ([What are the current problems/challenges in the area of Robotic Process Automation that people in the area are desperate to solve or explore?](https://www.quora.com/What-are-the-current-problems-challenges-in-the-area-of-Robotic-Process-Automation-that-people-in-the-area-are-desperate-to-solve-or-explore)).

![](https://cdn-images-1.medium.com/max/800/1*TD0V_ixKXWNddW-UZDKM9g.png)

Manifold different changes might lead to changes in a RPA flowWith RPA you rely on the **stability of user interface** of an application. But typically user interfaces change very often, just think of web based UIs which recently change almost monthly to satisfy new requirements on responsiveness or mobile friendliness. We even saw situations where the frontend itself rarely changed, but a different framework was used underneath leading to a complete rewrite of the RPA flow. This can also happen if the operating system changes. The timing of these changes might be completely out of your control. Backwards compatibility of user interfaces is nothing any software vendor commits to — in contrast to public APIs. So you have to prepare for increased and unplannable maintenance efforts**, **making every ROI calculation a complete guessing game.

I want to contrast this with the usage of a proper API. These are designed to be used in system integration so backwards compatibility and versioning is addressed. Breaking changes are typically not allowed. A lot of challenges around evolving APIs have been tackled so you can build upon real-life experience which is shared in huge IT communities and conferences around the globe. To give you two quick examples: The [tolerant reader pattern](https://martinfowler.com/bliki/TolerantReader.html) describes how to write clients for APIs that might not be touched even if the API evolves over time. And [consumer driven contracts](https://martinfowler.com/articles/consumerDrivenContracts.html) are a great way to automatically identify potential problems of changes in a service provider before they are even deployed.

So **using an API is always beneficial over steering a graphical user interface!**

Another stumbling block is that RPA is often marketed as **low-code**. Again! We already saw that happening with BPM solutions a couple of years ago. The promise is to automate core processes without involving developers or probably even without involving IT at all. Low-code fans** **don’t see this approach as a workaround for shortage of IT resources but as a strategy that you can do better without requiring IT. This never worked out and I am convinced it never will! A good background read on the why can be found [in Sandey Kemsleys’ white paper “Developer-Friendly BPM](https://camunda.com/learn/whitepapers/developer-friendly-bpm/)”. I see a more promising scenario in having interdisciplinary teams that acknowledge that you need both professions to be successful.

There might be small and isolated use cases besides the core processes of a company where it makes sense for business units to apply RPA on their own. This is basically the same as the myriads of MS Excel or MS Access files lying around in business departments or island solutions build on tools like [IFTTT](https://ifttt.com/). RPA is comparable, just more expensive. This might be OK — as long it is an isolated use case and it is consciously decided for RPA. But keep the maintenance costs in mind. A good exercise is to ask random people at your company if they have run things built on MS Office — and how they like it.

**Summary**: Applying RPA is [**technical debt**](https://en.wikipedia.org/wiki/Technical_debt)**. **You not only have to repay it some time in future, you also constantly pay interest (effort). This makes it harder to change or adjust procedures and that’s why “*bots might make innovation more difficult — and slower*” ([Five Robotic Process Automation Risks To Avoid](http://www.oliverwyman.com/our-expertise/insights/2017/oct/five-robotic-process-automation-risks-to-avoid.html)). So in the worst case RPA brings you even further away from a modern and agile IT which is the foundation of a successful future of your company!

### The danger of abusing painkillers (like RPA)
Even if you are aware of these challenges you still might justify an RPA project. What I hear often is:

> “Our IT has too much work on the table and is not available for this project. But we have to do something now…” or “The priority of this project is not high enough to get IT budget, but we have to solve this instant pain…”

These managers are aware that RPA is not the best solution, but it is the only one their business department can do on its own. And local RPA projects might quickly automate work of one or two employees which allows to present a good ROI and projects go ahead. While this behavior is understandable from a department view, it can get your company in trouble in the long run.

**RPA is a short term painkiller — not a long term strategy!**

As everybody knows from daily life: Using painkillers in the presence of pain might ease the current situation, but it is not a strategy to solve the underlying root cause. If you cannot avoid the painkiller therapy, make sure that it goes at least hand-in-hand with working on a proper long term strategy!

There are two major risks with the RPA painkiller.

**RPA can lead to a downward spiral in maintenance costs**Shortage of IT resources is often caused by too much time spent on maintenance of existing applications. This is typically known as “keeping the lights on” which stops you from innovation and new projects (see e.g. [How to balance maintenance and IT innovation](https://www.computerworld.com/article/2486278/it-management/how-to-balance-maintenance-and-it-innovation.html)); some companies spend 80–90% of the IT budget on maintenance.

As RPA solutions cause more effort in maintenance they lead to even more busy IT departments. This causes trouble for the next project, which might go for RPA then, which causes more effort in maintenance and so on and so on.

**You can only attract IT talent if you have a strong technical vision**Another problem is about your company culture and mindset. With the current wave of digital disruption it gets very clear that **most companies become essentially IT companies** in its heart as they work with information only rather than tangible goods. Take the [example of JPMorgan and Goldman Sachs,](https://thefinanser.com/2018/04/banks-technology-companies.html/) they are *transforming from “financial institutions that deploy tech” to “tech firms that distribute finance”* [(Theodora Lau](https://twitter.com/psb_dc/status/982641655705690117)).

> 

In this environment it is essential for almost every company on the planet to modernize their IT architecture and build up a great engineering force. But engineers can work where they want to work, there is [war for talent](https://en.wikipedia.org/wiki/War_for_talent)! Where do you think a top-notch programmer wants to work: At places that believe in IT as their heart and backbone — or at companies that try to avoid true engineering and going for RPA and low code?

**Only companies that embrace IT will be ahead of the market and survive in the long run. Using the RPA painkiller might get in your way here.**

This is true not only for big companies. I know small companies where single teams radically drive proper automation programs throughout the whole enterprise. These companies show a strong vision and talk about that publicly — and are able to attract IT talents even if they are very small, very remote located or working in a rather conservative industry like insurance.

**But I cannot influence our strategy, what to do?**A lot of projects might not have a choice as they are trapped by top managers who live in the past. In this case my advice is: be aware of the downsides of RPA; apply it with care and keep it local and replace it as soon as possible. Or you try to raise budget for an RPA solution but secure the commitment to decide on all the details yourself. Then you can take the money and hire external IT know-how and do a proper automation instead. That requires courage — but we have seen this lead to great results.

### Apply RPA properly and think about separation of concerns
But let’s start looking at the positive side of things and turn out attention to the sweet spot of RPA. The clue to successful RPA is to involve IT early and to decompose the solution into proper pieces where RPA plays one role among others: **RPA is there to talk to UI’s, but not to automate the business processes itself. **This is simply about proper decomposition, otherwise you will end up with a big spaghetti that mixes UI specific stuff with workflow logic.

Can you spot the business process in the following RPA flow?

![](https://cdn-images-1.medium.com/max/2560/1*RE7KQprY2VoHNtMlKgk8yQ.png)

Compare this to the following flow that concentrates on the business process only — and keep in mind that we only look at the happy path so far:

![](https://cdn-images-1.medium.com/max/800/1*hJ9iu0XOlmv2VEVjugXI7Q.png)

This workflow can invoke RPA flows whenever it integrates an application without API:

![](https://cdn-images-1.medium.com/max/800/1*prmGNLCToLeTQ1w7kh0FeQ.png)

Despite the improved visibility this has huge advantages to mitigate the challenges mentioned at the beginning.

For example the underlying business process typically changes independently of the user interfaces of involved applications. This works both ways: If you roll out a new release of some core application or simply the operating system, details in the UI might change and you need to adjust your RPA flows, but the business process keeps stable. Or you want to optimize your business process, but the integration of one application might not be touched at all.

![](https://cdn-images-1.medium.com/max/800/1*uCDBg6KgnbzO4is_5xm2zA.png)

Don’t build a big ball of mud as it requires a lot of changes on a big scopeBy separating business process logic and RPA flows you can avoid what is often called the [big ball of mud](https://en.wikipedia.org/wiki/Big_ball_of_mud). This approach keeps the business process visible and different aspects changeable independently at their respective pace.

It also allows to evolve a business process in terms of its automation ratio. So you might start introducing a workflow engine, but even start without RPA and just do plain human task management:

![](https://cdn-images-1.medium.com/max/800/1*C8BGh7iEcSmJiUwO6RGMWg.png)

Now you introduce RPA, but you might want to keep **human task management as a fallback** in case there are errors within the RPA flows. This allows you to concentrate on automating the 80% cases and route the exceptions to a human:

![](https://cdn-images-1.medium.com/max/800/1*kRRTJcnnlPhvG2-5W85wGg.png)

Whenever possible you can replace RPA with a proper integration of a real API:

![](https://cdn-images-1.medium.com/max/800/1*RNdUEial5Mh_D-80Wh4EAw.png)

You can decide this on a case by case basis. The CRM might be a stone-age tool that needs to be integrated by RPA, but you can already leverage a real API from your relatively modern ERP system.

### Combining RPA and workflow automation
In order to separate these concerns you need a workflow engine or workflow automation platform in addition to your RPA tool. Workflow tools can directly execute models as the ones shown above (in the [ISO standard BPMN](http://www.bpmn.org/)). You find working examples using the open source platform [Camunda](https://camunda.com/) (disclaimer: the company I co-founded) in [Robotic Process Automation (RPA) and Camunda BPM: A demo on using UiPath robots within a BPMN workflow](https://blog.camunda.com/post/2018/08/integrating-uipath-rpa-with-camunda/) and [RPA and Camunda BPM: Integrate a Software Robot by Using External Tasks](https://blog.camunda.com/post/2018/08/workfusion-rpa-with-external-tasks/) —the blog posts showing different tools and strategies for integration.

In the above sketched scenarios the workflow engine is in the lead and calls RPA whenever it needs to integrate with a resource that does not provide a proper API. The workflow engine therefor calls the API of the RPA tool, e.g. via REST. This requires that your RPA tool provides an API. Most of them do and you should definitely make this a must criteria on your RPA evaluation list!

![](https://cdn-images-1.medium.com/max/800/1*swf3B3ylczageMXXLRcJPQ.png)

One basic distinction when looking at the integration is that you can have attended or unattended robots. Unattended robots are running fully on their own — probably on a machine without a display connected. The processing can be easily started by an API call. For attended robots a task, job or work item is created, scheduled and the human at his computer can later pick it up and execute it in an attended manner. The details depend on the tools at hand, but technology wise the integration is typically very easy to do and I am happy to discuss concrete scenarios with you.

Some partners report, that they also concentrate on proper domain-driven APIs for their robots. So from the workflow perspective you cannot differentiate if you call some real service API or a robot — as both could offer the same `createSalesOpportunity` method.

A quick word of warning about marketing brochures that combine workflow automation and RPA in one tool, most often because of the low-code idea. I actually do not see an advantage of a workflow automation platform that is capable of RPA in its core. The problems are very different and I am in favor of a [best of breed](https://www.gartner.com/it-glossary/best-of-breed) architecture. This allows one to use a proper workflow automation platform, probably even on a broader scale in the company, and RPA whenever it make sense, as an addition in order to integrate software without a proper API — or as an island solution for local problems.

### Summary
To be successful you have to acknowledge RPA as an interim technology. Prefer to integrate via proper API’s whenever possible. Make sure that using the short-term painkiller RPA does not stop you from working on a proper strategy that involves a commitment to the importance of IT in the company. You should embrace that having more nerds is better than low-code!

In the right setup RPA can be a great addition to your toolbox. Its use case is to integrate legacy applications that do not provide an API. Combine RPA tools with a workflow engine for automating the underlying business process. This is a very powerful combination and allows a best-of-breed architecture with great advantages:

- Proven methodology, best practices and tools around workflow automation.
- Unified approach for automating the business process that can include API or RPA integration as well as Human Task Management. This allows business processes to evolve.
- RPA allows to integrate tools without proper API and avoids dead-ends in integrating legacy systems.

If you limit the usage of RPA to this sweet spot it will be easy to follow IT best practices. This helps to address a lot of the challenges around deployment, governance and flexibility.

*Thanks to *[*Jan Stamer*](https://medium.com/@remast)* and *[*Benedikt Uckat*](http://www.linkedin.com/in/benedikt-uckat)* for extensive feedback that helped to improve this article.*
