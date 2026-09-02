import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsCreatePage,
  boStockPage,
  type BrowserContext,
  dataCategories,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_stocks_filterStocksByCategories';

/*
 * Filter stocks page by categories and check products list
 */
describe('BO - Catalog - Stocks : Filter stocks by categories', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

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

    await boProductsCreatePage.goToSubMenu(
      page,
      boProductsCreatePage.catalogParentLink,
      boProductsCreatePage.stocksLink,
    );

    const pageTitle = await boStockPage.getPageTitle(page);
    expect(pageTitle).to.contains(boStockPage.pageTitle);
  });

  it('should reset filter and get number of products in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProductsInList', baseContext);

    numberOfProducts = await boStockPage.resetFilter(page);
    expect(numberOfProducts).to.be.above(0);
  });

  it('should filter by categories \'Art and Accessories\' and check the result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterCategories', baseContext);

    await boStockPage.filterByCategory(page, ['Art', 'Accessories']);

    const result = await boStockPage.getAllRowsColumnContent(page, 'reference');
    expect(result).to.include.members(dataCategories.art.products.concat(dataCategories.accessories.products));

    const numberOfProductsAfterFilter = await boStockPage.getTotalNumberOfProducts(page);
    expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
  });

  it('should uncheck the selected categories \'Art and Accessories\' and check the result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterCategories', baseContext);

    await boStockPage.filterByCategory(page, ['Art', 'Accessories']);

    const numberOfProductsAfterFilter = await boStockPage.getTotalNumberOfProducts(page);
    expect(numberOfProductsAfterFilter).to.equal(numberOfProducts);
  });

  it('should filter by category \'Clothes\' and check the result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByCategoryClothes', baseContext);

    await boStockPage.filterByCategory(page, ['Clothes']);

    const result = await boStockPage.getAllRowsColumnContent(page, 'reference');
    expect(result).to.include.members(dataCategories.clothes.products);

    const numberOfProductsAfterFilter = await boStockPage.getTotalNumberOfProducts(page);
    expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
  });

  it('should uncheck the selected category \'Clothes\' and check the result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterCategoryClothes', baseContext);

    await boStockPage.filterByCategory(page, ['Clothes']);

    const numberOfProductsAfterFilter = await boStockPage.getTotalNumberOfProducts(page);
    expect(numberOfProductsAfterFilter).to.equal(numberOfProducts);
  });

  it('should filter by all categories and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByAllCategories', baseContext);

    await boStockPage.filterByCategory(page,
      ['Home', 'Clothes', 'Men', 'Women', 'Accessories', 'Stationery', 'Home Accessories', 'Art']);

    const numberOfProductsAfterFilter = await boStockPage.getTotalNumberOfProducts(page);
    expect(numberOfProductsAfterFilter).to.equal(numberOfProducts);
  });

  it('should uncheck all categories and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uncheckAllCategories', baseContext);

    await boStockPage.filterByCategory(page,
      ['Home', 'Clothes', 'Men', 'Women', 'Accessories', 'Stationery', 'Home Accessories', 'Art']);

    const numberOfProductsAfterFilter = await boStockPage.getTotalNumberOfProducts(page);
    expect(numberOfProductsAfterFilter).to.equal(numberOfProducts);
  });
});
