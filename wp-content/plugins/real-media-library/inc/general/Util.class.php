<?php
namespace MatthiasWeb\RealMediaLibrary\general;
use MatthiasWeb\RealMediaLibrary\attachment;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
 * Others functionality for the plugin.
 */
class Util {
	private static $me = null;
	private $nonces = null;
        
    private function __construct() {
        // Silence is golden.
    }
    
    /*
     * Adds nonces to the backend.
     * 
     * @filter RML/Backend/Nonces
     * @filter RML/Backend/Nonces/manage_options
     * @hooked RML/Backend/LocalizeJS
     */
    public function nonces($arr) {
        if ($this->nonces == null) {
            $this->nonces = array(
                "bulkMove" => wp_create_nonce("rmlAjaxBulkMove"),
                "bulkSort" => wp_create_nonce("rmlAjaxBulkSort"),
                "folderCount" => wp_create_nonce("rmlAjaxFolderCount"),
                "folderRename" => wp_create_nonce("rmlAjaxFolderRename"),
                "folderDelete" => wp_create_nonce("rmlAjaxFolderDelete"),
                "folderCreate" => wp_create_nonce("rmlAjaxFolderCreate"),
                "sidebarResize" => wp_create_nonce("rmlAjaxSidebarResize"),
                "treeContent" => wp_create_nonce("rmlAjaxTreeContent"),
                "shortcutInfo" => wp_create_nonce("rmlAjaxShortcutInfo"),
                "migrateDismiss" => wp_create_nonce("rmlAjaxMigrateDismiss")
            );
        }
        
        $this->nonces = apply_filters("RML/Backend/Nonces", $this->nonces);
        
        // Add user orientated nonces
        if (current_user_can("manage_options")) {
            $this->nonces["wipe"] = wp_create_nonce("rmlAjaxWipe");
            $this->nonces = apply_filters("RML/Backend/Nonces/manage_options", $this->nonces);
        }
        
        $arr["nonces"] = $this->nonces;
        return $arr;
    }
    
    /*
     * Checks, if the permission to use a specific AJAX 
     * request is given. It automatically dies the current
     * screen and prints out an error.
     * 
     * @param nonce The nonce to check
     * @param cap The needed capability
     * @private
     */
    public function checkNonce($nonce = false, $cap = "upload_files") {
        if ($nonce !== false) {
            check_ajax_referer($nonce, 'nonce');
        }
        
        if (!current_user_can($cap)) {
            wp_send_json_error(__("Something went wrong."));
        }
    }
    
    /*
     * Query multiple sql statements.
     * 
     * @param mixed sql statements
     */
    public function query() {
        global $wpdb;
        
        if (is_array(func_get_arg(0))) {
            $sqls = func_get_arg(0);
        }else{
            $sqls = func_get_args();
        }
        
        foreach ($sqls as $param) {
            $wpdb->query($param);
        }
    }
    
    public static function getInstance() {
        if (self::$me == null) {
            self::$me = new Util();
        }
        return self::$me;
    }
}

?>