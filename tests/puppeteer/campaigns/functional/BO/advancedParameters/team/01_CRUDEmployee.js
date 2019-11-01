require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const EmployeesPage = require('@pages/BO/advancedParameters/team/employees');
const AddEmployeePage = require('@pages/BO/advancedParameters/team/addEmployee');
const ProductsPage = require('@pages/BO/products');
const OrdersPage = require('@pages/BO/orders');
const FOBasePage = require('@pages/FO/FObasePage');
const EmployeeFaker = require('@data/faker/employee');

let browser;
let page;
let numberOfEmployees = 0;
let createEmployeeData;
let firstEditEmployeeData;
let secondEditEmployeeData;

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

// Create, Read, Update and Delete Employee in BO
describe('Create, Read, Update and Delete Employee in BO', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    createEmployeeData = await (new EmployeeFaker({
      defaultPage: 'Products',
      language: 'English (English)',
      permissionProfile: 'Salesman',
    }));
    firstEditEmployeeData = await (new EmployeeFaker({
      password: '123456789',
      defaultPage: 'Orders',
      language: 'English (English)',
      permissionProfile: 'Salesman',
    }));
    secondEditEmployeeData = await (new EmployeeFaker({
      defaultPage: 'Orders',
      language: 'English (English)',
      permissionProfile: 'Salesman',
      active: false,
    }));
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

  // 1 : Create employee and go to FO to check sign in is OK
  describe('Create employee in BO and check Sign in in BO', async () => {
    it('should go to add new employee page', async function () {
      await this.pageObjects.employeesPage.goToAddNewEmployeePage();
      const pageTitle = await this.pageObjects.addEmployeePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addEmployeePage.pageTitleCreate);
    });

    it('should create employee and check result', async function () {
      const textResult = await this.pageObjects.addEmployeePage.createEditEmployee(createEmployeeData);
      await expect(textResult).to.equal(this.pageObjects.employeesPage.successfulCreationMessage);
      const numberOfEmployeesAfterCreation = await this.pageObjects.employeesPage.getNumberFromText(
        this.pageObjects.employeesPage.employeeGridTitle,
      );
      await expect(numberOfEmployeesAfterCreation).to.be.equal(numberOfEmployees + 1);
    });

    // Logout into BO
    loginCommon.logoutBO();

    it('should sign in with new account and verify the default page', async function () {
      await this.pageObjects.loginPage.login(createEmployeeData.email, createEmployeeData.password);
      const pageTitle = await this.pageObjects.productsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
    });

    // Logout into BO
    loginCommon.logoutBO();
  });

  // 2 : Update employee and check that employee can't sign in in BO (enabled = false)
  describe('Update employee Created two times', async () => {
    // Login into BO
    loginCommon.loginBO();
    describe('Update the password and the default page', async () => {
      it('should go to Employees page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.advancedParametersLink,
          this.pageObjects.boBasePage.teamLink,
        );
        await this.pageObjects.boBasePage.closeSfToolBar();
        const pageTitle = await this.pageObjects.employeesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.employeesPage.pageTitle);
      });

      it('should filter list by email', async function () {
        await this.pageObjects.employeesPage.filterEmployees(
          'input',
          'email',
          createEmployeeData.email,
        );
        const textEmail = await this.pageObjects.employeesPage.getTextContent(
          this.pageObjects.employeesPage.employeesListTableColumn.replace('%ROW', '1').replace('%COLUMN', 'email'),
        );
        await expect(textEmail).to.contains(createEmployeeData.email);
      });

      it('should go to edit employee page', async function () {
        await this.pageObjects.employeesPage.goToEditEmployeePage('1');
        const pageTitle = await this.pageObjects.addEmployeePage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addEmployeePage.pageTitleEdit);
      });

      it('should update the employee account', async function () {
        const textResult = await this.pageObjects.addEmployeePage.createEditEmployee(firstEditEmployeeData);
        await expect(textResult).to.equal(this.pageObjects.addEmployeePage.successfulUpdateMessage);
      });

      it('should click on cancel and verify the new employees number', async function () {
        await this.pageObjects.addEmployeePage.cancelPage();
        const numberOfEmployeesAfterUpdate = await this.pageObjects.employeesPage.resetAndGetNumberOfLines();
        await expect(numberOfEmployeesAfterUpdate).to.be.equal(numberOfEmployees + 1);
      });

      // Logout into BO
      loginCommon.logoutBO();

      it('should sign in with edited account and verify the default page', async function () {
        await this.pageObjects.loginPage.login(firstEditEmployeeData.email, firstEditEmployeeData.password);
        const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
      });

      // Logout into BO
      loginCommon.logoutBO();
    });
    describe('Deactivate the Employee and check it', async () => {
      // Login into BO
      loginCommon.loginBO();

      it('should go to Employees page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.advancedParametersLink,
          this.pageObjects.boBasePage.teamLink,
        );
        await this.pageObjects.boBasePage.closeSfToolBar();
        const pageTitle = await this.pageObjects.employeesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.employeesPage.pageTitle);
      });

      it('should filter list by email', async function () {
        await this.pageObjects.employeesPage.filterEmployees(
          'input',
          'email',
          firstEditEmployeeData.email,
        );
        const textEmail = await this.pageObjects.employeesPage.getTextContent(
          this.pageObjects.employeesPage.employeesListTableColumn.replace('%ROW', '1').replace('%COLUMN', 'email'),
        );
        await expect(textEmail).to.contains(firstEditEmployeeData.email);
      });

      it('should go to edit employee page', async function () {
        await this.pageObjects.employeesPage.goToEditEmployeePage('1');
        const pageTitle = await this.pageObjects.addEmployeePage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addEmployeePage.pageTitleEdit);
      });

      it('should deactivate the employee account', async function () {
        const textResult = await this.pageObjects.addEmployeePage.createEditEmployee(secondEditEmployeeData);
        await expect(textResult).to.equal(this.pageObjects.addEmployeePage.successfulUpdateMessage);
      });

      it('should click on cancel', async function () {
        await this.pageObjects.addEmployeePage.cancelPage();
        const pageTitle = await this.pageObjects.employeesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.employeesPage.pageTitle);
      });

      // Logout into BO
      loginCommon.logoutBO();

      it('should test sign in with the disabled Employee', async function () {
        await this.pageObjects.loginPage.login(secondEditEmployeeData.email, secondEditEmployeeData.password);
        const pageTitle = await this.pageObjects.loginPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.loginPage.pageTitle);
      });
    });
  });

  // 5 : Delete employee from BO
  describe('Delete employee', async () => {
    // Login into BO
    loginCommon.loginBO();

    it('should go to Employees page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.advancedParametersLink,
        this.pageObjects.boBasePage.teamLink,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.employeesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.employeesPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await this.pageObjects.employeesPage.filterEmployees(
        'input',
        'email',
        secondEditEmployeeData.email,
      );
      const textEmail = await this.pageObjects.employeesPage.getTextContent(
        this.pageObjects.employeesPage.employeesListTableColumn.replace('%ROW', '1').replace('%COLUMN', 'email'),
      );
      await expect(textEmail).to.contains(secondEditEmployeeData.email);
    });

    it('should delete employee', async function () {
      const textResult = await this.pageObjects.employeesPage.deleteEmployee('1');
      await expect(textResult).to.equal(this.pageObjects.employeesPage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of employees', async function () {
      const numberOfEmployeesAfterDelete = await this.pageObjects.employeesPage.resetAndGetNumberOfLines();
      await expect(numberOfEmployeesAfterDelete).to.be.equal(numberOfEmployees);
    });
  });
});
