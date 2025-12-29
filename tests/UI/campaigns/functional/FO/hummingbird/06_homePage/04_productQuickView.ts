import testContext from '@utils/testContext';
import {expect} from 'chai';

import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataProducts,
  FakerProduct,
  foHummingbirdCategoryPage,
  foHummingbirdHomePage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdModalQuickViewPage,
  type Page,
  type ProductAttribute,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_homePage_productQuickView';

/*
 * Pre-condition:
 * - Create product out of stock not allowed
 * - Install hummingbird theme
 * Scenario:
 * - Quick view product with combinations
 * - Quick view simple product
 * - Quick view customized product
 * - Quick view product out of stock not allowed
 * Post-condition:
 * - Delete created product
 * - Uninstall hummingbird theme
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
  const productOutOfStockNotAllowed: FakerProduct = new FakerProduct({
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
  enableTheme('hummingbird', `${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Quick view product with combinations', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it(`should quick view the product '${dataProducts.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct1', baseContext);

      await foHummingbirdHomePage.quickViewProduct(page, 3);

      const isModalVisible = await foHummingbirdModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      const result = await foHummingbirdModalQuickViewPage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(dataProducts.demo_6.name),
        expect(result.price).to.equal(dataProducts.demo_6.combinations[0].price),
        expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
        expect(result.shortDescription).to.equal(dataProducts.demo_6.summary),
        expect(result.coverImage).to.contains(dataProducts.demo_6.coverImage),
        expect(result.thumbImage).to.contains(dataProducts.demo_6.thumbImage),
      ]);

      const resultAttributes = await foHummingbirdModalQuickViewPage.getSelectedAttributesFromQuickViewModal(
        page,
        defaultAttributes,
      );
      expect(resultAttributes.length).to.be.equal(1);
      expect(resultAttributes[0].name).to.be.equal(defaultAttributes.name);
      expect(resultAttributes[0].value).to.be.equal(defaultAttributes.value);
    });

    it('should change combination and check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeCombination', baseContext);

      await foHummingbirdModalQuickViewPage.setAttribute(page, attributes);

      const result = await foHummingbirdModalQuickViewPage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(dataProducts.demo_6.name),
        expect(result.price).to.equal(dataProducts.demo_6.combinations[1].price),
        expect(result.shortDescription).to.equal(dataProducts.demo_6.summary),
        expect(result.coverImage).to.contains(dataProducts.demo_6.coverImage),
        expect(result.thumbImage).to.contains(dataProducts.demo_6.thumbImage),
      ]);

      const resultAttributes = await foHummingbirdModalQuickViewPage.getSelectedAttributesFromQuickViewModal(
        page,
        attributes,
      );
      expect(resultAttributes.length).to.be.equal(1);
      expect(resultAttributes[0].name).to.be.equal(attributes.name);
      expect(resultAttributes[0].value).to.be.equal(attributes.value);
    });

    it('should change the product quantity and click on add to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdModalQuickViewPage.setQuantity(page, attributesQty);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);

      const isVisible = await foHummingbirdModalBlockCartPage.isBlockCartModalVisible(page);
      expect(isVisible).to.equal(true);
    });

    it('should click on continue shopping and check that the modal is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnContinueShopping', baseContext);

      const isNotVisible = await foHummingbirdModalBlockCartPage.continueShopping(page);
      expect(isNotVisible).to.equal(true);
    });
  });

  describe('Quick view simple product', async () => {
    it('should go to the All products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFeaturedProductsPage', baseContext);

      await foHummingbirdHomePage.goToAllProductsPage(page, 'ps-featuredproducts');

      const isCategoryPageVisible = await foHummingbirdCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible).to.eq(true);
    });

    it(`should quick view the product '${dataProducts.demo_11.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct2', baseContext);

      await foHummingbirdCategoryPage.quickViewProduct(page, 6);

      const isModalVisible = await foHummingbirdModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSimpleProductInformation', baseContext);

      const result = await foHummingbirdModalQuickViewPage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(dataProducts.demo_11.name),
        expect(result.price).to.equal(dataProducts.demo_11.finalPrice),
        expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
        expect(result.shortDescription).to.equal(dataProducts.demo_11.summary),
        expect(result.coverImage).to.contains(dataProducts.demo_11.coverImage),
        expect(result.thumbImage).to.contains(dataProducts.demo_11.thumbImage),
      ]);
    });

    it('should change the product quantity and click on add to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await foHummingbirdModalQuickViewPage.setQuantity(page, attributesQty);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);

      const isVisible = await foHummingbirdModalBlockCartPage.isBlockCartModalVisible(page);
      expect(isVisible).to.equal(true);
    });

    it('should click on continue shopping and check that the modal is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnContinueShopping2', baseContext);

      const isNotVisible = await foHummingbirdModalBlockCartPage.continueShopping(page);
      expect(isNotVisible).to.equal(true);
    });
  });

  describe('Quick view customized product', async () => {
    it(`should go to the second page and quick view the product '${dataProducts.demo_14.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewCustomizedProduct', baseContext);

      await foHummingbirdCategoryPage.goToNextPage(page);
      await foHummingbirdCategoryPage.quickViewProduct(page, 7);

      const isModalVisible = await foHummingbirdModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomizedProductInformation', baseContext);

      const result = await foHummingbirdModalQuickViewPage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(dataProducts.demo_14.name),
        expect(result.price).to.equal(dataProducts.demo_14.price),
        expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
        expect(result.shortDescription).to.equal(dataProducts.demo_14.summary),
        expect(result.coverImage).to.contains(dataProducts.demo_14.coverImage),
        expect(result.thumbImage).to.contains(dataProducts.demo_14.thumbImage),
      ]);
    });

    it('should check that \'Add to cart\' button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddToCartButton', baseContext);

      const isEnabled = await foHummingbirdModalQuickViewPage.isAddToCartButtonEnabled(page);
      expect(isEnabled, 'Add to cart button is not disabled').to.equal(false);
    });

    it('should close the quick view modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeQuickOptionModal', baseContext);

      const isQuickViewModalClosed = await foHummingbirdModalQuickViewPage.closeQuickViewModal(page);
      expect(isQuickViewModalClosed).to.equal(true);
    });
  });

  describe('Quick view product out of stock not allowed', async () => {
    it(`should quick view the product '${productOutOfStockNotAllowed.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProductOutOfStock', baseContext);

      await foHummingbirdCategoryPage.quickViewProduct(page, 8);

      const isModalVisible = await foHummingbirdModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should check that \'Add to cart\' button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddToCartButton2', baseContext);

      const isEnabled = await foHummingbirdModalQuickViewPage.isAddToCartButtonEnabled(page);
      expect(isEnabled, 'Add to cart button is not disabled').to.equal(false);
    });

    it('should check the product availability', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductAvailability', baseContext);

      const availability = await foHummingbirdModalQuickViewPage.getProductAvailabilityText(page);
      expect(availability).to.contains('Out-of-Stock');
    });

    it('should close the quick view modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeQuickOptionModal2', baseContext);

      const isQuickViewModalClosed = await foHummingbirdModalQuickViewPage.closeQuickViewModal(page);
      expect(isQuickViewModalClosed).to.equal(true);
    });
  });

  // Post-condition : Delete the created product
  deleteProductTest(productOutOfStockNotAllowed, `${baseContext}_postTest_0`);

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest_1`);
});
