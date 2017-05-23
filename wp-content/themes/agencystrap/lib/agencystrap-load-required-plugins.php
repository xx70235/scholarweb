<?php 
require_once get_template_directory() . '/lib/TGM-Plugin-Activation-2.6.1/class-tgm-plugin-activation.php';
add_action( 'tgmpa_register', 'agencystrap_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * The variables passed to the `tgmpa()` function should be:
 * - an array of plugin arrays;
 * - optionally a configuration array.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function agencystrap_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		/* ------------------------------------------------
		// This is an example of how to include a plugin bundled with a theme.
		array(
			'name'               => 'Bootstrap Shortcodes for WordPress', // The plugin name.
			'slug'               => 'bootstrap-3-shortcodes', // The plugin slug (typically the folder name).
			'source'             => get_template_directory() . '/lib/plugins/bootstrap-3-shortcodes.3.3.8.zip', // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),
		------------------------------------------------ */

		// Include a plugin from the WordPress Plugin Repository.
		array(
			'name'      => 'Simple Shortcodes',
			'slug'      => 'smpl-shortcodes',
			'required'  => true,
		),
        
        array(
			'name'      => 'WP Subtitle',
			'slug'      => 'wp-subtitle',
			'required'  => true,
		),


	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'agencystrap',          // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => get_template_directory() . '/lib/TGM-Plugin-Activation-2.6.1/', // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );
}
