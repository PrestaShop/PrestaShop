// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {quickViewModal} from '@pages/FO/classic/modal/quickView';
import {blockCartModal} from '@pages/FO/classic/modal/blockCart';

import {
  // Import data
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  // Import BO pages
  boDashboardPage,
  boOrdersPage,
  // Import FO pages
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicLoginPage,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_cashondelivery_configuration_placeOrderWithIt';

describe('Cash on delivery (COD) module - Place an order with it', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let orderReference: string;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('FO - Order a product with Cash on delivery payment', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await foClassicHomePage.goToFo(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should add the first product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicLoginPage.goToHomePage(page);

      // Add first product to cart by quick view
      await homePage.quickViewProduct(page, 1);
      await quickViewModal.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.eq(foClassicCartPage.pageTitle);
    });

    it('should proceed to checkout and check Step Address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddressStep', baseContext);

      await foClassicCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);

      const isStepPersonalInformationComplete = await foClassicCheckoutPage.isStepCompleted(
        page,
        foClassicCheckoutPage.personalInformationStepForm,
      );
      expect(isStepPersonalInformationComplete).to.eq(true);
    });

    it('should validate Step Address and go to Delivery Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryStep', baseContext);

      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete).to.eq(true);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      // Payment step - Choose payment step
      await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.cashOnDelivery.moduleName);

      // Check the confirmation message
      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);

      orderReference = await foClassicCheckoutOrderConfirmationPage.getOrderReferenceValue(page);
      expect(orderReference.length).to.be.gt(0);
    });
  });

  describe('BO - Check the last order', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should reset all filters and get number of orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it('should check the last order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLastOrder', baseContext);

      const rowOrderReference = await boOrdersPage.getTextColumn(page, 'reference', 1);
      expect(rowOrderReference).to.be.equal(orderReference);

      const rowOrderPayment = await boOrdersPage.getTextColumn(page, 'payment', 1);
      expect(rowOrderPayment).to.be.equal(dataPaymentMethods.cashOnDelivery.displayName);

      const rowOrderStatus = await boOrdersPage.getTextColumn(page, 'osname', 1);
      expect(rowOrderStatus).to.be.equal(dataOrderStatuses.awaitingCashOnDelivery.name);
    });
  });
});
