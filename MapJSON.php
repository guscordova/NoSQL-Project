<?php

require_once('Tweet.php');

class MapJSON
{
    public function Map(array $tweetsWithHashtags, array $tweets)
    {
        foreach ($tweets as $tweet) {
	    	$retweeted = array();
	    	array_push($retweeted, $tweet["retweeted_status"]);
	    	foreach ($tweet["entities"]["hashtags"] as $hashtags) {
	    		$t = new Tweet();
				$t->id = $tweet["id_str"];
				$t->createdAt = $tweet["created_at"];
				$t->text = $tweet["text"];
				$t->long =  $tweet["coordinates"]["coordinates"][0];
				$t->lat = $tweet["coordinates"]["coordinates"][1];
				$t->place = $tweet["place"]["full_name"];
    	 		$t->hashtag = $hashtags["text"];
    	 		if (!isset($retweeted[0])) {
	    			$t->retweetCount = $tweet["retweet_count"];
	    		}
    			array_push($tweetsWithHashtags, $t);
	    	}
	    	if (isset($retweeted[0])) {
	    		$tweetsWithHashtags = $this->Map($tweetsWithHashtags, $retweeted);
	    	}
    		
		}
		return $tweetsWithHashtags;
	}
}
?>