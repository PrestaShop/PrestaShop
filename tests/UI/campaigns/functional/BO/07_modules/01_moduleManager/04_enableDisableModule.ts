// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import moduleManagerPage from '@pages/BO/modules/moduleManager';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_modules_moduleManager_enableDisableModule';

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

  it(`should search the module ${Modules.contactForm.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.contactForm);
    await expect(isModuleVisible).to.be.true;
  });

  ['disable', 'enable'].forEach((status: string) => {
    it(`should ${status} the module`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${status}Module`, baseContext);

      const successMessage = await moduleManagerPage.setActionInModuleModule(page, Modules.contactForm, status);

      if (status === 'disable') {
        await expect(successMessage).to.eq(moduleManagerPage.disableModuleSuccessMessage(Modules.contactForm.tag));
      } else {
        await expect(successMessage).to.eq(moduleManagerPage.enableModuleSuccessMessage(Modules.contactForm.tag));
      }
    });
  });

  ['disableMobile', 'enableMobile'].forEach((status: string) => {
    it(`should ${status}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${status}Mobile`, baseContext);

      const successMessage = await moduleManagerPage.setActionInModuleModule(page, Modules.contactForm, status);

      if (status === 'disableMobile') {
        await expect(successMessage).to.eq(moduleManagerPage.disableMobileSuccessMessage(Modules.contactForm.tag));
      } else {
        await expect(successMessage).to.eq(moduleManagerPage.enableMobileSuccessMessage(Modules.contactForm.tag));
      }
    });
  });

  it('should show all modules', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'showAllModules', baseContext);

    await moduleManagerPage.filterByStatus(page, 'all-Modules');

    const blocksNumber = await moduleManagerPage.getNumberOfBlocks(page);
    await expect(blocksNumber).greaterThan(2);
  });
});
