require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');

// Import common test
const {enableB2BTest, disableB2BTest} = require('@commonTests/BO/shopParameters/enableDisableB2B');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import test context
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const outstandingPage = require('@pages/BO/customers/outstanding');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderBasePage = require('@pages/BO/orders/view/viewOrderBasePage');

// Import data
const {Statuses} = require('@data/demo/orderStatuses');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');

const baseContext = 'functional_BO_customers_outstanding_viewOrder';

let browserContext;
let page;

// last order ID created
let orderId;

// last order reference created
let orderReference;

// last outstanding ID created
let outstandingId;

const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

describe('BO - Customers - Outstanding : View order from the outstanding page', async () => {
  // Pre-Condition : Enable B2B
  enableB2BTest(baseContext);

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

  describe('PRE-TEST: Update order status to payment accepted', async () => {
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

    [
      {args: {columnName: 'id_order', result: 1}},
      {args: {columnName: 'reference', result: null}},
    ].forEach((test) => {
      it(`should reset filter and get the last ${test.args.columnName}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterOrder', baseContext);

        await ordersPage.resetFilter(page);

        const orderColumn = await ordersPage.getTextColumn(page, `${test.args.columnName}`, 1);

        if (test.args.columnName === 'id_order') {
          orderId = orderColumn;
          await expect(orderColumn).to.be.at.least(test.args.result);
        } else {
          orderReference = orderColumn;
          await expect(orderColumn).to.not.equal(test.args.result);
        }
      });
    });

    it('should update order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await ordersPage.setOrderStatus(page, 1, Statuses.paymentAccepted);
      await expect(textResult).to.equal(ordersPage.successfulUpdateMessage);
    });

    it('should check that the status is updated successfully', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusBO', baseContext);

      const orderStatus = await ordersPage.getTextColumn(page, 'osname', 1);
      await expect(orderStatus, 'Order status was not updated').to.equal(Statuses.paymentAccepted.status);
    });
  });

  describe('BO - Customers - Outstanding : View order', async () => {
    it('should go to \'Customers > Outstanding\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOutstandingPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.outstandingLink,
      );

      await outstandingPage.closeSfToolBar(page);

      const pageTitle = await outstandingPage.getPageTitle(page);
      await expect(pageTitle).to.contains(outstandingPage.pageTitle);
    });

    it('should reset filter and get the last outstanding ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterOutstanding', baseContext);

      await outstandingPage.resetFilter(page);

      outstandingId = await outstandingPage.getTextColumn(page, 'id_order', 1);
      await expect(outstandingId).to.be.at.least(1);
    });

    it('should click on view Order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrder', baseContext);

      await outstandingPage.viewOrder(page, 'actions', 1);

      const outstandingOrderId = await viewOrderBasePage.getOrderID(page);
      const outstandingOrderReference = await viewOrderBasePage.getOrderReference(page);

      [
        {args: {columnName: outstandingOrderId, result: orderId}},
        {args: {columnName: outstandingOrderReference, result: orderReference}},
      ].forEach((test) => {
        expect(test.args.columnName).to.be.equal(test.args.result);
      });
    });
  });

  // Post-Condition : Disable B2B
  disableB2BTest(baseContext);
});
