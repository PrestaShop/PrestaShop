// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import performancePage from '@pages/BO/advancedParameters/performance';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_performance_debugMode';

/*
Enable/Disable debug mode
Check the existence debug toolbar
 */
describe('BO - Advanced Parameters - Performance : Enable/Disable debug mode', async () => {
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

  it('should go to \'Advanced Parameters > Performance\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPerformancePage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.performanceLink,
    );

    const pageTitle = await performancePage.getPageTitle(page);
    await expect(pageTitle).to.contains(performancePage.pageTitle);
  });

  const tests = [
    {args: {action: 'enable', exist: true}},
    {args: {action: 'disable', exist: false}},
  ];

  tests.forEach((test, index: number) => {
    it(`should ${test.args.action} debug mode`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}DebugMode`, baseContext);

      const result = await performancePage.setDebugMode(page, test.args.exist);
      await expect(result).to.contains(performancePage.successUpdateMessage);
    });

    it('should check the debug toolbar', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkDebugMode${index}`, baseContext);

      const isVisible = await performancePage.isDebugModeToggleVisible(page);
      await expect(isVisible).to.eq(test.args.exist);
    });
  });
});
