// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

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
import {homePage as foHomePage} from '@pages/FO/classic/home';
import foProductPage from '@pages/FO/classic/product';

// Import data
import Carriers from '@data/demo/carriers';
import Customers from '@data/demo/customers';
import Products from '@data/demo/products';
import CartRuleData from '@data/faker/cartRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_conditions_carrierSelection';

/*
Scenario:
- Create cart rule with restricted carrier
- Go to FO, Add product to cart
- Try to add promo code and check error message
- Proceed to checkout and login with default customer
- Choose an address and continue
- Choose the not selected carrier, set promo code and check the error message
- Choose the restricted carrier
- Add promo code and check the total after discount
- Delete product from the cart
Post-condition:
- Delete the created cart rule
 */
describe('BO - Catalog - Cart rules : Carrier selection', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const newCartRuleData: CartRuleData = new CartRuleData({
    name: 'Cart rule carrier selection',
    code: '4QABV6L3',
    carrierRestriction: true,
    discountType: 'Amount',
    discountAmount: {
      value: 10,
      currency: 'EUR',
      tax: 'Tax included',
    },
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('BO : Create new cart rule with carrier restriction', async () => {
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

      page = await addCartRulePage.viewMyShop(page);
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });
  });

  describe('FO : Check the created cart rule', async () => {
    it('should go to the third product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage', baseContext);

      await foHomePage.goToProductPage(page, 3);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(Products.demo_6.name.toUpperCase());
    });

    it('should add product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foProductPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should add the promo code and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessage', baseContext);

      await cartPage.addPromoCode(page, newCartRuleData.code);

      const alertMessage = await cartPage.getCartRuleErrorMessage(page);
      expect(alertMessage).to.equal(cartPage.cartRuleChooseCarrierAlertMessageText);
    });

    it('should proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

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

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmAddressStep', baseContext);

      const isDeliveryStep = await checkoutPage.goToDeliveryStep(page);
      expect(isDeliveryStep, 'Delivery Step block is not displayed').to.eq(true);
    });

    it('should set the promo code and choose the wrong shipping method', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseWrongShippingMethod', baseContext);

      await checkoutPage.chooseShippingMethodAndAddComment(page, Carriers.default.id);
      await checkoutPage.addPromoCode(page, newCartRuleData.code);

      const errorShippingMessage = await checkoutPage.getCartRuleErrorMessage(page);
      expect(errorShippingMessage).to.equal(cartPage.cartRuleCannotUseVoucherAlertMessageText);
    });

    it('should choose the restricted shipping method and continue', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseShippingMethod', baseContext);

      await checkoutPage.goToShippingStep(page);

      await checkoutPage.chooseShippingMethodAndAddComment(page, Carriers.myCarrier.id);

      const priceATI = await checkoutPage.getATIPrice(page);
      expect(priceATI.toFixed(2))
        .to.equal((Products.demo_6.combinations[0].price + Carriers.myCarrier.priceTTC).toFixed(2));
    });

    it('should set the promo code for second time and check total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setPromoCode', baseContext);

      await checkoutPage.addPromoCode(page, newCartRuleData.code);

      const totalAfterDiscount = Products.demo_6.combinations[0].price
        - newCartRuleData.discountAmount!.value + Carriers.myCarrier.priceTTC;

      const priceATI = await checkoutPage.getATIPrice(page);
      expect(priceATI.toFixed(2)).to.equal(totalAfterDiscount.toFixed(2));
    });

    it('should go to Home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foHomePage.clickOnHeaderLink(page, 'Logo');

      const pageTitle = await foHomePage.getPageTitle(page);
      expect(pageTitle).to.equal(foHomePage.pageTitle);
    });

    it('should go to cart page and delete the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      await foHomePage.goToCartPage(page);

      await cartPage.deleteProduct(page, 1);

      const notificationNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.be.equal(0);
    });
  });

  // Post-condition : Delete the created cart rule
  deleteCartRuleTest(newCartRuleData.name, `${baseContext}_postTest`);
});
