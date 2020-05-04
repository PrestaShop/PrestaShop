require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CustomersPage = require('@pages/BO/customers');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_customers_exportCustomers';

let browser;
let page;
let numberOfCustomers = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    customersPage: new CustomersPage(page),
  };
};

/*
Export customers
Check csv file was downloaded
Check existence of customers data in csv file
 */
describe('Export customers', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to customers page
  loginCommon.loginBO();

  it('should go to Customers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.customersParentLink,
      this.pageObjects.boBasePage.customersLink,
    );
    const pageTitle = await this.pageObjects.customersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
  });

  it('should reset all filters and get number of customers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);
    numberOfCustomers = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
    await expect(numberOfCustomers).to.be.above(0);
  });

  it('should export customers to a csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportCustomers', baseContext);
    await this.pageObjects.customersPage.exportDataToCsv();
    const doesFileExist = await files.doesFileExist('customer_', 5000, true, 'csv');
    await expect(doesFileExist, 'Export of data has failed').to.be.true;
  });

  it('should check existence of customers data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllCustomersInCsvFile', baseContext);
    numberOfCustomers = await this.pageObjects.customersPage.getNumberOfElementInGrid();
    const fileName = await files.getFileNameFromDir(global.BO.DOWNLOAD_PATH, 'customer_', '.csv');
    for (let row = 1; row <= numberOfCustomers; row++) {
      const customerInCsvFormat = await this.pageObjects.customersPage.getCustomerInCsvFormat(row);
      const textExist = await files.isTextInFile(fileName, customerInCsvFormat, true);
      await expect(textExist, `${customerInCsvFormat} was not found in the file`).to.be.true;
    }
    await files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${fileName}`);
  });
});
