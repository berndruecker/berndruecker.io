---
layout: post
title: "How Camunda 8 Is Driving Better Team Structures"
categories: [Business Insights, Getting Started]
image: assets/images/team-topologies.png
canonical: https://camunda.com/blog/2025/12/how-camunda-8-driving-better-team-structures/
---

The transition from Camunda 7 to Camunda 8 has sparked discussions about team structures, ownership, and how much centralization is actually needed (e.g. [Camunda 7 vs. 8 — A Look Through the Lens of Team Topologies](https://medium.com/miragon/camunda-7-vs-8-a-look-through-the-lens-of-team-topologies-4b253d0ba6b3)). Sometimes this gets interpreted as Camunda 8 simply adding more complexity—especially for development teams starting their first projects, who can no longer just embed a library into their application but need to run a separate component alongside it.

But there's an important perspective that often gets overlooked: the hidden complexity of running a process orchestration engine "on the side," and the very real benefits of having a platform team. And this is true regardless of which Camunda version you use.

Together with my colleague Leon Strauch, I published a book with Wiley earlier this year that dives deep into exactly this topic: [Enterprise Process Orchestration](https://www.amazon.com/Enterprise-Process-Orchestration-Hands-Technology/dp/1394309678/). It gives you the broader context for many of the design decisions behind Camunda 8—and why these changes are not just reasonable, but a real opportunity for your organization.

That's the angle I want to explore in today's blog post.

## The basics: Team topologies

To understand this, let's take one step back and get everybody on the same page. To create successful solutions in your organization, you need to start by looking at who is building and running those solutions. What teams should you have in place? How do they interact? How does that fit into your organizational topology? And how does this align with important philosophies around agile, DevOps, and product thinking?

I am a big fan of the book [Team Topologies](https://teamtopologies.com/book) by Matthew Skelton and Manuel Pais, who give great answers to those hard questions. In essence, they make a good case for [separating solution building, enablement, and platforms](https://teamtopologies.com/key-concepts).

![Team Topologies as defined by the book by Matthew Skelton and Manuel Pais](https://camunda.com/wp-content/uploads/2025/12/team-topologies.png)

In that model, productive stream-aligned teams, which are autonomous delivery teams maintaining some value stream, require enabling teams as well as platform teams to make their lives easier. Those supporting teams remove the burden of clarifying all the hard questions around what is called the "undifferentiated heavy lifting": how to set up the infrastructure (including dev and prod environments as well as a CI/CD pipeline), what tech stack to use, how to hook it into the organization's authentication mechanisms, etc. This reduces the cognitive load of the stream-aligned teams, freeing their brains to concentrate on the business problems and leading to business value being delivered much faster.

In [Enterprise Process Orchestration](https://www.amazon.com/Enterprise-Process-Orchestration-Hands-Technology/dp/1394309678/) we advise organizations to set up a dedicated team that not only provides the process orchestration platform, but also the enablement around it—this is called the adoption acceleration team, or AAT, that is discussed in detail in the book. This setup allows the stream-aligned delivery teams to work productively and remain concentrated on delivering business value—instead of sorting out tasks around the process orchestration platform.

## The myth: Stream-aligned simplicity

A common narrative suggests that Camunda 7 allowed stream-aligned teams to run independent process automation initiatives with minimal interference, while Camunda 8 necessitates a centralized platform team due to its complexity. However, this overlooks a key reality: operating Camunda 7 in a fully decentralized manner was never truly seamless. 

Take the example of embedding the orchestration engine as a library. While this sounds easy at a first glance ("just add a Maven dependency") it often results in dependency conflicts with your own application (did somebody just say "Jackson"—a JSON serialization library that is a very common source of version mismatches) and requires some understanding of Spring-based transaction management.

And without any central governance or a structured approach, teams often take shortcuts that seem productive in the short term but result in long-term challenges—technical debt, inconsistent implementations, and increased maintenance costs.

So scaling the usage of Camunda 7—organizationally and technically, across multiple projects—was where many teams hit the wall (if not earlier). I've seen countless organizations struggle with this, to the point where they not only created platform teams but ended up building their own orchestration platforms on top of Camunda 7. Sustainable scale required much more structure than most teams anticipated.

## Why a platform team was always a good idea

A platform team was actually always a good idea—long before Camunda 8 showed up. A dedicated team keeps process orchestration from becoming a fragmented side activity or a bottleneck. When you centralize this capability, you gain strategic leverage: Camunda becomes part of your enterprise architecture, not just another tool hidden inside a project. Stream-aligned teams can stay focused on delivering business value instead of wrestling with infrastructure questions, dependency conflicts, or tuning the engine. And with shared templates, best practices, and guardrails, you get accelerators that dramatically reduce time-to-value.

And [the most successful organizations built adoption-acceleration teams or full Centers of Excellence to scale effectively](https://camunda.com/resources/process-automation-coe-handbook/). What you shouldn't do—and this is important—is interpret "platform team" as "go build your own scalable orchestration platform." Yet that's exactly what many teams ended up doing with Camunda 7, simply because scaling across dozens of projects exposed gaps they tried to fill themselves. This is what we solved with Camunda 8.

And from my experience, the challenge wasn't only technical. Many Camunda 7 projects operated comfortably "under the C-level radar." That can feel convenient at first—fewer cooks in the kitchen—but it also meant that process orchestration rarely received real strategic attention. Teams built great things, but initiatives were often cut because nobody at the top understood the long-term impact. Yes, some Camunda 7 projects acted as lighthouses, but if we're honest, this too rarely translated into sustained, organization-wide momentum. When you bring the strategic story to the right people early, you create a completely different foundation for scaling process orchestration. And this isn't about golf-course selling—it's about helping leadership recognize the genuine strategic potential and wanting the transformation to succeed from the top.

## The benefit of a forcing function

Now the big difference with Camunda 8 is not that you can run it centrally within a platform team. You could already do this with Camunda 7. No, the big difference is that you cannot embed the platform as a library any more.

There are really good reasons for this decision (see also [Moving from Embedded to Remote Workflow Engines](https://camunda.com/blog/2022/02/moving-from-embedded-to-remote-workflow-engines/)) but it also has a great side effect: it is a forcing function for a good organizational setup. Since Camunda 8 must run as a separate component, stream-aligned teams may find this initially burdensome. At the same time, this makes things easier for platform teams.

## Easing Camunda 8 installations

That said, there is a valid concern that setting up Camunda 8 can get complex. This is mostly because we built a highly scalable and resilient platform, that can run millions of processes per second, in geo-redundant setups with low latency and high availability requirements. We scale horizontally, so there is literally no limit in what you can use Camunda for.

Now, many projects don't need that sophistication but automate rather simple processes. We listened to the community and now also allow a massively simplified installation, using a single Java binary (JAR). You can read more about it in [Simplified Deployment Options and Accelerated Getting Started Experience](https://camunda.com/blog/2024/04/simplified-deployment-options-accelerated-getting-started-experience/). We're also [working on supporting relational databases as an alternative storage to ElasticSearch](https://roadmap.camunda.com/c/234-relational-database-support-for-secondary-data-store). As a result, you will be able to run Camunda 8 in a very simple setup: As a single Java application that just requires a normal database connection, e.g. to Postgres or Oracle. You will not get the full scalability or resiliency in this setup—but it allows you a clear choice on how to tune the installation complexity to your project's requirements.

## Decentralized first, centralize later: A pragmatic approach

One concern we now face in some organizations is that they are not yet ready for centralization. They are used to a completely decentralized approach with the solution teams also running Camunda. Requiring them to set up a central AAT might stall further developments at their organization.

So how do we continue to make progress for teams eager to move forward?

A potential middle ground is to enable decentralized teams to use Camunda 8 with a simplified setup as sketched above. This is typically not much more than running two Java applications instead of one. This ensures continuity while you continue working on your strategy for a centralized AAT.

This hybrid model acknowledges that teams may need to operate autonomously in the short term while aligning to a broader, centralized strategy over time. Instead of enforcing a rigid structure from the outset, organizations can:

1. **Enable Quick Adoption**: Allow decentralized teams to adopt Camunda 8 using standalone deployments (Camunda Run, plain Java, RDBMS) to avoid delays caused by centralization efforts.
2. **Establish Central Governance**: Introduce a provisioning and monitoring layer that provides oversight without creating unnecessary roadblocks.
3. **Ensure a Smooth Transition**: As the central platform matures, facilitate a seamless migration path from independent deployments to a more structured, scalable architecture.

## Conclusion

While the transition from Camunda 7 to Camunda 8 introduces some challenges, it also presents an opportunity to increase the adoption of process orchestration. A well-defined platform team strategy was always beneficial—even in Camunda 7—and its value is even more pronounced with Camunda 8. By balancing decentralization for agility with centralization for long-term efficiency, organizations can achieve both speed and sustainability in their automation journey.

Learn more about how you can migrate, including resources and tools we've developed to make it easier, at [camunda.com/platform-7/migrate](https://camunda.com/platform-7/migrate/).
