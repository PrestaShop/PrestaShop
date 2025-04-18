import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boOrderMessagesPage,
  boOrderMessagesCreatePage,
  type BrowserContext,
  FakerOrderMessage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customerService_orderMessages_CRUDOrderMessage';

/*
Create order message
Update order message
Delete order message
 */
describe('BO - Customer Service - Order Messages : CRUD order message', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfOrderMessages: number = 0;

  const createOrderMessageData: FakerOrderMessage = new FakerOrderMessage();
  const editOrderMessageData: FakerOrderMessage = new FakerOrderMessage();

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

  it('should go to \'Customer Service > Order Messages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customerServiceParentLink,
      boDashboardPage.orderMessagesLink,
    );
    await boOrderMessagesPage.closeSfToolBar(page);

    const pageTitle = await boOrderMessagesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrderMessagesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfOrderMessages = await boOrderMessagesPage.resetAndGetNumberOfLines(page);
    expect(numberOfOrderMessages).to.be.above(0);
  });

  // 1: Create order message
  describe('Create order message', async () => {
    it('should go to new order message page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewOrderMessagePage', baseContext);

      await boOrderMessagesPage.goToAddNewOrderMessagePage(page);

      const pageTitle = await boOrderMessagesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrderMessagesCreatePage.pageTitle);
    });

    it('should create order message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrderMessage', baseContext);

      const result = await boOrderMessagesCreatePage.addEditOrderMessage(page, createOrderMessageData);
      expect(result).to.equal(boOrderMessagesPage.successfulCreationMessage);
    });

    it('should reset filters and check number of order messages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterCreate', baseContext);

      const numberOfOrderMessagesAfterReset = await boOrderMessagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrderMessagesAfterReset).to.be.equal(numberOfOrderMessages + 1);
    });
  });

  // 2: Update order message
  describe('Update order message', async () => {
    it(`should filter by name '${createOrderMessageData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      await boOrderMessagesPage.filterTable(page, 'name', createOrderMessageData.name);

      const numberOfOrderMessagesAfterFilter = await boOrderMessagesPage.getNumberOfElementInGrid(page);
      expect(numberOfOrderMessagesAfterFilter).to.be.at.most(numberOfOrderMessages + 1);

      const textColumn = await boOrderMessagesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textColumn).to.contains(createOrderMessageData.name);
    });

    it('should go to edit first order message page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

      await boOrderMessagesPage.gotoEditOrderMessage(page, 1);

      const pageTitle = await boOrderMessagesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrderMessagesCreatePage.pageTitleEdit);
    });

    it('should edit order message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderMessage', baseContext);

      const result = await boOrderMessagesCreatePage.addEditOrderMessage(page, editOrderMessageData);
      expect(result).to.equal(boOrderMessagesPage.successfulUpdateMessage);
    });

    it('should reset filters and check number of order messages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterUpdate', baseContext);

      const numberOfOrderMessagesAfterReset = await boOrderMessagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrderMessagesAfterReset).to.be.equal(numberOfOrderMessages + 1);
    });
  });

  // 3: Delete order message
  describe('Delete order message', async () => {
    it(`should filter by name '${editOrderMessageData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boOrderMessagesPage.filterTable(page, 'name', editOrderMessageData.name);

      const numberOfOrderMessagesAfterFilter = await boOrderMessagesPage.getNumberOfElementInGrid(page);
      expect(numberOfOrderMessagesAfterFilter).to.be.at.most(numberOfOrderMessages + 1);

      const textColumn = await boOrderMessagesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textColumn).to.contains(editOrderMessageData.name);
    });

    it('should delete order message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteOrderMessage', baseContext);

      // delete order message in first row
      const result = await boOrderMessagesPage.deleteOrderMessage(page, 1);
      expect(result).to.be.equal(boOrderMessagesPage.successfulDeleteMessage);
    });

    it('should reset filters and check number of order messages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfOrderMessagesAfterReset = await boOrderMessagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrderMessagesAfterReset).to.be.equal(numberOfOrderMessages);
    });
  });
});
