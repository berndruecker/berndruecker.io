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

$url = "https://api.airtable.com/v0/" . $base . "/Conferences?view=ConferenceDetailsForPHP";

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

for($i=0;$i<count($data["records"]);$i++) {
  if (isset($_GET['conference']) && $_GET['conference']==$data["records"][$i]['fields']['Conference']) {
    echo('<ul>');
    echo('<li><b>Date:</b> ' . $data["records"][$i]['fields']['Date from'] . ' - ' . $data["records"][$i]['fields']['Date to'] . "</li>");
    echo('<li><b>Attendees approx:</b> ' . $data["records"][$i]['fields']['Attendees'] . "</li>");
    echo('<li><b>Booth Staff:</b> ' . implode(", ", $data["records"][$i]['fields']['Booth Staffing']) . "</li>");
    echo('<li><b>Booth Setup DRI:</b> ' . $data["records"][$i]['fields']['Booth Setup DRI'] . "</li>");
    echo('<li><b>Booth Teardown DRI:</b> ' . $data["records"][$i]['fields']['Booth Teardown DRI'] . "</li>");
    echo('</ul>');
  }
}
?>
</body>
</html>