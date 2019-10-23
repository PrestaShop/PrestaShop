require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const PagesPage = require('@pages/BO/design/pages/pages');
const AddPageCategoryPage = require('@pages/BO/design/pages/addPageCategory');
const AddPagePage = require('@pages/BO/design/pages/addPage');
const CategoryFaker = require('@data/faker/CMScategory');
const PageFaker = require('@data/faker/CMSpage');

let browser;
let page;
let numberOfCategories = 0;
let numberOfPages = 0;
let firstCategoryData;
let secondCategoryData;
let firstPageData;
let secondPageData;

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
describe('Create Categories/ Pages, Then disable / Enable and Delete with Bulk actions', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    firstCategoryData = await (new CategoryFaker({name: 'todelete'}));
    secondCategoryData = await (new CategoryFaker({name: 'todelete'}));
    firstPageData = await (new PageFaker({title: 'todelete'}));
    secondPageData = await (new PageFaker({title: 'todelete'}));
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to Pages page
  loginCommon.loginBO();
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
    if (await this.pageObjects.pagesPage.elementVisible(
      this.pageObjects.pagesPage.categoryfilterResetButton, 2000)) {
      await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.categoryfilterResetButton);
    }
    numberOfCategories = await this.pageObjects.pagesPage.getNumberFromText(
      this.pageObjects.pagesPage.categoryGridTitle);
    if (numberOfCategories === 0) {
      const filterButtonVisibility = await this.pageObjects.pagesPage.elementVisible(
        this.pageObjects.pagesPage.categoryfilterResetButton, 2000);
      await expect(filterButtonVisibility).to.be.false;
    } else await expect(numberOfCategories).to.be.above(0);
  });
  it('should reset filter and get number of pages in BO', async function () {
    if (await this.pageObjects.pagesPage.elementVisible(
      this.pageObjects.pagesPage.pagefilterResetButton, 2000)) {
      await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
    }
    numberOfPages = await this.pageObjects.pagesPage.getNumberFromText(
      this.pageObjects.pagesPage.pageGridTitle);
    await expect(numberOfPages).to.be.above(0);
  });
  // 1 : Create 2 categories In BO
  describe('Create 2 categories', async () => {
    it('should go to add new page category', async function () {
      await this.pageObjects.pagesPage.clickAndWaitForNavigation(
        this.pageObjects.pagesPage.addNewPageCategoryLink,
      );
      const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
    });
    it('should create the first category ', async function () {
      const textResult = await this.pageObjects.addPageCategoryPage.createEditPageCategory(firstCategoryData);
      await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
    });
    it('should go back to categories list', async function () {
      await this.pageObjects.pagesPage.clickAndWaitForNavigation(
        this.pageObjects.pagesPage.backToListButton,
      );
      const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
    });
    it('should go to add new page category', async function () {
      await this.pageObjects.pagesPage.clickAndWaitForNavigation(
        this.pageObjects.pagesPage.addNewPageCategoryLink,
      );
      const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
    });
    it('should create the second category ', async function () {
      const textResult = await this.pageObjects.addPageCategoryPage.createEditPageCategory(secondCategoryData);
      await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
    });
    it('should go back to categories list', async function () {
      await this.pageObjects.pagesPage.clickAndWaitForNavigation(
        this.pageObjects.pagesPage.backToListButton,
      );
      const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
    });
  });
  // 2 : Enable/Disable categories created with bulk actions
  describe('Enable and Disable categories with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
      await this.pageObjects.pagesPage.filterPageCategories(
        'input',
        'name',
        'todelete',
      );
      const textResult = await this.pageObjects.pagesPage.getTextContent(
        this.pageObjects.pagesPage.categoriesListTableColumn.replace('%ROW', '1').replace('%COLUMN', 'name',
        ),
      );
      await expect(textResult).to.contains('todelete');
    });
    it('should disable categories with Bulk Actions and check Result', async function () {
      const disableTextResult = await this.pageObjects.pagesPage.changeCategoriesEnabledColumnBulkActions(false);
      await expect(disableTextResult).to.be.equal(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
      const numberOfCategoriesInGrid = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.categoryGridTitle);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCategoriesInGrid; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.categoriesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('clear');
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should enable categories with Bulk Actions and check Result', async function () {
      const enableTextResult = await this.pageObjects.pagesPage.changeCategoriesEnabledColumnBulkActions(true);
      await expect(enableTextResult).to.be.equal(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
      const numberOfCategoriesInGrid = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.categoryGridTitle,
      );
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCategoriesInGrid; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.categoriesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('check');
      }
      /* eslint-enable no-await-in-loop */
    });
  });
  // 3 : Delete Categories created with bulk actions
  describe('Delete categories with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
      await this.pageObjects.pagesPage.filterPageCategories(
        'input',
        'name',
        'todelete',
      );
      const textResult = await this.pageObjects.pagesPage.getTextContent(
        this.pageObjects.pagesPage.categoriesListTableColumn.replace('%ROW', '1').replace('%COLUMN', 'name',
        ),
      );
      await expect(textResult).to.contains('todelete');
    });
    it('should delete categories with Bulk Actions and check Result', async function () {
      const deleteTextResult = await this.pageObjects.pagesPage.deleteCategoriesBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.pagesPage.successfulMultiDeleteMessage);
    });
    it('should reset all filters', async function () {
      await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.categoryfilterResetButton);
      const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.categoryGridTitle);
      await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
    });
  });
  // 4 : Create 2 pages In BO
  describe('Create 2 pages', async () => {
    it('should go to add new page page', async function () {
      await this.pageObjects.pagesPage.clickAndWaitForNavigation(
        this.pageObjects.pagesPage.addNewPageLink,
      );
      const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
    });
    it('should create the first page ', async function () {
      const textResult = await this.pageObjects.addPagePage.createEditPage(firstPageData);
      await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
    });
    it('should go to add new page page', async function () {
      await this.pageObjects.pagesPage.clickAndWaitForNavigation(
        this.pageObjects.pagesPage.addNewPageLink,
      );
      const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
    });
    it('should create the first page ', async function () {
      const textResult = await this.pageObjects.addPagePage.createEditPage(secondPageData);
      await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
    });
  });
  // 5 : Enable/Disable Pages created with bulk actions
  describe('Enable and Disable pages with Bulk Actions', async () => {
    it('should filter list by Title', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'input',
        'meta_title',
        'todelete',
      );
      const textResult = await this.pageObjects.pagesPage.getTextContent(
        this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', '1').replace('%COLUMN', 'meta_title',
        ),
      );
      await expect(textResult).to.contains('todelete');
    });
    it('should disable pages with Bulk Actions and check Result', async function () {
      const disableTextResult = await this.pageObjects.pagesPage.changePagesEnabledColumnBulkActions(false);
      await expect(disableTextResult).to.be.equal(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
      const numberOfPagesInGrid = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      await expect(numberOfPagesInGrid).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesInGrid; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('clear');
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should enable pages with Bulk Actions and check Result', async function () {
      const enableTextResult = await this.pageObjects.pagesPage.changePagesEnabledColumnBulkActions(true);
      await expect(enableTextResult).to.be.equal(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
      const numberOfPagesInGrid = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle,
      );
      await expect(numberOfPagesInGrid).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesInGrid; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('check');
      }
      /* eslint-enable no-await-in-loop */
    });
  });
  // 3 : Delete Pages created with bulk actions
  describe('Delete pages with Bulk Actions', async () => {
    it('should filter list by Title', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'input',
        'meta_title',
        'todelete',
      );
      const textResult = await this.pageObjects.pagesPage.getTextContent(
        this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', '1').replace('%COLUMN', 'meta_title',
        ),
      );
      await expect(textResult).to.contains('todelete');
    });
    it('should delete pages with Bulk Actions and check Result', async function () {
      const deleteTextResult = await this.pageObjects.pagesPage.deletePagesBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.pagesPage.successfulMultiDeleteMessage);
    });
    it('should reset all filters', async function () {
      await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages);
    });
  });
});
