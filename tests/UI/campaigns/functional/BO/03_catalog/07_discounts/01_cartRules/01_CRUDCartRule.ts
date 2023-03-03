// Import utils
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
import foHomePage from '@pages/FO/home';
import foLoginPage from '@pages/FO/login';
import foProductPage from '@pages/FO/product';

// Import data
import Customers from '@data/demo/customers';
import Products from '@data/demo/products';
import CartRuleData from '@data/faker/cartRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule';

describe('BO - Catalog - Discounts : CRUD cart rule', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const newCartRuleData: CartRuleData = new CartRuleData({
    code: '4QABV6L3',
    customer: Customers.johnDoe,
    discountType: 'Percent',
    discountPercent: 20,
  });
  const editCartRuleData: CartRuleData = new CartRuleData({
    code: '3PAJA6B3',
    discountType: 'Percent',
    discountPercent: 30,
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

  describe('Create cart rule', async () => {
    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

      await cartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await addCartRulePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
    });

    it('should create new cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

      const validationMessage = await addCartRulePage.createEditCartRules(page, newCartRuleData);
      await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
    });
  });

  describe('Verify created cart rule in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShopToCheckCreatedDiscount', baseContext);

      // View my shop and init pages
      page = await addCartRulePage.viewMyShop(page);
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO_1', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO_1', baseContext);

      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage_1', baseContext);

      // Go to home page
      await foLoginPage.goToHomePage(page);
      await foHomePage.goToProductPage(page, 1);

      const pageTitle = await foProductPage.getPageTitle(page);
      await expect(pageTitle.toUpperCase()).to.contains(Products.demo_1.name.toUpperCase());
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart_1', baseContext);

      await foProductPage.addProductToTheCart(page);

      // getNumberFromText is used to get the notifications number in the cart
      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(1);
    });

    it('should verify the total before discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalBeforeDiscount_1', baseContext);

      const priceATI = await cartPage.getATIPrice(page);
      await expect(priceATI).to.equal(Products.demo_1.finalPrice);
    });

    it('should set the promo code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode_1', baseContext);

      await cartPage.addPromoCode(page, newCartRuleData.code);
    });

    it('should verify the total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalAfterDiscount_1', baseContext);

      const discountedPrice = Products.demo_1.finalPrice
        - ((Products.demo_1.finalPrice * newCartRuleData.discountPercent) / 100);

      const priceATI = await cartPage.getATIPrice(page);
      await expect(priceATI).to.equal(parseFloat(discountedPrice.toFixed(2)));
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighOutFO_1', baseContext);

      await cartPage.logout(page);

      const isCustomerConnected = await cartPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });

  describe('Update cart rule', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo_1', baseContext);

      // Close tab and init other page objects with new current tab
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await cartRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    it('should go to edit cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCartRulePage', baseContext);

      await cartRulesPage.goToEditCartRulePage(page, 1);

      const pageTitle = await addCartRulePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCartRulePage.editPageTitle);
    });

    it('should update cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCartRule', baseContext);

      const validationMessage = await addCartRulePage.createEditCartRules(page, editCartRuleData);
      await expect(validationMessage).to.contains(addCartRulePage.successfulUpdateMessage);
    });
  });

  describe('Verify updated cart rule in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShopToCheckEditedDiscount', baseContext);

      // View my shop and init pages
      page = await addCartRulePage.viewMyShop(page);
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO_2', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO_2', baseContext);

      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage_2', baseContext);

      // Go to home page
      await foLoginPage.goToHomePage(page);
      await foHomePage.goToProductPage(page, 1);

      const pageTitle = await foProductPage.getPageTitle(page);
      await expect(pageTitle.toUpperCase()).to.contains(Products.demo_1.name.toUpperCase());
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart_2', baseContext);

      await foProductPage.addProductToTheCart(page);

      // getNumberFromText is used to get the notifications number in the cart
      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(1);
    });

    it('should verify the total before discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalBeforeDiscount_2', baseContext);

      const priceATI = await cartPage.getATIPrice(page);
      await expect(priceATI).to.equal(Products.demo_1.finalPrice);
    });

    it('should set the promo code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode_2', baseContext);

      await cartPage.addPromoCode(page, editCartRuleData.code);
    });

    it('should verify the total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalAfterDiscount_2', baseContext);

      const discountedPrice = Products.demo_1.finalPrice
        - ((Products.demo_1.finalPrice * editCartRuleData.discountPercent) / 100);

      const priceATI = await cartPage.getATIPrice(page);
      await expect(priceATI).to.equal(parseFloat(discountedPrice.toFixed(2)));
    });
  });

  describe('Delete the created cart rule', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo_2', baseContext);

      // Close tab and init other page objects with new current tab
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await cartRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    it('should delete cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCartRule', baseContext);

      const validationMessage = await cartRulesPage.deleteCartRule(page);
      await expect(validationMessage).to.contains(cartRulesPage.successfulDeleteMessage);
    });
  });
});
