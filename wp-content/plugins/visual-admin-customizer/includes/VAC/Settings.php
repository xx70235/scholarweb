<?php
namespace YahnisElsts\VAC;

class Settings {
	private $customizationOption;
	private $configurationOption;

	public function __construct($optionPrefix = 'ws_vac_') {
		$this->customizationOption = $optionPrefix . 'customizations';
		$this->configurationOption = $optionPrefix . 'settings';
	}

	public function loadCustomizations() {
		$customizations = get_site_option('ws_vac_customizations', null);
		if (isset($customizations)) {
			$customizations = json_decode($customizations, true);
		}
		return $customizations;
	}

	public function updateCustomizations($customizations) {
		return update_site_option('ws_vac_customizations', json_encode($customizations));
	}
}