window["webservice"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 401);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
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

/***/ 10:
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
 * Class LinkRowActionExtension handles link row actions
 */

var LinkRowActionExtension = function () {
  function LinkRowActionExtension() {
    _classCallCheck(this, LinkRowActionExtension);
  }

  _createClass(LinkRowActionExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      this.initRowLinks(grid);
      this.initConfirmableActions(grid);
    }

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */

  }, {
    key: 'initConfirmableActions',
    value: function initConfirmableActions(grid) {
      grid.getContainer().on('click', '.js-link-row-action', function (event) {
        var confirmMessage = $(event.currentTarget).data('confirm-message');

        if (confirmMessage.length && !confirm(confirmMessage)) {
          event.preventDefault();
        }
      });
    }

    /**
     * Add a click event on rows that matches the first link action (if present)
     *
     * @param {Grid} grid
     */

  }, {
    key: 'initRowLinks',
    value: function initRowLinks(grid) {
      $('tr', grid.getContainer()).each(function initEachRow() {
        var $parentRow = $(this);

        $('.js-link-row-action[data-clickable-row=1]:first', $parentRow).each(function propagateFirstLinkAction() {
          var $rowAction = $(this);
          var $parentCell = $rowAction.closest('td');

          var clickableCells = $('td.clickable', $parentRow).not($parentCell);

          clickableCells.addClass('cursor-pointer').click(function () {
            var confirmMessage = $rowAction.data('confirm-message');

            if (!confirmMessage.length || confirm(confirmMessage)) {
              document.location = $rowAction.attr('href');
            }
          });
        });
      });
    }
  }]);

  return LinkRowActionExtension;
}();

exports.default = LinkRowActionExtension;

/***/ }),

/***/ 11:
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
 * Class SubmitRowActionExtension handles submitting of row action
 */

var SubmitRowActionExtension = function () {
  function SubmitRowActionExtension() {
    _classCallCheck(this, SubmitRowActionExtension);
  }

  _createClass(SubmitRowActionExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      grid.getContainer().on('click', '.js-submit-row-action', function (event) {
        event.preventDefault();

        var $button = $(event.currentTarget);
        var confirmMessage = $button.data('confirm-message');

        if (confirmMessage.length && !confirm(confirmMessage)) {
          return;
        }

        var method = $button.data('method');
        var isGetOrPostMethod = ['GET', 'POST'].includes(method);

        var $form = $('<form>', {
          'action': $button.data('url'),
          'method': isGetOrPostMethod ? method : 'POST'
        }).appendTo('body');

        if (!isGetOrPostMethod) {
          $form.append($('<input>', {
            'type': '_hidden',
            'name': '_method',
            'value': method
          }));
        }

        $form.submit();
      });
    }
  }]);

  return SubmitRowActionExtension;
}();

exports.default = SubmitRowActionExtension;

/***/ }),

/***/ 19:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

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

var $ = global.$;

/**
 * Class ReloadListExtension extends grid with "Column toggling" feature
 */

var ColumnTogglingExtension = function () {
  function ColumnTogglingExtension() {
    _classCallCheck(this, ColumnTogglingExtension);
  }

  _createClass(ColumnTogglingExtension, [{
    key: 'extend',


    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var _this = this;

      var $table = grid.getContainer().find('table.table');
      $table.find('.ps-togglable-row').on('click', function (e) {
        e.preventDefault();
        _this._toggleValue($(e.delegateTarget));
      });
    }

    /**
     * @param {jQuery} row
     * @private
     */

  }, {
    key: '_toggleValue',
    value: function _toggleValue(row) {
      var toggleUrl = row.data('toggleUrl');

      this._submitAsForm(toggleUrl);
    }

    /**
     * Submits request url as form
     *
     * @param {string} toggleUrl
     * @private
     */

  }, {
    key: '_submitAsForm',
    value: function _submitAsForm(toggleUrl) {
      var $form = $('<form>', {
        action: toggleUrl,
        method: 'POST'
      }).appendTo('body');

      $form.submit();
    }
  }]);

  return ColumnTogglingExtension;
}();

exports.default = ColumnTogglingExtension;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(0)))

/***/ }),

/***/ 2:
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
 * Class is responsible for handling Grid events
 */

var Grid = function () {
  /**
   * Grid id
   *
   * @param {string} id
   */
  function Grid(id) {
    _classCallCheck(this, Grid);

    this.id = id;
    this.$container = $('#' + this.id + '_grid');
  }

  /**
   * Get grid id
   *
   * @returns {string}
   */


  _createClass(Grid, [{
    key: 'getId',
    value: function getId() {
      return this.id;
    }

    /**
     * Get grid container
     *
     * @returns {jQuery}
     */

  }, {
    key: 'getContainer',
    value: function getContainer() {
      return this.$container;
    }

    /**
     * Get grid header container
     *
     * @returns {jQuery}
     */

  }, {
    key: 'getHeaderContainer',
    value: function getHeaderContainer() {
      return this.$container.closest('.js-grid-panel').find('.js-grid-header');
    }

    /**
     * Extend grid with external extensions
     *
     * @param {object} extension
     */

  }, {
    key: 'addExtension',
    value: function addExtension(extension) {
      extension.extend(this);
    }
  }]);

  return Grid;
}();

exports.default = Grid;

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

/***/ 250:
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

/**
 * Generates random values for inputs.
 *
 * Usage:
 *
 * There should be a button in HTML with 2 required data-* properties:
 *    1. data-target-input-id - input id for which value should be generated
 *    2. data-generated-value-size -
 *
 * Example button: <button class="js-generator-btn"
 *                         data-target-input-id="my-input-id"
 *                         data-generated-value-length="16"
 *                 >
 *                     Generate!
 *                 </button>
 *
 * In JavaScript you have to enable this functionality using GeneratableInput component like so:
 *
 * const generateableInput = new GeneratableInput();
 * generateableInput.attachOn('.js-generator-btn'); // every time our button is clicked
 *                                                  // it will generate random value of 16 characters
 *                                                  // for input with id of "my-input-id"
 *
 * You can attach as many different buttons as you like using "attachOn()" function
 * as long as 2 required data-* attributes are present at each button.
 */
var GeneratableInput = function () {
  function GeneratableInput() {
    var _this = this;

    _classCallCheck(this, GeneratableInput);

    return {
      'attachOn': function attachOn(btnSelector) {
        return _this._attachOn(btnSelector);
      }
    };
  }

  /**
   * Attaches event listener on button than can generate value
   *
   * @param {String} generatorBtnSelector
   *
   * @private
   */


  _createClass(GeneratableInput, [{
    key: '_attachOn',
    value: function _attachOn(generatorBtnSelector) {
      var _this2 = this;

      document.querySelector(generatorBtnSelector).addEventListener('click', function (event) {
        var attributes = event.currentTarget.attributes;

        var targetInputId = attributes.getNamedItem('data-target-input-id').value;
        var generatedValueLength = parseInt(attributes.getNamedItem('data-generated-value-length').value);

        var targetInput = document.querySelector('#' + targetInputId);
        targetInput.value = _this2._generateValue(generatedValueLength);
      });
    }

    /**
     * Generates random value for input
     *
     * @param {Number} length
     *
     * @returns {string}
     *
     * @private
     */

  }, {
    key: '_generateValue',
    value: function _generateValue(length) {
      var chars = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
      var generatedValue = '';

      for (var i = 1; i <= length; ++i) {
        generatedValue += chars.charAt(Math.floor(Math.random() * chars.length));
      }

      return generatedValue;
    }
  }]);

  return GeneratableInput;
}();

exports.default = GeneratableInput;

/***/ }),

/***/ 298:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

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
 * In Add/Edit page of Webservice key there is permissions table input (permissons as columns / resources as rows).
 * There is "All" column and once resource is checked under this column
 * every other permission column should be auto-selected for that resource.
 */

var PermissionsRowSelector = function PermissionsRowSelector() {
  _classCallCheck(this, PermissionsRowSelector);

  // when checkbox in "All" column is checked
  $('input[id^="webservice_key_permissions_all"]').on('change', function (event) {
    var $checkedBox = $(event.currentTarget);

    var isChecked = $checkedBox.is(':checked');

    // for each input in same row we need to toggle its value
    $checkedBox.closest('tr').find('input:not(input[id="' + $checkedBox.attr('id') + '"])').each(function (i, input) {
      $(input).prop('checked', isChecked);
    });
  });

  return {};
};

exports.default = PermissionsRowSelector;

/***/ }),

/***/ 3:
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

var _reset_search = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@app/utils/reset_search\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _reset_search2 = _interopRequireDefault(_reset_search);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Class FiltersResetExtension extends grid with filters resetting
 */

var FiltersResetExtension = function () {
  function FiltersResetExtension() {
    _classCallCheck(this, FiltersResetExtension);
  }

  _createClass(FiltersResetExtension, [{
    key: 'extend',


    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      grid.getContainer().on('click', '.js-reset-search', function (event) {
        (0, _reset_search2.default)($(event.currentTarget).data('url'), $(event.currentTarget).data('redirect'));
      });
    }
  }]);

  return FiltersResetExtension;
}();

exports.default = FiltersResetExtension;

/***/ }),

/***/ 4:
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

var _tableSorting = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@app/utils/table-sorting\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _tableSorting2 = _interopRequireDefault(_tableSorting);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Class ReloadListExtension extends grid with "List reload" action
 */
