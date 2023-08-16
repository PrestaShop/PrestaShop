// Import utils
import testContext from '@utils/testContext';
import helper from '@utils/helpers';

// Import commonTests
import {
  resetNewProductPageAsDefault,
  setFeatureFlag,
} from '@commonTests/BO/advancedParameters/newFeatures';

// Import BO pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
// Import FO pages
import {homePage} from '@pages/FO/home';

// Import data
import Categories from '@data/demo/categories';

import {expect} from 'chai';
import {BrowserContext, Page} from 'playwright';

const baseContext: string = 'sanity_catalogFO_filterProducts';

/*
  Open the FO home page
  Get the product number
  Filter products by a category
  Filter products by a subcategory
 */
describe('FO - Catalog : Filter Products by categories in Home page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let allProductsNumber: number = 0;

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

  describe('Catalog FO: Filter products from catalog', async () => {
    // Steps
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should check and get the products number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      await homePage.goToAllProductsPage(page);

      allProductsNumber = await homePage.getProductsNumber(page);
      expect(allProductsNumber).to.be.above(0);
    });

    it('should filter products by the category \'Accessories\' and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'FilterProductByCategory', baseContext);

      await homePage.goToCategory(page, Categories.accessories.id);

      const numberOfProducts = await homePage.getProductsNumber(page);
      expect(numberOfProducts).to.be.below(allProductsNumber);
    });

    it('should filter products by the subcategory \'Stationery\' and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'FilterProductBySubCategory', baseContext);

      await homePage.goToSubCategory(page, Categories.accessories.id, Categories.stationery.id);

      const numberOfProducts = await homePage.getProductsNumber(page);
      expect(numberOfProducts).to.be.below(allProductsNumber);
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
