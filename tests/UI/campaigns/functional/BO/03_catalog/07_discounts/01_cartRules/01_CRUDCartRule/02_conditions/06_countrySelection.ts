import testContext from '@utils/testContext';
import {expect} from 'chai';

import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

import {
  boCartRulesPage,
  boCartRulesCreatePage,
  boCountriesPage,
  boDashboardPage,
  boLoginPage,
  boZonesPage,
  type BrowserContext,
  dataCarriers,
  dataCountries,
  dataCustomers,
  dataProducts,
  FakerCartRule,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const cartRule: FakerCartRule = new FakerCartRule({
    name: 'Cart rule country selection',
    code: '4QABV6L3',
    countrySelection: true,
    countryIDToRemove: dataCountries.france.id,
    discountType: 'Amount',
    discountAmount: {
      value: 15,
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

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  describe(`BO : Enable the country '${dataCountries.unitedStates.name}'`, async () => {
    it('should go to \'International > Locations\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocationsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.internationalParentLink,
        boDashboardPage.locationsLink,
      );
      await boZonesPage.closeSfToolBar(page);

      const pageTitle = await boZonesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boZonesPage.pageTitle);
    });

    it('should go to \'Countries\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

      await boZonesPage.goToSubTabCountries(page);

      const pageTitle = await boCountriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCountriesPage.pageTitle);
    });

    it('should reset all filters and get number of countries in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      const numberOfCountries = await boCountriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCountries).to.be.above(0);
    });

    it(`should search for the country '${dataCountries.unitedStates.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToEnable', baseContext);

      await boCountriesPage.filterTable(page, 'input', 'b!name', dataCountries.unitedStates.name);

      const numberOfCountriesAfterFilter = await boCountriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCountriesAfterFilter).to.be.equal(1);

      const textColumn = await boCountriesPage.getTextColumnFromTable(page, 1, 'b!name');
      expect(textColumn).to.equal(dataCountries.unitedStates.name);
    });

    it('should enable the country', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'verifyTheCountryStatus', baseContext);

      await boCountriesPage.setCountryStatus(page, 1, true);

      const currentStatus = await boCountriesPage.getCountryStatus(page, 1);
      expect(currentStatus).to.eq(true);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterCountry', baseContext);

      await boCountriesPage.resetFilter(page);

      const numberOfCountriesAfterReset = await boCountriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCountriesAfterReset).to.be.at.least(1);
    });
  });

  describe('BO : Create new cart rule with country restriction', async () => {
    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
      );

      const pageTitle = await boCartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
    });

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

      await boCartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
    });

    it('should create cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRule);
      expect(validationMessage).to.contains(boCartRulesCreatePage.successfulCreationMessage);
    });
  });

  describe('FO : Check the created cart rule', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

      page = await boCartRulesCreatePage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to the third product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage', baseContext);

      await foClassicHomePage.goToProductPage(page, 3);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_6.name.toUpperCase());
    });

    it('should add product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicProductPage.addProductToTheCart(page);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should set the promo code and verify the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode1', baseContext);

      await foClassicCartPage.addPromoCode(page, cartRule.code);

      const chooseDeliveryAddressNotification = await foClassicCartPage.getAlertWarningForPromoCode(page);
      expect(chooseDeliveryAddressNotification).to.equal(foClassicCartPage.alertChooseDeliveryAddressWarningText);
    });

    it('should proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckoutAndSignIn', baseContext);

      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);

      const isCheckout = await foClassicCheckoutPage.isCheckoutPage(page);
      expect(isCheckout).to.eq(true);
    });

    it('should sign in by the default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foClassicCheckoutPage.clickOnSignIn(page);

      const isCustomerConnected = await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should choose the delivery address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseAndConfirmAddressStep', baseContext);

      await foClassicCheckoutPage.chooseDeliveryAddress(page, 2);

      const isDeliveryStep = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isDeliveryStep, 'Delivery Step block is not displayed').to.eq(true);
    });

    it('should set the promo code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode2', baseContext);

      await foClassicCheckoutPage.addPromoCode(page, cartRule.code);

      const cartRuleName = await foClassicCheckoutPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.equal(cartRule.name);
    });

    it('should check the total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount', baseContext);

      const totalAfterDiscount = await foClassicCheckoutPage.getATIPrice(page);
      expect(totalAfterDiscount).to.eq(
        dataProducts.demo_6.price - parseFloat(cartRule.discountAmount!.value.toString()) + dataCarriers.myCarrier.price,
      );
    });

    it('should remove the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeTheDiscount', baseContext);

      const isDeleteIconNotVisible = await foClassicCheckoutPage.removePromoCode(page);
      expect(isDeleteIconNotVisible, 'The discount is not removed').to.eq(true);
    });

    it('should go to Home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLogoLink', baseContext);

      await foClassicHomePage.clickOnHeaderLink(page, 'Logo');

      const pageTitle = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicHomePage.pageTitle);
    });

    it('should go to cart page and remove product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeProduct1', baseContext);

      await foClassicHomePage.goToCartPage(page);
      await foClassicCartPage.deleteProduct(page, 1);

      const notificationNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.be.equal(0);
    });
  });

  // Post-condition : Delete the created cart rule
  deleteCartRuleTest(cartRule.name, `${baseContext}_postTest`);
});