var SortingExtension = function () {
  function SortingExtension() {
    _classCallCheck(this, SortingExtension);
  }

  _createClass(SortingExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var $sortableTable = grid.getContainer().find('table.table');

      new _tableSorting2.default($sortableTable).attach();
    }
  }]);

  return SortingExtension;
}();

exports.default = SortingExtension;

/***/ }),

/***/ 401:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _grid = __webpack_require__(2);

var _grid2 = _interopRequireDefault(_grid);

var _filtersResetExtension = __webpack_require__(3);

var _filtersResetExtension2 = _interopRequireDefault(_filtersResetExtension);

var _reloadListExtension = __webpack_require__(5);

var _reloadListExtension2 = _interopRequireDefault(_reloadListExtension);

var _exportToSqlManagerExtension = __webpack_require__(6);

var _exportToSqlManagerExtension2 = _interopRequireDefault(_exportToSqlManagerExtension);

var _bulkActionCheckboxExtension = __webpack_require__(8);

var _bulkActionCheckboxExtension2 = _interopRequireDefault(_bulkActionCheckboxExtension);

var _submitBulkActionExtension = __webpack_require__(9);

var _submitBulkActionExtension2 = _interopRequireDefault(_submitBulkActionExtension);

var _sortingExtension = __webpack_require__(4);

var _sortingExtension2 = _interopRequireDefault(_sortingExtension);

var _submitRowActionExtension = __webpack_require__(11);

var _submitRowActionExtension2 = _interopRequireDefault(_submitRowActionExtension);

var _columnTogglingExtension = __webpack_require__(19);

var _columnTogglingExtension2 = _interopRequireDefault(_columnTogglingExtension);

var _choiceTree = __webpack_require__(20);

var _choiceTree2 = _interopRequireDefault(_choiceTree);

var _generatableInput = __webpack_require__(250);

var _generatableInput2 = _interopRequireDefault(_generatableInput);

var _multipleChoiceTable = __webpack_require__(67);

var _multipleChoiceTable2 = _interopRequireDefault(_multipleChoiceTable);

var _permissionsRowSelector = __webpack_require__(298);

var _permissionsRowSelector2 = _interopRequireDefault(_permissionsRowSelector);

var _linkRowActionExtension = __webpack_require__(10);

var _linkRowActionExtension2 = _interopRequireDefault(_linkRowActionExtension);

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
  var webserviceGrid = new _grid2.default('webservice_key');

  webserviceGrid.addExtension(new _reloadListExtension2.default());
  webserviceGrid.addExtension(new _exportToSqlManagerExtension2.default());
  webserviceGrid.addExtension(new _filtersResetExtension2.default());
  webserviceGrid.addExtension(new _columnTogglingExtension2.default());
  webserviceGrid.addExtension(new _sortingExtension2.default());
  webserviceGrid.addExtension(new _submitBulkActionExtension2.default());
  webserviceGrid.addExtension(new _submitRowActionExtension2.default());
  webserviceGrid.addExtension(new _bulkActionCheckboxExtension2.default());
  webserviceGrid.addExtension(new _linkRowActionExtension2.default());

  // needed for shop association input in form
  new _choiceTree2.default('#webservice_key_shop_association').enableAutoCheckChildren();

  // needed for permissions input in form
  new _multipleChoiceTable2.default();

  // needed for key input in form
  var generatableInput = new _generatableInput2.default();
  generatableInput.attachOn('.js-generator-btn');

  new _permissionsRowSelector2.default();
});

/***/ }),

/***/ 5:
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

/**
 * Class ReloadListExtension extends grid with "List reload" action
 */
var ReloadListExtension = function () {
  function ReloadListExtension() {
    _classCallCheck(this, ReloadListExtension);
  }

  _createClass(ReloadListExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      grid.getHeaderContainer().on('click', '.js-common_refresh_list-grid-action', function () {
        location.reload();
      });
    }
  }]);

  return ReloadListExtension;
}();

exports.default = ReloadListExtension;

/***/ }),

/***/ 6:
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
 * Class ExportToSqlManagerExtension extends grid with exporting query to SQL Manager
 */

var ExportToSqlManagerExtension = function () {
  function ExportToSqlManagerExtension() {
    _classCallCheck(this, ExportToSqlManagerExtension);
  }

  _createClass(ExportToSqlManagerExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var _this = this;

      grid.getHeaderContainer().on('click', '.js-common_show_query-grid-action', function () {
        return _this._onShowSqlQueryClick(grid);
      });
      grid.getHeaderContainer().on('click', '.js-common_export_sql_manager-grid-action', function () {
        return _this._onExportSqlManagerClick(grid);
      });
    }

    /**
     * Invoked when clicking on the "show sql query" toolbar button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_onShowSqlQueryClick',
    value: function _onShowSqlQueryClick(grid) {
      var $sqlManagerForm = $('#' + grid.getId() + '_common_show_query_modal_form');
      this._fillExportForm($sqlManagerForm, grid);

      var $modal = $('#' + grid.getId() + '_grid_common_show_query_modal');
      $modal.modal('show');

      $modal.on('click', '.btn-sql-submit', function () {
        return $sqlManagerForm.submit();
      });
    }

    /**
     * Invoked when clicking on the "export to the sql query" toolbar button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_onExportSqlManagerClick',
    value: function _onExportSqlManagerClick(grid) {
      var $sqlManagerForm = $('#' + grid.getId() + '_common_show_query_modal_form');

      this._fillExportForm($sqlManagerForm, grid);

      $sqlManagerForm.submit();
    }

    /**
     * Fill export form with SQL and it's name
     *
     * @param {jQuery} $sqlManagerForm
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_fillExportForm',
    value: function _fillExportForm($sqlManagerForm, grid) {
      var query = grid.getContainer().find('.js-grid-table').data('query');

      $sqlManagerForm.find('textarea[name="sql"]').val(query);
      $sqlManagerForm.find('input[name="name"]').val(this._getNameFromBreadcrumb());
    }

    /**
     * Get export name from page's breadcrumb
     *
     * @return {String}
     *
     * @private
     */

  }, {
    key: '_getNameFromBreadcrumb',
    value: function _getNameFromBreadcrumb() {
      var $breadcrumbs = $('.header-toolbar').find('.breadcrumb-item');
      var name = '';

      $breadcrumbs.each(function (i, item) {
        var $breadcrumb = $(item);

        var breadcrumbTitle = 0 < $breadcrumb.find('a').length ? $breadcrumb.find('a').text() : $breadcrumb.text();

        if (0 < name.length) {
          name = name.concat(' > ');
        }

        name = name.concat(breadcrumbTitle);
      });

      return name;
    }
  }]);

  return ExportToSqlManagerExtension;
}();

exports.default = ExportToSqlManagerExtension;

/***/ }),

/***/ 67:
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
 * MultipleChoiceTable is responsible for managing common actions in multiple choice table form type
 */

var MultipleChoiceTable = function () {
  /**
   * Init constructor
   */
  function MultipleChoiceTable() {
    var _this = this;

    _classCallCheck(this, MultipleChoiceTable);

    $(document).on('click', '.js-multiple-choice-table-select-column', function (e) {
      return _this.handleSelectColumn(e);
    });
  }

  /**
   * Check/uncheck all boxes in column
   *
   * @param {Event} event
   */


  _createClass(MultipleChoiceTable, [{
    key: 'handleSelectColumn',
    value: function handleSelectColumn(event) {
      event.preventDefault();

      var $selectColumnBtn = $(event.target);
      var checked = $selectColumnBtn.data('column-checked');
      $selectColumnBtn.data('column-checked', !checked);

      var $table = $selectColumnBtn.closest('table');

      $table.find('tbody tr td:nth-child(' + $selectColumnBtn.data('column-num') + ') input[type=checkbox]').prop('checked', !checked);
    }
  }]);

  return MultipleChoiceTable;
}();

exports.default = MultipleChoiceTable;

/***/ }),

/***/ 8:
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
 * Class BulkActionSelectCheckboxExtension
 */

