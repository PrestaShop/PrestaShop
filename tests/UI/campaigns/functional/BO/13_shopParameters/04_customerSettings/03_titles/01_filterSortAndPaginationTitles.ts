import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCustomerSettingsPage,
  boDashboardPage,
  boLoginPage,
  boTitlesPage,
  boTitlesCreatePage,
  type BrowserContext,
  dataTitles,
  FakerTitle,
  type Page,
  utilsCore,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_customerSettings_titles_filterSortAndPaginationTitles';

describe('BO - Shop Parameters - Customer Settings : Filter, sort and pagination titles', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfTitles: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await utilsFile.generateImage('image.png');
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await utilsFile.deleteFile('image.png');
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Shop Parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.customerSettingsLink,
    );
    await boCustomerSettingsPage.closeSfToolBar(page);

    const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
  });

  it('should go to \'Titles\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTitlesPage', baseContext);

    await boCustomerSettingsPage.goToTitlesPage(page);

    const pageTitle = await boTitlesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boTitlesPage.pageTitle);
  });

  it('should reset all filters and get number of titles in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTitles = await boTitlesPage.resetAndGetNumberOfLines(page);
    expect(numberOfTitles).to.be.above(0);
  });

  // 1 - Filter
  describe('Filter titles', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId', filterType: 'input', filterBy: 'id_gender', filterValue: dataTitles.Mrs.id.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterType: 'input', filterBy: 'name', filterValue: dataTitles.Mrs.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterGender', filterType: 'select', filterBy: 'type', filterValue: dataTitles.Mrs.gender,
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await boTitlesPage.filterTitles(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfTitlesAfterFilter = await boTitlesPage.getNumberOfElementInGrid(page);
        expect(numberOfTitlesAfterFilter).to.be.at.most(numberOfTitles);

        const textColumn = await boTitlesPage.getTextColumn(page, 1, test.args.filterBy);
        expect(textColumn).to.contains(test.args.filterValue);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfTitlesAfterReset = await boTitlesPage.resetAndGetNumberOfLines(page);
        expect(numberOfTitlesAfterReset).to.equal(numberOfTitles);
      });
    });
  });

  // 2 - Sort
  describe('Sort titles', async () => {
    [
      {
        args:
          {
            testIdentifier: 'sortByIdDesc', sortBy: 'id_gender', sortDirection: 'desc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortBySocialTitleAsc', sortBy: 'name', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortBySocialTitleDesc', sortBy: 'name', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByIdAsc', sortBy: 'id_gender', sortDirection: 'asc', isFloat: true,
          },
      },
    ].forEach((test: { args: { testIdentifier: string, sortBy: string, sortDirection: string, isFloat?: boolean } }) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await boTitlesPage.getAllRowsColumnContent(page, test.args.sortBy);
        await boTitlesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await boTitlesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await utilsCore.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 3 - Create 9 titles
  describe('Create 9 titles', async () => {
    const creationTests: number[] = new Array(9).fill(0, 0, 9);
    creationTests.forEach((value: number, index: number) => {
      const titleToCreate: FakerTitle = new FakerTitle({name: `toSortAndPaginate${index}`, imageName: 'image.png'});

      it('should go to add new title page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewTitle${index}`, baseContext);

        await boTitlesPage.goToAddNewTitle(page);

        const pageTitle = await boTitlesCreatePage.getPageTitle(page);
        expect(pageTitle).to.eq(boTitlesCreatePage.pageTitleCreate);
      });

      it('should create title and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createTitle${index}`, baseContext);

        const textResult = await boTitlesCreatePage.createEditTitle(page, titleToCreate);
        expect(textResult).to.contains(boTitlesPage.successfulCreationMessage);

        const numberOfTitlesAfterCreation = await boTitlesPage.getNumberOfElementInGrid(page);
        expect(numberOfTitlesAfterCreation).to.be.equal(numberOfTitles + index + 1);
      });
    });
  });

  // 4 - Pagination
  describe('Pagination titles', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await boTitlesPage.selectPaginationLimit(page, 10);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boTitlesPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boTitlesPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await boTitlesPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 5 - Bulk delete
  describe('Bulk delete titles', async () => {
    it('should filter list by title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await boTitlesPage.filterTitles(page, 'input', 'name', 'toSortAndPaginate');

      const numberOfTitlesAfterFilter = await boTitlesPage.getNumberOfElementInGrid(page);
      expect(numberOfTitlesAfterFilter).to.eq(9);

      for (let i = 1; i <= numberOfTitlesAfterFilter; i++) {
        const textColumn = await boTitlesPage.getTextColumn(page, i, 'name');
        expect(textColumn).to.contains('toSortAndPaginate');
      }
    });

    it('should delete titles with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteTitles', baseContext);

      const deleteTextResult = await boTitlesPage.bulkDeleteTitles(page);
      expect(deleteTextResult).to.be.contains(boTitlesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfTitlesAfterReset = await boTitlesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTitlesAfterReset).to.be.equal(numberOfTitles);
    });
  });
});
