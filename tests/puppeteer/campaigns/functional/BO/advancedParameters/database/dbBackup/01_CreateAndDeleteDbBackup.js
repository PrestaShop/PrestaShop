require('module-alias/register');
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const SqlManagerPage = require('@pages/BO/advancedParameters/database/sqlManager');
const DbBackupPage = require('@pages/BO/advancedParameters/database/dbBackup');

let browser;
let page;
let numberOfBackups = 0;
let dbBackupFilename = '';

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
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
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
    await files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${dbBackupFilename}`);
  });

  // Login into BO
  loginCommon.loginBO();

  // Go db backup page
  it('should go to database > sql manager  page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.advancedParametersLink,
      this.pageObjects.boBasePage.databaseLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.sqlManagerPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.sqlManagerPage.pageTitle);
  });

  it('should go to db backup page', async function () {
    await this.pageObjects.sqlManagerPage.goToDbBackupPage();
    const pageTitle = await this.pageObjects.dbBackupPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.dbBackupPage.pageTitle);
  });

  describe('Generate new backup', async () => {
    it('should check number of db backups', async function () {
      numberOfBackups = await this.pageObjects.dbBackupPage.getNumberOfElementInGrid();
      await expect(numberOfBackups).to.equal(0);
    });

    it('should generate new db backup', async function () {
      const result = await this.pageObjects.dbBackupPage.createDbDbBackup();
      await expect(result).to.equal(this.pageObjects.dbBackupPage.successfulBackupCreationMessage);
      const numberOfBackupsAfterCreation = await this.pageObjects.dbBackupPage.getNumberOfElementInGrid();
      await expect(numberOfBackupsAfterCreation).to.equal(numberOfBackups + 1);
    });
  });

  describe('Download db backup created', async () => {
    it('should download db backup created and check file existence', async function () {
      dbBackupFilename = await this.pageObjects.dbBackupPage.getBackupFilename(1);
      await this.pageObjects.dbBackupPage.downloadDbBackup();
      const found = await files.checkFileExistence(dbBackupFilename);
      await expect(found, 'Download backup file failed').to.be.true;
    });
  });

  describe('Delete db backup created', async () => {
    it('should delete db backup created', async function () {
      const result = await this.pageObjects.dbBackupPage.deleteBackup(1);
      await expect(result).to.be.equal(this.pageObjects.dbBackupPage.successfulDeleteMessage);
      const numberOfBackupsAfterDelete = await this.pageObjects.dbBackupPage.getNumberOfElementInGrid();
      await expect(numberOfBackupsAfterDelete).to.equal(numberOfBackups);
    });
  });
});
