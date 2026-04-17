import testContext from '@utils/testContext';
import {expect} from 'chai';

import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';

import {
  boDiscountsPage,
  boDiscountsCreatePage,
  boDashboardPage,
  boLoginPage,
  boFeatureFlagPage,
  type BrowserContext,
  dataAddresses,
  dataCarriers,
  dataCountries,
  dataCustomers,
  dataProducts,
  dataStates,
  FakerAddress,
  FakerDiscount,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdSearchResultsPage,
  foHummingbirdModalQuickViewPage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdMyAccountPage,
  foHummingbirdMyAddressesPage,
  foHummingbirdMyAddressesCreatePage,
  foHummingbirdCartPage,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_discountV2_minimumPurchaseAmountOnFreeShipping';

describe('BO - Catalog - Discounts : Minimum purchase amount (On free shipping)', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const discountData: FakerDiscount = new FakerDiscount({
    discountType: 'free_shipping',
    name: 'Test',
    noProductCondition: true,
    minimumPurchaseAmount: true,
    minimumAmountValue: 50,
    minimumAmountTax: 'Tax included',
    discountCode: 'TEST',
  });
  const discountErrorWithoutName: FakerDiscount = new FakerDiscount({
    ...discountData,
    name: ' ',
  });
  const discountErrorMinimumAmountZero: FakerDiscount = new FakerDiscount({
    ...discountData,
    minimumAmountValue: 0,
  });
  const discountErrorMinimumAmountNegative: FakerDiscount = new FakerDiscount({
    ...discountData,
    minimumAmountValue: -50,
  });
  const discountErrorMinimumAmountText: FakerDiscount = new FakerDiscount({
    ...discountData,
    minimumAmountValue: 'azerty',
    minimumAmountTax: 'Tax included',
  });
  const discountDataCountryFR: FakerDiscount = new FakerDiscount({
    ...discountData,
    deliveryConditionsCountries: [
      dataCountries.france,
    ],
  });
  const discountDataTaxExcluded: FakerDiscount = new FakerDiscount({
    ...discountData,
    minimumAmountTax: 'Tax excluded',
  });
  const customerAddressUS: FakerAddress = new FakerAddress({
    ...dataAddresses.address_2,
    state: dataStates.usIowa.name,
    country: dataCountries.unitedStates.name,
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Pre-condition: Enable discount
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

      await boDashboardPage.closeSfToolBar(page);
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
      await boDiscountsPage.selectDiscountType(page, discountErrorWithoutName.discountType!);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitle);
    });

    it('should create a discount without name and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_1', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountErrorWithoutName);
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'name');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageNameRequired);
    });

    it('should create a discount with a minimum amount value = 0 euro and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_2', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountErrorMinimumAmountZero);
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'amount');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageValueLowerThanZero);
    });

    it('should create a discount with a minimum amount value < 0 and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_3', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountErrorMinimumAmountNegative);
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'amount');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageValueLowerThanZero);
    });

    it('should create a discount with a minimum amount value = azerty and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_4', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountErrorMinimumAmountText);
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'amount');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageMinPurchaseAmountNotnumber);
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
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct_demo3', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_3.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it(`should add the product '${dataProducts.demo_3.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart_demo3', baseContext);

      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const shoppingCarts = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(shoppingCarts).to.equal(1);
    });

    it('should add the discount and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountToCart_errorMessage_1', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, discountData.discountCode);

      const cartRuleErrorMessage = await foHummingbirdCartPage.getCartRuleErrorMessage(page);
      expect(cartRuleErrorMessage).to.equals(`${foHummingbirdCartPage.minimumAmountErrorMessage
      } €${parseFloat(discountData.minimumAmountValue.toString()).toFixed(2)}.`);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'customerLogin', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/39929
    it.skip('should go to account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage_1', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdMyAccountPage.pageTitle);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/39929
    it.skip('should go to the "Your addresses" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage_1', baseContext);

      await foHummingbirdMyAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await foHummingbirdMyAddressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAddressesPage.pageTitle);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/39929
    it.skip('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage_1', baseContext);

      const addressPosition = await foHummingbirdMyAddressesPage.getAddressPosition(page, customerAddressUS.alias);
      await foHummingbirdMyAddressesPage.goToEditAddressPage(page, addressPosition);

      const pageHeaderTitle = await foHummingbirdMyAddressesCreatePage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAddressesCreatePage.updateFormTitle);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/39929
    it.skip('should update the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddress_1', baseContext);

      const textResult = await foHummingbirdMyAddressesCreatePage.setAddress(page, customerAddressUS);
      expect(textResult).to.equal(foHummingbirdMyAddressesPage.updateAddressSuccessfulMessage);
      await page.screenshot({
        path: `${global.SCREENSHOT.FOLDER}/updateAddress_1.png`,
        fullPage: true,
      });
    });

    it(`should search for the product '${dataProducts.demo_5.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct_1', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_5.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it(`should add the product '${dataProducts.demo_5.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart_1', baseContext);

      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const shoppingCarts = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(shoppingCarts).to.equal(2);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/39929
    it.skip('should add the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscount', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, discountData.discountCode);

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.contains(discountData.name);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/39929
    it.skip('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue1', baseContext);

      const discountValue = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(discountValue).to.contains('Free');

      const subTotalDiscount = await foHummingbirdCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toString()).to.equal(`-${dataCarriers.myCarrier.price.toFixed(0)}`);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/39929
    it.skip('should check the shipping cost', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost_1', baseContext);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.equal('Free');
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/39929
    it.skip('should check the Total (tax incl.) after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount_1', baseContext);

      const totalBeforeDiscount = dataProducts.demo_3.price
        - utilsCore.percentage(dataProducts.demo_3.price, dataProducts.demo_3.specificPrice.discount)
        + dataProducts.demo_5.priceTaxExcluded;

      const totalAfterDiscount = await foHummingbirdCartPage.getATIPrice(page);
      expect(totalAfterDiscount.toString()).to.equal(totalBeforeDiscount.toFixed(2));
    });
  });

  describe.skip('Edit discount in BO (with delivery countries) and check it in FO', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitle);
    });

    it('should go the discount edit page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editDiscount', baseContext);

      const pageTitle = await boDiscountsCreatePage.createDiscount(page, discountDataCountryFR);
      expect(pageTitle).to.contains(boDiscountsCreatePage.successfulUpdateMessage);
    });

    it('should go back to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO_1', baseContext);

      page = await boDiscountsCreatePage.changePage(browserContext, 1);

      await page.reload();

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdCartPage.pageTitle);
    });

    it('should check the shipping cost', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost_2', baseContext);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.equal(`€${dataCarriers.myCarrier.price.toFixed(2)}`);
    });

    it('should check the Total (tax incl.) after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount_2', baseContext);

      const total = dataProducts.demo_3.price
        - utilsCore.percentage(dataProducts.demo_3.price, dataProducts.demo_3.specificPrice.discount)
        + dataProducts.demo_5.priceTaxExcluded
        + dataCarriers.myCarrier.price;

      const atiPrice = await foHummingbirdCartPage.getATIPrice(page);
      expect(atiPrice.toString()).to.equal(total.toFixed(2));
    });

    it('should add the discount and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountToCart_errorMessage_2', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, discountData.discountCode);

      const cartRuleErrorMessage = await foHummingbirdCartPage.getCartRuleErrorMessage(page);
      expect(cartRuleErrorMessage).to.equals(foHummingbirdCartPage.cartRuleCannotUseVoucherCountryDelivery);
    });
  });

  // @todo : https://github.com/PrestaShop/PrestaShop/issues/41057
  describe.skip('Edit discount (with tax excluded) in BO and check it in FO', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitle);
    });

    it('should edit the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editDiscount', baseContext);

      const errorMessage = await boDiscountsCreatePage.createDiscount(page, discountDataTaxExcluded);
      expect(errorMessage).to.contains(boDiscountsCreatePage.successfulUpdateMessage);
    });

    // Check
    /* We have this result :
      2 items €57.72
      Shipping : €7.00
      Total (tax incl.) €64.72
    */

    it('should go back to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO_2', baseContext);

      page = await boDiscountsCreatePage.changePage(browserContext, 1);

      await page.reload();

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdCartPage.pageTitle);
    });

    it('should add the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscount', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, discountData.discountCode);

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.contains(discountData.name);
    });

    it('should check the shipping cost', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost_3', baseContext);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.equal(`€${dataCarriers.myCarrier.price.toFixed(2)}`);
    });

    it('should check the Total (tax incl.) after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount_3', baseContext);

      const total = dataProducts.demo_3.price
        - utilsCore.percentage(dataProducts.demo_3.price, dataProducts.demo_3.specificPrice.discount)
        + dataProducts.demo_5.priceTaxExcluded
        + dataCarriers.myCarrier.price;

      const atiPrice = await foHummingbirdCartPage.getATIPrice(page);
      expect(atiPrice.toString()).to.equal(total.toFixed(2));
    });

    it('should add the discount and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountToCart_errorMessage_3', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, discountData.discountCode);

      const cartRuleErrorMessage = await foHummingbirdCartPage.getCartRuleErrorMessage(page);
      expect(cartRuleErrorMessage).to.equals(foHummingbirdCartPage.cartRuleCannotUseVoucherCountryDelivery);
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

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/39929
    it.skip('should check the Total (tax incl.) after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount_4', baseContext);

      await foHummingbirdCartPage.reloadPage(page);
      const total = dataProducts.demo_3.price
        - utilsCore.percentage(dataProducts.demo_3.price, dataProducts.demo_3.specificPrice.discount)
        + dataProducts.demo_5.priceTaxExcluded
        + dataCarriers.myCarrier.price;

      const totalAfterDiscount = await foHummingbirdCartPage.getATIPrice(page);
      expect(totalAfterDiscount.toString()).to.equal(total.toFixed(2));
    });
  });

  // @todo : https://github.com/PrestaShop/PrestaShop/issues/39929
  describe.skip('Reset Adress on the FO', async () => {
    it('should go back to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO_4', baseContext);

      page = await boDiscountsCreatePage.changePage(browserContext, 1);

      await page.reload();

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdCartPage.pageTitle);
    });

    it('should go to account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage_2', baseContext);

      await foHummingbirdCartPage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to the "Your addresses" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage_2', baseContext);

      await foHummingbirdMyAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await foHummingbirdMyAddressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAddressesPage.pageTitle);
    });

    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage_2', baseContext);

      const addressPosition = await foHummingbirdMyAddressesPage.getAddressPosition(page, dataAddresses.address_2.alias);
      await foHummingbirdMyAddressesPage.goToEditAddressPage(page, addressPosition);

      const pageHeaderTitle = await foHummingbirdMyAddressesCreatePage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAddressesCreatePage.updateFormTitle);
    });

    it('should update the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddress_2', baseContext);

      const textResult = await foHummingbirdMyAddressesCreatePage.setAddress(page, dataAddresses.address_2);
      expect(textResult).to.equal(foHummingbirdMyAddressesPage.updateAddressSuccessfulMessage);

      await page.screenshot({
        path: `${global.SCREENSHOT.FOLDER}/updateAddress_2.png`,
        fullPage: true,
      });
    });
  });

  // Post-condition: Disable discount
  setFeatureFlag(boFeatureFlagPage.featureFlagDiscount, false, `${baseContext}_postTest`);
});
