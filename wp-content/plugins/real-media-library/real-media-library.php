<?php
/**
Plugin Name: WP Real Media Library
Plugin URI: https://matthias-web.com/wordpress/real-media-library/
Description: 更多WordPress汉化主题、主题升级、问题咨询请访问：<strong><a href="http://www.qcwlseo.com">http://www.qcwlseo.com</a></strong>或者光临<a href="http://qcseo.taobao.com">倾尘网络淘宝店</a>
Author: Matthias Günter
Version: 3.0.2
Author URI: https://matthias-web.com
Licence: GPLv2
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if (defined('RML_PATH')) return;
define('RML_PATH', dirname ( __FILE__ ));
define('RML_MIN_PHP_VERSION', "5.3.0");
define('RML_NS', "MatthiasWeb\\RealMediaLibrary");
define('RML_FILE', __FILE__);
define('RML_TD', 'real-media-library');
define('RML_VERSION', '3.0.2');
define('RML_PRE_GET_POSTS_PRIORITY', 9999999);

/**
 * CONSTANT FOLDER TYPES
 */
define('RML_TYPE_FOLDER', 0);
define('RML_TYPE_COLLECTION', 1);
define('RML_TYPE_GALLERY', 2);
define('RML_TYPE_ALL', 3);
define('RML_TYPE_ROOT', 4);

// Check PHP Version
if ((version_compare(phpversion(), RML_MIN_PHP_VERSION) >= 0)) {
    require_once(RML_PATH . "/inc/others/start.php");
}else{
    if (!function_exists("rml_skip_php_admin_notice")) {
        function rml_skip_php_admin_notice() {
            if (current_user_can("install_plugins")) {
            ?>
            <div class="notice notice-error">
                <p><strong>Real媒体库</strong> 无法初始化，因为您需要最少的PHP版本 <?php echo RML_MIN_PHP_VERSION; ?> ... 您正在运行: <?php echo phpversion(); ?>.
                <a target="_blank" href="http://justifiedgrid.com/support/fix/why-is-my-php-old/">为什么我的PHP老了？</a></p>
            </div>
            <?php
            }
        }
    }
    add_action( 'admin_notices', 'rml_skip_php_admin_notice' );
}
?>