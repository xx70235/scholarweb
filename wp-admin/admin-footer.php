<?php
/**
 * WordPress Administration Template Footer
 *
 * @package WordPress
 * @subpackage Administration
 */

// don't load directly
if ( !defined('ABSPATH') )
	die('-1');

/**
 * @global string $hook_suffix
 */
global $hook_suffix;
?>

<div class="clear"></div></div><!-- wpbody-content -->
<div class="clear"></div></div><!-- wpbody -->
<div class="clear"></div></div><!-- wpcontent -->

<div id="wpfooter" role="contentinfo">
	<?php
	/**
	 * Fires after the opening tag for the admin footer.
	 *
	 * @since 2.5.0
	 */
	do_action( 'in_admin_footer' );
	?>
	<p id="footer-left" class="alignleft">
<!--		--><?php
//		$text = sprintf( __( 'Thank you for creating with <a href="%s">WordPress</a>.' ), __( 'https://wordpress.org/' ) );
//		/**
//		 * Filters the "Thank you" text displayed in the admin footer.
//		 *
//		 * @since 2.8.0
//		 *
//		 * @param string $text The content that will be printed.
//		 */
//		echo apply_filters( 'admin_footer_text', '<span id="footer-thankyou">' . $text . '</span>' );
//		?>
	</p>
	<p id="footer-upgrade" class="alignright">
		<?php
		/**
		 * Filters the version/update text displayed in the admin footer.
		 *
		 * WordPress prints the current version and update information,
		 * using core_update_footer() at priority 10.
		 *
		 * @since 2.3.0
		 *
		 * @see core_update_footer()
		 *
		 * @param string $content The content that will be printed.
		 */
//		echo apply_filters( 'update_footer', '' );
		?>
	</p>
	<div class="clear"></div>
</div>
<?php
/**
 * Prints scripts or data before the default footer scripts.
 *
 * @since 1.2.0
 *
 * @param string $data The data to print.
 */
do_action( 'admin_footer', '' );

/**
 * Prints scripts and data queued for the footer.
 *
 * The dynamic portion of the hook name, `$hook_suffix`,
 * refers to the global hook suffix of the current page.
 *
 * @since 4.6.0
 */
do_action( "admin_print_footer_scripts-{$hook_suffix}" );

/**
 * Prints any scripts and data queued for the footer.
 *
 * @since 2.8.0
 */
do_action( 'admin_print_footer_scripts' );

/**
 * Prints scripts or data after the default footer scripts.
 *
 * The dynamic portion of the hook name, `$hook_suffix`,
 * refers to the global hook suffix of the current page.
 *
 * @since 2.8.0
 */
do_action( "admin_footer-{$hook_suffix}" );

// get_site_option() won't exist when auto upgrading from <= 2.7
if ( function_exists('get_site_option') ) {
	if ( false === get_site_option('can_compress_scripts') )
		compression_test();
}

?>

<div class="clear"></div></div><!-- wpwrap -->
<div class="popup" data-popup="popup-1">
    <div class="popup-inner">
        <h2>如何快速找到高质量的校徽或机构LOGO</h2>
        <div class="entry-content" itemprop="text">
            <p>① 校徽查找<br>
                维基百科（https://zh.wikipedia.org/）是查找高质量校徽最便捷的途径，请在右上角搜索框输入学校名称检索，鼠标移动到校徽图案上右键另存即可，国内外高校均由完整收录<br>
                例如Massachusetts Institute of Technology<br>
                <img src="http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo1.png" alt="" width="2524" height="826" class="aligncenter size-full wp-image-909" srcset="http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo1.png 2524w, http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo1-300x98.png 300w, http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo1-768x251.png 768w, http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo1-1024x335.png 1024w" sizes="(max-width: 2524px) 100vw, 2524px"><br>
                <img src="http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo2.png" alt="" width="295" height="295" class="aligncenter size-full wp-image-910" srcset="http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo2.png 295w, http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo2-150x150.png 150w" sizes="(max-width: 295px) 100vw, 295px">    </p>
            <p>上海交通大学<br>
                <img src="http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo3.png" alt="" width="2494" height="1010" class="aligncenter size-full wp-image-911" srcset="http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo3.png 2494w, http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo3-300x121.png 300w, http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo3-768x311.png 768w, http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo3-1024x415.png 1024w" sizes="(max-width: 2494px) 100vw, 2494px"><br>
                <img src="http://www.shidaixuezhe.com/wp-content/uploads/2017/08/lgo4.png" alt="" width="296" height="296" class="aligncenter size-full wp-image-908" srcset="http://www.shidaixuezhe.com/wp-content/uploads/2017/08/lgo4.png 296w, http://www.shidaixuezhe.com/wp-content/uploads/2017/08/lgo4-150x150.png 150w" sizes="(max-width: 296px) 100vw, 296px">  </p>
            <p>② 中科院研究机构LOGO<br>
                中科院研究所的LOGO一般文字内容较多，且部分无法另存为图片，因此方便起见可以利用电脑截图工具获得。<br>
                例如 中国科学院上海有机化学研究所<br>
                <img src="http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo5.png" alt="" width="850" height="131" class="aligncenter size-full wp-image-912" srcset="http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo5.png 850w, http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo5-300x46.png 300w, http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo5-768x118.png 768w" sizes="(max-width: 850px) 100vw, 850px"><br>
                中国科学院高能物理研究所<br>
                <img src="http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo6.png" alt="" width="707" height="133" class="aligncenter size-full wp-image-913" srcset="http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo6.png 707w, http://www.shidaixuezhe.com/wp-content/uploads/2017/08/logo6-300x56.png 300w" sizes="(max-width: 707px) 100vw, 707px"></p>
            <p>③ 如上述办法仍未获得理想素材，请联系单位宣传部门获得官方LOGO。</p>
            <p><a data-popup-close="popup-1" href="#">Close</a></p>
            <a class="popup-close" data-popup-close="popup-1" href="#">x</a>
        </div>
    </div>
</div>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
<link rel='stylesheet' href="./css/help.css" type="text/css" />
</body>

</html>
