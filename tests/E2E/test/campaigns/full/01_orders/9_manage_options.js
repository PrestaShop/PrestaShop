const {Menu} = require('../../../selectors/BO/menu.js');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {OrderPage} = require('../../../selectors/BO/order');
const {Invoices} = require('../../../selectors/BO/order');
const commonOrder = require('../../common_scenarios/order');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
let promise = Promise.resolve();


scenario('Manage options', () => {
  scenario('Open the browse', client => {
    test('should open the browser', () => client.open());
  }, 'order');

  scenario('Check invoice generation when disabling the invoice option', client => {
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
    commonOrder.createOrderFO();
    scenario('Disable invoice', client => {
      test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
      test('should go to "Order - Invoice" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.invoices_submenu));
      test('should close the symfony toolbar if exists', () => {
        return promise
          .then(() => client.isVisible(AddProductPage.symfony_toolbar))
          .then(() => {
            if (global.isVisible) {
              client.waitForExistAndClick(AddProductPage.symfony_toolbar);
            }
          });
      });
      test('should click on "NO" option for "Enable invoices"', () => client.waitForExistAndClick(Invoices.disable_invoice));
      test('should click on "Save" button', () => client.waitForExistAndClick(Invoices.save_button));
      test('should check the success message', () => client.checkTextValue(Invoices.success_msg, 'Update successful'));
      test('should go orders list', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
      test('should go the created order', () => client.waitForExistAndClick(OrderPage.order_view_button.replace('%ORDERNumber', 1)));
      test('should change order state to "Awaiting check payment"', () => client.changeOrderState(OrderPage, 'Payment accepted'));
      test('should click on "DOCUMENTS" tab', () => client.waitForExistAndClick(OrderPage.documents_tab));
      test('should check the visibility of the "There is no available document" message ', () => client.waitForVisible(OrderPage.empty_page_logo));
      test('should click on "Order" menu', () => client.waitForExistAndClick(Menu.Sell.Orders.orders_submenu));
      test('should Verify if there isn\'t the invoice logo in the "PDF" column', () => client.isNotExisting(OrderPage.pdf_icon.replace('%ORDER', 1)));
    }, 'order');
  }, 'order');

}, 'order');
