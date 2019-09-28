/**
 * This script is based on the scenario described in this test link
 * [id="PS-382"][Name="Click on discover"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const moduleCommonScenarios = require('../../common_scenarios/module');
const welcomeScenarios = require('../../common_scenarios/welcome');

scenario('Discover "Amazon market place" module in Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Check then install "ps_mbo" module', client => {
    moduleCommonScenarios.installUninstallMboModule(client, ModulePage, AddProductPage, 'ps_mbo', 'install');
  }, 'onboarding');

  scenario('Check that the "Amazon market place" module is opened', client => {
    moduleCommonScenarios.checkAmazonMarketPlace(client, ModulePage, '1');
  }, 'common_client');

  scenario('Check then uninstall "ps_mbo" module', client => {
    moduleCommonScenarios.installUninstallMboModule(client, ModulePage, AddProductPage, 'ps_mbo', 'Uninstall');
  }, 'onboarding');

  scenario('Check that the "Amazon market place" module is opened', client => {
    moduleCommonScenarios.checkAmazonMarketPlace(client, ModulePage, '2');
  }, 'common_client');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
