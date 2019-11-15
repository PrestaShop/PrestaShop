window["currency_form"] =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 343);
/******/ })
/************************************************************************/
/******/ ({

/***/ 12:
/***/ (function(module, exports) {

(function() { module.exports = window["jQuery"]; }());

/***/ }),

/***/ 16:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * 2007-2019 PrestaShop SA and Contributors
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * NOTICE OF LICENSE
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * This source file is subject to the Open Software License (OSL 3.0)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * that is bundled with this package in the file LICENSE.txt.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * It is also available through the world-wide-web at this URL:
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * https://opensource.org/licenses/OSL-3.0
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * If you did not receive a copy of the license and are unable to
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * obtain it through the world-wide-web, please send an email
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * to license@prestashop.com so we can send you a copy immediately.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * DISCLAIMER
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * versions in the future. If you wish to customize PrestaShop for your
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * needs please refer to https://www.prestashop.com for more information.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @author    PrestaShop SA <contact@prestashop.com>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @copyright 2007-2019 PrestaShop SA and Contributors
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * International Registered Trademark & Property of PrestaShop SA
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      */

var _eventEmitter = __webpack_require__(17);

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * This class is used to automatically toggle translated inputs (displayed with one
 * input and a language selector using the TranslatableType Symfony form type).
 * Also compatible with TranslatableField changes.
 */

var TranslatableInput = function () {
  function TranslatableInput(options) {
    _classCallCheck(this, TranslatableInput);

    options = options || {};

    this.localeItemSelector = options.localeItemSelector || '.js-locale-item';
    this.localeButtonSelector = options.localeButtonSelector || '.js-locale-btn';
    this.localeInputSelector = options.localeInputSelector || '.js-locale-input';

    $('body').on('click', this.localeItemSelector, this.toggleLanguage.bind(this));
    _eventEmitter.EventEmitter.on('languageSelected', this.toggleInputs.bind(this));
  }

  /**
   * Dispatch event on language selection to update inputs and other components which depend on the locale.
   *
   * @param event
   */


  _createClass(TranslatableInput, [{
    key: 'toggleLanguage',
    value: function toggleLanguage(event) {
      var localeItem = $(event.target);
      var form = localeItem.closest('form');
      _eventEmitter.EventEmitter.emit('languageSelected', { selectedLocale: localeItem.data('locale'), form: form });
    }

    /**
     * Toggle all translatable inputs in form in which locale was changed
     *
     * @param {Event} event
     */

  }, {
    key: 'toggleInputs',
    value: function toggleInputs(event) {
      var form = event.form;
      var selectedLocale = event.selectedLocale;
      var localeButton = form.find(this.localeButtonSelector);
      var changeLanguageUrl = localeButton.data('change-language-url');

      localeButton.text(selectedLocale);
      form.find(this.localeInputSelector).addClass('d-none');
      form.find(this.localeInputSelector + '.js-locale-' + selectedLocale).removeClass('d-none');

      if (changeLanguageUrl) {
        this._saveSelectedLanguage(changeLanguageUrl, selectedLocale);
      }
    }

    /**
     * Save language choice for employee forms.
     *
     * @param {String} changeLanguageUrl
     * @param {String} selectedLocale
     *
     * @private
     */

  }, {
    key: '_saveSelectedLanguage',
    value: function _saveSelectedLanguage(changeLanguageUrl, selectedLocale) {
      $.post({
        url: changeLanguageUrl,
        data: {
          language_iso_code: selectedLocale
        }
      });
    }
  }]);

  return TranslatableInput;
}();

exports.default = TranslatableInput;

/***/ }),

/***/ 17:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.EventEmitter = undefined;

var _events = __webpack_require__(19);

var _events2 = _interopRequireDefault(_events);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * We instanciate one EventEmitter (restricted via a const) so that every components
 * register/dispatch on the same one and can communicate with each other.
 */
var EventEmitter = exports.EventEmitter = new _events2.default(); /**
                                                                   * 2007-2019 PrestaShop SA and Contributors
                                                                   *
                                                                   * NOTICE OF LICENSE
                                                                   *
                                                                   * This source file is subject to the Open Software License (OSL 3.0)
                                                                   * that is bundled with this package in the file LICENSE.txt.
                                                                   * It is also available through the world-wide-web at this URL:
                                                                   * https://opensource.org/licenses/OSL-3.0
                                                                   * If you did not receive a copy of the license and are unable to
                                                                   * obtain it through the world-wide-web, please send an email
                                                                   * to license@prestashop.com so we can send you a copy immediately.
                                                                   *
                                                                   * DISCLAIMER
                                                                   *
                                                                   * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
                                                                   * versions in the future. If you wish to customize PrestaShop for your
                                                                   * needs please refer to https://www.prestashop.com for more information.
                                                                   *
                                                                   * @author    PrestaShop SA <contact@prestashop.com>
                                                                   * @copyright 2007-2019 PrestaShop SA and Contributors
                                                                   * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
                                                                   * International Registered Trademark & Property of PrestaShop SA
                                                                   */

/***/ }),

/***/ 19:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.



var R = typeof Reflect === 'object' ? Reflect : null
var ReflectApply = R && typeof R.apply === 'function'
  ? R.apply
  : function ReflectApply(target, receiver, args) {
    return Function.prototype.apply.call(target, receiver, args);
  }

var ReflectOwnKeys
if (R && typeof R.ownKeys === 'function') {
  ReflectOwnKeys = R.ownKeys
} else if (Object.getOwnPropertySymbols) {
  ReflectOwnKeys = function ReflectOwnKeys(target) {
    return Object.getOwnPropertyNames(target)
      .concat(Object.getOwnPropertySymbols(target));
  };
} else {
  ReflectOwnKeys = function ReflectOwnKeys(target) {
    return Object.getOwnPropertyNames(target);
  };
}

function ProcessEmitWarning(warning) {
  if (console && console.warn) console.warn(warning);
}

var NumberIsNaN = Number.isNaN || function NumberIsNaN(value) {
  return value !== value;
}

function EventEmitter() {
  EventEmitter.init.call(this);
}
module.exports = EventEmitter;

// Backwards-compat with node 0.10.x
EventEmitter.EventEmitter = EventEmitter;

EventEmitter.prototype._events = undefined;
EventEmitter.prototype._eventsCount = 0;
EventEmitter.prototype._maxListeners = undefined;

// By default EventEmitters will print a warning if more than 10 listeners are
// added to it. This is a useful default which helps finding memory leaks.
var defaultMaxListeners = 10;

Object.defineProperty(EventEmitter, 'defaultMaxListeners', {
  enumerable: true,
  get: function() {
    return defaultMaxListeners;
  },
  set: function(arg) {
    if (typeof arg !== 'number' || arg < 0 || NumberIsNaN(arg)) {
      throw new RangeError('The value of "defaultMaxListeners" is out of range. It must be a non-negative number. Received ' + arg + '.');
    }
    defaultMaxListeners = arg;
  }
});

EventEmitter.init = function() {

  if (this._events === undefined ||
      this._events === Object.getPrototypeOf(this)._events) {
    this._events = Object.create(null);
    this._eventsCount = 0;
  }

  this._maxListeners = this._maxListeners || undefined;
};

// Obviously not all Emitters should be limited to 10. This function allows
// that to be increased. Set to zero for unlimited.
EventEmitter.prototype.setMaxListeners = function setMaxListeners(n) {
  if (typeof n !== 'number' || n < 0 || NumberIsNaN(n)) {
    throw new RangeError('The value of "n" is out of range. It must be a non-negative number. Received ' + n + '.');
  }
  this._maxListeners = n;
  return this;
};

function $getMaxListeners(that) {
  if (that._maxListeners === undefined)
    return EventEmitter.defaultMaxListeners;
  return that._maxListeners;
}

EventEmitter.prototype.getMaxListeners = function getMaxListeners() {
  return $getMaxListeners(this);
};

EventEmitter.prototype.emit = function emit(type) {
  var args = [];
  for (var i = 1; i < arguments.length; i++) args.push(arguments[i]);
  var doError = (type === 'error');

  var events = this._events;
  if (events !== undefined)
    doError = (doError && events.error === undefined);
  else if (!doError)
    return false;

  // If there is no 'error' event listener then throw.
  if (doError) {
    var er;
    if (args.length > 0)
      er = args[0];
    if (er instanceof Error) {
      // Note: The comments on the `throw` lines are intentional, they show
      // up in Node's output if this results in an unhandled exception.
      throw er; // Unhandled 'error' event
    }
    // At least give some kind of context to the user
    var err = new Error('Unhandled error.' + (er ? ' (' + er.message + ')' : ''));
    err.context = er;
    throw err; // Unhandled 'error' event
  }

  var handler = events[type];

  if (handler === undefined)
    return false;

  if (typeof handler === 'function') {
    ReflectApply(handler, this, args);
  } else {
    var len = handler.length;
    var listeners = arrayClone(handler, len);
    for (var i = 0; i < len; ++i)
      ReflectApply(listeners[i], this, args);
  }

  return true;
};

function _addListener(target, type, listener, prepend) {
  var m;
  var events;
  var existing;

  if (typeof listener !== 'function') {
    throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
  }

  events = target._events;
  if (events === undefined) {
    events = target._events = Object.create(null);
    target._eventsCount = 0;
  } else {
    // To avoid recursion in the case that type === "newListener"! Before
    // adding it to the listeners, first emit "newListener".
    if (events.newListener !== undefined) {
      target.emit('newListener', type,
                  listener.listener ? listener.listener : listener);

      // Re-assign `events` because a newListener handler could have caused the
      // this._events to be assigned to a new object
      events = target._events;
    }
    existing = events[type];
  }

  if (existing === undefined) {
    // Optimize the case of one listener. Don't need the extra array object.
    existing = events[type] = listener;
    ++target._eventsCount;
  } else {
    if (typeof existing === 'function') {
      // Adding the second element, need to change to array.
      existing = events[type] =
        prepend ? [listener, existing] : [existing, listener];
      // If we've already got an array, just append.
    } else if (prepend) {
      existing.unshift(listener);
    } else {
      existing.push(listener);
    }

    // Check for listener leak
    m = $getMaxListeners(target);
    if (m > 0 && existing.length > m && !existing.warned) {
      existing.warned = true;
      // No error code for this since it is a Warning
      // eslint-disable-next-line no-restricted-syntax
      var w = new Error('Possible EventEmitter memory leak detected. ' +
                          existing.length + ' ' + String(type) + ' listeners ' +
                          'added. Use emitter.setMaxListeners() to ' +
                          'increase limit');
      w.name = 'MaxListenersExceededWarning';
      w.emitter = target;
      w.type = type;
      w.count = existing.length;
      ProcessEmitWarning(w);
    }
  }

  return target;
}

EventEmitter.prototype.addListener = function addListener(type, listener) {
  return _addListener(this, type, listener, false);
};

EventEmitter.prototype.on = EventEmitter.prototype.addListener;

EventEmitter.prototype.prependListener =
    function prependListener(type, listener) {
      return _addListener(this, type, listener, true);
    };

function onceWrapper() {
  var args = [];
  for (var i = 0; i < arguments.length; i++) args.push(arguments[i]);
  if (!this.fired) {
    this.target.removeListener(this.type, this.wrapFn);
    this.fired = true;
    ReflectApply(this.listener, this.target, args);
  }
}

function _onceWrap(target, type, listener) {
  var state = { fired: false, wrapFn: undefined, target: target, type: type, listener: listener };
  var wrapped = onceWrapper.bind(state);
  wrapped.listener = listener;
  state.wrapFn = wrapped;
  return wrapped;
}

EventEmitter.prototype.once = function once(type, listener) {
  if (typeof listener !== 'function') {
    throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
  }
  this.on(type, _onceWrap(this, type, listener));
  return this;
};

EventEmitter.prototype.prependOnceListener =
    function prependOnceListener(type, listener) {
      if (typeof listener !== 'function') {
        throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
      }
      this.prependListener(type, _onceWrap(this, type, listener));
      return this;
    };

// Emits a 'removeListener' event if and only if the listener was removed.
EventEmitter.prototype.removeListener =
    function removeListener(type, listener) {
      var list, events, position, i, originalListener;

      if (typeof listener !== 'function') {
        throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
      }

      events = this._events;
      if (events === undefined)
        return this;

      list = events[type];
      if (list === undefined)
        return this;

      if (list === listener || list.listener === listener) {
        if (--this._eventsCount === 0)
          this._events = Object.create(null);
        else {
          delete events[type];
          if (events.removeListener)
            this.emit('removeListener', type, list.listener || listener);
        }
      } else if (typeof list !== 'function') {
        position = -1;

        for (i = list.length - 1; i >= 0; i--) {
          if (list[i] === listener || list[i].listener === listener) {
            originalListener = list[i].listener;
            position = i;
            break;
          }
        }

        if (position < 0)
          return this;

        if (position === 0)
          list.shift();
        else {
          spliceOne(list, position);
        }

        if (list.length === 1)
          events[type] = list[0];

        if (events.removeListener !== undefined)
          this.emit('removeListener', type, originalListener || listener);
      }

      return this;
    };

