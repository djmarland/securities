require=(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
module.exports = function(opts) {
  return new ElementClass(opts)
}

function indexOf(arr, prop) {
  if (arr.indexOf) return arr.indexOf(prop)
  for (var i = 0, len = arr.length; i < len; i++)
    if (arr[i] === prop) return i
  return -1
}

function ElementClass(opts) {
  if (!(this instanceof ElementClass)) return new ElementClass(opts)
  var self = this
  if (!opts) opts = {}

  // similar doing instanceof HTMLElement but works in IE8
  if (opts.nodeType) opts = {el: opts}

  this.opts = opts
  this.el = opts.el || document.body
  if (typeof this.el !== 'object') this.el = document.querySelector(this.el)
}

ElementClass.prototype.add = function(className) {
  var el = this.el
  if (!el) return
  if (el.className === "") return el.className = className
  var classes = el.className.split(' ')
  if (indexOf(classes, className) > -1) return classes
  classes.push(className)
  el.className = classes.join(' ')
  return classes
}

ElementClass.prototype.remove = function(className) {
  var el = this.el
  if (!el) return
  if (el.className === "") return
  var classes = el.className.split(' ')
  var idx = indexOf(classes, className)
  if (idx > -1) classes.splice(idx, 1)
  el.className = classes.join(' ')
  return classes
}

ElementClass.prototype.has = function(className) {
  var el = this.el
  if (!el) return
  var classes = el.className.split(' ')
  return indexOf(classes, className) > -1
}

ElementClass.prototype.toggle = function(className) {
  var el = this.el
  if (!el) return
  if (this.has(className)) this.remove(className)
  else this.add(className)
}

},{}],2:[function(require,module,exports){
/*!
  Copyright (c) 2015 Jed Watson.
  Based on code that is Copyright 2013-2015, Facebook, Inc.
  All rights reserved.
*/

(function () {
	'use strict';

	var canUseDOM = !!(
		typeof window !== 'undefined' &&
		window.document &&
		window.document.createElement
	);

	var ExecutionEnvironment = {

		canUseDOM: canUseDOM,

		canUseWorkers: typeof Worker !== 'undefined',

		canUseEventListeners:
			canUseDOM && !!(window.addEventListener || window.attachEvent),

		canUseViewport: canUseDOM && !!window.screen

	};

	if (typeof define === 'function' && typeof define.amd === 'object' && define.amd) {
		define(function () {
			return ExecutionEnvironment;
		});
	} else if (typeof module !== 'undefined' && module.exports) {
		module.exports = ExecutionEnvironment;
	} else {
		window.ExecutionEnvironment = ExecutionEnvironment;
	}

}());

},{}],3:[function(require,module,exports){
/**
 * lodash 3.2.0 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var baseCopy = require('lodash._basecopy'),
    keys = require('lodash.keys');

/**
 * The base implementation of `_.assign` without support for argument juggling,
 * multiple sources, and `customizer` functions.
 *
 * @private
 * @param {Object} object The destination object.
 * @param {Object} source The source object.
 * @returns {Object} Returns `object`.
 */
function baseAssign(object, source) {
  return source == null
    ? object
    : baseCopy(source, keys(source), object);
}

module.exports = baseAssign;

},{"lodash._basecopy":4,"lodash.keys":12}],4:[function(require,module,exports){
/**
 * lodash 3.0.1 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/**
 * Copies properties of `source` to `object`.
 *
 * @private
 * @param {Object} source The object to copy properties from.
 * @param {Array} props The property names to copy.
 * @param {Object} [object={}] The object to copy properties to.
 * @returns {Object} Returns `object`.
 */
function baseCopy(source, props, object) {
  object || (object = {});

  var index = -1,
      length = props.length;

  while (++index < length) {
    var key = props[index];
    object[key] = source[key];
  }
  return object;
}

module.exports = baseCopy;

},{}],5:[function(require,module,exports){
/**
 * lodash 3.0.1 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/**
 * A specialized version of `baseCallback` which only supports `this` binding
 * and specifying the number of arguments to provide to `func`.
 *
 * @private
 * @param {Function} func The function to bind.
 * @param {*} thisArg The `this` binding of `func`.
 * @param {number} [argCount] The number of arguments to provide to `func`.
 * @returns {Function} Returns the callback.
 */
function bindCallback(func, thisArg, argCount) {
  if (typeof func != 'function') {
    return identity;
  }
  if (thisArg === undefined) {
    return func;
  }
  switch (argCount) {
    case 1: return function(value) {
      return func.call(thisArg, value);
    };
    case 3: return function(value, index, collection) {
      return func.call(thisArg, value, index, collection);
    };
    case 4: return function(accumulator, value, index, collection) {
      return func.call(thisArg, accumulator, value, index, collection);
    };
    case 5: return function(value, other, key, object, source) {
      return func.call(thisArg, value, other, key, object, source);
    };
  }
  return function() {
    return func.apply(thisArg, arguments);
  };
}

/**
 * This method returns the first argument provided to it.
 *
 * @static
 * @memberOf _
 * @category Utility
 * @param {*} value Any value.
 * @returns {*} Returns `value`.
 * @example
 *
 * var object = { 'user': 'fred' };
 *
 * _.identity(object) === object;
 * // => true
 */
function identity(value) {
  return value;
}

module.exports = bindCallback;

},{}],6:[function(require,module,exports){
/**
 * lodash 3.1.1 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var bindCallback = require('lodash._bindcallback'),
    isIterateeCall = require('lodash._isiterateecall'),
    restParam = require('lodash.restparam');

/**
 * Creates a function that assigns properties of source object(s) to a given
 * destination object.
 *
 * **Note:** This function is used to create `_.assign`, `_.defaults`, and `_.merge`.
 *
 * @private
 * @param {Function} assigner The function to assign values.
 * @returns {Function} Returns the new assigner function.
 */
function createAssigner(assigner) {
  return restParam(function(object, sources) {
    var index = -1,
        length = object == null ? 0 : sources.length,
        customizer = length > 2 ? sources[length - 2] : undefined,
        guard = length > 2 ? sources[2] : undefined,
        thisArg = length > 1 ? sources[length - 1] : undefined;

    if (typeof customizer == 'function') {
      customizer = bindCallback(customizer, thisArg, 5);
      length -= 2;
    } else {
      customizer = typeof thisArg == 'function' ? thisArg : undefined;
      length -= (customizer ? 1 : 0);
    }
    if (guard && isIterateeCall(sources[0], sources[1], guard)) {
      customizer = length < 3 ? undefined : customizer;
      length = 1;
    }
    while (++index < length) {
      var source = sources[index];
      if (source) {
        assigner(object, source, customizer);
      }
    }
    return object;
  });
}

module.exports = createAssigner;

},{"lodash._bindcallback":5,"lodash._isiterateecall":8,"lodash.restparam":13}],7:[function(require,module,exports){
/**
 * lodash 3.9.1 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/** `Object#toString` result references. */
var funcTag = '[object Function]';

/** Used to detect host constructors (Safari > 5). */
var reIsHostCtor = /^\[object .+?Constructor\]$/;

/**
 * Checks if `value` is object-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 */
function isObjectLike(value) {
  return !!value && typeof value == 'object';
}

/** Used for native method references. */
var objectProto = Object.prototype;

/** Used to resolve the decompiled source of functions. */
var fnToString = Function.prototype.toString;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Used to resolve the [`toStringTag`](http://ecma-international.org/ecma-262/6.0/#sec-object.prototype.tostring)
 * of values.
 */
var objToString = objectProto.toString;

/** Used to detect if a method is native. */
var reIsNative = RegExp('^' +
  fnToString.call(hasOwnProperty).replace(/[\\^$.*+?()[\]{}|]/g, '\\$&')
  .replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g, '$1.*?') + '$'
);

/**
 * Gets the native function at `key` of `object`.
 *
 * @private
 * @param {Object} object The object to query.
 * @param {string} key The key of the method to get.
 * @returns {*} Returns the function if it's native, else `undefined`.
 */
function getNative(object, key) {
  var value = object == null ? undefined : object[key];
  return isNative(value) ? value : undefined;
}

/**
 * Checks if `value` is classified as a `Function` object.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
 * @example
 *
 * _.isFunction(_);
 * // => true
 *
 * _.isFunction(/abc/);
 * // => false
 */
function isFunction(value) {
  // The use of `Object#toString` avoids issues with the `typeof` operator
  // in older versions of Chrome and Safari which return 'function' for regexes
  // and Safari 8 equivalents which return 'object' for typed array constructors.
  return isObject(value) && objToString.call(value) == funcTag;
}

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return !!value && (type == 'object' || type == 'function');
}

/**
 * Checks if `value` is a native function.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a native function, else `false`.
 * @example
 *
 * _.isNative(Array.prototype.push);
 * // => true
 *
 * _.isNative(_);
 * // => false
 */
function isNative(value) {
  if (value == null) {
    return false;
  }
  if (isFunction(value)) {
    return reIsNative.test(fnToString.call(value));
  }
  return isObjectLike(value) && reIsHostCtor.test(value);
}

module.exports = getNative;

},{}],8:[function(require,module,exports){
/**
 * lodash 3.0.9 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/** Used to detect unsigned integer values. */
var reIsUint = /^\d+$/;

/**
 * Used as the [maximum length](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-number.max_safe_integer)
 * of an array-like value.
 */
var MAX_SAFE_INTEGER = 9007199254740991;

/**
 * The base implementation of `_.property` without support for deep paths.
 *
 * @private
 * @param {string} key The key of the property to get.
 * @returns {Function} Returns the new function.
 */
function baseProperty(key) {
  return function(object) {
    return object == null ? undefined : object[key];
  };
}

/**
 * Gets the "length" property value of `object`.
 *
 * **Note:** This function is used to avoid a [JIT bug](https://bugs.webkit.org/show_bug.cgi?id=142792)
 * that affects Safari on at least iOS 8.1-8.3 ARM64.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {*} Returns the "length" value.
 */
var getLength = baseProperty('length');

/**
 * Checks if `value` is array-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is array-like, else `false`.
 */
function isArrayLike(value) {
  return value != null && isLength(getLength(value));
}

/**
 * Checks if `value` is a valid array-like index.
 *
 * @private
 * @param {*} value The value to check.
 * @param {number} [length=MAX_SAFE_INTEGER] The upper bounds of a valid index.
 * @returns {boolean} Returns `true` if `value` is a valid index, else `false`.
 */
function isIndex(value, length) {
  value = (typeof value == 'number' || reIsUint.test(value)) ? +value : -1;
  length = length == null ? MAX_SAFE_INTEGER : length;
  return value > -1 && value % 1 == 0 && value < length;
}

/**
 * Checks if the provided arguments are from an iteratee call.
 *
 * @private
 * @param {*} value The potential iteratee value argument.
 * @param {*} index The potential iteratee index or key argument.
 * @param {*} object The potential iteratee object argument.
 * @returns {boolean} Returns `true` if the arguments are from an iteratee call, else `false`.
 */
function isIterateeCall(value, index, object) {
  if (!isObject(object)) {
    return false;
  }
  var type = typeof index;
  if (type == 'number'
      ? (isArrayLike(object) && isIndex(index, object.length))
      : (type == 'string' && index in object)) {
    var other = object[index];
    return value === value ? (value === other) : (other !== other);
  }
  return false;
}

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This function is based on [`ToLength`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-tolength).
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 */
function isLength(value) {
  return typeof value == 'number' && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return !!value && (type == 'object' || type == 'function');
}

module.exports = isIterateeCall;

},{}],9:[function(require,module,exports){
/**
 * lodash 3.2.0 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var baseAssign = require('lodash._baseassign'),
    createAssigner = require('lodash._createassigner'),
    keys = require('lodash.keys');

/**
 * A specialized version of `_.assign` for customizing assigned values without
 * support for argument juggling, multiple sources, and `this` binding `customizer`
 * functions.
 *
 * @private
 * @param {Object} object The destination object.
 * @param {Object} source The source object.
 * @param {Function} customizer The function to customize assigned values.
 * @returns {Object} Returns `object`.
 */
function assignWith(object, source, customizer) {
  var index = -1,
      props = keys(source),
      length = props.length;

  while (++index < length) {
    var key = props[index],
        value = object[key],
        result = customizer(value, source[key], key, object, source);

    if ((result === result ? (result !== value) : (value === value)) ||
        (value === undefined && !(key in object))) {
      object[key] = result;
    }
  }
  return object;
}

/**
 * Assigns own enumerable properties of source object(s) to the destination
 * object. Subsequent sources overwrite property assignments of previous sources.
 * If `customizer` is provided it is invoked to produce the assigned values.
 * The `customizer` is bound to `thisArg` and invoked with five arguments:
 * (objectValue, sourceValue, key, object, source).
 *
 * **Note:** This method mutates `object` and is based on
 * [`Object.assign`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-object.assign).
 *
 * @static
 * @memberOf _
 * @alias extend
 * @category Object
 * @param {Object} object The destination object.
 * @param {...Object} [sources] The source objects.
 * @param {Function} [customizer] The function to customize assigned values.
 * @param {*} [thisArg] The `this` binding of `customizer`.
 * @returns {Object} Returns `object`.
 * @example
 *
 * _.assign({ 'user': 'barney' }, { 'age': 40 }, { 'user': 'fred' });
 * // => { 'user': 'fred', 'age': 40 }
 *
 * // using a customizer callback
 * var defaults = _.partialRight(_.assign, function(value, other) {
 *   return _.isUndefined(value) ? other : value;
 * });
 *
 * defaults({ 'user': 'barney' }, { 'age': 36 }, { 'user': 'fred' });
 * // => { 'user': 'barney', 'age': 36 }
 */
var assign = createAssigner(function(object, source, customizer) {
  return customizer
    ? assignWith(object, source, customizer)
    : baseAssign(object, source);
});

module.exports = assign;

},{"lodash._baseassign":3,"lodash._createassigner":6,"lodash.keys":12}],10:[function(require,module,exports){
/**
 * lodash 3.0.8 (Custom Build) <https://lodash.com/>
 * Build: `lodash modularize exports="npm" -o ./`
 * Copyright 2012-2016 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2016 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/** Used as references for various `Number` constants. */
var MAX_SAFE_INTEGER = 9007199254740991;

/** `Object#toString` result references. */
var argsTag = '[object Arguments]',
    funcTag = '[object Function]',
    genTag = '[object GeneratorFunction]';

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Used to resolve the [`toStringTag`](http://ecma-international.org/ecma-262/6.0/#sec-object.prototype.tostring)
 * of values.
 */
var objectToString = objectProto.toString;

/** Built-in value references. */
var propertyIsEnumerable = objectProto.propertyIsEnumerable;

/**
 * The base implementation of `_.property` without support for deep paths.
 *
 * @private
 * @param {string} key The key of the property to get.
 * @returns {Function} Returns the new function.
 */
function baseProperty(key) {
  return function(object) {
    return object == null ? undefined : object[key];
  };
}

/**
 * Gets the "length" property value of `object`.
 *
 * **Note:** This function is used to avoid a [JIT bug](https://bugs.webkit.org/show_bug.cgi?id=142792)
 * that affects Safari on at least iOS 8.1-8.3 ARM64.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {*} Returns the "length" value.
 */
var getLength = baseProperty('length');

/**
 * Checks if `value` is likely an `arguments` object.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
 * @example
 *
 * _.isArguments(function() { return arguments; }());
 * // => true
 *
 * _.isArguments([1, 2, 3]);
 * // => false
 */
function isArguments(value) {
  // Safari 8.1 incorrectly makes `arguments.callee` enumerable in strict mode.
  return isArrayLikeObject(value) && hasOwnProperty.call(value, 'callee') &&
    (!propertyIsEnumerable.call(value, 'callee') || objectToString.call(value) == argsTag);
}

/**
 * Checks if `value` is array-like. A value is considered array-like if it's
 * not a function and has a `value.length` that's an integer greater than or
 * equal to `0` and less than or equal to `Number.MAX_SAFE_INTEGER`.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is array-like, else `false`.
 * @example
 *
 * _.isArrayLike([1, 2, 3]);
 * // => true
 *
 * _.isArrayLike(document.body.children);
 * // => true
 *
 * _.isArrayLike('abc');
 * // => true
 *
 * _.isArrayLike(_.noop);
 * // => false
 */
function isArrayLike(value) {
  return value != null && isLength(getLength(value)) && !isFunction(value);
}

/**
 * This method is like `_.isArrayLike` except that it also checks if `value`
 * is an object.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an array-like object, else `false`.
 * @example
 *
 * _.isArrayLikeObject([1, 2, 3]);
 * // => true
 *
 * _.isArrayLikeObject(document.body.children);
 * // => true
 *
 * _.isArrayLikeObject('abc');
 * // => false
 *
 * _.isArrayLikeObject(_.noop);
 * // => false
 */
function isArrayLikeObject(value) {
  return isObjectLike(value) && isArrayLike(value);
}

/**
 * Checks if `value` is classified as a `Function` object.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
 * @example
 *
 * _.isFunction(_);
 * // => true
 *
 * _.isFunction(/abc/);
 * // => false
 */
function isFunction(value) {
  // The use of `Object#toString` avoids issues with the `typeof` operator
  // in Safari 8 which returns 'object' for typed array and weak map constructors,
  // and PhantomJS 1.9 which returns 'function' for `NodeList` instances.
  var tag = isObject(value) ? objectToString.call(value) : '';
  return tag == funcTag || tag == genTag;
}

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This function is loosely based on [`ToLength`](http://ecma-international.org/ecma-262/6.0/#sec-tolength).
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 * @example
 *
 * _.isLength(3);
 * // => true
 *
 * _.isLength(Number.MIN_VALUE);
 * // => false
 *
 * _.isLength(Infinity);
 * // => false
 *
 * _.isLength('3');
 * // => false
 */
function isLength(value) {
  return typeof value == 'number' &&
    value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(_.noop);
 * // => true
 *
 * _.isObject(null);
 * // => false
 */
function isObject(value) {
  var type = typeof value;
  return !!value && (type == 'object' || type == 'function');
}

/**
 * Checks if `value` is object-like. A value is object-like if it's not `null`
 * and has a `typeof` result of "object".
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 * @example
 *
 * _.isObjectLike({});
 * // => true
 *
 * _.isObjectLike([1, 2, 3]);
 * // => true
 *
 * _.isObjectLike(_.noop);
 * // => false
 *
 * _.isObjectLike(null);
 * // => false
 */
function isObjectLike(value) {
  return !!value && typeof value == 'object';
}

module.exports = isArguments;

},{}],11:[function(require,module,exports){
/**
 * lodash 3.0.4 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/** `Object#toString` result references. */
var arrayTag = '[object Array]',
    funcTag = '[object Function]';

/** Used to detect host constructors (Safari > 5). */
var reIsHostCtor = /^\[object .+?Constructor\]$/;

/**
 * Checks if `value` is object-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 */
function isObjectLike(value) {
  return !!value && typeof value == 'object';
}

/** Used for native method references. */
var objectProto = Object.prototype;

/** Used to resolve the decompiled source of functions. */
var fnToString = Function.prototype.toString;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Used to resolve the [`toStringTag`](http://ecma-international.org/ecma-262/6.0/#sec-object.prototype.tostring)
 * of values.
 */
var objToString = objectProto.toString;

/** Used to detect if a method is native. */
var reIsNative = RegExp('^' +
  fnToString.call(hasOwnProperty).replace(/[\\^$.*+?()[\]{}|]/g, '\\$&')
  .replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g, '$1.*?') + '$'
);

/* Native method references for those with the same name as other `lodash` methods. */
var nativeIsArray = getNative(Array, 'isArray');

/**
 * Used as the [maximum length](http://ecma-international.org/ecma-262/6.0/#sec-number.max_safe_integer)
 * of an array-like value.
 */
var MAX_SAFE_INTEGER = 9007199254740991;

/**
 * Gets the native function at `key` of `object`.
 *
 * @private
 * @param {Object} object The object to query.
 * @param {string} key The key of the method to get.
 * @returns {*} Returns the function if it's native, else `undefined`.
 */
function getNative(object, key) {
  var value = object == null ? undefined : object[key];
  return isNative(value) ? value : undefined;
}

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This function is based on [`ToLength`](http://ecma-international.org/ecma-262/6.0/#sec-tolength).
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 */
function isLength(value) {
  return typeof value == 'number' && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

/**
 * Checks if `value` is classified as an `Array` object.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
 * @example
 *
 * _.isArray([1, 2, 3]);
 * // => true
 *
 * _.isArray(function() { return arguments; }());
 * // => false
 */
var isArray = nativeIsArray || function(value) {
  return isObjectLike(value) && isLength(value.length) && objToString.call(value) == arrayTag;
};

/**
 * Checks if `value` is classified as a `Function` object.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
 * @example
 *
 * _.isFunction(_);
 * // => true
 *
 * _.isFunction(/abc/);
 * // => false
 */
function isFunction(value) {
  // The use of `Object#toString` avoids issues with the `typeof` operator
  // in older versions of Chrome and Safari which return 'function' for regexes
  // and Safari 8 equivalents which return 'object' for typed array constructors.
  return isObject(value) && objToString.call(value) == funcTag;
}

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return !!value && (type == 'object' || type == 'function');
}

/**
 * Checks if `value` is a native function.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a native function, else `false`.
 * @example
 *
 * _.isNative(Array.prototype.push);
 * // => true
 *
 * _.isNative(_);
 * // => false
 */
function isNative(value) {
  if (value == null) {
    return false;
  }
  if (isFunction(value)) {
    return reIsNative.test(fnToString.call(value));
  }
  return isObjectLike(value) && reIsHostCtor.test(value);
}

module.exports = isArray;

},{}],12:[function(require,module,exports){
/**
 * lodash 3.1.2 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var getNative = require('lodash._getnative'),
    isArguments = require('lodash.isarguments'),
    isArray = require('lodash.isarray');

/** Used to detect unsigned integer values. */
var reIsUint = /^\d+$/;

/** Used for native method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/* Native method references for those with the same name as other `lodash` methods. */
var nativeKeys = getNative(Object, 'keys');

/**
 * Used as the [maximum length](http://ecma-international.org/ecma-262/6.0/#sec-number.max_safe_integer)
 * of an array-like value.
 */
var MAX_SAFE_INTEGER = 9007199254740991;

/**
 * The base implementation of `_.property` without support for deep paths.
 *
 * @private
 * @param {string} key The key of the property to get.
 * @returns {Function} Returns the new function.
 */
function baseProperty(key) {
  return function(object) {
    return object == null ? undefined : object[key];
  };
}

/**
 * Gets the "length" property value of `object`.
 *
 * **Note:** This function is used to avoid a [JIT bug](https://bugs.webkit.org/show_bug.cgi?id=142792)
 * that affects Safari on at least iOS 8.1-8.3 ARM64.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {*} Returns the "length" value.
 */
var getLength = baseProperty('length');

/**
 * Checks if `value` is array-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is array-like, else `false`.
 */
function isArrayLike(value) {
  return value != null && isLength(getLength(value));
}

/**
 * Checks if `value` is a valid array-like index.
 *
 * @private
 * @param {*} value The value to check.
 * @param {number} [length=MAX_SAFE_INTEGER] The upper bounds of a valid index.
 * @returns {boolean} Returns `true` if `value` is a valid index, else `false`.
 */
function isIndex(value, length) {
  value = (typeof value == 'number' || reIsUint.test(value)) ? +value : -1;
  length = length == null ? MAX_SAFE_INTEGER : length;
  return value > -1 && value % 1 == 0 && value < length;
}

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This function is based on [`ToLength`](http://ecma-international.org/ecma-262/6.0/#sec-tolength).
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 */
function isLength(value) {
  return typeof value == 'number' && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

/**
 * A fallback implementation of `Object.keys` which creates an array of the
 * own enumerable property names of `object`.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names.
 */
function shimKeys(object) {
  var props = keysIn(object),
      propsLength = props.length,
      length = propsLength && object.length;

  var allowIndexes = !!length && isLength(length) &&
    (isArray(object) || isArguments(object));

  var index = -1,
      result = [];

  while (++index < propsLength) {
    var key = props[index];
    if ((allowIndexes && isIndex(key, length)) || hasOwnProperty.call(object, key)) {
      result.push(key);
    }
  }
  return result;
}

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return !!value && (type == 'object' || type == 'function');
}

/**
 * Creates an array of the own enumerable property names of `object`.
 *
 * **Note:** Non-object values are coerced to objects. See the
 * [ES spec](http://ecma-international.org/ecma-262/6.0/#sec-object.keys)
 * for more details.
 *
 * @static
 * @memberOf _
 * @category Object
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names.
 * @example
 *
 * function Foo() {
 *   this.a = 1;
 *   this.b = 2;
 * }
 *
 * Foo.prototype.c = 3;
 *
 * _.keys(new Foo);
 * // => ['a', 'b'] (iteration order is not guaranteed)
 *
 * _.keys('hi');
 * // => ['0', '1']
 */
var keys = !nativeKeys ? shimKeys : function(object) {
  var Ctor = object == null ? undefined : object.constructor;
  if ((typeof Ctor == 'function' && Ctor.prototype === object) ||
      (typeof object != 'function' && isArrayLike(object))) {
    return shimKeys(object);
  }
  return isObject(object) ? nativeKeys(object) : [];
};

/**
 * Creates an array of the own and inherited enumerable property names of `object`.
 *
 * **Note:** Non-object values are coerced to objects.
 *
 * @static
 * @memberOf _
 * @category Object
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names.
 * @example
 *
 * function Foo() {
 *   this.a = 1;
 *   this.b = 2;
 * }
 *
 * Foo.prototype.c = 3;
 *
 * _.keysIn(new Foo);
 * // => ['a', 'b', 'c'] (iteration order is not guaranteed)
 */
function keysIn(object) {
  if (object == null) {
    return [];
  }
  if (!isObject(object)) {
    object = Object(object);
  }
  var length = object.length;
  length = (length && isLength(length) &&
    (isArray(object) || isArguments(object)) && length) || 0;

  var Ctor = object.constructor,
      index = -1,
      isProto = typeof Ctor == 'function' && Ctor.prototype === object,
      result = Array(length),
      skipIndexes = length > 0;

  while (++index < length) {
    result[index] = (index + '');
  }
  for (var key in object) {
    if (!(skipIndexes && isIndex(key, length)) &&
        !(key == 'constructor' && (isProto || !hasOwnProperty.call(object, key)))) {
      result.push(key);
    }
  }
  return result;
}

module.exports = keys;

},{"lodash._getnative":7,"lodash.isarguments":10,"lodash.isarray":11}],13:[function(require,module,exports){
/**
 * lodash 3.6.1 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/** Used as the `TypeError` message for "Functions" methods. */
var FUNC_ERROR_TEXT = 'Expected a function';

/* Native method references for those with the same name as other `lodash` methods. */
var nativeMax = Math.max;

/**
 * Creates a function that invokes `func` with the `this` binding of the
 * created function and arguments from `start` and beyond provided as an array.
 *
 * **Note:** This method is based on the [rest parameter](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Functions/rest_parameters).
 *
 * @static
 * @memberOf _
 * @category Function
 * @param {Function} func The function to apply a rest parameter to.
 * @param {number} [start=func.length-1] The start position of the rest parameter.
 * @returns {Function} Returns the new function.
 * @example
 *
 * var say = _.restParam(function(what, names) {
 *   return what + ' ' + _.initial(names).join(', ') +
 *     (_.size(names) > 1 ? ', & ' : '') + _.last(names);
 * });
 *
 * say('hello', 'fred', 'barney', 'pebbles');
 * // => 'hello fred, barney, & pebbles'
 */
function restParam(func, start) {
  if (typeof func != 'function') {
    throw new TypeError(FUNC_ERROR_TEXT);
  }
  start = nativeMax(start === undefined ? (func.length - 1) : (+start || 0), 0);
  return function() {
    var args = arguments,
        index = -1,
        length = nativeMax(args.length - start, 0),
        rest = Array(length);

    while (++index < length) {
      rest[index] = args[start + index];
    }
    switch (start) {
      case 0: return func.call(this, rest);
      case 1: return func.call(this, args[0], rest);
      case 2: return func.call(this, args[0], args[1], rest);
    }
    var otherArgs = Array(start + 1);
    index = -1;
    while (++index < start) {
      otherArgs[index] = args[index];
    }
    otherArgs[start] = rest;
    return func.apply(this, otherArgs);
  };
}

module.exports = restParam;

},{}],14:[function(require,module,exports){
// shim for using process in browser

var process = module.exports = {};
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = setTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    clearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        setTimeout(drainQueue, 0);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };

},{}],15:[function(require,module,exports){
(function (process){
var React = require('react');
var ReactDOM = require('react-dom');
var ExecutionEnvironment = require('exenv');
var ModalPortal = React.createFactory(require('./ModalPortal'));
var ariaAppHider = require('../helpers/ariaAppHider');
var elementClass = require('element-class');
var renderSubtreeIntoContainer = require("react-dom").unstable_renderSubtreeIntoContainer;
var Assign = require('lodash.assign');

var SafeHTMLElement = ExecutionEnvironment.canUseDOM ? window.HTMLElement : {};
var AppElement = ExecutionEnvironment.canUseDOM ? document.body : {appendChild: function() {}};

var Modal = React.createClass({

  displayName: 'Modal',
  statics: {
    setAppElement: function(element) {
        AppElement = ariaAppHider.setElement(element);
    },
    injectCSS: function() {
      "production" !== process.env.NODE_ENV
        && console.warn('React-Modal: injectCSS has been deprecated ' +
                        'and no longer has any effect. It will be removed in a later version');
    }
  },

  propTypes: {
    isOpen: React.PropTypes.bool.isRequired,
    style: React.PropTypes.shape({
      content: React.PropTypes.object,
      overlay: React.PropTypes.object
    }),
    appElement: React.PropTypes.instanceOf(SafeHTMLElement),
    onAfterOpen: React.PropTypes.func,
    onRequestClose: React.PropTypes.func,
    closeTimeoutMS: React.PropTypes.number,
    ariaHideApp: React.PropTypes.bool,
    shouldCloseOnOverlayClick: React.PropTypes.bool
  },

  getDefaultProps: function () {
    return {
      isOpen: false,
      ariaHideApp: true,
      closeTimeoutMS: 0,
      shouldCloseOnOverlayClick: true
    };
  },

  componentDidMount: function() {
    this.node = document.createElement('div');
    this.node.className = 'ReactModalPortal';
    document.body.appendChild(this.node);
    this.renderPortal(this.props);
  },

  componentWillReceiveProps: function(newProps) {
    this.renderPortal(newProps);
  },

  componentWillUnmount: function() {
    ReactDOM.unmountComponentAtNode(this.node);
    document.body.removeChild(this.node);
    elementClass(document.body).remove('ReactModal__Body--open');
  },

  renderPortal: function(props) {
    if (props.isOpen) {
      elementClass(document.body).add('ReactModal__Body--open');
    } else {
      elementClass(document.body).remove('ReactModal__Body--open');
    }

    if (props.ariaHideApp) {
      ariaAppHider.toggle(props.isOpen, props.appElement);
    }

    this.portal = renderSubtreeIntoContainer(this, ModalPortal(Assign({}, props, {defaultStyles: Modal.defaultStyles})), this.node);
  },

  render: function () {
    return React.DOM.noscript();
  }
});

Modal.defaultStyles = {
  overlay: {
    position        : 'fixed',
    top             : 0,
    left            : 0,
    right           : 0,
    bottom          : 0,
    backgroundColor : 'rgba(255, 255, 255, 0.75)'
  },
  content: {
    position                : 'absolute',
    top                     : '40px',
    left                    : '40px',
    right                   : '40px',
    bottom                  : '40px',
    border                  : '1px solid #ccc',
    background              : '#fff',
    overflow                : 'auto',
    WebkitOverflowScrolling : 'touch',
    borderRadius            : '4px',
    outline                 : 'none',
    padding                 : '20px'
  }
}

module.exports = Modal

}).call(this,require('_process'))
},{"../helpers/ariaAppHider":17,"./ModalPortal":16,"_process":14,"element-class":1,"exenv":2,"lodash.assign":9,"react":"react","react-dom":"react-dom"}],16:[function(require,module,exports){
var React = require('react');
var div = React.DOM.div;
var focusManager = require('../helpers/focusManager');
var scopeTab = require('../helpers/scopeTab');
var Assign = require('lodash.assign');

// so that our CSS is statically analyzable
var CLASS_NAMES = {
  overlay: {
    base: 'ReactModal__Overlay',
    afterOpen: 'ReactModal__Overlay--after-open',
    beforeClose: 'ReactModal__Overlay--before-close'
  },
  content: {
    base: 'ReactModal__Content',
    afterOpen: 'ReactModal__Content--after-open',
    beforeClose: 'ReactModal__Content--before-close'
  }
};

var ModalPortal = module.exports = React.createClass({

  displayName: 'ModalPortal',

  getDefaultProps: function() {
    return {
      style: {
        overlay: {},
        content: {}
      }
    };
  },

  getInitialState: function() {
    return {
      afterOpen: false,
      beforeClose: false
    };
  },

  componentDidMount: function() {
    // Focus needs to be set when mounting and already open
    if (this.props.isOpen) {
      this.setFocusAfterRender(true);
      this.open();
    }
  },

  componentWillUnmount: function() {
    clearTimeout(this.closeTimer);
  },

  componentWillReceiveProps: function(newProps) {
    // Focus only needs to be set once when the modal is being opened
    if (!this.props.isOpen && newProps.isOpen) {
      this.setFocusAfterRender(true);
      this.open();
    } else if (this.props.isOpen && !newProps.isOpen) {
      this.close();
    }
  },

  componentDidUpdate: function () {
    if (this.focusAfterRender) {
      this.focusContent();
      this.setFocusAfterRender(false);
    }
  },

  setFocusAfterRender: function (focus) {
    this.focusAfterRender = focus;
  },

  open: function() {
    focusManager.setupScopedFocus(this.node);
    focusManager.markForFocusLater();
    this.setState({isOpen: true}, function() {
      this.setState({afterOpen: true});

      if (this.props.isOpen && this.props.onAfterOpen) {
        this.props.onAfterOpen();
      }
    }.bind(this));
  },

  close: function() {
    if (!this.ownerHandlesClose())
      return;
    if (this.props.closeTimeoutMS > 0)
      this.closeWithTimeout();
    else
      this.closeWithoutTimeout();
  },

  focusContent: function() {
    this.refs.content.focus();
  },

  closeWithTimeout: function() {
    this.setState({beforeClose: true}, function() {
      this.closeTimer = setTimeout(this.closeWithoutTimeout, this.props.closeTimeoutMS);
    }.bind(this));
  },

  closeWithoutTimeout: function() {
    this.setState({
      afterOpen: false,
      beforeClose: false
    }, this.afterClose);
  },

  afterClose: function() {
    focusManager.returnFocus();
    focusManager.teardownScopedFocus();
  },

  handleKeyDown: function(event) {
    if (event.keyCode == 9 /*tab*/) scopeTab(this.refs.content, event);
    if (event.keyCode == 27 /*esc*/) {
      event.preventDefault();
      this.requestClose(event);
    }
  },

  handleOverlayClick: function(event) {
    var node = event.target

    while (node) {
      if (node === this.refs.content) return
      node = node.parentNode
    }

    if (this.props.shouldCloseOnOverlayClick) {
      if (this.ownerHandlesClose())
        this.requestClose(event);
      else
        this.focusContent();
    }
  },

  requestClose: function(event) {
    if (this.ownerHandlesClose())
      this.props.onRequestClose(event);
  },

  ownerHandlesClose: function() {
    return this.props.onRequestClose;
  },

  shouldBeClosed: function() {
    return !this.props.isOpen && !this.state.beforeClose;
  },

  buildClassName: function(which, additional) {
    var className = CLASS_NAMES[which].base;
    if (this.state.afterOpen)
      className += ' '+CLASS_NAMES[which].afterOpen;
    if (this.state.beforeClose)
      className += ' '+CLASS_NAMES[which].beforeClose;
    return additional ? className + ' ' + additional : className;
  },

  render: function() {
    var contentStyles = (this.props.className) ? {} : this.props.defaultStyles.content;
    var overlayStyles = (this.props.overlayClassName) ? {} : this.props.defaultStyles.overlay;

    return this.shouldBeClosed() ? div() : (
      div({
        ref: "overlay",
        className: this.buildClassName('overlay', this.props.overlayClassName),
        style: Assign({}, overlayStyles, this.props.style.overlay || {}),
        onClick: this.handleOverlayClick
      },
        div({
          ref: "content",
          style: Assign({}, contentStyles, this.props.style.content || {}),
          className: this.buildClassName('content', this.props.className),
          tabIndex: "-1",
          onKeyDown: this.handleKeyDown
        },
          this.props.children
        )
      )
    );
  }
});

},{"../helpers/focusManager":18,"../helpers/scopeTab":19,"lodash.assign":9,"react":"react"}],17:[function(require,module,exports){
var _element = typeof document !== 'undefined' ? document.body : null;

function setElement(element) {
  if (typeof element === 'string') {
    var el = document.querySelectorAll(element);
    element = 'length' in el ? el[0] : el;
  }
  _element = element || _element;
  return _element;
}

function hide(appElement) {
  validateElement(appElement);
  (appElement || _element).setAttribute('aria-hidden', 'true');
}

function show(appElement) {
  validateElement(appElement);
  (appElement || _element).removeAttribute('aria-hidden');
}

function toggle(shouldHide, appElement) {
  if (shouldHide)
    hide(appElement);
  else
    show(appElement);
}

function validateElement(appElement) {
  if (!appElement && !_element)
    throw new Error('react-modal: You must set an element with `Modal.setAppElement(el)` to make this accessible');
}

function resetForTesting() {
  _element = document.body;
}

exports.toggle = toggle;
exports.setElement = setElement;
exports.show = show;
exports.hide = hide;
exports.resetForTesting = resetForTesting;

},{}],18:[function(require,module,exports){
var findTabbable = require('../helpers/tabbable');
var modalElement = null;
var focusLaterElement = null;
var needToFocus = false;

function handleBlur(event) {
  needToFocus = true;
}

function handleFocus(event) {
  if (needToFocus) {
    needToFocus = false;
    if (!modalElement) {
      return;
    }
    // need to see how jQuery shims document.on('focusin') so we don't need the
    // setTimeout, firefox doesn't support focusin, if it did, we could focus
    // the element outside of a setTimeout. Side-effect of this implementation 
    // is that the document.body gets focus, and then we focus our element right 
    // after, seems fine.
    setTimeout(function() {
      if (modalElement.contains(document.activeElement))
        return;
      var el = (findTabbable(modalElement)[0] || modalElement);
      el.focus();
    }, 0);
  }
}

exports.markForFocusLater = function() {
  focusLaterElement = document.activeElement;
};

exports.returnFocus = function() {
  try {
    focusLaterElement.focus();
  }
  catch (e) {
    console.warn('You tried to return focus to '+focusLaterElement+' but it is not in the DOM anymore');
  }
  focusLaterElement = null;
};

exports.setupScopedFocus = function(element) {
  modalElement = element;

  if (window.addEventListener) {
    window.addEventListener('blur', handleBlur, false);
    document.addEventListener('focus', handleFocus, true);
  } else {
    window.attachEvent('onBlur', handleBlur);
    document.attachEvent('onFocus', handleFocus);
  }
};

exports.teardownScopedFocus = function() {
  modalElement = null;

  if (window.addEventListener) {
    window.removeEventListener('blur', handleBlur);
    document.removeEventListener('focus', handleFocus);
  } else {
    window.detachEvent('onBlur', handleBlur);
    document.detachEvent('onFocus', handleFocus);
  }
};



},{"../helpers/tabbable":20}],19:[function(require,module,exports){
var findTabbable = require('../helpers/tabbable');

module.exports = function(node, event) {
  var tabbable = findTabbable(node);
  if (!tabbable.length) {
      event.preventDefault();
      return;
  }
  var finalTabbable = tabbable[event.shiftKey ? 0 : tabbable.length - 1];
  var leavingFinalTabbable = (
    finalTabbable === document.activeElement ||
    // handle immediate shift+tab after opening with mouse
    node === document.activeElement
  );
  if (!leavingFinalTabbable) return;
  event.preventDefault();
  var target = tabbable[event.shiftKey ? tabbable.length - 1 : 0];
  target.focus();
};

},{"../helpers/tabbable":20}],20:[function(require,module,exports){
/*!
 * Adapted from jQuery UI core
 *
 * http://jqueryui.com
 *
 * Copyright 2014 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/category/ui-core/
 */

function focusable(element, isTabIndexNotNaN) {
  var nodeName = element.nodeName.toLowerCase();
  return (/input|select|textarea|button|object/.test(nodeName) ?
    !element.disabled :
    "a" === nodeName ?
      element.href || isTabIndexNotNaN :
      isTabIndexNotNaN) && visible(element);
}

function hidden(el) {
  return (el.offsetWidth <= 0 && el.offsetHeight <= 0) ||
    el.style.display === 'none';
}

function visible(element) {
  while (element) {
    if (element === document.body) break;
    if (hidden(element)) return false;
    element = element.parentNode;
  }
  return true;
}

function tabbable(element) {
  var tabIndex = element.getAttribute('tabindex');
  if (tabIndex === null) tabIndex = undefined;
  var isTabIndexNaN = isNaN(tabIndex);
  return (isTabIndexNaN || tabIndex >= 0) && focusable(element, !isTabIndexNaN);
}

function findTabbableDescendants(element) {
  return [].slice.call(element.querySelectorAll('*'), 0).filter(function(el) {
    return tabbable(el);
  });
}

module.exports = findTabbableDescendants;


},{}],21:[function(require,module,exports){
module.exports = require('./components/Modal');


},{"./components/Modal":15}],22:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var Compare = function (_React$Component) {
    _inherits(Compare, _React$Component);

    function Compare() {
        _classCallCheck(this, Compare);

        var _this = _possibleConstructorReturn(this, (Compare.__proto__ || Object.getPrototypeOf(Compare)).call(this));

        _this.state = {
            sourceData: window.sourceIsins,
            inputField: '',
            inputData: '',
            calculating: false
        };
        return _this;
    }

    _createClass(Compare, [{
        key: 'handleInputChange',
        value: function handleInputChange(event) {
            var val = event.target.value;
            this.setState({
                inputField: val,
                calculating: true
            });
            setTimeout(function () {
                this.setState({
                    inputData: val,
                    calculating: false
                });
            }.bind(this), 100);
        }
    }, {
        key: 'getInput',
        value: function getInput() {
            var text = this.state.inputData;
            return text.split(/\n/);
        }
    }, {
        key: 'compareItems',
        value: function compareItems() {
            var input = this.getInput(),
                notInSource = this.diff(input, this.state.sourceData),
                notInInput = this.diff(this.state.sourceData, input),
                items = [];

            notInSource.forEach(function (item) {
                if (item.length == 0) {
                    return;
                }
                items.push(_react2.default.createElement(
                    'li',
                    null,
                    item,
                    ' (not in database)'
                ));
            });

            notInInput.forEach(function (item) {
                if (item.length == 0) {
                    return;
                }
                items.push(_react2.default.createElement(
                    'li',
                    null,
                    item,
                    ' (not in input)'
                ));
            });

            return items;
        }
    }, {
        key: 'diff',
        value: function diff(a, b) {
            return a.filter(function (i) {
                return b.indexOf(i) < 0;
            });
        }
    }, {
        key: 'render',
        value: function render() {
            var styles = { height: '400px', overflow: 'auto' },
                sourceItems = [],
                comparedItems = this.state.calculating ? [] : this.compareItems();

            this.state.sourceData.forEach(function (item, i) {
                sourceItems.push(_react2.default.createElement(
                    'li',
                    null,
                    item
                ));
            });

            var result = _react2.default.createElement(
                'p',
                null,
                'Enter data to see result'
            );

            if (this.state.calculating) {
                result = _react2.default.createElement(
                    'p',
                    null,
                    'Calculating...'
                );
            } else {
                if (this.state.inputData.length > 0) {
                    if (comparedItems.length == 0) {
                        result = _react2.default.createElement(
                            'p',
                            null,
                            'Perfect match'
                        );
                    } else {
                        result = _react2.default.createElement(
                            'ol',
                            null,
                            comparedItems
                        );
                    }
                }
            }

            return _react2.default.createElement(
                'div',
                { className: 'grid' },
                _react2.default.createElement(
                    'div',
                    { className: 'g 1/3' },
                    _react2.default.createElement(
                        'h2',
                        { className: 'g-unit' },
                        'ISINs in database'
                    ),
                    _react2.default.createElement(
                        'div',
                        { style: styles },
                        _react2.default.createElement(
                            'ol',
                            null,
                            sourceItems
                        )
                    )
                ),
                _react2.default.createElement(
                    'div',
                    { className: 'g 1/3' },
                    _react2.default.createElement(
                        'h2',
                        { className: 'g-unit' },
                        'Enter list to compare'
                    ),
                    _react2.default.createElement('textarea', {
                        onChange: this.handleInputChange.bind(this),
                        value: this.state.inputField,
                        style: styles })
                ),
                _react2.default.createElement(
                    'div',
                    { className: 'g 1/3' },
                    _react2.default.createElement(
                        'h2',
                        { className: 'g-unit' },
                        'Results'
                    ),
                    _react2.default.createElement(
                        'div',
                        { style: styles },
                        result
                    )
                )
            );
        }
    }]);

    return Compare;
}(_react2.default.Component);

exports.default = Compare;

},{"react":"react"}],23:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _FileDrop = require('../../Utils/FileDrop');

var _FileDrop2 = _interopRequireDefault(_FileDrop);

var _Loading = require('../../Utils/Loading');

var _Loading2 = _interopRequireDefault(_Loading);

var _Message = require('../../Utils/Message');

var _Message2 = _interopRequireDefault(_Message);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var BulkUpload = function (_React$Component) {
    _inherits(BulkUpload, _React$Component);

    _createClass(BulkUpload, null, [{
        key: 'STATUS_UPLOADING',
        get: function get() {
            return 'uploading';
        }
    }, {
        key: 'STATUS_PROCESSING',
        get: function get() {
            return 'processing';
        }
    }, {
        key: 'STATUS_ERROR',
        get: function get() {
            return 'error';
        }
    }]);

    function BulkUpload() {
        _classCallCheck(this, BulkUpload);

        var _this = _possibleConstructorReturn(this, (BulkUpload.__proto__ || Object.getPrototypeOf(BulkUpload)).call(this));

        _this.state = {
            status: null,
            results: []
        };
        _this.loop = false;
        return _this;
    }

    _createClass(BulkUpload, [{
        key: 'componentDidMount',
        value: function componentDidMount() {
            this.setState({
                stats: this.props.bulkStats || null
            });
        }
    }, {
        key: 'handleReceievedFile',
        value: function handleReceievedFile(file) {
            this.setState({
                status: BulkUpload.STATUS_UPLOADING
            });

            var reader = new FileReader(),
                url = '/admin/data/bulk-upload.json';

            reader.onload = function (e) {

                fetch(url, {
                    method: 'post',
                    body: e.currentTarget.result,
                    credentials: 'same-origin'
                }).then(function (response) {
                    return response.json();
                }.bind(this)).then(function (data) {
                    this.setState({
                        stats: data.stats,
                        status: null
                    });
                }.bind(this)).catch(function (error) {
                    this.setState({
                        status: BulkUpload.STATUS_ERROR
                    });
                }.bind(this));
            }.bind(this);

            reader.readAsText(file);
        }
    }, {
        key: 'handleClickBatch',
        value: function handleClickBatch() {
            this.setState({
                status: BulkUpload.STATUS_PROCESSING
            });

            var url = '/admin/data/bulk-process.json';
            fetch(url, {
                method: 'post',
                credentials: 'same-origin'
            }).then(function (response) {
                return response.json();
            }.bind(this)).then(function (data) {
                var results = data.securities.reverse();
                results = results.concat(this.state.results);

                this.setState({
                    stats: data.stats,
                    results: results.slice(0, 500)
                });

                if (this.loop) {
                    return this.handleClickBatch();
                }

                this.setState({
                    status: null
                });
            }.bind(this)).catch(function (e) {
                this.setState({
                    status: BulkUpload.STATUS_ERROR
                });
            }.bind(this));
        }
    }, {
        key: 'handleClickAll',
        value: function handleClickAll() {
            this.loop = true;
            this.handleClickBatch();
        }
    }, {
        key: 'handleStop',
        value: function handleStop() {
            this.loop = false;
        }
    }, {
        key: 'render',
        value: function render() {
            var filepanel = _react2.default.createElement(_FileDrop2.default, { onFileRecieved: this.handleReceievedFile.bind(this) }),
                panel = null;

            if (this.state.status == BulkUpload.STATUS_UPLOADING) {
                panel = _react2.default.createElement(_Loading2.default, null);
                filepanel = null;
            }

            if (this.state.stats) {
                var processButtons = _react2.default.createElement(
                    'span',
                    null,
                    _react2.default.createElement(
                        'button',
                        { className: 'button button--fat',
                            onClick: this.handleClickBatch.bind(this)
                        },
                        'Process Batch'
                    ),
                    ' ',
                    _react2.default.createElement(
                        'button',
                        { className: 'button button--fat',
                            onClick: this.handleClickAll.bind(this)
                        },
                        'Process All'
                    )
                ),
                    complete = this.state.stats.totalProcessed == this.state.totalToProcess;

                if (complete) {
                    processButtons = null;
                } else {
                    filepanel = null;
                }

                if (this.state.status == BulkUpload.STATUS_PROCESSING) {
                    processButtons = [_react2.default.createElement(_Loading2.default, { key: 'processloading', cssClasses: 'loading--sibling' })];
                    if (this.loop) {
                        processButtons.push(_react2.default.createElement(
                            'button',
                            { key: 'stopbutton', className: 'button button--fat',
                                onClick: this.handleStop.bind(this)
                            },
                            'Stop'
                        ));
                    }
                }

                panel = _react2.default.createElement(
                    'div',
                    { className: 'panel g-unit' },
                    _react2.default.createElement(
                        'div',
                        { className: 'grid grid--flat' },
                        _react2.default.createElement(
                            'div',
                            { className: 'g 2/3 g--align-center' },
                            _react2.default.createElement(
                                'p',
                                { className: 'a' },
                                this.state.stats.totalProcessedFormatted,
                                '/',
                                this.state.stats.totalToProcessFormatted,
                                ' processed'
                            )
                        ),
                        _react2.default.createElement(
                            'div',
                            { className: 'g 1/3 g--align-center' },
                            _react2.default.createElement(
                                'div',
                                { className: 'text--right' },
                                processButtons
                            )
                        )
                    )
                );
            }

            var message = null;
            if (this.state.status == BulkUpload.STATUS_ERROR) {
                message = _react2.default.createElement(_Message2.default, { type: _Message2.default.TYPE_ERROR, message: 'An error occurred' });
            }

            var results = null,
                items = [];
            if (this.state.results.length > 0) {
                this.state.results.forEach(function (item) {
                    items.push(_react2.default.createElement(BulkUploadSecurity, { key: item.isin, data: item }));
                });
                results = _react2.default.createElement(
                    'table',
                    { className: 'table table--striped' },
                    _react2.default.createElement(
                        'thead',
                        null,
                        _react2.default.createElement(
                            'tr',
                            null,
                            _react2.default.createElement(
                                'th',
                                null,
                                'ISIN'
                            ),
                            _react2.default.createElement(
                                'th',
                                null,
                                'Name'
                            ),
                            _react2.default.createElement(
                                'th',
                                null,
                                'Start Date'
                            )
                        )
                    ),
                    _react2.default.createElement(
                        'tbody',
                        null,
                        items
                    )
                );
            }

            return _react2.default.createElement(
                'div',
                null,
                _react2.default.createElement(
                    'h1',
                    { className: 'b g-unit' },
                    'Bulk upload ISINs'
                ),
                filepanel,
                panel,
                message,
                results
            );
        }
    }]);

    return BulkUpload;
}(_react2.default.Component);

exports.default = BulkUpload;

var BulkUploadSecurity = function (_React$Component2) {
    _inherits(BulkUploadSecurity, _React$Component2);

    function BulkUploadSecurity() {
        _classCallCheck(this, BulkUploadSecurity);

        return _possibleConstructorReturn(this, (BulkUploadSecurity.__proto__ || Object.getPrototypeOf(BulkUploadSecurity)).apply(this, arguments));
    }

    _createClass(BulkUploadSecurity, [{
        key: 'render',
        value: function render() {
            var url = '/securities/' + this.props.data.isin;
            return _react2.default.createElement(
                'tr',
                null,
                _react2.default.createElement(
                    'td',
                    null,
                    _react2.default.createElement(
                        'a',
                        { href: url },
                        this.props.data.isin
                    )
                ),
                _react2.default.createElement(
                    'td',
                    null,
                    this.props.data.name
                ),
                _react2.default.createElement(
                    'td',
                    null,
                    this.props.data.startDate
                )
            );
        }
    }]);

    return BulkUploadSecurity;
}(_react2.default.Component);

},{"../../Utils/FileDrop":36,"../../Utils/Loading":37,"../../Utils/Message":38,"react":"react"}],24:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _Menu = require('./Menu');

