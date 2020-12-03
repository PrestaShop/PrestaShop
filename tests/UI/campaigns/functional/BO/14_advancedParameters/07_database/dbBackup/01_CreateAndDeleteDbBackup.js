require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const sqlManagerPage = require('@pages/BO/advancedParameters/database/sqlManager');
const dbBackupPage = require('@pages/BO/advancedParameters/database/dbBackup');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParams_database_dbBackup_createAndDeleteDbBackup';

let browserContext;
let page;

let numberOfBackups = 0;
let filePath;

/*
Generate new backup
Download file
Delete backup
 */
describe('Generate db backup and download it', async () => {
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
  it('should go to \'database > sql manager\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSqlManagerPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.databaseLink,
    );

    await sqlManagerPage.closeSfToolBar(page);

    const pageTitle = await sqlManagerPage.getPageTitle(page);
    await expect(pageTitle).to.contains(sqlManagerPage.pageTitle);
  });

  it('should go to db backup page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDbBackupPage', baseContext);

    await sqlManagerPage.goToDbBackupPage(page);
    const pageTitle = await dbBackupPage.getPageTitle(page);
    await expect(pageTitle).to.contains(dbBackupPage.pageTitle);
  });

  describe('Generate new backup', async () => {
    it('should check number of db backups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDbBackups', baseContext);

      numberOfBackups = await dbBackupPage.getNumberOfElementInGrid(page);
      await expect(numberOfBackups).to.equal(0);
    });

    it('should generate new db backup', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateNewDbBackup', baseContext);

      const result = await dbBackupPage.createDbDbBackup(page);
      await expect(result).to.equal(dbBackupPage.successfulBackupCreationMessage);

      const numberOfBackupsAfterCreation = await dbBackupPage.getNumberOfElementInGrid(page);
      await expect(numberOfBackupsAfterCreation).to.equal(numberOfBackups + 1);
    });
  });

  describe('Download db backup created', async () => {
    it('should download db backup created and check file existence', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'DownloadDbBackup', baseContext);

      filePath = await dbBackupPage.downloadDbBackup(page);

      const found = await files.doesFileExist(filePath);
      await expect(found, 'Download backup file failed').to.be.true;
    });
  });

  describe('Delete db backup created', async () => {
    it('should delete db backup created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteDbBackup', baseContext);

      const result = await dbBackupPage.deleteBackup(page, 1);
      await expect(result).to.be.equal(dbBackupPage.successfulDeleteMessage);

      const numberOfBackupsAfterDelete = await dbBackupPage.getNumberOfElementInGrid(page);
      await expect(numberOfBackupsAfterDelete).to.equal(numberOfBackups);
    });
  });
});
