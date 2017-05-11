<?php

class HMW_Models_Rewrite {
    protected $_replace;

    public function clearRedirect() {
        HMW_Classes_Tools::$options = HMW_Classes_Tools::getOptions();
        $this->_replace = array();

        return $this;
    }

    //ADMIN_PATH is the new path and set in /config.php
    public function buildRedirect() {

        if (HMW_Classes_Tools::getOption('hmw_mode') <> 'default') {
            //Redirect the ADMIN

            //Redirect the ADMIN
            if (HMW_Classes_Tools::$default['hmw_admin_url'] <> HMW_Classes_Tools::getOption('hmw_admin_url')) {
                $safeoptions = HMW_Classes_Tools::getOptions(true);
                if (HMW_Classes_Tools::$default['hmw_admin_url'] <> $safeoptions['hmw_admin_url']) {
                    $this->_replace['to'][] = $safeoptions['hmw_admin_url'] . '/';
                    $this->_replace['from'][] = "wp-admin" . '/';
                    $this->_replace['rewrite'][] = true;
                }
                $this->_replace['to'][] = HMW_Classes_Tools::getOption('hmw_admin_url');
                $this->_replace['from'][] = "wp-admin" . '/';
                $this->_replace['rewrite'][] = true;

                $this->_replace['to'][] = HMW_Classes_Tools::getOption('hmw_admin_url') . '/';
                $this->_replace['from'][] = "wp-admin" . '/';
                $this->_replace['rewrite'][] = true;
            }

            if (HMW_Classes_Tools::getOption('hmw_login_url') <> 'wp-login.php') {
                $this->_replace['to'][] = HMW_Classes_Tools::getOption('hmw_login_url');
                $this->_replace['from'][] = "wp-login.php";
                $this->_replace['rewrite'][] = true;

                $this->_replace['to'][] = HMW_Classes_Tools::getOption('hmw_login_url') . '/';
                $this->_replace['from'][] = "wp-login.php";
                $this->_replace['rewrite'][] = true;


            }

        }

        return $this;
    }

    public function setRewriteRules() {
        require_once ABSPATH . 'wp-admin/includes/misc.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';


        $rewrites = array();
        $rewritecode = '';

        if (!empty($this->_replace)) {
            foreach ($this->_replace['to'] as $key => $row) {
                if ($this->_replace['rewrite'][$key]) {
                    $rewrites[] = array(
                        'from' => $this->_replace['to'][$key] . "(.*)",
                        'to' => $this->_replace['from'][$key] . "$" . (substr_count($this->_replace['to'][$key], '(') + 1),
                    );
                }
            }

            foreach ($rewrites as $rewrite) {
                add_rewrite_rule($rewrite['from'], $rewrite['to'], 'top');
            }
        }


        if (!save_mod_rewrite_rules()) {
            $home_root = parse_url(home_url());
            if (isset($home_root['path']))
                $home_root = trailingslashit($home_root['path']);
            else
                $home_root = '/';

            if (HMW_Classes_Tools::isApache() || HMW_Classes_Tools::isLitespeed()) {
                foreach ($rewrites as $rewrite) {
                    if (strpos($rewrite['to'], 'index.php') === false)
                        $rewritecode .= 'RewriteRule ^' . $rewrite['from'] . ' ' . $home_root . $rewrite['to'] . " [QSA,L]<br />";
                }
            }

            $form = '<form method="POST" style="margin: 8px 0;">
                        <input type="hidden" name="action" value="hmw_manualrewrite" />
                        <input type="hidden" name="hmw_nonce" value="' . wp_create_nonce(_HMW_NONCE_ID_) . '" />
                        <input type="submit" class="btn btn-success save" value="Okay, I set it up" />
                    </form>
                    ';
            if ($rewritecode <> '') {
                if (HMW_Classes_Tools::isApache() || HMW_Classes_Tools::isLitespeed()) {
                    if (is_multisite()) {
                        HMW_Classes_Error::setError('Multisite detected. You need to update your .htaccess file by adding following line before \'RewriteCond REQUEST_FILENAME} !-f\': <br /><br /><code>' . $rewritecode . '</code>' . $form);
                    } else {
                        HMW_Classes_Error::setError('.htaccess file is not writable. You need to update your .htaccess file by adding following line before \'RewriteCond REQUEST_FILENAME} !-f\': <br /><br /><code>' . $rewritecode . '</code>' . $form);
                    }
                } elseif (HMW_Classes_Tools::isNginx()) {
                    HMW_Classes_Error::setError(sprintf(__('Nginx detected. You need %sHide my WP PRO%s to work with Nginx servers', _HMW_PLUGIN_NAME_), '<a href="http://wpplugins.tips/product/hide-my-wordpress-pro/" target="_blank">', '</a>'));

                }
            } else {
                HMW_Classes_Error::setError('.htaccess file is not writable. If you added a different admin path before, please remove it in order to work without errors. ' . $form);

            }

            return false;
        }

        return true;
    }

