require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BOBasePage = require('@pages/BO/BObasePage');
const ProductsPage = require('@pages/BO/products');
const {Products} = require('@data/demo/products');
const {Categories} = require('@data/demo/categories');

let browser;
let page;
let numberOfProducts = 0;

// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    boBasePage: new BOBasePage(page),
    productsPage: new ProductsPage(page),
  };
};

// Test of filters in products page
describe('Filter in Products Page', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Steps
  loginCommon.loginBO();
  it('should go to Products page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.productsLink,
    );
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });
  it('should reset all filters and get Number of products in BO', async function () {
    if (await this.pageObjects.productsPage.elementVisible(this.pageObjects.productsPage.filterResetButton, 2000)) {
      await this.pageObjects.productsPage.resetFilter();
    }
    await this.pageObjects.productsPage.resetFilterCategory();
    numberOfProducts = await this.pageObjects.productsPage.getNumberOfProductsFromList();
    await expect(numberOfProducts).to.be.above(0);
  });
  it('should filter list by Name and check result', async function () {
    await this.pageObjects.productsPage.filterProducts('name', Products.demo_14.name);
    const numberOfProductsAfterFilter = await this.pageObjects.productsPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
  });
  it('should reset filter and check result', async function () {
    await this.pageObjects.productsPage.resetFilter();
    const numberOfProductsAfterReset = await this.pageObjects.productsPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
  });
  it('should filter by Reference and check result', async function () {
    await this.pageObjects.productsPage.filterProducts('reference', Products.demo_1.reference);
    const numberOfProductsAfterFilter = await this.pageObjects.productsPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
  });
  it('should reset filter and check result', async function () {
    await this.pageObjects.productsPage.resetFilter();
    const numberOfProductsAfterReset = await this.pageObjects.productsPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
  });
  it('should filter by Category and check result', async function () {
    await this.pageObjects.productsPage.filterProductsByCategory(Categories.men.name);
    const numberOfProductsAfterFilter = await this.pageObjects.productsPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
  });
  it('should reset filter Category and check result', async function () {
    await this.pageObjects.productsPage.resetFilterCategory();
    const numberOfProductsAfterReset = await this.pageObjects.productsPage.getNumberOfProductsFromList();
    await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
  });
});
