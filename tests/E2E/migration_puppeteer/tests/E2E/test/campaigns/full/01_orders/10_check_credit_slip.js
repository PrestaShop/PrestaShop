/**
 * This script is based on the scenario described in this test link
 * [id="PS-92"][Name="Check credit slips"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const commonOrder = require('../../common_scenarios/order');

scenario('Generate and check a Credit slip', () => {
  scenario('Open the browser login successfully in the Back Office ', client => {
    test('should open the browser', async () => {
      await client.open();
      await client.startTracing('checkCreditSlip');
    });
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  commonOrder.enableMerchandise();
  scenario('Create order and generate a credit slip', () => {
    scenario('Login in the Front Office ', client => {
      test('should access to the Front Office', async () => {
        await client.waitForExistAndClick(AccessPageBO.shopname);
        await client.switchWindow(1);
      });
      test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
    }, 'common_client');
    commonOrder.createOrderFO();
    scenario('Get the account name and logout from the Front Office', client => {
      test('should get the client Name & last name', () => client.getTextInVar(AccessPageFO.account, 'accountName'));
      test('should logout successfully from the Front Office', async () => {
        await client.signOutFO(AccessPageFO);
        await client.closeWindow();
        await client.switchWindow(0);
      });
    }, 'common_client');
    commonOrder.creditSlip('2');
    commonOrder.checkCreditSlip('2');

  }, 'order');
  scenario('Back to default behaviour', () => {
    commonOrder.disableMerchandise();
  }, 'order');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'order', true);

