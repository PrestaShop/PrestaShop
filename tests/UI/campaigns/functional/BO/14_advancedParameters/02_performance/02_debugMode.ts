// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boPerformancePage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  it('should go to \'Advanced Parameters > Performance\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPerformancePage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.performanceLink,
    );

    const pageTitle = await boPerformancePage.getPageTitle(page);
    expect(pageTitle).to.contains(boPerformancePage.pageTitle);
  });

  const tests = [
    {args: {action: 'enable', exist: true}},
    {args: {action: 'disable', exist: false}},
  ];

  tests.forEach((test, index: number) => {
    it(`should ${test.args.action} debug mode`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}DebugMode`, baseContext);

      const result = await boPerformancePage.setDebugMode(page, test.args.exist);
      expect(result).to.contains(boPerformancePage.successUpdateMessage);
    });

    it('should check the debug toolbar', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkDebugMode${index}`, baseContext);

      const isVisible = await boPerformancePage.isDebugModeToggleVisible(page);
      expect(isVisible).to.eq(test.args.exist);
    });
  });
});
