import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boModuleConfigurationPage,
  boModuleManagerPage,
  type BrowserContext,
  dataModules,
  type Page,
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

  it(`should search for module ${dataModules.contactForm.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchForModule', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.contactForm);
    expect(isModuleVisible, 'Module is not visible!').to.eq(true);
  });

  it('should go to module configuration page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'configureModule', baseContext);

    await boModuleManagerPage.goToConfigurationPage(page, dataModules.contactForm.tag);

    const pageSubtitle = await boModuleConfigurationPage.getPageSubtitle(page);
    expect(pageSubtitle).to.contains(dataModules.contactForm.name);
  });
});
