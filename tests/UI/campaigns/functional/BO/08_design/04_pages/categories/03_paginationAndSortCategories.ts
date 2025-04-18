import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCMSPageCategoriesCreatePage,
  boCMSPagesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerCMSCategory,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  // Go to Design>Pages page
  it('should go to \'Design > Pages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCmsPagesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.pagesLink,
    );
    await boCMSPagesPage.closeSfToolBar(page);

    const pageTitle = await boCMSPagesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCategories = await boCMSPagesPage.resetAndGetNumberOfLines(page, categoriesTableName);
    if (numberOfCategories !== 0) expect(numberOfCategories).to.be.above(0);
  });

  // 1 : Create 11 categories
  describe('Create 11 categories in BO', async () => {
    const tests: number[] = new Array(11).fill(0, 0, 11);
    tests.forEach((test: number, index: number) => {
      const createCategoryData: FakerCMSCategory = new FakerCMSCategory({name: `todelete${index}`});

      it('should go to add new page category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewPageCategoryPage${index}`, baseContext);

        await boCMSPagesPage.goToAddNewPageCategory(page);

        const pageTitle = await boCMSPageCategoriesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCMSPageCategoriesCreatePage.pageTitleCreate);
      });

      it(`should create category n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreatePageCategory${index}`, baseContext);

        const textResult = await boCMSPageCategoriesCreatePage.createEditPageCategory(page, createCategoryData);
        expect(textResult).to.equal(boCMSPagesPage.successfulCreationMessage);
      });

      it('should go back to categories list', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToCategories${index}`, baseContext);

        await boCMSPagesPage.backToList(page);

        const pageTitle = await boCMSPagesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
      });

      it('should check the categories number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkCategoriesNumber${index}`, baseContext);

        const numberOfCategoriesAfterCreation = await boCMSPagesPage.getNumberOfElementInGrid(
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

      const paginationNumber = await boCMSPagesPage.selectCategoryPaginationLimit(page, 10);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boCMSPagesPage.paginationCategoryNext(page);
      expect(paginationNumber).to.contain('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boCMSPagesPage.paginationCategoryPrevious(page);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await boCMSPagesPage.selectCategoryPaginationLimit(page, 50);
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

        const nonSortedTable = await boCMSPagesPage.getAllRowsColumnContentTableCmsPageCategory(
          page,
          test.args.sortBy,
        );
        await boCMSPagesPage.sortTableCmsPageCategory(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await boCMSPagesPage.getAllRowsColumnContentTableCmsPageCategory(
          page,
          test.args.sortBy,
        );

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult: number[] = await utilsCore.sortArrayNumber(nonSortedTableFloat);

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

  // 4 : Delete the 11 categories with bulk actions
  describe('Delete categories with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boCMSPagesPage.filterTable(page, categoriesTableName, 'input', 'name', 'todelete');

      const textResult = await boCMSPagesPage.getTextColumnFromTableCmsPageCategory(page, 1, 'name');
      expect(textResult).to.contains('todelete');
    });

    it('should delete categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCategories', baseContext);

      const deleteTextResult = await boCMSPagesPage.deleteWithBulkActions(page, categoriesTableName);
      expect(deleteTextResult).to.be.equal(boCMSPagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCategoriesAfterFilter = await boCMSPagesPage.resetAndGetNumberOfLines(
        page,
        categoriesTableName,
      );
      expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
    });
  });
});
