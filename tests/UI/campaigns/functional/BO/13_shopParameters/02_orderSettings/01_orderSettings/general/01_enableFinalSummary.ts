// Import utils
import testContext from '@utils/testContext';

import {
  boDashboardPage,
  boLoginPage,
  boOrderSettingsPage,
  type BrowserContext,
  dataCustomers,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_shopParameters_orderSettings_orderSettings_general_enableFinalSummary';

/*
Enable/Disable final summary
Go to FO and check final summary in payment step of checkout
 */
describe('BO - Shop Parameters - Order Settings : Enable/Disable final summary', async () => {
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

  it('should go to \'Shop Parameters > Order Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.orderSettingsLink,
    );
    await boOrderSettingsPage.closeSfToolBar(page);

    const pageTitle = await boOrderSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'enable', exist: true}},
    {args: {action: 'disable', exist: false}},
  ];

  tests.forEach((test, index: number) => {
    it(`should ${test.args.action} final summary`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}FinalSummary`, baseContext);

      const result = await boOrderSettingsPage.setFinalSummaryStatus(page, test.args.exist);
      expect(result).to.contains(boOrderSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}AndViewMyShop`, baseContext);

      // Click on view my shop
      page = await boOrderSettingsPage.viewMyShop(page);
      // Change FO language
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

      // Go to the first product page
      await foClassicHomePage.goToProductPage(page, 1);

      // Add the product to the cart
      await foClassicProductPage.addProductToTheCart(page);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(index + 1);
    });

    it('should proceed to checkout and login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `proceedToCheckout${index}`, baseContext);
      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);

      // Checkout the order
      if (index === 0) {
        // Personal information step - Login
        await foClassicCheckoutPage.clickOnSignIn(page);
        await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      }
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToPaymentStep${index}`, baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should check the final summary after checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkFinalSummary${index}`, baseContext);

      // Check the final summary existence in payment step
      const isVisible = await foClassicCheckoutOrderConfirmationPage.isFinalSummaryVisible(page);
      expect(isVisible).to.be.equal(test.args.exist);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}CheckAndBackToBO`, baseContext);

      page = await foClassicCheckoutOrderConfirmationPage.closePage(browserContext, page, 0);

      const pageTitle = await boOrderSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
    });
  });
});
