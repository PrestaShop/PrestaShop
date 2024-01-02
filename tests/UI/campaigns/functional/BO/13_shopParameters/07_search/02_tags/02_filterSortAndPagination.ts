// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import searchPage from '@pages/BO/shopParameters/search';
import tagsPage from '@pages/BO/shopParameters/search/tags';
import addTagPage from '@pages/BO/shopParameters/search/tags/add';

// Import data
import Languages from '@data/demo/languages';
import TagData from '@data/faker/tag';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_search_tags_filterSortAndPagination';

/*
Create 21 tags
Filter tags by : Id, Language, Name, Products
Sort tags by : Id, Language, Name, Products
Pagination next and previous
Delete by bulk actions
 */
describe('BO - Shop Parameters - Search : Filter, sort and pagination tag in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfTags: number = 0;

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

  it('should go to \'ShopParameters > Search\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.searchLink,
    );

    const pageTitle = await searchPage.getPageTitle(page);
    expect(pageTitle).to.contains(searchPage.pageTitle);
  });

  it('should go to \'Tags\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTagsPage', baseContext);

    await searchPage.goToTagsPage(page);
    numberOfTags = await tagsPage.getNumberOfElementInGrid(page);

    const pageTitle = await tagsPage.getPageTitle(page);
    expect(pageTitle).to.contains(tagsPage.pageTitle);
  });

  // 1 - Create tag
  describe('Create 21 tags in BO', async () => {
    const creationTests: number[] = new Array(21).fill(0, 0, 21);

    creationTests.forEach((test: number, index: number) => {
      const tagData: TagData = new TagData({name: `todelete${index}`, language: Languages.english.name});

      it('should go to add new tag page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddTagPage${index}`, baseContext);

        await tagsPage.goToAddNewTagPage(page);

        const pageTitle = await addTagPage.getPageTitle(page);
        expect(pageTitle).to.contains(addTagPage.pageTitleCreate);
      });

      it(`should create tag n° ${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createTag${index}`, baseContext);

        const textResult = await addTagPage.setTag(page, tagData);
        expect(textResult).to.contains(tagsPage.successfulCreationMessage);

        const numberOfElementAfterCreation = await tagsPage.getNumberOfElementInGrid(page);
        expect(numberOfElementAfterCreation).to.be.equal(numberOfTags + 1 + index);
      });
    });
  });

  // 2 - Filter tags
  describe('Filter tags table', async () => {
    const tests = [
      {args: {testIdentifier: 'filterById', filterBy: 'id_tag', filterValue: '5'}},
      {args: {testIdentifier: 'filterByLanguage', filterBy: 'l!name', filterValue: Languages.english.name}},
      {args: {testIdentifier: 'filterByName', filterBy: 'a!name', filterValue: 'todelete10'}},
      {args: {testIdentifier: 'filterByProducts', filterBy: 'products', filterValue: '0'}},
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await tagsPage.filterTable(page, test.args.filterBy, test.args.filterValue);

        const numberOfLinesAfterFilter = await tagsPage.getNumberOfElementInGrid(page);
        expect(numberOfLinesAfterFilter).to.be.at.most(numberOfTags + 21);

        for (let row = 1; row <= numberOfLinesAfterFilter; row++) {
          const textColumn = await tagsPage.getTextColumn(page, row, test.args.filterBy);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfLinesAfterReset = await tagsPage.resetAndGetNumberOfLines(page);
        expect(numberOfLinesAfterReset).to.equal(numberOfTags + 21);
      });
    });
  });

  // 3 - Sort tags table
  describe('Sort tags table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_tag', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByLanguageAsc', sortBy: 'l!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByLanguageDesc', sortBy: 'l!name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameAsc', sortBy: 'a!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc', sortBy: 'a!name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByProductAsc', sortBy: 'products', sortDirection: 'up', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByProductDesc', sortBy: 'products', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_tag', sortDirection: 'up', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await tagsPage.getAllRowsColumnContent(page, test.args.sortBy);

        await tagsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await tagsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult: number[] = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'up') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'up') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 4 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await tagsPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await tagsPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await tagsPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await tagsPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.equal('1');
    });
  });

  // 5 : Delete tags created by bulk actions
  describe('Delete tags with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await tagsPage.filterTable(page, 'a!name', 'todelete');

      const numberOfLinesAfterFilter = await tagsPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfLinesAfterFilter; i++) {
        const textColumn = await tagsPage.getTextColumn(page, i, 'a!name');
        expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete tags with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteTags', baseContext);

      const deleteTextResult = await tagsPage.bulkDelete(page);
      expect(deleteTextResult).to.be.contains(tagsPage.successfulMultiDeleteMessage);
    });
  });
});
