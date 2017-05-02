<?php
/**
 * 同步微博函数
 */
include_once(dirname(__FILE__) . '/config.php');

// 是否开启微博同步功能
if ($wptm_options['enable_wptm']) {
	add_action('admin_menu', 'wp_connect_add_sidebox');
	add_action('publish_post', 'wp_connect_publish');
	add_action('publish_page', 'wp_connect_publish');
	add_action('admin_init', 'publish_custom_post_types', 100);
	function publish_custom_post_types() { // 自定义文章类型
		if (function_exists('get_post_types')) {
			$post_types = get_post_types(array('public' => true, '_builtin' => false), 'names', 'and');
			foreach($post_types as $type => $object) {
				add_action('publish_' . $type, 'wp_connect_publish');
			} 
		} 
	} 
}  
 
// 文章发布页面 面板
function wp_connect_sidebox() {
	global $post;
	if ($post -> post_status != 'publish') {
		echo '<p><label><input type="checkbox" name="publish_no_sync" value="1" />不同步 (保存为草稿、待审也不会同步)</label></p>';
	} else {
		echo '<p><label><input type="checkbox" name="publish_update_sync" value="1" />同步 (不勾选则以文章更新间隔判断)</label></p>';
		echo '<p><label><input type="checkbox" name="publish_new_sync" value="1" />当作新文章同步</label></p>';
	} 
} 

function wp_connect_add_sidebox() {
	if (function_exists('add_meta_box')) {
		add_meta_box('wp-connect-sidebox', '微博同步设置 [只对本页面有效]', 'wp_connect_sidebox', 'post', 'side', 'high');
		add_meta_box('wp-connect-sidebox', '微博同步设置 [只对本页面有效]', 'wp_connect_sidebox', 'page', 'side', 'high');
		if (function_exists('get_post_types')) { // 自定义文章类型
			$post_types = get_post_types(array('public' => true, '_builtin' => false), 'names', 'and');
			foreach($post_types as $type => $object) {
				add_meta_box('wp-connect-sidebox', '微博同步设置 [只对本页面有效]', 'wp_connect_sidebox', $type, 'side', 'high');
			} 
		} 
	} 
} 

/**
 * 发布文章时同步
 * @since 1.0 (V1.9.22)
 */
