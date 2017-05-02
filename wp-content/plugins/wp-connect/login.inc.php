<?php defined('WORDPRESS_LOGIN') or exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
	<title><?php bloginfo('name'); ?> &rsaquo; 登录</title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<?php
	wp_admin_css( 'login', true );
	wp_admin_css( 'colors-fresh', true );

	if ( $is_iphone ) { ?>
	<meta name="viewport" content="width=320; initial-scale=0.9; maximum-scale=1.0; user-scalable=0;" />
	<style type="text/css" media="screen">
	form { margin-left: 0px; }
	#login { margin-top: 20px; }
	</style>
<?php
	} elseif ( isset($interim_login) && $interim_login ) { ?>
	<style type="text/css" media="all">
	.login #login { margin: 20px auto; }
	</style>
<?php
	}
	do_action( 'login_head' );
	?>
</head>
<body class="login">
<div id="login"><h1><a href="<?php bloginfo('url'); ?>/" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>
<div id="message" >
<?php
if($errors){
	echo "<p id=\"login_error\">$errors</p>";
} else {
	echo "<p class=\"message\">";
    echo ($_POST['bind'])?'已有帐号？绑定我的帐号':"这是您第一次登录，请先完善注册信息。 <a href='save.php?do=login'>跳过这一步</a>";
    echo "</p>";
}
$user_login = (isset($_POST['login'])) ? $_POST['login'] : $user[0][2];
$user_email = (in_array($user[0][0], array('ntid','tbtid','mtid'))) ? $user[1] : $_POST['email']; // V2.3
$user_pass = $_POST['password'];
?>
</div>
<form name="registerform" id="registerform" action="" method="post">
	<p>
		<label>用户名<br />
		<input type="text" name="login" id="user_login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" size="20" tabindex="10" /></label>
	</p>
	<p id="email"<?php echo ($_POST['bind'])?' style="display: none;"':"";?>>
		<label>电子邮件<br />
		<input type="text" name="email" id="user_email" class="input" value="<?php echo esc_attr(stripslashes($user_email)); ?>" size="25" tabindex="20" /></label>
	</p>
	<p id="password">
		<label>密码<br />
		<input type="password" name="password" id="user_pass" class="input" value="<?php echo esc_attr(stripslashes($user_pass)); ?>" size="25" tabindex="20" /></label>
	</p>
	<br class="clear" />
	<input type="hidden" name="bind" id="bind" value="<?php echo $_POST['bind']; ?>" />
	<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="确定" tabindex="100" /></p>
</form>
<p id="nav">
<a href="javascript:;" onClick="show(this)"><?php echo ($_POST['bind'])?"没有帐号？注册帐号":"已有帐号？绑定我的帐号";?></a> |
<a href="http://www.smyx.net/" target="_blank" title="程序：WordPress连接微博">连接微博</a>
</p>
</div>
<script type="text/JavaScript">
function show(objN){var email = document.getElementById('email');var password = document.getElementById('password');var message = document.getElementById('message');var bind = document.getElementById('bind');if(email.style.display == "none"){email.style.display = "block";message.innerHTML = "<p class=\"message\">这是您第一次登录，请先完善注册信息。 <a href='save.php?do=login'>跳过这一步</a></p>";objN.innerText = "已有帐号？绑定我的帐号";bind.value = "";}else{email.style.display = "none";message.innerHTML = "<p class=\"message\">已有帐号？绑定我的帐号</p>";objN.innerText = '没有帐号？注册帐号';bind.value = "1";}}
</script>
<p id="backtoblog"><a href="<?php bloginfo('url'); ?>/" title="<?php esc_attr_e('Are you lost?') ?>"><?php printf(__('&larr; Back to %s'), get_bloginfo('title', 'display' )); ?></a></p>
</body>
</html>