<?php
/*
Plugin Name: WordPress连接微博
Author: 水脉烟香
Author URI: http://www.smyx.net/
Plugin URI: http://blogqun.com/wp-connect.html
Description: 支持使用20家合作网站帐号登录WordPress，同步文章、评论到微博/SNS，支持使用社会化评论。
Version: 2.5.5
*/

define('WP_CONNECT_VERSION', '2.5.5');
$wpurl = get_bloginfo('wpurl');
$siteurl = get_bloginfo('url');
$plugin_url = plugins_url('wp-connect');
$wptm_basic = get_option('wptm_basic'); // denglu
$wptm_options = get_option('wptm_options');
$wptm_connect = get_option('wptm_connect');
$wptm_comment = get_option('wptm_comment'); // denglu
$wptm_advanced = get_option('wptm_advanced');
$wptm_share = get_option('wptm_share');
$wptm_version = get_option('wptm_version');
$wptm_key = get_option('wptm_key');
$wp_connect_advanced_version = "1.7.3";

//update_option('wptm_basic', '');

if ($wptm_version && $wptm_version != WP_CONNECT_VERSION) {
	if (version_compare($wptm_version, '2.1', '<') && $wptm_basic) { // v2.0 bug
		function delete_2_0_bug() {
			global $wpdb;
			return $wpdb -> query("DELETE FROM $wpdb->usermeta WHERE meta_key = ''");
		} 
		delete_2_0_bug(); // wp 3.3
	} 
	if (version_compare($wptm_version, '2.4.1', '<')) { // 删除搜狐微博Consumer Key
		$keybug = 1;
	}
	if (version_compare($wptm_version, '2.5.2', '<'))
		update_option("wptm_tips", 1);
	update_option('wptm_version', WP_CONNECT_VERSION);
}

function wp_connect_set_session() {
	if (session_id() == "") {
		session_start();
	} 
}
function wp_connect_set_session_init() {
	if (!empty($_GET['token']) || !empty($_GET['user_id']) || defined( 'IS_PROFILE_PAGE' ) || $_GET['page'] == "wp-connect" || $_GET['action'] == "profile") {
		do_action('connect_init');
	} 
}
add_action('connect_init', 'wp_connect_set_session');
add_action('init', 'wp_connect_set_session_init');

add_action('admin_menu', 'wp_connect_add_page');

include_once(dirname(__FILE__) . '/functions.php');
include_once(dirname(__FILE__) . '/denglu.func.php'); //灯鹭自定义函数
include_once(dirname(__FILE__) . '/update.php');
include_once(dirname(__FILE__) . '/sync.php');
include_once(dirname(__FILE__) . '/connect.php');
include_once(dirname(__FILE__) . '/page.php');

if (!$wptm_key) {
	update_option('wptm_key', get_appkey());
} elseif ($keybug) { // 1.9.18/2.4.1
	if ($wptm_key[5][0]) {
		$wptm_key[5] = array(ifold($wptm_key[5][0], 'UfnmJanXwQZjD1TvZwTd', ''), ifold($wptm_key[5][1], 'Ur7MxoeTc7tegk11!1mTvHg-rp0yJdR5G8mZi7c2', ''));
		if (update_option('wptm_key', $wptm_key))
			update_option('wptm_sohu', '');
	} 
}

if ($wptm_connect['enable_connect'] && $wptm_connect['widget']) {
	include_once(dirname(__FILE__) . '/widget.php');
}

function wp_connect_add_page() {
	global $plugin_url, $wptm_basic, $wptm_comment;
	if ($wptm_basic['appid'] && $wptm_basic['appkey'] && current_user_can('manage_options')) {
		add_object_page('灯鹭评论管理', '灯鹭评论管理', 'moderate_comments', 'denglu_admin', 'denglu_ocomment5', $plugin_url.'/images/logo_small.gif');
	} 
	add_options_page('WordPress连接微博', 'WordPress连接微博', 'manage_options', 'wp-connect', 'wp_connect_do_page');
} 

function donate_version($version, $operator = '<') {
	if (function_exists('wp_connect_advanced') && version_compare(WP_CONNECT_ADVANCED_VERSION, $version, $operator)) {
		return true;
	}
}

function is_donate() { // 2.0
	if (function_exists('wp_connect_advanced')) { // denglu
		return true;
	}
}

function wp_connect_warning() {
	if ($_COOKIE['sina_access_token_expires']) {
		echo '<div class="updated"><p>';
		echo $_COOKIE['sina_access_token_expires'];
		echo '</p></div>';
	}
	if (current_user_can('manage_options')) {
		global $wp_version,$wp_connect_advanced_version,$wptm_basic, $wptm_options, $wptm_connect, $wptm_version;
		if (isset($_POST['closeTips'])) {
			update_option("wptm_tips", '');
		} 
		$wptm_tips = get_option("wptm_tips");
		if ($wptm_tips || version_compare($wp_version, '3.0', '<') || (donate_version($wp_connect_advanced_version) && WP_CONNECT_ADVANCED_VERSION != '1.4.3') || (($wptm_options || $wptm_connect) && (!$wptm_version || !$wptm_basic['denglu']) || !$wptm_basic)) {
			echo '<div class="updated">';
			if ($wptm_tips) {
				echo '<p><form method="post" action=""><strong>WordPress连接微博 更新说明</strong> <input type="submit" name="closeTips" value="关闭提示" /></form></p>';
				wp_connect_tips();
			}
			if (version_compare($wp_version, '3.0', '<')) {
				echo '<p><strong>您的WordPress版本太低，请升级到WordPress3.0或者更高版本，否则不能正常使用“WordPress连接微博”。</strong></p>';
			} 
			if (donate_version($wp_connect_advanced_version) && WP_CONNECT_ADVANCED_VERSION != '1.4.3') {
				echo "<p><strong>您的“WordPress连接微博 高级设置”(捐赠版)版本太低，请到QQ群内下载最新版，解压后用ftp工具上传升级！</strong></p>";
			} 
			if (($wptm_options || $wptm_connect) && !$wptm_version) {
				echo '<p><strong>重要更新：从1.7.3版本开始，加入对同步帐号密码的加密处理，非OAuth授权的网站，请重新填写帐号和密码！然后请点击一次“同步设置”下面的“保存更改”按钮关闭提示。<a href="options-general.php?page=wp-connect">现在去更改</a></strong></p>';
			}
			if (!$wptm_basic) {
				echo '<p><strong>您还没有对“WordPress连接微博”进行设置，<a href="options-general.php?page=wp-connect">现在去设置</a></strong></p>';
			} elseif (!$wptm_basic['denglu']) {
				echo '<p><strong>您需要到 WordPress连接微博 插件页面更新设置才能继续使用该插件，<a href="options-general.php?page=wp-connect">现在去更新</a></strong></p>';
			}
			echo '</div>';
		}
	}
}
add_action('admin_notices', 'wp_connect_warning'); 

