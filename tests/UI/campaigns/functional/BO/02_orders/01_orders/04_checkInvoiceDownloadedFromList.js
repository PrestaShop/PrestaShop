require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders/index');
const homePage = require('@pages/FO/home');

// Import FO pages
const foLoginPage = require('@pages/FO/login');
const foMyAccountPage = require('@pages/FO/myAccount');
const foOrderHistoryPage = require('@pages/FO/myAccount/orderHistory');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import data
const {Statuses} = require('@data/demo/orderStatuses');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_checkInvoiceDownloadedFromList';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let filePath;

/*
Create order in FO, choose payment method bank wire payment
Go to BO orders page and change order status to 'payment accepted'
Check invoice creation
Download invoice from list and check pdf text
Go to FO and check the new order status
 */
describe('BO - orders : Check invoice downloaded from list', async () => {
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

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFoToOrder', baseContext);

      await homePage.goToLoginPage(page);
      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFoToOrder', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
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
      await expect(notificationsNumber).to.be.equal(1);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
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

    it('should go to \'Orders > Orders\' page', async function () {
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

    it('should download invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoice', baseContext);

      filePath = await ordersPage.downloadInvoice(page, 1);
      const doesFileExist = await files.doesFileExist(filePath, 5000);
      await expect(doesFileExist).to.be.true;
    });

    it('should check invoice pdf text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceText', baseContext);

      // Get order information
      const orderInformation = await ordersPage.getOrderFromTable(page, 1);

      // Check Reference in pdf
      const referenceExist = await files.isTextInPDF(filePath, orderInformation.reference);
      await expect(referenceExist, `Reference '${orderInformation.reference}' does not exist in invoice`).to.be.true;

      // Check country name in delivery Address in pdf
      const deliveryExist = await files.isTextInPDF(filePath, orderInformation.delivery);
      await expect(deliveryExist, `Country name '${orderInformation.delivery}' does not exist in invoice`).to.be.true;

      // Check customer name in pdf
      const customerExist = await files.isTextInPDF(filePath, orderInformation.customer.slice(3));
      await expect(customerExist, `Country name '${orderInformation.customer}' does not exist in invoice`).to.be.true;

      // Check total paid in pdf
      const totalPaidExist = await files.isTextInPDF(filePath, orderInformation.totalPaid);
      await expect(totalPaidExist, `Total paid '${orderInformation.totalPaid}' does not exist in invoice`).to.be.true;
    });
  });

  describe('Check order status in FO ', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCheckStatus', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFoToCheckStatus', baseContext);

      await homePage.goToLoginPage(page);
      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFoToCheckStatus', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to orders history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await homePage.goToMyAccountPage(page);
      await foMyAccountPage.goToHistoryAndDetailsPage(page);
      const pageTitle = await foOrderHistoryPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open order history page').to.contains(foOrderHistoryPage.pageTitle);
    });

    it('should check last order status in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLastOrderStatus', baseContext);

      const orderStatusFO = await foOrderHistoryPage.getOrderStatus(page, 1);
      await expect(orderStatusFO, 'Order status is not correct').to.equal(Statuses.paymentAccepted.status);
    });
  });
});
