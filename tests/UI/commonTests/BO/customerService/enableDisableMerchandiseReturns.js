require('module-alias/register');
// Import utils
const helper = require('@utils/helpers');

// Import login test
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const merchandiseReturnsPage = require('@pages/BO/customerService/merchandiseReturns');

// Import test context
const testContext = require('@utils/testContext');

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

/**
 * Function to enable merchandise returns
 * @param baseContext {string} String to identify the test
 */
function enableMerchandiseReturns(baseContext = 'commonTests-enableMerchandiseReturnsTest') {
  describe('PRE-TEST: Enable merchandise returns', async () => {
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

    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.merchandiseReturnsLink,
      );

      await merchandiseReturnsPage.closeSfToolBar(page);

      const pageTitle = await merchandiseReturnsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(merchandiseReturnsPage.pageTitle);
    });

    it('should enable merchandise returns', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableReturns', baseContext);

      const result = await merchandiseReturnsPage.setOrderReturnStatus(page, true);
      await expect(result).to.contains(merchandiseReturnsPage.successfulUpdateMessage);
    });
  });
}

/**
 * Function to disable merchandise returns
 * @param baseContext {string} String to identify the test
 */
function disableMerchandiseReturns(baseContext = 'commonTests-disableMerchandiseReturnsTest') {
  describe('POST-TEST: Disable merchandise returns', async () => {
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

    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPageToDisable', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.merchandiseReturnsLink,
      );

      const pageTitle = await merchandiseReturnsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(merchandiseReturnsPage.pageTitle);
    });

    it('should disable merchandise returns', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableReturns', baseContext);

      const result = await merchandiseReturnsPage.setOrderReturnStatus(page, false);
      await expect(result).to.contains(merchandiseReturnsPage.successfulUpdateMessage);
    });
  });
}

module.exports = {enableMerchandiseReturns, disableMerchandiseReturns};
