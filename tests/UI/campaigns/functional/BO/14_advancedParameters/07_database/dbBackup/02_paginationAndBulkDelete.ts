// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import sqlManagerPage from '@pages/BO/advancedParameters/database/sqlManager';
import dbBackupPage from '@pages/BO/advancedParameters/database/dbBackup';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_database_dbBackup_paginationAndBulkDelete';

/*
Create 11 SQL queries
Pagination next and previous
Delete by bulk actions
 */
describe('BO - Advanced Parameters - Database : Pagination and bulk delete DB Backup', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfBackups: number = 0;

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

  // Go db backup page
  it('should go to \'Advanced Parameters > Database\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSqlManagerPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.databaseLink,
    );

    await sqlManagerPage.closeSfToolBar(page);

    const pageTitle = await sqlManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(sqlManagerPage.pageTitle);
  });

  it('should go to \'DB Backup\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDbBackupPage', baseContext);

    await sqlManagerPage.goToDbBackupPage(page);

    const pageTitle = await dbBackupPage.getPageTitle(page);
    expect(pageTitle).to.contains(dbBackupPage.pageTitle);
  });

  it('should get number of db backups', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDbBackups', baseContext);

    numberOfBackups = await dbBackupPage.getNumberOfElementInGrid(page);
    expect(numberOfBackups).to.equal(0);
  });

  // 1 - Create 11 DB backup
  describe('Create 11 new DB Backup in BO', async () => {
    const creationTests: number[] = new Array(11).fill(0, 0, 11);

    creationTests.forEach((test: number, index: number) => {
      it(`should generate db backup n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `generateNewDbBackup${index}`, baseContext);

        const result = await dbBackupPage.createDbDbBackup(page);
        expect(result).to.equal(dbBackupPage.successfulBackupCreationMessage);

        const numberOfBackupsAfterCreation = await dbBackupPage.getNumberOfElementInGrid(page);
        expect(numberOfBackupsAfterCreation).to.equal(numberOfBackups + 1 + index);
      });
    });
  });

  // 2 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await dbBackupPage.selectPaginationLimit(page, 10);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await dbBackupPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await dbBackupPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await dbBackupPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  describe('Bulk delete the 11 DB Backups created', async () => {
    it('should delete DB Backups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteDbBackups', baseContext);

      const result = await dbBackupPage.deleteWithBulkActions(page);
      expect(result).to.be.equal(dbBackupPage.successfulMultiDeleteMessage);

      const numberOfBackupsAfterDelete = await dbBackupPage.getNumberOfElementInGrid(page);
      expect(numberOfBackupsAfterDelete).to.equal(numberOfBackups);
    });
  });
});
