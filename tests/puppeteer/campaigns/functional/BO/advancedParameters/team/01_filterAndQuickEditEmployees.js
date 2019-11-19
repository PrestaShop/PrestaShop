require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const EmployeeFaker = require('@data/faker/employee');
const {DefaultAccount} = require('@data/demo/employees');
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
const createEmployeeData = new EmployeeFaker({
  defaultPage: 'Orders',
  permissionProfile: 'Logistician',
  active: false,
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

// Filter And Quick Edit Employees in BO
describe('Filter And Quick Edit Employees', async () => {
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
    const tests = [
      {args: {filterType: 'input', filterBy: 'id_employee', filterValue: 1}},
      {args: {filterType: 'input', filterBy: 'firstname', filterValue: createEmployeeData.firstName}},
      {args: {filterType: 'input', filterBy: 'lastname', filterValue: DefaultAccount.lastName}},
      {args: {filterType: 'input', filterBy: 'email', filterValue: createEmployeeData.email}},
      {args: {filterType: 'select', filterBy: 'active', filterValue: createEmployeeData.active}, expected: 'clear'},
    ];
    it('should go to add new employee page', async function () {
      await this.pageObjects.employeesPage.goToAddNewEmployeePage();
      const pageTitle = await this.pageObjects.addEmployeePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addEmployeePage.pageTitleCreate);
    });

    it('should create employee', async function () {
      const textResult = await this.pageObjects.addEmployeePage.createEditEmployee(createEmployeeData);
      await expect(textResult).to.equal(this.pageObjects.employeesPage.successfulCreationMessage);
      const numberOfEmployeesAfterCreation = await this.pageObjects.employeesPage.getNumberOfElementInGrid();
      await expect(numberOfEmployeesAfterCreation).to.be.equal(numberOfEmployees + 1);
    });

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await this.pageObjects.employeesPage.filterEmployees(
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );
        const numberOfEmployeesAfterFilter = await this.pageObjects.employeesPage.getNumberOfElementInGrid();
        await expect(numberOfEmployeesAfterFilter).to.be.at.most(numberOfEmployees);
        for (let i = 1; i <= numberOfEmployeesAfterFilter; i++) {
          const textColumn = await this.pageObjects.employeesPage.getTextColumnFromTable(i, test.args.filterBy);
          if (test.expected !== undefined) {
            await expect(textColumn).to.contains(test.expected);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        const numberOfEmployeesAfterCreation = await this.pageObjects.employeesPage.resetAndGetNumberOfLines();
        await expect(numberOfEmployeesAfterCreation).to.be.above(numberOfEmployees);
      });
    });

    // 2 : Editing Employees from grid table
    describe('Quick Edit Employees', async () => {
      it('should filter by Email address', async function () {
        await this.pageObjects.employeesPage.filterEmployees('input', 'email', createEmployeeData.email);
        const numberOfEmployeesAfterFilter = await this.pageObjects.employeesPage.getNumberOfElementInGrid();
        await expect(numberOfEmployeesAfterFilter).to.be.at.most(numberOfEmployees);
        for (let i = 1; i <= numberOfEmployeesAfterFilter; i++) {
          const textColumn = await this.pageObjects.employeesPage.getTextColumnFromTable(i, 'email');
          await expect(textColumn).to.contains(createEmployeeData.email);
        }
      });

      const statuses = [
        {args: {status: 'disable', enable: false}},
        {args: {status: 'enable', enable: true}},
      ];
      statuses.forEach((employeeStatus) => {
        it(`should ${employeeStatus.args.status} the employee`, async function () {
          const isActionPerformed = await this.pageObjects.employeesPage.updateToggleColumnValue(
            1,
            employeeStatus.args.enable,
          );
          if (isActionPerformed) {
            const resultMessage = await this.pageObjects.employeesPage.getTextContent(
              this.pageObjects.employeesPage.alertSuccessBlockParagraph,
            );
            await expect(resultMessage).to.contains(this.pageObjects.employeesPage.successfulUpdateStatusMessage);
          }
          const isStatusChanged = await this.pageObjects.employeesPage.getToggleColumnValue(1);
          await expect(isStatusChanged).to.be.equal(employeeStatus.args.enable);
        });
      });
    });

    // 3 : Delete employee
    describe('Delete employee', async () => {
      it('should filter list by email', async function () {
        await this.pageObjects.employeesPage.filterEmployees(
          'input',
          'email',
          createEmployeeData.email,
        );
        const textEmail = await this.pageObjects.employeesPage.getTextColumnFromTable(1, 'email');
        await expect(textEmail).to.contains(createEmployeeData.email);
      });

      it('should delete employee', async function () {
        const textResult = await this.pageObjects.employeesPage.deleteEmployee(1);
        await expect(textResult).to.equal(this.pageObjects.employeesPage.successfulDeleteMessage);
      });

      it('should reset filter and check the number of employees', async function () {
        const numberOfEmployeesAfterDelete = await this.pageObjects.employeesPage.resetAndGetNumberOfLines();
        await expect(numberOfEmployeesAfterDelete).to.be.equal(numberOfEmployees);
      });
    });
  });
});
