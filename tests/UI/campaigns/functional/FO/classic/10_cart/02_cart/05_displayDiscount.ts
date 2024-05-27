// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import basicHelper from '@utils/basicHelper';

// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';
import {homePage} from '@pages/FO/classic/home';
import {quickViewModal} from '@pages/FO/classic/modal/quickView';
import {blockCartModal} from '@pages/FO/classic/modal/blockCart';
import {searchResultsPage} from '@pages/FO/classic/searchResults';
import {checkoutPage} from '@pages/FO/classic/checkout';

// Import commonTests
import {createCartRuleTest, deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// Import data
import CartRuleData from '@data/faker/cartRule';
import Products from '@data/demo/products';

const baseContext: string = 'functional_FO_classic_cart_cart_displayDiscount';

describe('FO - cart : Display discount', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create first cart rule
  const firstCartRuleData: CartRuleData = new CartRuleData({
    name: 'test1',
    code: '123456',
    quantity: 100,
    quantityPerUser: 100,
    discountType: 'Percent',
    discountPercent: 15,
    applyDiscountTo: 'Specific product',
    product: Products.demo_8.name,
  });
  // Data to create second cart rule
  const secondCartRuleData: CartRuleData = new CartRuleData({
    name: 'test2',
    code: '123456789',
    quantity: 100,
    quantityPerUser: 100,
    discountType: 'Amount',
    discountAmount: {
      value: 15,
      currency: 'EUR',
      tax: 'Tax included',
    },
    applyDiscountTo: 'Specific product',
    product: Products.demo_9.name,
  });

  // Pre-condition: Create first cart rule
  createCartRuleTest(firstCartRuleData, `${baseContext}_PreTest_1`);

  // Pre-condition: Create second cart rule
  createCartRuleTest(secondCartRuleData, `${baseContext}_PreTest_2`);

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Display discount', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it(`should search for the product '${Products.demo_8.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await homePage.searchProduct(page, Products.demo_8.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it(`should quick view the product '${Products.demo_8.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct', baseContext);

      await searchResultsPage.quickViewProduct(page, 1);

      const isModalVisible = await quickViewModal.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should add the product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await quickViewModal.addToCartByQuickView(page);

      const isNotVisible = await blockCartModal.continueShopping(page);
      expect(isNotVisible).to.equal(true);
    });

    it(`should search for the product '${Products.demo_9.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct2', baseContext);

      await homePage.searchProduct(page, Products.demo_9.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it(`should quick view the product '${Products.demo_9.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct2', baseContext);

      await searchResultsPage.quickViewProduct(page, 1);

      const isModalVisible = await quickViewModal.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should add the product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await quickViewModal.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should check the cart notifications', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartNotifications', baseContext);

      const shoppingCarts = await cartPage.getCartNotificationsNumber(page);
      expect(shoppingCarts).to.equal(2);
    });

    it('should add the first promo code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstPromoCode', baseContext);

      await cartPage.addPromoCode(page, firstCartRuleData.code);

      const cartRuleName = await cartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.equal(firstCartRuleData.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue1', baseContext);

      const discount = await basicHelper.percentage(Products.demo_9.finalPrice, firstCartRuleData.discountPercent!);

      const discountValue = await cartPage.getDiscountValue(page);
      expect(discountValue).to.equal(-discount.toFixed(2));
    });

    it('should check the total after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount1', baseContext);

      const discount = await basicHelper.percentage(Products.demo_9.finalPrice, firstCartRuleData.discountPercent!);

      const totalAfterDiscount = await cartPage.getATIPrice(page);
      expect(totalAfterDiscount.toString()).to.equal((Products.demo_9.finalPrice * 2 - discount).toFixed(2));
    });

    it('should add the second promo code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addSecondPromoCode', baseContext);

      await cartPage.addPromoCode(page, secondCartRuleData.code);

      const cartRuleName = await cartPage.getCartRuleName(page, 2);
      expect(cartRuleName).to.equal(secondCartRuleData.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue2', baseContext);

      const discountValue = await cartPage.getDiscountValue(page, 2);
      expect(discountValue).to.equal(-secondCartRuleData.discountAmount!.value.toFixed(2));
    });

    it('should check the total after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount', baseContext);

      const firstDiscount = await basicHelper.percentage(Products.demo_9.finalPrice, firstCartRuleData.discountPercent!);

      const totalAfterDiscount = await cartPage.getATIPrice(page);
      expect(totalAfterDiscount.toString())
        .to.equal((Products.demo_9.finalPrice * 2 - (firstDiscount + secondCartRuleData.discountAmount!.value))
          .toFixed(2));
    });

    it('should remove the second discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeTheDiscount2', baseContext);

      const isDeleteIconNotVisible = await checkoutPage.removePromoCode(page, 2);
      expect(isDeleteIconNotVisible, 'The discount is not removed').to.equal(true);
    });

    it('should check the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotal3', baseContext);

      const discount = await basicHelper.percentage(Products.demo_9.finalPrice, firstCartRuleData.discountPercent!);

      const totalAfterDiscount = await cartPage.getATIPrice(page);
      expect(totalAfterDiscount.toString()).to.equal((Products.demo_9.finalPrice * 2 - discount).toFixed(2));
    });

    it('should remove the first discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeTheDiscount', baseContext);

      const isDeleteIconNotVisible = await checkoutPage.removePromoCode(page, 1);
      expect(isDeleteIconNotVisible, 'The discount is not removed').to.equal(true);
    });

    it('should check the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotal', baseContext);

      const total = await cartPage.getATIPrice(page);
      expect(total.toString()).to.equal((Products.demo_9.finalPrice * 2).toFixed(2));
    });

    it('should delete the second product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLastProduct', baseContext);

      await cartPage.deleteProduct(page, 2);

      const notificationNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.equal(1);
    });

    it('should delete the first product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstProduct', baseContext);

      await cartPage.deleteProduct(page, 1);

      const notificationNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.equal(0);
    });
  });

  // Post-Condition: Delete first cart rule
  deleteCartRuleTest(firstCartRuleData.name, `${baseContext}_PostTest_1`);

  // Post-Condition: Delete second cart rule
  deleteCartRuleTest(secondCartRuleData.name, `${baseContext}_PostTest_2`);
});
