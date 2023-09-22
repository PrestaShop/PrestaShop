// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import orderMessagesPage from '@pages/BO/customerService/orderMessages';
import addOrderMessagePage from '@pages/BO/customerService/orderMessages/add';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import OrderMessageData from '@data/faker/orderMessage';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customerService_orderMessages_filterAndBulkDeleteOrderMessages';

/*
Create 2 order messages
Filter by name and message and check result
Delete order messages with bulk actions
 */
describe('BO - Customer Service - Order Messages : Filter and bulk delete order messages', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfOrderMessages: number = 0;

  const firstOrderMessageData: OrderMessageData = new OrderMessageData({name: 'todelete'});
  const secondOrderMessageData: OrderMessageData = new OrderMessageData({name: 'todelete2'});

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
    expect(pageTitle).to.contains(orderMessagesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfOrderMessages = await orderMessagesPage.resetAndGetNumberOfLines(page);
    expect(numberOfOrderMessages).to.be.above(0);
  });

  // 1: Create 2 order message
  describe('Create 2 order messages', async () => {
    [
      {args: {orderMessageToCreate: firstOrderMessageData}},
      {args: {orderMessageToCreate: secondOrderMessageData}},
    ].forEach((test, index: number) => {
      it('should go to new order message page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewOrderMessagePage${index + 1}`, baseContext);

        await orderMessagesPage.goToAddNewOrderMessagePage(page);

        const pageTitle = await addOrderMessagePage.getPageTitle(page);
        expect(pageTitle).to.contains(addOrderMessagePage.pageTitle);
      });

      it('should create order message', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createOrderMessage${index + 1}`, baseContext);

        const result = await addOrderMessagePage.addEditOrderMessage(page, test.args.orderMessageToCreate);
        expect(result).to.equal(orderMessagesPage.successfulCreationMessage);

        const numberOfOrderMessagesAfterCreation = await orderMessagesPage.getNumberOfElementInGrid(page);
        expect(numberOfOrderMessagesAfterCreation).to.be.equal(numberOfOrderMessages + index + 1);
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
        expect(numberOfOrderMessagesAfterFilter).to.be.at.most(numberOfOrderMessages + 1);

        const textColumn = await orderMessagesPage.getTextColumnFromTable(page, 1, test.args.filterBy);
        expect(textColumn).to.contains(test.args.filterValue);
      });

      it('should reset filters and check number of order messages', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfOrderMessagesAfterReset = await orderMessagesPage.resetAndGetNumberOfLines(page);
        expect(numberOfOrderMessagesAfterReset).to.be.equal(numberOfOrderMessages + 2);
      });
    });
  });

  // 3: Delete order messages with bulk actions
  describe('Delete order messages with bulk actions', async () => {
    it(`should filter by name '${firstOrderMessageData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await orderMessagesPage.filterTable(page, 'name', firstOrderMessageData.name);

      const textColumn = await orderMessagesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textColumn).to.contains(firstOrderMessageData.name);
    });

    it('should delete order messages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const result = await orderMessagesPage.deleteWithBulkActions(page);
      expect(result).to.be.equal(orderMessagesPage.successfulMultiDeleteMessage);
    });

    it('should reset filters and check number of order messages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfOrderMessagesAfterReset = await orderMessagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrderMessagesAfterReset).to.be.equal(numberOfOrderMessages);
    });
  });
});
