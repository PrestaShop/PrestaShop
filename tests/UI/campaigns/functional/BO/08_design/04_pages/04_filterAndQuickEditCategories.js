require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const CategoryPageFaker = require('@data/faker/CMScategory');

// Import pages
const dashboardPage = require('@pages/BO/dashboard/index');
const pagesPage = require('@pages/BO/design/pages/index');
const addPageCategoryPage = require('@pages/BO/design/pages/pageCategory/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_design_pages_filterAndQuickEditCategories';


let browserContext;
let page;
let numberOfCategories = 0;

const firstCategoryData = new CategoryPageFaker();
const secondCategoryData = new CategoryPageFaker();

// Filter And Quick Edit Pages
describe('Filter And Quick Edit Categories', async () => {
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
  it('should go to "Design>Pages" page', async function () {
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

  // 1 : Create two categories and Filter with all inputs and selects in grid table
  describe('Create 2 categories then filter the table', async () => {
    // Create 2 categories
    describe('Create Categories', async () => {
      const categoriesToCreate = [firstCategoryData, secondCategoryData];

      categoriesToCreate.forEach((categoryToCreate, index) => {
        it('should go to add new page category', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToAddCategory${index + 1}`, baseContext);

          await pagesPage.goToAddNewPageCategory(page);
          const pageTitle = await addPageCategoryPage.getPageTitle(page);
          await expect(pageTitle).to.contains(addPageCategoryPage.pageTitleCreate);
        });

        it('should create category ', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createCategory${index + 1}`, baseContext);

          const textResult = await addPageCategoryPage.createEditPageCategory(page, categoryToCreate);
          await expect(textResult).to.equal(pagesPage.successfulCreationMessage);
        });

        it('should go back to categories list', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `backToCategories${index + 1}`, baseContext);

          await pagesPage.backToList(page);
          const pageTitle = await pagesPage.getPageTitle(page);
          await expect(pageTitle).to.contains(pagesPage.pageTitle);
        });
      });
    });

    // Filter categories table
    describe('Filter Categories', async () => {
      it('should reset filter and get number of categories in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetBeforeFilter', baseContext);

        numberOfCategories = await pagesPage.resetAndGetNumberOfLines(page, 'cms_page_category');
        await expect(numberOfCategories).to.be.above(0);
      });

      const tests = [
        {
          args:
            {
              testIdentifier: 'filterIdCategory',
              filterType: 'input',
              filterBy: 'id_cms_category',
              filterValue: 1,
            },
        },
        {
          args:
            {
              testIdentifier: 'filterName',
              filterType: 'input',
              filterBy: 'name',
              filterValue: firstCategoryData.name,
            },
        },
        {
          args:
            {
              testIdentifier: 'filterDescription',
              filterType: 'input',
              filterBy: 'description',
              filterValue: secondCategoryData.description,
            },
        },
        {
          args:
            {
              testIdentifier: 'filterPosition',
              filterType: 'input',
              filterBy: 'position',
              filterValue: 5,
            },
        },
        {
          args:
            {
              testIdentifier: 'filterActive',
              filterType: 'select',
              filterBy: 'active',
              filterValue: secondCategoryData.displayed,
            },
        },
      ];

      tests.forEach((test) => {
        it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

          await pagesPage.filterTable(
            page,
            'cms_page_category',
            test.args.filterType,
            test.args.filterBy,
            test.args.filterValue,
          );

          const numberOfCategoriesAfterFilter = await pagesPage.getNumberOfElementInGrid(
            page,
            'cms_page_category',
          );

          await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);

          for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
            if (test.args.filterBy === 'active') {
              const categoryStatus = await pagesPage.getStatus(page, 'cms_page_category', i);
              await expect(categoryStatus).to.equal(test.args.filterValue);
            } else {
              const textColumn = await pagesPage.getTextColumnFromTableCmsPageCategory(
                page,
                i,
                test.args.filterBy,
              );

              await expect(textColumn).to.contains(test.args.filterValue);
            }
          }
        });

        it('should reset all filters', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `reset_${test.args.testIdentifier}`, baseContext);

          const numberOfCategoriesAfterFilter = await pagesPage.resetAndGetNumberOfLines(
            page,
            'cms_page_category',
          );

          await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
        });
      });
    });

    // 2 : Editing Categories from grid table
    describe('Quick Edit Categories', async () => {
      it('should filter by category name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkActions', baseContext);

        await pagesPage.filterTable(
          page,
          'cms_page_category',
          'input',
          'name',
          firstCategoryData.name,
        );

        const numberOfCategoriesAfterFilter = await pagesPage.getNumberOfElementInGrid(
          page,
          'cms_page_category',
        );

        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);

        const textColumn = await pagesPage.getTextColumnFromTableCmsPageCategory(
          page,
          1,
          'name',
        );

        await expect(textColumn).to.contains(firstCategoryData.name);
      });

      const statuses = [
        {args: {status: 'disable', enable: false}},
        {args: {status: 'enable', enable: true}},
      ];

      statuses.forEach((categoryStatus) => {
        it(`should ${categoryStatus.args.status} the category`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `bulk${categoryStatus.args.status}`, baseContext);

          const isActionPerformed = await pagesPage.setStatus(
            page,
            'cms_page_category',
            1,
            categoryStatus.args.enable,
          );

          if (isActionPerformed) {
            const resultMessage = await pagesPage.getAlertSuccessBlockParagraphContent(page);
            await expect(resultMessage).to.contains(pagesPage.successfulUpdateStatusMessage);
          }

          const currentStatus = await pagesPage.getStatus(page, 'cms_page_category', 1);
          await expect(currentStatus).to.be.equal(categoryStatus.args.enable);
        });

        it('should reset all filters', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `resetAfter${categoryStatus.args.status}`,
            baseContext,
          );

          const numberOfCategoriesAfterFilter = await pagesPage.resetAndGetNumberOfLines(
            page,
            'cms_page_category',
          );

          await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
        });
      });

      it('should delete categories with Bulk Actions and check Result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCategories', baseContext);

        const deleteTextResult = await pagesPage.deleteWithBulkActions(page, 'cms_page_category');
        await expect(deleteTextResult).to.be.equal(pagesPage.successfulMultiDeleteMessage);
      });
    });
  });
});
