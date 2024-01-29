// Import utils
import date from '@utils/date';
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
import {homePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import foVouchersPage from '@pages/FO/classic/myAccount/vouchers';
import {cartPage} from '@pages/FO/classic/cart';

// Import data
import CartRuleData from '@data/faker/cartRule';
import Customers from '@data/demo/customers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_conditions_limitToSingleCustomer';

/*
Scenario:
- Create new cart rule with limit to single customer
- Go to FO > Login by default customer
- Go to Vouchers page and check the voucher
- Sign out
- Add product to cart and proceed to checkout
- Check that no discount is applied
Post-condition:
- Delete the created cart rule
 */
describe('BO - Catalog - Cart rules : Limit to single customer', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create a date format
  const pastDate: string = date.getDateFormat('yyyy-mm-dd', 'past');
  const futureDate: string = date.getDateFormat('yyyy-mm-dd', 'future');
  const expirationDate: string = date.getDateFormat('mm/dd/yyyy', 'future');
  const newCartRuleData: CartRuleData = new CartRuleData({
    name: 'Cart rule limit to single customer',
    customer: Customers.johnDoe,
    discountType: 'Percent',
    discountPercent: 20,
    dateFrom: pastDate,
    dateTo: futureDate,
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

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to vouchers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOVouchersPage', baseContext);

      await homePage.goToMyAccountPage(page);
      await myAccountPage.goToVouchersPage(page);

      const pageHeaderTitle = await foVouchersPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foVouchersPage.pageTitle);
    });

    [
      {args: {column: 'code', value: ''}},
      {args: {column: 'description', value: newCartRuleData.name}},
      {args: {column: 'quantity', value: '1'}},
      {args: {column: 'value', value: '20%'}},
      {args: {column: 'minimum', value: 'None'}},
      {args: {column: 'cumulative', value: 'Yes'}},
      {args: {column: 'expiration_date', value: expirationDate}},
    ].forEach((cartRule, index: number) => {
      it(`should check the voucher ${cartRule.args.column}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkVoucher${index}`, baseContext);

        const cartRuleTextColumn = await foVouchersPage.getTextColumnFromTableVouchers(page, 1, cartRule.args.column);
        expect(cartRuleTextColumn).to.equal(cartRule.args.value);
      });
    });

    it('should sign out', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOut', baseContext);

      await foVouchersPage.logout(page);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
    });

    it('should add the first product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      await foLoginPage.goToHomePage(page);
      await homePage.addProductToCartByQuickView(page, 1);
      await homePage.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.eq(cartPage.pageTitle);
    });

    it('should check that there is no discount applied', async function () {
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
