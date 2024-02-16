// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import commonTests
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import {installHummingbird, uninstallHummingbird} from '@commonTests/FO/hummingbird';

// Import FO pages
import homePage from '@pages/FO/hummingbird/home';
import categoryPage from '@pages/FO/hummingbird/category';

// Import data
import Products from '@data/demo/products';
import ProductData from '@data/faker/product';
import {ProductAttribute} from '@data/types/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_homePage_productQuickView';

/*
Pre-condition:
- Create product out of stock not allowed
- Install hummingbird theme
Scenario:
- Quick view product with combinations
- Quick view simple product
- Quick view customized product
- Quick view product out of stock not allowed
Post-condition:
- Delete created product
- Uninstall hummingbird theme
 */
describe('FO - Home Page : Product quick view', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const defaultAttributes: ProductAttribute = {
    name: 'dimension',
    value: '40x60cm',
  };
  const attributes: ProductAttribute = {
    name: 'dimension',
    value: '60x90cm',
  };
  const attributesQty: number = 4;

  // Data to create product out of stock not allowed
  const productOutOfStockNotAllowed: ProductData = new ProductData({
    name: 'Out of stock not allowed',
    type: 'standard',
    taxRule: 'No tax',
    quantity: -15,
    minimumQuantity: 1,
    lowStockLevel: 3,
    behaviourOutOfStock: 'Deny orders',
  });

  // Pre-condition : Create product out of stock not allowed
  createProductTest(productOutOfStockNotAllowed, `${baseContext}_preTest_0`);

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile('../../admin-dev/hummingbird.zip');
  });

  describe('Quick view product with combinations', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it(`should quick view the product '${Products.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct1', baseContext);

      await homePage.quickViewProduct(page, 3);

      const isModalVisible = await homePage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      const result = await homePage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_6.name),
        expect(result.price).to.equal(Products.demo_6.combinations[0].price),
        expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
        expect(result.shortDescription).to.equal(Products.demo_6.summary),
        expect(result.coverImage).to.contains(Products.demo_6.coverImage),
        expect(result.thumbImage).to.contains(Products.demo_6.thumbImage),
      ]);

      const resultAttributes = await homePage.getSelectedAttributesFromQuickViewModal(page, defaultAttributes);
      expect(resultAttributes.length).to.be.equal(1);
      expect(resultAttributes[0].name).to.be.equal(defaultAttributes.name);
      expect(resultAttributes[0].value).to.be.equal(defaultAttributes.value);
    });

    it('should change combination and check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeCombination', baseContext);

      await homePage.changeAttributes(page, attributes);

      const result = await homePage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_6.name),
        expect(result.price).to.equal(Products.demo_6.combinations[1].price),
        expect(result.shortDescription).to.equal(Products.demo_6.summary),
        expect(result.coverImage).to.contains(Products.demo_6.coverImage),
        expect(result.thumbImage).to.contains(Products.demo_6.thumbImage),
      ]);

      const resultAttributes = await homePage.getSelectedAttributesFromQuickViewModal(page, attributes);
      expect(resultAttributes.length).to.be.equal(1);
      expect(resultAttributes[0].name).to.be.equal(attributes.name);
      expect(resultAttributes[0].value).to.be.equal(attributes.value);
    });

    it('should change the product quantity and click on add to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await homePage.changeQuantity(page, attributesQty);
      await homePage.addToCartByQuickView(page);

      const isVisible = await homePage.isBlockCartModalVisible(page);
      expect(isVisible).to.eq(true);
    });

    it('should click on continue shopping and check that the modal is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnContinueShopping', baseContext);

      const isNotVisible = await homePage.continueShopping(page);
      expect(isNotVisible).to.eq(true);
    });
  });

  describe('Quick view simple product', async () => {
    it(`should quick view the product '${Products.demo_11.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct2', baseContext);

      await homePage.quickViewProduct(page, 6);

      const isModalVisible = await homePage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSimpleProductInformation', baseContext);

      const result = await homePage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_11.name),
        expect(result.price).to.equal(Products.demo_11.finalPrice),
        expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
        expect(result.shortDescription).to.equal(Products.demo_11.summary),
        expect(result.coverImage).to.contains(Products.demo_11.coverImage),
        expect(result.thumbImage).to.contains(Products.demo_11.thumbImage),
      ]);
    });

    it('should change the product quantity and click on add to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await homePage.changeQuantity(page, attributesQty);
      await homePage.addToCartByQuickView(page);

      const isVisible = await homePage.isBlockCartModalVisible(page);
      expect(isVisible).to.eq(true);
    });

    it('should click on continue shopping and check that the modal is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnContinueShopping2', baseContext);

      const isNotVisible = await homePage.continueShopping(page);
      expect(isNotVisible).to.eq(true);
    });
  });

  describe('Quick view customized product', async () => {
    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProducts', baseContext);

      await homePage.clickOnAllProductsButton(page);

      const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it(`should go to the second page and quick view the product '${Products.demo_14.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewCustomizedProduct', baseContext);

      await categoryPage.goToNextPage(page);
      await categoryPage.quickViewProduct(page, 7);

      const isModalVisible = await categoryPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomizedProductInformation', baseContext);

      const result = await homePage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_14.name),
        expect(result.price).to.equal(Products.demo_14.price),
        expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
        expect(result.shortDescription).to.equal(Products.demo_14.summary),
        expect(result.coverImage).to.contains(Products.demo_14.coverImage),
        expect(result.thumbImage).to.contains(Products.demo_14.thumbImage),
      ]);
    });

    it('should check that \'Add to cart\' button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddToCartButton', baseContext);

      const isEnabled = await homePage.isAddToCartButtonEnabled(page);
      expect(isEnabled, 'Add to cart button is not disabled').to.eq(false);
    });

    it('should close the quick view modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeQuickOptionModal', baseContext);

      const isQuickViewModalClosed = await homePage.closeQuickViewModal(page);
      expect(isQuickViewModalClosed).to.eq(true);
    });
  });

  describe('Quick view product out of stock not allowed', async () => {
    it(`should quick view the product '${productOutOfStockNotAllowed.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProductOutOfStock', baseContext);

      await categoryPage.quickViewProduct(page, 8);

      const isModalVisible = await categoryPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should check that \'Add to cart\' button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddToCartButton2', baseContext);

      const isEnabled = await homePage.isAddToCartButtonEnabled(page);
      expect(isEnabled, 'Add to cart button is not disabled').to.eq(false);
    });

    it('should check the product availability', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductAvailability', baseContext);

      const availability = await homePage.getProductAvailabilityText(page);
      expect(availability).to.contains('Out-of-Stock');
    });

    it('should close the quick view modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeQuickOptionModal2', baseContext);

      const isQuickViewModalClosed = await homePage.closeQuickViewModal(page);
      expect(isQuickViewModalClosed).to.eq(true);
    });
  });

  // Post-condition : Delete the created product
  deleteProductTest(productOutOfStockNotAllowed, `${baseContext}_postTest_0`);

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_1`);
});
