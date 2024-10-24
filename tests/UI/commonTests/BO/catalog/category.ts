// Import utils
import testContext from '@utils/testContext';

// Import BO pages
import categoriesPage from '@pages/BO/catalog/categories';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type CategoryFilter,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

let browserContext: BrowserContext;
let page: Page;
let numberOfCategories: number;

/**
 * Function to bulk delete categories
 * @param categoryData {CategoryFilter} Category to delete
 * @param baseContext {string} String to identify the test
 */
function bulkDeleteCategoriesTest(
  categoryData: CategoryFilter,
  baseContext: string = 'commonTests-bulkDeleteCategoriesTest',
): void {
  describe(`POST-TEST: Bulk delete categories (filtered by ${categoryData.filterBy} "${categoryData.value}")`, async () => {
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

    it('should go to \'Catalog > Categories\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPageToCheckImport', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.categoriesLink,
      );
      await categoriesPage.closeSfToolBar(page);

      const pageTitle = await categoriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(categoriesPage.pageTitle);
    });

    it('should reset filter and get number of categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

      numberOfCategories = await categoriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCategories).to.be.above(0);
    });

    it('should filter list by Name \'category\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterCategoriesTable', baseContext);

      await categoriesPage.filterCategories(page, 'input', categoryData.filterBy, categoryData.value);

      const textColumn = await categoriesPage.getTextColumnFromTableCategories(page, 1, categoryData.filterBy);
      expect(textColumn).to.contains(categoryData.value);
    });

    it('should delete categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await categoriesPage.deleteCategoriesBulkActions(page);
      expect(deleteTextResult).to.be.equal(categoriesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfCategoriesAfterReset = await categoriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCategoriesAfterReset).to.be.below(numberOfCategories);
    });
  });
}

export default bulkDeleteCategoriesTest;
