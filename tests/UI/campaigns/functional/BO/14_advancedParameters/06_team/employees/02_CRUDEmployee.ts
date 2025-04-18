import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boEmployeesPage,
  boEmployeesCreatePage,
  boLoginPage,
  boOrdersPage,
  boProductsPage,
  type BrowserContext,
  FakerEmployee,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_team_employees_CRUDEmployee';

// Create, Read, Update and Delete Employee in BO
describe('BO - Advanced Parameters - Team : CRUD Employee', async () => {
  const createEmployeeData: FakerEmployee = new FakerEmployee({
    defaultPage: 'Products',
    language: 'English (English)',
    permissionProfile: 'Salesman',
  });
  const firstEditEmployeeData: FakerEmployee = new FakerEmployee({
    defaultPage: 'Orders',
    language: 'English (English)',
    permissionProfile: 'Salesman',
  });
  const secondEditEmployeeData: FakerEmployee = new FakerEmployee({
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

  // 1 : Create employee and go to BO to check sign in is OK
  describe('Create employee and check Sign in', async () => {
    it('should go to add new employee page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewEmployeePage', baseContext);

      await boEmployeesPage.goToAddNewEmployeePage(page);

      const pageTitle = await boEmployeesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boEmployeesCreatePage.pageTitleCreate);
    });

    it('should create employee and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createEmployee', baseContext);

      const textResult = await boEmployeesCreatePage.createEditEmployee(page, createEmployeeData);
      expect(textResult).to.equal(boEmployeesPage.successfulCreationMessage);

      const numberOfEmployeesAfterCreation = await boEmployeesPage.getNumberOfElementInGrid(page);
      expect(numberOfEmployeesAfterCreation).to.be.equal(numberOfEmployees + 1);
    });

    it('should logout from BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logoutBO', baseContext);

      await boDashboardPage.logoutBO(page);

      const pageTitle = await boLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLoginPage.pageTitle);
    });

    it('should sign in with new account and verify the default page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInWithCreatedEmployee', baseContext);

      await boLoginPage.successLogin(page, createEmployeeData.email, createEmployeeData.password);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should logout from BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logoutBO', baseContext);

      await boDashboardPage.logoutBO(page);

      const pageTitle = await boLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLoginPage.pageTitle);
    });
  });

  // 2 : Update employee and check that employee can't sign in in BO (enabled = false)
  describe('Update employee', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    describe('Update the password and the default page', async () => {
      it('should go to \'Advanced Parameters > Team\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEmployeePageForUpdate', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.advancedParametersLink,
          boDashboardPage.teamLink,
        );

        const pageTitle = await boEmployeesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boEmployeesPage.pageTitle);
      });

      it('should filter list by email', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

        await boEmployeesPage.filterEmployees(page, 'input', 'email', createEmployeeData.email);

        const textEmail = await boEmployeesPage.getTextColumnFromTable(page, 1, 'email');
        expect(textEmail).to.contains(createEmployeeData.email);
      });

      it('should go to edit employee page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditEmployeePage', baseContext);

        await boEmployeesPage.goToEditEmployeePage(page, 1);

        const pageTitle = await boEmployeesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boEmployeesCreatePage.pageTitleEdit(
          createEmployeeData.lastName, createEmployeeData.firstName));
      });

      it('should update the employee account', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateEmployee', baseContext);

        const textResult = await boEmployeesCreatePage.createEditEmployee(page, firstEditEmployeeData);
        expect(textResult).to.equal(boEmployeesCreatePage.successfulUpdateMessage);
      });

      it('should click on cancel and verify the new employee\'s number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'verifyNumberOfEmployeeAfterUpdate', baseContext);

        await boEmployeesCreatePage.cancel(page);

        const numberOfEmployeesAfterUpdate = await boEmployeesPage.resetAndGetNumberOfLines(page);
        expect(numberOfEmployeesAfterUpdate).to.be.equal(numberOfEmployees + 1);
      });

      it('should logout from BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'logoutBO', baseContext);

        await boDashboardPage.logoutBO(page);

        const pageTitle = await boLoginPage.getPageTitle(page);
        expect(pageTitle).to.contains(boLoginPage.pageTitle);
      });

      it('should sign in with edited account and verify the default page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signInWithUpdatedEmployee', baseContext);

        await boLoginPage.successLogin(page, firstEditEmployeeData.email, firstEditEmployeeData.password);

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should logout from BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'logoutBO', baseContext);

        await boDashboardPage.logoutBO(page);

        const pageTitle = await boLoginPage.getPageTitle(page);
        expect(pageTitle).to.contains(boLoginPage.pageTitle);
      });
    });

    describe('Disable the employee and check it', async () => {
      it('should login in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

        await boLoginPage.goTo(page, global.BO.URL);
        await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to \'Advanced Parameters > Team\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEmployeesPageToDisable', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.advancedParametersLink,
          boDashboardPage.teamLink,
        );

        const pageTitle = await boEmployeesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boEmployeesPage.pageTitle);
      });

      it('should filter list by email', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterEmployeesToDisable', baseContext);

        await boEmployeesPage.filterEmployees(page, 'input', 'email', firstEditEmployeeData.email);

        const textEmail = await boEmployeesPage.getTextColumnFromTable(page, 1, 'email');
        expect(textEmail).to.contains(firstEditEmployeeData.email);
      });

      it('should go to edit employee page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditEmployeePageToDisable', baseContext);

        await boEmployeesPage.goToEditEmployeePage(page, 1);

        const pageTitle = await boEmployeesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boEmployeesCreatePage.pageTitleEdit(
          firstEditEmployeeData.lastName, firstEditEmployeeData.firstName));
      });

      it('should disable the employee account', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableEmployee', baseContext);

        const textResult = await boEmployeesCreatePage.createEditEmployee(page, secondEditEmployeeData);
        expect(textResult).to.equal(boEmployeesCreatePage.successfulUpdateMessage);
      });

      it('should logout from BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'logoutBO', baseContext);

        await boDashboardPage.logoutBO(page);

        const pageTitle = await boLoginPage.getPageTitle(page);
        expect(pageTitle).to.contains(boLoginPage.pageTitle);
      });

      it('should test sign in with the disabled employee', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signInWithDisabledEmployee', baseContext);

        await boLoginPage.failedLogin(page, secondEditEmployeeData.email, secondEditEmployeeData.password);

        const loginError = await boLoginPage.getLoginError(page);
        expect(loginError).to.contains(boLoginPage.loginErrorText);
      });
    });
  });

  // 3 : Delete employee
  describe('Delete employee', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > Team\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmployeesPageToDelete', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.teamLink,
      );

      const pageTitle = await boEmployeesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boEmployeesPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterEmployeesToDelete', baseContext);

      await boEmployeesPage.filterEmployees(page, 'input', 'email', secondEditEmployeeData.email);

      const textEmail = await boEmployeesPage.getTextColumnFromTable(page, 1, 'email');
      expect(textEmail).to.contains(secondEditEmployeeData.email);
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
