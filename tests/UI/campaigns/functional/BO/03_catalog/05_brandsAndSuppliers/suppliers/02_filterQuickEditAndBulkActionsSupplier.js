/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
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

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_suppliers_filterQuickEditAndBulkActionsSupplier';


let browserContext;
let page;

const firstSupplierData = new SupplierFaker();
const secondSupplierData = new SupplierFaker();

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

// Filter, Quick edit and bulk actions suppliers
describe('Filter, Quick edit and bulk actions suppliers', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await Promise.all([
      files.deleteFile(firstSupplierData.logo),
      files.deleteFile(secondSupplierData.logo),
    ]);
  });

  // Login into BO
  loginCommon.loginBO();

  // Go to brands Page
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

  // Go to Suppliers Page
  it('should go to suppliers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSuppliersPage', baseContext);

    await this.pageObjects.brandsPage.goToSubTabSuppliers();
    const pageTitle = await this.pageObjects.suppliersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.suppliersPage.pageTitle);
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

  // 2: Filter Suppliers
  describe('Filter suppliers', async () => {
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
        expected: 'check',
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        if (test.args.filterBy === 'active') {
          await this.pageObjects.suppliersPage.filterSupplierEnabled(
            test.args.filterType,
            test.args.filterBy,
            test.args.filterValue,
          );
        } else {
          await this.pageObjects.suppliersPage.filterTable(
            test.args.filterType,
            test.args.filterBy,
            test.args.filterValue,
          );
        }

        // Check number of suppliers
        const numberOfSuppliersAfterFilter = await this.pageObjects.suppliersPage.getNumberOfElementInGrid();
        await expect(numberOfSuppliersAfterFilter).to.be.at.most(numberOfSuppliers);

        // check text column in all rows after filter
        for (let i = 1; i <= numberOfSuppliersAfterFilter; i++) {
          const textColumn = await this.pageObjects.suppliersPage.getTextColumnFromTableSupplier(i, test.args.filterBy);

          if (test.expected !== undefined) {
            await expect(textColumn).to.contains(test.expected);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset filter', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfSuppliersAfterReset = await this.pageObjects.suppliersPage.resetAndGetNumberOfLines();
        await expect(numberOfSuppliersAfterReset).to.be.equal(numberOfSuppliers);
      });
    });
  });

  // 3: Quick Edit Suppliers
  describe('Quick edit first supplier', async () => {
    it('should filter supplier by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await this.pageObjects.suppliersPage.filterTable(
        'input',
        'name',
        firstSupplierData.name,
      );

      // Check number od suppliers
      const numberOfSuppliersAfterFilter = await this.pageObjects.suppliersPage.getNumberOfElementInGrid();
      await expect(numberOfSuppliersAfterFilter).to.be.at.most(numberOfSuppliers);

      // check text column of first row after filter
      const textColumn = await this.pageObjects.suppliersPage.getTextColumnFromTableSupplier(1, 'name');
      await expect(textColumn).to.contains(firstSupplierData.name);
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} first supplier`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Supplier`, baseContext);

        const isActionPerformed = await this.pageObjects.suppliersPage.updateEnabledValue(1, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await this.pageObjects.suppliersPage.getTextContent(
            this.pageObjects.suppliersPage.alertSuccessBlockParagraph,
          );
          await expect(resultMessage).to.contains(this.pageObjects.suppliersPage.successfulUpdateStatusMessage);
        }

        const supplierStatus = await this.pageObjects.suppliersPage.getToggleColumnValue(1);
        await expect(supplierStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterQuickEdit', baseContext);

      const numberOfSuppliersAfterReset = await this.pageObjects.suppliersPage.resetAndGetNumberOfLines();
      await expect(numberOfSuppliersAfterReset).to.be.equal(numberOfSuppliers);
    });
  });

  // 4: Enable, disable and delete with bulk actions
  describe('Enable, disable and delete with bulk actions', async () => {
    const tests = [
      {args: {action: 'disable', enabledValue: false}, expected: 'clear'},
      {args: {action: 'enable', enabledValue: true}, expected: 'check'},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} with bulk actions`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulk${test.args.action}Suppliers`, baseContext);

        const disableTextResult = await this.pageObjects.suppliersPage.changeSuppliersEnabledColumnBulkActions(
          test.args.enabledValue,
        );

        await expect(disableTextResult).to.be.equal(this.pageObjects.suppliersPage.successfulUpdateStatusMessage);

        // Check that element in grid are disabled
        const numberOfSuppliersInGrid = await this.pageObjects.suppliersPage.getNumberOfElementInGrid();
        await expect(numberOfSuppliersInGrid).to.be.at.most(numberOfSuppliers);

        for (let i = 1; i <= numberOfSuppliersInGrid; i++) {
          const textColumn = await this.pageObjects.suppliersPage.getTextColumnFromTableSupplier(1, 'active');
          await expect(textColumn).to.contains(test.expected);
        }
      });
    });

    it('should delete with bulk actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteSuppliers', baseContext);

      const deleteTextResult = await this.pageObjects.suppliersPage.deleteWithBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.suppliersPage.successfulMultiDeleteMessage);

      // Check that empty row is visible (no elements in table)
      const tableIsVisible = await this.pageObjects.suppliersPage.elementVisible(
        this.pageObjects.suppliersPage.tableEmptyRow,
        1000,
      );

      await expect(tableIsVisible).to.be.true;
    });
  });
});
