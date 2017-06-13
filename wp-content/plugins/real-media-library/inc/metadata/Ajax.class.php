<?php
namespace MatthiasWeb\RealMediaLibrary\metadata;
use MatthiasWeb\RealMediaLibrary\general;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
 * Handle ajax requests for the metadata.
 */
class Ajax {
    
    private static $me = null;

    private function __construct() {
        // Silence is golden.
    }
    
    /*
     * Create nonces for the meta ajax requests.
     * 
     * @hooked RML/Backend/Nonces
     */
    public function nonces($nonces) {
        $nonces["metaContent"] = wp_create_nonce("rmlAjaxMetaContent");
        $nonces["metaSave"] = wp_create_nonce("rmlAjaxMetaSave");
        return $nonces;
    }
    
    /*
     * Print out the content for the meta options (custom fields)
     * for a given folder id.
     * 
     * @REQUEST folderId the folder id
     */
    public function wp_ajax_meta_content() {
        // Security checks
        general\Util::getInstance()->checkNonce('rmlAjaxMetaContent');
        
        // Process
        if (isset($_REQUEST["folderId"]) && is_numeric($_REQUEST["folderId"])) {
            echo Meta::getInstance()->prepare_content($_REQUEST["folderId"]);
        }
        wp_die();
    }
    
    /*
     * Save the meta options.
     * 
     * @filter RML/Folder/Meta/Save (Filter: array of errormessages,
     *                               Parameters: $folder object or null = root folder)
     * @REQUEST folderId the folder id
     * @REQUEST Form fields
     */
    public function wp_ajax_meta_save() {
        // Security checks
        general\Util::getInstance()->checkNonce('rmlAjaxMetaSave');
        
        // Process
        if (isset($_REQUEST["folderId"]) && is_numeric($_REQUEST["folderId"])) {
            $fid = $_REQUEST["folderId"];
            if ($fid == _wp_rml_root()) {
                $folder = null;
            }else{
                $folder = wp_rml_get_by_id($fid, null, true);
            }
            
            $response = apply_filters("RML/Folder/Meta/Save", array(), $folder);
        }
        
        if (is_array($response) && isset($response["errors"]) && count($response["errors"]) > 0) {
            wp_send_json_error($response);
        }else{
            if (isset($response["data"]) && is_array($response["data"])) {
                $response = $response["data"];
            }
            wp_send_json_success($response);
        }
    }
    
    public static function getInstance() {
        if (self::$me == null) {
            self::$me = new Ajax();
        }
        return self::$me;
    }
}