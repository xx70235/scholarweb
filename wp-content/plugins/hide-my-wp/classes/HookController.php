<?php

/**
 * The class handles the actions in WP
 */
class HMW_Classes_HookController {

    /** @var array the WP actions list from admin */
    private $admin_hooks = array();

    public function __construct() {
        //called in admin
        $this->admin_hooks = array(
            'init' => 'admin_init',
            'preload' => 'template_redirect',
            'head' => 'admin_head',
            'footer' => 'admin_footer',
            // --
            'menu' => 'admin_menu',
            'notices' => 'admin_notices',
        );

        //called in frontend
        $this->front_hooks = array(
            // --
            'frontinit' => 'init',
        );

    }

    /**
     * Calls the specified action in WP
     * @param string $instance The parent class instance
     *
     * @return void
     */
    public function setHooks($instance) {
        if (is_admin() || is_network_admin()) {
            $this->setAdminHooks($instance);
        } else {
            $this->setFrontHooks($instance);
        }
    }

    /**
     * Calls the specified action in WP
     * @param string $instance The parent class instance
     *
     * @return void
     */
    public function setAdminHooks($instance) {
        /* for each admin action check if is defined in class and call it */
        foreach ($this->admin_hooks as $hook => $value) {

            if (is_callable(array($instance, 'hook' . ucfirst($hook)))) {
                //call the WP add_action function
                add_action($value, array($instance, 'hook' . ucfirst($hook)));
            }
        }
    }

    /**
     * Calls the specified action in WP
     * @param string $instance The parent class instance
     *
     * @return void
     */
    public function setFrontHooks($instance) {
        /* for each admin action check if is defined in class and call it */
        foreach ($this->front_hooks as $hook => $value) {
            if (is_callable(array($instance, 'hook' . ucfirst($hook)))) {
                //call the WP add_action function
                add_action($value, array($instance, 'hook' . ucfirst($hook)), 5);
            }
        }
    }

}