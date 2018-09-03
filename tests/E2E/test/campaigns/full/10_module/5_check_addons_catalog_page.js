const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Menu} = require('../../../selectors/BO/menu');
const {ModulesCatalogPage} = require('../../../selectors/BO/addons_catalog_page');
let promise = Promise.resolve();

scenario('Check the addons catalog page in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Check the addons catalog page', client => {
    test('should go to "Modules" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_catalog_submenu));
    test('should click on "View all the Traffic modules" link', () => client.waitForExistAndClick(ModulesCatalogPage.view_all_traffic_modules_link));
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(1))
        .then(() => client.checkTextValue(ModulesCatalogPage.category_name_text, "Traffic", 'contain'))
        .then(() => client.switchWindow(0));
    });
    test('should click on "Discover" button of the "SEO Expert" module', () => client.waitForExistAndClick(ModulesCatalogPage.discover_button));
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(2))
        .then(() => client.checkTextValue(ModulesCatalogPage.module_name, "SEO Expert", 'contain'))
        .then(() => client.switchWindow(0));
    });
    test('should click on "Discover the payment modules" link', () => client.scrollWaitForExistAndClick(ModulesCatalogPage.discover_payment_modules_link));
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(3))
        .then(() => client.checkTextValue(ModulesCatalogPage.category_name_text, "Payment", 'contain'))
        .then(() => client.switchWindow(0));
    });
    test('should click on "View all modules" link', () => client.scrollWaitForExistAndClick(ModulesCatalogPage.view_all_modules_button));
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(4))
        .then(() => client.isExisting(ModulesCatalogPage.prestashop_addons_logo, 2000))
        .then(() => client.switchWindow(0));
    });
    test('should search for the modules on the prestashop addons', () => {
      return promise
        .then(() => client.waitAndSetValue(ModulesCatalogPage.search_addons_input, 'chat'))
        .then(() => client.keys('Enter'));
    });
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(5))
        .then(() => client.checkTextValue(ModulesCatalogPage.search_name, "chat"))
        .then(() => client.switchWindow(0));
    });
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);