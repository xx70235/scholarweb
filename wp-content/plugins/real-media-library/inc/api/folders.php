<?php
use MatthiasWeb\RealMediaLibrary\attachment;
use MatthiasWeb\RealMediaLibrary\general;
use MatthiasWeb\RealMediaLibrary\metadata;
use MatthiasWeb\RealMediaLibrary\order;
use MatthiasWeb\RealMediaLibrary\folder;
use MatthiasWeb\RealMediaLibrary\api;

if (!function_exists('is_rml_folder')) {
    /*
     * Checks, if a given variable is an implementation of the
     * IFolder interface.
     * 
     * @param $obj Object or int (ID)
     * @return boolean
     */
    function is_rml_folder($obj) {
        return is_int($obj) ? is_rml_folder(wp_rml_get_object_by_id($obj)) : $obj instanceof api\IFolder;
    }
}

if (!function_exists('wp_rml_get_parent_id')) {
    /*
     * Get the parent ID of a given folder id.
     * 
     * @param $id The id of the folder, collection, ...
     * @return int or null
     */
    function wp_rml_get_parent_id($id) {
        $folder = wp_rml_get_object_by_id($id);
        return is_rml_folder($folder) ? $folder->getParent() : null;
    }
}

if (!function_exists('wp_rml_objects')) {
    /*
     * Get all available folders, collections, galleries, ...
     * 
     * @return Array of api\IFolder objects
     */
    function wp_rml_objects() {
        return attachment\Structure::getInstance()->getParsed();
    }
}

if (!function_exists('wp_rml_root_childs')) {
    /*
     * Gets the first level childs of the media library.
     * 
     * @return Array of api\IFolder objects
     */
    function wp_rml_root_childs() {
        return attachment\Structure::getInstance()->getTree();
    }
}

if (!function_exists('wp_rml_select_tree')) {
    /*
     * Returns a .rml-root-list with an given tree. The selected folder id is
     * saved automatically in a hidden input type.
     * 
     * @param inputName the name for the hidden input type and the name for the list
     * @param selected the selected folder id (saved also in hidden input type)
     * @param tree Array of api\IFolder objects, default is the root tree
     * @param extraClasses classes for the rml root list container
     * @return Formatted HTML string
     * 
     * Experimental:
     * <strong>Note #1</strong> The select tree has a javascript callback when it
     * is initalized. You can bind it with this snippet:
     * 
     * window.rml.hooks.register("customList", function(obj, $) {
     *       //if (obj.hasClass("my-extra-class")) {
     *            alert(obj.html());
     *       //}
     * });
     * 
     * <strong>Note #2</strong> If you want to use the select tree after a DOM change (ajax,
     * for example: Modal dialog in visual editor) please call the javascript function
     * window.rml.library.customLists() to affect the initalization referred to Note #1.
     * 
     * <strong>Note #3</strong> You can use a sub class of api\IFolder to customize your tree.
     * 
     * @see To see an demo how to use it, have a look at ../inc/admin_footer/sidebar.dummy.php:96
     * @see ../assets/js/library.js:customLists()
     * @see Filters are available for the TreeNode: general\View
     */
    function wp_rml_select_tree($inputName, $selected, $tree = null, $extraClasses = "") {
        $output = '<div class="aio-tree rml-root-list rml-custom-list ' . $extraClasses . '" id="rml-list-' . $inputName . '" data-id="' . $inputName . '">
                <input type="hidden" name="' . $inputName . '" value="' . $selected . '" />
                
                <div class="aio-list-standard">
                    <div class="aio-nodes">
                        ' . attachment\Structure::getInstance()->getView()->treeHTML($selected, $tree, $inputName) . '
                    </div>
                </div>
            </div>';
        return $output;
    }
}

if (!function_exists('wp_rml_create')) {
    /*
     * Creates a folder. At first it checks if a folder in parent already exists.
     * Then it checks if the given type is allowed in the parent.
     * 
     * @param $name String Name of the folder
     * @param $parent int ID of the parent (_wp_rml_root() for root)
     * @param $type integer 0|1|2 @see Folder.class.inc
     * @param $restrictions Restrictions for this folder, see Permissions
     *                      The restrictions of the parent folder are also the restrictions
     *                      for the new folder (restrictions ending with ">").
     * @param $supress_validation Supress the permission validation
     * @param $return_existing_id If true and the folder already exists, then return the ID of the existing folder
     * @return  int (ID) when successfully
     *          array with error strings
     * 
     * It is highly recommenend, to use wp_rml_structure_reset() after you created your folders.
     */
    function wp_rml_create($name, $parent, $type, $restrictions = array(), $supress_validation = false, $return_existing_id = false) {
        return attachment\Structure::getInstance()->createFolder($name, $parent, $type, $restrictions, $supress_validation, $return_existing_id);
    }
}

