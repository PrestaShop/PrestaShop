const {Installation} = require('../../../selectors/BO/installation');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {OnBoarding} = require('../../../selectors/BO/onboarding.js');
const common_installation = require('./common_installation');
let promise = Promise.resolve();
scenario('The shop installation', client => {
    scenario('Open the browser and download the RC', client => {
       test('should open the browser', () => client.open());
     //  test('should log in install page ', () => client.linkAccess(rcLink));
     //  test('should click on "download now" button', () => client.waitForExistAndClick(Installation.prestashop_download_button));
     //  test('should click on the "Download this version" button and wait for the download to complete', () => client.clickAndWaitForDownload(Installation.download_version));
    }, 'installation');

    scenario('Go to the installation interface', client => {
        test('should log in install page ', () => client.localhost());
    }, 'installation');

  scenario('Installation of the last stable version of prestashop', client => {
        common_installation.prestaShopInstall(Installation, "en", "france");
    }, 'installation');

    scenario('Open the browser and connect to the BO', client => {
        test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
    }, 'installation');
    scenario('Close the onboarding modal ', client => {
        test('should close the onboarding modal', () => {
            return promise
                .then(() => client.isVisible(OnBoarding.welcome_modal))
                .then(() => client.closeBoarding(OnBoarding.popup_close_button))
        });
    }, 'installation');
    /** @todo: this part of code will be common function we  **/
    scenario('Install " 1-Click Upgrade " From Cross selling and configure it', client => {
        test('should click on "Module" button', () => client.goToSubtabMenuPage(ModulePage.modules_subtab, ModulePage.modules_subtab));
        test('should set the name of the module in the search input', () => client.waitAndSetValue(ModulePage.module_selection_input, "autoupgrade"));
        test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.selection_search_button));
        test('should click on "Install" button', () => client.waitForExistAndClick(ModulePage.install_button));
        test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
        test('should click on "Installed Modules"', () => client.waitForVisibleAndClick(ModulePage.installed_modules_tabs));
        test('should set the name of the module in the search input', () => client.waitAndSetValue(ModulePage.modules_search_input, "autoupgrade"));
        test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.modules_search_button));
        test('should check if the module "autoupgrade" was installed', () => client.checkTextValue(ModulePage.built_in_module, "1", "contain"));
        test('should click on "configure" button', () => client.waitForExistAndClick(ModulePage.action_module_built_button));

        test('should copy the downloaded RC to the autoupgrade directory', () => client.copyFileToAutoUpgrade(downloadsFolderPath, rcTarget));
        test('should click on "More options (Expert mode)" button', () => client.waitForExistAndClick(ModulePage.more_option_button));
        test('should select the "Channel" option', () => client.waitAndSelectByValue(ModulePage.channel_select, "archive"));
        test('should select the "Archive to use" option', () => client.waitAndSelectByValue(ModulePage.archive_select, global.filename));
        test('should set the Number of the version you want to upgrade to', () => client.waitAndSetValue(ModulePage.version_number, global.filename.replace(".zip", "")));
        test('should click on "save" button', () => client.waitForExistAndClick(ModulePage.save_button));
        test('should verify the success message', () => client.waitForVisibleElement(ModulePage.save_message));
        test('should click on "refrech the page" button', () => {
            return promise
                .then(() => client.moveToObject(ModulePage.upgrade_block))
                .then(() => client.waitForExistAndClick(ModulePage.refresh_button))
        });
        test('should click on "Upgrade PrestaShop now!" button', () => client.waitForExistAndClick(ModulePage.upgrade_button));
        test('should wait until the Upgrade is finished', () => client.waitForExist(ModulePage.loader_tag, 310000));
    }, 'installation');





/*    scenario('Logout from the back office', client => {
        test('should logout successfully from the Back Office', () => client.signOutBO());
    }, 'installation');*/

}, 'installation');
