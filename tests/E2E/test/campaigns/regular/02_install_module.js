const {AccessPageBO} = require('../../selectors/BO/access_page');
const {ModulePage} = require('../../selectors/BO/module_page');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {OnBoarding} = require('../../selectors/BO/onboarding.js');
let promise = Promise.resolve();
const module_common_scenarios = require('../common_scenarios/module');

scenario('Install and Uninstall Module from cross selling', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Close the onboarding modal if exist ', client => {
    test('should close the onboarding modal if exist', () => {
      return promise
        .then(() => client.isVisible(OnBoarding.welcome_modal))
        .then(() => client.closeBoarding(OnBoarding.popup_close_button))
    });
  }, 'order');

  if (disable_addons) {
    return;
  }

  scenario('Install "'+module_tech_name+'" From Cross selling', client => {
    module_common_scenarios.installModule(client, ModulePage, AddProductPage, module_tech_name);
  }, 'common_client');
  scenario('Check Configuration page of "'+module_tech_name+'"', client => {
    module_common_scenarios.checkConfigPage(client, ModulePage, module_tech_name);
  }, 'common_client');
  scenario('Disable Module "'+module_tech_name+'"', client => {
    module_common_scenarios.disableModule(client, ModulePage, AddProductPage, module_tech_name);
  }, 'common_client');
  scenario('Disable Module "'+module_tech_name+'"', client => {
    module_common_scenarios.enableModule(client, ModulePage, AddProductPage, module_tech_name);
  }, 'common_client');
  scenario('Uninstall "'+module_tech_name+'"', client => {
    module_common_scenarios.uninstallModule(client, ModulePage, AddProductPage, module_tech_name);
  }, 'common_client');
}, 'common_client', true);