var _Menu2 = _interopRequireDefault(_Menu);

var _Isin = require('./Isin/Isin');

var _Isin2 = _interopRequireDefault(_Isin);

var _BulkUpload = require('./BulkUpload/BulkUpload');

var _BulkUpload2 = _interopRequireDefault(_BulkUpload);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var DataEditor = function (_React$Component) {
    _inherits(DataEditor, _React$Component);

    function DataEditor() {
        _classCallCheck(this, DataEditor);

        var _this = _possibleConstructorReturn(this, (DataEditor.__proto__ || Object.getPrototypeOf(DataEditor)).call(this));

        var view = window.location.hash || 'isin';
        view = view.replace('#', '');

        _this.state = {
            currentView: view
        };
        _this.allViews = [{ id: "isin", title: "Add/Edit ISIN" }, { id: "isin-bulk", title: "Bulk upload ISIN" }, { id: "hierachy-bulk", title: "Bulk upload hiearchy" }];
        return _this;
    }

    _createClass(DataEditor, [{
        key: 'changeView',
        value: function changeView(newViewId) {
            this.setState({ currentView: newViewId });
        }
    }, {
        key: 'render',
        value: function render() {
            var contentArea = void 0;
            switch (this.state.currentView) {
                case 'isin-bulk':
                    contentArea = _react2.default.createElement(_BulkUpload2.default, { bulkStats: this.props.bulkStats });
                    break;
                case 'isin':
                default:
                    contentArea = _react2.default.createElement(_Isin2.default, { productOptions: this.props.productOptions });
            }

            return _react2.default.createElement(
                'div',
                { className: 'grid' },
                _react2.default.createElement(
                    'div',
                    { className: 'g 1/5' },
                    _react2.default.createElement(_Menu2.default, {
                        onChangeView: this.changeView.bind(this),
                        currentView: this.state.currentView,
                        allViews: this.allViews
                    })
                ),
                _react2.default.createElement(
                    'div',
                    { className: 'g 4/5' },
                    contentArea
                )
            );
        }
    }]);

    return DataEditor;
}(_react2.default.Component);

