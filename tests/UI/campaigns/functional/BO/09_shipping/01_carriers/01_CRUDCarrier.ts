// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import carriersPage from '@pages/BO/shipping/carriers';
import addCarrierPage from '@pages/BO/shipping/carriers/add';
// Import FO pages
import {cartPage} from '@pages/FO/cart';
import checkoutPage from '@pages/FO/checkout';
import {homePage} from '@pages/FO/home';
import {loginPage as foLoginPage} from '@pages/FO/login';
import productPage from '@pages/FO/product';

// Import data
import Customers from '@data/demo/customers';
import CarrierData from '@data/faker/carrier';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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

  const createCarrierData: CarrierData = new CarrierData({freeShipping: false, zoneID: 4, allZones: false});
  const editCarrierData: CarrierData = new CarrierData({
    freeShipping: false,
    rangeSup: 50,
    allZones: true,
    enable: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create images
    await Promise.all([
      files.generateImage(`${createCarrierData.name}.jpg`),
      files.generateImage(`${editCarrierData.name}.jpg`),
    ]);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    /* Delete the generated images */
    await Promise.all([
      files.deleteFile(`${createCarrierData.name}.jpg`),
      files.deleteFile(`${editCarrierData.name}.jpg`),
    ]);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Shipping > Carriers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shippingLink,
      dashboardPage.carriersLink,
    );

    const pageTitle = await carriersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(carriersPage.pageTitle);
  });

  it('should reset all filters and get number of carriers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCarriers = await carriersPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCarriers).to.be.above(0);
  });

  // 1 - Create carrier
  describe('Create carrier in BO', async () => {
    it('should go to add new carrier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddCarrierPage', baseContext);

      await carriersPage.goToAddNewCarrierPage(page);

      const pageTitle = await addCarrierPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCarrierPage.pageTitleCreate);
    });

    it('should create carrier and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCarrier', baseContext);

      const textResult = await addCarrierPage.createEditCarrier(page, createCarrierData);
      await expect(textResult).to.contains(carriersPage.successfulCreationMessage);

      const numberCarriersAfterCreation = await carriersPage.getNumberOfElementInGrid(page);
      await expect(numberCarriersAfterCreation).to.be.equal(numberOfCarriers + 1);
    });

    it('should filter list by name and get the new carrier ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToCheckNewCarrier', baseContext);

      await carriersPage.resetFilter(page);
      await carriersPage.filterTable(
        page,
        'input',
        'name',
        createCarrierData.name,
      );

      carrierID = parseInt(await carriersPage.getTextColumn(page, 1, 'id_carrier'), 10);

      const name = await carriersPage.getTextColumn(page, 1, 'name');
      await expect(name).to.contains(createCarrierData.name);
    });
  });

  // 2 - View the created carrier in FO
  describe('View the created carrier in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstViewMyShop', baseContext);

      // Click on view my shop
      page = await carriersPage.viewMyShop(page);
      // Change language
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page is not displayed').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstGoToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstSighInFO', baseContext);

      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should create an order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstCreateOrder', baseContext);

      // Go to home page
      await foLoginPage.goToHomePage(page);
      // Go to the first product page
      await homePage.goToProductPage(page, 1);
      // Add the created product to the cart
      await productPage.addProductToTheCart(page);
      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should check that the new carrier is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewCarrier', baseContext);

      const isShippingMethodVisible = await checkoutPage.isShippingMethodVisible(page, carrierID);
      await expect(isShippingMethodVisible, 'The carrier is visible').to.be.false;
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstSighOutFO', baseContext);

      await checkoutPage.goToHomePage(page);
      await checkoutPage.logout(page);

      const isCustomerConnected = await checkoutPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });

  // 3 - Update carrier
  describe('Update carrier created', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstGoBackToBO', baseContext);

      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await carriersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(carriersPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await carriersPage.resetFilter(page);
      await carriersPage.filterTable(
        page,
        'input',
        'name',
        createCarrierData.name,
      );

      const carrierName = await carriersPage.getTextColumn(page, 1, 'name');
      await expect(carrierName).to.contains(createCarrierData.name);
    });

    it('should go to edit carrier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCarrierPage', baseContext);

      await carriersPage.gotoEditCarrierPage(page, 1);

      const pageTitle = await addCarrierPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCarrierPage.pageTitleEdit);
    });

    it('should update carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCarrier', baseContext);

      const textResult = await addCarrierPage.createEditCarrier(page, editCarrierData);
      await expect(textResult).to.contains(carriersPage.successfulUpdateMessage);

      const numberOfCarriersAfterUpdate = await carriersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCarriersAfterUpdate).to.be.equal(numberOfCarriers + 1);
    });

    it('should filter list by name and get the edited carrier ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToCheckEditedCarrier', baseContext);

      await carriersPage.resetFilter(page);
      await carriersPage.filterTable(
        page,
        'input',
        'name',
        editCarrierData.name,
      );

      carrierID = parseInt(await carriersPage.getTextColumn(page, 1, 'id_carrier'), 10);

      const name = await carriersPage.getTextColumn(page, 1, 'name');
      await expect(name).to.contains(editCarrierData.name);
    });
  });

  // 4 - View the updated carrier in FO
  describe('View the updated carrier in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'secondViewMyShop', baseContext);

      // Click on view my shop
      page = await carriersPage.viewMyShop(page);
      // Change language
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page is not displayed').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'secondGoToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'secondSighInFO', baseContext);

      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should create an order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'secondCreateOrder', baseContext);

      // Go to home page
      await foLoginPage.goToHomePage(page);
      // Go to the first product page
      await homePage.goToProductPage(page, 1);
      // Add the created product to the cart
      await productPage.addProductToTheCart(page);
      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should check that the updated carrier is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedCarrier', baseContext);

      const isShippingMethodVisible = await checkoutPage.isShippingMethodVisible(page, carrierID);
      await expect(isShippingMethodVisible, 'The carrier is not visible').to.be.true;
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'secondSighOutFO', baseContext);

      await checkoutPage.goToHomePage(page);
      await checkoutPage.logout(page);

      const isCustomerConnected = await checkoutPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });

  // 5 - Delete carrier
  describe('Delete carrier', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'secondGoBackToBO', baseContext);

      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await carriersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(carriersPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await carriersPage.resetFilter(page);
      await carriersPage.filterTable(
        page,
        'input',
        'name',
        editCarrierData.name,
      );

      const carrierName = await carriersPage.getTextColumn(page, 1, 'name');
      await expect(carrierName).to.contains(editCarrierData.name);
    });

    it('should delete carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCarrier', baseContext);

      const textResult = await carriersPage.deleteCarrier(page, 1);
      await expect(textResult).to.contains(carriersPage.successfulDeleteMessage);

      const numberOfCarriersAfterDelete = await carriersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCarriersAfterDelete).to.be.equal(numberOfCarriers);
    });
  });
});
