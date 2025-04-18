import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  type BrowserContext,
  dataProducts,
  foClassicCartPage,
  foClassicCategoryPage,
  foClassicHomePage,
  foClassicModalBlockCartPage,
  foClassicModalQuickViewPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_addToCart';

describe('FO - Product page - Product page : Add to cart', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const qtyProductPage: number = 5;
  const qtyQuickView: number = 100;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

    await foClassicHomePage.goToFo(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it(`should search the product "${dataProducts.demo_12.name}"`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchDemo12', baseContext);

    await foClassicHomePage.searchProduct(page, dataProducts.demo_12.name);

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
  });

  it('should go to the product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageDemo12', baseContext);

    await foClassicSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_12.name);
  });

  it('should add the product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await foClassicProductPage.addProductToTheCart(page, qtyProductPage, [], null);

    const productDetails = await foClassicModalBlockCartPage.getProductDetailsFromBlockCartModal(page);
    expect(productDetails.quantity).to.equal(qtyProductPage);
    expect(productDetails.name).to.equal(dataProducts.demo_12.name);
  });

  it('should click on continue shopping button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'continueShopping', baseContext);

    const isModalClosed = await foClassicModalBlockCartPage.continueShopping(page);
    expect(isModalClosed).to.equal(true);
  });

  it('should go back to the home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goHomePage', baseContext);

    await foClassicProductPage.goToHomePage(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it('should go to all products page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAllProductsPage', baseContext);

    await foClassicHomePage.goToAllProductsPage(page);

    const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
    expect(isCategoryPageVisible, 'Home category page was not opened').to.equal(true);
  });

  it('should quick view the first product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct', baseContext);

    await foClassicCategoryPage.quickViewProduct(page, 1);

    const isModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
    expect(isModalVisible).to.equal(true);
  });

  it('should add product to cart by quick view', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCartByQuickView', baseContext);

    await foClassicModalQuickViewPage.setQuantity(page, qtyQuickView);
    await foClassicModalQuickViewPage.addToCartByQuickView(page);

    const productDetails = await foClassicModalBlockCartPage.getProductDetailsFromBlockCartModal(page);
    expect(productDetails.quantity).to.equal(qtyQuickView);
    expect(productDetails.name).to.equal(dataProducts.demo_1.name);
  });

  it('should proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

    await foClassicModalBlockCartPage.proceedToCheckout(page);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
  });

  it('should change the product quantity from cart page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeProductQuantity', baseContext);

    await foClassicCartPage.editProductQuantity(page, 1, 200);

    const notificationNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationNumber).to.equal(300);
  });

  it('should delete the first product from the cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstProduct', baseContext);

    await foClassicCartPage.deleteProduct(page, 1);

    const notificationNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationNumber).to.equal(100);
  });

  it('should delete the first product from the cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteSecondProduct', baseContext);

    await foClassicCartPage.deleteProduct(page, 1);

    const notificationNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationNumber).to.equal(0);
  });
});
