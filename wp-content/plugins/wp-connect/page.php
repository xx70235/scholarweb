<?php
// 支持同步的平台名称
function wp_sync_list() {
	$weibo = array("sina" => "新浪微博",
		"qq" => "腾讯微博",
		"netease" => "网易微博",
		"sohu" => "搜狐微博",
		"renren" => "人人网",
		"kaixin001" => "开心网",
		//"digu" => "嘀咕",
		"douban" => "豆瓣",
		"tianya" => "天涯微博",
		"wbto" => "微博通",
		//"fanfou" => "饭否",
		//"renjian" => "人间网",
		//"zuosa" => "做啥",
		"twitter" => "Twitter");
	return $weibo;
} 
// 自定义页面同步操作
function wp_update_page() {
	$account = wp_option_account();
	$wptm_options = get_option('wptm_options');
	$wptm_advanced = get_option('wptm_advanced');
	$status = $text = mb_substr(trim(strip_tags($_POST['message'])), 0, 140, 'utf-8');
	$urls = trim(stripslashes($_POST['url']));
	$url = '';
	if (function_exists('wp_connect_advanced')) {
		include_once(WP_PLUGIN_DIR . '/wp-connect-advanced/page.php');
	} else {
		if (!empty($urls) && strpos($urls, 'http') === 0) {
			$url = array('image', $urls);
		} 
	} 
	require_once(dirname(__FILE__) . '/OAuth/OAuth.php');
	if (isset($_POST['sina']) && $account['sina']) {
		if ($account['sina']['mediaUserID']) {
			wp_update_share($account['sina']['mediaUserID'], $status, '', '', $url[1]);
		} else {
			$sina = wp_update_t_sina($account['sina'], $status, $url);
			if ($_POST['subject'] == 2 && $sina['original_pic']) {
				$url = array('image', $sina['original_pic']);
			} 
		} 
	} 
	$mediaUserID = '';
	if (isset($_POST['qq']) && $account['qq']) {
		if ($account['qq']['mediaUserID']) {
			$mediaUserID .= $account['qq']['mediaUserID'] . ',';
		} else {
			wp_update_t_qq($account['qq'], $text, $url);
		} 
	} 
	if (isset($_POST['netease']) && $account['netease']) {
		if ($account['netease']['mediaUserID']) {
			$mediaUserID .= $account['netease']['mediaUserID'] . ',';
		} else {
			wp_update_t_163($account['netease'], $status, $url);
		} 
	} 
	if (isset($_POST['sohu']) && $account['sohu']) {
		if ($account['sohu']['mediaUserID']) {
			$mediaUserID .= $account['sohu']['mediaUserID'] . ',';
		} else {
			wp_update_t_sohu($account['sohu'], $status, $url);
		} 
	} 
	if (isset($_POST['tianya']) && $account['tianya']) {
		if ($account['tianya']['mediaUserID']) {
			$mediaUserID .= $account['tianya']['mediaUserID'] . ',';
		} else {
			wp_update_tianya($account['tianya'], $status, $url);
		} 
	} 
	if (isset($_POST['renren']) && $account['renren']) {
		if ($account['renren']['mediaUserID']) {
			$mediaUserID .= $account['renren']['mediaUserID'] . ',';
		} else {
			wp_update_renren($account['renren'], $status);
		} 
	} 
	if ($mediaUserID) {
		wp_update_share(rtrim($mediaUserID, ','), $status, '', '', $url[1]);
	} 
	if (isset($_POST['douban']) && $account['douban']) {
		wp_update_douban($account['douban'], $status);
	} 
	if (isset($_POST['wbto']) && $account['wbto']) {
		wp_update_wbto($account['wbto'], $status, $url);
	} 
	if (isset($_POST['digu']) && $account['digu']) {
		wp_update_digu($account['digu'], $status);
	} 
	if (isset($_POST['fanfou']) && $account['fanfou']) {
		wp_update_fanfou($account['fanfou'], $status);
	} 
	if (isset($_POST['renjian']) && $account['renjian']) {
		wp_update_renjian($account['renjian'], $status, $url);
	} 
	if (isset($_POST['zuosa']) && $account['zuosa']) {
		wp_update_zuosa($account['zuosa'], $status);
	} 
	if (isset($_POST['kaixin001']) && $account['kaixin001']) {
		wp_update_kaixin001($account['kaixin001'], $status, $url);
	} 
	if (isset($_POST['twitter']) && $account['twitter']) {
		wp_update_twitter($account['twitter'], $status);
	} 
} 

