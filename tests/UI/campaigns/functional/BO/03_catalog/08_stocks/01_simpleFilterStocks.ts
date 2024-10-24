// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boStockPage,
  type BrowserContext,
  dataProducts,
  dataSuppliers,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_stocks_simpleFilterStocks';

// Simple filter stocks
describe('BO - Catalog - Stocks : Simple filter stocks', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Catalog > Stocks\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.stocksLink,
    );
    await boStockPage.closeSfToolBar(page);

    const pageTitle = await boStockPage.getPageTitle(page);
    expect(pageTitle).to.contains(boStockPage.pageTitle);
  });

  it('should get number of products in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProductsInList', baseContext);

    numberOfProducts = await boStockPage.getTotalNumberOfProducts(page);
    expect(numberOfProducts).to.be.above(0);
  });

  // Filter products by name, reference, supplier
  describe('Filter products by name, reference and supplier', async () => {
    [
      {args: {testIdentifier: 'filterName', filterBy: 'product_name', filterValue: dataProducts.demo_1.name}},
      {args: {testIdentifier: 'filterReference', filterBy: 'reference', filterValue: dataProducts.demo_1.reference}},
      {args: {testIdentifier: 'filterSupplier', filterBy: 'supplier', filterValue: dataSuppliers.fashion.name}},
    ].forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await boStockPage.simpleFilter(page, test.args.filterValue);

        const numberOfProductsAfterFilter = await boStockPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterFilter).to.be.at.most(numberOfProducts);

        for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
          const textColumn = await boStockPage.getTextColumnFromTableStocks(page, i, test.args.filterBy);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfProductsAfterReset = await boStockPage.resetFilter(page);
        expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
      });
    });
  });
});
