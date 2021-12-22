require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');
const stocksPage = require('@pages/BO/catalog/stocks');

// Import data
const ProductFaker = require('@data/faker/product');

const baseContext = 'functional_BO_catalog_stocks_filterStocksByStatus';

let browserContext;
let page;

let numberOfProducts = 0;

const productData = new ProductFaker({type: 'Standard product', status: false});

/*
Create new disabled product
Filter stocks page by status and check existence of product
Delete product
 */
describe('BO - Catalog - Stocks : Filter stocks by status', async () => {
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

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToCreate', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.productsLink,
    );

    await productsPage.closeSfToolBar(page);

    const pageTitle = await productsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    await productsPage.resetFilterCategory(page);
    numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProducts).to.be.above(0);
  });

  it('should create disabled Product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

    await productsPage.goToAddProductPage(page);
    const createProductMessage = await addProductPage.createEditBasicProduct(page, productData);
    await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
  });

  describe('Check the disabled product in stocks page', async () => {
    it('should go to \'Catalog > Stocks\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

      await addProductPage.goToSubMenu(
        page,
        addProductPage.catalogParentLink,
        addProductPage.stocksLink,
      );

      const pageTitle = await stocksPage.getPageTitle(page);
      await expect(pageTitle).to.contains(stocksPage.pageTitle);
    });

    it('should filter by status \'disabled\' and check the existence of the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterStatus', baseContext);

      await stocksPage.filterByStatus(page, 'disabled');

      const textColumn = await stocksPage.getTextColumnFromTableStocks(page, 1, 'name');
      await expect(textColumn).to.contains(productData.name);
    });
  });

  describe('Delete product', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDelete', baseContext);

      await stocksPage.goToSubMenu(
        page,
        stocksPage.catalogParentLink,
        stocksPage.productsLink,
      );

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const testResult = await productsPage.deleteProduct(page, productData);
      await expect(testResult).to.equal(productsPage.productDeletedSuccessfulMessage);

      const numberOfProductsAfterDelete = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProductsAfterDelete).to.equal(numberOfProducts);
    });
  });
});
