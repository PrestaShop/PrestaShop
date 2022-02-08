require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');

// Import common tests
const loginCommon = require('@commonTests/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders/index');

// Import FO pages
const homePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const foMyAccountPage = require('@pages/FO/myAccount');
const foOrderHistoryPage = require('@pages/FO/myAccount/orderHistory');

// Import data
const {Statuses} = require('@data/demo/orderStatuses');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');

// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_checkDeliverySlipDownloadedFromList';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let filePath;

const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

/*
Pre-condition:
- Create order in FO with bank wire payment
Scenario:
- Go to BO orders page and change order status to 'shipped'
- Check delivery slip creation
- Download delivery slip from list and check pdf text
- Go to FO and check the new order status
 */
describe('BO - Orders : Check delivery slip downloaded from list', async () => {
  // Pre-condition: Create order on FO
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Check delivery slip file on BO', async () => {
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

    it(`should update order status to '${Statuses.shipped.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await ordersPage.setOrderStatus(page, 1, Statuses.shipped);
      await expect(textResult).to.equal(ordersPage.successfulUpdateMessage);

      const orderStatus = await ordersPage.getTextColumn(page, 'osname', 1);
      await expect(orderStatus, 'Order status was not updated').to.equal(Statuses.shipped.status);
    });

    it('should download delivery slip', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadDeliverySlip', baseContext);

      filePath = await ordersPage.downloadDeliverySlip(page, 1);
      const doesFileExist = await files.doesFileExist(filePath, 5000);
      await expect(doesFileExist).to.be.true;
    });

    it('should check delivery slip pdf text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliverySlipText', baseContext);

      // Get order information
      const orderInformation = await ordersPage.getOrderFromTable(page, 1);

      // Check Reference in pdf
      const referenceExist = await files.isTextInPDF(filePath, orderInformation.reference);

      await expect(
        referenceExist,
        `Reference '${orderInformation.reference}' does not exist in delivery slip`,
      ).to.be.true;

      // Check country name in delivery Address in pdf
      const deliveryExist = await files.isTextInPDF(filePath, orderInformation.delivery);

      await expect(
        deliveryExist,
        `Country name '${orderInformation.delivery}' does not exist in delivery slip`,
      ).to.be.true;

      // Check customer name in pdf
      const customerExist = await files.isTextInPDF(filePath, orderInformation.customer.slice(3));

      await expect(
        customerExist,
        `Country name '${orderInformation.customer}' does not exist in delivery slip`,
      ).to.be.true;

      // Check total paid in pdf
      const totalPaidExist = await files.isTextInPDF(filePath, orderInformation.totalPaid);

      await expect(
        totalPaidExist,
        `Total paid '${orderInformation.totalPaid}' does not exist in delivery slip`,
      ).to.be.true;
    });
  });

  describe('Check order status on FO ', async () => {
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

    it('should check last order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLastOrderStatus', baseContext);

      const orderStatusFO = await foOrderHistoryPage.getOrderStatus(page, 1);
      await expect(orderStatusFO, 'Order status is not correct').to.equal(Statuses.shipped.status);
    });
  });
});
