/**
 * This script is based on the scenario described in this test link
 * [id="PS-93"][Name="Credit slips PDF"]
 **/

const {Menu} = require('../../../selectors/BO/menu.js');
const {CreditSlip} = require('../../../selectors/BO/order');
const commonOrder = require('../../common_scenarios/order');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {MerchandiseReturns} = require('../../../selectors/BO/Merchandise_returns');
const common = require('../../../common.webdriverio');
const welcomeScenarios = require('../../common_scenarios/welcome');
let Date = common.getCustomDate(0);
scenario('Create three credits slips and generate them', () => {
  scenario('Open the browser login successfully in the Back Office ', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    welcomeScenarios.findAndCloseWelcomeModal();
    commonOrder.enableMerchandise(MerchandiseReturns);
  }, 'common_client');
  scenario('Create order and generate a credit slip', () => {
    for (let i = 0; i <= 2; i++) {
      scenario('Login in the Front Office ', client => {
        test('should access to the Front Office', async () => {
          await client.waitForExistAndClick(AccessPageBO.shopname, 3000);
          await client.switchWindow(1);
        });
        test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
      }, 'common_client');
      commonOrder.createOrderFO();
      scenario('Get the account name and logout from the Front Office', client => {
        test('should get the account name logout successfully from the Front Office', async () => {
          await client.getTextInVar(AccessPageFO.account, 'accountName');
          await client.signOutFO(AccessPageFO);
          await client.switchTab(0);
          await client.closeWindow(1);
        });
      }, 'common_client');
      scenario('Login in the Back Office ', client => {
        test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
      }, 'common_client');
      commonOrder.creditSlip('2', i, true);
      scenario('Login in the Back Office ', client => {
        test('should open the browser', () => client.open());
        test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
      }, 'common_client');
      commonOrder.checkCreditSlip('2', i);
    }
  }, 'order');
}, 'order');

scenario('Filter by the issue date the credit slips', client => {
  test('should go to the "Credit slips" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.credit_slips_submenu));
  test('should set "Form date" input', () => client.waitAndSetValue(CreditSlip.date_form, Date));
  test('should set "To date" input', () => client.waitAndSetValue(CreditSlip.date_to, Date));
  test('should click on "Generate PDF" button', async () => {
    await client.waitForExistAndClick(CreditSlip.generate_button);
    await client.pause(3000);
    global.creditSlip = await 'order-slips';
  });
  for (let i = 0; i <= 2; i++) {
    commonOrder.checkCreditSlip('2', i);
  }
}, 'order');
scenario('Filter by a date which there is no order with credit slip generated', client => {
  test('should go to the "Credit slips" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.credit_slips_submenu));
  test('should set "Form date" input', () => client.waitAndSetValue(CreditSlip.date_form, '1900-01-01', 2000));
  test('should set "To date" input', () => client.waitAndSetValue(CreditSlip.date_to, '1900-01-10', 200));
  test('should click on "Generate PDF" button', () => client.waitForExistAndClick(CreditSlip.generate_button));
  test('should check the error message existence', () => client.checkTextValue(CreditSlip.alert_message, 'No order slips were found for this period.', 'contain', 3000));
  commonOrder.disableMerchandise();
  test('should delete "order-slips" file', async () => {
    await client.checkFile(global.downloadsFolderPath, 'order-slips.pdf');
    if (global.existingFile) {
      await client.deleteFile(global.downloadsFolderPath, 'order-slips', '.pdf', 2000);
    }
  });
}, 'order', true);

