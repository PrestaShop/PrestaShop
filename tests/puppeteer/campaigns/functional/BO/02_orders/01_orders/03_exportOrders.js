require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const OrdersPage = require('@pages/BO/orders/index');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_exportOrders';

let browser;
let page;
let fileName;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    ordersPage: new OrdersPage(page),
  };
};
// Export orders and check csv file
describe('Export orders', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
    await files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${fileName}`);
  });

  // Login into BO and go to orders page
  loginCommon.loginBO();

  it('should go to orders page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.ordersParentLink,
      this.pageObjects.dashboardPage.ordersLink,
    );

    await this.pageObjects.ordersPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
  });

  it('should export orders to a csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportOrders', baseContext);

    await this.pageObjects.ordersPage.exportDataToCsv();
    const doesFileExist = await files.doesFileExist('order_', 5000, true, 'csv');
    await expect(doesFileExist, 'Export of data has failed').to.be.true;
  });

  it('should check existence of orders data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllOrdersInCsvFile', baseContext);

    // Get number of orders
    const numberOfOrders = await this.pageObjects.ordersPage.getNumberOfElementInGrid();

    // Get file name
    fileName = await files.getFileNameFromDir(global.BO.DOWNLOAD_PATH, 'order_', '.csv');

    // Check each order in file
    for (let row = 1; row <= numberOfOrders; row++) {
      const orderInCsvFormat = await this.pageObjects.ordersPage.getOrderInCsvFormat(row);
      const textExist = await files.isTextInFile(fileName, orderInCsvFormat, true, true);
      await expect(textExist, `${orderInCsvFormat} was not found in the file`).to.be.true;
    }
  });
});
