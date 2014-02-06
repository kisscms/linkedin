<?php
/* LinkedIn for KISSCMS */
class LinkedIn {

	public $name = "linkedin";
	private $key;
	private $secret;
	private $token;
	private $api;
	private $me;
	private $oauth;
	private $cache;

	function  __construct() {

		$this->key = $GLOBALS['config']['linkedin']['key'];
	 	$this->secret = $GLOBALS['config']['linkedin']['secret'];
		$this->me = ( empty($_SESSION['access']['linkedin']['user_id']) ) ? false : $_SESSION['access']['linkedin']['user_id'];
	 	$this->token = ( empty($_SESSION['access']['linkedin']['access_token']) ) ? false : $_SESSION['access']['linkedin']['access_token'];
	 	$this->api = "https://linkedin.com/api/3.0/";
		$this->oauth = "https://linkedin.com/api/oauth/2.0/";
		$this->cache = $this->getCache();
	}


	  function getProfile($resource = "~") {
    $profile_url = $this->base_url . "/v1/people/" . $resource;
    $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "GET", $profile_url);
    $request->sign_request($this->signature_method, $this->consumer, $this->access_token);
    $auth_header = $request->to_header("https://api.linkedin.com"); # this is the realm
    # This PHP library doesn't generate the header correctly when a realm is not specified.
    # Make sure there is a space and not a comma after OAuth
    // $auth_header = preg_replace("/Authorization\: OAuth\,/", "Authorization: OAuth ", $auth_header);
    // # Make sure there is a space between OAuth attribute
    // $auth_header = preg_replace('/\"\,/', '", ', $auth_header);
    if (DEBUG) {
      echo $auth_header;
    }
    // $response will now hold the XML document
    $response = $this->httpRequest($profile_url, $auth_header, "GET");
    return $response;
  }

  function setStatus($status) {
    $status_url = $this->base_url . "/v1/people/~/current-status";
    echo "Setting status...\n";
    $xml = "<current-status>" . htmlspecialchars($status, ENT_NOQUOTES, "UTF-8") . "</current-status>";
    echo $xml . "\n";
    $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "PUT", $status_url);
    $request->sign_request($this->signature_method, $this->consumer, $this->access_token);
    $auth_header = $request->to_header("https://api.linkedin.com");
    if (DEBUG) {
      echo $auth_header . "\n";
    }
    $response = $this->httpRequest($profile_url, $auth_header, "GET");
    return $response;
  }

  # Parameters should be a query string starting with "?"
  # Example search("?count=10&start=10&company=LinkedIn");
  function search($parameters) {
    $search_url = $this->base_url . "/v1/people-search:(people:(id,first-name,last-name,picture-url,site-standard-profile-request,headline),num-results)" . $parameters;
    //$search_url = $this->base_url . "/v1/people-search?keywords=facebook";

    echo "Performing search for: " . $parameters . "<br />";
    echo "Search URL: $search_url <br />";
    $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "GET", $search_url);
    $request->sign_request($this->signature_method, $this->consumer, $this->access_token);
    $auth_header = $request->to_header("https://api.linkedin.com");
    if (DEBUG) {
      echo $request->get_signature_base_string() . "\n";
      echo $auth_header . "\n";
    }
    $response = $this->httpRequest($search_url, $auth_header, "GET");
    return $response;
  }


	function getCache(){
		// set up the parent container, the first time
		if( !array_key_exists("linkedin", $_SESSION) ) $_SESSION['linkedin']= array();
		return $_SESSION['linkedin'];

	}

	function setCache( $data ){
		// save the data in the session
		foreach( $data as $key => $result ){
			$_SESSION['linkedin'][$key] = $result;
		}
		// update the local variable
		$this->cache = $this->getCache();
	}

	function deleteCache(){
		unset($_SESSION['linkedin']);
	}

}