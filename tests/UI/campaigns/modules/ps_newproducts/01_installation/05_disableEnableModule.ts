// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import FO pages
import {homePage} from '@pages/FO/classic/home';
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
import psNewProducts from '@pages/BO/modules/psNewProducts';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_newproducts_installation_disableEnableModule';

describe('New products block module - Disable/Enable module', async () => {
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

    it(`should search the module ${Modules.psNewProducts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleForEnable', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psNewProducts);
      expect(isModuleVisible).to.eq(true);
    });

    it('should disable and cancel the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableCancelModule', baseContext);

      await moduleManagerPage.setActionInModule(page, Modules.psNewProducts, 'disable', true);

      const isModuleVisible = await moduleManagerPage.isModuleVisible(page, Modules.psNewProducts);
      expect(isModuleVisible).to.eq(true);
    });

    it('should disable the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psNewProducts, 'disable');
      expect(successMessage).to.eq(moduleManagerPage.disableModuleSuccessMessage(Modules.psNewProducts.tag));
    });

    it('should go to the front office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOAfterDisable', baseContext);

      page = await psNewProducts.viewMyShop(page);
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

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module ${Modules.psNewProducts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleForDisable', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psNewProducts);
      expect(isModuleVisible).to.eq(true);
    });

    it('should enable the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psNewProducts, 'enable');
      expect(successMessage).to.eq(moduleManagerPage.enableModuleSuccessMessage(Modules.psNewProducts.tag));
    });

    it('should go to the front office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOAfterEnable', baseContext);

      page = await psNewProducts.viewMyShop(page);
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
