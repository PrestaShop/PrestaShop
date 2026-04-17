/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
