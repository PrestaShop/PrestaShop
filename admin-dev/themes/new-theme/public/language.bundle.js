window["language"] =
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

/***/ 10:
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

/***/ }),

/***/ 12:
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

/***/ 13:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

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

var $ = global.$;

/**
 * Makes a table sortable by columns.
 * This forces a page reload with more query parameters.
 */

var TableSorting = function () {

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
    key: 'attach',
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
    key: 'sortBy',
    value: function sortBy(columnName, direction) {
      var $column = this.columns.is('[data-sort-col-name="' + columnName + '"]');
      if (!$column) {
        throw new Error('Cannot sort by "' + columnName + '": invalid column');
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
    key: '_sortByColumn',
    value: function _sortByColumn(column, direction) {
      window.location = this._getUrl(column.data('sortColName'), direction === 'desc' ? 'desc' : 'asc', column.data('sortPrefix'));
    }

    /**
     * Returns the inverted direction to sort according to the column's current one
     * @param {jQuery} column
     * @return {string}
     * @private
     */

  }, {
    key: '_getToggledSortDirection',
    value: function _getToggledSortDirection(column) {
      return column.data('sortDirection') === 'asc' ? 'desc' : 'asc';
    }

    /**
     * Returns the url for the sorted table
     * @param {string} colName
     * @param {string} direction
     * @param {string} prefix
     * @return {string}
     * @private
     */

  }, {
    key: '_getUrl',
    value: function _getUrl(colName, direction, prefix) {
      var url = new URL(window.location.href);
      var params = url.searchParams;

      if (prefix) {
        params.set(prefix + '[orderBy]', colName);
        params.set(prefix + '[sortOrder]', direction);
      } else {
        params.set('orderBy', colName);
        params.set('sortOrder', direction);
      }

      return url.toString();
    }
  }]);

  return TableSorting;
}();

exports.default = TableSorting;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }),

/***/ 14:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

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
 * Send a Post Request to reset search Action.
 */

var $ = global.$;

var init = function resetSearch(url, redirectUrl) {
  $.post(url).then(function () {
    return window.location.assign(redirectUrl);
  });
};

exports.default = init;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }),

/***/ 15:
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

exports.default = LinkRowActionExtension;

/***/ }),

/***/ 17:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

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
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

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

/***/ 2:
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

/***/ 331:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _grid = __webpack_require__(2);

var _grid2 = _interopRequireDefault(_grid);

var _reloadListExtension = __webpack_require__(5);

var _reloadListExtension2 = _interopRequireDefault(_reloadListExtension);

var _exportToSqlManagerExtension = __webpack_require__(9);

var _exportToSqlManagerExtension2 = _interopRequireDefault(_exportToSqlManagerExtension);

var _filtersResetExtension = __webpack_require__(4);

var _filtersResetExtension2 = _interopRequireDefault(_filtersResetExtension);

var _sortingExtension = __webpack_require__(6);

var _sortingExtension2 = _interopRequireDefault(_sortingExtension);

var _linkRowActionExtension = __webpack_require__(15);

var _linkRowActionExtension2 = _interopRequireDefault(_linkRowActionExtension);

var _submitBulkActionExtension = __webpack_require__(10);

var _submitBulkActionExtension2 = _interopRequireDefault(_submitBulkActionExtension);

var _submitRowActionExtension = __webpack_require__(12);

var _submitRowActionExtension2 = _interopRequireDefault(_submitRowActionExtension);

var _bulkActionCheckboxExtension = __webpack_require__(8);

var _bulkActionCheckboxExtension2 = _interopRequireDefault(_bulkActionCheckboxExtension);

var _choiceTree = __webpack_require__(18);

var _choiceTree2 = _interopRequireDefault(_choiceTree);

var _columnTogglingExtension = __webpack_require__(17);

var _columnTogglingExtension2 = _interopRequireDefault(_columnTogglingExtension);

var _filtersSubmitButtonEnablerExtension = __webpack_require__(7);

var _filtersSubmitButtonEnablerExtension2 = _interopRequireDefault(_filtersSubmitButtonEnablerExtension);

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

$(document).ready(function () {
  var grid = new _grid2.default('language');

  grid.addExtension(new _reloadListExtension2.default());
  grid.addExtension(new _exportToSqlManagerExtension2.default());
  grid.addExtension(new _filtersResetExtension2.default());
  grid.addExtension(new _sortingExtension2.default());
  grid.addExtension(new _linkRowActionExtension2.default());
  grid.addExtension(new _submitBulkActionExtension2.default());
  grid.addExtension(new _submitRowActionExtension2.default());
  grid.addExtension(new _bulkActionCheckboxExtension2.default());
  grid.addExtension(new _columnTogglingExtension2.default());
  grid.addExtension(new _filtersSubmitButtonEnablerExtension2.default());

  // needed for shop association input in form
  new _choiceTree2.default('#language_shop_association').enableAutoCheckChildren();
});

/***/ }),

/***/ 4:
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

var _reset_search = __webpack_require__(14);

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

/***/ 5:
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

var _tableSorting = __webpack_require__(13);

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

/***/ 7:
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
 * Responsible for grid filters search and reset button availability when filter inputs changes.
 */
var FiltersSubmitButtonEnablerExtension = function () {
  function FiltersSubmitButtonEnablerExtension() {
    _classCallCheck(this, FiltersSubmitButtonEnablerExtension);
  }

  _createClass(FiltersSubmitButtonEnablerExtension, [{
    key: 'extend',


    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var $filtersRow = grid.getContainer().find('.column-filters');
      $filtersRow.find('.grid-search-button').prop('disabled', true);

      $filtersRow.find('input, select').on('input', function () {
        $filtersRow.find('.grid-search-button').prop('disabled', false);
        $filtersRow.find('.js-grid-reset-button').prop('hidden', false);
      });
    }
  }]);

  return FiltersSubmitButtonEnablerExtension;
}();

