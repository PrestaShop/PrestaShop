// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
// Import FO pages
import foProductPage from '@pages/FO/product';

// Import data
import ProductData from '@data/faker/product';
import {ProductInformations} from '@data/types/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  disableNewProductPageTest,
  resetNewProductPageAsDefault,
} from '@commonTests/BO/advancedParameters/newFeatures';

const baseContext: string = 'sanity_productsBO_CRUDStandardProductWithCombinationsInBO';

// Create, read, update and delete Standard product with combinations in BO
describe('BO - Catalog - Products : Create, read, update and delete Standard product '
  + 'with combinations in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let productInformation: ProductInformations = {
    price: 0,
    name: '',
    description: '',
    summary: '',
  };

  const productWithCombinations: ProductData = new ProductData({
    type: 'Standard product',
    productHasCombinations: true,
  });
  const editedProductWithCombinations: ProductData = new ProductData({
    type: 'Standard product',
    productHasCombinations: true,
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

  describe('Product page V1: Perform basic crud operations with combinations', async () => {
    // Steps
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      await productsPage.resetFilterCategory(page);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    it('should create Product with Combinations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await productsPage.goToAddProductPage(page);
      await addProductPage.createEditBasicProduct(page, productWithCombinations);

      const createProductMessage = await addProductPage.setAttributesInProduct(
        page,
        productWithCombinations,
      );
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct1', baseContext);

      page = await addProductPage.previewProduct(page);
      productInformation = await foProductPage.getProductInformation(page);

      const pageTitle = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productWithCombinations.name);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await addProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should check that all product attributes are correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductAttributes1', baseContext);

      // Check that all Product attribute are correct
      await Promise.all([
        expect(productInformation.name).to.equal(productWithCombinations.name),
        expect(productInformation.price).to.equal(productWithCombinations.price),
        expect(productInformation.description).to.contains(productWithCombinations.description),
        expect(productInformation.summary).to.contains(productWithCombinations.summary),
      ]);
    });

    it('should edit product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProduct', baseContext);

      await addProductPage.createEditBasicProduct(page, editedProductWithCombinations);

      const createProductMessage = await addProductPage.setAttributesInProduct(
        page,
        editedProductWithCombinations,
      );
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      page = await addProductPage.previewProduct(page);
      productInformation = await foProductPage.getProductInformation(page);

      const pageTitle = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(editedProductWithCombinations.name);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await addProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should check that all product attributes are correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductAttributes2', baseContext);

      // Check that all Product attribute are correct
      await Promise.all([
        expect(productInformation.name).to.equal(editedProductWithCombinations.name),
        expect(productInformation.price).to.equal(editedProductWithCombinations.price),
        expect(productInformation.summary).to.contains(editedProductWithCombinations.summary),
        expect(productInformation.description).to.contains(editedProductWithCombinations.description),
      ]);
    });

    it('should delete product and be on product list page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const testResult = await addProductPage.deleteProduct(page);
      await expect(testResult).to.equal(productsPage.productDeletedSuccessfulMessage);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
