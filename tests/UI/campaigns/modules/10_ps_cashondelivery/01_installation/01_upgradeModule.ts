import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {installModule, uninstallModule} from '@commonTests/BO/modules/moduleManager';

import {
  boDashboardPage,
  boLoginPage,
  boMaintenancePage,
  boModuleManagerPage,
  boShopParametersPage,
  type BrowserContext,
  dataCustomers,
  dataModules,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicModalBlockCartPage,
  foClassicModalQuickViewPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_cashondelivery_installation_upgradeModule';

describe('Cash on delivery (COD) module: Upgrade module', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // PRE-TEST : Uninstall ps_cashondelivery
  uninstallModule(dataModules.psCashOnDelivery, `${baseContext}_preTest_0`);
  // PRE-TEST : Install ps_cashondelivery (old version)
  installModule(dataModules.psCashOnDelivery, false, `${baseContext}_preTest_1`);

  describe('Upgrade with shop on maintenance', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
      await utilsFile.deleteFile('module.zip');
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Shop parameters > General\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopParamsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.shopParametersGeneralLink,
      );
      await boShopParametersPage.closeSfToolBar(page);

      const pageTitle = await boShopParametersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
    });

    it('should go to \'Maintenance\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMaintenancePage', baseContext);

      await boShopParametersPage.goToSubTabMaintenance(page);

      const pageTitle = await boMaintenancePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMaintenancePage.pageTitle);
    });

    it('should disable the shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableShop', baseContext);

      const resultStatus = await boMaintenancePage.changeShopStatus(page, false);
      expect(resultStatus).to.contains(boMaintenancePage.successfulUpdateMessage);

      const resultLoggedInEmployees = await boMaintenancePage.changeStoreForLoggedInEmployees(page, false);
      expect(resultLoggedInEmployees).to.contains(boMaintenancePage.successfulUpdateMessage);
    });

    it('should verify the existence of the maintenance text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'verifyMaintenanceText', baseContext);

      page = await boMaintenancePage.viewMyShop(page);

      const pageContent = await foClassicHomePage.getTextContent(page, foClassicHomePage.content);
      expect(pageContent).to.equal(boMaintenancePage.maintenanceText);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      // Go back to BO
      page = await foClassicHomePage.closePage(browserContext, page, 0);
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psCashOnDelivery.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psCashOnDelivery);
      expect(isModuleVisible).to.eq(true);

      const moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
      expect(dataModules.psCashOnDelivery.versionOld).to.contains(moduleInfo.version);
    });

    it('should display the upgrade modal and cancel it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetModuleAndCancel', baseContext);

      const textResult = await boModuleManagerPage.setActionInModule(page, dataModules.psCashOnDelivery, 'upgrade', true);
      expect(textResult).to.eq('');

      const isModuleVisible = await boModuleManagerPage.isModuleVisible(page, dataModules.psCashOnDelivery);
      expect(isModuleVisible).to.eq(true);

      const isModalVisible = await boModuleManagerPage.isModalActionVisible(page, dataModules.psCashOnDelivery, 'upgrade');
      expect(isModalVisible).to.eq(false);
    });

    it('should upgrade the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(
        page,
        dataModules.psCashOnDelivery,
        'upgrade',
        false,
        true,
      );
      expect(successMessage).to.eq(boModuleManagerPage.updateModuleSuccessMessage(dataModules.psCashOnDelivery.tag));
    });

    it('should reload the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkModule', baseContext);

      await boModuleManagerPage.reloadPage(page);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psCashOnDelivery);
      expect(isModuleVisible).to.eq(true);

      const moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
      expect(dataModules.psCashOnDelivery.versionCurrent).to.contains(moduleInfo.version);
    });

    it('should go to \'Shop parameters > General\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToShopParamsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.shopParametersGeneralLink,
      );
      await boShopParametersPage.closeSfToolBar(page);

      const pageTitle = await boShopParametersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
    });

    it('should go to \'Maintenance\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToMaintenancePage', baseContext);

      await boShopParametersPage.goToSubTabMaintenance(page);

      const pageTitle = await boMaintenancePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMaintenancePage.pageTitle);
    });

    it('should enable the shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableShop', baseContext);

      const result = await boMaintenancePage.changeShopStatus(page, true);
      expect(result).to.contains(boMaintenancePage.successfulUpdateMessage);
    });

    it('should go to the front office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTheFo', baseContext);

      page = await boModuleManagerPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should add the first product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicLoginPage.goToHomePage(page);

      // Add first product to cart by quick view
      await foClassicHomePage.quickViewProduct(page, 1);
      await foClassicModalQuickViewPage.addToCartByQuickView(page);
      await foClassicModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.eq(foClassicCartPage.pageTitle);
    });

    it('should proceed to checkout and check Step Address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddressStep', baseContext);

      await foClassicCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);

      const isStepPersonalInformationComplete = await foClassicCheckoutPage.isStepCompleted(
        page,
        foClassicCheckoutPage.personalInformationStepForm,
      );
      expect(isStepPersonalInformationComplete).to.eq(true);
    });

    it('should validate Step Address and go to Delivery Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryStep', baseContext);

      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete).to.eq(true);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it(`should check the '${dataModules.psCashOnDelivery.name}' payment module`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentModule', baseContext);

      // Payment step - Choose payment step
      const isVisible = await foClassicCheckoutPage.isPaymentMethodExist(page, dataModules.psCashOnDelivery.tag);
      expect(isVisible).to.eq(true);
    });
  });

  // PRE-TEST : Uninstall ps_cashondelivery
  uninstallModule(dataModules.psCashOnDelivery, `${baseContext}_middleTest_0`);
  // PRE-TEST : Install ps_cashondelivery (old version)
  installModule(dataModules.psCashOnDelivery, false, `${baseContext}_middleTest_1`);

  describe('Upgrade without shop on maintenance', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
      await utilsFile.deleteFile('module.zip');
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPageWoMaintenance', baseContext);

      // Go back to BO
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psCashOnDelivery.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleWoMaintenance', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psCashOnDelivery);
      expect(isModuleVisible).to.eq(true);

      const moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
      expect(dataModules.psCashOnDelivery.versionOld).to.contains(moduleInfo.version);
    });

    it('should upgrade the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'upgradeModuleWoMaintenance', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(
        page,
        dataModules.psCashOnDelivery,
        'upgrade',
        false,
        true,
      );
      expect(successMessage).to.eq(boModuleManagerPage.updateModuleSuccessMessage(dataModules.psCashOnDelivery.tag));
    });

    it('should reload the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkModuleWoMaintenance', baseContext);

      await boModuleManagerPage.reloadPage(page);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psCashOnDelivery);
      expect(isModuleVisible).to.eq(true);

      const moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
      expect(dataModules.psCashOnDelivery.versionCurrent).to.contains(moduleInfo.version);
    });

    it('should go to the front office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTheFoWoMaintenance', baseContext);

      page = await boModuleManagerPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFOWoMaintenance', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFOWoMaintenance', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should add the first product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCartWoMaintenance', baseContext);

      await foClassicLoginPage.goToHomePage(page);

      // Add first product to cart by quick view
      await foClassicHomePage.quickViewProduct(page, 1);
      await foClassicModalQuickViewPage.addToCartByQuickView(page);
      await foClassicModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.eq(foClassicCartPage.pageTitle);
    });

    it('should proceed to checkout and check Step Address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddressStepWoMaintenance', baseContext);

      await foClassicCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);

      const isStepPersonalInformationComplete = await foClassicCheckoutPage.isStepCompleted(
        page,
        foClassicCheckoutPage.personalInformationStepForm,
      );
      expect(isStepPersonalInformationComplete).to.eq(true);
    });

    it('should validate Step Address and go to Delivery Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryStepWoMaintenance', baseContext);

      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete).to.eq(true);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStepWoMaintenance', baseContext);

      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it(`should check the '${dataModules.psCashOnDelivery.name}' payment module`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentModuleWoMaintenance', baseContext);

      // Payment step - Choose payment step
      const isVisible = await foClassicCheckoutPage.isPaymentMethodExist(page, dataModules.psCashOnDelivery.tag);
      expect(isVisible).to.eq(true);
    });
  });
});
