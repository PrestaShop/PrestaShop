// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

import {expect} from 'chai';
import {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  dataModules,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_modules_moduleManager_modules_installUninstallModule';

describe('BO - Modules - Module Manager : Install/Uninstall module', async () => {
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

  it(`should search the module ${dataModules.psEmailSubscription.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, dataModules.psEmailSubscription);
    expect(isModuleVisible, 'Module is not visible!').to.eq(true);
  });

  it(`should uninstall the module '${dataModules.psEmailSubscription.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

    const successMessage = await moduleManagerPage.setActionInModule(page, dataModules.psEmailSubscription, 'uninstall');
    expect(successMessage).to.eq(moduleManagerPage.uninstallModuleSuccessMessage(dataModules.psEmailSubscription.tag));
  });

  it(`should install the module '${dataModules.psEmailSubscription.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'installModule', baseContext);

    const successMessage = await moduleManagerPage.setActionInModule(page, dataModules.psEmailSubscription, 'install');
    expect(successMessage).to.eq(moduleManagerPage.installModuleSuccessMessage(dataModules.psEmailSubscription.tag));
  });

  it('should show all modules', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'showAllModules', baseContext);

    await moduleManagerPage.filterByStatus(page, 'all-Modules');

    const blocksNumber = await moduleManagerPage.getNumberOfBlocks(page);
    expect(blocksNumber).greaterThan(2);
  });
});
