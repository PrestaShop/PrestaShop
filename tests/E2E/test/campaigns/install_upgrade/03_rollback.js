const {AccessPageBO} = require('../../selectors/BO/access_page');
const {ModulePage} = require('../../selectors/BO/module_page');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {AccessPageFO} = require('../../selectors/FO/access_page');
const {ShopParameters} = require('../../selectors/BO/shopParameters/shop_parameters.js');

const commonScenarios = require('../common_scenarios/product');
const orderCommonScenarios = require('../common_scenarios/order');

let promise = Promise.resolve();

let productData = {
  name: 'RollingBackProduct',
  reference: 'product',
  quantity: "10",
  price: '5',
  image_name: 'image_test.jpg',
};

scenario('The shop installation', () => {

  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO, UrlLastStableVersion));
  }, 'installation');

  scenario('Rollback to the old version ', client => {
    test('should click on "Module" button', () => client.waitForExistAndClick(ModulePage.module_autoUpgrade_menu));
    test('should deactivate the shop', () => {
      return promise
        .then(() => client.waitForVisibleElement(ModulePage.confirm_maintenance_shop_icon))
        .then(() => client.waitForExistAndClick(ModulePage.maintenance_shop));
    });
    test('should click on "Choose your backup" button', () => client.waitForExistAndClick(ModulePage.module_autoUpgrade_menu));
    test('should choose the back up version', () => client.waitForExistAndClick(ModulePage.rollback_version));
    test('should click on the "ROLLBACK" button', () => client.waitForExistAndClick(ModulePage.rollback_button));
    test('should wait until the rollback is finished', () => client.waitForExist(ModulePage.loader_tag, 310000));
    test('should check the success message appear', () => client.checkTextValue(ModulePage.success_msg, 'Rollback complete'));
  }, 'installation');

  scenario('logout successfully from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'installation');

  scenario('Connect to the Back Office', client => {
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO, UrlLastStableVersion));
  }, 'installation');

  scenario('Enable shop in the Back Office', client => {
    test('should go to "Shop parameters" page', () => client.waitForExistAndClick(ShopParameters.maintenance_mode_link));
    test('should set the shop "Enable" to "Yes"', () => client.waitAndSelectByValue(ShopParameters.enable_shop, "1"));
    test('should click on "Save" button', () => client.waitForExistAndClick(ShopParameters.save_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParameters.maintenance_success_panel, "Successful update."));
  }, 'common_client');

  commonScenarios.createProduct(AddProductPage, productData);

  scenario('Login in the Front Office', client => {
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO, UrlLastStableVersion));
  }, 'installation');

  orderCommonScenarios.createOrder();

  scenario('Logout from the back office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'installation');

}, 'installation', true);
