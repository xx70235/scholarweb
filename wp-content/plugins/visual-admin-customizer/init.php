<?php
require dirname(__FILE__) . '/includes/RoleInfo/load.php';
require dirname(__FILE__) . '/includes/AjaxWrapper/AjaxWrapper.php';
require dirname(__FILE__) . '/includes/AdminNotices/AdminNotice.php';

/**
 * A project-specific class loader implementation.
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {
	//Project-specific namespace prefix.
	$prefix = 'YahnisElsts\\VAC\\';

	//Base directory for the namespace prefix.
	$baseDir = __DIR__ . '/includes/VAC/';

	//Does the class use the namespace prefix?
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		//No, move to the next registered autoloader.
		return;
	}

	//Get the relative class name.
	$relativeClass = substr($class, $len);

	//Replace the namespace prefix with the base directory, replace namespace
	//separators with directory separators in the relative class name, append .php.
	$file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

	if (file_exists($file)) {
		/** @noinspection PhpIncludeInspection */
		require $file;
	}
});

$wsVisualEditor = new YahnisElsts\VAC\Editor();
$wsCustomizationApplicator = new YahnisElsts\VAC\Applicator($wsVisualEditor->settings);