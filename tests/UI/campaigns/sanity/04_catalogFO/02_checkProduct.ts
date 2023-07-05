// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {
  setFeatureFlag,
  resetNewProductPageAsDefault,
} from '@commonTests/BO/advancedParameters/newFeatures';

// Import BO pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
// Import FO pages
import {homePage} from '@pages/FO/home';
import productPage from '@pages/FO/product';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'sanity_catalogFO_checkProduct';

/*
  Open the FO home page
  Check the first product page
 */
describe('FO - Catalog : Check the Product page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  describe('Catalog FO: Check products from catalog', async () => {
    // Steps
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await homePage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should check the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPage', baseContext);

      const result = await productPage.getProductInformation(page);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_1.name),
        expect(result.price).to.equal(Products.demo_1.finalPrice),
        expect(result.description).to.contains(Products.demo_1.description),
      ]);
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
