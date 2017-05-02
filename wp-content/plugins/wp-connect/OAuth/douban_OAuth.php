<?php
/** 
 * 豆瓣操作类 
 * 
 * @package sae 
 * @author Easy Chen 
 * @version 1.0 
 */ 
class doubanClient 
{ 
    /** 
     * 构造函数 
     *  
     * @access public 
     * @param mixed $akey 微博开放平台应用APP KEY 
     * @param mixed $skey 微博开放平台应用APP SECRET 
     * @param mixed $accecss_token OAuth认证返回的token 
     * @param mixed $accecss_token_secret OAuth认证返回的token secret 
     * @return void 
     */ 
    function __construct( $akey , $skey , $accecss_token , $accecss_token_secret ) 
    { 
        $this->oauth = new doubanOAuth( $akey , $skey , $accecss_token , $accecss_token_secret ); 
    }

    /** 
     * 发表微博 
     *  
     * @access public 
     * @param mixed $text 要更新的微博信息。 
     * @return array 
     */ 
    function update( $text ) 
    { 
        $content = '<?xml version="1.0" encoding="UTF-8"?>'.
			'<entry xmlns:ns0="http://www.w3.org/2005/Atom" xmlns:db="http://www.douban.com/xmlns/">'.
			'<content>'.$text.'</content>'.
			'</entry>';
        return $this->oauth->OAuthRequest('http://api.douban.com/miniblog/saying', array(), 'POST', $content);
    }

}

/** 
 * 豆瓣 OAuth 认证类 
 * 
 * @package sae 
 * @author Easy Chen 
 * @version 1.0 
 */ 
class doubanOAuth {/*{{{*/
	/* Contains the last HTTP status code returned */
	private $http_status;

	/* Contains the last API call */
	private $last_api_call;

	/* Set up the API root URL */
	public static $TO_API_ROOT = "http://www.douban.com/service";

	/**
	 * Set API URLS
	 */
	function requestTokenURL() { return self::$TO_API_ROOT.'/auth/request_token'; }
	function authorizeURL() { return self::$TO_API_ROOT.'/auth/authorize'; }
	function authenticateURL() { return self::$TO_API_ROOT.'/auth/authenticate'; }
	function accessTokenURL() { return self::$TO_API_ROOT.'/auth/access_token'; }

	/**
	 * Debug helpers
	 */
	function lastStatusCode() { return $this->http_status; }
	function lastAPICall() { return $this->last_api_call; }

	/**
	 * construct DoubanOAuth object
	 */
	function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) {/*{{{*/
		$this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
		$this->consumer = new OAuthConsumer($consumer_key, $consumer_secret);
		if (!empty($oauth_token) || !empty($oauth_token_secret)) {
			$this->token = new OAuthConsumer($oauth_token, $oauth_token_secret);
		} else {
			$this->token = NULL;
		}
	}/*}}}*/


	/**
	 * Get a request_token from Douban
	 *
	 * @returns a key/value array containing oauth_token and oauth_token_secret
	 */
	function getRequestToken() {/*{{{*/
		$r = $this->oAuthRequest($this->requestTokenURL());

		$token = $this->oAuthParseResponse($r);
		$this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
		return $token;
	}/*}}}*/

	/**
	 * Parse a URL-encoded OAuth response
	 *
	 * @return a key/value array
	 */
	function oAuthParseResponse($responseString) {
		$r = array();
		foreach (explode('&', $responseString) as $param) {
			$pair = explode('=', $param, 2);
			if (count($pair) != 2) continue;
			$r[urldecode($pair[0])] = urldecode($pair[1]);
		}
		return $r;
	}

	/**
	 * Get the authorize URL
	 *
	 * @returns a string
	 */
	function getAuthorizeURL($token, $sign_in_with_Weibo = TRUE , $url) {/*{{{*/
		if (is_array($token)) $token = $token['oauth_token'];
		return $this->authorizeURL() . "?oauth_token={$token}&oauth_callback=" . urlencode($url); 
	}/*}}}*/
	/**
	 * Get the authenticate URL
	 *
	 * @returns a string
	 */
	function getAuthenticateURL($token) {/*{{{*/
		if (is_array($token)) $token = $token['oauth_token'];
		return $this->authenticateURL() . '?oauth_token=' . $token;
	}/*}}}*/

	/**
	 * Exchange the request token and secret for an access token and
	 * secret, to sign API calls.
	 *
	 * @returns array("oauth_token" => the access token,
	 *                "oauth_token_secret" => the access secret)
	 */
	function getAccessToken($token = NULL) {/*{{{*/
		$r = $this->oAuthRequest($this->accessTokenURL());
		$token = $this->oAuthParseResponse($r);
		$this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
		return $token;
	}/*}}}*/

	/**
	 * Format and sign an OAuth / API request
	 */
	function oAuthRequest($url, $args = array(), $method = NULL, $post_data = NULL) {/*{{{*/
			
		if (empty($method)) $method = empty($args) ? "GET" : "POST";
		$req = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $args);
		$req->sign_request($this->sha1_method, $this->consumer, $this->token);
		switch ($method) {
			case 'GET': return $this->http($req->to_url(),'GET');
			case 'POST': return $this->http($req->get_normalized_http_url(),'POST',
				$post_data, $req->to_header());
		}
	}/*}}}*/

	/**
	 * Make an HTTP request
	 *
	 * @return API results
	 */
	function http($url, $method, $postfields = null , $multi = false) {
		if($method=='POST'){
			$multi = WP_Http::processHeaders($multi);
			$header = array("Content-Type"=>"application/atom+xml");
			$header = array_merge($header, $multi['headers']);
		}
		$params = array(
			"method" => $method,
			"body" => $postfields,
			"headers" => $header
		);
		return class_http($url, $params);
	}
}