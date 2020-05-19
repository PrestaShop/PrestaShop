require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import data
const FileFaker = require('@data/faker/file');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const FilesPage = require('@pages/BO/catalog/files');
const AddFilePage = require('@pages/BO/catalog/files/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParams_team_profiles_filterAndSortFiles';

let browser;
let page;

let numberOfFiles = 0;

const firstFileData = new FileFaker();
const secondFileData = new FileFaker();
const thirdFileData = new FileFaker();

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    filesPage: new FilesPage(page),
    addFilePage: new AddFilePage(page),
  };
};

describe('Filter and sort files', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);

    this.pageObjects = await init();

    await Promise.all([
      files.createFile('.', firstFileData.filename, `test ${firstFileData.filename}`),
      files.createFile('.', secondFileData.filename, `test ${secondFileData.filename}`),
      files.createFile('.', thirdFileData.filename, `test ${thirdFileData.filename}`),
    ]);
  });
  after(async () => {
    await helper.closeBrowser(browser);

    /* Delete the generated files */
    await Promise.all([
      files.deleteFile(firstFileData.filename),
      files.deleteFile(secondFileData.filename),
      files.deleteFile(thirdFileData.filename),
    ]);
  });

  // Login into BO and go to files page
  loginCommon.loginBO();

  it('should go to \'Catalog > Files\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFilesPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.filesLink,
    );

    await this.pageObjects.filesPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.filesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.filesPage.pageTitle);
  });
  // 1: Create 3 files
  describe('Create 3 files', async () => {
    const tests = [
      {args: {fileToCreate: firstFileData}},
      {args: {fileToCreate: secondFileData}},
      {args: {fileToCreate: thirdFileData}},
    ];

    tests.forEach((test, index) => {
      it('should go to new file page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewFilePage${index}`, baseContext);

        await this.pageObjects.filesPage.goToAddNewFilePage();
        const pageTitle = await this.pageObjects.addFilePage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addFilePage.pageTitle);
      });

      it('should create file', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createFile${index}`, baseContext);

        const result = await this.pageObjects.addFilePage.createEditFile(test.args.fileToCreate);
        await expect(result).to.equal(this.pageObjects.filesPage.successfulCreationMessage);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfFiles = await this.pageObjects.filesPage.resetAndGetNumberOfLines();
      await expect(numberOfFiles).to.be.above(0);
    });
  });

  // 2 : Filter files table
  describe('Filter files in BO', async () => {
    const filterTests = [
      {
        args: {
          testIdentifier: 'filterId', filterType: 'input', filterBy: 'id_attachment', filterValue: '2',
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterType: 'input', filterBy: 'name', filterValue: secondFileData.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterSize', filterType: 'input', filterBy: 'file_size', filterValue: '64',
        },
      },
      {
        args: {
          testIdentifier: 'filterProducts', filterType: 'input', filterBy: 'products', filterValue: '1',
        },
      },
    ];
    filterTests.forEach((test) => {
      it(`should filter list by ${test.args.filterBy}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        await this.pageObjects.filesPage.filterTable(test.args.filterBy, test.args.filterValue);

        const numberOfFilesAfterFilter = await this.pageObjects.filesPage.getNumberOfElementInGrid();
        await expect(numberOfFilesAfterFilter).to.be.at.most(numberOfFiles);

        for (let i = 1; i <= numberOfFilesAfterFilter; i++) {
          const textName = await this.pageObjects.filesPage.getTextColumnFromTable(i, test.args.filterBy);
          await expect(textName).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

        const numberOfFilesAfterReset = await this.pageObjects.filesPage.resetAndGetNumberOfLines();
        await expect(numberOfFilesAfterReset).to.be.equal(numberOfFiles);
      });
    });
  });

  // 3 : Sort files table
  describe('Sort files', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_attachment', sortDirection: 'desc', isFloat: true,
        },
      },
      {args: {testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByFileNameAsc', sortBy: 'file', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByFileNameDesc', sortBy: 'file', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortBySizeAsc', sortBy: 'file_size', sortDirection: 'asc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortBySizeDesc', sortBy: 'file_size', sortDirection: 'desc', isFloat: true,
        },
      },
      {args: {testIdentifier: 'sortByProductsAsc', sortBy: 'products', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByProductsDesc', sortBy: 'products', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_attachment', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(
        `should sort files by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`,

        async function () {
          await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

          let nonSortedTable = await this.pageObjects.filesPage.getAllRowsColumnContent(test.args.sortBy);

          await this.pageObjects.filesPage.sortTable(test.args.sortBy, test.args.sortDirection);

          let sortedTable = await this.pageObjects.filesPage.getAllRowsColumnContent(test.args.sortBy);

          if (test.args.isFloat) {
            nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
            sortedTable = await sortedTable.map(text => parseFloat(text));
          }

          const expectedResult = await this.pageObjects.filesPage.sortArray(nonSortedTable, test.args.isFloat);

          if (test.args.sortDirection === 'asc') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        },
      );
    });
  });

  // 4 : Delete the created files
  describe('Delete created files with Bulk Actions', async () => {
    it('should delete files with Bulk Actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);

      const deleteTextResult = await this.pageObjects.filesPage.deleteFilesBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.filesPage.successfulMultiDeleteMessage);
    });
  });
});
