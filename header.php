<!DOCTYPE html>
<html lang="en">
  <head>        
    <meta name="title" content="<?php echo $metaTitle; ?>">
    <meta name="description" content="<?php echo $metaDescription; ?>">
    <meta name="keywords" content="<?php echo $metaKeywords; ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:creator" content="@berndruecker">

    <meta property="og:url" content="<?php echo $metaUrl; ?>" />
    <meta property="og:title" content="<?php echo $metaTitle; ?>" />
    <meta property="og:description" content="<?php echo $metaDescription; ?>" />
    <meta property="og:image" content="<?php echo $metaUrl; ?>overview.png" />    


    <meta name="robots" content="index, follow">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="language" content="English">
    <meta name="author" content="Bernd Ruecker">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--
    <link rel="icon" href="../assets/img/favicon.ico">
    -->

    <title>Bernd Rücker: <?php echo $metaTitle; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="../assets/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../assets/css/ionicons.min.css" rel="stylesheet">
    <link href="../assets/css/font-awesome.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">

    <style type="text/css">
.blog-preview {
    height:150px;
    width:auto;/*maintain aspect ratio*/
    max-width:100%;    
}
    </style>


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../assets/js/ie10-viewport-bug-workaround.js"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.min.js"></script>
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

    </script>


    
  </head>

  <body>

    <div id="h">
      <div class="logo">
        <h2></h2>
      </div><!--/logo-->
      <div class="container centered">
        <div class="row">
          <div class="col-md-8 col-md-offset-2" style="text-align: end;">
            <h1><b>Bernd Rücker</b><br/>Passionate about developer-friendly<br />workflow automation technology.</h1>
          </div>
        </div><!--/row-->

        
      </div><!--/container-->
    </div><!--H-->

<div id="f">
      <div class="container">
        <div class="row centered">
          <div class="col-md-8 col-md-offset-2">
            <a href="../" title="Home"><i class="ion-home"></i></a>

            <a href="mailto:bernd.ruecker@camunda.com" title="Email"><i class="ion-email"></i></a>
            <a href="http://twitter.com/berndruecker/" title="Twitter"><i class="ion-social-twitter"></i></a>
            <a href="http://github.com/berndruecker" title="Github"><i class="ion-social-github"></i></a>
            <a href="https://de.linkedin.com/in/bernd-ruecker-21661122" title="LinkedIn"><i class="ion-social-linkedin"></i></a>
            <a href="https://www.xing.com/profile/Bernd_Ruecker2" title="Xing"><i class="fa fa-xing" aria-hidden="true"></i></a>
            <a href="https://de.slideshare.net/berndruecker" title="Slides"><font size="5">Slides: </font><i class="fa fa-slideshare" aria-hidden="true"></i></a>
            <a href="https://blog.bernd-ruecker.com/" title="Blog"><font size="5">Blog: </font><i class="fa fa-medium" aria-hidden="true"></i></a>

            <a href="../bio.php" title="Bio"><font size="5">About me</font></a>

          </div><!--/col-md-8-->
        </div>
      </div><!--/container-->
    </div><!--/.F-->
<!--    
	<div style="background: #f5f5f5; padding-top: 10px;">
	      <div class="container">
	        <div class="row centered">
	          <div class="col-md-8 col-md-offset-2">
<form action="//bernd-ruecker.us15.list-manage.com/subscribe/post?u=d444657f2bddfe1694c4ae4af&amp;id=5e19c93416" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
	          	Newsletter: 
            <input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
              <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_d444657f2bddfe1694c4ae4af_5e19c93416" tabindex="-1" value=""></div>
            <input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button">
          </form>	          	
			  </div>
			</div>
		</div>
	</div>
    <div class="container ptb">
      <div class="row centered">
    <a href="http://bernd-ruecker.com/feedback" title="Feedback"><img src="assets/img/feedback.png"></a>
      </div>
    </div>
-->
