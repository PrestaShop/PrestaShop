// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import employeesPage from '@pages/BO/advancedParameters/team';
import addEmployeePage from '@pages/BO/advancedParameters/team/add';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  type FakerEmployee,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

let browserContext: BrowserContext;
let page: Page;
let numberOfEmployees: number = 0;

/**
 * Function to create employee
 * @param employeeData {EmployeeData} Data to set in employee form
 * @param baseContext {string} String to identify the test
 */
function createEmployeeTest(employeeData: FakerEmployee, baseContext: string = 'commonTests-createEmployeeTest'): void {
  describe('PRE-TEST: Create employee', async () => {
    // before and after functions
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > Team\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTeamPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.teamLink,
      );

      const pageTitle = await employeesPage.getPageTitle(page);
      expect(pageTitle).to.contains(employeesPage.pageTitle);
    });

    it('should reset all filters and get number of employees', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterEmployeeTable', baseContext);

      numberOfEmployees = await employeesPage.resetAndGetNumberOfLines(page);
      expect(numberOfEmployees).to.be.above(0);
    });

    it('should go to add new employee page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewEmployeePage', baseContext);

      await employeesPage.goToAddNewEmployeePage(page);

      const pageTitle = await addEmployeePage.getPageTitle(page);
      expect(pageTitle).to.contains(addEmployeePage.pageTitleCreate);
    });

    it('should create employee and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createEmployee', baseContext);

      const textResult = await addEmployeePage.createEditEmployee(page, employeeData);
      expect(textResult).to.equal(employeesPage.successfulCreationMessage);
    });
  });
}

/**
 * Function to delete employee
 * @param employeeData {EmployeeData} Data to set to delete employee
 * @param baseContext {string} String to identify the test
 */
function deleteEmployeeTest(employeeData: FakerEmployee, baseContext: string = 'commonTests-deleteEmployeeTest'): void {
  describe('POST-TEST: Delete employee', async () => {
    // before and after functions
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > Team\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmployeesPageToDelete', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.teamLink,
      );

      const pageTitle = await employeesPage.getPageTitle(page);
      expect(pageTitle).to.contains(employeesPage.pageTitle);
    });

    it('should filter list of employees by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterEmployeesToDelete', baseContext);

      await employeesPage.filterEmployees(page, 'input', 'email', employeeData.email);

      const textEmail = await employeesPage.getTextColumnFromTable(page, 1, 'email');
      expect(textEmail).to.contains(employeeData.email);
    });

    it('should delete employee', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteEmployee', baseContext);

      const textResult = await employeesPage.deleteEmployee(page, 1);
      expect(textResult).to.equal(employeesPage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of employees', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteEmployee', baseContext);

      const numberOfEmployeesAfterDelete = await employeesPage.resetAndGetNumberOfLines(page);
      expect(numberOfEmployeesAfterDelete).to.be.equal(numberOfEmployees);
    });
  });
}

export {createEmployeeTest, deleteEmployeeTest};
