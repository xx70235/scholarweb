<?php
namespace MatthiasWeb\RealMediaLibrary\metadata;
use MatthiasWeb\RealMediaLibrary\general;
use MatthiasWeb\RealMediaLibrary\attachment;
use MatthiasWeb\RealMediaLibrary\api;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
 * Create general functionality for the custom
 * folder fields.
 * 
 * For an example see the function-doc of this::content_general
 * and this::save_general
 * 
 * @see inc/api/meta.php
 * @see interface IMetadata for more details
 */
class Meta implements api\IMetadata {
    
    private static $me = null;
    private $view = null;
    private $boxes = array();

    private function __construct() {
        // Add our folder meta table to wpdb
        global $wpdb;
        if (!isset($wpdb->realmedialibrary_meta)) {
            $wpdb->realmedialibrarymeta = general\Core::tableName("meta");
        }
        
        $this->view = attachment\Structure::getInstance()->getView();
    }
    
    /*
     * The general custom fields.
     *
     * @see interface IMetadata
     */
    public function content($content, $folder) {
        if ($folder !== null) {
            $type = $folder->getType();
            $content .= '<tr>
                <th scope="row">' . __('Name', RML_TD) . '</th>
                <td>
                    <input name="name" type="text" value="' . $folder->getName() . '" class="regular-text">
                </td>
            </tr>
            <tr class="single-row">
                <th scope="row">' . __('Path', RML_TD) . '</th>
                <td>
                    <label>' . $folder->getPath(' <i class="fa fa-chevron-right" style="font-size: 11px;opacity: 0.5;"></i> ') . '</label>
                </td>
            </tr>';
        }else{
            $type = RML_TYPE_ROOT;
        }
        
        $typeName = $this->view->typeName($type);
        $typeIcon = $type == RML_TYPE_FOLDER ? '</i><i class="fa fa-folder"></i>' : $this->view->typeIcon($type);
        $typeDescription = $this->view->typeDescription($type);
        
        $content .= '<tr class="single-row">
            <th scope="row">' . __('Folder type', RML_TD) . '</th>
            <td>
                <label>' . $typeIcon . ' ' . $typeName . ' <i class="rml-meta-helper" title="' . $typeDescription . '">' . __('What does this mean?', RML_TD) . '</i></label>
            </td>
        </tr>
        <tr class="rml-meta-margin"></tr>';
        
        return $content;
    }
    
    /*
     * Save the general infos: Name
     * 
     * @see interface IMetadata
     */
    public function save($response, $folder) {
        if ($folder !== null && isset($_POST["name"])) {
            $newName = trim($_POST["name"]);
            if ($newName != $folder->getName()) {
                // Rename of normal folder
                $result = wp_rml_rename($newName, $folder->getId());
                
                if ($result === true) {
                    $response["data"]["newSlug"] = $folder->getAbsolutePath(true);
                }else{
                    $response["errors"] = $result;
                }
            }
        }
        
        return $response;
    }
    
    /*
     * The general scripts and styles.
     *
     * @see interface IMetadata
     */
    public function scripts() {
        // Silence is golden.
    }
    
    /*
     * Get content for the form in sweetAlert dialog.
     *
     * @param $fid the folder ID
     * @return HTML formatted string or empty string
     * @see meta.js
     */
    public function prepare_content($fid) {
        $root = _wp_rml_root();
        if ($fid == $root) {
            $folder = null;
            $inputID = $root;
            $type = RML_TYPE_ROOT;
        }else{
            $folder = wp_rml_get_by_id($fid, null, true);
            $inputID = $folder->getId();
            $type = $folder->getType();
            if ($folder === null) {
                return "";
            }
        }
        
        $content = '<form class="rml-meta" method="POST" action=""><table class="form-table" onsubmit="return false;">
            <input type="hidden" name="folderId" value="' . $inputID . '" />
            <input type="hidden" name="folderType" value="' . $type . '" />
            <ul class="rml-meta-errors"></ul>
            <tbody>';
        $content .= apply_filters('RML/Folder/Meta/Content', "", $folder);
        $content .= '</tbody></table></form>';
        return $content;
    }
    
    /*
     * Checks if a meta box is already registered.
     * 
     * @see meta.php
     * @see add_rml_meta_box()
     */
    public function add($name, $instance) {
        if ($this->get($name) !== null) {
            return false;
        }else{
            $this->boxes[$name] = $instance;
            return true;
        }
    }
    
    /*
     * Get the instance for a given meta box name.
     * 
     * @return instance or null
     */
    public function get($name) {
        foreach ($this->boxes as $key => $value) {
            if ($key === $name) {
                return $value;
            }
        }
        return null;
    }
    
    public function exists($name) {
        return $this->get($name) !== null;
    }
    
    /*
     * Delete the metas when a folder is deleted.
     * 
     * @hooked RML/Folder/Deleted
     */
    public function folder_deleted($fid, $oldData) {
        truncate_media_folder_meta($fid);
    }

    public static function getInstance() {
        if (self::$me == null) {
            self::$me = new Meta();
        }
        return self::$me;
    }
}