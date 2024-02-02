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
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import productPage from '@pages/FO/classic/product';

// Import data
import Customers from '@data/demo/customers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_payment_preferences_carrierRestrictions';

describe('BO - Payment - Preferences : Configure carrier restrictions and check FO', async () => {
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

  describe('Login into FO', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
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
      expect(pageTitle).to.contains(preferencesPage.pageTitle);
    });

    [
      {args: {action: 'uncheck', paymentModule: 'ps_wirepayment', exist: false}},
      {args: {action: 'check', paymentModule: 'ps_wirepayment', exist: true}},
      {args: {action: 'uncheck', paymentModule: 'ps_checkpayment', exist: false}},
      {args: {action: 'check', paymentModule: 'ps_checkpayment', exist: true}},
    ].forEach((test, index: number) => {
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

      it('should add the first product to the cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

        // Go to the first product page
        await homePage.goToProductPage(page, 1);
        // Add the product to the cart
        await productPage.addProductToTheCart(page);
        // Proceed to checkout the shopping cart
        await cartPage.clickOnProceedToCheckout(page);

        const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
        expect(isCheckoutPage).to.eq(true);
      });

      it('should continue to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

        // Address step - Go to delivery step
        const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should continue to payment step and check the existence of payment method', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkPaymentMethod${index}`, baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);

        // Payment step - Check payment method
        const isVisible = await checkoutPage.isPaymentMethodExist(page, test.args.paymentModule);
        expect(isVisible).to.be.equal(test.args.exist);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

        // Close current tab
        page = await homePage.closePage(browserContext, page, 0);

        const pageTitle = await preferencesPage.getPageTitle(page);
        expect(pageTitle).to.contains(preferencesPage.pageTitle);
      });
    });
  });
});
