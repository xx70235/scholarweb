<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && 'bind.php' == basename($_SERVER['SCRIPT_FILENAME']))
die ('Please do not load this page directly. Thanks!');
$wptm_options = get_option('wptm_options');
$wptm_advanced = get_option('wptm_advanced');
$action = IS_PROFILE_PAGE && $user_id ? $plugin_url.'/save.php?do=profile' : '';
?>
<link rel="stylesheet" type="text/css" href="<?php echo $plugin_url;?>/css/style.css" />
<script type="text/javascript" src="<?php echo $plugin_url;?>/js/jquery-1.2.6.pack.js"></script>
<script type="text/javascript" src="<?php echo $plugin_url;?>/js/floatdialog.js"></script>
<h3>帐号绑定</h3>
<p>请点击下面图标绑定账号: (友情提醒：绑定太多帐号会导致发布文章时缓慢或者响应超时！)</p>
<?php if (!$wptm_options['bind'] && $connect_plugin) {
	$oproviderKey = ($wptm_options['denglu_bind']) ? '<a href="http://open.denglu.cc/" target="_blank">灯鹭控制台</a> 的 “配置平台供应商”' : '<a href="#open" class="open">开放平台</a>';
	echo '<p style="color:green;">如果同步时要显示微博的“来自XXX”，请到 '.$oproviderKey.' 页面填写申请的key，更换APP key后，相应的帐号请重新绑定！</p>';
}?>
<div id="tlist">
<a href="javascript:;" id="<?php echo ($account['twitter']['oauth_token']) ? 'bind_twitter' : 'twitter';?>" class="twitter" title="Twitter"><b></b></a>
<a href="javascript:;" id="<?php echo ($account['qq']) ? 'bind_qq' : 'qq';?>" class="qq" title="腾讯微博"><b></b></a>
<a href="javascript:;" id="<?php echo ($account['sina']) ? 'bind_sina' : 'sina';?>" class="sina" title="新浪微博"><b></b></a>
<a href="javascript:;" id="<?php echo ($account['netease']) ? 'bind_netease' : 'netease';?>" class="netease" title="网易微博"><b></b></a>
<a href="javascript:;" id="<?php echo ($account['sohu']) ? 'bind_sohu' : 'sohu';?>" class="sohu" title="搜狐微博"><b></b></a>
<a href="javascript:;" id="<?php echo ($account['renren']) ? 'bind_renren' : 'renren';?>" class="renren" title="人人网"><b></b></a>
<?php if (is_donate()) { ?>
<a href="javascript:;" id="<?php echo ($account['kaixin001']['session_key']) ? 'bind_kaixin' : 'kaixin';?>" class="kaixin" title="开心网"><b></b></a>
<?php } ?>
<a href="javascript:;" id="<?php echo ($account['tianya']) ? 'bind_tianya' : 'tianya';?>" class="tianya" title="天涯微博"><b></b></a>
<a href="javascript:;" id="<?php echo ($account['douban']['oauth_token']) ? 'bind_douban' : 'douban';?>" class="douban" title="豆瓣"><b></b></a>
<a href="javascript:;" id="wbto" class="wbto<?php echo ($account['wbto']['password']) ? ' bind': '';?>" title="微博通wbto.cn"><b></b></a>
</div>
<?php
if ($wptm_options['multiple_authors'] || (function_exists('wp_connect_advanced') && $wptm_advanced['registered_users'])) {
	if ($connect_plugin) {
		if ($wptm_options['multiple_authors']) {
		    echo '<p>您已经开启了多作者博客，假如管理员只想同步自己发布的文章，请到 <a href="' . admin_url('profile.php') . '">我的资料</a> 里面绑定帐号。否则请在这里绑定 (即所有作者的文章都会同步到您绑定的微博上)。<br/>每位作者都可以自定义设置，互不干扰！</p>';
		}
		echo '<p>“我的资料”页面的设置或绑定优先级最大。当管理员在资料页有绑定任何一个帐号，则这里的帐号绑定将失效。</p>';
	} else {
		if ($wptm_options['multiple_authors']) {
			echo '<p>您可以在这里绑定帐号，当您发布文章时将同步该文章的信息到您的微博上。</p>';
		}
		if (function_exists('wp_connect_advanced') && $wptm_advanced['registered_users']) {
			echo '<p>绑定帐号后，您可以登录本站，在本站的微博自定义发布页面发布信息到您绑定的帐号上。</p>';
		}
		echo '<p><strong>请您再三确定您信任本站站长，否则导致微博等账户信息泄漏，插件开发者概不负责！</strong></p>';
	}
}
?>
<div class="dialog" id="dialog"> <a href="javascript:void(0);" class="close"></a>
<form method="post" action="<?php echo $action;?>">
<?php wp_nonce_field('options');?>
<p><img src="<?php echo $plugin_url;?>/images/qq.png" class="title_pic" /></p>
<table class="form-table">
<tr valign="top">
<th scope="row"><span class="token">Access token</span><span class="account">帐&nbsp;&nbsp;&nbsp;&nbsp;号</span> :</th>
<td><input type="text" class="bind_username" id="username" name="username" /></td>
</tr>
<tr valign="top">
<th scope="row"><span class="token">Token secret</span><span class="account">密&nbsp;&nbsp;&nbsp;&nbsp;码</span> :</th>
<td><input type="password" class="bind_password" id="password" name="password" autocomplete="off" /></td>
</tr>
</table>
<p class="submit">
<input type="submit" name="update" id="update" class="button-primary" value="<?php _e('Save Changes') ?>" /> &nbsp;
<input type="submit" name="delete" id="delete" class="button-primary" value="解除绑定" onclick="return confirm('Are you sure? ')" />
</p>
</form>
</div>

