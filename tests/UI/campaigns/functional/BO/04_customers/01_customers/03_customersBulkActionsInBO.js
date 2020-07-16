require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const customersPage = require('@pages/BO/customers');
const addCustomerPage = require('@pages/BO/customers/add');

// Import data
const CustomerFaker = require('@data/faker/customer');
// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_customers_customersBulkActionsInBO';


let browserContext;
let page;
let numberOfCustomers = 0;

const firstCustomerData = new CustomerFaker({firstName: 'todelete'});
const secondCustomerData = new CustomerFaker({firstName: 'todelete'});

// Create Customers, Then disable / Enable and Delete with Bulk actions
describe('Create Customers, Then disable / Enable and Delete with Bulk actions', async () => {
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

  it('should go to customers page', async function () {
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
  describe('Create  2 customers in BO', async () => {
    const tests = [
      {args: {customerToCreate: firstCustomerData}},
      {args: {customerToCreate: secondCustomerData}},
    ];

    tests.forEach((test, index) => {
      it('should go to add new customer page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCustomerPage${index + 1}`, baseContext);

        await customersPage.goToAddNewCustomerPage(page);
        const pageTitle = await addCustomerPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
      });

      it('should create customer and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCustomer${index + 1}`, baseContext);

        const textResult = await addCustomerPage.createEditCustomer(page, test.args.customerToCreate);
        await expect(textResult).to.equal(customersPage.successfulCreationMessage);

        const numberOfCustomersAfterCreation = await customersPage.getNumberOfElementInGrid(page);
        await expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + index + 1);
      });
    });
  });
  // 2 : Enable/Disable customers created with bulk actions
  describe('Enable and Disable customers with Bulk Actions', async () => {
    it('should filter list by firstName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkEdit', baseContext);

      await customersPage.filterCustomers(
        page,
        'input',
        'firstname',
        'todelete',
      );

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, 'firstname');
      await expect(textResult).to.contains('todelete');
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}, expected: 'clear'},
      {args: {action: 'enable', enabledValue: true}, expected: 'check'},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} customers with bulk actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Customers`, baseContext);

        const textResult = await customersPage.changeCustomersEnabledColumnBulkActions(
          page,
          test.args.enabledValue,
        );

        await expect(textResult).to.be.equal(customersPage.successfulUpdateMessage);

        const numberOfCustomersInGrid = await customersPage.getNumberOfElementInGrid(page);
        await expect(numberOfCustomersInGrid).to.be.at.least(2);

        for (let i = 1; i <= numberOfCustomersInGrid; i++) {
          const textColumn = await customersPage.getTextColumnFromTableCustomers(page, 1, 'active');
          await expect(textColumn).to.contains(test.expected);
        }
      });
    });
  });

  // 3 : Delete Customers created with bulk actions
  describe('Delete customers with Bulk Actions', async () => {
    it('should filter list by firstName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await customersPage.filterCustomers(
        page,
        'input',
        'firstname',
        'todelete',
      );

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, 'firstname');
      await expect(textResult).to.contains('todelete');
    });

    it('should delete customers with Bulk Actions and check Result', async function () {
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
