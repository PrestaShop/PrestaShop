require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const testContext = require('@utils/testContext');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const customerSettingsPage = require('@pages/BO/shopParameters/customerSettings');
const groupsPage = require('@pages/BO/shopParameters/customerSettings/groups');
const addGroupPage = require('@pages/BO/shopParameters/customerSettings/groups/add');
const preferencesPage = require('@pages/BO/shipping/preferences');
const carriersPage = require('@pages/BO/shipping/carriers');
const addCarrierPage = require('@pages/BO/shipping/carriers/add');

// Import FO pages
const homePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');

// Import data
const {groupAccess} = require('@data/demo/groupAccess');
const {DefaultCustomer} = require('@data/demo/customer');
const CarrierFaker = require('@data/faker/carrier');

const baseContext = 'functional_BO_shipping_preferences_handling_handlingCharges';

// Browser and tab
let browserContext;
let page;
let newCarrierID = 0;
const createCarrierData = new CarrierFaker({
  freeShipping: false,
  allZones: true,
  handlingCosts: true,
  allZonesValue: 5.00,
  rangeSup: 50,
});
const priceDisplayMethod = ['Tax excluded', 'Tax included'];
const defaultHandlingChargesValue = 2.00;
const updateHandlingChargesValue = 4.00;

/*
Choose display tax excluded in Customer group page
Create carrier and enable Add handling costs
Go to FO and check shipping cost for the new carrier
Update Handling charges
Go to FO and check shipping cost for the new carrier
Go back to default value : Handling charges
Delete created carrier
Go back to default value : tax included in Customer group page
 */
