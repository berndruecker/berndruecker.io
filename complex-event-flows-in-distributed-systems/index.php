<?php
  include("../header.php"); 
?>

<script type="text/javascript">
  var talkName = 'Complex event flows';
  var limit = 100;  
  var speaker = 'Bernd';
</script>

    <div class="container ptb">


      <div class="row">
        <div class="col-md-12">
          <p class="centered mb">Talk</h4></p>
          <h1 class="centered mb">Complex event flows in distributed systems</h1>
          <p>
            Event-driven architectures enable nicely decoupled microservices and are fundamental for decentral data management. However, using peer-to-peer event chains to implement complex end-to-end logic crossing service boundaries can accidentally increase coupling. Extracting such business logic into dedicated services reduces coupling and allows to keep sight of larger-scale flows - without violating bounded contexts, harming service autonomy or introducing god services. Service boundaries get clearer and service APIs get smarter by focusing on their potentially long running nature. I will demonstrate how the new generation of lightweight and highly-scalable state machines ease the implementation of long running services. Based on my real-life experiences, I will share how to handle complex logic and flows which require proper reactions on failures, timeouts and compensating actions and provide guidance backed by code examples to illustrate alternative approaches.
          </p>
        </div>
      </div>


      <div class="row">
        <div class="col-md-12">
            <h2>Source Code: <a href="https://github.com/berndruecker/flowing-retail" target="_blank">https://github.com/berndruecker/flowing-retail</a></h2>
        </div>
      </div> <!-- row -->

      

      <div class="row">
        
        <div class="col-md-4">
          <h2><a href="https://www.slideshare.net/BerndRuecker/complex-event-flows-in-distributed-systems" target="_blank">Slides</a></h2>

          <iframe src="https://www.slideshare.net/slideshow/embed_code/key/49kkFOzZ289Ke0" width="100%" height="250" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="border:1px solid #CCC; border-width:1px; margin-bottom:5px; max-width: 100%;" allowfullscreen> </iframe> 
        </div>
        <div class="col-md-4">

          <h2><a href="https://www.youtube.com/watch?v=O2-NHptllKQ&feature=youtu.be">Recording </a></h2>

          <div style="padding:56.25% 0 0 0;position:relative;">
          <iframe width="560" height="315" src="https://www.youtube.com/embed/EegrVoPTRbQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" style="position:absolute;top:0;left:0;width:100%;height:100%;"  allowfullscreen></iframe>    
          Alternative: <a href="https://www.infoq.com/presentations/event-flow-distributed-systems">QCon New York City</a>     
          </div>

        </div>
        <div class="col-md-4">

            <h2><a href="https://www.infoq.com/articles/events-workflow-automation" target="_blank">Article</a></h2>

              <div style="text-align: center;">
                <img src="https://res.infoq.com/articles/events-workflow-management/en/resources/6fig3-1513669723708.png" class="blog-preview">
              </div>

              <h4><a href="https://www.infoq.com/articles/events-workflow-automation">Events, Flows and Long-Running Services: A Modern Approach to Workflow Automation</a></h4>

              <p>
                If you have been following the recent discussions around the microservice architectural style, you may have heard this advice: "To effectively decouple your (micro)services you have to create an event-driven-architecture". The idea is backed by the Domain-Driven Design (DDD) community, by providing the nuts and bolts for leveraging domain events and by showing how they change the way we think about systems.

                Although we are generally supportive of event orientation, we asked ourselves what risks arise if we use them without further reflection. To answer this question we reviewed three common hypotheses...
              </p>
              <p>
                <a href="https://www.infoq.com/articles/events-workflow-automation">Read more...</a>
              </p>
        </div>     
      </div> <!-- row -->



      <div class="row" id="app"> <!-- vue.js app for dynamic content -->
        <div class="col-md-6" id="app"> 
          
              <h3>Upcoming gigs:</h3>
              <table class="table" style="font-size: 18px; color:#666;">
                
                <tr v-for="item in filterUpcoming(talks)">
                    <td>
                           <a v-bind:href="item['fields']['Agenda Link']">{{ item['fields']['Name'] }}  {{ item['fields']['Location'] }}</a>
                           
                           <span v-if="item['fields']['Language']=='en'" title='In English language'><img src='../assets/img/en.png' height='20px'></span>

                           <span v-if="item['fields']['Language']=='de'" title='In German language'><img src='../assets/img/de.png' height='20px'></span>

                           <br>{{ item['fields']['Talk-Date'] }}
                    </td>
                </tr> 

              </table>
            </div>
            <div class="col-md-6"> 


              <h3>Recently delivered at:</h3>
              <table class="table" style="font-size: 18px; color:#666;">
                
                <tr v-for="item in filterDone(talks)">
                    <td>
                           <a v-bind:href="item['fields']['Agenda Link']">{{ item['fields']['Name'] }}  {{ item['fields']['Location'] }}</a>
                           
                           <span v-if="item['fields']['Language']=='en'" title='In English language'><img src='../assets/img/en.png' height='20px'></span>

                           <span v-if="item['fields']['Language']=='de'" title='In German language'><img src='../assets/img/de.png' height='20px'></span>

                           <br>{{ item['fields']['Talk-Date'] }}
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
        
          
        </div>
      </div>
    </div>

      </div><!--/row-->
    </div><!-- /.container -->
    

<?php 
include("../footer.php");
?>