function wp_connect_tips() { 
	global $plugin_url;
?>
<h2>Wordpress连接微博 专业版 v4.1 [<a href="http://blogqun.com/wp-connect.html#Changelog" target="_blank">更新日志</a>]</h2>
<p><strong>插件简介：</strong><br />
1. 使用微信、QQ、新浪微博等21个社交帐号登录您的网站。<br />
2. 同步文章、评论到13个微博/SNS。<br />
3. <a href="http://www.smyx.net/pinglun.html" target="_blank">本地化“社会化评论框”，包括微博评论回推到网站。</a><br />
4. <a href="http://www.smyx.net/wp-connect-data.html" target="_blank">社交数据统计（包括注册/登录/同步评论等），社交用户分析（社交网站/性别/地区分布等）</a><span style="color: #ff0000;">NEW!</span><br />
5. 同步全文或者部分内容到同步全文到新浪博客、网易博客、人人网、开心网、点点网、豆瓣日记、Tumblr、LOFTER（网易轻博客）、QQ空间、百度空间<br />
6. 使用社会化分享按钮。<br />
7. 写文章/发微博WAP页面（适用于手机浏览器）<br />
8. 隐藏文章的部分或者全部内容，用户通过登录、回复、分享等行为后才能显示隐藏的内容。<br />
9. 支持wp后台自动升级。<br />
10. <strong>最新插件：</strong><a href="http://blogqun.com/wechat.html" target="_blank">WordPress连接微信</a> <span style="color: #ff0000;">NEW!</span>，支持使用微信发布微博。
</p>
<p><strong>插件地址：</strong><a href="http://blogqun.com/wp-connect.html" target="_blank">http://blogqun.com/wp-connect.html</a></p>
<p><strong>您的网站直接对接新浪、腾讯等开放平台的接口，不经过任何第三方服务器，更加安全、稳定、高效。</strong></p>
<p><strong>加水脉烟香为微信好友，可以用微信搜索微信号: smyxapp 或者扫描下面的二维码。(发送h可以获得帮助)：</strong><br /><img src="http://ww1.sinaimg.cn/small/62579065jw1e09m23tqfxj.jpg" /></p>
<p><strong>关于插件：</strong><br />Wordpress连接微博 是由 <a href="http://www.smyx.net/" target="_blank">水脉烟香</a> 一人开发的Wordpress插件。插件于2011年1月20日发布第1版，目前包括免费版、专业版、基础版等。</strong></p>
<p><strong>插件截图：</strong><br /><a href="http://blogqun.com/demo/wp-connect.html" target="_blank" title="点击查看更多截图"><img src="<?php echo $plugin_url;?>/images/wp-connect-pro.gif" /></a></p>
<?php
} 

