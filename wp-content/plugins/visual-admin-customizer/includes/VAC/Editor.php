<?php
namespace YahnisElsts\VAC;
use YeEasyAdminNotices\V1\AdminNotice;

class Editor {
	const sessionTokenName = 'vacEditSessionToken';

	const menuTitle = 'Admin Customizer';
	const menuSlug = 'vac-admin-customizer';

	private $editorPageHook;
	public $settings;

	public function __construct() {
		add_action('admin_menu', array($this, 'addAdminMenu'));
		add_action('current_screen', array($this, 'stripEditorPage'));

		add_action('wp_enqueue_scripts', array($this, 'enqueueInjectedDependencies'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueInjectedDependencies'));
		add_action('login_enqueue_scripts', array($this, 'enqueueInjectedDependencies'));

		add_action('admin_head', array($this, 'printInjectedData'));
		add_action('wp_head', array($this, 'printInjectedData'));

		$this->settings = new Settings();

		register_activation_hook(plugin_basename(WS_VAC_PLUGIN_FILE), array($this, 'onPluginActivated'));

		ajaw_v1_CreateAction('vac-save-changes')
			->handler(array($this, 'ajaxSaveChanges'))
			->requiredCap($this->getRequiredCapability())
			->requiredParam('customizations')
			->register();
	}

	public function addAdminMenu() {
		$this->editorPageHook = add_management_page(
			self::menuTitle,
			self::menuTitle,
			$this->getRequiredCapability(),
			self::menuSlug,
			array($this, 'displayEditor')
		);

		$this->registerDependencies();
	}

	private function getRequiredCapability() {
		return apply_filters('vac_required_capability', 'install_plugins');
	}

	private function registerDependencies() {
		wp_register_script('vac-requirejs', plugins_url('js/vendor/require.js', WS_VAC_PLUGIN_FILE));
		wp_add_inline_script(
			'vac-requirejs',
			sprintf(
				'require.config({
					baseUrl: "%s"
	            });',
				esc_js(plugins_url('js', WS_VAC_PLUGIN_FILE))
			)
		);

		$mainScript = 'editor-bundle.js';
		if (defined('WP_DEBUG') && constant('WP_DEBUG')) {
			$mainScript = 'editor.js';
		}

		wp_register_script(
			'ws-vac-editor',
			plugins_url('js/' . $mainScript, WS_VAC_PLUGIN_FILE),
			array(
				//To prevent "Mismatched anonymous define()" errors, load everything that
				//doesn't rely on Require first.
				\RiRoleInfo::scriptHandle, 'jquery', 'jquery-ui-position', 'jquery-ui-dialog', 'jquery-ui-resizable',
				'ajaw-v1-ajax-action-wrapper',

				//Then load RequireJS.
				'vac-requirejs',
			),
			'20161201'
		);

		//CodeMirror base CSS and themes.
		wp_register_style(
			'vac-codemirror-base',
			plugins_url('js/vendor/codemirror/lib/codemirror.css', WS_VAC_PLUGIN_FILE)
		);
		wp_register_style(
			'vac-codemirror-theme-neat',
			plugins_url('js/vendor/codemirror/theme/neat.css', WS_VAC_PLUGIN_FILE)
		);
		wp_register_style(
			'vac-codemirror-theme-neo',
			plugins_url('js/vendor/codemirror/theme/neo.css', WS_VAC_PLUGIN_FILE)
		);

		//Editor CSS.
		wp_register_style(
			'ws-vac-editor-ui',
			plugins_url('css/editor.css', WS_VAC_PLUGIN_FILE),
			array(
				'common', 'forms', 'dashicons', 'buttons',
				'vac-codemirror-base', 'vac-codemirror-theme-neat', 'vac-codemirror-theme-neo',
			),
			'20161202'
		);

		wp_localize_script(
			'ws-vac-editor',
			'wsVacEditorData',
			array(
				'pluginBaseUrl' => plugins_url('/', WS_VAC_PLUGIN_FILE),
				'editSessionToken' => $this->generateSessionToken(),
				'customizations' => $this->settings->loadCustomizations(),
				'dashboardUrl' => admin_url('/'),
			)
		);
	}

