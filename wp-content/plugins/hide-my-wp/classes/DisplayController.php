<?php

/**
 * The class handles the theme part in WP
 */
class HMW_Classes_DisplayController {

    private static $cache;

    private static function _isAjax() {
        $url = (isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : false));

        if ($url && strpos(admin_url('admin-ajax.php', 'relative'), $url) !== false) {
            return true;
        }

        return false;
    }

    /**
     * echo the css link from theme css directory
     *
     * @param string $uri The name of the css file or the entire uri path of the css file
     * @param string $media
     *
     * @return string
     */
    public static function loadMedia($uri = '', $media = 'all') {
        $css_uri = '';
        $js_uri = '';

        if (self::_isAjax() || isset(self::$cache[$uri])) {
            return;
        }

        self::$cache[$uri] = true;

        /* if is a custom css file */
        if (strpos($uri, '//') === false) {
            $name = strtolower($uri);
            if (file_exists(_HMW_THEME_DIR_ . 'css/' . $name . '.css')) {
                $css_uri = _HMW_THEME_URL_ . 'css/' . $name . '.css?ver=' . HMW_VERSION_ID;
            }
            if (file_exists(_HMW_THEME_DIR_ . 'js/' . $name . '.js')) {
                $js_uri = _HMW_THEME_URL_ . 'js/' . $name . '.js?ver=' . HMW_VERSION_ID;
            }
        } else {
            $name = strtolower(basename($uri));
            if (strpos($uri, '.css') !== FALSE)
                $css_uri = $uri;
            elseif (strpos($uri, '.js') !== FALSE) {
                $js_uri = $uri;
            }
        }

        if ($css_uri <> '') {

            if (!wp_style_is($name)) {
                wp_enqueue_style($name, $css_uri, null, HMW_VERSION_ID, $media);
            }

            wp_print_styles(array($name));
        }

        if ($js_uri <> '') {

            if (!wp_script_is($name)) {
                wp_enqueue_script($name, $js_uri, array('jquery'), HMW_VERSION_ID, true);
            }

            wp_print_scripts(array($name));
        }
    }

    /**
     * return the block content from theme directory
     *
     * @return string
     */
    public function getView($block, $view) {
        $output = null;
        if (file_exists(_HMW_THEME_DIR_ . $block . '.php')) {

            ob_start();
            include(_HMW_THEME_DIR_ . $block . '.php');
            $output .= ob_get_clean();
        }

        return $output;
    }

}