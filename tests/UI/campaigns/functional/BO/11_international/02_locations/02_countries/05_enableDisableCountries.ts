// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import zonesPage from '@pages/BO/international/locations';
import countriesPage from '@pages/BO/international/locations/countries';
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {orderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';

import {
  boDashboardPage,
  dataCountries,
  dataPaymentMethods,
  FakerAddress,
  FakerCustomer,
  foClassicHomePage,
  foClassicProductPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_locations_countries_enableDisableCountries';

describe('BO - International - Countries : Enable / Disable Countries', async () => {
  const customerData: FakerCustomer = new FakerCustomer({password: ''});
  const addressDataFR: FakerAddress = new FakerAddress({country: dataCountries.france.name});
  const addressDataUS: FakerAddress = new FakerAddress({
    country: dataCountries.unitedStates.name,
    state: 'Alabama',
  });

  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'International > Locations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocationsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.locationsLink,
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

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

    const numberOfCountries = await countriesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCountries).to.be.above(0);
  });

  it('should filter by Enabled countries', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterEnabled', baseContext);

    await countriesPage.filterTable(page, 'select', 'a!active', '1');

    const numberOfCountries = await countriesPage.getNumberOfElementInGrid(page);
    expect(numberOfCountries).to.equal(2);
  });

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    // Click on view my shop
    page = await countriesPage.viewMyShop(page);
    // Change FO language
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it('should add product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    // Go to the first product page
    await foClassicHomePage.goToProductPage(page, 1);
    // Add the product to the cart
    await foClassicProductPage.addProductToTheCart(page);

    const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.equal(1);
  });

  it('should proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    // Proceed to checkout the shopping cart
    await cartPage.clickOnProceedToCheckout(page);

    // Go to checkout page
    const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.eq(true);
  });

  it('should fill guest personal information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformation', baseContext);

    const isStepPersonalInfoCompleted = await checkoutPage.setGuestPersonalInformation(page, customerData);
    expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.eq(true);
  });

  it('should check available countries', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAvailableCountries', baseContext);

    const countries = await checkoutPage.getAvailableAddressCountries(page);
    expect(countries.length).to.equal(2);
    expect(countries).to.deep.equal([
      dataCountries.france.name,
      dataCountries.unitedStates.name,
    ]);
  });

  it('should fill address form and go to delivery step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

    const isStepAddressComplete = await checkoutPage.setAddress(page, addressDataFR);
    expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
  });

  it('should go to payment step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

    // Delivery step - Go to payment step
    const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
  });

  it('should choose payment method and confirm the order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

    // Payment step - Choose payment step
    await checkoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

    // Check the confirmation message
    const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
    expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
  });

  it('should disable France', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableFrance', baseContext);

    page = await orderConfirmationPage.closePage(browserContext, page, 0);

    await countriesPage.resetFilter(page);
    await countriesPage.filterTable(page, 'input', 'b!name', dataCountries.france.name);
    await countriesPage.setCountryStatus(page, 1, false);

    const currentStatus = await countriesPage.getCountryStatus(page, 1);
    expect(currentStatus).to.eq(false);

  });

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFOAfterDisable', baseContext);

    // Click on view my shop
    page = await countriesPage.viewMyShop(page);
    // Change FO language
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it('should add product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCartAfterDisable', baseContext);

    // Go to the first product page
    await foClassicHomePage.goToProductPage(page, 1);
    // Add the product to the cart
    await foClassicProductPage.addProductToTheCart(page);

    const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.equal(1);
  });

  it('should proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCartAfterDisable', baseContext);

    // Proceed to checkout the shopping cart
    await cartPage.clickOnProceedToCheckout(page);

    // Go to checkout page
    const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.eq(true);
  });

  it('should fill guest personal information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformationAfterDisable', baseContext);

    const isStepPersonalInfoCompleted = await checkoutPage.setGuestPersonalInformation(page, customerData);
    expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.eq(true);
  });

  it('should check available countries', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAvailableCountriesAfterDisable', baseContext);

    const countries = await checkoutPage.getAvailableAddressCountries(page);
    expect(countries.length).to.equal(1);
    expect(countries).to.deep.equal([
      dataCountries.unitedStates.name,
    ]);
  });

  it('should fill address form and go to delivery step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setAddressStepAfterDisable', baseContext);

    const isStepAddressComplete = await checkoutPage.setAddress(page, addressDataUS);
    expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
  });

  it('should go to payment step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStepAfterDisable', baseContext);

    // Delivery step - Go to payment step
    const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
  });

  // @todo : https://github.com/PrestaShop/PrestaShop/issues/36602
  it('should choose payment method and confirm the order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'confirmOrderAfterDisable', baseContext);

    this.skip();

    // Payment step - Choose payment step
    await checkoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

    // Check the confirmation message
    const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
    expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
  });

  it('POST-TEST : should enable France', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableFrance', baseContext);

    page = await orderConfirmationPage.closePage(browserContext, page, 0);

    await countriesPage.setCountryStatus(page, 1, true);

    const currentStatus = await countriesPage.getCountryStatus(page, 1);
    expect(currentStatus).to.eq(true);

    await countriesPage.resetFilter(page);
  });
});
