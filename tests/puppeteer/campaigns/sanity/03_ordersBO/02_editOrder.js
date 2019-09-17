const {expect} = require('chai');
const helper = require('../../utils/helpers');
// Using chai
// importing pages
const LoginPage = require('../../../pages/BO/login');
const DashboardPage = require('../../../pages/BO/dashboard');
const BOBasePage = require('../../../pages/BO/BObasePage');
const OrderPage = require('../../../pages/BO/order');

let browser;
let page;
let loginPage;
let dashboardPage;
let boBasePage;
let orderPage;
// creating pages objects in a function
const init = async function () {
  loginPage = await (new LoginPage(page));
  dashboardPage = await (new DashboardPage(page));
  boBasePage = await (new BOBasePage(page));
  orderPage = await (new OrderPage(page));
};

/*
  Connect to the BO
  Edit the first order
  Logout from the BO
 */
describe('Edit Order BO', async () => {
  before(async () => {
    browser = await helper.createBrowser();
    page = await browser.newPage();
    await init();
  });
  after(async () => {
    await browser.close();
  });
  it('should login into BO', async () => {
    await loginPage.goTo(global.URL_BO);
    await loginPage.login(global.EMAIL, global.PASSWD);
    const pageTitle = await dashboardPage.getPageTitle();
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    await boBasePage.closeOnboardingModal();
  });
  it('should go to the Orders page', async () => {
    await boBasePage.goToSubMenu(boBasePage.ordersParentLink, orderPage.ordersLink);
    const pageTitle = await orderPage.getPageTitle();
    await expect(pageTitle).to.contains(orderPage.pageTitle);
  });
  it('should go to the first order page', async () => {
    await boBasePage.waitForSelectorAndClick(orderPage.orderfirstLineIdTD);
    const pageTitle = await orderPage.getPageTitle();
    await expect(pageTitle).to.contains(orderPage.orderPageTitle);
  });
  it('should modify the product quantity and check the validation', async () => {
    await orderPage.modifyProductQuantity('1', '5');
  });
  it('should modify the order status and check the validation', async () => {
    await orderPage.modifyOrderStatus('Payment accepted');
  });
  it('should logout from the BO', async () => {
    await boBasePage.logoutBO();
    const pageTitle = await loginPage.getPageTitle();
    await expect(pageTitle).to.contains(loginPage.pageTitle);
  });
});
