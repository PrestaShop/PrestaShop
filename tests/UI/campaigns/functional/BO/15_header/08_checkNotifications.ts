// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// import common tests
import {createOrderByCustomerTest, createOrderByGuestTest} from '@commonTests/FO/order';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import {homePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import {orderHistoryPage} from '@pages/FO/classic/myAccount/orderHistory';
import {orderDetailsPage} from '@pages/FO/classic/myAccount/orderDetails';
import customerServicePage from '@pages/BO/customerService/customerService';
import viewOrderMessagePage from '@pages/BO/customerService/orderMessages/add';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';
import viewCustomerPage from '@pages/BO/customers/view';

// import data
import Customers from '@data/demo/customers';
import Products from '@data/demo/products';
import OrderData from '@data/faker/order';
import PaymentMethods from '@data/demo/paymentMethods';
import CustomerData from '@data/faker/customer';
import AddressData from '@data/faker/address';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {faker} from '@faker-js/faker';

const baseContext: string = 'functional_BO_header_checkNotifications';

/*
Pre-condition:
- Create order by customer
Scenario:
- Check notifications number in BO
- Open the notification
- Refresh the page and check that there is 0 notification
- Send message from the Order details page
- Check notification message in BO
- Click on the notification and check the message page
Pre-condition:
- Create order by guest in FO
Scenario:
- Go to BO and check notifications in Customers and orders tab
- Click on each notification and check order and customer page
 */
describe('BO - Header : Check notifications', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const orderByCustomerData: OrderData = new OrderData({
    customer: Customers.johnDoe,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });
  const messageSend: string = faker.lorem.sentence().substring(0, 35).trim();
  const messageOption: string = `${Products.demo_1.name} (Size: ${Products.demo_1.attributes[0].values[0]} `
    + `- Color: ${Products.demo_1.attributes[1].values[0]})`;
  const customerData: CustomerData = new CustomerData({password: ''});
  const addressData: AddressData = new AddressData({country: 'France'});
  // New order by guest data
  const orderByGuestData: OrderData = new OrderData({
    customer: customerData,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    deliveryAddress: addressData,
    paymentMethod: PaymentMethods.wirePayment,
  });

  // PRE-condition : Create order by default customer
  createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Send message from FO then check the notification in BO', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should check notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber', baseContext);

      const number = await dashboardPage.getAllNotificationsNumber(page);
      expect(number).to.be.at.least(0);
    });

    it('should click on notifications icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNotificationsLink', baseContext);

      const isNotificationsVisible = await dashboardPage.clickOnNotificationsLink(page);
      expect(isNotificationsVisible).to.eq(true);

      await dashboardPage.clickOnNotificationsTab(page, 'customers');
      await dashboardPage.clickOnNotificationsTab(page, 'messages');
    });

    it('should refresh the page and check the notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'refreshPageAndCheckNotifNumber', baseContext);

      await dashboardPage.reloadPage(page);

      const number = await dashboardPage.getAllNotificationsNumber(page);
      expect(number).to.equal(0);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMySHop', baseContext);

      page = await ordersPage.viewMyShop(page);
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
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await homePage.goToMyAccountPage(page);
      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await orderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
    });

    it('Go to order details and send message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      await orderHistoryPage.goToDetailsPage(page);

      const successMessageText = await orderDetailsPage.addAMessage(page, messageOption, messageSend);
      expect(successMessageText).to.equal(orderDetailsPage.successMessageText);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      // Close tab and init other page objects with new current tab
      page = await homePage.closePage(browserContext, page, 0);

      await dashboardPage.reloadPage(page);

      const pageTitle = await dashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(dashboardPage.pageTitle);
    });

    it('should check notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber2', baseContext);

      const number = await dashboardPage.getAllNotificationsNumber(page);
      expect(number).to.equal(1);
    });

    it('should click on notifications icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNotificationsLink2', baseContext);

      const isNotificationsVisible = await dashboardPage.clickOnNotificationsLink(page);
      expect(isNotificationsVisible).to.eq(true);
    });

    it('should click on Messages tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickMessagesTab', baseContext);

      await dashboardPage.clickOnNotificationsTab(page, 'messages');

      const notificationsNumber = await dashboardPage.getNotificationsNumberInTab(page, 'customer_messages');
      expect(notificationsNumber).to.equal(1);
    });

    it('should click on the first notification and check that the messages table is opened', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnFirstMessageNotification', baseContext);

      await dashboardPage.clickOnNotification(page, 'messages');

      const pageTitle = await customerServicePage.getPageTitle(page);
      expect(pageTitle).to.contains(customerServicePage.pageTitle);
    });
  });

  // PRE_condition: Create order by guest
  createOrderByGuestTest(orderByGuestData, baseContext);

  describe('Check customers and orders notifications in BO', async () => {
    it('should click on notifications icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNotificationsLink3', baseContext);

      await viewOrderMessagePage.goToDashboardPage(page);

      const isNotificationsVisible = await dashboardPage.clickOnNotificationsLink(page);
      expect(isNotificationsVisible).to.eq(true);
    });

    it('should check notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber3', baseContext);

      const number = await dashboardPage.getAllNotificationsNumber(page);
      expect(number).to.equal(2);
    });

    it('should click on the first notification in orders tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersNotification', baseContext);

      await dashboardPage.clickOnNotification(page, 'orders');

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it('should click on customers tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnCustomersTab', baseContext);

      await viewOrderMessagePage.goToDashboardPage(page);
      await dashboardPage.clickOnNotificationsLink(page);
      await dashboardPage.clickOnNotificationsTab(page, 'customers');

      const notificationsNumber = await dashboardPage.getNotificationsNumberInTab(page, 'customers');
      expect(notificationsNumber).to.equal(1);
    });

    it('should click on the first notification and check that the customers table is opened', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnFirstNotificationCustomers', baseContext);

      await dashboardPage.clickOnNotification(page, 'customers');

      const customerName: string = `${customerData.firstName[0]}. ${customerData.lastName}`;

      const pageTitle = await viewCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewCustomerPage.pageTitle(customerName));
    });
  });
});
