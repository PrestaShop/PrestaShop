require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import common tests
const loginCommon = require('@commonTests/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');
const orderMessagesPage = require('@pages/BO/customerService/orderMessages');

// Import FO pages
const foLoginPage = require('@pages/FO/login');
const foHomePage = require('@pages/FO/home');
const foOrderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
const foMyAccountPage = require('@pages/FO/myAccount');
const foOrderHistoryPage = require('@pages/FO/myAccount/orderHistory');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {PaymentMethods} = require('@data/demo/paymentMethods');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_messagesBlock';

let browserContext;
let page;

const messageData = {orderMessage: 'Delay', displayToCustomer: true, message: ''};
const secondMessageData = {orderMessage: 'Delay', displayToCustomer: false, message: 'test message visibility'};
let textMessage = '';

// Get today date format 'mm/dd/yyyy'
const today = new Date();
const mm = (`0${today.getMonth() + 1}`).slice(-2); // Current month
const dd = (`0${today.getDate()}`).slice(-2); // Current day
const yyyy = today.getFullYear(); // Current year
const todayDate = `${mm}/${dd}/${yyyy}`;

// New order by customer data
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

const messageToSendData = {product: '', message: 'Test customer message'};
/*
Pre-condition :
- Create order by default customer
Scenario :
- Go to view order page
- Send message and check message block( messages number, employee icon, date, sender)
- Check message in FO -> Issue https://github.com/PrestaShop/PrestaShop/issues/26532
- Uncheck display to customer and send message then check( messages number, message text, employee icon, date, sender)
- Check that the message is not visible in FO
- Send message from FO and check it on BO ( messages number, Message text, employee icon, date, sender)
- Check configure predefined message link
 */

