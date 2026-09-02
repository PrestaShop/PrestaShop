// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataModules,
  type Page,
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
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
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
        message: boModuleManagerPage.disableModuleSuccessMessage(dataModules.statsstock.tag),
      },
    },
  ].forEach((test) => {
    it(`should ${test.args.title}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.action, baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.statsstock, test.args.action);
      expect(successMessage).to.eq(test.args.message);
    });
  });

  it(`should search the module ${dataModules.statsstock.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.statsstock);
    expect(isModuleVisible).to.eq(true);
  });

  it('should enable the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableModule', baseContext);

    const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.statsstock, 'enable');
    expect(successMessage).to.eq(boModuleManagerPage.enableModuleSuccessMessage(dataModules.statsstock.tag));
  });

  it('should show all modules', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'showAllModules', baseContext);

    await boModuleManagerPage.filterByStatus(page, 'all-Modules');

    const blocksNumber = await boModuleManagerPage.getNumberOfBlocks(page);
    expect(blocksNumber).greaterThan(2);
  });
});
