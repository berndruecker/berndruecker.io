---
layout: post
title: "The Process Automation Map"
date: 2021-12-21 12:00:00 +0000
categories: [blog]
tags: ["camunda", "spring", "process-automation"]
author: Bernd Ruecker
excerpt: "Imagine your CEO wants you to increase process automation as part of the organization’s push towards becoming a digital enterprise. As a first project, you n..."
canonical: https://blog.bernd-ruecker.com/the-process-automation-map-1abe2d79192b
---
### The Process Automation Map
[*This article was originally posted on techspective*](https://techspective.net/2021/11/22/the-process-automation-map/).

Imagine your CEO wants you to increase process automation as part of the organization’s push towards becoming a digital enterprise. As a first project, you need to automate the payroll run, which is a manual and tedious process at your company currently. How could you go about this? Should you look into process automation platforms to help out?

In this case, the decision is not too hard: as thousands of companies have the exact same requirements you have, you can simply buy a standard HR software or leverage an off-the-shelf cloud service around payroll. This will quickly and cheaply automate this process for you.

Empowered by this success, your next process to automate is your company’s core order fulfillment process, also known as order-to-cash. Order fulfillment needs to integrate some really beasty legacy systems, so what do you do now? Buying standard software again, probably customizing it to your specific needs? Leveraging one of the low-code tools industries are raging about? Or applying software engineering methods accelerated by developer-friendly process automation technology?

This is actually much harder to answer. And guess what: it depends. It depends on various aspects of your situation and the process at hand. To help you with this kind of decision, I created what I call the process automation map (inspired by [“the culture map” by Erin Meyer](https://www.amazon.com/Culture-Map-Breaking-Invisible-Boundaries/dp/1610392507/), which is not a prerequisite to understand this article, even if it is definitely worth a read).

### Understanding the Process Automation Map
The process automation map defines the following set of dimensions that can guide you towards the right software solution for process automation:

- **Uniqueness of process**: Your payroll service is not unique, hence you use off-the-shelf standard software. The order fulfillment process has unique requirements, so a more tailor-made solution is required. Customizing standard software is a middle ground, but often ends up in nightmares during maintenance, especially with new software releases. Instead, I favor tailor-made processes for the specialties, integrating with standard software.
 For tailor-made solutions you need to look at the other dimensions:
- **Process complexity**: Your order fulfillment process needs to call out to various systems (e.g. some Cloud systems like Salesforce, your mainframe system, and bespoke legacy systems). Additionally, you need to pull in human decision-makers for risky orders and present them with the right information and context to do their decision as quickly as possible in an optimized user interface. These are complexity drivers.
- **Process scale**: You expect hundreds of orders per day, which is a medium scale. But you also know that you plan to run this huge ad campaign in autumn, where traffic hopefully peaks to thousands of orders in a single day, which poses elasticity and stability requirements on the software solution. An extreme example of big scale is one of our customers, [automating a process that shall be able to process up to two million payments per hour](https://page.camunda.com/cclive-2021-goldman-sachs).
- **Scope**: Your order fulfillment process is a process with multiple steps where you care about their sequence. For example, you have to make sure an order is approved before the money will be collected. And it shall only be delivered once it is successfully paid for. This is process automation, and it contrasts with the automation of single tasks like for example automating the human decision above with machine learning.
- **Project setup**: Your order-to-cash process is the critical backbone of your company and needs to run reliably. You will maintain the solution for the next years to come. So you want a strategic setup of your process automation project.

Now, you can rate the process automation candidate on all of these dimensions:

![](https://cdn-images-1.medium.com/max/800/0*5GCE7j9HvI1Zv6OA.png)

### Solution Categories
This rating now helps you to determine which solution to pick. For this article, I focus on four solution categories:

**1. Commercial off-the-shelf software** providing a ready-to-use implementation for certain common problems.

**2. Tailor-made solutions **requiring own development effort to build out the final solution for the business problem. The development effort can either use low code or pro code tools.

**2a. Low code**, meaning that non-developer are enabled to build the solution, which is typically reached by a mixture of abstractions of technical details, pre-built components, and graphical wizards.

**2b. Pro code**, meaning that software development is happening, but accelerated by tools that solve all problems related to process automation.

You can read more about these categories in [Understanding the process automation landscape](https://blog.bernd-ruecker.com/understanding-the-process-automation-landscape-9406fe019d93).

Now, the payroll process is a standard process. This rating leads you to a quick conclusion: you should go for commercial off-the-shelf software and can mostly ignore the other dimensions.

In contrast, unique processes require tailor-made solutions. These solutions can be built by low code or pro code tooling. And depending on where your rating tends to be on the map, you should select one or the other. The following illustration gives you a good indication of where the sweet spots for solution categories are.

![](https://cdn-images-1.medium.com/max/800/0*2NYUQUZDpW6doPTo.png)

In the order fulfillment process, the ratings are placed more on the right-hand side of the map so you are in the realm of pro code (developer-friendly) process automation tooling. These tools allow you to model the order fulfillment process graphically and then add glue code, most often in well-known programming languages like Java, C#, or NodeJS, to integrate it with its surroundings, in your case Salesforce and the bespoke systems via an API (e.g. REST) and your mainframe via custom code.

Using pro-code techniques allows you to leverage all best practices from software engineering: reusing existing frameworks and libraries, leveraging development environments and version control, applying continuous delivery practices, and so on. These practices have proven that they can deal with high complexity, scalability, and stability requirements very well. Solutions have high quality and maintainability. The process automation platform will simply add capabilities to deal with long-running process flows.

Let’s also look at the left-hand side of the map with another example. Assume you want to automate your marketing campaign process. This involves defining a campaign, approving it, making the necessary bookings, and assessing the impact it had afterward. Let’s rate this process:

**1. Uniqueness of process:** While this process is not super unique, the way you decide for campaigns and assess its result means that you need something additional to the standard marketing tools out there.

**2. Process complexity:** This process is not very complex. It is also fully owned by a handful of people all within the marketing team.

**3. Process scale:** You only run a handful of campaigns a month, which is a low scale.

**4. Scope:** Campaigns involve a couple of steps, so it is a process, but only with very simple process logic.

**5. Project setup:** As you just evolve your marketing practice, you expect that the process will also evolve over time. So you might be happy to automate only certain pain points in an ad-hoc fashion, knowing that you will replace these pieces of automation sooner or later again. If they fall apart, it does not do much harm.

![](https://cdn-images-1.medium.com/max/800/0*-dN6GrOaMPhXQ8iZ.png)

This time, the ratings tend towards the left-hand side. This is an indicator that low code tooling could work well in your case. Maybe a simple [Airtable](https://airtable.com/) to list campaigns alongside status flags is a sufficient basis for you. You can then build some low-code integrations that simply trigger emails when something needs to be done.

### Use The Map in Your Own Projects
There is no one-size-fits-all solution for process automation. This is why companies must understand the forces that might drag you towards one or another solution category. I have only scratched the surface, but you might want to have a look at [“exploring the Process Automation Map”](https://blog.bernd-ruecker.com/exploring-the-process-automation-map-7d9aa181a747) for more details on the various dimensions and guidance on how to rate your own process.

Depending on this rating, the sweet spots help you determine the solution category to look at. In this article software categories were simplified to standard software, low code, and pro code tools, but if you want to dive deeper into solutions categories, it might be worth looking into “[understanding the process automation landscape](https://www.infoworld.com/article/3617928/understanding-the-process-automation-landscape.html)”.

Of course, the map will always be an oversimplification. But as long as it is useful to guide you through the tooling jungle or help you find arguments to sell this approach internally, it is worth its existence. To help you create your own map, I [uploaded a template slide here](https://docs.google.com/presentation/d/1sh6tsSp-q2uz4pmUmAmPDiGo-UUFGBlsANOahCGGqmA/edit?usp=sharing). Feel free to use and distribute that at your own discretion. And I am always happy to receive copies of your own processes rated on the map.
