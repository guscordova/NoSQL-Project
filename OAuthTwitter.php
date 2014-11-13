<?php
/**
*	Oauth.php
*	created by Jon Hurlock on 2013-03-20.
* 	Source: https://github.com/jonhurlock/Twitter-Application-Only-Authentication-OAuth-PHP/blob/master/Oauth.php
*	Jon Hurlock's Twitter Application-only Authentication App by Jon Hurlock (@jonhurlock)
*	is licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
*	Permissions beyond the scope of this license may be available at http://www.jonhurlock.com/
*
*	Modified by Gustavo Córdova on 2014-11-12
*	
*
*/

class OAuthTwitter
{
    private $m_oauth_consumer_key;
    private $m_oauth_consumer_secret;
    private $m_oauth_token;
    private $m_oauth_token_secret;

    private $m_oauth_nonce;
    private $m_oauth_signature;
    private $m_oauth_signature_method = 'HMAC-SHA1';
    private $m_oauth_timestamp;
    private $m_oauth_version = '1.0';


    /**
     * Create the API access object. Requires an array of settings:
     * consumer key, consumer secret
     * These are all available by creating your own application on apps.twitter.com
     * 
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        if (!isset($settings['consumer_key'])
            || !isset($settings['consumer_secret'])
            || !isset($settings['token'])
            || !isset($settings['token_secret']))
        {
            throw new Exception('Incorrect parameters');
        }

        $this->m_oauth_consumer_key = $settings['consumer_key'];
        $this->m_oauth_consumer_secret = $settings['consumer_secret'];
        $this->m_oauth_token = $settings['token'];
        $this->m_oauth_token_secret = $settings['token_secret'];
        
        //
        // generate a nonce; we're just using a random md5() hash here.
        //
        $this->m_oauth_nonce = md5(mt_rand());
    }
    
    /**
	*	Get the Bearer Token, this is an implementation of steps 1&2
	*	from https://dev.twitter.com/docs/auth/application-only-auth
	*/
	private function getToken(){
		// Step 1
		// step 1.1 - url encode the consumer_key and consumer_secret in accordance with RFC 1738
		$encoded_consumer_key = urlencode($this->m_oauth_consumer_key);
		$encoded_consumer_secret = urlencode($this->m_oauth_consumer_secret);
		// step 1.2 - concatinate encoded consumer, a colon character and the encoded consumer secret
		$bearer_token = $encoded_consumer_key.':'.$encoded_consumer_secret;
		// step 1.3 - base64-encode bearer token
		$base64_encoded_bearer_token = base64_encode($bearer_token);
		// step 2
		$url = "https://api.twitter.com/oauth2/token"; // url to send data to for authentication
		$headers = array( 
			"POST /oauth2/token HTTP/1.1", 
			"Host: api.twitter.com", 
			"User-Agent: jonhurlock Twitter Application-only OAuth App v.1",
			"Authorization: Basic ".$base64_encoded_bearer_token."",
			"Content-Type: application/x-www-form-urlencoded;charset=UTF-8", 
			"Content-Length: 29"
		); 
		$ch = curl_init();  // setup a curl
		curl_setopt($ch, CURLOPT_URL,$url);  // set url to send to
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
		curl_setopt($ch, CURLOPT_POST, 1); // send as post
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return output
		curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials"); // post body/fields to be sent
		$header = curl_setopt($ch, CURLOPT_HEADER, 1); // send custom headers
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$retrievedhtml = curl_exec ($ch); // execute the curl
		curl_close($ch); // close the curl
		$output = explode("\n", $retrievedhtml);
		$bearer_token = '';
		foreach($output as $line)
		{
			if($line === false)
			{
				// there was no bearer token
			}else{
				$bearer_token = $line;
			}
		}
		$bearer_token = json_decode($bearer_token);
		return $bearer_token->{'access_token'};
	}
	
