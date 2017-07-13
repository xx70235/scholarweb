<!doctype html>
<head xmlns:v-bind="http://www.w3.org/1999/xhtml">
	<title>Visual Admin Customizer</title>

	<?php
	//Luckily, we can re-use the core dependency implementation to print the editor
	//styles, scripts and all of their dependencies.
	$styles = wp_styles();
	$styles->do_items(array('ws-vac-editor-ui'));

	$scripts = wp_scripts();
	$scripts->do_items(array('ws-vac-editor'));

	$currentUser = wp_get_current_user();
	$roles = wp_roles();
	?>
</head>
<body class="wp-core-ui">
	<div id="vac-iframe-container">
		<iframe src="<?php echo esc_attr(admin_url('/')); ?>" frameborder="0" id="vac-editor-frame"></iframe>
	</div>

	<div id="vac-editor">

		<div id="vac-editor-menubar" style="visibility: hidden;" v-bind:style="{visibility: 'visible'}">
			<fieldset id="vac-mode-switcher" class="vac-bar-item vac-button-group">
				<label>
					<input type="radio" name="vac-mode" value="edit" v-bind:disabled="isFrameLoading">
					Edit
				</label>
				<label>
					<input type="radio" name="vac-mode" value="navigate" v-bind:disabled="isFrameLoading">
					Navigate
				</label>
			</fieldset>

			<div class="vac-bar-item vac-mega-button vac-has-popup" id="vac-select-actor-filter">
				<strong class="vac-mega-title">Who</strong>
				<div id="vac-actor-summary" class="vac-mega-subtitle">
					{{ currentActorFilter.getSummary() }}
				</div>

				<div class="vac-popup-panel vac-menu-container" id="vac-actors">
					<ul class="vac-menu-items vac-has-labelled-items">
						<li class="vac-context-menu-item">
							<label title="<?php
							echo esc_attr(sprintf(
								'Everyone except the current user (%s, ID: %d)',
								$currentUser->user_login,
								$currentUser->ID
							));
							?>">
								<input type="radio" name="vac-selected-actor"
								       v-model="currentActorFilter" v-bind:value="actorFilterPresets.notCurrentUser">
								Everyone but you
							</label>
						</li>

						<li class="vac-context-menu-item vac-last-group-member">
							<label>
								<input type="radio" name="vac-selected-actor"
								       v-model="currentActorFilter" v-bind:value="actorFilterPresets.everyone">
								All users
							</label>
						</li>

						<li class="vac-context-menu-item" v-for="roleFilter in singleRoleFilters">
							<label v-bind:title="roleFilter.tooltip">
								<input type="radio" name="vac-selected-actor"
								       v-model="currentActorFilter" v-bind:value="roleFilter">
								{{ roleFilter.getSummary() }}
							</label>
						</li>

						<li class="vac-context-menu-item" id="vac-menu-item-selected-roles" v-if="false">
							<label>
								<input type="radio" name="vac-selected-actor"
								       v-model="currentActorFilter" v-bind:value="actorFilterPresets.custom">
								Selected roles:
							</label>
						</li>
					</ul>


					<div id="vac-actor-list-container" v-if="false">
						<label id="vac-select-all-actors">
							<input type="checkbox" v-model="areAllRolesSelected"> Select all
						</label>

						<ul class="vac-full-actor-list">
							<li v-for="role in roleInfo.rolesByName">
								<label v-bind:title="role.subjectId">
									<input type="checkbox" v-model="checkedCustomActorFilterOptions"
									       v-bind:value="role.subjectId">
									{{ role.displayName }}
								</label>
							</li>
					</ul>
					</div>
				</div>
			</div>
			<div class="vac-bar-item vac-mega-button vac-has-popup" id="vac-select-page-match">
				<strong class="vac-mega-title">Where</strong>
				<div id="vac-page-match-summary" class="vac-mega-subtitle">
					{{ selectedPagePreset ? selectedPagePreset.name : "" }}
				</div>

				<div class="vac-popup-panel vac-menu-container vac-has-labelled-items" id="vac-page-matches">
					<ul class="vac-menu-items">
						<li v-for="option in pageFilterPresets" class="vac-context-menu-item">
							<label>
								<input type="radio" name="vac-page-match-option"
								       v-model="selectedPagePreset" v-bind:value="option">
								{{ option.name }}
							</label>
						</li>
					</ul>
				</div>
			</div>

			<div class="vac-bar-item vac-button-group">
				<button class="button button-secondary vac-editor-button" id="vac-open-code-editor"
				        v-on:click="openCodeEditor" v-bind:disabled="isFrameLoading">
					Edit CSS
				</button>
			</div>

			<div class="vac-bar-item vac-button-group" id="vac-undo-group">
				<button id="vac-undo" class="button vac-editor-button" title="Undo"></button>
				<button id="vac-redo" class="button vac-editor-button" title="Redo"></button>
			</div>

			<div id="vac-toggle-change-list" class="vac-bar-item vac-mega-button vac-has-popup">
				Changes
				<span id="vac-change-count">({{ changes.orderedChanges.length }})</span>

				<div id="vac-change-list" class="vac-popup-panel">
					<h3>Changes</h3>

					<div v-if="changes.orderedChanges.length < 1">
						There are no changes or customizations.
					</div>

					<table id="vac-changes">
						<tbody>
						<tr v-for="change in changes.orderedChanges"
						    v-bind:class="{ 'vac-is-applicable-change': isChangeApplicable(change) }">
							<td class="vac-action-label">{{ getActionLabel(change.action) }}</td>
							<td>
								<div class="vac-selector">{{ change.selector }}</div>
							</td>
							<td class="vac-col-actor-filter">
								{{ change.actorFilter.getSummary() }}
							</td>
							<td class="vac-col-remove-change">
								<div class="vac-remove-change" title="Remove customization"
								     v-on:click="deleteChange(change)">
									<div class="dashicons dashicons-trash"></div>
								</div>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>

			<div class="vac-bar-item vac-button-group">
				<!--
				The success indicator starts out hidden and is displayed only when the user successfully
				saves changes. Note that by using v-if to remove the element when a save is in progress,
				we get to restart the animation "for free", without doing anything extra.
				-->
				<div id="vac-save-success-indicator-container"
				     v-if="!isSaveInProgress && savedSuccessfully"
				     style="display: none;"
				     v-show="true">
					<div id="vac-save-success-indicator" class="vac-indicator-animation">
						<div class="dashicons dashicons-yes"></div>
					</div>
				</div>

				<button class="button button-primary vac-editor-button" id="vac-save-changes"
				     v-on:click="saveChanges" v-bind:disabled="isSaveInProgress">
					<span v-show="isSaveInProgress">
						Saving...
					</span>
					<span v-show="!isSaveInProgress">
						Save <span class="vac-hide-in-small-viewports">Changes</span>
					</span>
				</button>
			</div>

			<div class="vac-bar-item vac-button-group" id="vac-exit-editor-container">
				<button class="button button-secondary vac-editor-button"
				        id="vac-exit-editor"
				        v-on:click="returnToDashboard">
					<span class="vac-hide-in-small-viewports">Exit</span>
				</button>
			</div>
		</div>

		<div id="vac-context-menu" class="vac-menu-container">
			<ul class="vac-menu-items">
				<li class="vac-context-menu-item" id="vac-remove-menu">
					Remove
				</li>
				<li class="vac-context-menu-item" id="vac-hide-menu">
					Hide
				</li>
				<li class="vac-context-menu-item" id="vac-change-text-menu">
					Change Text
				</li>
				<li class="vac-context-menu-item vac-has-submenu" id="vac-select-parent-menu">
					Expand Selection
					<!-- Select Parent -->

					<div id="vac-select-parent-submenu" class="vac-menu-container">
						<ul class="vac-menu-items">
							<li class="vac-context-menu-item">The parent list will be generated by JS</li>
						</ul>
					</div>
				</li>
				<li class="vac-context-menu-item" id="vac-select-suggested-parent">
					Item title will be set by JS
				</li>
			</ul>
		</div>

		<div class="vac-dialog" id="vac-dialog-change-text" style="display: none;">
			<label>
				<span class="vac-field-label" style="display: none;">Text</span>
				<textarea id="vac-element-text"></textarea>
			</label>
		</div>

		<div id="vac-frame-cover" v-show="isFrameLoading">
			<div id="vac-frame-cover-inside">
				<div id="vac-frame-loading-indicator">
					<div class="spinner is-active"></div>
					Loading...
				</div>
			</div>
		</div>

		<?php require 'custom-code-dialog.php'; ?>

	</div> <!-- /#vac-editor -->
</body>