window["order_create"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 368);
/******/ })
/************************************************************************/
/******/ ({

/***/ 100:
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

var _createOrderMap = __webpack_require__(33);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Renders cart rules (cartRules) block
 */

var CartRulesRenderer = function () {
  function CartRulesRenderer() {
    _classCallCheck(this, CartRulesRenderer);

    this.$cartRulesBlock = $(_createOrderMap2.default.cartRulesBlock);
    this.$cartRulesTable = $(_createOrderMap2.default.cartRulesTable);
    this.$searchResultBox = $(_createOrderMap2.default.cartRulesSearchResultBox);
  }

  /**
   * Responsible for rendering cartRules (a.k.a cart rules/discounts) block
   *
   * @param {Array} cartRules
   * @param {Boolean} emptyCart
   */


  _createClass(CartRulesRenderer, [{
    key: 'renderCartRulesBlock',
    value: function renderCartRulesBlock(cartRules, emptyCart) {
      this._hideErrorBlock();
      // do not render cart rules block at all if cart has no products
      if (emptyCart) {
        this._hideCartRulesBlock();
        return;
      }
      this._showCartRulesBlock();

      // do not render cart rules list when there are no cart rules
      if (cartRules.length === 0) {
        this._hideCartRulesList();

        return;
      }

      this._renderList(cartRules);
    }

    /**
     * Responsible for rendering search results dropdown
     *
     * @param searchResults
     */

  }, {
    key: 'renderSearchResults',
    value: function renderSearchResults(searchResults) {
      this._clearSearchResults();

      if (searchResults.cart_rules.length === 0) {
        this._renderNotFound();
      } else {
        this._renderFoundCartRules(searchResults.cart_rules);
      }

      this._showResultsDropdown();
    }

    /**
     * Displays error message bellow search input
     *
     * @param message
     */

  }, {
    key: 'displayErrorMessage',
    value: function displayErrorMessage(message) {
      $(_createOrderMap2.default.cartRuleErrorText).text(message);
      this._showErrorBlock();
    }

    /**
     * Hides cart rules search result dropdown
     */

  }, {
    key: 'hideResultsDropdown',
    value: function hideResultsDropdown() {
      this.$searchResultBox.addClass('d-none');
    }

    /**
     * Displays cart rules search result dropdown
     *
     * @private
     */

  }, {
    key: '_showResultsDropdown',
    value: function _showResultsDropdown() {
      this.$searchResultBox.removeClass('d-none');
    }

    /**
     * Renders warning that no cart rule was found
     *
     * @private
     */

  }, {
    key: '_renderNotFound',
    value: function _renderNotFound() {
      var $template = $($(_createOrderMap2.default.cartRulesNotFoundTemplate).html()).clone();
      this.$searchResultBox.html($template);
    }

    /**
     * Empties cart rule search results block
     *
     * @private
     */

  }, {
    key: '_clearSearchResults',
    value: function _clearSearchResults() {
      this.$searchResultBox.empty();
    }

    /**
     * Renders found cart rules after search
     *
     * @param cartRules
     *
     * @private
     */

  }, {
    key: '_renderFoundCartRules',
    value: function _renderFoundCartRules(cartRules) {
      var $cartRuleTemplate = $($(_createOrderMap2.default.foundCartRuleTemplate).html());
      for (var key in cartRules) {
        var $template = $cartRuleTemplate.clone();
        var cartRule = cartRules[key];

        var cartRuleName = cartRule.name;
        if (cartRule.code !== '') {
          cartRuleName = cartRule.name + ' - ' + cartRule.code;
        }

        $template.text(cartRuleName);
        $template.data('cart-rule-id', cartRule.cartRuleId);
        this.$searchResultBox.append($template);
      }
    }

    /**
     * Responsible for rendering the list of cart rules
     *
     * @param {Array} cartRules
     *
     * @private
     */

  }, {
    key: '_renderList',
    value: function _renderList(cartRules) {
      this._cleanCartRulesList();
      var $cartRulesTableRowTemplate = $($(_createOrderMap2.default.cartRulesTableRowTemplate).html());

      for (var key in cartRules) {
        var cartRule = cartRules[key];
        var $template = $cartRulesTableRowTemplate.clone();

        $template.find(_createOrderMap2.default.cartRuleNameField).text(cartRule.name);
        $template.find(_createOrderMap2.default.cartRuleDescriptionField).text(cartRule.description);
        $template.find(_createOrderMap2.default.cartRuleValueField).text(cartRule.value);
        $template.find(_createOrderMap2.default.cartRuleDeleteBtn).data('cart-rule-id', cartRule.cartRuleId);

        this.$cartRulesTable.find('tbody').append($template);
      }

      this._showCartRulesList();
    }

    /**
     * Shows error block
     *
     * @private
     */

  }, {
    key: '_showErrorBlock',
    value: function _showErrorBlock() {
      $(_createOrderMap2.default.cartRuleErrorBlock).removeClass('d-none');
    }

    /**
     * Hides error block
     *
     * @private
     */

  }, {
    key: '_hideErrorBlock',
    value: function _hideErrorBlock() {
      $(_createOrderMap2.default.cartRuleErrorBlock).addClass('d-none');
    }

    /**
     * Shows cartRules block
     *
     * @private
     */

  }, {
    key: '_showCartRulesBlock',
    value: function _showCartRulesBlock() {
      this.$cartRulesBlock.removeClass('d-none');
    }

    /**
     * hide cartRules block
     *
     * @private
     */

  }, {
    key: '_hideCartRulesBlock',
    value: function _hideCartRulesBlock() {
      this.$cartRulesBlock.addClass('d-none');
    }

    /**
     * Display the list block of cart rules
     *
     * @private
     */

  }, {
    key: '_showCartRulesList',
    value: function _showCartRulesList() {
      this.$cartRulesTable.removeClass('d-none');
    }

    /**
     * Hide list block of cart rules
     *
     * @private
     */

  }, {
    key: '_hideCartRulesList',
    value: function _hideCartRulesList() {
      this.$cartRulesTable.addClass('d-none');
    }

    /**
     * remove items in cart rules list
     *
     * @private
     */

  }, {
    key: '_cleanCartRulesList',
    value: function _cleanCartRulesList() {
      this.$cartRulesTable.find('tbody').empty();
    }
  }]);

  return CartRulesRenderer;
}();

exports.default = CartRulesRenderer;

/***/ }),

/***/ 101:
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

var _createOrderMap = __webpack_require__(33);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

var ProductRenderer = function () {
  function ProductRenderer() {
    _classCallCheck(this, ProductRenderer);

    this.$productsTable = $(_createOrderMap2.default.productsTable);
  }

  /**
   * Renders cart products list
   *
   * @param products
   */


  _createClass(ProductRenderer, [{
    key: 'renderList',
    value: function renderList(products) {
      this._cleanProductsList();

      if (products.length === 0) {
        this._hideProductsList();

        return;
      }

      var $productsTableRowTemplate = $($(_createOrderMap2.default.productsTableRowTemplate).html());

      for (var key in products) {
        var product = products[key];
        var $template = $productsTableRowTemplate.clone();

        $template.find(_createOrderMap2.default.productImageField).text(product.imageLink);
        $template.find(_createOrderMap2.default.productNameField).text(product.name);
        $template.find(_createOrderMap2.default.productAttrField).text(product.attribute);
        $template.find(_createOrderMap2.default.productReferenceField).text(product.reference);
        $template.find(_createOrderMap2.default.productUnitPriceInput).text(product.unitPrice);
        $template.find(_createOrderMap2.default.productTotalPriceField).text(product.price);
        $template.find(_createOrderMap2.default.productRemoveBtn).data('product-id', product.productId);
        $template.find(_createOrderMap2.default.productRemoveBtn).data('attribute-id', product.attributeId);
        $template.find(_createOrderMap2.default.productRemoveBtn).data('customization-id', product.customizationId);

        this.$productsTable.find('tbody').append($template);
      }

      this._showTaxWarning();
      this._showProductsList();
    }

    /**
     * Renders cart products search results block
     *
     * @param foundProducts
     */

  }, {
    key: 'renderSearchResults',
    value: function renderSearchResults(foundProducts) {
      this._cleanSearchResults();
      if (foundProducts.length === 0) {
        this._showNotFound();
        this._hideTaxWarning();

        return;
      }

      this._renderFoundProducts(foundProducts);

      this._hideNotFound();
      this._showTaxWarning();
      this._showResultBlock();
    }

    /**
     * Renders available fields related to selected product
     *
     * @param product
     */

  }, {
    key: 'renderProductMetadata',
    value: function renderProductMetadata(product) {
      this.renderStock(product.stock);
      this._renderCombinations(product.combinations);
      this._renderCustomizations(product.customization_fields);
    }

    /**
     * Updates stock text helper value
     *
     * @param stock
     */

  }, {
    key: 'renderStock',
    value: function renderStock(stock) {
      $(_createOrderMap2.default.inStockCounter).text(stock);
      $(_createOrderMap2.default.quantityInput).attr('max', stock);
    }

    /**
     * Renders found products select
     *
     * @param foundProducts
     *
     * @private
     */

  }, {
    key: '_renderFoundProducts',
    value: function _renderFoundProducts(foundProducts) {
      for (var key in foundProducts) {
        var product = foundProducts[key];

        var name = product.name;
        if (product.combinations.length === 0) {
          name += ' - ' + product.formatted_price;
        }

        $(_createOrderMap2.default.productSelect).append('<option value="' + product.product_id + '">' + name + '</option>');
      }
    }

    /**
     * Cleans product search result fields
     *
     * @private
     */

  }, {
    key: '_cleanSearchResults',
    value: function _cleanSearchResults() {
      $(_createOrderMap2.default.productSelect).empty();
      $(_createOrderMap2.default.combinationsSelect).empty();
      $(_createOrderMap2.default.quantityInput).empty();
    }

    /**
     * Renders combinations row with select options
     *
     * @param {Array} combinations
     *
     * @private
     */

  }, {
    key: '_renderCombinations',
    value: function _renderCombinations(combinations) {
      this._cleanCombinations();

      if (combinations.length === 0) {
        this._hideCombinations();

        return;
      }

      for (var key in combinations) {
        var combination = combinations[key];

        $(_createOrderMap2.default.combinationsSelect).append('<option\n          value="' + combination.attribute_combination_id + '">\n          ' + combination.attribute + ' - ' + combination.formatted_price + '\n        </option>');
      }

      this._showCombinations();
    }

    /**
     * Resolves weather to add customization fields to result block and adds them if needed
     *
     * @param customizationFields
     *
     * @private
     */

  }, {
    key: '_renderCustomizations',
    value: function _renderCustomizations(customizationFields) {
      var _templateTypeMap;

      // represents customization field type "file".
      var fieldTypeFile = 0;
      // represents customization field type "text".
      var fieldTypeText = 1;

      this._cleanCustomizations();
      if (customizationFields.length === 0) {
        this._hideCustomizations();

        return;
      }

      var $customFieldsContainer = $(_createOrderMap2.default.productCustomFieldsContainer);
      var $fileInputTemplate = $($(_createOrderMap2.default.productCustomFileTemplate).html());
      var $textInputTemplate = $($(_createOrderMap2.default.productCustomTextTemplate).html());

      var templateTypeMap = (_templateTypeMap = {}, _defineProperty(_templateTypeMap, fieldTypeFile, $fileInputTemplate), _defineProperty(_templateTypeMap, fieldTypeText, $textInputTemplate), _templateTypeMap);

      for (var key in customizationFields) {
        var customField = customizationFields[key];
        var $template = templateTypeMap[customField.type].clone();

        $template.find(_createOrderMap2.default.productCustomInput).attr('name', 'customizations[' + customField.customization_field_id + ']');
        $template.find(_createOrderMap2.default.productCustomInputLabel).attr('for', 'customizations[' + customField.customization_field_id + ']').text(customField.name);

        $customFieldsContainer.append($template);
      }

      this._showCustomizations();
    }

    /**
     * Shows product customization container
     *
     * @private
     */

  }, {
    key: '_showCustomizations',
    value: function _showCustomizations() {
      $(_createOrderMap2.default.productCustomizationContainer).removeClass('d-none');
    }

    /**
     * Hides product customization container
     *
     * @private
     */

  }, {
    key: '_hideCustomizations',
    value: function _hideCustomizations() {
      $(_createOrderMap2.default.productCustomizationContainer).addClass('d-none');
    }

    /**
     * Empties customization fields container
     *
     * @private
     */

  }, {
    key: '_cleanCustomizations',
    value: function _cleanCustomizations() {
      $(_createOrderMap2.default.productCustomFieldsContainer).empty();
    }

    /**
     * Shows result block
     *
     * @private
     */

  }, {
    key: '_showResultBlock',
    value: function _showResultBlock() {
      $(_createOrderMap2.default.productResultBlock).removeClass('d-none');
    }

    /**
     * Hides result block
     *
     * @private
     */

  }, {
    key: '_hideResultBlock',
    value: function _hideResultBlock() {
      $(_createOrderMap2.default.productResultBlock).addClass('d-none');
    }

    /**
     * Shows products list
     *
     * @private
     */

  }, {
    key: '_showProductsList',
    value: function _showProductsList() {
      this.$productsTable.removeClass('d-none');
    }

    /**
     * Hides products list
     *
     * @private
     */

  }, {
    key: '_hideProductsList',
    value: function _hideProductsList() {
      this.$productsTable.addClass('d-none');
    }

    /**
     * Empties products list
     *
     * @private
     */

  }, {
    key: '_cleanProductsList',
    value: function _cleanProductsList() {
      this.$productsTable.find('tbody').empty();
    }

    /**
     * Empties combinations select
     *
     * @private
     */

  }, {
    key: '_cleanCombinations',
    value: function _cleanCombinations() {
      $(_createOrderMap2.default.combinationsSelect).empty();
    }

    /**
     * Shows combinations row
     *
     * @private
     */

  }, {
    key: '_showCombinations',
    value: function _showCombinations() {
      $(_createOrderMap2.default.combinationsRow).removeClass('d-none');
    }

    /**
     * Hides combinations row
     *
     * @private
     */

  }, {
    key: '_hideCombinations',
    value: function _hideCombinations() {
      $(_createOrderMap2.default.combinationsRow).addClass('d-none');
    }

    /**
     * Shows warning of tax included/excluded
     *
     * @private
     */

  }, {
    key: '_showTaxWarning',
    value: function _showTaxWarning() {
      $(_createOrderMap2.default.productTaxWarning).removeClass('d-none');
    }

    /**
     * Hides warning of tax included/excluded
     *
     * @private
     */

  }, {
    key: '_hideTaxWarning',
    value: function _hideTaxWarning() {
      $(_createOrderMap2.default.productTaxWarning).addClass('d-none');
    }

    /**
     * Shows product not found warning
     *
     * @private
     */

  }, {
    key: '_showNotFound',
    value: function _showNotFound() {
      $(_createOrderMap2.default.noProductsFoundWarning).removeClass('d-none');
    }

    /**
     * Hides product not found warning
     *
     * @private
     */

  }, {
    key: '_hideNotFound',
    value: function _hideNotFound() {
      $(_createOrderMap2.default.noProductsFoundWarning).addClass('d-none');
    }
  }]);

  return ProductRenderer;
}();

exports.default = ProductRenderer;

/***/ }),

/***/ 17:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.EventEmitter = undefined;

var _events = __webpack_require__(19);

var _events2 = _interopRequireDefault(_events);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * We instanciate one EventEmitter (restricted via a const) so that every components
 * register/dispatch on the same one and can communicate with each other.
 */
var EventEmitter = exports.EventEmitter = new _events2.default(); /**
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

/***/ }),

/***/ 19:
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

/***/ 277:
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

var _createOrderMap = __webpack_require__(33);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _customerManager = __webpack_require__(372);

var _customerManager2 = _interopRequireDefault(_customerManager);

var _shippingRenderer = __webpack_require__(375);

var _shippingRenderer2 = _interopRequireDefault(_shippingRenderer);

var _cartProvider = __webpack_require__(370);

var _cartProvider2 = _interopRequireDefault(_cartProvider);

var _addressesRenderer = __webpack_require__(369);

var _addressesRenderer2 = _interopRequireDefault(_addressesRenderer);

var _cartRulesRenderer = __webpack_require__(100);

var _cartRulesRenderer2 = _interopRequireDefault(_cartRulesRenderer);

var _router = __webpack_require__(48);

var _router2 = _interopRequireDefault(_router);

var _eventEmitter = __webpack_require__(17);

var _cartEditor = __webpack_require__(73);

var _cartEditor2 = _interopRequireDefault(_cartEditor);

var _eventMap = __webpack_require__(51);

var _eventMap2 = _interopRequireDefault(_eventMap);

var _cartRuleManager = __webpack_require__(371);

var _cartRuleManager2 = _interopRequireDefault(_cartRuleManager);

var _productManager = __webpack_require__(374);

var _productManager2 = _interopRequireDefault(_productManager);

var _productRenderer = __webpack_require__(101);

var _productRenderer2 = _interopRequireDefault(_productRenderer);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Page Object for "Create order" page
 */

var CreateOrderPage = function () {
  function CreateOrderPage() {
    _classCallCheck(this, CreateOrderPage);

    this.cartId = null;
    this.$container = $(_createOrderMap2.default.orderCreationContainer);

    this.cartProvider = new _cartProvider2.default();
    this.customerManager = new _customerManager2.default();
    this.shippingRenderer = new _shippingRenderer2.default();
    this.addressesRenderer = new _addressesRenderer2.default();
    this.cartRulesRenderer = new _cartRulesRenderer2.default();
    this.router = new _router2.default();
    this.cartEditor = new _cartEditor2.default();
    this.cartRuleManager = new _cartRuleManager2.default();
    this.productManager = new _productManager2.default();
    this.productRenderer = new _productRenderer2.default();

    this._initListeners();
  }

  /**
   * Initializes event listeners
   *
   * @private
   */


  _createClass(CreateOrderPage, [{
    key: '_initListeners',
    value: function _initListeners() {
      var _this = this;

      this.$container.on('input', _createOrderMap2.default.customerSearchInput, function (e) {
        return _this._initCustomerSearch(e);
      });
      this.$container.on('click', _createOrderMap2.default.chooseCustomerBtn, function (e) {
        return _this._initCustomerSelect(e);
      });
      this.$container.on('click', _createOrderMap2.default.useCartBtn, function (e) {
        return _this._initCartSelect(e);
      });
      this.$container.on('click', _createOrderMap2.default.useOrderBtn, function (e) {
        return _this._initDuplicateOrderCart(e);
      });
      this.$container.on('input', _createOrderMap2.default.productSearch, function (e) {
        return _this._initProductSearch(e);
      });
      this.$container.on('input', _createOrderMap2.default.cartRuleSearchInput, function (e) {
        return _this._initCartRuleSearch(e);
      });
      this.$container.on('blur', _createOrderMap2.default.cartRuleSearchInput, function () {
        return _this.cartRuleManager.stopSearching();
      });
      this._initCartEditing();
      this._onCartLoaded();
      this._onCartAddressesChanged();
    }

    /**
     * Delegates actions to events associated with cart update (e.g. change cart address)
     *
     * @private
     */

  }, {
    key: '_initCartEditing',
    value: function _initCartEditing() {
      var _this2 = this;

      this.$container.on('change', _createOrderMap2.default.deliveryOptionSelect, function (e) {
        return _this2.cartEditor.changeDeliveryOption(_this2.cartId, e.currentTarget.value);
      });

      this.$container.on('change', _createOrderMap2.default.freeShippingSwitch, function (e) {
        return _this2.cartEditor.setFreeShipping(_this2.cartId, e.currentTarget.value);
      });

      this.$container.on('click', _createOrderMap2.default.addToCartButton, function () {
        return _this2.productManager.addProductToCart(_this2.cartId);
      });

      this.$container.on('change', _createOrderMap2.default.addressSelect, function () {
        return _this2._changeCartAddresses();
      });
      this.$container.on('click', _createOrderMap2.default.productRemoveBtn, function (e) {
        return _this2._initProductRemoveFromCart(e);
      });

      this._addCartRuleToCart();
      this._removeCartRuleFromCart();
    }

    /**
     * Listens for event when cart is loaded
     *
     * @private
     */

  }, {
    key: '_onCartLoaded',
    value: function _onCartLoaded() {
      var _this3 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartLoaded, function (cartInfo) {
        _this3.cartId = cartInfo.cartId;
        _this3._renderCartInfo(cartInfo);
        _this3.customerManager.loadCustomerCarts(_this3.cartId);
        _this3.customerManager.loadCustomerOrders();
      });
    }

    /**
     * Listens for cart addresses update event
     *
     * @private
     */

  }, {
    key: '_onCartAddressesChanged',
    value: function _onCartAddressesChanged() {
      var _this4 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartAddressesChanged, function (cartInfo) {
        _this4.addressesRenderer.render(cartInfo.addresses);
        _this4.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
      });
    }

    /**
     * Listens for cart delivery option update event
     *
     * @private
     */

  }, {
    key: '_onDeliveryOptionChanged',
    value: function _onDeliveryOptionChanged() {
      var _this5 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartDeliveryOptionChanged, function (cartInfo) {
        _this5.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
      });
    }

    /**
     * Listens for cart free shipping update event
     *
     * @private
     */

  }, {
    key: '_onFreeShippingChanged',
    value: function _onFreeShippingChanged() {
      var _this6 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartFreeShippingSet, function (cartInfo) {
        _this6.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
      });
    }

    /**
     * Init customer searching
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initCustomerSearch',
    value: function _initCustomerSearch(event) {
      var _this7 = this;

      setTimeout(function () {
        return _this7.customerManager.search($(event.currentTarget).val());
      }, 300);
    }

    /**
     * Init selecting customer for which order is being created
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initCustomerSelect',
    value: function _initCustomerSelect(event) {
      var customerId = this.customerManager.selectCustomer(event);
      this.cartProvider.loadEmptyCart(customerId);
    }

    /**
     * Inits selecting cart to load
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initCartSelect',
    value: function _initCartSelect(event) {
      var cartId = $(event.currentTarget).data('cart-id');
      this.cartProvider.getCart(cartId);
    }

    /**
     * Inits duplicating order cart
     *
     * @private
     */

  }, {
    key: '_initDuplicateOrderCart',
    value: function _initDuplicateOrderCart(event) {
      var orderId = $(event.currentTarget).data('order-id');
      this.cartProvider.duplicateOrderCart(orderId);
    }

    /**
     * Triggers cart rule searching
     *
     * @private
     */

  }, {
    key: '_initCartRuleSearch',
    value: function _initCartRuleSearch(event) {
      var searchPhrase = event.currentTarget.value;
      this.cartRuleManager.search(searchPhrase);
    }

    /**
     * Triggers cart rule select
     *
     * @private
     */

  }, {
    key: '_addCartRuleToCart',
    value: function _addCartRuleToCart() {
      var _this8 = this;

      this.$container.on('mousedown', _createOrderMap2.default.foundCartRuleListItem, function (event) {
        // prevent blur event to allow selecting cart rule
        event.preventDefault();
        var cartRuleId = $(event.currentTarget).data('cart-rule-id');
        _this8.cartRuleManager.addCartRuleToCart(cartRuleId, _this8.cartId);

        // manually fire blur event after cart rule is selected.
      }).on('click', _createOrderMap2.default.foundCartRuleListItem, function () {
        $(_createOrderMap2.default.cartRuleSearchInput).blur();
      });
    }

    /**
     * Triggers cart rule removal from cart
     *
     * @private
     */

  }, {
    key: '_removeCartRuleFromCart',
    value: function _removeCartRuleFromCart() {
      var _this9 = this;

      this.$container.on('click', _createOrderMap2.default.cartRuleDeleteBtn, function (event) {
        _this9.cartRuleManager.removeCartRuleFromCart($(event.currentTarget).data('cart-rule-id'), _this9.cartId);
      });
    }

    /**
     * Inits product searching
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initProductSearch',
    value: function _initProductSearch(event) {
      var _this10 = this;

      var $productSearchInput = $(event.currentTarget);
      var searchPhrase = $productSearchInput.val();

      setTimeout(function () {
        return _this10.productManager.search(searchPhrase);
      }, 300);
    }

    /**
     * Inits product removing from cart
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initProductRemoveFromCart',
    value: function _initProductRemoveFromCart(event) {
      var product = {
        productId: $(event.currentTarget).data('product-id'),
        attributeId: $(event.currentTarget).data('attribute-id'),
        customizationId: $(event.currentTarget).data('customization-id')
      };

      this.productManager.removeProductFromCart(this.cartId, product);
    }

    /**
     * Renders cart summary on the page
     *
     * @param {Object} cartInfo
     *
     * @private
     */

  }, {
    key: '_renderCartInfo',
    value: function _renderCartInfo(cartInfo) {
      this.addressesRenderer.render(cartInfo.addresses);
      this.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartInfo.products.length === 0);
      this.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
      this.productRenderer.renderList(cartInfo.products);
      // @todo: render Summary block when at least 1 product is in cart
      // and delivery options are available

      $(_createOrderMap2.default.cartBlock).removeClass('d-none');
    }

    /**
     * Changes cart addresses
     *
     * @private
     */

  }, {
    key: '_changeCartAddresses',
    value: function _changeCartAddresses() {
      var addresses = {
        deliveryAddressId: $(_createOrderMap2.default.deliveryAddressSelect).val(),
        invoiceAddressId: $(_createOrderMap2.default.invoiceAddressSelect).val()
      };

      this.cartEditor.changeCartAddresses(this.cartId, addresses);
    }
  }]);

  return CreateOrderPage;
}();

exports.default = CreateOrderPage;

/***/ }),

