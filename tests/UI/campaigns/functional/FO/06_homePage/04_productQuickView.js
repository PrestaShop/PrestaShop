require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');

// Import common tests
const {createProductTest, deleteProductTest} = require('@commonTests/BO/catalog/createDeleteProduct');

// Import FO pages
const homePage = require('@pages/FO/home');
const categoryPage = require('@pages/FO/category');

// Import Data
const {Products} = require('@data/demo/products');
const ProductFaker = require('@data/faker/product');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_homePage_productQuickView';

let browserContext;
let page;

const defaultAttributes = {
  dimension: '40x60cm',
  quantity: 1,
};
const attributes = {
  dimension: '60x90cm',
  quantity: 4,
};

// Data to create product out of stock not allowed
const productOutOfStockNotAllowed = new ProductFaker({
  name: 'Out of stock not allowed',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: -15,
  minimumQuantity: 1,
  lowStockLevel: 3,
  behaviourOutOfStock: 'Deny orders',
});

/*
Pre-condition:
- Create product out of stock not allowed
Scenario:
- Quick view product with combinations
- Quick view simple product
- Quick view customized product
- Quick view product out of stock not allowed
Post-condition:
- Delete created product
 */
describe('FO - Home Page : Product quick view', async () => {
  // Pre-condition : Create product out of stock not allowed
  createProductTest(productOutOfStockNotAllowed, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Quick view product with combinations', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);
      const result = await homePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it(`should quick view the product '${Products.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct1', baseContext);

      await homePage.quickViewProduct(page, 3);

      const isModalVisible = await homePage.isQuickViewProductModalVisible(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      let result = await homePage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_6.name),
        expect(result.price).to.equal(Products.demo_6.priceDimension4060),
        expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
        expect(result.shortDescription).to.equal(Products.demo_6.shortDescription),
        expect(result.coverImage).to.contains(Products.demo_6.coverImage),
        expect(result.thumbImage).to.contains(Products.demo_6.thumbnailImage),
      ]);

      result = await homePage.getSelectedAttributesFromQuickViewModal(page, defaultAttributes);
      await expect(result.dimension).to.equal(defaultAttributes.dimension);
    });

    it('should change combination and check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeCombination', baseContext);

      await homePage.changeAttributes(page, attributes);

      let result = await homePage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_6.name),
        expect(result.price).to.equal(Products.demo_6.priceDimension6090),
        expect(result.shortDescription).to.equal(Products.demo_6.shortDescription),
        expect(result.coverImage).to.contains(Products.demo_6.coverImage),
        expect(result.thumbImage).to.contains(Products.demo_6.thumbnailImage),
      ]);

      result = await homePage.getSelectedAttributesFromQuickViewModal(page, attributes);
      await expect(result.dimension).to.equal(attributes.dimension);
    });

    it('should change the product quantity and click on add to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await homePage.changeQuantity(page, attributes.quantity);

      await homePage.addToCartByQuickView(page);

      const isVisible = await homePage.isBlockCartModalVisible(page);
      await expect(isVisible).to.be.true;
    });

    it('should click on continue shopping and check that the modal is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnContinueShopping', baseContext);

      const isNotVisible = await homePage.continueShopping(page);
      await expect(isNotVisible).to.be.true;
    });
  });

  describe('Quick view simple product', async () => {
    it(`should quick view the product '${Products.demo_11.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct2', baseContext);

      await homePage.quickViewProduct(page, 6);

      const isModalVisible = await homePage.isQuickViewProductModalVisible(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSimpleProductInformation', baseContext);

      const result = await homePage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_11.name),
        expect(result.price).to.equal(Products.demo_11.finalPrice),
        expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
        expect(result.shortDescription).to.equal(Products.demo_11.shortDescription),
        expect(result.coverImage).to.contains(Products.demo_11.coverImage),
        expect(result.thumbImage).to.contains(Products.demo_11.thumbnailImage),
      ]);
    });

    it('should change the product quantity and click on add to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await homePage.changeQuantity(page, attributes.quantity);

      await homePage.addToCartByQuickView(page);

      const isVisible = await homePage.isBlockCartModalVisible(page);
      await expect(isVisible).to.be.true;
    });

    it('should click on continue shopping and check that the modal is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnContinueShopping2', baseContext);

      const isNotVisible = await homePage.continueShopping(page);
      await expect(isNotVisible).to.be.true;
    });
  });

  describe('Quick view customized product', async () => {
    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProducts', baseContext);

      await homePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
      await expect(isCategoryPageVisible, 'Home category page was not opened').to.be.true;
    });

    it(`should go to the second page and quick view the product '${Products.demo_14.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewCustomizedProduct', baseContext);

      await categoryPage.goToNextPage(page);

      await categoryPage.quickViewProduct(page, 7);

      const isModalVisible = await categoryPage.isQuickViewProductModalVisible(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomizedProductInformation', baseContext);

      const result = await homePage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_14.name),
        expect(result.price).to.equal(Products.demo_14.priceTaxIncl),
        expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
        expect(result.shortDescription).to.equal(Products.demo_14.shortDescription),
        expect(result.coverImage).to.contains(Products.demo_14.coverImage),
        expect(result.thumbImage).to.contains(Products.demo_14.thumbnailImage),
      ]);
    });

    it('should check that \'Add to cart\' button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddToCartButton', baseContext);

      const isEnabled = await homePage.isAddToCartButtonEnabled(page);
      await expect(isEnabled, 'Add to cart button is not disabled').to.be.false;
    });

    it('should close the quick view modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeQuickOptionModal', baseContext);

      const isQuickViewModalClosed = await homePage.closeQuickViewModal(page);
      await expect(isQuickViewModalClosed).to.be.true;
    });
  });

  describe('Quick view product out of stock not allowed', async () => {
    it(`should quick view the product '${productOutOfStockNotAllowed.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProductOutOfStock', baseContext);

      await categoryPage.quickViewProduct(page, 8);

      const isModalVisible = await categoryPage.isQuickViewProductModalVisible(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should check that \'Add to cart\' button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddToCartButton2', baseContext);

      const isEnabled = await homePage.isAddToCartButtonEnabled(page);
      await expect(isEnabled, 'Add to cart button is not disabled').to.be.false;
    });

    it('should check the product availability', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductAvailability', baseContext);

      const availability = await homePage.getProductAvailabilityText(page);
      await expect(availability).to.contains('Out-of-Stock');
    });

    it('should close the quick view modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeQuickOptionModal2', baseContext);

      const isQuickViewModalClosed = await homePage.closeQuickViewModal(page);
      await expect(isQuickViewModalClosed).to.be.true;
    });
  });

  // Post-condition : Delete the created product
  deleteProductTest(productOutOfStockNotAllowed, `${baseContext}_postTest`);
});
