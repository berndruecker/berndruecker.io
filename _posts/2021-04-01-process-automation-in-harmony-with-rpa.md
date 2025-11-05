---
layout: post
title: "Process Automation In Harmony With RPA"
date: 2021-04-01 12:00:00 +0000
categories: [blog]
tags: ["camunda", "orchestration", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "- Share Deutsche Telekom’s story about their initial success with RPA and the challenges emerging from that success
- Describe their key solution strategies,..."
---
### Process Automation In Harmony With RPA

#### The RPA journey from Deutsche Telekom — a great story to learn from
[At Camunda Con Live 2020](https://www.camundacon.com/day1_key_02), Deutsche Telekom talked about their journey around process automation and robotic process automation (RPA). I found their story so impressive that I feel the urge to write about it. So in this blog post I’ll:

- Share Deutsche Telekom’s story about their initial success with RPA and the challenges emerging from that success
- Describe their key solution strategies, such as separating the orchestration from the bot layer (in my words: “separating process from task automation”) and prioritizing back-end instead of front-end integration (in my words: “prefer APIs instead of bots”).

![](https://cdn-images-1.medium.com/max/800/0*MPGiy7gU6YOqZSQp)

The slide above shows a summary of the journey at Deutsche Telekom:

- They started with RPA to automate processes without core IT involvement. This allowed them to save costs quickly, but they also faced severe challenges quickly (They call this the “shaky shacks” phase)
- They added an orchestration layer (“solid foundation”)
- They want to move from RPA bots to API calls wherever possible (“transformation”)

Let’s dive into that journey in more detail.

### Initial Success With RPA
In 2015, when a field technician out at the customer’s property needed to diagnose a line, they had to call an inbound colleague via phone. Typically they needed to wait three to five minutes until somebody was available. The technician gave the phone number of the fixed-line to be checked via the phone and the inbound colleague used a legacy tool to diagnose the line. Overall, this was a frustrating experience for the technician (annoying, slow and error-prone) and an expensive process for Deutsche Telekom to operate (labor-intensive).

Some former initiatives to automate this process failed because of — well — reasons. Kind of the typical problems all big organizations have: Too many legacy systems that are very inflexible and have no proper API, or at least no documentation of it. There is no clarity on how an architecture should be set up to implement this and the bandwidth in core IT departments is very limited, so such a project might not get enough priority to be started.

This is why Deutsche Telekom applied robotic process automation (RPA) to this problem. With RPA you implement a bot that controls an existing user interface. In that sense, it can do exactly what a human could do with existing user interfaces. You do not necessarily need to involve core IT or professional developers to implement the RPA flow, as long as you clarify the question of who is operating the RPA solution.

![](https://cdn-images-1.medium.com/max/800/0*k_0FIFa8u8NgYAD4)

In a four-month project, they developed an app plus a bot, which automated the line diagnosis. Now, technicians can simply enter the fixed-line number in the app and get a result a couple of seconds later.

This project was a huge success. Not only did it drastically improve the user experience of the technician, it further lowered the operational spend and removed possible sources of error.

### The Success Catastrophe And Further Challenges
This is exactly why RPA is so successful. Such success stories lead companies to adopt and apply RPA to more and more use cases. This can easily lead to what some enterprise architect once called the “success catastrophe”: too many projects start in a relatively uncontrolled manner, lessons learned cannot be applied to new projects and uncontrolled growth makes governance close to impossible.

In the case of Deutsche Telekom, the success was significant. Impressive savings and improved processes lead to higher customer and employee satisfaction. But they also had challenges on RPA governance, operations and maintenance and they ended up with seven different RPA platforms being used. Just as an example, a change in the main CRM system led to change requests in four different RPA platforms and a big number of robots.

Furthermore, the complexity of the automated processes increased. In an extreme example they had bots with more than 220 steps, implementing large decision trees by if-then-else constructs in the bot. Business process logic got strongly mixed with RPA concepts or user interface details. The real business process logic became hard to understand, hard to monitor, and hard to maintain.

### Reinventing The RPA Approach

![](https://cdn-images-1.medium.com/max/800/0*j4ezppM2e3uQGTL4)

Deutsche Telekom approached these challenges head-on and embarked on a new strategy with three main goals:

- Separate the process layer from the bot layer. Or in my words: “Separate business process logic from the technical implementation details of single tasks, like RPA flows.”
- New governance and platform consolidation: They clearly wanted to reduce the number of technologies being used.
- From front-end to back-end automation: They wanted to replace RPA bots (front-end integration) with API calls (back-end automation).

Let’s briefly look at two key elements of this strategy that are most interesting from an architecture perspective.

### Separate The Orchestration Layer
Deutsche Telekom built a new platform (called OREO) leveraging Camunda as the workflow engine. End-to-end processes run on Camunda, described by executable BPMN process models, and orchestrate the RPA bots, but also other tasks, such as user tasks or service invocations.

This is exactly what I think is the best way to use RPA. RPA bots automate one task in a process, not a process itself. RPA is *task automation*, not *process automation*. It should actually be better called “robotic task automation”*.*

Process automation, in contrast, will be implemented by the workflow engine (or orchestration engine if you prefer). This engine can orchestrate various types of tasks, e.g. RPA bots, but also human tasks, services, decisions, and more. [In this blog post](https://blog.bernd-ruecker.com/how-to-benefit-from-robotic-process-automation-rpa-9edc04430afa) you can find some more information on this.

### From Front-end to Back-end Automation

![](https://cdn-images-1.medium.com/max/800/0*usIJQBGeXtAUKsSi)

But as bots are still more brittle than API calls, replacing a bot with a real API makes process automation more stable and efficient. Deutsche Telekom prefers API calls (“back-end automation”) over RPA bots (“front-end automation”) whenever possible, and also started some migration projects for existing bots.

### The Journey
In summary, the journey of Deutsche Telekom with regards to their process maturity passed through the following stages:

- Initially, processes often evolved out of manual work people are doing to solve an immediate business requirement, e.g. by exchanging emails or phone calls and using some existing legacy systems (like in the field technician example).
- Quick help was realized by RPA-driven automation that mimicked this design but automated part of it. This lowered manual effort, but came with the problems described above. For Deutsche Telekom that happened between 2015 and 2019.
- Since 2019 they separate the process layer to increase the visibility of the process, allowing stakeholders to understand and optimize it.
- Since end of 2020 they not only prefer API calls over bots, but also actively replace bots to make process automation more stable and efficient.

### A Surprising Finding
There was one learning that really surprised me at first: Using RPA leads to a better relationship between business and IT at Deutsche Telekom. How come? I typically have the impression that business tries to bypass IT whenever possible (and RPA is used to facilitate this) and on the other side, IT hates RPA by heart because of its brittleness.

But the trick is relatively simple: Various roles talk with each other much more often now. Whenever a business department wants to introduce RPA, they talk to IT first, discussing why RPA makes sense, if there is a good alternative possible, and what the future migration strategy to back-end automation will be. And IT sometimes even proposes introducing a bot, basically to buy some time to develop a real back-end-integrated solution.

### Very Impressive Numbers
All of this seems to work out great so far. Deutsche Telekom has moved further than most European businesses in developing an army of more than **2,500 RPA bots** to automatically handle and improve manual processes, handling more than **40 million successful bot transactions** and each transaction is on average **saving more than 2 Euros each**. So the realized savings are substantial and the process quality improved resulting in higher customer and employee satisfaction. In total they already **automated almost 500 processes**. Currently, every euro invested in the process automation space yields three euros in savings according to their calculations. Almost **10% of the total workload in customer service is now automated**.

### Conclusion
The strategy sketched here is an interesting role model:

- Use BPMN to automate processes
- Use RPA to automate tasks within the process
- These processes can not only orchestrate bots, but also humans or other systems
- Using a bot might be a valid temporary solution that requires a strategy to replace the bot (“front-end integration”) with an API call (“back-end integration”).

This way, you can not only achieve a great architecture that will allow you to understand and manage your processes, you can still move fast using some band-aids without piling up more and more technical debt over time.

Further information:

- [Camunda Con talk from Deutsche Telekom](https://www.camundacon.com/day1_key_02)
- Press release: [March 17, 2020 Digital Transformation: Deutsche Telekom Counts on Camunda for Process Automation and RPA Orchestration](https://camunda.com/press_release/digital-transformation-deutsche-telekom-counts-on-camunda-for-process-automation-and-rpa-orchestration/)
- Whitepaper: [Beyond RPA](https://page.camunda.com/wp-beyond-rpa)
- Blog post: [Automation Reinvented](https://camunda.com/blog/2020/10/automation-reinvented/)
