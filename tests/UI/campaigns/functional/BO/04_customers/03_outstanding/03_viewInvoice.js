require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');

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

// Import data
const {Statuses} = require('@data/demo/orderStatuses');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');

const baseContext = 'functional_BO_customers_outstanding_viewInvoice';

let browserContext;
let page;

// Variable used for the last order reference created
let orderReference;

// Variable used for the last outstanding ID created
let outstandingId;

// Variable used for the temporary invoice file
let filePath;

// New order by customer data
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

/*
Pre-condition:
- Enable B2B
- Create order in FO
- Update order status to payment accepted
Scenario:
- View invoice from the outstanding page
Post-condition:
- Disable B2B
*/

describe('BO - Customers - Outstanding : View invoice', async () => {
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

  // Pre-condition: Update order status to payment accepted
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

    it('should reset filter and get the last reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterOrder', baseContext);

      await ordersPage.resetFilter(page);

      orderReference = await ordersPage.getTextColumn(page, 'reference', 1);
      await expect(orderReference).to.not.equal(null);
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


  // 1 - View invoice from the outstanding
  describe('View invoice from the outstanding page', async () => {
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

      outstandingId = await outstandingPage.getTextColumn(page, 'id_invoice', 1);
      await expect(outstandingId).to.be.at.least(1);
    });

    it('should view the Invoice and check the order reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewInvoice', baseContext);

      filePath = await outstandingPage.viewInvoice(page, 'invoice', 1);

      const doesFileExist = await files.doesFileExist(filePath, 5000);
      await expect(doesFileExist, 'The file is not existing!').to.be.true;
    });

    it('should check reference in the invoice pdf file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceReference', baseContext);

      // Check Reference in pdf
      const referenceOrder = await files.isTextInPDF(filePath, orderReference);
      await expect(referenceOrder).to.be.true;
    });
  });

  // Post-Condition : Disable B2B
  disableB2BTest(baseContext);
});
