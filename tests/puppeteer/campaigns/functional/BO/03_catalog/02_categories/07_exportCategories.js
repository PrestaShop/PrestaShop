require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CategoriesPage = require('@pages/BO/catalog/categories');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_categories_exportCategories';

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    categoriesPage: new CategoriesPage(page),
  };
};

/*
Export categories
Check csv file was downloaded
Check existence of categories data in csv file
 */
describe('Export categories', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to categories page
  loginCommon.loginBO();

  it('should go to categories page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.categoriesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.categoriesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.categoriesPage.pageTitle);
  });

  it('should export categories to a csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportCategories', baseContext);
    await this.pageObjects.categoriesPage.exportDataToCsv();
    const doesFileExist = await files.doesFileExist('category_', 5000, true, 'csv');
    await expect(doesFileExist, 'Export of data has failed').to.be.true;
  });

  it('should check existence of categories data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllCategoriesInCsvFile', baseContext);
    const numberOfCategories = await this.pageObjects.categoriesPage.getNumberOfElementInGrid();
    const fileName = await files.getFileNameFromDir(global.BO.DOWNLOAD_PATH, 'category_', '.csv');
    for (let row = 1; row <= numberOfCategories; row++) {
      const categoryInCsvFormat = await this.pageObjects.categoriesPage.getCategoryInCsvFormat(row);
      const textExist = await files.isTextInFile(fileName, categoryInCsvFormat, true);
      await expect(textExist, `${categoryInCsvFormat} was not found in the file`).to.be.true;
    }
    await files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${fileName}`);
  });
});
