window["employee_form"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 494);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

exports.default = function (instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
};

/***/ }),

/***/ 1:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _defineProperty = __webpack_require__(19);

var _defineProperty2 = _interopRequireDefault(_defineProperty);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = function () {
  function defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      (0, _defineProperty2.default)(target, descriptor.key, descriptor);
    }
  }

  return function (Constructor, protoProps, staticProps) {
    if (protoProps) defineProperties(Constructor.prototype, protoProps);
    if (staticProps) defineProperties(Constructor, staticProps);
    return Constructor;
  };
}();

/***/ }),

/***/ 10:
/***/ (function(module, exports, __webpack_require__) {

var dP         = __webpack_require__(6)
  , createDesc = __webpack_require__(12);
module.exports = __webpack_require__(2) ? function(object, key, value){
  return dP.f(object, key, createDesc(1, value));
} : function(object, key, value){
  object[key] = value;
  return object;
};

/***/ }),

/***/ 11:
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4);
module.exports = function(it){
  if(!isObject(it))throw TypeError(it + ' is not an object!');
  return it;
};

/***/ }),

/***/ 12:
/***/ (function(module, exports) {

module.exports = function(bitmap, value){
  return {
    enumerable  : !(bitmap & 1),
    configurable: !(bitmap & 2),
    writable    : !(bitmap & 4),
    value       : value
  };
};

/***/ }),

/***/ 13:
/***/ (function(module, exports, __webpack_require__) {

// optional / simple context binding
var aFunction = __webpack_require__(18);
module.exports = function(fn, that, length){
  aFunction(fn);
  if(that === undefined)return fn;
  switch(length){
    case 1: return function(a){
      return fn.call(that, a);
    };
    case 2: return function(a, b){
      return fn.call(that, a, b);
    };
    case 3: return function(a, b, c){
      return fn.call(that, a, b, c);
    };
  }
  return function(/* ...args */){
    return fn.apply(that, arguments);
  };
};

/***/ }),

/***/ 14:
/***/ (function(module, exports, __webpack_require__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __webpack_require__(4);
// instead of the ES6 spec version, we didn't implement @@toPrimitive case
// and the second argument - flag - preferred type is a string
module.exports = function(it, S){
  if(!isObject(it))return it;
  var fn, val;
  if(S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it)))return val;
  if(typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it)))return val;
  if(!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it)))return val;
  throw TypeError("Can't convert object to primitive value");
};

/***/ }),

/***/ 16:
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4)
  , document = __webpack_require__(5).document
  // in old IE typeof document.createElement is 'object'
  , is = isObject(document) && isObject(document.createElement);
module.exports = function(it){
  return is ? document.createElement(it) : {};
};

/***/ }),

/***/ 17:
/***/ (function(module, exports, __webpack_require__) {

module.exports = !__webpack_require__(2) && !__webpack_require__(7)(function(){
  return Object.defineProperty(__webpack_require__(16)('div'), 'a', {get: function(){ return 7; }}).a != 7;
});

/***/ }),

/***/ 18:
/***/ (function(module, exports) {

module.exports = function(it){
  if(typeof it != 'function')throw TypeError(it + ' is not a function!');
  return it;
};

/***/ }),

/***/ 19:
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(20), __esModule: true };

/***/ }),

/***/ 2:
/***/ (function(module, exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__(7)(function(){
  return Object.defineProperty({}, 'a', {get: function(){ return 7; }}).a != 7;
});

/***/ }),

/***/ 20:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(21);
var $Object = __webpack_require__(3).Object;
module.exports = function defineProperty(it, key, desc){
  return $Object.defineProperty(it, key, desc);
};

/***/ }),

/***/ 21:
/***/ (function(module, exports, __webpack_require__) {

var $export = __webpack_require__(8);
// 19.1.2.4 / 15.2.3.6 Object.defineProperty(O, P, Attributes)
$export($export.S + $export.F * !__webpack_require__(2), 'Object', {defineProperty: __webpack_require__(6).f});

/***/ }),

/***/ 3:
/***/ (function(module, exports) {

var core = module.exports = {version: '2.4.0'};
if(typeof __e == 'number')__e = core; // eslint-disable-line no-undef

/***/ }),

/***/ 4:
/***/ (function(module, exports) {

module.exports = function(it){
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};

/***/ }),

/***/ 411:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _choiceTree = __webpack_require__(61);

var _choiceTree2 = _interopRequireDefault(_choiceTree);

var _addonsConnector = __webpack_require__(466);

var _addonsConnector2 = _interopRequireDefault(_addonsConnector);

var _changePasswordControl = __webpack_require__(468);

var _changePasswordControl2 = _interopRequireDefault(_changePasswordControl);

var _employeeFormMap = __webpack_require__(493);

var _employeeFormMap2 = _interopRequireDefault(_employeeFormMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Class responsible for javascript actions in employee add/edit page.
 */
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var EmployeeForm = function () {
  function EmployeeForm() {
    (0, _classCallCheck3.default)(this, EmployeeForm);

    this.shopChoiceTreeSelector = _employeeFormMap2.default.shopChoiceTree;
    this.shopChoiceTree = new _choiceTree2.default(this.shopChoiceTreeSelector);
    this.employeeProfileSelector = _employeeFormMap2.default.profileSelect;
    this.tabsDropdownSelector = _employeeFormMap2.default.defaultPageSelect;

    this.shopChoiceTree.enableAutoCheckChildren();

    new _addonsConnector2.default(_employeeFormMap2.default.addonsConnectForm, _employeeFormMap2.default.addonsLoginButton);

    new _changePasswordControl2.default(_employeeFormMap2.default.changePasswordInputsBlock, _employeeFormMap2.default.showChangePasswordBlockButton, _employeeFormMap2.default.hideChangePasswordBlockButton, _employeeFormMap2.default.generatePasswordButton, _employeeFormMap2.default.oldPasswordInput, _employeeFormMap2.default.newPasswordInput, _employeeFormMap2.default.confirmNewPasswordInput, _employeeFormMap2.default.generatedPasswordDisplayInput, _employeeFormMap2.default.passwordStrengthFeedbackContainer);

    this._initEvents();
    this._toggleShopTree();

    return {};
  }

  /**
   * Initialize page's events.
   *
   * @private
   */


  (0, _createClass3.default)(EmployeeForm, [{
    key: "_initEvents",
    value: function _initEvents() {
      var _this = this;

      var $employeeProfilesDropdown = $(this.employeeProfileSelector);
      var getTabsUrl = $employeeProfilesDropdown.data('get-tabs-url');

      $(document).on('change', this.employeeProfileSelector, function () {
        return _this._toggleShopTree();
      });

      // Reload tabs dropdown when employee profile is changed.
      $(document).on('change', this.employeeProfileSelector, function (event) {
        $.get(getTabsUrl, {
          profileId: $(event.currentTarget).val()
        }, function (tabs) {
          _this._reloadTabsDropdown(tabs);
        }, 'json');
      });
    }

    /**
     * Reload tabs dropdown with new content.
     *
     * @param {Object} accessibleTabs
     *
     * @private
     */

  }, {
    key: "_reloadTabsDropdown",
    value: function _reloadTabsDropdown(accessibleTabs) {
      var $tabsDropdown = $(this.tabsDropdownSelector);

      $tabsDropdown.empty();

      for (var key in accessibleTabs) {
        if (accessibleTabs[key]['children'].length > 0 && accessibleTabs[key]['name']) {
          // If tab has children - create an option group and put children inside.
          var $optgroup = this._createOptionGroup(accessibleTabs[key]['name']);

          for (var childKey in accessibleTabs[key]['children']) {
            if (accessibleTabs[key]['children'][childKey]['name']) {
              $optgroup.append(this._createOption(accessibleTabs[key]['children'][childKey]['name'], accessibleTabs[key]['children'][childKey]['id_tab']));
            }
          }

          $tabsDropdown.append($optgroup);
        } else if (accessibleTabs[key]['name']) {
          // If tab doesn't have children - create an option.
          $tabsDropdown.append(this._createOption(accessibleTabs[key]['name'], accessibleTabs[key]['id_tab']));
        }
      }
    }

    /**
     * Hide shop choice tree if superadmin profile is selected, show it otherwise.
     *
     * @private
     */

  }, {
    key: "_toggleShopTree",
    value: function _toggleShopTree() {
      var $employeeProfileDropdown = $(this.employeeProfileSelector);
      var superAdminProfileId = $employeeProfileDropdown.data('admin-profile');
      $(this.shopChoiceTreeSelector).closest('.form-group').toggleClass('d-none', $employeeProfileDropdown.val() == superAdminProfileId);
    }

    /**
     * Creates an <optgroup> element
     *
     * @param {String} name
     *
     * @returns {jQuery}
     *
     * @private
     */

  }, {
    key: "_createOptionGroup",
    value: function _createOptionGroup(name) {
      return $("<optgroup label=\"" + name + "\">");
    }

    /**
     * Creates an <option> element.
     *
     * @param {String} name
     * @param {String} value
     *
     * @returns {jQuery}
     *
     * @private
     */

  }, {
    key: "_createOption",
    value: function _createOption(name, value) {
      return $("<option value=\"" + value + "\">" + name + "</option>");
    }
  }]);
  return EmployeeForm;
}();

exports.default = EmployeeForm;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(42)))

/***/ }),

/***/ 42:
/***/ (function(module, exports) {

(function() { module.exports = window["jQuery"]; }());

/***/ }),

/***/ 466:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Responsible for connecting to addons marketplace.
 * Makes an addons connect request to the server, displays error messages if it fails.
 */

var AddonsConnector = function () {
  function AddonsConnector(addonsConnectFormSelector, loadingSpinnerSelector) {
    (0, _classCallCheck3.default)(this, AddonsConnector);

    this.addonsConnectFormSelector = addonsConnectFormSelector;
    this.$loadingSpinner = $(loadingSpinnerSelector);

    this._initEvents();

    return {};
  }

  /**
   * Initialize events related to connection to addons.
   *
   * @private
   */


  (0, _createClass3.default)(AddonsConnector, [{
    key: '_initEvents',
    value: function _initEvents() {
      var _this = this;

      $('body').on('submit', this.addonsConnectFormSelector, function (event) {
        var $form = $(event.currentTarget);
        event.preventDefault();
        event.stopPropagation();

        _this._connect($form.attr('action'), $form.serialize());
      });
    }

    /**
     * Do a POST request to connect to addons.
     *
     * @param {String} addonsConnectUrl
     * @param {Object} formData
     *
     * @private
     */

  }, {
    key: '_connect',
    value: function _connect(addonsConnectUrl, formData) {
      var _this2 = this;

      $.ajax({
        method: 'POST',
        url: addonsConnectUrl,
        dataType: 'json',
        data: formData,
        beforeSend: function beforeSend() {
          _this2.$loadingSpinner.show();
          $('button.btn[type="submit"]', _this2.addonsConnectFormSelector).hide();
        }
      }).then(function (response) {
        if (response.success === 1) {
          location.reload();
        } else {
          $.growl.error({
            message: response.message
          });

          _this2.$loadingSpinner.hide();
          $('button.btn[type="submit"]', _this2.addonsConnectFormSelector).fadeIn();
        }
      }, function () {
        $.growl.error({
          message: $(_this2.addonsConnectFormSelector).data('error-message')
        });

        _this2.$loadingSpinner.hide();
        $('button.btn[type="submit"]', _this2.addonsConnectFormSelector).show();
      });
    }
  }]);
  return AddonsConnector;
}();

exports.default = AddonsConnector;

/***/ }),

/***/ 467:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Generates a password and informs about it's strength.
 * You can pass a password input to watch the password strength and display feedback messages.
 * You can also generate a random password into an input.
 */

var ChangePasswordHandler = function () {
  function ChangePasswordHandler(passwordStrengthFeedbackContainerSelector) {
    var _this = this;

    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    (0, _classCallCheck3.default)(this, ChangePasswordHandler);

    // Minimum length of the generated password.
    this.minLength = options.minLength || 8;

    // Feedback container holds messages representing password strength.
    this.$feedbackContainer = $(passwordStrengthFeedbackContainerSelector);

    return {
      watchPasswordStrength: function watchPasswordStrength($input) {
        return _this.watchPasswordStrength($input);
      },
      generatePassword: function generatePassword($input) {
        return _this.generatePassword($input);
      }
    };
  }

  /**
   * Watch password, which is entered in the input, strength and inform about it.
   *
   * @param {jQuery} $input the input to watch.
   */


  (0, _createClass3.default)(ChangePasswordHandler, [{
    key: 'watchPasswordStrength',
    value: function watchPasswordStrength($input) {
      var _this2 = this;

      $.passy.requirements.length.min = this.minLength;
      $.passy.requirements.characters = 'DIGIT';

      $input.each(function (index, element) {
        var $outputContainer = $('<span>');

        $outputContainer.insertAfter($(element));

        $(element).passy(function (strength, valid) {
          _this2._displayFeedback($outputContainer, strength, valid);
        });
      });
    }

    /**
     * Generates a password and fills it to given input.
     *
     * @param {jQuery} $input the input to fill the password into.
     */

  }, {
    key: 'generatePassword',
    value: function generatePassword($input) {
      $input.passy('generate', this.minLength);
    }

    /**
     * Display feedback about password's strength.
     *
     * @param {jQuery} $outputContainer a container to put feedback output into.
     * @param {number} passwordStrength
     * @param {boolean} isPasswordValid
     *
     * @private
     */

  }, {
    key: '_displayFeedback',
    value: function _displayFeedback($outputContainer, passwordStrength, isPasswordValid) {
      var feedback = this._getPasswordStrengthFeedback(passwordStrength);
      $outputContainer.text(feedback.message);
      $outputContainer.removeClass('text-danger text-warning text-success');
      $outputContainer.addClass(feedback.elementClass);
      $outputContainer.toggleClass('d-none', !isPasswordValid);
    }

    /**
     * Get feedback that describes given password strength.
     * Response contains text message and element class.
     *
     * @param {number} strength
     *
     * @private
     */

  }, {
    key: '_getPasswordStrengthFeedback',
    value: function _getPasswordStrengthFeedback(strength) {
      switch (strength) {
        case $.passy.strength.LOW:
          return {
            message: this.$feedbackContainer.find('.strength-low').text(),
            elementClass: 'text-danger'
          };

        case $.passy.strength.MEDIUM:
          return {
            message: this.$feedbackContainer.find('.strength-medium').text(),
            elementClass: 'text-warning'
          };

        case $.passy.strength.HIGH:
          return {
            message: this.$feedbackContainer.find('.strength-high').text(),
            elementClass: 'text-success'
          };

        case $.passy.strength.EXTREME:
          return {
            message: this.$feedbackContainer.find('.strength-extreme').text(),
            elementClass: 'text-success'
          };
      }

      throw 'Invalid password strength indicator.';
    }
  }]);
  return ChangePasswordHandler;
}();

exports.default = ChangePasswordHandler;

/***/ }),

/***/ 468:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _changePasswordHandler = __webpack_require__(467);

var _changePasswordHandler2 = _interopRequireDefault(_changePasswordHandler);

var _passwordValidator = __webpack_require__(471);

var _passwordValidator2 = _interopRequireDefault(_passwordValidator);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Class responsible for actions related to "change password" form type.
 * Generates random passwords, validates new password and it's confirmation,
 * displays error messages related to validation.
 */

