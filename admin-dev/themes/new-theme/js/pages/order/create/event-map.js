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
export default {
  // when customer search action is done
  customerSearched: 'customerSearched',
  // when new customer is selected
  customerSelected: 'customerSelected',
  // when no customers found by search
  customersNotFound: 'customersNotFound',
  // when new cart is loaded, no matter if its empty, selected from carts list or duplicated by order.
  cartLoaded: 'cartLoaded',
  // when cart currency has been changed
  cartCurrencyChanged: 'cartCurrencyChanged',
  // when cart currency changing fails
  cartCurrencyChangeFailed: 'cartCurrencyChangeFailed',
  // when cart language has been changed
  cartLanguageChanged: 'cartLanguageChanged',
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
  // when adding product to cart fails
  productAddToCartFailed: 'productAddToCartFailed',
  // when product is removed from cart
  productRemovedFromCart: 'productRemovedFromCart',
  // when product in cart price has been changed
  productPriceChanged: 'productPriceChanged',
  // when product quantity in cart has been changed
  productQtyChanged: 'productQtyChanged',
  // when changing product quantity in cart failed
  productQtyChangeFailed: 'productQtyChangeFailed',
  // when order process email has been sent to customer
  processOrderEmailSent: 'processOrderEmailSent',
  // when order process email sending failed
  processOrderEmailFailed: 'processOrderEmailFailed',
};
