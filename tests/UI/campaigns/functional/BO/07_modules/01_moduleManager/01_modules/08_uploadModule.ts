// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  dataModules,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_modules_moduleManager_modules_uploadModule';

describe('BO - Modules - Module Manager : Upload module', async () => {
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

  it(`should download the zip of the module '${dataModules.keycloak.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'downloadModule', baseContext);

    await utilsFile.downloadFile(dataModules.keycloak.releaseZip, 'module.zip');

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

  it(`should upload the module '${dataModules.keycloak.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uploadModule', baseContext);

    const successMessage = await moduleManagerPage.uploadModule(page, 'module.zip');
    expect(successMessage).to.eq(moduleManagerPage.uploadModuleSuccessMessage);
  });

  it('should close upload module modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

    const isModalVisible = await moduleManagerPage.closeUploadModuleModal(page);
    expect(isModalVisible).to.eq(true);
  });

  it(`should search the module '${dataModules.keycloak.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, dataModules.keycloak);
    expect(isModuleVisible, 'Module is not visible!').to.eq(true);
  });

  it(`should uninstall the module '${dataModules.keycloak.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

    const successMessage = await moduleManagerPage.setActionInModule(page, dataModules.keycloak, 'uninstall');
    expect(successMessage).to.eq(moduleManagerPage.uninstallModuleSuccessMessage(dataModules.keycloak.tag));
  });
});
