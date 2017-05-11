<?php

/*
  Copyright (c) 2016, WPPlugins.
  The copyrights to the software code in this file are licensed under the (revised) BSD open source license.

  Plugin Name: Hide My Wp
  Plugin URI:
  Author: WPPlugins
  Description: You can choose to hide the admin URLs and login to increases your wordpress security against hackers and spammers. <br /> <a href="http://wpplugins.tips/wordpress" target="_blank"><strong>Unlock all features</strong></a>
  Version: 1.1.025
  Author URI: http://wpplugins.tips
 */
define('HMW_VERSION', '1.1.025');

/* Call config files */
require(dirname(__FILE__) . '/debug/index.php');
require(dirname(__FILE__) . '/config/config.php');

/* important to check the PHP version */
if (PHP_VERSION_ID >= 5100) {
    if (!class_exists('HMWP_Classes_ObjController')) {
        /* inport main classes */
        require_once(_HMW_CLASSES_DIR_ . 'ObjController.php');
        HMW_Classes_ObjController::getClass('HMW_Classes_FrontController');

        add_action( 'upgrader_process_complete', array(HMW_Classes_ObjController::getClass('HMW_Classes_Tools'), 'checkWpUpdates'), 1, 0 );

        if (is_admin() || is_network_admin()) {
            /* Main class call for admin */
            add_action('init', array(HMW_Classes_ObjController::getClass('HMW_Classes_FrontController'), 'runAdmin'));

            register_activation_hook(__FILE__, array(HMW_Classes_ObjController::getClass('HMW_Classes_Tools'), 'hmw_activate'));
            register_deactivation_hook(__FILE__, array(HMW_Classes_ObjController::getClass('HMW_Classes_Tools'), 'hmw_deactivate'));
        } else {
            add_action('init', array(HMW_Classes_ObjController::getClass('HMW_Classes_FrontController'), 'runFrontend'));
        }
    }
} else {
    /* Main class call */
    add_action('admin_notices', 'hmw_showError');
}

/**
 * Called in Notice Hook
 */
function hmw_showError() {
    echo '<div class="update-nag"><span style="color:red; font-weight:bold;">' . __('For Hide My Wordpress to work, the PHP version has to be equal or greater than 5.1', _HMW_PLUGIN_NAME_) . '</span></div>';
}


