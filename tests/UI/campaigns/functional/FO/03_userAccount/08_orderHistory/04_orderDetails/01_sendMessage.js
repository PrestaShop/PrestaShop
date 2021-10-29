require('module-alias/register');

const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {Statuses} = require('@data/demo/orderStatuses');
const faker = require('faker');

// Import data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');
const {Products} = require('@data/demo/products');

const messageSend = faker.lorem.sentence().substring(0, 35).trim();

// Importing pages
// FO
const foHomePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const foMyAccountPage = require('@pages/FO/myAccount');
const foOrderHistoryPage = require('@pages/FO/myAccount/orderHistory');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
const orderDetails = require('@pages/FO/myAccount/orderDetails');

// BO
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders/index');
const customerServicePage = require('@pages/BO/customerService/customerService');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_userAccount_orderHistory_orderDetails_sendMessage';
const messageOption = `${Products.demo_1.name} (Size: ${Products.demo_1.combination.size[0]} `
  + `- Color: ${Products.demo_1.combination.color[0]})`;

let browserContext;
let page;

/*
Go to FO and connect to an account
Go to account page and Order history and details
Select an order with an invoice
Click on details
Select a product and write a message
Click to send
Go to BO, Customers service, customers service, the message is displayed
 */

describe('FO - Account : Send a message with an ordered product', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create order in FO', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrder', baseContext);

      await foHomePage.goToFo(page);
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFoToOrder', baseContext);

      await foHomePage.goToLoginPage(page);
      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFoToOrder', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should create an order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrder', baseContext);

      // Go to home page
      await foLoginPage.goToHomePage(page);

      // Go to the first product page
      await foHomePage.goToProductPage(page, 1);

      // Add the created product to the cart
      await productPage.addProductToTheCart(page);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);

      // Check the confirmation message
      await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighOutFO', baseContext);

      await orderConfirmationPage.logout(page);
      const isCustomerConnected = await orderConfirmationPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });

  describe('Check invoice file in BO', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to the orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it(`should update order status to '${Statuses.paymentAccepted.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await ordersPage.setOrderStatus(page, 1, Statuses.paymentAccepted);
      await expect(textResult).to.equal(ordersPage.successfulUpdateMessage);

      const orderStatus = await ordersPage.getTextColumn(page, 'osname', 1);
      await expect(orderStatus, 'Order status was not updated').to.equal(Statuses.paymentAccepted.status);
    });

    it('disconnect from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });
  });

  describe('Send a message on order history', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      await foHomePage.goToFo(page);
      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foLoginPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foLoginPage.pageTitle);
    });

    it('Should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
      const isCustomerConnected = await foMyAccountPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await foHomePage.goToMyAccountPage(page);
      await foMyAccountPage.goToHistoryAndDetailsPage(page);
      const pageHeaderTitle = await foOrderHistoryPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foOrderHistoryPage.pageTitle);
    });

    it('Go to order details ', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

      await foOrderHistoryPage.goToDetailsPage(page);

      const successMessageText = await orderDetails.addAMessage(page, messageOption, messageSend);
      await expect(successMessageText).to.equal(orderDetails.successMessageText);
    });
  });

  describe('Check message in BO', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to customer service page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.customerServiceLink,
      );

      const pageTitle = await customerServicePage.getPageTitle(page);
      await expect(pageTitle).to.contains(customerServicePage.pageTitle);
    });

    it('should check customer name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerName', baseContext);

      const email = await customerServicePage.getTextColumn(page, 1, 'customer');
      await expect(email).to.contain(`${DefaultCustomer.firstName} ${DefaultCustomer.lastName}`);
    });

    it('should check customer email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerEmail', baseContext);

      const email = await customerServicePage.getTextColumn(page, 1, 'a!email');
      await expect(email).to.contain(DefaultCustomer.email);
    });

    it('should check message type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessageType', baseContext);

      const email = await customerServicePage.getTextColumn(page, 1, 'cl!id_contact');
      await expect(email).to.contain('--');
    });

    it('should check message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessage', baseContext);

      const email = await customerServicePage.getTextColumn(page, 1, 'message');
      await expect(email).to.contain(messageSend);
    });

    it('should delete the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

      const textResult = await customerServicePage.deleteMessage(page, 1);
      await expect(textResult).to.contains(customerServicePage.successfulDeleteMessage);
    });
  });
});