// 设置
function wp_connect_do_page() {
	global $wpurl,$plugin_url,$wptm_donate;
	wp_connect_update();
	$wptm_options = get_option('wptm_options');
	$wptm_connect = get_option('wptm_connect');
	$wptm_comment = get_option('wptm_comment');
	$wptm_key = get_option('wptm_key');
	$blog_token = get_option('blog_token');
    $qq = get_option('wptm_openqq');
    $sina = get_option('wptm_opensina');
	$wptm_basic = get_option('wptm_basic');
	$wptm_denglu = get_option('wptm_denglu');
	$version = this_version();
	// $version = 2;
	if (function_exists('wp_connect_advanced')) {
		wp_connect_advanced();
		$wptm_blog = get_option('wptm_blog');
		$blog_options = get_option('wptm_blog_options');
		$wptm_share = get_option('wptm_share');
		$wptm_advanced = get_option('wptm_advanced');
		if (function_exists('connect_has_donated')) { // 1.7.2
			if (!connect_has_donated($wptm_advanced)) {
				$keyerror = 1;
			}
		} elseif (WP_CONNECT_ADVANCED != "true") {
			$keyerror = 1;
		} 
		if ($keyerror) {
			$error = '<div id="wptm-tips"><p>请先在高级设置项填写正确授权码！</p></div>';
			if (donate_version('1.7.1', '>')) {
				echo '<div id="wptm-tips"><p><strong>更新提示：2012年11月27日更新了捐赠版授权码的算法，在这之前获得的授权码需要更新，请<a href="http://api.smyx.net/key/wp-connect.php" target="_blank">点击这里</a>。</strong></p></div>';
			}
		} else {
			if (donate_version('1.5.2')) {
				$donate_152 = '<div id="wptm-tips"><p>该捐赠版本不能使用该功能！</p></div>';
			}
		} 
	} else {
		$error = '<div id="wptm-tips"><p><a href="#blog" class="blog">同步博客</a>、<a href="#share" class="share">分享设置</a>、<a href="#advanced" class="advanced">高级设置</a>是<a href="http://blogqun.com/wp-connect.html" target="_blank">WordPress连接微博专业版</a>的独有功能。</p></div>';
	    $disabled = " disabled";
	}
	$account = wp_option_account();
	$_SESSION['user_id'] = '';
	$_SESSION['wp_url_bind'] = WP_CONNECT;
	$redirect_create = '?appid=3&redirect_create=' . urlencode($plugin_url.'/denglu.php');
	$connect_plugin = true; // bind.php
?>
<div class="wrap">
  <div id="icon-themes" class="icon32"><br /></div><h2>WordPress连接微博 v<?php echo WP_CONNECT_VERSION;?> <code><a href="http://blogqun.com/wp-connect.html" target="_blank">获取专业版</a></code> <span style="padding-left:10px"><iframe width="63" height="24" frameborder="0" allowtransparency="true" marginwidth="0" marginheight="0" scrolling="no" border="0" src="http://widget.weibo.com/relationship/followbutton.php?language=zh_cn&width=63&height=24&uid=1649905765&style=1&btn=red&dpc=1"></iframe></span></h2><div style="float:right;"><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZWMTWK2DGHCYS" target="_blank" title="PayPal"><img src="<?php echo $plugin_url;?>/images/donate_paypal.gif" /></a>/ <a href="http://blogqun.com/alipay/donate" target="_blank">支付宝</a></div>
  <div class="tabs">
    <ul class="nav">
      <li><a href="#basic" class="basic">基本设置</a></li>
      <li><a href="#sync" class="sync">同步微博</a></li>
	  <?php if ($version == 1) { ?>
	  <li><a href="#comment" class="comment">评论设置</a></li>
      <li><a href="#connect" class="connect">登录设置</a></li>
	  <li><a href="#open" class="open">开放平台</a></li>
	  <?php } if ($version == 1 || is_donate()) { ?>
	  <li><a href="#blog" class="blog">同步博客</a></li>
	  <?php } if (is_donate()) { ?>
      <li><a href="#share" class="share">分享设置</a></li>
      <li><a href="#advanced" class="advanced">高级设置</a></li>
	  <?php } ?>
      <li><a href="#check" class="check">环境检查</a></li>
	  <li><a href="#help" class="help">帮助文档</a></li>
    </ul>
    <div id="basic">
      <h3>设置向导</h3>
	  <?php
	  if ($version == 1) {
		  echo '<p>您已经成功安装了插件。';
		  if (!$wptm_basic['appid'] || !$wptm_basic['appkey']) {
			  echo '<span style="color:green;">请在 站点设置 中填写必需的 APP ID 和 APP Key</span>，您需要到 <a href="http://open.denglu.cc" target="_blank">灯鹭控制台</a> 获取并填写。';
		  } else {
			  echo '查看<a href="http://open.denglu.cc" target="_blank">灯鹭控制台</a>';
		  }
		  echo '</p>';
	  } elseif ($version == 3 || $version == 5) {
		  echo ($version == 3) ? '<p>您需要升级才能继续使用，' : '<p>这是您第一次使用，';?>
	  	  请先用以下社交帐号登录，完成与 <a href="http://open.denglu.cc/codes/getCodes.jsp?siteType=3" target="_blank">灯鹭控制台</a> 的连接。您也可以在下面的“站点设置”填写您在灯鹭控制台获取的APP ID和APP Key</p>
	      <p><a href="http://open.denglu.cc/transfer/qzone<?php echo $redirect_create;?>" title="使用QQ帐号登录"><img src="<?php echo $plugin_url;?>/images/qzone.png" /></a> <a href="http://open.denglu.cc/transfer/sina<?php echo $redirect_create;?>" title="使用新浪微博帐号登录"><img src="<?php echo $plugin_url;?>/images/sina.png" /></a> <a href="http://open.denglu.cc/transfer/tencent<?php echo $redirect_create;?>" title="使用腾讯微博帐号登录"><img src="<?php echo $plugin_url;?>/images/qq.png" /></a>  <a href="http://open.denglu.cc/transfer/renren<?php echo $redirect_create;?>" title="使用人人帐号登录"><img src="<?php echo $plugin_url;?>/images/renren.png" /></a> <a href="http://open.denglu.cc/transfer/douban<?php echo $redirect_create;?>" title="使用豆瓣帐号登录"><img src="<?php echo $plugin_url;?>/images/douban.png" /></a>  <a href="http://open.denglu.cc/transfer/baidu<?php echo $redirect_create;?>" title="使用百度帐号登录"><img src="<?php echo $plugin_url;?>/images/baidu.png" /></a>  <a href="http://open.denglu.cc/transfer/google<?php echo $redirect_create;?>" title="使用Google帐号登录"><img src="<?php echo $plugin_url;?>/images/google.png" /></a>  <a href="http://open.denglu.cc/transfer/twitter<?php echo $redirect_create;?>" title="使用Twitter帐号登录"><img src="<?php echo $plugin_url;?>/images/twitter.png" /></a>  <a href="http://open.denglu.cc/transfer/facebook<?php echo $redirect_create;?>" title="使用Facebook帐号登录"><img src="<?php echo $plugin_url;?>/images/facebook.png" /></a></p>
	  <?php
	  } elseif ($version == 4) {
		  echo '<p>您以前安装过 灯鹭 插件旧版，需要升级数据库才能兼容新版，请先点击下面的“升级数据库”按钮。</p>';
		  echo '<p><form method="post" action="options-general.php?page=wp-connect#basic"><span class="submit"><input type="submit" name="update_denglu" value="升级数据库" /></span></form></p>';
	  }
	  if ($version != 4) {
	  ?>
      <form method="post" action="options-general.php?page=wp-connect#basic">
        <?php wp_nonce_field('basic-options');?>
        <h3>站点设置</h3>
		<span style="color:green">请不要随意更改站点的APP ID，否则将会导致社会化评论出现异常，如：评论无法继续导入。</span>
	    <table class="form-table">
		    <tr>
			    <td width="25%" valign="top">APP ID: </td>
			    <td><label><input type="text" name="appid" size="32" value="<?php echo $wptm_basic['appid'];?>" /></label> (必填)</td>
		    </tr>
		    <tr>
			    <td width="25%" valign="top">APP Key: </td>
			    <td><label><input type="text" name="appkey" size="32" value="<?php echo $wptm_basic['appkey'];?>" /></label> (必填) (非常重要,不能泄漏)</td>
		    </tr>
        </table>
        <p class="submit">
		  <input type="hidden" name="denglu" value="1" />
          <input type="submit" name="basic_options" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
      </form>
	  <?php }
	  if ($version == 1 || $version == 2) { ?>
	  <h3>其他登录插件</h3>
	  <p style="color:green">假如你以前使用过其他类似的登录插件（<a href="http://wiki.smyx.net/wordpress/plugins" target="_blank">查看列表</a>），可以点击以下按钮进行数据转换，以便旧用户能使用本插件正常登录。</p>
      <form method="post" action="options-general.php?page=wp-connect#basic">
	    <?php wp_nonce_field('other-plugins');?>
		<span class="submit"><input type="submit" name="other_plugins" value="其他登录插件数据转换" /> (可能需要一些时间，请耐心等待！)</span>
	  </form>
	  <h3>卸载插件</h3>
      <form method="post" action="">
	    <?php wp_nonce_field('wptm-delete');?>
		<span class="submit"><input type="submit" name="wptm_delete" value="卸载 WordPress连接微博" onclick="return confirm('您确定要卸载WordPress连接微博？')" /></span>
	  </form>
	  <?php } ?>
      <div id="wptm-tips">
	    <p><strong>友情提示</strong></p>
        <p>若在使用时出现“时间戳有误”，请先点击“环境检查”查看服务器时间，跟北京时间对比下，然后在“同步微博”下面的“服务器时间校正”填写时间差！</p>
	    <p style="color:#880"><strong>从WordPress连接微博 插件旧版升级到V2.0 <a href="http://bbs.denglu.cc/thread-9056-1-1.html" target="_blank">注意事项</a></strong></p>
	    <p>新浪微博、淘宝网回调地址：<code><?php echo $plugin_url.'/dl_receiver.php';?></code></p>
	  </div>
    </div>
    <div id="sync">
      <form method="post" action="options-general.php?page=wp-connect">
        <?php wp_nonce_field('sync-options');?>
        <h3>同步微博</h3>
        <table class="form-table">
          <tr>
            <td width="25%" valign="top"><strong>基础设置</strong></td>
          </tr>
          <tr>
            <td width="25%" valign="top">功能开启</td>
            <td><label><input name="enable_wptm" type="checkbox" value="1" <?php if($wptm_options['enable_wptm']) echo "checked "; ?>> 开启“文章同步到微博”功能</label></td>
          </tr>
          <tr>
            <th>同步内容设置</th>
            <td><input name="sync_option" type="text" size="1" maxlength="1" value="<?php echo (!$wptm_options) ? '2' : $wptm_options['sync_option']; ?>" onkeyup="value=value.replace(/[^1-6]/g,'')" /> (填数字，留空为不同步，只对本页绑定的帐号有效！)<br />提示: 1. 标题+链接 2. 标题+摘要/内容+链接 3.文章摘要/内容 4. 文章摘要/内容+链接 5. 标题 + 内容 <br /> 自定义标题格式：<input name="format" type="text" size="25" value="<?php echo $wptm_options['format']; ?>" /> ( 标题: <code>%title%</code>，会继承上面的设置，可留空。 )<br />把以下内容当成微博话题 (<label><input name="enable_cats" type="checkbox" value="1" <?php if($wptm_options['enable_cats']) echo "checked "; ?>>文章分类</label> <label><input name="enable_tags" type="checkbox" value="1" <?php if($wptm_options['enable_tags']) echo "checked "; ?>>文章标签</label>) <label><input name="disable_pic" type="checkbox" value="1" <?php checked($wptm_options['disable_pic']); ?>>不同步图片</label> <label><input name="thumbnail" type="checkbox" value="1" <?php checked($wptm_options['thumbnail']); ?>/>优先同步特色图像</label></td>
          </tr>
		  <tr>
			<td width="25%" valign="top">选择同步接口</td>
			<td><label><input type="checkbox" name="denglu_bind" value="1" <?php checked(!$wptm_options || $wptm_options['denglu_bind']); ?>/> 使用灯鹭开放平台提供的同步接口</label><br /><span style="color:green;">勾选后，记得在下面重新绑定帐号，您发布的文章同步后在微博有评论时会被抓起回来（使用<a href="#comment" class="comment">社会化评论</a>时）</span></td>
		  </tr>
          <tr>
            <td width="25%" valign="top"><strong>可选设置</strong></td>
          </tr>
          <tr>
            <td width="25%" valign="top">多作者博客</td>
            <td><label><input name="multiple_authors" type="checkbox" value="1" <?php if($wptm_options['multiple_authors']) echo "checked "; ?>> 让每个作者发布的文章同步到他们各自绑定的微博上，可以通知他们在 <a href="<?php echo admin_url('profile.php');?>">我的资料</a> 里面设置。</label></td>
          </tr>
          <tr>
            <th>自定义消息</th>
            <td>新文章前缀: <input name="new_prefix" type="text" size="10" value="<?php echo $wptm_options['new_prefix']; ?>" /> 更新文章前缀: <input name="update_prefix" type="text" size="10" value="<?php echo $wptm_options['update_prefix']; ?>" /> 更新间隔: <input name="update_days" type="text" size="2" maxlength="4" value="<?php echo ($wptm_options['update_days']) ? $wptm_options['update_days'] : '0'; ?>" onkeyup="value=value.replace(/[^\d]/g,'')" /> 天 [0=修改文章时不同步] </td>
          </tr>
          <tr>
            <td width="25%" valign="top">禁止同步的文章分类ID (<a href="http://www.denglu.cc/source/wordpress_faqs.html#cat-ids" target="_blank">数字ID</a>)</td>
            <td><input name="cat_ids" type="text" value="<?php echo $wptm_options['cat_ids']; ?>" /> 用英文逗号(,)分开 (设置后该ID分类下的文章将不会同到微博)</td>
          </tr>
          <tr>
            <td width="25%" valign="top">禁止同步的自定义文章类型</td>
            <td><input name="post_types" type="text" size="30" value="<?php echo $wptm_options['post_types']; ?>" /> 用英文逗号(,)分开 ( 例如post_type=xxx ,请填写xxx )</td>
          </tr>
          <tr>
            <td width="25%" valign="top">自定义页面(一键发布到微博)</td>
            <td>自定义密码: <input name="page_password" type="password" value="<?php echo $wptm_options['page_password']; ?>" autocomplete="off" />
               [ <a href="http://www.denglu.cc/source/wordpress_faqs.html#page" target="_blank">如何使用？</a> ] <label><input name="disable_ajax" type="checkbox" value="1" <?php if($wptm_options['disable_ajax']) echo "checked "; ?>>禁用AJAX无刷新提交</label></td>
          </tr>
          <tr>
            <td width="25%" valign="top">自定义短网址</td>
            <td><label><input name="enable_shorten" type="checkbox"  value="1" <?php checked($wptm_options['enable_shorten']); ?>> 博客默认 ( http://yourblog.com/?p=1 )</label> <label><strong>短网址</strong> <select name="t_cn"><option value="">选择</option><option value="1"<?php selected($wptm_options['t_cn'] == "1");?>>t.cn (新浪)</option><option value="2"<?php selected($wptm_options['t_cn'] == "2");?>>dwz.cn (百度)</option></select></label></td>
          </tr>
          <tr>
            <td width="25%" valign="top">Twitter是否使用代理？</td>
            <td><label title="国外主机用户不要勾选噢！"><input name="enable_proxy" type="checkbox" value="1" <?php if($wptm_options['enable_proxy']) echo "checked "; ?>> (选填) 国内主机用户必须勾选才能使用Twitter</label> [ <a href="http://www.smyx.net/apps/oauth.php" target="_blank">去获取授权码</a> ]</td>
          </tr>
          <tr>
            <td width="25%" valign="top">服务器时间校正</td>
            <td>假如在使用 腾讯微博 时出现 “没有oauth_token或oauth_token不合法，请返回重试！” 才需要填写。请点击上面的“环境检查”，里面有一个当前服务器时间，跟你电脑(北京时间)比对一下，看相差几分钟！[ <a href="http://www.denglu.cc/source/wordpress_faqs.html#phptime" target="_blank">查看详细</a> ] <br />( 比北京时间 <select name="char"><option value="-1"<?php selected($wptm_options['char'] == "-1");?>>快了</option><option value="1"<?php selected($wptm_options['char'] == "1");?> >慢了</option></select> <input name="minutes" type="text" size="2" value="<?php echo $wptm_options['minutes'];?>" onkeyup="value=value.replace(/[^\d]/g,'')" /> 分钟 )</td>
          </tr>
        </table>
        <p class="submit">
          <input type="submit" name="update_options" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
      </form>
      <?php include( dirname(__FILE__) . '/bind.php' );?>
    </div>
    <div id="connect">
      <form method="post" action="options-general.php?page=wp-connect#connect">
        <?php wp_nonce_field('connect-options');?>
        <h3>登录设置</h3>
        <table class="form-table">
          <tr>
            <td width="25%" valign="top"><strong>基础设置</strong></td>
          </tr>
          <tr>
            <td width="25%" valign="top">功能开启</td>
            <td><label><input name="enable_connect" type="checkbox" value="1" <?php if($wptm_connect['enable_connect']) echo "checked "; ?>> 开启“社会化登录”功能</label></td>
          </tr>
          <tr>
            <td width="25%" valign="top">显示设置</td>
            <td><label><input name="manual" type="radio" value="2" <?php checked(!$wptm_connect['manual'] || $wptm_connect['manual'] == 2); ?>>评论框处(默认)</label> <label><input name="manual" type="radio" value="1" <?php checked($wptm_connect['manual'] == 1);?>>调用函数</label> ( <code>&lt;?php wp_connect();?&gt;</code> ) [ <a href="http://www.denglu.cc/source/wordpress_faqs.html#connect-manual" target="_blank">详细说明</a> ]</td>
          </tr>
          <tr>
            <td width="25%" valign="top">添加按钮</td>
            <td><label><input name="qqlogin" type="checkbox" value="qzone" <?php checked(!$wptm_connect || $wptm_connect['qqlogin']);?>/> QQ</label>
			  <label><input name="sina" type="checkbox" value="sina" <?php checked(!$wptm_connect || $wptm_connect['sina']);?>/> 新浪微博</label>
              <label><input name="qq" type="checkbox" value="tencent" <?php checked(!$wptm_connect || $wptm_connect['qq']);?> /> 腾讯微博</label>
              <label><input name="renren" type="checkbox" value="renren" <?php checked(!$wptm_connect || $wptm_connect['renren']);?> /> 人人网</label>
              <label><input name="kaixin001" type="checkbox" value="kaixin001" <?php checked(!$wptm_connect || $wptm_connect['kaixin001']);?>/> 开心网</label>
              <label><input name="douban" type="checkbox" value="douban" <?php checked(!$wptm_connect || $wptm_connect['douban']);?> /> 豆瓣</label><br />
			  <label><input name="taobao" type="checkbox" value="taobao" <?php checked(!$wptm_connect || $wptm_connect['taobao']);?> /> 淘宝网</label>
			  <label><input name="alipay" type="checkbox" value="alipayquick" <?php checked(!$wptm_connect || $wptm_connect['alipay']);?>/> 支付宝</label>
			  <label><input name="baidu" type="checkbox" value="baidu" <?php checked(!$wptm_connect || $wptm_connect['baidu']);?>/> 百度</label>
              <label><input name="sohu" type="checkbox" value="sohu" <?php checked(!$wptm_connect || $wptm_connect['sohu']);?> /> 搜狐微博</label>
              <label><input name="netease" type="checkbox" value="netease" <?php checked(!$wptm_connect || $wptm_connect['netease']);?> /> 网易微博</label>
              <label><input name="tianya" type="checkbox" value="tianya" <?php checked(!$wptm_connect || $wptm_connect['tianya']); ?>/> 天涯</label>
			  <label><input name="guard360" type="checkbox" value="guard360" <?php checked($wptm_connect['guard360']);?>/> 360</label>
			  <label><input name="tianyi" type="checkbox" value="tianyi" <?php checked($wptm_connect['tianyi']);?>/> 天翼</label><br />
			  <label><input name="msn" type="checkbox" value="windowslive" <?php checked(!$wptm_connect || $wptm_connect['msn']);?>/> MSN</label>
			  <label><input name="google" type="checkbox" value="google" <?php checked(!$wptm_connect || $wptm_connect['google']);?>/> 谷歌</label>
			  <label><input name="yahoo" type="checkbox" value="yahoo" <?php checked($wptm_connect['yahoo']);?>/> 雅虎</label>
			  <label><input name="twitter" type="checkbox" value="twitter" <?php checked(!$wptm_connect || $wptm_connect['twitter']);?>/> Twitter</label>
			  <label><input name="facebook" type="checkbox" value="facebook" <?php checked(!$wptm_connect || $wptm_connect['facebook']);?>/> Facebook</label>
			  <label><input name="netease163" type="checkbox" value="netease163" <?php checked($wptm_connect['netease163']);?>/> 网易通行证</label>
			  <br /><span style="color:green;">假如要排序，请到<a href="http://open.denglu.cc" target="_blank">灯鹭控制台</a>设置，设置后请在这个页面点击“保存更改”按钮，仅对选择默认风格(本地化) 有效。</span>
            </td>
          </tr>
          <tr>
            <td width="25%" valign="top"><strong>可选设置</strong></td>
          </tr>
		  <tr>
			<td width="25%" valign="top">注册信息</td>
			<td><label><input type="checkbox" name="reg" value="1" <?php if($wptm_connect['reg']) echo "checked "; ?>/> 用户首次登录时，强制要求用户填写个人信息</label></td>
		  </tr>
          <tr>
            <td width="25%" valign="top">登录样式</td>
            <td><label><input name="style" type="radio" value="1" <?php checked(!$wptm_connect['style'] || $wptm_connect['style'] == 1 || $wptm_connect['style'] == 2);?> />默认风格(本地化) </label><br /><label><input name="style" type="radio" value="4" <?php checked($wptm_connect['style'] == 4);?> />自定义样式 (请在下面粘帖从 <a href="http://open.denglu.cc" target="_blank">灯鹭控制台</a> 获取的js代码)</label><br /><textarea name="custom_style" cols="80" rows="4"><?php echo stripslashes($wptm_connect['custom_style']);?></textarea>
            </td>
          </tr>
		  <tr>
			<td width="25%" valign="top">小工具</td>
			<td><label><input type="checkbox" name="widget" value="1" <?php if(!$wptm_connect || $wptm_connect['widget']) echo "checked "; ?>/> 开启边栏登录按钮 (开启后到<a href="widgets.php">小工具</a>拖拽激活)</label></td>
		  </tr>
		  <tr>
			<td width="25%" valign="top">禁止头像</td>
			<td><label><input type="checkbox" name="head" value="1" <?php if($wptm_connect['head']) echo "checked "; ?>/> 不使用登录者的微博/社区头像作为她的头像</label></td>
		  </tr>
		  <?php if (is_donate()) { ?>
		  <tr>
			<td width="25%" valign="top">绑定登录帐号</td>
			<td><label><input type="checkbox" name="denglu_bind" value="1" <?php if($wptm_connect['denglu_bind']) echo "checked "; ?>/> 在<a href="<?php echo admin_url('profile.php');?>">个人资料</a>页面使用灯鹭的绑定登录帐号功能</label> ( 开启后，无法使用 高级设置版本的“<a href="http://www.smyx.net/wiki/wordpress/comment" target="_blank">高级评论功能</a>” )</td>
		  </tr>
		  <?php } ?>
          <tr>
            <td width="25%" valign="top">@微博帐号</td>
            <td>新浪微博昵称: <input name="sina_username" type="text" size="10" value='<?php echo $wptm_connect['sina_username'];?>' /> 腾讯微博帐号: <input name="qq_username" type="text" size="10" value='<?php echo $wptm_connect['qq_username'];?>' /><br />搜狐微博昵称: <input name="sohu_username" type="text" size="10" value='<?php echo $wptm_connect['sohu_username'];?>' /> 网易微博昵称: <input name="netease_username" type="text" size="10" value='<?php echo $wptm_connect['netease_username'];?>' /><br />(说明：有新的评论时将以 @微博帐号 的形式显示在您跟评论者相对应的微博上，仅对方勾选了同步评论到微博时才有效！注：腾讯微博帐号不是QQ号码)</td>
          </tr>
		  <tr>
			<td width="25%" valign="top">中文用户名</td>
			<td><label><input type="checkbox" name="chinese_username" value="1" <?php if(default_values('chinese_username', 1, $wptm_connect)) echo "checked "; ?>/> 支持中文用户名</label></td>
		  </tr>
          <tr>
            <td width="25%" valign="top">禁止注册的用户名</td>
            <td><input name="disable_username" type="text" size="60" value='<?php echo $wptm_connect['disable_username'];?>' /> 用英文逗号(,)分开</td>
          </tr>
        </table>
        <p class="submit">
          <input type="submit" name="wptm_connect" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
      </form>
      <div id="wptm-tips">
	    <p><strong>友情提示</strong></p>
        <p>若在使用时出现“时间戳有误”，请先点击“环境检查”查看服务器时间，跟北京时间对比下，然后在“同步微博”下面的“服务器时间校正”填写时间差！</p>
	    <p style="color:#880"><strong>从WordPress连接微博 插件旧版升级到V2.0 <a href="http://bbs.denglu.cc/thread-9056-1-1.html" target="_blank">注意事项</a></strong></p>
	    <p>淘宝网回调地址：<code><?php echo $plugin_url.'/dl_receiver.php';?></code></p>
	  </div>
    </div>
    <div id="comment">
      <form method="post" action="options-general.php?page=wp-connect#comment">
        <?php wp_nonce_field('comment-options');?>
        <h3>评论设置</h3>
	    <table class="form-table">
            <tr>
                <td width="25%" valign="top">功能开启</td>
                <td><label><input name="enable_comment" type="checkbox" value="1" <?php if($wptm_comment['enable_comment']) echo "checked "; ?> /> 开启“社会化评论”功能</label> <a href="http://www.denglu.cc/demo.html" target="_blank">查看演示</a></td>
            </tr>
		    <tr>
			    <td width="25%" valign="top">自定义函数</td>
			    <td><label><input name="manual" type="checkbox" value="1" <?php if($wptm_comment['manual']) echo "checked "; ?> /> 自己在主题添加函数（不推荐使用）</label><code>&lt;?php dengluComments();?&gt;</code></td>
		    </tr>
		    <tr>
			    <td width="25%" valign="top">单篇文章评论开关</td>
			    <td><label><input name="comments_open" type="checkbox" value="1" <?php if(default_values('comments_open', 1, $wptm_comment)) echo "checked ";?> /> 继承WordPress已有的评论开关，即当某篇文章关闭评论时，也不使用社会化评论功能。</label></td>
		    </tr>
		    <tr>
			    <td width="25%" valign="top">同步评论到本地</td>
			    <td><label><input name="dcToLocal" type="checkbox" value="1" <?php if(default_values('dcToLocal', 1, $wptm_comment)) echo "checked ";?> /> 灯鹭评论内容保存一份在WordPress本地评论数据库</label> <label>(每 <input name="time" type="text" size="1" maxlength="3" value="<?php echo ($wptm_comment['time']) ? $wptm_comment['time'] : '5'; ?>" onkeyup="value=value.replace(/[^0-9]/g,'')" /> 分钟更新一次)</label></td>
		    </tr>
		    <tr>
			    <td width="25%" valign="top">保存评论者头像到本地</td>
			    <td><label><input name="comment_avatar" type="checkbox" value="<?php echo (!$wptm_comment['comment_avatar']) ? 1 : 2; ?>"<?php if($wptm_comment['comment_avatar']) echo "checked "; ?> /> 会创建一个新的数据库表(wp_comments_avatar)来保存</label></td>
		    </tr>
		    <tr>
			    <td width="25%" valign="top">最新评论</td>
			    <td><label><input name="latest_comments" type="checkbox" value="1" <?php if($wptm_comment['latest_comments']) echo "checked "; ?> /> 开启侧边栏“最新评论”功能 (开启后到<a href="widgets.php">小工具</a>拖拽激活)</label></td>
		    </tr>
		    <tr>
			    <td width="25%" valign="top">SEO支持</td>
			    <td><label><input name="enable_seo" type="checkbox" value="1" <?php if($wptm_comment['enable_seo']) echo "checked "; ?> /> 评论支持SEO，让搜索引擎能爬到评论数据</label></td>
		    </tr>
        </table>
        <p class="submit">
          <input type="submit" name="comment_options" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
      </form>
	  <h3>导入导出</h3>
	  <p>导入数据到灯鹭控制台。导入后，您原有的网站评论将在“灯鹭社会化评论”的评论框内显示。</p>
	  <p><span class="submit"><input type="button" id="exportComments" value="评论导入" /></span> <span id="exportStatus">(可能需要一些时间，请耐心等待！)</span></p>
	  <!--
	  <p><form method="post" action="options-general.php?page=wp-connect#comment"><span class="submit"><input type="submit" name="importComment" value="评论导入" /> (可能需要一些时间，请耐心等待！)</span></form></p>
	  -->
      <div id="wptm-tips">
	    <p><strong>使用说明</strong></p>
        <p>使用前，请先在<a href="http://open.denglu.cc" target="_blank">灯鹭控制台</a>注册帐号，并创建站点，之后在插件的<a href="#basic" class="basic">基本设置</a>页面填写APP ID 和 APP Key .</p>
		<p><strong>评论的相关设置及管理，请打开<a href="<?php echo admin_url('admin.php?page=denglu_admin');?>" target="_blank">灯鹭评论管理</a>操作。</strong></p>
		<p>如果您只是需要单一的社会化评论功能，请直接下载 <a href="http://wordpress.org/extend/plugins/denglu/" target="_blank">Denglu评论</a> 插件 （直接在后台搜索插件 denglu 安装即可。）</p>
	  </div>
    </div>
    <div id="open">
      <form method="post" action="options-general.php?page=wp-connect#open">
        <?php wp_nonce_field('openkey-options');?>
		<h3>开放平台</h3>
		<div id="wptm-tips">
           <p>请在下面填写开放平台的key，填写后，同步时可以显示来源，即显示微博的“来自XXX”。<span style="color: red;">加***号的为使用时必填！</span></p>
		   <p>请同时到<a href="http://open.denglu.cc" target="_blank">灯鹭控制台</a>的 配置平台供应商 填写您申请的app key。</p>
	    </div>
		<p><strong>QQ登录</strong> ( APP ID: <input name="qq1" type="text" value='<?php echo $wptm_key[13][0];?>' /> APP Key: <input name="qq2" type="text" value='<?php echo $wptm_key[13][1];?>' /> [ <a href="http://wiki.smyx.net/wordpress/faqs/qq" target="_blank">如何获取?</a> ] ) ***</p>
		<p><strong>新浪微博</strong> ( App Key: <input name="sina1" type="text" value='<?php echo $sina['app_key'];?>' /> App Secret: <input name="sina2" type="text" value='<?php echo $sina['secret'];?>' /> [ <a href="http://open.weibo.com/connect" target="_blank">如何获取?</a> ] )</p>
		<p><strong>腾讯微博</strong> ( App Key: <input name="tqq1" type="text" value='<?php echo $qq['app_key'];?>' /> App Secret: <input name="tqq2" type="text" value='<?php echo $qq['secret'];?>' /> [ <a href="http://dev.t.qq.com/developer/web/" target="_blank">如何获取?</a> ] )</p>
		<p><strong>搜狐微博</strong> ( Consumer Key: <input name="sohu1" type="text" value='<?php echo $wptm_key[5][0];?>' /> Consumer secret: <input name="sohu2" type="text" value='<?php echo $wptm_key[5][1];?>' /> [ <a href="http://open.t.sohu.com/" target="_blank">如何获取?</a> ] ) ***</p>
		<p><strong>豆瓣</strong> ( API Key: <input name="douban1" type="text" value='<?php echo $wptm_key[9][0];?>' /> 私钥: <input name="douban2" type="text" value='<?php echo $wptm_key[9][1];?>' /> [ <a href="http://qgc.qq.com/17556663/t/10" target="_blank">如何获取?</a> ] )</p>
		<p><strong>天涯微博</strong> ( App Key: <input name="tianya1" type="text" value='<?php echo $wptm_key[17][0];?>' /> App Secret: <input name="tianya2" type="text" value='<?php echo $wptm_key[17][1];?>' /> [ <a href="http://developer.denglu.cc/title=%E5%A4%A9%E6%B6%AF%E7%A4%BE%E5%8C%BA%E7%94%B3%E8%AF%B7%E6%B5%81%E7%A8%8B" target="_blank">如何获取?</a> ] )</p>
		<p><strong>人人网</strong> ( API Key: <input name="renren1" type="text" value='<?php echo $wptm_key[7][0];?>' /> Secret Key: <input name="renren2" type="text" value='<?php echo $wptm_key[7][1];?>' /> [ <a href="http://wiki.smyx.net/wordpress/faqs/renren" target="_blank">如何获取?</a> ] ) ***</p>
		<p><strong>开心网</strong> ( API Key: <input name="kaixin1" type="text" value='<?php echo $wptm_key[8][0];?>' /> Secret Key: <input name="kaixin2" type="text" value='<?php echo $wptm_key[8][1];?>' /> [ <a href="http://wiki.open.kaixin001.com/index.php?id=%E5%BC%80%E5%BF%83%E8%BF%9E%E6%8E%A5" target="_blank">如何获取?</a> ] ) ***</p>
		<p><strong>淘宝网</strong> ( App Key: <input name="taobao1" type="text" value='<?php echo $wptm_key[16][0];?>' /> App Secret: <input name="taobao2" type="text" value='<?php echo $wptm_key[16][1];?>' /> [ <a href="http://open.taobao.com/doc/detail.htm?spm=0.0.0.179.d7fwt4&id=1028" target="_blank">如何获取?</a> ] ) ***</p>
		<p><strong>百度</strong> ( API Key: <input name="baidu1" type="text" value='<?php echo $wptm_key[19][0];?>' /> Secret Key: <input name="baidu2" type="text" value='<?php echo $wptm_key[19][1];?>' /> [ <a href="http://developer.denglu.cc/index.php?title=%E7%99%BE%E5%BA%A6%E7%94%B3%E8%AF%B7%E6%B5%81%E7%A8%8B" target="_blank">如何获取?</a> ] ) ***</p>
		<p><strong>MSN</strong> ( Client ID: <input name="msn1" type="text" value='<?php echo $wptm_key[2][0];?>' /> Client secret: <input name="msn2" type="text" value='<?php echo $wptm_key[2][1];?>' /> [ <a href="http://developer.denglu.cc/index.php?title=MSN%E7%94%B3%E8%AF%B7%E6%B5%81%E7%A8%8B" target="_blank">如何获取?</a> ] ) ***</p>
		<p><strong>Twitter</strong> ( App Key: <input name="twitter1" type="text" value='<?php echo $wptm_key[28][0];?>' /> App Secret: <input name="twitter2" type="text" value='<?php echo $wptm_key[28][1];?>' /> [ <a href="http://developer.denglu.cc/title=Twitter%E7%94%B3%E8%AF%B7%E6%B5%81%E7%A8%8B" target="_blank">如何获取?</a> ] )</p>
        <p class="submit">
          <input type="submit" name="wptm_key" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
      </form>
	</div>
    <div id="blog">
      <form method="post" action="options-general.php?page=wp-connect#blog">
        <?php wp_nonce_field('blog-options');?>
        <h3>同步博客</h3>
		<?php echo $error.$donate_152;?>
		<p>( 友情提醒：同时开启同步微博和同步博客会导致发布文章缓慢或者响应超时！)</p>
	    <table class="form-table">
            <tr>
                <td width="25%" valign="top">开启“同步博客”功能</td>
                <td><input name="enable_blog" type="checkbox" value="1" <?php if($blog_options[0]) echo "checked "; ?>></td>
            </tr>
		    <tr>
			    <td width="25%" valign="top">添加文章版权信息</td>
			    <td><input type="checkbox" name="copyright" value="1" <?php if($blog_options[1]) echo "checked "; ?>/></td>
		    </tr>
		    <tr>
			    <td width="25%" valign="top">允许同步的用户ID（开启多作者博客时生效）</td>
			    <td><label><input type="text" name="user_ids" value="<?php echo $blog_options[2];?>" /> 用英文逗号(,)分开，包括在高级设置填写的默认用户ID</label></td>
		    </tr>
		    <tr>
			    <td width="25%" valign="top">绑定帐号 (开放平台接口)</td>
			    <td>
				<?php 
	            if ($blog_token['qq']) {$b1 = "del"; $b2 = '(已绑定)';} else {$b1 = "bind"; $b2 = '';}
                if ($blog_token['renren']) {$b3 = "del"; $b4 = '(已绑定)';} else {$b3 = "bind"; $b4 = '';}
                if ($blog_token['kaixin']) {$b5 = "del"; $b6 = '(已绑定)';} else {$b5 = "bind"; $b6 = '';}?>
				<a href="<?php echo $plugin_url;?>-advanced/blogbind.php?<?php echo $b1;?>=qzone&from=blog">QQ空间<?php echo $b2;?></a> 、 <a href="<?php echo $plugin_url;?>-advanced/blogbind.php?<?php echo $b3;?>=renren&from=blog">人人网<?php echo $b4;?></a> 、 <a href="<?php echo $plugin_url;?>-advanced/blogbind.php?<?php echo $b5;?>=kaixin&from=blog">开心网<?php echo $b6;?></a> (使用前，请先到 <a href="#open" class="open">开放平台</a> 页面填写申请的key)</td>
		    </tr>
		    <tr>
			    <td width="25%" valign="top">新浪博客</td>
			    <td><label>邮 箱: <input type="text" name="user_sina" value="<?php echo $wptm_blog[0][1];?>" /></label> <label>密 码: <input type="password" name="pass_sina" autocomplete="off" /></label><?php if($wptm_blog[0][2]) echo ' (密码留空表示不修改)';?></td>
		    </tr>
		    <tr>
			    <td width="25%" valign="top">网易博客</td>
			    <td><label>邮 箱: <input type="text" name="user_163" value="<?php echo $wptm_blog[1][1];?>" /></label> <label>密 码: <input type="password" name="pass_163" autocomplete="off" /></label><?php if($wptm_blog[1][2]) echo ' (密码留空表示不修改)';?></td>
		    </tr>
		    <tr>
			    <td width="25%" valign="top">QQ空间 (邮箱接口，建议使用开放平台接口)</td>
			    <td><label>Q Q: <input type="text" name="user_qzone" value="<?php echo $wptm_blog[2][1];?>" /></label> <label>密 码: <input type="password" name="pass_qzone" autocomplete="off" /></label><?php if($wptm_blog[2][2]) echo ' (密码留空表示不修改)';?></td>
		    </tr>
        </table>
        <p class="submit">
          <input type="submit" name="blog_options" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
      </form>
      <form method="post" action="options-general.php?page=wp-connect#blog">
	    <p>如果你觉得QQ空间同步需要申请APP key比较麻烦，您可以使用邮箱接口，点击下面按钮进行检测。</p>
        <p><?php if (isset($_POST['verify_qzone'])) verify_qzone();?></p>
		<p class="submit"><input type="submit" name="verify_qzone" value="检查是否支持同步到QQ空间(邮箱接口)" /></p>
	  </form>
      <div id="wptm-tips">
	    <p><strong>注意事项</strong></p>
        <p>1、新浪博客、网易博客修改文章时会同步修改对应的博客文章，而不是创建新的博客文章。<br />2、QQ空间、人人网、开心网只会同步一次，下次修改文章时不会再同步。<br />3、快速编辑和密码保护的文章不会同步或更新。<br />4、当开启多作者博客时，在这里填写的“允许同步的用户ID”和在“高级设置”填写的“默认用户ID”对应的WP帐号发布文章时才会同步到博客。<br />5、有效期：人人网和开心网1个月，QQ空间3个月，发现不能同步时请重新绑定帐号。<br />6、使用QQ空间开放平台接口同步时，请确保已经激活 <code>add_one_blog</code>，否则请解除绑定！<br /><strong>7、绑定人人网、开心网帐号时，也会绑定“同步微博”下人人网、开心网的新鲜事/状态同步。你可以根据情况删除其中的一个。</strong></p>
	  </div>
    </div>
    <div id="share">
      <form method="post" id="formdrag" action="options-general.php?page=wp-connect#share">
        <?php wp_nonce_field('share-options');?>
        <h3>分享设置</h3>
		<?php echo $error;?>
        <table class="form-table">
          <tr>
            <td width="25%" valign="top">添加按钮</td>
            <td><label><input name="enable_share" type="radio" value="4" <?php checked($wptm_share['enable_share'] == 4); ?>> 不使用</label> <label><input name="enable_share" type="radio" value="3" <?php checked($wptm_share['enable_share'] == 3); ?>> 文章前面</label> <label><input name="enable_share" type="radio" value="1" <?php checked(!$wptm_share['enable_share'] || $wptm_share['enable_share'] == 1); ?>> 文章末尾</label> <label><input name="enable_share" type="radio" value="2" <?php checked($wptm_share['enable_share'] == 2); ?>> 调用函数</label> ( <code>&lt;?php wp_social_share();?&gt;</code> ) [ <a href="http://www.smyx.net/wiki/wordpress/share" target="_blank">详细说明</a> ]</td>
          </tr>
          <tr>
            <td width="25%" valign="top">样式选择</td>
            <td><label title="假如没有复制到主题样式中，请务必勾选！"><input name="css" type="checkbox" value="1" <?php checked(!$wptm_share || $wptm_share['css']); ?> /> 使用插件自带share.css文件 (建议复制样式到主题css文件中，以免升级时被覆盖！)</label>
            </td>
          </tr>
          <tr>
            <td width="25%" valign="top">显示设置</td>
            <td><label>分享按钮前面的文字: <input name="text" type="text" value="<?php echo (!$wptm_share) ? '分享到：' : $wptm_share['text'];?>" /></label><br /><label><input name="button" type="radio" value="1" <?php checked(!$wptm_share['button'] || $wptm_share['button'] == 1); ?> />显示图标按钮</label> ( 选择尺寸 <select name="size"><option value="16"<?php if($wptm_share['size'] == 16) echo " selected";?>>小图标</option><option value="32"<?php if($wptm_share['size'] == 32) echo " selected";?> >大图标</option></select> ) <label><input name="button" type="radio" value="2" <?php if($wptm_share['button'] == 2) echo "checked "; ?> />显示图文按钮</label> <label><input name="button" type="radio" value="3" <?php if($wptm_share['button'] == 3) echo "checked "; ?> />显示文字按钮</label></td>
          </tr>
		  <tr>
			<td width="25%" valign="top">Google Analytics</td>
			<td><label><input type="checkbox" name="analytics" value="1" <?php if($wptm_share['analytics']) echo "checked "; ?>/> 使用 Google Analytics 跟踪社会化分享按钮的使用效果</label> [ <a href="http://www.smyx.net/wiki/wordpress/share#ga" target="_blank">查看说明</a> ]<br /><label>配置文件ID: <input type="text" name="id" value="<?php echo $wptm_share['id'];?>" /></label></td>
		  </tr>
		  <?php if(!$donate_152) { ?>
		  <tr>
			<td width="25%" valign="top">选择文本分享</td>
			<td><label><input type="checkbox" name="selection" value="1" <?php if($wptm_share['selection']) echo "checked "; ?>/> <strong>在文章页面选中任何一段文本可以点击按钮分享到QQ空间、新浪微博、腾讯微博。</strong></label></td>
		  </tr>
		  <?php } ?>
        </table>
        <h3>Google+1</h3>
        <table class="form-table">
          <tr>
            <td width="25%" valign="top">开启“Google+1”功能</td>
            <td><input name="enable_plusone" type="checkbox" value="1" <?php checked($wptm_share['enable_plusone']); ?>> (提示: Google+1在国内使用不稳定，如果发现网站打开速度变慢，请关闭该功能。)</td>
          </tr>
          <tr>
            <td width="25%" valign="top">添加按钮</td>
            <td><label><input name="plusone" type="radio" value="1" <?php checked($wptm_share['plusone'] == 1); ?>>文章前面</label> <label><input name="plusone" type="radio" value="2" <?php checked(!$wptm_share['plusone'] || $wptm_share['plusone'] == 2); ?>>文章末尾</label> <label><input name="plusone" type="radio" value="3" <?php checked($wptm_share['plusone'] == 3); ?>> 调用函数</label> ( <code>&lt;?php wp_google_plusone();?&gt;</code> )</td>
          </tr>
          <tr>
            <td width="25%" valign="top">显示设置</td>
            <td><label>添加到 <select name="plusone_add"><option value="1"<?php selected($wptm_share['plusone_add'] == 1);?>>所有页面</option><option value="2"<?php selected($wptm_share['plusone_add'] == 2);?>>首页</option><option value="3"<?php selected($wptm_share['plusone_add'] == 3);?> >文章页和页面</option><option value="4"<?php selected(!$wptm_share['plusone_add'] || $wptm_share['plusone_add'] == 4);?> >文章页</option><option value="5"<?php selected($wptm_share['plusone_add'] == 5);?> >页面</option></select></label> <label>选择尺寸 <select name="plusone_size"><option value="small"<?php selected($wptm_share['plusone_size'] == 'small');?>>小（15 像素）</option><option value="medium"<?php selected($wptm_share['plusone_size'] == 'medium');?> >中（20 像素）</option><option value="standard"<?php selected(!$wptm_share['plusone_size'] || $wptm_share['plusone_size'] == 'standard');?> >标准（24 像素）</option><option value="tall"<?php selected($wptm_share['plusone_size'] == 'tall');?> >高（60 像素）</option></select><label> <input name="plusone_count" type="checkbox" value="1" <?php checked($wptm_share['plusone_count']); ?> />包含计数</label></td>
          </tr>
        </table>
        <h3>添加社会化分享按钮，可以上下左右拖拽排序(记得保存！) <span style="color:#440">[如果不能拖拽请刷新页面]</span>：</h3>
		  <ul id="dragbox">
		  <?php if (WP_CONNECT_ADVANCED == "true") {wp_social_share_options();} else {$social = wp_social_share_title();foreach($social as $key => $title) {echo "<li id=\"drag\"><input name=\"$key\" type=\"checkbox\" value=\"$key\" />$title</li>";}}?>
		    <div class="clear"></div>
		  </ul>
		  <div id="dragmarker">
		    <img src="<?php echo $plugin_url;?>/images/marker_top.gif">
		    <img src="<?php echo $plugin_url;?>/images/marker_middle.gif" id="dragmarkerline">
		    <img src="<?php echo $plugin_url;?>/images/marker_bottom.gif">
		  </div>
        <p class="submit">
          <input type="hidden" name="select">
          <input type="submit" name="share_options" onclick="saveData()" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
      </form>
    </div>
    <div id="advanced">
      <form method="post" action="options-general.php?page=wp-connect#advanced">
        <?php wp_nonce_field('advanced-options');?>
        <h3>高级设置</h3>
	    <table class="form-table">
		    <tr>
			    <td width="25%" valign="top">授权码</td>
			    <td><label>API Key: <input type="text" name="apikey" value="<?php echo $wptm_advanced['apikey'];?>" /></label> <label>Secret Key: <input type="text" name="secret" size="32" value="<?php echo $wptm_advanced['secret'];?>" /></label></td>
		    </tr>
		    <tr>
			    <td width="25%" valign="top">Google Talk</td>
			    <td><input name="gtalk" type="text" size="32" value="<?php echo $wptm_advanced['gtalk'];?>" /> (必填)</td>
		    </tr>
		    <tr>
			    <td width="25%" valign="top">默认用户ID</td>
			    <td><label><input name="user_id" type="text" size="2" maxlength="4" value="<?php echo $wptm_advanced['user_id'];?>" onkeyup="value=value.replace(/[^\d]/g,'')" /> 这是为Google Talk发布文章设置的</label> ( 提示: 当前登录的用户ID是<?php echo get_current_user_id();?> )</td>
		    </tr>
		    <tr>
			    <td width="25%" valign="top">自定义页面</td>
			    <td><label><input type="checkbox" name="registered_users" id="registered_users" value="1" <?php if($wptm_advanced['registered_users']) echo "checked "; ?>/> 支持所有注册用户 (用户登陆后可以在自定义页面发布信息到他们绑定的微博上。)</label></td>
		    </tr>
		    <tr>
			    <td width="25%" valign="top">微博秀</td>
			    <td><label><input type="checkbox" name="widget" value="1" <?php if($wptm_advanced['widget']) echo "checked "; ?>/> 开启侧边栏微博秀 (开启后到<a href="widgets.php">小工具</a>拖拽激活)</label> [ <a href="http://ishow.sinaapp.com/" target="_blank">获得代码</a> ]</td>
		    </tr>
        </table>
        <p class="submit">
          <input type="submit" name="advanced_options" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
		<div id="wptm-tips"><p>提示：高级设置版本已经停止更新，请看[ <a href="http://blogqun.com/wp-connect.html" target="_blank">WordPress连接微博专业版</a> ]</p></div>
      </form>
    </div>
    <div id="check">
	<p><iframe width="100%" height="680" frameborder="0" scrolling="no" src="<?php echo $plugin_url.'/check.php'?>"></iframe></p>
    </div>
    <div id="help">
	  <div id="wptm-tips">
	  <p><strong><a href="http://www.denglu.cc/source/wordpress2.0.html" target="_blank">点击查看 WordPress连接微博 v<?php echo WP_CONNECT_VERSION;?> 官方帮助文档</a></strong></p>
	  <?php wp_connect_tips();?>
	  </div>
    </div>
  </div>
</div>
<?php
}

/*
function wp_connect_plugin_row_meta( $links, $file ) {
	if( $file == plugin_basename( __FILE__ ) ) {
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZWMTWK2DGHCYS" target="_blank">PayPal</a>';
		$links[] = '<a href="https://me.alipay.com/smyx" target="_blank">支付宝</a>';
        $links[] = '<a href="http://www.smyx.net/wiki/" target="_blank">V1帮助</a>';
        $links[] = '<a href="http://www.denglu.cc/source/wordpress2.0.html" target="_blank">V2帮助</a>';
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'wp_connect_plugin_row_meta', 10, 2 );
*/
