// Import utils
import testContext from '@utils/testContext';

import {
  boCarriersPage,
  boCarriersCreatePage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  dataZones,
  FakerCarrier,
  boDashboardPage,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_shipping_carriers_CRUDCarrier';

/*
Create new carrier
Check the existence of the new carrier in FO
Update the created carrier
Check the existence of the update carrier in FO
Delete carrier
 */
describe('BO - Shipping - Carriers : CRUD carrier in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCarriers: number = 0;
  let carrierID: number = 0;

  const createCarrierData: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Carrier Created',
    speedGrade: 7,
    trackingURL: 'https://example.com/track.php?num=@',
    // Shipping locations and cost
    handlingCosts: false,
    freeShipping: false,
    billing: 'According to total weight',
    taxRule: 'No tax',
    outOfRangeBehavior: 'Apply the cost of the highest defined range',
    ranges: [
      {
        weightMin: 0,
        weightMax: 5,
        zones: [
          {
            zone: dataZones.europe,
            price: 5,
          },
          {
            zone: dataZones.northAmerica,
            price: 2,
          },
        ],
      },
      {
        weightMin: 5,
        weightMax: 10,
        zones: [
          {
            zone: dataZones.europe,
            price: 10,
          },
          {
            zone: dataZones.northAmerica,
            price: 4,
          },
        ],
      },
      {
        weightMin: 10,
        weightMax: 20,
        zones: [
          {
            zone: dataZones.europe,
            price: 20,
          },
          {
            zone: dataZones.northAmerica,
            price: 8,
          },
        ],
      },
    ],
    // Size weight and group access
    maxWidth: 200,
    maxHeight: 200,
    maxDepth: 200,
    maxWeight: 500,
    enable: true,
  });
  const editCarrierData: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Carrier Updated',
    // Shipping locations and cost
    handlingCosts: false,
    freeShipping: false,
    billing: 'According to total weight',
    ranges: [
      {
        weightMin: 0,
        weightMax: 5,
        zones: [
          {
            zone: dataZones.europe,
            price: 5,
          },
          {
            zone: dataZones.northAmerica,
            price: 2,
          },
        ],
      },
      {
        weightMin: 5,
        weightMax: 10,
        zones: [
          {
            zone: dataZones.europe,
            price: 10,
          },
          {
            zone: dataZones.northAmerica,
            price: 4,
          },
        ],
      },
      {
        weightMin: 10,
        weightMax: 20,
        zones: [
          {
            zone: dataZones.europe,
            price: 20,
          },
          {
            zone: dataZones.northAmerica,
            price: 8,
          },
        ],
      },
    ],
    // Size weight and group access
    maxWidth: 700,
    maxHeight: 500,
    enable: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create images
    await Promise.all([
      utilsFile.generateImage(`${createCarrierData.name}.jpg`),
      utilsFile.generateImage(`${editCarrierData.name}.jpg`),
    ]);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    /* Delete the generated images */
    await Promise.all([
      utilsFile.deleteFile(`${createCarrierData.name}.jpg`),
      utilsFile.deleteFile(`${editCarrierData.name}.jpg`),
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

  // 1 - Create carrier
  describe('Create carrier in BO', async () => {
    it('should go to add new carrier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddCarrierPage', baseContext);

      await boCarriersPage.goToAddNewCarrierPage(page);

      const pageTitle = await boCarriersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleCreate);
    });

    it('should create carrier and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCarrier', baseContext);

      const textResult = await boCarriersCreatePage.createEditCarrier(page, createCarrierData);
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

    it('should filter list by name and get the new carrier ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToCheckNewCarrier', baseContext);

      await boCarriersPage.resetFilter(page);
      await boCarriersPage.filterTable(
        page,
        'input',
        'name',
        createCarrierData.name,
      );

      carrierID = parseInt(await boCarriersPage.getTextColumn(page, 1, 'id_carrier'), 10);

      const name = await boCarriersPage.getTextColumn(page, 1, 'name');
      expect(name).to.contains(createCarrierData.name);
    });
  });

  // 2 - View the created carrier in FO
  describe('View the created carrier in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstViewMyShop', baseContext);

      // Click on view my shop
      page = await boCarriersPage.viewMyShop(page);
      // Change language
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstGoToLoginPageFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstSighInFO', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should create an order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstCreateOrder', baseContext);

      // Go to home page
      await foClassicLoginPage.goToHomePage(page);
      // Go to the first product page
      await foClassicHomePage.goToProductPage(page, 1);
      // Add the created product to the cart
      await foClassicProductPage.addProductToTheCart(page);
      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should check that the new carrier is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewCarrier', baseContext);

      const shippingMethodName = await foClassicCheckoutPage.getShippingMethodName(page, carrierID);
      expect(shippingMethodName).to.eq(createCarrierData.name);

      const isShippingMethodVisible = await foClassicCheckoutPage.isShippingMethodVisible(page, carrierID);
      expect(isShippingMethodVisible).to.eq(true);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstSighOutFO', baseContext);

      await foClassicCheckoutPage.goToHomePage(page);
      await foClassicCheckoutPage.logout(page);

      const isCustomerConnected = await foClassicCheckoutPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstGoBackToBO', baseContext);

      page = await foClassicCheckoutPage.closePage(browserContext, page, 0);

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });
  });

  // 3 - Update carrier
  describe('Update carrier created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await boCarriersPage.resetFilter(page);
      await boCarriersPage.filterTable(
        page,
        'input',
        'name',
        createCarrierData.name,
      );

      const carrierName = await boCarriersPage.getTextColumn(page, 1, 'name');
      expect(carrierName).to.contains(createCarrierData.name);
    });

    it('should go to edit carrier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCarrierPage', baseContext);

      await boCarriersPage.goToEditCarrierPage(page, 1);

      const pageTitle = await boCarriersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleEdit);
    });

    it('should update carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCarrier', baseContext);

      const textResult = await boCarriersCreatePage.createEditCarrier(page, editCarrierData);
      expect(textResult).to.contains(boCarriersPage.successfulUpdateMessage);
    });

    it('should return to carriers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToCarriersAfterUpdate', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.carriersLink,
      );

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);

      const numberOfCarriersAfterUpdate = await boCarriersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCarriersAfterUpdate).to.be.equal(numberOfCarriers + 1);
    });

    it('should filter list by name and get the edited carrier ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToCheckEditedCarrier', baseContext);

      await boCarriersPage.resetFilter(page);
      await boCarriersPage.filterTable(
        page,
        'input',
        'name',
        editCarrierData.name,
      );

      carrierID = parseInt(await boCarriersPage.getTextColumn(page, 1, 'id_carrier'), 10);

      const name = await boCarriersPage.getTextColumn(page, 1, 'name');
      expect(name).to.contains(editCarrierData.name);
    });
  });

  // 4 - View the updated carrier in FO
  describe('View the updated carrier in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'secondViewMyShop', baseContext);

      // Click on view my shop
      page = await boCarriersPage.viewMyShop(page);
      // Change language
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'secondGoToLoginPageFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'secondSighInFO', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should create an order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'secondCreateOrder', baseContext);

      // Go to home page
      await foClassicLoginPage.goToHomePage(page);
      // Go to the first product page
      await foClassicHomePage.goToProductPage(page, 1);
      // Add the created product to the cart
      await foClassicProductPage.addProductToTheCart(page);
      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should check that the updated carrier is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedCarrier', baseContext);

      const shippingMethodName = await foClassicCheckoutPage.getShippingMethodName(page, carrierID);
      expect(shippingMethodName).to.eq(editCarrierData.name);

      const isShippingMethodVisible = await foClassicCheckoutPage.isShippingMethodVisible(page, carrierID);
      expect(isShippingMethodVisible, 'The carrier is not visible').to.eq(true);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'secondSighOutFO', baseContext);

      await foClassicCheckoutPage.goToHomePage(page);
      await foClassicCheckoutPage.logout(page);

      const isCustomerConnected = await foClassicCheckoutPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'secondGoBackToBO', baseContext);

      page = await foClassicCheckoutPage.closePage(browserContext, page, 0);

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });
  });

  // 5 - Delete carrier
  describe('Delete carrier', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await boCarriersPage.resetFilter(page);
      await boCarriersPage.filterTable(
        page,
        'input',
        'name',
        editCarrierData.name,
      );

      const carrierName = await boCarriersPage.getTextColumn(page, 1, 'name');
      expect(carrierName).to.contains(editCarrierData.name);
    });

    it('should delete carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCarrier', baseContext);

      const textResult = await boCarriersPage.deleteCarrier(page, 1);
      expect(textResult).to.contains(boCarriersPage.successfulDeleteMessage);

      const numberOfCarriersAfterDelete = await boCarriersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCarriersAfterDelete).to.be.equal(numberOfCarriers);
    });
  });
});
