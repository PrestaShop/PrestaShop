import type {BrowserContext, Page} from 'playwright';
// Using chai
import {expect} from 'chai';
// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
// Import login steps
import loginCommon from '@commonTests/BO/loginBO';
// Import pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import orderPageProductsBlock from '@pages/BO/orders/view/productsBlock';
// Import data
import {Statuses} from '@data/demo/orderStatuses';

require('module-alias/register');

const baseContext = 'sanity_ordersBO_editOrder';

let browserContext: BrowserContext;
let page: Page;

/*
  Connect to the BO
  Edit the first order
  Logout from the BO
 */
describe('BO - Orders - Orders : Edit Order BO', async () => {
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

  it('should go to the \'Orders > Orders\' page', async function () {
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
    const pageTitle = await orderPageProductsBlock.getPageTitle(page);
    await expect(pageTitle).to.contains(orderPageProductsBlock.pageTitle);
  });

  it('should modify the product quantity and check the validation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editOrderQuantity', baseContext);

    const newQuantity = await orderPageProductsBlock.modifyProductQuantity(page, 1, 5);
    await expect(newQuantity, 'Quantity was not updated').to.equal(5);
  });

  it('should modify the order status and check the validation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editOrderStatus', baseContext);

    const orderStatus = await orderPageProductsBlock.modifyOrderStatus(page, Statuses.paymentAccepted.status);
    await expect(orderStatus).to.equal(Statuses.paymentAccepted.status);
  });

  // Logout from BO
  it('should log out from BO', async function () {
    await loginCommon.logoutBO(this, page);
  });
});
