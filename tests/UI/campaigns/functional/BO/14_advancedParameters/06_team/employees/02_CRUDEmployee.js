require('module-alias/register');

const {expect} = require('chai');

// Import Utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const EmployeeFaker = require('@data/faker/employee');

// Import pages
const loginPage = require('@pages/BO/login/index');
const dashboardPage = require('@pages/BO/dashboard/index');
const employeesPage = require('@pages/BO/advancedParameters/team/index');
const addEmployeePage = require('@pages/BO/advancedParameters/team/add');
const productsPage = require('@pages/BO/catalog/products/index');
const ordersPage = require('@pages/BO/orders/index');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParams_team_employees_CRUDEmployee';

let browserContext;
let page;
let numberOfEmployees = 0;

const createEmployeeData = new EmployeeFaker({
  defaultPage: 'Products',
  language: 'English (English)',
  permissionProfile: 'Salesman',
});

const firstEditEmployeeData = new EmployeeFaker({
  password: '123456789',
  defaultPage: 'Orders',
  language: 'English (English)',
  permissionProfile: 'Salesman',
});

const secondEditEmployeeData = new EmployeeFaker({
  defaultPage: 'Orders',
  language: 'English (English)',
  permissionProfile: 'Salesman',
  active: false,
});

// Create, Read, Update and Delete Employee in BO
describe('Create, Read, Update and Delete Employee in BO', async () => {
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

  it('should go to "Advanced parameters>Team" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAdvancedParamsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.teamLink,
    );

    await employeesPage.closeSfToolBar(page);

    const pageTitle = await employeesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(employeesPage.pageTitle);
  });

  it('should reset all filters and get number of employees', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfEmployees = await employeesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfEmployees).to.be.above(0);
  });

  // 1 : Create employee and go to FO to check sign in is OK
  describe('Create employee in BO and check Sign in in BO', async () => {
    it('should go to add new employee page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewEmployeePage', baseContext);

      await employeesPage.goToAddNewEmployeePage(page);
      const pageTitle = await addEmployeePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addEmployeePage.pageTitleCreate);
    });

    it('should create employee and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createEmployee', baseContext);

      const textResult = await addEmployeePage.createEditEmployee(page, createEmployeeData);
      await expect(textResult).to.equal(employeesPage.successfulCreationMessage);

      const numberOfEmployeesAfterCreation = await employeesPage.getNumberOfElementInGrid(page);
      await expect(numberOfEmployeesAfterCreation).to.be.equal(numberOfEmployees + 1);
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });

    it('should sign in with new account and verify the default page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInWithCreatedEmployee', baseContext);

      await loginPage.login(page, createEmployeeData.email, createEmployeeData.password);
      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });
  });

  // 2 : Update employee and check that employee can't sign in in BO (enabled = false)
  describe('Update the employee created', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    describe('Update the password and the default page', async () => {
      it('should go to Employees page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEmployeePageForUpdate', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.advancedParametersLink,
          dashboardPage.teamLink,
        );

        const pageTitle = await employeesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(employeesPage.pageTitle);
      });

      it('should filter list by email', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

        await employeesPage.filterEmployees(
          page,
          'input',
          'email',
          createEmployeeData.email,
        );

        const textEmail = await employeesPage.getTextColumnFromTable(page, 1, 'email');
        await expect(textEmail).to.contains(createEmployeeData.email);
      });

      it('should go to edit employee page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditEmployeePage', baseContext);

        await employeesPage.goToEditEmployeePage(page, 1);
        const pageTitle = await addEmployeePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addEmployeePage.pageTitleEdit);
      });

      it('should update the employee account', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateEmployee', baseContext);

        const textResult = await addEmployeePage.createEditEmployee(page, firstEditEmployeeData);
        await expect(textResult).to.equal(addEmployeePage.successfulUpdateMessage);
      });

      it('should click on cancel and verify the new employee\'s number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'verifyNumberOfEmployeeAfterUpdate', baseContext);

        await addEmployeePage.cancel(page);

        const numberOfEmployeesAfterUpdate = await employeesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfEmployeesAfterUpdate).to.be.equal(numberOfEmployees + 1);
      });

      it('should logout from BO', async function () {
        await loginCommon.logoutBO(this, page);
      });

      it('should sign in with edited account and verify the default page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signInWithUpdatedEmployee', baseContext);

        await loginPage.login(page, firstEditEmployeeData.email, firstEditEmployeeData.password);
        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should logout from BO', async function () {
        await loginCommon.logoutBO(this, page);
      });
    });
    describe('Disable the employee and check it', async () => {
      it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to Employees page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEmployeesPageToDisable', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.advancedParametersLink,
          dashboardPage.teamLink,
        );

        const pageTitle = await employeesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(employeesPage.pageTitle);
      });

      it('should filter list by email', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterEmployeesToDisable', baseContext);

        await employeesPage.filterEmployees(
          page,
          'input',
          'email',
          firstEditEmployeeData.email,
        );

        const textEmail = await employeesPage.getTextColumnFromTable(page, 1, 'email');
        await expect(textEmail).to.contains(firstEditEmployeeData.email);
      });

      it('should go to edit employee page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditEmployeePageToDisable', baseContext);

        await employeesPage.goToEditEmployeePage(page, 1);
        const pageTitle = await addEmployeePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addEmployeePage.pageTitleEdit);
      });

      it('should disable the employee account', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableEmployee', baseContext);

        const textResult = await addEmployeePage.createEditEmployee(page, secondEditEmployeeData);
        await expect(textResult).to.equal(addEmployeePage.successfulUpdateMessage);
      });

      it('should logout from BO', async function () {
        await loginCommon.logoutBO(this, page);
      });

      it('should test sign in with the disabled employee', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signInWithDisabledEmployee', baseContext);

        await loginPage.login(page, secondEditEmployeeData.email, secondEditEmployeeData.password, false);
        const loginError = await loginPage.getLoginError(page);
        await expect(loginError).to.contains(loginPage.loginErrorText);
      });
    });
  });

  // 5 : Delete employee
  describe('Delete employee', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to Employees page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmployeesPageToDelete', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.teamLink,
      );

      const pageTitle = await employeesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(employeesPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterEmployeesToDelete', baseContext);

      await employeesPage.filterEmployees(
        page,
        'input',
        'email',
        secondEditEmployeeData.email,
      );

      const textEmail = await employeesPage.getTextColumnFromTable(page, 1, 'email');
      await expect(textEmail).to.contains(secondEditEmployeeData.email);
    });

    it('should delete employee', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteEmployee', baseContext);

      const textResult = await employeesPage.deleteEmployee(page, 1);
      await expect(textResult).to.equal(employeesPage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of employees', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfEmployeesAfterDelete = await employeesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfEmployeesAfterDelete).to.be.equal(numberOfEmployees);
    });
  });
});
