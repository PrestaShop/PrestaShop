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

  // 1 : Create employees and Filter with all inputs and selects in grid table in BO
  describe('Create employees then filter the table', async () => {
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

        const numberOfEmployeesAfterCreation = await employeesPage.getNumberOfElementInGrid(page);
        await expect(numberOfEmployeesAfterCreation).to.be.equal(numberOfEmployees + index + 1);
      });
    });
  });

  // 2 : Enable/Disable employees created with bulk actions
  describe('Enable and Disable employees with Bulk Actions', async () => {
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

    // 3 : Delete employee with bulk actions
    describe('Delete employees with Bulk Actions', async () => {
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
});
