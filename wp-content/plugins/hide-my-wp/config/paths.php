<?php

$currentDir = dirname(__FILE__);

define('_HMW_NAMESPACE_', 'HMW');
define('_HMW_PLUGIN_NAME_', 'hide-my-wp');
define('_HMW_THEME_NAME_', 'default');
define('_HMW_SUPPORT_EMAIL_', 'contact@wpplugins.tips');

/* Directories */
define('_HMW_ROOT_DIR_', realpath($currentDir . '/..'));
define('_HMW_CLASSES_DIR_', _HMW_ROOT_DIR_ . '/classes/');
define('_HMW_CONTROLLER_DIR_', _HMW_ROOT_DIR_ . '/controllers/');
define('_HMW_MODEL_DIR_', _HMW_ROOT_DIR_ . '/models/');
define('_HMW_TRANSLATIONS_DIR_', _HMW_ROOT_DIR_ . '/languages/');
define('_HMW_THEME_DIR_', _HMW_ROOT_DIR_ . '/view/');

/* URLS */
define('_HMW_URL_', plugins_url() . '/' . _HMW_PLUGIN_NAME_);
define('_HMW_THEME_URL_', _HMW_URL_ . '/view/');

$upload_dir['baseurl'] = network_site_url('/wp-content/uploads');
$upload_dir['basedir'] = ABSPATH . 'wp-content/uploads';
