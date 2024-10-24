// Import utils
import testContext from '@utils/testContext';

// Import pages
import {moduleConfigurationPage} from '@pages/BO/modules/moduleConfiguration';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  boModuleManagerAlertsPage,
  type BrowserContext,
  dataModules,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_modules_moduleManager_alerts_configureModule';

describe('BO - Modules - Alerts : Configure module', async () => {
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

  it('should go to \'Alerts\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAlertsTab', baseContext);

    await boModuleManagerPage.goToAlertsTab(page);

    const pageTitle = await boModuleManagerAlertsPage.getPageTitle(page);
    expect(pageTitle).to.eq(boModuleManagerAlertsPage.pageTitle);
  });

  it('should go to module configuration page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'configureModule', baseContext);

    await boModuleManagerAlertsPage.goToConfigurationPage(page, dataModules.psCheckPayment.tag);

    const pageSubtitle = await moduleConfigurationPage.getPageSubtitle(page);
    expect(pageSubtitle).to.contains(dataModules.psCheckPayment.name);
  });
});
