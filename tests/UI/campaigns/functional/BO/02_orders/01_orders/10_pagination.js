require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const basicHelper = require('@utils/basicHelper');
const testContext = require('@utils/testContext');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');

// Import data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');

const baseContext = 'functional_BO_orders_orders_pagination';

let browserContext;
let page;
let numberOfOrders = 0;
let sortedTable = [];
let numberOfOrdersAfterFilter;

const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

/*
Pre-condition:
- Create 6 orders to have more than 10 orders
Scenario:
- Go to orders page
- Change items number par page to 10 and check number of pages
- Click on next and on previous
- Sort orders table by total desc
- Check the sort of the first page
- Click on next and check the sort of the second page
- Filter by customer name
- Check the filter on the first page
- Click on next and check the filter on the second page
 */
describe('BO - Orders : Pagination of orders table', async () => {
  // Pre-condition: Create 6 orders in FO
  const orderNumber = 6;
  for (let i = 1; i <= orderNumber; i++) {
    createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_${i}`);
  }

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Pagination next and previous', async () => {
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
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters and get number of orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

      numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemssNumberTo10', baseContext);

      const paginationNumber = await ordersPage.selectPaginationLimit(page, '10');
      expect(paginationNumber, `Number of pages is not correct (page 1 / ${Math.ceil(numberOfOrders / 10)})`)
        .to.contains(`(page 1 / ${Math.ceil(numberOfOrders / 10)})`);
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await ordersPage.paginationNext(page);
      expect(paginationNumber, `Number of pages is not (page 2 / ${Math.ceil(numberOfOrders / 10)})`)
        .to.contains(`(page 2 / ${Math.ceil(numberOfOrders / 10)})`);
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await ordersPage.paginationPrevious(page);
      expect(paginationNumber, `Number of pages is not (page 1 / ${Math.ceil(numberOfOrders / 10)})`)
        .to.contains(`(page 1 / ${Math.ceil(numberOfOrders / 10)})`);
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await ordersPage.selectPaginationLimit(page, '50');
      expect(paginationNumber, 'Number of pages is not correct').to.contains('(page 1 / 1)');
    });

    it('should sort orders by total desc', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sortOrdersDesc', baseContext);

      const nonSortedTable = await ordersPage.getAllRowsColumnContent(page, 'total_paid_tax_incl');

      await ordersPage.sortTable(page, 'total_paid_tax_incl', 'desc');

      sortedTable = await ordersPage.getAllRowsColumnContent(page, 'total_paid_tax_incl');

      const expectedResult = await basicHelper.sortArray(nonSortedTable, true);
      await expect(sortedTable).to.deep.equal(expectedResult.reverse());
    });

    it('should check that the orders table is sorted by total desc', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CheckSortDesc', baseContext);

      const allOrdersTable = await ordersPage.getAllRowsColumnContent(page, 'total_paid_tax_incl');
      await expect(allOrdersTable).to.deep.equal(sortedTable);
    });

    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo10', baseContext);

      const paginationNumber = await ordersPage.selectPaginationLimit(page, '10');
      expect(paginationNumber, `Number of pages is not correct (page 1 / ${Math.ceil(numberOfOrders / 10)})`)
        .to.contains(`(page 1 / ${Math.ceil(numberOfOrders / 10)})`);
    });

    it('should check that the first page is sorted by total desc', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstPageSortDesc', baseContext);

      const firstTable = await ordersPage.getAllRowsColumnContent(page, 'total_paid_tax_incl');
      await expect(firstTable).to.deep.equal(sortedTable.slice(0, 10));
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext2', baseContext);

      const paginationNumber = await ordersPage.paginationNext(page);
      expect(paginationNumber, `Number of pages is not (page 2 / ${Math.ceil(numberOfOrders / 10)})`)
        .to.contains(`(page 2 / ${Math.ceil(numberOfOrders / 10)})`);
    });

    it('should check that the second page is sorted by total desc', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondPageSortDesc', baseContext);

      const secondTable = await ordersPage.getAllRowsColumnContent(page, 'total_paid_tax_incl');
      const numberOfOrdersInPage = await ordersPage.getNumberOfOrdersInPage(page);

      await expect(secondTable).to.deep.equal(sortedTable.slice(10, 10 + numberOfOrdersInPage));
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50_2', baseContext);

      const paginationNumber = await ordersPage.selectPaginationLimit(page, '50');
      expect(paginationNumber, 'Number of pages is not correct').to.contains('(page 1 / 1)');
    });

    it('should go back to default sort', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackDefaultSort', baseContext);

      const nonSortedTable = await ordersPage.getAllRowsColumnContent(page, 'id_order');

      await ordersPage.sortTable(page, 'id_order', 'desc');

      sortedTable = await ordersPage.getAllRowsColumnContent(page, 'id_order');

      const expectedResult = await basicHelper.sortArray(nonSortedTable, true);
      await expect(sortedTable).to.deep.equal(expectedResult.reverse());
    });

    it('should filter by customer \'J.DOE\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', 'J. DOE');

      numberOfOrdersAfterFilter = await ordersPage.getNumberOfElementInGrid(page);
      await expect(numberOfOrdersAfterFilter).to.be.at.most(numberOfOrders);
    });

    it('should check that the orders table is filtered by customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CheckFilterByCustomer', baseContext);

      sortedTable = await ordersPage.getAllRowsColumnContent(page, 'customer');

      for (let row = 1; row <= numberOfOrdersAfterFilter; row++) {
        const textColumn = await ordersPage.getTextColumn(page, 'customer', row);
        await expect(textColumn).to.equal('J. DOE');
      }
    });

    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo10AfterFilter', baseContext);

      const paginationNumber = await ordersPage.selectPaginationLimit(page, '10');
      expect(paginationNumber, `Number of pages is not correct (page 1 / ${Math.ceil(numberOfOrders / 10)})`)
        .to.contains(`(page 1 / ${Math.ceil(numberOfOrders / 10)})`);
    });

    it('should check that the first page is filtered by Customer \'J.DOE\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFilterInFirstPage', baseContext);

      for (let row = 1; row <= 10; row++) {
        const textColumn = await ordersPage.getTextColumn(page, 'customer', row);
        await expect(textColumn).to.equal('J. DOE');
      }
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext3', baseContext);

      const paginationNumber = await ordersPage.paginationNext(page);
      expect(paginationNumber, `Number of pages is not (page 2 / ${Math.ceil(numberOfOrders / 10)})`)
        .to.contains(`(page 2 / ${Math.ceil(numberOfOrders / 10)})`);
    });

    it('should check that the second page is filtered by Customer \'J.DOE\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFilterInSecondPage', baseContext);

      const numberOfOrdersInPage = await ordersPage.getNumberOfOrdersInPage(page);

      for (let row = 1; row <= numberOfOrdersInPage; row++) {
        const textColumn = await ordersPage.getTextColumn(page, 'customer', row);
        await expect(textColumn).to.equal('J. DOE');
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfOrdersAfterReset = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrdersAfterReset).to.be.equal(numberOfOrders);
    });
  });
});
