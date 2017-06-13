/*!
 * @license
 * Copyright MatthiasWeb (Matthias Günter)
 * https://matthias-web.com
 */
/* global jQuery rmlOpts */

function RMLFormat() {
    var args = arguments;
    return args[0].replace(/{(\d+)}/g, function(match, number) { 
      return typeof args[(+number)+1] != 'undefined'
        ? args[(+number)+1]
        : match
      ;
    });
}

function RMLStartsWith(str, search) {
    return str.indexOf(search) === 0;
}

function RMLisDefined(attr) {
    if (typeof attr !== typeof undefined && attr !== false && attr !== null) {
        return true;
    }
    return false;
}

function RMLWpIs(name) {
    return typeof window.wp !== "undefined" && typeof window.wp[name] !== "undefined";
}

function RMLisAIO(object) {
    return object instanceof jQuery && document.body.contains(object[0]) && object.data("allInOneTree");
}

function RMLDebug(message /*, messages ... */) {
    if (typeof rmlOpts === "object" && rmlOpts.debug) {
        var args = jQuery.makeArray(arguments);
        args.unshift("[RML_DEBUG]");
        console.debug.apply(console, args);
    }
}

/* @see http://stackoverflow.com/questions/901115/how-can-i-get-query-string-values-in-javascript */
function RMLUrlParams(aIgnore) {
    return (function(a) {
        if (a == "") return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p=a[i].split('=', 2);
            if (p.length != 2) continue;
            if (aIgnore && aIgnore.length > 0 && jQuery.inArray(p[0], aIgnore) > -1) continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    })(window.location.search.substr(1).split('&'));
}

/** Function.prototype.bind polyfill */
Function.prototype.bind=(function(){}).bind||function(b){if(typeof this!=="function"){throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");}function c(){}var a=[].slice,f=a.call(arguments,1),e=this,d=function(){return e.apply(this instanceof c?this:b||window,f.concat(a.call(arguments)));};c.prototype=this.prototype;d.prototype=new c();return d;};

/** ReplaceWith should return the new object */
if (typeof jQuery.fn.replaceWithPush !== "function") {
    jQuery.fn.replaceWithPush = function(a) {
        var $ = jQuery, $a = $(a);
        this.replaceWith($a);
        return $a;
    };
}

/**
 * Hook System
 */
var RML_HOOK = {
    hooks: [],

    register: function(name, callback) {
        var names = name.split(" "),
            curName;
        for (var i = 0; i < names.length; i++) {
            curName = names[i];
            if ('undefined' == typeof(RML_HOOK.hooks[curName]))
                RML_HOOK.hooks[curName] = [];
            RML_HOOK.hooks[curName].push(callback);
        }
    },

    call: function(name, args, context) {
        RMLDebug("Call js hook '" + name + "' with arguments", args);
        if ('undefined' != typeof(RML_HOOK.hooks[name])) {
            for (var i = 0; i < RML_HOOK.hooks[name].length; ++i) {
                if (typeof args === "object") {
                    if (Object.prototype.toString.call(args) === '[object Array]') {
                        args.push(jQuery);
                    }else{
                        args = [args, jQuery];
                    }
                    
                    if (false == RML_HOOK.hooks[name][i].apply(context, args)) {
                        break;
                    } 
                }else{
                    if (false == RML_HOOK.hooks[name][i].apply(context, [ jQuery ])) {
                        break;
                    }
                }
            }
        }
    },

    exists: function(name) {
        return 'undefined' != typeof(RML_HOOK.hooks[name]);
    }
};

/**
 * General informations
 */
window.rml = {
    hooks: RML_HOOK,
    typeAccept: { }
}