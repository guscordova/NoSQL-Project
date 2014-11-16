<?php

// Include Class File
require_once('OAuthTwitter.php');
require_once('MapJSON.php');
require_once('Database.php');


//Set Access Tokens
//How to Generate Access Tokens: http://iag.me/socialmedia/how-to-create-a-twitter-app-in-8-easy-steps/
$settings = array(
    'consumer_key' => "",
    'consumer_secret' => "",
    'token' => "",
    'token_secret' => ""
);


//Search Using a Geocode
//Official documentation: https://dev.twitter.com/docs/api/1.1/get/search/tweets

//URL
$url = 'https://api.twitter.com/1.1/search/tweets.json';
//$url = 'https://stream.twitter.com/1.1/statuses/sample.json';
//Latitud, Longitud y Radio
// Monterrey lat 25.6750600 lon -100.3184600
$latlonrad = '25.6750600,-100.3184600,300mi';

$count = '100'; 
$twitter = new OAuthTwitter($settings);
$response =  $twitter->performRequest($url,$latlonrad,$count);

$json = json_decode($response, true);
if ($json) {
	print_r($json);
}

$map = new MapJSON();
$tweetsWithHashtags = array();
$tweetsWithHashtags = $map->Map($tweetsWithHashtags, $json["statuses"]);
print_r($tweetsWithHashtags);

$database = new Database(['127.0.0.1:9042']);
$database->connect();
$database->setKeyspace('bdd');

foreach ($tweetsWithHashtags as $tweet) {
	$hasCoodinates = !empty($tweet->long);
	$isRetweet = !empty($tweet->retweetCount);
	$database->beginBatch();
	if ($hasCoodinates && $isRetweet) {
		$database->query(
    		'INSERT INTO "tweet" ("id", "hashtag", "createdat", "lat", "long", "place", "retweetcount", "text") 
    			VALUES (:id, :hashtag, :createdat, :lat, :long, :place, :retweetcount, :text);',
    		[
	        	'id' => $tweet->id,
		        'hashtag' => $tweet->hashtag,
	    	    'createdat' => $tweet->createdAt,
	    	    'lat' => $tweet->lat,
	    	    'long' => $tweet->long,
	    	    'place' => $tweet->place,
	    	    'retweetcount' => $tweet->retweetCount,
    	    	'text' => $tweet->text
 	   		]
		);
	} else if ($hasCoodinates) {
		$database->query(
    		'INSERT INTO "tweet" ("id", "hashtag", "createdat", "lat", "long", "place", "text") 
    			VALUES (:id, :hashtag, :createdat, :lat, :long, :place, :text);',
    		[
	        	'id' => $tweet->id,
		        'hashtag' => $tweet->hashtag,
	    	    'createdat' => $tweet->createdAt,
	    	    'lat' => $tweet->lat,
	    	    'long' => $tweet->long,
	    	    'place' => $tweet->place,
    	    	'text' => $tweet->text
 	   		]
		);
	} else if ($isRetweet) {
		$database->query(
    		'INSERT INTO "tweet" ("id", "hashtag", "createdat", "retweetcount", "text") 
    			VALUES (:id, :hashtag, :createdat, :retweetcount, :text);',
    		[
	        	'id' => $tweet->id,
		        'hashtag' => $tweet->hashtag,
	    	    'createdat' => $tweet->createdAt,
	    	    'retweetcount' => $tweet->retweetCount,
    	    	'text' => $tweet->text
 	   		]
		);
	} else {
		$database->query(
    		'INSERT INTO "tweet" ("id", "hashtag", "createdat", "text") VALUES (:id, :hashtag, :createdat, :text);',
    		[
	        	'id' => $tweet->id,
		        'hashtag' => $tweet->hashtag,
	    	    'createdat' => $tweet->createdAt,
    	    	'text' => $tweet->text
 	   		]
		);
	}
	$result = $database->applyBatch();
}
//$tweets = json_decode($response);

//STREAMING Tweets by Keyword
//$twitter->start(array('Apple', 'keyword2', 'etc'));
?>