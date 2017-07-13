define(['vendor/lodash'], function(_) {
	/**
	 * Filters WordPress pages based on a few simple criteria.
	 * This class is used to determine which customizations to apply to which pages.
	 *
	 * @param {String} [predicate]
	 * @param {String} [operand]
	 * @constructor
	 */
	function VACPageFilter(predicate, operand) {
		this.predicate = predicate || VACPageFilter.builtInPredicates.isAdmin;
		this.screenId = '';

		if ((typeof operand !== 'undefined') && (predicate === VACPageFilter.builtInPredicates.screenId)) {
			this.screenId = operand;
		}
	}

	VACPageFilter.builtInPredicates = {
		isAdmin: 'is_admin',
		screenId: 'screen_id',
		any: '*'
	};

	/**
	 * @param {String} url
	 * @param {String} [screenId]
	 * @param {boolean} [isAdmin]
	 * @returns {boolean}
	 */
	VACPageFilter.prototype.matches = function(url, screenId, isAdmin) {
		if (this.predicate === VACPageFilter.builtInPredicates.isAdmin) {
			if (typeof isAdmin === 'undefined') {
				isAdmin = /^[^?#]+?\/wp-admin\//.test(url);
			}
			//noinspection PointlessBooleanExpressionJS Cast to boolean.
			return !!isAdmin;
		} else if (this.predicate === VACPageFilter.builtInPredicates.screenId) {
			return screenId === this.screenId;
		} else if (this.predicate === VACPageFilter.builtInPredicates.any) {
			return true;
		}
		return false;
	};

	VACPageFilter.prototype.matchesProps = function(pageProperties) {
		return this.matches(
			_.get(pageProperties, 'url', ''),
			_.get(pageProperties, 'screenId', ''),
			_.get(pageProperties, 'isAdmin', false)
		);
	};

	VACPageFilter.prototype.getContextKey = function() {
		var key = this.predicate;
		if (key === VACPageFilter.builtInPredicates.screenId) {
			key = key + '=' + this.screenId;
		}
		return key;
	};

	VACPageFilter.prototype.toJS = function () {
		return {
			predicate: this.predicate,
			operand: this.screenId
		};
	};

	VACPageFilter.fromJS = function (properties) {
		return new VACPageFilter(properties.predicate, properties.operand);
	};

	VACPageFilter.prototype.clone = function() {
		return new VACPageFilter(this.predicate, this.screenId);
	};

	return VACPageFilter;
});