var ChangePasswordControl = function () {
  function ChangePasswordControl(inputsBlockSelector, showButtonSelector, hideButtonSelector, generatePasswordButtonSelector, oldPasswordInputSelector, newPasswordInputSelector, confirmNewPasswordInputSelector, generatedPasswordDisplaySelector, passwordStrengthFeedbackContainerSelector) {
    (0, _classCallCheck3.default)(this, ChangePasswordControl);

    // Block that contains password inputs
    this.$inputsBlock = $(inputsBlockSelector);

    // Button that shows the password inputs block
    this.showButtonSelector = showButtonSelector;

    // Button that hides the password inputs block
    this.hideButtonSelector = hideButtonSelector;

    // Button that generates a random password
    this.generatePasswordButtonSelector = generatePasswordButtonSelector;

    // Input to enter old password
    this.oldPasswordInputSelector = oldPasswordInputSelector;

    // Input to enter new password
    this.newPasswordInputSelector = newPasswordInputSelector;

    // Input to confirm the new password
    this.confirmNewPasswordInputSelector = confirmNewPasswordInputSelector;

    // Input that displays generated random password
    this.generatedPasswordDisplaySelector = generatedPasswordDisplaySelector;

    // Main input for password generation
    this.$newPasswordInputs = this.$inputsBlock.find(this.newPasswordInputSelector);

    // Generated password will be copied to these inputs
    this.$copyPasswordInputs = this.$inputsBlock.find(this.confirmNewPasswordInputSelector).add(this.generatedPasswordDisplaySelector);

    // All inputs in the change password block, that are submittable with the form.
    this.$submittableInputs = this.$inputsBlock.find(this.oldPasswordInputSelector).add(this.newPasswordInputSelector).add(this.confirmNewPasswordInputSelector);

    this.passwordHandler = new _changePasswordHandler2.default(passwordStrengthFeedbackContainerSelector);

    this.passwordValidator = new _passwordValidator2.default(this.newPasswordInputSelector, this.confirmNewPasswordInputSelector);

    this._hideInputsBlock();
    this._initEvents();

    return {};
  }

  /**
   * Initialize events.
   *
   * @private
   */


  (0, _createClass3.default)(ChangePasswordControl, [{
    key: "_initEvents",
    value: function _initEvents() {
      var _this = this;

      // Show the inputs block when show button is clicked
      $(document).on('click', this.showButtonSelector, function (e) {
        _this._hide($(e.currentTarget));
        _this._showInputsBlock();
      });

      $(document).on('click', this.hideButtonSelector, function () {
        _this._hideInputsBlock();
        _this._show($(_this.showButtonSelector));
      });

      // Watch and display feedback about password's strength
      this.passwordHandler.watchPasswordStrength(this.$newPasswordInputs);

      $(document).on('click', this.generatePasswordButtonSelector, function () {
        // Generate the password into main input.
        _this.passwordHandler.generatePassword(_this.$newPasswordInputs);

        // Copy the generated password from main input to additional inputs
        _this.$copyPasswordInputs.val(_this.$newPasswordInputs.val());
        _this._checkPasswordValidity();
      });

      // Validate new password and it's confirmation when any of the inputs is changed
      $(document).on('keyup', this.newPasswordInputSelector + "," + this.confirmNewPasswordInputSelector, function () {
        _this._checkPasswordValidity();
      });

      // Prevent submitting the form if new password is not valid
      $(document).on('submit', $(this.oldPasswordInputSelector).closest('form'), function (event) {
        // If password input is disabled - we don't need to validate it.
        if ($(_this.oldPasswordInputSelector).is(':disabled')) {
          return;
        }

        if (!_this.passwordValidator.isPasswordValid()) {
          event.preventDefault();
        }
      });
    }

    /**
     * Check if password is valid, show error messages if it's not.
     *
     * @private
     */

  }, {
    key: "_checkPasswordValidity",
    value: function _checkPasswordValidity() {
      var $firstPasswordErrorContainer = $(this.newPasswordInputSelector).parent().find('.form-text');
      var $secondPasswordErrorContainer = $(this.confirmNewPasswordInputSelector).parent().find('.form-text');

      $firstPasswordErrorContainer.text(this._getPasswordLengthValidationMessage()).toggleClass('text-danger', !this.passwordValidator.isPasswordLengthValid());

      $secondPasswordErrorContainer.text(this._getPasswordConfirmationValidationMessage()).toggleClass('text-danger', !this.passwordValidator.isPasswordMatchingConfirmation());
    }

    /**
     * Get password confirmation validation message.
     *
     * @returns {String}
     *
     * @private
     */

  }, {
    key: "_getPasswordConfirmationValidationMessage",
    value: function _getPasswordConfirmationValidationMessage() {
      if (!this.passwordValidator.isPasswordMatchingConfirmation()) {
        return $(this.confirmNewPasswordInputSelector).data('invalid-password');
      }

      return '';
    }

    /**
     * Get password length validation message.
     *
     * @returns {String}
     *
     * @private
     */

  }, {
    key: "_getPasswordLengthValidationMessage",
    value: function _getPasswordLengthValidationMessage() {
      if (this.passwordValidator.isPasswordTooShort()) {
        return $(this.newPasswordInputSelector).data('password-too-short');
      }

      if (this.passwordValidator.isPasswordTooLong()) {
        return $(this.newPasswordInputSelector).data('password-too-long');
      }

      return '';
    }

    /**
     * Show the password inputs block.
     *
     * @private
     */

  }, {
    key: "_showInputsBlock",
    value: function _showInputsBlock() {
      this._show(this.$inputsBlock);
      this.$submittableInputs.removeAttr('disabled');
      this.$submittableInputs.attr('required', 'required');
    }

    /**
     * Hide the password inputs block.
     *
     * @private
     */

  }, {
    key: "_hideInputsBlock",
    value: function _hideInputsBlock() {
      this._hide(this.$inputsBlock);
      this.$submittableInputs.attr('disabled', 'disabled');
      this.$submittableInputs.removeAttr('required');
      this.$inputsBlock.find('input').val('');
      this.$inputsBlock.find('.form-text').text('');
    }

    /**
     * Hide an element.
     *
     * @param {jQuery} $el
     *
     * @private
     */

  }, {
    key: "_hide",
    value: function _hide($el) {
      $el.addClass('d-none');
    }

    /**
     * Show hidden element.
     *
     * @param {jQuery} $el
     *
     * @private
     */

  }, {
    key: "_show",
    value: function _show($el) {
      $el.removeClass('d-none');
    }
  }]);
  return ChangePasswordControl;
}();

exports.default = ChangePasswordControl;

/***/ }),

/***/ 471:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Class responsible for checking password's validity.
 * Can validate entered password's length against min/max values.
 * If password confirmation input is provided, can validate if entered password is matching confirmation.
 */
var PasswordValidator = function () {

  /**
   * @param {String} passwordInputSelector selector of the password input.
   * @param {String|null} confirmPasswordInputSelector (optional) selector for the password confirmation input.
   * @param {Object} options allows overriding default options.
   */
  function PasswordValidator(passwordInputSelector) {
    var confirmPasswordInputSelector = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    (0, _classCallCheck3.default)(this, PasswordValidator);

    this.newPasswordInput = document.querySelector(passwordInputSelector);
    this.confirmPasswordInput = document.querySelector(confirmPasswordInputSelector);

    // Minimum allowed length for entered password
    this.minPasswordLength = options.minPasswordLength || 8;

    // Maximum allowed length for entered password
    this.maxPasswordLength = options.maxPasswordLength || 255;
  }

  /**
   * Check if the password is valid.
   *
   * @returns {boolean}
   */


  (0, _createClass3.default)(PasswordValidator, [{
    key: 'isPasswordValid',
    value: function isPasswordValid() {
      if (this.confirmPasswordInput && !this.isPasswordMatchingConfirmation()) {
        return false;
      }

      return this.isPasswordLengthValid();
    }

    /**
     * Check if password's length is valid.
     *
     * @returns {boolean}
     */

  }, {
    key: 'isPasswordLengthValid',
    value: function isPasswordLengthValid() {
      return !this.isPasswordTooShort() && !this.isPasswordTooLong();
    }

    /**
     * Check if password is matching it's confirmation.
     *
     * @returns {boolean}
     */

  }, {
    key: 'isPasswordMatchingConfirmation',
    value: function isPasswordMatchingConfirmation() {
      if (!this.confirmPasswordInput) {
        throw 'Confirm password input is not provided for the password validator.';
      }

      if (this.confirmPasswordInput.value === '') {
        return true;
      }

      return this.newPasswordInput.value === this.confirmPasswordInput.value;
    }

    /**
     * Check if password is too short.
     *
     * @returns {boolean}
     */

  }, {
    key: 'isPasswordTooShort',
    value: function isPasswordTooShort() {
      return this.newPasswordInput.value.length < this.minPasswordLength;
    }

    /**
     * Check if password is too long.
     *
     * @returns {boolean}
     */

  }, {
    key: 'isPasswordTooLong',
    value: function isPasswordTooLong() {
      return this.newPasswordInput.value.length > this.maxPasswordLength;
    }
  }]);
  return PasswordValidator;
}();

exports.default = PasswordValidator;

/***/ }),

/***/ 493:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Defines all selectors that are used in employee add/edit form.
 */
exports.default = {
  shopChoiceTree: '#employee_shop_association',
  profileSelect: '#employee_profile',
  defaultPageSelect: '#employee_default_page',
  addonsConnectForm: '#addons-connect-form',
  addonsLoginButton: '#addons_login_btn',

  // selectors related to "change password" form control
  changePasswordInputsBlock: '.js-change-password-block',
  showChangePasswordBlockButton: '.js-change-password',
  hideChangePasswordBlockButton: '.js-change-password-cancel',
  generatePasswordButton: '#employee_change_password_generate_password_button',
  oldPasswordInput: '#employee_change_password_old_password',
  newPasswordInput: '#employee_change_password_new_password_first',
  confirmNewPasswordInput: '#employee_change_password_new_password_second',
  generatedPasswordDisplayInput: '#employee_change_password_generated_password',
  passwordStrengthFeedbackContainer: '.js-password-strength-feedback'
};

/***/ }),

/***/ 494:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

var _EmployeeForm = __webpack_require__(411);

var _EmployeeForm2 = _interopRequireDefault(_EmployeeForm);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

$(function () {
  new _EmployeeForm2.default();
}); /**
     * Copyright since 2007 PrestaShop SA and Contributors
     * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
     *
     * NOTICE OF LICENSE
     *
     * This source file is subject to the Open Software License (OSL 3.0)
     * that is bundled with this package in the file LICENSE.md.
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
     * needs please refer to https://devdocs.prestashop.com/ for more information.
     *
     * @author    PrestaShop SA and Contributors <contact@prestashop.com>
     * @copyright Since 2007 PrestaShop SA and Contributors
     * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
     */
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(42)))

/***/ }),

/***/ 5:
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self : Function('return this')();
if(typeof __g == 'number')__g = global; // eslint-disable-line no-undef

/***/ }),

/***/ 6:
/***/ (function(module, exports, __webpack_require__) {

var anObject       = __webpack_require__(11)
  , IE8_DOM_DEFINE = __webpack_require__(17)
  , toPrimitive    = __webpack_require__(14)
  , dP             = Object.defineProperty;

exports.f = __webpack_require__(2) ? Object.defineProperty : function defineProperty(O, P, Attributes){
  anObject(O);
  P = toPrimitive(P, true);
  anObject(Attributes);
  if(IE8_DOM_DEFINE)try {
    return dP(O, P, Attributes);
  } catch(e){ /* empty */ }
  if('get' in Attributes || 'set' in Attributes)throw TypeError('Accessors not supported!');
  if('value' in Attributes)O[P] = Attributes.value;
  return O;
};

/***/ }),

