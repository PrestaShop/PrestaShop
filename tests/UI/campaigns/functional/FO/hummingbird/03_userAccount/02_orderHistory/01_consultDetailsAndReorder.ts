// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import date from '@utils/date';

// Import common tests
import {createAddressTest} from '@commonTests/BO/customers/address';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import createAccountTest from '@commonTests/FO/hummingbird/account';
import {createOrderByCustomerTest} from '@commonTests/FO/hummingbird/order';
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import homePage from '@pages/FO/hummingbird/home';
import loginPage from '@pages/FO/hummingbird/login';
import orderHistoryPage from '@pages/FO/hummingbird/myAccount/orderHistory';
import myAccountPage from '@pages/FO/hummingbird/myAccount';
import orderDetailsPage from '@pages/FO/hummingbird/myAccount/orderDetails';
import checkoutPage from '@pages/FO/hummingbird/checkout';
import orderConfirmationPage from '@pages/FO/hummingbird/checkout/orderConfirmation';

// Import data
import OrderData from '@data/faker/order';
import Products from '@data/demo/products';

import {
  // Import data
  dataOrderStatuses,
  dataPaymentMethods,
  FakerAddress,
  FakerCustomer,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_userAccount_orderHistory_consultDetailsAndReorder';

/*
Pre-condition:
_ Install the theme hummingbird
- Create customer
- Create address
Scenario:
- Go to orders history page
- Check that number of orders is 0
- Create order
- Check that number of orders is 1
- Click on details link
- Click on reorder link and reorder
Post-condition
- Delete customer
- Delete the theme hummingbird
 */

describe('FO - Account - Order history : Consult details and reorder', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const customerData: FakerCustomer = new FakerCustomer();
  const addressData: FakerAddress = new FakerAddress({
    email: customerData.email,
    country: 'France',
  });
  const orderData: OrderData = new OrderData({
    customer: customerData,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });
  const today: string = date.getDateFormat('mm/dd/yyyy');

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_1`);

  // Pre-condition: Create new account
  createAccountTest(customerData, `${baseContext}_enableNewProduct`);

  // Pre-condition: Create new address
  createAddressTest(addressData, `${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Check that no order has been placed in order history', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await homePage.goToLoginPage(page);

      const pageHeaderTitle = await loginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(loginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await loginPage.customerLogin(page, customerData);

      const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await homePage.goToMyAccountPage(page);
      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await orderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
    });

    it('should check number of orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfOrders1', baseContext);

      const numberOfOrders = await orderHistoryPage.getNumberOfOrders(page);
      expect(numberOfOrders).to.equal(0);
    });
  });

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_3`);

  describe('Check that one order has been placed in order history', async () => {
    it('should reload the FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'reloadPage', baseContext);

      await orderHistoryPage.reloadPage(page);

      const pageHeaderTitle = await orderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
    });

    it('should check the number of orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfOrders2', baseContext);

      const numberOfOrders = await orderHistoryPage.getNumberOfOrders(page);
      expect(numberOfOrders).to.equal(1);
    });

    it('should check the order information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderInformation', baseContext);

      const result = await orderHistoryPage.getOrderHistoryDetails(page);
      await Promise.all([
        expect(result.reference).not.null,
        expect(result.date).to.equal(today),
        expect(result.price).to.equal(`€${Products.demo_1.finalPrice}`),
        expect(result.paymentType).to.equal(dataPaymentMethods.wirePayment.displayName),
        expect(result.status).to.equal(dataOrderStatuses.awaitingBankWire.name),
        expect(result.invoice).to.equal('-'),
      ]);
    });

    it('should go to order details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

      await orderHistoryPage.goToDetailsPage(page);

      const pageTitle = await orderDetailsPage.getPageTitle(page);
      expect(pageTitle).to.equal(orderDetailsPage.pageTitle);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage2', baseContext);

      await myAccountPage.clickOnOrderHistoryAndDetailsLeftMenu(page);

      const pageHeaderTitle = await orderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
    });

    it('should reorder the last order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'reorderLastOrder', baseContext);

      await orderHistoryPage.clickOnReorderLink(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      expect(isCheckoutPage, 'Browser is not in checkout Page').to.eq(true);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

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
      await checkoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  // Post-condition : Delete customer
  deleteCustomerTest(customerData, `${baseContext}_postText_1`);

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_2`);
});
