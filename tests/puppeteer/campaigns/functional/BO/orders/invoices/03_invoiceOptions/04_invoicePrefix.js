require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const InvoicesPage = require('@pages/BO/orders/invoices/index');
const OrdersPage = require('@pages/BO/orders/index');
const ViewOrderPage = require('@pages/BO/orders/view');
const CheckoutPage = require('@pages/FO/checkout');
const OrderConfirmationPage = require('@pages/FO/orderConfirmation');
// Importing data
const {Statuses} = require('@data/demo/orders');

let browser;
let page;
let fileName;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    invoicesPage: new InvoicesPage(page),
    ordersPage: new OrdersPage(page),
    viewOrderPage: new ViewOrderPage(page),
    checkoutPage: new CheckoutPage(page),
    orderConfirmationPage: new OrderConfirmationPage(page),
  };
};

/*
Edit invoice prefix
Change the Order status to Payment accepted
Check the invoice file name
Back to the default invoice prefix value
Check the invoice file name
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

  describe('Change the invoice prefix to \'#ABC\' then check the invoice file name', async () => {
    describe('Change the invoice prefix to \'#ABC\'', async () => {
      it('should go to invoices page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.invoicesLink,
        );
        await this.pageObjects.boBasePage.closeSfToolBar();
        const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
      });

      it('should change the invoice prefix to \'#ABC\'', async function () {
        await this.pageObjects.invoicesPage.changePrefix('#ABC');
        const textMessage = await this.pageObjects.invoicesPage.saveInvoiceOptions();
        await expect(textMessage).to.contains(this.pageObjects.invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Change the order status to "Payment accepted" and check the invoice file Name', async () => {
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

      it('should check that the invoice file name contain the prefix\'#ABC\'', async function () {
        fileName = await this.pageObjects.viewOrderPage.getFileName();
        expect(fileName).to.contains('ABC');
      });
    });

    describe('Back to the default invoice prefix value \'#IN\' then check the invoice file name', async () => {
      it('should go to invoices page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.invoicesLink,
        );
        await this.pageObjects.boBasePage.closeSfToolBar();
        const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
      });

      it('should change the invoice prefix to \'#IN\'', async function () {
        await this.pageObjects.invoicesPage.changePrefix('#IN');
        const textMessage = await this.pageObjects.invoicesPage.saveInvoiceOptions();
        await expect(textMessage).to.contains(this.pageObjects.invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Check the default prefix in the invoice file Name', async () => {
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

      it('should check that the invoice file name contain the default prefix \'#IN\'', async function () {
        fileName = await this.pageObjects.viewOrderPage.getFileName();
        expect(fileName).to.contains('IN');
      });
    });
  });
});