exports.default = DataEditor;

},{"./BulkUpload/BulkUpload":23,"./Isin/Isin":28,"./Menu":33,"react":"react"}],25:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _BaseField2 = require('./BaseField');

var _BaseField3 = _interopRequireDefault(_BaseField2);

var _Status = require('./Status');

var _Status2 = _interopRequireDefault(_Status);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var AutoCompleteField = function (_BaseField) {
    _inherits(AutoCompleteField, _BaseField);

    function AutoCompleteField() {
        _classCallCheck(this, AutoCompleteField);

        return _possibleConstructorReturn(this, (AutoCompleteField.__proto__ || Object.getPrototypeOf(AutoCompleteField)).apply(this, arguments));
    }

    _createClass(AutoCompleteField, [{
        key: 'getValue',
        value: function getValue() {
            return this.refs.textInput.value;
        }
    }, {
        key: 'setValue',
        value: function setValue(val) {
            var isEmpty = val.length == 0;
            this.setState({
                itemSelected: -1,
                fieldText: val,
                valueOptions: null,
                statusType: _Status2.default.STATUS_OK,
                statusText: 'OK'
            });
            if (isEmpty) {
                this.validateRequired();
                return;
            }

            // if value was set externally, it's valid
            this.props.onChange(this.props.id, null, true);
        }
    }, {
        key: 'handleInput',
        value: function handleInput() {
            if (this.timer) {
                clearTimeout(this.timer);
            }
            var val = this.refs.textInput.value,
                isEmpty = val.length == 0;
            this.setState({
                fieldText: val,
                itemSelected: -1,
                valueOptions: null,
                statusType: _Status2.default.STATUS_LOADING,
                statusText: null
            });
            if (isEmpty) {
                this.validateRequired(val);
                return;
            }

            var url = this.props.sourceUrl;
            url = url.replace('{search}', val);

            this.timer = setTimeout(function () {
                this.performSearch(url);
            }.bind(this), 400);
        }
    }, {
        key: 'performSearch',
        value: function performSearch(url, val) {

            // make an ajax call to get the ISIN.
            fetch(url, {
                method: 'get',
                credentials: 'same-origin'
            }).then(function (response) {
                return response.json();
            }.bind(this)).then(function (data) {
                if (data.results.length == 0) {
                    this.setState({
                        statusType: _Status2.default.STATUS_NEW,
                        statusText: 'New entry'
                    });
                    this.props.onChange(this.props.id, null, true);
                    return;
                }

                var autoValues = [],
                    matches = null;
                data.results.forEach(function (item) {
                    if (item.name == val) {
                        matches = item;
                    }
                    autoValues.push({
                        label: item.name,
                        data: item
                    });
                }.bind(this));

                if (matches) {
                    this.setState({
                        statusType: _Status2.default.STATUS_OK,
                        statusText: 'Entry exists. Re-using'
                    });

                    this.props.onChange(this.props.id, matches, true);
                    return;
                }

                if (autoValues.length == 0) {
                    this.setState({
                        statusType: _Status2.default.STATUS_NEW,
                        statusText: 'New Entry'
                    });
                } else {
                    this.setState({
                        statusType: _Status2.default.STATUS_NEW,
                        statusText: 'Choose entry',
                        valueOptions: autoValues
                    });
                }

                this.props.onChange(this.props.id, null, true);
            }.bind(this)).catch(function (err) {
                this.setState({
                    statusType: _Status2.default.STATUS_ERROR,
                    statusText: 'An error occurred'
                });
            }.bind(this));
        }
    }, {
        key: 'handleAutoCompleteSelect',
        value: function handleAutoCompleteSelect(data) {
            this.setState({
                fieldText: data.name,
                valueOptions: null,
                itemSelected: -1,
                statusType: _Status2.default.STATUS_OK,
                statusText: 'OK'
            });
            this.props.onChange(this.props.id, data, true);
        }
    }, {
        key: 'handleKey',
        value: function handleKey(event) {
            if (!this.state.valueOptions) {
                return; // nothing to do
            }
            var key = event.key,
                optionsCount = this.state.valueOptions.length;

            switch (key) {
                case 'ArrowDown':
                    event.preventDefault();
                    if (this.state.itemSelected < optionsCount - 1) {
                        this.setState({
                            itemSelected: this.state.itemSelected + 1
                        });
                    }
                    break;
                case 'ArrowUp':
                    event.preventDefault();
                    if (this.state.itemSelected > -1) {
                        this.setState({
                            itemSelected: this.state.itemSelected - 1
                        });
                    }
                    break;
                case 'Enter':
                    event.preventDefault();
                    if (this.state.itemSelected > -1) {
                        this.handleAutoCompleteSelect(this.state.valueOptions[this.state.itemSelected].data);
                    } else {
                        this.setState({
                            statusType: _Status2.default.STATUS_OK,
                            statusText: 'OK',
                            valueOptions: null
                        });
                    }
                    break;
            }
        }
    }, {
        key: 'render',
        value: function render() {
            var _this2 = this;

            var status = _react2.default.createElement(_Status2.default, {
                type: this.state.statusType,
                message: this.state.statusText
            });

            var autocomplete = null;
            if (this.state.valueOptions) {
                (function () {
                    var items = [];
                    _this2.state.valueOptions.forEach(function (item, i) {
                        items.push(_react2.default.createElement(AutoCompleteItem, {
                            key: i,
                            data: item.data,
                            active: this.state.itemSelected == i,
                            onClick: this.handleAutoCompleteSelect.bind(this),
                            label: item.label }));
                    }.bind(_this2));
                    autocomplete = _react2.default.createElement(
                        'ul',
                        { className: 'form__autocomplete' },
                        items
                    );
                })();
            }

            return _react2.default.createElement(
                'div',
                { className: 'form__group' },
                _react2.default.createElement(
                    'label',
                    { htmlFor: this.fieldId, className: 'form__label' },
                    this.props.label
                ),
                _react2.default.createElement('input', { className: 'form__input', id: this.fieldId,
                    value: this.state.fieldText,
                    ref: 'textInput',
                    disabled: this.state.disabled,
                    required: this.props.isRequired,
                    onChange: this.handleInput.bind(this),
                    onKeyDown: this.handleKey.bind(this) }),
                _react2.default.createElement(
                    'div',
                    { className: 'form__message' },
                    status
                ),
                autocomplete
            );
        }
    }]);

    return AutoCompleteField;
}(_BaseField3.default);

