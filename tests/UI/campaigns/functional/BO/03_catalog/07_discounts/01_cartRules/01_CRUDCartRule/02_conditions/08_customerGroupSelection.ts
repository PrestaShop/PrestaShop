// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import cartRulesPage from '@pages/BO/catalog/discounts';
import addCartRulePage from '@pages/BO/catalog/discounts/add';
// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';

import {
  boDashboardPage,
  dataCustomers,
  dataProducts,
  FakerCartRule,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_conditions_customerGroupSelection';

/*
Scenario:
- Create cart rule with customer group selection (Remove customer group)
- Go to FO > Add product to the cart > login by default customer
- Add the discount and check the error message
- Sign out and try to add the discount a second time
- Check that the promo code is applied to the cart
Post-condition:
- Delete the created cart rule
 */
describe('BO - Catalog - Cart rules : Customer Group selection', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const cartRuleCode: FakerCartRule = new FakerCartRule({
    name: 'New Cart rule customer group selection',
    code: '4QABV6L3',
    customerGroupSelection: true,
    discountType: 'Amount',
    discountAmount: {
      value: 100,
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

  describe('B0 : Create new cart rule', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
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

      const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleCode);
      expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await addCartRulePage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });
  });

  describe('FO : Check the created cart rule', async () => {
    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicLoginPage.pageTitle);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enterValidCredentials', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage', baseContext);

      await foClassicHomePage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicProductPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should add the promo code and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode', baseContext);

      await cartPage.addPromoCode(page, cartRuleCode.code);

      const alertMessage = await cartPage.getCartRuleErrorMessage(page);
      expect(alertMessage).to.equal(cartPage.cartRuleAlertMessageText);
    });

    it('should logout by the link in the header', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFOByHeaderLink', baseContext);

      await foClassicHomePage.logout(page);

      const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLogoLink', baseContext);

      await foClassicHomePage.clickOnHeaderLink(page, 'Logo');

      const pageTitle = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicHomePage.pageTitle);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage2', baseContext);

      await foClassicHomePage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await foClassicProductPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should add the promo code and verify the total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode2', baseContext);

      await cartPage.addPromoCode(page, cartRuleCode.code);

      const totalAfterDiscount = await cartPage.getATIPrice(page);
      expect(totalAfterDiscount).to.equal(0);
    });

    it('should go to Home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foClassicHomePage.clickOnHeaderLink(page, 'Logo');

      const pageTitle = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicHomePage.pageTitle);
    });

    it('should go to cart page and delete the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      await foClassicHomePage.goToCartPage(page);

      await cartPage.deleteProduct(page, 1);

      const notificationNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.be.equal(0);
    });
  });

  // Post-condition : Delete created cart rule
  deleteCartRuleTest(cartRuleCode.name, `${baseContext}_postTest`);
});
