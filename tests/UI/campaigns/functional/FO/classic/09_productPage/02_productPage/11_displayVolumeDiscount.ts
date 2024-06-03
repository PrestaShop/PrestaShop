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
import {productPage, productPage as foProductPage} from '@pages/FO/classic/product';
import {blockCartModal} from '@pages/FO/classic/modal/blockCart';
import {cartPage} from '@pages/FO/classic/cart';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  FakerProduct,
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

  describe('Create new product with specific price', async () => {
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
      await pricingTab.setRetailPrice(page, false, 20);

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
    it('should preview product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      page = await createProductPage.previewProduct(page);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the volume discount table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscount', baseContext);

      // Check quantity for discount value
      const quantityDiscountValue = await foProductPage.getQuantityDiscountValue(page);
      expect(quantityDiscountValue).to.equal(3);

      // Check unit discount value
      const unitDiscountValue = await foProductPage.getDiscountValue(page);
      expect(unitDiscountValue).to.equal('€2.00');

      // Check saved value
      const savedValue = await foProductPage.getSavedValue(page);
      expect(savedValue).to.equal('€6.00');
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice1', baseContext);

      const regularPrice = await foProductPage.getProductPrice(page);
      expect(regularPrice).to.equal('€20.00');
    });

    it('should set the product quantity to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setQuantity', baseContext);

      // Set quantity of the product
      await foProductPage.setQuantity(page, 3);

      const productQuantity = await foProductPage.getProductQuantity(page);
      expect(productQuantity).to.equal(3);
    });

    it('should check the tag \'New and -€2.00\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFlag', baseContext);

      const flagText = await foProductPage.getProductTag(page);
      expect(flagText).to.contains('-€2.00')
        .and.to.contain('New');
    });

    it('should check the product price before and after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice2', baseContext);

      const discountValue = await foProductPage.getDiscountAmount(page);
      expect(discountValue).to.equal('Save €2.00');

      const finalPrice = await foProductPage.getProductPrice(page);
      expect(finalPrice).to.equal('€18.00');

      const regularPrice = await foProductPage.getRegularPrice(page);
      expect(regularPrice).to.equal('€20.00');
    });

    it('should add the product to cart and check the block cart modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foProductPage.clickOnAddToCartButton(page);

      const result = await blockCartModal.getProductDetailsFromBlockCartModal(page);
      await Promise.all([
        expect(result.price).to.equal(18),
        expect(result.quantity).to.equal(3),
        expect(result.cartSubtotal).to.equal(54),
        expect(result.totalTaxIncl).to.equal(54),
      ]);
    });

    it('should remove the product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeProduct', baseContext);

      await blockCartModal.proceedToCheckout(page);
      await cartPage.deleteProduct(page, 1);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(0);
    });
  });

  describe('Create a second specific price', async () => {
    it('should go back to BO > Product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should create a second specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSecondSpecificPrice', baseContext);

      await pricingTab.clickOnAddSpecificPriceButton(page);

      const createProductMessage = await pricingTab.setSpecificPrice(page, secondSpecificPriceData.specificPrice);
      expect(createProductMessage).to.equal(createProductPage.successfulCreationMessage);
    });

    it('should go to the second tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSecondTab', baseContext);

      page = await foProductPage.changePage(browserContext, 0);
      await foProductPage.reloadPage(page);
    });

    it('should preview product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      page = await createProductPage.previewProduct(page);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the tag \'New and -15%\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFlag2', baseContext);

      const flagText = await foProductPage.getProductTag(page);
      expect(flagText).to.contains('-15%')
        .and.to.contain('New');
    });

    it('should check the product discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondDiscount', baseContext);

      // Check discount percentage
      const discountPercentage = await productPage.getDiscountPercentage(page);
      expect(discountPercentage).to.equal('Save 15%');
    });

    it('should check the product price before and after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice3', baseContext);

      const finalPrice = await foProductPage.getProductPrice(page);
      expect(finalPrice).to.equal('€17.00');

      const regularPrice = await foProductPage.getRegularPrice(page);
      expect(regularPrice).to.equal('€20.00');
    });

    it('should check the volume discount table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVolumeDiscount', baseContext);

      // Check quantity for discount value
      const quantityDiscountValue = await foProductPage.getQuantityDiscountValue(page);
      expect(quantityDiscountValue).to.equal(3);

      // Check unit discount value
      const unitDiscountValue = await foProductPage.getDiscountValue(page);
      expect(unitDiscountValue).to.equal('€2.00');

      // Check saved value
      const savedValue = await foProductPage.getSavedValue(page);
      expect(savedValue).to.equal('€6.00');
    });

    it('should add the product to cart and check the block cart modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await foProductPage.clickOnAddToCartButton(page);

      const result = await blockCartModal.getProductDetailsFromBlockCartModal(page);
      await Promise.all([
        expect(result.price).to.equal(17),
        expect(result.quantity).to.equal(1),
        expect(result.cartSubtotal).to.equal(17),
        expect(result.totalTaxIncl).to.equal(17),
      ]);
    });

    it('should remove the product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeProduct2', baseContext);

      await blockCartModal.proceedToCheckout(page);
      await cartPage.deleteProduct(page, 1);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(0);
    });
  });

  // Post-condition: Delete specific price
  deleteProductTest(newProductData, `${baseContext}_postTest`);
});
