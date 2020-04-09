require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const testContext = require('@utils/testContext');

const baseContext = 'sanity_productsBO_filterProducts';

// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BOBasePage = require('@pages/BO/BObasePage');
const ProductsPage = require('@pages/BO/catalog/products');
const {Products} = require('@data/demo/products');
const {Categories} = require('@data/demo/categories');
const {DefaultFrTax} = require('@data/demo/tax');

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
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.productsLink,
    );
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should reset all filters and get Number of products in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);
    await this.pageObjects.productsPage.resetFilterCategory();
    numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
    await expect(numberOfProducts).to.be.above(0);

    // Do not loop more than the products displayed via the pagination
    const numberOfProductsOnPage = await this.pageObjects.productsPage.getNumberOfProductsOnPage();
    // Check that prices have correct tax values
    for (let i = 1; i <= numberOfProducts && i <= numberOfProductsOnPage; i++) {
      const productPrice = await this.pageObjects.productsPage.getProductPriceFromList(i);
      const productPriceTTC = await this.pageObjects.productsPage.getProductPriceFromList(i, true);
      const conversionRate = (100 + parseInt(DefaultFrTax.rate, 10)) / 100;
      await expect(parseFloat(productPrice)).to.equal(parseFloat((productPriceTTC / conversionRate).toFixed(2)));
    }
  });

  const tests = [
    {args: {identifier: 'filterName', filterBy: 'name', filterValue: Products.demo_14.name}},
    {args: {identifier: 'filterReference', filterBy: 'reference', filterValue: Products.demo_1.reference}},
    {args: {identifier: 'filterCategory', filterBy: 'category', filterValue: Categories.men.name}},
  ];
  tests.forEach((test) => {
    it(`should filter list by ${test.args.filterBy} and check result`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `filterBy_${test.args.identifier}`, baseContext);
      if (test.args.filterBy === 'category') {
        await this.pageObjects.productsPage.filterProductsByCategory(test.args.filterValue);
      } else {
        await this.pageObjects.productsPage.filterProducts(test.args.filterBy, test.args.filterValue);
      }
      const numberOfProductsAfterFilter = await this.pageObjects.productsPage.getNumberOfProductsFromList();
      await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
    });

    it('should reset filter and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `resetFilters_${test.args.identifier}`, baseContext);
      let numberOfProductsAfterReset;
      if (test.args.filterBy === 'category') {
        await this.pageObjects.productsPage.resetFilterCategory();
        numberOfProductsAfterReset = await this.pageObjects.productsPage.getNumberOfProductsFromList();
      } else {
        numberOfProductsAfterReset = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
      }
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });
  });
});
