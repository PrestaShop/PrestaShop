const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const module_common_scenarios = require('../../common_scenarios/module');
let promise = Promise.resolve();


scenario('Install and Uninstall Module from cross selling', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  if (global.test_addons) {
    return;
  }
  scenario('Install " ' + module_tech_name + '" From Cross selling', client => {
    module_common_scenarios.installModule(client, ModulePage, AddProductPage, module_tech_name);
  }, 'common_client');
  scenario('Close "Symfony" toolbar', client => {
        test('should check then close the "Symfony" toolbar', () => {
            return promise
                .then(() => {
                    if (global.ps_mode_dev) {
                        client.waitForExistAndClick(AddProductPage.symfony_toolbar);
                    }
                })
                .then(() => client.pause(1000));
        });
    }, 'common_client');
  scenario('Check Configuration page of "' + module_tech_name + '"', client => {
    module_common_scenarios.checkConfigPage(client, ModulePage, module_tech_name);
  }, 'module');
  scenario('Disable Module "' + module_tech_name + '"', client => {
    module_common_scenarios.disableModule(client, ModulePage, AddProductPage, module_tech_name);
  }, 'module');
  scenario('Enable Module " ' + module_tech_name + '"', client => {
    module_common_scenarios.enableModule(client, ModulePage, AddProductPage, module_tech_name);
  }, 'module');
  scenario('Uninstall "' + module_tech_name + '"', client => {
    module_common_scenarios.uninstallModule(client, ModulePage, AddProductPage, module_tech_name);
  }, 'common_client');
}, 'common_client');

scenario('Install,disable,enable and Uninstall "PayPal" module (By partners)', () => {
  scenario('Install "PayPal" module From Cross selling', client => {
    module_common_scenarios.installModule(client, ModulePage, AddProductPage, "paypal");
  }, 'common_client');
  scenario('Check Configuration page of "PayPal" module', client => {
    module_common_scenarios.checkConfigPage(client, ModulePage, "paypal");
  }, 'module');
  scenario('Disable Module', client => {
    module_common_scenarios.disableModule(client, ModulePage, AddProductPage, "paypal");
  }, 'module');
  scenario('Enable Module', client => {
    module_common_scenarios.enableModule(client, ModulePage, AddProductPage, "paypal");
  }, 'module');
  scenario('Uninstall "PayPal" module', client => {
    module_common_scenarios.uninstallModule(client, ModulePage, AddProductPage, "paypal");
  }, 'module');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
