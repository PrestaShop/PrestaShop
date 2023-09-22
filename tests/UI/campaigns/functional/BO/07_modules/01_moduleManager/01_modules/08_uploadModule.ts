// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_modules_moduleManager_modules_uploadModule';

describe('BO - Modules - Module Manager : Upload module', async () => {
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

  it(`should download the zip of the module '${Modules.keycloak.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'downloadModule', baseContext);

    await files.downloadFile(Modules.keycloak.releaseZip, 'module.zip');

    const found = await files.doesFileExist('module.zip');
    expect(found).to.eq(true);
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

  it(`should upload the module '${Modules.keycloak.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uploadModule', baseContext);

    const successMessage = await moduleManagerPage.uploadModule(page, 'module.zip');
    expect(successMessage).to.eq(moduleManagerPage.uploadModuleSuccessMessage);
  });

  it('should close upload module modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

    const isModalVisible = await moduleManagerPage.closeUploadModuleModal(page);
    expect(isModalVisible).to.eq(true);
  });

  it(`should search the module '${Modules.keycloak.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.keycloak);
    expect(isModuleVisible, 'Module is not visible!').to.eq(true);
  });

  it(`should uninstall the module '${Modules.keycloak.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

    const successMessage = await moduleManagerPage.setActionInModule(page, Modules.keycloak, 'uninstall');
    expect(successMessage).to.eq(moduleManagerPage.uninstallModuleSuccessMessage(Modules.keycloak.tag));
  });
});
