---
layout: post
title: "What to do When You Can’t Quickly Migrate to Camunda 8"
date: 2022-05-25 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "- You can’t leverage Camunda 8 — SaaShttps://camunda.com/get-started, but also don’t have Kubernetes at your disposal to install the platform self-managedhtt..."
canonical: https://camunda.com/blog/2022/05/what-to-do-when-you-cant-quickly-migrate-to-camunda-8/
---
### What to do When You Can’t Quickly Migrate to Camunda 8

#### Managing a brownfield when you simply don’t have a green one
[With Camunda Platform 8 out of the door](https://camunda.com/blog/2022/04/camunda-platform-8-0-released-whats-new/) now, I’ve been having frequent discussions around migration. Many of them go along the lines of: “We are invested in Camunda 7, including a lot of best practices, project templates, and even code artifacts. We can’t quickly migrate to Camunda 8, so what should we do now?” I call this a [brownfield](https://en.wikipedia.org/wiki/Brownfield_(software_development)). If you are in this situation, this blog post is for you.

### Greenfield recommendation
But let’s start with the easy things first. Let’s assume you just entered the world of process automation and orchestration with Camunda, and you’re starting from scratch. In this case, we strongly recommend starting with Camunda 8 right away, for example, using [the Java greenfield stack](https://docs.camunda.io/docs/components/best-practices/architecture/deciding-about-your-stack/#the-greenfield-stack): Java, Spring Boot, Spring Zeebe, and Camunda Platform 8 — SaaS.

### Can’t use Camunda 8 just yet?
But there are some edge cases where you might not want to use Camunda 8 right away. The typical reasons include:

- You can’t leverage [Camunda 8 — SaaS](https://camunda.com/get-started), but also don’t have Kubernetes at your disposal to [install the platform self-managed](https://docs.camunda.io/docs/self-managed/overview/). While installing [Camunda 8 on bare-metal or VMs](https://docs.camunda.io/docs/self-managed/platform-deployment/local/) is possible, it is also not super straightforward and might not be your choice if you have to set up many engines in a big organization that embraces microservices. Of course, you could probably leverage existing Infrastructure as Code (IaC) toolchains to ease this task (like Terraform or Ansible).
- You are missing a concrete feature because Camunda 8 needs to catch up on feature parity. The prime examples are around [BPMN elements like compensation or conditions](https://docs.camunda.io/docs/components/modeler/bpmn/bpmn-coverage/).
- You stick to a principle not to run x.0 software versions in production (while I do see the point here, I want to add that I don’t think this applies to Camunda 8.0. It is technically a Camunda Cloud 1.4 release with quite some people already in production with it).

Independent of the exact reason, this means that you should start on a greenfield with Camunda 7. It’s worth repeating that this should be an exception. In this case, the recommendation is to start with the latest [Camunda 7 greenfield stack](https://docs.camunda.io/docs/components/best-practices/architecture/deciding-about-your-stack-c7/#the-java-greenfield-stack): Camunda Run as a remote engine via Docker and [External Tasks](https://docs.camunda.org/manual/latest/user-guide/process-engine/external-tasks/). If you code in Java, your process solution stack will be Java, Spring Boot, and the [Camunda REST Client](https://github.com/camunda-community-hub/camunda-engine-rest-client-java/). If you program in other languages, you should simply leverage the [REST API](https://docs.camunda.org/manual/latest/reference/rest/). This is conceptually pretty close to a Camunda 8 architecture. Let’s call it the **external task approach**.

There is one downside of this stack, though — the Java developer experience is not as great as it is with Camunda 8. [Historically, Camunda users preferred embedded engines](https://camunda.com/blog/2022/02/moving-from-embedded-to-remote-workflow-engines/) using [Java Delegates](https://docs.camunda.org/manual/latest/user-guide/process-engine/delegation-code/#java-delegate). This stack offers a great experience for Java developers. Camunda Run does not offer that same level of developer experience, even though it has improved over the years. While this is normally not a real problem, it might decrease developer motivation around Camunda projects. So if this is a real problem in your context, it is worth going with the greenfield stack from some years ago: Java, Spring Boot, [Camunda Spring Boot Starter](https://github.com/camunda/camunda-bpm-platform/tree/master/spring-boot-starter), and [Java Delegates](https://docs.camunda.org/manual/latest/user-guide/process-engine/delegation-code/#java-delegate). This stack is also [mentioned as the example in our migration guide](https://docs.camunda.io/docs/guides/migrating-from-camunda-platform-7/#process-solutions-using-spring-boot), as it is by far the most common Camunda 7 stack you’ll meet in the wild. Let’s call this the **Java Delegate approach**.

So I see both approaches as valid choices. But, of course, if you start with Camunda 7 now, you need to think ahead and prepare for a future Camunda 8 migration. This is where the approaches differ; with Java Delegates, you have a harder time making sure to stick to what we call *Clean Delegates*, as Java Delegates technically allow pretty dirty hacks. But there will be more on this later in this blog post.

### Greenfield recommendation summary
So let’s quickly recap our recommendations so far:

- Use Camunda Platform 8 — SaaS.
- If this is not possible, use Camunda Platform 8 — Self-Managed.
- If this is not possible, use Camunda Platform 7 Run and the external task approach.
- If this is not possible, use Camunda Platform 7 Spring Boot Starter, but implement *Clean Delegates.*

![](https://cdn-images-1.medium.com/max/800/1*krQbPLkFELWeLSu1a0NsBQ.png)

### Brownfields
Now let’s turn our attention back to the brownfield companies. In such situations, the company already uses Camunda 7 and will not migrate overnight to Camunda 8 ([which neither makes sense nor is necessary](https://docs.camunda.io/docs/guides/migrating-from-camunda-platform-7/#when-to-migrate)). In an ideal world, you would simply start new projects with Camunda 8 and migrate your existing projects step by step over time. But often, it is not that easy.

For example, your company might have invested a lot of effort into integrating Camunda 7 into its ecosystem. This goes far beyond the code of one process solution but includes best practices, examples, code snippets, reusable connectors, and many more. In such cases, you might still want to start new projects with Camunda 7 until you have a clear idea (and budget) of how to migrate all of those things.

Or your project is already in-flight and will be finished better with Camunda 7. Or an initiative pops up to extend an existing Camunda 7 process solution, and you cannot make the migration to Camunda 8 part of that endeavor.

In those cases, the typical question is, “Should we keep doing what we are doing, or should we quickly try to change our architecture to get closer to Camunda 8 already?”

The short answer is to **keep doing what you are doing**. This will make migration efforts easier at a later point in time, as you will have one common architecture to migrate. If you adjust your Camunda 7 architecture now, you might end up with two different architecture blueprints you need to migrate. Both external task and Java delegate approaches are OK!

But you should make sure to establish some practices as quickly as possible that will ease migration projects later on. Those are described in the rest of this post. While external tasks might enforce some practices, *Clean Delegates* are equally easy (or sometimes even easier) to migrate.

### Practices to ease migration
In order to implement Camunda 7 process solutions[ that can be easily migrated](https://www.youtube.com/watch?v=yS0wAO0KgBc), you should stick to the following rules (that are good development practices you should follow anyway), which will be explained in more detail later:

- Implement what we call Clean Delegates* — *concentrate on reading and writing process variables, plus business logic delegation. Data transformations will be mostly done as part of your delegate (and especially not as listeners, as mentioned below). Separate your actual business logic from the delegates and all Camunda APIs. Avoid accessing the BPMN model and invoking Camunda APIs within your delegates.
- Don’t use listeners or Spring beans in expressions to do data transformations via Java code.
- Don’t rely on an ACID transaction manager spanning multiple steps or resources.
- Don’t expose Camunda API (REST or Java) to other services or front-end applications.
- Use primitive variable types or JSON payloads only (no XML or serialized Java objects).
- Use simple expressions or plug-in FEEL. FEEL is the only supported expression language in Camunda 8. JSONPath is also relatively easy to translate to FEEL. Avoid using special variables in expressions, e.g., `execution` or `task`.
- Use your own user interface or Camunda Forms; the other form mechanisms are not supported out-of-the-box in Camunda 8.
- Avoid using any implementation classes from Camunda; generally, those with *.impl.* in their package name.
- Avoid using engine plugins.

For the moment, it might also be good to check the [BPMN elements supported in Camunda 8](https://docs.camunda.io/docs/components/modeler/bpmn/bpmn-coverage/), but this gap will most likely be closed soon.

[Execution Listeners](https://docs.camunda.org/manual/latest/user-guide/process-engine/delegation-code/#execution-listener) and [Task Listeners](https://docs.camunda.org/manual/latest/user-guide/process-engine/delegation-code/#execution-listener) are areas in Camunda 8 that are still under discussion. Currently, those use cases need to be solved slightly differently. Depending on your use case, the following Camunda 8 features can be used:

- Input and output mappings using FEEL
- Tasklist API
- History API
- Exporters
- Client interceptors
- Gateway interceptors
- Job workers on user tasks

I expect to soon have a solution in Camunda 8 for most of the problems that listeners solve. Still, it might be good practice to use as few listeners as possible, and especially don’t use them for data mapping as described below.

### Clean Delegates
With Java Delegates and the workflow engine being embedded as a library, projects can do dirty hacks in their code. Casting to implementation classes? No problem. Using a ThreadLocal or trusting a specific transaction manager implementation? Yeah, possible. Calling complex Spring beans hidden behind a simple JUEL (Java unified expression language) expression? Well, you guessed it — doable!

Those hacks are the real show stoppers for migration, as they simply cannot be migrated to Camunda 8. Actually, [Camunda 8 increased isolation intentionally](https://blog.bernd-ruecker.com/moving-from-embedded-to-remote-workflow-engines-8472992cc371).

So you should concentrate on what a Java Delegate is intended to do:

- Read variables from the process and potentially manipulate or transform that data to be used by your business logic.
- Delegate to business logic — this is where Java Delegates got their name from. In a perfect world, you would simply issue a call to your business code in another Spring bean or remote service.
- Transform the results of that business logic into variables you write into the process.

Here’s an example of an ideal JavaDelegate:

And you should never cast to Camunda implementation classes, use any ThreadLocal object, or influence the transaction manager in any way. Java Delegates should further always be stateless and not store any data in their fields.

The resulting delegate can be easily migrated to a Camunda 8 API, or simply be reused by the [adapter provided in this migration community extension](https://github.com/camunda-community-hub/camunda-7-to-8-migration/).

### No transaction managers
You [should not trust ACID transaction managers to glue together the workflow engine with your business code](https://blog.bernd-ruecker.com/achieving-consistency-without-transaction-managers-7cb480bd08c). Instead, you need to embrace eventual consistency and make every service task its own transactional step. If you are familiar with Camunda 7 lingo, this means that all BPMN elements will be async=true. A process solution that relies on five service tasks to be executed within one ACID transaction, probably rolling back in case of an error, will make migration challenging.

### Don’t expose Camunda API
You should try to apply the [information hiding principle](https://en.wikipedia.org/wiki/Information_hiding) and not expose too much of the Camunda API to other parts of your application.

In the above example, you should not hand over an execution context to your CrmFacade, which is hopefully intuitive anyway:

*// DO NOT DO THIS!*crmFacade.createCustomer(execution);The same holds true for when a new order is placed, and your order fulfillment process should be started. Instead of the front-end calling the Camunda API to start a process instance, you are better off providing your own endpoint to translate between the inbound REST call and Camunda, like this for example:

### Use primitive variable types or JSON
Camunda 7 provides quite flexible ways to add data to your process. For example, you could add Java objects that would be serialized as byte code. Java byte code is brittle and also tied to the Java runtime environment. Another possibility is magically transforming those objects on the fly to XML using Camunda Spin. It turned out this was black magic and led to regular problems, which is why Camunda 8 does not offer this anymore. Instead, you should do any transformation within your code before talking to Camunda. Camunda 8 only takes JSON as a payload, which automatically includes primitive values.

In the [above example](https://gist.github.com/berndruecker/dbc22c3bb92719be40d41bc9cbbb88d6), you can see that Jackson was used in the delegate for JSON to Java mapping:

This way, you have full control over what is happening, and such code is also easily migratable. And the overall complexity is even lower, as Jackson is quite known to Java people — a kind of de-facto standard with a lot of best practices and recipes available.

### Simple expressions and FEEL
[Camunda 8 uses FEEL as its expression language](https://docs.camunda.io/docs/components/modeler/feel/what-is-feel/). There are big advantages to this decision. Not only are the expression languages between BPMN and DMN harmonized, but also the language is really powerful for typical expressions. One of my favorite examples is the following onboarding demo we regularly show. A decision table will hand back a list of possible risks, whereas every risk has a severity indicator (yellow, red) and a description.

![](https://cdn-images-1.medium.com/max/800/0*1roHQ2SpVDuhjdnV)

The result of this decision shall be used in the process to make a routing decision:

![](https://cdn-images-1.medium.com/max/800/0*CHnRfoCIVQDPEm6Y)

To unwrap the DMN result in Camunda 7, you could write some Java code and attach that to a listener when leaving the DMN task (this is already an anti-pattern for migration as you will read next). This code is not super readable:

With FEEL, you can evaluate that data structure directly and have an expression on the “red” path:

= some risk in riskLevels satisfies risk = "red"Isn’t this a great expression? If you think, yes, and you have such use cases, you can even hook in FEEL as the scripting language in Camunda 7 today (as explained by [Scripting with DMN inside BPMN](https://camunda.com/blog/2018/07/dmn-scripting/) or [User Task Assignment based on a DMN Decision Table](https://camunda.com/blog/2020/05/camunda-bpm-user-task-assignment-based-on-a-dmn-decision-table/)).

But the more common situation is that you will keep using JUEL in Camunda 7. If you write simple expressions, they can be easily migrated automatically, as you can see in [the test case](https://github.com/camunda-community-hub/camunda-7-to-8-migration/blob/main/modeler-plugin-7-to-8-converter/client/JuelToFeelConverter.test.js) of the [migration community extension](https://github.com/camunda-community-hub/camunda-7-to-8-migration). You should avoid more complex expressions if possible. Very often, a good workaround to achieve this is to adjust the output mapping of your Java Delegate to prepare data in a form that allows for easy expressions.

You should definitely avoid hooking in Java code during an expression evaluation. The above listener to process the DMN result was one example of this. But a more diabolic example could be the following expression in Camunda 7:

#{ dmnResultChecker.check( riskDMNresult ) }Now, the dmnResultChecker is a Spring bean that can contain arbitrary Java logic, possibly even querying some remote service to query whether we currently accept yellow risks or not (sorry, this is not a good example). Such code can not be executed within Camunda 8 FEEL expressions, and the logic needs to be moved elsewhere.

### Camunda Forms
Finally, while Camunda 7 supports [different types of task forms](https://docs.camunda.org/manual/latest/user-guide/task-forms/), Camunda 8 only supports [Camunda Forms](https://docs.camunda.io/docs/guides/utilizing-forms/#configuration) (and will actually be extended over time). If you rely on other form types, you either need to make Camunda Forms out of them or use a bespoke tasklist where you still support those forms.

### Summary
In today’s blog post, I wanted to show you which path to take if Camunda 8 is not yet an option for you. In summary, it’s best you keep doing what you’re already doing. This normally means leveraging the external task approach or the Java Delegate approach. Both options are OK.

With Java Delegates, you have to be very mindful to avoid hacks that will hinder a migration to Camunda 8. This article sketched the practices you should stick to in order to make migration easier whenever you want to do it, which is mostly about writing clean delegates, sticking to common architecture best practices, using primitive values or JSON, and writing simple expressions.

As always, I am happy to hear your feedback or [discuss any questions you might have](https://forum.camunda.io/).
