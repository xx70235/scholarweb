/**
 * @property {RiRoleInfoCore} riRoleInfo
 */

//Object.create polyfill
if (typeof Object.create != 'function') {
	Object.create = (function(undefined) {
		var Temp = function() {};
		return function (prototype, propertiesObject) {
			if(prototype !== Object(prototype) && prototype !== null) {
				throw TypeError('Argument must be an object, or null');
			}
			Temp.prototype = prototype || {};
			var result = new Temp();
			Temp.prototype = null;
			if (propertiesObject !== undefined) {
				Object.defineProperties(result, propertiesObject);
			}

			// to imitate the case of Object.create(null)
			if(prototype === null) {
				result.__proto__ = null;
			}
			return result;
		};
	})();
}

require(
	[
		'vendor/js-biscuit', 'vendor/vue', 'vendor/lodash', 'actor-filter', 'page-filter', 'change',
		'change-store', 'vendor/codemirror/lib/codemirror', 'vendor/codemirror/mode/css/css'
	],
	(function(vacCookies, Vue, _, VACActorFilter, VACPageFilter, VACChange, VACChangeStore, CodeMirror) {

	var editorData = window['wsVacEditorData'] || {};

	jQuery(function ($) {
		//------------------------------------------------------------------------
		// View model

		var notCurrentUserFilter = new VACActorFilter().include('*').exclude(riRoleInfo.getCurrentUser()),
			customActorFilter = new VACActorFilter(),
			thisPagePreset = new VACPageFilter(VACPageFilter.builtInPredicates.screenId, 'not_implemented'),
			anyPagePreset = new VACPageFilter(VACPageFilter.builtInPredicates.any);

		//noinspection JSUnusedGlobalSymbols
		var vacApp = window.vacApp = new Vue({
			el: '#vac-editor',
			data: {
				roleInfo: riRoleInfo,

				actorFilterPresets: {
					everyone: new VACActorFilter().include('*'),
					notCurrentUser: notCurrentUserFilter,
					custom: customActorFilter
				},
				singleRoleFilters: _.map(riRoleInfo.rolesByName, function(role) {
					return new VACActorFilter().include(role).setTooltip(role.slug);
				}),
				currentActorFilter: notCurrentUserFilter,

				pageFilterPresets: [
					{
						name: 'This admin page',
						filter: thisPagePreset
					},
					{
						name: 'All admin pages',
						filter: new VACPageFilter(VACPageFilter.builtInPredicates.isAdmin)
					},
					{
						name: 'Everywhere',
						filter: anyPagePreset
					}
				],
				selectedPagePreset: null,

				currentPage: {
					url: '',
					isAdmin: false,
					screenId: null
				},

				isFrameLoading: true,

				changes: new VACChangeStore(),

				isSaveInProgress: false,
				savedSuccessfully: false,

				isCodeDialogOpen: false
			},

			computed: {
				checkedCustomActorFilterOptions: customActorFilter.getComputedProp(),
				areAllRolesSelected: {
					get: function() {
						return _.every(this.roleInfo.roles, function(role) {
							return customActorFilter.isIncluded(role.subjectId);
						});
					},
					set: function(selected) {
						customActorFilter.clear();
						if (selected) {
							_.forEach(this.roleInfo.roles, function(role) {
								customActorFilter.set(role.subjectId, selected);
							});
						}
					}
				},

				allActorFilters: function() {
					return [
						this.actorFilterPresets.notCurrentUser, this.actorFilterPresets.everyone
					].concat(this.singleRoleFilters);
				},

				currentPageFilter: function() {
					return this.selectedPagePreset ? this.selectedPagePreset.filter : null;
				}
			},

			methods: {
				saveChanges: function() {
					var self = this;

					//noinspection JSUnusedGlobalSymbols
					this.isSaveInProgress = true;
					//noinspection JSUnusedGlobalSymbols
					this.savedSuccessuflly = false;

					AjawV1.getAction('vac-save-changes').post(
						{
							customizations: vacApp.changes.toJSON()
						},
						function() {
							self.isSaveInProgress = false;
							self.savedSuccessfully = true;
						},
						function(errorResponse) {
							self.isSaveInProgress = false;
							if (typeof errorResponse['error'] !== 'undefined') {
								alert(errorResponse['error']);
							}
							if (console && console.error) {
								console.error(errorResponse);
							}
						}
					);
				},

				deleteChange: function(change) {
					var self = this;
					var deleteIt = function() {
						self.changes.remove(change);
						vacEditor.reapplyAllChanges();
					};

					vacEditor.addUndoStep({
						previousSetting: this.changes.getState(change.getContextKey()),
						undo: function() {
							vacEditor.reapplyAllChanges();
						},
						redo: deleteIt
					});

					deleteIt();
				},

				getActionLabel: function(actionName) {
					return vacEditor.actions[actionName].label;
				},

				/**
				 * Is the given change applicable to the current page and filters?
				 *
				 * @param {VACChange} change
				 * @return {boolean}
				 */
				isChangeApplicable: function(change) {
					return (change != null) && change.isApplicable(this.currentPage, this.currentActorFilter);
				},

				/**
				 * Exit the editor and return to the WP admin.
				 */
				returnToDashboard: function () {
					window.location.href = editorData['dashboardUrl'] || '/';
				},
				
				openCodeEditor: function () {
					vacEditor.stopEditing();
					vacEditor.closeMenuBarPopups();
					customCodeDialog.dialog('open');
				}
			}
		});

		vacApp.selectedPagePreset = vacApp.pageFilterPresets[0];

		//------------------------------------------------------------------------
		// VAC miscellany

		var $childFrame = $('#vac-editor-frame'),
			$menuBar = $('#vac-editor-menubar'),
			childWindow,
			$childDocument,
			isInitialLoad = true,

			$contextMenu = $('#vac-context-menu'),
			$parentSubmenu = $('#vac-select-parent-submenu'),
			$selectParentItem = $('#vac-select-parent-menu'),
			$selectSuggestedParent = $('#vac-select-suggested-parent');

		//Create a short-lived cookie for this editing session. It's used to
		//load dependencies for the injected script (inject.js).
		window.setInterval(function() {
			var inTwoMinutes = new Date(new Date().getTime() + 2 * 60 * 1000);
			vacCookies.set(
				'vacEditSessionToken',
				editorData['editSessionToken'],
				{ expires: inTwoMinutes }
			);
		}, 12000);

		window.vacEditor = {
			log: function() {
				if (console && console.log) {
					console.log.apply(console, arguments);
				}
			},

			warn: function() {
				if (console && console.warn) {
					console.warn.apply(console, arguments);
				}
			},

			onInjectedScriptReady: function() {
				vacApp.currentPage.url = childWindow.location.href;
				vacApp.currentPage.screenId = (typeof childWindow['_vacScreenId'] !== 'undefined') ? childWindow['_vacScreenId'] : '';
				vacApp.currentPage.isAdmin = (typeof childWindow['_vacIsAdmin'] !== 'undefined') ? childWindow['_vacIsAdmin'] : '';

				thisPagePreset.screenId = vacApp.currentPage.screenId;
				appliedChanges = [];

				if (isInitialLoad) {
					childWindow.vacInjected.switchToEditMode();
					isInitialLoad = false;
				}

				var mode = childWindow.vacInjected.mode;
				$menuBar.find('input[name="vac-mode"]').filter(function() {
					return $(this).val() === mode;
				}).prop('checked', true);

				vacEditor.applyAllChanges();

				//Load complete.
				vacApp.isFrameLoading = false;
			},

			onBeforeFrameUnload: function() {
				vacApp.isFrameLoading = true;
				vacEditor.stopEditing();
			},

			onSelected: function(element) {
				$('#vac-change-text-menu').toggle(element.containsOnlyOneTextNode());

				$contextMenu.show();
				updateContextMenuPosition();
				populateParentList();
			},

			onDeselected: function() {
				$contextMenu.hide();
			},

			onChildScroll: _.throttle(function() {
				if ($contextMenu.is(':visible')) {
					updateContextMenuPosition();
				}
			}, 20),

			stopEditing: function() {
				if (this.currentAction) {
					this.currentAction.cancel();
					this.currentAction = null;
				}

				$contextMenu.hide();
				if (!vacApp.isFrameLoading) {
					childWindow.vacInjected.selectElement(null);
				}
			},

			closeMenuBarPopups: function() {
				$menuBar.find('.vac-popup-panel').hide();
				$menuBar.find('.vac-has-popup').removeClass('vac-is-open');
			}
		};

		$childFrame.on('load', function() {
			vacEditor.log('Frame has loaded');
			childWindow = $childFrame.get(0).contentWindow;
			$childDocument = $childFrame.contents();

			var childDocument = $childFrame.get(0).contentDocument;

			var injectedScriptUrl = editorData['pluginBaseUrl'] + 'js/inject.js?ver=20161205';
			var scriptElement = childDocument.createElement('script');
			scriptElement.type = 'text/javascript';
			scriptElement.src = injectedScriptUrl;
			childDocument.body.appendChild(scriptElement);

			var styleElement = childDocument.createElement('link');
			styleElement.rel = 'stylesheet';
			styleElement.href = editorData['pluginBaseUrl'] + 'css/inject.css?ver=20161201';
			childDocument.head.appendChild(styleElement);
			vacEditor.log('Scripts and styles injected');
		});


		$menuBar.find('input[name="vac-mode"]').on('change', function() {
			var radioButton = $(this);
			if (radioButton.is(':checked')) {
				var mode = $(this).val();

				if (childWindow.vacInjected.mode === mode) {
					return;
				}

				if (mode === 'edit') {
					childWindow.vacInjected.switchToEditMode();
				} else if (mode === 'navigate') {
					childWindow.vacInjected.switchToNavigationMode();
				}
			}
		});

		function updateContextMenuPosition() {
			//The context menu should be positioned to the right or left of the selected jq,
			//and it must be inside the viewport.

			var rect = childWindow.vacInjected.getSelectionRect();

			//The bounding rectangle is in viewport space, which is handy because the iframe is positioned
			//at the top left of the window. So the frame's viewport coordinates match our viewport coordinates.

			var menuWidth = $contextMenu.outerWidth(),
				menuHeight = $contextMenu.outerHeight(),
				viewportWidth = $(window).width(),
				viewportHeight = $(window).height(),
				uiOffset = 8,
				minOffsetFromTop = uiOffset,
				minOffsetFromBottom = $menuBar.height() + uiOffset,
				minOffsetFromRight = Math.max(20, uiOffset); //Menu should not cover the scroll bar.

			//Show the menu on the right unless there's not enough room.
			var left = rect.right + uiOffset;
			if (left + menuWidth + minOffsetFromRight > viewportWidth) {
				//Put it on the left instead.
				left = rect.left - uiOffset - menuWidth;
				if (left < 0) {
					//Fallback: Put it next at right edge of the window.
					left = viewportWidth - menuWidth - minOffsetFromRight;
				}
			}

			//Place the menu at the top of the selection unless that's outside the viewport.
			var top = rect.top;
			if ((viewportHeight - top - menuHeight) < minOffsetFromBottom) {
				top = viewportHeight - minOffsetFromBottom - menuHeight;
			}
			if (top < minOffsetFromTop) {
				top = minOffsetFromTop;
			}

			$contextMenu.css({
				'left': left,
				'top': top
			});

			if ($parentSubmenu.is(':visible')) {
				updateParentSubmenuPosition();
			}
		}

		function updateParentSubmenuPosition() {
			$parentSubmenu.position({
				my: 'left bottom',
				at: 'right bottom',
				of: $selectParentItem,
				collision: 'flip fit'
			});
		}

		$selectParentItem.on('mouseenter click', function(event) {
			//Ignore events on submenu items.
			var target = $(event.target);
			if (target.closest($parentSubmenu).length > 0) {
				return;
			}

			$parentSubmenu.show();
			updateParentSubmenuPosition();
		});

		$selectParentItem.on('mouseleave', function() {
			$parentSubmenu.hide();

			//Clear the temporary parent highlight and re-light the current jq.
			childWindow.vacInjected.highlightSelectedElement();
		});

		var commonParentSelectors = [
			{
				selector: '.postbox',
				label: 'Meta Box'
			},
			{
				selector: '.screen-meta-toggle',
				label: 'Wrapper'
			},
			{
				selector: 'tr',
				label: 'Row'
			},
			{
				selector: 'li',
				label: 'List Item'
			}
		];

		function populateParentList() {
			var child = childWindow.vacInjected.getSelectedElement(),
				$list = $parentSubmenu.find('.vac-menu-items').first();

			$list.empty();
			if (!child) {
				return;
			}

			var parents = child.getParents();
			for (var i = 0; i < parents.length; i++) {
				$list.prepend(
					$('<li>', { text: parents[i].getShortPath() })
						.addClass('vac-context-menu-item')
						.data('vacElement', parents[i])
				);
			}

			//Make it easier to select commonly customized things like widgets, meta boxes, etc.
			var suggestedParent = null, suggestionLabel = null;
			for (var si = 0; (si < commonParentSelectors.length) && (suggestedParent === null); si++) {
				//This is basically $.closest().

				//Don't show suggestions that match the current selection. It would be confusing.
				if (child.jq.is(commonParentSelectors[si].selector)) {
					continue;
				}

				for (var parentIndex = 0; parentIndex < parents.length; parentIndex++) {
					if (parents[parentIndex].jq.is(commonParentSelectors[si].selector)) {
						suggestedParent = parents[parentIndex];
						suggestionLabel = commonParentSelectors[si].label;
						break;
					}
				}
			}

			$selectSuggestedParent.toggle(suggestedParent !== null);
			if (suggestedParent) {
				$selectSuggestedParent.text('Select ' + suggestionLabel).data('vacElement', suggestedParent);
			}
		}

		$parentSubmenu.on('mouseenter', 'li.vac-context-menu-item', function() {
			var element = $(this).data('vacElement');
			if (!element) {
				return;
			}
			childWindow.vacInjected.highlightElement(element);
		});

		$parentSubmenu.on('click', 'li.vac-context-menu-item', function(event) {
			$parentSubmenu.hide();
			event.stopPropagation();

			var element = $(this).data('vacElement');
			if (!element) {
				alert('Error: No jq!');
				return;
			}

			childWindow.vacInjected.selectElement(element.jq);
		});

		//The "Select [frequently used parent]" menu item.
		$selectSuggestedParent.on('mouseenter', function() {
			var element = $(this).data('vacElement');
			if (!element) {
				return;
			}
			childWindow.vacInjected.highlightElement(element);
		});

		$selectSuggestedParent.on('mouseleave', function() {
			//Re-highlight the selected element.
			childWindow.vacInjected.highlightSelectedElement();
		});

		$selectSuggestedParent.on('click', function() {
			var element = $(this).data('vacElement');
			if (!element) {
				alert('Error: No jq!');
				return;
			}
			childWindow.vacInjected.selectElement(element.jq);
		});

		//------------------------------------------------------------------------
		// Popup panels in the menu bar

		$menuBar.on('click', '.vac-has-popup', function(event) {
			var $button = $(this),
				$popup = $button.find('.vac-popup-panel'),
				isOpen = $popup.is(':visible');

			//Don't auto-hide the popup when the user clicks something inside it.
			//That should be handled by a popup-specific function.
			if (event.target && ($popup.is(event.target) || ($popup.find(event.target).length > 0))) {
				return;
			}

			isOpen = !isOpen;
			$popup.toggle(isOpen);
			$button.toggleClass('vac-is-open', isOpen);

			if (isOpen) {
				$popup.position({
					my: 'right bottom',
					at: 'right top',
					of: $button
				});

				//Hide other popups.
				$menuBar.find('.vac-popup-panel').not($popup).hide()
					.closest('.vac-has-popup').removeClass('vac-is-open');
			}
		});

		//------------------------------------------------------------------------
		// Undo support

		var undoStack = [], undoIndex = 0, pendingUndoBatches = [];

		vacEditor.commitAction = function(element, action, params) {
			var selector = element.getSelector(),
				change = new VACChange(
					action.id,
					selector,
					params,
					vacApp.currentActorFilter.clone(),
					vacApp.currentPageFilter.clone()
				);
			this.commitChange(change, element);
		};

		/**
		 * @param {VACChange} change
		 * @param {VACElement} [element]
		 */
		vacEditor.commitChange = function(change, element) {
			var previousSetting = vacApp.changes.getState(change.getContextKey()),
				actionMemento = null;

			if (vacApp.isChangeApplicable(change)) {
				actionMemento = this.applyChange(change, element);
			}

			//Store the new settings.
			vacApp.changes.set(change);

			//Add an undo step.
			this.addUndoStep({
				change: change,
				memento: actionMemento,
				previousSetting: previousSetting
			});
		};

		var appliedChanges = [];
		/**
		 * @param {VACChange} change
		 * @param {string|VACElement} [element]
		 * @return {Object|null} Action memento.
		 */
		vacEditor.applyChange = function(change, element) {
			var action = this.actions[change.action],
				actionMemento = null;

			element = element || change.selector;
			if (element && (typeof element !== 'object')) {
				element = childWindow.vacInjected.getElement(change.selector);
			}

			if (element) {
				actionMemento = this.applyAction(action, element, change.params, change);
				appliedChanges.push(change);
			}
			change.actionMemento = actionMemento;

			return actionMemento;
		};

		vacEditor.addUndoStep = function(step) {
			if (pendingUndoBatches.length > 0) {
				var currentBatch = pendingUndoBatches[pendingUndoBatches.length - 1];
				currentBatch.push(step);
				return;
			}

			//Clear redo steps first.
			this.clearRedoStack();

			undoStack.push(step);
			undoIndex = undoStack.length - 1;
			vacEditor.refreshUndoUi();
		};

		vacEditor.beginUndoBatch = function() {
			pendingUndoBatches.push([]);
		};

		vacEditor.endUndoBatch = function() {
			var batchSteps = pendingUndoBatches.pop();
			this.addUndoStep({
				isBatch: true,
				steps: batchSteps
			});
		};

		vacEditor.applyAction = function(action, element, params, change) {
			if (typeof action !== 'object') {
				action = this.actions[action];
			}

			if (typeof element === 'string') {
				element = childWindow.vacInjected.getElement(element);
			}

			if (!element) {
				throw 'Cannot apply action because the element does not exist';
			}

			return action.apply(element, params, change);
		};

		vacEditor.undo = function(step) {
			if (typeof step === 'undefined') {
				if (!this.hasUndoSteps()) {
					throw {
						message: 'There is nothing to undo.',
						stackLength: undoStack.length,
						undoIndex: undoIndex
					}
				}

				step = undoStack[undoIndex];
				if (undoIndex >= 0) {
					undoIndex--;
				}
			}

			if (step.isBatch) {
				for (var i = step.steps.length - 1; i >=0; i--) {
					this.undo(step.steps[i]);
				}
				return;
			}

			//Roll back to the earliest modified index, then reapply changes from that point on.
			if (step.previousSetting) {
				var result = vacApp.changes.restoreState(step.previousSetting);

				//Note: This is messy. There should be a better way to find the modification point
				//inside the appliedChanges array. Perhaps an incrementing rev. number of some kind?

				//Revert all applied changes down to the last unaffected change.
				var lastUnaffectedIndex = -1;
				_.forEachRight(result.unaffected, function(change) {
					var index = _.lastIndexOf(appliedChanges, change);
					if (index >= 0) {
						lastUnaffectedIndex = index;
						return false;
					}
				});
				this.revertAppliedChanges(lastUnaffectedIndex + 1);

				//Re-apply all affected changes.
				_.forEach(result.affected, function(change) {
					if (vacApp.isChangeApplicable(change)) {
						vacEditor.applyChange(change);
					}
				});
			}

			//Execute the custom undo callback.
			if (step.undo) {
				step.undo();
			}

			vacEditor.refreshUndoUi();
			childWindow.vacInjected.deselect();
		};

		vacEditor.redo = function(step) {
			if (typeof step === 'undefined') {
				if (!this.hasRedoSteps()) {
					throw {
						message: 'There is nothing to redo.',
						stackLength: undoStack.length,
						undoIndex: undoIndex
					}
				}

				undoIndex++;
				step = undoStack[undoIndex];
			}

			if (step.isBatch) {
				for (var i = 0; i < step.steps.length; i++) {
					this.redo(step.steps[i]);
				}
				return;
			}

			if (step.change) {
				if (vacApp.isChangeApplicable(step.change)) {
					//Update DOM.
					step.memento = this.applyChange(step.change);
				}

				//Update settings.
				vacApp.changes.set(step.change);
			}

			//Execute the custom redo callback.
			if (step.redo) {
				step.redo();
			}

			vacEditor.refreshUndoUi();
			childWindow.vacInjected.deselect();
		};

		vacEditor.clearRedoStack = function() {
			if (undoStack.length > 0 && undoIndex < undoStack.length - 1) {
				undoStack.splice(undoIndex + 1);
			}
			vacEditor.refreshUndoUi();
		};

		var $undoButton = $('#vac-undo'),
			$redoButton = $('#vac-redo');

		vacEditor.refreshUndoUi = function() {
			$undoButton.prop('disabled', !this.hasUndoSteps());
			$redoButton.prop('disabled', !this.hasRedoSteps());
		};

		vacEditor.hasUndoSteps = function() {
			return undoStack.length > 0 && undoIndex >= 0;
		};

		vacEditor.hasRedoSteps = function() {
			return undoStack.length > 0 && undoIndex < undoStack.length - 1;
		};

		$undoButton.click(function() {
			vacEditor.undo();
		});

		$redoButton.click(function() {
			vacEditor.redo();
		});

		vacEditor.refreshUndoUi();

		//------------------------------------------------------------------------
		// Actions

		vacEditor.currentAction = null;
		vacEditor.startAction = function(actionId) {
			var action = this.actions[actionId];
			if (!action) {
				throw {
					message: 'Invalid editor action' + actionId + '"'
				};
			}

			var element = childWindow.vacInjected.getSelectedElement();
			if (!element) {
				alert('You need to select something first!');
				return;
			}

			vacEditor.currentAction = action;

			action.run(element);
		};

		vacEditor.actions = {};

		var baseAction = {
			id: 'invalid-unnamed-action',
			label: 'Base Action',

			run: function(element) {
				vacEditor.commitAction(element, this, true);

				vacEditor.currentAction = null;
				vacEditor.clearRedoStack();
				vacEditor.stopEditing();
			},

			apply: function(element, params, change) {
				var memento = this.getMemento(element);
				this.updateDom(element, params, change);
				return memento;
			},

			updateDom: function(element, params, change) {
				//Should be overridden in descendants.
			},

			getMemento: function(element) {
				return {
					action: this,
					selector: element.getSelector()
				};
			},

			rollback: function(memento) {
				var element = childWindow.vacInjected.getElement(memento.selector);
				if (element) {
					this.rollbackDom(element, memento);
				}
			},

			rollbackDom: function(element, memento) {
				//Should be overridden in descendants.
			},

			cancel: function() {
				//Should be overridden by interactive actions.
			}
		};

		vacEditor.actions.remove =  $.extend(
			Object.create(baseAction),
			{
				id: 'remove',
				label: 'Remove',

				updateDom: function (element) {
					element.jq.addClass('vac-removed');
				},

				rollbackDom: function(element) {
					element.jq.removeClass('vac-removed');
				}
			}
		);

		$('#vac-remove-menu').click(function() {
			var element = childWindow.vacInjected.getSelectedElement();
			vacEditor.actions.remove.run(element);
		});

		vacEditor.actions.hide =  $.extend(
			Object.create(baseAction),
			{
				id: 'hide',
				label: 'Hide',

				updateDom: function (element) {
					element.jq.addClass('vac-hidden');
				},

				rollbackDom: function(element) {
					element.jq.removeClass('vac-hidden');
				}
			}
		);

		$('#vac-hide-menu').click(function() {
			var element = childWindow.vacInjected.getSelectedElement();
			vacEditor.actions.hide.run(element);
		});

		var $elementTextEditor = $('#vac-element-text');

		vacEditor.actions.changeText =  $.extend(
			Object.create(baseAction),
			{
				id: 'changeText',
				label: 'Change Text',

				currentElement: null,
				currentMemento: null,

				run: function(element) {
					this.currentElement = element;
					this.currentMemento = this.getMemento(element);
					$contextMenu.hide();

					$elementTextEditor.val(element.jq.text());

					changeTextDialog.dialog('open');
					$elementTextEditor.focus();
				},

				updateDom: function (element, params) {
					element.jq.text(params);
				},

				getMemento: function(element) {
					//noinspection JSUnresolvedFunction False positive.
					var memento = baseAction.getMemento.call(this, element);
					memento.oldText = element.jq.text();
					return memento;
				},

				rollbackDom: function(element, memento) {
					element.jq.text(memento.oldText);
				},

				preview: function(newText) {
					this.updateDom(this.currentElement, newText);
				},

				confirmEdit: function(newText) {
					//Get rid of the preview.
					this.rollbackDom(this.currentElement, this.currentMemento);

					//Apply the final text.
					vacEditor.commitAction(this.currentElement, this, newText);

					this.currentMemento = null;
					this.currentElement = null;

					//Done.
					vacEditor.currentAction = null;
					vacEditor.clearRedoStack();
					vacEditor.stopEditing();
				},

				cancel: function() {
					this.rollbackDom(this.currentElement, this.currentMemento);
					vacEditor.currentAction = null;
					vacEditor.stopEditing();

					this.currentElement = null;
					this.currentMemento = null;
				}
			}
		);

		$('#vac-change-text-menu').click(function() {
			var element = childWindow.vacInjected.getSelectedElement();
			vacEditor.actions.changeText.run(element);
		});

		var changeTextDialog = $('#vac-dialog-change-text').dialog({
			title: 'Change Text',
			width: 350,
			autoOpen: false,
			modal: true,
			buttons: [
				{
					text: 'Cancel',
					'class': 'button',
					click: function() {
						changeTextDialog.dialog('close');
						vacEditor.actions.changeText.cancel();
					}
				},
				{
					text: 'Apply',
					'class': 'button button-primary',
					click: function () {
						changeTextDialog.dialog('close');
						vacEditor.actions.changeText.confirmEdit(
							$elementTextEditor.val()
						);
					}
				}
			]
		});

		$elementTextEditor.on('keyup', function() {
			vacEditor.actions.changeText.preview($elementTextEditor.val());
		});

		//------------------------------------------------------------------------
		// Custom CSS/JS code editor

		vacEditor.actions.addCustomCss =  $.extend(
			Object.create(baseAction),
			{
				id: 'addCustomCss',
				label: 'Add CSS',

				isPreviewEnabled: false,
				tempCustomCss: {},
				detachedStyleTag: $('<style type="text/css"></style>'),

				getStyleTag: function(useDetachedTag) {
					if (useDetachedTag) {
						return this.detachedStyleTag;
					}

					if (!$childDocument) {
						console.warn('Tried to get the style tag but frame is not ready');
						return $();
					}

					var $head = $childDocument.find('head');
					if ($head.length < 1) {
						return $();
					}

					var $previewStyle = $head.find('style#_vac_custom_css_preview');
					if ($previewStyle.length < 1) {
						$previewStyle = $('<style id="_vac_custom_css_preview" type="text/css"></style>').appendTo($head);
					}

					return $previewStyle;
				},

				enablePreview: function() {
					if (this.isPreviewEnabled) {
						throw "Can't enable CSS preview because it's already enabled.";
					}
					this.detachedStyleTag.html(this.getStyleTag(false).html());
					this.isPreviewEnabled = true;
				},

				disablePreview: function() {
					if (!this.isPreviewEnabled) {
						throw "Can't disable CSS preview because it isn't enabled.";
					}
					this.getStyleTag(false).html(this.detachedStyleTag.html());
					this.isPreviewEnabled = false;
				},

				setPreviewCss: function(combinedCss) {
					this.getStyleTag(false).html(combinedCss);
				},

				refreshCssPreview: function () {
					var css = '',
						self = this;

					_.forEach(vacApp.allActorFilters, function(actorFilter) {
						if (vacApp.currentActorFilter.isSubsetOf(actorFilter)) {
							css = css + '\n' + _.get(self.tempCustomCss, actorFilter.getContextKey(), '');
						}
					});

					this.setPreviewCss(css);
				},

				/**
				 * @param {VACActorFilter} actorFilter
				 */
				getCssChangeKey: function(actorFilter) {
					return VACChange.buildContextKey('head', this.id, anyPagePreset, actorFilter);
				},

				getMemento: function(element) {
					//noinspection JSUnresolvedFunction False positive.
					var memento = baseAction.getMemento.call(this, element);
					memento.oldCss = this.getStyleTag(this.isPreviewEnabled).html();
					return memento;
				},

				updateDom: function (element, css, change, useDetachedTag) {
					if (typeof useDetachedTag === 'undefined') {
						useDetachedTag = this.isPreviewEnabled;
					}

					var styleTag = this.getStyleTag(useDetachedTag);
					var combinedCss = styleTag.html(),
						startMarker = '',
						endMarker = '';
					if (change) {
						//Remove the previous CSS associated with this change.
						var safeKey = change.getContextKey().replace('*/', '*-/');
						startMarker = '/*start:' + safeKey + '*/';
						endMarker = '/*end:' + safeKey + '*/';
						var startPos = combinedCss.indexOf(startMarker),
							endPos = combinedCss.indexOf(endMarker);
						if ((startPos >= 0) && (endPos >= 0)) {
							combinedCss = combinedCss.substr(0, startPos) + combinedCss.substr(endPos + endMarker.length)
						}
					}

					if (css !== '') {
						css = startMarker + '\n' + css + endMarker;
					}

					styleTag.html(combinedCss + css);
				},

				rollbackDom: function(element, memento) {
					this.getStyleTag(this.isPreviewEnabled).html(memento.oldCss);
				}
			}
		);

		var customCodeDialog = $('#vac-custom-code-dialog').dialog({
			title: 'Edit CSS',
			width: 600,
			height: 400,
			resizable: true,
			autoOpen: false,
			modal: true,
			buttons: [
				{
					text: 'Cancel',
					'class': 'button',
					click: function() {
						customCodeDialog.dialog('close');
					}
				},
				{
					text: 'Apply',
					'class': 'button button-primary',
					click: function () {
						customCodeDialog.dialog('close');

						//Save changes made to tempCustomCss.
						var action = vacEditor.actions.addCustomCss;

						vacEditor.beginUndoBatch();
						_.forEach(vacApp.allActorFilters, function(actorFilter) {
							var css = _.get(action.tempCustomCss, actorFilter.getContextKey(), ''),
								previousChange = vacApp.changes.get(action.getCssChangeKey(actorFilter));

							if (css === '') {
								if (previousChange !== null) {
									vacApp.deleteChange(previousChange);
								}
							} else {
								var change = new VACChange(action.id, 'head', css, actorFilter, anyPagePreset);
								//Apply the new CSS even if it's unchanged to ensure proper actor-based order.
								vacEditor.commitChange(change);
							}
						});
						vacEditor.endUndoBatch();
					}
				}
			],

			open: function() {
				vacApp.isCodeDialogOpen = true;
				codeEditor.refresh();

				//Make the widget overlay transparent to make it easy to see the effect of custom CSS.
				//noinspection CssUnusedSymbol
				$('head').append(
					'<style id="_vac_make_overlay_transparent">' +
						'.ui-widget-overlay {background: transparent;}' +
					'</style>'
				);

				var action = vacEditor.actions.addCustomCss;
				action.tempCustomCss = {};
				action.enablePreview();

				_.forEach(
					vacApp.allActorFilters,
					/**
					 * @param {VACActorFilter} filter
					 */
					function(filter) {
						var key = action.getCssChangeKey(filter),
							css = vacApp.changes.getParams(key);
						action.tempCustomCss[filter.getContextKey()] = (css !== null) ? css : '';
					}
				);

				codeEditor.setValue(_.get(action.tempCustomCss, vacApp.currentActorFilter.getContextKey(), ''));

				action.refreshCssPreview();
			},
			close: function () {
				vacApp.isCodeDialogOpen = false;
				vacEditor.actions.addCustomCss.disablePreview();

				//Remove the overlay transparency override.
				$('style#_vac_make_overlay_transparent').remove();
			}
		});

		var codeEditor = new CodeMirror(document.getElementById('vac-cc-content'), {
			value: '.test { color: blue }\n#some-id {\n\tmargin: 0;\n}',
			mode: 'css',
			theme: 'neat',
			lineNumbers: true
		});

		//When the user switches to another actor/filter, save the current CSS and display
		//the custom code that's set the new actor.
		vacApp.$watch(
			'currentActorFilter',
			/**
			 * @param {VACActorFilter} newFilter
			 * @param {VACActorFilter} oldFilter
			 */
			function(newFilter, oldFilter) {
				if (vacApp.isCodeDialogOpen !== true) {
					return;
				}
				var action = vacEditor.actions.addCustomCss;
				action.tempCustomCss[oldFilter.getContextKey()] = codeEditor.getValue().trim();
				codeEditor.setValue(_.get(action.tempCustomCss, newFilter.getContextKey(), ''));
			}
		);
		
		codeEditor.on('change', function() {
			var action = vacEditor.actions.addCustomCss;
			action.tempCustomCss[vacApp.currentActorFilter.getContextKey()] = codeEditor.getValue().trim();
			action.refreshCssPreview();
		});

		//------------------------------------------------------------------------
		// Settings - apply, reload, etc

		/**
		 * Apply all relevant changes to the current page.
		 */
		vacEditor.applyAllChanges = function() {
			var self = this;
			appliedChanges = [];

			vacApp.changes.forEach(function(change) {
				if (vacApp.isChangeApplicable(change)) {
					self.applyChange(change);
				}
			});
		};

		/**
		 * Revert all changes that were applied to the current page.
		 *
		 * @param {Number} [toIndex]
		 */
		vacEditor.revertAppliedChanges = function(toIndex) {
			var i, memento = null;
			if ((typeof toIndex === 'undefined') || (toIndex < 0)) {
				toIndex = 0;
			}

			for (i = appliedChanges.length - 1; i >= toIndex; i--) {
				var change = appliedChanges[i];
				if (change.actionMemento) {
					change.actionMemento.action.rollback(change.actionMemento);
				} else {
					this.warn('Tried to revert a change without a memento', change);
				}
			}
			appliedChanges.splice(toIndex);
		};

		/**
		 * Revert and reapply all changes to the current page. Useful when switching filters.
		 */
		vacEditor.reapplyAllChanges = function() {
			this.revertAppliedChanges();
			this.applyAllChanges();
		};

		//Reapply changes when the user changes the actor filter.
		vacApp.$watch(
			'currentActorFilter',
			_.debounce(function() {
				vacEditor.stopEditing();
				vacEditor.reapplyAllChanges();
			}, 500),
			{ deep: true }
		);

		//------------------------------------------------------------------------
		//Change list

		var $changeToggle = $('#vac-toggle-change-list');

		$changeToggle.click(function() {
			vacEditor.stopEditing();
		});

		//------------------------------------------------------------------------
		// Actor popup


		//------------------------------------------------------------------------
		// Context / page match popup

		var $pageMatches = $('#vac-page-matches');
		$pageMatches.find('.vac-context-menu-item').click(function() {
			$pageMatches.hide();
			$pageMatches.closest('.vac-has-popup').removeClass('vac-is-open');
		});


		//========================================================================
		// Done. Load settings and initialise the editor.
		//========================================================================

		if (_.get(editorData, 'customizations')) {
			vacApp.changes.load(editorData['customizations']);
		}
	});

}));