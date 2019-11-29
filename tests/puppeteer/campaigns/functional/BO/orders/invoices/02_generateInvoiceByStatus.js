require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {Statuses} = require('@data/demo/orders');
const {Invoices} = require('@data/demo/invoices');
const {OrderStatuses} = require('@data/demo/invoices');
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
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
    /* Delete the generated invoice */
    files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${Invoices.moreThanAnInvoice.fileName}`);
  });

  // Login into BO
  loginCommon.loginBO();

  // 1 : Generate PDF file by status
  describe('Generate PDF file by date', async () => {
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

    describe('Generate PDF file by status', async () => {
      it('should go to invoices page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.invoicesLink,
        );
        await this.pageObjects.boBasePage.closeSfToolBar();
        const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
      });

      it('should check the error message when we don\'t select a status', async function () {
        await this.pageObjects.invoicesPage.generatePDFByStatus();
        const textMessage = await this.pageObjects.invoicesPage.getTextContent(
          this.pageObjects.invoicesPage.alertTextBlock,
        );
        await expect(textMessage).to.equal(this.pageObjects.invoicesPage.errorMessageWhenNotSelectStatus);
      });

      it('should check the error message when there is no invoice in the status selected', async function () {
        await this.pageObjects.invoicesPage.chooseStatus(OrderStatuses.canceled.id);
        await this.pageObjects.invoicesPage.generatePDFByStatus();
        const textMessage = await this.pageObjects.invoicesPage.getTextContent(
          this.pageObjects.invoicesPage.alertTextBlock,
        );
        await expect(textMessage).to.equal(this.pageObjects.invoicesPage.errorMessageWhenGenerateFileByStatus);
      });

      it('should choose the statuses, generate the invoice and check the file existence', async function () {
        await this.pageObjects.invoicesPage.chooseStatus(OrderStatuses.paymentAccepted.id);
        await this.pageObjects.invoicesPage.chooseStatus(OrderStatuses.shipped.id);
        await this.pageObjects.invoicesPage.generatePDFByStatus();
        const exist = await files.checkFileExistence(
          global.BO.DOWNLOAD_PATH,
          Invoices.moreThanAnInvoice.fileName,
        );
        await expect(exist).to.be.true;
      });
    });
  });
});
