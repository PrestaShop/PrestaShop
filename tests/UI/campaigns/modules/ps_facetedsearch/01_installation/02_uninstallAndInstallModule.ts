// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import FO pages
import {productPage as foProductPage} from '@pages/FO/classic/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boModuleManagerPage,
  dataCategories,
  dataModules,
  foClassicCategoryPage,
  foClassicHomePage,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_facetedsearch_installation_uninstallAndInstallModule';

describe('Faceted search module - Uninstall and install module', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    await utilsFile.deleteFile('module.zip');
  });

  describe('BackOffice - Uninstall Module', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
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

    it(`should search the module ${dataModules.psFacetedSearch.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psFacetedSearch);
      expect(isModuleVisible).to.eq(true);
    });

    it('should display the uninstall modal and cancel it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModuleAndCancel', baseContext);

      const textResult = await boModuleManagerPage.setActionInModule(page, dataModules.psFacetedSearch, 'uninstall', true);
      expect(textResult).to.eq('');

      const isModuleVisible = await boModuleManagerPage.isModuleVisible(page, dataModules.psFacetedSearch);
      expect(isModuleVisible).to.eq(true);

      const isModalVisible = await boModuleManagerPage.isModalActionVisible(page, dataModules.psFacetedSearch, 'uninstall');
      expect(isModalVisible).to.eq(false);

      const dirExists = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/modules/${dataModules.psFacetedSearch.tag}/`);
      expect(dirExists).to.eq(true);
    });

    it('should uninstall the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psFacetedSearch, 'uninstall', false);
      expect(successMessage).to.eq(boModuleManagerPage.uninstallModuleSuccessMessage(dataModules.psFacetedSearch.tag));

      // Check the directory `modules/dataModules.psFacetedSearch.tag`
      const dirExists = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/modules/${dataModules.psFacetedSearch.tag}/`);
      expect(dirExists).to.eq(true);
    });
  });

  describe('FrontOffice - Check that the module is not present', async () => {
    it('should go to Front Office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoAfterDisable', baseContext);

      page = await boModuleManagerPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the category Page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoryPageAfterDisable', baseContext);

      await foClassicHomePage.goToCategory(page, dataCategories.clothes.id);

      const pageTitle = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.equal(dataCategories.clothes.name);
    });

    it(`should check that ${dataModules.psFacetedSearch.name} is not present`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkModuleNotPresent', baseContext);

      const hasFilters = await foClassicCategoryPage.hasSearchFilters(page);
      expect(hasFilters).to.eq(false);
    });
  });

  describe('BackOffice - Install the module', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);
      await boModuleManagerPage.reloadPage(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it('should install the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'installModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psFacetedSearch, 'install', false);
      expect(successMessage).to.eq(boModuleManagerPage.installModuleSuccessMessage(dataModules.psFacetedSearch.tag));

      // Check the directory `modules/dataModules.psFacetedSearch.tag`
      const dirExists = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/modules/${dataModules.psFacetedSearch.tag}/`);
      expect(dirExists).to.eq(true);
    });
  });

  describe('FrontOffice - Check that the module is present', async () => {
    it('should go to Front Office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoAfterEnable', baseContext);

      page = await boModuleManagerPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the category Page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoryPageAfterEnable', baseContext);

      await foClassicHomePage.goToCategory(page, dataCategories.clothes.id);

      const pageTitle = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.equal(dataCategories.clothes.name);
    });

    it(`should check that ${dataModules.psFacetedSearch.name} is not present`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkModulePresent', baseContext);

      const hasFilters = await foClassicCategoryPage.hasSearchFilters(page);
      expect(hasFilters).to.eq(true);
    });
  });
});
