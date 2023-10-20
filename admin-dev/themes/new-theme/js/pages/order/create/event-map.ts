/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Encapsulates js events used in create order page
 */
export default {
  // when customer search action is done
  customerSearched: 'OrderCreateCustomerSearched',
  // when new customer is selected
  customerSelected: 'OrderCreateCustomerSelected',
  // when no customers found by search
  customersNotFound: 'OrderCreateSearchCustomerNotFound',
  // when new cart is loaded,
  //  no matter if its empty, selected from carts list or duplicated by order.
  cartLoaded: 'OrderCreateCartLoaded',
  // when cart currency has been changed
  cartCurrencyChanged: 'OrderCreateCartCurrencyChanged',
  // when cart currency changing fails
  cartCurrencyChangeFailed: 'OrderCreateCartCurrencyChangeFailed',
  // when cart language has been changed
  cartLanguageChanged: 'OrderCreateCartLanguageChanged',
  // when cart addresses information has been changed
  cartAddressesChanged: 'OrderCreateCartAddressesChanged',
  // when cart delivery option has been changed
  cartDeliveryOptionChanged: 'OrderCreateCartDeliveryOptionChanged',
  // when cart delivery setting has been changed
  cartDeliverySettingChanged: 'OrderCreateCartDeliverySettingChangedSet',
  // when cart rules search action is done
  cartRuleSearched: 'OrderCreateCartRuleSearched',
  // when cart rule is removed from cart
  cartRuleRemoved: 'OrderCreateCartRuleRemoved',
  // when cart rule is added to cart
  cartRuleAdded: 'OrderCreateCartRuleAdded',
  // when cart rule cannot be added to cart
  cartRuleFailedToAdd: 'OrderCreateCartRuleFailedToAdd',
  // when product search action is done
  productSearched: 'OrderCreateProductSearched',
  // when product is added to cart
  productAddedToCart: 'OrderCreateProductAddedToCart',
  // when adding product to cart fails
  productAddToCartFailed: 'OrderCreateProductAddToCartFailed',
  // when product is removed from cart
  productRemovedFromCart: 'OrderCreateProductRemovedFromCart',
  // when product in cart price has been changed
  productPriceChanged: 'OrderCreateProductPriceChanged',
  // when product quantity in cart has been changed
  productQtyChanged: 'OrderCreateProductQtyChanged',
  // when changing product quantity in cart failed
  productQtyChangeFailed: 'OrderCreateProductQtyChangeFailed',
  // when order process email has been sent to customer
  processOrderEmailSent: 'OrderCreateProcessOrderEmailSent',
  // when order process email sending failed
  processOrderEmailFailed: 'OrderCreateProcessOrderEmailFailed',
};
