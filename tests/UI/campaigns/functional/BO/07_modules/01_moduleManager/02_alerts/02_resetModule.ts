// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
import moduleAlertsPage from '@pages/BO/modules/moduleAlerts';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_modules_moduleManager_alerts_resetModule';

describe('BO - Modules - Alerts tab : Reset module', async () => {
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

  it('should go to \'Alerts\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAlertsTab', baseContext);

    await moduleManagerPage.goToAlertsTab(page);

    const pageTitle = await moduleAlertsPage.getPageTitle(page);
    expect(pageTitle).to.eq(moduleAlertsPage.pageTitle);
  });

  it(`should reset the module '${Modules.psCheckPayment.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

    const successMessage = await moduleAlertsPage.setActionInModule(page, Modules.psCheckPayment, 'reset');
    expect(successMessage).to.eq(moduleAlertsPage.resetModuleSuccessMessage(Modules.psCheckPayment.tag));
  });
});
