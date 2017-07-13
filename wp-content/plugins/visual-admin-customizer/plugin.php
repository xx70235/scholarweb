<?php
/*
Plugin Name: Visual Admin Customizer
Plugin URI: http://w-shadow.com/
Description: Hide almost any part of the WordPress admin by using a visual editor.
Version: 1.0
Author: Janis Elsts
Author URI: http://w-shadow.com/blog/
*/

function ws_vac_show_version_error() {
	if (!current_user_can('activate_plugins')) {
		return;
	}

	printf(
		'<div class="notice notice-error"><p>
			Visual Admin Customizer requires PHP 5.3 or later. 
			Installed PHP version: %s
		</p></div>',
		esc_html(phpversion())
	);
};

if (version_compare(phpversion(), '5.3', '<')) {
	add_action('admin_notices', 'ws_vac_show_version_error');
	return;
}

define('WS_VAC_ROOT_DIR', __DIR__);
define('WS_VAC_PLUGIN_FILE', __FILE__);

require dirname(__FILE__) . '/init.php';