// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import {moduleConfigurationPage} from '@pages/BO/modules/moduleConfiguration';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_modules_moduleManager_modules_configureModule';

describe('BO - Modules - Module Manager : Configure module', async () => {
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
    expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
  });

  it(`should search for module ${Modules.contactForm.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchForModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.contactForm);
    expect(isModuleVisible, 'Module is not visible!').to.eq(true);
  });

  it('should go to module configuration page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'configureModule', baseContext);

    await moduleManagerPage.goToConfigurationPage(page, Modules.contactForm.tag);

    const pageSubtitle = await moduleConfigurationPage.getPageSubtitle(page);
    expect(pageSubtitle).to.contains(Modules.contactForm.name);
  });
});
