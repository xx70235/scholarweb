<?php
include "../../../wp-config.php";
//$callback = (!empty($_SESSION['wp_url_bind'])) ? $_SESSION['wp_url_bind'] : $_SESSION['wp_url_back'];
//if (!$callback) {
	if (isset($_GET['redirect_url'])) {
		$callback = utf8_uri_encode($_GET['redirect_url']);
	} else {
		$callback = get_bloginfo('url');
	} 
//} 
if ($_GET['dl_type'] == 'create') {
	$callback = admin_url('options-general.php?page=wp-connect#basic');
} 

header('Location:' . $callback);

?>