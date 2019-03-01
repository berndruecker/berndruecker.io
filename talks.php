<?php
  $metaTitle = "Bernd Rücker";
  $metaDescription = "Homepage of Bernd Rücker, passionate about developer-friendly workflow automation technology. Co-founder and technologist of Camunda.";
  $metaKeywords = "Workflow Automation";
  $metaUrl = "https://berndruecker.io/";

  include("./header.php"); 
?>

<script type="text/javascript">
  var talkName = '';
  var limit = 100;
  var speaker = 'Bernd';
</script>

 

<div style="background: #f5f5f5;" id="app">
<div class="container">
    <div class="row">
        <div class="col-md-4">
          <h2>Next talks</h2>
          
            <div v-for="item in filterUpcoming(talks)">
                <span style="white-space: nowrap;">
                  {{ item['fields']['Talk-Date'] }}
                </span>

                <span v-if="item['fields']['Language']=='en'" title='In English language'><img src='../assets/img/en.png' height='20px'></span>
                <span v-if="item['fields']['Language']=='de'" title='In German language'><img src='../assets/img/de.png' height='20px'></span>

                {{ item['fields']['Name'] }}  {{ item['fields']['Location'] }}:
                
                <a v-bind:href="item['fields']['Agenda Link']">{{ item['fields']['Talk Title'] }}</a>
            </div>
        </div>


        <div class="col-md-4">
          <h2>Past talks</h2>
            <table>
              <tr v-for="item in filterDone(talks)">    
                <td>          
                  <span style="white-space: nowrap;">
                    {{ item['fields']['Talk-Date'] }}
                  </span>

                  <span v-if="item['fields']['Language']=='en'" title='In English language'><img src='../assets/img/en.png' height='20px'></span>
                  <span v-if="item['fields']['Language']=='de'" title='In German language'><img src='../assets/img/de.png' height='20px'></span>                  

                  {{ item['fields']['Name'] }}  {{ item['fields']['Location'] }}:                               

                  <a v-bind:href="item['fields']['Agenda Link']">{{ item['fields']['Talk Title'] }}</a>
                </td>
                <td>
                  <a v-show="item['fields']['Talk Slides']" v-bind:href="item['fields']['Talk Slides']" title='Slides'><img src='../assets/img/slides.png' height='20px'></a>
                        <a v-show="item['fields']['Talk Recording']" v-bind:href="item['fields']['Talk Recording']" title='Recording'><img src='../assets/img/recording.png' height='20px'></a>
                        <a v-show="item['fields']['Talk Code']" v-bind:href="item['fields']['Talk Code']" title='Source code'><img src='../assets/img/code.png' height='20px'></a>
                        <a v-show="item['fields']['Interview']" v-bind:href="item['fields']['Interview']" title='Interview'><img src='../assets/img/interview.png' height='20px'></a>
                </td>
              </tr>
            </table>
        </div>



        <div class="col-md-4">
            <h3>Past articles and podcasts</h3>          
             <table >    

  <tr><td><p>2019-02 <span title='In English language'><img src='assets/img/en.png' height='20px'></span> InfoQ</p><a href='https://www.infoq.com/articles/monitor-workflow-collaborating-microservices'>Monitoring and Managing Workflows Across Collaborating Microservices </a></td></tr>
<tr><td><p>2019-01 <span title='In English language'><img src='assets/img/en.png' height='20px'></span> SE-Radio</p><a href='http://www.se-radio.net/2019/01/episode-351-bernd-rucker-on-orchestrating-microservices-with-workflow-management/'>Orchestrating Microservices with Workflow Management</a></td></tr>

<tr><td><p>2018-04 <span title='In English language'><img src='assets/img/en.png' height='20px'></span> TheNewStack</p><a href='https://thenewstack.io/5-workflow-automation-use-cases-you-might-not-have-considered/'>5 Workflow Automation Use Cases You Might Not Have Considered</a></td></tr>
<tr><td><p>2018-02 <span title='In English language'><img src='assets/img/en.png' height='20px'></span> InfoWorld</p><a href='https://www.infoworld.com/article/3254777/application-development/3-common-pitfalls-of-microservices-integrationand-how-to-avoid-them.html'>3 common pitfalls of microservices integration—and how to avoid them</a></td></tr>
<tr><td><p>2017-12 <span title='In English language'><img src='assets/img/en.png' height='20px'></span> InfoQ</p><a href='https://www.infoq.com/articles/events-workflow-automation'>Events, Flows and Long-Running Services: A Modern Approach to Workflow Automation</a></td></tr>
<tr><td><p>2017-06 <span title='In English language'><img src='assets/img/en.png' height='20px'></span> InfoQ</p><a href='https://www.infoq.com/articles/microservice-event-choreographies'>Know the Flow! Microservices and Event Choreographies</a></td></tr>
<tr><td><p>2016-10 <span title='In German language'><img src='assets/img/de.png' height='20px'></span> Windows Developer</p><a href='https://entwickler.de/online/windowsdeveloper/workflows-bpmn-automatisieren-301118.html'>Workflows mit BPMN effektiv automatisieren</a></td></tr>
<tr><td><p>2016-07 <span title='In German language'><img src='assets/img/de.png' height='20px'></span> Java Aktuell</p><a href='https://network.camunda.org/whitepaper/45'>BPM macht Spaß</a></td></tr>
<tr><td><p>2016-05 <span title='In German language'><img src='assets/img/de.png' height='20px'></span> Objekt Spektrum</p><a href='https://www.sigs-datacom.de/uploads/tx_dmjournals/ruecker_OS_05_16_TRBT.pdf'>Decision Model and Notation: Digitalisierung von Entscheidungen mit DMN</a></td></tr>
<tr><td><p>2016-04 <span title='In German language'><img src='assets/img/de.png' height='20px'></span> Business Technology</p><a href='https://jaxenter.de/der-brueckenschlag-warum-dmn-den-business-rules-engines-markt-aufmischt-37252'>Der Brückenschlag: Warum DMN den Business-Rules-Engines-Markt aufmischt</a></td></tr>
<tr><td><p>2016-01 <span title='In German language'><img src='assets/img/de.png' height='20px'></span> Java Magazin</p><a href=''>Quo Vadis BPM</a></td></tr>





              </table>
        </div>
    </div>
</div>
</div>





   
    

<?php 
include("./footer.php");
?>