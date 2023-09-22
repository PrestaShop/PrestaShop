// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import employeesPage from '@pages/BO/advancedParameters/team';
import rolesPage from '@pages/BO/advancedParameters/team/roles';
import addRolePage from '@pages/BO/advancedParameters/team/roles/add';

// Import data
import RoleData from '@data/faker/role';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_team_roles_CRUDRoles';

// Create, Read, Update and Delete role in BO
describe('BO - Advanced Parameters - Team : Create, Read, Update and Delete role in BO', async () => {
  const createRoleData: RoleData = new RoleData();
  const editRoleData: RoleData = new RoleData();

  let browserContext: BrowserContext;
  let page: Page;

  let numberOfRoles: number = 0;

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
    expect(pageTitle).to.contains(employeesPage.pageTitle);
  });

  it('should go to \'Roles\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToRolesPage', baseContext);

    await employeesPage.goToRolesPage(page);

    const pageTitle = await rolesPage.getPageTitle(page);
    expect(pageTitle).to.contains(rolesPage.pageTitle);
  });

  it('should reset all filters and get number of roles', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfRoles = await rolesPage.resetAndGetNumberOfLines(page);
    expect(numberOfRoles).to.be.above(0);
  });

  // 1 : Create role
  describe('Create role in BO', async () => {
    it('should go to add new role page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewRolePage', baseContext);

      await rolesPage.goToAddNewRolePage(page);

      const pageTitle = await addRolePage.getPageTitle(page);
      expect(pageTitle).to.contains(addRolePage.pageTitleCreate);
    });

    it('should create role and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createRole', baseContext);

      const textResult = await addRolePage.createEditRole(page, createRoleData);
      expect(textResult).to.equal(rolesPage.successfulCreationMessage);

      const numberOfRolesAfterCreation = await rolesPage.getNumberOfElementInGrid(page);
      expect(numberOfRolesAfterCreation).to.be.equal(numberOfRoles + 1);
    });
  });

  // 2 : Update role
  describe('Update role', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await rolesPage.filterRoles(page, 'input', 'name', createRoleData.name);

      const textName = await rolesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textName).to.contains(createRoleData.name);
    });

    it('should go to edit role page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditRole', baseContext);

      await rolesPage.goToEditRolePage(page, 1);

      const pageTitle = await addRolePage.getPageTitle(page);
      expect(pageTitle).to.contains(addRolePage.pageTitleEdit(createRoleData.name));
    });

    it('should update the role', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateRole', baseContext);

      const textResult = await addRolePage.createEditRole(page, editRoleData);
      expect(textResult).to.equal(addRolePage.successfulUpdateMessage);
    });
  });

  // 3 : Delete role
  describe('Delete role', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await rolesPage.filterRoles(page, 'input', 'name', editRoleData.name);

      const textName = await rolesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textName).to.contains(editRoleData.name);
    });

    it('should delete role', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteRole', baseContext);

      const textResult = await rolesPage.deleteRole(page, 1);
      expect(rolesPage.successfulDeleteMessage).to.contains(textResult);
    });

    it('should reset filter and check the number of roles', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfRolesAfterDelete = await rolesPage.resetAndGetNumberOfLines(page);
      expect(numberOfRolesAfterDelete).to.be.equal(numberOfRoles);
    });
  });
});
