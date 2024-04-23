// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
// Import FO pages
import {categoryPage} from '@pages/FO/classic/category';
import {homePage} from '@pages/FO/classic/home';

// Import data
import Categories from '@data/demo/categories';
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_facetedsearch_installation_uninstallAndDeleteModule';

describe('Faceted search module - Uninstall and delete module', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile('module.zip');
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Modules > Module Manager\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

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
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psFacetedSearch);
    expect(isModuleVisible).to.eq(true);
  });

  it('should display the uninstall modal and cancel it', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModuleAndCancel', baseContext);

    const textResult = await moduleManagerPage.setActionInModule(page, Modules.psFacetedSearch, 'uninstall', true);
    expect(textResult).to.eq('');

    const isModuleVisible = await moduleManagerPage.isModuleVisible(page, Modules.psFacetedSearch);
    expect(isModuleVisible).to.eq(true);

    const isModalVisible = await moduleManagerPage.isModalActionVisible(page, Modules.psFacetedSearch, 'uninstall');
    expect(isModalVisible).to.eq(false);

    const dirExists = await files.doesFileExist(`${files.getRootPath()}/modules/${Modules.psFacetedSearch.tag}/`);
    expect(dirExists).to.eq(true);
  });

  it('should uninstall the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

    const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psFacetedSearch, 'uninstall', false, true);
    expect(successMessage).to.eq(moduleManagerPage.uninstallModuleSuccessMessage(Modules.psFacetedSearch.tag));

    // Check the directory `modules/Modules.psFacetedSearch.tag`
    const dirExists = await files.doesFileExist(`${files.getRootPath()}/modules/${Modules.psFacetedSearch.tag}/`);
    expect(dirExists).to.eq(false);
  });

  it('should go to Front Office', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    page = await moduleManagerPage.viewMyShop(page);
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

  describe(`POST-CONDITION : Install the module ${Modules.psFacetedSearch.name}`, async () => {
    it('should go back to Back Office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToBo', baseContext);

      page = await categoryPage.closePage(browserContext, page, 0);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should download the zip of the module '${Modules.psFacetedSearch.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadModule', baseContext);

      await files.downloadFile(Modules.psFacetedSearch.releaseZip, 'module.zip');

      const found = await files.doesFileExist('module.zip');
      expect(found).to.eq(true);
    });

    it(`should upload the module '${Modules.psFacetedSearch.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uploadModule', baseContext);

      const successMessage = await moduleManagerPage.uploadModule(page, 'module.zip');
      expect(successMessage).to.eq(moduleManagerPage.uploadModuleSuccessMessage);
    });

    it('should close upload module modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

      const isModalNotVisible = await moduleManagerPage.closeUploadModuleModal(page);
      expect(isModalNotVisible).to.eq(true);
    });

    it(`should search the module '${Modules.psFacetedSearch.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkModulePresent', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psFacetedSearch);
      expect(isModuleVisible, 'Module is not visible!').to.eq(true);
    });
  });
});
