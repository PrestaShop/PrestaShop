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
    numberOfOrders = await this.pageObjects.ordersPage.resetAndGetNumberOfLines();
    await expect(numberOfOrders).to.be.above(0);
  });

  const tests = [
    {args: {filterType: 'input', filterBy: 'id_order', filterValue: Orders.firstOrder.id}},
    {args: {filterType: 'input', filterBy: 'reference', filterValue: Orders.fourthOrder.ref}},
    {args: {filterType: 'select', filterBy: 'order_state', filterValue: Statuses.paymentError.status}},
  ];

  tests.forEach((test) => {
    it('should filter the Orders table by ID and check the result', async function () {
      await this.pageObjects.ordersPage.filterOrders(
        test.args.filterType,
        test.args.filterBy,
        test.args.filterValue,
      );
      const textColumn = await this.pageObjects.ordersPage.getTextColumn(test.args.filterBy, 1);
      await expect(textColumn).to.contains(test.args.filterValue);
    });

    it('should reset all filters', async function () {
      const numberOfOrdersAfterReset = await this.pageObjects.ordersPage.resetAndGetNumberOfLines();
      await expect(numberOfOrdersAfterReset).to.be.equal(numberOfOrders);
    });
  });

  // Logout from BO
  loginCommon.logoutBO();
  
});
