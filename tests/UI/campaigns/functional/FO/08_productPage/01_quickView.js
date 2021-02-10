require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

// Importing pages
const homePage = require('@pages/FO/home');
const cartPage = require('@pages/FO/cart');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_productPage_quickView';

// Import data
const {customCartData} = require('@data/FO/cart');
const {firstProductData} = require('@data/FO/product');

let browserContext;
let page;

/*

 */

describe('Product quick view', async () => {
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
    await expect(isHomePage).to.be.true;
  });

  it('should add product to cart by quick view and check details', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addToCartByQuickView', baseContext);

    await homePage.addProductToCartByQuickView(page, 1, 1);

    const result = await homePage.getProductDetailsFromBlockCartModal(page);
    await Promise.all([
      expect(result.name).to.equal(customCartData.firstProduct.name),
      expect(result.price).to.equal(customCartData.firstProduct.price),
      expect(result.size).to.equal('S'),
      expect(result.color).to.equal('White'),
      expect(result.quantity).to.equal(1),
      expect(result.cartProductsCount).to.equal(1),
      expect(result.cartSubtotal).to.equal(customCartData.firstProduct.price),
      expect(result.cartShipping).to.contains('Free'),
      expect(result.totalTaxIncl).to.contains(customCartData.firstProduct.price),
    ]);
  });

  it('should proceed to checkout and delete product from the cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeBlockCartModal', baseContext);

    await homePage.proceedToCheckout(page);

    const pageTitle = await cartPage.getPageTitle(page);
    await expect(pageTitle).to.equal(cartPage.pageTitle);

    await cartPage.deleteProduct(page, 1);

    await cartPage.goToHomePage(page);
  });

  it('should change product quantity from quick view modal and check details', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeQuantityByQuickView', baseContext);

    await homePage.addProductToCartByQuickView(page, 1, 2);

    const result = await homePage.getProductDetailsFromBlockCartModal(page);
    await Promise.all([
      expect(result.name).to.equal(customCartData.firstProduct.name),
      expect(result.price).to.equal(customCartData.firstProduct.price),
      expect(result.size).to.equal('S'),
      expect(result.color).to.equal('White'),
      expect(result.quantity).to.equal(2),
      expect(result.cartProductsCount).to.equal(2),
      expect(result.cartSubtotal).to.equal('€45.89'),
      expect(result.cartShipping).to.contains('Free'),
      expect(result.totalTaxIncl).to.contains('€45.89'),
    ]);
  });

  it('should proceed to checkout and delete product from the cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeBlockCartModal', baseContext);

    await homePage.proceedToCheckout(page);

    const pageTitle = await cartPage.getPageTitle(page);
    await expect(pageTitle).to.equal(cartPage.pageTitle);

    await cartPage.deleteProduct(page, 1);

    await cartPage.goToHomePage(page);
  });

  it('should check share links \'Facebook, Twitter and Pinterest\' from quick view modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkShareLinks', baseContext);

    await homePage.quickViewProduct(page, 1);

    page = await homePage.goToSocialSharingLink(page, 'facebook');

    let url = await homePage.getCurrentURL(page);
    await expect(url).to.contains('facebook');

    page = await homePage.closePage(browserContext, page, 0);

    page = await homePage.goToSocialSharingLink(page, 'twitter');

    url = await homePage.getCurrentURL(page);
    await expect(url).to.contains('twitter');

    page = await homePage.closePage(browserContext, page, 0);

    page = await homePage.goToSocialSharingLink(page, 'pinterest');

    url = await homePage.getCurrentURL(page);
    await expect(url).to.contains('pinterest');

    page = await homePage.closePage(browserContext, page, 0);
  });

  it('should check product information from quick view modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

    const result = await homePage.getProductDetailsFromQuickViewModal(page);
    await Promise.all([
      expect(result.name.toUpperCase()).to.equal(firstProductData.name),
      expect(result.regularPrice).to.equal(firstProductData.regular_price),
      expect(result.price).to.equal(firstProductData.price),
      expect(result.discountPercentage).to.equal(firstProductData.discount_percentage),
      expect(result.taxShippingDeliveryLabel).to.equal(firstProductData.tax_shipping_delivery),
      expect(result.shortDescription).to.equal(firstProductData.short_description),
      expect(result.size).to.equal(firstProductData.size),
      expect(result.color).to.equal(firstProductData.color),
      expect(result.coverImage).to.contains(firstProductData.cover_image),
      expect(result.thumbImage).to.contains(firstProductData.thumb_image),
    ]);
  });

  it('should close quick option modal and check it', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeQuickOptionModal', baseContext);

    const isQuickViewModalClosed = await homePage.closeQuickViewModal(page);
    await expect(isQuickViewModalClosed).to.be.false;
  });
});
