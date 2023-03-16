// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {setFeatureFlag} from '@commonTests/BO/advancedParameters/newFeatures';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import homePage from '@pages/FO/home';
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import searchResultsPage from '@pages/FO/searchResults';
import productPage from '@pages/FO/product';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
import addProductPage from '@pages/BO/catalog/products/add';

// Import data
import Products from '@data/demo/products';
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_menuAndNavigation_navigationAndDisplay_displayTags';

describe('FO - Navigation and display : Display tags', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const specificPriceData: ProductData = new ProductData({
    specificPrice: {
      attributes: null,
      discount: 10,
      startingAt: 1,
      reductionType: '€',
    },
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

  describe('FO - Check new tag', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it(`should search for the product '${Products.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await homePage.searchProduct(page, Products.demo_6.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle).to.contains(Products.demo_6.name);
    });

    it('should check the new tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewTag', baseContext);

      const flagText = await productPage.getProductTag(page);
      await expect(flagText).to.eq('New');
    });
  });

  describe('BO - Edit \'Number of days for which the product is considered \'new\'\'', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.productSettingsLink,
      );

      const pageTitle = await productSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it('should change the number of days to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeNumberOfDays', baseContext);

      const result = await productSettingsPage.updateNumberOfDays(page, 0);
      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });
  });

  describe('FO - Check that the new tag is no displayed in product page', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it(`should search for the product '${Products.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await homePage.searchProduct(page, Products.demo_6.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle).to.contains(Products.demo_6.name);
    });

    it('should check that the new tag is not displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIsTagVisible', baseContext);

      const isTagVisible = await productPage.isProductTagVisible(page);
      await expect(isTagVisible).to.be.false;
    });
  });

  describe(`BO - Add specific price to the product '${Products.demo_6.name}'`, async () => {
    it('should go back BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      await productPage.goTo(page, global.BO.URL);

      const pageTitle = await dashboardPage.getPageTitle(page);
      await expect(pageTitle).to.contains(dashboardPage.pageTitle);
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

    it('should filter by product name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfActiveProducts', baseContext);

      await productsPage.filterProducts(page, 'name', Products.demo_6.name, 'input');

      const numberOfProducts = await productsPage.getNumberOfProductsFromList(page);
      await expect(numberOfProducts).to.eq(1);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await productsPage.goToEditProductPage(page, 1);

      const pageTitle = await addProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should add a specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addSpecificPrice', baseContext);

      const message = await addProductPage.addSpecificPrices(page, specificPriceData.specificPrice);
      await expect(message).to.equal(addProductPage.settingUpdatedMessage);
    });
  });

  describe('FO - Check discount tag', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it(`should search for the product '${Products.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await homePage.searchProduct(page, Products.demo_6.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle).to.contains(Products.demo_6.name);
    });

    it('should check the discount tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewTag', baseContext);

      const flagText = await productPage.getProductTag(page);
      await expect(flagText).to.eq('-€10.00');
    });
  });

  // Post-condition: Reset 'Number of days for which the product is considered 'new''
  describe('POST-TEST : Reset \'Number of days for which the product is considered \'new\'\'', async () => {
    it('should go back BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      await productPage.goTo(page, global.BO.URL);

      const pageTitle = await dashboardPage.getPageTitle(page);
      await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    });

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.productSettingsLink,
      );

      const pageTitle = await productSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it('should change the number of days to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeNumberOfDays', baseContext);

      const result = await productSettingsPage.updateNumberOfDays(page, 12);
      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });
  });

  // Post-condition: Delete specific price
  describe('POST-TEST : Delete specific price', async () => {
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

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await productsPage.goToEditProductPage(page, 1);

      const pageTitle = await addProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should delete the specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSpecificPrice', baseContext);

      const message = await addProductPage.deleteSpecificPrice(page);
      await expect(message).to.equal(addProductPage.successfulDeleteMessage);
    });
  });

  // Post-condition: Disable new product page
  setFeatureFlag(featureFlagPage.featureFlagProductPageV2, true, `${baseContext}_enableNewProduct`);
});
