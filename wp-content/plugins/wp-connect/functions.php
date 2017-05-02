<?php
/**
 * 自定义函数
 */
if (!function_exists('mb_substr')) {
	function mb_substr($str, $start = 0, $length = 0, $encode = 'utf-8') {
		$encode_len = ($encode == 'utf-8') ? 3 : 2;
		for($byteStart = $i = 0; $i < $start; ++$i) {
			$byteStart += ord($str{$byteStart}) < 128 ? 1 : $encode_len;
			if ($str{$byteStart} == '') return '';
		} 
		for($i = 0, $byteLen = $byteStart; $i < $length; ++$i)
		$byteLen += ord($str{$byteLen}) < 128 ? 1 : $encode_len;
		return substr($str, $byteStart, $byteLen - $byteStart);
	} 
} 
if (!function_exists('mb_strlen')) {
	function mb_strlen($str, $encode = 'utf-8') {
		return ($encode == 'utf-8') ? strlen(utf8_decode($str)) : strlen($str);
	} 
}
// 使用键名比较计算数组的差集 array_diff_key  < 5.1.0
if (!function_exists('array_diff_key')) {
	function array_diff_key() {
		$arrs = func_get_args();
		$result = array_shift($arrs);
		foreach ($arrs as $array) {
			foreach ($result as $key => $v) {
				if (array_key_exists($key, $array)) {
					unset($result[$key]);
				} 
			} 
		} 
		return $result;
	} 
}
// 根据键名、键值对比,得到数组的差集 array_diff_assoc  < 4.3.0
if (!function_exists('array_diff_assoc')) {
	function array_diff_assoc($a1, $a2) {
		foreach($a1 as $key => $value) {
			if (isset($a2[$key])) {
				if ((string) $value !== (string) $a2[$key]) {
					$r[$key] = $value;
				} 
			} else {
				$r[$key] = $value;
			} 
		} 
		return $r;
	} 
} 
// 使用键名比较计算数组的交集 array_intersect_key  < 5.1.0
if (!function_exists('array_intersect_key')) {
	function array_intersect_key($isec, $keys) {
		$argc = func_num_args();
		if ($argc > 2) {
			for ($i = 1; !empty($isec) && $i < $argc; $i++) {
				$arr = func_get_arg($i);
				foreach (array_keys($isec) as $key) {
					if (!isset($arr[$key])) {
						unset($isec[$key]);
					} 
				} 
			} 
			return $isec;
		} else {
			$res = array();
			foreach (array_keys($isec) as $key) {
				if (isset($keys[$key])) {
					$res[$key] = $isec[$key];
				} 
			} 
			return $res;
		} 
	} 
}
// 从数组中取出一段，保留键值 array_slice  < 5.0.2
if (!function_exists('php_array_slice')) {
	function php_array_slice($array, $offset, $length = null, $preserve_keys = false) {
		if (!$preserve_keys || version_compare(PHP_VERSION, '5.0.1', '>')) {
			return array_slice($array, $offset, $length, $preserve_keys);
		} 
		if (!is_array($array)) {
			user_error('The first argument should be an array', E_USER_WARNING);
			return;
		} 
		$keys = array_slice(array_keys($array), $offset, $length);
		$ret = array();
		foreach ($keys as $key) {
			$ret[$key] = $array[$key];
		} 
		return $ret;
	} 
}
if (!function_exists('parse_url_detail')) {
	function parse_url_detail($url) {
		$parts = parse_url($url);
		if(isset($parts['query'])) {
			parse_str(urldecode($parts['query']), $str);
		} 
		return $str;
	} 
}
// 字符长度(一个汉字代表一个字符，两个字母代表一个字符)
if (!function_exists('wp_strlen')) {
	function wp_strlen($text) {
		$a = mb_strlen($text, 'utf-8');
		$b = strlen($text);
		$c = $b / 3 ;
		$d = ($a + $b) / 4;
		if ($a == $b) { // 纯英文、符号、数字
			return $b / 2;
		} elseif ($a == $c) { // 纯中文
			return $a;
		} elseif ($a != $c) { // 混合
			return $d;
		} 
	} 
} 
// 截取字数
if (!function_exists('wp_status')) {
	function wp_status($content, $url, $length, $num = '') {
		$temp_length = (mb_strlen($content, 'utf-8')) + (mb_strlen($url, 'utf-8'));
		if ($num) {
			$temp_length = (wp_strlen($content)) + (wp_strlen($url));
		} 
		if ($url) {
			$length = $length - 4; // ' - '
			$url = ' ' . $url;
		} 
		if ($temp_length > $length) {
			$chars = $length - 3 - mb_strlen($url, 'utf-8'); // '...'
			if ($num) {
				$chars = $length - wp_strlen($url);
				$str = mb_substr($content, 0, $chars, 'utf-8');
				preg_match_all("/([\x{0000}-\x{00FF}]){1}/u", $str, $half_width); // 半角字符
				$chars = $chars + count($half_width[0]) / 2;
			} 
			$content = mb_substr($content, 0, $chars, 'utf-8');
			$content = $content . "...";
		} 
		$status = $content . $url;
		return trim($status);
	} 
} 

