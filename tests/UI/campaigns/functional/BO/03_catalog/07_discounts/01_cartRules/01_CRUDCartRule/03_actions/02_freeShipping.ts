import testContext from '@utils/testContext';
import {expect} from 'chai';

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
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdProductPage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsPlaywright,
  foHummingbirdCheckoutPage,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_actions_freeShipping';

describe('BO - Cart rules - Actions : Free shipping', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const cartRuleData: FakerCartRule = new FakerCartRule({
    name: 'Cart Rule Free Discount',
    description: 'It is a test for cart rules',
    code: 'ZD71W991',
    highlight: false,
    partialUse: true,
    priority: 1,
    status: true,
    freeShipping: true,
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('BO : Create cart rule', async () => {
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

      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleData);
      expect(validationMessage).to.contains(boCartRulesCreatePage.successfulCreationMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await boCartRulesCreatePage.viewMyShop(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });
  });

  describe('FO : Check the created cart rule', async () => {
    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle).to.eq(foHummingbirdLoginPage.pageTitle);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
    });

    it(`should search for the product '${dataProducts.demo_6.name}' and go to product page`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_6.name);
      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_6.name);
    });

    it('should add the product to cart and continue to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdProductPage.addProductToTheCart(page, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should check that discount is automatically applied to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountApplied', baseContext);

      const subTotal = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotal).to.eq('Free');

      const priceATI = await foHummingbirdCartPage.getATIPrice(page);
      expect(priceATI).to.eq(dataProducts.demo_6.combinations[0].price);
    });

    it('should proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      const isStepAddressComplete = await foHummingbirdCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);

      const allCarriersPrices = await foHummingbirdCheckoutPage.getAllCarriersPrices(page);
      expect(allCarriersPrices).to.deep.equals(['Free', `€${dataCarriers.myCarrier.priceTTC.toFixed(2)} tax incl.`]);
    });

    it(`should choose a carrier "${dataCarriers.myCarrier.name}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseCarrer', baseContext);

      await foHummingbirdCheckoutPage.chooseShippingMethod(page, dataCarriers.myCarrier.id);

      const shippingCost = await foHummingbirdCheckoutPage.getShippingCost(page);
      expect(shippingCost).to.equal(`€${dataCarriers.myCarrier.priceTTC.toFixed(2)}`);
    });

    it('should add the voucher', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addVoucher', baseContext);

      await foHummingbirdCheckoutPage.addPromoCode(page, cartRuleData.code);

      const shippingCost = await foHummingbirdCheckoutPage.getShippingCost(page);
      expect(shippingCost).to.equal('Free');

      const discountCost = await foHummingbirdCheckoutPage.getDiscountCost(page);
      expect(discountCost).to.equal(`- €${dataCarriers.myCarrier.priceTTC.toFixed(2)}`);

      const cartRuleName = await foHummingbirdCheckoutPage.getCartRuleName(page);
      expect(cartRuleName).to.contains(cartRuleData.name);

      const cartRuleValue = await foHummingbirdCheckoutPage.getCartRuleValue(page);
      expect(cartRuleValue).to.contains('Free shipping');
    });

    it('should reload page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'reloadPage', baseContext);

      await foHummingbirdCheckoutPage.reloadPage(page);

      const allCarriersPrices = await foHummingbirdCheckoutPage.getAllCarriersPrices(page);
      expect(allCarriersPrices).to.deep.equals(['Free', 'Free']);
    });
  });

  // Post-condition: Delete the created cart rule
  deleteCartRuleTest(cartRuleData.name, `${baseContext}_postTest_0`);
});
