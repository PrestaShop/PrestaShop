// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import createShoppingCart from '@commonTests/FO/classic/shoppingCart';
import {createCustomerTest, deleteCustomerTest} from '@commonTests/BO/customers/customer';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boShoppingCartsPage,
  type BrowserContext,
  dataProducts,
  dataShoppingCarts,
  FakerCustomer,
  FakerOrder,
  type Page,
  utilsCore,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_orders_shoppingCarts_filterShoppingCarts';

/*
Delete the non ordered shopping carts
Filter shopping carts By :
Id, order id, customer, carrier, date and online
*/
describe('BO - Orders - Shopping carts: Filter shopping cart table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfShoppingCarts: number;

  const dateToday: string = utilsDate.getDateFormat('mm/dd/yyyy');
  const dateTodayFilter: string = utilsDate.getDateFormat('yyyy-mm-dd');
  const dateTomorrowFilter: string = utilsDate.getDateFormat('yyyy-mm-dd', 'tomorrow');
  const dateFutureFilter: string = utilsDate.getDateFormat('yyyy-mm-dd', 'future');
  const customerData: FakerCustomer = new FakerCustomer();
  const orderData: FakerOrder = new FakerOrder({
    customer: customerData,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
  });

  // Pre-condition: Create customer
  createCustomerTest(customerData, `${baseContext}_preTest_0`);
  // Pre-condition: Create a non-ordered shopping cart being connected in the FO
  createShoppingCart(orderData, `${baseContext}_preTest_1`);

  describe('Filter the Shopping carts table', async () => {
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

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.shoppingCartsLink,
      );

      const pageTitle = await boShoppingCartsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShoppingCartsPage.pageTitle);
    });

    it('should reset all filters and get number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

      numberOfShoppingCarts = await boShoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCarts).to.be.above(0);
    });

    it('should filter by identifier with a not-existing ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterIdNonExisting', baseContext);

      await boShoppingCartsPage.filterTable(page, 'input', 'id_cart', '123456789');

      const numberOfShoppingCartsAfterFilter = await boShoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfShoppingCartsAfterFilter).to.equal(0);
    });

    it('should filter by identifier with a not-integer ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterIdNotInteger', baseContext);

      await boShoppingCartsPage.filterTable(page, 'input', 'id_cart', 'Hello');

      const numberOfShoppingCartsAfterFilter = await boShoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfShoppingCartsAfterFilter).to.equal(0);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterFilterNotExisting', baseContext);

      const numberOfShoppingCartsAfterReset = await boShoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });

    [
      {
        testIdentifier: 'filterId',
        filterType: 'input',
        filterBy: 'id_cart',
        filterValue: dataShoppingCarts[1].id.toString(),
      },
      {
        testIdentifier: 'filterOrderID',
        filterType: 'input',
        filterBy: 'id_order',
        filterValue: dataShoppingCarts[2].orderID.toString(),
      },
      {
        testIdentifier: 'filterStatus',
        filterType: 'select',
        filterBy: 'status',
        filterValue: dataShoppingCarts[2].status,
      },
      {
        testIdentifier: 'filterStatusNonOrdered',
        filterType: 'select',
        filterBy: 'status',
        filterValue: 'Non ordered',
      },
      {
        testIdentifier: 'filterCustomerFirstName',
        filterType: 'input',
        filterBy: 'customer_name',
        filterValue: dataShoppingCarts[3].customer.firstName.substring(0, 1),
      },
      {
        testIdentifier: 'filterCustomerLastName',
        filterType: 'input',
        filterBy: 'customer_name',
        filterValue: dataShoppingCarts[3].customer.lastName.substring(0, 3),
      },
      {
        testIdentifier: 'filterCarrier',
        filterType: 'input',
        filterBy: 'carrier_name',
        filterValue: dataShoppingCarts[0].carrier.name,
      },
      {
        testIdentifier: 'filterOnline',
        filterType: 'select',
        filterBy: 'customer_online',
        filterValue: dataShoppingCarts[4].online ? 'Yes' : 'No',
      },
    ].forEach((arg: {testIdentifier: string, filterType: string, filterBy: string, filterValue: string}) => {
      it(`should filter by ${arg.filterBy} '${arg.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', arg.testIdentifier, baseContext);

        await boShoppingCartsPage.filterTable(
          page,
          arg.filterType,
          arg.filterBy,
          arg.filterValue,
        );

        const numberOfShoppingCartsAfterFilter = await boShoppingCartsPage.getNumberOfElementInGrid(page);
        expect(numberOfShoppingCartsAfterFilter).to.be.at.most(numberOfShoppingCarts);

        for (let row = 1; row <= numberOfShoppingCartsAfterFilter; row++) {
          const textColumn = await boShoppingCartsPage.getTextColumn(page, row, arg.filterBy);

          if (arg.filterBy === 'id_guest') {
            expect(textColumn).to.equal(arg.filterValue === '1' ? 'Yes' : 'No');
          } else {
            expect(textColumn).to.contains(arg.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${arg.testIdentifier}Reset`, baseContext);

        const numberOfShoppingCartsAfterReset = await boShoppingCartsPage.resetAndGetNumberOfLines(page);
        expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
      });
    });

    it('should filter by date \'From\' and \'To\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDate', baseContext);

      // Filter by date
      await boShoppingCartsPage.filterByDate(page, dateTodayFilter, dateTodayFilter);

      // Check number of element
      const numberOfShoppingCartsAfterFilter = await boShoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfShoppingCartsAfterFilter).to.be.at.most(numberOfShoppingCarts);

      for (let row = 1; row <= numberOfShoppingCartsAfterFilter; row++) {
        const textColumn = await boShoppingCartsPage.getTextColumn(page, row, 'date_add');
        expect(textColumn).to.contains(dateToday);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterFilterDate', baseContext);

      const numberOfShoppingCartsAfterReset = await boShoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });

    it('should filter by date \'From\' and \'To\' in the future', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDateFuture', baseContext);

      // Filter by date
      await boShoppingCartsPage.filterByDate(page, dateTomorrowFilter, dateFutureFilter);

      // Check number of element
      const numberOfShoppingCartsAfterFilter = await boShoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfShoppingCartsAfterFilter).to.equals(0);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterFilterDateFuture', baseContext);

      const numberOfShoppingCartsAfterReset = await boShoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });

    [
      {
        testIdentifier: 'sortByIdDesc', sortBy: 'id_cart', sortDirection: 'desc', isFloat: true,
      },
      {
        testIdentifier: 'sortByOrderIDAsc', sortBy: 'status', sortDirection: 'asc', isFloat: true,
      },
      {
        testIdentifier: 'sortByOrderIDDesc', sortBy: 'status', sortDirection: 'desc', isFloat: true,
      },
      {
        testIdentifier: 'sortByStatusAsc', sortBy: 'status', sortDirection: 'asc',
      },
      {
        testIdentifier: 'sortByStatusDesc', sortBy: 'status', sortDirection: 'desc',
      },
      {
        testIdentifier: 'sortByCustomerAsc', sortBy: 'customer_name', sortDirection: 'asc',
      },
      {
        testIdentifier: 'sortByCustomerDesc', sortBy: 'customer_name', sortDirection: 'desc',
      },
      {
        testIdentifier: 'sortByCarrierAsc', sortBy: 'carrier_name', sortDirection: 'asc',
      },
      {
        testIdentifier: 'sortByCarrierDesc', sortBy: 'carrier_name', sortDirection: 'desc',
      },
      {
        testIdentifier: 'sortByDateAsc', sortBy: 'date_add', sortDirection: 'asc',
      },
      {
        testIdentifier: 'sortByDateDesc', sortBy: 'date_add', sortDirection: 'desc',
      },
      {
        testIdentifier: 'sortByOnlineAsc', sortBy: 'customer_online', sortDirection: 'asc',
      },
      {
        testIdentifier: 'sortByOnlineDesc', sortBy: 'customer_online', sortDirection: 'desc',
      },
      {
        testIdentifier: 'sortByIdAsc', sortBy: 'id_cart', sortDirection: 'asc', isFloat: true,
      },
    ].forEach((arg: {testIdentifier: string, sortBy: string, sortDirection: string, isFloat?: boolean}) => {
      it(`should sort by '${arg.sortBy}' '${arg.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', arg.testIdentifier, baseContext);

        const nonSortedTable = await boShoppingCartsPage.getAllRowsColumnContent(page, arg.sortBy);

        await boShoppingCartsPage.sortTable(page, arg.sortBy, arg.sortDirection);

        const sortedTable = await boShoppingCartsPage.getAllRowsColumnContent(page, arg.sortBy);

        if (arg.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text:string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text:string): number => parseFloat(text));

          const expectedResult = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (arg.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult: string[] = await utilsCore.sortArray(nonSortedTable);

          if (arg.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterSort', baseContext);

      const numberOfShoppingCartsAfterReset = await boShoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCartsAfterReset).to.be.above(1);
    });
  });

  // Post-condition: Delete guest account
  deleteCustomerTest(customerData, `${baseContext}_postTest_0`);
});
