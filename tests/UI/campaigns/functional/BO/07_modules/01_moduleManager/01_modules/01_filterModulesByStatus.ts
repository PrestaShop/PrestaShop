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

const baseContext: string = 'functional_BO_modules_moduleManager_modules_filterModulesByStatus';

describe('BO - Modules - Module Manager : Filter modules by status', async () => {
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

  describe('Filter modules by status', async () => {
    it(`should uninstall the module '${dataModules.contactForm.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, dataModules.contactForm, 'uninstall');
      expect(successMessage).to.eq(moduleManagerPage.uninstallModuleSuccessMessage(dataModules.contactForm.tag));
    });

    ['enabled', 'disabled', 'installed', 'uninstalled'].forEach((status: string, index: number) => {
      it(`should filter by status : '${status}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterByStatus${index}`, baseContext);

        await moduleManagerPage.filterByStatus(page, status);

        const modules = await moduleManagerPage.getAllModulesStatus(page, status);
        modules.map(
          (module) => expect(module.status, `'${module.name}' is not ${status}`).to.eq(true),
        );
      });
    });

    it(`should install the module '${dataModules.contactForm.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'installModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, dataModules.contactForm, 'install');
      expect(successMessage).to.eq(moduleManagerPage.installModuleSuccessMessage(dataModules.contactForm.tag));
    });

    it('should show all modules and check the different blocks', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'showAllModules', baseContext);

      await moduleManagerPage.filterByStatus(page, 'all-Modules');

      const blocksNumber = await moduleManagerPage.getNumberOfBlocks(page);
      expect(blocksNumber).greaterThan(2);
    });
  });
});
