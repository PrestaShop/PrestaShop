// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  type BrowserContext,
  dataOrders,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'sanity_ordersBO_filterOrders';

/*
  Connect to the BO
  Filter the Orders table
  Logout from the BO
 */
describe('BO - Orders - Orders : Filter the Orders table by ID, REFERENCE, STATUS', () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfOrders: number;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Steps
  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to the \'Orders > Orders\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.ordersParentLink,
      boDashboardPage.ordersLink,
    );

    const pageTitle = await boOrdersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrdersPage.pageTitle);
  });

  it('should reset all filters and get number of orders', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilters1', baseContext);

    numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
    expect(numberOfOrders).to.be.above(0);
  });

  const tests = [
    {
      args:
        {
          identifier: 'filterId',
          filterType: 'input',
          filterBy: 'id_order',
          filterValue: dataOrders.order_1.id.toString(),
        },
    },
    {
      args:
        {
          identifier: 'filterReference',
          filterType: 'input',
          filterBy: 'reference',
          filterValue: dataOrders.order_4.reference,
        },
    },
    {
      args:
        {
          identifier: 'filterOsName',
          filterType: 'select',
          filterBy: 'osname',
          filterValue: dataOrders.order_3.status?.name,
        },
    },
  ];

  tests.forEach((test) => {
    it(`should filter the Orders table by '${test.args.filterBy}' and check the result`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `filterOrders_${test.args.identifier}`, baseContext);

      await boOrdersPage.filterOrders(
        page,
        test.args.filterType,
        test.args.filterBy,
        test.args.filterValue,
      );

      const textColumn = await boOrdersPage.getTextColumn(page, test.args.filterBy, 1);
      expect(textColumn).to.equal(test.args.filterValue);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `resetFilters_${test.args.identifier}`, baseContext);

      const numberOfOrdersAfterReset = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrdersAfterReset).to.be.equal(numberOfOrders);
    });
  });

  // Logout from BO
  it('should log out from BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'logoutBO', baseContext);

    await boDashboardPage.logoutBO(page);

    const pageTitle = await boLoginPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLoginPage.pageTitle);
  });
});
