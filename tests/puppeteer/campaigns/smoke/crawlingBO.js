// importing pages
const LOGIN_PAGE = require('../../pages/BO/login');
const DASHBOARD_PAGE = require('../../pages/BO/dashboard');
const COMMON_PAGE = require('../../pages/BO/commonPage');
const ORDERS_PAGE = require('../../pages/BO/orders');
const PRODUCTS_PAGE = require('../../pages/BO/products');
const CUSTOMERS_PAGE = require('../../pages/BO/customers');

let page;
let LOGIN;
let DASHBOARD;
let COMMON;
let ORDERS;
let PRODUCTS;
let CUSTOMERS;
// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  LOGIN = await (new LOGIN_PAGE(page));
  DASHBOARD = await (new DASHBOARD_PAGE(page));
  COMMON = await (new COMMON_PAGE(page));
  ORDERS = await (new ORDERS_PAGE(page));
  PRODUCTS = await (new PRODUCTS_PAGE(page));
  CUSTOMERS = await (new CUSTOMERS_PAGE(page));
};

/*
  Connect to the BO
  Crawl a few key pages
  Logout from the BO
 */
global.scenario('Crawl into BO and check a few key pages', async () => {
  test('should login into BO', async () => {
    await LOGIN.goTo(global.URL_BO);
    await LOGIN.login(global.EMAIL, global.PASSWD);
    const pageTitle = await DASHBOARD.getPageTitle();
    await global.expect(pageTitle).to.contains(DASHBOARD.pageTitle);
    await COMMON.closeOnboardingModal();
  });

  test('should visit the Orders page', async () => {
    await COMMON.goToSubMenu(COMMON.ordersParentLink, COMMON.ordersLink);
    const pageTitle = await ORDERS.getPageTitle();
    await global.expect(pageTitle).to.contains(ORDERS.pageTitle);
  });

  test('should visit the Products page', async () => {
    await COMMON.goToSubMenu(COMMON.productsParentLink, COMMON.productsLink);
    const pageTitle = await PRODUCTS.getPageTitle();
    await global.expect(pageTitle).to.contains(PRODUCTS.pageTitle);
  });

  test('should visit the Customers page', async () => {
    await COMMON.goToSubMenu(COMMON.customersParentLink, COMMON.customersLink);
    const pageTitle = await CUSTOMERS.getPageTitle();
    await global.expect(pageTitle).to.contains(CUSTOMERS.pageTitle);
  });

  test('should logout from the BO', async () => {
    // await LOGIN.goTo(global.URL_BO);
    await COMMON.logoutBO();
    const pageTitle = await LOGIN.getPageTitle();
    await global.expect(pageTitle).to.contains(LOGIN.pageTitle);
  });
}, init, true);