/***/ 314:
/***/ (function(module, exports) {

module.exports = {"base_url":"","routes":{"admin_products_search":{"tokens":[["text","/sell/catalog/products/search"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_cart_rules_search":{"tokens":[["text","/sell/catalog/cart-rules/search"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_customers_view":{"tokens":[["text","/view"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],"defaults":[],"requirements":{"customerId":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_customers_search":{"tokens":[["text","/sell/customers/search"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_customers_carts":{"tokens":[["text","/carts"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],"defaults":[],"requirements":{"customerId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_customers_orders":{"tokens":[["text","/orders"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],"defaults":[],"requirements":{"customerId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_carts_view":{"tokens":[["text","/view"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_carts_info":{"tokens":[["text","/info"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_carts_create":{"tokens":[["text","/sell/orders/carts/new"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_addresses":{"tokens":[["text","/addresses"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_carrier":{"tokens":[["text","/carrier"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_set_free_shipping":{"tokens":[["text","/rules/free-shipping"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_add_cart_rule":{"tokens":[["text","/cart-rules"],["variable","/","[^/]++","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_delete_cart_rule":{"tokens":[["text","/delete"],["variable","/","[^/]++","cartRuleId"],["text","/cart-rules"],["variable","/","[^/]++","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_add_product":{"tokens":[["text","/products"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+","productId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_delete_product":{"tokens":[["text","/delete-product"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_view":{"tokens":[["text","/view"],["variable","/","[^/]++","orderId"],["text","/sell/orders/orders"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"admin_orders_duplicate_cart":{"tokens":[["text","/duplicate-cart"],["variable","/","\\d+","orderId"],["text","/sell/orders/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]}},"prefix":"","host":"localhost","port":"","scheme":"http","locale":[]}

/***/ }),

/***/ 33:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


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
 * Encapsulates selectors for "Create order" page
 */
exports.default = {
  orderCreationContainer: '#order-creation-container',

  // selectors related to customer block
  customerSearchInput: '#customer-search-input',
  customerSearchResultsBlock: '.js-customer-search-results',
  customerSearchResultTemplate: '#customer-search-result-template',
  changeCustomerBtn: '.js-change-customer-btn',
  customerSearchRow: '.js-search-customer-row',
  chooseCustomerBtn: '.js-choose-customer-btn',
  notSelectedCustomerSearchResults: '.js-customer-search-result:not(.border-success)',
  customerSearchResultName: '.js-customer-name',
  customerSearchResultEmail: '.js-customer-email',
  customerSearchResultId: '.js-customer-id',
  customerSearchResultBirthday: '.js-customer-birthday',
  customerDetailsBtn: '.js-details-customer-btn',
  customerSearchResultColumn: '.js-customer-search-result-col',
  customerSearchBlock: '#customer-search-block',
  customerCartsTab: '.js-customer-carts-tab',
  customerOrdersTab: '.js-customer-orders-tab',
  customerCartsTable: '#customer-carts-table',
  customerCartsTableRowTemplate: '#customer-carts-table-row-template',
  customerCheckoutHistory: '#customer-checkout-history',
  customerOrdersTable: '#customer-orders-table',
  customerOrdersTableRowTemplate: '#customer-orders-table-row-template',
  cartRulesTable: '#cart-rules-table',
  cartRulesTableRowTemplate: '#cart-rules-table-row-template',
  useCartBtn: '.js-use-cart-btn',
  cartDetailsBtn: '.js-cart-details-btn',
  cartIdField: '.js-cart-id',
  cartDateField: '.js-cart-date',
  cartTotalField: '.js-cart-total',
  useOrderBtn: '.js-use-order-btn',
  orderDetailsBtn: '.js-order-details-btn',
  orderIdField: '.js-order-id',
  orderDateField: '.js-order-date',
  orderProductsField: '.js-order-products',
  orderTotalField: '.js-order-total-paid',
  orderStatusField: '.js-order-status',

  // selectors related to cart block
  cartBlock: '#cart-block',

  // selectors related to cartRules block
  cartRulesBlock: '#cart-rules-block',
  cartRuleSearchInput: '#search-cart-rules-input',
  cartRulesSearchResultBox: '#search-cart-rules-result-box',
  cartRulesNotFoundTemplate: '#cart-rules-not-found-template',
  foundCartRuleTemplate: '#found-cart-rule-template',
  foundCartRuleListItem: '.js-found-cart-rule',
  cartRuleNameField: '.js-cart-rule-name',
  cartRuleDescriptionField: '.js-cart-rule-description',
  cartRuleValueField: '.js-cart-rule-value',
  cartRuleDeleteBtn: '.js-cart-rule-delete-btn',
  cartRuleErrorBlock: '#js-cart-rule-error-block',
  cartRuleErrorText: '#js-cart-rule-error-text',

  // selectors related to addresses block
  addressesBlock: '#addresses-block',
  deliveryAddressDetails: '#delivery-address-details',
  invoiceAddressDetails: '#invoice-address-details',
  deliveryAddressSelect: '#delivery-address-select',
  invoiceAddressSelect: '#invoice-address-select',
  addressSelect: '.js-address-select',
  addressesContent: '#addresses-content',
  addressesWarning: '#addresses-warning',

  // selectors related to summary block
  summaryBlock: '#summary-block',

  // selectors related to shipping block
  shippingBlock: '#shipping-block',
  shippingForm: '.js-shipping-form',
  noCarrierBlock: '.js-no-carrier-block',
  deliveryOptionSelect: '#delivery-option-select',
  totalShippingField: '.js-total-shipping',
  freeShippingSwitch: '.js-free-shipping-switch',

  // selectors related to cart products block
  productSearch: '#product-search',
  combinationsSelect: '#combination-select',
  productResultBlock: '#product-search-results',
  productSelect: '#product-select',
  quantityInput: '#quantity-input',
  inStockCounter: '.js-in-stock-counter',
  combinationsTemplate: '#combinations-template',
  combinationsRow: '.js-combinations-row',
  productSelectRow: '.js-product-select-row',
  productCustomFieldsContainer: '#js-custom-fields-container',
  productCustomizationContainer: '#js-customization-container',
  productCustomFileTemplate: '#js-product-custom-file-template',
  productCustomTextTemplate: '#js-product-custom-text-template',
  productCustomInputLabel: '.js-product-custom-input-label',
  productCustomInput: '.js-product-custom-input',
  quantityRow: '.js-quantity-row',
  addToCartButton: '#add-product-to-cart-btn',
  productsTable: '#products-table',
  productsTableRowTemplate: '#products-table-row-template',
  productImageField: '.js-product-image',
  productNameField: '.js-product-name',
  productAttrField: '.js-product-attr',
  productReferenceField: '.js-product-ref',
  productUnitPriceInput: '.js-product-unit-input',
  productTotalPriceField: '.js-product-total-price',
  productRemoveBtn: '.js-product-remove-btn',
  productTaxWarning: '.js-tax-warning',
  noProductsFoundWarning: '.js-no-products-found'
};

/***/ }),

/***/ 368:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _createOrderPage = __webpack_require__(277);

var _createOrderPage2 = _interopRequireDefault(_createOrderPage);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
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


$(document).ready(function () {
  new _createOrderPage2.default();
});

/***/ }),

/***/ 369:
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

var _createOrderMap = __webpack_require__(33);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Renders Delivery & Invoice addresses select
 */

var AddressesRenderer = function () {
  function AddressesRenderer() {
    _classCallCheck(this, AddressesRenderer);
  }

  _createClass(AddressesRenderer, [{
    key: 'render',


    /**
     * @param {Array} addresses
     */
    value: function render(addresses) {
      var deliveryAddressDetailsContent = '';
      var invoiceAddressDetailsContent = '';

      var $deliveryAddressDetails = $(_createOrderMap2.default.deliveryAddressDetails);
      var $invoiceAddressDetails = $(_createOrderMap2.default.invoiceAddressDetails);
      var $deliveryAddressSelect = $(_createOrderMap2.default.deliveryAddressSelect);
      var $invoiceAddressSelect = $(_createOrderMap2.default.invoiceAddressSelect);

      var $addressesContent = $(_createOrderMap2.default.addressesContent);
      var $addressesWarningContent = $(_createOrderMap2.default.addressesWarning);

      $deliveryAddressDetails.empty();
      $invoiceAddressDetails.empty();
      $deliveryAddressSelect.empty();
      $invoiceAddressSelect.empty();

      if (addresses.length === 0) {
        $addressesWarningContent.removeClass('d-none');
        $addressesContent.addClass('d-none');

        return;
      }

      $addressesContent.removeClass('d-none');
      $addressesWarningContent.addClass('d-none');

      for (var key in Object.keys(addresses)) {
        var address = addresses[key];

        var deliveryAddressOption = {
          value: address.addressId,
          text: address.alias
        };

        var invoiceAddressOption = {
          value: address.addressId,
          text: address.alias
        };

        if (address.delivery) {
          deliveryAddressDetailsContent = address.formattedAddress;
          deliveryAddressOption.selected = 'selected';
        }

        if (address.invoice) {
          invoiceAddressDetailsContent = address.formattedAddress;
          invoiceAddressOption.selected = 'selected';
        }

        $deliveryAddressSelect.append($('<option>', deliveryAddressOption));
        $invoiceAddressSelect.append($('<option>', invoiceAddressOption));
      }

      if (deliveryAddressDetailsContent) {
        $deliveryAddressDetails.html(deliveryAddressDetailsContent);
      }

      if (invoiceAddressDetailsContent) {
        $invoiceAddressDetails.html(invoiceAddressDetailsContent);
      }

      this._showAddressesBlock();
    }

    /**
     * Shows addresses block
     *
     * @private
     */

  }, {
    key: '_showAddressesBlock',
    value: function _showAddressesBlock() {
      $(_createOrderMap2.default.addressesBlock).removeClass('d-none');
    }
  }]);

  return AddressesRenderer;
}();

exports.default = AddressesRenderer;

/***/ }),

/***/ 370:
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

var _createOrderMap = __webpack_require__(33);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _router = __webpack_require__(48);

var _router2 = _interopRequireDefault(_router);

var _eventEmitter = __webpack_require__(17);

var _eventMap = __webpack_require__(51);

var _eventMap2 = _interopRequireDefault(_eventMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Provides ajax calls for getting cart information
 */

var CartProvider = function () {
  function CartProvider() {
    _classCallCheck(this, CartProvider);

    this.$container = $(_createOrderMap2.default.orderCreationContainer);
    this.router = new _router2.default();
  }

  /**
   * Gets cart information
   *
   * @param cartId
   *
   * @returns {jqXHR}. Object with cart information in response.
   */


  _createClass(CartProvider, [{
    key: 'getCart',
    value: function getCart(cartId) {
      $.get(this.router.generate('admin_carts_info', { cartId: cartId })).then(function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartLoaded, cartInfo);
      });
    }

    /**
     * Gets existing empty cart or creates new empty cart for customer.
     *
     * @param customerId
     *
     * @returns {jqXHR}. Object with cart information in response
     */

  }, {
    key: 'loadEmptyCart',
    value: function loadEmptyCart(customerId) {
      $.post(this.router.generate('admin_carts_create'), {
        customer_id: customerId
      }).then(function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartLoaded, cartInfo);
      });
    }

    /**
     * Duplicates cart from provided order
     *
     * @param orderId
     *
     * @returns {jqXHR}. Object with cart information in response
     */

  }, {
    key: 'duplicateOrderCart',
    value: function duplicateOrderCart(orderId) {
      $.post(this.router.generate('admin_orders_duplicate_cart', { orderId: orderId })).then(function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartLoaded, cartInfo);
      });
    }
  }]);

  return CartProvider;
}();

exports.default = CartProvider;

/***/ }),

/***/ 371:
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

var _cartEditor = __webpack_require__(73);

var _cartEditor2 = _interopRequireDefault(_cartEditor);

var _cartRulesRenderer = __webpack_require__(100);

var _cartRulesRenderer2 = _interopRequireDefault(_cartRulesRenderer);

var _createOrderMap = __webpack_require__(33);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _eventEmitter = __webpack_require__(17);

var _eventMap = __webpack_require__(51);

var _eventMap2 = _interopRequireDefault(_eventMap);

var _router = __webpack_require__(48);

var _router2 = _interopRequireDefault(_router);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Responsible for searching cart rules and managing cart rules search block
 */

var CartRuleManager = function () {
  function CartRuleManager() {
    var _this = this;

    _classCallCheck(this, CartRuleManager);

    this.router = new _router2.default();
    this.$searchInput = $(_createOrderMap2.default.cartRuleSearchInput);
    this.cartRulesRenderer = new _cartRulesRenderer2.default();
    this.cartEditor = new _cartEditor2.default();

    this._initListeners();

    return {
      search: function search() {
        return _this._search();
      },
      stopSearching: function stopSearching() {
        return _this.cartRulesRenderer.hideResultsDropdown();
      },
      addCartRuleToCart: function addCartRuleToCart(cartRuleId, cartId) {
        return _this.cartEditor.addCartRuleToCart(cartRuleId, cartId);
      },
      removeCartRuleFromCart: function removeCartRuleFromCart(cartRuleId, cartId) {
        return _this.cartEditor.removeCartRuleFromCart(cartRuleId, cartId);
      }
    };
  }

  /**
   * Initiates event listeners for cart rule actions
   *
   * @private
   */


  _createClass(CartRuleManager, [{
    key: '_initListeners',
    value: function _initListeners() {
      this._onCartRuleSearch();
      this._onAddCartRuleToCart();
      this._onAddCartRuleToCartFailure();
      this._onRemoveCartRuleFromCart();
    }

    /**
     * Listens for cart rule search action
     *
     * @private
     */

  }, {
    key: '_onCartRuleSearch',
    value: function _onCartRuleSearch() {
      var _this2 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartRuleSearched, function (cartRules) {
        _this2.cartRulesRenderer.renderSearchResults(cartRules);
      });
    }

    /**
     * Listens event of add cart rule to cart action
     *
     * @private
     */

  }, {
    key: '_onAddCartRuleToCart',
    value: function _onAddCartRuleToCart() {
      var _this3 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartRuleAdded, function (cartInfo) {
        _this3.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartInfo.products.length === 0);
      });
    }

    /**
     * Listens event when add cart rule to cart fails
     *
     * @private
     */

  }, {
    key: '_onAddCartRuleToCartFailure',
    value: function _onAddCartRuleToCartFailure() {
      var _this4 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartRuleFailedToAdd, function (message) {
        _this4.cartRulesRenderer.displayErrorMessage(message);
      });
    }

    /**
     * Listens event for remove cart rule from cart action
     *
     * @private
     */

  }, {
    key: '_onRemoveCartRuleFromCart',
    value: function _onRemoveCartRuleFromCart() {
      var _this5 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartRuleRemoved, function (cartInfo) {
        _this5.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartInfo.products.length === 0);
      });
    }

    /**
     * Searches for cart rules by search phrase
     *
     * @private
     */

  }, {
    key: '_search',
    value: function _search(searchPhrase) {
      if (searchPhrase.length < 3) {
        return;
      }

      $.get(this.router.generate('admin_cart_rules_search'), {
        search_phrase: searchPhrase
      }).then(function (cartRules) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartRuleSearched, cartRules);
      }).catch(function (e) {
        showErrorMessage(e.responseJSON.message);
      });
    }
  }]);

  return CartRuleManager;
}();

exports.default = CartRuleManager;

/***/ }),

/***/ 372:
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

var _createOrderMap = __webpack_require__(33);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _customerRenderer = __webpack_require__(373);

var _customerRenderer2 = _interopRequireDefault(_customerRenderer);

var _eventEmitter = __webpack_require__(17);

var _eventMap = __webpack_require__(51);

var _eventMap2 = _interopRequireDefault(_eventMap);

var _router = __webpack_require__(48);

var _router2 = _interopRequireDefault(_router);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Responsible for customers managing. (search, select, get customer info etc.)
 */

var CustomerManager = function () {
  function CustomerManager() {
    var _this = this;

    _classCallCheck(this, CustomerManager);

    this.customerId = null;
    this.activeSearchRequest = null;

    this.router = new _router2.default();
    this.$container = $(_createOrderMap2.default.customerSearchBlock);
    this.$searchInput = $(_createOrderMap2.default.customerSearchInput);
    this.$customerSearchResultBlock = $(_createOrderMap2.default.customerSearchResultsBlock);
    this.customerRenderer = new _customerRenderer2.default();

    this._initListeners();

    return {
      search: function search(searchPhrase) {
        return _this._search(searchPhrase);
      },
      selectCustomer: function selectCustomer(event) {
        return _this._selectCustomer(event);
      },
      loadCustomerCarts: function loadCustomerCarts(currentCartId) {
        return _this._loadCustomerCarts(currentCartId);
      },
      loadCustomerOrders: function loadCustomerOrders() {
        return _this._loadCustomerOrders();
      }
    };
  }

  /**
   * Initializes event listeners
   *
   * @private
   */


  _createClass(CustomerManager, [{
    key: '_initListeners',
    value: function _initListeners() {
      var _this2 = this;

      this.$container.on('click', _createOrderMap2.default.changeCustomerBtn, function () {
        return _this2._changeCustomer();
      });
      this._onCustomerSearch();
      this._onCustomerSelect();
    }

    /**
     * Listens for customer search event
     *
     * @private
     */

  }, {
    key: '_onCustomerSearch',
    value: function _onCustomerSearch() {
      var _this3 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.customerSearched, function (response) {
        _this3.activeSearchRequest = null;
        _this3.customerRenderer.renderSearchResults(response.customers);
      });
    }

    /**
     * Listens for customer select event
     *
     * @private
     */

  }, {
    key: '_onCustomerSelect',
    value: function _onCustomerSelect() {
      var _this4 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.customerSelected, function (event) {
        var $chooseBtn = $(event.currentTarget);
        _this4.customerId = $chooseBtn.data('customer-id');

        _this4.customerRenderer.displaySelectedCustomerBlock($chooseBtn);
      });
    }

    /**
     * Handles use case when customer is changed
     *
     * @private
     */

  }, {
    key: '_changeCustomer',
    value: function _changeCustomer() {
      this.customerRenderer.showCustomerSearch();
    }

    /**
     * Loads customer carts list
     *
     * @param currentCartId
     */

  }, {
    key: '_loadCustomerCarts',
    value: function _loadCustomerCarts(currentCartId) {
      var _this5 = this;

      var customerId = this.customerId;

      $.get(this.router.generate('admin_customers_carts', { customerId: customerId })).then(function (response) {
        _this5.customerRenderer.renderCarts(response.carts, currentCartId);
      }).catch(function (e) {
        showErrorMessage(e.responseJSON.message);
      });
    }

    /**
     * Loads customer orders list
     */

  }, {
    key: '_loadCustomerOrders',
    value: function _loadCustomerOrders() {
      var _this6 = this;

      var customerId = this.customerId;

      $.get(this.router.generate('admin_customers_orders', { customerId: customerId })).then(function (response) {
        _this6.customerRenderer.renderOrders(response.orders);
      }).catch(function (e) {
        showErrorMessage(e.responseJSON.message);
      });
    }

    /**
     * @param {Event} chooseCustomerEvent
     *
     * @return {Number}
     */

  }, {
    key: '_selectCustomer',
    value: function _selectCustomer(chooseCustomerEvent) {
      _eventEmitter.EventEmitter.emit(_eventMap2.default.customerSelected, chooseCustomerEvent);

      return this.customerId;
    }

    /**
     * Searches for customers
     * @todo: fix showing not found customers and rerender after change customer
     * @private
     */

  }, {
    key: '_search',
    value: function _search(searchPhrase) {
      if (searchPhrase.length < 3) {
        return;
      }

      if (this.activeSearchRequest !== null) {
        this.activeSearchRequest.abort();
      }

      var $searchRequest = $.get(this.router.generate('admin_customers_search'), {
        customer_search: searchPhrase
      });
      this.activeSearchRequest = $searchRequest;

      $searchRequest.then(function (response) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.customerSearched, response);
      }).catch(function (response) {
        if (response.statusText === 'abort') {
          return;
        }

        showErrorMessage(response.responseJSON.message);
      });
    }
  }]);

  return CustomerManager;
}();

exports.default = CustomerManager;

/***/ }),

/***/ 373:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

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

var _createOrderMap = __webpack_require__(33);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _router = __webpack_require__(48);

var _router2 = _interopRequireDefault(_router);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Responsible for customer information rendering
 */

var CustomerRenderer = function () {
  function CustomerRenderer() {
    _classCallCheck(this, CustomerRenderer);

    this.$container = $(_createOrderMap2.default.customerSearchBlock);
    this.$customerSearchResultBlock = $(_createOrderMap2.default.customerSearchResultsBlock);
    this.router = new _router2.default();
  }

  /**
   * Renders customer search results
   *
   * @param foundCustomers
   */


  _createClass(CustomerRenderer, [{
    key: 'renderSearchResults',
    value: function renderSearchResults(foundCustomers) {
      this._clearShownCustomers();

      if (foundCustomers.length === 0) {
        this._showNotFoundCustomers();

        return;
      }

      for (var customerId in foundCustomers) {
        var customerResult = foundCustomers[customerId];
        var customer = {
          id: customerId,
          firstName: customerResult.firstname,
          lastName: customerResult.lastname,
          email: customerResult.email,
          birthday: customerResult.birthday !== '0000-00-00' ? customerResult.birthday : ' '
        };

        this._renderFoundCustomer(customer);
      }
    }

    /**
     * Responsible for displaying customer block after customer select
     *
     * @param $targetedBtn
     */

  }, {
    key: 'displaySelectedCustomerBlock',
    value: function displaySelectedCustomerBlock($targetedBtn) {
      $targetedBtn.addClass('d-none');

      var $customerCard = $targetedBtn.closest('.card');

      $customerCard.addClass('border-success');
      $customerCard.find(_createOrderMap2.default.changeCustomerBtn).removeClass('d-none');

      this.$container.find(_createOrderMap2.default.customerSearchRow).addClass('d-none');
      this.$container.find(_createOrderMap2.default.notSelectedCustomerSearchResults).closest(_createOrderMap2.default.customerSearchResultColumn).remove();
    }

    /**
     * Shows customer search block
     */

  }, {
    key: 'showCustomerSearch',
    value: function showCustomerSearch() {
      this.$container.find(_createOrderMap2.default.customerSearchRow).removeClass('d-none');
    }

    /**
     * Renders customer carts list
     *
     * @param {Array} carts
     * @param {Int} currentCartId
     */

  }, {
    key: 'renderCarts',
    value: function renderCarts(carts, currentCartId) {
      var $cartsTable = $(_createOrderMap2.default.customerCartsTable);
      var $cartsTableRowTemplate = $($(_createOrderMap2.default.customerCartsTableRowTemplate).html());

      $cartsTable.find('tbody').empty();

      if (carts.length === 0) {
        return;
      }

      this._showCheckoutHistoryBlock();

      for (var key in carts) {
        var cart = carts[key];
        // do not render current cart
        if (cart.cartId === currentCartId) {
          continue;
        }
        var $template = $cartsTableRowTemplate.clone();

        $template.find(_createOrderMap2.default.cartIdField).text(cart.cartId);
        $template.find(_createOrderMap2.default.cartDateField).text(cart.creationDate);
        $template.find(_createOrderMap2.default.cartTotalField).text(cart.totalPrice);
        $template.find(_createOrderMap2.default.cartDetailsBtn).prop('href', this.router.generate('admin_carts_view', { cartId: cart.cartId }));

        $template.find(_createOrderMap2.default.useCartBtn).data('cart-id', cart.cartId);

        $cartsTable.find('tbody').append($template);
      }
    }

    /**
     * Renders customer orders list
     *
     * @param {Array} orders
     */

  }, {
    key: 'renderOrders',
    value: function renderOrders(orders) {
      var $ordersTable = $(_createOrderMap2.default.customerOrdersTable);
      var $rowTemplate = $($(_createOrderMap2.default.customerOrdersTableRowTemplate).html());

      $ordersTable.find('tbody').empty();

      if (orders.length === 0) {
        return;
      }

      this._showCheckoutHistoryBlock();

      for (var key in Object.keys(orders)) {
        var order = orders[key];
        var $template = $rowTemplate.clone();

        $template.find(_createOrderMap2.default.orderIdField).text(order.orderId);
        $template.find(_createOrderMap2.default.orderDateField).text(order.orderPlacedDate);
        $template.find(_createOrderMap2.default.orderProductsField).text(order.totalProductsCount);
        $template.find(_createOrderMap2.default.orderTotalField).text(order.totalPaid);
        $template.find(_createOrderMap2.default.orderStatusField).text(order.orderStatus);
        $template.find(_createOrderMap2.default.orderDetailsBtn).prop('href', this.router.generate('admin_orders_view', { orderId: order.orderId }));

        $template.find(_createOrderMap2.default.useOrderBtn).data('order-id', order.orderId);

        $ordersTable.find('tbody').append($template);
      }
    }

    /**
     * Renders customer information after search action
     *
     * @param {Object} customer
     *
     * @return {jQuery}
     *
     * @private
     */

  }, {
    key: '_renderFoundCustomer',
    value: function _renderFoundCustomer(customer) {
      var $customerSearchResultTemplate = $($(_createOrderMap2.default.customerSearchResultTemplate).html());
      var $template = $customerSearchResultTemplate.clone();

      $template.find(_createOrderMap2.default.customerSearchResultName).text(customer.firstName + ' ' + customer.lastName);
      $template.find(_createOrderMap2.default.customerSearchResultEmail).text(customer.email);
      $template.find(_createOrderMap2.default.customerSearchResultId).text(customer.id);
      $template.find(_createOrderMap2.default.customerSearchResultBirthday).text(customer.birthday);
      $template.find(_createOrderMap2.default.chooseCustomerBtn).data('customer-id', customer.id);
      $template.find(_createOrderMap2.default.customerDetailsBtn).prop('href', this.router.generate('admin_customers_view', { customerId: customer.id }));

      return this.$customerSearchResultBlock.append($template);
    }

    /**
     * Shows checkout history block where carts and orders are rendered
     *
     * @private
     */

  }, {
    key: '_showCheckoutHistoryBlock',
    value: function _showCheckoutHistoryBlock() {
      $(_createOrderMap2.default.customerCheckoutHistory).removeClass('d-none');
    }

    /**
     * Clears shown customers
     *
     * @private
     */

  }, {
    key: '_clearShownCustomers',
    value: function _clearShownCustomers() {
      this.$customerSearchResultBlock.empty();
    }

    /**
     * Shows empty result when customer is not found
     *
     * @private
     */

  }, {
    key: '_showNotFoundCustomers',
    value: function _showNotFoundCustomers() {
      var $emptyResultTemplate = $($('#customerSearchEmptyResultTemplate').html());

      this.$customerSearchResultBlock.append($emptyResultTemplate);
    }
  }]);

  return CustomerRenderer;
}();

exports.default = CustomerRenderer;

/***/ }),

/***/ 374:
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

var _cartEditor = __webpack_require__(73);

var _cartEditor2 = _interopRequireDefault(_cartEditor);

var _createOrderMap = __webpack_require__(33);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _eventMap = __webpack_require__(51);

var _eventMap2 = _interopRequireDefault(_eventMap);

var _eventEmitter = __webpack_require__(17);

var _productRenderer = __webpack_require__(101);

var _productRenderer2 = _interopRequireDefault(_productRenderer);

var _router = __webpack_require__(48);

var _router2 = _interopRequireDefault(_router);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Product component Object for "Create order" page
 */

var ProductManager = function () {
  function ProductManager() {
    var _this = this;

    _classCallCheck(this, ProductManager);

    this.products = {};
    this.selectedProductId = null;
    this.selectedCombinationId = null;
    this.activeSearchRequest = null;

    this.productRenderer = new _productRenderer2.default();
    this.router = new _router2.default();
    this.cartEditor = new _cartEditor2.default();

    this._initListeners();

    return {
      search: function search(searchPhrase) {
        return _this._search(searchPhrase);
      },
      addProductToCart: function addProductToCart(cartId) {
        return _this.cartEditor.addProduct(cartId, _this._getProductData());
      },
      removeProductFromCart: function removeProductFromCart(cartId, product) {
        return _this.cartEditor.removeProductFromCart(cartId, product);
      }
    };
  }

  /**
   * Initializes event listeners
   *
   * @private
   */


  _createClass(ProductManager, [{
    key: '_initListeners',
    value: function _initListeners() {
      var _this2 = this;

      $(_createOrderMap2.default.productSelect).on('change', function (e) {
        return _this2._initProductSelect(e);
      });
      $(_createOrderMap2.default.combinationsSelect).on('change', function (e) {
        return _this2._initCombinationSelect(e);
      });

      this._onProductSearch();
      this._onAddProductToCart();
      this._onRemoveProductFromCart();
    }

    /**
     * Listens for product search event
     *
     * @private
     */

  }, {
    key: '_onProductSearch',
    value: function _onProductSearch() {
      var _this3 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.productSearched, function (response) {
        _this3.products = JSON.parse(response);
        _this3.productRenderer.renderSearchResults(_this3.products);
        _this3._selectFirstResult();
      });
    }

    /**
     * Listens for add product to cart event
     *
     * @private
     */

  }, {
    key: '_onAddProductToCart',
    value: function _onAddProductToCart() {
      _eventEmitter.EventEmitter.on(_eventMap2.default.productAddedToCart, function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartLoaded, cartInfo);
      });
    }

    /**
     * Listens for remove product from cart event
     *
     * @private
     */

  }, {
    key: '_onRemoveProductFromCart',
    value: function _onRemoveProductFromCart() {
      _eventEmitter.EventEmitter.on(_eventMap2.default.productRemovedFromCart, function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartLoaded, cartInfo);
      });
    }

    /**
     * Initializes product select
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initProductSelect',
    value: function _initProductSelect(event) {
      var productId = Number($(event.currentTarget).find(':selected').val());
      this._selectProduct(productId);
    }

    /**
     * Initializes combination select
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initCombinationSelect',
    value: function _initCombinationSelect(event) {
      var combinationId = Number($(event.currentTarget).find(':selected').val());
      this._selectCombination(combinationId);
    }

    /**
     * Searches for product
     *
     * @private
     */

  }, {
    key: '_search',
    value: function _search(searchPhrase) {
      if (searchPhrase.length < 3) {
        return;
      }

      if (this.activeSearchRequest !== null) {
        this.activeSearchRequest.abort();
      }

      $.get(this.router.generate('admin_products_search'), {
        search_phrase: searchPhrase
      }).then(function (response) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.productSearched, response);
      }).catch(function (response) {
        if (response.statusText === 'abort') {
          return;
        }

        showErrorMessage(response.responseJSON.message);
      });
    }

    /**
     * Initiate first result dataset after search
     *
     * @private
     */

  }, {
    key: '_selectFirstResult',
    value: function _selectFirstResult() {
      this._unsetProduct();

      if (this.products.length !== 0) {
        this._selectProduct(Object.keys(this.products)[0]);
      }
    }

    /**
     * Handles use case when product is selected from search results
     *
     * @private
     *
     * @param productId
     */

  }, {
    key: '_selectProduct',
    value: function _selectProduct(productId) {
      this._unsetCombination();

      this.selectedProductId = productId;
      var product = this.products[productId];

      this.productRenderer.renderProductMetadata(product);

      // if product has combinations select the first else leave it null
      if (product.combinations.length !== 0) {
        this._selectCombination(Object.keys(product.combinations)[0]);
      }

      return product;
    }

    /**
     * Handles use case when new combination is selected
     *
     * @param combinationId
     *
     * @private
     */

  }, {
    key: '_selectCombination',
    value: function _selectCombination(combinationId) {
      var combination = this.products[this.selectedProductId].combinations[combinationId];

      this.selectedCombinationId = combinationId;
      this.productRenderer.renderStock(combination.stock);

      return combination;
    }

    /**
     * Sets the selected combination id to null
     *
     * @private
     */

  }, {
    key: '_unsetCombination',
    value: function _unsetCombination() {
      this.selectedCombinationId = null;
    }

    /**
     * Sets the selected product id to null
     *
     * @private
     */

  }, {
    key: '_unsetProduct',
    value: function _unsetProduct() {
      this.selectedProductId = null;
    }

    /**
     * Retrieves product data from product search result block fields
     *
     * @returns {FormData}
     * @private
     */

  }, {
    key: '_getProductData',
    value: function _getProductData() {
      var formData = new FormData();

      formData.append('productId', this.selectedProductId);
      formData.append('quantity', $(_createOrderMap2.default.quantityInput).val());
      formData.append('combinationId', this.selectedCombinationId);

      this._getCustomFieldsData(formData);

      return formData;
    }

    /**
     * Resolves product customization fields to be added to formData object
     *
     * @param {FormData} formData
     *
     * @returns {FormData}
     *
     * @private
     */

  }, {
    key: '_getCustomFieldsData',
    value: function _getCustomFieldsData(formData) {
      var $customFields = $(_createOrderMap2.default.productCustomInput);

      $customFields.each(function (key, field) {
        var $field = $(field);
        var name = $field.attr('name');

        if ($field.attr('type') === 'file') {
          formData.append(name, $field[0].files[0]);
        } else {
          formData.append(name, $field.val());
        }
      });

      return formData;
    }
  }]);

  return ProductManager;
}();

exports.default = ProductManager;

/***/ }),

/***/ 375:
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

var _createOrderMap = __webpack_require__(33);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Manupulates UI of Shipping block in Order creation page
 */

var ShippingRenderer = function () {
  function ShippingRenderer() {
    _classCallCheck(this, ShippingRenderer);

    this.$container = $(_createOrderMap2.default.shippingBlock);
    this.$form = $(_createOrderMap2.default.shippingForm);
    this.$noCarrierBlock = $(_createOrderMap2.default.noCarrierBlock);
  }

  /**
   * @param {Object} shipping
   * @param {Boolean} emptyCart
   */


  _createClass(ShippingRenderer, [{
    key: 'render',
    value: function render(shipping, emptyCart) {
      var shippingIsAvailable = typeof shipping !== 'undefined' && shipping !== null && shipping.length !== 0;

      if (emptyCart) {
        this._hideContainer();
      } else if (shippingIsAvailable) {
        this._displayForm(shipping);
      } else {
        this._displayNoCarriersWarning();
      }
    }

    /**
     * Show form block with rendered delivery options instead of warning message
     *
     * @param shipping
     *
     * @private
     */

  }, {
    key: '_displayForm',
    value: function _displayForm(shipping) {
      this._hideNoCarrierBlock();
      this._renderDeliveryOptions(shipping.deliveryOptions, shipping.selectedCarrierId);
      this._renderTotalShipping(shipping.shippingPrice);
      this._showForm();
      this._showContainer();
    }

    /**
     * Show warning message that no carriers are available and hide form block
     *
     * @private
     */

  }, {
    key: '_displayNoCarriersWarning',
    value: function _displayNoCarriersWarning() {
      this._showContainer();
      this._hideForm();
      this._showNoCarrierBlock();
    }

    /**
     * Renders delivery options selection block
     *
     * @param deliveryOptions
     * @param selectedVal
     *
     * @private
     */

  }, {
    key: '_renderDeliveryOptions',
    value: function _renderDeliveryOptions(deliveryOptions, selectedVal) {
      var $deliveryOptionSelect = $(_createOrderMap2.default.deliveryOptionSelect);
      $deliveryOptionSelect.empty();

      for (var key in Object.keys(deliveryOptions)) {
        var option = deliveryOptions[key];

        var deliveryOption = {
          value: option.carrierId,
          text: option.carrierName + ' - ' + option.carrierDelay
        };

        if (selectedVal === deliveryOption.value) {
          deliveryOption.selected = 'selected';
        }

        $deliveryOptionSelect.append($('<option>', deliveryOption));
      }
    }

    /**
     * Renders dynamic value of shipping price
     *
     * @param shippingPrice
     *
     * @private
     */

  }, {
    key: '_renderTotalShipping',
    value: function _renderTotalShipping(shippingPrice) {
      var $totalShippingField = $(_createOrderMap2.default.totalShippingField);
      $totalShippingField.empty();

      $totalShippingField.append(shippingPrice);
    }

    /**
     * Show whole shipping container
     *
     * @private
     */

  }, {
    key: '_showContainer',
    value: function _showContainer() {
      this.$container.removeClass('d-none');
    }

    /**
     * Hide whole shipping container
     *
     * @private
     */

  }, {
    key: '_hideContainer',
    value: function _hideContainer() {
      this.$container.addClass('d-none');
    }

    /**
     * Show form block
     *
     * @private
     */

  }, {
    key: '_showForm',
    value: function _showForm() {
      this.$form.removeClass('d-none');
    }

    /**
     * Hide form block
     *
     * @private
     */

  }, {
    key: '_hideForm',
    value: function _hideForm() {
      this.$form.addClass('d-none');
    }

    /**
     * Show warning message block which warns that no carriers are available
     *
     * @private
     */

  }, {
    key: '_showNoCarrierBlock',
    value: function _showNoCarrierBlock() {
      this.$noCarrierBlock.removeClass('d-none');
    }

    /**
     * Hide warning message block which warns that no carriers are available
     *
     * @private
     */

  }, {
    key: '_hideNoCarrierBlock',
    value: function _hideNoCarrierBlock() {
      this.$noCarrierBlock.addClass('d-none');
    }
  }]);

  return ShippingRenderer;
}();

exports.default = ShippingRenderer;

/***/ }),

/***/ 449:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
var _extends=Object.assign||function(a){for(var b,c=1;c<arguments.length;c++)for(var d in b=arguments[c],b)Object.prototype.hasOwnProperty.call(b,d)&&(a[d]=b[d]);return a},_typeof='function'==typeof Symbol&&'symbol'==typeof Symbol.iterator?function(a){return typeof a}:function(a){return a&&'function'==typeof Symbol&&a.constructor===Symbol&&a!==Symbol.prototype?'symbol':typeof a};function _classCallCheck(a,b){if(!(a instanceof b))throw new TypeError('Cannot call a class as a function')}var Routing=function a(){var b=this;_classCallCheck(this,a),this.setRoutes=function(a){b.routesRouting=a||[]},this.getRoutes=function(){return b.routesRouting},this.setBaseUrl=function(a){b.contextRouting.base_url=a},this.getBaseUrl=function(){return b.contextRouting.base_url},this.setPrefix=function(a){b.contextRouting.prefix=a},this.setScheme=function(a){b.contextRouting.scheme=a},this.getScheme=function(){return b.contextRouting.scheme},this.setHost=function(a){b.contextRouting.host=a},this.getHost=function(){return b.contextRouting.host},this.buildQueryParams=function(a,c,d){var e=new RegExp(/\[]$/);c instanceof Array?c.forEach(function(c,f){e.test(a)?d(a,c):b.buildQueryParams(a+'['+('object'===('undefined'==typeof c?'undefined':_typeof(c))?f:'')+']',c,d)}):'object'===('undefined'==typeof c?'undefined':_typeof(c))?Object.keys(c).forEach(function(e){return b.buildQueryParams(a+'['+e+']',c[e],d)}):d(a,c)},this.getRoute=function(a){var c=b.contextRouting.prefix+a;if(!!b.routesRouting[c])return b.routesRouting[c];else if(!b.routesRouting[a])throw new Error('The route "'+a+'" does not exist.');return b.routesRouting[a]},this.generate=function(a,c,d){var e=b.getRoute(a),f=c||{},g=_extends({},f),h='_scheme',i='',j=!0,k='';if((e.tokens||[]).forEach(function(b){if('text'===b[0])return i=b[1]+i,void(j=!1);if('variable'===b[0]){var c=(e.defaults||{})[b[3]];if(!1==j||!c||(f||{})[b[3]]&&f[b[3]]!==e.defaults[b[3]]){var d;if((f||{})[b[3]])d=f[b[3]],delete g[b[3]];else if(c)d=e.defaults[b[3]];else{if(j)return;throw new Error('The route "'+a+'" requires the parameter "'+b[3]+'".')}var h=!0===d||!1===d||''===d;if(!h||!j){var k=encodeURIComponent(d).replace(/%2F/g,'/');'null'===k&&null===d&&(k=''),i=b[1]+k+i}j=!1}else c&&delete g[b[3]];return}throw new Error('The token type "'+b[0]+'" is not supported.')}),''==i&&(i='/'),(e.hosttokens||[]).forEach(function(a){var b;return'text'===a[0]?void(k=a[1]+k):void('variable'===a[0]&&((f||{})[a[3]]?(b=f[a[3]],delete g[a[3]]):e.defaults[a[3]]&&(b=e.defaults[a[3]]),k=a[1]+b+k))}),i=b.contextRouting.base_url+i,e.requirements[h]&&b.getScheme()!==e.requirements[h]?i=e.requirements[h]+'://'+(k||b.getHost())+i:k&&b.getHost()!==k?i=b.getScheme()+'://'+k+i:!0===d&&(i=b.getScheme()+'://'+b.getHost()+i),0<Object.keys(g).length){var l=[],m=function(a,b){var c=b;c='function'==typeof c?c():c,c=null===c?'':c,l.push(encodeURIComponent(a)+'='+encodeURIComponent(c))};Object.keys(g).forEach(function(a){return b.buildQueryParams(a,g[a],m)}),i=i+'?'+l.join('&').replace(/%20/g,'+')}return i},this.setData=function(a){b.setBaseUrl(a.base_url),b.setRoutes(a.routes),'prefix'in a&&b.setPrefix(a.prefix),b.setHost(a.host),b.setScheme(a.scheme)},this.contextRouting={base_url:'',prefix:'',host:'',scheme:''}};module.exports=new Routing;

/***/ }),

/***/ 48:
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

var _fosRouting = __webpack_require__(449);

var _fosRouting2 = _interopRequireDefault(_fosRouting);

var _fos_js_routes = __webpack_require__(314);

var _fos_js_routes2 = _interopRequireDefault(_fos_js_routes);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Wraps FOSJsRoutingbundle with exposed routes.
 * To expose route add option `expose: true` in .yml routing config
 *
 * e.g.
 *
 * `my_route
 *    path: /my-path
 *    options:
 *      expose: true
 * `
 * And run `bin/console fos:js-routing:dump --format=json --target=admin-dev/themes/new-theme/js/fos_js_routes.json`
 */

var Router = function () {
  function Router() {
    _classCallCheck(this, Router);

    _fosRouting2.default.setData(_fos_js_routes2.default);
    _fosRouting2.default.setBaseUrl($(document).find('body').data('base-url'));

    return this;
  }

  /**
   * Decorated "generate" method, with predefined security token in params
   *
   * @param route
   * @param params
   *
   * @returns {String}
   */


  _createClass(Router, [{
    key: 'generate',
    value: function generate(route) {
      var params = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

      var tokenizedParams = Object.assign(params, { _token: $(document).find('body').data('token') });

      return _fosRouting2.default.generate(route, tokenizedParams);
    }
  }]);

  return Router;
}();

exports.default = Router;

/***/ }),

/***/ 51:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


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
 * Encapsulates js events used in create order page
 */
exports.default = {
  // when customer search action is done
  customerSearched: 'customerSearched',
  // when new customer is selected
  customerSelected: 'customerSelected',
  // when new cart is loaded, no matter if its empty, selected from carts list or duplicated by order.
  cartLoaded: 'cartLoaded',
  // when cart addresses information has been changed
  cartAddressesChanged: 'cartAddressesChanged',
  // when cart delivery option has been changed
  cartDeliveryOptionChanged: 'cartDeliveryOptionChanged',
  // when cart free shipping value has been changed
  cartFreeShippingSet: 'cartFreeShippingSet',
  // when cart rules search action is done
  cartRuleSearched: 'cartRuleSearched',
  // when cart rule is removed from cart
  cartRuleRemoved: 'cartRuleRemoved',
  // when cart rule is added to cart
  cartRuleAdded: 'cartRuleAdded',
  // when cart rule cannot be added to cart
  cartRuleFailedToAdd: 'cartRuleFailedToAdd',
  // when product search action is done
  productSearched: 'productSearched',
  // when product is added to cart
  productAddedToCart: 'productAddedToCart',
  // when product is removed from cart
  productRemovedFromCart: 'productRemovedFromCart'
};

/***/ }),

/***/ 73:
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

var _router = __webpack_require__(48);

var _router2 = _interopRequireDefault(_router);

var _eventEmitter = __webpack_require__(17);

var _eventMap = __webpack_require__(51);

var _eventMap2 = _interopRequireDefault(_eventMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Provides ajax calls for cart editing actions
 * Each method emits an event with updated cart information after success.
 */

var CartEditor = function () {
  function CartEditor() {
    _classCallCheck(this, CartEditor);

    this.router = new _router2.default();
  }

  /**
   * Changes cart addresses
   *
   * @param {Number} cartId
   * @param {Object} addresses
   */


  _createClass(CartEditor, [{
    key: 'changeCartAddresses',
    value: function changeCartAddresses(cartId, addresses) {
      $.post(this.router.generate('admin_carts_edit_addresses', { cartId: cartId }), addresses).then(function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartAddressesChanged, cartInfo);
      });
    }

    /**
     * Modifies cart delivery option
     *
     * @param {Number} cartId
     * @param {Number} value
     */

  }, {
    key: 'changeDeliveryOption',
    value: function changeDeliveryOption(cartId, value) {
      $.post(this.router.generate('admin_carts_edit_carrier', { cartId: cartId }), {
        carrierId: value
      }).then(function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartDeliveryOptionChanged, cartInfo);
      });
    }

    /**
     * Changes cart free shipping value
     *
     * @param {Number} cartId
     * @param {Boolean} value
     */

  }, {
    key: 'setFreeShipping',
    value: function setFreeShipping(cartId, value) {
      $.post(this.router.generate('admin_carts_set_free_shipping', { cartId: cartId }), {
        freeShipping: value
      }).then(function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartFreeShippingSet, cartInfo);
      });
    }

    /**
     * Adds cart rule to cart
     *
     * @param {Number} cartRuleId
     * @param {Number} cartId
     */

  }, {
    key: 'addCartRuleToCart',
    value: function addCartRuleToCart(cartRuleId, cartId) {
      $.post(this.router.generate('admin_carts_add_cart_rule', { cartId: cartId }), {
        cartRuleId: cartRuleId
      }).then(function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartRuleAdded, cartInfo);
      }).catch(function (response) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartRuleFailedToAdd, response.responseJSON.message);
      });
    }

    /**
     * Removes cart rule from cart
     *
     * @param {Number} cartRuleId
     * @param {Number} cartId
     */

  }, {
    key: 'removeCartRuleFromCart',
    value: function removeCartRuleFromCart(cartRuleId, cartId) {
      $.post(this.router.generate('admin_carts_delete_cart_rule', {
        cartId: cartId,
        cartRuleId: cartRuleId
      })).then(function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartRuleRemoved, cartInfo);
      }).catch(function (response) {
        showErrorMessage(response.responseJSON.message);
      });
    }

    /**
     * Adds product to cart
     *
     * @param {Number} cartId
     * @param {FormData} product
     */

  }, {
    key: 'addProduct',
    value: function addProduct(cartId, product) {
      $.ajax(this.router.generate('admin_carts_add_product', { cartId: cartId }), {
        method: 'POST',
        data: product,
        processData: false,
        contentType: false
      }).then(function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.productAddedToCart, cartInfo);
      }).catch(function (response) {
        showErrorMessage(response.responseJSON.message);
      });
    }

    /**
     * Removes product from cart
     *
     * @param {Number} cartId
     * @param {Object} product
     */

  }, {
    key: 'removeProductFromCart',
    value: function removeProductFromCart(cartId, product) {
      $.post(this.router.generate('admin_carts_delete_product', { cartId: cartId }), {
        productId: product.productId,
        attributeId: product.attributeId,
        customizationId: product.customizationId
      }).then(function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.productRemovedFromCart, cartInfo);
      }).catch(function (response) {
        showErrorMessage(response.responseJSON.message);
      });
    }
  }]);

  return CartEditor;
}();