exports.default = FiltersSubmitButtonEnablerExtension;

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

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQ/MjBkNCoqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8od2VicGFjaykvYnVpbGRpbi9nbG9iYWwuanM/MzY5OCoqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zdWJtaXQtYnVsay1hY3Rpb24tZXh0ZW5zaW9uLmpzPzFiMWYqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9yb3cvc3VibWl0LXJvdy1hY3Rpb24tZXh0ZW5zaW9uLmpzPzI3ZDEqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL3V0aWxzL3RhYmxlLXNvcnRpbmcuanM/MTVkNCoqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2FwcC91dGlscy9yZXNldF9zZWFyY2guanM/MWE3ZioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbGluay1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcz8zOWRjKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi10b2dnbGluZy1leHRlbnNpb24uanM/Njk0MyoqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2Zvcm0vY2hvaWNlLXRyZWUuanM/NTQxYSoqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9ncmlkLmpzPzgxM2EqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9sYW5ndWFnZS9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uLmpzPzE2ZjEqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvbi5qcz9kM2UwKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zb3J0aW5nLWV4dGVuc2lvbi5qcz8xMTNlKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXN1Ym1pdC1idXR0b24tZW5hYmxlci1leHRlbnNpb24uanM/MDM3OSoqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2J1bGstYWN0aW9uLWNoZWNrYm94LWV4dGVuc2lvbi5qcz9iMDk3KioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9leHBvcnQtdG8tc3FsLW1hbmFnZXItZXh0ZW5zaW9uLmpzP2VkMmEqKioqKioqKioqKiJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwiU3VibWl0QnVsa0FjdGlvbkV4dGVuc2lvbiIsImV4dGVuZCIsImdyaWQiLCJnZXRDb250YWluZXIiLCJvbiIsImV2ZW50Iiwic3VibWl0IiwiJHN1Ym1pdEJ0biIsImN1cnJlbnRUYXJnZXQiLCJjb25maXJtTWVzc2FnZSIsImRhdGEiLCJsZW5ndGgiLCJjb25maXJtIiwiJGZvcm0iLCJnZXRJZCIsImF0dHIiLCJTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24iLCJwcmV2ZW50RGVmYXVsdCIsIiRidXR0b24iLCJtZXRob2QiLCJpc0dldE9yUG9zdE1ldGhvZCIsImluY2x1ZGVzIiwiYXBwZW5kVG8iLCJhcHBlbmQiLCJnbG9iYWwiLCJUYWJsZVNvcnRpbmciLCJ0YWJsZSIsInNlbGVjdG9yIiwiY29sdW1ucyIsImZpbmQiLCJlIiwiJGNvbHVtbiIsImRlbGVnYXRlVGFyZ2V0IiwiX3NvcnRCeUNvbHVtbiIsIl9nZXRUb2dnbGVkU29ydERpcmVjdGlvbiIsImNvbHVtbk5hbWUiLCJkaXJlY3Rpb24iLCJpcyIsIkVycm9yIiwiY29sdW1uIiwibG9jYXRpb24iLCJfZ2V0VXJsIiwiY29sTmFtZSIsInByZWZpeCIsInVybCIsIlVSTCIsImhyZWYiLCJwYXJhbXMiLCJzZWFyY2hQYXJhbXMiLCJzZXQiLCJ0b1N0cmluZyIsImluaXQiLCJyZXNldFNlYXJjaCIsInJlZGlyZWN0VXJsIiwicG9zdCIsInRoZW4iLCJhc3NpZ24iLCJMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIiwiQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24iLCIkdGFibGUiLCJfdG9nZ2xlVmFsdWUiLCJyb3ciLCJ0b2dnbGVVcmwiLCJfc3VibWl0QXNGb3JtIiwiYWN0aW9uIiwiQ2hvaWNlVHJlZSIsInRyZWVTZWxlY3RvciIsIiRjb250YWluZXIiLCIkaW5wdXRXcmFwcGVyIiwiX3RvZ2dsZUNoaWxkVHJlZSIsIiRhY3Rpb24iLCJfdG9nZ2xlVHJlZSIsImVuYWJsZUF1dG9DaGVja0NoaWxkcmVuIiwiZW5hYmxlQWxsSW5wdXRzIiwiZGlzYWJsZUFsbElucHV0cyIsIiRjbGlja2VkQ2hlY2tib3giLCIkaXRlbVdpdGhDaGlsZHJlbiIsImNsb3Nlc3QiLCJwcm9wIiwicmVtb3ZlQXR0ciIsIiRwYXJlbnRXcmFwcGVyIiwiaGFzQ2xhc3MiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiJHBhcmVudENvbnRhaW5lciIsImNvbmZpZyIsImV4cGFuZCIsImNvbGxhcHNlIiwibmV4dEFjdGlvbiIsInRleHQiLCJpY29uIiwiZWFjaCIsImluZGV4IiwiaXRlbSIsIiRpdGVtIiwiR3JpZCIsImlkIiwiZXh0ZW5zaW9uIiwiZG9jdW1lbnQiLCJyZWFkeSIsImFkZEV4dGVuc2lvbiIsIlJlbG9hZExpc3RBY3Rpb25FeHRlbnNpb24iLCJFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24iLCJGaWx0ZXJzUmVzZXRFeHRlbnNpb24iLCJTb3J0aW5nRXh0ZW5zaW9uIiwiU3VibWl0QnVsa0V4dGVuc2lvbiIsIkJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbiIsIkZpbHRlcnNTdWJtaXRCdXR0b25FbmFibGVyRXh0ZW5zaW9uIiwiUmVsb2FkTGlzdEV4dGVuc2lvbiIsImdldEhlYWRlckNvbnRhaW5lciIsInJlbG9hZCIsIiRzb3J0YWJsZVRhYmxlIiwiYXR0YWNoIiwiJGZpbHRlcnNSb3ciLCJfaGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0IiwiX2hhbmRsZUJ1bGtBY3Rpb25TZWxlY3RBbGxDaGVja2JveCIsIiRjaGVja2JveCIsImlzQ2hlY2tlZCIsIl9lbmFibGVCdWxrQWN0aW9uc0J0biIsIl9kaXNhYmxlQnVsa0FjdGlvbnNCdG4iLCJjaGVja2VkUm93c0NvdW50IiwiX29uU2hvd1NxbFF1ZXJ5Q2xpY2siLCJfb25FeHBvcnRTcWxNYW5hZ2VyQ2xpY2siLCIkc3FsTWFuYWdlckZvcm0iLCJfZmlsbEV4cG9ydEZvcm0iLCIkbW9kYWwiLCJtb2RhbCIsInF1ZXJ5IiwidmFsIiwiX2dldE5hbWVGcm9tQnJlYWRjcnVtYiIsIiRicmVhZGNydW1icyIsIm5hbWUiLCJpIiwiJGJyZWFkY3J1bWIiLCJicmVhZGNydW1iVGl0bGUiLCJjb25jYXQiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7O0FDaEVBOztBQUVBO0FBQ0E7QUFDQTtBQUNB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Q0FBcUIsc0JBQXJCLEVBQTZDbUcsR0FBN0MsQ0FBaURELEtBQWpEO0FBQ0FKLHNCQUFnQjlGLElBQWhCLENBQXFCLG9CQUFyQixFQUEyQ21HLEdBQTNDLENBQStDLEtBQUtDLHNCQUFMLEVBQS9DO0FBQ0Q7O0FBRUQ7Ozs7Ozs7Ozs7NkNBT3lCO0FBQ3ZCLFVBQU1DLGVBQWVwSSxFQUFFLGlCQUFGLEVBQXFCK0IsSUFBckIsQ0FBMEIsa0JBQTFCLENBQXJCO0FBQ0EsVUFBSXNHLE9BQU8sRUFBWDs7QUFFQUQsbUJBQWF2QyxJQUFiLENBQWtCLFVBQUN5QyxDQUFELEVBQUl2QyxJQUFKLEVBQWE7QUFDN0IsWUFBTXdDLGNBQWN2SSxFQUFFK0YsSUFBRixDQUFwQjs7QUFFQSxZQUFNeUMsa0JBQWtCLElBQUlELFlBQVl4RyxJQUFaLENBQWlCLEdBQWpCLEVBQXNCbEIsTUFBMUIsR0FDdEIwSCxZQUFZeEcsSUFBWixDQUFpQixHQUFqQixFQUFzQjRELElBQXRCLEVBRHNCLEdBRXRCNEMsWUFBWTVDLElBQVosRUFGRjs7QUFJQSxZQUFJLElBQUkwQyxLQUFLeEgsTUFBYixFQUFxQjtBQUNuQndILGlCQUFPQSxLQUFLSSxNQUFMLENBQVksS0FBWixDQUFQO0FBQ0Q7O0FBRURKLGVBQU9BLEtBQUtJLE1BQUwsQ0FBWUQsZUFBWixDQUFQO0FBQ0QsT0FaRDs7QUFjQSxhQUFPSCxJQUFQO0FBQ0Q7Ozs7OztrQkFwRmtCN0IsMkIiLCJmaWxlIjoibGFuZ3VhZ2UuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAzMzEpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDY4ZTgyOTFmMTM2MDcwZjI3NmJkIiwidmFyIGc7XHJcblxyXG4vLyBUaGlzIHdvcmtzIGluIG5vbi1zdHJpY3QgbW9kZVxyXG5nID0gKGZ1bmN0aW9uKCkge1xyXG5cdHJldHVybiB0aGlzO1xyXG59KSgpO1xyXG5cclxudHJ5IHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIGV2YWwgaXMgYWxsb3dlZCAoc2VlIENTUClcclxuXHRnID0gZyB8fCBGdW5jdGlvbihcInJldHVybiB0aGlzXCIpKCkgfHwgKDEsZXZhbCkoXCJ0aGlzXCIpO1xyXG59IGNhdGNoKGUpIHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIHRoZSB3aW5kb3cgcmVmZXJlbmNlIGlzIGF2YWlsYWJsZVxyXG5cdGlmKHR5cGVvZiB3aW5kb3cgPT09IFwib2JqZWN0XCIpXHJcblx0XHRnID0gd2luZG93O1xyXG59XHJcblxyXG4vLyBnIGNhbiBzdGlsbCBiZSB1bmRlZmluZWQsIGJ1dCBub3RoaW5nIHRvIGRvIGFib3V0IGl0Li4uXHJcbi8vIFdlIHJldHVybiB1bmRlZmluZWQsIGluc3RlYWQgb2Ygbm90aGluZyBoZXJlLCBzbyBpdCdzXHJcbi8vIGVhc2llciB0byBoYW5kbGUgdGhpcyBjYXNlLiBpZighZ2xvYmFsKSB7IC4uLn1cclxuXHJcbm1vZHVsZS5leHBvcnRzID0gZztcclxuXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzXG4vLyBtb2R1bGUgaWQgPSAxXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyNSAyOCAzMiAzNiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBIYW5kbGVzIHN1Ym1pdCBvZiBncmlkIGFjdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU3VibWl0QnVsa0FjdGlvbkV4dGVuc2lvbiB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHJldHVybiB7XG4gICAgICBleHRlbmQ6IChncmlkKSA9PiB0aGlzLmV4dGVuZChncmlkKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkIHdpdGggYnVsayBhY3Rpb24gc3VibWl0dGluZ1xuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWJ1bGstYWN0aW9uLXN1Ym1pdC1idG4nLCAoZXZlbnQpID0+IHtcbiAgICAgIHRoaXMuc3VibWl0KGV2ZW50LCBncmlkKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGUgYnVsayBhY3Rpb24gc3VibWl0dGluZ1xuICAgKlxuICAgKiBAcGFyYW0ge0V2ZW50fSBldmVudFxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHN1Ym1pdChldmVudCwgZ3JpZCkge1xuICAgIGNvbnN0ICRzdWJtaXRCdG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJHN1Ym1pdEJ0bi5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcblxuICAgIGlmICh0eXBlb2YgY29uZmlybU1lc3NhZ2UgIT09IFwidW5kZWZpbmVkXCIgJiYgMCA8IGNvbmZpcm1NZXNzYWdlLmxlbmd0aCAmJiAhY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCAkZm9ybSA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19maWx0ZXJfZm9ybScpO1xuXG4gICAgJGZvcm0uYXR0cignYWN0aW9uJywgJHN1Ym1pdEJ0bi5kYXRhKCdmb3JtLXVybCcpKTtcbiAgICAkZm9ybS5hdHRyKCdtZXRob2QnLCAkc3VibWl0QnRuLmRhdGEoJ2Zvcm0tbWV0aG9kJykpO1xuICAgICRmb3JtLnN1Ym1pdCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1idWxrLWFjdGlvbi1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uIGhhbmRsZXMgc3VibWl0dGluZyBvZiByb3cgYWN0aW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLXN1Ym1pdC1yb3ctYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICBjb25zdCAkYnV0dG9uID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJGJ1dHRvbi5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcblxuICAgICAgaWYgKGNvbmZpcm1NZXNzYWdlLmxlbmd0aCAmJiAhY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBjb25zdCBtZXRob2QgPSAkYnV0dG9uLmRhdGEoJ21ldGhvZCcpO1xuICAgICAgY29uc3QgaXNHZXRPclBvc3RNZXRob2QgPSBbJ0dFVCcsICdQT1NUJ10uaW5jbHVkZXMobWV0aG9kKTtcblxuICAgICAgY29uc3QgJGZvcm0gPSAkKCc8Zm9ybT4nLCB7XG4gICAgICAgICdhY3Rpb24nOiAkYnV0dG9uLmRhdGEoJ3VybCcpLFxuICAgICAgICAnbWV0aG9kJzogaXNHZXRPclBvc3RNZXRob2QgPyBtZXRob2QgOiAnUE9TVCcsXG4gICAgICB9KS5hcHBlbmRUbygnYm9keScpO1xuXG4gICAgICBpZiAoIWlzR2V0T3JQb3N0TWV0aG9kKSB7XG4gICAgICAgICRmb3JtLmFwcGVuZCgkKCc8aW5wdXQ+Jywge1xuICAgICAgICAgICd0eXBlJzogJ19oaWRkZW4nLFxuICAgICAgICAgICduYW1lJzogJ19tZXRob2QnLFxuICAgICAgICAgICd2YWx1ZSc6IG1ldGhvZFxuICAgICAgICB9KSk7XG4gICAgICB9XG5cbiAgICAgICRmb3JtLnN1Ym1pdCgpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9yb3cvc3VibWl0LXJvdy1hY3Rpb24tZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG4vKipcbiAqIE1ha2VzIGEgdGFibGUgc29ydGFibGUgYnkgY29sdW1ucy5cbiAqIFRoaXMgZm9yY2VzIGEgcGFnZSByZWxvYWQgd2l0aCBtb3JlIHF1ZXJ5IHBhcmFtZXRlcnMuXG4gKi9cbmNsYXNzIFRhYmxlU29ydGluZyB7XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSB0YWJsZVxuICAgKi9cbiAgY29uc3RydWN0b3IodGFibGUpIHtcbiAgICB0aGlzLnNlbGVjdG9yID0gJy5wcy1zb3J0YWJsZS1jb2x1bW4nO1xuICAgIHRoaXMuY29sdW1ucyA9ICQodGFibGUpLmZpbmQodGhpcy5zZWxlY3Rvcik7XG4gIH1cblxuICAvKipcbiAgICogQXR0YWNoZXMgdGhlIGxpc3RlbmVyc1xuICAgKi9cbiAgYXR0YWNoKCkge1xuICAgIHRoaXMuY29sdW1ucy5vbignY2xpY2snLCAoZSkgPT4ge1xuICAgICAgY29uc3QgJGNvbHVtbiA9ICQoZS5kZWxlZ2F0ZVRhcmdldCk7XG4gICAgICB0aGlzLl9zb3J0QnlDb2x1bW4oJGNvbHVtbiwgdGhpcy5fZ2V0VG9nZ2xlZFNvcnREaXJlY3Rpb24oJGNvbHVtbikpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFNvcnQgdXNpbmcgYSBjb2x1bW4gbmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gY29sdW1uTmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIFwiYXNjXCIgb3IgXCJkZXNjXCJcbiAgICovXG4gIHNvcnRCeShjb2x1bW5OYW1lLCBkaXJlY3Rpb24pIHtcbiAgICBjb25zdCAkY29sdW1uID0gdGhpcy5jb2x1bW5zLmlzKGBbZGF0YS1zb3J0LWNvbC1uYW1lPVwiJHtjb2x1bW5OYW1lfVwiXWApO1xuICAgIGlmICghJGNvbHVtbikge1xuICAgICAgdGhyb3cgbmV3IEVycm9yKGBDYW5ub3Qgc29ydCBieSBcIiR7Y29sdW1uTmFtZX1cIjogaW52YWxpZCBjb2x1bW5gKTtcbiAgICB9XG5cbiAgICB0aGlzLl9zb3J0QnlDb2x1bW4oJGNvbHVtbiwgZGlyZWN0aW9uKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTb3J0IHVzaW5nIGEgY29sdW1uIGVsZW1lbnRcbiAgICogQHBhcmFtIHtqUXVlcnl9IGNvbHVtblxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIFwiYXNjXCIgb3IgXCJkZXNjXCJcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zb3J0QnlDb2x1bW4oY29sdW1uLCBkaXJlY3Rpb24pIHtcbiAgICB3aW5kb3cubG9jYXRpb24gPSB0aGlzLl9nZXRVcmwoY29sdW1uLmRhdGEoJ3NvcnRDb2xOYW1lJyksIChkaXJlY3Rpb24gPT09ICdkZXNjJykgPyAnZGVzYycgOiAnYXNjJywgY29sdW1uLmRhdGEoJ3NvcnRQcmVmaXgnKSk7XG4gIH1cblxuICAvKipcbiAgICogUmV0dXJucyB0aGUgaW52ZXJ0ZWQgZGlyZWN0aW9uIHRvIHNvcnQgYWNjb3JkaW5nIHRvIHRoZSBjb2x1bW4ncyBjdXJyZW50IG9uZVxuICAgKiBAcGFyYW0ge2pRdWVyeX0gY29sdW1uXG4gICAqIEByZXR1cm4ge3N0cmluZ31cbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRUb2dnbGVkU29ydERpcmVjdGlvbihjb2x1bW4pIHtcbiAgICByZXR1cm4gY29sdW1uLmRhdGEoJ3NvcnREaXJlY3Rpb24nKSA9PT0gJ2FzYycgPyAnZGVzYycgOiAnYXNjJztcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXR1cm5zIHRoZSB1cmwgZm9yIHRoZSBzb3J0ZWQgdGFibGVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGNvbE5hbWVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGRpcmVjdGlvblxuICAgKiBAcGFyYW0ge3N0cmluZ30gcHJlZml4XG4gICAqIEByZXR1cm4ge3N0cmluZ31cbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRVcmwoY29sTmFtZSwgZGlyZWN0aW9uLCBwcmVmaXgpIHtcbiAgICBjb25zdCB1cmwgPSBuZXcgVVJMKHdpbmRvdy5sb2NhdGlvbi5ocmVmKTtcbiAgICBjb25zdCBwYXJhbXMgPSB1cmwuc2VhcmNoUGFyYW1zO1xuXG4gICAgaWYgKHByZWZpeCkge1xuICAgICAgcGFyYW1zLnNldChwcmVmaXgrJ1tvcmRlckJ5XScsIGNvbE5hbWUpO1xuICAgICAgcGFyYW1zLnNldChwcmVmaXgrJ1tzb3J0T3JkZXJdJywgZGlyZWN0aW9uKTtcbiAgICB9IGVsc2Uge1xuICAgICAgcGFyYW1zLnNldCgnb3JkZXJCeScsIGNvbE5hbWUpO1xuICAgICAgcGFyYW1zLnNldCgnc29ydE9yZGVyJywgZGlyZWN0aW9uKTtcbiAgICB9XG5cbiAgICByZXR1cm4gdXJsLnRvU3RyaW5nKCk7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgVGFibGVTb3J0aW5nO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL3V0aWxzL3RhYmxlLXNvcnRpbmcuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG4vKipcbiAqIFNlbmQgYSBQb3N0IFJlcXVlc3QgdG8gcmVzZXQgc2VhcmNoIEFjdGlvbi5cbiAqL1xuXG5jb25zdCAkID0gZ2xvYmFsLiQ7XG5cbmNvbnN0IGluaXQgPSBmdW5jdGlvbiByZXNldFNlYXJjaCh1cmwsIHJlZGlyZWN0VXJsKSB7XG4gICAgJC5wb3N0KHVybCkudGhlbigoKSA9PiB3aW5kb3cubG9jYXRpb24uYXNzaWduKHJlZGlyZWN0VXJsKSk7XG59O1xuXG5leHBvcnQgZGVmYXVsdCBpbml0O1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL3V0aWxzL3Jlc2V0X3NlYXJjaC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIGhhbmRsZXMgbGluayByb3cgYWN0aW9uc1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtbGluay1yb3ctYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG5cbiAgICAgIGlmIChjb25maXJtTWVzc2FnZS5sZW5ndGggJiYgIWNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbGluay1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiQ29sdW1uIHRvZ2dsaW5nXCIgZmVhdHVyZVxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbiB7XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBjb25zdCAkdGFibGUgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJ3RhYmxlLnRhYmxlJyk7XG4gICAgJHRhYmxlLmZpbmQoJy5wcy10b2dnbGFibGUtcm93Jykub24oJ2NsaWNrJywgKGUpID0+IHtcbiAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgIHRoaXMuX3RvZ2dsZVZhbHVlKCQoZS5kZWxlZ2F0ZVRhcmdldCkpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSByb3dcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGVWYWx1ZShyb3cpIHtcbiAgICBjb25zdCB0b2dnbGVVcmwgPSByb3cuZGF0YSgndG9nZ2xlVXJsJyk7XG5cbiAgICB0aGlzLl9zdWJtaXRBc0Zvcm0odG9nZ2xlVXJsKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTdWJtaXRzIHJlcXVlc3QgdXJsIGFzIGZvcm1cbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IHRvZ2dsZVVybFxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3N1Ym1pdEFzRm9ybSh0b2dnbGVVcmwpIHtcbiAgICBjb25zdCAkZm9ybSA9ICQoJzxmb3JtPicsIHtcbiAgICAgIGFjdGlvbjogdG9nZ2xlVXJsLFxuICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgfSkuYXBwZW5kVG8oJ2JvZHknKTtcblxuICAgICRmb3JtLnN1Ym1pdCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi10b2dnbGluZy1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogSGFuZGxlcyBVSSBpbnRlcmFjdGlvbnMgb2YgY2hvaWNlIHRyZWVcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ2hvaWNlVHJlZSB7XG4gIC8qKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gdHJlZVNlbGVjdG9yXG4gICAqL1xuICBjb25zdHJ1Y3Rvcih0cmVlU2VsZWN0b3IpIHtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKHRyZWVTZWxlY3Rvcik7XG5cbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgJy5qcy1pbnB1dC13cmFwcGVyJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkaW5wdXRXcmFwcGVyID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcblxuICAgICAgdGhpcy5fdG9nZ2xlQ2hpbGRUcmVlKCRpbnB1dFdyYXBwZXIpO1xuICAgIH0pO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsICcuanMtdG9nZ2xlLWNob2ljZS10cmVlLWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGFjdGlvbiA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG5cbiAgICAgIHRoaXMuX3RvZ2dsZVRyZWUoJGFjdGlvbik7XG4gICAgfSk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW46ICgpID0+IHRoaXMuZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4oKSxcbiAgICAgIGVuYWJsZUFsbElucHV0czogKCkgPT4gdGhpcy5lbmFibGVBbGxJbnB1dHMoKSxcbiAgICAgIGRpc2FibGVBbGxJbnB1dHM6ICgpID0+IHRoaXMuZGlzYWJsZUFsbElucHV0cygpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogRW5hYmxlIGF1dG9tYXRpYyBjaGVjay91bmNoZWNrIG9mIGNsaWNrZWQgaXRlbSdzIGNoaWxkcmVuLlxuICAgKi9cbiAgZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4oKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjaGFuZ2UnLCAnaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkY2xpY2tlZENoZWNrYm94ID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0ICRpdGVtV2l0aENoaWxkcmVuID0gJGNsaWNrZWRDaGVja2JveC5jbG9zZXN0KCdsaScpO1xuXG4gICAgICAkaXRlbVdpdGhDaGlsZHJlblxuICAgICAgICAuZmluZCgndWwgaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJylcbiAgICAgICAgLnByb3AoJ2NoZWNrZWQnLCAkY2xpY2tlZENoZWNrYm94LmlzKCc6Y2hlY2tlZCcpKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbmFibGUgYWxsIGlucHV0cyBpbiB0aGUgY2hvaWNlIHRyZWUuXG4gICAqL1xuICBlbmFibGVBbGxJbnB1dHMoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLmZpbmQoJ2lucHV0JykucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNhYmxlIGFsbCBpbnB1dHMgaW4gdGhlIGNob2ljZSB0cmVlLlxuICAgKi9cbiAgZGlzYWJsZUFsbElucHV0cygpIHtcbiAgICB0aGlzLiRjb250YWluZXIuZmluZCgnaW5wdXQnKS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIENvbGxhcHNlIG9yIGV4cGFuZCBzdWItdHJlZSBmb3Igc2luZ2xlIHBhcmVudFxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGlucHV0V3JhcHBlclxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3RvZ2dsZUNoaWxkVHJlZSgkaW5wdXRXcmFwcGVyKSB7XG4gICAgY29uc3QgJHBhcmVudFdyYXBwZXIgPSAkaW5wdXRXcmFwcGVyLmNsb3Nlc3QoJ2xpJyk7XG5cbiAgICBpZiAoJHBhcmVudFdyYXBwZXIuaGFzQ2xhc3MoJ2V4cGFuZGVkJykpIHtcbiAgICAgICRwYXJlbnRXcmFwcGVyXG4gICAgICAgIC5yZW1vdmVDbGFzcygnZXhwYW5kZWQnKVxuICAgICAgICAuYWRkQ2xhc3MoJ2NvbGxhcHNlZCcpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgaWYgKCRwYXJlbnRXcmFwcGVyLmhhc0NsYXNzKCdjb2xsYXBzZWQnKSkge1xuICAgICAgJHBhcmVudFdyYXBwZXJcbiAgICAgICAgLnJlbW92ZUNsYXNzKCdjb2xsYXBzZWQnKVxuICAgICAgICAuYWRkQ2xhc3MoJ2V4cGFuZGVkJyk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIENvbGxhcHNlIG9yIGV4cGFuZCB3aG9sZSB0cmVlXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkYWN0aW9uXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdG9nZ2xlVHJlZSgkYWN0aW9uKSB7XG4gICAgY29uc3QgJHBhcmVudENvbnRhaW5lciA9ICRhY3Rpb24uY2xvc2VzdCgnLmpzLWNob2ljZS10cmVlLWNvbnRhaW5lcicpO1xuICAgIGNvbnN0IGFjdGlvbiA9ICRhY3Rpb24uZGF0YSgnYWN0aW9uJyk7XG5cbiAgICAvLyB0b2dnbGUgYWN0aW9uIGNvbmZpZ3VyYXRpb25cbiAgICBjb25zdCBjb25maWcgPSB7XG4gICAgICBhZGRDbGFzczoge1xuICAgICAgICBleHBhbmQ6ICdleHBhbmRlZCcsXG4gICAgICAgIGNvbGxhcHNlOiAnY29sbGFwc2VkJyxcbiAgICAgIH0sXG4gICAgICByZW1vdmVDbGFzczoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZWQnLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZGVkJyxcbiAgICAgIH0sXG4gICAgICBuZXh0QWN0aW9uOiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlJyxcbiAgICAgICAgY29sbGFwc2U6ICdleHBhbmQnLFxuICAgICAgfSxcbiAgICAgIHRleHQ6IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2VkLXRleHQnLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZGVkLXRleHQnLFxuICAgICAgfSxcbiAgICAgIGljb246IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2VkLWljb24nLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZGVkLWljb24nLFxuICAgICAgfVxuICAgIH07XG5cbiAgICAkcGFyZW50Q29udGFpbmVyLmZpbmQoJ2xpJykuZWFjaCgoaW5kZXgsIGl0ZW0pID0+IHtcbiAgICAgIGNvbnN0ICRpdGVtID0gJChpdGVtKTtcblxuICAgICAgaWYgKCRpdGVtLmhhc0NsYXNzKGNvbmZpZy5yZW1vdmVDbGFzc1thY3Rpb25dKSkge1xuICAgICAgICAgICRpdGVtLnJlbW92ZUNsYXNzKGNvbmZpZy5yZW1vdmVDbGFzc1thY3Rpb25dKVxuICAgICAgICAgICAgLmFkZENsYXNzKGNvbmZpZy5hZGRDbGFzc1thY3Rpb25dKTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgICRhY3Rpb24uZGF0YSgnYWN0aW9uJywgY29uZmlnLm5leHRBY3Rpb25bYWN0aW9uXSk7XG4gICAgJGFjdGlvbi5maW5kKCcubWF0ZXJpYWwtaWNvbnMnKS50ZXh0KCRhY3Rpb24uZGF0YShjb25maWcuaWNvblthY3Rpb25dKSk7XG4gICAgJGFjdGlvbi5maW5kKCcuanMtdG9nZ2xlLXRleHQnKS50ZXh0KCRhY3Rpb24uZGF0YShjb25maWcudGV4dFthY3Rpb25dKSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZm9ybS9jaG9pY2UtdHJlZS5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBpcyByZXNwb25zaWJsZSBmb3IgaGFuZGxpbmcgR3JpZCBldmVudHNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgR3JpZCB7XG4gIC8qKlxuICAgKiBHcmlkIGlkXG4gICAqXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBpZFxuICAgKi9cbiAgY29uc3RydWN0b3IoaWQpIHtcbiAgICB0aGlzLmlkID0gaWQ7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJCgnIycgKyB0aGlzLmlkICsgJ19ncmlkJyk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGdyaWQgaWRcbiAgICpcbiAgICogQHJldHVybnMge3N0cmluZ31cbiAgICovXG4gIGdldElkKCkge1xuICAgIHJldHVybiB0aGlzLmlkO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGNvbnRhaW5lclxuICAgKlxuICAgKiBAcmV0dXJucyB7alF1ZXJ5fVxuICAgKi9cbiAgZ2V0Q29udGFpbmVyKCkge1xuICAgIHJldHVybiB0aGlzLiRjb250YWluZXI7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGdyaWQgaGVhZGVyIGNvbnRhaW5lclxuICAgKlxuICAgKiBAcmV0dXJucyB7alF1ZXJ5fVxuICAgKi9cbiAgZ2V0SGVhZGVyQ29udGFpbmVyKCkge1xuICAgIHJldHVybiB0aGlzLiRjb250YWluZXIuY2xvc2VzdCgnLmpzLWdyaWQtcGFuZWwnKS5maW5kKCcuanMtZ3JpZC1oZWFkZXInKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZCB3aXRoIGV4dGVybmFsIGV4dGVuc2lvbnNcbiAgICpcbiAgICogQHBhcmFtIHtvYmplY3R9IGV4dGVuc2lvblxuICAgKi9cbiAgYWRkRXh0ZW5zaW9uKGV4dGVuc2lvbikge1xuICAgIGV4dGVuc2lvbi5leHRlbmQodGhpcyk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9ncmlkLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IEdyaWQgZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2dyaWQnO1xuaW1wb3J0IFJlbG9hZExpc3RBY3Rpb25FeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9yZWxvYWQtbGlzdC1leHRlbnNpb24nO1xuaW1wb3J0IEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2V4cG9ydC10by1zcWwtbWFuYWdlci1leHRlbnNpb24nO1xuaW1wb3J0IEZpbHRlcnNSZXNldEV4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uJztcbmltcG9ydCBTb3J0aW5nRXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc29ydGluZy1leHRlbnNpb24nO1xuaW1wb3J0IExpbmtSb3dBY3Rpb25FeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9saW5rLXJvdy1hY3Rpb24tZXh0ZW5zaW9uJztcbmltcG9ydCBTdWJtaXRCdWxrRXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc3VibWl0LWJ1bGstYWN0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYWN0aW9uL3Jvdy9zdWJtaXQtcm93LWFjdGlvbi1leHRlbnNpb24nO1xuaW1wb3J0IEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2J1bGstYWN0aW9uLWNoZWNrYm94LWV4dGVuc2lvbic7XG5pbXBvcnQgQ2hvaWNlVHJlZSBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9mb3JtL2Nob2ljZS10cmVlXCI7XG5pbXBvcnQgQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24gZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vY29sdW1uLXRvZ2dsaW5nLWV4dGVuc2lvblwiO1xuaW1wb3J0IEZpbHRlcnNTdWJtaXRCdXR0b25FbmFibGVyRXh0ZW5zaW9uXG4gIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1zdWJtaXQtYnV0dG9uLWVuYWJsZXItZXh0ZW5zaW9uJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKGRvY3VtZW50KS5yZWFkeSgoKSA9PiB7XG4gIGNvbnN0IGdyaWQgPSBuZXcgR3JpZCgnbGFuZ3VhZ2UnKTtcblxuICBncmlkLmFkZEV4dGVuc2lvbihuZXcgUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbigpKTtcbiAgZ3JpZC5hZGRFeHRlbnNpb24obmV3IEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbigpKTtcbiAgZ3JpZC5hZGRFeHRlbnNpb24obmV3IEZpbHRlcnNSZXNldEV4dGVuc2lvbigpKTtcbiAgZ3JpZC5hZGRFeHRlbnNpb24obmV3IFNvcnRpbmdFeHRlbnNpb24oKSk7XG4gIGdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uKCkpO1xuICBncmlkLmFkZEV4dGVuc2lvbihuZXcgU3VibWl0QnVsa0V4dGVuc2lvbigpKTtcbiAgZ3JpZC5hZGRFeHRlbnNpb24obmV3IFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbigpKTtcbiAgZ3JpZC5hZGRFeHRlbnNpb24obmV3IEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbigpKTtcbiAgZ3JpZC5hZGRFeHRlbnNpb24obmV3IENvbHVtblRvZ2dsaW5nRXh0ZW5zaW9uKCkpO1xuICBncmlkLmFkZEV4dGVuc2lvbihuZXcgRmlsdGVyc1N1Ym1pdEJ1dHRvbkVuYWJsZXJFeHRlbnNpb24oKSk7XG5cbiAgLy8gbmVlZGVkIGZvciBzaG9wIGFzc29jaWF0aW9uIGlucHV0IGluIGZvcm1cbiAgbmV3IENob2ljZVRyZWUoJyNsYW5ndWFnZV9zaG9wX2Fzc29jaWF0aW9uJykuZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4oKTtcbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvbGFuZ3VhZ2UvaW5kZXguanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgcmVzZXRTZWFyY2ggZnJvbSAnLi4vLi4vLi4vYXBwL3V0aWxzL3Jlc2V0X3NlYXJjaCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggZmlsdGVycyByZXNldHRpbmdcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIHtcblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1yZXNldC1zZWFyY2gnLCAoZXZlbnQpID0+IHtcbiAgICAgIHJlc2V0U2VhcmNoKCQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgndXJsJyksICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgncmVkaXJlY3QnKSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1yZXNldC1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG4vKipcbiAqIENsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggXCJMaXN0IHJlbG9hZFwiIGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX3JlZnJlc2hfbGlzdC1ncmlkLWFjdGlvbicsICgpID0+IHtcbiAgICAgIGxvY2F0aW9uLnJlbG9hZCgpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBUYWJsZVNvcnRpbmcgZnJvbSAnLi4vLi4vLi4vYXBwL3V0aWxzL3RhYmxlLXNvcnRpbmcnO1xuXG4vKipcbiAqIENsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggXCJMaXN0IHJlbG9hZFwiIGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTb3J0aW5nRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBjb25zdCAkc29ydGFibGVUYWJsZSA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgndGFibGUudGFibGUnKTtcblxuICAgIG5ldyBUYWJsZVNvcnRpbmcoJHNvcnRhYmxlVGFibGUpLmF0dGFjaCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBSZXNwb25zaWJsZSBmb3IgZ3JpZCBmaWx0ZXJzIHNlYXJjaCBhbmQgcmVzZXQgYnV0dG9uIGF2YWlsYWJpbGl0eSB3aGVuIGZpbHRlciBpbnB1dHMgY2hhbmdlcy5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRmlsdGVyc1N1Ym1pdEJ1dHRvbkVuYWJsZXJFeHRlbnNpb24ge1xuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgY29uc3QgJGZpbHRlcnNSb3cgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5jb2x1bW4tZmlsdGVycycpO1xuICAgICRmaWx0ZXJzUm93LmZpbmQoJy5ncmlkLXNlYXJjaC1idXR0b24nKS5wcm9wKCdkaXNhYmxlZCcsIHRydWUpO1xuXG4gICAgJGZpbHRlcnNSb3cuZmluZCgnaW5wdXQsIHNlbGVjdCcpLm9uKCdpbnB1dCcsICgpID0+IHtcbiAgICAgICRmaWx0ZXJzUm93LmZpbmQoJy5ncmlkLXNlYXJjaC1idXR0b24nKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICAgICRmaWx0ZXJzUm93LmZpbmQoJy5qcy1ncmlkLXJlc2V0LWJ1dHRvbicpLnByb3AoJ2hpZGRlbicsIGZhbHNlKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXN1Ym1pdC1idXR0b24tZW5hYmxlci1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgQnVsa0FjdGlvblNlbGVjdENoZWNrYm94RXh0ZW5zaW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZCB3aXRoIGJ1bGsgYWN0aW9uIGNoZWNrYm94ZXMgaGFuZGxpbmcgZnVuY3Rpb25hbGl0eVxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgdGhpcy5faGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0KGdyaWQpO1xuICAgIHRoaXMuX2hhbmRsZUJ1bGtBY3Rpb25TZWxlY3RBbGxDaGVja2JveChncmlkKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIFwiU2VsZWN0IGFsbFwiIGJ1dHRvbiBpbiB0aGUgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oYW5kbGVCdWxrQWN0aW9uU2VsZWN0QWxsQ2hlY2tib3goZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NoYW5nZScsICcuanMtYnVsay1hY3Rpb24tc2VsZWN0LWFsbCcsIChlKSA9PiB7XG4gICAgICBjb25zdCAkY2hlY2tib3ggPSAkKGUuY3VycmVudFRhcmdldCk7XG5cbiAgICAgIGNvbnN0IGlzQ2hlY2tlZCA9ICRjaGVja2JveC5pcygnOmNoZWNrZWQnKTtcbiAgICAgIGlmIChpc0NoZWNrZWQpIHtcbiAgICAgICAgdGhpcy5fZW5hYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB0aGlzLl9kaXNhYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9XG5cbiAgICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9uLWNoZWNrYm94JykucHJvcCgnY2hlY2tlZCcsIGlzQ2hlY2tlZCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlcyBlYWNoIGJ1bGsgYWN0aW9uIGNoZWNrYm94IHNlbGVjdCBpbiB0aGUgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oYW5kbGVCdWxrQWN0aW9uQ2hlY2tib3hTZWxlY3QoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NoYW5nZScsICcuanMtYnVsay1hY3Rpb24tY2hlY2tib3gnLCAoKSA9PiB7XG4gICAgICBjb25zdCBjaGVja2VkUm93c0NvdW50ID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb24tY2hlY2tib3g6Y2hlY2tlZCcpLmxlbmd0aDtcblxuICAgICAgaWYgKGNoZWNrZWRSb3dzQ291bnQgPiAwKSB7XG4gICAgICAgIHRoaXMuX2VuYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGhpcy5fZGlzYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEVuYWJsZSBidWxrIGFjdGlvbnMgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2VuYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbnMtYnRuJykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gIH1cblxuICAvKipcbiAgICogRGlzYWJsZSBidWxrIGFjdGlvbnMgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2Rpc2FibGVCdWxrQWN0aW9uc0J0bihncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb25zLWJ0bicpLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYnVsay1hY3Rpb24tY2hlY2tib3gtZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBleHBvcnRpbmcgcXVlcnkgdG8gU1FMIE1hbmFnZXJcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX3Nob3dfcXVlcnktZ3JpZC1hY3Rpb24nLCAoKSA9PiB0aGlzLl9vblNob3dTcWxRdWVyeUNsaWNrKGdyaWQpKTtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX2V4cG9ydF9zcWxfbWFuYWdlci1ncmlkLWFjdGlvbicsICgpID0+IHRoaXMuX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrKGdyaWQpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbnZva2VkIHdoZW4gY2xpY2tpbmcgb24gdGhlIFwic2hvdyBzcWwgcXVlcnlcIiB0b29sYmFyIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vblNob3dTcWxRdWVyeUNsaWNrKGdyaWQpIHtcbiAgICBjb25zdCAkc3FsTWFuYWdlckZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfY29tbW9uX3Nob3dfcXVlcnlfbW9kYWxfZm9ybScpO1xuICAgIHRoaXMuX2ZpbGxFeHBvcnRGb3JtKCRzcWxNYW5hZ2VyRm9ybSwgZ3JpZCk7XG5cbiAgICBjb25zdCAkbW9kYWwgPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfZ3JpZF9jb21tb25fc2hvd19xdWVyeV9tb2RhbCcpO1xuICAgICRtb2RhbC5tb2RhbCgnc2hvdycpO1xuXG4gICAgJG1vZGFsLm9uKCdjbGljaycsICcuYnRuLXNxbC1zdWJtaXQnLCAoKSA9PiAkc3FsTWFuYWdlckZvcm0uc3VibWl0KCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEludm9rZWQgd2hlbiBjbGlja2luZyBvbiB0aGUgXCJleHBvcnQgdG8gdGhlIHNxbCBxdWVyeVwiIHRvb2xiYXIgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrKGdyaWQpIHtcbiAgICBjb25zdCAkc3FsTWFuYWdlckZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfY29tbW9uX3Nob3dfcXVlcnlfbW9kYWxfZm9ybScpO1xuXG4gICAgdGhpcy5fZmlsbEV4cG9ydEZvcm0oJHNxbE1hbmFnZXJGb3JtLCBncmlkKTtcblxuICAgICRzcWxNYW5hZ2VyRm9ybS5zdWJtaXQoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBGaWxsIGV4cG9ydCBmb3JtIHdpdGggU1FMIGFuZCBpdCdzIG5hbWVcbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRzcWxNYW5hZ2VyRm9ybVxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9maWxsRXhwb3J0Rm9ybSgkc3FsTWFuYWdlckZvcm0sIGdyaWQpIHtcbiAgICBjb25zdCBxdWVyeSA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWdyaWQtdGFibGUnKS5kYXRhKCdxdWVyeScpO1xuXG4gICAgJHNxbE1hbmFnZXJGb3JtLmZpbmQoJ3RleHRhcmVhW25hbWU9XCJzcWxcIl0nKS52YWwocXVlcnkpO1xuICAgICRzcWxNYW5hZ2VyRm9ybS5maW5kKCdpbnB1dFtuYW1lPVwibmFtZVwiXScpLnZhbCh0aGlzLl9nZXROYW1lRnJvbUJyZWFkY3J1bWIoKSk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGV4cG9ydCBuYW1lIGZyb20gcGFnZSdzIGJyZWFkY3J1bWJcbiAgICpcbiAgICogQHJldHVybiB7U3RyaW5nfVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldE5hbWVGcm9tQnJlYWRjcnVtYigpIHtcbiAgICBjb25zdCAkYnJlYWRjcnVtYnMgPSAkKCcuaGVhZGVyLXRvb2xiYXInKS5maW5kKCcuYnJlYWRjcnVtYi1pdGVtJyk7XG4gICAgbGV0IG5hbWUgPSAnJztcblxuICAgICRicmVhZGNydW1icy5lYWNoKChpLCBpdGVtKSA9PiB7XG4gICAgICBjb25zdCAkYnJlYWRjcnVtYiA9ICQoaXRlbSk7XG5cbiAgICAgIGNvbnN0IGJyZWFkY3J1bWJUaXRsZSA9IDAgPCAkYnJlYWRjcnVtYi5maW5kKCdhJykubGVuZ3RoID9cbiAgICAgICAgJGJyZWFkY3J1bWIuZmluZCgnYScpLnRleHQoKSA6XG4gICAgICAgICRicmVhZGNydW1iLnRleHQoKTtcblxuICAgICAgaWYgKDAgPCBuYW1lLmxlbmd0aCkge1xuICAgICAgICBuYW1lID0gbmFtZS5jb25jYXQoJyA+ICcpO1xuICAgICAgfVxuXG4gICAgICBuYW1lID0gbmFtZS5jb25jYXQoYnJlYWRjcnVtYlRpdGxlKTtcbiAgICB9KTtcblxuICAgIHJldHVybiBuYW1lO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2V4cG9ydC10by1zcWwtbWFuYWdlci1leHRlbnNpb24uanMiXSwic291cmNlUm9vdCI6IiJ9