$sync_loaded = 0; // wp bug
function wp_connect_publish($post_ID) {
	global $sync_loaded, $wptm_options;
	$sync_loaded += 1;
	if (isset($_POST['publish_no_sync']) || $sync_loaded > 1 || $_POST['post_password']) {
		return;
	}
	@ini_set("max_execution_time", 120);
	$time = time();
	$post = get_post($post_ID);
	if ($wptm_options['post_types']) {
		if (in_array($post -> post_type, explode(',', $wptm_options['post_types']))) {
			return;
		}
	}
	$title = wp_replace($post -> post_title);
	$content = $post -> post_content;
	$excerpt = $post -> post_excerpt;
	$post_author_ID = $post -> post_author;
	$post_date = strtotime($post -> post_date);
	$post_modified = strtotime($post -> post_modified);
	$post_content = wp_replace($content);
	if ($wptm_options['multiple_authors']) {
		$wptm_profile = get_user_meta($post_author_ID, 'wptm_profile', true);
		if ($wptm_profile['sync_option']) {
			$account = wp_usermeta_account($post_author_ID);
		} 
	} 
	// 是否开启了多作者博客
	if ($account) {
		$sync_option = $wptm_profile['sync_option'];
		$new_prefix = $wptm_profile['new_prefix'];
		$update_prefix = $wptm_profile['update_prefix'];
		$update_days = $wptm_profile['update_days'] * 60 * 60 * 24;
		$is_author = true;
	} else {
		if (!$wptm_options['sync_option']) {
			return;
		} 
		$account = wp_option_account();
		$sync_option = $wptm_options['sync_option'];
		$new_prefix = $wptm_options['new_prefix'];
		$update_prefix = $wptm_options['update_prefix'];
		$update_days = $wptm_options['update_days'] * 60 * 60 * 24;
	} 
	// 是否绑定了帐号
	if (!$account) {
		return;
	} 
	// 新浪微博授权码过期检查 V1.9.22
	if (!empty($account['sina']['expires_in'])) {
		$expires = $account['sina']['expires_in'] - BJTIMESTAMP;
		if ($expires < 20 * 3600) {
			if ($is_author) {
				if (get_current_user_id() == $post_author_ID) {
					$into = "<a href=" . admin_url('profile.php') . " target=\"_blank\">我的个人资料</a>";
				} else {
					$into = "<a href=" . admin_url("user-edit.php?user_id=$post_author_ID") . " target=\"_blank\">用户资料</a>";
				} 
			} else {
				$into = "<a href=" . admin_url('options-general.php?page=wp-connect') . " target=\"_blank\">WordPress连接微博插件</a>";
			} 
			if ($expires < 60) {
				return setcookie('sina_access_token_expires', "您的 新浪微博授权码已经过期了，刚刚发布的文章已经取消了所有微博同步，请先到 " . $into . " 重新绑定下新浪微博帐号之后再同步吧，本条提示5分钟后自动消失。", BJTIMESTAMP + 600);
			} elseif ($expires < 3600) {
				$expires_in = (int) ($expires / 60) . '分钟';
			} else {
				$expires_in = (int) ($expires / 3600) . '小时';
			} 
			setcookie('sina_access_token_expires', "您的 新浪微博授权码再过 " . $expires_in . " 就过期了，咱们先到 " . $into . " 重新绑定下吧，否则不能同步到新浪微博噢，本条提示5分钟后自动消失。", BJTIMESTAMP + 600);
		} elseif ($_COOKIE['sina_access_token_expires']) {
			setcookie('sina_access_token_expires', "", BJTIMESTAMP - (BJTIMESTAMP + 600));
		} 
	} 
	// 是否为新发布
	if (($post -> post_status == 'publish' || $_POST['publish'] == 'Publish') && ($_POST['prev_status'] == 'draft' || $_POST['original_post_status'] == 'draft' || $_POST['original_post_status'] == 'auto-draft' || $_POST['prev_status'] == 'pending' || $_POST['original_post_status'] == 'pending')) {
		$prefix = $new_prefix;
	} elseif ((($_POST['originalaction'] == "editpost") && (($_POST['prev_status'] == 'publish') || ($_POST['original_post_status'] == 'publish'))) && $post -> post_status == 'publish') { // 是否已发布
		if (isset($_POST['publish_new_sync'])) {
			$prefix = $new_prefix;
		} elseif (!isset($_POST['publish_update_sync'])) {
			if ($update_days == 0 || ($time - $post_date < $update_days)) { // 判断当前时间与文章发布时间差
				return;
			} 
		} else {
			$prefix = $update_prefix;
		} 
	} elseif (isset($_POST['_inline_edit'])) { // 是否是快速编辑
		$quicktime = $_POST['aa'] . '-' . $_POST['mm'] . '-' . $_POST['jj'] . ' ' . $_POST['hh'] . ':' . $_POST['mn'] . ':00';
		$post_date = strtotime($quicktime);
		if ($update_days == 0 || ($time - $post_date < $update_days)) { // 判断当前时间与文章发布时间差
			return;
		} 
		$prefix = $update_prefix;
	} elseif (defined('DOING_CRON')) { // 定时发布
		$prefix = $new_prefix;
	} else { // 后台快速发布，xmlrpc等发布
		if ($post -> post_status == 'publish') {
			if ($post_modified == $post_date || $time - $post_date <= 30) { // 新文章(包括延迟<=30秒)
				$prefix = $new_prefix;
			} 
		} 
	} 
	// 微博话题
	$cat_ids = $wptm_options['cat_ids'];
	$enable_cats = $wptm_options['enable_cats'];
	$enable_tags = $wptm_options['enable_tags'];
	if ($enable_cats || $cat_ids) {
		if ($postcats = get_the_category($post_ID)) {
			foreach($postcats as $cat) {
				$cat_id .= $cat -> cat_ID . ',';
				$cat_name .= $cat -> cat_name . ',';
			} 
			// 不想同步的文章分类ID
			if ($cat_ids && wp_in_array($cat_ids, $cat_id)) {
				return;
			} 
			// 是否将文章分类当成话题
			if ($enable_cats) {
				$cats = $cat_name;
			} 
		} 
	} 
	// 是否将文章标签当成话题
	if (substr_count($cats, ',') < 2 && $enable_tags) {
		if ($posttags = get_the_tags($post_ID)) {
			foreach($posttags as $tag) {
				$tags .= $tag -> name . ',';
			} 
		} 
	} 
	$tags = $cats . $tags;
	if ($tags) {
		$tags = explode(',', rtrim($tags, ','));
		if (count($tags) == 1) {
			$tags = '#' . $tags[0] . '# ';
		} elseif (count($tags) >= 2) {
			$tags = '#' . $tags[0] . '# #' . $tags[1] . '# ';
		} 
	} 
	// 文章URL
	if ($wptm_options['enable_shorten']) { // 是否使用博客默认短网址
		$siteurl = get_bloginfo('url');
        if ($post -> post_type == 'post') {
			$postlink = $siteurl . "/?p=" . $post_ID;
		} elseif ($post -> post_type == 'page') {
			$postlink = $siteurl . "/?page_id=" . $post_ID;
		} else {
			$postlink = get_permalink($post_ID);
		} 
	} else {
		$postlink = get_permalink($post_ID);
	} 
	$url = $postlink;
	if ($excerpt) { // 是否有摘要
		$post_content = wp_replace($excerpt);
	} 
	$format = $wptm_options['format'];
	if ($format && strpos($format, '%title%') !== false) {
		$format_title = true;
		$title2 = str_replace('%title%', $title, $format);
	} else {
		$title2 = $title . ' | ';
	} 
	if ($sync_option == '2') { // 同步 前缀+标题+摘要/内容+链接
		$text = $tags . $prefix . $title2 . $post_content;
	} elseif ($sync_option == '3') { // 同步 文章摘要/内容
		$text = $tags . $prefix . $post_content;
		$url = "";
	} elseif ($sync_option == '4') { // 同步 文章摘要/内容+链接
		$text = $tags . $prefix . $post_content;
	} elseif ($sync_option == '5') { // 同步 标题 + 内容
		$text = $tags . $prefix . $title2 . $post_content;
		$url = "";
	} elseif ($sync_option == '6') { // 同步 标题
		$text = $tags . $prefix . $title2;
		$url = "";
	} else {  // 同步 标题 + 链接
		$title2 = ($format_title) ? $title2 : $title;
		$text = $tags . $prefix . $title2;
	} 
	$richMedia = wp_multi_media_url($content, $post_ID);
	$list = array('title' => $title, // 标题
		'content' => $content, // 内容
		'excerpt' => $excerpt, // 摘要
		'postlink' => $postlink, // 链接
		'tags' => $tags, // 标签话题
		'text' => str_replace(array("[embed]", "[/embed]", $richMedia[1]), "", $text), // 同步的内容
		'url' => $url, // 同步的网址
		'richMedia' => $richMedia, // 匹配视频、图片
		'is_author' => $is_author // 用户类型（站长 or 作者）
		);
	$list = apply_filters('post_sync_weibo', $list, $post_ID, $post_author_ID); 
	// return var_dump($list);
	// $other = array('is_author'=>$is_author, 'uid'=>$post_author_ID);
	// $account = array_merge($account, $other);
	if (is_array($list)) {
		wp_update_list($list['text'], $list['url'], $list['richMedia'], $account, $post_ID);
	} 
}

 /**
 * 同步列表
 * @since 2.4.5
 */
