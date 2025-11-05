---
layout: post
title: "Architecture options to run a workflow engine"
date: 2017-12-19 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "architecture"]
author: Bernd Ruecker
excerpt: "> “We do composite services, orchestrating two or three CRUD-Services to do something more useful. Our architects want to use your workflow engine for this b..."
---
### Architecture options to run a workflow engine
This week a customer called and asked (translated into my own words and shortened):

> “We do composite services, orchestrating two or three CRUD-Services to do something more useful. Our architects want to use your workflow engine for this because the orchestration flow might be long running. Is this a valid scenario for workflow? Currently we run one big central cluster for the workflow engine — won’t this get a mess?”

These are valid questions which recently we get asked a lot, especially in the context of [microservices](https://martinfowler.com/articles/microservices.html), modern SOA initiatives or [domain-driven design](https://www.amazon.com/Domain-Driven-Design-Tackling-Complexity-Software/dp/0321125215).

Modern workflow engines are incredibly flexible. In this blog post I will look at possible architectures using them. To illustrate these architecture I use the open source products my company provides([Camunda ](https://camunda.org/)and [Zeebe.io](http://zeebe.io/)) as I know them best and saw them “in the wild“ at hundreds if not thousands of companies. But these thoughts are applicable to comparable tools on the market as well.

### Aspects to consider

#### Central or de-central workflow engine
For over a decade, workflow engines (or BPM tools) were positioned as being something central. Very often this was the only way of running complex beasts that required challenging setups. Most vendors even cemented this approach into their licensing model which meant, not only was it horribly expensive but also tied to CPU cores.

![](https://cdn-images-1.medium.com/max/800/1*uyhORZTuV_LOu0TJPbwz_g.png)

This is not true any more with modern tools which are very flexible.

In [Avoiding the “BPM monolith” when using bounded contexts](https://blog.bernd-ruecker.com/avoiding-the-bpm-monolith-when-using-bounded-contexts-d86be6308d8) I show that in an architecture which cleanly separated responsibilities you should run multiple engines. And all modern trends like DDD or Microservices call for de-central components to allow more agility and autonomy which is about getting changes into production as fast as possible which will be the key capabilities of successful enterprises in future.

So in the above scenario I would run an **own workflow engine for every service **that needs long running behavior.

![](https://cdn-images-1.medium.com/max/800/1*8Pb5rg_e1WUalgzf3C6UUw.png)

Advantages:

- **Autonomy**: Every team that builds a service can decide on the best solution themselves. They can decide on the concrete tool to use (even if most customers limit the diversity to avoid complexity). They can update the engines version whenever they like. They can redeploy things or plan outages without the need of any coordination with other teams.
- **Isolation/Scalability**: Every service has a dedicated engine. This can be scaled depending on the concrete requirements. Workflows from other teams cannot harm your performance or stability.
- **Isolation/Security**: Team A cannot read or mess with data from Team B. If you share one engine you always meet in the data store. In Camunda we trust JDBC connections or Java clients therefore it’s hard to avoid any friendly attacks or accidental changes by operators.
- **Isolation/DevOps**: The local operating tool ([Cockpit ](https://camunda.com/products/cockpit/)when using Camunda) is 100% focused on the workflows the DevOps team is really responsible for. No other data is shown so people don’t get confused or distracted. This also makes it easier to give the team full access to Cockpit allowing for a true DevOps mindset which is essential to start a good continuous improvement cycle.

But of course there are also **drawbacks**:

- **Operating the engine**: You have to install, run and maintain multiple engines. Depending on the concrete deployment scenario (see below) this can be cumbersome but also every simple. This problem can be addressed by automated deployment pipelines.
- **Operating the workflows**: You will have multiple operating tools showing only the local processes. If you have end-to-end processes stretch across the boundaries of multiple services you don’t get a central view on all of them for free.

To get a proper overview our customers typically setup monitoring solutions collecting events from all engines. The central monitoring doesn’t show too many details but provides links to the right operating tool. You could e.g. use the [Elastic stack ](https://www.elastic.co/)for this. And [simple workflow engine plugins](https://docs.camunda.org/manual/latest/user-guide/process-engine/history/#provide-a-custom-history-backend) can generically push events within a workflow to the central tool. This is comparable to what [Camunda Optimize](https://camunda.com/products/optimize/) does, it collects data from many engines and makes them available for central analyzing (but with the goal of business analysis and improvement, not technical operations).

![](https://cdn-images-1.medium.com/max/800/1*6XXREZRFBLBzGqUwC-xAvQ.png)

However, there is one interesting hybrid possible, at least in the Camunda world: Running multiple engines on top of a shared database.

![](https://cdn-images-1.medium.com/max/800/1*04iJ1A0noLGwAtXFyZPw6g.png)

This is actually what a reasonable amount of customers do and it’s not only a valid approach but often a good compromise, especially because Camunda has two features supporting it:

- [Deployment aware engines](https://docs.camunda.org/manual/latest/user-guide/process-engine/the-job-executor/#job-execution-in-heterogeneous-clusters): Every engine knows the workflow definitions owned by the current service and only touches them (just make sure you configured your engine to act deployment aware).
- [Rolling updates](https://docs.camunda.org/manual/latest/update/rolling-update/): Camunda guarantees that two subsequent versions can run on the same database. Assume you run 7.7 and want to upgrade the engine to 7.8. You can first update the database to 7.8, services can keep running on their engine in 7.7! They can now eventually update as soon as they like. You are just blocked to migrate to 7.9 before all services have migrated to 7.8.

#### Orchestration in service or infrastructure
Some customers see the workflow engine as an infrastructure component, living on its own and not being part of a specific service.

![](https://cdn-images-1.medium.com/max/800/1*xvFSciUczQXgcuqswD74cA.png)

This is very much the view of a BPM or ESB-like component of the first wave of SOA projects, it is a central engine as described above. We see a lot of customers in this scenario use workflows as rather small integration flows, replacing an ESB. This is technically feasible but it poses** questions around the ownership and life-cycle of the workflow models**. I dedicate a section especially to this very important aspect. It is a valid approach if you sort out these questions even if a lot of customers turn away from this view because of bad experiences back in the old SOA days.

**Orchestrating serverless functions**

A very related use case in modern architectures is the orchestration of stateless and rather small functions. Using these functions (e.g. [Azure Functions](https://azure.microsoft.com/en-us/services/functions/), [AWS Lambda](https://aws.amazon.com/lambda/) or similar) you need a way to implement end-to-end business capabilities which often stretch across multiple functions. This is a perfect fit for a workflow engine. I published details of a recent customer project in the Azure Cloud in [Orchestrating Azure Functions using BPMN and Camunda — a case study](https://blog.bernd-ruecker.com/orchestrating-azure-functions-using-bpmn-and-camunda-a-case-study-ff71264cfad6), this is how the high-level architecture looks like:

![](https://cdn-images-1.medium.com/max/800/1*XVAlXSfu1z4TC0arsOcHhQ.png)

#### Ownership and life-cycle of workflow models
Every workflow model needs a clear owner which can decide not only for changes, but is also responsible that the workflow runs smoothly. In Microservices architectures (or similar) this ownership is typically given to teams building the service. In this scenario ownership for services is baked into the organization already. This makes it quite natural to make workflow models part of the service. If you have workflow models as own artifacts — as e.g. in the serverless example above — it is a bit harder but not less important to define a proper ownership.

Additionally you have to clarify for every workflow model when and how it gets deployed. Very often workflow models have a strong relationship to a certain domain meaning that you have to deploy them in-sync with the corresponding service (or function). This is easier to do if they are logically part of the service. If it’s a separated artifact with a separated owner it means you have to coordinate between teams which slows you down. And if it’s not really separated, why it’s not part of your service?

Once again there is not only a black and white approach. A hybrid solution is to run a centralized engine but to distribute ownership and responsibility for deployment of the workflow models to the services.

![](https://cdn-images-1.medium.com/max/800/1*07QDmrUqhwJweeiWJU8lmg.png)

This is a common scenario for **non-Java environments using Camunda**. It mitigates the biggest problems arising from a central engine or seeing workflow engines as infrastructure. It is a valid approach. If you are developing Java services, it might be a bigger overhead than running the engine within your service itself. But to understand this let’s turn our attention on how to run a workflow engine at all.

#### Engine as separate program, possibly remote
Typically a workflow engine runs as a separate application allowing you to connect remotely, e.g. via REST in Camunda or Message Pack in Zeebe. This allows you to run the engine on a different host but of course it can also run just beside the service itself.

![](https://cdn-images-1.medium.com/max/800/1*GG1rZId7pCd_yJok0Z09Dw.png)

A code example can be found here: [Use Camunda without touching Java and get an easy-to-use REST-based orchestration and workflow engine](https://blog.bernd-ruecker.com/use-camunda-without-touching-java-and-get-an-easy-to-use-rest-based-orchestration-and-workflow-7bdf25ac198e).

Forces:

- Good separation of concerns as the workflow engine lives in an isolated process and can be controlled independent of your own code. This makes it easier to hunt down failures and isolate components.
- You do not have ACID transactions in this model as the remote channel is not transactional. So you have to think about retrying, idempotency or duplicate calls. I personally do not see this as a disadvantage. We rapidly move towards distributed systems as the new normal and in this world we have to accept that consistency is an illusion (a good read is [Pet Halland: Life beyond distributed transactions](https://cs.brown.edu/courses/cs227/archives/2012/papers/weaker/cidr07p15.pdf)).
- Compared to an embedded engine (see below) you introduce remote communication. This increases complexity especially for unit tests as you have to make sure to run a workflow engine in a defined state only for a certain test case.

By the way, remote communication doesn’t have to feel bad for the developer. For example, Zeebe provides native client libraries (currently for Java and GoLang but more support is planned) which allow to use it like an embedded engine in terms of coding but keep the separation of concerns.

#### In love with Java? Embeddable workflow engines
Camunda is an example of an embeddable Java engine. That means the engine can also run as part of your Java application. In this case you can simply communicate with the engine by Java API and the engine can call Java code whenever it needs to transform data or call services. The following code snippet will work in any Java program and defines a workflow model, deploys it and starts instances (it is really that simple to get started!). You just need Camunda and a H2 database on the classpath:

This scenario is the de-facto standard for JUnit tests. I am very proud of the [Camunda test support](https://docs.camunda.org/manual/latest/user-guide/testing/#junit-4) (also providing [assertions](https://github.com/camunda/camunda-bpm-assert/),[ scenario tests](https://github.com/camunda/camunda-bpm-assert-scenario/) and [visualizations of test runs](https://github.com/camunda/camunda-bpm-process-test-coverage)).

**Spring Boot Starter**

But running an embedded engine is also what you do if you use the [Camunda Spring Boot Starter](https://github.com/camunda/camunda-bpm-spring-boot-starter/) which is a nice way of implementing the *one workflow engine per service* approach described above.

![](https://cdn-images-1.medium.com/max/800/1*YXK6GqsMr8n7V24kqXbNmg.png)

A Camunda Spring Boot code example can be found here: [https://github.com/berndruecker/camunda-spring-boot-amqp-microservice-cloud-example/](https://github.com/berndruecker/camunda-spring-boot-amqp-microservice-cloud-example/)

**Container-managed engine**

Within Camunda we provide something unique: A container-managed engine running as part of [Tomcat or application servers like WildFly or WebSphere](https://docs.camunda.org/manual/latest/introduction/supported-environments/#container-application-server-for-runtime-components-excluding-camunda-cycle). Having this available, you can deploy normal WAR artifacts which can contain workflow models which are automatically deployed to the container-managed engine. This allows for a clean distinction between infrastructure (container) and domain logic (WAR).

![](https://cdn-images-1.medium.com/max/800/1*WjjsR-M3B73S3agD_EQA_g.png)

A code example can be found here: [https://github.com/camunda-consulting/camunda-showcase-insurance-application](https://github.com/camunda-consulting/camunda-showcase-insurance-application).

In the past, it was often advocated to deploy multiple applications (WAR files) onto a single app server. We also [support this model in Camunda](https://docs.camunda.org/manual/latest/introduction/architecture/#shared-container-managed-process-engine). However, we do not see this being applied often any more as it breaks isolation between deployments. If one application goes nuts it can very well affect other applications on this server. And with technologies like Docker it’s super easy to run multiple app servers instead so I also favor the one app per application server approach.

**Spring Boot or container-managed engine?**

Deciding between Spring Boot or an application server is hard as it‘s still a very religious decision. Conceptually I like separating the infrastructure into an application server. And if you are running Docker, the WildFly approach might even allow for faster turnaround times (see below). But it’s also true that the environment around Spring Boot is quite active, pragmatic, stable, innovative and often ahead of Java EE (sorry, [EE4J](https://projects.eclipse.org/projects/ee4j/) of course). My advice would be to base your decision on what works best in your company, either way will be fine. Keep your team and the target runtime environment in mind when doing this selection.

In general, we do not recommend running an embedded Camunda engine and configuring it on your own (e.g. via plain Java or Spring). There are some things you have to do right (e.g. thread pooling, transactions, id generation) especially to guarantee proper behavior under load. So better use the Spring Boot Starter or the container-managed engine.

**Pros and cons of the embeddable engine approach**

Running the engine in the previously mentioned ways is optimal in a Java world as it provides many advantages including:

- easy to use (Java API),
- easy to write proper unit tests,
- clear deployment path for workflow models (as part of the normal application deployment),
- transactional integration,

But of course it has a major drawback:

- It is nailed to Java. If you do not develop your services in Java, this might not be for you.

Our current rule of thumb for Camunda customers is:

- If you do Java then use either Spring Boot (embedded engine) or any container-managed engine (Tomcat, WildFly, Websphere, …).
- If you don’t do Java, run a remote engine and talk to it via REST.

For Camunda there is a funny recursion: When running the workflow engine as a separate application, the Camunda engine itself has to be started up using one of the options already stated above (e.g. container-managed or using Spring Boot). The typical way is to run a container-managed engine on Tomcat, maybe using Docker.

#### Communication pattern
Assume you have a payment workflow that needs to communicate with a remote service to charge a credit card:

![](https://cdn-images-1.medium.com/max/800/1*M2xlXRroWLzE61_pogzEJg.png)

There are two basic patterns to implement this communication:

- **Push**: The workflow actively calls that service. It could be a Java invocation, a REST or SOAP Web-Service call but also sending a message to a queue is kind of an active push from the perspective of the workflow engine.

![](https://cdn-images-1.medium.com/max/800/1*l0mkhsHUyPwnX-wbng8hpg.png)

- **Pull**: A service or some intermediary connector asks the workflow engine for work. This reverses the communication direction. In Camunda this is supported by so called [External Tasks](https://docs.camunda.org/manual/latest/user-guide/process-engine/external-tasks/).

![](https://cdn-images-1.medium.com/max/800/1*xFffKR8lzrk95aNFDqkVrw.png)

Pulling work has a couple of **advantages**:

- The workflow engine does not have to know concrete endpoints (URL, queue names, etc).
- The called service defines the scaling itself depending on how many tasks it consumes and how fast. This might depend on a various factors like the current load, which implements so-called [back-pressure](https://en.wikipedia.org/wiki/Backpressure_routing).
- The called service can be implemented in any language and ask via REST API (or Message Pack in Zeebe).
- The core workflow engine can concentrate on the coordination of the workflow and does not have to handle integration logic and protocols.
- Transaction boundaries are enforced in a way which is natural for distributed systems thereby making the whole system more flexible to adapt to future technologies.

Of course it has **drawbacks**:

- You need to poll for work making the worker code ([Camunda Java worker example](https://github.com/berndruecker/flowing-retail/blob/master/rest/java/payment-camunda/src/main/java/io/flowing/retail/payment/resthacks/worker/CustomerCreditWorker.java), [Camunda Node.JS worker example](https://github.com/berndruecker/flowing-retail/blob/master/rest/java/payment-camunda/node-customer-credit-worker/index.js)) a bit more complex.

We regularly have customers which want to have true temporal decoupling in a way that service unavailability doesn’t cascade. They don’t want to do synchronous calls which needs to be retried when a service is unavailable. But a lot of them shy away from introducing messaging systems as they are an additional component in the stack which are hard to operate. As an alternative they often use the pull model to implement asynchronism and temporal decoupling. This works very well.

Overall we cannot give a general recommendation for the best communication style. It depends on too many factors. If you’re in doubt, we recently tend to favor the *pull approach. *But in the Java space most customers use the *push approach* as it is quite natural and simple to setup — and works very well for most use cases.

#### Running workflow engines in the cloud, on docker, cloud foundry or others
Embedded engines are part of your Java application and can basically run everywhere. Spring Boot packages a so-called uber-jar containing all dependencies. Some people also call this fat-jar as it typically grows around the same size as containers because it contains all dependencies they need (including the webserver, transaction manager, etc). Either way, you can run it on Docker, in Java build packs from CloudFoundry or Heroku or any other environment you like. A code example for Spring Boot and Cloud Foundry can be found here: [https://github.com/berndruecker/camunda-spring-boot-amqp-microservice-cloud-example/](https://github.com/berndruecker/camunda-spring-boot-amqp-microservice-cloud-example/)

With app servers like Tomcat or WildFly you will typically run Docker nowadays. There is one interesting thought on this: Docker works in layers, and new docker images just add new layers on top of existing ones.

![](https://cdn-images-1.medium.com/max/800/1*HtYXMYHGID6JxUB0amOuVA.png)

So you could define a base image for workflow applications and then just add WAR files to this. The WAR files are typically tiny (some kilobytes) allowing for very quick turnaround. Whether this advantage affects you depends on the development workflow in your company.

There are [Docker images available of Camunda](https://github.com/camunda/docker-camunda-bpm-platform) and [Zeebe](https://docs.zeebe.io/introduction/install.html#using-docker) and we provide several examples on how to [build custom Docker images](https://github.com/camunda-consulting/docker). These Docker images can run in basically all cloud environments providing container services.

**Summary**Gee, that was a long blog post. Sorry. I couldn’t stop writing. But I know that this is relevant information for a lot of people who are going to introduce workflow engines in their architecture — or to fix bad decisions from the past.

Modern tools allow for greater flexibility. And this flexibility comes with the burden that you have to make decisions. This post lists the most important aspects to consider and should help you make a well informed decision. Feel free to [approach me](mailto:bernd.ruecker@camunda.com) or the [Camunda team](https://camunda.com/contact/) if you need help in this endeavor. It is really important to think this through as some basic decisions have a huge impact on your architectures future.

#### Bonus: Greenfield stack for Java folks
For everybody who thinks: “I have no clue what this guy is writing about, I just want to get going” we defined a so-called greenfield stack in the Camunda Best Practices (which are reserved for our [Enterprise Customers](https://camunda.com/enterprise/), sorry!). It is not superior to any other stack, it is just the one we recommend if you don’t care. And because you read this till the end, you get it free as a bonus ;-)

We suggest as **runtime environment**:

- Latest version of Camunda, use the [Enterprise Edition](http://camunda.com/bpm/) (with patches),
- Deployed on WildFly (or JBoss EAP if you want to have a supported Enterprise Edition),
- using Java 8 (latest Sun JDK) ,
- as [Container Managed Engine](https://docs.camunda.org/manual/7.7/introduction/architecture/#shared-container-managed-process-engine),
- using [PostgreSQL](http://www.postgresql.org/) — or the database you already operate.
- For High Availability you normally have at least two application servers running, not necessarily forming a cluster but point to the same database.

**For development**:

- Develop [Process Applications](https://docs.camunda.org/manual/7.7/user-guide/process-applications/) as WAR,
- using [Maven](https://maven.apache.org/download.cgi) as build tool,
- using [Eclipse](https://eclipse.org/downloads/) as IDE,
- and modeling with the [Camunda Modeler](https://camunda.org/download/modeler/).

As runtime environment on the **local developer machine** we recommend:

- Same version of Camunda, WildFly and Java (as above)
- H2 database with file back-end as this easily allows every developer to have its own database without the necessity of installing a database. H2 is already contained and configured in our our [pre-packaged WildFly distribution](https://docs.camunda.org/enterprise/download/), which works perfectly for the use case of a “local developer machine”.

We **strongly discourage** that multiple developers share the same database during development as this can lead to a multitude of problems.

As always, I love getting your feedback. Comment below or [send me an email](mailto:mail@bernd-ruecker.com).

*Stay up to date with my *[*newsletter*](http://eepurl.com/cDXPRj)*.*
