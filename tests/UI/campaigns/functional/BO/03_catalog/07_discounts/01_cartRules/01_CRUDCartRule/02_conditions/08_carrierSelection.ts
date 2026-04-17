import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

import {
  boCartRulesPage,
  boCartRulesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  dataProducts,
  FakerCartRule,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const newCartRuleData: FakerCartRule = new FakerCartRule({
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
  const newCartRuleDiscount: number = parseFloat(newCartRuleData.discountAmount!.value.toString());

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('BO : Create new cart rule with carrier restriction', async () => {
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

      const pageTitle = await boCartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
    });

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

      await boCartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
    });

    it('should create cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, newCartRuleData);
      expect(validationMessage).to.contains(boCartRulesCreatePage.successfulCreationMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

      page = await boCartRulesCreatePage.viewMyShop(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });
  });

  describe('FO : Check the created cart rule', async () => {
    it('should go to the third product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage', baseContext);

      await foHummingbirdHomePage.goToProductPage(page, 3);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_6.name.toUpperCase());
    });

    it('should add product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdProductPage.addProductToTheCart(page);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should add the promo code and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessage', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, newCartRuleData.code);

      const alertMessage = await foHummingbirdCartPage.getCartRuleErrorMessage(page);
      expect(alertMessage).to.equal(foHummingbirdCartPage.cartRuleChooseCarrierAlertMessageText);
    });

    it('should proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const isCheckout = await foHummingbirdCheckoutPage.isCheckoutPage(page);
      expect(isCheckout).to.eq(true);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foHummingbirdCheckoutPage.clickOnSignIn(page);

      const isCustomerConnected = await foHummingbirdCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmAddressStep', baseContext);

      const isDeliveryStep = await foHummingbirdCheckoutPage.goToDeliveryStep(page);
      expect(isDeliveryStep, 'Delivery Step block is not displayed').to.eq(true);
    });

    it('should set the promo code and choose the wrong shipping method', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseWrongShippingMethod', baseContext);

      await foHummingbirdCheckoutPage.chooseShippingMethodAndAddComment(page, dataCarriers.clickAndCollect.id);
      await foHummingbirdCheckoutPage.addPromoCode(page, newCartRuleData.code);

      const errorShippingMessage = await foHummingbirdCheckoutPage.getCartRuleErrorMessage(page);
      expect(errorShippingMessage).to.equal(foHummingbirdCartPage.cartRuleCannotUseVoucherAlertMessageText);
    });

    it('should choose the restricted shipping method and continue', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseShippingMethod', baseContext);

      await foHummingbirdCheckoutPage.goToShippingStep(page);

      await foHummingbirdCheckoutPage.chooseShippingMethodAndAddComment(page, dataCarriers.myCarrier.id);

      const priceATI = await foHummingbirdCheckoutPage.getATIPrice(page);
      expect(priceATI.toFixed(2))
        .to.equal((dataProducts.demo_6.combinations[0].price + dataCarriers.myCarrier.priceTTC).toFixed(2));
    });

    it('should set the promo code for second time and check total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setPromoCode', baseContext);

      await foHummingbirdCheckoutPage.addPromoCode(page, newCartRuleData.code);

      const totalAfterDiscount = dataProducts.demo_6.combinations[0].price
        - newCartRuleDiscount + dataCarriers.myCarrier.priceTTC;

      const priceATI = await foHummingbirdCheckoutPage.getATIPrice(page);
      expect(priceATI.toFixed(2)).to.equal(totalAfterDiscount.toFixed(2));
    });

    it('should go to Home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foHummingbirdHomePage.clickOnHeaderLink(page, 'Logo');

      const pageTitle = await foHummingbirdHomePage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdHomePage.pageTitle);
    });

    it('should go to cart page and delete the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      await foHummingbirdHomePage.goToCartPage(page);

      await foHummingbirdCartPage.deleteProduct(page, 1);

      const notificationNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.be.equal(0);
    });
  });

  // Post-condition : Delete the created cart rule
  deleteCartRuleTest(newCartRuleData.name, `${baseContext}_postTest`);
});
