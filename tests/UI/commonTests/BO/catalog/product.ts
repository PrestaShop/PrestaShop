// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

let browserContext: BrowserContext;
let page: Page;
let numberOfProducts: number;

/**
 * Function to create standard product
 * @param productData {ProductData} Data to set to create product
 * @param baseContext {string} String to identify the test
 */
function createProductTest(productData: ProductData, baseContext: string = 'commonTests-createProductTest'): void {
  describe(`PRE-TEST: Create product '${productData.name}'`, async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);
      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.eq(true);
    });

    it(`should choose '${productData.type} product' and go to new product page`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseTypeOfProduct', baseContext);

      await productsPage.selectProductType(page, productData.type);
      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await addProductPage.closeSfToolBar(page);

      const createProductMessage = await addProductPage.setProduct(page, productData);
      expect(createProductMessage).to.equal(addProductPage.successfulUpdateMessage);
    });
  });
}

/**
 * Function to delete product
 * @param productData {ProductData} Data to set to delete product
 * @param baseContext {string} String to identify the test
 */
function deleteProductTest(productData: ProductData, baseContext: string = 'commonTests-deleteProductTest'): void {
  describe(`POST-TEST: Delete product '${productData.name}'`, async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDelete', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      await productsPage.resetFilter(page);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    it('should click on delete product button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDeleteProduct', baseContext);

      const isModalVisible: boolean = await productsPage.clickOnDeleteProductButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const textMessage: string = await productsPage.clickOnConfirmDialogButton(page);
      expect(textMessage).to.equal(productsPage.successfulDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfProductsAfterReset: number = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProductsAfterReset).to.be.above(0);
    });
  });
}

/**
 * Function to bulk delete product
 * @param productName {string} Value to set on product name input
 * @param baseContext {string} String to identify the test
 */
function bulkDeleteProductsTest(productName: string, baseContext: string = 'commonTests-bulkDeleteProductsTest'): void {
  describe('POST-TEST: Bulk delete created products', async () => {
    let numberOfProductsAfterFilter: number;

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
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToBulkDelete', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset filter and get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProduct', baseContext);

      numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    it('should filter list by \'Name\' and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterListByReference', baseContext);

      await productsPage.filterProducts(page, 'product_name', productName, 'input');

      numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);

      const textColumn = await productsPage.getTextColumn(page, 'product_name', 1);
      expect(textColumn).to.contains(productName);
    });

    it('should select the products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectProducts', baseContext);

      const isBulkDeleteButtonEnabled = await productsPage.bulkSelectProducts(page);
      expect(isBulkDeleteButtonEnabled).to.be.eq(true);
    });

    it('should click on bulk actions button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnBulkActionsButton', baseContext);

      const textMessage = await productsPage.clickOnBulkActionsProducts(page, 'delete');
      expect(textMessage).to.equal(`Deleting ${numberOfProductsAfterFilter} products`);
    });

    it('should bulk delete products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteProducts', baseContext);

      const textMessage = await productsPage.bulkActionsProduct(page, 'delete');
      expect(textMessage).to.equal(`Deleting ${numberOfProductsAfterFilter} / ${numberOfProductsAfterFilter} products`);
    });

    it('should close progress modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeDeleteProgressModal', baseContext);

      const isModalVisible = await productsPage.closeBulkActionsProgressModal(page, 'delete');
      expect(isModalVisible).to.be.eq(true);
    });

    it('should reset filter and get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProduct', baseContext);

      const numberOfProductAfterBulkActions = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProductAfterBulkActions).to.be.equal(numberOfProducts - numberOfProductsAfterFilter);
    });
  });
}

export {
  createProductTest, deleteProductTest, bulkDeleteProductsTest,
};
