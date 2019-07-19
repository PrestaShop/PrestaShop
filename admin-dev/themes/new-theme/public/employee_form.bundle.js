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
/******/ 	return __webpack_require__(__webpack_require__.s = 324);
/******/ })
/************************************************************************/
/******/ ({

/***/ 19:
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

/***/ 256:
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

var _choiceTree = __webpack_require__(19);

var _choiceTree2 = _interopRequireDefault(_choiceTree);

var _addonsConnector = __webpack_require__(305);

var _addonsConnector2 = _interopRequireDefault(_addonsConnector);

var _changePasswordControl = __webpack_require__(307);

var _changePasswordControl2 = _interopRequireDefault(_changePasswordControl);

var _employeeFormMap = __webpack_require__(323);

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
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(7)))

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

/***/ 323:
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

/***/ 324:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

var _EmployeeForm = __webpack_require__(256);

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
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(7)))

/***/ }),

/***/ 7:
/***/ (function(module, exports) {

(function() { module.exports = window["jQuery"]; }());

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODIiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9mb3JtL2Nob2ljZS10cmVlLmpzPzU0MWEqKioqKioqIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2VtcGxveWVlL0VtcGxveWVlRm9ybS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2FkZG9ucy1jb25uZWN0b3IuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9jaGFuZ2UtcGFzc3dvcmQtaGFuZGxlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2Zvcm0vY2hhbmdlLXBhc3N3b3JkLWNvbnRyb2wuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9wYXNzd29yZC12YWxpZGF0b3IuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvZW1wbG95ZWUvZW1wbG95ZWUtZm9ybS1tYXAuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvZW1wbG95ZWUvZm9ybS5qcyIsIndlYnBhY2s6Ly8vZXh0ZXJuYWwgXCJqUXVlcnlcIj8wY2I4KioqKioiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIkNob2ljZVRyZWUiLCJ0cmVlU2VsZWN0b3IiLCIkY29udGFpbmVyIiwib24iLCJldmVudCIsIiRpbnB1dFdyYXBwZXIiLCJjdXJyZW50VGFyZ2V0IiwiX3RvZ2dsZUNoaWxkVHJlZSIsIiRhY3Rpb24iLCJfdG9nZ2xlVHJlZSIsImVuYWJsZUF1dG9DaGVja0NoaWxkcmVuIiwiZW5hYmxlQWxsSW5wdXRzIiwiZGlzYWJsZUFsbElucHV0cyIsIiRjbGlja2VkQ2hlY2tib3giLCIkaXRlbVdpdGhDaGlsZHJlbiIsImNsb3Nlc3QiLCJmaW5kIiwicHJvcCIsImlzIiwicmVtb3ZlQXR0ciIsImF0dHIiLCIkcGFyZW50V3JhcHBlciIsImhhc0NsYXNzIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsIiRwYXJlbnRDb250YWluZXIiLCJhY3Rpb24iLCJkYXRhIiwiY29uZmlnIiwiZXhwYW5kIiwiY29sbGFwc2UiLCJuZXh0QWN0aW9uIiwidGV4dCIsImljb24iLCJlYWNoIiwiaW5kZXgiLCJpdGVtIiwiJGl0ZW0iLCJFbXBsb3llZUZvcm0iLCJzaG9wQ2hvaWNlVHJlZVNlbGVjdG9yIiwiZW1wbG95ZWVGb3JtTWFwIiwic2hvcENob2ljZVRyZWUiLCJlbXBsb3llZVByb2ZpbGVTZWxlY3RvciIsInByb2ZpbGVTZWxlY3QiLCJ0YWJzRHJvcGRvd25TZWxlY3RvciIsImRlZmF1bHRQYWdlU2VsZWN0IiwiQWRkb25zQ29ubmVjdG9yIiwiYWRkb25zQ29ubmVjdEZvcm0iLCJhZGRvbnNMb2dpbkJ1dHRvbiIsIkNoYW5nZVBhc3N3b3JkQ29udHJvbCIsImNoYW5nZVBhc3N3b3JkSW5wdXRzQmxvY2siLCJzaG93Q2hhbmdlUGFzc3dvcmRCbG9ja0J1dHRvbiIsImhpZGVDaGFuZ2VQYXNzd29yZEJsb2NrQnV0dG9uIiwiZ2VuZXJhdGVQYXNzd29yZEJ1dHRvbiIsIm9sZFBhc3N3b3JkSW5wdXQiLCJuZXdQYXNzd29yZElucHV0IiwiY29uZmlybU5ld1Bhc3N3b3JkSW5wdXQiLCJnZW5lcmF0ZWRQYXNzd29yZERpc3BsYXlJbnB1dCIsInBhc3N3b3JkU3RyZW5ndGhGZWVkYmFja0NvbnRhaW5lciIsIl9pbml0RXZlbnRzIiwiX3RvZ2dsZVNob3BUcmVlIiwiJGVtcGxveWVlUHJvZmlsZXNEcm9wZG93biIsImdldFRhYnNVcmwiLCJkb2N1bWVudCIsImdldCIsInByb2ZpbGVJZCIsInZhbCIsInRhYnMiLCJfcmVsb2FkVGFic0Ryb3Bkb3duIiwiYWNjZXNzaWJsZVRhYnMiLCIkdGFic0Ryb3Bkb3duIiwiZW1wdHkiLCJrZXkiLCJsZW5ndGgiLCIkb3B0Z3JvdXAiLCJfY3JlYXRlT3B0aW9uR3JvdXAiLCJjaGlsZEtleSIsImFwcGVuZCIsIl9jcmVhdGVPcHRpb24iLCIkZW1wbG95ZWVQcm9maWxlRHJvcGRvd24iLCJzdXBlckFkbWluUHJvZmlsZUlkIiwidG9nZ2xlQ2xhc3MiLCJuYW1lIiwidmFsdWUiLCJhZGRvbnNDb25uZWN0Rm9ybVNlbGVjdG9yIiwibG9hZGluZ1NwaW5uZXJTZWxlY3RvciIsIiRsb2FkaW5nU3Bpbm5lciIsIiRmb3JtIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJfY29ubmVjdCIsInNlcmlhbGl6ZSIsImFkZG9uc0Nvbm5lY3RVcmwiLCJmb3JtRGF0YSIsImFqYXgiLCJtZXRob2QiLCJ1cmwiLCJkYXRhVHlwZSIsImJlZm9yZVNlbmQiLCJzaG93IiwiaGlkZSIsInRoZW4iLCJyZXNwb25zZSIsInN1Y2Nlc3MiLCJsb2NhdGlvbiIsInJlbG9hZCIsImdyb3dsIiwiZXJyb3IiLCJtZXNzYWdlIiwiZmFkZUluIiwiQ2hhbmdlUGFzc3dvcmRIYW5kbGVyIiwicGFzc3dvcmRTdHJlbmd0aEZlZWRiYWNrQ29udGFpbmVyU2VsZWN0b3IiLCJvcHRpb25zIiwibWluTGVuZ3RoIiwiJGZlZWRiYWNrQ29udGFpbmVyIiwid2F0Y2hQYXNzd29yZFN0cmVuZ3RoIiwiJGlucHV0IiwiZ2VuZXJhdGVQYXNzd29yZCIsInBhc3N5IiwicmVxdWlyZW1lbnRzIiwibWluIiwiY2hhcmFjdGVycyIsImVsZW1lbnQiLCIkb3V0cHV0Q29udGFpbmVyIiwiaW5zZXJ0QWZ0ZXIiLCJzdHJlbmd0aCIsInZhbGlkIiwiX2Rpc3BsYXlGZWVkYmFjayIsInBhc3N3b3JkU3RyZW5ndGgiLCJpc1Bhc3N3b3JkVmFsaWQiLCJmZWVkYmFjayIsIl9nZXRQYXNzd29yZFN0cmVuZ3RoRmVlZGJhY2siLCJlbGVtZW50Q2xhc3MiLCJMT1ciLCJNRURJVU0iLCJISUdIIiwiRVhUUkVNRSIsImlucHV0c0Jsb2NrU2VsZWN0b3IiLCJzaG93QnV0dG9uU2VsZWN0b3IiLCJoaWRlQnV0dG9uU2VsZWN0b3IiLCJnZW5lcmF0ZVBhc3N3b3JkQnV0dG9uU2VsZWN0b3IiLCJvbGRQYXNzd29yZElucHV0U2VsZWN0b3IiLCJuZXdQYXNzd29yZElucHV0U2VsZWN0b3IiLCJjb25maXJtTmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yIiwiZ2VuZXJhdGVkUGFzc3dvcmREaXNwbGF5U2VsZWN0b3IiLCIkaW5wdXRzQmxvY2siLCIkbmV3UGFzc3dvcmRJbnB1dHMiLCIkY29weVBhc3N3b3JkSW5wdXRzIiwiYWRkIiwiJHN1Ym1pdHRhYmxlSW5wdXRzIiwicGFzc3dvcmRIYW5kbGVyIiwicGFzc3dvcmRWYWxpZGF0b3IiLCJQYXNzd29yZFZhbGlkYXRvciIsIl9oaWRlSW5wdXRzQmxvY2siLCJlIiwiX2hpZGUiLCJfc2hvd0lucHV0c0Jsb2NrIiwiX3Nob3ciLCJfY2hlY2tQYXNzd29yZFZhbGlkaXR5IiwiJGZpcnN0UGFzc3dvcmRFcnJvckNvbnRhaW5lciIsInBhcmVudCIsIiRzZWNvbmRQYXNzd29yZEVycm9yQ29udGFpbmVyIiwiX2dldFBhc3N3b3JkTGVuZ3RoVmFsaWRhdGlvbk1lc3NhZ2UiLCJpc1Bhc3N3b3JkTGVuZ3RoVmFsaWQiLCJfZ2V0UGFzc3dvcmRDb25maXJtYXRpb25WYWxpZGF0aW9uTWVzc2FnZSIsImlzUGFzc3dvcmRNYXRjaGluZ0NvbmZpcm1hdGlvbiIsImlzUGFzc3dvcmRUb29TaG9ydCIsImlzUGFzc3dvcmRUb29Mb25nIiwiJGVsIiwicGFzc3dvcmRJbnB1dFNlbGVjdG9yIiwiY29uZmlybVBhc3N3b3JkSW5wdXRTZWxlY3RvciIsInF1ZXJ5U2VsZWN0b3IiLCJjb25maXJtUGFzc3dvcmRJbnB1dCIsIm1pblBhc3N3b3JkTGVuZ3RoIiwibWF4UGFzc3dvcmRMZW5ndGgiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFD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QUE0QztBQUMxQyxlQUFPLElBQVA7QUFDRDs7QUFFRCxhQUFPLEtBQUs1QixnQkFBTCxDQUFzQjRCLEtBQXRCLEtBQWdDLEtBQUt5RixvQkFBTCxDQUEwQnpGLEtBQWpFO0FBQ0Q7O0FBRUQ7Ozs7Ozs7O3lDQUtxQjtBQUNuQixhQUFPLEtBQUs1QixnQkFBTCxDQUFzQjRCLEtBQXRCLENBQTRCVixNQUE1QixHQUFxQyxLQUFLb0csaUJBQWpEO0FBQ0Q7O0FBRUQ7Ozs7Ozs7O3dDQUtvQjtBQUNsQixhQUFPLEtBQUt0SCxnQkFBTCxDQUFzQjRCLEtBQXRCLENBQTRCVixNQUE1QixHQUFxQyxLQUFLcUcsaUJBQWpEO0FBQ0Q7Ozs7OztrQkF6RWtCdEIsaUI7Ozs7Ozs7Ozs7Ozs7QUM5QnJCOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBOzs7a0JBR2U7QUFDYi9HLGtCQUFnQiw0QkFESDtBQUViRSxpQkFBZSxtQkFGRjtBQUdiRSxxQkFBbUIsd0JBSE47QUFJYkUscUJBQW1CLHNCQUpOO0FBS2JDLHFCQUFtQixtQkFMTjs7QUFPYjtBQUNBRSw2QkFBMkIsMkJBUmQ7QUFTYkMsaUNBQStCLHFCQVRsQjtBQVViQyxpQ0FBK0IsNEJBVmxCO0FBV2JDLDBCQUF3QixvREFYWDtBQVliQyxvQkFBa0Isd0NBWkw7QUFhYkMsb0JBQWtCLDhDQWJMO0FBY2JDLDJCQUF5QiwrQ0FkWjtBQWViQyxpQ0FBK0IsOENBZmxCO0FBZ0JiQyxxQ0FBbUM7QUFoQnRCLEM7Ozs7Ozs7Ozs7QUNIZjs7Ozs7O0FBRUE1RCxFQUFFLFlBQU07QUFDTixNQUFJd0Msc0JBQUo7QUFDRCxDQUZELEUsQ0EzQkE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNBQSxhQUFhLG1DQUFtQyxFQUFFLEkiLCJmaWxlIjoiZW1wbG95ZWVfZm9ybS5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDMyNCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODIiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogSGFuZGxlcyBVSSBpbnRlcmFjdGlvbnMgb2YgY2hvaWNlIHRyZWVcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ2hvaWNlVHJlZSB7XG4gIC8qKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gdHJlZVNlbGVjdG9yXG4gICAqL1xuICBjb25zdHJ1Y3Rvcih0cmVlU2VsZWN0b3IpIHtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKHRyZWVTZWxlY3Rvcik7XG5cbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgJy5qcy1pbnB1dC13cmFwcGVyJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkaW5wdXRXcmFwcGVyID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcblxuICAgICAgdGhpcy5fdG9nZ2xlQ2hpbGRUcmVlKCRpbnB1dFdyYXBwZXIpO1xuICAgIH0pO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsICcuanMtdG9nZ2xlLWNob2ljZS10cmVlLWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGFjdGlvbiA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG5cbiAgICAgIHRoaXMuX3RvZ2dsZVRyZWUoJGFjdGlvbik7XG4gICAgfSk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW46ICgpID0+IHRoaXMuZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4oKSxcbiAgICAgIGVuYWJsZUFsbElucHV0czogKCkgPT4gdGhpcy5lbmFibGVBbGxJbnB1dHMoKSxcbiAgICAgIGRpc2FibGVBbGxJbnB1dHM6ICgpID0+IHRoaXMuZGlzYWJsZUFsbElucHV0cygpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogRW5hYmxlIGF1dG9tYXRpYyBjaGVjay91bmNoZWNrIG9mIGNsaWNrZWQgaXRlbSdzIGNoaWxkcmVuLlxuICAgKi9cbiAgZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4oKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjaGFuZ2UnLCAnaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkY2xpY2tlZENoZWNrYm94ID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0ICRpdGVtV2l0aENoaWxkcmVuID0gJGNsaWNrZWRDaGVja2JveC5jbG9zZXN0KCdsaScpO1xuXG4gICAgICAkaXRlbVdpdGhDaGlsZHJlblxuICAgICAgICAuZmluZCgndWwgaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJylcbiAgICAgICAgLnByb3AoJ2NoZWNrZWQnLCAkY2xpY2tlZENoZWNrYm94LmlzKCc6Y2hlY2tlZCcpKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbmFibGUgYWxsIGlucHV0cyBpbiB0aGUgY2hvaWNlIHRyZWUuXG4gICAqL1xuICBlbmFibGVBbGxJbnB1dHMoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLmZpbmQoJ2lucHV0JykucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNhYmxlIGFsbCBpbnB1dHMgaW4gdGhlIGNob2ljZSB0cmVlLlxuICAgKi9cbiAgZGlzYWJsZUFsbElucHV0cygpIHtcbiAgICB0aGlzLiRjb250YWluZXIuZmluZCgnaW5wdXQnKS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIENvbGxhcHNlIG9yIGV4cGFuZCBzdWItdHJlZSBmb3Igc2luZ2xlIHBhcmVudFxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGlucHV0V3JhcHBlclxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3RvZ2dsZUNoaWxkVHJlZSgkaW5wdXRXcmFwcGVyKSB7XG4gICAgY29uc3QgJHBhcmVudFdyYXBwZXIgPSAkaW5wdXRXcmFwcGVyLmNsb3Nlc3QoJ2xpJyk7XG5cbiAgICBpZiAoJHBhcmVudFdyYXBwZXIuaGFzQ2xhc3MoJ2V4cGFuZGVkJykpIHtcbiAgICAgICRwYXJlbnRXcmFwcGVyXG4gICAgICAgIC5yZW1vdmVDbGFzcygnZXhwYW5kZWQnKVxuICAgICAgICAuYWRkQ2xhc3MoJ2NvbGxhcHNlZCcpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgaWYgKCRwYXJlbnRXcmFwcGVyLmhhc0NsYXNzKCdjb2xsYXBzZWQnKSkge1xuICAgICAgJHBhcmVudFdyYXBwZXJcbiAgICAgICAgLnJlbW92ZUNsYXNzKCdjb2xsYXBzZWQnKVxuICAgICAgICAuYWRkQ2xhc3MoJ2V4cGFuZGVkJyk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIENvbGxhcHNlIG9yIGV4cGFuZCB3aG9sZSB0cmVlXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkYWN0aW9uXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdG9nZ2xlVHJlZSgkYWN0aW9uKSB7XG4gICAgY29uc3QgJHBhcmVudENvbnRhaW5lciA9ICRhY3Rpb24uY2xvc2VzdCgnLmpzLWNob2ljZS10cmVlLWNvbnRhaW5lcicpO1xuICAgIGNvbnN0IGFjdGlvbiA9ICRhY3Rpb24uZGF0YSgnYWN0aW9uJyk7XG5cbiAgICAvLyB0b2dnbGUgYWN0aW9uIGNvbmZpZ3VyYXRpb25cbiAgICBjb25zdCBjb25maWcgPSB7XG4gICAgICBhZGRDbGFzczoge1xuICAgICAgICBleHBhbmQ6ICdleHBhbmRlZCcsXG4gICAgICAgIGNvbGxhcHNlOiAnY29sbGFwc2VkJyxcbiAgICAgIH0sXG4gICAgICByZW1vdmVDbGFzczoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZWQnLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZGVkJyxcbiAgICAgIH0sXG4gICAgICBuZXh0QWN0aW9uOiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlJyxcbiAgICAgICAgY29sbGFwc2U6ICdleHBhbmQnLFxuICAgICAgfSxcbiAgICAgIHRleHQ6IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2VkLXRleHQnLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZGVkLXRleHQnLFxuICAgICAgfSxcbiAgICAgIGljb246IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2VkLWljb24nLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZGVkLWljb24nLFxuICAgICAgfVxuICAgIH07XG5cbiAgICAkcGFyZW50Q29udGFpbmVyLmZpbmQoJ2xpJykuZWFjaCgoaW5kZXgsIGl0ZW0pID0+IHtcbiAgICAgIGNvbnN0ICRpdGVtID0gJChpdGVtKTtcblxuICAgICAgaWYgKCRpdGVtLmhhc0NsYXNzKGNvbmZpZy5yZW1vdmVDbGFzc1thY3Rpb25dKSkge1xuICAgICAgICAgICRpdGVtLnJlbW92ZUNsYXNzKGNvbmZpZy5yZW1vdmVDbGFzc1thY3Rpb25dKVxuICAgICAgICAgICAgLmFkZENsYXNzKGNvbmZpZy5hZGRDbGFzc1thY3Rpb25dKTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgICRhY3Rpb24uZGF0YSgnYWN0aW9uJywgY29uZmlnLm5leHRBY3Rpb25bYWN0aW9uXSk7XG4gICAgJGFjdGlvbi5maW5kKCcubWF0ZXJpYWwtaWNvbnMnKS50ZXh0KCRhY3Rpb24uZGF0YShjb25maWcuaWNvblthY3Rpb25dKSk7XG4gICAgJGFjdGlvbi5maW5kKCcuanMtdG9nZ2xlLXRleHQnKS50ZXh0KCRhY3Rpb24uZGF0YShjb25maWcudGV4dFthY3Rpb25dKSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZm9ybS9jaG9pY2UtdHJlZS5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBDaG9pY2VUcmVlIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2Zvcm0vY2hvaWNlLXRyZWVcIjtcbmltcG9ydCBBZGRvbnNDb25uZWN0b3IgZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvYWRkb25zLWNvbm5lY3RvclwiO1xuaW1wb3J0IENoYW5nZVBhc3N3b3JkQ29udHJvbCBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9mb3JtL2NoYW5nZS1wYXNzd29yZC1jb250cm9sXCI7XG5pbXBvcnQgZW1wbG95ZWVGb3JtTWFwIGZyb20gXCIuL2VtcGxveWVlLWZvcm0tbWFwXCI7XG5cbi8qKlxuICogQ2xhc3MgcmVzcG9uc2libGUgZm9yIGphdmFzY3JpcHQgYWN0aW9ucyBpbiBlbXBsb3llZSBhZGQvZWRpdCBwYWdlLlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBFbXBsb3llZUZvcm0ge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLnNob3BDaG9pY2VUcmVlU2VsZWN0b3IgPSBlbXBsb3llZUZvcm1NYXAuc2hvcENob2ljZVRyZWU7XG4gICAgdGhpcy5zaG9wQ2hvaWNlVHJlZSA9IG5ldyBDaG9pY2VUcmVlKHRoaXMuc2hvcENob2ljZVRyZWVTZWxlY3Rvcik7XG4gICAgdGhpcy5lbXBsb3llZVByb2ZpbGVTZWxlY3RvciA9IGVtcGxveWVlRm9ybU1hcC5wcm9maWxlU2VsZWN0O1xuICAgIHRoaXMudGFic0Ryb3Bkb3duU2VsZWN0b3IgPSBlbXBsb3llZUZvcm1NYXAuZGVmYXVsdFBhZ2VTZWxlY3Q7XG5cbiAgICB0aGlzLnNob3BDaG9pY2VUcmVlLmVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCk7XG5cbiAgICBuZXcgQWRkb25zQ29ubmVjdG9yKFxuICAgICAgZW1wbG95ZWVGb3JtTWFwLmFkZG9uc0Nvbm5lY3RGb3JtLFxuICAgICAgZW1wbG95ZWVGb3JtTWFwLmFkZG9uc0xvZ2luQnV0dG9uXG4gICAgKTtcblxuICAgIG5ldyBDaGFuZ2VQYXNzd29yZENvbnRyb2woXG4gICAgICBlbXBsb3llZUZvcm1NYXAuY2hhbmdlUGFzc3dvcmRJbnB1dHNCbG9jayxcbiAgICAgIGVtcGxveWVlRm9ybU1hcC5zaG93Q2hhbmdlUGFzc3dvcmRCbG9ja0J1dHRvbixcbiAgICAgIGVtcGxveWVlRm9ybU1hcC5oaWRlQ2hhbmdlUGFzc3dvcmRCbG9ja0J1dHRvbixcbiAgICAgIGVtcGxveWVlRm9ybU1hcC5nZW5lcmF0ZVBhc3N3b3JkQnV0dG9uLFxuICAgICAgZW1wbG95ZWVGb3JtTWFwLm9sZFBhc3N3b3JkSW5wdXQsXG4gICAgICBlbXBsb3llZUZvcm1NYXAubmV3UGFzc3dvcmRJbnB1dCxcbiAgICAgIGVtcGxveWVlRm9ybU1hcC5jb25maXJtTmV3UGFzc3dvcmRJbnB1dCxcbiAgICAgIGVtcGxveWVlRm9ybU1hcC5nZW5lcmF0ZWRQYXNzd29yZERpc3BsYXlJbnB1dCxcbiAgICAgIGVtcGxveWVlRm9ybU1hcC5wYXNzd29yZFN0cmVuZ3RoRmVlZGJhY2tDb250YWluZXJcbiAgICApO1xuXG4gICAgdGhpcy5faW5pdEV2ZW50cygpO1xuICAgIHRoaXMuX3RvZ2dsZVNob3BUcmVlKCk7XG5cbiAgICByZXR1cm4ge307XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhbGl6ZSBwYWdlJ3MgZXZlbnRzLlxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRFdmVudHMoKSB7XG4gICAgY29uc3QgJGVtcGxveWVlUHJvZmlsZXNEcm9wZG93biA9ICQodGhpcy5lbXBsb3llZVByb2ZpbGVTZWxlY3Rvcik7XG4gICAgY29uc3QgZ2V0VGFic1VybCA9ICRlbXBsb3llZVByb2ZpbGVzRHJvcGRvd24uZGF0YSgnZ2V0LXRhYnMtdXJsJyk7XG5cbiAgICAkKGRvY3VtZW50KS5vbignY2hhbmdlJywgdGhpcy5lbXBsb3llZVByb2ZpbGVTZWxlY3RvciwgKCkgPT4gdGhpcy5fdG9nZ2xlU2hvcFRyZWUoKSk7XG5cbiAgICAvLyBSZWxvYWQgdGFicyBkcm9wZG93biB3aGVuIGVtcGxveWVlIHByb2ZpbGUgaXMgY2hhbmdlZC5cbiAgICAkKGRvY3VtZW50KS5vbignY2hhbmdlJywgdGhpcy5lbXBsb3llZVByb2ZpbGVTZWxlY3RvciwgKGV2ZW50KSA9PiB7XG4gICAgICAkLmdldChcbiAgICAgICAgZ2V0VGFic1VybCxcbiAgICAgICAge1xuICAgICAgICAgIHByb2ZpbGVJZDogJChldmVudC5jdXJyZW50VGFyZ2V0KS52YWwoKVxuICAgICAgICB9LFxuICAgICAgICAodGFicykgPT4ge1xuICAgICAgICAgIHRoaXMuX3JlbG9hZFRhYnNEcm9wZG93bih0YWJzKTtcbiAgICAgICAgfSxcbiAgICAgICAgJ2pzb24nXG4gICAgICApO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbG9hZCB0YWJzIGRyb3Bkb3duIHdpdGggbmV3IGNvbnRlbnQuXG4gICAqXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBhY2Nlc3NpYmxlVGFic1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbG9hZFRhYnNEcm9wZG93bihhY2Nlc3NpYmxlVGFicykge1xuICAgIGNvbnN0ICR0YWJzRHJvcGRvd24gPSAkKHRoaXMudGFic0Ryb3Bkb3duU2VsZWN0b3IpO1xuXG4gICAgJHRhYnNEcm9wZG93bi5lbXB0eSgpO1xuXG4gICAgZm9yIChsZXQga2V5IGluIGFjY2Vzc2libGVUYWJzKSB7XG4gICAgICBpZiAoYWNjZXNzaWJsZVRhYnNba2V5XVsnY2hpbGRyZW4nXS5sZW5ndGggPiAwICYmIGFjY2Vzc2libGVUYWJzW2tleV1bJ25hbWUnXSkge1xuICAgICAgICAvLyBJZiB0YWIgaGFzIGNoaWxkcmVuIC0gY3JlYXRlIGFuIG9wdGlvbiBncm91cCBhbmQgcHV0IGNoaWxkcmVuIGluc2lkZS5cbiAgICAgICAgY29uc3QgJG9wdGdyb3VwID0gdGhpcy5fY3JlYXRlT3B0aW9uR3JvdXAoYWNjZXNzaWJsZVRhYnNba2V5XVsnbmFtZSddKTtcblxuICAgICAgICBmb3IgKGxldCBjaGlsZEtleSBpbiBhY2Nlc3NpYmxlVGFic1trZXldWydjaGlsZHJlbiddKSB7XG4gICAgICAgICAgaWYgKGFjY2Vzc2libGVUYWJzW2tleV1bJ2NoaWxkcmVuJ11bY2hpbGRLZXldWyduYW1lJ10pIHtcbiAgICAgICAgICAgICRvcHRncm91cC5hcHBlbmQoXG4gICAgICAgICAgICAgIHRoaXMuX2NyZWF0ZU9wdGlvbihcbiAgICAgICAgICAgICAgICBhY2Nlc3NpYmxlVGFic1trZXldWydjaGlsZHJlbiddW2NoaWxkS2V5XVsnbmFtZSddLFxuICAgICAgICAgICAgICAgIGFjY2Vzc2libGVUYWJzW2tleV1bJ2NoaWxkcmVuJ11bY2hpbGRLZXldWydpZF90YWInXSlcbiAgICAgICAgICAgICk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgJHRhYnNEcm9wZG93bi5hcHBlbmQoJG9wdGdyb3VwKTtcbiAgICAgIH0gZWxzZSBpZiAoYWNjZXNzaWJsZVRhYnNba2V5XVsnbmFtZSddKSB7XG4gICAgICAgIC8vIElmIHRhYiBkb2Vzbid0IGhhdmUgY2hpbGRyZW4gLSBjcmVhdGUgYW4gb3B0aW9uLlxuICAgICAgICAkdGFic0Ryb3Bkb3duLmFwcGVuZChcbiAgICAgICAgICB0aGlzLl9jcmVhdGVPcHRpb24oXG4gICAgICAgICAgICBhY2Nlc3NpYmxlVGFic1trZXldWyduYW1lJ10sXG4gICAgICAgICAgICBhY2Nlc3NpYmxlVGFic1trZXldWydpZF90YWInXVxuICAgICAgICAgIClcbiAgICAgICAgKTtcbiAgICAgIH1cbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogSGlkZSBzaG9wIGNob2ljZSB0cmVlIGlmIHN1cGVyYWRtaW4gcHJvZmlsZSBpcyBzZWxlY3RlZCwgc2hvdyBpdCBvdGhlcndpc2UuXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdG9nZ2xlU2hvcFRyZWUoKSB7XG4gICAgY29uc3QgJGVtcGxveWVlUHJvZmlsZURyb3Bkb3duID0gJCh0aGlzLmVtcGxveWVlUHJvZmlsZVNlbGVjdG9yKTtcbiAgICBjb25zdCBzdXBlckFkbWluUHJvZmlsZUlkID0gJGVtcGxveWVlUHJvZmlsZURyb3Bkb3duLmRhdGEoJ2FkbWluLXByb2ZpbGUnKTtcbiAgICAkKHRoaXMuc2hvcENob2ljZVRyZWVTZWxlY3RvcilcbiAgICAgIC5jbG9zZXN0KCcuZm9ybS1ncm91cCcpXG4gICAgICAudG9nZ2xlQ2xhc3MoJ2Qtbm9uZScsICRlbXBsb3llZVByb2ZpbGVEcm9wZG93bi52YWwoKSA9PSBzdXBlckFkbWluUHJvZmlsZUlkKVxuICAgIDtcbiAgfVxuXG4gIC8qKlxuICAgKiBDcmVhdGVzIGFuIDxvcHRncm91cD4gZWxlbWVudFxuICAgKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gbmFtZVxuICAgKlxuICAgKiBAcmV0dXJucyB7alF1ZXJ5fVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NyZWF0ZU9wdGlvbkdyb3VwKG5hbWUpIHtcbiAgICByZXR1cm4gJChgPG9wdGdyb3VwIGxhYmVsPVwiJHtuYW1lfVwiPmApO1xuICB9XG5cbiAgLyoqXG4gICAqIENyZWF0ZXMgYW4gPG9wdGlvbj4gZWxlbWVudC5cbiAgICpcbiAgICogQHBhcmFtIHtTdHJpbmd9IG5hbWVcbiAgICogQHBhcmFtIHtTdHJpbmd9IHZhbHVlXG4gICAqXG4gICAqIEByZXR1cm5zIHtqUXVlcnl9XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfY3JlYXRlT3B0aW9uKG5hbWUsIHZhbHVlKSB7XG4gICAgcmV0dXJuICQoYDxvcHRpb24gdmFsdWU9XCIke3ZhbHVlfVwiPiR7bmFtZX08L29wdGlvbj5gKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvZW1wbG95ZWUvRW1wbG95ZWVGb3JtLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFJlc3BvbnNpYmxlIGZvciBjb25uZWN0aW5nIHRvIGFkZG9ucyBtYXJrZXRwbGFjZS5cbiAqIE1ha2VzIGFuIGFkZG9ucyBjb25uZWN0IHJlcXVlc3QgdG8gdGhlIHNlcnZlciwgZGlzcGxheXMgZXJyb3IgbWVzc2FnZXMgaWYgaXQgZmFpbHMuXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEFkZG9uc0Nvbm5lY3RvciB7XG4gIGNvbnN0cnVjdG9yKFxuICAgIGFkZG9uc0Nvbm5lY3RGb3JtU2VsZWN0b3IsXG4gICAgbG9hZGluZ1NwaW5uZXJTZWxlY3RvclxuICApIHtcbiAgICB0aGlzLmFkZG9uc0Nvbm5lY3RGb3JtU2VsZWN0b3IgPSBhZGRvbnNDb25uZWN0Rm9ybVNlbGVjdG9yO1xuICAgIHRoaXMuJGxvYWRpbmdTcGlubmVyID0gJChsb2FkaW5nU3Bpbm5lclNlbGVjdG9yKTtcblxuICAgIHRoaXMuX2luaXRFdmVudHMoKTtcblxuICAgIHJldHVybiB7fTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0aWFsaXplIGV2ZW50cyByZWxhdGVkIHRvIGNvbm5lY3Rpb24gdG8gYWRkb25zLlxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRFdmVudHMoKSB7XG4gICAgJCgnYm9keScpLm9uKCdzdWJtaXQnLCB0aGlzLmFkZG9uc0Nvbm5lY3RGb3JtU2VsZWN0b3IsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGZvcm0gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXG4gICAgICB0aGlzLl9jb25uZWN0KCRmb3JtLmF0dHIoJ2FjdGlvbicpLCAkZm9ybS5zZXJpYWxpemUoKSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogRG8gYSBQT1NUIHJlcXVlc3QgdG8gY29ubmVjdCB0byBhZGRvbnMuXG4gICAqXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBhZGRvbnNDb25uZWN0VXJsXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBmb3JtRGF0YVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2Nvbm5lY3QoYWRkb25zQ29ubmVjdFVybCwgZm9ybURhdGEpIHtcbiAgICAkLmFqYXgoe1xuICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgICB1cmw6IGFkZG9uc0Nvbm5lY3RVcmwsXG4gICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgZGF0YTogZm9ybURhdGEsXG4gICAgICBiZWZvcmVTZW5kOiAoKSA9PiB7XG4gICAgICAgIHRoaXMuJGxvYWRpbmdTcGlubmVyLnNob3coKTtcbiAgICAgICAgJCgnYnV0dG9uLmJ0blt0eXBlPVwic3VibWl0XCJdJywgdGhpcy5hZGRvbnNDb25uZWN0Rm9ybVNlbGVjdG9yKS5oaWRlKCk7XG4gICAgICB9XG4gICAgfSkudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgIGlmIChyZXNwb25zZS5zdWNjZXNzID09PSAxKSB7XG4gICAgICAgIGxvY2F0aW9uLnJlbG9hZCgpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgJC5ncm93bC5lcnJvcih7XG4gICAgICAgICAgbWVzc2FnZTogcmVzcG9uc2UubWVzc2FnZVxuICAgICAgICB9KTtcblxuICAgICAgICB0aGlzLiRsb2FkaW5nU3Bpbm5lci5oaWRlKCk7XG4gICAgICAgICQoJ2J1dHRvbi5idG5bdHlwZT1cInN1Ym1pdFwiXScsIHRoaXMuYWRkb25zQ29ubmVjdEZvcm1TZWxlY3RvcikuZmFkZUluKCk7XG4gICAgICB9XG4gICAgfSwgKCkgPT4ge1xuICAgICAgJC5ncm93bC5lcnJvcih7XG4gICAgICAgIG1lc3NhZ2U6ICQodGhpcy5hZGRvbnNDb25uZWN0Rm9ybVNlbGVjdG9yKS5kYXRhKCdlcnJvci1tZXNzYWdlJyksXG4gICAgICB9KTtcblxuICAgICAgdGhpcy4kbG9hZGluZ1NwaW5uZXIuaGlkZSgpO1xuICAgICAgJCgnYnV0dG9uLmJ0blt0eXBlPVwic3VibWl0XCJdJywgdGhpcy5hZGRvbnNDb25uZWN0Rm9ybVNlbGVjdG9yKS5zaG93KCk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvYWRkb25zLWNvbm5lY3Rvci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBHZW5lcmF0ZXMgYSBwYXNzd29yZCBhbmQgaW5mb3JtcyBhYm91dCBpdCdzIHN0cmVuZ3RoLlxuICogWW91IGNhbiBwYXNzIGEgcGFzc3dvcmQgaW5wdXQgdG8gd2F0Y2ggdGhlIHBhc3N3b3JkIHN0cmVuZ3RoIGFuZCBkaXNwbGF5IGZlZWRiYWNrIG1lc3NhZ2VzLlxuICogWW91IGNhbiBhbHNvIGdlbmVyYXRlIGEgcmFuZG9tIHBhc3N3b3JkIGludG8gYW4gaW5wdXQuXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENoYW5nZVBhc3N3b3JkSGFuZGxlciB7XG4gIGNvbnN0cnVjdG9yKHBhc3N3b3JkU3RyZW5ndGhGZWVkYmFja0NvbnRhaW5lclNlbGVjdG9yLCBvcHRpb25zID0ge30pIHtcbiAgICAvLyBNaW5pbXVtIGxlbmd0aCBvZiB0aGUgZ2VuZXJhdGVkIHBhc3N3b3JkLlxuICAgIHRoaXMubWluTGVuZ3RoID0gb3B0aW9ucy5taW5MZW5ndGggfHwgODtcblxuICAgIC8vIEZlZWRiYWNrIGNvbnRhaW5lciBob2xkcyBtZXNzYWdlcyByZXByZXNlbnRpbmcgcGFzc3dvcmQgc3RyZW5ndGguXG4gICAgdGhpcy4kZmVlZGJhY2tDb250YWluZXIgPSAkKHBhc3N3b3JkU3RyZW5ndGhGZWVkYmFja0NvbnRhaW5lclNlbGVjdG9yKTtcblxuICAgIHJldHVybiB7XG4gICAgICB3YXRjaFBhc3N3b3JkU3RyZW5ndGg6ICgkaW5wdXQpID0+IHRoaXMud2F0Y2hQYXNzd29yZFN0cmVuZ3RoKCRpbnB1dCksXG4gICAgICBnZW5lcmF0ZVBhc3N3b3JkOiAoJGlucHV0KSA9PiB0aGlzLmdlbmVyYXRlUGFzc3dvcmQoJGlucHV0KSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIFdhdGNoIHBhc3N3b3JkLCB3aGljaCBpcyBlbnRlcmVkIGluIHRoZSBpbnB1dCwgc3RyZW5ndGggYW5kIGluZm9ybSBhYm91dCBpdC5cbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRpbnB1dCB0aGUgaW5wdXQgdG8gd2F0Y2guXG4gICAqL1xuICB3YXRjaFBhc3N3b3JkU3RyZW5ndGgoJGlucHV0KSB7XG4gICAgJC5wYXNzeS5yZXF1aXJlbWVudHMubGVuZ3RoLm1pbiA9IHRoaXMubWluTGVuZ3RoO1xuICAgICQucGFzc3kucmVxdWlyZW1lbnRzLmNoYXJhY3RlcnMgPSAnRElHSVQnO1xuXG4gICAgJGlucHV0LmVhY2goKGluZGV4LCBlbGVtZW50KSA9PiB7XG4gICAgICBjb25zdCAkb3V0cHV0Q29udGFpbmVyID0gJCgnPHNwYW4+Jyk7XG5cbiAgICAgICRvdXRwdXRDb250YWluZXIuaW5zZXJ0QWZ0ZXIoJChlbGVtZW50KSk7XG5cbiAgICAgICQoZWxlbWVudCkucGFzc3koKHN0cmVuZ3RoLCB2YWxpZCkgPT4ge1xuICAgICAgICB0aGlzLl9kaXNwbGF5RmVlZGJhY2soJG91dHB1dENvbnRhaW5lciwgc3RyZW5ndGgsIHZhbGlkKTtcbiAgICAgIH0pO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEdlbmVyYXRlcyBhIHBhc3N3b3JkIGFuZCBmaWxscyBpdCB0byBnaXZlbiBpbnB1dC5cbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRpbnB1dCB0aGUgaW5wdXQgdG8gZmlsbCB0aGUgcGFzc3dvcmQgaW50by5cbiAgICovXG4gIGdlbmVyYXRlUGFzc3dvcmQoJGlucHV0KSB7XG4gICAgJGlucHV0LnBhc3N5KCdnZW5lcmF0ZScsIHRoaXMubWluTGVuZ3RoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNwbGF5IGZlZWRiYWNrIGFib3V0IHBhc3N3b3JkJ3Mgc3RyZW5ndGguXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkb3V0cHV0Q29udGFpbmVyIGEgY29udGFpbmVyIHRvIHB1dCBmZWVkYmFjayBvdXRwdXQgaW50by5cbiAgICogQHBhcmFtIHtudW1iZXJ9IHBhc3N3b3JkU3RyZW5ndGhcbiAgICogQHBhcmFtIHtib29sZWFufSBpc1Bhc3N3b3JkVmFsaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9kaXNwbGF5RmVlZGJhY2soJG91dHB1dENvbnRhaW5lciwgcGFzc3dvcmRTdHJlbmd0aCwgaXNQYXNzd29yZFZhbGlkKSB7XG4gICAgY29uc3QgZmVlZGJhY2sgPSB0aGlzLl9nZXRQYXNzd29yZFN0cmVuZ3RoRmVlZGJhY2socGFzc3dvcmRTdHJlbmd0aCk7XG4gICAgJG91dHB1dENvbnRhaW5lci50ZXh0KGZlZWRiYWNrLm1lc3NhZ2UpO1xuICAgICRvdXRwdXRDb250YWluZXIucmVtb3ZlQ2xhc3MoJ3RleHQtZGFuZ2VyIHRleHQtd2FybmluZyB0ZXh0LXN1Y2Nlc3MnKTtcbiAgICAkb3V0cHV0Q29udGFpbmVyLmFkZENsYXNzKGZlZWRiYWNrLmVsZW1lbnRDbGFzcyk7XG4gICAgJG91dHB1dENvbnRhaW5lci50b2dnbGVDbGFzcygnZC1ub25lJywgIWlzUGFzc3dvcmRWYWxpZCk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGZlZWRiYWNrIHRoYXQgZGVzY3JpYmVzIGdpdmVuIHBhc3N3b3JkIHN0cmVuZ3RoLlxuICAgKiBSZXNwb25zZSBjb250YWlucyB0ZXh0IG1lc3NhZ2UgYW5kIGVsZW1lbnQgY2xhc3MuXG4gICAqXG4gICAqIEBwYXJhbSB7bnVtYmVyfSBzdHJlbmd0aFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldFBhc3N3b3JkU3RyZW5ndGhGZWVkYmFjayhzdHJlbmd0aCkge1xuICAgIHN3aXRjaCAoc3RyZW5ndGgpIHtcbiAgICAgIGNhc2UgJC5wYXNzeS5zdHJlbmd0aC5MT1c6XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgbWVzc2FnZTogdGhpcy4kZmVlZGJhY2tDb250YWluZXIuZmluZCgnLnN0cmVuZ3RoLWxvdycpLnRleHQoKSxcbiAgICAgICAgICBlbGVtZW50Q2xhc3M6ICd0ZXh0LWRhbmdlcicsXG4gICAgICAgIH07XG5cbiAgICAgIGNhc2UgJC5wYXNzeS5zdHJlbmd0aC5NRURJVU06XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgbWVzc2FnZTogdGhpcy4kZmVlZGJhY2tDb250YWluZXIuZmluZCgnLnN0cmVuZ3RoLW1lZGl1bScpLnRleHQoKSxcbiAgICAgICAgICBlbGVtZW50Q2xhc3M6ICd0ZXh0LXdhcm5pbmcnLFxuICAgICAgICB9O1xuXG4gICAgICBjYXNlICQucGFzc3kuc3RyZW5ndGguSElHSDpcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICBtZXNzYWdlOiB0aGlzLiRmZWVkYmFja0NvbnRhaW5lci5maW5kKCcuc3RyZW5ndGgtaGlnaCcpLnRleHQoKSxcbiAgICAgICAgICBlbGVtZW50Q2xhc3M6ICd0ZXh0LXN1Y2Nlc3MnLFxuICAgICAgICB9O1xuXG4gICAgICBjYXNlICQucGFzc3kuc3RyZW5ndGguRVhUUkVNRTpcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICBtZXNzYWdlOiB0aGlzLiRmZWVkYmFja0NvbnRhaW5lci5maW5kKCcuc3RyZW5ndGgtZXh0cmVtZScpLnRleHQoKSxcbiAgICAgICAgICBlbGVtZW50Q2xhc3M6ICd0ZXh0LXN1Y2Nlc3MnLFxuICAgICAgICB9O1xuICAgIH1cblxuICAgIHRocm93ICdJbnZhbGlkIHBhc3N3b3JkIHN0cmVuZ3RoIGluZGljYXRvci4nO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2NoYW5nZS1wYXNzd29yZC1oYW5kbGVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IENoYW5nZVBhc3N3b3JkSGFuZGxlciBmcm9tIFwiLi4vY2hhbmdlLXBhc3N3b3JkLWhhbmRsZXJcIjtcbmltcG9ydCBQYXNzd29yZFZhbGlkYXRvciBmcm9tIFwiLi4vcGFzc3dvcmQtdmFsaWRhdG9yXCI7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyByZXNwb25zaWJsZSBmb3IgYWN0aW9ucyByZWxhdGVkIHRvIFwiY2hhbmdlIHBhc3N3b3JkXCIgZm9ybSB0eXBlLlxuICogR2VuZXJhdGVzIHJhbmRvbSBwYXNzd29yZHMsIHZhbGlkYXRlcyBuZXcgcGFzc3dvcmQgYW5kIGl0J3MgY29uZmlybWF0aW9uLFxuICogZGlzcGxheXMgZXJyb3IgbWVzc2FnZXMgcmVsYXRlZCB0byB2YWxpZGF0aW9uLlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDaGFuZ2VQYXNzd29yZENvbnRyb2wge1xuICBjb25zdHJ1Y3RvcihcbiAgICBpbnB1dHNCbG9ja1NlbGVjdG9yLFxuICAgIHNob3dCdXR0b25TZWxlY3RvcixcbiAgICBoaWRlQnV0dG9uU2VsZWN0b3IsXG4gICAgZ2VuZXJhdGVQYXNzd29yZEJ1dHRvblNlbGVjdG9yLFxuICAgIG9sZFBhc3N3b3JkSW5wdXRTZWxlY3RvcixcbiAgICBuZXdQYXNzd29yZElucHV0U2VsZWN0b3IsXG4gICAgY29uZmlybU5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvcixcbiAgICBnZW5lcmF0ZWRQYXNzd29yZERpc3BsYXlTZWxlY3RvcixcbiAgICBwYXNzd29yZFN0cmVuZ3RoRmVlZGJhY2tDb250YWluZXJTZWxlY3RvclxuICApIHtcbiAgICAvLyBCbG9jayB0aGF0IGNvbnRhaW5zIHBhc3N3b3JkIGlucHV0c1xuICAgIHRoaXMuJGlucHV0c0Jsb2NrID0gJChpbnB1dHNCbG9ja1NlbGVjdG9yKTtcblxuICAgIC8vIEJ1dHRvbiB0aGF0IHNob3dzIHRoZSBwYXNzd29yZCBpbnB1dHMgYmxvY2tcbiAgICB0aGlzLnNob3dCdXR0b25TZWxlY3RvciA9IHNob3dCdXR0b25TZWxlY3RvcjtcblxuICAgIC8vIEJ1dHRvbiB0aGF0IGhpZGVzIHRoZSBwYXNzd29yZCBpbnB1dHMgYmxvY2tcbiAgICB0aGlzLmhpZGVCdXR0b25TZWxlY3RvciA9IGhpZGVCdXR0b25TZWxlY3RvcjtcblxuICAgIC8vIEJ1dHRvbiB0aGF0IGdlbmVyYXRlcyBhIHJhbmRvbSBwYXNzd29yZFxuICAgIHRoaXMuZ2VuZXJhdGVQYXNzd29yZEJ1dHRvblNlbGVjdG9yID0gZ2VuZXJhdGVQYXNzd29yZEJ1dHRvblNlbGVjdG9yO1xuXG4gICAgLy8gSW5wdXQgdG8gZW50ZXIgb2xkIHBhc3N3b3JkXG4gICAgdGhpcy5vbGRQYXNzd29yZElucHV0U2VsZWN0b3IgPSBvbGRQYXNzd29yZElucHV0U2VsZWN0b3I7XG5cbiAgICAvLyBJbnB1dCB0byBlbnRlciBuZXcgcGFzc3dvcmRcbiAgICB0aGlzLm5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvciA9IG5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvcjtcblxuICAgIC8vIElucHV0IHRvIGNvbmZpcm0gdGhlIG5ldyBwYXNzd29yZFxuICAgIHRoaXMuY29uZmlybU5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvciA9IGNvbmZpcm1OZXdQYXNzd29yZElucHV0U2VsZWN0b3I7XG5cbiAgICAvLyBJbnB1dCB0aGF0IGRpc3BsYXlzIGdlbmVyYXRlZCByYW5kb20gcGFzc3dvcmRcbiAgICB0aGlzLmdlbmVyYXRlZFBhc3N3b3JkRGlzcGxheVNlbGVjdG9yID0gZ2VuZXJhdGVkUGFzc3dvcmREaXNwbGF5U2VsZWN0b3I7XG5cbiAgICAvLyBNYWluIGlucHV0IGZvciBwYXNzd29yZCBnZW5lcmF0aW9uXG4gICAgdGhpcy4kbmV3UGFzc3dvcmRJbnB1dHMgPSB0aGlzLiRpbnB1dHNCbG9ja1xuICAgICAgLmZpbmQodGhpcy5uZXdQYXNzd29yZElucHV0U2VsZWN0b3IpO1xuXG4gICAgLy8gR2VuZXJhdGVkIHBhc3N3b3JkIHdpbGwgYmUgY29waWVkIHRvIHRoZXNlIGlucHV0c1xuICAgIHRoaXMuJGNvcHlQYXNzd29yZElucHV0cyA9IHRoaXMuJGlucHV0c0Jsb2NrXG4gICAgICAuZmluZCh0aGlzLmNvbmZpcm1OZXdQYXNzd29yZElucHV0U2VsZWN0b3IpXG4gICAgICAuYWRkKHRoaXMuZ2VuZXJhdGVkUGFzc3dvcmREaXNwbGF5U2VsZWN0b3IpO1xuXG4gICAgLy8gQWxsIGlucHV0cyBpbiB0aGUgY2hhbmdlIHBhc3N3b3JkIGJsb2NrLCB0aGF0IGFyZSBzdWJtaXR0YWJsZSB3aXRoIHRoZSBmb3JtLlxuICAgIHRoaXMuJHN1Ym1pdHRhYmxlSW5wdXRzID0gdGhpcy4kaW5wdXRzQmxvY2tcbiAgICAgIC5maW5kKHRoaXMub2xkUGFzc3dvcmRJbnB1dFNlbGVjdG9yKVxuICAgICAgLmFkZCh0aGlzLm5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvcilcbiAgICAgIC5hZGQodGhpcy5jb25maXJtTmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yKTtcblxuICAgIHRoaXMucGFzc3dvcmRIYW5kbGVyID0gbmV3IENoYW5nZVBhc3N3b3JkSGFuZGxlcihcbiAgICAgIHBhc3N3b3JkU3RyZW5ndGhGZWVkYmFja0NvbnRhaW5lclNlbGVjdG9yXG4gICAgKTtcblxuICAgIHRoaXMucGFzc3dvcmRWYWxpZGF0b3IgPSBuZXcgUGFzc3dvcmRWYWxpZGF0b3IoXG4gICAgICB0aGlzLm5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvcixcbiAgICAgIHRoaXMuY29uZmlybU5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvclxuICAgICk7XG5cbiAgICB0aGlzLl9oaWRlSW5wdXRzQmxvY2soKTtcbiAgICB0aGlzLl9pbml0RXZlbnRzKCk7XG5cbiAgICByZXR1cm4ge307XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhbGl6ZSBldmVudHMuXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdEV2ZW50cygpIHtcbiAgICAvLyBTaG93IHRoZSBpbnB1dHMgYmxvY2sgd2hlbiBzaG93IGJ1dHRvbiBpcyBjbGlja2VkXG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5zaG93QnV0dG9uU2VsZWN0b3IsIChlKSA9PiB7XG4gICAgICB0aGlzLl9oaWRlKCQoZS5jdXJyZW50VGFyZ2V0KSk7XG4gICAgICB0aGlzLl9zaG93SW5wdXRzQmxvY2soKTtcbiAgICB9KTtcblxuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMuaGlkZUJ1dHRvblNlbGVjdG9yLCAoKSA9PiB7XG4gICAgICB0aGlzLl9oaWRlSW5wdXRzQmxvY2soKTtcbiAgICAgIHRoaXMuX3Nob3coJCh0aGlzLnNob3dCdXR0b25TZWxlY3RvcikpO1xuICAgIH0pO1xuXG4gICAgLy8gV2F0Y2ggYW5kIGRpc3BsYXkgZmVlZGJhY2sgYWJvdXQgcGFzc3dvcmQncyBzdHJlbmd0aFxuICAgIHRoaXMucGFzc3dvcmRIYW5kbGVyLndhdGNoUGFzc3dvcmRTdHJlbmd0aCh0aGlzLiRuZXdQYXNzd29yZElucHV0cyk7XG5cbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLmdlbmVyYXRlUGFzc3dvcmRCdXR0b25TZWxlY3RvciwgKCkgPT4ge1xuICAgICAgLy8gR2VuZXJhdGUgdGhlIHBhc3N3b3JkIGludG8gbWFpbiBpbnB1dC5cbiAgICAgIHRoaXMucGFzc3dvcmRIYW5kbGVyLmdlbmVyYXRlUGFzc3dvcmQodGhpcy4kbmV3UGFzc3dvcmRJbnB1dHMpO1xuXG4gICAgICAvLyBDb3B5IHRoZSBnZW5lcmF0ZWQgcGFzc3dvcmQgZnJvbSBtYWluIGlucHV0IHRvIGFkZGl0aW9uYWwgaW5wdXRzXG4gICAgICB0aGlzLiRjb3B5UGFzc3dvcmRJbnB1dHMudmFsKHRoaXMuJG5ld1Bhc3N3b3JkSW5wdXRzLnZhbCgpKTtcbiAgICAgIHRoaXMuX2NoZWNrUGFzc3dvcmRWYWxpZGl0eSgpO1xuICAgIH0pO1xuXG4gICAgLy8gVmFsaWRhdGUgbmV3IHBhc3N3b3JkIGFuZCBpdCdzIGNvbmZpcm1hdGlvbiB3aGVuIGFueSBvZiB0aGUgaW5wdXRzIGlzIGNoYW5nZWRcbiAgICAkKGRvY3VtZW50KS5vbigna2V5dXAnLCBgJHt0aGlzLm5ld1Bhc3N3b3JkSW5wdXRTZWxlY3Rvcn0sJHt0aGlzLmNvbmZpcm1OZXdQYXNzd29yZElucHV0U2VsZWN0b3J9YCwgKCkgPT4ge1xuICAgICAgdGhpcy5fY2hlY2tQYXNzd29yZFZhbGlkaXR5KCk7XG4gICAgfSk7XG5cbiAgICAvLyBQcmV2ZW50IHN1Ym1pdHRpbmcgdGhlIGZvcm0gaWYgbmV3IHBhc3N3b3JkIGlzIG5vdCB2YWxpZFxuICAgICQoZG9jdW1lbnQpLm9uKCdzdWJtaXQnLCAkKHRoaXMub2xkUGFzc3dvcmRJbnB1dFNlbGVjdG9yKS5jbG9zZXN0KCdmb3JtJyksIChldmVudCkgPT4ge1xuICAgICAgLy8gSWYgcGFzc3dvcmQgaW5wdXQgaXMgZGlzYWJsZWQgLSB3ZSBkb24ndCBuZWVkIHRvIHZhbGlkYXRlIGl0LlxuICAgICAgaWYgKCQodGhpcy5vbGRQYXNzd29yZElucHV0U2VsZWN0b3IpLmlzKCc6ZGlzYWJsZWQnKSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIGlmICghdGhpcy5wYXNzd29yZFZhbGlkYXRvci5pc1Bhc3N3b3JkVmFsaWQoKSkge1xuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIENoZWNrIGlmIHBhc3N3b3JkIGlzIHZhbGlkLCBzaG93IGVycm9yIG1lc3NhZ2VzIGlmIGl0J3Mgbm90LlxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NoZWNrUGFzc3dvcmRWYWxpZGl0eSgpIHtcbiAgICBjb25zdCAkZmlyc3RQYXNzd29yZEVycm9yQ29udGFpbmVyID0gJCh0aGlzLm5ld1Bhc3N3b3JkSW5wdXRTZWxlY3RvcikucGFyZW50KCkuZmluZCgnLmZvcm0tdGV4dCcpO1xuICAgIGNvbnN0ICRzZWNvbmRQYXNzd29yZEVycm9yQ29udGFpbmVyID0gJCh0aGlzLmNvbmZpcm1OZXdQYXNzd29yZElucHV0U2VsZWN0b3IpLnBhcmVudCgpLmZpbmQoJy5mb3JtLXRleHQnKTtcblxuICAgICRmaXJzdFBhc3N3b3JkRXJyb3JDb250YWluZXJcbiAgICAgIC50ZXh0KHRoaXMuX2dldFBhc3N3b3JkTGVuZ3RoVmFsaWRhdGlvbk1lc3NhZ2UoKSlcbiAgICAgIC50b2dnbGVDbGFzcygndGV4dC1kYW5nZXInLCAhdGhpcy5wYXNzd29yZFZhbGlkYXRvci5pc1Bhc3N3b3JkTGVuZ3RoVmFsaWQoKSlcbiAgICA7XG5cbiAgICAkc2Vjb25kUGFzc3dvcmRFcnJvckNvbnRhaW5lclxuICAgICAgLnRleHQodGhpcy5fZ2V0UGFzc3dvcmRDb25maXJtYXRpb25WYWxpZGF0aW9uTWVzc2FnZSgpKVxuICAgICAgLnRvZ2dsZUNsYXNzKCd0ZXh0LWRhbmdlcicsICF0aGlzLnBhc3N3b3JkVmFsaWRhdG9yLmlzUGFzc3dvcmRNYXRjaGluZ0NvbmZpcm1hdGlvbigpKVxuICAgIDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgcGFzc3dvcmQgY29uZmlybWF0aW9uIHZhbGlkYXRpb24gbWVzc2FnZS5cbiAgICpcbiAgICogQHJldHVybnMge1N0cmluZ31cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRQYXNzd29yZENvbmZpcm1hdGlvblZhbGlkYXRpb25NZXNzYWdlKCkge1xuICAgIGlmICghdGhpcy5wYXNzd29yZFZhbGlkYXRvci5pc1Bhc3N3b3JkTWF0Y2hpbmdDb25maXJtYXRpb24oKSkge1xuICAgICAgcmV0dXJuICQodGhpcy5jb25maXJtTmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yKS5kYXRhKCdpbnZhbGlkLXBhc3N3b3JkJyk7XG4gICAgfVxuXG4gICAgcmV0dXJuICcnO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBwYXNzd29yZCBsZW5ndGggdmFsaWRhdGlvbiBtZXNzYWdlLlxuICAgKlxuICAgKiBAcmV0dXJucyB7U3RyaW5nfVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldFBhc3N3b3JkTGVuZ3RoVmFsaWRhdGlvbk1lc3NhZ2UoKSB7XG4gICAgaWYgKHRoaXMucGFzc3dvcmRWYWxpZGF0b3IuaXNQYXNzd29yZFRvb1Nob3J0KCkpIHtcbiAgICAgIHJldHVybiAkKHRoaXMubmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yKS5kYXRhKCdwYXNzd29yZC10b28tc2hvcnQnKVxuICAgIH1cblxuICAgIGlmICh0aGlzLnBhc3N3b3JkVmFsaWRhdG9yLmlzUGFzc3dvcmRUb29Mb25nKCkpIHtcbiAgICAgIHJldHVybiAkKHRoaXMubmV3UGFzc3dvcmRJbnB1dFNlbGVjdG9yKS5kYXRhKCdwYXNzd29yZC10b28tbG9uZycpO1xuICAgIH1cblxuICAgIHJldHVybiAnJztcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93IHRoZSBwYXNzd29yZCBpbnB1dHMgYmxvY2suXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd0lucHV0c0Jsb2NrKCkge1xuICAgIHRoaXMuX3Nob3codGhpcy4kaW5wdXRzQmxvY2spO1xuICAgIHRoaXMuJHN1Ym1pdHRhYmxlSW5wdXRzLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gICAgdGhpcy4kc3VibWl0dGFibGVJbnB1dHMuYXR0cigncmVxdWlyZWQnLCAncmVxdWlyZWQnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlIHRoZSBwYXNzd29yZCBpbnB1dHMgYmxvY2suXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZUlucHV0c0Jsb2NrKCkge1xuICAgIHRoaXMuX2hpZGUodGhpcy4kaW5wdXRzQmxvY2spO1xuICAgIHRoaXMuJHN1Ym1pdHRhYmxlSW5wdXRzLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG4gICAgdGhpcy4kc3VibWl0dGFibGVJbnB1dHMucmVtb3ZlQXR0cigncmVxdWlyZWQnKTtcbiAgICB0aGlzLiRpbnB1dHNCbG9jay5maW5kKCdpbnB1dCcpLnZhbCgnJyk7XG4gICAgdGhpcy4kaW5wdXRzQmxvY2suZmluZCgnLmZvcm0tdGV4dCcpLnRleHQoJycpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGUgYW4gZWxlbWVudC5cbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRlbFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGUoJGVsKSB7XG4gICAgJGVsLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93IGhpZGRlbiBlbGVtZW50LlxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGVsXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvdygkZWwpIHtcbiAgICAkZWwucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2Zvcm0vY2hhbmdlLXBhc3N3b3JkLWNvbnRyb2wuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG4vKipcbiAqIENsYXNzIHJlc3BvbnNpYmxlIGZvciBjaGVja2luZyBwYXNzd29yZCdzIHZhbGlkaXR5LlxuICogQ2FuIHZhbGlkYXRlIGVudGVyZWQgcGFzc3dvcmQncyBsZW5ndGggYWdhaW5zdCBtaW4vbWF4IHZhbHVlcy5cbiAqIElmIHBhc3N3b3JkIGNvbmZpcm1hdGlvbiBpbnB1dCBpcyBwcm92aWRlZCwgY2FuIHZhbGlkYXRlIGlmIGVudGVyZWQgcGFzc3dvcmQgaXMgbWF0Y2hpbmcgY29uZmlybWF0aW9uLlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBQYXNzd29yZFZhbGlkYXRvciB7XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBwYXNzd29yZElucHV0U2VsZWN0b3Igc2VsZWN0b3Igb2YgdGhlIHBhc3N3b3JkIGlucHV0LlxuICAgKiBAcGFyYW0ge1N0cmluZ3xudWxsfSBjb25maXJtUGFzc3dvcmRJbnB1dFNlbGVjdG9yIChvcHRpb25hbCkgc2VsZWN0b3IgZm9yIHRoZSBwYXNzd29yZCBjb25maXJtYXRpb24gaW5wdXQuXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBvcHRpb25zIGFsbG93cyBvdmVycmlkaW5nIGRlZmF1bHQgb3B0aW9ucy5cbiAgICovXG4gIGNvbnN0cnVjdG9yKHBhc3N3b3JkSW5wdXRTZWxlY3RvciwgY29uZmlybVBhc3N3b3JkSW5wdXRTZWxlY3RvciA9IG51bGwsIG9wdGlvbnMgPSB7fSkge1xuICAgIHRoaXMubmV3UGFzc3dvcmRJbnB1dCA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IocGFzc3dvcmRJbnB1dFNlbGVjdG9yKTtcbiAgICB0aGlzLmNvbmZpcm1QYXNzd29yZElucHV0ID0gZG9jdW1lbnQucXVlcnlTZWxlY3Rvcihjb25maXJtUGFzc3dvcmRJbnB1dFNlbGVjdG9yKTtcblxuICAgIC8vIE1pbmltdW0gYWxsb3dlZCBsZW5ndGggZm9yIGVudGVyZWQgcGFzc3dvcmRcbiAgICB0aGlzLm1pblBhc3N3b3JkTGVuZ3RoID0gb3B0aW9ucy5taW5QYXNzd29yZExlbmd0aCB8fCA4O1xuXG4gICAgLy8gTWF4aW11bSBhbGxvd2VkIGxlbmd0aCBmb3IgZW50ZXJlZCBwYXNzd29yZFxuICAgIHRoaXMubWF4UGFzc3dvcmRMZW5ndGggPSBvcHRpb25zLm1heFBhc3N3b3JkTGVuZ3RoIHx8IDI1NTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDaGVjayBpZiB0aGUgcGFzc3dvcmQgaXMgdmFsaWQuXG4gICAqXG4gICAqIEByZXR1cm5zIHtib29sZWFufVxuICAgKi9cbiAgaXNQYXNzd29yZFZhbGlkKCkge1xuICAgIGlmICh0aGlzLmNvbmZpcm1QYXNzd29yZElucHV0ICYmICF0aGlzLmlzUGFzc3dvcmRNYXRjaGluZ0NvbmZpcm1hdGlvbigpKSB7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuXG4gICAgcmV0dXJuIHRoaXMuaXNQYXNzd29yZExlbmd0aFZhbGlkKCk7XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2sgaWYgcGFzc3dvcmQncyBsZW5ndGggaXMgdmFsaWQuXG4gICAqXG4gICAqIEByZXR1cm5zIHtib29sZWFufVxuICAgKi9cbiAgaXNQYXNzd29yZExlbmd0aFZhbGlkKCkge1xuICAgIHJldHVybiAhdGhpcy5pc1Bhc3N3b3JkVG9vU2hvcnQoKSAmJiAhdGhpcy5pc1Bhc3N3b3JkVG9vTG9uZygpO1xuICB9XG5cbiAgLyoqXG4gICAqIENoZWNrIGlmIHBhc3N3b3JkIGlzIG1hdGNoaW5nIGl0J3MgY29uZmlybWF0aW9uLlxuICAgKlxuICAgKiBAcmV0dXJucyB7Ym9vbGVhbn1cbiAgICovXG4gIGlzUGFzc3dvcmRNYXRjaGluZ0NvbmZpcm1hdGlvbigpIHtcbiAgICBpZiAoIXRoaXMuY29uZmlybVBhc3N3b3JkSW5wdXQpIHtcbiAgICAgIHRocm93ICdDb25maXJtIHBhc3N3b3JkIGlucHV0IGlzIG5vdCBwcm92aWRlZCBmb3IgdGhlIHBhc3N3b3JkIHZhbGlkYXRvci4nO1xuICAgIH1cblxuICAgIGlmICh0aGlzLmNvbmZpcm1QYXNzd29yZElucHV0LnZhbHVlID09PSAnJykge1xuICAgICAgcmV0dXJuIHRydWU7XG4gICAgfVxuXG4gICAgcmV0dXJuIHRoaXMubmV3UGFzc3dvcmRJbnB1dC52YWx1ZSA9PT0gdGhpcy5jb25maXJtUGFzc3dvcmRJbnB1dC52YWx1ZTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDaGVjayBpZiBwYXNzd29yZCBpcyB0b28gc2hvcnQuXG4gICAqXG4gICAqIEByZXR1cm5zIHtib29sZWFufVxuICAgKi9cbiAgaXNQYXNzd29yZFRvb1Nob3J0KCkge1xuICAgIHJldHVybiB0aGlzLm5ld1Bhc3N3b3JkSW5wdXQudmFsdWUubGVuZ3RoIDwgdGhpcy5taW5QYXNzd29yZExlbmd0aDtcbiAgfVxuXG4gIC8qKlxuICAgKiBDaGVjayBpZiBwYXNzd29yZCBpcyB0b28gbG9uZy5cbiAgICpcbiAgICogQHJldHVybnMge2Jvb2xlYW59XG4gICAqL1xuICBpc1Bhc3N3b3JkVG9vTG9uZygpIHtcbiAgICByZXR1cm4gdGhpcy5uZXdQYXNzd29yZElucHV0LnZhbHVlLmxlbmd0aCA+IHRoaXMubWF4UGFzc3dvcmRMZW5ndGg7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvcGFzc3dvcmQtdmFsaWRhdG9yLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBEZWZpbmVzIGFsbCBzZWxlY3RvcnMgdGhhdCBhcmUgdXNlZCBpbiBlbXBsb3llZSBhZGQvZWRpdCBmb3JtLlxuICovXG5leHBvcnQgZGVmYXVsdCB7XG4gIHNob3BDaG9pY2VUcmVlOiAnI2VtcGxveWVlX3Nob3BfYXNzb2NpYXRpb24nLFxuICBwcm9maWxlU2VsZWN0OiAnI2VtcGxveWVlX3Byb2ZpbGUnLFxuICBkZWZhdWx0UGFnZVNlbGVjdDogJyNlbXBsb3llZV9kZWZhdWx0X3BhZ2UnLFxuICBhZGRvbnNDb25uZWN0Rm9ybTogJyNhZGRvbnMtY29ubmVjdC1mb3JtJyxcbiAgYWRkb25zTG9naW5CdXR0b246ICcjYWRkb25zX2xvZ2luX2J0bicsXG5cbiAgLy8gc2VsZWN0b3JzIHJlbGF0ZWQgdG8gXCJjaGFuZ2UgcGFzc3dvcmRcIiBmb3JtIGNvbnRyb2xcbiAgY2hhbmdlUGFzc3dvcmRJbnB1dHNCbG9jazogJy5qcy1jaGFuZ2UtcGFzc3dvcmQtYmxvY2snLFxuICBzaG93Q2hhbmdlUGFzc3dvcmRCbG9ja0J1dHRvbjogJy5qcy1jaGFuZ2UtcGFzc3dvcmQnLFxuICBoaWRlQ2hhbmdlUGFzc3dvcmRCbG9ja0J1dHRvbjogJy5qcy1jaGFuZ2UtcGFzc3dvcmQtY2FuY2VsJyxcbiAgZ2VuZXJhdGVQYXNzd29yZEJ1dHRvbjogJyNlbXBsb3llZV9jaGFuZ2VfcGFzc3dvcmRfZ2VuZXJhdGVfcGFzc3dvcmRfYnV0dG9uJyxcbiAgb2xkUGFzc3dvcmRJbnB1dDogJyNlbXBsb3llZV9jaGFuZ2VfcGFzc3dvcmRfb2xkX3Bhc3N3b3JkJyxcbiAgbmV3UGFzc3dvcmRJbnB1dDogJyNlbXBsb3llZV9jaGFuZ2VfcGFzc3dvcmRfbmV3X3Bhc3N3b3JkX2ZpcnN0JyxcbiAgY29uZmlybU5ld1Bhc3N3b3JkSW5wdXQ6ICcjZW1wbG95ZWVfY2hhbmdlX3Bhc3N3b3JkX25ld19wYXNzd29yZF9zZWNvbmQnLFxuICBnZW5lcmF0ZWRQYXNzd29yZERpc3BsYXlJbnB1dDogJyNlbXBsb3llZV9jaGFuZ2VfcGFzc3dvcmRfZ2VuZXJhdGVkX3Bhc3N3b3JkJyxcbiAgcGFzc3dvcmRTdHJlbmd0aEZlZWRiYWNrQ29udGFpbmVyOiAnLmpzLXBhc3N3b3JkLXN0cmVuZ3RoLWZlZWRiYWNrJyxcbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2VtcGxveWVlL2VtcGxveWVlLWZvcm0tbWFwLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IEVtcGxveWVlRm9ybSBmcm9tIFwiLi9FbXBsb3llZUZvcm1cIjtcblxuJCgoKSA9PiB7XG4gIG5ldyBFbXBsb3llZUZvcm0oKTtcbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvZW1wbG95ZWUvZm9ybS5qcyIsIihmdW5jdGlvbigpIHsgbW9kdWxlLmV4cG9ydHMgPSB3aW5kb3dbXCJqUXVlcnlcIl07IH0oKSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gZXh0ZXJuYWwgXCJqUXVlcnlcIlxuLy8gbW9kdWxlIGlkID0gN1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA2IDIyIDI4IDMwIl0sInNvdXJjZVJvb3QiOiIifQ==