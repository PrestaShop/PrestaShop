require('module-alias/register');
// Import utils
const helper = require('@utils/helpers');

// Import login test
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const categoriesPage = require('@pages/BO/catalog/categories');

// Import test context
const testContext = require('@utils/testContext');

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfCategories;

/**
 * Function to bulk delete categories
 * @param categoryData {object} Category to delete
 * @param baseContext {string} String to identify the test
 */
function bulkDeleteCategoriesTest(categoryData, baseContext = 'commonTests-bulkDeleteCategoriesTest') {
  describe('POST-TEST: BUlk delete categories', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPageToCheckImport', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.categoriesLink,
      );

      await categoriesPage.closeSfToolBar(page);

      const pageTitle = await categoriesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(categoriesPage.pageTitle);
    });

    it('should reset filter and get number of categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

      numberOfCategories = await categoriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCategories).to.be.above(0);
    });

    it('should filter list by Name \'category\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterCategoriesTable', baseContext);

      await categoriesPage.filterCategories(page, 'input', categoryData.filterBy, categoryData.value);

      const textColumn = await categoriesPage.getTextColumnFromTableCategories(page, 1, categoryData.filterBy);
      await expect(textColumn).to.contains(categoryData.value);
    });

    it('should delete categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await categoriesPage.deleteCategoriesBulkActions(page);
      await expect(deleteTextResult).to.be.equal(categoriesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfCategoriesAfterReset = await categoriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCategoriesAfterReset).to.be.below(numberOfCategories);
    });
  });
}

module.exports = {bulkDeleteCategoriesTest};
