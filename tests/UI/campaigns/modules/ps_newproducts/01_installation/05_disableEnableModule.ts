// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import FO pages
import {homePage} from '@pages/FO/classic/home';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boModuleManagerPage,
  dataModules,
  modPsNewProductsBoMain,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_newproducts_installation_disableEnableModule';

describe('New products block module - Disable/Enable module', async () => {
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
      await loginCommon.loginBO(this, page);
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

    it(`should search the module ${dataModules.psNewProducts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleForEnable', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psNewProducts);
      expect(isModuleVisible).to.eq(true);
    });

    it('should disable and cancel the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableCancelModule', baseContext);

      await boModuleManagerPage.setActionInModule(page, dataModules.psNewProducts, 'disable', true);

      const isModuleVisible = await boModuleManagerPage.isModuleVisible(page, dataModules.psNewProducts);
      expect(isModuleVisible).to.eq(true);
    });

    it('should disable the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psNewProducts, 'disable');
      expect(successMessage).to.eq(boModuleManagerPage.disableModuleSuccessMessage(dataModules.psNewProducts.tag));
    });

    it('should go to the front office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOAfterDisable', baseContext);

      page = await modPsNewProductsBoMain.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should check if the "New Products" block is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotVisible', baseContext);

      const hasProductsBlock = await homePage.hasProductsBlock(page, 'newproducts');
      expect(hasProductsBlock).to.eq(false);
    });

    it('should return to the back office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnBO', baseContext);

      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psNewProducts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleForDisable', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psNewProducts);
      expect(isModuleVisible).to.eq(true);
    });

    it('should enable the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psNewProducts, 'enable');
      expect(successMessage).to.eq(boModuleManagerPage.enableModuleSuccessMessage(dataModules.psNewProducts.tag));
    });

    it('should go to the front office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOAfterEnable', baseContext);

      page = await modPsNewProductsBoMain.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should check if the "New Products" block is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVisible', baseContext);

      const hasProductsBlock = await homePage.hasProductsBlock(page, 'newproducts');
      expect(hasProductsBlock).to.eq(true);
    });
  });
});
