const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Menu} = require('../../../selectors/BO/menu');
const {ModulesCatalogPage} = require('../../../selectors/BO/addons_catalog_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');

let promise = Promise.resolve();

scenario('Check the addons catalog page in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Check the addons catalog page', client => {
    test('should go to "Modules Catalog" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_catalog_submenu));
    test('should click on "Modules Selections" tab', () => client.waitForExistAndClick(Menu.Improve.Modules.modules_selections_submenu));
    test('should click on "View all the Traffic modules" link', () => client.waitForExistAndClick(ModulesCatalogPage.view_all_traffic_modules_link));
    test('should check then close the "Symfony" toolbar', () => {
      return promise
        .then(() => {
          if (global.ps_mode_dev) {
            client.waitForExistAndClick(AddProductPage.symfony_toolbar);
          }
        })
        .then(() => client.pause(1000));
    });

    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.pause(2000))
        .then(() => client.switchWindow(1))
        .then(() => client.refresh())  /**Adding refreshing page because sometimes is not well opened we have to refresh it before */
        .then(() => client.checkTextValue(ModulesCatalogPage.category_name_text, "Traffic", 'contain'))
        .then(() => client.switchWindow(0));
    });
    test('should click on "Discover" button of the "SEO Expert" module', () => client.waitForExistAndClick(ModulesCatalogPage.discover_button));
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(2))
        .then(() => client.refresh())  /**Adding refreshing page because sometimes is not well opened we have to refresh it before */
        .then(() => client.checkTextValue(ModulesCatalogPage.module_name, "SEO Expert", 'contain'))
        .then(() => client.switchWindow(0));
    });
    test('should click on "Discover the payment modules" link', () => {
      return promise
        .then(() => client.moveToObject(ModulesCatalogPage.discover_payment_modules_link))
        .then(() => client.waitForVisibleAndClick(ModulesCatalogPage.discover_payment_modules_link))
        .then(() => client.pause(2000));
    });
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(3))
        .then(() => client.refresh())
        .then(() => client.checkTextValue(ModulesCatalogPage.category_name_text, "Payment", 'contain'))
        .then(() => client.switchWindow(0));
    });
    test('should click on "View all modules" link', () => {
      return promise
        .then(() => client.scrollWaitForExistAndClick(ModulesCatalogPage.view_all_modules_button))
        .then(() => client.pause(2000));
    });
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(4))
        .then(() => client.refresh())
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
        .then(() => client.refresh())
        .then(() => client.checkTextValue(ModulesCatalogPage.search_name, "chat"))
        .then(() => client.switchWindow(0));
    });
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
