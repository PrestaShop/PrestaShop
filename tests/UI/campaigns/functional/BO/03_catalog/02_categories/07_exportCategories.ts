import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCategoriesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_categories_exportCategories';

/*
Export categories
Check csv file was downloaded
Check existence of categories data in csv file
 */
describe('BO - Catalog - Categories : Export categories', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string|null;

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

  it('should export categories to a csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportCategories', baseContext);

    filePath = await boCategoriesPage.exportDataToCsv(page);

    const doesFileExist = await utilsFile.doesFileExist(filePath, 5000);
    expect(doesFileExist, 'Export of data has failed').to.eq(true);
  });

  it('should check existence of categories data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllCategoriesInCsvFile', baseContext);

    const numberOfCategories = await boCategoriesPage.getNumberOfElementInGrid(page);

    for (let row = 1; row <= numberOfCategories; row++) {
      const categoryInCsvFormat = await boCategoriesPage.getCategoryInCsvFormat(page, row);
      const textExist = await utilsFile.isTextInFile(filePath, categoryInCsvFormat, true);
      expect(textExist, `${categoryInCsvFormat} was not found in the file`).to.eq(true);
    }
  });
});
