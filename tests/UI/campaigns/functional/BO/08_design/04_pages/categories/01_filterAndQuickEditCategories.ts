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
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_pages_categories_filterAndQuickEditCategories';

/*
Create 2 categories
Filter categories table by : ID, Name, Description, Position and Displayed
Enable/Disable status by quick edit
Delete created categories by bulk actions
 */
describe('BO - Design - Pages : Filter and quick edit categories table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCategories: number = 0;

  const firstCategoryData: FakerCMSCategory = new FakerCMSCategory();
  const secondCategoryData: FakerCMSCategory = new FakerCMSCategory();
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

  // 1 : Create two categories and filter with all inputs and selects in grid table
  describe('Create 2 categories then filter the table', async () => {
    [firstCategoryData, secondCategoryData].forEach((categoryToCreate: FakerCMSCategory, index: number) => {
      it('should go to add new category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCategory${index + 1}`, baseContext);

        await boCMSPagesPage.goToAddNewPageCategory(page);

        const pageTitle = await boCMSPageCategoriesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCMSPageCategoriesCreatePage.pageTitleCreate);
      });

      it(`should create category n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCategory${index + 1}`, baseContext);

        const textResult = await boCMSPageCategoriesCreatePage.createEditPageCategory(page, categoryToCreate);
        expect(textResult).to.equal(boCMSPagesPage.successfulCreationMessage);
      });

      it('should go back to categories list', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `backToCategories${index + 1}`, baseContext);

        await boCMSPagesPage.backToList(page);

        const pageTitle = await boCMSPagesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
      });
    });
  });

  // Filter categories table
  describe('Filter categories table', async () => {
    it('should reset filter and get number of categories in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetBeforeFilter', baseContext);

      numberOfCategories = await boCMSPagesPage.resetAndGetNumberOfLines(page, categoriesTableName);
      expect(numberOfCategories).to.be.above(0);
    });

    const tests = [
      {
        args:
          {
            testIdentifier: 'filterIdCategory',
            filterType: 'input',
            filterBy: 'id_cms_category',
            filterValue: '1',
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
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: secondCategoryData.displayed ? '1' : '0',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await boCMSPagesPage.filterTable(
          page,
          categoriesTableName,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfCategoriesAfterFilter = await boCMSPagesPage.getNumberOfElementInGrid(
          page,
          categoriesTableName,
        );
        expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);

        for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const categoryStatus = await boCMSPagesPage.getStatus(page, categoriesTableName, i);
            expect(categoryStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await boCMSPagesPage.getTextColumnFromTableCmsPageCategory(
              page,
              i,
              test.args.filterBy,
            );
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `reset_${test.args.testIdentifier}`, baseContext);

        const numberOfCategoriesAfterFilter = await boCMSPagesPage.resetAndGetNumberOfLines(
          page,
          categoriesTableName,
        );
        expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
      });
    });
  });

  // 2 : Editing Categories from grid table
  describe('Quick edit categories', async () => {
    it(`should filter by category name '${firstCategoryData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkActions', baseContext);

      await boCMSPagesPage.filterTable(page, categoriesTableName, 'input', 'name', firstCategoryData.name);

      const numberOfCategoriesAfterFilter = await boCMSPagesPage.getNumberOfElementInGrid(page, categoriesTableName);
      expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);

      const textColumn = await boCMSPagesPage.getTextColumnFromTableCmsPageCategory(page, 1, 'name');
      expect(textColumn).to.contains(firstCategoryData.name);
    });

    [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ].forEach((categoryStatus) => {
      it(`should ${categoryStatus.args.status} the category`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulk${categoryStatus.args.status}`, baseContext);

        const isActionPerformed = await boCMSPagesPage.setStatus(page, categoriesTableName, 1, categoryStatus.args.enable);

        if (isActionPerformed) {
          const resultMessage = await boCMSPagesPage.getAlertSuccessBlockParagraphContent(page);
          expect(resultMessage).to.contains(boCMSPagesPage.successfulUpdateStatusMessage);
        }

        const currentStatus = await boCMSPagesPage.getStatus(page, categoriesTableName, 1);
        expect(currentStatus).to.be.equal(categoryStatus.args.enable);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `resetAfter${categoryStatus.args.status}`,
          baseContext,
        );

        const numberOfCategoriesAfterFilter = await boCMSPagesPage.resetAndGetNumberOfLines(page, categoriesTableName);
        expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
      });
    });

    it('should delete categories with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCategories', baseContext);

      const deleteTextResult = await boCMSPagesPage.deleteWithBulkActions(page, categoriesTableName);
      expect(deleteTextResult).to.be.equal(boCMSPagesPage.successfulMultiDeleteMessage);
    });
  });
});
