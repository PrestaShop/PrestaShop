// Import utils
import testContext from '@utils/testContext';

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

const baseContext: string = 'functional_BO_modules_moduleManager_alerts_installUninstallModule';

describe('BO - Modules - Alerts : Install/Uninstall module', async () => {
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

  it(`should uninstall the module '${dataModules.psCheckPayment.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

    const successMessage = await boModuleManagerAlertsPage.setActionInModule(page, dataModules.psCheckPayment, 'uninstall');
    expect(successMessage).to.eq(boModuleManagerAlertsPage.uninstallModuleSuccessMessage(dataModules.psCheckPayment.tag));
  });

  it(`should install the module '${dataModules.psCheckPayment.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'installModule', baseContext);

    const successMessage = await boModuleManagerAlertsPage.setActionInModule(page, dataModules.psCheckPayment, 'install');
    expect(successMessage).to.eq(boModuleManagerAlertsPage.installModuleSuccessMessage(dataModules.psCheckPayment.tag));
  });
});