function wp_connect_script_page () {
	wp_deregister_script('jquery');
	wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js', false, '1.4.2');
	wp_register_script('wp-connect-page', plugins_url('wp-connect/js/page.js'), array('jquery'), '0.1');
    wp_print_scripts('wp-connect-page');
}
add_action('wp_connect_action', 'wp_connect_script_page');

function wp_connect_action() {
	do_action('wp_connect_action');
} 
// 自定义页面 HTML
function wp_to_microblog() {
	global $plugin_url;
	$wptm_options = get_option('wptm_options');
	$wptm_advanced = get_option('wptm_advanced');
	if(!$wptm_options['disable_ajax']) {
		wp_connect_action();
	}
	$password = $_POST['password'];
	if (isset($_POST['message'])) {
		if (($wptm_options['page_password'] && $password == $wptm_options['page_password']) || (is_user_logged_in() && function_exists('wp_connect_advanced') && $wptm_advanced['registered_users'])) {
			wp_update_page();
		} else {
			$pwderror = ' style="display:inline;"';
		} 
	}
?>
<script type="text/javascript">
function textCounter(field,maxlimit){if(field.value.length>maxlimit){field.value=field.value.substring(0,maxlimit)}else{document.getElementById("wordage").childNodes[1].innerHTML=maxlimit-field.value.length}}
function selectall(form){for(var i=0;i<form.elements.length;i++){var box = form.elements[i];if (box.name != "chkall")box.checked = form.clickall.checked;}}
var wpurl = "<?php echo $wpurl;?>";
</script>
<link type="text/css" href="<?php echo $plugin_url;?>/css/page.css" rel="stylesheet" />
<form action="" method="post" id="tform">
  <fieldset>
    <div id="say">说说你的新鲜事
      <div id="wordage">你还可以输入 <span>140</span> 字</div>
    </div>
    <p id="v1"><textarea cols="60" rows="5" name="message" id="message" onblur="textCounter(this.form.message,140);" onKeyDown="textCounter(this.form.message,140);" onKeyUp="textCounter(this.form.message,140);"><?php echo ($pwderror)?$_POST['message']:'';?></textarea></p>
    图片地址：<p>
    <p id="v2"><input name="url" id="url" size="50" type="text" /></p>
    发布到：
    <p><label><input type="checkbox" id="clickall" onclick="selectall(this.form);" checked /> 全选</label>
<?php
$weibo_sync = wp_sync_list();
foreach($weibo_sync as $key => $name) {
	echo "<label><input name=\"$key\" id=\"$key\" type=\"checkbox\" value=\"1\" checked /> $name</label>\r\n";
}
?></p>
    <?php if (!is_user_logged_in() || !$wptm_advanced['registered_users']) {?>
    <p id="v3">密码：
    <input name="password" id="password" type="password" value="<?php echo (!$pwderror)?$_POST['password']:'';?>" /> <span<?php echo $pwderror;?>>密码错误！</span>
	</p>
	<?php } ?>
    <p><input type="submit" id="publish" value="发表" /></p>
    <p class="loading"><img src="<?php echo $plugin_url;?>/images/loading.gif" alt="Loading" /></p>
	<p class="error">你没有绑定帐号，请到我的资料页面或者到插件页面绑定！</p>
	<p class="success">发表成功！</p>
  </fieldset>
</form>
<?php
}
add_shortcode('wp_to_microblog', 'wp_to_microblog'); //简码
?>