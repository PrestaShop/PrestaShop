const {Installation} = require('../../../selectors/BO/installation');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {CheckoutOrderPage} = require('../../../selectors/FO/order_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {OnBoarding} = require('../../../selectors/BO/onboarding.js');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {ShopParameter} = require('../../../selectors/BO/shopParameters/index');

const {productPage} = require('../../../selectors/FO/product_page');

const commonScenarios = require('../02_product/product');
const commonInstallation = require('./common_installation');
const moduleCommonScenarios = require('../10_module/module');

let promise = Promise.resolve();

var productData = {
    name: 'UpgradeProduct',
    quantity: "10",
    price: '5',
    image_name: 'image_test.jpg',
};

scenario('The shop installation', client => {
    scenario('Open the browser and download the RC', client => {
        test('should open the browser', () => client.open());
       // test('should log in install page ', () => client.linkAccess(rcLink));
       // test('should click on "download now" button', () => client.waitForExistAndClick(Installation.prestashop_download_button));
       // test('should click on the "Download this version" button and wait for the download to complete', () => client.clickAndWaitForDownload(Installation.download_version));
    }, 'installation');
    scenario('Go to the installation interface', client => {
        test('should log in install page ', () => client.localhost(UrlLastStableVersion));
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
    /** @todo: must add install common function  **/
    scenario('Install " 1-Click Upgrade " From Cross selling and configure it', client => {
        moduleCommonScenarios.installModule(client, ModulePage, AddProductPage, "autoupgrade");
        test('should click on "configure" button', () => client.waitForExistAndClick(ModulePage.action_module_built_button));
        test('should deactivate the shop', () => {
            return promise
                .then(() => client.waitForVisibleElement(ModulePage.confirm_maintenance_shop_icon))
                .then(() => client.waitForExistAndClick(ModulePage.maintenance_shop))
        });
        test('should copy the downloaded RC to the auto upgrade directory', () => client.copyFileToAutoUpgrade(downloadsFolderPath, filename, rcTarget));
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
    /** @todo: must add create order in FO common function  **/
    scenario('Create order in FO', client => {
        test('should change the FO language to english', () => client.changeLanguage());
        test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, 'UpgradeProduct'));
        test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
        test('should set the product "quantity"', () => client.waitAndSetValue(productPage.first_product_quantity, "4"));
        test('should click on "Add to cart" button  ', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
        test('should click on proceed to checkout button 1', () => client.waitForExistAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
        test('should click on proceed to checkout button 2', () => client.waitForExistAndClick(CheckoutOrderPage.proceed_to_checkout_button));
        test('should click on confirm adress button', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step2_continue_button));
        test('should choose shipping method my carrier', () => client.waitForExistAndClick(CheckoutOrderPage.shipping_method_option));
        test('should create message', () => client.waitAndSetValue(CheckoutOrderPage.message_textarea, 'Order message test'));
        test('should click on "confirm delivery" button', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step3_continue_button));
        test('should set the payment type "Payment by bank wire"', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step4_payment_radio));
        test('should set "the condition to approve"', () => client.waitForExistAndClick(CheckoutOrderPage.condition_check_box));
        test('should click on order with an obligation to pay button', () => client.waitForExistAndClick(CheckoutOrderPage.confirmation_order_button));
        test('should check the order confirmation', () => client.checkTextValue(CheckoutOrderPage.confirmation_order_message, 'YOUR ORDER IS CONFIRMED', "contain"));
    }, 'installation');
    scenario('Logout from the back office', client => {
        test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
    }, 'installation');
}, 'installation');
