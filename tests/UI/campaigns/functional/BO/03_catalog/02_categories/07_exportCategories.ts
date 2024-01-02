// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import categoriesPage from '@pages/BO/catalog/categories';
import dashboardPage from '@pages/BO/dashboard';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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

  it('should export categories to a csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportCategories', baseContext);

    filePath = await categoriesPage.exportDataToCsv(page);

    const doesFileExist = await files.doesFileExist(filePath, 5000);
    expect(doesFileExist, 'Export of data has failed').to.eq(true);
  });

  it('should check existence of categories data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllCategoriesInCsvFile', baseContext);

    const numberOfCategories = await categoriesPage.getNumberOfElementInGrid(page);

    for (let row = 1; row <= numberOfCategories; row++) {
      const categoryInCsvFormat = await categoriesPage.getCategoryInCsvFormat(page, row);
      const textExist = await files.isTextInFile(filePath, categoryInCsvFormat, true);
      expect(textExist, `${categoryInCsvFormat} was not found in the file`).to.eq(true);
    }
  });
});