exports.default = AutoCompleteField;

var AutoCompleteItem = function (_React$Component) {
    _inherits(AutoCompleteItem, _React$Component);

    function AutoCompleteItem() {
        _classCallCheck(this, AutoCompleteItem);

        return _possibleConstructorReturn(this, (AutoCompleteItem.__proto__ || Object.getPrototypeOf(AutoCompleteItem)).apply(this, arguments));
    }

    _createClass(AutoCompleteItem, [{
        key: 'handleClick',
        value: function handleClick() {
            this.props.onClick(this.props.data);
        }
    }, {
        key: 'render',
        value: function render() {
            var className = 'form__autocomplete-item';
            if (this.props.active) {
                className += ' form__autocomplete-item--active';
            }

            return _react2.default.createElement(
                'li',
                { className: className, onClick: this.handleClick.bind(this) },
                this.props.label
            );
        }
    }]);

    return AutoCompleteItem;
}(_react2.default.Component);

},{"./BaseField":26,"./Status":32,"react":"react"}],26:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _Status = require('./Status');

var _Status2 = _interopRequireDefault(_Status);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var BaseField = function (_React$Component) {
    _inherits(BaseField, _React$Component);

    function BaseField() {
        _classCallCheck(this, BaseField);

        var _this = _possibleConstructorReturn(this, (BaseField.__proto__ || Object.getPrototypeOf(BaseField)).call(this));

        _this.fieldId = 'field-' + Math.floor(Math.random() * 10000);
        _this.state = {
            fieldText: '',
            disabled: false,
            statusType: null,
            statusText: null
        };
        return _this;
    }

    _createClass(BaseField, [{
        key: 'disable',
        value: function disable() {
            this.setState({
                disabled: true
            });
        }
    }, {
        key: 'enable',
        value: function enable() {
            this.setState({
                disabled: false
            });
        }
    }, {
        key: 'setValue',
        value: function setValue(val) {
            var isEmpty = val.length == 0;
            this.setState({
                fieldText: val,
                statusType: _Status2.default.STATUS_OK,
                statusText: 'OK'
            });
            if (isEmpty) {
                this.validateRequired();
                return;
            }

            // if value was set externally, it's valid
            this.props.onChange(this.props.id, null, true);
        }
    }, {
        key: 'validateRequired',
        value: function validateRequired() {
            if (this.props.isRequired) {
                this.setState({
                    statusType: _Status2.default.STATUS_ERROR,
                    statusText: 'Required'
                });
                this.props.onChange(this.props.id, null, false);
                return;
            }
            this.props.onChange(this.props.id, null, true);
        }
    }, {
        key: 'getStatusElement',
        value: function getStatusElement() {
            return _react2.default.createElement(_Status2.default, {
                type: this.state.statusType,
                message: this.state.statusText
            });
        }
    }]);

    return BaseField;
}(_react2.default.Component);

exports.default = BaseField;

},{"./Status":32,"react":"react"}],27:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _BaseField2 = require('./BaseField');

var _BaseField3 = _interopRequireDefault(_BaseField2);

var _Status = require('./Status');

var _Status2 = _interopRequireDefault(_Status);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var DateField = function (_BaseField) {
    _inherits(DateField, _BaseField);

    function DateField() {
        _classCallCheck(this, DateField);

        return _possibleConstructorReturn(this, (DateField.__proto__ || Object.getPrototypeOf(DateField)).apply(this, arguments));
    }

    _createClass(DateField, [{
        key: 'getValue',
        value: function getValue() {
            return this.refs.dateInput.value;
        }
    }, {
        key: 'handleInput',
        value: function handleInput() {
            var val = this.refs.dateInput.value;
            this.setState({
                fieldText: val,
                statusType: null,
                statusText: null
            });
            if (val.length == 0) {
                this.validateRequired();
                return;
            }

            if (this.isValidDate(val)) {
                this.setState({
                    statusType: _Status2.default.STATUS_OK,
                    statusText: 'Valid'
                });
                this.props.onChange(this.props.id, val, true);
                return;
            }

            this.setState({
                statusType: _Status2.default.STATUS_ERROR,
                statusText: 'Must be valid DD/MM/YYYY'
            });
            this.props.onChange(this.props.id, val, false);
        }
    }, {
        key: 'isValidDate',
        value: function isValidDate(s) {
            if (!/^[0-3][0-9]\/[0-1][0-9]\/[0-9]{4}$/.test(s)) {
                return false;
            }

            var bits = s.split('/'),
                y = bits[2],
                m = bits[1],
                d = bits[0],

            // Assume not leap year by default (note zero index for Jan)
            daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

            // If evenly divisible by 4 and not evenly divisible by 100,
            // or is evenly divisible by 400, then a leap year
            if (!(y % 4) && y % 100 || !(y % 400)) {
                daysInMonth[1] = 29;
            }
            return d <= daysInMonth[--m];
        }
    }, {
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                'div',
                { className: 'form__group' },
                _react2.default.createElement(
                    'label',
                    { htmlFor: this.fieldId, className: 'form__label' },
                    this.props.label
                ),
                _react2.default.createElement('input', { className: 'form__input', id: this.fieldId,
                    value: this.state.fieldText,
                    ref: 'dateInput',
                    required: this.props.isRequired,
                    onChange: this.handleInput.bind(this) }),
                _react2.default.createElement(
                    'div',
                    { className: 'form__message' },
                    this.getStatusElement()
                )
            );
        }
    }]);

    return DateField;
}(_BaseField3.default);

