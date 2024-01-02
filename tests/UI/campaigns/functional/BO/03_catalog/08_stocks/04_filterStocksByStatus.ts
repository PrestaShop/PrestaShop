// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
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

  const productData: ProductData = new ProductData({type: 'standard', status: false});

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

      await productsPage.resetFilter(page);

      numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.eq(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, productData.type);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await addProductPage.closeSfToolBar(page);

      const createProductMessage = await addProductPage.setProduct(page, productData);
      expect(createProductMessage).to.equal(addProductPage.successfulUpdateMessage);
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

    it('should filter list by the created product and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterListByReference', baseContext);

      await productsPage.filterProducts(page, 'reference', productData.reference, 'input');

      const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.equal(1);

      const textColumn = await productsPage.getTextColumn(page, 'reference', 1);
      expect(textColumn).to.equal(productData.reference);
    });

    it('should click on delete product button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDeleteProduct', baseContext);

      const isModalVisible = await productsPage.clickOnDeleteProductButton(page);
      expect(isModalVisible).to.be.equal(true);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const textMessage = await productsPage.clickOnConfirmDialogButton(page);
      expect(textMessage).to.equal(productsPage.successfulDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });
  });
});
