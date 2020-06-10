require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const CategoryPageFaker = require('@data/faker/CMScategory');

// Import pages
const LoginPage = require('@pages/BO/login/index');
const DashboardPage = require('@pages/BO/dashboard/index');
const PagesPage = require('@pages/BO/design/pages/index');
const AddPageCategoryPage = require('@pages/BO/design/pages/pageCategory/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_design_pages_categoryPagination';

let browser;
let page;
let numberOfCategories = 0;
const createCategoryData = new CategoryPageFaker();

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    pagesPage: new PagesPage(page),
    addPageCategoryPage: new AddPageCategoryPage(page),
  };
};

describe('Category pagination', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();

    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO
  loginCommon.loginBO();

  // Go to Design>Pages page
  it('should go to "Design > Pages" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCmsPagesPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.designParentLink,
      this.pageObjects.dashboardPage.pagesLink,
    );

    await this.pageObjects.pagesPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCategories = await this.pageObjects.pagesPage.resetAndGetNumberOfLines('cms_page_category');
    if (numberOfCategories !== 0) await expect(numberOfCategories).to.be.above(0);
  });

  // 1 : Create 11 categories
  const tests = new Array(11).fill(0, 0, 11);

  tests.forEach((test, index) => {
    describe(`Create category n°${index + 1} in BO`, async () => {
      it('should go to add new page category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewPageCategoryPage${index}`, baseContext);

        await this.pageObjects.pagesPage.goToAddNewPageCategory();
        const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
      });

      it('should create category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreatePageCategory${index}`, baseContext);

        const textResult = await this.pageObjects.addPageCategoryPage.createEditPageCategory(createCategoryData);
        await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
      });

      it('should go back to categories', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToCategories${index}`, baseContext);

        await this.pageObjects.pagesPage.backToList();
        const pageTitle = await this.pageObjects.pagesPage.getPageTitle();

        await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
      });

      it('should check the categories number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkCategoriesNumber${index}`, baseContext);

        const numberOfCategoriesAfterCreation = await this.pageObjects.pagesPage.getNumberOfElementInGrid(
          'cms_page_category',
        );

        await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1 + index);
      });
    });
  });

  // 2 : Test pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await this.pageObjects.pagesPage.selectCategoryPaginationLimit('10');
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await this.pageObjects.pagesPage.paginationCategoryNext();
      expect(paginationNumber).to.contain('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await this.pageObjects.pagesPage.paginationCategoryPrevious();
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await this.pageObjects.pagesPage.selectCategoryPaginationLimit('50');
      expect(paginationNumber).to.contain('(page 1 / 1)');
    });
  });

  // 3 : Delete the 11 categories with bulk actions
  describe('Delete categories with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await this.pageObjects.pagesPage.filterTable(
        'cms_page_category',
        'input',
        'name',
        createCategoryData.name,
      );

      const textResult = await this.pageObjects.pagesPage.getTextColumnFromTableCmsPageCategory(
        1,
        'name',
      );

      await expect(textResult).to.contains(createCategoryData.name);
    });

    it('should delete categories with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCategories', baseContext);

      const deleteTextResult = await this.pageObjects.pagesPage.deleteWithBulkActions('cms_page_category');
      await expect(deleteTextResult).to.be.equal(this.pageObjects.pagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.resetAndGetNumberOfLines(
        'cms_page_category',
      );

      await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
    });
  });
});
