// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import stocksPage from '@pages/BO/catalog/stocks';

// Import data
import Products from '@data/demo/products';
import Suppliers from '@data/demo/suppliers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_stocks_simpleFilterStocks';

// Simple filter stocks
describe('BO - Catalog - Stocks : Simple filter stocks', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

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
    expect(pageTitle).to.contains(stocksPage.pageTitle);
  });

  it('should get number of products in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProductsInList', baseContext);

    numberOfProducts = await stocksPage.getTotalNumberOfProducts(page);
    expect(numberOfProducts).to.be.above(0);
  });

  // Filter products by name, reference, supplier
  describe('Filter products by name, reference and supplier', async () => {
    [
      {args: {testIdentifier: 'filterName', filterBy: 'product_name', filterValue: Products.demo_1.name}},
      {args: {testIdentifier: 'filterReference', filterBy: 'reference', filterValue: Products.demo_1.reference}},
      {args: {testIdentifier: 'filterSupplier', filterBy: 'supplier', filterValue: Suppliers.fashionSupplier.name}},
    ].forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await stocksPage.simpleFilter(page, test.args.filterValue);

        const numberOfProductsAfterFilter = await stocksPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterFilter).to.be.at.most(numberOfProducts);

        for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
          const textColumn = await stocksPage.getTextColumnFromTableStocks(page, i, test.args.filterBy);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfProductsAfterReset = await stocksPage.resetFilter(page);
        expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
      });
    });
  });
});
