require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {DefaultAccount} = require('@data/demo/customer');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CustomersPage = require('@pages/BO/customers');

let browser;
let page;
let numberOfCustomers = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    customersPage: new CustomersPage(page),
  };
};

// Filter And Quick Edit Customers
describe('Filter And Quick Edit Customers', async () => {
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

  it('should go to Customers page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.customersParentLink,
      this.pageObjects.boBasePage.customersLink,
    );
    const pageTitle = await this.pageObjects.customersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
  });

  it('should reset all filters and get Number of customers in BO', async function () {
    numberOfCustomers = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
    await expect(numberOfCustomers).to.be.above(0);
  });
  // 1 : Filter Customers with all inputs and selects in grid table
  describe('Filter Customers', async () => {
    const tests = [
      {args: {filterType: 'input', filterBy: 'id_customer', filterValue: DefaultAccount.id}},
      {args: {filterType: 'select', filterBy: 'social_title', filterValue: DefaultAccount.socialTitle}},
      {args: {filterType: 'input', filterBy: 'firstname', filterValue: DefaultAccount.firstName}},
      {args: {filterType: 'input', filterBy: 'lastname', filterValue: DefaultAccount.lastName}},
      {args: {filterType: 'input', filterBy: 'email', filterValue: DefaultAccount.email}},
      {args: {filterType: 'select', filterBy: 'active', filterValue: DefaultAccount.enabled}, expected: 'check'},
      {args: {filterType: 'select', filterBy: 'newsletter', filterValue: DefaultAccount.newsletter}, expected: 'check'},
      {args: {filterType: 'select', filterBy: 'optin', filterValue: false}, expected: 'clear'},
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        if (typeof test.args.filterValue === 'boolean') {
          await this.pageObjects.customersPage.filterCustomersSwitch(
            test.args.filterBy,
            test.args.filterValue,
          );
        } else {
          await this.pageObjects.customersPage.filterCustomers(
            test.args.filterType,
            test.args.filterBy,
            test.args.filterValue,
          );
        }
        const numberOfCustomersAfterFilter = await this.pageObjects.customersPage.getNumberOfElementInGrid();
        await expect(numberOfCustomersAfterFilter).to.be.at.most(numberOfCustomers);
        for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
          const textColumn = await this.pageObjects.customersPage.getTextColumnFromTableCustomers(i, test.args.filterBy);
          if (test.expected !== undefined) {
            await expect(textColumn).to.contains(test.expected);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        const numberOfCustomersAfterReset = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
        await expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
      });
    });
  });
  // 2 : Editing customers from grid table
  describe('Quick Edit Customers', async () => {
    it('should filter by Email \'pub@prestashop.com\'', async function () {
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        DefaultAccount.email,
      );
      const numberOfCustomersAfterFilter = await this.pageObjects.customersPage.getNumberOfElementInGrid();
      await expect(numberOfCustomersAfterFilter).to.be.at.above(0);
    });

    const tests = [
      {args: {action: 'disable', column: 'active', value: false}},
      {args: {action: 'enable', column: 'active', value: true}},
      {args: {action: 'enable newsletter', column: 'newsletter', value: true}},
      {args: {action: 'disable newsletter', column: 'newsletter', value: false}},
      {args: {action: 'enable partner offers', column: 'optin', value: true}},
      {args: {action: 'disable partner offers', column: 'optin', value: false}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} for first customer`, async function () {
        const isActionPerformed = await this.pageObjects.customersPage.updateToggleColumnValue(
          1,
          test.args.column,
          test.args.value,
        );
        if (isActionPerformed) {
          const resultMessage = await this.pageObjects.customersPage.getTextContent(
            this.pageObjects.customersPage.alertSuccessBlockParagraph,
          );
          await expect(resultMessage).to.contains(this.pageObjects.customersPage.successfulUpdateStatusMessage);
        }
        const isStatusChanged = await this.pageObjects.customersPage.getToggleColumnValue(1, test.args.column);
        await expect(isStatusChanged).to.be.equal(test.args.value);
      });
    });
  });
});
