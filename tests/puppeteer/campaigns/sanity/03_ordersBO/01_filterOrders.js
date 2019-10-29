require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BOBasePage = require('@pages/BO/BObasePage');
const OrdersPage = require('@pages/BO/orders');
const {Orders, Statuses} = require('@data/demo/orders');

let numberOfOrders;
let browser;
let page;
// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    boBasePage: new BOBasePage(page),
    ordersPage: new OrdersPage(page),
  };
};

/*
  Connect to the BO
  Filter the Orders table
  Logout from the BO
 */
describe('Filter the Orders table by ID, REFERENCE, STATUS', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await browser.close();
  });
  // Steps
  loginCommon.loginBO();
  it('should go to the Orders page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.ordersParentLink,
      this.pageObjects.boBasePage.ordersLink,
    );
    const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
  });
  it('should reset all filters and get number of orders', async function () {
    numberOfOrders = await this.pageObjects.ordersPage.resetFilter();
    await expect(numberOfOrders).to.be.above(0);
  });
  it('should filter the Orders table by ID and check the result', async function () {
    await this.pageObjects.ordersPage.filterOrders(
      'input',
      'id_order',
      Orders.firstOrder.id,
    );
    const result = await this.pageObjects.ordersPage.checkTextValue(
      this.pageObjects.ordersPage.orderfirstLineIdTD.replace('%ROW', '1'),
      Orders.firstOrder.id,
    );
    await expect(result).to.be.true;
  });
  it('should reset all filters', async function () {
    const numberOfOrdersAfterReset = await this.pageObjects.ordersPage.resetFilter();
    await expect(numberOfOrdersAfterReset).to.be.equal(numberOfOrders);
  });
  it('should filter the Orders table by REFERENCE and check the result', async function () {
    await this.pageObjects.ordersPage.filterOrders(
      'input',
      'reference',
      Orders.fourthOrder.ref,
    );
    const result = await this.pageObjects.boBasePage.checkTextValue(
      this.pageObjects.ordersPage.orderfirstLineReferenceTD.replace('%ROW', '1'),
      Orders.fourthOrder.ref,
    );
    await expect(result).to.be.true;
  });
  it('should reset all filters', async function () {
    const numberOfOrdersAfterReset = await this.pageObjects.ordersPage.resetFilter();
    await expect(numberOfOrdersAfterReset).to.be.equal(numberOfOrders);
  });
  it('should filter the Orders table by STATUS and check the result', async function () {
    await this.pageObjects.ordersPage.filterOrders(
      'select',
      'order_state',
      Statuses.paymentError.status,
    );
    const result = await this.pageObjects.ordersPage.checkTextValue(
      this.pageObjects.ordersPage.orderfirstLineStatusTD.replace('%ROW', '1'),
      Statuses.paymentError.status,
    );
    await expect(result).to.be.true;
  });
  it('should reset all filters', async function () {
    const numberOfOrdersAfterReset = await this.pageObjects.ordersPage.resetFilter();
    await expect(numberOfOrdersAfterReset).to.be.equal(numberOfOrders);
  });
  it('should logout from the BO', async function () {
    await this.pageObjects.boBasePage.logoutBO();
    const pageTitle = await this.pageObjects.loginPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.loginPage.pageTitle);
  });
});
