import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boMonitoringPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

    it('should go to \'Catalog > Monitoring\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMonitoringPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.monitoringLink,
      );

      const pageTitle = await boMonitoringPage.getPageTitle(page);
      expect(pageTitle).to.contains(boMonitoringPage.pageTitle);
    });

    it('should bulk delete elements on table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteElements', baseContext);

      const textResult = await boMonitoringPage.bulkDeleteElementsInTable(page, tableID);
      expect(textResult).to.equal(boMonitoringPage.successfulDeleteMessage);
    });

    it('should check number of elements on table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'reset', baseContext);

      const numberOfElementsAfterDelete = await boMonitoringPage.resetAndGetNumberOfLines(page, tableID);
      expect(numberOfElementsAfterDelete).to.be.equal(0);
    });
  });
}

export default bulkDeleteProductsTest;
