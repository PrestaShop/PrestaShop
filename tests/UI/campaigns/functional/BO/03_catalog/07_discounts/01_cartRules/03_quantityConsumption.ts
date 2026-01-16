import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCartRulesPage,
  boCartRulesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  FakerCartRule,
  foHummingbirdCartPage,
  foHummingbirdCheckoutOrderConfirmationPage,
  foHummingbirdCheckoutPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdProductPage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';
import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_quantityConsumption';

describe('BO - Cart Rules : Quantity consumption', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCartRules: number = 0;

  const dateYesterday: string = utilsDate.getDateFormat('yyyy-mm-dd', 'yesterday');
  const dateTomorrow: string = utilsDate.getDateFormat('yyyy-mm-dd', 'tomorrow');
  const cartRuleData: FakerCartRule = new FakerCartRule({
    name: 'Test Amount',
    code: 'AMOUNT',
    dateFrom: dateYesterday,
    dateTo: dateTomorrow,
    quantity: 100,
    quantityPerUser: 100,
    discountType: 'Amount',
    discountAmount: {
      value: 5,
      currency: 'EUR',
      tax: 'Tax included',
    },
  });

  describe('BO - Cart Rules : Quantity consumption', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

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

    it('should reset and get number of cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

      numberOfCartRules = await boCartRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCartRules).to.be.at.least(0);
    });

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

      await boCartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
    });

    it('should create new cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleData);
      expect(validationMessage).to.contains(boCartRulesCreatePage.successfulCreationMessage);

      const numberOfCartRulesAfterCreation = await boCartRulesPage.getNumberOfElementInGrid(page);
      expect(numberOfCartRulesAfterCreation).to.be.at.most(numberOfCartRules + 1);
    });

    it('should check the quantity of cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkQuantityBeforeUse', baseContext);

      const colQuantity = await boCartRulesPage.getTextColumn(page, 1, 'quantity');
      expect(colQuantity).to.equal(cartRuleData.quantity.toString());
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      // View my shop and init pages
      page = await boCartRulesPage.viewMyShop(page);
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
      expect(isCustomerConnected).to.eq(true);
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

      const subTotalProducts = await foHummingbirdCartPage.getSubtotalProductsValue(page);
      expect(subTotalProducts).to.eq(dataProducts.demo_6.combinations[0].price);

      const hasSubtotalDiscount = await foHummingbirdCartPage.hasSubtotalDiscount(page);
      expect(hasSubtotalDiscount).to.eq(false);

      const priceATI = await foHummingbirdCartPage.getATIPrice(page);
      expect(priceATI.toFixed(2)).to.eq(dataProducts.demo_6.combinations[0].price.toFixed(2));
    });

    it('should check that discount is applied to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountApplied', baseContext);

      await foHummingbirdCartPage.addPromoCode(page, cartRuleData.code);

      const subTotalProducts = await foHummingbirdCartPage.getSubtotalProductsValue(page);
      expect(subTotalProducts).to.eq(dataProducts.demo_6.combinations[0].price);

      const subTotalDiscount = await foHummingbirdCartPage.getSubtotalDiscountValue(page);
      expect(subTotalDiscount.toFixed(2)).to.eq(`-${parseFloat(cartRuleData.discountAmount!.value.toString()).toFixed(2)}`);

      const hasSubtotalDiscount = await foHummingbirdCartPage.hasSubtotalDiscount(page);
      expect(hasSubtotalDiscount).to.eq(true);

      const priceATI = await foHummingbirdCartPage.getATIPrice(page);
      expect(priceATI.toFixed(2)).to.eq(
        (
          dataProducts.demo_6.combinations[0].price - parseFloat(cartRuleData.discountAmount!.value.toString())
        ).toFixed(2),
      );

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page);
      expect(cartRuleName).to.contains(cartRuleData.name);

      const cartRuleValue = await foHummingbirdCartPage.getCartRuleValue(page);
      expect(cartRuleValue.toString()).to.contains(`-€${parseFloat(cartRuleData.discountAmount!.value.toString()).toFixed(2)}`);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foHummingbirdCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete).to.eq(true);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foHummingbirdCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete).to.eq(true);
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      // Payment step - Choose payment step
      await foHummingbirdCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await foHummingbirdCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foHummingbirdCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should check the total (tax incl.)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderTotalTaxInc', baseContext);

      const totalProducts = dataProducts.demo_6.combinations[0].price;

      const totalDiscounts = parseFloat(cartRuleData.discountAmount!.value.toString());

      const orderTotalTaxInc = await foHummingbirdCheckoutOrderConfirmationPage.getOrderTotal(page);
      expect(orderTotalTaxInc).to.equal(`€${(totalProducts - totalDiscounts).toFixed(2)}`);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBackBO', baseContext);

      page = await foHummingbirdCheckoutOrderConfirmationPage.changePage(browserContext, 0);

      const pageTitle = await boCartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
    });

    it('should check the quantity of cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkQuantityAfterUse', baseContext);

      await boCartRulesPage.reloadPage(page);

      const colQuantity = await boCartRulesPage.getTextColumn(page, 1, 'quantity');
      expect(colQuantity).to.equal((cartRuleData.quantity - 1).toString());
    });
  });

  // Post-Condition: delete cart rules
  deleteCartRuleTest(cartRuleData.name, `${baseContext}_postTest_0`);
});
