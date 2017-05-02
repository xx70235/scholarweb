<?php
include "../../../wp-config.php";
define('WORDPRESS_LOGIN', true);
do_action('connect_init');

/**
 * 注册
 * 
 * @since 1.9.19
 */
// 注册验证
function wp_check_new_user($user_login, $user_email) {
	global $wptm_connect;
	$user_login = sanitize_user($user_login);
	$user_email = apply_filters('user_registration_email', $user_email);
	// Check the username
	if ($user_login == '') {
		return __('<strong>ERROR</strong>: Please enter a username.');
	} elseif (!validate_username($user_login)) {
		return "<strong>错误</strong>：用户名只能包含字母、数字、空格、下划线、连字符（-）、点号（.）和 @ 符号。";
		$user_login = '';
	} elseif (username_exists($user_login) || in_array($user_login, explode(',', $wptm_connect['disable_username']))) {
		return __('<strong>ERROR</strong>: This username is already registered, please choose another one.');
	} 
	// Check the e-mail address
	if ($user_email == '') {
		return __('<strong>ERROR</strong>: Please type your e-mail address.');
	} elseif (!is_email($user_email)) {
		return __('<strong>ERROR</strong>: The email address isn&#8217;t correct.');
		$user_email = '';
	} elseif (email_exists($user_email)) {
		return __('<strong>ERROR</strong>: This email is already registered, please choose another one.');
	} 
} 
// 绑定验证
function wp_check_bind_user($username, $password) {
	if (empty($password))
		return __('<strong>ERROR</strong>: The password field is empty.');

	$userdata = get_userdatabylogin($username);

	if (!$userdata)
		return sprintf(__('<strong>ERROR</strong>: Invalid username. <a href="%s" title="Password Lost and Found">Lost your password</a>?'), site_url('wp-login.php?action=lostpassword', 'login'));

	if (is_multisite()) {
		// Is user marked as spam?
		if (1 == $userdata -> spam)
			return __('<strong>ERROR</strong>: Your account has been marked as a spammer.'); 
		// Is a user's blog marked as spam?
		if (!is_super_admin($userdata -> ID) && isset($userdata -> primary_blog)) {
			$details = get_blog_details($userdata -> primary_blog);
			if (is_object($details) && $details -> spam == 1)
				return __('Site Suspended.');
		} 
	} 

	$userdata = apply_filters('wp_authenticate_user', $userdata, $password);
	if (is_wp_error($userdata))
		return;
	if (!wp_check_password($password, $userdata -> user_pass, $userdata -> ID))
		return sprintf(__('<strong>ERROR</strong>: The password you entered for the username <strong>%1$s</strong> is incorrect. <a href="%2$s" title="Password Lost and Found">Lost your password</a>?'), $username, site_url('wp-login.php?action=lostpassword', 'login'));
} 

$user = wp_connect_get_cookie("wp_connect_cookie_user");
if ($user) {
	$redirect_to = $user[2];
	if ($_POST['login'] && !$_POST['bind']) {
		$user_login = trim($_POST['login']);
		$user_email = trim($_POST['email']);
		$errors = wp_check_new_user($user_login, $user_email);
		if (!$errors) {
			$user[0][1] = $user_login;
			$user[1] = $user_email;
			$user[0][8] = trim($_POST['password']);
			$wpuid = wp_connect_login($user[0], $user[1], '', true);
			header('Location:' . $redirect_to);
		} 
	} elseif ($_POST['login'] && $_POST['bind']) {
		$username = trim($_POST['login']);
		$password = trim($_POST['password']);
		$errors = wp_check_bind_user($username, $password);
		if (!$errors) {
			$uid = username_exists($username);
			$wpuid = wp_connect_login($user[0], $user[1], $uid);
			header('Location:' . $redirect_to);
		} 
	} 
	include('login.inc.php');
}

?>