/***/ 61:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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

    (0, _classCallCheck3.default)(this, ChoiceTree);

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


  (0, _createClass3.default)(ChoiceTree, [{
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

/***/ 7:
/***/ (function(module, exports) {

module.exports = function(exec){
  try {
    return !!exec();
  } catch(e){
    return true;
  }
};

/***/ }),

/***/ 8:
/***/ (function(module, exports, __webpack_require__) {

var global    = __webpack_require__(5)
  , core      = __webpack_require__(3)
  , ctx       = __webpack_require__(13)
  , hide      = __webpack_require__(10)
  , PROTOTYPE = 'prototype';

var $export = function(type, name, source){
  var IS_FORCED = type & $export.F
    , IS_GLOBAL = type & $export.G
    , IS_STATIC = type & $export.S
    , IS_PROTO  = type & $export.P
    , IS_BIND   = type & $export.B
    , IS_WRAP   = type & $export.W
    , exports   = IS_GLOBAL ? core : core[name] || (core[name] = {})
    , expProto  = exports[PROTOTYPE]
    , target    = IS_GLOBAL ? global : IS_STATIC ? global[name] : (global[name] || {})[PROTOTYPE]
    , key, own, out;
  if(IS_GLOBAL)source = name;
  for(key in source){
    // contains in native
    own = !IS_FORCED && target && target[key] !== undefined;
    if(own && key in exports)continue;
    // export native or passed
    out = own ? target[key] : source[key];
    // prevent global pollution for namespaces
    exports[key] = IS_GLOBAL && typeof target[key] != 'function' ? source[key]
    // bind timers to global for call from export context
    : IS_BIND && own ? ctx(out, global)
    // wrap global constructors for prevent change them in library
    : IS_WRAP && target[key] == out ? (function(C){
      var F = function(a, b, c){
        if(this instanceof C){
          switch(arguments.length){
            case 0: return new C;
            case 1: return new C(a);
            case 2: return new C(a, b);
          } return new C(a, b, c);
        } return C.apply(this, arguments);
      };
      F[PROTOTYPE] = C[PROTOTYPE];
      return F;
    // make static versions for prototype methods
    })(out) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out;
    // export proto methods to core.%CONSTRUCTOR%.methods.%NAME%
    if(IS_PROTO){
      (exports.virtual || (exports.virtual = {}))[key] = out;
      // export proto methods to core.%CONSTRUCTOR%.prototype.%NAME%
      if(type & $export.R && expProto && !expProto[key])hide(expProto, key, out);
    }
  }
};
// type bitmap
$export.F = 1;   // forced
$export.G = 2;   // global
$export.S = 4;   // static
$export.P = 8;   // proto
$export.B = 16;  // bind
$export.W = 32;  // wrap
$export.U = 64;  // safe
$export.R = 128; // real proto method for `library` 
module.exports = $export;

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgM2E2MTdjZWQyOWViY2NiNmExZDA/MTI1MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY2xhc3NDYWxsQ2hlY2suanM/MjFhZioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2NyZWF0ZUNsYXNzLmpzPzFkZmUqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oaWRlLmpzP2E2ZGEqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2FuLW9iamVjdC5qcz8wZGEzKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19wcm9wZXJ0eS1kZXNjLmpzPzFlODYqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2N0eC5qcz9jZTAwKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1wcmltaXRpdmUuanM/NDlhNCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZG9tLWNyZWF0ZS5qcz9hYjQ0KioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pZTgtZG9tLWRlZmluZS5qcz9iZDFmKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hLWZ1bmN0aW9uLmpzP2Q1M2UqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanM/NWY3MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZGVzY3JpcHRvcnMuanM/NzA1MSoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qcz9iN2Q4KioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuZGVmaW5lLXByb3BlcnR5LmpzP2M4MmMqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NvcmUuanM/MWI2MioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXMtb2JqZWN0LmpzPzI0YzgqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2VtcGxveWVlL0VtcGxveWVlRm9ybS5qcyIsIndlYnBhY2s6Ly8vZXh0ZXJuYWwgXCJqUXVlcnlcIj8wY2I4KioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2FkZG9ucy1jb25uZWN0b3IuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9jaGFuZ2UtcGFzc3dvcmQtaGFuZGxlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2Zvcm0vY2hhbmdlLXBhc3N3b3JkLWNvbnRyb2wuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9wYXNzd29yZC12YWxpZGF0b3IuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvZW1wbG95ZWUvZW1wbG95ZWUtZm9ybS1tYXAuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvZW1wbG95ZWUvZm9ybS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19nbG9iYWwuanM/NzdhYSoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWRwLmpzPzQxMTYqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZm9ybS9jaG9pY2UtdHJlZS5qcz81NDFhKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZmFpbHMuanM/OTM1ZCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZXhwb3J0LmpzP2VjZTIqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIl0sIm5hbWVzIjpbIkVtcGxveWVlRm9ybSIsInNob3BDaG9pY2VUcmVlU2VsZWN0b3IiLCJlbXBsb3llZUZvcm1NYXAiLCJzaG9wQ2hvaWNlVHJlZSIsIkNob2ljZVRyZWUiLCJlbXBsb3llZVByb2ZpbGVTZWxlY3RvciIsInByb2ZpbGVTZWxlY3QiLCJ0YWJzRHJvcGRvd25TZWxlY3RvciIsImRlZmF1bHRQYWdlU2VsZWN0IiwiZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4iLCJBZGRvbnNDb25uZWN0b3IiLCJhZGRvbnNDb25uZWN0Rm9ybSIsImFkZG9uc0xvZ2luQnV0dG9uIiwiQ2hhbmdlUGFzc3dvcmRDb250cm9sIiwiY2hhbmdlUGFzc3dvcmRJbnB1dHNCbG9jayIsInNob3dDaGFuZ2VQYXNzd29yZEJsb2NrQnV0dG9uIiwiaGlkZUNoYW5nZVBhc3N3b3JkQmxvY2tCdXR0b24iLCJnZW5lcmF0ZVBhc3N3b3JkQnV0dG9uIiwib2xkUGFzc3dvcmRJbnB1dCIsIm5ld1Bhc3N3b3JkSW5wdXQiLCJjb25maXJtTmV3UGFzc3dvcmRJbnB1dCIsImdlbmVyYXRlZFBhc3N3b3JkRGlzcGxheUlucHV0IiwicGFzc3dvcmRTdHJlbmd0aEZlZWRiYWNrQ29udGFpbmVyIiwiX2luaXRFdmVudHMiLCJfdG9nZ2xlU2hvcFRyZWUiLCIkZW1wbG95ZWVQcm9maWxlc0Ryb3Bkb3duIiwiJCIsImdldFRhYnNVcmwiLCJkYXRhIiwiZG9jdW1lbnQiLCJvbiIsImV2ZW50IiwiZ2V0IiwicHJvZmlsZUlkIiwiY3VycmVudFRhcmdldCIsInZhbCIsInRhYnMiLCJfcmVsb2FkVGFic0Ryb3Bkb3duIiwiYWNjZXNzaWJsZVRhYnMiLCIkdGFic0Ryb3Bkb3duIiwiZW1wdHkiLCJrZXkiLCJsZW5ndGgiLCIkb3B0Z3JvdXAiLCJfY3JlYXRlT3B0aW9uR3JvdXAiLCJjaGlsZEtleSIsImFwcGVuZCIsIl9jcmVhdGVPcHRpb24iLCIkZW1wbG95ZWVQcm9maWxlRHJvcGRvd24iLCJzdXBlckFkbWluUHJvZmlsZUlkIiwiY2xvc2VzdCIsInRvZ2dsZUNsYXNzIiwibmFtZSIsInZhbHVlIiwid2luZG93IiwiYWRkb25zQ29ubmVjdEZvcm1TZWxlY3RvciIsImxvYWRpbmdTcGlubmVyU2VsZWN0b3IiLCIkbG9hZGluZ1NwaW5uZXIiLCIkZm9ybSIsInByZXZlbnREZWZhdWx0Iiwic3RvcFByb3BhZ2F0aW9uIiwiX2Nvbm5lY3QiLCJhdHRyIiwic2VyaWFsaXplIiwiYWRkb25zQ29ubmVjdFVybCIsImZvcm1EYXRhIiwiYWpheCIsIm1ldGhvZCIsInVybCIsImRhdGFUeXBlIiwiYmVmb3JlU2VuZCIsInNob3ciLCJoaWRlIiwidGhlbiIsInJlc3BvbnNlIiwic3VjY2VzcyIsImxvY2F0aW9uIiwicmVsb2FkIiwiZ3Jvd2wiLCJlcnJvciIsIm1lc3NhZ2UiLCJmYWRlSW4iLCJDaGFuZ2VQYXNzd29yZEhhbmRsZXIiLCJwYXNzd29yZFN0cmVuZ3RoRmVlZGJhY2tDb250YWluZXJTZWxlY3RvciIsIm9wdGlvbnMiLCJtaW5MZW5ndGgiLCIkZmVlZGJhY2tDb250YWluZXIiLCJ3YXRjaFBhc3N3b3JkU3RyZW5ndGgiLCIkaW5wdXQiLCJnZW5lcmF0ZVBhc3N3b3JkIiwicGFzc3kiLCJyZXF1aXJlbWVudHMiLCJtaW4iLCJjaGFyYWN0ZXJzIiwiZWFjaCIsImluZGV4IiwiZWxlbWVudCIsIiRvdXRwdXRDb250YWluZXIiLCJpbnNlcnRBZnRlciIsInN0cmVuZ3RoIiwidmFsaWQiLCJfZGlzcGxheUZlZWRiYWNrIiwicGFzc3dvcmRTdHJlbmd0aCIsImlzUGFzc3dvcmRWYWxpZCIsImZlZWRiYWNrIiwiX2dldFBhc3N3b3JkU3RyZW5ndGhGZWVkYmFjayIsInRleHQiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiZWxlbWVudENsYXNzIiwiTE9XIiwiZmluZCIsIk1FRElVTSIsIkhJR0giLCJFWFRSRU1FIiwiaW5wdXRzQmxvY2tTZWxlY3RvciIsInNob3dCdXR0b25TZWxlY3RvciIsImhpZGVCdXR0b25TZWxlY3RvciIsImdlbmVyYXRlUGFzc3dvcmRCdXR0b25TZWxlY3RvciIsIm9sZFBhc3N3b3JkSW5wdXRTZWxlY3RvciIsIm5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvciIsImNvbmZpcm1OZXdQYXNzd29yZElucHV0U2VsZWN0b3IiLCJnZW5lcmF0ZWRQYXNzd29yZERpc3BsYXlTZWxlY3RvciIsIiRpbnB1dHNCbG9jayIsIiRuZXdQYXNzd29yZElucHV0cyIsIiRjb3B5UGFzc3dvcmRJbnB1dHMiLCJhZGQiLCIkc3VibWl0dGFibGVJbnB1dHMiLCJwYXNzd29yZEhhbmRsZXIiLCJwYXNzd29yZFZhbGlkYXRvciIsIlBhc3N3b3JkVmFsaWRhdG9yIiwiX2hpZGVJbnB1dHNCbG9jayIsImUiLCJfaGlkZSIsIl9zaG93SW5wdXRzQmxvY2siLCJfc2hvdyIsIl9jaGVja1Bhc3N3b3JkVmFsaWRpdHkiLCJpcyIsIiRmaXJzdFBhc3N3b3JkRXJyb3JDb250YWluZXIiLCJwYXJlbnQiLCIkc2Vjb25kUGFzc3dvcmRFcnJvckNvbnRhaW5lciIsIl9nZXRQYXNzd29yZExlbmd0aFZhbGlkYXRpb25NZXNzYWdlIiwiaXNQYXNzd29yZExlbmd0aFZhbGlkIiwiX2dldFBhc3N3b3JkQ29uZmlybWF0aW9uVmFsaWRhdGlvbk1lc3NhZ2UiLCJpc1Bhc3N3b3JkTWF0Y2hpbmdDb25maXJtYXRpb24iLCJpc1Bhc3N3b3JkVG9vU2hvcnQiLCJpc1Bhc3N3b3JkVG9vTG9uZyIsInJlbW92ZUF0dHIiLCIkZWwiLCJwYXNzd29yZElucHV0U2VsZWN0b3IiLCJjb25maXJtUGFzc3dvcmRJbnB1dFNlbGVjdG9yIiwicXVlcnlTZWxlY3RvciIsImNvbmZpcm1QYXNzd29yZElucHV0IiwibWluUGFzc3dvcmRMZW5ndGgiLCJtYXhQYXNzd29yZExlbmd0aCIsInRyZWVTZWxlY3RvciIsIiRjb250YWluZXIiLCIkaW5wdXRXcmFwcGVyIiwiX3RvZ2dsZUNoaWxkVHJlZSIsIiRhY3Rpb24iLCJfdG9nZ2xlVHJlZSIsImVuYWJsZUFsbElucHV0cyIsImRpc2FibGVBbGxJbnB1dHMiLCIkY2xpY2tlZENoZWNrYm94IiwiJGl0ZW1XaXRoQ2hpbGRyZW4iLCJwcm9wIiwiJHBhcmVudFdyYXBwZXIiLCJoYXNDbGFzcyIsIiRwYXJlbnRDb250YWluZXIiLCJhY3Rpb24iLCJjb25maWciLCJleHBhbmQiLCJjb2xsYXBzZSIsIm5leHRBY3Rpb24iLCJpY29uIiwiaXRlbSIsIiRpdGVtIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7Ozs7QUNoRUE7O0FBRUE7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxFOzs7Ozs7OztBQ1JBOztBQUVBOztBQUVBOztBQUVBOztBQUVBLHNDQUFzQyx1Q0FBdUMsZ0JBQWdCOztBQUU3RjtBQUNBO0FBQ0EsbUJBQW1CLGtCQUFrQjtBQUNyQztBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsQ0FBQyxHOzs7Ozs7O0FDMUJEO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsQ0FBQztBQUNEO0FBQ0E7QUFDQSxFOzs7Ozs7O0FDUEE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxFOzs7Ozs7O0FDSkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxFOzs7Ozs7O0FDUEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtB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Q0FBZ0I2RCxNQUFoQixDQURaO0FBRUg7QUFDRixPQVBEOztBQVNBVixjQUFRbkksSUFBUixDQUFhLFFBQWIsRUFBdUI4SSxPQUFPRyxVQUFQLENBQWtCSixNQUFsQixDQUF2QjtBQUNBVixjQUFRaEQsSUFBUixDQUFhLGlCQUFiLEVBQWdDTCxJQUFoQyxDQUFxQ3FELFFBQVFuSSxJQUFSLENBQWE4SSxPQUFPSSxJQUFQLENBQVlMLE1BQVosQ0FBYixDQUFyQztBQUNBVixjQUFRaEQsSUFBUixDQUFhLGlCQUFiLEVBQWdDTCxJQUFoQyxDQUFxQ3FELFFBQVFuSSxJQUFSLENBQWE4SSxPQUFPaEUsSUFBUCxDQUFZK0QsTUFBWixDQUFiLENBQXJDO0FBQ0Q7Ozs7O2tCQTlIa0JySyxVOzs7Ozs7O0FDOUJyQjtBQUNBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBLEU7Ozs7Ozs7QUNOQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsbUVBQW1FO0FBQ25FO0FBQ0EscUZBQXFGO0FBQ3JGO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxXQUFXO0FBQ1gsU0FBUztBQUNUO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsS0FBSztBQUNMO0FBQ0E7QUFDQSwrQ0FBK0M7QUFDL0M7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsY0FBYztBQUNkLGNBQWM7QUFDZCxjQUFjO0FBQ2QsY0FBYztBQUNkLGVBQWU7QUFDZixlQUFlO0FBQ2YsZUFBZTtBQUNmLGdCQUFnQjtBQUNoQix5QiIsImZpbGUiOiJlbXBsb3llZV9mb3JtLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gNDk0KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCAzYTYxN2NlZDI5ZWJjY2I2YTFkMCIsIlwidXNlIHN0cmljdFwiO1xuXG5leHBvcnRzLl9fZXNNb2R1bGUgPSB0cnVlO1xuXG5leHBvcnRzLmRlZmF1bHQgPSBmdW5jdGlvbiAoaW5zdGFuY2UsIENvbnN0cnVjdG9yKSB7XG4gIGlmICghKGluc3RhbmNlIGluc3RhbmNlb2YgQ29uc3RydWN0b3IpKSB7XG4gICAgdGhyb3cgbmV3IFR5cGVFcnJvcihcIkNhbm5vdCBjYWxsIGEgY2xhc3MgYXMgYSBmdW5jdGlvblwiKTtcbiAgfVxufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2NsYXNzQ2FsbENoZWNrLmpzXG4vLyBtb2R1bGUgaWQgPSAwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiXCJ1c2Ugc3RyaWN0XCI7XG5cbmV4cG9ydHMuX19lc01vZHVsZSA9IHRydWU7XG5cbnZhciBfZGVmaW5lUHJvcGVydHkgPSByZXF1aXJlKFwiLi4vY29yZS1qcy9vYmplY3QvZGVmaW5lLXByb3BlcnR5XCIpO1xuXG52YXIgX2RlZmluZVByb3BlcnR5MiA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX2RlZmluZVByb3BlcnR5KTtcblxuZnVuY3Rpb24gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChvYmopIHsgcmV0dXJuIG9iaiAmJiBvYmouX19lc01vZHVsZSA/IG9iaiA6IHsgZGVmYXVsdDogb2JqIH07IH1cblxuZXhwb3J0cy5kZWZhdWx0ID0gZnVuY3Rpb24gKCkge1xuICBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0aWVzKHRhcmdldCwgcHJvcHMpIHtcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IHByb3BzLmxlbmd0aDsgaSsrKSB7XG4gICAgICB2YXIgZGVzY3JpcHRvciA9IHByb3BzW2ldO1xuICAgICAgZGVzY3JpcHRvci5lbnVtZXJhYmxlID0gZGVzY3JpcHRvci5lbnVtZXJhYmxlIHx8IGZhbHNlO1xuICAgICAgZGVzY3JpcHRvci5jb25maWd1cmFibGUgPSB0cnVlO1xuICAgICAgaWYgKFwidmFsdWVcIiBpbiBkZXNjcmlwdG9yKSBkZXNjcmlwdG9yLndyaXRhYmxlID0gdHJ1ZTtcbiAgICAgICgwLCBfZGVmaW5lUHJvcGVydHkyLmRlZmF1bHQpKHRhcmdldCwgZGVzY3JpcHRvci5rZXksIGRlc2NyaXB0b3IpO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiBmdW5jdGlvbiAoQ29uc3RydWN0b3IsIHByb3RvUHJvcHMsIHN0YXRpY1Byb3BzKSB7XG4gICAgaWYgKHByb3RvUHJvcHMpIGRlZmluZVByb3BlcnRpZXMoQ29uc3RydWN0b3IucHJvdG90eXBlLCBwcm90b1Byb3BzKTtcbiAgICBpZiAoc3RhdGljUHJvcHMpIGRlZmluZVByb3BlcnRpZXMoQ29uc3RydWN0b3IsIHN0YXRpY1Byb3BzKTtcbiAgICByZXR1cm4gQ29uc3RydWN0b3I7XG4gIH07XG59KCk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9jcmVhdGVDbGFzcy5qc1xuLy8gbW9kdWxlIGlkID0gMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBkUCAgICAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWRwJylcbiAgLCBjcmVhdGVEZXNjID0gcmVxdWlyZSgnLi9fcHJvcGVydHktZGVzYycpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpID8gZnVuY3Rpb24ob2JqZWN0LCBrZXksIHZhbHVlKXtcbiAgcmV0dXJuIGRQLmYob2JqZWN0LCBrZXksIGNyZWF0ZURlc2MoMSwgdmFsdWUpKTtcbn0gOiBmdW5jdGlvbihvYmplY3QsIGtleSwgdmFsdWUpe1xuICBvYmplY3Rba2V5XSA9IHZhbHVlO1xuICByZXR1cm4gb2JqZWN0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2hpZGUuanNcbi8vIG1vZHVsZSBpZCA9IDEwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIGlmKCFpc09iamVjdChpdCkpdGhyb3cgVHlwZUVycm9yKGl0ICsgJyBpcyBub3QgYW4gb2JqZWN0IScpO1xuICByZXR1cm4gaXQ7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYW4tb2JqZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSAxMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGJpdG1hcCwgdmFsdWUpe1xuICByZXR1cm4ge1xuICAgIGVudW1lcmFibGUgIDogIShiaXRtYXAgJiAxKSxcbiAgICBjb25maWd1cmFibGU6ICEoYml0bWFwICYgMiksXG4gICAgd3JpdGFibGUgICAgOiAhKGJpdG1hcCAmIDQpLFxuICAgIHZhbHVlICAgICAgIDogdmFsdWVcbiAgfTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19wcm9wZXJ0eS1kZXNjLmpzXG4vLyBtb2R1bGUgaWQgPSAxMlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyBvcHRpb25hbCAvIHNpbXBsZSBjb250ZXh0IGJpbmRpbmdcbnZhciBhRnVuY3Rpb24gPSByZXF1aXJlKCcuL19hLWZ1bmN0aW9uJyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGZuLCB0aGF0LCBsZW5ndGgpe1xuICBhRnVuY3Rpb24oZm4pO1xuICBpZih0aGF0ID09PSB1bmRlZmluZWQpcmV0dXJuIGZuO1xuICBzd2l0Y2gobGVuZ3RoKXtcbiAgICBjYXNlIDE6IHJldHVybiBmdW5jdGlvbihhKXtcbiAgICAgIHJldHVybiBmbi5jYWxsKHRoYXQsIGEpO1xuICAgIH07XG4gICAgY2FzZSAyOiByZXR1cm4gZnVuY3Rpb24oYSwgYil7XG4gICAgICByZXR1cm4gZm4uY2FsbCh0aGF0LCBhLCBiKTtcbiAgICB9O1xuICAgIGNhc2UgMzogcmV0dXJuIGZ1bmN0aW9uKGEsIGIsIGMpe1xuICAgICAgcmV0dXJuIGZuLmNhbGwodGhhdCwgYSwgYiwgYyk7XG4gICAgfTtcbiAgfVxuICByZXR1cm4gZnVuY3Rpb24oLyogLi4uYXJncyAqLyl7XG4gICAgcmV0dXJuIGZuLmFwcGx5KHRoYXQsIGFyZ3VtZW50cyk7XG4gIH07XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY3R4LmpzXG4vLyBtb2R1bGUgaWQgPSAxM1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyA3LjEuMSBUb1ByaW1pdGl2ZShpbnB1dCBbLCBQcmVmZXJyZWRUeXBlXSlcbnZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpO1xuLy8gaW5zdGVhZCBvZiB0aGUgRVM2IHNwZWMgdmVyc2lvbiwgd2UgZGlkbid0IGltcGxlbWVudCBAQHRvUHJpbWl0aXZlIGNhc2Vcbi8vIGFuZCB0aGUgc2Vjb25kIGFyZ3VtZW50IC0gZmxhZyAtIHByZWZlcnJlZCB0eXBlIGlzIGEgc3RyaW5nXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0LCBTKXtcbiAgaWYoIWlzT2JqZWN0KGl0KSlyZXR1cm4gaXQ7XG4gIHZhciBmbiwgdmFsO1xuICBpZihTICYmIHR5cGVvZiAoZm4gPSBpdC50b1N0cmluZykgPT0gJ2Z1bmN0aW9uJyAmJiAhaXNPYmplY3QodmFsID0gZm4uY2FsbChpdCkpKXJldHVybiB2YWw7XG4gIGlmKHR5cGVvZiAoZm4gPSBpdC52YWx1ZU9mKSA9PSAnZnVuY3Rpb24nICYmICFpc09iamVjdCh2YWwgPSBmbi5jYWxsKGl0KSkpcmV0dXJuIHZhbDtcbiAgaWYoIVMgJiYgdHlwZW9mIChmbiA9IGl0LnRvU3RyaW5nKSA9PSAnZnVuY3Rpb24nICYmICFpc09iamVjdCh2YWwgPSBmbi5jYWxsKGl0KSkpcmV0dXJuIHZhbDtcbiAgdGhyb3cgVHlwZUVycm9yKFwiQ2FuJ3QgY29udmVydCBvYmplY3QgdG8gcHJpbWl0aXZlIHZhbHVlXCIpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLXByaW1pdGl2ZS5qc1xuLy8gbW9kdWxlIGlkID0gMTRcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGlzT2JqZWN0ID0gcmVxdWlyZSgnLi9faXMtb2JqZWN0JylcbiAgLCBkb2N1bWVudCA9IHJlcXVpcmUoJy4vX2dsb2JhbCcpLmRvY3VtZW50XG4gIC8vIGluIG9sZCBJRSB0eXBlb2YgZG9jdW1lbnQuY3JlYXRlRWxlbWVudCBpcyAnb2JqZWN0J1xuICAsIGlzID0gaXNPYmplY3QoZG9jdW1lbnQpICYmIGlzT2JqZWN0KGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIHJldHVybiBpcyA/IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoaXQpIDoge307XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZG9tLWNyZWF0ZS5qc1xuLy8gbW9kdWxlIGlkID0gMTZcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSAhcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKSAmJiAhcmVxdWlyZSgnLi9fZmFpbHMnKShmdW5jdGlvbigpe1xuICByZXR1cm4gT2JqZWN0LmRlZmluZVByb3BlcnR5KHJlcXVpcmUoJy4vX2RvbS1jcmVhdGUnKSgnZGl2JyksICdhJywge2dldDogZnVuY3Rpb24oKXsgcmV0dXJuIDc7IH19KS5hICE9IDc7XG59KTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2llOC1kb20tZGVmaW5lLmpzXG4vLyBtb2R1bGUgaWQgPSAxN1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgaWYodHlwZW9mIGl0ICE9ICdmdW5jdGlvbicpdGhyb3cgVHlwZUVycm9yKGl0ICsgJyBpcyBub3QgYSBmdW5jdGlvbiEnKTtcbiAgcmV0dXJuIGl0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2EtZnVuY3Rpb24uanNcbi8vIG1vZHVsZSBpZCA9IDE4XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0geyBcImRlZmF1bHRcIjogcmVxdWlyZShcImNvcmUtanMvbGlicmFyeS9mbi9vYmplY3QvZGVmaW5lLXByb3BlcnR5XCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3QvZGVmaW5lLXByb3BlcnR5LmpzXG4vLyBtb2R1bGUgaWQgPSAxOVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyBUaGFuaydzIElFOCBmb3IgaGlzIGZ1bm55IGRlZmluZVByb3BlcnR5XG5tb2R1bGUuZXhwb3J0cyA9ICFyZXF1aXJlKCcuL19mYWlscycpKGZ1bmN0aW9uKCl7XG4gIHJldHVybiBPYmplY3QuZGVmaW5lUHJvcGVydHkoe30sICdhJywge2dldDogZnVuY3Rpb24oKXsgcmV0dXJuIDc7IH19KS5hICE9IDc7XG59KTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2Rlc2NyaXB0b3JzLmpzXG4vLyBtb2R1bGUgaWQgPSAyXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInJlcXVpcmUoJy4uLy4uL21vZHVsZXMvZXM2Lm9iamVjdC5kZWZpbmUtcHJvcGVydHknKTtcbnZhciAkT2JqZWN0ID0gcmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9fY29yZScpLk9iamVjdDtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24gZGVmaW5lUHJvcGVydHkoaXQsIGtleSwgZGVzYyl7XG4gIHJldHVybiAkT2JqZWN0LmRlZmluZVByb3BlcnR5KGl0LCBrZXksIGRlc2MpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanNcbi8vIG1vZHVsZSBpZCA9IDIwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciAkZXhwb3J0ID0gcmVxdWlyZSgnLi9fZXhwb3J0Jyk7XG4vLyAxOS4xLjIuNCAvIDE1LjIuMy42IE9iamVjdC5kZWZpbmVQcm9wZXJ0eShPLCBQLCBBdHRyaWJ1dGVzKVxuJGV4cG9ydCgkZXhwb3J0LlMgKyAkZXhwb3J0LkYgKiAhcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKSwgJ09iamVjdCcsIHtkZWZpbmVQcm9wZXJ0eTogcmVxdWlyZSgnLi9fb2JqZWN0LWRwJykuZn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYub2JqZWN0LmRlZmluZS1wcm9wZXJ0eS5qc1xuLy8gbW9kdWxlIGlkID0gMjFcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGNvcmUgPSBtb2R1bGUuZXhwb3J0cyA9IHt2ZXJzaW9uOiAnMi40LjAnfTtcbmlmKHR5cGVvZiBfX2UgPT0gJ251bWJlcicpX19lID0gY29yZTsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBuby11bmRlZlxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY29yZS5qc1xuLy8gbW9kdWxlIGlkID0gM1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIHR5cGVvZiBpdCA9PT0gJ29iamVjdCcgPyBpdCAhPT0gbnVsbCA6IHR5cGVvZiBpdCA9PT0gJ2Z1bmN0aW9uJztcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pcy1vYmplY3QuanNcbi8vIG1vZHVsZSBpZCA9IDRcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgQ2hvaWNlVHJlZSBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9mb3JtL2Nob2ljZS10cmVlXCI7XG5pbXBvcnQgQWRkb25zQ29ubmVjdG9yIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2FkZG9ucy1jb25uZWN0b3JcIjtcbmltcG9ydCBDaGFuZ2VQYXNzd29yZENvbnRyb2wgZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZm9ybS9jaGFuZ2UtcGFzc3dvcmQtY29udHJvbFwiO1xuaW1wb3J0IGVtcGxveWVlRm9ybU1hcCBmcm9tIFwiLi9lbXBsb3llZS1mb3JtLW1hcFwiO1xuXG4vKipcbiAqIENsYXNzIHJlc3BvbnNpYmxlIGZvciBqYXZhc2NyaXB0IGFjdGlvbnMgaW4gZW1wbG95ZWUgYWRkL2VkaXQgcGFnZS5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRW1wbG95ZWVGb3JtIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5zaG9wQ2hvaWNlVHJlZVNlbGVjdG9yID0gZW1wbG95ZWVGb3JtTWFwLnNob3BDaG9pY2VUcmVlO1xuICAgIHRoaXMuc2hvcENob2ljZVRyZWUgPSBuZXcgQ2hvaWNlVHJlZSh0aGlzLnNob3BDaG9pY2VUcmVlU2VsZWN0b3IpO1xuICAgIHRoaXMuZW1wbG95ZWVQcm9maWxlU2VsZWN0b3IgPSBlbXBsb3llZUZvcm1NYXAucHJvZmlsZVNlbGVjdDtcbiAgICB0aGlzLnRhYnNEcm9wZG93blNlbGVjdG9yID0gZW1wbG95ZWVGb3JtTWFwLmRlZmF1bHRQYWdlU2VsZWN0O1xuXG4gICAgdGhpcy5zaG9wQ2hvaWNlVHJlZS5lbmFibGVBdXRvQ2hlY2tDaGlsZHJlbigpO1xuXG4gICAgbmV3IEFkZG9uc0Nvbm5lY3RvcihcbiAgICAgIGVtcGxveWVlRm9ybU1hcC5hZGRvbnNDb25uZWN0Rm9ybSxcbiAgICAgIGVtcGxveWVlRm9ybU1hcC5hZGRvbnNMb2dpbkJ1dHRvblxuICAgICk7XG5cbiAgICBuZXcgQ2hhbmdlUGFzc3dvcmRDb250cm9sKFxuICAgICAgZW1wbG95ZWVGb3JtTWFwLmNoYW5nZVBhc3N3b3JkSW5wdXRzQmxvY2ssXG4gICAgICBlbXBsb3llZUZvcm1NYXAuc2hvd0NoYW5nZVBhc3N3b3JkQmxvY2tCdXR0b24sXG4gICAgICBlbXBsb3llZUZvcm1NYXAuaGlkZUNoYW5nZVBhc3N3b3JkQmxvY2tCdXR0b24sXG4gICAgICBlbXBsb3llZUZvcm1NYXAuZ2VuZXJhdGVQYXNzd29yZEJ1dHRvbixcbiAgICAgIGVtcGxveWVlRm9ybU1hcC5vbGRQYXNzd29yZElucHV0LFxuICAgICAgZW1wbG95ZWVGb3JtTWFwLm5ld1Bhc3N3b3JkSW5wdXQsXG4gICAgICBlbXBsb3llZUZvcm1NYXAuY29uZmlybU5ld1Bhc3N3b3JkSW5wdXQsXG4gICAgICBlbXBsb3llZUZvcm1NYXAuZ2VuZXJhdGVkUGFzc3dvcmREaXNwbGF5SW5wdXQsXG4gICAgICBlbXBsb3llZUZvcm1NYXAucGFzc3dvcmRTdHJlbmd0aEZlZWRiYWNrQ29udGFpbmVyXG4gICAgKTtcblxuICAgIHRoaXMuX2luaXRFdmVudHMoKTtcbiAgICB0aGlzLl90b2dnbGVTaG9wVHJlZSgpO1xuXG4gICAgcmV0dXJuIHt9O1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRpYWxpemUgcGFnZSdzIGV2ZW50cy5cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0RXZlbnRzKCkge1xuICAgIGNvbnN0ICRlbXBsb3llZVByb2ZpbGVzRHJvcGRvd24gPSAkKHRoaXMuZW1wbG95ZWVQcm9maWxlU2VsZWN0b3IpO1xuICAgIGNvbnN0IGdldFRhYnNVcmwgPSAkZW1wbG95ZWVQcm9maWxlc0Ryb3Bkb3duLmRhdGEoJ2dldC10YWJzLXVybCcpO1xuXG4gICAgJChkb2N1bWVudCkub24oJ2NoYW5nZScsIHRoaXMuZW1wbG95ZWVQcm9maWxlU2VsZWN0b3IsICgpID0+IHRoaXMuX3RvZ2dsZVNob3BUcmVlKCkpO1xuXG4gICAgLy8gUmVsb2FkIHRhYnMgZHJvcGRvd24gd2hlbiBlbXBsb3llZSBwcm9maWxlIGlzIGNoYW5nZWQuXG4gICAgJChkb2N1bWVudCkub24oJ2NoYW5nZScsIHRoaXMuZW1wbG95ZWVQcm9maWxlU2VsZWN0b3IsIChldmVudCkgPT4ge1xuICAgICAgJC5nZXQoXG4gICAgICAgIGdldFRhYnNVcmwsXG4gICAgICAgIHtcbiAgICAgICAgICBwcm9maWxlSWQ6ICQoZXZlbnQuY3VycmVudFRhcmdldCkudmFsKClcbiAgICAgICAgfSxcbiAgICAgICAgKHRhYnMpID0+IHtcbiAgICAgICAgICB0aGlzLl9yZWxvYWRUYWJzRHJvcGRvd24odGFicyk7XG4gICAgICAgIH0sXG4gICAgICAgICdqc29uJ1xuICAgICAgKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZWxvYWQgdGFicyBkcm9wZG93biB3aXRoIG5ldyBjb250ZW50LlxuICAgKlxuICAgKiBAcGFyYW0ge09iamVjdH0gYWNjZXNzaWJsZVRhYnNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZWxvYWRUYWJzRHJvcGRvd24oYWNjZXNzaWJsZVRhYnMpIHtcbiAgICBjb25zdCAkdGFic0Ryb3Bkb3duID0gJCh0aGlzLnRhYnNEcm9wZG93blNlbGVjdG9yKTtcblxuICAgICR0YWJzRHJvcGRvd24uZW1wdHkoKTtcblxuICAgIGZvciAobGV0IGtleSBpbiBhY2Nlc3NpYmxlVGFicykge1xuICAgICAgaWYgKGFjY2Vzc2libGVUYWJzW2tleV1bJ2NoaWxkcmVuJ10ubGVuZ3RoID4gMCAmJiBhY2Nlc3NpYmxlVGFic1trZXldWyduYW1lJ10pIHtcbiAgICAgICAgLy8gSWYgdGFiIGhhcyBjaGlsZHJlbiAtIGNyZWF0ZSBhbiBvcHRpb24gZ3JvdXAgYW5kIHB1dCBjaGlsZHJlbiBpbnNpZGUuXG4gICAgICAgIGNvbnN0ICRvcHRncm91cCA9IHRoaXMuX2NyZWF0ZU9wdGlvbkdyb3VwKGFjY2Vzc2libGVUYWJzW2tleV1bJ25hbWUnXSk7XG5cbiAgICAgICAgZm9yIChsZXQgY2hpbGRLZXkgaW4gYWNjZXNzaWJsZVRhYnNba2V5XVsnY2hpbGRyZW4nXSkge1xuICAgICAgICAgIGlmIChhY2Nlc3NpYmxlVGFic1trZXldWydjaGlsZHJlbiddW2NoaWxkS2V5XVsnbmFtZSddKSB7XG4gICAgICAgICAgICAkb3B0Z3JvdXAuYXBwZW5kKFxuICAgICAgICAgICAgICB0aGlzLl9jcmVhdGVPcHRpb24oXG4gICAgICAgICAgICAgICAgYWNjZXNzaWJsZVRhYnNba2V5XVsnY2hpbGRyZW4nXVtjaGlsZEtleV1bJ25hbWUnXSxcbiAgICAgICAgICAgICAgICBhY2Nlc3NpYmxlVGFic1trZXldWydjaGlsZHJlbiddW2NoaWxkS2V5XVsnaWRfdGFiJ10pXG4gICAgICAgICAgICApO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgICR0YWJzRHJvcGRvd24uYXBwZW5kKCRvcHRncm91cCk7XG4gICAgICB9IGVsc2UgaWYgKGFjY2Vzc2libGVUYWJzW2tleV1bJ25hbWUnXSkge1xuICAgICAgICAvLyBJZiB0YWIgZG9lc24ndCBoYXZlIGNoaWxkcmVuIC0gY3JlYXRlIGFuIG9wdGlvbi5cbiAgICAgICAgJHRhYnNEcm9wZG93bi5hcHBlbmQoXG4gICAgICAgICAgdGhpcy5fY3JlYXRlT3B0aW9uKFxuICAgICAgICAgICAgYWNjZXNzaWJsZVRhYnNba2V5XVsnbmFtZSddLFxuICAgICAgICAgICAgYWNjZXNzaWJsZVRhYnNba2V5XVsnaWRfdGFiJ11cbiAgICAgICAgICApXG4gICAgICAgICk7XG4gICAgICB9XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEhpZGUgc2hvcCBjaG9pY2UgdHJlZSBpZiBzdXBlcmFkbWluIHByb2ZpbGUgaXMgc2VsZWN0ZWQsIHNob3cgaXQgb3RoZXJ3aXNlLlxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3RvZ2dsZVNob3BUcmVlKCkge1xuICAgIGNvbnN0ICRlbXBsb3llZVByb2ZpbGVEcm9wZG93biA9ICQodGhpcy5lbXBsb3llZVByb2ZpbGVTZWxlY3Rvcik7XG4gICAgY29uc3Qgc3VwZXJBZG1pblByb2ZpbGVJZCA9ICRlbXBsb3llZVByb2ZpbGVEcm9wZG93bi5kYXRhKCdhZG1pbi1wcm9maWxlJyk7XG4gICAgJCh0aGlzLnNob3BDaG9pY2VUcmVlU2VsZWN0b3IpXG4gICAgICAuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKVxuICAgICAgLnRvZ2dsZUNsYXNzKCdkLW5vbmUnLCAkZW1wbG95ZWVQcm9maWxlRHJvcGRvd24udmFsKCkgPT0gc3VwZXJBZG1pblByb2ZpbGVJZClcbiAgICA7XG4gIH1cblxuICAvKipcbiAgICogQ3JlYXRlcyBhbiA8b3B0Z3JvdXA+IGVsZW1lbnRcbiAgICpcbiAgICogQHBhcmFtIHtTdHJpbmd9IG5hbWVcbiAgICpcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9jcmVhdGVPcHRpb25Hcm91cChuYW1lKSB7XG4gICAgcmV0dXJuICQoYDxvcHRncm91cCBsYWJlbD1cIiR7bmFtZX1cIj5gKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDcmVhdGVzIGFuIDxvcHRpb24+IGVsZW1lbnQuXG4gICAqXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lXG4gICAqIEBwYXJhbSB7U3RyaW5nfSB2YWx1ZVxuICAgKlxuICAgKiBAcmV0dXJucyB7alF1ZXJ5fVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NyZWF0ZU9wdGlvbihuYW1lLCB2YWx1ZSkge1xuICAgIHJldHVybiAkKGA8b3B0aW9uIHZhbHVlPVwiJHt2YWx1ZX1cIj4ke25hbWV9PC9vcHRpb24+YCk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2VtcGxveWVlL0VtcGxveWVlRm9ybS5qcyIsIihmdW5jdGlvbigpIHsgbW9kdWxlLmV4cG9ydHMgPSB3aW5kb3dbXCJqUXVlcnlcIl07IH0oKSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gZXh0ZXJuYWwgXCJqUXVlcnlcIlxuLy8gbW9kdWxlIGlkID0gNDJcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiA1IDYgNyA4IDkgMTcgMjYgMzAgNDQgNDgiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBSZXNwb25zaWJsZSBmb3IgY29ubmVjdGluZyB0byBhZGRvbnMgbWFya2V0cGxhY2UuXG4gKiBNYWtlcyBhbiBhZGRvbnMgY29ubmVjdCByZXF1ZXN0IHRvIHRoZSBzZXJ2ZXIsIGRpc3BsYXlzIGVycm9yIG1lc3NhZ2VzIGlmIGl0IGZhaWxzLlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBBZGRvbnNDb25uZWN0b3Ige1xuICBjb25zdHJ1Y3RvcihcbiAgICBhZGRvbnNDb25uZWN0Rm9ybVNlbGVjdG9yLFxuICAgIGxvYWRpbmdTcGlubmVyU2VsZWN0b3JcbiAgKSB7XG4gICAgdGhpcy5hZGRvbnNDb25uZWN0Rm9ybVNlbGVjdG9yID0gYWRkb25zQ29ubmVjdEZvcm1TZWxlY3RvcjtcbiAgICB0aGlzLiRsb2FkaW5nU3Bpbm5lciA9ICQobG9hZGluZ1NwaW5uZXJTZWxlY3Rvcik7XG5cbiAgICB0aGlzLl9pbml0RXZlbnRzKCk7XG5cbiAgICByZXR1cm4ge307XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhbGl6ZSBldmVudHMgcmVsYXRlZCB0byBjb25uZWN0aW9uIHRvIGFkZG9ucy5cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0RXZlbnRzKCkge1xuICAgICQoJ2JvZHknKS5vbignc3VibWl0JywgdGhpcy5hZGRvbnNDb25uZWN0Rm9ybVNlbGVjdG9yLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRmb3JtID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblxuICAgICAgdGhpcy5fY29ubmVjdCgkZm9ybS5hdHRyKCdhY3Rpb24nKSwgJGZvcm0uc2VyaWFsaXplKCkpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIERvIGEgUE9TVCByZXF1ZXN0IHRvIGNvbm5lY3QgdG8gYWRkb25zLlxuICAgKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gYWRkb25zQ29ubmVjdFVybFxuICAgKiBAcGFyYW0ge09iamVjdH0gZm9ybURhdGFcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9jb25uZWN0KGFkZG9uc0Nvbm5lY3RVcmwsIGZvcm1EYXRhKSB7XG4gICAgJC5hamF4KHtcbiAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgICAgdXJsOiBhZGRvbnNDb25uZWN0VXJsLFxuICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgIGRhdGE6IGZvcm1EYXRhLFxuICAgICAgYmVmb3JlU2VuZDogKCkgPT4ge1xuICAgICAgICB0aGlzLiRsb2FkaW5nU3Bpbm5lci5zaG93KCk7XG4gICAgICAgICQoJ2J1dHRvbi5idG5bdHlwZT1cInN1Ym1pdFwiXScsIHRoaXMuYWRkb25zQ29ubmVjdEZvcm1TZWxlY3RvcikuaGlkZSgpO1xuICAgICAgfVxuICAgIH0pLnRoZW4oKHJlc3BvbnNlKSA9PiB7XG4gICAgICBpZiAocmVzcG9uc2Uuc3VjY2VzcyA9PT0gMSkge1xuICAgICAgICBsb2NhdGlvbi5yZWxvYWQoKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgICQuZ3Jvd2wuZXJyb3Ioe1xuICAgICAgICAgIG1lc3NhZ2U6IHJlc3BvbnNlLm1lc3NhZ2VcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy4kbG9hZGluZ1NwaW5uZXIuaGlkZSgpO1xuICAgICAgICAkKCdidXR0b24uYnRuW3R5cGU9XCJzdWJtaXRcIl0nLCB0aGlzLmFkZG9uc0Nvbm5lY3RGb3JtU2VsZWN0b3IpLmZhZGVJbigpO1xuICAgICAgfVxuICAgIH0sICgpID0+IHtcbiAgICAgICQuZ3Jvd2wuZXJyb3Ioe1xuICAgICAgICBtZXNzYWdlOiAkKHRoaXMuYWRkb25zQ29ubmVjdEZvcm1TZWxlY3RvcikuZGF0YSgnZXJyb3ItbWVzc2FnZScpLFxuICAgICAgfSk7XG5cbiAgICAgIHRoaXMuJGxvYWRpbmdTcGlubmVyLmhpZGUoKTtcbiAgICAgICQoJ2J1dHRvbi5idG5bdHlwZT1cInN1Ym1pdFwiXScsIHRoaXMuYWRkb25zQ29ubmVjdEZvcm1TZWxlY3Rvcikuc2hvdygpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2FkZG9ucy1jb25uZWN0b3IuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBHZW5lcmF0ZXMgYSBwYXNzd29yZCBhbmQgaW5mb3JtcyBhYm91dCBpdCdzIHN0cmVuZ3RoLlxuICogWW91IGNhbiBwYXNzIGEgcGFzc3dvcmQgaW5wdXQgdG8gd2F0Y2ggdGhlIHBhc3N3b3JkIHN0cmVuZ3RoIGFuZCBkaXNwbGF5IGZlZWRiYWNrIG1lc3NhZ2VzLlxuICogWW91IGNhbiBhbHNvIGdlbmVyYXRlIGEgcmFuZG9tIHBhc3N3b3JkIGludG8gYW4gaW5wdXQuXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENoYW5nZVBhc3N3b3JkSGFuZGxlciB7XG4gIGNvbnN0cnVjdG9yKHBhc3N3b3JkU3RyZW5ndGhGZWVkYmFja0NvbnRhaW5lclNlbGVjdG9yLCBvcHRpb25zID0ge30pIHtcbiAgICAvLyBNaW5pbXVtIGxlbmd0aCBvZiB0aGUgZ2VuZXJhdGVkIHBhc3N3b3JkLlxuICAgIHRoaXMubWluTGVuZ3RoID0gb3B0aW9ucy5taW5MZW5ndGggfHwgODtcblxuICAgIC8vIEZlZWRiYWNrIGNvbnRhaW5lciBob2xkcyBtZXNzYWdlcyByZXByZXNlbnRpbmcgcGFzc3dvcmQgc3RyZW5ndGguXG4gICAgdGhpcy4kZmVlZGJhY2tDb250YWluZXIgPSAkKHBhc3N3b3JkU3RyZW5ndGhGZWVkYmFja0NvbnRhaW5lclNlbGVjdG9yKTtcblxuICAgIHJldHVybiB7XG4gICAgICB3YXRjaFBhc3N3b3JkU3RyZW5ndGg6ICgkaW5wdXQpID0+IHRoaXMud2F0Y2hQYXNzd29yZFN0cmVuZ3RoKCRpbnB1dCksXG4gICAgICBnZW5lcmF0ZVBhc3N3b3JkOiAoJGlucHV0KSA9PiB0aGlzLmdlbmVyYXRlUGFzc3dvcmQoJGlucHV0KSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIFdhdGNoIHBhc3N3b3JkLCB3aGljaCBpcyBlbnRlcmVkIGluIHRoZSBpbnB1dCwgc3RyZW5ndGggYW5kIGluZm9ybSBhYm91dCBpdC5cbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRpbnB1dCB0aGUgaW5wdXQgdG8gd2F0Y2guXG4gICAqL1xuICB3YXRjaFBhc3N3b3JkU3RyZW5ndGgoJGlucHV0KSB7XG4gICAgJC5wYXNzeS5yZXF1aXJlbWVudHMubGVuZ3RoLm1pbiA9IHRoaXMubWluTGVuZ3RoO1xuICAgICQucGFzc3kucmVxdWlyZW1lbnRzLmNoYXJhY3RlcnMgPSAnRElHSVQnO1xuXG4gICAgJGlucHV0LmVhY2goKGluZGV4LCBlbGVtZW50KSA9PiB7XG4gICAgICBjb25zdCAkb3V0cHV0Q29udGFpbmVyID0gJCgnPHNwYW4+Jyk7XG5cbiAgICAgICRvdXRwdXRDb250YWluZXIuaW5zZXJ0QWZ0ZXIoJChlbGVtZW50KSk7XG5cbiAgICAgICQoZWxlbWVudCkucGFzc3koKHN0cmVuZ3RoLCB2YWxpZCkgPT4ge1xuICAgICAgICB0aGlzLl9kaXNwbGF5RmVlZGJhY2soJG91dHB1dENvbnRhaW5lciwgc3RyZW5ndGgsIHZhbGlkKTtcbiAgICAgIH0pO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEdlbmVyYXRlcyBhIHBhc3N3b3JkIGFuZCBmaWxscyBpdCB0byBnaXZlbiBpbnB1dC5cbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRpbnB1dCB0aGUgaW5wdXQgdG8gZmlsbCB0aGUgcGFzc3dvcmQgaW50by5cbiAgICovXG4gIGdlbmVyYXRlUGFzc3dvcmQoJGlucHV0KSB7XG4gICAgJGlucHV0LnBhc3N5KCdnZW5lcmF0ZScsIHRoaXMubWluTGVuZ3RoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNwbGF5IGZlZWRiYWNrIGFib3V0IHBhc3N3b3JkJ3Mgc3RyZW5ndGguXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkb3V0cHV0Q29udGFpbmVyIGEgY29udGFpbmVyIHRvIHB1dCBmZWVkYmFjayBvdXRwdXQgaW50by5cbiAgICogQHBhcmFtIHtudW1iZXJ9IHBhc3N3b3JkU3RyZW5ndGhcbiAgICogQHBhcmFtIHtib29sZWFufSBpc1Bhc3N3b3JkVmFsaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9kaXNwbGF5RmVlZGJhY2soJG91dHB1dENvbnRhaW5lciwgcGFzc3dvcmRTdHJlbmd0aCwgaXNQYXNzd29yZFZhbGlkKSB7XG4gICAgY29uc3QgZmVlZGJhY2sgPSB0aGlzLl9nZXRQYXNzd29yZFN0cmVuZ3RoRmVlZGJhY2socGFzc3dvcmRTdHJlbmd0aCk7XG4gICAgJG91dHB1dENvbnRhaW5lci50ZXh0KGZlZWRiYWNrLm1lc3NhZ2UpO1xuICAgICRvdXRwdXRDb250YWluZXIucmVtb3ZlQ2xhc3MoJ3RleHQtZGFuZ2VyIHRleHQtd2FybmluZyB0ZXh0LXN1Y2Nlc3MnKTtcbiAgICAkb3V0cHV0Q29udGFpbmVyLmFkZENsYXNzKGZlZWRiYWNrLmVsZW1lbnRDbGFzcyk7XG4gICAgJG91dHB1dENvbnRhaW5lci50b2dnbGVDbGFzcygnZC1ub25lJywgIWlzUGFzc3dvcmRWYWxpZCk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGZlZWRiYWNrIHRoYXQgZGVzY3JpYmVzIGdpdmVuIHBhc3N3b3JkIHN0cmVuZ3RoLlxuICAgKiBSZXNwb25zZSBjb250YWlucyB0ZXh0IG1lc3NhZ2UgYW5kIGVsZW1lbnQgY2xhc3MuXG4gICAqXG4gICAqIEBwYXJhbSB7bnVtYmVyfSBzdHJlbmd0aFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldFBhc3N3b3JkU3RyZW5ndGhGZWVkYmFjayhzdHJlbmd0aCkge1xuICAgIHN3aXRjaCAoc3RyZW5ndGgpIHtcbiAgICAgIGNhc2UgJC5wYXNzeS5zdHJlbmd0aC5MT1c6XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgbWVzc2FnZTogdGhpcy4kZmVlZGJhY2tDb250YWluZXIuZmluZCgnLnN0cmVuZ3RoLWxvdycpLnRleHQoKSxcbiAgICAgICAgICBlbGVtZW50Q2xhc3M6ICd0ZXh0LWRhbmdlcicsXG4gICAgICAgIH07XG5cbiAgICAgIGNhc2UgJC5wYXNzeS5zdHJlbmd0aC5NRURJVU06XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgbWVzc2FnZTogdGhpcy4kZmVlZGJhY2tDb250YWluZXIuZmluZCgnLnN0cmVuZ3RoLW1lZGl1bScpLnRleHQoKSxcbiAgICAgICAgICBlbGVtZW50Q2xhc3M6ICd0ZXh0LXdhcm5pbmcnLFxuICAgICAgICB9O1xuXG4gICAgICBjYXNlICQucGFzc3kuc3RyZW5ndGguSElHSDpcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICBtZXNzYWdlOiB0aGlzLiRmZWVkYmFja0NvbnRhaW5lci5maW5kKCcuc3RyZW5ndGgtaGlnaCcpLnRleHQoKSxcbiAgICAgICAgICBlbGVtZW50Q2xhc3M6ICd0ZXh0LXN1Y2Nlc3MnLFxuICAgICAgICB9O1xuXG4gICAgICBjYXNlICQucGFzc3kuc3RyZW5ndGguRVhUUkVNRTpcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICBtZXNzYWdlOiB0aGlzLiRmZWVkYmFja0NvbnRhaW5lci5maW5kKCcuc3RyZW5ndGgtZXh0cmVtZScpLnRleHQoKSxcbiAgICAgICAgICBlbGVtZW50Q2xhc3M6ICd0ZXh0LXN1Y2Nlc3MnLFxuICAgICAgICB9O1xuICAgIH1cblxuICAgIHRocm93ICdJbnZhbGlkIHBhc3N3b3JkIHN0cmVuZ3RoIGluZGljYXRvci4nO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2NoYW5nZS1wYXNzd29yZC1oYW5kbGVyLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgQ2hhbmdlUGFzc3dvcmRIYW5kbGVyIGZyb20gXCIuLi9jaGFuZ2UtcGFzc3dvcmQtaGFuZGxlclwiO1xuaW1wb3J0IFBhc3N3b3JkVmFsaWRhdG9yIGZyb20gXCIuLi9wYXNzd29yZC12YWxpZGF0b3JcIjtcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIHJlc3BvbnNpYmxlIGZvciBhY3Rpb25zIHJlbGF0ZWQgdG8gXCJjaGFuZ2UgcGFzc3dvcmRcIiBmb3JtIHR5cGUuXG4gKiBHZW5lcmF0ZXMgcmFuZG9tIHBhc3N3b3JkcywgdmFsaWRhdGVzIG5ldyBwYXNzd29yZCBhbmQgaXQncyBjb25maXJtYXRpb24sXG4gKiBkaXNwbGF5cyBlcnJvciBtZXNzYWdlcyByZWxhdGVkIHRvIHZhbGlkYXRpb24uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENoYW5nZVBhc3N3b3JkQ29udHJvbCB7XG4gIGNvbnN0cnVjdG9yKFxuICAgIGlucHV0c0Jsb2NrU2VsZWN0b3IsXG4gICAgc2hvd0J1dHRvblNlbGVjdG9yLFxuICAgIGhpZGVCdXR0b25TZWxlY3RvcixcbiAgICBnZW5lcmF0ZVBhc3N3b3JkQnV0dG9uU2VsZWN0b3IsXG4gICAgb2xkUGFzc3dvcmRJbnB1dFNlbGVjdG9yLFxuICAgIG5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvcixcbiAgICBjb25maXJtTmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yLFxuICAgIGdlbmVyYXRlZFBhc3N3b3JkRGlzcGxheVNlbGVjdG9yLFxuICAgIHBhc3N3b3JkU3RyZW5ndGhGZWVkYmFja0NvbnRhaW5lclNlbGVjdG9yXG4gICkge1xuICAgIC8vIEJsb2NrIHRoYXQgY29udGFpbnMgcGFzc3dvcmQgaW5wdXRzXG4gICAgdGhpcy4kaW5wdXRzQmxvY2sgPSAkKGlucHV0c0Jsb2NrU2VsZWN0b3IpO1xuXG4gICAgLy8gQnV0dG9uIHRoYXQgc2hvd3MgdGhlIHBhc3N3b3JkIGlucHV0cyBibG9ja1xuICAgIHRoaXMuc2hvd0J1dHRvblNlbGVjdG9yID0gc2hvd0J1dHRvblNlbGVjdG9yO1xuXG4gICAgLy8gQnV0dG9uIHRoYXQgaGlkZXMgdGhlIHBhc3N3b3JkIGlucHV0cyBibG9ja1xuICAgIHRoaXMuaGlkZUJ1dHRvblNlbGVjdG9yID0gaGlkZUJ1dHRvblNlbGVjdG9yO1xuXG4gICAgLy8gQnV0dG9uIHRoYXQgZ2VuZXJhdGVzIGEgcmFuZG9tIHBhc3N3b3JkXG4gICAgdGhpcy5nZW5lcmF0ZVBhc3N3b3JkQnV0dG9uU2VsZWN0b3IgPSBnZW5lcmF0ZVBhc3N3b3JkQnV0dG9uU2VsZWN0b3I7XG5cbiAgICAvLyBJbnB1dCB0byBlbnRlciBvbGQgcGFzc3dvcmRcbiAgICB0aGlzLm9sZFBhc3N3b3JkSW5wdXRTZWxlY3RvciA9IG9sZFBhc3N3b3JkSW5wdXRTZWxlY3RvcjtcblxuICAgIC8vIElucHV0IHRvIGVudGVyIG5ldyBwYXNzd29yZFxuICAgIHRoaXMubmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yID0gbmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yO1xuXG4gICAgLy8gSW5wdXQgdG8gY29uZmlybSB0aGUgbmV3IHBhc3N3b3JkXG4gICAgdGhpcy5jb25maXJtTmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yID0gY29uZmlybU5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvcjtcblxuICAgIC8vIElucHV0IHRoYXQgZGlzcGxheXMgZ2VuZXJhdGVkIHJhbmRvbSBwYXNzd29yZFxuICAgIHRoaXMuZ2VuZXJhdGVkUGFzc3dvcmREaXNwbGF5U2VsZWN0b3IgPSBnZW5lcmF0ZWRQYXNzd29yZERpc3BsYXlTZWxlY3RvcjtcblxuICAgIC8vIE1haW4gaW5wdXQgZm9yIHBhc3N3b3JkIGdlbmVyYXRpb25cbiAgICB0aGlzLiRuZXdQYXNzd29yZElucHV0cyA9IHRoaXMuJGlucHV0c0Jsb2NrXG4gICAgICAuZmluZCh0aGlzLm5ld1Bhc3N3b3JkSW5wdXRTZWxlY3Rvcik7XG5cbiAgICAvLyBHZW5lcmF0ZWQgcGFzc3dvcmQgd2lsbCBiZSBjb3BpZWQgdG8gdGhlc2UgaW5wdXRzXG4gICAgdGhpcy4kY29weVBhc3N3b3JkSW5wdXRzID0gdGhpcy4kaW5wdXRzQmxvY2tcbiAgICAgIC5maW5kKHRoaXMuY29uZmlybU5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvcilcbiAgICAgIC5hZGQodGhpcy5nZW5lcmF0ZWRQYXNzd29yZERpc3BsYXlTZWxlY3Rvcik7XG5cbiAgICAvLyBBbGwgaW5wdXRzIGluIHRoZSBjaGFuZ2UgcGFzc3dvcmQgYmxvY2ssIHRoYXQgYXJlIHN1Ym1pdHRhYmxlIHdpdGggdGhlIGZvcm0uXG4gICAgdGhpcy4kc3VibWl0dGFibGVJbnB1dHMgPSB0aGlzLiRpbnB1dHNCbG9ja1xuICAgICAgLmZpbmQodGhpcy5vbGRQYXNzd29yZElucHV0U2VsZWN0b3IpXG4gICAgICAuYWRkKHRoaXMubmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yKVxuICAgICAgLmFkZCh0aGlzLmNvbmZpcm1OZXdQYXNzd29yZElucHV0U2VsZWN0b3IpO1xuXG4gICAgdGhpcy5wYXNzd29yZEhhbmRsZXIgPSBuZXcgQ2hhbmdlUGFzc3dvcmRIYW5kbGVyKFxuICAgICAgcGFzc3dvcmRTdHJlbmd0aEZlZWRiYWNrQ29udGFpbmVyU2VsZWN0b3JcbiAgICApO1xuXG4gICAgdGhpcy5wYXNzd29yZFZhbGlkYXRvciA9IG5ldyBQYXNzd29yZFZhbGlkYXRvcihcbiAgICAgIHRoaXMubmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yLFxuICAgICAgdGhpcy5jb25maXJtTmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yXG4gICAgKTtcblxuICAgIHRoaXMuX2hpZGVJbnB1dHNCbG9jaygpO1xuICAgIHRoaXMuX2luaXRFdmVudHMoKTtcblxuICAgIHJldHVybiB7fTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0aWFsaXplIGV2ZW50cy5cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0RXZlbnRzKCkge1xuICAgIC8vIFNob3cgdGhlIGlucHV0cyBibG9jayB3aGVuIHNob3cgYnV0dG9uIGlzIGNsaWNrZWRcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLnNob3dCdXR0b25TZWxlY3RvciwgKGUpID0+IHtcbiAgICAgIHRoaXMuX2hpZGUoJChlLmN1cnJlbnRUYXJnZXQpKTtcbiAgICAgIHRoaXMuX3Nob3dJbnB1dHNCbG9jaygpO1xuICAgIH0pO1xuXG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5oaWRlQnV0dG9uU2VsZWN0b3IsICgpID0+IHtcbiAgICAgIHRoaXMuX2hpZGVJbnB1dHNCbG9jaygpO1xuICAgICAgdGhpcy5fc2hvdygkKHRoaXMuc2hvd0J1dHRvblNlbGVjdG9yKSk7XG4gICAgfSk7XG5cbiAgICAvLyBXYXRjaCBhbmQgZGlzcGxheSBmZWVkYmFjayBhYm91dCBwYXNzd29yZCdzIHN0cmVuZ3RoXG4gICAgdGhpcy5wYXNzd29yZEhhbmRsZXIud2F0Y2hQYXNzd29yZFN0cmVuZ3RoKHRoaXMuJG5ld1Bhc3N3b3JkSW5wdXRzKTtcblxuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMuZ2VuZXJhdGVQYXNzd29yZEJ1dHRvblNlbGVjdG9yLCAoKSA9PiB7XG4gICAgICAvLyBHZW5lcmF0ZSB0aGUgcGFzc3dvcmQgaW50byBtYWluIGlucHV0LlxuICAgICAgdGhpcy5wYXNzd29yZEhhbmRsZXIuZ2VuZXJhdGVQYXNzd29yZCh0aGlzLiRuZXdQYXNzd29yZElucHV0cyk7XG5cbiAgICAgIC8vIENvcHkgdGhlIGdlbmVyYXRlZCBwYXNzd29yZCBmcm9tIG1haW4gaW5wdXQgdG8gYWRkaXRpb25hbCBpbnB1dHNcbiAgICAgIHRoaXMuJGNvcHlQYXNzd29yZElucHV0cy52YWwodGhpcy4kbmV3UGFzc3dvcmRJbnB1dHMudmFsKCkpO1xuICAgICAgdGhpcy5fY2hlY2tQYXNzd29yZFZhbGlkaXR5KCk7XG4gICAgfSk7XG5cbiAgICAvLyBWYWxpZGF0ZSBuZXcgcGFzc3dvcmQgYW5kIGl0J3MgY29uZmlybWF0aW9uIHdoZW4gYW55IG9mIHRoZSBpbnB1dHMgaXMgY2hhbmdlZFxuICAgICQoZG9jdW1lbnQpLm9uKCdrZXl1cCcsIGAke3RoaXMubmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yfSwke3RoaXMuY29uZmlybU5ld1Bhc3N3b3JkSW5wdXRTZWxlY3Rvcn1gLCAoKSA9PiB7XG4gICAgICB0aGlzLl9jaGVja1Bhc3N3b3JkVmFsaWRpdHkoKTtcbiAgICB9KTtcblxuICAgIC8vIFByZXZlbnQgc3VibWl0dGluZyB0aGUgZm9ybSBpZiBuZXcgcGFzc3dvcmQgaXMgbm90IHZhbGlkXG4gICAgJChkb2N1bWVudCkub24oJ3N1Ym1pdCcsICQodGhpcy5vbGRQYXNzd29yZElucHV0U2VsZWN0b3IpLmNsb3Nlc3QoJ2Zvcm0nKSwgKGV2ZW50KSA9PiB7XG4gICAgICAvLyBJZiBwYXNzd29yZCBpbnB1dCBpcyBkaXNhYmxlZCAtIHdlIGRvbid0IG5lZWQgdG8gdmFsaWRhdGUgaXQuXG4gICAgICBpZiAoJCh0aGlzLm9sZFBhc3N3b3JkSW5wdXRTZWxlY3RvcikuaXMoJzpkaXNhYmxlZCcpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgaWYgKCF0aGlzLnBhc3N3b3JkVmFsaWRhdG9yLmlzUGFzc3dvcmRWYWxpZCgpKSB7XG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2sgaWYgcGFzc3dvcmQgaXMgdmFsaWQsIHNob3cgZXJyb3IgbWVzc2FnZXMgaWYgaXQncyBub3QuXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfY2hlY2tQYXNzd29yZFZhbGlkaXR5KCkge1xuICAgIGNvbnN0ICRmaXJzdFBhc3N3b3JkRXJyb3JDb250YWluZXIgPSAkKHRoaXMubmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yKS5wYXJlbnQoKS5maW5kKCcuZm9ybS10ZXh0Jyk7XG4gICAgY29uc3QgJHNlY29uZFBhc3N3b3JkRXJyb3JDb250YWluZXIgPSAkKHRoaXMuY29uZmlybU5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvcikucGFyZW50KCkuZmluZCgnLmZvcm0tdGV4dCcpO1xuXG4gICAgJGZpcnN0UGFzc3dvcmRFcnJvckNvbnRhaW5lclxuICAgICAgLnRleHQodGhpcy5fZ2V0UGFzc3dvcmRMZW5ndGhWYWxpZGF0aW9uTWVzc2FnZSgpKVxuICAgICAgLnRvZ2dsZUNsYXNzKCd0ZXh0LWRhbmdlcicsICF0aGlzLnBhc3N3b3JkVmFsaWRhdG9yLmlzUGFzc3dvcmRMZW5ndGhWYWxpZCgpKVxuICAgIDtcblxuICAgICRzZWNvbmRQYXNzd29yZEVycm9yQ29udGFpbmVyXG4gICAgICAudGV4dCh0aGlzLl9nZXRQYXNzd29yZENvbmZpcm1hdGlvblZhbGlkYXRpb25NZXNzYWdlKCkpXG4gICAgICAudG9nZ2xlQ2xhc3MoJ3RleHQtZGFuZ2VyJywgIXRoaXMucGFzc3dvcmRWYWxpZGF0b3IuaXNQYXNzd29yZE1hdGNoaW5nQ29uZmlybWF0aW9uKCkpXG4gICAgO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBwYXNzd29yZCBjb25maXJtYXRpb24gdmFsaWRhdGlvbiBtZXNzYWdlLlxuICAgKlxuICAgKiBAcmV0dXJucyB7U3RyaW5nfVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldFBhc3N3b3JkQ29uZmlybWF0aW9uVmFsaWRhdGlvbk1lc3NhZ2UoKSB7XG4gICAgaWYgKCF0aGlzLnBhc3N3b3JkVmFsaWRhdG9yLmlzUGFzc3dvcmRNYXRjaGluZ0NvbmZpcm1hdGlvbigpKSB7XG4gICAgICByZXR1cm4gJCh0aGlzLmNvbmZpcm1OZXdQYXNzd29yZElucHV0U2VsZWN0b3IpLmRhdGEoJ2ludmFsaWQtcGFzc3dvcmQnKTtcbiAgICB9XG5cbiAgICByZXR1cm4gJyc7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHBhc3N3b3JkIGxlbmd0aCB2YWxpZGF0aW9uIG1lc3NhZ2UuXG4gICAqXG4gICAqIEByZXR1cm5zIHtTdHJpbmd9XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZ2V0UGFzc3dvcmRMZW5ndGhWYWxpZGF0aW9uTWVzc2FnZSgpIHtcbiAgICBpZiAodGhpcy5wYXNzd29yZFZhbGlkYXRvci5pc1Bhc3N3b3JkVG9vU2hvcnQoKSkge1xuICAgICAgcmV0dXJuICQodGhpcy5uZXdQYXNzd29yZElucHV0U2VsZWN0b3IpLmRhdGEoJ3Bhc3N3b3JkLXRvby1zaG9ydCcpXG4gICAgfVxuXG4gICAgaWYgKHRoaXMucGFzc3dvcmRWYWxpZGF0b3IuaXNQYXNzd29yZFRvb0xvbmcoKSkge1xuICAgICAgcmV0dXJuICQodGhpcy5uZXdQYXNzd29yZElucHV0U2VsZWN0b3IpLmRhdGEoJ3Bhc3N3b3JkLXRvby1sb25nJyk7XG4gICAgfVxuXG4gICAgcmV0dXJuICcnO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3cgdGhlIHBhc3N3b3JkIGlucHV0cyBibG9jay5cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93SW5wdXRzQmxvY2soKSB7XG4gICAgdGhpcy5fc2hvdyh0aGlzLiRpbnB1dHNCbG9jayk7XG4gICAgdGhpcy4kc3VibWl0dGFibGVJbnB1dHMucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcbiAgICB0aGlzLiRzdWJtaXR0YWJsZUlucHV0cy5hdHRyKCdyZXF1aXJlZCcsICdyZXF1aXJlZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGUgdGhlIHBhc3N3b3JkIGlucHV0cyBibG9jay5cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlSW5wdXRzQmxvY2soKSB7XG4gICAgdGhpcy5faGlkZSh0aGlzLiRpbnB1dHNCbG9jayk7XG4gICAgdGhpcy4kc3VibWl0dGFibGVJbnB1dHMuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcbiAgICB0aGlzLiRzdWJtaXR0YWJsZUlucHV0cy5yZW1vdmVBdHRyKCdyZXF1aXJlZCcpO1xuICAgIHRoaXMuJGlucHV0c0Jsb2NrLmZpbmQoJ2lucHV0JykudmFsKCcnKTtcbiAgICB0aGlzLiRpbnB1dHNCbG9jay5maW5kKCcuZm9ybS10ZXh0JykudGV4dCgnJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZSBhbiBlbGVtZW50LlxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGVsXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZSgkZWwpIHtcbiAgICAkZWwuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3cgaGlkZGVuIGVsZW1lbnQuXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkZWxcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93KCRlbCkge1xuICAgICRlbC5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZm9ybS9jaGFuZ2UtcGFzc3dvcmQtY29udHJvbC5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuLyoqXG4gKiBDbGFzcyByZXNwb25zaWJsZSBmb3IgY2hlY2tpbmcgcGFzc3dvcmQncyB2YWxpZGl0eS5cbiAqIENhbiB2YWxpZGF0ZSBlbnRlcmVkIHBhc3N3b3JkJ3MgbGVuZ3RoIGFnYWluc3QgbWluL21heCB2YWx1ZXMuXG4gKiBJZiBwYXNzd29yZCBjb25maXJtYXRpb24gaW5wdXQgaXMgcHJvdmlkZWQsIGNhbiB2YWxpZGF0ZSBpZiBlbnRlcmVkIHBhc3N3b3JkIGlzIG1hdGNoaW5nIGNvbmZpcm1hdGlvbi5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUGFzc3dvcmRWYWxpZGF0b3Ige1xuXG4gIC8qKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gcGFzc3dvcmRJbnB1dFNlbGVjdG9yIHNlbGVjdG9yIG9mIHRoZSBwYXNzd29yZCBpbnB1dC5cbiAgICogQHBhcmFtIHtTdHJpbmd8bnVsbH0gY29uZmlybVBhc3N3b3JkSW5wdXRTZWxlY3RvciAob3B0aW9uYWwpIHNlbGVjdG9yIGZvciB0aGUgcGFzc3dvcmQgY29uZmlybWF0aW9uIGlucHV0LlxuICAgKiBAcGFyYW0ge09iamVjdH0gb3B0aW9ucyBhbGxvd3Mgb3ZlcnJpZGluZyBkZWZhdWx0IG9wdGlvbnMuXG4gICAqL1xuICBjb25zdHJ1Y3RvcihwYXNzd29yZElucHV0U2VsZWN0b3IsIGNvbmZpcm1QYXNzd29yZElucHV0U2VsZWN0b3IgPSBudWxsLCBvcHRpb25zID0ge30pIHtcbiAgICB0aGlzLm5ld1Bhc3N3b3JkSW5wdXQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKHBhc3N3b3JkSW5wdXRTZWxlY3Rvcik7XG4gICAgdGhpcy5jb25maXJtUGFzc3dvcmRJbnB1dCA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoY29uZmlybVBhc3N3b3JkSW5wdXRTZWxlY3Rvcik7XG5cbiAgICAvLyBNaW5pbXVtIGFsbG93ZWQgbGVuZ3RoIGZvciBlbnRlcmVkIHBhc3N3b3JkXG4gICAgdGhpcy5taW5QYXNzd29yZExlbmd0aCA9IG9wdGlvbnMubWluUGFzc3dvcmRMZW5ndGggfHwgODtcblxuICAgIC8vIE1heGltdW0gYWxsb3dlZCBsZW5ndGggZm9yIGVudGVyZWQgcGFzc3dvcmRcbiAgICB0aGlzLm1heFBhc3N3b3JkTGVuZ3RoID0gb3B0aW9ucy5tYXhQYXNzd29yZExlbmd0aCB8fCAyNTU7XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2sgaWYgdGhlIHBhc3N3b3JkIGlzIHZhbGlkLlxuICAgKlxuICAgKiBAcmV0dXJucyB7Ym9vbGVhbn1cbiAgICovXG4gIGlzUGFzc3dvcmRWYWxpZCgpIHtcbiAgICBpZiAodGhpcy5jb25maXJtUGFzc3dvcmRJbnB1dCAmJiAhdGhpcy5pc1Bhc3N3b3JkTWF0Y2hpbmdDb25maXJtYXRpb24oKSkge1xuICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cblxuICAgIHJldHVybiB0aGlzLmlzUGFzc3dvcmRMZW5ndGhWYWxpZCgpO1xuICB9XG5cbiAgLyoqXG4gICAqIENoZWNrIGlmIHBhc3N3b3JkJ3MgbGVuZ3RoIGlzIHZhbGlkLlxuICAgKlxuICAgKiBAcmV0dXJucyB7Ym9vbGVhbn1cbiAgICovXG4gIGlzUGFzc3dvcmRMZW5ndGhWYWxpZCgpIHtcbiAgICByZXR1cm4gIXRoaXMuaXNQYXNzd29yZFRvb1Nob3J0KCkgJiYgIXRoaXMuaXNQYXNzd29yZFRvb0xvbmcoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDaGVjayBpZiBwYXNzd29yZCBpcyBtYXRjaGluZyBpdCdzIGNvbmZpcm1hdGlvbi5cbiAgICpcbiAgICogQHJldHVybnMge2Jvb2xlYW59XG4gICAqL1xuICBpc1Bhc3N3b3JkTWF0Y2hpbmdDb25maXJtYXRpb24oKSB7XG4gICAgaWYgKCF0aGlzLmNvbmZpcm1QYXNzd29yZElucHV0KSB7XG4gICAgICB0aHJvdyAnQ29uZmlybSBwYXNzd29yZCBpbnB1dCBpcyBub3QgcHJvdmlkZWQgZm9yIHRoZSBwYXNzd29yZCB2YWxpZGF0b3IuJztcbiAgICB9XG5cbiAgICBpZiAodGhpcy5jb25maXJtUGFzc3dvcmRJbnB1dC52YWx1ZSA9PT0gJycpIHtcbiAgICAgIHJldHVybiB0cnVlO1xuICAgIH1cblxuICAgIHJldHVybiB0aGlzLm5ld1Bhc3N3b3JkSW5wdXQudmFsdWUgPT09IHRoaXMuY29uZmlybVBhc3N3b3JkSW5wdXQudmFsdWU7XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2sgaWYgcGFzc3dvcmQgaXMgdG9vIHNob3J0LlxuICAgKlxuICAgKiBAcmV0dXJucyB7Ym9vbGVhbn1cbiAgICovXG4gIGlzUGFzc3dvcmRUb29TaG9ydCgpIHtcbiAgICByZXR1cm4gdGhpcy5uZXdQYXNzd29yZElucHV0LnZhbHVlLmxlbmd0aCA8IHRoaXMubWluUGFzc3dvcmRMZW5ndGg7XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2sgaWYgcGFzc3dvcmQgaXMgdG9vIGxvbmcuXG4gICAqXG4gICAqIEByZXR1cm5zIHtib29sZWFufVxuICAgKi9cbiAgaXNQYXNzd29yZFRvb0xvbmcoKSB7XG4gICAgcmV0dXJuIHRoaXMubmV3UGFzc3dvcmRJbnB1dC52YWx1ZS5sZW5ndGggPiB0aGlzLm1heFBhc3N3b3JkTGVuZ3RoO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL3Bhc3N3b3JkLXZhbGlkYXRvci5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuLyoqXG4gKiBEZWZpbmVzIGFsbCBzZWxlY3RvcnMgdGhhdCBhcmUgdXNlZCBpbiBlbXBsb3llZSBhZGQvZWRpdCBmb3JtLlxuICovXG5leHBvcnQgZGVmYXVsdCB7XG4gIHNob3BDaG9pY2VUcmVlOiAnI2VtcGxveWVlX3Nob3BfYXNzb2NpYXRpb24nLFxuICBwcm9maWxlU2VsZWN0OiAnI2VtcGxveWVlX3Byb2ZpbGUnLFxuICBkZWZhdWx0UGFnZVNlbGVjdDogJyNlbXBsb3llZV9kZWZhdWx0X3BhZ2UnLFxuICBhZGRvbnNDb25uZWN0Rm9ybTogJyNhZGRvbnMtY29ubmVjdC1mb3JtJyxcbiAgYWRkb25zTG9naW5CdXR0b246ICcjYWRkb25zX2xvZ2luX2J0bicsXG5cbiAgLy8gc2VsZWN0b3JzIHJlbGF0ZWQgdG8gXCJjaGFuZ2UgcGFzc3dvcmRcIiBmb3JtIGNvbnRyb2xcbiAgY2hhbmdlUGFzc3dvcmRJbnB1dHNCbG9jazogJy5qcy1jaGFuZ2UtcGFzc3dvcmQtYmxvY2snLFxuICBzaG93Q2hhbmdlUGFzc3dvcmRCbG9ja0J1dHRvbjogJy5qcy1jaGFuZ2UtcGFzc3dvcmQnLFxuICBoaWRlQ2hhbmdlUGFzc3dvcmRCbG9ja0J1dHRvbjogJy5qcy1jaGFuZ2UtcGFzc3dvcmQtY2FuY2VsJyxcbiAgZ2VuZXJhdGVQYXNzd29yZEJ1dHRvbjogJyNlbXBsb3llZV9jaGFuZ2VfcGFzc3dvcmRfZ2VuZXJhdGVfcGFzc3dvcmRfYnV0dG9uJyxcbiAgb2xkUGFzc3dvcmRJbnB1dDogJyNlbXBsb3llZV9jaGFuZ2VfcGFzc3dvcmRfb2xkX3Bhc3N3b3JkJyxcbiAgbmV3UGFzc3dvcmRJbnB1dDogJyNlbXBsb3llZV9jaGFuZ2VfcGFzc3dvcmRfbmV3X3Bhc3N3b3JkX2ZpcnN0JyxcbiAgY29uZmlybU5ld1Bhc3N3b3JkSW5wdXQ6ICcjZW1wbG95ZWVfY2hhbmdlX3Bhc3N3b3JkX25ld19wYXNzd29yZF9zZWNvbmQnLFxuICBnZW5lcmF0ZWRQYXNzd29yZERpc3BsYXlJbnB1dDogJyNlbXBsb3llZV9jaGFuZ2VfcGFzc3dvcmRfZ2VuZXJhdGVkX3Bhc3N3b3JkJyxcbiAgcGFzc3dvcmRTdHJlbmd0aEZlZWRiYWNrQ29udGFpbmVyOiAnLmpzLXBhc3N3b3JkLXN0cmVuZ3RoLWZlZWRiYWNrJyxcbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2VtcGxveWVlL2VtcGxveWVlLWZvcm0tbWFwLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgRW1wbG95ZWVGb3JtIGZyb20gXCIuL0VtcGxveWVlRm9ybVwiO1xuXG4kKCgpID0+IHtcbiAgbmV3IEVtcGxveWVlRm9ybSgpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9lbXBsb3llZS9mb3JtLmpzIiwiLy8gaHR0cHM6Ly9naXRodWIuY29tL3psb2lyb2NrL2NvcmUtanMvaXNzdWVzLzg2I2lzc3VlY29tbWVudC0xMTU3NTkwMjhcbnZhciBnbG9iYWwgPSBtb2R1bGUuZXhwb3J0cyA9IHR5cGVvZiB3aW5kb3cgIT0gJ3VuZGVmaW5lZCcgJiYgd2luZG93Lk1hdGggPT0gTWF0aFxuICA/IHdpbmRvdyA6IHR5cGVvZiBzZWxmICE9ICd1bmRlZmluZWQnICYmIHNlbGYuTWF0aCA9PSBNYXRoID8gc2VsZiA6IEZ1bmN0aW9uKCdyZXR1cm4gdGhpcycpKCk7XG5pZih0eXBlb2YgX19nID09ICdudW1iZXInKV9fZyA9IGdsb2JhbDsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBuby11bmRlZlxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZ2xvYmFsLmpzXG4vLyBtb2R1bGUgaWQgPSA1XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBhbk9iamVjdCAgICAgICA9IHJlcXVpcmUoJy4vX2FuLW9iamVjdCcpXG4gICwgSUU4X0RPTV9ERUZJTkUgPSByZXF1aXJlKCcuL19pZTgtZG9tLWRlZmluZScpXG4gICwgdG9QcmltaXRpdmUgICAgPSByZXF1aXJlKCcuL190by1wcmltaXRpdmUnKVxuICAsIGRQICAgICAgICAgICAgID0gT2JqZWN0LmRlZmluZVByb3BlcnR5O1xuXG5leHBvcnRzLmYgPSByZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpID8gT2JqZWN0LmRlZmluZVByb3BlcnR5IDogZnVuY3Rpb24gZGVmaW5lUHJvcGVydHkoTywgUCwgQXR0cmlidXRlcyl7XG4gIGFuT2JqZWN0KE8pO1xuICBQID0gdG9QcmltaXRpdmUoUCwgdHJ1ZSk7XG4gIGFuT2JqZWN0KEF0dHJpYnV0ZXMpO1xuICBpZihJRThfRE9NX0RFRklORSl0cnkge1xuICAgIHJldHVybiBkUChPLCBQLCBBdHRyaWJ1dGVzKTtcbiAgfSBjYXRjaChlKXsgLyogZW1wdHkgKi8gfVxuICBpZignZ2V0JyBpbiBBdHRyaWJ1dGVzIHx8ICdzZXQnIGluIEF0dHJpYnV0ZXMpdGhyb3cgVHlwZUVycm9yKCdBY2Nlc3NvcnMgbm90IHN1cHBvcnRlZCEnKTtcbiAgaWYoJ3ZhbHVlJyBpbiBBdHRyaWJ1dGVzKU9bUF0gPSBBdHRyaWJ1dGVzLnZhbHVlO1xuICByZXR1cm4gTztcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZHAuanNcbi8vIG1vZHVsZSBpZCA9IDZcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogSGFuZGxlcyBVSSBpbnRlcmFjdGlvbnMgb2YgY2hvaWNlIHRyZWVcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ2hvaWNlVHJlZSB7XG4gIC8qKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gdHJlZVNlbGVjdG9yXG4gICAqL1xuICBjb25zdHJ1Y3Rvcih0cmVlU2VsZWN0b3IpIHtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKHRyZWVTZWxlY3Rvcik7XG5cbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgJy5qcy1pbnB1dC13cmFwcGVyJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkaW5wdXRXcmFwcGVyID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcblxuICAgICAgdGhpcy5fdG9nZ2xlQ2hpbGRUcmVlKCRpbnB1dFdyYXBwZXIpO1xuICAgIH0pO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsICcuanMtdG9nZ2xlLWNob2ljZS10cmVlLWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGFjdGlvbiA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG5cbiAgICAgIHRoaXMuX3RvZ2dsZVRyZWUoJGFjdGlvbik7XG4gICAgfSk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW46ICgpID0+IHRoaXMuZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4oKSxcbiAgICAgIGVuYWJsZUFsbElucHV0czogKCkgPT4gdGhpcy5lbmFibGVBbGxJbnB1dHMoKSxcbiAgICAgIGRpc2FibGVBbGxJbnB1dHM6ICgpID0+IHRoaXMuZGlzYWJsZUFsbElucHV0cygpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogRW5hYmxlIGF1dG9tYXRpYyBjaGVjay91bmNoZWNrIG9mIGNsaWNrZWQgaXRlbSdzIGNoaWxkcmVuLlxuICAgKi9cbiAgZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4oKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjaGFuZ2UnLCAnaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkY2xpY2tlZENoZWNrYm94ID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0ICRpdGVtV2l0aENoaWxkcmVuID0gJGNsaWNrZWRDaGVja2JveC5jbG9zZXN0KCdsaScpO1xuXG4gICAgICAkaXRlbVdpdGhDaGlsZHJlblxuICAgICAgICAuZmluZCgndWwgaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJylcbiAgICAgICAgLnByb3AoJ2NoZWNrZWQnLCAkY2xpY2tlZENoZWNrYm94LmlzKCc6Y2hlY2tlZCcpKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbmFibGUgYWxsIGlucHV0cyBpbiB0aGUgY2hvaWNlIHRyZWUuXG4gICAqL1xuICBlbmFibGVBbGxJbnB1dHMoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLmZpbmQoJ2lucHV0JykucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNhYmxlIGFsbCBpbnB1dHMgaW4gdGhlIGNob2ljZSB0cmVlLlxuICAgKi9cbiAgZGlzYWJsZUFsbElucHV0cygpIHtcbiAgICB0aGlzLiRjb250YWluZXIuZmluZCgnaW5wdXQnKS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIENvbGxhcHNlIG9yIGV4cGFuZCBzdWItdHJlZSBmb3Igc2luZ2xlIHBhcmVudFxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGlucHV0V3JhcHBlclxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3RvZ2dsZUNoaWxkVHJlZSgkaW5wdXRXcmFwcGVyKSB7XG4gICAgY29uc3QgJHBhcmVudFdyYXBwZXIgPSAkaW5wdXRXcmFwcGVyLmNsb3Nlc3QoJ2xpJyk7XG5cbiAgICBpZiAoJHBhcmVudFdyYXBwZXIuaGFzQ2xhc3MoJ2V4cGFuZGVkJykpIHtcbiAgICAgICRwYXJlbnRXcmFwcGVyXG4gICAgICAgIC5yZW1vdmVDbGFzcygnZXhwYW5kZWQnKVxuICAgICAgICAuYWRkQ2xhc3MoJ2NvbGxhcHNlZCcpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgaWYgKCRwYXJlbnRXcmFwcGVyLmhhc0NsYXNzKCdjb2xsYXBzZWQnKSkge1xuICAgICAgJHBhcmVudFdyYXBwZXJcbiAgICAgICAgLnJlbW92ZUNsYXNzKCdjb2xsYXBzZWQnKVxuICAgICAgICAuYWRkQ2xhc3MoJ2V4cGFuZGVkJyk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIENvbGxhcHNlIG9yIGV4cGFuZCB3aG9sZSB0cmVlXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkYWN0aW9uXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdG9nZ2xlVHJlZSgkYWN0aW9uKSB7XG4gICAgY29uc3QgJHBhcmVudENvbnRhaW5lciA9ICRhY3Rpb24uY2xvc2VzdCgnLmpzLWNob2ljZS10cmVlLWNvbnRhaW5lcicpO1xuICAgIGNvbnN0IGFjdGlvbiA9ICRhY3Rpb24uZGF0YSgnYWN0aW9uJyk7XG5cbiAgICAvLyB0b2dnbGUgYWN0aW9uIGNvbmZpZ3VyYXRpb25cbiAgICBjb25zdCBjb25maWcgPSB7XG4gICAgICBhZGRDbGFzczoge1xuICAgICAgICBleHBhbmQ6ICdleHBhbmRlZCcsXG4gICAgICAgIGNvbGxhcHNlOiAnY29sbGFwc2VkJyxcbiAgICAgIH0sXG4gICAgICByZW1vdmVDbGFzczoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZWQnLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZGVkJyxcbiAgICAgIH0sXG4gICAgICBuZXh0QWN0aW9uOiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlJyxcbiAgICAgICAgY29sbGFwc2U6ICdleHBhbmQnLFxuICAgICAgfSxcbiAgICAgIHRleHQ6IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2VkLXRleHQnLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZGVkLXRleHQnLFxuICAgICAgfSxcbiAgICAgIGljb246IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2VkLWljb24nLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZGVkLWljb24nLFxuICAgICAgfVxuICAgIH07XG5cbiAgICAkcGFyZW50Q29udGFpbmVyLmZpbmQoJ2xpJykuZWFjaCgoaW5kZXgsIGl0ZW0pID0+IHtcbiAgICAgIGNvbnN0ICRpdGVtID0gJChpdGVtKTtcblxuICAgICAgaWYgKCRpdGVtLmhhc0NsYXNzKGNvbmZpZy5yZW1vdmVDbGFzc1thY3Rpb25dKSkge1xuICAgICAgICAgICRpdGVtLnJlbW92ZUNsYXNzKGNvbmZpZy5yZW1vdmVDbGFzc1thY3Rpb25dKVxuICAgICAgICAgICAgLmFkZENsYXNzKGNvbmZpZy5hZGRDbGFzc1thY3Rpb25dKTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgICRhY3Rpb24uZGF0YSgnYWN0aW9uJywgY29uZmlnLm5leHRBY3Rpb25bYWN0aW9uXSk7XG4gICAgJGFjdGlvbi5maW5kKCcubWF0ZXJpYWwtaWNvbnMnKS50ZXh0KCRhY3Rpb24uZGF0YShjb25maWcuaWNvblthY3Rpb25dKSk7XG4gICAgJGFjdGlvbi5maW5kKCcuanMtdG9nZ2xlLXRleHQnKS50ZXh0KCRhY3Rpb24uZGF0YShjb25maWcudGV4dFthY3Rpb25dKSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZm9ybS9jaG9pY2UtdHJlZS5qcyIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oZXhlYyl7XG4gIHRyeSB7XG4gICAgcmV0dXJuICEhZXhlYygpO1xuICB9IGNhdGNoKGUpe1xuICAgIHJldHVybiB0cnVlO1xuICB9XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZmFpbHMuanNcbi8vIG1vZHVsZSBpZCA9IDdcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGdsb2JhbCAgICA9IHJlcXVpcmUoJy4vX2dsb2JhbCcpXG4gICwgY29yZSAgICAgID0gcmVxdWlyZSgnLi9fY29yZScpXG4gICwgY3R4ICAgICAgID0gcmVxdWlyZSgnLi9fY3R4JylcbiAgLCBoaWRlICAgICAgPSByZXF1aXJlKCcuL19oaWRlJylcbiAgLCBQUk9UT1RZUEUgPSAncHJvdG90eXBlJztcblxudmFyICRleHBvcnQgPSBmdW5jdGlvbih0eXBlLCBuYW1lLCBzb3VyY2Upe1xuICB2YXIgSVNfRk9SQ0VEID0gdHlwZSAmICRleHBvcnQuRlxuICAgICwgSVNfR0xPQkFMID0gdHlwZSAmICRleHBvcnQuR1xuICAgICwgSVNfU1RBVElDID0gdHlwZSAmICRleHBvcnQuU1xuICAgICwgSVNfUFJPVE8gID0gdHlwZSAmICRleHBvcnQuUFxuICAgICwgSVNfQklORCAgID0gdHlwZSAmICRleHBvcnQuQlxuICAgICwgSVNfV1JBUCAgID0gdHlwZSAmICRleHBvcnQuV1xuICAgICwgZXhwb3J0cyAgID0gSVNfR0xPQkFMID8gY29yZSA6IGNvcmVbbmFtZV0gfHwgKGNvcmVbbmFtZV0gPSB7fSlcbiAgICAsIGV4cFByb3RvICA9IGV4cG9ydHNbUFJPVE9UWVBFXVxuICAgICwgdGFyZ2V0ICAgID0gSVNfR0xPQkFMID8gZ2xvYmFsIDogSVNfU1RBVElDID8gZ2xvYmFsW25hbWVdIDogKGdsb2JhbFtuYW1lXSB8fCB7fSlbUFJPVE9UWVBFXVxuICAgICwga2V5LCBvd24sIG91dDtcbiAgaWYoSVNfR0xPQkFMKXNvdXJjZSA9IG5hbWU7XG4gIGZvcihrZXkgaW4gc291cmNlKXtcbiAgICAvLyBjb250YWlucyBpbiBuYXRpdmVcbiAgICBvd24gPSAhSVNfRk9SQ0VEICYmIHRhcmdldCAmJiB0YXJnZXRba2V5XSAhPT0gdW5kZWZpbmVkO1xuICAgIGlmKG93biAmJiBrZXkgaW4gZXhwb3J0cyljb250aW51ZTtcbiAgICAvLyBleHBvcnQgbmF0aXZlIG9yIHBhc3NlZFxuICAgIG91dCA9IG93biA/IHRhcmdldFtrZXldIDogc291cmNlW2tleV07XG4gICAgLy8gcHJldmVudCBnbG9iYWwgcG9sbHV0aW9uIGZvciBuYW1lc3BhY2VzXG4gICAgZXhwb3J0c1trZXldID0gSVNfR0xPQkFMICYmIHR5cGVvZiB0YXJnZXRba2V5XSAhPSAnZnVuY3Rpb24nID8gc291cmNlW2tleV1cbiAgICAvLyBiaW5kIHRpbWVycyB0byBnbG9iYWwgZm9yIGNhbGwgZnJvbSBleHBvcnQgY29udGV4dFxuICAgIDogSVNfQklORCAmJiBvd24gPyBjdHgob3V0LCBnbG9iYWwpXG4gICAgLy8gd3JhcCBnbG9iYWwgY29uc3RydWN0b3JzIGZvciBwcmV2ZW50IGNoYW5nZSB0aGVtIGluIGxpYnJhcnlcbiAgICA6IElTX1dSQVAgJiYgdGFyZ2V0W2tleV0gPT0gb3V0ID8gKGZ1bmN0aW9uKEMpe1xuICAgICAgdmFyIEYgPSBmdW5jdGlvbihhLCBiLCBjKXtcbiAgICAgICAgaWYodGhpcyBpbnN0YW5jZW9mIEMpe1xuICAgICAgICAgIHN3aXRjaChhcmd1bWVudHMubGVuZ3RoKXtcbiAgICAgICAgICAgIGNhc2UgMDogcmV0dXJuIG5ldyBDO1xuICAgICAgICAgICAgY2FzZSAxOiByZXR1cm4gbmV3IEMoYSk7XG4gICAgICAgICAgICBjYXNlIDI6IHJldHVybiBuZXcgQyhhLCBiKTtcbiAgICAgICAgICB9IHJldHVybiBuZXcgQyhhLCBiLCBjKTtcbiAgICAgICAgfSByZXR1cm4gQy5hcHBseSh0aGlzLCBhcmd1bWVudHMpO1xuICAgICAgfTtcbiAgICAgIEZbUFJPVE9UWVBFXSA9IENbUFJPVE9UWVBFXTtcbiAgICAgIHJldHVybiBGO1xuICAgIC8vIG1ha2Ugc3RhdGljIHZlcnNpb25zIGZvciBwcm90b3R5cGUgbWV0aG9kc1xuICAgIH0pKG91dCkgOiBJU19QUk9UTyAmJiB0eXBlb2Ygb3V0ID09ICdmdW5jdGlvbicgPyBjdHgoRnVuY3Rpb24uY2FsbCwgb3V0KSA6IG91dDtcbiAgICAvLyBleHBvcnQgcHJvdG8gbWV0aG9kcyB0byBjb3JlLiVDT05TVFJVQ1RPUiUubWV0aG9kcy4lTkFNRSVcbiAgICBpZihJU19QUk9UTyl7XG4gICAgICAoZXhwb3J0cy52aXJ0dWFsIHx8IChleHBvcnRzLnZpcnR1YWwgPSB7fSkpW2tleV0gPSBvdXQ7XG4gICAgICAvLyBleHBvcnQgcHJvdG8gbWV0aG9kcyB0byBjb3JlLiVDT05TVFJVQ1RPUiUucHJvdG90eXBlLiVOQU1FJVxuICAgICAgaWYodHlwZSAmICRleHBvcnQuUiAmJiBleHBQcm90byAmJiAhZXhwUHJvdG9ba2V5XSloaWRlKGV4cFByb3RvLCBrZXksIG91dCk7XG4gICAgfVxuICB9XG59O1xuLy8gdHlwZSBiaXRtYXBcbiRleHBvcnQuRiA9IDE7ICAgLy8gZm9yY2VkXG4kZXhwb3J0LkcgPSAyOyAgIC8vIGdsb2JhbFxuJGV4cG9ydC5TID0gNDsgICAvLyBzdGF0aWNcbiRleHBvcnQuUCA9IDg7ICAgLy8gcHJvdG9cbiRleHBvcnQuQiA9IDE2OyAgLy8gYmluZFxuJGV4cG9ydC5XID0gMzI7ICAvLyB3cmFwXG4kZXhwb3J0LlUgPSA2NDsgIC8vIHNhZmVcbiRleHBvcnQuUiA9IDEyODsgLy8gcmVhbCBwcm90byBtZXRob2QgZm9yIGBsaWJyYXJ5YCBcbm1vZHVsZS5leHBvcnRzID0gJGV4cG9ydDtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2V4cG9ydC5qc1xuLy8gbW9kdWxlIGlkID0gOFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiXSwic291cmNlUm9vdCI6IiJ9