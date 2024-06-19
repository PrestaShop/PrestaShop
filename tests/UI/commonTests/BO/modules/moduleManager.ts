import testContext from '@utils/testContext';

import loginCommon from '@commonTests/BO/loginBO';

import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

import {
  boDashboardPage,
  type FakerModule,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

function installModule(module: FakerModule, baseContext: string = 'commonTests-installModule'): void {
  describe(`Install module ${module.name}`, async () => {
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

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it(`should download the zip of the module '${module.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadModule', baseContext);

      await utilsFile.downloadFile(module.releaseZip, 'module.zip');

      const found = await utilsFile.doesFileExist('module.zip');
      expect(found).to.eq(true);
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

    it(`should upload the module '${module.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uploadModule', baseContext);

      const successMessage = await moduleManagerPage.uploadModule(page, 'module.zip');
      expect(successMessage).to.eq(moduleManagerPage.uploadModuleSuccessMessage);
    });

    it('should close upload module modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

      const isModalVisible = await moduleManagerPage.closeUploadModuleModal(page);
      expect(isModalVisible).to.eq(true);
    });

    it(`should search the module '${module.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, module);
      expect(isModuleVisible, 'Module is not visible!').to.eq(true);
    });
  });
}

function uninstallModule(module: FakerModule, baseContext: string = 'commonTests-uninstallModule'): void {
  describe(`Uninstall module ${module.name}`, async () => {
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

    it(`should search the module '${module.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, module);
      expect(isModuleVisible, 'Module is not visible!').to.eq(true);
    });

    it(`should uninstall the module '${module.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, module, 'uninstall', false, true);
      expect(successMessage).to.eq(moduleManagerPage.uninstallModuleSuccessMessage(module.tag));
    });
  });
}

function resetModule(module: FakerModule, baseContext: string = 'commonTests-resetModule'): void {
  describe(`Reset module ${module.name}`, async () => {
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

    it(`should search the module '${module.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, module);
      expect(isModuleVisible, 'Module is not visible!').to.eq(true);
    });

    it(`should reset the module '${module.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, module, 'reset');
      expect(successMessage).to.eq(moduleManagerPage.resetModuleSuccessMessage(module.tag));
    });
  });
}

export {
  installModule,
  uninstallModule,
  resetModule,
};
