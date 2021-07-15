require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const testContext = require('@utils/testContext');
const helper = require('@utils/helpers');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const preferencesPage = require('@pages/BO/payment/preferences');

// Import FO pages
const productPage = require('@pages/FO/product');
const homePage = require('@pages/FO/home');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const foLoginPage = require('@pages/FO/login');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');

const baseContext = 'functional_BO_payment_preferences_carrierRestrictions';

let browserContext;
let page;

describe('BO - Payment - Preferences : Configure carrier restrictions and check FO', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Login into FO', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);
      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });
  });

  describe('Configure carrier restrictions', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Payment > Preferences\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.paymentParentLink,
        dashboardPage.preferencesLink,
      );

      await preferencesPage.closeSfToolBar(page);

      const pageTitle = await preferencesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(preferencesPage.pageTitle);
    });

    [
      {args: {action: 'uncheck', paymentModule: 'ps_wirepayment', exist: false}},
      {args: {action: 'check', paymentModule: 'ps_wirepayment', exist: true}},
      {args: {action: 'uncheck', paymentModule: 'ps_checkpayment', exist: false}},
      {args: {action: 'check', paymentModule: 'ps_checkpayment', exist: true}},
    ].forEach((test, index) => {
      it(`should ${test.args.action} free prestashop carrier for '${test.args.paymentModule}'`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          test.args.action + test.args.paymentModule,
          baseContext,
        );

        const result = await preferencesPage.setCarrierRestriction(
          page,
          0,
          test.args.paymentModule,
          test.args.exist,
        );

        await expect(result).to.contains(preferencesPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        // Click on view my shop
        page = await preferencesPage.viewMyShop(page);

        // Change language in FO
        await homePage.changeLanguage(page, 'en');

        const pageTitle = await homePage.getPageTitle(page);
        await expect(pageTitle).to.contains(homePage.pageTitle);
      });

      it('should add the first product to the cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

        // Go to the first product page
        await homePage.goToProductPage(page, 1);

        // Add the product to the cart
        await productPage.addProductToTheCart(page);

        // Proceed to checkout the shopping cart
        await cartPage.clickOnProceedToCheckout(page);

        const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
        await expect(isCheckoutPage).to.be.true;
      });

      it('should continue to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

        // Address step - Go to delivery step
        const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
        await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      });

      it('should continue to payment step and check the existence of payment method', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkPaymentMethod${index}`, baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
        await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

        // Payment step - Check payment method
        const isVisible = await checkoutPage.isPaymentMethodExist(page, test.args.paymentModule);
        await expect(isVisible).to.be.equal(test.args.exist);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

        // Close current tab
        page = await homePage.closePage(browserContext, page, 0);

        const pageTitle = await preferencesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(preferencesPage.pageTitle);
      });
    });
  });
});
