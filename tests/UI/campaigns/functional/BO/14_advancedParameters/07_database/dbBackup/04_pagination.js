require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const sqlManagerPage = require('@pages/BO/advancedParameters/database/sqlManager');
const dbBackupPage = require('@pages/BO/advancedParameters/database/dbBackup');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_database_dbBackup_pagination';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfBackups = 0;

/*
Create 11 SQL queries
Pagination next and previous
Delete by bulk actions
 */
describe('Pagination DB backup', async () => {
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

  it('should get number of db backups', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfDbBackups', baseContext);

    numberOfBackups = await dbBackupPage.getNumberOfElementInGrid(page);
    await expect(numberOfBackups).to.equal(0);
  });

  // 1 - Create 11 DB backup
  const creationTests = new Array(11).fill(0, 0, 11);

  creationTests.forEach((test, index) => {
    describe(`Create DB backup n°${index + 1} in BO`, async () => {
      it('should generate new db backup', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `generateNewDbBackup${index}`, baseContext);

        const result = await dbBackupPage.createDbDbBackup(page);
        await expect(result).to.equal(dbBackupPage.successfulBackupCreationMessage);

        const numberOfBackupsAfterCreation = await dbBackupPage.getNumberOfElementInGrid(page);
        await expect(numberOfBackupsAfterCreation).to.equal(numberOfBackups + 1 + index);
      });
    });
  });

  // 2 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await dbBackupPage.selectPaginationLimit(page, '10');
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

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await dbBackupPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });
});
