<?php

// Include Class File
require_once('OAuthTwitter.php');


//Set Access Tokens
//How to Generate Access Tokens: http://iag.me/socialmedia/how-to-create-a-twitter-app-in-8-easy-steps/
$settings = array(
    'consumer_key' => "CONSUMER-KEY-HERE",
    'consumer_secret' => "CONSUMER-SECRET-HERE",
    'token' => "TOKEN-HERE",
    'token_secret' => "TOKEN-SECRET-HERE"
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

print $response;
//$tweets = json_decode($response);

//STREAMING Tweets by Keyword
//$twitter->start(array('keyword', 'keyword2', 'etc'));
?>