// Import utils
import helper from '@utils/helpers';
import date from '@utils/date';
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

const baseContext: string = 'functional_BO_orders_orders_filterOrders';

/*
Filter orders By :
Id, reference, new client, delivery, customer, total, payment, status and date from, date to
*/
describe('BO - Orders : Filter the Orders table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfOrders: number;

  const today: string = date.getDateFormat('yyyy-mm-dd');
  const dateToCheck: string = date.getDateFormat('mm/dd/yyyy');

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

    const pageTitle = await ordersPage.getPageTitle(page);
    expect(pageTitle).to.contains(ordersPage.pageTitle);
  });

  it('should reset all filters and get number of orders', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

    numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
    expect(numberOfOrders).to.be.above(0);
  });

  [
    {
      args:
        {
          identifier: 'filterById',
          filterType: 'input',
          filterBy: 'id_order',
          filterValue: Orders.firstOrder.id.toString(),
        },
    },
    {
      args:
        {
          identifier: 'filterByReference',
          filterType: 'input',
          filterBy: 'reference',
          filterValue: Orders.fourthOrder.reference,
        },
    },
    {
      args:
        {
          identifier: 'filterByNewClient',
          filterType: 'select',
          filterBy: 'new',
          filterValue: Orders.firstOrder.newClient ? 'Yes' : 'No',
        },
    },
    {
      args:
        {
          identifier: 'filterByDelivery',
          filterType: 'select',
          filterBy: 'country_name',
          filterValue: Orders.firstOrder.delivery,
        },
    },
    {
      args:
        {
          identifier: 'filterByCustomer',
          filterType: 'input',
          filterBy: 'customer',
          filterValue: `${Orders.firstOrder.customer.firstName[0]}. ${Orders.firstOrder.customer.lastName.toUpperCase()}`,
        },
    },
    {
      args:
        {
          identifier: 'filterByTotalPaid',
          filterType: 'input',
          filterBy: 'total_paid_tax_incl',
          filterValue: Orders.fourthOrder.totalPaid.toString(),
        },
    },
    {
      args:
        {
          identifier: 'filterByPayment',
          filterType: 'input',
          filterBy: 'payment',
          filterValue: Orders.firstOrder.paymentMethod.name,
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
  ].forEach((test) => {
    it(`should filter the Orders table by '${test.args.filterBy}' and check the result`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.identifier, baseContext);

      await ordersPage.filterOrders(
        page,
        test.args.filterType,
        test.args.filterBy,
        test.args.filterValue,
      );

      const numberOfOrdersAfterFilter = await ordersPage.getNumberOfElementInGrid(page);
      expect(numberOfOrdersAfterFilter).to.be.at.most(numberOfOrders);

      for (let row = 1; row <= numberOfOrdersAfterFilter; row++) {
        const textColumn = await ordersPage.getTextColumn(page, test.args.filterBy, row);
        expect(textColumn).to.equal(test.args.filterValue);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.identifier}Reset`, baseContext);

      const numberOfOrdersAfterReset = await ordersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrdersAfterReset).to.be.equal(numberOfOrders);
    });
  });

  it('should filter the orders table by \'Date from\' and \'Date to\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByDate', baseContext);

    // Filter orders
    await ordersPage.filterOrdersByDate(page, today, today);

    // Check number of element
    const numberOfOrdersAfterFilter = await ordersPage.getNumberOfElementInGrid(page);
    expect(numberOfOrdersAfterFilter).to.be.at.most(numberOfOrders);

    for (let i = 1; i <= numberOfOrdersAfterFilter; i++) {
      const textColumn = await ordersPage.getTextColumn(page, 'date_add', i);
      expect(textColumn).to.contains(dateToCheck);
    }
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    const numberOfOrdersAfterReset = await ordersPage.resetAndGetNumberOfLines(page);
    expect(numberOfOrdersAfterReset).to.be.equal(numberOfOrders);
  });
});
