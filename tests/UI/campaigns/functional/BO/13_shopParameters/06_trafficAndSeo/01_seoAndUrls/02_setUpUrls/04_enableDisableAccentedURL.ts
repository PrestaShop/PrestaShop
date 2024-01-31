// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import seoAndUrlsPage from '@pages/BO/shopParameters/trafficAndSeo/seoAndUrls';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
import seoTab from '@pages/BO/catalog/products/add/seoTab';
// Import FO pages
import {homePage as foHomePage} from '@pages/FO/classic/home';

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
  const productData: ProductData = new ProductData({name: productName, type: 'standard'});

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
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.equal(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, productData.type);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await addProductPage.closeSfToolBar(page);

      const createProductMessage = await addProductPage.setProduct(page, productData);
      expect(createProductMessage).to.equal(addProductPage.successfulUpdateMessage);
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
        expect(pageTitle).to.contains(seoAndUrlsPage.pageTitle);
      });

      it(`should ${test.args.action} accented URL`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}AccentedUrl`, baseContext);

        const result = await seoAndUrlsPage.enableDisableAccentedURL(page, test.args.enable);
        expect(result).to.contains(seoAndUrlsPage.successfulSettingsUpdateMessage);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPageAfter${test.args.action}`, baseContext);

        await seoAndUrlsPage.goToSubMenu(
          page,
          seoAndUrlsPage.catalogParentLink,
          seoAndUrlsPage.productsLink,
        );

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfter${test.args.action}`, baseContext);

        await productsPage.resetFilter(page);

        const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
        expect(numberOfProducts).to.be.above(0);
      });

      it('should filter by the created product name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterProductTo${test.args.action}`, baseContext);

        await productsPage.filterProducts(page, 'product_name', productName);

        const textColumn = await productsPage.getTextColumn(page, 'product_name', 1);
        expect(textColumn).to.contains(productName);
      });

      it('should go to the created product page and reset the friendly url', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFriendlyURl${test.args.action}`, baseContext);

        await productsPage.goToProductPage(page, 1);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);

        await addProductPage.goToTab(page, 'seo');
        await seoTab.clickOnGenerateUrlFromNameButton(page);

        const updateProductMessage = await addProductPage.saveProduct(page);
        expect(updateProductMessage).to.equal(addProductPage.successfulUpdateMessage);
      });

      it('should check the product URL', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProductUrl${test.args.action}`, baseContext);

        // Go to product page in FO
        page = await addProductPage.previewProduct(page);

        const url = await foHomePage.getCurrentURL(page);
        expect(url).to.contains(test.args.productNameInURL.toLowerCase());
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${test.args.action}`, baseContext);

        // Go back to BO
        page = await foHomePage.closePage(browserContext, page, 0);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const testResult = await addProductPage.deleteProduct(page);
      expect(testResult).to.equal(productsPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });
});
