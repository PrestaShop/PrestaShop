// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import addProductPage from '@pages/BO/catalog/products/add';
import stocksPage from '@pages/BO/catalog/stocks';

// Import data
import Categories from '@data/demo/categories';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_stocks_filterStocksByCategories';

/*
Filter stocks page by categories and check products list
 */
describe('BO - Catalog - Stocks : Filter stocks by categories', async () => {
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

    await addProductPage.goToSubMenu(
      page,
      addProductPage.catalogParentLink,
      addProductPage.stocksLink,
    );

    const pageTitle = await stocksPage.getPageTitle(page);
    expect(pageTitle).to.contains(stocksPage.pageTitle);
  });

  it('should reset filter and get number of products in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProductsInList', baseContext);

    numberOfProducts = await stocksPage.resetFilter(page);
    expect(numberOfProducts).to.be.above(0);
  });

  it('should filter by categories \'Art and Accessories\' and check the result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterCategories', baseContext);

    await stocksPage.filterByCategory(page, ['Art', 'Accessories']);

    const result = await stocksPage.getAllRowsColumnContent(page, 'reference');
    expect(result).to.include.members(Categories.art.products.concat(Categories.accessories.products));

    const numberOfProductsAfterFilter = await stocksPage.getTotalNumberOfProducts(page);
    expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
  });

  it('should uncheck the selected categories \'Art and Accessories\' and check the result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterCategories', baseContext);

    await stocksPage.filterByCategory(page, ['Art', 'Accessories']);

    const numberOfProductsAfterFilter = await stocksPage.getTotalNumberOfProducts(page);
    expect(numberOfProductsAfterFilter).to.equal(numberOfProducts);
  });

  it('should filter by category \'Clothes\' and check the result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByCategoryClothes', baseContext);

    await stocksPage.filterByCategory(page, ['Clothes']);

    const result = await stocksPage.getAllRowsColumnContent(page, 'reference');
    expect(result).to.include.members(Categories.clothes.products);

    const numberOfProductsAfterFilter = await stocksPage.getTotalNumberOfProducts(page);
    expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
  });

  it('should uncheck the selected category \'Clothes\' and check the result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterCategoryClothes', baseContext);

    await stocksPage.filterByCategory(page, ['Clothes']);

    const numberOfProductsAfterFilter = await stocksPage.getTotalNumberOfProducts(page);
    expect(numberOfProductsAfterFilter).to.equal(numberOfProducts);
  });

  it('should filter by all categories and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByAllCategories', baseContext);

    await stocksPage.filterByCategory(page,
      ['Home', 'Clothes', 'Men', 'Women', 'Accessories', 'Stationery', 'Home Accessories', 'Art']);

    const numberOfProductsAfterFilter = await stocksPage.getTotalNumberOfProducts(page);
    expect(numberOfProductsAfterFilter).to.equal(numberOfProducts);
  });

  it('should uncheck all categories and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uncheckAllCategories', baseContext);

    await stocksPage.filterByCategory(page,
      ['Home', 'Clothes', 'Men', 'Women', 'Accessories', 'Stationery', 'Home Accessories', 'Art']);

    const numberOfProductsAfterFilter = await stocksPage.getTotalNumberOfProducts(page);
    expect(numberOfProductsAfterFilter).to.equal(numberOfProducts);
  });
});
