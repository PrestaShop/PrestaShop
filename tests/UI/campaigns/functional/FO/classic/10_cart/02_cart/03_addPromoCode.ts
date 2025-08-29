import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {createCartRuleTest, deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

import {
  type BrowserContext,
  FakerCartRule,
  foClassicCartPage,
  foClassicHomePage,
  foClassicModalBlockCartPage,
  foClassicModalQuickViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_cart_cart_addPromoCode';

describe('FO - cart : Add promo code', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create cart rule
  const newCartRuleData: FakerCartRule = new FakerCartRule({
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
  createCartRuleTest(newCartRuleData, `${baseContext}_PreTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Check promo code block', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foClassicHomePage.goToFo(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should add the first product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      await foClassicHomePage.quickViewProduct(page, 1);
      await foClassicModalQuickViewPage.addToCartByQuickView(page);
      await foClassicModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.eq(foClassicCartPage.pageTitle);
    });

    it('should add the promo code and check the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount', baseContext);

      await foClassicCartPage.addPromoCode(page, newCartRuleData.code);

      const isVisible = await foClassicCartPage.isCartRuleNameVisible(page);
      expect(isVisible).to.eq(true);
    });

    it('should check the cart rule name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartRuleName', baseContext);

      const cartRuleName = await foClassicCartPage.getCartRuleName(page);
      expect(cartRuleName).to.equal(newCartRuleData.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue', baseContext);

      const totalBeforeDiscount = await foClassicCartPage.getCartRuleValue(page);
      expect(totalBeforeDiscount).to.equal(`-€${parseFloat(newCartRuleData.discountAmount!.value.toString()).toFixed(2)}`);
    });

    it('should set the same promo code and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'samePromoCode', baseContext);

      await foClassicCartPage.addPromoCode(page, newCartRuleData.code);

      const isVisible = await foClassicCartPage.isCartRuleNameVisible(page, 2);
      expect(isVisible).to.eq(false);

      const voucherErrorText = await foClassicCartPage.getCartRuleErrorMessage(page);
      expect(voucherErrorText).to.equal(foClassicCartPage.cartRuleAlreadyInYourCartErrorText);
    });

    it('should set a not existing promo code and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'notExistingPromoCode', baseContext);

      await foClassicCartPage.addPromoCode(page, 'reduction', false);

      const isVisible = await foClassicCartPage.isCartRuleNameVisible(page, 2);
      expect(isVisible).to.eq(false);

      const voucherErrorText = await foClassicCartPage.getCartRuleErrorMessage(page);
      expect(voucherErrorText).to.equal(foClassicCartPage.cartRuleNotExistingErrorText);
    });

    it('should leave the promo code input blanc and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'leavePromoCodeEmpty', baseContext);

      await foClassicCartPage.addPromoCode(page, '', false);

      const voucherErrorText = await foClassicCartPage.getCartRuleErrorMessage(page);
      expect(voucherErrorText).to.equal(foClassicCartPage.cartRuleMustEnterVoucherErrorText);
    });
  });

  // Post-Condition: Delete cart rule
  deleteCartRuleTest(newCartRuleData.name, `${baseContext}_PostTest`);
});
