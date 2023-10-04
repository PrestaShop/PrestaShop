// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import date from '@utils/date';

// Import common tests
import {createAddressTest} from '@commonTests/BO/customers/address';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createAccountTest} from '@commonTests/FO/account';
import {createOrderByCustomerTest} from '@commonTests/FO/order';

// Import FO pages
import {homePage, homePage as foHomePage} from '@pages/FO/home';
import {loginPage as foLoginPage} from '@pages/FO/login';
import {orderHistoryPage} from '@pages/FO/myAccount/orderHistory';
import {myAccountPage} from '@pages/FO/myAccount';

// Import data
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';
import OrderData from '@data/faker/order';
import {OrderHistory} from '@data/types/order';
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import productPage from "@pages/FO/product";
import {cartPage} from "@pages/FO/cart";
import checkoutPage from "@pages/FO/checkout";
import orderConfirmationPage from "@pages/FO/checkout/orderConfirmation";

const baseContext: string = 'functional_FO_classic_userAccount_orderHistory_consultOrderList';

/*
Pre-condition:
- Create customer
- Create address
Scenario:
- Go to orders history page
- Check that number of orders is 0
- Create order
- Check that number of orders is 1
- Check the link of My account page
- Check the link of Home page
Post-condition
- Delete customer
 */

describe('FO - Account - Order history : Consult order list', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const customerData: CustomerData = new CustomerData();
  const addressData: AddressData = new AddressData({
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
    paymentMethod: PaymentMethods.wirePayment,
  });
  const today: string = date.getDateFormat('mm/dd/yyyy');

  // Pre-condition: Create new account
  createAccountTest(customerData, `${baseContext}_enableNewProduct`);

  // Pre-condition: Create new address
  createAddressTest(addressData, `${baseContext}_preTest_2`);

  describe('Consult order list', async () => {
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

        await foHomePage.goToFo(page);

        const isHomePage = await foHomePage.isHomePage(page);
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

        await foLoginPage.customerLogin(page, customerData);

        const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
        expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
      });

      it('should go to order history page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

        await foHomePage.goToMyAccountPage(page);
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

    describe('Create order on FO', async () => {
      it('should add product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

        await foLoginPage.goToHomePage(page);
        await homePage.goToProductPage(page, orderData.products[0].product.id);
        await productPage.addProductToTheCart(page, orderData.products[0].quantity);

        const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(orderData.products[0].quantity);
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
        await checkoutPage.choosePaymentAndOrder(page, orderData.paymentMethod.moduleName);

        // Check the confirmation message
        const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
      });
    });

    describe('Check that one order has been placed in order history', async () => {
      it('should go to order history page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

        await foLoginPage.goToHomePage(page);
        await foHomePage.goToMyAccountPage(page);
        await myAccountPage.goToHistoryAndDetailsPage(page);

        const pageHeaderTitle = await orderHistoryPage.getPageTitle(page);
        expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
      });

      it('should check the number of orders', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfOrders2', baseContext);

        const numberOfOrders: number = await orderHistoryPage.getNumberOfOrders(page);
        expect(numberOfOrders).to.equal(1);
      });

      it('should check the order information', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrderInformation', baseContext);

        const result: OrderHistory = await orderHistoryPage.getOrderHistoryDetails(page);
        await Promise.all([
          expect(result.reference).not.null,
          expect(result.date).to.equal(today),
          expect(result.price).to.equal(`€${Products.demo_1.finalPrice}`),
          expect(result.paymentType).to.equal(PaymentMethods.wirePayment.displayName),
          expect(result.status).to.equal(OrderStatuses.awaitingBankWire.name),
          expect(result.invoice).to.equal('-'),
        ]);
      });

      it('should click on \'Back to you account\' link', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'backToYourAccount', baseContext);

        await orderHistoryPage.clickOnBackToYourAccountLink(page);

        const pageTitle: string = await myAccountPage.getPageTitle(page);
        expect(pageTitle).to.equal(myAccountPage.pageTitle);
      });

      it('should go back to order history page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage2', baseContext);

        await myAccountPage.goToHistoryAndDetailsPage(page);

        const pageHeaderTitle: string = await orderHistoryPage.getPageTitle(page);
        expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
      });

      it('should click on \'Home\' link', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnHomeLink', baseContext);

        await orderHistoryPage.clickOnHomeLink(page);

        const pageTitle: string = await foHomePage.getPageTitle(page);
        expect(pageTitle).to.equal(foHomePage.pageTitle);
      });
    });
  });

  // Post-condition : Delete customer
  deleteCustomerTest(customerData, `${baseContext}_postText`);
});
