// Import utils
import helper from '@utils/helpers';

// Import test context
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

require('module-alias/register');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const viewCustomerPage = require('@pages/BO/customers/view');

// Import customer 'J. DOE'
const {DefaultCustomer} = require('@data/demo/customer');

const baseContext = 'functional_BO_orders_orders_viewCustomer';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

/*
Go to orders page
Filter by customer name 'J. DOE'
Click on customer link on grid
Check that View customer page is displayed
 */
describe('BO - Orders : View customer from orders page', async () => {
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

  it('should go to \'Orders > Orders\' page', async function () {
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

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

    const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
    await expect(numberOfOrders).to.be.above(0);
  });

  it('should filter order by customer name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

    await ordersPage.filterOrders(
      page,
      'input',
      'customer',
      DefaultCustomer.lastName,
    );

    const numberOfOrders = await ordersPage.getNumberOfElementInGrid(page);
    await expect(numberOfOrders).to.be.at.least(1);
  });

  it('should check customer link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewCustomer', baseContext);

    // Click on customer link first row
    page = await ordersPage.viewCustomer(page, 1);

    const pageTitle = await viewCustomerPage.getPageTitle(page);
    await expect(pageTitle).to
      .contains(`${viewCustomerPage.pageTitle} ${DefaultCustomer.firstName[0]}. ${DefaultCustomer.lastName}`);
  });

  it('should go back to \'Orders > Orders\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrdersPageToResetFilter', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.ordersParentLink,
      dashboardPage.ordersLink,
    );

    const pageTitle = await ordersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(ordersPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersAfterCheck', baseContext);

    const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
    await expect(numberOfOrders).to.be.above(0);
  });
});
