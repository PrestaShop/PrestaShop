const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {CheckoutOrderPage} = require('../../../selectors/FO/order_page');
const {OrderPage} = require('../../../selectors/BO/order');
const {ShoppingCart} = require('../../../selectors/BO/order');
const {accountPage} = require('../../../selectors/FO/add_account_page');
let promise = Promise.resolve();

scenario('Shopping carts view', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'order');
  scenario('Add products to the cart in the Front Office', () => {
    scenario('Go to the Front Office', client => {
      test('should go to the "Front Office"', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1))
          .then(() => client.signInFO(AccessPageFO));
      });
      test('should set the language of shop to "English"', () => client.changeLanguage());
      scenario('Add products to the cart', client => {
        test('should go to the first product page', () => client.waitForExistAndClick(productPage.first_product));
        test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
        test('should click on "CONTINUE SHOPPING" button', () => client.waitForVisibleAndClick(CheckoutOrderPage.continue_shopping_button));
        test('should go to "Home" page', () => client.waitForExistAndClick(AccessPageFO.logo_home_page, 1000));
        test('should set the language of shop to "English"', () => client.changeLanguage());
        test('should go to the second product page', () => client.waitForExistAndClick(productPage.second_product));
        test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
        test('should click on "PROCEED TO CHECKOUT" button', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
        test('should check the shopping cart informations', () => {
          return promise
            .then(() => client.getTextInVar(CheckoutOrderPage.total_cart, "total_cart"))
            .then(() => client.getTextInVar(CheckoutOrderPage.customer_name, "customer_name"))
            .then(() => client.getTextInVar(CheckoutOrderPage.product_name.replace('%NUMBER', 1), "first_product_name"))
            .then(() => client.getTextInVar(CheckoutOrderPage.product_name.replace('%NUMBER', 2), "second_product_name"))
            .then(() => client.getTextInVar(CheckoutOrderPage.product_unit_price.replace('%NUMBER', 1), "first_product_unit_price"))
            .then(() => client.getTextInVar(CheckoutOrderPage.product_unit_price.replace('%NUMBER', 2), "second_product_unit_price"));
        });
      }, 'order');
    }, 'order');
  }, 'order');
  scenario('View the shopping carts in the Back Office', () => {
    scenario('Go to the back Office', client => {
      test('should go back to the Back office', () => client.switchWindow(0));
    }, 'order');
    scenario('View the shopping cart', client => {
      test('should go to "Shopping cart" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.shopping_carts_submenu));
      test('should check that the first "Order ID" is equal to "Non ordered"', () => client.checkTextValue(ShoppingCart.check_first_order_id, 'Non ordered'));
      test('should check the customer', () => client.checkTextValue(ShoppingCart.check_order_customer, 'J. DOE'));
      test('should click on "View" button', () => client.waitForExistAndClick(ShoppingCart.view_order_button.replace('%NUMBER', 9)));
      test('should check the "Total Cart"', () => client.checkTextValue(ShoppingCart.total_cart, global.tab["total_cart"]));
      test('should check the "Customer name"', () => client.checkTextValue(ShoppingCart.customer_name.replace('%NAME', 'John DOE'), global.tab["customer_name"]));
      test('should check the "First product name"', () => client.checkTextValue(OrderPage.product_name.replace('%NUMBER', 1), global.tab["first_product_name"]));
      test('should check the "Second product name"', () => client.checkTextValue(OrderPage.product_name.replace('%NUMBER', 2), global.tab["second_product_name"]));
      test('should check the "First product unit price"', () => client.checkTextValue(ShoppingCart.product_unit_price.replace('%NUMBER', 1), global.tab["first_product_unit_price"]));
      test('should check the "Second product unit price"', () => client.checkTextValue(ShoppingCart.product_unit_price.replace('%NUMBER', 2), global.tab["second_product_unit_price"]));
    }, 'order');
  }, 'order');
  scenario('Increase the quantity of the first product', client => {
    scenario('Go back to the Front Office', client => {
      test('should go back to the "Front Office"', () => client.switchWindow(1));
      test('should increase the quantity of products', () => {
        return promise
          .then(() => client.waitForExistAndClick(CheckoutOrderPage.arrow_button_up.replace('%NUMBER', 1)))
          .then(() => client.getAttributeInVar(CheckoutOrderPage.quantity_input.replace('%NUMBER', 1), 'value', "quantity"))
      });
    }, 'order');
    scenario('Check the quantity of the first product', client => {
      test('should go back to the Back office', () => client.switchWindow(0));
      test('should check the first product quantity', () => client.checkTextValue(ShoppingCart.quantity_product.replace('%NUMBER', 1), global.tab["quantity"]));
    }, 'order');
  }, 'order');
  scenario('Create order in the Front Office', client => {
    test('should go back to the "Front Office"', () => client.switchWindow(1));
    test('should click on "PROCEED TO CHECKOUT" button', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_button));
    test('should click on "add new address"', () => client.waitForExistAndClick(accountPage.add_new_address));
    scenario('Create new address', client => {
      test('should set the "Address" input', () => client.waitAndSetValue(accountPage.adr_address, 'address'));
      test('should set the "Zip/Postal Code" input', () => client.waitAndSetValue(accountPage.adr_postcode, '12345'));
      test('should set the "City" input', () => client.waitAndSetValue(accountPage.adr_city, 'city'));
      test('should click on "CONTINUE" button', () => client.scrollWaitForExistAndClick(accountPage.new_address_btn));
    }, 'order');
    scenario('Choose "SHIPPING METHOD"', client => {
      test('should create message', () => client.waitAndSetValue(CheckoutOrderPage.message_textarea, 'Order message test'));
      test('should click on "confirm delivery" button', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step3_continue_button));
    }, 'common_client');
    scenario('Choose "PAYMENT" method', client => {
      test('should set the payment type "Payment by bank wire"', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step4_payment_radio));
      test('should set "the condition to approve"', () => client.waitForExistAndClick(CheckoutOrderPage.condition_check_box));
      test('should click on order with an obligation to pay button', () => client.waitForExistAndClick(CheckoutOrderPage.confirmation_order_button));
    }, 'order');
  }, 'order');
  scenario('Check order in the Back Office', client => {
    test('should go back to the Back office', () => client.switchWindow(0));
    test('should check the view informations', () => {
      return promise
        .then(() => client.getTextInVar(ShoppingCart.total_cart_summary, "total_cart_summary"))
        .then(() => client.getTextInVar(ShoppingCart.product_unit_price.replace('%NUMBER', 1), "unit_price_first_product"))
        .then(() => client.getTextInVar(ShoppingCart.product_unit_price.replace('%NUMBER', 2), "unit_price_second_product"))
        .then(() => client.getTextInVar(ShoppingCart.quantity_product.replace('%NUMBER', 1), "quantity_first_product"))
        .then(() => client.getTextInVar(ShoppingCart.quantity_product.replace('%NUMBER', 2), "quantity_second_product"))
        .then(() => client.getTextInVar(ShoppingCart.stock_product.replace('%NUMBER', 1), "stock_first_product"))
        .then(() => client.getTextInVar(ShoppingCart.stock_product.replace('%NUMBER', 2), "stock_second_product"))
        .then(() => client.getTextInVar(ShoppingCart.total_product.replace('%NUMBER', 1), "total_first_product_price"))
        .then(() => client.getTextInVar(ShoppingCart.total_product.replace('%NUMBER', 2), "total_second_product_price"));
    });
    scenario('Check order informations', client => {
      test('should go to the order page', () => client.waitForExistAndClick(ShoppingCart.order_page.replace("%s", 'order')));
      test('should check the first product base price', () => client.checkTextValue(OrderPage.product_basic_price_TTC.replace('%NUMBER', 1), global.tab["unit_price_first_product"]));
      test('should check the second product base price', () => client.checkTextValue(OrderPage.product_basic_price_TTC.replace('%NUMBER', 2), global.tab["unit_price_second_product"]));
      test('should check the first product quantity', () => client.checkTextValue(OrderPage.order_quantity.replace('%NUMBER', 1), global.tab["quantity_first_product"]));
      test('should check the second product quantity', () => client.checkTextValue(OrderPage.order_quantity.replace('%NUMBER', 2), global.tab["quantity_second_product"]));
      test('should check the first product stock', () => client.checkTextValue(OrderPage.stock_product.replace('%NUMBER', 1), global.tab["stock_first_product"]));
      test('should check the second product stock', () => client.checkTextValue(OrderPage.stock_product.replace('%NUMBER', 2), global.tab["stock_second_product"]));
      test('should check the first product total price', () => client.checkTextValue(OrderPage.total_product_price.replace('%NUMBER', 1), global.tab["total_first_product_price"]));
      test('should check the second product total price', () => client.checkTextValue(OrderPage.total_product_price.replace('%NUMBER', 2), global.tab["total_second_product_price"]));
      test('should check the total amount of the order', () => client.checkTextValue(OrderPage.total_order, global.tab["total_cart_summary"]));
    }, 'order');
  }, 'order', true);
}, 'order');
