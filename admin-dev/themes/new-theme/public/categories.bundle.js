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
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
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
/******/ 	__webpack_require__.p = "/admin-dev/themes/new-theme/public/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/pages/categories/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/app/utils/reset_search.js":
/*!**************************************!*\
  !*** ./js/app/utils/reset_search.js ***!
  \**************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/**
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
 * Send a Post Request to reset search Action.
 */
var $ = global.$;

var init = function resetSearch(url, redirectUrl) {
  $.post(url).then(function () {
    return window.location.assign(redirectUrl);
  });
};

/* harmony default export */ __webpack_exports__["default"] = (init);
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./js/app/utils/table-sorting.js":
/*!***************************************!*\
  !*** ./js/app/utils/table-sorting.js ***!
  \***************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
var $ = global.$;
/**
 * Makes a table sortable by columns.
 * This forces a page reload with more query parameters.
 */

var TableSorting =
/*#__PURE__*/
function () {
  /**
   * @param {jQuery} table
   */
  function TableSorting(table) {
    _classCallCheck(this, TableSorting);

    this.selector = '.ps-sortable-column';
    this.columns = $(table).find(this.selector);
  }
  /**
   * Attaches the listeners
   */


  _createClass(TableSorting, [{
    key: "attach",
    value: function attach() {
      var _this = this;

      this.columns.on('click', function (e) {
        var $column = $(e.delegateTarget);

        _this._sortByColumn($column, _this._getToggledSortDirection($column));
      });
    }
    /**
     * Sort using a column name
     * @param {string} columnName
     * @param {string} direction "asc" or "desc"
     */

  }, {
    key: "sortBy",
    value: function sortBy(columnName, direction) {
      var $column = this.columns.is("[data-sort-col-name=\"".concat(columnName, "\"]"));

      if (!$column) {
        throw new Error("Cannot sort by \"".concat(columnName, "\": invalid column"));
      }

      this._sortByColumn($column, direction);
    }
    /**
     * Sort using a column element
     * @param {jQuery} column
     * @param {string} direction "asc" or "desc"
     * @private
     */

  }, {
    key: "_sortByColumn",
    value: function _sortByColumn(column, direction) {
      window.location = this._getUrl(column.data('sortColName'), direction === 'desc' ? 'desc' : 'asc');
    }
    /**
     * Returns the inverted direction to sort according to the column's current one
     * @param {jQuery} column
     * @return {string}
     * @private
     */

  }, {
    key: "_getToggledSortDirection",
    value: function _getToggledSortDirection(column) {
      return column.data('sortDirection') === 'asc' ? 'desc' : 'asc';
    }
    /**
     * Returns the url for the sorted table
     * @param {string} colName
     * @param {string} direction
     * @return {string}
     * @private
     */

  }, {
    key: "_getUrl",
    value: function _getUrl(colName, direction) {
      var url = new URL(window.location.href);
      var params = url.searchParams;
      params.set('orderBy', colName);
      params.set('sortOrder', direction);
      return url.toString();
    }
  }]);

  return TableSorting;
}();

/* harmony default export */ __webpack_exports__["default"] = (TableSorting);
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./js/components/choice-table.js":
/*!***************************************!*\
  !*** ./js/components/choice-table.js ***!
  \***************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ChoiceTable; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
 * ChoiceTable is responsible for managing common actions in choice table form type
 */

var ChoiceTable =
/*#__PURE__*/
function () {
  /**
   * Init constructor
   */
  function ChoiceTable() {
    var _this = this;

    _classCallCheck(this, ChoiceTable);

    $(document).on('change', '.js-choice-table-select-all', function (e) {
      _this.handleSelectAll(e);
    });
  }
  /**
   * Check/uncheck all boxes in table
   *
   * @param {Event} event
   */


  _createClass(ChoiceTable, [{
    key: "handleSelectAll",
    value: function handleSelectAll(event) {
      var $selectAllCheckboxes = $(event.target);
      var isSelectAllChecked = $selectAllCheckboxes.is(':checked');
      $selectAllCheckboxes.closest('table').find('tbody input:checkbox').prop('checked', isSelectAllChecked);
    }
  }]);

  return ChoiceTable;
}();



/***/ }),

/***/ "./js/components/form-submit-button.js":
/*!*********************************************!*\
  !*** ./js/components/form-submit-button.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return FormSubmitButton; });
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
 * Component which allows submitting very simple forms without having to use <form> element.
 *
 * Useful when performing actions on resource where URL contains all needed data.
 * For example, to toggle category status via "POST /categories/2/toggle-status)"
 * or delete cover image via "POST /categories/2/delete-cover-image".
 *
 * Usage example in template:
 *
 * <button class="js-form-submit-btn"
 *         data-form-submit-url="/my-custom-url"          // (required) URL to which form will be submitted
 *         data-form-csrf-token="my-generated-csrf-token" // (optional) to increase security
 *         type="button"                                  // make sure its simple button
 *                                                        // so we can avoid submitting actual form
 *                                                        // when our button is defined inside form
 * >
 *     Click me to submit form
 * </button>
 *
 * In page specific JS you have to enable this feature:
 *
 * new FormSubmitButton();
 */

var FormSubmitButton = function FormSubmitButton() {
  _classCallCheck(this, FormSubmitButton);

  $(document).on('click', '.js-form-submit-btn', function (event) {
    event.preventDefault();
    var $btn = $(this);
    var $form = $('<form>', {
      'action': $btn.data('form-submit-url'),
      'method': 'POST'
    });

    if ($btn.data('form-csrf-token')) {
      $form.append($('<input>', {
        'type': '_hidden',
        'name': '_csrf_token',
        'value': $btn.data('form-csrf-token')
      }));
    }

    $form.appendTo('body').submit();
  });
};



/***/ }),

/***/ "./js/components/form/choice-tree.js":
/*!*******************************************!*\
  !*** ./js/components/form/choice-tree.js ***!
  \*******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ChoiceTree; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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

var ChoiceTree =
/*#__PURE__*/
function () {
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
      }
    };
  }
  /**
   * Enable automatic check/uncheck of clicked item's children.
   */


  _createClass(ChoiceTree, [{
    key: "enableAutoCheckChildren",
    value: function enableAutoCheckChildren() {
      this.$container.on('change', 'input[type="checkbox"]', function (event) {
        var $clickedCheckbox = $(event.currentTarget);
        var $itemWithChildren = $clickedCheckbox.closest('li');
        $itemWithChildren.find('ul input[type="checkbox"]').prop('checked', $clickedCheckbox.is(':checked'));
      });
    }
    /**
     * Collapse or expand sub-tree for single parent
     *
     * @param {jQuery} $inputWrapper
     *
     * @private
     */

  }, {
    key: "_toggleChildTree",
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
    key: "_toggleTree",
    value: function _toggleTree($action) {
      var $parentContainer = $action.closest('.js-choice-tree-container');
      var action = $action.data('action'); // toggle action configuration

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



/***/ }),

/***/ "./js/components/form/text-with-length-counter.js":
/*!********************************************************!*\
  !*** ./js/components/form/text-with-length-counter.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return TextWithLengthCounter; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
 * TextWithLengthCounter handles input with length counter UI.
 */

var TextWithLengthCounter =
/*#__PURE__*/
function () {
  function TextWithLengthCounter() {
    _classCallCheck(this, TextWithLengthCounter);

    $(document).on('input', '.js-text-with-counter-input-group input[type="text"]', function (e) {
      var $input = $(e.currentTarget);
      var remainingLength = $input.data('max-length') - $input.val().length;
      $input.closest('.js-text-with-counter-input-group').find('.js-counter-text').text(remainingLength);
    });
  }
  /**
   * Check/uncheck all boxes in table
   *
   * @param {Event} event
   */


  _createClass(TextWithLengthCounter, [{
    key: "handleSelectAll",
    value: function handleSelectAll(event) {
      var $selectAllCheckboxes = $(event.target);
      var isSelectAllChecked = $selectAllCheckboxes.is(':checked');
      $selectAllCheckboxes.closest('table').find('tbody input:checkbox').prop('checked', isSelectAllChecked);
    }
  }]);

  return TextWithLengthCounter;
}();



/***/ }),

/***/ "./js/components/grid/extension/action/bulk/category/delete-categories-bulk-action-extension.js":
/*!******************************************************************************************************!*\
  !*** ./js/components/grid/extension/action/bulk/category/delete-categories-bulk-action-extension.js ***!
  \******************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return DeleteCategoriesBulkActionExtension; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
 * Class DeleteCategoriesBulkActionExtension handles submitting of row action
 */

var DeleteCategoriesBulkActionExtension =
/*#__PURE__*/
function () {
  function DeleteCategoriesBulkActionExtension() {
    var _this = this;

    _classCallCheck(this, DeleteCategoriesBulkActionExtension);

    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */


  _createClass(DeleteCategoriesBulkActionExtension, [{
    key: "extend",
    value: function extend(grid) {
      grid.getContainer().on('click', '.js-delete-categories-bulk-action', function (event) {
        event.preventDefault();
        var submitUrl = $(event.currentTarget).data('categories-delete-url');
        var $deleteCategoriesModal = $("#".concat(grid.getId(), "_grid_delete_categories_modal"));
        $deleteCategoriesModal.modal('show');
        $deleteCategoriesModal.on('click', '.js-submit-delete-categories', function () {
          var $checkboxes = grid.getContainer().find('.js-bulk-action-checkbox');
          var $categoriesToDeleteInputBlock = $('#delete_categories_categories_to_delete');
          $checkboxes.each(function (i, value) {
            var $input = $(value);
            var categoryInput = $categoriesToDeleteInputBlock.data('prototype').replace(/__name__/g, $input.val());
            var $item = $($.parseHTML(categoryInput)[0]);
            $item.val($input.val());
            $categoriesToDeleteInputBlock.append($item);
          });
          var $form = $deleteCategoriesModal.find('form');
          $form.attr('action', submitUrl);
          $form.submit();
        });
      });
    }
  }]);

  return DeleteCategoriesBulkActionExtension;
}();



/***/ }),

/***/ "./js/components/grid/extension/action/row/category/delete-category-row-action-extension.js":
/*!**************************************************************************************************!*\
  !*** ./js/components/grid/extension/action/row/category/delete-category-row-action-extension.js ***!
  \**************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return DeleteCategoryRowActionExtension; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
 * Class CategoryDeleteRowActionExtension handles submitting of row action
 */

var DeleteCategoryRowActionExtension =
/*#__PURE__*/
function () {
  function DeleteCategoryRowActionExtension() {
    var _this = this;

    _classCallCheck(this, DeleteCategoryRowActionExtension);

    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */


  _createClass(DeleteCategoryRowActionExtension, [{
    key: "extend",
    value: function extend(grid) {
      grid.getContainer().on('click', '.js-delete-category-row-action', function (event) {
        event.preventDefault();
        var $deleteCategoriesModal = $('#' + grid.getId() + '_grid_delete_categories_modal');
        $deleteCategoriesModal.modal('show');
        $deleteCategoriesModal.on('click', '.js-submit-delete-categories', function () {
          var $button = $(event.currentTarget);
          var categoryId = $button.data('category-id');
          var $categoriesToDeleteInputBlock = $('#delete_categories_categories_to_delete');
          var categoryInput = $categoriesToDeleteInputBlock.data('prototype').replace(/__name__/g, $categoriesToDeleteInputBlock.children().length);
          var $item = $($.parseHTML(categoryInput)[0]);
          $item.val(categoryId);
          $categoriesToDeleteInputBlock.append($item);
          var $form = $deleteCategoriesModal.find('form');
          $form.attr('action', $button.data('category-delete-url'));
          $form.submit();
        });
      });
    }
  }]);

  return DeleteCategoryRowActionExtension;
}();



/***/ }),

/***/ "./js/components/grid/extension/action/row/submit-row-action-extension.js":
/*!********************************************************************************!*\
  !*** ./js/components/grid/extension/action/row/submit-row-action-extension.js ***!
  \********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return SubmitRowActionExtension; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
 * Class SubmitRowActionExtension handles submitting of row action
 */

var SubmitRowActionExtension =
/*#__PURE__*/
function () {
  function SubmitRowActionExtension() {
    _classCallCheck(this, SubmitRowActionExtension);
  }

  _createClass(SubmitRowActionExtension, [{
    key: "extend",

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



/***/ }),

/***/ "./js/components/grid/extension/bulk-action-checkbox-extension.js":
/*!************************************************************************!*\
  !*** ./js/components/grid/extension/bulk-action-checkbox-extension.js ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return BulkActionCheckboxExtension; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
 * Class BulkActionSelectCheckboxExtension
 */

var BulkActionCheckboxExtension =
/*#__PURE__*/
function () {
  function BulkActionCheckboxExtension() {
    _classCallCheck(this, BulkActionCheckboxExtension);
  }

  _createClass(BulkActionCheckboxExtension, [{
    key: "extend",

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
    key: "_handleBulkActionSelectAllCheckbox",
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
    key: "_handleBulkActionCheckboxSelect",
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
    key: "_enableBulkActionsBtn",
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
    key: "_disableBulkActionsBtn",
    value: function _disableBulkActionsBtn(grid) {
      grid.getContainer().find('.js-bulk-actions-btn').prop('disabled', true);
    }
  }]);

  return BulkActionCheckboxExtension;
}();



/***/ }),

/***/ "./js/components/grid/extension/column/catalog/category-position-extension.js":
/*!************************************************************************************!*\
  !*** ./js/components/grid/extension/column/catalog/category-position-extension.js ***!
  \************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return CategoryPositionExtension; });
/* harmony import */ var tablednd_dist_jquery_tablednd_min__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tablednd/dist/jquery.tablednd.min */ "./node_modules/tablednd/dist/jquery.tablednd.min.js");
/* harmony import */ var tablednd_dist_jquery_tablednd_min__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tablednd_dist_jquery_tablednd_min__WEBPACK_IMPORTED_MODULE_0__);
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
 * Class CategoryPositionExtension extends Grid with reorderable category positions
 */

var CategoryPositionExtension =
/*#__PURE__*/
function () {
  function CategoryPositionExtension() {
    var _this = this;

    _classCallCheck(this, CategoryPositionExtension);

    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */


  _createClass(CategoryPositionExtension, [{
    key: "extend",
    value: function extend(grid) {
      var _this2 = this;

      this.grid = grid;

      this._addIdsToGridTableRows();

      grid.getContainer().find('.js-grid-table').tableDnD({
        dragHandle: '.js-drag-handle',
        onDragStart: function onDragStart() {
          _this2.originalPositions = decodeURIComponent($.tableDnD.serialize());
        },
        onDrop: function onDrop(table, row) {
          return _this2._handleCategoryPositionChange(row);
        }
      });
    }
    /**
     * When position is changed handle update
     *
     * @param {HTMLElement} row
     *
     * @private
     */

  }, {
    key: "_handleCategoryPositionChange",
    value: function _handleCategoryPositionChange(row) {
      var positions = decodeURIComponent($.tableDnD.serialize());
      var way = this.originalPositions.indexOf(row.id) < positions.indexOf(row.id) ? 1 : 0;
      var $categoryPositionContainer = $(row).find('.js-' + this.grid.getId() + '-position:first');
      var categoryId = $categoryPositionContainer.data('id');
      var categoryParentId = $categoryPositionContainer.data('id-parent');
      var positionUpdateUrl = $categoryPositionContainer.data('position-update-url');
      var params = positions.replace(new RegExp(this.grid.getId() + '_grid_table', 'g'), 'category');
      var queryParams = {
        id_category_parent: categoryParentId,
        id_category_to_move: categoryId,
        way: way,
        ajax: 1,
        action: 'updatePositions'
      };

      if (positions.indexOf('_0&') !== -1) {
        queryParams.found_first = 1;
      }

      params += '&' + $.param(queryParams);

      this._updateCategoryPosition(positionUpdateUrl, params);
    }
    /**
     * Add ID's to Grid table rows to make tableDnD.onDrop() function work.
     *
     * @private
     */

  }, {
    key: "_addIdsToGridTableRows",
    value: function _addIdsToGridTableRows() {
      this.grid.getContainer().find('.js-grid-table').find('.js-' + this.grid.getId() + '-position').each(function (index, positionWrapper) {
        var $positionWrapper = $(positionWrapper);
        var categoryId = $positionWrapper.data('id');
        var categoryParentId = $positionWrapper.data('id-parent');
        var position = $positionWrapper.data('position');
        var id = 'tr_' + categoryParentId + '_' + categoryId + '_' + position;
        $positionWrapper.closest('tr').attr('id', id);
      });
    }
    /**
     * Update categories listing with new positions
     *
     * @private
     */

  }, {
    key: "_updateCategoryIdsAndPositions",
    value: function _updateCategoryIdsAndPositions() {
      this.grid.getContainer().find('.js-grid-table').find('.js-' + this.grid.getId() + '-position').each(function (index, positionWrapper) {
        var $positionWrapper = $(positionWrapper);
        var $row = $positionWrapper.closest('tr');
        var offset = $positionWrapper.data('pagination-offset');
        var newPosition = offset > 0 ? index + offset : index;
        var oldId = $row.attr('id');
        $row.attr('id', oldId.replace(/_[0-9]$/g, '_' + newPosition));
        $positionWrapper.find('.js-position').text(newPosition + 1);
        $positionWrapper.data('position', newPosition);
      });
    }
    /**
     * Process categories positions update
     *
     * @param {String} url
     * @param {String} params
     *
     * @private
     */

  }, {
    key: "_updateCategoryPosition",
    value: function _updateCategoryPosition(url, params) {
      var _this3 = this;

      $.post({
        url: url,
        headers: {
          'cache-control': 'no-cache'
        },
        data: params
      }).then(function (response) {
        response = JSON.parse(response);

        if (typeof response.message !== 'undefined') {
          showSuccessMessage(response.message);
        } else {
          // use legacy error
          // @todo: update when all category controller is migrated to symfony
          showErrorMessage(response.errors);
        }

        _this3._updateCategoryIdsAndPositions();
      });
    }
  }]);

  return CategoryPositionExtension;
}();



/***/ }),

/***/ "./js/components/grid/extension/column/common/async-toggle-column-extension.js":
/*!*************************************************************************************!*\
  !*** ./js/components/grid/extension/column/common/async-toggle-column-extension.js ***!
  \*************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return AsyncToggleColumnExtension; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
 * Class AsyncToggleColumnExtension submits toggle action using AJAX
 */

var AsyncToggleColumnExtension =
/*#__PURE__*/
function () {
  function AsyncToggleColumnExtension() {
    var _this = this;

    _classCallCheck(this, AsyncToggleColumnExtension);

    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */


  _createClass(AsyncToggleColumnExtension, [{
    key: "extend",
    value: function extend(grid) {
      var _this2 = this;

      grid.getContainer().find('.js-grid-table').on('click', '.ps-togglable-row', function (event) {
        event.preventDefault();
        var $button = $(event.currentTarget);
        $.post({
          url: $button.data('toggle-url')
        }).then(function (response) {
          if (response.status) {
            showSuccessMessage(response.message);

            _this2._toggleButtonDisplay($button);

            return;
          }

          showErrorMessage(response.message);
        });
      });
    }
    /**
     * Toggle button display from enabled to disabled and other way around
     *
     * @param {jQuery} $button
     *
     * @private
     */

  }, {
    key: "_toggleButtonDisplay",
    value: function _toggleButtonDisplay($button) {
      var isActive = $button.hasClass('grid-toggler-icon-valid');
      var classToAdd = isActive ? 'grid-toggler-icon-not-valid' : 'grid-toggler-icon-valid';
      var classToRemove = isActive ? 'grid-toggler-icon-valid' : 'grid-toggler-icon-not-valid';
      var icon = isActive ? 'clear' : 'check';
      $button.removeClass(classToRemove);
      $button.addClass(classToAdd);
      $button.text(icon);
    }
  }]);

  return AsyncToggleColumnExtension;
}();



/***/ }),

/***/ "./js/components/grid/extension/export-to-sql-manager-extension.js":
/*!*************************************************************************!*\
  !*** ./js/components/grid/extension/export-to-sql-manager-extension.js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ExportToSqlManagerExtension; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
 * Class ExportToSqlManagerExtension extends grid with exporting query to SQL Manager
 */

