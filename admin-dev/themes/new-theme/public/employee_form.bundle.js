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
/******/ 	return __webpack_require__(__webpack_require__.s = 323);
/******/ })
/************************************************************************/
/******/ ({

/***/ 11:
/***/ (function(module, exports) {

(function() { module.exports = window["jQuery"]; }());

/***/ }),

/***/ 18:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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

/***/ 255:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

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

var _choiceTree = __webpack_require__(18);

var _choiceTree2 = _interopRequireDefault(_choiceTree);

var _addonsConnector = __webpack_require__(305);

var _addonsConnector2 = _interopRequireDefault(_addonsConnector);

var _changePasswordControl = __webpack_require__(307);

var _changePasswordControl2 = _interopRequireDefault(_changePasswordControl);

var _employeeFormMap = __webpack_require__(322);

var _employeeFormMap2 = _interopRequireDefault(_employeeFormMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Class responsible for javascript actions in employee add/edit page.
 */
var EmployeeForm = function () {
  function EmployeeForm() {
    _classCallCheck(this, EmployeeForm);

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


  _createClass(EmployeeForm, [{
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
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(11)))

/***/ }),

/***/ 305:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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

/**
 * Responsible for connecting to addons marketplace.
 * Makes an addons connect request to the server, displays error messages if it fails.
 */

var AddonsConnector = function () {
  function AddonsConnector(addonsConnectFormSelector, loadingSpinnerSelector) {
    _classCallCheck(this, AddonsConnector);

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


  _createClass(AddonsConnector, [{
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

/***/ 306:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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

/**
 * Generates a password and informs about it's strength.
 * You can pass a password input to watch the password strength and display feedback messages.
 * You can also generate a random password into an input.
 */

var ChangePasswordHandler = function () {
  function ChangePasswordHandler(passwordStrengthFeedbackContainerSelector) {
    var _this = this;

    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

    _classCallCheck(this, ChangePasswordHandler);

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


  _createClass(ChangePasswordHandler, [{
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

/***/ 307:
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

var _changePasswordHandler = __webpack_require__(306);

var _changePasswordHandler2 = _interopRequireDefault(_changePasswordHandler);

var _passwordValidator = __webpack_require__(310);

var _passwordValidator2 = _interopRequireDefault(_passwordValidator);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Class responsible for actions related to "change password" form type.
 * Generates random passwords, validates new password and it's confirmation,
 * displays error messages related to validation.
 */

var ChangePasswordControl = function () {
  function ChangePasswordControl(inputsBlockSelector, showButtonSelector, hideButtonSelector, generatePasswordButtonSelector, oldPasswordInputSelector, newPasswordInputSelector, confirmNewPasswordInputSelector, generatedPasswordDisplaySelector, passwordStrengthFeedbackContainerSelector) {
    _classCallCheck(this, ChangePasswordControl);

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


  _createClass(ChangePasswordControl, [{
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

/***/ 310:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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

    _classCallCheck(this, PasswordValidator);

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


  _createClass(PasswordValidator, [{
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

/***/ 322:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
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

/***/ 323:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

var _EmployeeForm = __webpack_require__(255);

var _EmployeeForm2 = _interopRequireDefault(_EmployeeForm);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

$(function () {
  new _EmployeeForm2.default();
}); /**
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
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(11)))

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQiLCJ3ZWJwYWNrOi8vL2V4dGVybmFsIFwialF1ZXJ5XCI/MGNiOCoqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZm9ybS9jaG9pY2UtdHJlZS5qcz81NDFhKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9lbXBsb3llZS9FbXBsb3llZUZvcm0uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9hZGRvbnMtY29ubmVjdG9yLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvY2hhbmdlLXBhc3N3b3JkLWhhbmRsZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9mb3JtL2NoYW5nZS1wYXNzd29yZC1jb250cm9sLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvcGFzc3dvcmQtdmFsaWRhdG9yLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2VtcGxveWVlL2VtcGxveWVlLWZvcm0tbWFwLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2VtcGxveWVlL2Zvcm0uanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIkNob2ljZVRyZWUiLCJ0cmVlU2VsZWN0b3IiLCIkY29udGFpbmVyIiwib24iLCJldmVudCIsIiRpbnB1dFdyYXBwZXIiLCJjdXJyZW50VGFyZ2V0IiwiX3RvZ2dsZUNoaWxkVHJlZSIsIiRhY3Rpb24iLCJfdG9nZ2xlVHJlZSIsImVuYWJsZUF1dG9DaGVja0NoaWxkcmVuIiwiZW5hYmxlQWxsSW5wdXRzIiwiZGlzYWJsZUFsbElucHV0cyIsIiRjbGlja2VkQ2hlY2tib3giLCIkaXRlbVdpdGhDaGlsZHJlbiIsImNsb3Nlc3QiLCJmaW5kIiwicHJvcCIsImlzIiwicmVtb3ZlQXR0ciIsImF0dHIiLCIkcGFyZW50V3JhcHBlciIsImhhc0NsYXNzIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsIiRwYXJlbnRDb250YWluZXIiLCJhY3Rpb24iLCJkYXRhIiwiY29uZmlnIiwiZXhwYW5kIiwiY29sbGFwc2UiLCJuZXh0QWN0aW9uIiwidGV4dCIsImljb24iLCJlYWNoIiwiaW5kZXgiLCJpdGVtIiwiJGl0ZW0iLCJFbXBsb3llZUZvcm0iLCJzaG9wQ2hvaWNlVHJlZVNlbGVjdG9yIiwiZW1wbG95ZWVGb3JtTWFwIiwic2hvcENob2ljZVRyZWUiLCJlbXBsb3llZVByb2ZpbGVTZWxlY3RvciIsInByb2ZpbGVTZWxlY3QiLCJ0YWJzRHJvcGRvd25TZWxlY3RvciIsImRlZmF1bHRQYWdlU2VsZWN0IiwiQWRkb25zQ29ubmVjdG9yIiwiYWRkb25zQ29ubmVjdEZvcm0iLCJhZGRvbnNMb2dpbkJ1dHRvbiIsIkNoYW5nZVBhc3N3b3JkQ29udHJvbCIsImNoYW5nZVBhc3N3b3JkSW5wdXRzQmxvY2siLCJzaG93Q2hhbmdlUGFzc3dvcmRCbG9ja0J1dHRvbiIsImhpZGVDaGFuZ2VQYXNzd29yZEJsb2NrQnV0dG9uIiwiZ2VuZXJhdGVQYXNzd29yZEJ1dHRvbiIsIm9sZFBhc3N3b3JkSW5wdXQiLCJuZXdQYXNzd29yZElucHV0IiwiY29uZmlybU5ld1Bhc3N3b3JkSW5wdXQiLCJnZW5lcmF0ZWRQYXNzd29yZERpc3BsYXlJbnB1dCIsInBhc3N3b3JkU3RyZW5ndGhGZWVkYmFja0NvbnRhaW5lciIsIl9pbml0RXZlbnRzIiwiX3RvZ2dsZVNob3BUcmVlIiwiJGVtcGxveWVlUHJvZmlsZXNEcm9wZG93biIsImdldFRhYnNVcmwiLCJkb2N1bWVudCIsImdldCIsInByb2ZpbGVJZCIsInZhbCIsInRhYnMiLCJfcmVsb2FkVGFic0Ryb3Bkb3duIiwiYWNjZXNzaWJsZVRhYnMiLCIkdGFic0Ryb3Bkb3duIiwiZW1wdHkiLCJrZXkiLCJsZW5ndGgiLCIkb3B0Z3JvdXAiLCJfY3JlYXRlT3B0aW9uR3JvdXAiLCJjaGlsZEtleSIsImFwcGVuZCIsIl9jcmVhdGVPcHRpb24iLCIkZW1wbG95ZWVQcm9maWxlRHJvcGRvd24iLCJzdXBlckFkbWluUHJvZmlsZUlkIiwidG9nZ2xlQ2xhc3MiLCJuYW1lIiwidmFsdWUiLCJhZGRvbnNDb25uZWN0Rm9ybVNlbGVjdG9yIiwibG9hZGluZ1NwaW5uZXJTZWxlY3RvciIsIiRsb2FkaW5nU3Bpbm5lciIsIiRmb3JtIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJfY29ubmVjdCIsInNlcmlhbGl6ZSIsImFkZG9uc0Nvbm5lY3RVcmwiLCJmb3JtRGF0YSIsImFqYXgiLCJtZXRob2QiLCJ1cmwiLCJkYXRhVHlwZSIsImJlZm9yZVNlbmQiLCJzaG93IiwiaGlkZSIsInRoZW4iLCJyZXNwb25zZSIsInN1Y2Nlc3MiLCJsb2NhdGlvbiIsInJlbG9hZCIsImdyb3dsIiwiZXJyb3IiLCJtZXNzYWdlIiwiZmFkZUluIiwiQ2hhbmdlUGFzc3dvcmRIYW5kbGVyIiwicGFzc3dvcmRTdHJlbmd0aEZlZWRiYWNrQ29udGFpbmVyU2VsZWN0b3IiLCJvcHRpb25zIiwibWluTGVuZ3RoIiwiJGZlZWRiYWNrQ29udGFpbmVyIiwid2F0Y2hQYXNzd29yZFN0cmVuZ3RoIiwiJGlucHV0IiwiZ2VuZXJhdGVQYXNzd29yZCIsInBhc3N5IiwicmVxdWlyZW1lbnRzIiwibWluIiwiY2hhcmFjdGVycyIsImVsZW1lbnQiLCIkb3V0cHV0Q29udGFpbmVyIiwiaW5zZXJ0QWZ0ZXIiLCJzdHJlbmd0aCIsInZhbGlkIiwiX2Rpc3BsYXlGZWVkYmFjayIsInBhc3N3b3JkU3RyZW5ndGgiLCJpc1Bhc3N3b3JkVmFsaWQiLCJmZWVkYmFjayIsIl9nZXRQYXNzd29yZFN0cmVuZ3RoRmVlZGJhY2siLCJlbGVtZW50Q2xhc3MiLCJMT1ciLCJNRURJVU0iLCJISUdIIiwiRVhUUkVNRSIsImlucHV0c0Jsb2NrU2VsZWN0b3IiLCJzaG93QnV0dG9uU2VsZWN0b3IiLCJoaWRlQnV0dG9uU2VsZWN0b3IiLCJnZW5lcmF0ZVBhc3N3b3JkQnV0dG9uU2VsZWN0b3IiLCJvbGRQYXNzd29yZElucHV0U2VsZWN0b3IiLCJuZXdQYXNzd29yZElucHV0U2VsZWN0b3IiLCJjb25maXJtTmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yIiwiZ2VuZXJhdGVkUGFzc3dvcmREaXNwbGF5U2VsZWN0b3IiLCIkaW5wdXRzQmxvY2siLCIkbmV3UGFzc3dvcmRJbnB1dHMiLCIkY29weVBhc3N3b3JkSW5wdXRzIiwiYWRkIiwiJHN1Ym1pdHRhYmxlSW5wdXRzIiwicGFzc3dvcmRIYW5kbGVyIiwicGFzc3dvcmRWYWxpZGF0b3IiLCJQYXNzd29yZFZhbGlkYXRvciIsIl9oaWRlSW5wdXRzQmxvY2siLCJlIiwiX2hpZGUiLCJfc2hvd0lucHV0c0Jsb2NrIiwiX3Nob3ciLCJfY2hlY2tQYXNzd29yZFZhbGlkaXR5IiwiJGZpcnN0UGFzc3dvcmRFcnJvckNvbnRhaW5lciIsInBhcmVudCIsIiRzZWNvbmRQYXNzd29yZEVycm9yQ29udGFpbmVyIiwiX2dldFBhc3N3b3JkTGVuZ3RoVmFsaWRhdGlvbk1lc3NhZ2UiLCJpc1Bhc3N3b3JkTGVuZ3RoVmFsaWQiLCJfZ2V0UGFzc3dvcmRDb25maXJtYXRpb25WYWxpZGF0aW9uTWVzc2FnZSIsImlzUGFzc3dvcmRNYXRjaGluZ0NvbmZpcm1hdGlvbiIsImlzUGFzc3dvcmRUb29TaG9ydCIsImlzUGFzc3dvcmRUb29Mb25nIiwiJGVsIiwicGFzc3dvcmRJbnB1dFNlbGVjdG9yIiwiY29uZmlybVBhc3N3b3JkSW5wdXRTZWxlY3RvciIsInF1ZXJ5U2VsZWN0b3IiLCJjb25maXJtUGFzc3dvcmRJbnB1dCIsIm1pblBhc3N3b3JkTGVuZ3RoIiwibWF4UGFzc3dvcmRMZW5ndGgiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFD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QUFMLENBQTBCekYsS0FBMUIsS0FBb0MsRUFBeEMsRUFBNEM7QUFDMUMsZUFBTyxJQUFQO0FBQ0Q7O0FBRUQsYUFBTyxLQUFLNUIsZ0JBQUwsQ0FBc0I0QixLQUF0QixLQUFnQyxLQUFLeUYsb0JBQUwsQ0FBMEJ6RixLQUFqRTtBQUNEOztBQUVEOzs7Ozs7Ozt5Q0FLcUI7QUFDbkIsYUFBTyxLQUFLNUIsZ0JBQUwsQ0FBc0I0QixLQUF0QixDQUE0QlYsTUFBNUIsR0FBcUMsS0FBS29HLGlCQUFqRDtBQUNEOztBQUVEOzs7Ozs7Ozt3Q0FLb0I7QUFDbEIsYUFBTyxLQUFLdEgsZ0JBQUwsQ0FBc0I0QixLQUF0QixDQUE0QlYsTUFBNUIsR0FBcUMsS0FBS3FHLGlCQUFqRDtBQUNEOzs7Ozs7a0JBekVrQnRCLGlCOzs7Ozs7Ozs7Ozs7O0FDOUJyQjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQTs7O2tCQUdlO0FBQ2IvRyxrQkFBZ0IsNEJBREg7QUFFYkUsaUJBQWUsbUJBRkY7QUFHYkUscUJBQW1CLHdCQUhOO0FBSWJFLHFCQUFtQixzQkFKTjtBQUtiQyxxQkFBbUIsbUJBTE47O0FBT2I7QUFDQUUsNkJBQTJCLDJCQVJkO0FBU2JDLGlDQUErQixxQkFUbEI7QUFVYkMsaUNBQStCLDRCQVZsQjtBQVdiQywwQkFBd0Isb0RBWFg7QUFZYkMsb0JBQWtCLHdDQVpMO0FBYWJDLG9CQUFrQiw4Q0FiTDtBQWNiQywyQkFBeUIsK0NBZFo7QUFlYkMsaUNBQStCLDhDQWZsQjtBQWdCYkMscUNBQW1DO0FBaEJ0QixDOzs7Ozs7Ozs7O0FDSGY7Ozs7OztBQUVBNUQsRUFBRSxZQUFNO0FBQ04sTUFBSXdDLHNCQUFKO0FBQ0QsQ0FGRCxFLENBM0JBIiwiZmlsZSI6ImVtcGxveWVlX2Zvcm0uYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAzMjMpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDY4ZTgyOTFmMTM2MDcwZjI3NmJkIiwiKGZ1bmN0aW9uKCkgeyBtb2R1bGUuZXhwb3J0cyA9IHdpbmRvd1tcImpRdWVyeVwiXTsgfSgpKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyBleHRlcm5hbCBcImpRdWVyeVwiXG4vLyBtb2R1bGUgaWQgPSAxMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA2IDIzIDMwIDMyIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIEhhbmRsZXMgVUkgaW50ZXJhY3Rpb25zIG9mIGNob2ljZSB0cmVlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENob2ljZVRyZWUge1xuICAvKipcbiAgICogQHBhcmFtIHtTdHJpbmd9IHRyZWVTZWxlY3RvclxuICAgKi9cbiAgY29uc3RydWN0b3IodHJlZVNlbGVjdG9yKSB7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJCh0cmVlU2VsZWN0b3IpO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsICcuanMtaW5wdXQtd3JhcHBlcicsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGlucHV0V3JhcHBlciA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG5cbiAgICAgIHRoaXMuX3RvZ2dsZUNoaWxkVHJlZSgkaW5wdXRXcmFwcGVyKTtcbiAgICB9KTtcblxuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2xpY2snLCAnLmpzLXRvZ2dsZS1jaG9pY2UtdHJlZS1hY3Rpb24nLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRhY3Rpb24gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICB0aGlzLl90b2dnbGVUcmVlKCRhY3Rpb24pO1xuICAgIH0pO1xuXG4gICAgcmV0dXJuIHtcbiAgICAgIGVuYWJsZUF1dG9DaGVja0NoaWxkcmVuOiAoKSA9PiB0aGlzLmVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCksXG4gICAgICBlbmFibGVBbGxJbnB1dHM6ICgpID0+IHRoaXMuZW5hYmxlQWxsSW5wdXRzKCksXG4gICAgICBkaXNhYmxlQWxsSW5wdXRzOiAoKSA9PiB0aGlzLmRpc2FibGVBbGxJbnB1dHMoKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEVuYWJsZSBhdXRvbWF0aWMgY2hlY2svdW5jaGVjayBvZiBjbGlja2VkIGl0ZW0ncyBjaGlsZHJlbi5cbiAgICovXG4gIGVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2hhbmdlJywgJ2lucHV0W3R5cGU9XCJjaGVja2JveFwiXScsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGNsaWNrZWRDaGVja2JveCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBjb25zdCAkaXRlbVdpdGhDaGlsZHJlbiA9ICRjbGlja2VkQ2hlY2tib3guY2xvc2VzdCgnbGknKTtcblxuICAgICAgJGl0ZW1XaXRoQ2hpbGRyZW5cbiAgICAgICAgLmZpbmQoJ3VsIGlucHV0W3R5cGU9XCJjaGVja2JveFwiXScpXG4gICAgICAgIC5wcm9wKCdjaGVja2VkJywgJGNsaWNrZWRDaGVja2JveC5pcygnOmNoZWNrZWQnKSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogRW5hYmxlIGFsbCBpbnB1dHMgaW4gdGhlIGNob2ljZSB0cmVlLlxuICAgKi9cbiAgZW5hYmxlQWxsSW5wdXRzKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5maW5kKCdpbnB1dCcpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gIH1cblxuICAvKipcbiAgICogRGlzYWJsZSBhbGwgaW5wdXRzIGluIHRoZSBjaG9pY2UgdHJlZS5cbiAgICovXG4gIGRpc2FibGVBbGxJbnB1dHMoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLmZpbmQoJ2lucHV0JykuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDb2xsYXBzZSBvciBleHBhbmQgc3ViLXRyZWUgZm9yIHNpbmdsZSBwYXJlbnRcbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRpbnB1dFdyYXBwZXJcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGVDaGlsZFRyZWUoJGlucHV0V3JhcHBlcikge1xuICAgIGNvbnN0ICRwYXJlbnRXcmFwcGVyID0gJGlucHV0V3JhcHBlci5jbG9zZXN0KCdsaScpO1xuXG4gICAgaWYgKCRwYXJlbnRXcmFwcGVyLmhhc0NsYXNzKCdleHBhbmRlZCcpKSB7XG4gICAgICAkcGFyZW50V3JhcHBlclxuICAgICAgICAucmVtb3ZlQ2xhc3MoJ2V4cGFuZGVkJylcbiAgICAgICAgLmFkZENsYXNzKCdjb2xsYXBzZWQnKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGlmICgkcGFyZW50V3JhcHBlci5oYXNDbGFzcygnY29sbGFwc2VkJykpIHtcbiAgICAgICRwYXJlbnRXcmFwcGVyXG4gICAgICAgIC5yZW1vdmVDbGFzcygnY29sbGFwc2VkJylcbiAgICAgICAgLmFkZENsYXNzKCdleHBhbmRlZCcpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBDb2xsYXBzZSBvciBleHBhbmQgd2hvbGUgdHJlZVxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGFjdGlvblxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3RvZ2dsZVRyZWUoJGFjdGlvbikge1xuICAgIGNvbnN0ICRwYXJlbnRDb250YWluZXIgPSAkYWN0aW9uLmNsb3Nlc3QoJy5qcy1jaG9pY2UtdHJlZS1jb250YWluZXInKTtcbiAgICBjb25zdCBhY3Rpb24gPSAkYWN0aW9uLmRhdGEoJ2FjdGlvbicpO1xuXG4gICAgLy8gdG9nZ2xlIGFjdGlvbiBjb25maWd1cmF0aW9uXG4gICAgY29uc3QgY29uZmlnID0ge1xuICAgICAgYWRkQ2xhc3M6IHtcbiAgICAgICAgZXhwYW5kOiAnZXhwYW5kZWQnLFxuICAgICAgICBjb2xsYXBzZTogJ2NvbGxhcHNlZCcsXG4gICAgICB9LFxuICAgICAgcmVtb3ZlQ2xhc3M6IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2VkJyxcbiAgICAgICAgY29sbGFwc2U6ICdleHBhbmRlZCcsXG4gICAgICB9LFxuICAgICAgbmV4dEFjdGlvbjoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZScsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kJyxcbiAgICAgIH0sXG4gICAgICB0ZXh0OiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlZC10ZXh0JyxcbiAgICAgICAgY29sbGFwc2U6ICdleHBhbmRlZC10ZXh0JyxcbiAgICAgIH0sXG4gICAgICBpY29uOiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlZC1pY29uJyxcbiAgICAgICAgY29sbGFwc2U6ICdleHBhbmRlZC1pY29uJyxcbiAgICAgIH1cbiAgICB9O1xuXG4gICAgJHBhcmVudENvbnRhaW5lci5maW5kKCdsaScpLmVhY2goKGluZGV4LCBpdGVtKSA9PiB7XG4gICAgICBjb25zdCAkaXRlbSA9ICQoaXRlbSk7XG5cbiAgICAgIGlmICgkaXRlbS5oYXNDbGFzcyhjb25maWcucmVtb3ZlQ2xhc3NbYWN0aW9uXSkpIHtcbiAgICAgICAgICAkaXRlbS5yZW1vdmVDbGFzcyhjb25maWcucmVtb3ZlQ2xhc3NbYWN0aW9uXSlcbiAgICAgICAgICAgIC5hZGRDbGFzcyhjb25maWcuYWRkQ2xhc3NbYWN0aW9uXSk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICAkYWN0aW9uLmRhdGEoJ2FjdGlvbicsIGNvbmZpZy5uZXh0QWN0aW9uW2FjdGlvbl0pO1xuICAgICRhY3Rpb24uZmluZCgnLm1hdGVyaWFsLWljb25zJykudGV4dCgkYWN0aW9uLmRhdGEoY29uZmlnLmljb25bYWN0aW9uXSkpO1xuICAgICRhY3Rpb24uZmluZCgnLmpzLXRvZ2dsZS10ZXh0JykudGV4dCgkYWN0aW9uLmRhdGEoY29uZmlnLnRleHRbYWN0aW9uXSkpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2Zvcm0vY2hvaWNlLXRyZWUuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgQ2hvaWNlVHJlZSBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9mb3JtL2Nob2ljZS10cmVlXCI7XG5pbXBvcnQgQWRkb25zQ29ubmVjdG9yIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2FkZG9ucy1jb25uZWN0b3JcIjtcbmltcG9ydCBDaGFuZ2VQYXNzd29yZENvbnRyb2wgZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZm9ybS9jaGFuZ2UtcGFzc3dvcmQtY29udHJvbFwiO1xuaW1wb3J0IGVtcGxveWVlRm9ybU1hcCBmcm9tIFwiLi9lbXBsb3llZS1mb3JtLW1hcFwiO1xuXG4vKipcbiAqIENsYXNzIHJlc3BvbnNpYmxlIGZvciBqYXZhc2NyaXB0IGFjdGlvbnMgaW4gZW1wbG95ZWUgYWRkL2VkaXQgcGFnZS5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRW1wbG95ZWVGb3JtIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5zaG9wQ2hvaWNlVHJlZVNlbGVjdG9yID0gZW1wbG95ZWVGb3JtTWFwLnNob3BDaG9pY2VUcmVlO1xuICAgIHRoaXMuc2hvcENob2ljZVRyZWUgPSBuZXcgQ2hvaWNlVHJlZSh0aGlzLnNob3BDaG9pY2VUcmVlU2VsZWN0b3IpO1xuICAgIHRoaXMuZW1wbG95ZWVQcm9maWxlU2VsZWN0b3IgPSBlbXBsb3llZUZvcm1NYXAucHJvZmlsZVNlbGVjdDtcbiAgICB0aGlzLnRhYnNEcm9wZG93blNlbGVjdG9yID0gZW1wbG95ZWVGb3JtTWFwLmRlZmF1bHRQYWdlU2VsZWN0O1xuXG4gICAgdGhpcy5zaG9wQ2hvaWNlVHJlZS5lbmFibGVBdXRvQ2hlY2tDaGlsZHJlbigpO1xuXG4gICAgbmV3IEFkZG9uc0Nvbm5lY3RvcihcbiAgICAgIGVtcGxveWVlRm9ybU1hcC5hZGRvbnNDb25uZWN0Rm9ybSxcbiAgICAgIGVtcGxveWVlRm9ybU1hcC5hZGRvbnNMb2dpbkJ1dHRvblxuICAgICk7XG5cbiAgICBuZXcgQ2hhbmdlUGFzc3dvcmRDb250cm9sKFxuICAgICAgZW1wbG95ZWVGb3JtTWFwLmNoYW5nZVBhc3N3b3JkSW5wdXRzQmxvY2ssXG4gICAgICBlbXBsb3llZUZvcm1NYXAuc2hvd0NoYW5nZVBhc3N3b3JkQmxvY2tCdXR0b24sXG4gICAgICBlbXBsb3llZUZvcm1NYXAuaGlkZUNoYW5nZVBhc3N3b3JkQmxvY2tCdXR0b24sXG4gICAgICBlbXBsb3llZUZvcm1NYXAuZ2VuZXJhdGVQYXNzd29yZEJ1dHRvbixcbiAgICAgIGVtcGxveWVlRm9ybU1hcC5vbGRQYXNzd29yZElucHV0LFxuICAgICAgZW1wbG95ZWVGb3JtTWFwLm5ld1Bhc3N3b3JkSW5wdXQsXG4gICAgICBlbXBsb3llZUZvcm1NYXAuY29uZmlybU5ld1Bhc3N3b3JkSW5wdXQsXG4gICAgICBlbXBsb3llZUZvcm1NYXAuZ2VuZXJhdGVkUGFzc3dvcmREaXNwbGF5SW5wdXQsXG4gICAgICBlbXBsb3llZUZvcm1NYXAucGFzc3dvcmRTdHJlbmd0aEZlZWRiYWNrQ29udGFpbmVyXG4gICAgKTtcblxuICAgIHRoaXMuX2luaXRFdmVudHMoKTtcbiAgICB0aGlzLl90b2dnbGVTaG9wVHJlZSgpO1xuXG4gICAgcmV0dXJuIHt9O1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRpYWxpemUgcGFnZSdzIGV2ZW50cy5cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0RXZlbnRzKCkge1xuICAgIGNvbnN0ICRlbXBsb3llZVByb2ZpbGVzRHJvcGRvd24gPSAkKHRoaXMuZW1wbG95ZWVQcm9maWxlU2VsZWN0b3IpO1xuICAgIGNvbnN0IGdldFRhYnNVcmwgPSAkZW1wbG95ZWVQcm9maWxlc0Ryb3Bkb3duLmRhdGEoJ2dldC10YWJzLXVybCcpO1xuXG4gICAgJChkb2N1bWVudCkub24oJ2NoYW5nZScsIHRoaXMuZW1wbG95ZWVQcm9maWxlU2VsZWN0b3IsICgpID0+IHRoaXMuX3RvZ2dsZVNob3BUcmVlKCkpO1xuXG4gICAgLy8gUmVsb2FkIHRhYnMgZHJvcGRvd24gd2hlbiBlbXBsb3llZSBwcm9maWxlIGlzIGNoYW5nZWQuXG4gICAgJChkb2N1bWVudCkub24oJ2NoYW5nZScsIHRoaXMuZW1wbG95ZWVQcm9maWxlU2VsZWN0b3IsIChldmVudCkgPT4ge1xuICAgICAgJC5nZXQoXG4gICAgICAgIGdldFRhYnNVcmwsXG4gICAgICAgIHtcbiAgICAgICAgICBwcm9maWxlSWQ6ICQoZXZlbnQuY3VycmVudFRhcmdldCkudmFsKClcbiAgICAgICAgfSxcbiAgICAgICAgKHRhYnMpID0+IHtcbiAgICAgICAgICB0aGlzLl9yZWxvYWRUYWJzRHJvcGRvd24odGFicyk7XG4gICAgICAgIH0sXG4gICAgICAgICdqc29uJ1xuICAgICAgKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZWxvYWQgdGFicyBkcm9wZG93biB3aXRoIG5ldyBjb250ZW50LlxuICAgKlxuICAgKiBAcGFyYW0ge09iamVjdH0gYWNjZXNzaWJsZVRhYnNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZWxvYWRUYWJzRHJvcGRvd24oYWNjZXNzaWJsZVRhYnMpIHtcbiAgICBjb25zdCAkdGFic0Ryb3Bkb3duID0gJCh0aGlzLnRhYnNEcm9wZG93blNlbGVjdG9yKTtcblxuICAgICR0YWJzRHJvcGRvd24uZW1wdHkoKTtcblxuICAgIGZvciAobGV0IGtleSBpbiBhY2Nlc3NpYmxlVGFicykge1xuICAgICAgaWYgKGFjY2Vzc2libGVUYWJzW2tleV1bJ2NoaWxkcmVuJ10ubGVuZ3RoID4gMCAmJiBhY2Nlc3NpYmxlVGFic1trZXldWyduYW1lJ10pIHtcbiAgICAgICAgLy8gSWYgdGFiIGhhcyBjaGlsZHJlbiAtIGNyZWF0ZSBhbiBvcHRpb24gZ3JvdXAgYW5kIHB1dCBjaGlsZHJlbiBpbnNpZGUuXG4gICAgICAgIGNvbnN0ICRvcHRncm91cCA9IHRoaXMuX2NyZWF0ZU9wdGlvbkdyb3VwKGFjY2Vzc2libGVUYWJzW2tleV1bJ25hbWUnXSk7XG5cbiAgICAgICAgZm9yIChsZXQgY2hpbGRLZXkgaW4gYWNjZXNzaWJsZVRhYnNba2V5XVsnY2hpbGRyZW4nXSkge1xuICAgICAgICAgIGlmIChhY2Nlc3NpYmxlVGFic1trZXldWydjaGlsZHJlbiddW2NoaWxkS2V5XVsnbmFtZSddKSB7XG4gICAgICAgICAgICAkb3B0Z3JvdXAuYXBwZW5kKFxuICAgICAgICAgICAgICB0aGlzLl9jcmVhdGVPcHRpb24oXG4gICAgICAgICAgICAgICAgYWNjZXNzaWJsZVRhYnNba2V5XVsnY2hpbGRyZW4nXVtjaGlsZEtleV1bJ25hbWUnXSxcbiAgICAgICAgICAgICAgICBhY2Nlc3NpYmxlVGFic1trZXldWydjaGlsZHJlbiddW2NoaWxkS2V5XVsnaWRfdGFiJ10pXG4gICAgICAgICAgICApO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgICR0YWJzRHJvcGRvd24uYXBwZW5kKCRvcHRncm91cCk7XG4gICAgICB9IGVsc2UgaWYgKGFjY2Vzc2libGVUYWJzW2tleV1bJ25hbWUnXSkge1xuICAgICAgICAvLyBJZiB0YWIgZG9lc24ndCBoYXZlIGNoaWxkcmVuIC0gY3JlYXRlIGFuIG9wdGlvbi5cbiAgICAgICAgJHRhYnNEcm9wZG93bi5hcHBlbmQoXG4gICAgICAgICAgdGhpcy5fY3JlYXRlT3B0aW9uKFxuICAgICAgICAgICAgYWNjZXNzaWJsZVRhYnNba2V5XVsnbmFtZSddLFxuICAgICAgICAgICAgYWNjZXNzaWJsZVRhYnNba2V5XVsnaWRfdGFiJ11cbiAgICAgICAgICApXG4gICAgICAgICk7XG4gICAgICB9XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEhpZGUgc2hvcCBjaG9pY2UgdHJlZSBpZiBzdXBlcmFkbWluIHByb2ZpbGUgaXMgc2VsZWN0ZWQsIHNob3cgaXQgb3RoZXJ3aXNlLlxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3RvZ2dsZVNob3BUcmVlKCkge1xuICAgIGNvbnN0ICRlbXBsb3llZVByb2ZpbGVEcm9wZG93biA9ICQodGhpcy5lbXBsb3llZVByb2ZpbGVTZWxlY3Rvcik7XG4gICAgY29uc3Qgc3VwZXJBZG1pblByb2ZpbGVJZCA9ICRlbXBsb3llZVByb2ZpbGVEcm9wZG93bi5kYXRhKCdhZG1pbi1wcm9maWxlJyk7XG4gICAgJCh0aGlzLnNob3BDaG9pY2VUcmVlU2VsZWN0b3IpXG4gICAgICAuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKVxuICAgICAgLnRvZ2dsZUNsYXNzKCdkLW5vbmUnLCAkZW1wbG95ZWVQcm9maWxlRHJvcGRvd24udmFsKCkgPT0gc3VwZXJBZG1pblByb2ZpbGVJZClcbiAgICA7XG4gIH1cblxuICAvKipcbiAgICogQ3JlYXRlcyBhbiA8b3B0Z3JvdXA+IGVsZW1lbnRcbiAgICpcbiAgICogQHBhcmFtIHtTdHJpbmd9IG5hbWVcbiAgICpcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9jcmVhdGVPcHRpb25Hcm91cChuYW1lKSB7XG4gICAgcmV0dXJuICQoYDxvcHRncm91cCBsYWJlbD1cIiR7bmFtZX1cIj5gKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDcmVhdGVzIGFuIDxvcHRpb24+IGVsZW1lbnQuXG4gICAqXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lXG4gICAqIEBwYXJhbSB7U3RyaW5nfSB2YWx1ZVxuICAgKlxuICAgKiBAcmV0dXJucyB7alF1ZXJ5fVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NyZWF0ZU9wdGlvbihuYW1lLCB2YWx1ZSkge1xuICAgIHJldHVybiAkKGA8b3B0aW9uIHZhbHVlPVwiJHt2YWx1ZX1cIj4ke25hbWV9PC9vcHRpb24+YCk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2VtcGxveWVlL0VtcGxveWVlRm9ybS5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBSZXNwb25zaWJsZSBmb3IgY29ubmVjdGluZyB0byBhZGRvbnMgbWFya2V0cGxhY2UuXG4gKiBNYWtlcyBhbiBhZGRvbnMgY29ubmVjdCByZXF1ZXN0IHRvIHRoZSBzZXJ2ZXIsIGRpc3BsYXlzIGVycm9yIG1lc3NhZ2VzIGlmIGl0IGZhaWxzLlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBBZGRvbnNDb25uZWN0b3Ige1xuICBjb25zdHJ1Y3RvcihcbiAgICBhZGRvbnNDb25uZWN0Rm9ybVNlbGVjdG9yLFxuICAgIGxvYWRpbmdTcGlubmVyU2VsZWN0b3JcbiAgKSB7XG4gICAgdGhpcy5hZGRvbnNDb25uZWN0Rm9ybVNlbGVjdG9yID0gYWRkb25zQ29ubmVjdEZvcm1TZWxlY3RvcjtcbiAgICB0aGlzLiRsb2FkaW5nU3Bpbm5lciA9ICQobG9hZGluZ1NwaW5uZXJTZWxlY3Rvcik7XG5cbiAgICB0aGlzLl9pbml0RXZlbnRzKCk7XG5cbiAgICByZXR1cm4ge307XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhbGl6ZSBldmVudHMgcmVsYXRlZCB0byBjb25uZWN0aW9uIHRvIGFkZG9ucy5cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0RXZlbnRzKCkge1xuICAgICQoJ2JvZHknKS5vbignc3VibWl0JywgdGhpcy5hZGRvbnNDb25uZWN0Rm9ybVNlbGVjdG9yLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRmb3JtID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblxuICAgICAgdGhpcy5fY29ubmVjdCgkZm9ybS5hdHRyKCdhY3Rpb24nKSwgJGZvcm0uc2VyaWFsaXplKCkpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIERvIGEgUE9TVCByZXF1ZXN0IHRvIGNvbm5lY3QgdG8gYWRkb25zLlxuICAgKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gYWRkb25zQ29ubmVjdFVybFxuICAgKiBAcGFyYW0ge09iamVjdH0gZm9ybURhdGFcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9jb25uZWN0KGFkZG9uc0Nvbm5lY3RVcmwsIGZvcm1EYXRhKSB7XG4gICAgJC5hamF4KHtcbiAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgICAgdXJsOiBhZGRvbnNDb25uZWN0VXJsLFxuICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgIGRhdGE6IGZvcm1EYXRhLFxuICAgICAgYmVmb3JlU2VuZDogKCkgPT4ge1xuICAgICAgICB0aGlzLiRsb2FkaW5nU3Bpbm5lci5zaG93KCk7XG4gICAgICAgICQoJ2J1dHRvbi5idG5bdHlwZT1cInN1Ym1pdFwiXScsIHRoaXMuYWRkb25zQ29ubmVjdEZvcm1TZWxlY3RvcikuaGlkZSgpO1xuICAgICAgfVxuICAgIH0pLnRoZW4oKHJlc3BvbnNlKSA9PiB7XG4gICAgICBpZiAocmVzcG9uc2Uuc3VjY2VzcyA9PT0gMSkge1xuICAgICAgICBsb2NhdGlvbi5yZWxvYWQoKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgICQuZ3Jvd2wuZXJyb3Ioe1xuICAgICAgICAgIG1lc3NhZ2U6IHJlc3BvbnNlLm1lc3NhZ2VcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy4kbG9hZGluZ1NwaW5uZXIuaGlkZSgpO1xuICAgICAgICAkKCdidXR0b24uYnRuW3R5cGU9XCJzdWJtaXRcIl0nLCB0aGlzLmFkZG9uc0Nvbm5lY3RGb3JtU2VsZWN0b3IpLmZhZGVJbigpO1xuICAgICAgfVxuICAgIH0sICgpID0+IHtcbiAgICAgICQuZ3Jvd2wuZXJyb3Ioe1xuICAgICAgICBtZXNzYWdlOiAkKHRoaXMuYWRkb25zQ29ubmVjdEZvcm1TZWxlY3RvcikuZGF0YSgnZXJyb3ItbWVzc2FnZScpLFxuICAgICAgfSk7XG5cbiAgICAgIHRoaXMuJGxvYWRpbmdTcGlubmVyLmhpZGUoKTtcbiAgICAgICQoJ2J1dHRvbi5idG5bdHlwZT1cInN1Ym1pdFwiXScsIHRoaXMuYWRkb25zQ29ubmVjdEZvcm1TZWxlY3Rvcikuc2hvdygpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2FkZG9ucy1jb25uZWN0b3IuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogR2VuZXJhdGVzIGEgcGFzc3dvcmQgYW5kIGluZm9ybXMgYWJvdXQgaXQncyBzdHJlbmd0aC5cbiAqIFlvdSBjYW4gcGFzcyBhIHBhc3N3b3JkIGlucHV0IHRvIHdhdGNoIHRoZSBwYXNzd29yZCBzdHJlbmd0aCBhbmQgZGlzcGxheSBmZWVkYmFjayBtZXNzYWdlcy5cbiAqIFlvdSBjYW4gYWxzbyBnZW5lcmF0ZSBhIHJhbmRvbSBwYXNzd29yZCBpbnRvIGFuIGlucHV0LlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDaGFuZ2VQYXNzd29yZEhhbmRsZXIge1xuICBjb25zdHJ1Y3RvcihwYXNzd29yZFN0cmVuZ3RoRmVlZGJhY2tDb250YWluZXJTZWxlY3Rvciwgb3B0aW9ucyA9IHt9KSB7XG4gICAgLy8gTWluaW11bSBsZW5ndGggb2YgdGhlIGdlbmVyYXRlZCBwYXNzd29yZC5cbiAgICB0aGlzLm1pbkxlbmd0aCA9IG9wdGlvbnMubWluTGVuZ3RoIHx8IDg7XG5cbiAgICAvLyBGZWVkYmFjayBjb250YWluZXIgaG9sZHMgbWVzc2FnZXMgcmVwcmVzZW50aW5nIHBhc3N3b3JkIHN0cmVuZ3RoLlxuICAgIHRoaXMuJGZlZWRiYWNrQ29udGFpbmVyID0gJChwYXNzd29yZFN0cmVuZ3RoRmVlZGJhY2tDb250YWluZXJTZWxlY3Rvcik7XG5cbiAgICByZXR1cm4ge1xuICAgICAgd2F0Y2hQYXNzd29yZFN0cmVuZ3RoOiAoJGlucHV0KSA9PiB0aGlzLndhdGNoUGFzc3dvcmRTdHJlbmd0aCgkaW5wdXQpLFxuICAgICAgZ2VuZXJhdGVQYXNzd29yZDogKCRpbnB1dCkgPT4gdGhpcy5nZW5lcmF0ZVBhc3N3b3JkKCRpbnB1dCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBXYXRjaCBwYXNzd29yZCwgd2hpY2ggaXMgZW50ZXJlZCBpbiB0aGUgaW5wdXQsIHN0cmVuZ3RoIGFuZCBpbmZvcm0gYWJvdXQgaXQuXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkaW5wdXQgdGhlIGlucHV0IHRvIHdhdGNoLlxuICAgKi9cbiAgd2F0Y2hQYXNzd29yZFN0cmVuZ3RoKCRpbnB1dCkge1xuICAgICQucGFzc3kucmVxdWlyZW1lbnRzLmxlbmd0aC5taW4gPSB0aGlzLm1pbkxlbmd0aDtcbiAgICAkLnBhc3N5LnJlcXVpcmVtZW50cy5jaGFyYWN0ZXJzID0gJ0RJR0lUJztcblxuICAgICRpbnB1dC5lYWNoKChpbmRleCwgZWxlbWVudCkgPT4ge1xuICAgICAgY29uc3QgJG91dHB1dENvbnRhaW5lciA9ICQoJzxzcGFuPicpO1xuXG4gICAgICAkb3V0cHV0Q29udGFpbmVyLmluc2VydEFmdGVyKCQoZWxlbWVudCkpO1xuXG4gICAgICAkKGVsZW1lbnQpLnBhc3N5KChzdHJlbmd0aCwgdmFsaWQpID0+IHtcbiAgICAgICAgdGhpcy5fZGlzcGxheUZlZWRiYWNrKCRvdXRwdXRDb250YWluZXIsIHN0cmVuZ3RoLCB2YWxpZCk7XG4gICAgICB9KTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZW5lcmF0ZXMgYSBwYXNzd29yZCBhbmQgZmlsbHMgaXQgdG8gZ2l2ZW4gaW5wdXQuXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkaW5wdXQgdGhlIGlucHV0IHRvIGZpbGwgdGhlIHBhc3N3b3JkIGludG8uXG4gICAqL1xuICBnZW5lcmF0ZVBhc3N3b3JkKCRpbnB1dCkge1xuICAgICRpbnB1dC5wYXNzeSgnZ2VuZXJhdGUnLCB0aGlzLm1pbkxlbmd0aCk7XG4gIH1cblxuICAvKipcbiAgICogRGlzcGxheSBmZWVkYmFjayBhYm91dCBwYXNzd29yZCdzIHN0cmVuZ3RoLlxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJG91dHB1dENvbnRhaW5lciBhIGNvbnRhaW5lciB0byBwdXQgZmVlZGJhY2sgb3V0cHV0IGludG8uXG4gICAqIEBwYXJhbSB7bnVtYmVyfSBwYXNzd29yZFN0cmVuZ3RoXG4gICAqIEBwYXJhbSB7Ym9vbGVhbn0gaXNQYXNzd29yZFZhbGlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZGlzcGxheUZlZWRiYWNrKCRvdXRwdXRDb250YWluZXIsIHBhc3N3b3JkU3RyZW5ndGgsIGlzUGFzc3dvcmRWYWxpZCkge1xuICAgIGNvbnN0IGZlZWRiYWNrID0gdGhpcy5fZ2V0UGFzc3dvcmRTdHJlbmd0aEZlZWRiYWNrKHBhc3N3b3JkU3RyZW5ndGgpO1xuICAgICRvdXRwdXRDb250YWluZXIudGV4dChmZWVkYmFjay5tZXNzYWdlKTtcbiAgICAkb3V0cHV0Q29udGFpbmVyLnJlbW92ZUNsYXNzKCd0ZXh0LWRhbmdlciB0ZXh0LXdhcm5pbmcgdGV4dC1zdWNjZXNzJyk7XG4gICAgJG91dHB1dENvbnRhaW5lci5hZGRDbGFzcyhmZWVkYmFjay5lbGVtZW50Q2xhc3MpO1xuICAgICRvdXRwdXRDb250YWluZXIudG9nZ2xlQ2xhc3MoJ2Qtbm9uZScsICFpc1Bhc3N3b3JkVmFsaWQpO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBmZWVkYmFjayB0aGF0IGRlc2NyaWJlcyBnaXZlbiBwYXNzd29yZCBzdHJlbmd0aC5cbiAgICogUmVzcG9uc2UgY29udGFpbnMgdGV4dCBtZXNzYWdlIGFuZCBlbGVtZW50IGNsYXNzLlxuICAgKlxuICAgKiBAcGFyYW0ge251bWJlcn0gc3RyZW5ndGhcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRQYXNzd29yZFN0cmVuZ3RoRmVlZGJhY2soc3RyZW5ndGgpIHtcbiAgICBzd2l0Y2ggKHN0cmVuZ3RoKSB7XG4gICAgICBjYXNlICQucGFzc3kuc3RyZW5ndGguTE9XOlxuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgIG1lc3NhZ2U6IHRoaXMuJGZlZWRiYWNrQ29udGFpbmVyLmZpbmQoJy5zdHJlbmd0aC1sb3cnKS50ZXh0KCksXG4gICAgICAgICAgZWxlbWVudENsYXNzOiAndGV4dC1kYW5nZXInLFxuICAgICAgICB9O1xuXG4gICAgICBjYXNlICQucGFzc3kuc3RyZW5ndGguTUVESVVNOlxuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgIG1lc3NhZ2U6IHRoaXMuJGZlZWRiYWNrQ29udGFpbmVyLmZpbmQoJy5zdHJlbmd0aC1tZWRpdW0nKS50ZXh0KCksXG4gICAgICAgICAgZWxlbWVudENsYXNzOiAndGV4dC13YXJuaW5nJyxcbiAgICAgICAgfTtcblxuICAgICAgY2FzZSAkLnBhc3N5LnN0cmVuZ3RoLkhJR0g6XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgbWVzc2FnZTogdGhpcy4kZmVlZGJhY2tDb250YWluZXIuZmluZCgnLnN0cmVuZ3RoLWhpZ2gnKS50ZXh0KCksXG4gICAgICAgICAgZWxlbWVudENsYXNzOiAndGV4dC1zdWNjZXNzJyxcbiAgICAgICAgfTtcblxuICAgICAgY2FzZSAkLnBhc3N5LnN0cmVuZ3RoLkVYVFJFTUU6XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgbWVzc2FnZTogdGhpcy4kZmVlZGJhY2tDb250YWluZXIuZmluZCgnLnN0cmVuZ3RoLWV4dHJlbWUnKS50ZXh0KCksXG4gICAgICAgICAgZWxlbWVudENsYXNzOiAndGV4dC1zdWNjZXNzJyxcbiAgICAgICAgfTtcbiAgICB9XG5cbiAgICB0aHJvdyAnSW52YWxpZCBwYXNzd29yZCBzdHJlbmd0aCBpbmRpY2F0b3IuJztcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9jaGFuZ2UtcGFzc3dvcmQtaGFuZGxlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBDaGFuZ2VQYXNzd29yZEhhbmRsZXIgZnJvbSBcIi4uL2NoYW5nZS1wYXNzd29yZC1oYW5kbGVyXCI7XG5pbXBvcnQgUGFzc3dvcmRWYWxpZGF0b3IgZnJvbSBcIi4uL3Bhc3N3b3JkLXZhbGlkYXRvclwiO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgcmVzcG9uc2libGUgZm9yIGFjdGlvbnMgcmVsYXRlZCB0byBcImNoYW5nZSBwYXNzd29yZFwiIGZvcm0gdHlwZS5cbiAqIEdlbmVyYXRlcyByYW5kb20gcGFzc3dvcmRzLCB2YWxpZGF0ZXMgbmV3IHBhc3N3b3JkIGFuZCBpdCdzIGNvbmZpcm1hdGlvbixcbiAqIGRpc3BsYXlzIGVycm9yIG1lc3NhZ2VzIHJlbGF0ZWQgdG8gdmFsaWRhdGlvbi5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ2hhbmdlUGFzc3dvcmRDb250cm9sIHtcbiAgY29uc3RydWN0b3IoXG4gICAgaW5wdXRzQmxvY2tTZWxlY3RvcixcbiAgICBzaG93QnV0dG9uU2VsZWN0b3IsXG4gICAgaGlkZUJ1dHRvblNlbGVjdG9yLFxuICAgIGdlbmVyYXRlUGFzc3dvcmRCdXR0b25TZWxlY3RvcixcbiAgICBvbGRQYXNzd29yZElucHV0U2VsZWN0b3IsXG4gICAgbmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yLFxuICAgIGNvbmZpcm1OZXdQYXNzd29yZElucHV0U2VsZWN0b3IsXG4gICAgZ2VuZXJhdGVkUGFzc3dvcmREaXNwbGF5U2VsZWN0b3IsXG4gICAgcGFzc3dvcmRTdHJlbmd0aEZlZWRiYWNrQ29udGFpbmVyU2VsZWN0b3JcbiAgKSB7XG4gICAgLy8gQmxvY2sgdGhhdCBjb250YWlucyBwYXNzd29yZCBpbnB1dHNcbiAgICB0aGlzLiRpbnB1dHNCbG9jayA9ICQoaW5wdXRzQmxvY2tTZWxlY3Rvcik7XG5cbiAgICAvLyBCdXR0b24gdGhhdCBzaG93cyB0aGUgcGFzc3dvcmQgaW5wdXRzIGJsb2NrXG4gICAgdGhpcy5zaG93QnV0dG9uU2VsZWN0b3IgPSBzaG93QnV0dG9uU2VsZWN0b3I7XG5cbiAgICAvLyBCdXR0b24gdGhhdCBoaWRlcyB0aGUgcGFzc3dvcmQgaW5wdXRzIGJsb2NrXG4gICAgdGhpcy5oaWRlQnV0dG9uU2VsZWN0b3IgPSBoaWRlQnV0dG9uU2VsZWN0b3I7XG5cbiAgICAvLyBCdXR0b24gdGhhdCBnZW5lcmF0ZXMgYSByYW5kb20gcGFzc3dvcmRcbiAgICB0aGlzLmdlbmVyYXRlUGFzc3dvcmRCdXR0b25TZWxlY3RvciA9IGdlbmVyYXRlUGFzc3dvcmRCdXR0b25TZWxlY3RvcjtcblxuICAgIC8vIElucHV0IHRvIGVudGVyIG9sZCBwYXNzd29yZFxuICAgIHRoaXMub2xkUGFzc3dvcmRJbnB1dFNlbGVjdG9yID0gb2xkUGFzc3dvcmRJbnB1dFNlbGVjdG9yO1xuXG4gICAgLy8gSW5wdXQgdG8gZW50ZXIgbmV3IHBhc3N3b3JkXG4gICAgdGhpcy5uZXdQYXNzd29yZElucHV0U2VsZWN0b3IgPSBuZXdQYXNzd29yZElucHV0U2VsZWN0b3I7XG5cbiAgICAvLyBJbnB1dCB0byBjb25maXJtIHRoZSBuZXcgcGFzc3dvcmRcbiAgICB0aGlzLmNvbmZpcm1OZXdQYXNzd29yZElucHV0U2VsZWN0b3IgPSBjb25maXJtTmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yO1xuXG4gICAgLy8gSW5wdXQgdGhhdCBkaXNwbGF5cyBnZW5lcmF0ZWQgcmFuZG9tIHBhc3N3b3JkXG4gICAgdGhpcy5nZW5lcmF0ZWRQYXNzd29yZERpc3BsYXlTZWxlY3RvciA9IGdlbmVyYXRlZFBhc3N3b3JkRGlzcGxheVNlbGVjdG9yO1xuXG4gICAgLy8gTWFpbiBpbnB1dCBmb3IgcGFzc3dvcmQgZ2VuZXJhdGlvblxuICAgIHRoaXMuJG5ld1Bhc3N3b3JkSW5wdXRzID0gdGhpcy4kaW5wdXRzQmxvY2tcbiAgICAgIC5maW5kKHRoaXMubmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yKTtcblxuICAgIC8vIEdlbmVyYXRlZCBwYXNzd29yZCB3aWxsIGJlIGNvcGllZCB0byB0aGVzZSBpbnB1dHNcbiAgICB0aGlzLiRjb3B5UGFzc3dvcmRJbnB1dHMgPSB0aGlzLiRpbnB1dHNCbG9ja1xuICAgICAgLmZpbmQodGhpcy5jb25maXJtTmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yKVxuICAgICAgLmFkZCh0aGlzLmdlbmVyYXRlZFBhc3N3b3JkRGlzcGxheVNlbGVjdG9yKTtcblxuICAgIC8vIEFsbCBpbnB1dHMgaW4gdGhlIGNoYW5nZSBwYXNzd29yZCBibG9jaywgdGhhdCBhcmUgc3VibWl0dGFibGUgd2l0aCB0aGUgZm9ybS5cbiAgICB0aGlzLiRzdWJtaXR0YWJsZUlucHV0cyA9IHRoaXMuJGlucHV0c0Jsb2NrXG4gICAgICAuZmluZCh0aGlzLm9sZFBhc3N3b3JkSW5wdXRTZWxlY3RvcilcbiAgICAgIC5hZGQodGhpcy5uZXdQYXNzd29yZElucHV0U2VsZWN0b3IpXG4gICAgICAuYWRkKHRoaXMuY29uZmlybU5ld1Bhc3N3b3JkSW5wdXRTZWxlY3Rvcik7XG5cbiAgICB0aGlzLnBhc3N3b3JkSGFuZGxlciA9IG5ldyBDaGFuZ2VQYXNzd29yZEhhbmRsZXIoXG4gICAgICBwYXNzd29yZFN0cmVuZ3RoRmVlZGJhY2tDb250YWluZXJTZWxlY3RvclxuICAgICk7XG5cbiAgICB0aGlzLnBhc3N3b3JkVmFsaWRhdG9yID0gbmV3IFBhc3N3b3JkVmFsaWRhdG9yKFxuICAgICAgdGhpcy5uZXdQYXNzd29yZElucHV0U2VsZWN0b3IsXG4gICAgICB0aGlzLmNvbmZpcm1OZXdQYXNzd29yZElucHV0U2VsZWN0b3JcbiAgICApO1xuXG4gICAgdGhpcy5faGlkZUlucHV0c0Jsb2NrKCk7XG4gICAgdGhpcy5faW5pdEV2ZW50cygpO1xuXG4gICAgcmV0dXJuIHt9O1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRpYWxpemUgZXZlbnRzLlxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRFdmVudHMoKSB7XG4gICAgLy8gU2hvdyB0aGUgaW5wdXRzIGJsb2NrIHdoZW4gc2hvdyBidXR0b24gaXMgY2xpY2tlZFxuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMuc2hvd0J1dHRvblNlbGVjdG9yLCAoZSkgPT4ge1xuICAgICAgdGhpcy5faGlkZSgkKGUuY3VycmVudFRhcmdldCkpO1xuICAgICAgdGhpcy5fc2hvd0lucHV0c0Jsb2NrKCk7XG4gICAgfSk7XG5cbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLmhpZGVCdXR0b25TZWxlY3RvciwgKCkgPT4ge1xuICAgICAgdGhpcy5faGlkZUlucHV0c0Jsb2NrKCk7XG4gICAgICB0aGlzLl9zaG93KCQodGhpcy5zaG93QnV0dG9uU2VsZWN0b3IpKTtcbiAgICB9KTtcblxuICAgIC8vIFdhdGNoIGFuZCBkaXNwbGF5IGZlZWRiYWNrIGFib3V0IHBhc3N3b3JkJ3Mgc3RyZW5ndGhcbiAgICB0aGlzLnBhc3N3b3JkSGFuZGxlci53YXRjaFBhc3N3b3JkU3RyZW5ndGgodGhpcy4kbmV3UGFzc3dvcmRJbnB1dHMpO1xuXG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5nZW5lcmF0ZVBhc3N3b3JkQnV0dG9uU2VsZWN0b3IsICgpID0+IHtcbiAgICAgIC8vIEdlbmVyYXRlIHRoZSBwYXNzd29yZCBpbnRvIG1haW4gaW5wdXQuXG4gICAgICB0aGlzLnBhc3N3b3JkSGFuZGxlci5nZW5lcmF0ZVBhc3N3b3JkKHRoaXMuJG5ld1Bhc3N3b3JkSW5wdXRzKTtcblxuICAgICAgLy8gQ29weSB0aGUgZ2VuZXJhdGVkIHBhc3N3b3JkIGZyb20gbWFpbiBpbnB1dCB0byBhZGRpdGlvbmFsIGlucHV0c1xuICAgICAgdGhpcy4kY29weVBhc3N3b3JkSW5wdXRzLnZhbCh0aGlzLiRuZXdQYXNzd29yZElucHV0cy52YWwoKSk7XG4gICAgICB0aGlzLl9jaGVja1Bhc3N3b3JkVmFsaWRpdHkoKTtcbiAgICB9KTtcblxuICAgIC8vIFZhbGlkYXRlIG5ldyBwYXNzd29yZCBhbmQgaXQncyBjb25maXJtYXRpb24gd2hlbiBhbnkgb2YgdGhlIGlucHV0cyBpcyBjaGFuZ2VkXG4gICAgJChkb2N1bWVudCkub24oJ2tleXVwJywgYCR7dGhpcy5uZXdQYXNzd29yZElucHV0U2VsZWN0b3J9LCR7dGhpcy5jb25maXJtTmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yfWAsICgpID0+IHtcbiAgICAgIHRoaXMuX2NoZWNrUGFzc3dvcmRWYWxpZGl0eSgpO1xuICAgIH0pO1xuXG4gICAgLy8gUHJldmVudCBzdWJtaXR0aW5nIHRoZSBmb3JtIGlmIG5ldyBwYXNzd29yZCBpcyBub3QgdmFsaWRcbiAgICAkKGRvY3VtZW50KS5vbignc3VibWl0JywgJCh0aGlzLm9sZFBhc3N3b3JkSW5wdXRTZWxlY3RvcikuY2xvc2VzdCgnZm9ybScpLCAoZXZlbnQpID0+IHtcbiAgICAgIC8vIElmIHBhc3N3b3JkIGlucHV0IGlzIGRpc2FibGVkIC0gd2UgZG9uJ3QgbmVlZCB0byB2YWxpZGF0ZSBpdC5cbiAgICAgIGlmICgkKHRoaXMub2xkUGFzc3dvcmRJbnB1dFNlbGVjdG9yKS5pcygnOmRpc2FibGVkJykpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBpZiAoIXRoaXMucGFzc3dvcmRWYWxpZGF0b3IuaXNQYXNzd29yZFZhbGlkKCkpIHtcbiAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDaGVjayBpZiBwYXNzd29yZCBpcyB2YWxpZCwgc2hvdyBlcnJvciBtZXNzYWdlcyBpZiBpdCdzIG5vdC5cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9jaGVja1Bhc3N3b3JkVmFsaWRpdHkoKSB7XG4gICAgY29uc3QgJGZpcnN0UGFzc3dvcmRFcnJvckNvbnRhaW5lciA9ICQodGhpcy5uZXdQYXNzd29yZElucHV0U2VsZWN0b3IpLnBhcmVudCgpLmZpbmQoJy5mb3JtLXRleHQnKTtcbiAgICBjb25zdCAkc2Vjb25kUGFzc3dvcmRFcnJvckNvbnRhaW5lciA9ICQodGhpcy5jb25maXJtTmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yKS5wYXJlbnQoKS5maW5kKCcuZm9ybS10ZXh0Jyk7XG5cbiAgICAkZmlyc3RQYXNzd29yZEVycm9yQ29udGFpbmVyXG4gICAgICAudGV4dCh0aGlzLl9nZXRQYXNzd29yZExlbmd0aFZhbGlkYXRpb25NZXNzYWdlKCkpXG4gICAgICAudG9nZ2xlQ2xhc3MoJ3RleHQtZGFuZ2VyJywgIXRoaXMucGFzc3dvcmRWYWxpZGF0b3IuaXNQYXNzd29yZExlbmd0aFZhbGlkKCkpXG4gICAgO1xuXG4gICAgJHNlY29uZFBhc3N3b3JkRXJyb3JDb250YWluZXJcbiAgICAgIC50ZXh0KHRoaXMuX2dldFBhc3N3b3JkQ29uZmlybWF0aW9uVmFsaWRhdGlvbk1lc3NhZ2UoKSlcbiAgICAgIC50b2dnbGVDbGFzcygndGV4dC1kYW5nZXInLCAhdGhpcy5wYXNzd29yZFZhbGlkYXRvci5pc1Bhc3N3b3JkTWF0Y2hpbmdDb25maXJtYXRpb24oKSlcbiAgICA7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHBhc3N3b3JkIGNvbmZpcm1hdGlvbiB2YWxpZGF0aW9uIG1lc3NhZ2UuXG4gICAqXG4gICAqIEByZXR1cm5zIHtTdHJpbmd9XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZ2V0UGFzc3dvcmRDb25maXJtYXRpb25WYWxpZGF0aW9uTWVzc2FnZSgpIHtcbiAgICBpZiAoIXRoaXMucGFzc3dvcmRWYWxpZGF0b3IuaXNQYXNzd29yZE1hdGNoaW5nQ29uZmlybWF0aW9uKCkpIHtcbiAgICAgIHJldHVybiAkKHRoaXMuY29uZmlybU5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvcikuZGF0YSgnaW52YWxpZC1wYXNzd29yZCcpO1xuICAgIH1cblxuICAgIHJldHVybiAnJztcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgcGFzc3dvcmQgbGVuZ3RoIHZhbGlkYXRpb24gbWVzc2FnZS5cbiAgICpcbiAgICogQHJldHVybnMge1N0cmluZ31cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRQYXNzd29yZExlbmd0aFZhbGlkYXRpb25NZXNzYWdlKCkge1xuICAgIGlmICh0aGlzLnBhc3N3b3JkVmFsaWRhdG9yLmlzUGFzc3dvcmRUb29TaG9ydCgpKSB7XG4gICAgICByZXR1cm4gJCh0aGlzLm5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvcikuZGF0YSgncGFzc3dvcmQtdG9vLXNob3J0JylcbiAgICB9XG5cbiAgICBpZiAodGhpcy5wYXNzd29yZFZhbGlkYXRvci5pc1Bhc3N3b3JkVG9vTG9uZygpKSB7XG4gICAgICByZXR1cm4gJCh0aGlzLm5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvcikuZGF0YSgncGFzc3dvcmQtdG9vLWxvbmcnKTtcbiAgICB9XG5cbiAgICByZXR1cm4gJyc7XG4gIH1cblxuICAvKipcbiAgICogU2hvdyB0aGUgcGFzc3dvcmQgaW5wdXRzIGJsb2NrLlxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dJbnB1dHNCbG9jaygpIHtcbiAgICB0aGlzLl9zaG93KHRoaXMuJGlucHV0c0Jsb2NrKTtcbiAgICB0aGlzLiRzdWJtaXR0YWJsZUlucHV0cy5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICAgIHRoaXMuJHN1Ym1pdHRhYmxlSW5wdXRzLmF0dHIoJ3JlcXVpcmVkJywgJ3JlcXVpcmVkJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZSB0aGUgcGFzc3dvcmQgaW5wdXRzIGJsb2NrLlxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGVJbnB1dHNCbG9jaygpIHtcbiAgICB0aGlzLl9oaWRlKHRoaXMuJGlucHV0c0Jsb2NrKTtcbiAgICB0aGlzLiRzdWJtaXR0YWJsZUlucHV0cy5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuICAgIHRoaXMuJHN1Ym1pdHRhYmxlSW5wdXRzLnJlbW92ZUF0dHIoJ3JlcXVpcmVkJyk7XG4gICAgdGhpcy4kaW5wdXRzQmxvY2suZmluZCgnaW5wdXQnKS52YWwoJycpO1xuICAgIHRoaXMuJGlucHV0c0Jsb2NrLmZpbmQoJy5mb3JtLXRleHQnKS50ZXh0KCcnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlIGFuIGVsZW1lbnQuXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkZWxcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlKCRlbCkge1xuICAgICRlbC5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogU2hvdyBoaWRkZW4gZWxlbWVudC5cbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRlbFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3coJGVsKSB7XG4gICAgJGVsLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9mb3JtL2NoYW5nZS1wYXNzd29yZC1jb250cm9sLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBDbGFzcyByZXNwb25zaWJsZSBmb3IgY2hlY2tpbmcgcGFzc3dvcmQncyB2YWxpZGl0eS5cbiAqIENhbiB2YWxpZGF0ZSBlbnRlcmVkIHBhc3N3b3JkJ3MgbGVuZ3RoIGFnYWluc3QgbWluL21heCB2YWx1ZXMuXG4gKiBJZiBwYXNzd29yZCBjb25maXJtYXRpb24gaW5wdXQgaXMgcHJvdmlkZWQsIGNhbiB2YWxpZGF0ZSBpZiBlbnRlcmVkIHBhc3N3b3JkIGlzIG1hdGNoaW5nIGNvbmZpcm1hdGlvbi5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUGFzc3dvcmRWYWxpZGF0b3Ige1xuXG4gIC8qKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gcGFzc3dvcmRJbnB1dFNlbGVjdG9yIHNlbGVjdG9yIG9mIHRoZSBwYXNzd29yZCBpbnB1dC5cbiAgICogQHBhcmFtIHtTdHJpbmd8bnVsbH0gY29uZmlybVBhc3N3b3JkSW5wdXRTZWxlY3RvciAob3B0aW9uYWwpIHNlbGVjdG9yIGZvciB0aGUgcGFzc3dvcmQgY29uZmlybWF0aW9uIGlucHV0LlxuICAgKiBAcGFyYW0ge09iamVjdH0gb3B0aW9ucyBhbGxvd3Mgb3ZlcnJpZGluZyBkZWZhdWx0IG9wdGlvbnMuXG4gICAqL1xuICBjb25zdHJ1Y3RvcihwYXNzd29yZElucHV0U2VsZWN0b3IsIGNvbmZpcm1QYXNzd29yZElucHV0U2VsZWN0b3IgPSBudWxsLCBvcHRpb25zID0ge30pIHtcbiAgICB0aGlzLm5ld1Bhc3N3b3JkSW5wdXQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKHBhc3N3b3JkSW5wdXRTZWxlY3Rvcik7XG4gICAgdGhpcy5jb25maXJtUGFzc3dvcmRJbnB1dCA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoY29uZmlybVBhc3N3b3JkSW5wdXRTZWxlY3Rvcik7XG5cbiAgICAvLyBNaW5pbXVtIGFsbG93ZWQgbGVuZ3RoIGZvciBlbnRlcmVkIHBhc3N3b3JkXG4gICAgdGhpcy5taW5QYXNzd29yZExlbmd0aCA9IG9wdGlvbnMubWluUGFzc3dvcmRMZW5ndGggfHwgODtcblxuICAgIC8vIE1heGltdW0gYWxsb3dlZCBsZW5ndGggZm9yIGVudGVyZWQgcGFzc3dvcmRcbiAgICB0aGlzLm1heFBhc3N3b3JkTGVuZ3RoID0gb3B0aW9ucy5tYXhQYXNzd29yZExlbmd0aCB8fCAyNTU7XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2sgaWYgdGhlIHBhc3N3b3JkIGlzIHZhbGlkLlxuICAgKlxuICAgKiBAcmV0dXJucyB7Ym9vbGVhbn1cbiAgICovXG4gIGlzUGFzc3dvcmRWYWxpZCgpIHtcbiAgICBpZiAodGhpcy5jb25maXJtUGFzc3dvcmRJbnB1dCAmJiAhdGhpcy5pc1Bhc3N3b3JkTWF0Y2hpbmdDb25maXJtYXRpb24oKSkge1xuICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cblxuICAgIHJldHVybiB0aGlzLmlzUGFzc3dvcmRMZW5ndGhWYWxpZCgpO1xuICB9XG5cbiAgLyoqXG4gICAqIENoZWNrIGlmIHBhc3N3b3JkJ3MgbGVuZ3RoIGlzIHZhbGlkLlxuICAgKlxuICAgKiBAcmV0dXJucyB7Ym9vbGVhbn1cbiAgICovXG4gIGlzUGFzc3dvcmRMZW5ndGhWYWxpZCgpIHtcbiAgICByZXR1cm4gIXRoaXMuaXNQYXNzd29yZFRvb1Nob3J0KCkgJiYgIXRoaXMuaXNQYXNzd29yZFRvb0xvbmcoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDaGVjayBpZiBwYXNzd29yZCBpcyBtYXRjaGluZyBpdCdzIGNvbmZpcm1hdGlvbi5cbiAgICpcbiAgICogQHJldHVybnMge2Jvb2xlYW59XG4gICAqL1xuICBpc1Bhc3N3b3JkTWF0Y2hpbmdDb25maXJtYXRpb24oKSB7XG4gICAgaWYgKCF0aGlzLmNvbmZpcm1QYXNzd29yZElucHV0KSB7XG4gICAgICB0aHJvdyAnQ29uZmlybSBwYXNzd29yZCBpbnB1dCBpcyBub3QgcHJvdmlkZWQgZm9yIHRoZSBwYXNzd29yZCB2YWxpZGF0b3IuJztcbiAgICB9XG5cbiAgICBpZiAodGhpcy5jb25maXJtUGFzc3dvcmRJbnB1dC52YWx1ZSA9PT0gJycpIHtcbiAgICAgIHJldHVybiB0cnVlO1xuICAgIH1cblxuICAgIHJldHVybiB0aGlzLm5ld1Bhc3N3b3JkSW5wdXQudmFsdWUgPT09IHRoaXMuY29uZmlybVBhc3N3b3JkSW5wdXQudmFsdWU7XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2sgaWYgcGFzc3dvcmQgaXMgdG9vIHNob3J0LlxuICAgKlxuICAgKiBAcmV0dXJucyB7Ym9vbGVhbn1cbiAgICovXG4gIGlzUGFzc3dvcmRUb29TaG9ydCgpIHtcbiAgICByZXR1cm4gdGhpcy5uZXdQYXNzd29yZElucHV0LnZhbHVlLmxlbmd0aCA8IHRoaXMubWluUGFzc3dvcmRMZW5ndGg7XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2sgaWYgcGFzc3dvcmQgaXMgdG9vIGxvbmcuXG4gICAqXG4gICAqIEByZXR1cm5zIHtib29sZWFufVxuICAgKi9cbiAgaXNQYXNzd29yZFRvb0xvbmcoKSB7XG4gICAgcmV0dXJuIHRoaXMubmV3UGFzc3dvcmRJbnB1dC52YWx1ZS5sZW5ndGggPiB0aGlzLm1heFBhc3N3b3JkTGVuZ3RoO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL3Bhc3N3b3JkLXZhbGlkYXRvci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbi8qKlxuICogRGVmaW5lcyBhbGwgc2VsZWN0b3JzIHRoYXQgYXJlIHVzZWQgaW4gZW1wbG95ZWUgYWRkL2VkaXQgZm9ybS5cbiAqL1xuZXhwb3J0IGRlZmF1bHQge1xuICBzaG9wQ2hvaWNlVHJlZTogJyNlbXBsb3llZV9zaG9wX2Fzc29jaWF0aW9uJyxcbiAgcHJvZmlsZVNlbGVjdDogJyNlbXBsb3llZV9wcm9maWxlJyxcbiAgZGVmYXVsdFBhZ2VTZWxlY3Q6ICcjZW1wbG95ZWVfZGVmYXVsdF9wYWdlJyxcbiAgYWRkb25zQ29ubmVjdEZvcm06ICcjYWRkb25zLWNvbm5lY3QtZm9ybScsXG4gIGFkZG9uc0xvZ2luQnV0dG9uOiAnI2FkZG9uc19sb2dpbl9idG4nLFxuXG4gIC8vIHNlbGVjdG9ycyByZWxhdGVkIHRvIFwiY2hhbmdlIHBhc3N3b3JkXCIgZm9ybSBjb250cm9sXG4gIGNoYW5nZVBhc3N3b3JkSW5wdXRzQmxvY2s6ICcuanMtY2hhbmdlLXBhc3N3b3JkLWJsb2NrJyxcbiAgc2hvd0NoYW5nZVBhc3N3b3JkQmxvY2tCdXR0b246ICcuanMtY2hhbmdlLXBhc3N3b3JkJyxcbiAgaGlkZUNoYW5nZVBhc3N3b3JkQmxvY2tCdXR0b246ICcuanMtY2hhbmdlLXBhc3N3b3JkLWNhbmNlbCcsXG4gIGdlbmVyYXRlUGFzc3dvcmRCdXR0b246ICcjZW1wbG95ZWVfY2hhbmdlX3Bhc3N3b3JkX2dlbmVyYXRlX3Bhc3N3b3JkX2J1dHRvbicsXG4gIG9sZFBhc3N3b3JkSW5wdXQ6ICcjZW1wbG95ZWVfY2hhbmdlX3Bhc3N3b3JkX29sZF9wYXNzd29yZCcsXG4gIG5ld1Bhc3N3b3JkSW5wdXQ6ICcjZW1wbG95ZWVfY2hhbmdlX3Bhc3N3b3JkX25ld19wYXNzd29yZF9maXJzdCcsXG4gIGNvbmZpcm1OZXdQYXNzd29yZElucHV0OiAnI2VtcGxveWVlX2NoYW5nZV9wYXNzd29yZF9uZXdfcGFzc3dvcmRfc2Vjb25kJyxcbiAgZ2VuZXJhdGVkUGFzc3dvcmREaXNwbGF5SW5wdXQ6ICcjZW1wbG95ZWVfY2hhbmdlX3Bhc3N3b3JkX2dlbmVyYXRlZF9wYXNzd29yZCcsXG4gIHBhc3N3b3JkU3RyZW5ndGhGZWVkYmFja0NvbnRhaW5lcjogJy5qcy1wYXNzd29yZC1zdHJlbmd0aC1mZWVkYmFjaycsXG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9lbXBsb3llZS9lbXBsb3llZS1mb3JtLW1hcC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBFbXBsb3llZUZvcm0gZnJvbSBcIi4vRW1wbG95ZWVGb3JtXCI7XG5cbiQoKCkgPT4ge1xuICBuZXcgRW1wbG95ZWVGb3JtKCk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2VtcGxveWVlL2Zvcm0uanMiXSwic291cmNlUm9vdCI6IiJ9