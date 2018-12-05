<?php
  include("../header.php"); 
?>

<script type="text/javascript">
  var talkName = '3 pitfalls';
  var limit = 100;
  var speaker = 'Bernd';
</script>

   <div class="container ptb">
      <div class="row">
        <div class="col-md-12">
          <p class="centered mb">Talk</h4></p>
          <h1 class="centered mb">3 common pitfalls in microservice integration and how to avoid them</h1>
          <p>
            Integrating microservices and taming distributed systems is hard. In this talk I will present three challenges I've observed in real-life projects and discuss how to avoid them.
          </p>
            <p>1. Communication is complex. With everything being distributed failures are normal so you need sophisticated failure handling strategies (e.g. stateful retry).</p>
            <p>2. Asynchronicity requires you to handle timeouts. This is not only about milliseconds, systems get much more resilient when you can wait for minutes, hours or even longer.</p>
            <p>3. Distributed transactions cannot simply be delegated to protocols like XA. So you need to solve the requirement to retain consistency in case of failures.</p>
            <p>
              I will not only use slides but also demonstrate concrete source code examples available on GitHub.
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
          <h2><a href="http://www.slideshare.net/BerndRuecker/2018-lost-in-transaction" target="_blank">Slides</a></h2>

           <iframe src="https://www.slideshare.net/slideshow/embed_code/key/73Corvcd54k4XH" width="100%" height="250" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="border:1px solid #CCC; border-width:1px; margin-bottom:5px; max-width: 100%;" allowfullscreen> </iframe> 
        </div>
        <div class="col-md-4">

          <h2><a href="https://www.youtube.com/watch?v=O2-NHptllKQ&feature=youtu.be">Recording</a></h2>

          <div style="padding:56.25% 0 0 0;position:relative;">
          <iframe width="560" height="315" src="https://www.youtube.com/embed/O2-NHptllKQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" style="position:absolute;top:0;left:0;width:100%;height:100%;"  allowfullscreen></iframe>
          </div>

        </div>
        <div class="col-md-4">

            <h2><a href="https://www.infoworld.com/article/3254777/application-development/3-common-pitfalls-of-microservices-integrationand-how-to-avoid-them.html" target="_blank">Article</a></h2>

              <div style="text-align: center;">
                <img src="https://images.idgesg.net/images/article/2017/05/businessman_bridges_gap_problem_challenge_thinkstock_496726041-100724484-large.jpg" class="blog-preview">
              </div>

              <h4><a href="https://www.infoworld.com/article/3254777/application-development/3-common-pitfalls-of-microservices-integrationand-how-to-avoid-them.html">InfoWorld: 3 common pitfalls in microservice integration and how to avoid them</a></h4>

              <p>
                How to overcome the challenges of remote communication, asynchronicity, and transactions in microservices infrastructure
              </p>
              <p>
                <a href="https://blog.bernd-ruecker.com/saga-how-to-implement-complex-business-transactions-without-two-phase-commit-e00aa41a1b1b">Read more...</a>
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