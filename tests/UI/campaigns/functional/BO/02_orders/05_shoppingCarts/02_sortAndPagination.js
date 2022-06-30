require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const basicHelper = require('@utils/basicHelper');

// Common tests login BO
const loginCommon = require('@commonTests/BO/loginBO');
const {createOrderByGuestTest} = require('@commonTests/FO/createOrder');
const {bulkDeleteCustomersTest} = require('@commonTests/BO/customers/createDeleteCustomer');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const shoppingCartsPage = require('@pages/BO/orders/shoppingCarts');

// Import faker data
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');

// Import demo data
const {PaymentMethods} = require('@data/demo/paymentMethods');

const addressData = new AddressFaker({country: 'France'});
const customerData = new CustomerFaker({password: '', lastName: 'guest'});

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_shoppingCarts_sortAndPagination';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

// New order by guest data
const orderByGuestData = {
  customer: customerData,
  product: 1,
  productQuantity: 1,
  address: addressData,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

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
  // Pre-condition: Create 16 orders
  describe('PRE-TEST: Create 16 orders by guest in FO', async () => {
    const creationTests = new Array(16).fill(0, 0, 16);
    creationTests.forEach((value, index) => {
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
      await expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
    });

    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await shoppingCartsPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await shoppingCartsPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await shoppingCartsPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 300 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo300', baseContext);

      const paginationNumber = await shoppingCartsPage.selectPaginationLimit(page, '300');
      expect(paginationNumber).to.equal('1');
    });
  });

  // 2 - Sort shopping cart table
  describe('Sort shopping cart table', async () => {
    it('should filter by customer lastName start by \'guest\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToSort', baseContext);

      await shoppingCartsPage.filterTable(page, 'input', 'c!lastname', 'guest');

      const textColumn = await shoppingCartsPage.getTextColumn(page, 1, 'c!lastname');
      await expect(textColumn).to.contains(customerData.lastName);
    });

    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_cart', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByOrderIDAsc', sortBy: 'status', sortDirection: 'up', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByOrderIDDesc', sortBy: 'status', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByCarrierAsc', sortBy: 'ca!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCarrierDesc', sortBy: 'ca!name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByDateAsc', sortBy: 'date', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByDateDesc', sortBy: 'date', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByOnlineAsc', sortBy: 'id_guest', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByOnlineDesc', sortBy: 'id_guest', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_cart', sortDirection: 'up', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await shoppingCartsPage.getAllRowsColumnContent(page, test.args.sortBy);

        await shoppingCartsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await shoppingCartsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await basicHelper.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'up') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterSort', baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCartsAfterReset).to.be.above(1);
    });
  });

  // Post-condition: Delete created guest customers by bulk action
  bulkDeleteCustomersTest('email', customerData.email, `${baseContext}_postTest_1`);
});