    public function rewrite_rules($wp_rewrite) {
        $rules = array();
        //--
        //$rules["plugin/([^/]*)$"] = 'index.php?pagename=plugin&hmw_url=$matches[1]';

        if (is_array($wp_rewrite)) {
            return array_merge($rules, $wp_rewrite);
        }

        return $rules;
    }

    /**
     * Flush the changes in htaccess
     */
    public function flushChanges() {

        //Update the new options variables
        $this->clearRedirect();

        //Send email with the new URLs if selected
        if (HMW_Classes_Tools::getOption('hmw_send_email')) {
            HMW_Classes_Tools::sendEmail();
        }

        //Set no errors in DB
        HMW_Classes_Tools::saveOptions('error', false);
        HMW_Classes_Tools::saveOptions('logout', false);

        //save to safe mode in case of doudb
        foreach (HMW_Classes_Tools::$options as $key => $value) {
            HMW_Classes_Tools::saveOptions($key, $value, true);
        }

        //Empty the cache from other plugins
        HMW_Classes_Tools::emptyCache();

        //Build th eredirects
        $this->buildRedirect();

        //Flush the changes
        global $wp_rewrite;
        $wp_rewrite->flush_rules(true);
    }

    public function addParams($vars) {
        $vars[] = 'hmw_disable';
        return $vars;
    }

    public function admin_url($url) {
        if (!defined('ADMIN_COOKIE_PATH')) {
            return $url;
        }

        if (HMW_Classes_Tools::getOption('hmw_admin_url') == 'wp-admin') {
            return $url;
        }
        if (HMW_Classes_Tools::getOption('error') || HMW_Classes_Tools::getOption('logout')) {
            return $url;
        }

        if (strpos($url, HMW_Classes_Tools::$default['hmw_admin_url']) === false) {
            return $url;
        }


        if (HMW_Classes_Tools::$default['hmw_admin_url'] <> HMW_Classes_Tools::getOption('hmw_admin_url')) {
            $find = '/' . HMW_Classes_Tools::$default['hmw_admin_url'] . '/';
            $replace = '/' . HMW_Classes_Tools::getOption('hmw_admin_url') . '/';

            HMW_Debug::dump($url, $find, $replace);

            if (strpos($url, $find) !== false) {
                $url = str_replace($find, $replace, $url);
            }
        }

        return $url;
    }

    /**
     * Get the new Login URL
     * @param $url
     * @return string
     */
    public function login_url($url) {
        if (HMW_Classes_Tools::getOption('error')) {
            return $url;
        }

        if (HMW_Classes_Tools::$default['hmw_login_url'] <> HMW_Classes_Tools::getOption('hmw_login_url')) {
            //check if disable and do not redirect to login
            if (HMW_Classes_Tools::getIsset('hmw_disable')) {
                if (HMW_Classes_Tools::getValue('hmw_disable') == HMW_Classes_Tools::getOption('hmw_disable')) {
                    //add the disabled param in order to work without issues
                    return add_query_arg(array('hmw_disable' => HMW_Classes_Tools::getOption('hmw_disable')), $url);
                }
            }

            $url = site_url(HMW_Classes_Tools::getOption('hmw_login_url'));

        }
        return $url;
    }

    /**
     * Get the relative URL
     * @param $url
     * @return mixed|string
     */
    public function relative_url($url) {
        if ($url <> '') {
            $url = str_replace(get_bloginfo('wpurl'), '', $url);
            $url = trim($url, '/');
        }
        return $url;
    }

    public function network_admin_url($url) {
        if (!defined('ADMIN_COOKIE_PATH')) {
            return $url;
        }

        if (HMW_Classes_Tools::getOption('error') || HMW_Classes_Tools::getOption('logout')) {
            return $url;
        }

        if (HMW_Classes_Tools::getOption('hmw_admin_url') == 'wp-admin') {
            return $url;
        }

        if (HMW_Classes_Tools::$default['hmw_admin_url'] <> HMW_Classes_Tools::getOption('hmw_admin_url')) {
            $renameFrom = HMW_Classes_Tools::$default['hmw_admin_url'];
            $renameTo = HMW_Classes_Tools::getOption('hmw_admin_url');
            $find = network_site_url($renameFrom . '/', $renameTo);
            $replace = network_site_url('/' . HMW_Classes_Tools::getOption('hmw_admin_url') . '/', $renameTo);

            if (strpos($url, $find) === 0) {
                $url = $replace . substr($url, strlen($find));
            }
        }

        return $url;
    }