	/**
	* Invalidates the Bearer Token
	* Should the bearer token become compromised or need to be invalidated for any reason,
	* call this method/function.
	*
	* Modified on Nov 12, 2014 by Gustavo Córdova 
	*/
	private function invalidateToken($bearer_token){
		$encoded_consumer_key = urlencode($this->m_oauth_consumer_key);
		$encoded_consumer_secret = urlencode($this->m_oauth_consumer_secre);
		$consumer_token = $encoded_consumer_key.':'.$encoded_consumer_secret;
		$base64_encoded_consumer_token = base64_encode($consumer_token);
		// step 2
		$url = "https://api.twitter.com/oauth2/invalidate_token"; // url to send data to for authentication
		$headers = array( 
			"POST /oauth2/invalidate_token HTTP/1.1", 
			"Host: api.twitter.com", 
			"User-Agent: jonhurlock Twitter Application-only OAuth App v.1",
			"Authorization: Basic ".$base64_encoded_consumer_token."",
			"Accept: */*", 
			"Content-Type: application/x-www-form-urlencoded", 
				"Content-Length: ".(strlen($bearer_token)+13).""
		); 
    
		$ch = curl_init();  // setup a curl
		curl_setopt($ch, CURLOPT_URL,$url);  // set url to send to
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
		curl_setopt($ch, CURLOPT_POST, 1); // send as post
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return output
		curl_setopt($ch, CURLOPT_POSTFIELDS, "access_token=".$bearer_token.""); // post body/fields to be sent
		$header = curl_setopt($ch, CURLOPT_HEADER, 1); // send custom headers
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$retrievedhtml = curl_exec ($ch); // execute the curl
		curl_close($ch); // close the curl
		return $retrievedhtml;
	}

	/**
	* Search
	* Basic Search of the Search API
	* Based on https://dev.twitter.com/docs/api/1.1/get/search/tweets
	*/
	public function performRequest($url, $latlonrad, $count, $result_type='mixed'){
		$bearer_token = $this->getToken();
		
		//$url = "https://api.twitter.com/1.1/search/tweets.json"; // base url
		//$q = urlencode(trim($query)); // query term
		//$formed_url ='?q='.$q; // fully formed url
		
		$geocode = urlencode(trim($latlonrad));
		$formed_url = '?geocode='.$geocode;
		if($result_type!='mixed'){
			$formed_url = $formed_url.'&result_type='.$result_type;
		} // result type - mixed(default), recent, popular
		
		$formed_url = $formed_url.'&count='.$count;
		
			$formed_url = $formed_url.'&include_entities=true'; // makes sure the entities are included, note @mentions are not included see documentation
			$headers = array( 
			"GET /1.1/search/tweets.json".$formed_url." HTTP/1.1", 
			"Host: api.twitter.com", 
			"User-Agent: jonhurlock Twitter Application-only OAuth App v.1",
			"Authorization: Bearer ".$bearer_token."",
		);
		$ch = curl_init();  // setup a curl
		curl_setopt($ch, CURLOPT_URL,$url.$formed_url);  // set url to send to
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return output
		$retrievedhtml = curl_exec ($ch); // execute the curl
		curl_close($ch); // close the curl
		
		$this->invalidateToken($bearer_token);
		return $retrievedhtml;
	}
	
	//
    // process a tweet object from the stream
    //
    private function process_tweet(array $_data)
    {
        //print_r($_data);
		print_r("tweet\n");
		print_r($_data[geo]);
        return true;
    }
	
