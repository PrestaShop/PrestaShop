// Import utils
import testContext from '@utils/testContext';
import helper from '@utils/helpers';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {
  resetNewProductPageAsDefault,
  setFeatureFlag,
} from '@commonTests/BO/advancedParameters/newFeatures';

// Import pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'sanity_productsBO_deleteProductsWithBulkActions';

// Create 2 Standard products in BO and Delete it with Bulk Actions
describe('BO - Catalog - Product : Create Standard product in BO and Delete it with Bulk Actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const firstProductData: ProductData = new ProductData({
    name: 'product To Delete 1',
    type: 'Standard product',
  });
  const secondProductData: ProductData = new ProductData({
    name: 'product To Delete 2',
    type: 'Standard product',
  });

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

  describe('Product page V1: Delete product with bulk actions', async () => {
    // Steps
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      await productsPage.resetFilterCategory(page);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    [firstProductData, secondProductData].forEach((productData: ProductData, index: number) => {
      it('should create new product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createProduct${index + 1}`, baseContext);

        await productsPage.goToAddProductPage(page);
        await productsPage.closeSfToolBar(page);

        const createProductMessage = await addProductPage.createEditBasicProduct(page, productData);
        await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPageAfterCreate${index + 1}`, baseContext);

        await addProductPage.goToSubMenu(
          page,
          addProductPage.catalogParentLink,
          addProductPage.productsLink,
        );

        const pageTitle = await productsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productsPage.pageTitle);
      });
    });

    it('should delete products with bulk Actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      // Filter By reference first
      await productsPage.filterProducts(page, 'name', 'product To Delete ');

      const deleteTextResult = await productsPage.deleteAllProductsWithBulkActions(page);
      await expect(deleteTextResult).to.equal(productsPage.productMultiDeletedSuccessfulMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersLast', baseContext);

      await productsPage.resetFilterCategory(page);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