exports.default = DateField;

},{"./BaseField":26,"./Status":32,"react":"react"}],28:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _IsinField = require('./IsinField');

var _IsinField2 = _interopRequireDefault(_IsinField);

var _DateField = require('./DateField');

var _DateField2 = _interopRequireDefault(_DateField);

var _SimpleTextField = require('./SimpleTextField');

var _SimpleTextField2 = _interopRequireDefault(_SimpleTextField);

var _SimpleSelectField = require('./SimpleSelectField');

var _SimpleSelectField2 = _interopRequireDefault(_SimpleSelectField);

var _AutoCompleteField = require('./AutoCompleteField');

var _AutoCompleteField2 = _interopRequireDefault(_AutoCompleteField);

var _Status = require('./Status');

var _Status2 = _interopRequireDefault(_Status);

var _Message = require('../../Utils/Message');

var _Message2 = _interopRequireDefault(_Message);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var Isin = function (_React$Component) {
    _inherits(Isin, _React$Component);

    function Isin() {
        _classCallCheck(this, Isin);

        var _this = _possibleConstructorReturn(this, (Isin.__proto__ || Object.getPrototypeOf(Isin)).call(this));

        _this.state = {
            start: true,
            saving: false,
            messageType: null,
            messageText: null,
            invalidItems: []
        };
        return _this;
    }

    _createClass(Isin, [{
        key: 'canBeSaved',
        value: function canBeSaved() {
            return !this.state.start && !this.state.saving && Object.keys(this.state.invalidItems).length == 0;
        }
    }, {
        key: 'onFormChange',
        value: function onFormChange(id, value, valid) {
            var invalidItems = this.state.invalidItems;
            delete invalidItems[id];
            if (!valid) {
                invalidItems[id] = true;
            }
            this.setState({
                start: false,
                messageType: null,
                messageText: null,
                invalidItems: invalidItems
            });
        }
    }, {
        key: 'onIsinChange',
        value: function onIsinChange(id, value, valid, security) {
            this.onFormChange(id, value, valid);
            if (security) {
                this.refs.SECURITY_NAME.setValue(security.name || '');
                this.refs.SECURITY_START_DATE.setValue(security.startDate || '');
                this.refs.MATURITY_DATE.setValue(security.maturityDate || '');
                this.refs.SOURCE.setValue(security.source || '');
                this.refs.COUPON_RATE.setValue(security.coupon || '');
                this.refs.MONEY_RAISED_GBP.setValue(security.amountRaised || '');
                this.refs.MONEY_RAISED_LOCAL.setValue(security.amountRaisedLocal || '');
                this.refs.TRADING_CURRENCY.setValue(security.currency || '');
                this.refs.MARGIN.setValue(security.margin || '');

                if (security.issuer) {
                    this.setDataFromIssuer(security.issuer);
                }

                var product = '';
                if (security.product) {
                    product = security.product.number;
                }
                this.refs.PRA_ITEM_4748.setValue(product);
            }
        }
    }, {
        key: 'onIssuerChange',
        value: function onIssuerChange(id, data, valid) {
            this.onFormChange(id, data, valid);
            if (data) {
                this.setDataFromIssuer(data, true);
            } else {
                // this.refs.COUNTRY_OF_INCORPORATION.enable();
            }
        }
    }, {
        key: 'setDataFromIssuer',
        value: function setDataFromIssuer(issuer, excludeIssuerItself) {
            // let country = '';
            // if (issuer.country) {
            //     country = issuer.country.name;
            // }
            // this.refs.COUNTRY_OF_INCORPORATION.setValue(country);
            // this.refs.COUNTRY_OF_INCORPORATION.disable();

            if (excludeIssuerItself) {
                return;
            }
            this.refs.COMPANY_NAME.setValue(issuer.name);
        }
    }, {
        key: 'onSave',
        value: function onSave(e) {
            e.preventDefault();
            this.setState({
                saving: true,
                messageType: null,
                messageText: null
            });

            // prepare the ISIN data
            var fields = ['ISIN', 'SECURITY_NAME', 'SECURITY_START_DATE', 'MATURITY_DATE', 'SOURCE', 'COUPON_RATE', 'MONEY_RAISED_GBP', 'MONEY_RAISED_LOCAL', 'TRADING_CURRENCY', 'MARGIN', 'PRA_ITEM_4748', 'COMPANY_NAME'];

            var postData = {};
            fields.forEach(function (fieldId) {
                if (this.refs[fieldId]) {
                    postData[fieldId] = this.refs[fieldId].getValue();
                }
            }.bind(this));

            fetch('/admin/process-security.json', {
                method: 'post',
                body: JSON.stringify(postData),
                credentials: 'same-origin'
            }).then(function (response) {
                return response.json();
            }.bind(this)).then(function (data) {
                if (data.error) {
                    this.setState({
                        saving: false,
                        messageType: _Message2.default.TYPE_ERROR,
                        messageText: data.error
                    });
                    return;
                }

                this.setState({
                    saving: false,
                    messageType: _Message2.default.TYPE_OK,
                    messageText: 'Security saved successfully'
                });
            }.bind(this)).catch(function (err) {
                this.setState({
                    saving: false,
                    messageType: _Message2.default.TYPE_ERROR,
                    messageText: 'An error occurred saving the security'
                });
            }.bind(this));
        }
    }, {
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                'form',
                { onSubmit: this.onSave.bind(this) },
                _react2.default.createElement(
                    'h1',
                    { className: 'b g-unit' },
                    'Add/Edit ISIN'
                ),
                _react2.default.createElement(
                    'div',
                    { className: 'grid' },
                    _react2.default.createElement(
                        'div',
                        { className: 'g 1/2' },
                        _react2.default.createElement(
                            'span',
                            { className: 'e' },
                            '* Required'
                        )
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'g 1/2' },
                        _react2.default.createElement(
                            'div',
                            { className: 'text--right' },
                            _react2.default.createElement(_Status2.default, { isLoading: this.state.saving }),
                            _react2.default.createElement(
                                'button',
                                { className: 'button button--fat',
                                    type: 'submit',
                                    disabled: !this.canBeSaved() },
                                'Save'
                            )
                        )
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'g' },
                        _react2.default.createElement(_Message2.default, {
                            message: this.state.messageText,
                            type: this.state.messageType
                        })
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'g' },
                        _react2.default.createElement(_IsinField2.default, { id: 'ISIN',
                            ref: 'ISIN',
                            onChange: this.onIsinChange.bind(this),
                            label: 'ISIN: Enter new ISIN or one to search for*' })
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'g' },
                        _react2.default.createElement(_SimpleTextField2.default, { id: 'SECURITY_NAME',
                            ref: 'SECURITY_NAME',
                            onChange: this.onFormChange.bind(this),
                            isRequired: true,
                            label: 'SECURITY_NAME: Security Name*' })
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'g 1/2' },
                        _react2.default.createElement(_DateField2.default, { id: 'SECURITY_START_DATE',
                            ref: 'SECURITY_START_DATE',
                            onChange: this.onFormChange.bind(this),
                            isRequired: true,
                            label: 'SECURITY_START_DATE: Start Date*' })
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'g 1/2' },
                        _react2.default.createElement(_DateField2.default, { id: 'MATURITY_DATE',
                            ref: 'MATURITY_DATE',
                            onChange: this.onFormChange.bind(this),
                            isRequired: false,
                            label: 'MATURITY_DATE: Maturity Date' })
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'g 1/2' },
                        _react2.default.createElement(_SimpleTextField2.default, { id: 'SOURCE',
                            ref: 'SOURCE',
                            onChange: this.onFormChange.bind(this),
                            label: 'SOURCE: Source' })
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'g 1/2' },
                        _react2.default.createElement(_SimpleTextField2.default, { id: 'COUPON_RATE',
                            ref: 'COUPON_RATE',
                            regex: '^[0-9.]+[%]?$',
                            onChange: this.onFormChange.bind(this),
                            label: 'COUPON_RATE: Coupon (decimal, or with %)' })
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'g 1/2' },
                        _react2.default.createElement(_SimpleTextField2.default, { id: 'MONEY_RAISED_GBP',
                            ref: 'MONEY_RAISED_GBP',
                            regex: '^[0-9.]+$',
                            onChange: this.onFormChange.bind(this),
                            label: 'MONEY_RAISED_GBP: Money Raised (GBP £m)' })
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'g 1/2' },
                        _react2.default.createElement(_SimpleTextField2.default, { id: 'MONEY_RAISED_LOCAL',
                            ref: 'MONEY_RAISED_LOCAL',
                            regex: '^[0-9.]+$',
                            onChange: this.onFormChange.bind(this),
                            label: 'MONEY_RAISED_LOCAL: Money Raised (Local Currency)' })
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'g 1/2' },
                        _react2.default.createElement(_SimpleTextField2.default, { id: 'TRADING_CURRENCY',
                            ref: 'TRADING_CURRENCY',
                            regex: '^[A-Z]{3}$',
                            onChange: this.onFormChange.bind(this),
                            label: 'TRADING_CURRENCY: Trading Currency' })
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'g 1/2' },
                        _react2.default.createElement(_SimpleTextField2.default, { id: 'MARGIN',
                            ref: 'MARGIN',
                            regex: '^[0-9.]+[%]?$',
                            onChange: this.onFormChange.bind(this),
                            label: 'MARGIN: Margin (decimal, or with %)' })
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'g 1/2' },
                        _react2.default.createElement(_SimpleSelectField2.default, { id: 'PRA_ITEM_4748',
                            ref: 'PRA_ITEM_4748',
                            options: this.props.productOptions,
                            onChange: this.onFormChange.bind(this),
                            label: 'PRA_ITEM_4748: Product Type' })
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'g 1/2' },
                        _react2.default.createElement(_AutoCompleteField2.default, { id: 'COMPANY_NAME',
                            ref: 'COMPANY_NAME',
                            sourceUrl: '/admin/search.json?type=issuer&q={search}',
                            onChange: this.onIssuerChange.bind(this),
                            label: 'COMPANY_NAME: Issuer Name' })
                    )
                )
            );
        }
    }]);

    return Isin;
}(_react2.default.Component);

exports.default = Isin;

},{"../../Utils/Message":38,"./AutoCompleteField":25,"./DateField":27,"./IsinField":29,"./SimpleSelectField":30,"./SimpleTextField":31,"./Status":32,"react":"react"}],29:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _BaseField2 = require('./BaseField');

var _BaseField3 = _interopRequireDefault(_BaseField2);

var _Status = require('./Status');

var _Status2 = _interopRequireDefault(_Status);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var IsinField = function (_BaseField) {
    _inherits(IsinField, _BaseField);

    function IsinField() {
        _classCallCheck(this, IsinField);

        var _this = _possibleConstructorReturn(this, (IsinField.__proto__ || Object.getPrototypeOf(IsinField)).call(this));

        _this.state.loading = false;
        return _this;
    }

    _createClass(IsinField, [{
        key: 'getValue',
        value: function getValue() {
            return this.refs.isinInput.value;
        }
    }, {
        key: 'handleInput',
        value: function handleInput() {
            var val = this.getValue();
            this.setState({
                fieldText: val,
                statusType: null,
                statusText: null
            });
            this.props.onChange(this.props.id, val, false);
            if (val.length == 0) {
                this.setState({
                    statusType: _Status2.default.STATUS_ERROR,
                    statusText: 'Required'
                });
                return;
            }

            if (val.length != 12) {
                this.setState({
                    statusType: _Status2.default.STATUS_ERROR,
                    statusText: 'Must be 12 characters'
                });
                return;
            }
            this.setState({
                statusType: _Status2.default.STATUS_LOADING,
                statusText: 'Checking ISIN'
            });

            // make an ajax call to get the ISIN.
            fetch('/admin/securities-check/' + val + '.json', {
                method: 'get',
                credentials: 'same-origin'
            }).then(function (response) {
                return response.json();
            }.bind(this)).then(function (data) {
                if (data.status == 'error') {
                    this.setState({
                        statusType: _Status2.default.STATUS_ERROR,
                        statusText: 'Not a valid ISIN'
                    });
                    return;
                }

                if (data.status == 'found') {
                    this.setState({
                        statusType: _Status2.default.STATUS_OK,
                        statusText: 'ISIN found'
                    });

                    // send the data back to the main form, to complete all fields
                    this.props.onChange(this.props.id, val, true, data.security);
                    return;
                }

                if (data.status == 'new') {
                    this.setState({
                        statusType: _Status2.default.STATUS_NEW,
                        statusText: 'New ISIN'
                    });
                    this.props.onChange(this.props.id, val, true);
                    return;
                }

                this.setState({
                    statusType: _Status2.default.STATUS_ERROR,
                    statusText: 'ISIN could not be processed'
                });
                this.props.onChange(this.props.id, val, true);
            }.bind(this)).catch(function (err) {
                console.log(err);
                this.setState({
                    statusType: _Status2.default.STATUS_ERROR,
                    statusText: 'An error occurred checking this ISIN'
                });
            }.bind(this));
        }
    }, {
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                'div',
                { className: 'form__group' },
                _react2.default.createElement(
                    'label',
                    { htmlFor: this.fieldId, className: 'form__label' },
                    this.props.label
                ),
                _react2.default.createElement('input', { className: 'form__input', id: this.fieldId,
                    disabled: this.state.isLoading,
                    required: true,
                    value: this.state.fieldText,
                    ref: 'isinInput',
                    onChange: this.handleInput.bind(this) }),
                _react2.default.createElement(
                    'div',
                    { className: 'form__message' },
                    this.getStatusElement()
                )
            );
        }
    }]);

    return IsinField;
}(_BaseField3.default);

exports.default = IsinField;

},{"./BaseField":26,"./Status":32,"react":"react"}],30:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _BaseField2 = require('./BaseField');

var _BaseField3 = _interopRequireDefault(_BaseField2);

var _Status = require('./Status');

var _Status2 = _interopRequireDefault(_Status);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var SimpleSelectField = function (_BaseField) {
    _inherits(SimpleSelectField, _BaseField);

    function SimpleSelectField() {
        _classCallCheck(this, SimpleSelectField);

        return _possibleConstructorReturn(this, (SimpleSelectField.__proto__ || Object.getPrototypeOf(SimpleSelectField)).apply(this, arguments));
    }

    _createClass(SimpleSelectField, [{
        key: 'getValue',
        value: function getValue() {
            return this.refs.inputField.value;
        }
    }, {
        key: 'handleInput',
        value: function handleInput() {
            var val = this.refs.inputField.value;
            this.setState({
                fieldText: val
            });
            this.props.onChange(this.props.id, val, true);
        }
    }, {
        key: 'render',
        value: function render() {
            var items = [];
            items.push(_react2.default.createElement('option', { key: '', value: '' }));
            this.props.options.forEach(function (option, i) {
                items.push(_react2.default.createElement(
                    'option',
                    { key: option.value, value: option.value },
                    option.label
                ));
            }.bind(this));

            return _react2.default.createElement(
                'div',
                { className: 'form__group' },
                _react2.default.createElement(
                    'label',
                    { htmlFor: this.fieldId, className: 'form__label' },
                    this.props.label
                ),
                _react2.default.createElement(
                    'select',
                    { className: 'form__input',
                        ref: 'inputField',
                        id: this.fieldId,
                        value: this.state.fieldText,
                        onChange: this.handleInput.bind(this) },
                    items
                )
            );
        }
    }]);

    return SimpleSelectField;
}(_BaseField3.default);

exports.default = SimpleSelectField;

},{"./BaseField":26,"./Status":32,"react":"react"}],31:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _BaseField2 = require('./BaseField');

var _BaseField3 = _interopRequireDefault(_BaseField2);

var _Status = require('./Status');

