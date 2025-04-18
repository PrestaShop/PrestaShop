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

const baseContext: string = 'functional_BO_advancedParameters_team_employees_filterAndQuickEditEmployees';

// Filter and quick edit Employees in BO
describe('BO - Advanced Parameters - Team : Filter and quick edit Employees', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let numberOfEmployees: number = 0;

  const createEmployeeData: FakerEmployee = new FakerEmployee({
    defaultPage: 'Orders',
    permissionProfile: 'Logistician',
    active: false,
  });

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

  // PRE-TEST : Create employee and Filter with all inputs and selects in grid table in BO
  describe('PRE-TEST : Create employee', async () => {
    it('should go to add new employee page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewEmployeePage', baseContext);

      await boEmployeesPage.goToAddNewEmployeePage(page);

      const pageTitle = await boEmployeesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boEmployeesCreatePage.pageTitleCreate);
    });

    it('should create employee', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createEmployee', baseContext);

      const textResult = await boEmployeesCreatePage.createEditEmployee(page, createEmployeeData);
      expect(textResult).to.equal(boEmployeesPage.successfulCreationMessage);

      const numberOfEmployeesAfterCreation = await boEmployeesPage.getNumberOfElementInGrid(page);
      expect(numberOfEmployeesAfterCreation).to.be.equal(numberOfEmployees + 1);
    });
  });

  // 1 : Filter with all inputs and selects in grid table in BO
  describe('Filter the table', async () => {
    [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_employee',
            filterValue: '1',
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
            filterValue: dataEmployees.defaultEmployee.lastName,
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
            filterValue: createEmployeeData.active ? '1' : '0',
          },
      },
    ].forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        await boEmployeesPage.filterEmployees(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfEmployeesAfterFilter = await boEmployeesPage.getNumberOfElementInGrid(page);
        expect(numberOfEmployeesAfterFilter).to.be.at.most(numberOfEmployees);

        for (let i = 1; i <= numberOfEmployeesAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const employeeStatus = await boEmployeesPage.getStatus(page, i);
            expect(employeeStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await boEmployeesPage.getTextColumnFromTable(page, i, test.args.filterBy);
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfEmployeesAfterCreation = await boEmployeesPage.resetAndGetNumberOfLines(page);
        expect(numberOfEmployeesAfterCreation).to.be.above(numberOfEmployees);
      });
    });
  });

  // 3 : Quick edit Employees
  describe('Quick edit Employee', async () => {
    it('should filter by Email address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForQuickEdit', baseContext);

      await boEmployeesPage.filterEmployees(page, 'input', 'email', createEmployeeData.email);

      const numberOfEmployeesAfterFilter = await boEmployeesPage.getNumberOfElementInGrid(page);
      expect(numberOfEmployeesAfterFilter).to.be.at.most(numberOfEmployees);

      for (let i = 1; i <= numberOfEmployeesAfterFilter; i++) {
        const textColumn = await boEmployeesPage.getTextColumnFromTable(page, i, 'email');
        expect(textColumn).to.contains(createEmployeeData.email);
      }
    });

    [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ].forEach((employeeStatus) => {
      it(`should ${employeeStatus.args.status} the employee from the table`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${employeeStatus.args.status}Employee`,
          baseContext,
        );

        const isActionPerformed = await boEmployeesPage.setStatus(
          page,
          1,
          employeeStatus.args.enable,
        );

        if (isActionPerformed) {
          const resultMessage = await boEmployeesPage.getAlertSuccessBlockParagraphContent(page);
          expect(resultMessage).to.contains(boEmployeesPage.successfulUpdateStatusMessage);
        }

        const currentStatus = await boEmployeesPage.getStatus(page, 1);
        expect(currentStatus).to.be.equal(employeeStatus.args.enable);
      });
    });
  });

  // POST-TEST : Delete employee
  describe('POST-TEST : Delete employee', async () => {
    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await boEmployeesPage.filterEmployees(page, 'input', 'email', createEmployeeData.email);

      const textEmail = await boEmployeesPage.getTextColumnFromTable(page, 1, 'email');
      expect(textEmail).to.contains(createEmployeeData.email);
    });

    it('should delete employee', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteEmployee', baseContext);

      const textResult = await boEmployeesPage.deleteEmployee(page, 1);
      expect(textResult).to.equal(boEmployeesPage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of employees', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfEmployeesAfterDelete = await boEmployeesPage.resetAndGetNumberOfLines(page);
      expect(numberOfEmployeesAfterDelete).to.be.equal(numberOfEmployees);
    });
  });
});
