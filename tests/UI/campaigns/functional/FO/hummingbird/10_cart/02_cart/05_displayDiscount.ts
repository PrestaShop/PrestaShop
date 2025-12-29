import testContext from '@utils/testContext';
import {expect} from 'chai';

import {createCartRuleTest, deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataProducts,
  FakerCartRule,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdHomePage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdModalQuickViewPage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_cart_cart_displayDiscount';

describe('FO - Cart : Display discount on product', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create first cart rule
  const firstCartRuleData: FakerCartRule = new FakerCartRule({
    name: 'test1',
    code: '123456',
    quantity: 100,
    quantityPerUser: 100,
    discountType: 'Percent',
    discountPercent: 15,
    applyDiscountTo: 'Specific product',
    product: dataProducts.demo_8.name,
  });
  // Data to create second cart rule
  const secondCartRuleData: FakerCartRule = new FakerCartRule({
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
    product: dataProducts.demo_9.name,
  });
  const secondCartRuleDiscount: number = parseFloat(secondCartRuleData.discountAmount!.value.toString());

  // Pre-condition: Create first cart rule
  createCartRuleTest(firstCartRuleData, `${baseContext}_PreTest_1`);

  // Pre-condition: Create second cart rule
  createCartRuleTest(secondCartRuleData, `${baseContext}_PreTest_2`);

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest_3`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Display discount', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it(`should search for the product '${dataProducts.demo_8.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_8.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it(`should quick view the product '${dataProducts.demo_8.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct', baseContext);

      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);

      const isModalVisible = await foHummingbirdModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should add the product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);

      const isNotVisible = await foHummingbirdModalBlockCartPage.continueShopping(page);
      expect(isNotVisible).to.equal(true);
    });

    it(`should search for the product '${dataProducts.demo_9.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct2', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_9.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it(`should quick view the product '${dataProducts.demo_9.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct2', baseContext);

      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);

      const isModalVisible = await foHummingbirdModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should add the product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should check the cart notifications', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartNotifications', baseContext);

      const shoppingCarts = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(shoppingCarts).to.equal(2);
    });

    it('should add the first promo code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstPromoCode', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, firstCartRuleData.code);

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.contains(firstCartRuleData.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue1', baseContext);

      const discount = utilsCore.percentage(dataProducts.demo_9.finalPrice, firstCartRuleData.getDiscountPercent());

      const discountValue = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(discountValue).to.contains(`-€${discount.toFixed(2)}`);
    });

    it('should check the total after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount1', baseContext);

      const discount = utilsCore.percentage(dataProducts.demo_9.finalPrice, firstCartRuleData.getDiscountPercent());

      const totalAfterDiscount = await foHummingbirdCartPage.getATIPrice(page);
      expect(totalAfterDiscount.toString()).to.equal((dataProducts.demo_9.finalPrice * 2 - discount).toFixed(2));
    });

    it('should add the second promo code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addSecondPromoCode', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, secondCartRuleData.code);

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page, 2);
      expect(cartRuleName).to.contains(secondCartRuleData.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue2', baseContext);

      const discountValue = await foHummingbirdCartPage.getCartRuleValue(page, 2);
      expect(discountValue).to.contains(`-€${secondCartRuleDiscount.toFixed(2)}`);
    });

    it('should check the total after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount', baseContext);

      const firstDiscount = utilsCore.percentage(dataProducts.demo_9.finalPrice, firstCartRuleData.getDiscountPercent());

      const totalAfterDiscount = await foHummingbirdCartPage.getATIPrice(page);
      expect(totalAfterDiscount.toString())
        .to.equal((dataProducts.demo_9.finalPrice * 2 - (firstDiscount + secondCartRuleDiscount))
          .toFixed(2));
    });

    it('should remove the second discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeTheDiscount2', baseContext);

      const isDeleteIconNotVisible = await foHummingbirdCheckoutPage.removePromoCode(page, 2);
      expect(isDeleteIconNotVisible, 'The discount is not removed').to.equal(true);
    });

    it('should check the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotal3', baseContext);

      const discount = utilsCore.percentage(dataProducts.demo_9.finalPrice, firstCartRuleData.getDiscountPercent());

      const totalAfterDiscount = await foHummingbirdCartPage.getATIPrice(page);
      expect(totalAfterDiscount.toString()).to.equal((dataProducts.demo_9.finalPrice * 2 - discount).toFixed(2));
    });

    it('should remove the first discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeTheDiscount', baseContext);

      const isDeleteIconNotVisible = await foHummingbirdCheckoutPage.removePromoCode(page, 1);
      expect(isDeleteIconNotVisible, 'The discount is not removed').to.equal(true);
    });

    it('should check the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotal', baseContext);

      const total = await foHummingbirdCartPage.getATIPrice(page);
      expect(total.toString()).to.equal((dataProducts.demo_9.finalPrice * 2).toFixed(2));
    });

    it('should delete the second product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLastProduct', baseContext);

      await foHummingbirdCartPage.deleteProduct(page, 2);

      const notificationNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.equal(1);
    });

    it('should delete the first product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstProduct', baseContext);

      await foHummingbirdCartPage.deleteProduct(page, 1);

      const notificationNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.equal(0);
    });
  });

  // Post-Condition: Delete first cart rule
  deleteCartRuleTest(firstCartRuleData.name, `${baseContext}_PostTest_1`);

  // Post-Condition: Delete second cart rule
  deleteCartRuleTest(secondCartRuleData.name, `${baseContext}_PostTest_2`);

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest_3`);
});
