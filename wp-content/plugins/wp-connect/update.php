<?php
/**
 * 后台头部
 */
add_action('admin_init', 'wp_connect_header');
function wp_connect_header() {
	global $wpdb, $plugin_url, $wptm_options;
	if ($wptm_options['denglu_bind']) {
		if (isset($_POST['add_qq'])) { //使用灯鹭的同步接口
			header('Location:' . $plugin_url . '/login.php?go=tencent');
		} 
		if (isset($_POST['add_sina'])) {
			header('Location:' . $plugin_url . '/login.php?go=sina');
		} 
		if (isset($_POST['add_sohu'])) {
			header('Location:' . $plugin_url . '/login.php?go=sohu');
		} 
		if (isset($_POST['add_netease'])) {
			header('Location:' . $plugin_url . '/login.php?go=netease');
		} 
		if (isset($_POST['add_tianya'])) {
			header('Location:' . $plugin_url . '/login.php?go=tianya');
		} 
		if (isset($_POST['add_renren'])) {
			header('Location:' . $plugin_url . '/login.php?go=renren');
		} 
	} else {
		if (isset($_POST['add_qq'])) {
			header('Location:' . $plugin_url . '/go.php?bind=qq');
		} 
		if (isset($_POST['add_sina'])) {
			header('Location:' . $plugin_url . '/go.php?bind=sina');
		} 
		if (isset($_POST['add_sohu'])) {
			header('Location:' . $plugin_url . '/go.php?bind=sohu');
		} 
		if (isset($_POST['add_netease'])) {
			header('Location:' . $plugin_url . '/go.php?bind=netease');
		} 
		if (isset($_POST['add_tianya'])) {
			header('Location:' . $plugin_url . '/go.php?bind=tianya');
		} 
		if (isset($_POST['add_renren'])) {
			header('Location:' . $plugin_url . '-advanced/blogbind.php?bind=renren');
		} 
	} 
	if (isset($_POST['add_shuoshuo'])) {
		header('Location:' . $plugin_url . '-advanced/blogbind.php?bind=qzone');
	} 
	if (isset($_POST['add_kaixin'])) {
		header('Location:' . $plugin_url . '-advanced/blogbind.php?bind=kaixin');
	} 
	if (isset($_POST['add_douban'])) {
		header('Location:' . $plugin_url . '/go.php?bind=douban');
	} 
	if (isset($_POST['add_twitter'])) {
		header('Location:' . $plugin_url . '/go.php?bind=twitter');
	} 
	// 删除数据库+停用插件
	if (isset($_POST['wptm_delete'])) {
		$wpdb -> query("DELETE FROM $wpdb->options WHERE option_name like '%wptm_%'");
		if (function_exists('wp_nonce_url')) {
			$deactivate_url = 'plugins.php?action=deactivate&plugin=wp-connect/wp-connect.php';
			$deactivate_url = str_replace('&amp;', '&', wp_nonce_url($deactivate_url, 'deactivate-plugin_wp-connect/wp-connect.php'));
			header('Location:' . $deactivate_url);
		} 
	} 
} 

/**
 * 插件页面
 */
