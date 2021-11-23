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

// Importing data
const {DefaultCustomer} = require('@data/demo/customer');

const baseContext = 'functional_BO_payment_preferences_currencyRestrictions';

let browserContext;
let page;

describe('BO - Payment - Preferences : Configure currency restrictions', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

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
    it(`should ${test.args.action} the euro currency for '${test.args.paymentModule}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.action + test.args.paymentModule, baseContext);

      const result = await preferencesPage.setCurrencyRestriction(
        page,
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

    it('should create the order and go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `createOrder${index}`, baseContext);

      // Go to the first product page
      await homePage.goToProductPage(page, 1);

      // Add the product to the cart
      await productPage.addProductToTheCart(page);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Checkout the order
      if (index === 0) {
        // Personal information step - Login
        await checkoutPage.clickOnSignIn(page);
        await checkoutPage.customerLogin(page, DefaultCustomer);
      }

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
    });

    it(`should check the '${test.args.paymentModule}' payment module`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkPaymentModule${index}`, baseContext);

      // Payment step - Choose payment step
      const isVisible = await checkoutPage.isPaymentMethodExist(page, test.args.paymentModule);
      await expect(isVisible).to.be.equal(test.args.exist);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

      // Go back to BO
      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await preferencesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(preferencesPage.pageTitle);
    });
  });
});
