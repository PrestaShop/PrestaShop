// Using chai
const {expect} = require('chai');
const helper = require('../../utils/helpers');
const loginCommon = require('../../commonTests/loginBO');

// importing pages
const LoginPage = require('../../../pages/BO/login');
const DashboardPage = require('../../../pages/BO/dashboard');
const BOBasePage = require('../../../pages/BO/BObasePage');
const OrderPage = require('../../../pages/BO/order');
const {Orders, Statuses} = require('../../data/demo/orders');

let browser;
let page;
// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    boBasePage: new BOBasePage(page),
    orderPage: new OrderPage(page),
  };
};

/*
  Connect to the BO
  Filter the Orders table
  Logout from the BO
 */
describe('Filter the Orders table by ID, REFERENCE, STATUS', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await browser.close();
  });
  // Steps
  loginCommon.loginBO();
  it('should go to the Orders page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.ordersParentLink,
      this.pageObjects.orderPage.ordersLink,
    );
    const pageTitle = await this.pageObjects.orderPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.orderPage.pageTitle);
  });
  it('should filter the Orders table by ID and check the result', async function () {
    await this.pageObjects.orderPage.filterTableByInput(
      this.pageObjects.orderPage.orderFilterIdInput,
      Orders.firstOrder.id,
      this.pageObjects.orderPage.searchButton,
    );
    const result = await this.pageObjects.boBasePage.checkTextValue(
      this.pageObjects.orderPage.orderfirstLineIdTD,
      Orders.firstOrder.id,
    );
    await expect(result).to.be.true;
    await this.pageObjects.orderPage.waitForSelectorAndClick(this.pageObjects.orderPage.resetButton);
  });
  it('should filter the Orders table by REFERENCE and check the result', async function () {
    await this.pageObjects.orderPage.filterTableByInput(
      this.pageObjects.orderPage.orderFilterReferenceInput,
      Orders.fourthOrder.ref,
      this.pageObjects.orderPage.searchButton,
    );
    const result = await this.pageObjects.boBasePage.checkTextValue(
      this.pageObjects.orderPage.orderfirstLineReferenceTD,
      Orders.fourthOrder.ref,
    );
    await expect(result).to.be.true;
    await this.pageObjects.orderPage.waitForSelectorAndClick(this.pageObjects.orderPage.resetButton);
  });
  it('should filter the Orders table by STATUS and check the result', async function () {
    await this.pageObjects.orderPage.filterTableBySelect(
      this.pageObjects.orderPage.orderFilterStatusSelect,
      Statuses.paymentError.index,
    );
    const result = await this.pageObjects.orderPage.checkTextValue(
      this.pageObjects.orderPage.orderfirstLineStatusTD,
      Statuses.paymentError.status,
    );
    await expect(result).to.be.true;
  });
  it('should logout from the BO', async function () {
    await this.pageObjects.boBasePage.logoutBO();
    const pageTitle = await this.pageObjects.loginPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.loginPage.pageTitle);
  });
});
