// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import boDesignPositionsPage from '@pages/BO/design/positions';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  dataModules,
  modPsNewProductsBoMain,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_newproducts_configuration_boHeaderButtons';

describe('New products block module - BO Header Buttons', async () => {
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

  describe('BO - Header Buttons', async () => {
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
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psNewProducts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, dataModules.psNewProducts);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psNewProducts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await moduleManagerPage.goToConfigurationPage(page, dataModules.psNewProducts.tag);

      const pageTitle = await modPsNewProductsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsNewProductsBoMain.pageSubTitle);
    });

    it('should click on the "Back" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnBackButton', baseContext);

      await modPsNewProductsBoMain.clickHeaderBack(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it('should return to the configure page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnConfigureAfterBack', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, dataModules.psNewProducts);
      expect(isModuleVisible).to.eq(true);

      await moduleManagerPage.goToConfigurationPage(page, dataModules.psNewProducts.tag);

      const pageTitle = await modPsNewProductsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsNewProductsBoMain.pageSubTitle);
    });

    it('should click on the "Translate" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnTranslateButton', baseContext);

      await modPsNewProductsBoMain.clickHeaderTranslate(page);

      const isModalVisible = await modPsNewProductsBoMain.isModalTranslateVisible(page);
      expect(isModalVisible).to.be.equal(true);
    });

    it('should close the "Translate" modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeTranslateModal', baseContext);

      await modPsNewProductsBoMain.closeTranslateModal(page);

      const isModalVisible = await modPsNewProductsBoMain.isModalTranslateVisible(page);
      expect(isModalVisible).to.be.equal(false);

      const pageTitle = await modPsNewProductsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsNewProductsBoMain.pageSubTitle);
    });

    it('should click on the "Manage hooks" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnManageHooksButton', baseContext);

      await modPsNewProductsBoMain.clickHeaderManageHooks(page);

      const pageTitle = await boDesignPositionsPage.getPageTitle(page);
      expect(pageTitle).to.be.equal(boDesignPositionsPage.pageTitle);

      const moduleFiltered = await boDesignPositionsPage.getModuleFilter(page);
      expect(moduleFiltered).to.be.equal(dataModules.psNewProducts.name);
    });
  });
});
