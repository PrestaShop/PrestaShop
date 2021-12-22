require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import data
const {Products} = require('@data/demo/products');
const {Suppliers} = require('@data/demo/suppliers');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const stocksPage = require('@pages/BO/catalog/stocks');

const baseContext = 'functional_BO_catalog_stocks_simpleFilterStocks';

let browserContext;
let page;

let numberOfProducts = 0;

// Simple filter stocks
describe('BO - Catalog - Stocks : Simple filter stocks', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Catalog > Stocks\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.stocksLink,
    );

    await stocksPage.closeSfToolBar(page);

    const pageTitle = await stocksPage.getPageTitle(page);
    await expect(pageTitle).to.contains(stocksPage.pageTitle);
  });

  it('should get number of products in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProductsInList', baseContext);

    numberOfProducts = await stocksPage.getTotalNumberOfProducts(page);
    await expect(numberOfProducts).to.be.above(0);
  });

  // Filter products by name, reference, supplier
  describe('Filter products by name, reference and supplier', async () => {
    const tests = [
      {args: {testIdentifier: 'filterName', filterBy: 'name', filterValue: Products.demo_1.name}},
      {args: {testIdentifier: 'filterReference', filterBy: 'reference', filterValue: Products.demo_1.reference}},
      {args: {testIdentifier: 'filterSupplier', filterBy: 'supplier', filterValue: Suppliers.fashionSupplier.name}},
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await stocksPage.simpleFilter(page, test.args.filterValue);

        const numberOfProductsAfterFilter = await stocksPage.getNumberOfProductsFromList(page);
        await expect(numberOfProductsAfterFilter).to.be.at.most(numberOfProducts);

        for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
          const textColumn = await stocksPage.getTextColumnFromTableStocks(page, i, test.args.filterBy);
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfProductsAfterReset = await stocksPage.resetFilter(page);
        await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
      });
    });
  });
});
