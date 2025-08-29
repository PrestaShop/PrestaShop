import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCartRulesPage,
  boCartRulesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataProducts,
  FakerCartRule,
  foClassicCartPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_information_priority';

/*
Scenario:
- Create cart rule with priority 1
- Create cart rule with priority 2
- Go to FO, add product to cart and proceed to checkout
- In cart page check the 2 cart rules
- Bulk delete the created cart rules
 */
describe('BO - Catalog - Cart rules : CRUD cart rule with priority', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const pastDate: string = utilsDate.getDateFormat('yyyy-mm-dd', 'past');
  const futureDate: string = utilsDate.getDateFormat('yyyy-mm-dd', 'future');
  const cartRulePriority2: FakerCartRule = new FakerCartRule({
    name: 'cartRulePriority2',
    priority: 2,
    status: true,
    dateFrom: pastDate,
    dateTo: futureDate,
    discountType: 'Amount',
    discountAmount: {
      value: 2,
      currency: 'EUR',
      tax: 'Tax included',
    },
  });
  const cartRulePriority1: FakerCartRule = new FakerCartRule({
    name: 'cartRulePriority1',
    priority: 1,
    status: true,
    dateFrom: pastDate,
    dateTo: futureDate,
    discountType: 'Amount',
    discountAmount: {
      value: 2,
      currency: 'EUR',
      tax: 'Tax included',
    },
  });

  // before and after functions
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

  describe('Create 2 cart rules with priority 1 and 2', async () => {
    describe(`Create first cart rule '${cartRulePriority2.name}'`, async () => {
      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

        await boCartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
      });

      it('should create cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

        const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRulePriority2);
        expect(validationMessage).to.contains(boCartRulesCreatePage.successfulCreationMessage);
      });
    });

    describe(`Create second cart rule '${cartRulePriority1.name}'`, async () => {
      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage2', baseContext);

        await boCartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
      });

      it('should create cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCartRule2', baseContext);

        const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRulePriority1);
        expect(validationMessage).to.contains(boCartRulesCreatePage.successfulCreationMessage);
      });
    });
  });

  describe('Verify discount on FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

      // View my shop and init pages
      page = await boCartRulesCreatePage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage1', baseContext);

      await foClassicHomePage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
    });

    it('should add product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

      await foClassicProductPage.addProductToTheCart(page);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should check that the cart rule priority 1 is applied before priority 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartRule', baseContext);

      const firstCartRule = await foClassicCartPage.getCartRuleName(page, 1);
      expect(firstCartRule).to.equal(cartRulePriority1.name);

      const secondCartRule = await foClassicCartPage.getCartRuleName(page, 2);
      expect(secondCartRule).to.equal(cartRulePriority2.name);
    });

    it('should check the total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalAfterDiscount', baseContext);

      const totalAfterDiscount = dataProducts.demo_1.finalPrice
        - (
          parseFloat(cartRulePriority2.discountAmount!.value.toString())
          + parseFloat(cartRulePriority1.discountAmount!.value.toString())
        );

      const priceATI = await foClassicCartPage.getATIPrice(page);
      expect(priceATI).to.equal(parseFloat(totalAfterDiscount.toFixed(2)));
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue', baseContext);

      const discount1: number = parseFloat(cartRulePriority1.discountAmount!.value.toString());
      const discount2: number = parseFloat(cartRulePriority2.discountAmount!.value.toString());
      const totalDiscountValue = await foClassicCartPage.getSubtotalDiscountValue(page);
      expect(totalDiscountValue)
        .to.equal(-(discount2 + discount1));

      const firstDiscountValue = await foClassicCartPage.getCartRuleValue(page, 1);
      expect(firstDiscountValue).to.equal(`-€${discount1.toFixed(2)}`);

      const secondDiscountValue = await foClassicCartPage.getCartRuleValue(page, 1);
      expect(secondDiscountValue).to.equal(`-€${discount2.toFixed(2)}`);
    });

    it('should remove product from shopping cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeProduct1', baseContext);

      await foClassicCartPage.deleteProduct(page, 1);

      const notificationNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.be.equal(0);
    });
  });

  describe('Delete the created cart rules by bulk actions', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

      // Close tab and init other page objects with new current tab
      page = await foClassicHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boCartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
    });

    it('should bulk delete cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCartRules', baseContext);

      const deleteTextResult = await boCartRulesPage.bulkDeleteCartRules(page);
      expect(deleteTextResult).to.be.contains(boCartRulesPage.successfulMultiDeleteMessage);
    });
  });
});
