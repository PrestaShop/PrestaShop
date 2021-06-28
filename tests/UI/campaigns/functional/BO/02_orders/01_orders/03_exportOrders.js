require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders/index');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_exportOrders';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let filePath;

// Export orders and check csv file
describe('BO - Orders : Export orders', async () => {
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
    await expect(pageTitle).to.contains(ordersPage.pageTitle);
  });

  it('should export orders to a csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportOrders', baseContext);

    filePath = await ordersPage.exportDataToCsv(page);
    const doesFileExist = await files.doesFileExist(filePath, 5000);
    await expect(doesFileExist, 'Export of data has failed').to.be.true;
  });

  it('should check existence of orders data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllOrdersInCsvFile', baseContext);

    // Get number of orders
    const numberOfOrders = await ordersPage.getNumberOfElementInGrid(page);

    // Check each order in file
    for (let row = 1; row <= numberOfOrders; row++) {
      const orderInCsvFormat = await ordersPage.getOrderInCsvFormat(page, row);
      const textExist = await files.isTextInFile(filePath, orderInCsvFormat, true, true);
      await expect(textExist, `${orderInCsvFormat} was not found in the file`).to.be.true;
    }
  });
});
