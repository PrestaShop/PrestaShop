// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';

// Import data
import Orders from '@data/demo/orders';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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

    const pageTitle = await ordersPage.getPageTitle(page);
    expect(pageTitle).to.contains(ordersPage.pageTitle);
  });

  it('should reset all filters and get number of orders', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilters1', baseContext);

    numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
    expect(numberOfOrders).to.be.above(0);
  });

  const tests = [
    {
      args:
        {
          identifier: 'filterId',
          filterType: 'input',
          filterBy: 'id_order',
          filterValue: Orders.firstOrder.id.toString(),
        },
    },
    {
      args:
        {
          identifier: 'filterReference',
          filterType: 'input',
          filterBy: 'reference',
          filterValue: Orders.fourthOrder.reference,
        },
    },
    {
      args:
        {
          identifier: 'filterOsName',
          filterType: 'select',
          filterBy: 'osname',
          filterValue: Orders.thirdOrder.status?.name,
        },
    },
  ];

  tests.forEach((test) => {
    it(`should filter the Orders table by '${test.args.filterBy}' and check the result`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `filterOrders_${test.args.identifier}`, baseContext);

      await ordersPage.filterOrders(
        page,
        test.args.filterType,
        test.args.filterBy,
        test.args.filterValue,
      );

      const textColumn = await ordersPage.getTextColumn(page, test.args.filterBy, 1);
      expect(textColumn).to.equal(test.args.filterValue);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `resetFilters_${test.args.identifier}`, baseContext);

      const numberOfOrdersAfterReset = await ordersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrdersAfterReset).to.be.equal(numberOfOrders);
    });
  });

  // Logout from BO
  it('should log out from BO', async function () {
    await loginCommon.logoutBO(this, page);
  });
});
