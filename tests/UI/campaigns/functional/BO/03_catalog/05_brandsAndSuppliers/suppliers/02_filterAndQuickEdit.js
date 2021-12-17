require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const brandsPage = require('@pages/BO/catalog/brands');
const suppliersPage = require('@pages/BO/catalog/suppliers');

// Import data
const {Suppliers} = require('@data/demo/suppliers');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_suppliers_filterAndQuickEdit';

let browserContext;
let page;

let numberOfSuppliers = 0;

// Filter and quick edit suppliers
describe('BO - Catalog - Brands & Suppliers : Filter and quick edit suppliers', async () => {
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

  it('should go to Suppliers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSuppliersPage', baseContext);

    await brandsPage.goToSubTabSuppliers(page);
    const pageTitle = await suppliersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(suppliersPage.pageTitle);
  });

  it('should reset filter', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstReset', baseContext);

    numberOfSuppliers = await suppliersPage.resetAndGetNumberOfLines(page);
    await expect(numberOfSuppliers).to.be.at.least(0);
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
            filterValue: Suppliers.fashionSupplier.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterProductsCount',
            filterType: 'input',
            filterBy: 'products_count',
            filterValue: Suppliers.fashionSupplier.numberOfProducts.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: Suppliers.accessoriesSupplier.enabled,
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

      await suppliersPage.filterTable(page, 'input', 'name', Suppliers.fashionSupplier.name);

      // Check number od suppliers
      const numberOfSuppliersAfterFilter = await suppliersPage.getNumberOfElementInGrid(page);
      await expect(numberOfSuppliersAfterFilter).to.be.at.most(numberOfSuppliers);

      // check text column of first row after filter
      const textColumn = await suppliersPage.getTextColumnFromTableSupplier(page, 1, 'name');
      await expect(textColumn).to.contains(Suppliers.fashionSupplier.name);
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
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
});
