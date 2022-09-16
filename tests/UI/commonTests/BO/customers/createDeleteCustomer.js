require('module-alias/register');
// Import utils
const helper = require('@utils/helpers');

// Import login test
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const customersPage = require('@pages/BO/customers');
const addCustomerPage = require('@pages/BO/customers/add');

// Import test context
const testContext = require('@utils/testContext');

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfCustomers;

/**
 * Function to create customer
 * @param customerData {CustomerData} Data to set to create customer
 * @param baseContext {string} String to identify the test
 */
function createCustomerTest(customerData, baseContext = 'commonTests-createCustomerTest') {
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
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);

      await customersPage.goToAddNewCustomerPage(page);
      const pageTitle = await addCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
    });

    it('should create customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

      const textResult = await addCustomerPage.createEditCustomer(page, customerData);
      await expect(textResult).to.equal(customersPage.successfulCreationMessage);
    });
  });
}

/**
 * Function to create B2B customer
 * @param customerData {CustomerData} Data to set to create customer
 * @param baseContext {string} String to identify the test
 */
function createCustomerB2BTest(customerData, baseContext = 'commonTests-createCustomerB2BTest') {
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
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);

      await customersPage.goToAddNewCustomerPage(page);
      const pageTitle = await addCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
    });

    it('should create B2B customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

      const textResult = await addCustomerPage.createEditB2BCustomer(page, customerData);
      await expect(textResult).to.equal(customersPage.successfulCreationMessage);
    });
  });
}

/**
 * Function to delete customer
 * @param customerData {CustomerData} Data to set to delete customer
 * @param baseContext {string} String to identify the test
 */
function deleteCustomerTest(customerData, baseContext = 'commonTests-deleteCustomerTest') {
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
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCustomerFilterFirst', baseContext);

      numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomers).to.be.above(0);
    });

    it('should filter list by customer email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await customersPage.filterCustomers(page, 'input', 'email', customerData.email);

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textResult).to.contains(customerData.email);
    });

    it('should delete customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);

      const deleteTextResult = await customersPage.deleteCustomer(page, 1);
      await expect(deleteTextResult).to.be.equal(customersPage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of customers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteCustomer', baseContext);

      const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomersAfterReset).to.be.above(0);
    });
  });
}

/**
 * Function to bulk delete customers
 * @param filterBy {string} Value to filter by
 * @param value {string} Value to set in filter input to delete
 * @param baseContext {string} String to identify the test
 */
function bulkDeleteCustomersTest(filterBy, value, baseContext = 'commonTests-deleteCustomersByBulkActionsTest') {
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
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCustomerFilterFirst', baseContext);

      numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomers).to.be.above(0);
    });

    it(`should filter list by '${filterBy}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkEdit', baseContext);

      await customersPage.filterCustomers(page, 'input', filterBy, value);

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, filterBy);
      await expect(textResult).to.contains(value);
    });

    it('should delete customers with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCustomers', baseContext);

      const deleteTextResult = await customersPage.deleteCustomersBulkActions(page);
      await expect(deleteTextResult).to.be.equal(customersPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomersAfterReset).to.be.below(numberOfCustomers);
    });
  });
}

module.exports = {
  deleteCustomerTest,
  createCustomerTest,
  createCustomerB2BTest,
  bulkDeleteCustomersTest,
};
