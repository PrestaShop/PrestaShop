import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCountriesPage,
  boDashboardPage,
  boLoginPage,
  boZonesPage,
  type BrowserContext,
  dataCountries,
  dataPaymentMethods,
  FakerAddress,
  FakerCustomer,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

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

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

    const numberOfCountries = await boCountriesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCountries).to.be.above(0);
  });

  it('should filter by Enabled countries', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterEnabled', baseContext);

    await boCountriesPage.filterTable(page, 'select', 'a!active', '1');

    const numberOfCountries = await boCountriesPage.getNumberOfElementInGrid(page);
    expect(numberOfCountries).to.equal(2);
  });

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    // Click on view my shop
    page = await boCountriesPage.viewMyShop(page);
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

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.equal(1);
  });

  it('should proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'proceedtoCheckout', baseContext);

    // Proceed to checkout the shopping cart
    await foClassicCartPage.clickOnProceedToCheckout(page);

    // Go to checkout page
    const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.eq(true);
  });

  it('should fill guest personal information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformation', baseContext);

    const isStepPersonalInfoCompleted = await foClassicCheckoutPage.setGuestPersonalInformation(page, customerData);
    expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.eq(true);
  });

  it('should check available countries', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAvailableCountries', baseContext);

    const countries = await foClassicCheckoutPage.getAvailableAddressCountries(page);
    expect(countries.length).to.equal(2);
    expect(countries).to.deep.equal([
      dataCountries.france.name,
      dataCountries.unitedStates.name,
    ]);
  });

  it('should fill address form and go to delivery step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

    const isStepAddressComplete = await foClassicCheckoutPage.setAddress(page, addressDataFR);
    expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
  });

  it('should go to payment step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

    // Delivery step - Go to payment step
    const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
  });

  it('should choose payment method and confirm the order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

    // Payment step - Choose payment step
    await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

    // Check the confirmation message
    const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
    expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
  });

  it('should disable France', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableFrance', baseContext);

    page = await foClassicCheckoutOrderConfirmationPage.closePage(browserContext, page, 0);

    await boCountriesPage.resetFilter(page);
    await boCountriesPage.filterTable(page, 'input', 'b!name', dataCountries.france.name);
    await boCountriesPage.setCountryStatus(page, 1, false);

    const currentStatus = await boCountriesPage.getCountryStatus(page, 1);
    expect(currentStatus).to.eq(false);
  });

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFOAfterDisable', baseContext);

    // Click on view my shop
    page = await boCountriesPage.viewMyShop(page);
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

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.equal(1);
  });

  it('should proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'proceedtoCheckoutAfterDisable', baseContext);

    // Proceed to checkout the shopping cart
    await foClassicCartPage.clickOnProceedToCheckout(page);

    // Go to checkout page
    const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.eq(true);
  });

  it('should fill guest personal information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformationAfterDisable', baseContext);

    const isStepPersonalInfoCompleted = await foClassicCheckoutPage.setGuestPersonalInformation(page, customerData);
    expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.eq(true);
  });

  it('should check available countries', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAvailableCountriesAfterDisable', baseContext);

    const countries = await foClassicCheckoutPage.getAvailableAddressCountries(page);
    expect(countries.length).to.equal(1);
    expect(countries).to.deep.equal([
      dataCountries.unitedStates.name,
    ]);
  });

  it('should fill address form and go to delivery step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setAddressStepAfterDisable', baseContext);

    const isStepAddressComplete = await foClassicCheckoutPage.setAddress(page, addressDataUS);
    expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
  });

  it('should go to payment step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStepAfterDisable', baseContext);

    // Delivery step - Go to payment step
    const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
  });

  // @todo : https://github.com/PrestaShop/PrestaShop/issues/36602
  it('should choose payment method and confirm the order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'confirmOrderAfterDisable', baseContext);

    this.skip();

    // Payment step - Choose payment step
    await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

    // Check the confirmation message
    const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
    expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
  });

  it('POST-TEST : should enable France', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableFrance', baseContext);

    page = await foClassicCheckoutOrderConfirmationPage.closePage(browserContext, page, 0);

    await boCountriesPage.setCountryStatus(page, 1, true);

    const currentStatus = await boCountriesPage.getCountryStatus(page, 1);
    expect(currentStatus).to.eq(true);

    await boCountriesPage.resetFilter(page);
  });
});
