require('module-alias/register');

// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const testContext = require('@utils/testContext');

// importing pages
const LoginPage = require('@pages/BO/login');
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const addOrderPage = require('@pages/BO/orders/add');

const baseContext = 'sanity_ordersBO_createOrder';

let browserContext;
let page;



describe('Create Order BO', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

  });
  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to the Orders page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.ordersParentLink,
      dashboardPage.ordersLink,
    );

    await ordersPage.closeSfToolBar(page);
    const pageTitle = await ordersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(ordersPage.pageTitle);
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
