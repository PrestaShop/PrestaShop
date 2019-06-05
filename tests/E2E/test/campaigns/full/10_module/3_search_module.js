/**
 * This script is based on the scenario described in this test link
 * [id="PS-68"][Name="Search a module"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const moduleCommonScenarios = require('../../common_scenarios/module');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const welcomeScenarios = require('../../common_scenarios/welcome');

scenario('Search modules"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'module');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Check then install "ps_mbo" module', client => {
    moduleCommonScenarios.installUninstallMboModule(client, ModulePage, AddProductPage, "ps_mbo", 'install');
  }, 'onboarding');
  scenario('Search "mailchimp" module', client => {
    moduleCommonScenarios.checkExistenceModuleCatalog(client, 'mailchimp', ModulePage);
  }, 'common_client');
  scenario('Search "payments by check" module', client => {
    moduleCommonScenarios.checkExistenceModuleManager(client, 'check', ModulePage);
  }, 'common_client');
  scenario('Uninstall "ps_mbo" module if it is installed', client => {
    moduleCommonScenarios.installUninstallMboModule(client, ModulePage, AddProductPage, "ps_mbo", 'Uninstall');
  }, 'onboarding');
  scenario('Search "mailchimp" module', client => {
    moduleCommonScenarios.checkExistenceModuleCatalog(client, 'paypal', ModulePage);
  }, 'common_client');
  scenario('Search "payments by check" module', client => {
    moduleCommonScenarios.checkExistenceModuleManager(client, 'check', ModulePage);
  }, 'common_client');
  scenario('Check then install "ps_mbo" module', client => {
    moduleCommonScenarios.installUninstallMboModule(client, ModulePage, AddProductPage, "ps_mbo", 'install');
  }, 'onboarding');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'module');
}, 'module', true);
