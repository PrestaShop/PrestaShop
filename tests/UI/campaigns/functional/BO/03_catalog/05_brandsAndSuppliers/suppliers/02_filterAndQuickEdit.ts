import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boBrandsPage,
  boDashboardPage,
  boLoginPage,
  boSuppliersPage,
  type BrowserContext,
  dataSuppliers,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_brandsAndSuppliers_suppliers_filterAndQuickEdit';

// Filter and quick edit suppliers
describe('BO - Catalog - Brands & Suppliers : Filter and quick edit suppliers', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSuppliers: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  // Go to brands Page
  it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.brandsAndSuppliersLink,
    );
    await boBrandsPage.closeSfToolBar(page);

    const pageTitle = await boBrandsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boBrandsPage.pageTitle);
  });

  it('should go to Suppliers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSuppliersPage', baseContext);

    await boBrandsPage.goToSubTabSuppliers(page);

    const pageTitle = await boSuppliersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSuppliersPage.pageTitle);
  });

  it('should reset filter', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstReset', baseContext);

    numberOfSuppliers = await boSuppliersPage.resetAndGetNumberOfLines(page);
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
            filterValue: dataSuppliers.fashion.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterProductsCount',
            filterType: 'input',
            filterBy: 'products_count',
            filterValue: dataSuppliers.fashion.products.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: dataSuppliers.accessories.enabled ? '1' : '0',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        if (test.args.filterBy === 'active') {
          await boSuppliersPage.filterSupplierEnabled(
            page,
            test.args.filterValue === '1',
          );
        } else {
          await boSuppliersPage.filterTable(
            page,
            test.args.filterType,
            test.args.filterBy,
            test.args.filterValue,
          );
        }

        // Check number of suppliers
        const numberOfSuppliersAfterFilter = await boSuppliersPage.getNumberOfElementInGrid(page);
        expect(numberOfSuppliersAfterFilter).to.be.at.most(numberOfSuppliers);

        // Check text column or status in all rows after filter
        for (let i = 1; i <= numberOfSuppliersAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const supplierStatus = await boSuppliersPage.getStatus(page, i);
            expect(supplierStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await boSuppliersPage.getTextColumnFromTableSupplier(page, i, test.args.filterBy);
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset filter', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfSuppliersAfterReset = await boSuppliersPage.resetAndGetNumberOfLines(page);
        expect(numberOfSuppliersAfterReset).to.be.equal(numberOfSuppliers);
      });
    });
  });

  // 3: Quick Edit Suppliers
  describe('Quick edit first supplier', async () => {
    it('should filter supplier by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await boSuppliersPage.filterTable(page, 'input', 'name', dataSuppliers.fashion.name);

      // Check number od suppliers
      const numberOfSuppliersAfterFilter = await boSuppliersPage.getNumberOfElementInGrid(page);
      expect(numberOfSuppliersAfterFilter).to.be.at.most(numberOfSuppliers);

      // check text column of first row after filter
      const textColumn = await boSuppliersPage.getTextColumnFromTableSupplier(page, 1, 'name');
      expect(textColumn).to.contains(dataSuppliers.fashion.name);
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} first supplier`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Supplier`, baseContext);

        const isActionPerformed = await boSuppliersPage.setStatus(page, 1, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await boSuppliersPage.getAlertSuccessBlockParagraphContent(page);
          expect(resultMessage).to.contains(boSuppliersPage.successfulUpdateStatusMessage);
        }

        const supplierStatus = await boSuppliersPage.getStatus(page, 1);
        expect(supplierStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterQuickEdit', baseContext);

      const numberOfSuppliersAfterReset = await boSuppliersPage.resetAndGetNumberOfLines(page);
      expect(numberOfSuppliersAfterReset).to.be.equal(numberOfSuppliers);
    });
  });
});
