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

    const result = await homePage.getProductDetail(page);
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

  it('should change product quantity by quick view', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeQuantityByQuickView', baseContext);

    await homePage.addProductToCartByQuickView(page, 1, 2);

    const result = await homePage.getProductDetail(page);
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
});
