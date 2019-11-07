require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CustomersPage = require('@pages/BO/customers');
const AddCustomerPage = require('@pages/BO/customers/add');
const CustomerFaker = require('@data/faker/customer');

let browser;
let page;
let numberOfCustomers = 0;
let firstCustomerData;
let secondCustomerData;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
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
    firstCustomerData = await (new CustomerFaker({firstName: 'todelete'}));
    secondCustomerData = await (new CustomerFaker({firstName: 'todelete'}));
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to customers page
  loginCommon.loginBO();

  it('should go to customers page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.customersParentLink,
      this.pageObjects.boBasePage.customersLink,
    );
    const pageTitle = await this.pageObjects.customersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
  });

  it('should reset all filters', async function () {
    numberOfCustomers = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
    await expect(numberOfCustomers).to.be.above(0);
  });
  // 1 : Create 2 customers In BO
  describe('Create  2 customers in BO', async () => {
    it('should go to add new customer page', async function () {
      await this.pageObjects.customersPage.goToAddNewCustomerPage();
      const pageTitle = await this.pageObjects.addCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCustomerPage.pageTitleCreate);
    });

    it('should create first customer and check result', async function () {
      const textResult = await this.pageObjects.addCustomerPage.createEditCustomer(firstCustomerData);
      await expect(textResult).to.equal(this.pageObjects.customersPage.successfulCreationMessage);
      const numberOfCustomersAfterCreation = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + 1);
    });

    it('should go to add new customer page', async function () {
      await this.pageObjects.customersPage.goToAddNewCustomerPage();
      const pageTitle = await this.pageObjects.addCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCustomerPage.pageTitleCreate);
    });

    it('should create second customer and check result', async function () {
      const textResult = await this.pageObjects.addCustomerPage.createEditCustomer(secondCustomerData);
      await expect(textResult).to.equal(this.pageObjects.customersPage.successfulCreationMessage);
      const numberOfCustomersAfterCreation = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + 2);
    });
  });
  // 2 : Enable/Disable customers created with bulk actions
  describe('Enable and Disable customers with Bulk Actions', async () => {
    it('should filter list by firstName', async function () {
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'firstname',
        'todelete',
      );
      const textResult = await this.pageObjects.customersPage.getTextContent(
        this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', '1').replace('%COLUMN', 'firstname'),
      );
      await expect(textResult).to.contains('todelete');
    });

    it('should disable customers with Bulk Actions and check Result', async function () {
      const disableTextResult = await this.pageObjects.customersPage.changeCustomersEnabledColumnBulkActions(false);
      await expect(disableTextResult).to.be.equal(this.pageObjects.customersPage.successfulUpdateMessage);
      const numberOfCustomersInGrid = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersInGrid).to.be.at.most(numberOfCustomers);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCustomersInGrid; i++) {
        const textColumn = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', i).replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('clear');
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should enable customers with Bulk Actions and check Result', async function () {
      const enableTextResult = await this.pageObjects.customersPage.changeCustomersEnabledColumnBulkActions(true);
      await expect(enableTextResult).to.be.equal(this.pageObjects.customersPage.successfulUpdateMessage);
      const numberOfCustomersInGrid = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersInGrid).to.be.at.most(numberOfCustomers);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCustomersInGrid; i++) {
        const textColumn = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', i).replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('check');
      }
      /* eslint-enable no-await-in-loop */
    });
  });
  // 3 : Delete Customers created with bulk actions
  describe('Delete customers with Bulk Actions', async () => {
    it('should filter list by firstName', async function () {
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'firstname',
        'todelete',
      );
      const textResult = await this.pageObjects.customersPage.getTextContent(
        this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', '1').replace('%COLUMN', 'firstname'),
      );
      await expect(textResult).to.contains('todelete');
    });

    it('should delete customers with Bulk Actions and check Result', async function () {
      const deleteTextResult = await this.pageObjects.customersPage.deleteCustomersBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.customersPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      const numberOfCustomersAfterReset = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
      await expect(numberOfCustomersAfterReset).to.be.equal(numberOfCustomers);
    });
  });
});
