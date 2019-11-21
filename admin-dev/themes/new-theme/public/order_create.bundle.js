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
/******/ 	return __webpack_require__(__webpack_require__.s = 377);
/******/ })
/************************************************************************/
/******/ ({

/***/ 102:
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

var _createOrderMap = __webpack_require__(34);

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

/***/ 103:
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

var _createOrderMap = __webpack_require__(34);

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

var _events = __webpack_require__(18);

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

/***/ 18:
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

/***/ 283:
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

var _createOrderMap = __webpack_require__(34);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _customerManager = __webpack_require__(381);

var _customerManager2 = _interopRequireDefault(_customerManager);

var _shippingRenderer = __webpack_require__(384);

var _shippingRenderer2 = _interopRequireDefault(_shippingRenderer);

var _cartProvider = __webpack_require__(379);

var _cartProvider2 = _interopRequireDefault(_cartProvider);

var _addressesRenderer = __webpack_require__(378);

var _addressesRenderer2 = _interopRequireDefault(_addressesRenderer);

var _cartRulesRenderer = __webpack_require__(102);

var _cartRulesRenderer2 = _interopRequireDefault(_cartRulesRenderer);

var _router = __webpack_require__(35);

var _router2 = _interopRequireDefault(_router);

var _eventEmitter = __webpack_require__(17);

var _cartEditor = __webpack_require__(74);

var _cartEditor2 = _interopRequireDefault(_cartEditor);

var _eventMap = __webpack_require__(51);

var _eventMap2 = _interopRequireDefault(_eventMap);

var _cartRuleManager = __webpack_require__(380);

var _cartRuleManager2 = _interopRequireDefault(_cartRuleManager);

var _productManager = __webpack_require__(383);

var _productManager2 = _interopRequireDefault(_productManager);

var _productRenderer = __webpack_require__(103);

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

/***/ 34:
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

/***/ 35:
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

var _fosRouting = __webpack_require__(82);

var _fosRouting2 = _interopRequireDefault(_fosRouting);

var _fos_js_routes = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@js/fos_js_routes.json\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

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

/***/ 377:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _createOrderPage = __webpack_require__(283);

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

/***/ 378:
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

var _createOrderMap = __webpack_require__(34);

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

/***/ 379:
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

var _createOrderMap = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@pages/order/create/create-order-map\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _router = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/router\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _router2 = _interopRequireDefault(_router);

var _eventEmitter = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/event-emitter\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _eventMap = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@pages/order/create/event-map\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

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

/***/ 380:
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

var _cartEditor = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@pages/order/create/cart-editor\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _cartEditor2 = _interopRequireDefault(_cartEditor);

var _cartRulesRenderer = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@pages/order/create/cart-rules-renderer\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _cartRulesRenderer2 = _interopRequireDefault(_cartRulesRenderer);

var _createOrderMap = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@pages/order/create/create-order-map\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _eventEmitter = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/event-emitter\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _eventMap = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@pages/order/create/event-map\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _eventMap2 = _interopRequireDefault(_eventMap);

var _router = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/router\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

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

/***/ 381:
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

var _createOrderMap = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@pages/order/create/create-order-map\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _customerRenderer = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@pages/order/create/customer-renderer\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _customerRenderer2 = _interopRequireDefault(_customerRenderer);

var _eventEmitter = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/event-emitter\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _eventMap = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@pages/order/create/event-map\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _eventMap2 = _interopRequireDefault(_eventMap);

var _router = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/router\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

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

/***/ 383:
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

var _cartEditor = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@pages/order/create/cart-editor\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _cartEditor2 = _interopRequireDefault(_cartEditor);

var _createOrderMap = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@pages/order/create/create-order-map\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _eventMap = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@pages/order/create/event-map\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _eventMap2 = _interopRequireDefault(_eventMap);

var _eventEmitter = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/event-emitter\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _productRenderer = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@pages/order/create/product-renderer\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _productRenderer2 = _interopRequireDefault(_productRenderer);

var _router = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/router\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

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

/***/ 384:
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

var _createOrderMap = __webpack_require__(34);

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

/***/ 74:
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

var _router = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/router\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _router2 = _interopRequireDefault(_router);

var _eventEmitter = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/event-emitter\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _eventMap = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@pages/order/create/event-map\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

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

/***/ }),

