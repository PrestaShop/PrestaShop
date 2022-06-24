require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login test
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const performancePage = require('@pages/BO/advancedParameters/performance');

let browserContext;
let page;

/**
 * Function to clear cache
 * @param baseContext {string} String to identify the test
 */
function deleteCacheTest(baseContext = 'commonTests-deleteCache') {
  describe('PRE-TEST: Delete cache', async () => {
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

      await performancePage.closeSfToolBar(page);

      const pageTitle = await performancePage.getPageTitle(page);
      await expect(pageTitle).to.contains(performancePage.pageTitle);
    });

    it('should clear cache', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearCache', baseContext);

      const successMessage = await performancePage.clearCache(page);
      await expect(successMessage).to.equal(performancePage.clearCacheSuccessMessage);
    });
  });
}

module.exports = {deleteCacheTest};
