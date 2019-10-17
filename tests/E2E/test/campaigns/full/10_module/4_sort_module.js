/**
 * This script is based on the scenario described in this test link
 * [id="PS-372"][Name="Sort module list"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const moduleCommonScenarios = require('../../common_scenarios/module');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const welcomeScenarios = require('../../common_scenarios/welcome');

scenario('Check sort module by "Name"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'module');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Check then install "ps_mbo" module', client => {
    moduleCommonScenarios.installUninstallMboModule(client, ModulePage, AddProductPage, "ps_mbo", 'install');
  }, 'onboarding');
  scenario('Check the sort module by name, increasing price, decreasing price and popularity', client => {
    test('should go to "Modules > Modules Catalog" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_catalog_submenu));
    test('should get module number', () => client.getModuleNumber(ModulePage.modules_number_span, 'modules_number'));
    moduleCommonScenarios.sortModule(client, ModulePage, true, 0, 'Increasing price', 'price', 'data-price', true, true);
    moduleCommonScenarios.sortModule(client, ModulePage, true, 0, 'Decreasing price', 'price-desc', 'data-price', true, false);
  }, 'module');
  scenario('Select specific category then check the sort module by name, increasing price, decreasing price', client => {
    test('should select "Administration" from categories list', async () => {
      await client.waitForExistAndClick(ModulePage.categories_list);
      await client.getTextInVar(ModulePage.categories_option_number_span.replace('%CAT', 'Administration'), "category_modules_number");
      await client.getAttributeInVar(ModulePage.categories_by_name_option.replace('%CAT', 'Administration'), 'data-category-ref', 'categoryRef');
      await client.waitForExistAndClick(ModulePage.categories_option_link.replace('%CAT', 'Administration'));
    });
    test('should get module number', () => client.getModuleNumber(ModulePage.modules_number_span, 'modules_number'));
    test('should verify if the modules displayed are only modules of the selected category', () => client.checkNumberModule(ModulePage.modules_number_span, tab['modules_number']));
  }, 'module');
  scenario('Select category then search module that is in the selected category in module catalog page', client => {
    moduleCommonScenarios.searchModuleCategory(client, ModulePage, 'autoupgrade', 'catalog');
  }, 'module');
  scenario('Select category then search module that is not in the selected category in module catalog page', client => {
    moduleCommonScenarios.searchModuleCategory(client, ModulePage, 'ps_brandlist', 'catalog', false);
  }, 'module');
  scenario('Sort by each category then verify displayed modules', client => {
    test('should go to "Module Manager" page', () => client.waitForExistAndClick(Menu.Improve.Modules.modules_manager_submenu));
    test('should get the number of categories in the list', () => client.getCategoryNumber(ModulePage.category_list));
    moduleCommonScenarios.filterCategory(client, ModulePage);
  }, 'module');
  scenario('Sort by each category then verify displayed modules', client => {
    test('should go to "Module Manager" page', () => client.waitForExistAndClick(Menu.Improve.Modules.modules_manager_submenu));
    moduleCommonScenarios.DisableEnableModule(client, ModulePage);
  }, 'module');
  scenario('Select category then search module that is not in the selected category in module manager page', client => {
    moduleCommonScenarios.searchModuleCategory(client, ModulePage, 'ps_banner', 'manager', false);
  }, 'module');
  scenario('Select category then search module that is in the selected category in module manager page', client => {
    moduleCommonScenarios.searchModuleCategory(client, ModulePage, 'statsstock');
  }, 'module');
  scenario('Check then uninstall "ps_mbo" module', client => {
    moduleCommonScenarios.installUninstallMboModule(client, ModulePage, AddProductPage, "ps_mbo", 'Uninstall');
  }, 'onboarding');
  scenario('Check the sort module by name, increasing price, decreasing price and popularity', client => {
    test('should go to "Modules > Modules Catalog" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_catalog_submenu));
    test('should get module number', () => client.getModuleNumber(ModulePage.modules_number_span, 'modules_number'));
    moduleCommonScenarios.sortModule(client, ModulePage, false, 0, 'Name', 'name', 'data-name', false, true);
    moduleCommonScenarios.sortModule(client, ModulePage, false, 0, 'Increasing price', 'price', 'data-price', true, true);
    moduleCommonScenarios.sortModule(client, ModulePage, false, 0, 'Decreasing price', 'price-desc', 'data-price', true, false);
    moduleCommonScenarios.sortModule(client, ModulePage, false, 0, 'Popularity', 'scoring-desc', 'data-scoring', true, false);
  }, 'module');
  scenario('Select specific category then check the sort module by name, increasing price, decreasing price', client => {
    test('should select "Administration" from categories list', async () => {
      await client.waitForExistAndClick(ModulePage.categories_list);
      await client.getTextInVar(ModulePage.categories_option_number_span.replace('%CAT', 'Administration'), "category_modules_number");
      await client.getAttributeInVar(ModulePage.categories_by_name_option.replace('%CAT', 'Administration'), 'data-category-ref', 'categoryRef');
      await client.waitForExistAndClick(ModulePage.categories_option_link.replace('%CAT', 'Administration'));
    });
    test('should get module number', () => client.getModuleNumber(ModulePage.modules_number_span, 'modules_number'));
    moduleCommonScenarios.sortModule(client, ModulePage, false, 1, 'Name', 'name', 'data-name', false, true);
    moduleCommonScenarios.sortModule(client, ModulePage, false, 1, 'Increasing price', 'price', 'data-price', true, true);
    moduleCommonScenarios.sortModule(client, ModulePage, false, 1, 'Decreasing price', 'price-desc', 'data-price', true, false);
    test('should verify if the modules displayed are only modules of the selected category', () => client.checkNumberModule(ModulePage.modules_number_span, tab['modules_number']));
  }, 'module');
  scenario('Select category then search module who is in the selected category in module catalog page', client => {
    moduleCommonScenarios.searchModuleCategory(client, ModulePage, 'autoupgrade', 'catalog');
  }, 'module');
  scenario('Select category then search module who is not in the selected category in module catalog page', client => {
    moduleCommonScenarios.searchModuleCategory(client, ModulePage, 'ps_brandlist', 'catalog', false);
  }, 'module');
  scenario('Sort by each category then verify displayed modules', client => {
    test('should go to "Modules > Modules Catalog" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_catalog_submenu));
    test('should go to "Module Manager" page', () => client.waitForExistAndClick(Menu.Improve.Modules.modules_manager_submenu));
    test('should get the number of categories in the list', () => client.getCategoryNumber(ModulePage.category_list));
    moduleCommonScenarios.filterCategory(client, ModulePage);
  }, 'module');
  scenario('Sort by each category then verify displayed modules', client => {
    test('should go to "Module Manager" page', () => client.waitForExistAndClick(Menu.Improve.Modules.modules_manager_submenu));
    moduleCommonScenarios.DisableEnableModule(client, ModulePage);
  }, 'module');
  scenario('Select category then search module who is not in the selected category in module manager page', client => {
    moduleCommonScenarios.searchModuleCategory(client, ModulePage, 'ps_banner', 'manager', false);
  }, 'module');
  scenario('Select category then search module who is in the selected category in module manager page', client => {
    moduleCommonScenarios.searchModuleCategory(client, ModulePage, 'statsstock');
  }, 'module');
  scenario('Check then install "ps_mbo" module', client => {
    moduleCommonScenarios.installUninstallMboModule(client, ModulePage, AddProductPage, "ps_mbo", 'install');
  }, 'onboarding');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'module');
}, 'module', true);
