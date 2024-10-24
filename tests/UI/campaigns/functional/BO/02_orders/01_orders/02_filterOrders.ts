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
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_orders_orders_filterOrders';

/*
Filter orders By :
Id, reference, new client, delivery, customer, total, payment, status and date from, date to
*/
describe('BO - Orders : Filter the Orders table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfOrders: number;

  const today: string = utilsDate.getDateFormat('yyyy-mm-dd');
  const dateToCheck: string = utilsDate.getDateFormat('mm/dd/yyyy');

  // before and after functions
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

    const pageTitle = await boOrdersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrdersPage.pageTitle);
  });

  it('should reset all filters and get number of orders', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

    numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
    expect(numberOfOrders).to.be.above(0);
  });

  [
    {
      args:
        {
          identifier: 'filterById',
          filterType: 'input',
          filterBy: 'id_order',
          filterValue: dataOrders.order_1.id.toString(),
        },
    },
    {
      args:
        {
          identifier: 'filterByReference',
          filterType: 'input',
          filterBy: 'reference',
          filterValue: dataOrders.order_4.reference,
        },
    },
    {
      args:
        {
          identifier: 'filterByNewClient',
          filterType: 'select',
          filterBy: 'new',
          filterValue: dataOrders.order_1.newClient ? 'Yes' : 'No',
        },
    },
    {
      args:
        {
          identifier: 'filterByDelivery',
          filterType: 'select',
          filterBy: 'country_name',
          filterValue: dataOrders.order_1.delivery,
        },
    },
    {
      args:
        {
          identifier: 'filterByCustomer',
          filterType: 'input',
          filterBy: 'customer',
          filterValue: `${dataOrders.order_1.customer.firstName[0]}. ${dataOrders.order_1.customer.lastName.toUpperCase()}`,
        },
    },
    {
      args:
        {
          identifier: 'filterByTotalPaid',
          filterType: 'input',
          filterBy: 'total_paid_tax_incl',
          filterValue: dataOrders.order_4.totalPaid.toString(),
        },
    },
    {
      args:
        {
          identifier: 'filterByPayment',
          filterType: 'input',
          filterBy: 'payment',
          filterValue: dataOrders.order_1.paymentMethod.name,
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
  ].forEach((test) => {
    it(`should filter the Orders table by '${test.args.filterBy}' and check the result`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.identifier, baseContext);

      await boOrdersPage.filterOrders(
        page,
        test.args.filterType,
        test.args.filterBy,
        test.args.filterValue,
      );

      const numberOfOrdersAfterFilter = await boOrdersPage.getNumberOfElementInGrid(page);
      expect(numberOfOrdersAfterFilter).to.be.at.most(numberOfOrders);

      for (let row = 1; row <= numberOfOrdersAfterFilter; row++) {
        const textColumn = await boOrdersPage.getTextColumn(page, test.args.filterBy, row);
        expect(textColumn).to.equal(test.args.filterValue);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.identifier}Reset`, baseContext);

      const numberOfOrdersAfterReset = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrdersAfterReset).to.be.equal(numberOfOrders);
    });
  });

  it('should filter the orders table by \'Date from\' and \'Date to\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByDate', baseContext);

    // Filter orders
    await boOrdersPage.filterOrdersByDate(page, today, today);

    // Check number of element
    const numberOfOrdersAfterFilter = await boOrdersPage.getNumberOfElementInGrid(page);
    expect(numberOfOrdersAfterFilter).to.be.at.most(numberOfOrders);

    for (let i = 1; i <= numberOfOrdersAfterFilter; i++) {
      const textColumn = await boOrdersPage.getTextColumn(page, 'date_add', i);
      expect(textColumn).to.contains(dateToCheck);
    }
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    const numberOfOrdersAfterReset = await boOrdersPage.resetAndGetNumberOfLines(page);
    expect(numberOfOrdersAfterReset).to.be.equal(numberOfOrders);
  });
});
