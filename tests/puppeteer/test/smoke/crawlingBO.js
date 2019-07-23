// importing pages
const BO_LOGIN_PAGE = require('../../pages/BO/BO_login');
const BO_DASHBOARD_PAGE = require('../../pages/BO/BO_dashboard');
const BO_COMMON_PAGE = require('../../pages/BO/BO_commonPage');
const BO_ORDERS_PAGE = require('../../pages/BO/BO_orders');
const BO_PRODUCTS_PAGE = require('../../pages/BO/BO_products');
const BO_CUSTOMERS_PAGE = require('../../pages/BO/BO_customers');

let page;
let BO_LOGIN;
let BO_DASHBOARD;
let BO_COMMON;
let BO_ORDERS;
let BO_PRODUCTS;
let BO_CUSTOMERS;
// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  BO_LOGIN = await (new BO_LOGIN_PAGE(page));
  BO_DASHBOARD = await (new BO_DASHBOARD_PAGE(page));
  BO_COMMON = await (new BO_COMMON_PAGE(page));
  BO_ORDERS = await (new BO_ORDERS_PAGE(page));
  BO_PRODUCTS = await (new BO_PRODUCTS_PAGE(page));
  BO_CUSTOMERS = await (new BO_CUSTOMERS_PAGE(page));
};

/*
  Connect to the BO
  Crawl a few key pages
  Logout from the BO
 */
global.scenario('Crawl into BO et check a few key pages', async () => {
  test('should login into BO', async () => {
    await BO_LOGIN.goTo(global.URL_BO);
    await BO_LOGIN.login(global.EMAIL, global.PASSWD);
    const pageTitle = await BO_DASHBOARD.getPageTitle();
    await global.expect(pageTitle).to.contains(BO_DASHBOARD.pageTitle);
    await BO_COMMON.closeOnboardingModal();
  });

  test('should visit the Orders page', async () => {
    await BO_COMMON.goToSubMenu(BO_COMMON.ordersParentLink, BO_COMMON.ordersLink);
    const pageTitle = await BO_ORDERS.getPageTitle();
    await global.expect(pageTitle).to.contains(BO_ORDERS.pageTitle);
  });

  test('should visit the Products page', async () => {
    await BO_COMMON.goToSubMenu(BO_COMMON.productsParentLink, BO_COMMON.productsLink);
    const pageTitle = await BO_PRODUCTS.getPageTitle();
    await global.expect(pageTitle).to.contains(BO_PRODUCTS.pageTitle);
  });

  test('should visit the Customers page', async () => {
    await BO_COMMON.goToSubMenu(BO_COMMON.customersParentLink, BO_COMMON.customersLink);
    const pageTitle = await BO_CUSTOMERS.getPageTitle();
    await global.expect(pageTitle).to.contains(BO_CUSTOMERS.pageTitle);
  });

  test('should logout from the BO', async () => {
    // await BO_LOGIN.goTo(global.URL_BO);
    await BO_COMMON.logoutBO();
    const pageTitle = await BO_LOGIN.getPageTitle();
    await global.expect(pageTitle).to.contains(BO_LOGIN.pageTitle);
  });
}, init, true);
