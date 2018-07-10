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
    test('should go to "Modules" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_services_submenu));
    test('should click on "Selection" tab', () => client.waitForExistAndClick(Menu.Improve.Modules.selection_tab));
    test('should search for the module "Advanced top menu"', () => {
      return promise
        .then(() => client.waitAndSetValue(ModulePage.modules_search_input, 'pm_advancedtopmenu'))
        .then(() => client.waitForExistAndClick(ModulePage.modules_search_button));
    });
    test('should click on "Discover" button', () => client.waitForExistAndClick(ModulePage.discover_button));
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(1))
        .then(() => client.checkTextValue(ModulePage.module_name, "Advanced Top Menu", 'contain'))
        .then(() => client.switchWindow(0));
    });
  }, 'common_client');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
