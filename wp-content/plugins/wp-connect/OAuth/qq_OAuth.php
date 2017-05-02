<?php
/** 
 * 腾讯微博操作类 
 * 
 * @package sae 
 * @author Easy Chen 
 * @version 1.0 
 */ 
class qqClient 
{ 
    // 构造函数 
    function __construct( $akey , $skey , $accecss_token , $accecss_token_secret ) 
    { 
        $this->oauth = new qqOAuth( $akey , $skey , $accecss_token , $accecss_token_secret ); 
    }

	function get_ip()
	{
		if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]) {
			$ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
		} elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]) {
			$ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
		} elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"]) {
			$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
		} elseif (getenv("HTTP_X_FORWARDED_FOR")) {
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		} elseif (getenv("HTTP_CLIENT_IP")) {
			$ip = getenv("HTTP_CLIENT_IP");
		} elseif (getenv("REMOTE_ADDR")) {
			$ip = getenv("REMOTE_ADDR");
		} else {
			$ip = "Unknown";
		} 
		return $ip;
	} 
    // 获取其他人资料
	function show_user( $name )
	{
		$params = array();
		$params['format'] = 'json';
		$params['name'] = $name;
		return $this->oauth->get( 'http://open.t.qq.com/api/user/other_info',  $params );
	}
    // 其他用户发表时间线
	function user_timeline( $page = 0, $count = 20, $name )
	{
		$params = array();
		$params['format'] = 'json';
		$params['name'] = $name;
		$params['reqnum'] = $count;
		$params['pageflag'] = $page;

		return $this->oauth->get('http://open.t.qq.com/api/statuses/user_timeline', $params );
	}
    // 其他帐户听众列表
	function followers( $count = 20 , $name )
	{
		$params = array();
		$params['format'] = 'json';
		$params['name'] = $name;
		$params['reqnum'] = $count;
		return $this->oauth->get( 'http://open.t.qq.com/api/friends/user_fanslist', $params );
	}
	// 发表微博(文本、图片、视频、音乐)
	function update($text, $value = '') {
		$params = array();
		$params['format'] = 'json';
		$params['content'] = $text;
		$params['clientip'] = $this -> get_ip();
		if ($value && is_array($value)) {
			// 兼容旧版本
			if (in_array($value[0], array('image', 'video', 'music'))) {
				if ($value[1]) {
					if ($value[0] == 'image') {
						$value = array($value[1], '', '');
					} elseif ($value[0] == 'video') {
						$value = array('', $value[1], '');
					} elseif ($value[0] == 'music') {
						$value = array('', '', $value[1]);
					}
				} else {
					return $this -> oauth -> post('http://open.t.qq.com/api/t/add', $params);
				} 
			}
			if ($value[0] && !$value[1]) { // 图片
				//$params['pic'] = $value[0];
				//return $this -> oauth -> post('http://open.t.qq.com/api/t/add_pic', $params, true);
				$params['pic_url'] = $value[0];
				return $this -> oauth -> post('http://open.t.qq.com/api/t/add_pic_url', $params);
			} elseif ($value[1] && !$value[0]) { // 视频
				$params['url'] = $value[1];
				return $this -> oauth -> post('http://open.t.qq.com/api/t/add_video', $params);
			} else { // 图片、视频、音乐
				$params['pic_url'] = $value[0];
				$params['video_url'] = $value[1];
				if (is_array($value[2])) {
					$params['music_url'] = $value[2][2];
					$params['music_title'] = $value[2][1];
					$params['music_author'] = $value[2][0];
				} 
				return $this -> oauth -> post('http://open.t.qq.com/api/t/add_multi', $params);
			} 
		} 
		return $this -> oauth -> post('http://open.t.qq.com/api/t/add', $params);
	}
    // 对一条微博信息进行评论
    function comment( $sid , $text ) 
    { 
		$params = array();
		$params['format'] = 'json';
		$params['content'] = $text;
		$params['reid'] = $sid;
		$params['clientip'] = $this -> get_ip();
		return $this -> oauth -> post('http://open.t.qq.com/api/t/comment', $params);
    }
    // 转播一条微博
    function repost( $sid , $text ) 
    { 
		$params = array();
		$params['format'] = 'json';
		$params['content'] = $text;
		$params['reid'] = $sid;
		$params['clientip'] = $this -> get_ip();
		return $this -> oauth -> post('http://open.t.qq.com/api/t/re_add', $params);
    }
    // 根据微博ID返回某条微博的评论列表
    function get_comments( $rootid, $page = 0, $count = 20, $flag = 1, $twitterid = 0, $pagetime = 0 ) 
    { 
        $params = array();
		$params['format'] = 'json';
		$params['flag'] = $flag; // 0－转播列表 1－点评列表 2－点评与转播列表
        $params['rootid'] = $rootid; // 转发或回复的微博根结点id（源微博id）
		$params['pageflag'] = $page;
		$params['pagetime'] = $pagetime;
		$params['reqnum'] = $count;
		$params['twitterid'] = $twitterid;
        return $this->oauth->get('http://open.t.qq.com/api/t/re_list', $params);
    }
    // 获取视频信息
	function getvideoinfo( $url )
	{
		$params = array();
		$params['format'] = 'json';
		$params['url'] = $url;
		return $this->oauth->post('http://open.t.qq.com/api/t/getvideoinfo', $params);
	}
	// 获取自己信息
    function verify_credentials() 
    { 
		$params = array();
		$params['format'] = 'json';
        return $this->oauth->get( 'http://open.t.qq.com/api/user/info', $params );
    } 
	// 根据微博ID批量获取微博内容
    function get_list( $ids )
    {
		$params = array();
		$params['format'] = 'json';
		$params['ids'] = $ids;
        return $this->oauth->get( 'http://open.t.qq.com/api/t/list', $params );
    } 
	// 其他用户发表时间线索引
    function user_timeline_ids( $name, $page = 0, $count = 20, $type = 3 )
    {
		$params = array();
		$params['format'] = 'json';
		$params['name'] = $name;
		$params['reqnum'] = $count;
		$params['pageflag'] = $page;
		$params['type'] = $type;
        return $this->oauth->get( 'http://open.t.qq.com/api/statuses/user_timeline_ids', $params );
    } 
}

