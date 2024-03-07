// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import cartPage from '@pages/FO/hummingbird/cart';
import homePage from '@pages/FO/hummingbird/home';
import quickViewModal from '@pages/FO/hummingbird/modal/quickView';
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';

// Import commonTests
import {createCartRuleTest, deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// Import data
import CartRuleData from '@data/faker/cartRule';

const baseContext: string = 'functional_FO_hummingbird_cart_cart_addPromoCode';

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
  createCartRuleTest(newCartRuleData, `${baseContext}_PreTest_1`);

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_2`);

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
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should add the first product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      await homePage.quickViewProduct(page, 1);
      await quickViewModal.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.eq(cartPage.pageTitle);
    });

    it('should add the promo code and check the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount', baseContext);

      await cartPage.addPromoCode(page, newCartRuleData.code);

      const isVisible = await cartPage.isCartRuleNameVisible(page);
      expect(isVisible).to.eq(true);
    });

    it('should check the cart rule name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartRuleName', baseContext);

      const cartRuleName = await cartPage.getCartRuleName(page);
      expect(cartRuleName).to.equal(newCartRuleData.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue', baseContext);

      const totalBeforeDiscount = await cartPage.getDiscountValue(page);
      expect(totalBeforeDiscount).to.eq(-newCartRuleData.discountAmount!.value);
    });

    it('should set the same promo code and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'samePromoCode', baseContext);

      await cartPage.addPromoCode(page, newCartRuleData.code);

      const isVisible = await cartPage.isCartRuleNameVisible(page, 2);
      expect(isVisible).to.eq(false);

      const voucherErrorText = await cartPage.getCartRuleErrorMessage(page);
      expect(voucherErrorText).to.equal(cartPage.cartRuleAlreadyInYourCartErrorText);
    });

    it('should set a not existing promo code and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'notExistingPromoCode', baseContext);

      await cartPage.addPromoCode(page, 'reduction', false);

      const isVisible = await cartPage.isCartRuleNameVisible(page, 2);
      expect(isVisible).to.eq(false);

      const voucherErrorText = await cartPage.getCartRuleErrorMessage(page);
      expect(voucherErrorText).to.equal(cartPage.cartRuleNotExistingErrorText);
    });

    it('should leave the promo code input blanc and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'leavePromoCodeEmpty', baseContext);

      await cartPage.addPromoCode(page, '', false);

      const voucherErrorText = await cartPage.getCartRuleErrorMessage(page);
      expect(voucherErrorText).to.equal(cartPage.cartRuleMustEnterVoucherErrorText);
    });
  });

  // Post-Condition: Delete cart rule
  deleteCartRuleTest(newCartRuleData.name, `${baseContext}_PostTest_1`);

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_2`);
});
