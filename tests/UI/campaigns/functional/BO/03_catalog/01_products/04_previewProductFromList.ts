// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {
  setFeatureFlag,
  resetNewProductPageAsDefault,
} from '@commonTests/BO/advancedParameters/newFeatures';

// Import pages
// Import BO pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import productsPage from '@pages/BO/catalog/products';
import dashboardPage from '@pages/BO/dashboard';
// Import FO pages
import foProductPage from '@pages/FO/product';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_products_previewProductFromList';

/*
Go to products page
Filter products by name
Preview product
Check product information in FO
*/

describe('BO - Catalog - Products : Preview product from list', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

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

  describe('Preview product from list', async () => {
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

    it(`should filter product by name '${Products.demo_5.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToPreview', baseContext);

      await productsPage.filterProducts(page, 'name', Products.demo_5.name);

      const textColumn = await productsPage.getProductNameFromList(page, 1);
      expect(textColumn).to.contains(Products.demo_5.name);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Preview product
      page = await productsPage.previewProduct(page, 1);

      // Check product information in FO
      const productInformation = await foProductPage.getProductInformation(page);
      expect(productInformation.name).to.equal(Products.demo_5.name);
    });

    it('should close the current page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeCurrentPage', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset all filters and get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetSecond', baseContext);

      numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
