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

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to clear cache
 * @param baseContext {string} String to identify the test
 */
function deleteCacheTest(baseContext: string = 'commonTests-deleteCache'): void {
  describe('PRE-TEST: Delete cache', async () => {
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
      await boPerformancePage.closeSfToolBar(page);

      const pageTitle = await boPerformancePage.getPageTitle(page);
      expect(pageTitle).to.contains(boPerformancePage.pageTitle);
    });

    it('should clear cache', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearCache', baseContext);

      const successMessage = await boPerformancePage.clearCache(page);
      expect(successMessage).to.equal(boPerformancePage.clearCacheSuccessMessage);
    });
  });
}

export default deleteCacheTest;