var _Status2 = _interopRequireDefault(_Status);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var SimpleTextField = function (_BaseField) {
    _inherits(SimpleTextField, _BaseField);

    function SimpleTextField() {
        _classCallCheck(this, SimpleTextField);

        return _possibleConstructorReturn(this, (SimpleTextField.__proto__ || Object.getPrototypeOf(SimpleTextField)).apply(this, arguments));
    }

    _createClass(SimpleTextField, [{
        key: 'getValue',
        value: function getValue() {
            return this.refs.textInput.value;
        }
    }, {
        key: 'handleInput',
        value: function handleInput() {
            var val = this.refs.textInput.value;
            this.setState({
                fieldText: val,
                statusType: null,
                statusText: null
            });
            if (val.length == 0 && this.props.isRequired) {
                this.setState({
                    statusType: _Status2.default.STATUS_ERROR,
                    statusText: 'Required'
                });
                this.props.onChange(this.props.id, val, false);
                return;
            }

            if (val.length > 0 && this.props.regex) {
                var regex = new RegExp(this.props.regex);
                if (!regex.test(val)) {
                    this.setState({
                        statusType: _Status2.default.STATUS_ERROR,
                        statusText: 'Invalid data'
                    });
                    this.props.onChange(this.props.id, val, false);
                    return;
                }
            }

            if (val.length > 0) {
                this.setState({
                    statusType: _Status2.default.STATUS_OK,
                    statusText: 'OK'
                });
            }
            this.props.onChange(this.props.id, val, true);
        }
    }, {
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                'div',
                { className: 'form__group' },
                _react2.default.createElement(
                    'label',
                    { htmlFor: this.fieldId, className: 'form__label' },
                    this.props.label
                ),
                _react2.default.createElement('input', { className: 'form__input', id: this.fieldId,
                    value: this.state.fieldText,
                    ref: 'textInput',
                    required: this.props.isRequired,
                    onChange: this.handleInput.bind(this) }),
                _react2.default.createElement(
                    'div',
                    { className: 'form__message' },
                    this.getStatusElement()
                )
            );
        }
    }]);

    return SimpleTextField;
}(_BaseField3.default);

exports.default = SimpleTextField;

},{"./BaseField":26,"./Status":32,"react":"react"}],32:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

// import React from 'react';
// import Loading from '../../Utils/Loading';

var Status = function (_React$Component) {
    _inherits(Status, _React$Component);

    function Status() {
        _classCallCheck(this, Status);

        return _possibleConstructorReturn(this, (Status.__proto__ || Object.getPrototypeOf(Status)).apply(this, arguments));
    }

    _createClass(Status, [{
        key: 'render',
        value: function render() {
            switch (this.props.type) {
                case Status.STATUS_LOADING:
                    return React.createElement(StatusLoading, null);
                case Status.STATUS_ERROR:
                    return React.createElement(StatusError, { message: this.props.message });
                case Status.STATUS_NEW:
                    return React.createElement(StatusNew, { message: this.props.message });
                case Status.STATUS_OK:
                    return React.createElement(StatusOk, { message: this.props.message });
            }

            return React.createElement(StatusEmpty, null);
        }
    }], [{
        key: 'STATUS_OK',
        get: function get() {
            return 'ok';
        }
    }, {
        key: 'STATUS_NEW',
        get: function get() {
            return 'new';
        }
    }, {
        key: 'STATUS_ERROR',
        get: function get() {
            return 'error';
        }
    }, {
        key: 'STATUS_LOADING',
        get: function get() {
            return 'loading';
        }
    }]);

    return Status;
}(React.Component);

exports.default = Status;

var StatusEmpty = function (_React$Component2) {
    _inherits(StatusEmpty, _React$Component2);

    function StatusEmpty() {
        _classCallCheck(this, StatusEmpty);

        return _possibleConstructorReturn(this, (StatusEmpty.__proto__ || Object.getPrototypeOf(StatusEmpty)).apply(this, arguments));
    }

    _createClass(StatusEmpty, [{
        key: 'render',
        value: function render() {
            return null;
        }
    }]);

    return StatusEmpty;
}(React.Component);

var StatusLoading = function (_React$Component3) {
    _inherits(StatusLoading, _React$Component3);

    function StatusLoading() {
        _classCallCheck(this, StatusLoading);

        return _possibleConstructorReturn(this, (StatusLoading.__proto__ || Object.getPrototypeOf(StatusLoading)).apply(this, arguments));
    }

    _createClass(StatusLoading, [{
        key: 'render',
        value: function render() {
            return React.createElement(
                'span',
                { className: 'icon-text' },
                React.createElement(
                    'span',
                    { className: 'icon-text__icon' },
                    React.createElement(Loading, null)
                ),
                React.createElement(
                    'span',
                    { className: 'icon-text__text' },
                    this.props.message
                )
            );
        }
    }]);

    return StatusLoading;
}(React.Component);

var StatusOk = function (_React$Component4) {
    _inherits(StatusOk, _React$Component4);

    function StatusOk() {
        _classCallCheck(this, StatusOk);

        return _possibleConstructorReturn(this, (StatusOk.__proto__ || Object.getPrototypeOf(StatusOk)).apply(this, arguments));
    }

    _createClass(StatusOk, [{
        key: 'render',
        value: function render() {
            return React.createElement(
                'span',
                { className: 'icon-text icon-text--ok' },
                React.createElement(
                    'span',
                    { className: 'icon-text__icon' },
                    React.createElement(
                        'svg',
                        {
                            viewBox: '0 0 24 24',
                            xmlns: 'http://www.w3.org/2000/svg',
                            xmlnsXlink: 'http://www.w3.org/1999/xlink' },
                        React.createElement('use', { xlinkHref: '#icon-ok' })
                    )
                ),
                React.createElement(
                    'span',
                    { className: 'icon-text__text' },
                    this.props.message
                )
            );
        }
    }]);

    return StatusOk;
}(React.Component);

var StatusNew = function (_React$Component5) {
    _inherits(StatusNew, _React$Component5);

    function StatusNew() {
        _classCallCheck(this, StatusNew);

        return _possibleConstructorReturn(this, (StatusNew.__proto__ || Object.getPrototypeOf(StatusNew)).apply(this, arguments));
    }

    _createClass(StatusNew, [{
        key: 'render',
        value: function render() {
            return React.createElement(
                'span',
                { className: 'icon-text icon-text--info' },
                React.createElement(
                    'span',
                    { className: 'icon-text__icon' },
                    React.createElement(
                        'svg',
                        {
                            viewBox: '0 0 24 24',
                            xmlns: 'http://www.w3.org/2000/svg',
                            xmlnsXlink: 'http://www.w3.org/1999/xlink' },
                        React.createElement('use', { xlinkHref: '#icon-add' })
                    )
                ),
                React.createElement(
                    'span',
                    { className: 'icon-text__text' },
                    this.props.message
                )
            );
        }
    }]);

    return StatusNew;
}(React.Component);

var StatusError = function (_React$Component6) {
    _inherits(StatusError, _React$Component6);

    function StatusError() {
        _classCallCheck(this, StatusError);

        return _possibleConstructorReturn(this, (StatusError.__proto__ || Object.getPrototypeOf(StatusError)).apply(this, arguments));
    }

    _createClass(StatusError, [{
        key: 'render',
        value: function render() {
            return React.createElement(
                'span',
                { className: 'icon-text icon-text--error' },
                React.createElement(
                    'span',
                    { className: 'icon-text__icon' },
                    React.createElement(
                        'svg',
                        {
                            viewBox: '0 0 24 24',
                            xmlns: 'http://www.w3.org/2000/svg',
                            xmlnsXlink: 'http://www.w3.org/1999/xlink' },
                        React.createElement('use', { xlinkHref: '#icon-close' })
                    )
                ),
                React.createElement(
                    'span',
                    { className: 'icon-text__text' },
                    this.props.message
                )
            );
        }
    }]);

    return StatusError;
}(React.Component);

},{}],33:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require("react");

var _react2 = _interopRequireDefault(_react);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var Menu = function (_React$Component) {
    _inherits(Menu, _React$Component);

    function Menu() {
        _classCallCheck(this, Menu);

        return _possibleConstructorReturn(this, (Menu.__proto__ || Object.getPrototypeOf(Menu)).call(this));
    }

    _createClass(Menu, [{
        key: "changeView",
        value: function changeView(newViewId) {
            this.props.onChangeView(newViewId);
        }
    }, {
        key: "render",
        value: function render() {

            var items = [],
                currentView = this.props.currentView,
                onChange = this.changeView.bind(this);

            this.props.allViews.forEach(function (i) {
                items.push(_react2.default.createElement(MenuItem, {
                    key: i.id,
                    id: i.id,
                    title: i.title,
                    onChangeView: onChange,
                    currentView: currentView }));
            });

            return _react2.default.createElement(
                "div",
                { className: "finder" },
                _react2.default.createElement(
                    "ul",
                    { className: "finder__list" },
                    items
                )
            );
        }
    }]);

    return Menu;
}(_react2.default.Component);

exports.default = Menu;

var MenuItem = function (_React$Component2) {
    _inherits(MenuItem, _React$Component2);

    function MenuItem() {
        _classCallCheck(this, MenuItem);

        return _possibleConstructorReturn(this, (MenuItem.__proto__ || Object.getPrototypeOf(MenuItem)).apply(this, arguments));
    }

    _createClass(MenuItem, [{
        key: "changeView",
        value: function changeView() {
            this.props.onChangeView(this.props.id);
        }
    }, {
        key: "render",
        value: function render() {
            var itemClass = 'finder__item' + (this.props.currentView == this.props.id ? ' finder__active' : '');
            return _react2.default.createElement(
                "li",
                { className: itemClass },
                _react2.default.createElement(
                    "a",
                    { className: "finder__link", href: "#", onClick: this.changeView.bind(this) },
                    _react2.default.createElement(
                        "span",
                        { className: "finder__indicator finder__indicator--nodrop" },
                        _react2.default.createElement(
                            "svg",
                            { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24" },
                            _react2.default.createElement("circle", { cx: "12", cy: "12", r: "8" })
                        )
                    ),
                    _react2.default.createElement(
                        "span",
                        { className: "finder__text" },
                        this.props.title
                    )
                )
            );
        }
    }]);

    return MenuItem;
}(_react2.default.Component);

},{"react":"react"}],34:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _Lightbox = require('./Lightbox');

var _Lightbox2 = _interopRequireDefault(_Lightbox);

var _AutoComplete = require('./../utils/AutoComplete');

var _AutoComplete2 = _interopRequireDefault(_AutoComplete);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var Issuer = function (_React$Component) {
    _inherits(Issuer, _React$Component);

    function Issuer() {
        _classCallCheck(this, Issuer);

        // Initial state of the component
        var _this = _possibleConstructorReturn(this, (Issuer.__proto__ || Object.getPrototypeOf(Issuer)).call(this));

        _this.state = {
            loading: false,
            modals: {
                issuer: false,
                country: false,
                group: false
            },
            issuer: _this.getEmptyIssuer()
        };
        var ac = new _AutoComplete2.default('/issuers/%s.json', _this.singleIssuer.bind(_this));

        var noGroups = document.querySelectorAll('[data-js="fetch-issuer"]'),
            l = noGroups.length,
            i;
        for (i = 0; i < l; i++) {
            noGroups[i].addEventListener('click', function (e) {
                this.setState({ loading: true });
                window.scrollTo(0, 0);
                ac.newValue(e.target.dataset.id);
            }.bind(_this));
        }
        return _this;
    }

    _createClass(Issuer, [{
        key: 'singleIssuer',
        value: function singleIssuer(data) {
            this.setState({
                loading: false,
                issuer: data.issuer
            });
        }
    }, {
        key: 'getEmptyIssuer',
        value: function getEmptyIssuer() {
            return {
                name: null,
                id: null,
                country: {
                    name: null
                },
                parentGroup: {
                    name: null
                }
            };
        }
    }, {
        key: 'modalOpen',
        value: function modalOpen(type, e) {
            e.preventDefault();
            var modals = this.state.modals;
            modals[type] = true;
            this.setState({ modals: modals });
        }
    }, {
        key: 'clearIssuer',
        value: function clearIssuer(e) {
            this.setState({ issuer: this.getEmptyIssuer() });
        }
    }, {
        key: 'useIssuerCallback',
        value: function useIssuerCallback(issuer) {
            this.setState({
                issuer: issuer
            });
            this.modalCloseCallback('issuer');
        }
    }, {
        key: 'useGroupCallback',
        value: function useGroupCallback(group) {
            var issuer = this.state.issuer;
            issuer.parentGroup = group;
            this.setState({
                issuer: issuer
            });
            this.modalCloseCallback('group');
        }
    }, {
        key: 'useCountryCallback',
        value: function useCountryCallback(country) {
            var issuer = this.state.issuer;
            issuer.country = country;
            this.setState({
                issuer: issuer
            });
            this.modalCloseCallback('country');
        }
    }, {
        key: 'modalCloseCallback',
        value: function modalCloseCallback(name) {
            var modals = this.state.modals;
            modals[name] = false;
            this.setState({
                modals: modals
            });
        }
    }, {
        key: 'handleInputChange',
        value: function handleInputChange(prop, event) {
            var issuer = this.state.issuer;
            issuer[prop] = event.target.value;
            this.setState({
                issuer: issuer
            });
        }
    }, {
        key: 'removeValue',
        value: function removeValue(prop, e) {
            e.preventDefault();
            var issuer = this.state.issuer,
                original = this.getEmptyIssuer();
            issuer[prop] = original[prop];
            this.setState({
                issuer: issuer
            });
        }
    }, {
        key: 'render',
        value: function render() {
            var content = _react2.default.createElement(
                'div',
                { className: 'g-unit' },
                _react2.default.createElement(
                    'h2',
                    { className: 'c g-unit' },
                    'Add / Edit Issuer'
                ),
                _react2.default.createElement(
                    'p',
                    { className: 'text--right' },
                    _react2.default.createElement(
                        'button',
                        { className: 'button', onClick: this.modalOpen.bind(this, 'issuer') },
                        _react2.default.createElement(
                            'span',
                            { className: 'button__icon' },
                            _react2.default.createElement(
                                'svg',
                                {
                                    viewBox: '0 0 24 24',
                                    xmlns: 'http://www.w3.org/2000/svg',
                                    xmlnsXlink: 'http://www.w3.org/1999/xlink' },
                                _react2.default.createElement('use', { xlinkHref: '#icon-search' })
                            )
                        ),
                        _react2.default.createElement(
                            'span',
                            { className: 'button__text' },
                            'Search for existing issuer'
                        )
                    ),
                    " ",
                    _react2.default.createElement(
                        'button',
                        { className: 'button', onClick: this.clearIssuer.bind(this) },
                        _react2.default.createElement(
                            'span',
                            { className: 'button__icon' },
                            _react2.default.createElement(
                                'svg',
                                {
                                    viewBox: '0 0 24 24',
                                    xmlns: 'http://www.w3.org/2000/svg',
                                    xmlnsXlink: 'http://www.w3.org/1999/xlink' },
                                _react2.default.createElement('use', { xlinkHref: '#icon-close' })
                            )
                        ),
                        _react2.default.createElement(
                            'span',
                            { className: 'button__text' },
                            'Clear form'
                        )
                    )
                ),
                _react2.default.createElement(
                    'form',
                    { method: 'post' },
                    _react2.default.createElement(
                        'div',
                        { className: 'form__group' },
                        _react2.default.createElement(
                            'label',
                            { className: 'form__label', 'for': 'field-name' },
                            'Name'
                        ),
                        _react2.default.createElement('input', { className: 'form__input', id: 'field-name', name: 'field-name', required: true,
                            onChange: this.handleInputChange.bind(this, 'name'),
                            value: this.state.issuer.name })
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'form__group' },
                        _react2.default.createElement(
                            'label',
                            { className: 'form__label', 'for': 'field-country' },
                            _react2.default.createElement(
                                'em',
                                null,
                                'Country'
                            )
                        ),
                        _react2.default.createElement(
                            'div',
                            { className: 'grid' },
                            _react2.default.createElement(
                                'div',
                                { className: 'g 2/3' },
                                _react2.default.createElement('input', { className: 'form__input', id: 'field-country', name: 'field-country',
                                    readOnly: true,
                                    value: this.state.issuer.country ? this.state.issuer.country.name : null })
                            ),
                            _react2.default.createElement(
                                'div',
                                { className: 'g 1/6' },
                                _react2.default.createElement(
                                    'button',
                                    { className: 'button', onClick: this.modalOpen.bind(this, 'country') },
                                    _react2.default.createElement(
                                        'span',
                                        { className: 'button__icon' },
                                        _react2.default.createElement(
                                            'svg',
                                            {
                                                viewBox: '0 0 24 24',
                                                xmlns: 'http://www.w3.org/2000/svg',
                                                xmlnsXlink: 'http://www.w3.org/1999/xlink' },
                                            _react2.default.createElement('use', { xlinkHref: '#icon-edit' })
                                        )
                                    )
                                )
                            ),
                            _react2.default.createElement(
                                'div',
                                { className: 'g 1/6' },
                                _react2.default.createElement(
                                    'button',
                                    { className: 'button', onClick: this.removeValue.bind(this, 'country') },
                                    _react2.default.createElement(
                                        'span',
                                        { className: 'button__icon' },
                                        _react2.default.createElement(
                                            'svg',
                                            {
                                                viewBox: '0 0 24 24',
                                                xmlns: 'http://www.w3.org/2000/svg',
                                                xmlnsXlink: 'http://www.w3.org/1999/xlink' },
                                            _react2.default.createElement('use', { xlinkHref: '#icon-close' })
                                        )
                                    )
                                )
                            )
                        )
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'form__group' },
                        _react2.default.createElement(
                            'label',
                            { className: 'form__label', 'for': 'field-group' },
                            _react2.default.createElement(
                                'em',
                                null,
                                'Parent Group'
                            )
                        ),
                        _react2.default.createElement(
                            'div',
                            { className: 'grid' },
                            _react2.default.createElement(
                                'div',
                                { className: 'g 2/3' },
                                _react2.default.createElement('input', { className: 'form__input', id: 'field-group', name: 'field-group',
                                    readOnly: true,
                                    value: this.state.issuer.parentGroup ? this.state.issuer.parentGroup.name : null })
                            ),
                            _react2.default.createElement(
                                'div',
                                { className: 'g 1/6' },
                                _react2.default.createElement(
                                    'button',
                                    { className: 'button', onClick: this.modalOpen.bind(this, 'group') },
                                    _react2.default.createElement(
                                        'span',
                                        { className: 'button__icon' },
                                        _react2.default.createElement(
                                            'svg',
                                            {
                                                viewBox: '0 0 24 24',
                                                xmlns: 'http://www.w3.org/2000/svg',
                                                xmlnsXlink: 'http://www.w3.org/1999/xlink' },
                                            _react2.default.createElement('use', { xlinkHref: '#icon-edit' })
                                        )
                                    )
                                )
                            ),
                            _react2.default.createElement(
                                'div',
                                { className: 'g 1/6' },
                                _react2.default.createElement(
                                    'button',
                                    { className: 'button', onClick: this.removeValue.bind(this, 'parentGroup') },
                                    _react2.default.createElement(
                                        'span',
                                        { className: 'button__icon' },
                                        _react2.default.createElement(
                                            'svg',
                                            {
                                                viewBox: '0 0 24 24',
                                                xmlns: 'http://www.w3.org/2000/svg',
                                                xmlnsXlink: 'http://www.w3.org/1999/xlink' },
                                            _react2.default.createElement('use', { xlinkHref: '#icon-close' })
                                        )
                                    )
                                )
                            )
                        )
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'form__group' },
                        _react2.default.createElement(
                            'label',
                            { className: 'form__label', 'for': 'field-id' },
                            _react2.default.createElement(
                                'em',
                                null,
                                'ID'
                            )
                        ),
                        _react2.default.createElement('input', { 'data-js': 'field-id', className: 'form__input', id: 'field-id', name: 'field-id', readOnly: true,
                            value: this.state.issuer.id })
                    ),
                    _react2.default.createElement(
                        'p',
                        { className: 'text--right g-unit' },
                        _react2.default.createElement(
                            'button',
                            { id: 'save-isin', type: 'submit', className: 'button button--fat' },
                            this.state.issuer.id ? "Update" : "Save new"
                        )
                    )
                ),
                _react2.default.createElement(
                    _Lightbox2.default,
                    { modalIsOpen: this.state.modals.issuer,
                        closeCallback: this.modalCloseCallback.bind(this, 'issuer'),
                        title: 'Search for issuer' },
                    _react2.default.createElement(IssuerFinder, { useCallback: this.useIssuerCallback.bind(this) })
                ),
                _react2.default.createElement(
                    _Lightbox2.default,
                    { modalIsOpen: this.state.modals.country,
                        closeCallback: this.modalCloseCallback.bind(this, 'country'),
                        title: 'Select country' },
                    _react2.default.createElement(CountryFinder, { useCallback: this.useCountryCallback.bind(this) })
                ),
                _react2.default.createElement(
                    _Lightbox2.default,
                    { modalIsOpen: this.state.modals.group,
                        closeCallback: this.modalCloseCallback.bind(this, 'group'),
                        title: 'Select parent group' },
                    _react2.default.createElement(GroupFinder, { useCallback: this.useGroupCallback.bind(this) })
                )
            );

            return this.state.loading ? _react2.default.createElement(Loading, null) : content;
        }
    }]);

    return Issuer;
}(_react2.default.Component);

