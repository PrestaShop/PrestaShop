// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import pagesPage from '@pages/BO/design/pages';
import addPageCategoryPage from '@pages/BO/design/pages/pageCategory/add';

// Import data
import CMSCategoryData from '@data/faker/CMScategory';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_pages_categories_paginationAndSortCategories';

/*
Create 11 categories
Paginate between pages
Sort categories table by id, name, description, position
Delete categories with bulk actions
 */
describe('BO - Design - Pages : Pagination and sort categories table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCategories: number = 0;

  const categoriesTableName: string = 'cms_page_category';

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

  // Go to Design>Pages page
  it('should go to \'Design > Pages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCmsPagesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.pagesLink,
    );
    await pagesPage.closeSfToolBar(page);

    const pageTitle = await pagesPage.getPageTitle(page);
    expect(pageTitle).to.contains(pagesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCategories = await pagesPage.resetAndGetNumberOfLines(page, categoriesTableName);
    if (numberOfCategories !== 0) expect(numberOfCategories).to.be.above(0);
  });

  // 1 : Create 11 categories
  describe('Create 11 categories in BO', async () => {
    const tests: number[] = new Array(11).fill(0, 0, 11);
    tests.forEach((test: number, index: number) => {
      const createCategoryData: CMSCategoryData = new CMSCategoryData({name: `todelete${index}`});

      it('should go to add new page category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewPageCategoryPage${index}`, baseContext);

        await pagesPage.goToAddNewPageCategory(page);

        const pageTitle = await addPageCategoryPage.getPageTitle(page);
        expect(pageTitle).to.contains(addPageCategoryPage.pageTitleCreate);
      });

      it(`should create category n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreatePageCategory${index}`, baseContext);

        const textResult = await addPageCategoryPage.createEditPageCategory(page, createCategoryData);
        expect(textResult).to.equal(pagesPage.successfulCreationMessage);
      });

      it('should go back to categories list', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToCategories${index}`, baseContext);

        await pagesPage.backToList(page);

        const pageTitle = await pagesPage.getPageTitle(page);
        expect(pageTitle).to.contains(pagesPage.pageTitle);
      });

      it('should check the categories number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkCategoriesNumber${index}`, baseContext);

        const numberOfCategoriesAfterCreation = await pagesPage.getNumberOfElementInGrid(
          page,
          categoriesTableName,
        );
        expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1 + index);
      });
    });
  });

  // 2 : Test pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo10', baseContext);

      const paginationNumber = await pagesPage.selectCategoryPaginationLimit(page, 10);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await pagesPage.paginationCategoryNext(page);
      expect(paginationNumber).to.contain('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await pagesPage.paginationCategoryPrevious(page);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await pagesPage.selectCategoryPaginationLimit(page, 50);
      expect(paginationNumber).to.contain('(page 1 / 1)');
    });
  });

  // 3 : Sort categories table
  describe('Sort categories table', async () => {
    const sortTests = [
      {
        args:
          {
            testIdentifier: 'sortByIdDesc', sortBy: 'id_cms_category', sortDirection: 'desc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByPositionDesc', sortBy: 'position', sortDirection: 'desc', isFloat: true,
          },
      },
      {args: {testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortBNameDesc', sortBy: 'name', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByDescriptionAsc', sortBy: 'description', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByDescriptionDesc', sortBy: 'description', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByStatusAsc', sortBy: 'active', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByStatusDesc', sortBy: 'active', sortDirection: 'desc'}},
      {
        args:
          {
            testIdentifier: 'sortByPositionAsc', sortBy: 'position', sortDirection: 'asc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByIdAsc', sortBy: 'id_cms_category', sortDirection: 'asc', isFloat: true,
          },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await pagesPage.getAllRowsColumnContentTableCmsPageCategory(
          page,
          test.args.sortBy,
        );
        await pagesPage.sortTableCmsPageCategory(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await pagesPage.getAllRowsColumnContentTableCmsPageCategory(
          page,
          test.args.sortBy,
        );

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult: number[] = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 4 : Delete the 11 categories with bulk actions
  describe('Delete categories with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await pagesPage.filterTable(page, categoriesTableName, 'input', 'name', 'todelete');

      const textResult = await pagesPage.getTextColumnFromTableCmsPageCategory(page, 1, 'name');
      expect(textResult).to.contains('todelete');
    });

    it('should delete categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCategories', baseContext);

      const deleteTextResult = await pagesPage.deleteWithBulkActions(page, categoriesTableName);
      expect(deleteTextResult).to.be.equal(pagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCategoriesAfterFilter = await pagesPage.resetAndGetNumberOfLines(
        page,
        categoriesTableName,
      );
      expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
    });
  });
});
