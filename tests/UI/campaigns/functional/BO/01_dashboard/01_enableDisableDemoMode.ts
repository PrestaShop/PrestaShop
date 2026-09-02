// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
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
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
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
