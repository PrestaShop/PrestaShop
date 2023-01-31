// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to enable new product page
 * @param baseContext {string} String to identify the test
 */
function enableNewProductPageTest(baseContext: string = 'commonTests-enableNewProductPage'): void {
  describe('PRE-TEST: Enable "New product page"', async () => {
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

    it('should enable New product page', async function () {
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
function disableNewProductPageTest(baseContext: string = 'commonTests-disableNewProductPage'): void {
  describe('POST-TEST: Disable "New product page"', async () => {
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

    it('should disable New product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableNewProductPage', baseContext);

      const successMessage = await featureFlagPage.setNewProductPage(page, false);
      await expect(successMessage).to.be.contain(featureFlagPage.successfulUpdateMessage);
    });
  });
}

/**
 * Indicate the default state of new page with feature flags, depending on its initial value we need to
 * adapt the tests behaviour especially the part that enables/disables the page. We keep this value editable
 * here in case the default value changes the tests will be easy to adapt.
 */
function isNewProductPageEnabledByDefault(): boolean {
  return true;
}

export {enableNewProductPageTest, disableNewProductPageTest, isNewProductPageEnabledByDefault};
