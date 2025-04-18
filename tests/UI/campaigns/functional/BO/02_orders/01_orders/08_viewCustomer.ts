import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCustomersViewPage,
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  type BrowserContext,
  dataCustomers,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_orders_orders_viewCustomer';

/*
Go to orders page
Filter by customer name 'J. DOE'
Click on customer link on grid
Check that View customer page is displayed
 */
describe('BO - Orders : View customer from orders page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Orders > Orders\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.ordersParentLink,
      boDashboardPage.ordersLink,
    );
    await boOrdersPage.closeSfToolBar(page);

    const pageTitle = await boOrdersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrdersPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

    const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
    expect(numberOfOrders).to.be.above(0);
  });

  it('should filter order by customer name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

    await boOrdersPage.filterOrders(
      page,
      'input',
      'customer',
      dataCustomers.johnDoe.lastName,
    );

    const numberOfOrders = await boOrdersPage.getNumberOfElementInGrid(page);
    expect(numberOfOrders).to.be.at.least(1);
  });

  it('should check customer link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewCustomer', baseContext);

    // Click on customer link first row
    page = await boOrdersPage.viewCustomer(page, 1);

    const pageTitle = await boCustomersViewPage.getPageTitle(page);
    expect(pageTitle).to
      .eq(boCustomersViewPage.pageTitle(`${dataCustomers.johnDoe.firstName[0]}. ${dataCustomers.johnDoe.lastName}`));
  });

  it('should go back to \'Orders > Orders\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrdersPageToResetFilter', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.ordersParentLink,
      boDashboardPage.ordersLink,
    );

    const pageTitle = await boOrdersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrdersPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersAfterCheck', baseContext);

    const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
    expect(numberOfOrders).to.be.above(0);
  });
});
