require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import data
const FileFaker = require('@data/faker/file');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const filesPage = require('@pages/BO/catalog/files');
const addFilePage = require('@pages/BO/catalog/files/add');

const baseContext = 'functional_BO_advancedParams_team_profiles_filterSortPaginationAndBulkActionsFile';

let browserContext;
let page;

let numberOfFiles = 0;

/*
Create 11 files
Filter files
Paginate between pages
Sort files table
Delete files with bulk actions
 */
describe('BO - Catalog - Files : Filter, sort, pagination and bulk actions files table', async () => {
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

  it('should go to \'Catalog > Files\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFilesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.filesLink,
    );
    await dashboardPage.closeSfToolBar(page);

    numberOfFiles = await filesPage.resetAndGetNumberOfLines(page);

    const pageTitle = await filesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(filesPage.pageTitle);
  });

  // 1: Create 11 files
  describe('Create 11 files in BO', async () => {
    const creationTests = new Array(11).fill(0, 0, 11);
    creationTests.forEach((test, index) => {
      const createFileData = new FileFaker({name: `todelete${index}`});
      before(() => files.createFile('.', createFileData.filename, `test ${createFileData.filename}`));

      it('should go to new file page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewFilePage${index}`, baseContext);

        await filesPage.goToAddNewFilePage(page);
        const pageTitle = await addFilePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addFilePage.pageTitle);
      });

      it(`should create file n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createFile${index}`, baseContext);

        const result = await addFilePage.createEditFile(page, createFileData);
        await expect(result).to.equal(filesPage.successfulCreationMessage);

        const numberOfFilesAfterCreation = await filesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfFilesAfterCreation).to.be.equal(numberOfFiles + 1 + index);
      });

      after(() => files.deleteFile(createFileData.filename));
    });
  });
  // 2 : Filter files table
  describe('Filter files table', async () => {
    const filterTests = [
      {
        args: {
          testIdentifier: 'filterId', filterType: 'input', filterBy: 'id_attachment', filterValue: '2',
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterType: 'input', filterBy: 'name', filterValue: 'todelete',
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

        await filesPage.filterTable(page, test.args.filterBy, test.args.filterValue);
        const numberOfFilesAfterFilter = await filesPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfFilesAfterFilter; i++) {
          const textName = await filesPage.getTextColumnFromTable(page, i, test.args.filterBy);
          await expect(textName).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfFilesAfterReset = await filesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfFilesAfterReset).to.be.equal(numberOfFiles + 11);
      });
    });
  });

  // 3 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo10', baseContext);

      const paginationNumber = await filesPage.selectPaginationLimit(page, '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await filesPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await filesPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await filesPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 4 : Sort files table
  describe('Sort files table', async () => {
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

          let nonSortedTable = await filesPage.getAllRowsColumnContent(page, test.args.sortBy);
          await filesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

          let sortedTable = await filesPage.getAllRowsColumnContent(page, test.args.sortBy);

          if (test.args.isFloat) {
            nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
            sortedTable = await sortedTable.map(text => parseFloat(text));
          }

          const expectedResult = await filesPage.sortArray(nonSortedTable, test.args.isFloat);

          if (test.args.sortDirection === 'asc') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        },
      );
    });
  });

  // 5 : Delete the created files
  describe('Delete created files with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await filesPage.filterTable(page, 'name', 'todelete');
      const numberOfFilesAfterFilter = await filesPage.getNumberOfElementInGrid(page);
      await expect(numberOfFilesAfterFilter).to.be.above(0);

      for (let i = 1; i <= numberOfFilesAfterFilter; i++) {
        const textColumn = await filesPage.getTextColumnFromTable(
          page,
          i,
          'name',
        );
        await expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete files with Bulk Actions', async function () {
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
