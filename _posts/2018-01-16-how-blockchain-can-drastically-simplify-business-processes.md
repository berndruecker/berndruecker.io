---
layout: post
title: "How blockchain can drastically simplify business processes."
date: 2018-01-16 12:00:00 +0000
categories: [blog]
tags: ["camunda", "spring", "process-automation"]
author: Bernd Ruecker
excerpt: "!https://cdn-images-1.medium.com/max/800/1gfgR0d_AQX_qvold3T2xYg.jpeg"
---
### How blockchain can drastically simplify business processes.

### A simple example using smart contracts.

![](https://cdn-images-1.medium.com/max/800/1*gfgR0d_AQX_qvold3T2xYg.jpeg)

Recently I had to buy a car. And I wanted to blog about BPM and blockchain for ages. So let’s go! You probably know the process of buying a car nowadays: Go to the internet portal of your choice (e.g. mobile.de in Germany), search for a car and buy it via email. The platform is just a broker, so the buying process is done with the dealer directly. This also means you cannot simply pay via PayPal or the like.

Now we have two parties who don’t trust one another: I do not trust the car dealer (a noble profession probably, but I always think car dealers want to shaft me) and the car dealer does not trust me, basically because he/she doesn’t know me.

*Having partners doing business without mutual trust is the optimal setting for blockchain use cases!*

The transaction now looks like this (at least in Germany where I live) - I use [BPMN](https://www.amazon.de/dp/B01NAL67J8/) for a simple illustration:

![](https://cdn-images-1.medium.com/max/1200/1*kX84SGP2F9MDN6XCuFrZRw.png)

Not only is the process very inefficient and slow, but there is one moment that is really harsh (marked orange in the picture above): I have to transfer money before I get the car or even the documents (some dealers change this sequence to send the documents first). What if the dealer is a fake and the car does not exist?

*The classical approach to solve lack of trust is to introduce a trusted intermediate.*

In this case many dealers in Germany offer “sicherbezahlen.de” which is a startup dedicated for this special use case. Very often the intermediate is also a bank, a lawyer, some public authority or a notary. Actually a lot of companies or governance agencies today are there solely for this purpose. Now both parties are exposed to some risk: I pay before I get the car and the dealer gives the car away before he gets money. But we are both willing to complete the transaction as we trust the intermediary. This is however not for free, in this case it costs around 100 €.

![](https://cdn-images-1.medium.com/max/1200/1*jjrwgEAh6t2pYZFz2Vzh4g.png)

*Blockchain technology can make this intermediary unnecessary.*

Blockchain establishes trust without that intermediate party by

- providing a database where all data is distributed to everybody joining and
- adding some clever cryptography to make it impossible to change or fake data once it is in there.

This leads to a database everybody can trust as there is no single party in control.

How can we leverage blockchain for the car buying process? Let’s look at a concept called [smart contracts,](https://www.ethereum.org/greeter) for example offered by [Ethereum](https://www.ethereum.org/). A smart contract is basically a small software program that runs in the blockchain and is secured in the same way as any data. And as Ethereum offers also the cryptocurrency Ether you can very easily build a smart contract that locks money in a safe way.

*Smart contracts are automated programs in the blockchain that can replace the need for intermediates.*

Smart contracts use cryptography to make sure certain methods can only be called by the eligible participants. For [Ethereum, smart contract are written in a JavaScript like language](https://www.ethereum.org/greeter):

The smart contract forms a simple state machine but is publicly visible and trusted. By the way, smart contract execution costs money too but much less than 100 €.

![](https://cdn-images-1.medium.com/max/1200/1*CGSTdpCHG2MmAHqwg4nGdw.png)

Technically the API of e.g. Ethereum is pretty easy. Building a prototype to connect business processes which are automated on the [Camunda Platform](https://camunda.com/) with Ethereum smart contracts is easy to do. So **the private business process of one party **(e.g. the car dealer)** can be automated on a classical workflow automation platform and the public process is automated using a smart contract**. Because you can get rid of paperwork with smart contracts this setting will allow straight-through end-to-end processing of two parties without mutual trust. This can be a game changer!

By the way: A thought experiment we did was to generate Ethereum smart contract code out of a BPMN model like the one above. This would also be easily doable making BPMN a pretty interesting tool in the blockchain technology landscape.

To sum it up — technically it is easy to get going with blockchain and smart contracts and it is also easy to combine it with classical BPM methodology and workflow automation technology.

*So why isn’t everybody doing blockchain and smart contracts already?*I see many hurdles to a quick and broad adoption of blockchain:

- **You cannot do it alone.** There are probably use cases around compliance records but overall it does not make much sense to introduce blockchain **within **the company (relatively easy) best use cases are **between **parties which do not trust each other (hard).
- **Limited adoption of cryptocurrencies.** As seen above, smart contracts can work most efficiently if cryptocurrencies are used. The same example gets harder if done with “real” money.
- **Radical change.** Business processes need to change radically in order to take advantage of blockchain. Take the above example and imagine that even the government will participate. That means you might not even need documents for your car in order to prove ownership, it could be saved in the blockchain. The insurance agency could sign the smart contract to prove insurance. No paper work, no regulatory authority, no bank involved at all! This is a radical change that will take a long time to happen — in this case it is much more likely that we get autonomous cars so nobody needs to buy a car anymore at all.
- **Missing privacy.** Transactions in a blockchain are readable for all participants.
- **Limited number of transactions.** [Adrian Coyler calculated](https://blog.acolyer.org/2017/12/13/bolt-anonymous-payment-channels-for-decentralized-currencies-part-i/) something interesting: Assume that every human would use Bitcoin then everybody could do around 3 transactions **in his lifetime. **This is not much! It is because of the very limited throughput of Bitcoin, so the technology still has to evolve.

I don’t think this will stop the evolution of and revolution by blockchain technology in the long run. But it stops the quick adoption or the emergence of simple use cases or examples. This always stopped me from blogging about simple “Camunda + blockchain” examples which are kind of useless — as technology integration is the easiest piece of the puzzle.

So I was actually not surprised by what AWS CEO Andy Jessy said in a [recent interview](http://www.zdnet.com/article/aws-not-buying-into-the-blockchain-hype/):

> We don’t yet see a lot of practical use cases for blockchain that are much broader than using a distributed ledger. We don’t build technology because we think the technology is cool, we only build it if we think we can solve a customer problem and building that service is the best way to solve it.

*Interesting use cases?*That brings us to the question if there are real-life use cases available. If you know any [let me know](mailto:mail@bernd-ruecker.com) and I can probably add them here!

I know the [B3i initiative](https://b3i.tech/) which drives a private blockchain for re-insurers. This is actually a great setting as there are not too many re-insurers available (not too many parties) but enough to form a private blockchain with a sufficient level of trust (as you could start to cheat if you can control more than 50% of the participants of a blockchain).

And you can find more and more stories around this topic, e.g. [e-Estonia: The power and potential of digital identity](https://blogs.thomsonreuters.com/answerson/e-estonia-power-potential-digital-identity/) or [The First Government To Secure Land Titles On The Bitcoin Blockchain Expands Project](https://www.forbes.com/sites/laurashin/2017/02/07/the-first-government-to-secure-land-titles-on-the-bitcoin-blockchain-expands-project/#c338eba4dcdc).

Many thanks to [Johannes Pfeffer](https://medium.com/@oaeee) for giving me a great jump start into smart contracts and sharing a comparable use case with me in the first place.

As always, I love getting your feedback. Comment below or [send me an email](mailto:mail@bernd-ruecker.com).

*Stay up to date with my *[*newsletter*](http://eepurl.com/cDXPRj)*.*
