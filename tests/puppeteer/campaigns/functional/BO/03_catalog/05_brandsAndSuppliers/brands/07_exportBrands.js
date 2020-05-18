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
const BrandsPage = require('@pages/BO/catalog/brands');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_brands_exportBrands';

let browser;
let page;
let numberOfBrands = 0;
let fileName;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
  };
};
/*
Export brands
Check csv file was downloaded
Check existence of brands data in csv file
 */
describe('Export brands', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
    await files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${fileName}`);
  });
  // Login into BO
  loginCommon.loginBO();

  it('should go to brands page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.brandsAndSuppliersLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
  });

  it('should reset all filters and get number of brands in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);
    numberOfBrands = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer');
    await expect(numberOfBrands).to.be.above(0);
  });

  it('should export brands to a csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportBrands', baseContext);
    await this.pageObjects.brandsPage.exportBrandsDataToCsv();
    const doesFileExist = await files.doesFileExist('manufacturer_', 5000, true, 'csv');
    await expect(doesFileExist, 'Export of data has failed').to.be.true;
  });

  it('should check existence of brands data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllBrandsInCsvFile', baseContext);
    const numberOfCategories = await this.pageObjects.brandsPage.getNumberOfElementInGrid('manufacturer');
    fileName = await files.getFileNameFromDir(global.BO.DOWNLOAD_PATH, 'manufacturer_', '.csv');
    for (let row = 1; row <= numberOfCategories; row++) {
      const brandInCsvFormat = await this.pageObjects.brandsPage.getBrandInCsvFormat(row);
      const textExist = await files.isTextInFile(fileName, brandInCsvFormat, true, true);
      await expect(textExist, `${brandInCsvFormat} was not found in the file`).to.be.true;
    }
  });
});
