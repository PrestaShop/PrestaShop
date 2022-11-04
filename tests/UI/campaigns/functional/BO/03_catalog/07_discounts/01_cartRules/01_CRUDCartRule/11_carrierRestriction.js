require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import common tests
const {deleteCartRuleTest} = require('@commonTests/BO/catalog/createDeleteCartRule.js');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const addCartRulePage = require('@pages/BO/catalog/discounts/add');

// Import FO pages
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');

// Import data
const CartRuleFaker = require('@data/faker/cartRule');
const {DefaultCustomer} = require('@data/demo/customer');
const {Products} = require('@data/demo/products');
const {Carriers} = require('@data/demo/carriers');

// import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_carrierRestriction';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

const cartRuleCode = new CartRuleFaker(
  {
    name: 'addCartRuleName',
    code: '4QABV6L3',
    carrierRestriction: true,
    discountType: 'Amount',
    discountAmount: {
      value: 10,
      currency: 'EUR',
      tax: 'Tax included',
    },
  },
);

describe('BO - Catalog - Cart rules : Case 11 - Carrier Restriction', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create a Cart Rules', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'goToDiscountsPage',
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

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'goToNewCartRulePage',
        baseContext,
      );

      await cartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await addCartRulePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
    });

    it('should create cart rule', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'createCartRule',
        baseContext,
      );

      const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleCode);
      await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'viewMyShop1',
        baseContext,
      );

      page = await addCartRulePage.viewMyShop(page);

      await foHomePage.changeLanguage(page, 'en');
      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });
  });

  describe('Use Cart Rule', async () => {
    it('should go to the first product page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'goToFirstProductPage',
        baseContext,
      );

      await foHomePage.goToProductPage(page, 1);

      const pageTitle = await foProductPage.getPageTitle(page);
      await expect(pageTitle.toUpperCase()).to.contains(Products.demo_1.name.toUpperCase());
    });

    it('should add product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'addProductToCart',
        baseContext,
      );

      await foProductPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(1);
    });

    it('should add the promo code and get "You must choose a carrier before applying this voucher" error message',
      async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'addPromoCodeAndGetErrorMessage',
          baseContext,
        );

        await cartPage.addPromoCode(page, cartRuleCode.code);

        const alertMessage = await cartPage.getCartRuleErrorMessage(page);
        await expect(alertMessage).to.equal(cartPage.cartRuleChooseCarrierAlertMessageText);
      });

    it('should proceed to checkouts', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'proceedToCheckoutAndSignIn',
        baseContext,
      );

      await cartPage.clickOnProceedToCheckout(page);

      const isCheckout = await checkoutPage.isCheckoutPage(page);
      await expect(isCheckout).to.be.true;
    });

    it('should be authentificated', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'signInFO',
        baseContext,
      );

      await checkoutPage.clickOnSignIn(page);

      const isCustomerConnected = await checkoutPage.customerLogin(page, DefaultCustomer);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should confirm adress after signIn', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'confirmAddressStep',
        baseContext,
      );

      const isDeliveryStep = await checkoutPage.goToDeliveryStep(page);
      await expect(isDeliveryStep, 'Delivery Step block is not displayed').to.be.true;
    });

    it('should set the promo code and choose the wrong shipping method', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'addPromoCodeAndChooseWrongShippingMethod',
        baseContext,
      );

      await checkoutPage.chooseShippingMethodAndAddComment(page, 1);
      await checkoutPage.addPromoCode(page, cartRuleCode.code);

      const errorShippingMessage = await checkoutPage.getCartRuleErrorMessage(page);
      await expect(errorShippingMessage).to.equal(cartPage.cartRuleCannotUseVoucherAlertMessageText);
    });

    it('should set the promo code for second time and check total after discount', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'addPromoCodeAndVerifyTotalAfterDiscount',
        baseContext,
      );

      await checkoutPage.goToShippingStep(page);
      await checkoutPage.chooseShippingMethodAndAddComment(page, 2);
      await checkoutPage.addPromoCode(page, cartRuleCode.code);

      let discountedPrice = cartRuleCode.discountAmount.value;
      if (discountedPrice >= Products.demo_1.finalPrice) {
        discountedPrice = Products.demo_1.finalPrice;
      }

      const priceATI = await checkoutPage.getATIPrice(page);
      await expect(priceATI).to.equal(parseFloat(
        (Products.demo_1.finalPrice - discountedPrice + Carriers.myCarrier.priceTTC)
          .toFixed(2),
      ),
      );
    });

    it('should remove the discount', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'removeTheDiscount',
        baseContext,
      );

      const isDeleteIconNotVisible = await checkoutPage.removePromoCode(page);
      await expect(isDeleteIconNotVisible, 'The discount is not removed').to.be.true;
    });

    it('should click on the logo of the shop', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'checkLogoLink',
        baseContext,
      );

      await foHomePage.clickOnHeaderLink(page, 'Logo');
      const pageTitle = await foHomePage.getPageTitle(page);
      await expect(pageTitle).to.equal(foHomePage.pageTitle);
    });

    it('should click on the cart link', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'clickOnCartLink',
        baseContext,
      );

      await foHomePage.goToCartPage(page);
    });

    it('should remove product from shopping cart', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'removeProduct1',
        baseContext,
      );

      await cartPage.deleteProduct(page, 1);

      const notificationNumber = await cartPage.getCartNotificationsNumber(page);
      await expect(notificationNumber).to.be.equal(0);
    });
  });

  // post condition : delete cart rule
  deleteCartRuleTest(cartRuleCode.name, `${baseContext}_postTest_1`);
});
