<?php
namespace MatthiasWeb\RealMediaLibrary\folder;
use MatthiasWeb\RealMediaLibrary\attachment;
use MatthiasWeb\RealMediaLibrary\general;
use MatthiasWeb\RealMediaLibrary\order;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
 * This class creates a gallery object.
 * 
 * @see Creatable
 * @type "2" (2 for backwards-compatibility)
 */
class Gallery extends order\Sortable {
    /*
     * Simply check, if an id can be inserted in this folder. If something is
     * wrong with the id, please throw an exception!
     * 
     * @param $id The id
     * @throws Exception
     */
    protected function singleCheckInsert($id) {
        if (!wp_attachment_is_image($id)) {
            throw new \Exception(__("You can only move images to a gallery.", RML_TD));
        }
    }
    
    /*
     * Creates an instance for this folder type if the folder is newly
     * created and should be persisted.
     * 
     * @see Creatable::persist
     * @see Creatable::create
     * @throws Exception when something went wrong by creating
     * @return Gallery
     */
    public static function create($rowData) {
        $result = new Gallery($rowData->id);
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
     * @return Gallery
     */
    public static function instance($rowData) {
        return new Gallery($rowData->id, $rowData->parent, $rowData->name, $rowData->slug, $rowData->absolute, 
                            $rowData->ord, $rowData->cnt_result, $rowData);
    }
    
    /*
     * Checks, if a children type is allowed here.
     * 
     * @return Array with allowed types or TRUE for all types allowed
     */
    public function getAllowedChildrenTypes() {
        return array();
    }
    
    public function getType() {
        return RML_TYPE_GALLERY;
    }
}

?>