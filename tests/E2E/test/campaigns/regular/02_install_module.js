const {AccessPageBO} = require('../../selectors/BO/access_page');
const {ModulePage} = require('../../selectors/BO/module_page');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {OnBoarding} = require('../../selectors/BO/onboarding.js');
const module_common_scenarios = require('../common_scenarios/module');
const welcomeScenarios = require('../common_scenarios/welcome');

/**
 * If there is no module to install, return immediately.
 */
if (global.test_addons) {
  return;
}

scenario('Install and Uninstall Module from cross selling', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();

  scenario('Install "' + module_tech_name + '" From Cross selling', client => {
    module_common_scenarios.installModule(client, ModulePage, AddProductPage, module_tech_name);
  }, 'module');
  scenario('Check Configuration page of "' + module_tech_name + '"', client => {
    module_common_scenarios.checkConfigPage(client, ModulePage, module_tech_name);
  }, 'module');
  scenario('Disable Module "' + module_tech_name + '"', client => {
    module_common_scenarios.disableModule(client, ModulePage, AddProductPage, module_tech_name);
  }, 'module');
  scenario('Disable Module "' + module_tech_name + '"', client => {
    module_common_scenarios.enableModule(client, ModulePage, AddProductPage, module_tech_name);
  }, 'module');
  scenario('Uninstall "' + module_tech_name + '"', client => {
    module_common_scenarios.uninstallModule(client, ModulePage, AddProductPage, module_tech_name);
  }, 'module');
}, 'common_client', true);
