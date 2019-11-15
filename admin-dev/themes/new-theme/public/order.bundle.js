window["order"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 377);
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

/***/ 21:
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
 * Class SubmitGridActionExtension handles grid action submits
 */

var SubmitGridActionExtension = function () {
  function SubmitGridActionExtension() {
    var _this = this;

    _classCallCheck(this, SubmitGridActionExtension);

    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }

  _createClass(SubmitGridActionExtension, [{
    key: 'extend',
    value: function extend(grid) {
      var _this2 = this;

      grid.getHeaderContainer().on('click', '.js-grid-action-submit-btn', function (event) {
        _this2.handleSubmit(event, grid);
      });
    }

    /**
     * Handle grid action submit.
     * It uses grid form to submit actions.
     *
     * @param {Event} event
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: 'handleSubmit',
    value: function handleSubmit(event, grid) {
      var $submitBtn = $(event.currentTarget);
      var confirmMessage = $submitBtn.data('confirm-message');

      if (typeof confirmMessage !== "undefined" && 0 < confirmMessage.length && !confirm(confirmMessage)) {
        return;
      }

      var $form = $('#' + grid.getId() + '_filter_form');

      $form.attr('action', $submitBtn.data('url'));
      $form.attr('method', $submitBtn.data('method'));
      $form.find('input[name="' + grid.getId() + '[_token]"]').val($submitBtn.data('csrf'));
      $form.submit();
    }
  }]);

  return SubmitGridActionExtension;
}();

exports.default = SubmitGridActionExtension;

/***/ }),

/***/ 250:
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
 * This extension enables submit functionality of the choice fields in grid.
 *
 * Usage of the extension:
 *
 * const myGrid = new Grid('myGrid');
 * myGrid.addExtension(new ChoiceExtension());
 *
 */

var ChoiceExtension = function () {
  function ChoiceExtension() {
    _classCallCheck(this, ChoiceExtension);

    this.lock = [];
  }

  _createClass(ChoiceExtension, [{
    key: 'extend',
    value: function extend(grid) {
      var _this = this;

      var $choiceOptionsContainer = grid.getContainer().find('table.table .js-choice-options');

      $choiceOptionsContainer.find('.js-dropdown-item').on('click', function (e) {
        e.preventDefault();
        var $button = $(e.currentTarget);
        var $parent = $button.closest('.js-choice-options');
        var url = $parent.data('url');

        _this._submitForm(url, $button);
      });
    }

    /**
     * Submits the form.
     * @param {string} url
     * @param {jQuery} $button
     * @private
     */

  }, {
    key: '_submitForm',
    value: function _submitForm(url, $button) {
      var selectedStatusId = $button.data('value');

      if (this._isLocked(url)) {
        return;
      }

      var $form = $('<form>', {
        action: url,
        method: 'POST'
      }).append($('<input>', {
        name: 'value',
        value: selectedStatusId,
        type: 'hidden'
      }));

      $form.appendTo('body');
      $form.submit();

      this._lock(url);
    }

    /**
     * Checks if current url is being used at the moment.
     *
     * @param url
     * @return {boolean}
     *
     * @private
     */

  }, {
    key: '_isLocked',
    value: function _isLocked(url) {
      return this.lock.includes(url);
    }

    /**
     * Locks the current url so it cant be used twice to execute same request
     * @param url
     * @private
     */

  }, {
    key: '_lock',
    value: function _lock(url) {
      this.lock.push(url);
    }
  }]);

  return ChoiceExtension;
}();

exports.default = ChoiceExtension;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(0)))

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
 * Allows submitting form inside modals.
 * Form must be inside modal, see example structure below:
 *
 * <div class="modal" id="uniqueModalId">
 *  <form data-bulk-inputs-id="bulkInputs">
 *    <div class="d-none">
 *      <div id="bulkInputs" data-prototype="<input type="hidden" name="__name__"/>"></div>
 *    </div>
 *  </form>
 * </div>
 *
 * Note that "data-prototype" is required to add checked items to the form. "__name__"
 * will be replaced with value of bulk checkbox.
 */

var ModalFormSubmitExtension = function () {
  function ModalFormSubmitExtension() {
    _classCallCheck(this, ModalFormSubmitExtension);
  }

  _createClass(ModalFormSubmitExtension, [{
    key: 'extend',
    value: function extend(grid) {
      grid.getContainer().on('click', '.js-bulk-modal-form-submit-btn', function (event) {
        var modalId = $(event.target).data('modal-id');

        var $modal = $('#' + modalId);
        $modal.modal('show');

        $modal.find('.js-submit-modal-form-btn').on('click', function () {
          var $form = $modal.find('form');
          var $bulkInputsBlock = $form.find('#' + $form.data('bulk-inputs-id'));
          var $checkboxes = grid.getContainer().find('.js-bulk-action-checkbox:checked');

          $checkboxes.each(function (i, element) {
            var $checkbox = $(element);

            var input = $bulkInputsBlock.data('prototype').replace(/__name__/g, $checkbox.val());

            var $input = $($.parseHTML(input)[0]);
            $input.val($checkbox.val());

            $form.append($input);
          });

          $form.submit();
        });
      });
    }
  }]);

  return ModalFormSubmitExtension;
}();

exports.default = ModalFormSubmitExtension;

/***/ }),

/***/ 253:
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
 * Extends grid with preview functionality.
 */

var PreviewExtension = function () {
  function PreviewExtension(previewCustomization) {
    _classCallCheck(this, PreviewExtension);

    this.lock = [];
    this.expandSelector = '.js-expand';
    this.collapseSelector = '.js-collapse';
    this.previewOpenClass = 'preview-open';
    this.previewToggleSelector = '.preview-toggle';
    this.previewCustomization = previewCustomization;
  }

  /**
   * Extends provided grid with preview functionality
   *
   * @param grid
   */


  _createClass(PreviewExtension, [{
    key: 'extend',
    value: function extend(grid) {
      var _this = this;

      this.$gridContainer = $(grid.getContainer);

      this.$gridContainer.find('tbody tr').on('mouseover mouseleave', function (event) {
        return _this._handleIconHovering(event);
      });
      this.$gridContainer.find(this.previewToggleSelector).on('click', function (event) {
        return _this._togglePreview(event);
      });

      if (typeof this.previewCustomization === 'function') {
        this.previewCustomization(this.$gridContainer);
      }
    }

    /**
     * Shows/hides preview toggling icons
     *
     * @param event
     * @private
     */

  }, {
    key: '_handleIconHovering',
    value: function _handleIconHovering(event) {
      var $previewToggle = $(event.currentTarget).find(this.previewToggleSelector);

      if (event.type === 'mouseover' && !$(event.currentTarget).hasClass(this.previewOpenClass)) {
        this._showExpandIcon($previewToggle);
      } else {
        this._hideExpandIcon($previewToggle);
      }
    }

    /**
     * Shows/hides preview
     *
     * @param event
     * @private
     */

  }, {
    key: '_togglePreview',
    value: function _togglePreview(event) {
      var _this2 = this;

      var $previewToggle = $(event.currentTarget);
      var $columnRow = $previewToggle.closest('tr');

      if ($columnRow.hasClass(this.previewOpenClass)) {
        $columnRow.next('.preview-row').remove();
        $columnRow.removeClass(this.previewOpenClass);
        this._showExpandIcon($columnRow);
        this._hideCollapseIcon($columnRow);

        return;
      }

      this._closeOpenedPreviews();

      var dataUrl = $(event.currentTarget).data('preview-data-url');

      if (this._isLocked(dataUrl)) {
        return;
      }

      // Prevents loading preview multiple times.
      // Uses "dataUrl" as lock key.
      this._lock(dataUrl);

      $.ajax({
        url: dataUrl,
        method: 'GET',
        dataType: 'json',
        complete: function complete() {
          _this2._unlock(dataUrl);
        }
      }).then(function (response) {
        _this2._renderPreviewContent($columnRow, response.preview);
      }).catch(function (e) {
        showErrorMessage(e.responseJSON.message);
      });
    }

    /**
     * Renders preview content
     *
     * @param $columnRow
     * @param content
     *
     * @private
     */

  }, {
    key: '_renderPreviewContent',
    value: function _renderPreviewContent($columnRow, content) {
      var rowColumnCount = $columnRow.find('td').length;

      var previewTemplate = '\n        <tr class="preview-row">\n          <td colspan="' + rowColumnCount + '">' + content + '</td>\n        </tr>\n      ';

      $columnRow.addClass(this.previewOpenClass);
      this._showCollapseIcon($columnRow);
      this._hideExpandIcon($columnRow);
      $columnRow.after(previewTemplate);
    }

    /**
     * Shows preview expanding icon
     *
     * @param parent
     * @private
     */

  }, {
    key: '_showExpandIcon',
    value: function _showExpandIcon(parent) {
      parent.find(this.expandSelector).removeClass('d-none');
    }

    /**
     * Hides preview expanding icon
     *
     * @param parent
     * @private
     */

  }, {
    key: '_hideExpandIcon',
    value: function _hideExpandIcon(parent) {
      parent.find(this.expandSelector).addClass('d-none');
    }

    /**
     * Shows preview collapsing icon
     *
     * @param parent
     * @private
     */

  }, {
    key: '_showCollapseIcon',
    value: function _showCollapseIcon(parent) {
      parent.find(this.collapseSelector).removeClass('d-none');
    }

    /**
     * Hides preview collapsing icon
     *
     * @param parent
     * @private
     */

  }, {
    key: '_hideCollapseIcon',
    value: function _hideCollapseIcon(parent) {
      parent.find(this.collapseSelector).addClass('d-none');
    }
  }, {
    key: '_isLocked',
    value: function _isLocked(key) {
      return this.lock.indexOf(key) !== -1;
    }
  }, {
    key: '_lock',
    value: function _lock(key) {
      if (this._isLocked(key)) {
        return;
      }

      this.lock.push(key);
    }
  }, {
    key: '_unlock',
    value: function _unlock(key) {
      var index = this.lock.indexOf(key);

      if (index === -1) {
        return;
      }

      this.lock.splice(index, 1);
    }

    /**
     * Close all previews that are open.
     *
     * @private
     */

  }, {
    key: '_closeOpenedPreviews',
    value: function _closeOpenedPreviews() {
      var _this3 = this;

      var $rows = this.$gridContainer.find('.grid-table tbody').find('tr:not(.preview-row)');

      $.each($rows, function (i, row) {
        var $row = $(row);

        if (!$row.hasClass(_this3.previewOpenClass)) {
          return;
        }

        var $previewRow = $row.next();

        if (!$previewRow.hasClass('preview-row')) {
          return;
        }

        $previewRow.remove();
        $row.removeClass(_this3.previewOpenClass);
        _this3._hideCollapseIcon($row);
      });
    }
  }]);

  return PreviewExtension;
}();

