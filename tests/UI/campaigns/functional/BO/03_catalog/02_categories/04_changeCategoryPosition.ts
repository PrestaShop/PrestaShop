import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCategoriesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_categories_changeCategoryPosition';

describe('BO - Catalog - Categories : Change category position', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCategories: number = 0;

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
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.categoriesLink,
    );
    await boCategoriesPage.closeSfToolBar(page);

    const pageTitle = await boCategoriesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await boCategoriesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCategories).to.be.above(0);
  });

  it('should sort categories by position', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sortCategoriesByPosition', baseContext);

    const nonSortedTable = await boCategoriesPage.getAllRowsColumnContent(page, 'position');

    await boCategoriesPage.sortTable(page, 'position', 'asc');

    const sortedTable = await boCategoriesPage.getAllRowsColumnContent(page, 'position');

    const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
    const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

    const expectedResult = await utilsCore.sortArrayNumber(nonSortedTableFloat);
    expect(sortedTableFloat).to.deep.equal(expectedResult);
  });

  describe('Change categories position', async () => {
    it('should drag and drop the first category to the second position', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeCategoryPosition', baseContext);

      const firstCategoryNameBeforeUpdate = await boCategoriesPage.getTextColumnFromTableCategories(
        page,
        1,
        'name',
      );

      const resultText = await boCategoriesPage.changeCategoryPosition(page, 1, 2);
      expect(resultText).to.equal(boCategoriesPage.successfulUpdateMessage);

      const firstCategoryNameAfterUpdate = await boCategoriesPage.getTextColumnFromTableCategories(
        page,
        1,
        'name',
      );
      expect(firstCategoryNameBeforeUpdate).to.not.equal(firstCategoryNameAfterUpdate);

      const secondCategoryNameAfterUpdate = await boCategoriesPage.getTextColumnFromTableCategories(
        page,
        2,
        'name',
      );
      expect(firstCategoryNameBeforeUpdate).to.equal(secondCategoryNameAfterUpdate);
    });

    it('should reset category position', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCategoryPosition', baseContext);

      const secondCategoryNameBeforeUpdate = await boCategoriesPage.getTextColumnFromTableCategories(
        page,
        2,
        'name',
      );

      const resultText = await boCategoriesPage.changeCategoryPosition(page, 2, 1);
      expect(resultText).to.equal(boCategoriesPage.successfulUpdateMessage);

      const secondCategoryNameAfterUpdate = await boCategoriesPage.getTextColumnFromTableCategories(
        page,
        2,
        'name',
      );
      expect(secondCategoryNameBeforeUpdate).to.not.equal(secondCategoryNameAfterUpdate);

      const firstCategoryNameAfterUpdate = await boCategoriesPage.getTextColumnFromTableCategories(
        page,
        1,
        'name',
      );
      expect(secondCategoryNameBeforeUpdate).to.equal(firstCategoryNameAfterUpdate);
    });
  });
});
