/* global riRoleInfo:RiRoleInfoCore */

define(['vendor/vue', 'vendor/lodash'], function(Vue, _) {
	function VACActorFilter(settings) {
		this.settings = settings || {};
		//noinspection JSUnusedGlobalSymbols
		this.tooltip = '';
	}

	VACActorFilter.prototype.set = function(subject, isIncluded) {
		if (subject instanceof riRoleInfo.RiBaseSubject) {
			subject = subject.subjectId;
		}

		//Convert to boolean.
		isIncluded = !!isIncluded;

		if (typeof Vue !== 'undefined') {
			//Let Vue know about added properties.
			Vue.set(this.settings, subject, isIncluded);
		} else {
			//Just set it directly.
			this.settings[subject] = isIncluded;
		}
		return this;
	};

	/**
	 * @param {String|RiBaseSubject} subject
	 * @returns {VACActorFilter}
	 */
	VACActorFilter.prototype.include = function(subject) {
		this.set(subject, true);
		return this;
	};

	/**
	 * @param {String|RiBaseSubject} subject
	 * @returns {VACActorFilter}
	 */
	VACActorFilter.prototype.exclude = function(subject) {
		this.set(subject, false);
		return this;
	};

	VACActorFilter.prototype.clear = function() {
		this.settings = {};
		return this;
	};

	/**
	 * Set a tooltip for the filter.
	 * The tooltip is only used in the UI. It is not saved when storing changes.
	 *
	 * @param {String} text
	 * @return {VACActorFilter}
	 */
	VACActorFilter.prototype.setTooltip = function(text) {
		//noinspection JSUnusedGlobalSymbols
		this.tooltip = text;
		return this;
	};

	VACActorFilter.prototype.isIncluded = function(actorId) {
		if (this.settings.hasOwnProperty(actorId)) {
			return this.settings[actorId];
		}

		var userPrefix = 'user:';
		if (actorId.substr(0, userPrefix.length) === userPrefix) {
			//The user is included if at least one of their roles is included.
			var user =  riRoleInfo.getSubject(actorId),
				roles = (user && user.roles) ? user.roles : [],
				excludedRoleCount = 0;

			for (var i = 0; i < roles.length; i++) {
				if (this.isIncluded(roles[i].subjectId)) {
					return true;
				} else {
					excludedRoleCount++;
				}
			}

			if (excludedRoleCount > 0) {
				return false;
			}

			//Deliberate fall-through for users who have no roles.
		}

		var allActors = '*';
		if (actorId === allActors) {
			return false;
		} else {
			//Fallback: There are no settings for actorId, so use the global setting.
			return this.isIncluded(allActors);
		}
	};

	VACActorFilter.prototype.clone = function() {
		var settings = {}, key;

		//Shallow copy.
		for (key in this.settings) {
			if (this.settings.hasOwnProperty(key)) {
				settings[key] = this.settings[key];
			}
		}

		return new VACActorFilter(settings);
	};

	/**
	 * @param {VACActorFilter} otherFilter
	 * @returns {boolean}
	 */
	VACActorFilter.prototype.equals = function(otherFilter) {
		var key;
		for (key in this.settings) {
			if (!this.settings.hasOwnProperty(key)) {
				continue;
			}
			if (!otherFilter.settings.hasOwnProperty(key) || (otherFilter.settings[key] !== this.settings[key])) {
				return false;
			}
		}

		for (key in otherFilter.settings) {
			if (!otherFilter.settings.hasOwnProperty(key)) {
				continue;
			}
			if (!this.settings.hasOwnProperty(key) || (otherFilter.settings[key] !== this.settings[key])) {
				return false;
			}
		}
		return true;
	};

	/**
	 * Get a unique key for this filter configuration.
	 *
	 * @returns {string}
	 */
	VACActorFilter.prototype.getContextKey = function() {
		return _(this.settings)
			.toPairs()
			.sortBy([0]) //Sort by key.
			.map(function(value) {
				//Example: "foo=1"
				return value[0] + '=' + (value[1] ? 1 : 0);
			})
			.value()
			.join(';')
	};

	/**
	 * Check if another filter includes all/some of the subjects included by this filter.
	 *
	 * This IS NOT a proper subset calculation algorithm. It's just a heuristic that works
	 * with a limited number of well-known filter types: "Everyone", "Everyone except user X",
	 * "Roles A, B and C".
	 *
	 * @param {VACActorFilter} otherFilter
	 * @returns {boolean}
	 */
	VACActorFilter.prototype.isSubsetOf = function(otherFilter) {
		var defaultSetting = this.isIncluded('*');

		//All: If this filter includes everyone by default, the other filter must do the same.
		if ( defaultSetting && !otherFilter.isIncluded('*') ) {
			return false;
		}

		//Roles: The other filter must include at least one of the roles that this filter includes.
		var includedRoles = [],
			rolePrefix = 'role:';
		if (!defaultSetting) {
			for (var subjectId in this.settings) {
				if (!this.settings.hasOwnProperty(subjectId)) {
					continue;
				}

				if (subjectId.substr(0, rolePrefix.length) === rolePrefix) {
					if (this.settings[subjectId]) {
						includedRoles.push(subjectId);
					}
				}
			}

			if (includedRoles.length > 0) {
				var matchFound = false;
				for(var i = 0; i < includedRoles.length; i++) {
					if (otherFilter.isIncluded(includedRoles[i])) {
						matchFound = true;
						break;
					}
				}

				if (!matchFound) {
					return false;
				}
			}
		}

		//The other filter must not exclude any users that this filter would include.
		//Since it would be impractical to load and check every user, we just check per-user settings.
		var userPrefix = 'user:';
		for (subjectId in otherFilter.settings) {
			if (!otherFilter.settings.hasOwnProperty(subjectId)) {
				continue;
			}

			if ((subjectId.substr(0, userPrefix.length) === userPrefix) && !otherFilter.settings[subjectId]) {
				if (_.get(this.settings, subjectId, defaultSetting)) {
					return false;
				}
			}
		}

		//Technically, a filter that matches nothing is a subset of any other filter,
		//but it's more convenient to treat it as a special case.
		//noinspection RedundantIfStatementJS
		if (!_.some(this.settings)) {
			return this.equals(otherFilter);
		}

		return true;
	};

	VACActorFilter.prototype.toJS = function() {
		var settings = _.clone(this.settings);

		//Any role setting that matches the default can be safely discarded.
		var defaultSetting = _.get(settings, '*', false),
			rolePrefix = 'role:';

		settings = _.omitBy(
			settings,
			function(include, subjectId) {
				return (include === defaultSetting) && (subjectId.substr(0, rolePrefix.length) === rolePrefix);
			}
		);

		return settings;
	};

	VACActorFilter.fromJS = function (properties) {
		return new VACActorFilter(_.clone(properties));
	};

	//noinspection JSUnusedGlobalSymbols (It's used in the template.)
	VACActorFilter.prototype.getSummary = function() {
		var count = 0,
			defaultSetting = this.settings.hasOwnProperty('*') ? this.settings['*'] : false,
			included = [],
			excluded = [],
			nameList;

		for (var subjectId in this.settings) {
			if (!this.settings.hasOwnProperty(subjectId)) {
				continue;
			}
			count++;

			if (subjectId !== '*') {
				if (this.settings[subjectId]) {
					included.push(subjectId);
				} else {
					excluded.push(subjectId);
				}
			}
		}

		function formatSubjectList(ids, maxNamesToShow) {
			maxNamesToShow = Math.min(maxNamesToShow, ids.length);

			nameList = ids.slice(0, maxNamesToShow).map(function(subjectId) {
				var subject = riRoleInfo.getSubject(subjectId);
				if (!subject) {
					return subjectId;
				} else if (subject instanceof riRoleInfo.RiUser) {
					return subject.userLogin;
				}
				return subject.displayName;
			}).join(', ');

			if (ids.length > maxNamesToShow) {
				nameList = nameList + ' and ' + (ids.length - maxNamesToShow) + ' more';
			}

			return nameList;
		}

		if (defaultSetting) {
			if (excluded.length === 0) {
				return 'All users';
			} else {
				return 'Everyone but ' + formatSubjectList(excluded, 4);
			}
		} else {
			if (included.length === 0) {
				return 'Nobody';
			} else {
				return formatSubjectList(included, 4);
			}
		}
	};

	VACActorFilter.prototype.getComputedProp = function () {
		var roles = riRoleInfo.rolesByName,
			self = this;

		return {
			get: function() {
				var result = [];
				for (var i = 0; i < roles.length; i++) {
					if (self.isIncluded(roles[i].subjectId)) {
						result.push(roles[i].subjectId);
					}
				}
				return result;
			},
			set: function(includedIds) {
				for (var i = 0; i < roles.length; i++) {
					self.set(roles[i].subjectId, includedIds.indexOf(roles[i].subjectId) >= 0);
				}
			}
		};
	};

	return VACActorFilter;
});

