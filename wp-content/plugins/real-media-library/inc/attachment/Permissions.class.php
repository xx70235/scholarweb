<?php
namespace MatthiasWeb\RealMediaLibrary\attachment;
use MatthiasWeb\RealMediaLibrary\general;
use MatthiasWeb\RealMediaLibrary\folder;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
 * Allowed restrictions for folders:
 * 
 * - par        Restrict to change the parent id
 * - rea        Restrict to rearrange the hierarchical levels of all subfolders (it is downwards
 *              all subfolders!) and can not be inherited
 * - cre        Restrict to create new subfolders
 * - ins        Restrict to insert/upload new attachments, automatically moved to root if upload
 * - ren        Restrict to rename the folder
 * - del        Restrict to delete the folder
 * - mov        Restrict to move files outside the folder
 * 
 * You can append a ">" after each permission so it is inherited in each created subfolder: "cre>", "ins>", ...
 * 
 * @see folder\Folder::$restrictions
 * @see folder\Folder::isRestrictFor
 * @see Structure::createFolder
 */
class Permissions extends general\Base {
    
    private static $me = null;
    
    /*
     * Restrict to insert/upload new attachments, automatically moved to root if upload
     * Restrict to move files outside of a folder
     * 
     * @filter RML/Validate/Insert
     * @see folder\Folder::insert
     */
    public static function insert($errors, $id, $folder) {
        if (is_rml_folder($folder) && $folder->isRestrictFor("ins")) {
            $errors[] = __("You are not allowed to insert files here.", RML_TD);
            return $errors;
        }
        
        // Check if "mov" of current folder is allowed
        $otherFolder = wp_attachment_folder($id);
        if ($otherFolder !== "") {
            $otherFolder = wp_rml_get_by_id($otherFolder, null, true);
            if (is_rml_folder($otherFolder) && $otherFolder->isRestrictFor("mov")) {
                $errors[] = __("You are not allowed to move the file.", RML_TD);
            }
        }
        
        return $errors;
    }
    
    /*
     * Restrict to create new subfolders
     * 
     * @filter RML/Validate/Create
     * @see Structure::createFolder
     */
    public static function create($errors, $name, $parent, $type) {
        $folder = wp_rml_get_by_id($parent, null, true);
        if (is_rml_folder($folder) && $folder->isRestrictFor("cre")) {
            $errors[] = __("You are not allowed to create a subfolder here.", RML_TD);
        }
        return $errors;
    }
    
    /*
     * Restrict to create new subfolders
     * 
     * @filter RML/Validate/Delete
     * @see Structure::deleteFolder
     */
    public static function deleteFolder($errors, $id, $folder) {
        if (is_rml_folder($folder) && $folder->isRestrictFor("del")) {
            $errors[] = __("You are not allowed to delete this folder.", RML_TD);
        }
        return $errors;
    }
    
    /*
     * Restrict to rename a folder
     * 
     * @filter RML/Validate/Rename
     * @see folder\Folder::setName
     */
    public static function setName($errors, $name, $folder) {
        if (is_rml_folder($folder) && $folder->isRestrictFor("ren")) {
            $errors[] = __("You are not allowed to rename this folder.", RML_TD);
        }
        return $errors;
    }
    
    /*
     * Add mandatory classes to the <li> object to apply child permissions.
     * 
     * @filter RML/Folder/TreeNodeLi/Class
     */
    public function liClass($classes, $folder) {
        /*
         * Restrict hierarchical change.
         * 
         * @see $liClasses
         */
        if ($folder->isRestrictFor("rea")) {
            $classes[] = "aio-restrict-hierarchical-change";
        }
        
        //if ($folder->getRestrictionsCount() > 0 && !($folder->getRestrictionsCount() === 1 && $folder->isRestrictFor("rea"))) {
        //    $classes[] = "aio-restrict";
        //}
        return $classes;
    }

    public static function getInstance() {
        if (self::$me == null) {
            self::$me = new Permissions();
        }
        return self::$me;
    }
}

?>