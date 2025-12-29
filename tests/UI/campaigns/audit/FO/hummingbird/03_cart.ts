import {expect} from 'chai';
import {disableTheme, enableTheme} from '@commonTests/BO/design/hummingbird';
import testContext from '@utils/testContext';

import {
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  foHummingbirdCartPage,
  foHummingbirdCheckoutOrderConfirmationPage,
  foHummingbirdCheckoutPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'audit_FO_hummingbird_cart';

describe('Check FO pages in the checkout process', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Enable Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest_0`);

  describe('Check FO pages in the checkout process', async () => {
    const orderData: FakerOrder = new FakerOrder({
      customer: dataCustomers.johnDoe,
      products: [
        {
          product: dataProducts.demo_1,
          quantity: 1,
        },
      ],
      paymentMethod: dataPaymentMethods.wirePayment,
    });

    before(async function () {
      utilsPlaywright.setErrorsCaptured(true);

      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    beforeEach(async () => {
      utilsPlaywright.resetJsErrors();
    });

    it('should go to the home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);

      const jsErrors = utilsPlaywright.getJsErrors();
      expect(jsErrors.length).to.equals(0);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdLoginPage.pageTitle);

      const jsErrors = utilsPlaywright.getJsErrors();
      expect(jsErrors.length).to.equals(0);
    });

    it('should sign in with customer credentials', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'customerLogin', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, orderData.customer);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);

      const jsErrors = utilsPlaywright.getJsErrors();
      expect(jsErrors.length).to.equals(0);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHummingbirdLoginPage.goToHomePage(page);
      await foHummingbirdHomePage.goToProductPage(page, orderData.products[0].product.id);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());

      const jsErrors = utilsPlaywright.getJsErrors();
      expect(jsErrors.length).to.equals(0);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdProductPage.addProductToTheCart(page, orderData.products[0].quantity);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(orderData.products[0].quantity);

      const jsErrors = utilsPlaywright.getJsErrors();
      expect(jsErrors.length).to.equals(0);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const isStepAddressComplete = await foHummingbirdCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete).to.eq(true);

      const jsErrors = utilsPlaywright.getJsErrors();
      expect(jsErrors.length).to.equals(0);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      const isStepDeliveryComplete = await foHummingbirdCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete).to.eq(true);

      const jsErrors = utilsPlaywright.getJsErrors();
      expect(jsErrors.length).to.equals(0);
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await foHummingbirdCheckoutPage.choosePaymentAndOrder(page, orderData.paymentMethod.moduleName);

      const cardTitle = await foHummingbirdCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foHummingbirdCheckoutOrderConfirmationPage.orderConfirmationCardTitle);

      const jsErrors = utilsPlaywright.getJsErrors();
      expect(jsErrors.length).to.equals(0);
    });
  });

  // Post-condition : Disable Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest_0`);
});
