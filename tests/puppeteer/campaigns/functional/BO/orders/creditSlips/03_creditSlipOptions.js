require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CreditSlipsPage = require('@pages/BO/orders/creditSlips/index');
const OrdersPage = require('@pages/BO/orders/index');
const ViewOrderPage = require('@pages/BO/orders/view');
// Importing data
const {Statuses} = require('@data/demo/orders');

let browser;
let page;
let fileName;
const prefixToEdit = 'CreSlip';

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    creditSlipsPage: new CreditSlipsPage(page),
    ordersPage: new OrdersPage(page),
    viewOrderPage: new ViewOrderPage(page),
  };
};

/*
Edit credit slip prefix
Change the Order status to shipped
Check the credit slip file name
Delete the slip prefix value
Check the credit slip file name
 */
describe('Edit credit slip prefix and check the generated file name', async () => {
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

  describe(`Change the credit slip prefix to '${prefixToEdit}'`, async () => {
    it('should go to Credit slips page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.creditSlips,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.creditSlipsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.creditSlipsPage.pageTitle);
    });

    it(`should change the credit slip prefix to ${prefixToEdit}`, async function () {
      await this.pageObjects.creditSlipsPage.changePrefix(prefixToEdit);
      const textMessage = await this.pageObjects.creditSlipsPage.saveCreditSlipOptions();
      await expect(textMessage).to.contains(this.pageObjects.creditSlipsPage.successfulUpdateMessage);
    });
  });

  describe(`Change the order status to '${Statuses.shipped.status}' and check the credit slip file Name`, async () => {
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

    it(`should check that the credit slip file name contain the prefix '${prefixToEdit}'`, async function () {
      fileName = await this.pageObjects.viewOrderPage.getFileName(4);
      expect(fileName).to.contains(prefixToEdit);
    });
  });

  describe(`Back to the default credit slip prefix value '${prefixToEdit}'`, async () => {
    it('should go to Credit slips page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.creditSlips,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.creditSlipsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.creditSlipsPage.pageTitle);
    });

    it('should delete the credit slip prefix', async function () {
      await this.pageObjects.creditSlipsPage.changePrefix(' ');
      const textMessage = await this.pageObjects.creditSlipsPage.saveCreditSlipOptions();
      await expect(textMessage).to.contains(this.pageObjects.creditSlipsPage.successfulUpdateMessage);
    });
  });

  describe('Check that the edited prefix does not exist in the credit slip file Name', async () => {
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

    it('should check that the credit slip file name', async function () {
      fileName = await this.pageObjects.viewOrderPage.getFileName(4);
      expect(fileName).to.not.contains(prefixToEdit);
    });
  });
});
