const {AccessPageFO} = require('../../selectors/FO/access_page');
const {CheckoutOrderPage, CustomerAccount} = require('../../selectors/FO/order_page');
let promise = Promise.resolve();

scenario('Order history page', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
    test('should set the language of shop to "English"', () => client.changeLanguage());
  }, 'common_client');
  scenario('Display a list of orders', client => {
    test('should go to the customer account', () => client.waitForExistAndClick(CheckoutOrderPage.customer_name));
    test('should display a list of orders', () => {
      return promise
        .then(() => client.waitForExistAndClick(CustomerAccount.order_history_button))
        .then(() => client.checkList(CustomerAccount.details_buttons))
    });
    test('should click on the "Details" button', () => client.waitForExistAndClick(CustomerAccount.details_button.replace("%NUMBER", 5)));
  }, 'common_client');
  scenario('Order detail page', client => {
    test('should check that is the order details page', () => client.checkTextValue(CustomerAccount.order_details_words, "Order details"));
    test('should display order infos', () => client.waitForVisible(CustomerAccount.order_infos_block));
    test('should display order statuses', () => client.waitForVisible(CustomerAccount.order_status_block));
    test('should display invoice address', () => client.waitForVisible(CustomerAccount.invoice_address_block));
    test('should display order products', () => client.waitForVisible(CustomerAccount.order_products_block));
    test('should display the return button', () => client.waitForVisible(CustomerAccount.order_products_block));
    test('should display a form to add a message', () => client.waitForVisible(CustomerAccount.add_message_block));
    test('should add a message', () => client.waitAndSetValue(CustomerAccount.message_input, "Message about the first order product"));
    test('should click on the "SEND" button', () => client.waitForExistAndClick(CustomerAccount.send_button));
    test('should verify the appearance of the green validation', () => {
      return promise
        .then(() => client.waitForVisible(CustomerAccount.success_panel))
        .then(() => client.checkTextValue(CustomerAccount.success_panel, 'Message successfully sent'))
    });
  }, 'common_client');
}, 'common_client',true);
