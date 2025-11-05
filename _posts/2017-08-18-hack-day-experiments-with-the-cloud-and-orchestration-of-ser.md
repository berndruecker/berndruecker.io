---
layout: post
title: "Hack day experiments with the cloud and orchestration of serverless functions"
date: 2017-08-18 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "orchestration", "spring", "event-driven", "architecture"]
author: Bernd Ruecker
excerpt: "The idea was easy: Let’s do a simple ticket booking example, where we have to do reservation and a ticket creation. The latter might need computing power for..."
---
### Hack day experiments with the cloud and orchestration of serverless functions
During our yearly hack days some colleagues and me decided to get our hands dirty with latest technology trends. We wanted to translate what is easily drawn on a white board into working software in the cloud. We learned a lot —but step by step.

The idea was easy: Let’s do a simple ticket booking example, where we have to do reservation and a ticket creation. The latter might need computing power for the PDF creation, so a scalable serverless function might make sense here. But we also need a ticket creation service, that’s kicks off a flow orchestrating these function to a meaningful overall capability. The plan was quickly drawn in my sketch book:

![](/assets/images/hack-day-experiments-with-the-cloud-and-orchestration-of-ser-1.jpeg)

And we prepared logins for some of the well known cloud providers: AWS, Microsoft Azure, Google Cloud, IBM Bluemix, Pivotal, Heroku and OpenShift.

### Provisioning options
So let’s go. But how? It took us some tries and discussions to figure out, that there are multiple ways to go (apart from just clicking through web consoles manually of course):

#### Command Line Clients
All cloud providers have a command line client, you typically need anyway. This allows you to communicate in a repeatable manner and easily write shell scripts or batch files. This is quick as the cloud provider tutorials highlight this way. But often it is low level and cumbersome — and different for every cloud. So let’s look for an abstraction!

