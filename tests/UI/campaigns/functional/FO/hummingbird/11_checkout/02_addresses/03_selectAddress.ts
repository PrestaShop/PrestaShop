import {bulkDeleteAddressesTest, createAddressTest} from '@commonTests/BO/customers/address';
import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCountriesPage,
  boDashboardPage,
  boLoginPage,
  boPaymentPreferencesPage,
  boZonesPage,
  type BrowserContext,
  dataCountries,
  dataCustomers,
  dataModules,
  dataProducts,
  FakerAddress,
  FakerCountry,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_checkout_addresses_selectAddress';

/*
Scenario:
- Go to FO
- Add product to cart
- Go to checkout page
- Login as a customer
- Select the third address
- Check that no payment method is available
*/
describe('FO - Checkout - Addresses: Select address', async () => {
  const country: FakerCountry = dataCountries.germany;
  const address: FakerAddress = new FakerAddress({
    email: dataCustomers.johnDoe.email,
    country: country.name,
  });

  // Pre-condition: Create new address
  createAddressTest(address, `${baseContext}_preTest_0`);

  describe('FO - Checkout - Addresses: Select address', async () => {
    let browserContext: BrowserContext;
    let page: Page;

    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    describe('Enable Country', async () => {
      it('should login in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'enableCountry_loginBO', baseContext);

        await boLoginPage.goTo(page, global.BO.URL);
        await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to \'International > Locations\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'enableCountry_goToLocationsPage', baseContext);

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
        await testContext.addContextItem(this, 'testIdentifier', 'enableCountry_goToCountriesPage', baseContext);

        await boZonesPage.goToSubTabCountries(page);

        const pageTitle = await boCountriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCountriesPage.pageTitle);
      });

      it('should reset all filters and get number of countries in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'enableCountry_resetFilterFirst', baseContext);

        const numberOfCountries = await boCountriesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCountries).to.be.above(0);
      });

      it(`should search for the country '${country.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'enableCountry_filterByNameToEnable', baseContext);

        await boCountriesPage.filterTable(page, 'input', 'b!name', country.name);

        const numberOfCountriesAfterFilter = await boCountriesPage.getNumberOfElementInGrid(page);
        expect(numberOfCountriesAfterFilter).to.be.equal(1);

        const textColumn = await boCountriesPage.getTextColumnFromTable(page, 1, 'b!name');
        expect(textColumn).to.equal(country.name);
      });

      it(`should enable the country '${country.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'enableCountry', baseContext);

        await boCountriesPage.setCountryStatus(page, 1, true);

        const currentStatus = await boCountriesPage.getCountryStatus(page, 1);
        expect(currentStatus).to.eq(true);
      });

      it('should go to \'Payment > Preferences\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.paymentParentLink,
          boDashboardPage.preferencesLink,
        );
        await boPaymentPreferencesPage.closeSfToolBar(page);

        const pageTitle = await boPaymentPreferencesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boPaymentPreferencesPage.pageTitle);
      });

      it(`should disable the ${country.name} country for module "${dataModules.psCashOnDelivery.tag}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableCountryModule1', baseContext);

        const result = await boPaymentPreferencesPage.setCountryRestriction(
          page,
          country.id,
          dataModules.psCashOnDelivery.tag,
          false,
        );
        expect(result).to.contains(boPaymentPreferencesPage.successfulUpdateMessage);
      });

      it(`should disable the ${country.name} country for module "${dataModules.psCheckPayment.tag}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableCountryModule2', baseContext);

        const result = await boPaymentPreferencesPage.setCountryRestriction(
          page,
          country.id,
          dataModules.psCheckPayment.tag,
          false,
        );
        expect(result).to.contains(boPaymentPreferencesPage.successfulUpdateMessage);
      });

      it(`should disable the ${country.name} country for module "${dataModules.psWirePayment.tag}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableCountryModule3', baseContext);

        const result = await boPaymentPreferencesPage.setCountryRestriction(
          page,
          country.id,
          dataModules.psWirePayment.tag,
          false,
        );
        expect(result).to.contains(boPaymentPreferencesPage.successfulUpdateMessage);
      });
    });

    describe('Select address', async () => {
      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

        await foHummingbirdHomePage.goToFo(page);
        await foHummingbirdHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage).to.equal(true);
      });

      it('should go to the fourth product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

        await foHummingbirdHomePage.goToProductPage(page, 4);

        const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(dataProducts.demo_5.name);
      });

      it('should add product to cart and go to cart page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

        await foHummingbirdProductPage.addProductToTheCart(page, 1);

        const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
        expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
      });

      it('should validate shopping cart and go to checkout page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

        // Proceed to checkout the shopping cart
        await foHummingbirdCartPage.clickOnProceedToCheckout(page);

        const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
        expect(isCheckoutPage).to.equal(true);
      });

      it('should sign in with default customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'fillCustomerInformation', baseContext);

        await foHummingbirdCheckoutPage.clickOnSignIn(page);

        const isStepCompleted = await foHummingbirdCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
        expect(isStepCompleted).to.equal(true);
      });

      it('should choose the third address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'chooseThirdAddress', baseContext);

        await foHummingbirdCheckoutPage.selectDeliveryAddress(page, 1);

        const isStepCompleted = await foHummingbirdCheckoutPage.clickOnContinueButtonFromAddressStep(page);
        expect(isStepCompleted).to.eq(true);
      });

      it('should continue to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'continueToPaymentStep', baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await foHummingbirdCheckoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.equal(true);
      });

      it('should check that no payment method is available', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNoPaymentMethodAvailable', baseContext);

        const alertMessage = await foHummingbirdCheckoutPage.getNoPaymentAvailableMessage(page);
        expect(alertMessage).to.equal('Unfortunately, there is no payment method available.');
      });
    });

    describe('Disable Country', async () => {
      it('should login in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableCountry_loginBO', baseContext);

        await boLoginPage.goTo(page, global.BO.URL);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to \'International > Locations\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableCountry_goToLocationsPage', baseContext);

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
        await testContext.addContextItem(this, 'testIdentifier', 'disableCountry_goToCountriesPage', baseContext);

        await boZonesPage.goToSubTabCountries(page);

        const pageTitle = await boCountriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCountriesPage.pageTitle);
      });

      it('should reset all filters and get number of countries in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableCountry_resetFilterFirst', baseContext);

        const numberOfCountries = await boCountriesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCountries).to.be.above(0);
      });

      it(`should search for the country '${country.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableCountry_filterByNameToEnable', baseContext);

        await boCountriesPage.filterTable(page, 'input', 'b!name', country.name);

        const numberOfCountriesAfterFilter = await boCountriesPage.getNumberOfElementInGrid(page);
        expect(numberOfCountriesAfterFilter).to.be.equal(1);

        const textColumn = await boCountriesPage.getTextColumnFromTable(page, 1, 'b!name');
        expect(textColumn).to.equal(country.name);
      });

      it(`should disable the country '${country.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableCountry', baseContext);

        await boCountriesPage.setCountryStatus(page, 1, false);

        const currentStatus = await boCountriesPage.getCountryStatus(page, 1);
        expect(currentStatus).to.eq(false);
      });
    });
  });

  // Post-condition : Bulk delete created addresses
  bulkDeleteAddressesTest('select', 'id_country', dataCountries.germany.name, `${baseContext}_postTest_0`);
});
