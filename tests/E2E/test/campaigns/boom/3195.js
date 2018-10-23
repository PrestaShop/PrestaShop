const {Installation} = require('../../selectors/BO/installation');
const {AccessPageBO} = require('../../selectors/BO/access_page');
const {ModulePage} = require('../../selectors/BO/module_page');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {AccessPageFO} = require('../../selectors/FO/access_page');
const commonInstallation = require('../common_scenarios/common_installation');
const moduleCommonScenarios = require('../common_scenarios/module');
const welcomeScenarios = require('../common_scenarios/welcome');

let promise = Promise.resolve();


scenario('BOOM-3195: The shop installation', () => {

  scenario('Open the browser and download the RC', client => {
    test('should open the browser', () => client.open());
    test('should rename folder "admin" to "admin-dev" and "install" to "install-dev"', () => client.renameFolders(rcTarget));
    if (rcLink !== "") {
      test('should download the RC', () => {
        return promise
          .then(() => client.getRCName(rcLink))
          .then(() => client.linkAccess(rcLink))
          .then(() => client.WaitForDownload(Installation.download_version));
      })
    }
    test('should go to the last stable version URL', () => client.localhost(UrlLastStableVersion));
  }, 'installation');

  scenario('Installation of the last stable version of prestashop', () => {
    commonInstallation.prestaShopInstall(Installation, "en", "france");
  }, 'installation');

  scenario('Open the browser and connect to the Back Office', client => {
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO, UrlLastStableVersion));
  }, 'installation');

  welcomeScenarios.findAndCloseWelcomeModal('installation');

  scenario('Install "Top-sellers block" and "New products block" modules From Cross selling', client => {
    moduleCommonScenarios.installModule(client, ModulePage, AddProductPage, "ps_bestsellers");
    moduleCommonScenarios.installModule(client, ModulePage, AddProductPage, "ps_newproducts");
  }, 'installation');

  scenario('Install " 1-Click Upgrade " From Cross selling and configure it', client => {
    moduleCommonScenarios.installModule(client, ModulePage, AddProductPage, "autoupgrade");
    test('should click on "configure" button', () => client.waitForExistAndClick(ModulePage.configure_module_button.split('%moduleTechName').join("autoupgrade")));
    test('should deactivate the shop', () => {
      return promise
        .then(() => client.waitForVisibleElement(ModulePage.confirm_maintenance_shop_icon))
        .then(() => client.waitForExistAndClick(ModulePage.maintenance_shop));
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
        .then(() => client.waitForExistAndClick(ModulePage.refresh_button));
    });
    test('should click on "Upgrade PrestaShop now!" button', () => client.waitForExistAndClick(ModulePage.upgrade_button));
    test('should wait until the Upgrade is finished', () => client.waitForExist(ModulePage.loader_tag, 310000));
    test('should check the success message appear', () => client.checkTextValue(ModulePage.success_msg, 'Upgrade complete'));
  }, 'installation');

  scenario('logout successfully from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'installation');

  scenario('Login in the Front Office', client => {
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO, UrlLastStableVersion));
  }, 'installation');

  scenario('Check the existence of "Top sellers block" and "New products block"', client => {
    test('should set the language of shop to "English"', () => client.changeLanguage());
    test('should check the existence of "Top sellers" block', () => client.waitForVisible(AccessPageFO.top_sellers_block));
    test('should check the existence of "New products" block', () => client.waitForVisible(AccessPageFO.new_products_block));
  }, 'installation');

  scenario('Logout from the back office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'installation');
}, 'installation', true);
