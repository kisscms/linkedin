<?php
// FIX - to include the base OAuth lib not in alphabetical order
require_once( realpath("../") . "/app/plugins/oauth/helpers/kiss_oauth.php" );

/* Discus for KISSCMS */
class LinkedIn_OAuth extends KISS_OAuth_v1 {
	
	function  __construct( $api="linkedin", $url="https://api.linkedin.com/uas/oauth" ) {
		
		$this->url = array(
			'authorize' 		=> $url ."/authorize", 
			'request_token' 	=> $url ."/requestToken", 
			'access_token' 		=> $url ."/accessToken", 
		);
		
		parent::__construct( $api, $url );
		
	}
	
	function save( $response ){
		
		// erase the existing cache
		$linkedin = new LinkedIn();
		$linkedin->deleteCache();
		
		// save to the user session 
		$_SESSION['oauth']['linkedin'] = $response;
	}
	
}