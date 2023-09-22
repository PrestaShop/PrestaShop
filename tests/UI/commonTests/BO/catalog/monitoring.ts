// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import monitoringPage from '@pages/BO/catalog/monitoring';
import dashboardPage from '@pages/BO/dashboard';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to bulk delete all elements on table
 * @param tableID {string} Table name to bulk delete elements
 * @param baseContext {string} String to identify the test
 */
function bulkDeleteProductsTest(tableID: string, baseContext: string = `commonTests-bulkDelete${tableID}Test`): void {
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
      expect(pageTitle).to.contains(monitoringPage.pageTitle);
    });

    it('should bulk delete elements on table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteElements', baseContext);

      const textResult = await monitoringPage.bulkDeleteElementsInTable(page, tableID);
      expect(textResult).to.equal(monitoringPage.successfulDeleteMessage);
    });

    it('should check number of elements on table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'reset', baseContext);

      const numberOfElementsAfterDelete = await monitoringPage.resetAndGetNumberOfLines(page, tableID);
      expect(numberOfElementsAfterDelete).to.be.equal(0);
    });
  });
}

export default bulkDeleteProductsTest;