exports.default = PreviewExtension;

/***/ }),

/***/ 280:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = previewProductsToggler;
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
 * Toggles hidden products in order preview block.
 *
 * @param {jQuery} $gridContainer
 */
function previewProductsToggler($gridContainer) {
  $gridContainer.on('click', '.js-preview-more-products-btn', function (event) {
    event.preventDefault();

    var $btn = $(event.currentTarget);
    var $hiddenProducts = $btn.closest('tbody').find('.js-product-preview-more');

    $hiddenProducts.removeClass('d-none');
    $btn.closest('tr').remove();
  });
}

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

/***/ 377:
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

var _sortingExtension = __webpack_require__(4);

var _sortingExtension2 = _interopRequireDefault(_sortingExtension);

var _linkRowActionExtension = __webpack_require__(10);

var _linkRowActionExtension2 = _interopRequireDefault(_linkRowActionExtension);

var _submitGridActionExtension = __webpack_require__(21);

var _submitGridActionExtension2 = _interopRequireDefault(_submitGridActionExtension);

var _submitBulkActionExtension = __webpack_require__(9);

var _submitBulkActionExtension2 = _interopRequireDefault(_submitBulkActionExtension);

var _bulkActionCheckboxExtension = __webpack_require__(7);

var _bulkActionCheckboxExtension2 = _interopRequireDefault(_bulkActionCheckboxExtension);

var _filtersSubmitButtonEnablerExtension = __webpack_require__(6);

var _filtersSubmitButtonEnablerExtension2 = _interopRequireDefault(_filtersSubmitButtonEnablerExtension);

var _choiceExtension = __webpack_require__(250);

var _choiceExtension2 = _interopRequireDefault(_choiceExtension);

var _modalFormSubmitExtension = __webpack_require__(252);

var _modalFormSubmitExtension2 = _interopRequireDefault(_modalFormSubmitExtension);

var _previewExtension = __webpack_require__(253);

var _previewExtension2 = _interopRequireDefault(_previewExtension);

var _previewProductsToggler = __webpack_require__(280);

var _previewProductsToggler2 = _interopRequireDefault(_previewProductsToggler);

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
  var orderGrid = new _grid2.default('order');
  orderGrid.addExtension(new _reloadListExtension2.default());
  orderGrid.addExtension(new _exportToSqlManagerExtension2.default());
  orderGrid.addExtension(new _filtersResetExtension2.default());
  orderGrid.addExtension(new _sortingExtension2.default());
  orderGrid.addExtension(new _linkRowActionExtension2.default());
  orderGrid.addExtension(new _submitGridActionExtension2.default());
  orderGrid.addExtension(new _submitBulkActionExtension2.default());
  orderGrid.addExtension(new _bulkActionCheckboxExtension2.default());
  orderGrid.addExtension(new _filtersSubmitButtonEnablerExtension2.default());
  orderGrid.addExtension(new _modalFormSubmitExtension2.default());
  orderGrid.addExtension(new _choiceExtension2.default());
  orderGrid.addExtension(new _previewExtension2.default(_previewProductsToggler2.default));
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

      $filtersRow.find('input, select').on('input dp.change', function () {
        $filtersRow.find('.grid-search-button').prop('disabled', false);
        $filtersRow.find('.js-grid-reset-button').prop('hidden', false);
      });
    }
  }]);

  return FiltersSubmitButtonEnablerExtension;
}();

