require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {Statuses} = require('@data/demo/orders');
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

// Disable/enable invoices
describe('Test disable/enable invoices', async () => {
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

  describe('Disable invoices then check that there is no invoice created', async () => {
    it('should go to invoices page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.invoicesLink,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
    });

    it('should disable invoices', async function () {
      await this.pageObjects.invoicesPage.enableInvoices(false);
      const textMessage = await this.pageObjects.invoicesPage.saveInvoiceOptions();
      await expect(textMessage).to.contains(this.pageObjects.invoicesPage.successfulUpdateMessage);
    });

    it('should go to the orders page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.ordersLink,
      );
      const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
    });

    it('should go to the fourth order page', async function () {
      await this.pageObjects.ordersPage.goToOrder(4);
      const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.shipped.status);
      await expect(result).to.be.true;
    });

    it('should check that there is no invoice document created', async function () {
      const documentName = await this.pageObjects.viewOrderPage.getDocumentName();
      await expect(documentName).to.be.not.equal('Invoice');
    });
  });

  describe('Enable invoices then check the invoice file created', async () => {
    it('should go to invoices page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.invoicesLink,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
    });

    it('should enable invoices', async function () {
      await this.pageObjects.invoicesPage.enableInvoices(true);
      const textMessage = await this.pageObjects.invoicesPage.saveInvoiceOptions();
      await expect(textMessage).to.contains(this.pageObjects.invoicesPage.successfulUpdateMessage);
    });

    it('should go to the orders page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.ordersLink,
      );
      const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
    });

    it('should go to the third order page', async function () {
      await this.pageObjects.ordersPage.goToOrder(3);
      const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.shipped.status);
      await expect(result).to.be.true;
    });

    it('should check the invoice document', async function () {
      const documentName = await this.pageObjects.viewOrderPage.getDocumentName();
      await expect(documentName).to.be.equal('Invoice');
    });
  });
});
