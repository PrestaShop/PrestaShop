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
const featureFlagPage = require('@pages/BO/advancedParameters/featureFlag');

let browserContext;
let page;

/**
 * Function to enable new product page
 * @param baseContext {string} String to identify the test
 */
function enableNewProductPageTest(baseContext = 'commonTests-enableNewProductPage') {
  describe('PRE-TEST: Enable "New product page - Single store"', async () => {
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

    it('should go to \'Advanced Parameters > New & Experimental Features\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFeatureFlagPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.featureFlagLink,
      );

      await featureFlagPage.closeSfToolBar(page);

      const pageTitle = await featureFlagPage.getPageTitle(page);
      await expect(pageTitle).to.contains(featureFlagPage.pageTitle);
    });

    it('should enable New product page - Single store', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableNewProductPage', baseContext);

      const successMessage = await featureFlagPage.setNewProductPage(page, true);
      await expect(successMessage).to.be.contain(featureFlagPage.successfulUpdateMessage);
    });
  });
}

/**
 * Function to disable new product page
 * @param baseContext {string} String to identify the test
 */
function disableNewProductPageTest(baseContext = 'commonTests-disableNewProductPage') {
  describe('POST-TEST: Disable "New product page - Single store"', async () => {
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

    it('should go to \'Advanced Parameters > New & Experimental Features\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFeatureFlagPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.featureFlagLink,
      );

      await featureFlagPage.closeSfToolBar(page);

      const pageTitle = await featureFlagPage.getPageTitle(page);
      await expect(pageTitle).to.contains(featureFlagPage.pageTitle);
    });

    it('should disable New product page - Single store', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableNewProductPage', baseContext);

      const successMessage = await featureFlagPage.setNewProductPage(page, false);
      await expect(successMessage).to.be.contain(featureFlagPage.successfulUpdateMessage);
    });
  });
}

module.exports = {enableNewProductPageTest, disableNewProductPageTest};
