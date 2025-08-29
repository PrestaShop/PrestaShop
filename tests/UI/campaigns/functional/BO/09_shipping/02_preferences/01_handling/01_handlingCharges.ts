import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCarriersPage,
  boCarriersCreatePage,
  boCustomerGroupsPage,
  boCustomerGroupsCreatePage,
  boCustomerSettingsPage,
  boDashboardPage,
  boLoginPage,
  boShippingPreferencesPage,
  type BrowserContext,
  dataCustomers,
  dataGroups,
  FakerCarrier,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const createCarrierPrice: number = 5;
  const createCarrierData: FakerCarrier = new FakerCarrier({
    freeShipping: false,
    handlingCosts: true,
    ranges: [
      {
        weightMin: 0,
        weightMax: 50,
        zones: [
          {
            zone: 'all',
            price: createCarrierPrice,
          },
        ],
      },
    ],
    // Size weight and group access
    maxWidth: 200,
    maxHeight: 200,
    maxDepth: 200,
    maxWeight: 500,
  });
  const priceDisplayMethod: string[] = ['Tax excluded', 'Tax included'];
  const defaultHandlingChargesValue: number = 2.00;
  const updateHandlingChargesValue: number = 4.00;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create image
    await utilsFile.generateImage(`${createCarrierData.name}.jpg`);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Delete image
    await utilsFile.deleteFile(`${createCarrierData.name}.jpg`);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  // 1 - Choose display tax excluded in FO
  describe('Choose Price display method: tax excluded', async () => {
    it('should go to \'Shop parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.customerSettingsLink,
      );
      await boCustomerSettingsPage.closeSfToolBar(page);

      const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
    });

    it('should go to Groups page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage1', baseContext);

      await boCustomerSettingsPage.goToGroupsPage(page);

      const pageTitle = await boCustomerGroupsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsPage.pageTitle);
    });

    it(`should filter by '${dataGroups.customer.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByGroupName1', baseContext);

      await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', dataGroups.customer.name);

      const textColumn = await boCustomerGroupsPage.getTextColumn(page, 1, 'b!name');
      expect(textColumn).to.contains(dataGroups.customer.name);
    });

    it('should go to edit group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditGroupPage1', baseContext);

      await boCustomerGroupsPage.gotoEditGroupPage(page, 1);

      const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleEdit);
    });

    it('should update group by choosing \'Tax excluded\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateGroup1', baseContext);

      const textResult = await boCustomerGroupsCreatePage.setPriceDisplayMethod(page, priceDisplayMethod[0]);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulUpdateMessage);
    });
  });

  // 2 - Create carrier and enable Add handling costs
  describe('Create new carrier and enable \'Add handling costs\'', async () => {
    it('should go to \'Shipping > Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.carriersLink,
      );

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });

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
    });

    it('should filter list by name and get the new carrier ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToCheckNewCarrier', baseContext);

      await boCarriersPage.resetFilter(page);

      await boCarriersPage.filterTable(page, 'input', 'name', createCarrierData.name);

      newCarrierID = parseInt(await boCarriersPage.getTextColumn(page, 1, 'id_carrier'), 10);

      const name = await boCarriersPage.getTextColumn(page, 1, 'name');
      expect(name).to.contains(createCarrierData.name);
    });
  });

  // 3 - Go to FO and check shipping cost for the new carrier
  describe(`Check shipping costs for the carrier '${createCarrierData.name}' in FO`, async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstViewMyShop1', baseContext);

      // Click on view my shop
      page = await boCarriersPage.viewMyShop(page);
      // Change language
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstGoToLoginPageFO1', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstSighInFO1', baseContext);

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

    it('should select the new carrier and check the chipping costs', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost1', baseContext);

      await foClassicCheckoutPage.chooseShippingMethodAndAddComment(page, newCarrierID);

      const shippingCost = await foClassicCheckoutPage.getShippingCost(page);
      expect(shippingCost).to.contains(defaultHandlingChargesValue + createCarrierPrice);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO1', baseContext);

      await foClassicCheckoutPage.goToHomePage(page);
      await foClassicCheckoutPage.logout(page);

      const isCustomerConnected = await foClassicCheckoutPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });
  });

  // 4 - Update handling charges value
  describe('Update handling charges', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

      page = await foClassicCheckoutPage.closePage(browserContext, page, 0);

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });

    it('should go to \'Shipping > Preferences\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.shippingLink, boDashboardPage.shippingPreferencesLink);

      const pageTitle = await boShippingPreferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShippingPreferencesPage.pageTitle);
    });

    it('should update \'Handling charges\' value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateHandlingCharges1', baseContext);

      const textResult = await boShippingPreferencesPage.setHandlingCharges(page, updateHandlingChargesValue.toString());
      expect(textResult).to.contain(boShippingPreferencesPage.successfulUpdateMessage);
    });
  });

  // 5 - Go to FO and check shipping cost for the created carrier
  describe(`Check shipping costs for the carrier '${createCarrierData.name}' in FO`, async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstViewMyShop2', baseContext);

      // Click on view my shop
      page = await boCarriersPage.viewMyShop(page);
      // Change language
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstGoToLoginPageFO2', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstSighInFO2', baseContext);

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

    it('should select the new carrier and check the chipping costs', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCost2', baseContext);

      await foClassicCheckoutPage.chooseShippingMethodAndAddComment(page, newCarrierID);

      const shippingCost = await foClassicCheckoutPage.getShippingCost(page);
      expect(shippingCost).to.contains(updateHandlingChargesValue + createCarrierPrice);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFo2', baseContext);

      await foClassicCheckoutPage.goToHomePage(page);
      await foClassicCheckoutPage.logout(page);

      const isCustomerConnected = await foClassicCheckoutPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });
  });

  // 6 - Go back to default handling cost value
  describe('Go back to default handling cost value', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      page = await foClassicCheckoutPage.closePage(browserContext, page, 0);

      const pageTitle = await boShippingPreferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShippingPreferencesPage.pageTitle);
    });

    it('should update \'Handling charges\' value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateHandlingCharges2', baseContext);

      const textResult = await boShippingPreferencesPage.setHandlingCharges(page, defaultHandlingChargesValue.toString());
      expect(textResult).to.contain(boShippingPreferencesPage.successfulUpdateMessage);
    });
  });

  // 7 - Delete the new carrier
  describe('Delete carrier', async () => {
    it('should go to \'Shipping > Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.carriersLink,
      );

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await boCarriersPage.resetFilter(page);
      await boCarriersPage.filterTable(page, 'input', 'name', createCarrierData.name);

      const carrierName = await boCarriersPage.getTextColumn(page, 1, 'name');
      expect(carrierName).to.contains(createCarrierData.name);
    });

    it('should delete carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCarrier', baseContext);

      const textResult = await boCarriersPage.deleteCarrier(page, 1);
      expect(textResult).to.contains(boCarriersPage.successfulDeleteMessage);

      await boCarriersPage.resetFilter(page);
    });
  });

  // 8 - Go back to default value for price display method : Tax exclude
  describe('Choose Price display method: tax excluded', async () => {
    it('should go to \'Shop parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.customerSettingsLink,
      );
      await boCustomerSettingsPage.closeSfToolBar(page);

      const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
    });

    it('should go to Groups page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage2', baseContext);

      await boCustomerSettingsPage.goToGroupsPage(page);

      const pageTitle = await boCustomerGroupsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsPage.pageTitle);
    });

    it(`should filter by '${dataGroups.customer.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByGroupName2', baseContext);

      await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', dataGroups.customer.name);

      const textColumn = await boCustomerGroupsPage.getTextColumn(page, 1, 'b!name');
      expect(textColumn).to.contains(dataGroups.customer.name);
    });

    it('should go to edit group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditGroupPage2', baseContext);

      await boCustomerGroupsPage.gotoEditGroupPage(page, 1);

      const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleEdit);
    });

    it('should update group by choosing \'Tax included\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateGroup2', baseContext);

      const textResult = await boCustomerGroupsCreatePage.setPriceDisplayMethod(page, priceDisplayMethod[1]);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulUpdateMessage);
    });
  });
});
