<?php
include "../../../wp-config.php";
session_start();
if (empty($_SESSION['wp_url_bind'])) {
	header('Location:' . get_bloginfo('url'));
	return;
} 
if (is_user_logged_in()) {
	include_once(dirname(__FILE__) . '/config.php');
	$redirect_to = $_SESSION['wp_url_bind'];
	$bind = isset($_GET['bind']) ? strtolower($_GET['bind']) : "";
	if ($bind == "sina") { // OAuth V2
		// $_SESSION['wp_url_login'] = $bind;
		if (SINA_APP_KEY == $sina_app_key_default) { // 默认key
			$aurl = "http://smyx.sinaapp.com/connect.php?client_id=" . SINA_APP_KEY . "&redirect_to=" . urlencode(plugins_url('wp-connect/go.php'));
		} else { // 自定义key
			$_SESSION['source_receiver'] = 'wp-connect/go.php';
			$aurl = "https://api.weibo.com/oauth2/authorize?client_id=" . SINA_APP_KEY . "&redirect_uri=" . urlencode(plugins_url('wp-connect/dl_receiver.php')) . "&response_type=code&scope=follow_app_official_microblog";
		} 
		header('Location:' . $aurl);
		die();
	} elseif (isset($_GET['code'])) {
		$keys = array();
		class_exists('OAuthV2') or require(dirname(__FILE__) . "/OAuth/OAuthV2.php");
		$o = new OAuthV2(SINA_APP_KEY, SINA_APP_SECRET);
		$keys['code'] = $_GET['code'];
		$keys['access_token_url'] = 'https://api.weibo.com/oauth2/access_token';
		if (!empty($_SESSION['source_receiver'])) {
			$keys['redirect_uri'] = plugins_url('wp-connect/dl_receiver.php');
			$_SESSION['source_receiver'] = "";
		} else {
			$keys['redirect_uri'] = "http://smyx.sinaapp.com/receiver.php";
		} 
		$token = $o -> getAccessToken($keys);
		if ($token['access_token']) {
			$oauth_token = array('access_token' => $token['access_token'], 'expires_in' => BJTIMESTAMP + $token['expires_in']);
			if ($redirect_to == WP_CONNECT) {
				update_option('wptm_sina', $oauth_token);
			} elseif ($_SESSION['user_id']) {
				update_usermeta($_SESSION['user_id'], 'wptm_sina', $oauth_token);
			} 
		} else {
			return var_dump($token);
		}
		header('Location:' . $redirect_to);
		die();
	} 
	$callback = isset($_GET['callback']) ? $_GET['callback'] : '';
	require_once(dirname(__FILE__) . '/OAuth/OAuth.php');
	if ($bind) {
		include_once(dirname(__FILE__) . '/OAuth/' . $bind . '_OAuth.php');
		switch ($bind) {
			case "sina":
				$to = new sinaOAuth(SINA_APP_KEY, SINA_APP_SECRET);
				break;
			case "qq":
				$to = new qqOAuth(QQ_APP_KEY, QQ_APP_SECRET);
				break;
			case "sohu":
				$to = new sohuOAuth(SOHU_APP_KEY, SOHU_APP_SECRET);
				break;
			case "netease":
				$to = new neteaseOAuth(APP_KEY, APP_SECRET);
				break;
			case "douban":
				$to = new doubanOAuth(DOUBAN_APP_KEY, DOUBAN_APP_SECRET);
				break;
			case "tianya":
				$to = new tianyaOAuth(TIANYA_APP_KEY, TIANYA_APP_SECRET);
				break;
			case "twitter":
				$to = new twitterOAuth(T_APP_KEY, T_APP_SECRET);
				break;
			default:
		} 
		$backurl = plugins_url('wp-connect/go.php?callback=' . $bind);
		$keys = $to -> getRequestToken($backurl);
		$aurl = $to -> getAuthorizeURL($keys['oauth_token'], false, $backurl);
		$_SESSION['keys'] = $keys;
		header('Location:' . $aurl);
	} elseif ($callback) {
		include_once(dirname(__FILE__) . '/OAuth/' . $callback . '_OAuth.php');
		switch ($callback) {
			case "sina":
				$to = new sinaOAuth(SINA_APP_KEY, SINA_APP_SECRET, $_SESSION['keys']['oauth_token'], $_SESSION['keys']['oauth_token_secret']);
				break;
			case "qq":
				$to = new qqOAuth(QQ_APP_KEY, QQ_APP_SECRET, $_SESSION['keys']['oauth_token'], $_SESSION['keys']['oauth_token_secret']);
				break;
			case "sohu":
				$to = new sohuOAuth(SOHU_APP_KEY, SOHU_APP_SECRET, $_SESSION['keys']['oauth_token'], $_SESSION['keys']['oauth_token_secret']);
				break;
			case "netease":
				$to = new neteaseOAuth(APP_KEY, APP_SECRET, $_SESSION['keys']['oauth_token'], $_SESSION['keys']['oauth_token_secret']);
				break;
			case "douban":
				$to = new doubanOAuth(DOUBAN_APP_KEY, DOUBAN_APP_SECRET, $_SESSION['keys']['oauth_token'], $_SESSION['keys']['oauth_token_secret']);
				break;
			case "tianya":
				$to = new tianyaOAuth(TIANYA_APP_KEY, TIANYA_APP_SECRET, $_SESSION['keys']['oauth_token'], $_SESSION['keys']['oauth_token_secret']);
				break;
			case "twitter":
				$to = new twitterOAuth(T_APP_KEY, T_APP_SECRET, $_SESSION['keys']['oauth_token'], $_SESSION['keys']['oauth_token_secret']);
				break;
			default:
		} 
		$redirect_to = $_SESSION['wp_url_bind'];
		$last_key = $to -> getAccessToken($_REQUEST['oauth_verifier']);
		if (!$last_key['oauth_token']) {
			return var_dump($last_key);
		} 
		$update = array ('oauth_token' => $last_key['oauth_token'],
			'oauth_token_secret' => $last_key['oauth_token_secret']
			);
		$tok = 'wptm_' . $callback;
		if ($redirect_to == WP_CONNECT) {
			update_option($tok, $update);
		} elseif ($_SESSION['user_id']) {
			update_usermeta($_SESSION['user_id'], $tok, $update);
		} 
		header('Location:' . $redirect_to);
	} 
} 

?>