require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const SqlManagerPage = require('@pages/BO/advancedParameters/database/sqlManager');
const DbBackupPage = require('@pages/BO/advancedParameters/database/dbBackup');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParams_database_dbBackup_createAndDeleteDbBackup';

let browser;
let browserContext;
let page;

let numberOfBackups = 0;
let filePath;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    sqlManagerPage: new SqlManagerPage(page),
    dbBackupPage: new DbBackupPage(page),
  };
};

/*
Generate new backup
Download file
Delete backup
 */
describe('Generate db backup and download it', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    browserContext = await helper.createBrowserContext(browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO
  loginCommon.loginBO();

  // Go db backup page
  it('should go to \'database > sql manager\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSqlManagerPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.advancedParametersLink,
      this.pageObjects.dashboardPage.databaseLink,
    );

    await this.pageObjects.sqlManagerPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.sqlManagerPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.sqlManagerPage.pageTitle);
  });

  it('should go to db backup page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDbBackupPage', baseContext);

    await this.pageObjects.sqlManagerPage.goToDbBackupPage();
    const pageTitle = await this.pageObjects.dbBackupPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.dbBackupPage.pageTitle);
  });

  describe('Generate new backup', async () => {
    it('should check number of db backups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDbBackups', baseContext);

      numberOfBackups = await this.pageObjects.dbBackupPage.getNumberOfElementInGrid();
      await expect(numberOfBackups).to.equal(0);
    });

    it('should generate new db backup', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateNewDbBackup', baseContext);

      const result = await this.pageObjects.dbBackupPage.createDbDbBackup();
      await expect(result).to.equal(this.pageObjects.dbBackupPage.successfulBackupCreationMessage);

      const numberOfBackupsAfterCreation = await this.pageObjects.dbBackupPage.getNumberOfElementInGrid();
      await expect(numberOfBackupsAfterCreation).to.equal(numberOfBackups + 1);
    });
  });

  describe('Download db backup created', async () => {
    it('should download db backup created and check file existence', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'DownloadDbBackup', baseContext);

      filePath = await this.pageObjects.dbBackupPage.downloadDbBackup();

      const found = await files.doesFileExist(filePath);
      await expect(found, 'Download backup file failed').to.be.true;
    });
  });

  describe('Delete db backup created', async () => {
    it('should delete db backup created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteDbBackup', baseContext);

      const result = await this.pageObjects.dbBackupPage.deleteBackup(1);
      await expect(result).to.be.equal(this.pageObjects.dbBackupPage.successfulDeleteMessage);

      const numberOfBackupsAfterDelete = await this.pageObjects.dbBackupPage.getNumberOfElementInGrid();
      await expect(numberOfBackupsAfterDelete).to.equal(numberOfBackups);
    });
  });
});