exports.default = FiltersSubmitButtonEnablerExtension;

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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMWU2NjI2MzkwMGU5NjZkZmJiZjA/ODU5MCoqKioqKioqKioqKioqIiwid2VicGFjazovLy8od2VicGFjaykvYnVpbGRpbi9nbG9iYWwuanM/MzY5OCoqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9saW5rLXJvdy1hY3Rpb24tZXh0ZW5zaW9uLmpzPzM5ZGMqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvdXRpbHMvdGFibGUtc29ydGluZy5qcz8xNWQ0KioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL3V0aWxzL3Jlc2V0X3NlYXJjaC5qcz8xYTdmKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2dyaWQuanM/ODEzYSoqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc3VibWl0LWdyaWQtYWN0aW9uLWV4dGVuc2lvbi5qcz83NWMzKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vY2hvaWNlLWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL21vZGFsLWZvcm0tc3VibWl0LWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3ByZXZpZXctZXh0ZW5zaW9uLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL3ByZXZpZXctcHJvZHVjdHMtdG9nZ2xlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uLmpzPzE2ZjEqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uLmpzPzExM2UqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvbi5qcz9kM2UwKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXN1Ym1pdC1idXR0b24tZW5hYmxlci1leHRlbnNpb24uanM/MDM3OSoqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9idWxrLWFjdGlvbi1jaGVja2JveC1leHRlbnNpb24uanM/YjA5NyoqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZXhwb3J0LXRvLXNxbC1tYW5hZ2VyLWV4dGVuc2lvbi5qcz9lZDJhKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zdWJtaXQtYnVsay1hY3Rpb24tZXh0ZW5zaW9uLmpzPzFiMWYqKioqKioqKiJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwiTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiIsImdyaWQiLCJpbml0Um93TGlua3MiLCJpbml0Q29uZmlybWFibGVBY3Rpb25zIiwiZ2V0Q29udGFpbmVyIiwib24iLCJldmVudCIsImNvbmZpcm1NZXNzYWdlIiwiY3VycmVudFRhcmdldCIsImRhdGEiLCJsZW5ndGgiLCJjb25maXJtIiwicHJldmVudERlZmF1bHQiLCJlYWNoIiwiaW5pdEVhY2hSb3ciLCIkcGFyZW50Um93IiwicHJvcGFnYXRlRmlyc3RMaW5rQWN0aW9uIiwiJHJvd0FjdGlvbiIsIiRwYXJlbnRDZWxsIiwiY2xvc2VzdCIsImNsaWNrYWJsZUNlbGxzIiwibm90IiwiYWRkQ2xhc3MiLCJjbGljayIsImRvY3VtZW50IiwibG9jYXRpb24iLCJhdHRyIiwiZ2xvYmFsIiwiVGFibGVTb3J0aW5nIiwidGFibGUiLCJzZWxlY3RvciIsImNvbHVtbnMiLCJmaW5kIiwiZSIsIiRjb2x1bW4iLCJkZWxlZ2F0ZVRhcmdldCIsIl9zb3J0QnlDb2x1bW4iLCJfZ2V0VG9nZ2xlZFNvcnREaXJlY3Rpb24iLCJjb2x1bW5OYW1lIiwiZGlyZWN0aW9uIiwiaXMiLCJFcnJvciIsImNvbHVtbiIsIl9nZXRVcmwiLCJjb2xOYW1lIiwicHJlZml4IiwidXJsIiwiVVJMIiwiaHJlZiIsInBhcmFtcyIsInNlYXJjaFBhcmFtcyIsInNldCIsInRvU3RyaW5nIiwiaW5pdCIsInJlc2V0U2VhcmNoIiwicmVkaXJlY3RVcmwiLCJwb3N0IiwidGhlbiIsImFzc2lnbiIsIkdyaWQiLCJpZCIsIiRjb250YWluZXIiLCJleHRlbnNpb24iLCJleHRlbmQiLCJTdWJtaXRHcmlkQWN0aW9uRXh0ZW5zaW9uIiwiZ2V0SGVhZGVyQ29udGFpbmVyIiwiaGFuZGxlU3VibWl0IiwiJHN1Ym1pdEJ0biIsIiRmb3JtIiwiZ2V0SWQiLCJ2YWwiLCJzdWJtaXQiLCJDaG9pY2VFeHRlbnNpb24iLCJsb2NrIiwiJGNob2ljZU9wdGlvbnNDb250YWluZXIiLCIkYnV0dG9uIiwiJHBhcmVudCIsIl9zdWJtaXRGb3JtIiwic2VsZWN0ZWRTdGF0dXNJZCIsIl9pc0xvY2tlZCIsImFjdGlvbiIsIm1ldGhvZCIsImFwcGVuZCIsIm5hbWUiLCJ2YWx1ZSIsInR5cGUiLCJhcHBlbmRUbyIsIl9sb2NrIiwiaW5jbHVkZXMiLCJwdXNoIiwiTW9kYWxGb3JtU3VibWl0RXh0ZW5zaW9uIiwibW9kYWxJZCIsInRhcmdldCIsIiRtb2RhbCIsIm1vZGFsIiwiJGJ1bGtJbnB1dHNCbG9jayIsIiRjaGVja2JveGVzIiwiaSIsImVsZW1lbnQiLCIkY2hlY2tib3giLCJpbnB1dCIsInJlcGxhY2UiLCIkaW5wdXQiLCJwYXJzZUhUTUwiLCJQcmV2aWV3RXh0ZW5zaW9uIiwicHJldmlld0N1c3RvbWl6YXRpb24iLCJleHBhbmRTZWxlY3RvciIsImNvbGxhcHNlU2VsZWN0b3IiLCJwcmV2aWV3T3BlbkNsYXNzIiwicHJldmlld1RvZ2dsZVNlbGVjdG9yIiwiJGdyaWRDb250YWluZXIiLCJfaGFuZGxlSWNvbkhvdmVyaW5nIiwiX3RvZ2dsZVByZXZpZXciLCIkcHJldmlld1RvZ2dsZSIsImhhc0NsYXNzIiwiX3Nob3dFeHBhbmRJY29uIiwiX2hpZGVFeHBhbmRJY29uIiwiJGNvbHVtblJvdyIsIm5leHQiLCJyZW1vdmUiLCJyZW1vdmVDbGFzcyIsIl9oaWRlQ29sbGFwc2VJY29uIiwiX2Nsb3NlT3BlbmVkUHJldmlld3MiLCJkYXRhVXJsIiwiYWpheCIsImRhdGFUeXBlIiwiY29tcGxldGUiLCJfdW5sb2NrIiwicmVzcG9uc2UiLCJfcmVuZGVyUHJldmlld0NvbnRlbnQiLCJwcmV2aWV3IiwiY2F0Y2giLCJzaG93RXJyb3JNZXNzYWdlIiwicmVzcG9uc2VKU09OIiwibWVzc2FnZSIsImNvbnRlbnQiLCJyb3dDb2x1bW5Db3VudCIsInByZXZpZXdUZW1wbGF0ZSIsIl9zaG93Q29sbGFwc2VJY29uIiwiYWZ0ZXIiLCJwYXJlbnQiLCJrZXkiLCJpbmRleE9mIiwiaW5kZXgiLCJzcGxpY2UiLCIkcm93cyIsInJvdyIsIiRyb3ciLCIkcHJldmlld1JvdyIsInByZXZpZXdQcm9kdWN0c1RvZ2dsZXIiLCIkYnRuIiwiJGhpZGRlblByb2R1Y3RzIiwiRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIiwib3JkZXJHcmlkIiwiYWRkRXh0ZW5zaW9uIiwiUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbiIsIkV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiIsIlNvcnRpbmdFeHRlbnNpb24iLCJTdWJtaXRHcmlkRXh0ZW5zaW9uIiwiU3VibWl0QnVsa0V4dGVuc2lvbiIsIkJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbiIsIkZpbHRlcnNTdWJtaXRCdXR0b25FbmFibGVyRXh0ZW5zaW9uIiwiJHNvcnRhYmxlVGFibGUiLCJhdHRhY2giLCJSZWxvYWRMaXN0RXh0ZW5zaW9uIiwicmVsb2FkIiwiJGZpbHRlcnNSb3ciLCJwcm9wIiwiX2hhbmRsZUJ1bGtBY3Rpb25DaGVja2JveFNlbGVjdCIsIl9oYW5kbGVCdWxrQWN0aW9uU2VsZWN0QWxsQ2hlY2tib3giLCJpc0NoZWNrZWQiLCJfZW5hYmxlQnVsa0FjdGlvbnNCdG4iLCJfZGlzYWJsZUJ1bGtBY3Rpb25zQnRuIiwiY2hlY2tlZFJvd3NDb3VudCIsIl9vblNob3dTcWxRdWVyeUNsaWNrIiwiX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrIiwiJHNxbE1hbmFnZXJGb3JtIiwiX2ZpbGxFeHBvcnRGb3JtIiwicXVlcnkiLCJfZ2V0TmFtZUZyb21CcmVhZGNydW1iIiwiJGJyZWFkY3J1bWJzIiwiaXRlbSIsIiRicmVhZGNydW1iIiwiYnJlYWRjcnVtYlRpdGxlIiwidGV4dCIsImNvbmNhdCIsIlN1Ym1pdEJ1bGtBY3Rpb25FeHRlbnNpb24iXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7O0FDaEVBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLENBQUM7O0FBRUQ7QUFDQTtBQUNBO0FBQ0EsQ0FBQztBQUNEO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQSw0Q0FBNEM7O0FBRTVDOzs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDcEJBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJB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QUFBQSxlQUFVLE1BQUs4RCxNQUFMLENBQVk5RCxJQUFaLENBQVY7QUFBQTtBQURILEtBQVA7QUFHRDs7QUFFRDs7Ozs7Ozs7OzJCQUtPQSxJLEVBQU07QUFBQTs7QUFDWEEsV0FBS0csWUFBTCxHQUFvQkMsRUFBcEIsQ0FBdUIsT0FBdkIsRUFBZ0MsNEJBQWhDLEVBQThELFVBQUNDLEtBQUQsRUFBVztBQUN2RSxlQUFLaUUsTUFBTCxDQUFZakUsS0FBWixFQUFtQkwsSUFBbkI7QUFDRCxPQUZEO0FBR0Q7O0FBRUQ7Ozs7Ozs7Ozs7OzJCQVFPSyxLLEVBQU9MLEksRUFBTTtBQUNsQixVQUFNa0UsYUFBYXJFLEVBQUVRLE1BQU1FLGFBQVIsQ0FBbkI7QUFDQSxVQUFNRCxpQkFBaUI0RCxXQUFXMUQsSUFBWCxDQUFnQixpQkFBaEIsQ0FBdkI7O0FBRUEsVUFBSSxPQUFPRixjQUFQLEtBQTBCLFdBQTFCLElBQXlDLElBQUlBLGVBQWVHLE1BQTVELElBQXNFLENBQUNDLFFBQVFKLGNBQVIsQ0FBM0UsRUFBb0c7QUFDbEc7QUFDRDs7QUFFRCxVQUFNNkQsUUFBUXRFLEVBQUUsTUFBTUcsS0FBS29FLEtBQUwsRUFBTixHQUFxQixjQUF2QixDQUFkOztBQUVBRCxZQUFNMUMsSUFBTixDQUFXLFFBQVgsRUFBcUJ5QyxXQUFXMUQsSUFBWCxDQUFnQixVQUFoQixDQUFyQjtBQUNBMkQsWUFBTTFDLElBQU4sQ0FBVyxRQUFYLEVBQXFCeUMsV0FBVzFELElBQVgsQ0FBZ0IsYUFBaEIsQ0FBckI7QUFDQTJELFlBQU1HLE1BQU47QUFDRDs7Ozs7O2tCQXZDa0JtSCx5QiIsImZpbGUiOiJvcmRlci5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDM3Nyk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgMWU2NjI2MzkwMGU5NjZkZmJiZjAiLCJ2YXIgZztcclxuXHJcbi8vIFRoaXMgd29ya3MgaW4gbm9uLXN0cmljdCBtb2RlXHJcbmcgPSAoZnVuY3Rpb24oKSB7XHJcblx0cmV0dXJuIHRoaXM7XHJcbn0pKCk7XHJcblxyXG50cnkge1xyXG5cdC8vIFRoaXMgd29ya3MgaWYgZXZhbCBpcyBhbGxvd2VkIChzZWUgQ1NQKVxyXG5cdGcgPSBnIHx8IEZ1bmN0aW9uKFwicmV0dXJuIHRoaXNcIikoKSB8fCAoMSxldmFsKShcInRoaXNcIik7XHJcbn0gY2F0Y2goZSkge1xyXG5cdC8vIFRoaXMgd29ya3MgaWYgdGhlIHdpbmRvdyByZWZlcmVuY2UgaXMgYXZhaWxhYmxlXHJcblx0aWYodHlwZW9mIHdpbmRvdyA9PT0gXCJvYmplY3RcIilcclxuXHRcdGcgPSB3aW5kb3c7XHJcbn1cclxuXHJcbi8vIGcgY2FuIHN0aWxsIGJlIHVuZGVmaW5lZCwgYnV0IG5vdGhpbmcgdG8gZG8gYWJvdXQgaXQuLi5cclxuLy8gV2UgcmV0dXJuIHVuZGVmaW5lZCwgaW5zdGVhZCBvZiBub3RoaW5nIGhlcmUsIHNvIGl0J3NcclxuLy8gZWFzaWVyIHRvIGhhbmRsZSB0aGlzIGNhc2UuIGlmKCFnbG9iYWwpIHsgLi4ufVxyXG5cclxubW9kdWxlLmV4cG9ydHMgPSBnO1xyXG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAod2VicGFjaykvYnVpbGRpbi9nbG9iYWwuanNcbi8vIG1vZHVsZSBpZCA9IDBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDM0IDM3IDQ1IDQ5IiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIExpbmtSb3dBY3Rpb25FeHRlbnNpb24gaGFuZGxlcyBsaW5rIHJvdyBhY3Rpb25zXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIExpbmtSb3dBY3Rpb25FeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIHRoaXMuaW5pdFJvd0xpbmtzKGdyaWQpO1xuICAgIHRoaXMuaW5pdENvbmZpcm1hYmxlQWN0aW9ucyhncmlkKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGluaXRDb25maXJtYWJsZUFjdGlvbnMoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1saW5rLXJvdy1hY3Rpb24nLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcblxuICAgICAgaWYgKGNvbmZpcm1NZXNzYWdlLmxlbmd0aCAmJiAhY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBZGQgYSBjbGljayBldmVudCBvbiByb3dzIHRoYXQgbWF0Y2hlcyB0aGUgZmlyc3QgbGluayBhY3Rpb24gKGlmIHByZXNlbnQpXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgaW5pdFJvd0xpbmtzKGdyaWQpIHtcbiAgICAkKCd0cicsIGdyaWQuZ2V0Q29udGFpbmVyKCkpLmVhY2goZnVuY3Rpb24gaW5pdEVhY2hSb3coKSB7XG4gICAgICBjb25zdCAkcGFyZW50Um93ID0gJCh0aGlzKTtcblxuICAgICAgJCgnLmpzLWxpbmstcm93LWFjdGlvbltkYXRhLWNsaWNrYWJsZS1yb3c9MV06Zmlyc3QnLCAkcGFyZW50Um93KS5lYWNoKGZ1bmN0aW9uIHByb3BhZ2F0ZUZpcnN0TGlua0FjdGlvbigpIHtcbiAgICAgICAgY29uc3QgJHJvd0FjdGlvbiA9ICQodGhpcyk7XG4gICAgICAgIGNvbnN0ICRwYXJlbnRDZWxsID0gJHJvd0FjdGlvbi5jbG9zZXN0KCd0ZCcpO1xuXG4gICAgICAgIC8qXG4gICAgICAgICAqIE9ubHkgc2VhcmNoIGZvciBjZWxscyB3aXRoIG5vbiBjbGlja2FibGUgY29udGVudHMgdG8gYXZvaWQgY29uZmxpY3RzIHdpdGhcbiAgICAgICAgICogcHJldmlvdXMgY2VsbCBiZWhhdmlvdXIgKGFjdGlvbiwgdG9nZ2xlLCAuLi4pXG4gICAgICAgICAqL1xuICAgICAgICBjb25zdCBjbGlja2FibGVDZWxscyA9ICQoJ3RkLmRhdGEtdHlwZSwgdGQuaWRlbnRpZmllci10eXBlOm5vdCg6aGFzKGlucHV0KSksIHRkLmJhZGdlLXR5cGUsIHRkLnBvc2l0aW9uLXR5cGUnLCAkcGFyZW50Um93KVxuICAgICAgICAgIC5ub3QoJHBhcmVudENlbGwpXG4gICAgICAgIDtcblxuICAgICAgICBjbGlja2FibGVDZWxscy5hZGRDbGFzcygnY3Vyc29yLXBvaW50ZXInKS5jbGljaygoKSA9PiB7XG4gICAgICAgICAgY29uc3QgY29uZmlybU1lc3NhZ2UgPSAkcm93QWN0aW9uLmRhdGEoJ2NvbmZpcm0tbWVzc2FnZScpO1xuXG4gICAgICAgICAgaWYgKCFjb25maXJtTWVzc2FnZS5sZW5ndGggfHwgY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgICAgICAgIGRvY3VtZW50LmxvY2F0aW9uID0gJHJvd0FjdGlvbi5hdHRyKCdocmVmJyk7XG4gICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICAgIH0pO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2xpbmstcm93LWFjdGlvbi1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gZ2xvYmFsLiQ7XG5cbi8qKlxuICogTWFrZXMgYSB0YWJsZSBzb3J0YWJsZSBieSBjb2x1bW5zLlxuICogVGhpcyBmb3JjZXMgYSBwYWdlIHJlbG9hZCB3aXRoIG1vcmUgcXVlcnkgcGFyYW1ldGVycy5cbiAqL1xuY2xhc3MgVGFibGVTb3J0aW5nIHtcblxuICAvKipcbiAgICogQHBhcmFtIHtqUXVlcnl9IHRhYmxlXG4gICAqL1xuICBjb25zdHJ1Y3Rvcih0YWJsZSkge1xuICAgIHRoaXMuc2VsZWN0b3IgPSAnLnBzLXNvcnRhYmxlLWNvbHVtbic7XG4gICAgdGhpcy5jb2x1bW5zID0gJCh0YWJsZSkuZmluZCh0aGlzLnNlbGVjdG9yKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBdHRhY2hlcyB0aGUgbGlzdGVuZXJzXG4gICAqL1xuICBhdHRhY2goKSB7XG4gICAgdGhpcy5jb2x1bW5zLm9uKCdjbGljaycsIChlKSA9PiB7XG4gICAgICBjb25zdCAkY29sdW1uID0gJChlLmRlbGVnYXRlVGFyZ2V0KTtcbiAgICAgIHRoaXMuX3NvcnRCeUNvbHVtbigkY29sdW1uLCB0aGlzLl9nZXRUb2dnbGVkU29ydERpcmVjdGlvbigkY29sdW1uKSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogU29ydCB1c2luZyBhIGNvbHVtbiBuYW1lXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBjb2x1bW5OYW1lXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBkaXJlY3Rpb24gXCJhc2NcIiBvciBcImRlc2NcIlxuICAgKi9cbiAgc29ydEJ5KGNvbHVtbk5hbWUsIGRpcmVjdGlvbikge1xuICAgIGNvbnN0ICRjb2x1bW4gPSB0aGlzLmNvbHVtbnMuaXMoYFtkYXRhLXNvcnQtY29sLW5hbWU9XCIke2NvbHVtbk5hbWV9XCJdYCk7XG4gICAgaWYgKCEkY29sdW1uKSB7XG4gICAgICB0aHJvdyBuZXcgRXJyb3IoYENhbm5vdCBzb3J0IGJ5IFwiJHtjb2x1bW5OYW1lfVwiOiBpbnZhbGlkIGNvbHVtbmApO1xuICAgIH1cblxuICAgIHRoaXMuX3NvcnRCeUNvbHVtbigkY29sdW1uLCBkaXJlY3Rpb24pO1xuICB9XG5cbiAgLyoqXG4gICAqIFNvcnQgdXNpbmcgYSBjb2x1bW4gZWxlbWVudFxuICAgKiBAcGFyYW0ge2pRdWVyeX0gY29sdW1uXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBkaXJlY3Rpb24gXCJhc2NcIiBvciBcImRlc2NcIlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3NvcnRCeUNvbHVtbihjb2x1bW4sIGRpcmVjdGlvbikge1xuICAgIHdpbmRvdy5sb2NhdGlvbiA9IHRoaXMuX2dldFVybChjb2x1bW4uZGF0YSgnc29ydENvbE5hbWUnKSwgKGRpcmVjdGlvbiA9PT0gJ2Rlc2MnKSA/ICdkZXNjJyA6ICdhc2MnLCBjb2x1bW4uZGF0YSgnc29ydFByZWZpeCcpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXR1cm5zIHRoZSBpbnZlcnRlZCBkaXJlY3Rpb24gdG8gc29ydCBhY2NvcmRpbmcgdG8gdGhlIGNvbHVtbidzIGN1cnJlbnQgb25lXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSBjb2x1bW5cbiAgICogQHJldHVybiB7c3RyaW5nfVxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldFRvZ2dsZWRTb3J0RGlyZWN0aW9uKGNvbHVtbikge1xuICAgIHJldHVybiBjb2x1bW4uZGF0YSgnc29ydERpcmVjdGlvbicpID09PSAnYXNjJyA/ICdkZXNjJyA6ICdhc2MnO1xuICB9XG5cbiAgLyoqXG4gICAqIFJldHVybnMgdGhlIHVybCBmb3IgdGhlIHNvcnRlZCB0YWJsZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gY29sTmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBwcmVmaXhcbiAgICogQHJldHVybiB7c3RyaW5nfVxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldFVybChjb2xOYW1lLCBkaXJlY3Rpb24sIHByZWZpeCkge1xuICAgIGNvbnN0IHVybCA9IG5ldyBVUkwod2luZG93LmxvY2F0aW9uLmhyZWYpO1xuICAgIGNvbnN0IHBhcmFtcyA9IHVybC5zZWFyY2hQYXJhbXM7XG5cbiAgICBpZiAocHJlZml4KSB7XG4gICAgICBwYXJhbXMuc2V0KHByZWZpeCsnW29yZGVyQnldJywgY29sTmFtZSk7XG4gICAgICBwYXJhbXMuc2V0KHByZWZpeCsnW3NvcnRPcmRlcl0nLCBkaXJlY3Rpb24pO1xuICAgIH0gZWxzZSB7XG4gICAgICBwYXJhbXMuc2V0KCdvcmRlckJ5JywgY29sTmFtZSk7XG4gICAgICBwYXJhbXMuc2V0KCdzb3J0T3JkZXInLCBkaXJlY3Rpb24pO1xuICAgIH1cblxuICAgIHJldHVybiB1cmwudG9TdHJpbmcoKTtcbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBUYWJsZVNvcnRpbmc7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9hcHAvdXRpbHMvdGFibGUtc29ydGluZy5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbi8qKlxuICogU2VuZCBhIFBvc3QgUmVxdWVzdCB0byByZXNldCBzZWFyY2ggQWN0aW9uLlxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuY29uc3QgaW5pdCA9IGZ1bmN0aW9uIHJlc2V0U2VhcmNoKHVybCwgcmVkaXJlY3RVcmwpIHtcbiAgICAkLnBvc3QodXJsKS50aGVuKCgpID0+IHdpbmRvdy5sb2NhdGlvbi5hc3NpZ24ocmVkaXJlY3RVcmwpKTtcbn07XG5cbmV4cG9ydCBkZWZhdWx0IGluaXQ7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9hcHAvdXRpbHMvcmVzZXRfc2VhcmNoLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIGlzIHJlc3BvbnNpYmxlIGZvciBoYW5kbGluZyBHcmlkIGV2ZW50c1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBHcmlkIHtcbiAgLyoqXG4gICAqIEdyaWQgaWRcbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IGlkXG4gICAqL1xuICBjb25zdHJ1Y3RvcihpZCkge1xuICAgIHRoaXMuaWQgPSBpZDtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKCcjJyArIHRoaXMuaWQgKyAnX2dyaWQnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZ3JpZCBpZFxuICAgKlxuICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgKi9cbiAgZ2V0SWQoKSB7XG4gICAgcmV0dXJuIHRoaXMuaWQ7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGdyaWQgY29udGFpbmVyXG4gICAqXG4gICAqIEByZXR1cm5zIHtqUXVlcnl9XG4gICAqL1xuICBnZXRDb250YWluZXIoKSB7XG4gICAgcmV0dXJuIHRoaXMuJGNvbnRhaW5lcjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZ3JpZCBoZWFkZXIgY29udGFpbmVyXG4gICAqXG4gICAqIEByZXR1cm5zIHtqUXVlcnl9XG4gICAqL1xuICBnZXRIZWFkZXJDb250YWluZXIoKSB7XG4gICAgcmV0dXJuIHRoaXMuJGNvbnRhaW5lci5jbG9zZXN0KCcuanMtZ3JpZC1wYW5lbCcpLmZpbmQoJy5qcy1ncmlkLWhlYWRlcicpO1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkIHdpdGggZXh0ZXJuYWwgZXh0ZW5zaW9uc1xuICAgKlxuICAgKiBAcGFyYW0ge29iamVjdH0gZXh0ZW5zaW9uXG4gICAqL1xuICBhZGRFeHRlbnNpb24oZXh0ZW5zaW9uKSB7XG4gICAgZXh0ZW5zaW9uLmV4dGVuZCh0aGlzKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2dyaWQuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgU3VibWl0R3JpZEFjdGlvbkV4dGVuc2lvbiBoYW5kbGVzIGdyaWQgYWN0aW9uIHN1Ym1pdHNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU3VibWl0R3JpZEFjdGlvbkV4dGVuc2lvbiB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHJldHVybiB7XG4gICAgICBleHRlbmQ6IChncmlkKSA9PiB0aGlzLmV4dGVuZChncmlkKVxuICAgIH07XG4gIH1cblxuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0SGVhZGVyQ29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1ncmlkLWFjdGlvbi1zdWJtaXQtYnRuJywgKGV2ZW50KSA9PiB7XG4gICAgICB0aGlzLmhhbmRsZVN1Ym1pdChldmVudCwgZ3JpZCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlIGdyaWQgYWN0aW9uIHN1Ym1pdC5cbiAgICogSXQgdXNlcyBncmlkIGZvcm0gdG8gc3VibWl0IGFjdGlvbnMuXG4gICAqXG4gICAqIEBwYXJhbSB7RXZlbnR9IGV2ZW50XG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgaGFuZGxlU3VibWl0KGV2ZW50LCBncmlkKSB7XG4gICAgY29uc3QgJHN1Ym1pdEJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgY29uc3QgY29uZmlybU1lc3NhZ2UgPSAkc3VibWl0QnRuLmRhdGEoJ2NvbmZpcm0tbWVzc2FnZScpO1xuXG4gICAgaWYgKHR5cGVvZiBjb25maXJtTWVzc2FnZSAhPT0gXCJ1bmRlZmluZWRcIiAmJiAwIDwgY29uZmlybU1lc3NhZ2UubGVuZ3RoICYmICFjb25maXJtKGNvbmZpcm1NZXNzYWdlKSkge1xuICAgICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgY29uc3QgJGZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfZmlsdGVyX2Zvcm0nKTtcblxuICAgICRmb3JtLmF0dHIoJ2FjdGlvbicsICRzdWJtaXRCdG4uZGF0YSgndXJsJykpO1xuICAgICRmb3JtLmF0dHIoJ21ldGhvZCcsICRzdWJtaXRCdG4uZGF0YSgnbWV0aG9kJykpO1xuICAgICRmb3JtLmZpbmQoJ2lucHV0W25hbWU9XCInICsgZ3JpZC5nZXRJZCgpICsgJ1tfdG9rZW5dXCJdJykudmFsKCRzdWJtaXRCdG4uZGF0YSgnY3NyZicpKTtcbiAgICAkZm9ybS5zdWJtaXQoKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zdWJtaXQtZ3JpZC1hY3Rpb24tZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG4vKipcbiAqIFRoaXMgZXh0ZW5zaW9uIGVuYWJsZXMgc3VibWl0IGZ1bmN0aW9uYWxpdHkgb2YgdGhlIGNob2ljZSBmaWVsZHMgaW4gZ3JpZC5cbiAqXG4gKiBVc2FnZSBvZiB0aGUgZXh0ZW5zaW9uOlxuICpcbiAqIGNvbnN0IG15R3JpZCA9IG5ldyBHcmlkKCdteUdyaWQnKTtcbiAqIG15R3JpZC5hZGRFeHRlbnNpb24obmV3IENob2ljZUV4dGVuc2lvbigpKTtcbiAqXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENob2ljZUV4dGVuc2lvbiB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMubG9jayA9IFtdO1xuICB9XG5cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBjb25zdCAkY2hvaWNlT3B0aW9uc0NvbnRhaW5lciA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgndGFibGUudGFibGUgLmpzLWNob2ljZS1vcHRpb25zJyk7XG5cbiAgICAkY2hvaWNlT3B0aW9uc0NvbnRhaW5lci5maW5kKCcuanMtZHJvcGRvd24taXRlbScpLm9uKCdjbGljaycsIChlKSA9PiB7XG4gICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBjb25zdCAkYnV0dG9uID0gJChlLmN1cnJlbnRUYXJnZXQpO1xuICAgICAgY29uc3QgJHBhcmVudCA9ICRidXR0b24uY2xvc2VzdCgnLmpzLWNob2ljZS1vcHRpb25zJyk7XG4gICAgICBjb25zdCB1cmwgPSAkcGFyZW50LmRhdGEoJ3VybCcpO1xuXG4gICAgICB0aGlzLl9zdWJtaXRGb3JtKHVybCwgJGJ1dHRvbik7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogU3VibWl0cyB0aGUgZm9ybS5cbiAgICogQHBhcmFtIHtzdHJpbmd9IHVybFxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGJ1dHRvblxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3N1Ym1pdEZvcm0odXJsLCAkYnV0dG9uKSB7XG4gICAgY29uc3Qgc2VsZWN0ZWRTdGF0dXNJZCA9ICRidXR0b24uZGF0YSgndmFsdWUnKTtcblxuICAgIGlmICh0aGlzLl9pc0xvY2tlZCh1cmwpKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgY29uc3QgJGZvcm0gPSAkKCc8Zm9ybT4nLCB7XG4gICAgICBhY3Rpb246IHVybCxcbiAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgIH0pLmFwcGVuZChcbiAgICAgICQoJzxpbnB1dD4nLCB7XG4gICAgICAgIG5hbWU6ICd2YWx1ZScsXG4gICAgICAgIHZhbHVlOiBzZWxlY3RlZFN0YXR1c0lkLFxuICAgICAgICB0eXBlOiAnaGlkZGVuJyxcbiAgICAgIH0pKTtcblxuICAgICRmb3JtLmFwcGVuZFRvKCdib2R5Jyk7XG4gICAgJGZvcm0uc3VibWl0KCk7XG5cbiAgICB0aGlzLl9sb2NrKHVybCk7XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2tzIGlmIGN1cnJlbnQgdXJsIGlzIGJlaW5nIHVzZWQgYXQgdGhlIG1vbWVudC5cbiAgICpcbiAgICogQHBhcmFtIHVybFxuICAgKiBAcmV0dXJuIHtib29sZWFufVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2lzTG9ja2VkKHVybCkge1xuICAgIHJldHVybiB0aGlzLmxvY2suaW5jbHVkZXModXJsKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMb2NrcyB0aGUgY3VycmVudCB1cmwgc28gaXQgY2FudCBiZSB1c2VkIHR3aWNlIHRvIGV4ZWN1dGUgc2FtZSByZXF1ZXN0XG4gICAqIEBwYXJhbSB1cmxcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9sb2NrKHVybCkge1xuICAgIHRoaXMubG9jay5wdXNoKHVybCk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vY2hvaWNlLWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBBbGxvd3Mgc3VibWl0dGluZyBmb3JtIGluc2lkZSBtb2RhbHMuXG4gKiBGb3JtIG11c3QgYmUgaW5zaWRlIG1vZGFsLCBzZWUgZXhhbXBsZSBzdHJ1Y3R1cmUgYmVsb3c6XG4gKlxuICogPGRpdiBjbGFzcz1cIm1vZGFsXCIgaWQ9XCJ1bmlxdWVNb2RhbElkXCI+XG4gKiAgPGZvcm0gZGF0YS1idWxrLWlucHV0cy1pZD1cImJ1bGtJbnB1dHNcIj5cbiAqICAgIDxkaXYgY2xhc3M9XCJkLW5vbmVcIj5cbiAqICAgICAgPGRpdiBpZD1cImJ1bGtJbnB1dHNcIiBkYXRhLXByb3RvdHlwZT1cIjxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cIl9fbmFtZV9fXCIvPlwiPjwvZGl2PlxuICogICAgPC9kaXY+XG4gKiAgPC9mb3JtPlxuICogPC9kaXY+XG4gKlxuICogTm90ZSB0aGF0IFwiZGF0YS1wcm90b3R5cGVcIiBpcyByZXF1aXJlZCB0byBhZGQgY2hlY2tlZCBpdGVtcyB0byB0aGUgZm9ybS4gXCJfX25hbWVfX1wiXG4gKiB3aWxsIGJlIHJlcGxhY2VkIHdpdGggdmFsdWUgb2YgYnVsayBjaGVja2JveC5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgTW9kYWxGb3JtU3VibWl0RXh0ZW5zaW9uIHtcbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtYnVsay1tb2RhbC1mb3JtLXN1Ym1pdC1idG4nLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0IG1vZGFsSWQgPSAkKGV2ZW50LnRhcmdldCkuZGF0YSgnbW9kYWwtaWQnKTtcblxuICAgICAgY29uc3QgJG1vZGFsID0gJChgIyR7bW9kYWxJZH1gKTtcbiAgICAgICRtb2RhbC5tb2RhbCgnc2hvdycpO1xuXG4gICAgICAkbW9kYWwuZmluZCgnLmpzLXN1Ym1pdC1tb2RhbC1mb3JtLWJ0bicpLm9uKCdjbGljaycsICgpID0+IHtcbiAgICAgICAgY29uc3QgJGZvcm0gPSAkbW9kYWwuZmluZCgnZm9ybScpO1xuICAgICAgICBjb25zdCAkYnVsa0lucHV0c0Jsb2NrID0gJGZvcm0uZmluZChgIyR7JGZvcm0uZGF0YSgnYnVsay1pbnB1dHMtaWQnKX1gKTtcbiAgICAgICAgY29uc3QgJGNoZWNrYm94ZXMgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbi1jaGVja2JveDpjaGVja2VkJyk7XG5cbiAgICAgICAgJGNoZWNrYm94ZXMuZWFjaCgoaSwgZWxlbWVudCkgPT4ge1xuICAgICAgICAgIGNvbnN0ICRjaGVja2JveCA9ICQoZWxlbWVudCk7XG5cbiAgICAgICAgICBjb25zdCBpbnB1dCA9ICRidWxrSW5wdXRzQmxvY2tcbiAgICAgICAgICAgIC5kYXRhKCdwcm90b3R5cGUnKVxuICAgICAgICAgICAgLnJlcGxhY2UoL19fbmFtZV9fL2csICRjaGVja2JveC52YWwoKSk7XG5cbiAgICAgICAgICBjb25zdCAkaW5wdXQgPSAkKCQucGFyc2VIVE1MKGlucHV0KVswXSk7XG4gICAgICAgICAgJGlucHV0LnZhbCgkY2hlY2tib3gudmFsKCkpO1xuXG4gICAgICAgICAgJGZvcm0uYXBwZW5kKCRpbnB1dCk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgICRmb3JtLnN1Ym1pdCgpO1xuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbW9kYWwtZm9ybS1zdWJtaXQtZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIEV4dGVuZHMgZ3JpZCB3aXRoIHByZXZpZXcgZnVuY3Rpb25hbGl0eS5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUHJldmlld0V4dGVuc2lvbiB7XG4gIGNvbnN0cnVjdG9yKHByZXZpZXdDdXN0b21pemF0aW9uKSB7XG4gICAgdGhpcy5sb2NrID0gW107XG4gICAgdGhpcy5leHBhbmRTZWxlY3RvciA9ICcuanMtZXhwYW5kJztcbiAgICB0aGlzLmNvbGxhcHNlU2VsZWN0b3IgPSAnLmpzLWNvbGxhcHNlJztcbiAgICB0aGlzLnByZXZpZXdPcGVuQ2xhc3MgPSAncHJldmlldy1vcGVuJztcbiAgICB0aGlzLnByZXZpZXdUb2dnbGVTZWxlY3RvciA9ICcucHJldmlldy10b2dnbGUnO1xuICAgIHRoaXMucHJldmlld0N1c3RvbWl6YXRpb24gPSBwcmV2aWV3Q3VzdG9taXphdGlvbjtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmRzIHByb3ZpZGVkIGdyaWQgd2l0aCBwcmV2aWV3IGZ1bmN0aW9uYWxpdHlcbiAgICpcbiAgICogQHBhcmFtIGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgdGhpcy4kZ3JpZENvbnRhaW5lciA9ICQoZ3JpZC5nZXRDb250YWluZXIpO1xuXG4gICAgdGhpcy4kZ3JpZENvbnRhaW5lci5maW5kKCd0Ym9keSB0cicpLm9uKCdtb3VzZW92ZXIgbW91c2VsZWF2ZScsIGV2ZW50ID0+IHRoaXMuX2hhbmRsZUljb25Ib3ZlcmluZyhldmVudCkpO1xuICAgIHRoaXMuJGdyaWRDb250YWluZXIuZmluZCh0aGlzLnByZXZpZXdUb2dnbGVTZWxlY3Rvcikub24oJ2NsaWNrJywgZXZlbnQgPT4gdGhpcy5fdG9nZ2xlUHJldmlldyhldmVudCkpO1xuXG4gICAgaWYgKHR5cGVvZiB0aGlzLnByZXZpZXdDdXN0b21pemF0aW9uID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICB0aGlzLnByZXZpZXdDdXN0b21pemF0aW9uKHRoaXMuJGdyaWRDb250YWluZXIpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cy9oaWRlcyBwcmV2aWV3IHRvZ2dsaW5nIGljb25zXG4gICAqXG4gICAqIEBwYXJhbSBldmVudFxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hhbmRsZUljb25Ib3ZlcmluZyhldmVudCkge1xuICAgIGNvbnN0ICRwcmV2aWV3VG9nZ2xlID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5maW5kKHRoaXMucHJldmlld1RvZ2dsZVNlbGVjdG9yKTtcblxuICAgIGlmIChldmVudC50eXBlID09PSAnbW91c2VvdmVyJyAmJiAhJChldmVudC5jdXJyZW50VGFyZ2V0KS5oYXNDbGFzcyh0aGlzLnByZXZpZXdPcGVuQ2xhc3MpKSB7XG4gICAgICB0aGlzLl9zaG93RXhwYW5kSWNvbigkcHJldmlld1RvZ2dsZSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMuX2hpZGVFeHBhbmRJY29uKCRwcmV2aWV3VG9nZ2xlKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogU2hvd3MvaGlkZXMgcHJldmlld1xuICAgKlxuICAgKiBAcGFyYW0gZXZlbnRcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGVQcmV2aWV3KGV2ZW50KSB7XG4gICAgY29uc3QgJHByZXZpZXdUb2dnbGUgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgIGNvbnN0ICRjb2x1bW5Sb3cgPSAkcHJldmlld1RvZ2dsZS5jbG9zZXN0KCd0cicpO1xuXG4gICAgaWYgKCRjb2x1bW5Sb3cuaGFzQ2xhc3ModGhpcy5wcmV2aWV3T3BlbkNsYXNzKSkge1xuICAgICAgJGNvbHVtblJvdy5uZXh0KCcucHJldmlldy1yb3cnKS5yZW1vdmUoKTtcbiAgICAgICRjb2x1bW5Sb3cucmVtb3ZlQ2xhc3ModGhpcy5wcmV2aWV3T3BlbkNsYXNzKTtcbiAgICAgIHRoaXMuX3Nob3dFeHBhbmRJY29uKCRjb2x1bW5Sb3cpO1xuICAgICAgdGhpcy5faGlkZUNvbGxhcHNlSWNvbigkY29sdW1uUm93KTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIHRoaXMuX2Nsb3NlT3BlbmVkUHJldmlld3MoKTtcblxuICAgIGNvbnN0IGRhdGFVcmwgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ3ByZXZpZXctZGF0YS11cmwnKTtcblxuICAgIGlmICh0aGlzLl9pc0xvY2tlZChkYXRhVXJsKSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIC8vIFByZXZlbnRzIGxvYWRpbmcgcHJldmlldyBtdWx0aXBsZSB0aW1lcy5cbiAgICAvLyBVc2VzIFwiZGF0YVVybFwiIGFzIGxvY2sga2V5LlxuICAgIHRoaXMuX2xvY2soZGF0YVVybCk7XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdXJsOiBkYXRhVXJsLFxuICAgICAgbWV0aG9kOiAnR0VUJyxcbiAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICBjb21wbGV0ZTogKCkgPT4ge1xuICAgICAgICB0aGlzLl91bmxvY2soZGF0YVVybCk7XG4gICAgICB9LFxuICAgIH0pLnRoZW4oKHJlc3BvbnNlKSA9PiB7XG4gICAgICB0aGlzLl9yZW5kZXJQcmV2aWV3Q29udGVudCgkY29sdW1uUm93LCByZXNwb25zZS5wcmV2aWV3KTtcbiAgICB9KS5jYXRjaCgoZSkgPT4ge1xuICAgICAgc2hvd0Vycm9yTWVzc2FnZShlLnJlc3BvbnNlSlNPTi5tZXNzYWdlKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIHByZXZpZXcgY29udGVudFxuICAgKlxuICAgKiBAcGFyYW0gJGNvbHVtblJvd1xuICAgKiBAcGFyYW0gY29udGVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbmRlclByZXZpZXdDb250ZW50KCRjb2x1bW5Sb3csIGNvbnRlbnQpIHtcbiAgICBjb25zdCByb3dDb2x1bW5Db3VudCA9ICRjb2x1bW5Sb3cuZmluZCgndGQnKS5sZW5ndGg7XG5cbiAgICBjb25zdCBwcmV2aWV3VGVtcGxhdGUgPSBgXG4gICAgICAgIDx0ciBjbGFzcz1cInByZXZpZXctcm93XCI+XG4gICAgICAgICAgPHRkIGNvbHNwYW49XCIke3Jvd0NvbHVtbkNvdW50fVwiPiR7Y29udGVudH08L3RkPlxuICAgICAgICA8L3RyPlxuICAgICAgYDtcblxuICAgICRjb2x1bW5Sb3cuYWRkQ2xhc3ModGhpcy5wcmV2aWV3T3BlbkNsYXNzKTtcbiAgICB0aGlzLl9zaG93Q29sbGFwc2VJY29uKCRjb2x1bW5Sb3cpO1xuICAgIHRoaXMuX2hpZGVFeHBhbmRJY29uKCRjb2x1bW5Sb3cpO1xuICAgICRjb2x1bW5Sb3cuYWZ0ZXIocHJldmlld1RlbXBsYXRlKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyBwcmV2aWV3IGV4cGFuZGluZyBpY29uXG4gICAqXG4gICAqIEBwYXJhbSBwYXJlbnRcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93RXhwYW5kSWNvbihwYXJlbnQpIHtcbiAgICBwYXJlbnQuZmluZCh0aGlzLmV4cGFuZFNlbGVjdG9yKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgcHJldmlldyBleHBhbmRpbmcgaWNvblxuICAgKlxuICAgKiBAcGFyYW0gcGFyZW50XG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZUV4cGFuZEljb24ocGFyZW50KSB7XG4gICAgcGFyZW50LmZpbmQodGhpcy5leHBhbmRTZWxlY3RvcikuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzIHByZXZpZXcgY29sbGFwc2luZyBpY29uXG4gICAqXG4gICAqIEBwYXJhbSBwYXJlbnRcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93Q29sbGFwc2VJY29uKHBhcmVudCkge1xuICAgIHBhcmVudC5maW5kKHRoaXMuY29sbGFwc2VTZWxlY3RvcikucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIHByZXZpZXcgY29sbGFwc2luZyBpY29uXG4gICAqXG4gICAqIEBwYXJhbSBwYXJlbnRcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlQ29sbGFwc2VJY29uKHBhcmVudCkge1xuICAgIHBhcmVudC5maW5kKHRoaXMuY29sbGFwc2VTZWxlY3RvcikuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgX2lzTG9ja2VkKGtleSkge1xuICAgIHJldHVybiB0aGlzLmxvY2suaW5kZXhPZihrZXkpICE9PSAtMTtcbiAgfVxuXG4gIF9sb2NrKGtleSkge1xuICAgIGlmICh0aGlzLl9pc0xvY2tlZChrZXkpKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgdGhpcy5sb2NrLnB1c2goa2V5KTtcbiAgfVxuXG4gIF91bmxvY2soa2V5KSB7XG4gICAgY29uc3QgaW5kZXggPSB0aGlzLmxvY2suaW5kZXhPZihrZXkpO1xuXG4gICAgaWYgKGluZGV4ID09PSAtMSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIHRoaXMubG9jay5zcGxpY2UoaW5kZXgsIDEpO1xuICB9XG5cbiAgLyoqXG4gICAqIENsb3NlIGFsbCBwcmV2aWV3cyB0aGF0IGFyZSBvcGVuLlxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2Nsb3NlT3BlbmVkUHJldmlld3MoKSB7XG4gICAgY29uc3QgJHJvd3MgPSB0aGlzLiRncmlkQ29udGFpbmVyLmZpbmQoJy5ncmlkLXRhYmxlIHRib2R5JykuZmluZCgndHI6bm90KC5wcmV2aWV3LXJvdyknKTtcblxuICAgICQuZWFjaCgkcm93cywgKGksIHJvdykgPT4ge1xuICAgICAgY29uc3QgJHJvdyA9ICQocm93KTtcblxuICAgICAgaWYgKCEkcm93Lmhhc0NsYXNzKHRoaXMucHJldmlld09wZW5DbGFzcykpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBjb25zdCAkcHJldmlld1JvdyA9ICRyb3cubmV4dCgpO1xuXG4gICAgICBpZiAoISRwcmV2aWV3Um93Lmhhc0NsYXNzKCdwcmV2aWV3LXJvdycpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgJHByZXZpZXdSb3cucmVtb3ZlKCk7XG4gICAgICAkcm93LnJlbW92ZUNsYXNzKHRoaXMucHJldmlld09wZW5DbGFzcyk7XG4gICAgICB0aGlzLl9oaWRlQ29sbGFwc2VJY29uKCRyb3cpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3ByZXZpZXctZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFRvZ2dsZXMgaGlkZGVuIHByb2R1Y3RzIGluIG9yZGVyIHByZXZpZXcgYmxvY2suXG4gKlxuICogQHBhcmFtIHtqUXVlcnl9ICRncmlkQ29udGFpbmVyXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIHByZXZpZXdQcm9kdWN0c1RvZ2dsZXIoJGdyaWRDb250YWluZXIpIHtcbiAgJGdyaWRDb250YWluZXIub24oJ2NsaWNrJywgJy5qcy1wcmV2aWV3LW1vcmUtcHJvZHVjdHMtYnRuJywgKGV2ZW50KSA9PiB7XG4gICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgIGNvbnN0ICRidG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgIGNvbnN0ICRoaWRkZW5Qcm9kdWN0cyA9ICRidG4uY2xvc2VzdCgndGJvZHknKS5maW5kKCcuanMtcHJvZHVjdC1wcmV2aWV3LW1vcmUnKTtcblxuICAgICRoaWRkZW5Qcm9kdWN0cy5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgJGJ0bi5jbG9zZXN0KCd0cicpLnJlbW92ZSgpO1xuICB9KTtcbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL3ByZXZpZXctcHJvZHVjdHMtdG9nZ2xlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCByZXNldFNlYXJjaCBmcm9tICcuLi8uLi8uLi9hcHAvdXRpbHMvcmVzZXRfc2VhcmNoJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEZpbHRlcnNSZXNldEV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBmaWx0ZXJzIHJlc2V0dGluZ1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24ge1xuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLXJlc2V0LXNlYXJjaCcsIChldmVudCkgPT4ge1xuICAgICAgcmVzZXRTZWFyY2goJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCd1cmwnKSwgJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdyZWRpcmVjdCcpKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBHcmlkIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9ncmlkJztcbmltcG9ydCBGaWx0ZXJzUmVzZXRFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvbic7XG5pbXBvcnQgUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvbic7XG5pbXBvcnQgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZXhwb3J0LXRvLXNxbC1tYW5hZ2VyLWV4dGVuc2lvbic7XG5pbXBvcnQgU29ydGluZ0V4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uJztcbmltcG9ydCBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbGluay1yb3ctYWN0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgU3VibWl0R3JpZEV4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1ncmlkLWFjdGlvbi1leHRlbnNpb24nO1xuaW1wb3J0IFN1Ym1pdEJ1bGtFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zdWJtaXQtYnVsay1hY3Rpb24tZXh0ZW5zaW9uJztcbmltcG9ydCBCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9idWxrLWFjdGlvbi1jaGVja2JveC1leHRlbnNpb24nO1xuaW1wb3J0IEZpbHRlcnNTdWJtaXRCdXR0b25FbmFibGVyRXh0ZW5zaW9uXG4gIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1zdWJtaXQtYnV0dG9uLWVuYWJsZXItZXh0ZW5zaW9uJztcbmltcG9ydCBDaG9pY2VFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9jaG9pY2UtZXh0ZW5zaW9uJztcbmltcG9ydCBNb2RhbEZvcm1TdWJtaXRFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9tb2RhbC1mb3JtLXN1Ym1pdC1leHRlbnNpb24nO1xuaW1wb3J0IFByZXZpZXdFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9wcmV2aWV3LWV4dGVuc2lvbic7XG5pbXBvcnQgcHJldmlld1Byb2R1Y3RzVG9nZ2xlciBmcm9tICcuL3ByZXZpZXctcHJvZHVjdHMtdG9nZ2xlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gIGNvbnN0IG9yZGVyR3JpZCA9IG5ldyBHcmlkKCdvcmRlcicpO1xuICBvcmRlckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uKCkpO1xuICBvcmRlckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24oKSk7XG4gIG9yZGVyR3JpZC5hZGRFeHRlbnNpb24obmV3IEZpbHRlcnNSZXNldEV4dGVuc2lvbigpKTtcbiAgb3JkZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgU29ydGluZ0V4dGVuc2lvbigpKTtcbiAgb3JkZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbigpKTtcbiAgb3JkZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgU3VibWl0R3JpZEV4dGVuc2lvbigpKTtcbiAgb3JkZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgU3VibWl0QnVsa0V4dGVuc2lvbigpKTtcbiAgb3JkZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uKCkpO1xuICBvcmRlckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBGaWx0ZXJzU3VibWl0QnV0dG9uRW5hYmxlckV4dGVuc2lvbigpKTtcbiAgb3JkZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgTW9kYWxGb3JtU3VibWl0RXh0ZW5zaW9uKCkpO1xuICBvcmRlckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBDaG9pY2VFeHRlbnNpb24oKSk7XG4gIG9yZGVyR3JpZC5hZGRFeHRlbnNpb24obmV3IFByZXZpZXdFeHRlbnNpb24ocHJldmlld1Byb2R1Y3RzVG9nZ2xlcikpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9pbmRleC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBUYWJsZVNvcnRpbmcgZnJvbSAnLi4vLi4vLi4vYXBwL3V0aWxzL3RhYmxlLXNvcnRpbmcnO1xuXG4vKipcbiAqIENsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggXCJMaXN0IHJlbG9hZFwiIGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTb3J0aW5nRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBjb25zdCAkc29ydGFibGVUYWJsZSA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgndGFibGUudGFibGUnKTtcblxuICAgIG5ldyBUYWJsZVNvcnRpbmcoJHNvcnRhYmxlVGFibGUpLmF0dGFjaCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiTGlzdCByZWxvYWRcIiBhY3Rpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUmVsb2FkTGlzdEV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRIZWFkZXJDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWNvbW1vbl9yZWZyZXNoX2xpc3QtZ3JpZC1hY3Rpb24nLCAoKSA9PiB7XG4gICAgICBsb2NhdGlvbi5yZWxvYWQoKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9yZWxvYWQtbGlzdC1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG4vKipcbiAqIFJlc3BvbnNpYmxlIGZvciBncmlkIGZpbHRlcnMgc2VhcmNoIGFuZCByZXNldCBidXR0b24gYXZhaWxhYmlsaXR5IHdoZW4gZmlsdGVyIGlucHV0cyBjaGFuZ2VzLlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBGaWx0ZXJzU3VibWl0QnV0dG9uRW5hYmxlckV4dGVuc2lvbiB7XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBjb25zdCAkZmlsdGVyc1JvdyA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmNvbHVtbi1maWx0ZXJzJyk7XG4gICAgJGZpbHRlcnNSb3cuZmluZCgnLmdyaWQtc2VhcmNoLWJ1dHRvbicpLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG5cbiAgICAkZmlsdGVyc1Jvdy5maW5kKCdpbnB1dCwgc2VsZWN0Jykub24oJ2lucHV0IGRwLmNoYW5nZScsICgpID0+IHtcbiAgICAgICRmaWx0ZXJzUm93LmZpbmQoJy5ncmlkLXNlYXJjaC1idXR0b24nKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICAgICRmaWx0ZXJzUm93LmZpbmQoJy5qcy1ncmlkLXJlc2V0LWJ1dHRvbicpLnByb3AoJ2hpZGRlbicsIGZhbHNlKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXN1Ym1pdC1idXR0b24tZW5hYmxlci1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgQnVsa0FjdGlvblNlbGVjdENoZWNrYm94RXh0ZW5zaW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZCB3aXRoIGJ1bGsgYWN0aW9uIGNoZWNrYm94ZXMgaGFuZGxpbmcgZnVuY3Rpb25hbGl0eVxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgdGhpcy5faGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0KGdyaWQpO1xuICAgIHRoaXMuX2hhbmRsZUJ1bGtBY3Rpb25TZWxlY3RBbGxDaGVja2JveChncmlkKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIFwiU2VsZWN0IGFsbFwiIGJ1dHRvbiBpbiB0aGUgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oYW5kbGVCdWxrQWN0aW9uU2VsZWN0QWxsQ2hlY2tib3goZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NoYW5nZScsICcuanMtYnVsay1hY3Rpb24tc2VsZWN0LWFsbCcsIChlKSA9PiB7XG4gICAgICBjb25zdCAkY2hlY2tib3ggPSAkKGUuY3VycmVudFRhcmdldCk7XG5cbiAgICAgIGNvbnN0IGlzQ2hlY2tlZCA9ICRjaGVja2JveC5pcygnOmNoZWNrZWQnKTtcbiAgICAgIGlmIChpc0NoZWNrZWQpIHtcbiAgICAgICAgdGhpcy5fZW5hYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB0aGlzLl9kaXNhYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9XG5cbiAgICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9uLWNoZWNrYm94JykucHJvcCgnY2hlY2tlZCcsIGlzQ2hlY2tlZCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlcyBlYWNoIGJ1bGsgYWN0aW9uIGNoZWNrYm94IHNlbGVjdCBpbiB0aGUgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oYW5kbGVCdWxrQWN0aW9uQ2hlY2tib3hTZWxlY3QoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NoYW5nZScsICcuanMtYnVsay1hY3Rpb24tY2hlY2tib3gnLCAoKSA9PiB7XG4gICAgICBjb25zdCBjaGVja2VkUm93c0NvdW50ID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb24tY2hlY2tib3g6Y2hlY2tlZCcpLmxlbmd0aDtcblxuICAgICAgaWYgKGNoZWNrZWRSb3dzQ291bnQgPiAwKSB7XG4gICAgICAgIHRoaXMuX2VuYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGhpcy5fZGlzYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEVuYWJsZSBidWxrIGFjdGlvbnMgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2VuYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbnMtYnRuJykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gIH1cblxuICAvKipcbiAgICogRGlzYWJsZSBidWxrIGFjdGlvbnMgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2Rpc2FibGVCdWxrQWN0aW9uc0J0bihncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb25zLWJ0bicpLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYnVsay1hY3Rpb24tY2hlY2tib3gtZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBleHBvcnRpbmcgcXVlcnkgdG8gU1FMIE1hbmFnZXJcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX3Nob3dfcXVlcnktZ3JpZC1hY3Rpb24nLCAoKSA9PiB0aGlzLl9vblNob3dTcWxRdWVyeUNsaWNrKGdyaWQpKTtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX2V4cG9ydF9zcWxfbWFuYWdlci1ncmlkLWFjdGlvbicsICgpID0+IHRoaXMuX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrKGdyaWQpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbnZva2VkIHdoZW4gY2xpY2tpbmcgb24gdGhlIFwic2hvdyBzcWwgcXVlcnlcIiB0b29sYmFyIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vblNob3dTcWxRdWVyeUNsaWNrKGdyaWQpIHtcbiAgICBjb25zdCAkc3FsTWFuYWdlckZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfY29tbW9uX3Nob3dfcXVlcnlfbW9kYWxfZm9ybScpO1xuICAgIHRoaXMuX2ZpbGxFeHBvcnRGb3JtKCRzcWxNYW5hZ2VyRm9ybSwgZ3JpZCk7XG5cbiAgICBjb25zdCAkbW9kYWwgPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfZ3JpZF9jb21tb25fc2hvd19xdWVyeV9tb2RhbCcpO1xuICAgICRtb2RhbC5tb2RhbCgnc2hvdycpO1xuXG4gICAgJG1vZGFsLm9uKCdjbGljaycsICcuYnRuLXNxbC1zdWJtaXQnLCAoKSA9PiAkc3FsTWFuYWdlckZvcm0uc3VibWl0KCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEludm9rZWQgd2hlbiBjbGlja2luZyBvbiB0aGUgXCJleHBvcnQgdG8gdGhlIHNxbCBxdWVyeVwiIHRvb2xiYXIgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrKGdyaWQpIHtcbiAgICBjb25zdCAkc3FsTWFuYWdlckZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfY29tbW9uX3Nob3dfcXVlcnlfbW9kYWxfZm9ybScpO1xuXG4gICAgdGhpcy5fZmlsbEV4cG9ydEZvcm0oJHNxbE1hbmFnZXJGb3JtLCBncmlkKTtcblxuICAgICRzcWxNYW5hZ2VyRm9ybS5zdWJtaXQoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBGaWxsIGV4cG9ydCBmb3JtIHdpdGggU1FMIGFuZCBpdCdzIG5hbWVcbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRzcWxNYW5hZ2VyRm9ybVxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9maWxsRXhwb3J0Rm9ybSgkc3FsTWFuYWdlckZvcm0sIGdyaWQpIHtcbiAgICBjb25zdCBxdWVyeSA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWdyaWQtdGFibGUnKS5kYXRhKCdxdWVyeScpO1xuXG4gICAgJHNxbE1hbmFnZXJGb3JtLmZpbmQoJ3RleHRhcmVhW25hbWU9XCJzcWxcIl0nKS52YWwocXVlcnkpO1xuICAgICRzcWxNYW5hZ2VyRm9ybS5maW5kKCdpbnB1dFtuYW1lPVwibmFtZVwiXScpLnZhbCh0aGlzLl9nZXROYW1lRnJvbUJyZWFkY3J1bWIoKSk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGV4cG9ydCBuYW1lIGZyb20gcGFnZSdzIGJyZWFkY3J1bWJcbiAgICpcbiAgICogQHJldHVybiB7U3RyaW5nfVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldE5hbWVGcm9tQnJlYWRjcnVtYigpIHtcbiAgICBjb25zdCAkYnJlYWRjcnVtYnMgPSAkKCcuaGVhZGVyLXRvb2xiYXInKS5maW5kKCcuYnJlYWRjcnVtYi1pdGVtJyk7XG4gICAgbGV0IG5hbWUgPSAnJztcblxuICAgICRicmVhZGNydW1icy5lYWNoKChpLCBpdGVtKSA9PiB7XG4gICAgICBjb25zdCAkYnJlYWRjcnVtYiA9ICQoaXRlbSk7XG5cbiAgICAgIGNvbnN0IGJyZWFkY3J1bWJUaXRsZSA9IDAgPCAkYnJlYWRjcnVtYi5maW5kKCdhJykubGVuZ3RoID9cbiAgICAgICAgJGJyZWFkY3J1bWIuZmluZCgnYScpLnRleHQoKSA6XG4gICAgICAgICRicmVhZGNydW1iLnRleHQoKTtcblxuICAgICAgaWYgKDAgPCBuYW1lLmxlbmd0aCkge1xuICAgICAgICBuYW1lID0gbmFtZS5jb25jYXQoJyA+ICcpO1xuICAgICAgfVxuXG4gICAgICBuYW1lID0gbmFtZS5jb25jYXQoYnJlYWRjcnVtYlRpdGxlKTtcbiAgICB9KTtcblxuICAgIHJldHVybiBuYW1lO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2V4cG9ydC10by1zcWwtbWFuYWdlci1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogSGFuZGxlcyBzdWJtaXQgb2YgZ3JpZCBhY3Rpb25zXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFN1Ym1pdEJ1bGtBY3Rpb25FeHRlbnNpb24ge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICByZXR1cm4ge1xuICAgICAgZXh0ZW5kOiAoZ3JpZCkgPT4gdGhpcy5leHRlbmQoZ3JpZCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZCB3aXRoIGJ1bGsgYWN0aW9uIHN1Ym1pdHRpbmdcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1idWxrLWFjdGlvbi1zdWJtaXQtYnRuJywgKGV2ZW50KSA9PiB7XG4gICAgICB0aGlzLnN1Ym1pdChldmVudCwgZ3JpZCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlIGJ1bGsgYWN0aW9uIHN1Ym1pdHRpbmdcbiAgICpcbiAgICogQHBhcmFtIHtFdmVudH0gZXZlbnRcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBzdWJtaXQoZXZlbnQsIGdyaWQpIHtcbiAgICBjb25zdCAkc3VibWl0QnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICRzdWJtaXRCdG4uZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG5cbiAgICBpZiAodHlwZW9mIGNvbmZpcm1NZXNzYWdlICE9PSBcInVuZGVmaW5lZFwiICYmIDAgPCBjb25maXJtTWVzc2FnZS5sZW5ndGggJiYgIWNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgY29uc3QgJGZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfZmlsdGVyX2Zvcm0nKTtcblxuICAgICRmb3JtLmF0dHIoJ2FjdGlvbicsICRzdWJtaXRCdG4uZGF0YSgnZm9ybS11cmwnKSk7XG4gICAgJGZvcm0uYXR0cignbWV0aG9kJywgJHN1Ym1pdEJ0bi5kYXRhKCdmb3JtLW1ldGhvZCcpKTtcbiAgICAkZm9ybS5zdWJtaXQoKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zdWJtaXQtYnVsay1hY3Rpb24tZXh0ZW5zaW9uLmpzIl0sInNvdXJjZVJvb3QiOiIifQ==