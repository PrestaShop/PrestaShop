const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const module_common_scenarios = require('./module');
let promise = Promise.resolve();

scenario('Check sort module by "Name"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'module');
  /**@TODO we can add contact form search after we merge **/
  scenario('Check the sort module by name ', client => {
    test('should go to "Modules" page', () => client.goToSubtabMenuPage(ModulePage.modules_subtab, ModulePage.modules_subtab));
    test('should set the name of the module in the search input', () => client.waitAndSetValue(ModulePage.module_selection_input, 'contact form'));
    test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.selection_search_button));
    test('should get module number', () => client.getTextInVar(ModulePage.modules_number, "modules_number"));
    module_common_scenarios.sortModule(client, ModulePage, "name", "data-name");
    module_common_scenarios.sortModule(client, ModulePage, "price", "data-price");
    module_common_scenarios.sortModule(client, ModulePage, "price-desc", "data-price");
    module_common_scenarios.sortModule(client, ModulePage, "scoring-desc", "data-scoring");
  }, 'module');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'module')
}, 'module');