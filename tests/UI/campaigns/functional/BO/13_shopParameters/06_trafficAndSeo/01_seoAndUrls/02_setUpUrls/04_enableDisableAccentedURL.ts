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
// Import BO pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';
import seoAndUrlsPage from '@pages/BO/shopParameters/trafficAndSeo/seoAndUrls';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
// Import FO pages
import {homePage as foHomePage} from '@pages/FO/home';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_trafficAndSeo_seoAndUrls_setUpUrls_enableDisableAccentedURL';

describe('BO - Shop Parameters - Traffic & SEO : Enable/Disable accented URL', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productName: string = 'TESTURLÉ';
  const productNameWithoutAccent: string = 'TESTURLE';
  const productData: ProductData = new ProductData({name: productName, type: 'Standard product'});

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

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  describe('Enable/Disable accented URL', async () => {
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

    it('should create a product that the name contains accented characters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAccentedCharsProduct', baseContext);

      await productsPage.goToAddProductPage(page);

      const createProductMessage = await addProductPage.createEditBasicProduct(page, productData);
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });

    const tests = [
      {args: {action: 'enable', enable: true, productNameInURL: productName}},
      {args: {action: 'disable', enable: false, productNameInURL: productNameWithoutAccent}},
    ];

    tests.forEach((test) => {
      it('should go to \'Shop Parameters > SEO & Urls\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToSeoPageTo${test.args.action}`, baseContext);

        await addProductPage.goToSubMenu(
          page,
          addProductPage.shopParametersParentLink,
          addProductPage.trafficAndSeoLink,
        );

        const pageTitle = await seoAndUrlsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(seoAndUrlsPage.pageTitle);
      });

      it(`should ${test.args.action} accented URL`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}AccentedUrl`, baseContext);

        const result = await seoAndUrlsPage.enableDisableAccentedURL(page, test.args.enable);
        await expect(result).to.contains(seoAndUrlsPage.successfulSettingsUpdateMessage);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPageAfter${test.args.action}`, baseContext);

        await seoAndUrlsPage.goToSubMenu(
          page,
          seoAndUrlsPage.catalogParentLink,
          seoAndUrlsPage.productsLink,
        );

        const pageTitle = await productsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfter${test.args.action}`, baseContext);

        await productsPage.resetFilterCategory(page);

        const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProducts).to.be.above(0);
      });

      it('should filter by the created product name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterProductTo${test.args.action}`, baseContext);

        await productsPage.filterProducts(page, 'name', productName);

        const textColumn = await productsPage.getProductNameFromList(page, 1);
        await expect(textColumn).to.contains(productName);
      });

      it('should go to the created product page and reset the friendly url', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFriendlyURl${test.args.action}`, baseContext);

        await productsPage.goToProductPage(page, 1);

        const pageTitle = await addProductPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addProductPage.pageTitle);

        await addProductPage.resetURL(page);
      });

      it('should check the product URL', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProductUrl${test.args.action}`, baseContext);

        // Go to product page in FO
        page = await addProductPage.previewProduct(page);

        const url = await foHomePage.getCurrentURL(page);
        await expect(url).to.contains(test.args.productNameInURL.toLowerCase());
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${test.args.action}`, baseContext);

        // Go back to BO
        page = await foHomePage.closePage(browserContext, page, 0);

        const pageTitle = await addProductPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addProductPage.pageTitle);
      });
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const testResult = await addProductPage.deleteProduct(page);
      await expect(testResult).to.equal(productsPage.productDeletedSuccessfulMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
