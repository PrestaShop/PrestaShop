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
  foClassicCartPage,
  foClassicModalBlockCartPage,
  foClassicProductPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_displayVolumeDiscount';

describe('FO - Product page - Product page : Display volume discount', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const newProductData: FakerProduct = new FakerProduct({
    type: 'standard',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    tax: 20,
    quantity: 30,
    specificPrice: {
      attributes: null,
      discount: 2,
      startingAt: 3,
      reductionType: '€',
    },
    status: true,
  });

  const secondSpecificPriceData: FakerProduct = new FakerProduct({
    specificPrice: {
      attributes: null,
      discount: 15,
      startingAt: 1,
      reductionType: '%',
    },
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

  describe('Create new product with specific price', async () => {
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
      await boProductsCreateTabPricingPage.setRetailPrice(page, false, 20);

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
    it('should preview product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      page = await boProductsCreatePage.previewProduct(page);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the volume discount table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscount', baseContext);

      // Check quantity for discount value
      const quantityDiscountValue = await foClassicProductPage.getQuantityDiscountValue(page);
      expect(quantityDiscountValue).to.equal(3);

      // Check unit discount value
      const unitDiscountValue = await foClassicProductPage.getDiscountValue(page);
      expect(unitDiscountValue).to.equal('€2.00');

      // Check saved value
      const savedValue = await foClassicProductPage.getSavedValue(page);
      expect(savedValue).to.equal('€6.00');
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice1', baseContext);

      const regularPrice = await foClassicProductPage.getProductPrice(page);
      expect(regularPrice).to.equal('€20.00');
    });

    it('should set the product quantity to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setQuantity', baseContext);

      // Set quantity of the product
      await foClassicProductPage.setQuantity(page, 3);

      const productQuantity = await foClassicProductPage.getProductQuantity(page);
      expect(productQuantity).to.equal(3);
    });

    it('should check the tag \'New and -€2.00\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFlag', baseContext);

      const flagText = await foClassicProductPage.getProductTag(page);
      expect(flagText).to.contains('-€2.00')
        .and.to.contain('New');
    });

    it('should check the product price before and after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice2', baseContext);

      const discountValue = await foClassicProductPage.getDiscountAmount(page);
      expect(discountValue).to.equal('Save €2.00');

      const finalPrice = await foClassicProductPage.getProductPrice(page);
      expect(finalPrice).to.equal('€18.00');

      const regularPrice = await foClassicProductPage.getRegularPrice(page);
      expect(regularPrice).to.equal('€20.00');
    });

    it('should add the product to cart and check the block cart modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicProductPage.clickOnAddToCartButton(page);

      const result = await foClassicModalBlockCartPage.getProductDetailsFromBlockCartModal(page);
      await Promise.all([
        expect(result.price).to.equal(18),
        expect(result.quantity).to.equal(3),
        expect(result.cartSubtotal).to.equal(54),
        expect(result.totalTaxIncl).to.equal(54),
      ]);
    });

    it('should remove the product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeProduct', baseContext);

      await foClassicModalBlockCartPage.proceedToCheckout(page);
      await foClassicCartPage.deleteProduct(page, 1);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(0);
    });
  });

  describe('Create a second specific price', async () => {
    it('should go back to BO > Product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create a second specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSecondSpecificPrice', baseContext);

      await boProductsCreateTabPricingPage.clickOnAddSpecificPriceButton(page);

      const createProductMessage = await boProductsCreateTabPricingPage.setSpecificPrice(
        page,
        secondSpecificPriceData.specificPrice,
      );
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulCreationMessage);
    });

    it('should go to the second tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSecondTab', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 0);
      await foClassicProductPage.reloadPage(page);
    });

    it('should preview product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      page = await boProductsCreatePage.previewProduct(page);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the tag \'New and -15%\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFlag2', baseContext);

      const flagText = await foClassicProductPage.getProductTag(page);
      expect(flagText).to.contains('-15%')
        .and.to.contain('New');
    });

    it('should check the product discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondDiscount', baseContext);

      // Check discount percentage
      const discountPercentage = await foClassicProductPage.getDiscountPercentage(page);
      expect(discountPercentage).to.equal('Save 15%');
    });

    it('should check the product price before and after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice3', baseContext);

      const finalPrice = await foClassicProductPage.getProductPrice(page);
      expect(finalPrice).to.equal('€17.00');

      const regularPrice = await foClassicProductPage.getRegularPrice(page);
      expect(regularPrice).to.equal('€20.00');
    });

    it('should check the volume discount table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVolumeDiscount', baseContext);

      // Check quantity for discount value
      const quantityDiscountValue = await foClassicProductPage.getQuantityDiscountValue(page);
      expect(quantityDiscountValue).to.equal(3);

      // Check unit discount value
      const unitDiscountValue = await foClassicProductPage.getDiscountValue(page);
      expect(unitDiscountValue).to.equal('€2.00');

      // Check saved value
      const savedValue = await foClassicProductPage.getSavedValue(page);
      expect(savedValue).to.equal('€6.00');
    });

    it('should add the product to cart and check the block cart modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await foClassicProductPage.clickOnAddToCartButton(page);

      const result = await foClassicModalBlockCartPage.getProductDetailsFromBlockCartModal(page);
      await Promise.all([
        expect(result.price).to.equal(17),
        expect(result.quantity).to.equal(1),
        expect(result.cartSubtotal).to.equal(17),
        expect(result.totalTaxIncl).to.equal(17),
      ]);
    });

    it('should remove the product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeProduct2', baseContext);

      await foClassicModalBlockCartPage.proceedToCheckout(page);
      await foClassicCartPage.deleteProduct(page, 1);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(0);
    });
  });

  // Post-condition: Delete specific price
  deleteProductTest(newProductData, `${baseContext}_postTest`);
});