describe('BO - Shipping - Preferences : Test handling charges for carriers in FO', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create image
    await files.generateImage(`${createCarrierData.name}.jpg`);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    // Delete image
    await files.deleteFile(`${createCarrierData.name}.jpg`);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  // 1 - Choose display tax excluded in FO
  describe('Choose Price display method: tax excluded', async () => {
    it('should go to \'Shop parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.customerSettingsLink,
      );

      await customerSettingsPage.closeSfToolBar(page);

      const pageTitle = await customerSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
    });

    it('should go to Groups page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage1', baseContext);

      await customerSettingsPage.goToGroupsPage(page);

      const pageTitle = await groupsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(groupsPage.pageTitle);
    });

    it(`should filter by '${groupAccess.customer.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByGroupName1', baseContext);

      await groupsPage.filterTable(page, 'input', 'b!name', groupAccess.customer.name);

      const textColumn = await groupsPage.getTextColumn(page, 1, 'b!name');
      await expect(textColumn).to.contains(groupAccess.customer.name);
    });

    it('should go to edit group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditGroupPage1', baseContext);

      await groupsPage.gotoEditGroupPage(page, 1);

      const pageTitle = await addGroupPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addGroupPage.pageTitleEdit);
    });

    it('should update group by choosing \'Tax excluded\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateGroup1', baseContext);

      const textResult = await addGroupPage.setPriceDisplayMethod(page, priceDisplayMethod[0]);
      await expect(textResult).to.contains(groupsPage.successfulUpdateMessage);
    });
  });

  // 2 - Create carrier and enable Add handling costs
  describe('Create new carrier and enable \'Add handling costs\'', async () => {
    it('should go to \'Shipping > Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shippingLink,
        dashboardPage.carriersLink,
      );

      const pageTitle = await carriersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(carriersPage.pageTitle);
    });

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
    });

    it('should filter list by name and get the new carrier ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToCheckNewCarrier', baseContext);

      await carriersPage.resetFilter(page);

      await carriersPage.filterTable(page, 'input', 'name', createCarrierData.name);

      newCarrierID = await carriersPage.getTextColumn(page, 1, 'id_carrier');

      const name = await carriersPage.getTextColumn(page, 1, 'name');
      await expect(name).to.contains(createCarrierData.name);
    });
  });

  // 3 - Go to FO and check shipping cost for the new carrier
  describe(`Check shipping costs for the carrier '${createCarrierData.name}' in FO`, async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstViewMyShop1', baseContext);

      // Click on view my shop
      page = await carriersPage.viewMyShop(page);

      // Change language
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page is not displayed').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstGoToLoginPageFO1', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstSighInFO1', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
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

    it('should select the new carrier and check the chipping costs', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost1', baseContext);

      await checkoutPage.chooseShippingMethodAndAddComment(page, newCarrierID);

      const shippingCost = await checkoutPage.getShippingCost(page);
      expect(shippingCost).to.contains(defaultHandlingChargesValue + createCarrierData.allZonesValue);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO1', baseContext);

      await checkoutPage.goToHomePage(page);
      await checkoutPage.logout(page);

      const isCustomerConnected = await checkoutPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });

  // 4 - Update handling charges value
  describe('Update handling charges', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await carriersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(carriersPage.pageTitle);
    });

    it('should go to \'Shipping > Preferences\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.shippingLink, dashboardPage.shippingPreferencesLink);

      const pageTitle = await preferencesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(preferencesPage.pageTitle);
    });

    it('should update \'Handling charges\' value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateHandlingCharges1', baseContext);

      const textResult = await preferencesPage.setHandlingCharges(page, updateHandlingChargesValue);
      await expect(textResult).to.contain(preferencesPage.successfulUpdateMessage);
    });
  });

  // 5 - Go to FO and check shipping cost for the created carrier
  describe(`Check shipping costs for the carrier '${createCarrierData.name}' in FO`, async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstViewMyShop2', baseContext);

      // Click on view my shop
      page = await carriersPage.viewMyShop(page);

      // Change language
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page is not displayed').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstGoToLoginPageFO2', baseContext);

      await homePage.goToLoginPage(page);
      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstSighInFO2', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
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

    it('should select the new carrier and check the chipping costs', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost2', baseContext);

      await checkoutPage.chooseShippingMethodAndAddComment(page, newCarrierID);

      const shippingCost = await checkoutPage.getShippingCost(page);
      expect(shippingCost).to.contains(updateHandlingChargesValue + createCarrierData.allZonesValue);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFo2', baseContext);

      await checkoutPage.goToHomePage(page);
      await checkoutPage.logout(page);

      const isCustomerConnected = await checkoutPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });

  // 6 - Go back to default handling cost value
  describe('Go back to default handling cost value', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await preferencesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(preferencesPage.pageTitle);
    });

    it('should update \'Handling charges\' value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateHandlingCharges2', baseContext);

      const textResult = await preferencesPage.setHandlingCharges(page, defaultHandlingChargesValue);
      await expect(textResult).to.contain(preferencesPage.successfulUpdateMessage);
    });
  });

  // 7 - Delete the new carrier
  describe('Delete carrier', async () => {
    it('should go to \'Shipping > Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shippingLink,
        dashboardPage.carriersLink,
      );

      const pageTitle = await carriersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(carriersPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await carriersPage.resetFilter(page);

      await carriersPage.filterTable(page, 'input', 'name', createCarrierData.name);

      const carrierName = await carriersPage.getTextColumn(page, 1, 'name');
      await expect(carrierName).to.contains(createCarrierData.name);
    });

    it('should delete carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCarrier', baseContext);

      const textResult = await carriersPage.deleteCarrier(page, 1);
      await expect(textResult).to.contains(carriersPage.successfulDeleteMessage);

      await carriersPage.resetFilter(page);
    });
  });

  // 8 - Go back to default value for price display method : Tax exclude
  describe('Choose Price display method: tax excluded', async () => {
    it('should go to \'Shop parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.customerSettingsLink,
      );

      await customerSettingsPage.closeSfToolBar(page);

      const pageTitle = await customerSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
    });

    it('should go to Groups page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage2', baseContext);

      await customerSettingsPage.goToGroupsPage(page);

      const pageTitle = await groupsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(groupsPage.pageTitle);
    });

    it(`should filter by '${groupAccess.customer.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByGroupName2', baseContext);

      await groupsPage.filterTable(page, 'input', 'b!name', groupAccess.customer.name);

      const textColumn = await groupsPage.getTextColumn(page, 1, 'b!name');
      await expect(textColumn).to.contains(groupAccess.customer.name);
    });

    it('should go to edit group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditGroupPage2', baseContext);

      await groupsPage.gotoEditGroupPage(page, 1);

      const pageTitle = await addGroupPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addGroupPage.pageTitleEdit);
    });

    it('should update group by choosing \'Tax included\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateGroup2', baseContext);

      const textResult = await addGroupPage.setPriceDisplayMethod(page, priceDisplayMethod[1]);
      await expect(textResult).to.contains(groupsPage.successfulUpdateMessage);
    });
  });
});