/** 
 * 腾讯微博 OAuth 认证类 
 * 
 * @package sae 
 * @author Easy Chen 
 * @version 1.0 
 */ 
class qqOAuth {
    // Contains the last HTTP status code returned.  
    public $http_code; 
    // Contains the last API call. 
    public $url; 
    // Set up the API root URL. 
    public $host = "https://open.t.qq.com/cgi-bin/"; 
    // Set timeout default. 
    public $timeout = 30; 
    // Set connect timeout. 
    public $connecttimeout = 30;  
    // Verify SSL Cert. 
    public $ssl_verifypeer = FALSE; 
    // Respons format. 
    public $format = 'json'; 
    // Decode returned json data. 
    public $decode_json = TRUE; 
    // Contains the last HTTP headers returned. 
    public $http_info; 
    // Set the useragnet. 
    public $useragent = 'qqOAuth v0.0.1'; 
    /* Immediately retry the API call if the response was not successful. */ 
    //public $retry = TRUE; 
    
    /** 
     *Set API URLS
     */ 
    function accessTokenURL()  { return 'https://open.t.qq.com/cgi-bin/access_token'; } 
    function authenticateURL() { return 'https://open.t.qq.com/cgi-bin/authenticate'; } 
    function authorizeURL()    { return 'https://open.t.qq.com/cgi-bin/authorize'; }  
    function requestTokenURL() { return 'https://open.t.qq.com/cgi-bin/request_token'; } 

    /** 
     * Debug helpers 
     */ 
    function lastStatusCode() { return $this->http_status; } 
    function lastAPICall() { return $this->last_api_call; } 

