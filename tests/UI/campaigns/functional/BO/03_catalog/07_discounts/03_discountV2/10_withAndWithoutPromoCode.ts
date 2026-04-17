import testContext from '@utils/testContext';
import {expect} from 'chai';

import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';

import {
  // BO pages
  boDiscountsPage,
  boDiscountsCreatePage,
  boDashboardPage,
  boLoginPage,
  boFeatureFlagPage,
  // FO pages
  foHummingbirdHomePage,
  foHummingbirdSearchResultsPage,
  foHummingbirdModalQuickViewPage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdCartPage,
  // Data
  dataProducts,
  FakerDiscount,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_discountV2_withAndWithoutPromoCode';

describe('BO - Catalog - Discounts : Create a discount that applies automatically or via promo code', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let randomPromoCode: string = '';

  const discountWithoutName: FakerDiscount = new FakerDiscount({
    discountType: 'cart_level',
    name: ' ',
    noProductCondition: true,
    discountValue: 10,
    discountReductionType: '€',
    discountTax: 'Tax included',
  });
  const discountAmountZero: FakerDiscount = new FakerDiscount({
    ...discountWithoutName,
    name: 'Test',
    discountValue: '0',
  });
  const discountAmountNegative: FakerDiscount = new FakerDiscount({
    ...discountWithoutName,
    name: 'Test',
    discountValue: -20,
  });
  const discountData: FakerDiscount = new FakerDiscount({
    ...discountWithoutName,
    name: 'Test',
    discountValue: 10,
  });

  const editDiscountData: FakerDiscount = new FakerDiscount({
    ...discountWithoutName,
    name: 'Test',
    discountValue: 10,
    discountCode: 'test',
  });

  const discountRandomPromoCodeData: FakerDiscount = new FakerDiscount({
    ...discountWithoutName,
    name: 'Test',
    discountValue: 10,
    generateRandomCode: true,
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 1 - Pre-condition: Enable discount
  setFeatureFlag(boFeatureFlagPage.featureFlagDiscount, true, `${baseContext}_preTest`);

  describe('Create discount and check it in FO', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
      );

      const pageTitle = await boDiscountsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsPage.pageTitle);
    });

    it(`should click on create discount button and choose the type '${discountData.discountType}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDiscountType', baseContext);

      await boDiscountsPage.clickOnCreateDiscountButton(page);
      await boDiscountsPage.selectDiscountType(page, discountWithoutName.discountType!);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitle);
    });

    it('should create a discount without name and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_1', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountWithoutName);
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'name');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageNameRequired);
    });

    it('should create a discount with a discount value = 0 euro and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_2', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountAmountZero);
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'value');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageDiscountValue(
        discountAmountZero.discountValue.toString()));
    });

    it('should create a discount with a discount value < 0 and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_3', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountAmountNegative);
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'value');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageDiscountValue(
        discountAmountNegative.discountValue.toString()));
    });

    it('should create a discount without code and check the success message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscountWithoutCode', baseContext);

      const errorMessage = await boDiscountsCreatePage.createDiscount(page, discountData);
      expect(errorMessage).to.contains(boDiscountsCreatePage.successfulCreationMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await boDiscountsCreatePage.viewMyShop(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.equal(true);
    });

    it(`should search for the product '${dataProducts.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct_1', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_6.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it(`should add the product '${dataProducts.demo_6.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart_1', baseContext);

      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const shoppingCarts = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(shoppingCarts).to.equal(1);
    });

    it('should check that the discount is applied automatically', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountApplied', baseContext);

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.contains(discountData.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue_1', baseContext);

      const discount = parseFloat(discountData.discountValue.toString()).toFixed(2);

      const discountValue = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(discountValue).to.contains(`-€${discount}`);

      const subTotalDiscount = await foHummingbirdCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toString()).to.equal(`-${discountData.discountValue}`);
    });

    it('should check the shipping cost', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost_1', baseContext);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.equal('Free');
    });

    it('should check the Total (tax incl.) after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount_1', baseContext);

      const discount = parseFloat(discountData.discountValue.toString());

      const totalAfterDiscount = await foHummingbirdCartPage.getATIPrice(page);
      expect(totalAfterDiscount.toFixed(2)).to.equal((dataProducts.demo_6.combinations[0].price - discount).toFixed(2));
    });

    it('should delete the product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProductFromCart', baseContext);

      await foHummingbirdCartPage.deleteProduct(page, 1);

      const notificationNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.be.equal(0);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFoPage', baseContext);

      page = await foHummingbirdHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitle);
    });
  });

  describe('Edit discount in BO and check it in FO - Add promo code', async () => {
    it('should edit the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editDiscount', baseContext);

      const errorMessage = await boDiscountsCreatePage.createDiscount(page, editDiscountData);
      expect(errorMessage).to.contains(boDiscountsCreatePage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_2', baseContext);

      page = await boDiscountsCreatePage.viewMyShop(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.equal(true);
    });

    it(`should search for the product '${dataProducts.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct_2', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_6.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it(`should add the product '${dataProducts.demo_6.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart_2', baseContext);

      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const shoppingCarts = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(shoppingCarts).to.equal(1);
    });

    it('should check that the discount is not applied automatically', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountNotApplied', baseContext);

      await foHummingbirdCartPage.reloadPage(page);

      const isDiscountVisible = await foHummingbirdCartPage.isCartRuleNameVisible(page);
      expect(isDiscountVisible).to.equal(false);
    });

    it('should add the promo code and check the discount name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode_1', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, editDiscountData.discountCode);

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.contains(editDiscountData.name);
    });

    it('should check that the discount is applied', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountIsApplied', baseContext);

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.contains(editDiscountData.name);

      const discount = parseFloat(editDiscountData.discountValue.toString()).toFixed(2);

      const discountValue = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(discountValue).to.contains(`-€${discount}`);

      const subTotalDiscount = await foHummingbirdCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toString()).to.equal(`-${editDiscountData.discountValue}`);
    });

    it('should check the shipping cost', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost_2', baseContext);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.equal('Free');
    });

    it('should check the Total (tax incl.) after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount_2', baseContext);

      const discount = parseFloat(editDiscountData.discountValue.toString());

      const totalAfterDiscount = await foHummingbirdCartPage.getATIPrice(page);
      expect(totalAfterDiscount.toFixed(2)).to.equal((dataProducts.demo_6.combinations[0].price - discount).toFixed(2));
    });

    it('should delete the product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProductFromCart_1', baseContext);

      await foHummingbirdCartPage.deleteProduct(page, 1);

      const notificationNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.be.equal(0);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFoPage_2', baseContext);

      page = await foHummingbirdHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitle);
    });
  });

  describe('Edit discount in BO and check it in FO - Generate random promo code', async () => {
    it('should edit the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editDiscount_2', baseContext);

      const errorMessage = await boDiscountsCreatePage.createDiscount(page, discountRandomPromoCodeData);
      expect(errorMessage).to.contains(boDiscountsCreatePage.successfulUpdateMessage);
    });

    it('should get the discount code and regenerate a new random code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'regerenateNewCode', baseContext);

      const code = await boDiscountsCreatePage.getValue(page, 'code');

      const successMessage = await boDiscountsCreatePage.createDiscount(page, discountRandomPromoCodeData);
      expect(successMessage).to.contains(boDiscountsCreatePage.successfulUpdateMessage);

      randomPromoCode = await boDiscountsCreatePage.getValue(page, 'code');
      expect(code).not.equal(randomPromoCode);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_1', baseContext);

      page = await boDiscountsCreatePage.viewMyShop(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.equal(true);
    });

    it(`should search for the product '${dataProducts.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct_3', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_6.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it(`should add the product '${dataProducts.demo_6.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart_3', baseContext);

      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const shoppingCarts = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(shoppingCarts).to.equal(1);
    });

    it('should check that the discount is not applied automatically', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountNotApplied_2', baseContext);

      await foHummingbirdCartPage.reloadPage(page);

      const isDiscountVisible = await foHummingbirdCartPage.isCartRuleNameVisible(page);
      expect(isDiscountVisible).to.equal(false);
    });

    it('should add the promo code and check the discount name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode_2', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, randomPromoCode);

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.contains(editDiscountData.name);
    });

    it('should check that the discount is applied', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountIsApplied_2', baseContext);

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.contains(discountRandomPromoCodeData.name);

      const discount = parseFloat(discountRandomPromoCodeData.discountValue.toString()).toFixed(2);

      const discountValue = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(discountValue).to.contains(`-€${discount}`);

      const subTotalDiscount = await foHummingbirdCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toString()).to.equal(`-${discountRandomPromoCodeData.discountValue}`);
    });

    it('should check the shipping cost', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost_3', baseContext);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.equal('Free');
    });

    it('should check the Total (tax incl.) after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount_3', baseContext);

      const discount = parseFloat(discountRandomPromoCodeData.discountValue.toString());

      const totalAfterDiscount = await foHummingbirdCartPage.getATIPrice(page);
      expect(totalAfterDiscount.toFixed(2)).to.equal((dataProducts.demo_6.combinations[0].price - discount).toFixed(2));
    });
  });

  describe('Delete discount', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO_2', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitle);
    });

    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
      );

      const pageTitle = await boDiscountsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsPage.pageTitle);
    });

    it('should delete the created discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteDiscount', baseContext);

      const validationMessage = await boDiscountsPage.deleteDiscount(page, 1);
      expect(validationMessage).to.contains(boDiscountsPage.successfulDeleteMessage);
    });
  });

  // 1 - Post-condition: Disable discount
  setFeatureFlag(boFeatureFlagPage.featureFlagDiscount, false, `${baseContext}_postTest`);
});
