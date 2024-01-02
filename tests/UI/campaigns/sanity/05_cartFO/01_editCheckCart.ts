// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import {cartPage} from '@pages/FO/cart';
import {homePage} from '@pages/FO/home';
import productPage from '@pages/FO/product';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'sanity_cartFO_editCheckCart';

/*
  Open the FO home page
  Add the first product to the cart
  Add the second product to the cart
  Check the cart
  Edit the cart and check it
 */
describe('FO - Cart : Check Cart in FO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let totalATI: number = 0;
  let itemsNumber: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Cart FO: edit check cart', async () => {
    // Steps
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage1', baseContext);

      await homePage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should add product to cart and check that the number of products was updated in cart header', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

      await productPage.addProductToTheCart(page);
      // getNumberFromText is used to get the notifications number in the cart
      const notificationsNumber = await homePage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should go to the home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await homePage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the second product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage2', baseContext);

      await homePage.goToProductPage(page, 2);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_3.name);
    });

    it('should add product to cart and check that the number of products was updated in cart header', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await productPage.addProductToTheCart(page);

      // getNumberFromText is used to get the notifications number in the cart
      const notificationsNumber = await homePage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(2);
    });

    it('should check the first product details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetail1', baseContext);

      const result = await cartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_1.name),
        expect(result.price).to.equal(Products.demo_1.finalPrice),
        expect(result.quantity).to.equal(1),
      ]);
    });

    it('should check the second product details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetail2', baseContext);

      const result = await cartPage.getProductDetail(page, 2);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_3.name),
        expect(result.price).to.equal(Products.demo_3.finalPrice),
        expect(result.quantity).to.equal(1),
      ]);
    });

    it('should get the ATI price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalATI', baseContext);

      // getNumberFromText is used to get the price ATI
      totalATI = await cartPage.getATIPrice(page);
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/9779
      // expect(totalATI.toString()).to.be.equal((Products.demo_3.finalPrice + Products.demo_1.finalPrice)
      // .toFixed(2));
    });

    it('should get the product number and check that is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProductsInCart', baseContext);

      // getNumberFromText is used to get the products number
      itemsNumber = await cartPage.getProductsNumber(page);
      expect(itemsNumber).to.be.equal(2);
    });

    it('should edit the quantity of the first product ordered', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProductQuantity1', baseContext);

      await cartPage.editProductQuantity(page, 1, 3);

      // getNumberFromText is used to get the new price ATI
      const totalPrice = await cartPage.getATIPrice(page);
      expect(totalPrice).to.be.above(totalATI);

      // getNumberFromText is used to get the new products number
      const productsNumber = await cartPage.getProductsNumber(page);
      expect(productsNumber).to.be.above(itemsNumber);
    });

    it('should edit the quantity of the second product ordered', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProductQuantity2', baseContext);

      await cartPage.editProductQuantity(page, 2, 2);

      // getNumberFromText is used to get the new price ATI
      const totalPrice = await cartPage.getATIPrice(page);
      expect(totalPrice).to.be.above(totalATI);

      // getNumberFromText is used to get the new products number
      const productsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(productsNumber).to.be.above(itemsNumber);
    });
  });
});
