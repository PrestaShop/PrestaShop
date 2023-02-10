// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import cartRulesPage from '@pages/BO/catalog/discounts';
import addCartRulePage from '@pages/BO/catalog/discounts/add';
import dashboardPage from '@pages/BO/dashboard';
// Import FO pages
import cartPage from '@pages/FO/cart';
import {homePage as foHomePage} from '@pages/FO/home';
import foProductPage from '@pages/FO/product';

// Import data
import Products from '@data/demo/products';
import CartRuleData from '@data/faker/cartRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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

  const pastDate: string = date.getDateFormat('yyyy-mm-dd', 'past');
  const futureDate: string = date.getDateFormat('yyyy-mm-dd', 'future');
  const cartRulePriority2: CartRuleData = new CartRuleData({
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
  const cartRulePriority1: CartRuleData = new CartRuleData({
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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

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

  describe('Create 2 cart rules with priority 1 and 2', async () => {
    describe(`Create first cart rule '${cartRulePriority2.name}'`, async () => {
      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

        await cartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await addCartRulePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
      });

      it('should create cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

        const validationMessage = await addCartRulePage.createEditCartRules(page, cartRulePriority2);
        await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
      });
    });

    describe(`Create second cart rule '${cartRulePriority1.name}'`, async () => {
      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage2', baseContext);

        await cartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await addCartRulePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
      });

      it('should create cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCartRule2', baseContext);

        const validationMessage = await addCartRulePage.createEditCartRules(page, cartRulePriority1);
        await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
      });
    });
  });

  describe('Verify discount on FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

      // View my shop and init pages
      page = await addCartRulePage.viewMyShop(page);
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage1', baseContext);

      await foHomePage.goToProductPage(page, 1);

      const pageTitle = await foProductPage.getPageTitle(page);
      await expect(pageTitle.toUpperCase()).to.contains(Products.demo_1.name.toUpperCase());
    });

    it('should add product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

      await foProductPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(1);
    });

    it('should check that the cart rule priority 1 is applied before priority 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartRule', baseContext);

      const firstCartRule = await cartPage.getCartRuleName(page, 1);
      await expect(firstCartRule).to.equal(cartRulePriority1.name);

      const secondCartRule = await cartPage.getCartRuleName(page, 2);
      await expect(secondCartRule).to.equal(cartRulePriority2.name);
    });

    it('should check the total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalAfterDiscount', baseContext);

      const totalAfterDiscount = Products.demo_1.finalPrice
        - (cartRulePriority2.discountAmount!.value + cartRulePriority1.discountAmount!.value);

      const priceATI = await cartPage.getATIPrice(page);
      await expect(priceATI).to.equal(parseFloat(totalAfterDiscount.toFixed(2)));
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue', baseContext);

      const totalDiscountValue = await cartPage.getSubtotalDiscountValue(page);
      await expect(totalDiscountValue)
        .to.equal(-(cartRulePriority2.discountAmount!.value + cartRulePriority1.discountAmount!.value));

      const firstDiscountValue = await cartPage.getDiscountValue(page, 1);
      await expect(firstDiscountValue).to.equal(-(cartRulePriority1.discountAmount!.value));

      const secondDiscountValue = await cartPage.getDiscountValue(page, 1);
      await expect(secondDiscountValue).to.equal(-(cartRulePriority2.discountAmount!.value));
    });

    it('should remove product from shopping cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeProduct1', baseContext);

      await cartPage.deleteProduct(page, 1);

      const notificationNumber = await cartPage.getCartNotificationsNumber(page);
      await expect(notificationNumber).to.be.equal(0);
    });
  });

  describe('Delete the created cart rules by bulk actions', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

      // Close tab and init other page objects with new current tab
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await cartRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    it('should bulk delete cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCartRules', baseContext);

      const deleteTextResult = await cartRulesPage.bulkDeleteCartRules(page);
      await expect(deleteTextResult).to.be.contains(cartRulesPage.successfulMultiDeleteMessage);
    });
  });
});
