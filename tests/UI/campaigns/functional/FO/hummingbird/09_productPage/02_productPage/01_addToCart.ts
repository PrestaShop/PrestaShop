import testContext from '@utils/testContext';
import {expect} from 'chai';

import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataProducts,
  foHummingbirdCartPage,
  foHummingbirdHomePage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdModalQuickViewPage,
  foHummingbirdProductPage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_productPage_productPage_addToCart';

describe('FO - Product page - Product page : Add to cart', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const qtyProductPage: number = 5;
  const qtyQuickView: number = 100;
  const qtyQuickAdd: number = 1;

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Add to cart', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it(`should search the product "${dataProducts.demo_12.name}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchDemo12', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_12.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageDemo12', baseContext);

      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_12.name);
    });

    it('should add the product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdProductPage.addProductToTheCart(page, qtyProductPage, [], null);

      const productDetails = await foHummingbirdModalBlockCartPage.getProductDetailsFromBlockCartModal(page);
      expect(productDetails.quantity).to.be.equal(qtyProductPage);
      expect(productDetails.name).to.be.equal(dataProducts.demo_12.name);
    });

    it('should close the cart modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeCartModal', baseContext);

      const isModalClosed = await foHummingbirdModalBlockCartPage.closeBlockCartModal(page);
      expect(isModalClosed).to.be.equal(true);
    });

    it('should return to the home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchDemo6', baseContext);

      await foHummingbirdProductPage.goToHomePage(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should add product to cart by quick view', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCartByQuickView', baseContext);

      await foHummingbirdHomePage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.setQuantity(page, qtyQuickView);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);

      const productDetails = await foHummingbirdModalBlockCartPage.getProductDetailsFromBlockCartModal(page);
      expect(productDetails.quantity).to.be.equal(qtyQuickView);
      expect(productDetails.name).to.be.equal(dataProducts.demo_1.name);
    });

    it('should close the cart modal from quickview', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeCartModalQuickview', baseContext);

      const isModalClosed = await foHummingbirdModalBlockCartPage.closeBlockCartModal(page);
      expect(isModalClosed).to.be.equal(true);
    });

    it('should add product to cart by quick add', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCartByQuickAdd', baseContext);

      await foHummingbirdHomePage.addProductToCart(page, 1);

      const productDetails = await foHummingbirdModalBlockCartPage.getProductDetailsFromBlockCartModal(page);
      expect(productDetails.quantity).to.be.equal(qtyQuickView + qtyQuickAdd);
      expect(productDetails.name).to.be.equal(dataProducts.demo_1.name);
    });

    it('should proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.eq(foHummingbirdCartPage.pageTitle);
    });

    it('should remove products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeProducts', baseContext);

      await foHummingbirdCartPage.deleteProduct(page, 2);
      await foHummingbirdCartPage.deleteProduct(page, 1);

      const productCount = await foHummingbirdCartPage.getProductsNumber(page);
      expect(productCount).to.eq(0);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
