// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

// Import BO pages
import loginCommon from '@commonTests/BO/loginBO';
import dashboardPage from '@pages/BO/dashboard';
import cartRulesPage from '@pages/BO/catalog/discounts';
import addCartRulePage from '@pages/BO/catalog/discounts/add';

// Import FO pages
import {homePage} from '@pages/FO/home';
import {loginPage as foLoginPage} from '@pages/FO/login';
import {cartPage} from '@pages/FO/cart';

// Import data
import CartRuleData from '@data/faker/cartRule';
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_conditions_minimumAmount';

/*
Scenario:
- Create new cart rule with minimum amount
- Go to FO > Login by default customer
- Add product to cart and proceed to checkout
- Check that no discount is applied
- Add 2 products to cart
- Check that the discount is applied
Post-condition:
- Delete the created cart rule
 */
describe('BO - Catalog - Cart rules : Minimum amount', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const newCartRuleData: CartRuleData = new CartRuleData({
    name: 'Cart rule minimum amount',
    code: 'test',
    minimumAmount: {
      value: 50,
      currency: 'EUR',
      tax: 'Tax included',
      shipping: 'Shipping excluded',
    },
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

  describe('BO : Create new cart rule', async () => {
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

    it('should create new cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

      const validationMessage = await addCartRulePage.createEditCartRules(page, newCartRuleData);
      expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
    });
  });

  describe('FO : View discount', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should add the third product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOut', baseContext);

      await foLoginPage.goToHomePage(page);
      await homePage.addProductToCartByQuickView(page, 3);
      await homePage.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.eq(cartPage.pageTitle);
    });

    it('should add the promo code and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoDiscount', baseContext);

      await cartPage.addPromoCode(page, newCartRuleData.code);

      const errorMessage = await cartPage.getCartRuleErrorMessage(page);
      expect(errorMessage).to.eq(
        `${cartPage.minimumAmountErrorMessage} €${newCartRuleData.minimumAmount.value.toFixed(2)}.`);
    });

    it('should increase the quantity of product to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'increaseProductQuantity', baseContext);

      await cartPage.editProductQuantity(page, 1, 2);

      const totalBeforeDiscount = await cartPage.getATIPrice(page);
      expect(totalBeforeDiscount).to.eq(parseFloat((Products.demo_6.combinations[0].price * 2).toFixed(2)));
    });

    it('should add the promo code and check the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount', baseContext);

      await cartPage.addPromoCode(page, newCartRuleData.code);

      const discount = await basicHelper.percentage(Products.demo_6.combinations[0].price * 2, newCartRuleData.discountPercent!);

      const totalAfterDiscount = await cartPage.getATIPrice(page);
      expect(totalAfterDiscount).to.eq(parseFloat((Products.demo_6.combinations[0].price * 2 - discount).toFixed(2)));
    });

    it('should delete the last product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLastProduct', baseContext);

      await cartPage.deleteProduct(page, 1);

      const notificationNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.eq(0);
    });
  });

  // Post-condition: Delete the created cart rule
  deleteCartRuleTest(newCartRuleData.name, `${baseContext}_postTest`);
});
