import testContext from '@utils/testContext';
import {expect} from 'chai';

import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';
import {createCartRuleTest, deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

import {
  type BrowserContext,
  FakerCartRule,
  foHummingbirdCartPage,
  foHummingbirdHomePage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdModalQuickViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_cart_cart_addPromoCode';

describe('FO - Cart : Add promo code', async () => {
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
  const newCartRuleDiscount: number = parseFloat(newCartRuleData.discountAmount!.value.toString());

  // Pre-condition: Create cart rule and apply the discount to 'productWithCartRule'
  createCartRuleTest(newCartRuleData, `${baseContext}_PreTest_1`);

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest_2`);

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

      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should add the first product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      await foHummingbirdHomePage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.eq(foHummingbirdCartPage.pageTitle);
    });

    it('should add the promo code and check the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, newCartRuleData.code);

      const isVisible = await foHummingbirdCartPage.isCartRuleNameVisible(page);
      expect(isVisible).to.eq(true);
    });

    it('should check the cart rule name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartRuleName', baseContext);

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page);
      expect(cartRuleName).to.contains(newCartRuleData.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue', baseContext);

      const totalBeforeDiscount = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(totalBeforeDiscount).to.contains(`-€${newCartRuleDiscount.toFixed(2)}`);
    });

    it('should set the same promo code and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'samePromoCode', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, newCartRuleData.code);

      const isVisible = await foHummingbirdCartPage.isCartRuleNameVisible(page, 2);
      expect(isVisible).to.eq(false);

      const voucherErrorText = await foHummingbirdCartPage.getCartRuleErrorMessage(page);
      expect(voucherErrorText).to.equal(foHummingbirdCartPage.cartRuleAlreadyInYourCartErrorText);
    });

    it('should set a not existing promo code and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'notExistingPromoCode', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, 'reduction', false);

      const isVisible = await foHummingbirdCartPage.isCartRuleNameVisible(page, 2);
      expect(isVisible).to.eq(false);

      const voucherErrorText = await foHummingbirdCartPage.getCartRuleErrorMessage(page);
      expect(voucherErrorText).to.equal(foHummingbirdCartPage.cartRuleNotExistingErrorText);
    });

    it('should leave the promo code input blanc and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'leavePromoCodeEmpty', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, '', false);

      const voucherErrorText = await foHummingbirdCartPage.getCartRuleErrorMessage(page);
      expect(voucherErrorText).to.contains(foHummingbirdCartPage.cartRuleMustEnterVoucherErrorText);
    });
  });

  // Post-Condition: Delete cart rule
  deleteCartRuleTest(newCartRuleData.name, `${baseContext}_PostTest_1`);

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest_2`);
});
