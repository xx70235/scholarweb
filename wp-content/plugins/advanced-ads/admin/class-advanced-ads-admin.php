<?php

/**
 * Advanced Ads.
 *
 * @package   Advanced_Ads_Admin
 * @author    Thomas Maier <thomas.maier@webgilde.com>
 * @license   GPL-2.0+
 * @link      http://webgilde.com
 * @copyright 2013-2015 Thomas Maier, webgilde GmbH
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * @package Advanced_Ads_Admin
 * @author  Thomas Maier <thomas.maier@webgilde.com>
 */
class Advanced_Ads_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Instance of admin notice class.
	 *
	 * @since    1.5.2
	 * @var      object
	 */
	protected $notices = null;

	/**
	 * Slug of the settings page
	 *
	 * @since    1.0.0
	 * @var      string
	 */
	public $plugin_screen_hook_suffix = null;

	/**
	 * general plugin slug
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	protected $plugin_slug = '';

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			new Advanced_Ads_Ad_Ajax_Callbacks;
			add_action( 'plugins_loaded', array( $this, 'wp_plugins_loaded_ajax' ) );
		} else {
			add_action( 'plugins_loaded', array( $this, 'wp_plugins_loaded' ) );
		}
		// add shortcode creator to TinyMCE
		Advanced_Ads_Shortcode_Creator::get_instance();
		Advanced_Ads_Admin_Licenses::get_instance();
	}
	
	/**
	 * license handling legacy code after moving license handling code to Advanced_Ads_Admin_Licenses
	 * 
	 * @since version 1.7.16 (early January 2017)
	 */
	public function deactivate_license( $addon = '', $plugin_name = '', $options_slug = '' ){ return Advanced_Ads_Admin_Licenses::get_instance()->deactivate_license( $addon = '', $plugin_name = '', $options_slug = '' ); }
	public function get_license_status( $slug = '' ){ return Advanced_Ads_Admin_Licenses::get_instance()->get_license_status( $slug = '' ); }

	/**
	 * actions and filter available after all plugins are initialized
	 */
	public function wp_plugins_loaded() {
	/*
         * Call $plugin_slug from public plugin class.
         *
         */
		$plugin = Advanced_Ads::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 9 );

		// update placements
		add_action( 'admin_init', array('Advanced_Ads_Placements', 'update_placements') );
		
		// check for update logic
		add_action( 'admin_notices', array($this, 'admin_notices') );
		

		// set 1 column layout on overview page as user and page option
		add_filter( 'screen_layout_columns', array('Advanced_Ads_Overview_Widgets_Callbacks', 'one_column_overview_page') );
		add_filter( 'get_user_option_screen_layout_toplevel_page_advanced', array( 'Advanced_Ads_Overview_Widgets_Callbacks', 'one_column_overview_page_user') );
		
		// add links to plugin page
		add_filter( 'plugin_action_links_' . ADVADS_BASE, array( $this, 'add_plugin_links' ) );
		
		// display information when user is going to disable the plugin
		add_filter( 'admin_footer', array( $this, 'add_deactivation_logic' ) );
		// add_filter( 'after_plugin_row_' . ADVADS_BASE, array( $this, 'display_deactivation_message' ) );
		
		// disable adding rel="noopener noreferrer" to link added through TinyMCE for rich content ads
		add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_allow_unsafe_link_target' ) );
		
		Advanced_Ads_Admin_Meta_Boxes::get_instance();
		Advanced_Ads_Admin_Menu::get_instance();
		Advanced_Ads_Admin_Ad_Type::get_instance();
		Advanced_Ads_Admin_Settings::get_instance();
	}
	
	/**
	 * actions and filters that should also be available for ajax
	 */
	public function wp_plugins_loaded_ajax() {
		// needed here in order to work with Quick Edit option on ad list page
		Advanced_Ads_Admin_Ad_Type::get_instance();
		
		add_action( 'wp_ajax_advads_send_feedback', array( $this, 'send_feedback' ) );
	}
	
	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 */
	public function enqueue_admin_styles() {
		wp_enqueue_style( $this->plugin_slug . '-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), ADVADS_VERSION );
		if( self::screen_belongs_to_advanced_ads() ){
			// jQuery ui smoothness style 1.11.4
			wp_enqueue_style( $this->plugin_slug . '-jquery-ui-styles', plugins_url( 'assets/jquery-ui/jquery-ui.min.css', __FILE__ ), array(), '1.11.4' );
		}
		//wp_enqueue_style( 'jquery-style', '//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css' );
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		// global js script
		wp_enqueue_script( $this->plugin_slug . '-admin-global-script', plugins_url( 'assets/js/admin-global.js', __FILE__ ), array('jquery'), ADVADS_VERSION );
		wp_enqueue_script( $this->plugin_slug . '-admin-find-adblocker', plugins_url( 'assets/js/advertisement.js', __FILE__ ), array(), ADVADS_VERSION );

		if( self::screen_belongs_to_advanced_ads() ){
		    wp_register_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery', 'jquery-ui-autocomplete' , 'jquery-ui-button' ), ADVADS_VERSION );
		    wp_register_script( $this->plugin_slug . '-wizard-script', plugins_url( 'assets/js/wizard.js', __FILE__ ), array('jquery'), ADVADS_VERSION );

		    // jquery ui
		    wp_enqueue_script( 'jquery-ui-accordion' );
		    wp_enqueue_script( 'jquery-ui-button' );
		    wp_enqueue_script( 'jquery-ui-tooltip' );

		    // just register this script for later inclusion on ad group list page
		    wp_register_script( 'inline-edit-group-ads', plugins_url( 'assets/js/inline-edit-group-ads.js', __FILE__ ), array('jquery'), ADVADS_VERSION );
		    
		    // register admin.js translations
		    $translation_array = array(
			    'condition_or' => __( 'or', 'advanced-ads' ),
			    'condition_and' => __( 'and', 'advanced-ads' ),
			    'after_paragraph_promt' => __( 'After which paragraph?', 'advanced-ads' ),
		    );
		    wp_localize_script( $this->plugin_slug . '-admin-script', 'advadstxt', $translation_array );
		    
		    wp_enqueue_script( $this->plugin_slug . '-admin-script' );
		    wp_enqueue_script( $this->plugin_slug . '-wizard-script' );
		}

		//call media manager for image upload only on ad edit pages
		$screen = get_current_screen();
		if( isset( $screen->id ) && Advanced_Ads::POST_TYPE_SLUG === $screen->id ) {
			// the 'wp_enqueue_media' function can be executed only once and should be called with the 'post' parameter
			// in this case, the '_wpMediaViewsL10n' js object inside html will contain id of the post, that is necessary to view oEmbed priview inside tinyMCE editor.
			// since other plugins can call the 'wp_enqueue_media' function without the 'post' parameter, Advanced Ads should call it earlier.
			global $post;
			wp_enqueue_media( array( 'post' => $post ) );
		}

	}

	/**
	 * check if the current screen belongs to Advanced Ads
	 *
	 * @since 1.6.6
	 * @return bool true if screen belongs to Advanced Ads
	 */
	static function screen_belongs_to_advanced_ads(){

		if( ! function_exists( 'get_current_screen' ) ){
		    return false;
		}
		
		$screen = get_current_screen();
		//echo $screen->id;
		if( !isset( $screen->id ) ) {
			return false;
		}

		$advads_pages = apply_filters( 'advanced-ads-dashboard-screens', array(
			'advanced-ads_page_advanced-ads-groups', // ad groups
			'edit-advanced_ads', // ads overview
			'advanced_ads', // ad edit page
			'advanced-ads_page_advanced-ads-placements', // placements
			'advanced-ads_page_advanced-ads-settings', // settings
			'toplevel_page_advanced-ads', // overview
			'admin_page_advanced-ads-debug', // debug
			'advanced-ads_page_advanced-ads-support', // support
			'admin_page_advanced-ads-intro', // intro
			'admin_page_advanced-ads-import-export', // import & export
		));

		if( in_array( $screen->id, $advads_pages )){
			return true;
		}

		return false;
	}


	/**
	 * get action from the params
	 *
	 * @since 1.0.0
	 */
	public function current_action() {
		if ( isset($_REQUEST['action']) && -1 != $_REQUEST['action'] ) {
			return $_REQUEST['action'];
		}

		return false;
	}

        
    /**
     *  get DateTimeZone object for the WP installation
     */
    public static function get_wp_timezone() {
        $_time_zone = get_option( 'timezone_string' );
        $time_zone = new DateTimeZone( 'UTC' );
        if ( $_time_zone ) {
            $time_zone = new DateTimeZone( $_time_zone );
        } else {
            $gmt_offset = floatval( get_option( 'gmt_offset' ) );
            $sign = ( 0 > $gmt_offset )? '-' : '+';
            $int = floor( abs( $gmt_offset ) );
            $frac = abs( $gmt_offset ) - $int;
            
            $gmt = '';
            if ( $gmt_offset ) {
                $gmt .= $sign . zeroise( $int, 2 ) . ':' . zeroise( 60 * $frac, 2 );
                $time_zone = date_create( '2017-10-01T12:00:00' . $gmt )->getTimezone();
            }
            
        }
        return $time_zone;
    }
    
    /**
     *  get literal expression of timezone
     */
    public static function timezone_get_name( $DTZ ) {
        if ( $DTZ instanceof DateTimeZone ) {
            $TZ = timezone_name_get( $DTZ );
            if ( 'UTC' == $TZ ) {
                return 'UTC+0';
            }
            if ( false === strpos( $TZ, '/' ) ) {
                $TZ = 'UTC' . $TZ;
            } else {
                $TZ = sprintf( __( 'time of %s', 'advanced-ads' ), $TZ );
            }
            return $TZ;
        }
        return 'UTC+0';
    }

	/**
	 * initiate the admin notices class
	 *
	 * @since 1.5.3
	 */
	public function admin_notices(){
		// display ad block warning to everyone who can edit ads
		if( current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_edit_ads') ) ) {
			if ( $this->screen_belongs_to_advanced_ads() ){
				include ADVADS_BASE_PATH . 'admin/views/notices/adblock.php';
				include ADVADS_BASE_PATH . 'admin/views/notices/jqueryui_error.php';
			}
		}
		
		if( current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_edit_ads') ) ) {
			$this->notices = Advanced_Ads_Admin_Notices::get_instance()->notices;
			Advanced_Ads_Admin_Notices::get_instance()->display_notices();
		}
	}
	
	/**
	 * add links to the plugins list
	 *
	 * @since 1.6.14
	 * @param arr $links array of links for the plugins, adapted when the current plugin is found.
	 * @param str $file  the filename for the current plugin, which the filter loops through.
	 * @return array $links
	 */
	public function add_plugin_links( $links ) {
		// add link to settings
		//$settings_link = '<a href="' . admin_url( 'admin.php?page=advanced_ads&page=advanced-ads-settings' ) . '">' . __( 'Settings', 'advanced-ads' ) . '</a>';
		//array_unshift( $links, $settings_link );

		// add link to support page
		$support_link = '<a href="' . esc_url( admin_url( 'admin.php?page=advanced-ads-support' ) ) . '">' . __( 'Support', 'advanced-ads' ) . '</a>';
		array_unshift( $links, $support_link );

		// add link to add-ons
		$extend_link = '<a href="' . ADVADS_URL . 'add-ons/#utm_source=advanced-ads&utm_medium=link&utm_campaign=plugin-page" target="_blank">' . __( 'Add-Ons', 'advanced-ads' ) . '</a>';
		array_unshift( $links, $extend_link );
		
		return $links;
	}
	
	/**
	 * display deactivation logic on plugins page
	 * 
	 * @since 1.7.14
	 */
	public function add_deactivation_logic(){
	    
		$screen = get_current_screen();
		if ( ! isset( $screen->id ) || ! in_array( $screen->id, array( 'plugins', 'plugins-network' ), true ) ) {
			return;
		}
		
		$current_user = wp_get_current_user();
		if ( !($current_user instanceof WP_User) ){
		    $from = '';
		    $email = '';
		} else {
		    $from = $current_user->user_nicename . ' <' . trim( $current_user->user_email ) . '>';
		    $email = $current_user->user_email;
		}
		
		include ADVADS_BASE_PATH . 'admin/views/feedback-disable.php';		
	}
	
	/**
	 * send feedback via email
	 * 
	 * @since 1.7.14
	 */
	public function send_feedback(){
	    
		if ( isset( $_POST['formdata'] ) ) {
			parse_str( $_POST['formdata'], $form );
		}
		
		$text = '';
		if( isset( $form[ 'advanced_ads_disable_text' ] ) ){
		    $text = implode( "\n\r", $form[ 'advanced_ads_disable_text' ] );
		}
		
		// get first version to see if this is a new problem or might be an older on
		$options = Advanced_Ads_Plugin::get_instance()->internal_options();
		$installed = isset( $options['installed'] ) ? date( 'd.m.Y', $options['installed'] ) : '–';
		
		$text .= "\n\n" . home_url() . " ($installed)";
		
		$headers = array();
		
		$from = isset( $form['advanced_ads_disable_from'] ) ? $form['advanced_ads_disable_from'] : '';
		// if an address is given in the form then use that one
		if( isset( $form['advanced_ads_disable_reason'] ) && 'technical issue' === $form['advanced_ads_disable_reason'] 
			&& isset( $form[ 'advanced_ads_disable_reply' ] ) && !empty( $form[ 'advanced_ads_disable_reply_email' ] ) ){
			$from = $current_user->user_nicename . ' <' . trim( $form[ 'advanced_ads_disable_reply_email' ] ) . '>';
			$text .= "\n\n REPLY ALLOWED";
		}
		if( $from ){
			$headers[] = "From: $from";
			$headers[] = "Reply-To: $from";
		}
		
		$subject = isset( $form['advanced_ads_disable_reason'] ) ? $form['advanced_ads_disable_reason'] : '(no reason given)';
	    
		$success = wp_mail( 'improve@wpadvancedads.com', $subject, $text, $headers );
		
		die();
	    
	}
	
	public function tinymce_allow_unsafe_link_target( $mceInit ) {
	    
		// check if we are on the ad edit screen
		if( ! function_exists( 'get_current_screen' ) ){
		    return $mceInit;
		}
		
		$screen = get_current_screen();
		if( isset( $screen->id ) && $screen->id === 'advanced_ads' ) {
			$mceInit['allow_unsafe_link_target'] = true;
		}
		
		return $mceInit;
	}   

}
