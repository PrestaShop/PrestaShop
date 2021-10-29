require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const customersPage = require('@pages/BO/customers');
const addCustomerPage = require('@pages/BO/customers/add');

// Import data
const CustomerFaker = require('@data/faker/customer');

const baseContext = 'functional_BO_customers_customers_customersBulkActions';

let browserContext;
let page;
let numberOfCustomers = 0;

const firstCustomerData = new CustomerFaker({firstName: 'todelete'});
const secondCustomerData = new CustomerFaker({firstName: 'todelete'});

/*
Create Customer
Enable/Disable/Delete by Bulk actions
*/
describe('BO - Customers - Customers : Customers bulk actions', async () => {
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

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.customersParentLink,
      dashboardPage.customersLink,
    );

    await customersPage.closeSfToolBar(page);

    const pageTitle = await customersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(customersPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCustomers).to.be.above(0);
  });

  // 1 : Create 2 customers In BO
  describe('Create 2 customers in BO', async () => {
    [
      {args: {customerToCreate: firstCustomerData}},
      {args: {customerToCreate: secondCustomerData}},
    ].forEach((test, index) => {
      it('should go to add new customer page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCustomerPage${index + 1}`, baseContext);

        await customersPage.goToAddNewCustomerPage(page);
        const pageTitle = await addCustomerPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
      });

      it(`should create customer n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCustomer${index + 1}`, baseContext);

        const textResult = await addCustomerPage.createEditCustomer(page, test.args.customerToCreate);
        await expect(textResult).to.equal(customersPage.successfulCreationMessage);

        const numberOfCustomersAfterCreation = await customersPage.getNumberOfElementInGrid(page);
        await expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + index + 1);
      });
    });
  });

  // 2 : Enable/Disable customers by bulk actions
  describe('Enable/Disable customers by bulk actions', async () => {
    it('should filter list by firstName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkEdit', baseContext);

      await customersPage.filterCustomers(page, 'input', 'firstname', 'todelete');

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, 'firstname');
      await expect(textResult).to.contains('todelete');
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} customers with bulk actions and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Customers`, baseContext);

        const textResult = await customersPage.bulkSetStatus(page, test.args.enabledValue);
        await expect(textResult).to.be.equal(customersPage.successfulUpdateMessage);

        const numberOfCustomersInGrid = await customersPage.getNumberOfElementInGrid(page);
        await expect(numberOfCustomersInGrid).to.be.at.least(2);

        for (let i = 1; i <= numberOfCustomersInGrid; i++) {
          const customerStatus = await customersPage.getCustomerStatus(page, i);
          await expect(customerStatus).to.equals(test.args.enabledValue);
        }
      });
    });
  });

  // 3 : Delete Customers created with bulk actions
  describe('Delete customers by bulk actions', async () => {
    it('should filter list by firstName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await customersPage.filterCustomers(page, 'input', 'firstname', 'todelete');

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, 'firstname');
      await expect(textResult).to.contains('todelete');
    });

    it('should delete customers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCustomers', baseContext);

      const deleteTextResult = await customersPage.deleteCustomersBulkActions(page);
      await expect(deleteTextResult).to.be.equal(customersPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomersAfterReset).to.be.equal(numberOfCustomers);
    });
  });
});
