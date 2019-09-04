window["currency"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 319);
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

/***/ 252:
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
 * This class triggers events required for turning on or off exchange rates scheduler an displaying the right text
 * below the switch.
 */

var ExchangeRatesUpdateScheduler = function () {
  function ExchangeRatesUpdateScheduler() {
    _classCallCheck(this, ExchangeRatesUpdateScheduler);

    this._initEvents();

    return {};
  }

  _createClass(ExchangeRatesUpdateScheduler, [{
    key: '_initEvents',
    value: function _initEvents() {
      var _this = this;

      $(document).on('change', '.js-live-exchange-rate', function (event) {
        return _this._initLiveExchangeRate(event);
      });
    }

    /**
     * @param {Object} event
     *
     * @private
     */

  }, {
    key: '_initLiveExchangeRate',
    value: function _initLiveExchangeRate(event) {
      var _this2 = this;

      var $liveExchangeRatesSwitch = $(event.currentTarget);
      var $form = $liveExchangeRatesSwitch.closest('form');
      var formItems = $form.serialize();

      $.ajax({
        type: 'POST',
        url: $liveExchangeRatesSwitch.attr('data-url'),
        data: formItems
      }).then(function (response) {
        if (!response.status) {
          showErrorMessage(response.message);
          _this2._changeTextByCurrentSwitchValue($liveExchangeRatesSwitch.val());

          return;
        }

        showSuccessMessage(response.message);
        _this2._changeTextByCurrentSwitchValue($liveExchangeRatesSwitch.val());
      }).fail(function (response) {
        if (typeof response.responseJSON !== 'undefined') {
          showErrorMessage(response.responseJSON.message);
          _this2._changeTextByCurrentSwitchValue($liveExchangeRatesSwitch.val());
        }
      });
    }
  }, {
    key: '_changeTextByCurrentSwitchValue',
    value: function _changeTextByCurrentSwitchValue(switchValue) {
      var valueParsed = parseInt(switchValue);
      $('.js-exchange-rate-text-when-disabled').toggleClass('d-none', 0 !== valueParsed);
      $('.js-exchange-rate-text-when-enabled').toggleClass('d-none', 1 !== valueParsed);
    }
  }]);

  return ExchangeRatesUpdateScheduler;
}();

exports.default = ExchangeRatesUpdateScheduler;

/***/ }),

/***/ 319:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _grid = __webpack_require__(2);

var _grid2 = _interopRequireDefault(_grid);

var _sortingExtension = __webpack_require__(6);

var _sortingExtension2 = _interopRequireDefault(_sortingExtension);

var _filtersResetExtension = __webpack_require__(4);

var _filtersResetExtension2 = _interopRequireDefault(_filtersResetExtension);

var _reloadListExtension = __webpack_require__(5);

var _reloadListExtension2 = _interopRequireDefault(_reloadListExtension);

var _columnTogglingExtension = __webpack_require__(17);

var _columnTogglingExtension2 = _interopRequireDefault(_columnTogglingExtension);

var _submitRowActionExtension = __webpack_require__(12);

var _submitRowActionExtension2 = _interopRequireDefault(_submitRowActionExtension);

var _choiceTree = __webpack_require__(18);

var _choiceTree2 = _interopRequireDefault(_choiceTree);

var _ExchangeRatesUpdateScheduler = __webpack_require__(252);

var _ExchangeRatesUpdateScheduler2 = _interopRequireDefault(_ExchangeRatesUpdateScheduler);

var _filtersSubmitButtonEnablerExtension = __webpack_require__(7);

var _filtersSubmitButtonEnablerExtension2 = _interopRequireDefault(_filtersSubmitButtonEnablerExtension);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
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

