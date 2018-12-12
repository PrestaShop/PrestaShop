const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {Menu} = require('../../../../selectors/BO/menu');
const {ThemeCatalog} = require('../../../../selectors/BO/design/theme_catalog');
const {AddProductPage} = require('../../../../selectors/BO/add_product_page');
const welcomeScenarios = require('../../../common_scenarios/welcome');

let promise = Promise.resolve();

scenario('Check the addons theme in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Check the addons theme', client => {
    test('should go to "Theme catalog" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.theme_catalog_submenu));
    test('should click on "Discover all of the themes" button', () => client.waitForExistAndClick(ThemeCatalog.discover_all_of_the_theme_button, 1000));
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(1))
        .then(() => client.checkTextValue(ThemeCatalog.category_name_text, "PrestaShop Templates", 'contain', 1000))
        .then(() => client.switchWindow(0));
    });
    test('should click on "Discover" button of the second theme', () => {
      return promise
        .then(() => client.moveToObject(ThemeCatalog.discover_button.replace('%POS', '2'), 2000))
        .then(() => client.getTextInVar(ThemeCatalog.theme_name.replace('%POS', '2'), 'themeName'))
        .then(() => client.waitForExistAndClick(ThemeCatalog.discover_button.replace('%POS', '2')));
    });
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(2))
        .then(() => client.checkTextValue(ThemeCatalog.theme_header_name, tab['themeName'], 'contain'))
        .then(() => client.switchWindow(0));
    });
    test('should check then close the "Symfony" toolbar', () => {
        return promise
            .then(() => {
                if (global.ps_mode_dev) {
                    client.waitForExistAndClick(AddProductPage.symfony_toolbar);
                }
            })
            .then(() => client.pause(1000));
    });
    test('should click on "Discover" button of the 17th theme', () => {
      return promise
        .then(() => client.moveToObject(ThemeCatalog.discover_button.replace('%POS', '17'), 2000))
        .then(() => client.getTextInVar(ThemeCatalog.theme_name.replace('%POS', '17'), 'themeName'))
        .then(() => client.waitForExistAndClick(ThemeCatalog.discover_button.replace('%POS', '17')));
    });
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(3))
        .then(() => client.checkTextValue(ThemeCatalog.theme_header_name, tab['themeName'], 'contain'))
        .then(() => client.switchWindow(0));
    });
    test('should search for the theme on the prestashop addons', () => {
      return promise
        .then(() => client.moveToObject(ThemeCatalog.search_addons_input, 2000))
        .then(() => client.waitAndSetValue(ThemeCatalog.search_addons_input, 'mode'))
        .then(() => client.keys('Enter'));
    });
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(4))
        .then(() => client.checkTextValue(ThemeCatalog.search_name, "mode"))
        .then(() => client.switchWindow(0));
    });
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