/***/ 82:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
var _extends=Object.assign||function(a){for(var b,c=1;c<arguments.length;c++)for(var d in b=arguments[c],b)Object.prototype.hasOwnProperty.call(b,d)&&(a[d]=b[d]);return a},_typeof='function'==typeof Symbol&&'symbol'==typeof Symbol.iterator?function(a){return typeof a}:function(a){return a&&'function'==typeof Symbol&&a.constructor===Symbol&&a!==Symbol.prototype?'symbol':typeof a};function _classCallCheck(a,b){if(!(a instanceof b))throw new TypeError('Cannot call a class as a function')}var Routing=function a(){var b=this;_classCallCheck(this,a),this.setRoutes=function(a){b.routesRouting=a||[]},this.getRoutes=function(){return b.routesRouting},this.setBaseUrl=function(a){b.contextRouting.base_url=a},this.getBaseUrl=function(){return b.contextRouting.base_url},this.setPrefix=function(a){b.contextRouting.prefix=a},this.setScheme=function(a){b.contextRouting.scheme=a},this.getScheme=function(){return b.contextRouting.scheme},this.setHost=function(a){b.contextRouting.host=a},this.getHost=function(){return b.contextRouting.host},this.buildQueryParams=function(a,c,d){var e=new RegExp(/\[]$/);c instanceof Array?c.forEach(function(c,f){e.test(a)?d(a,c):b.buildQueryParams(a+'['+('object'===('undefined'==typeof c?'undefined':_typeof(c))?f:'')+']',c,d)}):'object'===('undefined'==typeof c?'undefined':_typeof(c))?Object.keys(c).forEach(function(e){return b.buildQueryParams(a+'['+e+']',c[e],d)}):d(a,c)},this.getRoute=function(a){var c=b.contextRouting.prefix+a;if(!!b.routesRouting[c])return b.routesRouting[c];else if(!b.routesRouting[a])throw new Error('The route "'+a+'" does not exist.');return b.routesRouting[a]},this.generate=function(a,c,d){var e=b.getRoute(a),f=c||{},g=_extends({},f),h='_scheme',i='',j=!0,k='';if((e.tokens||[]).forEach(function(b){if('text'===b[0])return i=b[1]+i,void(j=!1);if('variable'===b[0]){var c=(e.defaults||{})[b[3]];if(!1==j||!c||(f||{})[b[3]]&&f[b[3]]!==e.defaults[b[3]]){var d;if((f||{})[b[3]])d=f[b[3]],delete g[b[3]];else if(c)d=e.defaults[b[3]];else{if(j)return;throw new Error('The route "'+a+'" requires the parameter "'+b[3]+'".')}var h=!0===d||!1===d||''===d;if(!h||!j){var k=encodeURIComponent(d).replace(/%2F/g,'/');'null'===k&&null===d&&(k=''),i=b[1]+k+i}j=!1}else c&&delete g[b[3]];return}throw new Error('The token type "'+b[0]+'" is not supported.')}),''==i&&(i='/'),(e.hosttokens||[]).forEach(function(a){var b;return'text'===a[0]?void(k=a[1]+k):void('variable'===a[0]&&((f||{})[a[3]]?(b=f[a[3]],delete g[a[3]]):e.defaults[a[3]]&&(b=e.defaults[a[3]]),k=a[1]+b+k))}),i=b.contextRouting.base_url+i,e.requirements[h]&&b.getScheme()!==e.requirements[h]?i=e.requirements[h]+'://'+(k||b.getHost())+i:k&&b.getHost()!==k?i=b.getScheme()+'://'+k+i:!0===d&&(i=b.getScheme()+'://'+b.getHost()+i),0<Object.keys(g).length){var l=[],m=function(a,b){var c=b;c='function'==typeof c?c():c,c=null===c?'':c,l.push(encodeURIComponent(a)+'='+encodeURIComponent(c))};Object.keys(g).forEach(function(a){return b.buildQueryParams(a,g[a],m)}),i=i+'?'+l.join('&').replace(/%20/g,'+')}return i},this.setData=function(a){b.setBaseUrl(a.base_url),b.setRoutes(a.routes),'prefix'in a&&b.setPrefix(a.prefix),b.setHost(a.host),b.setScheme(a.scheme)},this.contextRouting={base_url:'',prefix:'',host:'',scheme:''}};module.exports=new Routing;

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgOTNlN2NjMWY2YWNkMjQxMGYyNDI/OGQ2NioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL2NhcnQtcnVsZXMtcmVuZGVyZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL3Byb2R1Y3QtcmVuZGVyZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ldmVudC1lbWl0dGVyLmpzPzBlMDMiLCJ3ZWJwYWNrOi8vLy4vfi9ldmVudHMvZXZlbnRzLmpzPzdjNzEiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL2NyZWF0ZS1vcmRlci1wYWdlLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jcmVhdGUtb3JkZXItbWFwLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvcm91dGVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvYWRkcmVzc2VzLXJlbmRlcmVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LXByb3ZpZGVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LXJ1bGUtbWFuYWdlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY3VzdG9tZXItbWFuYWdlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvcHJvZHVjdC1tYW5hZ2VyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9zaGlwcGluZy1yZW5kZXJlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvZXZlbnQtbWFwLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LWVkaXRvci5qcyIsIndlYnBhY2s6Ly8vLi9+L2Zvcy1yb3V0aW5nL2Rpc3Qvcm91dGluZy5qcyJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwiQ2FydFJ1bGVzUmVuZGVyZXIiLCIkY2FydFJ1bGVzQmxvY2siLCJjcmVhdGVPcmRlck1hcCIsImNhcnRSdWxlc0Jsb2NrIiwiJGNhcnRSdWxlc1RhYmxlIiwiY2FydFJ1bGVzVGFibGUiLCIkc2VhcmNoUmVzdWx0Qm94IiwiY2FydFJ1bGVzU2VhcmNoUmVzdWx0Qm94IiwiY2FydFJ1bGVzIiwiZW1wdHlDYXJ0IiwiX2hpZGVFcnJvckJsb2NrIiwiX2hpZGVDYXJ0UnVsZXNCbG9jayIsIl9zaG93Q2FydFJ1bGVzQmxvY2siLCJsZW5ndGgiLCJfaGlkZUNhcnRSdWxlc0xpc3QiLCJfcmVuZGVyTGlzdCIsInNlYXJjaFJlc3VsdHMiLCJfY2xlYXJTZWFyY2hSZXN1bHRzIiwiY2FydF9ydWxlcyIsIl9yZW5kZXJOb3RGb3VuZCIsIl9yZW5kZXJGb3VuZENhcnRSdWxlcyIsIl9zaG93UmVzdWx0c0Ryb3Bkb3duIiwibWVzc2FnZSIsImNhcnRSdWxlRXJyb3JUZXh0IiwidGV4dCIsIl9zaG93RXJyb3JCbG9jayIsImFkZENsYXNzIiwicmVtb3ZlQ2xhc3MiLCIkdGVtcGxhdGUiLCJjYXJ0UnVsZXNOb3RGb3VuZFRlbXBsYXRlIiwiaHRtbCIsImNsb25lIiwiZW1wdHkiLCIkY2FydFJ1bGVUZW1wbGF0ZSIsImZvdW5kQ2FydFJ1bGVUZW1wbGF0ZSIsImtleSIsImNhcnRSdWxlIiwiY2FydFJ1bGVOYW1lIiwibmFtZSIsImNvZGUiLCJkYXRhIiwiY2FydFJ1bGVJZCIsImFwcGVuZCIsIl9jbGVhbkNhcnRSdWxlc0xpc3QiLCIkY2FydFJ1bGVzVGFibGVSb3dUZW1wbGF0ZSIsImNhcnRSdWxlc1RhYmxlUm93VGVtcGxhdGUiLCJmaW5kIiwiY2FydFJ1bGVOYW1lRmllbGQiLCJjYXJ0UnVsZURlc2NyaXB0aW9uRmllbGQiLCJkZXNjcmlwdGlvbiIsImNhcnRSdWxlVmFsdWVGaWVsZCIsInZhbHVlIiwiY2FydFJ1bGVEZWxldGVCdG4iLCJfc2hvd0NhcnRSdWxlc0xpc3QiLCJjYXJ0UnVsZUVycm9yQmxvY2siLCJQcm9kdWN0UmVuZGVyZXIiLCIkcHJvZHVjdHNUYWJsZSIsInByb2R1Y3RzVGFibGUiLCJwcm9kdWN0cyIsIl9jbGVhblByb2R1Y3RzTGlzdCIsIl9oaWRlUHJvZHVjdHNMaXN0IiwiJHByb2R1Y3RzVGFibGVSb3dUZW1wbGF0ZSIsInByb2R1Y3RzVGFibGVSb3dUZW1wbGF0ZSIsInByb2R1Y3QiLCJwcm9kdWN0SW1hZ2VGaWVsZCIsImltYWdlTGluayIsInByb2R1Y3ROYW1lRmllbGQiLCJwcm9kdWN0QXR0ckZpZWxkIiwiYXR0cmlidXRlIiwicHJvZHVjdFJlZmVyZW5jZUZpZWxkIiwicmVmZXJlbmNlIiwicHJvZHVjdFVuaXRQcmljZUlucHV0IiwidW5pdFByaWNlIiwicHJvZHVjdFRvdGFsUHJpY2VGaWVsZCIsInByaWNlIiwicHJvZHVjdFJlbW92ZUJ0biIsInByb2R1Y3RJZCIsImF0dHJpYnV0ZUlkIiwiY3VzdG9taXphdGlvbklkIiwiX3Nob3dUYXhXYXJuaW5nIiwiX3Nob3dQcm9kdWN0c0xpc3QiLCJmb3VuZFByb2R1Y3RzIiwiX2NsZWFuU2VhcmNoUmVzdWx0cyIsIl9zaG93Tm90Rm91bmQiLCJfaGlkZVRheFdhcm5pbmciLCJfcmVuZGVyRm91bmRQcm9kdWN0cyIsIl9oaWRlTm90Rm91bmQiLCJfc2hvd1Jlc3VsdEJsb2NrIiwicmVuZGVyU3RvY2siLCJzdG9jayIsIl9yZW5kZXJDb21iaW5hdGlvbnMiLCJjb21iaW5hdGlvbnMiLCJfcmVuZGVyQ3VzdG9taXphdGlvbnMiLCJjdXN0b21pemF0aW9uX2ZpZWxkcyIsImluU3RvY2tDb3VudGVyIiwicXVhbnRpdHlJbnB1dCIsImF0dHIiLCJmb3JtYXR0ZWRfcHJpY2UiLCJwcm9kdWN0U2VsZWN0IiwicHJvZHVjdF9pZCIsImNvbWJpbmF0aW9uc1NlbGVjdCIsIl9jbGVhbkNvbWJpbmF0aW9ucyIsIl9oaWRlQ29tYmluYXRpb25zIiwiY29tYmluYXRpb24iLCJhdHRyaWJ1dGVfY29tYmluYXRpb25faWQiLCJfc2hvd0NvbWJpbmF0aW9ucyIsImN1c3RvbWl6YXRpb25GaWVsZHMiLCJmaWVsZFR5cGVGaWxlIiwiZmllbGRUeXBlVGV4dCIsIl9jbGVhbkN1c3RvbWl6YXRpb25zIiwiX2hpZGVDdXN0b21pemF0aW9ucyIsIiRjdXN0b21GaWVsZHNDb250YWluZXIiLCJwcm9kdWN0Q3VzdG9tRmllbGRzQ29udGFpbmVyIiwiJGZpbGVJbnB1dFRlbXBsYXRlIiwicHJvZHVjdEN1c3RvbUZpbGVUZW1wbGF0ZSIsIiR0ZXh0SW5wdXRUZW1wbGF0ZSIsInByb2R1Y3RDdXN0b21UZXh0VGVtcGxhdGUiLCJ0ZW1wbGF0ZVR5cGVNYXAiLCJjdXN0b21GaWVsZCIsInR5cGUiLCJwcm9kdWN0Q3VzdG9tSW5wdXQiLCJjdXN0b21pemF0aW9uX2ZpZWxkX2lkIiwicHJvZHVjdEN1c3RvbUlucHV0TGFiZWwiLCJfc2hvd0N1c3RvbWl6YXRpb25zIiwicHJvZHVjdEN1c3RvbWl6YXRpb25Db250YWluZXIiLCJwcm9kdWN0UmVzdWx0QmxvY2siLCJjb21iaW5hdGlvbnNSb3ciLCJwcm9kdWN0VGF4V2FybmluZyIsIm5vUHJvZHVjdHNGb3VuZFdhcm5pbmciLCJFdmVudEVtaXR0ZXIiLCJFdmVudEVtaXR0ZXJDbGFzcyIsIkNyZWF0ZU9yZGVyUGFnZSIsImNhcnRJZCIsIiRjb250YWluZXIiLCJvcmRlckNyZWF0aW9uQ29udGFpbmVyIiwiY2FydFByb3ZpZGVyIiwiQ2FydFByb3ZpZGVyIiwiY3VzdG9tZXJNYW5hZ2VyIiwiQ3VzdG9tZXJNYW5hZ2VyIiwic2hpcHBpbmdSZW5kZXJlciIsIlNoaXBwaW5nUmVuZGVyZXIiLCJhZGRyZXNzZXNSZW5kZXJlciIsIkFkZHJlc3Nlc1JlbmRlcmVyIiwiY2FydFJ1bGVzUmVuZGVyZXIiLCJyb3V0ZXIiLCJSb3V0ZXIiLCJjYXJ0RWRpdG9yIiwiQ2FydEVkaXRvciIsImNhcnRSdWxlTWFuYWdlciIsIkNhcnRSdWxlTWFuYWdlciIsInByb2R1Y3RNYW5hZ2VyIiwiUHJvZHVjdE1hbmFnZXIiLCJwcm9kdWN0UmVuZGVyZXIiLCJfaW5pdExpc3RlbmVycyIsIm9uIiwiY3VzdG9tZXJTZWFyY2hJbnB1dCIsIl9pbml0Q3VzdG9tZXJTZWFyY2giLCJlIiwiY2hvb3NlQ3VzdG9tZXJCdG4iLCJfaW5pdEN1c3RvbWVyU2VsZWN0IiwidXNlQ2FydEJ0biIsIl9pbml0Q2FydFNlbGVjdCIsInVzZU9yZGVyQnRuIiwiX2luaXREdXBsaWNhdGVPcmRlckNhcnQiLCJwcm9kdWN0U2VhcmNoIiwiX2luaXRQcm9kdWN0U2VhcmNoIiwiY2FydFJ1bGVTZWFyY2hJbnB1dCIsIl9pbml0Q2FydFJ1bGVTZWFyY2giLCJzdG9wU2VhcmNoaW5nIiwiX2luaXRDYXJ0RWRpdGluZyIsIl9vbkNhcnRMb2FkZWQiLCJfb25DYXJ0QWRkcmVzc2VzQ2hhbmdlZCIsImRlbGl2ZXJ5T3B0aW9uU2VsZWN0IiwiY2hhbmdlRGVsaXZlcnlPcHRpb24iLCJjdXJyZW50VGFyZ2V0IiwiZnJlZVNoaXBwaW5nU3dpdGNoIiwic2V0RnJlZVNoaXBwaW5nIiwiYWRkVG9DYXJ0QnV0dG9uIiwiYWRkUHJvZHVjdFRvQ2FydCIsImFkZHJlc3NTZWxlY3QiLCJfY2hhbmdlQ2FydEFkZHJlc3NlcyIsIl9pbml0UHJvZHVjdFJlbW92ZUZyb21DYXJ0IiwiX2FkZENhcnRSdWxlVG9DYXJ0IiwiX3JlbW92ZUNhcnRSdWxlRnJvbUNhcnQiLCJldmVudE1hcCIsImNhcnRMb2FkZWQiLCJjYXJ0SW5mbyIsIl9yZW5kZXJDYXJ0SW5mbyIsImxvYWRDdXN0b21lckNhcnRzIiwibG9hZEN1c3RvbWVyT3JkZXJzIiwiY2FydEFkZHJlc3Nlc0NoYW5nZWQiLCJyZW5kZXIiLCJhZGRyZXNzZXMiLCJzaGlwcGluZyIsImNhcnREZWxpdmVyeU9wdGlvbkNoYW5nZWQiLCJjYXJ0RnJlZVNoaXBwaW5nU2V0IiwiZXZlbnQiLCJzZXRUaW1lb3V0Iiwic2VhcmNoIiwidmFsIiwiY3VzdG9tZXJJZCIsInNlbGVjdEN1c3RvbWVyIiwibG9hZEVtcHR5Q2FydCIsImdldENhcnQiLCJvcmRlcklkIiwiZHVwbGljYXRlT3JkZXJDYXJ0Iiwic2VhcmNoUGhyYXNlIiwiZm91bmRDYXJ0UnVsZUxpc3RJdGVtIiwicHJldmVudERlZmF1bHQiLCJhZGRDYXJ0UnVsZVRvQ2FydCIsImJsdXIiLCJyZW1vdmVDYXJ0UnVsZUZyb21DYXJ0IiwiJHByb2R1Y3RTZWFyY2hJbnB1dCIsInJlbW92ZVByb2R1Y3RGcm9tQ2FydCIsInJlbmRlckNhcnRSdWxlc0Jsb2NrIiwicmVuZGVyTGlzdCIsImNhcnRCbG9jayIsImRlbGl2ZXJ5QWRkcmVzc0lkIiwiZGVsaXZlcnlBZGRyZXNzU2VsZWN0IiwiaW52b2ljZUFkZHJlc3NJZCIsImludm9pY2VBZGRyZXNzU2VsZWN0IiwiY2hhbmdlQ2FydEFkZHJlc3NlcyIsImN1c3RvbWVyU2VhcmNoUmVzdWx0c0Jsb2NrIiwiY3VzdG9tZXJTZWFyY2hSZXN1bHRUZW1wbGF0ZSIsImNoYW5nZUN1c3RvbWVyQnRuIiwiY3VzdG9tZXJTZWFyY2hSb3ciLCJub3RTZWxlY3RlZEN1c3RvbWVyU2VhcmNoUmVzdWx0cyIsImN1c3RvbWVyU2VhcmNoUmVzdWx0TmFtZSIsImN1c3RvbWVyU2VhcmNoUmVzdWx0RW1haWwiLCJjdXN0b21lclNlYXJjaFJlc3VsdElkIiwiY3VzdG9tZXJTZWFyY2hSZXN1bHRCaXJ0aGRheSIsImN1c3RvbWVyRGV0YWlsc0J0biIsImN1c3RvbWVyU2VhcmNoUmVzdWx0Q29sdW1uIiwiY3VzdG9tZXJTZWFyY2hCbG9jayIsImN1c3RvbWVyQ2FydHNUYWIiLCJjdXN0b21lck9yZGVyc1RhYiIsImN1c3RvbWVyQ2FydHNUYWJsZSIsImN1c3RvbWVyQ2FydHNUYWJsZVJvd1RlbXBsYXRlIiwiY3VzdG9tZXJDaGVja291dEhpc3RvcnkiLCJjdXN0b21lck9yZGVyc1RhYmxlIiwiY3VzdG9tZXJPcmRlcnNUYWJsZVJvd1RlbXBsYXRlIiwiY2FydERldGFpbHNCdG4iLCJjYXJ0SWRGaWVsZCIsImNhcnREYXRlRmllbGQiLCJjYXJ0VG90YWxGaWVsZCIsIm9yZGVyRGV0YWlsc0J0biIsIm9yZGVySWRGaWVsZCIsIm9yZGVyRGF0ZUZpZWxkIiwib3JkZXJQcm9kdWN0c0ZpZWxkIiwib3JkZXJUb3RhbEZpZWxkIiwib3JkZXJTdGF0dXNGaWVsZCIsImFkZHJlc3Nlc0Jsb2NrIiwiZGVsaXZlcnlBZGRyZXNzRGV0YWlscyIsImludm9pY2VBZGRyZXNzRGV0YWlscyIsImFkZHJlc3Nlc0NvbnRlbnQiLCJhZGRyZXNzZXNXYXJuaW5nIiwic3VtbWFyeUJsb2NrIiwic2hpcHBpbmdCbG9jayIsInNoaXBwaW5nRm9ybSIsIm5vQ2FycmllckJsb2NrIiwidG90YWxTaGlwcGluZ0ZpZWxkIiwiY29tYmluYXRpb25zVGVtcGxhdGUiLCJwcm9kdWN0U2VsZWN0Um93IiwicXVhbnRpdHlSb3ciLCJSb3V0aW5nIiwic2V0RGF0YSIsInJvdXRlcyIsInNldEJhc2VVcmwiLCJkb2N1bWVudCIsInJvdXRlIiwicGFyYW1zIiwidG9rZW5pemVkUGFyYW1zIiwiT2JqZWN0IiwiYXNzaWduIiwiX3Rva2VuIiwiZ2VuZXJhdGUiLCJyZWFkeSIsImRlbGl2ZXJ5QWRkcmVzc0RldGFpbHNDb250ZW50IiwiaW52b2ljZUFkZHJlc3NEZXRhaWxzQ29udGVudCIsIiRkZWxpdmVyeUFkZHJlc3NEZXRhaWxzIiwiY3JlYXRlT3JkZXJQYWdlTWFwIiwiJGludm9pY2VBZGRyZXNzRGV0YWlscyIsIiRkZWxpdmVyeUFkZHJlc3NTZWxlY3QiLCIkaW52b2ljZUFkZHJlc3NTZWxlY3QiLCIkYWRkcmVzc2VzQ29udGVudCIsIiRhZGRyZXNzZXNXYXJuaW5nQ29udGVudCIsImtleXMiLCJhZGRyZXNzIiwiZGVsaXZlcnlBZGRyZXNzT3B0aW9uIiwiYWRkcmVzc0lkIiwiYWxpYXMiLCJpbnZvaWNlQWRkcmVzc09wdGlvbiIsImRlbGl2ZXJ5IiwiZm9ybWF0dGVkQWRkcmVzcyIsInNlbGVjdGVkIiwiaW52b2ljZSIsIl9zaG93QWRkcmVzc2VzQmxvY2siLCJnZXQiLCJ0aGVuIiwiZW1pdCIsInBvc3QiLCJjdXN0b21lcl9pZCIsIiRzZWFyY2hJbnB1dCIsIl9zZWFyY2giLCJoaWRlUmVzdWx0c0Ryb3Bkb3duIiwiX29uQ2FydFJ1bGVTZWFyY2giLCJfb25BZGRDYXJ0UnVsZVRvQ2FydCIsIl9vbkFkZENhcnRSdWxlVG9DYXJ0RmFpbHVyZSIsIl9vblJlbW92ZUNhcnRSdWxlRnJvbUNhcnQiLCJjYXJ0UnVsZVNlYXJjaGVkIiwicmVuZGVyU2VhcmNoUmVzdWx0cyIsImNhcnRSdWxlQWRkZWQiLCJjYXJ0UnVsZUZhaWxlZFRvQWRkIiwiZGlzcGxheUVycm9yTWVzc2FnZSIsImNhcnRSdWxlUmVtb3ZlZCIsInNlYXJjaF9waHJhc2UiLCJjYXRjaCIsInNob3dFcnJvck1lc3NhZ2UiLCJyZXNwb25zZUpTT04iLCJhY3RpdmVTZWFyY2hSZXF1ZXN0IiwiJGN1c3RvbWVyU2VhcmNoUmVzdWx0QmxvY2siLCJjdXN0b21lclJlbmRlcmVyIiwiQ3VzdG9tZXJSZW5kZXJlciIsIl9zZWxlY3RDdXN0b21lciIsIl9sb2FkQ3VzdG9tZXJDYXJ0cyIsImN1cnJlbnRDYXJ0SWQiLCJfbG9hZEN1c3RvbWVyT3JkZXJzIiwiX2NoYW5nZUN1c3RvbWVyIiwiX29uQ3VzdG9tZXJTZWFyY2giLCJfb25DdXN0b21lclNlbGVjdCIsImN1c3RvbWVyU2VhcmNoZWQiLCJyZXNwb25zZSIsImN1c3RvbWVycyIsImN1c3RvbWVyU2VsZWN0ZWQiLCIkY2hvb3NlQnRuIiwiZGlzcGxheVNlbGVjdGVkQ3VzdG9tZXJCbG9jayIsInNob3dDdXN0b21lclNlYXJjaCIsInJlbmRlckNhcnRzIiwiY2FydHMiLCJyZW5kZXJPcmRlcnMiLCJvcmRlcnMiLCJjaG9vc2VDdXN0b21lckV2ZW50IiwiYWJvcnQiLCIkc2VhcmNoUmVxdWVzdCIsImN1c3RvbWVyX3NlYXJjaCIsInN0YXR1c1RleHQiLCJzZWxlY3RlZFByb2R1Y3RJZCIsInNlbGVjdGVkQ29tYmluYXRpb25JZCIsImFkZFByb2R1Y3QiLCJfZ2V0UHJvZHVjdERhdGEiLCJfaW5pdFByb2R1Y3RTZWxlY3QiLCJfaW5pdENvbWJpbmF0aW9uU2VsZWN0IiwiX29uUHJvZHVjdFNlYXJjaCIsIl9vbkFkZFByb2R1Y3RUb0NhcnQiLCJfb25SZW1vdmVQcm9kdWN0RnJvbUNhcnQiLCJwcm9kdWN0U2VhcmNoZWQiLCJKU09OIiwicGFyc2UiLCJfc2VsZWN0Rmlyc3RSZXN1bHQiLCJwcm9kdWN0QWRkZWRUb0NhcnQiLCJwcm9kdWN0UmVtb3ZlZEZyb21DYXJ0IiwiTnVtYmVyIiwiX3NlbGVjdFByb2R1Y3QiLCJjb21iaW5hdGlvbklkIiwiX3NlbGVjdENvbWJpbmF0aW9uIiwiX3Vuc2V0UHJvZHVjdCIsIl91bnNldENvbWJpbmF0aW9uIiwicmVuZGVyUHJvZHVjdE1ldGFkYXRhIiwiZm9ybURhdGEiLCJGb3JtRGF0YSIsIl9nZXRDdXN0b21GaWVsZHNEYXRhIiwiJGN1c3RvbUZpZWxkcyIsImVhY2giLCJmaWVsZCIsIiRmaWVsZCIsImZpbGVzIiwiJGZvcm0iLCIkbm9DYXJyaWVyQmxvY2siLCJzaGlwcGluZ0lzQXZhaWxhYmxlIiwiX2hpZGVDb250YWluZXIiLCJfZGlzcGxheUZvcm0iLCJfZGlzcGxheU5vQ2FycmllcnNXYXJuaW5nIiwiX2hpZGVOb0NhcnJpZXJCbG9jayIsIl9yZW5kZXJEZWxpdmVyeU9wdGlvbnMiLCJkZWxpdmVyeU9wdGlvbnMiLCJzZWxlY3RlZENhcnJpZXJJZCIsIl9yZW5kZXJUb3RhbFNoaXBwaW5nIiwic2hpcHBpbmdQcmljZSIsIl9zaG93Rm9ybSIsIl9zaG93Q29udGFpbmVyIiwiX2hpZGVGb3JtIiwiX3Nob3dOb0NhcnJpZXJCbG9jayIsInNlbGVjdGVkVmFsIiwiJGRlbGl2ZXJ5T3B0aW9uU2VsZWN0Iiwib3B0aW9uIiwiZGVsaXZlcnlPcHRpb24iLCJjYXJyaWVySWQiLCJjYXJyaWVyTmFtZSIsImNhcnJpZXJEZWxheSIsIiR0b3RhbFNoaXBwaW5nRmllbGQiLCJmcmVlU2hpcHBpbmciLCJhamF4IiwibWV0aG9kIiwicHJvY2Vzc0RhdGEiLCJjb250ZW50VHlwZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0EsbURBQTJDLGNBQWM7O0FBRXpEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7O0FBRUE7QUFDQTs7Ozs7Ozs7Ozs7Ozs7O3FqQkNoRUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkE7Ozs7Ozs7O0FBRUEsSUFBTUEsSUFBSUMsT0FBT0QsQ0FBakI7O0FBRUE7Ozs7SUFHcUJFLGlCO0FBQ25CLCtCQUFjO0FBQUE7O0FBQ1osU0FBS0MsZUFBTCxHQUF1QkgsRUFBRUkseUJBQWVDLGNBQWpCLENBQXZCO0FBQ0EsU0FBS0MsZUFBTCxHQUF1Qk4sRUFBRUkseUJBQWVHLGNBQWpCLENBQXZCO0FBQ0EsU0FBS0MsZ0JBQUwsR0FBd0JSLEVBQUVJLHlCQUFlSyx3QkFBakIsQ0FBeEI7QUFDRDs7QUFFRDs7Ozs7Ozs7Ozt5Q0FNcUJDLFMsRUFBV0MsUyxFQUFXO0FBQ3pDLFdBQUtDLGVBQUw7QUFDQTtBQUNBLFVBQUlELFNBQUosRUFBZTtBQUNiLGFBQUtFLG1CQUFMO0FBQ0E7QUFDRDtBQUNELFdBQUtDLG1CQUFMOztBQUVBO0FBQ0EsVUFBSUosVUFBVUssTUFBVixLQUFxQixDQUF6QixFQUE0QjtBQUMxQixhQUFLQyxrQkFB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Q0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsQ0FBQzs7QUFFRDs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQSxpQkFBaUIsc0JBQXNCO0FBQ3ZDOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGVBQWU7QUFDZjtBQUNBO0FBQ0E7QUFDQTtBQUNBLGNBQWM7QUFDZDs7QUFFQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBLG1CQUFtQixTQUFTO0FBQzVCO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEtBQUs7QUFDTDtBQUNBLEtBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLGlCQUFpQixzQkFBc0I7QUFDdkM7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0EsZUFBZTtBQUNmO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsT0FBTztBQUNQOztBQUVBLGlDQUFpQyxRQUFRO0FBQ3pDO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxTQUFTO0FBQ1Q7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxtQkFBbUIsaUJBQWlCO0FBQ3BDO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTs7QUFFQTtBQUNBO0FBQ0EsT0FBTztBQUNQO0FBQ0Esc0NBQXNDLFFBQVE7QUFDOUM7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtB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QXVCLDZCQUE2Qiw0QkFBNEIsNEJBQTRCLGlDQUFpQyw0QkFBNEIsMEJBQTBCLDRCQUE0QiwwQkFBMEIsMkJBQTJCLCtCQUErQiwwQkFBMEIsd0JBQXdCLHlCQUF5Qiw2QkFBNkIsdUNBQXVDLHlCQUF5QiwyQ0FBMkMsb0hBQW9ILCtGQUErRiw4Q0FBOEMsU0FBUywyQkFBMkIsZ0NBQWdDLGtEQUFrRCxpRkFBaUYsMEJBQTBCLCtCQUErQiwyQkFBMkIsY0FBYywrQkFBK0Isc0NBQXNDLDRDQUE0QyxzQkFBc0IscUJBQXFCLFFBQVEsb0JBQW9CLHFDQUFxQyxNQUFNLFNBQVMsaUNBQWlDLDZCQUE2QixLQUFLLFlBQVksd0VBQXdFLDZCQUE2QixXQUFXLGdEQUFnRCx3Q0FBd0MsS0FBSyx1QkFBdUIsT0FBTywrREFBK0Qsd0RBQXdELE1BQU0sa0VBQWtFLHVGQUF1RixzUEFBc1AseUJBQXlCLFFBQVEsc0dBQXNHLG1DQUFtQyxvQ0FBb0MsMENBQTBDLFNBQVMsMEJBQTBCLDJIQUEySCxzQkFBc0IsMENBQTBDLDJCIiwiZmlsZSI6Im9yZGVyX2NyZWF0ZS5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDM3Nyk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgOTNlN2NjMWY2YWNkMjQxMGYyNDIiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgY3JlYXRlT3JkZXJNYXAgZnJvbSAnLi9jcmVhdGUtb3JkZXItbWFwJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFJlbmRlcnMgY2FydCBydWxlcyAoY2FydFJ1bGVzKSBibG9ja1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDYXJ0UnVsZXNSZW5kZXJlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuJGNhcnRSdWxlc0Jsb2NrID0gJChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZXNCbG9jayk7XG4gICAgdGhpcy4kY2FydFJ1bGVzVGFibGUgPSAkKGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlc1RhYmxlKTtcbiAgICB0aGlzLiRzZWFyY2hSZXN1bHRCb3ggPSAkKGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlc1NlYXJjaFJlc3VsdEJveCk7XG4gIH1cblxuICAvKipcbiAgICogUmVzcG9uc2libGUgZm9yIHJlbmRlcmluZyBjYXJ0UnVsZXMgKGEuay5hIGNhcnQgcnVsZXMvZGlzY291bnRzKSBibG9ja1xuICAgKlxuICAgKiBAcGFyYW0ge0FycmF5fSBjYXJ0UnVsZXNcbiAgICogQHBhcmFtIHtCb29sZWFufSBlbXB0eUNhcnRcbiAgICovXG4gIHJlbmRlckNhcnRSdWxlc0Jsb2NrKGNhcnRSdWxlcywgZW1wdHlDYXJ0KSB7XG4gICAgdGhpcy5faGlkZUVycm9yQmxvY2soKTtcbiAgICAvLyBkbyBub3QgcmVuZGVyIGNhcnQgcnVsZXMgYmxvY2sgYXQgYWxsIGlmIGNhcnQgaGFzIG5vIHByb2R1Y3RzXG4gICAgaWYgKGVtcHR5Q2FydCkge1xuICAgICAgdGhpcy5faGlkZUNhcnRSdWxlc0Jsb2NrKCk7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIHRoaXMuX3Nob3dDYXJ0UnVsZXNCbG9jaygpO1xuXG4gICAgLy8gZG8gbm90IHJlbmRlciBjYXJ0IHJ1bGVzIGxpc3Qgd2hlbiB0aGVyZSBhcmUgbm8gY2FydCBydWxlc1xuICAgIGlmIChjYXJ0UnVsZXMubGVuZ3RoID09PSAwKSB7XG4gICAgICB0aGlzLl9oaWRlQ2FydFJ1bGVzTGlzdCgpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgdGhpcy5fcmVuZGVyTGlzdChjYXJ0UnVsZXMpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlc3BvbnNpYmxlIGZvciByZW5kZXJpbmcgc2VhcmNoIHJlc3VsdHMgZHJvcGRvd25cbiAgICpcbiAgICogQHBhcmFtIHNlYXJjaFJlc3VsdHNcbiAgICovXG4gIHJlbmRlclNlYXJjaFJlc3VsdHMoc2VhcmNoUmVzdWx0cykge1xuICAgIHRoaXMuX2NsZWFyU2VhcmNoUmVzdWx0cygpO1xuXG4gICAgaWYgKHNlYXJjaFJlc3VsdHMuY2FydF9ydWxlcy5sZW5ndGggPT09IDApIHtcbiAgICAgIHRoaXMuX3JlbmRlck5vdEZvdW5kKCk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMuX3JlbmRlckZvdW5kQ2FydFJ1bGVzKHNlYXJjaFJlc3VsdHMuY2FydF9ydWxlcyk7XG4gICAgfVxuXG4gICAgdGhpcy5fc2hvd1Jlc3VsdHNEcm9wZG93bigpO1xuICB9XG5cbiAgLyoqXG4gICAqIERpc3BsYXlzIGVycm9yIG1lc3NhZ2UgYmVsbG93IHNlYXJjaCBpbnB1dFxuICAgKlxuICAgKiBAcGFyYW0gbWVzc2FnZVxuICAgKi9cbiAgZGlzcGxheUVycm9yTWVzc2FnZShtZXNzYWdlKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZUVycm9yVGV4dCkudGV4dChtZXNzYWdlKTtcbiAgICB0aGlzLl9zaG93RXJyb3JCbG9jaygpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIGNhcnQgcnVsZXMgc2VhcmNoIHJlc3VsdCBkcm9wZG93blxuICAgKi9cbiAgaGlkZVJlc3VsdHNEcm9wZG93bigpIHtcbiAgICB0aGlzLiRzZWFyY2hSZXN1bHRCb3guYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIERpc3BsYXlzIGNhcnQgcnVsZXMgc2VhcmNoIHJlc3VsdCBkcm9wZG93blxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dSZXN1bHRzRHJvcGRvd24oKSB7XG4gICAgdGhpcy4kc2VhcmNoUmVzdWx0Qm94LnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIHdhcm5pbmcgdGhhdCBubyBjYXJ0IHJ1bGUgd2FzIGZvdW5kXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcmVuZGVyTm90Rm91bmQoKSB7XG4gICAgY29uc3QgJHRlbXBsYXRlID0gJCgkKGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlc05vdEZvdW5kVGVtcGxhdGUpLmh0bWwoKSkuY2xvbmUoKTtcbiAgICB0aGlzLiRzZWFyY2hSZXN1bHRCb3guaHRtbCgkdGVtcGxhdGUpO1xuICB9XG5cblxuICAvKipcbiAgICogRW1wdGllcyBjYXJ0IHJ1bGUgc2VhcmNoIHJlc3VsdHMgYmxvY2tcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9jbGVhclNlYXJjaFJlc3VsdHMoKSB7XG4gICAgdGhpcy4kc2VhcmNoUmVzdWx0Qm94LmVtcHR5KCk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBmb3VuZCBjYXJ0IHJ1bGVzIGFmdGVyIHNlYXJjaFxuICAgKlxuICAgKiBAcGFyYW0gY2FydFJ1bGVzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcmVuZGVyRm91bmRDYXJ0UnVsZXMoY2FydFJ1bGVzKSB7XG4gICAgY29uc3QgJGNhcnRSdWxlVGVtcGxhdGUgPSAkKCQoY3JlYXRlT3JkZXJNYXAuZm91bmRDYXJ0UnVsZVRlbXBsYXRlKS5odG1sKCkpO1xuICAgIGZvciAoY29uc3Qga2V5IGluIGNhcnRSdWxlcykge1xuICAgICAgY29uc3QgJHRlbXBsYXRlID0gJGNhcnRSdWxlVGVtcGxhdGUuY2xvbmUoKTtcbiAgICAgIGNvbnN0IGNhcnRSdWxlID0gY2FydFJ1bGVzW2tleV07XG5cbiAgICAgIGxldCBjYXJ0UnVsZU5hbWUgPSBjYXJ0UnVsZS5uYW1lO1xuICAgICAgaWYgKGNhcnRSdWxlLmNvZGUgIT09ICcnKSB7XG4gICAgICAgIGNhcnRSdWxlTmFtZSA9IGAke2NhcnRSdWxlLm5hbWV9IC0gJHtjYXJ0UnVsZS5jb2RlfWA7XG4gICAgICB9XG5cbiAgICAgICR0ZW1wbGF0ZS50ZXh0KGNhcnRSdWxlTmFtZSk7XG4gICAgICAkdGVtcGxhdGUuZGF0YSgnY2FydC1ydWxlLWlkJywgY2FydFJ1bGUuY2FydFJ1bGVJZCk7XG4gICAgICB0aGlzLiRzZWFyY2hSZXN1bHRCb3guYXBwZW5kKCR0ZW1wbGF0ZSk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIFJlc3BvbnNpYmxlIGZvciByZW5kZXJpbmcgdGhlIGxpc3Qgb2YgY2FydCBydWxlc1xuICAgKlxuICAgKiBAcGFyYW0ge0FycmF5fSBjYXJ0UnVsZXNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW5kZXJMaXN0KGNhcnRSdWxlcykge1xuICAgIHRoaXMuX2NsZWFuQ2FydFJ1bGVzTGlzdCgpO1xuICAgIGNvbnN0ICRjYXJ0UnVsZXNUYWJsZVJvd1RlbXBsYXRlID0gJCgkKGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlc1RhYmxlUm93VGVtcGxhdGUpLmh0bWwoKSk7XG5cbiAgICBmb3IgKGNvbnN0IGtleSBpbiBjYXJ0UnVsZXMpIHtcbiAgICAgIGNvbnN0IGNhcnRSdWxlID0gY2FydFJ1bGVzW2tleV07XG4gICAgICBjb25zdCAkdGVtcGxhdGUgPSAkY2FydFJ1bGVzVGFibGVSb3dUZW1wbGF0ZS5jbG9uZSgpO1xuXG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZU5hbWVGaWVsZCkudGV4dChjYXJ0UnVsZS5uYW1lKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlRGVzY3JpcHRpb25GaWVsZCkudGV4dChjYXJ0UnVsZS5kZXNjcmlwdGlvbik7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZVZhbHVlRmllbGQpLnRleHQoY2FydFJ1bGUudmFsdWUpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAuY2FydFJ1bGVEZWxldGVCdG4pLmRhdGEoJ2NhcnQtcnVsZS1pZCcsIGNhcnRSdWxlLmNhcnRSdWxlSWQpO1xuXG4gICAgICB0aGlzLiRjYXJ0UnVsZXNUYWJsZS5maW5kKCd0Ym9keScpLmFwcGVuZCgkdGVtcGxhdGUpO1xuICAgIH1cblxuICAgIHRoaXMuX3Nob3dDYXJ0UnVsZXNMaXN0KCk7XG4gIH1cblxuICAvKipcbiAgICogU2hvd3MgZXJyb3IgYmxvY2tcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93RXJyb3JCbG9jaygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlRXJyb3JCbG9jaykucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIGVycm9yIGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZUVycm9yQmxvY2soKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZUVycm9yQmxvY2spLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyBjYXJ0UnVsZXMgYmxvY2tcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93Q2FydFJ1bGVzQmxvY2soKSB7XG4gICAgdGhpcy4kY2FydFJ1bGVzQmxvY2sucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIGhpZGUgY2FydFJ1bGVzIGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZUNhcnRSdWxlc0Jsb2NrKCkge1xuICAgIHRoaXMuJGNhcnRSdWxlc0Jsb2NrLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNwbGF5IHRoZSBsaXN0IGJsb2NrIG9mIGNhcnQgcnVsZXNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93Q2FydFJ1bGVzTGlzdCgpIHtcbiAgICB0aGlzLiRjYXJ0UnVsZXNUYWJsZS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZSBsaXN0IGJsb2NrIG9mIGNhcnQgcnVsZXNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlQ2FydFJ1bGVzTGlzdCgpIHtcbiAgICB0aGlzLiRjYXJ0UnVsZXNUYWJsZS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogcmVtb3ZlIGl0ZW1zIGluIGNhcnQgcnVsZXMgbGlzdFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NsZWFuQ2FydFJ1bGVzTGlzdCgpIHtcbiAgICB0aGlzLiRjYXJ0UnVsZXNUYWJsZS5maW5kKCd0Ym9keScpLmVtcHR5KCk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LXJ1bGVzLXJlbmRlcmVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IGNyZWF0ZU9yZGVyTWFwIGZyb20gJy4vY3JlYXRlLW9yZGVyLW1hcCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUHJvZHVjdFJlbmRlcmVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy4kcHJvZHVjdHNUYWJsZSA9ICQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdHNUYWJsZSk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBjYXJ0IHByb2R1Y3RzIGxpc3RcbiAgICpcbiAgICogQHBhcmFtIHByb2R1Y3RzXG4gICAqL1xuICByZW5kZXJMaXN0KHByb2R1Y3RzKSB7XG4gICAgdGhpcy5fY2xlYW5Qcm9kdWN0c0xpc3QoKTtcblxuICAgIGlmIChwcm9kdWN0cy5sZW5ndGggPT09IDApIHtcbiAgICAgIHRoaXMuX2hpZGVQcm9kdWN0c0xpc3QoKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGNvbnN0ICRwcm9kdWN0c1RhYmxlUm93VGVtcGxhdGUgPSAkKCQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdHNUYWJsZVJvd1RlbXBsYXRlKS5odG1sKCkpO1xuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gcHJvZHVjdHMpIHtcbiAgICAgIGNvbnN0IHByb2R1Y3QgPSBwcm9kdWN0c1trZXldO1xuICAgICAgY29uc3QgJHRlbXBsYXRlID0gJHByb2R1Y3RzVGFibGVSb3dUZW1wbGF0ZS5jbG9uZSgpO1xuXG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5wcm9kdWN0SW1hZ2VGaWVsZCkudGV4dChwcm9kdWN0LmltYWdlTGluayk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5wcm9kdWN0TmFtZUZpZWxkKS50ZXh0KHByb2R1Y3QubmFtZSk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5wcm9kdWN0QXR0ckZpZWxkKS50ZXh0KHByb2R1Y3QuYXR0cmlidXRlKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RSZWZlcmVuY2VGaWVsZCkudGV4dChwcm9kdWN0LnJlZmVyZW5jZSk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5wcm9kdWN0VW5pdFByaWNlSW5wdXQpLnRleHQocHJvZHVjdC51bml0UHJpY2UpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdFRvdGFsUHJpY2VGaWVsZCkudGV4dChwcm9kdWN0LnByaWNlKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RSZW1vdmVCdG4pLmRhdGEoJ3Byb2R1Y3QtaWQnLCBwcm9kdWN0LnByb2R1Y3RJZCk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5wcm9kdWN0UmVtb3ZlQnRuKS5kYXRhKCdhdHRyaWJ1dGUtaWQnLCBwcm9kdWN0LmF0dHJpYnV0ZUlkKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RSZW1vdmVCdG4pLmRhdGEoJ2N1c3RvbWl6YXRpb24taWQnLCBwcm9kdWN0LmN1c3RvbWl6YXRpb25JZCk7XG5cbiAgICAgIHRoaXMuJHByb2R1Y3RzVGFibGUuZmluZCgndGJvZHknKS5hcHBlbmQoJHRlbXBsYXRlKTtcbiAgICB9XG5cbiAgICB0aGlzLl9zaG93VGF4V2FybmluZygpO1xuICAgIHRoaXMuX3Nob3dQcm9kdWN0c0xpc3QoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIGNhcnQgcHJvZHVjdHMgc2VhcmNoIHJlc3VsdHMgYmxvY2tcbiAgICpcbiAgICogQHBhcmFtIGZvdW5kUHJvZHVjdHNcbiAgICovXG4gIHJlbmRlclNlYXJjaFJlc3VsdHMoZm91bmRQcm9kdWN0cykge1xuICAgIHRoaXMuX2NsZWFuU2VhcmNoUmVzdWx0cygpO1xuICAgIGlmIChmb3VuZFByb2R1Y3RzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgdGhpcy5fc2hvd05vdEZvdW5kKCk7XG4gICAgICB0aGlzLl9oaWRlVGF4V2FybmluZygpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgdGhpcy5fcmVuZGVyRm91bmRQcm9kdWN0cyhmb3VuZFByb2R1Y3RzKTtcblxuICAgIHRoaXMuX2hpZGVOb3RGb3VuZCgpO1xuICAgIHRoaXMuX3Nob3dUYXhXYXJuaW5nKCk7XG4gICAgdGhpcy5fc2hvd1Jlc3VsdEJsb2NrKCk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBhdmFpbGFibGUgZmllbGRzIHJlbGF0ZWQgdG8gc2VsZWN0ZWQgcHJvZHVjdFxuICAgKlxuICAgKiBAcGFyYW0gcHJvZHVjdFxuICAgKi9cbiAgcmVuZGVyUHJvZHVjdE1ldGFkYXRhKHByb2R1Y3QpIHtcbiAgICB0aGlzLnJlbmRlclN0b2NrKHByb2R1Y3Quc3RvY2spO1xuICAgIHRoaXMuX3JlbmRlckNvbWJpbmF0aW9ucyhwcm9kdWN0LmNvbWJpbmF0aW9ucyk7XG4gICAgdGhpcy5fcmVuZGVyQ3VzdG9taXphdGlvbnMocHJvZHVjdC5jdXN0b21pemF0aW9uX2ZpZWxkcyk7XG4gIH1cblxuICAvKipcbiAgICogVXBkYXRlcyBzdG9jayB0ZXh0IGhlbHBlciB2YWx1ZVxuICAgKlxuICAgKiBAcGFyYW0gc3RvY2tcbiAgICovXG4gIHJlbmRlclN0b2NrKHN0b2NrKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5pblN0b2NrQ291bnRlcikudGV4dChzdG9jayk7XG4gICAgJChjcmVhdGVPcmRlck1hcC5xdWFudGl0eUlucHV0KS5hdHRyKCdtYXgnLCBzdG9jayk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBmb3VuZCBwcm9kdWN0cyBzZWxlY3RcbiAgICpcbiAgICogQHBhcmFtIGZvdW5kUHJvZHVjdHNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW5kZXJGb3VuZFByb2R1Y3RzKGZvdW5kUHJvZHVjdHMpIHtcbiAgICBmb3IgKGNvbnN0IGtleSBpbiBmb3VuZFByb2R1Y3RzKSB7XG4gICAgICBjb25zdCBwcm9kdWN0ID0gZm91bmRQcm9kdWN0c1trZXldO1xuXG4gICAgICBsZXQgbmFtZSA9IHByb2R1Y3QubmFtZTtcbiAgICAgIGlmIChwcm9kdWN0LmNvbWJpbmF0aW9ucy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgbmFtZSArPSBgIC0gJHtwcm9kdWN0LmZvcm1hdHRlZF9wcmljZX1gO1xuICAgICAgfVxuXG4gICAgICAkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RTZWxlY3QpLmFwcGVuZChgPG9wdGlvbiB2YWx1ZT1cIiR7cHJvZHVjdC5wcm9kdWN0X2lkfVwiPiR7bmFtZX08L29wdGlvbj5gKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogQ2xlYW5zIHByb2R1Y3Qgc2VhcmNoIHJlc3VsdCBmaWVsZHNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9jbGVhblNlYXJjaFJlc3VsdHMoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5wcm9kdWN0U2VsZWN0KS5lbXB0eSgpO1xuICAgICQoY3JlYXRlT3JkZXJNYXAuY29tYmluYXRpb25zU2VsZWN0KS5lbXB0eSgpO1xuICAgICQoY3JlYXRlT3JkZXJNYXAucXVhbnRpdHlJbnB1dCkuZW1wdHkoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIGNvbWJpbmF0aW9ucyByb3cgd2l0aCBzZWxlY3Qgb3B0aW9uc1xuICAgKlxuICAgKiBAcGFyYW0ge0FycmF5fSBjb21iaW5hdGlvbnNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW5kZXJDb21iaW5hdGlvbnMoY29tYmluYXRpb25zKSB7XG4gICAgdGhpcy5fY2xlYW5Db21iaW5hdGlvbnMoKTtcblxuICAgIGlmIChjb21iaW5hdGlvbnMubGVuZ3RoID09PSAwKSB7XG4gICAgICB0aGlzLl9oaWRlQ29tYmluYXRpb25zKCk7XG5cbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBmb3IgKGNvbnN0IGtleSBpbiBjb21iaW5hdGlvbnMpIHtcbiAgICAgIGNvbnN0IGNvbWJpbmF0aW9uID0gY29tYmluYXRpb25zW2tleV07XG5cbiAgICAgICQoY3JlYXRlT3JkZXJNYXAuY29tYmluYXRpb25zU2VsZWN0KS5hcHBlbmQoXG4gICAgICAgIGA8b3B0aW9uXG4gICAgICAgICAgdmFsdWU9XCIke2NvbWJpbmF0aW9uLmF0dHJpYnV0ZV9jb21iaW5hdGlvbl9pZH1cIj5cbiAgICAgICAgICAke2NvbWJpbmF0aW9uLmF0dHJpYnV0ZX0gLSAke2NvbWJpbmF0aW9uLmZvcm1hdHRlZF9wcmljZX1cbiAgICAgICAgPC9vcHRpb24+YCxcbiAgICAgICk7XG4gICAgfVxuXG4gICAgdGhpcy5fc2hvd0NvbWJpbmF0aW9ucygpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlc29sdmVzIHdlYXRoZXIgdG8gYWRkIGN1c3RvbWl6YXRpb24gZmllbGRzIHRvIHJlc3VsdCBibG9jayBhbmQgYWRkcyB0aGVtIGlmIG5lZWRlZFxuICAgKlxuICAgKiBAcGFyYW0gY3VzdG9taXphdGlvbkZpZWxkc1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbmRlckN1c3RvbWl6YXRpb25zKGN1c3RvbWl6YXRpb25GaWVsZHMpIHtcbiAgICAvLyByZXByZXNlbnRzIGN1c3RvbWl6YXRpb24gZmllbGQgdHlwZSBcImZpbGVcIi5cbiAgICBjb25zdCBmaWVsZFR5cGVGaWxlID0gMDtcbiAgICAvLyByZXByZXNlbnRzIGN1c3RvbWl6YXRpb24gZmllbGQgdHlwZSBcInRleHRcIi5cbiAgICBjb25zdCBmaWVsZFR5cGVUZXh0ID0gMTtcblxuICAgIHRoaXMuX2NsZWFuQ3VzdG9taXphdGlvbnMoKTtcbiAgICBpZiAoY3VzdG9taXphdGlvbkZpZWxkcy5sZW5ndGggPT09IDApIHtcbiAgICAgIHRoaXMuX2hpZGVDdXN0b21pemF0aW9ucygpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgY29uc3QgJGN1c3RvbUZpZWxkc0NvbnRhaW5lciA9ICQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdEN1c3RvbUZpZWxkc0NvbnRhaW5lcik7XG4gICAgY29uc3QgJGZpbGVJbnB1dFRlbXBsYXRlID0gJCgkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RDdXN0b21GaWxlVGVtcGxhdGUpLmh0bWwoKSk7XG4gICAgY29uc3QgJHRleHRJbnB1dFRlbXBsYXRlID0gJCgkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RDdXN0b21UZXh0VGVtcGxhdGUpLmh0bWwoKSk7XG5cbiAgICBjb25zdCB0ZW1wbGF0ZVR5cGVNYXAgPSB7XG4gICAgICBbZmllbGRUeXBlRmlsZV06ICRmaWxlSW5wdXRUZW1wbGF0ZSxcbiAgICAgIFtmaWVsZFR5cGVUZXh0XTogJHRleHRJbnB1dFRlbXBsYXRlLFxuICAgIH07XG5cbiAgICBmb3IgKGNvbnN0IGtleSBpbiBjdXN0b21pemF0aW9uRmllbGRzKSB7XG4gICAgICBjb25zdCBjdXN0b21GaWVsZCA9IGN1c3RvbWl6YXRpb25GaWVsZHNba2V5XTtcbiAgICAgIGNvbnN0ICR0ZW1wbGF0ZSA9IHRlbXBsYXRlVHlwZU1hcFtjdXN0b21GaWVsZC50eXBlXS5jbG9uZSgpO1xuXG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5wcm9kdWN0Q3VzdG9tSW5wdXQpXG4gICAgICAgIC5hdHRyKCduYW1lJywgYGN1c3RvbWl6YXRpb25zWyR7Y3VzdG9tRmllbGQuY3VzdG9taXphdGlvbl9maWVsZF9pZH1dYCk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5wcm9kdWN0Q3VzdG9tSW5wdXRMYWJlbClcbiAgICAgICAgLmF0dHIoJ2ZvcicsIGBjdXN0b21pemF0aW9uc1ske2N1c3RvbUZpZWxkLmN1c3RvbWl6YXRpb25fZmllbGRfaWR9XWApXG4gICAgICAgIC50ZXh0KGN1c3RvbUZpZWxkLm5hbWUpO1xuXG4gICAgICAkY3VzdG9tRmllbGRzQ29udGFpbmVyLmFwcGVuZCgkdGVtcGxhdGUpO1xuICAgIH1cblxuICAgIHRoaXMuX3Nob3dDdXN0b21pemF0aW9ucygpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzIHByb2R1Y3QgY3VzdG9taXphdGlvbiBjb250YWluZXJcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93Q3VzdG9taXphdGlvbnMoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5wcm9kdWN0Q3VzdG9taXphdGlvbkNvbnRhaW5lcikucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIHByb2R1Y3QgY3VzdG9taXphdGlvbiBjb250YWluZXJcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlQ3VzdG9taXphdGlvbnMoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5wcm9kdWN0Q3VzdG9taXphdGlvbkNvbnRhaW5lcikuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEVtcHRpZXMgY3VzdG9taXphdGlvbiBmaWVsZHMgY29udGFpbmVyXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfY2xlYW5DdXN0b21pemF0aW9ucygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RDdXN0b21GaWVsZHNDb250YWluZXIpLmVtcHR5KCk7XG4gIH1cblxuICAvKipcbiAgICogU2hvd3MgcmVzdWx0IGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd1Jlc3VsdEJsb2NrKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdFJlc3VsdEJsb2NrKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgcmVzdWx0IGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZVJlc3VsdEJsb2NrKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdFJlc3VsdEJsb2NrKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuXG4gIC8qKlxuICAgKiBTaG93cyBwcm9kdWN0cyBsaXN0XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd1Byb2R1Y3RzTGlzdCgpIHtcbiAgICB0aGlzLiRwcm9kdWN0c1RhYmxlLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlcyBwcm9kdWN0cyBsaXN0XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZVByb2R1Y3RzTGlzdCgpIHtcbiAgICB0aGlzLiRwcm9kdWN0c1RhYmxlLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbXB0aWVzIHByb2R1Y3RzIGxpc3RcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9jbGVhblByb2R1Y3RzTGlzdCgpIHtcbiAgICB0aGlzLiRwcm9kdWN0c1RhYmxlLmZpbmQoJ3Rib2R5JykuZW1wdHkoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbXB0aWVzIGNvbWJpbmF0aW9ucyBzZWxlY3RcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9jbGVhbkNvbWJpbmF0aW9ucygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmNvbWJpbmF0aW9uc1NlbGVjdCkuZW1wdHkoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyBjb21iaW5hdGlvbnMgcm93XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd0NvbWJpbmF0aW9ucygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmNvbWJpbmF0aW9uc1JvdykucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIGNvbWJpbmF0aW9ucyByb3dcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlQ29tYmluYXRpb25zKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuY29tYmluYXRpb25zUm93KS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogU2hvd3Mgd2FybmluZyBvZiB0YXggaW5jbHVkZWQvZXhjbHVkZWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93VGF4V2FybmluZygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RUYXhXYXJuaW5nKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgd2FybmluZyBvZiB0YXggaW5jbHVkZWQvZXhjbHVkZWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlVGF4V2FybmluZygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RUYXhXYXJuaW5nKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogU2hvd3MgcHJvZHVjdCBub3QgZm91bmQgd2FybmluZ1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dOb3RGb3VuZCgpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLm5vUHJvZHVjdHNGb3VuZFdhcm5pbmcpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlcyBwcm9kdWN0IG5vdCBmb3VuZCB3YXJuaW5nXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZU5vdEZvdW5kKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAubm9Qcm9kdWN0c0ZvdW5kV2FybmluZykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvcHJvZHVjdC1yZW5kZXJlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBFdmVudEVtaXR0ZXJDbGFzcyBmcm9tICdldmVudHMnO1xuXG4vKipcbiAqIFdlIGluc3RhbmNpYXRlIG9uZSBFdmVudEVtaXR0ZXIgKHJlc3RyaWN0ZWQgdmlhIGEgY29uc3QpIHNvIHRoYXQgZXZlcnkgY29tcG9uZW50c1xuICogcmVnaXN0ZXIvZGlzcGF0Y2ggb24gdGhlIHNhbWUgb25lIGFuZCBjYW4gY29tbXVuaWNhdGUgd2l0aCBlYWNoIG90aGVyLlxuICovXG5leHBvcnQgY29uc3QgRXZlbnRFbWl0dGVyID0gbmV3IEV2ZW50RW1pdHRlckNsYXNzKCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2V2ZW50LWVtaXR0ZXIuanMiLCIvLyBDb3B5cmlnaHQgSm95ZW50LCBJbmMuIGFuZCBvdGhlciBOb2RlIGNvbnRyaWJ1dG9ycy5cbi8vXG4vLyBQZXJtaXNzaW9uIGlzIGhlcmVieSBncmFudGVkLCBmcmVlIG9mIGNoYXJnZSwgdG8gYW55IHBlcnNvbiBvYnRhaW5pbmcgYVxuLy8gY29weSBvZiB0aGlzIHNvZnR3YXJlIGFuZCBhc3NvY2lhdGVkIGRvY3VtZW50YXRpb24gZmlsZXMgKHRoZVxuLy8gXCJTb2Z0d2FyZVwiKSwgdG8gZGVhbCBpbiB0aGUgU29mdHdhcmUgd2l0aG91dCByZXN0cmljdGlvbiwgaW5jbHVkaW5nXG4vLyB3aXRob3V0IGxpbWl0YXRpb24gdGhlIHJpZ2h0cyB0byB1c2UsIGNvcHksIG1vZGlmeSwgbWVyZ2UsIHB1Ymxpc2gsXG4vLyBkaXN0cmlidXRlLCBzdWJsaWNlbnNlLCBhbmQvb3Igc2VsbCBjb3BpZXMgb2YgdGhlIFNvZnR3YXJlLCBhbmQgdG8gcGVybWl0XG4vLyBwZXJzb25zIHRvIHdob20gdGhlIFNvZnR3YXJlIGlzIGZ1cm5pc2hlZCB0byBkbyBzbywgc3ViamVjdCB0byB0aGVcbi8vIGZvbGxvd2luZyBjb25kaXRpb25zOlxuLy9cbi8vIFRoZSBhYm92ZSBjb3B5cmlnaHQgbm90aWNlIGFuZCB0aGlzIHBlcm1pc3Npb24gbm90aWNlIHNoYWxsIGJlIGluY2x1ZGVkXG4vLyBpbiBhbGwgY29waWVzIG9yIHN1YnN0YW50aWFsIHBvcnRpb25zIG9mIHRoZSBTb2Z0d2FyZS5cbi8vXG4vLyBUSEUgU09GVFdBUkUgSVMgUFJPVklERUQgXCJBUyBJU1wiLCBXSVRIT1VUIFdBUlJBTlRZIE9GIEFOWSBLSU5ELCBFWFBSRVNTXG4vLyBPUiBJTVBMSUVELCBJTkNMVURJTkcgQlVUIE5PVCBMSU1JVEVEIFRPIFRIRSBXQVJSQU5USUVTIE9GXG4vLyBNRVJDSEFOVEFCSUxJVFksIEZJVE5FU1MgRk9SIEEgUEFSVElDVUxBUiBQVVJQT1NFIEFORCBOT05JTkZSSU5HRU1FTlQuIElOXG4vLyBOTyBFVkVOVCBTSEFMTCBUSEUgQVVUSE9SUyBPUiBDT1BZUklHSFQgSE9MREVSUyBCRSBMSUFCTEUgRk9SIEFOWSBDTEFJTSxcbi8vIERBTUFHRVMgT1IgT1RIRVIgTElBQklMSVRZLCBXSEVUSEVSIElOIEFOIEFDVElPTiBPRiBDT05UUkFDVCwgVE9SVCBPUlxuLy8gT1RIRVJXSVNFLCBBUklTSU5HIEZST00sIE9VVCBPRiBPUiBJTiBDT05ORUNUSU9OIFdJVEggVEhFIFNPRlRXQVJFIE9SIFRIRVxuLy8gVVNFIE9SIE9USEVSIERFQUxJTkdTIElOIFRIRSBTT0ZUV0FSRS5cblxuJ3VzZSBzdHJpY3QnO1xuXG52YXIgUiA9IHR5cGVvZiBSZWZsZWN0ID09PSAnb2JqZWN0JyA/IFJlZmxlY3QgOiBudWxsXG52YXIgUmVmbGVjdEFwcGx5ID0gUiAmJiB0eXBlb2YgUi5hcHBseSA9PT0gJ2Z1bmN0aW9uJ1xuICA/IFIuYXBwbHlcbiAgOiBmdW5jdGlvbiBSZWZsZWN0QXBwbHkodGFyZ2V0LCByZWNlaXZlciwgYXJncykge1xuICAgIHJldHVybiBGdW5jdGlvbi5wcm90b3R5cGUuYXBwbHkuY2FsbCh0YXJnZXQsIHJlY2VpdmVyLCBhcmdzKTtcbiAgfVxuXG52YXIgUmVmbGVjdE93bktleXNcbmlmIChSICYmIHR5cGVvZiBSLm93bktleXMgPT09ICdmdW5jdGlvbicpIHtcbiAgUmVmbGVjdE93bktleXMgPSBSLm93bktleXNcbn0gZWxzZSBpZiAoT2JqZWN0LmdldE93blByb3BlcnR5U3ltYm9scykge1xuICBSZWZsZWN0T3duS2V5cyA9IGZ1bmN0aW9uIFJlZmxlY3RPd25LZXlzKHRhcmdldCkge1xuICAgIHJldHVybiBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyh0YXJnZXQpXG4gICAgICAuY29uY2F0KE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHModGFyZ2V0KSk7XG4gIH07XG59IGVsc2Uge1xuICBSZWZsZWN0T3duS2V5cyA9IGZ1bmN0aW9uIFJlZmxlY3RPd25LZXlzKHRhcmdldCkge1xuICAgIHJldHVybiBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyh0YXJnZXQpO1xuICB9O1xufVxuXG5mdW5jdGlvbiBQcm9jZXNzRW1pdFdhcm5pbmcod2FybmluZykge1xuICBpZiAoY29uc29sZSAmJiBjb25zb2xlLndhcm4pIGNvbnNvbGUud2Fybih3YXJuaW5nKTtcbn1cblxudmFyIE51bWJlcklzTmFOID0gTnVtYmVyLmlzTmFOIHx8IGZ1bmN0aW9uIE51bWJlcklzTmFOKHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZSAhPT0gdmFsdWU7XG59XG5cbmZ1bmN0aW9uIEV2ZW50RW1pdHRlcigpIHtcbiAgRXZlbnRFbWl0dGVyLmluaXQuY2FsbCh0aGlzKTtcbn1cbm1vZHVsZS5leHBvcnRzID0gRXZlbnRFbWl0dGVyO1xuXG4vLyBCYWNrd2FyZHMtY29tcGF0IHdpdGggbm9kZSAwLjEwLnhcbkV2ZW50RW1pdHRlci5FdmVudEVtaXR0ZXIgPSBFdmVudEVtaXR0ZXI7XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuX2V2ZW50cyA9IHVuZGVmaW5lZDtcbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuX2V2ZW50c0NvdW50ID0gMDtcbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuX21heExpc3RlbmVycyA9IHVuZGVmaW5lZDtcblxuLy8gQnkgZGVmYXVsdCBFdmVudEVtaXR0ZXJzIHdpbGwgcHJpbnQgYSB3YXJuaW5nIGlmIG1vcmUgdGhhbiAxMCBsaXN0ZW5lcnMgYXJlXG4vLyBhZGRlZCB0byBpdC4gVGhpcyBpcyBhIHVzZWZ1bCBkZWZhdWx0IHdoaWNoIGhlbHBzIGZpbmRpbmcgbWVtb3J5IGxlYWtzLlxudmFyIGRlZmF1bHRNYXhMaXN0ZW5lcnMgPSAxMDtcblxuT2JqZWN0LmRlZmluZVByb3BlcnR5KEV2ZW50RW1pdHRlciwgJ2RlZmF1bHRNYXhMaXN0ZW5lcnMnLCB7XG4gIGVudW1lcmFibGU6IHRydWUsXG4gIGdldDogZnVuY3Rpb24oKSB7XG4gICAgcmV0dXJuIGRlZmF1bHRNYXhMaXN0ZW5lcnM7XG4gIH0sXG4gIHNldDogZnVuY3Rpb24oYXJnKSB7XG4gICAgaWYgKHR5cGVvZiBhcmcgIT09ICdudW1iZXInIHx8IGFyZyA8IDAgfHwgTnVtYmVySXNOYU4oYXJnKSkge1xuICAgICAgdGhyb3cgbmV3IFJhbmdlRXJyb3IoJ1RoZSB2YWx1ZSBvZiBcImRlZmF1bHRNYXhMaXN0ZW5lcnNcIiBpcyBvdXQgb2YgcmFuZ2UuIEl0IG11c3QgYmUgYSBub24tbmVnYXRpdmUgbnVtYmVyLiBSZWNlaXZlZCAnICsgYXJnICsgJy4nKTtcbiAgICB9XG4gICAgZGVmYXVsdE1heExpc3RlbmVycyA9IGFyZztcbiAgfVxufSk7XG5cbkV2ZW50RW1pdHRlci5pbml0ID0gZnVuY3Rpb24oKSB7XG5cbiAgaWYgKHRoaXMuX2V2ZW50cyA9PT0gdW5kZWZpbmVkIHx8XG4gICAgICB0aGlzLl9ldmVudHMgPT09IE9iamVjdC5nZXRQcm90b3R5cGVPZih0aGlzKS5fZXZlbnRzKSB7XG4gICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICB0aGlzLl9ldmVudHNDb3VudCA9IDA7XG4gIH1cblxuICB0aGlzLl9tYXhMaXN0ZW5lcnMgPSB0aGlzLl9tYXhMaXN0ZW5lcnMgfHwgdW5kZWZpbmVkO1xufTtcblxuLy8gT2J2aW91c2x5IG5vdCBhbGwgRW1pdHRlcnMgc2hvdWxkIGJlIGxpbWl0ZWQgdG8gMTAuIFRoaXMgZnVuY3Rpb24gYWxsb3dzXG4vLyB0aGF0IHRvIGJlIGluY3JlYXNlZC4gU2V0IHRvIHplcm8gZm9yIHVubGltaXRlZC5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuc2V0TWF4TGlzdGVuZXJzID0gZnVuY3Rpb24gc2V0TWF4TGlzdGVuZXJzKG4pIHtcbiAgaWYgKHR5cGVvZiBuICE9PSAnbnVtYmVyJyB8fCBuIDwgMCB8fCBOdW1iZXJJc05hTihuKSkge1xuICAgIHRocm93IG5ldyBSYW5nZUVycm9yKCdUaGUgdmFsdWUgb2YgXCJuXCIgaXMgb3V0IG9mIHJhbmdlLiBJdCBtdXN0IGJlIGEgbm9uLW5lZ2F0aXZlIG51bWJlci4gUmVjZWl2ZWQgJyArIG4gKyAnLicpO1xuICB9XG4gIHRoaXMuX21heExpc3RlbmVycyA9IG47XG4gIHJldHVybiB0aGlzO1xufTtcblxuZnVuY3Rpb24gJGdldE1heExpc3RlbmVycyh0aGF0KSB7XG4gIGlmICh0aGF0Ll9tYXhMaXN0ZW5lcnMgPT09IHVuZGVmaW5lZClcbiAgICByZXR1cm4gRXZlbnRFbWl0dGVyLmRlZmF1bHRNYXhMaXN0ZW5lcnM7XG4gIHJldHVybiB0aGF0Ll9tYXhMaXN0ZW5lcnM7XG59XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuZ2V0TWF4TGlzdGVuZXJzID0gZnVuY3Rpb24gZ2V0TWF4TGlzdGVuZXJzKCkge1xuICByZXR1cm4gJGdldE1heExpc3RlbmVycyh0aGlzKTtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuZW1pdCA9IGZ1bmN0aW9uIGVtaXQodHlwZSkge1xuICB2YXIgYXJncyA9IFtdO1xuICBmb3IgKHZhciBpID0gMTsgaSA8IGFyZ3VtZW50cy5sZW5ndGg7IGkrKykgYXJncy5wdXNoKGFyZ3VtZW50c1tpXSk7XG4gIHZhciBkb0Vycm9yID0gKHR5cGUgPT09ICdlcnJvcicpO1xuXG4gIHZhciBldmVudHMgPSB0aGlzLl9ldmVudHM7XG4gIGlmIChldmVudHMgIT09IHVuZGVmaW5lZClcbiAgICBkb0Vycm9yID0gKGRvRXJyb3IgJiYgZXZlbnRzLmVycm9yID09PSB1bmRlZmluZWQpO1xuICBlbHNlIGlmICghZG9FcnJvcilcbiAgICByZXR1cm4gZmFsc2U7XG5cbiAgLy8gSWYgdGhlcmUgaXMgbm8gJ2Vycm9yJyBldmVudCBsaXN0ZW5lciB0aGVuIHRocm93LlxuICBpZiAoZG9FcnJvcikge1xuICAgIHZhciBlcjtcbiAgICBpZiAoYXJncy5sZW5ndGggPiAwKVxuICAgICAgZXIgPSBhcmdzWzBdO1xuICAgIGlmIChlciBpbnN0YW5jZW9mIEVycm9yKSB7XG4gICAgICAvLyBOb3RlOiBUaGUgY29tbWVudHMgb24gdGhlIGB0aHJvd2AgbGluZXMgYXJlIGludGVudGlvbmFsLCB0aGV5IHNob3dcbiAgICAgIC8vIHVwIGluIE5vZGUncyBvdXRwdXQgaWYgdGhpcyByZXN1bHRzIGluIGFuIHVuaGFuZGxlZCBleGNlcHRpb24uXG4gICAgICB0aHJvdyBlcjsgLy8gVW5oYW5kbGVkICdlcnJvcicgZXZlbnRcbiAgICB9XG4gICAgLy8gQXQgbGVhc3QgZ2l2ZSBzb21lIGtpbmQgb2YgY29udGV4dCB0byB0aGUgdXNlclxuICAgIHZhciBlcnIgPSBuZXcgRXJyb3IoJ1VuaGFuZGxlZCBlcnJvci4nICsgKGVyID8gJyAoJyArIGVyLm1lc3NhZ2UgKyAnKScgOiAnJykpO1xuICAgIGVyci5jb250ZXh0ID0gZXI7XG4gICAgdGhyb3cgZXJyOyAvLyBVbmhhbmRsZWQgJ2Vycm9yJyBldmVudFxuICB9XG5cbiAgdmFyIGhhbmRsZXIgPSBldmVudHNbdHlwZV07XG5cbiAgaWYgKGhhbmRsZXIgPT09IHVuZGVmaW5lZClcbiAgICByZXR1cm4gZmFsc2U7XG5cbiAgaWYgKHR5cGVvZiBoYW5kbGVyID09PSAnZnVuY3Rpb24nKSB7XG4gICAgUmVmbGVjdEFwcGx5KGhhbmRsZXIsIHRoaXMsIGFyZ3MpO1xuICB9IGVsc2Uge1xuICAgIHZhciBsZW4gPSBoYW5kbGVyLmxlbmd0aDtcbiAgICB2YXIgbGlzdGVuZXJzID0gYXJyYXlDbG9uZShoYW5kbGVyLCBsZW4pO1xuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgbGVuOyArK2kpXG4gICAgICBSZWZsZWN0QXBwbHkobGlzdGVuZXJzW2ldLCB0aGlzLCBhcmdzKTtcbiAgfVxuXG4gIHJldHVybiB0cnVlO1xufTtcblxuZnVuY3Rpb24gX2FkZExpc3RlbmVyKHRhcmdldCwgdHlwZSwgbGlzdGVuZXIsIHByZXBlbmQpIHtcbiAgdmFyIG07XG4gIHZhciBldmVudHM7XG4gIHZhciBleGlzdGluZztcblxuICBpZiAodHlwZW9mIGxpc3RlbmVyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgdGhyb3cgbmV3IFR5cGVFcnJvcignVGhlIFwibGlzdGVuZXJcIiBhcmd1bWVudCBtdXN0IGJlIG9mIHR5cGUgRnVuY3Rpb24uIFJlY2VpdmVkIHR5cGUgJyArIHR5cGVvZiBsaXN0ZW5lcik7XG4gIH1cblxuICBldmVudHMgPSB0YXJnZXQuX2V2ZW50cztcbiAgaWYgKGV2ZW50cyA9PT0gdW5kZWZpbmVkKSB7XG4gICAgZXZlbnRzID0gdGFyZ2V0Ll9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgIHRhcmdldC5fZXZlbnRzQ291bnQgPSAwO1xuICB9IGVsc2Uge1xuICAgIC8vIFRvIGF2b2lkIHJlY3Vyc2lvbiBpbiB0aGUgY2FzZSB0aGF0IHR5cGUgPT09IFwibmV3TGlzdGVuZXJcIiEgQmVmb3JlXG4gICAgLy8gYWRkaW5nIGl0IHRvIHRoZSBsaXN0ZW5lcnMsIGZpcnN0IGVtaXQgXCJuZXdMaXN0ZW5lclwiLlxuICAgIGlmIChldmVudHMubmV3TGlzdGVuZXIgIT09IHVuZGVmaW5lZCkge1xuICAgICAgdGFyZ2V0LmVtaXQoJ25ld0xpc3RlbmVyJywgdHlwZSxcbiAgICAgICAgICAgICAgICAgIGxpc3RlbmVyLmxpc3RlbmVyID8gbGlzdGVuZXIubGlzdGVuZXIgOiBsaXN0ZW5lcik7XG5cbiAgICAgIC8vIFJlLWFzc2lnbiBgZXZlbnRzYCBiZWNhdXNlIGEgbmV3TGlzdGVuZXIgaGFuZGxlciBjb3VsZCBoYXZlIGNhdXNlZCB0aGVcbiAgICAgIC8vIHRoaXMuX2V2ZW50cyB0byBiZSBhc3NpZ25lZCB0byBhIG5ldyBvYmplY3RcbiAgICAgIGV2ZW50cyA9IHRhcmdldC5fZXZlbnRzO1xuICAgIH1cbiAgICBleGlzdGluZyA9IGV2ZW50c1t0eXBlXTtcbiAgfVxuXG4gIGlmIChleGlzdGluZyA9PT0gdW5kZWZpbmVkKSB7XG4gICAgLy8gT3B0aW1pemUgdGhlIGNhc2Ugb2Ygb25lIGxpc3RlbmVyLiBEb24ndCBuZWVkIHRoZSBleHRyYSBhcnJheSBvYmplY3QuXG4gICAgZXhpc3RpbmcgPSBldmVudHNbdHlwZV0gPSBsaXN0ZW5lcjtcbiAgICArK3RhcmdldC5fZXZlbnRzQ291bnQ7XG4gIH0gZWxzZSB7XG4gICAgaWYgKHR5cGVvZiBleGlzdGluZyA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgLy8gQWRkaW5nIHRoZSBzZWNvbmQgZWxlbWVudCwgbmVlZCB0byBjaGFuZ2UgdG8gYXJyYXkuXG4gICAgICBleGlzdGluZyA9IGV2ZW50c1t0eXBlXSA9XG4gICAgICAgIHByZXBlbmQgPyBbbGlzdGVuZXIsIGV4aXN0aW5nXSA6IFtleGlzdGluZywgbGlzdGVuZXJdO1xuICAgICAgLy8gSWYgd2UndmUgYWxyZWFkeSBnb3QgYW4gYXJyYXksIGp1c3QgYXBwZW5kLlxuICAgIH0gZWxzZSBpZiAocHJlcGVuZCkge1xuICAgICAgZXhpc3RpbmcudW5zaGlmdChsaXN0ZW5lcik7XG4gICAgfSBlbHNlIHtcbiAgICAgIGV4aXN0aW5nLnB1c2gobGlzdGVuZXIpO1xuICAgIH1cblxuICAgIC8vIENoZWNrIGZvciBsaXN0ZW5lciBsZWFrXG4gICAgbSA9ICRnZXRNYXhMaXN0ZW5lcnModGFyZ2V0KTtcbiAgICBpZiAobSA+IDAgJiYgZXhpc3RpbmcubGVuZ3RoID4gbSAmJiAhZXhpc3Rpbmcud2FybmVkKSB7XG4gICAgICBleGlzdGluZy53YXJuZWQgPSB0cnVlO1xuICAgICAgLy8gTm8gZXJyb3IgY29kZSBmb3IgdGhpcyBzaW5jZSBpdCBpcyBhIFdhcm5pbmdcbiAgICAgIC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBuby1yZXN0cmljdGVkLXN5bnRheFxuICAgICAgdmFyIHcgPSBuZXcgRXJyb3IoJ1Bvc3NpYmxlIEV2ZW50RW1pdHRlciBtZW1vcnkgbGVhayBkZXRlY3RlZC4gJyArXG4gICAgICAgICAgICAgICAgICAgICAgICAgIGV4aXN0aW5nLmxlbmd0aCArICcgJyArIFN0cmluZyh0eXBlKSArICcgbGlzdGVuZXJzICcgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICAnYWRkZWQuIFVzZSBlbWl0dGVyLnNldE1heExpc3RlbmVycygpIHRvICcgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICAnaW5jcmVhc2UgbGltaXQnKTtcbiAgICAgIHcubmFtZSA9ICdNYXhMaXN0ZW5lcnNFeGNlZWRlZFdhcm5pbmcnO1xuICAgICAgdy5lbWl0dGVyID0gdGFyZ2V0O1xuICAgICAgdy50eXBlID0gdHlwZTtcbiAgICAgIHcuY291bnQgPSBleGlzdGluZy5sZW5ndGg7XG4gICAgICBQcm9jZXNzRW1pdFdhcm5pbmcodyk7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIHRhcmdldDtcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5hZGRMaXN0ZW5lciA9IGZ1bmN0aW9uIGFkZExpc3RlbmVyKHR5cGUsIGxpc3RlbmVyKSB7XG4gIHJldHVybiBfYWRkTGlzdGVuZXIodGhpcywgdHlwZSwgbGlzdGVuZXIsIGZhbHNlKTtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUub24gPSBFdmVudEVtaXR0ZXIucHJvdG90eXBlLmFkZExpc3RlbmVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnByZXBlbmRMaXN0ZW5lciA9XG4gICAgZnVuY3Rpb24gcHJlcGVuZExpc3RlbmVyKHR5cGUsIGxpc3RlbmVyKSB7XG4gICAgICByZXR1cm4gX2FkZExpc3RlbmVyKHRoaXMsIHR5cGUsIGxpc3RlbmVyLCB0cnVlKTtcbiAgICB9O1xuXG5mdW5jdGlvbiBvbmNlV3JhcHBlcigpIHtcbiAgdmFyIGFyZ3MgPSBbXTtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCBhcmd1bWVudHMubGVuZ3RoOyBpKyspIGFyZ3MucHVzaChhcmd1bWVudHNbaV0pO1xuICBpZiAoIXRoaXMuZmlyZWQpIHtcbiAgICB0aGlzLnRhcmdldC5yZW1vdmVMaXN0ZW5lcih0aGlzLnR5cGUsIHRoaXMud3JhcEZuKTtcbiAgICB0aGlzLmZpcmVkID0gdHJ1ZTtcbiAgICBSZWZsZWN0QXBwbHkodGhpcy5saXN0ZW5lciwgdGhpcy50YXJnZXQsIGFyZ3MpO1xuICB9XG59XG5cbmZ1bmN0aW9uIF9vbmNlV3JhcCh0YXJnZXQsIHR5cGUsIGxpc3RlbmVyKSB7XG4gIHZhciBzdGF0ZSA9IHsgZmlyZWQ6IGZhbHNlLCB3cmFwRm46IHVuZGVmaW5lZCwgdGFyZ2V0OiB0YXJnZXQsIHR5cGU6IHR5cGUsIGxpc3RlbmVyOiBsaXN0ZW5lciB9O1xuICB2YXIgd3JhcHBlZCA9IG9uY2VXcmFwcGVyLmJpbmQoc3RhdGUpO1xuICB3cmFwcGVkLmxpc3RlbmVyID0gbGlzdGVuZXI7XG4gIHN0YXRlLndyYXBGbiA9IHdyYXBwZWQ7XG4gIHJldHVybiB3cmFwcGVkO1xufVxuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9uY2UgPSBmdW5jdGlvbiBvbmNlKHR5cGUsIGxpc3RlbmVyKSB7XG4gIGlmICh0eXBlb2YgbGlzdGVuZXIgIT09ICdmdW5jdGlvbicpIHtcbiAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdUaGUgXCJsaXN0ZW5lclwiIGFyZ3VtZW50IG11c3QgYmUgb2YgdHlwZSBGdW5jdGlvbi4gUmVjZWl2ZWQgdHlwZSAnICsgdHlwZW9mIGxpc3RlbmVyKTtcbiAgfVxuICB0aGlzLm9uKHR5cGUsIF9vbmNlV3JhcCh0aGlzLCB0eXBlLCBsaXN0ZW5lcikpO1xuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucHJlcGVuZE9uY2VMaXN0ZW5lciA9XG4gICAgZnVuY3Rpb24gcHJlcGVuZE9uY2VMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcikge1xuICAgICAgaWYgKHR5cGVvZiBsaXN0ZW5lciAhPT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdUaGUgXCJsaXN0ZW5lclwiIGFyZ3VtZW50IG11c3QgYmUgb2YgdHlwZSBGdW5jdGlvbi4gUmVjZWl2ZWQgdHlwZSAnICsgdHlwZW9mIGxpc3RlbmVyKTtcbiAgICAgIH1cbiAgICAgIHRoaXMucHJlcGVuZExpc3RlbmVyKHR5cGUsIF9vbmNlV3JhcCh0aGlzLCB0eXBlLCBsaXN0ZW5lcikpO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfTtcblxuLy8gRW1pdHMgYSAncmVtb3ZlTGlzdGVuZXInIGV2ZW50IGlmIGFuZCBvbmx5IGlmIHRoZSBsaXN0ZW5lciB3YXMgcmVtb3ZlZC5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlTGlzdGVuZXIgPVxuICAgIGZ1bmN0aW9uIHJlbW92ZUxpc3RlbmVyKHR5cGUsIGxpc3RlbmVyKSB7XG4gICAgICB2YXIgbGlzdCwgZXZlbnRzLCBwb3NpdGlvbiwgaSwgb3JpZ2luYWxMaXN0ZW5lcjtcblxuICAgICAgaWYgKHR5cGVvZiBsaXN0ZW5lciAhPT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdUaGUgXCJsaXN0ZW5lclwiIGFyZ3VtZW50IG11c3QgYmUgb2YgdHlwZSBGdW5jdGlvbi4gUmVjZWl2ZWQgdHlwZSAnICsgdHlwZW9mIGxpc3RlbmVyKTtcbiAgICAgIH1cblxuICAgICAgZXZlbnRzID0gdGhpcy5fZXZlbnRzO1xuICAgICAgaWYgKGV2ZW50cyA9PT0gdW5kZWZpbmVkKVxuICAgICAgICByZXR1cm4gdGhpcztcblxuICAgICAgbGlzdCA9IGV2ZW50c1t0eXBlXTtcbiAgICAgIGlmIChsaXN0ID09PSB1bmRlZmluZWQpXG4gICAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgICBpZiAobGlzdCA9PT0gbGlzdGVuZXIgfHwgbGlzdC5saXN0ZW5lciA9PT0gbGlzdGVuZXIpIHtcbiAgICAgICAgaWYgKC0tdGhpcy5fZXZlbnRzQ291bnQgPT09IDApXG4gICAgICAgICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICAgICAgZWxzZSB7XG4gICAgICAgICAgZGVsZXRlIGV2ZW50c1t0eXBlXTtcbiAgICAgICAgICBpZiAoZXZlbnRzLnJlbW92ZUxpc3RlbmVyKVxuICAgICAgICAgICAgdGhpcy5lbWl0KCdyZW1vdmVMaXN0ZW5lcicsIHR5cGUsIGxpc3QubGlzdGVuZXIgfHwgbGlzdGVuZXIpO1xuICAgICAgICB9XG4gICAgICB9IGVsc2UgaWYgKHR5cGVvZiBsaXN0ICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIHBvc2l0aW9uID0gLTE7XG5cbiAgICAgICAgZm9yIChpID0gbGlzdC5sZW5ndGggLSAxOyBpID49IDA7IGktLSkge1xuICAgICAgICAgIGlmIChsaXN0W2ldID09PSBsaXN0ZW5lciB8fCBsaXN0W2ldLmxpc3RlbmVyID09PSBsaXN0ZW5lcikge1xuICAgICAgICAgICAgb3JpZ2luYWxMaXN0ZW5lciA9IGxpc3RbaV0ubGlzdGVuZXI7XG4gICAgICAgICAgICBwb3NpdGlvbiA9IGk7XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBpZiAocG9zaXRpb24gPCAwKVxuICAgICAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgICAgIGlmIChwb3NpdGlvbiA9PT0gMClcbiAgICAgICAgICBsaXN0LnNoaWZ0KCk7XG4gICAgICAgIGVsc2Uge1xuICAgICAgICAgIHNwbGljZU9uZShsaXN0LCBwb3NpdGlvbik7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAobGlzdC5sZW5ndGggPT09IDEpXG4gICAgICAgICAgZXZlbnRzW3R5cGVdID0gbGlzdFswXTtcblxuICAgICAgICBpZiAoZXZlbnRzLnJlbW92ZUxpc3RlbmVyICE9PSB1bmRlZmluZWQpXG4gICAgICAgICAgdGhpcy5lbWl0KCdyZW1vdmVMaXN0ZW5lcicsIHR5cGUsIG9yaWdpbmFsTGlzdGVuZXIgfHwgbGlzdGVuZXIpO1xuICAgICAgfVxuXG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9mZiA9IEV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlTGlzdGVuZXI7XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlQWxsTGlzdGVuZXJzID1cbiAgICBmdW5jdGlvbiByZW1vdmVBbGxMaXN0ZW5lcnModHlwZSkge1xuICAgICAgdmFyIGxpc3RlbmVycywgZXZlbnRzLCBpO1xuXG4gICAgICBldmVudHMgPSB0aGlzLl9ldmVudHM7XG4gICAgICBpZiAoZXZlbnRzID09PSB1bmRlZmluZWQpXG4gICAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgICAvLyBub3QgbGlzdGVuaW5nIGZvciByZW1vdmVMaXN0ZW5lciwgbm8gbmVlZCB0byBlbWl0XG4gICAgICBpZiAoZXZlbnRzLnJlbW92ZUxpc3RlbmVyID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgaWYgKGFyZ3VtZW50cy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICB0aGlzLl9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgICAgICAgIHRoaXMuX2V2ZW50c0NvdW50ID0gMDtcbiAgICAgICAgfSBlbHNlIGlmIChldmVudHNbdHlwZV0gIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgIGlmICgtLXRoaXMuX2V2ZW50c0NvdW50ID09PSAwKVxuICAgICAgICAgICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICAgICAgICBlbHNlXG4gICAgICAgICAgICBkZWxldGUgZXZlbnRzW3R5cGVdO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgICAgfVxuXG4gICAgICAvLyBlbWl0IHJlbW92ZUxpc3RlbmVyIGZvciBhbGwgbGlzdGVuZXJzIG9uIGFsbCBldmVudHNcbiAgICAgIGlmIChhcmd1bWVudHMubGVuZ3RoID09PSAwKSB7XG4gICAgICAgIHZhciBrZXlzID0gT2JqZWN0LmtleXMoZXZlbnRzKTtcbiAgICAgICAgdmFyIGtleTtcbiAgICAgICAgZm9yIChpID0gMDsgaSA8IGtleXMubGVuZ3RoOyArK2kpIHtcbiAgICAgICAgICBrZXkgPSBrZXlzW2ldO1xuICAgICAgICAgIGlmIChrZXkgPT09ICdyZW1vdmVMaXN0ZW5lcicpIGNvbnRpbnVlO1xuICAgICAgICAgIHRoaXMucmVtb3ZlQWxsTGlzdGVuZXJzKGtleSk7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5yZW1vdmVBbGxMaXN0ZW5lcnMoJ3JlbW92ZUxpc3RlbmVyJyk7XG4gICAgICAgIHRoaXMuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgICAgIHRoaXMuX2V2ZW50c0NvdW50ID0gMDtcbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgICB9XG5cbiAgICAgIGxpc3RlbmVycyA9IGV2ZW50c1t0eXBlXTtcblxuICAgICAgaWYgKHR5cGVvZiBsaXN0ZW5lcnMgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgdGhpcy5yZW1vdmVMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcnMpO1xuICAgICAgfSBlbHNlIGlmIChsaXN0ZW5lcnMgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAvLyBMSUZPIG9yZGVyXG4gICAgICAgIGZvciAoaSA9IGxpc3RlbmVycy5sZW5ndGggLSAxOyBpID49IDA7IGktLSkge1xuICAgICAgICAgIHRoaXMucmVtb3ZlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXJzW2ldKTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9O1xuXG5mdW5jdGlvbiBfbGlzdGVuZXJzKHRhcmdldCwgdHlwZSwgdW53cmFwKSB7XG4gIHZhciBldmVudHMgPSB0YXJnZXQuX2V2ZW50cztcblxuICBpZiAoZXZlbnRzID09PSB1bmRlZmluZWQpXG4gICAgcmV0dXJuIFtdO1xuXG4gIHZhciBldmxpc3RlbmVyID0gZXZlbnRzW3R5cGVdO1xuICBpZiAoZXZsaXN0ZW5lciA9PT0gdW5kZWZpbmVkKVxuICAgIHJldHVybiBbXTtcblxuICBpZiAodHlwZW9mIGV2bGlzdGVuZXIgPT09ICdmdW5jdGlvbicpXG4gICAgcmV0dXJuIHVud3JhcCA/IFtldmxpc3RlbmVyLmxpc3RlbmVyIHx8IGV2bGlzdGVuZXJdIDogW2V2bGlzdGVuZXJdO1xuXG4gIHJldHVybiB1bndyYXAgP1xuICAgIHVud3JhcExpc3RlbmVycyhldmxpc3RlbmVyKSA6IGFycmF5Q2xvbmUoZXZsaXN0ZW5lciwgZXZsaXN0ZW5lci5sZW5ndGgpO1xufVxuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmxpc3RlbmVycyA9IGZ1bmN0aW9uIGxpc3RlbmVycyh0eXBlKSB7XG4gIHJldHVybiBfbGlzdGVuZXJzKHRoaXMsIHR5cGUsIHRydWUpO1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5yYXdMaXN0ZW5lcnMgPSBmdW5jdGlvbiByYXdMaXN0ZW5lcnModHlwZSkge1xuICByZXR1cm4gX2xpc3RlbmVycyh0aGlzLCB0eXBlLCBmYWxzZSk7XG59O1xuXG5FdmVudEVtaXR0ZXIubGlzdGVuZXJDb3VudCA9IGZ1bmN0aW9uKGVtaXR0ZXIsIHR5cGUpIHtcbiAgaWYgKHR5cGVvZiBlbWl0dGVyLmxpc3RlbmVyQ291bnQgPT09ICdmdW5jdGlvbicpIHtcbiAgICByZXR1cm4gZW1pdHRlci5saXN0ZW5lckNvdW50KHR5cGUpO1xuICB9IGVsc2Uge1xuICAgIHJldHVybiBsaXN0ZW5lckNvdW50LmNhbGwoZW1pdHRlciwgdHlwZSk7XG4gIH1cbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUubGlzdGVuZXJDb3VudCA9IGxpc3RlbmVyQ291bnQ7XG5mdW5jdGlvbiBsaXN0ZW5lckNvdW50KHR5cGUpIHtcbiAgdmFyIGV2ZW50cyA9IHRoaXMuX2V2ZW50cztcblxuICBpZiAoZXZlbnRzICE9PSB1bmRlZmluZWQpIHtcbiAgICB2YXIgZXZsaXN0ZW5lciA9IGV2ZW50c1t0eXBlXTtcblxuICAgIGlmICh0eXBlb2YgZXZsaXN0ZW5lciA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgcmV0dXJuIDE7XG4gICAgfSBlbHNlIGlmIChldmxpc3RlbmVyICE9PSB1bmRlZmluZWQpIHtcbiAgICAgIHJldHVybiBldmxpc3RlbmVyLmxlbmd0aDtcbiAgICB9XG4gIH1cblxuICByZXR1cm4gMDtcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5ldmVudE5hbWVzID0gZnVuY3Rpb24gZXZlbnROYW1lcygpIHtcbiAgcmV0dXJuIHRoaXMuX2V2ZW50c0NvdW50ID4gMCA/IFJlZmxlY3RPd25LZXlzKHRoaXMuX2V2ZW50cykgOiBbXTtcbn07XG5cbmZ1bmN0aW9uIGFycmF5Q2xvbmUoYXJyLCBuKSB7XG4gIHZhciBjb3B5ID0gbmV3IEFycmF5KG4pO1xuICBmb3IgKHZhciBpID0gMDsgaSA8IG47ICsraSlcbiAgICBjb3B5W2ldID0gYXJyW2ldO1xuICByZXR1cm4gY29weTtcbn1cblxuZnVuY3Rpb24gc3BsaWNlT25lKGxpc3QsIGluZGV4KSB7XG4gIGZvciAoOyBpbmRleCArIDEgPCBsaXN0Lmxlbmd0aDsgaW5kZXgrKylcbiAgICBsaXN0W2luZGV4XSA9IGxpc3RbaW5kZXggKyAxXTtcbiAgbGlzdC5wb3AoKTtcbn1cblxuZnVuY3Rpb24gdW53cmFwTGlzdGVuZXJzKGFycikge1xuICB2YXIgcmV0ID0gbmV3IEFycmF5KGFyci5sZW5ndGgpO1xuICBmb3IgKHZhciBpID0gMDsgaSA8IHJldC5sZW5ndGg7ICsraSkge1xuICAgIHJldFtpXSA9IGFycltpXS5saXN0ZW5lciB8fCBhcnJbaV07XG4gIH1cbiAgcmV0dXJuIHJldDtcbn1cblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9ldmVudHMvZXZlbnRzLmpzXG4vLyBtb2R1bGUgaWQgPSAxOFxuLy8gbW9kdWxlIGNodW5rcyA9IDEyIDE0IDMwIDMyIDMzIDM1IDQyIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IGNyZWF0ZU9yZGVyTWFwIGZyb20gJy4vY3JlYXRlLW9yZGVyLW1hcCc7XG5pbXBvcnQgQ3VzdG9tZXJNYW5hZ2VyIGZyb20gJy4vY3VzdG9tZXItbWFuYWdlcic7XG5pbXBvcnQgU2hpcHBpbmdSZW5kZXJlciBmcm9tICcuL3NoaXBwaW5nLXJlbmRlcmVyJztcbmltcG9ydCBDYXJ0UHJvdmlkZXIgZnJvbSAnLi9jYXJ0LXByb3ZpZGVyJztcbmltcG9ydCBBZGRyZXNzZXNSZW5kZXJlciBmcm9tICcuL2FkZHJlc3Nlcy1yZW5kZXJlcic7XG5pbXBvcnQgQ2FydFJ1bGVzUmVuZGVyZXIgZnJvbSAnLi9jYXJ0LXJ1bGVzLXJlbmRlcmVyJztcbmltcG9ydCBSb3V0ZXIgZnJvbSAnLi4vLi4vLi4vY29tcG9uZW50cy9yb3V0ZXInO1xuaW1wb3J0IHtFdmVudEVtaXR0ZXJ9IGZyb20gJy4uLy4uLy4uL2NvbXBvbmVudHMvZXZlbnQtZW1pdHRlcic7XG5pbXBvcnQgQ2FydEVkaXRvciBmcm9tICcuL2NhcnQtZWRpdG9yJztcbmltcG9ydCBldmVudE1hcCBmcm9tICcuL2V2ZW50LW1hcCc7XG5pbXBvcnQgQ2FydFJ1bGVNYW5hZ2VyIGZyb20gJy4vY2FydC1ydWxlLW1hbmFnZXInO1xuaW1wb3J0IFByb2R1Y3RNYW5hZ2VyIGZyb20gJy4vcHJvZHVjdC1tYW5hZ2VyJztcbmltcG9ydCBQcm9kdWN0UmVuZGVyZXIgZnJvbSAnLi9wcm9kdWN0LXJlbmRlcmVyJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFBhZ2UgT2JqZWN0IGZvciBcIkNyZWF0ZSBvcmRlclwiIHBhZ2VcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ3JlYXRlT3JkZXJQYWdlIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5jYXJ0SWQgPSBudWxsO1xuICAgIHRoaXMuJGNvbnRhaW5lciA9ICQoY3JlYXRlT3JkZXJNYXAub3JkZXJDcmVhdGlvbkNvbnRhaW5lcik7XG5cbiAgICB0aGlzLmNhcnRQcm92aWRlciA9IG5ldyBDYXJ0UHJvdmlkZXIoKTtcbiAgICB0aGlzLmN1c3RvbWVyTWFuYWdlciA9IG5ldyBDdXN0b21lck1hbmFnZXIoKTtcbiAgICB0aGlzLnNoaXBwaW5nUmVuZGVyZXIgPSBuZXcgU2hpcHBpbmdSZW5kZXJlcigpO1xuICAgIHRoaXMuYWRkcmVzc2VzUmVuZGVyZXIgPSBuZXcgQWRkcmVzc2VzUmVuZGVyZXIoKTtcbiAgICB0aGlzLmNhcnRSdWxlc1JlbmRlcmVyID0gbmV3IENhcnRSdWxlc1JlbmRlcmVyKCk7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gICAgdGhpcy5jYXJ0RWRpdG9yID0gbmV3IENhcnRFZGl0b3IoKTtcbiAgICB0aGlzLmNhcnRSdWxlTWFuYWdlciA9IG5ldyBDYXJ0UnVsZU1hbmFnZXIoKTtcbiAgICB0aGlzLnByb2R1Y3RNYW5hZ2VyID0gbmV3IFByb2R1Y3RNYW5hZ2VyKCk7XG4gICAgdGhpcy5wcm9kdWN0UmVuZGVyZXIgPSBuZXcgUHJvZHVjdFJlbmRlcmVyKCk7XG5cbiAgICB0aGlzLl9pbml0TGlzdGVuZXJzKCk7XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhbGl6ZXMgZXZlbnQgbGlzdGVuZXJzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdExpc3RlbmVycygpIHtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2lucHV0JywgY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJTZWFyY2hJbnB1dCwgZSA9PiB0aGlzLl9pbml0Q3VzdG9tZXJTZWFyY2goZSkpO1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2xpY2snLCBjcmVhdGVPcmRlck1hcC5jaG9vc2VDdXN0b21lckJ0biwgZSA9PiB0aGlzLl9pbml0Q3VzdG9tZXJTZWxlY3QoZSkpO1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2xpY2snLCBjcmVhdGVPcmRlck1hcC51c2VDYXJ0QnRuLCBlID0+IHRoaXMuX2luaXRDYXJ0U2VsZWN0KGUpKTtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgY3JlYXRlT3JkZXJNYXAudXNlT3JkZXJCdG4sIGUgPT4gdGhpcy5faW5pdER1cGxpY2F0ZU9yZGVyQ2FydChlKSk7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdpbnB1dCcsIGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RTZWFyY2gsIGUgPT4gdGhpcy5faW5pdFByb2R1Y3RTZWFyY2goZSkpO1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignaW5wdXQnLCBjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZVNlYXJjaElucHV0LCBlID0+IHRoaXMuX2luaXRDYXJ0UnVsZVNlYXJjaChlKSk7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdibHVyJywgY3JlYXRlT3JkZXJNYXAuY2FydFJ1bGVTZWFyY2hJbnB1dCwgKCkgPT4gdGhpcy5jYXJ0UnVsZU1hbmFnZXIuc3RvcFNlYXJjaGluZygpKTtcbiAgICB0aGlzLl9pbml0Q2FydEVkaXRpbmcoKTtcbiAgICB0aGlzLl9vbkNhcnRMb2FkZWQoKTtcbiAgICB0aGlzLl9vbkNhcnRBZGRyZXNzZXNDaGFuZ2VkKCk7XG4gIH1cblxuICAvKipcbiAgICogRGVsZWdhdGVzIGFjdGlvbnMgdG8gZXZlbnRzIGFzc29jaWF0ZWQgd2l0aCBjYXJ0IHVwZGF0ZSAoZS5nLiBjaGFuZ2UgY2FydCBhZGRyZXNzKVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRDYXJ0RWRpdGluZygpIHtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NoYW5nZScsIGNyZWF0ZU9yZGVyTWFwLmRlbGl2ZXJ5T3B0aW9uU2VsZWN0LCBlID0+XG4gICAgICB0aGlzLmNhcnRFZGl0b3IuY2hhbmdlRGVsaXZlcnlPcHRpb24odGhpcy5jYXJ0SWQsIGUuY3VycmVudFRhcmdldC52YWx1ZSlcbiAgICApO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjaGFuZ2UnLCBjcmVhdGVPcmRlck1hcC5mcmVlU2hpcHBpbmdTd2l0Y2gsIGUgPT5cbiAgICAgIHRoaXMuY2FydEVkaXRvci5zZXRGcmVlU2hpcHBpbmcodGhpcy5jYXJ0SWQsIGUuY3VycmVudFRhcmdldC52YWx1ZSlcbiAgICApO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsIGNyZWF0ZU9yZGVyTWFwLmFkZFRvQ2FydEJ1dHRvbiwgKCkgPT5cbiAgICAgIHRoaXMucHJvZHVjdE1hbmFnZXIuYWRkUHJvZHVjdFRvQ2FydCh0aGlzLmNhcnRJZClcbiAgICApO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjaGFuZ2UnLCBjcmVhdGVPcmRlck1hcC5hZGRyZXNzU2VsZWN0LCAoKSA9PiB0aGlzLl9jaGFuZ2VDYXJ0QWRkcmVzc2VzKCkpO1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2xpY2snLCBjcmVhdGVPcmRlck1hcC5wcm9kdWN0UmVtb3ZlQnRuLCBlID0+IHRoaXMuX2luaXRQcm9kdWN0UmVtb3ZlRnJvbUNhcnQoZSkpO1xuXG4gICAgdGhpcy5fYWRkQ2FydFJ1bGVUb0NhcnQoKTtcbiAgICB0aGlzLl9yZW1vdmVDYXJ0UnVsZUZyb21DYXJ0KCk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBmb3IgZXZlbnQgd2hlbiBjYXJ0IGlzIGxvYWRlZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uQ2FydExvYWRlZCgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAuY2FydExvYWRlZCwgKGNhcnRJbmZvKSA9PiB7XG4gICAgICB0aGlzLmNhcnRJZCA9IGNhcnRJbmZvLmNhcnRJZDtcbiAgICAgIHRoaXMuX3JlbmRlckNhcnRJbmZvKGNhcnRJbmZvKTtcbiAgICAgIHRoaXMuY3VzdG9tZXJNYW5hZ2VyLmxvYWRDdXN0b21lckNhcnRzKHRoaXMuY2FydElkKTtcbiAgICAgIHRoaXMuY3VzdG9tZXJNYW5hZ2VyLmxvYWRDdXN0b21lck9yZGVycygpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZm9yIGNhcnQgYWRkcmVzc2VzIHVwZGF0ZSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uQ2FydEFkZHJlc3Nlc0NoYW5nZWQoKSB7XG4gICAgRXZlbnRFbWl0dGVyLm9uKGV2ZW50TWFwLmNhcnRBZGRyZXNzZXNDaGFuZ2VkLCAoY2FydEluZm8pID0+IHtcbiAgICAgIHRoaXMuYWRkcmVzc2VzUmVuZGVyZXIucmVuZGVyKGNhcnRJbmZvLmFkZHJlc3Nlcyk7XG4gICAgICB0aGlzLnNoaXBwaW5nUmVuZGVyZXIucmVuZGVyKGNhcnRJbmZvLnNoaXBwaW5nLCBjYXJ0SW5mby5wcm9kdWN0cy5sZW5ndGggPT09IDApO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZm9yIGNhcnQgZGVsaXZlcnkgb3B0aW9uIHVwZGF0ZSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uRGVsaXZlcnlPcHRpb25DaGFuZ2VkKCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5jYXJ0RGVsaXZlcnlPcHRpb25DaGFuZ2VkLCAoY2FydEluZm8pID0+IHtcbiAgICAgIHRoaXMuc2hpcHBpbmdSZW5kZXJlci5yZW5kZXIoY2FydEluZm8uc2hpcHBpbmcsIGNhcnRJbmZvLnByb2R1Y3RzLmxlbmd0aCA9PT0gMCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBmb3IgY2FydCBmcmVlIHNoaXBwaW5nIHVwZGF0ZSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uRnJlZVNoaXBwaW5nQ2hhbmdlZCgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAuY2FydEZyZWVTaGlwcGluZ1NldCwgKGNhcnRJbmZvKSA9PiB7XG4gICAgICB0aGlzLnNoaXBwaW5nUmVuZGVyZXIucmVuZGVyKGNhcnRJbmZvLnNoaXBwaW5nLCBjYXJ0SW5mby5wcm9kdWN0cy5sZW5ndGggPT09IDApO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXQgY3VzdG9tZXIgc2VhcmNoaW5nXG4gICAqXG4gICAqIEBwYXJhbSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRDdXN0b21lclNlYXJjaChldmVudCkge1xuICAgIHNldFRpbWVvdXQoKCkgPT4gdGhpcy5jdXN0b21lck1hbmFnZXIuc2VhcmNoKCQoZXZlbnQuY3VycmVudFRhcmdldCkudmFsKCkpLCAzMDApO1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXQgc2VsZWN0aW5nIGN1c3RvbWVyIGZvciB3aGljaCBvcmRlciBpcyBiZWluZyBjcmVhdGVkXG4gICAqXG4gICAqIEBwYXJhbSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRDdXN0b21lclNlbGVjdChldmVudCkge1xuICAgIGNvbnN0IGN1c3RvbWVySWQgPSB0aGlzLmN1c3RvbWVyTWFuYWdlci5zZWxlY3RDdXN0b21lcihldmVudCk7XG4gICAgdGhpcy5jYXJ0UHJvdmlkZXIubG9hZEVtcHR5Q2FydChjdXN0b21lcklkKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0cyBzZWxlY3RpbmcgY2FydCB0byBsb2FkXG4gICAqXG4gICAqIEBwYXJhbSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRDYXJ0U2VsZWN0KGV2ZW50KSB7XG4gICAgY29uc3QgY2FydElkID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdjYXJ0LWlkJyk7XG4gICAgdGhpcy5jYXJ0UHJvdmlkZXIuZ2V0Q2FydChjYXJ0SWQpO1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRzIGR1cGxpY2F0aW5nIG9yZGVyIGNhcnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0RHVwbGljYXRlT3JkZXJDYXJ0KGV2ZW50KSB7XG4gICAgY29uc3Qgb3JkZXJJZCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnb3JkZXItaWQnKTtcbiAgICB0aGlzLmNhcnRQcm92aWRlci5kdXBsaWNhdGVPcmRlckNhcnQob3JkZXJJZCk7XG4gIH1cblxuICAvKipcbiAgICogVHJpZ2dlcnMgY2FydCBydWxlIHNlYXJjaGluZ1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRDYXJ0UnVsZVNlYXJjaChldmVudCkge1xuICAgIGNvbnN0IHNlYXJjaFBocmFzZSA9IGV2ZW50LmN1cnJlbnRUYXJnZXQudmFsdWU7XG4gICAgdGhpcy5jYXJ0UnVsZU1hbmFnZXIuc2VhcmNoKHNlYXJjaFBocmFzZSk7XG4gIH1cblxuICAvKipcbiAgICogVHJpZ2dlcnMgY2FydCBydWxlIHNlbGVjdFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2FkZENhcnRSdWxlVG9DYXJ0KCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignbW91c2Vkb3duJywgY3JlYXRlT3JkZXJNYXAuZm91bmRDYXJ0UnVsZUxpc3RJdGVtLCAoZXZlbnQpID0+IHtcbiAgICAgIC8vIHByZXZlbnQgYmx1ciBldmVudCB0byBhbGxvdyBzZWxlY3RpbmcgY2FydCBydWxlXG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgY29uc3QgY2FydFJ1bGVJZCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnY2FydC1ydWxlLWlkJyk7XG4gICAgICB0aGlzLmNhcnRSdWxlTWFuYWdlci5hZGRDYXJ0UnVsZVRvQ2FydChjYXJ0UnVsZUlkLCB0aGlzLmNhcnRJZCk7XG5cbiAgICAgIC8vIG1hbnVhbGx5IGZpcmUgYmx1ciBldmVudCBhZnRlciBjYXJ0IHJ1bGUgaXMgc2VsZWN0ZWQuXG4gICAgfSkub24oJ2NsaWNrJywgY3JlYXRlT3JkZXJNYXAuZm91bmRDYXJ0UnVsZUxpc3RJdGVtLCAoKSA9PiB7XG4gICAgICAkKGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlU2VhcmNoSW5wdXQpLmJsdXIoKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBUcmlnZ2VycyBjYXJ0IHJ1bGUgcmVtb3ZhbCBmcm9tIGNhcnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW1vdmVDYXJ0UnVsZUZyb21DYXJ0KCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2xpY2snLCBjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZURlbGV0ZUJ0biwgKGV2ZW50KSA9PiB7XG4gICAgICB0aGlzLmNhcnRSdWxlTWFuYWdlci5yZW1vdmVDYXJ0UnVsZUZyb21DYXJ0KCQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnY2FydC1ydWxlLWlkJyksIHRoaXMuY2FydElkKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0cyBwcm9kdWN0IHNlYXJjaGluZ1xuICAgKlxuICAgKiBAcGFyYW0gZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0UHJvZHVjdFNlYXJjaChldmVudCkge1xuICAgIGNvbnN0ICRwcm9kdWN0U2VhcmNoSW5wdXQgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgIGNvbnN0IHNlYXJjaFBocmFzZSA9ICRwcm9kdWN0U2VhcmNoSW5wdXQudmFsKCk7XG5cbiAgICBzZXRUaW1lb3V0KCgpID0+IHRoaXMucHJvZHVjdE1hbmFnZXIuc2VhcmNoKHNlYXJjaFBocmFzZSksIDMwMCk7XG4gIH1cblxuICAvKipcbiAgICogSW5pdHMgcHJvZHVjdCByZW1vdmluZyBmcm9tIGNhcnRcbiAgICpcbiAgICogQHBhcmFtIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdFByb2R1Y3RSZW1vdmVGcm9tQ2FydChldmVudCkge1xuICAgIGNvbnN0IHByb2R1Y3QgPSB7XG4gICAgICBwcm9kdWN0SWQ6ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgncHJvZHVjdC1pZCcpLFxuICAgICAgYXR0cmlidXRlSWQ6ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnYXR0cmlidXRlLWlkJyksXG4gICAgICBjdXN0b21pemF0aW9uSWQ6ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnY3VzdG9taXphdGlvbi1pZCcpLFxuICAgIH07XG5cbiAgICB0aGlzLnByb2R1Y3RNYW5hZ2VyLnJlbW92ZVByb2R1Y3RGcm9tQ2FydCh0aGlzLmNhcnRJZCwgcHJvZHVjdCk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBjYXJ0IHN1bW1hcnkgb24gdGhlIHBhZ2VcbiAgICpcbiAgICogQHBhcmFtIHtPYmplY3R9IGNhcnRJbmZvXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcmVuZGVyQ2FydEluZm8oY2FydEluZm8pIHtcbiAgICB0aGlzLmFkZHJlc3Nlc1JlbmRlcmVyLnJlbmRlcihjYXJ0SW5mby5hZGRyZXNzZXMpO1xuICAgIHRoaXMuY2FydFJ1bGVzUmVuZGVyZXIucmVuZGVyQ2FydFJ1bGVzQmxvY2soY2FydEluZm8uY2FydFJ1bGVzLCBjYXJ0SW5mby5wcm9kdWN0cy5sZW5ndGggPT09IDApO1xuICAgIHRoaXMuc2hpcHBpbmdSZW5kZXJlci5yZW5kZXIoY2FydEluZm8uc2hpcHBpbmcsIGNhcnRJbmZvLnByb2R1Y3RzLmxlbmd0aCA9PT0gMCk7XG4gICAgdGhpcy5wcm9kdWN0UmVuZGVyZXIucmVuZGVyTGlzdChjYXJ0SW5mby5wcm9kdWN0cyk7XG4gICAgLy8gQHRvZG86IHJlbmRlciBTdW1tYXJ5IGJsb2NrIHdoZW4gYXQgbGVhc3QgMSBwcm9kdWN0IGlzIGluIGNhcnRcbiAgICAvLyBhbmQgZGVsaXZlcnkgb3B0aW9ucyBhcmUgYXZhaWxhYmxlXG5cbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmNhcnRCbG9jaykucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIENoYW5nZXMgY2FydCBhZGRyZXNzZXNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9jaGFuZ2VDYXJ0QWRkcmVzc2VzKCkge1xuICAgIGNvbnN0IGFkZHJlc3NlcyA9IHtcbiAgICAgIGRlbGl2ZXJ5QWRkcmVzc0lkOiAkKGNyZWF0ZU9yZGVyTWFwLmRlbGl2ZXJ5QWRkcmVzc1NlbGVjdCkudmFsKCksXG4gICAgICBpbnZvaWNlQWRkcmVzc0lkOiAkKGNyZWF0ZU9yZGVyTWFwLmludm9pY2VBZGRyZXNzU2VsZWN0KS52YWwoKSxcbiAgICB9O1xuXG4gICAgdGhpcy5jYXJ0RWRpdG9yLmNoYW5nZUNhcnRBZGRyZXNzZXModGhpcy5jYXJ0SWQsIGFkZHJlc3Nlcyk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jcmVhdGUtb3JkZXItcGFnZS5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbi8qKlxuICogRW5jYXBzdWxhdGVzIHNlbGVjdG9ycyBmb3IgXCJDcmVhdGUgb3JkZXJcIiBwYWdlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IHtcbiAgb3JkZXJDcmVhdGlvbkNvbnRhaW5lcjogJyNvcmRlci1jcmVhdGlvbi1jb250YWluZXInLFxuXG4gIC8vIHNlbGVjdG9ycyByZWxhdGVkIHRvIGN1c3RvbWVyIGJsb2NrXG4gIGN1c3RvbWVyU2VhcmNoSW5wdXQ6ICcjY3VzdG9tZXItc2VhcmNoLWlucHV0JyxcbiAgY3VzdG9tZXJTZWFyY2hSZXN1bHRzQmxvY2s6ICcuanMtY3VzdG9tZXItc2VhcmNoLXJlc3VsdHMnLFxuICBjdXN0b21lclNlYXJjaFJlc3VsdFRlbXBsYXRlOiAnI2N1c3RvbWVyLXNlYXJjaC1yZXN1bHQtdGVtcGxhdGUnLFxuICBjaGFuZ2VDdXN0b21lckJ0bjogJy5qcy1jaGFuZ2UtY3VzdG9tZXItYnRuJyxcbiAgY3VzdG9tZXJTZWFyY2hSb3c6ICcuanMtc2VhcmNoLWN1c3RvbWVyLXJvdycsXG4gIGNob29zZUN1c3RvbWVyQnRuOiAnLmpzLWNob29zZS1jdXN0b21lci1idG4nLFxuICBub3RTZWxlY3RlZEN1c3RvbWVyU2VhcmNoUmVzdWx0czogJy5qcy1jdXN0b21lci1zZWFyY2gtcmVzdWx0Om5vdCguYm9yZGVyLXN1Y2Nlc3MpJyxcbiAgY3VzdG9tZXJTZWFyY2hSZXN1bHROYW1lOiAnLmpzLWN1c3RvbWVyLW5hbWUnLFxuICBjdXN0b21lclNlYXJjaFJlc3VsdEVtYWlsOiAnLmpzLWN1c3RvbWVyLWVtYWlsJyxcbiAgY3VzdG9tZXJTZWFyY2hSZXN1bHRJZDogJy5qcy1jdXN0b21lci1pZCcsXG4gIGN1c3RvbWVyU2VhcmNoUmVzdWx0QmlydGhkYXk6ICcuanMtY3VzdG9tZXItYmlydGhkYXknLFxuICBjdXN0b21lckRldGFpbHNCdG46ICcuanMtZGV0YWlscy1jdXN0b21lci1idG4nLFxuICBjdXN0b21lclNlYXJjaFJlc3VsdENvbHVtbjogJy5qcy1jdXN0b21lci1zZWFyY2gtcmVzdWx0LWNvbCcsXG4gIGN1c3RvbWVyU2VhcmNoQmxvY2s6ICcjY3VzdG9tZXItc2VhcmNoLWJsb2NrJyxcbiAgY3VzdG9tZXJDYXJ0c1RhYjogJy5qcy1jdXN0b21lci1jYXJ0cy10YWInLFxuICBjdXN0b21lck9yZGVyc1RhYjogJy5qcy1jdXN0b21lci1vcmRlcnMtdGFiJyxcbiAgY3VzdG9tZXJDYXJ0c1RhYmxlOiAnI2N1c3RvbWVyLWNhcnRzLXRhYmxlJyxcbiAgY3VzdG9tZXJDYXJ0c1RhYmxlUm93VGVtcGxhdGU6ICcjY3VzdG9tZXItY2FydHMtdGFibGUtcm93LXRlbXBsYXRlJyxcbiAgY3VzdG9tZXJDaGVja291dEhpc3Rvcnk6ICcjY3VzdG9tZXItY2hlY2tvdXQtaGlzdG9yeScsXG4gIGN1c3RvbWVyT3JkZXJzVGFibGU6ICcjY3VzdG9tZXItb3JkZXJzLXRhYmxlJyxcbiAgY3VzdG9tZXJPcmRlcnNUYWJsZVJvd1RlbXBsYXRlOiAnI2N1c3RvbWVyLW9yZGVycy10YWJsZS1yb3ctdGVtcGxhdGUnLFxuICBjYXJ0UnVsZXNUYWJsZTogJyNjYXJ0LXJ1bGVzLXRhYmxlJyxcbiAgY2FydFJ1bGVzVGFibGVSb3dUZW1wbGF0ZTogJyNjYXJ0LXJ1bGVzLXRhYmxlLXJvdy10ZW1wbGF0ZScsXG4gIHVzZUNhcnRCdG46ICcuanMtdXNlLWNhcnQtYnRuJyxcbiAgY2FydERldGFpbHNCdG46ICcuanMtY2FydC1kZXRhaWxzLWJ0bicsXG4gIGNhcnRJZEZpZWxkOiAnLmpzLWNhcnQtaWQnLFxuICBjYXJ0RGF0ZUZpZWxkOiAnLmpzLWNhcnQtZGF0ZScsXG4gIGNhcnRUb3RhbEZpZWxkOiAnLmpzLWNhcnQtdG90YWwnLFxuICB1c2VPcmRlckJ0bjogJy5qcy11c2Utb3JkZXItYnRuJyxcbiAgb3JkZXJEZXRhaWxzQnRuOiAnLmpzLW9yZGVyLWRldGFpbHMtYnRuJyxcbiAgb3JkZXJJZEZpZWxkOiAnLmpzLW9yZGVyLWlkJyxcbiAgb3JkZXJEYXRlRmllbGQ6ICcuanMtb3JkZXItZGF0ZScsXG4gIG9yZGVyUHJvZHVjdHNGaWVsZDogJy5qcy1vcmRlci1wcm9kdWN0cycsXG4gIG9yZGVyVG90YWxGaWVsZDogJy5qcy1vcmRlci10b3RhbC1wYWlkJyxcbiAgb3JkZXJTdGF0dXNGaWVsZDogJy5qcy1vcmRlci1zdGF0dXMnLFxuXG4gIC8vIHNlbGVjdG9ycyByZWxhdGVkIHRvIGNhcnQgYmxvY2tcbiAgY2FydEJsb2NrOiAnI2NhcnQtYmxvY2snLFxuXG4gIC8vIHNlbGVjdG9ycyByZWxhdGVkIHRvIGNhcnRSdWxlcyBibG9ja1xuICBjYXJ0UnVsZXNCbG9jazogJyNjYXJ0LXJ1bGVzLWJsb2NrJyxcbiAgY2FydFJ1bGVTZWFyY2hJbnB1dDogJyNzZWFyY2gtY2FydC1ydWxlcy1pbnB1dCcsXG4gIGNhcnRSdWxlc1NlYXJjaFJlc3VsdEJveDogJyNzZWFyY2gtY2FydC1ydWxlcy1yZXN1bHQtYm94JyxcbiAgY2FydFJ1bGVzTm90Rm91bmRUZW1wbGF0ZTogJyNjYXJ0LXJ1bGVzLW5vdC1mb3VuZC10ZW1wbGF0ZScsXG4gIGZvdW5kQ2FydFJ1bGVUZW1wbGF0ZTogJyNmb3VuZC1jYXJ0LXJ1bGUtdGVtcGxhdGUnLFxuICBmb3VuZENhcnRSdWxlTGlzdEl0ZW06ICcuanMtZm91bmQtY2FydC1ydWxlJyxcbiAgY2FydFJ1bGVOYW1lRmllbGQ6ICcuanMtY2FydC1ydWxlLW5hbWUnLFxuICBjYXJ0UnVsZURlc2NyaXB0aW9uRmllbGQ6ICcuanMtY2FydC1ydWxlLWRlc2NyaXB0aW9uJyxcbiAgY2FydFJ1bGVWYWx1ZUZpZWxkOiAnLmpzLWNhcnQtcnVsZS12YWx1ZScsXG4gIGNhcnRSdWxlRGVsZXRlQnRuOiAnLmpzLWNhcnQtcnVsZS1kZWxldGUtYnRuJyxcbiAgY2FydFJ1bGVFcnJvckJsb2NrOiAnI2pzLWNhcnQtcnVsZS1lcnJvci1ibG9jaycsXG4gIGNhcnRSdWxlRXJyb3JUZXh0OiAnI2pzLWNhcnQtcnVsZS1lcnJvci10ZXh0JyxcblxuICAvLyBzZWxlY3RvcnMgcmVsYXRlZCB0byBhZGRyZXNzZXMgYmxvY2tcbiAgYWRkcmVzc2VzQmxvY2s6ICcjYWRkcmVzc2VzLWJsb2NrJyxcbiAgZGVsaXZlcnlBZGRyZXNzRGV0YWlsczogJyNkZWxpdmVyeS1hZGRyZXNzLWRldGFpbHMnLFxuICBpbnZvaWNlQWRkcmVzc0RldGFpbHM6ICcjaW52b2ljZS1hZGRyZXNzLWRldGFpbHMnLFxuICBkZWxpdmVyeUFkZHJlc3NTZWxlY3Q6ICcjZGVsaXZlcnktYWRkcmVzcy1zZWxlY3QnLFxuICBpbnZvaWNlQWRkcmVzc1NlbGVjdDogJyNpbnZvaWNlLWFkZHJlc3Mtc2VsZWN0JyxcbiAgYWRkcmVzc1NlbGVjdDogJy5qcy1hZGRyZXNzLXNlbGVjdCcsXG4gIGFkZHJlc3Nlc0NvbnRlbnQ6ICcjYWRkcmVzc2VzLWNvbnRlbnQnLFxuICBhZGRyZXNzZXNXYXJuaW5nOiAnI2FkZHJlc3Nlcy13YXJuaW5nJyxcblxuICAvLyBzZWxlY3RvcnMgcmVsYXRlZCB0byBzdW1tYXJ5IGJsb2NrXG4gIHN1bW1hcnlCbG9jazogJyNzdW1tYXJ5LWJsb2NrJyxcblxuICAvLyBzZWxlY3RvcnMgcmVsYXRlZCB0byBzaGlwcGluZyBibG9ja1xuICBzaGlwcGluZ0Jsb2NrOiAnI3NoaXBwaW5nLWJsb2NrJyxcbiAgc2hpcHBpbmdGb3JtOiAnLmpzLXNoaXBwaW5nLWZvcm0nLFxuICBub0NhcnJpZXJCbG9jazogJy5qcy1uby1jYXJyaWVyLWJsb2NrJyxcbiAgZGVsaXZlcnlPcHRpb25TZWxlY3Q6ICcjZGVsaXZlcnktb3B0aW9uLXNlbGVjdCcsXG4gIHRvdGFsU2hpcHBpbmdGaWVsZDogJy5qcy10b3RhbC1zaGlwcGluZycsXG4gIGZyZWVTaGlwcGluZ1N3aXRjaDogJy5qcy1mcmVlLXNoaXBwaW5nLXN3aXRjaCcsXG5cbiAgLy8gc2VsZWN0b3JzIHJlbGF0ZWQgdG8gY2FydCBwcm9kdWN0cyBibG9ja1xuICBwcm9kdWN0U2VhcmNoOiAnI3Byb2R1Y3Qtc2VhcmNoJyxcbiAgY29tYmluYXRpb25zU2VsZWN0OiAnI2NvbWJpbmF0aW9uLXNlbGVjdCcsXG4gIHByb2R1Y3RSZXN1bHRCbG9jazogJyNwcm9kdWN0LXNlYXJjaC1yZXN1bHRzJyxcbiAgcHJvZHVjdFNlbGVjdDogJyNwcm9kdWN0LXNlbGVjdCcsXG4gIHF1YW50aXR5SW5wdXQ6ICcjcXVhbnRpdHktaW5wdXQnLFxuICBpblN0b2NrQ291bnRlcjogJy5qcy1pbi1zdG9jay1jb3VudGVyJyxcbiAgY29tYmluYXRpb25zVGVtcGxhdGU6ICcjY29tYmluYXRpb25zLXRlbXBsYXRlJyxcbiAgY29tYmluYXRpb25zUm93OiAnLmpzLWNvbWJpbmF0aW9ucy1yb3cnLFxuICBwcm9kdWN0U2VsZWN0Um93OiAnLmpzLXByb2R1Y3Qtc2VsZWN0LXJvdycsXG4gIHByb2R1Y3RDdXN0b21GaWVsZHNDb250YWluZXI6ICcjanMtY3VzdG9tLWZpZWxkcy1jb250YWluZXInLFxuICBwcm9kdWN0Q3VzdG9taXphdGlvbkNvbnRhaW5lcjogJyNqcy1jdXN0b21pemF0aW9uLWNvbnRhaW5lcicsXG4gIHByb2R1Y3RDdXN0b21GaWxlVGVtcGxhdGU6ICcjanMtcHJvZHVjdC1jdXN0b20tZmlsZS10ZW1wbGF0ZScsXG4gIHByb2R1Y3RDdXN0b21UZXh0VGVtcGxhdGU6ICcjanMtcHJvZHVjdC1jdXN0b20tdGV4dC10ZW1wbGF0ZScsXG4gIHByb2R1Y3RDdXN0b21JbnB1dExhYmVsOiAnLmpzLXByb2R1Y3QtY3VzdG9tLWlucHV0LWxhYmVsJyxcbiAgcHJvZHVjdEN1c3RvbUlucHV0OiAnLmpzLXByb2R1Y3QtY3VzdG9tLWlucHV0JyxcbiAgcXVhbnRpdHlSb3c6ICcuanMtcXVhbnRpdHktcm93JyxcbiAgYWRkVG9DYXJ0QnV0dG9uOiAnI2FkZC1wcm9kdWN0LXRvLWNhcnQtYnRuJyxcbiAgcHJvZHVjdHNUYWJsZTogJyNwcm9kdWN0cy10YWJsZScsXG4gIHByb2R1Y3RzVGFibGVSb3dUZW1wbGF0ZTogJyNwcm9kdWN0cy10YWJsZS1yb3ctdGVtcGxhdGUnLFxuICBwcm9kdWN0SW1hZ2VGaWVsZDogJy5qcy1wcm9kdWN0LWltYWdlJyxcbiAgcHJvZHVjdE5hbWVGaWVsZDogJy5qcy1wcm9kdWN0LW5hbWUnLFxuICBwcm9kdWN0QXR0ckZpZWxkOiAnLmpzLXByb2R1Y3QtYXR0cicsXG4gIHByb2R1Y3RSZWZlcmVuY2VGaWVsZDogJy5qcy1wcm9kdWN0LXJlZicsXG4gIHByb2R1Y3RVbml0UHJpY2VJbnB1dDogJy5qcy1wcm9kdWN0LXVuaXQtaW5wdXQnLFxuICBwcm9kdWN0VG90YWxQcmljZUZpZWxkOiAnLmpzLXByb2R1Y3QtdG90YWwtcHJpY2UnLFxuICBwcm9kdWN0UmVtb3ZlQnRuOiAnLmpzLXByb2R1Y3QtcmVtb3ZlLWJ0bicsXG4gIHByb2R1Y3RUYXhXYXJuaW5nOiAnLmpzLXRheC13YXJuaW5nJyxcbiAgbm9Qcm9kdWN0c0ZvdW5kV2FybmluZzogJy5qcy1uby1wcm9kdWN0cy1mb3VuZCcsXG59O1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL2NyZWF0ZS1vcmRlci1tYXAuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgUm91dGluZyBmcm9tICdmb3Mtcm91dGluZyc7XG5pbXBvcnQgcm91dGVzIGZyb20gJ0Bqcy9mb3NfanNfcm91dGVzLmpzb24nO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogV3JhcHMgRk9TSnNSb3V0aW5nYnVuZGxlIHdpdGggZXhwb3NlZCByb3V0ZXMuXG4gKiBUbyBleHBvc2Ugcm91dGUgYWRkIG9wdGlvbiBgZXhwb3NlOiB0cnVlYCBpbiAueW1sIHJvdXRpbmcgY29uZmlnXG4gKlxuICogZS5nLlxuICpcbiAqIGBteV9yb3V0ZVxuICogICAgcGF0aDogL215LXBhdGhcbiAqICAgIG9wdGlvbnM6XG4gKiAgICAgIGV4cG9zZTogdHJ1ZVxuICogYFxuICogQW5kIHJ1biBgYmluL2NvbnNvbGUgZm9zOmpzLXJvdXRpbmc6ZHVtcCAtLWZvcm1hdD1qc29uIC0tdGFyZ2V0PWFkbWluLWRldi90aGVtZXMvbmV3LXRoZW1lL2pzL2Zvc19qc19yb3V0ZXMuanNvbmBcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUm91dGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgUm91dGluZy5zZXREYXRhKHJvdXRlcyk7XG4gICAgUm91dGluZy5zZXRCYXNlVXJsKCQoZG9jdW1lbnQpLmZpbmQoJ2JvZHknKS5kYXRhKCdiYXNlLXVybCcpKTtcblxuICAgIHJldHVybiB0aGlzO1xuICB9XG5cbiAgLyoqXG4gICAqIERlY29yYXRlZCBcImdlbmVyYXRlXCIgbWV0aG9kLCB3aXRoIHByZWRlZmluZWQgc2VjdXJpdHkgdG9rZW4gaW4gcGFyYW1zXG4gICAqXG4gICAqIEBwYXJhbSByb3V0ZVxuICAgKiBAcGFyYW0gcGFyYW1zXG4gICAqXG4gICAqIEByZXR1cm5zIHtTdHJpbmd9XG4gICAqL1xuICBnZW5lcmF0ZShyb3V0ZSwgcGFyYW1zID0ge30pIHtcbiAgICBjb25zdCB0b2tlbml6ZWRQYXJhbXMgPSBPYmplY3QuYXNzaWduKHBhcmFtcywge190b2tlbjogJChkb2N1bWVudCkuZmluZCgnYm9keScpLmRhdGEoJ3Rva2VuJyl9KTtcblxuICAgIHJldHVybiBSb3V0aW5nLmdlbmVyYXRlKHJvdXRlLCB0b2tlbml6ZWRQYXJhbXMpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL3JvdXRlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5pbXBvcnQgQ3JlYXRlT3JkZXJQYWdlIGZyb20gJy4vY3JlYXRlL2NyZWF0ZS1vcmRlci1wYWdlJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKGRvY3VtZW50KS5yZWFkeSgoKSA9PiB7XG4gIG5ldyBDcmVhdGVPcmRlclBhZ2UoKTtcbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvY3JlYXRlLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IGNyZWF0ZU9yZGVyUGFnZU1hcCBmcm9tICcuL2NyZWF0ZS1vcmRlci1tYXAnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogUmVuZGVycyBEZWxpdmVyeSAmIEludm9pY2UgYWRkcmVzc2VzIHNlbGVjdFxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBBZGRyZXNzZXNSZW5kZXJlciB7XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7QXJyYXl9IGFkZHJlc3Nlc1xuICAgKi9cbiAgcmVuZGVyKGFkZHJlc3Nlcykge1xuICAgIGxldCBkZWxpdmVyeUFkZHJlc3NEZXRhaWxzQ29udGVudCA9ICcnO1xuICAgIGxldCBpbnZvaWNlQWRkcmVzc0RldGFpbHNDb250ZW50ID0gJyc7XG5cbiAgICBjb25zdCAkZGVsaXZlcnlBZGRyZXNzRGV0YWlscyA9ICQoY3JlYXRlT3JkZXJQYWdlTWFwLmRlbGl2ZXJ5QWRkcmVzc0RldGFpbHMpO1xuICAgIGNvbnN0ICRpbnZvaWNlQWRkcmVzc0RldGFpbHMgPSAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5pbnZvaWNlQWRkcmVzc0RldGFpbHMpO1xuICAgIGNvbnN0ICRkZWxpdmVyeUFkZHJlc3NTZWxlY3QgPSAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5kZWxpdmVyeUFkZHJlc3NTZWxlY3QpO1xuICAgIGNvbnN0ICRpbnZvaWNlQWRkcmVzc1NlbGVjdCA9ICQoY3JlYXRlT3JkZXJQYWdlTWFwLmludm9pY2VBZGRyZXNzU2VsZWN0KTtcblxuICAgIGNvbnN0ICRhZGRyZXNzZXNDb250ZW50ID0gJChjcmVhdGVPcmRlclBhZ2VNYXAuYWRkcmVzc2VzQ29udGVudCk7XG4gICAgY29uc3QgJGFkZHJlc3Nlc1dhcm5pbmdDb250ZW50ID0gJChjcmVhdGVPcmRlclBhZ2VNYXAuYWRkcmVzc2VzV2FybmluZyk7XG5cbiAgICAkZGVsaXZlcnlBZGRyZXNzRGV0YWlscy5lbXB0eSgpO1xuICAgICRpbnZvaWNlQWRkcmVzc0RldGFpbHMuZW1wdHkoKTtcbiAgICAkZGVsaXZlcnlBZGRyZXNzU2VsZWN0LmVtcHR5KCk7XG4gICAgJGludm9pY2VBZGRyZXNzU2VsZWN0LmVtcHR5KCk7XG5cbiAgICBpZiAoYWRkcmVzc2VzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgJGFkZHJlc3Nlc1dhcm5pbmdDb250ZW50LnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAgICRhZGRyZXNzZXNDb250ZW50LmFkZENsYXNzKCdkLW5vbmUnKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgICRhZGRyZXNzZXNDb250ZW50LnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAkYWRkcmVzc2VzV2FybmluZ0NvbnRlbnQuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gT2JqZWN0LmtleXMoYWRkcmVzc2VzKSkge1xuICAgICAgY29uc3QgYWRkcmVzcyA9IGFkZHJlc3Nlc1trZXldO1xuXG4gICAgICBjb25zdCBkZWxpdmVyeUFkZHJlc3NPcHRpb24gPSB7XG4gICAgICAgIHZhbHVlOiBhZGRyZXNzLmFkZHJlc3NJZCxcbiAgICAgICAgdGV4dDogYWRkcmVzcy5hbGlhcyxcbiAgICAgIH07XG5cbiAgICAgIGNvbnN0IGludm9pY2VBZGRyZXNzT3B0aW9uID0ge1xuICAgICAgICB2YWx1ZTogYWRkcmVzcy5hZGRyZXNzSWQsXG4gICAgICAgIHRleHQ6IGFkZHJlc3MuYWxpYXMsXG4gICAgICB9O1xuXG4gICAgICBpZiAoYWRkcmVzcy5kZWxpdmVyeSkge1xuICAgICAgICBkZWxpdmVyeUFkZHJlc3NEZXRhaWxzQ29udGVudCA9IGFkZHJlc3MuZm9ybWF0dGVkQWRkcmVzcztcbiAgICAgICAgZGVsaXZlcnlBZGRyZXNzT3B0aW9uLnNlbGVjdGVkID0gJ3NlbGVjdGVkJztcbiAgICAgIH1cblxuICAgICAgaWYgKGFkZHJlc3MuaW52b2ljZSkge1xuICAgICAgICBpbnZvaWNlQWRkcmVzc0RldGFpbHNDb250ZW50ID0gYWRkcmVzcy5mb3JtYXR0ZWRBZGRyZXNzO1xuICAgICAgICBpbnZvaWNlQWRkcmVzc09wdGlvbi5zZWxlY3RlZCA9ICdzZWxlY3RlZCc7XG4gICAgICB9XG5cbiAgICAgICRkZWxpdmVyeUFkZHJlc3NTZWxlY3QuYXBwZW5kKCQoJzxvcHRpb24+JywgZGVsaXZlcnlBZGRyZXNzT3B0aW9uKSk7XG4gICAgICAkaW52b2ljZUFkZHJlc3NTZWxlY3QuYXBwZW5kKCQoJzxvcHRpb24+JywgaW52b2ljZUFkZHJlc3NPcHRpb24pKTtcbiAgICB9XG5cbiAgICBpZiAoZGVsaXZlcnlBZGRyZXNzRGV0YWlsc0NvbnRlbnQpIHtcbiAgICAgICRkZWxpdmVyeUFkZHJlc3NEZXRhaWxzLmh0bWwoZGVsaXZlcnlBZGRyZXNzRGV0YWlsc0NvbnRlbnQpO1xuICAgIH1cblxuICAgIGlmIChpbnZvaWNlQWRkcmVzc0RldGFpbHNDb250ZW50KSB7XG4gICAgICAkaW52b2ljZUFkZHJlc3NEZXRhaWxzLmh0bWwoaW52b2ljZUFkZHJlc3NEZXRhaWxzQ29udGVudCk7XG4gICAgfVxuXG4gICAgdGhpcy5fc2hvd0FkZHJlc3Nlc0Jsb2NrKCk7XG4gIH1cblxuICAvKipcbiAgICogU2hvd3MgYWRkcmVzc2VzIGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd0FkZHJlc3Nlc0Jsb2NrKCkge1xuICAgICQoY3JlYXRlT3JkZXJQYWdlTWFwLmFkZHJlc3Nlc0Jsb2NrKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9hZGRyZXNzZXMtcmVuZGVyZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgY3JlYXRlT3JkZXJQYWdlTWFwIGZyb20gJ0BwYWdlcy9vcmRlci9jcmVhdGUvY3JlYXRlLW9yZGVyLW1hcCc7XG5pbXBvcnQgUm91dGVyIGZyb20gJ0Bjb21wb25lbnRzL3JvdXRlcic7XG5pbXBvcnQge0V2ZW50RW1pdHRlcn0gZnJvbSAnQGNvbXBvbmVudHMvZXZlbnQtZW1pdHRlcic7XG5pbXBvcnQgZXZlbnRNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL2NyZWF0ZS9ldmVudC1tYXAnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogUHJvdmlkZXMgYWpheCBjYWxscyBmb3IgZ2V0dGluZyBjYXJ0IGluZm9ybWF0aW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENhcnRQcm92aWRlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lciA9ICQoY3JlYXRlT3JkZXJQYWdlTWFwLm9yZGVyQ3JlYXRpb25Db250YWluZXIpO1xuICAgIHRoaXMucm91dGVyID0gbmV3IFJvdXRlcigpO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldHMgY2FydCBpbmZvcm1hdGlvblxuICAgKlxuICAgKiBAcGFyYW0gY2FydElkXG4gICAqXG4gICAqIEByZXR1cm5zIHtqcVhIUn0uIE9iamVjdCB3aXRoIGNhcnQgaW5mb3JtYXRpb24gaW4gcmVzcG9uc2UuXG4gICAqL1xuICBnZXRDYXJ0KGNhcnRJZCkge1xuICAgICQuZ2V0KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9jYXJ0c19pbmZvJywge2NhcnRJZH0pKS50aGVuKChjYXJ0SW5mbykgPT4ge1xuICAgICAgRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAuY2FydExvYWRlZCwgY2FydEluZm8pO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldHMgZXhpc3RpbmcgZW1wdHkgY2FydCBvciBjcmVhdGVzIG5ldyBlbXB0eSBjYXJ0IGZvciBjdXN0b21lci5cbiAgICpcbiAgICogQHBhcmFtIGN1c3RvbWVySWRcbiAgICpcbiAgICogQHJldHVybnMge2pxWEhSfS4gT2JqZWN0IHdpdGggY2FydCBpbmZvcm1hdGlvbiBpbiByZXNwb25zZVxuICAgKi9cbiAgbG9hZEVtcHR5Q2FydChjdXN0b21lcklkKSB7XG4gICAgJC5wb3N0KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9jYXJ0c19jcmVhdGUnKSwge1xuICAgICAgY3VzdG9tZXJfaWQ6IGN1c3RvbWVySWQsXG4gICAgfSkudGhlbigoY2FydEluZm8pID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRMb2FkZWQsIGNhcnRJbmZvKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEdXBsaWNhdGVzIGNhcnQgZnJvbSBwcm92aWRlZCBvcmRlclxuICAgKlxuICAgKiBAcGFyYW0gb3JkZXJJZFxuICAgKlxuICAgKiBAcmV0dXJucyB7anFYSFJ9LiBPYmplY3Qgd2l0aCBjYXJ0IGluZm9ybWF0aW9uIGluIHJlc3BvbnNlXG4gICAqL1xuICBkdXBsaWNhdGVPcmRlckNhcnQob3JkZXJJZCkge1xuICAgICQucG9zdCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fb3JkZXJzX2R1cGxpY2F0ZV9jYXJ0Jywge29yZGVySWR9KSkudGhlbigoY2FydEluZm8pID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRMb2FkZWQsIGNhcnRJbmZvKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL2NhcnQtcHJvdmlkZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgQ2FydEVkaXRvciBmcm9tICdAcGFnZXMvb3JkZXIvY3JlYXRlL2NhcnQtZWRpdG9yJztcbmltcG9ydCBDYXJ0UnVsZXNSZW5kZXJlciBmcm9tICdAcGFnZXMvb3JkZXIvY3JlYXRlL2NhcnQtcnVsZXMtcmVuZGVyZXInO1xuaW1wb3J0IGNyZWF0ZU9yZGVyTWFwIGZyb20gJ0BwYWdlcy9vcmRlci9jcmVhdGUvY3JlYXRlLW9yZGVyLW1hcCc7XG5pbXBvcnQge0V2ZW50RW1pdHRlcn0gZnJvbSAnQGNvbXBvbmVudHMvZXZlbnQtZW1pdHRlcic7XG5pbXBvcnQgZXZlbnRNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL2NyZWF0ZS9ldmVudC1tYXAnO1xuaW1wb3J0IFJvdXRlciBmcm9tICdAY29tcG9uZW50cy9yb3V0ZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogUmVzcG9uc2libGUgZm9yIHNlYXJjaGluZyBjYXJ0IHJ1bGVzIGFuZCBtYW5hZ2luZyBjYXJ0IHJ1bGVzIHNlYXJjaCBibG9ja1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDYXJ0UnVsZU1hbmFnZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgICB0aGlzLiRzZWFyY2hJbnB1dCA9ICQoY3JlYXRlT3JkZXJNYXAuY2FydFJ1bGVTZWFyY2hJbnB1dCk7XG4gICAgdGhpcy5jYXJ0UnVsZXNSZW5kZXJlciA9IG5ldyBDYXJ0UnVsZXNSZW5kZXJlcigpO1xuICAgIHRoaXMuY2FydEVkaXRvciA9IG5ldyBDYXJ0RWRpdG9yKCk7XG5cbiAgICB0aGlzLl9pbml0TGlzdGVuZXJzKCk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgc2VhcmNoOiAoKSA9PiB0aGlzLl9zZWFyY2goKSxcbiAgICAgIHN0b3BTZWFyY2hpbmc6ICgpID0+IHRoaXMuY2FydFJ1bGVzUmVuZGVyZXIuaGlkZVJlc3VsdHNEcm9wZG93bigpLFxuICAgICAgYWRkQ2FydFJ1bGVUb0NhcnQ6IChjYXJ0UnVsZUlkLCBjYXJ0SWQpID0+IHRoaXMuY2FydEVkaXRvci5hZGRDYXJ0UnVsZVRvQ2FydChjYXJ0UnVsZUlkLCBjYXJ0SWQpLFxuICAgICAgcmVtb3ZlQ2FydFJ1bGVGcm9tQ2FydDogKGNhcnRSdWxlSWQsIGNhcnRJZCkgPT4gdGhpcy5jYXJ0RWRpdG9yLnJlbW92ZUNhcnRSdWxlRnJvbUNhcnQoY2FydFJ1bGVJZCwgY2FydElkKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRpYXRlcyBldmVudCBsaXN0ZW5lcnMgZm9yIGNhcnQgcnVsZSBhY3Rpb25zXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdExpc3RlbmVycygpIHtcbiAgICB0aGlzLl9vbkNhcnRSdWxlU2VhcmNoKCk7XG4gICAgdGhpcy5fb25BZGRDYXJ0UnVsZVRvQ2FydCgpO1xuICAgIHRoaXMuX29uQWRkQ2FydFJ1bGVUb0NhcnRGYWlsdXJlKCk7XG4gICAgdGhpcy5fb25SZW1vdmVDYXJ0UnVsZUZyb21DYXJ0KCk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBmb3IgY2FydCBydWxlIHNlYXJjaCBhY3Rpb25cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vbkNhcnRSdWxlU2VhcmNoKCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5jYXJ0UnVsZVNlYXJjaGVkLCAoY2FydFJ1bGVzKSA9PiB7XG4gICAgICB0aGlzLmNhcnRSdWxlc1JlbmRlcmVyLnJlbmRlclNlYXJjaFJlc3VsdHMoY2FydFJ1bGVzKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGV2ZW50IG9mIGFkZCBjYXJ0IHJ1bGUgdG8gY2FydCBhY3Rpb25cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vbkFkZENhcnRSdWxlVG9DYXJ0KCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5jYXJ0UnVsZUFkZGVkLCAoY2FydEluZm8pID0+IHtcbiAgICAgIHRoaXMuY2FydFJ1bGVzUmVuZGVyZXIucmVuZGVyQ2FydFJ1bGVzQmxvY2soY2FydEluZm8uY2FydFJ1bGVzLCBjYXJ0SW5mby5wcm9kdWN0cy5sZW5ndGggPT09IDApO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZXZlbnQgd2hlbiBhZGQgY2FydCBydWxlIHRvIGNhcnQgZmFpbHNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vbkFkZENhcnRSdWxlVG9DYXJ0RmFpbHVyZSgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAuY2FydFJ1bGVGYWlsZWRUb0FkZCwgKG1lc3NhZ2UpID0+IHtcbiAgICAgIHRoaXMuY2FydFJ1bGVzUmVuZGVyZXIuZGlzcGxheUVycm9yTWVzc2FnZShtZXNzYWdlKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGV2ZW50IGZvciByZW1vdmUgY2FydCBydWxlIGZyb20gY2FydCBhY3Rpb25cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vblJlbW92ZUNhcnRSdWxlRnJvbUNhcnQoKSB7XG4gICAgRXZlbnRFbWl0dGVyLm9uKGV2ZW50TWFwLmNhcnRSdWxlUmVtb3ZlZCwgKGNhcnRJbmZvKSA9PiB7XG4gICAgICB0aGlzLmNhcnRSdWxlc1JlbmRlcmVyLnJlbmRlckNhcnRSdWxlc0Jsb2NrKGNhcnRJbmZvLmNhcnRSdWxlcywgY2FydEluZm8ucHJvZHVjdHMubGVuZ3RoID09PSAwKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTZWFyY2hlcyBmb3IgY2FydCBydWxlcyBieSBzZWFyY2ggcGhyYXNlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2VhcmNoKHNlYXJjaFBocmFzZSkge1xuICAgIGlmIChzZWFyY2hQaHJhc2UubGVuZ3RoIDwgMykge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgICQuZ2V0KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9jYXJ0X3J1bGVzX3NlYXJjaCcpLCB7XG4gICAgICBzZWFyY2hfcGhyYXNlOiBzZWFyY2hQaHJhc2UsXG4gICAgfSkudGhlbigoY2FydFJ1bGVzKSA9PiB7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5jYXJ0UnVsZVNlYXJjaGVkLCBjYXJ0UnVsZXMpO1xuICAgIH0pLmNhdGNoKChlKSA9PiB7XG4gICAgICBzaG93RXJyb3JNZXNzYWdlKGUucmVzcG9uc2VKU09OLm1lc3NhZ2UpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY2FydC1ydWxlLW1hbmFnZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgY3JlYXRlT3JkZXJNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL2NyZWF0ZS9jcmVhdGUtb3JkZXItbWFwJztcbmltcG9ydCBDdXN0b21lclJlbmRlcmVyIGZyb20gJ0BwYWdlcy9vcmRlci9jcmVhdGUvY3VzdG9tZXItcmVuZGVyZXInO1xuaW1wb3J0IHtFdmVudEVtaXR0ZXJ9IGZyb20gJ0Bjb21wb25lbnRzL2V2ZW50LWVtaXR0ZXInO1xuaW1wb3J0IGV2ZW50TWFwIGZyb20gJ0BwYWdlcy9vcmRlci9jcmVhdGUvZXZlbnQtbWFwJztcbmltcG9ydCBSb3V0ZXIgZnJvbSAnQGNvbXBvbmVudHMvcm91dGVyJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFJlc3BvbnNpYmxlIGZvciBjdXN0b21lcnMgbWFuYWdpbmcuIChzZWFyY2gsIHNlbGVjdCwgZ2V0IGN1c3RvbWVyIGluZm8gZXRjLilcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ3VzdG9tZXJNYW5hZ2VyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5jdXN0b21lcklkID0gbnVsbDtcbiAgICB0aGlzLmFjdGl2ZVNlYXJjaFJlcXVlc3QgPSBudWxsO1xuXG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJChjcmVhdGVPcmRlck1hcC5jdXN0b21lclNlYXJjaEJsb2NrKTtcbiAgICB0aGlzLiRzZWFyY2hJbnB1dCA9ICQoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJTZWFyY2hJbnB1dCk7XG4gICAgdGhpcy4kY3VzdG9tZXJTZWFyY2hSZXN1bHRCbG9jayA9ICQoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJTZWFyY2hSZXN1bHRzQmxvY2spO1xuICAgIHRoaXMuY3VzdG9tZXJSZW5kZXJlciA9IG5ldyBDdXN0b21lclJlbmRlcmVyKCk7XG5cbiAgICB0aGlzLl9pbml0TGlzdGVuZXJzKCk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgc2VhcmNoOiBzZWFyY2hQaHJhc2UgPT4gdGhpcy5fc2VhcmNoKHNlYXJjaFBocmFzZSksXG4gICAgICBzZWxlY3RDdXN0b21lcjogZXZlbnQgPT4gdGhpcy5fc2VsZWN0Q3VzdG9tZXIoZXZlbnQpLFxuICAgICAgbG9hZEN1c3RvbWVyQ2FydHM6IGN1cnJlbnRDYXJ0SWQgPT4gdGhpcy5fbG9hZEN1c3RvbWVyQ2FydHMoY3VycmVudENhcnRJZCksXG4gICAgICBsb2FkQ3VzdG9tZXJPcmRlcnM6ICgpID0+IHRoaXMuX2xvYWRDdXN0b21lck9yZGVycygpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhbGl6ZXMgZXZlbnQgbGlzdGVuZXJzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdExpc3RlbmVycygpIHtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgY3JlYXRlT3JkZXJNYXAuY2hhbmdlQ3VzdG9tZXJCdG4sICgpID0+IHRoaXMuX2NoYW5nZUN1c3RvbWVyKCkpO1xuICAgIHRoaXMuX29uQ3VzdG9tZXJTZWFyY2goKTtcbiAgICB0aGlzLl9vbkN1c3RvbWVyU2VsZWN0KCk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBmb3IgY3VzdG9tZXIgc2VhcmNoIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25DdXN0b21lclNlYXJjaCgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAuY3VzdG9tZXJTZWFyY2hlZCwgKHJlc3BvbnNlKSA9PiB7XG4gICAgICB0aGlzLmFjdGl2ZVNlYXJjaFJlcXVlc3QgPSBudWxsO1xuICAgICAgdGhpcy5jdXN0b21lclJlbmRlcmVyLnJlbmRlclNlYXJjaFJlc3VsdHMocmVzcG9uc2UuY3VzdG9tZXJzKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGZvciBjdXN0b21lciBzZWxlY3QgZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vbkN1c3RvbWVyU2VsZWN0KCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5jdXN0b21lclNlbGVjdGVkLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRjaG9vc2VCdG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgdGhpcy5jdXN0b21lcklkID0gJGNob29zZUJ0bi5kYXRhKCdjdXN0b21lci1pZCcpO1xuXG4gICAgICB0aGlzLmN1c3RvbWVyUmVuZGVyZXIuZGlzcGxheVNlbGVjdGVkQ3VzdG9tZXJCbG9jaygkY2hvb3NlQnRuKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIHVzZSBjYXNlIHdoZW4gY3VzdG9tZXIgaXMgY2hhbmdlZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NoYW5nZUN1c3RvbWVyKCkge1xuICAgIHRoaXMuY3VzdG9tZXJSZW5kZXJlci5zaG93Q3VzdG9tZXJTZWFyY2goKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMb2FkcyBjdXN0b21lciBjYXJ0cyBsaXN0XG4gICAqXG4gICAqIEBwYXJhbSBjdXJyZW50Q2FydElkXG4gICAqL1xuICBfbG9hZEN1c3RvbWVyQ2FydHMoY3VycmVudENhcnRJZCkge1xuICAgIGNvbnN0IGN1c3RvbWVySWQgPSB0aGlzLmN1c3RvbWVySWQ7XG5cbiAgICAkLmdldCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY3VzdG9tZXJzX2NhcnRzJywge2N1c3RvbWVySWR9KSkudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgIHRoaXMuY3VzdG9tZXJSZW5kZXJlci5yZW5kZXJDYXJ0cyhyZXNwb25zZS5jYXJ0cywgY3VycmVudENhcnRJZCk7XG4gICAgfSkuY2F0Y2goKGUpID0+IHtcbiAgICAgIHNob3dFcnJvck1lc3NhZ2UoZS5yZXNwb25zZUpTT04ubWVzc2FnZSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTG9hZHMgY3VzdG9tZXIgb3JkZXJzIGxpc3RcbiAgICovXG4gIF9sb2FkQ3VzdG9tZXJPcmRlcnMoKSB7XG4gICAgY29uc3QgY3VzdG9tZXJJZCA9IHRoaXMuY3VzdG9tZXJJZDtcblxuICAgICQuZ2V0KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9jdXN0b21lcnNfb3JkZXJzJywge2N1c3RvbWVySWR9KSkudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgIHRoaXMuY3VzdG9tZXJSZW5kZXJlci5yZW5kZXJPcmRlcnMocmVzcG9uc2Uub3JkZXJzKTtcbiAgICB9KS5jYXRjaCgoZSkgPT4ge1xuICAgICAgc2hvd0Vycm9yTWVzc2FnZShlLnJlc3BvbnNlSlNPTi5tZXNzYWdlKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0ge0V2ZW50fSBjaG9vc2VDdXN0b21lckV2ZW50XG4gICAqXG4gICAqIEByZXR1cm4ge051bWJlcn1cbiAgICovXG4gIF9zZWxlY3RDdXN0b21lcihjaG9vc2VDdXN0b21lckV2ZW50KSB7XG4gICAgRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAuY3VzdG9tZXJTZWxlY3RlZCwgY2hvb3NlQ3VzdG9tZXJFdmVudCk7XG5cbiAgICByZXR1cm4gdGhpcy5jdXN0b21lcklkO1xuICB9XG5cbiAgLyoqXG4gICAqIFNlYXJjaGVzIGZvciBjdXN0b21lcnNcbiAgICogQHRvZG86IGZpeCBzaG93aW5nIG5vdCBmb3VuZCBjdXN0b21lcnMgYW5kIHJlcmVuZGVyIGFmdGVyIGNoYW5nZSBjdXN0b21lclxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3NlYXJjaChzZWFyY2hQaHJhc2UpIHtcbiAgICBpZiAoc2VhcmNoUGhyYXNlLmxlbmd0aCA8IDMpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBpZiAodGhpcy5hY3RpdmVTZWFyY2hSZXF1ZXN0ICE9PSBudWxsKSB7XG4gICAgICB0aGlzLmFjdGl2ZVNlYXJjaFJlcXVlc3QuYWJvcnQoKTtcbiAgICB9XG5cbiAgICBjb25zdCAkc2VhcmNoUmVxdWVzdCA9ICQuZ2V0KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9jdXN0b21lcnNfc2VhcmNoJyksIHtcbiAgICAgIGN1c3RvbWVyX3NlYXJjaDogc2VhcmNoUGhyYXNlLFxuICAgIH0pO1xuICAgIHRoaXMuYWN0aXZlU2VhcmNoUmVxdWVzdCA9ICRzZWFyY2hSZXF1ZXN0O1xuXG4gICAgJHNlYXJjaFJlcXVlc3QudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmN1c3RvbWVyU2VhcmNoZWQsIHJlc3BvbnNlKTtcbiAgICB9KS5jYXRjaCgocmVzcG9uc2UpID0+IHtcbiAgICAgIGlmIChyZXNwb25zZS5zdGF0dXNUZXh0ID09PSAnYWJvcnQnKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgc2hvd0Vycm9yTWVzc2FnZShyZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jdXN0b21lci1tYW5hZ2VyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IENhcnRFZGl0b3IgZnJvbSAnQHBhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LWVkaXRvcic7XG5pbXBvcnQgY3JlYXRlT3JkZXJNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL2NyZWF0ZS9jcmVhdGUtb3JkZXItbWFwJztcbmltcG9ydCBldmVudE1hcCBmcm9tICdAcGFnZXMvb3JkZXIvY3JlYXRlL2V2ZW50LW1hcCc7XG5pbXBvcnQge0V2ZW50RW1pdHRlcn0gZnJvbSAnQGNvbXBvbmVudHMvZXZlbnQtZW1pdHRlcic7XG5pbXBvcnQgUHJvZHVjdFJlbmRlcmVyIGZyb20gJ0BwYWdlcy9vcmRlci9jcmVhdGUvcHJvZHVjdC1yZW5kZXJlcic7XG5pbXBvcnQgUm91dGVyIGZyb20gJ0Bjb21wb25lbnRzL3JvdXRlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBQcm9kdWN0IGNvbXBvbmVudCBPYmplY3QgZm9yIFwiQ3JlYXRlIG9yZGVyXCIgcGFnZVxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBQcm9kdWN0TWFuYWdlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMucHJvZHVjdHMgPSB7fTtcbiAgICB0aGlzLnNlbGVjdGVkUHJvZHVjdElkID0gbnVsbDtcbiAgICB0aGlzLnNlbGVjdGVkQ29tYmluYXRpb25JZCA9IG51bGw7XG4gICAgdGhpcy5hY3RpdmVTZWFyY2hSZXF1ZXN0ID0gbnVsbDtcblxuICAgIHRoaXMucHJvZHVjdFJlbmRlcmVyID0gbmV3IFByb2R1Y3RSZW5kZXJlcigpO1xuICAgIHRoaXMucm91dGVyID0gbmV3IFJvdXRlcigpO1xuICAgIHRoaXMuY2FydEVkaXRvciA9IG5ldyBDYXJ0RWRpdG9yKCk7XG5cbiAgICB0aGlzLl9pbml0TGlzdGVuZXJzKCk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgc2VhcmNoOiBzZWFyY2hQaHJhc2UgPT4gdGhpcy5fc2VhcmNoKHNlYXJjaFBocmFzZSksXG4gICAgICBhZGRQcm9kdWN0VG9DYXJ0OiBjYXJ0SWQgPT4gdGhpcy5jYXJ0RWRpdG9yLmFkZFByb2R1Y3QoY2FydElkLCB0aGlzLl9nZXRQcm9kdWN0RGF0YSgpKSxcbiAgICAgIHJlbW92ZVByb2R1Y3RGcm9tQ2FydDogKGNhcnRJZCwgcHJvZHVjdCkgPT4gdGhpcy5jYXJ0RWRpdG9yLnJlbW92ZVByb2R1Y3RGcm9tQ2FydChjYXJ0SWQsIHByb2R1Y3QpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhbGl6ZXMgZXZlbnQgbGlzdGVuZXJzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdExpc3RlbmVycygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RTZWxlY3QpLm9uKCdjaGFuZ2UnLCBlID0+IHRoaXMuX2luaXRQcm9kdWN0U2VsZWN0KGUpKTtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmNvbWJpbmF0aW9uc1NlbGVjdCkub24oJ2NoYW5nZScsIGUgPT4gdGhpcy5faW5pdENvbWJpbmF0aW9uU2VsZWN0KGUpKTtcblxuICAgIHRoaXMuX29uUHJvZHVjdFNlYXJjaCgpO1xuICAgIHRoaXMuX29uQWRkUHJvZHVjdFRvQ2FydCgpO1xuICAgIHRoaXMuX29uUmVtb3ZlUHJvZHVjdEZyb21DYXJ0KCk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBmb3IgcHJvZHVjdCBzZWFyY2ggZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vblByb2R1Y3RTZWFyY2goKSB7XG4gICAgRXZlbnRFbWl0dGVyLm9uKGV2ZW50TWFwLnByb2R1Y3RTZWFyY2hlZCwgKHJlc3BvbnNlKSA9PiB7XG4gICAgICB0aGlzLnByb2R1Y3RzID0gSlNPTi5wYXJzZShyZXNwb25zZSk7XG4gICAgICB0aGlzLnByb2R1Y3RSZW5kZXJlci5yZW5kZXJTZWFyY2hSZXN1bHRzKHRoaXMucHJvZHVjdHMpO1xuICAgICAgdGhpcy5fc2VsZWN0Rmlyc3RSZXN1bHQoKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGZvciBhZGQgcHJvZHVjdCB0byBjYXJ0IGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25BZGRQcm9kdWN0VG9DYXJ0KCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5wcm9kdWN0QWRkZWRUb0NhcnQsIChjYXJ0SW5mbykgPT4ge1xuICAgICAgRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAuY2FydExvYWRlZCwgY2FydEluZm8pO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZm9yIHJlbW92ZSBwcm9kdWN0IGZyb20gY2FydCBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uUmVtb3ZlUHJvZHVjdEZyb21DYXJ0KCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5wcm9kdWN0UmVtb3ZlZEZyb21DYXJ0LCAoY2FydEluZm8pID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRMb2FkZWQsIGNhcnRJbmZvKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0aWFsaXplcyBwcm9kdWN0IHNlbGVjdFxuICAgKlxuICAgKiBAcGFyYW0gZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0UHJvZHVjdFNlbGVjdChldmVudCkge1xuICAgIGNvbnN0IHByb2R1Y3RJZCA9IE51bWJlcigkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmZpbmQoJzpzZWxlY3RlZCcpLnZhbCgpKTtcbiAgICB0aGlzLl9zZWxlY3RQcm9kdWN0KHByb2R1Y3RJZCk7XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhbGl6ZXMgY29tYmluYXRpb24gc2VsZWN0XG4gICAqXG4gICAqIEBwYXJhbSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRDb21iaW5hdGlvblNlbGVjdChldmVudCkge1xuICAgIGNvbnN0IGNvbWJpbmF0aW9uSWQgPSBOdW1iZXIoJChldmVudC5jdXJyZW50VGFyZ2V0KS5maW5kKCc6c2VsZWN0ZWQnKS52YWwoKSk7XG4gICAgdGhpcy5fc2VsZWN0Q29tYmluYXRpb24oY29tYmluYXRpb25JZCk7XG4gIH1cblxuICAvKipcbiAgICogU2VhcmNoZXMgZm9yIHByb2R1Y3RcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zZWFyY2goc2VhcmNoUGhyYXNlKSB7XG4gICAgaWYgKHNlYXJjaFBocmFzZS5sZW5ndGggPCAzKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgaWYgKHRoaXMuYWN0aXZlU2VhcmNoUmVxdWVzdCAhPT0gbnVsbCkge1xuICAgICAgdGhpcy5hY3RpdmVTZWFyY2hSZXF1ZXN0LmFib3J0KCk7XG4gICAgfVxuXG4gICAgJC5nZXQodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX3Byb2R1Y3RzX3NlYXJjaCcpLCB7XG4gICAgICBzZWFyY2hfcGhyYXNlOiBzZWFyY2hQaHJhc2UsXG4gICAgfSkudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLnByb2R1Y3RTZWFyY2hlZCwgcmVzcG9uc2UpO1xuICAgIH0pLmNhdGNoKChyZXNwb25zZSkgPT4ge1xuICAgICAgaWYgKHJlc3BvbnNlLnN0YXR1c1RleHQgPT09ICdhYm9ydCcpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBzaG93RXJyb3JNZXNzYWdlKHJlc3BvbnNlLnJlc3BvbnNlSlNPTi5tZXNzYWdlKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0aWF0ZSBmaXJzdCByZXN1bHQgZGF0YXNldCBhZnRlciBzZWFyY2hcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zZWxlY3RGaXJzdFJlc3VsdCgpIHtcbiAgICB0aGlzLl91bnNldFByb2R1Y3QoKTtcblxuICAgIGlmICh0aGlzLnByb2R1Y3RzLmxlbmd0aCAhPT0gMCkge1xuICAgICAgdGhpcy5fc2VsZWN0UHJvZHVjdChPYmplY3Qua2V5cyh0aGlzLnByb2R1Y3RzKVswXSk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZXMgdXNlIGNhc2Ugd2hlbiBwcm9kdWN0IGlzIHNlbGVjdGVkIGZyb20gc2VhcmNoIHJlc3VsdHNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICpcbiAgICogQHBhcmFtIHByb2R1Y3RJZFxuICAgKi9cbiAgX3NlbGVjdFByb2R1Y3QocHJvZHVjdElkKSB7XG4gICAgdGhpcy5fdW5zZXRDb21iaW5hdGlvbigpO1xuXG4gICAgdGhpcy5zZWxlY3RlZFByb2R1Y3RJZCA9IHByb2R1Y3RJZDtcbiAgICBjb25zdCBwcm9kdWN0ID0gdGhpcy5wcm9kdWN0c1twcm9kdWN0SWRdO1xuXG4gICAgdGhpcy5wcm9kdWN0UmVuZGVyZXIucmVuZGVyUHJvZHVjdE1ldGFkYXRhKHByb2R1Y3QpO1xuXG4gICAgLy8gaWYgcHJvZHVjdCBoYXMgY29tYmluYXRpb25zIHNlbGVjdCB0aGUgZmlyc3QgZWxzZSBsZWF2ZSBpdCBudWxsXG4gICAgaWYgKHByb2R1Y3QuY29tYmluYXRpb25zLmxlbmd0aCAhPT0gMCkge1xuICAgICAgdGhpcy5fc2VsZWN0Q29tYmluYXRpb24oT2JqZWN0LmtleXMocHJvZHVjdC5jb21iaW5hdGlvbnMpWzBdKTtcbiAgICB9XG5cbiAgICByZXR1cm4gcHJvZHVjdDtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIHVzZSBjYXNlIHdoZW4gbmV3IGNvbWJpbmF0aW9uIGlzIHNlbGVjdGVkXG4gICAqXG4gICAqIEBwYXJhbSBjb21iaW5hdGlvbklkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2VsZWN0Q29tYmluYXRpb24oY29tYmluYXRpb25JZCkge1xuICAgIGNvbnN0IGNvbWJpbmF0aW9uID0gdGhpcy5wcm9kdWN0c1t0aGlzLnNlbGVjdGVkUHJvZHVjdElkXS5jb21iaW5hdGlvbnNbY29tYmluYXRpb25JZF07XG5cbiAgICB0aGlzLnNlbGVjdGVkQ29tYmluYXRpb25JZCA9IGNvbWJpbmF0aW9uSWQ7XG4gICAgdGhpcy5wcm9kdWN0UmVuZGVyZXIucmVuZGVyU3RvY2soY29tYmluYXRpb24uc3RvY2spO1xuXG4gICAgcmV0dXJuIGNvbWJpbmF0aW9uO1xuICB9XG5cbiAgLyoqXG4gICAqIFNldHMgdGhlIHNlbGVjdGVkIGNvbWJpbmF0aW9uIGlkIHRvIG51bGxcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF91bnNldENvbWJpbmF0aW9uKCkge1xuICAgIHRoaXMuc2VsZWN0ZWRDb21iaW5hdGlvbklkID0gbnVsbDtcbiAgfVxuXG4gIC8qKlxuICAgKiBTZXRzIHRoZSBzZWxlY3RlZCBwcm9kdWN0IGlkIHRvIG51bGxcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF91bnNldFByb2R1Y3QoKSB7XG4gICAgdGhpcy5zZWxlY3RlZFByb2R1Y3RJZCA9IG51bGw7XG4gIH1cblxuICAvKipcbiAgICogUmV0cmlldmVzIHByb2R1Y3QgZGF0YSBmcm9tIHByb2R1Y3Qgc2VhcmNoIHJlc3VsdCBibG9jayBmaWVsZHNcbiAgICpcbiAgICogQHJldHVybnMge0Zvcm1EYXRhfVxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldFByb2R1Y3REYXRhKCkge1xuICAgIGNvbnN0IGZvcm1EYXRhID0gbmV3IEZvcm1EYXRhKCk7XG5cbiAgICBmb3JtRGF0YS5hcHBlbmQoJ3Byb2R1Y3RJZCcsIHRoaXMuc2VsZWN0ZWRQcm9kdWN0SWQpO1xuICAgIGZvcm1EYXRhLmFwcGVuZCgncXVhbnRpdHknLCAkKGNyZWF0ZU9yZGVyTWFwLnF1YW50aXR5SW5wdXQpLnZhbCgpKTtcbiAgICBmb3JtRGF0YS5hcHBlbmQoJ2NvbWJpbmF0aW9uSWQnLCB0aGlzLnNlbGVjdGVkQ29tYmluYXRpb25JZCk7XG5cbiAgICB0aGlzLl9nZXRDdXN0b21GaWVsZHNEYXRhKGZvcm1EYXRhKTtcblxuICAgIHJldHVybiBmb3JtRGF0YTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXNvbHZlcyBwcm9kdWN0IGN1c3RvbWl6YXRpb24gZmllbGRzIHRvIGJlIGFkZGVkIHRvIGZvcm1EYXRhIG9iamVjdFxuICAgKlxuICAgKiBAcGFyYW0ge0Zvcm1EYXRhfSBmb3JtRGF0YVxuICAgKlxuICAgKiBAcmV0dXJucyB7Rm9ybURhdGF9XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZ2V0Q3VzdG9tRmllbGRzRGF0YShmb3JtRGF0YSkge1xuICAgIGNvbnN0ICRjdXN0b21GaWVsZHMgPSAkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RDdXN0b21JbnB1dCk7XG5cbiAgICAkY3VzdG9tRmllbGRzLmVhY2goKGtleSwgZmllbGQpID0+IHtcbiAgICAgIGNvbnN0ICRmaWVsZCA9ICQoZmllbGQpO1xuICAgICAgY29uc3QgbmFtZSA9ICRmaWVsZC5hdHRyKCduYW1lJyk7XG5cbiAgICAgIGlmICgkZmllbGQuYXR0cigndHlwZScpID09PSAnZmlsZScpIHtcbiAgICAgICAgZm9ybURhdGEuYXBwZW5kKG5hbWUsICRmaWVsZFswXS5maWxlc1swXSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBmb3JtRGF0YS5hcHBlbmQobmFtZSwgJGZpZWxkLnZhbCgpKTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgIHJldHVybiBmb3JtRGF0YTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL3Byb2R1Y3QtbWFuYWdlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBjcmVhdGVPcmRlclBhZ2VNYXAgZnJvbSAnLi9jcmVhdGUtb3JkZXItbWFwJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIE1hbnVwdWxhdGVzIFVJIG9mIFNoaXBwaW5nIGJsb2NrIGluIE9yZGVyIGNyZWF0aW9uIHBhZ2VcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU2hpcHBpbmdSZW5kZXJlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lciA9ICQoY3JlYXRlT3JkZXJQYWdlTWFwLnNoaXBwaW5nQmxvY2spO1xuICAgIHRoaXMuJGZvcm0gPSAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5zaGlwcGluZ0Zvcm0pO1xuICAgIHRoaXMuJG5vQ2FycmllckJsb2NrID0gJChjcmVhdGVPcmRlclBhZ2VNYXAubm9DYXJyaWVyQmxvY2spO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBzaGlwcGluZ1xuICAgKiBAcGFyYW0ge0Jvb2xlYW59IGVtcHR5Q2FydFxuICAgKi9cbiAgcmVuZGVyKHNoaXBwaW5nLCBlbXB0eUNhcnQpIHtcbiAgICBjb25zdCBzaGlwcGluZ0lzQXZhaWxhYmxlID0gdHlwZW9mIHNoaXBwaW5nICE9PSAndW5kZWZpbmVkJyAmJiBzaGlwcGluZyAhPT0gbnVsbCAmJiBzaGlwcGluZy5sZW5ndGggIT09IDA7XG5cbiAgICBpZiAoZW1wdHlDYXJ0KSB7XG4gICAgICB0aGlzLl9oaWRlQ29udGFpbmVyKCk7XG4gICAgfSBlbHNlIGlmIChzaGlwcGluZ0lzQXZhaWxhYmxlKSB7XG4gICAgICB0aGlzLl9kaXNwbGF5Rm9ybShzaGlwcGluZyk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMuX2Rpc3BsYXlOb0NhcnJpZXJzV2FybmluZygpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93IGZvcm0gYmxvY2sgd2l0aCByZW5kZXJlZCBkZWxpdmVyeSBvcHRpb25zIGluc3RlYWQgb2Ygd2FybmluZyBtZXNzYWdlXG4gICAqXG4gICAqIEBwYXJhbSBzaGlwcGluZ1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2Rpc3BsYXlGb3JtKHNoaXBwaW5nKSB7XG4gICAgdGhpcy5faGlkZU5vQ2FycmllckJsb2NrKCk7XG4gICAgdGhpcy5fcmVuZGVyRGVsaXZlcnlPcHRpb25zKHNoaXBwaW5nLmRlbGl2ZXJ5T3B0aW9ucywgc2hpcHBpbmcuc2VsZWN0ZWRDYXJyaWVySWQpO1xuICAgIHRoaXMuX3JlbmRlclRvdGFsU2hpcHBpbmcoc2hpcHBpbmcuc2hpcHBpbmdQcmljZSk7XG4gICAgdGhpcy5fc2hvd0Zvcm0oKTtcbiAgICB0aGlzLl9zaG93Q29udGFpbmVyKCk7XG4gIH1cblxuICAvKipcbiAgICogU2hvdyB3YXJuaW5nIG1lc3NhZ2UgdGhhdCBubyBjYXJyaWVycyBhcmUgYXZhaWxhYmxlIGFuZCBoaWRlIGZvcm0gYmxvY2tcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9kaXNwbGF5Tm9DYXJyaWVyc1dhcm5pbmcoKSB7XG4gICAgdGhpcy5fc2hvd0NvbnRhaW5lcigpO1xuICAgIHRoaXMuX2hpZGVGb3JtKCk7XG4gICAgdGhpcy5fc2hvd05vQ2FycmllckJsb2NrKCk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBkZWxpdmVyeSBvcHRpb25zIHNlbGVjdGlvbiBibG9ja1xuICAgKlxuICAgKiBAcGFyYW0gZGVsaXZlcnlPcHRpb25zXG4gICAqIEBwYXJhbSBzZWxlY3RlZFZhbFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbmRlckRlbGl2ZXJ5T3B0aW9ucyhkZWxpdmVyeU9wdGlvbnMsIHNlbGVjdGVkVmFsKSB7XG4gICAgY29uc3QgJGRlbGl2ZXJ5T3B0aW9uU2VsZWN0ID0gJChjcmVhdGVPcmRlclBhZ2VNYXAuZGVsaXZlcnlPcHRpb25TZWxlY3QpO1xuICAgICRkZWxpdmVyeU9wdGlvblNlbGVjdC5lbXB0eSgpO1xuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gT2JqZWN0LmtleXMoZGVsaXZlcnlPcHRpb25zKSkge1xuICAgICAgY29uc3Qgb3B0aW9uID0gZGVsaXZlcnlPcHRpb25zW2tleV07XG5cbiAgICAgIGNvbnN0IGRlbGl2ZXJ5T3B0aW9uID0ge1xuICAgICAgICB2YWx1ZTogb3B0aW9uLmNhcnJpZXJJZCxcbiAgICAgICAgdGV4dDogYCR7b3B0aW9uLmNhcnJpZXJOYW1lfSAtICR7b3B0aW9uLmNhcnJpZXJEZWxheX1gLFxuICAgICAgfTtcblxuICAgICAgaWYgKHNlbGVjdGVkVmFsID09PSBkZWxpdmVyeU9wdGlvbi52YWx1ZSkge1xuICAgICAgICBkZWxpdmVyeU9wdGlvbi5zZWxlY3RlZCA9ICdzZWxlY3RlZCc7XG4gICAgICB9XG5cbiAgICAgICRkZWxpdmVyeU9wdGlvblNlbGVjdC5hcHBlbmQoJCgnPG9wdGlvbj4nLCBkZWxpdmVyeU9wdGlvbikpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIGR5bmFtaWMgdmFsdWUgb2Ygc2hpcHBpbmcgcHJpY2VcbiAgICpcbiAgICogQHBhcmFtIHNoaXBwaW5nUHJpY2VcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW5kZXJUb3RhbFNoaXBwaW5nKHNoaXBwaW5nUHJpY2UpIHtcbiAgICBjb25zdCAkdG90YWxTaGlwcGluZ0ZpZWxkID0gJChjcmVhdGVPcmRlclBhZ2VNYXAudG90YWxTaGlwcGluZ0ZpZWxkKTtcbiAgICAkdG90YWxTaGlwcGluZ0ZpZWxkLmVtcHR5KCk7XG5cbiAgICAkdG90YWxTaGlwcGluZ0ZpZWxkLmFwcGVuZChzaGlwcGluZ1ByaWNlKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93IHdob2xlIHNoaXBwaW5nIGNvbnRhaW5lclxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dDb250YWluZXIoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlIHdob2xlIHNoaXBwaW5nIGNvbnRhaW5lclxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGVDb250YWluZXIoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93IGZvcm0gYmxvY2tcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93Rm9ybSgpIHtcbiAgICB0aGlzLiRmb3JtLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlIGZvcm0gYmxvY2tcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlRm9ybSgpIHtcbiAgICB0aGlzLiRmb3JtLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93IHdhcm5pbmcgbWVzc2FnZSBibG9jayB3aGljaCB3YXJucyB0aGF0IG5vIGNhcnJpZXJzIGFyZSBhdmFpbGFibGVcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93Tm9DYXJyaWVyQmxvY2soKSB7XG4gICAgdGhpcy4kbm9DYXJyaWVyQmxvY2sucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGUgd2FybmluZyBtZXNzYWdlIGJsb2NrIHdoaWNoIHdhcm5zIHRoYXQgbm8gY2FycmllcnMgYXJlIGF2YWlsYWJsZVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGVOb0NhcnJpZXJCbG9jaygpIHtcbiAgICB0aGlzLiRub0NhcnJpZXJCbG9jay5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9zaGlwcGluZy1yZW5kZXJlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbi8qKlxuICogRW5jYXBzdWxhdGVzIGpzIGV2ZW50cyB1c2VkIGluIGNyZWF0ZSBvcmRlciBwYWdlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IHtcbiAgLy8gd2hlbiBjdXN0b21lciBzZWFyY2ggYWN0aW9uIGlzIGRvbmVcbiAgY3VzdG9tZXJTZWFyY2hlZDogJ2N1c3RvbWVyU2VhcmNoZWQnLFxuICAvLyB3aGVuIG5ldyBjdXN0b21lciBpcyBzZWxlY3RlZFxuICBjdXN0b21lclNlbGVjdGVkOiAnY3VzdG9tZXJTZWxlY3RlZCcsXG4gIC8vIHdoZW4gbmV3IGNhcnQgaXMgbG9hZGVkLCBubyBtYXR0ZXIgaWYgaXRzIGVtcHR5LCBzZWxlY3RlZCBmcm9tIGNhcnRzIGxpc3Qgb3IgZHVwbGljYXRlZCBieSBvcmRlci5cbiAgY2FydExvYWRlZDogJ2NhcnRMb2FkZWQnLFxuICAvLyB3aGVuIGNhcnQgYWRkcmVzc2VzIGluZm9ybWF0aW9uIGhhcyBiZWVuIGNoYW5nZWRcbiAgY2FydEFkZHJlc3Nlc0NoYW5nZWQ6ICdjYXJ0QWRkcmVzc2VzQ2hhbmdlZCcsXG4gIC8vIHdoZW4gY2FydCBkZWxpdmVyeSBvcHRpb24gaGFzIGJlZW4gY2hhbmdlZFxuICBjYXJ0RGVsaXZlcnlPcHRpb25DaGFuZ2VkOiAnY2FydERlbGl2ZXJ5T3B0aW9uQ2hhbmdlZCcsXG4gIC8vIHdoZW4gY2FydCBmcmVlIHNoaXBwaW5nIHZhbHVlIGhhcyBiZWVuIGNoYW5nZWRcbiAgY2FydEZyZWVTaGlwcGluZ1NldDogJ2NhcnRGcmVlU2hpcHBpbmdTZXQnLFxuICAvLyB3aGVuIGNhcnQgcnVsZXMgc2VhcmNoIGFjdGlvbiBpcyBkb25lXG4gIGNhcnRSdWxlU2VhcmNoZWQ6ICdjYXJ0UnVsZVNlYXJjaGVkJyxcbiAgLy8gd2hlbiBjYXJ0IHJ1bGUgaXMgcmVtb3ZlZCBmcm9tIGNhcnRcbiAgY2FydFJ1bGVSZW1vdmVkOiAnY2FydFJ1bGVSZW1vdmVkJyxcbiAgLy8gd2hlbiBjYXJ0IHJ1bGUgaXMgYWRkZWQgdG8gY2FydFxuICBjYXJ0UnVsZUFkZGVkOiAnY2FydFJ1bGVBZGRlZCcsXG4gIC8vIHdoZW4gY2FydCBydWxlIGNhbm5vdCBiZSBhZGRlZCB0byBjYXJ0XG4gIGNhcnRSdWxlRmFpbGVkVG9BZGQ6ICdjYXJ0UnVsZUZhaWxlZFRvQWRkJyxcbiAgLy8gd2hlbiBwcm9kdWN0IHNlYXJjaCBhY3Rpb24gaXMgZG9uZVxuICBwcm9kdWN0U2VhcmNoZWQ6ICdwcm9kdWN0U2VhcmNoZWQnLFxuICAvLyB3aGVuIHByb2R1Y3QgaXMgYWRkZWQgdG8gY2FydFxuICBwcm9kdWN0QWRkZWRUb0NhcnQ6ICdwcm9kdWN0QWRkZWRUb0NhcnQnLFxuICAvLyB3aGVuIHByb2R1Y3QgaXMgcmVtb3ZlZCBmcm9tIGNhcnRcbiAgcHJvZHVjdFJlbW92ZWRGcm9tQ2FydDogJ3Byb2R1Y3RSZW1vdmVkRnJvbUNhcnQnLFxufTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9ldmVudC1tYXAuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgUm91dGVyIGZyb20gJ0Bjb21wb25lbnRzL3JvdXRlcic7XG5pbXBvcnQge0V2ZW50RW1pdHRlcn0gZnJvbSAnQGNvbXBvbmVudHMvZXZlbnQtZW1pdHRlcic7XG5pbXBvcnQgZXZlbnRNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL2NyZWF0ZS9ldmVudC1tYXAnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogUHJvdmlkZXMgYWpheCBjYWxscyBmb3IgY2FydCBlZGl0aW5nIGFjdGlvbnNcbiAqIEVhY2ggbWV0aG9kIGVtaXRzIGFuIGV2ZW50IHdpdGggdXBkYXRlZCBjYXJ0IGluZm9ybWF0aW9uIGFmdGVyIHN1Y2Nlc3MuXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENhcnRFZGl0b3Ige1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDaGFuZ2VzIGNhcnQgYWRkcmVzc2VzXG4gICAqXG4gICAqIEBwYXJhbSB7TnVtYmVyfSBjYXJ0SWRcbiAgICogQHBhcmFtIHtPYmplY3R9IGFkZHJlc3Nlc1xuICAgKi9cbiAgY2hhbmdlQ2FydEFkZHJlc3NlcyhjYXJ0SWQsIGFkZHJlc3Nlcykge1xuICAgICQucG9zdCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY2FydHNfZWRpdF9hZGRyZXNzZXMnLCB7Y2FydElkfSksIGFkZHJlc3NlcykudGhlbigoY2FydEluZm8pID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRBZGRyZXNzZXNDaGFuZ2VkLCBjYXJ0SW5mbyk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTW9kaWZpZXMgY2FydCBkZWxpdmVyeSBvcHRpb25cbiAgICpcbiAgICogQHBhcmFtIHtOdW1iZXJ9IGNhcnRJZFxuICAgKiBAcGFyYW0ge051bWJlcn0gdmFsdWVcbiAgICovXG4gIGNoYW5nZURlbGl2ZXJ5T3B0aW9uKGNhcnRJZCwgdmFsdWUpIHtcbiAgICAkLnBvc3QodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX2NhcnRzX2VkaXRfY2FycmllcicsIHtjYXJ0SWR9KSwge1xuICAgICAgY2FycmllcklkOiB2YWx1ZSxcbiAgICB9KS50aGVuKChjYXJ0SW5mbykgPT4ge1xuICAgICAgRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAuY2FydERlbGl2ZXJ5T3B0aW9uQ2hhbmdlZCwgY2FydEluZm8pO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIENoYW5nZXMgY2FydCBmcmVlIHNoaXBwaW5nIHZhbHVlXG4gICAqXG4gICAqIEBwYXJhbSB7TnVtYmVyfSBjYXJ0SWRcbiAgICogQHBhcmFtIHtCb29sZWFufSB2YWx1ZVxuICAgKi9cbiAgc2V0RnJlZVNoaXBwaW5nKGNhcnRJZCwgdmFsdWUpIHtcbiAgICAkLnBvc3QodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX2NhcnRzX3NldF9mcmVlX3NoaXBwaW5nJywge2NhcnRJZH0pLCB7XG4gICAgICBmcmVlU2hpcHBpbmc6IHZhbHVlLFxuICAgIH0pLnRoZW4oKGNhcnRJbmZvKSA9PiB7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5jYXJ0RnJlZVNoaXBwaW5nU2V0LCBjYXJ0SW5mbyk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQWRkcyBjYXJ0IHJ1bGUgdG8gY2FydFxuICAgKlxuICAgKiBAcGFyYW0ge051bWJlcn0gY2FydFJ1bGVJZFxuICAgKiBAcGFyYW0ge051bWJlcn0gY2FydElkXG4gICAqL1xuICBhZGRDYXJ0UnVsZVRvQ2FydChjYXJ0UnVsZUlkLCBjYXJ0SWQpIHtcbiAgICAkLnBvc3QodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX2NhcnRzX2FkZF9jYXJ0X3J1bGUnLCB7Y2FydElkfSksIHtcbiAgICAgIGNhcnRSdWxlSWQsXG4gICAgfSkudGhlbigoY2FydEluZm8pID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRSdWxlQWRkZWQsIGNhcnRJbmZvKTtcbiAgICB9KS5jYXRjaCgocmVzcG9uc2UpID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRSdWxlRmFpbGVkVG9BZGQsIHJlc3BvbnNlLnJlc3BvbnNlSlNPTi5tZXNzYWdlKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW1vdmVzIGNhcnQgcnVsZSBmcm9tIGNhcnRcbiAgICpcbiAgICogQHBhcmFtIHtOdW1iZXJ9IGNhcnRSdWxlSWRcbiAgICogQHBhcmFtIHtOdW1iZXJ9IGNhcnRJZFxuICAgKi9cbiAgcmVtb3ZlQ2FydFJ1bGVGcm9tQ2FydChjYXJ0UnVsZUlkLCBjYXJ0SWQpIHtcbiAgICAkLnBvc3QodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX2NhcnRzX2RlbGV0ZV9jYXJ0X3J1bGUnLCB7XG4gICAgICBjYXJ0SWQsXG4gICAgICBjYXJ0UnVsZUlkLFxuICAgIH0pKS50aGVuKChjYXJ0SW5mbykgPT4ge1xuICAgICAgRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAuY2FydFJ1bGVSZW1vdmVkLCBjYXJ0SW5mbyk7XG4gICAgfSkuY2F0Y2goKHJlc3BvbnNlKSA9PiB7XG4gICAgICBzaG93RXJyb3JNZXNzYWdlKHJlc3BvbnNlLnJlc3BvbnNlSlNPTi5tZXNzYWdlKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBZGRzIHByb2R1Y3QgdG8gY2FydFxuICAgKlxuICAgKiBAcGFyYW0ge051bWJlcn0gY2FydElkXG4gICAqIEBwYXJhbSB7Rm9ybURhdGF9IHByb2R1Y3RcbiAgICovXG4gIGFkZFByb2R1Y3QoY2FydElkLCBwcm9kdWN0KSB7XG4gICAgJC5hamF4KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9jYXJ0c19hZGRfcHJvZHVjdCcsIHtjYXJ0SWR9KSwge1xuICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgICBkYXRhOiBwcm9kdWN0LFxuICAgICAgcHJvY2Vzc0RhdGE6IGZhbHNlLFxuICAgICAgY29udGVudFR5cGU6IGZhbHNlLFxuICAgIH0pLnRoZW4oKGNhcnRJbmZvKSA9PiB7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5wcm9kdWN0QWRkZWRUb0NhcnQsIGNhcnRJbmZvKTtcbiAgICB9KS5jYXRjaCgocmVzcG9uc2UpID0+IHtcbiAgICAgIHNob3dFcnJvck1lc3NhZ2UocmVzcG9uc2UucmVzcG9uc2VKU09OLm1lc3NhZ2UpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbW92ZXMgcHJvZHVjdCBmcm9tIGNhcnRcbiAgICpcbiAgICogQHBhcmFtIHtOdW1iZXJ9IGNhcnRJZFxuICAgKiBAcGFyYW0ge09iamVjdH0gcHJvZHVjdFxuICAgKi9cbiAgcmVtb3ZlUHJvZHVjdEZyb21DYXJ0KGNhcnRJZCwgcHJvZHVjdCkge1xuICAgICQucG9zdCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY2FydHNfZGVsZXRlX3Byb2R1Y3QnLCB7Y2FydElkfSksIHtcbiAgICAgIHByb2R1Y3RJZDogcHJvZHVjdC5wcm9kdWN0SWQsXG4gICAgICBhdHRyaWJ1dGVJZDogcHJvZHVjdC5hdHRyaWJ1dGVJZCxcbiAgICAgIGN1c3RvbWl6YXRpb25JZDogcHJvZHVjdC5jdXN0b21pemF0aW9uSWQsXG4gICAgfSkudGhlbigoY2FydEluZm8pID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLnByb2R1Y3RSZW1vdmVkRnJvbUNhcnQsIGNhcnRJbmZvKTtcbiAgICB9KS5jYXRjaCgocmVzcG9uc2UpID0+IHtcbiAgICAgIHNob3dFcnJvck1lc3NhZ2UocmVzcG9uc2UucmVzcG9uc2VKU09OLm1lc3NhZ2UpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY2FydC1lZGl0b3IuanMiLCIndXNlIHN0cmljdCc7dmFyIF9leHRlbmRzPU9iamVjdC5hc3NpZ258fGZ1bmN0aW9uKGEpe2Zvcih2YXIgYixjPTE7Yzxhcmd1bWVudHMubGVuZ3RoO2MrKylmb3IodmFyIGQgaW4gYj1hcmd1bWVudHNbY10sYilPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwoYixkKSYmKGFbZF09YltkXSk7cmV0dXJuIGF9LF90eXBlb2Y9J2Z1bmN0aW9uJz09dHlwZW9mIFN5bWJvbCYmJ3N5bWJvbCc9PXR5cGVvZiBTeW1ib2wuaXRlcmF0b3I/ZnVuY3Rpb24oYSl7cmV0dXJuIHR5cGVvZiBhfTpmdW5jdGlvbihhKXtyZXR1cm4gYSYmJ2Z1bmN0aW9uJz09dHlwZW9mIFN5bWJvbCYmYS5jb25zdHJ1Y3Rvcj09PVN5bWJvbCYmYSE9PVN5bWJvbC5wcm90b3R5cGU/J3N5bWJvbCc6dHlwZW9mIGF9O2Z1bmN0aW9uIF9jbGFzc0NhbGxDaGVjayhhLGIpe2lmKCEoYSBpbnN0YW5jZW9mIGIpKXRocm93IG5ldyBUeXBlRXJyb3IoJ0Nhbm5vdCBjYWxsIGEgY2xhc3MgYXMgYSBmdW5jdGlvbicpfXZhciBSb3V0aW5nPWZ1bmN0aW9uIGEoKXt2YXIgYj10aGlzO19jbGFzc0NhbGxDaGVjayh0aGlzLGEpLHRoaXMuc2V0Um91dGVzPWZ1bmN0aW9uKGEpe2Iucm91dGVzUm91dGluZz1hfHxbXX0sdGhpcy5nZXRSb3V0ZXM9ZnVuY3Rpb24oKXtyZXR1cm4gYi5yb3V0ZXNSb3V0aW5nfSx0aGlzLnNldEJhc2VVcmw9ZnVuY3Rpb24oYSl7Yi5jb250ZXh0Um91dGluZy5iYXNlX3VybD1hfSx0aGlzLmdldEJhc2VVcmw9ZnVuY3Rpb24oKXtyZXR1cm4gYi5jb250ZXh0Um91dGluZy5iYXNlX3VybH0sdGhpcy5zZXRQcmVmaXg9ZnVuY3Rpb24oYSl7Yi5jb250ZXh0Um91dGluZy5wcmVmaXg9YX0sdGhpcy5zZXRTY2hlbWU9ZnVuY3Rpb24oYSl7Yi5jb250ZXh0Um91dGluZy5zY2hlbWU9YX0sdGhpcy5nZXRTY2hlbWU9ZnVuY3Rpb24oKXtyZXR1cm4gYi5jb250ZXh0Um91dGluZy5zY2hlbWV9LHRoaXMuc2V0SG9zdD1mdW5jdGlvbihhKXtiLmNvbnRleHRSb3V0aW5nLmhvc3Q9YX0sdGhpcy5nZXRIb3N0PWZ1bmN0aW9uKCl7cmV0dXJuIGIuY29udGV4dFJvdXRpbmcuaG9zdH0sdGhpcy5idWlsZFF1ZXJ5UGFyYW1zPWZ1bmN0aW9uKGEsYyxkKXt2YXIgZT1uZXcgUmVnRXhwKC9cXFtdJC8pO2MgaW5zdGFuY2VvZiBBcnJheT9jLmZvckVhY2goZnVuY3Rpb24oYyxmKXtlLnRlc3QoYSk/ZChhLGMpOmIuYnVpbGRRdWVyeVBhcmFtcyhhKydbJysoJ29iamVjdCc9PT0oJ3VuZGVmaW5lZCc9PXR5cGVvZiBjPyd1bmRlZmluZWQnOl90eXBlb2YoYykpP2Y6JycpKyddJyxjLGQpfSk6J29iamVjdCc9PT0oJ3VuZGVmaW5lZCc9PXR5cGVvZiBjPyd1bmRlZmluZWQnOl90eXBlb2YoYykpP09iamVjdC5rZXlzKGMpLmZvckVhY2goZnVuY3Rpb24oZSl7cmV0dXJuIGIuYnVpbGRRdWVyeVBhcmFtcyhhKydbJytlKyddJyxjW2VdLGQpfSk6ZChhLGMpfSx0aGlzLmdldFJvdXRlPWZ1bmN0aW9uKGEpe3ZhciBjPWIuY29udGV4dFJvdXRpbmcucHJlZml4K2E7aWYoISFiLnJvdXRlc1JvdXRpbmdbY10pcmV0dXJuIGIucm91dGVzUm91dGluZ1tjXTtlbHNlIGlmKCFiLnJvdXRlc1JvdXRpbmdbYV0pdGhyb3cgbmV3IEVycm9yKCdUaGUgcm91dGUgXCInK2ErJ1wiIGRvZXMgbm90IGV4aXN0LicpO3JldHVybiBiLnJvdXRlc1JvdXRpbmdbYV19LHRoaXMuZ2VuZXJhdGU9ZnVuY3Rpb24oYSxjLGQpe3ZhciBlPWIuZ2V0Um91dGUoYSksZj1jfHx7fSxnPV9leHRlbmRzKHt9LGYpLGg9J19zY2hlbWUnLGk9Jycsaj0hMCxrPScnO2lmKChlLnRva2Vuc3x8W10pLmZvckVhY2goZnVuY3Rpb24oYil7aWYoJ3RleHQnPT09YlswXSlyZXR1cm4gaT1iWzFdK2ksdm9pZChqPSExKTtpZigndmFyaWFibGUnPT09YlswXSl7dmFyIGM9KGUuZGVmYXVsdHN8fHt9KVtiWzNdXTtpZighMT09anx8IWN8fChmfHx7fSlbYlszXV0mJmZbYlszXV0hPT1lLmRlZmF1bHRzW2JbM11dKXt2YXIgZDtpZigoZnx8e30pW2JbM11dKWQ9ZltiWzNdXSxkZWxldGUgZ1tiWzNdXTtlbHNlIGlmKGMpZD1lLmRlZmF1bHRzW2JbM11dO2Vsc2V7aWYoailyZXR1cm47dGhyb3cgbmV3IEVycm9yKCdUaGUgcm91dGUgXCInK2ErJ1wiIHJlcXVpcmVzIHRoZSBwYXJhbWV0ZXIgXCInK2JbM10rJ1wiLicpfXZhciBoPSEwPT09ZHx8ITE9PT1kfHwnJz09PWQ7aWYoIWh8fCFqKXt2YXIgaz1lbmNvZGVVUklDb21wb25lbnQoZCkucmVwbGFjZSgvJTJGL2csJy8nKTsnbnVsbCc9PT1rJiZudWxsPT09ZCYmKGs9JycpLGk9YlsxXStrK2l9aj0hMX1lbHNlIGMmJmRlbGV0ZSBnW2JbM11dO3JldHVybn10aHJvdyBuZXcgRXJyb3IoJ1RoZSB0b2tlbiB0eXBlIFwiJytiWzBdKydcIiBpcyBub3Qgc3VwcG9ydGVkLicpfSksJyc9PWkmJihpPScvJyksKGUuaG9zdHRva2Vuc3x8W10pLmZvckVhY2goZnVuY3Rpb24oYSl7dmFyIGI7cmV0dXJuJ3RleHQnPT09YVswXT92b2lkKGs9YVsxXStrKTp2b2lkKCd2YXJpYWJsZSc9PT1hWzBdJiYoKGZ8fHt9KVthWzNdXT8oYj1mW2FbM11dLGRlbGV0ZSBnW2FbM11dKTplLmRlZmF1bHRzW2FbM11dJiYoYj1lLmRlZmF1bHRzW2FbM11dKSxrPWFbMV0rYitrKSl9KSxpPWIuY29udGV4dFJvdXRpbmcuYmFzZV91cmwraSxlLnJlcXVpcmVtZW50c1toXSYmYi5nZXRTY2hlbWUoKSE9PWUucmVxdWlyZW1lbnRzW2hdP2k9ZS5yZXF1aXJlbWVudHNbaF0rJzovLycrKGt8fGIuZ2V0SG9zdCgpKStpOmsmJmIuZ2V0SG9zdCgpIT09az9pPWIuZ2V0U2NoZW1lKCkrJzovLycraytpOiEwPT09ZCYmKGk9Yi5nZXRTY2hlbWUoKSsnOi8vJytiLmdldEhvc3QoKStpKSwwPE9iamVjdC5rZXlzKGcpLmxlbmd0aCl7dmFyIGw9W10sbT1mdW5jdGlvbihhLGIpe3ZhciBjPWI7Yz0nZnVuY3Rpb24nPT10eXBlb2YgYz9jKCk6YyxjPW51bGw9PT1jPycnOmMsbC5wdXNoKGVuY29kZVVSSUNvbXBvbmVudChhKSsnPScrZW5jb2RlVVJJQ29tcG9uZW50KGMpKX07T2JqZWN0LmtleXMoZykuZm9yRWFjaChmdW5jdGlvbihhKXtyZXR1cm4gYi5idWlsZFF1ZXJ5UGFyYW1zKGEsZ1thXSxtKX0pLGk9aSsnPycrbC5qb2luKCcmJykucmVwbGFjZSgvJTIwL2csJysnKX1yZXR1cm4gaX0sdGhpcy5zZXREYXRhPWZ1bmN0aW9uKGEpe2Iuc2V0QmFzZVVybChhLmJhc2VfdXJsKSxiLnNldFJvdXRlcyhhLnJvdXRlcyksJ3ByZWZpeCdpbiBhJiZiLnNldFByZWZpeChhLnByZWZpeCksYi5zZXRIb3N0KGEuaG9zdCksYi5zZXRTY2hlbWUoYS5zY2hlbWUpfSx0aGlzLmNvbnRleHRSb3V0aW5nPXtiYXNlX3VybDonJyxwcmVmaXg6JycsaG9zdDonJyxzY2hlbWU6Jyd9fTttb2R1bGUuZXhwb3J0cz1uZXcgUm91dGluZztcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vZm9zLXJvdXRpbmcvZGlzdC9yb3V0aW5nLmpzXG4vLyBtb2R1bGUgaWQgPSA4MlxuLy8gbW9kdWxlIGNodW5rcyA9IDE0Il0sInNvdXJjZVJvb3QiOiIifQ==