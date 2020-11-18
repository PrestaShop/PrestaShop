/**
 * This script is based on the scenario described in this test link
 * [id="PS-19"][Name="Installation of a module by zip folder"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const moduleCommonScenarios = require('../../common_scenarios/module');
const welcomeScenarios = require('../../common_scenarios/welcome');

scenario('Install "abondoned cart pro" module', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Check then uninstall "ps_mbo" module', client => {
    moduleCommonScenarios.installUninstallMboModule(client, ModulePage, AddProductPage, "ps_mbo", 'Uninstall');
  }, 'onboarding');
  moduleCommonScenarios.installAndCheckAbondonedCartProModule(ModulePage);
  scenario('Uninstall "abondoned cart pro" module', client => {
    moduleCommonScenarios.uninstallModule(client, ModulePage, AddProductPage, 'cartabandonmentpro');
  }, 'common_client');
  moduleCommonScenarios.installAndCheckAbondonedCartProModule(ModulePage);
  scenario('Check then install "ps_mbo" module', client => {
    moduleCommonScenarios.installUninstallMboModule(client, ModulePage, AddProductPage, "ps_mbo", 'install');
  }, 'onboarding');

  //verify this steps with Marion (we have to uninstall module before installing it)
  scenario('Uninstall "abondoned cart pro" module', client => {
    moduleCommonScenarios.uninstallModule(client, ModulePage, AddProductPage, 'cartabandonmentpro');
  }, 'common_client');

  moduleCommonScenarios.installAndCheckAbondonedCartProModule(ModulePage);
  scenario('Uninstall "abondoned cart pro" module', client => {
    moduleCommonScenarios.uninstallModule(client, ModulePage, AddProductPage, 'cartabandonmentpro');
  }, 'common_client');
  moduleCommonScenarios.installAndCheckAbondonedCartProModule(ModulePage);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
