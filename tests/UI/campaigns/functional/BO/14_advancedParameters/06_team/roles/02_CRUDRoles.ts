import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boEmployeesPage,
  boLoginPage,
  boRolesPage,
  boRolesCreatePage,
  type BrowserContext,
  FakerEmployeeRole,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_team_roles_CRUDRoles';

// Create, Read, Update and Delete role in BO
describe('BO - Advanced Parameters - Team : Create, Read, Update and Delete role in BO', async () => {
  const createRoleData: FakerEmployeeRole = new FakerEmployeeRole();
  const editRoleData: FakerEmployeeRole = new FakerEmployeeRole();

  let browserContext: BrowserContext;
  let page: Page;

  let numberOfRoles: number = 0;

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

  it('should go to \'Roles\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToRolesPage', baseContext);

    await boEmployeesPage.goToRolesPage(page);

    const pageTitle = await boRolesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boRolesPage.pageTitle);
  });

  it('should reset all filters and get number of roles', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfRoles = await boRolesPage.resetAndGetNumberOfLines(page);
    expect(numberOfRoles).to.be.above(0);
  });

  // 1 : Create role
  describe('Create role in BO', async () => {
    it('should go to add new role page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewRolePage', baseContext);

      await boRolesPage.goToAddNewRolePage(page);

      const pageTitle = await boRolesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boRolesCreatePage.pageTitleCreate);
    });

    it('should create role and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createRole', baseContext);

      const textResult = await boRolesCreatePage.createEditRole(page, createRoleData);
      expect(textResult).to.equal(boRolesPage.successfulCreationMessage);

      const numberOfRolesAfterCreation = await boRolesPage.getNumberOfElementInGrid(page);
      expect(numberOfRolesAfterCreation).to.be.equal(numberOfRoles + 1);
    });
  });

  // 2 : Update role
  describe('Update role', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await boRolesPage.filterRoles(page, 'input', 'name', createRoleData.name);

      const textName = await boRolesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textName).to.contains(createRoleData.name);
    });

    it('should go to edit role page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditRole', baseContext);

      await boRolesPage.goToEditRolePage(page, 1);

      const pageTitle = await boRolesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boRolesCreatePage.pageTitleEdit(createRoleData.name));
    });

    it('should update the role', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateRole', baseContext);

      const textResult = await boRolesCreatePage.createEditRole(page, editRoleData);
      expect(textResult).to.equal(boRolesCreatePage.successfulUpdateMessage);
    });
  });

  // 3 : Delete role
  describe('Delete role', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await boRolesPage.filterRoles(page, 'input', 'name', editRoleData.name);

      const textName = await boRolesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textName).to.contains(editRoleData.name);
    });

    it('should delete role', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteRole', baseContext);

      const textResult = await boRolesPage.deleteRole(page, 1);
      expect(boRolesPage.successfulDeleteMessage).to.contains(textResult);
    });

    it('should reset filter and check the number of roles', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfRolesAfterDelete = await boRolesPage.resetAndGetNumberOfLines(page);
      expect(numberOfRolesAfterDelete).to.be.equal(numberOfRoles);
    });
  });
});
