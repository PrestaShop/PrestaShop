// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import pages
import {homePage} from '@pages/FO/classic/home';
import {searchResultsPage} from '@pages/FO/classic/searchResults';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_menuAndNavigation_navigationAndDisplay_quickViewProducts';

/*
- Go to FO > quick view the product demo_3 and check information
quick view the product demo_12 and check information
quick view the product demo_14 and check information
 */
describe('FO - Navigation and display : Quick view products', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe(`Quick view the product '${Products.demo_3.name}'`, async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it(`should search for the product '${Products.demo_3.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct1', baseContext);

      await homePage.searchProduct(page, Products.demo_3.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it(`should quick view the product '${Products.demo_3.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct1', baseContext);

      await searchResultsPage.quickViewProduct(page, 1);

      const isModalVisible = await searchResultsPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation1', baseContext);

      const result = await homePage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_3.name),
        expect(result.price).to.equal(Products.demo_3.finalPrice),
        expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
        expect(result.shortDescription).to.equal(Products.demo_3.summary),
        expect(result.coverImage).to.contains(Products.demo_3.coverImage),
        expect(result.thumbImage).to.contains(Products.demo_3.thumbImage),
      ]);
    });

    it('should add product to cart and check that the block cart modal is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnContinueShopping', baseContext);

      await homePage.addToCartByQuickView(page);

      const isVisible = await homePage.isBlockCartModalVisible(page);
      expect(isVisible).to.eq(true);
    });

    it('should click on continue shopping button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'continueShopping', baseContext);

      const isModalNotVisible = await homePage.continueShopping(page);
      expect(isModalNotVisible).to.eq(true);
    });
  });

  describe(`Quick view the product '${Products.demo_12.name}'`, async () => {
    it(`should search for the product '${Products.demo_12.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct2', baseContext);

      await homePage.searchProduct(page, Products.demo_12.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it(`should quick view the product '${Products.demo_12.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct2', baseContext);

      await searchResultsPage.quickViewProduct(page, 1);

      const isModalVisible = await searchResultsPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation2', baseContext);

      const result = await homePage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_12.name),
        expect(result.price).to.equal(Products.demo_12.price),
        expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
        expect(result.shortDescription).to.equal(Products.demo_12.summary),
        expect(result.coverImage).to.contains(Products.demo_12.coverImage),
        expect(result.thumbImage).to.contains(Products.demo_12.thumbImage),
      ]);
    });

    it('should close the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeModal1', baseContext);

      const isQuickViewModalClosed = await homePage.closeQuickViewModal(page);
      expect(isQuickViewModalClosed).to.eq(true);
    });
  });

  describe(`Quick view the product '${Products.demo_14.name}'`, async () => {
    it(`should search for the product '${Products.demo_12.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct3', baseContext);

      await homePage.searchProduct(page, Products.demo_14.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it(`should quick view the product '${Products.demo_14.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct3', baseContext);

      await searchResultsPage.quickViewProduct(page, 1);

      const isModalVisible = await searchResultsPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation3', baseContext);

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

    it('should close the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeModal2', baseContext);

      const isQuickViewModalClosed = await homePage.closeQuickViewModal(page);
      expect(isQuickViewModalClosed).to.eq(true);
    });
  });
});