EventEmitter.prototype.off = EventEmitter.prototype.removeListener;

EventEmitter.prototype.removeAllListeners =
    function removeAllListeners(type) {
      var listeners, events, i;

      events = this._events;
      if (events === undefined)
        return this;

      // not listening for removeListener, no need to emit
      if (events.removeListener === undefined) {
        if (arguments.length === 0) {
          this._events = Object.create(null);
          this._eventsCount = 0;
        } else if (events[type] !== undefined) {
          if (--this._eventsCount === 0)
            this._events = Object.create(null);
          else
            delete events[type];
        }
        return this;
      }

      // emit removeListener for all listeners on all events
      if (arguments.length === 0) {
        var keys = Object.keys(events);
        var key;
        for (i = 0; i < keys.length; ++i) {
          key = keys[i];
          if (key === 'removeListener') continue;
          this.removeAllListeners(key);
        }
        this.removeAllListeners('removeListener');
        this._events = Object.create(null);
        this._eventsCount = 0;
        return this;
      }

      listeners = events[type];

      if (typeof listeners === 'function') {
        this.removeListener(type, listeners);
      } else if (listeners !== undefined) {
        // LIFO order
        for (i = listeners.length - 1; i >= 0; i--) {
          this.removeListener(type, listeners[i]);
        }
      }

      return this;
    };

function _listeners(target, type, unwrap) {
  var events = target._events;

  if (events === undefined)
    return [];

  var evlistener = events[type];
  if (evlistener === undefined)
    return [];

  if (typeof evlistener === 'function')
    return unwrap ? [evlistener.listener || evlistener] : [evlistener];

  return unwrap ?
    unwrapListeners(evlistener) : arrayClone(evlistener, evlistener.length);
}

EventEmitter.prototype.listeners = function listeners(type) {
  return _listeners(this, type, true);
};

EventEmitter.prototype.rawListeners = function rawListeners(type) {
  return _listeners(this, type, false);
};

EventEmitter.listenerCount = function(emitter, type) {
  if (typeof emitter.listenerCount === 'function') {
    return emitter.listenerCount(type);
  } else {
    return listenerCount.call(emitter, type);
  }
};

EventEmitter.prototype.listenerCount = listenerCount;
function listenerCount(type) {
  var events = this._events;

  if (events !== undefined) {
    var evlistener = events[type];

    if (typeof evlistener === 'function') {
      return 1;
    } else if (evlistener !== undefined) {
      return evlistener.length;
    }
  }

  return 0;
}

EventEmitter.prototype.eventNames = function eventNames() {
  return this._eventsCount > 0 ? ReflectOwnKeys(this._events) : [];
};

function arrayClone(arr, n) {
  var copy = new Array(n);
  for (var i = 0; i < n; ++i)
    copy[i] = arr[i];
  return copy;
}

function spliceOne(list, index) {
  for (; index + 1 < list.length; index++)
    list[index] = list[index + 1];
  list.pop();
}

function unwrapListeners(arr) {
  var ret = new Array(arr.length);
  for (var i = 0; i < ret.length; ++i) {
    ret[i] = arr[i].listener || arr[i];
  }
  return ret;
}


/***/ }),

/***/ 20:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var $ = window.$;

/**
 * Handles UI interactions of choice tree
 */

var ChoiceTree = function () {
  /**
   * @param {String} treeSelector
   */
  function ChoiceTree(treeSelector) {
    var _this = this;

    _classCallCheck(this, ChoiceTree);

    this.$container = $(treeSelector);

    this.$container.on('click', '.js-input-wrapper', function (event) {
      var $inputWrapper = $(event.currentTarget);

      _this._toggleChildTree($inputWrapper);
    });

    this.$container.on('click', '.js-toggle-choice-tree-action', function (event) {
      var $action = $(event.currentTarget);

      _this._toggleTree($action);
    });

    return {
      enableAutoCheckChildren: function enableAutoCheckChildren() {
        return _this.enableAutoCheckChildren();
      },
      enableAllInputs: function enableAllInputs() {
        return _this.enableAllInputs();
      },
      disableAllInputs: function disableAllInputs() {
        return _this.disableAllInputs();
      }
    };
  }

  /**
   * Enable automatic check/uncheck of clicked item's children.
   */


  _createClass(ChoiceTree, [{
    key: 'enableAutoCheckChildren',
    value: function enableAutoCheckChildren() {
      this.$container.on('change', 'input[type="checkbox"]', function (event) {
        var $clickedCheckbox = $(event.currentTarget);
        var $itemWithChildren = $clickedCheckbox.closest('li');

        $itemWithChildren.find('ul input[type="checkbox"]').prop('checked', $clickedCheckbox.is(':checked'));
      });
    }

    /**
     * Enable all inputs in the choice tree.
     */

  }, {
    key: 'enableAllInputs',
    value: function enableAllInputs() {
      this.$container.find('input').removeAttr('disabled');
    }

    /**
     * Disable all inputs in the choice tree.
     */

  }, {
    key: 'disableAllInputs',
    value: function disableAllInputs() {
      this.$container.find('input').attr('disabled', 'disabled');
    }

    /**
     * Collapse or expand sub-tree for single parent
     *
     * @param {jQuery} $inputWrapper
     *
     * @private
     */

  }, {
    key: '_toggleChildTree',
    value: function _toggleChildTree($inputWrapper) {
      var $parentWrapper = $inputWrapper.closest('li');

      if ($parentWrapper.hasClass('expanded')) {
        $parentWrapper.removeClass('expanded').addClass('collapsed');

        return;
      }

      if ($parentWrapper.hasClass('collapsed')) {
        $parentWrapper.removeClass('collapsed').addClass('expanded');
      }
    }

    /**
     * Collapse or expand whole tree
     *
     * @param {jQuery} $action
     *
     * @private
     */

  }, {
    key: '_toggleTree',
    value: function _toggleTree($action) {
      var $parentContainer = $action.closest('.js-choice-tree-container');
      var action = $action.data('action');

      // toggle action configuration
      var config = {
        addClass: {
          expand: 'expanded',
          collapse: 'collapsed'
        },
        removeClass: {
          expand: 'collapsed',
          collapse: 'expanded'
        },
        nextAction: {
          expand: 'collapse',
          collapse: 'expand'
        },
        text: {
          expand: 'collapsed-text',
          collapse: 'expanded-text'
        },
        icon: {
          expand: 'collapsed-icon',
          collapse: 'expanded-icon'
        }
      };

      $parentContainer.find('li').each(function (index, item) {
        var $item = $(item);

        if ($item.hasClass(config.removeClass[action])) {
          $item.removeClass(config.removeClass[action]).addClass(config.addClass[action]);
        }
      });

      $action.data('action', config.nextAction[action]);
      $action.find('.material-icons').text($action.data(config.icon[action]));
      $action.find('.js-toggle-text').text($action.data(config.text[action]));
    }
  }]);

  return ChoiceTree;
}();

exports.default = ChoiceTree;

/***/ }),

/***/ 265:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Defines all selectors that are used in currency add/edit form.
 */
exports.default = {
  currencyForm: '#currency_form',
  currencySelector: '#currency_selected_iso_code',
  isUnofficialCheckbox: '#currency_unofficial',
  namesInput: function namesInput(langId) {
    return '#currency_names_' + langId;
  },
  symbolsInput: function symbolsInput(langId) {
    return '#currency_symbols_' + langId;
  },
  isoCodeInput: '#currency_iso_code',
  exchangeRateInput: '#currency_exchange_rate',
  resetDefaultSettingsInput: '#currency_reset_default_settings',
  loadingDataModal: '#currency_loading_data_modal',
  precisionInput: '#currency_precision',
  shopAssociationTree: '#currency_shop_association'
};

/***/ }),

/***/ 266:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * 2007-2019 PrestaShop SA and Contributors
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * NOTICE OF LICENSE
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * This source file is subject to the Open Software License (OSL 3.0)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * that is bundled with this package in the file LICENSE.txt.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * It is also available through the world-wide-web at this URL:
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * https://opensource.org/licenses/OSL-3.0
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * If you did not receive a copy of the license and are unable to
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * obtain it through the world-wide-web, please send an email
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * to license@prestashop.com so we can send you a copy immediately.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * DISCLAIMER
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * versions in the future. If you wish to customize PrestaShop for your
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * needs please refer to https://www.prestashop.com for more information.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @author    PrestaShop SA <contact@prestashop.com>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @copyright 2007-2019 PrestaShop SA and Contributors
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * International Registered Trademark & Property of PrestaShop SA
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      */

var _growl = __webpack_require__(53);

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var CurrencyForm = function () {
  /**
   * @param {object} currencyFormMap - Page map
   */
  function CurrencyForm(currencyFormMap) {
    _classCallCheck(this, CurrencyForm);

    this.map = currencyFormMap;
    this.$currencyForm = $(this.map.currencyForm);
    this.getCLDRDataUrl = this.$currencyForm.data('get-cldr-data');
    this.$currencySelector = $(this.map.currencySelector);
    this.$isUnofficialCheckbox = $(this.map.isUnofficialCheckbox);
    this.$isoCodeInput = $(this.map.isoCodeInput);
    this.$exchangeRateInput = $(this.map.exchangeRateInput);
    this.$precisionInput = $(this.map.precisionInput);
    this.$resetDefaultSettingsButton = $(this.map.resetDefaultSettingsInput);
    this.$loadingDataModal = $(this.map.loadingDataModal);
  }

  _createClass(CurrencyForm, [{
    key: 'init',
    value: function init() {
      this._initListeners();
      this._initFields();
    }
  }, {
    key: '_initListeners',
    value: function _initListeners() {
      this.$currencySelector.change(this._onCurrencySelectorChange.bind(this));
      this.$isUnofficialCheckbox.change(this._onIsUnofficialCheckboxChange.bind(this));
      this.$resetDefaultSettingsButton.click(this._onResetDefaultSettingsClick.bind(this));
    }
  }, {
    key: '_initFields',
    value: function _initFields() {
      if (!this._isUnofficialCurrency()) {
        this.$isUnofficialCheckbox.prop('checked', false);
        this.$isoCodeInput.prop('readonly', true);
      } else {
        this.$currencySelector.val('');
        this.$isoCodeInput.prop('readonly', false);
      }
    }
  }, {
    key: '_onCurrencySelectorChange',
    value: function _onCurrencySelectorChange() {
      var selectedISOCode = this.$currencySelector.val();
      if ('' !== selectedISOCode) {
        this.$isUnofficialCheckbox.prop('checked', false);
        this.$isoCodeInput.prop('readonly', true);
        this._resetCurrencyData(selectedISOCode);
      } else {
        this.$isUnofficialCheckbox.prop('checked', true);
        this.$isoCodeInput.prop('readonly', false);
      }
    }
  }, {
    key: '_isUnofficialCurrency',
    value: function _isUnofficialCurrency() {
      if ('hidden' === this.$isUnofficialCheckbox.prop('type')) {
        return '1' === this.$isUnofficialCheckbox.attr('value');
      }

      return this.$isUnofficialCheckbox.prop('checked');
    }
  }, {
    key: '_onIsUnofficialCheckboxChange',
    value: function _onIsUnofficialCheckboxChange() {
      if (this._isUnofficialCurrency()) {
        this.$currencySelector.val('');
        this.$isoCodeInput.prop('readonly', false);
      } else {
        this.$isoCodeInput.prop('readonly', true);
      }
    }
  }, {
    key: '_onResetDefaultSettingsClick',
    value: function _onResetDefaultSettingsClick() {
      this._resetCurrencyData(this.$isoCodeInput.val());
    }
  }, {
    key: '_resetCurrencyData',
    value: function _resetCurrencyData(selectedISOCode) {
      var _this = this;

      this.$loadingDataModal.modal('show');
      this.$resetDefaultSettingsButton.addClass('spinner');
      var getCurrencyData = this.getCLDRDataUrl.replace('CURRENCY_ISO_CODE', selectedISOCode);
      $.get(getCurrencyData).then(function (currencyData) {
        for (var langId in currencyData.names) {
          var langNameSelector = _this.map.namesInput(langId);
          $(langNameSelector).val(currencyData.names[langId]);
        }
        for (var _langId in currencyData.symbols) {
          var langSymbolSelector = _this.map.symbolsInput(_langId);
          $(langSymbolSelector).val(currencyData.symbols[_langId]);
        }
        _this.$isoCodeInput.val(currencyData.isoCode);
        if (currencyData.exchangeRate) {
          _this.$exchangeRateInput.val(currencyData.exchangeRate);
        }
        _this.$precisionInput.val(currencyData.precision);
      }).fail(function (currencyData) {
        var errorMessage = 'Can not find CLDR data for currency ' + selectedISOCode;
        if (currencyData && currencyData.responseJSON && currencyData.responseJSON.error) {
          errorMessage = currencyData.responseJSON.error;
        }
        (0, _growl.showGrowl)('error', errorMessage, 3000);
      }).always(function () {
        _this.$loadingDataModal.modal('hide');
        _this.$resetDefaultSettingsButton.removeClass('spinner');
      });
    }
  }]);

  return CurrencyForm;
}();

