require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const sqlManagerPage = require('@pages/BO/advancedParameters/database/sqlManager');
const dbBackupPage = require('@pages/BO/advancedParameters/database/dbBackup');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParams_database_dbBackup_bulkDeleteDbBackups';

let browserContext;
let page;
let numberOfBackups = 0;

/*
Generate 2 backups
Delete backups with bulk actions
 */
describe('Generate 2 db backup and bulk delete them', async () => {
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
  it('should go to database > sql manager page', async function () {
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

  it('should check number of db backups', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfDbBackups', baseContext);

    numberOfBackups = await dbBackupPage.getNumberOfElementInGrid(page);
    await expect(numberOfBackups).to.equal(0);
  });

  describe('Generate 2 db backups', async () => {
    ['first', 'second'].forEach((test, index) => {
      it(`should generate ${test} db backup`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `generateNewDbBackup${index + 1}`, baseContext);

        const result = await dbBackupPage.createDbDbBackup(page);
        await expect(result).to.equal(dbBackupPage.successfulBackupCreationMessage);

        const numberOfBackupsAfterCreation = await dbBackupPage.getNumberOfElementInGrid(page);
        await expect(numberOfBackupsAfterCreation).to.equal(numberOfBackups + index + 1);
      });
    });
  });

  describe('Bulk delete db backups', async () => {
    it('should delete db backups created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteDbBackups', baseContext);

      const result = await dbBackupPage.deleteWithBulkActions(page);
      await expect(result).to.be.equal(dbBackupPage.successfulMultiDeleteMessage);

      const numberOfBackupsAfterDelete = await dbBackupPage.getNumberOfElementInGrid(page);
      await expect(numberOfBackupsAfterDelete).to.equal(numberOfBackups);
    });
  });
});
