import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabDescriptionPage,
  boProductsCreateTabOptionsPage,
  type BrowserContext,
  FakerProduct,
  foClassicHomePage,
  foClassicCategoryPage,
  foClassicModalQuickViewPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_optionsTab';

describe('BO - Catalog - Products : Options tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let productsNumber: number;

  // Data to create standard product
  const newProductData: FakerProduct = new FakerProduct({
    name: 'Sleek Concrete Shirt',
    type: 'standard',
    coverImage: 'cover.jpg',
    quantity: 100,
    minimumQuantity: 1,
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    if (newProductData.coverImage) {
      await utilsFile.generateImage(newProductData.coverImage);
    }
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    if (newProductData.coverImage) {
      await utilsFile.deleteFile(newProductData.coverImage);
    }
  });

  // 1 - Create product
  describe('Create product', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should choose \'Standard product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);
      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      await expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.setProduct(page, newProductData);
      await expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  // 2- Check all options in options tab
  describe('Check all options in options tab', async () => {
    it('should add category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCategory', baseContext);

      await boProductsCreatePage.goToTab(page, 'description');
      await boProductsCreateTabDescriptionPage.addNewCategory(page, ['Clothes']);

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should choose \'Clothes\' as default category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCategory', baseContext);

      await boProductsCreateTabDescriptionPage.chooseDefaultCategory(page, 'Clothes');

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should check the visibility to catalog only in options tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryOnly', baseContext);

      await boProductsCreatePage.goToTab(page, 'options');
      await boProductsCreateTabOptionsPage.setVisibility(page, 'catalog_only');

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should click on Clothes category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickHomeCategory', baseContext);

      await foClassicProductPage.clickOnBreadCrumbLink(page, 'clothes');

      const pageTitle = await foClassicCategoryPage.getHeaderPageName(page);
      expect(pageTitle).to.contains('CLOTHES');
    });

    it('should check that the created product is visible in clothes category list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatProductIsVisible', baseContext);

      productsNumber = await foClassicCategoryPage.getProductsNumber(page);
      await foClassicCategoryPage.quickViewProduct(page, productsNumber);

      const isModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.eq(true);

      const result = await foClassicModalQuickViewPage.getProductDetailsFromQuickViewModal(page);
      expect(result.name).to.equal(newProductData.name);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should check the visibility to search only in options tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSearchOnly', baseContext);

      await boProductsCreateTabOptionsPage.setVisibility(page, 'search_only');

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should click on Clothes category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickClothesCategory2', baseContext);

      await foClassicProductPage.clickOnBreadCrumbLink(page, 'clothes');

      const pageTitle = await foClassicCategoryPage.getHeaderPageName(page);
      expect(pageTitle).to.contains('CLOTHES');
    });

    it('should check that the created product is not visible in clothes category list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatProductIsNotVisible', baseContext);

      const productsNumberInCategory = await foClassicCategoryPage.getProductsNumber(page);
      expect(productsNumberInCategory).to.eq(productsNumber - 1);
    });

    it('should search for the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await foClassicCategoryPage.searchProduct(page, newProductData.name);
      await foClassicSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage2', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should check the visibility to nowhere in options tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNowhere', baseContext);

      await boProductsCreateTabOptionsPage.setVisibility(page, 'nowhere');

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct3', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should click on Clothes category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickClothesCategory3', baseContext);

      await foClassicProductPage.clickOnBreadCrumbLink(page, 'clothes');

      const pageTitle = await foClassicCategoryPage.getHeaderPageName(page);
      expect(pageTitle).to.contains('CLOTHES');
    });

    it('should check that the created product is not visible in clothes category list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatProductIsNotVisible2', baseContext);

      const productsNumberInCategory = await foClassicCategoryPage.getProductsNumber(page);
      expect(productsNumberInCategory).to.eq(productsNumber - 1);
    });

    it('should search for the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct2', baseContext);

      await foClassicHomePage.searchProduct(page, newProductData.name);

      const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

      const hasResults = await foClassicSearchResultsPage.hasResults(page);
      expect(hasResults, 'There are results!').to.eq(false);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage3', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should check the visibility to everywhere in options tab and disable Available for order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEverywhere', baseContext);

      await boProductsCreateTabOptionsPage.setVisibility(page, 'everywhere');
      await boProductsCreateTabOptionsPage.setAvailableForOrder(page, false);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct4', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that the add to cart button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isAddToCartButtonEnabled', baseContext);

      const isVisible = await foClassicProductPage.isAddToCartButtonEnabled(page);
      expect(isVisible).eq(false);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage4', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should disable the option show price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableShowPrice', baseContext);

      await boProductsCreateTabOptionsPage.setShowPrice(page, false);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct5', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that the price is not displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isPriceDisplayed', baseContext);

      const isVisible = await foClassicProductPage.isPriceDisplayed(page);
      expect(isVisible).to.equal(false);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage5', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should enable the option show price and the option web only', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableShowPrice', baseContext);

      await boProductsCreateTabOptionsPage.setShowPrice(page, true);
      await boProductsCreateTabOptionsPage.setWebOnly(page, true);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct6', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the online tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOnlineTag', baseContext);

      const flagText = await foClassicProductPage.getProductTag(page);
      expect(flagText).to.contains('Online only');
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage6', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should choose the supplier associated with the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseSupplierAssociated', baseContext);

      await boProductsCreateTabOptionsPage.chooseSupplier(page, 1);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should check the 2 blocks \'Default supplier\' & \'Supplier references\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewBlocks', baseContext);

      let isVisible = await boProductsCreateTabOptionsPage.isDefaultSupplierSectionVisible(page);
      expect(isVisible, 'Default supplier block is not visible!').to.eq(true);

      isVisible = await boProductsCreateTabOptionsPage.isSupplierReferencesSectionVisible(page);
      expect(isVisible, 'Supplier references block is not visible!').to.eq(true);
    });
  });

  // 3 - Delete product
  describe('POST-TEST: Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteProductMessage = await boProductsCreatePage.deleteProduct(page);
      expect(deleteProductMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });
});
