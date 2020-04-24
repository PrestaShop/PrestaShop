require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {Statuses} = require('@data/demo/orderStatuses');
const {Invoices} = require('@data/demo/invoices');
const files = require('@utils/files');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const InvoicesPage = require('@pages/BO/orders/invoices/index');
const OrdersPage = require('@pages/BO/orders/index');
const ViewOrderPage = require('@pages/BO/orders/view');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_invoices_generateInvoiceByDate';

let browser;
let page;
const today = new Date();
// Create a future date that there is no invoices (yyy-mm-dd)
today.setFullYear(today.getFullYear() + 1);
const futureDate = today.toISOString().slice(0, 10);

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    invoicesPage: new InvoicesPage(page),
    ordersPage: new OrdersPage(page),
    viewOrderPage: new ViewOrderPage(page),
  };
};

// Generate PDF file by date
describe('Generate PDF file by date', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
    /* Delete the generated invoice */
    await files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${Invoices.moreThanAnInvoice.fileName}`);
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
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.ordersLink,
        );
        await this.pageObjects.boBasePage.closeSfToolBar();
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

  describe('Generate invoice by date', async () => {
    it('should go to invoices page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPage', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.invoicesLink,
      );
      const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
    });

    it('should generate PDF file by date and check the file existence', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGeneratedInvoicesPdfFile', baseContext);
      await this.pageObjects.invoicesPage.generatePDFByDate();
      const exist = await files.doesFileExist(Invoices.moreThanAnInvoice.fileName);
      await expect(exist).to.be.true;
    });

    it('should check the error message when there is no invoice in the entered date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessageNonexistentInvoice', baseContext);
      await this.pageObjects.invoicesPage.generatePDFByDate(futureDate, futureDate);
      const textMessage = await this.pageObjects.invoicesPage.getTextContent(
        this.pageObjects.invoicesPage.alertTextBlock,
      );
      await expect(textMessage).to.equal(this.pageObjects.invoicesPage.errorMessageWhenGenerateFileByDate);
    });
  });
});
