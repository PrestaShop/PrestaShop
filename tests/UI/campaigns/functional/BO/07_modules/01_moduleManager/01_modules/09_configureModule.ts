// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import {moduleConfigurationPage} from '@pages/BO/modules/moduleConfiguration';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  dataModules,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_modules_moduleManager_modules_configureModule';

describe('BO - Modules - Module Manager : Configure module', async () => {
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

  it(`should search for module ${dataModules.contactForm.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchForModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, dataModules.contactForm);
    expect(isModuleVisible, 'Module is not visible!').to.eq(true);
  });

  it('should go to module configuration page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'configureModule', baseContext);

    await moduleManagerPage.goToConfigurationPage(page, dataModules.contactForm.tag);

    const pageSubtitle = await moduleConfigurationPage.getPageSubtitle(page);
    expect(pageSubtitle).to.contains(dataModules.contactForm.name);
  });
});
