---
layout: post
title: "If an Event is Published to a Topic and No One is Around to Consume it, Does it Make a Sound?"
date: 2021-03-18 12:00:00 +0000
categories: [blog]
tags: ["camunda", "orchestration", "event-driven", "architecture", "process-automation"]
author: Bernd Ruecker
excerpt: "!https://cdn-images-1.medium.com/max/800/0TkucOxNYRndoWMmp"
---
### If an Event is Published to a Topic and No One is Around to Consume it, Does it Make a Sound?

#### How I Conquered a Fuzzy Feeling to Not Fully Understand Event Streaming Architectures
When I got introduced to event streaming architectures I had long discussions with an enterprise architect from a big international bank. During these conversations, I got the impression that one of the core promises of event streaming architectures is that you can easily change stream processor logic any time and simply [replay the stream of events](https://www.confluent.io/blog/data-reprocessing-with-kafka-streams-resetting-a-streams-application/).

![](https://cdn-images-1.medium.com/max/800/0*TkucOxNYRndoWMmp)

I talked about this at Kafka Summit 2020 ([Slides and Recording](https://www.confluent.de/resources/kafka-summit-2020/if-an-event-is-published-to-a-topic-and-no-one-is-around-to-consume-it-does-it-make-a-sound/))This really puzzled me: How can I change logic and replay data streams if there might have been actions already triggered, such as an approval notice to the customer being sent? And on the other hand, if there are **no*** *such actions, what’s the value of that stream in this case? Does a stream of data has value on its own ? This reminded me of the well-known philosophical question: [If a tree falls in a forest and no one is around to hear it, does it make a sound?](https://en.wikipedia.org/wiki/If_a_tree_falls_in_a_forest)

### The Story of Connor Riley
I find the metaphor of Twitter and a concrete story helps to dig into this. Early 2009, the college student [Connor Riley](https://www.nbcnews.com/id/wbna29901380) tweeted that Cisco offered her a job, but that she would actually not like it. This tweet (=[event](https://martinfowler.com/eaaDev/DomainEvent.html)) went into the giant stream of tweets (=[stream](https://docs.confluent.io/platform/current/streams/concepts.html#stream)) on Twitter. Now there might have been an application filtering for certain tweets, e.g. all tweets that contain something around “Cisco” (=[stream processor](https://docs.confluent.io/platform/current/streams/concepts.html#stream-processor)). This filter produces a new stream of tweets. Then there might be another filter looking for offensive tweets within all the Cisco-related ones. But by just having a list of offensive tweets around Twitter, nothing in the real world has just happened, it is just some data existing somewhere.

![](https://cdn-images-1.medium.com/max/800/0*skmbw59E9G4vCK2f)

Only when, for example, a human looks at this list and decides to act on the tweet it leads to changes in the real world. In Connor Riley’s case, Cisco really revoked the job offer and responded to her tweet. So without that action, the stream of data would have been useless for Cisco.

Now Cisco might improve its filter to find offensive tweets. That’s so great that they want to re-evaluate all tweets of the last 6 months. This results in some new entries in the offensive tweet stream, some entries keep being in the stream, but others might also vanish from the stream. How should the person taking action based on these tweets react? OK, they can easily act on new entries and ignore tweets they already worked on before. But here is the tricky part: What should they do about tweets that were on the list before the changes, but are not any longer?

In other words: If Cisco classifies Connor’s tweet no longer as offensive, but maybe as humoristic, what happens then? Do they need to undo their actions, meaning to apologize for the earlier reaction and offer her a job? Probably not, and the exact behavior is a business decision which is not the topic of this post.

My point here is that in order to be able to implement any reasonable business strategy, Cisco needs to remember its former actions that have been taken.

### Deriving a Pattern
As a general pattern, you need to introduce persistent state to translate between the world of streams and the world of real actions, at least if you want to really leverage the functionality to replay streams of data. Then you need to take this history into account when deciding for which action to take.

While this seems obvious to me in hindsight, I remember the feeling of not understanding this detail in the beginning. This is my motivation for this blog post.

If the Twitter story is too metaphoric for you, I [describe a real-life example from a vehicle maintenance use case in this blog post](https://www.confluent.io/blog/data-streams-are-nothing-without-actionable-insights-leading-to-actions/). There you can read how billions of sensor data points came in via Kafka and must be transformed into insights that occasionally lead to important actions a mechanic needs to take.

### Conclusion
Data streams are passive in nature. On their own, they do not lead to any action. But at some point in time, actions must be taken. The action might be carried out by a human, looking at data and reacting to it, or an external service that’s called, or a “traditional” database that’s updated, or a workflow that’s started. If there’s never any action, your stream is kind of useless.

The translation from stream to action is not always super simple. It might take a custom component as described in [“Stream Processing is Nothing Without Action”](https://www.confluent.io/blog/data-streams-are-nothing-without-actionable-insights-leading-to-actions/) and can lead to interesting questions, such as if Connor Riley should work at Cisco or not.

I talked about all of this with great success at Kafka Summit 2020 ([Slides and Recording are available here](https://www.confluent.de/resources/kafka-summit-2020/if-an-event-is-published-to-a-topic-and-no-one-is-around-to-consume-it-does-it-make-a-sound/)).

If you want to learn more about streaming and process automation you might also want to have a look at the [Practical Process Automation](https://processautomationbook.com/) book, which briefly touches on data streaming, but also discusses questions around event-driven choreography vs. orchestration with workflow engines. And [don’t hesitate to reach out to me](mailto:bernd.ruecker@camunda.com) if you have your own streaming + process automation story to share.
