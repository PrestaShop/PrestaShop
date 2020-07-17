require('module-alias/register');

// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders/index');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_sortOrders';

let browserContext;
let page;

describe('Sort orders', async () => {
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

  it('should go to orders page', async function () {
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

  const tests = [
    {
      args:
        {
          testIdentifier: 'sortByReferenceAsc', sortBy: 'reference', sortDirection: 'asc', isFloat: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByReferenceDesc', sortBy: 'reference', sortDirection: 'desc', isFloat: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByCountryNameAsc', sortBy: 'country_name', sortDirection: 'asc', isFloat: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByCountryNameDesc', sortBy: 'country_name', sortDirection: 'desc', isFloat: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByCustomerAsc', sortBy: 'customer', sortDirection: 'asc', isFloat: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByCustomerDesc', sortBy: 'customer', sortDirection: 'desc', isFloat: false,
        },
    },
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
    {
      args:
        {
          testIdentifier: 'sortByPaymentAsc', sortBy: 'payment', sortDirection: 'asc', isFloat: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByPaymentDesc', sortBy: 'payment', sortDirection: 'desc', isFloat: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByOsnameAsc', sortBy: 'osname', sortDirection: 'asc', isFloat: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByOsnameDesc', sortBy: 'osname', sortDirection: 'desc', isFloat: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_order', sortDirection: 'asc', isFloat: true,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_order', sortDirection: 'desc', isFloat: true,
        },
    },
  ];

  tests.forEach((test) => {
    it(`should sort by ${test.args.sortBy} ${test.args.sortDirection}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

      let nonSortedTable = await ordersPage.getAllRowsColumnContent(page, test.args.sortBy);

      await ordersPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

      let sortedTable = await ordersPage.getAllRowsColumnContent(page, test.args.sortBy);

      if (test.args.isFloat) {
        nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
        sortedTable = await sortedTable.map(text => parseFloat(text));
      }

      const expectedResult = await ordersPage.sortArray(nonSortedTable, test.args.isFloat);

      if (test.args.sortDirection === 'asc') {
        await expect(sortedTable).to.deep.equal(expectedResult);
      } else {
        await expect(sortedTable).to.deep.equal(expectedResult.reverse());
      }
    });
  });
});