    public function site_url($url, $path = '') {
        if (HMW_Classes_Tools::getOption('error') || HMW_Classes_Tools::getOption('logout') || $url == '') {
            return $url;
        }

        if (strpos($url, 'wp-login') !== false) {
            $query = '';
            if ($path <> '') {
                $parsed = @parse_url($path);
                if (isset($parsed['query'])) {
                    $query = '?' . $parsed['query'];
                }
            }

            $url = site_url() . '/' . HMW_Classes_Tools::getOption('hmw_login_url') . $query;

            if (HMW_Classes_Tools::getValue('noredirect', false)) {
                $url = add_query_arg(array('noredirect' => true), $url);
            }
        }

        return $url;
    }

    public function logout_url($url) {

        return add_query_arg(array('hmw_disable' => HMW_Classes_Tools::getOption('hmw_disable')), $url);
    }

    public function wp_logout() {
        HMW_Classes_Tools::$options = HMW_Classes_Tools::getOptions();
        if (HMW_Classes_Tools::getOption('logout')) {
            $this->flushChanges();
        }
        wp_destroy_current_session();
        wp_clear_auth_cookie();
        $_REQUEST['redirect_to'] = $redirect_to = network_site_url();
        wp_safe_redirect($redirect_to);
        die();
    }

    public function sanitize_redirect($redirect, $request, $user) {

        $parsed = parse_url($redirect);
        if (isset($parsed['query']) && !empty($parsed['query'])) {
            parse_str($parsed['query']);
            if (isset($hmw_disable)) {
                if ($hmw_disable == HMW_Classes_Tools::getOption('hmw_disable')) {
                    $_GET['hmw_disable'] = HMW_Classes_Tools::getOption('hmw_disable');
                }
            }
        }

        if (HMW_Classes_Tools::getIsset('hmw_disable')) {
            if (HMW_Classes_Tools::getValue('hmw_disable') == HMW_Classes_Tools::getOption('hmw_disable')) {
                HMW_Classes_Tools::$options = array_merge(HMW_Classes_Tools::$options, HMW_Classes_Tools::$default);
                HMW_Classes_Tools::saveOptions();
                $this->hmw_remove_config_cache();
                $this->flushChanges();
                return HMW_Classes_Tools::$default['hmw_admin_url'];
            }
        }

        if (HMW_Classes_Tools::getOption('hmw_login_url') <> HMW_Classes_Tools::$default['hmw_login_url']) {
            if (strpos($redirect, 'wp-login.php') !== false) {
                $redirect = admin_url(HMW_Classes_Tools::getOption('hmw_login_url'));
            }
        }

        if (HMW_Classes_Tools::getOption('hmw_admin_url') <> HMW_Classes_Tools::$default['hmw_admin_url']) {
            if (strpos($redirect, 'wp-admin') !== false) {
                $redirect = admin_url();
            }
        }
        if (strrpos(strrev($redirect), strrev(HMW_Classes_Tools::getOption('hmw_admin_url'))) === 0) {
            $redirect = admin_url();

        }

        if (HMW_Classes_Tools::getValue('noredirect', false)) {
            if (isset($user) && isset($user->ID)) {
                if (!is_wp_error($user) && empty($_REQUEST['reauth'])) {
                    //exit($redirect . ' ' . admin_url());
                    if ((empty($redirect) || $redirect == 'wp-admin/' || $redirect == admin_url())) {
                        // If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
                        if (is_multisite() && !get_active_blog_for_user($user->ID) && !is_super_admin($user->ID))
                            $redirect = user_admin_url();
                        elseif (method_exists($user, 'has_cap') && is_multisite() && !$user->has_cap('read'))
                            $redirect = get_dashboard_url($user->ID);
                        elseif (method_exists($user, 'has_cap') && !$user->has_cap('edit_posts'))
                            $redirect = $user->has_cap('read') ? admin_url('profile.php') : home_url();

                        wp_redirect($redirect);
                        exit();
                    }
                    wp_safe_redirect($redirect);
                    exit();
                }
            }
        }


        return $redirect;
    }

    /**
     * Login Header Hook
     */
    public function login_head() {
        add_filter('login_headerurl', array($this, 'login_url'), 99, 1);
    }

