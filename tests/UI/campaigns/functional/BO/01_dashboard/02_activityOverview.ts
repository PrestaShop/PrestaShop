// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import statsPage from '@pages/BO/stats';
import shoppingCartsPage from '@pages/BO/orders/shoppingCarts';

// Import FO pages
import {homePage} from '@pages/FO/home';
import {loginPage as foLoginPage} from '@pages/FO/login';
import productPage from '@pages/FO/product';
import {cartPage} from '@pages/FO/cart';
import checkoutPage from '@pages/FO/checkout';
import orderConfirmationPage from '@pages/FO/checkout/orderConfirmation';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import data
import Customers from '@data/demo/customers';
import PaymentMethods from '@data/demo/paymentMethods';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_dashboard_activityOverview';

describe('BO - Dashboard : Activity overview', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let activeShoppingCarts: number;

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

  it('should click on Online visitor link and check stats page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnlineVisitors', baseContext);

    await dashboardPage.clickOnOnlineVisitorsLink(page);

    const pageTitle = await statsPage.getPageTitle(page);
    expect(pageTitle).to.eq(statsPage.pageTitle);
  });

  it('should go back to dashboard page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard', baseContext);

    await statsPage.goToDashboardPage(page);

    const pageTitle = await dashboardPage.getPageTitle(page);
    expect(pageTitle).to.eq(dashboardPage.pageTitle);
  });

  it('should click on Active shopping carts link and check shopping carts page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickActiveShoppingCarts', baseContext);

    await dashboardPage.clickOnActiveShoppingCartsLink(page);

    const pageTitle = await shoppingCartsPage.getPageTitle(page);
    expect(pageTitle).to.eq(shoppingCartsPage.pageTitle);
  });

  it('should go back to dashboard page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard2', baseContext);

    await shoppingCartsPage.goToDashboardPage(page);

    const pageTitle = await dashboardPage.getPageTitle(page);
    expect(pageTitle).to.eq(dashboardPage.pageTitle);
  });

  it('should get the number of active shopping carts', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfActiveShoppingCarts', baseContext);

    activeShoppingCarts = await dashboardPage.getActiveShoppingCarts(page);
  });

  describe('Create new order and check Active shopping carts number', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      page = await dashboardPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO', baseContext);

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

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Go to home page
      await foLoginPage.goToHomePage(page);
      // Go to the first product page
      await homePage.goToProductPage(page, 1);
      // Add the product to the cart
      await productPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      // Close page and init page objects
      page = await orderConfirmationPage.closePage(browserContext, page, 0);
      await shoppingCartsPage.reloadPage(page);

      const pageTitle = await dashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(dashboardPage.pageTitle);
    });

    it('should check the number of active shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkActiveShoppingCarts', baseContext);

      const newActiveShoppingCarts = await dashboardPage.getActiveShoppingCarts(page);
      expect(newActiveShoppingCarts).to.eq(activeShoppingCarts + 1);
    });
  });
});