// 读取数据库
function wp_option_account() {
	$account = array('qq' => get_option('wptm_qq'),
		'sina' => get_option('wptm_sina'),
		'sohu' => get_option('wptm_sohu'),
		'netease' => get_option('wptm_netease'),
		'twitter' => get_option('wptm_twitter'),
		'shuoshuo' => get_option('wptm_shuoshuo'),
		'renren' => get_option('wptm_renren'),
		'kaixin001' => get_option('wptm_kaixin001'),
		'digu' => get_option('wptm_digu'),
		'douban' => get_option('wptm_douban'),
		'tianya' => get_option('wptm_tianya'),
		'renjian' => get_option('wptm_renjian'),
		'fanfou' => get_option('wptm_fanfou'),
		'zuosa' => get_option('wptm_zuosa'),
		'wbto' => get_option('wptm_wbto'));
	return array_filter($account);
}
// 保存设置
function wp_connect_update() {
	$updated = '<div class="updated"><p><strong>' . __('Settings saved.') . '</strong></p></div>'; 
	// 同步微博设置
	if (isset($_POST['update_options'])) {
		$update_days = (trim($_POST['update_days'])) ? trim($_POST['update_days']) : '0';
		$update_options = array('enable_wptm' => trim($_POST['enable_wptm']),
			'enable_proxy' => trim($_POST['enable_proxy']),
			'bind' => trim($_POST['bind']),
			'denglu_bind' => trim($_POST['denglu_bind']),
			'sync_option' => trim($_POST['sync_option']),
			'format' => $_POST['format'],
			'enable_cats' => trim($_POST['enable_cats']),
			'enable_tags' => trim($_POST['enable_tags']),
			'disable_pic' => trim($_POST['disable_pic']),
            'thumbnail' => trim($_POST['thumbnail']),
			//'sina_v2' => trim($_POST['sina_v2']),
			'new_prefix' => trim($_POST['new_prefix']),
			'update_prefix' => trim($_POST['update_prefix']),
			'update_days' => $update_days,
			'cat_ids' => trim($_POST['cat_ids']),
			'post_types' => trim($_POST['post_types']),
			'page_password' => trim($_POST['page_password']),
			'disable_ajax' => trim($_POST['disable_ajax']),
			'multiple_authors' => trim($_POST['multiple_authors']),
			'enable_shorten' => trim($_POST['enable_shorten']),
			't_cn' => trim($_POST['t_cn']),
			'char' => trim($_POST['char']),
			'minutes' => trim($_POST['minutes'])
			);
		update_option("wptm_options", $update_options);
		update_option('wptm_version', WP_CONNECT_VERSION);
		echo $updated;
	} 
	// 登录设置
	if (isset($_POST['wptm_connect'])) {
		$disable_username = (trim($_POST['disable_username'])) ? trim($_POST['disable_username']) : 'admin';
		$wptm_connect = array('enable_connect' => trim($_POST['enable_connect']),
			'manual' => trim($_POST['manual']),
			'reg' => trim($_POST['reg']),
			'qqlogin' => trim($_POST['qqlogin']),
			'sina' => trim($_POST['sina']),
			'qq' => trim($_POST['qq']),
			'sohu' => trim($_POST['sohu']),
			'netease' => trim($_POST['netease']),
			'renren' => trim($_POST['renren']),
			'kaixin001' => trim($_POST['kaixin001']),
			'douban' => trim($_POST['douban']),
			'taobao' => trim($_POST['taobao']),
			'baidu' => trim($_POST['baidu']),
			'tianya' => trim($_POST['tianya']),
			'msn' => trim($_POST['msn']),
			'google' => trim($_POST['google']),
			'yahoo' => trim($_POST['yahoo']),
			'alipay' => trim($_POST['alipay']),
			'twitter' => trim($_POST['twitter']),
			'facebook' => trim($_POST['facebook']),
			'guard360' => trim($_POST['guard360']),
			'tianyi' => trim($_POST['tianyi']),
			'netease163' => trim($_POST['netease163']),
			'style' => trim($_POST['style']),
			'custom_style' => trim($_POST['custom_style']),
			'denglu_bind' => trim($_POST['denglu_bind']),
			'sina_username' => trim($_POST['sina_username']),
			'qq_username' => trim($_POST['qq_username']),
			'sohu_username' => trim($_POST['sohu_username']),
			'netease_username' => trim($_POST['netease_username']),
			'head' => trim($_POST['head']),
			'widget' => trim($_POST['widget']),
            'chinese_username' => trim($_POST['chinese_username']),
			'disable_username' => $disable_username
			);
		update_option("wptm_connect", $wptm_connect);
		update_option('wptm_version', WP_CONNECT_VERSION);
		save_user_denglu_platform();
		echo $updated;
	}
	// 开放平台
	if (isset($_POST['wptm_key'])) {
		$keys = array('2' => array(trim($_POST['msn1']), trim($_POST['msn2'])),
			'5' => array(trim($_POST['sohu1']), trim($_POST['sohu2'])),
			'6' => array(trim($_POST['netease1']), trim($_POST['netease2'])),
			'7' => array(trim($_POST['renren1']), trim($_POST['renren2'])),
			'8' => array(trim($_POST['kaixin1']), trim($_POST['kaixin2'])),
			'9' => array(trim($_POST['douban1']), trim($_POST['douban2'])),
			'13' => array(trim($_POST['qq1']), trim($_POST['qq2'])),
			'16' => array(trim($_POST['taobao1']), trim($_POST['taobao2'])),
			'17' => array(trim($_POST['tianya1']), trim($_POST['tianya2'])),
			'19' => array(trim($_POST['baidu1']), trim($_POST['baidu2'])),
			'28' => array(trim($_POST['twitter1']), trim($_POST['twitter2']))
			);
		update_option("wptm_key", $keys);
		update_option("wptm_opensina", array('app_key' => trim($_POST['sina1']), 'secret' => trim($_POST['sina2'])));
		update_option("wptm_openqq", array('app_key' => trim($_POST['tqq1']), 'secret' => trim($_POST['tqq2'])));
		echo $updated;
	} 
	// 其他登录插件数据转换
	if (isset($_POST['other_plugins'])) {
		include_once(dirname(__FILE__) . '/other_plugins.php');
		all_import_user();
		echo '<div class="updated"><p><strong>数据转换成功！</strong></p></div>';
		return;
	} 
	$update = array('username' => trim($_POST['username']),
		'password' => key_encode(trim($_POST['password']))
		);
	$token = array('oauth_token' => trim($_POST['username']),
		'oauth_token_secret' => trim($_POST['password'])
		);
	if (isset($_POST['update_twitter'])) {
		update_option("wptm_twitter", $token);
		echo $updated;
	} 
	if (isset($_POST['update_qq'])) {
		update_option("wptm_qq", $token);
		echo $updated;
	} 
	if (isset($_POST['update_sina'])) {
		update_option("wptm_sina", $token);
		echo $updated;
	} 
	if (isset($_POST['update_sohu'])) {
		update_option("wptm_sohu", $token);
		echo $updated;
	} 
	if (isset($_POST['update_netease'])) {
		update_option("wptm_netease", $token);
		echo $updated;
	} 
	if (isset($_POST['update_douban'])) {
		update_option("wptm_douban", $token);
		echo $updated;
	} 
	if (isset($_POST['update_tianya'])) {
		update_option("wptm_tianya", $token);
		echo $updated;
	} 
	if (isset($_POST['update_renren'])) {
		update_option("wptm_renren", $update);
		echo $updated;
	} 
	// if (isset($_POST['update_kaixin'])) {
	// update_option("wptm_kaixin001", $update);
	// echo $updated;
	// }
	if (isset($_POST['update_digu'])) {
		update_option("wptm_digu", $update);
		echo $updated;
	} 
	if (isset($_POST['update_fanfou'])) {
		update_option("wptm_fanfou", $update);
		echo $updated;
	} 
	if (isset($_POST['update_renjian'])) {
		update_option("wptm_renjian", $update);
		echo $updated;
	} 
	if (isset($_POST['update_zuosa'])) {
		update_option("wptm_zuosa", $update);
		echo $updated;
	} 
	if (isset($_POST['update_wbto'])) {
		update_option("wptm_wbto", $update);
		echo $updated;
	} 
	// delete
	if (isset($_POST['delete_twitter'])) {
		update_option("wptm_twitter", '');
	} 
	if (isset($_POST['delete_qq'])) {
		update_option("wptm_qq", '');
	} 
	if (isset($_POST['delete_sina'])) {
		update_option("wptm_sina", '');
	} 
	if (isset($_POST['delete_sohu'])) {
		update_option("wptm_sohu", '');
	} 
	if (isset($_POST['delete_netease'])) {
		update_option("wptm_netease", '');
	} 
	if (isset($_POST['delete_douban'])) {
		update_option("wptm_douban", '');
	} 
	if (isset($_POST['delete_tianya'])) {
		update_option("wptm_tianya", '');
	} 
	if (isset($_POST['delete_shuoshuo'])) {
		update_option("wptm_shuoshuo", '');
	} 
	if (isset($_POST['delete_renren'])) {
		update_option("wptm_renren", '');
	} 
	if (isset($_POST['delete_kaixin'])) {
		update_option("wptm_kaixin001", '');
	} 
	if (isset($_POST['delete_digu'])) {
		update_option("wptm_digu", '');
	} 
	if (isset($_POST['delete_fanfou'])) {
		update_option("wptm_fanfou", '');
	} 
	if (isset($_POST['delete_renjian'])) {
		update_option("wptm_renjian", '');
	} 
	if (isset($_POST['delete_zuosa'])) {
		update_option("wptm_zuosa", '');
	} 
	if (isset($_POST['delete_wbto'])) {
		update_option("wptm_wbto", '');
	} 
	// 钩子，方便自定义插件
	do_action('save_connent_options');
} 
define("WP_DONTPEEP" , 'Yp64QLB0Ho8ymIRs');

