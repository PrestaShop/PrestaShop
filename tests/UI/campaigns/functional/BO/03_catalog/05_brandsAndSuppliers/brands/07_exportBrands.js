require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/catalog/brands');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_brands_exportBrands';


let browserContext;
let page;
let numberOfBrands = 0;
let filePath;

// Init objects needed
const init = async function () {
  return {
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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO
  loginCommon.loginBO();

  it('should go to brands page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.brandsAndSuppliersLink,
    );

    await this.pageObjects.brandsPage.closeSfToolBar();

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

    filePath = await this.pageObjects.brandsPage.exportBrandsDataToCsv();
    const doesFileExist = await files.doesFileExist(filePath, 5000);
    await expect(doesFileExist, 'Export of data has failed').to.be.true;
  });

  it('should check existence of brands data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllBrandsInCsvFile', baseContext);

    const numberOfCategories = await this.pageObjects.brandsPage.getNumberOfElementInGrid('manufacturer');

    for (let row = 1; row <= numberOfCategories; row++) {
      const brandInCsvFormat = await this.pageObjects.brandsPage.getBrandInCsvFormat(row);

      const textExist = await files.isTextInFile(filePath, brandInCsvFormat, true, true);
      await expect(textExist, `${brandInCsvFormat} was not found in the file`).to.be.true;
    }
  });
});
