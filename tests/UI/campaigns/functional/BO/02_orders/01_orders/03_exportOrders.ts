// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_orders_exportOrders';

// Export orders and check csv file
describe('BO - Orders : Export orders', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string|null;

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

  it('should go to \'Orders > Orders\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.ordersParentLink,
      dashboardPage.ordersLink,
    );
    await ordersPage.closeSfToolBar(page);

    const pageTitle = await ordersPage.getPageTitle(page);
    expect(pageTitle).to.contains(ordersPage.pageTitle);
  });

  it('should export orders to a csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportOrders', baseContext);

    filePath = await ordersPage.exportDataToCsv(page);

    const doesFileExist = await files.doesFileExist(filePath, 5000);
    expect(doesFileExist, 'Export of data has failed').to.eq(true);
  });

  it('should check existence of orders data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllOrdersInCsvFile', baseContext);

    // Get number of orders
    const numberOfOrders = await ordersPage.getNumberOfElementInGrid(page);

    // Check each order in file
    for (let row = 1; row <= numberOfOrders; row++) {
      const orderInCsvFormat = await ordersPage.getOrderInCsvFormat(page, row);
      const textExist = await files.isTextInFile(filePath, orderInCsvFormat, true, true);
      expect(textExist, `${orderInCsvFormat} was not found in the file`).to.eq(true);
    }
  });
});