if (!function_exists('wp_rml_create_or_return_existing_id')) {
    /*
     * Wrapper function for wp_rml_create()
     * 
     * @see wp_rml_create();
     * @return int (ID) of the created OR existing folder
     *         array with errors strings
     */
    function wp_rml_create_or_return_existing_id($name, $parent, $type, $restrictions = array(), $supress_validation = false) {
        return wp_rml_create($name, $parent, $type, $restrictions, $supress_validation, true);
    }
}

if (!function_exists('wp_rml_rename')) {
    /*
     * Renames a folder and then checks, if there is no duplicate folder in the
     * parent folder.
     * 
     * @param $name String New name of the folder
     * @param $id The ID of the folder
     * @param $supress_validation Supress the permission validation
     * @return true or array with error strings
     */
    function wp_rml_rename($name, $id, $supress_validation = false) {
        return attachment\Structure::getInstance()->renameFolder($name, $id, $supress_validation);
    }
}

if (!function_exists('wp_rml_delete')) {
    /*
     * Deletes a folder by ID.
     * 
     * @param $id The ID of the folder
     * @param $supress_validation Supress the permission validation
     * @return true or array with error string
     */
    function wp_rml_delete($id, $supress_validation = false) {
        return attachment\Structure::getInstance()->deleteFolder($id, $supress_validation);
    }
}

if (!function_exists('wp_rml_update_count')) {
    /*
     * Handle the count cache for the folders. This should avoid
     * a lack SQL subquery which loads data from the postmeta table.
     * 
     * @param $folders Array of folders ID, if null then all folders with cnt = NULL are updated
     * @param $attachments Array of attachments ID, is merged with $folders if given
     */
    function wp_rml_update_count($folders = null, $attachments = null) {
        attachment\CountCache::getInstance()->updateCountCache($folders, $attachments);
    }
}

if (!function_exists('wp_rml_dropdown')) {
    /*
     * This functions returns a HTML formatted string which contains
     * <options> elements with all folders, collections and galleries.
     * 
     * @param $selected The selected item
     *              "":             "All Files"
     *              _wp_rml_root(): "Root"
     *              int:            Folder ID
     * @param $disabled array Defines, which folder types are disabled (@see ./real-media-library.php for Constant-Types)
     *                        Default disabled is RML_TYPE_COLLECTION
     * @param $useAll boolean Defines, if "All Files" should be showed
     * @return String
     */
    function wp_rml_dropdown($selected, $disabled, $useAll = true) {
        return attachment\Structure::getInstance()->optionsFasade($selected, $disabled, $useAll);
    }
}

if (!function_exists('wp_rml_dropdown_collection')) {
    /*
     * This functions returns a HTML formatted string which contains
     * <options> elements with all folders, collections and galleries.
     * Note: Only COLLECTIONS are SELECTABLE!
     * 
     * @param $selected The selected item
     *              "":             "All Files"
     *              _wp_rml_root(): "Root"
     *              int:            Folder ID
     * @return String
     */
    function wp_rml_dropdown_collection($selected) {
        return wp_rml_dropdown($selected, array(0,2,3,4));
    }
}

if (!function_exists('wp_rml_dropdown_gallery')) {
    /*
     * This functions returns a HTML formatted string which contains
     * <options> elements with all folders, collections and galleries.
     * Note: Only GALLERIES are SELECTABLE!
     * 
     * @param $selected The selected item
     *              "":             "All Files"
     *              _wp_rml_root(): "Root"
     *              int:            Folder ID
     * @return String
     */
    function wp_rml_dropdown_gallery($selected) {
        return wp_rml_dropdown($selected, array(0,1,3,4));
    }
}

if (!function_exists('wp_rml_dropdown_gallery_or_collection')) {
    /*
     * This functions returns a HTML formatted string which contains
     * <options> elements with all folders, collections and galleries.
     * Note: Only GALLERIES AND COLLECTIONS are SELECTABLE!
     * 
     * @param $selected The selected item
     *              "":             "All Files"
     *              _wp_rml_root(): "Root"
     *              int:            Folder ID
     * @return String
     */
    function wp_rml_dropdown_gallery_or_collection($selected) {
        return wp_rml_dropdown($selected, array(0,3,4));
    }
}

if (!function_exists('wp_rml_is_type')) {
    /*
     * Determines, if a Folder is a special folder type.
     * 
     * @param $folder folder\Creatable or int
     * @param $allowed array Defines, which folder types are allowed (@see ./real-media-library.php for Constant-Types) 
     * @return boolean
     */
    function wp_rml_is_type($folder, $allowed) {
        if (!is_rml_folder($folder)) {
            $folder = wp_rml_get_by_id($folder, null, true);
            
            if (!is_rml_folder($folder)) {
                return false;
            }
        }
        
        return in_array($folder->getType(), $allowed);
    }
}