    public function login_init() {
        if (HMW_Classes_Tools::getOption('error')) {
            return;
        }

        if (HMW_Classes_Tools::$default['hmw_login_url'] <> HMW_Classes_Tools::getOption('hmw_login_url')) {
            add_filter('lostpassword_redirect', array($this, 'lostpassword_redirect'), 1, 0);
            add_filter('registration_redirect', array($this, 'registration_redirect'), 1, 0);
        }

        add_filter('wp_safe_redirect_fallback', array($this, 'loopCheck'), 99, 1);
        add_filter('wp_redirect', array($this, 'loopCheck'), 99, 1);

        //remove clasiera theme loop
        remove_action('login_init', 'classiera_cubiq_login_init');

        //remove boddyboss theme login redirect
        remove_filter( 'login_redirect', 'buddyboss_redirect_previous_page', 10, 3 );

        //if prevent redirect option is activated
        if (HMW_Classes_Tools::getValue('noredirect', false)) {
            remove_all_actions('login_init');
            remove_all_filters('login_redirect');
            add_filter('login_redirect', array($this, 'sanitize_redirect'), 1, 3);
        }

    }

    public function loopCheck($url) {
        if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI']) && $url <> '') {
            $current_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $redirect_url = parse_url($url, PHP_URL_HOST) . parse_url($url, PHP_URL_PATH);

            if ($current_url <> '' && $redirect_url <> '') {
                if ($current_url == $redirect_url) {
                    return add_query_arg(array('noredirect' => true), $url);
                }
            }

            if (HMW_Classes_Tools::$default['hmw_login_url'] <> HMW_Classes_Tools::getOption('hmw_login_url')) {
                if ((strpos($url, '/wp-login/') !== false || strpos($url, '/wp-login.php') !== false) && HMW_Classes_Tools::getOption('hmw_hide_login')) {
                    return add_query_arg(array('noredirect' => true), site_url(HMW_Classes_Tools::getOption('hmw_login_url')));
                }
                if (strpos($url, '/login/') !== false && HMW_Classes_Tools::getOption('hmw_login_url') <> 'login' && HMW_Classes_Tools::getOption('hmw_hide_login')) {
                    return add_query_arg(array('noredirect' => true), site_url(HMW_Classes_Tools::getOption('hmw_login_url')));
                }
            }

        }

        if (HMW_Classes_Tools::getValue('noredirect', false)) {
            return add_query_arg(array('noredirect' => true), $url);
        }

