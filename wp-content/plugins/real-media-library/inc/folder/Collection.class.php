<?php
namespace MatthiasWeb\RealMediaLibrary\folder;
use MatthiasWeb\RealMediaLibrary\attachment;
use MatthiasWeb\RealMediaLibrary\general;
use MatthiasWeb\RealMediaLibrary\order;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
 * This class creates a collection object.
 * 
 * @see Creatable
 * @type "1" (1 for backwards-compatibility)
 */
class Collection extends order\Sortable {
    /*
     * Insert an amount of post ID's (attachments) to this folder.
     * 
     * @param $ids Array of post ids
     * @param $supress_validation Supress the permission validation
     * @param $isShortcut Determines, if the ID's are copies
     * @throws Exception
     * @return true
     */
    public function insert($ids, $supress_validation = false, $isShortcut = false) {
        throw new \Exception(__("A collection can contain no files.", RML_TD));
    }
    
    /*
     * Creates an instance for this folder type if the folder is newly
     * created and should be persisted.
     * 
     * @see Creatable::persist
     * @see Creatable::create
     * @throws Exception when something went wrong by creating
     * @return Collection
     */
    public static function create($rowData) {
        $result = new Collection($rowData->id);
        $result->setName($rowData->name, $rowData->supress_validation);
        $result->setParent($rowData->parent);
        $result->setRestrictions($rowData->restrictions);
        return $result;
    }
    
    /*
     * Creates an instance for this folder type if the folder is loaded
     * for the tree and already exists.
     * 
     * @see Creatable::instance
     * @return Collection
     */
    public static function instance($rowData) {
        return new Collection($rowData->id, $rowData->parent, $rowData->name, $rowData->slug, $rowData->absolute, 
                            $rowData->ord, $rowData->cnt_result, $rowData);
    }
    
    /*
     * Checks, if a children type is allowed here.
     * 
     * @return Array with allowed types or TRUE for all types allowed
     */
    public function getAllowedChildrenTypes() {
        return array(RML_TYPE_GALLERY, RML_TYPE_COLLECTION);
    }
    
    public function getType() {
        return RML_TYPE_COLLECTION;
    }
}

?>