<!DOCTYPE html>
<html lang="en">
  <head>        
    <meta name="title" content="Conference Details">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta charset="utf-8">
  </head>
  <body>

<?php
include("airtable-key.php");

$url = "https://api.airtable.com/v0/" . $base . "/Conferences?view=Conferences%20Working%20Sheet";

$headers = array(
    'Authorization: Bearer ' . $api_key
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPGET, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_URL, $url);
$result= curl_exec($ch);
curl_close($ch);

//echo $result;

$data = json_decode($result, true);

function echoIfSet($name, $array, $arrayIndex) {
    if (array_key_exists($arrayIndex, $array)) {
       echo('<li><b>' . $name . ':</b> ' . $array[$arrayIndex] . '</li>');
    } else {
       echo('<li><b>' . $name . ':</b> -</li>');
    }
}

for($i=0;$i<count($data["records"]);$i++) {
  if (isset($_GET['conference']) && $_GET['conference']==$data["records"][$i]['fields']['Conference']) {
    echo('<ul>');
    echo('<li><b>Date:</b> ' . $data["records"][$i]['fields']['Date from'] . ' -- ' . $data["records"][$i]['fields']['Date to'] . "</li>");
    echoIfSet('Attendees approx', $data["records"][$i]['fields'], 'Attendees');
    if (array_key_exists('Booth Staffing',  $data["records"][$i]['fields'])) {
      echo('<li><b>Booth Staff:</b><ul><li> ' . implode("</li><li>", $data["records"][$i]['fields']['Booth Staffing']) . "</li></ul></li>");
    } else {
       echo('<li><b>Booth Staff:</b> -</li>');
    }
    echoIfSet('Booth Setup DRI', $data["records"][$i]['fields'], 'Booth Setup DRI');
    echoIfSet('Booth Teardown DRI', $data["records"][$i]['fields'], 'Booth Teardown DRI');
    echo('</ul>');
  }
}
?>
</body>
</html>