    /** 
     * construct WeiboOAuth object 
     */ 
    function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) { 
        $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1(); 
        $this->consumer = new OAuthConsumer($consumer_key, $consumer_secret); 
        if (!empty($oauth_token) && !empty($oauth_token_secret)) { 
            $this->token = new OAuthConsumer($oauth_token, $oauth_token_secret); 
        } else { 
            $this->token = NULL; 
        } 
    } 

    /** 
     * Get a request_token from Weibo 
     * 
     * @return array a key/value array containing oauth_token and oauth_token_secret 
     */ 
    function getRequestToken($oauth_callback = NULL) { 
        $parameters = array(); 
        if (!empty($oauth_callback)) { 
            $parameters['oauth_callback'] = $oauth_callback; 
        }  

        $request = $this->oAuthRequest($this->requestTokenURL(), 'GET', $parameters); 
        $token = OAuthUtil::parse_parameters($request); 
        $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']); 
        return $token; 
    } 

    /** 
     * Get the authorize URL 
     * 
     * @return string 
     */ 
    function getAuthorizeURL($token, $sign_in_with_Weibo = TRUE , $url) { 
        if (is_array($token)) { 
            $token = $token['oauth_token']; 
        } 
        if (empty($sign_in_with_Weibo)) { 
            return $this->authorizeURL() . "?oauth_token={$token}&oauth_callback=" . urlencode($url); 
        } else { 
            return $this->authenticateURL() . "?oauth_token={$token}&oauth_callback=". urlencode($url); 
        } 
    }  

    /** 
     * Exchange the request token and secret for an access token and 
     * secret, to sign API calls. 
     * 
     * @return array array("oauth_token" => the access token, 
     *                "oauth_token_secret" => the access secret) 
     */ 
    function getAccessToken($oauth_verifier = FALSE, $oauth_token = false) { 
        $parameters = array(); 
        if (!empty($oauth_verifier)) { 
            $parameters['oauth_verifier'] = $oauth_verifier; 
        } 


        $request = $this->oAuthRequest($this->accessTokenURL(), 'GET', $parameters); 
        $token = OAuthUtil::parse_parameters($request); 
        $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']); 
        return $token; 
    } 

    /** 
     * GET wrappwer for oAuthRequest. 
     * 
     * @return mixed 
     */ 
    function get($url, $parameters = array()) { 
        $response = $this->oAuthRequest($url, 'GET', $parameters); 
        if ($this->format === 'json' && $this->decode_json) { 
            return json_decode($response, true); 
        } 
        return $response; 
    } 

    /** 
     * POST wreapper for oAuthRequest. 
     * 
     * @return mixed 
     */ 
    function post($url, $parameters = array() , $multi = false) { 
        
        $response = $this->oAuthRequest($url, 'POST', $parameters , $multi ); 
        if ($this->format === 'json' && $this->decode_json) { 
            return json_decode($response, true); 
        } 
        return $response; 
    } 

    /** 
     * DELTE wrapper for oAuthReqeust. 
     * 
     * @return mixed 
     */ 
    function delete($url, $parameters = array()) { 
        $response = $this->oAuthRequest($url, 'DELETE', $parameters); 
        if ($this->format === 'json' && $this->decode_json) { 
            return json_decode($response, true); 
        } 
        return $response; 
    } 

    /** 
     * Format and sign an OAuth / API request 
     * 
     * @return string 
     */ 
    function oAuthRequest($url, $method, $parameters , $multi = false) { 
        // echo $url ; 
        $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters); 
        $request->sign_request($this->sha1_method, $this->consumer, $this->token); 
        switch ($method) { 
        case 'GET': 
            //echo $request->to_url(); 
            return $this->http($request->to_url(), 'GET'); 
        default: 
            return $this->http($request->get_normalized_http_url(), $method, $request->to_postdata($multi) , $multi ); 
        } 
    } 

    /** 
     * Make an HTTP request 
     * 
     * @return string API results 
     */ 
	function http($url, $method, $postfields = null , $multi = false) {
		$params = array(
			"method" => $method,
			"timeout" => $this -> timeout,
			"user-agent" => $this -> useragent,
			"sslverify" => $this -> ssl_verifypeer,
			"body" => $postfields,
			"headers" => ($multi) ? array("Content-Type" => "multipart/form-data; boundary=" . OAuthUtil :: $boundary , "Expect: ") : ''
		);
		return class_http($url, $params);
	}
}