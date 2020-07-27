require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import data
const FileFaker = require('@data/faker/file');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const filesPage = require('@pages/BO/catalog/files');
const addFilePage = require('@pages/BO/catalog/files/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_files_fileBulkActions';

let browserContext;
let page;
let numberOfFiles = 0;

const firstFileData = new FileFaker({name: 'todelete'});
const secondFileData = new FileFaker({name: 'todelete'});

// Create Files and Delete with Bulk actions
describe('Create Files and Delete with Bulk actions', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await Promise.all([
      files.createFile('.', firstFileData.filename, `test ${firstFileData.filename}`),
      files.createFile('.', secondFileData.filename, `test ${secondFileData.filename}`),
    ]);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    /* Delete the generated files */
    await Promise.all([
      files.deleteFile(firstFileData.filename),
      files.deleteFile(secondFileData.filename),
    ]);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Catalog>Files\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFilesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.filesLink,
    );

    await filesPage.closeSfToolBar(page);

    const pageTitle = await filesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(filesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfFiles = await filesPage.resetAndGetNumberOfLines(page);

    if (numberOfFiles === 0) {
      await expect(numberOfFiles).to.be.equal(0);
    }

    if (numberOfFiles !== 0) {
      await expect(numberOfFiles).to.be.above(0);
    }
  });

  // 1 : Create 2 files In BO
  describe('Create 2 files in BO', async () => {
    const tests = [
      {args: {fileToCreate: firstFileData}},
      {args: {fileToCreate: secondFileData}},
    ];

    tests.forEach((test, index) => {
      it('should go to add new file page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddFilePage${index + 1}`, baseContext);

        await filesPage.goToAddNewFilePage(page);
        const pageTitle = await addFilePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addFilePage.pageTitle);
      });

      it('should create file and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createFile${index + 1}`, baseContext);

        const textResult = await addFilePage.createEditFile(page, test.args.fileToCreate);
        await expect(textResult).to.equal(filesPage.successfulCreationMessage);

        const numberOfFilesAfterCreation = await filesPage.getNumberOfElementInGrid(page);
        await expect(numberOfFilesAfterCreation).to.be.equal(numberOfFiles + index + 1);
      });
    });
  });

  // 2 : Delete Files created with bulk actions
  describe('Delete files with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await filesPage.filterTable(
        page,
        'name',
        'todelete',
      );

      const numberOfFilesAfterFilter = await filesPage.getNumberOfElementInGrid(page);
      await expect(numberOfFilesAfterFilter).to.be.equal(2);

      for (let i = 1; i <= numberOfFilesAfterFilter; i++) {
        const textColumn = await filesPage.getTextColumnFromTable(
          page,
          i,
          'name',
        );

        await expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete files with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);

      const deleteTextResult = await filesPage.deleteFilesBulkActions(page);
      await expect(deleteTextResult).to.be.equal(filesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfFilesAfterReset = await filesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfFilesAfterReset).to.be.equal(numberOfFiles);
    });
  });
});
