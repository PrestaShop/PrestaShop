/**
 * This script is based on the scenario described in this test link
 * [id="PS-94"][Name="Credit slips options"]
 **/

const {CreditSlip} = require('../../../selectors/BO/order');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {OrderPage} = require('../../../selectors/BO/order');
const {Menu} = require('../../../selectors/BO/menu.js');
const welcomeScenarios = require('../../common_scenarios/welcome');

require('./10_check_credit_slip');
scenario('Generate and check a Credit slips options ', () => {
  scenario('Open the browser and login successfully in the Back Office ', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Change the credit slip prefix ', client => {
    test('should go to "Credit slip" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.credit_slips_submenu));
    test('should change the credit slip prefix value', () => client.waitAndSetValue(CreditSlip.credit_slip_prefix_input, 'PrefixTest'));
    test('should click on "Save" button', () => client.waitForExistAndClick(CreditSlip.save_button, 2000));
    test('should check the green validation message', () => client.checkTextValue(CreditSlip.green_validation, 'The settings have been successfully updated.', 'contain'));
  }, 'common_client');
  scenario('Verify the prefix value', client => {
    test('should go to "Orders" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
    test('should go to the created order', () => client.waitForExistAndClick(OrderPage.order_view_button.replace('%ORDERNumber', 1)));
    test('should click on "DOCUMENTS" subtab', async () => {
      await client.waitForExistAndClick(OrderPage.document_submenu, 5000);
      await client.pause(3000);
      await client.getCreditSlipDocumentName(OrderPage.credit_slip_document_name);
    });
    test('should download the credit slip', async () => {
      // for headless, we need to remove attribute 'target' to avoid download in a new Tab
      if(global.headless)  await client.removeAttribute(OrderPage.credit_slip_document_name,'target');
      await client.waitForExistAndClick(OrderPage.credit_slip_document_name);
    });
    test('should check the existence of "prefix value" ', async () => {
      await client.pause(1000);
      await client.checkFile(global.downloadsFolderPath, global.creditSlip + '.pdf');
      if (global.existingFile) {
        await client.pause(6000);
        await client.checkDocument(global.downloadsFolderPath, global.creditSlip, 'PrefixTest');
      }
    });
  }, 'order');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);

