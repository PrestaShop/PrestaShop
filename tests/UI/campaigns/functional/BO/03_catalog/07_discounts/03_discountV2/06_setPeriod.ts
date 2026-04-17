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
  FakerProduct,
  utilsDate,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_discountV2_setPeriod';

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
describe('BO - Catalog - Discounts : Set period', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const discountEndDateBeforeStart: FakerDiscount = new FakerDiscount({
    discountType: 'cart_level',
    name: 'Test',
    dateFrom: '2024-01-01',
    dateTo: '1810-01-01',
    singleProduct: true,
    specificProduct: dataProducts.demo_13,
    discountValue: 10,
    discountReductionType: '€',
    discountTax: 'Tax included',
    discountCode: 'CODE10',
  });
  const discountStartDateLetter: FakerDiscount = new FakerDiscount({
    ...discountEndDateBeforeStart,
    dateFrom: 'premier janvier deux milles vingt quatre',
    dateTo: '2025-01-01',
    specificProduct: new FakerProduct({name: ' '}),
  });
  const discountPeriodNeverExpiresData: FakerDiscount = new FakerDiscount({
    ...discountEndDateBeforeStart,
    dateFrom: '2024-01-01',
    dateTo: '',
    neverExpires: true,
    specificProduct: new FakerProduct({name: ' '}),
  });
  const discountPeriodExpiresData: FakerDiscount = new FakerDiscount({
    ...discountEndDateBeforeStart,
    dateFrom: '2024-01-01 12:00:00',
    dateTo: '2024-01-01 16:00',
    neverExpires: false,
    specificProduct: new FakerProduct({name: ' '}),
  });

  const today: string = utilsDate.getDateFormat('yyyy-mm-dd');

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

    it(`should click on create discount and choose the type '${discountEndDateBeforeStart.discountType}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDiscountType', baseContext);

      await boDiscountsPage.clickOnCreateDiscountButton(page);
      await boDiscountsPage.selectDiscountType(page, discountEndDateBeforeStart.discountType!);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitle);
    });

    it('should check the start and the end date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDate', baseContext);

      const startDate = await boDiscountsCreatePage.getValue(page, 'validFrom');
      expect(startDate).to.contains(today);

      const endDate = await boDiscountsCreatePage.getValue(page, 'validTo');
      expect(endDate).to.not.contains(today);
    });

    it('should create a discount end date before start date and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_1', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountEndDateBeforeStart);
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'date');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageExpirationDateBeforeStart);
    });

    it('should create a discount with the start date in letter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_2', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountStartDateLetter);
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'date');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageExpirationDateBeforeStart);

      const startDate = await boDiscountsCreatePage.getValue(page, 'validFrom');
      expect(startDate).to.contains(today);
    });

    it('should create a discount with period never expires', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_3', baseContext);

      const errorMessage = await boDiscountsCreatePage.createDiscount(page, discountPeriodNeverExpiresData);
      expect(errorMessage).to.contains(boDiscountsCreatePage.successfulCreationMessage);

      const endDate = await boDiscountsCreatePage.getValue(page, 'validTo');
      expect(endDate).to.equal('');
    });

    it('should go to Discounts page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToDiscountsPage', baseContext);

      await boDiscountsCreatePage.clickOnBreadCrumbLink(page, 'discounts');

      const pageTitle = await boDiscountsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsPage.pageTitle);
    });

    it('should check that the selected tab is All', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getSelectedGroup_2', baseContext);

      const activeGroup = await boDiscountsPage.getActiveTab(page);
      expect(activeGroup).to.equal('All');
    });

    it('should check the number of discounts in All tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDiscounts', baseContext);

      const number = await boDiscountsPage.getNumberOfElementInGrid(page);
      expect(number).to.equal(1);
    });

    it('should click on the Active tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnActiveTab', baseContext);

      await boDiscountsPage.goToTab(page, 'active');

      const activeGroup = await boDiscountsPage.getActiveTab(page);
      expect(activeGroup).to.equal('Active');
    });

    it('should check the number of discounts in Active tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDiscounts_2', baseContext);

      const number = await boDiscountsPage.getNumberOfElementInGrid(page);
      expect(number).to.equal(1);
    });

    it('should click on the Scheduled tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnScheduledTab', baseContext);

      await boDiscountsPage.goToTab(page, 'scheduled');

      const activeGroup = await boDiscountsPage.getActiveTab(page);
      expect(activeGroup).to.equal('Scheduled');
    });

    it('should check the number of discounts in Schedule tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDiscounts_3', baseContext);

      const number = await boDiscountsPage.getNumberOfElementInGrid(page);
      expect(number).to.equal(0);
    });

    it('should click on the Expired tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnExpiredTab', baseContext);

      await boDiscountsPage.goToTab(page, 'expired');

      const activeGroup = await boDiscountsPage.getActiveTab(page);
      expect(activeGroup).to.equal('Expired');
    });

    it('should check the number of discounts in Expired tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDiscounts_4', baseContext);

      const number = await boDiscountsPage.getNumberOfElementInGrid(page);
      expect(number).to.equal(0);
    });

    it('should go back to the All tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackAllTab', baseContext);

      await boDiscountsPage.goToTab(page, 'All');

      const activeGroup = await boDiscountsPage.getActiveTab(page);
      expect(activeGroup).to.equal('All');
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await boDiscountsCreatePage.viewMyShop(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.equal(true);
    });

    it(`should search for the product '${dataProducts.demo_15.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct_1', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_15.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it(`should add the product '${dataProducts.demo_15.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart_1', baseContext);

      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const shoppingCarts = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(shoppingCarts).to.equal(1);
    });

    it('should check the cart total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotal', baseContext);

      const totalAfterDiscount = await foHummingbirdCartPage.getATIPrice(page);
      expect(totalAfterDiscount.toString()).to.equal(dataProducts.demo_15.price.toFixed(2));
    });

    it('should add the promo code and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode_1', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, discountPeriodNeverExpiresData.discountCode);

      const voucherErrorText = await foHummingbirdCartPage.getCartRuleErrorMessage(page);
      expect(voucherErrorText).to.equal(foHummingbirdCartPage.VoucherNotWithTheseProductsErrorMessage);
    });
  });

  describe('Edit discount and check it in FO', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitle);
    });

    it('should go to edit discount page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditDiscountPage', baseContext);

      await boDiscountsPage.goToEditDiscountPage(page, 1);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitleEdit);
    });

    it('should edit the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editDiscount', baseContext);

      const errorMessage = await boDiscountsCreatePage.createDiscount(page, discountPeriodExpiresData);
      expect(errorMessage).to.contains(boDiscountsCreatePage.successfulUpdateMessage);
    });

    it('should go to Discounts page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToDiscountsPage_2', baseContext);

      await boDiscountsCreatePage.clickOnBreadCrumbLink(page, 'discounts');

      const pageTitle = await boDiscountsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsPage.pageTitle);
    });

    it('should check that the selected tab is All', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getSelectedGroup', baseContext);

      const activeGroup = await boDiscountsPage.getActiveTab(page);
      expect(activeGroup).to.equal('All');
    });

    it('should check the number of discounts in All tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDiscounts_5', baseContext);

      const number = await boDiscountsPage.getNumberOfElementInGrid(page);
      expect(number).to.equal(1);
    });

    it('should click on the Active tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnActiveTab_2', baseContext);

      await boDiscountsPage.goToTab(page, 'active');

      const activeGroup = await boDiscountsPage.getActiveTab(page);
      expect(activeGroup).to.equal('Active');
    });

    it('should check the number of discounts in Active tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDiscounts_6', baseContext);

      const number = await boDiscountsPage.getNumberOfElementInGrid(page);
      expect(number).to.equal(0);
    });

    it('should click on the Scheduled tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnScheduledTab_2', baseContext);

      await boDiscountsPage.goToTab(page, 'scheduled');

      const activeGroup = await boDiscountsPage.getActiveTab(page);
      expect(activeGroup).to.equal('Scheduled');
    });

    it('should check the number of discounts in Scheduled tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDiscounts_7', baseContext);

      const number = await boDiscountsPage.getNumberOfElementInGrid(page);
      expect(number).to.equal(0);
    });

    it('should click on the Expired tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnExpiredTab_2', baseContext);

      await boDiscountsPage.goToTab(page, 'expired');

      const activeGroup = await boDiscountsPage.getActiveTab(page);
      expect(activeGroup).to.equal('Expired');
    });

    it('should check the number of discounts in Expired tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDiscounts_8', baseContext);

      const number = await boDiscountsPage.getNumberOfElementInGrid(page);
      expect(number).to.equal(1);
    });

    it('should go back to the All tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackAllTab_2', baseContext);

      await boDiscountsPage.goToTab(page, 'All');

      const activeGroup = await boDiscountsPage.getActiveTab(page);
      expect(activeGroup).to.equal('All');
    });

    it('should go back to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO', baseContext);

      page = await boDiscountsCreatePage.changePage(browserContext, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdCartPage.pageTitle);
    });

    it('should add the promo code and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode_2', baseContext);

      await foHummingbirdCartPage.reloadPage(page);
      await foHummingbirdCartPage.addPromoCode(page, discountPeriodNeverExpiresData.discountCode);

      const voucherErrorText = await foHummingbirdCartPage.getCartRuleErrorMessage(page);
      expect(voucherErrorText).to.equal(foHummingbirdCartPage.expiredDiscountErrorMessage);
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
  });

  // 1 - Post-condition: Disable discount
  setFeatureFlag(boFeatureFlagPage.featureFlagDiscount, false, `${baseContext}_postTest`);
});
