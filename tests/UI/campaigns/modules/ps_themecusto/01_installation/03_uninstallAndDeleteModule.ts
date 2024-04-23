// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import themeAndLogoPage from '@pages/BO/design/themeAndLogo/themeAndLogo';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_themecusto_installation_uninstallAndDeleteModule';

describe('Theme Customization module - Uninstall and delete module', async () => {
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

  it(`should search the module ${Modules.psThemeCusto.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psThemeCusto);
    expect(isModuleVisible).to.eq(true);
  });

  it('should display the uninstall modal and cancel it', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModuleAndCancel', baseContext);

    const textResult = await moduleManagerPage.setActionInModule(page, Modules.psThemeCusto, 'uninstall', true);
    expect(textResult).to.eq('');

    const isModuleVisible = await moduleManagerPage.isModuleVisible(page, Modules.psThemeCusto);
    expect(isModuleVisible).to.eq(true);

    const isModalVisible = await moduleManagerPage.isModalActionVisible(page, Modules.psThemeCusto, 'uninstall');
    expect(isModalVisible).to.eq(false);

    const dirExists = await files.doesFileExist(`${files.getRootPath()}/modules/${Modules.psThemeCusto.tag}/`);
    expect(dirExists).to.eq(true);
  });

  it('should uninstall the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

    const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psThemeCusto, 'uninstall', false, true);
    expect(successMessage).to.eq(moduleManagerPage.uninstallModuleSuccessMessage(Modules.psThemeCusto.tag));

    // Check the directory `modules/Modules.psThemeCusto.tag`
    const dirExists = await files.doesFileExist(`${files.getRootPath()}/modules/${Modules.psThemeCusto.tag}/`);
    expect(dirExists).to.eq(false);
  });

  it('should go to \'Design > Theme & Logo\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToThemeAndLogoPage', baseContext);

    // Reload => The "Theme & Logo" link identifier changes
    await moduleManagerPage.reloadPage(page);
    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.themeAndLogoLink,
    );
    await themeAndLogoPage.closeSfToolBar(page);

    const pageTitle = await themeAndLogoPage.getPageTitle(page);
    expect(pageTitle).to.equal(themeAndLogoPage.pageTitle);
  });

  it('should check that tabs are not present', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkTabsNotPresent', baseContext);

    const hasSubTabAdvancedCustomization = await themeAndLogoPage.hasSubTabAdvancedCustomization(page);
    expect(hasSubTabAdvancedCustomization).to.equal(false);

    const hasSubTabPagesConfiguration = await themeAndLogoPage.hasSubTabPagesConfiguration(page);
    expect(hasSubTabPagesConfiguration).to.equal(false);
  });

  describe(`POST-CONDITION : Install the module ${Modules.psThemeCusto.name}`, async () => {
    it('should go back to Back Office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToModulesManager', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should download the zip of the module '${Modules.psThemeCusto.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadModule', baseContext);

      await files.downloadFile(Modules.psThemeCusto.releaseZip, 'module.zip');

      const found = await files.doesFileExist('module.zip');
      expect(found).to.eq(true);
    });

    it(`should upload the module '${Modules.psThemeCusto.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uploadModule', baseContext);

      const successMessage = await moduleManagerPage.uploadModule(page, 'module.zip');
      expect(successMessage).to.eq(moduleManagerPage.uploadModuleSuccessMessage);
    });

    it('should close upload module modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

      const isModalNotVisible = await moduleManagerPage.closeUploadModuleModal(page);
      expect(isModalNotVisible).to.eq(true);
    });

    it(`should search the module '${Modules.psThemeCusto.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkModulePresent', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psThemeCusto);
      expect(isModuleVisible, 'Module is not visible!').to.eq(true);
    });
  });
});
