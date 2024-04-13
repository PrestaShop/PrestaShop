// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import basicHelper from '@utils/basicHelper';

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
import {homePage} from '@pages/FO/classic/home';
import {loginPage} from '@pages/FO/classic/login';
import {productPage} from '@pages/FO/classic/product';
import {searchResultsPage} from '@pages/FO/classic/searchResults';
import {quickViewModal} from '@pages/FO/classic/modal/quickView';
import {blockCartModal} from '@pages/FO/classic/modal/blockCart';

// Import data
import Products from '@data/demo/products';
import CartRuleData from '@data/faker/cartRule';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_conditions_productSelection';

/*
Scenario:
- Create cart rule with product restricted
- Go to FO > Add 3 products to the cart (the product selected in the Cart rule + 2 other products)
- Add the discount and check the total after discount
- Remove one product from the cart and check the total after discount
- Remove the product restriction and check that the discount is deleted
- Remove the last product from the cart
Post-condition:
- Delete the created cart rule
 */
describe('BO - Catalog - Cart rules : Product selection', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const newCartRuleData: CartRuleData = new CartRuleData({
    name: 'Discount product selection',
    code: '4QABV6L3',
    productSelection: true,
    productSelectionNumber: 1,
    productRestriction: [{quantity: 1, ruleType: 'Products', value: Products.demo_8.id}],
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

  describe('BO : Create cart rule', async () => {
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
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });
  });

  describe('FO : Check the created cart rule', async () => {
    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await loginPage.getPageTitle(page);
      expect(pageTitle).to.eq(loginPage.pageTitle);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await loginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await loginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
    });

    it(`should search for the product '${Products.demo_8.name}' and go to product page`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await homePage.searchProduct(page, Products.demo_8.name);
      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_8.name);
    });

    it('should add the product to cart and click on continue shopping', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await productPage.addProductToTheCart(page, 1, undefined, false);
      await productPage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should quick view the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewTheFirstProduct', baseContext);

      await homePage.quickViewProduct(page, 1);

      const isQuickViewModalVisible = await quickViewModal.isQuickViewProductModalVisible(page);
      expect(isQuickViewModalVisible).to.equal(true);
    });

    it('should add the product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      await quickViewModal.addToCartByQuickView(page);

      const isNotVisible = await blockCartModal.continueShopping(page);
      expect(isNotVisible).to.eq(true);
    });

    it('should add the second product to the cart by quick view', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addSecondProductToCart', baseContext);

      await homePage.quickViewProduct(page, 2);
      await quickViewModal.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      // Check number of products in cart
      const notificationsNumber = await homePage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.eq(3);
    });

    it('should add the promo code and check the cart rule name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode', baseContext);

      await cartPage.addPromoCode(page, newCartRuleData.code);

      const cartRuleName = await cartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.eq(newCartRuleData.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue1', baseContext);

      const total = Products.demo_8.finalPrice + Products.demo_1.finalPrice + Products.demo_3.finalPrice;

      const discount = await basicHelper.percentage(total, newCartRuleData.discountPercent!);

      const discountValue = await cartPage.getDiscountValue(page);
      expect(discountValue).to.eq(-discount.toFixed(2));
    });

    it('should delete the second product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSecondProductFromCart', baseContext);

      await cartPage.deleteProduct(page, 2);

      const notificationNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.eq(2);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue2', baseContext);

      const total = Products.demo_8.finalPrice + Products.demo_3.finalPrice;

      const discount = await basicHelper.percentage(total, newCartRuleData.discountPercent!);

      const discountValue = await cartPage.getDiscountValue(page);
      expect(discountValue).to.eq(-discount.toFixed(2));
    });

    it(`should delete the product '${Products.demo_8.name}' from the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstProductFromCart', baseContext);

      await cartPage.deleteProduct(page, 1);

      const notificationNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.eq(1);
    });

    it('should check that no discount is applied to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoDiscount', baseContext);

      const isVisible = await cartPage.isCartRuleNameVisible(page);
      expect(isVisible).to.eq(false);
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
