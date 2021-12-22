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

const baseContext = 'functional_BO_customerService_orderMessages_filterAndBulkDeleteOrderMessages';

let browserContext;
let page;

const firstOrderMessageData = new OrderMessageFaker({name: 'todelete'});
const secondOrderMessageData = new OrderMessageFaker({name: 'todelete2'});

let numberOfOrderMessages = 0;

/*
Create 2 order messages
Filter by name and message and check result
Delete order messages with bulk actions
 */
describe('BO - Customer Service - Order Messages : Filter and bulk delete order messages', async () => {
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

    await orderMessagesPage.closeSfToolBar(page);

    const pageTitle = await orderMessagesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(orderMessagesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfOrderMessages = await orderMessagesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfOrderMessages).to.be.above(0);
  });

  // 1: Create 2 order message
  describe('Create 2 order messages', async () => {
    [
      {args: {orderMessageToCreate: firstOrderMessageData}},
      {args: {orderMessageToCreate: secondOrderMessageData}},
    ].forEach((test, index) => {
      it('should go to new order message page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewOrderMessagePage${index + 1}`, baseContext);

        await orderMessagesPage.goToAddNewOrderMessagePage(page);
        const pageTitle = await addOrderMessagePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addOrderMessagePage.pageTitle);
      });

      it('should create order message', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createOrderMessage${index + 1}`, baseContext);

        const result = await addOrderMessagePage.addEditOrderMessage(page, test.args.orderMessageToCreate);
        await expect(result).to.equal(orderMessagesPage.successfulCreationMessage);

        const numberOfOrderMessagesAfterCreation = await orderMessagesPage.getNumberOfElementInGrid(page);
        await expect(numberOfOrderMessagesAfterCreation).to.be.equal(numberOfOrderMessages + index + 1);
      });
    });
  });

  // 2: filter order Messages
  describe('Filter order messages table', async () => {
    [
      {args: {testIdentifier: 'filterName', filterBy: 'name', filterValue: secondOrderMessageData.name}},
      {args: {testIdentifier: 'filterMessage', filterBy: 'message', filterValue: secondOrderMessageData.message}},
    ].forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        await orderMessagesPage.filterTable(page, test.args.filterBy, test.args.filterValue);

        const numberOfOrderMessagesAfterFilter = await orderMessagesPage.getNumberOfElementInGrid(page);
        await expect(numberOfOrderMessagesAfterFilter).to.be.at.most(numberOfOrderMessages + 1);

        const textColumn = await orderMessagesPage.getTextColumnFromTable(page, 1, test.args.filterBy);
        await expect(textColumn).to.contains(test.args.filterValue);
      });

      it('should reset filters and check number of order messages', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfOrderMessagesAfterReset = await orderMessagesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfOrderMessagesAfterReset).to.be.equal(numberOfOrderMessages + 2);
      });
    });
  });

  // 3: Delete order messages with bulk actions
  describe('Delete order messages with bulk actions', async () => {
    it(`should filter by name '${firstOrderMessageData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await orderMessagesPage.filterTable(page, 'name', firstOrderMessageData.name);

      const textColumn = await orderMessagesPage.getTextColumnFromTable(page, 1, 'name');
      await expect(textColumn).to.contains(firstOrderMessageData.name);
    });

    it('should delete order messages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const result = await orderMessagesPage.deleteWithBulkActions(page);
      await expect(result).to.be.equal(orderMessagesPage.successfulMultiDeleteMessage);
    });

    it('should reset filters and check number of order messages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfOrderMessagesAfterReset = await orderMessagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrderMessagesAfterReset).to.be.equal(numberOfOrderMessages);
    });
  });
});
