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

const baseContext: string = 'functional_BO_modules_moduleManager_modules_bulkActions';

describe('BO - Modules - Module Manager : Bulk actions', async () => {
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

  it('should check that the bulk action button is disabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkBulkActionButton', baseContext);

    const isBulkActionsDisabled = await boModuleManagerPage.isBulkActionsButtonDisabled(page);
    expect(isBulkActionsDisabled).to.eq(true);
  });

  it(`should select the module '${dataModules.statsstock.name}' and check the bulk actions button`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkBulkActionButtonEnabled', baseContext);

    await boModuleManagerPage.selectModule(page, dataModules.statsstock.tag);

    const isBulkActionsDisabled = await boModuleManagerPage.isBulkActionsButtonDisabled(page);
    expect(isBulkActionsDisabled).to.eq(false);
  });

  [
    {
      args: {
        action: 'Disable',
        message: boModuleManagerPage.disableModuleSuccessMessage(dataModules.statsstock.tag),
      },
    },
    {args: {action: 'Enable', message: boModuleManagerPage.enableModuleSuccessMessage(dataModules.statsstock.tag)}},
    {
      args: {
        action: 'Uninstall',
        message: boModuleManagerPage.uninstallModuleSuccessMessage(dataModules.statsstock.tag),
      },
    },
    {
      args: {
        action: 'Install',
        message: boModuleManagerPage.installModuleSuccessMessage(dataModules.statsstock.tag),
      },
    },
    {args: {action: 'Reset', message: boModuleManagerPage.resetModuleSuccessMessage(dataModules.statsstock.tag)}},
  ].forEach((test, index: number) => {
    it(`should '${test.args.action}' with bulk actions`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `bulkActions${index}`, baseContext);

      const successMessage = await boModuleManagerPage.bulkActions(page, test.args.action);
      expect(successMessage).to.eq(test.args.message);
    });
  });
});
