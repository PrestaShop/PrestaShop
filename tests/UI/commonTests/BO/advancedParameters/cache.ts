// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import performancePage from '@pages/BO/advancedParameters/performance';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
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
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > Performance\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPerformancePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.performanceLink,
      );
      await performancePage.closeSfToolBar(page);

      const pageTitle = await performancePage.getPageTitle(page);
      expect(pageTitle).to.contains(performancePage.pageTitle);
    });

    it('should clear cache', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearCache', baseContext);

      const successMessage = await performancePage.clearCache(page);
      expect(successMessage).to.equal(performancePage.clearCacheSuccessMessage);
    });
  });
}

export default deleteCacheTest;
