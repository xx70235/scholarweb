<?php

class RiRoleInfo {
	const scriptHandle = 'ye-role-info-js';

	public function __construct() {
		add_action('wp_loaded', array($this, 'registerScript'));
		//add_action('wp_print_scripts', array($this, 'justInTimeScriptData'), 2000);
	}

	public function registerScript() {
		wp_register_script(
			self::scriptHandle,
			plugins_url('roleinfo.js', __FILE__),
			array(),
			'20161029'
		);

		wp_localize_script(self::scriptHandle, 'wsRiRoleInfoData', $this->getScriptData());
	}

	/**
	 * Optimization: Load script data when the script is about to be printed instead of on initialisation.
	 * BUG: This doesn't work when the script that depends on this library is directly printed via do_items().
	 */
	public function justInTimeScriptData() {
		/*if (wp_script_is(self::scriptHandle, 'enqueued') || wp_script_is(self::scriptHandle, 'to_do')) {
			wp_localize_script(self::scriptHandle, 'wsRiRoleInfoData', $this->getScriptData());
		}*/
	}

	private function getScriptData() {
		$wpRoles = wp_roles();

		$roles = array();
		foreach($wpRoles->role_objects as $slug => $role) {
			/** @var WP_Role $role */

			if ( isset($role->capabilities) && is_array($role->capabilities) ) {
				$capabilities = array_map(array($this, 'castToBool'), $role->capabilities);
			} else {
				$capabilities = array();
			}

			$roles[] = array(
				'slug' => $slug,
				'displayName' => $wpRoles->role_names[$slug],
				'capabilities' => $capabilities,
			);
		}

		$currentUser = wp_get_current_user();
		$usersToInclude = array();
		if ($currentUser && $currentUser->exists()) {
			$usersToInclude[] = $currentUser;
		}

		$users = array();
		foreach($usersToInclude as $user) {
			/** @var WP_User $user */

			if (isset($user->caps) && is_array($user->caps)) {
				$capabilities = array_map(array($this, 'castToBool'), $user->caps);
			} else {
				$capabilities = array();
			}

			if (isset($user->roles) && is_array($user->roles) && !empty($user->roles)) {
				$userRoleSlugs = array_values($user->roles);
			} else {
				//Some plugins have bugs that corrupt the role list.
				//Lets try to get roles from the capability list, like in WP_User::get_role_caps().
				$userRoleSlugs = array_filter(array_keys($capabilities), array($wpRoles, 'is_role'));
			}

			$users[] = array(
				'userId' => $user->ID,
				'userLogin' => $user->user_login,
				'displayName' => $user->display_name,
				'capabilities' => $capabilities,
				'roles' => $userRoleSlugs,
				'isSuperAdmin' => is_multisite() && is_super_admin($user->ID),
			);
		}

		return array(
			'roles' => $roles,
			'users' => $users,
			'currentUserId' => ($currentUser && $currentUser->exists()) ? $currentUser->ID : 0,
		);
	}

	private function castToBool($value) {
		return (bool)$value;
	}
}