var ExportToSqlManagerExtension =
/*#__PURE__*/
function () {
  function ExportToSqlManagerExtension() {
    _classCallCheck(this, ExportToSqlManagerExtension);
  }

  _createClass(ExportToSqlManagerExtension, [{
    key: "extend",

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
    key: "_onShowSqlQueryClick",
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
    key: "_onExportSqlManagerClick",
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
    key: "_fillExportForm",
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
    key: "_getNameFromBreadcrumb",
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



/***/ }),

/***/ "./js/components/grid/extension/filters-reset-extension.js":
/*!*****************************************************************!*\
  !*** ./js/components/grid/extension/filters-reset-extension.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return FiltersResetExtension; });
/* harmony import */ var _app_utils_reset_search__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../app/utils/reset_search */ "./js/app/utils/reset_search.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
 * Class FiltersResetExtension extends grid with filters resetting
 */

var FiltersResetExtension =
/*#__PURE__*/
function () {
  function FiltersResetExtension() {
    _classCallCheck(this, FiltersResetExtension);
  }

  _createClass(FiltersResetExtension, [{
    key: "extend",

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      grid.getContainer().on('click', '.js-reset-search', function (event) {
        Object(_app_utils_reset_search__WEBPACK_IMPORTED_MODULE_0__["default"])($(event.currentTarget).data('url'), $(event.currentTarget).data('redirect'));
      });
    }
  }]);

  return FiltersResetExtension;
}();



/***/ }),

/***/ "./js/components/grid/extension/link-row-action-extension.js":
/*!*******************************************************************!*\
  !*** ./js/components/grid/extension/link-row-action-extension.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return LinkRowActionExtension; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
 * Class LinkRowActionExtension handles link row actions
 */

var LinkRowActionExtension =
/*#__PURE__*/
function () {
  function LinkRowActionExtension() {
    _classCallCheck(this, LinkRowActionExtension);
  }

  _createClass(LinkRowActionExtension, [{
    key: "extend",

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      grid.getContainer().on('click', '.js-link-row-action', function (event) {
        var confirmMessage = $(event.currentTarget).data('confirm-message');

        if (confirmMessage.length && !confirm(confirmMessage)) {
          event.preventDefault();
        }
      });
    }
  }]);

  return LinkRowActionExtension;
}();



/***/ }),

/***/ "./js/components/grid/extension/reload-list-extension.js":
/*!***************************************************************!*\
  !*** ./js/components/grid/extension/reload-list-extension.js ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ReloadListExtension; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
 * Class ReloadListExtension extends grid with "List reload" action
 */
var ReloadListExtension =
/*#__PURE__*/
function () {
  function ReloadListExtension() {
    _classCallCheck(this, ReloadListExtension);
  }

  _createClass(ReloadListExtension, [{
    key: "extend",

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



/***/ }),

/***/ "./js/components/grid/extension/sorting-extension.js":
/*!***********************************************************!*\
  !*** ./js/components/grid/extension/sorting-extension.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return SortingExtension; });
/* harmony import */ var _app_utils_table_sorting__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../app/utils/table-sorting */ "./js/app/utils/table-sorting.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
 * Class ReloadListExtension extends grid with "List reload" action
 */

var SortingExtension =
/*#__PURE__*/
function () {
  function SortingExtension() {
    _classCallCheck(this, SortingExtension);
  }

  _createClass(SortingExtension, [{
    key: "extend",

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var $sortableTable = grid.getContainer().find('table.table');
      new _app_utils_table_sorting__WEBPACK_IMPORTED_MODULE_0__["default"]($sortableTable).attach();
    }
  }]);

  return SortingExtension;
}();



/***/ }),

/***/ "./js/components/grid/extension/submit-bulk-action-extension.js":
/*!**********************************************************************!*\
  !*** ./js/components/grid/extension/submit-bulk-action-extension.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return SubmitBulkActionExtension; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
 * Handles submit of grid actions
 */

var SubmitBulkActionExtension =
/*#__PURE__*/
function () {
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
    key: "extend",
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
    key: "submit",
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



/***/ }),

/***/ "./js/components/grid/grid.js":
/*!************************************!*\
  !*** ./js/components/grid/grid.js ***!
  \************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Grid; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
 * Class is responsible for handling Grid events
 */

var Grid =
/*#__PURE__*/
function () {
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
    key: "getId",
    value: function getId() {
      return this.id;
    }
    /**
     * Get grid container
     *
     * @returns {jQuery}
     */

  }, {
    key: "getContainer",
    value: function getContainer() {
      return this.$container;
    }
    /**
     * Get grid header container
     *
     * @returns {jQuery}
     */

  }, {
    key: "getHeaderContainer",
    value: function getHeaderContainer() {
      return this.$container.closest('.js-grid-panel').find('.js-grid-header');
    }
    /**
     * Extend grid with external extensions
     *
     * @param {object} extension
     */

  }, {
    key: "addExtension",
    value: function addExtension(extension) {
      extension.extend(this);
    }
  }]);

  return Grid;
}();



/***/ }),

/***/ "./js/components/translatable-input.js":
/*!*********************************************!*\
  !*** ./js/components/translatable-input.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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

var TranslatableInput =
/*#__PURE__*/
function () {
  function TranslatableInput(options) {
    _classCallCheck(this, TranslatableInput);

    options = options || {};
    this.localeItemSelector = options.localeItemSelector || '.js-locale-item';
    this.localeButtonSelector = options.localeButtonSelector || '.js-locale-btn';
    this.localeInputSelector = options.localeInputSelector || '.js-locale-input';
    $('body').on('click', this.localeItemSelector, this.toggleInputs.bind(this));
  }
  /**
   * Toggle all translatable inputs in form in which locale was changed
   *
   * @param {Event} event
   */


  _createClass(TranslatableInput, [{
    key: "toggleInputs",
    value: function toggleInputs(event) {
      var localeItem = $(event.target);
      var form = localeItem.closest('form');
      var selectedLocale = localeItem.data('locale');
      form.find(this.localeButtonSelector).text(selectedLocale);
      form.find(this.localeInputSelector).addClass('d-none');
      form.find(this.localeInputSelector + '.js-locale-' + selectedLocale).removeClass('d-none');
    }
  }]);

  return TranslatableInput;
}();

/* harmony default export */ __webpack_exports__["default"] = (TranslatableInput);

/***/ }),

/***/ "./js/pages/categories/index.js":
/*!**************************************!*\
  !*** ./js/pages/categories/index.js ***!
  \**************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_grid_grid__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/grid/grid */ "./js/components/grid/grid.js");
/* harmony import */ var _components_grid_extension_filters_reset_extension__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../components/grid/extension/filters-reset-extension */ "./js/components/grid/extension/filters-reset-extension.js");
/* harmony import */ var _components_grid_extension_sorting_extension__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/grid/extension/sorting-extension */ "./js/components/grid/extension/sorting-extension.js");
/* harmony import */ var _components_grid_extension_export_to_sql_manager_extension__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../components/grid/extension/export-to-sql-manager-extension */ "./js/components/grid/extension/export-to-sql-manager-extension.js");
/* harmony import */ var _components_grid_extension_reload_list_extension__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../components/grid/extension/reload-list-extension */ "./js/components/grid/extension/reload-list-extension.js");
/* harmony import */ var _components_grid_extension_bulk_action_checkbox_extension__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../components/grid/extension/bulk-action-checkbox-extension */ "./js/components/grid/extension/bulk-action-checkbox-extension.js");
/* harmony import */ var _components_grid_extension_submit_bulk_action_extension__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../components/grid/extension/submit-bulk-action-extension */ "./js/components/grid/extension/submit-bulk-action-extension.js");
/* harmony import */ var _components_grid_extension_action_row_submit_row_action_extension__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../components/grid/extension/action/row/submit-row-action-extension */ "./js/components/grid/extension/action/row/submit-row-action-extension.js");
/* harmony import */ var _components_grid_extension_link_row_action_extension__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../components/grid/extension/link-row-action-extension */ "./js/components/grid/extension/link-row-action-extension.js");
/* harmony import */ var _components_grid_extension_column_catalog_category_position_extension__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../components/grid/extension/column/catalog/category-position-extension */ "./js/components/grid/extension/column/catalog/category-position-extension.js");
/* harmony import */ var _components_grid_extension_column_common_async_toggle_column_extension__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../components/grid/extension/column/common/async-toggle-column-extension */ "./js/components/grid/extension/column/common/async-toggle-column-extension.js");
/* harmony import */ var _components_grid_extension_action_row_category_delete_category_row_action_extension__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../../components/grid/extension/action/row/category/delete-category-row-action-extension */ "./js/components/grid/extension/action/row/category/delete-category-row-action-extension.js");
/* harmony import */ var _components_grid_extension_action_bulk_category_delete_categories_bulk_action_extension__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../../components/grid/extension/action/bulk/category/delete-categories-bulk-action-extension */ "./js/components/grid/extension/action/bulk/category/delete-categories-bulk-action-extension.js");
/* harmony import */ var _components_translatable_input__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../../components/translatable-input */ "./js/components/translatable-input.js");
/* harmony import */ var _components_choice_table__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ../../components/choice-table */ "./js/components/choice-table.js");
/* harmony import */ var _components_form_text_with_length_counter__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ../../components/form/text-with-length-counter */ "./js/components/form/text-with-length-counter.js");
/* harmony import */ var _name_to_link_rewrite_copier__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./name-to-link-rewrite-copier */ "./js/pages/categories/name-to-link-rewrite-copier.js");
/* harmony import */ var _components_form_choice_tree__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ../../components/form/choice-tree */ "./js/components/form/choice-tree.js");
/* harmony import */ var _components_form_submit_button__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ../../components/form-submit-button */ "./js/components/form-submit-button.js");
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
  var categoriesGrid = new _components_grid_grid__WEBPACK_IMPORTED_MODULE_0__["default"]('categories');
  categoriesGrid.addExtension(new _components_grid_extension_filters_reset_extension__WEBPACK_IMPORTED_MODULE_1__["default"]());
  categoriesGrid.addExtension(new _components_grid_extension_sorting_extension__WEBPACK_IMPORTED_MODULE_2__["default"]());
  categoriesGrid.addExtension(new _components_grid_extension_column_catalog_category_position_extension__WEBPACK_IMPORTED_MODULE_9__["default"]());
  categoriesGrid.addExtension(new _components_grid_extension_export_to_sql_manager_extension__WEBPACK_IMPORTED_MODULE_3__["default"]());
  categoriesGrid.addExtension(new _components_grid_extension_reload_list_extension__WEBPACK_IMPORTED_MODULE_4__["default"]());
  categoriesGrid.addExtension(new _components_grid_extension_bulk_action_checkbox_extension__WEBPACK_IMPORTED_MODULE_5__["default"]());
  categoriesGrid.addExtension(new _components_grid_extension_submit_bulk_action_extension__WEBPACK_IMPORTED_MODULE_6__["default"]());
  categoriesGrid.addExtension(new _components_grid_extension_action_row_submit_row_action_extension__WEBPACK_IMPORTED_MODULE_7__["default"]());
  categoriesGrid.addExtension(new _components_grid_extension_link_row_action_extension__WEBPACK_IMPORTED_MODULE_8__["default"]());
  categoriesGrid.addExtension(new _components_grid_extension_column_common_async_toggle_column_extension__WEBPACK_IMPORTED_MODULE_10__["default"]());
  categoriesGrid.addExtension(new _components_grid_extension_action_row_category_delete_category_row_action_extension__WEBPACK_IMPORTED_MODULE_11__["default"]());
  categoriesGrid.addExtension(new _components_grid_extension_action_bulk_category_delete_categories_bulk_action_extension__WEBPACK_IMPORTED_MODULE_12__["default"]());
  new _components_translatable_input__WEBPACK_IMPORTED_MODULE_13__["default"]();
  new _components_choice_table__WEBPACK_IMPORTED_MODULE_14__["default"]();
  new _components_form_text_with_length_counter__WEBPACK_IMPORTED_MODULE_15__["default"]();
  new _name_to_link_rewrite_copier__WEBPACK_IMPORTED_MODULE_16__["default"]();
  new _components_form_submit_button__WEBPACK_IMPORTED_MODULE_18__["default"]();
  new _components_form_choice_tree__WEBPACK_IMPORTED_MODULE_17__["default"]('#category_id_parent');
  new _components_form_choice_tree__WEBPACK_IMPORTED_MODULE_17__["default"]('#category_shop_association').enableAutoCheckChildren();
  new _components_form_choice_tree__WEBPACK_IMPORTED_MODULE_17__["default"]('#root_category_id_parent');
  new _components_form_choice_tree__WEBPACK_IMPORTED_MODULE_17__["default"]('#root_category_shop_association').enableAutoCheckChildren();
});

/***/ }),

/***/ "./js/pages/categories/name-to-link-rewrite-copier.js":
/*!************************************************************!*\
  !*** ./js/pages/categories/name-to-link-rewrite-copier.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function($) {/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return NameToLinkRewriteCopier; });
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
 * Copies name of category to link rewrite input.
 */
var NameToLinkRewriteCopier = function NameToLinkRewriteCopier() {
  _classCallCheck(this, NameToLinkRewriteCopier);

  ['category', 'root_category'].forEach(function (categoryType) {
    var $categoryForm = $("form[name=\"".concat(categoryType, "\"]"));

    if (0 === $categoryForm.length) {
      return;
    }

    $categoryForm.on('input', "input[name^=\"".concat(categoryType, "[name]\"]"), function (event) {
      var $nameInput = $(event.currentTarget);
      var langId = $nameInput.closest('.js-locale-input').data('lang-id');
      $categoryForm.find("input[name=\"".concat(categoryType, "[link_rewrite][").concat(langId, "]\"]")).val(str2url($nameInput.val(), 'UTF-8'));
    });
  });
};


/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "jquery")))

/***/ }),

/***/ "./node_modules/tablednd/dist/jquery.tablednd.min.js":
/*!***********************************************************!*\
  !*** ./node_modules/tablednd/dist/jquery.tablednd.min.js ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(jQuery) {/*! jquery.tablednd.js 30-12-2017 */
!function(a,b,c,d){var e="touchstart mousedown",f="touchmove mousemove",g="touchend mouseup";a(c).ready(function(){function b(a){for(var b={},c=a.match(/([^;:]+)/g)||[];c.length;)b[c.shift()]=c.shift().trim();return b}a("table").each(function(){"dnd"===a(this).data("table")&&a(this).tableDnD({onDragStyle:a(this).data("ondragstyle")&&b(a(this).data("ondragstyle"))||null,onDropStyle:a(this).data("ondropstyle")&&b(a(this).data("ondropstyle"))||null,onDragClass:a(this).data("ondragclass")===d&&"tDnD_whileDrag"||a(this).data("ondragclass"),onDrop:a(this).data("ondrop")&&new Function("table","row",a(this).data("ondrop")),onDragStart:a(this).data("ondragstart")&&new Function("table","row",a(this).data("ondragstart")),onDragStop:a(this).data("ondragstop")&&new Function("table","row",a(this).data("ondragstop")),scrollAmount:a(this).data("scrollamount")||5,sensitivity:a(this).data("sensitivity")||10,hierarchyLevel:a(this).data("hierarchylevel")||0,indentArtifact:a(this).data("indentartifact")||'<div class="indent">&nbsp;</div>',autoWidthAdjust:a(this).data("autowidthadjust")||!0,autoCleanRelations:a(this).data("autocleanrelations")||!0,jsonPretifySeparator:a(this).data("jsonpretifyseparator")||"\t",serializeRegexp:a(this).data("serializeregexp")&&new RegExp(a(this).data("serializeregexp"))||/[^\-]*$/,serializeParamName:a(this).data("serializeparamname")||!1,dragHandle:a(this).data("draghandle")||null})})}),jQuery.tableDnD={currentTable:null,dragObject:null,mouseOffset:null,oldX:0,oldY:0,build:function(b){return this.each(function(){this.tableDnDConfig=a.extend({onDragStyle:null,onDropStyle:null,onDragClass:"tDnD_whileDrag",onDrop:null,onDragStart:null,onDragStop:null,scrollAmount:5,sensitivity:10,hierarchyLevel:0,indentArtifact:'<div class="indent">&nbsp;</div>',autoWidthAdjust:!0,autoCleanRelations:!0,jsonPretifySeparator:"\t",serializeRegexp:/[^\-]*$/,serializeParamName:!1,dragHandle:null},b||{}),a.tableDnD.makeDraggable(this),this.tableDnDConfig.hierarchyLevel&&a.tableDnD.makeIndented(this)}),this},makeIndented:function(b){var c,d,e=b.tableDnDConfig,f=b.rows,g=a(f).first().find("td:first")[0],h=0,i=0;if(a(b).hasClass("indtd"))return null;d=a(b).addClass("indtd").attr("style"),a(b).css({whiteSpace:"nowrap"});for(var j=0;j<f.length;j++)i<a(f[j]).find("td:first").text().length&&(i=a(f[j]).find("td:first").text().length,c=j);for(a(g).css({width:"auto"}),j=0;j<e.hierarchyLevel;j++)a(f[c]).find("td:first").prepend(e.indentArtifact);for(g&&a(g).css({width:g.offsetWidth}),d&&a(b).css(d),j=0;j<e.hierarchyLevel;j++)a(f[c]).find("td:first").children(":first").remove();return e.hierarchyLevel&&a(f).each(function(){(h=a(this).data("level")||0)<=e.hierarchyLevel&&a(this).data("level",h)||a(this).data("level",0);for(var b=0;b<a(this).data("level");b++)a(this).find("td:first").prepend(e.indentArtifact)}),this},makeDraggable:function(b){var c=b.tableDnDConfig;c.dragHandle&&a(c.dragHandle,b).each(function(){a(this).bind(e,function(d){return a.tableDnD.initialiseDrag(a(this).parents("tr")[0],b,this,d,c),!1})})||a(b.rows).each(function(){a(this).hasClass("nodrag")?a(this).css("cursor",""):a(this).bind(e,function(d){if("TD"===d.target.tagName)return a.tableDnD.initialiseDrag(this,b,this,d,c),!1}).css("cursor","move")})},currentOrder:function(){var b=this.currentTable.rows;return a.map(b,function(b){return(a(b).data("level")+b.id).replace(/\s/g,"")}).join("")},initialiseDrag:function(b,d,e,h,i){this.dragObject=b,this.currentTable=d,this.mouseOffset=this.getMouseOffset(e,h),this.originalOrder=this.currentOrder(),a(c).bind(f,this.mousemove).bind(g,this.mouseup),i.onDragStart&&i.onDragStart(d,e)},updateTables:function(){this.each(function(){this.tableDnDConfig&&a.tableDnD.makeDraggable(this)})},mouseCoords:function(a){return a.originalEvent.changedTouches?{x:a.originalEvent.changedTouches[0].clientX,y:a.originalEvent.changedTouches[0].clientY}:a.pageX||a.pageY?{x:a.pageX,y:a.pageY}:{x:a.clientX+c.body.scrollLeft-c.body.clientLeft,y:a.clientY+c.body.scrollTop-c.body.clientTop}},getMouseOffset:function(a,c){var d,e;return c=c||b.event,e=this.getPosition(a),d=this.mouseCoords(c),{x:d.x-e.x,y:d.y-e.y}},getPosition:function(a){var b=0,c=0;for(0===a.offsetHeight&&(a=a.firstChild);a.offsetParent;)b+=a.offsetLeft,c+=a.offsetTop,a=a.offsetParent;return b+=a.offsetLeft,c+=a.offsetTop,{x:b,y:c}},autoScroll:function(a){var d=this.currentTable.tableDnDConfig,e=b.pageYOffset,f=b.innerHeight?b.innerHeight:c.documentElement.clientHeight?c.documentElement.clientHeight:c.body.clientHeight;c.all&&(void 0!==c.compatMode&&"BackCompat"!==c.compatMode?e=c.documentElement.scrollTop:void 0!==c.body&&(e=c.body.scrollTop)),a.y-e<d.scrollAmount&&b.scrollBy(0,-d.scrollAmount)||f-(a.y-e)<d.scrollAmount&&b.scrollBy(0,d.scrollAmount)},moveVerticle:function(a,b){0!==a.vertical&&b&&this.dragObject!==b&&this.dragObject.parentNode===b.parentNode&&(0>a.vertical&&this.dragObject.parentNode.insertBefore(this.dragObject,b.nextSibling)||0<a.vertical&&this.dragObject.parentNode.insertBefore(this.dragObject,b))},moveHorizontal:function(b,c){var d,e=this.currentTable.tableDnDConfig;if(!e.hierarchyLevel||0===b.horizontal||!c||this.dragObject!==c)return null;d=a(c).data("level"),0<b.horizontal&&d>0&&a(c).find("td:first").children(":first").remove()&&a(c).data("level",--d),0>b.horizontal&&d<e.hierarchyLevel&&a(c).prev().data("level")>=d&&a(c).children(":first").prepend(e.indentArtifact)&&a(c).data("level",++d)},mousemove:function(b){var c,d,e,f,g,h=a(a.tableDnD.dragObject),i=a.tableDnD.currentTable.tableDnDConfig;return b&&b.preventDefault(),!!a.tableDnD.dragObject&&("touchmove"===b.type&&event.preventDefault(),i.onDragClass&&h.addClass(i.onDragClass)||h.css(i.onDragStyle),d=a.tableDnD.mouseCoords(b),f=d.x-a.tableDnD.mouseOffset.x,g=d.y-a.tableDnD.mouseOffset.y,a.tableDnD.autoScroll(d),c=a.tableDnD.findDropTargetRow(h,g),e=a.tableDnD.findDragDirection(f,g),a.tableDnD.moveVerticle(e,c),a.tableDnD.moveHorizontal(e,c),!1)},findDragDirection:function(a,b){var c=this.currentTable.tableDnDConfig.sensitivity,d=this.oldX,e=this.oldY,f=d-c,g=d+c,h=e-c,i=e+c,j={horizontal:a>=f&&a<=g?0:a>d?-1:1,vertical:b>=h&&b<=i?0:b>e?-1:1};return 0!==j.horizontal&&(this.oldX=a),0!==j.vertical&&(this.oldY=b),j},findDropTargetRow:function(b,c){for(var d=0,e=this.currentTable.rows,f=this.currentTable.tableDnDConfig,g=0,h=null,i=0;i<e.length;i++)if(h=e[i],g=this.getPosition(h).y,d=parseInt(h.offsetHeight)/2,0===h.offsetHeight&&(g=this.getPosition(h.firstChild).y,d=parseInt(h.firstChild.offsetHeight)/2),c>g-d&&c<g+d)return b.is(h)||f.onAllowDrop&&!f.onAllowDrop(b,h)||a(h).hasClass("nodrop")?null:h;return null},processMouseup:function(){if(!this.currentTable||!this.dragObject)return null;var b=this.currentTable.tableDnDConfig,d=this.dragObject,e=0,h=0;a(c).unbind(f,this.mousemove).unbind(g,this.mouseup),b.hierarchyLevel&&b.autoCleanRelations&&a(this.currentTable.rows).first().find("td:first").children().each(function(){(h=a(this).parents("tr:first").data("level"))&&a(this).parents("tr:first").data("level",--h)&&a(this).remove()})&&b.hierarchyLevel>1&&a(this.currentTable.rows).each(function(){if((h=a(this).data("level"))>1)for(e=a(this).prev().data("level");h>e+1;)a(this).find("td:first").children(":first").remove(),a(this).data("level",--h)}),b.onDragClass&&a(d).removeClass(b.onDragClass)||a(d).css(b.onDropStyle),this.dragObject=null,b.onDrop&&this.originalOrder!==this.currentOrder()&&a(d).hide().fadeIn("fast")&&b.onDrop(this.currentTable,d),b.onDragStop&&b.onDragStop(this.currentTable,d),this.currentTable=null},mouseup:function(b){return b&&b.preventDefault(),a.tableDnD.processMouseup(),!1},jsonize:function(a){var b=this.currentTable;return a?JSON.stringify(this.tableData(b),null,b.tableDnDConfig.jsonPretifySeparator):JSON.stringify(this.tableData(b))},serialize:function(){return a.param(this.tableData(this.currentTable))},serializeTable:function(a){for(var b="",c=a.tableDnDConfig.serializeParamName||a.id,d=a.rows,e=0;e<d.length;e++){b.length>0&&(b+="&");var f=d[e].id;f&&a.tableDnDConfig&&a.tableDnDConfig.serializeRegexp&&(f=f.match(a.tableDnDConfig.serializeRegexp)[0],b+=c+"[]="+f)}return b},serializeTables:function(){var b=[];return a("table").each(function(){this.id&&b.push(a.param(a.tableDnD.tableData(this)))}),b.join("&")},tableData:function(b){var c,d,e,f,g=b.tableDnDConfig,h=[],i=0,j=0,k=null,l={};if(b||(b=this.currentTable),!b||!b.rows||!b.rows.length)return{error:{code:500,message:"Not a valid table."}};if(!b.id&&!g.serializeParamName)return{error:{code:500,message:"No serializable unique id provided."}};f=g.autoCleanRelations&&b.rows||a.makeArray(b.rows),d=g.serializeParamName||b.id,e=d,c=function(a){return a&&g&&g.serializeRegexp?a.match(g.serializeRegexp)[0]:a},l[e]=[],!g.autoCleanRelations&&a(f[0]).data("level")&&f.unshift({id:"undefined"});for(var m=0;m<f.length;m++)if(g.hierarchyLevel){if(0===(j=a(f[m]).data("level")||0))e=d,h=[];else if(j>i)h.push([e,i]),e=c(f[m-1].id);else if(j<i)for(var n=0;n<h.length;n++)h[n][1]===j&&(e=h[n][0]),h[n][1]>=i&&(h[n][1]=0);i=j,a.isArray(l[e])||(l[e]=[]),k=c(f[m].id),k&&l[e].push(k)}else(k=c(f[m].id))&&l[e].push(k);return l}},jQuery.fn.extend({tableDnD:a.tableDnD.build,tableDnDUpdate:a.tableDnD.updateTables,tableDnDSerialize:a.proxy(a.tableDnD.serialize,a.tableDnD),tableDnDSerializeAll:a.tableDnD.serializeTables,tableDnDData:a.proxy(a.tableDnD.tableData,a.tableDnD)})}(jQuery,window,window.document);
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "jquery")))

