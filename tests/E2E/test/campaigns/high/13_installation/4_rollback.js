const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {CheckoutOrderPage} = require('../../../selectors/FO/order_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {ShopParameter} = require('../../../selectors/BO/shopParameters/index');

const commonScenarios = require('../02_product/product');

let promise = Promise.resolve();

var productData = {
    name: 'RollingBackProduct',
    quantity: "10",
    price: '5',
    image_name: 'image_test.jpg',
};

scenario('The shop installation', client => {

    scenario('Open the browser and connect to the BO', client => {
        test('should open the browser', () => client.open());
        test('should log in successfully in BO', () => client.signInBO(AccessPageBO, UrlLastStableVersion));
    }, 'installation');

    scenario('Rollback to the old version ', client => {
        test('should click on "Module" button', () => client.waitForExistAndClick(ModulePage.module_autoUpgrade_menu));
        test('should deactivate the shop', () => {
            return promise
                .then(() => client.waitForVisibleElement(ModulePage.confirm_maintenance_shop_icon))
                .then(() => client.waitForExistAndClick(ModulePage.maintenance_shop))
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
