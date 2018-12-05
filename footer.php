
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://unpkg.com/vue"></script>
<script type="text/javascript">

<?php
include("airtable-key.php");

$url = "https://api.airtable.com/v0/" . $base . "/Conferences?view=Talks%20Homepage" . "&sort%5B0%5D%5Bfield%5D=Talk-Date&sort%5B0%5D%5Bdirection%5D=desc";

$headers = array(
    'Authorization: Bearer ' . $api_key
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPGET, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_URL, $url);
$entries = curl_exec($ch);
curl_close($ch);
?>

           var talksData = JSON.parse(<?php echo json_encode($entries) ?>).records;

           function sortTalkDate(a, b) {
                        if (!a['fields']['Talk-Date']) {
                            a['fields']['Talk-Date'] = a['fields']['Date from'];
                        }
                        if (!b['fields']['Talk-Date']) {
                            b['fields']['Talk-Date'] = b['fields']['Date from'];
                        }
                        if (!a['fields']['Talk-Date']) {
                            return -1;
                        }
                        if (!b['fields']['Talk-Date']) {
                            return 1;
                        } 
                        return new Date(a['fields']['Talk-Date']) - new Date(b['fields']['Talk-Date']);
                    };

            var app = new Vue({
                el: '#app',
                data: {
                    talks: talksData
                },                
                methods: {
                    filterUpcoming: function (talks) {
                      var filteredTaks = talks.filter(function (item) {
                        if (talkName && item['fields']['Talk Shorttitle']!=talkName) {
                          return false;
                        }
                        return (item['fields']['Talk Status']=='accepted' && item['fields']['Speaker']==speaker);                        
                      });
                      var sortedTalks = filteredTaks.sort(function(a, b) {
                        return sortTalkDate(a, b);
                      });
                      var pagedTalks = sortedTalks.slice(0, limit);
                      return pagedTalks;
                    },
                    filterDone: function (talks) {
                      var filteredTaks = talks.filter(function (item) {
                        if (talkName && item['fields']['Talk Shorttitle']!=talkName) {
                          return false;
                        }
                        return (item['fields']['Talk Status']=='done' && item['fields']['Speaker']==speaker);                        
                      });
                      var sortedTalks = filteredTaks.sort(function(a, b) {
                        return sortTalkDate(b, a);
                      });
                      var pagedTalks = sortedTalks.slice(0, limit);
                      return pagedTalks;
                    }
                }
            })
    </script>


<!-- Matomo -->
<script type="text/javascript">
  var _paq = _paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//berndruecker.io/stats/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', '1']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Matomo Code -->
<!-- Matomo Image Tracker-->
<noscript>
<img src="https://berndruecker.io/stats/piwik.php?idsite=1&rec=1" style="border:0" alt="" />
</noscript>
<!-- End Matomo -->

  </body>
</html>
