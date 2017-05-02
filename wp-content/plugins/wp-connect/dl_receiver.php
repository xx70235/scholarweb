<?php
include "../../../wp-config.php";
session_start();
if (!empty($_SESSION['source_receiver']) && in_array($_SESSION['source_receiver'], array('wp-connect/go.php', 'wp-connect-advanced/bind.php', 'wp-connect-advanced/login.php'))) {
	$receiver = plugins_url($_SESSION['source_receiver']);
} else {
	$receiver = 'http://open.denglu.cc/receiver';
} 

if ($receiver)
	header('location: ' . $receiver . '?' . $_SERVER['QUERY_STRING']);

?>