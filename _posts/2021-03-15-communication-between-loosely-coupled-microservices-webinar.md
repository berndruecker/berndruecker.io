---
layout: post
title: "Communication Between Loosely Coupled Microservices — Webinar FAQ"
date: 2021-03-15 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "event-driven", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "!https://cdn-images-1.medium.com/max/800/1GLFVaK-ZYnXuTV5b1biJPA.png"
canonical: https://blog.bernd-ruecker.com/communication-between-loosely-coupled-microservices-webinar-faq-a02708b3c8b5
---
### Communication Between Loosely Coupled Microservices — Webinar FAQ

![](https://cdn-images-1.medium.com/max/800/1*GLFVaK-ZYnXuTV5b1biJPA.png)

In the recent [webinar titled “Communication Between Loosely Coupled Microservices”](https://page.camunda.com/wb-communication-between-microservices) we got a lot of great questions and because of the limited time some were left unanswered. As community questions are really important to me I want to follow my tradition to answer remaining questions in a blog post (as I have for example also done roughly a year ago in “[Webinar FAQ for Monitoring & Orchestrating Your Microservices Landscape using Workflow Automation](https://blog.bernd-ruecker.com/microservices-webinar-faq-1a9741f4481c)”).

### What Was The Webinar About?
You can find the [slides (here)](https://www.slideshare.net/BerndRuecker/webinar-communication-between-loosely-coupled-microservices) and [recording (here)](https://page.camunda.com/wb-communication-between-microservices) online.

The webinar covered different styles of communication. This was explained by looking at how ordering food works:

- **Synchronous blocking**: A call uses a synchronous protocol, like HTTP, and blocks for the result. This is you, calling a pizza place via phone.
- **Asynchronous non-blocking**: A call is done by sending a message using messaging infrastructure. In this case, the sender is not blocked. This is you sending an email to the pizza place. The missing feedback loop here can be a problem — you might not feel confident that the pizza place received your email. This is why an email confirmation back to you makes a lot of sense, which can be delivered asynchronously again.

![](https://cdn-images-1.medium.com/max/800/0*rU1JNJqpVi9aA896)

The communication style is very different from styles of collaboration of services, which was also discussed:

- **Command-driven**: You want something to happen, e.g. you order a pizza. This is a command sent to the pizza place, independent of the communication style (synchronous via phone or asynchronous via email).
- **Event-driven**: You let the world know that something has happened, the decision of what action (if any) follows this event is not yours. In the example you might tweet that you are hungry, which might lead to somebody bringing you food — or most probably not.

The following table gives an overview of communication and collaboration styles.

![](https://cdn-images-1.medium.com/max/800/0*9sN0sMneIQlrbedp)

Let’s not repeat the webinar, but dive into your open questions!

### Architecture Questions

#### Q: For communication styles, isn’t it worth distinguishing between requirement and implementation level? A blocking behavior can be easily implemented asynchronously.
This is indeed true, you can always push the problem one layer “down”.

For example, computer networks leverage [TCP/IP](https://en.wikipedia.org/wiki/Transmission_Control_Protocol) (you just used it when you opened this web page). This protocol sends asynchronous packets under the hood, but adds a protocol layer on top to guarantee reliable transmission and detect possible errors. So logically it looks synchronous, but on the implementation level it is completely asynchronous.

You can apply the same argument to messaging systems and apply some RPC (Remote Procedure Call = synchronous blocking) style of communication on top of the messages (asynchronous), like for example shown in the [RabbitMQ RPC tutorial](https://www.rabbitmq.com/tutorials/tutorial-six-python.html):

![](https://cdn-images-1.medium.com/max/800/0*cxm2aYCZa5TLu0LJ)

Please note, that this design doesn’t completely hide the asynchronous channel, as you have to deal with timeouts in your client code very explicitly. I find this is a very good thing, as it reminds every developer that a call might actually time out, which could also happen with synchronous REST calls — where most people don’t think about time outs.

So strictly speaking I would answer yes to the question of whether we should distinguish requirement and implementation level. But practically speaking I would say no, as I find it much easier to not distinguish between the requirement and implementation behavior. This just adds a source of confusion and might lead to unnecessary questions and discussions. I even remember such discussions when I was a junior developer, and I was really puzzled by people that kept saying that the internet is always asynchronous, no matter if we use REST or messaging (or SOAP and JMS at that time ;-)).

My recommendation is to use the term “asynchronous” if you use asynchronous communication technologies like messaging. And “synchronous” if you use synchronous communication technologies like blocking REST calls.

If you need synchronous behavior from a business perspective, but implement that with asynchronous technology, you will most probably leverage a synchronous facade, which makes a lot of sense, but please call it so.

By the way — did I point out that asynchronous communication often leads to requirements around long running behavior, which is why it makes sense to use a workflow engine in these scenarios? No? Well — next time :-)

#### Q: How is microservice orchestration better than point to point connections between microservices? Can you explain this via a metaphor?
Of course. Actually I thought I did already:

> 

For more information on orchestration and choreography I can recommend my upcoming book [Practical Process Automation](https://processautomationbook.com/).

#### Q: Doesn’t the proliferation of microservices increase the security attack surface?
I think that this question relates microservices to a monolithic architecture with regards to security, which is a really interesting question that would need its own blog post to answer. The super short answer is: it depends :-) Yes, I think this is true for many cases, but I also know situations, where the monolithic system was such a mess, that a bunch of smaller and focused microservices increased the security level.

#### Q: Is it fair to say event-driven communication drives microservices, and each microservice would use command-driven orchestrations to execute commands?
I would not agree to this way of phrasing it. Microservices can react to events. So these events can drive action (event-driven), yes. But microservices might also offer a command-based API, and then they need to obey these commands, which also drives action, this time by command-driven communication.

And such an action in one of the microservices might lead to further events being emitted, or also to new commands being sent to other microservices. The latter being orchestration.

Now you can argue that every action within a microservice happens, because something else happened, so some event occurred. That means that there needs to be a translation between the event that just happened and the command that should happen because of it. The interesting observation now is that this translation can either happen within the microservice executing the command (event-driven communication) or a microservice sending the command (command-driven communication) as visualized below.

![](https://cdn-images-1.medium.com/max/800/0*0tHWzYUt3ZLOeVZ8)

![](https://cdn-images-1.medium.com/max/800/0*Uz_BiFIKOj87HWd0)

You might also have a separate service doing that translation, in my typical order fulfillment example, this might be an order fulfillment microservice.

![](https://cdn-images-1.medium.com/max/800/0*4Dh5_fGS5eZDzRgt)

You can find an in-depth discussion of this topic in chapter eight of my upcoming book [Practical Process Automation](https://processautomationbook.com/).

#### Q: How to decide between commands and events?
With an event, the decision to act on the event is within the receiver. Exaggerating a bit, the sender should not care what happens, because it has sent an event.

With a command, the sender decides that something needs to happen, the receiver has no choice and has to execute the command (even though it could reject it — it can’t ignore it).

So you should ask which component (also meaning which team) is responsible for ensuring something really happens. Another way to think about this is to ask which team your CIO would approach if a process got stuck. For example, when doing order fulfillment, there might be an own component responsible for sending customer notifications. They react to events. That means, the order fulfillment team is not at all responsible for these notifications. If emails are missing, it is not the problem of the order fulfillment team, the CIO will approach the notifications team.

This might be different if you have notifications that are required by law. The order fulfillment team might be responsible (and held accountable) for that notification in this case, which motivates using commands.

In chapter eight of my upcoming book [Practical Process Automation](https://processautomationbook.com/) I used the following example to discuss this question further:

![](https://cdn-images-1.medium.com/max/800/0*sEwFy4YaCyyfGR1R)

As you see, the customer onboarding team is responsible for the following activities, that’s why they leverage command-driven communication:

- The address being checked at the right moment in time, so that there cannot be customers with invalid addresses onboarded.
- The credit being checked at the right moment in time, so that there cannot be customers with a high risk of not paying their bills.
- Sending a legally important welcome letter.

But the team is not responsible for some other activities, which is why they trust that the domain event they emit will be sufficient:

- The onboarding email notification to the customer, which is taken care of by an own notification service.
- Registering new customers in the loyalty points program.

To summarize, in the first three use cases you would use commands (check address, check credit, send welcome letter), and the latter two would react to events (customer created).

I also discussed this question in my talk “[Opportunities and Pitfalls of Event-driven Utopia](https://berndruecker.io/opportunities-and-pitfalls-of-event-driven-utopia/)”.

#### Q: What is the general strategy to get an end-to-end view on the process, with each microservice maintaining its own database?
This is a very interesting question and you can find some answers in the blog post “[Gaining visibility into processes spanning multiple microservices](https://blog.bernd-ruecker.com/gaining-visibility-into-processes-spanning-multiple-microservices-a1fc751c4c13)”. A more recent version also made it into chapter 11 of my upcoming book [Practical Process Automation](https://processautomationbook.com/).

#### Q: What kind of information or data should be persisted in the orchestrator database?
An orchestrator or workflow engine is not a database, so the rule of thumb is to store as little data as possible within the workflow engine’s context. That normally means just storing references (IDs) there. But of course, there are valid exceptions, they are summarized in the “[Handling Data in Processes Best Practice](https://camunda.com/best-practices/handling-data-in-processes/)”.

#### Q: How would you handle transactions and especially rollbacks that span multiple microservices?
In microservices architectures, you have technical transactions available only within one microservice. Technical transactions cannot span multiple services. To rollback a business transaction with more than one microservice involved, you typically apply the Saga pattern, a great topic I wrote about in “[Saga: How to implement complex business transactions without two phase commit](https://blog.bernd-ruecker.com/saga-how-to-implement-complex-business-transactions-without-two-phase-commit-e00aa41a1b1b)” or talked about in “[Lost in transaction? Strategies to manage consistency in distributed systems](https://berndruecker.io/lost-in-transaction/)”.

I wrote about this topic in chapter nine of my upcoming book [Practical Process Automation](https://processautomationbook.com/).

#### Q: Some people recommend not using synchronous communication at all, but use asynchronous communication instead. But for fast tasks, REST seems fine to me and a broker just adds overhead and a point of failure. Can you comment?
This is actually a very valid question. And while I agree that from a theoretical point of view asynchronous communication is preferable, it indeed has a couple of typical drawbacks in practice:

- **Overhead**: You need additional infrastructure that needs to be evaluated, installed, operated, maintained and understood. This is not always an easy undertaking, especially as messaging infrastructure is typically centralized. On the other hand, if you operate in the cloud, you might simply leverage a managed service, and bigger organizations might already have something in place. But I do agree that this should not be introduced lightly (a reason why some of our customers even [use Camunda as an asynchronous communication tool between services](https://blog.bernd-ruecker.com/the-microservice-workflow-automation-cheat-sheet-fc0a80dc25aa#d96e)).
- **Team knowledge:** Your engineers must have some experience with messaging systems, which to be frank, is rarely the case. This can become a huge bottleneck and might actually make it easier to apply synchronous communication.Look at applying the right patterns to mitigate problems.
- **Maturity of the tools:** It is 2021 and the tooling around messaging systems is still not yet great. You will need capabilities to look into the health of the broker and the queues, but also to sort out problems with single messages, like dead or faulty messages. So far I still see companies building their own “message hospitals”, which is at least an effort you need to be able to invest in.

So long answer short: Practically speaking it can be really a better idea to just go with synchronous communication if this is what you and your team is experienced with.

### Camunda-specific Questions

#### Q: Can we see some Java code, for example to send events to Camunda?
Of course, let’s just point you to:

- Camunda Platform docs around BPMN Message events: [https://docs.camunda.org/manual/latest/reference/bpmn20/events/message-events/#using-the-runtime-service-s-correlation-methods](https://docs.camunda.org/manual/latest/reference/bpmn20/events/message-events/#using-the-runtime-service-s-correlation-methods)
- Some Java Spring Boot code that correlates Kafka records with events to Camunda Platform: [https://github.com/berndruecker/flowing-retail/blob/master/kafka/java/order-camunda/src/main/java/io/flowing/retail/order/messages/MessageListener.java#L47](https://github.com/berndruecker/flowing-retail/blob/master/kafka/java/order-camunda/src/main/java/io/flowing/retail/order/messages/MessageListener.java#L47)
- Something similar for RabbitMQ: [https://github.com/berndruecker/camunda-spring-boot-amqp-microservice-cloud-example/blob/master/src/main/java/com/camunda/demo/springboot/adapter/AmqpReceiver.java#L40](https://github.com/berndruecker/camunda-spring-boot-amqp-microservice-cloud-example/blob/master/src/main/java/com/camunda/demo/springboot/adapter/AmqpReceiver.java#L40)

#### Q: Is asynchronous event-driven communication via Camunda processes and job workers a good idea? We challenged it against AWS SNS/SQS, GCP Pub/Sub and RabbitMQ and Camunda won.
In “[The Microservices Workflow Automation Cheat Sheet — Work distribution by workflow engine](https://blog.bernd-ruecker.com/the-microservice-workflow-automation-cheat-sheet-fc0a80dc25aa#d96e)” I wrote about this architecture, where you use the workflow engine itself to distribute work amongst various services.

I find this a very valid architecture, but you need to be aware of the fact that this makes Camunda a central piece in your infrastructure. I am not at all concerned about this and I know Camunda can handle this very well. Still, it is the typical discussion when proposing this kind of architecture.

It sounds as you did a proper evaluation and have good reasons to choose this architecture — so go for it!

#### Q: Does Camunda provide a Kafka connector?
There is community effort around a [Kafka connector for Camunda Cloud](https://github.com/zeebe-io/kafka-connect-zeebe) but nothing comparable for Camunda Platform. While this is regularly discussed, at the moment it is simply too easy to set up Camunda Platform + Kafka if you leverage Spring Boot to justify an own connector. But I’m happy to be proven wrong!

#### Q: What are best practices to develop microservices with Camunda and Kafka for scalable deployments on Openshift?
This is a quite open question, so it’s too hard to give general advice. Happy to give a better answer if the question is refined. Otherwise I would say: Yes :-)

#### Q: Camunda Platform vs Camunda Cloud (Zeebe)?
Camunda offers two different products:

- **Camunda Platform **is our **on-prem **process automation platform (you might also know it as Camunda BPM). It is an embeddable Java Library most often leveraged in Java Spring Boot environments, but can also be used via Camunda Run and REST in polyglot environments. This is what most people refer to when they say “Camunda”.
- **Camunda Cloud (and its underlying workflow engine Zeebe) **delivers process automation as a service . This is ideal for cloud-native environments and for anyone who requires a **managed service**. It can be used in polyglot environments having different programming language clients available. Camunda Cloud was recently explained in a bit more depth [in this blog post, especially the FAQ at the end](https://camunda.com/blog/2021/03/the-zeebe-community-welcomes-camunda-cloud/).

#### Q: Can Camunda Cloud (Zeebe) be used to implement the SAGA pattern similar to how we can do this with Camunda Platform?
As both products provide a BPMN workflow engine, both can basically [implement the Saga pattern](https://blog.bernd-ruecker.com/saga-how-to-implement-complex-business-transactions-without-two-phase-commit-e00aa41a1b1b). BUT: Camunda Cloud does not yet support BPMN compensation events (see Camunda Cloud’s[ BPMN coverage map](https://docs.camunda.io/docs/reference/bpmn-workflows/bpmn-coverage) compared to [Camunda Platform’s BPMN coverage map](https://docs.camunda.org/manual/latest/reference/bpmn20/)), which makes it harder to apply the pattern. The good news is that it is on the roadmap for Camunda Cloud. So, as of today, it is a bit easier to implement Sagas with Camunda Platform than Camunda Cloud.

#### Q: Can we handle millions of processes using a remote Camunda Engine in a Microservices environment?
Yes. In the details it always depends, but so far we haven’t seen a use case we couldn’t tackle with Camunda. The recommendation is always to measure before you guess, a good resource to also look into is the [best practice around performance tuning](https://camunda.com/best-practices/performance-tuning-camunda/). And don’t hesitate [to contact us](https://camunda.com/contact/) if you have any doubts or need help.

#### Q: Do performance measuring methodologies differ when using microservices? Given we introduce asynchronicity in communication and scale on demand (topology change).
I am not really an expert in this topic, but due to my observation it does conceptually not change too much, but gets more complicated the more components are involved to implement any end-to-end functionality.

#### Q: Can we use event sourcing frameworks like AxonIQ together with Camunda?
I am glad you asked — and yes, you can: [https://github.com/plexiti/axon-camunda-poc/](https://github.com/plexiti/axon-camunda-poc/)

#### Q: Is there a slack community for Camunda?
The [Camunda Platform community meets in its forum](https://forum.camunda.org/), so there is no dedicated Slack channel available. That’s said, there is a[ Slack channel dedicated to the Camunda Cloud community](https://docs.camunda.io/docs/product-manuals/zeebe/open-source/get-help-get-involved/#public-slack-group). Note that interesting slack discussions are copied to the [Camunda Cloud forum](https://forum.camunda.io/) by the awesome [Slack Archivist](https://camunda.com/blog/2020/12/slack-archivist/) — so you are also safe by “just” following the forum.

And let me take this opportunity to advertise that we just recently launched the [Camunda Community Hub](https://github.com/camunda-community-hub) — a GitHub Organization that serves as a single place to find Camunda open source community extensions. It’s a community of maintainers and Camunda employees that provides peer support, automation, and discoverability. The hub also provides best practices and learning resources for maintainers.
