import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  type BrowserContext,
  FakerProduct,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

let browserContext: BrowserContext;
let page: Page;
let numberOfProducts: number;

/**
 * Function to create standard product
 * @param productData {FakerProduct} Data to set to create product
 * @param baseContext {string} String to identify the test
 */
function createProductTest(productData: FakerProduct, baseContext: string = 'commonTests-createProductTest'): void {
  describe(`PRE-TEST: Create product '${productData.name}'`, async () => {
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

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.eq(true);
    });

    it(`should choose '${productData.type} product'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseTypeOfProduct', baseContext);

      await boProductsPage.selectProductType(page, productData.type);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setProduct', baseContext);

      await boProductsCreatePage.closeSfToolBar(page);

      const createProductMessage = await boProductsCreatePage.setProduct(page, productData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });
}

/**
 * Function to delete product
 * @param productData {FakerProduct} Data to set to delete product
 * @param baseContext {string} String to identify the test
 */
function deleteProductTest(productData: FakerProduct, baseContext: string = 'commonTests-deleteProductTest'): void {
  describe(`POST-TEST: Delete product '${productData.name}'`, async () => {
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

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDelete', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      await boProductsPage.resetFilter(page);

      const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    it('should click on delete product button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDeleteProduct', baseContext);

      const isModalVisible: boolean = await boProductsPage.clickOnDeleteProductButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const textMessage: string = await boProductsPage.clickOnConfirmDialogButton(page);
      expect(textMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfProductsAfterReset: number = await boProductsPage.resetAndGetNumberOfLines(page);
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

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToBulkDelete', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should reset filter and get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProduct', baseContext);

      numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    it('should filter list by \'Name\' and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterListByReference', baseContext);

      await boProductsPage.filterProducts(page, 'product_name', productName, 'input');

      numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);

      const textColumn = await boProductsPage.getTextColumn(page, 'product_name', 1);
      expect(textColumn).to.contains(productName);
    });

    it('should select the products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectProducts', baseContext);

      const isBulkDeleteButtonEnabled = await boProductsPage.bulkSelectProducts(page);
      expect(isBulkDeleteButtonEnabled).to.be.eq(true);
    });

    it('should click on bulk actions button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnBulkActionsButton', baseContext);

      const textMessage = await boProductsPage.clickOnBulkActionsProducts(page, 'delete');
      expect(textMessage).to.equal(`Deleting ${numberOfProductsAfterFilter} products`);
    });

    it('should bulk delete products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteProducts', baseContext);

      const textMessage = await boProductsPage.bulkActionsProduct(page, 'delete');
      expect(textMessage).to.equal(`Deleting ${numberOfProductsAfterFilter} / ${numberOfProductsAfterFilter} products`);
    });

    it('should close progress modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeDeleteProgressModal', baseContext);

      const isModalVisible = await boProductsPage.closeBulkActionsProgressModal(page, 'delete');
      expect(isModalVisible).to.be.eq(true);
    });

    it('should reset filter and get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProduct', baseContext);

      const numberOfProductAfterBulkActions = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProductAfterBulkActions).to.be.equal(numberOfProducts - numberOfProductsAfterFilter);
    });
  });
}

export {
  createProductTest, deleteProductTest, bulkDeleteProductsTest,
};
