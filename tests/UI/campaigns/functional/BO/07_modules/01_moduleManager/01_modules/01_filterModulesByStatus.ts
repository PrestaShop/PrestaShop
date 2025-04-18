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

  describe('Filter modules by status', async () => {
    it(`should uninstall the module '${dataModules.contactForm.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.contactForm, 'uninstall');
      expect(successMessage).to.eq(boModuleManagerPage.uninstallModuleSuccessMessage(dataModules.contactForm.tag));
    });

    ['enabled', 'disabled', 'installed', 'uninstalled'].forEach((status: string, index: number) => {
      it(`should filter by status : '${status}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterByStatus${index}`, baseContext);

        await boModuleManagerPage.filterByStatus(page, status);

        const modules = await boModuleManagerPage.getAllModulesStatus(page, status);
        modules.map(
          (module) => expect(module.status, `'${module.name}' is not ${status}`).to.eq(true),
        );
      });
    });

    it(`should install the module '${dataModules.contactForm.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'installModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.contactForm, 'install');
      expect(successMessage).to.eq(boModuleManagerPage.installModuleSuccessMessage(dataModules.contactForm.tag));
    });

    it('should show all modules and check the different blocks', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'showAllModules', baseContext);

      await boModuleManagerPage.filterByStatus(page, 'all-Modules');

      const blocksNumber = await boModuleManagerPage.getNumberOfBlocks(page);
      expect(blocksNumber).greaterThan(2);
    });
  });
});
