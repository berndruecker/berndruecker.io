<script type="text/javascript">
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

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>

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
