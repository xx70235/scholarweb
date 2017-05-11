<?php

/**
 * The configuration file
 */
!defined('HMW_REQUEST_TIME') && define('HMW_REQUEST_TIME', microtime(true));
defined('_HMW_NONCE_ID_') || define('_HMW_NONCE_ID_', NONCE_KEY);

if (!defined('HMW_VERSION_ID')) {
    $version = explode('.', HMW_VERSION);
    define('HMW_VERSION_ID', ((int) @$version[0] * 1000 + (int) @$version[1] * 100 + ((isset($version[2])) ? ((int) $version[2] * 10) : 0)));
}

/* No path file? error ... */
require_once(dirname(__FILE__) . '/paths.php');

/* Define the record name in the Option and UserMeta tables */
define('HMW_OPTION', 'hmw_options');
define('HMW_OPTION_SAFE', 'hmw_options_safe');