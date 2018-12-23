const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const module_common_scenarios = require('../../common_scenarios/module');
const {Menu} = require('../../../selectors/BO/menu.js');
const welcomeScenarios = require('../../common_scenarios/welcome');
let promise = Promise.resolve();

scenario('Install "PrestaShop Security" module', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Install "PrestaShop Security" module by uploading a ZIP file', client => {
    test('should go to "Module" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
    test('should click on "Upload a module" button', () => client.waitForExistAndClick(ModulePage.upload_button));
    test('should add zip file', () => client.addFile(ModulePage.zip_file_input, "v1.1.7-prestafraud.zip"));
    test('should verify that the module is installed', () => {
      return promise
        .then(() => client.waitForVisible(ModulePage.success_install_message))
        .then(() => client.checkTextValue(ModulePage.module_import_success, "Module installed!"))
    });
    test('should click on close modal button', () => client.waitForExistAndClick(ModulePage.close_modal_button));
    test('should click on "Installed Modules"', () => client.waitForExistAndClick(Menu.Improve.Modules.installed_modules_tabs, 1000));
    test('should search for "PrestaShop Security" module in the installed module tab', () => client.waitAndSetValue(ModulePage.modules_search_input, "prestafraud"));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.modules_search_button));
    test('should check if the module "prestafraud" was installed', () => client.isVisible(ModulePage.installed_module_div.replace("%moduleTechName","prestafraud")));
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
  scenario('Check Configuration page of "PrestaShop Security" module', client => {
    module_common_scenarios.checkConfigPage(client, ModulePage, "prestafraud");
  }, 'module');
  scenario('Uninstall "PrestaShop Security" module', client => {
    module_common_scenarios.uninstallModule(client, ModulePage, AddProductPage, "prestafraud");
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
