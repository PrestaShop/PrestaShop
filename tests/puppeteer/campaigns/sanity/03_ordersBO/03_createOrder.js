require('module-alias/register');

// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');


// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const OrdersPage = require('@pages/BO/orders');
const AddOrderPage = require('@pages/BO/orders/add');


let browser;
let page;

// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    ordersPage: new OrdersPage(page),
    addOrderPage: new AddOrderPage(page),
  };
};

/*

 */
describe('Create Order BO', async () => {
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

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.ordersParentLink,
      this.pageObjects.dashboardPage.ordersLink,
    );

    await this.pageObjects.ordersPage.closeSfToolBar();
    const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
  });

  it('should search a customer and click on first customer of the results list', async function () {

  });

  it('should add a product with a quantity and see the stock counter changes', async function () {

  });

  it('should modify the quantity of added product and see the stock counter changes', async function () {

  });

  it('should delete a product from cart and see the stock counter changes', async function () {

  });

  // Logout from BO
  loginCommon.logoutBO();
});
