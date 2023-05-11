// Import utils
import testContext from '@utils/testContext';
import helper from '@utils/helpers';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  disableNewProductPageTest,
  resetNewProductPageAsDefault,
} from '@commonTests/BO/advancedParameters/newFeatures';

const baseContext: string = 'sanity_productsBO_deleteProduct';

// Create Standard product in BO and Delete it with DropDown Menu
describe('BO - Catalog - Products : Create Standard product in BO and Delete it with DropDown Menu', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: ProductData = new ProductData({
    type: 'Standard product',
    productHasCombinations: false,
  });

  // Pre-condition: Disable new product page
  disableNewProductPageTest(`${baseContext}_disableNewProduct`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Product page V1: Delete product', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters1', baseContext);

      await productsPage.resetFilterCategory(page);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await productsPage.goToAddProductPage(page);
      await productsPage.closeSfToolBar(page);

      const createProductMessage = await addProductPage.createEditBasicProduct(page, productData);
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

      await addProductPage.goToSubMenu(
        page,
        addProductPage.catalogParentLink,
        addProductPage.productsLink,
      );

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should delete product from DropDown Menu', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteTextResult = await productsPage.deleteProduct(page, productData);
      await expect(deleteTextResult).to.equal(productsPage.productDeletedSuccessfulMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters2', baseContext);

      await productsPage.resetFilterCategory(page);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
