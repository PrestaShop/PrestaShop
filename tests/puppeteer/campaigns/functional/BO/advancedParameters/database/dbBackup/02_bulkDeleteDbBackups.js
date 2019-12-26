require('module-alias/register');
const {expect} = require('chai');
const helper = require('@utils/helpers');
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
Generate 2 backups
Delete backups with bulk actions
 */
describe('Generate 2 db backup and bulk delete them', async () => {
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

  // Login into BO
  loginCommon.loginBO();

  // Go db backup page
  it('should go to database > sql manager page', async function () {
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

  it('should check number of db backups', async function () {
    numberOfBackups = await this.pageObjects.dbBackupPage.getNumberOfElementInGrid();
    await expect(numberOfBackups).to.equal(0);
  });

  describe('Generate 2 db backups', async () => {
    ['first', 'second'].forEach((test, index) => {
      it(`should generate ${test} db backup`, async function () {
        const result = await this.pageObjects.dbBackupPage.createDbDbBackup();
        await expect(result).to.equal(this.pageObjects.dbBackupPage.successfulBackupCreationMessage);
        const numberOfBackupsAfterCreation = await this.pageObjects.dbBackupPage.getNumberOfElementInGrid();
        await expect(numberOfBackupsAfterCreation).to.equal(numberOfBackups + index + 1);
      });
    });
  });

  describe('Bulk delete db backups', async () => {
    it('should delete db backups created', async function () {
      const result = await this.pageObjects.dbBackupPage.deleteWithBulkActions();
      await expect(result).to.be.equal(this.pageObjects.dbBackupPage.successfulMultiDeleteMessage);
      const numberOfBackupsAfterDelete = await this.pageObjects.dbBackupPage.getNumberOfElementInGrid();
      await expect(numberOfBackupsAfterDelete).to.equal(numberOfBackups);
    });
  });
});
