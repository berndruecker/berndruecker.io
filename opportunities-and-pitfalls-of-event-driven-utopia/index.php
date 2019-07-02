<?php

  $metaTitle = "Opportunities and Pitfalls of Event-Driven Utopia";
  $metaDescription = "Talk that gives an overview of what event-driven might mean and what the benefits and pitfalls are. Held e.g. at QCon and others";
  $metaKeywords = "Microservices, Event-Driven Architecture, Event Sourcing, Orchestration, Choreography";
  $metaUrl = "https://berndruecker.io/opportunities-and-pitfalls-of-event-driven-utopia/";

  include("../header.php"); 
?>

<script type="text/javascript">
  var talkName = 'Event-Driven Utopia';
  var limit = 100;  
  var speaker = 'Bernd';
</script>

    <div class="container ptb">


      <div class="row">
        <div class="col-md-12">
          <p class="centered mb">Talk</h4></p>
          <h1 class="centered mb">Opportunities and Pitfalls of Event-Driven Utopia</h1>
          <p>
            Event-driven architectures are on the rise. They promise both better decoupling of components by using an event bus and improved scalability in terms of throughput. Decoupled modules help to scale your software development efforts itself. Event streaming promises to handle ever-growing amounts of "data in motion" in real-time, event sourcing allows us to time travel, and domain events have turned out to be powerful building blocks that lead to a better understanding of underlying business requirements.
          </p>
          <p>
            But there are also pitfalls that you’d better be aware of. For example event-notifications used inappropriately can lead to tighter coupling or cyclic dependencies between components. It is also easy to lose sight of flows across service boundaries, making it hard to understand how core business logic is actually implemented. This can get even worse if you lack tooling to get insights into your event flows. Last but not least, the event-driven approach is not well-understood by most developers or business analysts, making it hard for companies to adopt. In this talk, I will quickly go over the concepts, the advantages, and the pitfalls of event-driven utopia. Whenever possible, I will share real-life stories or point to source code examples.
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
          <h2><a href="https://www.slideshare.net/BerndRuecker/qcon-2019-opportunities-and-pitfalls-of-eventdriven-utopia/" target="_blank">Slides</a></h2>

          <iframe src="https://www.slideshare.net/slideshow/embed_code/key/fK11bckLsNzq3X" width="100%" height="250" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="border:1px solid #CCC; border-width:1px; margin-bottom:5px; max-width: 100%;" allowfullscreen> </iframe> 
        </div>
        <div class="col-md-4">

          <h2>Recording coming soon</a></h2>


        </div>
        <div class="col-md-4">

            <h2>Articles coming soon</h2>

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