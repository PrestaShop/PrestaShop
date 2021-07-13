require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import data
const PageFaker = require('@data/faker/CMSpage');

// Import pages
const dashboardPage = require('@pages/BO/dashboard/index');
const pagesPage = require('@pages/BO/design/pages/index');
const addPagePage = require('@pages/BO/design/pages/add');

const baseContext = 'functional_BO_design_pages_pages_paginationAndSortPages';

let browserContext;
let page;
let numberOfPages = 0;

/*
Create 11 pages
Paginate between pages
Sort pages table by id, url, title, position
Delete pages with bulk actions
 */
describe('BO - design - Pages : Pagination and sort Pages table', async () => {
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

  it('should go to \'Design > Pages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCmsPagesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.pagesLink,
    );

    await pagesPage.closeSfToolBar(page);

    const pageTitle = await pagesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(pagesPage.pageTitle);
  });

  it('should reset all filters and get number of pages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfPages = await pagesPage.resetAndGetNumberOfLines(page, 'cms_page');
    if (numberOfPages !== 0) {
      await expect(numberOfPages).to.be.above(0);
    }
  });

  // 1 : Create 11 pages
  describe('Create 11 pages in BO', async () => {
    const tests = new Array(11).fill(0, 0, 11);
    tests.forEach((test, index) => {
      const createPageData = new PageFaker({title: `todelete${index}`});

      it('should go to add new page page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewPagePage${index}`, baseContext);

        await pagesPage.goToAddNewPage(page);
        const pageTitle = await addPagePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addPagePage.pageTitleCreate);
      });

      it(`should create page n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createPage${index}`, baseContext);

        const textResult = await addPagePage.createEditPage(page, createPageData);
        await expect(textResult).to.equal(pagesPage.successfulCreationMessage);
      });
    });

    it('should check the pages number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPagesNumber', baseContext);

      const numberOfPagesAfterCreation = await pagesPage.getNumberOfElementInGrid(page, 'cms_page');
      await expect(numberOfPagesAfterCreation).to.be.equal(numberOfPages + 11);
    });
  });

  // 2 : Test pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo10', baseContext);

      const paginationNumber = await pagesPage.selectPagesPaginationLimit(page, '10');
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await pagesPage.paginationPagesNext(page);
      expect(paginationNumber).to.contain('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await pagesPage.paginationPagesPrevious(page);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await pagesPage.selectPagesPaginationLimit(page, '50');
      expect(paginationNumber).to.contain('(page 1 / 1)');
    });
  });

  // 3 : Sort pages table
  describe('Sort pages table', async () => {
    const sortTests = [
      {
        args:
          {
            testIdentifier: 'sortByPositionDesc', sortBy: 'position', sortDirection: 'desc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByIdAsc', sortBy: 'id_cms', sortDirection: 'asc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByIdDesc', sortBy: 'id_cms', sortDirection: 'desc', isFloat: true,
          },
      },
      {args: {testIdentifier: 'sortByUrlAsc', sortBy: 'link_rewrite', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByUrlDesc', sortBy: 'link_rewrite', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByTitleAsc', sortBy: 'meta_title', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByTitleDesc', sortBy: 'meta_title', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByStatusAsc', sortBy: 'active', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByStatusDesc', sortBy: 'active', sortDirection: 'desc'}},
      {
        args:
          {
            testIdentifier: 'sortByPositionAsc', sortBy: 'position', sortDirection: 'asc', isFloat: true,
          },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await pagesPage.getAllRowsColumnContentTableCmsPage(page, test.args.sortBy);
        await pagesPage.sortTableCmsPage(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await pagesPage.getAllRowsColumnContentTableCmsPage(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await pagesPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 4 : Delete the 11 pages with bulk actions
  describe('Delete pages with Bulk Actions', async () => {
    it('should filter list by title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await pagesPage.filterTable(page, 'cms_page', 'input', 'meta_title', 'todelete');

      const textResult = await pagesPage.getTextColumnFromTableCmsPage(page, 1, 'meta_title');
      await expect(textResult).to.contains('todelete');
    });

    it('should delete pages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);

      const deleteTextResult = await pagesPage.deleteWithBulkActions(page, 'cms_page');
      await expect(deleteTextResult).to.be.equal(pagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfPagesAfterFilter = await pagesPage.resetAndGetNumberOfLines(page, 'cms_page');
      await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages);
    });
  });
});
