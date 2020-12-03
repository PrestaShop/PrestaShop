require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {Statuses} = require('@data/demo/orderStatuses');
const files = require('@utils/files');

// Importing pages
const dashboardPage = require('@pages/BO/dashboard');
const invoicesPage = require('@pages/BO/orders/invoices/index');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view');

// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_invoices_generateInvoiceByDate';


let browserContext;
let page;
let filePath;

const today = new Date();
// Create a future date that there is no invoices (yyy-mm-dd)
today.setFullYear(today.getFullYear() + 1);
const futureDate = today.toISOString().slice(0, 10);


// Generate PDF file by date
describe('Generate PDF file by date', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  describe('Create invoice', async () => {
    const tests = [
      {args: {orderRow: 1, status: Statuses.shipped.status}},
      {args: {orderRow: 2, status: Statuses.paymentAccepted.status}},
    ];

    tests.forEach((orderToEdit, index) => {
      it('should go to the orders page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage${index + 1}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        await ordersPage.closeSfToolBar(page);

        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should reset all filters and get number of orders', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFiltersFirst${index + 1}`, baseContext);

        const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
        await expect(numberOfOrders).to.be.above(0);
      });

      it(`should go to the order page number '${orderToEdit.args.orderRow}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrderPage${index + 1}`, baseContext);

        // View order
        await ordersPage.goToOrder(page, orderToEdit.args.orderRow);

        const pageTitle = await viewOrderPage.getPageTitle(page);
        await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
      });

      it(`should change the order status to '${orderToEdit.args.status}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateOrderStatus${index + 1}`, baseContext);

        const result = await viewOrderPage.modifyOrderStatus(page, orderToEdit.args.status);
        await expect(result).to.equal(orderToEdit.args.status);
      });
    });
  });

  describe('Generate invoice by date', async () => {
    it('should go to invoices page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPage', baseContext);

      await viewOrderPage.goToSubMenu(
        page,
        viewOrderPage.ordersParentLink,
        viewOrderPage.invoicesLink,
      );

      const pageTitle = await invoicesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(invoicesPage.pageTitle);
    });

    it('should generate PDF file by date and check the file existence', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGeneratedInvoicesPdfFile', baseContext);

      // Generate PDF
      filePath = await invoicesPage.generatePDFByDateAndDownload(page);

      const exist = await files.doesFileExist(filePath);
      await expect(exist, 'File does not exist').to.be.true;
    });

    it('should check the error message when there is no invoice in the entered date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessageNonexistentInvoice', baseContext);

      // Generate PDF
      const textMessage = await invoicesPage.generatePDFByDateAndFail(page, futureDate, futureDate);

      await expect(textMessage).to.equal(invoicesPage.errorMessageWhenGenerateFileByDate);
    });
  });
});
