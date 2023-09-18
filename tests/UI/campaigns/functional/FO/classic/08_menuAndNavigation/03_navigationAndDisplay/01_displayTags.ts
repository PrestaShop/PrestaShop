// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {setFeatureFlag} from '@commonTests/BO/advancedParameters/newFeatures';
import {deleteProductTest} from '@commonTests/BO/catalog/product';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import {homePage} from '@pages/FO/home';
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import {searchResultsPage} from '@pages/FO/searchResults';
import productPage from '@pages/FO/product';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
import addProductPage from '@pages/BO/catalog/products/add';

// Import data
import Products from '@data/demo/products';
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_menuAndNavigation_navigationAndDisplay_displayTags';

/*
Pre-condition:
- Disable new product page
Scenario:
- Go to Fo and check the new tag
- Edit 'Number of days for which the product is considered 'New''
- Check that the new tag is no displayed in product page
- Add specific price to the product demo_6 in BO
- Check the discount tag in FO
- Create a pack of products in BO
- Check the pack tag in FO
- Change the created product quantity to 0 in BO
- Check the out-of-stock tag in FO
Post-condition:
- Reset number of days which product is considered new
- Delete specific price
- Enable new product page
 */
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

  const packOfProducts: ProductData = new ProductData({
    name: 'Pack of products',
    type: 'Pack of products',
    pack: [
      {
        reference: 'demo_13',
        quantity: 1,
      },
      {
        reference: 'demo_7',
        quantity: 1,
      },
    ],
    price: 12.65,
    taxRule: 'No tax',
    quantity: 100,
    minimumQuantity: 2,
    stockLocation: 'stock 3',
    lowStockLevel: 3,
    behaviourOutOfStock: 'Default behavior',
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

  describe('FO - Check the new tag', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShopPage', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it(`should search for the product '${Products.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductDemo6', baseContext);

      await homePage.searchProduct(page, Products.demo_6.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageDemo6', baseContext);

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

  describe('BO - Edit \'Number of days for which the product is considered \'New\'\'', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.productSettingsLink,
      );

      const pageTitle = await productSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it('should change the number of days to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeNumberOfDays0', baseContext);

      const result = await productSettingsPage.updateNumberOfDays(page, 0);
      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });
  });

  describe('FO - Check that the new tag is no displayed in product page', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO1', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it(`should search for the product '${Products.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductDemo6_2', baseContext);

      await homePage.searchProduct(page, Products.demo_6.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageDemo6_2', baseContext);

      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle).to.contains(Products.demo_6.name);
    });

    it('should check that the new tag is not displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIsNewTagNotVisible', baseContext);

      const isTagVisible = await productPage.isProductTagVisible(page);
      await expect(isTagVisible).to.be.false;
    });
  });

  describe(`BO - Add specific price to the product '${Products.demo_6.name}'`, async () => {
    it('should go back BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

      await productPage.goTo(page, global.BO.URL);

      const pageTitle = await dashboardPage.getPageTitle(page);
      await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

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

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    it('should filter by product name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterProductName', baseContext);

      await productsPage.filterProducts(page, 'name', Products.demo_6.name, 'input');

      const numberOfProducts = await productsPage.getNumberOfProductsFromList(page);
      await expect(numberOfProducts).to.eq(1);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage2', baseContext);

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

  describe('FO - Check the discount tag', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO2', baseContext);

      page = await addProductPage.previewProduct(page);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle).to.contains(Products.demo_6.name);
    });

    it('should check the discount tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountTag', baseContext);

      const flagText = await productPage.getProductTag(page);
      await expect(flagText).to.eq(`-€${specificPriceData.specificPrice.discount.toFixed(2)}`);
    });
  });

  describe('BO - Create a pack of products', async () => {
    it('should go back BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await addProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage3', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);
      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should go to add product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddProductPage2', baseContext);

      await productsPage.goToAddProductPage(page);

      const pageTitle = await addProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it(`create product '${packOfProducts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      const createProductMessage = await addProductPage.setProduct(page, packOfProducts);
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });
  });

  describe('FO - Check the pack tag', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO3', baseContext);

      page = await addProductPage.previewProduct(page);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle).to.contains(packOfProducts.name);
    });

    it('should check the pack tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPackTag', baseContext);

      const flagText = await productPage.getProductTag(page);
      await expect(flagText).to.eq('Pack');
    });
  });

  describe('BO - Change the created product quantity to 0', async () => {
    it('should go back BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await addProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should edit the quantity', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editQuantity', baseContext);

      await addProductPage.setProductQuantity(page, 0);

      const message = await addProductPage.saveProduct(page);
      await expect(message).to.eq(addProductPage.settingUpdatedMessage);
    });
  });

  describe('FO - Check the out-of-stock tag', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO4', baseContext);

      page = await addProductPage.previewProduct(page);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle).to.contains(packOfProducts.name);
    });

    it('should check the out-of-stock and pack tags', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOutOfStockTag', baseContext);

      const flagText = await productPage.getProductTag(page);
      await expect(flagText).to.contain('Pack')
        .and.contain('Out-of-Stock');
    });
  });

  // Post-condition: Reset 'Number of days for which the product is considered 'new''
  describe('POST-TEST : Reset \'Number of days for which the product is considered \'new\'\'', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO4', baseContext);

      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await addProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.productSettingsLink,
      );

      const pageTitle = await productSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it('should change the number of days to 12', async function () {
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

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters2', baseContext);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    it('should filter by product name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByProductName2', baseContext);

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

    it('should delete the specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSpecificPrice', baseContext);

      const message = await addProductPage.deleteSpecificPrice(page);
      await expect(message).to.equal(addProductPage.successfulDeleteMessage);
    });
  });

  // Post-condition: Delete created product
  deleteProductTest(packOfProducts, `${baseContext}_deleteProduct`);

  // Post-condition: Enable new product page
  setFeatureFlag(featureFlagPage.featureFlagProductPageV2, true, `${baseContext}_enableNewProduct`);
});
