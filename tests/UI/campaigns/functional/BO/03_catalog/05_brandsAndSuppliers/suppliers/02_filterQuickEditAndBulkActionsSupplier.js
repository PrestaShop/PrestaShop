require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import data
const SupplierFaker = require('@data/faker/supplier');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const brandsPage = require('@pages/BO/catalog/brands');
const suppliersPage = require('@pages/BO/catalog/suppliers');
const addSupplierPage = require('@pages/BO/catalog/suppliers/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_suppliers_filterQuickEditAndBulkActionsSupplier';

let browserContext;
let page;

const firstSupplierData = new SupplierFaker();
const secondSupplierData = new SupplierFaker();

let numberOfSuppliers = 0;

// Filter, quick edit and bulk actions suppliers
describe('BO - Catalog - Brands & Suppliers : Filter, quick edit and bulk actions suppliers', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Generate logos
    await Promise.all([
      files.generateImage(firstSupplierData.logo),
      files.generateImage(secondSupplierData.logo),
    ]);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await Promise.all([
      files.deleteFile(firstSupplierData.logo),
      files.deleteFile(secondSupplierData.logo),
    ]);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  // Go to brands Page
  it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
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

  // Go to Suppliers Page
  it('should go to Suppliers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSuppliersPage', baseContext);

    await brandsPage.goToSubTabSuppliers(page);
    const pageTitle = await suppliersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(suppliersPage.pageTitle);
  });

  // 1: Create 2 suppliers
  describe('Create 2 suppliers', async () => {
    const tests = [
      {args: {supplierToCreate: firstSupplierData}},
      {args: {supplierToCreate: secondSupplierData}},
    ];

    tests.forEach((test, index) => {
      it('should go to new supplier page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddSupplierPage${index + 1}`, baseContext);

        await suppliersPage.goToAddNewSupplierPage(page);
        const pageTitle = await addSupplierPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addSupplierPage.pageTitle);
      });

      it(`should create supplier n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createSupplier${index + 1}`, baseContext);

        const result = await addSupplierPage.createEditSupplier(page, test.args.supplierToCreate);
        await expect(result).to.equal(suppliersPage.successfulCreationMessage);
      });
    });

    it('should reset filter and get number of suppliers after creation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterCreation', baseContext);

      numberOfSuppliers = await suppliersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfSuppliers).to.be.at.least(2);
    });
  });

  // 2: Filter Suppliers
  describe('Filter suppliers table', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: firstSupplierData.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterProductsCount',
            filterType: 'input',
            filterBy: 'products_count',
            filterValue: firstSupplierData.products.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: firstSupplierData.enabled,
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        if (test.args.filterBy === 'active') {
          await suppliersPage.filterSupplierEnabled(
            page,
            test.args.filterValue,
          );
        } else {
          await suppliersPage.filterTable(
            page,
            test.args.filterType,
            test.args.filterBy,
            test.args.filterValue,
          );
        }

        // Check number of suppliers
        const numberOfSuppliersAfterFilter = await suppliersPage.getNumberOfElementInGrid(page);
        await expect(numberOfSuppliersAfterFilter).to.be.at.most(numberOfSuppliers);

        // Check text column or status in all rows after filter
        for (let i = 1; i <= numberOfSuppliersAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const supplierStatus = await suppliersPage.getStatus(page, i);
            await expect(supplierStatus).to.equal(test.args.filterValue);
          } else {
            const textColumn = await suppliersPage.getTextColumnFromTableSupplier(page, i, test.args.filterBy);
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset filter', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfSuppliersAfterReset = await suppliersPage.resetAndGetNumberOfLines(page);
        await expect(numberOfSuppliersAfterReset).to.be.equal(numberOfSuppliers);
      });
    });
  });

  // 3: Quick Edit Suppliers
  describe('Quick edit first supplier', async () => {
    it('should filter supplier by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await suppliersPage.filterTable(page, 'input', 'name', firstSupplierData.name);

      // Check number od suppliers
      const numberOfSuppliersAfterFilter = await suppliersPage.getNumberOfElementInGrid(page);
      await expect(numberOfSuppliersAfterFilter).to.be.at.most(numberOfSuppliers);

      // check text column of first row after filter
      const textColumn = await suppliersPage.getTextColumnFromTableSupplier(page, 1, 'name');
      await expect(textColumn).to.contains(firstSupplierData.name);
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} first supplier`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Supplier`, baseContext);

        const isActionPerformed = await suppliersPage.setStatus(page, 1, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await suppliersPage.getAlertSuccessBlockParagraphContent(page);
          await expect(resultMessage).to.contains(suppliersPage.successfulUpdateStatusMessage);
        }

        const supplierStatus = await suppliersPage.getStatus(page, 1);
        await expect(supplierStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterQuickEdit', baseContext);

      const numberOfSuppliersAfterReset = await suppliersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfSuppliersAfterReset).to.be.equal(numberOfSuppliers);
    });
  });

  // 4: Enable, disable and delete with bulk actions
  describe('Enable, disable and delete with bulk actions', async () => {
    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} with bulk actions`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulk${test.args.action}Suppliers`, baseContext);

        const disableTextResult = await suppliersPage.bulkSetStatus(
          page,
          test.args.enabledValue,
        );

        await expect(disableTextResult).to.be.equal(suppliersPage.successfulUpdateStatusMessage);

        // Check that element in grid are disabled
        const numberOfSuppliersInGrid = await suppliersPage.getNumberOfElementInGrid(page);
        await expect(numberOfSuppliersInGrid).to.be.at.most(numberOfSuppliers);

        for (let i = 1; i <= numberOfSuppliersInGrid; i++) {
          const supplierStatus = await suppliersPage.getStatus(page, i);
          await expect(supplierStatus).to.equal(test.args.enabledValue);
        }
      });
    });

    it('should delete with bulk actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteSuppliers', baseContext);

      const deleteTextResult = await suppliersPage.deleteWithBulkActions(page);
      await expect(deleteTextResult).to.be.equal(suppliersPage.successfulMultiDeleteMessage);

      // Check that empty row is visible (no elements in table)
      const tableIsVisible = await suppliersPage.elementVisible(
        page,
        suppliersPage.tableEmptyRow,
        1000,
      );

      await expect(tableIsVisible).to.be.true;
    });
  });
});
