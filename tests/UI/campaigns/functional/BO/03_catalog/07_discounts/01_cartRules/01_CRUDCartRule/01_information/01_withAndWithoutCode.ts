// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import cartRulesPage from '@pages/BO/catalog/discounts';
import addCartRulePage from '@pages/BO/catalog/discounts/add';
import dashboardPage from '@pages/BO/dashboard';
// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';
import {homePage as foHomePage} from '@pages/FO/classic/home';
import {productPage as foProductPage} from '@pages/FO/classic/product';

// Import data
import Products from '@data/demo/products';
import CartRuleData from '@data/faker/cartRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_information_withAndWithoutCode';

describe('BO - Catalog - Cart rules : CRUD cart rule with/without code', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const pastDate: string = date.getDateFormat('yyyy-mm-dd', 'past');
  const cartRuleWithoutCode: CartRuleData = new CartRuleData({
    dateFrom: pastDate,
    name: 'withoutCode',
    discountType: 'Percent',
    discountPercent: 20,
  });
  const cartRuleWithCode: CartRuleData = new CartRuleData({
    name: 'withCodeHighlightFalse',
    code: '4QABV6L3',
    highlight: false,
    discountType: 'Percent',
    discountPercent: 20,
  });
  const secondCartRuleWithCode: CartRuleData = new CartRuleData({
    name: 'withCodeHighlightTrue',
    code: '4QABU4OP',
    highlight: true,
    discountType: 'Percent',
    discountPercent: 20,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Catalog > Discounts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.discountsLink,
    );

    const pageTitle = await cartRulesPage.getPageTitle(page);
    expect(pageTitle).to.contains(cartRulesPage.pageTitle);
  });

  describe('case 1 : Create cart rule without code then check it on FO', async () => {
    describe('Create cart rule on BO', async () => {
      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

        await cartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await addCartRulePage.getPageTitle(page);
        expect(pageTitle).to.contains(addCartRulePage.pageTitle);
      });

      it('should create cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

        const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleWithoutCode);
        expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
      });
    });

    describe('Verify discount on FO', async () => {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

        // View my shop and init pages
        page = await addCartRulePage.viewMyShop(page);
        await foHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage1', baseContext);

        await foHomePage.goToProductPage(page, 1);

        const pageTitle = await foProductPage.getPageTitle(page);
        expect(pageTitle.toUpperCase()).to.contains(Products.demo_1.name.toUpperCase());
      });

      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

        await foProductPage.addProductToTheCart(page);

        const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(1);
      });

      it('should verify the total after discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalAfterDiscount1', baseContext);

        const discountPercent = cartRuleWithoutCode.discountPercent!;
        const totalAfterDiscount = Products.demo_1.finalPrice
          - ((Products.demo_1.finalPrice * discountPercent) / 100);

        const priceATI = await cartPage.getATIPrice(page);
        expect(priceATI).to.equal(parseFloat(totalAfterDiscount.toFixed(2)));

        const cartRuleName = await cartPage.getCartRuleName(page);
        expect(cartRuleName).to.equal(cartRuleWithoutCode.name);

        const discountValue = await cartPage.getDiscountValue(page);
        expect(discountValue).to.equal(parseFloat(totalAfterDiscount.toFixed(2)) - Products.demo_1.finalPrice);
      });

      it('should remove product from shopping cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'removeProduct1', baseContext);

        await cartPage.deleteProduct(page, 1);

        const notificationNumber = await cartPage.getCartNotificationsNumber(page);
        expect(notificationNumber).to.be.equal(0);
      });
    });
  });

  describe('case 2 : Create cart rule with code, highlight disabled then check it on FO', async () => {
    describe('Update cart rule on BO', async () => {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBoToUpdate', baseContext);

        // Close tab and init other page objects with new current tab
        page = await foHomePage.closePage(browserContext, page, 0);

        const pageTitle = await cartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(cartRulesPage.pageTitle);
      });

      it('should go to edit cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditCartRulePage', baseContext);

        await cartRulesPage.goToEditCartRulePage(page, 1);

        const pageTitle = await addCartRulePage.getPageTitle(page);
        expect(pageTitle).to.contains(addCartRulePage.editPageTitle);
      });

      it('should update cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateCartRule', baseContext);

        const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleWithCode);
        expect(validationMessage).to.contains(addCartRulePage.successfulUpdateMessage);
      });
    });

    describe('Verify discount on FO', async () => {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop2', baseContext);

        // View my shop and init pages
        page = await addCartRulePage.viewMyShop(page);
        await foHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage2', baseContext);

        await foHomePage.goToProductPage(page, 1);

        const pageTitle = await foProductPage.getPageTitle(page);
        expect(pageTitle.toUpperCase()).to.contains(Products.demo_1.name.toUpperCase());
      });

      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

        await foProductPage.addProductToTheCart(page);

        const notificationNumber = await cartPage.getCartNotificationsNumber(page);
        expect(notificationNumber).to.be.equal(1);
      });

      it('should verify the total before the discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkTotalBeforeDiscount', baseContext);

        const priceATI = await cartPage.getATIPrice(page);
        expect(priceATI).to.equal(Products.demo_1.finalPrice);
      });

      it('should set the promo code', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode', baseContext);

        await cartPage.addPromoCode(page, cartRuleWithCode.code);

        const cartRuleName = await cartPage.getCartRuleName(page, 1);
        expect(cartRuleName).to.equal(cartRuleWithCode.name);
      });

      it('should verify the total after the discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount2', baseContext);

        const discountPercent = cartRuleWithoutCode.discountPercent!;
        const totalAfterPromoCode = Products.demo_1.finalPrice
          - ((Products.demo_1.finalPrice * discountPercent) / 100);

        const priceATI = await cartPage.getATIPrice(page);
        expect(priceATI).to.equal(parseFloat(totalAfterPromoCode.toFixed(2)));

        const discountValue = await cartPage.getDiscountValue(page, 1);
        expect(discountValue).to.equal(parseFloat((totalAfterPromoCode - Products.demo_1.finalPrice).toFixed(2)));
      });

      it('should remove voucher and product from shopping cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'removeProductAndVoucher', baseContext);

        await cartPage.removeVoucher(page, 1);
        await cartPage.deleteProduct(page, 1);

        const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(0);
      });
    });
  });

  describe('case 3 : Create cart rule with code, highlight enabled then check it on FO', async () => {
    describe('Update cart rule on BO', async () => {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBoToUpdate2', baseContext);

        // Close tab and init other page objects with new current tab
        page = await foHomePage.closePage(browserContext, page, 0);

        const pageTitle = await cartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(cartRulesPage.pageTitle);
      });

      it('should go to edit cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditCartRulePage2', baseContext);

        await cartRulesPage.goToEditCartRulePage(page, 1);

        const pageTitle = await addCartRulePage.getPageTitle(page);
        expect(pageTitle).to.contains(addCartRulePage.editPageTitle);
      });

      it('should update cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateCartRule2', baseContext);

        const validationMessage = await addCartRulePage.createEditCartRules(page, secondCartRuleWithCode);
        expect(validationMessage).to.contains(addCartRulePage.successfulUpdateMessage);
      });
    });

    describe('Verify discount on FO', async () => {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop3', baseContext);

        // View my shop and init pages
        page = await addCartRulePage.viewMyShop(page);
        await foHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage3', baseContext);

        await foHomePage.goToProductPage(page, 1);

        const pageTitle = await foProductPage.getPageTitle(page);
        expect(pageTitle.toUpperCase()).to.contains(Products.demo_1.name.toUpperCase());
      });

      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart3', baseContext);

        await foProductPage.addProductToTheCart(page);

        const notificationNumber = await cartPage.getCartNotificationsNumber(page);
        expect(notificationNumber).to.be.equal(1);
      });

      it('should verify the total before the discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkTotalBeforeDiscount2', baseContext);

        const priceATI = await cartPage.getATIPrice(page);
        expect(priceATI).to.equal(Products.demo_1.finalPrice);
      });

      it('should check the displayed promo code', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkPromoCodeBlock2', baseContext);

        const promoCode = await cartPage.getHighlightPromoCode(page);
        expect(promoCode).to.equal(`${secondCartRuleWithCode.code} - ${secondCartRuleWithCode.name}`);
      });

      it('should click on the promo code', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode2', baseContext);

        await cartPage.clickOnPromoCode(page);

        const cartRuleName = await cartPage.getCartRuleName(page, 1);
        expect(cartRuleName).to.equal(secondCartRuleWithCode.name);
      });

      it('should verify the total after the discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount3', baseContext);

        const totalAfterPromoCode = Products.demo_1.finalPrice
          - ((Products.demo_1.finalPrice * (cartRuleWithCode.discountPercent!)) / 100);

        const priceATI = await cartPage.getATIPrice(page);
        expect(priceATI).to.equal(parseFloat(totalAfterPromoCode.toFixed(2)));

        const discountValue = await cartPage.getDiscountValue(page, 1);
        expect(discountValue).to.equal(parseFloat((totalAfterPromoCode - Products.demo_1.finalPrice).toFixed(2)));
      });

      it('should remove voucher and product from shopping cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'removeProductAndVoucher2', baseContext);

        await cartPage.removeVoucher(page, 1);
        await cartPage.deleteProduct(page, 1);

        const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(0);
      });
    });
  });

  describe('Delete the updated cart rule', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

      // Close tab and init other page objects with new current tab
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await cartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    it('should delete cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCartRule', baseContext);

      const validationMessage = await cartRulesPage.deleteCartRule(page);
      expect(validationMessage).to.contains(cartRulesPage.successfulDeleteMessage);
    });
  });
});
