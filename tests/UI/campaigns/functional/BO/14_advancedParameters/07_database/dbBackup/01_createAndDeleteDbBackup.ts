// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import sqlManagerPage from '@pages/BO/advancedParameters/database/sqlManager';
import dbBackupPage from '@pages/BO/advancedParameters/database/dbBackup';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_database_dbBackup_createAndDeleteDbBackup';

/*
Generate new backup
Download file
Delete backup
 */
describe('BO - Advanced Parameters - Database : Generate db backup and download it', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let numberOfBackups: number = 0;
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

  describe('Generate new backup', async () => {
    it('should check number of db backups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDbBackups', baseContext);

      numberOfBackups = await dbBackupPage.getNumberOfElementInGrid(page);
      expect(numberOfBackups).to.equal(0);
    });

    it('should generate new db backup', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateNewDbBackup', baseContext);

      const result = await dbBackupPage.createDbDbBackup(page);
      expect(result).to.equal(dbBackupPage.successfulBackupCreationMessage);

      const numberOfBackupsAfterCreation = await dbBackupPage.getNumberOfElementInGrid(page);
      expect(numberOfBackupsAfterCreation).to.equal(numberOfBackups + 1);
    });
  });

  describe('Download db backup created', async () => {
    it('should download db backup created and check file existence', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'DownloadDbBackup', baseContext);

      filePath = await dbBackupPage.downloadDbBackup(page);

      const found = await files.doesFileExist(filePath);
      expect(found, 'Download backup file failed').to.eq(true);
    });
  });

  describe('Delete db backup created', async () => {
    it('should delete db backup created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteDbBackup', baseContext);

      const result = await dbBackupPage.deleteBackup(page, 1);
      expect(result).to.be.equal(dbBackupPage.successfulDeleteMessage);

      const numberOfBackupsAfterDelete = await dbBackupPage.getNumberOfElementInGrid(page);
      expect(numberOfBackupsAfterDelete).to.equal(numberOfBackups);
    });
  });
});
