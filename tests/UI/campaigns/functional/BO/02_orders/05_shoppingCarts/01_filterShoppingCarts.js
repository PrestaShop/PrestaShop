require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const shoppingCartsPage = require('@pages/BO/orders/shoppingCarts');

// Import data
const {ShoppingCarts} = require('@data/demo/shoppingCarts');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_shoppingCarts_filterShoppingCarts';

// Import expect from chai
const {expect} = require('chai');

let numberOfShoppingCarts;
let browserContext;
let page;

// Today date
const today = new Date();

// Current day
const day = (`0${today.getDate()}`).slice(-2);

// Current month
const month = (`0${today.getMonth() + 1}`).slice(-2);

// Current year
const year = today.getFullYear();

// Date today format (yyy/mm/dd)
const todayDate = `${day}/${month}/${year}`;

/*
Filter shopping carts By :
Id, order id, customer, carrier, date and online
*/
describe('Filter the Shopping carts table', async () => {
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

  it('should go to \'Orders/Shopping carts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.ordersParentLink,
      dashboardPage.shoppingCartsLink,
    );

    const pageTitle = await shoppingCartsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
  });

  it('should reset all filters and get number of shopping carts', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

    numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfShoppingCarts).to.be.above(0);
  });

  const tests = [
    {
      args:
        {
          testIdentifier: 'filterId',
          filterType: 'input',
          filterBy: 'id_cart',
          filterValue: ShoppingCarts[1].id,
        },
    },
    {
      args:
        {
          testIdentifier: 'filterOrderID',
          filterType: 'input',
          filterBy: 'status',
          filterValue: ShoppingCarts[2].orderID,
        },
    },
    {
      args:
        {
          testIdentifier: 'filterCarrier',
          filterType: 'input',
          filterBy: 'c!lastname',
          filterValue: ShoppingCarts[3].customer,
        },
    },
    {
      args:
        {
          testIdentifier: 'filterDate',
          filterType: 'input',
          filterBy: 'ca!name',
          filterValue: ShoppingCarts[0].carrier,
        },
    },
    {
      args:
        {
          testIdentifier: 'filterOnline',
          filterType: 'select',
          filterBy: 'id_guest',
          filterValue: ShoppingCarts[4].online,
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
      await expect(numberOfShoppingCartsAfterFilter).to.be.at.most(numberOfShoppingCarts);

      for (let row = 1; row <= numberOfShoppingCartsAfterFilter; row++) {
        const textColumn = await shoppingCartsPage.getTextColumn(
          page,
          row,
          test.args.filterBy,
        );

        if (typeof test.args.filterValue === 'boolean') {
          await expect(textColumn).to.equal(test.args.filterValue ? 'Yes' : 'No');
        } else {
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });
  });

  it('should filter by date \'From\' and \'To\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByDate', baseContext);

    // Filter by date
    await shoppingCartsPage.filterByDate(page, todayDate, todayDate);

    // Check number of element
    const numberOfShoppingCartsAfterFilter = await shoppingCartsPage.getNumberOfElementInGrid(page);
    await expect(numberOfShoppingCartsAfterFilter).to.be.at.most(numberOfShoppingCarts);

    for (let row = 1; row <= numberOfShoppingCartsAfterFilter; row++) {
      const textColumn = await shoppingCartsPage.getTextColumn(
        page,
        row,
        test.args.filterBy,
      );
      await expect(textColumn).to.contains(today);
    }
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAfterFilterDate', baseContext);

    const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
  });
});
