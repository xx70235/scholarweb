var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
(function () {
    var RiBaseSubject = (function () {
        function RiBaseSubject(subjectId, displayName, ownCapabilities) {
            this.subjectId = subjectId;
            this.displayName = displayName;
            this.ownCapabilities = ownCapabilities ? ownCapabilities : {};
        }
        RiBaseSubject.prototype.hasCap = function (capability) {
            if (capability === 'exist') {
                return true;
            }
            var result = this.hasOwnCap(capability);
            if (result === null) {
                result = false;
            }
            return result;
        };
        /**
         * Get the capability setting directly from this subject, ignoring capabilities
         * granted by user roles or the Super Admin flag.
         *
         * Returns NULL for capabilities that are neither granted nor denied.
         */
        RiBaseSubject.prototype.hasOwnCap = function (capability) {
            //The do_not_allow capability is special in that nobody has it, not even Super Admin.
            if (capability === 'do_not_allow') {
                return false;
            }
            if (this.ownCapabilities.hasOwnProperty(capability)) {
                return this.ownCapabilities[capability];
            }
            return null;
        };
        return RiBaseSubject;
    }());
    var RiRole = (function (_super) {
        __extends(RiRole, _super);
        function RiRole(slug, displayName, ownCapabilities) {
            if (ownCapabilities === void 0) { ownCapabilities = {}; }
            _super.call(this, 'role:' + slug, displayName, ownCapabilities);
            this.slug = slug;
        }
        RiRole.prototype.hasOwnCap = function (capability) {
            //In WordPress, a role name is also a capability name. Users that have the role "foo" always
            //have the "foo" capability. It's debatable whether the role itself actually has that capability
            //(WP_Role says no), but it's convenient to treat it that way.
            if (capability === this.slug) {
                return true;
            }
            return _super.prototype.hasOwnCap.call(this, capability);
        };
        return RiRole;
    }(RiBaseSubject));
    var RiSuperAdmin = (function (_super) {
        __extends(RiSuperAdmin, _super);
        function RiSuperAdmin() {
            _super.call(this, 'special:super_admin', 'Super Admin');
        }
        RiSuperAdmin.getInstance = function () {
            if (RiSuperAdmin.instance === null) {
                RiSuperAdmin.instance = new RiSuperAdmin();
            }
            return RiSuperAdmin.instance;
        };
        RiSuperAdmin.prototype.hasOwnCap = function (capability) {
            //The Super Admin has all possible capabilities except the special "do_not_allow" flag.
            return (capability !== 'do_not_allow');
        };
        RiSuperAdmin.instance = null;
        return RiSuperAdmin;
    }(RiBaseSubject));
    var RiUser = (function (_super) {
        __extends(RiUser, _super);
        function RiUser(userLogin, displayName, capabilities, roles, isSuperAdmin, userId) {
            if (isSuperAdmin === void 0) { isSuperAdmin = false; }
            _super.call(this, 'user:' + userLogin, displayName, capabilities);
            this.isSuperAdmin = false;
            this.roles = [];
            this.userLogin = userLogin;
            this.isSuperAdmin = isSuperAdmin;
            this.userId = userId ? userId : 0;
            for (var _i = 0, roles_1 = roles; _i < roles_1.length; _i++) {
                var role = roles_1[_i];
                this.roles.push(role);
            }
        }
        RiUser.prototype.hasCap = function (capability) {
            var hasOwnCap = this.hasOwnCap(capability);
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
            var hasCap = null;
            for (var _i = 0, _a = this.roles; _i < _a.length; _i++) {
                var role = _a[_i];
                var roleHasCap = role.hasOwnCap(capability);
                if (roleHasCap !== null) {
                    hasCap = roleHasCap;
                }
            }
            return (hasCap !== null) ? hasCap : false;
        };
        return RiUser;
    }(RiBaseSubject));
    var RiRoleInfoCore = (function () {
        function RiRoleInfoCore(config) {
            this.roles = {};
            this.loadedUsers = {};
            this.currentUser = null;
            //Let scripts that import this library use inner classes if they need them.
            //noinspection JSUnusedGlobalSymbols
            this.RiBaseSubject = RiBaseSubject;
            //noinspection JSUnusedGlobalSymbols
            this.RiRole = RiRole;
            //noinspection JSUnusedGlobalSymbols
            this.RiUser = RiUser;
            //noinspection JSUnusedGlobalSymbols
            this.RiSuperAdmin = RiSuperAdmin;
            //Load roles.
            for (var _i = 0, _a = config['roles']; _i < _a.length; _i++) {
                var roleData = _a[_i];
                var role = new RiRole(roleData.slug, roleData.displayName, roleData.capabilities);
                this.roles[role.slug] = role;
            }
            //Load users.
            var currentUserId = parseInt(config['currentUserId']);
            for (var _b = 0, _c = config['users']; _b < _c.length; _b++) {
                var userData = _c[_b];
                var userRoles = [];
                for (var _d = 0, _e = userData.roles; _d < _e.length; _d++) {
                    var slug = _e[_d];
                    var role = this.getRole(slug);
                    if (role) {
                        userRoles.push(role);
                    }
                }
                var user = new RiUser(userData.userLogin, userData.displayName, userData.capabilities, userRoles, userData.isSuperAdmin, userData.userId);
                this.loadedUsers[user.userId] = user;
                if (user.userId === currentUserId) {
                    this.currentUser = user;
                }
            }
        }
        Object.defineProperty(RiRoleInfoCore.prototype, "rolesByName", {
            get: function () {
                var result = [];
                for (var slug in this.roles) {
                    result.push(this.roles[slug]);
                }
                result.sort(function (a, b) {
                    if (a.displayName < b.displayName) {
                        return -1;
                    }
                    else if (a.displayName > b.displayName) {
                        return 1;
                    }
                    return 0;
                });
                return result;
            },
            enumerable: true,
            configurable: true
        });
        RiRoleInfoCore.prototype.getRole = function (slug) {
            if (this.roles.hasOwnProperty(slug)) {
                return this.roles[slug];
            }
            return null;
        };
        RiRoleInfoCore.prototype.getCurrentUser = function () {
            return this.currentUser;
        };
        RiRoleInfoCore.prototype.getUser = function (id) {
            if (id in this.loadedUsers) {
                return this.loadedUsers[id];
            }
            return null;
        };
        RiRoleInfoCore.prototype.getSubject = function (id) {
            var prefixLength = id.indexOf(':');
            if ((prefixLength < 1) || (id.length - prefixLength < 0)) {
                throw {
                    message: 'Invalid subject ID "' + id + '"',
                    subjectId: id
                };
            }
            var prefix = id.substr(0, prefixLength);
            if (prefix === 'role') {
                var slug = id.substr(prefixLength + 1);
                if (this.roles.hasOwnProperty(slug)) {
                    return this.roles[slug];
                }
            }
            else if (prefix === 'user') {
                //This could be optimized.
                var login = id.substr(prefixLength + 1);
                for (var userId in this.loadedUsers) {
                    if (this.loadedUsers[userId].userLogin === login) {
                        return this.loadedUsers[userId];
                    }
                }
            }
            else if (id === 'special:super_admin') {
                return RiSuperAdmin.getInstance();
            }
            if (console && console.warn) {
                console.warn('Subject not found: "' + id + '"');
            }
            return null;
        };
        return RiRoleInfoCore;
    }());
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
        }
        else if (typeof module === 'object' && module.exports) {
            //Node. Does not work with strict CommonJS, but
            //only CommonJS-like environments that support module.exports,
            //like Node.
            module.exports = factory();
        }
        else {
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
//# sourceMappingURL=roleinfo.js.map