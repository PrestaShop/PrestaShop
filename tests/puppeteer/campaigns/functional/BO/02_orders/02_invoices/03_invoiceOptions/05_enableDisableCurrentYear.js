require('module-alias/register');
// Using chai
const {expect} = require('chai');
const chai = require('chai');
chai.use(require('chai-string'));
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const InvoicesPage = require('@pages/BO/orders/invoices/index');
const OrdersPage = require('@pages/BO/orders/index');
const ViewOrderPage = require('@pages/BO/orders/view');
// Importing data
const {Statuses} = require('@data/demo/orders');

let browser;
let page;
let fileName;
const today = new Date();
const currentYear = today.getFullYear().toString();

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

/*
Enable Add current year to invoice number
Choose the option After the sequential number
Change the first Order status to shipped
Check the current year in the invoice file name
Choose the option Before the sequential number
Check the current year in the invoice file name
Disable Add current year to invoice number
Check that the current year does not exist in the invoice file name
 */
describe('Edit invoice prefix and check the generated invoice file name', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO
  loginCommon.loginBO();

  describe('Enable add current year to invoice number then check the invoice file name', async () => {
    describe('Enable add current year to invoice number', async () => {
      it('should go to invoices page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.invoicesLink,
        );
        await this.pageObjects.boBasePage.closeSfToolBar();
        const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
      });

      it('should enable add current year to invoice number', async function () {
        await this.pageObjects.invoicesPage.enableAddCurrentYearToInvoice(true);
        const textMessage = await this.pageObjects.invoicesPage.saveInvoiceOptions();
        await expect(textMessage).to.contains(this.pageObjects.invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Choose the position of the year date at the end', async () => {
      it('should choose \'After the sequential number\'', async function () {
        // Choose the option 'After the sequential number' (ID = 0)
        await this.pageObjects.invoicesPage.chooseInvoiceOptionsYearPosition(0);
        const textMessage = await this.pageObjects.invoicesPage.saveInvoiceOptions();
        await expect(textMessage).to.contains(this.pageObjects.invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Check the invoice file Name', async () => {
      it('should go to the orders page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.ordersLink,
        );
        const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await this.pageObjects.ordersPage.goToOrder(1);
        const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
      });

      it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
        const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.shipped.status);
        await expect(result).to.be.true;
      });

      it('should check that the invoice file name contain current year at the end', async function () {
        fileName = await this.pageObjects.viewOrderPage.getFileName();
        expect(fileName).to.endWith(currentYear);
      });
    });

    describe('Choose the position of the year at the beginning', async () => {
      it('should go to invoices page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.invoicesLink,
        );
        await this.pageObjects.boBasePage.closeSfToolBar();
        const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
      });

      it('should choose \'Before the sequential number\'', async function () {
        // Choose the option 'Before the sequential number' (ID = 1)
        await this.pageObjects.invoicesPage.chooseInvoiceOptionsYearPosition(1);
        const textMessage = await this.pageObjects.invoicesPage.saveInvoiceOptions();
        await expect(textMessage).to.contains(this.pageObjects.invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Check the invoice file Name', async () => {
      it('should go to the orders page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.ordersLink,
        );
        const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await this.pageObjects.ordersPage.goToOrder(1);
        const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
      });

      it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
        const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.shipped.status);
        await expect(result).to.be.true;
      });

      it('should check that the invoice file name contain current year at the beginning', async function () {
        fileName = await this.pageObjects.viewOrderPage.getFileName();
        expect(fileName).to.startWith(`IN${currentYear}`);
      });
    });
  });

  describe('Disable add current year to invoice number then check the invoice file name', async () => {
    describe('Disable add current year to invoice number', async () => {
      it('should go to invoices page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.invoicesLink,
        );
        await this.pageObjects.boBasePage.closeSfToolBar();
        const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
      });

      it('should disable add current year to invoice number', async function () {
        await this.pageObjects.invoicesPage.enableAddCurrentYearToInvoice(false);
        const textMessage = await this.pageObjects.invoicesPage.saveInvoiceOptions();
        await expect(textMessage).to.contains(this.pageObjects.invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Check the invoice file Name', async () => {
      it('should go to the orders page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.ordersLink,
        );
        const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await this.pageObjects.ordersPage.goToOrder(1);
        const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
      });

      it('should check that the invoice file name does not contain the current year', async function () {
        fileName = await this.pageObjects.viewOrderPage.getFileName();
        expect(fileName).to.not.contains(currentYear);
      });
    });
  });
});
