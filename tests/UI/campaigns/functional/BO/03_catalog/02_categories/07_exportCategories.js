require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const categoriesPage = require('@pages/BO/catalog/categories');

const baseContext = 'functional_BO_catalog_categories_exportCategories';

let browserContext;
let page;

let filePath;

/*
Export categories
Check csv file was downloaded
Check existence of categories data in csv file
 */
describe('BO - Catalog - Categories : Export categories', async () => {
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

  it('should export categories to a csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportCategories', baseContext);

    filePath = await categoriesPage.exportDataToCsv(page);
    const doesFileExist = await files.doesFileExist(filePath, 5000);
    await expect(doesFileExist, 'Export of data has failed').to.be.true;
  });

  it('should check existence of categories data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllCategoriesInCsvFile', baseContext);

    const numberOfCategories = await categoriesPage.getNumberOfElementInGrid(page);

    for (let row = 1; row <= numberOfCategories; row++) {
      const categoryInCsvFormat = await categoriesPage.getCategoryInCsvFormat(page, row);
      const textExist = await files.isTextInFile(filePath, categoryInCsvFormat, true);
      await expect(textExist, `${categoryInCsvFormat} was not found in the file`).to.be.true;
    }
  });
});
