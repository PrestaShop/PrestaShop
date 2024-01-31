import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import createProductPage from '@pages/BO/catalog/products/add';
import optionsTab from '@pages/BO/catalog/products/add/optionsTab';
import productsPage from '@pages/BO/catalog/products';
import descriptionTab from '@pages/BO/catalog/products/add/descriptionTab';

// Import FO pages
import foProductPage from '@pages/FO/classic/product';
import {searchResultsPage} from '@pages/FO/classic/searchResults';
import categoryPageFO from '@pages/FO/classic/category';
import {homePage} from '@pages/FO/classic/home';

// Import data
import ProductData from '@data/faker/product';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

const baseContext: string = 'functional_BO_catalog_products_optionsTab';

describe('BO - Catalog - Products : Options tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let productsNumber: number;

  // Data to create standard product
  const newProductData: ProductData = new ProductData({
    name: 'Sleek Concrete Shirt',
    type: 'standard',
    coverImage: 'cover.jpg',
    quantity: 100,
    minimumQuantity: 1,
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    if (newProductData.coverImage) {
      await files.generateImage(newProductData.coverImage);
    }
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    if (newProductData.coverImage) {
      await files.deleteFile(newProductData.coverImage);
    }
  });

  // 1 - Create product
  describe('Create product', async () => {
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
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should choose \'Standard product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, newProductData.type);
      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await createProductPage.setProduct(page, newProductData);
      await expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });
  });

  // 2- Check all options in options tab
  describe('Check all options in options tab', async () => {
    it('should add category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCategory', baseContext);

      await createProductPage.goToTab(page, 'description');
      await descriptionTab.addNewCategory(page, ['Clothes']);

      const createProductMessage = await createProductPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });

    it('should choose \'Clothes\' as default category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCategory', baseContext);

      await descriptionTab.chooseDefaultCategory(page, 2);

      const createProductMessage = await createProductPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });

    it('should check the visibility to catalog only in options tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryOnly', baseContext);

      await createProductPage.goToTab(page, 'options');
      await optionsTab.setVisibility(page, 'catalog_only');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should click on Clothes category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickHomeCategory', baseContext);

      await foProductPage.clickOnBreadCrumbLink(page, 'clothes');

      const pageTitle = await categoryPageFO.getHeaderPageName(page);
      expect(pageTitle).to.contains('CLOTHES');
    });

    it('should check that the created product is visible in clothes category list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatProductIsVisible', baseContext);

      productsNumber = await homePage.getProductsNumber(page);
      await categoryPageFO.quickViewProduct(page, productsNumber);

      const isModalVisible = await categoryPageFO.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.eq(true);

      const result = await homePage.getProductDetailsFromQuickViewModal(page);
      expect(result.name).to.equal(newProductData.name);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should check the visibility to search only in options tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSearchOnly', baseContext);

      await optionsTab.setVisibility(page, 'search_only');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should click on Clothes category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickClothesCategory2', baseContext);

      await foProductPage.clickOnBreadCrumbLink(page, 'clothes');

      const pageTitle = await categoryPageFO.getHeaderPageName(page);
      expect(pageTitle).to.contains('CLOTHES');
    });

    it('should check that the created product is not visible in clothes category list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatProductIsNotVisible', baseContext);

      const productsNumberInCategory = await homePage.getProductsNumber(page);
      expect(productsNumberInCategory).to.eq(productsNumber - 1);
    });

    it('should search for the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await homePage.searchProduct(page, newProductData.name);
      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage2', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should check the visibility to nowhere in options tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNowhere', baseContext);

      await optionsTab.setVisibility(page, 'nowhere');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct3', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should click on Clothes category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickClothesCategory3', baseContext);

      await foProductPage.clickOnBreadCrumbLink(page, 'clothes');

      const pageTitle = await categoryPageFO.getHeaderPageName(page);
      expect(pageTitle).to.contains('CLOTHES');
    });

    it('should check that the created product is not visible in clothes category list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatProductIsNotVisible2', baseContext);

      const productsNumberInCategory = await homePage.getProductsNumber(page);
      expect(productsNumberInCategory).to.eq(productsNumber - 1);
    });

    it('should search for the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct2', baseContext);

      await homePage.searchProduct(page, newProductData.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);

      const hasResults = await searchResultsPage.hasResults(page);
      expect(hasResults, 'There are results!').to.eq(false);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage3', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should check the visibility to everywhere in options tab and disable Available for order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEverywhere', baseContext);

      await optionsTab.setVisibility(page, 'everywhere');
      await optionsTab.setAvailableForOrder(page, false);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct4', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that the add to cart button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isAddToCartButtonEnabled', baseContext);

      const isVisible = await foProductPage.isAddToCartButtonEnabled(page);
      expect(isVisible).eq(false);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage4', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should disable the option show price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableShowPrice', baseContext);

      await optionsTab.setShowPrice(page, false);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct5', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that the price is not displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isPriceDisplayed', baseContext);

      const isVisible = await foProductPage.isPriceDisplayed(page);
      expect(isVisible).to.equal(false);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage5', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should enable the option show price and the option web only', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableShowPrice', baseContext);

      await optionsTab.setShowPrice(page, true);
      await optionsTab.setWebOnly(page, true);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct6', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the online tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOnlineTag', baseContext);

      const flagText = await foProductPage.getProductTag(page);
      expect(flagText).to.contains('Online only');
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage6', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should choose the supplier associated with the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseSupplierAssociated', baseContext);

      await optionsTab.chooseSupplier(page, 1);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should check the 2 blocks \'Default supplier\' & \'Supplier references\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewBlocks', baseContext);

      let isVisible = await optionsTab.isDefaultSupplierSectionVisible(page);
      expect(isVisible, 'Default supplier block is not visible!').to.eq(true);

      isVisible = await optionsTab.isSupplierReferencesSectionVisible(page);
      expect(isVisible, 'Supplier references block is not visible!').to.eq(true);
    });
  });

  // 3 - Delete product
  describe('POST-TEST: Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteProductMessage = await createProductPage.deleteProduct(page);
      expect(deleteProductMessage).to.equal(productsPage.successfulDeleteMessage);
    });
  });
});
