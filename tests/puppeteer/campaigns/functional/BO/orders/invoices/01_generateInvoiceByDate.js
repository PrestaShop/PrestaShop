require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {Statuses} = require('@data/demo/orders');
const {Invoices} = require('@data/demo/invoices');
const files = require('@utils/files');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const InvoicesPage = require('@pages/BO/orders/invoices/index');
const OrdersPage = require('@pages/BO/orders/index');
const ViewOrderPage = require('@pages/BO/orders/view');

let browser;
let page;
const today = new Date();
// Create a start and end date that there is no invoices (yyy-mm-dd)
const day = today.getDate() + 100;
const month = today.getMonth() + 101;
const year = today.getFullYear();
const dateFrom = `${(year + 1).toString()}-${month.toString().substring(1)}-${day.toString().substring(1)}`;
const dateTo = `${(year + 1).toString()}-${(month + 1).toString()}-${day.toString()}`;

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
    tests.forEach((orderToEdit) => {
      it('should go to the orders page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.ordersLink,
        );
        const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
      });

      it(`should go to the order page number '${orderToEdit.args.orderRow}'`, async function () {
        await this.pageObjects.ordersPage.goToOrder(orderToEdit.args.orderRow);
        const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
      });

      it(`should change the order status to '${orderToEdit.args.status}' and check it`, async function () {
        const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(orderToEdit.args.status);
        await expect(result).to.be.true;
      });
    });
  });

  describe('Generate invoice by date', async () => {
    it('should go to invoices page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.invoicesLink,
      );
      const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
    });

    it('should generate PDF file by date and check the file existence', async function () {
      await this.pageObjects.invoicesPage.generatePDFByDate();
      const exist = await files.checkFileExistence(Invoices.moreThanAnInvoice.fileName);
      await expect(exist).to.be.true;
    });

    it('should check the error message when there is no invoice in the entered date', async function () {
      await this.pageObjects.invoicesPage.generatePDFByDate(dateFrom, dateTo);
      const textMessage = await this.pageObjects.invoicesPage.getTextContent(
        this.pageObjects.invoicesPage.alertTextBlock,
      );
      await expect(textMessage).to.equal(this.pageObjects.invoicesPage.errorMessageWhenGenerateFileByDate);
    });
  });
});
