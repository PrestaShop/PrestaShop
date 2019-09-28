/**
 * This script is based on the scenario described in this test link
 * [id="PS-70"][Name="Check notifications"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const moduleCommonScenarios = require('../../common_scenarios/module');
const welcomeScenarios = require('../../common_scenarios/welcome');

scenario('Check notification module in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Check then install "ps_mbo" module', client => {
    moduleCommonScenarios.installUninstallMboModule(client, ModulePage, AddProductPage, 'ps_mbo', 'install');
  }, 'onboarding');
  scenario('Configure "Bank Transfer" module then check notifications', client => {
    moduleCommonScenarios.configureModule(client, 'ps_wirepayment', ModulePage);
    moduleCommonScenarios.resetModule(client, ModulePage, AddProductPage, 'Bank transfer', 'ps_wirepayment');
  }, 'common_client');
  scenario('Check then uninstall "ps_mbo" module', client => {
    moduleCommonScenarios.installUninstallMboModule(client, ModulePage, AddProductPage, 'ps_mbo', 'Uninstall');
  }, 'onboarding');
  scenario('Configure "Bank Transfer" module then check notifications', client => {
    moduleCommonScenarios.configureModule(client, 'ps_wirepayment', ModulePage);
  }, 'common_client');
  scenario('Reset "Bank Transfer" module', client => {
    moduleCommonScenarios.resetModule(client, ModulePage, AddProductPage, 'Bank transfer', 'ps_wirepayment');
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
