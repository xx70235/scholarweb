<?php
use MatthiasWeb\RealMediaLibrary\attachment;
use MatthiasWeb\RealMediaLibrary\general;
use MatthiasWeb\RealMediaLibrary\metadata;
use MatthiasWeb\RealMediaLibrary\order;
use MatthiasWeb\RealMediaLibrary\folder;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
 * In this file you will find attachment relevant functions.
 * 
 * DEFINED POST TYPES
 * 
 *      define('RML_TYPE_FOLDER', 0);
 *      define('RML_TYPE_COLLECTION', 1);
 *      define('RML_TYPE_GALLERY', 2);
 *
 * ==========================================
 * 
 * Example Szenario #1:
 *   1. User navigates to http://example.com/rml/collection1
 *   2. Use wp_rml_get_by_absolute_path("/collection1") to get the api\IFolder Object
 *   3. (Additional check) $folder->is(RML_TYPE_COLLECTION) to check, if it is a collection.
 *   4. Iterate the childrens with foreach ($folder->getChildren() as $value) { }
 *   5. In collection can only be other collections or galleries.
 * 
 *   6. (Additional check) $value->is(RML_TYPE_GALLERY) to check, if it is a gallery.
 *   7. Fetch the IDs with $value->read();
 * 
 * ==========================================
 * 
 * If you want to use more functions look into the attachment\Structure Class.
 * You easily get it with attachment\Structure::getInstance() (Singleton).
 * 
 * Meaning: Root = Unorganized Pictures
 * 
 * ==========================================
 * 
 * ORDER QUERY
 * 
 * Using the custom order of galleries: In your get_posts()
 * query args use the option "orderby" => "rml" to get the
 * images ordered by custom user order.
 * 
 * ==========================================
 * 
 * CUSTOM FIELDS FOR FOLDERS, COLLECTIONS, GALLERIES, ....
 * 
 * You want create your own custom fields for a rml object?
 * Have a look at themetadata\Metaclass.
 * 
 * @see inc/metadata/Meta.class.php
 */
 
if (!function_exists('wp_rml_get_attachments')) {
    /*
     * @see api\IFolder::read for the other parameters
     * 
     * @param $fid The folder id
     * @return null if folder not exists or array of post ids
     */
    function wp_rml_get_attachments($fid, $order = null, $orderby = null) {
        $folder = wp_rml_get_object_by_id($fid);
        return is_rml_folder($folder) ? $folder->read($order, $orderby) : null;
    }
}

if (!function_exists('wp_attachment_folder')) {
    /*
     * Returns the folder id of an given attachment or more than one attachment (array). If you pass an array
     * as attachment ids, then the default value does not work, only for single queries. When you pass a 
     * shortcut attachment id, the folder id for the shortcut is returned.
     * 
     * @param $attachmentId The attachment ID, if you pass an array you get an array of folder IDs
     * @param $default If no folder was found for this, this value is returned for the attachment
     * @return Folder ID or $default or Array
     */
    function wp_attachment_folder($attachmentId, $default = null) {
        return attachment\Filter::getInstance()->getAttachmentFolder($attachmentId, $default);
    }
}

if (!function_exists('wp_attachment_order_update')) {
    /*
     * Moves an attachment before another given attachment in the order table.
     * 
     * @param $folderId The folder id where the attachment exists
     * @param $attachmentId The attachment which should be moved
     * @param $nextId The attachment next to the currentId, if it is
     *               false the currentId should be moved to the end of table.
     * @param $lastIdInView (optional) If you have pagination, you can pass the last id from this view
     * @return true or array with error strings
     */
    function wp_attachment_order_update($folderId, $attachmentId, $nextId, $lastIdInView = false) {
        // Get folder
        $folder = wp_rml_get_object_by_id($folderId);
        if (is_rml_folder($folder)) {
            // Try to insert
            try {
                $folder->contentOrder($attachmentId, $nextId, $lastIdInView);
                return true;
            }catch (Exception $e) {
                general\Core::getInstance()->debug($e->getMessage(), __FUNCTION__);
                return array($e->getMessage());
            }
        }else{
            general\Core::getInstance()->debug("Could not find the folder with id $folderId", __FUNCTION__);
            return array(__("The given folder was not found.", RML_TD));
        }
    }
}

if (!function_exists('wp_rml_move')) {
    /*
     * Move or create shortcuts of a set of attachments to a specific folder.
     * 
     * If you copy attachments, the action called is also "RML/Item/Move"... but
     * there is a paramter $isShortcut.
     * 
     * @param $to Folder ID
     * @param $ids Array of attachment ids
     * @param $supress_validation Supress the permission validation
     * @param $isShortcut Determines, if the ID's are copies
     * @return true or Array with errors
     * 
     * @see order\Order
     * @see wp_rml_create_shortcuts
     */
    function wp_rml_move($to, $ids, $supress_validation = false, $isShortcut = false) {
        if ($to === false || !is_numeric($to)) { // No movement
            return array(__("The given folder was not found.", RML_TD));
        }
        
        // Get folder
        $folder = wp_rml_get_object_by_id($to);
        if (is_rml_folder($folder)) {
            // Try to insert
            try {
                $folder->insert($ids, $supress_validation, $isShortcut);
                return true;
            }catch (Exception $e) {
                general\Core::getInstance()->debug($e->getMessage(), __FUNCTION__);
                return array($e->getMessage());
            }
        }else{
            general\Core::getInstance()->debug("Could not find the folder with id $to", __FUNCTION__);
            return array(__("The given folder was not found.", RML_TD));
        }
    }
}