exports.default = CurrencyForm;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(12)))

/***/ }),

/***/ 343:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _choiceTree = __webpack_require__(20);

var _choiceTree2 = _interopRequireDefault(_choiceTree);

var _translatableInput = __webpack_require__(16);

var _translatableInput2 = _interopRequireDefault(_translatableInput);

var _currencyFormMap = __webpack_require__(265);

var _currencyFormMap2 = _interopRequireDefault(_currencyFormMap);

var _currencyForm = __webpack_require__(266);

var _currencyForm2 = _interopRequireDefault(_currencyForm);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var $ = window.$;

$(function () {
  new _translatableInput2.default();
  var choiceTree = new _choiceTree2.default(_currencyFormMap2.default.shopAssociationTree);
  choiceTree.enableAutoCheckChildren();
  var currencyForm = new _currencyForm2.default(_currencyFormMap2.default);
  currencyForm.init();
});

/***/ }),

/***/ 53:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.showGrowl = showGrowl;
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
function showGrowl(type, message, duration) {
  duration = undefined !== duration ? duration : 2000;
  window.$.growl[type]({
    title: '',
    size: "large",
    message: message,
    duration: duration
  });
};

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMWU2NjI2MzkwMGU5NjZkZmJiZjA/ODU5MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vL2V4dGVybmFsIFwialF1ZXJ5XCI/MGNiOCoqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvdHJhbnNsYXRhYmxlLWlucHV0LmpzPzE1OTQqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2V2ZW50LWVtaXR0ZXIuanM/MGUwMyoqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2V2ZW50cy9ldmVudHMuanM/N2M3MSoqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2Zvcm0vY2hvaWNlLXRyZWUuanM/NTQxYSoqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvY3VycmVuY3kvZm9ybS9jdXJyZW5jeS1mb3JtLW1hcC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9jdXJyZW5jeS9mb3JtL2N1cnJlbmN5LWZvcm0uanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvY3VycmVuY3kvZm9ybS9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvdXRpbHMvZ3Jvd2wuanM/NDA1NCoiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIlRyYW5zbGF0YWJsZUlucHV0Iiwib3B0aW9ucyIsImxvY2FsZUl0ZW1TZWxlY3RvciIsImxvY2FsZUJ1dHRvblNlbGVjdG9yIiwibG9jYWxlSW5wdXRTZWxlY3RvciIsIm9uIiwidG9nZ2xlTGFuZ3VhZ2UiLCJiaW5kIiwiRXZlbnRFbWl0dGVyIiwidG9nZ2xlSW5wdXRzIiwiZXZlbnQiLCJsb2NhbGVJdGVtIiwidGFyZ2V0IiwiZm9ybSIsImNsb3Nlc3QiLCJlbWl0Iiwic2VsZWN0ZWRMb2NhbGUiLCJkYXRhIiwibG9jYWxlQnV0dG9uIiwiZmluZCIsImNoYW5nZUxhbmd1YWdlVXJsIiwidGV4dCIsImFkZENsYXNzIiwicmVtb3ZlQ2xhc3MiLCJfc2F2ZVNlbGVjdGVkTGFuZ3VhZ2UiLCJwb3N0IiwidXJsIiwibGFuZ3VhZ2VfaXNvX2NvZGUiLCJFdmVudEVtaXR0ZXJDbGFzcyIsIkNob2ljZVRyZWUiLCJ0cmVlU2VsZWN0b3IiLCIkY29udGFpbmVyIiwiJGlucHV0V3JhcHBlciIsImN1cnJlbnRUYXJnZXQiLCJfdG9nZ2xlQ2hpbGRUcmVlIiwiJGFjdGlvbiIsIl90b2dnbGVUcmVlIiwiZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4iLCJlbmFibGVBbGxJbnB1dHMiLCJkaXNhYmxlQWxsSW5wdXRzIiwiJGNsaWNrZWRDaGVja2JveCIsIiRpdGVtV2l0aENoaWxkcmVuIiwicHJvcCIsImlzIiwicmVtb3ZlQXR0ciIsImF0dHIiLCIkcGFyZW50V3JhcHBlciIsImhhc0NsYXNzIiwiJHBhcmVudENvbnRhaW5lciIsImFjdGlvbiIsImNvbmZpZyIsImV4cGFuZCIsImNvbGxhcHNlIiwibmV4dEFjdGlvbiIsImljb24iLCJlYWNoIiwiaW5kZXgiLCJpdGVtIiwiJGl0ZW0iLCJjdXJyZW5jeUZvcm0iLCJjdXJyZW5jeVNlbGVjdG9yIiwiaXNVbm9mZmljaWFsQ2hlY2tib3giLCJuYW1lc0lucHV0IiwibGFuZ0lkIiwic3ltYm9sc0lucHV0IiwiaXNvQ29kZUlucHV0IiwiZXhjaGFuZ2VSYXRlSW5wdXQiLCJyZXNldERlZmF1bHRTZXR0aW5nc0lucHV0IiwibG9hZGluZ0RhdGFNb2RhbCIsInByZWNpc2lvbklucHV0Iiwic2hvcEFzc29jaWF0aW9uVHJlZSIsIkN1cnJlbmN5Rm9ybSIsImN1cnJlbmN5Rm9ybU1hcCIsIm1hcCIsIiRjdXJyZW5jeUZvcm0iLCJnZXRDTERSRGF0YVVybCIsIiRjdXJyZW5jeVNlbGVjdG9yIiwiJGlzVW5vZmZpY2lhbENoZWNrYm94IiwiJGlzb0NvZGVJbnB1dCIsIiRleGNoYW5nZVJhdGVJbnB1dCIsIiRwcmVjaXNpb25JbnB1dCIsIiRyZXNldERlZmF1bHRTZXR0aW5nc0J1dHRvbiIsIiRsb2FkaW5nRGF0YU1vZGFsIiwiX2luaXRMaXN0ZW5lcnMiLCJfaW5pdEZpZWxkcyIsImNoYW5nZSIsIl9vbkN1cnJlbmN5U2VsZWN0b3JDaGFuZ2UiLCJfb25Jc1Vub2ZmaWNpYWxDaGVja2JveENoYW5nZSIsImNsaWNrIiwiX29uUmVzZXREZWZhdWx0U2V0dGluZ3NDbGljayIsIl9pc1Vub2ZmaWNpYWxDdXJyZW5jeSIsInZhbCIsInNlbGVjdGVkSVNPQ29kZSIsIl9yZXNldEN1cnJlbmN5RGF0YSIsIm1vZGFsIiwiZ2V0Q3VycmVuY3lEYXRhIiwicmVwbGFjZSIsImdldCIsInRoZW4iLCJjdXJyZW5jeURhdGEiLCJuYW1lcyIsImxhbmdOYW1lU2VsZWN0b3IiLCJzeW1ib2xzIiwibGFuZ1N5bWJvbFNlbGVjdG9yIiwiaXNvQ29kZSIsImV4Y2hhbmdlUmF0ZSIsInByZWNpc2lvbiIsImZhaWwiLCJlcnJvck1lc3NhZ2UiLCJyZXNwb25zZUpTT04iLCJlcnJvciIsImFsd2F5cyIsImNob2ljZVRyZWUiLCJpbml0Iiwic2hvd0dyb3dsIiwidHlwZSIsIm1lc3NhZ2UiLCJkdXJhdGlvbiIsInVuZGVmaW5lZCIsImdyb3dsIiwidGl0bGUiLCJzaXplIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7OztBQ2hF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QTBDLGFBQVczRSx1QkFBWDtBQUNBLE1BQU1zQixlQUFlLElBQUlZLHNCQUFKLENBQWlCQyx5QkFBakIsQ0FBckI7QUFDQWIsZUFBYXNELElBQWI7QUFDRCxDQU5ELEU7Ozs7Ozs7Ozs7Ozs7UUNSZ0JDLFMsR0FBQUEsUztBQXhCaEI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXdCTyxTQUFTQSxTQUFULENBQW1CQyxJQUFuQixFQUF5QkMsT0FBekIsRUFBa0NDLFFBQWxDLEVBQTRDO0FBQ2pEQSxhQUFXQyxjQUFjRCxRQUFkLEdBQXlCQSxRQUF6QixHQUFvQyxJQUEvQztBQUNBdEgsU0FBT0QsQ0FBUCxDQUFTeUgsS0FBVCxDQUFlSixJQUFmLEVBQXFCO0FBQ25CSyxXQUFPLEVBRFk7QUFFbkJDLFVBQU0sT0FGYTtBQUduQkwsYUFBU0EsT0FIVTtBQUluQkMsY0FBVUE7QUFKUyxHQUFyQjtBQU1ELEUiLCJmaWxlIjoiY3VycmVuY3lfZm9ybS5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDM0Myk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgMWU2NjI2MzkwMGU5NjZkZmJiZjAiLCIoZnVuY3Rpb24oKSB7IG1vZHVsZS5leHBvcnRzID0gd2luZG93W1wialF1ZXJ5XCJdOyB9KCkpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIGV4dGVybmFsIFwialF1ZXJ5XCJcbi8vIG1vZHVsZSBpZCA9IDEyXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDYgMTUgMjAgMzEgMzIgMzkgNDAgNDUiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQge0V2ZW50RW1pdHRlcn0gZnJvbSAnLi9ldmVudC1lbWl0dGVyJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFRoaXMgY2xhc3MgaXMgdXNlZCB0byBhdXRvbWF0aWNhbGx5IHRvZ2dsZSB0cmFuc2xhdGVkIGlucHV0cyAoZGlzcGxheWVkIHdpdGggb25lXG4gKiBpbnB1dCBhbmQgYSBsYW5ndWFnZSBzZWxlY3RvciB1c2luZyB0aGUgVHJhbnNsYXRhYmxlVHlwZSBTeW1mb255IGZvcm0gdHlwZSkuXG4gKiBBbHNvIGNvbXBhdGlibGUgd2l0aCBUcmFuc2xhdGFibGVGaWVsZCBjaGFuZ2VzLlxuICovXG5jbGFzcyBUcmFuc2xhdGFibGVJbnB1dCB7XG4gIGNvbnN0cnVjdG9yKG9wdGlvbnMpIHtcbiAgICBvcHRpb25zID0gb3B0aW9ucyB8fCB7fTtcblxuICAgIHRoaXMubG9jYWxlSXRlbVNlbGVjdG9yID0gb3B0aW9ucy5sb2NhbGVJdGVtU2VsZWN0b3IgfHwgJy5qcy1sb2NhbGUtaXRlbSc7XG4gICAgdGhpcy5sb2NhbGVCdXR0b25TZWxlY3RvciA9IG9wdGlvbnMubG9jYWxlQnV0dG9uU2VsZWN0b3IgfHwgJy5qcy1sb2NhbGUtYnRuJztcbiAgICB0aGlzLmxvY2FsZUlucHV0U2VsZWN0b3IgPSBvcHRpb25zLmxvY2FsZUlucHV0U2VsZWN0b3IgfHwgJy5qcy1sb2NhbGUtaW5wdXQnO1xuXG4gICAgJCgnYm9keScpLm9uKCdjbGljaycsIHRoaXMubG9jYWxlSXRlbVNlbGVjdG9yLCB0aGlzLnRvZ2dsZUxhbmd1YWdlLmJpbmQodGhpcykpO1xuICAgIEV2ZW50RW1pdHRlci5vbignbGFuZ3VhZ2VTZWxlY3RlZCcsIHRoaXMudG9nZ2xlSW5wdXRzLmJpbmQodGhpcykpO1xuICB9XG5cbiAgLyoqXG4gICAqIERpc3BhdGNoIGV2ZW50IG9uIGxhbmd1YWdlIHNlbGVjdGlvbiB0byB1cGRhdGUgaW5wdXRzIGFuZCBvdGhlciBjb21wb25lbnRzIHdoaWNoIGRlcGVuZCBvbiB0aGUgbG9jYWxlLlxuICAgKlxuICAgKiBAcGFyYW0gZXZlbnRcbiAgICovXG4gIHRvZ2dsZUxhbmd1YWdlKGV2ZW50KSB7XG4gICAgY29uc3QgbG9jYWxlSXRlbSA9ICQoZXZlbnQudGFyZ2V0KTtcbiAgICBjb25zdCBmb3JtID0gbG9jYWxlSXRlbS5jbG9zZXN0KCdmb3JtJyk7XG4gICAgRXZlbnRFbWl0dGVyLmVtaXQoJ2xhbmd1YWdlU2VsZWN0ZWQnLCB7c2VsZWN0ZWRMb2NhbGU6IGxvY2FsZUl0ZW0uZGF0YSgnbG9jYWxlJyksIGZvcm06IGZvcm19KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBUb2dnbGUgYWxsIHRyYW5zbGF0YWJsZSBpbnB1dHMgaW4gZm9ybSBpbiB3aGljaCBsb2NhbGUgd2FzIGNoYW5nZWRcbiAgICpcbiAgICogQHBhcmFtIHtFdmVudH0gZXZlbnRcbiAgICovXG4gIHRvZ2dsZUlucHV0cyhldmVudCkge1xuICAgIGNvbnN0IGZvcm0gPSBldmVudC5mb3JtO1xuICAgIGNvbnN0IHNlbGVjdGVkTG9jYWxlID0gZXZlbnQuc2VsZWN0ZWRMb2NhbGU7XG4gICAgY29uc3QgbG9jYWxlQnV0dG9uID0gZm9ybS5maW5kKHRoaXMubG9jYWxlQnV0dG9uU2VsZWN0b3IpO1xuICAgIGNvbnN0IGNoYW5nZUxhbmd1YWdlVXJsID0gbG9jYWxlQnV0dG9uLmRhdGEoJ2NoYW5nZS1sYW5ndWFnZS11cmwnKTtcblxuICAgIGxvY2FsZUJ1dHRvbi50ZXh0KHNlbGVjdGVkTG9jYWxlKTtcbiAgICBmb3JtLmZpbmQodGhpcy5sb2NhbGVJbnB1dFNlbGVjdG9yKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgZm9ybS5maW5kKGAke3RoaXMubG9jYWxlSW5wdXRTZWxlY3Rvcn0uanMtbG9jYWxlLSR7c2VsZWN0ZWRMb2NhbGV9YCkucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuXG4gICAgaWYgKGNoYW5nZUxhbmd1YWdlVXJsKSB7XG4gICAgICB0aGlzLl9zYXZlU2VsZWN0ZWRMYW5ndWFnZShjaGFuZ2VMYW5ndWFnZVVybCwgc2VsZWN0ZWRMb2NhbGUpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBTYXZlIGxhbmd1YWdlIGNob2ljZSBmb3IgZW1wbG95ZWUgZm9ybXMuXG4gICAqXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBjaGFuZ2VMYW5ndWFnZVVybFxuICAgKiBAcGFyYW0ge1N0cmluZ30gc2VsZWN0ZWRMb2NhbGVcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zYXZlU2VsZWN0ZWRMYW5ndWFnZShjaGFuZ2VMYW5ndWFnZVVybCwgc2VsZWN0ZWRMb2NhbGUpIHtcbiAgICAkLnBvc3Qoe1xuICAgICAgdXJsOiBjaGFuZ2VMYW5ndWFnZVVybCxcbiAgICAgIGRhdGE6IHtcbiAgICAgICAgbGFuZ3VhZ2VfaXNvX2NvZGU6IHNlbGVjdGVkTG9jYWxlXG4gICAgICB9LFxuICAgIH0pO1xuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IFRyYW5zbGF0YWJsZUlucHV0O1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy90cmFuc2xhdGFibGUtaW5wdXQuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgRXZlbnRFbWl0dGVyQ2xhc3MgZnJvbSAnZXZlbnRzJztcblxuLyoqXG4gKiBXZSBpbnN0YW5jaWF0ZSBvbmUgRXZlbnRFbWl0dGVyIChyZXN0cmljdGVkIHZpYSBhIGNvbnN0KSBzbyB0aGF0IGV2ZXJ5IGNvbXBvbmVudHNcbiAqIHJlZ2lzdGVyL2Rpc3BhdGNoIG9uIHRoZSBzYW1lIG9uZSBhbmQgY2FuIGNvbW11bmljYXRlIHdpdGggZWFjaCBvdGhlci5cbiAqL1xuZXhwb3J0IGNvbnN0IEV2ZW50RW1pdHRlciA9IG5ldyBFdmVudEVtaXR0ZXJDbGFzcygpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ldmVudC1lbWl0dGVyLmpzIiwiLy8gQ29weXJpZ2h0IEpveWVudCwgSW5jLiBhbmQgb3RoZXIgTm9kZSBjb250cmlidXRvcnMuXG4vL1xuLy8gUGVybWlzc2lvbiBpcyBoZXJlYnkgZ3JhbnRlZCwgZnJlZSBvZiBjaGFyZ2UsIHRvIGFueSBwZXJzb24gb2J0YWluaW5nIGFcbi8vIGNvcHkgb2YgdGhpcyBzb2Z0d2FyZSBhbmQgYXNzb2NpYXRlZCBkb2N1bWVudGF0aW9uIGZpbGVzICh0aGVcbi8vIFwiU29mdHdhcmVcIiksIHRvIGRlYWwgaW4gdGhlIFNvZnR3YXJlIHdpdGhvdXQgcmVzdHJpY3Rpb24sIGluY2x1ZGluZ1xuLy8gd2l0aG91dCBsaW1pdGF0aW9uIHRoZSByaWdodHMgdG8gdXNlLCBjb3B5LCBtb2RpZnksIG1lcmdlLCBwdWJsaXNoLFxuLy8gZGlzdHJpYnV0ZSwgc3VibGljZW5zZSwgYW5kL29yIHNlbGwgY29waWVzIG9mIHRoZSBTb2Z0d2FyZSwgYW5kIHRvIHBlcm1pdFxuLy8gcGVyc29ucyB0byB3aG9tIHRoZSBTb2Z0d2FyZSBpcyBmdXJuaXNoZWQgdG8gZG8gc28sIHN1YmplY3QgdG8gdGhlXG4vLyBmb2xsb3dpbmcgY29uZGl0aW9uczpcbi8vXG4vLyBUaGUgYWJvdmUgY29weXJpZ2h0IG5vdGljZSBhbmQgdGhpcyBwZXJtaXNzaW9uIG5vdGljZSBzaGFsbCBiZSBpbmNsdWRlZFxuLy8gaW4gYWxsIGNvcGllcyBvciBzdWJzdGFudGlhbCBwb3J0aW9ucyBvZiB0aGUgU29mdHdhcmUuXG4vL1xuLy8gVEhFIFNPRlRXQVJFIElTIFBST1ZJREVEIFwiQVMgSVNcIiwgV0lUSE9VVCBXQVJSQU5UWSBPRiBBTlkgS0lORCwgRVhQUkVTU1xuLy8gT1IgSU1QTElFRCwgSU5DTFVESU5HIEJVVCBOT1QgTElNSVRFRCBUTyBUSEUgV0FSUkFOVElFUyBPRlxuLy8gTUVSQ0hBTlRBQklMSVRZLCBGSVRORVNTIEZPUiBBIFBBUlRJQ1VMQVIgUFVSUE9TRSBBTkQgTk9OSU5GUklOR0VNRU5ULiBJTlxuLy8gTk8gRVZFTlQgU0hBTEwgVEhFIEFVVEhPUlMgT1IgQ09QWVJJR0hUIEhPTERFUlMgQkUgTElBQkxFIEZPUiBBTlkgQ0xBSU0sXG4vLyBEQU1BR0VTIE9SIE9USEVSIExJQUJJTElUWSwgV0hFVEhFUiBJTiBBTiBBQ1RJT04gT0YgQ09OVFJBQ1QsIFRPUlQgT1Jcbi8vIE9USEVSV0lTRSwgQVJJU0lORyBGUk9NLCBPVVQgT0YgT1IgSU4gQ09OTkVDVElPTiBXSVRIIFRIRSBTT0ZUV0FSRSBPUiBUSEVcbi8vIFVTRSBPUiBPVEhFUiBERUFMSU5HUyBJTiBUSEUgU09GVFdBUkUuXG5cbid1c2Ugc3RyaWN0JztcblxudmFyIFIgPSB0eXBlb2YgUmVmbGVjdCA9PT0gJ29iamVjdCcgPyBSZWZsZWN0IDogbnVsbFxudmFyIFJlZmxlY3RBcHBseSA9IFIgJiYgdHlwZW9mIFIuYXBwbHkgPT09ICdmdW5jdGlvbidcbiAgPyBSLmFwcGx5XG4gIDogZnVuY3Rpb24gUmVmbGVjdEFwcGx5KHRhcmdldCwgcmVjZWl2ZXIsIGFyZ3MpIHtcbiAgICByZXR1cm4gRnVuY3Rpb24ucHJvdG90eXBlLmFwcGx5LmNhbGwodGFyZ2V0LCByZWNlaXZlciwgYXJncyk7XG4gIH1cblxudmFyIFJlZmxlY3RPd25LZXlzXG5pZiAoUiAmJiB0eXBlb2YgUi5vd25LZXlzID09PSAnZnVuY3Rpb24nKSB7XG4gIFJlZmxlY3RPd25LZXlzID0gUi5vd25LZXlzXG59IGVsc2UgaWYgKE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHMpIHtcbiAgUmVmbGVjdE93bktleXMgPSBmdW5jdGlvbiBSZWZsZWN0T3duS2V5cyh0YXJnZXQpIHtcbiAgICByZXR1cm4gT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXModGFyZ2V0KVxuICAgICAgLmNvbmNhdChPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzKHRhcmdldCkpO1xuICB9O1xufSBlbHNlIHtcbiAgUmVmbGVjdE93bktleXMgPSBmdW5jdGlvbiBSZWZsZWN0T3duS2V5cyh0YXJnZXQpIHtcbiAgICByZXR1cm4gT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXModGFyZ2V0KTtcbiAgfTtcbn1cblxuZnVuY3Rpb24gUHJvY2Vzc0VtaXRXYXJuaW5nKHdhcm5pbmcpIHtcbiAgaWYgKGNvbnNvbGUgJiYgY29uc29sZS53YXJuKSBjb25zb2xlLndhcm4od2FybmluZyk7XG59XG5cbnZhciBOdW1iZXJJc05hTiA9IE51bWJlci5pc05hTiB8fCBmdW5jdGlvbiBOdW1iZXJJc05hTih2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgIT09IHZhbHVlO1xufVxuXG5mdW5jdGlvbiBFdmVudEVtaXR0ZXIoKSB7XG4gIEV2ZW50RW1pdHRlci5pbml0LmNhbGwodGhpcyk7XG59XG5tb2R1bGUuZXhwb3J0cyA9IEV2ZW50RW1pdHRlcjtcblxuLy8gQmFja3dhcmRzLWNvbXBhdCB3aXRoIG5vZGUgMC4xMC54XG5FdmVudEVtaXR0ZXIuRXZlbnRFbWl0dGVyID0gRXZlbnRFbWl0dGVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9ldmVudHMgPSB1bmRlZmluZWQ7XG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9ldmVudHNDb3VudCA9IDA7XG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9tYXhMaXN0ZW5lcnMgPSB1bmRlZmluZWQ7XG5cbi8vIEJ5IGRlZmF1bHQgRXZlbnRFbWl0dGVycyB3aWxsIHByaW50IGEgd2FybmluZyBpZiBtb3JlIHRoYW4gMTAgbGlzdGVuZXJzIGFyZVxuLy8gYWRkZWQgdG8gaXQuIFRoaXMgaXMgYSB1c2VmdWwgZGVmYXVsdCB3aGljaCBoZWxwcyBmaW5kaW5nIG1lbW9yeSBsZWFrcy5cbnZhciBkZWZhdWx0TWF4TGlzdGVuZXJzID0gMTA7XG5cbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShFdmVudEVtaXR0ZXIsICdkZWZhdWx0TWF4TGlzdGVuZXJzJywge1xuICBlbnVtZXJhYmxlOiB0cnVlLFxuICBnZXQ6IGZ1bmN0aW9uKCkge1xuICAgIHJldHVybiBkZWZhdWx0TWF4TGlzdGVuZXJzO1xuICB9LFxuICBzZXQ6IGZ1bmN0aW9uKGFyZykge1xuICAgIGlmICh0eXBlb2YgYXJnICE9PSAnbnVtYmVyJyB8fCBhcmcgPCAwIHx8IE51bWJlcklzTmFOKGFyZykpIHtcbiAgICAgIHRocm93IG5ldyBSYW5nZUVycm9yKCdUaGUgdmFsdWUgb2YgXCJkZWZhdWx0TWF4TGlzdGVuZXJzXCIgaXMgb3V0IG9mIHJhbmdlLiBJdCBtdXN0IGJlIGEgbm9uLW5lZ2F0aXZlIG51bWJlci4gUmVjZWl2ZWQgJyArIGFyZyArICcuJyk7XG4gICAgfVxuICAgIGRlZmF1bHRNYXhMaXN0ZW5lcnMgPSBhcmc7XG4gIH1cbn0pO1xuXG5FdmVudEVtaXR0ZXIuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXG4gIGlmICh0aGlzLl9ldmVudHMgPT09IHVuZGVmaW5lZCB8fFxuICAgICAgdGhpcy5fZXZlbnRzID09PSBPYmplY3QuZ2V0UHJvdG90eXBlT2YodGhpcykuX2V2ZW50cykge1xuICAgIHRoaXMuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgdGhpcy5fZXZlbnRzQ291bnQgPSAwO1xuICB9XG5cbiAgdGhpcy5fbWF4TGlzdGVuZXJzID0gdGhpcy5fbWF4TGlzdGVuZXJzIHx8IHVuZGVmaW5lZDtcbn07XG5cbi8vIE9idmlvdXNseSBub3QgYWxsIEVtaXR0ZXJzIHNob3VsZCBiZSBsaW1pdGVkIHRvIDEwLiBUaGlzIGZ1bmN0aW9uIGFsbG93c1xuLy8gdGhhdCB0byBiZSBpbmNyZWFzZWQuIFNldCB0byB6ZXJvIGZvciB1bmxpbWl0ZWQuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnNldE1heExpc3RlbmVycyA9IGZ1bmN0aW9uIHNldE1heExpc3RlbmVycyhuKSB7XG4gIGlmICh0eXBlb2YgbiAhPT0gJ251bWJlcicgfHwgbiA8IDAgfHwgTnVtYmVySXNOYU4obikpIHtcbiAgICB0aHJvdyBuZXcgUmFuZ2VFcnJvcignVGhlIHZhbHVlIG9mIFwiblwiIGlzIG91dCBvZiByYW5nZS4gSXQgbXVzdCBiZSBhIG5vbi1uZWdhdGl2ZSBudW1iZXIuIFJlY2VpdmVkICcgKyBuICsgJy4nKTtcbiAgfVxuICB0aGlzLl9tYXhMaXN0ZW5lcnMgPSBuO1xuICByZXR1cm4gdGhpcztcbn07XG5cbmZ1bmN0aW9uICRnZXRNYXhMaXN0ZW5lcnModGhhdCkge1xuICBpZiAodGhhdC5fbWF4TGlzdGVuZXJzID09PSB1bmRlZmluZWQpXG4gICAgcmV0dXJuIEV2ZW50RW1pdHRlci5kZWZhdWx0TWF4TGlzdGVuZXJzO1xuICByZXR1cm4gdGhhdC5fbWF4TGlzdGVuZXJzO1xufVxuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmdldE1heExpc3RlbmVycyA9IGZ1bmN0aW9uIGdldE1heExpc3RlbmVycygpIHtcbiAgcmV0dXJuICRnZXRNYXhMaXN0ZW5lcnModGhpcyk7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmVtaXQgPSBmdW5jdGlvbiBlbWl0KHR5cGUpIHtcbiAgdmFyIGFyZ3MgPSBbXTtcbiAgZm9yICh2YXIgaSA9IDE7IGkgPCBhcmd1bWVudHMubGVuZ3RoOyBpKyspIGFyZ3MucHVzaChhcmd1bWVudHNbaV0pO1xuICB2YXIgZG9FcnJvciA9ICh0eXBlID09PSAnZXJyb3InKTtcblxuICB2YXIgZXZlbnRzID0gdGhpcy5fZXZlbnRzO1xuICBpZiAoZXZlbnRzICE9PSB1bmRlZmluZWQpXG4gICAgZG9FcnJvciA9IChkb0Vycm9yICYmIGV2ZW50cy5lcnJvciA9PT0gdW5kZWZpbmVkKTtcbiAgZWxzZSBpZiAoIWRvRXJyb3IpXG4gICAgcmV0dXJuIGZhbHNlO1xuXG4gIC8vIElmIHRoZXJlIGlzIG5vICdlcnJvcicgZXZlbnQgbGlzdGVuZXIgdGhlbiB0aHJvdy5cbiAgaWYgKGRvRXJyb3IpIHtcbiAgICB2YXIgZXI7XG4gICAgaWYgKGFyZ3MubGVuZ3RoID4gMClcbiAgICAgIGVyID0gYXJnc1swXTtcbiAgICBpZiAoZXIgaW5zdGFuY2VvZiBFcnJvcikge1xuICAgICAgLy8gTm90ZTogVGhlIGNvbW1lbnRzIG9uIHRoZSBgdGhyb3dgIGxpbmVzIGFyZSBpbnRlbnRpb25hbCwgdGhleSBzaG93XG4gICAgICAvLyB1cCBpbiBOb2RlJ3Mgb3V0cHV0IGlmIHRoaXMgcmVzdWx0cyBpbiBhbiB1bmhhbmRsZWQgZXhjZXB0aW9uLlxuICAgICAgdGhyb3cgZXI7IC8vIFVuaGFuZGxlZCAnZXJyb3InIGV2ZW50XG4gICAgfVxuICAgIC8vIEF0IGxlYXN0IGdpdmUgc29tZSBraW5kIG9mIGNvbnRleHQgdG8gdGhlIHVzZXJcbiAgICB2YXIgZXJyID0gbmV3IEVycm9yKCdVbmhhbmRsZWQgZXJyb3IuJyArIChlciA/ICcgKCcgKyBlci5tZXNzYWdlICsgJyknIDogJycpKTtcbiAgICBlcnIuY29udGV4dCA9IGVyO1xuICAgIHRocm93IGVycjsgLy8gVW5oYW5kbGVkICdlcnJvcicgZXZlbnRcbiAgfVxuXG4gIHZhciBoYW5kbGVyID0gZXZlbnRzW3R5cGVdO1xuXG4gIGlmIChoYW5kbGVyID09PSB1bmRlZmluZWQpXG4gICAgcmV0dXJuIGZhbHNlO1xuXG4gIGlmICh0eXBlb2YgaGFuZGxlciA9PT0gJ2Z1bmN0aW9uJykge1xuICAgIFJlZmxlY3RBcHBseShoYW5kbGVyLCB0aGlzLCBhcmdzKTtcbiAgfSBlbHNlIHtcbiAgICB2YXIgbGVuID0gaGFuZGxlci5sZW5ndGg7XG4gICAgdmFyIGxpc3RlbmVycyA9IGFycmF5Q2xvbmUoaGFuZGxlciwgbGVuKTtcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IGxlbjsgKytpKVxuICAgICAgUmVmbGVjdEFwcGx5KGxpc3RlbmVyc1tpXSwgdGhpcywgYXJncyk7XG4gIH1cblxuICByZXR1cm4gdHJ1ZTtcbn07XG5cbmZ1bmN0aW9uIF9hZGRMaXN0ZW5lcih0YXJnZXQsIHR5cGUsIGxpc3RlbmVyLCBwcmVwZW5kKSB7XG4gIHZhciBtO1xuICB2YXIgZXZlbnRzO1xuICB2YXIgZXhpc3Rpbmc7XG5cbiAgaWYgKHR5cGVvZiBsaXN0ZW5lciAhPT0gJ2Z1bmN0aW9uJykge1xuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ1RoZSBcImxpc3RlbmVyXCIgYXJndW1lbnQgbXVzdCBiZSBvZiB0eXBlIEZ1bmN0aW9uLiBSZWNlaXZlZCB0eXBlICcgKyB0eXBlb2YgbGlzdGVuZXIpO1xuICB9XG5cbiAgZXZlbnRzID0gdGFyZ2V0Ll9ldmVudHM7XG4gIGlmIChldmVudHMgPT09IHVuZGVmaW5lZCkge1xuICAgIGV2ZW50cyA9IHRhcmdldC5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICB0YXJnZXQuX2V2ZW50c0NvdW50ID0gMDtcbiAgfSBlbHNlIHtcbiAgICAvLyBUbyBhdm9pZCByZWN1cnNpb24gaW4gdGhlIGNhc2UgdGhhdCB0eXBlID09PSBcIm5ld0xpc3RlbmVyXCIhIEJlZm9yZVxuICAgIC8vIGFkZGluZyBpdCB0byB0aGUgbGlzdGVuZXJzLCBmaXJzdCBlbWl0IFwibmV3TGlzdGVuZXJcIi5cbiAgICBpZiAoZXZlbnRzLm5ld0xpc3RlbmVyICE9PSB1bmRlZmluZWQpIHtcbiAgICAgIHRhcmdldC5lbWl0KCduZXdMaXN0ZW5lcicsIHR5cGUsXG4gICAgICAgICAgICAgICAgICBsaXN0ZW5lci5saXN0ZW5lciA/IGxpc3RlbmVyLmxpc3RlbmVyIDogbGlzdGVuZXIpO1xuXG4gICAgICAvLyBSZS1hc3NpZ24gYGV2ZW50c2AgYmVjYXVzZSBhIG5ld0xpc3RlbmVyIGhhbmRsZXIgY291bGQgaGF2ZSBjYXVzZWQgdGhlXG4gICAgICAvLyB0aGlzLl9ldmVudHMgdG8gYmUgYXNzaWduZWQgdG8gYSBuZXcgb2JqZWN0XG4gICAgICBldmVudHMgPSB0YXJnZXQuX2V2ZW50cztcbiAgICB9XG4gICAgZXhpc3RpbmcgPSBldmVudHNbdHlwZV07XG4gIH1cblxuICBpZiAoZXhpc3RpbmcgPT09IHVuZGVmaW5lZCkge1xuICAgIC8vIE9wdGltaXplIHRoZSBjYXNlIG9mIG9uZSBsaXN0ZW5lci4gRG9uJ3QgbmVlZCB0aGUgZXh0cmEgYXJyYXkgb2JqZWN0LlxuICAgIGV4aXN0aW5nID0gZXZlbnRzW3R5cGVdID0gbGlzdGVuZXI7XG4gICAgKyt0YXJnZXQuX2V2ZW50c0NvdW50O1xuICB9IGVsc2Uge1xuICAgIGlmICh0eXBlb2YgZXhpc3RpbmcgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgIC8vIEFkZGluZyB0aGUgc2Vjb25kIGVsZW1lbnQsIG5lZWQgdG8gY2hhbmdlIHRvIGFycmF5LlxuICAgICAgZXhpc3RpbmcgPSBldmVudHNbdHlwZV0gPVxuICAgICAgICBwcmVwZW5kID8gW2xpc3RlbmVyLCBleGlzdGluZ10gOiBbZXhpc3RpbmcsIGxpc3RlbmVyXTtcbiAgICAgIC8vIElmIHdlJ3ZlIGFscmVhZHkgZ290IGFuIGFycmF5LCBqdXN0IGFwcGVuZC5cbiAgICB9IGVsc2UgaWYgKHByZXBlbmQpIHtcbiAgICAgIGV4aXN0aW5nLnVuc2hpZnQobGlzdGVuZXIpO1xuICAgIH0gZWxzZSB7XG4gICAgICBleGlzdGluZy5wdXNoKGxpc3RlbmVyKTtcbiAgICB9XG5cbiAgICAvLyBDaGVjayBmb3IgbGlzdGVuZXIgbGVha1xuICAgIG0gPSAkZ2V0TWF4TGlzdGVuZXJzKHRhcmdldCk7XG4gICAgaWYgKG0gPiAwICYmIGV4aXN0aW5nLmxlbmd0aCA+IG0gJiYgIWV4aXN0aW5nLndhcm5lZCkge1xuICAgICAgZXhpc3Rpbmcud2FybmVkID0gdHJ1ZTtcbiAgICAgIC8vIE5vIGVycm9yIGNvZGUgZm9yIHRoaXMgc2luY2UgaXQgaXMgYSBXYXJuaW5nXG4gICAgICAvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgbm8tcmVzdHJpY3RlZC1zeW50YXhcbiAgICAgIHZhciB3ID0gbmV3IEVycm9yKCdQb3NzaWJsZSBFdmVudEVtaXR0ZXIgbWVtb3J5IGxlYWsgZGV0ZWN0ZWQuICcgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICBleGlzdGluZy5sZW5ndGggKyAnICcgKyBTdHJpbmcodHlwZSkgKyAnIGxpc3RlbmVycyAnICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgJ2FkZGVkLiBVc2UgZW1pdHRlci5zZXRNYXhMaXN0ZW5lcnMoKSB0byAnICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgJ2luY3JlYXNlIGxpbWl0Jyk7XG4gICAgICB3Lm5hbWUgPSAnTWF4TGlzdGVuZXJzRXhjZWVkZWRXYXJuaW5nJztcbiAgICAgIHcuZW1pdHRlciA9IHRhcmdldDtcbiAgICAgIHcudHlwZSA9IHR5cGU7XG4gICAgICB3LmNvdW50ID0gZXhpc3RpbmcubGVuZ3RoO1xuICAgICAgUHJvY2Vzc0VtaXRXYXJuaW5nKHcpO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiB0YXJnZXQ7XG59XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuYWRkTGlzdGVuZXIgPSBmdW5jdGlvbiBhZGRMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcikge1xuICByZXR1cm4gX2FkZExpc3RlbmVyKHRoaXMsIHR5cGUsIGxpc3RlbmVyLCBmYWxzZSk7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9uID0gRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5hZGRMaXN0ZW5lcjtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5wcmVwZW5kTGlzdGVuZXIgPVxuICAgIGZ1bmN0aW9uIHByZXBlbmRMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcikge1xuICAgICAgcmV0dXJuIF9hZGRMaXN0ZW5lcih0aGlzLCB0eXBlLCBsaXN0ZW5lciwgdHJ1ZSk7XG4gICAgfTtcblxuZnVuY3Rpb24gb25jZVdyYXBwZXIoKSB7XG4gIHZhciBhcmdzID0gW107XG4gIGZvciAodmFyIGkgPSAwOyBpIDwgYXJndW1lbnRzLmxlbmd0aDsgaSsrKSBhcmdzLnB1c2goYXJndW1lbnRzW2ldKTtcbiAgaWYgKCF0aGlzLmZpcmVkKSB7XG4gICAgdGhpcy50YXJnZXQucmVtb3ZlTGlzdGVuZXIodGhpcy50eXBlLCB0aGlzLndyYXBGbik7XG4gICAgdGhpcy5maXJlZCA9IHRydWU7XG4gICAgUmVmbGVjdEFwcGx5KHRoaXMubGlzdGVuZXIsIHRoaXMudGFyZ2V0LCBhcmdzKTtcbiAgfVxufVxuXG5mdW5jdGlvbiBfb25jZVdyYXAodGFyZ2V0LCB0eXBlLCBsaXN0ZW5lcikge1xuICB2YXIgc3RhdGUgPSB7IGZpcmVkOiBmYWxzZSwgd3JhcEZuOiB1bmRlZmluZWQsIHRhcmdldDogdGFyZ2V0LCB0eXBlOiB0eXBlLCBsaXN0ZW5lcjogbGlzdGVuZXIgfTtcbiAgdmFyIHdyYXBwZWQgPSBvbmNlV3JhcHBlci5iaW5kKHN0YXRlKTtcbiAgd3JhcHBlZC5saXN0ZW5lciA9IGxpc3RlbmVyO1xuICBzdGF0ZS53cmFwRm4gPSB3cmFwcGVkO1xuICByZXR1cm4gd3JhcHBlZDtcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5vbmNlID0gZnVuY3Rpb24gb25jZSh0eXBlLCBsaXN0ZW5lcikge1xuICBpZiAodHlwZW9mIGxpc3RlbmVyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgdGhyb3cgbmV3IFR5cGVFcnJvcignVGhlIFwibGlzdGVuZXJcIiBhcmd1bWVudCBtdXN0IGJlIG9mIHR5cGUgRnVuY3Rpb24uIFJlY2VpdmVkIHR5cGUgJyArIHR5cGVvZiBsaXN0ZW5lcik7XG4gIH1cbiAgdGhpcy5vbih0eXBlLCBfb25jZVdyYXAodGhpcywgdHlwZSwgbGlzdGVuZXIpKTtcbiAgcmV0dXJuIHRoaXM7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnByZXBlbmRPbmNlTGlzdGVuZXIgPVxuICAgIGZ1bmN0aW9uIHByZXBlbmRPbmNlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXIpIHtcbiAgICAgIGlmICh0eXBlb2YgbGlzdGVuZXIgIT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgdGhyb3cgbmV3IFR5cGVFcnJvcignVGhlIFwibGlzdGVuZXJcIiBhcmd1bWVudCBtdXN0IGJlIG9mIHR5cGUgRnVuY3Rpb24uIFJlY2VpdmVkIHR5cGUgJyArIHR5cGVvZiBsaXN0ZW5lcik7XG4gICAgICB9XG4gICAgICB0aGlzLnByZXBlbmRMaXN0ZW5lcih0eXBlLCBfb25jZVdyYXAodGhpcywgdHlwZSwgbGlzdGVuZXIpKTtcbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH07XG5cbi8vIEVtaXRzIGEgJ3JlbW92ZUxpc3RlbmVyJyBldmVudCBpZiBhbmQgb25seSBpZiB0aGUgbGlzdGVuZXIgd2FzIHJlbW92ZWQuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnJlbW92ZUxpc3RlbmVyID1cbiAgICBmdW5jdGlvbiByZW1vdmVMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcikge1xuICAgICAgdmFyIGxpc3QsIGV2ZW50cywgcG9zaXRpb24sIGksIG9yaWdpbmFsTGlzdGVuZXI7XG5cbiAgICAgIGlmICh0eXBlb2YgbGlzdGVuZXIgIT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgdGhyb3cgbmV3IFR5cGVFcnJvcignVGhlIFwibGlzdGVuZXJcIiBhcmd1bWVudCBtdXN0IGJlIG9mIHR5cGUgRnVuY3Rpb24uIFJlY2VpdmVkIHR5cGUgJyArIHR5cGVvZiBsaXN0ZW5lcik7XG4gICAgICB9XG5cbiAgICAgIGV2ZW50cyA9IHRoaXMuX2V2ZW50cztcbiAgICAgIGlmIChldmVudHMgPT09IHVuZGVmaW5lZClcbiAgICAgICAgcmV0dXJuIHRoaXM7XG5cbiAgICAgIGxpc3QgPSBldmVudHNbdHlwZV07XG4gICAgICBpZiAobGlzdCA9PT0gdW5kZWZpbmVkKVxuICAgICAgICByZXR1cm4gdGhpcztcblxuICAgICAgaWYgKGxpc3QgPT09IGxpc3RlbmVyIHx8IGxpc3QubGlzdGVuZXIgPT09IGxpc3RlbmVyKSB7XG4gICAgICAgIGlmICgtLXRoaXMuX2V2ZW50c0NvdW50ID09PSAwKVxuICAgICAgICAgIHRoaXMuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgICAgIGVsc2Uge1xuICAgICAgICAgIGRlbGV0ZSBldmVudHNbdHlwZV07XG4gICAgICAgICAgaWYgKGV2ZW50cy5yZW1vdmVMaXN0ZW5lcilcbiAgICAgICAgICAgIHRoaXMuZW1pdCgncmVtb3ZlTGlzdGVuZXInLCB0eXBlLCBsaXN0Lmxpc3RlbmVyIHx8IGxpc3RlbmVyKTtcbiAgICAgICAgfVxuICAgICAgfSBlbHNlIGlmICh0eXBlb2YgbGlzdCAhPT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICBwb3NpdGlvbiA9IC0xO1xuXG4gICAgICAgIGZvciAoaSA9IGxpc3QubGVuZ3RoIC0gMTsgaSA+PSAwOyBpLS0pIHtcbiAgICAgICAgICBpZiAobGlzdFtpXSA9PT0gbGlzdGVuZXIgfHwgbGlzdFtpXS5saXN0ZW5lciA9PT0gbGlzdGVuZXIpIHtcbiAgICAgICAgICAgIG9yaWdpbmFsTGlzdGVuZXIgPSBsaXN0W2ldLmxpc3RlbmVyO1xuICAgICAgICAgICAgcG9zaXRpb24gPSBpO1xuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHBvc2l0aW9uIDwgMClcbiAgICAgICAgICByZXR1cm4gdGhpcztcblxuICAgICAgICBpZiAocG9zaXRpb24gPT09IDApXG4gICAgICAgICAgbGlzdC5zaGlmdCgpO1xuICAgICAgICBlbHNlIHtcbiAgICAgICAgICBzcGxpY2VPbmUobGlzdCwgcG9zaXRpb24pO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGxpc3QubGVuZ3RoID09PSAxKVxuICAgICAgICAgIGV2ZW50c1t0eXBlXSA9IGxpc3RbMF07XG5cbiAgICAgICAgaWYgKGV2ZW50cy5yZW1vdmVMaXN0ZW5lciAhPT0gdW5kZWZpbmVkKVxuICAgICAgICAgIHRoaXMuZW1pdCgncmVtb3ZlTGlzdGVuZXInLCB0eXBlLCBvcmlnaW5hbExpc3RlbmVyIHx8IGxpc3RlbmVyKTtcbiAgICAgIH1cblxuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5vZmYgPSBFdmVudEVtaXR0ZXIucHJvdG90eXBlLnJlbW92ZUxpc3RlbmVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnJlbW92ZUFsbExpc3RlbmVycyA9XG4gICAgZnVuY3Rpb24gcmVtb3ZlQWxsTGlzdGVuZXJzKHR5cGUpIHtcbiAgICAgIHZhciBsaXN0ZW5lcnMsIGV2ZW50cywgaTtcblxuICAgICAgZXZlbnRzID0gdGhpcy5fZXZlbnRzO1xuICAgICAgaWYgKGV2ZW50cyA9PT0gdW5kZWZpbmVkKVxuICAgICAgICByZXR1cm4gdGhpcztcblxuICAgICAgLy8gbm90IGxpc3RlbmluZyBmb3IgcmVtb3ZlTGlzdGVuZXIsIG5vIG5lZWQgdG8gZW1pdFxuICAgICAgaWYgKGV2ZW50cy5yZW1vdmVMaXN0ZW5lciA9PT0gdW5kZWZpbmVkKSB7XG4gICAgICAgIGlmIChhcmd1bWVudHMubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICAgICAgICB0aGlzLl9ldmVudHNDb3VudCA9IDA7XG4gICAgICAgIH0gZWxzZSBpZiAoZXZlbnRzW3R5cGVdICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICBpZiAoLS10aGlzLl9ldmVudHNDb3VudCA9PT0gMClcbiAgICAgICAgICAgIHRoaXMuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgICAgICAgZWxzZVxuICAgICAgICAgICAgZGVsZXRlIGV2ZW50c1t0eXBlXTtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICAgIH1cblxuICAgICAgLy8gZW1pdCByZW1vdmVMaXN0ZW5lciBmb3IgYWxsIGxpc3RlbmVycyBvbiBhbGwgZXZlbnRzXG4gICAgICBpZiAoYXJndW1lbnRzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICB2YXIga2V5cyA9IE9iamVjdC5rZXlzKGV2ZW50cyk7XG4gICAgICAgIHZhciBrZXk7XG4gICAgICAgIGZvciAoaSA9IDA7IGkgPCBrZXlzLmxlbmd0aDsgKytpKSB7XG4gICAgICAgICAga2V5ID0ga2V5c1tpXTtcbiAgICAgICAgICBpZiAoa2V5ID09PSAncmVtb3ZlTGlzdGVuZXInKSBjb250aW51ZTtcbiAgICAgICAgICB0aGlzLnJlbW92ZUFsbExpc3RlbmVycyhrZXkpO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMucmVtb3ZlQWxsTGlzdGVuZXJzKCdyZW1vdmVMaXN0ZW5lcicpO1xuICAgICAgICB0aGlzLl9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgICAgICB0aGlzLl9ldmVudHNDb3VudCA9IDA7XG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgICAgfVxuXG4gICAgICBsaXN0ZW5lcnMgPSBldmVudHNbdHlwZV07XG5cbiAgICAgIGlmICh0eXBlb2YgbGlzdGVuZXJzID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIHRoaXMucmVtb3ZlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXJzKTtcbiAgICAgIH0gZWxzZSBpZiAobGlzdGVuZXJzICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgLy8gTElGTyBvcmRlclxuICAgICAgICBmb3IgKGkgPSBsaXN0ZW5lcnMubGVuZ3RoIC0gMTsgaSA+PSAwOyBpLS0pIHtcbiAgICAgICAgICB0aGlzLnJlbW92ZUxpc3RlbmVyKHR5cGUsIGxpc3RlbmVyc1tpXSk7XG4gICAgICAgIH1cbiAgICAgIH1cblxuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfTtcblxuZnVuY3Rpb24gX2xpc3RlbmVycyh0YXJnZXQsIHR5cGUsIHVud3JhcCkge1xuICB2YXIgZXZlbnRzID0gdGFyZ2V0Ll9ldmVudHM7XG5cbiAgaWYgKGV2ZW50cyA9PT0gdW5kZWZpbmVkKVxuICAgIHJldHVybiBbXTtcblxuICB2YXIgZXZsaXN0ZW5lciA9IGV2ZW50c1t0eXBlXTtcbiAgaWYgKGV2bGlzdGVuZXIgPT09IHVuZGVmaW5lZClcbiAgICByZXR1cm4gW107XG5cbiAgaWYgKHR5cGVvZiBldmxpc3RlbmVyID09PSAnZnVuY3Rpb24nKVxuICAgIHJldHVybiB1bndyYXAgPyBbZXZsaXN0ZW5lci5saXN0ZW5lciB8fCBldmxpc3RlbmVyXSA6IFtldmxpc3RlbmVyXTtcblxuICByZXR1cm4gdW53cmFwID9cbiAgICB1bndyYXBMaXN0ZW5lcnMoZXZsaXN0ZW5lcikgOiBhcnJheUNsb25lKGV2bGlzdGVuZXIsIGV2bGlzdGVuZXIubGVuZ3RoKTtcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5saXN0ZW5lcnMgPSBmdW5jdGlvbiBsaXN0ZW5lcnModHlwZSkge1xuICByZXR1cm4gX2xpc3RlbmVycyh0aGlzLCB0eXBlLCB0cnVlKTtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmF3TGlzdGVuZXJzID0gZnVuY3Rpb24gcmF3TGlzdGVuZXJzKHR5cGUpIHtcbiAgcmV0dXJuIF9saXN0ZW5lcnModGhpcywgdHlwZSwgZmFsc2UpO1xufTtcblxuRXZlbnRFbWl0dGVyLmxpc3RlbmVyQ291bnQgPSBmdW5jdGlvbihlbWl0dGVyLCB0eXBlKSB7XG4gIGlmICh0eXBlb2YgZW1pdHRlci5saXN0ZW5lckNvdW50ID09PSAnZnVuY3Rpb24nKSB7XG4gICAgcmV0dXJuIGVtaXR0ZXIubGlzdGVuZXJDb3VudCh0eXBlKTtcbiAgfSBlbHNlIHtcbiAgICByZXR1cm4gbGlzdGVuZXJDb3VudC5jYWxsKGVtaXR0ZXIsIHR5cGUpO1xuICB9XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmxpc3RlbmVyQ291bnQgPSBsaXN0ZW5lckNvdW50O1xuZnVuY3Rpb24gbGlzdGVuZXJDb3VudCh0eXBlKSB7XG4gIHZhciBldmVudHMgPSB0aGlzLl9ldmVudHM7XG5cbiAgaWYgKGV2ZW50cyAhPT0gdW5kZWZpbmVkKSB7XG4gICAgdmFyIGV2bGlzdGVuZXIgPSBldmVudHNbdHlwZV07XG5cbiAgICBpZiAodHlwZW9mIGV2bGlzdGVuZXIgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgIHJldHVybiAxO1xuICAgIH0gZWxzZSBpZiAoZXZsaXN0ZW5lciAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICByZXR1cm4gZXZsaXN0ZW5lci5sZW5ndGg7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIDA7XG59XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuZXZlbnROYW1lcyA9IGZ1bmN0aW9uIGV2ZW50TmFtZXMoKSB7XG4gIHJldHVybiB0aGlzLl9ldmVudHNDb3VudCA+IDAgPyBSZWZsZWN0T3duS2V5cyh0aGlzLl9ldmVudHMpIDogW107XG59O1xuXG5mdW5jdGlvbiBhcnJheUNsb25lKGFyciwgbikge1xuICB2YXIgY29weSA9IG5ldyBBcnJheShuKTtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCBuOyArK2kpXG4gICAgY29weVtpXSA9IGFycltpXTtcbiAgcmV0dXJuIGNvcHk7XG59XG5cbmZ1bmN0aW9uIHNwbGljZU9uZShsaXN0LCBpbmRleCkge1xuICBmb3IgKDsgaW5kZXggKyAxIDwgbGlzdC5sZW5ndGg7IGluZGV4KyspXG4gICAgbGlzdFtpbmRleF0gPSBsaXN0W2luZGV4ICsgMV07XG4gIGxpc3QucG9wKCk7XG59XG5cbmZ1bmN0aW9uIHVud3JhcExpc3RlbmVycyhhcnIpIHtcbiAgdmFyIHJldCA9IG5ldyBBcnJheShhcnIubGVuZ3RoKTtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCByZXQubGVuZ3RoOyArK2kpIHtcbiAgICByZXRbaV0gPSBhcnJbaV0ubGlzdGVuZXIgfHwgYXJyW2ldO1xuICB9XG4gIHJldHVybiByZXQ7XG59XG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vZXZlbnRzL2V2ZW50cy5qc1xuLy8gbW9kdWxlIGlkID0gMTlcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDUgNiA3IDggMTAgMTEgMTIgMTMgMzAgMzIgMzMgMzYgMzcgMzkgNDMgNDQiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogSGFuZGxlcyBVSSBpbnRlcmFjdGlvbnMgb2YgY2hvaWNlIHRyZWVcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ2hvaWNlVHJlZSB7XG4gIC8qKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gdHJlZVNlbGVjdG9yXG4gICAqL1xuICBjb25zdHJ1Y3Rvcih0cmVlU2VsZWN0b3IpIHtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKHRyZWVTZWxlY3Rvcik7XG5cbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgJy5qcy1pbnB1dC13cmFwcGVyJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkaW5wdXRXcmFwcGVyID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcblxuICAgICAgdGhpcy5fdG9nZ2xlQ2hpbGRUcmVlKCRpbnB1dFdyYXBwZXIpO1xuICAgIH0pO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsICcuanMtdG9nZ2xlLWNob2ljZS10cmVlLWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGFjdGlvbiA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG5cbiAgICAgIHRoaXMuX3RvZ2dsZVRyZWUoJGFjdGlvbik7XG4gICAgfSk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW46ICgpID0+IHRoaXMuZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4oKSxcbiAgICAgIGVuYWJsZUFsbElucHV0czogKCkgPT4gdGhpcy5lbmFibGVBbGxJbnB1dHMoKSxcbiAgICAgIGRpc2FibGVBbGxJbnB1dHM6ICgpID0+IHRoaXMuZGlzYWJsZUFsbElucHV0cygpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogRW5hYmxlIGF1dG9tYXRpYyBjaGVjay91bmNoZWNrIG9mIGNsaWNrZWQgaXRlbSdzIGNoaWxkcmVuLlxuICAgKi9cbiAgZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4oKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjaGFuZ2UnLCAnaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkY2xpY2tlZENoZWNrYm94ID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0ICRpdGVtV2l0aENoaWxkcmVuID0gJGNsaWNrZWRDaGVja2JveC5jbG9zZXN0KCdsaScpO1xuXG4gICAgICAkaXRlbVdpdGhDaGlsZHJlblxuICAgICAgICAuZmluZCgndWwgaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJylcbiAgICAgICAgLnByb3AoJ2NoZWNrZWQnLCAkY2xpY2tlZENoZWNrYm94LmlzKCc6Y2hlY2tlZCcpKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbmFibGUgYWxsIGlucHV0cyBpbiB0aGUgY2hvaWNlIHRyZWUuXG4gICAqL1xuICBlbmFibGVBbGxJbnB1dHMoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLmZpbmQoJ2lucHV0JykucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNhYmxlIGFsbCBpbnB1dHMgaW4gdGhlIGNob2ljZSB0cmVlLlxuICAgKi9cbiAgZGlzYWJsZUFsbElucHV0cygpIHtcbiAgICB0aGlzLiRjb250YWluZXIuZmluZCgnaW5wdXQnKS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIENvbGxhcHNlIG9yIGV4cGFuZCBzdWItdHJlZSBmb3Igc2luZ2xlIHBhcmVudFxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGlucHV0V3JhcHBlclxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3RvZ2dsZUNoaWxkVHJlZSgkaW5wdXRXcmFwcGVyKSB7XG4gICAgY29uc3QgJHBhcmVudFdyYXBwZXIgPSAkaW5wdXRXcmFwcGVyLmNsb3Nlc3QoJ2xpJyk7XG5cbiAgICBpZiAoJHBhcmVudFdyYXBwZXIuaGFzQ2xhc3MoJ2V4cGFuZGVkJykpIHtcbiAgICAgICRwYXJlbnRXcmFwcGVyXG4gICAgICAgIC5yZW1vdmVDbGFzcygnZXhwYW5kZWQnKVxuICAgICAgICAuYWRkQ2xhc3MoJ2NvbGxhcHNlZCcpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgaWYgKCRwYXJlbnRXcmFwcGVyLmhhc0NsYXNzKCdjb2xsYXBzZWQnKSkge1xuICAgICAgJHBhcmVudFdyYXBwZXJcbiAgICAgICAgLnJlbW92ZUNsYXNzKCdjb2xsYXBzZWQnKVxuICAgICAgICAuYWRkQ2xhc3MoJ2V4cGFuZGVkJyk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIENvbGxhcHNlIG9yIGV4cGFuZCB3aG9sZSB0cmVlXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkYWN0aW9uXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdG9nZ2xlVHJlZSgkYWN0aW9uKSB7XG4gICAgY29uc3QgJHBhcmVudENvbnRhaW5lciA9ICRhY3Rpb24uY2xvc2VzdCgnLmpzLWNob2ljZS10cmVlLWNvbnRhaW5lcicpO1xuICAgIGNvbnN0IGFjdGlvbiA9ICRhY3Rpb24uZGF0YSgnYWN0aW9uJyk7XG5cbiAgICAvLyB0b2dnbGUgYWN0aW9uIGNvbmZpZ3VyYXRpb25cbiAgICBjb25zdCBjb25maWcgPSB7XG4gICAgICBhZGRDbGFzczoge1xuICAgICAgICBleHBhbmQ6ICdleHBhbmRlZCcsXG4gICAgICAgIGNvbGxhcHNlOiAnY29sbGFwc2VkJyxcbiAgICAgIH0sXG4gICAgICByZW1vdmVDbGFzczoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZWQnLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZGVkJyxcbiAgICAgIH0sXG4gICAgICBuZXh0QWN0aW9uOiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlJyxcbiAgICAgICAgY29sbGFwc2U6ICdleHBhbmQnLFxuICAgICAgfSxcbiAgICAgIHRleHQ6IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2VkLXRleHQnLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZGVkLXRleHQnLFxuICAgICAgfSxcbiAgICAgIGljb246IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2VkLWljb24nLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZGVkLWljb24nLFxuICAgICAgfVxuICAgIH07XG5cbiAgICAkcGFyZW50Q29udGFpbmVyLmZpbmQoJ2xpJykuZWFjaCgoaW5kZXgsIGl0ZW0pID0+IHtcbiAgICAgIGNvbnN0ICRpdGVtID0gJChpdGVtKTtcblxuICAgICAgaWYgKCRpdGVtLmhhc0NsYXNzKGNvbmZpZy5yZW1vdmVDbGFzc1thY3Rpb25dKSkge1xuICAgICAgICAgICRpdGVtLnJlbW92ZUNsYXNzKGNvbmZpZy5yZW1vdmVDbGFzc1thY3Rpb25dKVxuICAgICAgICAgICAgLmFkZENsYXNzKGNvbmZpZy5hZGRDbGFzc1thY3Rpb25dKTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgICRhY3Rpb24uZGF0YSgnYWN0aW9uJywgY29uZmlnLm5leHRBY3Rpb25bYWN0aW9uXSk7XG4gICAgJGFjdGlvbi5maW5kKCcubWF0ZXJpYWwtaWNvbnMnKS50ZXh0KCRhY3Rpb24uZGF0YShjb25maWcuaWNvblthY3Rpb25dKSk7XG4gICAgJGFjdGlvbi5maW5kKCcuanMtdG9nZ2xlLXRleHQnKS50ZXh0KCRhY3Rpb24uZGF0YShjb25maWcudGV4dFthY3Rpb25dKSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZm9ybS9jaG9pY2UtdHJlZS5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbi8qKlxuICogRGVmaW5lcyBhbGwgc2VsZWN0b3JzIHRoYXQgYXJlIHVzZWQgaW4gY3VycmVuY3kgYWRkL2VkaXQgZm9ybS5cbiAqL1xuZXhwb3J0IGRlZmF1bHQge1xuICBjdXJyZW5jeUZvcm06ICcjY3VycmVuY3lfZm9ybScsXG4gIGN1cnJlbmN5U2VsZWN0b3I6ICcjY3VycmVuY3lfc2VsZWN0ZWRfaXNvX2NvZGUnLFxuICBpc1Vub2ZmaWNpYWxDaGVja2JveDogJyNjdXJyZW5jeV91bm9mZmljaWFsJyxcbiAgbmFtZXNJbnB1dDogbGFuZ0lkID0+IGAjY3VycmVuY3lfbmFtZXNfJHtsYW5nSWR9YCxcbiAgc3ltYm9sc0lucHV0OiBsYW5nSWQgPT4gYCNjdXJyZW5jeV9zeW1ib2xzXyR7bGFuZ0lkfWAsXG4gIGlzb0NvZGVJbnB1dDogJyNjdXJyZW5jeV9pc29fY29kZScsXG4gIGV4Y2hhbmdlUmF0ZUlucHV0OiAnI2N1cnJlbmN5X2V4Y2hhbmdlX3JhdGUnLFxuICByZXNldERlZmF1bHRTZXR0aW5nc0lucHV0OiAnI2N1cnJlbmN5X3Jlc2V0X2RlZmF1bHRfc2V0dGluZ3MnLFxuICBsb2FkaW5nRGF0YU1vZGFsOiAnI2N1cnJlbmN5X2xvYWRpbmdfZGF0YV9tb2RhbCcsXG4gIHByZWNpc2lvbklucHV0OiAnI2N1cnJlbmN5X3ByZWNpc2lvbicsXG4gIHNob3BBc3NvY2lhdGlvblRyZWU6ICcjY3VycmVuY3lfc2hvcF9hc3NvY2lhdGlvbicsXG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9jdXJyZW5jeS9mb3JtL2N1cnJlbmN5LWZvcm0tbWFwLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IHsgc2hvd0dyb3dsIH0gZnJvbSAnLi4vLi4vLi4vYXBwL3V0aWxzL2dyb3dsJztcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ3VycmVuY3lGb3JtIHtcbiAgLyoqXG4gICAqIEBwYXJhbSB7b2JqZWN0fSBjdXJyZW5jeUZvcm1NYXAgLSBQYWdlIG1hcFxuICAgKi9cbiAgY29uc3RydWN0b3IoY3VycmVuY3lGb3JtTWFwKSB7XG4gICAgdGhpcy5tYXAgPSBjdXJyZW5jeUZvcm1NYXA7XG4gICAgdGhpcy4kY3VycmVuY3lGb3JtID0gJCh0aGlzLm1hcC5jdXJyZW5jeUZvcm0pO1xuICAgIHRoaXMuZ2V0Q0xEUkRhdGFVcmwgPSB0aGlzLiRjdXJyZW5jeUZvcm0uZGF0YSgnZ2V0LWNsZHItZGF0YScpO1xuICAgIHRoaXMuJGN1cnJlbmN5U2VsZWN0b3IgPSAkKHRoaXMubWFwLmN1cnJlbmN5U2VsZWN0b3IpO1xuICAgIHRoaXMuJGlzVW5vZmZpY2lhbENoZWNrYm94ID0gJCh0aGlzLm1hcC5pc1Vub2ZmaWNpYWxDaGVja2JveCk7XG4gICAgdGhpcy4kaXNvQ29kZUlucHV0ID0gJCh0aGlzLm1hcC5pc29Db2RlSW5wdXQpO1xuICAgIHRoaXMuJGV4Y2hhbmdlUmF0ZUlucHV0ID0gJCh0aGlzLm1hcC5leGNoYW5nZVJhdGVJbnB1dCk7XG4gICAgdGhpcy4kcHJlY2lzaW9uSW5wdXQgPSAkKHRoaXMubWFwLnByZWNpc2lvbklucHV0KTtcbiAgICB0aGlzLiRyZXNldERlZmF1bHRTZXR0aW5nc0J1dHRvbiA9ICQodGhpcy5tYXAucmVzZXREZWZhdWx0U2V0dGluZ3NJbnB1dCk7XG4gICAgdGhpcy4kbG9hZGluZ0RhdGFNb2RhbCA9ICQodGhpcy5tYXAubG9hZGluZ0RhdGFNb2RhbCk7XG4gIH1cblxuICBpbml0KCkge1xuICAgIHRoaXMuX2luaXRMaXN0ZW5lcnMoKTtcbiAgICB0aGlzLl9pbml0RmllbGRzKCk7XG4gIH1cblxuICBfaW5pdExpc3RlbmVycygpIHtcbiAgICB0aGlzLiRjdXJyZW5jeVNlbGVjdG9yLmNoYW5nZSh0aGlzLl9vbkN1cnJlbmN5U2VsZWN0b3JDaGFuZ2UuYmluZCh0aGlzKSk7XG4gICAgdGhpcy4kaXNVbm9mZmljaWFsQ2hlY2tib3guY2hhbmdlKHRoaXMuX29uSXNVbm9mZmljaWFsQ2hlY2tib3hDaGFuZ2UuYmluZCh0aGlzKSk7XG4gICAgdGhpcy4kcmVzZXREZWZhdWx0U2V0dGluZ3NCdXR0b24uY2xpY2sodGhpcy5fb25SZXNldERlZmF1bHRTZXR0aW5nc0NsaWNrLmJpbmQodGhpcykpO1xuICB9XG5cbiAgX2luaXRGaWVsZHMoKSB7XG4gICAgaWYgKCF0aGlzLl9pc1Vub2ZmaWNpYWxDdXJyZW5jeSgpKSB7XG4gICAgICB0aGlzLiRpc1Vub2ZmaWNpYWxDaGVja2JveC5wcm9wKCdjaGVja2VkJywgZmFsc2UpO1xuICAgICAgdGhpcy4kaXNvQ29kZUlucHV0LnByb3AoJ3JlYWRvbmx5JywgdHJ1ZSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMuJGN1cnJlbmN5U2VsZWN0b3IudmFsKCcnKTtcbiAgICAgIHRoaXMuJGlzb0NvZGVJbnB1dC5wcm9wKCdyZWFkb25seScsIGZhbHNlKTtcbiAgICB9XG4gIH1cblxuICBfb25DdXJyZW5jeVNlbGVjdG9yQ2hhbmdlKCkge1xuICAgIGNvbnN0IHNlbGVjdGVkSVNPQ29kZSA9IHRoaXMuJGN1cnJlbmN5U2VsZWN0b3IudmFsKCk7XG4gICAgaWYgKCcnICE9PSBzZWxlY3RlZElTT0NvZGUpIHtcbiAgICAgIHRoaXMuJGlzVW5vZmZpY2lhbENoZWNrYm94LnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG4gICAgICB0aGlzLiRpc29Db2RlSW5wdXQucHJvcCgncmVhZG9ubHknLCB0cnVlKTtcbiAgICAgIHRoaXMuX3Jlc2V0Q3VycmVuY3lEYXRhKHNlbGVjdGVkSVNPQ29kZSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMuJGlzVW5vZmZpY2lhbENoZWNrYm94LnByb3AoJ2NoZWNrZWQnLCB0cnVlKTtcbiAgICAgIHRoaXMuJGlzb0NvZGVJbnB1dC5wcm9wKCdyZWFkb25seScsIGZhbHNlKTtcbiAgICB9XG4gIH1cblxuICBfaXNVbm9mZmljaWFsQ3VycmVuY3koKSB7XG4gICAgaWYgKCdoaWRkZW4nID09PSB0aGlzLiRpc1Vub2ZmaWNpYWxDaGVja2JveC5wcm9wKCd0eXBlJykpIHtcbiAgICAgIHJldHVybiAnMScgPT09IHRoaXMuJGlzVW5vZmZpY2lhbENoZWNrYm94LmF0dHIoJ3ZhbHVlJyk7XG4gICAgfVxuXG4gICAgcmV0dXJuIHRoaXMuJGlzVW5vZmZpY2lhbENoZWNrYm94LnByb3AoJ2NoZWNrZWQnKTtcbiAgfVxuXG4gIF9vbklzVW5vZmZpY2lhbENoZWNrYm94Q2hhbmdlKCkge1xuICAgIGlmICh0aGlzLl9pc1Vub2ZmaWNpYWxDdXJyZW5jeSgpKSB7XG4gICAgICB0aGlzLiRjdXJyZW5jeVNlbGVjdG9yLnZhbCgnJyk7XG4gICAgICB0aGlzLiRpc29Db2RlSW5wdXQucHJvcCgncmVhZG9ubHknLCBmYWxzZSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMuJGlzb0NvZGVJbnB1dC5wcm9wKCdyZWFkb25seScsIHRydWUpO1xuICAgIH1cbiAgfVxuXG4gIF9vblJlc2V0RGVmYXVsdFNldHRpbmdzQ2xpY2soKSB7XG4gICAgdGhpcy5fcmVzZXRDdXJyZW5jeURhdGEodGhpcy4kaXNvQ29kZUlucHV0LnZhbCgpKTtcbiAgfVxuXG4gIF9yZXNldEN1cnJlbmN5RGF0YShzZWxlY3RlZElTT0NvZGUpIHtcbiAgICB0aGlzLiRsb2FkaW5nRGF0YU1vZGFsLm1vZGFsKCdzaG93Jyk7XG4gICAgdGhpcy4kcmVzZXREZWZhdWx0U2V0dGluZ3NCdXR0b24uYWRkQ2xhc3MoJ3NwaW5uZXInKTtcbiAgICBjb25zdCBnZXRDdXJyZW5jeURhdGEgPSB0aGlzLmdldENMRFJEYXRhVXJsLnJlcGxhY2UoJ0NVUlJFTkNZX0lTT19DT0RFJywgc2VsZWN0ZWRJU09Db2RlKTtcbiAgICAkLmdldChnZXRDdXJyZW5jeURhdGEpXG4gICAgICAudGhlbigoY3VycmVuY3lEYXRhKSA9PiB7XG4gICAgICAgIGZvciAobGV0IGxhbmdJZCBpbiBjdXJyZW5jeURhdGEubmFtZXMpIHtcbiAgICAgICAgICBsZXQgbGFuZ05hbWVTZWxlY3RvciA9IHRoaXMubWFwLm5hbWVzSW5wdXQobGFuZ0lkKTtcbiAgICAgICAgICAkKGxhbmdOYW1lU2VsZWN0b3IpLnZhbChjdXJyZW5jeURhdGEubmFtZXNbbGFuZ0lkXSk7XG4gICAgICAgIH1cbiAgICAgICAgZm9yIChsZXQgbGFuZ0lkIGluIGN1cnJlbmN5RGF0YS5zeW1ib2xzKSB7XG4gICAgICAgICAgbGV0IGxhbmdTeW1ib2xTZWxlY3RvciA9IHRoaXMubWFwLnN5bWJvbHNJbnB1dChsYW5nSWQpO1xuICAgICAgICAgICQobGFuZ1N5bWJvbFNlbGVjdG9yKS52YWwoY3VycmVuY3lEYXRhLnN5bWJvbHNbbGFuZ0lkXSk7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy4kaXNvQ29kZUlucHV0LnZhbChjdXJyZW5jeURhdGEuaXNvQ29kZSk7XG4gICAgICAgIGlmIChjdXJyZW5jeURhdGEuZXhjaGFuZ2VSYXRlKSB7XG4gICAgICAgICAgdGhpcy4kZXhjaGFuZ2VSYXRlSW5wdXQudmFsKGN1cnJlbmN5RGF0YS5leGNoYW5nZVJhdGUpO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMuJHByZWNpc2lvbklucHV0LnZhbChjdXJyZW5jeURhdGEucHJlY2lzaW9uKTtcbiAgICAgIH0pXG4gICAgICAuZmFpbCgoY3VycmVuY3lEYXRhKSA9PiB7XG4gICAgICAgIGxldCBlcnJvck1lc3NhZ2UgPSAnQ2FuIG5vdCBmaW5kIENMRFIgZGF0YSBmb3IgY3VycmVuY3kgJyArIHNlbGVjdGVkSVNPQ29kZTtcbiAgICAgICAgaWYgKGN1cnJlbmN5RGF0YSAmJiBjdXJyZW5jeURhdGEucmVzcG9uc2VKU09OICYmIGN1cnJlbmN5RGF0YS5yZXNwb25zZUpTT04uZXJyb3IpIHtcbiAgICAgICAgICBlcnJvck1lc3NhZ2UgPSBjdXJyZW5jeURhdGEucmVzcG9uc2VKU09OLmVycm9yO1xuICAgICAgICB9XG4gICAgICAgIHNob3dHcm93bCgnZXJyb3InLCBlcnJvck1lc3NhZ2UsIDMwMDApO1xuICAgICAgfSlcbiAgICAgIC5hbHdheXMoKCkgPT4ge1xuICAgICAgICB0aGlzLiRsb2FkaW5nRGF0YU1vZGFsLm1vZGFsKCdoaWRlJyk7XG4gICAgICAgIHRoaXMuJHJlc2V0RGVmYXVsdFNldHRpbmdzQnV0dG9uLnJlbW92ZUNsYXNzKCdzcGlubmVyJyk7XG4gICAgICB9KVxuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9jdXJyZW5jeS9mb3JtL2N1cnJlbmN5LWZvcm0uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgQ2hvaWNlVHJlZSBmcm9tICcuLi8uLi8uLi9jb21wb25lbnRzL2Zvcm0vY2hvaWNlLXRyZWUnO1xuaW1wb3J0IFRyYW5zbGF0YWJsZUlucHV0IGZyb20gJy4uLy4uLy4uL2NvbXBvbmVudHMvdHJhbnNsYXRhYmxlLWlucHV0JztcbmltcG9ydCBjdXJyZW5jeUZvcm1NYXAgZnJvbSBcIi4vY3VycmVuY3ktZm9ybS1tYXBcIjtcbmltcG9ydCBDdXJyZW5jeUZvcm0gZnJvbSAnLi9jdXJyZW5jeS1mb3JtJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKCgpID0+IHtcbiAgbmV3IFRyYW5zbGF0YWJsZUlucHV0KCk7XG4gIGNvbnN0IGNob2ljZVRyZWUgPSBuZXcgQ2hvaWNlVHJlZShjdXJyZW5jeUZvcm1NYXAuc2hvcEFzc29jaWF0aW9uVHJlZSk7XG4gIGNob2ljZVRyZWUuZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4oKTtcbiAgY29uc3QgY3VycmVuY3lGb3JtID0gbmV3IEN1cnJlbmN5Rm9ybShjdXJyZW5jeUZvcm1NYXApO1xuICBjdXJyZW5jeUZvcm0uaW5pdCgpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9jdXJyZW5jeS9mb3JtL2luZGV4LmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cbmV4cG9ydCBmdW5jdGlvbiBzaG93R3Jvd2wodHlwZSwgbWVzc2FnZSwgZHVyYXRpb24pIHtcbiAgZHVyYXRpb24gPSB1bmRlZmluZWQgIT09IGR1cmF0aW9uID8gZHVyYXRpb24gOiAyMDAwO1xuICB3aW5kb3cuJC5ncm93bFt0eXBlXSh7XG4gICAgdGl0bGU6ICcnLFxuICAgIHNpemU6IFwibGFyZ2VcIixcbiAgICBtZXNzYWdlOiBtZXNzYWdlLFxuICAgIGR1cmF0aW9uOiBkdXJhdGlvblxuICB9KTtcbn07XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9hcHAvdXRpbHMvZ3Jvd2wuanMiXSwic291cmNlUm9vdCI6IiJ9