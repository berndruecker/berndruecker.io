<?php
  include("../header.php"); 
?>

<script type="text/javascript">
  var talkName = 'Lost in transaction';
  var limit = 100;
  var speaker = 'Bernd';
</script>

    <div class="container ptb">


      <div class="row">
        <div class="col-md-12">
          <p class="centered mb">Talk</h4></p>
          <h1 class="centered mb">Lost in transaction?<br> Strategies to manage consistency in distributed systems</h1>
          <p>You probably work on a distributed system. Even if you don't yet face a serverless microservice architecture using fancy NoSQL databases, you might simply call some remote services via REST or SOAP. This leaves you in charge of dealing with consistency yourself. ACID transactions are only available locally within components and protocols like two-phase commit don’t scale. Many projects either risk adventurous inconsistencies or write a lot of code for consistency management in the application layer. In this talk I discuss these problems and go over possible solutions, including the Saga-Pattern. I will discuss recipes and frameworks that ease the management of the right level of consistency. This allows you write business logic code. Expect fun little live hacking sessions with open source components, but also real-life stories.</p>
        </div>
      </div>


      <div class="row">
        <div class="col-md-12">
            <h2>Source Code: <a href="https://github.com/berndruecker/flowing-retail" target="_blank">https://github.com/berndruecker/flowing-retail</a></h2>
        </div>
      </div> <!-- row -->

      

     <div class="row">
        
        <div class="col-md-4">
          <h2><a href="http://www.slideshare.net/BerndRuecker/2018-lost-in-transaction" target="_blank">Slides</a></h2>

          <iframe src="https://www.slideshare.net/slideshow/embed_code/key/2TCsde4jwsuy0N" width="100%" height="250" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="border:1px solid #CCC; border-width:1px; margin-bottom:5px; max-width: 100%;" allowfullscreen> </iframe> 
          <div style="margin-bottom:5px"> <strong> </strong>  </div>
        </div>
        <div class="col-md-4">

          <h2><a href="https://vimeo.com/289508460">Recording</h2>

          <div style="padding:56.25% 0 0 0;position:relative;">
            <iframe src="https://player.vimeo.com/video/289508460?color=ff9933&byline=0&portrait=0" style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
          </div><script src="https://player.vimeo.com/api/player.js"></script>

        </div>
        <div class="col-md-4">

            <h2><a href="https://blog.bernd-ruecker.com/saga-how-to-implement-complex-business-transactions-without-two-phase-commit-e00aa41a1b1b" target="_blank">Article</a></h2>


              <div style="text-align: center;">
                <img src="https://cdn-images-1.medium.com/max/600/1*ZMiv7akyM_e7BeMzPAOp_g.png" class="blog-preview">
              </div>

              <h4><a href="https://blog.bernd-ruecker.com/saga-how-to-implement-complex-business-transactions-without-two-phase-commit-e00aa41a1b1b">Saga: How to implement complex business transactions without two phase commit.</a></h4>


              <p>
            The Saga pattern describes how to solve distributed (business) transactions without two-phase-commit as this does not scale in distributed systems. The basic idea is to break the overall transaction into multiple steps or activities. Only the steps internally can be performed in atomic transactions but the overall consistency is taken care of by the Saga. The Saga has the responsibility to either get the overall business transaction completed or to leave the system in a known termination state. So in case of errors a business rollback procedure is applied which occurs by calling compensation steps or activities in reverse order.
          </p><p>
            <a href="https://blog.bernd-ruecker.com/saga-how-to-implement-complex-business-transactions-without-two-phase-commit-e00aa41a1b1b">Read more...</a>       
          </p>

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