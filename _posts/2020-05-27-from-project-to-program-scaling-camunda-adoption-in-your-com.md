---
layout: post
title: "From Project to Program: Scaling Camunda Adoption in Your Company"
date: 2020-05-27 12:00:00 +0000
categories: [blog]
tags: ["camunda", "microservices", "spring", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "> How can we scale Camunda adoption within the enterprise?"
---
### From Project to Program: Scaling Camunda Adoption in Your Company

#### How to move beyond your first projects and automate hundreds of processes successfully using an agile step-by-step approach
We often get questions like:

> How can we scale Camunda adoption within the enterprise?

> How can we set up a company-wide workflow platform?

> We have seen the scale of Camunda adoption within Goldman Sachs (3000 workflows, 8000 daily users), Societe Generale (600 workflows, 60k human tasks completed/month, 7500 active users) or 24Hour Fitness (800 processes, 230M activity instances/day), how can we get there?

![](https://cdn-images-1.medium.com/max/800/0*rvbQ7Ckhjdc7_l9l)

[Some slides from Camunda Con Live 2020](https://www.camundacon.com/live/hub/)This is why I wrote this blog post.

**Spoiler alert: If you want a company-wide platform, don’t start with a company-wide platform!**

### A Real-life Success Story
Let’s find out why and start with a real-life success story, which a lot of our customers can relate to. It is about an insurance company with around 7000 employees, where I could observe progress over the last five years. Unfortunately I can’t name them here.

In 2014 they formed a team to automate the handling of specific claims around their car insurance. There was a real pain back then, as the existing claim handling was mainly manually driven and spanned a couple of organization units. This made it easy to build a business case for the project and to get buy-in from top management. This was further backed by the strategic initiative to intensify “process orientation”, that was a hot topic for insurances back then.

As part of this project they:

- evaluated a workflow tool,
- modeled the workflow,
- implemented the whole workflow solution,
- integrated it with their existing user interface,
- integrated it with their existing SOA infrastructure,
- exported relevant data into their data warehouse
- And set it live and operated it.

The project went relatively smoothly and took around 12 months.

After that project, the team was reorganized into its own department. They were given responsibility to help other teams design and develop workflow solutions. In the first two to three years, they did a lot of the implementation work for these teams, but over time evolved into an internal consulting task force that “just” helped other teams to get started.

They naturally became the go-to place for any questions or discussions around workflow tooling and thus not only made sure experiences and learnings were kept, but also facilitated knowledge sharing across the entire organization. In the meanwhile they run an internal BPM blog, organize their own training classes and manage an annual internal community event where different teams can share best practices.

While they did develop some tools on top of Camunda, they never forced anybody in the company to use them. And while they started operating a central BPM platform back in 2015, they moved away from this model to allow people to run their own engine. They still provide reusable components around Camunda, e.g. to hook into Active Directory or to talk to their internal ESB, but these are provided as additional libraries to teams building workflow solutions.

By the end of 2019 they had almost 100 different workflow solutions running in production. Not only is the workflow team super satisfied, but so too are upper management.

### Stories About Adoption Journeys
I am currently collecting adoption journeys and stories from various customers. For example, this great journey from a large bank:

![](https://cdn-images-1.medium.com/max/800/0*MfH1PpI2C39nDS3z)

I would expect that we can extract a couple of typical patterns from it. These patterns and stories can help other users to learn from it. So do you have your own story to share? Please [contact me](mailto:bernd.ruecker@camunda.com)!

### Elements of a Successful Adoption Journey
You can derive a lot of learnings from the initial example, backed by many other stories too:

- Start with a project, not a program.
- Don’t start big and strategic endeavors too early in your journey. Instead, go step-by-step until you are ready to scale.
- Resist the temptation to create your own platform.
- Get buy-in from your decision-maker. This is much easier to obtain when there is some real pain that your workflow is going to solve.
- Let your lessons learned influence your target picture, don’t just adopt some consulting company’s best practices.
- Make sure to give experienced people the opportunity to help in follow-on projects.
- Capture best practices and ensure knowledge sharing.
- Provide reusable components if they increase productivity, but as libraries that teams want to adapt (instead of have to adapt).
- Establish an internal consulting approach, probably organized as a[ center of excellence](https://en.wikipedia.org/wiki/Center_of_excellence). At least identify and nurture one well-known champion in the enterprise that can drive the topic.
- Define learning paths for new people or teams.
- Make sure to let projects breath and make their own decisions.

Let’s look into some aspects in more depth.

### Phases in Your Adoption Journey
From hundreds of real-life projects over the years we derived a simple pattern that is most successful when introducing workflow tooling into an organization (our consulting team described it in a best practice called[ the customer success path](https://camunda.com/best-practices/following-the-customer-success-path/)):

![](https://cdn-images-1.medium.com/max/800/0*tIW9Rsg_z6Onrt0a)

Start first with a **pilot project**. The goal of this project is to define and validate architecture and stack. Very often, this pilot project is set up as a [proof-of-concept (POC)](https://camunda.com/best-practices/doing-a-proper-poc/). However, it is important to go-live with that pilot to really learn about all aspects of the workflow solution throughout the full software development life cycle. You should choose a scenario where you can show at least some of the benefits of workflow automation (e.g., increased efficiency, effectiveness, compliance), as many people, including decision-makers, will be interested in quantifiable results.

Soon after running a successful pilot, you should tackle a **lighthouse project**. This project has a broader, more realistic scope and can be better leveraged to show off architecture, tooling and value of workflow automation to other people and teams within your organization. Make sure to select a relevant use case. Use caution to avoid political suicide missions.

And only then, the next step is to **scale Camunda adoption** across your enterprise. Still you should enter this phase slowly. Make sure to not go too broad before you have experienced enough relevant learnings in at least a handful of projects.

### Start with a project, not a program!
This is so important that I deliberately repeat it throughout this post: Concentrate on delivering business value with your workflow automation projects right from the start. This means two things.

- Do a concrete project and avoid strategic platform initiatives for as long as possible. Doing too much strategic work too early has a high risk that you don’t deliver any business value for a long time and probably get completely stuck in shaping a complex platform, without understanding its use case.
- Second, favor agile development approaches that develop workflow solutions iteratively and incrementally. This allows you to learn fast and let these learnings correct your course. This is a very positive and motivating spiral that we have seen working very successfully for many customers.

### Don’t Get Stuck in Big Platform Initiatives
“We want to build a company wide BPM (or process automation) platform on top of Camunda — how can we do this”? This is a very common question and its motivation is often two-fold. First, you don’t want to be dependent on Camunda too much and second, you might need some integration into company specifics that all projects can leverage. Some companies even assemble a whole SOA or integration stack with components of different vendors.

This is a risky endeavor for multiple reasons: It is quite hard to set up a bespoke platform and it will distract you from delivering business value. It makes it hard to include learnings in later projects, as you settle on certain architecture primitives very early in your journey. Also, it is complicated and time consuming to keep such a platform up-to-date or to fix bugs. Or simply to make all features of the underlying products available or to include new features of new versions. And finally, you simply can’t google for problems in your own bespoke platform, but you can for well-known open-source products.

So far, every one of these initiatives I saw struggled, especially if they were started too early in the journey. You should not think about creating a bespoke platform before you have a couple of projects live, so that you can really understand the common characteristics and double-check the value and applicability with each project.

![](https://cdn-images-1.medium.com/max/800/0*xEa8nXqLfHAAu5gD)

Of course, you might still do some work in first projects to make operations or enterprise architects happy. For example, you might integrate into your authentication and authorization infrastructure or make sure the workflow tooling adds its logs into your central logging facility.

### Dos and Don’ts Around Reuse
Reuse can make a lot of sense as you can save effort and costs. If all of your workflow solutions need to communicate with your messaging infrastructure (or worse: your mainframe!), you don’t want to reinvent that wheel in every project.

But instead of building your bespoke platform, another pattern can turn out to be really successful. Think of reusable components or libraries as internal open-source projects. You offer it to your company and provide some resources and help. If it is great, most people will happily apply it. But nobody has to, probably with the exception of the very first projects, where you evolve these libraries hand-in-hand. If projects need some additional feature they are not locked out, but can always provide pull requests — or fork the project. This model of thinking scales much better and does not block any team from being productive.

Camunda constantly increases its support for this kind of reuse, for example by a worker catalogue. This will allow you to register [external task workers](https://docs.camunda.org/manual/latest/user-guide/process-engine/external-tasks/) that can be easily reused in your workflow models using the graphical modeler. These workers (or connectors if you will) can e.g. [connect Camunda to your RPA tool](https://blog.camunda.com/tags/rpa/). This approach can make your developers more productive, without restricting anybody to only use these connectors. It concentrates on helpful guidelines instead of putting constraints in place.

Most workflow initiatives also create the idea of extracting process fragments that can be reused in different business processes. I am very skeptical about this. If this is done within one project team, it is totally fine. If these fragments should be shared across teams, you should not do process fragments but instead extract your own services with a properly defined capability and API. It will become an implementation detail that there is a workflow at play. The concept of [bounded contexts](https://martinfowler.com/bliki/BoundedContext.html) is applicable, if you are familiar with DDD. Which brings us to microservices.

### Managing Decentralized Workflow Engines
Instead of central platforms [I advocate for an approach that every team runs its own engine](https://blog.bernd-ruecker.com/the-microservice-workflow-automation-cheat-sheet-fc0a80dc25aa#def8), especially in a microservices context. The main advantage is to allow for scale by isolating teams.

![](https://cdn-images-1.medium.com/max/800/0*tU2XyVHIUiOr0sxv)

This means that with microservices you deliberately accept a wild mix of Camunda installations! Typically it is not a problem for your teams to set up Camunda, as they will simply leverage the Camunda documentation, as well as your own best practices or samples.

But how can you get an overview of what is actually running? How can you make sure the Camunda installations have all the important patches installed? Are all engines doing well? And in case you run the enterprise edition, you want to collect metrics from various engines to check that you are in your license limits.

Typically these questions are asked by the center of excellence, your Camunda champion or an enterprise architect with responsibility for Camunda.

In a recent POC, we validated a very simple idea. We automatically harvested the relevant data from different engines within the company. In order to do so, you can leverage the out-of-the-box REST API and retrieve the data via the[ metrics](https://docs.camunda.org/manual/latest/reference/rest/metrics/) and[ version](https://docs.camunda.org/manual/latest/reference/rest/version/) endpoints. You can [find a screenshot and the source code ](https://github.com/berndruecker/camunda-engine-harvester)on GitHub.

![](https://cdn-images-1.medium.com/max/800/0*s8xnzxodQ8hCegVy)

Of course, you need to register the endpoints of your engines centrally. But this is actually a chance for the center of excellence to get in touch with the Camunda users. As an alternative you could also push this data to the harvester, e.g. by writing a simple [process engine plugin](https://docs.camunda.org/manual/latest/user-guide/process-engine/process-engine-plugins/).

![](https://cdn-images-1.medium.com/max/800/0*6CGNz_xAQMDqmWMq)

### Leverage Cloud to Ease Provisioning
Provisioning and governance is much easier with managed services. So running multiple engines becomes super easy with[ Camunda Cloud](https://camunda.com/de/products/cloud/), as it already has a control plane built-in. This shows exactly the above mentioned information at a glance. It goes even further, as it allows you to update or patch engines automatically or with the click of a button.

### Monitor Business Processes End-To-End
As you scale Camunda usage, an organization’s entire end-to-end process typically exceeds the boundary of one workflow engine. Maybe the process is spread across different microservices using different Camunda engines or third-party workflow engines. Or some steps are executed by legacy software. Either way, you will still need visibility into the end-to-end process.

Trying to force everybody into the same Camunda engine has not proven to be a good approach, as this would limit the independence of different teams.

Most companies rely on business intelligence or data warehousing solutions to gain that overview. While this is a valid approach, our customers report that this is not easy to set up and typically misses the business process perspective. Other tools around observability or distributed tracing are typically too technical. This is why we introduced “[process events monitoring](https://blog.camunda.com/post/2020/04/announcing-camunda-optimize-3.0/)” into our process monitoring and reporting tool Optimize.

Do a step-by-step approach and avoid falling into paralysis by analysis, for example because you want to discuss the end-to-end monitoring upfront with all stakeholders involved.

### Establish a Center of Excellence
If you have one team doing the pilot and probably also the lighthouse project, they will not only become very familiar with the technology and architecture, but also learn a couple of lessons the hard way. These are super valuable experiences and you should make sure they can be leveraged in the following projects.

One option is that these people will simply continue building workflow solutions in a team. This is definitely super efficient, but does not scale. You could also split up the team to send the people to different projects, which I have seen working very well, but means you need to have some flexibility in team assignments. The third possibility is the one sketched in the introductory example: transform the project team into a center of excellence (COE).

![](https://cdn-images-1.medium.com/max/800/0*EJ-c_agXO90ncYa7)

The COE can be set up as dedicated Camunda COE, but more often it is a general process automation COE. Then its job is extended to evaluate workflow technology and to help decide the right tool for the job at hand. Typically these COEs also manage technologies around robotic process automation (RPA) or skill-based routing for human tasks.

The COE creates and maintains internal best practices. You can basically lean on the Camunda Docs and [Camunda Best Practices](https://camunda.com/best-practices/) as a basis. You should further document decisions, constraints or additions that apply to your company. For example you might want projects to always use the standalone Camunda Run distro, do external tasks via REST and add forms as HTML snippets. You can describe how Camunda is easily hooked into your central Active Directory. You can further link a couple of internal projects that provide integration into RabbitMQ, SOAP Web Services and FTP.

One customer (a big bank) told me how they developed a “self-service portal” within the COE over the course of two years. This portal contains getting started guides, Maven project templates and some reusable components as maintained libraries. This setup allows most projects to get going on their own, including projects staffed by big offshore IT integrators. In the beginning they had to develop the first six workflow solutions themselves, but now have seven additional projects already completed via self-service, which proves the direction they are heading.

The COE can also foster a community, simply by being available to talk to. Additionally you can add a forum, a Slack channel or regular face-to-face or web meetings. The right tool choice depends heavily on your company’s culture.

It is also worth investing in internal marketing as it is important that other projects know about the COE. You might even want to talk publicly about your use case and serve as a reference for Camunda, as I often hear of customers googling for Camunda, only to find a use case within their own company.

### Manage Architecture Decisions
I already made clear that I am not a fan of rigid standardization. Project teams need some freedom to choose the right tools. In many situations it is even best if the team can, for example, decide if they need a workflow engine at all. Your COE and lighthouse projects might have generated enough internal marketing for people to know the benefits of such a tool, so they should be enabled to decide.

But, of course, it is risky to let every team choose whatever they fancy in that moment, as this quickly becomes backed by trends, hypes, personal preferences or simply people that “wanted to try this out for ages”. It is important for everybody to understand that certain technology decisions are a commitment for years and sometimes even decades. So these decisions and the resulting maintenance affect more than just the current team.

What works well is to combine the freedom of choice with the reliability to operate and support the software solution in production, which is known as “you build it, you run it”. This important primitive makes the team aware that they are held accountable for their decisions. Whenever this is truly in place, teams make more sensible decisions and are more likely to choose [boring technology](http://mcfunley.com/choose-boring-technology).

Another common approach is to establish an architecture board that defines some guarding rails. Ideally, this does not dictate arbitrary standards but maintains a list of approved tools and frameworks. Whenever a team wants to use something that is not (yet) on the list, they have to discuss it with that board. Teams need to present the framework and the reasons why they need exactly this tool. This can even lead to a fruitful sparring around the tool. Teams might learn about alternatives that are better suited or they might get questions around maintenance they have not thought of. But of course they can also convince the board and get a green light.

I have also seen more rigid gatekeeping at customers, especially around bridge technologies that can easily be abused. For example when a team wants to use [RPA](https://blog.bernd-ruecker.com/how-to-benefit-from-robotic-process-automation-rpa-9edc04430afa), they need to build a case for it, because this will increase technical debt.

### Perception Management: What Is a Camunda Solution?
Customers use Camunda for very different use cases. One theme is to build solutions that are essentially Java applications, but with some workflow embedded. Internally these applications might be seen as “Camunda projects”, even if the workflow part of the application is relatively small. While this is not a problem, it comes with a risk. There might be huge bespoke applications being built. That means, it can take a lot of time before they are actually put into production. Or the projects might get very expensive, or even cancelled due to problems while building the applications. All these factors are not at all related to Camunda, but because the projects are connected to the Camunda brand, this might damage workflow automation as a topic.

Keep an eye on this risk, even though we like that you talk about Camunda internally :-)

The good news is: With a lot of customers moving more towards using Camunda as a standalone workflow engine (e.g. with [Camunda Cloud](https://camunda.com/products/cloud/) or [Camunda Run](https://docs.camunda.org/manual/latest/user-guide/camunda-bpm-run/) + [External Tasks](https://docs.camunda.org/manual/latest/user-guide/process-engine/external-tasks/)), this risk is reduced.

### Roles And Skill Development
I did a lot of proof of concepts in the early days of the company and faced motivated and super clever developers frequently. Like many other software companies, we mostly dealt with [early adopters](https://en.wikipedia.org/wiki/Early_adopter) in the beginning. Then, one day, I had a consulting assignment for one of the largest telecom companies in the world. I talked to a couple of developers who did not care about Camunda at all. They simply wanted to get the job done and their salary paid, and Camunda was thrown into the project by some enterprise guideline. And to be honest, they were good folks, but not really exceptionally gifted when it came to programming. And that is totally OK! I just had to realize myself, being a nerd and workflow engine addict, that there are normal people too.

That’s when I learned that you have to differentiate between different groups of developers:

- **Rockstar Developers **are the early-adopter developers that can sometimes perform miracles. They are highly motivated and passionate. You simply give them the Camunda get started guide and get out of their way. They will most probably google their way along. These folks are probably best located in the early projects and probably the COE. But they also come with the challenge that they always want the latest and greatest technology and sometimes tend to over engineer. And please pay attention as they are typically easily distracted — [oh look there’s a squirrel](https://www.youtube.com/watch?v=SSUXXzN26zg)!
- **Professional Developers** are trained software engineers. They are productive in their environment of choice with a very individual selection of tools (programming language, IDE, CI/CD, …). In order to be productive with Camunda they need to learn the basics of BPMN as well as get a solid foundation of Camunda concepts and API. Depending on your architecture and stack, you can choose between [Camunda BPM for Java Developers](https://camunda.com/services/training/camunda-java) and a more polyglot [Camunda BPM and Microservices](https://camunda.com/services/training/camunda-bpm-and-microservices) training course. It is important to give professional developers the freedom they need to be productive.
- **Low-Code Developers** are not trained software engineers, but often have a business background. They slipped into development using Microsoft Office tools, macros or RPA. They often dedicate their full working time to developing solutions in these environments. For many companies, the key to scaling their process automation efforts is to enable these citizen developers to model executable workflows. Some companies (like [e.g. Goldman Sachs](https://www.camundacon.com/live/)) invested themselves in supporting low-code developers to work with Camunda. Camunda will also increase support in future, e.g. by capabilities to step through the workflow directly in the modeler, by allowing users to pick connectors from a worker catalogue or by making expressions easier to define. Low-code developers often need a customized training course in the exact environment they will be working in.
- **Citizen Developers** are also not software engineers, but typically end-users with some IT affinity, that want to solve an active pain with a technology they can master. You might enable them to use Camunda with the platform you build for low-code developers, but we typically see customers focusing more on the low-code developers than citizen developers with their initiatives.

But of course it is not all about developers:

- **Business Analysts** need to learn to model BPMN. While they might use different techniques (e.g. around creativity methods) to discover and discuss workflow models, they should be able to create a BPMN model as input for development as well as understand all models done by developers. We recommend taking the [BPMN 2.0 Training Course.](https://camunda.com/services/training/bpmn-training)
- **Operations** people need to understand what it takes to deploy and run Camunda as well as how to troubleshoot failure situations. We set up [Camunda BPM DevOps](https://camunda.com/services/training/camunda-devops) for this matter.
- **Enterprise Architects** need to understand the role of Camunda in the bigger picture and architecture. While I advocated against too much architecture upfront, it is still important to include enterprise architects early in your journey to make sure they are on board. In problematic political situations we have seen it is wise to wait to talk to enterprise architecture until there is a concrete lighthouse project to show.

Some customers also report that they have additional workflow methodology experts that are really good at checking if a certain workflow design is the most reasonable one at hand. They constantly try to get at the bottom of design decisions striving to simplify workflows. These people are typically organized within the center of excellence.

Of course, roles and responsibilities can vary, and every person fulfilling a role will also “live” it in their own way. So while some basic understanding of roles and required skills is important to scale Camunda adoption in your organization, you should also be aware that these are just rough guidelines. I have seen “business folks” that program their smart home themselves and can definitely think like a developer. I have seen developers that are communication geniuses and thus could easily do business analysis without problems. But I have also seen developers that are scared to death by talking to people and business departments hardly know how to switch on a computer.

Note that a good training course can only be effective if you start using the knowledge for a real-life project right after. Try to have the training as timely as possible to your project start.

Additionally you should organize some coaching on the job. This can be delivered by Camunda, a partner or probably your own center of excellence. Very often, a remote consulting offering does work well for this kind of assignment.

### Process Architecture and Landscapes
“Before we can even decide on a process for the POC, we need to capture all business processes in the firm and put them on a process landscape. Otherwise we don’t understand the full picture and are not able to prioritize correctly”!

We are too often confronted with this mindset, which is dangerous. Not only that the effort of capturing these processes quickly explodes and too many people will be pulled into the initiative, but, more importantly, you have not yet gained enough experience with workflow automation methodology and BPMN to produce models in the right quality. This leads to process models that are useless in the best case or even models that become obstacles.

Sometimes companies want to safeguard past investments: “Hey, we did analyze this process already three years ago for our quality and compliance program. We still have the model around, let’s just use it for this automation project”. Hell no!

Process architectures and landscape have their place, but they have a more high-level view on processes and are only loosely connected to executable BPMN workflows. When you start your journey with Camunda you should decouple the first projects from any of these initiatives to make sure the project can breathe, learn and make its own decisions, without getting sucked into endless political or methodology discussions. Once you have more than a handful of projects live and have gained experience with BPMN, you can start aligning the different streams within your company.

I have seen lean approaches working best, so for example a simple confluence page can serve as an entry point into the process landscape, showing a basic structure that links to high-level process descriptions. From there, you can directly navigate into executable workflow models, either in the development source control or within [Cawemo](http://cawemo.com/).

Having a process architect who has an overview on this process architecture makes sense as soon as you scale adoption, but not before.

### Foster Collaboration
More important than a shiny process architecture is a practical procedure that allows collaboration on workflow design with all important stakeholders. Tooling-wise this can be as simple as Confluence pages with the [BPMN plugin](https://blog.camunda.com/post/2016/09/bpmn-dmn-modeler-for-confluence/) or as customized as bespoke tools leveraging Git and [bpmn.io](https://bpmn.io/) to allow joined modeling. More often, I see models on shared file systems opened with the Camunda Modeler or of course [Cawemo](https://cawemo.com/), our collaborative process modeling tool. All of this can work if the stakeholders involved have clarity on how this is envisioned. Ideally, your COE can help with this.

Try to avoid rolling out tools just because they are already established in business departments. Very often they [don’t support BPMN](http://bpmn-miwg.github.io/bpmn-miwg-tools/) or cannot be used to collaborate on executable models at all.

Of course, you also need to create a culture that fosters collaboration and open discussion. It will never work to throw models from business analysts “over the fence” to IT to implement them.

Part of this is to make sure that all important stakeholders have access to the process models and respective tooling. Much too often I see companies that don’t want to provide a license to every developer, which will result in a broken process. If license costs are a show stopper, consider a lightweight solution.

### Don’t Forget About Project Economics
It is important to focus on delivering business value with workflow projects. Additionally you need some mechanisms to prioritize automation candidates as a basis to decide what to tackle next. I will not go into project portfolio management in this post, as it is quite complex on its own and organized very differently throughout our customer base. And it would actually make for an own blog post, as this is quite too long already. Just make sure that you evaluate the potential of projects before jumping right into them.

Note that you might not need such strict rules to select your POC or lighthouse project, as this might be driven more by technical matters. But soon after this is completed, every project should be justified by numbers and business value, not only by technical enthusiasm.

### Conclusion
This post should give you a good understanding of what a successful adoption journey looks like. As a rule of thumb you should decentralize as much as possible and favor an agile step-by-step approach, that allows you to learn on the way, as the devil is always in the details and every situation has its unique challenges.

Enjoy your journey and all the best with your Camunda endeavors!
