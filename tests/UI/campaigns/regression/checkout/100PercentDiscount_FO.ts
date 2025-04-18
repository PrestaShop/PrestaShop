// Import utils
import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCartRulesPage,
  boCartRulesCreatePage,
  boDashboardPage,
  boLoginPage,
  boOrderSettingsPage,
  type BrowserContext,
  dataCMSPages,
  FakerAddress,
  FakerCartRule,
  FakerCustomer,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicModalBlockCartPage,
  foClassicModalQuickViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'regression_checkout_100PercentDiscount_FO';

/**
 * https://github.com/PrestaShop/PrestaShop/issues/9927
 *
 * If order has 100% discount and free shipping AND shop does not ask for
 * terms and conditions => checkout button will stay disabled even tho it should
 * not be disabled
 *
 * login to BO
 * add new discount (-100% free shipping)
 * Change terms and conditions setting
 */
describe('Regression - Checkout: Create 100% discount with free shipping discount code', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const percentCartRule: FakerCartRule = new FakerCartRule({
    name: 'discount100',
    code: 'discount100',
    discountType: 'Percent',
    discountPercent: 100,
    freeShipping: true,
  });
  const customerData: FakerCustomer = new FakerCustomer({password: ''});
  const addressData: FakerAddress = new FakerAddress({country: 'France'});

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  /**
   * Setup, create new 100% discount code with free shipping
   */
  describe('SETUP', async () => {
    it('should go to cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCartRulesPageToCreate', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
      );

      const pageTitle = await boCartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
    });

    describe('Create a percentage cart rule', async () => {
      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage1', baseContext);

        await boCartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
      });

      it('should create new cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createPercentCartRule', baseContext);

        const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, percentCartRule);
        expect(validationMessage).to.contains(boCartRulesCreatePage.successfulCreationMessage);
      });
    });

    describe('Change terms and conditions setting no', async () => {
      it("should go to 'Shop Parameters > Order Settings' page", async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.shopParametersParentLink,
          boDashboardPage.orderSettingsLink,
        );

        const pageTitle = await boOrderSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
      });

      it('should change the terms and conditions back to disabled', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableTermAndConditions', baseContext);

        const result = await boOrderSettingsPage.setTermsOfService(page, false);
        expect(result).to.contains(boOrderSettingsPage.successfulUpdateMessage);
      });
    });
  });

  /**
   * Test case
   * go through the checkout with out discount code and check the complete order button state
   */
  describe('Place an order with discounts in FO', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await boCartRulesPage.viewMyShop(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should quick view the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewTheFirstProduct', baseContext);

      await foClassicHomePage.quickViewProduct(page, 1);

      const isQuickViewModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isQuickViewModalVisible).to.equal(true);
    });

    it('should add product to cart and Proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicModalQuickViewPage.addToCartByQuickView(page);
      await foClassicModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
    });

    it('should add our discount code and check that the total price is 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPercentDiscount', baseContext);

      await foClassicCartPage.addPromoCode(page, percentCartRule.code);

      const totalPrice = await foClassicCartPage.getATIPrice(page);
      expect(totalPrice, 'Order total price is incorrect').to.equal(0);
    });

    it('should go to checkout process', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

      await foClassicCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should fill personal information as a guest', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformation', baseContext);

      const isStepPersonalInfoCompleted = await foClassicCheckoutPage.setGuestPersonalInformation(page, customerData);
      expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.eq(true);
    });

    it('should fill address form and go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

      const isStepAddressComplete = await foClassicCheckoutPage.setAddress(page, addressData);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should go to last step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLastStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should contain no payment needed text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoPaymentNeededText', baseContext);

      const noPaymentNeededText = await foClassicCheckoutPage.getNoPaymentNeededBlockContent(page);
      expect(noPaymentNeededText).to.contains(foClassicCheckoutPage.noPaymentNeededText);
    });

    it('should check that complete order button is enabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCompleteIsNotDisabled', baseContext);

      const confirmButtonVisible = await foClassicCheckoutPage.isPaymentConfirmationButtonVisibleAndEnabled(page);
      expect(confirmButtonVisible, 'Confirm button visible').to.eq(true);
    });

    it('should complete the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'completeOrder', baseContext);

      // complete the order
      await foClassicCheckoutPage.orderWithoutPaymentMethod(page);

      // Check that we got to order confirmation (probably not necessary)
      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  /**
   * Cleanup
   * Remove the discount code and the generated order
   * Change the terms and conditions setting back to enabled
   */
  describe('CLEANUP', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BackToBOForCleanup', baseContext);

      page = await foClassicCheckoutPage.closePage(browserContext, page, 0);

      const pageTitle = await boOrderSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
    });

    describe('Delete created cart rules', async () => {
      it('should go to cart rules page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCartRulesPageToDelete', baseContext);

        await boCartRulesPage.goToSubMenu(
          page,
          boCartRulesPage.catalogParentLink,
          boCartRulesPage.discountsLink,
        );

        const pageTitle = await boCartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
      });

      it('should delete our 100% cart rules', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'deleteCartRules', baseContext);

        const validationMessage = await boCartRulesPage.deleteCartRule(page);
        expect(validationMessage).to.contains(
          boCartRulesPage.successfulDeleteMessage,
        );
      });
    });

    describe('Change terms and conditions setting back to yes', async () => {
      it("should go to 'Shop Parameters > Order Settings' page", async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPageToReset', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.shopParametersParentLink,
          boDashboardPage.orderSettingsLink,
        );

        const pageTitle = await boOrderSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
      });

      it('should change the terms and conditions back to enabled', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetTermsAndConditionsValue', baseContext);

        const result = await boOrderSettingsPage.setTermsOfService(page, true, dataCMSPages.termsAndCondition.title);
        expect(result).to.contains(boOrderSettingsPage.successfulUpdateMessage);
      });
    });
  });
});
