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
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  describe('Generate new backup', async () => {
    it('should check number of db backups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDbBackups', baseContext);

      numberOfBackups = await boDbBackupPage.getNumberOfElementInGrid(page);
      expect(numberOfBackups).to.equal(0);
    });

    it('should generate new db backup', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateNewDbBackup', baseContext);

      const result = await boDbBackupPage.createDbDbBackup(page);
      expect(result).to.equal(boDbBackupPage.successfulBackupCreationMessage);

      const numberOfBackupsAfterCreation = await boDbBackupPage.getNumberOfElementInGrid(page);
      expect(numberOfBackupsAfterCreation).to.equal(numberOfBackups + 1);
    });
  });

  describe('Download db backup created', async () => {
    it('should download db backup created and check file existence', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'DownloadDbBackup', baseContext);

      filePath = await boDbBackupPage.downloadDbBackup(page);

      const found = await utilsFile.doesFileExist(filePath);
      expect(found, 'Download backup file failed').to.eq(true);
    });
  });

  describe('Delete db backup created', async () => {
    it('should delete db backup created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteDbBackup', baseContext);

      const result = await boDbBackupPage.deleteBackup(page, 1);
      expect(result).to.be.equal(boDbBackupPage.successfulDeleteMessage);

      const numberOfBackupsAfterDelete = await boDbBackupPage.getNumberOfElementInGrid(page);
      expect(numberOfBackupsAfterDelete).to.equal(numberOfBackups);
    });
  });
});
