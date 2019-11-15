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
/******/ 	return __webpack_require__(__webpack_require__.s = 392);
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

          /*
           * Only search for cells with non clickable contents to avoid conflicts with
           * previous cell behaviour (action, toggle, ...)
           */
          var clickableCells = $('td.data-type, td.identifier-type:not(:has(input)), td.badge-type, td.position-type', $parentRow).not($parentCell);

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

/***/ 14:
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
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(0)))

/***/ }),

/***/ 15:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

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
 * Send a Post Request to reset search Action.
 */

var $ = global.$;

var init = function resetSearch(url, redirectUrl) {
  $.post(url).then(function () {
    return window.location.assign(redirectUrl);
  });
};

exports.default = init;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(0)))

/***/ }),

/***/ 18:
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

/***/ 246:
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

/***/ 291:
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

var _reset_search = __webpack_require__(15);

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

/***/ 392:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _grid = __webpack_require__(2);

var _grid2 = _interopRequireDefault(_grid);

var _filtersResetExtension = __webpack_require__(3);

var _filtersResetExtension2 = _interopRequireDefault(_filtersResetExtension);

var _reloadListExtension = __webpack_require__(5);

var _reloadListExtension2 = _interopRequireDefault(_reloadListExtension);

var _exportToSqlManagerExtension = __webpack_require__(8);

var _exportToSqlManagerExtension2 = _interopRequireDefault(_exportToSqlManagerExtension);

var _bulkActionCheckboxExtension = __webpack_require__(7);

var _bulkActionCheckboxExtension2 = _interopRequireDefault(_bulkActionCheckboxExtension);

var _submitBulkActionExtension = __webpack_require__(9);

var _submitBulkActionExtension2 = _interopRequireDefault(_submitBulkActionExtension);

var _sortingExtension = __webpack_require__(4);

var _sortingExtension2 = _interopRequireDefault(_sortingExtension);

var _submitRowActionExtension = __webpack_require__(11);

var _submitRowActionExtension2 = _interopRequireDefault(_submitRowActionExtension);

var _columnTogglingExtension = __webpack_require__(18);

var _columnTogglingExtension2 = _interopRequireDefault(_columnTogglingExtension);

var _choiceTree = __webpack_require__(20);

var _choiceTree2 = _interopRequireDefault(_choiceTree);

var _generatableInput = __webpack_require__(246);

var _generatableInput2 = _interopRequireDefault(_generatableInput);

var _multipleChoiceTable = __webpack_require__(66);

var _multipleChoiceTable2 = _interopRequireDefault(_multipleChoiceTable);

var _permissionsRowSelector = __webpack_require__(291);

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

var _tableSorting = __webpack_require__(14);

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

/***/ 66:
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

