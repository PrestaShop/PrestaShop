// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import categoriesPage from '@pages/BO/catalog/categories';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_categories_changeCategoryPosition';

describe('BO - Catalog - Categories : Change category position', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCategories: number = 0;

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
    expect(pageTitle).to.contains(categoriesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await categoriesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCategories).to.be.above(0);
  });

  it('should sort categories by position', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sortCategoriesByPosition', baseContext);

    const nonSortedTable = await categoriesPage.getAllRowsColumnContent(page, 'position');

    await categoriesPage.sortTable(page, 'position', 'asc');

    const sortedTable = await categoriesPage.getAllRowsColumnContent(page, 'position');

    const nonSortedTableFloat: number[] = await nonSortedTable.map((text: string): number => parseFloat(text));
    const sortedTableFloat: number[] = await sortedTable.map((text: string): number => parseFloat(text));

    const expectedResult = await basicHelper.sortArrayNumber(nonSortedTableFloat);
    expect(sortedTableFloat).to.deep.equal(expectedResult);
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
      expect(resultText).to.equal(categoriesPage.successfulUpdateMessage);

      const firstCategoryNameAfterUpdate = await categoriesPage.getTextColumnFromTableCategories(
        page,
        1,
        'name',
      );
      expect(firstCategoryNameBeforeUpdate).to.not.equal(firstCategoryNameAfterUpdate);

      const secondCategoryNameAfterUpdate = await categoriesPage.getTextColumnFromTableCategories(
        page,
        2,
        'name',
      );
      expect(firstCategoryNameBeforeUpdate).to.equal(secondCategoryNameAfterUpdate);
    });

    it('should reset category position', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCategoryPosition', baseContext);

      const secondCategoryNameBeforeUpdate = await categoriesPage.getTextColumnFromTableCategories(
        page,
        2,
        'name',
      );

      const resultText = await categoriesPage.changeCategoryPosition(page, 2, 1);
      expect(resultText).to.equal(categoriesPage.successfulUpdateMessage);

      const secondCategoryNameAfterUpdate = await categoriesPage.getTextColumnFromTableCategories(
        page,
        2,
        'name',
      );
      expect(secondCategoryNameBeforeUpdate).to.not.equal(secondCategoryNameAfterUpdate);

      const firstCategoryNameAfterUpdate = await categoriesPage.getTextColumnFromTableCategories(
        page,
        1,
        'name',
      );
      expect(secondCategoryNameBeforeUpdate).to.equal(firstCategoryNameAfterUpdate);
    });
  });
});
