// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import preferencesPage from '@pages/BO/payment/preferences';
// Import FO pages
import {cartPage} from '@pages/FO/cart';
import checkoutPage from '@pages/FO/checkout';
import {homePage} from '@pages/FO/home';
import productPage from '@pages/FO/product';

// Import data
import Customers from '@data/demo/customers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_payment_preferences_countryRestrictions';

describe('BO - Payment - Preferences : Configure country restrictions', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const countryID: number = 74;

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
  ].forEach((test, index: number) => {
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

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      // Click on view my shop
      page = await preferencesPage.viewMyShop(page);
      // Change language in FO
      await homePage.changeLanguage(page, 'en');

      const pageTitle = await homePage.getPageTitle(page);
      await expect(pageTitle).to.contains(homePage.pageTitle);
    });

    it('should add the first product to the cart and checkout', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `addFirstProductToCart${test.args.paymentModule}_${test.args.exist}`,
        baseContext,
      );

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
      await testContext.addContextItem(this, 'testIdentifier', `loginToFO${index}`, baseContext);

      if (index === 0) {
        // Personal information step - Login
        await checkoutPage.clickOnSignIn(page);

        const isStepLoginComplete = await checkoutPage.customerLogin(page, Customers.johnDoe);
        await expect(isStepLoginComplete, 'Step Personal information is not complete').to.be.true;
      }
    });

    it('should continue to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should continue to payment step and check the existence of payment method', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToPaymentStep${index}`, baseContext);

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
