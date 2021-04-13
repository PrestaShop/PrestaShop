require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const EmployeeFaker = require('@data/faker/employee');
const {DefaultEmployee} = require('@data/demo/employees');

// Import pages
const dashboardPage = require('@pages/BO/dashboard/index');
const employeesPage = require('@pages/BO/advancedParameters/team/index');
const addEmployeePage = require('@pages/BO/advancedParameters/team/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParams_team_employees_filterAndQuickEditEmployees';

let browserContext;
let page;

let numberOfEmployees = 0;

const createEmployeeData = new EmployeeFaker({
  defaultPage: 'Orders',
  permissionProfile: 'Logistician',
  active: false,
});

// Filter And Quick Edit Employees in BO
describe('Filter And Quick Edit Employees', async () => {
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

  // 1 : Create employee and Filter with all inputs and selects in grid table in BO
  describe('Create employee then filter the table', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_employee',
            filterValue: 1,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterFirstName',
            filterType: 'input',
            filterBy: 'firstname',
            filterValue: createEmployeeData.firstName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterLastName',
            filterType: 'input',
            filterBy: 'lastname',
            filterValue: DefaultEmployee.lastName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterEmail',
            filterType: 'input',
            filterBy: 'email',
            filterValue: createEmployeeData.email,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: createEmployeeData.active,
          },
      },
    ];
    it('should go to add new employee page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewEmployeePage', baseContext);

      await employeesPage.goToAddNewEmployeePage(page);
      const pageTitle = await addEmployeePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addEmployeePage.pageTitleCreate);
    });

    it('should create employee', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createEmployee', baseContext);

      const textResult = await addEmployeePage.createEditEmployee(page, createEmployeeData);
      await expect(textResult).to.equal(employeesPage.successfulCreationMessage);

      const numberOfEmployeesAfterCreation = await employeesPage.getNumberOfElementInGrid(page);
      await expect(numberOfEmployeesAfterCreation).to.be.equal(numberOfEmployees + 1);
    });

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        await employeesPage.filterEmployees(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfEmployeesAfterFilter = await employeesPage.getNumberOfElementInGrid(page);
        await expect(numberOfEmployeesAfterFilter).to.be.at.most(numberOfEmployees);

        for (let i = 1; i <= numberOfEmployeesAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const employeeStatus = await employeesPage.getStatus(page, i);
            await expect(employeeStatus).to.equal(test.args.filterValue);
          } else {
            const textColumn = await employeesPage.getTextColumnFromTable(page, i, test.args.filterBy);
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfEmployeesAfterCreation = await employeesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfEmployeesAfterCreation).to.be.above(numberOfEmployees);
      });
    });

    // 2 : Editing Employees from grid table
    describe('Quick Edit Employees', async () => {
      it('should filter by Email address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterForQuickEdit', baseContext);

        await employeesPage.filterEmployees(page, 'input', 'email', createEmployeeData.email);

        const numberOfEmployeesAfterFilter = await employeesPage.getNumberOfElementInGrid(page);
        await expect(numberOfEmployeesAfterFilter).to.be.at.most(numberOfEmployees);

        for (let i = 1; i <= numberOfEmployeesAfterFilter; i++) {
          const textColumn = await employeesPage.getTextColumnFromTable(page, i, 'email');
          await expect(textColumn).to.contains(createEmployeeData.email);
        }
      });

      const statuses = [
        {args: {status: 'disable', enable: false}},
        {args: {status: 'enable', enable: true}},
      ];

      statuses.forEach((employeeStatus) => {
        it(`should ${employeeStatus.args.status} the employee`, async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `${employeeStatus.args.status}Employee`,
            baseContext,
          );

          const isActionPerformed = await employeesPage.setStatus(
            page,
            1,
            employeeStatus.args.enable,
          );

          if (isActionPerformed) {
            const resultMessage = await employeesPage.getAlertSuccessBlockParagraphContent(page);
            await expect(resultMessage).to.contains(employeesPage.successfulUpdateStatusMessage);
          }

          const currentStatus = await employeesPage.getStatus(page, 1);
          await expect(currentStatus).to.be.equal(employeeStatus.args.enable);
        });
      });
    });

    // 3 : Delete employee
    describe('Delete employee', async () => {
      it('should filter list by email', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

        await employeesPage.filterEmployees(
          page,
          'input',
          'email',
          createEmployeeData.email,
        );

        const textEmail = await employeesPage.getTextColumnFromTable(page, 1, 'email');
        await expect(textEmail).to.contains(createEmployeeData.email);
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
});