exports.default = Issuer;

var Loading = function (_React$Component2) {
    _inherits(Loading, _React$Component2);

    function Loading() {
        _classCallCheck(this, Loading);

        return _possibleConstructorReturn(this, (Loading.__proto__ || Object.getPrototypeOf(Loading)).apply(this, arguments));
    }

    _createClass(Loading, [{
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                'div',
                { className: 'loading g--align-center' },
                _react2.default.createElement(
                    'svg',
                    { className: 'loading__spinner', viewBox: '-2 -2 70 70', xmlns: 'http://www.w3.org/2000/svg' },
                    _react2.default.createElement('circle', { className: 'loading__path', fill: 'none', strokeWidth: '8', cx: '33', cy: '33', r: '30' })
                )
            );
        }
    }]);

    return Loading;
}(_react2.default.Component);

var IssuerFinder = function (_React$Component3) {
    _inherits(IssuerFinder, _React$Component3);

    function IssuerFinder() {
        _classCallCheck(this, IssuerFinder);

        // Initial state of the component
        var _this3 = _possibleConstructorReturn(this, (IssuerFinder.__proto__ || Object.getPrototypeOf(IssuerFinder)).call(this));

        _this3.state = {
            fieldText: '',
            empty: true,
            loading: false,
            resultData: null
        };
        _this3.autocomplete = new _AutoComplete2.default('/search.json?q=%s', _this3.displayResults.bind(_this3));
        return _this3;
    }

    _createClass(IssuerFinder, [{
        key: 'handleSearchInput',
        value: function handleSearchInput() {
            var val = this.refs.searchInput.value;
            if (val.length > 0) {
                this.setState({
                    fieldText: val,
                    empty: false,
                    loading: true
                });
                this.autocomplete.newValue(val);
            } else {
                this.setState({
                    fieldText: val,
                    loading: false,
                    empty: true
                });
            }
        }
    }, {
        key: 'useCallback',
        value: function useCallback(issuer) {
            this.props.useCallback(issuer);
        }
    }, {
        key: 'displayResults',
        value: function displayResults(results) {
            this.setState({
                loading: false,
                resultData: results.issuers
            });
        }
    }, {
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                'div',
                null,
                _react2.default.createElement(
                    'div',
                    { className: 'form__group' },
                    _react2.default.createElement('input', {
                        type: 'text',
                        className: 'form__input',
                        placeholder: 'Enter part of an issuer name',
                        value: this.state.fieldText,
                        ref: 'searchInput',
                        onChange: this.handleSearchInput.bind(this)
                    })
                ),
                _react2.default.createElement(
                    'div',
                    null,
                    this.state.empty ? null : this.state.loading ? _react2.default.createElement(Loading, null) : _react2.default.createElement(IssuerResults, { data: this.state.resultData, useCallback: this.useCallback.bind(this) })
                )
            );
        }
    }]);

    return IssuerFinder;
}(_react2.default.Component);

var GroupFinder = function (_React$Component4) {
    _inherits(GroupFinder, _React$Component4);

    function GroupFinder() {
        _classCallCheck(this, GroupFinder);

        // Initial state of the component
        var _this4 = _possibleConstructorReturn(this, (GroupFinder.__proto__ || Object.getPrototypeOf(GroupFinder)).call(this));

        _this4.state = {
            fieldText: '',
            empty: true,
            loading: false,
            resultData: null
        };
        _this4.autocomplete = new _AutoComplete2.default('/search.json?q=%s', _this4.displayResults.bind(_this4));
        return _this4;
    }

    _createClass(GroupFinder, [{
        key: 'handleSearchInput',
        value: function handleSearchInput() {
            var val = this.refs.searchInput.value;
            if (val.length > 0) {
                this.setState({
                    fieldText: val,
                    empty: false,
                    loading: true
                });
                this.autocomplete.newValue(val);
            } else {
                this.setState({
                    fieldText: val,
                    loading: false,
                    empty: true
                });
            }
        }
    }, {
        key: 'useCallback',
        value: function useCallback(group) {
            this.props.useCallback(group);
        }
    }, {
        key: 'useNew',
        value: function useNew() {
            var val = this.refs.searchInput.value;
            this.props.useCallback({
                name: val
            });
        }
    }, {
        key: 'displayResults',
        value: function displayResults(results) {
            this.setState({
                loading: false,
                resultData: results.groups
            });
        }
    }, {
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                'div',
                null,
                _react2.default.createElement(
                    'div',
                    { className: 'form__group' },
                    _react2.default.createElement(
                        'div',
                        { className: 'grid' },
                        _react2.default.createElement(
                            'div',
                            { className: 'g 4/5' },
                            _react2.default.createElement('input', {
                                type: 'text',
                                className: 'form__input',
                                placeholder: 'Enter part of a parent group name or enter a new one',
                                value: this.state.fieldText,
                                ref: 'searchInput',
                                onChange: this.handleSearchInput.bind(this)
                            })
                        ),
                        _react2.default.createElement(
                            'div',
                            { className: 'g 1/5' },
                            _react2.default.createElement(
                                'button',
                                { className: 'button button--fat', onClick: this.useNew.bind(this) },
                                'Use new'
                            )
                        )
                    )
                ),
                _react2.default.createElement(
                    'div',
                    null,
                    this.state.empty ? null : this.state.loading ? _react2.default.createElement(Loading, null) : _react2.default.createElement(GroupResults, { data: this.state.resultData, useCallback: this.useCallback.bind(this) })
                )
            );
        }
    }]);

    return GroupFinder;
}(_react2.default.Component);

var CountryFinder = function (_React$Component5) {
    _inherits(CountryFinder, _React$Component5);

    function CountryFinder() {
        _classCallCheck(this, CountryFinder);

        var _this5 = _possibleConstructorReturn(this, (CountryFinder.__proto__ || Object.getPrototypeOf(CountryFinder)).call(this));

        _this5.state = {
            fieldText: '',
            loading: true,
            resultData: null
        };

        var autoComplete = new _AutoComplete2.default('/countries.json', _this5.displayResults.bind(_this5));
        autoComplete.newValue('');
        return _this5;
    }

    _createClass(CountryFinder, [{
        key: 'handleSearchInput',
        value: function handleSearchInput() {
            var val = this.refs.searchInput.value;
            this.setState({
                fieldText: val
            });
        }
    }, {
        key: 'useCallback',
        value: function useCallback(group) {
            this.props.useCallback(group);
        }
    }, {
        key: 'useNew',
        value: function useNew() {
            var val = this.refs.searchInput.value;
            this.props.useCallback({
                name: val
            });
        }
    }, {
        key: 'displayResults',
        value: function displayResults(results) {
            this.setState({
                loading: false,
                resultData: results.countries
            });
        }
    }, {
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                'div',
                null,
                _react2.default.createElement(
                    'div',
                    { className: 'form__group' },
                    _react2.default.createElement(
                        'div',
                        { className: 'grid' },
                        _react2.default.createElement(
                            'div',
                            { className: 'g 4/5' },
                            _react2.default.createElement('input', {
                                type: 'text',
                                className: 'form__input',
                                placeholder: 'Enter a new country name',
                                value: this.state.fieldText,
                                ref: 'searchInput',
                                onChange: this.handleSearchInput.bind(this)
                            })
                        ),
                        _react2.default.createElement(
                            'div',
                            { className: 'g 1/5' },
                            _react2.default.createElement(
                                'button',
                                { className: 'button button--fat', onClick: this.useNew.bind(this) },
                                'Create new'
                            )
                        )
                    )
                ),
                _react2.default.createElement(
                    'div',
                    null,
                    this.state.loading ? _react2.default.createElement(Loading, null) : _react2.default.createElement(CountryResults, { data: this.state.resultData, useCallback: this.useCallback.bind(this) })
                )
            );
        }
    }]);

    return CountryFinder;
}(_react2.default.Component);

var IssuerResults = function (_React$Component6) {
    _inherits(IssuerResults, _React$Component6);

    function IssuerResults() {
        _classCallCheck(this, IssuerResults);

        return _possibleConstructorReturn(this, (IssuerResults.__proto__ || Object.getPrototypeOf(IssuerResults)).apply(this, arguments));
    }

    _createClass(IssuerResults, [{
        key: 'useCallback',
        value: function useCallback(issuer) {
            this.props.useCallback(issuer);
        }
    }, {
        key: 'render',
        value: function render() {
            var items = [];
            var data = this.props.data;
            data.forEach(function (issuer) {
                items.push(_react2.default.createElement(IssuerResult, { issuer: issuer, useCallback: this.useCallback.bind(this) }));
            }.bind(this));
            if (items.length) {
                return _react2.default.createElement(
                    'ul',
                    { className: 'list--lined' },
                    items
                );
            }
            return _react2.default.createElement(
                'p',
                { className: 'text--center' },
                'No results'
            );
        }
    }]);

    return IssuerResults;
}(_react2.default.Component);

var IssuerResult = function (_React$Component7) {
    _inherits(IssuerResult, _React$Component7);

    function IssuerResult() {
        _classCallCheck(this, IssuerResult);

        return _possibleConstructorReturn(this, (IssuerResult.__proto__ || Object.getPrototypeOf(IssuerResult)).apply(this, arguments));
    }

    _createClass(IssuerResult, [{
        key: 'useItem',
        value: function useItem() {
            this.props.useCallback(this.props.issuer);
        }
    }, {
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                'li',
                null,
                _react2.default.createElement(
                    'div',
                    { className: 'grid' },
                    _react2.default.createElement(
                        'a',
                        { className: 'g 4/5', href: '/issuers/' + this.props.issuer.id },
                        this.props.issuer.name
                    ),
                    _react2.default.createElement(
                        'button',
                        { className: 'g 1/5 button button--fat', onClick: this.useItem.bind(this) },
                        'Use'
                    )
                )
            );
        }
    }]);

    return IssuerResult;
}(_react2.default.Component);

var CountryResults = function (_React$Component8) {
    _inherits(CountryResults, _React$Component8);

    function CountryResults() {
        _classCallCheck(this, CountryResults);

        return _possibleConstructorReturn(this, (CountryResults.__proto__ || Object.getPrototypeOf(CountryResults)).apply(this, arguments));
    }

    _createClass(CountryResults, [{
        key: 'useCallback',
        value: function useCallback(country) {
            this.props.useCallback(country);
        }
    }, {
        key: 'render',
        value: function render() {
            var items = [];
            var data = this.props.data;
            data.forEach(function (country) {
                items.push(_react2.default.createElement(CountryResult, { country: country, useCallback: this.useCallback.bind(this) }));
            }.bind(this));
            if (items.length) {
                return _react2.default.createElement(
                    'ul',
                    { className: 'list--lined' },
                    items
                );
            }
            return _react2.default.createElement(
                'p',
                { className: 'text--center' },
                'No results'
            );
        }
    }]);

    return CountryResults;
}(_react2.default.Component);

var CountryResult = function (_React$Component9) {
    _inherits(CountryResult, _React$Component9);

    function CountryResult() {
        _classCallCheck(this, CountryResult);

        return _possibleConstructorReturn(this, (CountryResult.__proto__ || Object.getPrototypeOf(CountryResult)).apply(this, arguments));
    }

    _createClass(CountryResult, [{
        key: 'useItem',
        value: function useItem() {
            this.props.useCallback(this.props.country);
        }
    }, {
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                'li',
                null,
                _react2.default.createElement(
                    'div',
                    { className: 'grid' },
                    _react2.default.createElement(
                        'p',
                        { className: 'g 4/5' },
                        this.props.country.name
                    ),
                    _react2.default.createElement(
                        'button',
                        { className: 'g 1/5 button button--fat', onClick: this.useItem.bind(this) },
                        'Use'
                    )
                )
            );
        }
    }]);

    return CountryResult;
}(_react2.default.Component);