	//
    // the main stream manager
    //
    public function start(array $_keywords)
    {
        while(1)
        {
            $fp = fsockopen("ssl://stream.twitter.com", 443, $errno, $errstr, 30);
            if (!$fp)
            {
                echo "ERROR: Twitter Stream Error: failed to open socket";
            } else
            {
                //
                // build the data and store it so we can get a length
                //
                $data = 'track=' . rawurlencode(implode($_keywords, ','));

                //
                // store the current timestamp
                //
                $this->m_oauth_timestamp = time();

                //
                // generate the base string based on all the data
                //
                $base_string = 'POST&' . 
                    rawurlencode('https://stream.twitter.com/1.1/statuses/filter.json') . '&' .
                    rawurlencode('oauth_consumer_key=' . $this->m_oauth_consumer_key . '&' .
                        'oauth_nonce=' . $this->m_oauth_nonce . '&' .
                        'oauth_signature_method=' . $this->m_oauth_signature_method . '&' . 
                        'oauth_timestamp=' . $this->m_oauth_timestamp . '&' .
                        'oauth_token=' . $this->m_oauth_token . '&' .
                        'oauth_version=' . $this->m_oauth_version . '&' .
                        $data);

                //
                // generate the secret key to use to hash
                //
                $secret = rawurlencode($this->m_oauth_consumer_secret) . '&' . 
                    rawurlencode($this->m_oauth_token_secret);

                //
                // generate the signature using HMAC-SHA1
                //
                // hash_hmac() requires PHP >= 5.1.2 or PECL hash >= 1.1
                //
                $raw_hash = hash_hmac('sha1', $base_string, $secret, true);

                //
                // base64 then urlencode the raw hash
                //
                $this->m_oauth_signature = rawurlencode(base64_encode($raw_hash));

                //
                // build the OAuth Authorization header
                //
                $oauth = 'OAuth oauth_consumer_key="' . $this->m_oauth_consumer_key . '", ' .
                        'oauth_nonce="' . $this->m_oauth_nonce . '", ' .
                        'oauth_signature="' . $this->m_oauth_signature . '", ' .
                        'oauth_signature_method="' . $this->m_oauth_signature_method . '", ' .
                        'oauth_timestamp="' . $this->m_oauth_timestamp . '", ' .
                        'oauth_token="' . $this->m_oauth_token . '", ' .
                        'oauth_version="' . $this->m_oauth_version . '"';

                //
                // build the request
                //
                $request  = "POST /1.1/statuses/filter.json HTTP/1.1\r\n";
                $request .= "Host: stream.twitter.com\r\n";
                $request .= "Authorization: " . $oauth . "\r\n";
                $request .= "Content-Length: " . strlen($data) . "\r\n";
                $request .= "Content-Type: application/x-www-form-urlencoded\r\n\r\n";
                $request .= $data;

                //
                // write the request
                //
                fwrite($fp, $request);

                //
                // set it to non-blocking
                //
                stream_set_blocking($fp, 0);

                while(!feof($fp))
                {
                    $read   = array($fp);
                    $write  = null;
                    $except = null;

                    //
                    // select, waiting up to 10 minutes for a tweet; if we don't get one, then
                    // then reconnect, because it's possible something went wrong.
                    //
                    $res = stream_select($read, $write, $except, 600, 0);
                    if ( ($res == false) || ($res == 0) )
                    {
                        break;
                    }

                    //
                    // read the JSON object from the socket
                    //
                    $json = fgets($fp);

                    //
                    // look for a HTTP response code
                    //
                    if (strncmp($json, 'HTTP/1.1', 8) == 0)
                    {
                        $json = trim($json);
                        if ($json != 'HTTP/1.1 200 OK')
                        {
                            echo 'ERROR: ' . $json . "\n";
                            return false;
                        }
                    }

                    //
                    // if there is some data, then process it
                    //
                    if ( ($json !== false) && (strlen($json) > 0) )
                    {
                        //
                        // decode the socket to a PHP array
                        //
                        $data = json_decode($json, true);
                        if ($data)
                        {
                            //
                            // process it
                            //
                            if(is_array($data)) {
                            	$this->process_tweet($data);
                            } else {
                            	//print $data;
                            }
                        }
                    }
                }
            }

            fclose($fp);
            sleep(10);
        }

        return;
    }
    
}

?>