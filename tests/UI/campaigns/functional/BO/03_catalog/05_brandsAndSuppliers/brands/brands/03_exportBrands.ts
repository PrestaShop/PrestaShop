// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import page
import brandsPage from '@pages/BO/catalog/brands';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_brandsAndSuppliers_brands_brands_exportBrands';

/*
Export brands
Check csv file was downloaded
Check existence of brands data in csv file
 */
describe('BO - Catalog - Brands & Suppliers : Export brands', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfBrands: number = 0;
  let filePath: string|null;

  const tableName: string = 'manufacturer';

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.brandsAndSuppliersLink,
    );
    await brandsPage.closeSfToolBar(page);

    const pageTitle = await brandsPage.getPageTitle(page);
    expect(pageTitle).to.contains(brandsPage.pageTitle);
  });

  it('should reset all filters and get number of brands in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    numberOfBrands = await brandsPage.resetAndGetNumberOfLines(page, tableName);
    expect(numberOfBrands).to.be.above(0);
  });

  it('should export brands to a csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportBrands', baseContext);

    filePath = await brandsPage.exportBrandsDataToCsv(page);

    const doesFileExist = await utilsFile.doesFileExist(filePath, 5000);
    expect(doesFileExist, 'Export of data has failed').to.eq(true);
  });

  it('should check existence of brands data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllBrandsInCsvFile', baseContext);

    const numberOfCategories = await brandsPage.getNumberOfElementInGrid(page, tableName);

    for (let row = 1; row <= numberOfCategories; row++) {
      const brandInCsvFormat = await brandsPage.getBrandInCsvFormat(page, row);

      const textExist = await utilsFile.isTextInFile(filePath, brandInCsvFormat, true, true);
      expect(textExist, `${brandInCsvFormat} was not found in the file`).to.eq(true);
    }
  });
});
