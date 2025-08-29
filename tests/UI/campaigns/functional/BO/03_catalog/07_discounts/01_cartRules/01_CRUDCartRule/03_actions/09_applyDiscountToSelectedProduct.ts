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
  foClassicCartPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
  utilsCore,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_actions_applyDiscountToSelectedProduct';

describe('BO - Cart rules - Actions : Apply a discount to Selected product(s)', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const cartRuleData: FakerCartRule = new FakerCartRule({
    name: 'Cart Rule Discount Selected Product',
    description: 'It is a test for cart rules',
    code: 'CRDSelectedProduct',
    discountType: 'Percent',
    discountPercent: 20,
    freeShipping: true,
    applyDiscountTo: 'Selected products',
    productSelection: true,
    productSelectionNumber: 1,
    productRestriction: [
      {
        quantity: 1,
        ruleType: 'Products',
        values: [
          dataProducts.demo_19,
          dataProducts.demo_16,
          dataProducts.demo_9,
        ],
      },
    ],
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Apply a discount to Selected product(s)', async () => {
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
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.eq(foClassicLoginPage.pageTitle);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
    });

    it(`should search for the product '${dataProducts.demo_16.name}' and go to product page`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foClassicHomePage.searchProduct(page, dataProducts.demo_16.name);
      await foClassicSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_16.name);
    });

    it('should add the product to cart and continue to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicProductPage.addProductToTheCart(page, 1);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
    });

    it('should check that discount is automatically applied to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountApplied', baseContext);

      await foClassicCartPage.addPromoCode(page, cartRuleData.code);

      const discount = utilsCore.percentage(dataProducts.demo_16.finalPrice, cartRuleData.getDiscountPercent());

      const subTotalProducts = await foClassicCartPage.getSubtotalProductsValue(page);
      expect(subTotalProducts).to.eq(dataProducts.demo_16.finalPrice);

      const subTotalDiscount = await foClassicCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toString()).to.eq(`-${discount.toFixed(2)}`);

      const subTotalShipping = await foClassicCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.eq('Free');

      const priceATI = await foClassicCartPage.getATIPrice(page);
      expect(priceATI.toString()).to.eq((dataProducts.demo_16.finalPrice - discount).toFixed(2));

      const cartRuleName = await foClassicCartPage.getCartRuleName(page);
      expect(cartRuleName).to.equal(cartRuleData.name);

      const cartRuleValue = await foClassicCartPage.getCartRuleValue(page);
      expect(cartRuleValue.toString()).to.eq(`-€${discount.toFixed(2)}`);
    });
  });

  describe('Update cart rule with errors', async () => {
    it('should go to edit cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCartRulePageErrors', baseContext);

      page = await foClassicCartPage.changePage(browserContext, 0);
      await boCartRulesPage.goToEditCartRulePage(page, 1);

      const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesCreatePage.editPageTitle);
    });

    it('should update the discount percent to -50%', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updatePercentTo-50', baseContext);

      cartRuleData.setDiscountPercent(-20);
      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleData);
      expect(validationMessage).to.contains(boCartRulesCreatePage.errorMessageReductionPercentageBetween);
      expect(validationMessage).to.contains(boCartRulesCreatePage.errorMessageFieldInvalid('reduction_percent'));
    });

    it('should update the discount percent to o"à%', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updatePercentToChars', baseContext);

      cartRuleData.setDiscountPercent('o"à');
      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleData);
      expect(validationMessage).to.contains(boCartRulesCreatePage.errorMessageFieldInvalid('reduction_percent'));
    });
  });

  describe('Add a non-selected product to cart', async () => {
    it('should update the discount amount value to "0"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCartRuleAmountValue0', baseContext);

      cartRuleData.setDiscountPercent(20);
      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleData);
      expect(validationMessage).to.contains(boCartRulesCreatePage.successfulUpdateMessage);
    });

    it(`should search for the product '${dataProducts.demo_3.name}' and go to product page`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage2', baseContext);

      page = await boCartRulesCreatePage.changePage(browserContext, 1);
      await foClassicHomePage.searchProduct(page, dataProducts.demo_3.name);
      await foClassicSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_3.name);
    });

    it('should add the product to cart and continue to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await foClassicProductPage.addProductToTheCart(page, 1);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
    });

    it('should check that discount is automatically applied to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountApplied1', baseContext);

      const discount = utilsCore.percentage(dataProducts.demo_16.finalPrice, cartRuleData.getDiscountPercent());

      const subTotalProducts = await foClassicCartPage.getSubtotalProductsValue(page);
      expect(subTotalProducts).to.eq(dataProducts.demo_16.finalPrice + dataProducts.demo_3.finalPrice);

      const subTotalDiscount = await foClassicCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toString()).to.eq(`-${discount.toFixed(2)}`);

      const subTotalShipping = await foClassicCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.eq('Free');

      const priceATI = await foClassicCartPage.getATIPrice(page);
      expect(priceATI.toFixed(2)).to.eq(
        (dataProducts.demo_16.finalPrice + dataProducts.demo_3.finalPrice - discount).toFixed(2),
      );

      const cartRuleName = await foClassicCartPage.getCartRuleName(page);
      expect(cartRuleName).to.equal(cartRuleData.name);

      const cartRuleValue = await foClassicCartPage.getCartRuleValue(page);
      expect(cartRuleValue.toString()).to.eq(`-€${discount.toFixed(2)}`);
    });
  });

  describe('Remove the selected product', async () => {
    it(`should delete the product '${dataProducts.demo_8.name}' from the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstProductFromCart', baseContext);

      await foClassicCartPage.deleteProduct(page, 1);

      const notificationNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.eq(1);
    });

    it('should check that discount is automatically applied to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountApplied2', baseContext);

      const subTotalProducts = await foClassicCartPage.getSubtotalProductsValue(page);
      expect(subTotalProducts).to.eq(dataProducts.demo_3.finalPrice);

      const hasSubtotalDiscount = await foClassicCartPage.hasSubtotalDiscount(page);
      expect(hasSubtotalDiscount).to.eq(false);

      const subTotalShipping = await foClassicCartPage.getSubtotalShippingValue(page);
      expect(subTotalShipping).to.eq('Free');

      const priceATI = await foClassicCartPage.getATIPrice(page);
      expect(priceATI.toFixed(2)).to.eq((dataProducts.demo_3.finalPrice).toFixed(2));
    });
  });

  // Post-condition: Delete the created cart rule
  deleteCartRuleTest(cartRuleData.name, `${baseContext}_postTest_0`);
});
