require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {Statuses} = require('@data/demo/orderStatuses');
const files = require('@utils/files');
// Importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const InvoicesPage = require('@pages/BO/orders/invoices/index');
const OrdersPage = require('@pages/BO/orders/index');
const ViewOrderPage = require('@pages/BO/orders/view');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_invoices_generateInvoiceByStatus';


let browserContext;
let page;
let filePath;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    invoicesPage: new InvoicesPage(page),
    ordersPage: new OrdersPage(page),
    viewOrderPage: new ViewOrderPage(page),
  };
};

// 1 : Generate PDF file by status
describe('Generate PDF file by status', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO
  loginCommon.loginBO();

  describe('Create invoice', async () => {
    const tests = [
      {args: {orderRow: 1, status: Statuses.shipped.status}},
      {args: {orderRow: 2, status: Statuses.paymentAccepted.status}},
    ];
    tests.forEach((orderToEdit, index) => {
      it('should go to the orders page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage${index + 1}`, baseContext);

        await this.pageObjects.dashboardPage.goToSubMenu(
          this.pageObjects.dashboardPage.ordersParentLink,
          this.pageObjects.dashboardPage.ordersLink,
        );

        await this.pageObjects.ordersPage.closeSfToolBar();

        const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
      });

      it(`should go to the order page number '${orderToEdit.args.orderRow}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrderPage${index + 1}`, baseContext);

        await this.pageObjects.ordersPage.goToOrder(orderToEdit.args.orderRow);
        const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
      });

      it(`should change the order status to '${orderToEdit.args.status}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateOrderStatus${index + 1}`, baseContext);

        const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(orderToEdit.args.status);
        await expect(result).to.equal(orderToEdit.args.status);
      });
    });
  });

  describe('Generate invoice by status', async () => {
    it('should go to invoices page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPage', baseContext);

      await this.pageObjects.viewOrderPage.goToSubMenu(
        this.pageObjects.viewOrderPage.ordersParentLink,
        this.pageObjects.viewOrderPage.invoicesLink,
      );

      const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
    });

    it('should check the error message when we don\'t select a status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoSelectedStatusMessageError', baseContext);

      // Generate PDF
      const textMessage = await this.pageObjects.invoicesPage.generatePDFByStatusAndFail();

      await expect(textMessage).to.equal(this.pageObjects.invoicesPage.errorMessageWhenNotSelectStatus);
    });

    it('should check the error message when there is no invoice in the status selected', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoInvoiceMessageError', baseContext);

      // Choose one status
      await this.pageObjects.invoicesPage.chooseStatus(Statuses.canceled.status);

      // Generate PDF
      const textMessage = await this.pageObjects.invoicesPage.generatePDFByStatusAndFail();

      await expect(textMessage).to.equal(this.pageObjects.invoicesPage.errorMessageWhenGenerateFileByStatus);
    });

    it('should choose the statuses, generate the invoice and check the file existence', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceExistence', baseContext);

      // Choose 2 status
      await this.pageObjects.invoicesPage.chooseStatus(Statuses.paymentAccepted.status);
      await this.pageObjects.invoicesPage.chooseStatus(Statuses.shipped.status);

      // Generate PDF
      filePath = await this.pageObjects.invoicesPage.generatePDFByStatusAndDownload();

      // Check that file exist
      const exist = await files.doesFileExist(filePath);
      await expect(exist).to.be.true;
    });
  });
});
