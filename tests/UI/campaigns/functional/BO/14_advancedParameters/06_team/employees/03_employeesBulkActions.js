require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const EmployeeFaker = require('@data/faker/employee');

// Import pages
const dashboardPage = require('@pages/BO/dashboard/index');
const employeesPage = require('@pages/BO/advancedParameters/team/index');
const addEmployeePage = require('@pages/BO/advancedParameters/team/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParams_team_employees_employeesBulkActions';

let browserContext;
let page;
let numberOfEmployees = 0;

const firstEmployeeData = new EmployeeFaker(
  {
    firstName: 'todelete',
    defaultPage: 'Orders',
    permissionProfile: 'Logistician',
  },
);
const secondEmployeeData = new EmployeeFaker(
  {
    firstName: 'todelete',
    defaultPage: 'Orders',
    permissionProfile: 'Logistician',
  },
);

// Create Employees, Then disable / Enable and Delete with Bulk actions
describe('Create Employees, Then disable / Enable and Delete with Bulk actions', async () => {
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

  // 1 : Create employees and Filter with all inputs and selects in grid table in BO
  describe('Create employees then filter the table', async () => {
    const employeesToCreate = [firstEmployeeData, secondEmployeeData];

    employeesToCreate.forEach((employeeToCreate, index) => {
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
      it(`should ${employeeStatus.args.status} employees with Bulk Actions and check Result`, async function () {
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
      it('should delete employees with Bulk Actions and check Result', async function () {
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