if (!function_exists('wp_rml_get_object_by_id')) {
    /*
     * A shortcut function for the wp_rml_get_by_id() function that ensures, that 
     * a Creatable object is returned. For -1 the folder\Root instance is returned.
     * 
     * @return Creatable or null
     * @see wp_rml_get_by_id()
     */
    function wp_rml_get_object_by_id($id, $allowed = null) {
        return wp_rml_get_by_id($id, $allowed, true, false);
    }
}

if (!function_exists('wp_rml_get_by_id')) {
    /*
     * This functions checks if a specific folder exists by ID and is
     * a given allowed RML Folder Type. If the given folder is _wp_rml_root() you will
     * get the first level folders.
     * 
     * @param $id int Folder ID
     * @param $allowed array Defines, which folder types are allowed (@see ./real-media-library.php for Constant-Types)
     *                       If it is null, all folder types are allowed.
     * @param $mustBeFolderObject Defines if the function may return the wp_rml_root_childs result
     * @param $nullForRoot If set to false and $id == -1 then the Root instance is returned
     * @return api\IFolder object or NULL
     * 
     * Note: The Folder ID must be a valid Folder ID, not Root and "All Files" => FolderID > _wp_rml_root()
     */
    function wp_rml_get_by_id($id, $allowed = null, $mustBeFolderObject = false, $nullForRoot = true) {
        if (!is_numeric($id)) {
            return null;
        }
        
        if ($mustBeFolderObject == false && $id == _wp_rml_root()) {
            return wp_rml_root_childs();
        }
        
        $folder = attachment\Structure::getInstance()->getFolderByID($id, $nullForRoot);
        
        if (is_array($allowed)) {
            if (!wp_rml_is_type($folder, $allowed)) {
                return null;
            }
        }
        
        return $folder;
    }
}

if (!function_exists('wp_rml_get_by_absolute_path')) {
    /*
     * This functions checks if a specific folder exists by absolute path and is
     * a given allowed RML Folder Type.
     * 
     * @param $path string Folder Absolute Path
     * @param $allowed array Defines, which folder types are allowed (@see ./real-media-library.php for Constant-Types)
     *                       If it is null, all folder types are allowed.
     * @return api\IFolder object or NULL
     * 
     * Note: The absolute path may not be "/" (Root).
     */
    function wp_rml_get_by_absolute_path($path, $allowed = null) {
        $folder = attachment\Structure::getInstance()->getFolderByAbsolutePath($path);
        
        if (is_array($allowed)) {
            if (!wp_rml_is_type($folder, $allowed)) {
                return null;
            }
        }
        
        return $folder;
    }
}

if (!function_exists('wp_rml_register_creatable')) {
    /*
     * Register a new folder type for RML. It does not check if the creatable type
     * is already registered.
     * 
     * @param $qualified The qualified name of the class representing the creatable
     * @param $type The type of the creatable. It must be the same as in yourClass::getType is returned
     * @param $onRegister Calls the onRegister function
     */
    function wp_rml_register_creatable($qualified, $type, $onRegister = false) {
        attachment\Structure::getInstance()->registerCreatable($qualified, $type, $onRegister);
    }
}

if (!function_exists('_wp_rml_root')) {
    /*
     * Get the parent root folder for a given blog id.
     * 
     * @filter RML/ParentRoot
     * @example
        add_action("init", function() {
        	add_filter("RML/ParentRoot", function($root, $blogId) {
        		$current_user = wp_get_current_user();
        		return $current_user->ID;
        	}, 10, 2);
        }, 1);
     */
    function _wp_rml_root() {
        $result = apply_filters("RML/ParentRoot", -1, get_current_blog_id());
        return $result;
    }
}

if (!function_exists('_wp_rml_sanitize')) {
    /*
     * Sanitize to a valid folder name for a given folder name. If the
     * passed folder name contains only unvalide characters, then it falls
     * back to the base64_encode.
     * 
     * @param $name The name of the folder
     * @return String
     */
    function _wp_rml_sanitize($name) {
        $slug = sanitize_file_name($name);
        return empty($slug) ? base64_encode($name) : $slug;
    }
}

if (!function_exists('wp_rml_structure_reset')) {
    /*
     * @see Structure.class.php::resetData
     * 
     * ATTENTION: This function will be declared as deprecated soon, because it is
     * planned to automatically reset the structure data / reset folder data (lazy loading 
     * of Folder objects).
     */
    function wp_rml_structure_reset($root = null, $fetchData = true) {
        attachment\Structure::getInstance()->resetData($root, $fetchData);
    }
}