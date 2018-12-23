const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {Menu} = require('../../../selectors/BO/menu');
let promise = Promise.resolve();

scenario('Discover "Advanced top menu" module in Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  scenario('Check that the "Advanced top menu" module is opened', client => {
    test('should go to "Modules Catalog" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_catalog_submenu));
    test('should search for the module "Advanced top menu"', () => {
      return promise
        .then(() => client.waitAndSetValue(ModulePage.module_selection_input, 'pm_advancedtopmenu'))
        .then(() => client.waitForExistAndClick(ModulePage.selection_search_button));
    });
    test('should click on "Discover" button', () => client.waitForExistAndClick(ModulePage.discover_button));
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(1))
        .then(() => client.refresh()) /**Adding refreshing page because sometimes is not well opened we have to refresh it before */
        .then(() => client.checkTextValue(ModulePage.module_name, "Advanced Top Menu", 'contain'))
        .then(() => client.switchWindow(0));
    });
  }, 'common_client');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
