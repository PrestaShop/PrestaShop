import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boPaymentPreferencesPage,
  type BrowserContext,
  dataCountries,
  dataCustomers,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_payment_preferences_countryRestrictions';

describe('BO - Payment - Preferences : Configure country restrictions', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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
    it(`should ${test.args.action} the France country for '${test.args.paymentModule}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.action + test.args.paymentModule, baseContext);

      const result = await boPaymentPreferencesPage.setCountryRestriction(
        page,
        dataCountries.france.id,
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
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const pageTitle = await foHummingbirdHomePage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdHomePage.pageTitle);
    });

    it('should add the first product to the cart and checkout', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `addFirstProductToCart${test.args.paymentModule}_${test.args.exist}`,
        baseContext,
      );

      // Go to the first product page
      await foHummingbirdHomePage.goToProductPage(page, 1);
      // Add the product to the cart
      await foHummingbirdProductPage.addProductToTheCart(page);
      // Proceed to checkout the shopping cart
      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    // Personal information step - Login
    it('should login and go to address step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `loginToFO${index}`, baseContext);

      if (index === 0) {
        // Personal information step - Login
        await foHummingbirdCheckoutPage.clickOnSignIn(page);

        const isStepLoginComplete = await foHummingbirdCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
        expect(isStepLoginComplete, 'Step Personal information is not complete').to.eq(true);
      }
    });

    it('should continue to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foHummingbirdCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should continue to payment step and check the existence of payment method', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToPaymentStep${index}`, baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foHummingbirdCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);

      // Payment step - Check payment method
      const isVisible = await foHummingbirdCheckoutPage.isPaymentMethodExist(page, test.args.paymentModule);
      expect(isVisible).to.be.equal(test.args.exist);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

      // Close current tab
      page = await foHummingbirdHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boPaymentPreferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boPaymentPreferencesPage.pageTitle);
    });
  });
});
