// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataCategories,
  dataModules,
  foClassicCategoryPage,
  foClassicHomePage,
  modPsFacetedsearchBoMain,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_facetedsearch_installation_disableEnableModule';

describe('Faceted search module - Disable/Enable module', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Disable/Enable module', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPageForEnable', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleForDisable', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psFacetedSearch);
      expect(isModuleVisible).to.eq(true);
    });

    it('should disable and cancel the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableCancelModule', baseContext);

      await boModuleManagerPage.setActionInModule(page, dataModules.psFacetedSearch, 'disable', true);

      const isModuleVisible = await boModuleManagerPage.isModuleVisible(page, dataModules.psFacetedSearch);
      expect(isModuleVisible).to.eq(true);
    });

    it('should disable the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psFacetedSearch, 'disable');
      expect(successMessage).to.eq(boModuleManagerPage.disableModuleSuccessMessage(dataModules.psFacetedSearch.tag));
    });

    it('should go to the front office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOAfterDisable', baseContext);

      page = await modPsFacetedsearchBoMain.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the category Page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoryPage', baseContext);

      await foClassicHomePage.goToCategory(page, dataCategories.clothes.id);

      const pageTitle = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.equal(dataCategories.clothes.name);
    });

    it(`should check that ${dataModules.psFacetedSearch.name} is not present`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkModuleNotPresent', baseContext);

      const hasFilters = await foClassicCategoryPage.hasSearchFilters(page);
      expect(hasFilters).to.eq(false);
    });

    it('should return to the back office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnBO', baseContext);

      page = await foClassicCategoryPage.closePage(browserContext, page, 0);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psFacetedSearch.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleForEnsable', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psFacetedSearch);
      expect(isModuleVisible).to.eq(true);
    });

    it('should enable the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psFacetedSearch, 'enable');
      expect(successMessage).to.eq(boModuleManagerPage.enableModuleSuccessMessage(dataModules.psFacetedSearch.tag));
    });

    it('should go to the front office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOAfterEnable', baseContext);

      page = await modPsFacetedsearchBoMain.viewMyShop(page);
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

    it(`should check that ${dataModules.psFacetedSearch.name} is present`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkModulePresent', baseContext);

      const hasFilters = await foClassicCategoryPage.hasSearchFilters(page);
      expect(hasFilters).to.eq(true);
    });
  });
});
