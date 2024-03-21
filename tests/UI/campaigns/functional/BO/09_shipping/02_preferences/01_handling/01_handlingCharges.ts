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
import preferencesPage from '@pages/BO/shipping/preferences';
import customerSettingsPage from '@pages/BO/shopParameters/customerSettings';
import groupsPage from '@pages/BO/shopParameters/customerSettings/groups';
import addGroupPage from '@pages/BO/shopParameters/customerSettings/groups/add';
// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {homePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {productPage} from '@pages/FO/classic/product';

import {
  // Import data
  dataCustomers,
  dataGroups,
  FakerCarrier,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shipping_preferences_handling_handlingCharges';

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
  let browserContext: BrowserContext;
  let page: Page;
  let newCarrierID: number = 0;

  const createCarrierData: FakerCarrier = new FakerCarrier({
    freeShipping: false,
    allZones: true,
    handlingCosts: true,
    allZonesValue: 5.00,
    rangeSup: 50,
  });
  const priceDisplayMethod: string[] = ['Tax excluded', 'Tax included'];
  const defaultHandlingChargesValue: number = 2.00;
  const updateHandlingChargesValue: number = 4.00;

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
      expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
    });

    it('should go to Groups page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage1', baseContext);

      await customerSettingsPage.goToGroupsPage(page);

      const pageTitle = await groupsPage.getPageTitle(page);
      expect(pageTitle).to.contains(groupsPage.pageTitle);
    });

    it(`should filter by '${dataGroups.customer.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByGroupName1', baseContext);

      await groupsPage.filterTable(page, 'input', 'b!name', dataGroups.customer.name);

      const textColumn = await groupsPage.getTextColumn(page, 1, 'b!name');
      expect(textColumn).to.contains(dataGroups.customer.name);
    });

    it('should go to edit group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditGroupPage1', baseContext);

      await groupsPage.gotoEditGroupPage(page, 1);

      const pageTitle = await addGroupPage.getPageTitle(page);
      expect(pageTitle).to.contains(addGroupPage.pageTitleEdit);
    });

    it('should update group by choosing \'Tax excluded\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateGroup1', baseContext);

      const textResult = await addGroupPage.setPriceDisplayMethod(page, priceDisplayMethod[0]);
      expect(textResult).to.contains(groupsPage.successfulUpdateMessage);
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
      expect(pageTitle).to.contains(carriersPage.pageTitle);
    });

    it('should go to add new carrier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddCarrierPage', baseContext);

      await carriersPage.goToAddNewCarrierPage(page);

      const pageTitle = await addCarrierPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCarrierPage.pageTitleCreate);
    });

    it('should create carrier and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCarrier', baseContext);

      const textResult = await addCarrierPage.createEditCarrier(page, createCarrierData);
      expect(textResult).to.contains(carriersPage.successfulCreationMessage);
    });

    it('should filter list by name and get the new carrier ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToCheckNewCarrier', baseContext);

      await carriersPage.resetFilter(page);

      await carriersPage.filterTable(page, 'input', 'name', createCarrierData.name);

      newCarrierID = parseInt(await carriersPage.getTextColumn(page, 1, 'id_carrier'), 10);

      const name = await carriersPage.getTextColumn(page, 1, 'name');
      expect(name).to.contains(createCarrierData.name);
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
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstGoToLoginPageFO1', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstSighInFO1', baseContext);

      await foLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
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
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
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
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });
  });

  // 4 - Update handling charges value
  describe('Update handling charges', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await carriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(carriersPage.pageTitle);
    });

    it('should go to \'Shipping > Preferences\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.shippingLink, dashboardPage.shippingPreferencesLink);

      const pageTitle = await preferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(preferencesPage.pageTitle);
    });

    it('should update \'Handling charges\' value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateHandlingCharges1', baseContext);

      const textResult = await preferencesPage.setHandlingCharges(page, updateHandlingChargesValue.toString());
      expect(textResult).to.contain(preferencesPage.successfulUpdateMessage);
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
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstGoToLoginPageFO2', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstSighInFO2', baseContext);

      await foLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
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
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
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
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });
  });

  // 6 - Go back to default handling cost value
  describe('Go back to default handling cost value', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await preferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(preferencesPage.pageTitle);
    });

    it('should update \'Handling charges\' value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateHandlingCharges2', baseContext);

      const textResult = await preferencesPage.setHandlingCharges(page, defaultHandlingChargesValue.toString());
      expect(textResult).to.contain(preferencesPage.successfulUpdateMessage);
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
      expect(pageTitle).to.contains(carriersPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await carriersPage.resetFilter(page);
      await carriersPage.filterTable(page, 'input', 'name', createCarrierData.name);

      const carrierName = await carriersPage.getTextColumn(page, 1, 'name');
      expect(carrierName).to.contains(createCarrierData.name);
    });

    it('should delete carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCarrier', baseContext);

      const textResult = await carriersPage.deleteCarrier(page, 1);
      expect(textResult).to.contains(carriersPage.successfulDeleteMessage);

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
      expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
    });

    it('should go to Groups page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage2', baseContext);

      await customerSettingsPage.goToGroupsPage(page);

      const pageTitle = await groupsPage.getPageTitle(page);
      expect(pageTitle).to.contains(groupsPage.pageTitle);
    });

    it(`should filter by '${dataGroups.customer.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByGroupName2', baseContext);

      await groupsPage.filterTable(page, 'input', 'b!name', dataGroups.customer.name);

      const textColumn = await groupsPage.getTextColumn(page, 1, 'b!name');
      expect(textColumn).to.contains(dataGroups.customer.name);
    });

    it('should go to edit group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditGroupPage2', baseContext);

      await groupsPage.gotoEditGroupPage(page, 1);

      const pageTitle = await addGroupPage.getPageTitle(page);
      expect(pageTitle).to.contains(addGroupPage.pageTitleEdit);
    });

    it('should update group by choosing \'Tax included\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateGroup2', baseContext);

      const textResult = await addGroupPage.setPriceDisplayMethod(page, priceDisplayMethod[1]);
      expect(textResult).to.contains(groupsPage.successfulUpdateMessage);
    });
  });
});
