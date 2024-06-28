// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boModuleManagerPage,
  dataModules,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_modules_moduleManager_modules_enableDisableModule';

describe('BO - Modules - Module Manager : Enable/Disable module', async () => {
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
    await boModuleManagerPage.closeSfToolBar(page);

    const pageTitle = await boModuleManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
  });

  [
    {
      args: {
        title: 'disable the module',
        action: 'disable',
        message: boModuleManagerPage.disableModuleSuccessMessage(dataModules.availableQuantities.tag),
      },
    },
  ].forEach((test) => {
    it(`should ${test.args.title}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.action, baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.availableQuantities, test.args.action);
      expect(successMessage).to.eq(test.args.message);
    });
  });

  it(`should search the module ${dataModules.availableQuantities.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.availableQuantities);
    expect(isModuleVisible).to.eq(true);
  });

  it('should enable the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableModule', baseContext);

    const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.availableQuantities, 'enable');
    expect(successMessage).to.eq(boModuleManagerPage.enableModuleSuccessMessage(dataModules.availableQuantities.tag));
  });

  it('should show all modules', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'showAllModules', baseContext);

    await boModuleManagerPage.filterByStatus(page, 'all-Modules');

    const blocksNumber = await boModuleManagerPage.getNumberOfBlocks(page);
    expect(blocksNumber).greaterThan(2);
  });
});
