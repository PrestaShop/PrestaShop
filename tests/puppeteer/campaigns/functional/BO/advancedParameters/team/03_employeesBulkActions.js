require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const EmployeeFaker = require('@data/faker/employee');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login/index');
const DashboardPage = require('@pages/BO/dashboard/index');
const EmployeesPage = require('@pages/BO/advancedParameters/team/index');
const AddEmployeePage = require('@pages/BO/advancedParameters/team/add');
const ProductsPage = require('@pages/BO/catalog/products/index');
const OrdersPage = require('@pages/BO/orders/index');
const FOBasePage = require('@pages/FO/FObasePage');

let browser;
let page;
let numberOfEmployees = 0;
const firstEmployeeData = new EmployeeFaker({
  firstName: 'todelete',
  defaultPage: 'Orders',
  permissionProfile: 'Logistician',
});
const secondEmployeeData = new EmployeeFaker({
  firstName: 'todelete',
  defaultPage: 'Orders',
  permissionProfile: 'Logistician',
});

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    employeesPage: new EmployeesPage(page),
    addEmployeePage: new AddEmployeePage(page),
    productsPage: new ProductsPage(page),
    ordersPage: new OrdersPage(page),
    foBasePage: new FOBasePage(page),
  };
};

// Create Employees, Then disable / Enable and Delete with Bulk actions
describe('Create Employees, Then disable / Enable and Delete with Bulk actions', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to "Advanced parameters>Team" page
  loginCommon.loginBO();

  it('should go to "Advanced parameters>Team" page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.advancedParametersLink,
      this.pageObjects.boBasePage.teamLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.employeesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.employeesPage.pageTitle);
  });

  it('should reset all filters and get number of employees', async function () {
    numberOfEmployees = await this.pageObjects.employeesPage.resetAndGetNumberOfLines();
    await expect(numberOfEmployees).to.be.above(0);
  });

  // 1 : Create employee and Filter with all inputs and selects in grid table in BO
  describe('Create employee then filter the table', async () => {
    const employeesToCreate = [firstEmployeeData, secondEmployeeData];
    employeesToCreate.forEach((employeeToCreate) => {
      it('should go to add new employee page', async function () {
        await this.pageObjects.employeesPage.goToAddNewEmployeePage();
        const pageTitle = await this.pageObjects.addEmployeePage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addEmployeePage.pageTitleCreate);
      });

      it('should create employee', async function () {
        const textResult = await this.pageObjects.addEmployeePage.createEditEmployee(employeeToCreate);
        await expect(textResult).to.equal(this.pageObjects.employeesPage.successfulCreationMessage);
      });
    });
  });

  // 2 : Enable/Disable employees created with bulk actions
  describe.skip('Enable and Disable employees with Bulk Actions', async () => {
    it('should filter by First name', async function () {
      await this.pageObjects.employeesPage.filterEmployees('input', 'firstname', firstEmployeeData.firstName);
      const numberOfEmployeesAfterFilter = await this.pageObjects.employeesPage.getNumberFromText(
        this.pageObjects.employeesPage.employeeGridTitle,
      );
      await expect(numberOfEmployeesAfterFilter).to.be.at.most(numberOfEmployees);
      for (let i = 1; i <= numberOfEmployeesAfterFilter; i++) {
        const textColumn = await this.pageObjects.employeesPage.getTextColumnFromTable(i, 'firstname');
        await expect(textColumn).to.contains(firstEmployeeData.firstName);
      }
    });
    const statuses = [
      {args: {status: 'disable', enable: false}, expected: 'clear'},
      {args: {status: 'enable', enable: true}, expected: 'check'},
    ];
    statuses.forEach((employeeStatus) => {
      it(`should ${employeeStatus.args.status} employees with Bulk Actions and check Result`, async function () {
        const disableTextResult = await this.pageObjects.employeesPage.changeEnabledColumnBulkActions(
          employeeStatus.args.enable,
        );
        await expect(disableTextResult).to.be.equal(this.pageObjects.employeesPage.successfulUpdateStatusMessage);
        const numberOfEmployeesInGrid = await this.pageObjects.employeesPage.getNumberFromText(
          this.pageObjects.employeesPage.employeeGridTitle,
        );
        for (let i = 1; i <= numberOfEmployeesInGrid; i++) {
          const textColumn = await this.pageObjects.employeesPage.getTextColumnFromTable(i, 'active');
          await expect(textColumn).to.contains(employeeStatus.expected);
        }
      });
    });

    // 3 : Delete employee with bulk actions
    describe.skip('Delete employees with Bulk Actions', async () => {
      it('should delete employees with Bulk Actions and check Result', async function () {
        const deleteTextResult = await this.pageObjects.employeesPage.deleteBulkActions();
        await expect(deleteTextResult).to.be.equal(this.pageObjects.employeesPage.successfulMultiDeleteMessage);
      });

      it('should reset all filters', async function () {
        const numberOfEmployeesAfterDelete = await this.pageObjects.employeesPage.resetAndGetNumberOfLines();
        await expect(numberOfEmployeesAfterDelete).to.be.equal(numberOfEmployees);
      });
    });
  });
});
