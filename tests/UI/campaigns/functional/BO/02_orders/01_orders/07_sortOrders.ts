// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_orders_sortOrders';

/*
Sort orders by:
ID, reference, delivery, customer, total, payment, status and date
 */
describe('BO - Orders : Sort orders', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  it('should go to \'Orders > Orders\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.ordersParentLink,
      dashboardPage.ordersLink,
    );
    await ordersPage.closeSfToolBar(page);

    const pageTitle = await ordersPage.getPageTitle(page);
    expect(pageTitle).to.contains(ordersPage.pageTitle);
  });

  const tests = [
    {args: {testIdentifier: 'sortByReferenceAsc', sortBy: 'reference', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByReferenceDesc', sortBy: 'reference', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByCountryNameAsc', sortBy: 'country_name', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByCountryNameDesc', sortBy: 'country_name', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByCustomerAsc', sortBy: 'customer', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByCustomerDesc', sortBy: 'customer', sortDirection: 'desc'}},
    {
      args:
        {
          testIdentifier: 'sortByTotalPaidAsc', sortBy: 'total_paid_tax_incl', sortDirection: 'asc', isFloat: true,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByTotalPaidDesc', sortBy: 'total_paid_tax_incl', sortDirection: 'desc', isFloat: true,
        },
    },
    {args: {testIdentifier: 'sortByPaymentAsc', sortBy: 'payment', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByPaymentDesc', sortBy: 'payment', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByOsnameAsc', sortBy: 'osname', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByOsnameDesc', sortBy: 'osname', sortDirection: 'desc'}},
    {
      args: {
        testIdentifier: 'sortByDateDesc', sortBy: 'date_add', sortDirection: 'desc', isDate: true,
      },
    },
    {
      args: {
        testIdentifier: 'sortByDateAsc', sortBy: 'date_add', sortDirection: 'asc', isDate: true,
      },
    },
    {
      args: {
        testIdentifier: 'sortByIdAsc', sortBy: 'id_order', sortDirection: 'asc', isFloat: true,
      },
    },
    {
      args: {
        testIdentifier: 'sortByIdDesc', sortBy: 'id_order', sortDirection: 'desc', isFloat: true,
      },
    },
  ];

  tests.forEach((test) => {
    it(`should sort by ${test.args.sortBy} ${test.args.sortDirection}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

      const nonSortedTable = await ordersPage.getAllRowsColumnContent(page, test.args.sortBy);

      await ordersPage.sortTable(page, test.args.sortBy, test.args.sortDirection);
      const sortedTable = await ordersPage.getAllRowsColumnContent(page, test.args.sortBy);

      if (test.args.isFloat) {
        const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
        const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

        const expectedResult = await basicHelper.sortArrayNumber(nonSortedTableFloat);

        if (test.args.sortDirection === 'asc') {
          expect(sortedTableFloat).to.deep.equal(expectedResult);
        } else {
          expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
        }
      } else {
        const expectedResult: string[] = await basicHelper.sortArray(nonSortedTable);

        if (test.args.sortDirection === 'asc') {
          expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      }
    });
  });
});
