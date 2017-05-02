<?php 
/** 
 * 新浪微博操作类 
 * 
 * @version 1.0 
 */ 

class sinaClientV2 extends OAuthV2
{ 
	public $host = "https://api.weibo.com/2/"; //Set up the API root URL.

	/**
	 * 根据用户UID或昵称获取用户资料
	 *
	 * 按用户UID或昵称返回用户资料，同时也将返回用户的最新发布的微博。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/users/show users/show}
	 * 
	 * @access public
	 * @param int  $uid 用户UID。
	 * @return array
	 */
	function show_user( $uid )
	{
		$params = array();
		$params['uid'] = $uid;
		$params['access_token'] = $this->access_token;
		return $this->get( 'users/show', $params );
	}

	/**
	 * 获取用户发布的微博信息列表
	 *
	 * 返回用户的发布的最近n条信息，和用户微博页面返回内容是一致的。此接口也可以请求其他用户的最新发表微博。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/user_timeline statuses/user_timeline}
	 * 
	 * @access public
	 * @param int $page 页码
	 * @param int $count 每次返回的最大记录数，最多返回200条，默认50。
	 * @param mixed $uid 指定用户UID或微博昵称
	 * @param int $since_id 若指定此参数，则只返回ID比since_id大的微博消息（即比since_id发表时间晚的微博消息）。可选。
	 * @param int $max_id 若指定此参数，则返回ID小于或等于max_id的提到当前登录用户微博消息。可选。
	 * @param int $base_app 是否基于当前应用来获取数据。1为限制本应用微博，0为不做限制。默认为0。
	 * @param int $feature 过滤类型ID，0：全部、1：原创、2：图片、3：视频、4：音乐，默认为0。
	 * @param int $trim_user 返回值中user信息开关，0：返回完整的user信息、1：user字段仅返回uid，默认为0。
	 * @return array
	 */
	function user_timeline( $uid = NULL , $page = 1 , $count = 50 , $since_id = 0, $max_id = 0, $feature = 0, $trim_user = 0, $base_app = 0 )
	{
		$params = array();
		$params['uid'] = $uid;
		if ($since_id) {
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$params['max_id'] = $max_id;
		}
		$params['base_app'] = intval($base_app);
		$params['feature'] = intval($feature);
		$params['count'] = intval($count);
		$params['page'] = intval($page);
		$params['trim_user'] = intval($trim_user);
		$params['access_token'] = $this->access_token;
		return $this->get( 'statuses/user_timeline', $params );
	}

	/**
	 * 获取用户的粉丝列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/friendships/followers friendships/followers}
	 *
	 * @param int $uid  需要查询的用户UID
	 * @param int $count 单页返回的记录条数，默认为50，最大不超过200。
	 * @param int $cursor false 返回结果的游标，下一页用返回值里的next_cursor，上一页用previous_cursor，默认为0。
	 * @return array
	 **/
	function followers( $uid , $cursor = 0 , $count = 50 )
	{
		$params = array();
		$params['uid'] = $uid;
		$params['count'] = $count;
		$params['cursor'] = $cursor;
		$params['access_token'] = $this->access_token;
		return $this->get( 'friendships/followers', $params );
	}

	/**
	 * 获取优质粉丝
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/friendships/followers/active friendships/followers/active}
	 *
	 * @param int $uid 需要查询的用户UID。
	 * @param int $count 返回的记录条数，默认为20，最大不超过200。
     * @return array
	 **/
	function followers_active( $uid, $count = 20 )
	{
		$params = array();
		$params['uid'] = $uid;
		$params['count'] = $count;
		$params['access_token'] = $this->access_token;
		return $this->get( 'friendships/followers/active', $params );
	}

    // 发布一条微博信息(文本、图片)
    function update( $text, $value = '' )
    {  
        $params = array(); 
        $params['status'] = $text; 
		$params['access_token'] = $this->access_token;
		if ($value[0] == "image" && $value[1]) {
			$params['pic'] = '@'.$value[1];
			return $this->post( 'statuses/upload', $params, true );
		} else {
            return $this->post( 'statuses/update', $params ); 
		}
    } 

	/**
	 * 对一条微博进行评论
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/comments/create comments/create}
	 *
	 * @param string $comment 评论内容，内容不超过140个汉字。
	 * @param int $id 需要评论的微博ID。
	 * @param int $comment_ori 当评论转发微博时，是否评论给原微博，0：否、1：是，默认为0。
	 * @return array
	 */
	function comment( $id , $comment , $comment_ori = 1 )
	{
		$params = array();
		$params['comment'] = $comment;
		$params['id'] = $id;
		$params['comment_ori'] = $comment_ori;
		$params['access_token'] = $this->access_token;
		return $this->post( 'comments/create', $params );
	}

	/**
	 * 根据微博ID返回某条微博的评论列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/comments/show comments/show}
	 *
	 * @param int $sid 需要查询的微博ID。
	 * @param int $page 返回结果的页码，默认为1。
	 * @param int $count 单页返回的记录条数，默认为50。
	 * @param int $since_id 若指定此参数，则返回ID比since_id大的评论（即比since_id时间晚的评论），默认为0。
	 * @param int $max_id  若指定此参数，则返回ID小于或等于max_id的评论，默认为0。
	 * @param int $filter_by_author 作者筛选类型，0：全部、1：我关注的人、2：陌生人，默认为0。
	 * @return array
	 */
	function get_comments( $sid, $page = 1, $count = 20, $since_id = 0, $max_id = 0, $filter_by_author = 0 )
	{
		$params = array();
		$params['id'] = $sid;
		if ($since_id) {
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$params['max_id'] = $max_id;
		}
		$params['count'] = $count;
		$params['page'] = $page;
		$params['filter_by_author'] = $filter_by_author;
		$params['access_token'] = $this->access_token;
		return $this->get( 'comments/show',  $params );
	}

	/**
	 * 根据评论ID批量返回评论信息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/comments/show_batch comments/show_batch}
	 *
	 * @param string $cids 需要查询的批量评论ID，用半角逗号分隔，最大50
	 * @return array
	 */
	function comments_show_batch( $cids )
	{
		$params = array();
		if (is_array($cids) && !empty($cids)) {
			$params['cids'] = implode(',', $cids);
		} else {
			$params['cids'] = $cids;
		}
		$params['access_token'] = $this->access_token;
		return $this->get( 'comments/show_batch', $params );
	}

	/**
	 * 转发一条微博信息。
	 *
	 * 可加评论。为防止重复，发布的信息与最新信息一样话，将会被忽略。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/repost statuses/repost}
	 * 
	 * @access public
	 * @param int $sid 转发的微博ID
	 * @param string $text 添加的评论信息。可选。
	 * @param int $is_comment 是否在转发的同时发表评论，0：否、1：评论给当前微博、2：评论给原微博、3：都评论，默认为0。
	 * @return array
	 */
    function repost( $sid , $text = NULL, $is_comment = 3 ) 
    { 
        $params = array();
        $params['id'] = $sid;
        if( $text ) $params['status'] = $text;
        $params['is_comment'] = $is_comment;
		$params['access_token'] = $this->access_token;
        return $this->post( 'statuses/repost', $params );
    }
} 
