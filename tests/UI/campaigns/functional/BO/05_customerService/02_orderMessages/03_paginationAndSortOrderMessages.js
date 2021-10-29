require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import data
const OrderMessageFaker = require('@data/faker/orderMessage');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const orderMessagesPage = require('@pages/BO/customerService/orderMessages');
const addOrderMessagePage = require('@pages/BO/customerService/orderMessages/add');

const baseContext = 'functional_BO_customerService_orderMessages_paginationAndSortOrderMessages';

let browserContext;
let page;
let numberOfOrderMessages = 0;

/*
Create 11 order messages
Paginate between pages
Sort order messages table
Delete order messages with bulk actions
 */
describe('BO - Customer Service - Order Messages : Pagination and sort order messages', async () => {
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

  it('should go to \'Customer Message > Order Messages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.customerServiceParentLink,
      dashboardPage.orderMessagesLink,
    );

    await dashboardPage.closeSfToolBar(page);

    const pageTitle = await orderMessagesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(orderMessagesPage.pageTitle);
  });

  it('should reset all filters and get number of order messages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfOrderMessages = await orderMessagesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfOrderMessages).to.be.above(0);
  });

  describe('Create 10 order messages in BO', async () => {
    const tests = new Array(10).fill(0, 0, 10);
    tests.forEach((test, index) => {
      const createOrderMessageData = new OrderMessageFaker({name: `toSortAndPaginate${index}`});

      it('should go to add new order message page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewOrderMessagePage${index}`, baseContext);

        await orderMessagesPage.goToAddNewOrderMessagePage(page);
        const pageTitle = await addOrderMessagePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addOrderMessagePage.pageTitle);
      });

      it(`should create order message n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createOrderMessage${index}`, baseContext);

        const textResult = await addOrderMessagePage.addEditOrderMessage(page, createOrderMessageData);
        await expect(textResult).to.equal(orderMessagesPage.successfulCreationMessage);
      });

      it('should check the order messages number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkOrderMessageNumber${index}`, baseContext);

        const numberOfOrderMessagesAfterCreation = await orderMessagesPage.getNumberOfElementInGrid(page);
        await expect(numberOfOrderMessagesAfterCreation).to.be.equal(numberOfOrderMessages + 1 + index);
      });
    });
  });

  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo10', baseContext);

      const paginationNumber = await orderMessagesPage.selectPaginationLimit(page, '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await orderMessagesPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await orderMessagesPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await orderMessagesPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  describe('Sort order messages table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIDDesc', sortBy: 'id_order_message', sortDirection: 'desc', isFloat: true,
        },
      },
      {args: {testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByMessageDesc', sortBy: 'message', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByMessageAsc', sortBy: 'message', sortDirection: 'asc'}},
      {
        args: {
          testIdentifier: 'sortByIDAsc', sortBy: 'id_order_message', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await orderMessagesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await orderMessagesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await orderMessagesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await orderMessagesPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  describe('Delete order messages with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await orderMessagesPage.filterTable(page, 'name', 'toSortAndPaginate');

      const textResult = await orderMessagesPage.getTextColumnFromTable(page, 1, 'name');
      await expect(textResult).to.contains('toSortAndPaginate');
    });

    it('should delete order messages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);

      const deleteTextResult = await orderMessagesPage.deleteWithBulkActions(page);
      await expect(deleteTextResult).to.be.equal(orderMessagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfOrderMessagesAfterFilter = await orderMessagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrderMessagesAfterFilter).to.be.equal(numberOfOrderMessages);
    });
  });
});
