<?php

// Include Class File
require_once('Database.php');

function parseToXML($htmlStr)
{
$xmlStr=str_replace('<','&lt;',$htmlStr);
$xmlStr=str_replace('>','&gt;',$xmlStr);
$xmlStr=str_replace('"','&quot;',$xmlStr);
$xmlStr=str_replace("'",'&#39;',$xmlStr);
$xmlStr=str_replace("&",'&amp;',$xmlStr);
return $xmlStr;
}

$database = new Database(['127.0.0.1:9042']);
$database->connect();
$database->setKeyspace('bdd');

// Arturo: Adaptar query Cassandra a como se desee
$tweets = $database->query('SELECT * FROM "tweet" WHERE HASHTAG=\'YaMeCanse\'', []);

header("Content-type: text/xml");

// Generar el XML que requiere Google Maps para mapear
echo '<markers>';

foreach($tweets[0] as $child) {
   echo $child . "\n";
}
// Iterate through the rows, printing XML nodes for each
/*
while ($row = @mysql_fetch_assoc($result)){
  // ADD TO XML DOCUMENT NODE
  echo '<marker ';
  echo 'name="' . parseToXML($row['name']) . '" ';
  echo 'address="' . parseToXML($row['address']) . '" ';
  echo 'lat="' . $row['lat'] . '" ';
  echo 'lng="' . $row['lng'] . '" ';
  echo 'type="' . $row['type'] . '" ';
  echo '/>';
}
*/
// End XML file
echo '</markers>';

?>