/**
 * 我的资料
 */
// 读取数据库
function wp_usermeta_account($uid) {
	$user = get_userdata($uid);
	$account = array('qq' => $user -> wptm_qq,
		'sina' => $user -> wptm_sina,
		'sohu' => $user -> wptm_sohu,
		'netease' => $user -> wptm_netease,
		'twitter' => $user -> wptm_twitter,
		'shuoshuo' => $user -> wptm_shuoshuo,
		'renren' => $user -> wptm_renren,
		'kaixin001' => $user -> wptm_kaixin001,
		'digu' => $user -> wptm_digu,
		'douban' => $user -> wptm_douban,
		'tianya' => $user -> wptm_tianya,
		'renjian' => $user -> wptm_renjian,
		'fanfou' => $user -> wptm_fanfou,
		'zuosa' => $user -> wptm_zuosa,
		'wbto' => $user -> wptm_wbto);
	return array_filter($account);
} 
// 保存设置
function wp_user_profile_update($user_id) {
	$update = array('username' => trim($_POST['username']),
		'password' => key_encode(trim($_POST['password']))
		);
	$token = array('oauth_token' => trim($_POST['username']),
		'oauth_token_secret' => trim($_POST['password'])
		);
	if (isset($_POST['update_twitter'])) {
		update_usermeta($user_id, "wptm_twitter", $token);
	} 
	if (isset($_POST['update_qq'])) {
		update_usermeta($user_id, "wptm_qq", $token);
	} 
	if (isset($_POST['update_sina'])) {
		update_usermeta($user_id, "wptm_sina", $token);
	} 
	if (isset($_POST['update_sohu'])) {
		update_usermeta($user_id, "wptm_sohu", $token);
	} 
	if (isset($_POST['update_netease'])) {
		update_usermeta($user_id, "wptm_netease", $token);
	} 
	if (isset($_POST['update_douban'])) {
		update_usermeta($user_id, "wptm_douban", $token);
	} 
	if (isset($_POST['update_tianya'])) {
		update_usermeta($user_id, "wptm_tianya", $token);
	} 
	if (isset($_POST['update_renren'])) {
		update_usermeta($user_id, 'wptm_renren', $update);
	} 
	// if (isset($_POST['update_kaixin'])) {
	// update_usermeta( $user_id, 'wptm_kaixin001', $update);
	// }
	if (isset($_POST['update_digu'])) {
		update_usermeta($user_id, 'wptm_digu', $update);
	} 
	if (isset($_POST['update_renjian'])) {
		update_usermeta($user_id, 'wptm_renjian', $update);
	} 
	if (isset($_POST['update_fanfou'])) {
		update_usermeta($user_id, 'wptm_fanfou', $update);
	} 
	if (isset($_POST['update_zuosa'])) {
		update_usermeta($user_id, 'wptm_zuosa', $update);
	} 
	if (isset($_POST['update_wbto'])) {
		update_usermeta($user_id, 'wptm_wbto', $update);
	} 
	// delete
	if (isset($_POST['delete_twitter'])) {
		update_usermeta($user_id, 'wptm_twitter', '');
	} 
	if (isset($_POST['delete_qq'])) {
		update_usermeta($user_id, 'wptm_qq', '');
	} 
	if (isset($_POST['delete_sina'])) {
		update_usermeta($user_id, 'wptm_sina', '');
	} 
	if (isset($_POST['delete_sohu'])) {
		update_usermeta($user_id, 'wptm_sohu', '');
	} 
	if (isset($_POST['delete_netease'])) {
		update_usermeta($user_id, 'wptm_netease', '');
	} 
	if (isset($_POST['delete_douban'])) {
		update_usermeta($user_id, 'wptm_douban', '');
	} 
	if (isset($_POST['delete_tianya'])) {
		update_usermeta($user_id, 'wptm_tianya', '');
	} 
	if (isset($_POST['delete_shuoshuo'])) {
		update_usermeta($user_id, 'wptm_shuoshuo', '');
	} 
	if (isset($_POST['delete_renren'])) {
		update_usermeta($user_id, 'wptm_renren', '');
	} 
	if (isset($_POST['delete_kaixin'])) {
		update_usermeta($user_id, 'wptm_kaixin001', '');
	} 
	if (isset($_POST['delete_digu'])) {
		update_usermeta($user_id, 'wptm_digu', '');
	} 
	if (isset($_POST['delete_renjian'])) {
		update_usermeta($user_id, 'wptm_renjian', '');
	} 
	if (isset($_POST['delete_fanfou'])) {
		update_usermeta($user_id, 'wptm_fanfou', '');
	} 
	if (isset($_POST['delete_zuosa'])) {
		update_usermeta($user_id, 'wptm_zuosa', '');
	} 
	if (isset($_POST['delete_wbto'])) {
		update_usermeta($user_id, 'wptm_wbto', '');
	} 
} 
// 同步设置
if ( $wptm_options['enable_wptm'] && ($wptm_options['multiple_authors'] || (function_exists('wp_connect_advanced') && $wptm_advanced['registered_users'])) ) {
	add_action('show_user_profile', 'wp_user_profile_fields', 12);
	add_action('edit_user_profile', 'wp_user_profile_fields', 12);
	add_action('personal_options_update', 'wp_save_user_profile_fields', 12);
	add_action('edit_user_profile_update', 'wp_save_user_profile_fields', 12);
} 

