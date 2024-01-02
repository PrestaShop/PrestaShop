// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import customerSettingsPage from '@pages/BO/shopParameters/customerSettings';
import CustomerSettingsOptions from '@pages/BO/shopParameters/customerSettings/options';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to enable B2B mode
 * @param baseContext {string} String to identify the test
 */
function enableB2BTest(baseContext: string = 'commonTests-enableB2BTest'): void {
  describe('PRE-TEST: Enable B2B', async () => {
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

    it('should go to \'Shop parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.customerSettingsLink,
      );
      await customerSettingsPage.closeSfToolBar(page);

      const pageTitle = await customerSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
    });

    it('should enable B2B mode', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableB2BMode', baseContext);

      const result = await customerSettingsPage.setOptionStatus(
        page,
        CustomerSettingsOptions.OPTION_B2B,
        true,
      );
      expect(result).to.contains(customerSettingsPage.successfulUpdateMessage);
    });
  });
}

/**
 * Function to disable B2B mode
 * @param baseContext {string} String to identify the test
 */
function disableB2BTest(baseContext: string = 'commonTests-disableB2BTest'): void {
  describe('POST-TEST: Disable B2B', async () => {
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

    it('should go to \'Shop parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.customerSettingsLink,
      );
      await customerSettingsPage.closeSfToolBar(page);

      const pageTitle = await customerSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
    });

    it('should disable B2B mode', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableB2BMode', baseContext);

      const result = await customerSettingsPage.setOptionStatus(
        page,
        CustomerSettingsOptions.OPTION_B2B,
        false,
      );
      expect(result).to.contains(customerSettingsPage.successfulUpdateMessage);
    });
  });
}

export {enableB2BTest, disableB2BTest};
