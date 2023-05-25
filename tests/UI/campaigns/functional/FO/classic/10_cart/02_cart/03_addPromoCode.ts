// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import cartPage from '@pages/FO/cart';
import {homePage} from '@pages/FO/home';

// Import commonTests
import {createCartRuleTest, deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// Import data
import CartRuleData from '@data/faker/cartRule';

const baseContext: string = 'functional_FO_classic_cart_cart_addPromoCode';

describe('FO - cart : Add promo code', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create cart rule
  const newCartRuleData: CartRuleData = new CartRuleData({
    name: 'reduction',
    code: 'reduc',
    discountType: 'Amount',
    discountAmount: {
      value: 20,
      currency: 'EUR',
      tax: 'Tax included',
    },
  });

  // Pre-condition: Create cart rule and apply the discount to 'productWithCartRule'
  createCartRuleTest(newCartRuleData, baseContext);

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Check promo code block', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should add the first product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      await homePage.addProductToCartByQuickView(page, 1, 1);
      await homePage.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      await expect(pageTitle).to.eq(cartPage.pageTitle);
    });

    it('should add the promo code and check the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount', baseContext);

      await cartPage.addPromoCode(page, newCartRuleData.code);

      const isVisible = await cartPage.isCartRuleNameVisible(page);
      await expect(isVisible).to.be.true;
    });

    it('should check the cart rule name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartRuleName', baseContext);

      const cartRuleName = await cartPage.getCartRuleName(page);
      await expect(cartRuleName).to.equal(newCartRuleData.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue', baseContext);

      const totalBeforeDiscount = await cartPage.getDiscountValue(page);
      await expect(totalBeforeDiscount).to.eq(-newCartRuleData.discountAmount!.value);
    });

    it('should set the same promo code and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'samePromoCode', baseContext);

      await cartPage.addPromoCode(page, newCartRuleData.code);

      const isVisible = await cartPage.isCartRuleNameVisible(page, 2);
      await expect(isVisible).to.be.false;

      const voucherErrorText = await cartPage.getCartRuleErrorMessage(page);
      await expect(voucherErrorText).to.equal(cartPage.cartRuleAlreadyInYourCartErrorText);
    });

    it('should set a not existing promo code and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'notExistingPromoCode', baseContext);

      await cartPage.addPromoCode(page, 'reduction', false);

      const isVisible = await cartPage.isCartRuleNameVisible(page, 2);
      await expect(isVisible).to.be.false;

      const voucherErrorText = await cartPage.getCartRuleErrorMessage(page);
      await expect(voucherErrorText).to.equal(cartPage.cartRuleNotExistingErrorText);
    });

    it('should leave the promo code input blanc and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'leavePromoCodeEmpty', baseContext);

      await cartPage.addPromoCode(page, '', false);

      const voucherErrorText = await cartPage.getCartRuleErrorMessage(page);
      await expect(voucherErrorText).to.equal(cartPage.cartRuleMustEnterVoucherErrorText);
    });
  });

  // Post-Condition: Delete cart rule
  deleteCartRuleTest(newCartRuleData.name, baseContext);
});