exports.default = CartEditor;

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMWU2NjI2MzkwMGU5NjZkZmJiZjA/ODU5MCoqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL2NhcnQtcnVsZXMtcmVuZGVyZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL3Byb2R1Y3QtcmVuZGVyZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ldmVudC1lbWl0dGVyLmpzIiwid2VicGFjazovLy8uL34vZXZlbnRzL2V2ZW50cy5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY3JlYXRlLW9yZGVyLXBhZ2UuanMiLCJ3ZWJwYWNrOi8vLy4vanMvZm9zX2pzX3JvdXRlcy5qc29uIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jcmVhdGUtb3JkZXItbWFwLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvYWRkcmVzc2VzLXJlbmRlcmVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LXByb3ZpZGVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LXJ1bGUtbWFuYWdlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY3VzdG9tZXItbWFuYWdlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY3VzdG9tZXItcmVuZGVyZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL3Byb2R1Y3QtbWFuYWdlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvc2hpcHBpbmctcmVuZGVyZXIuanMiLCJ3ZWJwYWNrOi8vLy4vfi9mb3Mtcm91dGluZy9kaXN0L3JvdXRpbmcuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9yb3V0ZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL2V2ZW50LW1hcC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY2FydC1lZGl0b3IuanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIkNhcnRSdWxlc1JlbmRlcmVyIiwiJGNhcnRSdWxlc0Jsb2NrIiwiY3JlYXRlT3JkZXJNYXAiLCJjYXJ0UnVsZXNCbG9jayIsIiRjYXJ0UnVsZXNUYWJsZSIsImNhcnRSdWxlc1RhYmxlIiwiJHNlYXJjaFJlc3VsdEJveCIsImNhcnRSdWxlc1NlYXJjaFJlc3VsdEJveCIsImNhcnRSdWxlcyIsImVtcHR5Q2FydCIsIl9oaWRlRXJyb3JCbG9jayIsIl9oaWRlQ2FydFJ1bGVzQmxvY2siLCJfc2hvd0NhcnRSdWxlc0Jsb2NrIiwibGVuZ3RoIiwiX2hpZGVDYXJ0UnVsZXNMaXN0IiwiX3JlbmRlckxpc3QiLCJzZWFyY2hSZXN1bHRzIiwiX2NsZWFyU2VhcmNoUmVzdWx0cyIsImNhcnRfcnVsZXMiLCJfcmVuZGVyTm90Rm91bmQiLCJfcmVuZGVyRm91bmRDYXJ0UnVsZXMiLCJfc2hvd1Jlc3VsdHNEcm9wZG93biIsIm1lc3NhZ2UiLCJjYXJ0UnVsZUVycm9yVGV4dCIsInRleHQiLCJfc2hvd0Vycm9yQmxvY2siLCJhZGRDbGFzcyIsInJlbW92ZUNsYXNzIiwiJHRlbXBsYXRlIiwiY2FydFJ1bGVzTm90Rm91bmRUZW1wbGF0ZSIsImh0bWwiLCJjbG9uZSIsImVtcHR5IiwiJGNhcnRSdWxlVGVtcGxhdGUiLCJmb3VuZENhcnRSdWxlVGVtcGxhdGUiLCJrZXkiLCJjYXJ0UnVsZSIsImNhcnRSdWxlTmFtZSIsIm5hbWUiLCJjb2RlIiwiZGF0YSIsImNhcnRSdWxlSWQiLCJhcHBlbmQiLCJfY2xlYW5DYXJ0UnVsZXNMaXN0IiwiJGNhcnRSdWxlc1RhYmxlUm93VGVtcGxhdGUiLCJjYXJ0UnVsZXNUYWJsZVJvd1RlbXBsYXRlIiwiZmluZCIsImNhcnRSdWxlTmFtZUZpZWxkIiwiY2FydFJ1bGVEZXNjcmlwdGlvbkZpZWxkIiwiZGVzY3JpcHRpb24iLCJjYXJ0UnVsZVZhbHVlRmllbGQiLCJ2YWx1ZSIsImNhcnRSdWxlRGVsZXRlQnRuIiwiX3Nob3dDYXJ0UnVsZXNMaXN0IiwiY2FydFJ1bGVFcnJvckJsb2NrIiwiUHJvZHVjdFJlbmRlcmVyIiwiJHByb2R1Y3RzVGFibGUiLCJwcm9kdWN0c1RhYmxlIiwicHJvZHVjdHMiLCJfY2xlYW5Qcm9kdWN0c0xpc3QiLCJfaGlkZVByb2R1Y3RzTGlzdCIsIiRwcm9kdWN0c1RhYmxlUm93VGVtcGxhdGUiLCJwcm9kdWN0c1RhYmxlUm93VGVtcGxhdGUiLCJwcm9kdWN0IiwicHJvZHVjdEltYWdlRmllbGQiLCJpbWFnZUxpbmsiLCJwcm9kdWN0TmFtZUZpZWxkIiwicHJvZHVjdEF0dHJGaWVsZCIsImF0dHJpYnV0ZSIsInByb2R1Y3RSZWZlcmVuY2VGaWVsZCIsInJlZmVyZW5jZSIsInByb2R1Y3RVbml0UHJpY2VJbnB1dCIsInVuaXRQcmljZSIsInByb2R1Y3RUb3RhbFByaWNlRmllbGQiLCJwcmljZSIsInByb2R1Y3RSZW1vdmVCdG4iLCJwcm9kdWN0SWQiLCJhdHRyaWJ1dGVJZCIsImN1c3RvbWl6YXRpb25JZCIsIl9zaG93VGF4V2FybmluZyIsIl9zaG93UHJvZHVjdHNMaXN0IiwiZm91bmRQcm9kdWN0cyIsIl9jbGVhblNlYXJjaFJlc3VsdHMiLCJfc2hvd05vdEZvdW5kIiwiX2hpZGVUYXhXYXJuaW5nIiwiX3JlbmRlckZvdW5kUHJvZHVjdHMiLCJfaGlkZU5vdEZvdW5kIiwiX3Nob3dSZXN1bHRCbG9jayIsInJlbmRlclN0b2NrIiwic3RvY2siLCJfcmVuZGVyQ29tYmluYXRpb25zIiwiY29tYmluYXRpb25zIiwiX3JlbmRlckN1c3RvbWl6YXRpb25zIiwiY3VzdG9taXphdGlvbl9maWVsZHMiLCJpblN0b2NrQ291bnRlciIsInF1YW50aXR5SW5wdXQiLCJhdHRyIiwiZm9ybWF0dGVkX3ByaWNlIiwicHJvZHVjdFNlbGVjdCIsInByb2R1Y3RfaWQiLCJjb21iaW5hdGlvbnNTZWxlY3QiLCJfY2xlYW5Db21iaW5hdGlvbnMiLCJfaGlkZUNvbWJpbmF0aW9ucyIsImNvbWJpbmF0aW9uIiwiYXR0cmlidXRlX2NvbWJpbmF0aW9uX2lkIiwiX3Nob3dDb21iaW5hdGlvbnMiLCJjdXN0b21pemF0aW9uRmllbGRzIiwiZmllbGRUeXBlRmlsZSIsImZpZWxkVHlwZVRleHQiLCJfY2xlYW5DdXN0b21pemF0aW9ucyIsIl9oaWRlQ3VzdG9taXphdGlvbnMiLCIkY3VzdG9tRmllbGRzQ29udGFpbmVyIiwicHJvZHVjdEN1c3RvbUZpZWxkc0NvbnRhaW5lciIsIiRmaWxlSW5wdXRUZW1wbGF0ZSIsInByb2R1Y3RDdXN0b21GaWxlVGVtcGxhdGUiLCIkdGV4dElucHV0VGVtcGxhdGUiLCJwcm9kdWN0Q3VzdG9tVGV4dFRlbXBsYXRlIiwidGVtcGxhdGVUeXBlTWFwIiwiY3VzdG9tRmllbGQiLCJ0eXBlIiwicHJvZHVjdEN1c3RvbUlucHV0IiwiY3VzdG9taXphdGlvbl9maWVsZF9pZCIsInByb2R1Y3RDdXN0b21JbnB1dExhYmVsIiwiX3Nob3dDdXN0b21pemF0aW9ucyIsInByb2R1Y3RDdXN0b21pemF0aW9uQ29udGFpbmVyIiwicHJvZHVjdFJlc3VsdEJsb2NrIiwiY29tYmluYXRpb25zUm93IiwicHJvZHVjdFRheFdhcm5pbmciLCJub1Byb2R1Y3RzRm91bmRXYXJuaW5nIiwiRXZlbnRFbWl0dGVyIiwiRXZlbnRFbWl0dGVyQ2xhc3MiLCJDcmVhdGVPcmRlclBhZ2UiLCJjYXJ0SWQiLCIkY29udGFpbmVyIiwib3JkZXJDcmVhdGlvbkNvbnRhaW5lciIsImNhcnRQcm92aWRlciIsIkNhcnRQcm92aWRlciIsImN1c3RvbWVyTWFuYWdlciIsIkN1c3RvbWVyTWFuYWdlciIsInNoaXBwaW5nUmVuZGVyZXIiLCJTaGlwcGluZ1JlbmRlcmVyIiwiYWRkcmVzc2VzUmVuZGVyZXIiLCJBZGRyZXNzZXNSZW5kZXJlciIsImNhcnRSdWxlc1JlbmRlcmVyIiwicm91dGVyIiwiUm91dGVyIiwiY2FydEVkaXRvciIsIkNhcnRFZGl0b3IiLCJjYXJ0UnVsZU1hbmFnZXIiLCJDYXJ0UnVsZU1hbmFnZXIiLCJwcm9kdWN0TWFuYWdlciIsIlByb2R1Y3RNYW5hZ2VyIiwicHJvZHVjdFJlbmRlcmVyIiwiX2luaXRMaXN0ZW5lcnMiLCJvbiIsImN1c3RvbWVyU2VhcmNoSW5wdXQiLCJfaW5pdEN1c3RvbWVyU2VhcmNoIiwiZSIsImNob29zZUN1c3RvbWVyQnRuIiwiX2luaXRDdXN0b21lclNlbGVjdCIsInVzZUNhcnRCdG4iLCJfaW5pdENhcnRTZWxlY3QiLCJ1c2VPcmRlckJ0biIsIl9pbml0RHVwbGljYXRlT3JkZXJDYXJ0IiwicHJvZHVjdFNlYXJjaCIsIl9pbml0UHJvZHVjdFNlYXJjaCIsImNhcnRSdWxlU2VhcmNoSW5wdXQiLCJfaW5pdENhcnRSdWxlU2VhcmNoIiwic3RvcFNlYXJjaGluZyIsIl9pbml0Q2FydEVkaXRpbmciLCJfb25DYXJ0TG9hZGVkIiwiX29uQ2FydEFkZHJlc3Nlc0NoYW5nZWQiLCJkZWxpdmVyeU9wdGlvblNlbGVjdCIsImNoYW5nZURlbGl2ZXJ5T3B0aW9uIiwiY3VycmVudFRhcmdldCIsImZyZWVTaGlwcGluZ1N3aXRjaCIsInNldEZyZWVTaGlwcGluZyIsImFkZFRvQ2FydEJ1dHRvbiIsImFkZFByb2R1Y3RUb0NhcnQiLCJhZGRyZXNzU2VsZWN0IiwiX2NoYW5nZUNhcnRBZGRyZXNzZXMiLCJfaW5pdFByb2R1Y3RSZW1vdmVGcm9tQ2FydCIsIl9hZGRDYXJ0UnVsZVRvQ2FydCIsIl9yZW1vdmVDYXJ0UnVsZUZyb21DYXJ0IiwiZXZlbnRNYXAiLCJjYXJ0TG9hZGVkIiwiY2FydEluZm8iLCJfcmVuZGVyQ2FydEluZm8iLCJsb2FkQ3VzdG9tZXJDYXJ0cyIsImxvYWRDdXN0b21lck9yZGVycyIsImNhcnRBZGRyZXNzZXNDaGFuZ2VkIiwicmVuZGVyIiwiYWRkcmVzc2VzIiwic2hpcHBpbmciLCJjYXJ0RGVsaXZlcnlPcHRpb25DaGFuZ2VkIiwiY2FydEZyZWVTaGlwcGluZ1NldCIsImV2ZW50Iiwic2V0VGltZW91dCIsInNlYXJjaCIsInZhbCIsImN1c3RvbWVySWQiLCJzZWxlY3RDdXN0b21lciIsImxvYWRFbXB0eUNhcnQiLCJnZXRDYXJ0Iiwib3JkZXJJZCIsImR1cGxpY2F0ZU9yZGVyQ2FydCIsInNlYXJjaFBocmFzZSIsImZvdW5kQ2FydFJ1bGVMaXN0SXRlbSIsInByZXZlbnREZWZhdWx0IiwiYWRkQ2FydFJ1bGVUb0NhcnQiLCJibHVyIiwicmVtb3ZlQ2FydFJ1bGVGcm9tQ2FydCIsIiRwcm9kdWN0U2VhcmNoSW5wdXQiLCJyZW1vdmVQcm9kdWN0RnJvbUNhcnQiLCJyZW5kZXJDYXJ0UnVsZXNCbG9jayIsInJlbmRlckxpc3QiLCJjYXJ0QmxvY2siLCJkZWxpdmVyeUFkZHJlc3NJZCIsImRlbGl2ZXJ5QWRkcmVzc1NlbGVjdCIsImludm9pY2VBZGRyZXNzSWQiLCJpbnZvaWNlQWRkcmVzc1NlbGVjdCIsImNoYW5nZUNhcnRBZGRyZXNzZXMiLCJjdXN0b21lclNlYXJjaFJlc3VsdHNCbG9jayIsImN1c3RvbWVyU2VhcmNoUmVzdWx0VGVtcGxhdGUiLCJjaGFuZ2VDdXN0b21lckJ0biIsImN1c3RvbWVyU2VhcmNoUm93Iiwibm90U2VsZWN0ZWRDdXN0b21lclNlYXJjaFJlc3VsdHMiLCJjdXN0b21lclNlYXJjaFJlc3VsdE5hbWUiLCJjdXN0b21lclNlYXJjaFJlc3VsdEVtYWlsIiwiY3VzdG9tZXJTZWFyY2hSZXN1bHRJZCIsImN1c3RvbWVyU2VhcmNoUmVzdWx0QmlydGhkYXkiLCJjdXN0b21lckRldGFpbHNCdG4iLCJjdXN0b21lclNlYXJjaFJlc3VsdENvbHVtbiIsImN1c3RvbWVyU2VhcmNoQmxvY2siLCJjdXN0b21lckNhcnRzVGFiIiwiY3VzdG9tZXJPcmRlcnNUYWIiLCJjdXN0b21lckNhcnRzVGFibGUiLCJjdXN0b21lckNhcnRzVGFibGVSb3dUZW1wbGF0ZSIsImN1c3RvbWVyQ2hlY2tvdXRIaXN0b3J5IiwiY3VzdG9tZXJPcmRlcnNUYWJsZSIsImN1c3RvbWVyT3JkZXJzVGFibGVSb3dUZW1wbGF0ZSIsImNhcnREZXRhaWxzQnRuIiwiY2FydElkRmllbGQiLCJjYXJ0RGF0ZUZpZWxkIiwiY2FydFRvdGFsRmllbGQiLCJvcmRlckRldGFpbHNCdG4iLCJvcmRlcklkRmllbGQiLCJvcmRlckRhdGVGaWVsZCIsIm9yZGVyUHJvZHVjdHNGaWVsZCIsIm9yZGVyVG90YWxGaWVsZCIsIm9yZGVyU3RhdHVzRmllbGQiLCJhZGRyZXNzZXNCbG9jayIsImRlbGl2ZXJ5QWRkcmVzc0RldGFpbHMiLCJpbnZvaWNlQWRkcmVzc0RldGFpbHMiLCJhZGRyZXNzZXNDb250ZW50IiwiYWRkcmVzc2VzV2FybmluZyIsInN1bW1hcnlCbG9jayIsInNoaXBwaW5nQmxvY2siLCJzaGlwcGluZ0Zvcm0iLCJub0NhcnJpZXJCbG9jayIsInRvdGFsU2hpcHBpbmdGaWVsZCIsImNvbWJpbmF0aW9uc1RlbXBsYXRlIiwicHJvZHVjdFNlbGVjdFJvdyIsInF1YW50aXR5Um93IiwiZG9jdW1lbnQiLCJyZWFkeSIsImRlbGl2ZXJ5QWRkcmVzc0RldGFpbHNDb250ZW50IiwiaW52b2ljZUFkZHJlc3NEZXRhaWxzQ29udGVudCIsIiRkZWxpdmVyeUFkZHJlc3NEZXRhaWxzIiwiY3JlYXRlT3JkZXJQYWdlTWFwIiwiJGludm9pY2VBZGRyZXNzRGV0YWlscyIsIiRkZWxpdmVyeUFkZHJlc3NTZWxlY3QiLCIkaW52b2ljZUFkZHJlc3NTZWxlY3QiLCIkYWRkcmVzc2VzQ29udGVudCIsIiRhZGRyZXNzZXNXYXJuaW5nQ29udGVudCIsIk9iamVjdCIsImtleXMiLCJhZGRyZXNzIiwiZGVsaXZlcnlBZGRyZXNzT3B0aW9uIiwiYWRkcmVzc0lkIiwiYWxpYXMiLCJpbnZvaWNlQWRkcmVzc09wdGlvbiIsImRlbGl2ZXJ5IiwiZm9ybWF0dGVkQWRkcmVzcyIsInNlbGVjdGVkIiwiaW52b2ljZSIsIl9zaG93QWRkcmVzc2VzQmxvY2siLCJnZXQiLCJnZW5lcmF0ZSIsInRoZW4iLCJlbWl0IiwicG9zdCIsImN1c3RvbWVyX2lkIiwiJHNlYXJjaElucHV0IiwiX3NlYXJjaCIsImhpZGVSZXN1bHRzRHJvcGRvd24iLCJfb25DYXJ0UnVsZVNlYXJjaCIsIl9vbkFkZENhcnRSdWxlVG9DYXJ0IiwiX29uQWRkQ2FydFJ1bGVUb0NhcnRGYWlsdXJlIiwiX29uUmVtb3ZlQ2FydFJ1bGVGcm9tQ2FydCIsImNhcnRSdWxlU2VhcmNoZWQiLCJyZW5kZXJTZWFyY2hSZXN1bHRzIiwiY2FydFJ1bGVBZGRlZCIsImNhcnRSdWxlRmFpbGVkVG9BZGQiLCJkaXNwbGF5RXJyb3JNZXNzYWdlIiwiY2FydFJ1bGVSZW1vdmVkIiwic2VhcmNoX3BocmFzZSIsImNhdGNoIiwic2hvd0Vycm9yTWVzc2FnZSIsInJlc3BvbnNlSlNPTiIsImFjdGl2ZVNlYXJjaFJlcXVlc3QiLCIkY3VzdG9tZXJTZWFyY2hSZXN1bHRCbG9jayIsImN1c3RvbWVyUmVuZGVyZXIiLCJDdXN0b21lclJlbmRlcmVyIiwiX3NlbGVjdEN1c3RvbWVyIiwiX2xvYWRDdXN0b21lckNhcnRzIiwiY3VycmVudENhcnRJZCIsIl9sb2FkQ3VzdG9tZXJPcmRlcnMiLCJfY2hhbmdlQ3VzdG9tZXIiLCJfb25DdXN0b21lclNlYXJjaCIsIl9vbkN1c3RvbWVyU2VsZWN0IiwiY3VzdG9tZXJTZWFyY2hlZCIsInJlc3BvbnNlIiwiY3VzdG9tZXJzIiwiY3VzdG9tZXJTZWxlY3RlZCIsIiRjaG9vc2VCdG4iLCJkaXNwbGF5U2VsZWN0ZWRDdXN0b21lckJsb2NrIiwic2hvd0N1c3RvbWVyU2VhcmNoIiwicmVuZGVyQ2FydHMiLCJjYXJ0cyIsInJlbmRlck9yZGVycyIsIm9yZGVycyIsImNob29zZUN1c3RvbWVyRXZlbnQiLCJhYm9ydCIsIiRzZWFyY2hSZXF1ZXN0IiwiY3VzdG9tZXJfc2VhcmNoIiwic3RhdHVzVGV4dCIsImZvdW5kQ3VzdG9tZXJzIiwiX2NsZWFyU2hvd25DdXN0b21lcnMiLCJfc2hvd05vdEZvdW5kQ3VzdG9tZXJzIiwiY3VzdG9tZXJSZXN1bHQiLCJjdXN0b21lciIsImlkIiwiZmlyc3ROYW1lIiwiZmlyc3RuYW1lIiwibGFzdE5hbWUiLCJsYXN0bmFtZSIsImVtYWlsIiwiYmlydGhkYXkiLCJfcmVuZGVyRm91bmRDdXN0b21lciIsIiR0YXJnZXRlZEJ0biIsIiRjdXN0b21lckNhcmQiLCJjbG9zZXN0IiwicmVtb3ZlIiwiJGNhcnRzVGFibGUiLCIkY2FydHNUYWJsZVJvd1RlbXBsYXRlIiwiX3Nob3dDaGVja291dEhpc3RvcnlCbG9jayIsImNhcnQiLCJjcmVhdGlvbkRhdGUiLCJ0b3RhbFByaWNlIiwicHJvcCIsIiRvcmRlcnNUYWJsZSIsIiRyb3dUZW1wbGF0ZSIsIm9yZGVyIiwib3JkZXJQbGFjZWREYXRlIiwidG90YWxQcm9kdWN0c0NvdW50IiwidG90YWxQYWlkIiwib3JkZXJTdGF0dXMiLCIkY3VzdG9tZXJTZWFyY2hSZXN1bHRUZW1wbGF0ZSIsIiRlbXB0eVJlc3VsdFRlbXBsYXRlIiwic2VsZWN0ZWRQcm9kdWN0SWQiLCJzZWxlY3RlZENvbWJpbmF0aW9uSWQiLCJhZGRQcm9kdWN0IiwiX2dldFByb2R1Y3REYXRhIiwiX2luaXRQcm9kdWN0U2VsZWN0IiwiX2luaXRDb21iaW5hdGlvblNlbGVjdCIsIl9vblByb2R1Y3RTZWFyY2giLCJfb25BZGRQcm9kdWN0VG9DYXJ0IiwiX29uUmVtb3ZlUHJvZHVjdEZyb21DYXJ0IiwicHJvZHVjdFNlYXJjaGVkIiwiSlNPTiIsInBhcnNlIiwiX3NlbGVjdEZpcnN0UmVzdWx0IiwicHJvZHVjdEFkZGVkVG9DYXJ0IiwicHJvZHVjdFJlbW92ZWRGcm9tQ2FydCIsIk51bWJlciIsIl9zZWxlY3RQcm9kdWN0IiwiY29tYmluYXRpb25JZCIsIl9zZWxlY3RDb21iaW5hdGlvbiIsIl91bnNldFByb2R1Y3QiLCJfdW5zZXRDb21iaW5hdGlvbiIsInJlbmRlclByb2R1Y3RNZXRhZGF0YSIsImZvcm1EYXRhIiwiRm9ybURhdGEiLCJfZ2V0Q3VzdG9tRmllbGRzRGF0YSIsIiRjdXN0b21GaWVsZHMiLCJlYWNoIiwiZmllbGQiLCIkZmllbGQiLCJmaWxlcyIsIiRmb3JtIiwiJG5vQ2FycmllckJsb2NrIiwic2hpcHBpbmdJc0F2YWlsYWJsZSIsIl9oaWRlQ29udGFpbmVyIiwiX2Rpc3BsYXlGb3JtIiwiX2Rpc3BsYXlOb0NhcnJpZXJzV2FybmluZyIsIl9oaWRlTm9DYXJyaWVyQmxvY2siLCJfcmVuZGVyRGVsaXZlcnlPcHRpb25zIiwiZGVsaXZlcnlPcHRpb25zIiwic2VsZWN0ZWRDYXJyaWVySWQiLCJfcmVuZGVyVG90YWxTaGlwcGluZyIsInNoaXBwaW5nUHJpY2UiLCJfc2hvd0Zvcm0iLCJfc2hvd0NvbnRhaW5lciIsIl9oaWRlRm9ybSIsIl9zaG93Tm9DYXJyaWVyQmxvY2siLCJzZWxlY3RlZFZhbCIsIiRkZWxpdmVyeU9wdGlvblNlbGVjdCIsIm9wdGlvbiIsImRlbGl2ZXJ5T3B0aW9uIiwiY2FycmllcklkIiwiY2Fycmllck5hbWUiLCJjYXJyaWVyRGVsYXkiLCIkdG90YWxTaGlwcGluZ0ZpZWxkIiwiUm91dGluZyIsInNldERhdGEiLCJyb3V0ZXMiLCJzZXRCYXNlVXJsIiwicm91dGUiLCJwYXJhbXMiLCJ0b2tlbml6ZWRQYXJhbXMiLCJhc3NpZ24iLCJfdG9rZW4iLCJmcmVlU2hpcHBpbmciLCJhamF4IiwibWV0aG9kIiwicHJvY2Vzc0RhdGEiLCJjb250ZW50VHlwZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0EsbURBQTJDLGNBQWM7O0FBRXpEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FB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RDs7QUFFRDs7Ozs7Ozs7OzBDQU1zQjhHLE0sRUFBUXJFLE8sRUFBUztBQUNyQ2pFLFFBQUVxUyxJQUFGLENBQU8sS0FBS25KLE1BQUwsQ0FBWWdKLFFBQVosQ0FBcUIsNEJBQXJCLEVBQW1ELEVBQUM1SixjQUFELEVBQW5ELENBQVAsRUFBcUU7QUFDbkV4RCxtQkFBV2IsUUFBUWEsU0FEZ0Q7QUFFbkVDLHFCQUFhZCxRQUFRYyxXQUY4QztBQUduRUMseUJBQWlCZixRQUFRZTtBQUgwQyxPQUFyRSxFQUlHbU4sSUFKSCxDQUlRLFVBQUN2RyxRQUFELEVBQWM7QUFDcEJ6RCxtQ0FBYWlLLElBQWIsQ0FBa0IxRyxtQkFBU3dNLHNCQUEzQixFQUFtRHRNLFFBQW5EO0FBQ0QsT0FORCxFQU1HeUgsS0FOSCxDQU1TLFVBQUNlLFFBQUQsRUFBYztBQUNyQmQseUJBQWlCYyxTQUFTYixZQUFULENBQXNCL1IsT0FBdkM7QUFDRCxPQVJEO0FBU0Q7Ozs7OztrQkFqSGtCNkgsVSIsImZpbGUiOiJvcmRlcl9jcmVhdGUuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAzNjgpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDFlNjYyNjM5MDBlOTY2ZGZiYmYwIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IGNyZWF0ZU9yZGVyTWFwIGZyb20gJy4vY3JlYXRlLW9yZGVyLW1hcCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBSZW5kZXJzIGNhcnQgcnVsZXMgKGNhcnRSdWxlcykgYmxvY2tcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ2FydFJ1bGVzUmVuZGVyZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLiRjYXJ0UnVsZXNCbG9jayA9ICQoY3JlYXRlT3JkZXJNYXAuY2FydFJ1bGVzQmxvY2spO1xuICAgIHRoaXMuJGNhcnRSdWxlc1RhYmxlID0gJChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZXNUYWJsZSk7XG4gICAgdGhpcy4kc2VhcmNoUmVzdWx0Qm94ID0gJChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZXNTZWFyY2hSZXN1bHRCb3gpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlc3BvbnNpYmxlIGZvciByZW5kZXJpbmcgY2FydFJ1bGVzIChhLmsuYSBjYXJ0IHJ1bGVzL2Rpc2NvdW50cykgYmxvY2tcbiAgICpcbiAgICogQHBhcmFtIHtBcnJheX0gY2FydFJ1bGVzXG4gICAqIEBwYXJhbSB7Qm9vbGVhbn0gZW1wdHlDYXJ0XG4gICAqL1xuICByZW5kZXJDYXJ0UnVsZXNCbG9jayhjYXJ0UnVsZXMsIGVtcHR5Q2FydCkge1xuICAgIHRoaXMuX2hpZGVFcnJvckJsb2NrKCk7XG4gICAgLy8gZG8gbm90IHJlbmRlciBjYXJ0IHJ1bGVzIGJsb2NrIGF0IGFsbCBpZiBjYXJ0IGhhcyBubyBwcm9kdWN0c1xuICAgIGlmIChlbXB0eUNhcnQpIHtcbiAgICAgIHRoaXMuX2hpZGVDYXJ0UnVsZXNCbG9jaygpO1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICB0aGlzLl9zaG93Q2FydFJ1bGVzQmxvY2soKTtcblxuICAgIC8vIGRvIG5vdCByZW5kZXIgY2FydCBydWxlcyBsaXN0IHdoZW4gdGhlcmUgYXJlIG5vIGNhcnQgcnVsZXNcbiAgICBpZiAoY2FydFJ1bGVzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgdGhpcy5faGlkZUNhcnRSdWxlc0xpc3QoKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIHRoaXMuX3JlbmRlckxpc3QoY2FydFJ1bGVzKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXNwb25zaWJsZSBmb3IgcmVuZGVyaW5nIHNlYXJjaCByZXN1bHRzIGRyb3Bkb3duXG4gICAqXG4gICAqIEBwYXJhbSBzZWFyY2hSZXN1bHRzXG4gICAqL1xuICByZW5kZXJTZWFyY2hSZXN1bHRzKHNlYXJjaFJlc3VsdHMpIHtcbiAgICB0aGlzLl9jbGVhclNlYXJjaFJlc3VsdHMoKTtcblxuICAgIGlmIChzZWFyY2hSZXN1bHRzLmNhcnRfcnVsZXMubGVuZ3RoID09PSAwKSB7XG4gICAgICB0aGlzLl9yZW5kZXJOb3RGb3VuZCgpO1xuICAgIH0gZWxzZSB7XG4gICAgICB0aGlzLl9yZW5kZXJGb3VuZENhcnRSdWxlcyhzZWFyY2hSZXN1bHRzLmNhcnRfcnVsZXMpO1xuICAgIH1cblxuICAgIHRoaXMuX3Nob3dSZXN1bHRzRHJvcGRvd24oKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNwbGF5cyBlcnJvciBtZXNzYWdlIGJlbGxvdyBzZWFyY2ggaW5wdXRcbiAgICpcbiAgICogQHBhcmFtIG1lc3NhZ2VcbiAgICovXG4gIGRpc3BsYXlFcnJvck1lc3NhZ2UobWVzc2FnZSkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuY2FydFJ1bGVFcnJvclRleHQpLnRleHQobWVzc2FnZSk7XG4gICAgdGhpcy5fc2hvd0Vycm9yQmxvY2soKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlcyBjYXJ0IHJ1bGVzIHNlYXJjaCByZXN1bHQgZHJvcGRvd25cbiAgICovXG4gIGhpZGVSZXN1bHRzRHJvcGRvd24oKSB7XG4gICAgdGhpcy4kc2VhcmNoUmVzdWx0Qm94LmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNwbGF5cyBjYXJ0IHJ1bGVzIHNlYXJjaCByZXN1bHQgZHJvcGRvd25cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93UmVzdWx0c0Ryb3Bkb3duKCkge1xuICAgIHRoaXMuJHNlYXJjaFJlc3VsdEJveC5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyB3YXJuaW5nIHRoYXQgbm8gY2FydCBydWxlIHdhcyBmb3VuZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbmRlck5vdEZvdW5kKCkge1xuICAgIGNvbnN0ICR0ZW1wbGF0ZSA9ICQoJChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZXNOb3RGb3VuZFRlbXBsYXRlKS5odG1sKCkpLmNsb25lKCk7XG4gICAgdGhpcy4kc2VhcmNoUmVzdWx0Qm94Lmh0bWwoJHRlbXBsYXRlKTtcbiAgfVxuXG5cbiAgLyoqXG4gICAqIEVtcHRpZXMgY2FydCBydWxlIHNlYXJjaCByZXN1bHRzIGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfY2xlYXJTZWFyY2hSZXN1bHRzKCkge1xuICAgIHRoaXMuJHNlYXJjaFJlc3VsdEJveC5lbXB0eSgpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbmRlcnMgZm91bmQgY2FydCBydWxlcyBhZnRlciBzZWFyY2hcbiAgICpcbiAgICogQHBhcmFtIGNhcnRSdWxlc1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbmRlckZvdW5kQ2FydFJ1bGVzKGNhcnRSdWxlcykge1xuICAgIGNvbnN0ICRjYXJ0UnVsZVRlbXBsYXRlID0gJCgkKGNyZWF0ZU9yZGVyTWFwLmZvdW5kQ2FydFJ1bGVUZW1wbGF0ZSkuaHRtbCgpKTtcbiAgICBmb3IgKGNvbnN0IGtleSBpbiBjYXJ0UnVsZXMpIHtcbiAgICAgIGNvbnN0ICR0ZW1wbGF0ZSA9ICRjYXJ0UnVsZVRlbXBsYXRlLmNsb25lKCk7XG4gICAgICBjb25zdCBjYXJ0UnVsZSA9IGNhcnRSdWxlc1trZXldO1xuXG4gICAgICBsZXQgY2FydFJ1bGVOYW1lID0gY2FydFJ1bGUubmFtZTtcbiAgICAgIGlmIChjYXJ0UnVsZS5jb2RlICE9PSAnJykge1xuICAgICAgICBjYXJ0UnVsZU5hbWUgPSBgJHtjYXJ0UnVsZS5uYW1lfSAtICR7Y2FydFJ1bGUuY29kZX1gO1xuICAgICAgfVxuXG4gICAgICAkdGVtcGxhdGUudGV4dChjYXJ0UnVsZU5hbWUpO1xuICAgICAgJHRlbXBsYXRlLmRhdGEoJ2NhcnQtcnVsZS1pZCcsIGNhcnRSdWxlLmNhcnRSdWxlSWQpO1xuICAgICAgdGhpcy4kc2VhcmNoUmVzdWx0Qm94LmFwcGVuZCgkdGVtcGxhdGUpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBSZXNwb25zaWJsZSBmb3IgcmVuZGVyaW5nIHRoZSBsaXN0IG9mIGNhcnQgcnVsZXNcbiAgICpcbiAgICogQHBhcmFtIHtBcnJheX0gY2FydFJ1bGVzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcmVuZGVyTGlzdChjYXJ0UnVsZXMpIHtcbiAgICB0aGlzLl9jbGVhbkNhcnRSdWxlc0xpc3QoKTtcbiAgICBjb25zdCAkY2FydFJ1bGVzVGFibGVSb3dUZW1wbGF0ZSA9ICQoJChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZXNUYWJsZVJvd1RlbXBsYXRlKS5odG1sKCkpO1xuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gY2FydFJ1bGVzKSB7XG4gICAgICBjb25zdCBjYXJ0UnVsZSA9IGNhcnRSdWxlc1trZXldO1xuICAgICAgY29uc3QgJHRlbXBsYXRlID0gJGNhcnRSdWxlc1RhYmxlUm93VGVtcGxhdGUuY2xvbmUoKTtcblxuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAuY2FydFJ1bGVOYW1lRmllbGQpLnRleHQoY2FydFJ1bGUubmFtZSk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZURlc2NyaXB0aW9uRmllbGQpLnRleHQoY2FydFJ1bGUuZGVzY3JpcHRpb24pO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAuY2FydFJ1bGVWYWx1ZUZpZWxkKS50ZXh0KGNhcnRSdWxlLnZhbHVlKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlRGVsZXRlQnRuKS5kYXRhKCdjYXJ0LXJ1bGUtaWQnLCBjYXJ0UnVsZS5jYXJ0UnVsZUlkKTtcblxuICAgICAgdGhpcy4kY2FydFJ1bGVzVGFibGUuZmluZCgndGJvZHknKS5hcHBlbmQoJHRlbXBsYXRlKTtcbiAgICB9XG5cbiAgICB0aGlzLl9zaG93Q2FydFJ1bGVzTGlzdCgpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzIGVycm9yIGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd0Vycm9yQmxvY2soKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZUVycm9yQmxvY2spLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlcyBlcnJvciBibG9ja1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGVFcnJvckJsb2NrKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuY2FydFJ1bGVFcnJvckJsb2NrKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogU2hvd3MgY2FydFJ1bGVzIGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd0NhcnRSdWxlc0Jsb2NrKCkge1xuICAgIHRoaXMuJGNhcnRSdWxlc0Jsb2NrLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBoaWRlIGNhcnRSdWxlcyBibG9ja1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGVDYXJ0UnVsZXNCbG9jaygpIHtcbiAgICB0aGlzLiRjYXJ0UnVsZXNCbG9jay5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogRGlzcGxheSB0aGUgbGlzdCBibG9jayBvZiBjYXJ0IHJ1bGVzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd0NhcnRSdWxlc0xpc3QoKSB7XG4gICAgdGhpcy4kY2FydFJ1bGVzVGFibGUucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGUgbGlzdCBibG9jayBvZiBjYXJ0IHJ1bGVzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZUNhcnRSdWxlc0xpc3QoKSB7XG4gICAgdGhpcy4kY2FydFJ1bGVzVGFibGUuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIHJlbW92ZSBpdGVtcyBpbiBjYXJ0IHJ1bGVzIGxpc3RcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9jbGVhbkNhcnRSdWxlc0xpc3QoKSB7XG4gICAgdGhpcy4kY2FydFJ1bGVzVGFibGUuZmluZCgndGJvZHknKS5lbXB0eSgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY2FydC1ydWxlcy1yZW5kZXJlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBjcmVhdGVPcmRlck1hcCBmcm9tICcuL2NyZWF0ZS1vcmRlci1tYXAnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFByb2R1Y3RSZW5kZXJlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuJHByb2R1Y3RzVGFibGUgPSAkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RzVGFibGUpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbmRlcnMgY2FydCBwcm9kdWN0cyBsaXN0XG4gICAqXG4gICAqIEBwYXJhbSBwcm9kdWN0c1xuICAgKi9cbiAgcmVuZGVyTGlzdChwcm9kdWN0cykge1xuICAgIHRoaXMuX2NsZWFuUHJvZHVjdHNMaXN0KCk7XG5cbiAgICBpZiAocHJvZHVjdHMubGVuZ3RoID09PSAwKSB7XG4gICAgICB0aGlzLl9oaWRlUHJvZHVjdHNMaXN0KCk7XG5cbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCAkcHJvZHVjdHNUYWJsZVJvd1RlbXBsYXRlID0gJCgkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RzVGFibGVSb3dUZW1wbGF0ZSkuaHRtbCgpKTtcblxuICAgIGZvciAoY29uc3Qga2V5IGluIHByb2R1Y3RzKSB7XG4gICAgICBjb25zdCBwcm9kdWN0ID0gcHJvZHVjdHNba2V5XTtcbiAgICAgIGNvbnN0ICR0ZW1wbGF0ZSA9ICRwcm9kdWN0c1RhYmxlUm93VGVtcGxhdGUuY2xvbmUoKTtcblxuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdEltYWdlRmllbGQpLnRleHQocHJvZHVjdC5pbWFnZUxpbmspO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdE5hbWVGaWVsZCkudGV4dChwcm9kdWN0Lm5hbWUpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdEF0dHJGaWVsZCkudGV4dChwcm9kdWN0LmF0dHJpYnV0ZSk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5wcm9kdWN0UmVmZXJlbmNlRmllbGQpLnRleHQocHJvZHVjdC5yZWZlcmVuY2UpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdFVuaXRQcmljZUlucHV0KS50ZXh0KHByb2R1Y3QudW5pdFByaWNlKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RUb3RhbFByaWNlRmllbGQpLnRleHQocHJvZHVjdC5wcmljZSk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5wcm9kdWN0UmVtb3ZlQnRuKS5kYXRhKCdwcm9kdWN0LWlkJywgcHJvZHVjdC5wcm9kdWN0SWQpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdFJlbW92ZUJ0bikuZGF0YSgnYXR0cmlidXRlLWlkJywgcHJvZHVjdC5hdHRyaWJ1dGVJZCk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5wcm9kdWN0UmVtb3ZlQnRuKS5kYXRhKCdjdXN0b21pemF0aW9uLWlkJywgcHJvZHVjdC5jdXN0b21pemF0aW9uSWQpO1xuXG4gICAgICB0aGlzLiRwcm9kdWN0c1RhYmxlLmZpbmQoJ3Rib2R5JykuYXBwZW5kKCR0ZW1wbGF0ZSk7XG4gICAgfVxuXG4gICAgdGhpcy5fc2hvd1RheFdhcm5pbmcoKTtcbiAgICB0aGlzLl9zaG93UHJvZHVjdHNMaXN0KCk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBjYXJ0IHByb2R1Y3RzIHNlYXJjaCByZXN1bHRzIGJsb2NrXG4gICAqXG4gICAqIEBwYXJhbSBmb3VuZFByb2R1Y3RzXG4gICAqL1xuICByZW5kZXJTZWFyY2hSZXN1bHRzKGZvdW5kUHJvZHVjdHMpIHtcbiAgICB0aGlzLl9jbGVhblNlYXJjaFJlc3VsdHMoKTtcbiAgICBpZiAoZm91bmRQcm9kdWN0cy5sZW5ndGggPT09IDApIHtcbiAgICAgIHRoaXMuX3Nob3dOb3RGb3VuZCgpO1xuICAgICAgdGhpcy5faGlkZVRheFdhcm5pbmcoKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIHRoaXMuX3JlbmRlckZvdW5kUHJvZHVjdHMoZm91bmRQcm9kdWN0cyk7XG5cbiAgICB0aGlzLl9oaWRlTm90Rm91bmQoKTtcbiAgICB0aGlzLl9zaG93VGF4V2FybmluZygpO1xuICAgIHRoaXMuX3Nob3dSZXN1bHRCbG9jaygpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbmRlcnMgYXZhaWxhYmxlIGZpZWxkcyByZWxhdGVkIHRvIHNlbGVjdGVkIHByb2R1Y3RcbiAgICpcbiAgICogQHBhcmFtIHByb2R1Y3RcbiAgICovXG4gIHJlbmRlclByb2R1Y3RNZXRhZGF0YShwcm9kdWN0KSB7XG4gICAgdGhpcy5yZW5kZXJTdG9jayhwcm9kdWN0LnN0b2NrKTtcbiAgICB0aGlzLl9yZW5kZXJDb21iaW5hdGlvbnMocHJvZHVjdC5jb21iaW5hdGlvbnMpO1xuICAgIHRoaXMuX3JlbmRlckN1c3RvbWl6YXRpb25zKHByb2R1Y3QuY3VzdG9taXphdGlvbl9maWVsZHMpO1xuICB9XG5cbiAgLyoqXG4gICAqIFVwZGF0ZXMgc3RvY2sgdGV4dCBoZWxwZXIgdmFsdWVcbiAgICpcbiAgICogQHBhcmFtIHN0b2NrXG4gICAqL1xuICByZW5kZXJTdG9jayhzdG9jaykge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuaW5TdG9ja0NvdW50ZXIpLnRleHQoc3RvY2spO1xuICAgICQoY3JlYXRlT3JkZXJNYXAucXVhbnRpdHlJbnB1dCkuYXR0cignbWF4Jywgc3RvY2spO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbmRlcnMgZm91bmQgcHJvZHVjdHMgc2VsZWN0XG4gICAqXG4gICAqIEBwYXJhbSBmb3VuZFByb2R1Y3RzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcmVuZGVyRm91bmRQcm9kdWN0cyhmb3VuZFByb2R1Y3RzKSB7XG4gICAgZm9yIChjb25zdCBrZXkgaW4gZm91bmRQcm9kdWN0cykge1xuICAgICAgY29uc3QgcHJvZHVjdCA9IGZvdW5kUHJvZHVjdHNba2V5XTtcblxuICAgICAgbGV0IG5hbWUgPSBwcm9kdWN0Lm5hbWU7XG4gICAgICBpZiAocHJvZHVjdC5jb21iaW5hdGlvbnMubGVuZ3RoID09PSAwKSB7XG4gICAgICAgIG5hbWUgKz0gYCAtICR7cHJvZHVjdC5mb3JtYXR0ZWRfcHJpY2V9YDtcbiAgICAgIH1cblxuICAgICAgJChjcmVhdGVPcmRlck1hcC5wcm9kdWN0U2VsZWN0KS5hcHBlbmQoYDxvcHRpb24gdmFsdWU9XCIke3Byb2R1Y3QucHJvZHVjdF9pZH1cIj4ke25hbWV9PC9vcHRpb24+YCk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIENsZWFucyBwcm9kdWN0IHNlYXJjaCByZXN1bHQgZmllbGRzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfY2xlYW5TZWFyY2hSZXN1bHRzKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdFNlbGVjdCkuZW1wdHkoKTtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmNvbWJpbmF0aW9uc1NlbGVjdCkuZW1wdHkoKTtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLnF1YW50aXR5SW5wdXQpLmVtcHR5KCk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBjb21iaW5hdGlvbnMgcm93IHdpdGggc2VsZWN0IG9wdGlvbnNcbiAgICpcbiAgICogQHBhcmFtIHtBcnJheX0gY29tYmluYXRpb25zXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcmVuZGVyQ29tYmluYXRpb25zKGNvbWJpbmF0aW9ucykge1xuICAgIHRoaXMuX2NsZWFuQ29tYmluYXRpb25zKCk7XG5cbiAgICBpZiAoY29tYmluYXRpb25zLmxlbmd0aCA9PT0gMCkge1xuICAgICAgdGhpcy5faGlkZUNvbWJpbmF0aW9ucygpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gY29tYmluYXRpb25zKSB7XG4gICAgICBjb25zdCBjb21iaW5hdGlvbiA9IGNvbWJpbmF0aW9uc1trZXldO1xuXG4gICAgICAkKGNyZWF0ZU9yZGVyTWFwLmNvbWJpbmF0aW9uc1NlbGVjdCkuYXBwZW5kKFxuICAgICAgICBgPG9wdGlvblxuICAgICAgICAgIHZhbHVlPVwiJHtjb21iaW5hdGlvbi5hdHRyaWJ1dGVfY29tYmluYXRpb25faWR9XCI+XG4gICAgICAgICAgJHtjb21iaW5hdGlvbi5hdHRyaWJ1dGV9IC0gJHtjb21iaW5hdGlvbi5mb3JtYXR0ZWRfcHJpY2V9XG4gICAgICAgIDwvb3B0aW9uPmAsXG4gICAgICApO1xuICAgIH1cblxuICAgIHRoaXMuX3Nob3dDb21iaW5hdGlvbnMoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXNvbHZlcyB3ZWF0aGVyIHRvIGFkZCBjdXN0b21pemF0aW9uIGZpZWxkcyB0byByZXN1bHQgYmxvY2sgYW5kIGFkZHMgdGhlbSBpZiBuZWVkZWRcbiAgICpcbiAgICogQHBhcmFtIGN1c3RvbWl6YXRpb25GaWVsZHNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW5kZXJDdXN0b21pemF0aW9ucyhjdXN0b21pemF0aW9uRmllbGRzKSB7XG4gICAgLy8gcmVwcmVzZW50cyBjdXN0b21pemF0aW9uIGZpZWxkIHR5cGUgXCJmaWxlXCIuXG4gICAgY29uc3QgZmllbGRUeXBlRmlsZSA9IDA7XG4gICAgLy8gcmVwcmVzZW50cyBjdXN0b21pemF0aW9uIGZpZWxkIHR5cGUgXCJ0ZXh0XCIuXG4gICAgY29uc3QgZmllbGRUeXBlVGV4dCA9IDE7XG5cbiAgICB0aGlzLl9jbGVhbkN1c3RvbWl6YXRpb25zKCk7XG4gICAgaWYgKGN1c3RvbWl6YXRpb25GaWVsZHMubGVuZ3RoID09PSAwKSB7XG4gICAgICB0aGlzLl9oaWRlQ3VzdG9taXphdGlvbnMoKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGNvbnN0ICRjdXN0b21GaWVsZHNDb250YWluZXIgPSAkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RDdXN0b21GaWVsZHNDb250YWluZXIpO1xuICAgIGNvbnN0ICRmaWxlSW5wdXRUZW1wbGF0ZSA9ICQoJChjcmVhdGVPcmRlck1hcC5wcm9kdWN0Q3VzdG9tRmlsZVRlbXBsYXRlKS5odG1sKCkpO1xuICAgIGNvbnN0ICR0ZXh0SW5wdXRUZW1wbGF0ZSA9ICQoJChjcmVhdGVPcmRlck1hcC5wcm9kdWN0Q3VzdG9tVGV4dFRlbXBsYXRlKS5odG1sKCkpO1xuXG4gICAgY29uc3QgdGVtcGxhdGVUeXBlTWFwID0ge1xuICAgICAgW2ZpZWxkVHlwZUZpbGVdOiAkZmlsZUlucHV0VGVtcGxhdGUsXG4gICAgICBbZmllbGRUeXBlVGV4dF06ICR0ZXh0SW5wdXRUZW1wbGF0ZSxcbiAgICB9O1xuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gY3VzdG9taXphdGlvbkZpZWxkcykge1xuICAgICAgY29uc3QgY3VzdG9tRmllbGQgPSBjdXN0b21pemF0aW9uRmllbGRzW2tleV07XG4gICAgICBjb25zdCAkdGVtcGxhdGUgPSB0ZW1wbGF0ZVR5cGVNYXBbY3VzdG9tRmllbGQudHlwZV0uY2xvbmUoKTtcblxuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdEN1c3RvbUlucHV0KVxuICAgICAgICAuYXR0cignbmFtZScsIGBjdXN0b21pemF0aW9uc1ske2N1c3RvbUZpZWxkLmN1c3RvbWl6YXRpb25fZmllbGRfaWR9XWApO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdEN1c3RvbUlucHV0TGFiZWwpXG4gICAgICAgIC5hdHRyKCdmb3InLCBgY3VzdG9taXphdGlvbnNbJHtjdXN0b21GaWVsZC5jdXN0b21pemF0aW9uX2ZpZWxkX2lkfV1gKVxuICAgICAgICAudGV4dChjdXN0b21GaWVsZC5uYW1lKTtcblxuICAgICAgJGN1c3RvbUZpZWxkc0NvbnRhaW5lci5hcHBlbmQoJHRlbXBsYXRlKTtcbiAgICB9XG5cbiAgICB0aGlzLl9zaG93Q3VzdG9taXphdGlvbnMoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyBwcm9kdWN0IGN1c3RvbWl6YXRpb24gY29udGFpbmVyXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd0N1c3RvbWl6YXRpb25zKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdEN1c3RvbWl6YXRpb25Db250YWluZXIpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlcyBwcm9kdWN0IGN1c3RvbWl6YXRpb24gY29udGFpbmVyXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZUN1c3RvbWl6YXRpb25zKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdEN1c3RvbWl6YXRpb25Db250YWluZXIpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbXB0aWVzIGN1c3RvbWl6YXRpb24gZmllbGRzIGNvbnRhaW5lclxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NsZWFuQ3VzdG9taXphdGlvbnMoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5wcm9kdWN0Q3VzdG9tRmllbGRzQ29udGFpbmVyKS5lbXB0eSgpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzIHJlc3VsdCBibG9ja1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dSZXN1bHRCbG9jaygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RSZXN1bHRCbG9jaykucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIHJlc3VsdCBibG9ja1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGVSZXN1bHRCbG9jaygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RSZXN1bHRCbG9jaykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cblxuICAvKipcbiAgICogU2hvd3MgcHJvZHVjdHMgbGlzdFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dQcm9kdWN0c0xpc3QoKSB7XG4gICAgdGhpcy4kcHJvZHVjdHNUYWJsZS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgcHJvZHVjdHMgbGlzdFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGVQcm9kdWN0c0xpc3QoKSB7XG4gICAgdGhpcy4kcHJvZHVjdHNUYWJsZS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogRW1wdGllcyBwcm9kdWN0cyBsaXN0XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfY2xlYW5Qcm9kdWN0c0xpc3QoKSB7XG4gICAgdGhpcy4kcHJvZHVjdHNUYWJsZS5maW5kKCd0Ym9keScpLmVtcHR5KCk7XG4gIH1cblxuICAvKipcbiAgICogRW1wdGllcyBjb21iaW5hdGlvbnMgc2VsZWN0XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfY2xlYW5Db21iaW5hdGlvbnMoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5jb21iaW5hdGlvbnNTZWxlY3QpLmVtcHR5KCk7XG4gIH1cblxuICAvKipcbiAgICogU2hvd3MgY29tYmluYXRpb25zIHJvd1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dDb21iaW5hdGlvbnMoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5jb21iaW5hdGlvbnNSb3cpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlcyBjb21iaW5hdGlvbnMgcm93XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZUNvbWJpbmF0aW9ucygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmNvbWJpbmF0aW9uc1JvdykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzIHdhcm5pbmcgb2YgdGF4IGluY2x1ZGVkL2V4Y2x1ZGVkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd1RheFdhcm5pbmcoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5wcm9kdWN0VGF4V2FybmluZykucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIHdhcm5pbmcgb2YgdGF4IGluY2x1ZGVkL2V4Y2x1ZGVkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZVRheFdhcm5pbmcoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5wcm9kdWN0VGF4V2FybmluZykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzIHByb2R1Y3Qgbm90IGZvdW5kIHdhcm5pbmdcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93Tm90Rm91bmQoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5ub1Byb2R1Y3RzRm91bmRXYXJuaW5nKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgcHJvZHVjdCBub3QgZm91bmQgd2FybmluZ1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGVOb3RGb3VuZCgpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLm5vUHJvZHVjdHNGb3VuZFdhcm5pbmcpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL3Byb2R1Y3QtcmVuZGVyZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgRXZlbnRFbWl0dGVyQ2xhc3MgZnJvbSAnZXZlbnRzJztcblxuLyoqXG4gKiBXZSBpbnN0YW5jaWF0ZSBvbmUgRXZlbnRFbWl0dGVyIChyZXN0cmljdGVkIHZpYSBhIGNvbnN0KSBzbyB0aGF0IGV2ZXJ5IGNvbXBvbmVudHNcbiAqIHJlZ2lzdGVyL2Rpc3BhdGNoIG9uIHRoZSBzYW1lIG9uZSBhbmQgY2FuIGNvbW11bmljYXRlIHdpdGggZWFjaCBvdGhlci5cbiAqL1xuZXhwb3J0IGNvbnN0IEV2ZW50RW1pdHRlciA9IG5ldyBFdmVudEVtaXR0ZXJDbGFzcygpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ldmVudC1lbWl0dGVyLmpzIiwiLy8gQ29weXJpZ2h0IEpveWVudCwgSW5jLiBhbmQgb3RoZXIgTm9kZSBjb250cmlidXRvcnMuXG4vL1xuLy8gUGVybWlzc2lvbiBpcyBoZXJlYnkgZ3JhbnRlZCwgZnJlZSBvZiBjaGFyZ2UsIHRvIGFueSBwZXJzb24gb2J0YWluaW5nIGFcbi8vIGNvcHkgb2YgdGhpcyBzb2Z0d2FyZSBhbmQgYXNzb2NpYXRlZCBkb2N1bWVudGF0aW9uIGZpbGVzICh0aGVcbi8vIFwiU29mdHdhcmVcIiksIHRvIGRlYWwgaW4gdGhlIFNvZnR3YXJlIHdpdGhvdXQgcmVzdHJpY3Rpb24sIGluY2x1ZGluZ1xuLy8gd2l0aG91dCBsaW1pdGF0aW9uIHRoZSByaWdodHMgdG8gdXNlLCBjb3B5LCBtb2RpZnksIG1lcmdlLCBwdWJsaXNoLFxuLy8gZGlzdHJpYnV0ZSwgc3VibGljZW5zZSwgYW5kL29yIHNlbGwgY29waWVzIG9mIHRoZSBTb2Z0d2FyZSwgYW5kIHRvIHBlcm1pdFxuLy8gcGVyc29ucyB0byB3aG9tIHRoZSBTb2Z0d2FyZSBpcyBmdXJuaXNoZWQgdG8gZG8gc28sIHN1YmplY3QgdG8gdGhlXG4vLyBmb2xsb3dpbmcgY29uZGl0aW9uczpcbi8vXG4vLyBUaGUgYWJvdmUgY29weXJpZ2h0IG5vdGljZSBhbmQgdGhpcyBwZXJtaXNzaW9uIG5vdGljZSBzaGFsbCBiZSBpbmNsdWRlZFxuLy8gaW4gYWxsIGNvcGllcyBvciBzdWJzdGFudGlhbCBwb3J0aW9ucyBvZiB0aGUgU29mdHdhcmUuXG4vL1xuLy8gVEhFIFNPRlRXQVJFIElTIFBST1ZJREVEIFwiQVMgSVNcIiwgV0lUSE9VVCBXQVJSQU5UWSBPRiBBTlkgS0lORCwgRVhQUkVTU1xuLy8gT1IgSU1QTElFRCwgSU5DTFVESU5HIEJVVCBOT1QgTElNSVRFRCBUTyBUSEUgV0FSUkFOVElFUyBPRlxuLy8gTUVSQ0hBTlRBQklMSVRZLCBGSVRORVNTIEZPUiBBIFBBUlRJQ1VMQVIgUFVSUE9TRSBBTkQgTk9OSU5GUklOR0VNRU5ULiBJTlxuLy8gTk8gRVZFTlQgU0hBTEwgVEhFIEFVVEhPUlMgT1IgQ09QWVJJR0hUIEhPTERFUlMgQkUgTElBQkxFIEZPUiBBTlkgQ0xBSU0sXG4vLyBEQU1BR0VTIE9SIE9USEVSIExJQUJJTElUWSwgV0hFVEhFUiBJTiBBTiBBQ1RJT04gT0YgQ09OVFJBQ1QsIFRPUlQgT1Jcbi8vIE9USEVSV0lTRSwgQVJJU0lORyBGUk9NLCBPVVQgT0YgT1IgSU4gQ09OTkVDVElPTiBXSVRIIFRIRSBTT0ZUV0FSRSBPUiBUSEVcbi8vIFVTRSBPUiBPVEhFUiBERUFMSU5HUyBJTiBUSEUgU09GVFdBUkUuXG5cbid1c2Ugc3RyaWN0JztcblxudmFyIFIgPSB0eXBlb2YgUmVmbGVjdCA9PT0gJ29iamVjdCcgPyBSZWZsZWN0IDogbnVsbFxudmFyIFJlZmxlY3RBcHBseSA9IFIgJiYgdHlwZW9mIFIuYXBwbHkgPT09ICdmdW5jdGlvbidcbiAgPyBSLmFwcGx5XG4gIDogZnVuY3Rpb24gUmVmbGVjdEFwcGx5KHRhcmdldCwgcmVjZWl2ZXIsIGFyZ3MpIHtcbiAgICByZXR1cm4gRnVuY3Rpb24ucHJvdG90eXBlLmFwcGx5LmNhbGwodGFyZ2V0LCByZWNlaXZlciwgYXJncyk7XG4gIH1cblxudmFyIFJlZmxlY3RPd25LZXlzXG5pZiAoUiAmJiB0eXBlb2YgUi5vd25LZXlzID09PSAnZnVuY3Rpb24nKSB7XG4gIFJlZmxlY3RPd25LZXlzID0gUi5vd25LZXlzXG59IGVsc2UgaWYgKE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHMpIHtcbiAgUmVmbGVjdE93bktleXMgPSBmdW5jdGlvbiBSZWZsZWN0T3duS2V5cyh0YXJnZXQpIHtcbiAgICByZXR1cm4gT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXModGFyZ2V0KVxuICAgICAgLmNvbmNhdChPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzKHRhcmdldCkpO1xuICB9O1xufSBlbHNlIHtcbiAgUmVmbGVjdE93bktleXMgPSBmdW5jdGlvbiBSZWZsZWN0T3duS2V5cyh0YXJnZXQpIHtcbiAgICByZXR1cm4gT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXModGFyZ2V0KTtcbiAgfTtcbn1cblxuZnVuY3Rpb24gUHJvY2Vzc0VtaXRXYXJuaW5nKHdhcm5pbmcpIHtcbiAgaWYgKGNvbnNvbGUgJiYgY29uc29sZS53YXJuKSBjb25zb2xlLndhcm4od2FybmluZyk7XG59XG5cbnZhciBOdW1iZXJJc05hTiA9IE51bWJlci5pc05hTiB8fCBmdW5jdGlvbiBOdW1iZXJJc05hTih2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgIT09IHZhbHVlO1xufVxuXG5mdW5jdGlvbiBFdmVudEVtaXR0ZXIoKSB7XG4gIEV2ZW50RW1pdHRlci5pbml0LmNhbGwodGhpcyk7XG59XG5tb2R1bGUuZXhwb3J0cyA9IEV2ZW50RW1pdHRlcjtcblxuLy8gQmFja3dhcmRzLWNvbXBhdCB3aXRoIG5vZGUgMC4xMC54XG5FdmVudEVtaXR0ZXIuRXZlbnRFbWl0dGVyID0gRXZlbnRFbWl0dGVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9ldmVudHMgPSB1bmRlZmluZWQ7XG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9ldmVudHNDb3VudCA9IDA7XG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9tYXhMaXN0ZW5lcnMgPSB1bmRlZmluZWQ7XG5cbi8vIEJ5IGRlZmF1bHQgRXZlbnRFbWl0dGVycyB3aWxsIHByaW50IGEgd2FybmluZyBpZiBtb3JlIHRoYW4gMTAgbGlzdGVuZXJzIGFyZVxuLy8gYWRkZWQgdG8gaXQuIFRoaXMgaXMgYSB1c2VmdWwgZGVmYXVsdCB3aGljaCBoZWxwcyBmaW5kaW5nIG1lbW9yeSBsZWFrcy5cbnZhciBkZWZhdWx0TWF4TGlzdGVuZXJzID0gMTA7XG5cbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShFdmVudEVtaXR0ZXIsICdkZWZhdWx0TWF4TGlzdGVuZXJzJywge1xuICBlbnVtZXJhYmxlOiB0cnVlLFxuICBnZXQ6IGZ1bmN0aW9uKCkge1xuICAgIHJldHVybiBkZWZhdWx0TWF4TGlzdGVuZXJzO1xuICB9LFxuICBzZXQ6IGZ1bmN0aW9uKGFyZykge1xuICAgIGlmICh0eXBlb2YgYXJnICE9PSAnbnVtYmVyJyB8fCBhcmcgPCAwIHx8IE51bWJlcklzTmFOKGFyZykpIHtcbiAgICAgIHRocm93IG5ldyBSYW5nZUVycm9yKCdUaGUgdmFsdWUgb2YgXCJkZWZhdWx0TWF4TGlzdGVuZXJzXCIgaXMgb3V0IG9mIHJhbmdlLiBJdCBtdXN0IGJlIGEgbm9uLW5lZ2F0aXZlIG51bWJlci4gUmVjZWl2ZWQgJyArIGFyZyArICcuJyk7XG4gICAgfVxuICAgIGRlZmF1bHRNYXhMaXN0ZW5lcnMgPSBhcmc7XG4gIH1cbn0pO1xuXG5FdmVudEVtaXR0ZXIuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXG4gIGlmICh0aGlzLl9ldmVudHMgPT09IHVuZGVmaW5lZCB8fFxuICAgICAgdGhpcy5fZXZlbnRzID09PSBPYmplY3QuZ2V0UHJvdG90eXBlT2YodGhpcykuX2V2ZW50cykge1xuICAgIHRoaXMuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgdGhpcy5fZXZlbnRzQ291bnQgPSAwO1xuICB9XG5cbiAgdGhpcy5fbWF4TGlzdGVuZXJzID0gdGhpcy5fbWF4TGlzdGVuZXJzIHx8IHVuZGVmaW5lZDtcbn07XG5cbi8vIE9idmlvdXNseSBub3QgYWxsIEVtaXR0ZXJzIHNob3VsZCBiZSBsaW1pdGVkIHRvIDEwLiBUaGlzIGZ1bmN0aW9uIGFsbG93c1xuLy8gdGhhdCB0byBiZSBpbmNyZWFzZWQuIFNldCB0byB6ZXJvIGZvciB1bmxpbWl0ZWQuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnNldE1heExpc3RlbmVycyA9IGZ1bmN0aW9uIHNldE1heExpc3RlbmVycyhuKSB7XG4gIGlmICh0eXBlb2YgbiAhPT0gJ251bWJlcicgfHwgbiA8IDAgfHwgTnVtYmVySXNOYU4obikpIHtcbiAgICB0aHJvdyBuZXcgUmFuZ2VFcnJvcignVGhlIHZhbHVlIG9mIFwiblwiIGlzIG91dCBvZiByYW5nZS4gSXQgbXVzdCBiZSBhIG5vbi1uZWdhdGl2ZSBudW1iZXIuIFJlY2VpdmVkICcgKyBuICsgJy4nKTtcbiAgfVxuICB0aGlzLl9tYXhMaXN0ZW5lcnMgPSBuO1xuICByZXR1cm4gdGhpcztcbn07XG5cbmZ1bmN0aW9uICRnZXRNYXhMaXN0ZW5lcnModGhhdCkge1xuICBpZiAodGhhdC5fbWF4TGlzdGVuZXJzID09PSB1bmRlZmluZWQpXG4gICAgcmV0dXJuIEV2ZW50RW1pdHRlci5kZWZhdWx0TWF4TGlzdGVuZXJzO1xuICByZXR1cm4gdGhhdC5fbWF4TGlzdGVuZXJzO1xufVxuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmdldE1heExpc3RlbmVycyA9IGZ1bmN0aW9uIGdldE1heExpc3RlbmVycygpIHtcbiAgcmV0dXJuICRnZXRNYXhMaXN0ZW5lcnModGhpcyk7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmVtaXQgPSBmdW5jdGlvbiBlbWl0KHR5cGUpIHtcbiAgdmFyIGFyZ3MgPSBbXTtcbiAgZm9yICh2YXIgaSA9IDE7IGkgPCBhcmd1bWVudHMubGVuZ3RoOyBpKyspIGFyZ3MucHVzaChhcmd1bWVudHNbaV0pO1xuICB2YXIgZG9FcnJvciA9ICh0eXBlID09PSAnZXJyb3InKTtcblxuICB2YXIgZXZlbnRzID0gdGhpcy5fZXZlbnRzO1xuICBpZiAoZXZlbnRzICE9PSB1bmRlZmluZWQpXG4gICAgZG9FcnJvciA9IChkb0Vycm9yICYmIGV2ZW50cy5lcnJvciA9PT0gdW5kZWZpbmVkKTtcbiAgZWxzZSBpZiAoIWRvRXJyb3IpXG4gICAgcmV0dXJuIGZhbHNlO1xuXG4gIC8vIElmIHRoZXJlIGlzIG5vICdlcnJvcicgZXZlbnQgbGlzdGVuZXIgdGhlbiB0aHJvdy5cbiAgaWYgKGRvRXJyb3IpIHtcbiAgICB2YXIgZXI7XG4gICAgaWYgKGFyZ3MubGVuZ3RoID4gMClcbiAgICAgIGVyID0gYXJnc1swXTtcbiAgICBpZiAoZXIgaW5zdGFuY2VvZiBFcnJvcikge1xuICAgICAgLy8gTm90ZTogVGhlIGNvbW1lbnRzIG9uIHRoZSBgdGhyb3dgIGxpbmVzIGFyZSBpbnRlbnRpb25hbCwgdGhleSBzaG93XG4gICAgICAvLyB1cCBpbiBOb2RlJ3Mgb3V0cHV0IGlmIHRoaXMgcmVzdWx0cyBpbiBhbiB1bmhhbmRsZWQgZXhjZXB0aW9uLlxuICAgICAgdGhyb3cgZXI7IC8vIFVuaGFuZGxlZCAnZXJyb3InIGV2ZW50XG4gICAgfVxuICAgIC8vIEF0IGxlYXN0IGdpdmUgc29tZSBraW5kIG9mIGNvbnRleHQgdG8gdGhlIHVzZXJcbiAgICB2YXIgZXJyID0gbmV3IEVycm9yKCdVbmhhbmRsZWQgZXJyb3IuJyArIChlciA/ICcgKCcgKyBlci5tZXNzYWdlICsgJyknIDogJycpKTtcbiAgICBlcnIuY29udGV4dCA9IGVyO1xuICAgIHRocm93IGVycjsgLy8gVW5oYW5kbGVkICdlcnJvcicgZXZlbnRcbiAgfVxuXG4gIHZhciBoYW5kbGVyID0gZXZlbnRzW3R5cGVdO1xuXG4gIGlmIChoYW5kbGVyID09PSB1bmRlZmluZWQpXG4gICAgcmV0dXJuIGZhbHNlO1xuXG4gIGlmICh0eXBlb2YgaGFuZGxlciA9PT0gJ2Z1bmN0aW9uJykge1xuICAgIFJlZmxlY3RBcHBseShoYW5kbGVyLCB0aGlzLCBhcmdzKTtcbiAgfSBlbHNlIHtcbiAgICB2YXIgbGVuID0gaGFuZGxlci5sZW5ndGg7XG4gICAgdmFyIGxpc3RlbmVycyA9IGFycmF5Q2xvbmUoaGFuZGxlciwgbGVuKTtcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IGxlbjsgKytpKVxuICAgICAgUmVmbGVjdEFwcGx5KGxpc3RlbmVyc1tpXSwgdGhpcywgYXJncyk7XG4gIH1cblxuICByZXR1cm4gdHJ1ZTtcbn07XG5cbmZ1bmN0aW9uIF9hZGRMaXN0ZW5lcih0YXJnZXQsIHR5cGUsIGxpc3RlbmVyLCBwcmVwZW5kKSB7XG4gIHZhciBtO1xuICB2YXIgZXZlbnRzO1xuICB2YXIgZXhpc3Rpbmc7XG5cbiAgaWYgKHR5cGVvZiBsaXN0ZW5lciAhPT0gJ2Z1bmN0aW9uJykge1xuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ1RoZSBcImxpc3RlbmVyXCIgYXJndW1lbnQgbXVzdCBiZSBvZiB0eXBlIEZ1bmN0aW9uLiBSZWNlaXZlZCB0eXBlICcgKyB0eXBlb2YgbGlzdGVuZXIpO1xuICB9XG5cbiAgZXZlbnRzID0gdGFyZ2V0Ll9ldmVudHM7XG4gIGlmIChldmVudHMgPT09IHVuZGVmaW5lZCkge1xuICAgIGV2ZW50cyA9IHRhcmdldC5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICB0YXJnZXQuX2V2ZW50c0NvdW50ID0gMDtcbiAgfSBlbHNlIHtcbiAgICAvLyBUbyBhdm9pZCByZWN1cnNpb24gaW4gdGhlIGNhc2UgdGhhdCB0eXBlID09PSBcIm5ld0xpc3RlbmVyXCIhIEJlZm9yZVxuICAgIC8vIGFkZGluZyBpdCB0byB0aGUgbGlzdGVuZXJzLCBmaXJzdCBlbWl0IFwibmV3TGlzdGVuZXJcIi5cbiAgICBpZiAoZXZlbnRzLm5ld0xpc3RlbmVyICE9PSB1bmRlZmluZWQpIHtcbiAgICAgIHRhcmdldC5lbWl0KCduZXdMaXN0ZW5lcicsIHR5cGUsXG4gICAgICAgICAgICAgICAgICBsaXN0ZW5lci5saXN0ZW5lciA/IGxpc3RlbmVyLmxpc3RlbmVyIDogbGlzdGVuZXIpO1xuXG4gICAgICAvLyBSZS1hc3NpZ24gYGV2ZW50c2AgYmVjYXVzZSBhIG5ld0xpc3RlbmVyIGhhbmRsZXIgY291bGQgaGF2ZSBjYXVzZWQgdGhlXG4gICAgICAvLyB0aGlzLl9ldmVudHMgdG8gYmUgYXNzaWduZWQgdG8gYSBuZXcgb2JqZWN0XG4gICAgICBldmVudHMgPSB0YXJnZXQuX2V2ZW50cztcbiAgICB9XG4gICAgZXhpc3RpbmcgPSBldmVudHNbdHlwZV07XG4gIH1cblxuICBpZiAoZXhpc3RpbmcgPT09IHVuZGVmaW5lZCkge1xuICAgIC8vIE9wdGltaXplIHRoZSBjYXNlIG9mIG9uZSBsaXN0ZW5lci4gRG9uJ3QgbmVlZCB0aGUgZXh0cmEgYXJyYXkgb2JqZWN0LlxuICAgIGV4aXN0aW5nID0gZXZlbnRzW3R5cGVdID0gbGlzdGVuZXI7XG4gICAgKyt0YXJnZXQuX2V2ZW50c0NvdW50O1xuICB9IGVsc2Uge1xuICAgIGlmICh0eXBlb2YgZXhpc3RpbmcgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgIC8vIEFkZGluZyB0aGUgc2Vjb25kIGVsZW1lbnQsIG5lZWQgdG8gY2hhbmdlIHRvIGFycmF5LlxuICAgICAgZXhpc3RpbmcgPSBldmVudHNbdHlwZV0gPVxuICAgICAgICBwcmVwZW5kID8gW2xpc3RlbmVyLCBleGlzdGluZ10gOiBbZXhpc3RpbmcsIGxpc3RlbmVyXTtcbiAgICAgIC8vIElmIHdlJ3ZlIGFscmVhZHkgZ290IGFuIGFycmF5LCBqdXN0IGFwcGVuZC5cbiAgICB9IGVsc2UgaWYgKHByZXBlbmQpIHtcbiAgICAgIGV4aXN0aW5nLnVuc2hpZnQobGlzdGVuZXIpO1xuICAgIH0gZWxzZSB7XG4gICAgICBleGlzdGluZy5wdXNoKGxpc3RlbmVyKTtcbiAgICB9XG5cbiAgICAvLyBDaGVjayBmb3IgbGlzdGVuZXIgbGVha1xuICAgIG0gPSAkZ2V0TWF4TGlzdGVuZXJzKHRhcmdldCk7XG4gICAgaWYgKG0gPiAwICYmIGV4aXN0aW5nLmxlbmd0aCA+IG0gJiYgIWV4aXN0aW5nLndhcm5lZCkge1xuICAgICAgZXhpc3Rpbmcud2FybmVkID0gdHJ1ZTtcbiAgICAgIC8vIE5vIGVycm9yIGNvZGUgZm9yIHRoaXMgc2luY2UgaXQgaXMgYSBXYXJuaW5nXG4gICAgICAvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgbm8tcmVzdHJpY3RlZC1zeW50YXhcbiAgICAgIHZhciB3ID0gbmV3IEVycm9yKCdQb3NzaWJsZSBFdmVudEVtaXR0ZXIgbWVtb3J5IGxlYWsgZGV0ZWN0ZWQuICcgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICBleGlzdGluZy5sZW5ndGggKyAnICcgKyBTdHJpbmcodHlwZSkgKyAnIGxpc3RlbmVycyAnICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgJ2FkZGVkLiBVc2UgZW1pdHRlci5zZXRNYXhMaXN0ZW5lcnMoKSB0byAnICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgJ2luY3JlYXNlIGxpbWl0Jyk7XG4gICAgICB3Lm5hbWUgPSAnTWF4TGlzdGVuZXJzRXhjZWVkZWRXYXJuaW5nJztcbiAgICAgIHcuZW1pdHRlciA9IHRhcmdldDtcbiAgICAgIHcudHlwZSA9IHR5cGU7XG4gICAgICB3LmNvdW50ID0gZXhpc3RpbmcubGVuZ3RoO1xuICAgICAgUHJvY2Vzc0VtaXRXYXJuaW5nKHcpO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiB0YXJnZXQ7XG59XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuYWRkTGlzdGVuZXIgPSBmdW5jdGlvbiBhZGRMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcikge1xuICByZXR1cm4gX2FkZExpc3RlbmVyKHRoaXMsIHR5cGUsIGxpc3RlbmVyLCBmYWxzZSk7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9uID0gRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5hZGRMaXN0ZW5lcjtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5wcmVwZW5kTGlzdGVuZXIgPVxuICAgIGZ1bmN0aW9uIHByZXBlbmRMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcikge1xuICAgICAgcmV0dXJuIF9hZGRMaXN0ZW5lcih0aGlzLCB0eXBlLCBsaXN0ZW5lciwgdHJ1ZSk7XG4gICAgfTtcblxuZnVuY3Rpb24gb25jZVdyYXBwZXIoKSB7XG4gIHZhciBhcmdzID0gW107XG4gIGZvciAodmFyIGkgPSAwOyBpIDwgYXJndW1lbnRzLmxlbmd0aDsgaSsrKSBhcmdzLnB1c2goYXJndW1lbnRzW2ldKTtcbiAgaWYgKCF0aGlzLmZpcmVkKSB7XG4gICAgdGhpcy50YXJnZXQucmVtb3ZlTGlzdGVuZXIodGhpcy50eXBlLCB0aGlzLndyYXBGbik7XG4gICAgdGhpcy5maXJlZCA9IHRydWU7XG4gICAgUmVmbGVjdEFwcGx5KHRoaXMubGlzdGVuZXIsIHRoaXMudGFyZ2V0LCBhcmdzKTtcbiAgfVxufVxuXG5mdW5jdGlvbiBfb25jZVdyYXAodGFyZ2V0LCB0eXBlLCBsaXN0ZW5lcikge1xuICB2YXIgc3RhdGUgPSB7IGZpcmVkOiBmYWxzZSwgd3JhcEZuOiB1bmRlZmluZWQsIHRhcmdldDogdGFyZ2V0LCB0eXBlOiB0eXBlLCBsaXN0ZW5lcjogbGlzdGVuZXIgfTtcbiAgdmFyIHdyYXBwZWQgPSBvbmNlV3JhcHBlci5iaW5kKHN0YXRlKTtcbiAgd3JhcHBlZC5saXN0ZW5lciA9IGxpc3RlbmVyO1xuICBzdGF0ZS53cmFwRm4gPSB3cmFwcGVkO1xuICByZXR1cm4gd3JhcHBlZDtcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5vbmNlID0gZnVuY3Rpb24gb25jZSh0eXBlLCBsaXN0ZW5lcikge1xuICBpZiAodHlwZW9mIGxpc3RlbmVyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgdGhyb3cgbmV3IFR5cGVFcnJvcignVGhlIFwibGlzdGVuZXJcIiBhcmd1bWVudCBtdXN0IGJlIG9mIHR5cGUgRnVuY3Rpb24uIFJlY2VpdmVkIHR5cGUgJyArIHR5cGVvZiBsaXN0ZW5lcik7XG4gIH1cbiAgdGhpcy5vbih0eXBlLCBfb25jZVdyYXAodGhpcywgdHlwZSwgbGlzdGVuZXIpKTtcbiAgcmV0dXJuIHRoaXM7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnByZXBlbmRPbmNlTGlzdGVuZXIgPVxuICAgIGZ1bmN0aW9uIHByZXBlbmRPbmNlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXIpIHtcbiAgICAgIGlmICh0eXBlb2YgbGlzdGVuZXIgIT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgdGhyb3cgbmV3IFR5cGVFcnJvcignVGhlIFwibGlzdGVuZXJcIiBhcmd1bWVudCBtdXN0IGJlIG9mIHR5cGUgRnVuY3Rpb24uIFJlY2VpdmVkIHR5cGUgJyArIHR5cGVvZiBsaXN0ZW5lcik7XG4gICAgICB9XG4gICAgICB0aGlzLnByZXBlbmRMaXN0ZW5lcih0eXBlLCBfb25jZVdyYXAodGhpcywgdHlwZSwgbGlzdGVuZXIpKTtcbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH07XG5cbi8vIEVtaXRzIGEgJ3JlbW92ZUxpc3RlbmVyJyBldmVudCBpZiBhbmQgb25seSBpZiB0aGUgbGlzdGVuZXIgd2FzIHJlbW92ZWQuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnJlbW92ZUxpc3RlbmVyID1cbiAgICBmdW5jdGlvbiByZW1vdmVMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcikge1xuICAgICAgdmFyIGxpc3QsIGV2ZW50cywgcG9zaXRpb24sIGksIG9yaWdpbmFsTGlzdGVuZXI7XG5cbiAgICAgIGlmICh0eXBlb2YgbGlzdGVuZXIgIT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgdGhyb3cgbmV3IFR5cGVFcnJvcignVGhlIFwibGlzdGVuZXJcIiBhcmd1bWVudCBtdXN0IGJlIG9mIHR5cGUgRnVuY3Rpb24uIFJlY2VpdmVkIHR5cGUgJyArIHR5cGVvZiBsaXN0ZW5lcik7XG4gICAgICB9XG5cbiAgICAgIGV2ZW50cyA9IHRoaXMuX2V2ZW50cztcbiAgICAgIGlmIChldmVudHMgPT09IHVuZGVmaW5lZClcbiAgICAgICAgcmV0dXJuIHRoaXM7XG5cbiAgICAgIGxpc3QgPSBldmVudHNbdHlwZV07XG4gICAgICBpZiAobGlzdCA9PT0gdW5kZWZpbmVkKVxuICAgICAgICByZXR1cm4gdGhpcztcblxuICAgICAgaWYgKGxpc3QgPT09IGxpc3RlbmVyIHx8IGxpc3QubGlzdGVuZXIgPT09IGxpc3RlbmVyKSB7XG4gICAgICAgIGlmICgtLXRoaXMuX2V2ZW50c0NvdW50ID09PSAwKVxuICAgICAgICAgIHRoaXMuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgICAgIGVsc2Uge1xuICAgICAgICAgIGRlbGV0ZSBldmVudHNbdHlwZV07XG4gICAgICAgICAgaWYgKGV2ZW50cy5yZW1vdmVMaXN0ZW5lcilcbiAgICAgICAgICAgIHRoaXMuZW1pdCgncmVtb3ZlTGlzdGVuZXInLCB0eXBlLCBsaXN0Lmxpc3RlbmVyIHx8IGxpc3RlbmVyKTtcbiAgICAgICAgfVxuICAgICAgfSBlbHNlIGlmICh0eXBlb2YgbGlzdCAhPT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICBwb3NpdGlvbiA9IC0xO1xuXG4gICAgICAgIGZvciAoaSA9IGxpc3QubGVuZ3RoIC0gMTsgaSA+PSAwOyBpLS0pIHtcbiAgICAgICAgICBpZiAobGlzdFtpXSA9PT0gbGlzdGVuZXIgfHwgbGlzdFtpXS5saXN0ZW5lciA9PT0gbGlzdGVuZXIpIHtcbiAgICAgICAgICAgIG9yaWdpbmFsTGlzdGVuZXIgPSBsaXN0W2ldLmxpc3RlbmVyO1xuICAgICAgICAgICAgcG9zaXRpb24gPSBpO1xuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHBvc2l0aW9uIDwgMClcbiAgICAgICAgICByZXR1cm4gdGhpcztcblxuICAgICAgICBpZiAocG9zaXRpb24gPT09IDApXG4gICAgICAgICAgbGlzdC5zaGlmdCgpO1xuICAgICAgICBlbHNlIHtcbiAgICAgICAgICBzcGxpY2VPbmUobGlzdCwgcG9zaXRpb24pO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGxpc3QubGVuZ3RoID09PSAxKVxuICAgICAgICAgIGV2ZW50c1t0eXBlXSA9IGxpc3RbMF07XG5cbiAgICAgICAgaWYgKGV2ZW50cy5yZW1vdmVMaXN0ZW5lciAhPT0gdW5kZWZpbmVkKVxuICAgICAgICAgIHRoaXMuZW1pdCgncmVtb3ZlTGlzdGVuZXInLCB0eXBlLCBvcmlnaW5hbExpc3RlbmVyIHx8IGxpc3RlbmVyKTtcbiAgICAgIH1cblxuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5vZmYgPSBFdmVudEVtaXR0ZXIucHJvdG90eXBlLnJlbW92ZUxpc3RlbmVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnJlbW92ZUFsbExpc3RlbmVycyA9XG4gICAgZnVuY3Rpb24gcmVtb3ZlQWxsTGlzdGVuZXJzKHR5cGUpIHtcbiAgICAgIHZhciBsaXN0ZW5lcnMsIGV2ZW50cywgaTtcblxuICAgICAgZXZlbnRzID0gdGhpcy5fZXZlbnRzO1xuICAgICAgaWYgKGV2ZW50cyA9PT0gdW5kZWZpbmVkKVxuICAgICAgICByZXR1cm4gdGhpcztcblxuICAgICAgLy8gbm90IGxpc3RlbmluZyBmb3IgcmVtb3ZlTGlzdGVuZXIsIG5vIG5lZWQgdG8gZW1pdFxuICAgICAgaWYgKGV2ZW50cy5yZW1vdmVMaXN0ZW5lciA9PT0gdW5kZWZpbmVkKSB7XG4gICAgICAgIGlmIChhcmd1bWVudHMubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICAgICAgICB0aGlzLl9ldmVudHNDb3VudCA9IDA7XG4gICAgICAgIH0gZWxzZSBpZiAoZXZlbnRzW3R5cGVdICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICBpZiAoLS10aGlzLl9ldmVudHNDb3VudCA9PT0gMClcbiAgICAgICAgICAgIHRoaXMuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgICAgICAgZWxzZVxuICAgICAgICAgICAgZGVsZXRlIGV2ZW50c1t0eXBlXTtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gdGhpcztcbiAgICAgIH1cblxuICAgICAgLy8gZW1pdCByZW1vdmVMaXN0ZW5lciBmb3IgYWxsIGxpc3RlbmVycyBvbiBhbGwgZXZlbnRzXG4gICAgICBpZiAoYXJndW1lbnRzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICB2YXIga2V5cyA9IE9iamVjdC5rZXlzKGV2ZW50cyk7XG4gICAgICAgIHZhciBrZXk7XG4gICAgICAgIGZvciAoaSA9IDA7IGkgPCBrZXlzLmxlbmd0aDsgKytpKSB7XG4gICAgICAgICAga2V5ID0ga2V5c1tpXTtcbiAgICAgICAgICBpZiAoa2V5ID09PSAncmVtb3ZlTGlzdGVuZXInKSBjb250aW51ZTtcbiAgICAgICAgICB0aGlzLnJlbW92ZUFsbExpc3RlbmVycyhrZXkpO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMucmVtb3ZlQWxsTGlzdGVuZXJzKCdyZW1vdmVMaXN0ZW5lcicpO1xuICAgICAgICB0aGlzLl9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgICAgICB0aGlzLl9ldmVudHNDb3VudCA9IDA7XG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgICAgfVxuXG4gICAgICBsaXN0ZW5lcnMgPSBldmVudHNbdHlwZV07XG5cbiAgICAgIGlmICh0eXBlb2YgbGlzdGVuZXJzID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIHRoaXMucmVtb3ZlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXJzKTtcbiAgICAgIH0gZWxzZSBpZiAobGlzdGVuZXJzICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgLy8gTElGTyBvcmRlclxuICAgICAgICBmb3IgKGkgPSBsaXN0ZW5lcnMubGVuZ3RoIC0gMTsgaSA+PSAwOyBpLS0pIHtcbiAgICAgICAgICB0aGlzLnJlbW92ZUxpc3RlbmVyKHR5cGUsIGxpc3RlbmVyc1tpXSk7XG4gICAgICAgIH1cbiAgICAgIH1cblxuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfTtcblxuZnVuY3Rpb24gX2xpc3RlbmVycyh0YXJnZXQsIHR5cGUsIHVud3JhcCkge1xuICB2YXIgZXZlbnRzID0gdGFyZ2V0Ll9ldmVudHM7XG5cbiAgaWYgKGV2ZW50cyA9PT0gdW5kZWZpbmVkKVxuICAgIHJldHVybiBbXTtcblxuICB2YXIgZXZsaXN0ZW5lciA9IGV2ZW50c1t0eXBlXTtcbiAgaWYgKGV2bGlzdGVuZXIgPT09IHVuZGVmaW5lZClcbiAgICByZXR1cm4gW107XG5cbiAgaWYgKHR5cGVvZiBldmxpc3RlbmVyID09PSAnZnVuY3Rpb24nKVxuICAgIHJldHVybiB1bndyYXAgPyBbZXZsaXN0ZW5lci5saXN0ZW5lciB8fCBldmxpc3RlbmVyXSA6IFtldmxpc3RlbmVyXTtcblxuICByZXR1cm4gdW53cmFwID9cbiAgICB1bndyYXBMaXN0ZW5lcnMoZXZsaXN0ZW5lcikgOiBhcnJheUNsb25lKGV2bGlzdGVuZXIsIGV2bGlzdGVuZXIubGVuZ3RoKTtcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5saXN0ZW5lcnMgPSBmdW5jdGlvbiBsaXN0ZW5lcnModHlwZSkge1xuICByZXR1cm4gX2xpc3RlbmVycyh0aGlzLCB0eXBlLCB0cnVlKTtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmF3TGlzdGVuZXJzID0gZnVuY3Rpb24gcmF3TGlzdGVuZXJzKHR5cGUpIHtcbiAgcmV0dXJuIF9saXN0ZW5lcnModGhpcywgdHlwZSwgZmFsc2UpO1xufTtcblxuRXZlbnRFbWl0dGVyLmxpc3RlbmVyQ291bnQgPSBmdW5jdGlvbihlbWl0dGVyLCB0eXBlKSB7XG4gIGlmICh0eXBlb2YgZW1pdHRlci5saXN0ZW5lckNvdW50ID09PSAnZnVuY3Rpb24nKSB7XG4gICAgcmV0dXJuIGVtaXR0ZXIubGlzdGVuZXJDb3VudCh0eXBlKTtcbiAgfSBlbHNlIHtcbiAgICByZXR1cm4gbGlzdGVuZXJDb3VudC5jYWxsKGVtaXR0ZXIsIHR5cGUpO1xuICB9XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmxpc3RlbmVyQ291bnQgPSBsaXN0ZW5lckNvdW50O1xuZnVuY3Rpb24gbGlzdGVuZXJDb3VudCh0eXBlKSB7XG4gIHZhciBldmVudHMgPSB0aGlzLl9ldmVudHM7XG5cbiAgaWYgKGV2ZW50cyAhPT0gdW5kZWZpbmVkKSB7XG4gICAgdmFyIGV2bGlzdGVuZXIgPSBldmVudHNbdHlwZV07XG5cbiAgICBpZiAodHlwZW9mIGV2bGlzdGVuZXIgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgIHJldHVybiAxO1xuICAgIH0gZWxzZSBpZiAoZXZsaXN0ZW5lciAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICByZXR1cm4gZXZsaXN0ZW5lci5sZW5ndGg7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIDA7XG59XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuZXZlbnROYW1lcyA9IGZ1bmN0aW9uIGV2ZW50TmFtZXMoKSB7XG4gIHJldHVybiB0aGlzLl9ldmVudHNDb3VudCA+IDAgPyBSZWZsZWN0T3duS2V5cyh0aGlzLl9ldmVudHMpIDogW107XG59O1xuXG5mdW5jdGlvbiBhcnJheUNsb25lKGFyciwgbikge1xuICB2YXIgY29weSA9IG5ldyBBcnJheShuKTtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCBuOyArK2kpXG4gICAgY29weVtpXSA9IGFycltpXTtcbiAgcmV0dXJuIGNvcHk7XG59XG5cbmZ1bmN0aW9uIHNwbGljZU9uZShsaXN0LCBpbmRleCkge1xuICBmb3IgKDsgaW5kZXggKyAxIDwgbGlzdC5sZW5ndGg7IGluZGV4KyspXG4gICAgbGlzdFtpbmRleF0gPSBsaXN0W2luZGV4ICsgMV07XG4gIGxpc3QucG9wKCk7XG59XG5cbmZ1bmN0aW9uIHVud3JhcExpc3RlbmVycyhhcnIpIHtcbiAgdmFyIHJldCA9IG5ldyBBcnJheShhcnIubGVuZ3RoKTtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCByZXQubGVuZ3RoOyArK2kpIHtcbiAgICByZXRbaV0gPSBhcnJbaV0ubGlzdGVuZXIgfHwgYXJyW2ldO1xuICB9XG4gIHJldHVybiByZXQ7XG59XG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vZXZlbnRzL2V2ZW50cy5qc1xuLy8gbW9kdWxlIGlkID0gMTlcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDUgNiA3IDggMTAgMTEgMTIgMTMgMzAgMzIgMzMgMzYgMzcgMzkgNDMgNDQiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgY3JlYXRlT3JkZXJNYXAgZnJvbSAnLi9jcmVhdGUtb3JkZXItbWFwJztcbmltcG9ydCBDdXN0b21lck1hbmFnZXIgZnJvbSAnLi9jdXN0b21lci1tYW5hZ2VyJztcbmltcG9ydCBTaGlwcGluZ1JlbmRlcmVyIGZyb20gJy4vc2hpcHBpbmctcmVuZGVyZXInO1xuaW1wb3J0IENhcnRQcm92aWRlciBmcm9tICcuL2NhcnQtcHJvdmlkZXInO1xuaW1wb3J0IEFkZHJlc3Nlc1JlbmRlcmVyIGZyb20gJy4vYWRkcmVzc2VzLXJlbmRlcmVyJztcbmltcG9ydCBDYXJ0UnVsZXNSZW5kZXJlciBmcm9tICcuL2NhcnQtcnVsZXMtcmVuZGVyZXInO1xuaW1wb3J0IFJvdXRlciBmcm9tICcuLi8uLi8uLi9jb21wb25lbnRzL3JvdXRlcic7XG5pbXBvcnQge0V2ZW50RW1pdHRlcn0gZnJvbSAnLi4vLi4vLi4vY29tcG9uZW50cy9ldmVudC1lbWl0dGVyJztcbmltcG9ydCBDYXJ0RWRpdG9yIGZyb20gJy4vY2FydC1lZGl0b3InO1xuaW1wb3J0IGV2ZW50TWFwIGZyb20gJy4vZXZlbnQtbWFwJztcbmltcG9ydCBDYXJ0UnVsZU1hbmFnZXIgZnJvbSAnLi9jYXJ0LXJ1bGUtbWFuYWdlcic7XG5pbXBvcnQgUHJvZHVjdE1hbmFnZXIgZnJvbSAnLi9wcm9kdWN0LW1hbmFnZXInO1xuaW1wb3J0IFByb2R1Y3RSZW5kZXJlciBmcm9tICcuL3Byb2R1Y3QtcmVuZGVyZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogUGFnZSBPYmplY3QgZm9yIFwiQ3JlYXRlIG9yZGVyXCIgcGFnZVxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDcmVhdGVPcmRlclBhZ2Uge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLmNhcnRJZCA9IG51bGw7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJChjcmVhdGVPcmRlck1hcC5vcmRlckNyZWF0aW9uQ29udGFpbmVyKTtcblxuICAgIHRoaXMuY2FydFByb3ZpZGVyID0gbmV3IENhcnRQcm92aWRlcigpO1xuICAgIHRoaXMuY3VzdG9tZXJNYW5hZ2VyID0gbmV3IEN1c3RvbWVyTWFuYWdlcigpO1xuICAgIHRoaXMuc2hpcHBpbmdSZW5kZXJlciA9IG5ldyBTaGlwcGluZ1JlbmRlcmVyKCk7XG4gICAgdGhpcy5hZGRyZXNzZXNSZW5kZXJlciA9IG5ldyBBZGRyZXNzZXNSZW5kZXJlcigpO1xuICAgIHRoaXMuY2FydFJ1bGVzUmVuZGVyZXIgPSBuZXcgQ2FydFJ1bGVzUmVuZGVyZXIoKTtcbiAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgICB0aGlzLmNhcnRFZGl0b3IgPSBuZXcgQ2FydEVkaXRvcigpO1xuICAgIHRoaXMuY2FydFJ1bGVNYW5hZ2VyID0gbmV3IENhcnRSdWxlTWFuYWdlcigpO1xuICAgIHRoaXMucHJvZHVjdE1hbmFnZXIgPSBuZXcgUHJvZHVjdE1hbmFnZXIoKTtcbiAgICB0aGlzLnByb2R1Y3RSZW5kZXJlciA9IG5ldyBQcm9kdWN0UmVuZGVyZXIoKTtcblxuICAgIHRoaXMuX2luaXRMaXN0ZW5lcnMoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0aWFsaXplcyBldmVudCBsaXN0ZW5lcnNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0TGlzdGVuZXJzKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignaW5wdXQnLCBjcmVhdGVPcmRlck1hcC5jdXN0b21lclNlYXJjaElucHV0LCBlID0+IHRoaXMuX2luaXRDdXN0b21lclNlYXJjaChlKSk7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsIGNyZWF0ZU9yZGVyTWFwLmNob29zZUN1c3RvbWVyQnRuLCBlID0+IHRoaXMuX2luaXRDdXN0b21lclNlbGVjdChlKSk7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsIGNyZWF0ZU9yZGVyTWFwLnVzZUNhcnRCdG4sIGUgPT4gdGhpcy5faW5pdENhcnRTZWxlY3QoZSkpO1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2xpY2snLCBjcmVhdGVPcmRlck1hcC51c2VPcmRlckJ0biwgZSA9PiB0aGlzLl9pbml0RHVwbGljYXRlT3JkZXJDYXJ0KGUpKTtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2lucHV0JywgY3JlYXRlT3JkZXJNYXAucHJvZHVjdFNlYXJjaCwgZSA9PiB0aGlzLl9pbml0UHJvZHVjdFNlYXJjaChlKSk7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdpbnB1dCcsIGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlU2VhcmNoSW5wdXQsIGUgPT4gdGhpcy5faW5pdENhcnRSdWxlU2VhcmNoKGUpKTtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2JsdXInLCBjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZVNlYXJjaElucHV0LCAoKSA9PiB0aGlzLmNhcnRSdWxlTWFuYWdlci5zdG9wU2VhcmNoaW5nKCkpO1xuICAgIHRoaXMuX2luaXRDYXJ0RWRpdGluZygpO1xuICAgIHRoaXMuX29uQ2FydExvYWRlZCgpO1xuICAgIHRoaXMuX29uQ2FydEFkZHJlc3Nlc0NoYW5nZWQoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEZWxlZ2F0ZXMgYWN0aW9ucyB0byBldmVudHMgYXNzb2NpYXRlZCB3aXRoIGNhcnQgdXBkYXRlIChlLmcuIGNoYW5nZSBjYXJ0IGFkZHJlc3MpXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdENhcnRFZGl0aW5nKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2hhbmdlJywgY3JlYXRlT3JkZXJNYXAuZGVsaXZlcnlPcHRpb25TZWxlY3QsIGUgPT5cbiAgICAgIHRoaXMuY2FydEVkaXRvci5jaGFuZ2VEZWxpdmVyeU9wdGlvbih0aGlzLmNhcnRJZCwgZS5jdXJyZW50VGFyZ2V0LnZhbHVlKVxuICAgICk7XG5cbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NoYW5nZScsIGNyZWF0ZU9yZGVyTWFwLmZyZWVTaGlwcGluZ1N3aXRjaCwgZSA9PlxuICAgICAgdGhpcy5jYXJ0RWRpdG9yLnNldEZyZWVTaGlwcGluZyh0aGlzLmNhcnRJZCwgZS5jdXJyZW50VGFyZ2V0LnZhbHVlKVxuICAgICk7XG5cbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgY3JlYXRlT3JkZXJNYXAuYWRkVG9DYXJ0QnV0dG9uLCAoKSA9PlxuICAgICAgdGhpcy5wcm9kdWN0TWFuYWdlci5hZGRQcm9kdWN0VG9DYXJ0KHRoaXMuY2FydElkKVxuICAgICk7XG5cbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NoYW5nZScsIGNyZWF0ZU9yZGVyTWFwLmFkZHJlc3NTZWxlY3QsICgpID0+IHRoaXMuX2NoYW5nZUNhcnRBZGRyZXNzZXMoKSk7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsIGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RSZW1vdmVCdG4sIGUgPT4gdGhpcy5faW5pdFByb2R1Y3RSZW1vdmVGcm9tQ2FydChlKSk7XG5cbiAgICB0aGlzLl9hZGRDYXJ0UnVsZVRvQ2FydCgpO1xuICAgIHRoaXMuX3JlbW92ZUNhcnRSdWxlRnJvbUNhcnQoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGZvciBldmVudCB3aGVuIGNhcnQgaXMgbG9hZGVkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25DYXJ0TG9hZGVkKCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5jYXJ0TG9hZGVkLCAoY2FydEluZm8pID0+IHtcbiAgICAgIHRoaXMuY2FydElkID0gY2FydEluZm8uY2FydElkO1xuICAgICAgdGhpcy5fcmVuZGVyQ2FydEluZm8oY2FydEluZm8pO1xuICAgICAgdGhpcy5jdXN0b21lck1hbmFnZXIubG9hZEN1c3RvbWVyQ2FydHModGhpcy5jYXJ0SWQpO1xuICAgICAgdGhpcy5jdXN0b21lck1hbmFnZXIubG9hZEN1c3RvbWVyT3JkZXJzKCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBmb3IgY2FydCBhZGRyZXNzZXMgdXBkYXRlIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25DYXJ0QWRkcmVzc2VzQ2hhbmdlZCgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAuY2FydEFkZHJlc3Nlc0NoYW5nZWQsIChjYXJ0SW5mbykgPT4ge1xuICAgICAgdGhpcy5hZGRyZXNzZXNSZW5kZXJlci5yZW5kZXIoY2FydEluZm8uYWRkcmVzc2VzKTtcbiAgICAgIHRoaXMuc2hpcHBpbmdSZW5kZXJlci5yZW5kZXIoY2FydEluZm8uc2hpcHBpbmcsIGNhcnRJbmZvLnByb2R1Y3RzLmxlbmd0aCA9PT0gMCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBmb3IgY2FydCBkZWxpdmVyeSBvcHRpb24gdXBkYXRlIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25EZWxpdmVyeU9wdGlvbkNoYW5nZWQoKSB7XG4gICAgRXZlbnRFbWl0dGVyLm9uKGV2ZW50TWFwLmNhcnREZWxpdmVyeU9wdGlvbkNoYW5nZWQsIChjYXJ0SW5mbykgPT4ge1xuICAgICAgdGhpcy5zaGlwcGluZ1JlbmRlcmVyLnJlbmRlcihjYXJ0SW5mby5zaGlwcGluZywgY2FydEluZm8ucHJvZHVjdHMubGVuZ3RoID09PSAwKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGZvciBjYXJ0IGZyZWUgc2hpcHBpbmcgdXBkYXRlIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25GcmVlU2hpcHBpbmdDaGFuZ2VkKCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5jYXJ0RnJlZVNoaXBwaW5nU2V0LCAoY2FydEluZm8pID0+IHtcbiAgICAgIHRoaXMuc2hpcHBpbmdSZW5kZXJlci5yZW5kZXIoY2FydEluZm8uc2hpcHBpbmcsIGNhcnRJbmZvLnByb2R1Y3RzLmxlbmd0aCA9PT0gMCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogSW5pdCBjdXN0b21lciBzZWFyY2hpbmdcbiAgICpcbiAgICogQHBhcmFtIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdEN1c3RvbWVyU2VhcmNoKGV2ZW50KSB7XG4gICAgc2V0VGltZW91dCgoKSA9PiB0aGlzLmN1c3RvbWVyTWFuYWdlci5zZWFyY2goJChldmVudC5jdXJyZW50VGFyZ2V0KS52YWwoKSksIDMwMCk7XG4gIH1cblxuICAvKipcbiAgICogSW5pdCBzZWxlY3RpbmcgY3VzdG9tZXIgZm9yIHdoaWNoIG9yZGVyIGlzIGJlaW5nIGNyZWF0ZWRcbiAgICpcbiAgICogQHBhcmFtIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdEN1c3RvbWVyU2VsZWN0KGV2ZW50KSB7XG4gICAgY29uc3QgY3VzdG9tZXJJZCA9IHRoaXMuY3VzdG9tZXJNYW5hZ2VyLnNlbGVjdEN1c3RvbWVyKGV2ZW50KTtcbiAgICB0aGlzLmNhcnRQcm92aWRlci5sb2FkRW1wdHlDYXJ0KGN1c3RvbWVySWQpO1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRzIHNlbGVjdGluZyBjYXJ0IHRvIGxvYWRcbiAgICpcbiAgICogQHBhcmFtIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdENhcnRTZWxlY3QoZXZlbnQpIHtcbiAgICBjb25zdCBjYXJ0SWQgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ2NhcnQtaWQnKTtcbiAgICB0aGlzLmNhcnRQcm92aWRlci5nZXRDYXJ0KGNhcnRJZCk7XG4gIH1cblxuICAvKipcbiAgICogSW5pdHMgZHVwbGljYXRpbmcgb3JkZXIgY2FydFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXREdXBsaWNhdGVPcmRlckNhcnQoZXZlbnQpIHtcbiAgICBjb25zdCBvcmRlcklkID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdvcmRlci1pZCcpO1xuICAgIHRoaXMuY2FydFByb3ZpZGVyLmR1cGxpY2F0ZU9yZGVyQ2FydChvcmRlcklkKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBUcmlnZ2VycyBjYXJ0IHJ1bGUgc2VhcmNoaW5nXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdENhcnRSdWxlU2VhcmNoKGV2ZW50KSB7XG4gICAgY29uc3Qgc2VhcmNoUGhyYXNlID0gZXZlbnQuY3VycmVudFRhcmdldC52YWx1ZTtcbiAgICB0aGlzLmNhcnRSdWxlTWFuYWdlci5zZWFyY2goc2VhcmNoUGhyYXNlKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBUcmlnZ2VycyBjYXJ0IHJ1bGUgc2VsZWN0XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfYWRkQ2FydFJ1bGVUb0NhcnQoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdtb3VzZWRvd24nLCBjcmVhdGVPcmRlck1hcC5mb3VuZENhcnRSdWxlTGlzdEl0ZW0sIChldmVudCkgPT4ge1xuICAgICAgLy8gcHJldmVudCBibHVyIGV2ZW50IHRvIGFsbG93IHNlbGVjdGluZyBjYXJ0IHJ1bGVcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBjb25zdCBjYXJ0UnVsZUlkID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdjYXJ0LXJ1bGUtaWQnKTtcbiAgICAgIHRoaXMuY2FydFJ1bGVNYW5hZ2VyLmFkZENhcnRSdWxlVG9DYXJ0KGNhcnRSdWxlSWQsIHRoaXMuY2FydElkKTtcblxuICAgICAgLy8gbWFudWFsbHkgZmlyZSBibHVyIGV2ZW50IGFmdGVyIGNhcnQgcnVsZSBpcyBzZWxlY3RlZC5cbiAgICB9KS5vbignY2xpY2snLCBjcmVhdGVPcmRlck1hcC5mb3VuZENhcnRSdWxlTGlzdEl0ZW0sICgpID0+IHtcbiAgICAgICQoY3JlYXRlT3JkZXJNYXAuY2FydFJ1bGVTZWFyY2hJbnB1dCkuYmx1cigpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFRyaWdnZXJzIGNhcnQgcnVsZSByZW1vdmFsIGZyb20gY2FydFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbW92ZUNhcnRSdWxlRnJvbUNhcnQoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsIGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlRGVsZXRlQnRuLCAoZXZlbnQpID0+IHtcbiAgICAgIHRoaXMuY2FydFJ1bGVNYW5hZ2VyLnJlbW92ZUNhcnRSdWxlRnJvbUNhcnQoJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdjYXJ0LXJ1bGUtaWQnKSwgdGhpcy5jYXJ0SWQpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRzIHByb2R1Y3Qgc2VhcmNoaW5nXG4gICAqXG4gICAqIEBwYXJhbSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRQcm9kdWN0U2VhcmNoKGV2ZW50KSB7XG4gICAgY29uc3QgJHByb2R1Y3RTZWFyY2hJbnB1dCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgY29uc3Qgc2VhcmNoUGhyYXNlID0gJHByb2R1Y3RTZWFyY2hJbnB1dC52YWwoKTtcblxuICAgIHNldFRpbWVvdXQoKCkgPT4gdGhpcy5wcm9kdWN0TWFuYWdlci5zZWFyY2goc2VhcmNoUGhyYXNlKSwgMzAwKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0cyBwcm9kdWN0IHJlbW92aW5nIGZyb20gY2FydFxuICAgKlxuICAgKiBAcGFyYW0gZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0UHJvZHVjdFJlbW92ZUZyb21DYXJ0KGV2ZW50KSB7XG4gICAgY29uc3QgcHJvZHVjdCA9IHtcbiAgICAgIHByb2R1Y3RJZDogJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdwcm9kdWN0LWlkJyksXG4gICAgICBhdHRyaWJ1dGVJZDogJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdhdHRyaWJ1dGUtaWQnKSxcbiAgICAgIGN1c3RvbWl6YXRpb25JZDogJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdjdXN0b21pemF0aW9uLWlkJyksXG4gICAgfTtcblxuICAgIHRoaXMucHJvZHVjdE1hbmFnZXIucmVtb3ZlUHJvZHVjdEZyb21DYXJ0KHRoaXMuY2FydElkLCBwcm9kdWN0KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIGNhcnQgc3VtbWFyeSBvbiB0aGUgcGFnZVxuICAgKlxuICAgKiBAcGFyYW0ge09iamVjdH0gY2FydEluZm9cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW5kZXJDYXJ0SW5mbyhjYXJ0SW5mbykge1xuICAgIHRoaXMuYWRkcmVzc2VzUmVuZGVyZXIucmVuZGVyKGNhcnRJbmZvLmFkZHJlc3Nlcyk7XG4gICAgdGhpcy5jYXJ0UnVsZXNSZW5kZXJlci5yZW5kZXJDYXJ0UnVsZXNCbG9jayhjYXJ0SW5mby5jYXJ0UnVsZXMsIGNhcnRJbmZvLnByb2R1Y3RzLmxlbmd0aCA9PT0gMCk7XG4gICAgdGhpcy5zaGlwcGluZ1JlbmRlcmVyLnJlbmRlcihjYXJ0SW5mby5zaGlwcGluZywgY2FydEluZm8ucHJvZHVjdHMubGVuZ3RoID09PSAwKTtcbiAgICB0aGlzLnByb2R1Y3RSZW5kZXJlci5yZW5kZXJMaXN0KGNhcnRJbmZvLnByb2R1Y3RzKTtcbiAgICAvLyBAdG9kbzogcmVuZGVyIFN1bW1hcnkgYmxvY2sgd2hlbiBhdCBsZWFzdCAxIHByb2R1Y3QgaXMgaW4gY2FydFxuICAgIC8vIGFuZCBkZWxpdmVyeSBvcHRpb25zIGFyZSBhdmFpbGFibGVcblxuICAgICQoY3JlYXRlT3JkZXJNYXAuY2FydEJsb2NrKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogQ2hhbmdlcyBjYXJ0IGFkZHJlc3Nlc1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NoYW5nZUNhcnRBZGRyZXNzZXMoKSB7XG4gICAgY29uc3QgYWRkcmVzc2VzID0ge1xuICAgICAgZGVsaXZlcnlBZGRyZXNzSWQ6ICQoY3JlYXRlT3JkZXJNYXAuZGVsaXZlcnlBZGRyZXNzU2VsZWN0KS52YWwoKSxcbiAgICAgIGludm9pY2VBZGRyZXNzSWQ6ICQoY3JlYXRlT3JkZXJNYXAuaW52b2ljZUFkZHJlc3NTZWxlY3QpLnZhbCgpLFxuICAgIH07XG5cbiAgICB0aGlzLmNhcnRFZGl0b3IuY2hhbmdlQ2FydEFkZHJlc3Nlcyh0aGlzLmNhcnRJZCwgYWRkcmVzc2VzKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL2NyZWF0ZS1vcmRlci1wYWdlLmpzIiwibW9kdWxlLmV4cG9ydHMgPSB7XCJiYXNlX3VybFwiOlwiXCIsXCJyb3V0ZXNcIjp7XCJhZG1pbl9wcm9kdWN0c19zZWFyY2hcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3NlbGwvY2F0YWxvZy9wcm9kdWN0cy9zZWFyY2hcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOltdLFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0X3J1bGVzX3NlYXJjaFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc2VsbC9jYXRhbG9nL2NhcnQtcnVsZXMvc2VhcmNoXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY3VzdG9tZXJzX3ZpZXdcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3ZpZXdcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY3VzdG9tZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9jdXN0b21lcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImN1c3RvbWVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIixcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY3VzdG9tZXJzX3NlYXJjaFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc2VsbC9jdXN0b21lcnMvc2VhcmNoXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY3VzdG9tZXJzX2NhcnRzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9jYXJ0c1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjdXN0b21lcklkXCJdLFtcInRleHRcIixcIi9zZWxsL2N1c3RvbWVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY3VzdG9tZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jdXN0b21lcnNfb3JkZXJzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9vcmRlcnNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY3VzdG9tZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9jdXN0b21lcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImN1c3RvbWVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfdmlld1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvdmlld1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfaW5mb1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvaW5mb1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfY3JlYXRlXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0cy9uZXdcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOltdLFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfZWRpdF9hZGRyZXNzZXNcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2FkZHJlc3Nlc1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2VkaXRfY2FycmllclwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvY2FycmllclwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX3NldF9mcmVlX3NoaXBwaW5nXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9ydWxlcy9mcmVlLXNoaXBwaW5nXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfYWRkX2NhcnRfcnVsZVwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvY2FydC1ydWxlc1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiW14vXSsrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2RlbGV0ZV9jYXJ0X3J1bGVcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2RlbGV0ZVwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiW14vXSsrXCIsXCJjYXJ0UnVsZUlkXCJdLFtcInRleHRcIixcIi9jYXJ0LXJ1bGVzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJbXi9dKytcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOltdLFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfYWRkX3Byb2R1Y3RcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3Byb2R1Y3RzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCIsXCJwcm9kdWN0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2RlbGV0ZV9wcm9kdWN0XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9kZWxldGUtcHJvZHVjdFwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc192aWV3XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi92aWV3XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJbXi9dKytcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W10sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX2R1cGxpY2F0ZV9jYXJ0XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9kdXBsaWNhdGUtY2FydFwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfX0sXCJwcmVmaXhcIjpcIlwiLFwiaG9zdFwiOlwibG9jYWxob3N0XCIsXCJwb3J0XCI6XCJcIixcInNjaGVtZVwiOlwiaHR0cFwiLFwibG9jYWxlXCI6W119XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9qcy9mb3NfanNfcm91dGVzLmpzb25cbi8vIG1vZHVsZSBpZCA9IDMxNFxuLy8gbW9kdWxlIGNodW5rcyA9IDEzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBFbmNhcHN1bGF0ZXMgc2VsZWN0b3JzIGZvciBcIkNyZWF0ZSBvcmRlclwiIHBhZ2VcbiAqL1xuZXhwb3J0IGRlZmF1bHQge1xuICBvcmRlckNyZWF0aW9uQ29udGFpbmVyOiAnI29yZGVyLWNyZWF0aW9uLWNvbnRhaW5lcicsXG5cbiAgLy8gc2VsZWN0b3JzIHJlbGF0ZWQgdG8gY3VzdG9tZXIgYmxvY2tcbiAgY3VzdG9tZXJTZWFyY2hJbnB1dDogJyNjdXN0b21lci1zZWFyY2gtaW5wdXQnLFxuICBjdXN0b21lclNlYXJjaFJlc3VsdHNCbG9jazogJy5qcy1jdXN0b21lci1zZWFyY2gtcmVzdWx0cycsXG4gIGN1c3RvbWVyU2VhcmNoUmVzdWx0VGVtcGxhdGU6ICcjY3VzdG9tZXItc2VhcmNoLXJlc3VsdC10ZW1wbGF0ZScsXG4gIGNoYW5nZUN1c3RvbWVyQnRuOiAnLmpzLWNoYW5nZS1jdXN0b21lci1idG4nLFxuICBjdXN0b21lclNlYXJjaFJvdzogJy5qcy1zZWFyY2gtY3VzdG9tZXItcm93JyxcbiAgY2hvb3NlQ3VzdG9tZXJCdG46ICcuanMtY2hvb3NlLWN1c3RvbWVyLWJ0bicsXG4gIG5vdFNlbGVjdGVkQ3VzdG9tZXJTZWFyY2hSZXN1bHRzOiAnLmpzLWN1c3RvbWVyLXNlYXJjaC1yZXN1bHQ6bm90KC5ib3JkZXItc3VjY2VzcyknLFxuICBjdXN0b21lclNlYXJjaFJlc3VsdE5hbWU6ICcuanMtY3VzdG9tZXItbmFtZScsXG4gIGN1c3RvbWVyU2VhcmNoUmVzdWx0RW1haWw6ICcuanMtY3VzdG9tZXItZW1haWwnLFxuICBjdXN0b21lclNlYXJjaFJlc3VsdElkOiAnLmpzLWN1c3RvbWVyLWlkJyxcbiAgY3VzdG9tZXJTZWFyY2hSZXN1bHRCaXJ0aGRheTogJy5qcy1jdXN0b21lci1iaXJ0aGRheScsXG4gIGN1c3RvbWVyRGV0YWlsc0J0bjogJy5qcy1kZXRhaWxzLWN1c3RvbWVyLWJ0bicsXG4gIGN1c3RvbWVyU2VhcmNoUmVzdWx0Q29sdW1uOiAnLmpzLWN1c3RvbWVyLXNlYXJjaC1yZXN1bHQtY29sJyxcbiAgY3VzdG9tZXJTZWFyY2hCbG9jazogJyNjdXN0b21lci1zZWFyY2gtYmxvY2snLFxuICBjdXN0b21lckNhcnRzVGFiOiAnLmpzLWN1c3RvbWVyLWNhcnRzLXRhYicsXG4gIGN1c3RvbWVyT3JkZXJzVGFiOiAnLmpzLWN1c3RvbWVyLW9yZGVycy10YWInLFxuICBjdXN0b21lckNhcnRzVGFibGU6ICcjY3VzdG9tZXItY2FydHMtdGFibGUnLFxuICBjdXN0b21lckNhcnRzVGFibGVSb3dUZW1wbGF0ZTogJyNjdXN0b21lci1jYXJ0cy10YWJsZS1yb3ctdGVtcGxhdGUnLFxuICBjdXN0b21lckNoZWNrb3V0SGlzdG9yeTogJyNjdXN0b21lci1jaGVja291dC1oaXN0b3J5JyxcbiAgY3VzdG9tZXJPcmRlcnNUYWJsZTogJyNjdXN0b21lci1vcmRlcnMtdGFibGUnLFxuICBjdXN0b21lck9yZGVyc1RhYmxlUm93VGVtcGxhdGU6ICcjY3VzdG9tZXItb3JkZXJzLXRhYmxlLXJvdy10ZW1wbGF0ZScsXG4gIGNhcnRSdWxlc1RhYmxlOiAnI2NhcnQtcnVsZXMtdGFibGUnLFxuICBjYXJ0UnVsZXNUYWJsZVJvd1RlbXBsYXRlOiAnI2NhcnQtcnVsZXMtdGFibGUtcm93LXRlbXBsYXRlJyxcbiAgdXNlQ2FydEJ0bjogJy5qcy11c2UtY2FydC1idG4nLFxuICBjYXJ0RGV0YWlsc0J0bjogJy5qcy1jYXJ0LWRldGFpbHMtYnRuJyxcbiAgY2FydElkRmllbGQ6ICcuanMtY2FydC1pZCcsXG4gIGNhcnREYXRlRmllbGQ6ICcuanMtY2FydC1kYXRlJyxcbiAgY2FydFRvdGFsRmllbGQ6ICcuanMtY2FydC10b3RhbCcsXG4gIHVzZU9yZGVyQnRuOiAnLmpzLXVzZS1vcmRlci1idG4nLFxuICBvcmRlckRldGFpbHNCdG46ICcuanMtb3JkZXItZGV0YWlscy1idG4nLFxuICBvcmRlcklkRmllbGQ6ICcuanMtb3JkZXItaWQnLFxuICBvcmRlckRhdGVGaWVsZDogJy5qcy1vcmRlci1kYXRlJyxcbiAgb3JkZXJQcm9kdWN0c0ZpZWxkOiAnLmpzLW9yZGVyLXByb2R1Y3RzJyxcbiAgb3JkZXJUb3RhbEZpZWxkOiAnLmpzLW9yZGVyLXRvdGFsLXBhaWQnLFxuICBvcmRlclN0YXR1c0ZpZWxkOiAnLmpzLW9yZGVyLXN0YXR1cycsXG5cbiAgLy8gc2VsZWN0b3JzIHJlbGF0ZWQgdG8gY2FydCBibG9ja1xuICBjYXJ0QmxvY2s6ICcjY2FydC1ibG9jaycsXG5cbiAgLy8gc2VsZWN0b3JzIHJlbGF0ZWQgdG8gY2FydFJ1bGVzIGJsb2NrXG4gIGNhcnRSdWxlc0Jsb2NrOiAnI2NhcnQtcnVsZXMtYmxvY2snLFxuICBjYXJ0UnVsZVNlYXJjaElucHV0OiAnI3NlYXJjaC1jYXJ0LXJ1bGVzLWlucHV0JyxcbiAgY2FydFJ1bGVzU2VhcmNoUmVzdWx0Qm94OiAnI3NlYXJjaC1jYXJ0LXJ1bGVzLXJlc3VsdC1ib3gnLFxuICBjYXJ0UnVsZXNOb3RGb3VuZFRlbXBsYXRlOiAnI2NhcnQtcnVsZXMtbm90LWZvdW5kLXRlbXBsYXRlJyxcbiAgZm91bmRDYXJ0UnVsZVRlbXBsYXRlOiAnI2ZvdW5kLWNhcnQtcnVsZS10ZW1wbGF0ZScsXG4gIGZvdW5kQ2FydFJ1bGVMaXN0SXRlbTogJy5qcy1mb3VuZC1jYXJ0LXJ1bGUnLFxuICBjYXJ0UnVsZU5hbWVGaWVsZDogJy5qcy1jYXJ0LXJ1bGUtbmFtZScsXG4gIGNhcnRSdWxlRGVzY3JpcHRpb25GaWVsZDogJy5qcy1jYXJ0LXJ1bGUtZGVzY3JpcHRpb24nLFxuICBjYXJ0UnVsZVZhbHVlRmllbGQ6ICcuanMtY2FydC1ydWxlLXZhbHVlJyxcbiAgY2FydFJ1bGVEZWxldGVCdG46ICcuanMtY2FydC1ydWxlLWRlbGV0ZS1idG4nLFxuICBjYXJ0UnVsZUVycm9yQmxvY2s6ICcjanMtY2FydC1ydWxlLWVycm9yLWJsb2NrJyxcbiAgY2FydFJ1bGVFcnJvclRleHQ6ICcjanMtY2FydC1ydWxlLWVycm9yLXRleHQnLFxuXG4gIC8vIHNlbGVjdG9ycyByZWxhdGVkIHRvIGFkZHJlc3NlcyBibG9ja1xuICBhZGRyZXNzZXNCbG9jazogJyNhZGRyZXNzZXMtYmxvY2snLFxuICBkZWxpdmVyeUFkZHJlc3NEZXRhaWxzOiAnI2RlbGl2ZXJ5LWFkZHJlc3MtZGV0YWlscycsXG4gIGludm9pY2VBZGRyZXNzRGV0YWlsczogJyNpbnZvaWNlLWFkZHJlc3MtZGV0YWlscycsXG4gIGRlbGl2ZXJ5QWRkcmVzc1NlbGVjdDogJyNkZWxpdmVyeS1hZGRyZXNzLXNlbGVjdCcsXG4gIGludm9pY2VBZGRyZXNzU2VsZWN0OiAnI2ludm9pY2UtYWRkcmVzcy1zZWxlY3QnLFxuICBhZGRyZXNzU2VsZWN0OiAnLmpzLWFkZHJlc3Mtc2VsZWN0JyxcbiAgYWRkcmVzc2VzQ29udGVudDogJyNhZGRyZXNzZXMtY29udGVudCcsXG4gIGFkZHJlc3Nlc1dhcm5pbmc6ICcjYWRkcmVzc2VzLXdhcm5pbmcnLFxuXG4gIC8vIHNlbGVjdG9ycyByZWxhdGVkIHRvIHN1bW1hcnkgYmxvY2tcbiAgc3VtbWFyeUJsb2NrOiAnI3N1bW1hcnktYmxvY2snLFxuXG4gIC8vIHNlbGVjdG9ycyByZWxhdGVkIHRvIHNoaXBwaW5nIGJsb2NrXG4gIHNoaXBwaW5nQmxvY2s6ICcjc2hpcHBpbmctYmxvY2snLFxuICBzaGlwcGluZ0Zvcm06ICcuanMtc2hpcHBpbmctZm9ybScsXG4gIG5vQ2FycmllckJsb2NrOiAnLmpzLW5vLWNhcnJpZXItYmxvY2snLFxuICBkZWxpdmVyeU9wdGlvblNlbGVjdDogJyNkZWxpdmVyeS1vcHRpb24tc2VsZWN0JyxcbiAgdG90YWxTaGlwcGluZ0ZpZWxkOiAnLmpzLXRvdGFsLXNoaXBwaW5nJyxcbiAgZnJlZVNoaXBwaW5nU3dpdGNoOiAnLmpzLWZyZWUtc2hpcHBpbmctc3dpdGNoJyxcblxuICAvLyBzZWxlY3RvcnMgcmVsYXRlZCB0byBjYXJ0IHByb2R1Y3RzIGJsb2NrXG4gIHByb2R1Y3RTZWFyY2g6ICcjcHJvZHVjdC1zZWFyY2gnLFxuICBjb21iaW5hdGlvbnNTZWxlY3Q6ICcjY29tYmluYXRpb24tc2VsZWN0JyxcbiAgcHJvZHVjdFJlc3VsdEJsb2NrOiAnI3Byb2R1Y3Qtc2VhcmNoLXJlc3VsdHMnLFxuICBwcm9kdWN0U2VsZWN0OiAnI3Byb2R1Y3Qtc2VsZWN0JyxcbiAgcXVhbnRpdHlJbnB1dDogJyNxdWFudGl0eS1pbnB1dCcsXG4gIGluU3RvY2tDb3VudGVyOiAnLmpzLWluLXN0b2NrLWNvdW50ZXInLFxuICBjb21iaW5hdGlvbnNUZW1wbGF0ZTogJyNjb21iaW5hdGlvbnMtdGVtcGxhdGUnLFxuICBjb21iaW5hdGlvbnNSb3c6ICcuanMtY29tYmluYXRpb25zLXJvdycsXG4gIHByb2R1Y3RTZWxlY3RSb3c6ICcuanMtcHJvZHVjdC1zZWxlY3Qtcm93JyxcbiAgcHJvZHVjdEN1c3RvbUZpZWxkc0NvbnRhaW5lcjogJyNqcy1jdXN0b20tZmllbGRzLWNvbnRhaW5lcicsXG4gIHByb2R1Y3RDdXN0b21pemF0aW9uQ29udGFpbmVyOiAnI2pzLWN1c3RvbWl6YXRpb24tY29udGFpbmVyJyxcbiAgcHJvZHVjdEN1c3RvbUZpbGVUZW1wbGF0ZTogJyNqcy1wcm9kdWN0LWN1c3RvbS1maWxlLXRlbXBsYXRlJyxcbiAgcHJvZHVjdEN1c3RvbVRleHRUZW1wbGF0ZTogJyNqcy1wcm9kdWN0LWN1c3RvbS10ZXh0LXRlbXBsYXRlJyxcbiAgcHJvZHVjdEN1c3RvbUlucHV0TGFiZWw6ICcuanMtcHJvZHVjdC1jdXN0b20taW5wdXQtbGFiZWwnLFxuICBwcm9kdWN0Q3VzdG9tSW5wdXQ6ICcuanMtcHJvZHVjdC1jdXN0b20taW5wdXQnLFxuICBxdWFudGl0eVJvdzogJy5qcy1xdWFudGl0eS1yb3cnLFxuICBhZGRUb0NhcnRCdXR0b246ICcjYWRkLXByb2R1Y3QtdG8tY2FydC1idG4nLFxuICBwcm9kdWN0c1RhYmxlOiAnI3Byb2R1Y3RzLXRhYmxlJyxcbiAgcHJvZHVjdHNUYWJsZVJvd1RlbXBsYXRlOiAnI3Byb2R1Y3RzLXRhYmxlLXJvdy10ZW1wbGF0ZScsXG4gIHByb2R1Y3RJbWFnZUZpZWxkOiAnLmpzLXByb2R1Y3QtaW1hZ2UnLFxuICBwcm9kdWN0TmFtZUZpZWxkOiAnLmpzLXByb2R1Y3QtbmFtZScsXG4gIHByb2R1Y3RBdHRyRmllbGQ6ICcuanMtcHJvZHVjdC1hdHRyJyxcbiAgcHJvZHVjdFJlZmVyZW5jZUZpZWxkOiAnLmpzLXByb2R1Y3QtcmVmJyxcbiAgcHJvZHVjdFVuaXRQcmljZUlucHV0OiAnLmpzLXByb2R1Y3QtdW5pdC1pbnB1dCcsXG4gIHByb2R1Y3RUb3RhbFByaWNlRmllbGQ6ICcuanMtcHJvZHVjdC10b3RhbC1wcmljZScsXG4gIHByb2R1Y3RSZW1vdmVCdG46ICcuanMtcHJvZHVjdC1yZW1vdmUtYnRuJyxcbiAgcHJvZHVjdFRheFdhcm5pbmc6ICcuanMtdGF4LXdhcm5pbmcnLFxuICBub1Byb2R1Y3RzRm91bmRXYXJuaW5nOiAnLmpzLW5vLXByb2R1Y3RzLWZvdW5kJyxcbn07XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY3JlYXRlLW9yZGVyLW1hcC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5pbXBvcnQgQ3JlYXRlT3JkZXJQYWdlIGZyb20gJy4vY3JlYXRlL2NyZWF0ZS1vcmRlci1wYWdlJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKGRvY3VtZW50KS5yZWFkeSgoKSA9PiB7XG4gIG5ldyBDcmVhdGVPcmRlclBhZ2UoKTtcbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvY3JlYXRlLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IGNyZWF0ZU9yZGVyUGFnZU1hcCBmcm9tICcuL2NyZWF0ZS1vcmRlci1tYXAnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogUmVuZGVycyBEZWxpdmVyeSAmIEludm9pY2UgYWRkcmVzc2VzIHNlbGVjdFxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBBZGRyZXNzZXNSZW5kZXJlciB7XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7QXJyYXl9IGFkZHJlc3Nlc1xuICAgKi9cbiAgcmVuZGVyKGFkZHJlc3Nlcykge1xuICAgIGxldCBkZWxpdmVyeUFkZHJlc3NEZXRhaWxzQ29udGVudCA9ICcnO1xuICAgIGxldCBpbnZvaWNlQWRkcmVzc0RldGFpbHNDb250ZW50ID0gJyc7XG5cbiAgICBjb25zdCAkZGVsaXZlcnlBZGRyZXNzRGV0YWlscyA9ICQoY3JlYXRlT3JkZXJQYWdlTWFwLmRlbGl2ZXJ5QWRkcmVzc0RldGFpbHMpO1xuICAgIGNvbnN0ICRpbnZvaWNlQWRkcmVzc0RldGFpbHMgPSAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5pbnZvaWNlQWRkcmVzc0RldGFpbHMpO1xuICAgIGNvbnN0ICRkZWxpdmVyeUFkZHJlc3NTZWxlY3QgPSAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5kZWxpdmVyeUFkZHJlc3NTZWxlY3QpO1xuICAgIGNvbnN0ICRpbnZvaWNlQWRkcmVzc1NlbGVjdCA9ICQoY3JlYXRlT3JkZXJQYWdlTWFwLmludm9pY2VBZGRyZXNzU2VsZWN0KTtcblxuICAgIGNvbnN0ICRhZGRyZXNzZXNDb250ZW50ID0gJChjcmVhdGVPcmRlclBhZ2VNYXAuYWRkcmVzc2VzQ29udGVudCk7XG4gICAgY29uc3QgJGFkZHJlc3Nlc1dhcm5pbmdDb250ZW50ID0gJChjcmVhdGVPcmRlclBhZ2VNYXAuYWRkcmVzc2VzV2FybmluZyk7XG5cbiAgICAkZGVsaXZlcnlBZGRyZXNzRGV0YWlscy5lbXB0eSgpO1xuICAgICRpbnZvaWNlQWRkcmVzc0RldGFpbHMuZW1wdHkoKTtcbiAgICAkZGVsaXZlcnlBZGRyZXNzU2VsZWN0LmVtcHR5KCk7XG4gICAgJGludm9pY2VBZGRyZXNzU2VsZWN0LmVtcHR5KCk7XG5cbiAgICBpZiAoYWRkcmVzc2VzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgJGFkZHJlc3Nlc1dhcm5pbmdDb250ZW50LnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAgICRhZGRyZXNzZXNDb250ZW50LmFkZENsYXNzKCdkLW5vbmUnKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgICRhZGRyZXNzZXNDb250ZW50LnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAkYWRkcmVzc2VzV2FybmluZ0NvbnRlbnQuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gT2JqZWN0LmtleXMoYWRkcmVzc2VzKSkge1xuICAgICAgY29uc3QgYWRkcmVzcyA9IGFkZHJlc3Nlc1trZXldO1xuXG4gICAgICBjb25zdCBkZWxpdmVyeUFkZHJlc3NPcHRpb24gPSB7XG4gICAgICAgIHZhbHVlOiBhZGRyZXNzLmFkZHJlc3NJZCxcbiAgICAgICAgdGV4dDogYWRkcmVzcy5hbGlhcyxcbiAgICAgIH07XG5cbiAgICAgIGNvbnN0IGludm9pY2VBZGRyZXNzT3B0aW9uID0ge1xuICAgICAgICB2YWx1ZTogYWRkcmVzcy5hZGRyZXNzSWQsXG4gICAgICAgIHRleHQ6IGFkZHJlc3MuYWxpYXMsXG4gICAgICB9O1xuXG4gICAgICBpZiAoYWRkcmVzcy5kZWxpdmVyeSkge1xuICAgICAgICBkZWxpdmVyeUFkZHJlc3NEZXRhaWxzQ29udGVudCA9IGFkZHJlc3MuZm9ybWF0dGVkQWRkcmVzcztcbiAgICAgICAgZGVsaXZlcnlBZGRyZXNzT3B0aW9uLnNlbGVjdGVkID0gJ3NlbGVjdGVkJztcbiAgICAgIH1cblxuICAgICAgaWYgKGFkZHJlc3MuaW52b2ljZSkge1xuICAgICAgICBpbnZvaWNlQWRkcmVzc0RldGFpbHNDb250ZW50ID0gYWRkcmVzcy5mb3JtYXR0ZWRBZGRyZXNzO1xuICAgICAgICBpbnZvaWNlQWRkcmVzc09wdGlvbi5zZWxlY3RlZCA9ICdzZWxlY3RlZCc7XG4gICAgICB9XG5cbiAgICAgICRkZWxpdmVyeUFkZHJlc3NTZWxlY3QuYXBwZW5kKCQoJzxvcHRpb24+JywgZGVsaXZlcnlBZGRyZXNzT3B0aW9uKSk7XG4gICAgICAkaW52b2ljZUFkZHJlc3NTZWxlY3QuYXBwZW5kKCQoJzxvcHRpb24+JywgaW52b2ljZUFkZHJlc3NPcHRpb24pKTtcbiAgICB9XG5cbiAgICBpZiAoZGVsaXZlcnlBZGRyZXNzRGV0YWlsc0NvbnRlbnQpIHtcbiAgICAgICRkZWxpdmVyeUFkZHJlc3NEZXRhaWxzLmh0bWwoZGVsaXZlcnlBZGRyZXNzRGV0YWlsc0NvbnRlbnQpO1xuICAgIH1cblxuICAgIGlmIChpbnZvaWNlQWRkcmVzc0RldGFpbHNDb250ZW50KSB7XG4gICAgICAkaW52b2ljZUFkZHJlc3NEZXRhaWxzLmh0bWwoaW52b2ljZUFkZHJlc3NEZXRhaWxzQ29udGVudCk7XG4gICAgfVxuXG4gICAgdGhpcy5fc2hvd0FkZHJlc3Nlc0Jsb2NrKCk7XG4gIH1cblxuICAvKipcbiAgICogU2hvd3MgYWRkcmVzc2VzIGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd0FkZHJlc3Nlc0Jsb2NrKCkge1xuICAgICQoY3JlYXRlT3JkZXJQYWdlTWFwLmFkZHJlc3Nlc0Jsb2NrKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9hZGRyZXNzZXMtcmVuZGVyZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgY3JlYXRlT3JkZXJQYWdlTWFwIGZyb20gJy4vY3JlYXRlLW9yZGVyLW1hcCc7XG5pbXBvcnQgUm91dGVyIGZyb20gJy4uLy4uLy4uL2NvbXBvbmVudHMvcm91dGVyJztcbmltcG9ydCB7RXZlbnRFbWl0dGVyfSBmcm9tICcuLi8uLi8uLi9jb21wb25lbnRzL2V2ZW50LWVtaXR0ZXInO1xuaW1wb3J0IGV2ZW50TWFwIGZyb20gJy4vZXZlbnQtbWFwJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFByb3ZpZGVzIGFqYXggY2FsbHMgZm9yIGdldHRpbmcgY2FydCBpbmZvcm1hdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDYXJ0UHJvdmlkZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5vcmRlckNyZWF0aW9uQ29udGFpbmVyKTtcbiAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXRzIGNhcnQgaW5mb3JtYXRpb25cbiAgICpcbiAgICogQHBhcmFtIGNhcnRJZFxuICAgKlxuICAgKiBAcmV0dXJucyB7anFYSFJ9LiBPYmplY3Qgd2l0aCBjYXJ0IGluZm9ybWF0aW9uIGluIHJlc3BvbnNlLlxuICAgKi9cbiAgZ2V0Q2FydChjYXJ0SWQpIHtcbiAgICAkLmdldCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY2FydHNfaW5mbycsIHtjYXJ0SWR9KSkudGhlbigoY2FydEluZm8pID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRMb2FkZWQsIGNhcnRJbmZvKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXRzIGV4aXN0aW5nIGVtcHR5IGNhcnQgb3IgY3JlYXRlcyBuZXcgZW1wdHkgY2FydCBmb3IgY3VzdG9tZXIuXG4gICAqXG4gICAqIEBwYXJhbSBjdXN0b21lcklkXG4gICAqXG4gICAqIEByZXR1cm5zIHtqcVhIUn0uIE9iamVjdCB3aXRoIGNhcnQgaW5mb3JtYXRpb24gaW4gcmVzcG9uc2VcbiAgICovXG4gIGxvYWRFbXB0eUNhcnQoY3VzdG9tZXJJZCkge1xuICAgICQucG9zdCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY2FydHNfY3JlYXRlJyksIHtcbiAgICAgIGN1c3RvbWVyX2lkOiBjdXN0b21lcklkLFxuICAgIH0pLnRoZW4oKGNhcnRJbmZvKSA9PiB7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5jYXJ0TG9hZGVkLCBjYXJ0SW5mbyk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogRHVwbGljYXRlcyBjYXJ0IGZyb20gcHJvdmlkZWQgb3JkZXJcbiAgICpcbiAgICogQHBhcmFtIG9yZGVySWRcbiAgICpcbiAgICogQHJldHVybnMge2pxWEhSfS4gT2JqZWN0IHdpdGggY2FydCBpbmZvcm1hdGlvbiBpbiByZXNwb25zZVxuICAgKi9cbiAgZHVwbGljYXRlT3JkZXJDYXJ0KG9yZGVySWQpIHtcbiAgICAkLnBvc3QodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19kdXBsaWNhdGVfY2FydCcsIHtvcmRlcklkfSkpLnRoZW4oKGNhcnRJbmZvKSA9PiB7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5jYXJ0TG9hZGVkLCBjYXJ0SW5mbyk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LXByb3ZpZGVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IENhcnRFZGl0b3IgZnJvbSAnLi9jYXJ0LWVkaXRvcic7XG5pbXBvcnQgQ2FydFJ1bGVzUmVuZGVyZXIgZnJvbSAnLi9jYXJ0LXJ1bGVzLXJlbmRlcmVyJztcbmltcG9ydCBjcmVhdGVPcmRlck1hcCBmcm9tICcuL2NyZWF0ZS1vcmRlci1tYXAnO1xuaW1wb3J0IHtFdmVudEVtaXR0ZXJ9IGZyb20gJy4uLy4uLy4uL2NvbXBvbmVudHMvZXZlbnQtZW1pdHRlcic7XG5pbXBvcnQgZXZlbnRNYXAgZnJvbSAnLi9ldmVudC1tYXAnO1xuaW1wb3J0IFJvdXRlciBmcm9tICcuLi8uLi8uLi9jb21wb25lbnRzL3JvdXRlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBSZXNwb25zaWJsZSBmb3Igc2VhcmNoaW5nIGNhcnQgcnVsZXMgYW5kIG1hbmFnaW5nIGNhcnQgcnVsZXMgc2VhcmNoIGJsb2NrXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENhcnRSdWxlTWFuYWdlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMucm91dGVyID0gbmV3IFJvdXRlcigpO1xuICAgIHRoaXMuJHNlYXJjaElucHV0ID0gJChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZVNlYXJjaElucHV0KTtcbiAgICB0aGlzLmNhcnRSdWxlc1JlbmRlcmVyID0gbmV3IENhcnRSdWxlc1JlbmRlcmVyKCk7XG4gICAgdGhpcy5jYXJ0RWRpdG9yID0gbmV3IENhcnRFZGl0b3IoKTtcblxuICAgIHRoaXMuX2luaXRMaXN0ZW5lcnMoKTtcblxuICAgIHJldHVybiB7XG4gICAgICBzZWFyY2g6ICgpID0+IHRoaXMuX3NlYXJjaCgpLFxuICAgICAgc3RvcFNlYXJjaGluZzogKCkgPT4gdGhpcy5jYXJ0UnVsZXNSZW5kZXJlci5oaWRlUmVzdWx0c0Ryb3Bkb3duKCksXG4gICAgICBhZGRDYXJ0UnVsZVRvQ2FydDogKGNhcnRSdWxlSWQsIGNhcnRJZCkgPT4gdGhpcy5jYXJ0RWRpdG9yLmFkZENhcnRSdWxlVG9DYXJ0KGNhcnRSdWxlSWQsIGNhcnRJZCksXG4gICAgICByZW1vdmVDYXJ0UnVsZUZyb21DYXJ0OiAoY2FydFJ1bGVJZCwgY2FydElkKSA9PiB0aGlzLmNhcnRFZGl0b3IucmVtb3ZlQ2FydFJ1bGVGcm9tQ2FydChjYXJ0UnVsZUlkLCBjYXJ0SWQpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhdGVzIGV2ZW50IGxpc3RlbmVycyBmb3IgY2FydCBydWxlIGFjdGlvbnNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0TGlzdGVuZXJzKCkge1xuICAgIHRoaXMuX29uQ2FydFJ1bGVTZWFyY2goKTtcbiAgICB0aGlzLl9vbkFkZENhcnRSdWxlVG9DYXJ0KCk7XG4gICAgdGhpcy5fb25BZGRDYXJ0UnVsZVRvQ2FydEZhaWx1cmUoKTtcbiAgICB0aGlzLl9vblJlbW92ZUNhcnRSdWxlRnJvbUNhcnQoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGZvciBjYXJ0IHJ1bGUgc2VhcmNoIGFjdGlvblxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uQ2FydFJ1bGVTZWFyY2goKSB7XG4gICAgRXZlbnRFbWl0dGVyLm9uKGV2ZW50TWFwLmNhcnRSdWxlU2VhcmNoZWQsIChjYXJ0UnVsZXMpID0+IHtcbiAgICAgIHRoaXMuY2FydFJ1bGVzUmVuZGVyZXIucmVuZGVyU2VhcmNoUmVzdWx0cyhjYXJ0UnVsZXMpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZXZlbnQgb2YgYWRkIGNhcnQgcnVsZSB0byBjYXJ0IGFjdGlvblxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uQWRkQ2FydFJ1bGVUb0NhcnQoKSB7XG4gICAgRXZlbnRFbWl0dGVyLm9uKGV2ZW50TWFwLmNhcnRSdWxlQWRkZWQsIChjYXJ0SW5mbykgPT4ge1xuICAgICAgdGhpcy5jYXJ0UnVsZXNSZW5kZXJlci5yZW5kZXJDYXJ0UnVsZXNCbG9jayhjYXJ0SW5mby5jYXJ0UnVsZXMsIGNhcnRJbmZvLnByb2R1Y3RzLmxlbmd0aCA9PT0gMCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBldmVudCB3aGVuIGFkZCBjYXJ0IHJ1bGUgdG8gY2FydCBmYWlsc1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uQWRkQ2FydFJ1bGVUb0NhcnRGYWlsdXJlKCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5jYXJ0UnVsZUZhaWxlZFRvQWRkLCAobWVzc2FnZSkgPT4ge1xuICAgICAgdGhpcy5jYXJ0UnVsZXNSZW5kZXJlci5kaXNwbGF5RXJyb3JNZXNzYWdlKG1lc3NhZ2UpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZXZlbnQgZm9yIHJlbW92ZSBjYXJ0IHJ1bGUgZnJvbSBjYXJ0IGFjdGlvblxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uUmVtb3ZlQ2FydFJ1bGVGcm9tQ2FydCgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAuY2FydFJ1bGVSZW1vdmVkLCAoY2FydEluZm8pID0+IHtcbiAgICAgIHRoaXMuY2FydFJ1bGVzUmVuZGVyZXIucmVuZGVyQ2FydFJ1bGVzQmxvY2soY2FydEluZm8uY2FydFJ1bGVzLCBjYXJ0SW5mby5wcm9kdWN0cy5sZW5ndGggPT09IDApO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFNlYXJjaGVzIGZvciBjYXJ0IHJ1bGVzIGJ5IHNlYXJjaCBwaHJhc2VcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zZWFyY2goc2VhcmNoUGhyYXNlKSB7XG4gICAgaWYgKHNlYXJjaFBocmFzZS5sZW5ndGggPCAzKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgJC5nZXQodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX2NhcnRfcnVsZXNfc2VhcmNoJyksIHtcbiAgICAgIHNlYXJjaF9waHJhc2U6IHNlYXJjaFBocmFzZSxcbiAgICB9KS50aGVuKChjYXJ0UnVsZXMpID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRSdWxlU2VhcmNoZWQsIGNhcnRSdWxlcyk7XG4gICAgfSkuY2F0Y2goKGUpID0+IHtcbiAgICAgIHNob3dFcnJvck1lc3NhZ2UoZS5yZXNwb25zZUpTT04ubWVzc2FnZSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LXJ1bGUtbWFuYWdlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBjcmVhdGVPcmRlck1hcCBmcm9tICcuL2NyZWF0ZS1vcmRlci1tYXAnO1xuaW1wb3J0IEN1c3RvbWVyUmVuZGVyZXIgZnJvbSAnLi9jdXN0b21lci1yZW5kZXJlcic7XG5pbXBvcnQge0V2ZW50RW1pdHRlcn0gZnJvbSAnLi4vLi4vLi4vY29tcG9uZW50cy9ldmVudC1lbWl0dGVyJztcbmltcG9ydCBldmVudE1hcCBmcm9tICcuL2V2ZW50LW1hcCc7XG5pbXBvcnQgUm91dGVyIGZyb20gJy4uLy4uLy4uL2NvbXBvbmVudHMvcm91dGVyJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFJlc3BvbnNpYmxlIGZvciBjdXN0b21lcnMgbWFuYWdpbmcuIChzZWFyY2gsIHNlbGVjdCwgZ2V0IGN1c3RvbWVyIGluZm8gZXRjLilcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ3VzdG9tZXJNYW5hZ2VyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5jdXN0b21lcklkID0gbnVsbDtcbiAgICB0aGlzLmFjdGl2ZVNlYXJjaFJlcXVlc3QgPSBudWxsO1xuXG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJChjcmVhdGVPcmRlck1hcC5jdXN0b21lclNlYXJjaEJsb2NrKTtcbiAgICB0aGlzLiRzZWFyY2hJbnB1dCA9ICQoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJTZWFyY2hJbnB1dCk7XG4gICAgdGhpcy4kY3VzdG9tZXJTZWFyY2hSZXN1bHRCbG9jayA9ICQoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJTZWFyY2hSZXN1bHRzQmxvY2spO1xuICAgIHRoaXMuY3VzdG9tZXJSZW5kZXJlciA9IG5ldyBDdXN0b21lclJlbmRlcmVyKCk7XG5cbiAgICB0aGlzLl9pbml0TGlzdGVuZXJzKCk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgc2VhcmNoOiBzZWFyY2hQaHJhc2UgPT4gdGhpcy5fc2VhcmNoKHNlYXJjaFBocmFzZSksXG4gICAgICBzZWxlY3RDdXN0b21lcjogZXZlbnQgPT4gdGhpcy5fc2VsZWN0Q3VzdG9tZXIoZXZlbnQpLFxuICAgICAgbG9hZEN1c3RvbWVyQ2FydHM6IGN1cnJlbnRDYXJ0SWQgPT4gdGhpcy5fbG9hZEN1c3RvbWVyQ2FydHMoY3VycmVudENhcnRJZCksXG4gICAgICBsb2FkQ3VzdG9tZXJPcmRlcnM6ICgpID0+IHRoaXMuX2xvYWRDdXN0b21lck9yZGVycygpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhbGl6ZXMgZXZlbnQgbGlzdGVuZXJzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdExpc3RlbmVycygpIHtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgY3JlYXRlT3JkZXJNYXAuY2hhbmdlQ3VzdG9tZXJCdG4sICgpID0+IHRoaXMuX2NoYW5nZUN1c3RvbWVyKCkpO1xuICAgIHRoaXMuX29uQ3VzdG9tZXJTZWFyY2goKTtcbiAgICB0aGlzLl9vbkN1c3RvbWVyU2VsZWN0KCk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBmb3IgY3VzdG9tZXIgc2VhcmNoIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25DdXN0b21lclNlYXJjaCgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAuY3VzdG9tZXJTZWFyY2hlZCwgKHJlc3BvbnNlKSA9PiB7XG4gICAgICB0aGlzLmFjdGl2ZVNlYXJjaFJlcXVlc3QgPSBudWxsO1xuICAgICAgdGhpcy5jdXN0b21lclJlbmRlcmVyLnJlbmRlclNlYXJjaFJlc3VsdHMocmVzcG9uc2UuY3VzdG9tZXJzKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGZvciBjdXN0b21lciBzZWxlY3QgZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vbkN1c3RvbWVyU2VsZWN0KCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5jdXN0b21lclNlbGVjdGVkLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRjaG9vc2VCdG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgdGhpcy5jdXN0b21lcklkID0gJGNob29zZUJ0bi5kYXRhKCdjdXN0b21lci1pZCcpO1xuXG4gICAgICB0aGlzLmN1c3RvbWVyUmVuZGVyZXIuZGlzcGxheVNlbGVjdGVkQ3VzdG9tZXJCbG9jaygkY2hvb3NlQnRuKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIHVzZSBjYXNlIHdoZW4gY3VzdG9tZXIgaXMgY2hhbmdlZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NoYW5nZUN1c3RvbWVyKCkge1xuICAgIHRoaXMuY3VzdG9tZXJSZW5kZXJlci5zaG93Q3VzdG9tZXJTZWFyY2goKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMb2FkcyBjdXN0b21lciBjYXJ0cyBsaXN0XG4gICAqXG4gICAqIEBwYXJhbSBjdXJyZW50Q2FydElkXG4gICAqL1xuICBfbG9hZEN1c3RvbWVyQ2FydHMoY3VycmVudENhcnRJZCkge1xuICAgIGNvbnN0IGN1c3RvbWVySWQgPSB0aGlzLmN1c3RvbWVySWQ7XG5cbiAgICAkLmdldCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY3VzdG9tZXJzX2NhcnRzJywge2N1c3RvbWVySWR9KSkudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgIHRoaXMuY3VzdG9tZXJSZW5kZXJlci5yZW5kZXJDYXJ0cyhyZXNwb25zZS5jYXJ0cywgY3VycmVudENhcnRJZCk7XG4gICAgfSkuY2F0Y2goKGUpID0+IHtcbiAgICAgIHNob3dFcnJvck1lc3NhZ2UoZS5yZXNwb25zZUpTT04ubWVzc2FnZSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTG9hZHMgY3VzdG9tZXIgb3JkZXJzIGxpc3RcbiAgICovXG4gIF9sb2FkQ3VzdG9tZXJPcmRlcnMoKSB7XG4gICAgY29uc3QgY3VzdG9tZXJJZCA9IHRoaXMuY3VzdG9tZXJJZDtcblxuICAgICQuZ2V0KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9jdXN0b21lcnNfb3JkZXJzJywge2N1c3RvbWVySWR9KSkudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgIHRoaXMuY3VzdG9tZXJSZW5kZXJlci5yZW5kZXJPcmRlcnMocmVzcG9uc2Uub3JkZXJzKTtcbiAgICB9KS5jYXRjaCgoZSkgPT4ge1xuICAgICAgc2hvd0Vycm9yTWVzc2FnZShlLnJlc3BvbnNlSlNPTi5tZXNzYWdlKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0ge0V2ZW50fSBjaG9vc2VDdXN0b21lckV2ZW50XG4gICAqXG4gICAqIEByZXR1cm4ge051bWJlcn1cbiAgICovXG4gIF9zZWxlY3RDdXN0b21lcihjaG9vc2VDdXN0b21lckV2ZW50KSB7XG4gICAgRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAuY3VzdG9tZXJTZWxlY3RlZCwgY2hvb3NlQ3VzdG9tZXJFdmVudCk7XG5cbiAgICByZXR1cm4gdGhpcy5jdXN0b21lcklkO1xuICB9XG5cbiAgLyoqXG4gICAqIFNlYXJjaGVzIGZvciBjdXN0b21lcnNcbiAgICogQHRvZG86IGZpeCBzaG93aW5nIG5vdCBmb3VuZCBjdXN0b21lcnMgYW5kIHJlcmVuZGVyIGFmdGVyIGNoYW5nZSBjdXN0b21lclxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3NlYXJjaChzZWFyY2hQaHJhc2UpIHtcbiAgICBpZiAoc2VhcmNoUGhyYXNlLmxlbmd0aCA8IDMpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBpZiAodGhpcy5hY3RpdmVTZWFyY2hSZXF1ZXN0ICE9PSBudWxsKSB7XG4gICAgICB0aGlzLmFjdGl2ZVNlYXJjaFJlcXVlc3QuYWJvcnQoKTtcbiAgICB9XG5cbiAgICBjb25zdCAkc2VhcmNoUmVxdWVzdCA9ICQuZ2V0KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9jdXN0b21lcnNfc2VhcmNoJyksIHtcbiAgICAgIGN1c3RvbWVyX3NlYXJjaDogc2VhcmNoUGhyYXNlLFxuICAgIH0pO1xuICAgIHRoaXMuYWN0aXZlU2VhcmNoUmVxdWVzdCA9ICRzZWFyY2hSZXF1ZXN0O1xuXG4gICAgJHNlYXJjaFJlcXVlc3QudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmN1c3RvbWVyU2VhcmNoZWQsIHJlc3BvbnNlKTtcbiAgICB9KS5jYXRjaCgocmVzcG9uc2UpID0+IHtcbiAgICAgIGlmIChyZXNwb25zZS5zdGF0dXNUZXh0ID09PSAnYWJvcnQnKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgc2hvd0Vycm9yTWVzc2FnZShyZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL2N1c3RvbWVyLW1hbmFnZXIuanMiLCJpbXBvcnQgY3JlYXRlT3JkZXJNYXAgZnJvbSAnLi9jcmVhdGUtb3JkZXItbWFwJztcblxuLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IFJvdXRlciBmcm9tICcuLi8uLi8uLi9jb21wb25lbnRzL3JvdXRlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBSZXNwb25zaWJsZSBmb3IgY3VzdG9tZXIgaW5mb3JtYXRpb24gcmVuZGVyaW5nXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEN1c3RvbWVyUmVuZGVyZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKGNyZWF0ZU9yZGVyTWFwLmN1c3RvbWVyU2VhcmNoQmxvY2spO1xuICAgIHRoaXMuJGN1c3RvbWVyU2VhcmNoUmVzdWx0QmxvY2sgPSAkKGNyZWF0ZU9yZGVyTWFwLmN1c3RvbWVyU2VhcmNoUmVzdWx0c0Jsb2NrKTtcbiAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIGN1c3RvbWVyIHNlYXJjaCByZXN1bHRzXG4gICAqXG4gICAqIEBwYXJhbSBmb3VuZEN1c3RvbWVyc1xuICAgKi9cbiAgcmVuZGVyU2VhcmNoUmVzdWx0cyhmb3VuZEN1c3RvbWVycykge1xuICAgIHRoaXMuX2NsZWFyU2hvd25DdXN0b21lcnMoKTtcblxuICAgIGlmIChmb3VuZEN1c3RvbWVycy5sZW5ndGggPT09IDApIHtcbiAgICAgIHRoaXMuX3Nob3dOb3RGb3VuZEN1c3RvbWVycygpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgZm9yIChjb25zdCBjdXN0b21lcklkIGluIGZvdW5kQ3VzdG9tZXJzKSB7XG4gICAgICBjb25zdCBjdXN0b21lclJlc3VsdCA9IGZvdW5kQ3VzdG9tZXJzW2N1c3RvbWVySWRdO1xuICAgICAgY29uc3QgY3VzdG9tZXIgPSB7XG4gICAgICAgIGlkOiBjdXN0b21lcklkLFxuICAgICAgICBmaXJzdE5hbWU6IGN1c3RvbWVyUmVzdWx0LmZpcnN0bmFtZSxcbiAgICAgICAgbGFzdE5hbWU6IGN1c3RvbWVyUmVzdWx0Lmxhc3RuYW1lLFxuICAgICAgICBlbWFpbDogY3VzdG9tZXJSZXN1bHQuZW1haWwsXG4gICAgICAgIGJpcnRoZGF5OiBjdXN0b21lclJlc3VsdC5iaXJ0aGRheSAhPT0gJzAwMDAtMDAtMDAnID8gY3VzdG9tZXJSZXN1bHQuYmlydGhkYXkgOiAnICcsXG4gICAgICB9O1xuXG4gICAgICB0aGlzLl9yZW5kZXJGb3VuZEN1c3RvbWVyKGN1c3RvbWVyKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogUmVzcG9uc2libGUgZm9yIGRpc3BsYXlpbmcgY3VzdG9tZXIgYmxvY2sgYWZ0ZXIgY3VzdG9tZXIgc2VsZWN0XG4gICAqXG4gICAqIEBwYXJhbSAkdGFyZ2V0ZWRCdG5cbiAgICovXG4gIGRpc3BsYXlTZWxlY3RlZEN1c3RvbWVyQmxvY2soJHRhcmdldGVkQnRuKSB7XG4gICAgJHRhcmdldGVkQnRuLmFkZENsYXNzKCdkLW5vbmUnKTtcblxuICAgIGNvbnN0ICRjdXN0b21lckNhcmQgPSAkdGFyZ2V0ZWRCdG4uY2xvc2VzdCgnLmNhcmQnKTtcblxuICAgICRjdXN0b21lckNhcmQuYWRkQ2xhc3MoJ2JvcmRlci1zdWNjZXNzJyk7XG4gICAgJGN1c3RvbWVyQ2FyZC5maW5kKGNyZWF0ZU9yZGVyTWFwLmNoYW5nZUN1c3RvbWVyQnRuKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG5cbiAgICB0aGlzLiRjb250YWluZXIuZmluZChjcmVhdGVPcmRlck1hcC5jdXN0b21lclNlYXJjaFJvdykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgIHRoaXMuJGNvbnRhaW5lci5maW5kKGNyZWF0ZU9yZGVyTWFwLm5vdFNlbGVjdGVkQ3VzdG9tZXJTZWFyY2hSZXN1bHRzKVxuICAgICAgLmNsb3Nlc3QoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJTZWFyY2hSZXN1bHRDb2x1bW4pXG4gICAgICAucmVtb3ZlKClcbiAgICA7XG4gIH1cblxuICAvKipcbiAgICogU2hvd3MgY3VzdG9tZXIgc2VhcmNoIGJsb2NrXG4gICAqL1xuICBzaG93Q3VzdG9tZXJTZWFyY2goKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLmZpbmQoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJTZWFyY2hSb3cpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIGN1c3RvbWVyIGNhcnRzIGxpc3RcbiAgICpcbiAgICogQHBhcmFtIHtBcnJheX0gY2FydHNcbiAgICogQHBhcmFtIHtJbnR9IGN1cnJlbnRDYXJ0SWRcbiAgICovXG4gIHJlbmRlckNhcnRzKGNhcnRzLCBjdXJyZW50Q2FydElkKSB7XG4gICAgY29uc3QgJGNhcnRzVGFibGUgPSAkKGNyZWF0ZU9yZGVyTWFwLmN1c3RvbWVyQ2FydHNUYWJsZSk7XG4gICAgY29uc3QgJGNhcnRzVGFibGVSb3dUZW1wbGF0ZSA9ICQoJChjcmVhdGVPcmRlck1hcC5jdXN0b21lckNhcnRzVGFibGVSb3dUZW1wbGF0ZSkuaHRtbCgpKTtcblxuICAgICRjYXJ0c1RhYmxlLmZpbmQoJ3Rib2R5JykuZW1wdHkoKTtcblxuICAgIGlmIChjYXJ0cy5sZW5ndGggPT09IDApIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB0aGlzLl9zaG93Q2hlY2tvdXRIaXN0b3J5QmxvY2soKTtcblxuICAgIGZvciAoY29uc3Qga2V5IGluIGNhcnRzKSB7XG4gICAgICBjb25zdCBjYXJ0ID0gY2FydHNba2V5XTtcbiAgICAgIC8vIGRvIG5vdCByZW5kZXIgY3VycmVudCBjYXJ0XG4gICAgICBpZiAoY2FydC5jYXJ0SWQgPT09IGN1cnJlbnRDYXJ0SWQpIHtcbiAgICAgICAgY29udGludWU7XG4gICAgICB9XG4gICAgICBjb25zdCAkdGVtcGxhdGUgPSAkY2FydHNUYWJsZVJvd1RlbXBsYXRlLmNsb25lKCk7XG5cbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLmNhcnRJZEZpZWxkKS50ZXh0KGNhcnQuY2FydElkKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLmNhcnREYXRlRmllbGQpLnRleHQoY2FydC5jcmVhdGlvbkRhdGUpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAuY2FydFRvdGFsRmllbGQpLnRleHQoY2FydC50b3RhbFByaWNlKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLmNhcnREZXRhaWxzQnRuKS5wcm9wKFxuICAgICAgICAnaHJlZicsXG4gICAgICAgIHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9jYXJ0c192aWV3Jywge2NhcnRJZDogY2FydC5jYXJ0SWR9KVxuICAgICAgKTtcblxuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAudXNlQ2FydEJ0bikuZGF0YSgnY2FydC1pZCcsIGNhcnQuY2FydElkKTtcblxuICAgICAgJGNhcnRzVGFibGUuZmluZCgndGJvZHknKS5hcHBlbmQoJHRlbXBsYXRlKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBjdXN0b21lciBvcmRlcnMgbGlzdFxuICAgKlxuICAgKiBAcGFyYW0ge0FycmF5fSBvcmRlcnNcbiAgICovXG4gIHJlbmRlck9yZGVycyhvcmRlcnMpIHtcbiAgICBjb25zdCAkb3JkZXJzVGFibGUgPSAkKGNyZWF0ZU9yZGVyTWFwLmN1c3RvbWVyT3JkZXJzVGFibGUpO1xuICAgIGNvbnN0ICRyb3dUZW1wbGF0ZSA9ICQoJChjcmVhdGVPcmRlck1hcC5jdXN0b21lck9yZGVyc1RhYmxlUm93VGVtcGxhdGUpLmh0bWwoKSk7XG5cbiAgICAkb3JkZXJzVGFibGUuZmluZCgndGJvZHknKS5lbXB0eSgpO1xuXG4gICAgaWYgKG9yZGVycy5sZW5ndGggPT09IDApIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB0aGlzLl9zaG93Q2hlY2tvdXRIaXN0b3J5QmxvY2soKTtcblxuICAgIGZvciAoY29uc3Qga2V5IGluIE9iamVjdC5rZXlzKG9yZGVycykpIHtcbiAgICAgIGNvbnN0IG9yZGVyID0gb3JkZXJzW2tleV07XG4gICAgICBjb25zdCAkdGVtcGxhdGUgPSAkcm93VGVtcGxhdGUuY2xvbmUoKTtcblxuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAub3JkZXJJZEZpZWxkKS50ZXh0KG9yZGVyLm9yZGVySWQpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAub3JkZXJEYXRlRmllbGQpLnRleHQob3JkZXIub3JkZXJQbGFjZWREYXRlKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLm9yZGVyUHJvZHVjdHNGaWVsZCkudGV4dChvcmRlci50b3RhbFByb2R1Y3RzQ291bnQpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAub3JkZXJUb3RhbEZpZWxkKS50ZXh0KG9yZGVyLnRvdGFsUGFpZCk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5vcmRlclN0YXR1c0ZpZWxkKS50ZXh0KG9yZGVyLm9yZGVyU3RhdHVzKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLm9yZGVyRGV0YWlsc0J0bikucHJvcChcbiAgICAgICAgJ2hyZWYnLFxuICAgICAgICB0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fb3JkZXJzX3ZpZXcnLCB7b3JkZXJJZDogb3JkZXIub3JkZXJJZH0pXG4gICAgICApO1xuXG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC51c2VPcmRlckJ0bikuZGF0YSgnb3JkZXItaWQnLCBvcmRlci5vcmRlcklkKTtcblxuICAgICAgJG9yZGVyc1RhYmxlLmZpbmQoJ3Rib2R5JykuYXBwZW5kKCR0ZW1wbGF0ZSk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIFJlbmRlcnMgY3VzdG9tZXIgaW5mb3JtYXRpb24gYWZ0ZXIgc2VhcmNoIGFjdGlvblxuICAgKlxuICAgKiBAcGFyYW0ge09iamVjdH0gY3VzdG9tZXJcbiAgICpcbiAgICogQHJldHVybiB7alF1ZXJ5fVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbmRlckZvdW5kQ3VzdG9tZXIoY3VzdG9tZXIpIHtcbiAgICBjb25zdCAkY3VzdG9tZXJTZWFyY2hSZXN1bHRUZW1wbGF0ZSA9ICQoJChjcmVhdGVPcmRlck1hcC5jdXN0b21lclNlYXJjaFJlc3VsdFRlbXBsYXRlKS5odG1sKCkpO1xuICAgIGNvbnN0ICR0ZW1wbGF0ZSA9ICRjdXN0b21lclNlYXJjaFJlc3VsdFRlbXBsYXRlLmNsb25lKCk7XG5cbiAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5jdXN0b21lclNlYXJjaFJlc3VsdE5hbWUpLnRleHQoYCR7Y3VzdG9tZXIuZmlyc3ROYW1lfSAke2N1c3RvbWVyLmxhc3ROYW1lfWApO1xuICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLmN1c3RvbWVyU2VhcmNoUmVzdWx0RW1haWwpLnRleHQoY3VzdG9tZXIuZW1haWwpO1xuICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLmN1c3RvbWVyU2VhcmNoUmVzdWx0SWQpLnRleHQoY3VzdG9tZXIuaWQpO1xuICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLmN1c3RvbWVyU2VhcmNoUmVzdWx0QmlydGhkYXkpLnRleHQoY3VzdG9tZXIuYmlydGhkYXkpO1xuICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLmNob29zZUN1c3RvbWVyQnRuKS5kYXRhKCdjdXN0b21lci1pZCcsIGN1c3RvbWVyLmlkKTtcbiAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5jdXN0b21lckRldGFpbHNCdG4pLnByb3AoXG4gICAgICAnaHJlZicsXG4gICAgICB0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY3VzdG9tZXJzX3ZpZXcnLCB7Y3VzdG9tZXJJZDogY3VzdG9tZXIuaWR9KVxuICAgICk7XG5cbiAgICByZXR1cm4gdGhpcy4kY3VzdG9tZXJTZWFyY2hSZXN1bHRCbG9jay5hcHBlbmQoJHRlbXBsYXRlKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyBjaGVja291dCBoaXN0b3J5IGJsb2NrIHdoZXJlIGNhcnRzIGFuZCBvcmRlcnMgYXJlIHJlbmRlcmVkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd0NoZWNrb3V0SGlzdG9yeUJsb2NrKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJDaGVja291dEhpc3RvcnkpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDbGVhcnMgc2hvd24gY3VzdG9tZXJzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfY2xlYXJTaG93bkN1c3RvbWVycygpIHtcbiAgICB0aGlzLiRjdXN0b21lclNlYXJjaFJlc3VsdEJsb2NrLmVtcHR5KCk7XG4gIH1cblxuICAvKipcbiAgICogU2hvd3MgZW1wdHkgcmVzdWx0IHdoZW4gY3VzdG9tZXIgaXMgbm90IGZvdW5kXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd05vdEZvdW5kQ3VzdG9tZXJzKCkge1xuICAgIGNvbnN0ICRlbXB0eVJlc3VsdFRlbXBsYXRlID0gJCgkKCcjY3VzdG9tZXJTZWFyY2hFbXB0eVJlc3VsdFRlbXBsYXRlJykuaHRtbCgpKTtcblxuICAgIHRoaXMuJGN1c3RvbWVyU2VhcmNoUmVzdWx0QmxvY2suYXBwZW5kKCRlbXB0eVJlc3VsdFRlbXBsYXRlKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL2N1c3RvbWVyLXJlbmRlcmVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IENhcnRFZGl0b3IgZnJvbSAnLi9jYXJ0LWVkaXRvcic7XG5pbXBvcnQgY3JlYXRlT3JkZXJNYXAgZnJvbSAnLi9jcmVhdGUtb3JkZXItbWFwJztcbmltcG9ydCBldmVudE1hcCBmcm9tICcuL2V2ZW50LW1hcCc7XG5pbXBvcnQge0V2ZW50RW1pdHRlcn0gZnJvbSAnLi4vLi4vLi4vY29tcG9uZW50cy9ldmVudC1lbWl0dGVyJztcbmltcG9ydCBQcm9kdWN0UmVuZGVyZXIgZnJvbSAnLi9wcm9kdWN0LXJlbmRlcmVyJztcbmltcG9ydCBSb3V0ZXIgZnJvbSAnLi4vLi4vLi4vY29tcG9uZW50cy9yb3V0ZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogUHJvZHVjdCBjb21wb25lbnQgT2JqZWN0IGZvciBcIkNyZWF0ZSBvcmRlclwiIHBhZ2VcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUHJvZHVjdE1hbmFnZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLnByb2R1Y3RzID0ge307XG4gICAgdGhpcy5zZWxlY3RlZFByb2R1Y3RJZCA9IG51bGw7XG4gICAgdGhpcy5zZWxlY3RlZENvbWJpbmF0aW9uSWQgPSBudWxsO1xuICAgIHRoaXMuYWN0aXZlU2VhcmNoUmVxdWVzdCA9IG51bGw7XG5cbiAgICB0aGlzLnByb2R1Y3RSZW5kZXJlciA9IG5ldyBQcm9kdWN0UmVuZGVyZXIoKTtcbiAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgICB0aGlzLmNhcnRFZGl0b3IgPSBuZXcgQ2FydEVkaXRvcigpO1xuXG4gICAgdGhpcy5faW5pdExpc3RlbmVycygpO1xuXG4gICAgcmV0dXJuIHtcbiAgICAgIHNlYXJjaDogc2VhcmNoUGhyYXNlID0+IHRoaXMuX3NlYXJjaChzZWFyY2hQaHJhc2UpLFxuICAgICAgYWRkUHJvZHVjdFRvQ2FydDogY2FydElkID0+IHRoaXMuY2FydEVkaXRvci5hZGRQcm9kdWN0KGNhcnRJZCwgdGhpcy5fZ2V0UHJvZHVjdERhdGEoKSksXG4gICAgICByZW1vdmVQcm9kdWN0RnJvbUNhcnQ6IChjYXJ0SWQsIHByb2R1Y3QpID0+IHRoaXMuY2FydEVkaXRvci5yZW1vdmVQcm9kdWN0RnJvbUNhcnQoY2FydElkLCBwcm9kdWN0KSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRpYWxpemVzIGV2ZW50IGxpc3RlbmVyc1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRMaXN0ZW5lcnMoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5wcm9kdWN0U2VsZWN0KS5vbignY2hhbmdlJywgZSA9PiB0aGlzLl9pbml0UHJvZHVjdFNlbGVjdChlKSk7XG4gICAgJChjcmVhdGVPcmRlck1hcC5jb21iaW5hdGlvbnNTZWxlY3QpLm9uKCdjaGFuZ2UnLCBlID0+IHRoaXMuX2luaXRDb21iaW5hdGlvblNlbGVjdChlKSk7XG5cbiAgICB0aGlzLl9vblByb2R1Y3RTZWFyY2goKTtcbiAgICB0aGlzLl9vbkFkZFByb2R1Y3RUb0NhcnQoKTtcbiAgICB0aGlzLl9vblJlbW92ZVByb2R1Y3RGcm9tQ2FydCgpO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZm9yIHByb2R1Y3Qgc2VhcmNoIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25Qcm9kdWN0U2VhcmNoKCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5wcm9kdWN0U2VhcmNoZWQsIChyZXNwb25zZSkgPT4ge1xuICAgICAgdGhpcy5wcm9kdWN0cyA9IEpTT04ucGFyc2UocmVzcG9uc2UpO1xuICAgICAgdGhpcy5wcm9kdWN0UmVuZGVyZXIucmVuZGVyU2VhcmNoUmVzdWx0cyh0aGlzLnByb2R1Y3RzKTtcbiAgICAgIHRoaXMuX3NlbGVjdEZpcnN0UmVzdWx0KCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBmb3IgYWRkIHByb2R1Y3QgdG8gY2FydCBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uQWRkUHJvZHVjdFRvQ2FydCgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAucHJvZHVjdEFkZGVkVG9DYXJ0LCAoY2FydEluZm8pID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRMb2FkZWQsIGNhcnRJbmZvKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGZvciByZW1vdmUgcHJvZHVjdCBmcm9tIGNhcnQgZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vblJlbW92ZVByb2R1Y3RGcm9tQ2FydCgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAucHJvZHVjdFJlbW92ZWRGcm9tQ2FydCwgKGNhcnRJbmZvKSA9PiB7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5jYXJ0TG9hZGVkLCBjYXJ0SW5mbyk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhbGl6ZXMgcHJvZHVjdCBzZWxlY3RcbiAgICpcbiAgICogQHBhcmFtIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdFByb2R1Y3RTZWxlY3QoZXZlbnQpIHtcbiAgICBjb25zdCBwcm9kdWN0SWQgPSBOdW1iZXIoJChldmVudC5jdXJyZW50VGFyZ2V0KS5maW5kKCc6c2VsZWN0ZWQnKS52YWwoKSk7XG4gICAgdGhpcy5fc2VsZWN0UHJvZHVjdChwcm9kdWN0SWQpO1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRpYWxpemVzIGNvbWJpbmF0aW9uIHNlbGVjdFxuICAgKlxuICAgKiBAcGFyYW0gZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0Q29tYmluYXRpb25TZWxlY3QoZXZlbnQpIHtcbiAgICBjb25zdCBjb21iaW5hdGlvbklkID0gTnVtYmVyKCQoZXZlbnQuY3VycmVudFRhcmdldCkuZmluZCgnOnNlbGVjdGVkJykudmFsKCkpO1xuICAgIHRoaXMuX3NlbGVjdENvbWJpbmF0aW9uKGNvbWJpbmF0aW9uSWQpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNlYXJjaGVzIGZvciBwcm9kdWN0XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2VhcmNoKHNlYXJjaFBocmFzZSkge1xuICAgIGlmIChzZWFyY2hQaHJhc2UubGVuZ3RoIDwgMykge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGlmICh0aGlzLmFjdGl2ZVNlYXJjaFJlcXVlc3QgIT09IG51bGwpIHtcbiAgICAgIHRoaXMuYWN0aXZlU2VhcmNoUmVxdWVzdC5hYm9ydCgpO1xuICAgIH1cblxuICAgICQuZ2V0KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9wcm9kdWN0c19zZWFyY2gnKSwge1xuICAgICAgc2VhcmNoX3BocmFzZTogc2VhcmNoUGhyYXNlLFxuICAgIH0pLnRoZW4oKHJlc3BvbnNlKSA9PiB7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5wcm9kdWN0U2VhcmNoZWQsIHJlc3BvbnNlKTtcbiAgICB9KS5jYXRjaCgocmVzcG9uc2UpID0+IHtcbiAgICAgIGlmIChyZXNwb25zZS5zdGF0dXNUZXh0ID09PSAnYWJvcnQnKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgc2hvd0Vycm9yTWVzc2FnZShyZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhdGUgZmlyc3QgcmVzdWx0IGRhdGFzZXQgYWZ0ZXIgc2VhcmNoXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2VsZWN0Rmlyc3RSZXN1bHQoKSB7XG4gICAgdGhpcy5fdW5zZXRQcm9kdWN0KCk7XG5cbiAgICBpZiAodGhpcy5wcm9kdWN0cy5sZW5ndGggIT09IDApIHtcbiAgICAgIHRoaXMuX3NlbGVjdFByb2R1Y3QoT2JqZWN0LmtleXModGhpcy5wcm9kdWN0cylbMF0pO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIHVzZSBjYXNlIHdoZW4gcHJvZHVjdCBpcyBzZWxlY3RlZCBmcm9tIHNlYXJjaCByZXN1bHRzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqXG4gICAqIEBwYXJhbSBwcm9kdWN0SWRcbiAgICovXG4gIF9zZWxlY3RQcm9kdWN0KHByb2R1Y3RJZCkge1xuICAgIHRoaXMuX3Vuc2V0Q29tYmluYXRpb24oKTtcblxuICAgIHRoaXMuc2VsZWN0ZWRQcm9kdWN0SWQgPSBwcm9kdWN0SWQ7XG4gICAgY29uc3QgcHJvZHVjdCA9IHRoaXMucHJvZHVjdHNbcHJvZHVjdElkXTtcblxuICAgIHRoaXMucHJvZHVjdFJlbmRlcmVyLnJlbmRlclByb2R1Y3RNZXRhZGF0YShwcm9kdWN0KTtcblxuICAgIC8vIGlmIHByb2R1Y3QgaGFzIGNvbWJpbmF0aW9ucyBzZWxlY3QgdGhlIGZpcnN0IGVsc2UgbGVhdmUgaXQgbnVsbFxuICAgIGlmIChwcm9kdWN0LmNvbWJpbmF0aW9ucy5sZW5ndGggIT09IDApIHtcbiAgICAgIHRoaXMuX3NlbGVjdENvbWJpbmF0aW9uKE9iamVjdC5rZXlzKHByb2R1Y3QuY29tYmluYXRpb25zKVswXSk7XG4gICAgfVxuXG4gICAgcmV0dXJuIHByb2R1Y3Q7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlcyB1c2UgY2FzZSB3aGVuIG5ldyBjb21iaW5hdGlvbiBpcyBzZWxlY3RlZFxuICAgKlxuICAgKiBAcGFyYW0gY29tYmluYXRpb25JZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3NlbGVjdENvbWJpbmF0aW9uKGNvbWJpbmF0aW9uSWQpIHtcbiAgICBjb25zdCBjb21iaW5hdGlvbiA9IHRoaXMucHJvZHVjdHNbdGhpcy5zZWxlY3RlZFByb2R1Y3RJZF0uY29tYmluYXRpb25zW2NvbWJpbmF0aW9uSWRdO1xuXG4gICAgdGhpcy5zZWxlY3RlZENvbWJpbmF0aW9uSWQgPSBjb21iaW5hdGlvbklkO1xuICAgIHRoaXMucHJvZHVjdFJlbmRlcmVyLnJlbmRlclN0b2NrKGNvbWJpbmF0aW9uLnN0b2NrKTtcblxuICAgIHJldHVybiBjb21iaW5hdGlvbjtcbiAgfVxuXG4gIC8qKlxuICAgKiBTZXRzIHRoZSBzZWxlY3RlZCBjb21iaW5hdGlvbiBpZCB0byBudWxsXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdW5zZXRDb21iaW5hdGlvbigpIHtcbiAgICB0aGlzLnNlbGVjdGVkQ29tYmluYXRpb25JZCA9IG51bGw7XG4gIH1cblxuICAvKipcbiAgICogU2V0cyB0aGUgc2VsZWN0ZWQgcHJvZHVjdCBpZCB0byBudWxsXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdW5zZXRQcm9kdWN0KCkge1xuICAgIHRoaXMuc2VsZWN0ZWRQcm9kdWN0SWQgPSBudWxsO1xuICB9XG5cbiAgLyoqXG4gICAqIFJldHJpZXZlcyBwcm9kdWN0IGRhdGEgZnJvbSBwcm9kdWN0IHNlYXJjaCByZXN1bHQgYmxvY2sgZmllbGRzXG4gICAqXG4gICAqIEByZXR1cm5zIHtGb3JtRGF0YX1cbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRQcm9kdWN0RGF0YSgpIHtcbiAgICBjb25zdCBmb3JtRGF0YSA9IG5ldyBGb3JtRGF0YSgpO1xuXG4gICAgZm9ybURhdGEuYXBwZW5kKCdwcm9kdWN0SWQnLCB0aGlzLnNlbGVjdGVkUHJvZHVjdElkKTtcbiAgICBmb3JtRGF0YS5hcHBlbmQoJ3F1YW50aXR5JywgJChjcmVhdGVPcmRlck1hcC5xdWFudGl0eUlucHV0KS52YWwoKSk7XG4gICAgZm9ybURhdGEuYXBwZW5kKCdjb21iaW5hdGlvbklkJywgdGhpcy5zZWxlY3RlZENvbWJpbmF0aW9uSWQpO1xuXG4gICAgdGhpcy5fZ2V0Q3VzdG9tRmllbGRzRGF0YShmb3JtRGF0YSk7XG5cbiAgICByZXR1cm4gZm9ybURhdGE7XG4gIH1cblxuICAvKipcbiAgICogUmVzb2x2ZXMgcHJvZHVjdCBjdXN0b21pemF0aW9uIGZpZWxkcyB0byBiZSBhZGRlZCB0byBmb3JtRGF0YSBvYmplY3RcbiAgICpcbiAgICogQHBhcmFtIHtGb3JtRGF0YX0gZm9ybURhdGFcbiAgICpcbiAgICogQHJldHVybnMge0Zvcm1EYXRhfVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldEN1c3RvbUZpZWxkc0RhdGEoZm9ybURhdGEpIHtcbiAgICBjb25zdCAkY3VzdG9tRmllbGRzID0gJChjcmVhdGVPcmRlck1hcC5wcm9kdWN0Q3VzdG9tSW5wdXQpO1xuXG4gICAgJGN1c3RvbUZpZWxkcy5lYWNoKChrZXksIGZpZWxkKSA9PiB7XG4gICAgICBjb25zdCAkZmllbGQgPSAkKGZpZWxkKTtcbiAgICAgIGNvbnN0IG5hbWUgPSAkZmllbGQuYXR0cignbmFtZScpO1xuXG4gICAgICBpZiAoJGZpZWxkLmF0dHIoJ3R5cGUnKSA9PT0gJ2ZpbGUnKSB7XG4gICAgICAgIGZvcm1EYXRhLmFwcGVuZChuYW1lLCAkZmllbGRbMF0uZmlsZXNbMF0pO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgZm9ybURhdGEuYXBwZW5kKG5hbWUsICRmaWVsZC52YWwoKSk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICByZXR1cm4gZm9ybURhdGE7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9wcm9kdWN0LW1hbmFnZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgY3JlYXRlT3JkZXJQYWdlTWFwIGZyb20gJy4vY3JlYXRlLW9yZGVyLW1hcCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBNYW51cHVsYXRlcyBVSSBvZiBTaGlwcGluZyBibG9jayBpbiBPcmRlciBjcmVhdGlvbiBwYWdlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFNoaXBwaW5nUmVuZGVyZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5zaGlwcGluZ0Jsb2NrKTtcbiAgICB0aGlzLiRmb3JtID0gJChjcmVhdGVPcmRlclBhZ2VNYXAuc2hpcHBpbmdGb3JtKTtcbiAgICB0aGlzLiRub0NhcnJpZXJCbG9jayA9ICQoY3JlYXRlT3JkZXJQYWdlTWFwLm5vQ2FycmllckJsb2NrKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0ge09iamVjdH0gc2hpcHBpbmdcbiAgICogQHBhcmFtIHtCb29sZWFufSBlbXB0eUNhcnRcbiAgICovXG4gIHJlbmRlcihzaGlwcGluZywgZW1wdHlDYXJ0KSB7XG4gICAgY29uc3Qgc2hpcHBpbmdJc0F2YWlsYWJsZSA9IHR5cGVvZiBzaGlwcGluZyAhPT0gJ3VuZGVmaW5lZCcgJiYgc2hpcHBpbmcgIT09IG51bGwgJiYgc2hpcHBpbmcubGVuZ3RoICE9PSAwO1xuXG4gICAgaWYgKGVtcHR5Q2FydCkge1xuICAgICAgdGhpcy5faGlkZUNvbnRhaW5lcigpO1xuICAgIH0gZWxzZSBpZiAoc2hpcHBpbmdJc0F2YWlsYWJsZSkge1xuICAgICAgdGhpcy5fZGlzcGxheUZvcm0oc2hpcHBpbmcpO1xuICAgIH0gZWxzZSB7XG4gICAgICB0aGlzLl9kaXNwbGF5Tm9DYXJyaWVyc1dhcm5pbmcoKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogU2hvdyBmb3JtIGJsb2NrIHdpdGggcmVuZGVyZWQgZGVsaXZlcnkgb3B0aW9ucyBpbnN0ZWFkIG9mIHdhcm5pbmcgbWVzc2FnZVxuICAgKlxuICAgKiBAcGFyYW0gc2hpcHBpbmdcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9kaXNwbGF5Rm9ybShzaGlwcGluZykge1xuICAgIHRoaXMuX2hpZGVOb0NhcnJpZXJCbG9jaygpO1xuICAgIHRoaXMuX3JlbmRlckRlbGl2ZXJ5T3B0aW9ucyhzaGlwcGluZy5kZWxpdmVyeU9wdGlvbnMsIHNoaXBwaW5nLnNlbGVjdGVkQ2FycmllcklkKTtcbiAgICB0aGlzLl9yZW5kZXJUb3RhbFNoaXBwaW5nKHNoaXBwaW5nLnNoaXBwaW5nUHJpY2UpO1xuICAgIHRoaXMuX3Nob3dGb3JtKCk7XG4gICAgdGhpcy5fc2hvd0NvbnRhaW5lcigpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3cgd2FybmluZyBtZXNzYWdlIHRoYXQgbm8gY2FycmllcnMgYXJlIGF2YWlsYWJsZSBhbmQgaGlkZSBmb3JtIGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZGlzcGxheU5vQ2FycmllcnNXYXJuaW5nKCkge1xuICAgIHRoaXMuX3Nob3dDb250YWluZXIoKTtcbiAgICB0aGlzLl9oaWRlRm9ybSgpO1xuICAgIHRoaXMuX3Nob3dOb0NhcnJpZXJCbG9jaygpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbmRlcnMgZGVsaXZlcnkgb3B0aW9ucyBzZWxlY3Rpb24gYmxvY2tcbiAgICpcbiAgICogQHBhcmFtIGRlbGl2ZXJ5T3B0aW9uc1xuICAgKiBAcGFyYW0gc2VsZWN0ZWRWYWxcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW5kZXJEZWxpdmVyeU9wdGlvbnMoZGVsaXZlcnlPcHRpb25zLCBzZWxlY3RlZFZhbCkge1xuICAgIGNvbnN0ICRkZWxpdmVyeU9wdGlvblNlbGVjdCA9ICQoY3JlYXRlT3JkZXJQYWdlTWFwLmRlbGl2ZXJ5T3B0aW9uU2VsZWN0KTtcbiAgICAkZGVsaXZlcnlPcHRpb25TZWxlY3QuZW1wdHkoKTtcblxuICAgIGZvciAoY29uc3Qga2V5IGluIE9iamVjdC5rZXlzKGRlbGl2ZXJ5T3B0aW9ucykpIHtcbiAgICAgIGNvbnN0IG9wdGlvbiA9IGRlbGl2ZXJ5T3B0aW9uc1trZXldO1xuXG4gICAgICBjb25zdCBkZWxpdmVyeU9wdGlvbiA9IHtcbiAgICAgICAgdmFsdWU6IG9wdGlvbi5jYXJyaWVySWQsXG4gICAgICAgIHRleHQ6IGAke29wdGlvbi5jYXJyaWVyTmFtZX0gLSAke29wdGlvbi5jYXJyaWVyRGVsYXl9YCxcbiAgICAgIH07XG5cbiAgICAgIGlmIChzZWxlY3RlZFZhbCA9PT0gZGVsaXZlcnlPcHRpb24udmFsdWUpIHtcbiAgICAgICAgZGVsaXZlcnlPcHRpb24uc2VsZWN0ZWQgPSAnc2VsZWN0ZWQnO1xuICAgICAgfVxuXG4gICAgICAkZGVsaXZlcnlPcHRpb25TZWxlY3QuYXBwZW5kKCQoJzxvcHRpb24+JywgZGVsaXZlcnlPcHRpb24pKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBkeW5hbWljIHZhbHVlIG9mIHNoaXBwaW5nIHByaWNlXG4gICAqXG4gICAqIEBwYXJhbSBzaGlwcGluZ1ByaWNlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcmVuZGVyVG90YWxTaGlwcGluZyhzaGlwcGluZ1ByaWNlKSB7XG4gICAgY29uc3QgJHRvdGFsU2hpcHBpbmdGaWVsZCA9ICQoY3JlYXRlT3JkZXJQYWdlTWFwLnRvdGFsU2hpcHBpbmdGaWVsZCk7XG4gICAgJHRvdGFsU2hpcHBpbmdGaWVsZC5lbXB0eSgpO1xuXG4gICAgJHRvdGFsU2hpcHBpbmdGaWVsZC5hcHBlbmQoc2hpcHBpbmdQcmljZSk7XG4gIH1cblxuICAvKipcbiAgICogU2hvdyB3aG9sZSBzaGlwcGluZyBjb250YWluZXJcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93Q29udGFpbmVyKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZSB3aG9sZSBzaGlwcGluZyBjb250YWluZXJcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlQ29udGFpbmVyKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogU2hvdyBmb3JtIGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd0Zvcm0oKSB7XG4gICAgdGhpcy4kZm9ybS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZSBmb3JtIGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZUZvcm0oKSB7XG4gICAgdGhpcy4kZm9ybS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogU2hvdyB3YXJuaW5nIG1lc3NhZ2UgYmxvY2sgd2hpY2ggd2FybnMgdGhhdCBubyBjYXJyaWVycyBhcmUgYXZhaWxhYmxlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd05vQ2FycmllckJsb2NrKCkge1xuICAgIHRoaXMuJG5vQ2FycmllckJsb2NrLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlIHdhcm5pbmcgbWVzc2FnZSBibG9jayB3aGljaCB3YXJucyB0aGF0IG5vIGNhcnJpZXJzIGFyZSBhdmFpbGFibGVcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlTm9DYXJyaWVyQmxvY2soKSB7XG4gICAgdGhpcy4kbm9DYXJyaWVyQmxvY2suYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvc2hpcHBpbmctcmVuZGVyZXIuanMiLCIndXNlIHN0cmljdCc7dmFyIF9leHRlbmRzPU9iamVjdC5hc3NpZ258fGZ1bmN0aW9uKGEpe2Zvcih2YXIgYixjPTE7Yzxhcmd1bWVudHMubGVuZ3RoO2MrKylmb3IodmFyIGQgaW4gYj1hcmd1bWVudHNbY10sYilPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwoYixkKSYmKGFbZF09YltkXSk7cmV0dXJuIGF9LF90eXBlb2Y9J2Z1bmN0aW9uJz09dHlwZW9mIFN5bWJvbCYmJ3N5bWJvbCc9PXR5cGVvZiBTeW1ib2wuaXRlcmF0b3I/ZnVuY3Rpb24oYSl7cmV0dXJuIHR5cGVvZiBhfTpmdW5jdGlvbihhKXtyZXR1cm4gYSYmJ2Z1bmN0aW9uJz09dHlwZW9mIFN5bWJvbCYmYS5jb25zdHJ1Y3Rvcj09PVN5bWJvbCYmYSE9PVN5bWJvbC5wcm90b3R5cGU/J3N5bWJvbCc6dHlwZW9mIGF9O2Z1bmN0aW9uIF9jbGFzc0NhbGxDaGVjayhhLGIpe2lmKCEoYSBpbnN0YW5jZW9mIGIpKXRocm93IG5ldyBUeXBlRXJyb3IoJ0Nhbm5vdCBjYWxsIGEgY2xhc3MgYXMgYSBmdW5jdGlvbicpfXZhciBSb3V0aW5nPWZ1bmN0aW9uIGEoKXt2YXIgYj10aGlzO19jbGFzc0NhbGxDaGVjayh0aGlzLGEpLHRoaXMuc2V0Um91dGVzPWZ1bmN0aW9uKGEpe2Iucm91dGVzUm91dGluZz1hfHxbXX0sdGhpcy5nZXRSb3V0ZXM9ZnVuY3Rpb24oKXtyZXR1cm4gYi5yb3V0ZXNSb3V0aW5nfSx0aGlzLnNldEJhc2VVcmw9ZnVuY3Rpb24oYSl7Yi5jb250ZXh0Um91dGluZy5iYXNlX3VybD1hfSx0aGlzLmdldEJhc2VVcmw9ZnVuY3Rpb24oKXtyZXR1cm4gYi5jb250ZXh0Um91dGluZy5iYXNlX3VybH0sdGhpcy5zZXRQcmVmaXg9ZnVuY3Rpb24oYSl7Yi5jb250ZXh0Um91dGluZy5wcmVmaXg9YX0sdGhpcy5zZXRTY2hlbWU9ZnVuY3Rpb24oYSl7Yi5jb250ZXh0Um91dGluZy5zY2hlbWU9YX0sdGhpcy5nZXRTY2hlbWU9ZnVuY3Rpb24oKXtyZXR1cm4gYi5jb250ZXh0Um91dGluZy5zY2hlbWV9LHRoaXMuc2V0SG9zdD1mdW5jdGlvbihhKXtiLmNvbnRleHRSb3V0aW5nLmhvc3Q9YX0sdGhpcy5nZXRIb3N0PWZ1bmN0aW9uKCl7cmV0dXJuIGIuY29udGV4dFJvdXRpbmcuaG9zdH0sdGhpcy5idWlsZFF1ZXJ5UGFyYW1zPWZ1bmN0aW9uKGEsYyxkKXt2YXIgZT1uZXcgUmVnRXhwKC9cXFtdJC8pO2MgaW5zdGFuY2VvZiBBcnJheT9jLmZvckVhY2goZnVuY3Rpb24oYyxmKXtlLnRlc3QoYSk/ZChhLGMpOmIuYnVpbGRRdWVyeVBhcmFtcyhhKydbJysoJ29iamVjdCc9PT0oJ3VuZGVmaW5lZCc9PXR5cGVvZiBjPyd1bmRlZmluZWQnOl90eXBlb2YoYykpP2Y6JycpKyddJyxjLGQpfSk6J29iamVjdCc9PT0oJ3VuZGVmaW5lZCc9PXR5cGVvZiBjPyd1bmRlZmluZWQnOl90eXBlb2YoYykpP09iamVjdC5rZXlzKGMpLmZvckVhY2goZnVuY3Rpb24oZSl7cmV0dXJuIGIuYnVpbGRRdWVyeVBhcmFtcyhhKydbJytlKyddJyxjW2VdLGQpfSk6ZChhLGMpfSx0aGlzLmdldFJvdXRlPWZ1bmN0aW9uKGEpe3ZhciBjPWIuY29udGV4dFJvdXRpbmcucHJlZml4K2E7aWYoISFiLnJvdXRlc1JvdXRpbmdbY10pcmV0dXJuIGIucm91dGVzUm91dGluZ1tjXTtlbHNlIGlmKCFiLnJvdXRlc1JvdXRpbmdbYV0pdGhyb3cgbmV3IEVycm9yKCdUaGUgcm91dGUgXCInK2ErJ1wiIGRvZXMgbm90IGV4aXN0LicpO3JldHVybiBiLnJvdXRlc1JvdXRpbmdbYV19LHRoaXMuZ2VuZXJhdGU9ZnVuY3Rpb24oYSxjLGQpe3ZhciBlPWIuZ2V0Um91dGUoYSksZj1jfHx7fSxnPV9leHRlbmRzKHt9LGYpLGg9J19zY2hlbWUnLGk9Jycsaj0hMCxrPScnO2lmKChlLnRva2Vuc3x8W10pLmZvckVhY2goZnVuY3Rpb24oYil7aWYoJ3RleHQnPT09YlswXSlyZXR1cm4gaT1iWzFdK2ksdm9pZChqPSExKTtpZigndmFyaWFibGUnPT09YlswXSl7dmFyIGM9KGUuZGVmYXVsdHN8fHt9KVtiWzNdXTtpZighMT09anx8IWN8fChmfHx7fSlbYlszXV0mJmZbYlszXV0hPT1lLmRlZmF1bHRzW2JbM11dKXt2YXIgZDtpZigoZnx8e30pW2JbM11dKWQ9ZltiWzNdXSxkZWxldGUgZ1tiWzNdXTtlbHNlIGlmKGMpZD1lLmRlZmF1bHRzW2JbM11dO2Vsc2V7aWYoailyZXR1cm47dGhyb3cgbmV3IEVycm9yKCdUaGUgcm91dGUgXCInK2ErJ1wiIHJlcXVpcmVzIHRoZSBwYXJhbWV0ZXIgXCInK2JbM10rJ1wiLicpfXZhciBoPSEwPT09ZHx8ITE9PT1kfHwnJz09PWQ7aWYoIWh8fCFqKXt2YXIgaz1lbmNvZGVVUklDb21wb25lbnQoZCkucmVwbGFjZSgvJTJGL2csJy8nKTsnbnVsbCc9PT1rJiZudWxsPT09ZCYmKGs9JycpLGk9YlsxXStrK2l9aj0hMX1lbHNlIGMmJmRlbGV0ZSBnW2JbM11dO3JldHVybn10aHJvdyBuZXcgRXJyb3IoJ1RoZSB0b2tlbiB0eXBlIFwiJytiWzBdKydcIiBpcyBub3Qgc3VwcG9ydGVkLicpfSksJyc9PWkmJihpPScvJyksKGUuaG9zdHRva2Vuc3x8W10pLmZvckVhY2goZnVuY3Rpb24oYSl7dmFyIGI7cmV0dXJuJ3RleHQnPT09YVswXT92b2lkKGs9YVsxXStrKTp2b2lkKCd2YXJpYWJsZSc9PT1hWzBdJiYoKGZ8fHt9KVthWzNdXT8oYj1mW2FbM11dLGRlbGV0ZSBnW2FbM11dKTplLmRlZmF1bHRzW2FbM11dJiYoYj1lLmRlZmF1bHRzW2FbM11dKSxrPWFbMV0rYitrKSl9KSxpPWIuY29udGV4dFJvdXRpbmcuYmFzZV91cmwraSxlLnJlcXVpcmVtZW50c1toXSYmYi5nZXRTY2hlbWUoKSE9PWUucmVxdWlyZW1lbnRzW2hdP2k9ZS5yZXF1aXJlbWVudHNbaF0rJzovLycrKGt8fGIuZ2V0SG9zdCgpKStpOmsmJmIuZ2V0SG9zdCgpIT09az9pPWIuZ2V0U2NoZW1lKCkrJzovLycraytpOiEwPT09ZCYmKGk9Yi5nZXRTY2hlbWUoKSsnOi8vJytiLmdldEhvc3QoKStpKSwwPE9iamVjdC5rZXlzKGcpLmxlbmd0aCl7dmFyIGw9W10sbT1mdW5jdGlvbihhLGIpe3ZhciBjPWI7Yz0nZnVuY3Rpb24nPT10eXBlb2YgYz9jKCk6YyxjPW51bGw9PT1jPycnOmMsbC5wdXNoKGVuY29kZVVSSUNvbXBvbmVudChhKSsnPScrZW5jb2RlVVJJQ29tcG9uZW50KGMpKX07T2JqZWN0LmtleXMoZykuZm9yRWFjaChmdW5jdGlvbihhKXtyZXR1cm4gYi5idWlsZFF1ZXJ5UGFyYW1zKGEsZ1thXSxtKX0pLGk9aSsnPycrbC5qb2luKCcmJykucmVwbGFjZSgvJTIwL2csJysnKX1yZXR1cm4gaX0sdGhpcy5zZXREYXRhPWZ1bmN0aW9uKGEpe2Iuc2V0QmFzZVVybChhLmJhc2VfdXJsKSxiLnNldFJvdXRlcyhhLnJvdXRlcyksJ3ByZWZpeCdpbiBhJiZiLnNldFByZWZpeChhLnByZWZpeCksYi5zZXRIb3N0KGEuaG9zdCksYi5zZXRTY2hlbWUoYS5zY2hlbWUpfSx0aGlzLmNvbnRleHRSb3V0aW5nPXtiYXNlX3VybDonJyxwcmVmaXg6JycsaG9zdDonJyxzY2hlbWU6Jyd9fTttb2R1bGUuZXhwb3J0cz1uZXcgUm91dGluZztcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vZm9zLXJvdXRpbmcvZGlzdC9yb3V0aW5nLmpzXG4vLyBtb2R1bGUgaWQgPSA0NDlcbi8vIG1vZHVsZSBjaHVua3MgPSAxMyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBSb3V0aW5nIGZyb20gJ2Zvcy1yb3V0aW5nJztcbmltcG9ydCByb3V0ZXMgZnJvbSAnLi4vZm9zX2pzX3JvdXRlcy5qc29uJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFdyYXBzIEZPU0pzUm91dGluZ2J1bmRsZSB3aXRoIGV4cG9zZWQgcm91dGVzLlxuICogVG8gZXhwb3NlIHJvdXRlIGFkZCBvcHRpb24gYGV4cG9zZTogdHJ1ZWAgaW4gLnltbCByb3V0aW5nIGNvbmZpZ1xuICpcbiAqIGUuZy5cbiAqXG4gKiBgbXlfcm91dGVcbiAqICAgIHBhdGg6IC9teS1wYXRoXG4gKiAgICBvcHRpb25zOlxuICogICAgICBleHBvc2U6IHRydWVcbiAqIGBcbiAqIEFuZCBydW4gYGJpbi9jb25zb2xlIGZvczpqcy1yb3V0aW5nOmR1bXAgLS1mb3JtYXQ9anNvbiAtLXRhcmdldD1hZG1pbi1kZXYvdGhlbWVzL25ldy10aGVtZS9qcy9mb3NfanNfcm91dGVzLmpzb25gXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFJvdXRlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIFJvdXRpbmcuc2V0RGF0YShyb3V0ZXMpO1xuICAgIFJvdXRpbmcuc2V0QmFzZVVybCgkKGRvY3VtZW50KS5maW5kKCdib2R5JykuZGF0YSgnYmFzZS11cmwnKSk7XG5cbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIC8qKlxuICAgKiBEZWNvcmF0ZWQgXCJnZW5lcmF0ZVwiIG1ldGhvZCwgd2l0aCBwcmVkZWZpbmVkIHNlY3VyaXR5IHRva2VuIGluIHBhcmFtc1xuICAgKlxuICAgKiBAcGFyYW0gcm91dGVcbiAgICogQHBhcmFtIHBhcmFtc1xuICAgKlxuICAgKiBAcmV0dXJucyB7U3RyaW5nfVxuICAgKi9cbiAgZ2VuZXJhdGUocm91dGUsIHBhcmFtcyA9IHt9KSB7XG4gICAgY29uc3QgdG9rZW5pemVkUGFyYW1zID0gT2JqZWN0LmFzc2lnbihwYXJhbXMsIHtfdG9rZW46ICQoZG9jdW1lbnQpLmZpbmQoJ2JvZHknKS5kYXRhKCd0b2tlbicpfSk7XG5cbiAgICByZXR1cm4gUm91dGluZy5nZW5lcmF0ZShyb3V0ZSwgdG9rZW5pemVkUGFyYW1zKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9yb3V0ZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG4vKipcbiAqIEVuY2Fwc3VsYXRlcyBqcyBldmVudHMgdXNlZCBpbiBjcmVhdGUgb3JkZXIgcGFnZVxuICovXG5leHBvcnQgZGVmYXVsdCB7XG4gIC8vIHdoZW4gY3VzdG9tZXIgc2VhcmNoIGFjdGlvbiBpcyBkb25lXG4gIGN1c3RvbWVyU2VhcmNoZWQ6ICdjdXN0b21lclNlYXJjaGVkJyxcbiAgLy8gd2hlbiBuZXcgY3VzdG9tZXIgaXMgc2VsZWN0ZWRcbiAgY3VzdG9tZXJTZWxlY3RlZDogJ2N1c3RvbWVyU2VsZWN0ZWQnLFxuICAvLyB3aGVuIG5ldyBjYXJ0IGlzIGxvYWRlZCwgbm8gbWF0dGVyIGlmIGl0cyBlbXB0eSwgc2VsZWN0ZWQgZnJvbSBjYXJ0cyBsaXN0IG9yIGR1cGxpY2F0ZWQgYnkgb3JkZXIuXG4gIGNhcnRMb2FkZWQ6ICdjYXJ0TG9hZGVkJyxcbiAgLy8gd2hlbiBjYXJ0IGFkZHJlc3NlcyBpbmZvcm1hdGlvbiBoYXMgYmVlbiBjaGFuZ2VkXG4gIGNhcnRBZGRyZXNzZXNDaGFuZ2VkOiAnY2FydEFkZHJlc3Nlc0NoYW5nZWQnLFxuICAvLyB3aGVuIGNhcnQgZGVsaXZlcnkgb3B0aW9uIGhhcyBiZWVuIGNoYW5nZWRcbiAgY2FydERlbGl2ZXJ5T3B0aW9uQ2hhbmdlZDogJ2NhcnREZWxpdmVyeU9wdGlvbkNoYW5nZWQnLFxuICAvLyB3aGVuIGNhcnQgZnJlZSBzaGlwcGluZyB2YWx1ZSBoYXMgYmVlbiBjaGFuZ2VkXG4gIGNhcnRGcmVlU2hpcHBpbmdTZXQ6ICdjYXJ0RnJlZVNoaXBwaW5nU2V0JyxcbiAgLy8gd2hlbiBjYXJ0IHJ1bGVzIHNlYXJjaCBhY3Rpb24gaXMgZG9uZVxuICBjYXJ0UnVsZVNlYXJjaGVkOiAnY2FydFJ1bGVTZWFyY2hlZCcsXG4gIC8vIHdoZW4gY2FydCBydWxlIGlzIHJlbW92ZWQgZnJvbSBjYXJ0XG4gIGNhcnRSdWxlUmVtb3ZlZDogJ2NhcnRSdWxlUmVtb3ZlZCcsXG4gIC8vIHdoZW4gY2FydCBydWxlIGlzIGFkZGVkIHRvIGNhcnRcbiAgY2FydFJ1bGVBZGRlZDogJ2NhcnRSdWxlQWRkZWQnLFxuICAvLyB3aGVuIGNhcnQgcnVsZSBjYW5ub3QgYmUgYWRkZWQgdG8gY2FydFxuICBjYXJ0UnVsZUZhaWxlZFRvQWRkOiAnY2FydFJ1bGVGYWlsZWRUb0FkZCcsXG4gIC8vIHdoZW4gcHJvZHVjdCBzZWFyY2ggYWN0aW9uIGlzIGRvbmVcbiAgcHJvZHVjdFNlYXJjaGVkOiAncHJvZHVjdFNlYXJjaGVkJyxcbiAgLy8gd2hlbiBwcm9kdWN0IGlzIGFkZGVkIHRvIGNhcnRcbiAgcHJvZHVjdEFkZGVkVG9DYXJ0OiAncHJvZHVjdEFkZGVkVG9DYXJ0JyxcbiAgLy8gd2hlbiBwcm9kdWN0IGlzIHJlbW92ZWQgZnJvbSBjYXJ0XG4gIHByb2R1Y3RSZW1vdmVkRnJvbUNhcnQ6ICdwcm9kdWN0UmVtb3ZlZEZyb21DYXJ0Jyxcbn07XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvZXZlbnQtbWFwLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IFJvdXRlciBmcm9tICcuLi8uLi8uLi9jb21wb25lbnRzL3JvdXRlcic7XG5pbXBvcnQge0V2ZW50RW1pdHRlcn0gZnJvbSAnLi4vLi4vLi4vY29tcG9uZW50cy9ldmVudC1lbWl0dGVyJztcbmltcG9ydCBldmVudE1hcCBmcm9tICcuL2V2ZW50LW1hcCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBQcm92aWRlcyBhamF4IGNhbGxzIGZvciBjYXJ0IGVkaXRpbmcgYWN0aW9uc1xuICogRWFjaCBtZXRob2QgZW1pdHMgYW4gZXZlbnQgd2l0aCB1cGRhdGVkIGNhcnQgaW5mb3JtYXRpb24gYWZ0ZXIgc3VjY2Vzcy5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ2FydEVkaXRvciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMucm91dGVyID0gbmV3IFJvdXRlcigpO1xuICB9XG5cbiAgLyoqXG4gICAqIENoYW5nZXMgY2FydCBhZGRyZXNzZXNcbiAgICpcbiAgICogQHBhcmFtIHtOdW1iZXJ9IGNhcnRJZFxuICAgKiBAcGFyYW0ge09iamVjdH0gYWRkcmVzc2VzXG4gICAqL1xuICBjaGFuZ2VDYXJ0QWRkcmVzc2VzKGNhcnRJZCwgYWRkcmVzc2VzKSB7XG4gICAgJC5wb3N0KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9jYXJ0c19lZGl0X2FkZHJlc3NlcycsIHtjYXJ0SWR9KSwgYWRkcmVzc2VzKS50aGVuKChjYXJ0SW5mbykgPT4ge1xuICAgICAgRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAuY2FydEFkZHJlc3Nlc0NoYW5nZWQsIGNhcnRJbmZvKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBNb2RpZmllcyBjYXJ0IGRlbGl2ZXJ5IG9wdGlvblxuICAgKlxuICAgKiBAcGFyYW0ge051bWJlcn0gY2FydElkXG4gICAqIEBwYXJhbSB7TnVtYmVyfSB2YWx1ZVxuICAgKi9cbiAgY2hhbmdlRGVsaXZlcnlPcHRpb24oY2FydElkLCB2YWx1ZSkge1xuICAgICQucG9zdCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY2FydHNfZWRpdF9jYXJyaWVyJywge2NhcnRJZH0pLCB7XG4gICAgICBjYXJyaWVySWQ6IHZhbHVlLFxuICAgIH0pLnRoZW4oKGNhcnRJbmZvKSA9PiB7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5jYXJ0RGVsaXZlcnlPcHRpb25DaGFuZ2VkLCBjYXJ0SW5mbyk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQ2hhbmdlcyBjYXJ0IGZyZWUgc2hpcHBpbmcgdmFsdWVcbiAgICpcbiAgICogQHBhcmFtIHtOdW1iZXJ9IGNhcnRJZFxuICAgKiBAcGFyYW0ge0Jvb2xlYW59IHZhbHVlXG4gICAqL1xuICBzZXRGcmVlU2hpcHBpbmcoY2FydElkLCB2YWx1ZSkge1xuICAgICQucG9zdCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY2FydHNfc2V0X2ZyZWVfc2hpcHBpbmcnLCB7Y2FydElkfSksIHtcbiAgICAgIGZyZWVTaGlwcGluZzogdmFsdWUsXG4gICAgfSkudGhlbigoY2FydEluZm8pID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRGcmVlU2hpcHBpbmdTZXQsIGNhcnRJbmZvKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBZGRzIGNhcnQgcnVsZSB0byBjYXJ0XG4gICAqXG4gICAqIEBwYXJhbSB7TnVtYmVyfSBjYXJ0UnVsZUlkXG4gICAqIEBwYXJhbSB7TnVtYmVyfSBjYXJ0SWRcbiAgICovXG4gIGFkZENhcnRSdWxlVG9DYXJ0KGNhcnRSdWxlSWQsIGNhcnRJZCkge1xuICAgICQucG9zdCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY2FydHNfYWRkX2NhcnRfcnVsZScsIHtjYXJ0SWR9KSwge1xuICAgICAgY2FydFJ1bGVJZCxcbiAgICB9KS50aGVuKChjYXJ0SW5mbykgPT4ge1xuICAgICAgRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAuY2FydFJ1bGVBZGRlZCwgY2FydEluZm8pO1xuICAgIH0pLmNhdGNoKChyZXNwb25zZSkgPT4ge1xuICAgICAgRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAuY2FydFJ1bGVGYWlsZWRUb0FkZCwgcmVzcG9uc2UucmVzcG9uc2VKU09OLm1lc3NhZ2UpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbW92ZXMgY2FydCBydWxlIGZyb20gY2FydFxuICAgKlxuICAgKiBAcGFyYW0ge051bWJlcn0gY2FydFJ1bGVJZFxuICAgKiBAcGFyYW0ge051bWJlcn0gY2FydElkXG4gICAqL1xuICByZW1vdmVDYXJ0UnVsZUZyb21DYXJ0KGNhcnRSdWxlSWQsIGNhcnRJZCkge1xuICAgICQucG9zdCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY2FydHNfZGVsZXRlX2NhcnRfcnVsZScsIHtcbiAgICAgIGNhcnRJZCxcbiAgICAgIGNhcnRSdWxlSWQsXG4gICAgfSkpLnRoZW4oKGNhcnRJbmZvKSA9PiB7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5jYXJ0UnVsZVJlbW92ZWQsIGNhcnRJbmZvKTtcbiAgICB9KS5jYXRjaCgocmVzcG9uc2UpID0+IHtcbiAgICAgIHNob3dFcnJvck1lc3NhZ2UocmVzcG9uc2UucmVzcG9uc2VKU09OLm1lc3NhZ2UpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEFkZHMgcHJvZHVjdCB0byBjYXJ0XG4gICAqXG4gICAqIEBwYXJhbSB7TnVtYmVyfSBjYXJ0SWRcbiAgICogQHBhcmFtIHtGb3JtRGF0YX0gcHJvZHVjdFxuICAgKi9cbiAgYWRkUHJvZHVjdChjYXJ0SWQsIHByb2R1Y3QpIHtcbiAgICAkLmFqYXgodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX2NhcnRzX2FkZF9wcm9kdWN0Jywge2NhcnRJZH0pLCB7XG4gICAgICBtZXRob2Q6ICdQT1NUJyxcbiAgICAgIGRhdGE6IHByb2R1Y3QsXG4gICAgICBwcm9jZXNzRGF0YTogZmFsc2UsXG4gICAgICBjb250ZW50VHlwZTogZmFsc2UsXG4gICAgfSkudGhlbigoY2FydEluZm8pID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLnByb2R1Y3RBZGRlZFRvQ2FydCwgY2FydEluZm8pO1xuICAgIH0pLmNhdGNoKChyZXNwb25zZSkgPT4ge1xuICAgICAgc2hvd0Vycm9yTWVzc2FnZShyZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogUmVtb3ZlcyBwcm9kdWN0IGZyb20gY2FydFxuICAgKlxuICAgKiBAcGFyYW0ge051bWJlcn0gY2FydElkXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBwcm9kdWN0XG4gICAqL1xuICByZW1vdmVQcm9kdWN0RnJvbUNhcnQoY2FydElkLCBwcm9kdWN0KSB7XG4gICAgJC5wb3N0KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9jYXJ0c19kZWxldGVfcHJvZHVjdCcsIHtjYXJ0SWR9KSwge1xuICAgICAgcHJvZHVjdElkOiBwcm9kdWN0LnByb2R1Y3RJZCxcbiAgICAgIGF0dHJpYnV0ZUlkOiBwcm9kdWN0LmF0dHJpYnV0ZUlkLFxuICAgICAgY3VzdG9taXphdGlvbklkOiBwcm9kdWN0LmN1c3RvbWl6YXRpb25JZCxcbiAgICB9KS50aGVuKChjYXJ0SW5mbykgPT4ge1xuICAgICAgRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAucHJvZHVjdFJlbW92ZWRGcm9tQ2FydCwgY2FydEluZm8pO1xuICAgIH0pLmNhdGNoKChyZXNwb25zZSkgPT4ge1xuICAgICAgc2hvd0Vycm9yTWVzc2FnZShyZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LWVkaXRvci5qcyJdLCJzb3VyY2VSb290IjoiIn0=