import testContext from '@utils/testContext';
import {expect} from 'chai';

import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

import {
  boCartRulesPage,
  boCartRulesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
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
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_actions_sendFreeGift';

describe('BO - Cart rules - Actions : Send a free gift', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const cartRuleData: FakerCartRule = new FakerCartRule({
    name: 'Cart Rule Discount Send a free gift',
    description: 'It is a test for cart rules',
    code: 'CRDSendFreeGift',
    freeGift: true,
    freeGiftProduct: dataProducts.demo_20,
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Send a free gift', async () => {
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

    it(`should search for the product '${dataProducts.demo_1.name}' and go to product page`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_1.name);
      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_1.name);
    });

    it('should add the product to cart and continue to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdProductPage.addProductToTheCart(page, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should check that discount is applied to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountApplied', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, cartRuleData.code);

      const isProductGift = await foHummingbirdCartPage.isProductGift(page, 2);
      expect(isProductGift).to.equals(true);

      const subTotalProducts = await foHummingbirdCartPage.getSubtotalProductsValue(page);
      expect(subTotalProducts).to.eq(dataProducts.demo_1.finalPrice + dataProducts.demo_20.finalPrice);

      const subTotalDiscount = await foHummingbirdCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toFixed(2)).to.eq(`-${dataProducts.demo_20.finalPrice.toFixed(2)}`);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.eq('Free');

      const priceATI = await foHummingbirdCartPage.getATIPrice(page);
      expect(priceATI.toFixed(2)).to.eq(dataProducts.demo_1.finalPrice.toFixed(2));

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page);
      expect(cartRuleName).to.contains(cartRuleData.name);

      const cartRuleValue = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(cartRuleValue.toString()).to.contains(`-€${dataProducts.demo_20.finalPrice.toFixed(2)}`);
    });

    it(`should search for the product '${dataProducts.demo_20.name}' and go to product page`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage2', baseContext);

      await foHummingbirdCartPage.searchProduct(page, dataProducts.demo_20.name);
      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_20.name);
    });

    it('should add the product to cart and continue to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await foHummingbirdProductPage.addProductToTheCart(page, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should check that discount is applied to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountApplied2', baseContext);

      const isProductGift = await foHummingbirdCartPage.isProductGift(page, 2);
      expect(isProductGift).to.equals(true);

      const subTotalProducts = await foHummingbirdCartPage.getSubtotalProductsValue(page);
      expect(subTotalProducts.toFixed(2)).to.eq(
        (dataProducts.demo_1.finalPrice + dataProducts.demo_20.finalPrice * 2).toFixed(2),
      );

      const subTotalDiscount = await foHummingbirdCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toFixed(2)).to.eq(`-${dataProducts.demo_20.finalPrice.toFixed(2)}`);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.eq('Free');

      const priceATI = await foHummingbirdCartPage.getATIPrice(page);
      expect(priceATI.toFixed(2)).to.eq(
        (dataProducts.demo_1.finalPrice + dataProducts.demo_20.finalPrice).toFixed(2),
      );

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page);
      expect(cartRuleName).to.contains(cartRuleData.name);

      const cartRuleValue = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(cartRuleValue.toString()).to.contains(`-€${dataProducts.demo_20.finalPrice.toFixed(2)}`);
    });
  });

  // Post-condition: Delete the created cart rule
  deleteCartRuleTest(cartRuleData.name, `${baseContext}_postTest_0`);
});