<div class="dialog_add" id="dialog_add"> <a href="javascript:void(0);" class="close"></a>
<form method="post" action="<?php echo $action;?>">
<?php wp_nonce_field('add');?>
<p><img src="<?php echo $plugin_url;?>/images/qq.png" class="title_pic" /></p>
  <p>您还没有绑定同步授权，是否<b>绑定</b>？</p>
  <p>
    <input type="submit" class="button-primary add" name="add" value="是" /> &nbsp;
	<input type="button" class="button-primary close" value="否" /> 
  </p>
</form>
</div>

<div class="dialog_delete" id="dialog_delete"> <a href="javascript:void(0);" class="close"></a>
<form method="post" action="<?php echo $action;?>">
<?php wp_nonce_field('delete');?>
<p><img src="<?php echo $plugin_url;?>/images/qq.png" class="title_pic" /></p>
  <p>您已经绑定了同步授权，是否<b>解除</b>？</p>
  <p>
    <input type="submit" class="button-primary delete" name="delete" value="是" onclick="return confirm('Are you sure? ')" /> &nbsp;
	<input type="button" class="button-primary close" value="否" /> 
  </p>
</form>
</div>
<script type="text/javascript">
$(function () {
  var tabContainers = $('div.tabs > div');
  var hash = window.location.hash || "<?php echo ($version == 1 && $wptm_basic['appid'] && $wptm_basic['appkey']) ? '#sync' : '#basic';?>";
  var css = hash.replace('#', '.');
  tabContainers.hide().filter(hash).show();
  $(css).addClass('selected');

  $('.basic,.sync,.blog,.connect,.comment,.open,.share,.advanced,.check,.help').click(function () {
    tabContainers.hide();
    tabContainers.filter(this.hash).show();
    $('div.tabs ul.nav a').removeClass('selected');
    //$(this).addClass('selected');
    var css = this.hash.replace('#', '.');
    $(css).addClass('selected');
    return false;
  });
<?php if ($connect_plugin) {?>
  // 评论导入到灯鹭
  $("#exportComments").click(function () {
    $(this).attr("disabled", true);
    exportDenglu(0);
  });

  function exportDenglu(start) {
    var plugins_url = "<?php echo $plugin_url;?>";
    $('#exportStatus').html('正在导入评论到灯鹭评论框 <img src="' + plugins_url + '/images/loading16.gif" />');
    $.post(plugins_url + "/export.php", {
      //type: type,
      start: start,
    }, function (data) {
      if (!isNaN(data)) {
        exportDenglu(data);
        $('#exportStatus').html('已经导入了 ' + data + ' 条评论 <img src="' + plugins_url + '/images/loading16.gif" />');
      } else if (data == 'success') {
        $('#exportStatus').html('导入成功！');
        $("#exportComments").attr("disabled", false);
      } else {
        $('#exportStatus').html('出错了，请重试！');
        $("#exportComments").attr("disabled", false);
		alert(data);
      }
    });
  }
<?php }?>
});
$(".close").show();
$("<?php if($wptm_options['bind']) {echo '#twitter, #qq, #sina, #sohu, #netease, #douban, #tianya,';} elseif($wptm_options['enable_proxy']) {echo '#twitter,';}?><?php if(!is_donate() && !$wptm_options['denglu_bind']) echo '#renren,';?> #digu, #fanfou, #renjian, #zuosa, #wbto").click(function () {
  var id = $(this).attr("id").replace('_porxy', '');
  $(".title_pic").attr("src", "<?php echo $plugin_url;?>/images/" + id + ".png");
  $('input[name="username"]').attr("id", "username_" + id);
  $('input[name="password"]').attr("id", "password_" + id);
  $("#username_renren").attr("value", "<?php echo $account['renren']['username'];?>");
  //$("#username_kaixin").attr("value", "<?php echo $account['kaixin001']['username'];?>");
  $("#username_digu").attr("value", "<?php echo $account['digu']['username'];?>");
  $("#username_fanfou").attr("value", "<?php echo $account['fanfou']['username'];?>");
  $("#username_renjian").attr("value", "<?php echo $account['renjian']['username'];?>");
  $("#username_zuosa").attr("value", "<?php echo $account['zuosa']['username'];?>");
  $("#username_follow5").attr("value", "<?php echo $account['follow5']['username'];?>");
  $("#username_wbto").attr("value", "<?php echo $account['wbto']['username'];?>");
  $(".password").attr("value", "");
  if (id == "twitter" || id == "qq" || id == "sina" || id == "sohu" || id == "netease" || id == "douban" || id == "tianya") {
    $(".account").hide();
    $(".token").show();
  } else {
    $(".token").hide();
	$(".account").show();
  }
  $('#update').attr("name", 'update_' + id);
  $('#delete').attr("name", 'delete_' + id);
  $(".dialog").attr("id", "dialog_" + id);
  $("#delete").hide();
});
$(".bind").click(function () {
  $("#delete").show();
});
<?php
$t_btn = '';
if (!$wptm_options['bind']) {
	$t_btn = '#twitter, #qq, #sina, #sohu, #netease, #douban, #tianya,';
} 
if (is_donate()) {
	$t_btn .= '#shuoshuo, #renren, #kaixin';
} elseif ($wptm_options['denglu_bind']) {
	$t_btn .= '#renren';
} 
if ($t_btn) {
?>
$("<?php echo $t_btn;?>").click(function () {
  var id = $(this).attr("id");
  $(".title_pic").attr("src", "<?php echo $plugin_url;?>/images/" + id + ".png");
  $(".dialog_add").attr("id", "dialog_" + id);
  $(".add").attr("name", "add_" + id);
});
<?php }?>
$("#bind_twitter, #bind_qq, #bind_sina, #bind_sohu, #bind_netease, #bind_douban, #bind_tianya, #bind_renren, #bind_kaixin, #bind_shuoshuo").click(function () {
  var id = $(this).attr("id").replace('bind_', '');
  $(".title_pic").attr("src", "<?php echo $plugin_url;?>/images/" + id + ".png");
  $(".dialog_delete").attr("id", "dialog_" + id);
  $(".delete").attr("name", "delete_" + id);
});
$("#demo").floatdialog("dialog");
$("#demo_add").floatdialog("dialog_add");
$("#demo_delete").floatdialog("dialog_delete");
$("#twitter, #bind_twitter").floatdialog("dialog_twitter");
$("#qq, #bind_qq").floatdialog("dialog_qq");
$("#sina, #bind_sina").floatdialog("dialog_sina");
$("#sohu, #bind_sohu").floatdialog("dialog_sohu");
$("#netease, #bind_netease").floatdialog("dialog_netease");
$("#douban, #bind_douban").floatdialog("dialog_douban");
$("#tianya, #bind_tianya").floatdialog("dialog_tianya");
$("#shuoshuo, #bind_shuoshuo").floatdialog("dialog_shuoshuo");
$("#renren, #bind_renren").floatdialog("dialog_renren");
$("#kaixin, #bind_kaixin").floatdialog("dialog_kaixin");
$("#digu").floatdialog("dialog_digu");
$("#fanfou").floatdialog("dialog_fanfou");
$("#renjian").floatdialog("dialog_renjian");
$("#zuosa").floatdialog("dialog_zuosa");
$("#wbto").floatdialog("dialog_wbto");
$('#update').click(function () {
  if (($(".bind_username").val() == '') || ($(".bind_password").val() == '')) {
    alert("值不能为空!  ");
    return false;
  }
});
//$('.wrap').click(function () {
//   $('.updated').slideUp("normal");
//});
$(function () {
   $('.show_botton').append( $('.hide_botton').html() );
   $('.hide_botton').hide();
});
</script>