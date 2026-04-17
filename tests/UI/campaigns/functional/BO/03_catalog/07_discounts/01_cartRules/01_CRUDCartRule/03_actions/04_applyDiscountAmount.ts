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
  utilsCore,
  dataCurrencies,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_actions_applyDiscountAmount';

describe('BO - Cart rules - Actions : Apply a discount Amount', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const cartRuleData: FakerCartRule = new FakerCartRule({
    name: 'Cart Rule Discount amount',
    description: 'It is a test for cart rules',
    code: 'CRDAmount',
    discountType: 'Amount',
    discountAmount: {
      value: 20,
      currency: dataCurrencies.euro.isoCode,
      tax: 'Tax included',
    },
    applyDiscountTo: 'Order',
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Create cart rule with Discount Percent : 20%', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountApplied1', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, cartRuleData.code);

      const discount: number = cartRuleData.getDiscountAmount();

      const subTotalDiscount = await foHummingbirdCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toFixed(2)).to.eq(`-${discount.toFixed(2)}`);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.eq('Free');

      const priceATI = await foHummingbirdCartPage.getATIPrice(page);
      expect(priceATI.toFixed(2)).to.eq((dataProducts.demo_6.combinations[0].price - discount).toFixed(2));

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page);
      expect(cartRuleName).to.contains(cartRuleData.name);

      const cartRuleValue = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(cartRuleValue).to.contains(`-€${discount.toFixed(2)}`);
    });
  });

  describe('Update cart rule with Discount Amount Tax : Tax excluded', async () => {
    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCartRulePage1', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);
      await boCartRulesPage.goToEditCartRulePage(page, 1);

      const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesCreatePage.editPageTitle);
    });

    it('should update the discount amount tax to "Tax excluded"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCartRuleTaxExcluded', baseContext);

      cartRuleData.setDiscountAmountTax('Tax excluded');
      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleData);
      expect(validationMessage).to.contains(boCartRulesCreatePage.successfulUpdateMessage);
    });

    it('should check that discount is automatically applied to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountApplied2', baseContext);

      page = await boCartRulesCreatePage.changePage(browserContext, 1);
      await foHummingbirdCartPage.reloadPage(page);

      const discount: number = cartRuleData.getDiscountAmount()
        + utilsCore.percentage(cartRuleData.getDiscountAmount(), dataProducts.demo_6.tax);

      const subTotalDiscount = await foHummingbirdCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toFixed(2)).to.eq(`-${discount.toFixed(2)}`);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.eq('Free');

      const priceATI = await foHummingbirdCartPage.getATIPrice(page);
      expect(priceATI.toFixed(2)).to.eq((dataProducts.demo_6.combinations[0].price - discount).toFixed(2));

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page);
      expect(cartRuleName).to.contains(cartRuleData.name);

      const cartRuleValue = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(cartRuleValue).to.contains(`-€${discount.toFixed(2)}`);
    });
  });

  describe('Update cart rule with Discount Amount Value : 100', async () => {
    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCartRulePage2', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);
      await boCartRulesPage.goToEditCartRulePage(page, 1);

      const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesCreatePage.editPageTitle);
    });

    it('should update the discount amount value to "100"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCartRuleAmountValue100', baseContext);

      cartRuleData.setDiscountAmountValue(100);
      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleData);
      expect(validationMessage).to.contains(boCartRulesCreatePage.successfulUpdateMessage);
    });

    it('should check that discount is automatically applied to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountApplied3', baseContext);

      page = await boCartRulesCreatePage.changePage(browserContext, 1);
      await foHummingbirdCartPage.reloadPage(page);

      let discount: number = cartRuleData.getDiscountAmount()
        + utilsCore.percentage(cartRuleData.getDiscountAmount(), dataProducts.demo_6.tax);
      discount = discount < dataProducts.demo_6.combinations[0].price ? discount : dataProducts.demo_6.combinations[0].price;

      const subTotalDiscount = await foHummingbirdCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toFixed(2)).to.eq(`-${discount.toFixed(2)}`);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.eq('Free');

      const priceATI = await foHummingbirdCartPage.getATIPrice(page);
      expect(priceATI.toFixed(2)).to.eq((dataProducts.demo_6.combinations[0].price - discount).toFixed(2));

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page);
      expect(cartRuleName).to.contains(cartRuleData.name);

      const cartRuleValue = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(cartRuleValue).to.contains(`-€${discount.toFixed(2)}`);
    });
  });

  describe('Update cart rule with errors', async () => {
    it('should go to edit cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCartRulePageErrors', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);
      await boCartRulesPage.goToEditCartRulePage(page, 1);

      const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesCreatePage.editPageTitle);
    });

    it('should update the discount amount value to -50', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updatePercentTo-50', baseContext);

      cartRuleData.setDiscountAmountValue(-50);
      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleData);
      expect(validationMessage).to.contains(boCartRulesCreatePage.errorMessageReductionAmountGreatherThan);
    });

    it('should update the discount amount value to ab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updatePercentToab', baseContext);

      cartRuleData.setDiscountAmountValue('ab');
      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleData);
      expect(validationMessage).to.contains(boCartRulesCreatePage.errorMessageFieldInvalid('reduction_amount'));
    });

    it('should update the discount amount value to ù^$', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updatePercentToChars', baseContext);

      cartRuleData.setDiscountAmountValue('ù^$');
      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleData);
      expect(validationMessage).to.contains(boCartRulesCreatePage.errorMessageFieldInvalid('reduction_amount'));
    });
  });

  describe('Update cart rule with Discount Amount Value : 0', async () => {
    it('should update the discount amount value to "0"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCartRuleAmountValue0', baseContext);

      cartRuleData.setDiscountAmountValue(0);
      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleData);
      expect(validationMessage).to.contains(boCartRulesCreatePage.successfulUpdateMessage);
    });

    it('should check that discount is automatically applied to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountApplied4', baseContext);

      page = await boCartRulesCreatePage.changePage(browserContext, 1);
      await foHummingbirdCartPage.reloadPage(page);

      const hasSubtotalDiscount = await foHummingbirdCartPage.hasSubtotalDiscount(page);
      expect(hasSubtotalDiscount).to.equals(false);

      const subTotalShipping = await foHummingbirdCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.eq('Free');

      const priceATI = await foHummingbirdCartPage.getATIPrice(page);
      expect(priceATI.toFixed(2)).to.eq((dataProducts.demo_6.combinations[0].price).toFixed(2));

      const isCartRuleNameVisible = await foHummingbirdCartPage.isCartRuleNameVisible(page);
      expect(isCartRuleNameVisible).to.equals(true);
    });
  });

  // Post-condition: Delete the created cart rule
  deleteCartRuleTest(cartRuleData.name, `${baseContext}_postTest_0`);
});
