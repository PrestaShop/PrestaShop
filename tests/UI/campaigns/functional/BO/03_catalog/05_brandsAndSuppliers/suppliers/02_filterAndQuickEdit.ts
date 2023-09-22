// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import brandsPage from '@pages/BO/catalog/brands';
import suppliersPage from '@pages/BO/catalog/suppliers';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import Suppliers from '@data/demo/suppliers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_brandsAndSuppliers_suppliers_filterAndQuickEdit';

// Filter and quick edit suppliers
describe('BO - Catalog - Brands & Suppliers : Filter and quick edit suppliers', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSuppliers: number = 0;

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
    expect(pageTitle).to.contains(brandsPage.pageTitle);
  });

  it('should go to Suppliers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSuppliersPage', baseContext);

    await brandsPage.goToSubTabSuppliers(page);

    const pageTitle = await suppliersPage.getPageTitle(page);
    expect(pageTitle).to.contains(suppliersPage.pageTitle);
  });

  it('should reset filter', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstReset', baseContext);

    numberOfSuppliers = await suppliersPage.resetAndGetNumberOfLines(page);
    expect(numberOfSuppliers).to.be.at.least(0);
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
            filterValue: Suppliers.fashionSupplier.products.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: Suppliers.accessoriesSupplier.enabled ? '1' : '0',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        if (test.args.filterBy === 'active') {
          await suppliersPage.filterSupplierEnabled(
            page,
            test.args.filterValue === '1',
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
        expect(numberOfSuppliersAfterFilter).to.be.at.most(numberOfSuppliers);

        // Check text column or status in all rows after filter
        for (let i = 1; i <= numberOfSuppliersAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const supplierStatus = await suppliersPage.getStatus(page, i);
            expect(supplierStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await suppliersPage.getTextColumnFromTableSupplier(page, i, test.args.filterBy);
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset filter', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfSuppliersAfterReset = await suppliersPage.resetAndGetNumberOfLines(page);
        expect(numberOfSuppliersAfterReset).to.be.equal(numberOfSuppliers);
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
      expect(numberOfSuppliersAfterFilter).to.be.at.most(numberOfSuppliers);

      // check text column of first row after filter
      const textColumn = await suppliersPage.getTextColumnFromTableSupplier(page, 1, 'name');
      expect(textColumn).to.contains(Suppliers.fashionSupplier.name);
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
          expect(resultMessage).to.contains(suppliersPage.successfulUpdateStatusMessage);
        }

        const supplierStatus = await suppliersPage.getStatus(page, 1);
        expect(supplierStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterQuickEdit', baseContext);

      const numberOfSuppliersAfterReset = await suppliersPage.resetAndGetNumberOfLines(page);
      expect(numberOfSuppliersAfterReset).to.be.equal(numberOfSuppliers);
    });
  });
});
