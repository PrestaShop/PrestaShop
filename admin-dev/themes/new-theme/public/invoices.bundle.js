window["invoices"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 331);
/******/ })
/************************************************************************/
/******/ ({

/***/ 1:
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || Function("return this")() || (1,eval)("this");
} catch(e) {
	// This works if the window reference is available
	if(typeof window === "object")
		g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ 16:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * 2007-2019 PrestaShop and Contributors
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

var _eventEmitter = __webpack_require__(18);

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

/***/ 18:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.EventEmitter = undefined;

var _events = __webpack_require__(20);

var _events2 = _interopRequireDefault(_events);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * We instanciate one EventEmitter (restricted via a const) so that every components
 * register/dispatch on the same one and can communicate with each other.
 */
var EventEmitter = exports.EventEmitter = new _events2.default(); /**
                                                                   * 2007-2019 PrestaShop and Contributors
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

/***/ 20:
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

/***/ 331:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _datepicker = __webpack_require__(54);

var _datepicker2 = _interopRequireDefault(_datepicker);

var _translatableInput = __webpack_require__(16);

var _translatableInput2 = _interopRequireDefault(_translatableInput);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * 2007-2019 PrestaShop and Contributors
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
  (0, _datepicker2.default)();
  new _translatableInput2.default();
});

/***/ }),

/***/ 54:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

Object.defineProperty(exports, "__esModule", {
  value: true
});

__webpack_require__(74);

var $ = global.$;

/**
 * Initializes all datetimepickers.
 *
 * 'data-format' attribute is optional to define custom date-time format.
 * If provided format contains time, the time picker appears aside date picker calendar.
 * Default datepicker icons are overridden in css by appending context to corresponding class.
 *
 * Example usage in template:
 *
 * <div class="input-group datepicker">      // .datepicker class is used to select datetimepicker object
 *   <input type="text" class="form-control"
 *     data-format="YYYY-MM-DD HH:mm:ss"     // provide data-format attr in case you need custom format
 *   />
 * </div>
 */
