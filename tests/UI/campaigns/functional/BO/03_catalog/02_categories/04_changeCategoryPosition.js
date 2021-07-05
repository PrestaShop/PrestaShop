require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const categoriesPage = require('@pages/BO/catalog/categories');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_categories_changeCategoryPosition';

let browserContext;
let page;
let numberOfCategories = 0;

describe('BO - Catalog - Categories : Change category position', async () => {
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

  it('should go to \'Catalog > Categories\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.categoriesLink,
    );

    await categoriesPage.closeSfToolBar(page);

    const pageTitle = await categoriesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(categoriesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await categoriesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCategories).to.be.above(0);
  });

  it('should sort categories by position', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sortCategoriesByPosition', baseContext);

    let nonSortedTable = await categoriesPage.getAllRowsColumnContent(page, 'position');

    await categoriesPage.sortTable(page, 'position', 'asc');

    let sortedTable = await categoriesPage.getAllRowsColumnContent(page, 'position');

    nonSortedTable = await nonSortedTable.map(text => parseFloat(text));

    sortedTable = await sortedTable.map(text => parseFloat(text));

    const expectedResult = await categoriesPage.sortArray(nonSortedTable, true);
    await expect(sortedTable).to.deep.equal(expectedResult);
  });

  describe('Change categories position', async () => {
    it('should drag and drop the first category to the second position', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeCategoryPosition', baseContext);

      const firstCategoryNameBeforeUpdate = await categoriesPage.getTextColumnFromTableCategories(
        page,
        1,
        'name',
      );

      const resultText = await categoriesPage.changeCategoryPosition(page, 1, 2);
      await expect(resultText).to.equal(categoriesPage.successfulUpdateMessage);

      const firstCategoryNameAfterUpdate = await categoriesPage.getTextColumnFromTableCategories(
        page,
        1,
        'name',
      );

      await expect(firstCategoryNameBeforeUpdate).to.not.equal(firstCategoryNameAfterUpdate);

      const secondCategoryNameAfterUpdate = await categoriesPage.getTextColumnFromTableCategories(
        page,
        2,
        'name',
      );

      await expect(firstCategoryNameBeforeUpdate).to.equal(secondCategoryNameAfterUpdate);
    });

    it('should reset category position', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCategoryPosition', baseContext);

      const secondCategoryNameBeforeUpdate = await categoriesPage.getTextColumnFromTableCategories(
        page,
        2,
        'name',
      );

      const resultText = await categoriesPage.changeCategoryPosition(page, 2, 1);
      await expect(resultText).to.equal(categoriesPage.successfulUpdateMessage);

      const secondCategoryNameAfterUpdate = await categoriesPage.getTextColumnFromTableCategories(
        page,
        2,
        'name',
      );

      await expect(secondCategoryNameBeforeUpdate).to.not.equal(secondCategoryNameAfterUpdate);

      const firstCategoryNameAfterUpdate = await categoriesPage.getTextColumnFromTableCategories(
        page,
        1,
        'name',
      );

      await expect(secondCategoryNameBeforeUpdate).to.equal(firstCategoryNameAfterUpdate);
    });
  });
});
