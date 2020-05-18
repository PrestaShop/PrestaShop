require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const OrderMessageFaker = require('@data/faker/orderMessage');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const OrderMessagesPage = require('@pages/BO/customerService/orderMessages');
const AddOrderMessagePage = require('@pages/BO/customerService/orderMessages/add');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customerService_orderMessages_pagination';

let browser;
let page;
let numberOfOrderMessages = 0;
const createOrderMessageData = new OrderMessageFaker();

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    orderMessagesPage: new OrderMessagesPage(page),
    addOrderMessagePage: new AddOrderMessagePage(page),
  };
};

/*
Create 11 order messages
Paginate between pages
Delete order messages with bulk actions
 */
describe('Order messages pagination', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO
  loginCommon.loginBO();

  it('should go to order messages page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.customerServiceParentLink,
      this.pageObjects.boBasePage.orderMessagesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.orderMessagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.orderMessagesPage.pageTitle);
  });

  it('should reset all filters and get number of order messages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);
    numberOfOrderMessages = await this.pageObjects.orderMessagesPage.resetAndGetNumberOfLines();
    if (numberOfOrderMessages !== 0) await expect(numberOfOrderMessages).to.be.above(0);
  });

  const tests = new Array(10).fill(0, 0, 10);
  tests.forEach((test, index) => {
    describe(`Create order message n°${index + 1} in BO`, async () => {
      it('should go to add new order message page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewOrderMessagePage${index}`, baseContext);
        await this.pageObjects.orderMessagesPage.goToAddNewOrderMessagePage();
        const pageTitle = await this.pageObjects.addOrderMessagePage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addOrderMessagePage.pageTitle);
      });

      it('should create order message', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createOrderMessage${index}`, baseContext);
        const textResult = await this.pageObjects.addOrderMessagePage.addEditOrderMessage(createOrderMessageData);
        await expect(textResult).to.equal(this.pageObjects.orderMessagesPage.successfulCreationMessage);
      });

      it('should check the order messages number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkOrderMessageNumber${index}`, baseContext);
        const numberOfOrderMessagesAfterCreation = await this.pageObjects.orderMessagesPage.getNumberOfElementInGrid();
        await expect(numberOfOrderMessagesAfterCreation).to.be.equal(numberOfOrderMessages + 1 + index);
      });
    });
  });

  describe('Pagination next and previous', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);
      const paginationNumber = await this.pageObjects.orderMessagesPage.selectPaginationLimit('10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);
      const paginationNumber = await this.pageObjects.orderMessagesPage.paginationNext();
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);
      const paginationNumber = await this.pageObjects.orderMessagesPage.paginationPrevious();
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);
      const paginationNumber = await this.pageObjects.orderMessagesPage.selectPaginationLimit('50');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  describe('Delete order messages with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);
      await this.pageObjects.orderMessagesPage.filterTable('name', createOrderMessageData.name);
      const textResult = await this.pageObjects.orderMessagesPage.getTextColumnFromTable(1, 'name');
      await expect(textResult).to.contains(createOrderMessageData.name);
    });

    it('should delete order messages with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);
      const deleteTextResult = await this.pageObjects.orderMessagesPage.deleteWithBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.orderMessagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);
      const numberOfOrderMessagesAfterFilter = await this.pageObjects.orderMessagesPage.resetAndGetNumberOfLines();
      await expect(numberOfOrderMessagesAfterFilter).to.be.equal(numberOfOrderMessages);
    });
  });
});
