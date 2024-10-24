// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boDbBackupPage,
  boLoginPage,
  boSqlManagerPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  // Go db backup page
  it('should go to \'Advanced Parameters > Database\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSqlManagerPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.databaseLink,
    );

    await boSqlManagerPage.closeSfToolBar(page);

    const pageTitle = await boSqlManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSqlManagerPage.pageTitle);
  });

  it('should go to \'DB Backup\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDbBackupPage', baseContext);

    await boSqlManagerPage.goToDbBackupPage(page);

    const pageTitle = await boDbBackupPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDbBackupPage.pageTitle);
  });

  it('should get number of db backups', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDbBackups', baseContext);

    numberOfBackups = await boDbBackupPage.getNumberOfElementInGrid(page);
    expect(numberOfBackups).to.equal(0);
  });

  // 1 - Create 11 DB backup
  describe('Create 11 new DB Backup in BO', async () => {
    const creationTests: number[] = new Array(11).fill(0, 0, 11);

    creationTests.forEach((test: number, index: number) => {
      it(`should generate db backup n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `generateNewDbBackup${index}`, baseContext);

        const result = await boDbBackupPage.createDbDbBackup(page);
        expect(result).to.equal(boDbBackupPage.successfulBackupCreationMessage);

        const numberOfBackupsAfterCreation = await boDbBackupPage.getNumberOfElementInGrid(page);
        expect(numberOfBackupsAfterCreation).to.equal(numberOfBackups + 1 + index);
      });
    });
  });

  // 2 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await boDbBackupPage.selectPaginationLimit(page, 10);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boDbBackupPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boDbBackupPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await boDbBackupPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  describe('Bulk delete the 11 DB Backups created', async () => {
    it('should delete DB Backups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteDbBackups', baseContext);

      const result = await boDbBackupPage.deleteWithBulkActions(page);
      expect(result).to.be.equal(boDbBackupPage.successfulMultiDeleteMessage);

      const numberOfBackupsAfterDelete = await boDbBackupPage.getNumberOfElementInGrid(page);
      expect(numberOfBackupsAfterDelete).to.equal(numberOfBackups);
    });
  });
});
