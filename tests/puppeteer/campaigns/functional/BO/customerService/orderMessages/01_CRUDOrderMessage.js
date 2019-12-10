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
let createOrderMessageData;
let editOrderMessageData;
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
Create order message
Update order message
Delete order message
 */
describe('Create, update and delete order message', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    createOrderMessageData = await (new OrderMessageFaker());
    editOrderMessageData = await (new OrderMessageFaker());
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

  // 1: Create order message
  describe('Create order message', async () => {
    it('should go to new order message page', async function () {
      await this.pageObjects.orderMessagesPage.goToAddNewOrderMessagePage();
      const pageTitle = await this.pageObjects.addOrderMessagePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addOrderMessagePage.pageTitle);
    });

    it('should create order message', async function () {
      const result = await this.pageObjects.addOrderMessagePage.AddEditOrderMessage(createOrderMessageData);
      await expect(result).to.equal(this.pageObjects.orderMessagesPage.successfulCreationMessage);
    });

    it('should reset filters and check number of order messages', async function () {
      const numberOfOrderMessagesAfterReset = await this.pageObjects.orderMessagesPage.resetAndGetNumberOfLines();
      await expect(numberOfOrderMessagesAfterReset).to.be.equal(numberOfOrderMessages + 1);
    });
  });
  // 2: Update order message
  describe('Update order message', async () => {
    it('should filter by name of order message', async function () {
      await this.pageObjects.orderMessagesPage.filterTable('name', createOrderMessageData.name);
      const numberOfOrderMessagesAfterFilter = await this.pageObjects.orderMessagesPage.getNumberOfElementInGrid();
      await expect(numberOfOrderMessagesAfterFilter).to.be.at.most(numberOfOrderMessages + 1);
      const textColumn = await this.pageObjects.orderMessagesPage.getTextColumnFromTable(1, 'name');
      await expect(textColumn).to.contains(createOrderMessageData.name);
    });

    it('should go to edit first order message page', async function () {
      await this.pageObjects.orderMessagesPage.gotoEditOrderMessage(1);
      const pageTitle = await this.pageObjects.addOrderMessagePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addOrderMessagePage.pageTitleEdit);
    });

    it('should edit order message', async function () {
      const result = await this.pageObjects.addOrderMessagePage.AddEditOrderMessage(editOrderMessageData);
      await expect(result).to.equal(this.pageObjects.orderMessagesPage.successfulUpdateMessage);
    });

    it('should reset filters and check number of order messages', async function () {
      const numberOfOrderMessagesAfterReset = await this.pageObjects.orderMessagesPage.resetAndGetNumberOfLines();
      await expect(numberOfOrderMessagesAfterReset).to.be.equal(numberOfOrderMessages + 1);
    });
  });
  // 3: Delete order message
  describe('Delete order message', async () => {
    it('should filter by name of order message', async function () {
      await this.pageObjects.orderMessagesPage.filterTable('name', editOrderMessageData.name);
      const numberOfOrderMessagesAfterFilter = await this.pageObjects.orderMessagesPage.getNumberOfElementInGrid();
      await expect(numberOfOrderMessagesAfterFilter).to.be.at.most(numberOfOrderMessages + 1);
      const textColumn = await this.pageObjects.orderMessagesPage.getTextColumnFromTable(1, 'name');
      await expect(textColumn).to.contains(editOrderMessageData.name);
    });

    it('should delete order message', async function () {
      // delete order message in first row
      const result = await this.pageObjects.orderMessagesPage.deleteOrderMessage(1);
      await expect(result).to.be.equal(this.pageObjects.orderMessagesPage.successfulDeleteMessage);
    });

    it('should reset filters and check number of order messages', async function () {
      const numberOfOrderMessagesAfterReset = await this.pageObjects.orderMessagesPage.resetAndGetNumberOfLines();
      await expect(numberOfOrderMessagesAfterReset).to.be.equal(numberOfOrderMessages);
    });
  });
});
