require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');
const files = require('@utils/files');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders/index');
const orderPageTabListBlock = require('@pages/BO/orders/view/tabListBlock');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');
const {PaymentMethods} = require('@data/demo/paymentMethods');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_changeStatus';

let browserContext;
let page;
let filePath;

// New order by customer data
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

/*
Pre-condition :
- Create order in FO
Scenario :
- View first order page
- Select same status and check result
- Select 'Payment accepted' status and check result
- Select 'Shipped' status and check result
 */
describe('BO - Orders - View and edit order : Change order status', async () => {
  // Pre-Condition : Create order from FO
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Change the order status and check result', async () => {
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

    it('should filter the Orders table by the default customer and check the result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrder', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it('should check that \'Update status button\' is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatUpdateStatusButtonIsDisabled1', baseContext);

      const isButtonDisabled = await orderPageTabListBlock.isUpdateStatusButtonDisabled(page);
      await expect(isButtonDisabled, 'Update status button is not disabled!').to.be.true;
    });

    it('should check that \'Partial refund\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPartialRefundButton1', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isPartialRefundButtonVisible(page);
      await expect(isButtonVisible, 'Partial refund button is visible!').to.be.false;
    });

    it('should check that \'View invoice\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewInvoiceButton1', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isViewInvoiceButtonVisible(page);
      await expect(isButtonVisible, 'Partial refund button is visible!').to.be.false;
    });

    it('should select the same status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectSameStatus', baseContext);

      const actualStatus = await orderPageTabListBlock.getOrderStatus(page);
      expect(actualStatus).to.be.equal(Statuses.awaitingBankWire.status);

      await orderPageTabListBlock.selectOrderStatus(page, actualStatus);
    });

    it('should check that \'Update status button\' still disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatUpdateStatusButtonIsDisabled2', baseContext);

      const isButtonDisabled = await orderPageTabListBlock.isUpdateStatusButtonDisabled(page);
      await expect(isButtonDisabled, 'Update status button is not disabled!').to.be.true;
    });

    it('should check that \'Partial refund\' button still not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPartialRefundButton2', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isPartialRefundButtonVisible(page);
      await expect(isButtonVisible, 'Partial refund button is visible!').to.be.false;
    });

    it('should check that \'View invoice\' button still not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewInvoiceButton2', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isViewInvoiceButtonVisible(page);
      await expect(isButtonVisible, 'Partial refund button is visible!').to.be.false;
    });

    it('should check that \'View delivery slip\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewInvoiceButton3', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isDeliverySlipButtonVisible(page);
      await expect(isButtonVisible, 'Partial refund button is visible!').to.be.false;
    });

    it(`should select the status '${Statuses.canceled.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectCanceledStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, Statuses.canceled.status);
      await expect(result).to.equal(Statuses.canceled.status);
    });

    it('should check that the statuses number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusesNumber1', baseContext);

      const statusesNumber = await orderPageTabListBlock.getStatusesNumber(page);
      await expect(statusesNumber).to.be.equal(2);
    });

    it(`should check that the actual status is '${Statuses.canceled.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkActualStatus', baseContext);

      const actualStatus = await orderPageTabListBlock.getOrderStatus(page);
      expect(actualStatus).to.be.equal(Statuses.canceled.status);
    });

    it('should check that \'Partial refund\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPartialRefundButton3', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isPartialRefundButtonVisible(page);
      await expect(isButtonVisible, 'Partial refund button is visible!').to.be.false;
    });

    it('should check that \'View invoice\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewInvoiceButton4', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isViewInvoiceButtonVisible(page);
      await expect(isButtonVisible, 'Partial refund button is visible!').to.be.false;
    });

    it('should check that \'View delivery slip\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewInvoiceButton5', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isDeliverySlipButtonVisible(page);
      await expect(isButtonVisible, 'Partial refund button is visible!').to.be.false;
    });

    it(`should select the status '${Statuses.paymentAccepted.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectPaymentAcceptedStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, Statuses.paymentAccepted.status);
      await expect(result).to.equal(Statuses.paymentAccepted.status);
    });

    it('should check that the statuses number is equal to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusesNumber2', baseContext);

      const statusesNumber = await orderPageTabListBlock.getStatusesNumber(page);
      await expect(statusesNumber).to.be.equal(3);
    });

    it(`should check that the actual status is '${Statuses.paymentAccepted.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkActualStatus2', baseContext);

      const actualStatus = await orderPageTabListBlock.getOrderStatus(page);
      expect(actualStatus).to.be.equal(Statuses.paymentAccepted.status);
    });

    it('should check that \'Partial refund\' button is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPartialRefundButton4', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isPartialRefundButtonVisible(page);
      await expect(isButtonVisible, 'Partial refund button is not visible!').to.be.true;
    });

    it('should check that \'View invoice\' button is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewInvoiceButton6', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isViewInvoiceButtonVisible(page);
      await expect(isButtonVisible, 'Partial refund button is not visible!').to.be.true;
    });

    it('should check that \'View delivery slip\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewInvoiceButton7', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isDeliverySlipButtonVisible(page);
      await expect(isButtonVisible, 'Partial refund button is visible!').to.be.false;
    });

    it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice', baseContext);

      filePath = await orderPageTabListBlock.viewInvoice(page);

      const doesFileExist = await files.doesFileExist(filePath, 5000);
      await expect(doesFileExist, 'File is not downloaded!').to.be.true;
    });

    it(`should select the status '${Statuses.shipped.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectShippedStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    it('should check that the statuses number is equal to 4', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusesNumber3', baseContext);

      const statusesNumber = await orderPageTabListBlock.getStatusesNumber(page);
      await expect(statusesNumber).to.be.equal(4);
    });

    it(`should check that the actual status is '${Statuses.shipped.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkActualStatus3', baseContext);

      const actualStatus = await orderPageTabListBlock.getOrderStatus(page);
      expect(actualStatus).to.be.equal(Statuses.shipped.status);
    });

    it('should click on \'View delivery slip\' button and check that the file is downloaded', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice2', baseContext);

      filePath = await orderPageTabListBlock.viewDeliverySlip(page);

      const doesFileExist = await files.doesFileExist(filePath, 5000);
      await expect(doesFileExist, 'File is not downloaded!').to.be.true;
    });
  });
});
