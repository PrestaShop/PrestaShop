// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {deleteProductTest} from '@commonTests/BO/catalog/product';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
import pricingTab from '@pages/BO/catalog/products/add/pricingTab';
import stocksTab from '@pages/BO/catalog/products/add/stocksTab';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
import {homePage} from '@pages/FO/home';
import {searchResultsPage} from '@pages/FO/searchResults';
import productPage from '@pages/FO/product';

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
  const newProductData: ProductData = new ProductData({
    type: 'standard',
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
    quantity: 100,
    minimumQuantity: 2,
    status: true,
  });
  const packOfProducts: ProductData = new ProductData({
    name: 'Pack of products',
    type: 'pack',
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
      expect(result).to.eq(true);
    });

    it(`should search for the product '${Products.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductDemo6', baseContext);

      await homePage.searchProduct(page, Products.demo_6.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageDemo6', baseContext);

      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_6.name);
    });

    it('should check the new tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewTag', baseContext);

      const flagText = await productPage.getProductTag(page);
      expect(flagText).to.eq('New');
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
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it('should change the number of days to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeNumberOfDays0', baseContext);

      const result = await productSettingsPage.updateNumberOfDays(page, 0);
      expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });
  });

  describe('FO - Check that the new tag is no displayed in product page', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO1', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it(`should search for the product '${Products.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductDemo6_2', baseContext);

      await homePage.searchProduct(page, Products.demo_6.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageDemo6_2', baseContext);

      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_6.name);
    });

    it('should check that the new tag is not displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIsNewTagNotVisible', baseContext);

      const isTagVisible = await productPage.isProductTagVisible(page);
      expect(isTagVisible).to.eq(false);
    });
  });

  describe('BO - Create product with specific', async () => {
    it('should go back BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

      await productPage.goTo(page, global.BO.URL);

      const pageTitle = await dashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(dashboardPage.pageTitle);
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
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on new product button and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductPage', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.eq(true);

      await productsPage.selectProductType(page, newProductData.type);
      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await addProductPage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(addProductPage.successfulUpdateMessage);
    });

    it('should add a specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addSpecificPrice', baseContext);

      await pricingTab.clickOnAddSpecificPriceButton(page);

      const message = await pricingTab.setSpecificPrice(page, specificPriceData.specificPrice);
      expect(message).to.equal(addProductPage.successfulCreationMessage);
    });
  });

  describe('FO - Check the discount tag', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO2', baseContext);

      page = await addProductPage.previewProduct(page);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the discount tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountTag', baseContext);

      const flagText = await productPage.getProductTag(page);
      expect(flagText).to.eq(`-€${specificPriceData.specificPrice.discount.toFixed(2)}`);
    });
  });

  describe('BO - Create a pack of products', async () => {
    it('should go back BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage3', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);
      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible: boolean = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.equal(true);
    });

    it('should choose \'Pack of products\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, packOfProducts.type);

      const pageTitle: string = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle: string = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it(`create product '${packOfProducts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      const createProductMessage = await addProductPage.setProduct(page, packOfProducts);
      expect(createProductMessage).to.equal(addProductPage.successfulUpdateMessage);
    });
  });

  describe('FO - Check the pack tag', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO3', baseContext);

      page = await addProductPage.previewProduct(page);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(packOfProducts.name);
    });

    it('should check the pack tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPackTag', baseContext);

      const flagText = await productPage.getProductTag(page);
      expect(flagText).to.eq('Pack');
    });
  });

  describe('BO - Change the created product quantity to 0', async () => {
    it('should go back BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should edit the quantity', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editQuantity', baseContext);

      await stocksTab.setProductQuantity(page, 0);

      const message = await addProductPage.saveProduct(page);
      expect(message).to.eq(addProductPage.successfulUpdateMessage);
    });
  });

  describe('FO - Check the out-of-stock tag', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO4', baseContext);

      page = await addProductPage.previewProduct(page);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(packOfProducts.name);
    });

    it('should check the out-of-stock and pack tags', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOutOfStockTag', baseContext);

      const flagText = await productPage.getProductTag(page);
      expect(flagText).to.contain('Pack')
        .and.contain('Out-of-Stock');
    });
  });

  // Post-condition: Reset 'Number of days for which the product is considered 'new''
  describe('POST-TEST : Reset \'Number of days for which the product is considered \'new\'\'', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO4', baseContext);

      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.productSettingsLink,
      );

      const pageTitle = await productSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it('should change the number of days to 12', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeNumberOfDays', baseContext);

      const result = await productSettingsPage.updateNumberOfDays(page, 12);
      expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });
  });

  // Post-condition: Delete specific price
  deleteProductTest(newProductData, `${baseContext}_deleteProduct_1`);

  // Post-condition: Delete created product
  deleteProductTest(packOfProducts, `${baseContext}_deleteProduct_2`);
});
