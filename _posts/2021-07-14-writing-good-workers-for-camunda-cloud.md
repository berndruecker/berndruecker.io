---
layout: post
title: "Writing Good Workers For Camunda Cloud"
date: 2021-07-14 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "spring", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "Therefore, this post will look into these topics:"
---
### Writing Good Workers For Camunda Cloud

#### Camunda Cloud Architecture Blog Post Series — Part 3
Part one of this blog post series ([Connecting The Workflow Engine With Your World](https://blog.bernd-ruecker.com/drafting-your-camunda-cloud-architecture-connecting-the-workflow-engine-with-your-world-3d94e8d404d6)) described that custom glue code is often used to invoke services (or to write customized logic). [Service tasks](https://docs.camunda.io/docs/reference/bpmn-processes/service-tasks/service-tasks) within Camunda Cloud require you to set a task type and implement [job workers](https://docs.camunda.io/docs/product-manuals/concepts/job-workers) who perform whatever needs to be performed. This post looks at how to write good job workers — and explains what “good” might mean in this context.

Therefore, this post will look into these topics:

- Organizing glue code and workers — aka how many task types should one worker handle?
- Thinking about idempotency and data minimization.
- Scaling workers with blocking or non-blocking (reactive) code with examples in Java, NodeJS, and C#.

You might also be interested in [part 2 of this blog post series: Service Integration Patterns With BPMN And Camunda Cloud](https://blog.bernd-ruecker.com/service-integration-patterns-with-bpmn-and-camunda-cloud-53b0f458e49).

### Naming conventions
Boring — I know — but let’s quickly start by mentioning that I use the term “worker” here in sync with the[ Camunda Cloud glossary](https://docs.camunda.io/docs/reference/glossary/):

- **Worker**: Synonym for “job worker”.
- **Job Worker**: Active software component that subscribes to Zeebe to execute available jobs (typically when a process instance reaches a service task).

Related terms:

- **Bridge**: Synonym for “connector”.
- **Connector**: A piece of software that connects Zeebe with some other system or infrastructure. Might be uni or bidirectional and possibly includes a job worker. The boundary between connector and job worker can be fuzzy, in general connectors connect to other active pieces of software. For example, a ‘DMN connector’ might connect Zeebe to a managed DMN Engine, a ‘DMN worker’ will use a DMN library to execute decisions.
- **Glue Code**: Any piece of programming code that is connected to the process model (e.g. the handler part of a job worker).

### Organizing glue code and workers
Assume the following simple order fulfillment process:

![](https://cdn-images-1.medium.com/max/800/0*b4NHzX5_hOkVwF91)

As described in [Service Integration Patterns With BPMN And Camunda Cloud](https://blog.bernd-ruecker.com/service-integration-patterns-with-bpmn-and-camunda-cloud-53b0f458e49) this could mean that all service tasks invoke services either synchronously or asynchronously. Let’s quickly assume we need three synchronous REST calls to the responsible systems (payment, inventory, and shipping). As a quick reminder, [Connecting The Workflow Engine With Your World](https://blog.bernd-ruecker.com/drafting-your-camunda-cloud-architecture-connecting-the-workflow-engine-with-your-world-3d94e8d404d6) described why custom glue code might work better than connectors for this case.

So should you create three different applications with a worker for one task type each, or would it be better to process all task types within one application?

As a rule of thumb, I recommend implementing **all glue code in one application**, which for me is the so-called process solution (as described in [Practical Process Automation](https://processautomationbook.com/)). This process solution might also include the BPMN process model itself, deployed during startup. Thus, you create a self-contained application that is easy to version, test, integrate and deploy.

![](https://cdn-images-1.medium.com/max/800/0*KZ-6Q29-j9639W4o)

Figure taken from [Practical Process Automation](https://processautomationbook.com/)Thinking of Java, the three REST invocations might live in three classes within the same package (showing only two for brevity):

Of course, you can also pull the glue code for all task types into one class. Technically it does not make any difference and some people find that structure in their code easier. This is fine. If in doubt, my default is to create one class per task type.

There are exceptions when you might not want to have all glue code within one application:

- You need to specifically control the load for one task type, like scaling it out or throttling it. For example, if one service task is doing PDF generation, which is compute-intensive, you might need to scale it much more than all other glue code. On the other hand, it could also mean limiting the number of parallel generation jobs due to licensing limitations of your third-party PDF generation library.
- You want to write glue code in different programming languages, for example, because writing specific logic in a specific language is much easier (like using Python for certain AI calculations or Java for certain mainframe integrations).

### Understanding workers conceptually
Now, let’s briefly understand how job execution with a job worker really works — so I can give you some more tips based on this knowledge.

![](https://cdn-images-1.medium.com/max/800/0*5AeSSJuIZvZD3FCd)

Whenever a process instance arrives at a service task, a new job is created and pushed to an internal persistent queue within Camunda Cloud. A client application can subscribe to these jobs with the workflow engine by the task type name (which is comparable to a queue name).

If there is no worker subscribed when a job is created, the job is simply put in a queue. If multiple workers are subscribed, they are competing consumers, and jobs are distributed among them.

Whenever the worker has finished whatever it needs to do (like invoking the REST endpoint), it completes the job, which is another call to the workflow engine.

### Thinking about idempotency of workers
Executing the glue code is external to the workflow engine and there is no technical transaction spanning both components. In other words: things can get out of sync if either the job handler or the workflow engine fails.

Camunda Cloud uses the “at-least-once” strategy for job handlers, which is a typical choice in distributed systems. This means that the process instance only advances in the happy case (the job was completed, the workflow engine received the complete job request and committed it). A typical failure case occurs when the worker who polled the job crashes and cannot complete the job anymore. [In this case, the workflow engine gives the job to another worker after a configured timeout](https://docs.camunda.io/docs/product-manuals/concepts/job-workers#timeouts). This ensures that the job handler is executed at least once.

But this can mean that the handler is executed more than once! You need to consider this in your handler code, as the handler might be called more than one time. The [technical term describing this is idempotency](https://en.wikipedia.org/wiki/Idempotence). I described typical strategies in [3 common pitfalls in microservice integration — and how to avoid them](https://blog.bernd-ruecker.com/3-common-pitfalls-in-microservice-integration-and-how-to-avoid-them-3f27a442cd07): One possibility is to ask the service provider if it has already seen the same request. A more common approach is to implement the service provider in a way that allows for duplicate calls. There are two easy ways of mastering this:

- Natural idempotency. Some methods can be executed as often as you want because they just flip some state. Example: *confirmCustomer()*
- Business idempotency. Sometimes you have business identifiers that allow you to detect duplicate calls (e.g. by keeping a database of records that you can check). Example: *createCustomer(email)*

If these approaches do not work, you will need to add your own idempotency handling by using unique IDs or hashes. For example, you can generate a unique identifier and add it to the call. This way a duplicate call can be easily spotted if you store that ID on the service provider side. If you leverage a workflow engine you probably can let it do the heavy lifting. *Example: charge(transactionId, amount)*

Whatever strategy you use, make sure that you’ve thought about idempotency consciously.

### Data minimization in workers
Talking about idempotency, I also want to give you two rules of thumb about data in your workers.

First, if performance matters, minimize what data you read for your job. In your job client, you can define which process variables you will need in your worker, and only these will be read and transferred, saving resources on the broker as well as network bandwidth.

Second, minimize what data you write on job completion. You should explicitly not transmit the input variables of a job upon completion, which might happen easily if you simply “reuse” the map of variables you received as input for submitting the result.

Not transmitting all variables saves resources and bandwidth, but serves another purpose as well: upon job completion, these variables are written to the process and might overwrite existing variables. If you have parallel paths in your process (e.g. [parallel gateway](https://docs.camunda.io/docs/reference/bpmn-processes/parallel-gateways/parallel-gateways), [multiple instance](https://docs.camunda.io/docs/reference/bpmn-processes/multi-instance/multi-instance)) this can lead to race conditions that you need to think about. The less data you write, the smaller the problem.

### Scaling workers
Let’s talk about processing a lot of jobs.

Workers can control the number of jobs retrieved at once. In a busy system it makes sense to not only request one job, but probably 20 or even up to 50 jobs in one remote request to the workflow engine, and then start working on them locally. In a lesser utilized system long polling is used to avoid delays when a job comes in. Long polling means the client’s request to fetch jobs is blocked until a job is received (or some timeout hits). So the client does not constantly need to ask.

Anyway, you will have jobs in your local application that need to be processed. The worst-case in terms of scalability is that you process the jobs sequentially one after the other. While this sounds bad, it is valid for many use cases. Most projects I know do not need any parallel processing in the worker code as they simply do not care whether a job is executed a second earlier or later. Think of a business process that is executed only some hundred times per day and includes mostly human tasks — a sequential worker is totally sufficient (congratulations, this means you can safely skip this section of the blog post).

However, you might need to do better and process jobs in parallel and utilize the full power of your worker’s CPUs. In such a case, you should read on and understand the difference between writing blocking and non-blocking code.

### Blocking (also known as synchronous) code and thread pools
With blocking code a thread needs to wait (is blocked) until something finishes before it can move on. In the above example, making a REST call requires the client to wait for IO — the response. The CPU cannot compute anything during this time period, however, the thread cannot do anything else.

Assume that your worker shall invoke 20 REST requests, each taking around 100ms, this will take 2s in total to process. Your throughput can’t go beyond 10 jobs per second with one thread.

A common approach to scaling throughput beyond this limit is to leverage a thread pool. This works as blocked threads are not actively consuming CPU cores, so you can run more threads than CPU cores — since they are only waiting for I/O most of the time. In the above example with 100ms latency of REST calls, having a thread pool of 10 threads increases throughput to 100 jobs/second.

The downside of using thread pools is that you need to have a good understanding of your code, thread pools in general, and the concrete libraries being used. Typically, I would not recommend configuring thread pools yourself. If you need to scale beyond the linear execution of jobs, leverage reactive programming.

### Non-blocking (also known as reactive or asynchronous) code
Reactive programming uses a different approach to achieve parallel work: extract the waiting part from your code.

With a reactive HTTP client you will write code to issue the REST request, but then not block for the response. Instead, you define a callback as to what happens if the request returns. Most of you know this from JavaScript programming. Thus, the runtime can optimize the utilization of threads itself, without you the developer, even knowing.

In general, using reactive programming is favorable in most situations where parallel processing is important. However, I still see a lack of understanding and adoption in developer communities, which might hinder adoption in your environment.

### Client library examples
Let’s go through a few code examples using Java, NodeJS, and C#, using the corresponding client libraries. All code is available on Github:

[**berndruecker/camunda-cloud-clients-parallel-job-execution**
*Contribute to berndruecker/camunda-cloud-clients-parallel-job-execution development by creating an account on GitHub.*github.com](https://github.com/berndruecker/camunda-cloud-clients-parallel-job-execution)A walk through recording is available on YouTube:

### Java
Using the [Java Client](https://github.com/camunda-cloud/camunda-cloud-get-started/tree/master/java) you can write worker code like this:

This is abstracted by the [Spring integration](https://github.com/zeebe-io/spring-zeebe/), which itself [uses a normal worker from the Java client](https://github.com/zeebe-io/spring-zeebe/blob/master/client/spring-zeebe/src/main/java/io/camunda/zeebe/spring/client/config/processor/ZeebeWorkerPostProcessor.java#L56) underneath. So your code might look more like this:

In the background, a worker starts a polling component and [a thread pool](https://github.com/camunda-cloud/zeebe/blob/d24b31493b8e22ad3405ee183adfd5a546b7742e/clients/java/src/main/java/io/camunda/zeebe/client/impl/ZeebeClientImpl.java#L179-L183) to [handle the polled jobs](https://github.com/camunda-cloud/zeebe/blob/develop/clients/java/src/main/java/io/camunda/zeebe/client/impl/worker/JobPoller.java#L109-L111). The [**default thread pool size is one**](https://github.com/camunda-cloud/zeebe/blob/760074f59bc1bcfb483fab4645501430f362a475/clients/java/src/main/java/io/camunda/zeebe/client/impl/ZeebeClientBuilderImpl.java#L49). If you need more, you can enable a thread pool:

Now, you can **leverage blocking code** for your REST call, like for example the RestTemplate inside Spring:

Doing so limits the degree of parallelism to the number of threads you have configured. You can [observe in the logs](https://github.com/berndruecker/camunda-cloud-clients-parallel-job-execution/blob/main/results/java-blocking-thread-1.log) that jobs are executed sequentially when running with one thread ([the code is available on GitHub)](https://github.com/berndruecker/camunda-cloud-clients-parallel-job-execution/blob/main/java-worker/src/main/java/io/berndruecker/experiments/cloudclient/java/RestInvocationWorker.java):

10:57:00.258 [pool-4-thread-1] Invoke REST call…10:57:00.258 [ault-executor-0] Activated 32 jobs for worker default and job type rest10:57:00.398 [pool-4-thread-1] …finished. Complete Job…10:57:00.446 [pool-4-thread-1] …completed (1). Current throughput (jobs/s ): 110:57:00.446 [pool-4-thread-1] Invoke REST call…10:57:00.562 [pool-4-thread-1] …finished. Complete Job…10:57:00.648 [pool-4-thread-1] …completed (2). Current throughput (jobs/s ): 210:57:00.648 [pool-4-thread-1] Invoke REST call…10:57:00.764 [pool-4-thread-1] …finished. Complete Job…10:57:00.805 [pool-4-thread-1] …completed (3). Current throughput (jobs/s ): 3If you experience a large number of jobs, and these jobs are waiting for IO the whole time — as REST calls do — you should think about using **reactive programming**. For the REST call this means for example the Spring WebClient:

This code also uses the reactive approach to use the Zeebe API:

With this reactive glue code, you don’t need to worry about thread pools in the workers anymore, as this is handled under the hood from the frameworks or the Java runtime. [You can see in the logs ](https://github.com/berndruecker/camunda-cloud-clients-parallel-job-execution/blob/main/results/java-nonblocking.log)that many jobs are now executed in parallel — and even by the same thread in a loop within milliseconds.

10:54:07.105 [pool-4-thread-1] Invoke REST call…[…] 30–40 times!10:54:07.421 [pool-4-thread-1] Invoke REST call…10:54:07.451 [ctor-http-nio-3] …finished. Complete Job…10:54:07.451 [ctor-http-nio-7] …finished. Complete Job…10:54:07.451 [ctor-http-nio-2] …finished. Complete Job…10:54:07.451 [ctor-http-nio-5] …finished. Complete Job…10:54:07.451 [ctor-http-nio-1] …finished. Complete Job…10:54:07.451 [ctor-http-nio-6] …finished. Complete Job…10:54:07.451 [ctor-http-nio-4] …finished. Complete Job…[…]10:54:08.090 [pool-4-thread-1] Invoke REST call…10:54:08.091 [pool-4-thread-1] Invoke REST call…[…]10:54:08.167 [ault-executor-2] …completed (56). Current throughput (jobs/s ): 56, Max: 5610:54:08.167 [ault-executor-1] …completed (54). Current throughput (jobs/s ): 54, Max: 5410:54:08.167 [ault-executor-0] …completed (55). Current throughput (jobs/s ): 55, Max: 55These observations yield in the following recommendations:

![](https://cdn-images-1.medium.com/max/800/1*omkAnsbxL3i91fdNNLMMmw.png)

### NodeJs client
Using the [NodeJS client](https://github.com/camunda-cloud/camunda-cloud-get-started/tree/master/nodejs) you will write worker code like this, assuming that you use Axios to do rest calls (but of course any other library is fine as well):

This is **reactive code**. And a really interesting observation is that reactive programming is so deep in the JavaScript language that it is impossible to write blocking code, even code that looks blocking is still [executed in a non-blocking fashion](https://github.com/berndruecker/camunda-cloud-clients-parallel-job-execution/blob/main/results/nodejs-blocking.log).

NodeJs code scales pretty well and there is no specific thread pool defined or necessary. The Camunda Cloud NodeJS client library also [uses reactive programming internally](https://github.com/camunda-community-hub/zeebe-client-node-js/blob/master/src/zb/ZBWorker.ts#L28).

This makes the recommendation very straight-forward:

![](https://cdn-images-1.medium.com/max/800/1*1p7bs5nRYI2LGpOJ2ZCUmw.png)

### C#
Using the [C# client](https://github.com/camunda-cloud/camunda-cloud-get-started/tree/master/csharp) you can write worker code like this:

You can see that you can set a number of handler threads. Interestingly, this is a naming legacy. The C# client uses the [Dataflow Task Parallel Library (TPL)](https://docs.microsoft.com/en-us/dotnet/standard/parallel-programming/dataflow-task-parallel-library) to implement parallelism, so the thread count configures the degree of parallelism allowed to TPL in reality. Internally this is implemented as a mixture of event loop and threading, which is an implementation detail of TPL. This is a great foundation to scale the worker.

You need to provide a Handler. For this handler you have to make sure to write non-blocking code, the following example shows this for a REST call using the [HttpClient](https://docs.microsoft.com/en-us/dotnet/api/system.net.http.httpclient?view=net-5.0) library:

The code is executed in parallel,[ as you can see in the logs](https://github.com/berndruecker/camunda-cloud-clients-parallel-job-execution/blob/main/results/dotnet-nonblocking.log). Interestingly, the following code runs even faster for me, but [that’s a topic for another discussion](https://stackoverflow.com/questions/21403023/performance-of-task-continuewith-in-non-async-method-vs-using-async-await):

In contrast to NodeJS you can also write **blocking code** in C# if you want to (or more probable: it happens by accident):

The degree of parallelism is down to one again, [according to the logs](https://github.com/berndruecker/camunda-cloud-clients-parallel-job-execution/blob/main/results/dotnet-blocking-thread-1.log). So C# is comparable to Java, just that the typically used C# libraries are reactive by default, whereas Java still knows just too many blocking libraries. The recommendations for C#:

![](https://cdn-images-1.medium.com/max/800/1*NxeYsrUGh93ftWMzJUWNFg.png)

### Conclusion
This blog covered some rules on how to write good workers:

- Write all glue code in one application, separating different classes or functions for the different task types.
- Think about idempotency and read or write as little data as possible from/to the process.
- Write non-blocking (reactive, async) code for your workers if you need to parallelize work. Use blocking code only for use cases where all work can be executed in a serialized manner. Don’t think about configuring thread pools yourself.

I hope you find this useful and I definitely look forward to [any questions or discussions in our Camunda Cloud forum](https://forum.camunda.io/).

[Subscribe to me on Twitter](https://twitter.com/berndruecker) to ensure you see **part four of this series, which will discuss backpressure in the context of Camunda Cloud**.
