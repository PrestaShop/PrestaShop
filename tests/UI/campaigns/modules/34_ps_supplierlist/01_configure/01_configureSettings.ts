// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {disableModule, enableModule} from '@commonTests/BO/modules/moduleManager';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  boShopParametersPage,
  type BrowserContext,
  dataModules,
  foHummingbirdCategoryPage,
  foHummingbirdHomePage,
  modPsNewProductsBoMain,
  modPsSupplierListBoMain,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_supplierlist_configure_configureSettings';

describe('ps_supplierlist - Configure Settings', async () => {
  // PRE-TEST : Enable ps_supplierlist
  enableModule(dataModules.psSupplierList, `${baseContext}_preTest_0`);

  describe('Configure Settings', async () => {
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

    it('should go to \'Shop parameters > General\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGeneralPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.shopParametersGeneralLink,
      );
      await boShopParametersPage.closeSfToolBar(page);

      const pageTitle = await boShopParametersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
    });

    it('should enable display suppliers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableDisplaySuppliers', baseContext);

      const result = await boShopParametersPage.setDisplaySuppliers(page, true);
      expect(result).to.contains(boShopParametersPage.successfulUpdateMessage);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psSupplierList.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psSupplierList);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psSupplierList.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psSupplierList.tag);

      const pageTitle = await modPsSupplierListBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsSupplierListBoMain.pageSubTitle);
    });

    it('should set the type of display to "dropdown"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setTypeDisplayDropdown', baseContext);

      const result = await modPsSupplierListBoMain.setTypeOfDisplay(page, modPsSupplierListBoMain.typeOfDisplayDropdown);
      expect(result).to.contains(modPsNewProductsBoMain.updateSettingsSuccessMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await modPsNewProductsBoMain.viewMyShop(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProducts', baseContext);

      await foHummingbirdHomePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await foHummingbirdCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible).to.equal(true);

      const hasFiltersSuppliers = await foHummingbirdCategoryPage.hasFiltersSuppliers(page);
      expect(hasFiltersSuppliers).to.equal(true);

      const isSupplierListDropdown = await foHummingbirdCategoryPage.isSupplierListDropdown(page);
      expect(isSupplierListDropdown).to.equal(true);
    });

    it('should set the type of display to "plain-text"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setTypeDisplayPlaintext', baseContext);

      page = await foHummingbirdCategoryPage.changePage(browserContext, 0);

      const result = await modPsSupplierListBoMain.setTypeOfDisplay(page, modPsSupplierListBoMain.typeOfDisplayPlaintext);
      expect(result).to.contains(modPsNewProductsBoMain.updateSettingsSuccessMessage);
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToAllProducts', baseContext);

      page = await foHummingbirdCategoryPage.changePage(browserContext, 1);
      await foHummingbirdCategoryPage.reloadPage(page);

      const isCategoryPageVisible = await foHummingbirdCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible).to.equal(true);

      const hasFiltersSuppliers = await foHummingbirdCategoryPage.hasFiltersSuppliers(page);
      expect(hasFiltersSuppliers).to.equal(true);

      const isSupplierListDropdown = await foHummingbirdCategoryPage.isSupplierListDropdown(page);
      expect(isSupplierListDropdown).to.equal(false);
    });

    it('should go to \'Shop parameters > General\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToGeneralPage', baseContext);

      page = await foHummingbirdCategoryPage.changePage(browserContext, 0);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.shopParametersGeneralLink,
      );
      await boShopParametersPage.closeSfToolBar(page);

      const pageTitle = await boShopParametersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
    });

    it('should disable display suppliers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableDisplaySuppliers', baseContext);

      const result = await boShopParametersPage.setDisplaySuppliers(page, false);
      expect(result).to.contains(boShopParametersPage.successfulUpdateMessage);
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToAllProductsAfterDisable', baseContext);

      page = await foHummingbirdCategoryPage.changePage(browserContext, 1);
      await foHummingbirdCategoryPage.reloadPage(page);

      const isCategoryPageVisible = await foHummingbirdCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible).to.equal(true);

      const hasFiltersSuppliers = await foHummingbirdCategoryPage.hasFiltersSuppliers(page);
      expect(hasFiltersSuppliers).to.equal(false);
    });
  });

  // POST-TEST : Disable ps_supplierlist
  disableModule(dataModules.psSupplierList, `${baseContext}_postTest_0`);
});