if (!function_exists('wp_urlencode')) {
	function wp_urlencode($url) {
		$a = array('+', '%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
		$b = array(" ", "!", "*", "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
		$url = str_replace($a, $b, urlencode($url));
		return $url;
	} 
} 

if (!function_exists('wp_replace')) {
	function wp_replace($str) {
		$a = array('&#160;', '&#038;', '&#8211;', '&#8216;', '&#8217;', '&#8220;', '&#8221;', '&amp;', '&lt;', '&gt', '&ldquo;', '&rdquo;', '&nbsp;', 'Posted by Wordmobi');
		$b = array(' ', '&', '-', '‘', '’', '“', '”', '&', '<', '>', '“', '”', ' ', '');
		$str = str_replace($a, $b, strip_tags($str));
		return trim($str);
	} 
} 

if (!function_exists('class_http')) {
	function close_curl() {
		if (!extension_loaded('curl')) {
			return " <span style=\"color:blue\">请在php.ini中打开扩展extension=php_curl.dll</span>";
		} else {
			$func_str = '';
			if (!function_exists('curl_init')) {
				$func_str .= "curl_init() ";
			} 
			if (!function_exists('curl_setopt')) {
				$func_str .= "curl_setopt() ";
			} 
			if (!function_exists('curl_exec')) {
				$func_str .= "curl_exec()";
			} 
			if ($func_str)
				return " <span style=\"color:blue\">不支持 $func_str 等函数，请在php.ini里面的disable_functions中删除这些函数的禁用！</span>";
		} 
	} 
	// SSL
	function http_ssl($url) {
		$arrURL = parse_url($url);
		$r['ssl'] = $arrURL['scheme'] == 'https' || $arrURL['scheme'] == 'ssl';
		$is_ssl = isset($r['ssl']) && $r['ssl'];
		if ($is_ssl && !extension_loaded('openssl'))
			return wp_die('您的主机不支持openssl，请查看<a href="' . MY_PLUGIN_URL . '/check.php" target="_blank">环境检查</a>');
	} 
	function class_http($url, $params = array()) {
		if ($params['http']) {
			$class = 'WP_Http_' . ucfirst($params['http']);
		} else {
			if (!close_curl()) {
				$class = 'WP_Http_Curl';
			} else {
				http_ssl($url);
				if (@ini_get('allow_url_fopen') && function_exists('fopen')) {
					$class = 'WP_Http_Streams';
				} elseif (function_exists('fsockopen')) {
					$class = 'WP_Http_Fsockopen';
				} else {
					return wp_die('没有可以完成请求的 HTTP 传输器，请查看<a href="' . MY_PLUGIN_URL . '/check.php" target="_blank">环境检查</a>');
				} 
			} 
		} 
		$http = new $class;
		$response = $http -> request($url, $params);
		if (!is_array($response)) {
			if ($params['method'] == 'GET' && @ini_get('allow_url_fopen') && function_exists('file_get_contents')) {
				return file_get_contents($url . '?' . $params['body']);
			} 
			$errors = $response -> errors;
			$error = $errors['http_request_failed'][0];
			if (!$error)
				$error = $errors['http_failure'][0];
			if ($error == "couldn't connect to host" || strpos($error, 'timed out') !== false) {
				return;
			} 
			wp_die('出错了: ' . $error . '<br /><br />可能是您的主机不支持，请查看<a href="' . MY_PLUGIN_URL . '/check.php" target="_blank">环境检查</a>');
		} 
		return $response['body'];
	} 
    // GET
	function get_url_contents($url, $timeout = 30) {
		if (!close_curl()) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			$content = curl_exec($ch);
			curl_close($ch);
			return $content;
		} else {
			$params = array();
			if (@ini_get('allow_url_fopen')) {
				if (function_exists('file_get_contents')) {
					return file_get_contents($url);
				} 
				if (function_exists('fopen')) {
					$params['http'] = 'streams';
				} 
			} elseif (function_exists('fsockopen')) {
				$params['http'] = 'fsockopen';
			} else {
				return wp_die('没有可以完成请求的 HTTP 传输器，请查看<a href="' . MY_PLUGIN_URL . '/check.php" target="_blank">环境检查</a>');
			} 
			$params += array("method" => 'GET',
				"timeout" => $timeout,
				"sslverify" => false
				);
			return class_http($url, $params);
		} 
	} 

	function get_url_array($url) {
		return json_decode(get_url_contents($url), true);
	} 
} 

function close_socket() {
	if (function_exists('fsockopen')) {
		$fp = 'fsockopen()';
	} elseif (function_exists('pfsockopen')) {
		$fp = 'pfsockopen()';
	} elseif (function_exists('stream_socket_client')) {
		$fp = 'stream_socket_client()';
	} 
	if (!$fp) {
		return " <span style=\"color:blue\">必须支持以下函数中的其中一个： fsockopen() 或者 pfsockopen() 或者 stream_socket_client() 函数，请在php.ini里面的disable_functions中删除这些函数的禁用！</span>";
	} 
} 

function sfsockopen($host, $port, $errno, $errstr, $timeout) {
	if (function_exists('fsockopen')) {
		$fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
	} elseif (function_exists('pfsockopen')) {
		$fp = @pfsockopen($host, $port, $errno, $errstr, $timeout);
	} elseif (function_exists('stream_socket_client')) {
		$fp = @stream_socket_client($host . ':' . $port, $errno, $errstr, $timeout);
	} 
	return $fp;
} 

if (!function_exists('key_authcode')) {
	function key_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
		$ckey_length = 4;
		$key = ($key) ? md5($key) : '';
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), - $ckey_length)) : '';

		$cryptkey = $keya . md5($keya . $keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		} 

		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		} 

		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		} 

		if ($operation == 'DECODE') {
			if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			} 
		} else {
			return $keyc . str_replace('=', '', base64_encode($result));
		} 
	} 
} 

