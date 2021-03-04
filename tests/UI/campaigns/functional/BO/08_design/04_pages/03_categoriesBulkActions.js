require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const CategoryFaker = require('@data/faker/CMScategory');

// Import pages
const dashboardPage = require('@pages/BO/dashboard/index');
const pagesPage = require('@pages/BO/design/pages/index');
const addPageCategoryPage = require('@pages/BO/design/pages/pageCategory/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_design_pages_categoriesBulkActions';


let browserContext;
let page;
let numberOfCategories = 0;

const firstCategoryData = new CategoryFaker({name: 'todelete'});
const secondCategoryData = new CategoryFaker({name: 'todelete'});

// Create Categories, Then disable / Enable and Delete with Bulk actions
describe('Create Categories, Then disable / Enable and Delete with Bulk actions', async () => {
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

  it('should reset filter and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCategories = await pagesPage.resetAndGetNumberOfLines(page, 'cms_page_category');

    if (numberOfCategories !== 0) {
      await expect(numberOfCategories).to.be.above(0);
    }
  });

  // 1 : Create 2 categories In BO
  describe('Create 2 categories', async () => {
    const categoriesToCreate = [firstCategoryData, secondCategoryData];

    categoriesToCreate.forEach((categoryToCreate, index) => {
      it('should go to add new page category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCategory${index + 1}`, baseContext);

        await pagesPage.goToAddNewPageCategory(page);
        const pageTitle = await addPageCategoryPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addPageCategoryPage.pageTitleCreate);
      });

      it('should create the first category ', async function () {
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

  // 2 : Enable/Disable categories created with bulk actions
  describe('Enable and Disable categories with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToChangeStatus', baseContext);

      await pagesPage.filterTable(
        page,
        'cms_page_category',
        'input',
        'name',
        'todelete',
      );

      const textResult = await pagesPage.getTextColumnFromTableCmsPageCategory(
        page,
        1,
        'name',
      );

      await expect(textResult).to.contains('todelete');
    });

    const statuses = [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ];

    statuses.forEach((categoryStatus) => {
      it(`should ${categoryStatus.args.status} categories with Bulk Actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${categoryStatus.args.status}Category`, baseContext);

        const textResult = await pagesPage.bulkSetStatus(
          page,
          'cms_page_category',
          categoryStatus.args.enable,
        );

        await expect(textResult).to.be.equal(pagesPage.successfulUpdateStatusMessage);

        const numberOfCategoriesInGrid = await pagesPage.getNumberOfElementInGrid(
          page,
          'cms_page_category',
        );

        for (let i = 1; i <= numberOfCategoriesInGrid; i++) {
          const textColumn = await pagesPage.getStatus(
            page,
            'cms_page_category',
            i,
          );

          await expect(textColumn).to.equal(categoryStatus.args.enable);
        }
      });
    });
  });

  // 3 : Delete Categories created with bulk actions
  describe('Delete categories with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await pagesPage.filterTable(
        page,
        'cms_page_category',
        'input',
        'name',
        'todelete',
      );

      const textResult = await pagesPage.getTextColumnFromTableCmsPageCategory(
        page,
        1,
        'name',
      );

      await expect(textResult).to.contains('todelete');
    });

    it('should delete categories with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCategories', baseContext);

      const deleteTextResult = await pagesPage.deleteWithBulkActions(page, 'cms_page_category');
      await expect(deleteTextResult).to.be.equal(pagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCategoriesAfterFilter = await pagesPage.resetAndGetNumberOfLines(
        page,
        'cms_page_category',
      );

      await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
    });
  });
});
