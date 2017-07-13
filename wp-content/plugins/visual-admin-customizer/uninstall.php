<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

delete_site_option('ws_vac_customizations');
delete_site_option('ws_vac_settings');