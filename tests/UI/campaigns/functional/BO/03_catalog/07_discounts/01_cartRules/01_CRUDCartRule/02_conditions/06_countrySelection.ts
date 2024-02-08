// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import cartRulesPage from '@pages/BO/catalog/discounts';
import addCartRulePage from '@pages/BO/catalog/discounts/add';
import dashboardPage from '@pages/BO/dashboard';
import zonesPage from '@pages/BO/international/locations';
import countriesPage from '@pages/BO/international/locations/countries';
// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {homePage as foHomePage} from '@pages/FO/classic/home';
import {productPage as foProductPage} from '@pages/FO/classic/product';

// Import data
import Countries from '@data/demo/countries';
import Customers from '@data/demo/customers';
import Products from '@data/demo/products';
import Carriers from '@data/demo/carriers';
import CartRuleData from '@data/faker/cartRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_conditions_countrySelection';

/*
Pre-condition:
- Enable the country US
Scenario:
- Create cart rule with restricted country US
- Go to FO, Add product to cart
- Try to add promo code and check error message
- Proceed to checkout and login with default customer
- Choose the US address and continue
- Add promo code and check the total after discount
- Delete product from the cart
Post-condition:
- Delete the created cart rule
 */
describe('BO - Catalog - Cart rules : Country selection', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const cartRule: CartRuleData = new CartRuleData({
    name: 'Cart rule country selection',
    code: '4QABV6L3',
    countrySelection: true,
    countryIDToRemove: Countries.france.id,
    discountType: 'Amount',
    discountAmount: {
      value: 15,
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

  describe(`BO : Enable the country '${Countries.unitedStates.name}'`, async () => {
    it('should go to \'International > Locations\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocationsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.internationalParentLink,
        dashboardPage.locationsLink,
      );
      await zonesPage.closeSfToolBar(page);

      const pageTitle = await zonesPage.getPageTitle(page);
      expect(pageTitle).to.contains(zonesPage.pageTitle);
    });

    it('should go to \'Countries\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

      await zonesPage.goToSubTabCountries(page);

      const pageTitle = await countriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(countriesPage.pageTitle);
    });

    it('should reset all filters and get number of countries in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      const numberOfCountries = await countriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCountries).to.be.above(0);
    });

    it(`should search for the country '${Countries.unitedStates.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToEnable', baseContext);

      await countriesPage.filterTable(page, 'input', 'b!name', Countries.unitedStates.name);

      const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCountriesAfterFilter).to.be.equal(1);

      const textColumn = await countriesPage.getTextColumnFromTable(page, 1, 'b!name');
      expect(textColumn).to.equal(Countries.unitedStates.name);
    });

    it('should enable the country', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'verifyTheCountryStatus', baseContext);

      await countriesPage.setCountryStatus(page, 1, true);

      const currentStatus = await countriesPage.getCountryStatus(page, 1);
      expect(currentStatus).to.eq(true);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterCountry', baseContext);

      await countriesPage.resetFilter(page);

      const numberOfCountriesAfterReset = await countriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCountriesAfterReset).to.be.at.least(1);
    });
  });

  describe('BO : Create new cart rule with country restriction', async () => {
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

      const validationMessage = await addCartRulePage.createEditCartRules(page, cartRule);
      expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
    });
  });

  describe('FO : Check the created cart rule', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

      page = await addCartRulePage.viewMyShop(page);
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to the third product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage', baseContext);

      await foHomePage.goToProductPage(page, 3);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(Products.demo_6.name.toUpperCase());
    });

    it('should add product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foProductPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should set the promo code and verify the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode1', baseContext);

      await cartPage.addPromoCode(page, cartRule.code);

      const chooseDeliveryAddressNotification = await cartPage.getAlertWarningForPromoCode(page);
      expect(chooseDeliveryAddressNotification).to.equal(cartPage.alertChooseDeliveryAddressWarningText);
    });

    it('should proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckoutAndSignIn', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      const isCheckout = await checkoutPage.isCheckoutPage(page);
      expect(isCheckout).to.eq(true);
    });

    it('should sign in by the default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await checkoutPage.clickOnSignIn(page);

      const isCustomerConnected = await checkoutPage.customerLogin(page, Customers.johnDoe);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should choose the delivery address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseAndConfirmAddressStep', baseContext);

      await checkoutPage.chooseDeliveryAddress(page, 2);

      const isDeliveryStep = await checkoutPage.goToDeliveryStep(page);
      expect(isDeliveryStep, 'Delivery Step block is not displayed').to.eq(true);
    });

    it('should set the promo code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode2', baseContext);

      await checkoutPage.addPromoCode(page, cartRule.code);

      const cartRuleName = await checkoutPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.equal(cartRule.name);
    });

    it('should check the total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount', baseContext);

      const totalAfterDiscount = await checkoutPage.getATIPrice(page);
      expect(totalAfterDiscount).to.eq(Products.demo_6.price - cartRule.discountAmount!.value + Carriers.myCarrier.price);
    });

    it('should remove the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeTheDiscount', baseContext);

      const isDeleteIconNotVisible = await checkoutPage.removePromoCode(page);
      expect(isDeleteIconNotVisible, 'The discount is not removed').to.eq(true);
    });

    it('should go to Home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLogoLink', baseContext);

      await foHomePage.clickOnHeaderLink(page, 'Logo');

      const pageTitle = await foHomePage.getPageTitle(page);
      expect(pageTitle).to.equal(foHomePage.pageTitle);
    });

    it('should go to cart page and remove product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeProduct1', baseContext);

      await foHomePage.goToCartPage(page);
      await cartPage.deleteProduct(page, 1);

      const notificationNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.be.equal(0);
    });
  });

  // Post-condition : Delete the created cart rule
  deleteCartRuleTest(cartRule.name, `${baseContext}_postTest`);
});
