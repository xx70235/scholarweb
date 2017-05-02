<?php
include "../../../wp-config.php";
do_action('connect_init');

if ($_GET['do'] == "profile") {
	if (is_user_logged_in()) {
		if ($_POST['add_qq'] || $_POST['add_sina'] || $_POST['add_renren'] || $_POST['add_shuoshuo'] || $_POST['add_sohu'] || $_POST['add_netease'] || $_POST['add_douban'] || $_POST['add_tianya'] || $_POST['add_kaixin'] || $_POST['add_twitter']) {
			wp_connect_header();
		} else {
			$user_id = get_current_user_id();
			wp_user_profile_update($user_id);
			header('Location:' . admin_url('profile.php'));
		} 
	} 
} 

if ($_GET['do'] == "page") {
	$wptm_options = get_option('wptm_options');
	$wptm_advanced = get_option('wptm_advanced');
	$password = $_POST['password'];
	if (isset($_POST['message'])) {
		if (($wptm_options['page_password'] && $password == $wptm_options['page_password']) || (is_user_logged_in() && function_exists('wp_connect_advanced') && $wptm_advanced['registered_users'])) {
			wp_update_page();
		} else {
			echo 'pwderror';
		} 
	} 
} 

if ($_GET['do'] == "login") {
	$user = wp_connect_get_cookie("wp_connect_cookie_user");
	if ($user) {
		$user[0][1] = ifuser($user[0][1]);
		wp_connect_login($user[0], $user[1], '', true);
		header('Location:' . $user[2]);
	} 
} 

?>