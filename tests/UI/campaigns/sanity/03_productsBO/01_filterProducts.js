require('module-alias/register');
// Using chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const productsPage = require('@pages/BO/catalog/products');

// Import data
const {Products} = require('@data/demo/products');
const {Categories} = require('@data/demo/categories');
const {DefaultFrTax} = require('@data/demo/tax');

const baseContext = 'sanity_productsBO_filterProducts';

let browserContext;
let page;
let numberOfProducts = 0;
let numberOfProductsOnPage = 0;

// Test of filters in products page
describe('BO - Catalog - Products : Filter in Products Page', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Steps
  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.productsLink,
    );

    const pageTitle = await productsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it('should reset all filters and get number of products in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

    await productsPage.resetFilterCategory(page);
    numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProducts).to.be.above(0);

    // Do not loop more than the products displayed via the pagination
    numberOfProductsOnPage = await productsPage.getNumberOfProductsOnPage(page);
  });

  it('should check that prices have correct tax values', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkTaxRules', baseContext);

    // Check that prices have correct tax values
    for (let i = 1; i <= numberOfProducts && i <= numberOfProductsOnPage; i++) {
      const productPrice = await productsPage.getProductPriceFromList(page, i, false);
      const productPriceATI = await productsPage.getProductPriceFromList(page, i, true);
      const conversionRate = (100 + parseInt(DefaultFrTax.rate, 10)) / 100;
      await expect(parseFloat(productPrice)).to.equal(parseFloat((productPriceATI / conversionRate).toFixed(2)));
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
        await productsPage.filterProductsByCategory(page, test.args.filterValue);
      } else {
        await productsPage.filterProducts(page, test.args.filterBy, test.args.filterValue);
      }

      const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
      await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
    });

    it('should reset filter and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `resetFilters_${test.args.identifier}`, baseContext);

      let numberOfProductsAfterReset;

      if (test.args.filterBy === 'category') {
        await productsPage.resetFilterCategory(page);
        numberOfProductsAfterReset = await productsPage.getNumberOfProductsFromList(page);
      } else {
        numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
      }

      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });
  });
});
