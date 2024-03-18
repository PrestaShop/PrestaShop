// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

// Import FO pages
import {checkoutPage} from '@pages/FO/classic/checkout';
import {orderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';
import {homePage as foHomePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import {orderDetailsPage} from '@pages/FO/classic/myAccount/orderDetails';
import {orderHistoryPage} from '@pages/FO/classic/myAccount/orderHistory';

// Import data
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import OrderData from '@data/faker/order';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// context
const baseContext: string = 'functional_FO_classic_userAccount_orderHistory_orderDetails_reorderFromOrderDetails';

/*
Pre-condition:
- Create order by default customer
Scenario:
- Go to userAccount > order history > order detail
- Click on the reorder link
- Proceed checkout
- Go back to the order list
- Check if the reorder is displayed
- Go to the order detail
- Check if the reorder contain the same product as the "original" order
 */
describe('FO - Account - Order details : Reorder from order detail', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const orderData: OrderData = new OrderData({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Go to order detail and proceed reorder', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      await foHomePage.goToFo(page);

      const isHomePage: boolean = await foHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foLoginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foLoginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await foHomePage.goToMyAccountPage(page);

      const pageTitle = await myAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(myAccountPage.pageTitle);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await orderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
    });

    it('should go to order details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

      await orderHistoryPage.goToDetailsPage(page);

      const pageTitle = await orderDetailsPage.getPageTitle(page);
      expect(pageTitle).to.equal(orderDetailsPage.pageTitle);
    });

    it('should click on reorder link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnReorderLink', baseContext);

      await orderDetailsPage.clickOnReorderLink(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      expect(isCheckoutPage, 'Browser is not in checkout Page').to.eq(true);
    });

    it('should validate Step Address and go to Delivery Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryStepForReorder', baseContext);

      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should validate Step Delivery and go to Payment Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStepForReorder', baseContext);

      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should Pay by bank wire and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmReorder', baseContext);

      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      const pageTitle = await orderConfirmationPage.getPageTitle(page);
      expect(pageTitle).to.equal(orderConfirmationPage.pageTitle);

      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('Go to new order detail and check content', async () => {
    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToAccountPage', baseContext);

      await foHomePage.goToMyAccountPage(page);

      const pageTitle = await myAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(myAccountPage.pageTitle);
    });

    it('should go back to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrderHistoryPage', baseContext);

      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await orderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
    });

    it('should go to order details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFoToOrderDetails', baseContext);

      await orderHistoryPage.goToDetailsPage(page);

      const pageTitle = await orderDetailsPage.getPageTitle(page);
      expect(pageTitle).to.equal(orderDetailsPage.pageTitle);
    });

    it('should check the ordered product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTheOrderedProduct', baseContext);

      const orderedProduct = await orderDetailsPage.getProductName(page);
      expect(orderedProduct).to.contain(Products.demo_1.name);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await orderConfirmationPage.logout(page);

      const isCustomerConnected = await orderConfirmationPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });
  });
});
