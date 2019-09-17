const {expect} = require('chai');
const helper = require('../../utils/helpers');
// Using chai

// importing pages
const LoginPage = require('../../../pages/BO/login');
const DashboardPage = require('../../../pages/BO/dashboard');
const BOBasePage = require('../../../pages/BO/BObasePage');
const ProductsPage = require('../../../pages/BO/products');

let browser;
let page;
let loginPage;
let dashboardPage;
let boBasePage;
let productsPage;
let numberOfProducts = 0;

// creating pages objects in a function
const init = async function () {
  loginPage = await (new LoginPage(page));
  dashboardPage = await (new DashboardPage(page));
  boBasePage = await (new BOBasePage(page));
  productsPage = await (new ProductsPage(page));
};

// Test of filters in products page
describe('Filter in Products Page', async () => {
  before(async () => {
    browser = await helper.createBrowser();
    page = await browser.newPage();
    await init();
  });
  after(async () => {
    await browser.close();
  });
  it('should login in BO', async () => {
    await loginPage.goTo(global.URL_BO);
    await loginPage.login(global.EMAIL, global.PASSWD);
    const pageTitle = await dashboardPage.getPageTitle();
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    await boBasePage.closeOnboardingModal();
  });
  it('should go to Products page', async () => {
    await boBasePage.goToSubMenu(boBasePage.productsParentLink, boBasePage.productsLink);
    const pageTitle = await productsPage.getPageTitle();
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });
  it('should reset all filters and get Number of products in BO', async () => {
    if (await productsPage.elementVisible(productsPage.filterResetButton, 2000)) await productsPage.resetFilter();
    await productsPage.resetFilterCategory();
    numberOfProducts = await productsPage.getNumberOfProductsFromList();
    await expect(numberOfProducts).to.be.above(0);
  });
  it('should filter list by Name and check result', async () => {
    await productsPage.filterProducts('name', 'Customizable mug');
    const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
  });
  it('should reset filter and check result', async () => {
    await productsPage.resetFilter();
    const numberOfProductsAfterReset = await productsPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
  });
  it('should filter by Reference and check result', async () => {
    await productsPage.filterProducts('reference', 'demo_1');
    const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
  });
  it('should reset filter and check result', async () => {
    await productsPage.resetFilter();
    const numberOfProductsAfterReset = await productsPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
  });
  it('should filter by Category and check result', async () => {
    await productsPage.filterProductsByCategory('Men');
    const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
  });
  it('should reset filter Category and check result', async () => {
    await productsPage.resetFilterCategory();
    const numberOfProductsAfterReset = await productsPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
  });
});
