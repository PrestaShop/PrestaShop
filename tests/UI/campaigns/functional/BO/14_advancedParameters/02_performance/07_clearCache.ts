// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boPerformancePage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_performance_clearCache';

describe('BO - Advanced Parameters - Performance : Clear cache', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  it('should clear cache', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clearCache', baseContext);

    const successMessage = await boPerformancePage.clearCache(page);
    expect(successMessage).to.equal(boPerformancePage.clearCacheSuccessMessage);
  });
});
