// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import { installModule, uninstallModule } from '@commonTests/BO/modules/moduleManager';
// Import BO pages
import generalPage from '@pages/BO/shopParameters/general';
import maintenancePage from '@pages/BO/shopParameters/general/maintenance';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boModuleManagerPage,
  dataModules,
  foClassicHomePage,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_newproducts_installation_upgradeModule';

describe('New products block module: Upgrade module', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // PRE-TEST : Uninstall ps_newproducts
  uninstallModule(dataModules.psNewProducts, `${baseContext}_preTest_0`);
  // PRE-TEST : Install ps_newproducts (old version)
  installModule(dataModules.psNewProducts, false, `${baseContext}_preTest_1`);

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
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Shop parameters > General\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopParamsPage', baseContext);
  
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.shopParametersGeneralLink,
      );
      await generalPage.closeSfToolBar(page);
  
      const pageTitle = await generalPage.getPageTitle(page);
      expect(pageTitle).to.contains(generalPage.pageTitle);
    });
  
    it('should go to \'Maintenance\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMaintenancePage', baseContext);
  
      await generalPage.goToSubTabMaintenance(page);
  
      const pageTitle = await maintenancePage.getPageTitle(page);
      expect(pageTitle).to.contains(maintenancePage.pageTitle);
    });
  
    it('should disable the shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableShop', baseContext);
  
      const resultStatus = await maintenancePage.changeShopStatus(page, false);
      expect(resultStatus).to.contains(maintenancePage.successfulUpdateMessage);
  
      const resultLoggedInEmployees = await maintenancePage.changeStoreForLoggedInEmployees(page, false);
      expect(resultLoggedInEmployees).to.contains(maintenancePage.successfulUpdateMessage);
    });

    it('should verify the existence of the maintenance text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'verifyMaintenanceText', baseContext);
  
      page = await maintenancePage.viewMyShop(page);
  
      const pageContent = await foClassicHomePage.getTextContent(page, foClassicHomePage.content);
      expect(pageContent).to.equal(maintenancePage.maintenanceText);
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

    it(`should search the module ${dataModules.psNewProducts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psNewProducts);
      expect(isModuleVisible).to.eq(true);

      const moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
      expect(dataModules.psNewProducts.versionOld).to.contains(moduleInfo.version);
    });

    it('should display the upgrade modal and cancel it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetModuleAndCancel', baseContext);

      const textResult = await boModuleManagerPage.setActionInModule(page, dataModules.psNewProducts, 'upgrade', true);
      expect(textResult).to.eq('');

      const isModuleVisible = await boModuleManagerPage.isModuleVisible(page, dataModules.psNewProducts);
      expect(isModuleVisible).to.eq(true);

      const isModalVisible = await boModuleManagerPage.isModalActionVisible(page, dataModules.psNewProducts, 'upgrade');
      expect(isModalVisible).to.eq(false);
    });

    it('should upgrade the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psNewProducts, 'upgrade', false, true);
      expect(successMessage).to.eq(boModuleManagerPage.updateModuleSuccessMessage(dataModules.psNewProducts.tag));
    });

    it('should reload the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

      await boModuleManagerPage.reloadPage(page);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psNewProducts);
      expect(isModuleVisible).to.eq(true);

      const moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
      expect(dataModules.psNewProducts.versionCurrent).to.contains(moduleInfo.version);
    });

    it('should go to \'Shop parameters > General\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopParamsPage', baseContext);
  
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.shopParametersGeneralLink,
      );
      await generalPage.closeSfToolBar(page);
  
      const pageTitle = await generalPage.getPageTitle(page);
      expect(pageTitle).to.contains(generalPage.pageTitle);
    });
  
    it('should go to \'Maintenance\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMaintenancePage', baseContext);
  
      await generalPage.goToSubTabMaintenance(page);
  
      const pageTitle = await maintenancePage.getPageTitle(page);
      expect(pageTitle).to.contains(maintenancePage.pageTitle);
    });
  
    it('should enable the shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableShop', baseContext);
  
      const result = await maintenancePage.changeShopStatus(page, true);
      expect(result).to.contains(maintenancePage.successfulUpdateMessage);
    });

    it('should go to the front office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTheFo', baseContext);

      page = await boModuleManagerPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should check if the "New Products" block is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVisible', baseContext);

      const hasProductsBlock = await foClassicHomePage.hasProductsBlock(page, 'newproducts');
      expect(hasProductsBlock).to.eq(true);
    });
  });

  // PRE-TEST : Uninstall ps_newproducts
  uninstallModule(dataModules.psNewProducts, `${baseContext}_preTest_0`);
  // PRE-TEST : Install ps_newproducts (old version)
  installModule(dataModules.psNewProducts, false, `${baseContext}_preTest_1`);

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
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

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

    it(`should search the module ${dataModules.psNewProducts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psNewProducts);
      expect(isModuleVisible).to.eq(true);

      const moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
      expect(dataModules.psNewProducts.versionOld).to.contains(moduleInfo.version);
    });

    it('should upgrade the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psNewProducts, 'upgrade', false, true);
      expect(successMessage).to.eq(boModuleManagerPage.updateModuleSuccessMessage(dataModules.psNewProducts.tag));
    });

    it('should reload the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

      await boModuleManagerPage.reloadPage(page);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psNewProducts);
      expect(isModuleVisible).to.eq(true);

      const moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
      expect(dataModules.psNewProducts.versionCurrent).to.contains(moduleInfo.version);
    });

    it('should go to the front office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTheFo', baseContext);

      page = await boModuleManagerPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should check if the "New Products" block is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVisible', baseContext);

      const hasProductsBlock = await foClassicHomePage.hasProductsBlock(page, 'newproducts');
      expect(hasProductsBlock).to.eq(true);
    });
  });
});