#### Terraform & Ansible
[Terraform ](https://www.terraform.io/)allows to write configuration files to provision your infrastructure on various cloud platform. The configuration can be checked into Git and automatically applied. Sounds great!

But wait, you still define your infrastructure on a very low level. Basically you say “Get me an AWS EC instance with this base image” and “get me a lambda function” and “get me this API gateway with some special configuration”. So it avoids clicking through the web consoles or use the AWS command line client, but if you want to just say “run me this docker image” it does not help you. This is especially true for AWS which makes it quite hard to run docker images at the moment.

We asked our DevOps guys and they recommended [Ansible ](https://www.ansible.com/)to configure the EC2 instance (which was created by terraform) to be able to run docker. While this is again nice, because you can write configuration files you can version control, you still have to get your hands dirty on low level details — all we want is to run a docker image!

So after another hint from our DevOps team we drew attention to another approach:

#### Docker & Kubernetes
[Kubernetes ](https://kubernetes.io/)allows a better abstraction, as you can directly run docker images, by defining “deployments” in[ simple YAML files](https://github.com/bullshit-bingo/zeebe-kubernetes/blob/master/broker-deployment.yaml). It was no problem to use images from a [private docker registry](https://kubernetes.io/docs/tasks/configure-pod-container/pull-image-private-registry/). I really like this:

Now there are multiple cloud providers supporting Kubernetes, most prominently Google Cloud and Azure, but also Bluemix and probably others. I tried Azure and Google and could easily provision my services on both and assign a public IP to my services with ease. Kubernetes does all the dirty details about machines, resilience, auto-scaling, load-balancing and so on. Nice:

![](/assets/images/hack-day-experiments-with-the-cloud-and-orchestration-of-ser-2.png)

We now used Docker and Kubernetes to run the orchestration engines for our ticket flow — as described in more details below. But now let’s do some real business logic — as serverless functions:

### Serverless business logic
I used Azure Functions and made use of the [command line client to do so](https://github.com/bullshit-bingo/azure-function-csharp-cli). Functions can be written in different languages, e.g. C#. Using Camunda for orchestration I leveraged [this Camunda C# Command Line client](https://github.com/berndruecker/camunda-dot-net-showcase) which I [added to the function as dll](https://github.com/bullshit-bingo/azure-function-csharp-cli/tree/master/CreateTicket/bin). The function gets pretty simple, this example starts a new flow instance in Camunda:

Now I created 2 more functions for our little example:

![](/assets/images/hack-day-experiments-with-the-cloud-and-orchestration-of-ser-3.png)

Using Camunda in this scenario, the [reservation worker now has to poll for work via REST API,](https://blog.bernd-ruecker.com/use-camunda-without-touching-java-and-get-an-easy-to-use-rest-based-orchestration-and-workflow-7bdf25ac198e) which is called [**External Tasks**](https://blog.camunda.org/post/2015/11/external-tasks/). Using Zeebe this could be different as you can also open streams to get notified of work. But let’s keep with Camunda for the moment, which means I [created a scheduled function](https://github.com/bullshit-bingo/azure-function-csharp-cli/blob/master/ReservationWorker/function.json). Fine. But I have to define a scheduling interval, like every second or every minute. While this is fast enough for most use cases, we envisioned for our example that a customer might still wait at his browser for a PDF to be generated. So we want to be faster — let’s finish in milliseconds!

There are multiple options to go, but the easiest is to give our workers a hint, that there is new work to do. You can use messaging or eventing here, so e.g. leverage Azure Event Grid, AWS Kinesis or whatsoever. You could now create a Camunda plugin to emit a new event every time a new task is created (this is low hanging fruit actually, comparable to [this example](https://github.com/berndruecker/camunda-spring-boot-amqp-microservice-cloud-example/tree/master/src/main/java/com/camunda/demo/springboot/conf/plugin)). For the first prototype I took an easier approach: My azure functions emit events on the Azure Event Hub when finishing, and this triggers all workers. It basically just is a “something has happened, might be worth checking for tasks now” hint. But this allowed me to reduce the scheduled polling interval, which I wanted to keep as a safety net (e.g. for retrying failed tasks). Unfortunately a Azure function can only have one trigger, so I needed to create two functions, one scheduled and one event driven one. But maybe I missed a better approach of doing this in the heat of the moment. My Camunda flows finishes very fast now:

![](/assets/images/hack-day-experiments-with-the-cloud-and-orchestration-of-ser-4.png)

Yeah.

And what I really liked when doing the exercise is the Azure web environment, where you can see all resources clearly arranged and easily develop and change your function and re-test it:

![](/assets/images/hack-day-experiments-with-the-cloud-and-orchestration-of-ser-5.png)

![](/assets/images/hack-day-experiments-with-the-cloud-and-orchestration-of-ser-6.png)

![](/assets/images/hack-day-experiments-with-the-cloud-and-orchestration-of-ser-7.png)

You can basically do the same with AWS, but the web UI and docs are not that nice there. To emphasis the difference: When I had the whole Camunda flow running on Azure a colleague screamed: “yeah, finally I have my AWS Gateway configured in a way, that it passes my query parameters and I can access my first Lambda and doesn’t time out!”. A single function. The issues faced might not make a huge difference in the long run, but definitely screw up the get started experience with AWS.

We also wanted to leverage the [Serverless framework](https://serverless.com/), which I think is a very good idea to do when doing serverless. Unfortunately I struggled to install it on my windows 10 machine and finally gave up when experiencing node compilation errors with the azure plugin. So we could only use it with AWS like in[ this example](https://github.com/bullshit-bingo/aws-lambda-deploy-function-serverless-framework).

### Serverless? Use cases and architecture
At some point we met in the nice garden outside and discussed about sense and nonsense of what we were doing. We posed the following question:

> Do serverless functions really help us in our endeavor?

In the end the functions boil down to a docker image anyway. So wouldn’t it be easier to directly **build and run **the docker images by Kubernetes? That would ease the build, versioning and deployment and also harmonize the workflow over different cloud platforms (because serverless is slightly different everywhere).

Personally I also think, that even docker is still a bit too low level, as I have to care about the Linux of the docker container and so on. I would love to start a level higher — like serverless functions, but more corse grained. I really like the way Cloud Foundry or Heroku give you language specific build packs, like e.g. the [Java Buildpack](https://docs.cloudfoundry.org/buildpacks/). I quickly drew the following picture to better explain what I mean:

![](/assets/images/hack-day-experiments-with-the-cloud-and-orchestration-of-ser-8.png)

In Java this means you can build an Uber-Jar and deploy and run that, without caring about anything underneath. If you combine this with Spring Boot and Spring Cloud it is a very powerful approach, which I definitely like more than anything I have done so far on these hack days. You can also choose your granularity here, so you are not limited to define one function as deployment, but can also combine a set of functions as one deployment unit, which I think is often easier to handle.

**Granularity **brings us to an important thought: We have ignored the use case for serverless in our example. Typical examples of serverless functions are:

- You drop some binary file on a storage (S3, Azure Blob Storage, …) which triggers a function (e.g. video transcoding) and stores the result on another storage. The work done is computing intensive.
- You deliver some specific web content in a highly scalable manner. For example BPMN diagram rendering in the cloud tool [Cawemo](https://cawemo.com/) is done using AWS Lambda functions.

[To quote Kiriaty](https://thenewstack.io/serverless-computing-use-cases-image-processing-social-cognition/):

> Image processing and content processing and doing any content manipulation in the background is very common. Storing an image and then resizing it, putting it back into storage is a good example

**Typical business applications should not be disassembled into a huge number of fine grained serverless functions**. You should only carefully select use cases for it, like e.g. the ticket generation in our case. But most parts of your system is still “normal” development within [bounded contexts](https://martinfowler.com/bliki/BoundedContext.html), which are more coarse grained. The resulting *microservices* or *self contained systems* can still be deployed to the cloud in a way, that you don’t have to think about low level details too much. Spring Cloud combined with [Cloud Foundry and language specific build packs](https://docs.cloudfoundry.org/buildpacks/) (e.g. run on Pivotal Web Services) is a great example of this. You can also run [C# application on Cloud Foundry as shown in this example](https://github.com/cloudfoundry-incubator/NET-sample-app). But of course Docker on Kubernetes is also a way to go, but I like the level of abstraction of Cloud Foundry very much.

In any way, keep an eye on granularity and appropriate use cases:

![](https://cdn-images-1.medium.com/max/800/1*qwWwWMpTvlv0hIwDnER7aQ.png)

And of course, just because we wanted to play around with latest technology doesn’t mean that we would apply it in each and every project. I still like to have the [monolith first pattern](https://martinfowler.com/bliki/MonolithFirst.html) in mind.

### Orchestration options
But now lets get to the important things: How to orchestrate the serverless functions (or microservices). Of course, this was one of my focus points and I wanted to try several options. We started with the [new kid on the block](https://jaxenter.com/zeebe-brings-open-source-order-microservice-orchestration-136261.html):

#### Zeebe
[Zeebe ](https://zeebe.io/)is a very young open source project and it is quite promising. It just had its first public release last week (0.1.0) and it had one not yet implemented feature that we missed in our cloud environment: It [doesn’t yet support advertised endpoints](https://github.com/zeebe-io/zeebe/issues/298). Some background, so that you know what I am talking about: The Zeebe broker knows the current cluster topology and hands it over to any connecting client. The client now does client side load balancing. If you run Zeebe in a Kubernetes cluster, you can talk to it from inside this cluster, but not from the outside (the internet), as the Zeebe broker is not yet aware of the external endpoint / public IP. There are some workarounds, but they need some additional work. We simply provisioned Zeebe on an AWS EC2 instance ourselves and manually configured the public DNS name in the zeebe config. That worked. Using a [simple monitor I recently hacked together,](https://github.com/camunda-consulting/zeebe-simple-monitor) you can see that working:

![](https://cdn-images-1.medium.com/max/800/1*JqBM6IYQaItg2a9OSjA3iw.png)

Another hack days team created a Zeebe resource for Kubernetes, which even allows you to just define a “zeebe-broker” resource and use auto-scaling, then you do not even have to think about Docker. Cool stuff ahead.

#### Camunda
[Camunda BPM](https://camunda.org) is proven and mature. It is easy to [start via docker](https://blog.bernd-ruecker.com/use-camunda-without-touching-java-and-get-an-easy-to-use-rest-based-orchestration-and-workflow-7bdf25ac198e) and therefor could be [easily provisioned using Kubernetes](https://github.com/bullshit-bingo/camunda-kubernetes). Afterwards I connect via REST and start the web tools to see what is going on. Yeah —that was easy — so I used this instance in my further experiments described above.

![](https://cdn-images-1.medium.com/max/800/1*C6MTYvlo4TfF8Zg9dXGcyA.png)

#### Azure Logic Apps
Of course I also wanted to use the Azure native orchestration tool, called [logic apps](https://azure.microsoft.com/de-de/services/logic-apps/). I could click together a new flow and design the data mappings via browser wizards. I connected Azure functions to the steps, but this time actively called via REST (Push) — not like Camunda where tasks where queried via REST (Pull). But you could use messaging via Event Hub or the like.

![](https://cdn-images-1.medium.com/max/800/1*hVvA0ImCo2IkEcrUde_Qow.png)

Thing is: While this was kind of easy for this simple example, I would not use it for more complex processes. The whole process of designing the flow is [zero code — and I don’t believe that this is a efficient way of developing flows](http://www.bpm-guide.de/2014/06/12/neues-whitepaper-der-mythos-zero-coding-bpm/?lang_pref=en). I have no idea how to test this flow (which is very [cool in Camunda](https://github.com/berndruecker/camunda-spring-boot-amqp-microservice-cloud-example/blob/master/src/test/java/com/camunda/demo/springboot/OrderProcessTest.java)). As far as I saw, logic apps also misses a lot of concepts in the area of error handling or compensation. So I have no idea how to handle situations where thousands of flow instances go nuts and need to be repaired, which is doable in Camunda properly. I also have no idea how to properly [implement a Saga pattern](https://blog.bernd-ruecker.com/saga-how-to-implement-complex-business-transactions-without-two-phase-commit-e00aa41a1b1b) here.

The use case for logic apps, which is also available branded as [Microsoft Flow](https://flow.microsoft.com/), is in my opinion well — but also only — suited for simple [IFTTT ](https://ifttt.com/)like logic.

#### Netflix Conductor
Netflix conductor was released last year for microservice orchestration within Netflix. As Conductor is kind of competition, I want to be careful with judging it. But my get starting experience was honestly horrible. I followed the get started guide from the docs but did not succeed in any of the described ways:

- Building from source: Gradle exited with an error message which I think was caused by a incompatibility between my Gradle version and a Netflix Plugin (nebula.netflixoss). So I skipped this way.
- Docker: There was no pre-built image available on docker hub and I could not build it myself, as the jar build above did not work. Dead end.
- I wanted to download the `conductor-server-all` jar as mentioned in the docs, but that is not available on Maven Central. I downloaded the conducter-server instead, but this is a war, not a jar. A simple deploy on a vanilla Tomcat threw exceptions — so this also did not work as I don’t know how to deploy that war.

I now tried to use [this third-party docker image](https://hub.docker.com/r/devopsopen/docker-netflix-conductor/) to get going. It did not run locally, basically the server did not come up. I also could not [run it via Kubernetes,](https://github.com/bullshit-bingo/netflix-conductor-kubernetes) same problem, no server on port 8080. Might be a problem that the docker image uses conductor from the master branch and not a stable tag. Additionally the web connection on our remote hack days location was not so good, so after downloading some gigs for several tries I had to give up.

I will give it another try some other time. For now I can only note that you really recognize that Netflix doesn’t focus on distribution of Conductor — which is understandable as Netflix is not a product vendor, nor is Conductor productized in any way. So use at your own risk.

### Summary
48 hours are not enough to explore all latest technologies. But we could run quite a lot and that helped us massively to understand the technologies better including the typical pains and gains. A lot of tooling is in quite early stages, so no need to hurry to jump on every train. Docker and Kubernetes are on a good way to proper commodity, so this is definitely cool to use. The cloud provider underneath might be a matter of taste or depend on legal or sales issues more than technical reasons. As a gut feeling I liked Azure most, as they provide a very good user interface. On the other hand I am a big fan of the Spring Cloud approach running on Pivotal. But that is Cloud Foundry which is also available on other platforms, including Azure. But haven’t yet played with this.

Orchestration is important to keep your functions or microservices fulfilling real business capabilities — and keep an overview on that. Camunda BPM is a proven choice and works well in the cloud, so it is the best way to go at the moment. But keep an eye on Zeebe.

Overall it was fun. But I look forward to write some Java code instead of YAML again.

As always, I love getting your feedback. Comment below or [send me an email](mailto:mail@bernd-ruecker.com).

*Stay up to date with my *[*newsletter*](http://eepurl.com/cDXPRj)*.*
