require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CustomersPage = require('@pages/BO/customers');
const AddCustomerPage = require('@pages/BO/customers/add');

// Import data
const CustomerFaker = require('@data/faker/customer');
// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_customers_customersBulkActionsInBO';

let browser;
let page;
let numberOfCustomers = 0;

const firstCustomerData = new CustomerFaker({firstName: 'todelete'});
const secondCustomerData = new CustomerFaker({firstName: 'todelete'});

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    customersPage: new CustomersPage(page),
    addCustomerPage: new AddCustomerPage(page),
  };
};

// Create Customers, Then disable / Enable and Delete with Bulk actions
describe('Create Customers, Then disable / Enable and Delete with Bulk actions', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to customers page
  loginCommon.loginBO();

  it('should go to customers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.customersParentLink,
      this.pageObjects.dashboardPage.customersLink,
    );

    await this.pageObjects.customersPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.customersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCustomers = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
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
        await this.pageObjects.customersPage.goToAddNewCustomerPage();
        const pageTitle = await this.pageObjects.addCustomerPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addCustomerPage.pageTitleCreate);
      });

      it('should create customer and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCustomer${index + 1}`, baseContext);

        const textResult = await this.pageObjects.addCustomerPage.createEditCustomer(test.args.customerToCreate);
        await expect(textResult).to.equal(this.pageObjects.customersPage.successfulCreationMessage);

        const numberOfCustomersAfterCreation = await this.pageObjects.customersPage.getNumberOfElementInGrid();
        await expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + index + 1);
      });
    });
  });
  // 2 : Enable/Disable customers created with bulk actions
  describe('Enable and Disable customers with Bulk Actions', async () => {
    it('should filter list by firstName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkEdit', baseContext);

      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'firstname',
        'todelete',
      );

      const textResult = await this.pageObjects.customersPage.getTextColumnFromTableCustomers(1, 'firstname');
      await expect(textResult).to.contains('todelete');
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}, expected: 'clear'},
      {args: {action: 'enable', enabledValue: true}, expected: 'check'},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} customers with bulk actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Customers`, baseContext);

        const textResult = await this.pageObjects.customersPage.changeCustomersEnabledColumnBulkActions(
          test.args.enabledValue,
        );

        await expect(textResult).to.be.equal(this.pageObjects.customersPage.successfulUpdateMessage);

        const numberOfCustomersInGrid = await this.pageObjects.customersPage.getNumberOfElementInGrid();
        await expect(numberOfCustomersInGrid).to.be.at.least(2);

        for (let i = 1; i <= numberOfCustomersInGrid; i++) {
          const textColumn = await this.pageObjects.customersPage.getTextColumnFromTableCustomers(1, 'active');
          await expect(textColumn).to.contains(test.expected);
        }
      });
    });
  });

  // 3 : Delete Customers created with bulk actions
  describe('Delete customers with Bulk Actions', async () => {
    it('should filter list by firstName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'firstname',
        'todelete',
      );

      const textResult = await this.pageObjects.customersPage.getTextColumnFromTableCustomers(1, 'firstname');
      await expect(textResult).to.contains('todelete');
    });

    it('should delete customers with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCustomers', baseContext);

      const deleteTextResult = await this.pageObjects.customersPage.deleteCustomersBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.customersPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfCustomersAfterReset = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
      await expect(numberOfCustomersAfterReset).to.be.equal(numberOfCustomers);
    });
  });
});
