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
  // when new cart is loaded, no matter if its empty, selected from carts list or duplicated by order.
  cartLoaded: 'cartLoaded',
  // when customer carts list is loaded.
  customerCartsLoaded: 'customerCartsLoaded',
  // when customer orders list is loaded
  customerOrdersLoaded: 'customerOrdersLoaded',
  // when cart addresses information has been changed
  cartAddressesChanged: 'cartAddressesChanged',
  // when cart delivery option has been changed
  cartDeliveryOptionChanged: 'cartDeliveryOptionChanged',
  // when cart free shipping value has been changed
  cartFreeShippingSet: 'cartFreeShippingSet',
  // when cart rule is removed from cart
  cartRuleRemoved: 'cartRuleRemoved',
  // when cart rule is added to cart
  cartRuleAdded: 'cartRuleAdded',
  // when cart rule cannot be added to cart
  cartRuleFailedToAdd: 'cartRuleFailedToAdd',
  // when product is added to cart
  productAddedToCart: 'productAddedToCart',
};
