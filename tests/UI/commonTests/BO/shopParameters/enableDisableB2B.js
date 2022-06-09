require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const customerSettingsPage = require('@pages/BO/shopParameters/customerSettings');
const {options} = require('@pages/BO/shopParameters/customerSettings/options');

// Import test context
const testContext = require('@utils/testContext');

let browserContext;
let page;

/**
 * Function to enable B2B mode
 * @param baseContext {string} String to identify the test
 */
function enableB2BTest(baseContext = 'commonTests-enableB2BTest') {
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
      await expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
    });

    it('should enable B2B mode', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableB2BMode', baseContext);

      const result = await customerSettingsPage.setOptionStatus(
        page,
        options.OPTION_B2B,
        true,
      );

      await expect(result).to.contains(customerSettingsPage.successfulUpdateMessage);
    });
  });
}

/**
 * Function to disable B2B mode
 * @param baseContext {string} String to identify the test
 */
function disableB2BTest(baseContext = 'commonTests-disableB2BTest') {
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
      await expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
    });

    it('should disable B2B mode', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableB2BMode', baseContext);

      const result = await customerSettingsPage.setOptionStatus(
        page,
        options.OPTION_B2B,
        false,
      );

      await expect(result).to.contains(customerSettingsPage.successfulUpdateMessage);
    });
  });
}

module.exports = {enableB2BTest, disableB2BTest};
