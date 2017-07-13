declare var wsRiRoleInfoData: any;
declare var define, module, require;

(function() {

	interface RiCapabilityMap {
		[capabilityName: string] : boolean
	}

	abstract class RiBaseSubject {
		public readonly subjectId: string;
		public readonly displayName: string;
		protected ownCapabilities: RiCapabilityMap;

		constructor(subjectId: string, displayName: string, ownCapabilities?: RiCapabilityMap) {
			this.subjectId = subjectId;
			this.displayName = displayName;
			this.ownCapabilities = ownCapabilities ? ownCapabilities : {};
		}

		public hasCap(capability: string): boolean {
			if (capability === 'exist') {
				return true;
			}

			let result = this.hasOwnCap(capability);
			if (result === null) {
				result = false;
			}
			return result;
		}

		/**
		 * Get the capability setting directly from this subject, ignoring capabilities
		 * granted by user roles or the Super Admin flag.
		 *
		 * Returns NULL for capabilities that are neither granted nor denied.
		 */
		public hasOwnCap(capability: string): boolean|null {
			//The do_not_allow capability is special in that nobody has it, not even Super Admin.
			if (capability === 'do_not_allow') {
				return false;
			}
			if (this.ownCapabilities.hasOwnProperty(capability)) {
				return this.ownCapabilities[capability];
			}
			return null;
		}
	}

	class RiRole extends RiBaseSubject {
		readonly slug: string;

		constructor(slug: string, displayName: string, ownCapabilities: RiCapabilityMap = {}) {
			super('role:' + slug, displayName, ownCapabilities);
			this.slug = slug;
		}

		public hasOwnCap(capability: string): boolean|null {
			//In WordPress, a role name is also a capability name. Users that have the role "foo" always
			//have the "foo" capability. It's debatable whether the role itself actually has that capability
			//(WP_Role says no), but it's convenient to treat it that way.
			if (capability === this.slug) {
				return true;
			}
			return super.hasOwnCap(capability);
		}
	}

	class RiSuperAdmin extends RiBaseSubject {
		private static instance: RiSuperAdmin = null;

		private constructor() {
			super('special:super_admin', 'Super Admin');
		}

		static getInstance(): RiSuperAdmin {
			if (RiSuperAdmin.instance === null) {
				RiSuperAdmin.instance = new RiSuperAdmin();
			}
			return RiSuperAdmin.instance;
		}

		public hasOwnCap(capability: string): boolean {
			//The Super Admin has all possible capabilities except the special "do_not_allow" flag.
			return (capability !== 'do_not_allow');
		}
	}

	class RiUser extends RiBaseSubject {
		readonly userId: number;
		readonly userLogin: string;
		readonly isSuperAdmin: boolean = false;
		readonly roles: RiRole[] = [];

		constructor(
			userLogin: string,
			displayName: string,
			capabilities: RiCapabilityMap,
			roles: RiRole[],
			isSuperAdmin: boolean = false,
			userId?: number
		) {
			super('user:' + userLogin, displayName, capabilities);

			this.userLogin = userLogin;
			this.isSuperAdmin = isSuperAdmin;
			this.userId = userId ? userId : 0;

			for (let role of roles) {
				this.roles.push(role);
			}
		}


		public hasCap(capability: string): boolean {
			let hasOwnCap = this.hasOwnCap(capability);
			if (hasOwnCap !== null) {
				return hasOwnCap;
			}

			if (this.isSuperAdmin) {
				return RiSuperAdmin.getInstance().hasCap(capability);
			}

			/*
			 * Warning: Poorly defined behaviour.
			 *
			 * WordPress doesn't specify how to merge conflicting capability settings from different roles.
			 * For example, lets say role A has capability "foo", but the same capability is explicitly
			 * denied for role B (i.e. $role->capabilities['foo'] === false). Depending on the order of
			 * role names in the $user->caps array, this capability could end up either enabled or disabled.
			 * WP core does not sort the capability array, so *usually* it will remain in insertion order.
			 *
			 * We try to emulate that. The last role that has an explicit setting gets the final say.
			 */

			let hasCap = null;
			for(let role of this.roles) {
				let roleHasCap = role.hasOwnCap(capability);
				if (roleHasCap !== null) {
					hasCap = roleHasCap;
				}
			}

			return (hasCap !== null) ? hasCap : false;
		}
	}

	class RiRoleInfoCore {
		readonly roles: {[slug: string]: RiRole} = {};

		private loadedUsers: {[userId: number]: RiUser} = {};
		private currentUser: RiUser = null;

		//Let scripts that import this library use inner classes if they need them.
		//noinspection JSUnusedGlobalSymbols
		public readonly RiBaseSubject = RiBaseSubject;
		//noinspection JSUnusedGlobalSymbols
		public readonly RiRole = RiRole;
		//noinspection JSUnusedGlobalSymbols
		public readonly RiUser = RiUser;
		//noinspection JSUnusedGlobalSymbols
		public readonly RiSuperAdmin = RiSuperAdmin;

		constructor(config: Object) {
			//Load roles.
			for(let roleData of config['roles']) {
				let role = new RiRole(roleData.slug, roleData.displayName, roleData.capabilities);
				this.roles[role.slug] = role;
			}

			//Load users.
			let currentUserId = parseInt(config['currentUserId']);
			for(let userData of config['users']) {
				let userRoles = [];
				for (let slug of userData.roles) {
					let role = this.getRole(slug);
					if (role) {
						userRoles.push(role);
					}
				}

				let user = new RiUser(
					userData.userLogin,
					userData.displayName,
					userData.capabilities,
					userRoles,
					userData.isSuperAdmin,
					userData.userId
				);

				this.loadedUsers[user.userId] = user;

				if (user.userId === currentUserId) {
					this.currentUser = user;
				}
			}

		}

		get rolesByName(): RiRole[] {
			let result = [];
			for (let slug in this.roles) {
				result.push(this.roles[slug]);
			}
			result.sort(function(a, b) {
				if (a.displayName < b.displayName) {
					return -1;
				} else if (a.displayName > b.displayName) {
					return 1;
				}
				return 0;
			});
			return result;
		}

		getRole(slug: string) {
			if (this.roles.hasOwnProperty(slug)) {
				return this.roles[slug];
			}
			return null;
		}

		getCurrentUser() {
			return this.currentUser;
		}

		getUser(id: number) {
			if (id in this.loadedUsers) {
				return this.loadedUsers[id];
			}
			return null;
		}

		getSubject(id: string): RiBaseSubject|null {
			var prefixLength = id.indexOf(':');
			if ((prefixLength < 1) || (id.length - prefixLength < 0)) {
				throw {
					message: 'Invalid subject ID "' + id + '"',
					subjectId: id
				}
			}

			let prefix = id.substr(0, prefixLength);
			if (prefix === 'role') {
				let slug = id.substr(prefixLength + 1);
				if (this.roles.hasOwnProperty(slug)) {
					return this.roles[slug];
				}
			} else if (prefix === 'user') {
				//This could be optimized.
				let login = id.substr(prefixLength + 1);
				for (let userId in this.loadedUsers) {
					if (this.loadedUsers[userId].userLogin === login) {
						return this.loadedUsers[userId];
					}
				}
			} else if (id === 'special:super_admin') {
				return RiSuperAdmin.getInstance();
			}

			if (console && console.warn) {
				console.warn('Subject not found: "' + id + '"');
			}

			return null;
		}
	}


	//AMD and global exports.
	//------------------------------------------------------------------------
	//Based on umd/returnExportsGlobal.js

	//noinspection ThisExpressionReferencesGlobalObjectJS
	(function (root, factory) {
		if (typeof define === 'function' && define.amd) {
			//AMD. Register as an anonymous module.
			define(function () {
				return (root.riRoleInfo = factory());
			});
		} else if (typeof module === 'object' && module.exports) {
			//Node. Does not work with strict CommonJS, but
			//only CommonJS-like environments that support module.exports,
			//like Node.
			module.exports = factory();
		} else {
			//Browser globals
			root.riRoleInfo = factory();
		}
	}(this, function () {
		//Just return a value to define the module export.
		//This example returns an object, but the module
		//can return a function as the exported value.
		return new RiRoleInfoCore(wsRiRoleInfoData);
	}));

	//------------------------------------------------------------------------

})();