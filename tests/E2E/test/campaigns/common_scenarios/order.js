const {productPage} = require('../../selectors/FO/product_page');
const {CheckoutOrderPage} = require('../../selectors/FO/order_page');
const {accountPage} = require('../../selectors/FO/add_account_page');
const {OrderPage} = require('../../selectors/BO/order');
const {Menu} = require('../../selectors/BO/menu.js');
const {ShoppingCarts} = require('../../selectors/BO/order');

let dateFormat = require('dateformat');
let data = require('../../datas/customer_and_address_data');
let promise = Promise.resolve();

module.exports = {
  createOrderFO: function (authentication = "connected") {
    scenario('Create order in the Front Office', client => {
      test('should set the language of shop to "English"', () => client.changeLanguage());
      test('should go to the first product page', () => client.waitForExistAndClick(productPage.first_product));
      test('should select product "size M" ', () => client.waitAndSelectByValue(productPage.first_product_size, '2'));
      test('should select product "color Black"', () => client.waitForExistAndClick(productPage.first_product_color));
      test('should set the product "quantity"', () => {
        return promise
          .then(() => client.waitAndSetValue(productPage.first_product_quantity, "4"))
          .then(() => client.getTextInVar(CheckoutOrderPage.product_current_price, "first_basic_price"));
      });
      test('should click on "Add to cart" button  ', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
      test('should click on proceed to checkout button 1', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
      /**
       * This scenario is based on the bug described in this ticket
       * http://forge.prestashop.com/browse/BOOM-4132
       */
      test('should change quantity to "5" using the keyboard and push "Enter"', () => {
        return promise
          .then(() => client.waitAndSetValue(CheckoutOrderPage.quantity_input.replace('%NUMBER', 1), '5'))
          .then(() => client.keys('\uE007'))
          .then(() => client.pause(1000));
      });
      test('should check that the quantity is equal to "5"', () => client.checkAttributeValue(CheckoutOrderPage.quantity_input.replace('%NUMBER', 1), 'value', '5', 'equal', 1000));
      test('should change quantity to "4" using the keyboard without pushing "Enter"', () => client.waitAndSetValue(CheckoutOrderPage.quantity_input.replace('%NUMBER', 1), '4'));
      test('should check that the quantity is equal to "4"', () => client.checkAttributeValue(CheckoutOrderPage.quantity_input.replace('%NUMBER', 1), 'value', '4', 'equal', 1000));
      /**** END ****/
      test('should click on proceed to checkout button 2', () => client.waitForExistAndClick(CheckoutOrderPage.proceed_to_checkout_button));

      if (authentication === "create_account" || authentication === "guest") {
        scenario('Create new account', client => {
          test('should choose a "Social title"', () => client.waitForExistAndClick(accountPage.radio_button_gender));
          test('should set the "First name" input', () => client.waitAndSetValue(accountPage.firstname_input, data.customer.firstname));
          test('should set the "Last name" input', () => client.waitAndSetValue(accountPage.lastname_input, data.customer.lastname));
          if (authentication === "create_account") {
            test('should set the "Email" input', () => client.waitAndSetValue(accountPage.new_email_input, data.customer.email.replace("%ID", date_time)));
            test('should set the "Password" input', () => client.waitAndSetValue(accountPage.new_password_input, data.customer.password));
          } else {
            test('should set the "Email" input', () => client.waitAndSetValue(accountPage.new_email_input, data.customer.email.replace("%ID", '_guest' + date_time)));
          }
          test('should click on "CONTINUE" button', () => client.waitForExistAndClick(accountPage.new_customer_btn));
        }, 'common_client');

        scenario('Create new address', client => {
          test('should set the "Address" input', () => client.waitAndSetValue(accountPage.adr_address, data.address.address));
          test('should set the "Zip/Postal Code" input', () => client.waitAndSetValue(accountPage.adr_postcode, data.address.postalCode));
          test('should set the "City" input', () => client.waitAndSetValue(accountPage.adr_city, data.address.city));
          test('should click on "CONTINUE" button', () => client.scrollWaitForExistAndClick(accountPage.new_address_btn));
        }, 'common_client');
      }

      if (authentication === "connect") {
        scenario('Login with existing customer', client => {
          test('should choose shipping method my carrier', () => client.waitForExistAndClick(accountPage.sign_tab));
          test('should set the "Email" input', () => client.waitAndSetValue(accountPage.signin_email_input, 'pub@prestashop.com'));
          test('should set the "Password" input', () => client.waitAndSetValue(accountPage.signin_password_input, '123456789'));
          test('should click on "CONTINUE" button', () => client.waitForExistAndClick(accountPage.continue_button));
        }, 'common_client');
      }

      if (authentication === "connected" || authentication === "connect") {
        scenario('Choose the personal and delivery address ', client => {
          test('should click on confirm address button', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step2_continue_button));
        }, 'common_client');
      }

      scenario('Choose "SHIPPING METHOD"', client => {
        test('should choose shipping method my carrier', () => client.waitForExistAndClick(CheckoutOrderPage.shipping_method_option));
        test('should create message', () => client.waitAndSetValue(CheckoutOrderPage.message_textarea, 'Order message test'));
        test('should click on "confirm delivery" button', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step3_continue_button));
      }, 'common_client');
      scenario('Choose "PAYMENT" method', client => {
        test('should set the payment type "Payment by bank wire"', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step4_payment_radio));
        test('should set "the condition to approve"', () => client.waitForExistAndClick(CheckoutOrderPage.condition_check_box));
        test('should click on order with an obligation to pay button', () => client.waitForExistAndClick(CheckoutOrderPage.confirmation_order_button));
        test('should check the order confirmation', () => {
          return promise
            .then(() => client.checkTextValue(CheckoutOrderPage.confirmation_order_message, 'YOUR ORDER IS CONFIRMED', "contain"))
            .then(() => client.getTextInVar(CheckoutOrderPage.order_product, "product"))
            .then(() => client.getTextInVar(CheckoutOrderPage.order_basic_price, "basic_price"))
            .then(() => client.getTextInVar(CheckoutOrderPage.order_total_price, "total_price"))
            .then(() => client.getTextInVar(CheckoutOrderPage.order_reference, "reference", true))
            .then(() => client.getTextInVar(CheckoutOrderPage.shipping_method, "method", true))
            .then(() => client.getTextInVar(CheckoutOrderPage.order_shipping_prince_value, "shipping_price"))
        });
        /**
         * This scenario is based on the bug described in this ticket
         * http://forge.prestashop.com/browse/BOOM-3886
         */
        test('should check that the basic price is equal to "22,94 €"', () => client.checkTextValue(CheckoutOrderPage.order_basic_price, global.tab["first_basic_price"]));
        /**** END ****/
      }, 'common_client');
    }, 'common_client');
  },
  createOrderBO: function (OrderPage, CreateOrder, productData) {
    scenario('Create order in the Back Office', client => {
      test('should go to "Orders" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
      test('should click on "Add new order" button', () => client.waitForExistAndClick(CreateOrder.new_order_button, 1000));
      test('should search for a customer', () => client.waitAndSetValue(CreateOrder.customer_search_input, 'john doe'));
      test('should choose the customer', () => client.waitForExistAndClick(CreateOrder.choose_customer_button));
      test('should search for a product by name', () => client.waitAndSetValue(CreateOrder.product_search_input, productData.name + global.date_time));
      test('should set the product combination', () => client.waitAndSelectByValue(CreateOrder.product_combination, global.combinationId));
      test('should set the product quantity', () => client.waitAndSetValue(CreateOrder.quantity_input, '4'));
      test('should click on "Add to cart" button', () => client.scrollWaitForExistAndClick(CreateOrder.add_to_cart_button));
      test('should get the basic product price', () => client.getTextInVar(CreateOrder.basic_price_value, global.basic_price));
      test('should set the delivery option ', () => client.waitAndSelectByValue(CreateOrder.delivery_option, '2,'));
      test('should add an order message ', () => client.addOrderMessage('Order message test'));
      test('should set the payment type ', () => client.waitAndSelectByValue(CreateOrder.payment, 'ps_checkpayment'));
      test('should set the order status ', () => client.waitAndSelectByValue(OrderPage.order_state_select, '1'));
      test('should click on "Create the order"', () => client.waitForExistAndClick(CreateOrder.create_order_button));
    }, 'order');
  },
  checkOrderInBO: function (clientType = "client") {
    scenario('Check the created order information in the Back Office', client => {
      test('should go to "Orders" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
      test('should search for the created order by reference', () => client.waitAndSetValue(OrderPage.search_by_reference_input, global.tab['reference']));
      test('should go to search order', () => client.waitForExistAndClick(OrderPage.search_order_button));
      test('should go to the order', () => client.scrollWaitForExistAndClick(OrderPage.view_order_button.replace('%NUMBER', 1), 150, 2000));
      test('should check that the customer name is "John DOE"', () => client.checkTextValue(OrderPage.customer_name, 'John DOE', 'contain'));
      if (clientType === "guest") {
        test('should check that the order has been placed by a guest', () => client.isExisting(OrderPage.transform_guest_customer_button));
      }
      test('should status be equal to "Awaiting bank wire payment"', () => client.checkTextValue(OrderPage.order_status, 'Awaiting bank wire payment'));
      test('should check the shipping price', () => client.checkTextValue(OrderPage.shipping_cost, global.tab['shipping_price']));
      test('should check the product name', () => client.checkTextValue(OrderPage.product_name.replace("%NUMBER", 1), global.tab['product']));
      test('should check the order message', () => client.checkTextValue(OrderPage.message_order, 'Order message test'));
      test('should check the total price', () => client.checkTextValue(OrderPage.total_price, global.tab["total_price"]));
      test('should check basic product price', () => {
        return promise
          .then(() => client.scrollWaitForExistAndClick(OrderPage.edit_product_button))
          .then(() => client.checkAttributeValue(OrderPage.product_basic_price.replace("%NUMBER", 1), 'value', global.tab["basic_price"].replace('€', '')))
      });
      test('should check shipping method', () => client.checkTextValue(OrderPage.shipping_method, global.tab["method"].split('\n')[0], 'contain'));
    }, "order");
  },
  getShoppingCartsInfo: function () {
    scenario('Get all informations about the ' + global.shoppingCartsNumber + ' shopping carts', client => {
      for (let i = 1; i <= global.shoppingCartsNumber; i++) {
        test('Get the information of the ' + client.stringifyNumber(i) + ' shopping cart', () => {
          return promise
            .then(() => client.getTextInVar(ShoppingCarts.id.replace('%NUMBER', i), "id"))
            .then(() => client.getTextInVar(ShoppingCarts.order_id.replace('%NUMBER', i), "order_id"))
            .then(() => client.getTextInVar(ShoppingCarts.customer.replace('%NUMBER', i), "customer"))
            .then(() => client.getTextInVar(ShoppingCarts.total.replace('%NUMBER', i), "total"))
            .then(() => client.getTextInVar(ShoppingCarts.carrier.replace('%NUMBER', i), "carrier"))
            .then(() => client.getTextInVar(ShoppingCarts.date.replace('%NUMBER', i), "date"))
            .then(() => client.getTextInVar(ShoppingCarts.customer_online.replace('%NUMBER', i), "customer_online"))
            .then(() => {
              parseInt(global.tab["order_id"]) ? global.tab["order_id"] = parseInt(global.tab["order_id"]) : global.tab["order_id"] = '"' + global.tab["order_id"] + '"';
              global.tab["carrier"] === '--' ? global.tab["carrier"] = '' : global.tab["carrier"] = '"' + global.tab["carrier"] + '"';
              global.tab["customer_online"] === 'Yes' ? global.tab["customer_online"] = 1 : global.tab["customer_online"] = 0;
              global.tab["date"] = dateFormat(global.tab["date"], "yyyy-mm-dd hh:MM:ss");
              global.orders.push(parseInt(global.tab["id"]) + ';' + global.tab["order_id"] + ';' + '"' + global.tab["customer"] + '"' + ';' + global.tab["total"] + ';' + global.tab["carrier"] + ';' + '"' + global.tab["date"] + '"' + ';' + global.tab["customer_online"]);
            });
        });
      }
    }, 'order');
  },
  checkExportedFile: function () {
    scenario('Check that the exported shopping carts file contains exactly the same shopping carts information', client => {
      test('should export carts', () => client.downloadCart(ShoppingCarts.export_carts_button));
      test('should check the file name', () => client.checkFile(global.downloadsFolderPath, global.exportCartFileName));
      test('should read the file', () => client.readFile(global.downloadsFolderPath, global.exportCartFileName, 1000));
      test('should compare both informations', () => client.checkExportedFileInfo(1000));
      test('should reset filter', () => client.waitForExistAndClick(ShoppingCarts.reset_button));
    }, 'order', true);
  }
};
