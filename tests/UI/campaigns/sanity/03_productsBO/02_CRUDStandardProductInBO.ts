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
import tax from '@data/demo/tax';
import ProductData from '@data/faker/product';
import {ProductInformations} from '@data/types/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  disableNewProductPageTest,
  resetNewProductPageAsDefault,
} from '@commonTests/BO/advancedParameters/newFeatures';

const baseContext: string = 'sanity_productsBO_CRUDStandardProductInBO';

// Create, read, update and delete Standard product in BO
describe('BO - Catalog - Products : Create, read, update and delete Standard product in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let productInformation: ProductInformations = {
    price: 0,
    name: '',
    description: '',
    summary: '',
  };

  const productData: ProductData = new ProductData({
    type: 'Standard product',
    productHasCombinations: false,
  });
  const editedProductData: ProductData = new ProductData({
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

  describe('Product page V1: Perform basic crud operations', async () => {
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

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await productsPage.goToAddProductPage(page);

      const createProductMessage = await addProductPage.createEditBasicProduct(page, productData);
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });

    it('should preview product and get all information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct1', baseContext);

      // Preview product in FO and get product information
      page = await addProductPage.previewProduct(page);
      productInformation = await foProductPage.getProductInformation(page);

      const pageTitle = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productData.name);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await addProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should check that all product attributes are correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductAttributes1', baseContext);

      // Check that all Product attribute are correct
      await Promise.all([
        expect(productInformation.name).to.equal(productData.name),
        expect(productInformation.price).to.equal(productData.price),
        expect(productInformation.summary).to.equal(productData.summary),
        expect(productInformation.description).to.contains(productData.description),
      ]);
    });

    it('should edit Product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProduct', baseContext);

      const createProductMessage = await addProductPage.createEditBasicProduct(page, editedProductData);
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });

    it('should preview product and get all information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      page = await addProductPage.previewProduct(page);
      productInformation = await foProductPage.getProductInformation(page);
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
        expect(productInformation.name).to.equal(editedProductData.name),
        expect(productInformation.price).to.equal(editedProductData.price),
        expect(productInformation.summary).to.be.equal(editedProductData.summary),
        expect(productInformation.description).to.be.equal(editedProductData.description),
      ]);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToCheckPrices', baseContext);

      await addProductPage.goToSubMenu(
        page,
        addProductPage.catalogParentLink,
        addProductPage.productsLink,
      );

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should filter list by reference and check prices', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsPrices', baseContext);

      await productsPage.filterProducts(page, 'reference', editedProductData.reference);

      const productPrice = await productsPage.getProductPriceFromList(page, 1, false);
      const productPriceATI = await productsPage.getProductPriceFromList(page, 1, true);

      const conversionRate = (100 + parseInt(tax.DefaultFrTax.rate, 10)) / 100;
      await expect(productPrice).to.equal(parseFloat((editedProductData.price / conversionRate).toFixed(2)));
      await expect(productPriceATI).to.equal(editedProductData.price);
    });

    it('should go to edit product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditProductPage', baseContext);

      await productsPage.goToEditProductPage(page, 1);

      const pageTitle = await addProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addProductPage.pageTitle);
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
