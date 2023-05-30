// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import {BrowserContext, Page} from 'playwright';

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

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.modulesParentLink,
      dashboardPage.moduleManagerLink,
    );
    await moduleManagerPage.closeSfToolBar(page);

    const pageTitle = await moduleManagerPage.getPageTitle(page);
    await expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
  });

  it('should check that the bulk action button is disabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkBulkActionButton', baseContext);

    const isBulkActionsDisabled = await moduleManagerPage.isBulkActionsButtonDisabled(page);
    await expect(isBulkActionsDisabled).to.be.true;
  });

  it(`should select the module '${Modules.availableQuantities.name}' and check the bulk actions button`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkBulkActionButtonEnabled', baseContext);

    await moduleManagerPage.selectModule(page, Modules.availableQuantities.tag);

    const isBulkActionsDisabled = await moduleManagerPage.isBulkActionsButtonDisabled(page);
    await expect(isBulkActionsDisabled).to.be.false;
  });

  [
    {
      args: {
        action: 'Disable',
        message: moduleManagerPage.disableModuleSuccessMessage(Modules.availableQuantities.tag),
      },
    },
    {args: {action: 'Enable', message: moduleManagerPage.enableModuleSuccessMessage(Modules.availableQuantities.tag)}},
    {
      args: {
        action: 'Uninstall',
        message: moduleManagerPage.uninstallModuleSuccessMessage(Modules.availableQuantities.tag),
      },
    },
    {
      args: {
        action: 'Install',
        message: moduleManagerPage.installModuleSuccessMessage(Modules.availableQuantities.tag),
      },
    },
    {args: {action: 'Reset', message: moduleManagerPage.resetModuleSuccessMessage(Modules.availableQuantities.tag)}},
    {
      args: {
        action: 'Disable Mobile',
        message: moduleManagerPage.disableMobileSuccessMessage(Modules.availableQuantities.tag),
      },
    },
    {
      args: {
        action: 'Enable Mobile',
        message: moduleManagerPage.enableMobileSuccessMessage(Modules.availableQuantities.tag),
      },
    },

  ].forEach((test, index: number) => {
    it(`should '${test.args.action}' with bulk actions`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `bulkActions${index}`, baseContext);

      const successMessage = await moduleManagerPage.bulkActions(page, test.args.action);
      await expect(successMessage).to.eq(test.args.message);
    });
  });
});
