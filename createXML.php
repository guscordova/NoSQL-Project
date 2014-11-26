<?php

// Include Class File
require_once('Database.php');

$database = new Database(['127.0.0.1:9042']);
$database->connect();
$database->setKeyspace('bdd');

$tweets = $database->query('SELECT * FROM "tweet" ', []);
$tweetsJSON = json_encode($tweets);

$xml_tweet_info = new SimpleXMLElement("<?xml version=\"1.0\"?><tweet></tweet>");

// function call to convert array to xml
array_to_xml($tweets,$xml_tweet_info);

$xml_tweet_info->asXML(dirname(__FILE__)."/tweet.xml") ;

//$tweets = json_decode($response);

//STREAMING Tweets by Keyword
//$twitter->start(array('Apple', 'keyword2', 'etc'));




function array_to_xml($tweet_info, &$xml_tweet_info) {
    foreach($tweet_info as $key => $value) {
        if(is_array($value)) {
            if(!is_numeric($key)){
                $subnode = $xml_tweet_info->addChild("$key");
                array_to_xml($value, $subnode);
            }
            else{
                $subnode = $xml_tweet_info->addChild("item$key");
                array_to_xml($value, $subnode);
            }
        }
        else {
            $xml_tweet_info->addChild("$key",htmlspecialchars("$value"));
        }
    }
}

?>