/***/ 7:
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMWU2NjI2MzkwMGU5NjZkZmJiZjA/ODU5MCoqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLyh3ZWJwYWNrKS9idWlsZGluL2dsb2JhbC5qcz8zNjk4KioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbGluay1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcz8zOWRjKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYWN0aW9uL3Jvdy9zdWJtaXQtcm93LWFjdGlvbi1leHRlbnNpb24uanM/MjdkMSoqKioqKioqIiwid2VicGFjazovLy8uL2pzL2FwcC91dGlscy90YWJsZS1zb3J0aW5nLmpzPzE1ZDQqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2FwcC91dGlscy9yZXNldF9zZWFyY2guanM/MWE3ZioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9jb2x1bW4tdG9nZ2xpbmctZXh0ZW5zaW9uLmpzPzY5NDMqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9ncmlkLmpzPzgxM2EqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZm9ybS9jaG9pY2UtdHJlZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dlbmVyYXRhYmxlLWlucHV0LmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL3dlYnNlcnZpY2UvcGVybWlzc2lvbnMtcm93LXNlbGVjdG9yLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1yZXNldC1leHRlbnNpb24uanM/MTZmMSoqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvd2Vic2VydmljZS9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uLmpzPzExM2UqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcmVsb2FkLWxpc3QtZXh0ZW5zaW9uLmpzP2QzZTAqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvbXVsdGlwbGUtY2hvaWNlLXRhYmxlLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYnVsay1hY3Rpb24tY2hlY2tib3gtZXh0ZW5zaW9uLmpzP2IwOTcqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZXhwb3J0LXRvLXNxbC1tYW5hZ2VyLWV4dGVuc2lvbi5qcz9lZDJhKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1idWxrLWFjdGlvbi1leHRlbnNpb24uanM/MWIxZioqKioqKioqKioiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIkxpbmtSb3dBY3Rpb25FeHRlbnNpb24iLCJncmlkIiwiaW5pdFJvd0xpbmtzIiwiaW5pdENvbmZpcm1hYmxlQWN0aW9ucyIsImdldENvbnRhaW5lciIsIm9uIiwiZXZlbnQiLCJjb25maXJtTWVzc2FnZSIsImN1cnJlbnRUYXJnZXQiLCJkYXRhIiwibGVuZ3RoIiwiY29uZmlybSIsInByZXZlbnREZWZhdWx0IiwiZWFjaCIsImluaXRFYWNoUm93IiwiJHBhcmVudFJvdyIsInByb3BhZ2F0ZUZpcnN0TGlua0FjdGlvbiIsIiRyb3dBY3Rpb24iLCIkcGFyZW50Q2VsbCIsImNsb3Nlc3QiLCJjbGlja2FibGVDZWxscyIsIm5vdCIsImFkZENsYXNzIiwiY2xpY2siLCJkb2N1bWVudCIsImxvY2F0aW9uIiwiYXR0ciIsIlN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiIsIiRidXR0b24iLCJtZXRob2QiLCJpc0dldE9yUG9zdE1ldGhvZCIsImluY2x1ZGVzIiwiJGZvcm0iLCJhcHBlbmRUbyIsImFwcGVuZCIsInN1Ym1pdCIsImdsb2JhbCIsIlRhYmxlU29ydGluZyIsInRhYmxlIiwic2VsZWN0b3IiLCJjb2x1bW5zIiwiZmluZCIsImUiLCIkY29sdW1uIiwiZGVsZWdhdGVUYXJnZXQiLCJfc29ydEJ5Q29sdW1uIiwiX2dldFRvZ2dsZWRTb3J0RGlyZWN0aW9uIiwiY29sdW1uTmFtZSIsImRpcmVjdGlvbiIsImlzIiwiRXJyb3IiLCJjb2x1bW4iLCJfZ2V0VXJsIiwiY29sTmFtZSIsInByZWZpeCIsInVybCIsIlVSTCIsImhyZWYiLCJwYXJhbXMiLCJzZWFyY2hQYXJhbXMiLCJzZXQiLCJ0b1N0cmluZyIsImluaXQiLCJyZXNldFNlYXJjaCIsInJlZGlyZWN0VXJsIiwicG9zdCIsInRoZW4iLCJhc3NpZ24iLCJDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbiIsIiR0YWJsZSIsIl90b2dnbGVWYWx1ZSIsInJvdyIsInRvZ2dsZVVybCIsIl9zdWJtaXRBc0Zvcm0iLCJhY3Rpb24iLCJHcmlkIiwiaWQiLCIkY29udGFpbmVyIiwiZXh0ZW5zaW9uIiwiZXh0ZW5kIiwiQ2hvaWNlVHJlZSIsInRyZWVTZWxlY3RvciIsIiRpbnB1dFdyYXBwZXIiLCJfdG9nZ2xlQ2hpbGRUcmVlIiwiJGFjdGlvbiIsIl90b2dnbGVUcmVlIiwiZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4iLCJlbmFibGVBbGxJbnB1dHMiLCJkaXNhYmxlQWxsSW5wdXRzIiwiJGNsaWNrZWRDaGVja2JveCIsIiRpdGVtV2l0aENoaWxkcmVuIiwicHJvcCIsInJlbW92ZUF0dHIiLCIkcGFyZW50V3JhcHBlciIsImhhc0NsYXNzIiwicmVtb3ZlQ2xhc3MiLCIkcGFyZW50Q29udGFpbmVyIiwiY29uZmlnIiwiZXhwYW5kIiwiY29sbGFwc2UiLCJuZXh0QWN0aW9uIiwidGV4dCIsImljb24iLCJpbmRleCIsIml0ZW0iLCIkaXRlbSIsIkdlbmVyYXRhYmxlSW5wdXQiLCJidG5TZWxlY3RvciIsIl9hdHRhY2hPbiIsImdlbmVyYXRvckJ0blNlbGVjdG9yIiwicXVlcnlTZWxlY3RvciIsImFkZEV2ZW50TGlzdGVuZXIiLCJhdHRyaWJ1dGVzIiwidGFyZ2V0SW5wdXRJZCIsImdldE5hbWVkSXRlbSIsInZhbHVlIiwiZ2VuZXJhdGVkVmFsdWVMZW5ndGgiLCJwYXJzZUludCIsInRhcmdldElucHV0IiwiX2dlbmVyYXRlVmFsdWUiLCJjaGFycyIsImdlbmVyYXRlZFZhbHVlIiwiaSIsImNoYXJBdCIsIk1hdGgiLCJmbG9vciIsInJhbmRvbSIsIlBlcm1pc3Npb25zUm93U2VsZWN0b3IiLCIkY2hlY2tlZEJveCIsImlzQ2hlY2tlZCIsImlucHV0IiwiRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIiwid2Vic2VydmljZUdyaWQiLCJhZGRFeHRlbnNpb24iLCJSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uIiwiRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIiwiU29ydGluZ0V4dGVuc2lvbiIsIlN1Ym1pdEJ1bGtBY3Rpb25FeHRlbnNpb24iLCJCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24iLCJNdWx0aXBsZUNob2ljZVRhYmxlIiwiZ2VuZXJhdGFibGVJbnB1dCIsImF0dGFjaE9uIiwiJHNvcnRhYmxlVGFibGUiLCJhdHRhY2giLCJSZWxvYWRMaXN0RXh0ZW5zaW9uIiwiZ2V0SGVhZGVyQ29udGFpbmVyIiwicmVsb2FkIiwiaGFuZGxlU2VsZWN0Q29sdW1uIiwiJHNlbGVjdENvbHVtbkJ0biIsInRhcmdldCIsImNoZWNrZWQiLCJfaGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0IiwiX2hhbmRsZUJ1bGtBY3Rpb25TZWxlY3RBbGxDaGVja2JveCIsIiRjaGVja2JveCIsIl9lbmFibGVCdWxrQWN0aW9uc0J0biIsIl9kaXNhYmxlQnVsa0FjdGlvbnNCdG4iLCJjaGVja2VkUm93c0NvdW50IiwiX29uU2hvd1NxbFF1ZXJ5Q2xpY2siLCJfb25FeHBvcnRTcWxNYW5hZ2VyQ2xpY2siLCIkc3FsTWFuYWdlckZvcm0iLCJnZXRJZCIsIl9maWxsRXhwb3J0Rm9ybSIsIiRtb2RhbCIsIm1vZGFsIiwicXVlcnkiLCJ2YWwiLCJfZ2V0TmFtZUZyb21CcmVhZGNydW1iIiwiJGJyZWFkY3J1bWJzIiwibmFtZSIsIiRicmVhZGNydW1iIiwiYnJlYWRjcnVtYlRpdGxlIiwiY29uY2F0IiwiJHN1Ym1pdEJ0biJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0EsbURBQTJDLGNBQWM7O0FBRXpEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7O0FBRUE7QUFDQTs7Ozs7Ozs7QUNoRUE7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsQ0FBQzs7QUFFRDtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLDRDQUE0Qzs7QUFFNUM7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNwQkE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTUEsSUFBSUMsT0FBT0QsQ0FBakI7O0FBRUE7Ozs7SUFHcUJFLHNCOzs7Ozs7OztBQUNuQjs7Ozs7MkJBS09DLEksRUFBTTtBQUNYLFdBQUtDLFlBQUwsQ0FBa0JELElBQWxCO0FBQ0EsV0FBS0Usc0JBQUwsQ0FBNEJGLElBQTVCO0FBQ0Q7O0FBRUQ7Ozs7Ozs7OzJDQUt1QkEsSSxFQUFNO0FBQzNCQSxXQUFLRyxZQUFMLEdBQW9CQyxFQUFw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QUFYLENBQWdCLGFBQWhCLENBQXJCO0FBQ0F1QixZQUFNRyxNQUFOO0FBQ0Q7Ozs7OztrQkF2Q2tCc0cseUIiLCJmaWxlIjoid2Vic2VydmljZS5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDM5Mik7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgMWU2NjI2MzkwMGU5NjZkZmJiZjAiLCJ2YXIgZztcclxuXHJcbi8vIFRoaXMgd29ya3MgaW4gbm9uLXN0cmljdCBtb2RlXHJcbmcgPSAoZnVuY3Rpb24oKSB7XHJcblx0cmV0dXJuIHRoaXM7XHJcbn0pKCk7XHJcblxyXG50cnkge1xyXG5cdC8vIFRoaXMgd29ya3MgaWYgZXZhbCBpcyBhbGxvd2VkIChzZWUgQ1NQKVxyXG5cdGcgPSBnIHx8IEZ1bmN0aW9uKFwicmV0dXJuIHRoaXNcIikoKSB8fCAoMSxldmFsKShcInRoaXNcIik7XHJcbn0gY2F0Y2goZSkge1xyXG5cdC8vIFRoaXMgd29ya3MgaWYgdGhlIHdpbmRvdyByZWZlcmVuY2UgaXMgYXZhaWxhYmxlXHJcblx0aWYodHlwZW9mIHdpbmRvdyA9PT0gXCJvYmplY3RcIilcclxuXHRcdGcgPSB3aW5kb3c7XHJcbn1cclxuXHJcbi8vIGcgY2FuIHN0aWxsIGJlIHVuZGVmaW5lZCwgYnV0IG5vdGhpbmcgdG8gZG8gYWJvdXQgaXQuLi5cclxuLy8gV2UgcmV0dXJuIHVuZGVmaW5lZCwgaW5zdGVhZCBvZiBub3RoaW5nIGhlcmUsIHNvIGl0J3NcclxuLy8gZWFzaWVyIHRvIGhhbmRsZSB0aGlzIGNhc2UuIGlmKCFnbG9iYWwpIHsgLi4ufVxyXG5cclxubW9kdWxlLmV4cG9ydHMgPSBnO1xyXG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAod2VicGFjaykvYnVpbGRpbi9nbG9iYWwuanNcbi8vIG1vZHVsZSBpZCA9IDBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDM0IDM3IDQ1IDQ5IiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIExpbmtSb3dBY3Rpb25FeHRlbnNpb24gaGFuZGxlcyBsaW5rIHJvdyBhY3Rpb25zXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIExpbmtSb3dBY3Rpb25FeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIHRoaXMuaW5pdFJvd0xpbmtzKGdyaWQpO1xuICAgIHRoaXMuaW5pdENvbmZpcm1hYmxlQWN0aW9ucyhncmlkKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGluaXRDb25maXJtYWJsZUFjdGlvbnMoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1saW5rLXJvdy1hY3Rpb24nLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcblxuICAgICAgaWYgKGNvbmZpcm1NZXNzYWdlLmxlbmd0aCAmJiAhY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBZGQgYSBjbGljayBldmVudCBvbiByb3dzIHRoYXQgbWF0Y2hlcyB0aGUgZmlyc3QgbGluayBhY3Rpb24gKGlmIHByZXNlbnQpXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgaW5pdFJvd0xpbmtzKGdyaWQpIHtcbiAgICAkKCd0cicsIGdyaWQuZ2V0Q29udGFpbmVyKCkpLmVhY2goZnVuY3Rpb24gaW5pdEVhY2hSb3coKSB7XG4gICAgICBjb25zdCAkcGFyZW50Um93ID0gJCh0aGlzKTtcblxuICAgICAgJCgnLmpzLWxpbmstcm93LWFjdGlvbltkYXRhLWNsaWNrYWJsZS1yb3c9MV06Zmlyc3QnLCAkcGFyZW50Um93KS5lYWNoKGZ1bmN0aW9uIHByb3BhZ2F0ZUZpcnN0TGlua0FjdGlvbigpIHtcbiAgICAgICAgY29uc3QgJHJvd0FjdGlvbiA9ICQodGhpcyk7XG4gICAgICAgIGNvbnN0ICRwYXJlbnRDZWxsID0gJHJvd0FjdGlvbi5jbG9zZXN0KCd0ZCcpO1xuXG4gICAgICAgIC8qXG4gICAgICAgICAqIE9ubHkgc2VhcmNoIGZvciBjZWxscyB3aXRoIG5vbiBjbGlja2FibGUgY29udGVudHMgdG8gYXZvaWQgY29uZmxpY3RzIHdpdGhcbiAgICAgICAgICogcHJldmlvdXMgY2VsbCBiZWhhdmlvdXIgKGFjdGlvbiwgdG9nZ2xlLCAuLi4pXG4gICAgICAgICAqL1xuICAgICAgICBjb25zdCBjbGlja2FibGVDZWxscyA9ICQoJ3RkLmRhdGEtdHlwZSwgdGQuaWRlbnRpZmllci10eXBlOm5vdCg6aGFzKGlucHV0KSksIHRkLmJhZGdlLXR5cGUsIHRkLnBvc2l0aW9uLXR5cGUnLCAkcGFyZW50Um93KVxuICAgICAgICAgIC5ub3QoJHBhcmVudENlbGwpXG4gICAgICAgIDtcblxuICAgICAgICBjbGlja2FibGVDZWxscy5hZGRDbGFzcygnY3Vyc29yLXBvaW50ZXInKS5jbGljaygoKSA9PiB7XG4gICAgICAgICAgY29uc3QgY29uZmlybU1lc3NhZ2UgPSAkcm93QWN0aW9uLmRhdGEoJ2NvbmZpcm0tbWVzc2FnZScpO1xuXG4gICAgICAgICAgaWYgKCFjb25maXJtTWVzc2FnZS5sZW5ndGggfHwgY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgICAgICAgIGRvY3VtZW50LmxvY2F0aW9uID0gJHJvd0FjdGlvbi5hdHRyKCdocmVmJyk7XG4gICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICAgIH0pO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2xpbmstcm93LWFjdGlvbi1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uIGhhbmRsZXMgc3VibWl0dGluZyBvZiByb3cgYWN0aW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLXN1Ym1pdC1yb3ctYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICBjb25zdCAkYnV0dG9uID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJGJ1dHRvbi5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcblxuICAgICAgaWYgKGNvbmZpcm1NZXNzYWdlLmxlbmd0aCAmJiAhY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBjb25zdCBtZXRob2QgPSAkYnV0dG9uLmRhdGEoJ21ldGhvZCcpO1xuICAgICAgY29uc3QgaXNHZXRPclBvc3RNZXRob2QgPSBbJ0dFVCcsICdQT1NUJ10uaW5jbHVkZXMobWV0aG9kKTtcblxuICAgICAgY29uc3QgJGZvcm0gPSAkKCc8Zm9ybT4nLCB7XG4gICAgICAgICdhY3Rpb24nOiAkYnV0dG9uLmRhdGEoJ3VybCcpLFxuICAgICAgICAnbWV0aG9kJzogaXNHZXRPclBvc3RNZXRob2QgPyBtZXRob2QgOiAnUE9TVCcsXG4gICAgICB9KS5hcHBlbmRUbygnYm9keScpO1xuXG4gICAgICBpZiAoIWlzR2V0T3JQb3N0TWV0aG9kKSB7XG4gICAgICAgICRmb3JtLmFwcGVuZCgkKCc8aW5wdXQ+Jywge1xuICAgICAgICAgICd0eXBlJzogJ19oaWRkZW4nLFxuICAgICAgICAgICduYW1lJzogJ19tZXRob2QnLFxuICAgICAgICAgICd2YWx1ZSc6IG1ldGhvZFxuICAgICAgICB9KSk7XG4gICAgICB9XG5cbiAgICAgICRmb3JtLnN1Ym1pdCgpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9yb3cvc3VibWl0LXJvdy1hY3Rpb24tZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG4vKipcbiAqIE1ha2VzIGEgdGFibGUgc29ydGFibGUgYnkgY29sdW1ucy5cbiAqIFRoaXMgZm9yY2VzIGEgcGFnZSByZWxvYWQgd2l0aCBtb3JlIHF1ZXJ5IHBhcmFtZXRlcnMuXG4gKi9cbmNsYXNzIFRhYmxlU29ydGluZyB7XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSB0YWJsZVxuICAgKi9cbiAgY29uc3RydWN0b3IodGFibGUpIHtcbiAgICB0aGlzLnNlbGVjdG9yID0gJy5wcy1zb3J0YWJsZS1jb2x1bW4nO1xuICAgIHRoaXMuY29sdW1ucyA9ICQodGFibGUpLmZpbmQodGhpcy5zZWxlY3Rvcik7XG4gIH1cblxuICAvKipcbiAgICogQXR0YWNoZXMgdGhlIGxpc3RlbmVyc1xuICAgKi9cbiAgYXR0YWNoKCkge1xuICAgIHRoaXMuY29sdW1ucy5vbignY2xpY2snLCAoZSkgPT4ge1xuICAgICAgY29uc3QgJGNvbHVtbiA9ICQoZS5kZWxlZ2F0ZVRhcmdldCk7XG4gICAgICB0aGlzLl9zb3J0QnlDb2x1bW4oJGNvbHVtbiwgdGhpcy5fZ2V0VG9nZ2xlZFNvcnREaXJlY3Rpb24oJGNvbHVtbikpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFNvcnQgdXNpbmcgYSBjb2x1bW4gbmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gY29sdW1uTmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIFwiYXNjXCIgb3IgXCJkZXNjXCJcbiAgICovXG4gIHNvcnRCeShjb2x1bW5OYW1lLCBkaXJlY3Rpb24pIHtcbiAgICBjb25zdCAkY29sdW1uID0gdGhpcy5jb2x1bW5zLmlzKGBbZGF0YS1zb3J0LWNvbC1uYW1lPVwiJHtjb2x1bW5OYW1lfVwiXWApO1xuICAgIGlmICghJGNvbHVtbikge1xuICAgICAgdGhyb3cgbmV3IEVycm9yKGBDYW5ub3Qgc29ydCBieSBcIiR7Y29sdW1uTmFtZX1cIjogaW52YWxpZCBjb2x1bW5gKTtcbiAgICB9XG5cbiAgICB0aGlzLl9zb3J0QnlDb2x1bW4oJGNvbHVtbiwgZGlyZWN0aW9uKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTb3J0IHVzaW5nIGEgY29sdW1uIGVsZW1lbnRcbiAgICogQHBhcmFtIHtqUXVlcnl9IGNvbHVtblxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIFwiYXNjXCIgb3IgXCJkZXNjXCJcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zb3J0QnlDb2x1bW4oY29sdW1uLCBkaXJlY3Rpb24pIHtcbiAgICB3aW5kb3cubG9jYXRpb24gPSB0aGlzLl9nZXRVcmwoY29sdW1uLmRhdGEoJ3NvcnRDb2xOYW1lJyksIChkaXJlY3Rpb24gPT09ICdkZXNjJykgPyAnZGVzYycgOiAnYXNjJywgY29sdW1uLmRhdGEoJ3NvcnRQcmVmaXgnKSk7XG4gIH1cblxuICAvKipcbiAgICogUmV0dXJucyB0aGUgaW52ZXJ0ZWQgZGlyZWN0aW9uIHRvIHNvcnQgYWNjb3JkaW5nIHRvIHRoZSBjb2x1bW4ncyBjdXJyZW50IG9uZVxuICAgKiBAcGFyYW0ge2pRdWVyeX0gY29sdW1uXG4gICAqIEByZXR1cm4ge3N0cmluZ31cbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRUb2dnbGVkU29ydERpcmVjdGlvbihjb2x1bW4pIHtcbiAgICByZXR1cm4gY29sdW1uLmRhdGEoJ3NvcnREaXJlY3Rpb24nKSA9PT0gJ2FzYycgPyAnZGVzYycgOiAnYXNjJztcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXR1cm5zIHRoZSB1cmwgZm9yIHRoZSBzb3J0ZWQgdGFibGVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGNvbE5hbWVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGRpcmVjdGlvblxuICAgKiBAcGFyYW0ge3N0cmluZ30gcHJlZml4XG4gICAqIEByZXR1cm4ge3N0cmluZ31cbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRVcmwoY29sTmFtZSwgZGlyZWN0aW9uLCBwcmVmaXgpIHtcbiAgICBjb25zdCB1cmwgPSBuZXcgVVJMKHdpbmRvdy5sb2NhdGlvbi5ocmVmKTtcbiAgICBjb25zdCBwYXJhbXMgPSB1cmwuc2VhcmNoUGFyYW1zO1xuXG4gICAgaWYgKHByZWZpeCkge1xuICAgICAgcGFyYW1zLnNldChwcmVmaXgrJ1tvcmRlckJ5XScsIGNvbE5hbWUpO1xuICAgICAgcGFyYW1zLnNldChwcmVmaXgrJ1tzb3J0T3JkZXJdJywgZGlyZWN0aW9uKTtcbiAgICB9IGVsc2Uge1xuICAgICAgcGFyYW1zLnNldCgnb3JkZXJCeScsIGNvbE5hbWUpO1xuICAgICAgcGFyYW1zLnNldCgnc29ydE9yZGVyJywgZGlyZWN0aW9uKTtcbiAgICB9XG5cbiAgICByZXR1cm4gdXJsLnRvU3RyaW5nKCk7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgVGFibGVTb3J0aW5nO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL3V0aWxzL3RhYmxlLXNvcnRpbmcuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG4vKipcbiAqIFNlbmQgYSBQb3N0IFJlcXVlc3QgdG8gcmVzZXQgc2VhcmNoIEFjdGlvbi5cbiAqL1xuXG5jb25zdCAkID0gZ2xvYmFsLiQ7XG5cbmNvbnN0IGluaXQgPSBmdW5jdGlvbiByZXNldFNlYXJjaCh1cmwsIHJlZGlyZWN0VXJsKSB7XG4gICAgJC5wb3N0KHVybCkudGhlbigoKSA9PiB3aW5kb3cubG9jYXRpb24uYXNzaWduKHJlZGlyZWN0VXJsKSk7XG59O1xuXG5leHBvcnQgZGVmYXVsdCBpbml0O1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL3V0aWxzL3Jlc2V0X3NlYXJjaC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiQ29sdW1uIHRvZ2dsaW5nXCIgZmVhdHVyZVxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbiB7XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBjb25zdCAkdGFibGUgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJ3RhYmxlLnRhYmxlJyk7XG4gICAgJHRhYmxlLmZpbmQoJy5wcy10b2dnbGFibGUtcm93Jykub24oJ2NsaWNrJywgKGUpID0+IHtcbiAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgIHRoaXMuX3RvZ2dsZVZhbHVlKCQoZS5kZWxlZ2F0ZVRhcmdldCkpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSByb3dcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGVWYWx1ZShyb3cpIHtcbiAgICBjb25zdCB0b2dnbGVVcmwgPSByb3cuZGF0YSgndG9nZ2xlVXJsJyk7XG5cbiAgICB0aGlzLl9zdWJtaXRBc0Zvcm0odG9nZ2xlVXJsKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTdWJtaXRzIHJlcXVlc3QgdXJsIGFzIGZvcm1cbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IHRvZ2dsZVVybFxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3N1Ym1pdEFzRm9ybSh0b2dnbGVVcmwpIHtcbiAgICBjb25zdCAkZm9ybSA9ICQoJzxmb3JtPicsIHtcbiAgICAgIGFjdGlvbjogdG9nZ2xlVXJsLFxuICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgfSkuYXBwZW5kVG8oJ2JvZHknKTtcblxuICAgICRmb3JtLnN1Ym1pdCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi10b2dnbGluZy1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgaXMgcmVzcG9uc2libGUgZm9yIGhhbmRsaW5nIEdyaWQgZXZlbnRzXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEdyaWQge1xuICAvKipcbiAgICogR3JpZCBpZFxuICAgKlxuICAgKiBAcGFyYW0ge3N0cmluZ30gaWRcbiAgICovXG4gIGNvbnN0cnVjdG9yKGlkKSB7XG4gICAgdGhpcy5pZCA9IGlkO1xuICAgIHRoaXMuJGNvbnRhaW5lciA9ICQoJyMnICsgdGhpcy5pZCArICdfZ3JpZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGlkXG4gICAqXG4gICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAqL1xuICBnZXRJZCgpIHtcbiAgICByZXR1cm4gdGhpcy5pZDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZ3JpZCBjb250YWluZXJcbiAgICpcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICovXG4gIGdldENvbnRhaW5lcigpIHtcbiAgICByZXR1cm4gdGhpcy4kY29udGFpbmVyO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGhlYWRlciBjb250YWluZXJcbiAgICpcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICovXG4gIGdldEhlYWRlckNvbnRhaW5lcigpIHtcbiAgICByZXR1cm4gdGhpcy4kY29udGFpbmVyLmNsb3Nlc3QoJy5qcy1ncmlkLXBhbmVsJykuZmluZCgnLmpzLWdyaWQtaGVhZGVyJyk7XG4gIH1cblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWQgd2l0aCBleHRlcm5hbCBleHRlbnNpb25zXG4gICAqXG4gICAqIEBwYXJhbSB7b2JqZWN0fSBleHRlbnNpb25cbiAgICovXG4gIGFkZEV4dGVuc2lvbihleHRlbnNpb24pIHtcbiAgICBleHRlbnNpb24uZXh0ZW5kKHRoaXMpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZ3JpZC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBIYW5kbGVzIFVJIGludGVyYWN0aW9ucyBvZiBjaG9pY2UgdHJlZVxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDaG9pY2VUcmVlIHtcbiAgLyoqXG4gICAqIEBwYXJhbSB7U3RyaW5nfSB0cmVlU2VsZWN0b3JcbiAgICovXG4gIGNvbnN0cnVjdG9yKHRyZWVTZWxlY3Rvcikge1xuICAgIHRoaXMuJGNvbnRhaW5lciA9ICQodHJlZVNlbGVjdG9yKTtcblxuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2xpY2snLCAnLmpzLWlucHV0LXdyYXBwZXInLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRpbnB1dFdyYXBwZXIgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICB0aGlzLl90b2dnbGVDaGlsZFRyZWUoJGlucHV0V3JhcHBlcik7XG4gICAgfSk7XG5cbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgJy5qcy10b2dnbGUtY2hvaWNlLXRyZWUtYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkYWN0aW9uID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcblxuICAgICAgdGhpcy5fdG9nZ2xlVHJlZSgkYWN0aW9uKTtcbiAgICB9KTtcblxuICAgIHJldHVybiB7XG4gICAgICBlbmFibGVBdXRvQ2hlY2tDaGlsZHJlbjogKCkgPT4gdGhpcy5lbmFibGVBdXRvQ2hlY2tDaGlsZHJlbigpLFxuICAgICAgZW5hYmxlQWxsSW5wdXRzOiAoKSA9PiB0aGlzLmVuYWJsZUFsbElucHV0cygpLFxuICAgICAgZGlzYWJsZUFsbElucHV0czogKCkgPT4gdGhpcy5kaXNhYmxlQWxsSW5wdXRzKCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbmFibGUgYXV0b21hdGljIGNoZWNrL3VuY2hlY2sgb2YgY2xpY2tlZCBpdGVtJ3MgY2hpbGRyZW4uXG4gICAqL1xuICBlbmFibGVBdXRvQ2hlY2tDaGlsZHJlbigpIHtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NoYW5nZScsICdpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0nLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRjbGlja2VkQ2hlY2tib3ggPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgY29uc3QgJGl0ZW1XaXRoQ2hpbGRyZW4gPSAkY2xpY2tlZENoZWNrYm94LmNsb3Nlc3QoJ2xpJyk7XG5cbiAgICAgICRpdGVtV2l0aENoaWxkcmVuXG4gICAgICAgIC5maW5kKCd1bCBpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0nKVxuICAgICAgICAucHJvcCgnY2hlY2tlZCcsICRjbGlja2VkQ2hlY2tib3guaXMoJzpjaGVja2VkJykpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEVuYWJsZSBhbGwgaW5wdXRzIGluIHRoZSBjaG9pY2UgdHJlZS5cbiAgICovXG4gIGVuYWJsZUFsbElucHV0cygpIHtcbiAgICB0aGlzLiRjb250YWluZXIuZmluZCgnaW5wdXQnKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIERpc2FibGUgYWxsIGlucHV0cyBpbiB0aGUgY2hvaWNlIHRyZWUuXG4gICAqL1xuICBkaXNhYmxlQWxsSW5wdXRzKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5maW5kKCdpbnB1dCcpLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG4gIH1cblxuICAvKipcbiAgICogQ29sbGFwc2Ugb3IgZXhwYW5kIHN1Yi10cmVlIGZvciBzaW5nbGUgcGFyZW50XG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkaW5wdXRXcmFwcGVyXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdG9nZ2xlQ2hpbGRUcmVlKCRpbnB1dFdyYXBwZXIpIHtcbiAgICBjb25zdCAkcGFyZW50V3JhcHBlciA9ICRpbnB1dFdyYXBwZXIuY2xvc2VzdCgnbGknKTtcblxuICAgIGlmICgkcGFyZW50V3JhcHBlci5oYXNDbGFzcygnZXhwYW5kZWQnKSkge1xuICAgICAgJHBhcmVudFdyYXBwZXJcbiAgICAgICAgLnJlbW92ZUNsYXNzKCdleHBhbmRlZCcpXG4gICAgICAgIC5hZGRDbGFzcygnY29sbGFwc2VkJyk7XG5cbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBpZiAoJHBhcmVudFdyYXBwZXIuaGFzQ2xhc3MoJ2NvbGxhcHNlZCcpKSB7XG4gICAgICAkcGFyZW50V3JhcHBlclxuICAgICAgICAucmVtb3ZlQ2xhc3MoJ2NvbGxhcHNlZCcpXG4gICAgICAgIC5hZGRDbGFzcygnZXhwYW5kZWQnKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogQ29sbGFwc2Ugb3IgZXhwYW5kIHdob2xlIHRyZWVcbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRhY3Rpb25cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGVUcmVlKCRhY3Rpb24pIHtcbiAgICBjb25zdCAkcGFyZW50Q29udGFpbmVyID0gJGFjdGlvbi5jbG9zZXN0KCcuanMtY2hvaWNlLXRyZWUtY29udGFpbmVyJyk7XG4gICAgY29uc3QgYWN0aW9uID0gJGFjdGlvbi5kYXRhKCdhY3Rpb24nKTtcblxuICAgIC8vIHRvZ2dsZSBhY3Rpb24gY29uZmlndXJhdGlvblxuICAgIGNvbnN0IGNvbmZpZyA9IHtcbiAgICAgIGFkZENsYXNzOiB7XG4gICAgICAgIGV4cGFuZDogJ2V4cGFuZGVkJyxcbiAgICAgICAgY29sbGFwc2U6ICdjb2xsYXBzZWQnLFxuICAgICAgfSxcbiAgICAgIHJlbW92ZUNsYXNzOiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlZCcsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kZWQnLFxuICAgICAgfSxcbiAgICAgIG5leHRBY3Rpb246IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2UnLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZCcsXG4gICAgICB9LFxuICAgICAgdGV4dDoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZWQtdGV4dCcsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kZWQtdGV4dCcsXG4gICAgICB9LFxuICAgICAgaWNvbjoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZWQtaWNvbicsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kZWQtaWNvbicsXG4gICAgICB9XG4gICAgfTtcblxuICAgICRwYXJlbnRDb250YWluZXIuZmluZCgnbGknKS5lYWNoKChpbmRleCwgaXRlbSkgPT4ge1xuICAgICAgY29uc3QgJGl0ZW0gPSAkKGl0ZW0pO1xuXG4gICAgICBpZiAoJGl0ZW0uaGFzQ2xhc3MoY29uZmlnLnJlbW92ZUNsYXNzW2FjdGlvbl0pKSB7XG4gICAgICAgICAgJGl0ZW0ucmVtb3ZlQ2xhc3MoY29uZmlnLnJlbW92ZUNsYXNzW2FjdGlvbl0pXG4gICAgICAgICAgICAuYWRkQ2xhc3MoY29uZmlnLmFkZENsYXNzW2FjdGlvbl0pO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgJGFjdGlvbi5kYXRhKCdhY3Rpb24nLCBjb25maWcubmV4dEFjdGlvblthY3Rpb25dKTtcbiAgICAkYWN0aW9uLmZpbmQoJy5tYXRlcmlhbC1pY29ucycpLnRleHQoJGFjdGlvbi5kYXRhKGNvbmZpZy5pY29uW2FjdGlvbl0pKTtcbiAgICAkYWN0aW9uLmZpbmQoJy5qcy10b2dnbGUtdGV4dCcpLnRleHQoJGFjdGlvbi5kYXRhKGNvbmZpZy50ZXh0W2FjdGlvbl0pKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9mb3JtL2Nob2ljZS10cmVlLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBHZW5lcmF0ZXMgcmFuZG9tIHZhbHVlcyBmb3IgaW5wdXRzLlxuICpcbiAqIFVzYWdlOlxuICpcbiAqIFRoZXJlIHNob3VsZCBiZSBhIGJ1dHRvbiBpbiBIVE1MIHdpdGggMiByZXF1aXJlZCBkYXRhLSogcHJvcGVydGllczpcbiAqICAgIDEuIGRhdGEtdGFyZ2V0LWlucHV0LWlkIC0gaW5wdXQgaWQgZm9yIHdoaWNoIHZhbHVlIHNob3VsZCBiZSBnZW5lcmF0ZWRcbiAqICAgIDIuIGRhdGEtZ2VuZXJhdGVkLXZhbHVlLXNpemUgLVxuICpcbiAqIEV4YW1wbGUgYnV0dG9uOiA8YnV0dG9uIGNsYXNzPVwianMtZ2VuZXJhdG9yLWJ0blwiXG4gKiAgICAgICAgICAgICAgICAgICAgICAgICBkYXRhLXRhcmdldC1pbnB1dC1pZD1cIm15LWlucHV0LWlkXCJcbiAqICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGEtZ2VuZXJhdGVkLXZhbHVlLWxlbmd0aD1cIjE2XCJcbiAqICAgICAgICAgICAgICAgICA+XG4gKiAgICAgICAgICAgICAgICAgICAgIEdlbmVyYXRlIVxuICogICAgICAgICAgICAgICAgIDwvYnV0dG9uPlxuICpcbiAqIEluIEphdmFTY3JpcHQgeW91IGhhdmUgdG8gZW5hYmxlIHRoaXMgZnVuY3Rpb25hbGl0eSB1c2luZyBHZW5lcmF0YWJsZUlucHV0IGNvbXBvbmVudCBsaWtlIHNvOlxuICpcbiAqIGNvbnN0IGdlbmVyYXRlYWJsZUlucHV0ID0gbmV3IEdlbmVyYXRhYmxlSW5wdXQoKTtcbiAqIGdlbmVyYXRlYWJsZUlucHV0LmF0dGFjaE9uKCcuanMtZ2VuZXJhdG9yLWJ0bicpOyAvLyBldmVyeSB0aW1lIG91ciBidXR0b24gaXMgY2xpY2tlZFxuICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIGl0IHdpbGwgZ2VuZXJhdGUgcmFuZG9tIHZhbHVlIG9mIDE2IGNoYXJhY3RlcnNcbiAqICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBmb3IgaW5wdXQgd2l0aCBpZCBvZiBcIm15LWlucHV0LWlkXCJcbiAqXG4gKiBZb3UgY2FuIGF0dGFjaCBhcyBtYW55IGRpZmZlcmVudCBidXR0b25zIGFzIHlvdSBsaWtlIHVzaW5nIFwiYXR0YWNoT24oKVwiIGZ1bmN0aW9uXG4gKiBhcyBsb25nIGFzIDIgcmVxdWlyZWQgZGF0YS0qIGF0dHJpYnV0ZXMgYXJlIHByZXNlbnQgYXQgZWFjaCBidXR0b24uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEdlbmVyYXRhYmxlSW5wdXQge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICByZXR1cm4ge1xuICAgICAgJ2F0dGFjaE9uJzogKGJ0blNlbGVjdG9yKSA9PiB0aGlzLl9hdHRhY2hPbihidG5TZWxlY3RvciksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBdHRhY2hlcyBldmVudCBsaXN0ZW5lciBvbiBidXR0b24gdGhhbiBjYW4gZ2VuZXJhdGUgdmFsdWVcbiAgICpcbiAgICogQHBhcmFtIHtTdHJpbmd9IGdlbmVyYXRvckJ0blNlbGVjdG9yXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfYXR0YWNoT24oZ2VuZXJhdG9yQnRuU2VsZWN0b3IpIHtcbiAgICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKGdlbmVyYXRvckJ0blNlbGVjdG9yKS5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgYXR0cmlidXRlcyA9IGV2ZW50LmN1cnJlbnRUYXJnZXQuYXR0cmlidXRlcztcblxuICAgICAgY29uc3QgdGFyZ2V0SW5wdXRJZCA9IGF0dHJpYnV0ZXMuZ2V0TmFtZWRJdGVtKCdkYXRhLXRhcmdldC1pbnB1dC1pZCcpLnZhbHVlO1xuICAgICAgY29uc3QgZ2VuZXJhdGVkVmFsdWVMZW5ndGggPSBwYXJzZUludChhdHRyaWJ1dGVzLmdldE5hbWVkSXRlbSgnZGF0YS1nZW5lcmF0ZWQtdmFsdWUtbGVuZ3RoJykudmFsdWUpO1xuXG4gICAgICBjb25zdCB0YXJnZXRJbnB1dCA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyMnICsgdGFyZ2V0SW5wdXRJZCk7XG4gICAgICB0YXJnZXRJbnB1dC52YWx1ZSA9IHRoaXMuX2dlbmVyYXRlVmFsdWUoZ2VuZXJhdGVkVmFsdWVMZW5ndGgpXG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogR2VuZXJhdGVzIHJhbmRvbSB2YWx1ZSBmb3IgaW5wdXRcbiAgICpcbiAgICogQHBhcmFtIHtOdW1iZXJ9IGxlbmd0aFxuICAgKlxuICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dlbmVyYXRlVmFsdWUobGVuZ3RoKSB7XG4gICAgY29uc3QgY2hhcnMgPSAnMTIzNDU2Nzg5QUJDREVGR0hJSktMTU5QUVJTVFVWV1hZWic7XG4gICAgbGV0IGdlbmVyYXRlZFZhbHVlID0gJyc7XG5cbiAgICBmb3IgKGxldCBpID0gMTsgaSA8PSBsZW5ndGg7ICsraSkge1xuICAgICAgZ2VuZXJhdGVkVmFsdWUgKz0gY2hhcnMuY2hhckF0KE1hdGguZmxvb3IoTWF0aC5yYW5kb20oKSAqIGNoYXJzLmxlbmd0aCkpO1xuICAgIH1cblxuICAgIHJldHVybiBnZW5lcmF0ZWRWYWx1ZTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9nZW5lcmF0YWJsZS1pbnB1dC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBJbiBBZGQvRWRpdCBwYWdlIG9mIFdlYnNlcnZpY2Uga2V5IHRoZXJlIGlzIHBlcm1pc3Npb25zIHRhYmxlIGlucHV0IChwZXJtaXNzb25zIGFzIGNvbHVtbnMgLyByZXNvdXJjZXMgYXMgcm93cykuXG4gKiBUaGVyZSBpcyBcIkFsbFwiIGNvbHVtbiBhbmQgb25jZSByZXNvdXJjZSBpcyBjaGVja2VkIHVuZGVyIHRoaXMgY29sdW1uXG4gKiBldmVyeSBvdGhlciBwZXJtaXNzaW9uIGNvbHVtbiBzaG91bGQgYmUgYXV0by1zZWxlY3RlZCBmb3IgdGhhdCByZXNvdXJjZS5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUGVybWlzc2lvbnNSb3dTZWxlY3RvciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIC8vIHdoZW4gY2hlY2tib3ggaW4gXCJBbGxcIiBjb2x1bW4gaXMgY2hlY2tlZFxuICAgICQoJ2lucHV0W2lkXj1cIndlYnNlcnZpY2Vfa2V5X3Blcm1pc3Npb25zX2FsbFwiXScpLm9uKCdjaGFuZ2UnLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRjaGVja2VkQm94ID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcblxuICAgICAgY29uc3QgaXNDaGVja2VkID0gJGNoZWNrZWRCb3guaXMoJzpjaGVja2VkJyk7XG5cbiAgICAgIC8vIGZvciBlYWNoIGlucHV0IGluIHNhbWUgcm93IHdlIG5lZWQgdG8gdG9nZ2xlIGl0cyB2YWx1ZVxuICAgICAgJGNoZWNrZWRCb3guY2xvc2VzdCgndHInKS5maW5kKGBpbnB1dDpub3QoaW5wdXRbaWQ9XCIkeyRjaGVja2VkQm94LmF0dHIoJ2lkJyl9XCJdKWApLmVhY2goKGksIGlucHV0KSA9PiB7XG4gICAgICAgICQoaW5wdXQpLnByb3AoJ2NoZWNrZWQnLCBpc0NoZWNrZWQpO1xuICAgICAgfSk7XG4gICAgfSk7XG5cbiAgICByZXR1cm4ge307XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL3dlYnNlcnZpY2UvcGVybWlzc2lvbnMtcm93LXNlbGVjdG9yLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IHJlc2V0U2VhcmNoIGZyb20gJy4uLy4uLy4uL2FwcC91dGlscy9yZXNldF9zZWFyY2gnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIGZpbHRlcnMgcmVzZXR0aW5nXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEZpbHRlcnNSZXNldEV4dGVuc2lvbiB7XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtcmVzZXQtc2VhcmNoJywgKGV2ZW50KSA9PiB7XG4gICAgICByZXNldFNlYXJjaCgkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ3VybCcpLCAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ3JlZGlyZWN0JykpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IEdyaWQgZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZ3JpZC9ncmlkXCI7XG5pbXBvcnQgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uXCI7XG5pbXBvcnQgUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbiBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9yZWxvYWQtbGlzdC1leHRlbnNpb25cIjtcbmltcG9ydCBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24gZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZXhwb3J0LXRvLXNxbC1tYW5hZ2VyLWV4dGVuc2lvblwiO1xuaW1wb3J0IEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbiBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9idWxrLWFjdGlvbi1jaGVja2JveC1leHRlbnNpb25cIjtcbmltcG9ydCBTdWJtaXRCdWxrQWN0aW9uRXh0ZW5zaW9uIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1idWxrLWFjdGlvbi1leHRlbnNpb25cIjtcbmltcG9ydCBTb3J0aW5nRXh0ZW5zaW9uIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uXCI7XG5pbXBvcnQgU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9yb3cvc3VibWl0LXJvdy1hY3Rpb24tZXh0ZW5zaW9uXCI7XG5pbXBvcnQgQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24gZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vY29sdW1uLXRvZ2dsaW5nLWV4dGVuc2lvblwiO1xuaW1wb3J0IENob2ljZVRyZWUgZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZm9ybS9jaG9pY2UtdHJlZVwiO1xuaW1wb3J0IEdlbmVyYXRhYmxlSW5wdXQgZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZ2VuZXJhdGFibGUtaW5wdXRcIjtcbmltcG9ydCBNdWx0aXBsZUNob2ljZVRhYmxlIGZyb20gXCIuLi8uLi9jb21wb25lbnRzL211bHRpcGxlLWNob2ljZS10YWJsZVwiO1xuaW1wb3J0IFBlcm1pc3Npb25zUm93U2VsZWN0b3IgZnJvbSBcIi4vcGVybWlzc2lvbnMtcm93LXNlbGVjdG9yXCI7XG5pbXBvcnQgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2xpbmstcm93LWFjdGlvbi1leHRlbnNpb24nO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICBjb25zdCB3ZWJzZXJ2aWNlR3JpZCA9IG5ldyBHcmlkKCd3ZWJzZXJ2aWNlX2tleScpO1xuXG4gIHdlYnNlcnZpY2VHcmlkLmFkZEV4dGVuc2lvbihuZXcgUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbigpKTtcbiAgd2Vic2VydmljZUdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24oKSk7XG4gIHdlYnNlcnZpY2VHcmlkLmFkZEV4dGVuc2lvbihuZXcgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uKCkpO1xuICB3ZWJzZXJ2aWNlR3JpZC5hZGRFeHRlbnNpb24obmV3IENvbHVtblRvZ2dsaW5nRXh0ZW5zaW9uKCkpO1xuICB3ZWJzZXJ2aWNlR3JpZC5hZGRFeHRlbnNpb24obmV3IFNvcnRpbmdFeHRlbnNpb24oKSk7XG4gIHdlYnNlcnZpY2VHcmlkLmFkZEV4dGVuc2lvbihuZXcgU3VibWl0QnVsa0FjdGlvbkV4dGVuc2lvbigpKTtcbiAgd2Vic2VydmljZUdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24oKSk7XG4gIHdlYnNlcnZpY2VHcmlkLmFkZEV4dGVuc2lvbihuZXcgQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uKCkpO1xuICB3ZWJzZXJ2aWNlR3JpZC5hZGRFeHRlbnNpb24obmV3IExpbmtSb3dBY3Rpb25FeHRlbnNpb24oKSk7XG5cbiAgLy8gbmVlZGVkIGZvciBzaG9wIGFzc29jaWF0aW9uIGlucHV0IGluIGZvcm1cbiAgbmV3IENob2ljZVRyZWUoJyN3ZWJzZXJ2aWNlX2tleV9zaG9wX2Fzc29jaWF0aW9uJykuZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4oKTtcblxuICAvLyBuZWVkZWQgZm9yIHBlcm1pc3Npb25zIGlucHV0IGluIGZvcm1cbiAgbmV3IE11bHRpcGxlQ2hvaWNlVGFibGUoKTtcblxuICAvLyBuZWVkZWQgZm9yIGtleSBpbnB1dCBpbiBmb3JtXG4gIGNvbnN0IGdlbmVyYXRhYmxlSW5wdXQgPSBuZXcgR2VuZXJhdGFibGVJbnB1dCgpO1xuICBnZW5lcmF0YWJsZUlucHV0LmF0dGFjaE9uKCcuanMtZ2VuZXJhdG9yLWJ0bicpO1xuXG4gIG5ldyBQZXJtaXNzaW9uc1Jvd1NlbGVjdG9yKCk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL3dlYnNlcnZpY2UvaW5kZXguanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgVGFibGVTb3J0aW5nIGZyb20gJy4uLy4uLy4uL2FwcC91dGlscy90YWJsZS1zb3J0aW5nJztcblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiTGlzdCByZWxvYWRcIiBhY3Rpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU29ydGluZ0V4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgY29uc3QgJHNvcnRhYmxlVGFibGUgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJ3RhYmxlLnRhYmxlJyk7XG5cbiAgICBuZXcgVGFibGVTb3J0aW5nKCRzb3J0YWJsZVRhYmxlKS5hdHRhY2goKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zb3J0aW5nLWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbi8qKlxuICogQ2xhc3MgUmVsb2FkTGlzdEV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBcIkxpc3QgcmVsb2FkXCIgYWN0aW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0SGVhZGVyQ29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1jb21tb25fcmVmcmVzaF9saXN0LWdyaWQtYWN0aW9uJywgKCkgPT4ge1xuICAgICAgbG9jYXRpb24ucmVsb2FkKCk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcmVsb2FkLWxpc3QtZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIE11bHRpcGxlQ2hvaWNlVGFibGUgaXMgcmVzcG9uc2libGUgZm9yIG1hbmFnaW5nIGNvbW1vbiBhY3Rpb25zIGluIG11bHRpcGxlIGNob2ljZSB0YWJsZSBmb3JtIHR5cGVcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgTXVsdGlwbGVDaG9pY2VUYWJsZSB7XG4gIC8qKlxuICAgKiBJbml0IGNvbnN0cnVjdG9yXG4gICAqL1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnLmpzLW11bHRpcGxlLWNob2ljZS10YWJsZS1zZWxlY3QtY29sdW1uJywgKGUpID0+IHRoaXMuaGFuZGxlU2VsZWN0Q29sdW1uKGUpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDaGVjay91bmNoZWNrIGFsbCBib3hlcyBpbiBjb2x1bW5cbiAgICpcbiAgICogQHBhcmFtIHtFdmVudH0gZXZlbnRcbiAgICovXG4gIGhhbmRsZVNlbGVjdENvbHVtbihldmVudCkge1xuICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICBjb25zdCAkc2VsZWN0Q29sdW1uQnRuID0gJChldmVudC50YXJnZXQpO1xuICAgIGNvbnN0IGNoZWNrZWQgPSAkc2VsZWN0Q29sdW1uQnRuLmRhdGEoJ2NvbHVtbi1jaGVja2VkJyk7XG4gICAgJHNlbGVjdENvbHVtbkJ0bi5kYXRhKCdjb2x1bW4tY2hlY2tlZCcsICFjaGVja2VkKTtcblxuICAgIGNvbnN0ICR0YWJsZSA9ICRzZWxlY3RDb2x1bW5CdG4uY2xvc2VzdCgndGFibGUnKTtcblxuICAgICR0YWJsZVxuICAgICAgLmZpbmQoJ3Rib2R5IHRyIHRkOm50aC1jaGlsZCgnICsgJHNlbGVjdENvbHVtbkJ0bi5kYXRhKCdjb2x1bW4tbnVtJykgKyAnKSBpbnB1dFt0eXBlPWNoZWNrYm94XScpXG4gICAgICAucHJvcCgnY2hlY2tlZCcsICFjaGVja2VkKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9tdWx0aXBsZS1jaG9pY2UtdGFibGUuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgQnVsa0FjdGlvblNlbGVjdENoZWNrYm94RXh0ZW5zaW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZCB3aXRoIGJ1bGsgYWN0aW9uIGNoZWNrYm94ZXMgaGFuZGxpbmcgZnVuY3Rpb25hbGl0eVxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgdGhpcy5faGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0KGdyaWQpO1xuICAgIHRoaXMuX2hhbmRsZUJ1bGtBY3Rpb25TZWxlY3RBbGxDaGVja2JveChncmlkKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIFwiU2VsZWN0IGFsbFwiIGJ1dHRvbiBpbiB0aGUgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oYW5kbGVCdWxrQWN0aW9uU2VsZWN0QWxsQ2hlY2tib3goZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NoYW5nZScsICcuanMtYnVsay1hY3Rpb24tc2VsZWN0LWFsbCcsIChlKSA9PiB7XG4gICAgICBjb25zdCAkY2hlY2tib3ggPSAkKGUuY3VycmVudFRhcmdldCk7XG5cbiAgICAgIGNvbnN0IGlzQ2hlY2tlZCA9ICRjaGVja2JveC5pcygnOmNoZWNrZWQnKTtcbiAgICAgIGlmIChpc0NoZWNrZWQpIHtcbiAgICAgICAgdGhpcy5fZW5hYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB0aGlzLl9kaXNhYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9XG5cbiAgICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9uLWNoZWNrYm94JykucHJvcCgnY2hlY2tlZCcsIGlzQ2hlY2tlZCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlcyBlYWNoIGJ1bGsgYWN0aW9uIGNoZWNrYm94IHNlbGVjdCBpbiB0aGUgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oYW5kbGVCdWxrQWN0aW9uQ2hlY2tib3hTZWxlY3QoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NoYW5nZScsICcuanMtYnVsay1hY3Rpb24tY2hlY2tib3gnLCAoKSA9PiB7XG4gICAgICBjb25zdCBjaGVja2VkUm93c0NvdW50ID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb24tY2hlY2tib3g6Y2hlY2tlZCcpLmxlbmd0aDtcblxuICAgICAgaWYgKGNoZWNrZWRSb3dzQ291bnQgPiAwKSB7XG4gICAgICAgIHRoaXMuX2VuYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGhpcy5fZGlzYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEVuYWJsZSBidWxrIGFjdGlvbnMgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2VuYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbnMtYnRuJykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gIH1cblxuICAvKipcbiAgICogRGlzYWJsZSBidWxrIGFjdGlvbnMgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2Rpc2FibGVCdWxrQWN0aW9uc0J0bihncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb25zLWJ0bicpLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYnVsay1hY3Rpb24tY2hlY2tib3gtZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBleHBvcnRpbmcgcXVlcnkgdG8gU1FMIE1hbmFnZXJcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX3Nob3dfcXVlcnktZ3JpZC1hY3Rpb24nLCAoKSA9PiB0aGlzLl9vblNob3dTcWxRdWVyeUNsaWNrKGdyaWQpKTtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX2V4cG9ydF9zcWxfbWFuYWdlci1ncmlkLWFjdGlvbicsICgpID0+IHRoaXMuX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrKGdyaWQpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbnZva2VkIHdoZW4gY2xpY2tpbmcgb24gdGhlIFwic2hvdyBzcWwgcXVlcnlcIiB0b29sYmFyIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vblNob3dTcWxRdWVyeUNsaWNrKGdyaWQpIHtcbiAgICBjb25zdCAkc3FsTWFuYWdlckZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfY29tbW9uX3Nob3dfcXVlcnlfbW9kYWxfZm9ybScpO1xuICAgIHRoaXMuX2ZpbGxFeHBvcnRGb3JtKCRzcWxNYW5hZ2VyRm9ybSwgZ3JpZCk7XG5cbiAgICBjb25zdCAkbW9kYWwgPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfZ3JpZF9jb21tb25fc2hvd19xdWVyeV9tb2RhbCcpO1xuICAgICRtb2RhbC5tb2RhbCgnc2hvdycpO1xuXG4gICAgJG1vZGFsLm9uKCdjbGljaycsICcuYnRuLXNxbC1zdWJtaXQnLCAoKSA9PiAkc3FsTWFuYWdlckZvcm0uc3VibWl0KCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEludm9rZWQgd2hlbiBjbGlja2luZyBvbiB0aGUgXCJleHBvcnQgdG8gdGhlIHNxbCBxdWVyeVwiIHRvb2xiYXIgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrKGdyaWQpIHtcbiAgICBjb25zdCAkc3FsTWFuYWdlckZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfY29tbW9uX3Nob3dfcXVlcnlfbW9kYWxfZm9ybScpO1xuXG4gICAgdGhpcy5fZmlsbEV4cG9ydEZvcm0oJHNxbE1hbmFnZXJGb3JtLCBncmlkKTtcblxuICAgICRzcWxNYW5hZ2VyRm9ybS5zdWJtaXQoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBGaWxsIGV4cG9ydCBmb3JtIHdpdGggU1FMIGFuZCBpdCdzIG5hbWVcbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRzcWxNYW5hZ2VyRm9ybVxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9maWxsRXhwb3J0Rm9ybSgkc3FsTWFuYWdlckZvcm0sIGdyaWQpIHtcbiAgICBjb25zdCBxdWVyeSA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWdyaWQtdGFibGUnKS5kYXRhKCdxdWVyeScpO1xuXG4gICAgJHNxbE1hbmFnZXJGb3JtLmZpbmQoJ3RleHRhcmVhW25hbWU9XCJzcWxcIl0nKS52YWwocXVlcnkpO1xuICAgICRzcWxNYW5hZ2VyRm9ybS5maW5kKCdpbnB1dFtuYW1lPVwibmFtZVwiXScpLnZhbCh0aGlzLl9nZXROYW1lRnJvbUJyZWFkY3J1bWIoKSk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGV4cG9ydCBuYW1lIGZyb20gcGFnZSdzIGJyZWFkY3J1bWJcbiAgICpcbiAgICogQHJldHVybiB7U3RyaW5nfVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldE5hbWVGcm9tQnJlYWRjcnVtYigpIHtcbiAgICBjb25zdCAkYnJlYWRjcnVtYnMgPSAkKCcuaGVhZGVyLXRvb2xiYXInKS5maW5kKCcuYnJlYWRjcnVtYi1pdGVtJyk7XG4gICAgbGV0IG5hbWUgPSAnJztcblxuICAgICRicmVhZGNydW1icy5lYWNoKChpLCBpdGVtKSA9PiB7XG4gICAgICBjb25zdCAkYnJlYWRjcnVtYiA9ICQoaXRlbSk7XG5cbiAgICAgIGNvbnN0IGJyZWFkY3J1bWJUaXRsZSA9IDAgPCAkYnJlYWRjcnVtYi5maW5kKCdhJykubGVuZ3RoID9cbiAgICAgICAgJGJyZWFkY3J1bWIuZmluZCgnYScpLnRleHQoKSA6XG4gICAgICAgICRicmVhZGNydW1iLnRleHQoKTtcblxuICAgICAgaWYgKDAgPCBuYW1lLmxlbmd0aCkge1xuICAgICAgICBuYW1lID0gbmFtZS5jb25jYXQoJyA+ICcpO1xuICAgICAgfVxuXG4gICAgICBuYW1lID0gbmFtZS5jb25jYXQoYnJlYWRjcnVtYlRpdGxlKTtcbiAgICB9KTtcblxuICAgIHJldHVybiBuYW1lO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2V4cG9ydC10by1zcWwtbWFuYWdlci1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogSGFuZGxlcyBzdWJtaXQgb2YgZ3JpZCBhY3Rpb25zXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFN1Ym1pdEJ1bGtBY3Rpb25FeHRlbnNpb24ge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICByZXR1cm4ge1xuICAgICAgZXh0ZW5kOiAoZ3JpZCkgPT4gdGhpcy5leHRlbmQoZ3JpZCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZCB3aXRoIGJ1bGsgYWN0aW9uIHN1Ym1pdHRpbmdcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1idWxrLWFjdGlvbi1zdWJtaXQtYnRuJywgKGV2ZW50KSA9PiB7XG4gICAgICB0aGlzLnN1Ym1pdChldmVudCwgZ3JpZCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlIGJ1bGsgYWN0aW9uIHN1Ym1pdHRpbmdcbiAgICpcbiAgICogQHBhcmFtIHtFdmVudH0gZXZlbnRcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBzdWJtaXQoZXZlbnQsIGdyaWQpIHtcbiAgICBjb25zdCAkc3VibWl0QnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICRzdWJtaXRCdG4uZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG5cbiAgICBpZiAodHlwZW9mIGNvbmZpcm1NZXNzYWdlICE9PSBcInVuZGVmaW5lZFwiICYmIDAgPCBjb25maXJtTWVzc2FnZS5sZW5ndGggJiYgIWNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgY29uc3QgJGZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfZmlsdGVyX2Zvcm0nKTtcblxuICAgICRmb3JtLmF0dHIoJ2FjdGlvbicsICRzdWJtaXRCdG4uZGF0YSgnZm9ybS11cmwnKSk7XG4gICAgJGZvcm0uYXR0cignbWV0aG9kJywgJHN1Ym1pdEJ0bi5kYXRhKCdmb3JtLW1ldGhvZCcpKTtcbiAgICAkZm9ybS5zdWJtaXQoKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zdWJtaXQtYnVsay1hY3Rpb24tZXh0ZW5zaW9uLmpzIl0sInNvdXJjZVJvb3QiOiIifQ==