// Import utils
import helper from '@utils/helpers';
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
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_modules_moduleManager_modules_resetModule';

describe('BO - Modules - Module Manager : Reset module', async () => {
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

  it(`should search the module ${dataModules.contactForm.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, dataModules.contactForm);
    expect(isModuleVisible).to.eq(true);
  });

  it('should reset the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

    const successMessage = await moduleManagerPage.setActionInModule(page, dataModules.contactForm, 'reset');
    expect(successMessage).to.eq(moduleManagerPage.resetModuleSuccessMessage(dataModules.contactForm.tag));
  });

  it('should show all modules', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'showAllModules', baseContext);

    await moduleManagerPage.filterByStatus(page, 'all-Modules');

    const blocksNumber = await moduleManagerPage.getNumberOfBlocks(page);
    expect(blocksNumber).greaterThan(2);
  });
});
