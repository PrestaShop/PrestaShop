require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_payment_preferences_countryRestrictions';

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const preferencesPage = require('@pages/BO/payment/preferences');
const productPage = require('@pages/FO/product');
const homePage = require('@pages/FO/home');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');

// Import data
const {DefaultAccount} = require('@data/demo/customer');

let browserContext;
let page;

const countryID = 74;

describe('Configure country restrictions', async () => {
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

  const tests = [
    {args: {action: 'uncheck', paymentModule: 'ps_wirepayment', exist: false}},
    {args: {action: 'check', paymentModule: 'ps_wirepayment', exist: true}},
    {args: {action: 'uncheck', paymentModule: 'ps_checkpayment', exist: false}},
    {args: {action: 'check', paymentModule: 'ps_checkpayment', exist: true}},
  ];

  tests.forEach((test, index) => {
    it(`should ${test.args.action} the France country for '${test.args.paymentModule}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.action + test.args.paymentModule, baseContext);

      const result = await preferencesPage.setCountryRestriction(
        page,
        countryID,
        test.args.paymentModule,
        test.args.exist,
      );

      await expect(result).to.contains(preferencesPage.successfulUpdateMessage);
    });

    it('should go to FO and add the first product to the cart', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `check_${test.args.paymentModule}_${test.args.exist}`,
        baseContext,
      );

      // Click on view my shop
      page = await preferencesPage.viewMyShop(page);

      // Change language in FO
      await homePage.changeLanguage(page, 'en');

      // Go to the first product page
      await homePage.goToProductPage(page, 1);

      // Add the product to the cart
      await productPage.addProductToTheCart(page);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      await expect(isCheckoutPage).to.be.true;
    });

    // Personal information step - Login
    it('should login and go to address step', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `loginToFO${index}`,
        baseContext,
      );

      if (index === 0) {
        // Personal information step - Login
        await checkoutPage.clickOnSignIn(page);
        const isStepLoginComplete = await checkoutPage.customerLogin(page, DefaultAccount);
        await expect(isStepLoginComplete, 'Step Personal information is not complete').to.be.true;
      }
    });

    it('should continue to delivery step', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `goToDeliveryStep${index}`,
        baseContext,
      );

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should continue to payment step and check the existence of payment method', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `goToPaymentStep${index}`,
        baseContext,
      );

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

      // Payment step - Check payment method
      const isVisible = await checkoutPage.isPaymentMethodExist(page, test.args.paymentModule);
      await expect(isVisible).to.be.equal(test.args.exist);

      // Go back to BO
      page = await checkoutPage.closePage(browserContext, page, 0);
    });
  });
});