/**
 * 2007-2019 PrestaShop and Contributors
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
var init = function initDatePickers() {
  var datePickers = $('.datepicker input[type="text"]');

  datePickers.each(function (key, picker) {
    $(picker).datetimepicker({
      locale: global.full_language_code,
      format: $(picker).data('format') ? $(picker).data('format') : 'YYYY-MM-DD',
      sideBySide: true,
      icons: {
        up: 'up',
        down: 'down',
        date: 'date',
        time: 'time'
      }
    });
  }).on('dp.show', replaceDatePicker).on('dp.hide', function () {
    $(window).off('resize', replaceDatePicker);
  });

  function replaceDatePicker() {
    var datepicker = $('body').find('.bootstrap-datetimepicker-widget');
    if (datepicker.length <= 0) {
      return;
    }

    var position = datepicker.offset(),
        originalHeight = datepicker.outerHeight(),
        margin = (datepicker.outerHeight(true) - datepicker.outerHeight()) / 2;

    // Move datepicker to the exact same place it was but attached to body
    datepicker.appendTo('body');

    //Height changed because the css from column-filters is not applied any more
    var top = position.top + originalHeight - margin - datepicker.outerHeight();

    datepicker.css({
      position: 'absolute',
      top: top,
      bottom: 'auto',
      left: position.left,
      right: 'auto'
    });

    $(window).on('resize', replaceDatePicker);
  }
};

exports.default = init;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }),

/***/ 74:
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {(function(global) {
  /**
   * Polyfill URLSearchParams
   *
   * Inspired from : https://github.com/WebReflection/url-search-params/blob/master/src/url-search-params.js
   */

  var checkIfIteratorIsSupported = function() {
    try {
      return !!Symbol.iterator;
    } catch (error) {
      return false;
    }
  };


  var iteratorSupported = checkIfIteratorIsSupported();

  var createIterator = function(items) {
    var iterator = {
      next: function() {
        var value = items.shift();
        return { done: value === void 0, value: value };
      }
    };

    if (iteratorSupported) {
      iterator[Symbol.iterator] = function() {
        return iterator;
      };
    }

    return iterator;
  };

  /**
   * Search param name and values should be encoded according to https://url.spec.whatwg.org/#urlencoded-serializing
   * encodeURIComponent() produces the same result except encoding spaces as `%20` instead of `+`.
   */
  var serializeParam = function(value) {
    return encodeURIComponent(value).replace(/%20/g, '+');
  };

  var deserializeParam = function(value) {
    return decodeURIComponent(value).replace(/\+/g, ' ');
  };

  var polyfillURLSearchParams = function() {

    var URLSearchParams = function(searchString) {
      Object.defineProperty(this, '_entries', { writable: true, value: {} });
      var typeofSearchString = typeof searchString;

      if (typeofSearchString === 'undefined') {
        // do nothing
      } else if (typeofSearchString === 'string') {
        if (searchString !== '') {
          this._fromString(searchString);
        }
      } else if (searchString instanceof URLSearchParams) {
        var _this = this;
        searchString.forEach(function(value, name) {
          _this.append(name, value);
        });
      } else if ((searchString !== null) && (typeofSearchString === 'object')) {
        if (Object.prototype.toString.call(searchString) === '[object Array]') {
          for (var i = 0; i < searchString.length; i++) {
            var entry = searchString[i];
            if ((Object.prototype.toString.call(entry) === '[object Array]') || (entry.length !== 2)) {
              this.append(entry[0], entry[1]);
            } else {
              throw new TypeError('Expected [string, any] as entry at index ' + i + ' of URLSearchParams\'s input');
            }
          }
        } else {
          for (var key in searchString) {
            if (searchString.hasOwnProperty(key)) {
              this.append(key, searchString[key]);
            }
          }
        }
      } else {
        throw new TypeError('Unsupported input\'s type for URLSearchParams');
      }
    };

    var proto = URLSearchParams.prototype;

    proto.append = function(name, value) {
      if (name in this._entries) {
        this._entries[name].push(String(value));
      } else {
        this._entries[name] = [String(value)];
      }
    };

    proto.delete = function(name) {
      delete this._entries[name];
    };

    proto.get = function(name) {
      return (name in this._entries) ? this._entries[name][0] : null;
    };

    proto.getAll = function(name) {
      return (name in this._entries) ? this._entries[name].slice(0) : [];
    };

    proto.has = function(name) {
      return (name in this._entries);
    };

    proto.set = function(name, value) {
      this._entries[name] = [String(value)];
    };

    proto.forEach = function(callback, thisArg) {
      var entries;
      for (var name in this._entries) {
        if (this._entries.hasOwnProperty(name)) {
          entries = this._entries[name];
          for (var i = 0; i < entries.length; i++) {
            callback.call(thisArg, entries[i], name, this);
          }
        }
      }
    };

    proto.keys = function() {
      var items = [];
      this.forEach(function(value, name) {
        items.push(name);
      });
      return createIterator(items);
    };

    proto.values = function() {
      var items = [];
      this.forEach(function(value) {
        items.push(value);
      });
      return createIterator(items);
    };

    proto.entries = function() {
      var items = [];
      this.forEach(function(value, name) {
        items.push([name, value]);
      });
      return createIterator(items);
    };

    if (iteratorSupported) {
      proto[Symbol.iterator] = proto.entries;
    }

    proto.toString = function() {
      var searchArray = [];
      this.forEach(function(value, name) {
        searchArray.push(serializeParam(name) + '=' + serializeParam(value));
      });
      return searchArray.join('&');
    };


    global.URLSearchParams = URLSearchParams;
  };

  if (!('URLSearchParams' in global) || (new URLSearchParams('?a=1').toString() !== 'a=1')) {
    polyfillURLSearchParams();
  }

  var proto = URLSearchParams.prototype;

  if (typeof proto.sort !== 'function') {
    proto.sort = function() {
      var _this = this;
      var items = [];
      this.forEach(function(value, name) {
        items.push([name, value]);
        if (!_this._entries) {
          _this.delete(name);
        }
      });
      items.sort(function(a, b) {
        if (a[0] < b[0]) {
          return -1;
        } else if (a[0] > b[0]) {
          return +1;
        } else {
          return 0;
        }
      });
      if (_this._entries) { // force reset because IE keeps keys index
        _this._entries = {};
      }
      for (var i = 0; i < items.length; i++) {
        this.append(items[i][0], items[i][1]);
      }
    };
  }

  if (typeof proto._fromString !== 'function') {
    Object.defineProperty(proto, '_fromString', {
      enumerable: false,
      configurable: false,
      writable: false,
      value: function(searchString) {
        if (this._entries) {
          this._entries = {};
        } else {
          var keys = [];
          this.forEach(function(value, name) {
            keys.push(name);
          });
          for (var i = 0; i < keys.length; i++) {
            this.delete(keys[i]);
          }
        }

        searchString = searchString.replace(/^\?/, '');
        var attributes = searchString.split('&');
        var attribute;
        for (var i = 0; i < attributes.length; i++) {
          attribute = attributes[i].split('=');
          this.append(
            deserializeParam(attribute[0]),
            (attribute.length > 1) ? deserializeParam(attribute[1]) : ''
          );
        }
      }
    });
  }

  // HTMLAnchorElement

})(
  (typeof global !== 'undefined') ? global
    : ((typeof window !== 'undefined') ? window
    : ((typeof self !== 'undefined') ? self : this))
);

(function(global) {
  /**
   * Polyfill URL
   *
   * Inspired from : https://github.com/arv/DOM-URL-Polyfill/blob/master/src/url.js
   */

  var checkIfURLIsSupported = function() {
    try {
      var u = new URL('b', 'http://a');
      u.pathname = 'c%20d';
      return (u.href === 'http://a/c%20d') && u.searchParams;
    } catch (e) {
      return false;
    }
  };


  var polyfillURL = function() {
    var _URL = global.URL;

    var URL = function(url, base) {
      if (typeof url !== 'string') url = String(url);

      // Only create another document if the base is different from current location.
      var doc = document, baseElement;
      if (base && (global.location === void 0 || base !== global.location.href)) {
        doc = document.implementation.createHTMLDocument('');
        baseElement = doc.createElement('base');
        baseElement.href = base;
        doc.head.appendChild(baseElement);
        try {
          if (baseElement.href.indexOf(base) !== 0) throw new Error(baseElement.href);
        } catch (err) {
          throw new Error('URL unable to set base ' + base + ' due to ' + err);
        }
      }

      var anchorElement = doc.createElement('a');
      anchorElement.href = url;
      if (baseElement) {
        doc.body.appendChild(anchorElement);
        anchorElement.href = anchorElement.href; // force href to refresh
      }

      if (anchorElement.protocol === ':' || !/:/.test(anchorElement.href)) {
        throw new TypeError('Invalid URL');
      }

      Object.defineProperty(this, '_anchorElement', {
        value: anchorElement
      });


      // create a linked searchParams which reflect its changes on URL
      var searchParams = new URLSearchParams(this.search);
      var enableSearchUpdate = true;
      var enableSearchParamsUpdate = true;
      var _this = this;
      ['append', 'delete', 'set'].forEach(function(methodName) {
        var method = searchParams[methodName];
        searchParams[methodName] = function() {
          method.apply(searchParams, arguments);
          if (enableSearchUpdate) {
            enableSearchParamsUpdate = false;
            _this.search = searchParams.toString();
            enableSearchParamsUpdate = true;
          }
        };
      });

      Object.defineProperty(this, 'searchParams', {
        value: searchParams,
        enumerable: true
      });

      var search = void 0;
      Object.defineProperty(this, '_updateSearchParams', {
        enumerable: false,
        configurable: false,
        writable: false,
        value: function() {
          if (this.search !== search) {
            search = this.search;
            if (enableSearchParamsUpdate) {
              enableSearchUpdate = false;
              this.searchParams._fromString(this.search);
              enableSearchUpdate = true;
            }
          }
        }
      });
    };

    var proto = URL.prototype;

    var linkURLWithAnchorAttribute = function(attributeName) {
      Object.defineProperty(proto, attributeName, {
        get: function() {
          return this._anchorElement[attributeName];
        },
        set: function(value) {
          this._anchorElement[attributeName] = value;
        },
        enumerable: true
      });
    };

    ['hash', 'host', 'hostname', 'port', 'protocol']
      .forEach(function(attributeName) {
        linkURLWithAnchorAttribute(attributeName);
      });

    Object.defineProperty(proto, 'search', {
      get: function() {
        return this._anchorElement['search'];
      },
      set: function(value) {
        this._anchorElement['search'] = value;
        this._updateSearchParams();
      },
      enumerable: true
    });

    Object.defineProperties(proto, {

      'toString': {
        get: function() {
          var _this = this;
          return function() {
            return _this.href;
          };
        }
      },

      'href': {
        get: function() {
          return this._anchorElement.href.replace(/\?$/, '');
        },
        set: function(value) {
          this._anchorElement.href = value;
          this._updateSearchParams();
        },
        enumerable: true
      },

      'pathname': {
        get: function() {
          return this._anchorElement.pathname.replace(/(^\/?)/, '/');
        },
        set: function(value) {
          this._anchorElement.pathname = value;
        },
        enumerable: true
      },

      'origin': {
        get: function() {
          // get expected port from protocol
          var expectedPort = { 'http:': 80, 'https:': 443, 'ftp:': 21 }[this._anchorElement.protocol];
          // add port to origin if, expected port is different than actual port
          // and it is not empty f.e http://foo:8080
          // 8080 != 80 && 8080 != ''
          var addPortToOrigin = this._anchorElement.port != expectedPort &&
            this._anchorElement.port !== '';

          return this._anchorElement.protocol +
            '//' +
            this._anchorElement.hostname +
            (addPortToOrigin ? (':' + this._anchorElement.port) : '');
        },
        enumerable: true
      },

      'password': { // TODO
        get: function() {
          return '';
        },
        set: function(value) {
        },
        enumerable: true
      },

      'username': { // TODO
        get: function() {
          return '';
        },
        set: function(value) {
        },
        enumerable: true
      },
    });

    URL.createObjectURL = function(blob) {
      return _URL.createObjectURL.apply(_URL, arguments);
    };

    URL.revokeObjectURL = function(url) {
      return _URL.revokeObjectURL.apply(_URL, arguments);
    };

    global.URL = URL;

  };

  if (!checkIfURLIsSupported()) {
    polyfillURL();
  }

  if ((global.location !== void 0) && !('origin' in global.location)) {
    var getOrigin = function() {
      return global.location.protocol + '//' + global.location.hostname + (global.location.port ? (':' + global.location.port) : '');
    };

    try {
      Object.defineProperty(global.location, 'origin', {
        get: getOrigin,
        enumerable: true
      });
    } catch (e) {
      setInterval(function() {
        global.location.origin = getOrigin();
      }, 100);
    }
  }

})(
  (typeof global !== 'undefined') ? global
    : ((typeof window !== 'undefined') ? window
    : ((typeof self !== 'undefined') ? self : this))
);

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODI/M2YxNioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzPzM2OTgqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy90cmFuc2xhdGFibGUtaW5wdXQuanM/MTU5NCoqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZXZlbnQtZW1pdHRlci5qcz8wZTAzKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9ldmVudHMvZXZlbnRzLmpzPzdjNzEqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9pbnZvaWNlcy9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvdXRpbHMvZGF0ZXBpY2tlci5qcz81YzdkIiwid2VicGFjazovLy8uL34vdXJsLXBvbHlmaWxsL3VybC1wb2x5ZmlsbC5qcz8xYTI1Il0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJUcmFuc2xhdGFibGVJbnB1dCIsIm9wdGlvbnMiLCJsb2NhbGVJdGVtU2VsZWN0b3IiLCJsb2NhbGVCdXR0b25TZWxlY3RvciIsImxvY2FsZUlucHV0U2VsZWN0b3IiLCJvbiIsInRvZ2dsZUxhbmd1YWdlIiwiYmluZCIsIkV2ZW50RW1pdHRlciIsInRvZ2dsZUlucHV0cyIsImV2ZW50IiwibG9jYWxlSXRlbSIsInRhcmdldCIsImZvcm0iLCJjbG9zZXN0IiwiZW1pdCIsInNlbGVjdGVkTG9jYWxlIiwiZGF0YSIsImxvY2FsZUJ1dHRvbiIsImZpbmQiLCJjaGFuZ2VMYW5ndWFnZVVybCIsInRleHQiLCJhZGRDbGFzcyIsInJlbW92ZUNsYXNzIiwiX3NhdmVTZWxlY3RlZExhbmd1YWdlIiwicG9zdCIsInVybCIsImxhbmd1YWdlX2lzb19jb2RlIiwiRXZlbnRFbWl0dGVyQ2xhc3MiLCJnbG9iYWwiLCJpbml0IiwiaW5pdERhdGVQaWNrZXJzIiwiZGF0ZVBpY2tlcnMiLCJlYWNoIiwia2V5IiwicGlja2VyIiwiZGF0ZXRpbWVwaWNrZXIiLCJsb2NhbGUiLCJmdWxsX2xhbmd1YWdlX2NvZGUiLCJmb3JtYXQiLCJzaWRlQnlTaWRlIiwiaWNvbnMiLCJ1cCIsImRvd24iLCJkYXRlIiwidGltZSIsInJlcGxhY2VEYXRlUGlja2VyIiwib2ZmIiwiZGF0ZXBpY2tlciIsImxlbmd0aCIsInBvc2l0aW9uIiwib2Zmc2V0Iiwib3JpZ2luYWxIZWlnaHQiLCJvdXRlckhlaWdodCIsIm1hcmdpbiIsImFwcGVuZFRvIiwidG9wIiwiY3NzIiwiYm90dG9tIiwibGVmdCIsInJpZ2h0Il0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7OztBQ2hFQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxDQUFDOztBQUVEO0FBQ0E7QUFDQTtBQUNBLENBQUM7QUFDRDtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0EsNENBQTRDOztBQUU1Qzs7Ozs7Ozs7Ozs7Ozs7O3FqQkNwQkE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkE7Ozs7QUFFQSxJQUFNQSxJQUFJQyxPQUFPRCxDQUFqQjs7QUFFQTs7Ozs7O0lBS01FLGlCO0FBQ0osNkJBQVlDLE9B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QUFFQTtBQUNBO0FBQ0E7O0FBRUE7O0FBRUE7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsT0FBTztBQUNQLEtBQUs7QUFDTDtBQUNBO0FBQ0EsT0FBTztBQUNQO0FBQ0E7O0FBRUEsQ0FBQztBQUNEO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6Imludm9pY2VzLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMzMxKTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCA1ZDk5OTA5NGQxMWFlZmYwYjU4MiIsInZhciBnO1xyXG5cclxuLy8gVGhpcyB3b3JrcyBpbiBub24tc3RyaWN0IG1vZGVcclxuZyA9IChmdW5jdGlvbigpIHtcclxuXHRyZXR1cm4gdGhpcztcclxufSkoKTtcclxuXHJcbnRyeSB7XHJcblx0Ly8gVGhpcyB3b3JrcyBpZiBldmFsIGlzIGFsbG93ZWQgKHNlZSBDU1ApXHJcblx0ZyA9IGcgfHwgRnVuY3Rpb24oXCJyZXR1cm4gdGhpc1wiKSgpIHx8ICgxLGV2YWwpKFwidGhpc1wiKTtcclxufSBjYXRjaChlKSB7XHJcblx0Ly8gVGhpcyB3b3JrcyBpZiB0aGUgd2luZG93IHJlZmVyZW5jZSBpcyBhdmFpbGFibGVcclxuXHRpZih0eXBlb2Ygd2luZG93ID09PSBcIm9iamVjdFwiKVxyXG5cdFx0ZyA9IHdpbmRvdztcclxufVxyXG5cclxuLy8gZyBjYW4gc3RpbGwgYmUgdW5kZWZpbmVkLCBidXQgbm90aGluZyB0byBkbyBhYm91dCBpdC4uLlxyXG4vLyBXZSByZXR1cm4gdW5kZWZpbmVkLCBpbnN0ZWFkIG9mIG5vdGhpbmcgaGVyZSwgc28gaXQnc1xyXG4vLyBlYXNpZXIgdG8gaGFuZGxlIHRoaXMgY2FzZS4gaWYoIWdsb2JhbCkgeyAuLi59XHJcblxyXG5tb2R1bGUuZXhwb3J0cyA9IGc7XHJcblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vICh3ZWJwYWNrKS9idWlsZGluL2dsb2JhbC5qc1xuLy8gbW9kdWxlIGlkID0gMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjUgMzAgMzUiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQge0V2ZW50RW1pdHRlcn0gZnJvbSAnLi9ldmVudC1lbWl0dGVyJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFRoaXMgY2xhc3MgaXMgdXNlZCB0byBhdXRvbWF0aWNhbGx5IHRvZ2dsZSB0cmFuc2xhdGVkIGlucHV0cyAoZGlzcGxheWVkIHdpdGggb25lXG4gKiBpbnB1dCBhbmQgYSBsYW5ndWFnZSBzZWxlY3RvciB1c2luZyB0aGUgVHJhbnNsYXRhYmxlVHlwZSBTeW1mb255IGZvcm0gdHlwZSkuXG4gKiBBbHNvIGNvbXBhdGlibGUgd2l0aCBUcmFuc2xhdGFibGVGaWVsZCBjaGFuZ2VzLlxuICovXG5jbGFzcyBUcmFuc2xhdGFibGVJbnB1dCB7XG4gIGNvbnN0cnVjdG9yKG9wdGlvbnMpIHtcbiAgICBvcHRpb25zID0gb3B0aW9ucyB8fCB7fTtcblxuICAgIHRoaXMubG9jYWxlSXRlbVNlbGVjdG9yID0gb3B0aW9ucy5sb2NhbGVJdGVtU2VsZWN0b3IgfHwgJy5qcy1sb2NhbGUtaXRlbSc7XG4gICAgdGhpcy5sb2NhbGVCdXR0b25TZWxlY3RvciA9IG9wdGlvbnMubG9jYWxlQnV0dG9uU2VsZWN0b3IgfHwgJy5qcy1sb2NhbGUtYnRuJztcbiAgICB0aGlzLmxvY2FsZUlucHV0U2VsZWN0b3IgPSBvcHRpb25zLmxvY2FsZUlucHV0U2VsZWN0b3IgfHwgJy5qcy1sb2NhbGUtaW5wdXQnO1xuXG4gICAgJCgnYm9keScpLm9uKCdjbGljaycsIHRoaXMubG9jYWxlSXRlbVNlbGVjdG9yLCB0aGlzLnRvZ2dsZUxhbmd1YWdlLmJpbmQodGhpcykpO1xuICAgIEV2ZW50RW1pdHRlci5vbignbGFuZ3VhZ2VTZWxlY3RlZCcsIHRoaXMudG9nZ2xlSW5wdXRzLmJpbmQodGhpcykpO1xuICB9XG5cbiAgLyoqXG4gICAqIERpc3BhdGNoIGV2ZW50IG9uIGxhbmd1YWdlIHNlbGVjdGlvbiB0byB1cGRhdGUgaW5wdXRzIGFuZCBvdGhlciBjb21wb25lbnRzIHdoaWNoIGRlcGVuZCBvbiB0aGUgbG9jYWxlLlxuICAgKlxuICAgKiBAcGFyYW0gZXZlbnRcbiAgICovXG4gIHRvZ2dsZUxhbmd1YWdlKGV2ZW50KSB7XG4gICAgY29uc3QgbG9jYWxlSXRlbSA9ICQoZXZlbnQudGFyZ2V0KTtcbiAgICBjb25zdCBmb3JtID0gbG9jYWxlSXRlbS5jbG9zZXN0KCdmb3JtJyk7XG4gICAgRXZlbnRFbWl0dGVyLmVtaXQoJ2xhbmd1YWdlU2VsZWN0ZWQnLCB7c2VsZWN0ZWRMb2NhbGU6IGxvY2FsZUl0ZW0uZGF0YSgnbG9jYWxlJyksIGZvcm06IGZvcm19KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBUb2dnbGUgYWxsIHRyYW5zbGF0YWJsZSBpbnB1dHMgaW4gZm9ybSBpbiB3aGljaCBsb2NhbGUgd2FzIGNoYW5nZWRcbiAgICpcbiAgICogQHBhcmFtIHtFdmVudH0gZXZlbnRcbiAgICovXG4gIHRvZ2dsZUlucHV0cyhldmVudCkge1xuICAgIGNvbnN0IGZvcm0gPSBldmVudC5mb3JtO1xuICAgIGNvbnN0IHNlbGVjdGVkTG9jYWxlID0gZXZlbnQuc2VsZWN0ZWRMb2NhbGU7XG4gICAgY29uc3QgbG9jYWxlQnV0dG9uID0gZm9ybS5maW5kKHRoaXMubG9jYWxlQnV0dG9uU2VsZWN0b3IpO1xuICAgIGNvbnN0IGNoYW5nZUxhbmd1YWdlVXJsID0gbG9jYWxlQnV0dG9uLmRhdGEoJ2NoYW5nZS1sYW5ndWFnZS11cmwnKTtcblxuICAgIGxvY2FsZUJ1dHRvbi50ZXh0KHNlbGVjdGVkTG9jYWxlKTtcbiAgICBmb3JtLmZpbmQodGhpcy5sb2NhbGVJbnB1dFNlbGVjdG9yKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgZm9ybS5maW5kKGAke3RoaXMubG9jYWxlSW5wdXRTZWxlY3Rvcn0uanMtbG9jYWxlLSR7c2VsZWN0ZWRMb2NhbGV9YCkucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuXG4gICAgaWYgKGNoYW5nZUxhbmd1YWdlVXJsKSB7XG4gICAgICB0aGlzLl9zYXZlU2VsZWN0ZWRMYW5ndWFnZShjaGFuZ2VMYW5ndWFnZVVybCwgc2VsZWN0ZWRMb2NhbGUpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBTYXZlIGxhbmd1YWdlIGNob2ljZSBmb3IgZW1wbG95ZWUgZm9ybXMuXG4gICAqXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBjaGFuZ2VMYW5ndWFnZVVybFxuICAgKiBAcGFyYW0ge1N0cmluZ30gc2VsZWN0ZWRMb2NhbGVcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zYXZlU2VsZWN0ZWRMYW5ndWFnZShjaGFuZ2VMYW5ndWFnZVVybCwgc2VsZWN0ZWRMb2NhbGUpIHtcbiAgICAkLnBvc3Qoe1xuICAgICAgdXJsOiBjaGFuZ2VMYW5ndWFnZVVybCxcbiAgICAgIGRhdGE6IHtcbiAgICAgICAgbGFuZ3VhZ2VfaXNvX2NvZGU6IHNlbGVjdGVkTG9jYWxlXG4gICAgICB9LFxuICAgIH0pO1xuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IFRyYW5zbGF0YWJsZUlucHV0O1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy90cmFuc2xhdGFibGUtaW5wdXQuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgRXZlbnRFbWl0dGVyQ2xhc3MgZnJvbSAnZXZlbnRzJztcblxuLyoqXG4gKiBXZSBpbnN0YW5jaWF0ZSBvbmUgRXZlbnRFbWl0dGVyIChyZXN0cmljdGVkIHZpYSBhIGNvbnN0KSBzbyB0aGF0IGV2ZXJ5IGNvbXBvbmVudHNcbiAqIHJlZ2lzdGVyL2Rpc3BhdGNoIG9uIHRoZSBzYW1lIG9uZSBhbmQgY2FuIGNvbW11bmljYXRlIHdpdGggZWFjaCBvdGhlci5cbiAqL1xuZXhwb3J0IGNvbnN0IEV2ZW50RW1pdHRlciA9IG5ldyBFdmVudEVtaXR0ZXJDbGFzcygpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ldmVudC1lbWl0dGVyLmpzIiwiLy8gQ29weXJpZ2h0IEpveWVudCwgSW5jLiBhbmQgb3RoZXIgTm9kZSBjb250cmlidXRvcnMuXG4vL1xuLy8gUGVybWlzc2lvbiBpcyBoZXJlYnkgZ3JhbnRlZCwgZnJlZSBvZiBjaGFyZ2UsIHRvIGFueSBwZXJzb24gb2J0YWluaW5nIGFcbi8vIGNvcHkgb2YgdGhpcyBzb2Z0d2FyZSBhbmQgYXNzb2NpYXRlZCBkb2N1bWVudGF0aW9uIGZpbGVzICh0aGVcbi8vIFwiU29mdHdhcmVcIiksIHRvIGRlYWwgaW4gdGhlIFNvZnR3YXJlIHdpdGhvdXQgcmVzdHJpY3Rpb24sIGluY2x1ZGluZ1xuLy8gd2l0aG91dCBsaW1pdGF0aW9uIHRoZSByaWdodHMgdG8gdXNlLCBjb3B5LCBtb2RpZnksIG1lcmdlLCBwdWJsaXNoLFxuLy8gZGlzdHJpYnV0ZSwgc3VibGljZW5zZSwgYW5kL29yIHNlbGwgY29waWVzIG9mIHRoZSBTb2Z0d2FyZSwgYW5kIHRvIHBlcm1pdFxuLy8gcGVyc29ucyB0byB3aG9tIHRoZSBTb2Z0d2FyZSBpcyBmdXJuaXNoZWQgdG8gZG8gc28sIHN1YmplY3QgdG8gdGhlXG4vLyBmb2xsb3dpbmcgY29uZGl0aW9uczpcbi8vXG4vLyBUaGUgYWJvdmUgY29weXJpZ2h0IG5vdGljZSBhbmQgdGhpcyBwZXJtaXNzaW9uIG5vdGljZSBzaGFsbCBiZSBpbmNsdWRlZFxuLy8gaW4gYWxsIGNvcGllcyBvciBzdWJzdGFudGlhbCBwb3J0aW9ucyBvZiB0aGUgU29mdHdhcmUuXG4vL1xuLy8gVEhFIFNPRlRXQVJFIElTIFBST1ZJREVEIFwiQVMgSVNcIiwgV0lUSE9VVCBXQVJSQU5UWSBPRiBBTlkgS0lORCwgRVhQUkVTU1xuLy8gT1IgSU1QTElFRCwgSU5DTFVESU5HIEJVVCBOT1QgTElNSVRFRCBUTyBUSEUgV0FSUkFOVElFUyBPRlxuLy8gTUVSQ0hBTlRBQklMSVRZLCBGSVRORVNTIEZPUiBBIFBBUlRJQ1VMQVIgUFVSUE9TRSBBTkQgTk9OSU5GUklOR0VNRU5ULiBJTlxuLy8gTk8gRVZFTlQgU0hBTEwgVEhFIEFVVEhPUlMgT1IgQ09QWVJJR0hUIEhPTERFUlMgQkUgTElBQkxFIEZPUiBBTlkgQ0xBSU0sXG4vLyBEQU1BR0VTIE9SIE9USEVSIExJQUJJTElUWSwgV0hFVEhFUiBJTiBBTiBBQ1RJT04gT0YgQ09OVFJBQ1QsIFRPUlQgT1Jcbi8vIE9USEVSV0lTRSwgQVJJU0lORyBGUk9NLCBPVVQgT0YgT1IgSU4gQ09OTkVDVElPTiBXSVRIIFRIRSBTT0ZUV0FSRSBPUiBUSEVcbi8vIFVTRSBPUiBPVEhFUiBERUFMSU5HUyBJTiBUSEUgU09GVFdBUkUuXG5cbid1c2Ugc3RyaWN0JztcblxudmFyIFIgPSB0eXBlb2YgUmVmbGVjdCA9PT0gJ29iamVjdCcgPyBSZWZsZWN0IDogbnVsbFxudmFyIFJlZmxlY3RBcHBseSA9IFIgJiYgdHlwZW9mIFIuYXBwbHkgPT09ICdmdW5jdGlvbidcbiAgPyBSLmFwcGx5XG4gIDogZnVuY3Rpb24gUmVmbGVjdEFwcGx5KHRhcmdldCwgcmVjZWl2ZXIsIGFyZ3MpIHtcbiAgICByZXR1cm4gRnVuY3Rpb24ucHJvdG90eXBlLmFwcGx5LmNhbGwodGFyZ2V0LCByZWNlaXZlciwgYXJncyk7XG4gIH1cblxudmFyIFJlZmxlY3RPd25LZXlzXG5pZiAoUiAmJiB0eXBlb2YgUi5vd25LZXlzID09PSAnZnVuY3Rpb24nKSB7XG4gIFJlZmxlY3RPd25LZXlzID0gUi5vd25LZXlzXG59IGVsc2UgaWYgKE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHMpIHtcbiAgUmVmbGVjdE93bktleXMgPSBmdW5jdGlvbiBSZWZsZWN0T3duS2V5cyh0YXJnZXQpIHtcbiAgICByZXR1cm4gT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXModGFyZ2V0KVxuICAgICAgLmNvbmNhdChPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzKHRhcmdldCkpO1xuICB9O1xufSBlbHNlIHtcbiAgUmVmbGVjdE93bktleXMgPSBmdW5jdGlvbiBSZWZsZWN0T3duS2V5cyh0YXJnZXQpIHtcbiAgICByZXR1cm4gT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXModGFyZ2V0KTtcbiAgfTtcbn1cblxuZnVuY3Rpb24gUHJvY2Vzc0VtaXRXYXJuaW5nKHdhcm5pbmcpIHtcbiAgaWYgKGNvbnNvbGUgJiYgY29uc29sZS53YXJuKSBjb25zb2xlLndhcm4od2FybmluZyk7XG59XG5cbnZhciBOdW1iZXJJc05hTiA9IE51bWJlci5pc05hTiB8fCBmdW5jdGlvbiBOdW1iZXJJc05hTih2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgIT09IHZhbHVlO1xufVxuXG5mdW5jdGlvbiBFdmVudEVtaXR0ZXIoKSB7XG4gIEV2ZW50RW1pdHRlci5pbml0LmNhbGwodGhpcyk7XG59XG5tb2R1bGUuZXhwb3J0cyA9IEV2ZW50RW1pdHRlcjtcblxuLy8gQmFja3dhcmRzLWNvbXBhdCB3aXRoIG5vZGUgMC4xMC54XG5FdmVudEVtaXR0ZXIuRXZlbnRFbWl0dGVyID0gRXZlbnRFbWl0dGVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9ldmVudHMgPSB1bmRlZmluZWQ7XG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9ldmVudHNDb3VudCA9IDA7XG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9tYXhMaXN0ZW5lcnMgPSB1bmRlZmluZWQ7XG5cbi8vIEJ5IGRlZmF1bHQgRXZlbnRFbWl0dGVycyB3aWxsIHByaW50IGEgd2FybmluZyBpZiBtb3JlIHRoYW4gMTAgbGlzdGVuZXJzIGFyZVxuLy8gYWRkZWQgdG8gaXQuIFRoaXMgaXMgYSB1c2VmdWwgZGVmYXVsdCB3aGljaCBoZWxwcyBmaW5kaW5nIG1lbW9yeSBsZWFrcy5cbnZhciBkZWZhdWx0TWF4TGlzdGVuZXJzID0gMTA7XG5cbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShFdmVudEVtaXR0ZXIsICdkZWZhdWx0TWF4TGlzdGVuZXJzJywge1xuICBlbnVtZXJhYmxlOiB0cnVlLFxuICBnZXQ6IGZ1bmN0aW9uKCkge1xuICAgIHJldHVybiBkZWZhdWx0TWF4TGlzdGVuZXJzO1xuICB9LFxuICBzZXQ6IGZ1bmN0aW9uKGFyZykge1xuICAgIGlmICh0eXBlb2YgYXJnICE9PSAnbnVtYmVyJyB8fCBhcmcgPCAwIHx8IE51bWJlcklzTmFOKGFyZykpIHtcbiAgICAgIHRocm93IG5ldyBSYW5nZUVycm9yKCdUaGUgdmFsdWUgb2YgXCJkZWZhdWx0TWF4TGlzdGVuZXJzXCIgaXMgb3V0IG9mIHJhbmdlLiBJdCBtdXN0IGJlIGEgbm9uLW5lZ2F0aXZlIG51bWJlci4gUmVjZWl2ZWQgJyArIGFyZyArICcuJyk7XG4gICAgfVxuICAgIGRlZmF1bHRNYXhMaXN0ZW5lcnMgPSBhcmc7XG4gIH1cbn0pO1xuXG5FdmVudEVtaXR0ZXIuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXG4gIGlmICh0aGlzLl9ldmVudHMgPT09IHVuZGVmaW5lZCB8fFxuICAgICAgdGhpcy5fZXZlbnRzID09PSBPYmplY3QuZ2V0UHJvdG90eXBlT2YodGhpcykuX2V2ZW50cykge1xuICAgIHRoaXMuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgdGhpcy5fZXZlbnRzQ291bnQgPSAwO1xuICB9XG5cbiAgdGhpcy5fbWF4TGlzdGVuZXJzID0gdGhpcy5fbWF4TGlzdGVuZXJzIHx8IHVuZGVmaW5lZDtcbn07XG5cbi8vIE9idmlvdXNseSBub3QgYWxsIEVtaXR0ZXJzIHNob3VsZCBiZSBsaW1pdGVkIHRvIDEwLiBUaGlzIGZ1bmN0aW9uIGFsbG93c1xuLy8gdGhhdCB0byBiZSBpbmNyZWFzZWQuIFNldCB0byB6ZXJvIGZvciB1bmxpbWl0ZWQuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnNldE1heExpc3RlbmVycyA9IGZ1bmN0aW9uIHNldE1heExpc3RlbmVycyhuKSB7XG4gIGlmICh0eXBlb2YgbiAhPT0gJ251bWJlcicgfHwgbiA8IDAgfHwgTnVtYmVySXNOYU4obikpIHtcbiAgICB0aHJvdyBuZXcgUmFuZ2VFcnJvcignVGhlIHZhbHVlIG9mIFwiblwiIGlzIG91dCBvZiByYW5nZS4gSXQgbXVzdCBiZSBhIG5vbi1uZWdhdGl2ZSBudW1iZXIuIFJlY2VpdmVkICcgKyBuICsgJy4nKTtcbiAgfVxuICB0aGlzLl9tYXhMaXN0ZW5lcnMgPSBuO1xuICByZXR1cm4gdGhpcztcbn07XG5cbmZ1bmN0aW9uICRnZXRNYXhMaXN0ZW5lcnModGhhdCkge1xuICBpZiAodGhhdC5fbWF4TGlzdGVuZXJzID09PSB1bmRlZmluZWQpXG4gICAgcmV0dXJuIEV2ZW50RW1pdHRlci5kZWZhdWx0TWF4TGlzdGVuZXJzO1xuICByZXR1cm4gdGhhdC5fbWF4TGlzdGVuZXJzO1xufVxuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmdldE1heExpc3RlbmVycyA9IGZ1bmN0aW9uIGdldE1heExpc3RlbmVycygpIHtcbiAgcmV0dXJuICRnZXRNYXhMaXN0ZW5lcnModGhpcyk7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmVtaXQgPSBmdW5jdGlvbiBlbWl0KHR5cGUpIHtcbiAgdmFyIGFyZ3MgPSBbXTtcbiAgZm9yICh2YXIgaSA9IDE7IGkgPCBhcmd1bWVudHMubGVuZ3RoOyBpKyspIGFyZ3MucHVzaChhcmd1bWVudHNbaV0pO1xuICB2YXIgZG9FcnJvciA9ICh0eXBlID09PSAnZXJyb3InKTtcblxuICB2YXIgZXZlbnRzID0gdGhpcy5fZXZlbnRzO1xuICBpZiAoZXZlbnRzICE9PSB1bmRlZmluZWQpXG4gICAgZG9FcnJvciA9IChkb0Vycm9yICYmIGV2ZW50cy5lcnJvciA9PT0gdW5kZWZpbmVkKTtcbiAgZWxzZSBpZiAoIWRvRXJyb3IpXG4gICAgcmV0dXJuIGZhbHNlO1xuXG4gIC8vIElmIHRoZXJlIGlzIG5vICdlcnJvcicgZXZlbnQgbGlzdGVuZXIgdGhlbiB0aHJvdy5cbiAgaWYgKGRvRXJyb3IpIHtcbiAgICB2YXIgZXI7XG4gICAgaWYgKGFyZ3MubGVuZ3RoID4gMClcbiAgICAgIGVyID0gYXJnc1swXTtcbiAgICBpZiAoZXIgaW5zdGFuY2VvZiBFcnJvcikge1xuICAgICAgLy8gTm90ZTogVGhlIGNvbW1lbnRzIG9uIHRoZSBgdGhyb3dgIGxpbmVzIGFyZSBpbnRlbnRpb25hbCwgdGhleSBzaG93XG4gICAgICAvLyB1cCBpbiBOb2RlJ3Mgb3V0cHV0IGlmIHRoaXMgcmVzdWx0cyBpbiBhbiB1bmhhbmRsZWQgZXhjZXB0aW9uLlxuICAgICAgdGhyb3cgZXI7IC8vIFVuaGFuZGxlZCAnZXJyb3InIGV2ZW50XG4gICAgfVxuICAgIC8vIEF0IGxlYXN0IGdpdmUgc29tZSBraW5kIG9mIGNvbnRleHQgdG8gdGhlIHVzZXJcbiAgICB2YXIgZXJyID0gbmV3IEVycm9yKCdVbmhhbmRsZWQgZXJyb3IuJyArIChlciA/ICcgKCcgKyBlci5tZXNzYWdlICsgJyknIDogJycpKTtcbiAgICBlcnIuY29udGV4dCA9IGVyO1xuICAgIHRocm93IGVycjsgLy8gVW5oYW5kbGVkICdlcnJvcicgZXZlbnRcbiAgfVxuXG4gIHZhciBoYW5kbGVyID0gZXZlbnRzW3R5cGVdO1xuXG4gIGlmIChoYW5kbGVyID09PSB1bmRlZmluZWQpXG4gICAgcmV0dXJuIGZhbHNlO1xuXG4gIGlmICh0eXBlb2YgaGFuZGxlciA9PT0gJ2Z1bmN0aW9uJykge1xuICAgIFJlZmxlY3RBcHBseShoYW5kbGVyLCB0aGlzLCBhcmdzKTtcbiAgfSBlbHNlIHtcbiAgICB2YXIgbGVuID0gaGFuZGxlci5sZW5ndGg7XG4gICAgdmFyIGxpc3RlbmVycyA9IGFycmF5Q2xvbmUoaGFuZGxlciwgbGVuKTtcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IGxlbjsgKytpKVxuICAgICAgUmVmbGVjdEFwcGx5KGxpc3RlbmVyc1tpXSwgdGhpcywgYXJncyk7XG4gIH1cblxuICByZXR1cm4gdHJ1ZTtcbn07XG5cbmZ1bmN0aW9uIF9hZGRMaXN0ZW5lcih0YXJnZXQsIHR5cGUsIGxpc3RlbmVyLCBwcmVwZW5kKSB7XG4gIHZhciBtO1xuICB2YXIgZXZlbnRzO1xuICB2YXIgZXhpc3Rpbmc7XG5cbiAgaWYgKHR5cGVvZiBsaXN0ZW5lciAhPT0gJ2Z1bmN0aW9uJykge1xuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ1RoZSBcImxpc3RlbmVyXCIgYXJndW1lbnQgbXVzdCBiZSBvZiB0eXBlIEZ1bmN0aW9uLiBSZWNlaXZlZCB0eXBlICcgKyB0eXBlb2YgbGlzdGVuZXIpO1xuICB9XG5cbiAgZXZlbnRzID0gdGFyZ2V0Ll9ldmVudHM7XG4gIGlmIChldmVudHMgPT09IHVuZGVmaW5lZCkge1xuICAgIGV2ZW50cyA9IHRhcmdldC5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICB0YXJnZXQuX2V2ZW50c0NvdW50ID0gMDtcbiAgfSBlbHNlIHtcbiAgICAvLyBUbyBhdm9pZCByZWN1cnNpb24gaW4gdGhlIGNhc2UgdGhhdCB0eXBlID09PSBcIm5ld0xpc3RlbmVyXCIhIEJlZm9yZVxuICAgIC8vIGFkZGluZyBpdCB0byB0aGUgbGlzdGVuZXJzLCBmaXJzdCBlbWl0IFwibmV3TGlzdGVuZXJcIi5cbiAgICBpZiAoZXZlbnRzLm5ld0xpc3RlbmVyICE9PSB1bmRlZmluZWQpIHtcbiAgICAgIHRhcmdldC5lbWl0KCduZXdMaXN0ZW5lcicsIHR5cGUsXG4gICAgICAgICAgICAgICAgICBsaXN0ZW5lci5saXN0ZW5lciA/IGxpc3RlbmVyLmxpc3RlbmVyIDogbGlzdGVuZXIpO1xuXG4gICAgICAvLyBSZS1hc3NpZ24gYGV2ZW50c2AgYmVjYXVzZSBhIG5ld0xpc3RlbmVyIGhhbmRsZXIgY291bGQgaGF2ZSBjYXVzZWQgdGhlXG4gICAgICAvLyB0aGlzLl9ldmVudHMgdG8gYmUgYXNzaWduZWQgdG8gYSBuZXcgb2JqZWN0XG4gICAgICBldmVudHMgPSB0YXJnZXQuX2V2ZW50cztcbiAgICB9XG4gICAgZXhpc3RpbmcgPSBldmVudHNbdHlwZV07XG4gIH1cblxuICBpZiAoZXhpc3RpbmcgPT09IHVuZGVmaW5lZCkge1xuICAgIC8vIE9wdGltaXplIHRoZSBjYXNlIG9mIG9uZSBsaXN0ZW5lci4gRG9uJ3QgbmVlZCB0aGUgZXh0cmEgYXJyYXkgb2JqZWN0LlxuICAgIGV4aXN0aW5nID0gZXZlbnRzW3R5cGVdID0gbGlzdGVuZXI7XG4gICAgKyt0YXJnZXQuX2V2ZW50c0NvdW50O1xuICB9IGVsc2Uge1xuICAgIGlmICh0eXBlb2YgZXhpc3RpbmcgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgIC8vIEFkZGluZyB0aGUgc2Vjb25kIGVsZW1lbnQsIG5lZWQgdG8gY2hhbmdlIHRvIGFycmF5LlxuICAgICAgZXhpc3RpbmcgPSBldmVudHNbdHlwZV0gPVxuICAgICAgICBwcmVwZW5kID8gW2xpc3RlbmVyLCBleGlzdGluZ10gOiBbZXhpc3RpbmcsIGxpc3RlbmVyXTtcbiAgICAgIC8vIElmIHdlJ3ZlIGFscmVhZHkgZ290IGFuIGFycmF5LCBqdXN0IGFwcGVuZC5cbiAgICB9IGVsc2UgaWYgKHByZXBlbmQpIHtcbiAgICAgIGV4aXN0aW5nLnVuc2hpZnQobGlzdGVuZXIpO1xuICAgIH0gZWxzZSB7XG4gICAgICBleGlzdGluZy5wdXNoKGxpc3RlbmVyKTtcbiAgICB9XG5cbiAgICAvLyBDaGVjayBmb3IgbGlzdGVuZXIgbGVha1xuICAgIG0gPSAkZ2V0TWF4TGlzdGVuZXJzKHRhcmdldCk7XG4gICAgaWYgKG0gPiAwICYmIGV4aXN0aW5nLmxlbmd0aCA+IG0gJiYgIWV4aXN0aW5nLndhcm5lZCkge1xuICAgICAgZXhpc3Rpbmcud2FybmVkID0gdHJ1ZTtcbiAgICAgIC8vIE5vIGVycm9yIGNvZGUgZm9yIHRoaXMgc2luY2UgaXQgaXMgYSBXYXJuaW5nXG4gICAgICAvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgbm8tcmVzdHJpY3RlZC1zeW50YXhcbiAgICAgIHZhciB3ID0gbmV3IEVycm9yKCdQb3NzaWJsZSBFdmVudEVtaXR0ZXIgbWVtb3J5IGxlYWsgZGV0ZWN0ZWQuICcgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICBleGlzdGluZy5sZW5ndGggKyAnICcgKyBTdHJpbmcodHlwZSkgKyAnIGxpc3RlbmVycyAnICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgJ2FkZGVkLiBVc2UgZW1pdHRlci5zZXRNYXhMaXN0ZW5lcnMoKSB0byAnICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgJ2luY3JlYXNlIGxpbWl0Jyk7XG4gICAgICB3Lm5hbWUgPSAnTWF4TGlzdGVuZXJzRXhjZWVkZWRXYXJuaW5nJztcbiAgICAgIHcuZW1pdHRlciA9IHRhcmdldDtcbiAgICAgIHcudHlwZSA9IHR5cGU7XG4gICAgICB3LmNvdW50ID0gZXhpc3RpbmcubGVuZ3RoO1xuICAgICAgUHJvY2Vzc0VtaXRXYXJuaW5nKHcpO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiB0YXJnZXQ7XG59XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuYWRkTGlzdGVuZXIgPSBmdW5jdGlvbiBhZGRMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcikge1xuICByZXR1cm4gX2FkZExpc3RlbmVyKHRoaXMsIHR5cGUsIGxpc3RlbmVyLCBmYWxzZSk7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9uID0gRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5hZGRMaXN0ZW5lcjtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5wcmVwZW5kTGlzdGVuZXIgPVxuICAgIGZ1bmN0aW9uIHByZXBlbmRMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcikge1xuICAgICAgcmV0dXJuIF9hZGRMaXN0ZW5lcih0aGlzLCB0eXBlLCBsaXN0ZW5lciwgdHJ1ZSk7XG4gICAgfTtcblxuZnVuY3Rpb24gb25jZVdyYXBwZXIoKSB7XG4gIHZhciBhcmdzID0gW107XG4gIGZvciAodmFyIGkgPSAwOyBpIDwgYXJndW1lbnRzLmxlbmd0aDsgaSsrKSBhcmdzLnB1c2goYXJndW1lbnRzW2ldKTtcbiAgaWYgKCF0aGlzLmZpcmVkKSB7XG4gICAgdGhpcy50YXJnZXQucmVtb3ZlTGlzdGVuZXIodGhpcy50eXBlLCB0aGlzLndyYXBGbik7XG4gICAgdGhpcy5maXJlZCA9IHRydWU7XG4gICAgUmVmbGVjdEFwcGx5KHRoaXMubGlzdGVuZXIsIHRoaXMudGFyZ2V0LCBhcmdzKTtcbiAgfVxufVxuXG5mdW5jdGlvbiBfb25jZVdyYXAodGFyZ2V0LCB0eXBlLCBsaXN0ZW5lcikge1xuICB2YXIgc3RhdGUgPSB7IGZpcmVkOiBmYWxzZSwgd3JhcEZuOiB1bmRlZmluZWQsIHRhcmdldDogdGFyZ2V0LCB0eXBlOiB0eXBlLCBsaXN0ZW5lcjogbGlzdGVuZXIgfTtcbiAgdmFyIHdyYXBwZWQgPSBvbmNlV3JhcHBlci5iaW5kKHN0YXRlKTtcbiAgd3JhcHBlZC5saXN0ZW5lciA9IGxpc3RlbmVyO1xuICBzdGF0ZS53cmFwRm4gPSB3cmFwcGVkO1xuICByZXR1cm4gd3JhcHBlZDtcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5vbmNlID0gZnVuY3Rpb24gb25jZSh0eXBlLCBsaXN0ZW5lcikge1xuICBpZiAodHlwZW9mIGxpc3RlbmVyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgdGhyb3cgbmV3IFR5cGVFcnJvcignVGhlIFwibGlzdGVuZXJcIiBhcmd1bWVudCBtdXN0IGJlIG9mIHR5cGUgRnVuY3Rpb24uIFJlY2VpdmVkIHR5cGUgJyArIHR5cGVvZiBsaXN0ZW5lcik7XG4gIH1cbiAgdGhpcy5vbih0eXBlLCBfb25jZVdyYXAodGhpcywgdHlwZSwgbGlzdGVuZXIpKTtcbiAgcmV0dXJuIHRoaXM7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnByZXBlbmRPbmNlTGlzdGVuZXIgPVxuICAgIGZ1bmN0aW9uIHByZXBlbmRPbmNlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXIpIHtcbiAgICAgIGlmICh0eXBlb2YgbGlzdGVuZXIgIT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgdGhyb3cgbmV3IFR5cGVFcnJvcignVGhlIFwibGlzdGVuZXJcIiBhcmd1bWVudCBtdXN0IGJlIG9mIHR5cGUgRnVuY3Rpb24uIFJlY2VpdmVkIHR5cGUgJyArIHR5cGVvZiBsaXN0ZW5lcik7XG4gICAgICB9XG4gICAgICB0aGlzLnByZXBlbmRMaXN0ZW5lcih0eXBlLCBfb25jZVdyYXAodGhpcywgdHlwZSwgbGlzdGVuZXIpKTtcbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH07XG5cbi8vIEVtaXRzIGEgJ3JlbW92ZUxpc3RlbmVyJyBldmVudCBpZiBhbmQgb25seSBpZiB0aGUgbGlzdGVuZXIgd2FzIHJlbW92ZWQuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnJlbW92ZUxpc3RlbmVyID1cbiAgICBmdW5jdGlvbiByZW1vdmVMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcikge1xuICAgICAgdmFyIGxpc3QsIGV2ZW50cywgcG9zaXRpb24sIGksIG9yaWdpbmFsTGlzdGVuZXI7XG5cbiAgICAgIGlmICh0eXBlb2YgbGlzdGVuZXIgIT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgdGhyb3cgbmV3IFR5cGVFcnJvcignVGhlIFwibGlzdGVuZXJcIiBhcmd1bWVudCBtdXN0IGJlIG9mIHR5cGUgRnVuY3Rpb24uIFJlY2VpdmVkIHR5cGUgJyArIHR5cGVvZiBsaXN0ZW5lcik7XG4gICAgICB9XG5cbiAgICAgIGV2ZW50cyA9IHRoaXMuX2V2ZW50cztcbiAgICAgIGlmIChldmVudHMgPT09IHVuZGVmaW5lZClcbiAgICAgICAgcmV0dXJuIHRoaXM7XG5cbiAgICAgIGxpc3QgPSBldmVudHNbdHlwZV07XG4gICAgICBpZiAobGlzdCA9PT0gdW5kZWZpbmVkKVxuICAgICAgICByZXR1cm4gdGhpcztcblxuICAgICAgaWYgKGxpc3QgPT09IGxpc3RlbmVyIHx8IGxpc3QubGlzdGVuZXIgPT09IGxpc3RlbmVyKSB7XG4gICAgICAgIGlmICgtLXRoaXMuX2V2ZW50c0NvdW50ID09PSAwKVxuICAgICAgICAgIHRoaXMuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgICAgIGVsc2Uge1xuICAgICAgICAgIGRlbGV0ZSBldmVudHNbdHlwZV07XG4gICAgICAgICAgaWYgKGV2ZW50cy5yZW1vdmVMaXN0ZW5lcilcbiAgICAgICAgICAgIHRoaXMuZW1pdCgncmVtb3ZlTGlzdGVuZXInLCB0eXBlLCBsaXN0Lmxpc3RlbmVyIHx8IGxpc3RlbmVyKTtcbiAgICAgICAgfVxuICAgICAgfSBlbHNlIGlmICh0eXBlb2YgbGlzdCAhPT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICBwb3NpdGlvbiA9IC0xO1xuXG4gICAgICAgIGZvciAoaSA9IGxpc3QubGVuZ3RoIC0gMTsgaSA+PSAwOyBpLS0pIHtcbiAgICAgICAgICBpZiAobGlzdFtpXSA9PT0gbGlzdGVuZXIgfHwgbGlzdFtpXS5saXN0ZW5lciA9PT0gbGlzdGVuZXIpIHtcbiAgICAgICAgICAgIG9yaWdpbmFsTGlzdGVuZXIgPSBsaXN0W2ldLmxpc3RlbmVyO1xuICAgICAgICAgICAgcG9zaXRpb24gPSBpO1xuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHBvc2l0aW9uIDwgMClcbiAgICAgICAgICByZXR1cm4gdGhpcztcblxuICAgICAgICBpZiAocG9zaXRpb24gPT09IDApXG4gICAgICAgICAgbGlzdC5zaGlmdCgpO1xuICAgICAgICBlbHNlIHtcbiAgICAgICAgICBzcGxpY2VPbmUobGlzdCwgcG9zaXRpb24pO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGxpc3QubGVuZ3RoID09PSAxKVxuICAgICAgICAgIGV2ZW50c1t0eXBlXSA9IGxpc3RbMF07XG5cbiAgICAgICAgaWYgKGV2ZW50cy5yZW1vdmVMaXN0ZW5lciAhPT0gdW5kZWZpbmVkKVxuICAgICAgICAgIHRoaXMuZW1pdCgncmVtb3ZlTGlzdGVuZXInLCB0eXBlLCBvcmlnaW5hbExpc3RlbmVyIHx8IGxpc3RlbmVyKTtcbiAgICAgIH1cblxuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5vZmYgPSBFdmVudEVtaXR0ZXIucHJvdG90eXBlLnJlbW92ZUxpc3RlbmVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnJlbW92ZUFsbExpc3RlbmVycyA9XG4gICAgZnVuY3Rpb24gcmVtb3ZlQWxsTGlzdGVuZXJzKHR5cGUpIHtcbiAgICAgIHZhciBsaXN0ZW5lcnMsIGV2ZW50cywgaTtcblxuICAgICAgZXZlbnRzID0gdGhpcy5fZXZlbnRzO1xuICAgICAgaWYgKGV2ZW50cyA9PT0gdW5kZWZpbmVkKVxuICAgICAgICByZXR1cm4gdGhpcztcblxuICAgICAgLy8gbm90IGxpc3RlbmluZyBmb3IgcmVtb3ZlTGlzdGVuZXIsIG5vIG5lZWQgdG8gZW1pdFxuICAgICAgaWYgKGV2ZW50cy5yZW1vdmVMaXN0ZW5lciA9PT0gdW5kZWZpbmVkKSB7XG4gICAgICAgIGlmIChhcmd1bWVudHMubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICAgICAgICB0aGlzLl9ldmVudHNDb3VudCA9IDA7XG4gICAgICAgIH0gZWxzZSBpZiAoZXZlbnRzW3R5cGVdICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICBpZiAoLS10aGlzLl9ldmVudHNDb3VudCA9PT0gMClcbiAgICAgICAgICAgIHRoaXMuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgICAgICAgZWxzZVxuICAgICAgICAgICAgZGVsZXRlIGV2ZW50c1t0eXBlXTtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICAgIH1cblxuICAgICAgLy8gZW1pdCByZW1vdmVMaXN0ZW5lciBmb3IgYWxsIGxpc3RlbmVycyBvbiBhbGwgZXZlbnRzXG4gICAgICBpZiAoYXJndW1lbnRzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICB2YXIga2V5cyA9IE9iamVjdC5rZXlzKGV2ZW50cyk7XG4gICAgICAgIHZhciBrZXk7XG4gICAgICAgIGZvciAoaSA9IDA7IGkgPCBrZXlzLmxlbmd0aDsgKytpKSB7XG4gICAgICAgICAga2V5ID0ga2V5c1tpXTtcbiAgICAgICAgICBpZiAoa2V5ID09PSAncmVtb3ZlTGlzdGVuZXInKSBjb250aW51ZTtcbiAgICAgICAgICB0aGlzLnJlbW92ZUFsbExpc3RlbmVycyhrZXkpO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMucmVtb3ZlQWxsTGlzdGVuZXJzKCdyZW1vdmVMaXN0ZW5lcicpO1xuICAgICAgICB0aGlzLl9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgICAgICB0aGlzLl9ldmVudHNDb3VudCA9IDA7XG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgICAgfVxuXG4gICAgICBsaXN0ZW5lcnMgPSBldmVudHNbdHlwZV07XG5cbiAgICAgIGlmICh0eXBlb2YgbGlzdGVuZXJzID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIHRoaXMucmVtb3ZlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXJzKTtcbiAgICAgIH0gZWxzZSBpZiAobGlzdGVuZXJzICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgLy8gTElGTyBvcmRlclxuICAgICAgICBmb3IgKGkgPSBsaXN0ZW5lcnMubGVuZ3RoIC0gMTsgaSA+PSAwOyBpLS0pIHtcbiAgICAgICAgICB0aGlzLnJlbW92ZUxpc3RlbmVyKHR5cGUsIGxpc3RlbmVyc1tpXSk7XG4gICAgICAgIH1cbiAgICAgIH1cblxuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfTtcblxuZnVuY3Rpb24gX2xpc3RlbmVycyh0YXJnZXQsIHR5cGUsIHVud3JhcCkge1xuICB2YXIgZXZlbnRzID0gdGFyZ2V0Ll9ldmVudHM7XG5cbiAgaWYgKGV2ZW50cyA9PT0gdW5kZWZpbmVkKVxuICAgIHJldHVybiBbXTtcblxuICB2YXIgZXZsaXN0ZW5lciA9IGV2ZW50c1t0eXBlXTtcbiAgaWYgKGV2bGlzdGVuZXIgPT09IHVuZGVmaW5lZClcbiAgICByZXR1cm4gW107XG5cbiAgaWYgKHR5cGVvZiBldmxpc3RlbmVyID09PSAnZnVuY3Rpb24nKVxuICAgIHJldHVybiB1bndyYXAgPyBbZXZsaXN0ZW5lci5saXN0ZW5lciB8fCBldmxpc3RlbmVyXSA6IFtldmxpc3RlbmVyXTtcblxuICByZXR1cm4gdW53cmFwID9cbiAgICB1bndyYXBMaXN0ZW5lcnMoZXZsaXN0ZW5lcikgOiBhcnJheUNsb25lKGV2bGlzdGVuZXIsIGV2bGlzdGVuZXIubGVuZ3RoKTtcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5saXN0ZW5lcnMgPSBmdW5jdGlvbiBsaXN0ZW5lcnModHlwZSkge1xuICByZXR1cm4gX2xpc3RlbmVycyh0aGlzLCB0eXBlLCB0cnVlKTtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmF3TGlzdGVuZXJzID0gZnVuY3Rpb24gcmF3TGlzdGVuZXJzKHR5cGUpIHtcbiAgcmV0dXJuIF9saXN0ZW5lcnModGhpcywgdHlwZSwgZmFsc2UpO1xufTtcblxuRXZlbnRFbWl0dGVyLmxpc3RlbmVyQ291bnQgPSBmdW5jdGlvbihlbWl0dGVyLCB0eXBlKSB7XG4gIGlmICh0eXBlb2YgZW1pdHRlci5saXN0ZW5lckNvdW50ID09PSAnZnVuY3Rpb24nKSB7XG4gICAgcmV0dXJuIGVtaXR0ZXIubGlzdGVuZXJDb3VudCh0eXBlKTtcbiAgfSBlbHNlIHtcbiAgICByZXR1cm4gbGlzdGVuZXJDb3VudC5jYWxsKGVtaXR0ZXIsIHR5cGUpO1xuICB9XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmxpc3RlbmVyQ291bnQgPSBsaXN0ZW5lckNvdW50O1xuZnVuY3Rpb24gbGlzdGVuZXJDb3VudCh0eXBlKSB7XG4gIHZhciBldmVudHMgPSB0aGlzLl9ldmVudHM7XG5cbiAgaWYgKGV2ZW50cyAhPT0gdW5kZWZpbmVkKSB7XG4gICAgdmFyIGV2bGlzdGVuZXIgPSBldmVudHNbdHlwZV07XG5cbiAgICBpZiAodHlwZW9mIGV2bGlzdGVuZXIgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgIHJldHVybiAxO1xuICAgIH0gZWxzZSBpZiAoZXZsaXN0ZW5lciAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICByZXR1cm4gZXZsaXN0ZW5lci5sZW5ndGg7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIDA7XG59XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuZXZlbnROYW1lcyA9IGZ1bmN0aW9uIGV2ZW50TmFtZXMoKSB7XG4gIHJldHVybiB0aGlzLl9ldmVudHNDb3VudCA+IDAgPyBSZWZsZWN0T3duS2V5cyh0aGlzLl9ldmVudHMpIDogW107XG59O1xuXG5mdW5jdGlvbiBhcnJheUNsb25lKGFyciwgbikge1xuICB2YXIgY29weSA9IG5ldyBBcnJheShuKTtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCBuOyArK2kpXG4gICAgY29weVtpXSA9IGFycltpXTtcbiAgcmV0dXJuIGNvcHk7XG59XG5cbmZ1bmN0aW9uIHNwbGljZU9uZShsaXN0LCBpbmRleCkge1xuICBmb3IgKDsgaW5kZXggKyAxIDwgbGlzdC5sZW5ndGg7IGluZGV4KyspXG4gICAgbGlzdFtpbmRleF0gPSBsaXN0W2luZGV4ICsgMV07XG4gIGxpc3QucG9wKCk7XG59XG5cbmZ1bmN0aW9uIHVud3JhcExpc3RlbmVycyhhcnIpIHtcbiAgdmFyIHJldCA9IG5ldyBBcnJheShhcnIubGVuZ3RoKTtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCByZXQubGVuZ3RoOyArK2kpIHtcbiAgICByZXRbaV0gPSBhcnJbaV0ubGlzdGVuZXIgfHwgYXJyW2ldO1xuICB9XG4gIHJldHVybiByZXQ7XG59XG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vZXZlbnRzL2V2ZW50cy5qc1xuLy8gbW9kdWxlIGlkID0gMjBcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDUgNiA3IDggMTAgMTEgMTIgMjQgMjUgMjkiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgaW5pdERhdGVQaWNrZXJzIGZyb20gJy4uLy4uL2FwcC91dGlscy9kYXRlcGlja2VyJztcbmltcG9ydCBUcmFuc2xhdGFibGVJbnB1dCBmcm9tICcuLi8uLi9jb21wb25lbnRzL3RyYW5zbGF0YWJsZS1pbnB1dCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gICAgaW5pdERhdGVQaWNrZXJzKCk7XG4gICAgbmV3IFRyYW5zbGF0YWJsZUlucHV0KCk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2ludm9pY2VzL2luZGV4LmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cbmltcG9ydCAndXJsLXBvbHlmaWxsJztcblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG4vKipcbiAqIEluaXRpYWxpemVzIGFsbCBkYXRldGltZXBpY2tlcnMuXG4gKlxuICogJ2RhdGEtZm9ybWF0JyBhdHRyaWJ1dGUgaXMgb3B0aW9uYWwgdG8gZGVmaW5lIGN1c3RvbSBkYXRlLXRpbWUgZm9ybWF0LlxuICogSWYgcHJvdmlkZWQgZm9ybWF0IGNvbnRhaW5zIHRpbWUsIHRoZSB0aW1lIHBpY2tlciBhcHBlYXJzIGFzaWRlIGRhdGUgcGlja2VyIGNhbGVuZGFyLlxuICogRGVmYXVsdCBkYXRlcGlja2VyIGljb25zIGFyZSBvdmVycmlkZGVuIGluIGNzcyBieSBhcHBlbmRpbmcgY29udGV4dCB0byBjb3JyZXNwb25kaW5nIGNsYXNzLlxuICpcbiAqIEV4YW1wbGUgdXNhZ2UgaW4gdGVtcGxhdGU6XG4gKlxuICogPGRpdiBjbGFzcz1cImlucHV0LWdyb3VwIGRhdGVwaWNrZXJcIj4gICAgICAvLyAuZGF0ZXBpY2tlciBjbGFzcyBpcyB1c2VkIHRvIHNlbGVjdCBkYXRldGltZXBpY2tlciBvYmplY3RcbiAqICAgPGlucHV0IHR5cGU9XCJ0ZXh0XCIgY2xhc3M9XCJmb3JtLWNvbnRyb2xcIlxuICogICAgIGRhdGEtZm9ybWF0PVwiWVlZWS1NTS1ERCBISDptbTpzc1wiICAgICAvLyBwcm92aWRlIGRhdGEtZm9ybWF0IGF0dHIgaW4gY2FzZSB5b3UgbmVlZCBjdXN0b20gZm9ybWF0XG4gKiAgIC8+XG4gKiA8L2Rpdj5cbiAqL1xuY29uc3QgaW5pdCA9IGZ1bmN0aW9uIGluaXREYXRlUGlja2VycygpIHtcbiAgY29uc3QgZGF0ZVBpY2tlcnMgPSAkKCcuZGF0ZXBpY2tlciBpbnB1dFt0eXBlPVwidGV4dFwiXScpO1xuXG4gIGRhdGVQaWNrZXJzLmVhY2goKGtleSwgcGlja2VyKSA9PiB7XG4gICAgJChwaWNrZXIpLmRhdGV0aW1lcGlja2VyKHtcbiAgICAgIGxvY2FsZTogZ2xvYmFsLmZ1bGxfbGFuZ3VhZ2VfY29kZSxcbiAgICAgIGZvcm1hdDogJChwaWNrZXIpLmRhdGEoJ2Zvcm1hdCcpID8gJChwaWNrZXIpLmRhdGEoJ2Zvcm1hdCcpIDogJ1lZWVktTU0tREQnLFxuICAgICAgc2lkZUJ5U2lkZTogdHJ1ZSxcbiAgICAgIGljb25zOiB7XG4gICAgICAgIHVwOiAndXAnLFxuICAgICAgICBkb3duOiAnZG93bicsXG4gICAgICAgIGRhdGU6ICdkYXRlJyxcbiAgICAgICAgdGltZTogJ3RpbWUnLFxuICAgICAgfSxcbiAgICB9KTtcbiAgfSlcbiAgLm9uKCdkcC5zaG93JywgcmVwbGFjZURhdGVQaWNrZXIpXG4gIC5vbignZHAuaGlkZScsIGZ1bmN0aW9uKCkge1xuICAgICQod2luZG93KS5vZmYoJ3Jlc2l6ZScsIHJlcGxhY2VEYXRlUGlja2VyKTtcbiAgfSk7XG5cbiAgZnVuY3Rpb24gcmVwbGFjZURhdGVQaWNrZXIoKSB7XG4gICAgY29uc3QgZGF0ZXBpY2tlciA9ICQoJ2JvZHknKS5maW5kKCcuYm9vdHN0cmFwLWRhdGV0aW1lcGlja2VyLXdpZGdldCcpO1xuICAgIGlmIChkYXRlcGlja2VyLmxlbmd0aCA8PSAwKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgY29uc3QgcG9zaXRpb24gPSBkYXRlcGlja2VyLm9mZnNldCgpLFxuICAgICAgb3JpZ2luYWxIZWlnaHQgPSBkYXRlcGlja2VyLm91dGVySGVpZ2h0KCksXG4gICAgICBtYXJnaW4gPSAoZGF0ZXBpY2tlci5vdXRlckhlaWdodCh0cnVlKSAtIGRhdGVwaWNrZXIub3V0ZXJIZWlnaHQoKSkgLyAyXG4gICAgO1xuXG4gICAgLy8gTW92ZSBkYXRlcGlja2VyIHRvIHRoZSBleGFjdCBzYW1lIHBsYWNlIGl0IHdhcyBidXQgYXR0YWNoZWQgdG8gYm9keVxuICAgIGRhdGVwaWNrZXIuYXBwZW5kVG8oJ2JvZHknKTtcblxuICAgIC8vSGVpZ2h0IGNoYW5nZWQgYmVjYXVzZSB0aGUgY3NzIGZyb20gY29sdW1uLWZpbHRlcnMgaXMgbm90IGFwcGxpZWQgYW55IG1vcmVcbiAgICBjb25zdCB0b3AgPSBwb3NpdGlvbi50b3AgKyBvcmlnaW5hbEhlaWdodCAtIG1hcmdpbiAtIGRhdGVwaWNrZXIub3V0ZXJIZWlnaHQoKTtcblxuICAgIGRhdGVwaWNrZXIuY3NzKHtcbiAgICAgIHBvc2l0aW9uOiAnYWJzb2x1dGUnLFxuICAgICAgdG9wOiB0b3AsXG4gICAgICBib3R0b206ICdhdXRvJyxcbiAgICAgIGxlZnQ6IHBvc2l0aW9uLmxlZnQsXG4gICAgICByaWdodDogJ2F1dG8nXG4gICAgfSk7XG5cbiAgICAkKHdpbmRvdykub24oJ3Jlc2l6ZScsIHJlcGxhY2VEYXRlUGlja2VyKTtcbiAgfVxufTtcblxuZXhwb3J0IGRlZmF1bHQgaW5pdDtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC91dGlscy9kYXRlcGlja2VyLmpzIiwiKGZ1bmN0aW9uKGdsb2JhbCkge1xyXG4gIC8qKlxyXG4gICAqIFBvbHlmaWxsIFVSTFNlYXJjaFBhcmFtc1xyXG4gICAqXHJcbiAgICogSW5zcGlyZWQgZnJvbSA6IGh0dHBzOi8vZ2l0aHViLmNvbS9XZWJSZWZsZWN0aW9uL3VybC1zZWFyY2gtcGFyYW1zL2Jsb2IvbWFzdGVyL3NyYy91cmwtc2VhcmNoLXBhcmFtcy5qc1xyXG4gICAqL1xyXG5cclxuICB2YXIgY2hlY2tJZkl0ZXJhdG9ySXNTdXBwb3J0ZWQgPSBmdW5jdGlvbigpIHtcclxuICAgIHRyeSB7XHJcbiAgICAgIHJldHVybiAhIVN5bWJvbC5pdGVyYXRvcjtcclxuICAgIH0gY2F0Y2ggKGVycm9yKSB7XHJcbiAgICAgIHJldHVybiBmYWxzZTtcclxuICAgIH1cclxuICB9O1xyXG5cclxuXHJcbiAgdmFyIGl0ZXJhdG9yU3VwcG9ydGVkID0gY2hlY2tJZkl0ZXJhdG9ySXNTdXBwb3J0ZWQoKTtcclxuXHJcbiAgdmFyIGNyZWF0ZUl0ZXJhdG9yID0gZnVuY3Rpb24oaXRlbXMpIHtcclxuICAgIHZhciBpdGVyYXRvciA9IHtcclxuICAgICAgbmV4dDogZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgdmFyIHZhbHVlID0gaXRlbXMuc2hpZnQoKTtcclxuICAgICAgICByZXR1cm4geyBkb25lOiB2YWx1ZSA9PT0gdm9pZCAwLCB2YWx1ZTogdmFsdWUgfTtcclxuICAgICAgfVxyXG4gICAgfTtcclxuXHJcbiAgICBpZiAoaXRlcmF0b3JTdXBwb3J0ZWQpIHtcclxuICAgICAgaXRlcmF0b3JbU3ltYm9sLml0ZXJhdG9yXSA9IGZ1bmN0aW9uKCkge1xyXG4gICAgICAgIHJldHVybiBpdGVyYXRvcjtcclxuICAgICAgfTtcclxuICAgIH1cclxuXHJcbiAgICByZXR1cm4gaXRlcmF0b3I7XHJcbiAgfTtcclxuXHJcbiAgLyoqXHJcbiAgICogU2VhcmNoIHBhcmFtIG5hbWUgYW5kIHZhbHVlcyBzaG91bGQgYmUgZW5jb2RlZCBhY2NvcmRpbmcgdG8gaHR0cHM6Ly91cmwuc3BlYy53aGF0d2cub3JnLyN1cmxlbmNvZGVkLXNlcmlhbGl6aW5nXHJcbiAgICogZW5jb2RlVVJJQ29tcG9uZW50KCkgcHJvZHVjZXMgdGhlIHNhbWUgcmVzdWx0IGV4Y2VwdCBlbmNvZGluZyBzcGFjZXMgYXMgYCUyMGAgaW5zdGVhZCBvZiBgK2AuXHJcbiAgICovXHJcbiAgdmFyIHNlcmlhbGl6ZVBhcmFtID0gZnVuY3Rpb24odmFsdWUpIHtcclxuICAgIHJldHVybiBlbmNvZGVVUklDb21wb25lbnQodmFsdWUpLnJlcGxhY2UoLyUyMC9nLCAnKycpO1xyXG4gIH07XHJcblxyXG4gIHZhciBkZXNlcmlhbGl6ZVBhcmFtID0gZnVuY3Rpb24odmFsdWUpIHtcclxuICAgIHJldHVybiBkZWNvZGVVUklDb21wb25lbnQodmFsdWUpLnJlcGxhY2UoL1xcKy9nLCAnICcpO1xyXG4gIH07XHJcblxyXG4gIHZhciBwb2x5ZmlsbFVSTFNlYXJjaFBhcmFtcyA9IGZ1bmN0aW9uKCkge1xyXG5cclxuICAgIHZhciBVUkxTZWFyY2hQYXJhbXMgPSBmdW5jdGlvbihzZWFyY2hTdHJpbmcpIHtcclxuICAgICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KHRoaXMsICdfZW50cmllcycsIHsgd3JpdGFibGU6IHRydWUsIHZhbHVlOiB7fSB9KTtcclxuICAgICAgdmFyIHR5cGVvZlNlYXJjaFN0cmluZyA9IHR5cGVvZiBzZWFyY2hTdHJpbmc7XHJcblxyXG4gICAgICBpZiAodHlwZW9mU2VhcmNoU3RyaW5nID09PSAndW5kZWZpbmVkJykge1xyXG4gICAgICAgIC8vIGRvIG5vdGhpbmdcclxuICAgICAgfSBlbHNlIGlmICh0eXBlb2ZTZWFyY2hTdHJpbmcgPT09ICdzdHJpbmcnKSB7XHJcbiAgICAgICAgaWYgKHNlYXJjaFN0cmluZyAhPT0gJycpIHtcclxuICAgICAgICAgIHRoaXMuX2Zyb21TdHJpbmcoc2VhcmNoU3RyaW5nKTtcclxuICAgICAgICB9XHJcbiAgICAgIH0gZWxzZSBpZiAoc2VhcmNoU3RyaW5nIGluc3RhbmNlb2YgVVJMU2VhcmNoUGFyYW1zKSB7XHJcbiAgICAgICAgdmFyIF90aGlzID0gdGhpcztcclxuICAgICAgICBzZWFyY2hTdHJpbmcuZm9yRWFjaChmdW5jdGlvbih2YWx1ZSwgbmFtZSkge1xyXG4gICAgICAgICAgX3RoaXMuYXBwZW5kKG5hbWUsIHZhbHVlKTtcclxuICAgICAgICB9KTtcclxuICAgICAgfSBlbHNlIGlmICgoc2VhcmNoU3RyaW5nICE9PSBudWxsKSAmJiAodHlwZW9mU2VhcmNoU3RyaW5nID09PSAnb2JqZWN0JykpIHtcclxuICAgICAgICBpZiAoT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZy5jYWxsKHNlYXJjaFN0cmluZykgPT09ICdbb2JqZWN0IEFycmF5XScpIHtcclxuICAgICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgc2VhcmNoU3RyaW5nLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICAgIHZhciBlbnRyeSA9IHNlYXJjaFN0cmluZ1tpXTtcclxuICAgICAgICAgICAgaWYgKChPYmplY3QucHJvdG90eXBlLnRvU3RyaW5nLmNhbGwoZW50cnkpID09PSAnW29iamVjdCBBcnJheV0nKSB8fCAoZW50cnkubGVuZ3RoICE9PSAyKSkge1xyXG4gICAgICAgICAgICAgIHRoaXMuYXBwZW5kKGVudHJ5WzBdLCBlbnRyeVsxXSk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgdGhyb3cgbmV3IFR5cGVFcnJvcignRXhwZWN0ZWQgW3N0cmluZywgYW55XSBhcyBlbnRyeSBhdCBpbmRleCAnICsgaSArICcgb2YgVVJMU2VhcmNoUGFyYW1zXFwncyBpbnB1dCcpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICB9XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgIGZvciAodmFyIGtleSBpbiBzZWFyY2hTdHJpbmcpIHtcclxuICAgICAgICAgICAgaWYgKHNlYXJjaFN0cmluZy5oYXNPd25Qcm9wZXJ0eShrZXkpKSB7XHJcbiAgICAgICAgICAgICAgdGhpcy5hcHBlbmQoa2V5LCBzZWFyY2hTdHJpbmdba2V5XSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgdGhyb3cgbmV3IFR5cGVFcnJvcignVW5zdXBwb3J0ZWQgaW5wdXRcXCdzIHR5cGUgZm9yIFVSTFNlYXJjaFBhcmFtcycpO1xyXG4gICAgICB9XHJcbiAgICB9O1xyXG5cclxuICAgIHZhciBwcm90byA9IFVSTFNlYXJjaFBhcmFtcy5wcm90b3R5cGU7XHJcblxyXG4gICAgcHJvdG8uYXBwZW5kID0gZnVuY3Rpb24obmFtZSwgdmFsdWUpIHtcclxuICAgICAgaWYgKG5hbWUgaW4gdGhpcy5fZW50cmllcykge1xyXG4gICAgICAgIHRoaXMuX2VudHJpZXNbbmFtZV0ucHVzaChTdHJpbmcodmFsdWUpKTtcclxuICAgICAgfSBlbHNlIHtcclxuICAgICAgICB0aGlzLl9lbnRyaWVzW25hbWVdID0gW1N0cmluZyh2YWx1ZSldO1xyXG4gICAgICB9XHJcbiAgICB9O1xyXG5cclxuICAgIHByb3RvLmRlbGV0ZSA9IGZ1bmN0aW9uKG5hbWUpIHtcclxuICAgICAgZGVsZXRlIHRoaXMuX2VudHJpZXNbbmFtZV07XHJcbiAgICB9O1xyXG5cclxuICAgIHByb3RvLmdldCA9IGZ1bmN0aW9uKG5hbWUpIHtcclxuICAgICAgcmV0dXJuIChuYW1lIGluIHRoaXMuX2VudHJpZXMpID8gdGhpcy5fZW50cmllc1tuYW1lXVswXSA6IG51bGw7XHJcbiAgICB9O1xyXG5cclxuICAgIHByb3RvLmdldEFsbCA9IGZ1bmN0aW9uKG5hbWUpIHtcclxuICAgICAgcmV0dXJuIChuYW1lIGluIHRoaXMuX2VudHJpZXMpID8gdGhpcy5fZW50cmllc1tuYW1lXS5zbGljZSgwKSA6IFtdO1xyXG4gICAgfTtcclxuXHJcbiAgICBwcm90by5oYXMgPSBmdW5jdGlvbihuYW1lKSB7XHJcbiAgICAgIHJldHVybiAobmFtZSBpbiB0aGlzLl9lbnRyaWVzKTtcclxuICAgIH07XHJcblxyXG4gICAgcHJvdG8uc2V0ID0gZnVuY3Rpb24obmFtZSwgdmFsdWUpIHtcclxuICAgICAgdGhpcy5fZW50cmllc1tuYW1lXSA9IFtTdHJpbmcodmFsdWUpXTtcclxuICAgIH07XHJcblxyXG4gICAgcHJvdG8uZm9yRWFjaCA9IGZ1bmN0aW9uKGNhbGxiYWNrLCB0aGlzQXJnKSB7XHJcbiAgICAgIHZhciBlbnRyaWVzO1xyXG4gICAgICBmb3IgKHZhciBuYW1lIGluIHRoaXMuX2VudHJpZXMpIHtcclxuICAgICAgICBpZiAodGhpcy5fZW50cmllcy5oYXNPd25Qcm9wZXJ0eShuYW1lKSkge1xyXG4gICAgICAgICAgZW50cmllcyA9IHRoaXMuX2VudHJpZXNbbmFtZV07XHJcbiAgICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGVudHJpZXMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgY2FsbGJhY2suY2FsbCh0aGlzQXJnLCBlbnRyaWVzW2ldLCBuYW1lLCB0aGlzKTtcclxuICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgIH1cclxuICAgIH07XHJcblxyXG4gICAgcHJvdG8ua2V5cyA9IGZ1bmN0aW9uKCkge1xyXG4gICAgICB2YXIgaXRlbXMgPSBbXTtcclxuICAgICAgdGhpcy5mb3JFYWNoKGZ1bmN0aW9uKHZhbHVlLCBuYW1lKSB7XHJcbiAgICAgICAgaXRlbXMucHVzaChuYW1lKTtcclxuICAgICAgfSk7XHJcbiAgICAgIHJldHVybiBjcmVhdGVJdGVyYXRvcihpdGVtcyk7XHJcbiAgICB9O1xyXG5cclxuICAgIHByb3RvLnZhbHVlcyA9IGZ1bmN0aW9uKCkge1xyXG4gICAgICB2YXIgaXRlbXMgPSBbXTtcclxuICAgICAgdGhpcy5mb3JFYWNoKGZ1bmN0aW9uKHZhbHVlKSB7XHJcbiAgICAgICAgaXRlbXMucHVzaCh2YWx1ZSk7XHJcbiAgICAgIH0pO1xyXG4gICAgICByZXR1cm4gY3JlYXRlSXRlcmF0b3IoaXRlbXMpO1xyXG4gICAgfTtcclxuXHJcbiAgICBwcm90by5lbnRyaWVzID0gZnVuY3Rpb24oKSB7XHJcbiAgICAgIHZhciBpdGVtcyA9IFtdO1xyXG4gICAgICB0aGlzLmZvckVhY2goZnVuY3Rpb24odmFsdWUsIG5hbWUpIHtcclxuICAgICAgICBpdGVtcy5wdXNoKFtuYW1lLCB2YWx1ZV0pO1xyXG4gICAgICB9KTtcclxuICAgICAgcmV0dXJuIGNyZWF0ZUl0ZXJhdG9yKGl0ZW1zKTtcclxuICAgIH07XHJcblxyXG4gICAgaWYgKGl0ZXJhdG9yU3VwcG9ydGVkKSB7XHJcbiAgICAgIHByb3RvW1N5bWJvbC5pdGVyYXRvcl0gPSBwcm90by5lbnRyaWVzO1xyXG4gICAgfVxyXG5cclxuICAgIHByb3RvLnRvU3RyaW5nID0gZnVuY3Rpb24oKSB7XHJcbiAgICAgIHZhciBzZWFyY2hBcnJheSA9IFtdO1xyXG4gICAgICB0aGlzLmZvckVhY2goZnVuY3Rpb24odmFsdWUsIG5hbWUpIHtcclxuICAgICAgICBzZWFyY2hBcnJheS5wdXNoKHNlcmlhbGl6ZVBhcmFtKG5hbWUpICsgJz0nICsgc2VyaWFsaXplUGFyYW0odmFsdWUpKTtcclxuICAgICAgfSk7XHJcbiAgICAgIHJldHVybiBzZWFyY2hBcnJheS5qb2luKCcmJyk7XHJcbiAgICB9O1xyXG5cclxuXHJcbiAgICBnbG9iYWwuVVJMU2VhcmNoUGFyYW1zID0gVVJMU2VhcmNoUGFyYW1zO1xyXG4gIH07XHJcblxyXG4gIGlmICghKCdVUkxTZWFyY2hQYXJhbXMnIGluIGdsb2JhbCkgfHwgKG5ldyBVUkxTZWFyY2hQYXJhbXMoJz9hPTEnKS50b1N0cmluZygpICE9PSAnYT0xJykpIHtcclxuICAgIHBvbHlmaWxsVVJMU2VhcmNoUGFyYW1zKCk7XHJcbiAgfVxyXG5cclxuICB2YXIgcHJvdG8gPSBVUkxTZWFyY2hQYXJhbXMucHJvdG90eXBlO1xyXG5cclxuICBpZiAodHlwZW9mIHByb3RvLnNvcnQgIT09ICdmdW5jdGlvbicpIHtcclxuICAgIHByb3RvLnNvcnQgPSBmdW5jdGlvbigpIHtcclxuICAgICAgdmFyIF90aGlzID0gdGhpcztcclxuICAgICAgdmFyIGl0ZW1zID0gW107XHJcbiAgICAgIHRoaXMuZm9yRWFjaChmdW5jdGlvbih2YWx1ZSwgbmFtZSkge1xyXG4gICAgICAgIGl0ZW1zLnB1c2goW25hbWUsIHZhbHVlXSk7XHJcbiAgICAgICAgaWYgKCFfdGhpcy5fZW50cmllcykge1xyXG4gICAgICAgICAgX3RoaXMuZGVsZXRlKG5hbWUpO1xyXG4gICAgICAgIH1cclxuICAgICAgfSk7XHJcbiAgICAgIGl0ZW1zLnNvcnQoZnVuY3Rpb24oYSwgYikge1xyXG4gICAgICAgIGlmIChhWzBdIDwgYlswXSkge1xyXG4gICAgICAgICAgcmV0dXJuIC0xO1xyXG4gICAgICAgIH0gZWxzZSBpZiAoYVswXSA+IGJbMF0pIHtcclxuICAgICAgICAgIHJldHVybiArMTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgcmV0dXJuIDA7XHJcbiAgICAgICAgfVxyXG4gICAgICB9KTtcclxuICAgICAgaWYgKF90aGlzLl9lbnRyaWVzKSB7IC8vIGZvcmNlIHJlc2V0IGJlY2F1c2UgSUUga2VlcHMga2V5cyBpbmRleFxyXG4gICAgICAgIF90aGlzLl9lbnRyaWVzID0ge307XHJcbiAgICAgIH1cclxuICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBpdGVtcy5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgIHRoaXMuYXBwZW5kKGl0ZW1zW2ldWzBdLCBpdGVtc1tpXVsxXSk7XHJcbiAgICAgIH1cclxuICAgIH07XHJcbiAgfVxyXG5cclxuICBpZiAodHlwZW9mIHByb3RvLl9mcm9tU3RyaW5nICE9PSAnZnVuY3Rpb24nKSB7XHJcbiAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkocHJvdG8sICdfZnJvbVN0cmluZycsIHtcclxuICAgICAgZW51bWVyYWJsZTogZmFsc2UsXHJcbiAgICAgIGNvbmZpZ3VyYWJsZTogZmFsc2UsXHJcbiAgICAgIHdyaXRhYmxlOiBmYWxzZSxcclxuICAgICAgdmFsdWU6IGZ1bmN0aW9uKHNlYXJjaFN0cmluZykge1xyXG4gICAgICAgIGlmICh0aGlzLl9lbnRyaWVzKSB7XHJcbiAgICAgICAgICB0aGlzLl9lbnRyaWVzID0ge307XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgIHZhciBrZXlzID0gW107XHJcbiAgICAgICAgICB0aGlzLmZvckVhY2goZnVuY3Rpb24odmFsdWUsIG5hbWUpIHtcclxuICAgICAgICAgICAga2V5cy5wdXNoKG5hbWUpO1xyXG4gICAgICAgICAgfSk7XHJcbiAgICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGtleXMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgdGhpcy5kZWxldGUoa2V5c1tpXSk7XHJcbiAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBzZWFyY2hTdHJpbmcgPSBzZWFyY2hTdHJpbmcucmVwbGFjZSgvXlxcPy8sICcnKTtcclxuICAgICAgICB2YXIgYXR0cmlidXRlcyA9IHNlYXJjaFN0cmluZy5zcGxpdCgnJicpO1xyXG4gICAgICAgIHZhciBhdHRyaWJ1dGU7XHJcbiAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBhdHRyaWJ1dGVzLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICBhdHRyaWJ1dGUgPSBhdHRyaWJ1dGVzW2ldLnNwbGl0KCc9Jyk7XHJcbiAgICAgICAgICB0aGlzLmFwcGVuZChcclxuICAgICAgICAgICAgZGVzZXJpYWxpemVQYXJhbShhdHRyaWJ1dGVbMF0pLFxyXG4gICAgICAgICAgICAoYXR0cmlidXRlLmxlbmd0aCA+IDEpID8gZGVzZXJpYWxpemVQYXJhbShhdHRyaWJ1dGVbMV0pIDogJydcclxuICAgICAgICAgICk7XHJcbiAgICAgICAgfVxyXG4gICAgICB9XHJcbiAgICB9KTtcclxuICB9XHJcblxyXG4gIC8vIEhUTUxBbmNob3JFbGVtZW50XHJcblxyXG59KShcclxuICAodHlwZW9mIGdsb2JhbCAhPT0gJ3VuZGVmaW5lZCcpID8gZ2xvYmFsXHJcbiAgICA6ICgodHlwZW9mIHdpbmRvdyAhPT0gJ3VuZGVmaW5lZCcpID8gd2luZG93XHJcbiAgICA6ICgodHlwZW9mIHNlbGYgIT09ICd1bmRlZmluZWQnKSA/IHNlbGYgOiB0aGlzKSlcclxuKTtcclxuXHJcbihmdW5jdGlvbihnbG9iYWwpIHtcclxuICAvKipcclxuICAgKiBQb2x5ZmlsbCBVUkxcclxuICAgKlxyXG4gICAqIEluc3BpcmVkIGZyb20gOiBodHRwczovL2dpdGh1Yi5jb20vYXJ2L0RPTS1VUkwtUG9seWZpbGwvYmxvYi9tYXN0ZXIvc3JjL3VybC5qc1xyXG4gICAqL1xyXG5cclxuICB2YXIgY2hlY2tJZlVSTElzU3VwcG9ydGVkID0gZnVuY3Rpb24oKSB7XHJcbiAgICB0cnkge1xyXG4gICAgICB2YXIgdSA9IG5ldyBVUkwoJ2InLCAnaHR0cDovL2EnKTtcclxuICAgICAgdS5wYXRobmFtZSA9ICdjJTIwZCc7XHJcbiAgICAgIHJldHVybiAodS5ocmVmID09PSAnaHR0cDovL2EvYyUyMGQnKSAmJiB1LnNlYXJjaFBhcmFtcztcclxuICAgIH0gY2F0Y2ggKGUpIHtcclxuICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgfVxyXG4gIH07XHJcblxyXG5cclxuICB2YXIgcG9seWZpbGxVUkwgPSBmdW5jdGlvbigpIHtcclxuICAgIHZhciBfVVJMID0gZ2xvYmFsLlVSTDtcclxuXHJcbiAgICB2YXIgVVJMID0gZnVuY3Rpb24odXJsLCBiYXNlKSB7XHJcbiAgICAgIGlmICh0eXBlb2YgdXJsICE9PSAnc3RyaW5nJykgdXJsID0gU3RyaW5nKHVybCk7XHJcblxyXG4gICAgICAvLyBPbmx5IGNyZWF0ZSBhbm90aGVyIGRvY3VtZW50IGlmIHRoZSBiYXNlIGlzIGRpZmZlcmVudCBmcm9tIGN1cnJlbnQgbG9jYXRpb24uXHJcbiAgICAgIHZhciBkb2MgPSBkb2N1bWVudCwgYmFzZUVsZW1lbnQ7XHJcbiAgICAgIGlmIChiYXNlICYmIChnbG9iYWwubG9jYXRpb24gPT09IHZvaWQgMCB8fCBiYXNlICE9PSBnbG9iYWwubG9jYXRpb24uaHJlZikpIHtcclxuICAgICAgICBkb2MgPSBkb2N1bWVudC5pbXBsZW1lbnRhdGlvbi5jcmVhdGVIVE1MRG9jdW1lbnQoJycpO1xyXG4gICAgICAgIGJhc2VFbGVtZW50ID0gZG9jLmNyZWF0ZUVsZW1lbnQoJ2Jhc2UnKTtcclxuICAgICAgICBiYXNlRWxlbWVudC5ocmVmID0gYmFzZTtcclxuICAgICAgICBkb2MuaGVhZC5hcHBlbmRDaGlsZChiYXNlRWxlbWVudCk7XHJcbiAgICAgICAgdHJ5IHtcclxuICAgICAgICAgIGlmIChiYXNlRWxlbWVudC5ocmVmLmluZGV4T2YoYmFzZSkgIT09IDApIHRocm93IG5ldyBFcnJvcihiYXNlRWxlbWVudC5ocmVmKTtcclxuICAgICAgICB9IGNhdGNoIChlcnIpIHtcclxuICAgICAgICAgIHRocm93IG5ldyBFcnJvcignVVJMIHVuYWJsZSB0byBzZXQgYmFzZSAnICsgYmFzZSArICcgZHVlIHRvICcgKyBlcnIpO1xyXG4gICAgICAgIH1cclxuICAgICAgfVxyXG5cclxuICAgICAgdmFyIGFuY2hvckVsZW1lbnQgPSBkb2MuY3JlYXRlRWxlbWVudCgnYScpO1xyXG4gICAgICBhbmNob3JFbGVtZW50LmhyZWYgPSB1cmw7XHJcbiAgICAgIGlmIChiYXNlRWxlbWVudCkge1xyXG4gICAgICAgIGRvYy5ib2R5LmFwcGVuZENoaWxkKGFuY2hvckVsZW1lbnQpO1xyXG4gICAgICAgIGFuY2hvckVsZW1lbnQuaHJlZiA9IGFuY2hvckVsZW1lbnQuaHJlZjsgLy8gZm9yY2UgaHJlZiB0byByZWZyZXNoXHJcbiAgICAgIH1cclxuXHJcbiAgICAgIGlmIChhbmNob3JFbGVtZW50LnByb3RvY29sID09PSAnOicgfHwgIS86Ly50ZXN0KGFuY2hvckVsZW1lbnQuaHJlZikpIHtcclxuICAgICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdJbnZhbGlkIFVSTCcpO1xyXG4gICAgICB9XHJcblxyXG4gICAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkodGhpcywgJ19hbmNob3JFbGVtZW50Jywge1xyXG4gICAgICAgIHZhbHVlOiBhbmNob3JFbGVtZW50XHJcbiAgICAgIH0pO1xyXG5cclxuXHJcbiAgICAgIC8vIGNyZWF0ZSBhIGxpbmtlZCBzZWFyY2hQYXJhbXMgd2hpY2ggcmVmbGVjdCBpdHMgY2hhbmdlcyBvbiBVUkxcclxuICAgICAgdmFyIHNlYXJjaFBhcmFtcyA9IG5ldyBVUkxTZWFyY2hQYXJhbXModGhpcy5zZWFyY2gpO1xyXG4gICAgICB2YXIgZW5hYmxlU2VhcmNoVXBkYXRlID0gdHJ1ZTtcclxuICAgICAgdmFyIGVuYWJsZVNlYXJjaFBhcmFtc1VwZGF0ZSA9IHRydWU7XHJcbiAgICAgIHZhciBfdGhpcyA9IHRoaXM7XHJcbiAgICAgIFsnYXBwZW5kJywgJ2RlbGV0ZScsICdzZXQnXS5mb3JFYWNoKGZ1bmN0aW9uKG1ldGhvZE5hbWUpIHtcclxuICAgICAgICB2YXIgbWV0aG9kID0gc2VhcmNoUGFyYW1zW21ldGhvZE5hbWVdO1xyXG4gICAgICAgIHNlYXJjaFBhcmFtc1ttZXRob2ROYW1lXSA9IGZ1bmN0aW9uKCkge1xyXG4gICAgICAgICAgbWV0aG9kLmFwcGx5KHNlYXJjaFBhcmFtcywgYXJndW1lbnRzKTtcclxuICAgICAgICAgIGlmIChlbmFibGVTZWFyY2hVcGRhdGUpIHtcclxuICAgICAgICAgICAgZW5hYmxlU2VhcmNoUGFyYW1zVXBkYXRlID0gZmFsc2U7XHJcbiAgICAgICAgICAgIF90aGlzLnNlYXJjaCA9IHNlYXJjaFBhcmFtcy50b1N0cmluZygpO1xyXG4gICAgICAgICAgICBlbmFibGVTZWFyY2hQYXJhbXNVcGRhdGUgPSB0cnVlO1xyXG4gICAgICAgICAgfVxyXG4gICAgICAgIH07XHJcbiAgICAgIH0pO1xyXG5cclxuICAgICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KHRoaXMsICdzZWFyY2hQYXJhbXMnLCB7XHJcbiAgICAgICAgdmFsdWU6IHNlYXJjaFBhcmFtcyxcclxuICAgICAgICBlbnVtZXJhYmxlOiB0cnVlXHJcbiAgICAgIH0pO1xyXG5cclxuICAgICAgdmFyIHNlYXJjaCA9IHZvaWQgMDtcclxuICAgICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KHRoaXMsICdfdXBkYXRlU2VhcmNoUGFyYW1zJywge1xyXG4gICAgICAgIGVudW1lcmFibGU6IGZhbHNlLFxyXG4gICAgICAgIGNvbmZpZ3VyYWJsZTogZmFsc2UsXHJcbiAgICAgICAgd3JpdGFibGU6IGZhbHNlLFxyXG4gICAgICAgIHZhbHVlOiBmdW5jdGlvbigpIHtcclxuICAgICAgICAgIGlmICh0aGlzLnNlYXJjaCAhPT0gc2VhcmNoKSB7XHJcbiAgICAgICAgICAgIHNlYXJjaCA9IHRoaXMuc2VhcmNoO1xyXG4gICAgICAgICAgICBpZiAoZW5hYmxlU2VhcmNoUGFyYW1zVXBkYXRlKSB7XHJcbiAgICAgICAgICAgICAgZW5hYmxlU2VhcmNoVXBkYXRlID0gZmFsc2U7XHJcbiAgICAgICAgICAgICAgdGhpcy5zZWFyY2hQYXJhbXMuX2Zyb21TdHJpbmcodGhpcy5zZWFyY2gpO1xyXG4gICAgICAgICAgICAgIGVuYWJsZVNlYXJjaFVwZGF0ZSA9IHRydWU7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgIH0pO1xyXG4gICAgfTtcclxuXHJcbiAgICB2YXIgcHJvdG8gPSBVUkwucHJvdG90eXBlO1xyXG5cclxuICAgIHZhciBsaW5rVVJMV2l0aEFuY2hvckF0dHJpYnV0ZSA9IGZ1bmN0aW9uKGF0dHJpYnV0ZU5hbWUpIHtcclxuICAgICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KHByb3RvLCBhdHRyaWJ1dGVOYW1lLCB7XHJcbiAgICAgICAgZ2V0OiBmdW5jdGlvbigpIHtcclxuICAgICAgICAgIHJldHVybiB0aGlzLl9hbmNob3JFbGVtZW50W2F0dHJpYnV0ZU5hbWVdO1xyXG4gICAgICAgIH0sXHJcbiAgICAgICAgc2V0OiBmdW5jdGlvbih2YWx1ZSkge1xyXG4gICAgICAgICAgdGhpcy5fYW5jaG9yRWxlbWVudFthdHRyaWJ1dGVOYW1lXSA9IHZhbHVlO1xyXG4gICAgICAgIH0sXHJcbiAgICAgICAgZW51bWVyYWJsZTogdHJ1ZVxyXG4gICAgICB9KTtcclxuICAgIH07XHJcblxyXG4gICAgWydoYXNoJywgJ2hvc3QnLCAnaG9zdG5hbWUnLCAncG9ydCcsICdwcm90b2NvbCddXHJcbiAgICAgIC5mb3JFYWNoKGZ1bmN0aW9uKGF0dHJpYnV0ZU5hbWUpIHtcclxuICAgICAgICBsaW5rVVJMV2l0aEFuY2hvckF0dHJpYnV0ZShhdHRyaWJ1dGVOYW1lKTtcclxuICAgICAgfSk7XHJcblxyXG4gICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KHByb3RvLCAnc2VhcmNoJywge1xyXG4gICAgICBnZXQ6IGZ1bmN0aW9uKCkge1xyXG4gICAgICAgIHJldHVybiB0aGlzLl9hbmNob3JFbGVtZW50WydzZWFyY2gnXTtcclxuICAgICAgfSxcclxuICAgICAgc2V0OiBmdW5jdGlvbih2YWx1ZSkge1xyXG4gICAgICAgIHRoaXMuX2FuY2hvckVsZW1lbnRbJ3NlYXJjaCddID0gdmFsdWU7XHJcbiAgICAgICAgdGhpcy5fdXBkYXRlU2VhcmNoUGFyYW1zKCk7XHJcbiAgICAgIH0sXHJcbiAgICAgIGVudW1lcmFibGU6IHRydWVcclxuICAgIH0pO1xyXG5cclxuICAgIE9iamVjdC5kZWZpbmVQcm9wZXJ0aWVzKHByb3RvLCB7XHJcblxyXG4gICAgICAndG9TdHJpbmcnOiB7XHJcbiAgICAgICAgZ2V0OiBmdW5jdGlvbigpIHtcclxuICAgICAgICAgIHZhciBfdGhpcyA9IHRoaXM7XHJcbiAgICAgICAgICByZXR1cm4gZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgICAgIHJldHVybiBfdGhpcy5ocmVmO1xyXG4gICAgICAgICAgfTtcclxuICAgICAgICB9XHJcbiAgICAgIH0sXHJcblxyXG4gICAgICAnaHJlZic6IHtcclxuICAgICAgICBnZXQ6IGZ1bmN0aW9uKCkge1xyXG4gICAgICAgICAgcmV0dXJuIHRoaXMuX2FuY2hvckVsZW1lbnQuaHJlZi5yZXBsYWNlKC9cXD8kLywgJycpO1xyXG4gICAgICAgIH0sXHJcbiAgICAgICAgc2V0OiBmdW5jdGlvbih2YWx1ZSkge1xyXG4gICAgICAgICAgdGhpcy5fYW5jaG9yRWxlbWVudC5ocmVmID0gdmFsdWU7XHJcbiAgICAgICAgICB0aGlzLl91cGRhdGVTZWFyY2hQYXJhbXMoKTtcclxuICAgICAgICB9LFxyXG4gICAgICAgIGVudW1lcmFibGU6IHRydWVcclxuICAgICAgfSxcclxuXHJcbiAgICAgICdwYXRobmFtZSc6IHtcclxuICAgICAgICBnZXQ6IGZ1bmN0aW9uKCkge1xyXG4gICAgICAgICAgcmV0dXJuIHRoaXMuX2FuY2hvckVsZW1lbnQucGF0aG5hbWUucmVwbGFjZSgvKF5cXC8/KS8sICcvJyk7XHJcbiAgICAgICAgfSxcclxuICAgICAgICBzZXQ6IGZ1bmN0aW9uKHZhbHVlKSB7XHJcbiAgICAgICAgICB0aGlzLl9hbmNob3JFbGVtZW50LnBhdGhuYW1lID0gdmFsdWU7XHJcbiAgICAgICAgfSxcclxuICAgICAgICBlbnVtZXJhYmxlOiB0cnVlXHJcbiAgICAgIH0sXHJcblxyXG4gICAgICAnb3JpZ2luJzoge1xyXG4gICAgICAgIGdldDogZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgICAvLyBnZXQgZXhwZWN0ZWQgcG9ydCBmcm9tIHByb3RvY29sXHJcbiAgICAgICAgICB2YXIgZXhwZWN0ZWRQb3J0ID0geyAnaHR0cDonOiA4MCwgJ2h0dHBzOic6IDQ0MywgJ2Z0cDonOiAyMSB9W3RoaXMuX2FuY2hvckVsZW1lbnQucHJvdG9jb2xdO1xyXG4gICAgICAgICAgLy8gYWRkIHBvcnQgdG8gb3JpZ2luIGlmLCBleHBlY3RlZCBwb3J0IGlzIGRpZmZlcmVudCB0aGFuIGFjdHVhbCBwb3J0XHJcbiAgICAgICAgICAvLyBhbmQgaXQgaXMgbm90IGVtcHR5IGYuZSBodHRwOi8vZm9vOjgwODBcclxuICAgICAgICAgIC8vIDgwODAgIT0gODAgJiYgODA4MCAhPSAnJ1xyXG4gICAgICAgICAgdmFyIGFkZFBvcnRUb09yaWdpbiA9IHRoaXMuX2FuY2hvckVsZW1lbnQucG9ydCAhPSBleHBlY3RlZFBvcnQgJiZcclxuICAgICAgICAgICAgdGhpcy5fYW5jaG9yRWxlbWVudC5wb3J0ICE9PSAnJztcclxuXHJcbiAgICAgICAgICByZXR1cm4gdGhpcy5fYW5jaG9yRWxlbWVudC5wcm90b2NvbCArXHJcbiAgICAgICAgICAgICcvLycgK1xyXG4gICAgICAgICAgICB0aGlzLl9hbmNob3JFbGVtZW50Lmhvc3RuYW1lICtcclxuICAgICAgICAgICAgKGFkZFBvcnRUb09yaWdpbiA/ICgnOicgKyB0aGlzLl9hbmNob3JFbGVtZW50LnBvcnQpIDogJycpO1xyXG4gICAgICAgIH0sXHJcbiAgICAgICAgZW51bWVyYWJsZTogdHJ1ZVxyXG4gICAgICB9LFxyXG5cclxuICAgICAgJ3Bhc3N3b3JkJzogeyAvLyBUT0RPXHJcbiAgICAgICAgZ2V0OiBmdW5jdGlvbigpIHtcclxuICAgICAgICAgIHJldHVybiAnJztcclxuICAgICAgICB9LFxyXG4gICAgICAgIHNldDogZnVuY3Rpb24odmFsdWUpIHtcclxuICAgICAgICB9LFxyXG4gICAgICAgIGVudW1lcmFibGU6IHRydWVcclxuICAgICAgfSxcclxuXHJcbiAgICAgICd1c2VybmFtZSc6IHsgLy8gVE9ET1xyXG4gICAgICAgIGdldDogZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgICByZXR1cm4gJyc7XHJcbiAgICAgICAgfSxcclxuICAgICAgICBzZXQ6IGZ1bmN0aW9uKHZhbHVlKSB7XHJcbiAgICAgICAgfSxcclxuICAgICAgICBlbnVtZXJhYmxlOiB0cnVlXHJcbiAgICAgIH0sXHJcbiAgICB9KTtcclxuXHJcbiAgICBVUkwuY3JlYXRlT2JqZWN0VVJMID0gZnVuY3Rpb24oYmxvYikge1xyXG4gICAgICByZXR1cm4gX1VSTC5jcmVhdGVPYmplY3RVUkwuYXBwbHkoX1VSTCwgYXJndW1lbnRzKTtcclxuICAgIH07XHJcblxyXG4gICAgVVJMLnJldm9rZU9iamVjdFVSTCA9IGZ1bmN0aW9uKHVybCkge1xyXG4gICAgICByZXR1cm4gX1VSTC5yZXZva2VPYmplY3RVUkwuYXBwbHkoX1VSTCwgYXJndW1lbnRzKTtcclxuICAgIH07XHJcblxyXG4gICAgZ2xvYmFsLlVSTCA9IFVSTDtcclxuXHJcbiAgfTtcclxuXHJcbiAgaWYgKCFjaGVja0lmVVJMSXNTdXBwb3J0ZWQoKSkge1xyXG4gICAgcG9seWZpbGxVUkwoKTtcclxuICB9XHJcblxyXG4gIGlmICgoZ2xvYmFsLmxvY2F0aW9uICE9PSB2b2lkIDApICYmICEoJ29yaWdpbicgaW4gZ2xvYmFsLmxvY2F0aW9uKSkge1xyXG4gICAgdmFyIGdldE9yaWdpbiA9IGZ1bmN0aW9uKCkge1xyXG4gICAgICByZXR1cm4gZ2xvYmFsLmxvY2F0aW9uLnByb3RvY29sICsgJy8vJyArIGdsb2JhbC5sb2NhdGlvbi5ob3N0bmFtZSArIChnbG9iYWwubG9jYXRpb24ucG9ydCA/ICgnOicgKyBnbG9iYWwubG9jYXRpb24ucG9ydCkgOiAnJyk7XHJcbiAgICB9O1xyXG5cclxuICAgIHRyeSB7XHJcbiAgICAgIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShnbG9iYWwubG9jYXRpb24sICdvcmlnaW4nLCB7XHJcbiAgICAgICAgZ2V0OiBnZXRPcmlnaW4sXHJcbiAgICAgICAgZW51bWVyYWJsZTogdHJ1ZVxyXG4gICAgICB9KTtcclxuICAgIH0gY2F0Y2ggKGUpIHtcclxuICAgICAgc2V0SW50ZXJ2YWwoZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgZ2xvYmFsLmxvY2F0aW9uLm9yaWdpbiA9IGdldE9yaWdpbigpO1xyXG4gICAgICB9LCAxMDApO1xyXG4gICAgfVxyXG4gIH1cclxuXHJcbn0pKFxyXG4gICh0eXBlb2YgZ2xvYmFsICE9PSAndW5kZWZpbmVkJykgPyBnbG9iYWxcclxuICAgIDogKCh0eXBlb2Ygd2luZG93ICE9PSAndW5kZWZpbmVkJykgPyB3aW5kb3dcclxuICAgIDogKCh0eXBlb2Ygc2VsZiAhPT0gJ3VuZGVmaW5lZCcpID8gc2VsZiA6IHRoaXMpKVxyXG4pO1xyXG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vdXJsLXBvbHlmaWxsL3VybC1wb2x5ZmlsbC5qc1xuLy8gbW9kdWxlIGlkID0gNzRcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDI1Il0sInNvdXJjZVJvb3QiOiIifQ==