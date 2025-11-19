---
layout: post
title: "How Open is Camunda Platform 8?"
date: 2022-05-25 12:00:00 +0000
categories: [blog]
tags: ["camunda", "architecture"]
author: Bernd Ruecker
excerpt: "- Green: Open source license.
- Green stripes: Source-available license for the curious, the difference between open source and source-available is explained..."
canonical: https://camunda.com/blog/2022/05/how-open-is-camunda-platform-8/
---
### How Open is Camunda Platform 8?
With [Camunda Platform 8 being available to the public](https://camunda.com/blog/2022/04/camunda-platform-8-0-released-whats-new/), we regularly answer questions about our open source strategy and the licenses for its various components. Let’s sort this out in today’s blog post by looking at the specifics of the components, sketching a path to put Camunda 8 into production without the need to pay us any money, and the difference between open source and source-available licenses.

### Component overview
Let’s look at the various [components that make up Camunda Platform 8](https://docs.camunda.io/docs/components/). The following illustration colors the components according to their license:

- **Green**: Open source license.
- **Green stripes**: Source-available license (for the curious, the difference between open source and source-available is explained below, for most people, there is no real difference).
- **Blue**: This software is available but only free for non-production use. If you want to put these components into production, you will need to buy a license (via enterprise subscription) from Camunda.
- **Red**: This software is only available within Camunda Platform 8 — SaaS and can’t be run self-managed. **Note:** This is subject to change, and some of the red components should turn blue over time.

![](https://cdn-images-1.medium.com/max/800/0*OU2RxW1hNr3mReuF.png)

The short summary is that you can run everything **green **(including green stripes) as [self-managed](https://docs.camunda.io/docs/self-managed/overview/) in production without needing a license. The green components are open source, as coined by the [Open Source Initiative](https://opensource.org/licenses). The striped components use a source-available license. Regarding Zeebe, this is the [Zeebe Community License v1.0](https://camunda.com/blog/2019/07/introducing-zeebe-community-license-1-0/). It is based on the very liberal open source [MIT license](https://opensource.org/licenses/MIT) but with one restriction — users are **not **allowed to use the components for providing a commercial workflow service in the cloud. This is typically not a limitation for any of our existing customers, users, or prospects. If you want to know more about open source licensing, visit [Why We Created The Zeebe Community License](https://camunda.com/blog/2019/07/zeebe-community-license/) and [Zeebe License Overview and FAQ](https://camunda.com/legal/terms/cloud-terms-and-conditions/zeebe-license-overview-and-faq/).

Furthermore, you can run all the **blue **components during development and testing. This not only allows you to try them out but will help you with your development efforts. If you want to keep using them while going into production, you will need to buy a license from Camunda. Later in this blog post, I will explain how you can go live without those components, as there is a possible path.

Now, let’s quickly look at a typical question in this context: why are the blue boxes not available for production, even in a limited version?

### Why free for non-production and not open core?
With Camunda Platform 7, we have an open core model where parts of the components are available open source, and the full-feature set is only available to you if you buy an enterprise subscription. So for example, the basic tier of Camunda Cockpit allows you to see running instances in open source, but only the Enterprise Edition of Camunda Cockpit shows the historical data and provides the full-feature set.

While this looks good at first glance, it actually adds a lot of friction and confusion for our users. First, they have to understand the feature differences in detail. Second, most people even miss that there is a more powerful version of Cockpit available, leading them to redevelop features that are already there. And finally, even if the customer’s team requires the power of the Enterprise Edition of Cockpit, selling the license is hard in situations where decision-makers might not care enough about the daily friction of operations to spend the money. In other words, our power users often want an Enterprise license and have a good business case for it but are still let down by their decision-makers.

This is why we made the whole model radically simpler. You can have all the tools with all the features during development without any fluff. Everything is easily accessible ([available on DockerHub](https://hub.docker.com/u/camunda), for example), can help you learn Camunda, and speed up development. For example, Camunda Operate (the Cockpit equivalent in Camunda 8) helps you to understand what’s going on in your workflow engine, especially when you are new and start developing.

You will only need to buy a license when you put it into production. But the argument for the Enterprise Edition is now very simple to understand — without it, you can’t use those productivity tools. So far, our users are actually pretty happy about that change, as it makes it easier for them to ask for the necessary budget.

If for whatever reason your company doesn’t want to pay for the Enterprise Edition, there is still a way to production, as described below. However, it is less convenient and involves more work for you. Whether this is worth saving the subscription money is your company’s decision.

We believe this model has a very good balance of interests:

- First, you can easily start developing process solutions with Camunda Platform 8, but also run severe workloads in production with a completely source-available stack.
- Second, there is sufficient motivation to pay for the additional software, which guarantees that Camunda will stick around.
- Third, this allows Camunda to stay focused and continue to invest in great software and the community.

### How SaaS changes the game
So far, we’ve talked about self-managed installations. Somehow, this still seems to be the default in the heads of most people. They want to download and run the software, but this is changing. When you really think about it, you don’t want software — you want some service or feature the software is delivering. This is what cloud and SaaS (software as a service) provide. With Camunda 8, we introduced our [own SaaS offering](https://camunda.com/pricing/), where you can completely consume it in the cloud.

Now, this changes one important aspect — you have to be clear if you’re searching for open source or something that is **free to use**. And most people actually search for the latter, which can also be delivered without open source.

So with Camunda Platform 8 — SaaS, the equivalent of a Community Edition is a free tier, where users can use the service (within certain boundaries) without generating any bills. As I’m writing this blog post, we are working to extend our free tier with Camunda 8. The current situation is that you can already have a [**free plan for modeling**](https://camunda.com/pricing/)** **use cases. And we are **working on a free tier to support execution** use cases, but still have to work out some details. In contrast to providing a Community Edition for download, every running cluster in the cloud adds up on our own GCP bill, so we have to be diligent about it.

In general, I expect a big mindset shift over the next few years in this regard. Users will mostly consume SaaS services, and having a free tier will be more important to them than software being open source.

At this point, I want to add one important side note — our SaaS focus will not mean that our open source commitment will be weakened, on the contrary. We have a big group of passionate people in our community that do miracles for us, and we continuously increase our investment in the community.

Camunda 8 has all the key ingredients to make a vital [open source community](https://camunda.com/developers/community/) work:

- The source code for core components is available.
- Code, issues, and discussions live in the open on GitHub. The frequent pull requests to our documentation are great examples of this.
- Extension points allow community contributions.
- Frequent meetups, talks, and blog posts.
- A great developer relations team that deeply cares about the community.

### A path to production with source-available software
Let’s come back to self-managed software and sketch a path to production that neither requires a commercial license nor breaks any license agreements. For production, this basically comes down to using only the source-available parts of Camunda 8:

![](https://cdn-images-1.medium.com/max/800/0*btxqJ5cln3p0KHbC.png)

Additionally, you will need to find solutions to replace the tools you cannot use.

**Tasklist**

You will need to implement your own task management solution based on using workers subscribing to Zeebe [as described in the docs](https://docs.camunda.io/docs/components/modeler/bpmn/user-tasks/). That also means you have to build your own persistence to allow task queries, as the [Tasklist API](https://docs.camunda.io/docs/apis-clients/tasklist-api/overview/) is part of the Tasklist component and is not free for production use.

**Operate**

Operate is the component you will miss most, as you typically want to gain a clear understanding of what is going on in your workflow engine and take corrective actions.

For looking at data, you can access it in Elastic (check the [Elastic Exporter](https://github.com/camunda/zeebe/tree/main/exporters/elasticsearch-exporter) for details), leverage the [metrics](https://docs.camunda.io/docs/self-managed/zeebe-deployment/operations/metrics/), or build your [own exporters](https://docs.camunda.io/docs/components/zeebe/technical-concepts/architecture/#exporters) to push it to some data storage component that is convenient for you. Exporters can also filter or pre-process data on the fly. It is worth noting that the [Operate data pre-processing logic backing the History API](https://docs.camunda.io/docs/apis-clients/operate-api/) is part of Operate and not free for production use.

For influencing process instances (like canceling them), you can use the existing [Zeebe API](https://docs.camunda.io/docs/apis-clients/grpc/), which is also exposed as the [command-line tool zbctl](https://docs.camunda.io/docs/apis-clients/cli-client/).

This flexibility allows you to hook functionality into your own front-ends. Of course, this takes effort, but it is definitely possible, and we know of users that have done it. As already mentioned, you should contrast that effort with the costs of the license.

**Optimize**

Optimize is hard to replace because it goes quite deep into process-based analytics, which is hard to build on your own. If you can’t use Optimize, the closest you might get to it is by adding your [own exporters](https://docs.camunda.io/docs/components/zeebe/technical-concepts/architecture/#exporters) to push the data to an existing general-purpose BI (Business Intelligence), DWH (Data Warehouse), or data lake solution.

### Conclusion
In this blog post, I wanted to make it very clear what components of the Camunda 8 stack are open source (or source-available) and which are not free for production use. I gave some pointers to go into production with a pure source-available stack but also tried to explain the efforts that might require, which is, of course, the upselling potential the company needs. I hope this was understandable, and I’m happy to discuss this in [the Camunda forum](https://forum.camunda.io/) in case there are open questions.
