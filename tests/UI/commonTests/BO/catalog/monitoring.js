require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');

// Import login test
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const monitoringPage = require('@pages/BO/catalog/monitoring');

// Import test context
const testContext = require('@utils/testContext');

let browserContext;
let page;

/**
 * Function to bulk delete all elements on table
 * @param tableID {string} Table name to bulk delete elements
 * @param baseContext {string} String to identify the test
 */
function bulkDeleteProductsTest(tableID, baseContext = `commonTests-bulkDelete${tableID}Test`) {
  describe(`POST-TEST: Bulk delete products from '${tableID}' table`, async () => {
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

    it('should go to \'Catalog > Monitoring\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMonitoringPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.monitoringLink,
      );

      const pageTitle = await monitoringPage.getPageTitle(page);
      await expect(pageTitle).to.contains(monitoringPage.pageTitle);
    });

    it('should bulk delete elements on table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteElements', baseContext);

      const textResult = await monitoringPage.bulkDeleteElementsInTable(page, tableID);
      await expect(textResult).to.equal(monitoringPage.successfulDeleteMessage);
    });

    it('should check number of elements on table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'reset', baseContext);

      const numberOfElementsAfterDelete = await monitoringPage.resetAndGetNumberOfLines(page, tableID);
      await expect(numberOfElementsAfterDelete).to.be.equal(0);
    });
  });
}

module.exports = {bulkDeleteProductsTest};
