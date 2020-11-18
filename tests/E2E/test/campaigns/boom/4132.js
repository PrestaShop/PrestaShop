const {productPage} = require('../../selectors/FO/product_page');
const {CheckoutOrderPage} = require('../../selectors/FO/order_page');
const {AccessPageFO} = require('../../selectors/FO/access_page');
let promise = Promise.resolve();

scenario('BOOM-4132: Create order in the Front Office', client => {
  scenario('Open the browser and connect to the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'order');

  scenario('Create order in the Front Office', () => {
    test('should set the language of shop to "English"', () => client.changeLanguage());
    test('should go to the first product page', () => client.waitForExistAndClick(productPage.first_product));
    test('should select product "size M" ', () => client.waitAndSelectByValue(productPage.first_product_size, '2'));
    test('should select product "color Black"', () => client.waitForExistAndClick(productPage.first_product_color));
    test('should set the product "quantity"', () => client.waitAndSetValue(productPage.first_product_quantity, "4"));
    test('should click on "Add to cart" button  ', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
    test('should click on proceed to checkout button 1', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
    test('should change quantity to "5" using the keyboard and push "Enter"', () => {
      return promise
        .then(() => client.waitAndSetValue(CheckoutOrderPage.quantity_input.replace("%NUMBER", 1), '5'))
        .then(() => client.keys('\uE007'))
        .then(() => client.pause(1000));
    });
    test('should check that the quantity is equal to "5"', () => client.checkAttributeValue(CheckoutOrderPage.quantity_input.replace("%NUMBER", 1), 'value', '5', 'equal', 1000));
    test('should change quantity to "4" using the keyboard without pushing "Enter"', () => client.waitAndSetValue(CheckoutOrderPage.quantity_input.replace("%NUMBER", 1), '4'));
    test('should check that the quantity is equal to "4"', () => client.checkAttributeValue(CheckoutOrderPage.quantity_input.replace("%NUMBER", 1), 'value', '4', 'equal', 1000));
  }, 'order');

  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'order');
}, 'order', true);
