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
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import data
const CartRuleFaker = require('@data/faker/cartRule');
const {DefaultCustomer} = require('@data/demo/customer');
const {Products} = require('@data/demo/products');
const {PaymentMethods} = require('@data/demo/paymentMethods');

// import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_checkTotalAvailable';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

const cartRuleCode = new CartRuleFaker(
  {
    name: 'addCartRuleName',
    code: '4QABV6L3',
    discountType: 'Percent',
    discountPercent: 20,
  },
);

describe('BO - Catalog - Cart rules : Case 9 - Check Total available', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create a Cart rules', async () => {
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
      await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

      await cartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await addCartRulePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
    });

    it('should create cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

      const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleCode);
      await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

      // View my shop and init pages
      page = await addCartRulePage.viewMyShop(page);

      await foHomePage.changeLanguage(page, 'en');
      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });
  });

  const tests = [
    {args: {testIdentifier: 'cartRuleAccepted', testTitle: 'for the first time'}},
    {args: {testIdentifier: 'cartRuleNotAccepted', testTitle: 'for the second time'}},
  ];

  tests.forEach((test) => {
    describe(`Use Cart Rule ${test.args.testTitle}`, async () => {
      it('should go to the first product page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}GoToFirstProductPage`,
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
          `${test.args.testIdentifier}AddProductToCart`,
          baseContext,
        );

        await foProductPage.addProductToTheCart(page);

        const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
        await expect(notificationsNumber).to.be.equal(1);
      });

      it('should set the promo code', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}AddPromoCode`,
          baseContext,
        );

        await cartPage.addPromoCode(page, cartRuleCode.code);
      });

      if (test.args.testIdentifier === 'cartRuleAccepted') {
        it('should verify the total after discount', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `${test.args.testIdentifier}VerifyTotalAfterDiscount`,
            baseContext,
          );

          const discountedPrice = Products.demo_1.finalPrice
            - (Products.demo_1.finalPrice * cartRuleCode.discountPercent / 100);

          const priceATI = await cartPage.getATIPrice(page);
          await expect(priceATI).to.equal(parseFloat(discountedPrice.toFixed(2)));
        });

        it('should proceed to checkouts', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `${test.args.testIdentifier}ProceedToCheckoutAndSignIn`,
            baseContext,
          );

          // Proceed to checkout the shopping cart
          await cartPage.clickOnProceedToCheckout(page);
          const isCheckout = await checkoutPage.isCheckoutPage(page);
          await expect(isCheckout).to.be.true;
        });

        it('should checkout by signIn', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}SignInFO`, baseContext);

          await checkoutPage.clickOnSignIn(page);
          const isCustomerConnected = await checkoutPage.customerLogin(page, DefaultCustomer);
          await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
        });

        it('should confirm adress after signIn', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `${test.args.testIdentifier}ConfirmAdressStep`,
            baseContext,
          );

          const isDeliveryStep = await checkoutPage.goToDeliveryStep(page);
          await expect(isDeliveryStep, 'Delivery Step boc is not displayed').to.be.true;
        });

        it('should choose the shipping method', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `${test.args.testIdentifier}ShippingMethodStep`,
            baseContext,
          );

          const isPaymentStep = await checkoutPage.goToPaymentStep(page);
          await expect(isPaymentStep, 'Payment Step bloc is not displayed').to.be.true;
        });

        it('should choose the payment type and confirm the order', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `${test.args.testIdentifier}PaymentTypeStep`,
            baseContext,
          );

          await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
          const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
          // Check the confirmation message
          await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
        });

        it('should click on the logo of the shop', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `${test.args.testIdentifier}CheckLogoLink`,
            baseContext,
          );

          await foHomePage.clickOnHeaderLink(page, 'Logo');
          const pageTitle = await foHomePage.getPageTitle(page);
          await expect(pageTitle).to.equal(foHomePage.pageTitle);
        });
      }
      if (test.args.testIdentifier === 'cartRuleNotAccepted') {
        it('should search for the same created voucher and check the error message', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'searchExistingVoucher', baseContext);

          const voucherErrorText = await cartPage.getCartRuleErrorMessage(page);
          await expect(voucherErrorText).to.equal(cartPage.cartRuleAlreadyUsedErrorText);
        });
      }
    });
  });

  // post condition : delete cart rule
  deleteCartRuleTest(cartRuleCode.name, `${baseContext}_postTest_1`);
});
