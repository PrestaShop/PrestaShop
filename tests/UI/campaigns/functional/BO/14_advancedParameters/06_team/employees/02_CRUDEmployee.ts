// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import loginPage from '@pages/BO/login';
import dashboardPage from '@pages/BO/dashboard';
import employeesPage from '@pages/BO/advancedParameters/team';
import addEmployeePage from '@pages/BO/advancedParameters/team/add';
import productsPage from '@pages/BO/catalog/products';
import ordersPage from '@pages/BO/orders';

// Import data
import EmployeeData from '@data/faker/employee';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_team_employees_CRUDEmployee';

// Create, Read, Update and Delete Employee in BO
describe('BO - Advanced Parameters - Team : CRUD Employee', async () => {
  const createEmployeeData: EmployeeData = new EmployeeData({
    defaultPage: 'Products',
    language: 'English (English)',
    permissionProfile: 'Salesman',
  });
  const firstEditEmployeeData: EmployeeData = new EmployeeData({
    defaultPage: 'Orders',
    language: 'English (English)',
    permissionProfile: 'Salesman',
  });
  const secondEditEmployeeData: EmployeeData = new EmployeeData({
    defaultPage: 'Orders',
    language: 'English (English)',
    permissionProfile: 'Salesman',
    active: false,
  });

  let browserContext: BrowserContext;
  let page: Page;
  let numberOfEmployees: number = 0;

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

  it('should go to \'Advanced Parameters > Team\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAdvancedParamsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.teamLink,
    );
    await employeesPage.closeSfToolBar(page);

    const pageTitle = await employeesPage.getPageTitle(page);
    expect(pageTitle).to.contains(employeesPage.pageTitle);
  });

  it('should reset all filters and get number of employees', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfEmployees = await employeesPage.resetAndGetNumberOfLines(page);
    expect(numberOfEmployees).to.be.above(0);
  });

  // 1 : Create employee and go to BO to check sign in is OK
  describe('Create employee and check Sign in', async () => {
    it('should go to add new employee page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewEmployeePage', baseContext);

      await employeesPage.goToAddNewEmployeePage(page);

      const pageTitle = await addEmployeePage.getPageTitle(page);
      expect(pageTitle).to.contains(addEmployeePage.pageTitleCreate);
    });

    it('should create employee and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createEmployee', baseContext);

      const textResult = await addEmployeePage.createEditEmployee(page, createEmployeeData);
      expect(textResult).to.equal(employeesPage.successfulCreationMessage);

      const numberOfEmployeesAfterCreation = await employeesPage.getNumberOfElementInGrid(page);
      expect(numberOfEmployeesAfterCreation).to.be.equal(numberOfEmployees + 1);
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });

    it('should sign in with new account and verify the default page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInWithCreatedEmployee', baseContext);

      await loginPage.successLogin(page, createEmployeeData.email, createEmployeeData.password);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });
  });

  // 2 : Update employee and check that employee can't sign in in BO (enabled = false)
  describe('Update employee', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    describe('Update the password and the default page', async () => {
      it('should go to \'Advanced Parameters > Team\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEmployeePageForUpdate', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.advancedParametersLink,
          dashboardPage.teamLink,
        );

        const pageTitle = await employeesPage.getPageTitle(page);
        expect(pageTitle).to.contains(employeesPage.pageTitle);
      });

      it('should filter list by email', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

        await employeesPage.filterEmployees(page, 'input', 'email', createEmployeeData.email);

        const textEmail = await employeesPage.getTextColumnFromTable(page, 1, 'email');
        expect(textEmail).to.contains(createEmployeeData.email);
      });

      it('should go to edit employee page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditEmployeePage', baseContext);

        await employeesPage.goToEditEmployeePage(page, 1);

        const pageTitle = await addEmployeePage.getPageTitle(page);
        expect(pageTitle).to.contains(addEmployeePage.pageTitleEdit(
          createEmployeeData.lastName, createEmployeeData.firstName));
      });

      it('should update the employee account', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateEmployee', baseContext);

        const textResult = await addEmployeePage.createEditEmployee(page, firstEditEmployeeData);
        expect(textResult).to.equal(addEmployeePage.successfulUpdateMessage);
      });

      it('should click on cancel and verify the new employee\'s number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'verifyNumberOfEmployeeAfterUpdate', baseContext);

        await addEmployeePage.cancel(page);

        const numberOfEmployeesAfterUpdate = await employeesPage.resetAndGetNumberOfLines(page);
        expect(numberOfEmployeesAfterUpdate).to.be.equal(numberOfEmployees + 1);
      });

      it('should logout from BO', async function () {
        await loginCommon.logoutBO(this, page);
      });

      it('should sign in with edited account and verify the default page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signInWithUpdatedEmployee', baseContext);

        await loginPage.successLogin(page, firstEditEmployeeData.email, firstEditEmployeeData.password);

        const pageTitle = await ordersPage.getPageTitle(page);
        expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should logout from BO', async function () {
        await loginCommon.logoutBO(this, page);
      });
    });

    describe('Disable the employee and check it', async () => {
      it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to \'Advanced Parameters > Team\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEmployeesPageToDisable', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.advancedParametersLink,
          dashboardPage.teamLink,
        );

        const pageTitle = await employeesPage.getPageTitle(page);
        expect(pageTitle).to.contains(employeesPage.pageTitle);
      });

      it('should filter list by email', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterEmployeesToDisable', baseContext);

        await employeesPage.filterEmployees(page, 'input', 'email', firstEditEmployeeData.email);

        const textEmail = await employeesPage.getTextColumnFromTable(page, 1, 'email');
        expect(textEmail).to.contains(firstEditEmployeeData.email);
      });

      it('should go to edit employee page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditEmployeePageToDisable', baseContext);

        await employeesPage.goToEditEmployeePage(page, 1);

        const pageTitle = await addEmployeePage.getPageTitle(page);
        expect(pageTitle).to.contains(addEmployeePage.pageTitleEdit(
          firstEditEmployeeData.lastName, firstEditEmployeeData.firstName));
      });

      it('should disable the employee account', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableEmployee', baseContext);

        const textResult = await addEmployeePage.createEditEmployee(page, secondEditEmployeeData);
        expect(textResult).to.equal(addEmployeePage.successfulUpdateMessage);
      });

      it('should logout from BO', async function () {
        await loginCommon.logoutBO(this, page);
      });

      it('should test sign in with the disabled employee', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signInWithDisabledEmployee', baseContext);

        await loginPage.failedLogin(page, secondEditEmployeeData.email, secondEditEmployeeData.password);

        const loginError = await loginPage.getLoginError(page);
        expect(loginError).to.contains(loginPage.loginErrorText);
      });
    });
  });

  // 3 : Delete employee
  describe('Delete employee', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > Team\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmployeesPageToDelete', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.teamLink,
      );

      const pageTitle = await employeesPage.getPageTitle(page);
      expect(pageTitle).to.contains(employeesPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterEmployeesToDelete', baseContext);

      await employeesPage.filterEmployees(page, 'input', 'email', secondEditEmployeeData.email);

      const textEmail = await employeesPage.getTextColumnFromTable(page, 1, 'email');
      expect(textEmail).to.contains(secondEditEmployeeData.email);
    });

    it('should delete employee', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteEmployee', baseContext);

      const textResult = await employeesPage.deleteEmployee(page, 1);
      expect(textResult).to.equal(employeesPage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of employees', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfEmployeesAfterDelete = await employeesPage.resetAndGetNumberOfLines(page);
      expect(numberOfEmployeesAfterDelete).to.be.equal(numberOfEmployees);
    });
  });
});
