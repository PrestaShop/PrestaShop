// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import pages
import homePage from '@pages/FO/hummingbird/home';
import cartPage from '@pages/FO/hummingbird/cart';
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';
import quickViewModal from '@pages/FO/hummingbird/modal/quickView';
import productPage from '@pages/FO/hummingbird/product';
import searchResultsPage from '@pages/FO/hummingbird/searchResults';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_productPage_productPage_addToCart';

describe('FO - Product page - Product page : Add to cart', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const qtyProductPage: number = 5;
  const qtyQuickView: number = 100;
  const qtyQuickAdd: number = 1;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile('../../admin-dev/hummingbird.zip');
  });

  describe('Add to cart', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it(`should search the product "${Products.demo_12.name}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchDemo12', baseContext);

      await homePage.searchProduct(page, Products.demo_12.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageDemo12', baseContext);

      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_12.name);
    });

    it('should add the product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await productPage.addProductToTheCart(page, qtyProductPage, [], null);

      const productDetails = await blockCartModal.getProductDetailsFromBlockCartModal(page);
      expect(productDetails.quantity).to.be.equal(qtyProductPage);
      expect(productDetails.name).to.be.equal(Products.demo_12.name);
    });

    it('should close the cart modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeCartModal', baseContext);

      const isModalClosed = await blockCartModal.closeBlockCartModal(page);
      expect(isModalClosed).to.be.equal(true);
    });

    it('should return to the home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchDemo6', baseContext);

      await productPage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should add product to cart by quick view', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCartByQuickView', baseContext);

      await homePage.quickViewProduct(page, 1);
      await quickViewModal.setQuantity(page, qtyQuickView);
      await quickViewModal.addToCartByQuickView(page);

      const productDetails = await blockCartModal.getProductDetailsFromBlockCartModal(page);
      expect(productDetails.quantity).to.be.equal(qtyQuickView);
      expect(productDetails.name).to.be.equal(Products.demo_1.name);
    });

    it('should close the cart modal from quickview', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeCartModalQuickview', baseContext);

      const isModalClosed = await blockCartModal.closeBlockCartModal(page);
      expect(isModalClosed).to.be.equal(true);
    });

    it('should add product to cart by quick add', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCartByQuickAdd', baseContext);

      await homePage.addProductToCart(page, 1);

      const productDetails = await blockCartModal.getProductDetailsFromBlockCartModal(page);
      expect(productDetails.quantity).to.be.equal(qtyQuickView + qtyQuickAdd);
      expect(productDetails.name).to.be.equal(Products.demo_1.name);
    });

    it('should proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.eq(cartPage.pageTitle);
    });

    it('should remove products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeProducts', baseContext);

      await cartPage.deleteProduct(page, 2);
      await cartPage.deleteProduct(page, 1);

      const productCount = await cartPage.getProductsNumber(page);
      expect(productCount).to.eq(0);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
