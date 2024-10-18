// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boCustomersPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customers_customers_exportCustomers';

/*
Export customers
Check csv file was downloaded
Check existence of customers data in csv file
 */
describe('BO - Customers - Customers : Export customers', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCustomers: number = 0;
  let filePath: string|null;

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

  it('should go to \'Customers > Customers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customersParentLink,
      boDashboardPage.customersLink,
    );

    const pageTitle = await boCustomersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomersPage.pageTitle);
  });

  it('should reset all filters and get number of customers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCustomers = await boCustomersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCustomers).to.be.above(0);
  });

  it('should export customers to a csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportCustomers', baseContext);

    filePath = await boCustomersPage.exportDataToCsv(page);

    const doesFileExist = await utilsFile.doesFileExist(filePath, 5000);
    expect(doesFileExist, 'Export of data has failed').to.eq(true);
  });

  it('should check existence of customers data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllCustomersInCsvFile', baseContext);

    numberOfCustomers = await boCustomersPage.getNumberOfElementInGrid(page);

    for (let row: number = 1; row <= numberOfCustomers; row++) {
      const customerInCsvFormat = await boCustomersPage.getCustomerInCsvFormat(page, row);
      const textExist = await utilsFile.isTextInFile(filePath, customerInCsvFormat, true);
      expect(textExist, `${customerInCsvFormat} was not found in the file`).to.eq(true);
    }
  });
});
