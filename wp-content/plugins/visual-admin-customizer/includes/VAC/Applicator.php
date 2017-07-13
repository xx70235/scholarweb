<?php
namespace YahnisElsts\VAC;


class Applicator {
	private $settings;

	public function __construct(Settings $settings) {
		$this->settings = $settings;
		add_action('admin_head', array($this, 'applyCustomizations'));
	}

	public function applyCustomizations() {
		$customizations = $this->settings->loadCustomizations();
		if (!isset($customizations)) {
			return;
		}

		$currentUser = wp_get_current_user();
		$currentScreen = function_exists('get_current_screen') ? get_current_screen() : null;

		$removed = array();
		$hidden = array();
		$replacementText = array();
		$userCss = array();

		foreach($customizations['changes'] as $change) {
			//Filter applicable changes
			if (!$this->isApplicable($change, $currentUser, $currentScreen)) {
				continue;
			}

			switch($change['action']) {
				case 'remove':
					$removed[] = $change['selector'];
					break;
				case 'hide':
					$hidden[] = $change['selector'];
					break;
				case 'changeText':
					$replacementText[$change['selector']] = strval($change['params']);
					break;
				case 'addCustomCss':
					$userCss[] = strval($change['params']);
					break;
				default:
					//var_dump($change);
					break;
			}
		}

		$css = array();

		if (!empty($removed)) {
			$css[] = sprintf(
				'%s { display: none !important; }',
				implode(', ', $removed)
			);
		}
		if (!empty($hidden)) {
			$css[] = sprintf(
				'%s { visibility: hidden !important; }',
				implode(', ', $hidden)
			);
		}

		if (!empty($userCss)) {
			printf(
				'<style type="text/css" id="vac-user-css">%s</style>',
				implode("\n", $userCss)
			);
		}

		if (!empty($css)) {
			printf(
				'<style type="text/css" id="vac-css-customizations">%s</style>',
				implode("\n", $css)
			);
		}

		if (!empty($replacementText)) {
			wp_enqueue_script(
				'vac-customization-applicator',
				plugins_url('js/applicator.js', WS_VAC_PLUGIN_FILE),
				array('jquery'),
				'20161109',
				true
			);

			wp_localize_script(
				'vac-customization-applicator',
				'wsVacApplicatorData',
				array(
					'replacementText' => $replacementText
				)
			);
		}
	}

	private function isApplicable($change, \WP_User $user, \WP_Screen $screen = null) {
		return $this->pageFilterMatches($change['pageFilter'], $screen)
			&& $this->actorFilterMatches($change['actorFilter'], $user);
	}

	private function pageFilterMatches($pageFilter, \WP_Screen $screen = null) {
		switch($pageFilter['predicate']) {
			case 'is_admin':
				return is_admin();
			case 'screen_id':
				return $screen && ($screen->id === $pageFilter['operand']);
			case '*':
				return true;
		}
		return false;
	}

	private function actorFilterMatches($actorFilter, \WP_User $user) {
		if (!$user) {
			//Technically this should never happen. If it does, use the default setting.
			return isset($actorFilter['*']) && $actorFilter['*'];
		}

		//Is there a specific setting for this user?
		if (isset($actorFilter['user:' . $user->user_login])) {
			return (boolean)$actorFilter['user:' . $user->user_login];
		}

		//Does the filter match *any* of the user's roles?
		if (isset($user->roles) && is_array($user->roles) && !empty($user->roles)) {
			foreach($user->roles as $role) {
				if (isset($actorFilter['role:' . $role]) && $actorFilter['role:' . $role]) {
					return true;
				}
			}
		}

		//Use the default.
		return isset($actorFilter['*']) ? $actorFilter['*'] : false;
	}
}