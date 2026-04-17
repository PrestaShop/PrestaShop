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
  boOrdersPage,
  boOrdersViewBlockProductsPage,
  // FO pages
  foHummingbirdHomePage,
  foHummingbirdSearchResultsPage,
  foHummingbirdModalQuickViewPage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdCheckoutOrderConfirmationPage,
  // Data
  dataProducts,
  FakerDiscount,
  dataCustomers,
  dataCarriers,
  dataPaymentMethods,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_discountV2_triggerBasedOnProductsQuantity';

describe('BO - Catalog - Discounts : Trigger based on the total quantity of products in the cart', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const discountWithoutName: FakerDiscount = new FakerDiscount({
    discountType: 'cart_level',
    name: ' ',
    minimumProductQuantity: true,
    productQuantity: 5,
    discountValue: 10,
    discountReductionType: '€',
    discountTax: 'Tax included',
  });
  const discountMinProductQuantityZero: FakerDiscount = new FakerDiscount({
    ...discountWithoutName,
    name: 'test',
    productQuantity: '0',
  });
  const discountMinProductQuantityNegative: FakerDiscount = new FakerDiscount({
    ...discountWithoutName,
    name: 'test',
    productQuantity: -1,
  });
  const discountMinProductQuantityText: FakerDiscount = new FakerDiscount({
    ...discountWithoutName,
    name: 'test',
    productQuantity: 'azsdf',
  });
  const discountValueNegative: FakerDiscount = new FakerDiscount({
    ...discountWithoutName,
    name: 'test',
    productQuantity: 2,
    discountValue: -10,
  });

  const discountData: FakerDiscount = new FakerDiscount({
    ...discountWithoutName,
    name: 'test',
    productQuantity: 2,
    discountValue: 10,
    discountCode: 'TEST',
  });
  const editDiscountData: FakerDiscount = new FakerDiscount({
    ...discountWithoutName,
    name: 'test',
    productQuantity: 3,
    discountValue: 10,
    discountCode: 'TEST',
    discountTax: 'Tax included',
  });
  const secondEditDiscountData: FakerDiscount = new FakerDiscount({
    ...discountWithoutName,
    name: 'test',
    productQuantity: 3,
    discountValue: 10,
    discountCode: 'TEST',
    discountTax: 'Tax excluded',
  });
  const thirdEditDiscountData: FakerDiscount = new FakerDiscount({
    ...discountWithoutName,
    name: 'test',
    productQuantity: 2,
    discountValue: 100,
    discountTax: 'Tax included',
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

    it('should create a discount with a minimum product quantity = 0 euro and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_2', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountMinProductQuantityZero);
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'product quantity');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageValueLowerThanZero);
    });

    it('should create a discount with a minimum product quantity < 0 and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_3', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountMinProductQuantityNegative);
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'product quantity');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageValueLowerThanZero);
    });

    it('should create a discount with a minimum product quantity = text and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_4', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountMinProductQuantityText);
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'product quantity');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageValueLowerThanZero);
    });

    it('should create a discount with a discount value < 0 and check the error', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscount_5', baseContext);

      let errorMessage = await boDiscountsCreatePage.createDiscount(page, discountValueNegative);
      expect(errorMessage).to.contains(errorMessage);

      errorMessage = await boDiscountsCreatePage.getErrorMessageInvalidInput(page, 'value');
      expect(errorMessage).to.contains(boDiscountsCreatePage.errorMessageDiscountValue(
        discountValueNegative.discountValue.toString()));
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
      expect(voucherErrorText).to.equal(foHummingbirdCartPage.VoucherNotWithTheseProductsErrorMessage);
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
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, discountData.discountCode);

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
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost_3', baseContext);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.equal('Free');
    });

    it('should check the Total (tax incl.) after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount_1', baseContext);

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

    it('should edit the discount (product quantity = 3)', async function () {
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

    it('should check that the reduction is not applied', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue_2', baseContext);

      await foHummingbirdCartPage.reloadPage(page);

      const subTotal = await foHummingbirdCartPage.getATIPrice(page);
      expect(subTotal.toString()).to.equal((dataProducts.demo_3.finalPrice + dataProducts.demo_5.price).toFixed(2));
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO_2', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitle);
    });

    it('should edit the discount (discount tax = tax excluded)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editDiscount_2', baseContext);

      const errorMessage = await boDiscountsCreatePage.createDiscount(page, secondEditDiscountData);
      expect(errorMessage).to.contains(boDiscountsCreatePage.successfulUpdateMessage);
    });

    it('should go back to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO_2', baseContext);

      page = await boDiscountsCreatePage.changePage(browserContext, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdCartPage.pageTitle);
    });

    it('should check that the reduction is not applied', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue_3', baseContext);

      await foHummingbirdCartPage.reloadPage(page);

      const subTotal = await foHummingbirdCartPage.getATIPrice(page);
      expect(subTotal.toString()).to.equal((dataProducts.demo_3.finalPrice + dataProducts.demo_5.price).toFixed(2));
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO_3', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitle);
    });

    it('should edit the discount (product quantity = 2)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editDiscount_3', baseContext);

      const errorMessage = await boDiscountsCreatePage.createDiscount(page, thirdEditDiscountData);
      expect(errorMessage).to.contains(boDiscountsCreatePage.successfulUpdateMessage);
    });

    it('should go back to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO_3', baseContext);

      page = await boDiscountsCreatePage.changePage(browserContext, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdCartPage.pageTitle);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue_4', baseContext);

      await foHummingbirdCartPage.reloadPage(page);

      const discount = parseFloat((dataProducts.demo_3.finalPrice + dataProducts.demo_5.price).toFixed(2));

      const discountValue = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(discountValue).to.contains(`-€${discount}`);

      const subTotalDiscount = await foHummingbirdCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toString()).to.equal(`-${discount}`);
    });

    it('should check the shipping cost', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost_4', baseContext);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.equal('Free');
    });

    it('should check the Total (tax incl.) after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount_2', baseContext);

      const totalAfterDiscount = await foHummingbirdCartPage.getATIPrice(page);
      expect(totalAfterDiscount).to.equal(0);
    });

    it(`should add the product '${dataProducts.demo_1.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct_3', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_1.name);
      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const shoppingCarts = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(shoppingCarts).to.equal(3);
    });

    it(`should add the product '${dataProducts.demo_6.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct_4', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_6.name);
      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const shoppingCarts = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(shoppingCarts).to.equal(4);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue_5', baseContext);

      const discount = parseFloat(thirdEditDiscountData.discountValue.toString()).toFixed(2);

      const discountValue = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(discountValue).to.contains(`-€${discount}`);

      const subTotalDiscount = await foHummingbirdCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toString()).to.equal(`-${thirdEditDiscountData.discountValue}`);
    });

    it('should check the shipping cost', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost_2', baseContext);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.equal('Free');
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/39436
    it.skip('should check the Total (tax incl.) after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount_3', baseContext);

      const discount = parseFloat(thirdEditDiscountData.discountValue.toString());

      const totalBeforeDiscount = dataProducts.demo_3.finalPrice + dataProducts.demo_5.finalPrice + dataProducts.demo_1.finalPrice
        + dataProducts.demo_6.combinations[0].price;

      const totalAfterDiscount = await foHummingbirdCartPage.getATIPrice(page);
      expect(totalAfterDiscount.toString()).to.equal((totalBeforeDiscount - discount).toFixed(2));
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foHummingbirdCheckoutPage.clickOnSignIn(page);

      const isCustomerConnected = await foHummingbirdCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isCustomerConnected, 'Customer is not connected!').to.equal(true);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foHummingbirdCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.equal(true);
    });

    it('should select the first carrier and go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingPrice1', baseContext);

      await foHummingbirdCheckoutPage.chooseShippingMethod(page, dataCarriers.myCarrier.id);

      const isPaymentStep = await foHummingbirdCheckoutPage.goToPaymentStep(page);
      expect(isPaymentStep).to.eq(true);
    });

    it('should Pay by bank wire and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await foHummingbirdCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      const pageTitle = await foHummingbirdCheckoutOrderConfirmationPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCheckoutOrderConfirmationPage.pageTitle);

      const cardTitle = await foHummingbirdCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foHummingbirdCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('Update order from BO and check the discount', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO_4', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageProductsBlock1', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
      expect(pageTitle, 'Error when view order page!').to.contains(boOrdersViewBlockProductsPage.pageTitle);
    });

    it('should check that the discount table is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountTable_1', baseContext);

      const isVisible = await boOrdersViewBlockProductsPage.isDiscountListTableVisible(page);
      expect(isVisible, 'Discount list table is not visible').to.eq(true);
    });

    it('should delete 3 products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProductsFromOrder', baseContext);
      for (let i = 1; i <= 3; i++) {
        const textResult = await boOrdersViewBlockProductsPage.deleteProduct(page, 1);
        expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulDeleteProductMessage);
      }
    });

    it('should check number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      const productCount = await boOrdersViewBlockProductsPage.getProductsNumber(page);
      expect(productCount).to.equal(1);
    });

    it('should check that the discount table is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountTable', baseContext);

      await boOrdersViewBlockProductsPage.reloadPage(page);

      const isVisible = await boOrdersViewBlockProductsPage.isDiscountListTableVisible(page);
      expect(isVisible, 'Discount list table is visible').to.eq(false);
    });
  });

  describe('Delete discount', async () => {
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
