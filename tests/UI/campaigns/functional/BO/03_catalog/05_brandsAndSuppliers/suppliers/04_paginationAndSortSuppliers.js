require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import data
const SupplierFaker = require('@data/faker/supplier');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/catalog/brands');
const SuppliersPage = require('@pages/BO/catalog/suppliers');
const AddSupplierPage = require('@pages/BO/catalog/suppliers/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_suppliers_paginationAndSortSuppliers';

let browser;
let browserContext;
let page;
let numberOfSuppliers = 0;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
    suppliersPage: new SuppliersPage(page),
    addSupplierPage: new AddSupplierPage(page),
  };
};
/*
Create 11 suppliers
Paginate between pages
Sort suppliers table
Delete suppliers with bulk actions
 */
describe('Pagination and sort suppliers', async () => {
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

  // Go to brands page
  it('should go to brands page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.brandsAndSuppliersLink,
    );
    await this.pageObjects.dashboardPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
  });

  // Go to suppliers page
  it('should go to suppliers page and get number of suppliers', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSuppliersPage', baseContext);

    await this.pageObjects.brandsPage.goToSubTabSuppliers();
    numberOfSuppliers = await this.pageObjects.suppliersPage.resetAndGetNumberOfLines();
    const pageTitle = await this.pageObjects.suppliersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.suppliersPage.pageTitle);
  });

  // 1 : Create 11 new suppliers
  const creationTests = new Array(11).fill(0, 0, 11);
  creationTests.forEach((test, index) => {
    describe(`Create supplier n°${index + 1} in BO`, async () => {
      const createSupplierData = new SupplierFaker({name: `todelete${index}`});

      it('should go to add new supplier page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewSupplierPage${index}`, baseContext);

        await this.pageObjects.suppliersPage.goToAddNewSupplierPage();
        const pageTitle = await this.pageObjects.addSupplierPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addSupplierPage.pageTitle);
      });

      it('should create supplier and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createSupplier${index}`, baseContext);

        const result = await this.pageObjects.addSupplierPage.createEditSupplier(createSupplierData);
        await expect(result).to.equal(this.pageObjects.suppliersPage.successfulCreationMessage);

        const numberOfSuppliersAfterCreation = await this.pageObjects.suppliersPage.getNumberOfElementInGrid();
        await expect(numberOfSuppliersAfterCreation).to.be.equal(numberOfSuppliers + 1 + index);
      });

      after(() => files.deleteFile(createSupplierData.logo));
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await this.pageObjects.suppliersPage.selectPaginationLimit('10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await this.pageObjects.suppliersPage.paginationNext();
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await this.pageObjects.suppliersPage.paginationPrevious();
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await this.pageObjects.suppliersPage.selectPaginationLimit('50');
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
      {args: {testIdentifier: 'sortByNumberProductsAsc', sortBy: 'products_count', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByNumberProductsDesc', sortBy: 'products_count', sortDirection: 'desc'}},
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

          let nonSortedTable = await this.pageObjects.suppliersPage.getAllRowsColumnContent(test.args.sortBy);

          await this.pageObjects.suppliersPage.sortTable(test.args.sortBy, test.args.sortDirection);

          let sortedTable = await this.pageObjects.suppliersPage.getAllRowsColumnContent(test.args.sortBy);

          if (test.args.isFloat) {
            nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
            sortedTable = await sortedTable.map(text => parseFloat(text));
          }

          const expectedResult = await this.pageObjects.suppliersPage.sortArray(nonSortedTable, test.args.isFloat);

          if (test.args.sortDirection === 'asc') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        },
      );
    });
  });

  // 4 : Delete suppliers created with bulk actions
  describe('Delete suppliers with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await this.pageObjects.suppliersPage.filterTable(
        'input',
        'name',
        'todelete',
      );
      const textColumn = await this.pageObjects.suppliersPage.getTextColumnFromTableSupplier(1, 'name');
      await expect(textColumn).to.contains('todelete');
    });

    it('should delete suppliers with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await this.pageObjects.suppliersPage.deleteWithBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.suppliersPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfSuppliersAfterReset = await this.pageObjects.suppliersPage.resetAndGetNumberOfLines();
      await expect(numberOfSuppliersAfterReset).to.equal(numberOfSuppliers);
    });
  });
});
