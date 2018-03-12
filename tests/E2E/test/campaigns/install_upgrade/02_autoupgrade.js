const {Installation} = require('../../selectors/BO/installation');
const {AccessPageBO} = require('../../selectors/BO/access_page');
const {ModulePage} = require('../../selectors/BO/module_page');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {OnBoarding} = require('../../selectors/BO/onboarding.js');
const {AccessPageFO} = require('../../selectors/FO/access_page');
const {ShopParameter} = require('../../selectors/BO/shopParameters/index');

const commonScenarios = require('../high/02_product/product');
const commonInstallation = require('./common_installation');
const moduleCommonScenarios = require('../high/10_module/module');
const orderCommonScenarios = require('../high/01_orders/order');

let promise = Promise.resolve();

var productData = {
  name: 'UpgradeProduct',
  reference: 'product',
  quantity: "10",
  price: '5',
  image_name: 'image_test.jpg',
};

scenario('The shop installation', () => {

  scenario('Open the browser and download the RC', client => {
    test('should open the browser', () => client.open());
    test('should rename folder "admin" to "admin-dev" and "install" to "install-dev"', () => client.renameFolders(rcTarget));
    if (rcLink !== "") {
      test('should download the RC', () => {
        return promise
          .then(() => client.getRCName(rcLink))
          .then(() => client.linkAccess(rcLink))
          .then(() => client.WaitForDownload(Installation.download_version))
      })
    }
    test('should go to the last stable version URL', () => client.localhost(UrlLastStableVersion));
  }, 'installation');

  scenario('Installation of the last stable version of prestashop', client => {
    commonInstallation.prestaShopInstall(Installation, "en", "france");
  }, 'installation');

  scenario('Open the browser and connect to the BO', client => {
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO, UrlLastStableVersion));
  }, 'installation');

  scenario('Close the onboarding modal ', client => {
    test('should close the onboarding modal', () => {
      return promise
        .then(() => client.isVisible(OnBoarding.welcome_modal))
        .then(() => client.closeBoarding(OnBoarding.popup_close_button))
    });
  }, 'installation');

  /**
   * This scenario is based on the bug described in this ticket
   * http://forge.prestashop.com/browse/BOOM-3195
   **/

  scenario('Install "Top-sellers block" and "New products block" modules From Cross selling', client => {
    moduleCommonScenarios.installModule(client, ModulePage, AddProductPage, "ps_bestsellers");
    moduleCommonScenarios.installModule(client, ModulePage, AddProductPage, "ps_newproducts");
  }, 'installation');

  /****** END *****/

  scenario('Install " 1-Click Upgrade " From Cross selling and configure it', client => {
    moduleCommonScenarios.installModule(client, ModulePage, AddProductPage, "autoupgrade");
    test('should click on "configure" button', () => client.waitForExistAndClick(ModulePage.configure_module_button.split('%moduleTechName').join(autoupgrade)));
    test('should deactivate the shop', () => {
      return promise
        .then(() => client.waitForVisibleElement(ModulePage.confirm_maintenance_shop_icon))
        .then(() => client.waitForExistAndClick(ModulePage.maintenance_shop))
    });
    if (rcLink !== "") {
      test('should copy the downloaded RC to the auto upgrade directory', () => client.copyFileToAutoUpgrade(downloadsFolderPath, filename, rcTarget + "admin-dev/autoupgrade/download"));
    }
    test('should click on "More options (Expert mode)" button', () => client.waitForExistAndClick(ModulePage.more_option_button));
    test('should select the "Channel" option', () => client.waitAndSelectByValue(ModulePage.channel_select, "archive"));
    test('should select the "Archive to use" option', () => client.waitAndSelectByValue(ModulePage.archive_select, global.filename));
    test('should set the Number of the version you want to upgrade to', () => client.waitAndSetValue(ModulePage.version_number, global.filename.replace(".zip", "")));
    test('should click on "save" button', () => client.waitForExistAndClick(ModulePage.save_button));
    test('should verify the success message', () => client.waitForVisibleElement(ModulePage.save_message));
    test('should click on "refresh the page" button', () => {
      return promise
        .then(() => client.moveToObject(ModulePage.upgrade_block))
        .then(() => client.waitForExistAndClick(ModulePage.refresh_button))
    });
    test('should click on "Upgrade PrestaShop now!" button', () => client.waitForExistAndClick(ModulePage.upgrade_button));
    test('should wait until the Upgrade is finished', () => client.waitForExist(ModulePage.loader_tag, 310000));
    test('should check the success message appear', () => client.checkTextValue(ModulePage.success_msg, 'Upgrade complete'));
  }, 'installation');

  scenario('logout successfully from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'installation');

  scenario('Connect to the BO', client => {
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO, UrlLastStableVersion));
  }, 'installation');

  scenario('Enable shop in the Back Office', client => {
    test('should go to "Shop parameters" page', () => client.waitForExistAndClick(ShopParameter.maintenance_mode_link));
    test('should set the shop "Enable"', () => client.waitForExistAndClick(ShopParameter.enable_shop.replace("%s", 'on')));
    test('should click on "Save" button', () => client.waitForExistAndClick(ShopParameter.save_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParameter.success_panel, "The settings have been successfully updated."));
  }, 'common_client');

  commonScenarios.createProduct(AddProductPage, productData);

  scenario('Login in the Front Office', client => {
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO, UrlLastStableVersion));
  }, 'installation');

  /**
   * This scenario is based on the bug described in this ticket
   * http://forge.prestashop.com/browse/BOOM-3195
   **/

  scenario('Check the existence of "Top sellers block" and "New products block"', client => {
    test('should set the language of shop to "English"', () => client.changeLanguage());
    test('should check the existence of "Top sellers" block', () => client.waitForVisible(AccessPageFO.top_sellers_block));
    test('should check the existence of "New products" block', () => client.waitForVisible(AccessPageFO.new_products_block));
  }, 'installation');

  /****** END *****/

  orderCommonScenarios.createOrder();

  scenario('Logout from the back office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'installation');
}, 'installation', true);
