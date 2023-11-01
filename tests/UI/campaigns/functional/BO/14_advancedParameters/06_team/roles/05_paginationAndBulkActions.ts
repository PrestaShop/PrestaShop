// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import employeesPage from '@pages/BO/advancedParameters/team';
import rolesPage from '@pages/BO/advancedParameters/team/roles';
import addProfilePage from '@pages/BO/advancedParameters/team/roles/add';

// Import data
import RoleData from '@data/faker/role';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_team_roles_paginationAndBulkActions';

describe('BO - Advanced Parameters - Team : Pagination and delete roles by bulk actions', async () => {
  const createRoleData: RoleData = new RoleData();

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

  // 1 : Create 11 roles
  describe('Create 10 roles in BO', async () => {
    const tests: number[] = new Array(10).fill(0, 0, 10);
    tests.forEach((test: number, index: number) => {
      it('should go to add new profile page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewRolePage${index}`, baseContext);

        await rolesPage.goToAddNewRolePage(page);

        const pageTitle = await addProfilePage.getPageTitle(page);
        expect(pageTitle).to.contains(addProfilePage.pageTitleCreate);
      });

      it(`should create profile n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateRole${index}`, baseContext);

        const textResult = await addProfilePage.createEditRole(page, createRoleData);
        expect(textResult).to.equal(rolesPage.successfulCreationMessage);
      });

      it('should check roles number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProfilesNumber${index}`, baseContext);

        const numberOfRolesAfterDelete = await rolesPage.resetAndGetNumberOfLines(page);
        expect(numberOfRolesAfterDelete).to.be.equal(numberOfRoles + 1 + index);
      });
    });
  });

  // 2 : Test pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await rolesPage.selectPaginationLimit(page, 10);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await rolesPage.paginationNext(page);
      expect(paginationNumber).to.contain('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await rolesPage.paginationPrevious(page);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await rolesPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contain('(page 1 / 1)');
    });
  });

  // 3 : Delete the 11 roles with bulk actions
  describe('Delete roles with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await rolesPage.filterRoles(page, 'input', 'name', createRoleData.name);

      const textName = await rolesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textName).to.contains(createRoleData.name);
    });

    it('should delete roles with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteProfile', baseContext);

      const deleteTextResult = await rolesPage.deleteBulkActions(page);
      expect(rolesPage.successfulDeleteMessage).to.be.contains(deleteTextResult);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfRolesAfterDelete = await rolesPage.resetAndGetNumberOfLines(page);
      expect(numberOfRolesAfterDelete).to.be.equal(numberOfRoles);
    });
  });
});
