require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const testContext = require('@utils/testContext');

const baseContext = 'sanity_ordersBO_editOrder';

// importing pages
const dashboardPage = require('@pages/BO/dashboard');
const orderPage = require('@pages/BO/orders/view');
const ordersPage = require('@pages/BO/orders');
const {Statuses} = require('@data/demo/orderStatuses');

let browserContext;
let page;

/*
  Connect to the BO
  Edit the first order
  Logout from the BO
 */
describe('Edit Order BO', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });
  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Steps
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

  it('should go to the first order page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrder', baseContext);

    await ordersPage.goToOrder(page, 1);
    const pageTitle = await orderPage.getPageTitle(page);
    await expect(pageTitle).to.contains(orderPage.pageTitle);
  });

  it('should modify the product quantity and check the validation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editOrderQuantity', baseContext);

    const newQuantity = await orderPage.modifyProductQuantity(page, 1, 5);
    await expect(newQuantity, 'Quantity was not updated').to.equal(5);
  });

  it('should modify the order status and check the validation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editOrderStatus', baseContext);
    const orderStatus = await orderPage.modifyOrderStatus(page, Statuses.paymentAccepted.status);
    await expect(orderStatus).to.equal(Statuses.paymentAccepted.status);
  });

  // Logout from BO
  it('should log out from BO', async function () {
    await loginCommon.logoutBO(this, page);
  });
});