function key_encode($string, $expiry = 0) {
	return key_authcode($string, 'ENCODE', 'WP-CONNECT', $expiry);
} 

function key_decode($string) {
	return key_authcode($string, 'DECODE', 'WP-CONNECT');
} 

if (!function_exists('filter_value')) {
	function filter_value($v) { // array_filter $callback
		if (is_array($v)) $v = $v[0];
		if ($v !== "") {
			return true;
		} 
		return false;
	} 
} 

if (!function_exists('wp_in_array')) {
	function wp_in_array($a, $b) {
		$arrayA = explode(',', rtrim($a, ','));
		$arrayB = explode(',', rtrim($b, ','));
		if (array_intersect($arrayA, $arrayB)) {
			return true;
		} 
		return false;
	} 
}

if (!function_exists('post_user')) {
	function post_user($username, $password, $pwd) { // $pwd为旧密码
		$username = trim($username);
		$password = trim($password);
		return array($username, (!$username) ? '' : (($password) ? key_encode($password) : $pwd));
	} 
} 

if (!function_exists('default_values')) { // 设置默认值
	function default_values($key, $vaule, $array) {
		if (!is_array($array)) {
			return true;
		} else {
			if ($array[$key] == $vaule || !array_key_exists($key, $array)) {
				return true;
		    }
		}
	} 
}

