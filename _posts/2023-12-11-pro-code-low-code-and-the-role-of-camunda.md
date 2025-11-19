---
layout: post
title: "Pro-code, Low-code, and the Role of Camunda"
date: 2023-12-11 12:00:00 +0000
categories: [blog]
tags: ["camunda", "orchestration", "spring", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "Here is the TL/DR: We will stay 100% developer-friendly and pro-code is our heart and soul or bread and butter if you prefer. But people that create process ..."
canonical: https://camunda.com/blog/2023/12/pro-code-low-code-role-of-camunda/
---
### Pro-code, Low-code, and the Role of Camunda

#### Pro-code is our heart and soul, but people and processes are diverse. Our optional low-code features support more use cases without getting in the way of pro-code developers.
Developers regularly ask me about Camunda’s product strategy. Especially around the Camunda 8 launch they raised concerns that we “forgot our roots” or “abandoned our developer-friendliness” — the exact attributes that developers love us for. They presume that we “jumped on the low-code train” instead, because we now have funding and need to “chase the big dollars.” As a developer at heart myself I can tell you that nothing is further from the truth, so let me explain our strategy in this post.

Here is the **TL/DR**: We will stay 100% developer-friendly and pro-code is our heart and soul (or bread and butter if you prefer). But people that create process solutions are diverse, as are the processes that need to be automated. So for some use cases low-code does make sense, and it is great to be able to support those cases. But low-code features in Camunda are optional and do not get in the way of pro-code developers.

For example, your worker code can become a reusable [**Connector**](https://docs.camunda.io/docs/components/connectors/introduction-to-connectors/) (or be replaced by an out-of-the-box one) that is configured in the BPMN model using element templates. But you don’t have to use that and can just stay in your development environment to code your way forward. This flexibility allows you to use Camunda for a wide variety of use cases, which prevents business departments from being forced into shaky low-code solutions just because IT lacks resources.

But step by step…

### Camunda 8 loves developers
First of all, Camunda 8 focuses on the developer experience in the same way — or even more strongly — than former Camunda versions. The whole point of providing Camunda as a product was to break out of unhandy huge BPM or low-code suites, that are simply impossible to use in professional software engineering projects (see [**the Camunda story here**](https://blog.bernd-ruecker.com/camunda-closes-100-million-series-b-funding-round-to-automate-any-process-anywhere-c82013bdaacf#8635) for example). This hasn’t changed. The heart of Camunda is around bringing process orchestration into the professional software developers toolbelt.

Especially with Camunda 8, we put a lot of focus on providing an excellent developer experience and a great programming model. And we now also extend that beyond the Java ecosystem. We might still have to do some homework here and there (for example getting the Spring integration to a supported product component 2024) — but it is very close to what we always had. Let me give you some short examples (you can find [**working code on GitHub**](https://github.com/berndruecker/customer-onboarding-camunda-8-springboot)).

Writing worker code (aka Java Delegates):

Using the Spring Boot Starter as Maven dependency:

Writing a JUnit test case (with an in-memory engine):

The only real change from Camunda version 7 to 8 is that the orchestration engine (or workflow engine if you prefer that term) runs as a separate Java process. So the above Spring Boot Starter actually starts a client that connects to the engine, not the whole engine itself. I wrote about why this is a huge advantage in [**moving from embedded to remote workflow engines**](https://blog.bernd-ruecker.com/moving-from-embedded-to-remote-workflow-engines-8472992cc371). Summarized, it is about isolating your code from the engine’s code and simplifying your overall solution project (think about optimizing the engine configuration or resolving third-party dependency version incompatibilities).

The adjusted architecture without relational database allows us to continuously look at scalability and performance and make big leaps with Camunda 8, allowing use cases we could not tackle with Camunda 7 (e.g. multiple thousands of process instances per second, geo-redundant active/active datacenters, etc.).

A common misconception is that you have to use our cloud/SaaS offering, but this is not true. You can [**run the engine self-managed as well**](https://docs.camunda.io/docs/next/self-managed/about-self-managed/) and there are different options to do that. The SaaS offering is an additional possibility you can leverage, freeing you from thinking about how to run and operate Camunda, but it is up to you if you want to make use of it.

This is a general recurring theme in Camunda 8: We added more possibilities you can leverage to make your own life easier — but we do not force anyone to use them.

The prime example of new possibilities are our low-code accelerators (e.g. [**Connectors**](https://camunda.com/platform/modeler/connectors/)). Let’s quickly dive into why we do low-code next before touching on how Connectors can help more concretely.

### Existing customers adopt Camunda for many use cases
We learned from our customers that they want to use Camunda for a wide variety of use cases. Many of the use cases are core end-to-end business processes, like customer onboarding, order fulfillment, claim settlement, payment processing, trading, or the like.

But customers also need to automate simpler processes. Those processes are less complex, less critical, and typically less valuable, but still those processes are there and automating them has a return on investment or is simply necessary to fulfill customer expectations. Good examples are around master data changes (e.g. address or bank account data), bank transfer limits, annual mileage reports for insurances, delay compensation, and so on.

![](https://cdn-images-1.medium.com/max/800/0*XymXq_jGVngKBkI0.png)

In the past, organizations often did not consider using Camunda for those processes, as they could not set up and staff software development projects for simpler, less critical processes.

And the non-functional requirements for those simpler process automation solutions differ. While the super critical high complex use cases are always implemented with the help of the IT team, to make sure the quality meets the expectations for this kind of solution and everything runs smoothly, the use cases on the lower end of that spectrum don’t have to comply with the same requirements. If they are down, it might not be the end of the world. If they get hacked, it might not be headline news. If there are wired bugs, it might just be annoying. So it is probably OK to apply a different approach to create solutions for these less critical processes.

### Categorizing use cases
The important thing is to make a conscious choice and not apply the wrong approach for the process at hand. What we have seen working successfully is to categorize use cases and place them into three buckets:

- **Red**: Processes are mission critical for the organization. They are also complex to automate and probably need to operate at scale. Performance and information security can be very relevant, and regulatory requirements might need to be fulfilled. Often we talk about core end-to-end business processes here, but sometimes also other processes might be that critical. For these use cases you need to do professional software engineering using industry best practices like version control, automated testing, continuous integration and continuous delivery. The organization wants to apply some governance, for example around which tools can be used and what best practices need to be applied.
- **Yellow**: Processes are less critical, but still the organization’s operations would be seriously affected if there are problems. So you need to apply a healthy level of governance, but need to accept that solutions are not created in the same quality as for red use cases, mostly because you simply have a shortage of software developers.
- **Green**: Simple automations, often being very local to one business unit or even an individual. These are often quick fixes stitched together to make one’s life a bit easier, but the overall organization might not even recognize if they break apart. For those uncritical use cases, the organization can afford leaving a lot of freedom to people, so typically there is no governance or quality assurance applied.

While the red use cases are traditionally done with Camunda, and the green use cases are traditionally done with Office-like tooling or low-code solutions (like Airtable or Zapier), the yellow bucket gets interesting. And this is a long tail of processes, that all needs to be automated with a fair level of governance, quality assurance and information security.

![](https://cdn-images-1.medium.com/max/800/0*bo2COBvqC4x5fz9h.png)

We already know organizations using Camunda for those yellow use cases. In order to do this and to ease solution creation, they developed low-code tooling on top of Camunda. A prime example is [**Goldman Sachs, who built a quite extensive platform based on Camunda 7**](https://camunda.com/blog/2018/07/camunda-days-nyc-goldman-sachs-workflow-platform/) (side note: they also[** talk about a differentiation between core banking use cases and the long tail of simpler processes across the firm in later presentations**](https://camunda.com/blog/2022/03/why-goldman-sachs-built-a-brand-new-platform/)). Speaking to those customers we found a recurring theme and used this feedback to design product extensions that those organizations could have used off-the-shelf (if it would have been there when they started). And we designed this solution to not get in the way of professional software developers when implementing red use cases around critical core processes.

I am not going into too much detail around all of those low code accelerators in this post, but it is mostly around [**Connectors**](https://camunda.com/platform/modeler/connectors/), rich forms, data handling, the out-of-the-box experience of tools like Tasklist, and browser-based tooling.

For me it is important to re-emphasize the pattern mentioned earlier: Those accelerators are an offer — you don’t have to use them. And if you look deeper, those accelerators are not mystic black boxes. A Connector, for example, is “just” a reusable [**job worker**](https://docs.camunda.io/docs/components/concepts/job-workers/) with a focused properties panel (if you are interested in code, check out any of our [**existing out-of-the-box Connectors**](https://github.com/camunda/connectors/tree/main/connectors)), whereas the property panel can even be [**generated from Java code**](https://github.com/camunda/connectors/blob/main/connectors/http/rest/pom.xml#L86). [**Camunda Marketplace**](https://marketplace.camunda.com/) helps you to make this reusable piece of functionality discoverable. Existing Connectors are available in their source and can be extended if needed.

![](https://cdn-images-1.medium.com/max/800/0*wegtI1zLFS3BjXBK.png)

### Democratization and acceleration by Connectors
There are two main motivations to use Connectors.

Software developers might simply become more productive by using them, and this is what we call **acceleration**. For example, it might simply be quicker to use a Twilio Connector instead of figuring out the REST API for sending an SMS and how it is best called from Java. As mentioned, if this is not true for you, e.g. because you have an internal library you simply use to hide the complexity of using Twilio, this is great, then you just keep using that. Also, when you want to write more JUnit tests, it might be simpler to write integration code in Java yourself. This is fine! You are not forced to use Connectors, it is an offer, and if it makes your life easier, use them.

The other more important advantage is that it allows a more diverse set of people to take part in solution creation, which is referred to as **democratization**. So for example, a tech-savvy business person could probably stitch together a simpler process using Connectors, even if they cannot write any programming code. Remember, we are talking about the long tail of simpler processes (yellow) here.

A powerful pattern then is that software developers **enable** other roles within the organization. One way of doing this can be to have a [**Center of Excellence**](https://camunda.com/blog/2022/12/how-to-create-grow-center-of-excellence/) where custom Connectors are built specifically shaped around the needs of the organization. And those Connectors are then used by other roles to stitch together the processes. One big advantage is that your IT team has control over how Connectors are built and used, allowing them to enforce important governance rules, e.g. around information security or secret handling (something which is a huge problem with typical low code solutions).

You could also mix different roles in one team creating a solution, and the developer can focus on the technical problems to set up Connectors properly, and more business-like people can concentrate on the process model. And of course there are many nuances in the middle.

![](https://cdn-images-1.medium.com/max/800/0*R97S7d25pSpbGKd_.png)

This is comparable to a situation we know from software vendors embedding Camunda into their software for customization. Their software product then typically comes with a default process model and consultants can customize the processes to end-customer needs within certain limits the software team built-in.

### Avoiding the danger zone when doing vendor rationalization and tool harmonization
Many organizations currently try to reduce the number of vendors and tools they are using. This is understandable on many levels, but it is very risky if the different non-functional requirements of green, yellow, and red processes are ignored.

For example, procurement departments might not want to have multiple process automation tools. But for them, the difference between Camunda and a low-code vendor is not very tangible as they both automate processes.

For red use cases, customers can still easily argue why they cannot use a low-code tool because those tools simply don’t fit into professional software development approaches. But for yellow use cases, this gets much more complicated to argue. This can lead to a situation where low-code tools, made for green use cases, are applied for yellow ones. This might work for very simple yellow processes, but can easily become risky if processes are getting too complex, or simply if requirements around stability, resilience, easing maintenance, scalability or information security rise over time. This is why I consider this a big danger zone for companies to be in.

![](https://cdn-images-1.medium.com/max/800/0*5rwLp4uJ6SWGvHXe.png)

Camunda’s low-code acceleration features allow you to use Camunda in more yellow use cases, as you don’t have to involve software developers for everything. But if non-functional requirements rise, you can always fulfill those with Camunda, as it is built for red use cases as well. Just as an example, you could start adding automated tests whenever the solution starts to be too shaky. Or you could scale operations, if you face an unexpected high demand (think of flight cancellations around the Covid pandemic — this was a yellow use case for airlines, but it became highly important to be able to process them efficiently basically overnight).

To summarize: It’s better to target yellow use cases with a pro-code solution like Camunda with added low-code acceleration layers that you can use, but don’t have to. This prevents risky situations with low-code solutions that cannot cope with rising non-functional requirements.

And to link back to our product strategy: With Camunda 8 we worked hard to allow even “redder” use cases (because of improved performance, scalability, and resilience), as well as more yellow use cases at the same time. So you can go further left (red) and right (yellow) at the same time.

### Summary
In today’s post I re-emphasized that Camunda is and will be developer-friendly. Pro-code (red) use cases are our bread and butter business, and honestly those are super exciting use cases where we can play to our strengths. This is strategically highly relevant, even if you might see a lot of marketing messaging around low-code accelerations at the moment.

Those low-code accelerators allow building less complex solutions (yellow) too, where typically other roles take part in solution creation (democratization, acceleration, and enablement). This helps you to reduce the risk of using the wrong tool for yellow use cases ending up in headline news.

You can read more about our [**vision for low-code here**](https://camunda.com/blog/2023/03/camunda-vision-low-code/), or if you’re curious about how our Connectors work, feel free to check out [**our docs to learn more**](https://docs.camunda.io/docs/components/connectors/introduction-to-connectors/).
