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

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_suppliers_sortSuppliers';

let browser;
let page;

const firstSupplierData = new SupplierFaker();
const secondSupplierData = new SupplierFaker();
const thirdSupplierData = new SupplierFaker({enabled: false});

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

// Sort suppliers table
describe('Sort suppliers', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);

    await Promise.all([
      files.deleteFile(firstSupplierData.logo),
      files.deleteFile(secondSupplierData.logo),
      files.deleteFile(thirdSupplierData.logo),
    ]);
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

    await this.pageObjects.brandsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
  });

  // Go to suppliers page
  it('should go to suppliers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSuppliersPage', baseContext);

    await this.pageObjects.brandsPage.goToSubTabSuppliers();
    const pageTitle = await this.pageObjects.suppliersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.suppliersPage.pageTitle);
  });

  // 1: Create 3 suppliers
  describe('Create 3 suppliers', async () => {
    const tests = [
      {args: {supplierToCreate: firstSupplierData}},
      {args: {supplierToCreate: secondSupplierData}},
      {args: {supplierToCreate: thirdSupplierData}},
    ];

    tests.forEach((test, index) => {
      it('should go to new supplier page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddSupplierPage${index + 1}`, baseContext);

        await this.pageObjects.suppliersPage.goToAddNewSupplierPage();
        const pageTitle = await this.pageObjects.addSupplierPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addSupplierPage.pageTitle);
      });

      it('should create supplier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createSupplier${index + 1}`, baseContext);

        const result = await this.pageObjects.addSupplierPage.createEditSupplier(test.args.supplierToCreate);
        await expect(result).to.equal(this.pageObjects.suppliersPage.successfulCreationMessage);
      });
    });

    it('should reset filter and get number of suppliers after creation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterCreation', baseContext);

      numberOfSuppliers = await this.pageObjects.suppliersPage.resetAndGetNumberOfLines();
      await expect(numberOfSuppliers).to.be.at.least(2);
    });
  });

  // 2 : Sort suppliers
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

  // 3 : Delete the 3 created suppliers
  describe('Delete the 3 created suppliers', async () => {
    const tests = [
      {args: {supplierData: firstSupplierData}},
      {args: {supplierData: secondSupplierData}},
      {args: {supplierData: thirdSupplierData}},
    ];

    tests.forEach((test, index) => {
      it('should filter supplier by name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterToDelete${index + 1}`, baseContext);

        await this.pageObjects.suppliersPage.filterTable(
          'input',
          'name',
          test.args.supplierData.name,
        );

        // Check number of suppliers
        const numberOfSuppliersAfterFilter = await this.pageObjects.suppliersPage.getNumberOfElementInGrid();
        await expect(numberOfSuppliersAfterFilter).to.be.at.most(numberOfSuppliers);

        // check text column of first row after filter
        const textColumn = await this.pageObjects.suppliersPage.getTextColumnFromTableSupplier(1, 'name');
        await expect(textColumn).to.contains(test.args.supplierData.name);
      });

      it('should delete supplier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteSupplier${index + 1}`, baseContext);

        // delete supplier in first row
        const result = await this.pageObjects.suppliersPage.deleteSupplier(1);
        await expect(result).to.be.equal(this.pageObjects.suppliersPage.successfulDeleteMessage);
      });
    });
  });
});
