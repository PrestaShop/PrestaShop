const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const moduleCommonScenarios = require('../../common_scenarios/module');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
let promise = Promise.resolve();

scenario('Check sort module by "Name"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'module');
  scenario('Uninstall "ps_mbo" module', client => {
    moduleCommonScenarios.uninstallModule(client, ModulePage, AddProductPage, 'ps_mbo');
  }, 'common_client');
  scenario('Check the sort module by name ', client => {
    test('should go to "Modules Catalog" page', () => {
      return promise
        .then(() => client.waitForExistAndClick(Menu.dashboard_menu, 2000))
        .then(() => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_catalog_submenu));
    });
    test('should click on "Modules Catalog" tab', () => client.waitForExistAndClick(Menu.Improve.Modules.modules_catalog_submenu));
    test('should set the name of the module in the search input', () => client.waitAndSetValue(ModulePage.module_selection_input, 'contact form'));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.selection_search_button));
    test('should get module number', () => client.getTextInVar(ModulePage.modules_number, "modules_number"));
    moduleCommonScenarios.sortModule(client, ModulePage, "name", "data-name");
    moduleCommonScenarios.sortModule(client, ModulePage, "price", "data-price");
    moduleCommonScenarios.sortModule(client, ModulePage, "price-desc", "data-price");
    moduleCommonScenarios.sortModule(client, ModulePage, "scoring-desc", "data-scoring");
  }, 'module');
  scenario('Install "ps_mbo" module', client => {
    moduleCommonScenarios.installModule(client, ModulePage, AddProductPage, 'ps_mbo');
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'module');
}, 'module', true);
