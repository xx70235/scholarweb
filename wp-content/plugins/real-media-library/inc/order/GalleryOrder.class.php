<?php
namespace MatthiasWeb\RealMediaLibrary\order;
use MatthiasWeb\RealMediaLibrary\general;
use MatthiasWeb\RealMediaLibrary\metadata;
use MatthiasWeb\RealMediaLibrary\api;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
 * Implements a order by field.
 * 
 * @see interface IMetadata for more details
 */
class GalleryOrder extends general\Base implements api\IMetadata {
    
    static $cachedOrders = null;
    
    /*
     * Start to order the given folder by a given order type.
     * 
     * @param $fid The folder id
     * @param $orderby The ordertype key
     * @return boolean
     * @see this::getAvailableOrders
     */
    public static function order($fid, $orderby) {
        $orders = self::getAvailableOrders();
        $core = general\Core::getInstance();
        $core->debug("Try to order the folder $fid by $orderby...", __METHOD__);
        if (in_array($orderby, array_keys($orders))) {
            global $wpdb;
            
            // Get order
            $split = explode("_", $orderby);
            $order = $orders[$orderby];
            $direction = $split[1];
            $table_name = general\Core::getInstance()->getTableName("posts");
            
            // Run SQL
            $sql = $wpdb->prepare("UPDATE $table_name AS rmlo2
                LEFT JOIN (
                	SELECT @rownum := @rownum + 1 AS nr, t.ID
                	FROM ( SELECT wp.ID
                		FROM $table_name AS rmlo
                		INNER JOIN $wpdb->posts AS wp ON rmlo.attachment = wp.id AND wp.post_type = \"attachment\"
                		WHERE rmlo.fid = %d
                		ORDER BY " . $order["sqlOrder"] . " $direction ) AS t, (SELECT @rownum := 0) AS r
                ) AS rmlonew ON rmlo2.attachment = rmlonew.ID
                SET rmlo2.nr = rmlonew.nr
                WHERE rmlo2.fid = %d", $fid, $fid);
            $wpdb->query($wpdb->prepare("UPDATE " . $core->getTableName() . " SET contentCustomOrder=1 WHERE id = %d", $fid));
            $wpdb->query($sql);
            
            // Save in the metadata
            update_media_folder_meta($fid, "orderby", $orderby);
            $core->debug("Successfully ordered folder", __METHOD__);
            return true;
        }else{
            $core->debug("'$orderby' is not a valid order...", __METHOD__);
            return false;
        }
    }
    
    /*
     * Get all available order by methods.
     * 
     * @return Localized array
     */
    public static function getAvailableOrders() {
        if (self::$cachedOrders === null) {
            $orders = array(
                "title_asc" => array(
                    "label" => __("Order by title ascending", RML_TD),
                    "sqlOrder" => "wp.post_title"
                ),
                "title_desc" => array(
                    "label" => __("Order by title descending", RML_TD),
                    "sqlOrder" => "wp.post_title"
                ),
                "filename_asc" => array(
                    "label" => __("Order by filename ascending", RML_TD),
                    "sqlOrder" => "SUBSTRING_INDEX(wp.guid, '/', -1)"
                ),
                "filename_desc" => array(
                    "label" => __("Order by filename descending", RML_TD),
                    "sqlOrder" => "SUBSTRING_INDEX(wp.guid, '/', -1)"
                ),
                "filenameNat_asc" => array(
                    "label" => __("Natural order by filename ascending", RML_TD),
                    "sqlOrder" => "LENGTH(SUBSTRING_INDEX(wp.guid, '/', -1)), SUBSTRING_INDEX(wp.guid, '/', -1)"
                ),
                "filenameNat_desc" => array(
                    "label" => __("Natural order by filename descending", RML_TD),
                    "sqlOrder" => "LENGTH(SUBSTRING_INDEX(wp.guid, '/', -1)) desc, SUBSTRING_INDEX(wp.guid, '/', -1)"
                ),
                "id_asc" => array(
                    "label" => __("Order by ID ascending", RML_TD),
                    "sqlOrder" => "wp.ID"
                ),
                "id_desc" => array(
                    "label" => __("Order by ID descending", RML_TD),
                    "sqlOrder" => "wp.ID"
                )
            );
            self::$cachedOrders = apply_filters("RML/Order/Orderby", $orders);
        }
        return self::$cachedOrders;
    }
    
    /*
     * The input field.
     *
     * @see interface IMetadata
     */
    public function content($content, $folder) {
        if (is_rml_folder($folder) && $folder->isContentCustomOrderAllowed()) {
            $content .= '<tr>
                <th scope="row">' . __('Order') . '</th>
                <td>
                    <select>';
            
            foreach (self::getAvailableOrders() as $key => $value) {
                $content .= '<option value="' . $key . '">' . $value["label"] . '</option>';
            }
            
            $content .= '
                    </select>
                    <a class="button actionbutton" id="rml-meta-action-order-by" data-nonce-key="attachmentOrderBy" 
                        data-action="rml_attachment_order_by" 
                        data-method="' . $folder->getId() . '" href="#">' . __('Apply', RML_TD) . '</a>
                    <br />
                </td>
            </tr>';
        }
        
        return $content;
    }
    
    /*
     * Save the general infos: Name
     * 
     * @see interface IMetadata
     */
    public function save($response, $folder) {
        // Silence is golden.
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
}