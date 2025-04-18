// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';

// Import data
import {
  boCarriersCreatePage,
  boCarriersPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCarriers,
  dataCountries,
  dataCustomers,
  dataGroups,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCarrier,
  FakerCustomer,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shipping_carriers_generalSettings';

describe('BO - Shipping - Carriers : General Settings', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCarriers: number = 0;
  let idCarrier: number;

  const carrierData: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Test General',
    speedGrade: 0,
    transitName: '9 days',
    trackingURL: '',
    // Shipping locations and cost
    freeShipping: true,
    ranges: [
      {
        zones: [
          {
            zone: 'all',
          },
        ],
      },
    ],
    // Size weight and group access
    enable: false,
    groupAccesses: [
      dataGroups.customer,
      dataGroups.guest,
      dataGroups.visitor,
    ],
  });
  const carrierDataEnabled: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Test General',
    speedGrade: 0,
    transitName: '9 days',
    trackingURL: '',
    // Shipping locations and cost
    freeShipping: true,
    ranges: [
      {
        zones: [
          {
            zone: 'all',
          },
        ],
      },
    ],
    // Size weight and group access
    enable: true,
    groupAccesses: [
      dataGroups.customer,
      dataGroups.guest,
      dataGroups.visitor,
    ],
  });
  const carrierDataName: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Test General 2',
    speedGrade: 9,
    transitName: '1 day',
    trackingURL: '',
    // Shipping locations and cost
    freeShipping: true,
    ranges: [
      {
        zones: [
          {
            zone: 'all',
          },
        ],
      },
    ],
    // Size weight and group access
    enable: true,
    groupAccesses: [
      dataGroups.customer,
      dataGroups.guest,
      dataGroups.visitor,
    ],
  });
  const carrierDataGuest: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Test General 2',
    speedGrade: 9,
    transitName: '1 day',
    trackingURL: '',
    // Shipping locations and cost
    freeShipping: true,
    ranges: [
      {
        zones: [
          {
            zone: 'all',
          },
        ],
      },
    ],
    // Size weight and group access
    enable: true,
    groupAccesses: [
      dataGroups.guest,
    ],
  });
  const customerGuest: FakerCustomer = new FakerCustomer({});
  const addressGuest: FakerAddress = new FakerAddress({
    country: dataCountries.france.name,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create images
    await Promise.all([
      utilsFile.generateImage(`${carrierData.name}.jpg`),
      utilsFile.generateImage(`${carrierDataName.name}.jpg`),
    ]);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Delete the generated images
    await Promise.all([
      utilsFile.deleteFile(`${carrierData.name}.jpg`),
      utilsFile.deleteFile(`${carrierDataName.name}.jpg`),
    ]);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Shipping > Carriers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shippingLink,
      boDashboardPage.carriersLink,
    );

    const pageTitle = await boCarriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersPage.pageTitle);
  });

  it('should reset all filters and get number of carriers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCarriers = await boCarriersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCarriers).to.be.above(0);
  });

  it('should go to add new carrier page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddCarrierPage', baseContext);

    await boCarriersPage.goToAddNewCarrierPage(page);

    const pageTitle = await boCarriersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleCreate);
  });

  it('should create carrier and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createCarrier', baseContext);

    const textResult = await boCarriersCreatePage.createEditCarrier(page, carrierData);
    expect(textResult).to.contains(boCarriersPage.successfulCreationMessage);
  });

  it('should return to carriers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'returnToCarriers', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shippingLink,
      boDashboardPage.carriersLink,
    );

    const pageTitle = await boCarriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersPage.pageTitle);

    const numberCarriersAfterCreation = await boCarriersPage.getNumberOfElementInGrid(page);
    expect(numberCarriersAfterCreation).to.be.equal(numberOfCarriers + 1);
  });

  it('should view my shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

    // Click on view my shop
    page = await boCarriersPage.viewMyShop(page);
    // Change language
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Home page is not displayed').to.eq(true);
  });

  it(`should search for the product '${dataProducts.demo_6.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchForProduct', baseContext);

    await foClassicHomePage.searchProduct(page, dataProducts.demo_6.name);

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
  });

  it('should add the product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await foClassicSearchResultsPage.goToProductPage(page, 1);
    // Add the product to the cart
    await foClassicProductPage.addProductToTheCart(page, 1, [], false);

    const notificationsNumber = await foClassicProductPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.equal(1);
  });

  it('should go to shopping cart page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCart', baseContext);

    await foClassicProductPage.goToCartPage(page);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.contains(foClassicCartPage.pageTitle);
  });

  it('should proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

    // Proceed to checkout the shopping cart
    await foClassicCartPage.clickOnProceedToCheckout(page);

    // Go to checkout page
    const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.eq(true);
  });

  it('should fill guest personal information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformation', baseContext);

    await foClassicCheckoutPage.clickOnSignIn(page);

    const isCustomerConnected = await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
    expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
  });

  it('should choose the delivery address', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseAndConfirmAddressStep', baseContext);

    await foClassicCheckoutPage.chooseDeliveryAddress(page, 1);

    const isDeliveryStep = await foClassicCheckoutPage.goToDeliveryStep(page);
    expect(isDeliveryStep).to.eq(true);
  });

  it('should check the carriers position', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPosition', baseContext);

    const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carriers).to.deep.equal([dataCarriers.clickAndCollect.name, dataCarriers.myCarrier.name]);
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstGoBackToBO', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);

    const pageTitle = await boCarriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersPage.pageTitle);
  });

  it('should filter list by name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

    await boCarriersPage.resetFilter(page);
    await boCarriersPage.filterTable(
      page,
      'input',
      'name',
      carrierData.name,
    );

    const carrierName = await boCarriersPage.getTextColumn(page, 1, 'name');
    expect(carrierName).to.contains(carrierData.name);

    idCarrier = parseInt(await boCarriersPage.getTextColumn(page, 1, 'id_carrier'), 10);
  });

  it('should go to edit carrier page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditCarrierPageStatus', baseContext);

    await boCarriersPage.goToEditCarrierPage(page, 1);

    const pageTitle = await boCarriersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleEdit);
  });

  it('should update the carrier status', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCarrierStatus', baseContext);

    const textResult = await boCarriersCreatePage.createEditCarrier(page, carrierDataEnabled);
    expect(textResult).to.contains(boCarriersPage.successfulUpdateMessage);
  });

  it('should return to carriers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'returnToCarriersAfterUpdateCarrierStatus', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shippingLink,
      boDashboardPage.carriersLink,
    );

    const pageTitle = await boCarriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersPage.pageTitle);

    const numberCarriersAfterUpdate = await boCarriersPage.getNumberOfElementInGrid(page);
    expect(numberCarriersAfterUpdate).to.be.equal(1);

    idCarrier = parseInt(await boCarriersPage.getTextColumn(page, 1, 'id_carrier'), 10);
  });

  it('should check the carriers position', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPositionAfterStatus', baseContext);

    page = await boCarriersPage.changePage(browserContext, 1);
    await foClassicCheckoutPage.reloadPage(page);

    const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carriers).to.deep.equal([
      carrierDataEnabled.name,
      dataCarriers.clickAndCollect.name,
      dataCarriers.myCarrier.name,
    ]);
  });

  it('should go to edit carrier page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditCarrierPageName', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);
    await boCarriersPage.goToEditCarrierPage(page, 1);

    const pageTitle = await boCarriersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleEdit);
  });

  it('should update the carrier name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCarrierName', baseContext);

    const textResult = await boCarriersCreatePage.createEditCarrier(page, carrierDataName);
    expect(textResult).to.contains(boCarriersPage.successfulUpdateMessage);
  });

  it('should return to carriers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'returnToCarriersAfterUpdateCarrierName', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shippingLink,
      boDashboardPage.carriersLink,
    );

    const pageTitle = await boCarriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersPage.pageTitle);

    const numberCarriersAfterUpdate = await boCarriersPage.getNumberOfElementInGrid(page);
    expect(numberCarriersAfterUpdate).to.be.equal(1);

    idCarrier = parseInt(await boCarriersPage.getTextColumn(page, 1, 'id_carrier'), 10);
  });

  it('should check the carriers position', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPositionAfterName', baseContext);

    page = await boCarriersPage.changePage(browserContext, 1);
    await foClassicCheckoutPage.reloadPage(page);

    const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carriers).to.deep.equal([
      carrierDataName.name,
      dataCarriers.clickAndCollect.name,
      dataCarriers.myCarrier.name,
    ]);

    const carrierData = await foClassicCheckoutPage.getCarrierData(page, idCarrier);
    await Promise.all([
      expect(carrierData.name).to.equal(carrierDataName.name),
      expect(carrierData.transitName).to.equal(carrierDataName.transitName),
    ]);
  });

  it('should go to edit carrier page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditCarrierPageGroupAccess', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);
    await boCarriersPage.goToEditCarrierPage(page, 1);

    const pageTitle = await boCarriersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleEdit);
  });

  it('should update the carrier group access', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCarrierGroupAccess', baseContext);

    const textResult = await boCarriersCreatePage.createEditCarrier(page, carrierDataGuest);
    expect(textResult).to.contains(boCarriersPage.successfulUpdateMessage);
  });

  it('should return to carriers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'returnToCarriersAfterUpdateCarrierGroupAccess', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shippingLink,
      boDashboardPage.carriersLink,
    );

    const pageTitle = await boCarriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersPage.pageTitle);

    const numberCarriersAfterUpdate = await boCarriersPage.getNumberOfElementInGrid(page);
    expect(numberCarriersAfterUpdate).to.be.equal(1);

    idCarrier = parseInt(await boCarriersPage.getTextColumn(page, 1, 'id_carrier'), 10);
  });

  it('should check the carriers position', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPositionAfterGroupAccess', baseContext);

    page = await boCarriersPage.changePage(browserContext, 1);
    await foClassicCheckoutPage.reloadPage(page);

    const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carriers).to.deep.equal([
      dataCarriers.clickAndCollect.name,
      dataCarriers.myCarrier.name,
    ]);
  });

  it('should click Personal information tab and logout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickPersonalInformationLogout', baseContext);

    await foClassicCheckoutPage.clickOnEditPersonalInformationStep(page);
    const isCustomerConnected = await foClassicCheckoutPage.logOutCustomer(page);
    expect(isCustomerConnected).to.eq(false);
  });

  it(`should search for the product '${dataProducts.demo_6.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchForProductGuest', baseContext);

    await foClassicCartPage.searchProduct(page, dataProducts.demo_6.name);

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
  });

  it('should add the product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCartGuest', baseContext);

    await foClassicSearchResultsPage.goToProductPage(page, 1);
    // Add the product to the cart
    await foClassicProductPage.addProductToTheCart(page, 1, [], false);

    const notificationsNumber = await foClassicProductPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.equal(1);
  });

  it('should go to shopping cart page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartGuest', baseContext);

    await foClassicProductPage.goToCartPage(page);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.contains(foClassicCartPage.pageTitle);
  });

  it('should proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckoutGuest', baseContext);

    // Proceed to checkout the shopping cart
    await foClassicCartPage.clickOnProceedToCheckout(page);

    // Go to checkout page
    const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.eq(true);
  });

  it('should fill guest personal information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformationGuest', baseContext);

    const isStepPersonalInfoCompleted = await foClassicCheckoutPage.setGuestPersonalInformation(page, customerGuest);
    expect(isStepPersonalInfoCompleted).to.equal(true);
  });

  it('should fill address form and go to delivery step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

    const isStepAddressComplete = await foClassicCheckoutPage.setAddress(page, addressGuest);
    expect(isStepAddressComplete).to.equal(true);
  });

  it('should validate the order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'validateOrder', baseContext);

    // Delivery step - Go to payment step
    const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete, 'Step Address is not complete').to.equal(true);

    // Payment step - Choose payment step
    await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);
    const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);

    // Check the confirmation message
    expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
  });

  it('should delete carrier', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteCarrier', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);

    const textResult = await boCarriersPage.deleteCarrier(page, 1);
    expect(textResult).to.contains(boCarriersPage.successfulDeleteMessage);

    const numberOfCarriersAfterDelete = await boCarriersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCarriersAfterDelete).to.be.equal(numberOfCarriers);
  });
});
