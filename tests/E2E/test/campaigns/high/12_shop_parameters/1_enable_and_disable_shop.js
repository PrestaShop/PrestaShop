const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {ShopParameters} = require('../../../selectors/BO/shopParameters/shop_parameters.js');
const {Menu} = require('../../../selectors/BO/menu.js');
let promise = Promise.resolve();

scenario('Configure shop in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  scenario('Disable shop in the Back Office', client => {
    test('should go to "Shop parameters" page', () => client.waitForExistAndClick(Menu.Configure.ShopParameters.shop_parameters_menu));
    test('should click on "Enable multistore"', () => client.scrollWaitForExistAndClick(ShopParameters.enable_multistore, 50));
    test('should click on "Save" button', () => client.waitForExistAndClick(ShopParameters.general_save_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParameters.general_success_panel, "The settings have been successfully updated."));
    test('should click on "Maintenance" tab', () => client.waitForExistAndClick(ShopParameters.maintenance_tab));
    test('should set the "Enable shop" to "NO"', () => client.waitAndSelectByValue(ShopParameters.enable_shop, "0"));
    test('should set the "Custom maintenance" textarea', () => client.setTextToEditor(ShopParameters.textarea_input.replace("%ID", 1), 'We are currently disabled our shop and will be back really soon.'));
    test('should switch to the "French" language', () => client.waitForExistAndClick(ShopParameters.language_option.replace("%LANG", 'Fr').replace("%ID", "1")));
    test('should set the "Custom maintenance" textarea', () => client.setTextToEditor(ShopParameters.textarea_input.replace("%ID", 2), 'Nous avons actuellement désactivés notre boutique et serons de retour très bientôt.'));
    test('should click on "Save" button', () => client.waitForExistAndClick(ShopParameters.save_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParameters.maintenance_success_panel, "Successful update."));
    test('should go to the front office', () => {
      return promise
        .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
        .then(() => client.switchWindow(1))
    });
    test('should check that the shop is disabled', () => client.checkTextValue(ShopParameters.maintenance_message, 'Nous avons actuellement désactivés notre boutique et serons de retour très bientôt.', 'contain'));
  }, 'common_client');

  scenario('Enable shop in the Back Office', client => {
    test('should set the "Enable shop" to "YES"', () => {
      return promise
        .then(() => client.switchWindow(0))
        .then(() => client.waitAndSelectByValue(ShopParameters.enable_shop, "1"));
    });
    test('should click on "Save" button', () => client.waitForExistAndClick(ShopParameters.save_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParameters.maintenance_success_panel, "Successful update."));
    test('should go to the front office', () => {
      return promise
        .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
        .then(() => client.switchWindow(1))
    });
    test('should check that the shop is enabled', () => client.signInFO(AccessPageFO));
  }, 'common_client');
}, 'common_client', true);
