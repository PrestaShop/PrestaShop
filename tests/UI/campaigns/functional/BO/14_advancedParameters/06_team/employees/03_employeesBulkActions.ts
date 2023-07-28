// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import employeesPage from '@pages/BO/advancedParameters/team';
import addEmployeePage from '@pages/BO/advancedParameters/team/add';

// Import data
import EmployeeData from '@data/faker/employee';
import Employees from '@data/demo/employees';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_team_employees_employeesBulkActions';

// Create Employees, Then disable / Enable and Delete with Bulk actions
describe('BO - Advanced Parameters - Team : Create/Disable/Enable and bulk delete Employees', async () => {
  const firstEmployeeData: EmployeeData = new EmployeeData({
    firstName: 'todelete',
    defaultPage: 'Orders',
    permissionProfile: 'Logistician',
  });
  const secondEmployeeData: EmployeeData = new EmployeeData({
    firstName: 'todelete',
    defaultPage: 'Orders',
    permissionProfile: 'Logistician',
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
    await expect(pageTitle).to.contains(employeesPage.pageTitle);
  });

  it('should reset all filters and get number of employees', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfEmployees = await employeesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfEmployees).to.be.above(0);
  });

  // 1 : Try to Enable/Disable the default employee with bulk actions
  describe('Case 1 : Try to enable/disable the default employee with Bulk Actions', async () => {
    it('should filter by First name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterDefaultEmployee', baseContext);

      await employeesPage.filterEmployees(page, 'input', 'firstname', Employees.DefaultEmployee.firstName);

      const textColumn = await employeesPage.getTextColumnFromTable(page, 1, 'firstname');
      await expect(textColumn).to.contains(Employees.DefaultEmployee.firstName);
    });

    const statuses = [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ];
    statuses.forEach((employeeStatus) => {
      it(`should try to ${employeeStatus.args.status} default employee and check the error message`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${employeeStatus.args.status}DefaultEmployee`, baseContext);

        const disableTextResult = await employeesPage.bulkSetStatus(page, employeeStatus.args.enable, false);
        await expect(disableTextResult).to.be.equal(employeesPage.errorDeleteOwnAccountMessage);
      });
    });
  });

  // 2 : Delete employee with bulk actions
  describe('Case 1 : Try to delete default employee with Bulk Actions', async () => {
    it('should try to delete default employee and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteDefaultEmployee', baseContext);

      const deleteTextResult = await employeesPage.deleteBulkActions(page, false);
      await expect(deleteTextResult).to.be.equal(employeesPage.errorDeleteOwnAccountMessage);
    });
  });

  // 3 : Create employees and Filter with all inputs and selects in grid table in BO
  describe('Case 2 : Create 2 employees then filter the table', async () => {
    const employeesToCreate: EmployeeData[] = [firstEmployeeData, secondEmployeeData];

    employeesToCreate.forEach((employeeToCreate: EmployeeData, index: number) => {
      it('should go to add new employee page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewEmployeePage${index + 1}`, baseContext);

        await employeesPage.goToAddNewEmployeePage(page);

        const pageTitle = await addEmployeePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addEmployeePage.pageTitleCreate);
      });

      it('should create employee', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createEmployee${index + 1}`, baseContext);

        const textResult = await addEmployeePage.createEditEmployee(page, employeeToCreate);
        await expect(textResult).to.equal(employeesPage.successfulCreationMessage);
      });
    });
  });

  // 4 : Enable/Disable employees created with bulk actions
  describe('Case 2 : Enable/Disable the created employees with Bulk Actions', async () => {
    it('should filter by First name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkEditStatus', baseContext);

      await employeesPage.filterEmployees(page, 'input', 'firstname', firstEmployeeData.firstName);

      const numberOfEmployeesAfterFilter = await employeesPage.getNumberOfElementInGrid(page);
      await expect(numberOfEmployeesAfterFilter).to.be.at.most(numberOfEmployees + 2);

      for (let i = 1; i <= numberOfEmployeesAfterFilter; i++) {
        const textColumn = await employeesPage.getTextColumnFromTable(page, i, 'firstname');
        await expect(textColumn).to.contains(firstEmployeeData.firstName);
      }
    });

    const statuses = [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ];

    statuses.forEach((employeeStatus) => {
      it(`should ${employeeStatus.args.status} employees with Bulk Actions and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${employeeStatus.args.status}Employee`, baseContext);

        const disableTextResult = await employeesPage.bulkSetStatus(
          page,
          employeeStatus.args.enable,
        );
        await expect(disableTextResult).to.be.equal(employeesPage.successfulUpdateStatusMessage);

        const numberOfEmployeesInGrid = await employeesPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfEmployeesInGrid; i++) {
          const textColumn = await employeesPage.getStatus(page, i);
          await expect(textColumn).to.equal(employeeStatus.args.enable);
        }
      });
    });
  });

  // 5 : Delete employee with bulk actions
  describe('Case 2 : Delete employees with Bulk Actions', async () => {
    it('should delete employees with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteEmployee', baseContext);

      const deleteTextResult = await employeesPage.deleteBulkActions(page);
      await expect(deleteTextResult).to.be.equal(employeesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfEmployeesAfterDelete = await employeesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfEmployeesAfterDelete).to.be.equal(numberOfEmployees);
    });
  });
});
