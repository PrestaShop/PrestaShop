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

const baseContext = 'functional_BO_advancedParams_team_employees_sortAndPagination';

let browserContext;
let page;
let numberOfEmployees = 0;

const employeeData = new EmployeeFaker();

/*
Create 20 employees
Sort employee list
Pagination
Delete created employees
 */
describe('Sort and pagination employees', async () => {
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

  it('should go to "Advanced parameters > Team" page', async function () {
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

  // 1 : Create 20 employees
  const tests = new Array(10).fill(0, 0, 10);

  tests.forEach((test, index) => {
    const employeeToCreate = new EmployeeFaker({email: `${employeeData.email}${index}`});
    describe(`Create employee n°${index + 1} in BO`, async () => {
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

  // 2 : Sort employees list
  const sortTests = [
    {
      args: {
        testIdentifier: 'sortByIDDesc', sortBy: 'id_employee', sortDirection: 'desc', isFloat: true,
      },
    },
    {args: {testIdentifier: 'sortByFirstNameDesc', sortBy: 'firstname', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByFirstNameAsc', sortBy: 'firstname', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByLastNameDesc', sortBy: 'lastname', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByLastNameAsc', sortBy: 'lastname', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByEmailDesc', sortBy: 'email', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByEmailAsc', sortBy: 'email', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByProfileDesc', sortBy: 'profile', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByProfileAsc', sortBy: 'profile', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByActiveDesc', sortBy: 'active', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByActiveAsc', sortBy: 'active', sortDirection: 'asc'}},
    {
      args: {
        testIdentifier: 'sortByIDAsc', sortBy: 'id_employee', sortDirection: 'asc', isFloat: true,
      },
    },
  ];
  describe('Sort employee', async () => {
    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await employeesPage.getAllRowsColumnContent(page, test.args.sortBy);
        await employeesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await employeesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await employeesPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 3 : Test pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await employeesPage.selectPaginationLimit(page, '10');
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await employeesPage.paginationNext(page);
      expect(paginationNumber).to.contain('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await employeesPage.paginationPrevious(page);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await employeesPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contain('(page 1 / 1)');
    });
  });

  // 4 : Delete employee with bulk actions
  describe('Delete employees with Bulk Actions', async () => {
    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await employeesPage.filterEmployees(
        page,
        'input',
        'email',
        employeeData.email,
      );

      const textEmail = await employeesPage.getTextColumnFromTable(page, 1, 'email');
      await expect(textEmail).to.contains(employeeData.email);
    });

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
