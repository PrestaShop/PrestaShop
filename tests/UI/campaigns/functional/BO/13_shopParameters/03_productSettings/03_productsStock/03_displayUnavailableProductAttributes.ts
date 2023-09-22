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
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
// Import FO pages
import productPage from '@pages/FO/product';
import {homePage} from '@pages/FO/home';
import {searchResultsPage} from '@pages/FO/searchResults';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsStock_displayUnavailableProductAttributes';

describe('BO - Shop Parameters - Product Settings : Display unavailable product attributes '
  + 'on the product page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: ProductData = new ProductData({
    type: 'Standard product',
    attributes: [
      {
        name: 'color',
        values: ['White'],
      },
      {
        name: 'size',
        values: ['S'],
      },
    ],
    quantity: 0,
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

  describe('Display unavailable product attributes on the product page', async () => {
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

    it('should go to create product page and create a product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await productsPage.goToAddProductPage(page);
      await addProductPage.createEditBasicProduct(page, productData);

      const validationMessage = await addProductPage.setAttributesInProduct(page, productData);
      expect(validationMessage).to.equal(addProductPage.settingUpdatedMessage);
    });

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

      await addProductPage.goToSubMenu(
        page,
        addProductPage.shopParametersParentLink,
        addProductPage.productSettingsLink,
      );

      const pageTitle = await productSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    const tests = [
      {args: {action: 'disable', enable: false}},
      {args: {action: 'enable', enable: true}},
    ];
    tests.forEach((test, index: number) => {
      it(`should ${test.args.action} Display unavailable product attributes on the product page`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.action}DisplayUnavailableProductAttributes`,
          baseContext,
        );

        const result = await productSettingsPage.setDisplayUnavailableProductAttributesStatus(
          page,
          test.args.enable,
        );

        expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
      });

      it('should check the unavailable product attributes in FO product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkUnavailableAttribute${index}`, baseContext);

        page = await productSettingsPage.viewMyShop(page);

        await homePage.changeLanguage(page, 'en');
        await homePage.searchProduct(page, productData.name);
        await searchResultsPage.goToProductPage(page, 1);

        const sizeIsVisible = await productPage.isUnavailableProductSizeDisplayed(
          page,
          productData.attributes[1].values[0],
        );
        expect(sizeIsVisible).to.be.equal(test.args.enable);

        const colorIsVisible = await productPage.isUnavailableProductColorDisplayed(
          page,
          productData.attributes[0].values[0],
        );
        expect(colorIsVisible).to.be.equal(test.args.enable);
      });

      it('should close the page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `closePageAndBackToBO${index}`, baseContext);

        page = await productPage.closePage(browserContext, page, 0);

        const pageTitle = await productSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productSettingsPage.pageTitle);
      });
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDeleteProduct', baseContext);

      await productSettingsPage.goToSubMenu(
        page,
        productSettingsPage.catalogParentLink,
        productSettingsPage.productsLink,
      );

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteTextResult = await productsPage.deleteProduct(page, productData);
      expect(deleteTextResult).to.equal(productsPage.productDeletedSuccessfulMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

      await productsPage.resetFilterCategory(page);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
