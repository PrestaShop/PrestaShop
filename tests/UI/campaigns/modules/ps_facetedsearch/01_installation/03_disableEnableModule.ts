// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {categoryPage} from '@pages/FO/classic/category';
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
import psFacetedSearch from '@pages/BO/modules/psFacetedSearch';

// Import data
import Categories from '@data/demo/categories';
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_facetedsearch_installation_disableEnableModule';

describe('Faceted search module - Disable/Enable module', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Disable/Enable module', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPageForEnable', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module ${Modules.psFacetedSearch.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleForDisable', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psFacetedSearch);
      expect(isModuleVisible).to.eq(true);
    });

    it('should disable and cancel the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableCancelModule', baseContext);

      await moduleManagerPage.setActionInModule(page, Modules.psFacetedSearch, 'disable', true);

      const isModuleVisible = await moduleManagerPage.isModuleVisible(page, Modules.psFacetedSearch);
      expect(isModuleVisible).to.eq(true);
    });

    it('should disable the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psFacetedSearch, 'disable');
      expect(successMessage).to.eq(moduleManagerPage.disableModuleSuccessMessage(Modules.psFacetedSearch.tag));
    });

    it('should go to the front office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOAfterDisable', baseContext);

      page = await psFacetedSearch.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the category Page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoryPage', baseContext);

      await homePage.goToCategory(page, Categories.clothes.id);

      const pageTitle = await homePage.getPageTitle(page);
      expect(pageTitle).to.equal(Categories.clothes.name);
    });

    it(`should check that ${Modules.psFacetedSearch.name} is not present`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkModuleNotPresent', baseContext);

      const hasFilters = await categoryPage.hasSearchFilters(page);
      expect(hasFilters).to.eq(false);
    });

    it('should return to the back office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnBO', baseContext);

      page = await categoryPage.closePage(browserContext, page, 0);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module ${Modules.psFacetedSearch.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleForEnsable', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psFacetedSearch);
      expect(isModuleVisible).to.eq(true);
    });

    it('should enable the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psFacetedSearch, 'enable');
      expect(successMessage).to.eq(moduleManagerPage.enableModuleSuccessMessage(Modules.psFacetedSearch.tag));
    });

    it('should go to the front office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOAfterEnable', baseContext);

      page = await psFacetedSearch.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the category Page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoryPageAfterEnable', baseContext);

      await homePage.goToCategory(page, Categories.clothes.id);

      const pageTitle = await homePage.getPageTitle(page);
      expect(pageTitle).to.equal(Categories.clothes.name);
    });

    it(`should check that ${Modules.psFacetedSearch.name} is present`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkModulePresent', baseContext);

      const hasFilters = await categoryPage.hasSearchFilters(page);
      expect(hasFilters).to.eq(true);
    });
  });
});
