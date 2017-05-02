<?php
/**
 * V1.9.15 - v3.4
 * 应一些网友要求，增加使用其他媒体登录插件的用户数据转换，以便旧用户支持WordPress连接微博插件，不会删除原有插件数据。
 * 支持以下插件：
 * 1、新浪连接: http://fairyfish.net/project/sina-connect/  或者 http://wordpress.org/extend/plugins/sina-connect/
 * 2、腾讯连接: http://fairyfish.net/2010/12/20/qq-connect/
 * 3、qq-connect: http://wordpress.org/extend/plugins/qq-connect/
 * 4、Douban Connect: http://fairyfish.net/2009/06/15/douban-connect/
 * 5、Sina_Weibo_Plus: http://www.ecihui.com/tech/227.htm 或者 http://wordpress.org/extend/plugins/sina-weibo-plus/
 * 6、Social Medias Connect: http://wordpress.org/extend/plugins/social-medias-connect/
 * 7、sina weibo wordpress plugin: http://wordpress.org/extend/plugins/sina-weibo-wordpress-plugin-by-wwwresult-searchcom
 * 8、EzEngage: http://wordpress.org/extend/plugins/ezengage
 */
function smc_get_id($name) {
	$weibo = array('sinaweibo' => array('stid', 'stid'),
		'qqweibo' => array('tqqid', 'qtid', '@t.qq.com'),
		'sohuweibo' => array('sohuid', 'shtid', '@t.sohu.com'),
		'163weibo' => array('neteaseid', 'ntid', '@t.163.com'),
		'twitter' => array('twitterid', 'ttid', '@twitter.com'),
		'tianya' => array('tytid', 'tytid'),
		'renren' => array('renrenid', 'rtid'),
		'kaixin' => array('kaixinid', 'ktid'),
		'douban' => array('dtid', 'dtid')
		);
	return $weibo[$name];
} 
function smc_import_user() {
	global $wpdb;
	@ini_set("max_execution_time", 180);
	$users = $wpdb -> get_results("SELECT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key = 'smcdata'", ARRAY_A);
	foreach ($users as $user) {
		smc_save_user($user['user_id'], maybe_unserialize($user['meta_value']));
	} 
} 
function smc_save_user($uid, $ret) {
	if(isset($ret['socialmedia'])){ // v2.0及以上版本
		foreach ($ret['socialmedia'] as $media => $data) {
			if ($media == 'sinaweibo') {
				$path = explode('/', $data['avatar']);
				update_usermeta($uid, 'stid', (int)$path[3]);
			} elseif (in_array($media, array('kaixin', 'renren', 'sohuweibo', 'taobao', 'tianya', 'baidu', '360cn', 'facebook', 'msnlive'))) {
				if ($media == 'sohuweibo') {$media = 'sohu';}elseif ($media == '360cn') {$media = 'guard360';}elseif ($media == 'tianya') {$media = 'tyt';}
				update_usermeta($uid, $media . 'id', $data['uid']);
			}
		}
	} elseif (isset($ret['smcweibo']) && $id = smc_get_id($ret['smcweibo'])) {
		if (empty($ret['smcid'])) { // v1.5及以上版本
			if ($id[0] == 'stid') {
				$path = explode('/', $ret['avatar']);
				update_usermeta($uid, $id[0], $path[3]);
			} elseif (in_array($id[0], array('tqqid', 'renrenid', 'kaixinid'))) {
				update_usermeta($uid, $id[0], $ret['username']);
				update_usermeta($uid, $id[1], $ret['avatar']);
			} elseif ($id[0] == 'dtid') {
				update_usermeta($uid, $id[0], $ret['username']);
			} elseif ($id[0] == 'tytid') {
				$path = explode('/', $ret['userurl']);
				update_usermeta($uid, $id[0], $path[3]);
			} elseif ($id[0] == 'sohuid') {
				update_usermeta($uid, $id[0], str_replace('http://t.sohu.com/u/', '', $ret['userurl']));
				update_usermeta($uid, $id[1], $ret['avatar']);
			} elseif ($id[0] == 'twitterid') {
				update_usermeta($uid, $id[0], str_replace('@twitter.com', '', $ret['useremail']));
				update_usermeta($uid, $id[1], $ret['avatar']);
			} 
			update_usermeta($uid, 'last_login', $id[1]);
		} else {
			if ($id[0] == 'stid' || $id[0] == 'dtid') {
				update_usermeta($uid, $id[0], $ret['smcid']);
				update_usermeta($uid, 'last_login', $id[1]);
			} else {
				$user_email = get_useremail($uid);
				if (strstr($user_email, '@') == $id[2]) {
					update_usermeta($uid, $id[0], str_replace($id[2], '', $user_email));
					update_usermeta($uid, $id[1], $ret['smcid']);
					update_usermeta($uid, 'last_login', $id[1]);
				} 
			} 
		} 
	} 
} 
// var_dump(smc_import_user());
function sc_import_user() {
	global $wpdb;
	@ini_set("max_execution_time", 180);
	$users = $wpdb -> get_results("SELECT user_id,meta_key,meta_value FROM $wpdb->usermeta WHERE (meta_key = 'scid' OR meta_key = 'qcid' OR meta_key = 'dcid')", ARRAY_A);
	foreach ($users as $user) {
		update_usermeta($user['user_id'], str_replace('cid', 'tid', $user['meta_key']), $user['meta_value']);
		update_usermeta($user['user_id'], 'last_login', str_replace('cid', 'tid', $user['meta_key']));
	} 
} 
// var_dump(sc_import_user());
function yaha_import_user() {
	global $wpdb;
	@ini_set("max_execution_time", 180);
	$users = $wpdb -> get_results("SELECT user_id FROM $wpdb->usermeta WHERE meta_key like '%sina_open_token_array%'", ARRAY_A);
	foreach ($users as $user) {
		$open_id = get_user_meta($user['user_id'], 'open_id', true);
		if ($open_id) {
			update_usermeta($user['user_id'], 'stid', $open_id);
			update_usermeta($user['user_id'], 'last_login', 'stid');
		} 
	} 
} 
// var_dump(yaha_import_user());
function ezengage_import_user() {
	global $wpdb;
	@ini_set("max_execution_time", 180);
	$table_name = $wpdb -> prefix . 'ezengage_identity';
	$users = $wpdb -> get_results("SELECT user_id,provider,identity,avatar_url FROM $table_name", ARRAY_A);

	$name = array('sinaweibo' => array('stid', 'stid', 'http://t.sina.com.cn/'),
		'tencentweibo' => array('tqqid', 'qtid', 'http://t.qq.com/'),
		'renren' => array('renrenid', 'rtid', 'http://www.renren.com/home?id='),
		'sohuweibo' => array('sohuid', 'shtid', 'http://t.sohu.com/'),
		'qzone' => array('qqid', 'qqtid', 'http://qzone.qq.com/')
		);

	foreach ($users as $user) {
		$weibo = $name[$user['provider']];
		if ($weibo) {
			update_usermeta($user['user_id'], $weibo[0], str_replace($weibo[2], '', $user['identity']));
			if ($weibo[1] != 'stid') {
				update_usermeta($user['user_id'], $weibo[1], $user['avatar_url']);
			} 
			update_usermeta($user['user_id'], 'last_login', $weibo[1]);
		} 
	} 
} 
// var_dump(ezengage_import_user());
function all_import_user() {
	@ini_set("max_execution_time", 300);
	sc_import_user();
	smc_import_user();
	yaha_import_user();
	ezengage_import_user();
} 

?>