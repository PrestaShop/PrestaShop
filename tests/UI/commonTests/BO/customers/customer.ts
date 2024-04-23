// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import customersPage from '@pages/BO/customers';
import addCustomerPage from '@pages/BO/customers/add';
import dashboardPage from '@pages/BO/dashboard';

import {
  // Import data
  FakerCustomer,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

let browserContext: BrowserContext;
let page: Page;
let numberOfCustomers: number;

/**
 * Function to create customer
 * @param customerData {FakerCustomer} Data to set to create customer
 * @param baseContext {string} String to identify the test
 */
function createCustomerTest(customerData: FakerCustomer, baseContext: string = 'commonTests-createCustomerTest'): void {
  describe('PRE-TEST: Create customer', async () => {
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

    it('should go \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.customersParentLink, dashboardPage.customersLink);

      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);

      await customersPage.goToAddNewCustomerPage(page);

      const pageTitle = await addCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
    });

    it('should create customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

      const textResult = await addCustomerPage.createEditCustomer(page, customerData);
      expect(textResult).to.equal(customersPage.successfulCreationMessage);
    });
  });
}

/**
 * Function to create B2B customer
 * @param customerData {FakerCustomer} Data to set to create customer
 * @param baseContext {string} String to identify the test
 */
function createCustomerB2BTest(customerData: FakerCustomer, baseContext: string = 'commonTests-createCustomerB2BTest'): void {
  describe('PRE-TEST: Create B2B customer', async () => {
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

    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.customersParentLink, dashboardPage.customersLink);

      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);

      await customersPage.goToAddNewCustomerPage(page);

      const pageTitle = await addCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
    });

    it('should create B2B customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

      const textResult = await addCustomerPage.createEditB2BCustomer(page, customerData);
      expect(textResult).to.equal(customersPage.successfulCreationMessage);
    });
  });
}

/**
 * Function to delete customer
 * @param customerData {FakerCustomer} Data to set to delete customer
 * @param baseContext {string} String to identify the test
 */
function deleteCustomerTest(customerData: FakerCustomer, baseContext: string = 'commonTests-deleteCustomerTest'): void {
  describe('POST-TEST: Delete customer', async () => {
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

    it('should go \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.customersParentLink, dashboardPage.customersLink);

      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCustomerFilterFirst', baseContext);

      numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomers).to.be.above(0);
    });

    it('should filter list by customer email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await customersPage.filterCustomers(page, 'input', 'email', customerData.email);

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textResult).to.contains(customerData.email);
    });

    it('should delete customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);

      const deleteTextResult = await customersPage.deleteCustomer(page, 1);
      expect(deleteTextResult).to.be.equal(customersPage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of customers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteCustomer', baseContext);

      const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomersAfterReset).to.be.above(0);
    });
  });
}

/**
 * Function to bulk delete customers
 * @param filterBy {string} Value to filter by
 * @param value {string} Value to set in filter input to delete
 * @param baseContext {string} String to identify the test
 */
function bulkDeleteCustomersTest(
  filterBy: string,
  value: string,
  baseContext: string = 'commonTests-deleteCustomersByBulkActionsTest',
): void {
  describe('POST-TEST: Delete customers by bulk actions', async () => {
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

    it('should go \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.customersParentLink, dashboardPage.customersLink);

      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCustomerFilterFirst', baseContext);

      numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomers).to.be.above(0);
    });

    it(`should filter list by '${filterBy}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkEdit', baseContext);

      await customersPage.filterCustomers(page, 'input', filterBy, value);

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, filterBy);
      expect(textResult).to.contains(value);
    });

    it('should delete customers with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCustomers', baseContext);

      const deleteTextResult = await customersPage.deleteCustomersBulkActions(page);
      expect(deleteTextResult).to.be.equal(customersPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomersAfterReset).to.be.below(numberOfCustomers);
    });
  });
}

export {
  deleteCustomerTest,
  createCustomerTest,
  createCustomerB2BTest,
  bulkDeleteCustomersTest,
};