if (!function_exists('ifabc')) {
	function ifab($a, $b) {
		return $a ? $a : $b;
	} 
	function ifb($a, $b) {
		return $a ? $b : '';
	} 
	function ifac($a, $b, $c) {
		return $a ? $a : ($b ? $c : '');
	} 
	function ifabc($a, $b, $c) {
		return $a ? $a : ($b ? $b : $c);
	} 
	function ifold($str, $old, $new) { // 以旧换新
		return (empty($str) || $str == $old) ? $new : $str;
	}
} 
// 检测用户名，如果重复前面加u
function ifuser($username) {
	return username_exists($username) ? ifuser('u' . $username) : $username;
} 

/**
 * 接口函数
 */
// 自定义短网址
function get_url_short($url) {
	global $wptm_options;
	if ($wptm_options['t_cn'] == 1) {
		$url = url_short_t_cn($url);
	} elseif ($wptm_options['t_cn'] == 2) {
		$url = url_short_dwz_cn($url);
	} 
	return $url;
} 
// 新浪t.cn短网址
function url_short_t_cn($long_url) {
	$api_url = 'http://api.weibo.com/2/short_url/shorten.json?source=3845272542&url_long=' . urlencode($long_url);
	$request = new WP_Http;
	$result = $request -> request($api_url);
	if (is_array($result)) {
		$result = $result['body'];
		$result = json_decode($result, true);
		$result = $result['urls'];
		$url_short = $result[0]['url_short'];
		if ($url_short) $long_url = $url_short;
	}
	return $long_url;
} 
// 兼容旧版
if (!function_exists('get_t_cn')) {
	function get_t_cn($long_url) {
		return url_short_t_cn($long_url);
	} 
} 
// 百度dwz.cn短网址
function url_short_dwz_cn($long_url) {
	$request = new WP_Http;
	$api_url = 'http://dwz.cn/create.php';
	$result = $request -> request($api_url , array('method' => 'POST', "timeout" => 5, 'body' => 'url=' . urlencode($long_url)));
	if (is_array($result)) {
		$result = $result['body'];
		$result = json_decode($result, true);
		$url_short = $result['tinyurl'];
		if ($url_short) $long_url = $url_short;
	}
	return $long_url;
} 

/**
 * 插件函数
 */
// 设置cookie
function wp_connect_set_cookie($name, $value, $expire = '') {
	//if (is_array($value)) {
	//	setcookie($name, json_encode($value), $expire);
	//} elseif (is_string($value)) {
	//	setcookie($name, $value, $expire);
	//}
	$_SESSION[$name] = $value;

} 
// 清空cookie
function wp_connect_clear_cookie($name) {
	//setcookie($name, "", time() - 3600*24);
	$_SESSION[$name] = "";
}
// 获取cookie
function wp_connect_get_cookie($name) {
	//return json_decode(stripslashes($_COOKIE[$name]), true);
	return $_SESSION[$name];
} 
// 开放平台KEY v1.9.12
function get_appkey() {
	global $wptm_connect;
	$sohu = get_option('wptm_opensohu');
	$netease = get_option('wptm_opennetease');
	return array('2' => array($wptm_connect['msn_api_key'], $wptm_connect['msn_secret']),
		'5' => array($sohu['app_key'], $sohu['secret']),
		'6' => array(ifab($netease['app_key'], '9fPHd1CNVZAKGQJ3'), ifab($netease['secret'], 'o98cf9oY07yHwJSjsPSYFyhosUyd43vO')),
		'7' => array($wptm_connect['renren_api_key'], $wptm_connect['renren_secret']),
		'8' => array($wptm_connect['kaixin001_api_key'], $wptm_connect['kaixin001_secret']),
		'13' => array($wptm_connect['qq_app_id'], $wptm_connect['qq_app_key']),
		'16' => array($wptm_connect['taobao_api_key'], $wptm_connect['taobao_secret']),
		'17' => array(TIANYA_APP_KEY, TIANYA_APP_SECRET),
		'19' => array($wptm_connect['baidu_api_key'], $wptm_connect['baidu_secret']),
		'28' => array(T_APP_KEY, T_APP_SECRET)
		);
}
// 获得user_id
function get_user_by_meta_value($meta_key, $meta_value) {
	global $wpdb;
	$sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '%s' AND meta_value = '%s'";
	return $wpdb -> get_var($wpdb -> prepare($sql, $meta_key, $meta_value));
}
// 保存wp_comments表某个字段
if (!function_exists('wp_update_comment_key')) {
	function wp_update_comment_key($comment_ID, $comment_key, $vaule) {
		global $wpdb;
		$$comment_key = $vaule;
		$result = $wpdb -> update($wpdb -> comments, compact($comment_key), compact('comment_ID'));
		return $result;
	} 
}
// 获得登录者ID
if (!function_exists('get_current_user_id')) {
	function get_current_user_id() {
		$user = wp_get_current_user();
        return ( isset( $user->ID ) ? (int) $user->ID : 0 );
    }
}