        return $url;
    }

    /**
     * Change the password confirm URL with the new URL
     * @return string
     */
    public function lostpassword_redirect() {
        return site_url('wp-login.php?checkemail=confirm');
    }

    /**
     * Change the register confirmation URL with the new URL
     * @return string
     */
    public function registration_redirect() {
        return site_url('wp-login.php?checkemail=registered');
    }

    /**
     * Return 404 page
     */
    function getNotFound() {

        if (HMW_Classes_Tools::getOption('hmw_url_redirect') == '404') {
            status_header(404);
            nocache_headers();
            global $wp_query;
            $wp_query->is_404 = true;

            wp_safe_redirect(site_url('404'));
        } else if (HMW_Classes_Tools::getOption('hmw_url_redirect') == '.') {
            wp_safe_redirect(site_url());
        } else {
            wp_safe_redirect(site_url(HMW_Classes_Tools::getOption('hmw_url_redirect')));
        }


        die();
    }

    public function hmw_remove_config_cache() {
        if (file_exists(ABSPATH . 'wp-config.php')) {
            $global_config_file = ABSPATH . 'wp-config.php';
        } else {
            $global_config_file = dirname(ABSPATH) . '/wp-config.php';
        }


        $lines = file($global_config_file);
        foreach ((array)$lines as $line) {
            if (preg_match("/define *\( *'ADMIN_COOKIE_PATH'/", $line)) {
                if (!$this->is_writeable_ACLSafe($global_config_file) || !$this->wp_cache_replace_line('define *\( *\'ADMIN_COOKIE_PATH\'', '', $global_config_file)) {
                    HMW_Classes_Error::setError(sprintf(__('%s is not writable. Open the file and remove: <br/><em><strong>%s</strong></em> on line 2 ', _HMW_PLUGIN_NAME_), $global_config_file, 'define *\( *\'ADMIN_COOKIE_PATH\' ..'));
                    return false;
                }
            }
        }

        return true;
    }

    /**
     *
     *
     * @param $url
     * @return bool
     */
    public function hmw_create_config_cache($url) {
        return $this->hmw_remove_config_cache();
    }

    public function is_writeable_Htaccess() {
        if (is_multisite()) {
            return false;
        }

        global $wp_rewrite;

        $home_path = get_home_path();
        $htaccess_file = $home_path . '.htaccess';

        /*
         * If the file doesn't already exist check for write access to the directory
         * and whether we have some rules. Else check for write access to the file.
         */
        if ((!file_exists($htaccess_file) && is_writable($home_path) && $wp_rewrite->using_mod_rewrite_permalinks()) || is_writable($htaccess_file)) {
            if (got_mod_rewrite()) {
                return true;
            }
        }

        return false;
    }

    public function is_writeable_ACLSafe($path) {
        // PHP's is_writable does not work with Win32 NTFS
        if ($path{strlen($path) - 1} == '/') // recursively return a temporary file path
            return $this->is_writeable_ACLSafe($path . uniqid(mt_rand()) . '.tmp');
        else if (is_dir($path))
            return $this->is_writeable_ACLSafe($path . '/' . uniqid(mt_rand()) . '.tmp');
        // check tmp file for read/write capabilities
        $rm = file_exists($path);
        $f = @fopen($path, 'a');
        if ($f === false)
            return false;
        fclose($f);
        if (!$rm)
            unlink($path);
        return true;
    }

    public function wp_cache_replace_line($old, $new, $my_file) {
        if (@is_file($my_file) == false) {
            return false;
        }
        if (!$this->is_writeable_ACLSafe($my_file)) {
            return false;
        }

        $found = false;
        $lines = file($my_file);
        foreach ((array)$lines as $line) {
            if (preg_match("/$old/", $line)) {
                $found = true;
                break;
            }
        }
        if ($found) {
            $fd = fopen($my_file, 'w');
            foreach ((array)$lines as $line) {
                if (!preg_match("/$old/", $line))
                    fputs($fd, $line);
                elseif ($new <> '') {
                    fputs($fd, "$new //Added by Hide My Wordpress\n\r");
                }
            }
            fclose($fd);
            return true;
        }
        $fd = fopen($my_file, 'w');
        $done = false;
        foreach ((array)$lines as $line) {
            if ($done || !preg_match('/^(if\ \(\ \!\ )?define|\$|\?>/', $line)) {
                fputs($fd, $line);
            } else {
                if ($new <> '') {
                    fputs($fd, "$new //Added by Hide My Wordpress\n\r");
                }
                fputs($fd, $line);
                $done = true;
            }
        }
        fclose($fd);
        return true;
    }


    /********************** COOKIES  ******************************/
    public function checkCookies() {
        if (HMW_Classes_Tools::$default['hmw_admin_url'] <> HMW_Classes_Tools::getOption('hmw_admin_url')) {
            HMW_Classes_Tools::setCookieContants();
            add_filter('redirect_post_location', array($this, 'setPostCookie'), 11, 2);
            add_action('set_auth_cookie', array($this, 'setLoginCookie'), 11, 2);
            add_action('clear_auth_cookie', array($this, 'setCleanCookie'), 11, 0);
        }
    }

    public function setPostCookie($location, $post_id) {
        if (defined('HMW_ADMIN_COOKIE_PATH')) {
            if ($post_id > 0) {
                if (isset($_COOKIE['wp-saving-post']) && $_COOKIE['wp-saving-post'] === $post_id . '-check') {
                    setcookie('wp-saving-post', $post_id . '-saved', time() + DAY_IN_SECONDS, HMW_ADMIN_COOKIE_PATH, COOKIE_DOMAIN, is_ssl());
                }
            }
        }
        return $location;
    }

    public function setLoginCookie($auth_cookie, $expire) {
        if (defined('HMW_ADMIN_COOKIE_PATH') && defined('PLUGINS_COOKIE_PATH')) {
            $secure = is_ssl();
            if ($secure) {
                $auth_cookie_name = SECURE_AUTH_COOKIE;
            } else {
                $auth_cookie_name = AUTH_COOKIE;
            }

            setcookie($auth_cookie_name, $auth_cookie, $expire, HMW_ADMIN_COOKIE_PATH, COOKIE_DOMAIN, $secure, true);
        }
    }

    public function setCleanCookie() {
        if (defined('HMW_ADMIN_COOKIE_PATH')) {
            setcookie(AUTH_COOKIE, ' ', time() - YEAR_IN_SECONDS, HMW_ADMIN_COOKIE_PATH, COOKIE_DOMAIN);
            setcookie(SECURE_AUTH_COOKIE, ' ', time() - YEAR_IN_SECONDS, HMW_ADMIN_COOKIE_PATH, COOKIE_DOMAIN);
        }
    }
    /******************************************************************************/
}