var BulkActionCheckboxExtension = function () {
  function BulkActionCheckboxExtension() {
    _classCallCheck(this, BulkActionCheckboxExtension);
  }

  _createClass(BulkActionCheckboxExtension, [{
    key: 'extend',

    /**
     * Extend grid with bulk action checkboxes handling functionality
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      this._handleBulkActionCheckboxSelect(grid);
      this._handleBulkActionSelectAllCheckbox(grid);
    }

    /**
     * Handles "Select all" button in the grid
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_handleBulkActionSelectAllCheckbox',
    value: function _handleBulkActionSelectAllCheckbox(grid) {
      var _this = this;

      grid.getContainer().on('change', '.js-bulk-action-select-all', function (e) {
        var $checkbox = $(e.currentTarget);

        var isChecked = $checkbox.is(':checked');
        if (isChecked) {
          _this._enableBulkActionsBtn(grid);
        } else {
          _this._disableBulkActionsBtn(grid);
        }

        grid.getContainer().find('.js-bulk-action-checkbox').prop('checked', isChecked);
      });
    }

    /**
     * Handles each bulk action checkbox select in the grid
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_handleBulkActionCheckboxSelect',
    value: function _handleBulkActionCheckboxSelect(grid) {
      var _this2 = this;

      grid.getContainer().on('change', '.js-bulk-action-checkbox', function () {
        var checkedRowsCount = grid.getContainer().find('.js-bulk-action-checkbox:checked').length;

        if (checkedRowsCount > 0) {
          _this2._enableBulkActionsBtn(grid);
        } else {
          _this2._disableBulkActionsBtn(grid);
        }
      });
    }

    /**
     * Enable bulk actions button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_enableBulkActionsBtn',
    value: function _enableBulkActionsBtn(grid) {
      grid.getContainer().find('.js-bulk-actions-btn').prop('disabled', false);
    }

    /**
     * Disable bulk actions button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_disableBulkActionsBtn',
    value: function _disableBulkActionsBtn(grid) {
      grid.getContainer().find('.js-bulk-actions-btn').prop('disabled', true);
    }
  }]);

  return BulkActionCheckboxExtension;
}();

exports.default = BulkActionCheckboxExtension;

/***/ }),

/***/ 9:
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
 * Handles submit of grid actions
 */

var SubmitBulkActionExtension = function () {
  function SubmitBulkActionExtension() {
    var _this = this;

    _classCallCheck(this, SubmitBulkActionExtension);

    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }

  /**
   * Extend grid with bulk action submitting
   *
   * @param {Grid} grid
   */


  _createClass(SubmitBulkActionExtension, [{
    key: 'extend',
    value: function extend(grid) {
      var _this2 = this;

      grid.getContainer().on('click', '.js-bulk-action-submit-btn', function (event) {
        _this2.submit(event, grid);
      });
    }

    /**
     * Handle bulk action submitting
     *
     * @param {Event} event
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: 'submit',
    value: function submit(event, grid) {
      var $submitBtn = $(event.currentTarget);
      var confirmMessage = $submitBtn.data('confirm-message');

      if (typeof confirmMessage !== "undefined" && 0 < confirmMessage.length && !confirm(confirmMessage)) {
        return;
      }

      var $form = $('#' + grid.getId() + '_filter_form');

      $form.attr('action', $submitBtn.data('form-url'));
      $form.attr('method', $submitBtn.data('form-method'));
      $form.submit();
    }
  }]);

  return SubmitBulkActionExtension;
}();

exports.default = SubmitBulkActionExtension;

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgOTNlN2NjMWY2YWNkMjQxMGYyNDI/OGQ2NioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzPzM2OTgqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbGluay1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcz8zOWRjKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9yb3cvc3VibWl0LXJvdy1hY3Rpb24tZXh0ZW5zaW9uLmpzPzI3ZDEiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9jb2x1bW4tdG9nZ2xpbmctZXh0ZW5zaW9uLmpzPzY5NDMqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9ncmlkLmpzPzgxM2EqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZm9ybS9jaG9pY2UtdHJlZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dlbmVyYXRhYmxlLWlucHV0LmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL3dlYnNlcnZpY2UvcGVybWlzc2lvbnMtcm93LXNlbGVjdG9yLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1yZXNldC1leHRlbnNpb24uanM/MTZmMSoiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zb3J0aW5nLWV4dGVuc2lvbi5qcz8xMTNlKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy93ZWJzZXJ2aWNlL2luZGV4LmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcmVsb2FkLWxpc3QtZXh0ZW5zaW9uLmpzP2QzZTAqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZXhwb3J0LXRvLXNxbC1tYW5hZ2VyLWV4dGVuc2lvbi5qcz9lZDJhKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL211bHRpcGxlLWNob2ljZS10YWJsZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2J1bGstYWN0aW9uLWNoZWNrYm94LWV4dGVuc2lvbi5qcz9iMDk3KiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1idWxrLWFjdGlvbi1leHRlbnNpb24uanM/MWIxZioiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIkxpbmtSb3dBY3Rpb25FeHRlbnNpb24iLCJncmlkIiwiaW5pdFJvd0xpbmtzIiwiaW5pdENvbmZpcm1hYmxlQWN0aW9ucyIsImdldENvbnRhaW5lciIsIm9uIiwiZXZlbnQiLCJjb25maXJtTWVzc2FnZSIsImN1cnJlbnRUYXJnZXQiLCJkYXRhIiwibGVuZ3RoIiwiY29uZmlybSIsInByZXZlbnREZWZhdWx0IiwiZWFjaCIsImluaXRFYWNoUm93IiwiJHBhcmVudFJvdyIsInByb3BhZ2F0ZUZpcnN0TGlua0FjdGlvbiIsIiRyb3dBY3Rpb24iLCIkcGFyZW50Q2VsbCIsImNsb3Nlc3QiLCJjbGlja2FibGVDZWxscyIsIm5vdCIsImFkZENsYXNzIiwiY2xpY2siLCJkb2N1bWVudCIsImxvY2F0aW9uIiwiYXR0ciIsIlN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiIsIiRidXR0b24iLCJtZXRob2QiLCJpc0dldE9yUG9zdE1ldGhvZCIsImluY2x1ZGVzIiwiJGZvcm0iLCJhcHBlbmRUbyIsImFwcGVuZCIsInN1Ym1pdCIsImdsb2JhbCIsIkNvbHVtblRvZ2dsaW5nRXh0ZW5zaW9uIiwiJHRhYmxlIiwiZmluZCIsImUiLCJfdG9nZ2xlVmFsdWUiLCJkZWxlZ2F0ZVRhcmdldCIsInJvdyIsInRvZ2dsZVVybCIsIl9zdWJtaXRBc0Zvcm0iLCJhY3Rpb24iLCJHcmlkIiwiaWQiLCIkY29udGFpbmVyIiwiZXh0ZW5zaW9uIiwiZXh0ZW5kIiwiQ2hvaWNlVHJlZSIsInRyZWVTZWxlY3RvciIsIiRpbnB1dFdyYXBwZXIiLCJfdG9nZ2xlQ2hpbGRUcmVlIiwiJGFjdGlvbiIsIl90b2dnbGVUcmVlIiwiZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4iLCJlbmFibGVBbGxJbnB1dHMiLCJkaXNhYmxlQWxsSW5wdXRzIiwiJGNsaWNrZWRDaGVja2JveCIsIiRpdGVtV2l0aENoaWxkcmVuIiwicHJvcCIsImlzIiwicmVtb3ZlQXR0ciIsIiRwYXJlbnRXcmFwcGVyIiwiaGFzQ2xhc3MiLCJyZW1vdmVDbGFzcyIsIiRwYXJlbnRDb250YWluZXIiLCJjb25maWciLCJleHBhbmQiLCJjb2xsYXBzZSIsIm5leHRBY3Rpb24iLCJ0ZXh0IiwiaWNvbiIsImluZGV4IiwiaXRlbSIsIiRpdGVtIiwiR2VuZXJhdGFibGVJbnB1dCIsImJ0blNlbGVjdG9yIiwiX2F0dGFjaE9uIiwiZ2VuZXJhdG9yQnRuU2VsZWN0b3IiLCJxdWVyeVNlbGVjdG9yIiwiYWRkRXZlbnRMaXN0ZW5lciIsImF0dHJpYnV0ZXMiLCJ0YXJnZXRJbnB1dElkIiwiZ2V0TmFtZWRJdGVtIiwidmFsdWUiLCJnZW5lcmF0ZWRWYWx1ZUxlbmd0aCIsInBhcnNlSW50IiwidGFyZ2V0SW5wdXQiLCJfZ2VuZXJhdGVWYWx1ZSIsImNoYXJzIiwiZ2VuZXJhdGVkVmFsdWUiLCJpIiwiY2hhckF0IiwiTWF0aCIsImZsb29yIiwicmFuZG9tIiwiUGVybWlzc2lvbnNSb3dTZWxlY3RvciIsIiRjaGVja2VkQm94IiwiaXNDaGVja2VkIiwiaW5wdXQiLCJGaWx0ZXJzUmVzZXRFeHRlbnNpb24iLCJTb3J0aW5nRXh0ZW5zaW9uIiwiJHNvcnRhYmxlVGFibGUiLCJUYWJsZVNvcnRpbmciLCJhdHRhY2giLCJ3ZWJzZXJ2aWNlR3JpZCIsImFkZEV4dGVuc2lvbiIsIlJlbG9hZExpc3RBY3Rpb25FeHRlbnNpb24iLCJFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24iLCJTdWJtaXRCdWxrQWN0aW9uRXh0ZW5zaW9uIiwiQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uIiwiTXVsdGlwbGVDaG9pY2VUYWJsZSIsImdlbmVyYXRhYmxlSW5wdXQiLCJhdHRhY2hPbiIsIlJlbG9hZExpc3RFeHRlbnNpb24iLCJnZXRIZWFkZXJDb250YWluZXIiLCJyZWxvYWQiLCJfb25TaG93U3FsUXVlcnlDbGljayIsIl9vbkV4cG9ydFNxbE1hbmFnZXJDbGljayIsIiRzcWxNYW5hZ2VyRm9ybSIsImdldElkIiwiX2ZpbGxFeHBvcnRGb3JtIiwiJG1vZGFsIiwibW9kYWwiLCJxdWVyeSIsInZhbCIsIl9nZXROYW1lRnJvbUJyZWFkY3J1bWIiLCIkYnJlYWRjcnVtYnMiLCJuYW1lIiwiJGJyZWFkY3J1bWIiLCJicmVhZGNydW1iVGl0bGUiLCJjb25jYXQiLCJoYW5kbGVTZWxlY3RDb2x1bW4iLCIkc2VsZWN0Q29sdW1uQnRuIiwidGFyZ2V0IiwiY2hlY2tlZCIsIl9oYW5kbGVCdWxrQWN0aW9uQ2hlY2tib3hTZWxlY3QiLCJfaGFuZGxlQnVsa0FjdGlvblNlbGVjdEFsbENoZWNrYm94IiwiJGNoZWNrYm94IiwiX2VuYWJsZUJ1bGtBY3Rpb25zQnRuIiwiX2Rpc2FibGVCdWxrQWN0aW9uc0J0biIsImNoZWNrZWRSb3dzQ291bnQiLCIkc3VibWl0QnRuIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtB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QUFDWEEsV0FBS0csWUFBTCxHQUFvQkMsRUFBcEIsQ0FBdUIsT0FBdkIsRUFBZ0MsNEJBQWhDLEVBQThELFVBQUNDLEtBQUQsRUFBVztBQUN2RSxlQUFLNkIsTUFBTCxDQUFZN0IsS0FBWixFQUFtQkwsSUFBbkI7QUFDRCxPQUZEO0FBR0Q7O0FBRUQ7Ozs7Ozs7Ozs7OzJCQVFPSyxLLEVBQU9MLEksRUFBTTtBQUNsQixVQUFNaUosYUFBYXBKLEVBQUVRLE1BQU1FLGFBQVIsQ0FBbkI7QUFDQSxVQUFNRCxpQkFBaUIySSxXQUFXekksSUFBWCxDQUFnQixpQkFBaEIsQ0FBdkI7O0FBRUEsVUFBSSxPQUFPRixjQUFQLEtBQTBCLFdBQTFCLElBQXlDLElBQUlBLGVBQWVHLE1BQTVELElBQXNFLENBQUNDLFFBQVFKLGNBQVIsQ0FBM0UsRUFBb0c7QUFDbEc7QUFDRDs7QUFFRCxVQUFNeUIsUUFBUWxDLEVBQUUsTUFBTUcsS0FBSzJILEtBQUwsRUFBTixHQUFxQixjQUF2QixDQUFkOztBQUVBNUYsWUFBTU4sSUFBTixDQUFXLFFBQVgsRUFBcUJ3SCxXQUFXekksSUFBWCxDQUFnQixVQUFoQixDQUFyQjtBQUNBdUIsWUFBTU4sSUFBTixDQUFXLFFBQVgsRUFBcUJ3SCxXQUFXekksSUFBWCxDQUFnQixhQUFoQixDQUFyQjtBQUNBdUIsWUFBTUcsTUFBTjtBQUNEOzs7Ozs7a0JBdkNrQjhFLHlCIiwiZmlsZSI6IndlYnNlcnZpY2UuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSA0MDEpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDkzZTdjYzFmNmFjZDI0MTBmMjQyIiwidmFyIGc7XHJcblxyXG4vLyBUaGlzIHdvcmtzIGluIG5vbi1zdHJpY3QgbW9kZVxyXG5nID0gKGZ1bmN0aW9uKCkge1xyXG5cdHJldHVybiB0aGlzO1xyXG59KSgpO1xyXG5cclxudHJ5IHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIGV2YWwgaXMgYWxsb3dlZCAoc2VlIENTUClcclxuXHRnID0gZyB8fCBGdW5jdGlvbihcInJldHVybiB0aGlzXCIpKCkgfHwgKDEsZXZhbCkoXCJ0aGlzXCIpO1xyXG59IGNhdGNoKGUpIHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIHRoZSB3aW5kb3cgcmVmZXJlbmNlIGlzIGF2YWlsYWJsZVxyXG5cdGlmKHR5cGVvZiB3aW5kb3cgPT09IFwib2JqZWN0XCIpXHJcblx0XHRnID0gd2luZG93O1xyXG59XHJcblxyXG4vLyBnIGNhbiBzdGlsbCBiZSB1bmRlZmluZWQsIGJ1dCBub3RoaW5nIHRvIGRvIGFib3V0IGl0Li4uXHJcbi8vIFdlIHJldHVybiB1bmRlZmluZWQsIGluc3RlYWQgb2Ygbm90aGluZyBoZXJlLCBzbyBpdCdzXHJcbi8vIGVhc2llciB0byBoYW5kbGUgdGhpcyBjYXNlLiBpZighZ2xvYmFsKSB7IC4uLn1cclxuXHJcbm1vZHVsZS5leHBvcnRzID0gZztcclxuXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzXG4vLyBtb2R1bGUgaWQgPSAwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgNCAxMCAxMiAxNiAyMyAzMSA0OCA1MSIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIGhhbmRsZXMgbGluayByb3cgYWN0aW9uc1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICB0aGlzLmluaXRSb3dMaW5rcyhncmlkKTtcbiAgICB0aGlzLmluaXRDb25maXJtYWJsZUFjdGlvbnMoZ3JpZCk7XG4gIH1cblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBpbml0Q29uZmlybWFibGVBY3Rpb25zKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtbGluay1yb3ctYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG5cbiAgICAgIGlmIChjb25maXJtTWVzc2FnZS5sZW5ndGggJiYgIWNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQWRkIGEgY2xpY2sgZXZlbnQgb24gcm93cyB0aGF0IG1hdGNoZXMgdGhlIGZpcnN0IGxpbmsgYWN0aW9uIChpZiBwcmVzZW50KVxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGluaXRSb3dMaW5rcyhncmlkKSB7XG4gICAgJCgndHInLCBncmlkLmdldENvbnRhaW5lcigpKS5lYWNoKGZ1bmN0aW9uIGluaXRFYWNoUm93KCkge1xuICAgICAgY29uc3QgJHBhcmVudFJvdyA9ICQodGhpcyk7XG5cbiAgICAgICQoJy5qcy1saW5rLXJvdy1hY3Rpb25bZGF0YS1jbGlja2FibGUtcm93PTFdOmZpcnN0JywgJHBhcmVudFJvdykuZWFjaChmdW5jdGlvbiBwcm9wYWdhdGVGaXJzdExpbmtBY3Rpb24oKSB7XG4gICAgICAgIGNvbnN0ICRyb3dBY3Rpb24gPSAkKHRoaXMpO1xuICAgICAgICBjb25zdCAkcGFyZW50Q2VsbCA9ICRyb3dBY3Rpb24uY2xvc2VzdCgndGQnKTtcbiAgICAgICAgXG4gICAgICAgIGNvbnN0IGNsaWNrYWJsZUNlbGxzID0gJCgndGQuY2xpY2thYmxlJywgJHBhcmVudFJvdylcbiAgICAgICAgICAubm90KCRwYXJlbnRDZWxsKVxuICAgICAgICA7XG5cbiAgICAgICAgY2xpY2thYmxlQ2VsbHMuYWRkQ2xhc3MoJ2N1cnNvci1wb2ludGVyJykuY2xpY2soKCkgPT4ge1xuICAgICAgICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJHJvd0FjdGlvbi5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcblxuICAgICAgICAgIGlmICghY29uZmlybU1lc3NhZ2UubGVuZ3RoIHx8IGNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICAgICAgICBkb2N1bWVudC5sb2NhdGlvbiA9ICRyb3dBY3Rpb24uYXR0cignaHJlZicpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgICB9KTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9saW5rLXJvdy1hY3Rpb24tZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiBoYW5kbGVzIHN1Ym1pdHRpbmcgb2Ygcm93IGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1zdWJtaXQtcm93LWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgY29uc3QgJGJ1dHRvbiA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICRidXR0b24uZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG5cbiAgICAgIGlmIChjb25maXJtTWVzc2FnZS5sZW5ndGggJiYgIWNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgY29uc3QgbWV0aG9kID0gJGJ1dHRvbi5kYXRhKCdtZXRob2QnKTtcbiAgICAgIGNvbnN0IGlzR2V0T3JQb3N0TWV0aG9kID0gWydHRVQnLCAnUE9TVCddLmluY2x1ZGVzKG1ldGhvZCk7XG5cbiAgICAgIGNvbnN0ICRmb3JtID0gJCgnPGZvcm0+Jywge1xuICAgICAgICAnYWN0aW9uJzogJGJ1dHRvbi5kYXRhKCd1cmwnKSxcbiAgICAgICAgJ21ldGhvZCc6IGlzR2V0T3JQb3N0TWV0aG9kID8gbWV0aG9kIDogJ1BPU1QnLFxuICAgICAgfSkuYXBwZW5kVG8oJ2JvZHknKTtcblxuICAgICAgaWYgKCFpc0dldE9yUG9zdE1ldGhvZCkge1xuICAgICAgICAkZm9ybS5hcHBlbmQoJCgnPGlucHV0PicsIHtcbiAgICAgICAgICAndHlwZSc6ICdfaGlkZGVuJyxcbiAgICAgICAgICAnbmFtZSc6ICdfbWV0aG9kJyxcbiAgICAgICAgICAndmFsdWUnOiBtZXRob2RcbiAgICAgICAgfSkpO1xuICAgICAgfVxuXG4gICAgICAkZm9ybS5zdWJtaXQoKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9hY3Rpb24vcm93L3N1Ym1pdC1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiQ29sdW1uIHRvZ2dsaW5nXCIgZmVhdHVyZVxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbiB7XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBjb25zdCAkdGFibGUgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJ3RhYmxlLnRhYmxlJyk7XG4gICAgJHRhYmxlLmZpbmQoJy5wcy10b2dnbGFibGUtcm93Jykub24oJ2NsaWNrJywgKGUpID0+IHtcbiAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgIHRoaXMuX3RvZ2dsZVZhbHVlKCQoZS5kZWxlZ2F0ZVRhcmdldCkpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSByb3dcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGVWYWx1ZShyb3cpIHtcbiAgICBjb25zdCB0b2dnbGVVcmwgPSByb3cuZGF0YSgndG9nZ2xlVXJsJyk7XG5cbiAgICB0aGlzLl9zdWJtaXRBc0Zvcm0odG9nZ2xlVXJsKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTdWJtaXRzIHJlcXVlc3QgdXJsIGFzIGZvcm1cbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IHRvZ2dsZVVybFxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3N1Ym1pdEFzRm9ybSh0b2dnbGVVcmwpIHtcbiAgICBjb25zdCAkZm9ybSA9ICQoJzxmb3JtPicsIHtcbiAgICAgIGFjdGlvbjogdG9nZ2xlVXJsLFxuICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgfSkuYXBwZW5kVG8oJ2JvZHknKTtcblxuICAgICRmb3JtLnN1Ym1pdCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi10b2dnbGluZy1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgaXMgcmVzcG9uc2libGUgZm9yIGhhbmRsaW5nIEdyaWQgZXZlbnRzXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEdyaWQge1xuICAvKipcbiAgICogR3JpZCBpZFxuICAgKlxuICAgKiBAcGFyYW0ge3N0cmluZ30gaWRcbiAgICovXG4gIGNvbnN0cnVjdG9yKGlkKSB7XG4gICAgdGhpcy5pZCA9IGlkO1xuICAgIHRoaXMuJGNvbnRhaW5lciA9ICQoJyMnICsgdGhpcy5pZCArICdfZ3JpZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGlkXG4gICAqXG4gICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAqL1xuICBnZXRJZCgpIHtcbiAgICByZXR1cm4gdGhpcy5pZDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZ3JpZCBjb250YWluZXJcbiAgICpcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICovXG4gIGdldENvbnRhaW5lcigpIHtcbiAgICByZXR1cm4gdGhpcy4kY29udGFpbmVyO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGhlYWRlciBjb250YWluZXJcbiAgICpcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICovXG4gIGdldEhlYWRlckNvbnRhaW5lcigpIHtcbiAgICByZXR1cm4gdGhpcy4kY29udGFpbmVyLmNsb3Nlc3QoJy5qcy1ncmlkLXBhbmVsJykuZmluZCgnLmpzLWdyaWQtaGVhZGVyJyk7XG4gIH1cblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWQgd2l0aCBleHRlcm5hbCBleHRlbnNpb25zXG4gICAqXG4gICAqIEBwYXJhbSB7b2JqZWN0fSBleHRlbnNpb25cbiAgICovXG4gIGFkZEV4dGVuc2lvbihleHRlbnNpb24pIHtcbiAgICBleHRlbnNpb24uZXh0ZW5kKHRoaXMpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZ3JpZC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBIYW5kbGVzIFVJIGludGVyYWN0aW9ucyBvZiBjaG9pY2UgdHJlZVxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDaG9pY2VUcmVlIHtcbiAgLyoqXG4gICAqIEBwYXJhbSB7U3RyaW5nfSB0cmVlU2VsZWN0b3JcbiAgICovXG4gIGNvbnN0cnVjdG9yKHRyZWVTZWxlY3Rvcikge1xuICAgIHRoaXMuJGNvbnRhaW5lciA9ICQodHJlZVNlbGVjdG9yKTtcblxuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2xpY2snLCAnLmpzLWlucHV0LXdyYXBwZXInLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRpbnB1dFdyYXBwZXIgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICB0aGlzLl90b2dnbGVDaGlsZFRyZWUoJGlucHV0V3JhcHBlcik7XG4gICAgfSk7XG5cbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgJy5qcy10b2dnbGUtY2hvaWNlLXRyZWUtYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkYWN0aW9uID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcblxuICAgICAgdGhpcy5fdG9nZ2xlVHJlZSgkYWN0aW9uKTtcbiAgICB9KTtcblxuICAgIHJldHVybiB7XG4gICAgICBlbmFibGVBdXRvQ2hlY2tDaGlsZHJlbjogKCkgPT4gdGhpcy5lbmFibGVBdXRvQ2hlY2tDaGlsZHJlbigpLFxuICAgICAgZW5hYmxlQWxsSW5wdXRzOiAoKSA9PiB0aGlzLmVuYWJsZUFsbElucHV0cygpLFxuICAgICAgZGlzYWJsZUFsbElucHV0czogKCkgPT4gdGhpcy5kaXNhYmxlQWxsSW5wdXRzKCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbmFibGUgYXV0b21hdGljIGNoZWNrL3VuY2hlY2sgb2YgY2xpY2tlZCBpdGVtJ3MgY2hpbGRyZW4uXG4gICAqL1xuICBlbmFibGVBdXRvQ2hlY2tDaGlsZHJlbigpIHtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NoYW5nZScsICdpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0nLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRjbGlja2VkQ2hlY2tib3ggPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgY29uc3QgJGl0ZW1XaXRoQ2hpbGRyZW4gPSAkY2xpY2tlZENoZWNrYm94LmNsb3Nlc3QoJ2xpJyk7XG5cbiAgICAgICRpdGVtV2l0aENoaWxkcmVuXG4gICAgICAgIC5maW5kKCd1bCBpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0nKVxuICAgICAgICAucHJvcCgnY2hlY2tlZCcsICRjbGlja2VkQ2hlY2tib3guaXMoJzpjaGVja2VkJykpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEVuYWJsZSBhbGwgaW5wdXRzIGluIHRoZSBjaG9pY2UgdHJlZS5cbiAgICovXG4gIGVuYWJsZUFsbElucHV0cygpIHtcbiAgICB0aGlzLiRjb250YWluZXIuZmluZCgnaW5wdXQnKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIERpc2FibGUgYWxsIGlucHV0cyBpbiB0aGUgY2hvaWNlIHRyZWUuXG4gICAqL1xuICBkaXNhYmxlQWxsSW5wdXRzKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5maW5kKCdpbnB1dCcpLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG4gIH1cblxuICAvKipcbiAgICogQ29sbGFwc2Ugb3IgZXhwYW5kIHN1Yi10cmVlIGZvciBzaW5nbGUgcGFyZW50XG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkaW5wdXRXcmFwcGVyXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdG9nZ2xlQ2hpbGRUcmVlKCRpbnB1dFdyYXBwZXIpIHtcbiAgICBjb25zdCAkcGFyZW50V3JhcHBlciA9ICRpbnB1dFdyYXBwZXIuY2xvc2VzdCgnbGknKTtcblxuICAgIGlmICgkcGFyZW50V3JhcHBlci5oYXNDbGFzcygnZXhwYW5kZWQnKSkge1xuICAgICAgJHBhcmVudFdyYXBwZXJcbiAgICAgICAgLnJlbW92ZUNsYXNzKCdleHBhbmRlZCcpXG4gICAgICAgIC5hZGRDbGFzcygnY29sbGFwc2VkJyk7XG5cbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBpZiAoJHBhcmVudFdyYXBwZXIuaGFzQ2xhc3MoJ2NvbGxhcHNlZCcpKSB7XG4gICAgICAkcGFyZW50V3JhcHBlclxuICAgICAgICAucmVtb3ZlQ2xhc3MoJ2NvbGxhcHNlZCcpXG4gICAgICAgIC5hZGRDbGFzcygnZXhwYW5kZWQnKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogQ29sbGFwc2Ugb3IgZXhwYW5kIHdob2xlIHRyZWVcbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRhY3Rpb25cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGVUcmVlKCRhY3Rpb24pIHtcbiAgICBjb25zdCAkcGFyZW50Q29udGFpbmVyID0gJGFjdGlvbi5jbG9zZXN0KCcuanMtY2hvaWNlLXRyZWUtY29udGFpbmVyJyk7XG4gICAgY29uc3QgYWN0aW9uID0gJGFjdGlvbi5kYXRhKCdhY3Rpb24nKTtcblxuICAgIC8vIHRvZ2dsZSBhY3Rpb24gY29uZmlndXJhdGlvblxuICAgIGNvbnN0IGNvbmZpZyA9IHtcbiAgICAgIGFkZENsYXNzOiB7XG4gICAgICAgIGV4cGFuZDogJ2V4cGFuZGVkJyxcbiAgICAgICAgY29sbGFwc2U6ICdjb2xsYXBzZWQnLFxuICAgICAgfSxcbiAgICAgIHJlbW92ZUNsYXNzOiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlZCcsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kZWQnLFxuICAgICAgfSxcbiAgICAgIG5leHRBY3Rpb246IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2UnLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZCcsXG4gICAgICB9LFxuICAgICAgdGV4dDoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZWQtdGV4dCcsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kZWQtdGV4dCcsXG4gICAgICB9LFxuICAgICAgaWNvbjoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZWQtaWNvbicsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kZWQtaWNvbicsXG4gICAgICB9XG4gICAgfTtcblxuICAgICRwYXJlbnRDb250YWluZXIuZmluZCgnbGknKS5lYWNoKChpbmRleCwgaXRlbSkgPT4ge1xuICAgICAgY29uc3QgJGl0ZW0gPSAkKGl0ZW0pO1xuXG4gICAgICBpZiAoJGl0ZW0uaGFzQ2xhc3MoY29uZmlnLnJlbW92ZUNsYXNzW2FjdGlvbl0pKSB7XG4gICAgICAgICAgJGl0ZW0ucmVtb3ZlQ2xhc3MoY29uZmlnLnJlbW92ZUNsYXNzW2FjdGlvbl0pXG4gICAgICAgICAgICAuYWRkQ2xhc3MoY29uZmlnLmFkZENsYXNzW2FjdGlvbl0pO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgJGFjdGlvbi5kYXRhKCdhY3Rpb24nLCBjb25maWcubmV4dEFjdGlvblthY3Rpb25dKTtcbiAgICAkYWN0aW9uLmZpbmQoJy5tYXRlcmlhbC1pY29ucycpLnRleHQoJGFjdGlvbi5kYXRhKGNvbmZpZy5pY29uW2FjdGlvbl0pKTtcbiAgICAkYWN0aW9uLmZpbmQoJy5qcy10b2dnbGUtdGV4dCcpLnRleHQoJGFjdGlvbi5kYXRhKGNvbmZpZy50ZXh0W2FjdGlvbl0pKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9mb3JtL2Nob2ljZS10cmVlLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBHZW5lcmF0ZXMgcmFuZG9tIHZhbHVlcyBmb3IgaW5wdXRzLlxuICpcbiAqIFVzYWdlOlxuICpcbiAqIFRoZXJlIHNob3VsZCBiZSBhIGJ1dHRvbiBpbiBIVE1MIHdpdGggMiByZXF1aXJlZCBkYXRhLSogcHJvcGVydGllczpcbiAqICAgIDEuIGRhdGEtdGFyZ2V0LWlucHV0LWlkIC0gaW5wdXQgaWQgZm9yIHdoaWNoIHZhbHVlIHNob3VsZCBiZSBnZW5lcmF0ZWRcbiAqICAgIDIuIGRhdGEtZ2VuZXJhdGVkLXZhbHVlLXNpemUgLVxuICpcbiAqIEV4YW1wbGUgYnV0dG9uOiA8YnV0dG9uIGNsYXNzPVwianMtZ2VuZXJhdG9yLWJ0blwiXG4gKiAgICAgICAgICAgICAgICAgICAgICAgICBkYXRhLXRhcmdldC1pbnB1dC1pZD1cIm15LWlucHV0LWlkXCJcbiAqICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGEtZ2VuZXJhdGVkLXZhbHVlLWxlbmd0aD1cIjE2XCJcbiAqICAgICAgICAgICAgICAgICA+XG4gKiAgICAgICAgICAgICAgICAgICAgIEdlbmVyYXRlIVxuICogICAgICAgICAgICAgICAgIDwvYnV0dG9uPlxuICpcbiAqIEluIEphdmFTY3JpcHQgeW91IGhhdmUgdG8gZW5hYmxlIHRoaXMgZnVuY3Rpb25hbGl0eSB1c2luZyBHZW5lcmF0YWJsZUlucHV0IGNvbXBvbmVudCBsaWtlIHNvOlxuICpcbiAqIGNvbnN0IGdlbmVyYXRlYWJsZUlucHV0ID0gbmV3IEdlbmVyYXRhYmxlSW5wdXQoKTtcbiAqIGdlbmVyYXRlYWJsZUlucHV0LmF0dGFjaE9uKCcuanMtZ2VuZXJhdG9yLWJ0bicpOyAvLyBldmVyeSB0aW1lIG91ciBidXR0b24gaXMgY2xpY2tlZFxuICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIGl0IHdpbGwgZ2VuZXJhdGUgcmFuZG9tIHZhbHVlIG9mIDE2IGNoYXJhY3RlcnNcbiAqICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBmb3IgaW5wdXQgd2l0aCBpZCBvZiBcIm15LWlucHV0LWlkXCJcbiAqXG4gKiBZb3UgY2FuIGF0dGFjaCBhcyBtYW55IGRpZmZlcmVudCBidXR0b25zIGFzIHlvdSBsaWtlIHVzaW5nIFwiYXR0YWNoT24oKVwiIGZ1bmN0aW9uXG4gKiBhcyBsb25nIGFzIDIgcmVxdWlyZWQgZGF0YS0qIGF0dHJpYnV0ZXMgYXJlIHByZXNlbnQgYXQgZWFjaCBidXR0b24uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEdlbmVyYXRhYmxlSW5wdXQge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICByZXR1cm4ge1xuICAgICAgJ2F0dGFjaE9uJzogKGJ0blNlbGVjdG9yKSA9PiB0aGlzLl9hdHRhY2hPbihidG5TZWxlY3RvciksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBdHRhY2hlcyBldmVudCBsaXN0ZW5lciBvbiBidXR0b24gdGhhbiBjYW4gZ2VuZXJhdGUgdmFsdWVcbiAgICpcbiAgICogQHBhcmFtIHtTdHJpbmd9IGdlbmVyYXRvckJ0blNlbGVjdG9yXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfYXR0YWNoT24oZ2VuZXJhdG9yQnRuU2VsZWN0b3IpIHtcbiAgICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKGdlbmVyYXRvckJ0blNlbGVjdG9yKS5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgYXR0cmlidXRlcyA9IGV2ZW50LmN1cnJlbnRUYXJnZXQuYXR0cmlidXRlcztcblxuICAgICAgY29uc3QgdGFyZ2V0SW5wdXRJZCA9IGF0dHJpYnV0ZXMuZ2V0TmFtZWRJdGVtKCdkYXRhLXRhcmdldC1pbnB1dC1pZCcpLnZhbHVlO1xuICAgICAgY29uc3QgZ2VuZXJhdGVkVmFsdWVMZW5ndGggPSBwYXJzZUludChhdHRyaWJ1dGVzLmdldE5hbWVkSXRlbSgnZGF0YS1nZW5lcmF0ZWQtdmFsdWUtbGVuZ3RoJykudmFsdWUpO1xuXG4gICAgICBjb25zdCB0YXJnZXRJbnB1dCA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyMnICsgdGFyZ2V0SW5wdXRJZCk7XG4gICAgICB0YXJnZXRJbnB1dC52YWx1ZSA9IHRoaXMuX2dlbmVyYXRlVmFsdWUoZ2VuZXJhdGVkVmFsdWVMZW5ndGgpXG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogR2VuZXJhdGVzIHJhbmRvbSB2YWx1ZSBmb3IgaW5wdXRcbiAgICpcbiAgICogQHBhcmFtIHtOdW1iZXJ9IGxlbmd0aFxuICAgKlxuICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dlbmVyYXRlVmFsdWUobGVuZ3RoKSB7XG4gICAgY29uc3QgY2hhcnMgPSAnMTIzNDU2Nzg5QUJDREVGR0hJSktMTU5QUVJTVFVWV1hZWic7XG4gICAgbGV0IGdlbmVyYXRlZFZhbHVlID0gJyc7XG5cbiAgICBmb3IgKGxldCBpID0gMTsgaSA8PSBsZW5ndGg7ICsraSkge1xuICAgICAgZ2VuZXJhdGVkVmFsdWUgKz0gY2hhcnMuY2hhckF0KE1hdGguZmxvb3IoTWF0aC5yYW5kb20oKSAqIGNoYXJzLmxlbmd0aCkpO1xuICAgIH1cblxuICAgIHJldHVybiBnZW5lcmF0ZWRWYWx1ZTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9nZW5lcmF0YWJsZS1pbnB1dC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBJbiBBZGQvRWRpdCBwYWdlIG9mIFdlYnNlcnZpY2Uga2V5IHRoZXJlIGlzIHBlcm1pc3Npb25zIHRhYmxlIGlucHV0IChwZXJtaXNzb25zIGFzIGNvbHVtbnMgLyByZXNvdXJjZXMgYXMgcm93cykuXG4gKiBUaGVyZSBpcyBcIkFsbFwiIGNvbHVtbiBhbmQgb25jZSByZXNvdXJjZSBpcyBjaGVja2VkIHVuZGVyIHRoaXMgY29sdW1uXG4gKiBldmVyeSBvdGhlciBwZXJtaXNzaW9uIGNvbHVtbiBzaG91bGQgYmUgYXV0by1zZWxlY3RlZCBmb3IgdGhhdCByZXNvdXJjZS5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUGVybWlzc2lvbnNSb3dTZWxlY3RvciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIC8vIHdoZW4gY2hlY2tib3ggaW4gXCJBbGxcIiBjb2x1bW4gaXMgY2hlY2tlZFxuICAgICQoJ2lucHV0W2lkXj1cIndlYnNlcnZpY2Vfa2V5X3Blcm1pc3Npb25zX2FsbFwiXScpLm9uKCdjaGFuZ2UnLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRjaGVja2VkQm94ID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcblxuICAgICAgY29uc3QgaXNDaGVja2VkID0gJGNoZWNrZWRCb3guaXMoJzpjaGVja2VkJyk7XG5cbiAgICAgIC8vIGZvciBlYWNoIGlucHV0IGluIHNhbWUgcm93IHdlIG5lZWQgdG8gdG9nZ2xlIGl0cyB2YWx1ZVxuICAgICAgJGNoZWNrZWRCb3guY2xvc2VzdCgndHInKS5maW5kKGBpbnB1dDpub3QoaW5wdXRbaWQ9XCIkeyRjaGVja2VkQm94LmF0dHIoJ2lkJyl9XCJdKWApLmVhY2goKGksIGlucHV0KSA9PiB7XG4gICAgICAgICQoaW5wdXQpLnByb3AoJ2NoZWNrZWQnLCBpc0NoZWNrZWQpO1xuICAgICAgfSk7XG4gICAgfSk7XG5cbiAgICByZXR1cm4ge307XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL3dlYnNlcnZpY2UvcGVybWlzc2lvbnMtcm93LXNlbGVjdG9yLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IHJlc2V0U2VhcmNoIGZyb20gJ0BhcHAvdXRpbHMvcmVzZXRfc2VhcmNoJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEZpbHRlcnNSZXNldEV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBmaWx0ZXJzIHJlc2V0dGluZ1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24ge1xuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLXJlc2V0LXNlYXJjaCcsIChldmVudCkgPT4ge1xuICAgICAgcmVzZXRTZWFyY2goJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCd1cmwnKSwgJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdyZWRpcmVjdCcpKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBUYWJsZVNvcnRpbmcgZnJvbSAnQGFwcC91dGlscy90YWJsZS1zb3J0aW5nJztcblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiTGlzdCByZWxvYWRcIiBhY3Rpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU29ydGluZ0V4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgY29uc3QgJHNvcnRhYmxlVGFibGUgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJ3RhYmxlLnRhYmxlJyk7XG5cbiAgICBuZXcgVGFibGVTb3J0aW5nKCRzb3J0YWJsZVRhYmxlKS5hdHRhY2goKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zb3J0aW5nLWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBHcmlkIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dyaWQvZ3JpZFwiO1xuaW1wb3J0IEZpbHRlcnNSZXNldEV4dGVuc2lvbiBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvblwiO1xuaW1wb3J0IFJlbG9hZExpc3RBY3Rpb25FeHRlbnNpb24gZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcmVsb2FkLWxpc3QtZXh0ZW5zaW9uXCI7XG5pbXBvcnQgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2V4cG9ydC10by1zcWwtbWFuYWdlci1leHRlbnNpb25cIjtcbmltcG9ydCBCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24gZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYnVsay1hY3Rpb24tY2hlY2tib3gtZXh0ZW5zaW9uXCI7XG5pbXBvcnQgU3VibWl0QnVsa0FjdGlvbkV4dGVuc2lvbiBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zdWJtaXQtYnVsay1hY3Rpb24tZXh0ZW5zaW9uXCI7XG5pbXBvcnQgU29ydGluZ0V4dGVuc2lvbiBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zb3J0aW5nLWV4dGVuc2lvblwiO1xuaW1wb3J0IFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9hY3Rpb24vcm93L3N1Ym1pdC1yb3ctYWN0aW9uLWV4dGVuc2lvblwiO1xuaW1wb3J0IENvbHVtblRvZ2dsaW5nRXh0ZW5zaW9uIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi10b2dnbGluZy1leHRlbnNpb25cIjtcbmltcG9ydCBDaG9pY2VUcmVlIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2Zvcm0vY2hvaWNlLXRyZWVcIjtcbmltcG9ydCBHZW5lcmF0YWJsZUlucHV0IGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dlbmVyYXRhYmxlLWlucHV0XCI7XG5pbXBvcnQgTXVsdGlwbGVDaG9pY2VUYWJsZSBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9tdWx0aXBsZS1jaG9pY2UtdGFibGVcIjtcbmltcG9ydCBQZXJtaXNzaW9uc1Jvd1NlbGVjdG9yIGZyb20gXCIuL3Blcm1pc3Npb25zLXJvdy1zZWxlY3RvclwiO1xuaW1wb3J0IExpbmtSb3dBY3Rpb25FeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9saW5rLXJvdy1hY3Rpb24tZXh0ZW5zaW9uJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKCgpID0+IHtcbiAgY29uc3Qgd2Vic2VydmljZUdyaWQgPSBuZXcgR3JpZCgnd2Vic2VydmljZV9rZXknKTtcblxuICB3ZWJzZXJ2aWNlR3JpZC5hZGRFeHRlbnNpb24obmV3IFJlbG9hZExpc3RBY3Rpb25FeHRlbnNpb24oKSk7XG4gIHdlYnNlcnZpY2VHcmlkLmFkZEV4dGVuc2lvbihuZXcgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uKCkpO1xuICB3ZWJzZXJ2aWNlR3JpZC5hZGRFeHRlbnNpb24obmV3IEZpbHRlcnNSZXNldEV4dGVuc2lvbigpKTtcbiAgd2Vic2VydmljZUdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbigpKTtcbiAgd2Vic2VydmljZUdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTb3J0aW5nRXh0ZW5zaW9uKCkpO1xuICB3ZWJzZXJ2aWNlR3JpZC5hZGRFeHRlbnNpb24obmV3IFN1Ym1pdEJ1bGtBY3Rpb25FeHRlbnNpb24oKSk7XG4gIHdlYnNlcnZpY2VHcmlkLmFkZEV4dGVuc2lvbihuZXcgU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uKCkpO1xuICB3ZWJzZXJ2aWNlR3JpZC5hZGRFeHRlbnNpb24obmV3IEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbigpKTtcbiAgd2Vic2VydmljZUdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uKCkpO1xuXG4gIC8vIG5lZWRlZCBmb3Igc2hvcCBhc3NvY2lhdGlvbiBpbnB1dCBpbiBmb3JtXG4gIG5ldyBDaG9pY2VUcmVlKCcjd2Vic2VydmljZV9rZXlfc2hvcF9hc3NvY2lhdGlvbicpLmVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCk7XG5cbiAgLy8gbmVlZGVkIGZvciBwZXJtaXNzaW9ucyBpbnB1dCBpbiBmb3JtXG4gIG5ldyBNdWx0aXBsZUNob2ljZVRhYmxlKCk7XG5cbiAgLy8gbmVlZGVkIGZvciBrZXkgaW5wdXQgaW4gZm9ybVxuICBjb25zdCBnZW5lcmF0YWJsZUlucHV0ID0gbmV3IEdlbmVyYXRhYmxlSW5wdXQoKTtcbiAgZ2VuZXJhdGFibGVJbnB1dC5hdHRhY2hPbignLmpzLWdlbmVyYXRvci1idG4nKTtcblxuICBuZXcgUGVybWlzc2lvbnNSb3dTZWxlY3RvcigpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy93ZWJzZXJ2aWNlL2luZGV4LmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiTGlzdCByZWxvYWRcIiBhY3Rpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUmVsb2FkTGlzdEV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRIZWFkZXJDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWNvbW1vbl9yZWZyZXNoX2xpc3QtZ3JpZC1hY3Rpb24nLCAoKSA9PiB7XG4gICAgICBsb2NhdGlvbi5yZWxvYWQoKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9yZWxvYWQtbGlzdC1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIGV4cG9ydGluZyBxdWVyeSB0byBTUUwgTWFuYWdlclxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0SGVhZGVyQ29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1jb21tb25fc2hvd19xdWVyeS1ncmlkLWFjdGlvbicsICgpID0+IHRoaXMuX29uU2hvd1NxbFF1ZXJ5Q2xpY2soZ3JpZCkpO1xuICAgIGdyaWQuZ2V0SGVhZGVyQ29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1jb21tb25fZXhwb3J0X3NxbF9tYW5hZ2VyLWdyaWQtYWN0aW9uJywgKCkgPT4gdGhpcy5fb25FeHBvcnRTcWxNYW5hZ2VyQ2xpY2soZ3JpZCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEludm9rZWQgd2hlbiBjbGlja2luZyBvbiB0aGUgXCJzaG93IHNxbCBxdWVyeVwiIHRvb2xiYXIgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uU2hvd1NxbFF1ZXJ5Q2xpY2soZ3JpZCkge1xuICAgIGNvbnN0ICRzcWxNYW5hZ2VyRm9ybSA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19jb21tb25fc2hvd19xdWVyeV9tb2RhbF9mb3JtJyk7XG4gICAgdGhpcy5fZmlsbEV4cG9ydEZvcm0oJHNxbE1hbmFnZXJGb3JtLCBncmlkKTtcblxuICAgIGNvbnN0ICRtb2RhbCA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19ncmlkX2NvbW1vbl9zaG93X3F1ZXJ5X21vZGFsJyk7XG4gICAgJG1vZGFsLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAkbW9kYWwub24oJ2NsaWNrJywgJy5idG4tc3FsLXN1Ym1pdCcsICgpID0+ICRzcWxNYW5hZ2VyRm9ybS5zdWJtaXQoKSk7XG4gIH1cblxuICAvKipcbiAgICogSW52b2tlZCB3aGVuIGNsaWNraW5nIG9uIHRoZSBcImV4cG9ydCB0byB0aGUgc3FsIHF1ZXJ5XCIgdG9vbGJhciBidXR0b25cbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25FeHBvcnRTcWxNYW5hZ2VyQ2xpY2soZ3JpZCkge1xuICAgIGNvbnN0ICRzcWxNYW5hZ2VyRm9ybSA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19jb21tb25fc2hvd19xdWVyeV9tb2RhbF9mb3JtJyk7XG5cbiAgICB0aGlzLl9maWxsRXhwb3J0Rm9ybSgkc3FsTWFuYWdlckZvcm0sIGdyaWQpO1xuXG4gICAgJHNxbE1hbmFnZXJGb3JtLnN1Ym1pdCgpO1xuICB9XG5cbiAgLyoqXG4gICAqIEZpbGwgZXhwb3J0IGZvcm0gd2l0aCBTUUwgYW5kIGl0J3MgbmFtZVxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJHNxbE1hbmFnZXJGb3JtXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2ZpbGxFeHBvcnRGb3JtKCRzcWxNYW5hZ2VyRm9ybSwgZ3JpZCkge1xuICAgIGNvbnN0IHF1ZXJ5ID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtZ3JpZC10YWJsZScpLmRhdGEoJ3F1ZXJ5Jyk7XG5cbiAgICAkc3FsTWFuYWdlckZvcm0uZmluZCgndGV4dGFyZWFbbmFtZT1cInNxbFwiXScpLnZhbChxdWVyeSk7XG4gICAgJHNxbE1hbmFnZXJGb3JtLmZpbmQoJ2lucHV0W25hbWU9XCJuYW1lXCJdJykudmFsKHRoaXMuX2dldE5hbWVGcm9tQnJlYWRjcnVtYigpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZXhwb3J0IG5hbWUgZnJvbSBwYWdlJ3MgYnJlYWRjcnVtYlxuICAgKlxuICAgKiBAcmV0dXJuIHtTdHJpbmd9XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZ2V0TmFtZUZyb21CcmVhZGNydW1iKCkge1xuICAgIGNvbnN0ICRicmVhZGNydW1icyA9ICQoJy5oZWFkZXItdG9vbGJhcicpLmZpbmQoJy5icmVhZGNydW1iLWl0ZW0nKTtcbiAgICBsZXQgbmFtZSA9ICcnO1xuXG4gICAgJGJyZWFkY3J1bWJzLmVhY2goKGksIGl0ZW0pID0+IHtcbiAgICAgIGNvbnN0ICRicmVhZGNydW1iID0gJChpdGVtKTtcblxuICAgICAgY29uc3QgYnJlYWRjcnVtYlRpdGxlID0gMCA8ICRicmVhZGNydW1iLmZpbmQoJ2EnKS5sZW5ndGggP1xuICAgICAgICAkYnJlYWRjcnVtYi5maW5kKCdhJykudGV4dCgpIDpcbiAgICAgICAgJGJyZWFkY3J1bWIudGV4dCgpO1xuXG4gICAgICBpZiAoMCA8IG5hbWUubGVuZ3RoKSB7XG4gICAgICAgIG5hbWUgPSBuYW1lLmNvbmNhdCgnID4gJyk7XG4gICAgICB9XG5cbiAgICAgIG5hbWUgPSBuYW1lLmNvbmNhdChicmVhZGNydW1iVGl0bGUpO1xuICAgIH0pO1xuXG4gICAgcmV0dXJuIG5hbWU7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZXhwb3J0LXRvLXNxbC1tYW5hZ2VyLWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBNdWx0aXBsZUNob2ljZVRhYmxlIGlzIHJlc3BvbnNpYmxlIGZvciBtYW5hZ2luZyBjb21tb24gYWN0aW9ucyBpbiBtdWx0aXBsZSBjaG9pY2UgdGFibGUgZm9ybSB0eXBlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIE11bHRpcGxlQ2hvaWNlVGFibGUge1xuICAvKipcbiAgICogSW5pdCBjb25zdHJ1Y3RvclxuICAgKi9cbiAgY29uc3RydWN0b3IoKSB7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5qcy1tdWx0aXBsZS1jaG9pY2UtdGFibGUtc2VsZWN0LWNvbHVtbicsIChlKSA9PiB0aGlzLmhhbmRsZVNlbGVjdENvbHVtbihlKSk7XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2svdW5jaGVjayBhbGwgYm94ZXMgaW4gY29sdW1uXG4gICAqXG4gICAqIEBwYXJhbSB7RXZlbnR9IGV2ZW50XG4gICAqL1xuICBoYW5kbGVTZWxlY3RDb2x1bW4oZXZlbnQpIHtcbiAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgY29uc3QgJHNlbGVjdENvbHVtbkJ0biA9ICQoZXZlbnQudGFyZ2V0KTtcbiAgICBjb25zdCBjaGVja2VkID0gJHNlbGVjdENvbHVtbkJ0bi5kYXRhKCdjb2x1bW4tY2hlY2tlZCcpO1xuICAgICRzZWxlY3RDb2x1bW5CdG4uZGF0YSgnY29sdW1uLWNoZWNrZWQnLCAhY2hlY2tlZCk7XG5cbiAgICBjb25zdCAkdGFibGUgPSAkc2VsZWN0Q29sdW1uQnRuLmNsb3Nlc3QoJ3RhYmxlJyk7XG5cbiAgICAkdGFibGVcbiAgICAgIC5maW5kKCd0Ym9keSB0ciB0ZDpudGgtY2hpbGQoJyArICRzZWxlY3RDb2x1bW5CdG4uZGF0YSgnY29sdW1uLW51bScpICsgJykgaW5wdXRbdHlwZT1jaGVja2JveF0nKVxuICAgICAgLnByb3AoJ2NoZWNrZWQnLCAhY2hlY2tlZCk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvbXVsdGlwbGUtY2hvaWNlLXRhYmxlLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEJ1bGtBY3Rpb25TZWxlY3RDaGVja2JveEV4dGVuc2lvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWQgd2l0aCBidWxrIGFjdGlvbiBjaGVja2JveGVzIGhhbmRsaW5nIGZ1bmN0aW9uYWxpdHlcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIHRoaXMuX2hhbmRsZUJ1bGtBY3Rpb25DaGVja2JveFNlbGVjdChncmlkKTtcbiAgICB0aGlzLl9oYW5kbGVCdWxrQWN0aW9uU2VsZWN0QWxsQ2hlY2tib3goZ3JpZCk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlcyBcIlNlbGVjdCBhbGxcIiBidXR0b24gaW4gdGhlIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGFuZGxlQnVsa0FjdGlvblNlbGVjdEFsbENoZWNrYm94KGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjaGFuZ2UnLCAnLmpzLWJ1bGstYWN0aW9uLXNlbGVjdC1hbGwnLCAoZSkgPT4ge1xuICAgICAgY29uc3QgJGNoZWNrYm94ID0gJChlLmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICBjb25zdCBpc0NoZWNrZWQgPSAkY2hlY2tib3guaXMoJzpjaGVja2VkJyk7XG4gICAgICBpZiAoaXNDaGVja2VkKSB7XG4gICAgICAgIHRoaXMuX2VuYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGhpcy5fZGlzYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfVxuXG4gICAgICBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbi1jaGVja2JveCcpLnByb3AoJ2NoZWNrZWQnLCBpc0NoZWNrZWQpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZXMgZWFjaCBidWxrIGFjdGlvbiBjaGVja2JveCBzZWxlY3QgaW4gdGhlIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0KGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjaGFuZ2UnLCAnLmpzLWJ1bGstYWN0aW9uLWNoZWNrYm94JywgKCkgPT4ge1xuICAgICAgY29uc3QgY2hlY2tlZFJvd3NDb3VudCA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9uLWNoZWNrYm94OmNoZWNrZWQnKS5sZW5ndGg7XG5cbiAgICAgIGlmIChjaGVja2VkUm93c0NvdW50ID4gMCkge1xuICAgICAgICB0aGlzLl9lbmFibGVCdWxrQWN0aW9uc0J0bihncmlkKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHRoaXMuX2Rpc2FibGVCdWxrQWN0aW9uc0J0bihncmlkKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbmFibGUgYnVsayBhY3Rpb25zIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9lbmFibGVCdWxrQWN0aW9uc0J0bihncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb25zLWJ0bicpLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICB9XG5cbiAgLyoqXG4gICAqIERpc2FibGUgYnVsayBhY3Rpb25zIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9kaXNhYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9ucy1idG4nKS5wcm9wKCdkaXNhYmxlZCcsIHRydWUpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2J1bGstYWN0aW9uLWNoZWNrYm94LWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBIYW5kbGVzIHN1Ym1pdCBvZiBncmlkIGFjdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU3VibWl0QnVsa0FjdGlvbkV4dGVuc2lvbiB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHJldHVybiB7XG4gICAgICBleHRlbmQ6IChncmlkKSA9PiB0aGlzLmV4dGVuZChncmlkKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkIHdpdGggYnVsayBhY3Rpb24gc3VibWl0dGluZ1xuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWJ1bGstYWN0aW9uLXN1Ym1pdC1idG4nLCAoZXZlbnQpID0+IHtcbiAgICAgIHRoaXMuc3VibWl0KGV2ZW50LCBncmlkKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGUgYnVsayBhY3Rpb24gc3VibWl0dGluZ1xuICAgKlxuICAgKiBAcGFyYW0ge0V2ZW50fSBldmVudFxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHN1Ym1pdChldmVudCwgZ3JpZCkge1xuICAgIGNvbnN0ICRzdWJtaXRCdG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJHN1Ym1pdEJ0bi5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcblxuICAgIGlmICh0eXBlb2YgY29uZmlybU1lc3NhZ2UgIT09IFwidW5kZWZpbmVkXCIgJiYgMCA8IGNvbmZpcm1NZXNzYWdlLmxlbmd0aCAmJiAhY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCAkZm9ybSA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19maWx0ZXJfZm9ybScpO1xuXG4gICAgJGZvcm0uYXR0cignYWN0aW9uJywgJHN1Ym1pdEJ0bi5kYXRhKCdmb3JtLXVybCcpKTtcbiAgICAkZm9ybS5hdHRyKCdtZXRob2QnLCAkc3VibWl0QnRuLmRhdGEoJ2Zvcm0tbWV0aG9kJykpO1xuICAgICRmb3JtLnN1Ym1pdCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1idWxrLWFjdGlvbi1leHRlbnNpb24uanMiXSwic291cmNlUm9vdCI6IiJ9