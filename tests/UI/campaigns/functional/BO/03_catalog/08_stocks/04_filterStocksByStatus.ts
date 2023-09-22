// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {
  resetNewProductPageAsDefault,
  setFeatureFlag,
} from '@commonTests/BO/advancedParameters/newFeatures';

// Import pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
import stocksPage from '@pages/BO/catalog/stocks';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_stocks_filterStocksByStatus';

/*
Create new disabled product
Filter stocks page by status and check existence of product
Delete product
 */
describe('BO - Catalog - Stocks : Filter stocks by status', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

  const productData: ProductData = new ProductData({type: 'Standard product', status: false});

  // Pre-condition: Disable new product page
  setFeatureFlag(featureFlagPage.featureFlagProductPageV2, false, `${baseContext}_disableNewProduct`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create disabled product', async () => {
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
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      await productsPage.resetFilterCategory(page);

      numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    it('should create disabled Product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await productsPage.goToAddProductPage(page);

      const createProductMessage = await addProductPage.createEditBasicProduct(page, productData);
      expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });
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
      expect(pageTitle).to.contains(stocksPage.pageTitle);
    });

    it('should filter by status \'disabled\' and check the existence of the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterStatus', baseContext);

      await stocksPage.filterByStatus(page, 'disabled');

      const textColumn = await stocksPage.getTextColumnFromTableStocks(page, 1, 'name');
      expect(textColumn).to.contains(productData.name);
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
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const testResult = await productsPage.deleteProduct(page, productData);
      expect(testResult).to.equal(productsPage.productDeletedSuccessfulMessage);

      const numberOfProductsAfterDelete = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProductsAfterDelete).to.equal(numberOfProducts);
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
