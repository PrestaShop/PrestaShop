// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import common tests
import {deleteProductTest} from '@commonTests/BO/catalog/product';

// Import BO pages
import loginCommon from '@commonTests/BO/loginBO';
import productsPage from '@pages/BO/catalog/products';
import createProductPage from '@pages/BO/catalog/products/add';
import pricingTab from '@pages/BO/catalog/products/add/pricingTab';

// Import FO pages
import {productPage as foProductPage} from '@pages/FO/classic/product';
import {homePage} from '@pages/FO/classic/home';
import {categoryPage} from '@pages/FO/classic/category';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {boDashboardPage} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_displayDiscount';

describe('FO - Product page - Product page : Display discount', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let productsNumber: number;

  const newProductData: ProductData = new ProductData({
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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    if (newProductData.coverImage) {
      await files.generateImage(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await files.generateImage(newProductData.thumbImage);
    }
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    if (newProductData.coverImage) {
      await files.deleteFile(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await files.deleteFile(newProductData.thumbImage);
    }
  });

  describe('Create new product', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );

      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, newProductData.type);

      const productTypeDescription = await productsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(productsPage.standardProductDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should create the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createPackOfProducts', baseContext);

      await createProductPage.closeSfToolBar(page);

      const createProductMessage = await createProductPage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });

    it('should go to pricing tab and set the retail price tax excl.', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setRetailPrice', baseContext);

      await createProductPage.goToTab(page, 'pricing');
      await pricingTab.setRetailPrice(page, true, 20);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should create new specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setSpecificPrice', baseContext);

      await pricingTab.clickOnAddSpecificPriceButton(page);

      const createProductMessage = await pricingTab.setSpecificPrice(page, newProductData.specificPrice);
      expect(createProductMessage).to.equal(createProductPage.successfulCreationMessage);
    });
  });

  describe('Check discount in Product page', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      // Click on preview button
      page = await createProductPage.viewMyShop(page);
      await foProductPage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProductsPage', baseContext);

      await homePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should go to the second product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSecondProductsPage', baseContext);

      await categoryPage.goToNextPage(page);

      productsNumber = await categoryPage.getProductsNumber(page);
      expect(productsNumber).to.not.equal(19);
    });

    it('should check the tag \'New, -€2.00\' for the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTagsInAllProductPage', baseContext);

      const flagText = await categoryPage.getProductTag(page, productsNumber - 12);
      expect(flagText).to.contains('-€2.00')
        .and.to.contain('New');
    });

    it('should go to the created product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedProductPage', baseContext);

      await categoryPage.goToProductPage(page, productsNumber - 12);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the tag \'New, -€2.00\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFlag', baseContext);

      const flagText = await foProductPage.getProductTag(page);
      expect(flagText).to.contains('-€2.00')
        .and.to.contain('New');
    });

    it('should check the product price before and after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice', baseContext);

      const discountValue = await foProductPage.getDiscountAmount(page);
      expect(discountValue).to.equal('Save €2.00');

      const finalPrice = await foProductPage.getProductPrice(page);
      expect(finalPrice).to.equal('€22.00');

      const regularPrice = await foProductPage.getRegularPrice(page);
      expect(regularPrice).to.equal('€24.00');
    });
  });

  // Post-condition: Delete specific price
  deleteProductTest(newProductData, `${baseContext}_postTest`);
});
