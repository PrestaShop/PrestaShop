// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import pages
import {homePage} from '@pages/FO/classic/home';
import {cartPage} from '@pages/FO/classic/cart';
import {blockCartModal} from '@pages/FO/classic/modal/blockCart';
import {quickViewModal} from '@pages/FO/classic/modal/quickView';
import {productPage} from '@pages/FO/classic/product';
import {searchResultsPage} from '@pages/FO/classic/searchResults';
import {categoryPage} from '@pages/FO/classic/category';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  dataProducts,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_addToCart';

describe('FO - Product page - Product page : Add to cart', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const qtyProductPage: number = 5;
  const qtyQuickView: number = 100;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it(`should search the product "${dataProducts.demo_12.name}"`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchDemo12', baseContext);

    await homePage.searchProduct(page, dataProducts.demo_12.name);

    const pageTitle = await searchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(searchResultsPage.pageTitle);
  });

  it('should go to the product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageDemo12', baseContext);

    await searchResultsPage.goToProductPage(page, 1);

    const pageTitle = await productPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_12.name);
  });

  it('should add the product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await productPage.addProductToTheCart(page, qtyProductPage, [], null);

    const productDetails = await blockCartModal.getProductDetailsFromBlockCartModal(page);
    expect(productDetails.quantity).to.equal(qtyProductPage);
    expect(productDetails.name).to.equal(dataProducts.demo_12.name);
  });

  it('should click on continue shopping button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'continueShopping', baseContext);

    const isModalClosed = await blockCartModal.continueShopping(page);
    expect(isModalClosed).to.equal(true);
  });

  it('should go back to the home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goHomePage', baseContext);

    await productPage.goToHomePage(page);

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it('should go to all products page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAllProductsPage', baseContext);

    await homePage.goToAllProductsPage(page);

    const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
    expect(isCategoryPageVisible, 'Home category page was not opened').to.equal(true);
  });

  it('should quick view the first product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct', baseContext);

    await categoryPage.quickViewProduct(page, 1);

    const isModalVisible = await quickViewModal.isQuickViewProductModalVisible(page);
    expect(isModalVisible).to.equal(true);
  });

  it('should add product to cart by quick view', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCartByQuickView', baseContext);

    await quickViewModal.setQuantity(page, qtyQuickView);
    await quickViewModal.addToCartByQuickView(page);

    const productDetails = await blockCartModal.getProductDetailsFromBlockCartModal(page);
    expect(productDetails.quantity).to.equal(qtyQuickView);
    expect(productDetails.name).to.equal(dataProducts.demo_1.name);
  });

  it('should proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

    await blockCartModal.proceedToCheckout(page);

    const pageTitle = await cartPage.getPageTitle(page);
    expect(pageTitle).to.equal(cartPage.pageTitle);
  });

  it('should change the product quantity from cart page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeProductQuantity', baseContext);

    await cartPage.editProductQuantity(page, 1, 200);

    const notificationNumber = await cartPage.getCartNotificationsNumber(page);
    expect(notificationNumber).to.equal(300);
  });

  it('should delete the first product from the cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstProduct', baseContext);

    await cartPage.deleteProduct(page, 1);

    const notificationNumber = await cartPage.getCartNotificationsNumber(page);
    expect(notificationNumber).to.equal(100);
  });

  it('should delete the first product from the cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteSecondProduct', baseContext);

    await cartPage.deleteProduct(page, 1);

    const notificationNumber = await cartPage.getCartNotificationsNumber(page);
    expect(notificationNumber).to.equal(0);
  });
});
