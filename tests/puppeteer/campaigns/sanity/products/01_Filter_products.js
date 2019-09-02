// Using chai
const {expect} = require('chai');

// importing pages
const BO_LOGIN_PAGE = require('../../../pages/BO/login');
const BO_DASHBOARD_PAGE = require('../../../pages/BO/dashboard');
const BOBasePage = require('../../../pages/BO/BObasePage');
const ProductPage = require('../../../pages/BO/product');

let page;
let BO_LOGIN;
let BO_DASHBOARD;
let boBasePage;
let productPage;
let numberOfProducts;

// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  BO_LOGIN = await (new BO_LOGIN_PAGE(page));
  BO_DASHBOARD = await (new BO_DASHBOARD_PAGE(page));
  boBasePage = await (new BOBasePage(page));
  productPage = await (new ProductPage(page));
  numberOfProducts = 0;
};


// Test of filters in products page
global.scenario('Filter in Products Page', async () => {
  test('should login in BO', async () => {
    await BO_LOGIN.goTo(global.URL_BO);
    await BO_LOGIN.login(global.EMAIL, global.PASSWD);
    const pageTitle = await BO_DASHBOARD.getPageTitle();
    await expect(pageTitle).to.contains(BO_DASHBOARD.pageTitle);
    await boBasePage.closeOnboardingModal();
  });
  test('should go to Products page', async () => {
    await boBasePage.goToSubMenu(boBasePage.productsParentLink, boBasePage.productsLink);
    const pageTitle = await productPage.getPageTitle();
    await expect(pageTitle).to.contains(productPage.pageTitle);
  });
  test('should reset all filters and get Number of products in BO', async () => {
    if (await productPage.elementVisible(productPage.filterResetButton,2000)) await productPage.resetFilter();
    await productPage.resetFilterCategory();
    numberOfProducts = await productPage.getNumberOfProductsFromList();
    await expect(numberOfProducts).to.be.above(0);
  });
  test('should filter list by Name and check result', async () => {
    await productPage.filterProducts('name', 'Customizable mug');
    const numberOfProductsAfterFilter = await productPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
  });
  test('should reset filter and check result', async () => {
    await productPage.resetFilter();
    const numberOfProductsAfterReset = await productPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
  });
  test('should filter by Reference and check result', async () => {
    await productPage.filterProducts('reference', 'demo_1');
    const numberOfProductsAfterFilter = await productPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
  });
  test('should reset filter and check result', async () => {
    await productPage.resetFilter();
    const numberOfProductsAfterReset = await productPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
  });
  test('should filter by Category and check result', async () => {
    await productPage.filterProductsByCategory('Men');
    const numberOfProductsAfterFilter = await productPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
  });
  test('should reset filter Category and check result', async () => {
    await productPage.resetFilterCategory();
    const numberOfProductsAfterReset = await productPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
  });
}, init, true);
