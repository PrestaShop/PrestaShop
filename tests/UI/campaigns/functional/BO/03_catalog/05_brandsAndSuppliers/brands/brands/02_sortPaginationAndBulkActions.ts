// Import utils
import basicHelper from '@utils/basicHelper';
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import importFileTest from '@commonTests/BO/advancedParameters/importFile';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import brandsPage from '@pages/BO/catalog/brands';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import ImportBrands from '@data/import/brands';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_brandsAndSuppliers_brands_brands_sortPaginationAndBulkActions';

/*
Pre-condition:
- Import list of customers
Scenario:
- Paginate between pages
- Sort brands table
- Enable/Disable/Delete brands with bulk actions
 */
describe('BO - Catalog - Brands & Suppliers : Sort, pagination and bulk actions Brands table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfBrands: number = 0;

  const tableName: string = 'manufacturer';
  // Variable used to create customers csv file
  const fileName: string = 'brands.csv';
  const numberOfImportedBrands: number = 10;

  // Pre-condition: Import list of categories
  importFileTest(fileName, ImportBrands.entity, `${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    // Create csv file with all brands data
    await files.createCSVFile('.', fileName, ImportBrands);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    // Delete created csv file
    await files.deleteFile(fileName);
  });

  // 1 : Pagination of brands table
  describe('Pagination next and previous of Brands table', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.brandsAndSuppliersLink,
      );
      await dashboardPage.closeSfToolBar(page);

      const pageTitle = await brandsPage.getPageTitle(page);
      expect(pageTitle).to.contains(brandsPage.pageTitle);
    });

    it('should reset all filters and get number of brands in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterBrandsTable', baseContext);

      numberOfBrands = await brandsPage.resetAndGetNumberOfLines(page, tableName);
      expect(numberOfBrands).to.be.above(0);
    });

    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'brandsChangeItemsNumberTo10', baseContext);

      const paginationNumber = await brandsPage.selectPaginationLimit(page, tableName, 10);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'brandsClickOnNext', baseContext);

      const paginationNumber = await brandsPage.paginationNext(page, tableName);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'brandsClickOnPrevious', baseContext);

      const paginationNumber = await brandsPage.paginationPrevious(page, tableName);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'brandsChangeItemsNumberTo50', baseContext);

      const paginationNumber = await brandsPage.selectPaginationLimit(page, tableName, 50);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 2 : sort brands
  describe('Sort Brands table', async () => {
    const brandsTests = [
      {
        args:
          {
            testIdentifier: 'sortBrandsByIdBrandDesc', sortBy: 'id_manufacturer', sortDirection: 'desc', isFloat: true,
          },
      },
      {args: {testIdentifier: 'sortBrandsByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortBrandsByNameDesc', sortBy: 'name', sortDirection: 'desc'}},
      {
        args:
          {
            testIdentifier: 'sortBrandsByIdBrandAsc', sortBy: 'id_manufacturer', sortDirection: 'asc', isFloat: true,
          },
      },
    ];
    brandsTests.forEach((test) => {
      it(
        `should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

          const nonSortedTable = await brandsPage.getAllRowsColumnContentBrandsTable(page, test.args.sortBy);

          await brandsPage.sortTableBrands(page, test.args.sortBy, test.args.sortDirection);
          const sortedTable = await brandsPage.getAllRowsColumnContentBrandsTable(page, test.args.sortBy);

          if (test.args.isFloat) {
            const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
            const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

            const expectedResult: number[] = await basicHelper.sortArrayNumber(nonSortedTableFloat);

            if (test.args.sortDirection === 'asc') {
              expect(sortedTableFloat).to.deep.equal(expectedResult);
            } else {
              expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
            }
          } else {
            const expectedResult: string[] = await basicHelper.sortArray(nonSortedTable);

            if (test.args.sortDirection === 'asc') {
              expect(sortedTable).to.deep.equal(expectedResult);
            } else {
              expect(sortedTable).to.deep.equal(expectedResult.reverse());
            }
          }
        },
      );
    });
  });

  // 3 : Disable, enable Brands
  describe('Disable, enable created Brands', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDisableBrands', baseContext);

      await brandsPage.filterBrands(page, 'input', 'name', 'todelete');

      const textColumn = await brandsPage.getTextColumnFromTableBrands(page, 1, 'name');
      expect(textColumn).to.contains('todelete');
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} brands`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Brand`, baseContext);

        const textResult = await brandsPage.bulkSetBrandsStatus(page, test.args.enabledValue);
        expect(textResult).to.be.equal(brandsPage.successfulUpdateStatusMessage);

        const numberOfBrandsInGrid = await brandsPage.getNumberOfElementInGrid(page, tableName);
        expect(numberOfBrandsInGrid).to.be.equal(numberOfImportedBrands);

        for (let i = 1; i <= numberOfBrandsInGrid; i++) {
          const brandStatus = await brandsPage.getBrandStatus(page, i);
          expect(brandStatus).to.equal(test.args.enabledValue);
        }
      });
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkEdit', baseContext);

      const numberOfBrandsAfterReset = await brandsPage.resetAndGetNumberOfLines(page, tableName);
      expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands);
    });
  });

  // 4 : Delete brands with bulk actions
  describe('Delete Brands with bulk actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeleteBrands', baseContext);

      await brandsPage.filterBrands(page, 'input', 'name', 'todelete');

      const textColumn = await brandsPage.getTextColumnFromTableBrands(page, 1, 'name');
      expect(textColumn).to.contains('todelete');
    });

    it('should delete with bulk actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteBrands', baseContext);

      const deleteTextResult = await brandsPage.deleteWithBulkActions(page, tableName);
      expect(deleteTextResult).to.be.equal(brandsPage.successfulDeleteMessage);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteBrands', baseContext);

      const numberOfBrandsAfterReset = await brandsPage.resetAndGetNumberOfLines(page, tableName);
      expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands - numberOfImportedBrands);
    });
  });
});
