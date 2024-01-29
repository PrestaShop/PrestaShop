// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import basicHelper from '@utils/basicHelper';

// Import commonTests
import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import cartRulesPage from '@pages/BO/catalog/discounts';
import addCartRulePage from '@pages/BO/catalog/discounts/add';
import dashboardPage from '@pages/BO/dashboard';
// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';
import checkoutPage from '@pages/FO/classic/checkout';
import orderConfirmationPage from '@pages/FO/classic/checkout/orderConfirmation';
import {homePage} from '@pages/FO/classic/home';
import productPage from '@pages/FO/classic/product';
import {loginPage} from '@pages/FO/classic/login';

// Import data
import Customers from '@data/demo/customers';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import CartRuleData from '@data/faker/cartRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_conditions_checkTotalAvailableForEachUser';

/*
Scenario:
- Create cart rule with total for each user = 1
- Go to FO, Add product to cart, login by default customer
- Add promo code and check total after discount
- Complete the order
- Try to use a second time the same promo code anc check the error message
- Logout and try to set a third time the promo code and check total after discount
Post-condition:
- Delete the created cart rule
*/
describe('BO - Catalog - Cart rules : Check Total available for each user', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const newCartRuleData: CartRuleData = new CartRuleData({
    name: 'New cart rule',
    code: '4QABV6L3',
    quantity: 2,
    quantityPerUser: 1,
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

  describe('BO : Create cart rule', async () => {
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

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

      await cartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await addCartRulePage.getPageTitle(page);
      expect(pageTitle).to.contains(addCartRulePage.pageTitle);
    });

    it('should create cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

      const validationMessage = await addCartRulePage.createEditCartRules(page, newCartRuleData);
      expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

      // View my shop and init pages
      page = await addCartRulePage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });
  });

  [
    {args: {testIdentifier: 'cartRuleAccepted', testTitle: 'for the first time by default customer'}},
    {args: {testIdentifier: 'cartRuleNotAccepted', testTitle: 'for the second time by default customer'}},
    {args: {testIdentifier: 'cartRuleAccepted_2', testTitle: 'for the third time without sign in'}},
  ].forEach((test, index) => {
    describe(`FO : Check the created cart rule '${test.args.testTitle}'`, async () => {
      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFirstProductPage${index}`, baseContext);

        await homePage.goToProductPage(page, 1);

        const pageTitle = await productPage.getPageTitle(page);
        expect(pageTitle.toUpperCase()).to.contains(Products.demo_1.name.toUpperCase());
      });

      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

        await productPage.addProductToTheCart(page);

        const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(1);
      });

      it('should set the promo code', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addPromoCode${index}`, baseContext);

        await cartPage.addPromoCode(page, newCartRuleData.code);
      });

      if (test.args.testIdentifier === 'cartRuleAccepted') {
        it('should verify the total after discount', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalAfterDiscount', baseContext);

          const discountedPrice = Products.demo_1.finalPrice
            - await basicHelper.percentage(Products.demo_1.finalPrice, newCartRuleData.discountPercent!);

          const totalAfterDiscount = await cartPage.getATIPrice(page);
          expect(totalAfterDiscount).to.equal(parseFloat(discountedPrice.toFixed(2)));
        });

        it('should proceed to checkout', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'ProceedToCheckout', baseContext);

          // Proceed to checkout the shopping cart
          await cartPage.clickOnProceedToCheckout(page);

          const isCheckout = await checkoutPage.isCheckoutPage(page);
          expect(isCheckout).to.eq(true);
        });

        it('should sign in by default customer', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

          await checkoutPage.clickOnSignIn(page);

          const isCustomerConnected = await checkoutPage.customerLogin(page, Customers.johnDoe);
          expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
        });

        it('should go to delivery address step', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'confirmAddressStep', baseContext);

          const isDeliveryStep = await checkoutPage.goToDeliveryStep(page);
          expect(isDeliveryStep, 'Delivery Step boc is not displayed').to.eq(true);
        });

        it('should choose the shipping method', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'shippingMethodStep', baseContext);

          const isPaymentStep = await checkoutPage.goToPaymentStep(page);
          expect(isPaymentStep, 'Payment Step bloc is not displayed').to.eq(true);
        });

        it('should choose the payment type and confirm the order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'choosePaymentMethod', baseContext);

          await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

          const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
          // Check the confirmation message
          expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
        });

        it('should go to home page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

          await homePage.clickOnHeaderLink(page, 'Logo');

          const pageTitle = await homePage.getPageTitle(page);
          expect(pageTitle).to.equal(homePage.pageTitle);
        });
      }
      if (test.args.testIdentifier === 'cartRuleNotAccepted') {
        it('should check the promo code error message', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessage', baseContext);

          const voucherErrorText = await cartPage.getCartRuleErrorMessage(page);
          expect(voucherErrorText).to.equal(cartPage.cartRuleLimitUsageErrorText);
        });

        it('should sign out', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'signOut', baseContext);

          await cartPage.logout(page);
          await loginPage.clickOnHeaderLink(page, 'Logo');

          const isCustomerConnected = await homePage.isCustomerConnected(page);
          expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
        });
      }

      if (test.args.testIdentifier === 'cartRuleAccepted_2') {
        it('should check that the promo code is applied to the cart', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkPromoCode', baseContext);

          const discountedPrice = Products.demo_1.finalPrice
            - await basicHelper.percentage(Products.demo_1.finalPrice, newCartRuleData.discountPercent!);

          const totalAfterDiscount = await cartPage.getATIPrice(page);
          expect(totalAfterDiscount).to.equal(parseFloat(discountedPrice.toFixed(2)));
        });

        it('should delete the last product from the cart', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'deleteLastProduct', baseContext);

          await cartPage.deleteProduct(page, 1);

          const notificationNumber = await cartPage.getCartNotificationsNumber(page);
          expect(notificationNumber).to.eq(0);
        });
      }
    });
  });

  // Post-condition : Delete the created cart rule
  deleteCartRuleTest(newCartRuleData.name, `${baseContext}_postTest`);
});
