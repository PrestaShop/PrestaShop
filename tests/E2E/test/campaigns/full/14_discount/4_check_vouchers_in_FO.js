const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {CheckoutOrderPage} = require('../../../selectors/FO/order_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const commonDiscountScenarios = require('../../common_scenarios/discount');
const commonScenarios = require('../../common_scenarios/product');
let promise = Promise.resolve();

let cartRuleData = [
  {
    name: 'Percent1 50%',
    customer_email: 'pub@prestashop.com',
    minimum_amount: '20',
    type: 'percent',
    reduction: '50'
  },
  {
    name: 'Percent2 50%',
    customer_email: 'pub@prestashop.com',
    minimum_amount: '20',
    type: 'percent',
    reduction: '50'
  },
  {
    name: 'Amount €20',
    customer_email: 'pub@prestashop.com',
    minimum_amount: '20',
    type: 'amount',
    reduction: '20'
  }
];

let productData = {
  name: 'PV',
  quantity: "50",
  price: '20',
  image_name: 'image_test.jpg',
  reference: 'product for the voucher'
};

scenario('Create a new "Cart Rule" in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'discount');
  for (let i = 0; i < cartRuleData.length; i++) {
    commonDiscountScenarios.createCartRule(cartRuleData[i], 'codePromo' + (i+1));
    commonDiscountScenarios.checkCartRule(cartRuleData[i], 'codePromo' + (i+1));
  }
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'discount');
}, 'discount', true);

scenario('Create product in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'product/product');
  commonScenarios.createProduct(AddProductPage, productData);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'product/product');
}, 'product/product', true);

/**
 * This scenario is based on the bug described in this ticket
 * http://forge.prestashop.com/browse/BOOM-2518
 **/

scenario('Check the total price after applying vouchers in the Front Office', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'discount');
  scenario('Check the total price of the shopping cart', client => {
    test('should change front office language to english', () => client.changeLanguage('english'));
    test('should search for the product "A"', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
    test('should set the "Quantity" input', () => client.waitAndSetValue(productPage.first_product_quantity, 3));
    test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
    test('should click on "PROCEED TO CHECKOUT" modal button', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
    test('should click on "Have a promo code?" link', () => client.waitForExistAndClick(CheckoutOrderPage.promo_code_link));
    test('should set the "Promo code" input', () => client.setPromoCode(CheckoutOrderPage.promo_code_input, CheckoutOrderPage.promo_code_add_button, 'codePromo1'));
    test('should click on "Have a promo code?" link', () => client.waitForExistAndClick(CheckoutOrderPage.promo_code_link));
    test('should set the "Promo code" input', () => client.setPromoCode(CheckoutOrderPage.promo_code_input, CheckoutOrderPage.promo_code_add_button, 'codePromo2'));
    test('should check the total price after reduction', () => {
      return promise
        .then(() => client.getTextInVar(CheckoutOrderPage.cart_subtotal_products, "totalProducts"))
        .then(() => client.getTextInVar(CheckoutOrderPage.cart_subtotal_discount, "totalDiscount"))
        .then(() => client.checkTotalPrice(CheckoutOrderPage.cart_total))
    });
    test('should click on "Remove voucher" button', () => client.waitForExistAndClick(CheckoutOrderPage.remove_voucher_button));
    test('should click on "Have a promo code?" link', () => client.waitForExistAndClick(CheckoutOrderPage.promo_code_link));
    test('should set the "Promo code" input', () => client.setPromoCode(CheckoutOrderPage.promo_code_input, CheckoutOrderPage.promo_code_add_button, 'codePromo3'));
    test('should check the total price after reduction', () => {
      return promise
        .then(() => client.getTextInVar(CheckoutOrderPage.cart_subtotal_products, "totalProducts"))
        .then(() => client.getTextInVar(CheckoutOrderPage.cart_subtotal_discount, "totalDiscount"))
        .then(() => client.checkTotalPrice(CheckoutOrderPage.cart_total, 'amount'))
    });
  }, 'discount');
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'discount');
}, 'discount', true);

scenario('Delete "Cart Rule" in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'discount');
  for (let i = 0; i < cartRuleData.length; i++) {
    commonDiscountScenarios.deleteCartRule(cartRuleData[i].name);
  }
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'discount');
}, 'discount', true);