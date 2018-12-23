const {CreditSlip} = require('../../../selectors/BO/order');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {OrderPage} = require('../../../selectors/BO/order');
const {Menu} = require('../../../selectors/BO/menu.js');
let promise = Promise.resolve();
require('./10_credit_slip');
scenario('Generate and check a Credit slips options ', () => {
  scenario('Open the browser and login successfully in the Back Office ', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Change the credit slip prefix ', client => {
    test('should go to "Credit slip" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.credit_slips_submenu));
    test('should change the credit slip prefix value', () => client.waitAndSetValue(CreditSlip.credit_slip_prefix_input, 'PrefixTest'));
    test('should click on "Save" button', () => client.waitForExistAndClick(CreditSlip.save_button));
    test('should check the green validation message', () => client.checkTextValue(CreditSlip.green_validation, 'The settings have been successfully updated.', 'contain'));
  }, 'common_client');
  scenario('Verify the prefix value', client => {
    test('should go to "Orders" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
    test('should go to the created order', () => client.waitForExistAndClick(OrderPage.order_view_button.replace('%ORDERNumber', 1)));
    test('should click on "DOCUMENTS" subtab', () => client.scrollWaitForExistAndClick(OrderPage.document_submenu));
    test('should get the credit slip name', () => client.getDocumentName(OrderPage.credit_slip_document_name));
    test('should check the existence of "prefix value" ', () => {
      return promise
        .then(() => client.checkDocument(global.downloadsFolderPath, global.creditSlip, 'PrefixTest'))
        .then(() => client.deleteDownloadedDocument(global.creditSlip));
    });
  }, 'order');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);