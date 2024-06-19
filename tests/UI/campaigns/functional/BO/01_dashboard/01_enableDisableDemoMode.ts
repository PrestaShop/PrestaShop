// Import utils
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_dashboard_enableDisableDemoMode';

describe('BO - Dashboard : Enable/Disable demo mode & check stats', async () => {
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

  it('should enable demo mode and check stats', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableDemoMode', baseContext);

    await boDashboardPage.setDemoMode(page, true);

    const salesScore = await boDashboardPage.getSalesScore(page);
    expect(salesScore).to.be.above(400000);
  });

  it('should disable demo mode and check stats', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableDemoMode', baseContext);

    await boDashboardPage.setDemoMode(page, false);

    const salesScore = await boDashboardPage.getSalesScore(page);
    expect(salesScore).to.be.below(50000);
  });
});