function wp_save_user_profile_fields($user_id) {
	if (!current_user_can('edit_user', $user_id)) {
		return false;
	} 
	$update_days = (trim($_POST['update_days'])) ? trim($_POST['update_days']) : '0';
	$wptm_profile = array('sync_option' => trim($_POST['sync_option']),
		'new_prefix' => trim($_POST['new_prefix']),
		'update_prefix' => trim($_POST['update_prefix']),
		'update_days' => $update_days
		);
	update_usermeta($user_id, 'wptm_profile', $wptm_profile);
} 

function wp_user_profile_fields( $user ) {
	global $plugin_url, $user_level, $wptm_options, $wptm_advanced;
	$user_id = $user->ID;
	wp_user_profile_update($user_id);
	$account = wp_usermeta_account($user_id);
	$wptm_profile = get_user_meta($user_id, 'wptm_profile', true);
	$_SESSION['user_id'] = $user_id;
	$_SESSION['wp_url_bind'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	if ($wptm_options['multiple_authors'] && ($user_level > 1 || is_super_admin())) { //是否开启多作者和判断用户等级
		$canbind = true;
?>
<h3>同步设置</h3>
<table class="form-table">
<tr>
	<th>同步内容设置</th>
	<td><input name="sync_option" type="text" size="1" maxlength="1" value="<?php echo $wptm_profile['sync_option']; ?>" onkeyup="value=value.replace(/[^1-6]/g,'')" /> (填数字，留空为不同步) <br />提示：1. 前缀+标题+链接 2. 前缀+标题+摘要/内容+链接 3.文章摘要/内容 4. 文章摘要/内容+链接
	</td>
</tr>
<tr>
	<th>自定义消息</th>
	<td>新文章前缀：<input name="new_prefix" type="text" size="10" value="<?php echo $wptm_profile['new_prefix']; ?>" /> 更新文章前缀：<input name="update_prefix" type="text" size="10" value="<?php echo $wptm_profile['update_prefix']; ?>" /> 更新间隔：<input name="update_days" type="text" size="2" maxlength="4" value="<?php echo $wptm_profile['update_days']; ?>" onkeyup="value=value.replace(/[^\d]/g,'')" /> 天 [0=修改文章时不同步]
	</td>
</tr>
</table>
<?php
	}
    if ( $canbind || $wptm_advanced['registered_users'] ) {
?>
<p class="show_botton"></p>
</form>
</div>
<?php echo $super_admin;include( dirname(__FILE__) . '/bind.php' );?>
<div class="hide_botton">
<?php } 
}