$(function () {
  var currency = new _grid2.default('currency');
  currency.addExtension(new _sortingExtension2.default());
  currency.addExtension(new _filtersResetExtension2.default());
  currency.addExtension(new _reloadListExtension2.default());
  currency.addExtension(new _columnTogglingExtension2.default());
  currency.addExtension(new _submitRowActionExtension2.default());
  currency.addExtension(new _filtersSubmitButtonEnablerExtension2.default());

  var choiceTree = new _choiceTree2.default('#currency_shop_association');
  choiceTree.enableAutoCheckChildren();

  new _ExchangeRatesUpdateScheduler2.default();
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

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQ/MjBkNCoqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzPzM2OTgqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9yb3cvc3VibWl0LXJvdy1hY3Rpb24tZXh0ZW5zaW9uLmpzPzI3ZDEqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvdXRpbHMvdGFibGUtc29ydGluZy5qcz8xNWQ0KioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2FwcC91dGlscy9yZXNldF9zZWFyY2guanM/MWE3ZioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi10b2dnbGluZy1leHRlbnNpb24uanM/Njk0MyoqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZm9ybS9jaG9pY2UtdHJlZS5qcz81NDFhKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9ncmlkLmpzPzgxM2EqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvY3VycmVuY3kvRXhjaGFuZ2VSYXRlc1VwZGF0ZVNjaGVkdWxlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9jdXJyZW5jeS9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uLmpzPzE2ZjEqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9yZWxvYWQtbGlzdC1leHRlbnNpb24uanM/ZDNlMCoqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uLmpzPzExM2UqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXN1Ym1pdC1idXR0b24tZW5hYmxlci1leHRlbnNpb24uanM/MDM3OSoqKioqKioqKioqKioiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIlN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiIsImdyaWQiLCJnZXRDb250YWluZXIiLCJvbiIsImV2ZW50IiwicHJldmVudERlZmF1bHQiLCIkYnV0dG9uIiwiY3VycmVudFRhcmdldCIsImNvbmZpcm1NZXNzYWdlIiwiZGF0YSIsImxlbmd0aCIsImNvbmZpcm0iLCJtZXRob2QiLCJpc0dldE9yUG9zdE1ldGhvZCIsImluY2x1ZGVzIiwiJGZvcm0iLCJhcHBlbmRUbyIsImFwcGVuZCIsInN1Ym1pdCIsImdsb2JhbCIsIlRhYmxlU29ydGluZyIsInRhYmxlIiwic2VsZWN0b3IiLCJjb2x1bW5zIiwiZmluZCIsImUiLCIkY29sdW1uIiwiZGVsZWdhdGVUYXJnZXQiLCJfc29ydEJ5Q29sdW1uIiwiX2dldFRvZ2dsZWRTb3J0RGlyZWN0aW9uIiwiY29sdW1uTmFtZSIsImRpcmVjdGlvbiIsImlzIiwiRXJyb3IiLCJjb2x1bW4iLCJsb2NhdGlvbiIsIl9nZXRVcmwiLCJjb2xOYW1lIiwicHJlZml4IiwidXJsIiwiVVJMIiwiaHJlZiIsInBhcmFtcyIsInNlYXJjaFBhcmFtcyIsInNldCIsInRvU3RyaW5nIiwiaW5pdCIsInJlc2V0U2VhcmNoIiwicmVkaXJlY3RVcmwiLCJwb3N0IiwidGhlbiIsImFzc2lnbiIsIkNvbHVtblRvZ2dsaW5nRXh0ZW5zaW9uIiwiJHRhYmxlIiwiX3RvZ2dsZVZhbHVlIiwicm93IiwidG9nZ2xlVXJsIiwiX3N1Ym1pdEFzRm9ybSIsImFjdGlvbiIsIkNob2ljZVRyZWUiLCJ0cmVlU2VsZWN0b3IiLCIkY29udGFpbmVyIiwiJGlucHV0V3JhcHBlciIsIl90b2dnbGVDaGlsZFRyZWUiLCIkYWN0aW9uIiwiX3RvZ2dsZVRyZWUiLCJlbmFibGVBdXRvQ2hlY2tDaGlsZHJlbiIsImVuYWJsZUFsbElucHV0cyIsImRpc2FibGVBbGxJbnB1dHMiLCIkY2xpY2tlZENoZWNrYm94IiwiJGl0ZW1XaXRoQ2hpbGRyZW4iLCJjbG9zZXN0IiwicHJvcCIsInJlbW92ZUF0dHIiLCJhdHRyIiwiJHBhcmVudFdyYXBwZXIiLCJoYXNDbGFzcyIsInJlbW92ZUNsYXNzIiwiYWRkQ2xhc3MiLCIkcGFyZW50Q29udGFpbmVyIiwiY29uZmlnIiwiZXhwYW5kIiwiY29sbGFwc2UiLCJuZXh0QWN0aW9uIiwidGV4dCIsImljb24iLCJlYWNoIiwiaW5kZXgiLCJpdGVtIiwiJGl0ZW0iLCJHcmlkIiwiaWQiLCJleHRlbnNpb24iLCJleHRlbmQiLCJFeGNoYW5nZVJhdGVzVXBkYXRlU2NoZWR1bGVyIiwiX2luaXRFdmVudHMiLCJkb2N1bWVudCIsIl9pbml0TGl2ZUV4Y2hhbmdlUmF0ZSIsIiRsaXZlRXhjaGFuZ2VSYXRlc1N3aXRjaCIsImZvcm1JdGVtcyIsInNlcmlhbGl6ZSIsImFqYXgiLCJ0eXBlIiwicmVzcG9uc2UiLCJzdGF0dXMiLCJzaG93RXJyb3JNZXNzYWdlIiwibWVzc2FnZSIsIl9jaGFuZ2VUZXh0QnlDdXJyZW50U3dpdGNoVmFsdWUiLCJ2YWwiLCJzaG93U3VjY2Vzc01lc3NhZ2UiLCJmYWlsIiwicmVzcG9uc2VKU09OIiwic3dpdGNoVmFsdWUiLCJ2YWx1ZVBhcnNlZCIsInBhcnNlSW50IiwidG9nZ2xlQ2xhc3MiLCJjdXJyZW5jeSIsImFkZEV4dGVuc2lvbiIsIlNvcnRpbmdFeHRlbnNpb24iLCJGaWx0ZXJzUmVzZXRFeHRlbnNpb24iLCJSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uIiwiRmlsdGVyc1N1Ym1pdEJ1dHRvbkVuYWJsZXJFeHRlbnNpb24iLCJjaG9pY2VUcmVlIiwiUmVsb2FkTGlzdEV4dGVuc2lvbiIsImdldEhlYWRlckNvbnRhaW5lciIsInJlbG9hZCIsIiRzb3J0YWJsZVRhYmxlIiwiYXR0YWNoIiwiJGZpbHRlcnNSb3ciXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7O0FDaEVBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLENBQUM7O0FBRUQ7QUFDQTtBQUNBO0FBQ0EsQ0FBQztBQUNEO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQSw0Q0FBNEM7O0FBRTVDOzs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDcEJBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBLElBQU1BLElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7O0lBR3FCRSx3Qjs7Ozs7Ozs7QUFDbkI7Ozs7OzJCQUtPQyxJLEVBQU07QUFDWEEsV0FBS0MsWUFBTCxHQUFvQkMsRUFBcEIsQ0FBdUIsT0FBdkIsRUFBZ0MsdUJBQWhDLEVBQXlELFVBQUNDLEtBQUQsRUFBVztBQUNsRUEsY0FBTUMsY0FBTjs7QUFFQSxZQUFNQyxVQUFVUixFQUFFTSxNQUFNRyxhQUFSLENBQWhCO0FBQ0EsWUFBTUMsaUJBQWlCRixRQUFRRyxJQUFSLENBQWEsaUJBQWIsQ0FBdkI7O0FBRUEsWUFBSUQsZUFBZUUsTUFBZixJQUF5QixDQUFDQyxRQUFRSCxjQUFSLENBQTlCLEVBQXVEO0FB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OztxakJDNUJyQjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQTs7Ozs7Ozs7QUFFQTs7O0lBR3FCTCxnQjs7Ozs7Ozs7QUFDbkI7Ozs7OzJCQUtPckgsSSxFQUFNO0FBQ1gsVUFBTTZILGlCQUFpQjdILEtBQUtDLFlBQUwsR0FBb0JzQixJQUFwQixDQUF5QixhQUF6QixDQUF2Qjs7QUFFQSxVQUFJSixzQkFBSixDQUFpQjBHLGNBQWpCLEVBQWlDQyxNQUFqQztBQUNEOzs7Ozs7a0JBVmtCVCxnQjs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDOUJyQjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQTs7O0lBR3FCRyxtQzs7Ozs7Ozs7O0FBRW5COzs7OzsyQkFLT3hILEksRUFBTTtBQUNYLFVBQU0rSCxjQUFjL0gsS0FBS0MsWUFBTCxHQUFvQnNCLElBQXBCLENBQXlCLGlCQUF6QixDQUFwQjtBQUNBd0csa0JBQVl4RyxJQUFaLENBQWlCLHFCQUFqQixFQUF3Q2dELElBQXhDLENBQTZDLFVBQTdDLEVBQXlELElBQXpEOztBQUVBd0Qsa0JBQVl4RyxJQUFaLENBQWlCLGVBQWpCLEVBQWtDckIsRUFBbEMsQ0FBcUMsT0FBckMsRUFBOEMsWUFBTTtBQUNsRDZILG9CQUFZeEcsSUFBWixDQUFpQixxQkFBakIsRUFBd0NnRCxJQUF4QyxDQUE2QyxVQUE3QyxFQUF5RCxLQUF6RDtBQUNBd0Qsb0JBQVl4RyxJQUFaLENBQWlCLHVCQUFqQixFQUEwQ2dELElBQTFDLENBQStDLFFBQS9DLEVBQXlELEtBQXpEO0FBQ0QsT0FIRDtBQUlEOzs7Ozs7a0JBZmtCaUQsbUMiLCJmaWxlIjoiY3VycmVuY3kuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAzMTkpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDY4ZTgyOTFmMTM2MDcwZjI3NmJkIiwidmFyIGc7XHJcblxyXG4vLyBUaGlzIHdvcmtzIGluIG5vbi1zdHJpY3QgbW9kZVxyXG5nID0gKGZ1bmN0aW9uKCkge1xyXG5cdHJldHVybiB0aGlzO1xyXG59KSgpO1xyXG5cclxudHJ5IHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIGV2YWwgaXMgYWxsb3dlZCAoc2VlIENTUClcclxuXHRnID0gZyB8fCBGdW5jdGlvbihcInJldHVybiB0aGlzXCIpKCkgfHwgKDEsZXZhbCkoXCJ0aGlzXCIpO1xyXG59IGNhdGNoKGUpIHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIHRoZSB3aW5kb3cgcmVmZXJlbmNlIGlzIGF2YWlsYWJsZVxyXG5cdGlmKHR5cGVvZiB3aW5kb3cgPT09IFwib2JqZWN0XCIpXHJcblx0XHRnID0gd2luZG93O1xyXG59XHJcblxyXG4vLyBnIGNhbiBzdGlsbCBiZSB1bmRlZmluZWQsIGJ1dCBub3RoaW5nIHRvIGRvIGFib3V0IGl0Li4uXHJcbi8vIFdlIHJldHVybiB1bmRlZmluZWQsIGluc3RlYWQgb2Ygbm90aGluZyBoZXJlLCBzbyBpdCdzXHJcbi8vIGVhc2llciB0byBoYW5kbGUgdGhpcyBjYXNlLiBpZighZ2xvYmFsKSB7IC4uLn1cclxuXHJcbm1vZHVsZS5leHBvcnRzID0gZztcclxuXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzXG4vLyBtb2R1bGUgaWQgPSAxXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyNSAyOCAzMiAzNiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24gaGFuZGxlcyBzdWJtaXR0aW5nIG9mIHJvdyBhY3Rpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtc3VibWl0LXJvdy1hY3Rpb24nLCAoZXZlbnQpID0+IHtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgIGNvbnN0ICRidXR0b24gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgY29uc3QgY29uZmlybU1lc3NhZ2UgPSAkYnV0dG9uLmRhdGEoJ2NvbmZpcm0tbWVzc2FnZScpO1xuXG4gICAgICBpZiAoY29uZmlybU1lc3NhZ2UubGVuZ3RoICYmICFjb25maXJtKGNvbmZpcm1NZXNzYWdlKSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIGNvbnN0IG1ldGhvZCA9ICRidXR0b24uZGF0YSgnbWV0aG9kJyk7XG4gICAgICBjb25zdCBpc0dldE9yUG9zdE1ldGhvZCA9IFsnR0VUJywgJ1BPU1QnXS5pbmNsdWRlcyhtZXRob2QpO1xuXG4gICAgICBjb25zdCAkZm9ybSA9ICQoJzxmb3JtPicsIHtcbiAgICAgICAgJ2FjdGlvbic6ICRidXR0b24uZGF0YSgndXJsJyksXG4gICAgICAgICdtZXRob2QnOiBpc0dldE9yUG9zdE1ldGhvZCA/IG1ldGhvZCA6ICdQT1NUJyxcbiAgICAgIH0pLmFwcGVuZFRvKCdib2R5Jyk7XG5cbiAgICAgIGlmICghaXNHZXRPclBvc3RNZXRob2QpIHtcbiAgICAgICAgJGZvcm0uYXBwZW5kKCQoJzxpbnB1dD4nLCB7XG4gICAgICAgICAgJ3R5cGUnOiAnX2hpZGRlbicsXG4gICAgICAgICAgJ25hbWUnOiAnX21ldGhvZCcsXG4gICAgICAgICAgJ3ZhbHVlJzogbWV0aG9kXG4gICAgICAgIH0pKTtcbiAgICAgIH1cblxuICAgICAgJGZvcm0uc3VibWl0KCk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYWN0aW9uL3Jvdy9zdWJtaXQtcm93LWFjdGlvbi1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gZ2xvYmFsLiQ7XG5cbi8qKlxuICogTWFrZXMgYSB0YWJsZSBzb3J0YWJsZSBieSBjb2x1bW5zLlxuICogVGhpcyBmb3JjZXMgYSBwYWdlIHJlbG9hZCB3aXRoIG1vcmUgcXVlcnkgcGFyYW1ldGVycy5cbiAqL1xuY2xhc3MgVGFibGVTb3J0aW5nIHtcblxuICAvKipcbiAgICogQHBhcmFtIHtqUXVlcnl9IHRhYmxlXG4gICAqL1xuICBjb25zdHJ1Y3Rvcih0YWJsZSkge1xuICAgIHRoaXMuc2VsZWN0b3IgPSAnLnBzLXNvcnRhYmxlLWNvbHVtbic7XG4gICAgdGhpcy5jb2x1bW5zID0gJCh0YWJsZSkuZmluZCh0aGlzLnNlbGVjdG9yKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBdHRhY2hlcyB0aGUgbGlzdGVuZXJzXG4gICAqL1xuICBhdHRhY2goKSB7XG4gICAgdGhpcy5jb2x1bW5zLm9uKCdjbGljaycsIChlKSA9PiB7XG4gICAgICBjb25zdCAkY29sdW1uID0gJChlLmRlbGVnYXRlVGFyZ2V0KTtcbiAgICAgIHRoaXMuX3NvcnRCeUNvbHVtbigkY29sdW1uLCB0aGlzLl9nZXRUb2dnbGVkU29ydERpcmVjdGlvbigkY29sdW1uKSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogU29ydCB1c2luZyBhIGNvbHVtbiBuYW1lXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBjb2x1bW5OYW1lXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBkaXJlY3Rpb24gXCJhc2NcIiBvciBcImRlc2NcIlxuICAgKi9cbiAgc29ydEJ5KGNvbHVtbk5hbWUsIGRpcmVjdGlvbikge1xuICAgIGNvbnN0ICRjb2x1bW4gPSB0aGlzLmNvbHVtbnMuaXMoYFtkYXRhLXNvcnQtY29sLW5hbWU9XCIke2NvbHVtbk5hbWV9XCJdYCk7XG4gICAgaWYgKCEkY29sdW1uKSB7XG4gICAgICB0aHJvdyBuZXcgRXJyb3IoYENhbm5vdCBzb3J0IGJ5IFwiJHtjb2x1bW5OYW1lfVwiOiBpbnZhbGlkIGNvbHVtbmApO1xuICAgIH1cblxuICAgIHRoaXMuX3NvcnRCeUNvbHVtbigkY29sdW1uLCBkaXJlY3Rpb24pO1xuICB9XG5cbiAgLyoqXG4gICAqIFNvcnQgdXNpbmcgYSBjb2x1bW4gZWxlbWVudFxuICAgKiBAcGFyYW0ge2pRdWVyeX0gY29sdW1uXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBkaXJlY3Rpb24gXCJhc2NcIiBvciBcImRlc2NcIlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3NvcnRCeUNvbHVtbihjb2x1bW4sIGRpcmVjdGlvbikge1xuICAgIHdpbmRvdy5sb2NhdGlvbiA9IHRoaXMuX2dldFVybChjb2x1bW4uZGF0YSgnc29ydENvbE5hbWUnKSwgKGRpcmVjdGlvbiA9PT0gJ2Rlc2MnKSA/ICdkZXNjJyA6ICdhc2MnLCBjb2x1bW4uZGF0YSgnc29ydFByZWZpeCcpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXR1cm5zIHRoZSBpbnZlcnRlZCBkaXJlY3Rpb24gdG8gc29ydCBhY2NvcmRpbmcgdG8gdGhlIGNvbHVtbidzIGN1cnJlbnQgb25lXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSBjb2x1bW5cbiAgICogQHJldHVybiB7c3RyaW5nfVxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldFRvZ2dsZWRTb3J0RGlyZWN0aW9uKGNvbHVtbikge1xuICAgIHJldHVybiBjb2x1bW4uZGF0YSgnc29ydERpcmVjdGlvbicpID09PSAnYXNjJyA/ICdkZXNjJyA6ICdhc2MnO1xuICB9XG5cbiAgLyoqXG4gICAqIFJldHVybnMgdGhlIHVybCBmb3IgdGhlIHNvcnRlZCB0YWJsZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gY29sTmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBwcmVmaXhcbiAgICogQHJldHVybiB7c3RyaW5nfVxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldFVybChjb2xOYW1lLCBkaXJlY3Rpb24sIHByZWZpeCkge1xuICAgIGNvbnN0IHVybCA9IG5ldyBVUkwod2luZG93LmxvY2F0aW9uLmhyZWYpO1xuICAgIGNvbnN0IHBhcmFtcyA9IHVybC5zZWFyY2hQYXJhbXM7XG5cbiAgICBpZiAocHJlZml4KSB7XG4gICAgICBwYXJhbXMuc2V0KHByZWZpeCsnW29yZGVyQnldJywgY29sTmFtZSk7XG4gICAgICBwYXJhbXMuc2V0KHByZWZpeCsnW3NvcnRPcmRlcl0nLCBkaXJlY3Rpb24pO1xuICAgIH0gZWxzZSB7XG4gICAgICBwYXJhbXMuc2V0KCdvcmRlckJ5JywgY29sTmFtZSk7XG4gICAgICBwYXJhbXMuc2V0KCdzb3J0T3JkZXInLCBkaXJlY3Rpb24pO1xuICAgIH1cblxuICAgIHJldHVybiB1cmwudG9TdHJpbmcoKTtcbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBUYWJsZVNvcnRpbmc7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9hcHAvdXRpbHMvdGFibGUtc29ydGluZy5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbi8qKlxuICogU2VuZCBhIFBvc3QgUmVxdWVzdCB0byByZXNldCBzZWFyY2ggQWN0aW9uLlxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuY29uc3QgaW5pdCA9IGZ1bmN0aW9uIHJlc2V0U2VhcmNoKHVybCwgcmVkaXJlY3RVcmwpIHtcbiAgICAkLnBvc3QodXJsKS50aGVuKCgpID0+IHdpbmRvdy5sb2NhdGlvbi5hc3NpZ24ocmVkaXJlY3RVcmwpKTtcbn07XG5cbmV4cG9ydCBkZWZhdWx0IGluaXQ7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9hcHAvdXRpbHMvcmVzZXRfc2VhcmNoLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG4vKipcbiAqIENsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggXCJDb2x1bW4gdG9nZ2xpbmdcIiBmZWF0dXJlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENvbHVtblRvZ2dsaW5nRXh0ZW5zaW9uIHtcblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGNvbnN0ICR0YWJsZSA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgndGFibGUudGFibGUnKTtcbiAgICAkdGFibGUuZmluZCgnLnBzLXRvZ2dsYWJsZS1yb3cnKS5vbignY2xpY2snLCAoZSkgPT4ge1xuICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgdGhpcy5fdG9nZ2xlVmFsdWUoJChlLmRlbGVnYXRlVGFyZ2V0KSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIHtqUXVlcnl9IHJvd1xuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3RvZ2dsZVZhbHVlKHJvdykge1xuICAgIGNvbnN0IHRvZ2dsZVVybCA9IHJvdy5kYXRhKCd0b2dnbGVVcmwnKTtcblxuICAgIHRoaXMuX3N1Ym1pdEFzRm9ybSh0b2dnbGVVcmwpO1xuICB9XG5cbiAgLyoqXG4gICAqIFN1Ym1pdHMgcmVxdWVzdCB1cmwgYXMgZm9ybVxuICAgKlxuICAgKiBAcGFyYW0ge3N0cmluZ30gdG9nZ2xlVXJsXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc3VibWl0QXNGb3JtKHRvZ2dsZVVybCkge1xuICAgIGNvbnN0ICRmb3JtID0gJCgnPGZvcm0+Jywge1xuICAgICAgYWN0aW9uOiB0b2dnbGVVcmwsXG4gICAgICBtZXRob2Q6ICdQT1NUJyxcbiAgICB9KS5hcHBlbmRUbygnYm9keScpO1xuXG4gICAgJGZvcm0uc3VibWl0KCk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vY29sdW1uLXRvZ2dsaW5nLWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBIYW5kbGVzIFVJIGludGVyYWN0aW9ucyBvZiBjaG9pY2UgdHJlZVxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDaG9pY2VUcmVlIHtcbiAgLyoqXG4gICAqIEBwYXJhbSB7U3RyaW5nfSB0cmVlU2VsZWN0b3JcbiAgICovXG4gIGNvbnN0cnVjdG9yKHRyZWVTZWxlY3Rvcikge1xuICAgIHRoaXMuJGNvbnRhaW5lciA9ICQodHJlZVNlbGVjdG9yKTtcblxuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2xpY2snLCAnLmpzLWlucHV0LXdyYXBwZXInLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRpbnB1dFdyYXBwZXIgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICB0aGlzLl90b2dnbGVDaGlsZFRyZWUoJGlucHV0V3JhcHBlcik7XG4gICAgfSk7XG5cbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgJy5qcy10b2dnbGUtY2hvaWNlLXRyZWUtYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkYWN0aW9uID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcblxuICAgICAgdGhpcy5fdG9nZ2xlVHJlZSgkYWN0aW9uKTtcbiAgICB9KTtcblxuICAgIHJldHVybiB7XG4gICAgICBlbmFibGVBdXRvQ2hlY2tDaGlsZHJlbjogKCkgPT4gdGhpcy5lbmFibGVBdXRvQ2hlY2tDaGlsZHJlbigpLFxuICAgICAgZW5hYmxlQWxsSW5wdXRzOiAoKSA9PiB0aGlzLmVuYWJsZUFsbElucHV0cygpLFxuICAgICAgZGlzYWJsZUFsbElucHV0czogKCkgPT4gdGhpcy5kaXNhYmxlQWxsSW5wdXRzKCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbmFibGUgYXV0b21hdGljIGNoZWNrL3VuY2hlY2sgb2YgY2xpY2tlZCBpdGVtJ3MgY2hpbGRyZW4uXG4gICAqL1xuICBlbmFibGVBdXRvQ2hlY2tDaGlsZHJlbigpIHtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NoYW5nZScsICdpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0nLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRjbGlja2VkQ2hlY2tib3ggPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgY29uc3QgJGl0ZW1XaXRoQ2hpbGRyZW4gPSAkY2xpY2tlZENoZWNrYm94LmNsb3Nlc3QoJ2xpJyk7XG5cbiAgICAgICRpdGVtV2l0aENoaWxkcmVuXG4gICAgICAgIC5maW5kKCd1bCBpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0nKVxuICAgICAgICAucHJvcCgnY2hlY2tlZCcsICRjbGlja2VkQ2hlY2tib3guaXMoJzpjaGVja2VkJykpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEVuYWJsZSBhbGwgaW5wdXRzIGluIHRoZSBjaG9pY2UgdHJlZS5cbiAgICovXG4gIGVuYWJsZUFsbElucHV0cygpIHtcbiAgICB0aGlzLiRjb250YWluZXIuZmluZCgnaW5wdXQnKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIERpc2FibGUgYWxsIGlucHV0cyBpbiB0aGUgY2hvaWNlIHRyZWUuXG4gICAqL1xuICBkaXNhYmxlQWxsSW5wdXRzKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5maW5kKCdpbnB1dCcpLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG4gIH1cblxuICAvKipcbiAgICogQ29sbGFwc2Ugb3IgZXhwYW5kIHN1Yi10cmVlIGZvciBzaW5nbGUgcGFyZW50XG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkaW5wdXRXcmFwcGVyXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdG9nZ2xlQ2hpbGRUcmVlKCRpbnB1dFdyYXBwZXIpIHtcbiAgICBjb25zdCAkcGFyZW50V3JhcHBlciA9ICRpbnB1dFdyYXBwZXIuY2xvc2VzdCgnbGknKTtcblxuICAgIGlmICgkcGFyZW50V3JhcHBlci5oYXNDbGFzcygnZXhwYW5kZWQnKSkge1xuICAgICAgJHBhcmVudFdyYXBwZXJcbiAgICAgICAgLnJlbW92ZUNsYXNzKCdleHBhbmRlZCcpXG4gICAgICAgIC5hZGRDbGFzcygnY29sbGFwc2VkJyk7XG5cbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBpZiAoJHBhcmVudFdyYXBwZXIuaGFzQ2xhc3MoJ2NvbGxhcHNlZCcpKSB7XG4gICAgICAkcGFyZW50V3JhcHBlclxuICAgICAgICAucmVtb3ZlQ2xhc3MoJ2NvbGxhcHNlZCcpXG4gICAgICAgIC5hZGRDbGFzcygnZXhwYW5kZWQnKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogQ29sbGFwc2Ugb3IgZXhwYW5kIHdob2xlIHRyZWVcbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRhY3Rpb25cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGVUcmVlKCRhY3Rpb24pIHtcbiAgICBjb25zdCAkcGFyZW50Q29udGFpbmVyID0gJGFjdGlvbi5jbG9zZXN0KCcuanMtY2hvaWNlLXRyZWUtY29udGFpbmVyJyk7XG4gICAgY29uc3QgYWN0aW9uID0gJGFjdGlvbi5kYXRhKCdhY3Rpb24nKTtcblxuICAgIC8vIHRvZ2dsZSBhY3Rpb24gY29uZmlndXJhdGlvblxuICAgIGNvbnN0IGNvbmZpZyA9IHtcbiAgICAgIGFkZENsYXNzOiB7XG4gICAgICAgIGV4cGFuZDogJ2V4cGFuZGVkJyxcbiAgICAgICAgY29sbGFwc2U6ICdjb2xsYXBzZWQnLFxuICAgICAgfSxcbiAgICAgIHJlbW92ZUNsYXNzOiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlZCcsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kZWQnLFxuICAgICAgfSxcbiAgICAgIG5leHRBY3Rpb246IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2UnLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZCcsXG4gICAgICB9LFxuICAgICAgdGV4dDoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZWQtdGV4dCcsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kZWQtdGV4dCcsXG4gICAgICB9LFxuICAgICAgaWNvbjoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZWQtaWNvbicsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kZWQtaWNvbicsXG4gICAgICB9XG4gICAgfTtcblxuICAgICRwYXJlbnRDb250YWluZXIuZmluZCgnbGknKS5lYWNoKChpbmRleCwgaXRlbSkgPT4ge1xuICAgICAgY29uc3QgJGl0ZW0gPSAkKGl0ZW0pO1xuXG4gICAgICBpZiAoJGl0ZW0uaGFzQ2xhc3MoY29uZmlnLnJlbW92ZUNsYXNzW2FjdGlvbl0pKSB7XG4gICAgICAgICAgJGl0ZW0ucmVtb3ZlQ2xhc3MoY29uZmlnLnJlbW92ZUNsYXNzW2FjdGlvbl0pXG4gICAgICAgICAgICAuYWRkQ2xhc3MoY29uZmlnLmFkZENsYXNzW2FjdGlvbl0pO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgJGFjdGlvbi5kYXRhKCdhY3Rpb24nLCBjb25maWcubmV4dEFjdGlvblthY3Rpb25dKTtcbiAgICAkYWN0aW9uLmZpbmQoJy5tYXRlcmlhbC1pY29ucycpLnRleHQoJGFjdGlvbi5kYXRhKGNvbmZpZy5pY29uW2FjdGlvbl0pKTtcbiAgICAkYWN0aW9uLmZpbmQoJy5qcy10b2dnbGUtdGV4dCcpLnRleHQoJGFjdGlvbi5kYXRhKGNvbmZpZy50ZXh0W2FjdGlvbl0pKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9mb3JtL2Nob2ljZS10cmVlLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIGlzIHJlc3BvbnNpYmxlIGZvciBoYW5kbGluZyBHcmlkIGV2ZW50c1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBHcmlkIHtcbiAgLyoqXG4gICAqIEdyaWQgaWRcbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IGlkXG4gICAqL1xuICBjb25zdHJ1Y3RvcihpZCkge1xuICAgIHRoaXMuaWQgPSBpZDtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKCcjJyArIHRoaXMuaWQgKyAnX2dyaWQnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZ3JpZCBpZFxuICAgKlxuICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgKi9cbiAgZ2V0SWQoKSB7XG4gICAgcmV0dXJuIHRoaXMuaWQ7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGdyaWQgY29udGFpbmVyXG4gICAqXG4gICAqIEByZXR1cm5zIHtqUXVlcnl9XG4gICAqL1xuICBnZXRDb250YWluZXIoKSB7XG4gICAgcmV0dXJuIHRoaXMuJGNvbnRhaW5lcjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZ3JpZCBoZWFkZXIgY29udGFpbmVyXG4gICAqXG4gICAqIEByZXR1cm5zIHtqUXVlcnl9XG4gICAqL1xuICBnZXRIZWFkZXJDb250YWluZXIoKSB7XG4gICAgcmV0dXJuIHRoaXMuJGNvbnRhaW5lci5jbG9zZXN0KCcuanMtZ3JpZC1wYW5lbCcpLmZpbmQoJy5qcy1ncmlkLWhlYWRlcicpO1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkIHdpdGggZXh0ZXJuYWwgZXh0ZW5zaW9uc1xuICAgKlxuICAgKiBAcGFyYW0ge29iamVjdH0gZXh0ZW5zaW9uXG4gICAqL1xuICBhZGRFeHRlbnNpb24oZXh0ZW5zaW9uKSB7XG4gICAgZXh0ZW5zaW9uLmV4dGVuZCh0aGlzKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2dyaWQuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogVGhpcyBjbGFzcyB0cmlnZ2VycyBldmVudHMgcmVxdWlyZWQgZm9yIHR1cm5pbmcgb24gb3Igb2ZmIGV4Y2hhbmdlIHJhdGVzIHNjaGVkdWxlciBhbiBkaXNwbGF5aW5nIHRoZSByaWdodCB0ZXh0XG4gKiBiZWxvdyB0aGUgc3dpdGNoLlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBFeGNoYW5nZVJhdGVzVXBkYXRlU2NoZWR1bGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICB0aGlzLl9pbml0RXZlbnRzKCk7XG5cbiAgIHJldHVybiB7fTtcbiAgfVxuXG4gIF9pbml0RXZlbnRzKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjaGFuZ2UnLCAnLmpzLWxpdmUtZXhjaGFuZ2UtcmF0ZScsIChldmVudCkgPT4gdGhpcy5faW5pdExpdmVFeGNoYW5nZVJhdGUoZXZlbnQpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0ge09iamVjdH0gZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0TGl2ZUV4Y2hhbmdlUmF0ZShldmVudCkge1xuICAgIGNvbnN0ICRsaXZlRXhjaGFuZ2VSYXRlc1N3aXRjaCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgY29uc3QgJGZvcm0gPSAkbGl2ZUV4Y2hhbmdlUmF0ZXNTd2l0Y2guY2xvc2VzdCgnZm9ybScpO1xuICAgIGNvbnN0IGZvcm1JdGVtcyA9ICRmb3JtLnNlcmlhbGl6ZSgpO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgIHVybDogJGxpdmVFeGNoYW5nZVJhdGVzU3dpdGNoLmF0dHIoJ2RhdGEtdXJsJyksXG4gICAgICBkYXRhOiBmb3JtSXRlbXMsXG4gICAgfSlcbiAgICAgIC50aGVuKChyZXNwb25zZSkgPT4ge1xuICAgICAgICBpZiAoIXJlc3BvbnNlLnN0YXR1cykge1xuICAgICAgICAgIHNob3dFcnJvck1lc3NhZ2UocmVzcG9uc2UubWVzc2FnZSk7XG4gICAgICAgICAgdGhpcy5fY2hhbmdlVGV4dEJ5Q3VycmVudFN3aXRjaFZhbHVlKCRsaXZlRXhjaGFuZ2VSYXRlc1N3aXRjaC52YWwoKSk7XG5cbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBzaG93U3VjY2Vzc01lc3NhZ2UocmVzcG9uc2UubWVzc2FnZSk7XG4gICAgICAgIHRoaXMuX2NoYW5nZVRleHRCeUN1cnJlbnRTd2l0Y2hWYWx1ZSgkbGl2ZUV4Y2hhbmdlUmF0ZXNTd2l0Y2gudmFsKCkpO1xuICAgICAgfVxuICAgICkuZmFpbCgocmVzcG9uc2UpID0+IHtcbiAgICAgIGlmICh0eXBlb2YgcmVzcG9uc2UucmVzcG9uc2VKU09OICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICBzaG93RXJyb3JNZXNzYWdlKHJlc3BvbnNlLnJlc3BvbnNlSlNPTi5tZXNzYWdlKTtcbiAgICAgICAgdGhpcy5fY2hhbmdlVGV4dEJ5Q3VycmVudFN3aXRjaFZhbHVlKCRsaXZlRXhjaGFuZ2VSYXRlc1N3aXRjaC52YWwoKSk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICBfY2hhbmdlVGV4dEJ5Q3VycmVudFN3aXRjaFZhbHVlKHN3aXRjaFZhbHVlKSB7XG4gICAgY29uc3QgdmFsdWVQYXJzZWQgPSBwYXJzZUludChzd2l0Y2hWYWx1ZSk7XG4gICAgJCgnLmpzLWV4Y2hhbmdlLXJhdGUtdGV4dC13aGVuLWRpc2FibGVkJykudG9nZ2xlQ2xhc3MoJ2Qtbm9uZScsIDAgIT09IHZhbHVlUGFyc2VkKTtcbiAgICAkKCcuanMtZXhjaGFuZ2UtcmF0ZS10ZXh0LXdoZW4tZW5hYmxlZCcpLnRvZ2dsZUNsYXNzKCdkLW5vbmUnLCAxICE9PSB2YWx1ZVBhcnNlZCk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2N1cnJlbmN5L0V4Y2hhbmdlUmF0ZXNVcGRhdGVTY2hlZHVsZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgR3JpZCBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZ3JpZCc7XG5pbXBvcnQgU29ydGluZ0V4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uJztcbmltcG9ydCBGaWx0ZXJzUmVzZXRFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvbic7XG5pbXBvcnQgUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvbic7XG5pbXBvcnQgQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9jb2x1bW4tdG9nZ2xpbmctZXh0ZW5zaW9uJztcbmltcG9ydCBTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9hY3Rpb24vcm93L3N1Ym1pdC1yb3ctYWN0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgQ2hvaWNlVHJlZSBmcm9tICcuLi8uLi9jb21wb25lbnRzL2Zvcm0vY2hvaWNlLXRyZWUnO1xuaW1wb3J0IEV4Y2hhbmdlUmF0ZXNVcGRhdGVTY2hlZHVsZXIgZnJvbSAnLi9FeGNoYW5nZVJhdGVzVXBkYXRlU2NoZWR1bGVyJztcbmltcG9ydCBGaWx0ZXJzU3VibWl0QnV0dG9uRW5hYmxlckV4dGVuc2lvblxuICBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtc3VibWl0LWJ1dHRvbi1lbmFibGVyLWV4dGVuc2lvbic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gIGNvbnN0IGN1cnJlbmN5ID0gbmV3IEdyaWQoJ2N1cnJlbmN5Jyk7XG4gIGN1cnJlbmN5LmFkZEV4dGVuc2lvbihuZXcgU29ydGluZ0V4dGVuc2lvbigpKTtcbiAgY3VycmVuY3kuYWRkRXh0ZW5zaW9uKG5ldyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24oKSk7XG4gIGN1cnJlbmN5LmFkZEV4dGVuc2lvbihuZXcgUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbigpKTtcbiAgY3VycmVuY3kuYWRkRXh0ZW5zaW9uKG5ldyBDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbigpKTtcbiAgY3VycmVuY3kuYWRkRXh0ZW5zaW9uKG5ldyBTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24oKSk7XG4gIGN1cnJlbmN5LmFkZEV4dGVuc2lvbihuZXcgRmlsdGVyc1N1Ym1pdEJ1dHRvbkVuYWJsZXJFeHRlbnNpb24oKSk7XG5cbiAgY29uc3QgY2hvaWNlVHJlZSA9IG5ldyBDaG9pY2VUcmVlKCcjY3VycmVuY3lfc2hvcF9hc3NvY2lhdGlvbicpO1xuICBjaG9pY2VUcmVlLmVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCk7XG5cbiAgbmV3IEV4Y2hhbmdlUmF0ZXNVcGRhdGVTY2hlZHVsZXIoKTtcbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvY3VycmVuY3kvaW5kZXguanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgcmVzZXRTZWFyY2ggZnJvbSAnLi4vLi4vLi4vYXBwL3V0aWxzL3Jlc2V0X3NlYXJjaCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggZmlsdGVycyByZXNldHRpbmdcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIHtcblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1yZXNldC1zZWFyY2gnLCAoZXZlbnQpID0+IHtcbiAgICAgIHJlc2V0U2VhcmNoKCQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgndXJsJyksICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgncmVkaXJlY3QnKSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1yZXNldC1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG4vKipcbiAqIENsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggXCJMaXN0IHJlbG9hZFwiIGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX3JlZnJlc2hfbGlzdC1ncmlkLWFjdGlvbicsICgpID0+IHtcbiAgICAgIGxvY2F0aW9uLnJlbG9hZCgpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBUYWJsZVNvcnRpbmcgZnJvbSAnLi4vLi4vLi4vYXBwL3V0aWxzL3RhYmxlLXNvcnRpbmcnO1xuXG4vKipcbiAqIENsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggXCJMaXN0IHJlbG9hZFwiIGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTb3J0aW5nRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBjb25zdCAkc29ydGFibGVUYWJsZSA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgndGFibGUudGFibGUnKTtcblxuICAgIG5ldyBUYWJsZVNvcnRpbmcoJHNvcnRhYmxlVGFibGUpLmF0dGFjaCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBSZXNwb25zaWJsZSBmb3IgZ3JpZCBmaWx0ZXJzIHNlYXJjaCBhbmQgcmVzZXQgYnV0dG9uIGF2YWlsYWJpbGl0eSB3aGVuIGZpbHRlciBpbnB1dHMgY2hhbmdlcy5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRmlsdGVyc1N1Ym1pdEJ1dHRvbkVuYWJsZXJFeHRlbnNpb24ge1xuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgY29uc3QgJGZpbHRlcnNSb3cgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5jb2x1bW4tZmlsdGVycycpO1xuICAgICRmaWx0ZXJzUm93LmZpbmQoJy5ncmlkLXNlYXJjaC1idXR0b24nKS5wcm9wKCdkaXNhYmxlZCcsIHRydWUpO1xuXG4gICAgJGZpbHRlcnNSb3cuZmluZCgnaW5wdXQsIHNlbGVjdCcpLm9uKCdpbnB1dCcsICgpID0+IHtcbiAgICAgICRmaWx0ZXJzUm93LmZpbmQoJy5ncmlkLXNlYXJjaC1idXR0b24nKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICAgICRmaWx0ZXJzUm93LmZpbmQoJy5qcy1ncmlkLXJlc2V0LWJ1dHRvbicpLnByb3AoJ2hpZGRlbicsIGZhbHNlKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXN1Ym1pdC1idXR0b24tZW5hYmxlci1leHRlbnNpb24uanMiXSwic291cmNlUm9vdCI6IiJ9