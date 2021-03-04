require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

// Import pages
const homePage = require('@pages/FO/home');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');

// Import data
const {Products} = require('@data/demo/products');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_productPage_addToCart';

let browserContext;
let page;
const quantity = 6;
const totalPrice = 137.66;
const combination = {color: 'Black', size: 'M'};

/*
Add product to cart
Check product details on the cart
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

  // 1 - Add product to cart
  describe('Add product to cart', async () => {
    it('should go to product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await homePage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should choose combination and add product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await productPage.addProductToTheCart(page, quantity, combination);

      const pageTitle = await cartPage.getPageTitle(page);
      await expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should check the ordered product in cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderedProduct', baseContext);

      let result = await cartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_1.name),
        expect(result.price).to.equal(Products.demo_1.finalPrice),
        expect(result.totalPrice).to.equal(totalPrice),
        expect(result.quantity).to.equal(quantity),
      ]);

      result = await cartPage.getProductAttributes(page, 1);
      await Promise.all([
        expect(result.size).to.equal(combination.size),
        expect(result.color).to.equal(combination.color),
      ]);
    });
  });
});
