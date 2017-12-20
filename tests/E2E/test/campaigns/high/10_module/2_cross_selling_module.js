const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
scenario('Install "Google AdWords Module"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Install "Google AdWords Module " From Cross selling', client => {
    test('should click on "Module" button', () => client.goToSubtabMenuPage(ModulePage.modules_subtab, ModulePage.modules_subtab));
    test('should set the name of the module in the search input', () => client.waitAndSetValue(ModulePage.module_selection_input, "gadwords"));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.selection_search_button));
    test('should click on "Install" button', () => client.waitForExistAndClick(ModulePage.install_button));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should click on "Installed Modules"', () => client.waitForVisibleAndClick(ModulePage.installed_modules_tabs));
    test('should set the name of the module in the search input', () => client.waitAndSetValue(ModulePage.modules_search_input, "gadwords"));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.modules_search_button));
    test('should check if the module "prestafraud" was installed', () => client.checkTextValue(ModulePage.built_in_module, "1", "contain"));
  }, 'common_client');
  scenario('Check Configuration page of "Google AdWords Module"', client => {
    test('should click on "Configure" button', () => client.waitForExistAndClick(ModulePage.action_module_built_button));
    test('should check the configuration page', () => client.checkTextValue(ModulePage.config_legend_adwords, "Google AdWords"));
  }, 'common_client');
  scenario('Uninstall "Google AdWords Module"', client => {
    test('should click on "Module" button', () => client.goToSubtabMenuPage(ModulePage.modules_subtab, ModulePage.modules_subtab));
    test('should click on "Installed Modules"', () => client.waitForVisibleAndClick(ModulePage.installed_modules_tabs));
    test('should set the name of the module in the search input', () => client.waitAndSetValue(ModulePage.modules_search_input, "gadwords"));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.modules_search_button));
    test('should click on option button', () => client.waitForVisibleAndClick(ModulePage.option_button));
    test('should click on "Uninstall" button', () => client.waitForExistAndClick(ModulePage.uninstall_button));
    test('should click on "Yes, uninstall it" button', () => client.waitForVisibleAndClick(ModulePage.uninstall_adwords_module));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should check if the module "prestafraud" was installed', () => client.checkTextValue(ModulePage.built_in_module, "0", "contain"));
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
