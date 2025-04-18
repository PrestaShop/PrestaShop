import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boEmployeesPage,
  boEmployeesCreatePage,
  boLoginPage,
  type BrowserContext,
  dataEmployees,
  FakerEmployee,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_team_employees_employeesBulkActions';

// Create Employees, Then disable / Enable and Delete with Bulk actions
describe('BO - Advanced Parameters - Team : Create/Disable/Enable and bulk delete Employees', async () => {
  const firstEmployeeData: FakerEmployee = new FakerEmployee({
    firstName: 'todelete',
    defaultPage: 'Orders',
    permissionProfile: 'Logistician',
  });
  const secondEmployeeData: FakerEmployee = new FakerEmployee({
    firstName: 'todelete',
    defaultPage: 'Orders',
    permissionProfile: 'Logistician',
  });

  let browserContext: BrowserContext;
  let page: Page;
  let numberOfEmployees: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Advanced Parameters > Team\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAdvancedParamsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.teamLink,
    );
    await boEmployeesPage.closeSfToolBar(page);

    const pageTitle = await boEmployeesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boEmployeesPage.pageTitle);
  });

  it('should reset all filters and get number of employees', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfEmployees = await boEmployeesPage.resetAndGetNumberOfLines(page);
    expect(numberOfEmployees).to.be.above(0);
  });

  // 1 : Try to Enable/Disable the default employee with bulk actions
  describe('Case 1 : Try to enable/disable the default employee with Bulk Actions', async () => {
    it('should filter by First name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterDefaultEmployee', baseContext);

      await boEmployeesPage.filterEmployees(page, 'input', 'firstname', dataEmployees.defaultEmployee.firstName);

      const textColumn = await boEmployeesPage.getTextColumnFromTable(page, 1, 'firstname');
      expect(textColumn).to.contains(dataEmployees.defaultEmployee.firstName);
    });

    const statuses = [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ];
    statuses.forEach((employeeStatus) => {
      it(`should try to ${employeeStatus.args.status} default employee and check the error message`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${employeeStatus.args.status}DefaultEmployee`, baseContext);

        const disableTextResult = await boEmployeesPage.bulkSetStatus(page, employeeStatus.args.enable, false);
        expect(disableTextResult).to.be.equal(boEmployeesPage.errorDeleteOwnAccountMessage);
      });
    });
  });

  // 2 : Delete employee with bulk actions
  describe('Case 1 : Try to delete default employee with Bulk Actions', async () => {
    it('should try to delete default employee and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteDefaultEmployee', baseContext);

      const deleteTextResult = await boEmployeesPage.deleteBulkActions(page, false);
      expect(deleteTextResult).to.be.equal(boEmployeesPage.errorDeleteOwnAccountMessage);
    });
  });

  // 3 : Create employees and Filter with all inputs and selects in grid table in BO
  describe('Case 2 : Create 2 employees then filter the table', async () => {
    const employeesToCreate: FakerEmployee[] = [firstEmployeeData, secondEmployeeData];

    employeesToCreate.forEach((employeeToCreate: FakerEmployee, index: number) => {
      it('should go to add new employee page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewEmployeePage${index + 1}`, baseContext);

        await boEmployeesPage.goToAddNewEmployeePage(page);

        const pageTitle = await boEmployeesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boEmployeesCreatePage.pageTitleCreate);
      });

      it('should create employee', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createEmployee${index + 1}`, baseContext);

        const textResult = await boEmployeesCreatePage.createEditEmployee(page, employeeToCreate);
        expect(textResult).to.equal(boEmployeesPage.successfulCreationMessage);
      });
    });
  });

  // 4 : Enable/Disable employees created with bulk actions
  describe('Case 2 : Enable/Disable the created employees with Bulk Actions', async () => {
    it('should filter by First name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkEditStatus', baseContext);

      await boEmployeesPage.filterEmployees(page, 'input', 'firstname', firstEmployeeData.firstName);

      const numberOfEmployeesAfterFilter = await boEmployeesPage.getNumberOfElementInGrid(page);
      expect(numberOfEmployeesAfterFilter).to.be.at.most(numberOfEmployees + 2);

      for (let i = 1; i <= numberOfEmployeesAfterFilter; i++) {
        const textColumn = await boEmployeesPage.getTextColumnFromTable(page, i, 'firstname');
        expect(textColumn).to.contains(firstEmployeeData.firstName);
      }
    });

    const statuses = [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ];

    statuses.forEach((employeeStatus) => {
      it(`should ${employeeStatus.args.status} employees with Bulk Actions and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${employeeStatus.args.status}Employee`, baseContext);

        const disableTextResult = await boEmployeesPage.bulkSetStatus(
          page,
          employeeStatus.args.enable,
        );
        expect(disableTextResult).to.be.equal(boEmployeesPage.successfulUpdateStatusMessage);

        const numberOfEmployeesInGrid = await boEmployeesPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfEmployeesInGrid; i++) {
          const textColumn = await boEmployeesPage.getStatus(page, i);
          expect(textColumn).to.equal(employeeStatus.args.enable);
        }
      });
    });
  });

  // 5 : Delete employee with bulk actions
  describe('Case 2 : Delete employees with Bulk Actions', async () => {
    it('should delete employees with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteEmployee', baseContext);

      const deleteTextResult = await boEmployeesPage.deleteBulkActions(page);
      expect(deleteTextResult).to.be.equal(boEmployeesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfEmployeesAfterDelete = await boEmployeesPage.resetAndGetNumberOfLines(page);
      expect(numberOfEmployeesAfterDelete).to.be.equal(numberOfEmployees);
    });
  });
});
