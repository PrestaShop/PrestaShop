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
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_modules_moduleManager_modules_enableDisableModule';

describe('BO - Modules - Module Manager : Enable/Disable module', async () => {
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

  [
    {
      args: {
        title: 'disable the module',
        action: 'disable',
        message: moduleManagerPage.disableModuleSuccessMessage(Modules.availableQuantities.tag),
      },
    },
  ].forEach((test) => {
    it(`should ${test.args.title}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.action, baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, Modules.availableQuantities, test.args.action);
      await expect(successMessage).to.eq(test.args.message);
    });
  });

  it(`should search the module ${Modules.availableQuantities.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.availableQuantities);
    await expect(isModuleVisible).to.be.true;
  });

  it('should enable the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableModule', baseContext);

    const successMessage = await moduleManagerPage.setActionInModule(page, Modules.availableQuantities, 'enable');
    await expect(successMessage).to.eq(moduleManagerPage.enableModuleSuccessMessage(Modules.availableQuantities.tag));
  });

  it('should show all modules', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'showAllModules', baseContext);

    await moduleManagerPage.filterByStatus(page, 'all-Modules');

    const blocksNumber = await moduleManagerPage.getNumberOfBlocks(page);
    await expect(blocksNumber).greaterThan(2);
  });
});
