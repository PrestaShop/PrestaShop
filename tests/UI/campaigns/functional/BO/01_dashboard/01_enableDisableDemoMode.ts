// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import pages
import dashboardPage from '@pages/BO/dashboard';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_dashboard_enableDisableDemoMode';

describe('BO - Dashboard : Enable/Disable demo mode & check stats', async () => {
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

  it('should enable demo mode and check stats', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableDemoMode', baseContext);

    await dashboardPage.setDemoMode(page, true);

    const salesScore = await dashboardPage.getSalesScore(page);
    expect(salesScore).to.be.above(400000);
  });

  it('should disable demo mode and check stats', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableDemoMode', baseContext);

    await dashboardPage.setDemoMode(page, false);

    const salesScore = await dashboardPage.getSalesScore(page);
    expect(salesScore).to.be.below(50000);
  });
});
