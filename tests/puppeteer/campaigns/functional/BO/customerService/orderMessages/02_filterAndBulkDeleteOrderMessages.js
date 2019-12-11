require('module-alias/register');
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

let browser;
let page;
const firstOrderMessageData = new OrderMessageFaker({name: 'todelete'});
const secondOrderMessageData = new OrderMessageFaker({name: 'todelete2'});
let numberOfOrderMessages = 0;

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
Create 2 order messages
Filter by name and message and check result
Delete order messages with bulk actions
 */
describe('Create order messages, check filter results and delete them with bulk actions', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to order messages page
  loginCommon.loginBO();

  it('should go to order messages page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.customerServiceParentLink,
      this.pageObjects.boBasePage.orderMessagesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.orderMessagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.orderMessagesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    numberOfOrderMessages = await this.pageObjects.orderMessagesPage.resetAndGetNumberOfLines();
    await expect(numberOfOrderMessages).to.be.above(0);
  });

  // 1: Create 2 order message
  describe('Create 2 order messages', async () => {
    const tests = [
      {args: {orderMessageToCreate: firstOrderMessageData}},
      {args: {orderMessageToCreate: secondOrderMessageData}},
    ];
    tests.forEach((test, index) => {
      it('should go to new order message page', async function () {
        await this.pageObjects.orderMessagesPage.goToAddNewOrderMessagePage();
        const pageTitle = await this.pageObjects.addOrderMessagePage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addOrderMessagePage.pageTitle);
      });

      it('should create order message', async function () {
        const result = await this.pageObjects.addOrderMessagePage.AddEditOrderMessage(test.args.orderMessageToCreate);
        await expect(result).to.equal(this.pageObjects.orderMessagesPage.successfulCreationMessage);
        const numberOfOrderMessagesAfterCreation = await this.pageObjects.orderMessagesPage.getNumberOfElementInGrid();
        await expect(numberOfOrderMessagesAfterCreation).to.be.equal(numberOfOrderMessages + index + 1);
      });
    });
  });
  // 2: filter order Messages
  describe('Filter order Messages', async () => {
    const tests = [
      {args: {filterBy: 'name', filterValue: secondOrderMessageData.name}},
      {args: {filterBy: 'message', filterValue: secondOrderMessageData.message}},
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await this.pageObjects.orderMessagesPage.filterTable('name', test.args.filterValue);
        const numberOfOrderMessagesAfterFilter = await this.pageObjects.orderMessagesPage.getNumberOfElementInGrid();
        await expect(numberOfOrderMessagesAfterFilter).to.be.at.most(numberOfOrderMessages + 1);
        const textColumn = await this.pageObjects.orderMessagesPage.getTextColumnFromTable(1, test.args.filterBy);
        await expect(textColumn).to.contains(test.args.filterValue);
      });

      it('should reset filters and check number of order messages', async function () {
        const numberOfOrderMessagesAfterReset = await this.pageObjects.orderMessagesPage.resetAndGetNumberOfLines();
        await expect(numberOfOrderMessagesAfterReset).to.be.equal(numberOfOrderMessages + 2);
      });
    });
  });

  // 3: Delete order messages with bulk actions
  describe('Delete order messages with bulk actions', async () => {
    it('should filter by name of order message', async function () {
      await this.pageObjects.orderMessagesPage.filterTable('name', 'todelete');
      const textColumn = await this.pageObjects.orderMessagesPage.getTextColumnFromTable(1, 'name');
      await expect(textColumn).to.contains('todelete');
    });

    it('should delete order messages', async function () {
      const result = await this.pageObjects.orderMessagesPage.deleteWithBulkActions();
      await expect(result).to.be.equal(this.pageObjects.orderMessagesPage.successfulMultiDeleteMessage);
    });

    it('should reset filters and check number of order messages', async function () {
      const numberOfOrderMessagesAfterReset = await this.pageObjects.orderMessagesPage.resetAndGetNumberOfLines();
      await expect(numberOfOrderMessagesAfterReset).to.be.equal(numberOfOrderMessages);
    });
  });
});
