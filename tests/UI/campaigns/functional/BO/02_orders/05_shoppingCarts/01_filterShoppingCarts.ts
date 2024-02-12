// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import shoppingCartsPage from '@pages/BO/orders/shoppingCarts';

// Import data
import ShoppingCarts from '@data/demo/shoppingCarts';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_shoppingCarts_filterShoppingCarts';

/*
Delete the non ordered shopping carts
Filter shopping carts By :
Id, order id, customer, carrier, date and online
*/
describe('BO - Orders - Shopping carts : Filter the Shopping carts table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfShoppingCarts: number;

  const todayDate: string = date.getDateFormat('mm/dd/yyyy');

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

  it('should go to \'Orders > Shopping carts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.ordersParentLink,
      dashboardPage.shoppingCartsLink,
    );

    const pageTitle = await shoppingCartsPage.getPageTitle(page);
    expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
  });

  it('should reset all filters and get number of shopping carts', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

    numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
    expect(numberOfShoppingCarts).to.be.above(0);
  });

  it('should search the non ordered shopping carts and delete them if exist', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts', baseContext);

    await shoppingCartsPage.filterTable(page, 'select', 'status', 'Non ordered');

    const numberOfShoppingCartsAfterFilter = await shoppingCartsPage.getNumberOfElementInGrid(page);
    expect(numberOfShoppingCartsAfterFilter).to.be.at.most(numberOfShoppingCarts);

    numberOfShoppingCarts -= numberOfShoppingCartsAfterFilter;

    for (let row = 1; row <= numberOfShoppingCartsAfterFilter; row++) {
      const textColumn = await shoppingCartsPage.getTextColumn(page, row, 'status');
      expect(textColumn).to.contains('Non ordered');
    }

    if (numberOfShoppingCartsAfterFilter > 0) {
      const deleteTextResult = await shoppingCartsPage.bulkDeleteShoppingCarts(page);
      expect(deleteTextResult).to.be.contains(shoppingCartsPage.successfulMultiDeleteMessage);
    }
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteNonOrderedCarts', baseContext);

    const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
    expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
  });

  it('should change pagination to 300 items per page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo300', baseContext);

    let paginationNumber: string = '1';

    if (numberOfShoppingCarts >= 21) {
      paginationNumber = await shoppingCartsPage.selectPaginationLimit(page, 300);
    }

    expect(paginationNumber).to.equal('1');
  });

  const tests = [
    {
      args:
        {
          testIdentifier: 'filterId',
          filterType: 'input',
          filterBy: 'id_cart',
          filterValue: ShoppingCarts[1].id.toString(),
        },
    },
    {
      args:
        {
          testIdentifier: 'filterOrderID',
          filterType: 'input',
          filterBy: 'status',
          filterValue: ShoppingCarts[2].orderID.toString(),
        },
    },
    {
      args:
        {
          testIdentifier: 'filterCustomer',
          filterType: 'input',
          filterBy: 'c!lastname',
          filterValue: ShoppingCarts[3].customer.lastName,
        },
    },
    {
      args:
        {
          testIdentifier: 'filterCarrier',
          filterType: 'input',
          filterBy: 'ca!name',
          filterValue: ShoppingCarts[0].carrier.name,
        },
    },
    {
      args:
        {
          testIdentifier: 'filterOnline',
          filterType: 'select',
          filterBy: 'id_guest',
          filterValue: ShoppingCarts[4].online ? '1' : '0',
        },
    },
  ];

  tests.forEach((test) => {
    it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

      await shoppingCartsPage.filterTable(
        page,
        test.args.filterType,
        test.args.filterBy,
        test.args.filterValue,
      );

      const numberOfShoppingCartsAfterFilter = await shoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfShoppingCartsAfterFilter).to.be.at.most(numberOfShoppingCarts);

      for (let row = 1; row <= numberOfShoppingCartsAfterFilter; row++) {
        const textColumn = await shoppingCartsPage.getTextColumn(page, row, test.args.filterBy);

        if (test.args.filterBy === 'id_guest') {
          expect(textColumn).to.equal(test.args.filterValue === '1' ? 'Yes' : 'No');
        } else {
          expect(textColumn).to.contains(test.args.filterValue);
        }
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });
  });

  it('should filter by date \'From\' and \'To\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByDate', baseContext);

    // Filter by date
    await shoppingCartsPage.filterByDate(page, todayDate, todayDate);

    // Check number of element
    const numberOfShoppingCartsAfterFilter = await shoppingCartsPage.getNumberOfElementInGrid(page);
    expect(numberOfShoppingCartsAfterFilter).to.be.at.most(numberOfShoppingCarts);

    for (let row = 1; row <= numberOfShoppingCartsAfterFilter; row++) {
      const textColumn = await shoppingCartsPage.getTextColumn(page, row, 'date');
      expect(textColumn).to.contains(todayDate);
    }
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAfterFilterDate', baseContext);

    const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
    expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
  });
});
