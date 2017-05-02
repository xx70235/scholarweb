<?php
$_SESSION['wp_url_bind'] = '';
//$_SESSION['wp_url_back'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$wptm_basic = get_option('wptm_basic');
$wptm_comment = get_option('wptm_comment');
$wptm_connect = get_option('wptm_connect');
$user = wp_get_current_user();
if ($user->ID) {
	$head = get_image_by_content(get_avatar($user->ID, 50));
	if (strpos($head, "gravatar.com/avatar") !== false) $head = "";
	$userinfo = base64_encode($user->display_name.','.$user->user_email.','.$head);
}
if (is_object($post)) {
	$media_url = wp_multi_media_url($post -> post_content, $post -> ID);
}
?>
<div id="comments" style="display:none"><a name="respond"></a></div>
<script type='text/javascript' charset='utf-8' src='http://open.denglu.cc/connect/commentcode?appid=<?php echo $wptm_basic['appid'];?>&v=1.0.1'></script>
<script type="text/javascript" charset='utf-8'>
    var param = {};
    param.title = "<?php echo rawurlencode(get_the_title());?>"; // 文章标题
    param.postid = "<?php the_ID();?>"; // 文章ID
<?php
	if ($media_url) { // 是否有视频、图片
    echo "param.image = \"" . $media_url[0] ."\";\n"; // 需要同步的图片地址
    echo "param.video = \"" . $media_url[1] ."\";\n"; // 需要同步的视频地址，支持土豆优酷等
    }
	if ($wptm_connect['enable_connect']) { // 是否开启了社会化登录
		$paramlogin = "param.login = false;\n";
	}
	echo (!is_user_logged_in()) ? $paramlogin :"param.userinfo = \"".$userinfo."\";param.login = true;\n"; // 是否已经登录
	echo "param.exit = \"".urlencode(wp_logout_url(get_permalink()))."\";\n"; // 退出链接
?>
    _dl_comment_widget.show(param);
</script>
<?php if ($wptm_comment['enable_seo'] && have_comments()) : ?>
<div id="dengluComments">
	<h3 id="comments"><?php	printf( '《%2$s》有 %1$s 条评论', number_format_i18n( get_comments_number() ), '<em>' . get_the_title() . '</em>' );?></h3>

	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>

	<ol class="commentlist">
	<?php wp_list_comments();?>
	</ol>

	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>
</div>
<script type="text/javascript">
    document.getElementById('dengluComments').style.display="none";
</script>
<?php endif; ?>