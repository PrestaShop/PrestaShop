require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import common tests
const {deleteCartRuleTest} = require('@commonTests/BO/catalog/createDeleteCartRule.js');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const addCartRulePage = require('@pages/BO/catalog/discounts/add');
const zonesPage = require('@pages/BO/international/locations');
const countriesPage = require('@pages/BO/international/locations/countries');

// Import FO pages
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');

// Import data
const CartRuleFaker = require('@data/faker/cartRule');
const {countries} = require('@data/demo/countries');
const {Products} = require('@data/demo/products');
const {DefaultCustomer} = require('@data/demo/customer');

// import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_countryRestriction';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

const cartRule = new CartRuleFaker(
  {
    name: 'addCartRuleName',
    code: '4QABV6L3',
    countrySelection: true,
    countryIDToRemove: countries.france.id,
    discountType: 'Amount',
    discountPercent: 100,
  },
);

describe('BO - Catalog - Cart rules : Case 10 - Country Restriction', async () => {
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

  describe(`Enable the country '${countries.unitedStates.name}'`, async () => {
    it('should go to \'International > Locations\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocationsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.internationalParentLink,
        dashboardPage.locationsLink,
      );

      await zonesPage.closeSfToolBar(page);

      const pageTitle = await zonesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(zonesPage.pageTitle);
    });

    it('should go to \'Countries\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

      await zonesPage.goToSubTabCountries(page);

      const pageTitle = await countriesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(countriesPage.pageTitle);
    });

    it('should reset all filters and get number of countries in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      const numberOfCountries = await countriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCountries).to.be.above(0);
    });

    it(`should search for the country '${countries.unitedStates.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToEnable', baseContext);

      await countriesPage.filterTable(page, 'input', 'b!name', countries.unitedStates.name);

      const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
      await expect(numberOfCountriesAfterFilter).to.be.equal(1);

      const textColumn = await countriesPage.getTextColumnFromTable(page, 1, 'b!name');
      await expect(textColumn).to.equal(countries.unitedStates.name);
    });

    it('should enable the country', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'verifyTheCountryStatus', baseContext);

      await countriesPage.setCountryStatus(page, 1, true);

      const currentStatus = await countriesPage.getCountryStatus(page, 1);
      await expect(currentStatus).to.be.true;
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterCountry', baseContext);

      await countriesPage.resetFilter(page);

      const numberOfCountriesAfterReset = await countriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCountriesAfterReset).to.be.at.least(1);
    });
  });


  describe('Create new cart rule with country restriction', async () => {
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

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

      await cartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await addCartRulePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
    });

    it('should create cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

      const validationMessage = await addCartRulePage.createEditCartRules(page, cartRule);
      await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
    });
  });

  describe('Check the created discount in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

      page = await addCartRulePage.viewMyShop(page);

      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'goToFirstProductPage',
        baseContext,
      );

      await foHomePage.goToProductPage(page, 1);

      const pageTitle = await foProductPage.getPageTitle(page);
      await expect(pageTitle.toUpperCase()).to.contains(Products.demo_1.name.toUpperCase());
    });

    it('should add product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'addProductToCart',
        baseContext,
      );

      await foProductPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(1);
    });

    it('should set the promo code and verify the error message', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'addPromoCode',
        baseContext,
      );

      await cartPage.addPromoCode(page, cartRule.code);

      const chooseDeliveryAddressNotification = await cartPage.getAlertWarningForPromoCode(page);
      await expect(chooseDeliveryAddressNotification).to.equal(cartPage.alertChooseDeliveryAddressWarningtext);
    });

    it('should proceed to checkout', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'proceedToCheckoutAndSignIn',
        baseContext,
      );

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      const isCheckout = await checkoutPage.isCheckoutPage(page);
      await expect(isCheckout).to.be.true;
    });

    it('should checkout by signIn', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await checkoutPage.clickOnSignIn(page);

      const isCustomerConnected = await checkoutPage.customerLogin(page, DefaultCustomer);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should confirm address after signIn', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'chooseAndConfirmAddressStep',
        baseContext,
      );

      await checkoutPage.chooseDeliveryAddress(page, 2);

      const isDeliveryStep = await checkoutPage.goToDeliveryStep(page);
      await expect(isDeliveryStep, 'Delivery Step block is not displayed').to.be.true;
    });

    it('should set the promo code for second time and check total after discount', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'addPromoCodeAndVerifyTotalAfterDiscount',
        baseContext,
      );

      await checkoutPage.addPromoCode(page, cartRule.code);

      const cartRuleName = await cartPage.getCartRuleName(page, 1);
      await expect(cartRuleName).to.equal(cartRule.name);
    });
  });


  describe('Delete the shopping cart', async () => {
    it('should remove the discount', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'removeTheDiscount',
        baseContext,
      );

      const isDeleteIconNotVisible = await checkoutPage.removePromoCode(page);
      await expect(isDeleteIconNotVisible, 'The discount is not removed').to.be.true;
    });

    it('should go to Home page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'checkLogoLink',
        baseContext,
      );

      await foHomePage.clickOnHeaderLink(page, 'Logo');

      const pageTitle = await foHomePage.getPageTitle(page);
      await expect(pageTitle).to.equal(foHomePage.pageTitle);
    });

    it('should click on the cart link', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'clickOnCartLink',
        baseContext,
      );

      await foHomePage.goToCartPage(page);
    });

    it('should remove product from shopping cart', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'removeProduct1',
        baseContext,
      );

      await cartPage.deleteProduct(page, 1);

      const notificationNumber = await cartPage.getCartNotificationsNumber(page);
      await expect(notificationNumber).to.be.equal(0);
    });
  });

  // post condition : delete cart rule
  deleteCartRuleTest(cartRule.name, `${baseContext}_postTest_1`);
});
