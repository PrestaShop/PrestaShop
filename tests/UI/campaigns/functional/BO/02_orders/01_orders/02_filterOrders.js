require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_filterOrders';

// importing pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');

const {Orders} = require('@data/demo/orders');

let numberOfOrders;
let browserContext;
let page;

/*
Filter orders By :
Id, reference, new client, delivery, customer, total, payment and status
*/
describe('Filter the Orders table by ID, REFERENCE, STATUS', async () => {
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

    const pageTitle = await ordersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(ordersPage.pageTitle);
  });

  it('should reset all filters and get number of orders', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

    numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
    await expect(numberOfOrders).to.be.above(0);
  });

  const tests = [
    {
      args:
        {
          identifier: 'filterById',
          filterType: 'input',
          filterBy: 'id_order',
          filterValue: Orders.firstOrder.id,
        },
    },
    {
      args:
        {
          identifier: 'filterByReference',
          filterType: 'input',
          filterBy: 'reference',
          filterValue: Orders.fourthOrder.ref,
        },
    },
    {
      args:
        {
          identifier: 'filterByNewClient',
          filterType: 'select',
          filterBy: 'new',
          filterValue: Orders.firstOrder.newClient,
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
          filterValue: Orders.firstOrder.customer,
        },
    },
    {
      args:
        {
          identifier: 'filterByTotalPaid',
          filterType: 'input',
          filterBy: 'total_paid_tax_incl',
          filterValue: Orders.fourthOrder.totalPaid,
        },
    },
    {
      args:
        {
          identifier: 'filterByPayment',
          filterType: 'input',
          filterBy: 'payment',
          filterValue: Orders.firstOrder.paymentMethod,
        },
    },
    {
      args:
        {
          identifier: 'filterOsName',
          filterType: 'select',
          filterBy: 'osname',
          filterValue: Orders.thirdOrder.status,
        },
    },
  ];

  tests.forEach((test) => {
    it(`should filter the Orders table by '${test.args.filterBy}' and check the result`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.identifier, baseContext);

      await ordersPage.filterOrders(
        page,
        test.args.filterType,
        test.args.filterBy,
        test.args.filterValue,
      );

      const numberOfOrdersAfterFilter = await ordersPage.getNumberOfElementInGrid(page);
      await expect(numberOfOrdersAfterFilter).to.be.at.most(numberOfOrders);

      for (let row = 1; row <= numberOfOrdersAfterFilter; row++) {
        const textColumn = await ordersPage.getTextColumn(page, test.args.filterBy, row);
        await expect(textColumn).to.contains(test.args.filterValue);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.identifier}Reset`, baseContext);

      const numberOfOrdersAfterReset = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrdersAfterReset).to.be.equal(numberOfOrders);
    });
  });
});
