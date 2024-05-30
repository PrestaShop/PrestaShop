// Import utils
import helper from '@utils/helpers';
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
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_modules_moduleManager_modules_bulkActions';

describe('BO - Modules - Module Manager : Bulk actions', async () => {
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

  it('should check that the bulk action button is disabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkBulkActionButton', baseContext);

    const isBulkActionsDisabled = await moduleManagerPage.isBulkActionsButtonDisabled(page);
    expect(isBulkActionsDisabled).to.eq(true);
  });

  it(`should select the module '${dataModules.availableQuantities.name}' and check the bulk actions button`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkBulkActionButtonEnabled', baseContext);

    await moduleManagerPage.selectModule(page, dataModules.availableQuantities.tag);

    const isBulkActionsDisabled = await moduleManagerPage.isBulkActionsButtonDisabled(page);
    expect(isBulkActionsDisabled).to.eq(false);
  });

  [
    {
      args: {
        action: 'Disable',
        message: moduleManagerPage.disableModuleSuccessMessage(dataModules.availableQuantities.tag),
      },
    },
    {args: {action: 'Enable', message: moduleManagerPage.enableModuleSuccessMessage(dataModules.availableQuantities.tag)}},
    {
      args: {
        action: 'Uninstall',
        message: moduleManagerPage.uninstallModuleSuccessMessage(dataModules.availableQuantities.tag),
      },
    },
    {
      args: {
        action: 'Install',
        message: moduleManagerPage.installModuleSuccessMessage(dataModules.availableQuantities.tag),
      },
    },
    {args: {action: 'Reset', message: moduleManagerPage.resetModuleSuccessMessage(dataModules.availableQuantities.tag)}},
  ].forEach((test, index: number) => {
    it(`should '${test.args.action}' with bulk actions`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `bulkActions${index}`, baseContext);

      const successMessage = await moduleManagerPage.bulkActions(page, test.args.action);
      expect(successMessage).to.eq(test.args.message);
    });
  });
});
