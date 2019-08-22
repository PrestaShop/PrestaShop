// Using chai
const {expect} = require('chai');
// importing pages
const LoginPage = require('../../pages/BO/login');
const DashboardPage = require('../../pages/BO/dashboard');
const BOBasePage = require('../../pages/BO/BObasePage');
const OrderPage = require('../../pages/BO/order');
const ProductPage = require('../../pages/BO/product');
const CustomerPage = require('../../pages/BO/customer');

let page;
let loginPage;
let dashboardPage;
let boBasePage;
let orderPage;
let productPage;
let customerPage;
// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  loginPage = await (new LoginPage(page));
  dashboardPage = await (new DashboardPage(page));
  boBasePage = await (new BOBasePage(page));
  orderPage = await (new OrderPage(page));
  productPage = await (new ProductPage(page));
  customerPage = await (new CustomerPage(page));
};

/*
  Connect to the BO
  Crawl a few key pages
  Logout from the BO
 */
global.scenario('Crawl into BO and check a few key pages', async () => {
  test('should login into BO', async () => {
    await loginPage.goTo(global.URL_BO);
    await loginPage.login(global.EMAIL, global.PASSWD);
    const pageTitle = await dashboardPage.getPageTitle();
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    await boBasePage.closeOnboardingModal();
  });

  test('should visit the Orders page', async () => {
    await boBasePage.goToSubMenu(boBasePage.ordersParentLink, boBasePage.ordersLink);
    const pageTitle = await orderPage.getPageTitle();
    await expect(pageTitle).to.contains(orderPage.pageTitle);
  });

  test('should visit the Products page', async () => {
    await boBasePage.goToSubMenu(boBasePage.productsParentLink, boBasePage.productsLink);
    const pageTitle = await productPage.getPageTitle();
    await expect(pageTitle).to.contains(productPage.pageTitle);
  });

  test('should visit the Customers page', async () => {
    await boBasePage.goToSubMenu(boBasePage.customersParentLink, boBasePage.customersLink);
    const pageTitle = await customerPage.getPageTitle();
    await expect(pageTitle).to.contains(customerPage.pageTitle);
  });

  test('should logout from the BO', async () => {
    // await LOGIN.goTo(global.URL_BO);
    await boBasePage.logoutBO();
    const pageTitle = await loginPage.getPageTitle();
    await expect(pageTitle).to.contains(loginPage.pageTitle);
  });
}, init, true);
