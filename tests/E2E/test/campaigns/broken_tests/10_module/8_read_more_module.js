const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {Menu} = require('../../../selectors/BO/menu');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const commonModule = require('../../common_scenarios/module');
let promise = Promise.resolve();

scenario('Check the click on "Read more" button', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  scenario('Install MBO module if it\'s not installed', client => {
    test('should go to "Modules Catalog" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_catalog_submenu));
    test('should click on "Modules Catalog" tab', () => client.waitForExistAndClick(Menu.Improve.Modules.modules_catalog_submenu));
    test('should set the name of the module in the search input', () => client.waitAndSetValue(ModulePage.module_selection_input, 'ps_mbo'));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.selection_search_button));
    test('should install the module if it\'s exist', () => {
      return promise
        .then(() => client.isVisible(ModulePage.install_button.replace("%moduleTechName", 'ps_mbo')))
        .then(() => {
          if (global.isVisible) {
            scenario('Install module', client => {
              test('should click on "Install" button', () => client.waitForExistAndClick(ModulePage.install_button.replace("%moduleTechName", 'ps_mbo')));
              test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
              test('should click on "Installed Modules"', () => client.waitForVisibleAndClick(Menu.Improve.Modules.installed_modules_tabs));
              test('should search for "ps_mbo" module in the installed module tab', () => client.waitAndSetValue(ModulePage.modules_search_input, 'ps_mbo'));
              test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.modules_search_button));
              test('should check if the module "ps_mbo" was installed', () => client.isExisting(ModulePage.installed_module_div.replace('%moduleTechName', 'ps_mbo')));
            }, 'common_client');
          } else {
            return promise.then(() => client.pause(0));
          }
        });
    });
  }, 'common_client');
  commonModule.clickOnReadMore(ModulePage, "Mailchimp", 'mailchimpintegration');
  scenario('Uninstall MBO module if it\'s installed', client => {
    test('should go to "Module manager" page', () => {
      return promise
        .then(() => client.pause(3000))
        .then(() => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
    });
    test('should search for the module "ps_mbo"', () => {
      return promise
        .then(() => client.waitAndSetValue(ModulePage.module_selection_input, 'ps_mbo'))
        .then(() => client.waitForExistAndClick(ModulePage.modules_search_button));
    });
    test('should uninstall the module if it\'s exist', () => {
      return promise
        .then(() => client.isVisible(ModulePage.action_dropdown.replace('%moduleTechName', 'ps_mbo')))
        .then(() => {
          if (global.isVisible) {
            scenario('Uninstall module', client => {
              test('should click on module dropdown', () => client.waitForVisibleAndClick(ModulePage.action_dropdown.replace('%moduleTechName', 'ps_mbo')));
              test('should click on "Uninstall" button', () => client.waitForExistAndClick(ModulePage.uninstall_button.split('%moduleTechName').join('ps_mbo')));
              test('should click on "Yes, uninstall it" button', () => client.waitForVisibleAndClick(ModulePage.uninstall_module_modal));
              test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
              test('should check that the backdrop is hidden', () => client.checkIsNotVisible(ModulePage.backdrop_modale));
              test('should check if the module "ps_mbo" was uninstalled', () => client.checkTextValue(ModulePage.built_in_module_span, "0", "contain"));
            }, 'common_client');
          } else {
            return promise.then(() => client.pause(0));
          }
        });
    });
  }, 'common_client');
  commonModule.clickOnReadMore(ModulePage, "Mailchimp", 'mailchimpintegration');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