/*
 * Shortcut relevant API.
 */


if (!function_exists('wp_rml_create_shortcuts')) {
    /*
     * Link/Copy a set of attachments to a specific folder. When the folder
     * has already a given shortcut, the movement for the given attachment will be skipped.
     * 
     * If you want to receive the last created shortcut ID's you can use the
     * wp_rml_created_shortcuts_last_ids() function.
     * 
     * @param $to Folder ID, if folder not exists then root will be
     * @param $ids Array of attachment ids
     * @param $supress_validation Supress the permission validation
     * @return true or Array with errors
     * 
     * @see wp_rml_move
     */
    function wp_rml_create_shortcuts($to, $ids, $supress_validation = false) {
        return wp_rml_move($to, $ids, $supress_validation, true);
    }
}

if (!function_exists('wp_rml_created_shortcuts_last_ids')) {
    /*
     * If you create shortcuts you will be 
     * 
     * @return int Array
     * @see wp_rml_create_shortcuts
     */
    function wp_rml_created_shortcuts_last_ids() {
        return attachment\Shortcut::getInstance()->getLastIds();
    }
}

if (!function_exists('wp_attachment_ensure_source_file')) {
    /*
     * Checks if a given attachment has already a shortcut in a given folder id
     * or has generelly shortcuts.
     * 
     * @param $post The attachment id or a WP_Post object
     * @return int or WP_Post object
     */
    function wp_attachment_ensure_source_file($post) {
        $isShortcut = wp_attachment_is_shortcut($post, true);
        if ($isShortcut > 0) {
            return $post instanceof \WP_Post ? get_post($isShortcut) : $isShortcut;
        }
        return $post;
    }
}

if (!function_exists('wp_attachment_has_shortcuts')) {
    /*
     * Checks if a given attachment has already a shortcut in a given folder id
     * or has generelly shortcuts.
     * 
     * @param $postId The attachment id
     * @param $fid The folder id, if false, it checks if there generelly exists shortcuts
     * @return boolean
     */
    function wp_attachment_has_shortcuts($postId, $fid = false) {
        global $wpdb;
        $table_name = general\Core::getInstance()->getTableName("posts");
        if ($fid !== false) {
            $sql = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE isShortcut=%d AND fid=%d",
                $postId, $fid);
        }else{
            $sql = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE isShortcut=%d",
                $postId);
        }
        return $wpdb->get_var($sql) > 0;
    }
}

if (!function_exists('wp_attachment_get_shortcuts')) {
    /*
     * Checks if a given attachment ID has shortcut and returns the shortcut IDs as array.
     * 
     * @param $postId The attachment id
     * @param $fid The folder id, if false, it checks if there generelly exists shortcuts
     * @param $extended If true the result is an array with all informations about the associated folder
     * @return array
     */
    function wp_attachment_get_shortcuts($postId, $fid = false, $extended = false) {
        global $wpdb;
        $table_name = general\Core::getInstance()->getTableName("posts");
        $table_name_rml = general\Core::getInstance()->getTableName();
        $join = $extended ? "LEFT JOIN $table_name_rml AS rml ON rml.id = p.fid" : "";
        $select = $extended ? ", rml.*" : "";
        $orderby = $extended ? "ORDER BY name" : "";
        if ($fid !== false) {
            $sql = $wpdb->prepare("SELECT p.attachment, p.fid AS folderId, $select FROM $table_name AS p $join WHERE p.isShortcut=%d AND p.fid=%d $orderby",
                $postId, $fid);
        }else{
            $sql = $wpdb->prepare("SELECT p.attachment, p.fid AS folderId $select FROM $table_name AS p $join WHERE p.isShortcut=%d $orderby",
                $postId);
        }
        return $extended ? $wpdb->get_results($sql, ARRAY_A) : $wpdb->get_col($sql);
    }
}

if (!function_exists('wp_attachment_is_shortcut')) {
    /*
     * Checks if a given attachment is a shortcut, use the $returnSourceId
     * parameter to get the source attachment id.
     * 
     * @param $post The attachment id or a WP_Post object
     * @param $returnSourceId If true, the return will be the source attachment id or 0 if it is no shortcut
     * @return boolean or int or 0
     */
    function wp_attachment_is_shortcut($post, $returnSourceId = false) {
        $guid = get_the_guid($post);
        preg_match('/\?sc=([0-9]+)$/', $guid, $matches);
        if (isset($matches) && is_array($matches) && isset($matches[1])) {
            return $returnSourceId ? (int) $matches[1] : true;
        }else{
            return $returnSourceId ? 0 : false;
        }
    }
}

if (!function_exists('_wp_rml_synchronize_attachment')) {
    /*
     * Synchronizes a result with the realmedialibrary_posts table so on this
     * base there can be made the folder content. It also creates shortcuts, if the
     * given $isShortcut parameter is true.
     * 
     * Do not use this directly, instead use the wp_rml_move function.
     * 
     * @param $postId The post ID
     * @param $fid The folder ID
     * @param $isShortcut true = Is shortcut in the given folder, false = Is no shortcut, mainly in this folder
     * @return boolean
     */
    function _wp_rml_synchronize_attachment($postId, $fid, $isShortcut = false) {
        return attachment\Shortcut::getInstance()->create($postId, $fid, $isShortcut);
    }
}
?>