	private function generateSessionToken() {
		static $token = null;
		if (!isset($token)) {
			$timestamp = time();
			$hash = wp_hash($timestamp . '|' . $this->getClientIp());
			$token = $timestamp . '|' . $hash;
		}
		return $token;
	}

	private function isValidSessionToken($token) {
		if (!is_string($token)) {
			return false;
		}

		$parts = explode('|', $token, 2);
		if (count($parts) < 2) {
			return false;
		}

		$timestamp = intval($parts[0]);
		if (($timestamp < strtotime('-72 hours')) || ($timestamp > (time() + 10))) {
			return false;
		}

		$receivedHash = $parts[1];
		$expectedHash = wp_hash($timestamp . '|' . $this->getClientIp());
		return ($receivedHash === $expectedHash);
	}

	private function getClientIp() {
		if (!empty($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		}
		return 'unknown';
	}

	public function stripEditorPage($screen) {
		if (isset($screen, $screen->id) && ($screen->id === $this->editorPageHook)) {
			$_GET['noheader'] = 1;
		}
	}

	public function displayEditor() {
		if (!current_user_can($this->getRequiredCapability())) {
			wp_die("You are not allowed to use this plugin.");
			return;
		}

		setcookie(
			self::sessionTokenName,
			$this->generateSessionToken(),
			time() + 60,
			'/'
		);

		require WS_VAC_ROOT_DIR . '/templates/editor.php';

		//No admin footer for you!
		exit;
	}

	public function enqueueInjectedDependencies() {
		if ( $this->isSessionActive() ) {
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-position');
		}
	}

	public function isSessionActive() {
		$hasToken = isset($_COOKIE[self::sessionTokenName])
			&& $this->isValidSessionToken($_COOKIE[self::sessionTokenName]);
		return $hasToken;
	}

	public function printInjectedData() {
		if ( $this->isSessionActive() ) {
			/** @noinspection BadExpressionStatementJS */
			/** @noinspection UnterminatedStatementJS */
			printf(
				'<script type="text/javascript">
					_vacScreenId = "%s"; 
					_vacIsAdmin = %s;
					_vacHookSuffix = "%s";
				</script>',
				esc_js($this->getCurrentScreenId()),
				is_admin() ? 'true' : 'false',
				esc_js(isset($GLOBALS['hook_suffix']) ? strval($GLOBALS['hook_suffix']) : '')
			);
		}
	}

	private function getCurrentScreenId() {
		if (!function_exists('get_current_screen')) {
			return null;
		}

		$screen = \get_current_screen();
		if ($screen === null) {
			return '';
		}
		return $screen->id;
	}

	public function ajaxSaveChanges($params) {
		$customizations = json_decode($params['customizations']);
		if (empty($customizations)) {
			return new \WP_Error(
				'invalid_data_format',
				'JSON parsing failed. The "customizations" field contains invalid data.'
			);
		}

		$updated = $this->settings->updateCustomizations($customizations);
		return array(
			'settingsChanged' => $updated,
			'bytesReceived' => strlen($params['customizations'])
		);
	}

	public function onPluginActivated() {
		//Show a notice explaining how to reach the settings page.
		/** @noinspection HtmlUnknownTarget */
		AdminNotice::create('vac-admin-menu-hint')
			->info(sprintf(
				'Tip: Go to <a href="%1$s">Tools -&gt; %2$s</a> to start customizing the WordPress admin.',
				esc_attr(add_query_arg('page', self::menuSlug, admin_url('tools.php'))),
				self::menuTitle
			))
			->persistentlyDismissible()
			->showOnNextPage();
	}
}