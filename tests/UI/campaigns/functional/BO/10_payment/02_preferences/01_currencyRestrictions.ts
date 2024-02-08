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
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {homePage} from '@pages/FO/classic/home';
import productPage from '@pages/FO/classic/product';

// Importing data
import Customers from '@data/demo/customers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_payment_preferences_currencyRestrictions';

describe('BO - Payment - Preferences : Configure currency restrictions', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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
    expect(pageTitle).to.contains(preferencesPage.pageTitle);
  });

  [
    {args: {action: 'uncheck', paymentModule: 'ps_wirepayment', exist: false}},
    {args: {action: 'check', paymentModule: 'ps_wirepayment', exist: true}},
    {args: {action: 'uncheck', paymentModule: 'ps_checkpayment', exist: false}},
    {args: {action: 'check', paymentModule: 'ps_checkpayment', exist: true}},
  ].forEach((test, index: number) => {
    it(`should ${test.args.action} the euro currency for '${test.args.paymentModule}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.action + test.args.paymentModule, baseContext);

      const result = await preferencesPage.setCurrencyRestriction(
        page,
        test.args.paymentModule,
        test.args.exist,
      );
      expect(result).to.contains(preferencesPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      // Click on view my shop
      page = await preferencesPage.viewMyShop(page);
      // Change language in FO
      await homePage.changeLanguage(page, 'en');

      const pageTitle = await homePage.getPageTitle(page);
      expect(pageTitle).to.contains(homePage.pageTitle);
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
        await checkoutPage.customerLogin(page, Customers.johnDoe);
      }

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it(`should check the '${test.args.paymentModule}' payment module`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkPaymentModule${index}`, baseContext);

      // Payment step - Choose payment step
      const isVisible = await checkoutPage.isPaymentMethodExist(page, test.args.paymentModule);
      expect(isVisible).to.be.equal(test.args.exist);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

      // Go back to BO
      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await preferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(preferencesPage.pageTitle);
    });
  });
});
