import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boPaymentPreferencesPage,
  type BrowserContext,
  dataCustomers,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_payment_preferences_currencyRestrictions';

describe('BO - Payment - Preferences : Configure currency restrictions', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Payment > Preferences\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.paymentParentLink,
      boDashboardPage.preferencesLink,
    );
    await boPaymentPreferencesPage.closeSfToolBar(page);

    const pageTitle = await boPaymentPreferencesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boPaymentPreferencesPage.pageTitle);
  });

  [
    {args: {action: 'uncheck', paymentModule: 'ps_wirepayment', exist: false}},
    {args: {action: 'check', paymentModule: 'ps_wirepayment', exist: true}},
    {args: {action: 'uncheck', paymentModule: 'ps_checkpayment', exist: false}},
    {args: {action: 'check', paymentModule: 'ps_checkpayment', exist: true}},
  ].forEach((test, index: number) => {
    it(`should ${test.args.action} the euro currency for '${test.args.paymentModule}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.action + test.args.paymentModule, baseContext);

      const result = await boPaymentPreferencesPage.setCurrencyRestriction(
        page,
        test.args.paymentModule,
        test.args.exist,
      );
      expect(result).to.contains(boPaymentPreferencesPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      // Click on view my shop
      page = await boPaymentPreferencesPage.viewMyShop(page);
      // Change language in FO
      await foClassicHomePage.changeLanguage(page, 'en');

      const pageTitle = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicHomePage.pageTitle);
    });

    it('should create the order and go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `createOrder${index}`, baseContext);

      // Go to the first product page
      await foClassicHomePage.goToProductPage(page, 1);
      // Add the product to the cart
      await foClassicProductPage.addProductToTheCart(page);
      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);

      // Checkout the order
      if (index === 0) {
        // Personal information step - Login
        await foClassicCheckoutPage.clickOnSignIn(page);
        await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      }

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it(`should check the '${test.args.paymentModule}' payment module`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkPaymentModule${index}`, baseContext);

      // Payment step - Choose payment step
      const isVisible = await foClassicCheckoutPage.isPaymentMethodExist(page, test.args.paymentModule);
      expect(isVisible).to.be.equal(test.args.exist);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

      // Go back to BO
      page = await foClassicCheckoutPage.closePage(browserContext, page, 0);

      const pageTitle = await boPaymentPreferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boPaymentPreferencesPage.pageTitle);
    });
  });
});
