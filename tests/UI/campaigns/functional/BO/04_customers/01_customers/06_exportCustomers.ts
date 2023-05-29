// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import customersPage from '@pages/BO/customers';
import dashboardPage from '@pages/BO/dashboard';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Customers > Customers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.customersParentLink,
      dashboardPage.customersLink,
    );

    const pageTitle = await customersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(customersPage.pageTitle);
  });

  it('should reset all filters and get number of customers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCustomers).to.be.above(0);
  });

  it('should export customers to a csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportCustomers', baseContext);

    filePath = await customersPage.exportDataToCsv(page);

    const doesFileExist = await files.doesFileExist(filePath, 5000);
    await expect(doesFileExist, 'Export of data has failed').to.be.true;
  });

  it('should check existence of customers data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllCustomersInCsvFile', baseContext);

    numberOfCustomers = await customersPage.getNumberOfElementInGrid(page);

    for (let row: number = 1; row <= numberOfCustomers; row++) {
      const customerInCsvFormat = await customersPage.getCustomerInCsvFormat(page, row);
      const textExist = await files.isTextInFile(filePath, customerInCsvFormat, true);
      await expect(textExist, `${customerInCsvFormat} was not found in the file`).to.be.true;
    }
  });
});
