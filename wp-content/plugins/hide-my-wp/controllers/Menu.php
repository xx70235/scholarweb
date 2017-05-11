<?php

class HMW_Controllers_Menu extends HMW_Classes_FrontController {

    public function __construct() {
        parent::__construct();

        //add_filter('rewrite_rules_array', array(HMW_Classes_ObjController::getClass('HMW_Models_Rewrite'), 'rewrite_rules'), 999, 1);
    }

    /**
     * Hook the Admin load
     */
    public function hookInit() {
        /* add the plugin menu in admin */
        if (current_user_can('manage_options')) {
            //check if activated
            if (get_transient('hmw_activate') == 1) {
                // Delete the redirect transient
                delete_transient('hmw_activate');

                wp_safe_redirect(admin_url('admin.php?page=hmw_settings'));
                exit();
            }

            //Check if there are expected upgrades
            HMW_Classes_Tools::checkUpgrade();
            HMW_Classes_ObjController::getClass('HMW_Controllers_Notice');
        }
    }

    /**
     * Creates the Setting menu in Wordpress
     */
    public function hookMenu() {
        /* add the plugin menu in admin */
        $this->model->addSubmenu(array('options-general.php',
            __('Hide My Wordpress Settings', _HMW_PLUGIN_NAME_),
            __('Hide My WP', _HMW_PLUGIN_NAME_),
            'manage_options',
            'hmw_settings',
            array(HMW_Classes_ObjController::getClass('HMW_Controllers_Settings'), 'init')
        ));
        $this->model->addOption(array(
            __('Brute Force Protection', _HMW_PLUGIN_NAME_),
            __('Brute Force Protection', _HMW_PLUGIN_NAME_),
            'manage_options',
            'hmw_brute',
            array(HMW_Classes_ObjController::getClass('HMW_Controllers_Settings'), 'init')
        ));
    }
}