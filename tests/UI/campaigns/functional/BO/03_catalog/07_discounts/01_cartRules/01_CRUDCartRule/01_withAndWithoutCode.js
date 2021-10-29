require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const addCartRulePage = require('@pages/BO/catalog/discounts/add');

// Import FO pages
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');

// Import data
const CartRuleFaker = require('@data/faker/cartRule');
const ProductData = require('@data/FO/product');
const {Products} = require('@data/demo/products');

// import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_withAndWithoutCode';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

// Today date
const now = new Date();
// Day before
const today = (`0${now.getDate()}`).slice(-2);
// Current month
const month = (`0${now.getMonth() + 1}`).slice(-2);
// Current year
const year = now.getFullYear();
// Date yesterday format (yyyy-mm-dd)
const dateFrom = `${year}-${month}-${today} 01:00:00`;

const cartRuleWithoutCode = new CartRuleFaker(
  {
    dateFrom,
    name: 'withoutCode',
    discountType: 'Percent',
    discountPercent: 20,
  },
);

const cartRuleWithCode = new CartRuleFaker(
  {
    name: 'withCode',
    code: '4QABV6L3',
    discountType: 'Percent',
    discountPercent: 20,
  },
);

describe('BO - Catalog - Cart rules : CRUD cart rule with/without code', async () => {
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

  describe('case 1 : Create cart rule without code then check it on FO', async () => {
    describe('Create cart rule on BO', async () => {
      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

        await cartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await addCartRulePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
      });

      it('should create cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

        const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleWithoutCode);
        await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
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
        await expect(pageTitle.toUpperCase()).to.contains(ProductData.firstProductData.name);
      });

      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

        await foProductPage.addProductToTheCart(page);

        const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
        await expect(notificationsNumber).to.be.equal(1);
      });

      it('should verify the total after discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalAfterDiscount1', baseContext);

        const totalAfterDiscount = Products.demo_1.finalPrice
          - (Products.demo_1.finalPrice * cartRuleWithoutCode.discountPercent / 100);

        const priceATI = await cartPage.getATIPrice(page);
        await expect(priceATI).to.equal(parseFloat(totalAfterDiscount.toFixed(2)));

        const cartRuleName = await cartPage.getCartRuleName(page);
        await expect(cartRuleName).to.equal(cartRuleWithoutCode.name);

        const discountValue = await cartPage.getDiscountValue(page);
        await expect(discountValue).to.equal(parseFloat(totalAfterDiscount.toFixed(2)) - Products.demo_1.finalPrice);
      });

      it('should remove product from shopping cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'removeProduct1', baseContext);

        await cartPage.deleteProduct(page, 1);

        const notificationNumber = await cartPage.getCartNotificationsNumber(page);
        await expect(notificationNumber).to.be.equal(0);
      });
    });
  });

  describe('case 2 : Create cart rule with code then check it on FO', async () => {
    describe('Update cart rule on BO', async () => {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBoToUpdate', baseContext);

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

        const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleWithCode);
        await expect(validationMessage).to.contains(addCartRulePage.successfulUpdateMessage);
      });
    });

    describe('Verify discount on FO', async () => {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop2', baseContext);

        // View my shop and init pages
        page = await addCartRulePage.viewMyShop(page);

        await foHomePage.changeLanguage(page, 'en');
        const isHomePage = await foHomePage.isHomePage(page);
        await expect(isHomePage, 'Fail to open FO home page').to.be.true;
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage2', baseContext);

        await foHomePage.goToProductPage(page, 1);

        const pageTitle = await foProductPage.getPageTitle(page);
        await expect(pageTitle.toUpperCase()).to.contains(ProductData.firstProductData.name);
      });

      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

        await foProductPage.addProductToTheCart(page);

        const notificationNumber = await cartPage.getCartNotificationsNumber(page);
        await expect(notificationNumber).to.be.equal(1);
      });

      it('should verify the total before the discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkTotalBeforeDiscount', baseContext);

        const priceATI = await cartPage.getATIPrice(page);
        await expect(priceATI).to.equal(Products.demo_1.finalPrice);
      });

      it('should set the promo code', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode', baseContext);

        await cartPage.addPromoCode(page, cartRuleWithCode.code);
        const cartRuleName = await cartPage.getCartRuleName(page, 1);
        await expect(cartRuleName).to.equal(cartRuleWithCode.name);
      });

      it('should verify the total after the discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount2', baseContext);

        const totalAfterPromoCode = Products.demo_1.finalPrice
          - (Products.demo_1.finalPrice * cartRuleWithCode.discountPercent / 100);

        const priceATI = await cartPage.getATIPrice(page);
        await expect(priceATI).to.equal(parseFloat(totalAfterPromoCode.toFixed(2)));

        const discountValue = await cartPage.getDiscountValue(page, 1);
        await expect(discountValue).to.equal(parseFloat((totalAfterPromoCode - Products.demo_1.finalPrice).toFixed(2)));
      });

      it('should remove voucher and product from shopping cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'removeProductAndVoucher', baseContext);

        await cartPage.removeVoucher(page, 1);
        await cartPage.deleteProduct(page, 1);

        const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
        await expect(notificationsNumber).to.be.equal(0);
      });
    });
  });

  describe('Delete the updated cart rule', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

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
