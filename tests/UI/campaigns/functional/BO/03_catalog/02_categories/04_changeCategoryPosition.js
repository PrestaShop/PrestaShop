require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CategoriesPage = require('@pages/BO/catalog/categories');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_categories_changeCategoryPosition';

let browser;
let browserContext;
let page;
let numberOfCategories = 0;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    categoriesPage: new CategoriesPage(page),
  };
};

describe('Change category position', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    browserContext = await helper.createBrowserContext(browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to categories page
  loginCommon.loginBO();

  it('should go to categories page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.categoriesLink,
    );

    await this.pageObjects.categoriesPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.categoriesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.categoriesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
    await expect(numberOfCategories).to.be.above(0);
  });

  it('should sort categories by position', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sortCategoriesByPosition', baseContext);

    let nonSortedTable = await this.pageObjects.categoriesPage.getAllRowsColumnContent('position');

    await this.pageObjects.categoriesPage.sortTable('position', 'asc');

    let sortedTable = await this.pageObjects.categoriesPage.getAllRowsColumnContent('position');

    nonSortedTable = await nonSortedTable.map(text => parseFloat(text));

    sortedTable = await sortedTable.map(text => parseFloat(text));

    const expectedResult = await this.pageObjects.categoriesPage.sortArray(nonSortedTable, true);
    await expect(sortedTable).to.deep.equal(expectedResult);
  });

  describe('Change categories position', async () => {
    it('should change category position', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeCategoryPosition', baseContext);

      const firstCategoryNameBeforeUpdate = await this.pageObjects.categoriesPage.getTextColumnFromTableCategories(
        1,
        'name',
      );

      const resultText = await this.pageObjects.categoriesPage.changeCategoryPosition(1, 2);
      await expect(resultText).to.equal(this.pageObjects.categoriesPage.successfulUpdateMessage);

      const firstCategoryNameAfterUpdate = await this.pageObjects.categoriesPage.getTextColumnFromTableCategories(
        1,
        'name',
      );

      await expect(firstCategoryNameBeforeUpdate).to.not.equal(firstCategoryNameAfterUpdate);

      const secondCategoryNameAfterUpdate = await this.pageObjects.categoriesPage.getTextColumnFromTableCategories(
        2,
        'name',
      );

      await expect(firstCategoryNameBeforeUpdate).to.equal(secondCategoryNameAfterUpdate);
    });

    it('should reset category position', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCategoryPosition', baseContext);

      const secondCategoryNameBeforeUpdate = await this.pageObjects.categoriesPage.getTextColumnFromTableCategories(
        2,
        'name',
      );

      const resultText = await this.pageObjects.categoriesPage.changeCategoryPosition(2, 1);
      await expect(resultText).to.equal(this.pageObjects.categoriesPage.successfulUpdateMessage);

      const secondCategoryNameAfterUpdate = await this.pageObjects.categoriesPage.getTextColumnFromTableCategories(
        2,
        'name',
      );

      await expect(secondCategoryNameBeforeUpdate).to.not.equal(secondCategoryNameAfterUpdate);

      const firstCategoryNameAfterUpdate = await this.pageObjects.categoriesPage.getTextColumnFromTableCategories(
        1,
        'name',
      );

      await expect(secondCategoryNameBeforeUpdate).to.equal(firstCategoryNameAfterUpdate);
    });
  });
});