/***/ }),

/***/ "./node_modules/webpack/buildin/global.js":
/*!***********************************!*\
  !*** (webpack)/buildin/global.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || new Function("return this")();
} catch (e) {
	// This works if the window reference is available
	if (typeof window === "object") g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL3V0aWxzL3Jlc2V0X3NlYXJjaC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvdXRpbHMvdGFibGUtc29ydGluZy5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2Nob2ljZS10YWJsZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2Zvcm0tc3VibWl0LWJ1dHRvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2Zvcm0vY2hvaWNlLXRyZWUuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9mb3JtL3RleHQtd2l0aC1sZW5ndGgtY291bnRlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9idWxrL2NhdGVnb3J5L2RlbGV0ZS1jYXRlZ29yaWVzLWJ1bGstYWN0aW9uLWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9yb3cvY2F0ZWdvcnkvZGVsZXRlLWNhdGVnb3J5LXJvdy1hY3Rpb24tZXh0ZW5zaW9uLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYWN0aW9uL3Jvdy9zdWJtaXQtcm93LWFjdGlvbi1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9idWxrLWFjdGlvbi1jaGVja2JveC1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9jb2x1bW4vY2F0YWxvZy9jYXRlZ29yeS1wb3NpdGlvbi1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9jb2x1bW4vY29tbW9uL2FzeW5jLXRvZ2dsZS1jb2x1bW4tZXh0ZW5zaW9uLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZXhwb3J0LXRvLXNxbC1tYW5hZ2VyLWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbGluay1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc3VibWl0LWJ1bGstYWN0aW9uLWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZ3JpZC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL3RyYW5zbGF0YWJsZS1pbnB1dC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9jYXRlZ29yaWVzL2luZGV4LmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2NhdGVnb3JpZXMvbmFtZS10by1saW5rLXJld3JpdGUtY29waWVyLmpzIiwid2VicGFjazovLy8uL25vZGVfbW9kdWxlcy90YWJsZWRuZC9kaXN0L2pxdWVyeS50YWJsZWRuZC5taW4uanMiLCJ3ZWJwYWNrOi8vLyh3ZWJwYWNrKS9idWlsZGluL2dsb2JhbC5qcyIsIndlYnBhY2s6Ly8vZXh0ZXJuYWwgXCJqUXVlcnlcIiJdLCJuYW1lcyI6WyIkIiwiZ2xvYmFsIiwiaW5pdCIsInJlc2V0U2VhcmNoIiwidXJsIiwicmVkaXJlY3RVcmwiLCJwb3N0IiwidGhlbiIsIndpbmRvdyIsImxvY2F0aW9uIiwiYXNzaWduIiwiVGFibGVTb3J0aW5nIiwidGFibGUiLCJzZWxlY3RvciIsImNvbHVtbnMiLCJmaW5kIiwib24iLCJlIiwiJGNvbHVtbiIsImRlbGVnYXRlVGFyZ2V0IiwiX3NvcnRCeUNvbHVtbiIsIl9nZXRUb2dnbGVkU29ydERpcmVjdGlvbiIsImNvbHVtbk5hbWUiLCJkaXJlY3Rpb24iLCJpcyIsIkVycm9yIiwiY29sdW1uIiwiX2dldFVybCIsImRhdGEiLCJjb2xOYW1lIiwiVVJMIiwiaHJlZiIsInBhcmFtcyIsInNlYXJjaFBhcmFtcyIsInNldCIsInRvU3RyaW5nIiwiQ2hvaWNlVGFibGUiLCJkb2N1bWVudCIsImhhbmRsZVNlbGVjdEFsbCIsImV2ZW50IiwiJHNlbGVjdEFsbENoZWNrYm94ZXMiLCJ0YXJnZXQiLCJpc1NlbGVjdEFsbENoZWNrZWQiLCJjbG9zZXN0IiwicHJvcCIsIkZvcm1TdWJtaXRCdXR0b24iLCJwcmV2ZW50RGVmYXVsdCIsIiRidG4iLCIkZm9ybSIsImFwcGVuZCIsImFwcGVuZFRvIiwic3VibWl0IiwiQ2hvaWNlVHJlZSIsInRyZWVTZWxlY3RvciIsIiRjb250YWluZXIiLCIkaW5wdXRXcmFwcGVyIiwiY3VycmVudFRhcmdldCIsIl90b2dnbGVDaGlsZFRyZWUiLCIkYWN0aW9uIiwiX3RvZ2dsZVRyZWUiLCJlbmFibGVBdXRvQ2hlY2tDaGlsZHJlbiIsIiRjbGlja2VkQ2hlY2tib3giLCIkaXRlbVdpdGhDaGlsZHJlbiIsIiRwYXJlbnRXcmFwcGVyIiwiaGFzQ2xhc3MiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiJHBhcmVudENvbnRhaW5lciIsImFjdGlvbiIsImNvbmZpZyIsImV4cGFuZCIsImNvbGxhcHNlIiwibmV4dEFjdGlvbiIsInRleHQiLCJpY29uIiwiZWFjaCIsImluZGV4IiwiaXRlbSIsIiRpdGVtIiwiVGV4dFdpdGhMZW5ndGhDb3VudGVyIiwiJGlucHV0IiwicmVtYWluaW5nTGVuZ3RoIiwidmFsIiwibGVuZ3RoIiwiRGVsZXRlQ2F0ZWdvcmllc0J1bGtBY3Rpb25FeHRlbnNpb24iLCJleHRlbmQiLCJncmlkIiwiZ2V0Q29udGFpbmVyIiwic3VibWl0VXJsIiwiJGRlbGV0ZUNhdGVnb3JpZXNNb2RhbCIsImdldElkIiwibW9kYWwiLCIkY2hlY2tib3hlcyIsIiRjYXRlZ29yaWVzVG9EZWxldGVJbnB1dEJsb2NrIiwiaSIsInZhbHVlIiwiY2F0ZWdvcnlJbnB1dCIsInJlcGxhY2UiLCJwYXJzZUhUTUwiLCJhdHRyIiwiRGVsZXRlQ2F0ZWdvcnlSb3dBY3Rpb25FeHRlbnNpb24iLCIkYnV0dG9uIiwiY2F0ZWdvcnlJZCIsImNoaWxkcmVuIiwiU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uIiwiY29uZmlybU1lc3NhZ2UiLCJjb25maXJtIiwibWV0aG9kIiwiaXNHZXRPclBvc3RNZXRob2QiLCJpbmNsdWRlcyIsIkJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbiIsIl9oYW5kbGVCdWxrQWN0aW9uQ2hlY2tib3hTZWxlY3QiLCJfaGFuZGxlQnVsa0FjdGlvblNlbGVjdEFsbENoZWNrYm94IiwiJGNoZWNrYm94IiwiaXNDaGVja2VkIiwiX2VuYWJsZUJ1bGtBY3Rpb25zQnRuIiwiX2Rpc2FibGVCdWxrQWN0aW9uc0J0biIsImNoZWNrZWRSb3dzQ291bnQiLCJDYXRlZ29yeVBvc2l0aW9uRXh0ZW5zaW9uIiwiX2FkZElkc1RvR3JpZFRhYmxlUm93cyIsInRhYmxlRG5EIiwiZHJhZ0hhbmRsZSIsIm9uRHJhZ1N0YXJ0Iiwib3JpZ2luYWxQb3NpdGlvbnMiLCJkZWNvZGVVUklDb21wb25lbnQiLCJzZXJpYWxpemUiLCJvbkRyb3AiLCJyb3ciLCJfaGFuZGxlQ2F0ZWdvcnlQb3NpdGlvbkNoYW5nZSIsInBvc2l0aW9ucyIsIndheSIsImluZGV4T2YiLCJpZCIsIiRjYXRlZ29yeVBvc2l0aW9uQ29udGFpbmVyIiwiY2F0ZWdvcnlQYXJlbnRJZCIsInBvc2l0aW9uVXBkYXRlVXJsIiwiUmVnRXhwIiwicXVlcnlQYXJhbXMiLCJpZF9jYXRlZ29yeV9wYXJlbnQiLCJpZF9jYXRlZ29yeV90b19tb3ZlIiwiYWpheCIsImZvdW5kX2ZpcnN0IiwicGFyYW0iLCJfdXBkYXRlQ2F0ZWdvcnlQb3NpdGlvbiIsInBvc2l0aW9uV3JhcHBlciIsIiRwb3NpdGlvbldyYXBwZXIiLCJwb3NpdGlvbiIsIiRyb3ciLCJvZmZzZXQiLCJuZXdQb3NpdGlvbiIsIm9sZElkIiwiaGVhZGVycyIsInJlc3BvbnNlIiwiSlNPTiIsInBhcnNlIiwibWVzc2FnZSIsInNob3dTdWNjZXNzTWVzc2FnZSIsInNob3dFcnJvck1lc3NhZ2UiLCJlcnJvcnMiLCJfdXBkYXRlQ2F0ZWdvcnlJZHNBbmRQb3NpdGlvbnMiLCJBc3luY1RvZ2dsZUNvbHVtbkV4dGVuc2lvbiIsInN0YXR1cyIsIl90b2dnbGVCdXR0b25EaXNwbGF5IiwiaXNBY3RpdmUiLCJjbGFzc1RvQWRkIiwiY2xhc3NUb1JlbW92ZSIsIkV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiIsImdldEhlYWRlckNvbnRhaW5lciIsIl9vblNob3dTcWxRdWVyeUNsaWNrIiwiX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrIiwiJHNxbE1hbmFnZXJGb3JtIiwiX2ZpbGxFeHBvcnRGb3JtIiwiJG1vZGFsIiwicXVlcnkiLCJfZ2V0TmFtZUZyb21CcmVhZGNydW1iIiwiJGJyZWFkY3J1bWJzIiwibmFtZSIsIiRicmVhZGNydW1iIiwiYnJlYWRjcnVtYlRpdGxlIiwiY29uY2F0IiwiRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIiwiTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiIsIlJlbG9hZExpc3RFeHRlbnNpb24iLCJyZWxvYWQiLCJTb3J0aW5nRXh0ZW5zaW9uIiwiJHNvcnRhYmxlVGFibGUiLCJhdHRhY2giLCJTdWJtaXRCdWxrQWN0aW9uRXh0ZW5zaW9uIiwiJHN1Ym1pdEJ0biIsIkdyaWQiLCJleHRlbnNpb24iLCJUcmFuc2xhdGFibGVJbnB1dCIsIm9wdGlvbnMiLCJsb2NhbGVJdGVtU2VsZWN0b3IiLCJsb2NhbGVCdXR0b25TZWxlY3RvciIsImxvY2FsZUlucHV0U2VsZWN0b3IiLCJ0b2dnbGVJbnB1dHMiLCJiaW5kIiwibG9jYWxlSXRlbSIsImZvcm0iLCJzZWxlY3RlZExvY2FsZSIsImNhdGVnb3JpZXNHcmlkIiwiYWRkRXh0ZW5zaW9uIiwiU3VibWl0QnVsa0V4dGVuc2lvbiIsIk5hbWVUb0xpbmtSZXdyaXRlQ29waWVyIiwiZm9yRWFjaCIsImNhdGVnb3J5VHlwZSIsIiRjYXRlZ29yeUZvcm0iLCIkbmFtZUlucHV0IiwibGFuZ0lkIiwic3RyMnVybCJdLCJtYXBwaW5ncyI6IjtBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0Esa0RBQTBDLGdDQUFnQztBQUMxRTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLGdFQUF3RCxrQkFBa0I7QUFDMUU7QUFDQSx5REFBaUQsY0FBYztBQUMvRDs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsaURBQXlDLGlDQUFpQztBQUMxRSx3SEFBZ0gsbUJBQW1CLEVBQUU7QUFDckk7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7O0FBR0E7QUFDQTs7Ozs7Ozs7Ozs7OztBQ2xGQTtBQUFBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBOzs7QUFJQSxJQUFNQSxDQUFDLEdBQUdDLE1BQU0sQ0FBQ0QsQ0FBakI7O0FBRUEsSUFBTUUsSUFBSSxHQUFHLFNBQVNDLFdBQVQsQ0FBcUJDLEdBQXJCLEVBQTBCQyxXQUExQixFQUF1QztBQUNoREwsR0FBQyxDQUFDTSxJQUFGLENBQU9GLEdBQVAsRUFBWUcsSUFB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QUFrQyxxREFBcUQsY0FBYyx1QkFBdUIsd0RBQXdELCtEQUErRCxPQUFPLHdDQUF3Qyx1Q0FBdUMsT0FBTyx5REFBeUQsbUdBQW1HLCtEQUErRCxrRUFBa0UsZUFBZSxFQUFFLFlBQVksV0FBVyx5QkFBeUIsNkNBQTZDLHlDQUF5Qyx3QkFBd0IsV0FBVyxxREFBcUQsNERBQTRELGlDQUFpQyxVQUFVLG1CQUFtQixrT0FBa08sRUFBRSxnQzs7Ozs7Ozs7Ozs7O0FDRDNxUzs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxDQUFDOztBQUVEO0FBQ0E7QUFDQTtBQUNBLENBQUM7QUFDRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLDRDQUE0Qzs7QUFFNUM7Ozs7Ozs7Ozs7OztBQ25CQSx3QiIsImZpbGUiOiJjYXRlZ29yaWVzLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwgeyBlbnVtZXJhYmxlOiB0cnVlLCBnZXQ6IGdldHRlciB9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yID0gZnVuY3Rpb24oZXhwb3J0cykge1xuIFx0XHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcbiBcdFx0fVxuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xuIFx0fTtcblxuIFx0Ly8gY3JlYXRlIGEgZmFrZSBuYW1lc3BhY2Ugb2JqZWN0XG4gXHQvLyBtb2RlICYgMTogdmFsdWUgaXMgYSBtb2R1bGUgaWQsIHJlcXVpcmUgaXRcbiBcdC8vIG1vZGUgJiAyOiBtZXJnZSBhbGwgcHJvcGVydGllcyBvZiB2YWx1ZSBpbnRvIHRoZSBuc1xuIFx0Ly8gbW9kZSAmIDQ6IHJldHVybiB2YWx1ZSB3aGVuIGFscmVhZHkgbnMgb2JqZWN0XG4gXHQvLyBtb2RlICYgOHwxOiBiZWhhdmUgbGlrZSByZXF1aXJlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnQgPSBmdW5jdGlvbih2YWx1ZSwgbW9kZSkge1xuIFx0XHRpZihtb2RlICYgMSkgdmFsdWUgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKHZhbHVlKTtcbiBcdFx0aWYobW9kZSAmIDgpIHJldHVybiB2YWx1ZTtcbiBcdFx0aWYoKG1vZGUgJiA0KSAmJiB0eXBlb2YgdmFsdWUgPT09ICdvYmplY3QnICYmIHZhbHVlICYmIHZhbHVlLl9fZXNNb2R1bGUpIHJldHVybiB2YWx1ZTtcbiBcdFx0dmFyIG5zID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yKG5zKTtcbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KG5zLCAnZGVmYXVsdCcsIHsgZW51bWVyYWJsZTogdHJ1ZSwgdmFsdWU6IHZhbHVlIH0pO1xuIFx0XHRpZihtb2RlICYgMiAmJiB0eXBlb2YgdmFsdWUgIT0gJ3N0cmluZycpIGZvcih2YXIga2V5IGluIHZhbHVlKSBfX3dlYnBhY2tfcmVxdWlyZV9fLmQobnMsIGtleSwgZnVuY3Rpb24oa2V5KSB7IHJldHVybiB2YWx1ZVtrZXldOyB9LmJpbmQobnVsbCwga2V5KSk7XG4gXHRcdHJldHVybiBucztcbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiL2FkbWluLWRldi90aGVtZXMvbmV3LXRoZW1lL3B1YmxpYy9cIjtcblxuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IFwiLi9qcy9wYWdlcy9jYXRlZ29yaWVzL2luZGV4LmpzXCIpO1xuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBTZW5kIGEgUG9zdCBSZXF1ZXN0IHRvIHJlc2V0IHNlYXJjaCBBY3Rpb24uXG4gKi9cblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG5jb25zdCBpbml0ID0gZnVuY3Rpb24gcmVzZXRTZWFyY2godXJsLCByZWRpcmVjdFVybCkge1xuICAgICQucG9zdCh1cmwpLnRoZW4oKCkgPT4gd2luZG93LmxvY2F0aW9uLmFzc2lnbihyZWRpcmVjdFVybCkpO1xufTtcblxuZXhwb3J0IGRlZmF1bHQgaW5pdDtcbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuLyoqXG4gKiBNYWtlcyBhIHRhYmxlIHNvcnRhYmxlIGJ5IGNvbHVtbnMuXG4gKiBUaGlzIGZvcmNlcyBhIHBhZ2UgcmVsb2FkIHdpdGggbW9yZSBxdWVyeSBwYXJhbWV0ZXJzLlxuICovXG5jbGFzcyBUYWJsZVNvcnRpbmcge1xuXG4gIC8qKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gdGFibGVcbiAgICovXG4gIGNvbnN0cnVjdG9yKHRhYmxlKSB7XG4gICAgdGhpcy5zZWxlY3RvciA9ICcucHMtc29ydGFibGUtY29sdW1uJztcbiAgICB0aGlzLmNvbHVtbnMgPSAkKHRhYmxlKS5maW5kKHRoaXMuc2VsZWN0b3IpO1xuICB9XG5cbiAgLyoqXG4gICAqIEF0dGFjaGVzIHRoZSBsaXN0ZW5lcnNcbiAgICovXG4gIGF0dGFjaCgpIHtcbiAgICB0aGlzLmNvbHVtbnMub24oJ2NsaWNrJywgKGUpID0+IHtcbiAgICAgIGNvbnN0ICRjb2x1bW4gPSAkKGUuZGVsZWdhdGVUYXJnZXQpO1xuICAgICAgdGhpcy5fc29ydEJ5Q29sdW1uKCRjb2x1bW4sIHRoaXMuX2dldFRvZ2dsZWRTb3J0RGlyZWN0aW9uKCRjb2x1bW4pKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTb3J0IHVzaW5nIGEgY29sdW1uIG5hbWVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGNvbHVtbk5hbWVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGRpcmVjdGlvbiBcImFzY1wiIG9yIFwiZGVzY1wiXG4gICAqL1xuICBzb3J0QnkoY29sdW1uTmFtZSwgZGlyZWN0aW9uKSB7XG4gICAgY29uc3QgJGNvbHVtbiA9IHRoaXMuY29sdW1ucy5pcyhgW2RhdGEtc29ydC1jb2wtbmFtZT1cIiR7Y29sdW1uTmFtZX1cIl1gKTtcbiAgICBpZiAoISRjb2x1bW4pIHtcbiAgICAgIHRocm93IG5ldyBFcnJvcihgQ2Fubm90IHNvcnQgYnkgXCIke2NvbHVtbk5hbWV9XCI6IGludmFsaWQgY29sdW1uYCk7XG4gICAgfVxuXG4gICAgdGhpcy5fc29ydEJ5Q29sdW1uKCRjb2x1bW4sIGRpcmVjdGlvbik7XG4gIH1cblxuICAvKipcbiAgICogU29ydCB1c2luZyBhIGNvbHVtbiBlbGVtZW50XG4gICAqIEBwYXJhbSB7alF1ZXJ5fSBjb2x1bW5cbiAgICogQHBhcmFtIHtzdHJpbmd9IGRpcmVjdGlvbiBcImFzY1wiIG9yIFwiZGVzY1wiXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc29ydEJ5Q29sdW1uKGNvbHVtbiwgZGlyZWN0aW9uKSB7XG4gICAgd2luZG93LmxvY2F0aW9uID0gdGhpcy5fZ2V0VXJsKGNvbHVtbi5kYXRhKCdzb3J0Q29sTmFtZScpLCAoZGlyZWN0aW9uID09PSAnZGVzYycpID8gJ2Rlc2MnIDogJ2FzYycpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJldHVybnMgdGhlIGludmVydGVkIGRpcmVjdGlvbiB0byBzb3J0IGFjY29yZGluZyB0byB0aGUgY29sdW1uJ3MgY3VycmVudCBvbmVcbiAgICogQHBhcmFtIHtqUXVlcnl9IGNvbHVtblxuICAgKiBAcmV0dXJuIHtzdHJpbmd9XG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZ2V0VG9nZ2xlZFNvcnREaXJlY3Rpb24oY29sdW1uKSB7XG4gICAgcmV0dXJuIGNvbHVtbi5kYXRhKCdzb3J0RGlyZWN0aW9uJykgPT09ICdhc2MnID8gJ2Rlc2MnIDogJ2FzYyc7XG4gIH1cblxuICAvKipcbiAgICogUmV0dXJucyB0aGUgdXJsIGZvciB0aGUgc29ydGVkIHRhYmxlXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBjb2xOYW1lXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBkaXJlY3Rpb25cbiAgICogQHJldHVybiB7c3RyaW5nfVxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldFVybChjb2xOYW1lLCBkaXJlY3Rpb24pIHtcbiAgICBjb25zdCB1cmwgPSBuZXcgVVJMKHdpbmRvdy5sb2NhdGlvbi5ocmVmKTtcbiAgICBjb25zdCBwYXJhbXMgPSB1cmwuc2VhcmNoUGFyYW1zO1xuXG4gICAgcGFyYW1zLnNldCgnb3JkZXJCeScsIGNvbE5hbWUpO1xuICAgIHBhcmFtcy5zZXQoJ3NvcnRPcmRlcicsIGRpcmVjdGlvbik7XG5cbiAgICByZXR1cm4gdXJsLnRvU3RyaW5nKCk7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgVGFibGVTb3J0aW5nO1xuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENob2ljZVRhYmxlIGlzIHJlc3BvbnNpYmxlIGZvciBtYW5hZ2luZyBjb21tb24gYWN0aW9ucyBpbiBjaG9pY2UgdGFibGUgZm9ybSB0eXBlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENob2ljZVRhYmxlIHtcbiAgLyoqXG4gICAqIEluaXQgY29uc3RydWN0b3JcbiAgICovXG4gIGNvbnN0cnVjdG9yKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjaGFuZ2UnLCAnLmpzLWNob2ljZS10YWJsZS1zZWxlY3QtYWxsJywgKGUpID0+IHtcbiAgICAgIHRoaXMuaGFuZGxlU2VsZWN0QWxsKGUpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIENoZWNrL3VuY2hlY2sgYWxsIGJveGVzIGluIHRhYmxlXG4gICAqXG4gICAqIEBwYXJhbSB7RXZlbnR9IGV2ZW50XG4gICAqL1xuICBoYW5kbGVTZWxlY3RBbGwoZXZlbnQpIHtcbiAgICBjb25zdCAkc2VsZWN0QWxsQ2hlY2tib3hlcyA9ICQoZXZlbnQudGFyZ2V0KTtcbiAgICBjb25zdCBpc1NlbGVjdEFsbENoZWNrZWQgPSAkc2VsZWN0QWxsQ2hlY2tib3hlcy5pcygnOmNoZWNrZWQnKTtcblxuICAgICRzZWxlY3RBbGxDaGVja2JveGVzLmNsb3Nlc3QoJ3RhYmxlJykuZmluZCgndGJvZHkgaW5wdXQ6Y2hlY2tib3gnKS5wcm9wKCdjaGVja2VkJywgaXNTZWxlY3RBbGxDaGVja2VkKTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENvbXBvbmVudCB3aGljaCBhbGxvd3Mgc3VibWl0dGluZyB2ZXJ5IHNpbXBsZSBmb3JtcyB3aXRob3V0IGhhdmluZyB0byB1c2UgPGZvcm0+IGVsZW1lbnQuXG4gKlxuICogVXNlZnVsIHdoZW4gcGVyZm9ybWluZyBhY3Rpb25zIG9uIHJlc291cmNlIHdoZXJlIFVSTCBjb250YWlucyBhbGwgbmVlZGVkIGRhdGEuXG4gKiBGb3IgZXhhbXBsZSwgdG8gdG9nZ2xlIGNhdGVnb3J5IHN0YXR1cyB2aWEgXCJQT1NUIC9jYXRlZ29yaWVzLzIvdG9nZ2xlLXN0YXR1cylcIlxuICogb3IgZGVsZXRlIGNvdmVyIGltYWdlIHZpYSBcIlBPU1QgL2NhdGVnb3JpZXMvMi9kZWxldGUtY292ZXItaW1hZ2VcIi5cbiAqXG4gKiBVc2FnZSBleGFtcGxlIGluIHRlbXBsYXRlOlxuICpcbiAqIDxidXR0b24gY2xhc3M9XCJqcy1mb3JtLXN1Ym1pdC1idG5cIlxuICogICAgICAgICBkYXRhLWZvcm0tc3VibWl0LXVybD1cIi9teS1jdXN0b20tdXJsXCIgICAgICAgICAgLy8gKHJlcXVpcmVkKSBVUkwgdG8gd2hpY2ggZm9ybSB3aWxsIGJlIHN1Ym1pdHRlZFxuICogICAgICAgICBkYXRhLWZvcm0tY3NyZi10b2tlbj1cIm15LWdlbmVyYXRlZC1jc3JmLXRva2VuXCIgLy8gKG9wdGlvbmFsKSB0byBpbmNyZWFzZSBzZWN1cml0eVxuICogICAgICAgICB0eXBlPVwiYnV0dG9uXCIgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gbWFrZSBzdXJlIGl0cyBzaW1wbGUgYnV0dG9uXG4gKiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gc28gd2UgY2FuIGF2b2lkIHN1Ym1pdHRpbmcgYWN0dWFsIGZvcm1cbiAqICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyB3aGVuIG91ciBidXR0b24gaXMgZGVmaW5lZCBpbnNpZGUgZm9ybVxuICogPlxuICogICAgIENsaWNrIG1lIHRvIHN1Ym1pdCBmb3JtXG4gKiA8L2J1dHRvbj5cbiAqXG4gKiBJbiBwYWdlIHNwZWNpZmljIEpTIHlvdSBoYXZlIHRvIGVuYWJsZSB0aGlzIGZlYXR1cmU6XG4gKlxuICogbmV3IEZvcm1TdWJtaXRCdXR0b24oKTtcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRm9ybVN1Ym1pdEJ1dHRvbiB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuanMtZm9ybS1zdWJtaXQtYnRuJywgZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICBjb25zdCAkYnRuID0gJCh0aGlzKTtcblxuICAgICAgY29uc3QgJGZvcm0gPSAkKCc8Zm9ybT4nLCB7XG4gICAgICAgICdhY3Rpb24nOiAkYnRuLmRhdGEoJ2Zvcm0tc3VibWl0LXVybCcpLFxuICAgICAgICAnbWV0aG9kJzogJ1BPU1QnLFxuICAgICAgfSk7XG5cbiAgICAgIGlmICgkYnRuLmRhdGEoJ2Zvcm0tY3NyZi10b2tlbicpKSB7XG4gICAgICAgICRmb3JtLmFwcGVuZCgkKCc8aW5wdXQ+Jywge1xuICAgICAgICAgICd0eXBlJzogJ19oaWRkZW4nLFxuICAgICAgICAgICduYW1lJzogJ19jc3JmX3Rva2VuJyxcbiAgICAgICAgICAndmFsdWUnOiAkYnRuLmRhdGEoJ2Zvcm0tY3NyZi10b2tlbicpXG4gICAgICAgIH0pKTtcbiAgICAgIH1cblxuICAgICAgJGZvcm0uYXBwZW5kVG8oJ2JvZHknKS5zdWJtaXQoKTtcbiAgICB9KTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIEhhbmRsZXMgVUkgaW50ZXJhY3Rpb25zIG9mIGNob2ljZSB0cmVlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENob2ljZVRyZWUge1xuICAvKipcbiAgICogQHBhcmFtIHtTdHJpbmd9IHRyZWVTZWxlY3RvclxuICAgKi9cbiAgY29uc3RydWN0b3IodHJlZVNlbGVjdG9yKSB7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJCh0cmVlU2VsZWN0b3IpO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsICcuanMtaW5wdXQtd3JhcHBlcicsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGlucHV0V3JhcHBlciA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG5cbiAgICAgIHRoaXMuX3RvZ2dsZUNoaWxkVHJlZSgkaW5wdXRXcmFwcGVyKTtcbiAgICB9KTtcblxuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2xpY2snLCAnLmpzLXRvZ2dsZS1jaG9pY2UtdHJlZS1hY3Rpb24nLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRhY3Rpb24gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICB0aGlzLl90b2dnbGVUcmVlKCRhY3Rpb24pO1xuICAgIH0pO1xuXG4gICAgcmV0dXJuIHtcbiAgICAgIGVuYWJsZUF1dG9DaGVja0NoaWxkcmVuOiAoKSA9PiB0aGlzLmVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbmFibGUgYXV0b21hdGljIGNoZWNrL3VuY2hlY2sgb2YgY2xpY2tlZCBpdGVtJ3MgY2hpbGRyZW4uXG4gICAqL1xuICBlbmFibGVBdXRvQ2hlY2tDaGlsZHJlbigpIHtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NoYW5nZScsICdpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0nLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRjbGlja2VkQ2hlY2tib3ggPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgY29uc3QgJGl0ZW1XaXRoQ2hpbGRyZW4gPSAkY2xpY2tlZENoZWNrYm94LmNsb3Nlc3QoJ2xpJyk7XG5cbiAgICAgICRpdGVtV2l0aENoaWxkcmVuXG4gICAgICAgIC5maW5kKCd1bCBpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0nKVxuICAgICAgICAucHJvcCgnY2hlY2tlZCcsICRjbGlja2VkQ2hlY2tib3guaXMoJzpjaGVja2VkJykpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIENvbGxhcHNlIG9yIGV4cGFuZCBzdWItdHJlZSBmb3Igc2luZ2xlIHBhcmVudFxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGlucHV0V3JhcHBlclxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3RvZ2dsZUNoaWxkVHJlZSgkaW5wdXRXcmFwcGVyKSB7XG4gICAgY29uc3QgJHBhcmVudFdyYXBwZXIgPSAkaW5wdXRXcmFwcGVyLmNsb3Nlc3QoJ2xpJyk7XG5cbiAgICBpZiAoJHBhcmVudFdyYXBwZXIuaGFzQ2xhc3MoJ2V4cGFuZGVkJykpIHtcbiAgICAgICRwYXJlbnRXcmFwcGVyXG4gICAgICAgIC5yZW1vdmVDbGFzcygnZXhwYW5kZWQnKVxuICAgICAgICAuYWRkQ2xhc3MoJ2NvbGxhcHNlZCcpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgaWYgKCRwYXJlbnRXcmFwcGVyLmhhc0NsYXNzKCdjb2xsYXBzZWQnKSkge1xuICAgICAgJHBhcmVudFdyYXBwZXJcbiAgICAgICAgLnJlbW92ZUNsYXNzKCdjb2xsYXBzZWQnKVxuICAgICAgICAuYWRkQ2xhc3MoJ2V4cGFuZGVkJyk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIENvbGxhcHNlIG9yIGV4cGFuZCB3aG9sZSB0cmVlXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkYWN0aW9uXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdG9nZ2xlVHJlZSgkYWN0aW9uKSB7XG4gICAgY29uc3QgJHBhcmVudENvbnRhaW5lciA9ICRhY3Rpb24uY2xvc2VzdCgnLmpzLWNob2ljZS10cmVlLWNvbnRhaW5lcicpO1xuICAgIGNvbnN0IGFjdGlvbiA9ICRhY3Rpb24uZGF0YSgnYWN0aW9uJyk7XG5cbiAgICAvLyB0b2dnbGUgYWN0aW9uIGNvbmZpZ3VyYXRpb25cbiAgICBjb25zdCBjb25maWcgPSB7XG4gICAgICBhZGRDbGFzczoge1xuICAgICAgICBleHBhbmQ6ICdleHBhbmRlZCcsXG4gICAgICAgIGNvbGxhcHNlOiAnY29sbGFwc2VkJyxcbiAgICAgIH0sXG4gICAgICByZW1vdmVDbGFzczoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZWQnLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZGVkJyxcbiAgICAgIH0sXG4gICAgICBuZXh0QWN0aW9uOiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlJyxcbiAgICAgICAgY29sbGFwc2U6ICdleHBhbmQnLFxuICAgICAgfSxcbiAgICAgIHRleHQ6IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2VkLXRleHQnLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZGVkLXRleHQnLFxuICAgICAgfSxcbiAgICAgIGljb246IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2VkLWljb24nLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZGVkLWljb24nLFxuICAgICAgfVxuICAgIH07XG5cbiAgICAkcGFyZW50Q29udGFpbmVyLmZpbmQoJ2xpJykuZWFjaCgoaW5kZXgsIGl0ZW0pID0+IHtcbiAgICAgIGNvbnN0ICRpdGVtID0gJChpdGVtKTtcblxuICAgICAgaWYgKCRpdGVtLmhhc0NsYXNzKGNvbmZpZy5yZW1vdmVDbGFzc1thY3Rpb25dKSkge1xuICAgICAgICAgICRpdGVtLnJlbW92ZUNsYXNzKGNvbmZpZy5yZW1vdmVDbGFzc1thY3Rpb25dKVxuICAgICAgICAgICAgLmFkZENsYXNzKGNvbmZpZy5hZGRDbGFzc1thY3Rpb25dKTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgICRhY3Rpb24uZGF0YSgnYWN0aW9uJywgY29uZmlnLm5leHRBY3Rpb25bYWN0aW9uXSk7XG4gICAgJGFjdGlvbi5maW5kKCcubWF0ZXJpYWwtaWNvbnMnKS50ZXh0KCRhY3Rpb24uZGF0YShjb25maWcuaWNvblthY3Rpb25dKSk7XG4gICAgJGFjdGlvbi5maW5kKCcuanMtdG9nZ2xlLXRleHQnKS50ZXh0KCRhY3Rpb24uZGF0YShjb25maWcudGV4dFthY3Rpb25dKSk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBUZXh0V2l0aExlbmd0aENvdW50ZXIgaGFuZGxlcyBpbnB1dCB3aXRoIGxlbmd0aCBjb3VudGVyIFVJLlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBUZXh0V2l0aExlbmd0aENvdW50ZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICAkKGRvY3VtZW50KS5vbignaW5wdXQnLCAnLmpzLXRleHQtd2l0aC1jb3VudGVyLWlucHV0LWdyb3VwIGlucHV0W3R5cGU9XCJ0ZXh0XCJdJywgKGUpID0+IHtcbiAgICAgIGNvbnN0ICRpbnB1dCA9ICQoZS5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0IHJlbWFpbmluZ0xlbmd0aCA9ICRpbnB1dC5kYXRhKCdtYXgtbGVuZ3RoJykgLSAkaW5wdXQudmFsKCkubGVuZ3RoO1xuXG4gICAgICAkaW5wdXQuY2xvc2VzdCgnLmpzLXRleHQtd2l0aC1jb3VudGVyLWlucHV0LWdyb3VwJykuZmluZCgnLmpzLWNvdW50ZXItdGV4dCcpLnRleHQocmVtYWluaW5nTGVuZ3RoKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDaGVjay91bmNoZWNrIGFsbCBib3hlcyBpbiB0YWJsZVxuICAgKlxuICAgKiBAcGFyYW0ge0V2ZW50fSBldmVudFxuICAgKi9cbiAgaGFuZGxlU2VsZWN0QWxsKGV2ZW50KSB7XG4gICAgY29uc3QgJHNlbGVjdEFsbENoZWNrYm94ZXMgPSAkKGV2ZW50LnRhcmdldCk7XG4gICAgY29uc3QgaXNTZWxlY3RBbGxDaGVja2VkID0gJHNlbGVjdEFsbENoZWNrYm94ZXMuaXMoJzpjaGVja2VkJyk7XG5cbiAgICAkc2VsZWN0QWxsQ2hlY2tib3hlcy5jbG9zZXN0KCd0YWJsZScpLmZpbmQoJ3Rib2R5IGlucHV0OmNoZWNrYm94JykucHJvcCgnY2hlY2tlZCcsIGlzU2VsZWN0QWxsQ2hlY2tlZCk7XG4gIH1cbn1cblxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIERlbGV0ZUNhdGVnb3JpZXNCdWxrQWN0aW9uRXh0ZW5zaW9uIGhhbmRsZXMgc3VibWl0dGluZyBvZiByb3cgYWN0aW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIERlbGV0ZUNhdGVnb3JpZXNCdWxrQWN0aW9uRXh0ZW5zaW9uIHtcblxuICBjb25zdHJ1Y3RvcigpIHtcbiAgICByZXR1cm4ge1xuICAgICAgZXh0ZW5kOiAoZ3JpZCkgPT4gdGhpcy5leHRlbmQoZ3JpZCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWRlbGV0ZS1jYXRlZ29yaWVzLWJ1bGstYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICBjb25zdCBzdWJtaXRVcmwgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ2NhdGVnb3JpZXMtZGVsZXRlLXVybCcpO1xuXG4gICAgICBjb25zdCAkZGVsZXRlQ2F0ZWdvcmllc01vZGFsID0gJChgIyR7Z3JpZC5nZXRJZCgpfV9ncmlkX2RlbGV0ZV9jYXRlZ29yaWVzX21vZGFsYCk7XG4gICAgICAkZGVsZXRlQ2F0ZWdvcmllc01vZGFsLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAgICRkZWxldGVDYXRlZ29yaWVzTW9kYWwub24oJ2NsaWNrJywgJy5qcy1zdWJtaXQtZGVsZXRlLWNhdGVnb3JpZXMnLCAoKSA9PiB7XG4gICAgICAgIGNvbnN0ICRjaGVja2JveGVzID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb24tY2hlY2tib3gnKTtcbiAgICAgICAgY29uc3QgJGNhdGVnb3JpZXNUb0RlbGV0ZUlucHV0QmxvY2sgPSAkKCcjZGVsZXRlX2NhdGVnb3JpZXNfY2F0ZWdvcmllc190b19kZWxldGUnKTtcblxuICAgICAgICAkY2hlY2tib3hlcy5lYWNoKChpLCB2YWx1ZSkgPT4ge1xuICAgICAgICAgIGNvbnN0ICRpbnB1dCA9ICQodmFsdWUpO1xuXG4gICAgICAgICAgY29uc3QgY2F0ZWdvcnlJbnB1dCA9ICRjYXRlZ29yaWVzVG9EZWxldGVJbnB1dEJsb2NrXG4gICAgICAgICAgICAuZGF0YSgncHJvdG90eXBlJylcbiAgICAgICAgICAgIC5yZXBsYWNlKC9fX25hbWVfXy9nLCAkaW5wdXQudmFsKCkpO1xuXG4gICAgICAgICAgY29uc3QgJGl0ZW0gPSAkKCQucGFyc2VIVE1MKGNhdGVnb3J5SW5wdXQpWzBdKTtcbiAgICAgICAgICAkaXRlbS52YWwoJGlucHV0LnZhbCgpKTtcblxuICAgICAgICAgICRjYXRlZ29yaWVzVG9EZWxldGVJbnB1dEJsb2NrLmFwcGVuZCgkaXRlbSk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGNvbnN0ICRmb3JtID0gJGRlbGV0ZUNhdGVnb3JpZXNNb2RhbC5maW5kKCdmb3JtJyk7XG5cbiAgICAgICAgJGZvcm0uYXR0cignYWN0aW9uJywgc3VibWl0VXJsKTtcbiAgICAgICAgJGZvcm0uc3VibWl0KCk7XG4gICAgICB9KTtcbiAgICB9KTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIENhdGVnb3J5RGVsZXRlUm93QWN0aW9uRXh0ZW5zaW9uIGhhbmRsZXMgc3VibWl0dGluZyBvZiByb3cgYWN0aW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIERlbGV0ZUNhdGVnb3J5Um93QWN0aW9uRXh0ZW5zaW9uIHtcblxuICBjb25zdHJ1Y3RvcigpIHtcbiAgICByZXR1cm4ge1xuICAgICAgZXh0ZW5kOiAoZ3JpZCkgPT4gdGhpcy5leHRlbmQoZ3JpZCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWRlbGV0ZS1jYXRlZ29yeS1yb3ctYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICBjb25zdCAkZGVsZXRlQ2F0ZWdvcmllc01vZGFsID0gJCgnIycgKyBncmlkLmdldElkKCkgKyAnX2dyaWRfZGVsZXRlX2NhdGVnb3JpZXNfbW9kYWwnKTtcbiAgICAgICRkZWxldGVDYXRlZ29yaWVzTW9kYWwubW9kYWwoJ3Nob3cnKTtcblxuICAgICAgJGRlbGV0ZUNhdGVnb3JpZXNNb2RhbC5vbignY2xpY2snLCAnLmpzLXN1Ym1pdC1kZWxldGUtY2F0ZWdvcmllcycsICgpID0+IHtcbiAgICAgICAgY29uc3QgJGJ1dHRvbiA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICAgIGNvbnN0IGNhdGVnb3J5SWQgPSAkYnV0dG9uLmRhdGEoJ2NhdGVnb3J5LWlkJyk7XG5cbiAgICAgICAgY29uc3QgJGNhdGVnb3JpZXNUb0RlbGV0ZUlucHV0QmxvY2sgPSAkKCcjZGVsZXRlX2NhdGVnb3JpZXNfY2F0ZWdvcmllc190b19kZWxldGUnKTtcblxuICAgICAgICBjb25zdCBjYXRlZ29yeUlucHV0ID0gJGNhdGVnb3JpZXNUb0RlbGV0ZUlucHV0QmxvY2tcbiAgICAgICAgICAuZGF0YSgncHJvdG90eXBlJylcbiAgICAgICAgICAucmVwbGFjZSgvX19uYW1lX18vZywgJGNhdGVnb3JpZXNUb0RlbGV0ZUlucHV0QmxvY2suY2hpbGRyZW4oKS5sZW5ndGgpO1xuXG4gICAgICAgIGNvbnN0ICRpdGVtID0gJCgkLnBhcnNlSFRNTChjYXRlZ29yeUlucHV0KVswXSk7XG4gICAgICAgICRpdGVtLnZhbChjYXRlZ29yeUlkKTtcblxuICAgICAgICAkY2F0ZWdvcmllc1RvRGVsZXRlSW5wdXRCbG9jay5hcHBlbmQoJGl0ZW0pO1xuXG4gICAgICAgIGNvbnN0ICRmb3JtID0gJGRlbGV0ZUNhdGVnb3JpZXNNb2RhbC5maW5kKCdmb3JtJyk7XG5cbiAgICAgICAgJGZvcm0uYXR0cignYWN0aW9uJywgJGJ1dHRvbi5kYXRhKCdjYXRlZ29yeS1kZWxldGUtdXJsJykpO1xuICAgICAgICAkZm9ybS5zdWJtaXQoKTtcbiAgICAgIH0pO1xuICAgIH0pO1xuICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uIGhhbmRsZXMgc3VibWl0dGluZyBvZiByb3cgYWN0aW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLXN1Ym1pdC1yb3ctYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICBjb25zdCAkYnV0dG9uID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJGJ1dHRvbi5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcblxuICAgICAgaWYgKGNvbmZpcm1NZXNzYWdlLmxlbmd0aCAmJiAhY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBjb25zdCBtZXRob2QgPSAkYnV0dG9uLmRhdGEoJ21ldGhvZCcpO1xuICAgICAgY29uc3QgaXNHZXRPclBvc3RNZXRob2QgPSBbJ0dFVCcsICdQT1NUJ10uaW5jbHVkZXMobWV0aG9kKTtcblxuICAgICAgY29uc3QgJGZvcm0gPSAkKCc8Zm9ybT4nLCB7XG4gICAgICAgICdhY3Rpb24nOiAkYnV0dG9uLmRhdGEoJ3VybCcpLFxuICAgICAgICAnbWV0aG9kJzogaXNHZXRPclBvc3RNZXRob2QgPyBtZXRob2QgOiAnUE9TVCcsXG4gICAgICB9KS5hcHBlbmRUbygnYm9keScpO1xuXG4gICAgICBpZiAoIWlzR2V0T3JQb3N0TWV0aG9kKSB7XG4gICAgICAgICRmb3JtLmFwcGVuZCgkKCc8aW5wdXQ+Jywge1xuICAgICAgICAgICd0eXBlJzogJ19oaWRkZW4nLFxuICAgICAgICAgICduYW1lJzogJ19tZXRob2QnLFxuICAgICAgICAgICd2YWx1ZSc6IG1ldGhvZFxuICAgICAgICB9KSk7XG4gICAgICB9XG5cbiAgICAgICRmb3JtLnN1Ym1pdCgpO1xuICAgIH0pO1xuICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgQnVsa0FjdGlvblNlbGVjdENoZWNrYm94RXh0ZW5zaW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZCB3aXRoIGJ1bGsgYWN0aW9uIGNoZWNrYm94ZXMgaGFuZGxpbmcgZnVuY3Rpb25hbGl0eVxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgdGhpcy5faGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0KGdyaWQpO1xuICAgIHRoaXMuX2hhbmRsZUJ1bGtBY3Rpb25TZWxlY3RBbGxDaGVja2JveChncmlkKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIFwiU2VsZWN0IGFsbFwiIGJ1dHRvbiBpbiB0aGUgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oYW5kbGVCdWxrQWN0aW9uU2VsZWN0QWxsQ2hlY2tib3goZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NoYW5nZScsICcuanMtYnVsay1hY3Rpb24tc2VsZWN0LWFsbCcsIChlKSA9PiB7XG4gICAgICBjb25zdCAkY2hlY2tib3ggPSAkKGUuY3VycmVudFRhcmdldCk7XG5cbiAgICAgIGNvbnN0IGlzQ2hlY2tlZCA9ICRjaGVja2JveC5pcygnOmNoZWNrZWQnKTtcbiAgICAgIGlmIChpc0NoZWNrZWQpIHtcbiAgICAgICAgdGhpcy5fZW5hYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB0aGlzLl9kaXNhYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9XG5cbiAgICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9uLWNoZWNrYm94JykucHJvcCgnY2hlY2tlZCcsIGlzQ2hlY2tlZCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlcyBlYWNoIGJ1bGsgYWN0aW9uIGNoZWNrYm94IHNlbGVjdCBpbiB0aGUgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oYW5kbGVCdWxrQWN0aW9uQ2hlY2tib3hTZWxlY3QoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NoYW5nZScsICcuanMtYnVsay1hY3Rpb24tY2hlY2tib3gnLCAoKSA9PiB7XG4gICAgICBjb25zdCBjaGVja2VkUm93c0NvdW50ID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb24tY2hlY2tib3g6Y2hlY2tlZCcpLmxlbmd0aDtcblxuICAgICAgaWYgKGNoZWNrZWRSb3dzQ291bnQgPiAwKSB7XG4gICAgICAgIHRoaXMuX2VuYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGhpcy5fZGlzYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEVuYWJsZSBidWxrIGFjdGlvbnMgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2VuYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbnMtYnRuJykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gIH1cblxuICAvKipcbiAgICogRGlzYWJsZSBidWxrIGFjdGlvbnMgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2Rpc2FibGVCdWxrQWN0aW9uc0J0bihncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb25zLWJ0bicpLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCB0YWJsZURuRCBmcm9tIFwidGFibGVkbmQvZGlzdC9qcXVlcnkudGFibGVkbmQubWluXCI7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBDYXRlZ29yeVBvc2l0aW9uRXh0ZW5zaW9uIGV4dGVuZHMgR3JpZCB3aXRoIHJlb3JkZXJhYmxlIGNhdGVnb3J5IHBvc2l0aW9uc1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDYXRlZ29yeVBvc2l0aW9uRXh0ZW5zaW9uIHtcblxuICBjb25zdHJ1Y3RvcigpIHtcbiAgICByZXR1cm4ge1xuICAgICAgZXh0ZW5kOiAoZ3JpZCkgPT4gdGhpcy5leHRlbmQoZ3JpZCksXG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICB0aGlzLmdyaWQgPSBncmlkO1xuXG4gICAgdGhpcy5fYWRkSWRzVG9HcmlkVGFibGVSb3dzKCk7XG5cbiAgICBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1ncmlkLXRhYmxlJykudGFibGVEbkQoe1xuICAgICAgZHJhZ0hhbmRsZTogJy5qcy1kcmFnLWhhbmRsZScsXG4gICAgICBvbkRyYWdTdGFydDogKCkgPT4ge1xuICAgICAgICB0aGlzLm9yaWdpbmFsUG9zaXRpb25zID0gZGVjb2RlVVJJQ29tcG9uZW50KCQudGFibGVEbkQuc2VyaWFsaXplKCkpO1xuICAgICAgfSxcbiAgICAgIG9uRHJvcDogKHRhYmxlLCByb3cpID0+IHRoaXMuX2hhbmRsZUNhdGVnb3J5UG9zaXRpb25DaGFuZ2Uocm93KSxcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBXaGVuIHBvc2l0aW9uIGlzIGNoYW5nZWQgaGFuZGxlIHVwZGF0ZVxuICAgKlxuICAgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSByb3dcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oYW5kbGVDYXRlZ29yeVBvc2l0aW9uQ2hhbmdlKHJvdykge1xuICAgIGNvbnN0IHBvc2l0aW9ucyA9IGRlY29kZVVSSUNvbXBvbmVudCgkLnRhYmxlRG5ELnNlcmlhbGl6ZSgpKTtcbiAgICBjb25zdCB3YXkgPSAodGhpcy5vcmlnaW5hbFBvc2l0aW9ucy5pbmRleE9mKHJvdy5pZCkgPCBwb3NpdGlvbnMuaW5kZXhPZihyb3cuaWQpKSA/IDEgOiAwO1xuXG4gICAgY29uc3QgJGNhdGVnb3J5UG9zaXRpb25Db250YWluZXIgPSAkKHJvdykuZmluZCgnLmpzLScgKyB0aGlzLmdyaWQuZ2V0SWQoKSArICctcG9zaXRpb246Zmlyc3QnKTtcblxuICAgIGNvbnN0IGNhdGVnb3J5SWQgPSAkY2F0ZWdvcnlQb3NpdGlvbkNvbnRhaW5lci5kYXRhKCdpZCcpO1xuICAgIGNvbnN0IGNhdGVnb3J5UGFyZW50SWQgPSAkY2F0ZWdvcnlQb3NpdGlvbkNvbnRhaW5lci5kYXRhKCdpZC1wYXJlbnQnKTtcbiAgICBjb25zdCBwb3NpdGlvblVwZGF0ZVVybCA9ICRjYXRlZ29yeVBvc2l0aW9uQ29udGFpbmVyLmRhdGEoJ3Bvc2l0aW9uLXVwZGF0ZS11cmwnKTtcblxuICAgIGxldCBwYXJhbXMgPSBwb3NpdGlvbnMucmVwbGFjZShuZXcgUmVnRXhwKHRoaXMuZ3JpZC5nZXRJZCgpICsgJ19ncmlkX3RhYmxlJywgJ2cnKSwgJ2NhdGVnb3J5Jyk7XG5cbiAgICBsZXQgcXVlcnlQYXJhbXMgPSB7XG4gICAgICBpZF9jYXRlZ29yeV9wYXJlbnQ6IGNhdGVnb3J5UGFyZW50SWQsXG4gICAgICBpZF9jYXRlZ29yeV90b19tb3ZlOiBjYXRlZ29yeUlkLFxuICAgICAgd2F5OiB3YXksXG4gICAgICBhamF4OiAxLFxuICAgICAgYWN0aW9uOiAndXBkYXRlUG9zaXRpb25zJ1xuICAgIH07XG5cbiAgICBpZiAocG9zaXRpb25zLmluZGV4T2YoJ18wJicpICE9PSAtMSkge1xuICAgICAgcXVlcnlQYXJhbXMuZm91bmRfZmlyc3QgPSAxO1xuICAgIH1cblxuICAgIHBhcmFtcyArPSAnJicgKyAkLnBhcmFtKHF1ZXJ5UGFyYW1zKTtcblxuICAgIHRoaXMuX3VwZGF0ZUNhdGVnb3J5UG9zaXRpb24ocG9zaXRpb25VcGRhdGVVcmwsIHBhcmFtcyk7XG4gIH1cblxuICAvKipcbiAgICogQWRkIElEJ3MgdG8gR3JpZCB0YWJsZSByb3dzIHRvIG1ha2UgdGFibGVEbkQub25Ecm9wKCkgZnVuY3Rpb24gd29yay5cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9hZGRJZHNUb0dyaWRUYWJsZVJvd3MoKSB7XG4gICAgdGhpcy5ncmlkLmdldENvbnRhaW5lcigpXG4gICAgICAuZmluZCgnLmpzLWdyaWQtdGFibGUnKVxuICAgICAgLmZpbmQoJy5qcy0nICsgdGhpcy5ncmlkLmdldElkKCkgKyAnLXBvc2l0aW9uJylcbiAgICAgIC5lYWNoKChpbmRleCwgcG9zaXRpb25XcmFwcGVyKSA9PiB7XG4gICAgICAgIGNvbnN0ICRwb3NpdGlvbldyYXBwZXIgPSAkKHBvc2l0aW9uV3JhcHBlcik7XG5cbiAgICAgICAgY29uc3QgY2F0ZWdvcnlJZCA9ICRwb3NpdGlvbldyYXBwZXIuZGF0YSgnaWQnKTtcbiAgICAgICAgY29uc3QgY2F0ZWdvcnlQYXJlbnRJZCA9ICRwb3NpdGlvbldyYXBwZXIuZGF0YSgnaWQtcGFyZW50Jyk7XG4gICAgICAgIGNvbnN0IHBvc2l0aW9uID0gJHBvc2l0aW9uV3JhcHBlci5kYXRhKCdwb3NpdGlvbicpO1xuXG4gICAgICAgIGNvbnN0IGlkID0gJ3RyXycgKyBjYXRlZ29yeVBhcmVudElkICsgJ18nICsgY2F0ZWdvcnlJZCArICdfJyArIHBvc2l0aW9uO1xuXG4gICAgICAgICRwb3NpdGlvbldyYXBwZXIuY2xvc2VzdCgndHInKS5hdHRyKCdpZCcsIGlkKTtcbiAgICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFVwZGF0ZSBjYXRlZ29yaWVzIGxpc3Rpbmcgd2l0aCBuZXcgcG9zaXRpb25zXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdXBkYXRlQ2F0ZWdvcnlJZHNBbmRQb3NpdGlvbnMoKSB7XG4gICAgdGhpcy5ncmlkLmdldENvbnRhaW5lcigpXG4gICAgICAuZmluZCgnLmpzLWdyaWQtdGFibGUnKVxuICAgICAgLmZpbmQoJy5qcy0nICsgdGhpcy5ncmlkLmdldElkKCkgKyAnLXBvc2l0aW9uJylcbiAgICAgIC5lYWNoKChpbmRleCwgcG9zaXRpb25XcmFwcGVyKSA9PiB7XG4gICAgICAgIGNvbnN0ICRwb3NpdGlvbldyYXBwZXIgPSAkKHBvc2l0aW9uV3JhcHBlcik7XG4gICAgICAgIGNvbnN0ICRyb3cgPSAkcG9zaXRpb25XcmFwcGVyLmNsb3Nlc3QoJ3RyJyk7XG5cbiAgICAgICAgY29uc3Qgb2Zmc2V0ID0gJHBvc2l0aW9uV3JhcHBlci5kYXRhKCdwYWdpbmF0aW9uLW9mZnNldCcpO1xuICAgICAgICBjb25zdCBuZXdQb3NpdGlvbiA9IG9mZnNldCA+IDAgPyBpbmRleCArIG9mZnNldCA6IGluZGV4O1xuXG4gICAgICAgIGNvbnN0IG9sZElkID0gJHJvdy5hdHRyKCdpZCcpO1xuICAgICAgICAkcm93LmF0dHIoJ2lkJywgb2xkSWQucmVwbGFjZSgvX1swLTldJC9nLCAnXycgKyBuZXdQb3NpdGlvbikpO1xuXG4gICAgICAgICRwb3NpdGlvbldyYXBwZXIuZmluZCgnLmpzLXBvc2l0aW9uJykudGV4dChuZXdQb3NpdGlvbiArIDEpO1xuICAgICAgICAkcG9zaXRpb25XcmFwcGVyLmRhdGEoJ3Bvc2l0aW9uJywgbmV3UG9zaXRpb24pO1xuICAgICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogUHJvY2VzcyBjYXRlZ29yaWVzIHBvc2l0aW9ucyB1cGRhdGVcbiAgICpcbiAgICogQHBhcmFtIHtTdHJpbmd9IHVybFxuICAgKiBAcGFyYW0ge1N0cmluZ30gcGFyYW1zXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdXBkYXRlQ2F0ZWdvcnlQb3NpdGlvbih1cmwsIHBhcmFtcykge1xuICAgICQucG9zdCh7XG4gICAgICB1cmw6IHVybCxcbiAgICAgIGhlYWRlcnM6IHtcbiAgICAgICAgJ2NhY2hlLWNvbnRyb2wnOiAnbm8tY2FjaGUnXG4gICAgICB9LFxuICAgICAgZGF0YTogcGFyYW1zXG4gICAgfSkudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgIHJlc3BvbnNlID0gSlNPTi5wYXJzZShyZXNwb25zZSk7XG5cbiAgICAgIGlmICh0eXBlb2YgcmVzcG9uc2UubWVzc2FnZSAhPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgc2hvd1N1Y2Nlc3NNZXNzYWdlKHJlc3BvbnNlLm1lc3NhZ2UpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgLy8gdXNlIGxlZ2FjeSBlcnJvclxuICAgICAgICAvLyBAdG9kbzogdXBkYXRlIHdoZW4gYWxsIGNhdGVnb3J5IGNvbnRyb2xsZXIgaXMgbWlncmF0ZWQgdG8gc3ltZm9ueVxuICAgICAgICBzaG93RXJyb3JNZXNzYWdlKHJlc3BvbnNlLmVycm9ycyk7XG4gICAgICB9XG5cbiAgICAgIHRoaXMuX3VwZGF0ZUNhdGVnb3J5SWRzQW5kUG9zaXRpb25zKCk7XG4gICAgfSk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBBc3luY1RvZ2dsZUNvbHVtbkV4dGVuc2lvbiBzdWJtaXRzIHRvZ2dsZSBhY3Rpb24gdXNpbmcgQUpBWFxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBBc3luY1RvZ2dsZUNvbHVtbkV4dGVuc2lvbiB7XG5cbiAgY29uc3RydWN0b3IoKSB7XG4gICAgcmV0dXJuIHtcbiAgICAgIGV4dGVuZDogKGdyaWQpID0+IHRoaXMuZXh0ZW5kKGdyaWQpLFxuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtZ3JpZC10YWJsZScpLm9uKCdjbGljaycsICcucHMtdG9nZ2xhYmxlLXJvdycsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgY29uc3QgJGJ1dHRvbiA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG5cbiAgICAgICQucG9zdCh7XG4gICAgICAgIHVybDogJGJ1dHRvbi5kYXRhKCd0b2dnbGUtdXJsJyksXG4gICAgICB9KS50aGVuKChyZXNwb25zZSkgPT4ge1xuICAgICAgICBpZiAocmVzcG9uc2Uuc3RhdHVzKSB7XG4gICAgICAgICAgc2hvd1N1Y2Nlc3NNZXNzYWdlKHJlc3BvbnNlLm1lc3NhZ2UpO1xuXG4gICAgICAgICAgdGhpcy5fdG9nZ2xlQnV0dG9uRGlzcGxheSgkYnV0dG9uKTtcblxuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHNob3dFcnJvck1lc3NhZ2UocmVzcG9uc2UubWVzc2FnZSk7XG4gICAgICB9KTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBUb2dnbGUgYnV0dG9uIGRpc3BsYXkgZnJvbSBlbmFibGVkIHRvIGRpc2FibGVkIGFuZCBvdGhlciB3YXkgYXJvdW5kXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkYnV0dG9uXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdG9nZ2xlQnV0dG9uRGlzcGxheSgkYnV0dG9uKSB7XG4gICAgY29uc3QgaXNBY3RpdmUgPSAkYnV0dG9uLmhhc0NsYXNzKCdncmlkLXRvZ2dsZXItaWNvbi12YWxpZCcpO1xuXG4gICAgY29uc3QgY2xhc3NUb0FkZCA9IGlzQWN0aXZlID8gJ2dyaWQtdG9nZ2xlci1pY29uLW5vdC12YWxpZCcgOiAnZ3JpZC10b2dnbGVyLWljb24tdmFsaWQnO1xuICAgIGNvbnN0IGNsYXNzVG9SZW1vdmUgPSBpc0FjdGl2ZSA/ICdncmlkLXRvZ2dsZXItaWNvbi12YWxpZCcgOiAnZ3JpZC10b2dnbGVyLWljb24tbm90LXZhbGlkJztcbiAgICBjb25zdCBpY29uID0gaXNBY3RpdmUgPyAnY2xlYXInIDogJ2NoZWNrJztcblxuICAgICRidXR0b24ucmVtb3ZlQ2xhc3MoY2xhc3NUb1JlbW92ZSk7XG4gICAgJGJ1dHRvbi5hZGRDbGFzcyhjbGFzc1RvQWRkKTtcbiAgICAkYnV0dG9uLnRleHQoaWNvbik7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggZXhwb3J0aW5nIHF1ZXJ5IHRvIFNRTCBNYW5hZ2VyXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRIZWFkZXJDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWNvbW1vbl9zaG93X3F1ZXJ5LWdyaWQtYWN0aW9uJywgKCkgPT4gdGhpcy5fb25TaG93U3FsUXVlcnlDbGljayhncmlkKSk7XG4gICAgZ3JpZC5nZXRIZWFkZXJDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWNvbW1vbl9leHBvcnRfc3FsX21hbmFnZXItZ3JpZC1hY3Rpb24nLCAoKSA9PiB0aGlzLl9vbkV4cG9ydFNxbE1hbmFnZXJDbGljayhncmlkKSk7XG4gIH1cblxuICAvKipcbiAgICogSW52b2tlZCB3aGVuIGNsaWNraW5nIG9uIHRoZSBcInNob3cgc3FsIHF1ZXJ5XCIgdG9vbGJhciBidXR0b25cbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25TaG93U3FsUXVlcnlDbGljayhncmlkKSB7XG4gICAgY29uc3QgJHNxbE1hbmFnZXJGb3JtID0gJCgnIycgKyBncmlkLmdldElkKCkgKyAnX2NvbW1vbl9zaG93X3F1ZXJ5X21vZGFsX2Zvcm0nKTtcbiAgICB0aGlzLl9maWxsRXhwb3J0Rm9ybSgkc3FsTWFuYWdlckZvcm0sIGdyaWQpO1xuXG4gICAgY29uc3QgJG1vZGFsID0gJCgnIycgKyBncmlkLmdldElkKCkgKyAnX2dyaWRfY29tbW9uX3Nob3dfcXVlcnlfbW9kYWwnKTtcbiAgICAkbW9kYWwubW9kYWwoJ3Nob3cnKTtcblxuICAgICRtb2RhbC5vbignY2xpY2snLCAnLmJ0bi1zcWwtc3VibWl0JywgKCkgPT4gJHNxbE1hbmFnZXJGb3JtLnN1Ym1pdCgpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbnZva2VkIHdoZW4gY2xpY2tpbmcgb24gdGhlIFwiZXhwb3J0IHRvIHRoZSBzcWwgcXVlcnlcIiB0b29sYmFyIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vbkV4cG9ydFNxbE1hbmFnZXJDbGljayhncmlkKSB7XG4gICAgY29uc3QgJHNxbE1hbmFnZXJGb3JtID0gJCgnIycgKyBncmlkLmdldElkKCkgKyAnX2NvbW1vbl9zaG93X3F1ZXJ5X21vZGFsX2Zvcm0nKTtcblxuICAgIHRoaXMuX2ZpbGxFeHBvcnRGb3JtKCRzcWxNYW5hZ2VyRm9ybSwgZ3JpZCk7XG5cbiAgICAkc3FsTWFuYWdlckZvcm0uc3VibWl0KCk7XG4gIH1cblxuICAvKipcbiAgICogRmlsbCBleHBvcnQgZm9ybSB3aXRoIFNRTCBhbmQgaXQncyBuYW1lXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkc3FsTWFuYWdlckZvcm1cbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZmlsbEV4cG9ydEZvcm0oJHNxbE1hbmFnZXJGb3JtLCBncmlkKSB7XG4gICAgY29uc3QgcXVlcnkgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1ncmlkLXRhYmxlJykuZGF0YSgncXVlcnknKTtcblxuICAgICRzcWxNYW5hZ2VyRm9ybS5maW5kKCd0ZXh0YXJlYVtuYW1lPVwic3FsXCJdJykudmFsKHF1ZXJ5KTtcbiAgICAkc3FsTWFuYWdlckZvcm0uZmluZCgnaW5wdXRbbmFtZT1cIm5hbWVcIl0nKS52YWwodGhpcy5fZ2V0TmFtZUZyb21CcmVhZGNydW1iKCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBleHBvcnQgbmFtZSBmcm9tIHBhZ2UncyBicmVhZGNydW1iXG4gICAqXG4gICAqIEByZXR1cm4ge1N0cmluZ31cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXROYW1lRnJvbUJyZWFkY3J1bWIoKSB7XG4gICAgY29uc3QgJGJyZWFkY3J1bWJzID0gJCgnLmhlYWRlci10b29sYmFyJykuZmluZCgnLmJyZWFkY3J1bWItaXRlbScpO1xuICAgIGxldCBuYW1lID0gJyc7XG5cbiAgICAkYnJlYWRjcnVtYnMuZWFjaCgoaSwgaXRlbSkgPT4ge1xuICAgICAgY29uc3QgJGJyZWFkY3J1bWIgPSAkKGl0ZW0pO1xuXG4gICAgICBjb25zdCBicmVhZGNydW1iVGl0bGUgPSAwIDwgJGJyZWFkY3J1bWIuZmluZCgnYScpLmxlbmd0aCA/XG4gICAgICAgICRicmVhZGNydW1iLmZpbmQoJ2EnKS50ZXh0KCkgOlxuICAgICAgICAkYnJlYWRjcnVtYi50ZXh0KCk7XG5cbiAgICAgIGlmICgwIDwgbmFtZS5sZW5ndGgpIHtcbiAgICAgICAgbmFtZSA9IG5hbWUuY29uY2F0KCcgPiAnKTtcbiAgICAgIH1cblxuICAgICAgbmFtZSA9IG5hbWUuY29uY2F0KGJyZWFkY3J1bWJUaXRsZSk7XG4gICAgfSk7XG5cbiAgICByZXR1cm4gbmFtZTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IHJlc2V0U2VhcmNoIGZyb20gJy4uLy4uLy4uL2FwcC91dGlscy9yZXNldF9zZWFyY2gnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIGZpbHRlcnMgcmVzZXR0aW5nXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEZpbHRlcnNSZXNldEV4dGVuc2lvbiB7XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtcmVzZXQtc2VhcmNoJywgKGV2ZW50KSA9PiB7XG4gICAgICByZXNldFNlYXJjaCgkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ3VybCcpLCAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ3JlZGlyZWN0JykpO1xuICAgIH0pO1xuICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiBoYW5kbGVzIGxpbmsgcm93IGFjdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWxpbmstcm93LWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgY29uZmlybU1lc3NhZ2UgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ2NvbmZpcm0tbWVzc2FnZScpO1xuXG4gICAgICBpZiAoY29uZmlybU1lc3NhZ2UubGVuZ3RoICYmICFjb25maXJtKGNvbmZpcm1NZXNzYWdlKSkge1xuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG4vKipcbiAqIENsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggXCJMaXN0IHJlbG9hZFwiIGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX3JlZnJlc2hfbGlzdC1ncmlkLWFjdGlvbicsICgpID0+IHtcbiAgICAgIGxvY2F0aW9uLnJlbG9hZCgpO1xuICAgIH0pO1xuICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgVGFibGVTb3J0aW5nIGZyb20gJy4uLy4uLy4uL2FwcC91dGlscy90YWJsZS1zb3J0aW5nJztcblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiTGlzdCByZWxvYWRcIiBhY3Rpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU29ydGluZ0V4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgY29uc3QgJHNvcnRhYmxlVGFibGUgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJ3RhYmxlLnRhYmxlJyk7XG5cbiAgICBuZXcgVGFibGVTb3J0aW5nKCRzb3J0YWJsZVRhYmxlKS5hdHRhY2goKTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIEhhbmRsZXMgc3VibWl0IG9mIGdyaWQgYWN0aW9uc1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTdWJtaXRCdWxrQWN0aW9uRXh0ZW5zaW9uIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgcmV0dXJuIHtcbiAgICAgIGV4dGVuZDogKGdyaWQpID0+IHRoaXMuZXh0ZW5kKGdyaWQpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWQgd2l0aCBidWxrIGFjdGlvbiBzdWJtaXR0aW5nXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtYnVsay1hY3Rpb24tc3VibWl0LWJ0bicsIChldmVudCkgPT4ge1xuICAgICAgdGhpcy5zdWJtaXQoZXZlbnQsIGdyaWQpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZSBidWxrIGFjdGlvbiBzdWJtaXR0aW5nXG4gICAqXG4gICAqIEBwYXJhbSB7RXZlbnR9IGV2ZW50XG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgc3VibWl0KGV2ZW50LCBncmlkKSB7XG4gICAgY29uc3QgJHN1Ym1pdEJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgY29uc3QgY29uZmlybU1lc3NhZ2UgPSAkc3VibWl0QnRuLmRhdGEoJ2NvbmZpcm0tbWVzc2FnZScpO1xuXG4gICAgaWYgKHR5cGVvZiBjb25maXJtTWVzc2FnZSAhPT0gXCJ1bmRlZmluZWRcIiAmJiAwIDwgY29uZmlybU1lc3NhZ2UubGVuZ3RoICYmICFjb25maXJtKGNvbmZpcm1NZXNzYWdlKSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGNvbnN0ICRmb3JtID0gJCgnIycgKyBncmlkLmdldElkKCkgKyAnX2ZpbHRlcl9mb3JtJyk7XG5cbiAgICAkZm9ybS5hdHRyKCdhY3Rpb24nLCAkc3VibWl0QnRuLmRhdGEoJ2Zvcm0tdXJsJykpO1xuICAgICRmb3JtLmF0dHIoJ21ldGhvZCcsICRzdWJtaXRCdG4uZGF0YSgnZm9ybS1tZXRob2QnKSk7XG4gICAgJGZvcm0uc3VibWl0KCk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBpcyByZXNwb25zaWJsZSBmb3IgaGFuZGxpbmcgR3JpZCBldmVudHNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgR3JpZCB7XG4gIC8qKlxuICAgKiBHcmlkIGlkXG4gICAqXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBpZFxuICAgKi9cbiAgY29uc3RydWN0b3IoaWQpIHtcbiAgICB0aGlzLmlkID0gaWQ7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJCgnIycgKyB0aGlzLmlkICsgJ19ncmlkJyk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGdyaWQgaWRcbiAgICpcbiAgICogQHJldHVybnMge3N0cmluZ31cbiAgICovXG4gIGdldElkKCkge1xuICAgIHJldHVybiB0aGlzLmlkO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGNvbnRhaW5lclxuICAgKlxuICAgKiBAcmV0dXJucyB7alF1ZXJ5fVxuICAgKi9cbiAgZ2V0Q29udGFpbmVyKCkge1xuICAgIHJldHVybiB0aGlzLiRjb250YWluZXI7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGdyaWQgaGVhZGVyIGNvbnRhaW5lclxuICAgKlxuICAgKiBAcmV0dXJucyB7alF1ZXJ5fVxuICAgKi9cbiAgZ2V0SGVhZGVyQ29udGFpbmVyKCkge1xuICAgIHJldHVybiB0aGlzLiRjb250YWluZXIuY2xvc2VzdCgnLmpzLWdyaWQtcGFuZWwnKS5maW5kKCcuanMtZ3JpZC1oZWFkZXInKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZCB3aXRoIGV4dGVybmFsIGV4dGVuc2lvbnNcbiAgICpcbiAgICogQHBhcmFtIHtvYmplY3R9IGV4dGVuc2lvblxuICAgKi9cbiAgYWRkRXh0ZW5zaW9uKGV4dGVuc2lvbikge1xuICAgIGV4dGVuc2lvbi5leHRlbmQodGhpcyk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuY2xhc3MgVHJhbnNsYXRhYmxlSW5wdXQge1xuICAgIGNvbnN0cnVjdG9yKG9wdGlvbnMpIHtcbiAgICAgICAgb3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG5cbiAgICAgICAgdGhpcy5sb2NhbGVJdGVtU2VsZWN0b3IgPSBvcHRpb25zLmxvY2FsZUl0ZW1TZWxlY3RvciB8fMKgJy5qcy1sb2NhbGUtaXRlbSc7XG4gICAgICAgIHRoaXMubG9jYWxlQnV0dG9uU2VsZWN0b3IgPSBvcHRpb25zLmxvY2FsZUJ1dHRvblNlbGVjdG9yIHx8wqAnLmpzLWxvY2FsZS1idG4nO1xuICAgICAgICB0aGlzLmxvY2FsZUlucHV0U2VsZWN0b3IgPSBvcHRpb25zLmxvY2FsZUlucHV0U2VsZWN0b3IgfHzCoCcuanMtbG9jYWxlLWlucHV0JztcblxuICAgICAgICAkKCdib2R5Jykub24oJ2NsaWNrJywgdGhpcy5sb2NhbGVJdGVtU2VsZWN0b3IsIHRoaXMudG9nZ2xlSW5wdXRzLmJpbmQodGhpcykpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFRvZ2dsZSBhbGwgdHJhbnNsYXRhYmxlIGlucHV0cyBpbiBmb3JtIGluIHdoaWNoIGxvY2FsZSB3YXMgY2hhbmdlZFxuICAgICAqXG4gICAgICogQHBhcmFtIHtFdmVudH0gZXZlbnRcbiAgICAgKi9cbiAgICB0b2dnbGVJbnB1dHMoZXZlbnQpIHtcbiAgICAgICAgY29uc3QgbG9jYWxlSXRlbSA9ICQoZXZlbnQudGFyZ2V0KTtcbiAgICAgICAgY29uc3QgZm9ybSA9IGxvY2FsZUl0ZW0uY2xvc2VzdCgnZm9ybScpO1xuICAgICAgICBjb25zdCBzZWxlY3RlZExvY2FsZSA9IGxvY2FsZUl0ZW0uZGF0YSgnbG9jYWxlJyk7XG5cbiAgICAgICAgZm9ybS5maW5kKHRoaXMubG9jYWxlQnV0dG9uU2VsZWN0b3IpLnRleHQoc2VsZWN0ZWRMb2NhbGUpO1xuICAgICAgICBmb3JtLmZpbmQodGhpcy5sb2NhbGVJbnB1dFNlbGVjdG9yKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgICAgIGZvcm0uZmluZCh0aGlzLmxvY2FsZUlucHV0U2VsZWN0b3IrJy5qcy1sb2NhbGUtJyArIHNlbGVjdGVkTG9jYWxlKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBUcmFuc2xhdGFibGVJbnB1dDtcbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBHcmlkIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9ncmlkJztcbmltcG9ydCBGaWx0ZXJzUmVzZXRFeHRlbnNpb24gZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1yZXNldC1leHRlbnNpb25cIjtcbmltcG9ydCBTb3J0aW5nRXh0ZW5zaW9uIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uXCI7XG5pbXBvcnQgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2V4cG9ydC10by1zcWwtbWFuYWdlci1leHRlbnNpb25cIjtcbmltcG9ydCBSZWxvYWRMaXN0RXh0ZW5zaW9uIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvblwiO1xuaW1wb3J0IEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbiBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9idWxrLWFjdGlvbi1jaGVja2JveC1leHRlbnNpb25cIjtcbmltcG9ydCBTdWJtaXRCdWxrRXh0ZW5zaW9uIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1idWxrLWFjdGlvbi1leHRlbnNpb25cIjtcbmltcG9ydCBTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24gZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYWN0aW9uL3Jvdy9zdWJtaXQtcm93LWFjdGlvbi1leHRlbnNpb25cIjtcbmltcG9ydCBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2xpbmstcm93LWFjdGlvbi1leHRlbnNpb25cIjtcbmltcG9ydCBDYXRlZ29yeVBvc2l0aW9uRXh0ZW5zaW9uIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi9jYXRhbG9nL2NhdGVnb3J5LXBvc2l0aW9uLWV4dGVuc2lvblwiO1xuaW1wb3J0IEFzeW5jVG9nZ2xlQ29sdW1uRXh0ZW5zaW9uIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi9jb21tb24vYXN5bmMtdG9nZ2xlLWNvbHVtbi1leHRlbnNpb25cIjtcbmltcG9ydCBEZWxldGVDYXRlZ29yeVJvd0FjdGlvbkV4dGVuc2lvbiBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9hY3Rpb24vcm93L2NhdGVnb3J5L2RlbGV0ZS1jYXRlZ29yeS1yb3ctYWN0aW9uLWV4dGVuc2lvblwiO1xuaW1wb3J0IERlbGV0ZUNhdGVnb3JpZXNCdWxrQWN0aW9uRXh0ZW5zaW9uIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9idWxrL2NhdGVnb3J5L2RlbGV0ZS1jYXRlZ29yaWVzLWJ1bGstYWN0aW9uLWV4dGVuc2lvblwiO1xuaW1wb3J0IFRyYW5zbGF0YWJsZUlucHV0IGZyb20gXCIuLi8uLi9jb21wb25lbnRzL3RyYW5zbGF0YWJsZS1pbnB1dFwiO1xuaW1wb3J0IENob2ljZVRhYmxlIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2Nob2ljZS10YWJsZVwiO1xuaW1wb3J0IFRleHRXaXRoTGVuZ3RoQ291bnRlciBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9mb3JtL3RleHQtd2l0aC1sZW5ndGgtY291bnRlclwiO1xuaW1wb3J0IE5hbWVUb0xpbmtSZXdyaXRlQ29waWVyIGZyb20gXCIuL25hbWUtdG8tbGluay1yZXdyaXRlLWNvcGllclwiO1xuaW1wb3J0IENob2ljZVRyZWUgZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZm9ybS9jaG9pY2UtdHJlZVwiO1xuaW1wb3J0IEZvcm1TdWJtaXRCdXR0b24gZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZm9ybS1zdWJtaXQtYnV0dG9uXCI7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gIGNvbnN0IGNhdGVnb3JpZXNHcmlkID0gbmV3IEdyaWQoJ2NhdGVnb3JpZXMnKTtcblxuICBjYXRlZ29yaWVzR3JpZC5hZGRFeHRlbnNpb24obmV3IEZpbHRlcnNSZXNldEV4dGVuc2lvbigpKTtcbiAgY2F0ZWdvcmllc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTb3J0aW5nRXh0ZW5zaW9uKCkpO1xuICBjYXRlZ29yaWVzR3JpZC5hZGRFeHRlbnNpb24obmV3IENhdGVnb3J5UG9zaXRpb25FeHRlbnNpb24oKSk7XG4gIGNhdGVnb3JpZXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uKCkpO1xuICBjYXRlZ29yaWVzR3JpZC5hZGRFeHRlbnNpb24obmV3IFJlbG9hZExpc3RFeHRlbnNpb24oKSk7XG4gIGNhdGVnb3JpZXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uKCkpO1xuICBjYXRlZ29yaWVzR3JpZC5hZGRFeHRlbnNpb24obmV3IFN1Ym1pdEJ1bGtFeHRlbnNpb24oKSk7XG4gIGNhdGVnb3JpZXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uKCkpO1xuICBjYXRlZ29yaWVzR3JpZC5hZGRFeHRlbnNpb24obmV3IExpbmtSb3dBY3Rpb25FeHRlbnNpb24oKSk7XG4gIGNhdGVnb3JpZXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgQXN5bmNUb2dnbGVDb2x1bW5FeHRlbnNpb24oKSk7XG4gIGNhdGVnb3JpZXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgRGVsZXRlQ2F0ZWdvcnlSb3dBY3Rpb25FeHRlbnNpb24oKSk7XG4gIGNhdGVnb3JpZXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgRGVsZXRlQ2F0ZWdvcmllc0J1bGtBY3Rpb25FeHRlbnNpb24oKSk7XG5cbiAgbmV3IFRyYW5zbGF0YWJsZUlucHV0KCk7XG4gIG5ldyBDaG9pY2VUYWJsZSgpO1xuICBuZXcgVGV4dFdpdGhMZW5ndGhDb3VudGVyKCk7XG4gIG5ldyBOYW1lVG9MaW5rUmV3cml0ZUNvcGllcigpO1xuICBuZXcgRm9ybVN1Ym1pdEJ1dHRvbigpO1xuXG4gIG5ldyBDaG9pY2VUcmVlKCcjY2F0ZWdvcnlfaWRfcGFyZW50Jyk7XG4gIG5ldyBDaG9pY2VUcmVlKCcjY2F0ZWdvcnlfc2hvcF9hc3NvY2lhdGlvbicpLmVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCk7XG5cbiAgbmV3IENob2ljZVRyZWUoJyNyb290X2NhdGVnb3J5X2lkX3BhcmVudCcpO1xuICBuZXcgQ2hvaWNlVHJlZSgnI3Jvb3RfY2F0ZWdvcnlfc2hvcF9hc3NvY2lhdGlvbicpLmVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCk7XG59KTtcbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbi8qKlxuICogQ29waWVzIG5hbWUgb2YgY2F0ZWdvcnkgdG8gbGluayByZXdyaXRlIGlucHV0LlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBOYW1lVG9MaW5rUmV3cml0ZUNvcGllciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIFsnY2F0ZWdvcnknLCAncm9vdF9jYXRlZ29yeSddLmZvckVhY2goKGNhdGVnb3J5VHlwZSkgPT4ge1xuICAgICAgY29uc3QgJGNhdGVnb3J5Rm9ybSA9ICQoYGZvcm1bbmFtZT1cIiR7Y2F0ZWdvcnlUeXBlfVwiXWApO1xuXG4gICAgICBpZiAoMCA9PT0gICRjYXRlZ29yeUZvcm0ubGVuZ3RoKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgJGNhdGVnb3J5Rm9ybS5vbignaW5wdXQnLCBgaW5wdXRbbmFtZV49XCIke2NhdGVnb3J5VHlwZX1bbmFtZV1cIl1gLCAoZXZlbnQpID0+IHtcbiAgICAgICAgY29uc3QgJG5hbWVJbnB1dCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICAgIGNvbnN0IGxhbmdJZCA9ICRuYW1lSW5wdXQuY2xvc2VzdCgnLmpzLWxvY2FsZS1pbnB1dCcpLmRhdGEoJ2xhbmctaWQnKTtcblxuICAgICAgICAkY2F0ZWdvcnlGb3JtXG4gICAgICAgICAgLmZpbmQoYGlucHV0W25hbWU9XCIke2NhdGVnb3J5VHlwZX1bbGlua19yZXdyaXRlXVske2xhbmdJZH1dXCJdYClcbiAgICAgICAgICAudmFsKHN0cjJ1cmwoJG5hbWVJbnB1dC52YWwoKSwgJ1VURi04JykpO1xuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cbn1cbiIsIi8qISBqcXVlcnkudGFibGVkbmQuanMgMzAtMTItMjAxNyAqL1xuIWZ1bmN0aW9uKGEsYixjLGQpe3ZhciBlPVwidG91Y2hzdGFydCBtb3VzZWRvd25cIixmPVwidG91Y2htb3ZlIG1vdXNlbW92ZVwiLGc9XCJ0b3VjaGVuZCBtb3VzZXVwXCI7YShjKS5yZWFkeShmdW5jdGlvbigpe2Z1bmN0aW9uIGIoYSl7Zm9yKHZhciBiPXt9LGM9YS5tYXRjaCgvKFteOzpdKykvZyl8fFtdO2MubGVuZ3RoOyliW2Muc2hpZnQoKV09Yy5zaGlmdCgpLnRyaW0oKTtyZXR1cm4gYn1hKFwidGFibGVcIikuZWFjaChmdW5jdGlvbigpe1wiZG5kXCI9PT1hKHRoaXMpLmRhdGEoXCJ0YWJsZVwiKSYmYSh0aGlzKS50YWJsZURuRCh7b25EcmFnU3R5bGU6YSh0aGlzKS5kYXRhKFwib25kcmFnc3R5bGVcIikmJmIoYSh0aGlzKS5kYXRhKFwib25kcmFnc3R5bGVcIikpfHxudWxsLG9uRHJvcFN0eWxlOmEodGhpcykuZGF0YShcIm9uZHJvcHN0eWxlXCIpJiZiKGEodGhpcykuZGF0YShcIm9uZHJvcHN0eWxlXCIpKXx8bnVsbCxvbkRyYWdDbGFzczphKHRoaXMpLmRhdGEoXCJvbmRyYWdjbGFzc1wiKT09PWQmJlwidERuRF93aGlsZURyYWdcInx8YSh0aGlzKS5kYXRhKFwib25kcmFnY2xhc3NcIiksb25Ecm9wOmEodGhpcykuZGF0YShcIm9uZHJvcFwiKSYmbmV3IEZ1bmN0aW9uKFwidGFibGVcIixcInJvd1wiLGEodGhpcykuZGF0YShcIm9uZHJvcFwiKSksb25EcmFnU3RhcnQ6YSh0aGlzKS5kYXRhKFwib25kcmFnc3RhcnRcIikmJm5ldyBGdW5jdGlvbihcInRhYmxlXCIsXCJyb3dcIixhKHRoaXMpLmRhdGEoXCJvbmRyYWdzdGFydFwiKSksb25EcmFnU3RvcDphKHRoaXMpLmRhdGEoXCJvbmRyYWdzdG9wXCIpJiZuZXcgRnVuY3Rpb24oXCJ0YWJsZVwiLFwicm93XCIsYSh0aGlzKS5kYXRhKFwib25kcmFnc3RvcFwiKSksc2Nyb2xsQW1vdW50OmEodGhpcykuZGF0YShcInNjcm9sbGFtb3VudFwiKXx8NSxzZW5zaXRpdml0eTphKHRoaXMpLmRhdGEoXCJzZW5zaXRpdml0eVwiKXx8MTAsaGllcmFyY2h5TGV2ZWw6YSh0aGlzKS5kYXRhKFwiaGllcmFyY2h5bGV2ZWxcIil8fDAsaW5kZW50QXJ0aWZhY3Q6YSh0aGlzKS5kYXRhKFwiaW5kZW50YXJ0aWZhY3RcIil8fCc8ZGl2IGNsYXNzPVwiaW5kZW50XCI+Jm5ic3A7PC9kaXY+JyxhdXRvV2lkdGhBZGp1c3Q6YSh0aGlzKS5kYXRhKFwiYXV0b3dpZHRoYWRqdXN0XCIpfHwhMCxhdXRvQ2xlYW5SZWxhdGlvbnM6YSh0aGlzKS5kYXRhKFwiYXV0b2NsZWFucmVsYXRpb25zXCIpfHwhMCxqc29uUHJldGlmeVNlcGFyYXRvcjphKHRoaXMpLmRhdGEoXCJqc29ucHJldGlmeXNlcGFyYXRvclwiKXx8XCJcXHRcIixzZXJpYWxpemVSZWdleHA6YSh0aGlzKS5kYXRhKFwic2VyaWFsaXplcmVnZXhwXCIpJiZuZXcgUmVnRXhwKGEodGhpcykuZGF0YShcInNlcmlhbGl6ZXJlZ2V4cFwiKSl8fC9bXlxcLV0qJC8sc2VyaWFsaXplUGFyYW1OYW1lOmEodGhpcykuZGF0YShcInNlcmlhbGl6ZXBhcmFtbmFtZVwiKXx8ITEsZHJhZ0hhbmRsZTphKHRoaXMpLmRhdGEoXCJkcmFnaGFuZGxlXCIpfHxudWxsfSl9KX0pLGpRdWVyeS50YWJsZURuRD17Y3VycmVudFRhYmxlOm51bGwsZHJhZ09iamVjdDpudWxsLG1vdXNlT2Zmc2V0Om51bGwsb2xkWDowLG9sZFk6MCxidWlsZDpmdW5jdGlvbihiKXtyZXR1cm4gdGhpcy5lYWNoKGZ1bmN0aW9uKCl7dGhpcy50YWJsZURuRENvbmZpZz1hLmV4dGVuZCh7b25EcmFnU3R5bGU6bnVsbCxvbkRyb3BTdHlsZTpudWxsLG9uRHJhZ0NsYXNzOlwidERuRF93aGlsZURyYWdcIixvbkRyb3A6bnVsbCxvbkRyYWdTdGFydDpudWxsLG9uRHJhZ1N0b3A6bnVsbCxzY3JvbGxBbW91bnQ6NSxzZW5zaXRpdml0eToxMCxoaWVyYXJjaHlMZXZlbDowLGluZGVudEFydGlmYWN0Oic8ZGl2IGNsYXNzPVwiaW5kZW50XCI+Jm5ic3A7PC9kaXY+JyxhdXRvV2lkdGhBZGp1c3Q6ITAsYXV0b0NsZWFuUmVsYXRpb25zOiEwLGpzb25QcmV0aWZ5U2VwYXJhdG9yOlwiXFx0XCIsc2VyaWFsaXplUmVnZXhwOi9bXlxcLV0qJC8sc2VyaWFsaXplUGFyYW1OYW1lOiExLGRyYWdIYW5kbGU6bnVsbH0sYnx8e30pLGEudGFibGVEbkQubWFrZURyYWdnYWJsZSh0aGlzKSx0aGlzLnRhYmxlRG5EQ29uZmlnLmhpZXJhcmNoeUxldmVsJiZhLnRhYmxlRG5ELm1ha2VJbmRlbnRlZCh0aGlzKX0pLHRoaXN9LG1ha2VJbmRlbnRlZDpmdW5jdGlvbihiKXt2YXIgYyxkLGU9Yi50YWJsZURuRENvbmZpZyxmPWIucm93cyxnPWEoZikuZmlyc3QoKS5maW5kKFwidGQ6Zmlyc3RcIilbMF0saD0wLGk9MDtpZihhKGIpLmhhc0NsYXNzKFwiaW5kdGRcIikpcmV0dXJuIG51bGw7ZD1hKGIpLmFkZENsYXNzKFwiaW5kdGRcIikuYXR0cihcInN0eWxlXCIpLGEoYikuY3NzKHt3aGl0ZVNwYWNlOlwibm93cmFwXCJ9KTtmb3IodmFyIGo9MDtqPGYubGVuZ3RoO2orKylpPGEoZltqXSkuZmluZChcInRkOmZpcnN0XCIpLnRleHQoKS5sZW5ndGgmJihpPWEoZltqXSkuZmluZChcInRkOmZpcnN0XCIpLnRleHQoKS5sZW5ndGgsYz1qKTtmb3IoYShnKS5jc3Moe3dpZHRoOlwiYXV0b1wifSksaj0wO2o8ZS5oaWVyYXJjaHlMZXZlbDtqKyspYShmW2NdKS5maW5kKFwidGQ6Zmlyc3RcIikucHJlcGVuZChlLmluZGVudEFydGlmYWN0KTtmb3IoZyYmYShnKS5jc3Moe3dpZHRoOmcub2Zmc2V0V2lkdGh9KSxkJiZhKGIpLmNzcyhkKSxqPTA7ajxlLmhpZXJhcmNoeUxldmVsO2orKylhKGZbY10pLmZpbmQoXCJ0ZDpmaXJzdFwiKS5jaGlsZHJlbihcIjpmaXJzdFwiKS5yZW1vdmUoKTtyZXR1cm4gZS5oaWVyYXJjaHlMZXZlbCYmYShmKS5lYWNoKGZ1bmN0aW9uKCl7KGg9YSh0aGlzKS5kYXRhKFwibGV2ZWxcIil8fDApPD1lLmhpZXJhcmNoeUxldmVsJiZhKHRoaXMpLmRhdGEoXCJsZXZlbFwiLGgpfHxhKHRoaXMpLmRhdGEoXCJsZXZlbFwiLDApO2Zvcih2YXIgYj0wO2I8YSh0aGlzKS5kYXRhKFwibGV2ZWxcIik7YisrKWEodGhpcykuZmluZChcInRkOmZpcnN0XCIpLnByZXBlbmQoZS5pbmRlbnRBcnRpZmFjdCl9KSx0aGlzfSxtYWtlRHJhZ2dhYmxlOmZ1bmN0aW9uKGIpe3ZhciBjPWIudGFibGVEbkRDb25maWc7Yy5kcmFnSGFuZGxlJiZhKGMuZHJhZ0hhbmRsZSxiKS5lYWNoKGZ1bmN0aW9uKCl7YSh0aGlzKS5iaW5kKGUsZnVuY3Rpb24oZCl7cmV0dXJuIGEudGFibGVEbkQuaW5pdGlhbGlzZURyYWcoYSh0aGlzKS5wYXJlbnRzKFwidHJcIilbMF0sYix0aGlzLGQsYyksITF9KX0pfHxhKGIucm93cykuZWFjaChmdW5jdGlvbigpe2EodGhpcykuaGFzQ2xhc3MoXCJub2RyYWdcIik/YSh0aGlzKS5jc3MoXCJjdXJzb3JcIixcIlwiKTphKHRoaXMpLmJpbmQoZSxmdW5jdGlvbihkKXtpZihcIlREXCI9PT1kLnRhcmdldC50YWdOYW1lKXJldHVybiBhLnRhYmxlRG5ELmluaXRpYWxpc2VEcmFnKHRoaXMsYix0aGlzLGQsYyksITF9KS5jc3MoXCJjdXJzb3JcIixcIm1vdmVcIil9KX0sY3VycmVudE9yZGVyOmZ1bmN0aW9uKCl7dmFyIGI9dGhpcy5jdXJyZW50VGFibGUucm93cztyZXR1cm4gYS5tYXAoYixmdW5jdGlvbihiKXtyZXR1cm4oYShiKS5kYXRhKFwibGV2ZWxcIikrYi5pZCkucmVwbGFjZSgvXFxzL2csXCJcIil9KS5qb2luKFwiXCIpfSxpbml0aWFsaXNlRHJhZzpmdW5jdGlvbihiLGQsZSxoLGkpe3RoaXMuZHJhZ09iamVjdD1iLHRoaXMuY3VycmVudFRhYmxlPWQsdGhpcy5tb3VzZU9mZnNldD10aGlzLmdldE1vdXNlT2Zmc2V0KGUsaCksdGhpcy5vcmlnaW5hbE9yZGVyPXRoaXMuY3VycmVudE9yZGVyKCksYShjKS5iaW5kKGYsdGhpcy5tb3VzZW1vdmUpLmJpbmQoZyx0aGlzLm1vdXNldXApLGkub25EcmFnU3RhcnQmJmkub25EcmFnU3RhcnQoZCxlKX0sdXBkYXRlVGFibGVzOmZ1bmN0aW9uKCl7dGhpcy5lYWNoKGZ1bmN0aW9uKCl7dGhpcy50YWJsZURuRENvbmZpZyYmYS50YWJsZURuRC5tYWtlRHJhZ2dhYmxlKHRoaXMpfSl9LG1vdXNlQ29vcmRzOmZ1bmN0aW9uKGEpe3JldHVybiBhLm9yaWdpbmFsRXZlbnQuY2hhbmdlZFRvdWNoZXM/e3g6YS5vcmlnaW5hbEV2ZW50LmNoYW5nZWRUb3VjaGVzWzBdLmNsaWVudFgseTphLm9yaWdpbmFsRXZlbnQuY2hhbmdlZFRvdWNoZXNbMF0uY2xpZW50WX06YS5wYWdlWHx8YS5wYWdlWT97eDphLnBhZ2VYLHk6YS5wYWdlWX06e3g6YS5jbGllbnRYK2MuYm9keS5zY3JvbGxMZWZ0LWMuYm9keS5jbGllbnRMZWZ0LHk6YS5jbGllbnRZK2MuYm9keS5zY3JvbGxUb3AtYy5ib2R5LmNsaWVudFRvcH19LGdldE1vdXNlT2Zmc2V0OmZ1bmN0aW9uKGEsYyl7dmFyIGQsZTtyZXR1cm4gYz1jfHxiLmV2ZW50LGU9dGhpcy5nZXRQb3NpdGlvbihhKSxkPXRoaXMubW91c2VDb29yZHMoYykse3g6ZC54LWUueCx5OmQueS1lLnl9fSxnZXRQb3NpdGlvbjpmdW5jdGlvbihhKXt2YXIgYj0wLGM9MDtmb3IoMD09PWEub2Zmc2V0SGVpZ2h0JiYoYT1hLmZpcnN0Q2hpbGQpO2Eub2Zmc2V0UGFyZW50OyliKz1hLm9mZnNldExlZnQsYys9YS5vZmZzZXRUb3AsYT1hLm9mZnNldFBhcmVudDtyZXR1cm4gYis9YS5vZmZzZXRMZWZ0LGMrPWEub2Zmc2V0VG9wLHt4OmIseTpjfX0sYXV0b1Njcm9sbDpmdW5jdGlvbihhKXt2YXIgZD10aGlzLmN1cnJlbnRUYWJsZS50YWJsZURuRENvbmZpZyxlPWIucGFnZVlPZmZzZXQsZj1iLmlubmVySGVpZ2h0P2IuaW5uZXJIZWlnaHQ6Yy5kb2N1bWVudEVsZW1lbnQuY2xpZW50SGVpZ2h0P2MuZG9jdW1lbnRFbGVtZW50LmNsaWVudEhlaWdodDpjLmJvZHkuY2xpZW50SGVpZ2h0O2MuYWxsJiYodm9pZCAwIT09Yy5jb21wYXRNb2RlJiZcIkJhY2tDb21wYXRcIiE9PWMuY29tcGF0TW9kZT9lPWMuZG9jdW1lbnRFbGVtZW50LnNjcm9sbFRvcDp2b2lkIDAhPT1jLmJvZHkmJihlPWMuYm9keS5zY3JvbGxUb3ApKSxhLnktZTxkLnNjcm9sbEFtb3VudCYmYi5zY3JvbGxCeSgwLC1kLnNjcm9sbEFtb3VudCl8fGYtKGEueS1lKTxkLnNjcm9sbEFtb3VudCYmYi5zY3JvbGxCeSgwLGQuc2Nyb2xsQW1vdW50KX0sbW92ZVZlcnRpY2xlOmZ1bmN0aW9uKGEsYil7MCE9PWEudmVydGljYWwmJmImJnRoaXMuZHJhZ09iamVjdCE9PWImJnRoaXMuZHJhZ09iamVjdC5wYXJlbnROb2RlPT09Yi5wYXJlbnROb2RlJiYoMD5hLnZlcnRpY2FsJiZ0aGlzLmRyYWdPYmplY3QucGFyZW50Tm9kZS5pbnNlcnRCZWZvcmUodGhpcy5kcmFnT2JqZWN0LGIubmV4dFNpYmxpbmcpfHwwPGEudmVydGljYWwmJnRoaXMuZHJhZ09iamVjdC5wYXJlbnROb2RlLmluc2VydEJlZm9yZSh0aGlzLmRyYWdPYmplY3QsYikpfSxtb3ZlSG9yaXpvbnRhbDpmdW5jdGlvbihiLGMpe3ZhciBkLGU9dGhpcy5jdXJyZW50VGFibGUudGFibGVEbkRDb25maWc7aWYoIWUuaGllcmFyY2h5TGV2ZWx8fDA9PT1iLmhvcml6b250YWx8fCFjfHx0aGlzLmRyYWdPYmplY3QhPT1jKXJldHVybiBudWxsO2Q9YShjKS5kYXRhKFwibGV2ZWxcIiksMDxiLmhvcml6b250YWwmJmQ+MCYmYShjKS5maW5kKFwidGQ6Zmlyc3RcIikuY2hpbGRyZW4oXCI6Zmlyc3RcIikucmVtb3ZlKCkmJmEoYykuZGF0YShcImxldmVsXCIsLS1kKSwwPmIuaG9yaXpvbnRhbCYmZDxlLmhpZXJhcmNoeUxldmVsJiZhKGMpLnByZXYoKS5kYXRhKFwibGV2ZWxcIik+PWQmJmEoYykuY2hpbGRyZW4oXCI6Zmlyc3RcIikucHJlcGVuZChlLmluZGVudEFydGlmYWN0KSYmYShjKS5kYXRhKFwibGV2ZWxcIiwrK2QpfSxtb3VzZW1vdmU6ZnVuY3Rpb24oYil7dmFyIGMsZCxlLGYsZyxoPWEoYS50YWJsZURuRC5kcmFnT2JqZWN0KSxpPWEudGFibGVEbkQuY3VycmVudFRhYmxlLnRhYmxlRG5EQ29uZmlnO3JldHVybiBiJiZiLnByZXZlbnREZWZhdWx0KCksISFhLnRhYmxlRG5ELmRyYWdPYmplY3QmJihcInRvdWNobW92ZVwiPT09Yi50eXBlJiZldmVudC5wcmV2ZW50RGVmYXVsdCgpLGkub25EcmFnQ2xhc3MmJmguYWRkQ2xhc3MoaS5vbkRyYWdDbGFzcyl8fGguY3NzKGkub25EcmFnU3R5bGUpLGQ9YS50YWJsZURuRC5tb3VzZUNvb3JkcyhiKSxmPWQueC1hLnRhYmxlRG5ELm1vdXNlT2Zmc2V0LngsZz1kLnktYS50YWJsZURuRC5tb3VzZU9mZnNldC55LGEudGFibGVEbkQuYXV0b1Njcm9sbChkKSxjPWEudGFibGVEbkQuZmluZERyb3BUYXJnZXRSb3coaCxnKSxlPWEudGFibGVEbkQuZmluZERyYWdEaXJlY3Rpb24oZixnKSxhLnRhYmxlRG5ELm1vdmVWZXJ0aWNsZShlLGMpLGEudGFibGVEbkQubW92ZUhvcml6b250YWwoZSxjKSwhMSl9LGZpbmREcmFnRGlyZWN0aW9uOmZ1bmN0aW9uKGEsYil7dmFyIGM9dGhpcy5jdXJyZW50VGFibGUudGFibGVEbkRDb25maWcuc2Vuc2l0aXZpdHksZD10aGlzLm9sZFgsZT10aGlzLm9sZFksZj1kLWMsZz1kK2MsaD1lLWMsaT1lK2Msaj17aG9yaXpvbnRhbDphPj1mJiZhPD1nPzA6YT5kPy0xOjEsdmVydGljYWw6Yj49aCYmYjw9aT8wOmI+ZT8tMToxfTtyZXR1cm4gMCE9PWouaG9yaXpvbnRhbCYmKHRoaXMub2xkWD1hKSwwIT09ai52ZXJ0aWNhbCYmKHRoaXMub2xkWT1iKSxqfSxmaW5kRHJvcFRhcmdldFJvdzpmdW5jdGlvbihiLGMpe2Zvcih2YXIgZD0wLGU9dGhpcy5jdXJyZW50VGFibGUucm93cyxmPXRoaXMuY3VycmVudFRhYmxlLnRhYmxlRG5EQ29uZmlnLGc9MCxoPW51bGwsaT0wO2k8ZS5sZW5ndGg7aSsrKWlmKGg9ZVtpXSxnPXRoaXMuZ2V0UG9zaXRpb24oaCkueSxkPXBhcnNlSW50KGgub2Zmc2V0SGVpZ2h0KS8yLDA9PT1oLm9mZnNldEhlaWdodCYmKGc9dGhpcy5nZXRQb3NpdGlvbihoLmZpcnN0Q2hpbGQpLnksZD1wYXJzZUludChoLmZpcnN0Q2hpbGQub2Zmc2V0SGVpZ2h0KS8yKSxjPmctZCYmYzxnK2QpcmV0dXJuIGIuaXMoaCl8fGYub25BbGxvd0Ryb3AmJiFmLm9uQWxsb3dEcm9wKGIsaCl8fGEoaCkuaGFzQ2xhc3MoXCJub2Ryb3BcIik/bnVsbDpoO3JldHVybiBudWxsfSxwcm9jZXNzTW91c2V1cDpmdW5jdGlvbigpe2lmKCF0aGlzLmN1cnJlbnRUYWJsZXx8IXRoaXMuZHJhZ09iamVjdClyZXR1cm4gbnVsbDt2YXIgYj10aGlzLmN1cnJlbnRUYWJsZS50YWJsZURuRENvbmZpZyxkPXRoaXMuZHJhZ09iamVjdCxlPTAsaD0wO2EoYykudW5iaW5kKGYsdGhpcy5tb3VzZW1vdmUpLnVuYmluZChnLHRoaXMubW91c2V1cCksYi5oaWVyYXJjaHlMZXZlbCYmYi5hdXRvQ2xlYW5SZWxhdGlvbnMmJmEodGhpcy5jdXJyZW50VGFibGUucm93cykuZmlyc3QoKS5maW5kKFwidGQ6Zmlyc3RcIikuY2hpbGRyZW4oKS5lYWNoKGZ1bmN0aW9uKCl7KGg9YSh0aGlzKS5wYXJlbnRzKFwidHI6Zmlyc3RcIikuZGF0YShcImxldmVsXCIpKSYmYSh0aGlzKS5wYXJlbnRzKFwidHI6Zmlyc3RcIikuZGF0YShcImxldmVsXCIsLS1oKSYmYSh0aGlzKS5yZW1vdmUoKX0pJiZiLmhpZXJhcmNoeUxldmVsPjEmJmEodGhpcy5jdXJyZW50VGFibGUucm93cykuZWFjaChmdW5jdGlvbigpe2lmKChoPWEodGhpcykuZGF0YShcImxldmVsXCIpKT4xKWZvcihlPWEodGhpcykucHJldigpLmRhdGEoXCJsZXZlbFwiKTtoPmUrMTspYSh0aGlzKS5maW5kKFwidGQ6Zmlyc3RcIikuY2hpbGRyZW4oXCI6Zmlyc3RcIikucmVtb3ZlKCksYSh0aGlzKS5kYXRhKFwibGV2ZWxcIiwtLWgpfSksYi5vbkRyYWdDbGFzcyYmYShkKS5yZW1vdmVDbGFzcyhiLm9uRHJhZ0NsYXNzKXx8YShkKS5jc3MoYi5vbkRyb3BTdHlsZSksdGhpcy5kcmFnT2JqZWN0PW51bGwsYi5vbkRyb3AmJnRoaXMub3JpZ2luYWxPcmRlciE9PXRoaXMuY3VycmVudE9yZGVyKCkmJmEoZCkuaGlkZSgpLmZhZGVJbihcImZhc3RcIikmJmIub25Ecm9wKHRoaXMuY3VycmVudFRhYmxlLGQpLGIub25EcmFnU3RvcCYmYi5vbkRyYWdTdG9wKHRoaXMuY3VycmVudFRhYmxlLGQpLHRoaXMuY3VycmVudFRhYmxlPW51bGx9LG1vdXNldXA6ZnVuY3Rpb24oYil7cmV0dXJuIGImJmIucHJldmVudERlZmF1bHQoKSxhLnRhYmxlRG5ELnByb2Nlc3NNb3VzZXVwKCksITF9LGpzb25pemU6ZnVuY3Rpb24oYSl7dmFyIGI9dGhpcy5jdXJyZW50VGFibGU7cmV0dXJuIGE/SlNPTi5zdHJpbmdpZnkodGhpcy50YWJsZURhdGEoYiksbnVsbCxiLnRhYmxlRG5EQ29uZmlnLmpzb25QcmV0aWZ5U2VwYXJhdG9yKTpKU09OLnN0cmluZ2lmeSh0aGlzLnRhYmxlRGF0YShiKSl9LHNlcmlhbGl6ZTpmdW5jdGlvbigpe3JldHVybiBhLnBhcmFtKHRoaXMudGFibGVEYXRhKHRoaXMuY3VycmVudFRhYmxlKSl9LHNlcmlhbGl6ZVRhYmxlOmZ1bmN0aW9uKGEpe2Zvcih2YXIgYj1cIlwiLGM9YS50YWJsZURuRENvbmZpZy5zZXJpYWxpemVQYXJhbU5hbWV8fGEuaWQsZD1hLnJvd3MsZT0wO2U8ZC5sZW5ndGg7ZSsrKXtiLmxlbmd0aD4wJiYoYis9XCImXCIpO3ZhciBmPWRbZV0uaWQ7ZiYmYS50YWJsZURuRENvbmZpZyYmYS50YWJsZURuRENvbmZpZy5zZXJpYWxpemVSZWdleHAmJihmPWYubWF0Y2goYS50YWJsZURuRENvbmZpZy5zZXJpYWxpemVSZWdleHApWzBdLGIrPWMrXCJbXT1cIitmKX1yZXR1cm4gYn0sc2VyaWFsaXplVGFibGVzOmZ1bmN0aW9uKCl7dmFyIGI9W107cmV0dXJuIGEoXCJ0YWJsZVwiKS5lYWNoKGZ1bmN0aW9uKCl7dGhpcy5pZCYmYi5wdXNoKGEucGFyYW0oYS50YWJsZURuRC50YWJsZURhdGEodGhpcykpKX0pLGIuam9pbihcIiZcIil9LHRhYmxlRGF0YTpmdW5jdGlvbihiKXt2YXIgYyxkLGUsZixnPWIudGFibGVEbkRDb25maWcsaD1bXSxpPTAsaj0wLGs9bnVsbCxsPXt9O2lmKGJ8fChiPXRoaXMuY3VycmVudFRhYmxlKSwhYnx8IWIucm93c3x8IWIucm93cy5sZW5ndGgpcmV0dXJue2Vycm9yOntjb2RlOjUwMCxtZXNzYWdlOlwiTm90IGEgdmFsaWQgdGFibGUuXCJ9fTtpZighYi5pZCYmIWcuc2VyaWFsaXplUGFyYW1OYW1lKXJldHVybntlcnJvcjp7Y29kZTo1MDAsbWVzc2FnZTpcIk5vIHNlcmlhbGl6YWJsZSB1bmlxdWUgaWQgcHJvdmlkZWQuXCJ9fTtmPWcuYXV0b0NsZWFuUmVsYXRpb25zJiZiLnJvd3N8fGEubWFrZUFycmF5KGIucm93cyksZD1nLnNlcmlhbGl6ZVBhcmFtTmFtZXx8Yi5pZCxlPWQsYz1mdW5jdGlvbihhKXtyZXR1cm4gYSYmZyYmZy5zZXJpYWxpemVSZWdleHA/YS5tYXRjaChnLnNlcmlhbGl6ZVJlZ2V4cClbMF06YX0sbFtlXT1bXSwhZy5hdXRvQ2xlYW5SZWxhdGlvbnMmJmEoZlswXSkuZGF0YShcImxldmVsXCIpJiZmLnVuc2hpZnQoe2lkOlwidW5kZWZpbmVkXCJ9KTtmb3IodmFyIG09MDttPGYubGVuZ3RoO20rKylpZihnLmhpZXJhcmNoeUxldmVsKXtpZigwPT09KGo9YShmW21dKS5kYXRhKFwibGV2ZWxcIil8fDApKWU9ZCxoPVtdO2Vsc2UgaWYoaj5pKWgucHVzaChbZSxpXSksZT1jKGZbbS0xXS5pZCk7ZWxzZSBpZihqPGkpZm9yKHZhciBuPTA7bjxoLmxlbmd0aDtuKyspaFtuXVsxXT09PWomJihlPWhbbl1bMF0pLGhbbl1bMV0+PWkmJihoW25dWzFdPTApO2k9aixhLmlzQXJyYXkobFtlXSl8fChsW2VdPVtdKSxrPWMoZlttXS5pZCksayYmbFtlXS5wdXNoKGspfWVsc2Uoaz1jKGZbbV0uaWQpKSYmbFtlXS5wdXNoKGspO3JldHVybiBsfX0salF1ZXJ5LmZuLmV4dGVuZCh7dGFibGVEbkQ6YS50YWJsZURuRC5idWlsZCx0YWJsZURuRFVwZGF0ZTphLnRhYmxlRG5ELnVwZGF0ZVRhYmxlcyx0YWJsZURuRFNlcmlhbGl6ZTphLnByb3h5KGEudGFibGVEbkQuc2VyaWFsaXplLGEudGFibGVEbkQpLHRhYmxlRG5EU2VyaWFsaXplQWxsOmEudGFibGVEbkQuc2VyaWFsaXplVGFibGVzLHRhYmxlRG5ERGF0YTphLnByb3h5KGEudGFibGVEbkQudGFibGVEYXRhLGEudGFibGVEbkQpfSl9KGpRdWVyeSx3aW5kb3csd2luZG93LmRvY3VtZW50KTsiLCJ2YXIgZztcblxuLy8gVGhpcyB3b3JrcyBpbiBub24tc3RyaWN0IG1vZGVcbmcgPSAoZnVuY3Rpb24oKSB7XG5cdHJldHVybiB0aGlzO1xufSkoKTtcblxudHJ5IHtcblx0Ly8gVGhpcyB3b3JrcyBpZiBldmFsIGlzIGFsbG93ZWQgKHNlZSBDU1ApXG5cdGcgPSBnIHx8IG5ldyBGdW5jdGlvbihcInJldHVybiB0aGlzXCIpKCk7XG59IGNhdGNoIChlKSB7XG5cdC8vIFRoaXMgd29ya3MgaWYgdGhlIHdpbmRvdyByZWZlcmVuY2UgaXMgYXZhaWxhYmxlXG5cdGlmICh0eXBlb2Ygd2luZG93ID09PSBcIm9iamVjdFwiKSBnID0gd2luZG93O1xufVxuXG4vLyBnIGNhbiBzdGlsbCBiZSB1bmRlZmluZWQsIGJ1dCBub3RoaW5nIHRvIGRvIGFib3V0IGl0Li4uXG4vLyBXZSByZXR1cm4gdW5kZWZpbmVkLCBpbnN0ZWFkIG9mIG5vdGhpbmcgaGVyZSwgc28gaXQnc1xuLy8gZWFzaWVyIHRvIGhhbmRsZSB0aGlzIGNhc2UuIGlmKCFnbG9iYWwpIHsgLi4ufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGc7XG4iLCJtb2R1bGUuZXhwb3J0cyA9IGpRdWVyeTsiXSwic291cmNlUm9vdCI6IiJ9