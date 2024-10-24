// Import utils
import testContext from '@utils/testContext';

// Import pages
import pagesPage from '@pages/BO/design/pages';
import addPageCategoryPage from '@pages/BO/design/pages/pageCategory/add';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerCMSCategory,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_pages_categories_categoriesBulkActions';

/* Create 2 categories
Enable/Disable/Delete categories by bulk actions
 */
describe('BO - Design - Pages : Enable/Disable/Delete categories with Bulk Actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCategories: number = 0;

  const firstCategoryData: FakerCMSCategory = new FakerCMSCategory({name: 'todelete'});
  const secondCategoryData: FakerCMSCategory = new FakerCMSCategory({name: 'todelete'});
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
    await pagesPage.closeSfToolBar(page);

    const pageTitle = await pagesPage.getPageTitle(page);
    expect(pageTitle).to.contains(pagesPage.pageTitle);
  });

  it('should reset filter and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCategories = await pagesPage.resetAndGetNumberOfLines(page, categoriesTableName);

    if (numberOfCategories !== 0) {
      expect(numberOfCategories).to.be.above(0);
    }
  });

  // 1 : Create 2 categories In BO
  describe('Create 2 categories', async () => {
    [firstCategoryData, secondCategoryData].forEach((categoryToCreate: FakerCMSCategory, index: number) => {
      it('should go to add new page category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCategory${index + 1}`, baseContext);

        await pagesPage.goToAddNewPageCategory(page);

        const pageTitle = await addPageCategoryPage.getPageTitle(page);
        expect(pageTitle).to.contains(addPageCategoryPage.pageTitleCreate);
      });

      it(`should create category n° ${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCategory${index + 1}`, baseContext);

        const textResult = await addPageCategoryPage.createEditPageCategory(page, categoryToCreate);
        expect(textResult).to.equal(pagesPage.successfulCreationMessage);
      });

      it('should go back to categories list', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `backToCategories${index + 1}`, baseContext);

        await pagesPage.backToList(page);

        const pageTitle = await pagesPage.getPageTitle(page);
        expect(pageTitle).to.contains(pagesPage.pageTitle);
      });
    });
  });

  // 2 : Enable/Disable categories created with bulk actions
  describe('Enable and Disable categories with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToChangeStatus', baseContext);

      await pagesPage.filterTable(page, categoriesTableName, 'input', 'name', 'todelete');

      const textResult = await pagesPage.getTextColumnFromTableCmsPageCategory(page, 1, 'name');
      expect(textResult).to.contains('todelete');
    });

    [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ].forEach((categoryStatus) => {
      it(`should ${categoryStatus.args.status} categories with bulk actions and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${categoryStatus.args.status}Category`, baseContext);

        const textResult = await pagesPage.bulkSetStatus(page, categoriesTableName, categoryStatus.args.enable);
        expect(textResult).to.be.equal(pagesPage.successfulUpdateStatusMessage);

        const numberOfCategoriesInGrid = await pagesPage.getNumberOfElementInGrid(page, categoriesTableName);

        for (let i = 1; i <= numberOfCategoriesInGrid; i++) {
          const textColumn = await pagesPage.getStatus(
            page,
            categoriesTableName,
            i,
          );
          expect(textColumn).to.equal(categoryStatus.args.enable);
        }
      });
    });
  });

  // 3 : Delete Categories created with bulk actions
  describe('Delete categories with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
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