describe('BO - Orders - View and edit order : Check messages block', async () => {
  // Pre-condition - Create order by default customer
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // 1 - Go to view order page
  describe('Go to view order page', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters1', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer1', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage1', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });
  });

  // 2 - Send message and check messages block
  describe('Send message and check messages block on BO', async () => {
    it('should send message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      const textMessage = await viewOrderPage.sendMessage(page, messageData);
      expect(textMessage).to.equal(viewOrderPage.commentSuccessfullMessage);
    });

    it('should check that the messages number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessageNumber', baseContext);

      const messagesNumber = await viewOrderPage.getMessagesNumber(page);
      await expect(messagesNumber).to.be.equal(1);
    });

    it('should check that the message is visible and get it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessage', baseContext);

      const isVisible = await viewOrderPage.isMessageVisible(page);
      await expect(isVisible).to.be.true;
    });

    it('should check the message sender and the date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSenderAndDate1', baseContext);

      textMessage = await viewOrderPage.getTextMessage(page);
      await expect(textMessage).to.contains(`Me ${todayDate}`);
    });

    it('should check the employee icon, the message date and the message sender', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmployeeIcon', baseContext);

      const isVisible = await viewOrderPage.isEmployeeIconVisible(page);
      await expect(isVisible).to.be.true;
    });
  });

  // 3 - Check message in FO
  describe('Check message in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCheckStatus', baseContext);

      // Click on view my shop
      page = await viewOrderPage.viewMyShop(page);

      // Change FO language
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFoToCheckStatus', baseContext);

      await foHomePage.goToLoginPage(page);
      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo1', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to orders history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage1', baseContext);

      await foHomePage.goToMyAccountPage(page);

      await foMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageTitle = await foOrderHistoryPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open order history page').to.contains(foOrderHistoryPage.pageTitle);
    });

    it('should go to the first order in the list and check order message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderMessageBlock1', baseContext);

      await foOrderHistoryPage.goToDetailsPage(page, 1);

      const isBoxMessagesVisible = await foOrderHistoryPage.isBoxMessagesSectionVisible(page);
      await expect(isBoxMessagesVisible, 'Box messages is not visible!').to.be.true;

      const isMessageRowVisible = await foOrderHistoryPage.isMessageRowVisible(page);
      await expect(isMessageRowVisible, 'Message is not visible!').to.be.true;
    });

    // Issue https://github.com/PrestaShop/PrestaShop/issues/26532
    /* it('should check the message text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderMessageBlock2', baseContext);

      const message = await foOrderHistoryPage.getMessageRow(page);
      await expect(message)
        .to.contain(todayDate)
        .and.to.contain(`${DefaultEmployee.firstName} ${DefaultEmployee.lastName}`)
        .and.to.contain(textMessage);
    }); */

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO2', baseContext);

      await foOrderConfirmationPage.logout(page);

      const isCustomerConnected = await foOrderConfirmationPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });

  // 4 - Send second message and uncheck display to customer
  describe('Uncheck display to customer and send message', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo1', baseContext);

      // Close page and init page objects
      page = await foOrderConfirmationPage.closePage(browserContext, page, 0);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it('should send second message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage1', baseContext);

      const textMessage = await viewOrderPage.sendMessage(page, secondMessageData);
      expect(textMessage).to.equal(viewOrderPage.commentSuccessfullMessage);
    });

    it('should check that messages number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessageNumber', baseContext);

      const messagesNumber = await viewOrderPage.getMessagesNumber(page);
      await expect(messagesNumber).to.be.equal(2);
    });

    it('should check that the second message is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessage', baseContext);

      const isVisible = await viewOrderPage.isMessageVisible(page, 2);
      await expect(isVisible).to.be.true;
    });

    it('should check that the employee icon is private', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmployeeIcon', baseContext);

      const isVisible = await viewOrderPage.isEmployeePrivateIconVisible(page, 2);
      await expect(isVisible).to.be.true;
    });

    it('should check the sender message and the date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSenderAndDate2', baseContext);

      textMessage = await viewOrderPage.getTextMessage(page, 2);
      await expect(textMessage, 'Sender or date is incorrect!').to.contains(`Me ${todayDate}`);
    });
  });

  // 5 - Check that the second message is not visible on FO
  describe('Check that the second message is not visible on FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCheckStatus', baseContext);

      // Click on view my shop
      page = await viewOrderPage.viewMyShop(page);

      // Change FO language
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFoToCheckStatus', baseContext);

      await foHomePage.goToLoginPage(page);
      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo2', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to orders history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage1', baseContext);

      await foHomePage.goToMyAccountPage(page);

      await foMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageTitle = await foOrderHistoryPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open order history page').to.contains(foOrderHistoryPage.pageTitle);
    });

    it('should go to the first order in the list and check that new message is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderMessageBlock3', baseContext);

      await foOrderHistoryPage.goToDetailsPage(page, 1);

      // New message is on the first row
      const message = await foOrderHistoryPage.getMessageRow(page, 1);
      await expect(message, 'Second message is not visible!').to.not.contain(secondMessageData.message);
    });
  });

  // 6 - Send message from FO and check it on BO
  describe('Send message from FO and check it on BO', async () => {
    it('should send message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage2', baseContext);

      const alertMessage = await foOrderHistoryPage.sendMessage(page, messageToSendData);
      expect(alertMessage, 'Success message is not displayed!').to.equal(foOrderHistoryPage.messageSuccessSent);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

      // Close page and init page objects
      page = await foOrderHistoryPage.closePage(browserContext, page, 0);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to go back to BO!').to.contains(viewOrderPage.pageTitle);
    });

    it('should reload the page and check that the messages number is equal to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessageNumber3', baseContext);

      await viewOrderPage.reloadPage(page);

      const messagesNumber = await viewOrderPage.getMessagesNumber(page);
      await expect(messagesNumber, 'Messages number is not correct!').to.be.equal(3);
    });

    it('should check that the third message is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThirdMessage', baseContext);

      const isVisible = await viewOrderPage.isMessageVisible(page, 3, 'customer');
      await expect(isVisible, 'Message is not visible!').to.be.true;
    });

    it('should check the message, the sender message and the date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSenderAndDate2', baseContext);

      textMessage = await viewOrderPage.getTextMessage(page, 3, 'customer');
      await expect(textMessage, 'Sender or date is not correct!')
        .to.contains(`${DefaultCustomer.firstName} ${DefaultCustomer.lastName} ${todayDate}`)
        .and.to.contains(messageToSendData.message);
    });
  });

  // 7 - Check configure predefined message link
  describe('Check \'Configure predefined messages\' link', async () => {
    it('should click on \'Configure predefined messages\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLink', baseContext);

      await viewOrderPage.clickOnConfigureMessageLink(page);

      const pageTitle = await orderMessagesPage.getPageTitle(page);
      await expect(pageTitle, 'Order messages page is not opened!').to.contains(orderMessagesPage.pageTitle);
    });
  });
});
