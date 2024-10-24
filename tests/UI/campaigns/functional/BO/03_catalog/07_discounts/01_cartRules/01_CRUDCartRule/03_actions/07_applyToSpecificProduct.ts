// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

// Import pages
// Import BO pages
import cartRulesPage from '@pages/BO/catalog/discounts';
import addCartRulePage from '@pages/BO/catalog/discounts/add';
// Import FO pages
import {blockCartModal} from '@pages/FO/classic/modal/blockCart';

import {
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  dataProducts,
  FakerCartRule,
  foClassicCartPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicModalQuickViewPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_actions_applyToSpecificProduct';

/*
Scenario:
- Create cart rule with apply discount to specific product
- Go to FO > Add 3 products to the cart (the specific product in the Cart rule + 2 other products)
- Add the discount and check the total after discount
- Remove one product from the cart and check the total after discount
- Remove the product restriction and check that the discount is deleted
- Remove the last product from the cart
Post-condition:
- Delete the created cart rule
 */
describe('BO - Catalog - Cart rules : Apply discount to specific product', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const newCartRuleData: FakerCartRule = new FakerCartRule({
    name: 'Discount specific product',
    code: '4QABV6L3',
    discountType: 'Percent',
    discountPercent: 20,
    applyDiscountTo: 'Specific product',
    product: dataProducts.demo_8.name,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('BO : Create cart rule', async () => {
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
      expect(pageTitle).to.eq(foClassicLoginPage.pageTitle);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
    });

    it(`should search for the product '${dataProducts.demo_8.name}' and go to product page`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foClassicHomePage.searchProduct(page, dataProducts.demo_8.name);
      await foClassicSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_8.name);
    });

    it('should add the product to cart and click on continue shopping', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicProductPage.addProductToTheCart(page, 1, undefined, false);
      await foClassicProductPage.goToHomePage(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should quick view the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewTheFirstProduct', baseContext);

      await foClassicHomePage.quickViewProduct(page, 1);

      const isQuickViewModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isQuickViewModalVisible).to.equal(true);
    });

    it('should add the first product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      await foClassicModalQuickViewPage.addToCartByQuickView(page);

      const isNotVisible = await blockCartModal.continueShopping(page);
      expect(isNotVisible).to.equal(true);
    });

    it('should quick view the second product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewTheSecondProduct', baseContext);

      await foClassicHomePage.quickViewProduct(page, 2);

      const isQuickViewModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isQuickViewModalVisible).to.equal(true);
    });

    it('should add the second product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addSecondProductToCart', baseContext);

      await foClassicModalQuickViewPage.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      // Check number of products in cart
      const notificationsNumber = await foClassicHomePage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.eq(3);
    });

    it('should add the promo code and check the cart rule name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode', baseContext);

      await foClassicCartPage.addPromoCode(page, newCartRuleData.code);

      const cartRuleName = await foClassicCartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.eq(newCartRuleData.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue1', baseContext);

      const discount = await utilsCore.percentage(dataProducts.demo_8.finalPrice, newCartRuleData.discountPercent!);

      const discountValue = await foClassicCartPage.getDiscountValue(page);
      expect(discountValue).to.eq(-discount.toFixed(2));
    });

    it('should delete the second product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSecondProductFromCart', baseContext);

      await foClassicCartPage.deleteProduct(page, 2);

      const notificationNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.eq(2);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue2', baseContext);

      const discount = await utilsCore.percentage(dataProducts.demo_8.finalPrice, newCartRuleData.discountPercent!);

      const discountValue = await foClassicCartPage.getDiscountValue(page);
      expect(discountValue).to.eq(-discount.toFixed(2));
    });

    it(`should delete the product '${dataProducts.demo_8.name}' from the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstProductFromCart', baseContext);

      await foClassicCartPage.deleteProduct(page, 1);

      const notificationNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.eq(1);
    });

    it('should check that no discount is applied to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoDiscount', baseContext);

      const isVisible = await foClassicCartPage.isCartRuleNameVisible(page);
      expect(isVisible).to.eq(false);
    });

    it('should delete the last product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLastProduct', baseContext);

      await foClassicCartPage.deleteProduct(page, 1);

      const notificationNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.eq(0);
    });
  });

  // Post-condition: Delete the created cart rule
  deleteCartRuleTest(newCartRuleData.name, `${baseContext}_postTest`);
});
