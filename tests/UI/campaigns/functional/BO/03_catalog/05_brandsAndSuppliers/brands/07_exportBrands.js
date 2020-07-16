require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import page
const dashboardPage = require('@pages/BO/dashboard');
const brandsPage = require('@pages/BO/catalog/brands');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_brands_exportBrands';


let browserContext;
let page;
let numberOfBrands = 0;
let filePath;

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
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to brands page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.brandsAndSuppliersLink,
    );

    await brandsPage.closeSfToolBar(page);

    const pageTitle = await brandsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(brandsPage.pageTitle);
  });

  it('should reset all filters and get number of brands in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    numberOfBrands = await brandsPage.resetAndGetNumberOfLines(page, 'manufacturer');
    await expect(numberOfBrands).to.be.above(0);
  });

  it('should export brands to a csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportBrands', baseContext);

    filePath = await brandsPage.exportBrandsDataToCsv(page);
    const doesFileExist = await files.doesFileExist(filePath, 5000);
    await expect(doesFileExist, 'Export of data has failed').to.be.true;
  });

  it('should check existence of brands data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllBrandsInCsvFile', baseContext);

    const numberOfCategories = await brandsPage.getNumberOfElementInGrid(page, 'manufacturer');

    for (let row = 1; row <= numberOfCategories; row++) {
      const brandInCsvFormat = await brandsPage.getBrandInCsvFormat(page, row);

      const textExist = await files.isTextInFile(filePath, brandInCsvFormat, true, true);
      await expect(textExist, `${brandInCsvFormat} was not found in the file`).to.be.true;
    }
  });
});
