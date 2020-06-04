require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const SqlManagerPage = require('@pages/BO/advancedParameters/database/sqlManager');
const DbBackupPage = require('@pages/BO/advancedParameters/database/dbBackup');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParams_database_dbBackup_bulkDeleteDbBackups';


let browserContext;
let page;
let numberOfBackups = 0;

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
Generate 2 backups
Delete backups with bulk actions
 */
describe('Generate 2 db backup and bulk delete them', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO
  loginCommon.loginBO();

  // Go db backup page
  it('should go to database > sql manager page', async function () {
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

  it('should check number of db backups', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDbBackups', baseContext);

    numberOfBackups = await this.pageObjects.dbBackupPage.getNumberOfElementInGrid();
    await expect(numberOfBackups).to.equal(0);
  });

  describe('Generate 2 db backups', async () => {
    ['first', 'second'].forEach((test, index) => {
      it(`should generate ${test} db backup`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `generateNewDbBackup${index + 1}`, baseContext);

        const result = await this.pageObjects.dbBackupPage.createDbDbBackup();
        await expect(result).to.equal(this.pageObjects.dbBackupPage.successfulBackupCreationMessage);

        const numberOfBackupsAfterCreation = await this.pageObjects.dbBackupPage.getNumberOfElementInGrid();
        await expect(numberOfBackupsAfterCreation).to.equal(numberOfBackups + index + 1);
      });
    });
  });

  describe('Bulk delete db backups', async () => {
    it('should delete db backups created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteDbBackups', baseContext);

      const result = await this.pageObjects.dbBackupPage.deleteWithBulkActions();
      await expect(result).to.be.equal(this.pageObjects.dbBackupPage.successfulMultiDeleteMessage);

      const numberOfBackupsAfterDelete = await this.pageObjects.dbBackupPage.getNumberOfElementInGrid();
      await expect(numberOfBackupsAfterDelete).to.equal(numberOfBackups);
    });
  });
});
