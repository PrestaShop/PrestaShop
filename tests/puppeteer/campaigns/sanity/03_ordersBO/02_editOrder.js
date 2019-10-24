require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BOBasePage = require('@pages/BO/BObasePage');
const OrderPage = require('@pages/BO/order');
const OrdersPage = require('@pages/BO/orders');
const {Statuses} = require('@data/demo/orders');

let browser;
let page;
// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    boBasePage: new BOBasePage(page),
    orderPage: new OrderPage(page),
    ordersPage: new OrdersPage(page),
  };
};

/*
  Connect to the BO
  Edit the first order
  Logout from the BO
 */
describe('Edit Order BO', async () => {
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
  it('should go to the first order page', async function () {
    await this.pageObjects.ordersPage.goToOrder('1');
    const pageTitle = await this.pageObjects.orderPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.orderPage.pageTitle);
  });
  it('should modify the product quantity and check the validation', async function () {
    const result = await this.pageObjects.orderPage.modifyProductQuantity('1', '5');
    await expect(result).to.be.true;
  });
  it('should modify the order status and check the validation', async function () {
    const result = await this.pageObjects.orderPage.modifyOrderStatus(Statuses.paymentAccepted.status);
    await expect(result).to.be.true;
  });
  it('should logout from the BO', async function () {
    await this.pageObjects.boBasePage.logoutBO();
    const pageTitle = await this.pageObjects.loginPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.loginPage.pageTitle);
  });
});
