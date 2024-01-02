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
import {homePage} from '@pages/FO/home';
import {loginPage as foLoginPage} from '@pages/FO/login';
import {cartPage} from '@pages/FO/cart';

// Import data
import CartRuleData from '@data/faker/cartRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_conditions_invalidDateTime';

/*
Scenario:
- Create new cart rule with Invalid date time
- Go to FO > Login by default customer
- Add product to cart and proceed to checkout
- Check that no discount is applied
Post-condition:
- Delete the created cart rule
 */
describe('BO - Catalog - Cart rules : Invalid date time', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create a date format
  const pastDate: string = date.getDateFormat('yyyy-mm-dd', 'past');
  const newCartRuleData: CartRuleData = new CartRuleData({
    name: 'Cart rule invalid date time',
    discountType: 'Percent',
    discountPercent: 20,
    dateFrom: pastDate,
    dateTo: pastDate,
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

    it('should add the first product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOut', baseContext);

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
