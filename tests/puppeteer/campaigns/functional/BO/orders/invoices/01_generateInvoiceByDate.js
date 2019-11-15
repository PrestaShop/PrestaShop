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

// Filter And Quick Edit invoices
describe('Filter And Quick Edit invoices', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO
  loginCommon.loginBO();

  // 1 : Generate PDF file by date
  describe('Generate PDF file by date', async () => {
    describe('Create invoice', async () => {
      it('should go to the orders page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.ordersLink,
        );
        const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
      });

      it('should go to the first order', async function () {
        await this.pageObjects.ordersPage.goToOrder(1);
        const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
      });

      it('should change the order status to \'Shipped\' and check the validation', async function () {
        const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.shipped.status);
        await expect(result).to.be.true;
      });

      it('should go to the orders page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.ordersLink,
        );
        const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
      });

      it('should go to the second order', async function () {
        await this.pageObjects.ordersPage.goToOrder(2);
        const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
      });

      it('should change the order status to \'Payment accepted\' and check the validation', async function () {
        const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.paymentAccepted.status);
        await expect(result).to.be.true;
      });
    });

    describe('Generate PDF file by date', async () => {
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
        await files.waitForFileToDownload(global.BO.DOWNLOADSPATH);
        const exist = await files.checkFileExistence(
          global.BO.DOWNLOADSPATH,
          Invoices.moreThanAnInvoice.fileName,
        );
        await expect(exist, 'The file does not exist!').to.be.true;
      });

      it('should delete the invoice', async function () {
        await files.deleteFile(`${global.BO.DOWNLOADSPATH}/${Invoices.moreThanAnInvoice.fileName}`);
        const exist = await files.checkFileExistence(
          global.BO.DOWNLOADSPATH,
          Invoices.moreThanAnInvoice.fileName,
        );
        await expect(exist, 'The file exist!').to.be.false;
      });

      /*it('should check the error message when there is no invoice in the entered date', async function () {
        await this.pageObjects.invoicesPage.generatePDFByDate('2020-08-04', '2020-08-10');
        const textMessage = await this.pageObjects.invoicesPage.getTextContent(
          this.pageObjects.invoicesPage.alertTextBlock,
        );
        await expect(textMessage).to.equal(this.pageObjects.invoicesPage.errorMessageWhenGenerateFileByDate);
      });*/
    });
  });
});
