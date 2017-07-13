define(function (require) {
	"use strict";
	var VACChange = require('change');

	function VACChangeStore() {
		this.changesByKey = {};
		this.orderedChanges = [];
	}

	VACChangeStore.prototype.set = function (change, index) {
		var contextKey = change.getContextKey();
		if (typeof index === 'undefined') {
			index = null;
		}

		var isNew = true, oldEntry;
		if (this.changesByKey.hasOwnProperty(contextKey)) {
			isNew = false;
			oldEntry = this.changesByKey[contextKey];
		}
		this.changesByKey[contextKey] = change;

		var lowestModifiedIndex = null;
		if (isNew) {
			//New entry.
			if (index === null) {
				this.orderedChanges.push(change);
				lowestModifiedIndex = this.orderedChanges.length - 1;
			} else {
				this.orderedChanges.splice(index, 0, change);
				lowestModifiedIndex = index;
			}
		} else {
			//Replace an existing entry.
			if (index === null) {
				index = this.orderedChanges.length - 1;
			}

			var oldIndex = this.orderedChanges.indexOf(oldEntry);
			//Remove the old entry.
			this.orderedChanges.splice(oldIndex, 1);
			//Insert the new entry in the new spot.
			this.orderedChanges.splice(index, 0, change);

			lowestModifiedIndex = Math.min(index, oldIndex);
		}

		return lowestModifiedIndex;
	};

	/**
	 * Remove a change.
	 *
	 * @param {string|VACChange} contextKey
	 * @return {Number|null}
	 */
	VACChangeStore.prototype.remove = function (contextKey) {
		if (contextKey instanceof VACChange) {
			contextKey = contextKey.getContextKey();
		}

		if (!this.exists(contextKey)) {
			//Nothing to do.
			return null;
		}

		var index = this.orderedChanges.indexOf(this.changesByKey[contextKey]);
		this.orderedChanges.splice(index, 1);
		delete this.changesByKey[contextKey];

		return index;
	};

	/**
	 * Get a change by key.
	 * @param {string} contextKey
	 * @return {VACChange|null}
	 */
	VACChangeStore.prototype.get = function(contextKey) {
		if (this.changesByKey.hasOwnProperty(contextKey)) {
			return this.changesByKey[contextKey];
		}
		return null;
	};

	VACChangeStore.prototype.exists = function (contextKey) {
		return this.changesByKey.hasOwnProperty(contextKey);
	};

	VACChangeStore.prototype.getParams = function (contextKey) {
		if (this.changesByKey.hasOwnProperty(contextKey)) {
			return this.changesByKey[contextKey].params;
		}
		return null;
	};

	VACChangeStore.prototype.getState = function (contextKey) {
		if (this.exists(contextKey)) {
			return {
				change: this.changesByKey[contextKey],
				index: this.orderedChanges.indexOf(this.changesByKey[contextKey]),
				contextKey: contextKey
			};
		} else {
			return {
				change: null,
				index: null,
				contextKey: contextKey
			};
		}
	};

	/**
	 * @param {Object} state
	 * @return {Object}
	 */
	VACChangeStore.prototype.restoreState = function (state) {
		var startIndex;
		if (state.change === null) {
			startIndex = this.remove(state.contextKey);
		} else {
			startIndex = this.set(state.change, state.index);
		}

		if (startIndex === null) {
			return {
				unaffected: this.orderedChanges.slice(0),
				affected: []
			};
		} else {
			return {
				unaffected: this.orderedChanges.slice(0, startIndex),
				affected  : this.orderedChanges.slice(startIndex)
			}
		}
	};

	VACChangeStore.prototype.forEach = function (callback) {
		for (var i = 0; i < this.orderedChanges.length; i++) {
			callback(this.orderedChanges[i], i);
		}
	};

	VACChangeStore.prototype.toJS = function () {
		var changeList = [];
		for(var i = 0; i < this.orderedChanges.length; i++) {
			changeList.push(this.orderedChanges[i].toJS());
		}

		return {
			format: {
				name: 'VAC change list',
				version: 1
			},
			changes: changeList
		};
	};

	VACChangeStore.prototype.toJSON = function() {
		//noinspection AmdModulesDependencies
		return JSON.stringify(this.toJS());
	};

	VACChangeStore.prototype.load = function (settings) {
		this.orderedChanges = [];
		this.changesByKey = {};

		for (var i = 0; i < settings.changes.length; i++) {
			var change = VACChange.fromJS(settings.changes[i]);

			this.orderedChanges.push(change);
			this.changesByKey[change.getContextKey()] = change;
		}
	};

	return VACChangeStore;
});