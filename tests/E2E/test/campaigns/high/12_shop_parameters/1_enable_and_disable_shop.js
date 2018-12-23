const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {ShopParameters} = require('../../../selectors/BO/shopParameters/shop_parameters.js');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const welcomeScenarios = require('../../common_scenarios/welcome');
let promise = Promise.resolve();

scenario('Configure shop in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Disable shop in the Back Office', client => {
    test('should go to "Shop parameters" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.general_submenu));
    test('should close symfony Profiler', () => {
      return promise
        .then(() => {
          if (global.ps_mode_dev) {
            client.waitForExistAndClick(AddProductPage.symfony_toolbar);
          }
        });
    });
    test('should click on "Enable multistore"', () => client.scrollWaitForExistAndClick(ShopParameters.enable_disable_multistore_toggle_button.replace("%ID",1), 50));
    test('should click on "Save" button', () => client.waitForExistAndClick(ShopParameters.general_save_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParameters.success_box, "Successful update."));
    test('should click on "Maintenance" tab', () => client.waitForExistAndClick(ShopParameters.maintenance_tab));
    test('should set the "Enable shop" parameter to "NO"', () => client.waitForExistAndClick(ShopParameters.enable_shop.replace("%ID", '0')));
    test('should set the "Custom maintenance" textarea', () => client.setEditorText(ShopParameters.textarea_input.replace("%ID", 1), 'We are currently disabled our shop and will be back really soon.'));
    test('should switch to the "French" language', () => client.waitForExistAndClick(ShopParameters.language_option.replace("%LANG", 'Fr').replace("%ID", "1")));
    test('should set the "Custom maintenance" textarea', () => client.setEditorText(ShopParameters.textarea_input.replace("%ID", 2), 'Nous avons actuellement désactivés notre boutique et serons de retour très bientôt.'));
    test('should click on "Save" button', () => client.waitForExistAndClick(ShopParameters.save_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParameters.success_box, "Successful update."));
    test('should go to the front office', () => {
      return promise
        .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
        .then(() => client.switchWindow(1));
    });
    test('should check that the shop is disabled', () => client.checkTextValue(ShopParameters.maintenance_message, 'Nous avons actuellement désactivés notre boutique et serons de retour très bientôt.', 'contain'));
  }, 'common_client');

  scenario('Enable shop in the Back Office', client => {
    test('should set the "Enable shop" to "YES"', () => {
      return promise
        .then(() => client.switchWindow(0))
        .then(() => client.waitForExistAndClick(ShopParameters.enable_shop.replace("%ID", '1')));
    });
    test('should click on "Save" button', () => client.waitForExistAndClick(ShopParameters.save_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParameters.success_box, "Successful update."));
    test('should go to the front office', () => {
      return promise
        .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
        .then(() => client.switchWindow(1));
    });
    test('should check that the shop is enabled', () => client.signInFO(AccessPageFO));
  }, 'common_client');
  scenario('Disable "MultiStore" in the Back Office', client => {
    test('should go back to the "Back Office"', () => client.switchWindow(0));
    test('should go to "General" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.general_submenu));
    test('should disable multistore', () => client.scrollWaitForExistAndClick(ShopParameters.enable_disable_multistore_toggle_button.replace('%ID', 0)));
    test('should click on "Save" button', () => client.scrollWaitForExistAndClick(ShopParameters.save_button));
  }, 'common_client');
}, 'common_client', true);
