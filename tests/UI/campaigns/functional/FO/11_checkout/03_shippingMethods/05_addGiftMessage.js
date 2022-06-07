require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const orderSettingsPage = require('@pages/BO/shopParameters/orderSettings');
const ordersPage = require('@pages/BO/orders');
const orderPageTabListBlock = require('@pages/BO/orders/view/tabListBlock');

// Import FO pages
const productPage = require('@pages/FO/product');
const homePage = require('@pages/FO/home');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const foLoginPage = require('@pages/FO/login');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');
const {PaymentMethods} = require('@data/demo/paymentMethods');

const baseContext = 'functional_FO_checkout_shippingMethods_addGiftMessage';

let browserContext;
let page;

/*
Scenario:
- Enable 'Offer gift wrapping' from Shop parameters > order settings page
- Create an order from FO (check gift checkbox and add a message from shipping step)
- Go to BO > Orders > view last order page
- Check that the gift message is visible on carriers tab
- Disable 'Offer gift wrapping' from Shop parameters > order settings page
 */
describe('FO - Checkout - Shipping methods : Add gift message', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('PRE-TEST: Enable \'Offer gift wrapping\'', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Shop Parameters > Order Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.shopParametersParentLink, dashboardPage.orderSettingsLink);

      await orderSettingsPage.closeSfToolBar(page);

      const pageTitle = await orderSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
    });

    it('should enable \'Offer gift wrapping\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableGiftWrapping', baseContext);

      const result = await orderSettingsPage.setGiftOptions(page, true, 0, 'None', false);
      await expect(result).to.contains(orderSettingsPage.successfulUpdateMessage);
    });
  });

  describe('Create an order from FO and add a gift message', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await orderSettingsPage.viewMyShop(page);

      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFOToCheck', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should add the fourth product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await homePage.goToProductPage(page, 4);

      await productPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getNumberFromText(page, cartPage.cartProductsCount);
      await expect(notificationsNumber).to.be.equal(1);
    });

    it('should proceed to checkout and go to shipping step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShippingStep', baseContext);

      await cartPage.clickOnProceedToCheckout(page);

      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should check that gift checkbox is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGiftVisibility', baseContext);

      const isGiftCheckboxVisible = await checkoutPage.isGiftCheckboxVisible(page);
      await expect(isGiftCheckboxVisible, 'Gift checkbox is not visible!').to.be.true;
    });

    it('should check the gift checkbox and check that the gift message textarea is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGiftTextarea', baseContext);

      await checkoutPage.setGiftCheckBox(page);

      const isVisible = await checkoutPage.isGiftMessageTextareaVisible(page);
      await expect(isVisible, 'Gift message textarea is not visible!').to.be.true;
    });

    it('should set a gift message and continue to payment', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setGiftMessage', baseContext);

      await checkoutPage.setGiftMessage(page, 'This is your gift');

      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.checkPayment.moduleName);

      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('Check the gift message from BO > Orders > view order page', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await orderSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.ordersParentLink, dashboardPage.ordersLink);

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters1', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it('should view the first order in the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock1', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it('should click on \'Carriers\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayCarriersTab', baseContext);

      const isTabOpened = await orderPageTabListBlock.goToCarriersTab(page);
      await expect(isTabOpened).to.be.true;
    });

    it('should check the gift message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGiftMessage', baseContext);

      const giftMessageText = await orderPageTabListBlock.getGiftMessage(page);
      await expect(giftMessageText).to.be.equal('This is your gift');
    });
  });

  describe('POST-TEST: Disable \'Offer gift wrapping\'', async () => {
    it('should go to \'Shop Parameters > Order Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage2', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.shopParametersParentLink, dashboardPage.orderSettingsLink);

      const pageTitle = await orderSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
    });

    it('should disable \'Offer gift wrapping\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableGiftWrapping', baseContext);

      const result = await orderSettingsPage.setGiftOptions(page, false, 0, 'None', false);
      await expect(result).to.contains(orderSettingsPage.successfulUpdateMessage);
    });
  });
});