function wp_get_user_info($uid) {
	$user = get_userdata($uid);
	$userinfo = array('user_login' => $user->user_login, 'user_pass' => $user->user_pass, 'user_email' => $user->user_email, 'user_url' => $user->user_url);
	return $userinfo;
}
// 通过用户ID，获得用户数据
function get_user_by_uid($user_id, $field = '') {
	if (empty($field)) {
		return get_userdata($user_id);
	} 
	global $wpdb;

	if (!is_numeric($user_id))
		return false;

	$user_id = absint($user_id);
	if (!$user_id)
		return false;

	$user = wp_cache_get($user_id, 'users');

	if ($user) {
		return array($field => $user -> $field);
	} 

	if (!$user = $wpdb -> get_row("SELECT $field FROM $wpdb->users WHERE ID = $user_id LIMIT 1", ARRAY_A))
		return false;
	return $user;
}

function get_username($uid) { // 通过用户ID，获得用户名
	$user = get_user_by_uid($uid, 'user_login');
	return $user['user_login'];
}

function get_useremail($uid) { // 通过用户ID，获得用户邮箱
	$user = get_user_by_uid($uid, 'user_email');
	return $user['user_email'];
}
// 根据链接或者用户ID
if (!function_exists('get_uid_by_url')) {
	function get_uid_by_url($url) {
		if (is_user_logged_in()) {
			if ($url == admin_url('profile.php')) { // 我的个人资料
				$wpuid = get_current_user_id();
			} elseif (current_user_can('manage_options')) { // 用户
				$parse_str = parse_url_detail($url);
				$wpuid = $parse_str['user_id'];
			} 
		} 
		return $wpuid;
	}
}
// 支持中文用户名
if (default_values('chinese_username', 1, $wptm_connect)) {
	function sanitize_user_chinese_username($username, $raw_username, $strict) {
		if ($strict && $username != $raw_username) {
			$username = $raw_username;
			$username = wp_strip_all_tags($username);
			$username = remove_accents($username); 
			// Kill octets
			$username = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', $username);
			$username = preg_replace('/&.+?;/', '', $username); // Kill entities  
			// If strict, reduce to ASCII for max portability.
			$username = preg_replace('|[^a-z0-9 _.\-@\x80-\xff]|i', '', $username);

			$username = trim($username); 
			// Consolidate contiguous whitespace
			$username = preg_replace('|\s+|', ' ', $username);
		} 
		return $username;
	} 
	add_filter('sanitize_user', 'sanitize_user_chinese_username', 3, 3);
}
// 匹配视频,图片 v1.9.19
function wp_multi_media_url($content, $post_ID = '') {
	global $wptm_options;
	$richMedia = apply_filters('wp_multi_media_url', '', $content, $post_ID);
	if (is_array($richMedia) && array_filter($richMedia)) {
		return $richMedia;
	} 
	preg_match_all('/<embed[^>]+src=[\"\']{1}(([^\"\'\s]+)\.swf)[\"\']{1}[^>]+>/isU', $content, $video);
	$v_sum = count($video[1]);
	if ($v_sum > 0) {
		$v = $video[1][0];
	} else {
		$content = str_replace(array("[/", "</"), "\n", $content);
		preg_match_all('/http:\/\/(v.youku.com\/v_show|www.tudou.com\/(programs\/view|albumplay|listplay))+(?(?=[\/])(.*))/', $content, $match);
		if (count($match[0]) > 0) $v = trim($match[0][0]);
	} 
	if (empty($wptm_options['disable_pic'])) {
		preg_match_all('/<img[^>]+src=[\'"](http[^\'"]+)[\'"].*>/isU', $content, $image);
		$p_sum = count($image[1]);
		if ($p_sum > 0) {
			$p = $image[1][0];
		} 
		if (!$p || $wptm_options['thumbnail']) {
			if (is_numeric($post_ID) && function_exists('has_post_thumbnail') && has_post_thumbnail($post_ID)) { // 特色图像 WordPress v2.9.0
				if ($image_url = wp_get_attachment_image_src(get_post_thumbnail_id($post_ID), 'full'))
					$p = $image_url[0];
			} 
		} 
	} 
	if ($p || $v)
		return array($p, $v);
}
// 得到图片url
if (!function_exists('get_image_by_content')) {
	function get_image_by_content($content) {
		preg_match_all('/<img[^>]+src=[\'"](http[^\'"]+)[\'"].*>/isU', $content, $image);
		return $image[1][0];
	} 
}
// 同步QQ空间 检测
function verify_qzone() {
	if (!close_socket()) {
		error_reporting(0);
		ini_set('display_errors', 0);
		$fp = sfsockopen("smtp.qq.com", 25, $errno, $errstr, 10);
		if (!$fp) {
			echo "很抱歉！您的服务器不能同步到QQ空间，因为腾讯邮件客户端的 smtp.qq.com:25 禁止您的服务器访问！请不要在上面填写QQ号码和密码，以免发布文章时出错或者拖慢您的服务器，谢谢支持！";
		} else {
			echo "恭喜！检查通过，请在上面填写QQ号码和密码，然后发布一篇文章试试，如果不能同步(多试几次)，请务必删除刚刚填写QQ号码和密码，并保存修改，以免发布文章时出错或者拖慢您的服务器，谢谢支持！";
		} 
	} else {
		echo "很抱歉！您的服务器不支持 fsockopen() 或者 pfsockopen() 或者 stream_socket_client() 任一函数，不能同步到QQ空间，请联系空间商开启！请暂时不要在上面填写QQ号码和密码，以免发布文章时出错或者拖慢您的服务器，谢谢支持！";
	} 
}
/*
add_filter('user_contactmethods', 'wp_connect_author_page');
function wp_connect_author_page($input) {
	$input['imqq'] = 'QQ';
	//$input['msn'] = 'MSN';
	//unset($input['yim']);
	//unset($input['aim']);
	return $input;
}
*/
// 社会化分享按钮，共52个
function wp_social_share_title() {
	$socialShare_title = array("qzone" => "QQ空间",
		"sina" => "新浪微博",
		"baidu" => "百度搜藏",
		"renren" => "人人网",
		"qq" => "腾讯微博",
		"kaixin001" => "开心网",
		"sohu" => "搜狐微博",
		"hibaidu" => "百度空间",
		"t163" => "网易微博",
		"douban" => "豆瓣",
		"taojianghu" => "淘江湖",
		"msn" => "MSN",
		"buzz" => "谷歌Buzz",
		"qqshuqian" => "QQ书签",
		"tieba" => "百度贴吧",
		"shequ51" => "51社区",
		"shouji" => "手机",
		"zhuaxia" => "抓虾",
		"baishehui" => "搜狐白社会",
		"ifeng" => "凤凰微博",
		"pengyou" => "腾讯朋友",
		"facebook" => "Facebook",
		"twitter" => "Twitter",
		"tianya" => "天涯社区",
		"fanfou" => "饭否",
		"sc115" => "115收藏",
		"feixin" => "飞信",
		"digu" => "嘀咕",
		"linkedin" => "LinkedIn",
		"tongxue" => "同学网",
		"youdao" => "有道书签",
		"google" => "Google",
		"delicious" => "Delicious",
		"digg" => "Digg",
		"yahoo" => "Yahoo!",
		"live" => "微软live",
		"hexun" => "和讯微博",
		"xianguo" => "鲜果",
		"zuosa" => "做啥",
		"shuoke" => "139说客",
		"myspace" => "聚友网",
		"waakee" => "挖客",
		"leshou" => "乐收",
		"mop" => "猫扑推客",
		"cnfol" => "中金微博",
		"douban9" => "豆瓣9点",
		"dream163" => "梦幻人生",
		"taonan" => "淘男网",
		"club189" => "天翼社区",
		"baohe" => "宝盒网",
		"renmaiku" => "人脉库",
		"ushi" => "优士网");
	return array_merge( apply_filters('socialShare_title', array()), $socialShare_title );
} 

?>