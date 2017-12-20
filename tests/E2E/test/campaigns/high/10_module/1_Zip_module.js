const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
let promise = Promise.resolve();
scenario('Install "PrestaShop Security Module"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Install " PrestaShop Security Module " with ZIP file', client => {
    test('should click on "Module" button', () => client.goToSubtabMenuPage(ModulePage.modules_subtab, ModulePage.modules_subtab));
    test('should click on "Upload a module" button', () => client.waitForExistAndClick(ModulePage.upload_button));
    test('should add zip file', () => client.addFile(ModulePage.zip_file_input,"v1.1.7-prestafraud.zip"));
    test('should verify that the module is installed', () => {
      return promise
        .then(() => client.waitForVisible(ModulePage.success_install_message))
        .then(() => client.checkTextValue(ModulePage.installed_message, "Module installed!"))
    });
    test('should click on close modal button', () => client.waitForExistAndClick(ModulePage.close_modal_button));
    test('should click on "Installed Modules"', () => client.waitForExistAndClick(ModulePage.installed_modules_tabs, 1000));
    test('should set the name of the module in the search input', () => client.waitAndSetValue(ModulePage.modules_search_input, "prestafraud"));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.modules_search_button));
    test('should check if the module "prestafraud" was installed', () => client.checkTextValue(ModulePage.built_in_module, "1", "contain"));
  }, 'common_client');
  scenario('Check Configuration page of "PrestaShop Security Module"', client => {
    test('should click on "Configure" button', () => client.waitForExistAndClick(ModulePage.action_module_built_button));
    test('should check the configuration page', () => client.checkTextValue(ModulePage.config_legend, "PrestaShop Security configuration"));
  }, 'common_client');
  scenario('Uninstall "PrestaShop Security Module"', client => {
    test('should click on "Module" button', () => client.goToSubtabMenuPage(ModulePage.modules_subtab, ModulePage.modules_subtab));
    test('should click on "Installed Modules"', () => client.waitForExistAndClick(ModulePage.installed_modules_tabs, 1000));
    test('should set the name of the module in the search input', () => client.waitAndSetValue(ModulePage.modules_search_input, "prestafraud"));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.modules_search_button));
    test('should click on option button', () => client.waitForVisibleAndClick(ModulePage.option_button));
    test('should click on "Uninstall" button', () => client.waitForExistAndClick(ModulePage.uninstall_button));
    test('should click on "Yes, uninstall it" button', () => client.waitForVisibleAndClick(ModulePage.uninstall_confirmation));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should check if the module "prestafraud" was installed', () => client.checkTextValue(ModulePage.built_in_module, "0", "contain"));
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