var GroupResults = function (_React$Component10) {
    _inherits(GroupResults, _React$Component10);

    function GroupResults() {
        _classCallCheck(this, GroupResults);

        return _possibleConstructorReturn(this, (GroupResults.__proto__ || Object.getPrototypeOf(GroupResults)).apply(this, arguments));
    }

    _createClass(GroupResults, [{
        key: 'useCallback',
        value: function useCallback(group) {
            this.props.useCallback(group);
        }
    }, {
        key: 'render',
        value: function render() {
            var items = [];
            var data = this.props.data;
            data.forEach(function (group) {
                items.push(_react2.default.createElement(GroupResult, { group: group, useCallback: this.useCallback.bind(this) }));
            }.bind(this));
            if (items.length) {
                return _react2.default.createElement(
                    'ul',
                    { className: 'list--lined' },
                    items
                );
            }
            return _react2.default.createElement(
                'p',
                { className: 'text--center' },
                'No results'
            );
        }
    }]);

    return GroupResults;
}(_react2.default.Component);

var GroupResult = function (_React$Component11) {
    _inherits(GroupResult, _React$Component11);

    function GroupResult() {
        _classCallCheck(this, GroupResult);

        return _possibleConstructorReturn(this, (GroupResult.__proto__ || Object.getPrototypeOf(GroupResult)).apply(this, arguments));
    }

    _createClass(GroupResult, [{
        key: 'useItem',
        value: function useItem() {
            this.props.useCallback(this.props.group);
        }
    }, {
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                'li',
                null,
                _react2.default.createElement(
                    'div',
                    { className: 'grid' },
                    _react2.default.createElement(
                        'a',
                        { className: 'g 4/5', href: '/groups/' + this.props.group.id },
                        this.props.group.name
                    ),
                    _react2.default.createElement(
                        'button',
                        { className: 'g 1/5 button button--fat', onClick: this.useItem.bind(this) },
                        'Use'
                    )
                )
            );
        }
    }]);

    return GroupResult;
}(_react2.default.Component);

},{"./../utils/AutoComplete":40,"./Lightbox":35,"react":"react"}],35:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _reactModal = require('react-modal');

var _reactModal2 = _interopRequireDefault(_reactModal);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var Lightbox = function (_React$Component) {
    _inherits(Lightbox, _React$Component);

    function Lightbox() {
        _classCallCheck(this, Lightbox);

        var _this = _possibleConstructorReturn(this, (Lightbox.__proto__ || Object.getPrototypeOf(Lightbox)).call(this));

        _this.state = { modalIsOpen: false };
        return _this;
    }

    _createClass(Lightbox, [{
        key: 'componentWillReceiveProps',
        value: function componentWillReceiveProps(props) {
            this.setState({
                modalIsOpen: props.modalIsOpen
            });
        }
    }, {
        key: 'show',
        value: function show() {
            this.setState({ modalIsOpen: true });
        }
    }, {
        key: 'close',
        value: function close() {
            this.setState({ modalIsOpen: false });
            if (this.props.closeCallback) {
                this.props.closeCallback();
            }
        }
    }, {
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                _reactModal2.default,
                {
                    isOpen: this.state.modalIsOpen,
                    onRequestClose: this.close.bind(this),
                    className: 'lightbox__panel',
                    overlayClassName: 'lightbox__overlay' },
                _react2.default.createElement(
                    'div',
                    { className: 'lightbox__topbar' },
                    _react2.default.createElement(
                        'button',
                        { className: 'lightbox__close', onClick: this.close.bind(this) },
                        _react2.default.createElement(
                            'svg',
                            {
                                viewBox: '0 0 24 24',
                                xmlns: 'http://www.w3.org/2000/svg',
                                xmlnsXlink: 'http://www.w3.org/1999/xlink' },
                            _react2.default.createElement('use', { xlinkHref: '#icon-close' })
                        )
                    ),
                    _react2.default.createElement(
                        'p',
                        { className: 'lightbox__title' },
                        this.props.title
                    )
                ),
                _react2.default.createElement(
                    'div',
                    { className: 'lightbox__body' },
                    this.props.children
                )
            );
        }
    }]);

    return Lightbox;
}(_react2.default.Component);

exports.default = Lightbox;
;

},{"react":"react","react-modal":21}],36:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _Message = require('./Message');

var _Message2 = _interopRequireDefault(_Message);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var FileDrop = function (_React$Component) {
    _inherits(FileDrop, _React$Component);

    function FileDrop() {
        _classCallCheck(this, FileDrop);

        var _this = _possibleConstructorReturn(this, (FileDrop.__proto__ || Object.getPrototypeOf(FileDrop)).call(this));

        _this.state = {
            isDragActive: false,
            messageType: null,
            messageText: null
        };
        return _this;
    }

    _createClass(FileDrop, [{
        key: 'componentDidMount',
        value: function componentDidMount() {
            this.enterCounter = 0;
        }
    }, {
        key: 'handleClick',
        value: function handleClick() {
            this.refs.fileInputEl.value = null;
            this.refs.fileInputEl.click();
        }
    }, {
        key: 'handleDragOver',
        value: function handleDragOver(e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, {
        key: 'handleDrop',
        value: function handleDrop(e) {
            e.preventDefault();

            // Reset the counter along with the drag on a drop.
            this.enterCounter = 0;

            this.setState({
                isDragActive: false,
                isDragReject: false,
                messageType: null,
                messageText: null
            });

            var droppedFiles = e.dataTransfer ? e.dataTransfer.files : e.target.files;
            if (droppedFiles.length > 1) {
                this.setState({
                    messageType: _Message2.default.TYPE_ERROR,
                    messageText: 'Only upload one file at a time'
                });
                return;
            }

            this.props.onFileRecieved(droppedFiles[0]);
        }
    }, {
        key: 'handleDragEnter',
        value: function handleDragEnter(e) {
            e.preventDefault();

            // Count the dropzone and any children that are entered.
            ++this.enterCounter;

            this.setState({
                isDragActive: true,
                messageType: null,
                messageText: null
            });
        }
    }, {
        key: 'handleDragLeave',
        value: function handleDragLeave(e) {
            e.preventDefault();

            // Only deactivate once the dropzone and all children was left.
            if (--this.enterCounter > 0) {
                return;
            }

            this.setState({
                isDragActive: false
            });
        }
    }, {
        key: 'render',
        value: function render() {
            var elClass = 'filedrop g-unit';
            if (this.state.isDragActive) {
                elClass += ' filedrop--drag';
            }

            return _react2.default.createElement(
                'div',
                { className: 'g-unit' },
                _react2.default.createElement(
                    'div',
                    { className: elClass,
                        onClick: this.handleClick.bind(this),
                        onDragEnter: this.handleDragEnter.bind(this),
                        onDragLeave: this.handleDragLeave.bind(this),
                        onDrop: this.handleDrop.bind(this),
                        onDragOver: this.handleDragOver.bind(this)
                    },
                    _react2.default.createElement(
                        'svg',
                        {
                            className: 'filedrop__icon',
                            viewBox: '0 0 24 24',
                            xmlns: 'http://www.w3.org/2000/svg',
                            xmlnsXlink: 'http://www.w3.org/1999/xlink' },
                        _react2.default.createElement('use', { xlinkHref: '#icon-upload' })
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'filedrop__text' },
                        'Drag and drop a CSV file (or click to choose).'
                    ),
                    _react2.default.createElement('input', { ref: 'fileInputEl', style: { display: 'none' }, type: 'file', onChange: this.handleDrop.bind(this) })
                ),
                _react2.default.createElement(_Message2.default, {
                    message: this.state.messageText,
                    type: this.state.messageType
                })
            );
        }
    }]);

    return FileDrop;
}(_react2.default.Component);

exports.default = FileDrop;

},{"./Message":38,"react":"react"}],37:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var Loading = function (_React$Component) {
    _inherits(Loading, _React$Component);

    function Loading() {
        _classCallCheck(this, Loading);

        return _possibleConstructorReturn(this, (Loading.__proto__ || Object.getPrototypeOf(Loading)).apply(this, arguments));
    }

    _createClass(Loading, [{
        key: 'render',
        value: function render() {
            var css = 'loading g--align--center ' + (this.props.cssClasses || '');

            return _react2.default.createElement(
                'div',
                { className: css },
                _react2.default.createElement(
                    'svg',
                    { className: 'loading__spinner', xmlns: 'http://www.w3.org/2000/svg' },
                    _react2.default.createElement('circle', { className: 'loading__path', fill: 'none', strokeWidth: '4', cx: '14', cy: '14', r: '10' })
                )
            );
        }
    }]);

    return Loading;
}(_react2.default.Component);

exports.default = Loading;

},{"react":"react"}],38:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var Message = function (_React$Component) {
    _inherits(Message, _React$Component);

    _createClass(Message, null, [{
        key: 'TYPE_OK',
        get: function get() {
            return 'ok';
        }
    }, {
        key: 'TYPE_WARNING',
        get: function get() {
            return 'warning';
        }
    }, {
        key: 'TYPE_ERROR',
        get: function get() {
            return 'error';
        }
    }, {
        key: 'TYPE_INFO',
        get: function get() {
            return 'info';
        }
    }]);

    function Message() {
        _classCallCheck(this, Message);

        return _possibleConstructorReturn(this, (Message.__proto__ || Object.getPrototypeOf(Message)).call(this));
    }

    _createClass(Message, [{
        key: 'render',
        value: function render() {
            if (!this.props.message) {
                return _react2.default.createElement(MessageNone, null);
            }
            switch (this.props.type) {
                case Message.TYPE_OK:
                    return _react2.default.createElement(MessageOk, { message: this.props.message });
                    break;
                case Message.TYPE_WARNING:
                    return _react2.default.createElement(MessageWarning, { message: this.props.message });
                    break;
                case Message.TYPE_ERROR:
                    return _react2.default.createElement(MessageError, { message: this.props.message });
                    break;
                case Message.TYPE_INFO:
                default:
                    return _react2.default.createElement(MessageInfo, { message: this.props.message });
                    break;
            }
        }
    }]);

    return Message;
}(_react2.default.Component);

exports.default = Message;

var MessageOk = function (_React$Component2) {
    _inherits(MessageOk, _React$Component2);

    function MessageOk() {
        _classCallCheck(this, MessageOk);

        return _possibleConstructorReturn(this, (MessageOk.__proto__ || Object.getPrototypeOf(MessageOk)).apply(this, arguments));
    }

    _createClass(MessageOk, [{
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                'div',
                { className: 'message message--ok' },
                _react2.default.createElement(
                    'span',
                    { className: 'icon-text' },
                    _react2.default.createElement(
                        'span',
                        { className: 'icon-text__icon' },
                        _react2.default.createElement(
                            'svg',
                            {
                                viewBox: '0 0 24 24',
                                xmlns: 'http://www.w3.org/2000/svg',
                                xmlnsXlink: 'http://www.w3.org/1999/xlink' },
                            _react2.default.createElement('use', { xlinkHref: '#icon-ok' })
                        )
                    ),
                    _react2.default.createElement(
                        'span',
                        { className: 'icon-text__text' },
                        this.props.message
                    )
                )
            );
        }
    }]);

    return MessageOk;
}(_react2.default.Component);

var MessageWarning = function (_React$Component3) {
    _inherits(MessageWarning, _React$Component3);

    function MessageWarning() {
        _classCallCheck(this, MessageWarning);

        return _possibleConstructorReturn(this, (MessageWarning.__proto__ || Object.getPrototypeOf(MessageWarning)).apply(this, arguments));
    }

    _createClass(MessageWarning, [{
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                'div',
                { className: 'message message--warning' },
                _react2.default.createElement(
                    'span',
                    { className: 'icon-text' },
                    _react2.default.createElement(
                        'span',
                        { className: 'icon-text__icon' },
                        '!'
                    ),
                    _react2.default.createElement(
                        'span',
                        { className: 'icon-text__text' },
                        this.props.message
                    )
                )
            );
        }
    }]);

    return MessageWarning;
}(_react2.default.Component);

var MessageError = function (_React$Component4) {
    _inherits(MessageError, _React$Component4);

    function MessageError() {
        _classCallCheck(this, MessageError);

        return _possibleConstructorReturn(this, (MessageError.__proto__ || Object.getPrototypeOf(MessageError)).apply(this, arguments));
    }

    _createClass(MessageError, [{
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                'div',
                { className: 'message message--error' },
                _react2.default.createElement(
                    'span',
                    { className: 'icon-text' },
                    _react2.default.createElement(
                        'span',
                        { className: 'icon-text__icon' },
                        _react2.default.createElement(
                            'svg',
                            {
                                viewBox: '0 0 24 24',
                                xmlns: 'http://www.w3.org/2000/svg',
                                xmlnsXlink: 'http://www.w3.org/1999/xlink' },
                            _react2.default.createElement('use', { xlinkHref: '#icon-close' })
                        )
                    ),
                    _react2.default.createElement(
                        'span',
                        { className: 'icon-text__text' },
                        this.props.message
                    )
                )
            );
        }
    }]);

    return MessageError;
}(_react2.default.Component);

var MessageInfo = function (_React$Component5) {
    _inherits(MessageInfo, _React$Component5);

    function MessageInfo() {
        _classCallCheck(this, MessageInfo);

        return _possibleConstructorReturn(this, (MessageInfo.__proto__ || Object.getPrototypeOf(MessageInfo)).apply(this, arguments));
    }

    _createClass(MessageInfo, [{
        key: 'render',
        value: function render() {
            return _react2.default.createElement(
                'div',
                { className: 'message message--info' },
                _react2.default.createElement(
                    'span',
                    { className: 'icon-text' },
                    _react2.default.createElement(
                        'span',
                        { className: 'icon-text__icon' },
                        '(i)'
                    ),
                    _react2.default.createElement(
                        'span',
                        { className: 'icon-text__text' },
                        this.props.message
                    )
                )
            );
        }
    }]);

    return MessageInfo;
}(_react2.default.Component);

var MessageNone = function (_React$Component6) {
    _inherits(MessageNone, _React$Component6);

    function MessageNone() {
        _classCallCheck(this, MessageNone);

        return _possibleConstructorReturn(this, (MessageNone.__proto__ || Object.getPrototypeOf(MessageNone)).apply(this, arguments));
    }

    _createClass(MessageNone, [{
        key: 'render',
        value: function render() {
            return null;
        }
    }]);

    return MessageNone;
}(_react2.default.Component);

},{"react":"react"}],39:[function(require,module,exports){
'use strict';

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _reactDom = require('react-dom');

var _reactDom2 = _interopRequireDefault(_reactDom);

var _DataEditor = require('./DataEditor/DataEditor');

var _DataEditor2 = _interopRequireDefault(_DataEditor);

var _Issuer = require('./Issuer');

var _Issuer2 = _interopRequireDefault(_Issuer);

var _Compare = require('./Compare');

var _Compare2 = _interopRequireDefault(_Compare);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

(function () {
    "use strict";

    function init() {
        var data = document.getElementById('data-editor'),
            issuer = document.getElementById('issuer-editor'),
            compare = document.getElementById('compare-editor');

        if (data) {
            var productOptions = window.ISIN.productOptions || null;
            var bulkStats = window.ISIN.bulkStats || null;
            _reactDom2.default.render(_react2.default.createElement(_DataEditor2.default, { productOptions: productOptions, bulkStats: bulkStats }), data);
        } else if (issuer) {
            _reactDom2.default.render(_react2.default.createElement(_Issuer2.default, null), issuer);
        } else if (compare) {
            _reactDom2.default.render(_react2.default.createElement(_Compare2.default, null), compare);
        }

        // disable some events globally
        ['dragover', 'drop'].forEach(function (name) {
            window.addEventListener(name, function (e) {
                e = e || event;
                e.preventDefault();
            }, false);
        });
    }

    // Cut the mustard
    if (document.getElementsByClassName && document.addEventListener && window.history) {
        init();
    }
})();

},{"./Compare":22,"./DataEditor/DataEditor":24,"./Issuer":34,"react":"react","react-dom":"react-dom"}],40:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var AutoComplete = function () {
    function AutoComplete(path, callback) {
        _classCallCheck(this, AutoComplete);

        this.path = path;
        this.callback = callback;
        this.timer = null;
        this.timeoutValue = 600;
    }

    _createClass(AutoComplete, [{
        key: 'newValue',
        value: function newValue(value) {
            clearTimeout(this.timer);
            this.timer = setTimeout(function () {
                var url = this.path.replace('%s', value),
                    request = new XMLHttpRequest(),
                    callback = this.callback;
                request.open('GET', url, true);

                request.onload = function () {
                    if (this.status >= 200 && this.status < 400) {
                        var data = JSON.parse(this.response);
                        return callback(data);
                    } else {
                        return callback(false);
                    }
                };

                request.onerror = function () {
                    return callback(false);
                };

                request.send();
            }.bind(this), this.timeoutValue);
        }
    }]);

    return AutoComplete;
}();

exports.default = AutoComplete;

},{}],"react-dom":[function(require,module,exports){
"use strict";

module.exports = window.ReactDOM;

},{}],"react":[function(require,module,exports){
"use strict";

module.exports = window.React;

},{}]},{},[39]);
