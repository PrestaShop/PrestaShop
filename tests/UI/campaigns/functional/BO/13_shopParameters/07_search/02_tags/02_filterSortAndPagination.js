require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import common tests
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const searchPage = require('@pages/BO/shopParameters/search');
const tagsPage = require('@pages/BO/shopParameters/search/tags');
const addTagPage = require('@pages/BO/shopParameters/search/tags/add');

// Import data
const TagFaker = require('@data/faker/tag');
const {Languages} = require('@data/demo/languages');

const baseContext = 'functional_BO_shopParameters_search_tags_filterSortAndPagination';

// Browser and tab
let browserContext;
let page;
let numberOfTags = 0;

/*
Create 21 tags
Filter tags by : Id, Language, Name, Products
Sort tags by : Id, Language, Name, Products
Pagination next and previous
Delete by bulk actions
 */
describe('BO - Shop Parameters - Search : Filter, sort and pagination tag in BO', async () => {
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
    await expect(pageTitle).to.contains(searchPage.pageTitle);
  });

  it('should go to \'Tags\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTagsPage', baseContext);

    await searchPage.goToTagsPage(page);

    numberOfTags = await tagsPage.getNumberOfElementInGrid(page);

    const pageTitle = await tagsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(tagsPage.pageTitle);
  });

  // 1 - Create tag
  describe('Create 21 tags in BO', async () => {
    const creationTests = new Array(21).fill(0, 0, 21);

    creationTests.forEach((test, index) => {
      const tagData = new TagFaker({name: `todelete${index}`, language: Languages.english.name});

      it('should go to add new tag page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddTagPage${index}`, baseContext);

        await tagsPage.goToAddNewTagPage(page);

        const pageTitle = await addTagPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addTagPage.pageTitleCreate);
      });

      it(`should create tag n° ${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createTag${index}`, baseContext);

        const textResult = await addTagPage.setTag(page, tagData);
        await expect(textResult).to.contains(tagsPage.successfulCreationMessage);

        const numberOfElementAfterCreation = await tagsPage.getNumberOfElementInGrid(page);
        await expect(numberOfElementAfterCreation).to.be.equal(numberOfTags + 1 + index);
      });
    });
  });

  // 2 - Filter tags
  describe('Filter tags table', async () => {
    const tests = [
      {args: {testIdentifier: 'filterById', filterBy: 'id_tag', filterValue: 5}},
      {args: {testIdentifier: 'filterByLanguage', filterBy: 'l!name', filterValue: Languages.english.name}},
      {args: {testIdentifier: 'filterByName', filterBy: 'a!name', filterValue: 'todelete10'}},
      {args: {testIdentifier: 'filterByProducts', filterBy: 'products', filterValue: 0}},
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await tagsPage.filterTable(page, test.args.filterBy, test.args.filterValue);

        const numberOfLinesAfterFilter = await tagsPage.getNumberOfElementInGrid(page);
        await expect(numberOfLinesAfterFilter).to.be.at.most(numberOfTags + 21);

        for (let row = 1; row <= numberOfLinesAfterFilter; row++) {
          const textColumn = await tagsPage.getTextColumn(page, row, test.args.filterBy);
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfLinesAfterReset = await tagsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfLinesAfterReset).to.equal(numberOfTags + 21);
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

        let nonSortedTable = await tagsPage.getAllRowsColumnContent(page, test.args.sortBy);

        await tagsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await tagsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await tagsPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'up') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 4 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await tagsPage.selectPaginationLimit(page, '20');
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

      const paginationNumber = await tagsPage.selectPaginationLimit(page, '50');
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
        await expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete tags with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteTags', baseContext);

      const deleteTextResult = await tagsPage.bulkDelete(page);
      await expect(deleteTextResult).to.be.contains(tagsPage.successfulMultiDeleteMessage);
    });
  });
});
