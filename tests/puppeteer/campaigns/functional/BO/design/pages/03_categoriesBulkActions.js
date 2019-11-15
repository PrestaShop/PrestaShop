require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const CategoryFaker = require('@data/faker/CMScategory');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login/index');
const DashboardPage = require('@pages/BO/dashboard/index');
const PagesPage = require('@pages/BO/design/pages/index');
const AddPageCategoryPage = require('@pages/BO/design/pages/pageCategory/add');
const AddPagePage = require('@pages/BO/design/pages/add');

let browser;
let page;
let numberOfCategories = 0;
let firstCategoryData = new CategoryFaker({name: 'todelete'});
let secondCategoryData = new CategoryFaker({name: 'todelete'});

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    pagesPage: new PagesPage(page),
    addPageCategoryPage: new AddPageCategoryPage(page),
    addPagePage: new AddPagePage(page),
  };
};

// Create Categories, Then disable / Enable and Delete with Bulk actions
describe('Create Categories, Then disable / Enable and Delete with Bulk actions', async () => {
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

  it('should reset filter and get number of categories in BO', async function () {
    numberOfCategories = await this.pageObjects.pagesPage.resetAndGetNumberOfLines('cms_page_category');
    if (numberOfCategories !== 0) await expect(numberOfCategories).to.be.above(0);
  });

  // 1 : Create 2 categories In BO
  describe('Create 2 categories', async () => {
    const categoriesToCreate = [firstCategoryData, secondCategoryData];
    categoriesToCreate.forEach((categoryToCreate) => {
      it('should go to add new page category', async function () {
        await this.pageObjects.pagesPage.goToAddNewPageCategory();
        const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
      });

      it('should create the first category ', async function () {
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
  // 2 : Enable/Disable categories created with bulk actions
  describe('Enable and Disable categories with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
      await this.pageObjects.pagesPage.filterTable(
        'cms_page_category',
        'input',
        'name',
        'todelete',
      );
      const textResult = await this.pageObjects.pagesPage.getTextColumnFromTable(
        'cms_page_category',
        1,
        'name',
      );
      await expect(textResult).to.contains('todelete');
    });
    const statuses = [
      {args: {status: 'disable', enable: false}, expected: 'clear'},
      {args: {status: 'enable', enable: true}, expected: 'check'},
    ];
    statuses.forEach((categoryStatus) => {
      it(`should ${categoryStatus.args.status} categories with Bulk Actions and check Result`, async function () {
        const textResult = await this.pageObjects.pagesPage.changeEnabledColumnBulkActions(
          'cms_page_category',
          categoryStatus.args.enable,
        );
        await expect(textResult).to.be.equal(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
        const numberOfCategoriesInGrid = await this.pageObjects.pagesPage.getNumberOfElementInGrid(
          'cms_page_category',
        );
        for (let i = 1; i <= numberOfCategoriesInGrid; i++) {
          const textColumn = await this.pageObjects.pagesPage.getTextColumnFromTable(
            'cms_page_category',
            i,
            'active',
          );
          await expect(textColumn).to.contains(categoryStatus.expected);
        }
      });
    });
  });
  // 3 : Delete Categories created with bulk actions
  describe('Delete categories with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
      await this.pageObjects.pagesPage.filterTable(
        'cms_page_category',
        'input',
        'name',
        'todelete',
      );
      const textResult = await this.pageObjects.pagesPage.getTextColumnFromTable(
        'cms_page_category',
        1,
        'name',
      );
      await expect(textResult).to.contains('todelete');
    });

    it('should delete categories with Bulk Actions and check Result', async function () {
      const deleteTextResult = await this.pageObjects.pagesPage.deleteRowInTableBulkActions('cms_page_category');
      await expect(deleteTextResult).to.be.equal(this.pageObjects.pagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.resetAndGetNumberOfLines(
        'cms_page_category',
      );
      await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
    });
  });
});
