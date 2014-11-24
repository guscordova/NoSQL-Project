<?php

// Include Class File
require_once('Database.php');

function prettyPrint($a) {
    echo "<pre>";
    print_r($a);
    echo "</pre>";
}

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
$tweets = $database->query('SELECT hashtag, place, lat, long FROM tweet WHERE HASHTAG=\'YaMeCanse\'', []);

header("Content-type: text/xml");

// Generar el XML que requiere Google Maps para mapear
echo '<markers>';

// prettyPrint($tweets);

// Iterar el arreglo para generar el XML que pide GoogleMaps
foreach($tweets as $t)
{
  echo '<marker ';
  echo 'name="' . parseToXML($t['hashtag']) . '" ';
  echo 'address="' . parseToXML($t['place']) . '" ';
  echo 'lat="' . $t['lat'] . '" ';
  echo 'lng="' . $t['long'] . '" ';
  echo '/>';
}

// End XML file
echo '</markers>';

?>
