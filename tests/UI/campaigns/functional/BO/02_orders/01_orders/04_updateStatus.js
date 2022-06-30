require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders/index');
const homePage = require('@pages/FO/home');

// Import FO pages
const foLoginPage = require('@pages/FO/login');
const foMyAccountPage = require('@pages/FO/myAccount');
const foOrderHistoryPage = require('@pages/FO/myAccount/orderHistory');

// Import data
const {Statuses} = require('@data/demo/orderStatuses');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_updateStatus';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let filePath;
let orderId;

const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

/*
Pre-condition:
- Create order in FO, choose payment method bank wire payment
Scenario:
- Go to BO orders page and change order status to 'Canceled'
- Go to FO and check the new order status
- Go to BO orders page and change order status to 'Payment accepted'
- Check invoice creation
- Download invoice from list and check pdf text
- Go to FO and check the new order status and the invoice
- Go to BO orders page and change order status to 'Delivered'
- Go to FO and check the new order status
 */
describe('BO - orders : Update order status', async () => {
  // Pre-condition: Create order in FO
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Go to \'Orders > Orders\' page', async () => {
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

    it('should reset filter and get the last order ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      await ordersPage.resetFilter(page);

      orderId = await ordersPage.getTextColumn(page, 'id_order', 1);
      await expect(orderId).to.be.at.least(1);
    });
  });

  describe('Change orders status in BO then check it in FO', async () => {
    [
      {args: {orderStatus: Statuses.canceled}},
      {args: {orderStatus: Statuses.paymentAccepted}},
      {args: {orderStatus: Statuses.delivered}},

    ].forEach((test, index) => {
      describe(`Change orders status to '${test.args.orderStatus.status}' in BO`, async () => {
        it('should update order status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `updateOrderStatus${index}`, baseContext);

          const textResult = await ordersPage.setOrderStatus(page, 1, test.args.orderStatus);
          await expect(textResult).to.equal(ordersPage.successfulUpdateMessage);
        });

        it('should check that the status is updated successfully', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkStatusBO${index}`, baseContext);

          const orderStatus = await ordersPage.getTextColumn(page, 'osname', 1);
          await expect(orderStatus, 'Order status was not updated').to.equal(test.args.orderStatus.status);
        });

        if (test.args.orderStatus.status === Statuses.paymentAccepted.status) {
          it('should download invoice', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoice', baseContext);

            filePath = await ordersPage.downloadInvoice(page, 1);

            const doesFileExist = await files.doesFileExist(filePath, 5000);
            await expect(doesFileExist, 'The file is not existing!').to.be.true;
          });

          it('should check invoice pdf file', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceText', baseContext);

            // Get order information
            const orderInformation = await ordersPage.getOrderFromTable(page, 1);

            // Check Reference in pdf
            const referenceExist = await files.isTextInPDF(filePath, orderInformation.reference);
            await expect(referenceExist, `Reference '${orderInformation.reference}' does not exist in invoice`)
              .to.be.true;

            // Check country name in delivery Address in pdf
            const deliveryExist = await files.isTextInPDF(filePath, orderInformation.delivery);
            await expect(deliveryExist, `Country name '${orderInformation.delivery}' does not exist in invoice`)
              .to.be.true;

            // Check customer name in pdf
            const customerExist = await files.isTextInPDF(filePath, orderInformation.customer.slice(3));
            await expect(customerExist, `Customer name '${orderInformation.customer}' does not exist in invoice`)
              .to.be.true;

            // Check total paid in pdf
            const totalPaidExist = await files.isTextInPDF(filePath, orderInformation.totalPaid);
            await expect(totalPaidExist, `Total paid '${orderInformation.totalPaid}' does not exist in invoice`)
              .to.be.true;
          });
        }

        if (test.args.orderStatus.status === Statuses.delivered.status) {
          it('should download delivery slip', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'downloadDeliverySlip', baseContext);

            filePath = await ordersPage.downloadDeliverySlip(page, 1);

            const doesFileExist = await files.doesFileExist(filePath, 5000);
            await expect(doesFileExist).to.be.true;
          });

          it('should check delivery slip pdf file', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkDeliverySlip', baseContext);

            // Get delivery slip information
            const orderInformation = await ordersPage.getOrderFromTable(page, 1);

            // Check Reference in pdf
            const referenceExist = await files.isTextInPDF(filePath, orderInformation.reference);

            await expect(referenceExist, `Reference '${orderInformation.reference}' does not exist in delivery slip`)
              .to.be.true;

            // Check country name in delivery Address in pdf
            const deliveryExist = await files.isTextInPDF(filePath, orderInformation.delivery);
            await expect(deliveryExist, `Country name '${orderInformation.delivery}' does not exist in delivery slip`)
              .to.be.true;

            // Check customer name in pdf
            const customerExist = await files.isTextInPDF(filePath, orderInformation.customer.slice(3));
            await expect(customerExist, `Country name '${orderInformation.customer}' does not exist in delivery slip`)
              .to.be.true;

            // Check total paid in pdf
            const totalPaidExist = await files.isTextInPDF(filePath, orderInformation.totalPaid);
            await expect(totalPaidExist, `Total paid '${orderInformation.totalPaid}' does not exist in delivery slip`)
              .to.be.true;
          });
        }
      });

      describe('Check the order status in FO ', async () => {
        it('should go to FO page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToFoToCheckStatus${index}`, baseContext);

          page = await ordersPage.viewMyShop(page);
          await homePage.changeLanguage(page, 'en');

          const isHomePage = await homePage.isHomePage(page);
          await expect(isHomePage, 'Fail to open FO home page').to.be.true;
        });

        if (index === 0) {
          it('should go to login page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `goToLoginPage${index}`, baseContext);

            await homePage.goToLoginPage(page);
            const pageTitle = await foLoginPage.getPageTitle(page);
            await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
          });

          it('should sign in with default customer', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `sighInFoToCheckStatus${index}`, baseContext);

            await foLoginPage.customerLogin(page, DefaultCustomer);
            const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
            await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
          });
        }
        it('should go to orders history page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToOrderHistoryPage${index}`, baseContext);

          await homePage.goToMyAccountPage(page);
          await foMyAccountPage.goToHistoryAndDetailsPage(page);

          const pageTitle = await foOrderHistoryPage.getPageTitle(page);
          await expect(pageTitle, 'Fail to open order history page').to.contains(foOrderHistoryPage.pageTitle);
        });

        it('should check the last order status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkLastOrderStatus${index}`, baseContext);

          const orderStatusFO = await foOrderHistoryPage.getOrderStatus(page, 1);
          await expect(orderStatusFO, 'Order status is not correct').to.equal(test.args.orderStatus.status);
        });

        if (test.args.orderStatus.status === Statuses.paymentAccepted.status) {
          it('should check if the last invoice is visible', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkLastInvoice${index}`, baseContext);

            const isVisible = await foOrderHistoryPage.isInvoiceVisible(page, 1);
            await expect(isVisible, 'The invoice file is not existing!').to.be.true;
          });

          it('should check the order ID of the invoice', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkOrderID${index}`, baseContext);

            const orderID = await foOrderHistoryPage.getOrderIdFromInvoiceHref(page, 1);
            await expect(orderID, 'The invoice file attached is not correct!').to.contains(`id_order=${orderId}`);
          });
        }

        it('should close the shop page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `closeShop${index}`, baseContext);

          page = await foOrderHistoryPage.closePage(browserContext, page, 0);

          const pageTitle = await ordersPage.getPageTitle(page);
          await expect(pageTitle).to.contains(ordersPage.pageTitle);
        });
      });
    });
  });
});
