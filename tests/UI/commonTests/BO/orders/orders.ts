// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to bulk update order status
 * @param orderStatusData {object} Order status data to update
 * @param baseContext {string} String to identify the test
 */
function bulkUpdateOrderStatusTest(orderStatusData: object, baseContext: string = 'commonTests-bulkUpdateOrderStatusTest'): void {
  describe('POST-TEST: Bulk update order status', async () => {
    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    it('should login to BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to Orders > Orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should update created orders status with bulk action', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkUpdateOrdersStatus', baseContext);

      const textResult = await ordersPage.bulkUpdateOrdersStatus(
        page,
        orderStatusData.orderStatus,
        orderStatusData.isAllOrders,
        orderStatusData.rows,
      );
      expect(textResult).to.equal(ordersPage.successfulUpdateMessage);
    });
  });
}

export default bulkUpdateOrderStatusTest;
