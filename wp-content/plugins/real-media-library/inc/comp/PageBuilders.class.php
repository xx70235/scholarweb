<?php
namespace MatthiasWeb\RealMediaLibrary\comp;
use MatthiasWeb\RealMediaLibrary\general;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
 * This class handles the compatibility for general page builders. If a page builder
 * has more compatibility options, please see / create another compatibility class.
 */
class PageBuilders extends general\Base {
    
    private static $me = null;
    
    /*
     * C'tor
     */
    private function __construct($root = null) {
        // Silence is golden.
    }
    
    public function init() {
        if (class_exists("Tatsu_Builder")) {
            $this->oshine_tatsu_builder();
        }
    }
    
    /**
     * OSHINE TATSU PAGE BUILDER
     * https://themeforest.net/item/oshine-creative-multipurpose-wordpress-theme/9545812
     * 
     * The tatsu builder needs some custom CSS.
     */
    private function oshine_tatsu_builder() {
        add_action('tatsu_builder_head',                            array(general\Backend::getInstance(), 'admin_enqueue_scripts') );
        add_action('tatsu_builder_footer',                          array(general\Backend::getInstance(), 'admin_footer'), 11);
        add_action('tatsu_builder_footer',                          array($this, 'oshine_tatsu_builder_footer'), 9);
    }
    public function oshine_tatsu_builder_footer() {
        echo '<style>.rml-container .clear {
	clear: both;
}
.rml-container .aio-expander {
	top: -1px;
	left: -6px;
}
.rml-container .aio-list-standard a {
	padding: 5px 10px 5px 17px;
}</style>';
    }
    
    public static function getInstance() {
        if (self::$me == null) {
            self::$me = new PageBuilders();
        }
        return self::$me;
    }
}

?>