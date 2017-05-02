<?php 
/** 
 * 新浪微博操作类 
 * 
 * @package sae 
 * @author Easy Chen 
 * @version 1.0 
 */ 
class sinaClient 
{ 

    // 构造函数
    function __construct( $akey , $skey , $accecss_token , $accecss_token_secret ) 
    { 
        $this->oauth = new sinaOAuth( $akey , $skey , $accecss_token , $accecss_token_secret ); 
    }

	/**
	 * 获取当前登录用户及其所关注用户的最新微博消息。
	 * 获取当前登录用户及其所关注用户的最新微博消息。和用户登录 http://t.sina.com.cn 后在“我的首页”中看到的内容相同。
	 * 
	 * @access public
	 * @param int $page 指定返回结果的页码。根据当前登录用户所关注的用户数及这些被关注用户发表的微博数，翻页功能最多能查看的总记录数会有所不同，通常最多能查看1000条左右。默认值1。可选。
	 * @param int $count 每次返回的记录数。缺省值20，最大值200。可选。
	 * @return array
	 */

	function user_timeline( $page = 1, $count = 20, $uid)
	{
		$params = array();
		$params['user_id'] = $uid;
		$params['count'] = $count;
		$params['page'] = $page;

		return $this->oauth->get('http://api.t.sina.com.cn/statuses/user_timeline.json', $params );
	}

	/**
	 * 批量获取一组微博的评论数及转发数
	 * 批量统计微博的评论数，转发数，一次请求最多获取100个。
	 * 
	 * @access public
	 * @param mixed $sids 微博ID号列表，用逗号隔开。或使用数据传递一组微博ID。如："32817222,32817223"或array(32817222, 32817223)
	 * @return array
	 */
	function get_count_info_by_ids( $sids )
	{
		$params = array();
		if (is_array($sids) && !empty($sids)) {
			foreach ($sids as $k => $v) {
				$this->id_format($sids[$k]);
			}
			$params['ids'] = join(',', $sids);
		} else {
			$params['ids'] = $sids;
		}

		return $this->oauth->get( 'http://api.t.sina.com.cn/statuses/counts.json' , $params );
	}

	/**
	 * 获取用户粉丝列表及及每个粉丝用户最新一条微博
	 * 返回用户的粉丝列表，并返回粉丝的最新微博。按粉丝的关注时间倒序返回，每次返回100个。注意目前接口最多只返回5000个粉丝。
	 * 
	 * @access public
	 * @param int $cursor 单页只能包含100个粉丝列表，为了获取更多则cursor默认从-1开始，通过增加或减少cursor来获取更多的，如果没有下一页，则next_cursor返回0。可选。
	 * @param int $count 每次返回的最大记录数（即页面大小），不大于200,默认返回20。可选。
	 * @param mixed $uid 要获取粉丝的 UID或微博昵称。不提供时默认返回当前用户的关注列表。可选。
	 * @return array
	 */
	function followers( $cursor = NULL , $count = 20 , $uid )
	{
		$params = array();
		$params['user_id'] = $uid;
		$params['count'] = $count;
		$params['cursor'] = $cursor;
		return $this->oauth->get( 'http://api.t.sina.com.cn/statuses/followers.json' , $params );
	}

	/**
	 * 根据用户UID或昵称获取用户资料
	 * 按用户UID或昵称返回用户资料，同时也将返回用户的最新发布的微博。
	 * 
	 * @return array
	 */
	function show_user( $uid )
	{
		$params = array();
		$params['user_id'] = $uid;
		return $this->oauth->get( 'http://api.t.sina.com.cn/users/show.json' ,  $params );
	}

    // 发表微博(文本、图片)
    function update( $text, $value = '' )
    {  
        $param = array(); 
        $param['status'] = $text; 

		if ($value[0] == "image" && $value[1]) {
			$param['pic'] = $value[1];
			return $this->oauth->post( 'http://api.t.sina.com.cn/statuses/upload.json' , $param , true );
		} else {
            return $this->oauth->post( 'http://api.t.sina.com.cn/statuses/update.json' , $param ); 
		}
    } 
	
    // 对一条微博信息进行评论
    function comment( $sid , $text , $cid = false ) 
    { 
        $param = array();
        $param['id'] = $sid;
        $param['comment'] = $text;
        if( $cid ) $param['cid '] = $cid;

        return $this->oauth->post( 'http://api.t.sina.com.cn/statuses/comment.json' , $param  ); 
    }

    // 根据微博ID返回某条微博的评论列表
    function get_comments( $sid , $page = 1 , $count = 20 ) 
    { 
        $param = array();
        $param['id'] = $sid;
		if( $page ) $param['page'] = $page;
		if( $count ) $param['count'] = $count;

        return $this->oauth->post( 'http://api.t.sina.com.cn/statuses/comments.json' , $param  );
    }

    // 转发一条微博消息
    function repost( $sid , $text ) 
    { 
        $param = array();
        $param['id'] = $sid;
        $param['status'] = $text;
        $param['is_comment'] = 3;

        return $this->oauth->post( 'http://api.t.sina.com.cn/statuses/repost.json' , $param  );
    }
    
    // 获取自己信息
    function verify_credentials() 
    { 
        return $this->oauth->get( 'http://api.t.sina.com.cn/account/verify_credentials.json' );
    }
    
	// 修改头像
    function update_avatar( $pic_path )
	{
		$param = array();
		$param['image'] = "@".$pic_path;
        
        return $this->oauth->post( 'http://api.t.sina.com.cn/account/update_profile_image.json' , $param , true ); 
	
	} 

	// 获取API的访问频率限制。返回当前小时内还能访问的次数。
    function rate_limiting()
	{
        return $this->oauth->get( 'http://api.t.sina.com.cn/account/rate_limit_status.json' );
	}

} 

/** 
 * 新浪微博 OAuth 认证类 
 * 
 * @package sae 
 * @author Easy Chen 
 * @version 1.0 
 */ 
class sinaOAuth { 
    // Contains the last HTTP status code returned.  
    public $http_code; 
    // Contains the last API call. 
    public $url; 
    // Set up the API root URL. 
    public $host = "http://api.t.sina.com.cn/"; 
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
    public $useragent = 'Sae T OAuth v0.2.0-beta2'; 
    /* Immediately retry the API call if the response was not successful. */ 
    //public $retry = TRUE; 
    
    /** 
     *Set API URLS
     */
    function accessTokenURL()  { return 'http://api.t.sina.com.cn/oauth/access_token'; } 
    function authenticateURL() { return 'http://api.t.sina.com.cn/oauth/authenticate'; } 
    function authorizeURL()    { return 'http://api.t.sina.com.cn/oauth/authorize'; } 
    function requestTokenURL() { return 'http://api.t.sina.com.cn/oauth/request_token'; } 


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

        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'http://') !== 0) { 
            $url = "{$this->host}{$url}.{$this->format}"; 
        } 

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