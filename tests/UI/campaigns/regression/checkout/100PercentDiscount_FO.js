require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages

// BO pages
const dashboardPage = require('@pages/BO/dashboard');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const addCartRulePage = require('@pages/BO/catalog/discounts/add');
const orderSettingsPage = require('@pages/BO/shopParameters/orderSettings');


// FO pages
const homePage = require('@pages/FO/home');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import expect from chai
const {expect} = require('chai');

// Import faker data
const CartRuleFaker = require('@data/faker/cartRule');
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');

const percentCartRule = new CartRuleFaker({
  name: 'discount100',
  code: 'discount100',
  discountType: 'Percent',
  discountPercent: 100,
  freeShipping: true,
});

const customerData = new CustomerFaker({password: ''});
const addressData = new AddressFaker();

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'regression_checkout_100PercentDiscount_FO';

// Browser and tab
let browserContext;
let page;

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
describe('Create 100% discount with free shipping discount code', async () => {
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

  /**
   * Setup, create new 100% discount code with free shipping
   */
  describe('SETUP', async () => {
    it('should go to cart rule page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'goToCartRulesPageToCreate',
        baseContext,
      );

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.discountsLink,
      );

      const pageTitle = await cartRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    describe('Create a percentage cart rule', async () => {
      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'goToNewCartRulePage1',
          baseContext,
        );

        await cartRulesPage.goToAddNewCartRulesPage(page);
        const pageTitle = await addCartRulePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
      });

      it('should create new cart rule', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'createPercentCartRule',
          baseContext,
        );

        const validationMessage = await addCartRulePage.createEditCartRules(
          page,
          percentCartRule,
        );

        await expect(validationMessage).to.contains(
          addCartRulePage.successfulCreationMessage,
        );
      });
    });

    describe('Change terms and conditions setting no', async () => {
      it("should go to 'Shop Parameters > Order Settings' page", async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'goToOrderSettingsPage',
          baseContext,
        );

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.shopParametersParentLink,
          dashboardPage.orderSettingsLink,
        );

        const pageTitle = await orderSettingsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
      });

      it('should change the terms and conditions back to disabled', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'disableTermAndConditions',
          baseContext,
        );

        const result = await orderSettingsPage.setTermsOfService(page, false);
        await expect(result).to.contains(
          orderSettingsPage.successfulUpdateMessage,
        );
      });
    });
  });

  /**
   * Test case
   * go through the checkout with out discount code and check the complete order button state
   */
  describe('Place an order with discounts in FO', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'viewMyShop',
        baseContext,
      );

      page = await cartRulesPage.viewMyShop(page);
      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should add first product to cart and Proceed to checkout', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'addProductToCart',
        baseContext,
      );

      await homePage.addProductToCartByQuickView(page, 1, '1');
      await homePage.proceedToCheckout(page);
      const pageTitle = await cartPage.getPageTitle(page);
      await expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should add our discount code and check that the total price is 0', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'addPercentDiscount',
        baseContext,
      );

      await cartPage.addPromoCode(page, percentCartRule.code);

      const totalPrice = await cartPage.getATIPrice(page);
      await expect(totalPrice, 'Order total price is incorrect').to.equal(0);
    });

    it('should go to checkout process', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'proceedToCheckout',
        baseContext,
      );

      await cartPage.clickOnProceedToCheckout(page);
      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      await expect(isCheckoutPage).to.be.true;
    });

    it('should fill personal information as a guest', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'setPersonalInformation',
        baseContext,
      );

      const isStepPersonalInfoCompleted = await checkoutPage.setGuestPersonalInformation(
        page,
        customerData,
      );
      await expect(
        isStepPersonalInfoCompleted,
        'Step personal information is not completed',
      ).to.be.true;
    });

    it('should fill address form and go to delivery step', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'setAddressStep',
        baseContext,
      );

      const isStepAddressComplete = await checkoutPage.setAddress(
        page,
        addressData,
      );

      await expect(isStepAddressComplete, 'Step Address is not complete')
        .to.be.true;
    });

    it('should go to last step', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'goToLastStep',
        baseContext,
      );

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete')
        .to.be.true;
    });

    it('should contain no payment needed text', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'checkNoPaymentNeededText',
        baseContext,
      );

      const noPaymentNeededText = await checkoutPage.getNoPaymentNeededBlockContent(page);

      await expect(noPaymentNeededText).to.contains(checkoutPage.noPaymentNeededText);
    });

    it('should check that complete order button is enabled', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'checkCompleteIsNotDisabled',
        baseContext,
      );

      const confirmButtonVisible = await checkoutPage.isPaymentConfirmationButtonVisibleAndEnabled(
        page,
      );

      await expect(confirmButtonVisible, 'Confirm button visible').to.be.true;
    });

    it('should complete the order', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'completeOrder',
        baseContext,
      );

      // complete the order
      await checkoutPage.orderWithoutPaymentMethod(page);

      // Check that we got to order confirmation (probably not necessary)
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(
        page,
      );

      await expect(cardTitle).to.contains(
        orderConfirmationPage.orderConfirmationCardTitle,
      );
    });
  });

  /**
   * Cleanup
   * Remove the discount code and the generated order
   * Change the terms and conditions setting back to enabled
   */
  describe('CLEANUP', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'BackToBOForCleanup',
        baseContext,
      );

      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await orderSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
    });

    describe('Delete created cart rules', async () => {
      it('should go to cart rules page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'goToCartRulesPageToDelete',
          baseContext,
        );

        await cartRulesPage.goToSubMenu(
          page,
          cartRulesPage.catalogParentLink,
          cartRulesPage.discountsLink,
        );

        const pageTitle = await cartRulesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
      });

      it('should delete our 100% cart rules', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'deleteCartRules',
          baseContext,
        );

        const validationMessage = await cartRulesPage.deleteCartRule(page);
        await expect(validationMessage).to.contains(
          cartRulesPage.successfulDeleteMessage,
        );
      });
    });

    describe('Change terms and conditions setting back to yes', async () => {
      it("should go to 'Shop Parameters > Order Settings' page", async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'goToOrderSettingsPageToReset',
          baseContext,
        );

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.shopParametersParentLink,
          dashboardPage.orderSettingsLink,
        );

        const pageTitle = await orderSettingsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
      });

      it('should change the terms and conditions back to enabled', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'resetTermsAndConditionsValue',
          baseContext,
        );

        const result = await orderSettingsPage.setTermsOfService(
          page,
          true,
          'Terms and conditions of use',
        );

        await expect(result).to.contains(
          orderSettingsPage.successfulUpdateMessage,
        );
      });
    });
  });
});
