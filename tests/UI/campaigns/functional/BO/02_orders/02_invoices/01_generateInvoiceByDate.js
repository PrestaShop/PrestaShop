require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const invoicesPage = require('@pages/BO/orders/invoices/index');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view');

// Import data
const {Statuses} = require('@data/demo/orderStatuses');

// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_invoices_generateInvoiceByDate';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let filePath;

const today = new Date();

// Get today date format (yyyy-mm-dd)
const todayDate = today.toISOString().slice(0, 10);

// Create a future date that there is no invoices (yyyy-mm-dd)
today.setFullYear(today.getFullYear() + 1);
const futureDate = today.toISOString().slice(0, 10);


// Generate PDF file by date
describe('BO - Orders - Invoices : Generate PDF file by date', async () => {
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

  describe('Create an invoice by changing the first order status', async () => {
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

    it('should reset all filters and get number of orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await viewOrderPage.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });
  });

  describe('Generate invoice by date', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
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
      filePath = await invoicesPage.generatePDFByDateAndDownload(page, todayDate, todayDate);

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
