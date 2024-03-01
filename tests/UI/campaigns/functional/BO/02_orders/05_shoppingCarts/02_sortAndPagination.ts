// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {bulkDeleteCustomersTest} from '@commonTests/BO/customers/customer';
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByGuestTest} from '@commonTests/FO/classic/order';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import shoppingCartsPage from '@pages/BO/orders/shoppingCarts';

// Import data
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';
import OrderData from '@data/faker/order';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_shoppingCarts_sortAndPagination';

/*
Pre-condition:
- Create 16 shopping carts by guest
Scenario:
- Pagination
- Sort shopping cart table by Id, Order ID, Customer, carrier, date and Online
Post-condition:
- Delete customers
*/
describe('BO - Orders - Shopping carts : Sort and pagination shopping carts', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const addressData: AddressData = new AddressData({country: 'France'});
  const customerData: CustomerData = new CustomerData({password: '', lastName: 'guest'});
  // New order by guest data
  const orderByGuestData: OrderData = new OrderData({
    customer: customerData,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    deliveryAddress: addressData,
    paymentMethod: PaymentMethods.wirePayment,
  });

  // Pre-condition: Create 16 orders
  describe('PRE-TEST: Create 16 orders by guest in FO', async () => {
    const creationTests: number[] = new Array(16).fill(0, 0, 16);
    creationTests.forEach((value: number, index: number) => {
      createOrderByGuestTest(orderByGuestData, `${baseContext}_preTest_${index}`);
    });
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // 1 - Pagination
  describe('Pagination next and previous', async () => {
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

    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await shoppingCartsPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await shoppingCartsPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await shoppingCartsPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 300 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo300', baseContext);

      const paginationNumber = await shoppingCartsPage.selectPaginationLimit(page, 100);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 2 - Sort shopping cart table
  describe('Sort shopping cart table', async () => {
    it('should filter by customer lastName start by \'guest\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToSort', baseContext);

      await shoppingCartsPage.filterTable(page, 'input', 'customer_name', 'guest');

      const textColumn = await shoppingCartsPage.getTextColumn(page, 1, 'customer_name');
      expect(textColumn).to.contains(customerData.lastName);
    });

    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_cart', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByOrderIDAsc', sortBy: 'status', sortDirection: 'asc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByOrderIDDesc', sortBy: 'status', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByCarrierAsc', sortBy: 'carrier_name', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCarrierDesc', sortBy: 'carrier_name', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByDateAsc', sortBy: 'date_add', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByDateDesc', sortBy: 'date_add', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByOnlineAsc', sortBy: 'customer_online', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByOnlineDesc', sortBy: 'customer_online', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_cart', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await shoppingCartsPage.getAllRowsColumnContent(page, test.args.sortBy);

        await shoppingCartsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await shoppingCartsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text:string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text:string): number => parseFloat(text));

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

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterSort', baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCartsAfterReset).to.be.above(1);
    });
  });

  // Post-condition: Delete created guest customers by bulk action
  bulkDeleteCustomersTest('email', customerData.email, `${baseContext}_postTest_1`);
});