function wp_update_list($text, $url, $pic, $account, $post_id = '') {
	global $wptm_options;
	if (is_array($pic)) {
		// 兼容旧版本
		if ($pic[0] == 'image') {
			$pic = array($pic[1], '', '');
		} elseif ($pic[0] == 'video') {
			$pic = array('', $pic[1], '');
		} elseif ($pic[0] == 'music') {
			$pic = array('', '', $pic[1]);
		} 
		if ($pic[0]) { // 图片
			$picture = array('image', $pic[0]);
		} 
		if ($pic[1] && $pic[1] != $url) { // 视频
			$vurl = $pic[1];
		} elseif (is_array($pic[2])) { // 音乐
			if ($pic[2][1] && $pic[2][2]) {
				$vurl = '#' . $pic[2][0] . '#' . $pic[2][1] . ' ' . $pic[2][2]; // #歌手# 歌曲 url
			} else {
				$vurl = $pic[2][0]; // url
			} 
		} 
	} 
	// 是否使用短网址
	if ($wptm_options['t_cn']) {
		$url = get_url_short($url);
	} 
	// 处理完毕输出链接
	$postlink = trim($vurl . ' ' . $url); 
	// 截取字数
	$status = wp_status($text, '', 140, 1); //灯鹭
	$status1 = wp_status($text, $postlink, 140); //网易/人人/饭否/做啥
	$status2 = wp_status($text, urlencode($postlink), 140, 1); //新浪/天涯
	$status3 = wp_status($text, $postlink, 140, 1); //腾讯/开心
	// return var_dump($status3);
	// 开始同步
	require_once(dirname(__FILE__) . '/OAuth/OAuth.php');
	$output = array();
	if ($account['sina']['mediaUserID']) {
		wp_update_share($account['sina']['mediaUserID'], $status, $url, '', $pic[0], $pic[1], $post_id);
	} elseif ($account['sina']) { // 新浪微博 /140*
		$ms = wp_update_t_sina($account['sina'], $status2, $picture);
		$output['sina'] = $ms['mid'];
	} 
	$mediaUserID = '';
	if ($account['qq']['oauth_token']) { // 腾讯微博 /140*
		$output['qq'] = wp_update_t_qq($account['qq'], $status3, $pic);
	} elseif ($account['qq']['mediaUserID']) {
		$mediaUserID .= $account['qq']['mediaUserID'] . ',';
	} 
	if ($account['shuoshuo']) { // 说说 /140
		wp_post_shuoshuo($account['shuoshuo'], $status2, $pic);
	} 
	if ($account['sohu']['oauth_token']) { // 搜狐微博 /+
		wp_update_t_sohu($account['sohu'], wp_status($text, $postlink, 200, 1), $picture);
	} elseif ($account['sohu']['mediaUserID']) {
		$mediaUserID .= $account['sohu']['mediaUserID'] . ',';
	} 
	if ($account['netease']['oauth_token']) { // 网易微博 /163
		wp_update_t_163($account['netease'], $status1, $picture);
	} elseif ($account['netease']['mediaUserID']) {
		$mediaUserID .= $account['netease']['mediaUserID'] . ',';
	} 
	if ($account['renren']['mediaUserID']) {
		$mediaUserID .= $account['renren']['mediaUserID'] . ',';
	} elseif ($account['renren']) { // 人人网 /140
		wp_update_renren($account['renren'], $status1);
	} 
	if ($account['tianya']['oauth_token']) { // 天涯 /140*
		wp_update_tianya($account['tianya'], $status2, $picture);
	} elseif ($account['tianya']['mediaUserID']) {
		$mediaUserID .= $account['tianya']['mediaUserID'];
	} 
	if ($mediaUserID) {
		wp_update_share(rtrim($mediaUserID, ','), $status, $url, '', $pic[0], $pic[1], $post_id);
	} 
	if ($account['wbto']) { // 微博通 /140+
		wp_update_wbto($account['wbto'], wp_status($text, $postlink, 140, 1), $picture);
	} 
	if ($account['douban']) { // 豆瓣 /128
		wp_update_douban($account['douban'], wp_status($text, $postlink, 128));
	} 
	if ($account['twitter']) { // twitter /140
		wp_update_twitter($account['twitter'], wp_status($text, wp_urlencode($postlink), 140));
	} 
	if ($account['kaixin001']) { // 开心网 /140+
		wp_update_kaixin001($account['kaixin001'], $status3, $picture);
	} 
	/*
	if ($account['digu']) { // 嘀咕 /140
		wp_update_digu($account['digu'], wp_status($text, urlencode($postlink), 140));
	} 
	if ($account['fanfou']) { // 饭否 /140
		wp_update_fanfou($account['fanfou'], $status1);
	} 
	if ($account['renjian']) { // 人间 /+
		wp_update_renjian($account['renjian'], wp_status($text, urlencode($postlink), 200, 1), $picture);
	} 
	if ($account['zuosa']) { // 做啥 /140
		wp_update_zuosa($account['zuosa'], $status1);
	} 
	*/
	// 钩子，方便自定义插件
	do_action('wp_update_list_update', $output, $ms, $post_id);
	return $output;
}
// 腾讯微博
function wp_update_t_qq($tok, $status, $value = "") {
	if (!class_exists('qqOAuth')) {
		include dirname(__FILE__) . '/OAuth/qq_OAuth.php';
	} 
	$to = new qqClient(QQ_APP_KEY, QQ_APP_SECRET, $tok['oauth_token'], $tok['oauth_token_secret']);
	$result = $to -> update($status, $value);
	return $result['data']['id'];
}
// 新浪微博
function wp_update_t_sina($tok, $status, $value = "") {
	if ($tok['oauth_token']) {
		class_exists('sinaOAuth') or require(dirname(__FILE__) . "/OAuth/sina_OAuth.php");
		$to = new sinaClient(SINA_APP_KEY, SINA_APP_SECRET, $tok['oauth_token'], $tok['oauth_token_secret']);
		$result = $to -> update($status, $value);
	} elseif ($tok['access_token']) { // V2.0
		class_exists('OAuthV2') or require(dirname(__FILE__) . "/OAuth/OAuthV2.php");
		class_exists('sinaClientV2') or require(dirname(__FILE__) . "/OAuth/sina_OAuthV2.php");
		$to = new sinaClientV2(SINA_APP_KEY, SINA_APP_SECRET, $tok['access_token']);
		$result = $to -> update($status, $value);
	} 
	return $result;
} 
// 搜狐微博
function wp_update_t_sohu($tok, $status, $value = "") {
	if (!class_exists('sohuOAuth')) {
		include dirname(__FILE__) . '/OAuth/sohu_OAuth.php';
	} 
	$to = new sohuClient(SOHU_APP_KEY, SOHU_APP_SECRET, $tok['oauth_token'], $tok['oauth_token_secret']);
	$result = $to -> update($status, $value);
	return $result;
}
// 网易微博
function wp_update_t_163($tok, $status, $value = "") {
	if (!class_exists('neteaseOAuth')) {
		include dirname(__FILE__) . '/OAuth/netease_OAuth.php';
	}
	$to = new neteaseClient(APP_KEY, APP_SECRET, $tok['oauth_token'], $tok['oauth_token_secret']);
	$result = $to -> update($status, $value);
	return $result;
} 
// Twitter
function wp_update_twitter($tok, $status, $value = "") {
	global $wptm_options;
	if ($wptm_options['enable_proxy']) {
		$text = "twitter={$status}&pic={$value}&t1={$tok['oauth_token']}&t2={$tok['oauth_token_secret']}";
		wp_update_api($text);
	} else {
		if (!class_exists('twitterOAuth')) {
			include dirname(__FILE__) . '/OAuth/twitter_OAuth.php';
		}
		$to = new twitterClient(T_APP_KEY, T_APP_SECRET, $tok['oauth_token'], $tok['oauth_token_secret']);
		$result = $to -> update($status, $value);
		return $result;
	}
}
// 豆瓣
function wp_update_douban($tok, $status) {
	if (!class_exists('doubanOAuth')) {
		include dirname(__FILE__) . '/OAuth/douban_OAuth.php';
	} 
	$to = new doubanClient(DOUBAN_APP_KEY, DOUBAN_APP_SECRET, $tok['oauth_token'], $tok['oauth_token_secret']);
	$result = $to -> update($status);
	return $result;
} 
// 天涯
function wp_update_tianya($tok, $status, $value = "") {
	if (!class_exists('tianyaOAuth')) {
		include dirname(__FILE__) . '/OAuth/tianya_OAuth.php';
	}
	$to = new tianyaClient(TIANYA_APP_KEY, TIANYA_APP_SECRET, $tok['oauth_token'], $tok['oauth_token_secret']);
	$result = $to -> update($status, $value);
	return $result;
} 
// 嘀咕
function wp_update_digu($user, $status) {
	$api_url = 'http://api.minicloud.com.cn/statuses/update.json';
	$body = array('content' => $status);
	$password = key_decode($user['password']);
	$headers = array('Authorization' => 'Basic ' . base64_encode("{$user['username']}:$password"));
	$request = new WP_Http;
	$result = $request -> request($api_url , array('method' => 'POST', 'body' => $body, 'headers' => $headers));
} 
// 饭否
function wp_update_fanfou($user, $status) {
	$api_url = 'http://api.fanfou.com/statuses/update.json';
	$body = array('status' => $status);
	$password = key_decode($user['password']);
	$headers = array('Authorization' => 'Basic ' . base64_encode("{$user['username']}:$password"));
	$request = new WP_Http;
	$result = $request -> request($api_url , array('method' => 'POST', 'body' => $body, 'headers' => $headers));
}
// 人间网
function wp_update_renjian($user, $status, $value = "") {
	$api_url = 'http://api.renjian.com/v2/statuses/create.json';
	$body = array();
	$body['text'] = $status;
	if ($value[0] == "image" && $value[1]) {
		$body['status_type'] = "PICTURE";
		$body['url'] = $value[1];
	}
	$password = key_decode($user['password']);
	$headers = array('Authorization' => 'Basic ' . base64_encode("{$user['username']}:$password"));
	$request = new WP_Http;
	$result = $request -> request($api_url , array('method' => 'POST', 'body' => $body, 'headers' => $headers));
} 
// 做啥网
function wp_update_zuosa($user, $status) {
	$api_url = 'http://api.zuosa.com/statuses/update.json';
	$body = array('status' => $status);
	$password = key_decode($user['password']);
	$headers = array('Authorization' => 'Basic ' . base64_encode("{$user['username']}:$password"));
	$request = new WP_Http;
	$result = $request -> request($api_url , array('method' => 'POST', 'body' => $body, 'headers' => $headers));
}
/*
// Follow5
function wp_update_follow5($user, $status, $value) {
	$api_url = 'http://api.follow5.com/api/statuses/update.xml?api_key=C1D656C887DB993D6FB6CA4A30754ED8';
	$body = array();
	$body['source'] = 'qq_wp_follow5';
	$body['status'] = $status;
	if ($value[1]) {
		$body['link'] = $value[1];
	} 
	$password = key_decode($user['password']);
	$headers = array('Authorization' => 'Basic ' . base64_encode("{$user['username']}:$password"));
	$request = new WP_Http;
	$result = $request -> request($api_url , array('method' => 'POST', 'body' => $body, 'headers' => $headers));
}
*/
// wbto
function wp_update_wbto($user, $status, $value = "") {
	$body = array();
	$body['source'] = 'wordpress';
	$body['content'] = rawurlencode($status);
	if ($value[0] == "image" && $value[1]) {
		$body['imgurl'] = $value[1];
		$api_url = 'http://wbto.cn/api/upload.json';
	} else {
	    $api_url = 'http://wbto.cn/api/update.json';
	}
	$password = key_decode($user['password']);
	$headers = array('Authorization' => 'Basic ' . base64_encode("{$user['username']}:$password"));
	$request = new WP_Http;
	$result = $request -> request($api_url , array('method' => 'POST', 'body' => $body, 'headers' => $headers));
}
// 人人网
function wp_update_renren($user, $status) {
	if (function_exists('wp_renren_status') && $user['session_key']) {
		return wp_renren_status($user['session_key'], $status);
	} elseif ($user["username"] && $user['password']) {
		$cookie = tempnam('./tmp', 'renren');
		$password = key_decode($user['password']);
		$ch = wp_getCurl($cookie, "http://passport.renren.com/PLogin.do");
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'email=' . rawurlencode($user["username"]) . '&password=' . rawurlencode($password) . '&autoLogin=true&origURL=http%3A%2F%2Fwww.renren.com%2FHome.do&domain=renren.com');
		$str = wp_update_result($ch);
		$pattern = "/get_check:'([^']+)'/";
		preg_match($pattern, $str, $matches);
		$get_check = $matches[1];
		$ch = wp_getCurl($cookie, "http://status.renren.com/doing/update.do");
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'c=' . rawurlencode($status) . '&raw=' . rawurlencode($status) . '&isAtHome=1&publisher_form_ticket=' . $get_check . '&requestToken=' . $get_check);
		curl_setopt($ch, CURLOPT_REFERER, 'http://status.renren.com/ajaxproxy.htm');
		$ret = wp_update_result($ch);
	} 
}
// 开心网
function wp_update_kaixin001($user, $status, $vaule = "") {
	if (function_exists('wp_kaixin_status') && $user['session_key']) {
		wp_kaixin_status($user['session_key'], $status, $vaule); 
	}
}
// 新浪微博, 转发一条微博信息
function wp_repost_t_sina($tok, $sid, $text) {
	if ($tok['oauth_token']) {
		class_exists('sinaOAuth') or require(dirname(__FILE__) . "/OAuth/sina_OAuth.php");
		$to = new sinaClient(SINA_APP_KEY, SINA_APP_SECRET, $tok['oauth_token'], $tok['oauth_token_secret']);
		$result = $to -> repost($sid, $text);
	} elseif ($tok['access_token']) { // V2.0
		class_exists('OAuthV2') or require(dirname(__FILE__) . "/OAuth/OAuthV2.php");
		class_exists('sinaClientV2') or require(dirname(__FILE__) . "/OAuth/sina_OAuthV2.php");
		$to = new sinaClientV2(SINA_APP_KEY, SINA_APP_SECRET, $tok['access_token']);
		$result = $to -> repost($sid, $text);
	} 
	return $result;
}
// 腾讯微博, 对一条微博信息进行评论
function wp_comment_t_qq($tok, $sid, $text) {
	class_exists('qqOAuth') or require(dirname(__FILE__) . "/OAuth/qq_OAuth.php");
	$to = new qqClient(QQ_APP_KEY, QQ_APP_SECRET, $tok['oauth_token'], $tok['oauth_token_secret']);
	$result = $to -> comment($sid, $text);
	return $result;
}
// api
function wp_update_api($status) {
	$api_url = 'http://open.smyx.net/weibo/api.php';
	$api_url = apply_filters('wp_update_api', $api_url, $status);
	$request = new WP_Http;
	$result = $request -> request($api_url , array('method' => 'POST', 'body' => $status));
}

function wp_getCurl($cookie, $url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12');
	curl_setopt($ch, CURLOPT_POST, 1);
	return $ch;
}

function wp_update_result($ch) {
	$str = curl_exec($ch);
	curl_close($ch);
	unset($ch);
	return $str;
}