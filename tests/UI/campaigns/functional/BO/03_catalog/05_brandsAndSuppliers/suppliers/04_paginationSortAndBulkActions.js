require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import data
const SupplierFaker = require('@data/faker/supplier');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const brandsPage = require('@pages/BO/catalog/brands');
const suppliersPage = require('@pages/BO/catalog/suppliers');
const addSupplierPage = require('@pages/BO/catalog/suppliers/add');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_suppliers_paginationSortAndBulkActions';

let browserContext;
let page;
let numberOfSuppliers = 0;

/*
Create 11 suppliers
Paginate between pages
Sort suppliers table
Bulk enable and disable suppliers
Bulk delete them
 */
describe('BO - Catalog - Brands & Suppliers : Pagination and sort suppliers', async () => {
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

  // Go to brands page
  it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.brandsAndSuppliersLink,
    );
    await dashboardPage.closeSfToolBar(page);

    const pageTitle = await brandsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(brandsPage.pageTitle);
  });

  // Go to suppliers page
  it('should go to Suppliers page and get number of suppliers', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSuppliersPage', baseContext);

    await brandsPage.goToSubTabSuppliers(page);
    numberOfSuppliers = await suppliersPage.resetAndGetNumberOfLines(page);
    const pageTitle = await suppliersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(suppliersPage.pageTitle);
  });

  // 1 : Create 11 new suppliers
  describe('Create 11 suppliers in BO', async () => {
    const creationTests = new Array(11).fill(0, 0, 11);
    creationTests.forEach((test, index) => {
      const createSupplierData = new SupplierFaker({name: `todelete${index}`});
      before(() => files.generateImage(createSupplierData.logo));

      it('should go to add new supplier page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewSupplierPage${index}`, baseContext);

        await suppliersPage.goToAddNewSupplierPage(page);
        const pageTitle = await addSupplierPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addSupplierPage.pageTitle);
      });

      it(`should create supplier n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createSupplier${index}`, baseContext);

        const result = await addSupplierPage.createEditSupplier(page, createSupplierData);
        await expect(result).to.equal(suppliersPage.successfulCreationMessage);

        const numberOfSuppliersAfterCreation = await suppliersPage.getNumberOfElementInGrid(page);
        await expect(numberOfSuppliersAfterCreation).to.be.equal(numberOfSuppliers + 1 + index);
      });

      after(() => files.deleteFile(createSupplierData.logo));
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo10', baseContext);

      const paginationNumber = await suppliersPage.selectPaginationLimit(page, '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await suppliersPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await suppliersPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await suppliersPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 3 : Sort suppliers
  describe('Sort suppliers', async () => {
    const brandsTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_supplier', sortDirection: 'desc', isFloat: true,
        },
      },
      {args: {testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortByNumberProductsAsc', sortBy: 'products_count', sortDirection: 'asc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByNumberProductsDesc', sortBy: 'products_count', sortDirection: 'desc', isFloat: true,
        },
      },
      {args: {testIdentifier: 'sortByEnabledAsc', sortBy: 'active', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByEnabledDesc', sortBy: 'active', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_supplier', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    brandsTests.forEach((test) => {
      it(
        `should sort suppliers by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

          let nonSortedTable = await suppliersPage.getAllRowsColumnContent(page, test.args.sortBy);

          await suppliersPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

          let sortedTable = await suppliersPage.getAllRowsColumnContent(page, test.args.sortBy);

          if (test.args.isFloat) {
            nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
            sortedTable = await sortedTable.map(text => parseFloat(text));
          }

          const expectedResult = await suppliersPage.sortArray(nonSortedTable, test.args.isFloat);

          if (test.args.sortDirection === 'asc') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        },
      );
    });
  });

  // 4: Enable, disable and delete with bulk actions
  describe('Bulk enable, disable and delete suppliers', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await suppliersPage.filterTable(
        page,
        'input',
        'name',
        'todelete',
      );
      const textColumn = await suppliersPage.getTextColumnFromTableSupplier(page, 1, 'name');
      await expect(textColumn).to.contains('todelete');
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should bulk ${test.args.action} suppliers`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulk${test.args.action}Suppliers`, baseContext);

        const disableTextResult = await suppliersPage.bulkSetStatus(page, test.args.enabledValue);

        await expect(disableTextResult).to.be.equal(suppliersPage.successfulUpdateStatusMessage);

        // Check that element in grid are disabled
        const numberOfSuppliersInGrid = await suppliersPage.getNumberOfElementInGrid(page);
        await expect(numberOfSuppliersInGrid).to.be.at.most(11);

        for (let i = 1; i <= numberOfSuppliersInGrid; i++) {
          const supplierStatus = await suppliersPage.getStatus(page, i);
          await expect(supplierStatus).to.equal(test.args.enabledValue);
        }
      });
    });

    it('should bulk delete suppliers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteSuppliers', baseContext);

      const deleteTextResult = await suppliersPage.deleteWithBulkActions(page);
      await expect(deleteTextResult).to.be.equal(suppliersPage.successfulMultiDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterBulkDelete', baseContext);

      const numberOfSuppliersAfterReset = await suppliersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfSuppliersAfterReset).to.be.equal(numberOfSuppliers);
    });
  });
});
