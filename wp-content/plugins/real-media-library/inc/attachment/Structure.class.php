<?php
namespace MatthiasWeb\RealMediaLibrary\attachment;
use MatthiasWeb\RealMediaLibrary\general;
use MatthiasWeb\RealMediaLibrary\folder;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
 * This class handles all hooks and functions for the structur.
 * If something will print out, this is a fasade-wrapper function
 * for the class general\View (stored in private $view).
 */
class Structure extends general\Base {
    
    private static $me = null;
    
    /*
     * The structure should be accessible within one
     * or more blogs. For those purposes use the wp standard
     * method switch_to_blog();
     * 
     * This is an array of Structure objects.
     * 
     * @see self::getInstance()
     */
    private static $blogs = array();
    
    /*
     * The root folder ID. Can only be set by the constructor!
     * 
     * @see self::newInstance()
     */
    private $root;
    
    /*
     * Array of Databased readed rows
     */
    private $rows;
    /*
     * $rows formed to folder\Folder objects
     */
    private $parsed;
    /*
     * Tree of folder\Folder objects. @see $childrens of folder\Folder object.
     */
    private $tree;
    private $view;
    
    /*
     * Checks, if the folder tree is already loaded and do the initial
     * load if needed by an function. So, RML can guarantee lazy loading.
     * 
     * @see this::initialLoad()
     */
    private $hasInitialLoad = false;
    
    /*
     * @see this::registerCreatable()
     */
    private $creatables = array();
    
    /*
     * C'tor
     * When starting the structure by singleton getInstance()
     * then fetch all folders with their parents.
     * 
     * @param $root The root folder
     * @see this::resetData()
     */
    private function __construct($root = null) {
        $this->view = new general\View($this);
        $this->resetData($root, false);
    }
    
    /*
     * Checks, if there is some data and parse it.
     */
    public function initialLoad() {
        if (!$this->hasInitialLoad) {
            $this->hasInitialLoad = true;
            $this->resetData($this->root);
        }
    }
    
    /*
     * Resets the data of the structure.
     * 
     * @param $root The root folder
     * @param $fetchData Determine, if the data should be refetched
     * @api wp_rml_structure_reset
     * @see this::initialLoad()
     */
    public function resetData($root = null, $fetchData = true) {
        $this->root = $root === null ? _wp_rml_root() : $root;
        $this->rows = array();
        $this->parsed = array();
        $this->tree = array();
    
        if ($fetchData) {
            $this->fetch();
        }else{
            $this->hasInitialLoad = false;
        }
    }
    
