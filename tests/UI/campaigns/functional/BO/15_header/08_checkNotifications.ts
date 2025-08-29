import testContext from '@utils/testContext';
import {expect} from 'chai';
import {faker} from '@faker-js/faker';

import {createOrderByCustomerTest, createOrderByGuestTest} from '@commonTests/FO/classic/order';

import {
  boCustomerServicePage,
  boCustomersViewPage,
  boDashboardPage,
  boLoginPage,
  boOrderMessagesCreatePage,
  boOrdersPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  FakerOrder,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyOrderDetailsPage,
  foClassicMyOrderHistoryPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const orderByCustomerData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });
  const messageSend: string = faker.lorem.sentence().substring(0, 35).trim();
  const messageOption: string = `${dataProducts.demo_1.name} (Size: ${dataProducts.demo_1.attributes[0].values[0]} `
    + `- Color: ${dataProducts.demo_1.attributes[1].values[0]})`;
  const customerData: FakerCustomer = new FakerCustomer({password: ''});
  const addressData: FakerAddress = new FakerAddress({country: 'France'});
  // New order by guest data
  const orderByGuestData: FakerOrder = new FakerOrder({
    customer: customerData,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    deliveryAddress: addressData,
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // PRE-condition : Create order by default customer
  createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Send message from FO then check the notification in BO', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should check notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber', baseContext);

      const number = await boDashboardPage.getAllNotificationsNumber(page);
      expect(number).to.be.at.least(0);
    });

    it('should click on notifications icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNotificationsLink', baseContext);

      const isNotificationsVisible = await boDashboardPage.clickOnNotificationsLink(page);
      expect(isNotificationsVisible).to.eq(true);

      await boDashboardPage.clickOnNotificationsTab(page, 'customers');
      await boDashboardPage.clickOnNotificationsTab(page, 'messages');
    });

    it('should refresh the page and check the notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'refreshPageAndCheckNotifNumber', baseContext);

      await boDashboardPage.reloadPage(page);

      const number = await boDashboardPage.getAllNotificationsNumber(page);
      expect(number).to.equal(0);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMySHop', baseContext);

      page = await boOrdersPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);
      await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await foClassicMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyOrderHistoryPage.pageTitle);
    });

    it('Go to order details and send message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      await foClassicMyOrderHistoryPage.goToDetailsPage(page);

      const successMessageText = await foClassicMyOrderDetailsPage.addAMessage(page, messageOption, messageSend);
      expect(successMessageText).to.equal(foClassicMyOrderDetailsPage.successMessageText);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      // Close tab and init other page objects with new current tab
      page = await foClassicHomePage.closePage(browserContext, page, 0);

      await boDashboardPage.reloadPage(page);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should check notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber2', baseContext);

      const number = await boDashboardPage.getAllNotificationsNumber(page);
      expect(number).to.equal(1);
    });

    it('should click on notifications icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNotificationsLink2', baseContext);

      const isNotificationsVisible = await boDashboardPage.clickOnNotificationsLink(page);
      expect(isNotificationsVisible).to.eq(true);
    });

    it('should click on Messages tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickMessagesTab', baseContext);

      await boDashboardPage.clickOnNotificationsTab(page, 'messages');

      const notificationsNumber = await boDashboardPage.getNotificationsNumberInTab(page, 'customer_messages');
      expect(notificationsNumber).to.equal(1);
    });

    it('should click on the first notification and check that the messages table is opened', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnFirstMessageNotification', baseContext);

      await boDashboardPage.clickOnNotification(page, 'messages');

      const pageTitle = await boCustomerServicePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);
    });
  });

  // PRE_condition: Create order by guest
  createOrderByGuestTest(orderByGuestData, baseContext);

  describe('Check customers and orders notifications in BO', async () => {
    it('should click on notifications icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNotificationsLink3', baseContext);

      await boOrderMessagesCreatePage.goToDashboardPage(page);

      const isNotificationsVisible = await boDashboardPage.clickOnNotificationsLink(page);
      expect(isNotificationsVisible).to.eq(true);
    });

    it('should check notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber3', baseContext);

      const number = await boDashboardPage.getAllNotificationsNumber(page);
      expect(number).to.equal(2);
    });

    it('should click on the first notification in orders tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersNotification', baseContext);

      await boDashboardPage.clickOnNotification(page, 'orders');

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it('should click on customers tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnCustomersTab', baseContext);

      await boOrderMessagesCreatePage.goToDashboardPage(page);
      await boDashboardPage.clickOnNotificationsLink(page);
      await boDashboardPage.clickOnNotificationsTab(page, 'customers');

      const notificationsNumber = await boDashboardPage.getNotificationsNumberInTab(page, 'customers');
      expect(notificationsNumber).to.equal(1);
    });

    it('should click on the first notification and check that the customers table is opened', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnFirstNotificationCustomers', baseContext);

      await boDashboardPage.clickOnNotification(page, 'customers');

      const customerName: string = `${customerData.firstName[0]}. ${customerData.lastName}`;

      const pageTitle = await boCustomersViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersViewPage.pageTitle(customerName));
    });
  });
});
