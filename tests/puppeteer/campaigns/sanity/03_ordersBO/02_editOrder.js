require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const testContext = require('@utils/testContext');

const baseContext = 'sanity_ordersBO_editOrder';

// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const OrderPage = require('@pages/BO/orders/view');
const OrdersPage = require('@pages/BO/orders');
const {Statuses} = require('@data/demo/orderStatuses');

let browser;
let page;
// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.ordersParentLink,
      this.pageObjects.dashboardPage.ordersLink,
    );

    await this.pageObjects.ordersPage.closeSfToolBar();
    const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
  });

  it('should go to the first order page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrder', baseContext);
    await this.pageObjects.ordersPage.goToOrder('1');
    const pageTitle = await this.pageObjects.orderPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.orderPage.pageTitle);
  });

  it('should modify the product quantity and check the validation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editOrderQuantity', baseContext);
    const newQuantity = await this.pageObjects.orderPage.modifyProductQuantity(1, 5);
    await expect(newQuantity, 'Quantity was not updated').to.equal(5);
  });

  it('should modify the order status and check the validation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editOrderStatus', baseContext);
    const orderStatus = await this.pageObjects.orderPage.modifyOrderStatus(Statuses.paymentAccepted.status);
    await expect(orderStatus).to.equal(Statuses.paymentAccepted.status);
  });

  // Logout from BO
  loginCommon.logoutBO();
});
