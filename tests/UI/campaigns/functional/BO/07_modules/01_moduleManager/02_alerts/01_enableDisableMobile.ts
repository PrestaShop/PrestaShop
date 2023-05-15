// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import moduleAlertsPage from '@pages/BO/modules/moduleAlerts';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_modules_moduleManager_alerts_enableDisableMobile';

describe('BO - Modules - Alerts tab : Disable / Enable mobile', async () => {
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

  it('should go to \'Alerts\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAlertsTab', baseContext);

    await moduleManagerPage.goToAlertsTab(page);

    const pageTitle = await moduleAlertsPage.getPageTitle(page);
    await expect(pageTitle).to.eq(moduleAlertsPage.pageTitle);
  });

  [
    {
      title: 'disable mobile',
      action: 'disableMobile',
      message: moduleAlertsPage.disableMobileSuccessMessage(Modules.psCheckPayment.tag),
    },
    {
      title: 'enable mobile',
      action: 'enableMobile',
      message: moduleAlertsPage.enableMobileSuccessMessage(Modules.psCheckPayment.tag),
    },
  ].forEach((test) => {
    it(`should ${test.title}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.action, baseContext);

      const successMessage = await moduleAlertsPage.setActionInModule(page, Modules.psCheckPayment, test.action);
      await expect(successMessage).to.eq(test.message);
    });
  });
});
