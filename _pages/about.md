---
layout: page
title: About
permalink: /about
comments: false
---

<style>
.book-cover {
    height: 200px;
    width: 150px;
    object-fit: contain;
    background-color: #f8f9fa;
    padding: 10px;
}

.bio-section {
    margin-top: 2rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.bio-language {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    padding: 0.75rem 0;
    font-weight: 500;
    user-select: none;
}

.bio-language:hover {
    opacity: 0.8;
}

.bio-chevron {
    transition: transform 0.3s;
}

.bio-language.collapsed .bio-chevron {
    transform: rotate(-90deg);
}

.bio-content {
    margin-top: 1rem;
}

.bio-type {
    margin-top: 1.5rem;
    padding: 1rem;
    background: white;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}

.bio-type-title {
    font-weight: 600;
    font-size: 0.95rem;
    color: #495057;
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.bio-text {
    color: #212529;
    line-height: 1.6;
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
}

.bio-copy-btn {
    display: inline-block;
    padding: 0.4rem 0.75rem;
    background: #6c757d;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85rem;
    text-decoration: none;
    transition: background 0.3s;
}

.bio-copy-btn:hover {
    background: #5a6268;
    text-decoration: none;
    color: white;
}

.bio-copy-btn.copied {
    background: #28a745;
}
</style>

<div class="row justify-content-between">
<div class="col-md-8 pr-5">

<h4>Bernd Ruecker</h4>

<p>Bernd is software engineer at heart and the co-founder and chief technologist at <a href="https://camunda.com/">Camunda</a>. He is the author of "Practical Process Automation," the co-author of "Enterprise Process Orchestration" and "Real-Life BPMN," and is active on the technology speaking circuit. He holds an MSc in software technology from HFT Stuttgart.</p>

<p>See his complete bio below.</p>


<h4 class="mt-5">Books</h4>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="row g-0">
                <div class="col-4">
                    <img src="{{site.baseurl}}/assets/images/cover-enterprise-process-orchestration.jpg" class="book-cover" alt="Enterprise Process Orchestration">
                </div>
                <div class="col-8">
                    <div class="card-body">
                        <h5 class="card-title"><a href="https://www.amazon.com/Enterprise-Process-Orchestration-Hands-Technology/dp/1394309678/" target="_blank" class="text-dark">Enterprise Process Orchestration</a></h5>
                        <p class="card-text"><em>A hands-on guide to strategy, team structures, and technology that will transform your business</em></p>
                        <p class="card-text"><small class="text-muted"><b>Wiley</b>, 2024</small></p>
                        <a href="https://www.amazon.com/Enterprise-Process-Orchestration-Hands-Technology/dp/1394309678/" class="btn btn-sm btn-outline-primary" target="_blank">View on Amazon</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="row g-0">
                <div class="col-4">
                    <img src="{{site.baseurl}}/assets/images/practical-process-automation.jpg" class="book-cover" alt="Practical Process Automation">
                </div>
                <div class="col-8">
                    <div class="card-body">
                        <h5 class="card-title"><a href="https://www.amazon.com/dp/149206145X" target="_blank" class="text-dark">Practical Process Automation</a></h5>
                        <p class="card-text"><em>Orchestration and Integration in Microservices and Cloud Native Architectures</em></p>
                        <p class="card-text"><small class="text-muted"><b>O'Reilly</b> Media, 2021</small></p>
                        <a href="https://www.amazon.com/dp/149206145X" class="btn btn-sm btn-outline-primary" target="_blank">View on Amazon</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="row g-0">
                <div class="col-4">
                    <img src="{{site.baseurl}}/assets/images/real-life-bpmn.jpg" class="book-cover" alt="Real-Life BPMN">
                </div>
                <div class="col-8">
                    <div class="card-body">
                        <h5 class="card-title"><a href="https://www.amazon.com/dp/B0F9YP459S" target="_blank" class="text-dark">Real-Life BPMN</a></h5>
                        <p class="card-text"><em>A practical guide that helps business and IT professionals model and improve processes using BPMN</em></p>
                        <p class="card-text"><small class="text-muted">Independant, English, 5th Edition, 2025</small></p>
                        <a href="https://www.amazon.com/dp/B0F9YP459S" class="btn btn-sm btn-outline-primary" target="_blank">View on Amazon</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="row g-0">
                <div class="col-4">
                    <img src="{{site.baseurl}}/assets/images/cover-praxhishandbuch-bpmn.jpg" class="book-cover" alt="Praxishandbuch BPMN">
                </div>
                <div class="col-8">
                    <div class="card-body">
                        <h5 class="card-title"><a href="https://www.amazon.de/dp/3446482490" target="_blank" class="text-dark">Praxishandbuch BPMN</a></h5>
                        <p class="card-text"><em>A practical guide that helps business and IT professionals model and improve processes using BPMN</em></p>
                        <p class="card-text"><small class="text-muted"><b>Hanser</b>, German, 6th Edition, 2025</small></p>
                        <a href="https://www.amazon.de/dp/3446482490" class="btn btn-sm btn-outline-primary" target="_blank">View on Amazon</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<h4 class="mt-5">Co-founder of Camunda</h4>

I co-founded Camunda in 2008 as a consulting company focused on business process management (BPM). In 2013, we pivoted toward building an open-source workflow engine—and that shift defined our trajectory. Today, Camunda is the leading process orchestration and automation platform, shaping Agentic Automation to operationalize Agentic AI and bring LLM-powered intelligence into core business processes. Unlike proprietary, closed vendors, we’ve always built Camunda as an open, developer-friendly platform. Now, thousands of organizations worldwide use Camunda to design, automate, and optimize complex processes at scale—staying open, composable, and adaptable in a fast-changing world. Our mission remains the same as on day one: to make process orchestration open, flexible, and powerful for everyone.


<p class="mt-3">
    <a href="https://camunda.com/" target="_blank" class="btn btn-primary">
        <i class="fas fa-external-link-alt"></i> Visit Camunda
    </a>
</p>

<h4 class="mt-5">Bios for CFPs & Speaking Engagements</h4>

<p class="text-muted small">Ready to copy and paste. Click to expand language, then use the copy button for your preferred bio version.</p>

<!-- ENGLISH BIOS -->
<div class="bio-section">
    <div class="bio-language" onclick="toggleBioLanguage(this)">
        <span>English Bios</span>
        <span class="bio-chevron">▼</span>
    </div>
    <div class="bio-content" style="display: none;">
        <div class="bio-type">
            <div class="bio-type-title">Short Bio</div>
            <div class="bio-text">Bernd Ruecker is co-founder and Chief Technologist at Camunda. A skilled software developer, he has implemented large-scale process automation systems in enterprise environments, helping teams adopt automation paradigms that fit modern architectures. His current focus includes orchestration, event-driven systems, and agentic automation in distributed systems.</div>
            <button class="bio-copy-btn" onclick="copyBio(this)">📋 Copy Short Bio</button>
        </div>
        <div class="bio-type">
            <div class="bio-type-title">Long Bio</div>
            <div class="bio-text">Bernd Ruecker is co-founder and Chief Technologist at Camunda and a skilled software developer focused on process automation in distributed systems.</div>
            <div class="bio-text">Over the last 15+ years, he has implemented large-scale automation systems in enterprise environments, helping organizations fully utilize automation technology and adopt paradigms that fit modern architectures.</div>
            <div class="bio-text">Bernd is the author of Practical Process Automation and co-author of Enterprise Process Orchestration and Real-Life BPMN. He is an active open-source contributor and regular conference speaker, focusing on orchestration, event-driven architectures, agentic automation, and building reliable automation systems for production use.</div>
            <button class="bio-copy-btn" onclick="copyBio(this)">📋 Copy Long Bio</button>
        </div>
        <div class="bio-type">
            <div class="bio-type-title">Engineering-Focused</div>
            <div class="bio-text">Bernd Ruecker builds workflow engines and orchestration platforms for distributed systems. As co-founder and Chief Technologist at Camunda, he has spent over 15 years implementing large-scale automation systems and evolving orchestration concepts that work in production. His work centers on BPMN, event-driven architecture, and agentic orchestration for modern systems.</div>
            <button class="bio-copy-btn" onclick="copyBio(this)">📋 Copy Engineering Bio</button>
        </div>
    </div>
</div>

<!-- GERMAN BIOS -->
<div class="bio-section">
    <div class="bio-language" onclick="toggleBioLanguage(this)">
        <span>German Bios</span>
        <span class="bio-chevron">▼</span>
    </div>
    <div class="bio-content" style="display: none;">
        <div class="bio-type">
            <div class="bio-type-title">Kurz</div>
            <div class="bio-text">Bernd Rücker ist Mitgründer und Chief Technologist bei Camunda. Als erfahrener Softwareentwickler hat er großskalige Prozessautomatisierungssysteme umgesetzt und Teams dabei geholfen, Automatisierungskonzepte einzuführen, die zu modernen Architekturen passen. Sein aktueller Fokus liegt auf Orchestrierung, eventgetriebenen Systemen und agentischer Automatisierung.</div>
            <button class="bio-copy-btn" onclick="copyBio(this)">📋 Bio kopieren</button>
        </div>
        <div class="bio-type">
            <div class="bio-type-title">Lang</div>
            <div class="bio-text">Bernd Rücker ist Mitgründer und Chief Technologist bei Camunda und erfahrener Softwareentwickler mit Schwerpunkt auf Prozessautomatisierung in verteilten Systemen.</div>
            <div class="bio-text">Seit über 15 Jahren implementiert er skalierbare Automatisierungssysteme in Unternehmensumgebungen und unterstützt Organisationen dabei, Automatisierungstechnologien effektiv einzusetzen und Architekturmuster zu etablieren, die zu modernen Architekturen passen.</div>
            <div class="bio-text">Er ist Autor von Practical Process Automation sowie Co-Autor von Enterprise Process Orchestration und Real-Life BPMN. Als Open-Source-Contributor und regelmäßiger Konferenzsprecher beschäftigt er sich mit Orchestrierung, eventgetriebenen Architekturen, agentischer Automatisierung und robusten Automatisierungskonzepten.</div>
            <button class="bio-copy-btn" onclick="copyBio(this)">📋 Bio kopieren</button>
        </div>
        <div class="bio-type">
            <div class="bio-type-title">Technisch / Engineering</div>
            <div class="bio-text">Bernd Rücker entwickelt Workflow-Engines und Orchestrierungsplattformen für verteilte Systeme. Als Mitgründer und Chief Technologist bei Camunda arbeitet er seit über 15 Jahren an produktiven Automatisierungssystemen. Seine Schwerpunkte sind BPMN, eventgetriebene Architekturen und agentische Orchestrierung.</div>
            <button class="bio-copy-btn" onclick="copyBio(this)">📋 Bio kopieren</button>
        </div>
    </div>
</div>



<h4 class="mt-5">Selected talks</h4>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Lost in transaction? Strategies to manage consistency in distributed systems</h5>
                    <p class="card-text"><em>You probably work on a distributed system. Even if you don’t (yet) face a serverless microservice architecture, calling remote services leaves you in charge of consistency.</em></p>
                    <p class="card-text">
                        <a href="http://www.slideshare.net/BerndRuecker/2018-lost-in-transaction" class="btn btn-sm btn-outline-primary" target="_blank">Slides</a>
                        <a href="https://vimeo.com/289508460" class="btn btn-sm btn-outline-secondary" target="_blank">Recording</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">3 common pitfalls in microservice integration and how to avoid them</h5>
                    <p class="card-text"><em>Integrating microservices and taming distributed systems is hard. This talk highlights three real-life challenges and how to avoid them, with concrete code examples.</em></p>
                    <p class="card-text">
                        <a href="https://www.slideshare.net/BerndRuecker/3-common-pitfalls-in-microservice-integration" class="btn btn-sm btn-outline-primary" target="_blank">Slides</a>
                        <a href="https://www.youtube.com/watch?v=O2-NHptllKQ" class="btn btn-sm btn-outline-secondary" target="_blank">Recording</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Complex event flows in distributed systems</h5>
                    <p class="card-text"><em>Event-driven architectures decouple microservices, but peer-to-peer event chains for complex end-to-end logic can increase coupling and complexity.</em></p>
                    <p class="card-text">
                        <a href="https://www.slideshare.net/BerndRuecker/complex-event-flows-in-distributed-systems" class="btn btn-sm btn-outline-primary" target="_blank">Slides</a>
                        <a href="https://www.youtube.com/watch?v=EegrVoPTRbQ" class="btn btn-sm btn-outline-secondary" target="_blank">Recording</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Opportunities and Pitfalls of Event‑Driven Utopia</h5>
                    <p class="card-text"><em>Event-driven architectures come with great promises. This talk covers core concepts, advantages, and common pitfalls to watch out for.</em></p>
                    <p class="card-text">
                        <a href="https://www.slideshare.net/BerndRuecker/qcon-2019-opportunities-and-pitfalls-of-eventdriven-utopia" class="btn btn-sm btn-outline-primary" target="_blank">Slides</a>
                        <a href="https://vimeo.com/362507687" class="btn btn-sm btn-outline-secondary" target="_blank">Recording</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    </div>

<div class="col-md-4">

<div class="sticky-top sticky-top-80">
    <img class="shadow-lg" src="{{site.baseurl}}/assets/images/BerndRuecker.jpg" alt="Bernd Ruecker" style="width: 100%; border-radius: 8px;" />
    <p class="mt-4">
      <a href="https://www.linkedin.com/in/bernd-ruecker/" target="_blank" class="btn btn-primary">
        <i class="fab fa-linkedin"></i> Connect with Bernd on LinkedIn
      </a>
    </p>
    <p class="mt-2">
      <a href="https://github.com/berndruecker" target="_blank" class="btn btn-primary">
        <i class="fab fa-github"></i> Follow Bernd on GitHub
      </a>
    </p>

</div>
</div>
</div>

<script>
function toggleBioLanguage(element) {
    const content = element.nextElementSibling;
    const chevron = element.querySelector('.bio-chevron');
    
    if (content.style.display === 'none' || content.style.display === '') {
        content.style.display = 'block';
        element.classList.remove('collapsed');
    } else {
        content.style.display = 'none';
        element.classList.add('collapsed');
    }
}

function copyBio(button) {
    // Find the bio text (all paragraphs before the button)
    let bioText = '';
    let sibling = button.previousElementSibling;
    
    while (sibling && sibling.classList.contains('bio-text')) {
        bioText = sibling.textContent + '\n\n' + bioText;
        sibling = sibling.previousElementSibling;
    }
    
    bioText = bioText.trim();
    
    // Copy to clipboard
    navigator.clipboard.writeText(bioText).then(function() {
        const originalText = button.textContent;
        button.textContent = '✓ Copied!';
        button.classList.add('copied');
        
        setTimeout(function() {
            button.textContent = originalText;
            button.classList.remove('copied');
        }, 2000);
    }).catch(function(err) {
        // Fallback for older browsers
        const tempInput = document.createElement('textarea');
        tempInput.value = bioText;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        
        button.textContent = '✓ Copied!';
        button.classList.add('copied');
        setTimeout(function() {
            const isEnglish = button.textContent.includes('English');
            button.textContent = button.textContent.includes('kopieren') ? '📋 Bio kopieren' : '📋 Copy';
            button.classList.remove('copied');
        }, 2000);
    });
}
</script>
