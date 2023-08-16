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
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_products_bulkActions';

/*
Go to products page
Create 2 products
Enable/Disable/Duplicate/Delete products by bulk actions
*/

describe('BO - Catalog - Products : Bulk actions products', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;
  let numberOfFilteredProductsAfterDuplicate: number = 0;

  const firstProductData: ProductData = new ProductData({name: 'TO DELETE 1', type: 'Standard product'});
  const secondProductData: ProductData = new ProductData({name: 'TO DELETE 2', type: 'Standard product'});

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

  describe('Create 2 products', async () => {
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
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset all filters and get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

      numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    [firstProductData, secondProductData].forEach((productData: ProductData, index: number) => {
      it(`should create product n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createProduct${index + 1}`, baseContext);

        await productsPage.goToAddProductPage(page);

        const createProductMessage = await addProductPage.createEditBasicProduct(page, productData);
        expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
      });

      it('should go to catalog page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToCatalogPage${index + 1}`, baseContext);

        await addProductPage.goToCatalogPage(page);

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });
    });
  });

  describe('Bulk set product status', async () => {
    it('should filter products by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkSetStatus', baseContext);

      await productsPage.filterProducts(page, 'name', 'TO DELETE');

      const textColumn = await productsPage.getProductNameFromList(page, 1);
      expect(textColumn).to.contains('TO DELETE');
    });

    [
      {action: 'enable', status: true},
      {action: 'disable', status: false},
    ].forEach((test) => {
      it(`should ${test.action} products`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.action}Products`, baseContext);

        const textResult = await productsPage.bulkSetStatus(page, test.status);
        expect(textResult)
          .to.equal(
            test.status
              ? productsPage.productMultiActivatedSuccessfulMessage
              : productsPage.productMultiDeactivatedSuccessfulMessage,
          );

        for (let row = 1; row <= 2; row++) {
          const productStatus = await productsPage.getProductStatusFromList(page, row);
          expect(productStatus).to.equal(test.status);
        }
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkSetStatus', baseContext);

      const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProductsAfterReset).to.be.equal(numberOfProducts + 2);
    });
  });

  describe('Bulk duplicate products', async () => {
    it('should filter products by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDuplicate', baseContext);

      await productsPage.filterProducts(page, 'name', 'TO DELETE');

      const textColumn = await productsPage.getProductNameFromList(page, 1);
      expect(textColumn).to.contains('TO DELETE');
    });

    it('should duplicate products by bulk actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDuplicate', baseContext);

      const duplicateTextResult = await productsPage.duplicateAllProductsWithBulkActions(page);
      expect(duplicateTextResult).to.equal(productsPage.productMultiDuplicatedSuccessfulMessage);

      numberOfFilteredProductsAfterDuplicate = await productsPage.getNumberOfProductsFromList(page);
      expect(numberOfFilteredProductsAfterDuplicate).to.be.below(numberOfProducts);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDuplicate', baseContext);

      const numberOfProductsAfterDuplicate = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProductsAfterDuplicate)
        .to.be.equal(numberOfProducts + numberOfFilteredProductsAfterDuplicate);
    });
  });

  describe('Bulk delete products', async () => {
    it('should filter products by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await productsPage.filterProducts(page, 'name', 'TO DELETE');

      const textColumn = await productsPage.getProductNameFromList(page, 1);
      expect(textColumn).to.contains('TO DELETE');
    });

    it('should delete products by bulk actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await productsPage.deleteAllProductsWithBulkActions(page);
      expect(deleteTextResult).to.equal(productsPage.productMultiDeletedSuccessfulMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProductsAfterReset).to.be.equal(numberOfProducts);
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
