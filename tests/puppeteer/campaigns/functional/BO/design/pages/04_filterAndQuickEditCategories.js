require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const CategoryPageFaker = require('@data/faker/CMScategory');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login/index');
const DashboardPage = require('@pages/BO/dashboard/index');
const AddPageCategoryPage = require('@pages/BO/design/pages/pageCategory/add');
const PagesPage = require('@pages/BO/design/pages/index');

let browser;
let page;
let numberOfCategories = 0;
const firstCategoryData = new CategoryPageFaker();
const secondCategoryData = new CategoryPageFaker();

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    addPageCategoryPage: new AddPageCategoryPage(page),
    pagesPage: new PagesPage(page),
  };
};

// Filter And Quick Edit Pages
describe('Filter And Quick Edit Categories', async () => {
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
  it('should go to "Design>Pages" page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.designParentLink,
      this.pageObjects.boBasePage.pagesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
  });

  // 1 : Create two categories and Filter with all inputs and selects in grid table
  describe('Create 2 categories then filter the table', async () => {
    // Create 2 categories
    describe('Create Categories', async () => {
      const categoriesToCreate = [firstCategoryData, secondCategoryData];
      categoriesToCreate.forEach((categoryToCreate) => {
        it('should go to add new page category', async function () {
          await this.pageObjects.pagesPage.goToAddNewPageCategory();
          const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
          await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
        });

        it('should create category ', async function () {
          const textResult = await this.pageObjects.addPageCategoryPage.createEditPageCategory(categoryToCreate);
          await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
        });

        it('should go back to categories list', async function () {
          await this.pageObjects.pagesPage.backToList();
          const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
          await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
        });
      });
    });
    // Filter categories table
    describe('Filter Categories', async () => {
      it('should reset filter and get number of categories in BO', async function () {
        numberOfCategories = await this.pageObjects.pagesPage.resetAndGetNumberOfLines('cms_page_category');
        await expect(numberOfCategories).to.be.above(0);
      });
      const tests = [
        {args: {filterType: 'input', filterBy: 'id_cms_category', filterValue: 1}},
        {args: {filterType: 'input', filterBy: 'name', filterValue: firstCategoryData.name}},
        {args: {filterType: 'input', filterBy: 'description', filterValue: secondCategoryData.description}},
        {args: {filterType: 'input', filterBy: 'position', filterValue: 5}},
        {
          args: {filterType: 'select', filterBy: 'active', filterValue: secondCategoryData.displayed},
          expected: 'check',
        },
      ];
      tests.forEach((test) => {
        it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
          await this.pageObjects.pagesPage.filterTable(
            'cms_page_category',
            test.args.filterType,
            test.args.filterBy,
            test.args.filterValue,
          );
          const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberOfElementInGrid(
            'cms_page_category',
          );
          await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
          for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
            const textColumn = await this.pageObjects.pagesPage.getTextColumnFromTable(
              'cms_page_category',
              i,
              test.args.filterBy,
            );
            if (test.expected !== undefined) {
              await expect(textColumn).to.contains(test.expected);
            } else {
              await expect(textColumn).to.contains(test.args.filterValue);
            }
          }
        });

        it('should reset all filters', async function () {
          const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.resetAndGetNumberOfLines(
            'cms_page_category',
          );
          await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
        });
      });
    });

    // 2 : Editing Categories from grid table
    describe('Quick Edit Categories', async () => {
      it('should filter by category name', async function () {
        await this.pageObjects.pagesPage.filterTable(
          'cms_page_category',
          'input',
          'name',
          firstCategoryData.name,
        );
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberOfElementInGrid(
          'cms_page_category',
        );
        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
        const textColumn = await this.pageObjects.pagesPage.getTextColumnFromTable(
          'cms_page_category',
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
          const isActionPerformed = await this.pageObjects.pagesPage.updateToggleColumnValue(
            'cms_page_category',
            1,
            categoryStatus.enable,
          );
          if (isActionPerformed) {
            const resultMessage = await this.pageObjects.pagesPage.getTextContent(
              this.pageObjects.pagesPage.alertSuccessBlockParagraph,
            );
            await expect(resultMessage).to.contains(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
          }
          const isStatusChanged = await this.pageObjects.pagesPage.getToggleColumnValue('cms_page_category', 1);
          if (categoryStatus.enable) await expect(isStatusChanged).to.be.false;
          else await expect(isStatusChanged).to.be.true;
        });

        it('should reset all filters', async function () {
          const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.resetAndGetNumberOfLines(
            'cms_page_category',
          );
          await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
        });
      });

      it('should delete categories with Bulk Actions and check Result', async function () {
        const deleteTextResult = await this.pageObjects.pagesPage.deleteRowInTableBulkActions('cms_page_category');
        await expect(deleteTextResult).to.be.equal(this.pageObjects.pagesPage.successfulMultiDeleteMessage);
      });
    });
  });
});
