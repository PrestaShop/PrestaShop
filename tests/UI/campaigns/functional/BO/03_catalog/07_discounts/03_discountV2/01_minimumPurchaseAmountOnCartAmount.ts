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
  utilsCore,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_discountV2_minimumPurchaseAmountOnCartAmount';

/*
Pre-condition:
- Enable discount
Scenario:
- Create discount in BO and check it in FO
- Edit discount in BO and check it in FO
- Delete discount
Post-condition:
- Disable discount
 */
describe('BO - Catalog - Discounts : Minimum purchase amount (On cart amount)', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const discountWithoutName: FakerDiscount = new FakerDiscount({
    discountType: 'cart_level',
    name: ' ',
    noProductCondition: true,
    minimumPurchaseAmount: true,
    minimumAmountValue: 20,
    minimumAmountTax: 'Tax included',
    discountValue: 10,
    discountReductionType: '€',
    discountTax: 'Tax included',
  });
  const discountPurchaseAmountZero: FakerDiscount = new FakerDiscount({
    discountType: 'cart_level',
    name: 'Test',
    noProductCondition: true,
    minimumPurchaseAmount: true,
    minimumAmountValue: '0',
    minimumAmountTax: 'Tax included',
    discountValue: 10,
    discountReductionType: '€',
    discountTax: 'Tax included',
  });
  const discountPurchaseAmountNegative: FakerDiscount = new FakerDiscount({
    discountType: 'cart_level',
    name: 'Test',
    noProductCondition: true,
    minimumPurchaseAmount: true,
    minimumAmountValue: -50,
    minimumAmountTax: 'Tax included',
    discountValue: 10,
    discountReductionType: '€',
    discountTax: 'Tax included',
  });
  const discountPurchaseAmountText: FakerDiscount = new FakerDiscount({
    discountType: 'cart_level',
    name: 'Test',
    noProductCondition: true,
    minimumPurchaseAmount: true,
    minimumAmountValue: 'asefr',
    minimumAmountTax: 'Tax included',
    discountValue: 10,
    discountReductionType: '€',
    discountTax: 'Tax included',
  });
  const discountValueNegative: FakerDiscount = new FakerDiscount({
    discountType: 'cart_level',
    name: 'Test',
    noProductCondition: true,
    minimumPurchaseAmount: true,
    minimumAmountValue: 50,
    minimumAmountTax: 'Tax included',
    discountValue: -20,
    discountReductionType: '€',
    discountTax: 'Tax included',
  });
  const discountValueZero: FakerDiscount = new FakerDiscount({
    discountType: 'cart_level',
    name: 'Test',
    noProductCondition: true,
    minimumPurchaseAmount: true,
    minimumAmountValue: 50,
    minimumAmountTax: 'Tax included',
    discountValue: '0',
    discountReductionType: '€',
    discountTax: 'Tax included',
  });

  const discountData: FakerDiscount = new FakerDiscount({
    discountType: 'cart_level',
    name: 'Test',
    noProductCondition: true,
    minimumPurchaseAmount: true,
    minimumAmountValue: 50,
    minimumAmountTax: 'Tax included',
    discountValue: 10,
    discountReductionType: '€',
    discountTax: 'Tax included',
    discountCode: 'test',
  });
  const editDiscountData: FakerDiscount = new FakerDiscount({
    discountType: 'cart_level',
    name: 'Test',
    noProductCondition: true,
    minimumPurchaseAmount: true,
    minimumAmountValue: 50,
    minimumAmountTax: 'Tax excluded',
    discountValue: 10,
    discountReductionType: '€',
    discountTax: 'Tax excluded',
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

    it('should create a discount with a minimum purchase value = 0 euro and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_2', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountPurchaseAmountZero);
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'amount');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageValueLowerThanZero);
    });

    it('should create a discount with a minimum purchase value < 0 and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_3', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountPurchaseAmountNegative);
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'amount');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageValueLowerThanZero);
    });

    it('should create a discount with a minimum purchase value = asefr and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_4', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountPurchaseAmountText);
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'amount');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageMinPurchaseAmountNotnumber);
    });

    it('should create a discount with a discount value < 0 and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_5', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountValueNegative);
      expect(errorMessage).to.contains(errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'value');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageDiscountValue(
        discountValueNegative.discountValue.toString()));
    });

    it('should create a discount with a discount value = 0 and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_6', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountValueZero);
      expect(errorMessage).to.contains(errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'value');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageDiscountValue('0'));
    });

    it('should create a discount with code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscountWithCode', baseContext);

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

    it(`should search for the product '${dataProducts.demo_3.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct_1', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_3.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it(`should add the product '${dataProducts.demo_3.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart_1', baseContext);

      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const shoppingCarts = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(shoppingCarts).to.equal(1);
    });

    it('should add the promo code and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode_1', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, discountData.discountCode);

      const voucherErrorText = await foHummingbirdCartPage.getCartRuleErrorMessage(page);
      expect(voucherErrorText).to.equal(`${foHummingbirdCartPage.minimumAmountErrorMessage
      } €${parseFloat(discountData.minimumAmountValue.toString()).toFixed(2)}.`);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foHummingbirdCartPage.goToHomePage(page);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it(`should search for the product '${dataProducts.demo_5.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct_2', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_5.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it(`should add the product '${dataProducts.demo_5.name}' to cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart_2', baseContext);

      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const shoppingCarts = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(shoppingCarts).to.equal(2);
    });

    it('should add the promo code and check the discount name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode_2', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, discountData.discountCode);

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.contains(discountData.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue1', baseContext);

      const discount = parseFloat(discountData.discountValue.toString()).toFixed(2);

      const discountValue = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(discountValue).to.contains(`-€${discount}`);

      const subTotalDiscount = await foHummingbirdCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toString()).to.equal(`-${discountData.discountValue}`);
    });

    it('should check the shipping cost', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost_2', baseContext);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.equal('Free');
    });

    it('should check the Total (tax incl.) after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount1', baseContext);

      const discount = parseFloat(discountData.discountValue.toString());

      const totalBeforeDiscount = dataProducts.demo_3.finalPrice + dataProducts.demo_5.finalPrice;

      const totalAfterDiscount = await foHummingbirdCartPage.getATIPrice(page);
      expect(totalAfterDiscount.toString()).to.equal((totalBeforeDiscount - discount).toFixed(2));
    });
  });

  describe('Edit discount in BO and check it in FO', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitle);
    });

    it('should edit the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editDiscount', baseContext);

      const errorMessage = await boDiscountsCreatePage.createDiscount(page, editDiscountData);
      expect(errorMessage).to.contains(boDiscountsCreatePage.successfulUpdateMessage);
    });

    it('should go back to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO', baseContext);

      page = await boDiscountsCreatePage.changePage(browserContext, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdCartPage.pageTitle);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue_2', baseContext);

      await foHummingbirdCartPage.reloadPage(page);

      const discount = utilsCore.percentage(parseFloat(editDiscountData.discountValue.toString()),
        20) + parseFloat(editDiscountData.discountValue.toString());

      const discountValue = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(discountValue).to.contains(`-€${discount.toFixed(2)}`);

      const subTotalDiscount = await foHummingbirdCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toString()).to.equal(`-${discount}`);
    });

    it('should check the shipping cost', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost_3', baseContext);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.eq('Free');
    });

    it('should check the Total (tax incl.) after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount_2', baseContext);

      const total = dataProducts.demo_3.finalPrice + dataProducts.demo_5.price;
      const discount = utilsCore.percentage(parseFloat(editDiscountData.discountValue.toString()),
        20) + parseFloat(editDiscountData.discountValue.toString());

      const totalAfterDiscount = await foHummingbirdCartPage.getATIPrice(page);
      expect(totalAfterDiscount.toString()).to.equal((total - discount).toFixed(2));
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

    it('should delete the create discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteDiscount', baseContext);

      const validationMessage = await boDiscountsPage.deleteDiscount(page, 1);
      expect(validationMessage).to.contains(boDiscountsPage.successfulDeleteMessage);
    });

    it('should go back to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO_3', baseContext);

      page = await boDiscountsCreatePage.changePage(browserContext, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdCartPage.pageTitle);
    });

    it('should check the Total (tax incl.) after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount_3', baseContext);

      await foHummingbirdCartPage.reloadPage(page);
      const total = dataProducts.demo_3.finalPrice + dataProducts.demo_5.finalPrice;

      const totalAfterDiscount = await foHummingbirdCartPage.getATIPrice(page);
      expect(totalAfterDiscount.toString()).to.equal(total.toFixed(2));
    });
  });

  // 1 - Post-condition: Disable discount
  setFeatureFlag(boFeatureFlagPage.featureFlagDiscount, false, `${baseContext}_postTest`);
});
