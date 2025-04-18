import testContext from '@utils/testContext';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabPricingPage,
  type BrowserContext,
  FakerProduct,
  foClassicCategoryPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_displayDiscount';

describe('FO - Product page - Product page : Display discount', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let productsNumber: number;

  const newProductData: FakerProduct = new FakerProduct({
    type: 'standard',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    tax: 20,
    quantity: 10,
    specificPrice: {
      attributes: null,
      discount: 2,
      startingAt: 1,
      reductionType: '€',
    },
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    if (newProductData.coverImage) {
      await utilsFile.generateImage(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.generateImage(newProductData.thumbImage);
    }
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    if (newProductData.coverImage) {
      await utilsFile.deleteFile(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.deleteFile(newProductData.thumbImage);
    }
  });

  describe('Create new product', async () => {
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
      expect(isModalVisible).to.equal(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);

      const productTypeDescription = await boProductsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(boProductsPage.standardProductDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createPackOfProducts', baseContext);

      await boProductsCreatePage.closeSfToolBar(page);

      const createProductMessage = await boProductsCreatePage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should go to pricing tab and set the retail price tax excl.', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setRetailPrice', baseContext);

      await boProductsCreatePage.goToTab(page, 'pricing');
      await boProductsCreateTabPricingPage.setRetailPrice(page, true, 20);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should create new specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setSpecificPrice', baseContext);

      await boProductsCreateTabPricingPage.clickOnAddSpecificPriceButton(page);

      const createProductMessage = await boProductsCreateTabPricingPage.setSpecificPrice(page, newProductData.specificPrice);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulCreationMessage);
    });
  });

  describe('Check discount in Product page', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.viewMyShop(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProductsPage', baseContext);

      await foClassicHomePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should go to the second product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSecondProductsPage', baseContext);

      await foClassicCategoryPage.goToNextPage(page);

      productsNumber = await foClassicCategoryPage.getProductsNumber(page);
      expect(productsNumber).to.not.equal(19);
    });

    it('should check the tag \'New, -€2.00\' for the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTagsInAllProductPage', baseContext);

      const flagText = await foClassicCategoryPage.getProductTag(page, productsNumber - 12);
      expect(flagText).to.contains('-€2.00')
        .and.to.contain('New');
    });

    it('should go to the created product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedProductPage', baseContext);

      await foClassicCategoryPage.goToProductPage(page, productsNumber - 12);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the tag \'New, -€2.00\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFlag', baseContext);

      const flagText = await foClassicProductPage.getProductTag(page);
      expect(flagText).to.contains('-€2.00')
        .and.to.contain('New');
    });

    it('should check the product price before and after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice', baseContext);

      const discountValue = await foClassicProductPage.getDiscountAmount(page);
      expect(discountValue).to.equal('Save €2.00');

      const finalPrice = await foClassicProductPage.getProductPrice(page);
      expect(finalPrice).to.equal('€22.00');

      const regularPrice = await foClassicProductPage.getRegularPrice(page);
      expect(regularPrice).to.equal('€24.00');
    });
  });

  // Post-condition: Delete specific price
  deleteProductTest(newProductData, `${baseContext}_postTest`);
});
