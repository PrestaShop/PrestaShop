const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {Menu} = require('../../../../selectors/BO/menu');
const {ThemeAndLogo} = require('../../../../selectors/BO/design/theme_and_logo');
let promise = Promise.resolve();

scenario('Check the addons theme in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Check the addons theme', client => {
    test('should go to "Theme & Logo" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.theme_logo_submenu));
    test('should click on "Discover all of the themes" button', () => {
      return promise
        .then(() => client.setAttributeById('footer'))
        .then(() => client.waitForExistAndClick(ThemeAndLogo.discover_all_of_the_theme_button, 1000));
    });
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(1))
        .then(() => client.checkTextValue(ThemeAndLogo.category_name_text, "PrestaShop Templates", 'contain', 1000))
        .then(() => client.switchWindow(0));
    });
    test('should click on "Discover" button of the second theme', () => {
      return promise
        .then(() => client.moveToObject(ThemeAndLogo.discover_button.replace('%POS', '2'), 2000))
        .then(() => client.getTextInVar(ThemeAndLogo.theme_name.replace('%POS', '2'), 'themeName'))
        .then(() => client.waitForExistAndClick(ThemeAndLogo.discover_button.replace('%POS', '2')));
    });
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(2))
        .then(() => client.checkTextValue(ThemeAndLogo.theme_header_name, tab['themeName'], 'contain'))
        .then(() => client.switchWindow(0));
    });
    test('should click on "Discover" button of the 17th theme', () => {
      return promise
        .then(() => client.moveToObject(ThemeAndLogo.discover_button.replace('%POS', '17'), 2000))
        .then(() => client.getTextInVar(ThemeAndLogo.theme_name.replace('%POS', '17'), 'themeName'))
        .then(() => client.waitForExistAndClick(ThemeAndLogo.discover_button.replace('%POS', '17')));
    });
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(3))
        .then(() => client.checkTextValue(ThemeAndLogo.theme_header_name, tab['themeName'], 'contain'))
        .then(() => client.switchWindow(0));
    });
    test('should search for the theme on the prestashop addons', () => {
      return promise
        .then(() => client.moveToObject(ThemeAndLogo.search_addons_input, 2000))
        .then(() => client.waitAndSetValue(ThemeAndLogo.search_addons_input, 'mode'))
        .then(() => client.keys('Enter'));
    });
    test('should check that the page is well opened', () => {
      return promise
        .then(() => client.switchWindow(4))
        .then(() => client.checkTextValue(ThemeAndLogo.search_name, "mode"))
        .then(() => client.switchWindow(0));
    });
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);