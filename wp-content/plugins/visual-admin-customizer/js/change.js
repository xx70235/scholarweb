define(['actor-filter', 'page-filter'], function(VACActorFilter, VACPageFilter) {
	/**
	 *
	 * @param {String} action
	 * @param {String} selector
	 * @param {Object} params
	 * @param {VACActorFilter} actorFilter
	 * @param {VACPageFilter} pageFilter
	 * @constructor
	 */
	function VACChange(action, selector, params, actorFilter, pageFilter) {
		this.action = action || '';
		this.selector = selector || 0;
		this.params = params || null;

		//Used for undo/redo.
		this.actionMemento = null;

		/**
		 *
		 * @type {VACActorFilter}
		 */
		this.actorFilter = actorFilter || null;

		/**
		 * @type {VACPageFilter}
		 */
		this.pageFilter = pageFilter || null;
	}

	/**
	 *
	 * @param {string} selector
	 * @param {string} action
	 * @param {VACPageFilter} pageFilter
	 * @param {VACActorFilter} actorFilter
	 */
	VACChange.buildContextKey = function(selector, action, pageFilter, actorFilter) {
		return [
			selector,
			action,
			pageFilter.getContextKey(),
			actorFilter.getContextKey()
		].join('|');
	};

	VACChange.prototype.getContextKey = function() {
		return VACChange.buildContextKey(this.selector, this.action, this.pageFilter, this.actorFilter);
	};

	VACChange.prototype.toJS = function() {
		return {
			action: this.action,
			selector: this.selector,
			params: this.params,
			actorFilter: this.actorFilter.toJS(),
			pageFilter: this.pageFilter.toJS()
		};
	};

	VACChange.fromJS = function(properties) {
		return new VACChange(
			properties.action,
			properties.selector,
			properties.params,
			VACActorFilter.fromJS(properties.actorFilter),
			VACPageFilter.fromJS(properties.pageFilter)
		);
	};

	/**
	 * @param {Object} pageSpec
	 * @param {VACActorFilter} actorFilter
	 */
	VACChange.prototype.isApplicable = function(pageSpec, actorFilter) {
		return this.pageFilter.matchesProps(pageSpec) && actorFilter.isSubsetOf(this.actorFilter);
	};

	return VACChange;
});