    /*
     * Fetching all available folders into an array.
     * 
     * @sql example for hierarchical
        SELECT  id,
                name,
                parent 
        FROM    (SELECT * FROM wp_realmedialibrary
                 ORDER BY parent, id) AS products_sorted,
                (SELECT @pv := 'FOLDER_ID') AS tn
        WHERE   FIND_IN_SET(parent, @pv) > 0
        AND     @pv := concat(@pv, ',', id)
     */
    private function fetch() {
        global $wpdb;
        
        $table_name = general\Core::getInstance()->getTableName();
        
        // SELECT fields
        $fields = join(", ", apply_filters("RML/Tree/SQLStatement/SELECT", array(
            // The whole row of the folder
            "tn.*",
            // Count images for this folder
            "IFNULL(tn.cnt, (SELECT COUNT(*)
            	FROM " . general\Core::getInstance()->getTableName("posts") . " AS rmlpostscnt
            	WHERE rmlpostscnt.fid = tn.id
            )) AS cnt_result"
        )));
        
        // JOINS
        $joins = join(" ", apply_filters("RML/Tree/SQLStatement/JOIN", array()));

        // Full SQL statement filter
        $sqlStatement = apply_filters("RML/Tree/SQLStatement", array("
            SELECT " . $fields . "
            FROM $table_name AS tn
            $joins
            ORDER BY parent, ord
        ", $table_name));
        
        $this->rows = $wpdb->get_results($sqlStatement[0]);
        $this->rows = apply_filters("RML/Tree/SQLRows",  $this->rows);
        
        $this->parse();
    }
    
    /*
     * This functions parses the readed rows into folder objects.
     * It also handles the `cnt` cache for the attachments in this folder.
     * 
     * @see CountCache::updateCountCache
     */
    private function parse() {
        if (!empty($this->rows)) {
            $noCntCache = false;
            foreach ($this->rows as $key => $value) {
                // Check for image cache
                if (is_null($value->cnt)) {
                    $noCntCache = true;
                }
                
                // Prepare the data types
                $this->rows[$key]->id = intval($value->id);
                $this->rows[$key]->parent = intval($value->parent);
                $this->rows[$key]->ord = intval($value->ord);
                $this->rows[$key]->cnt_result = intval($value->cnt_result);
                
                // Craetable instance
                if (isset($this->creatables[$value->type])) {
                    $result = call_user_func_array(array($this->creatables[$value->type], 'instance'), array($value));
                    if (is_rml_folder($result)) {
                        $this->parsed[] = $result;
                    }
                }
            }
            
            if ($noCntCache) {
                CountCache::getInstance()->updateCountCache();
            }
        }
        
        // Create the tree
        $folder = null;
        foreach($this->parsed as $key => $category){
            $parent = $category->getParent();
            if ($parent > -1) {
                $folder = $this->getFolderByID($parent);
                if ($folder !== null) {
                    $folder->addChildren($category);
                }
            }
        }
        
        $cats_tree = array();
        foreach ($this->parsed as $category) {
            if ($category->getParent() <= -1) {
                $cats_tree[] = $category;
            }
        }
        $this->tree = $cats_tree;
    }
    
    /*
     * @api wp_rml_create()
     */
    public function createFolder($name, $parent, $type, $restrictions = array(), $supress_validation = false, $return_existing_id = false) {
        $this->debug("Try to create folder with name '$name'...", __METHOD__);
        
        try {
            // Create restrictions from parent
            if ($parent >= 0) {
                $parentFolder = wp_rml_get_by_id($parent, null, true);
                if (is_rml_folder($parentFolder)) {
                    $parentRestrictions = $parentFolder->getRestrictions();
                    foreach ($parentRestrictions as $parentRestriction) {
                        if (substr($parentRestriction, -1) == '>') {
                            $restrictions[] = $parentRestriction;
                        }
                    }
                }
            }
            
            // Create the new instance for the folder
            $result = call_user_func_array(array($this->creatables[$type], 'create'), array( (object) (array(
                "id" => -1,
                "parent" => intval($parent),
                "name" => $name,
                "restrictions" => $restrictions,
                "supress_validation" => $supress_validation
            )) ));
            
            // Check if other fails are counted
            if (!$supress_validation) {
                $errors = apply_filters("RML/Validate/Create", array(), $name, $parent, $type);
                if (count($errors) > 0) {
                    throw new \Exception(implode(" ", $errors));
                }
            }
            
            // Persist it!
            return $result->persist();
        } catch (general\FolderAlreadyExistsException $e) {
            $error = $e;
            if ($return_existing_id) {
                $existing_id = $e->getFolder()->getId();
                $this->debug("Found folder with same name, now return the already existing folder id $existing_id...", __METHOD__);
                return $existing_id;
            }
        } catch (\Exception $e) {
            $error = $e;
        }
        
        $this->debug("Error: " . $e->getMessage(), __METHOD__);
        return array($e->getMessage());
    }
    
    /*
     * @api wp_rml_rename()
     */
    public function renameFolder($name, $id, $supress_validation = false) {
        try {
            $folder = wp_rml_get_by_id($id, null, true);
            if ($folder !== null) {
                $folder->setName($name, $supress_validation);
            }else{
                throw new \Exception(__("The given folder does not exist or you can not rename this folder.", RML_TD));
            }
            return true;
        }catch (\Exception $e) {
            $this->debug("Error:" . $e->getMessage(), __METHOD__);
            return array($e->getMessage());
        }
    }
    
    /*
     * @api wp_rml_delete()
     */
    public function deleteFolder($id, $supress_validation = false) {
        $this->debug("Try to delete folder id $id...", __METHOD__);
        
        try {
            $folder = $this->getFolderByID($id);
            
            if ($folder !== null) {
                // Check if other fails are counted
                if ($supress_validation === false) {
                    $errors = apply_filters("RML/Validate/Delete", array(), $id, $folder);
                    if (count($errors) > 0) {
                        throw new \Exception(implode(" ", $errors));
                    }
                }
                
                // Delete folder
                do_action("RML/Folder/Predeletion", $folder);
                global $wpdb;
                $table_name = general\Core::getInstance()->getTableName();
                $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %d", $id));
                
                $table_name = general\Core::getInstance()->getTableName("posts");
                $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE fid = %d", $id));
                
                // Do the action
                $rowData = $folder->getRowData();
                $this->resetData();
                do_action("RML/Folder/Deleted", $id, $rowData);
                $this->debug("Successfully deleted folder id $id", __METHOD__);
                
                return true;
            }else{
                throw new \Exception(__("The given folder does not exist.", RML_TD));
            }
        }catch (\Exception $e) {
            $this->debug("Error: " . $e->getMessage(), __METHOD__);
            return array($e->getMessage());
        }
    }
    
    /*
     * Checks, if a Parents folder has a child with the given slug.
     * 
     * @param $parentID Id of the Parent folder (_wp_rml_root() for root)
     * @param $slug String Slug or Name of folder
     * @param $isSlug boolean Set it to false, if the slug is not santizied
     * @param $returnObject If set to true and a children with this name is found, then return the object for this folder
     * @return boolean true/false
     * @see Creatable::hasChildren
     */
    public function hasChildren($parentID, $slug, $isSlug = true, $returnObject = false) {
        $parent = wp_rml_get_object_by_id($parentID);
        return $parent->hasChildren($slug, $isSlug, $returnObject);
    }
    
    public function optionsFasade($selected, $disabled, $useAll = true) {
        return $this->view->optionsHTML($selected, null, "", "&nbsp;&nbsp;", $useAll, $disabled);
    }
    
    /*
     * @api wp_rml_register_creatable()
     */
    public function registerCreatable($qualified, $type, $onRegister = false) {
        $this->creatables[$type] = $qualified;
        if ($onRegister) {
            call_user_func(array($qualified, 'onRegister'));
        }
    }
    
    /*
     * Get a folder by ID.
     * 
     * @param $id The id of the folder
     * @param $nullForRoot If set to false and $id == -1 then the Root instance is returned
     * @return Creatable
     */
    public function getFolderByID($id, $nullForRoot = true) {
        if (!$nullForRoot && $id == -1) {
            return folder\Root::getInstance();
        }
        
        foreach ($this->getParsed() as $folder) {
            if ($folder->getId() == $id) {
                return $folder;
            }
        }
        return null;
    }
    
    public function getFolderByAbsolutePath($slug) {
        $slug = trim($slug, '/');
        foreach ($this->getParsed() as $folder) {
            if (strtolower($folder->getAbsolutePath()) == strtolower($slug)) {
                return $folder;
            }
        }
        return null;
    }
    
    public function getBreadcrumbByID($id) {
        $folder = $this->getFolderByID($id);
        if ($folder === null) {
            return null;
        }
        
        $return = array($folder);
        
        while (true) {
            if ($folder->getParent() > 0) {
                $folder = $this->getFolderByID($folder->getParent());
                if ($folder === null) {
                    return null;
                }else{
                    $return[] = $folder;
                }
            }else{
                break;
            }
        }
        
        return array_reverse($return);
    }
    
    public function getRows() {
        $this->initialLoad();
        return $this->rows;
    }
    
    public function getParsed() {
        $this->initialLoad();
        return $this->parsed;
    }
    
    public function getTree() {
        $this->initialLoad();
        return $this->tree;
    }
    
    public function getCntAttachments() {
        return wp_count_posts('attachment')->inherit;
    }
    
    public function getCntRoot() {
        $cnt = 0;
        foreach ($this->getParsed() as $folder) {
            $cnt += $folder->getCnt();
        }
        $result = $this->getCntAttachments() - $cnt;
        return $result >= 0 ? $result : 0;
    }
    
    public function getView() {
        return $this->view;
    }
    
    /*
     * Get or create an instance of the Structure object
     * depending on the current blog id.
     * 
     * @see self::$blogs
     */
    public static function getInstance() {
        $bid = get_current_blog_id();
        if (!isset(self::$blogs[$bid])) {
            self::$blogs[$bid] = self::newInstance();
        }
        return self::$blogs[$bid];
    }
    
    public static function newInstance($root = null) {
        return new Structure($root);
    }
}

?>