// Using chai
const {expect} = require('chai');
// importing pages
const LoginPage = require('../../../pages/BO/login');
const DashboardPage = require('../../../pages/BO/dashboard');
const BOBasePage = require('../../../pages/BO/BObasePage');
const OrderPage = require('../../../pages/BO/order');

let page;
let loginPage;
let dashboardPage;
let boBasePage;
let orderPage;
// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  loginPage = await (new LoginPage(page));
  dashboardPage = await (new DashboardPage(page));
  boBasePage = await (new BOBasePage(page));
  orderPage = await (new OrderPage(page));
};

/*
  Connect to the BO
  Filter the Orders table
  Logout from the BO
 */
global.scenario('Filter the Orders table by ID, REFERENCE, STATUS', async () => {
  test('should login into BO', async () => {
    await loginPage.goTo(global.URL_BO);
    await loginPage.login(global.EMAIL, global.PASSWD);
    const pageTitle = await dashboardPage.getPageTitle();
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    await boBasePage.closeOnboardingModal();
  });

  test('should go to the Orders page', async () => {
    await boBasePage.goToSubMenu(boBasePage.ordersParentLink, orderPage.ordersLink);
    const pageTitle = await orderPage.getPageTitle();
    await expect(pageTitle).to.contains(orderPage.pageTitle);
  });

  test('should filter the Orders table by ID and check the result', async () => {
    await orderPage.filterTableByInput(orderPage.orderFilterIdInput, '1', orderPage.searchButton);
    await boBasePage.checkTextValue(orderPage.orderfirstLineIdTD, '1');
    await orderPage.waitForSelectorAndClick(orderPage.resetButton)
  });

  test('should filter the Orders table by REFERENCE and check the result', async () => {
    await orderPage.filterTableByInput(orderPage.orderFilterReferenceInput, 'FFATNOMMJ', orderPage.searchButton);
    await boBasePage.checkTextValue(orderPage.orderfirstLineReferenceTD, 'FFATNOMMJ');
    await orderPage.waitForSelectorAndClick(orderPage.resetButton)
  });

  test('should filter the Orders table by STATUS and check the result', async () => {
    await orderPage.filterTableBySelect(orderPage.orderFilterStatusSelect, '8');
    await orderPage.checkTextValue(orderPage.orderfirstLineStatusTD, 'Payment error');
  });

  test('should logout from the BO', async () => {
    await boBasePage.logoutBO();
    const pageTitle = await loginPage.getPageTitle();
    await expect(pageTitle).to.contains(loginPage.pageTitle);
  });
}, init, true);
