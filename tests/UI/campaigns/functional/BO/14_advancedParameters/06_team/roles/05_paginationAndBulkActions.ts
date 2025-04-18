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

const baseContext: string = 'functional_BO_advancedParameters_team_roles_paginationAndBulkActions';

describe('BO - Advanced Parameters - Team : Pagination and delete roles by bulk actions', async () => {
  const createRoleData: FakerEmployeeRole = new FakerEmployeeRole();

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

  // 1 : Create 11 roles
  describe('Create 10 roles in BO', async () => {
    const tests: number[] = new Array(10).fill(0, 0, 10);
    tests.forEach((test: number, index: number) => {
      it('should go to add new profile page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewRolePage${index}`, baseContext);

        await boRolesPage.goToAddNewRolePage(page);

        const pageTitle = await boRolesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boRolesCreatePage.pageTitleCreate);
      });

      it(`should create profile n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateRole${index}`, baseContext);

        const textResult = await boRolesCreatePage.createEditRole(page, createRoleData);
        expect(textResult).to.equal(boRolesPage.successfulCreationMessage);
      });

      it('should check roles number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProfilesNumber${index}`, baseContext);

        const numberOfRolesAfterDelete = await boRolesPage.resetAndGetNumberOfLines(page);
        expect(numberOfRolesAfterDelete).to.be.equal(numberOfRoles + 1 + index);
      });
    });
  });

  // 2 : Test pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await boRolesPage.selectPaginationLimit(page, 10);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boRolesPage.paginationNext(page);
      expect(paginationNumber).to.contain('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boRolesPage.paginationPrevious(page);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await boRolesPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contain('(page 1 / 1)');
    });
  });

  // 3 : Delete the 11 roles with bulk actions
  describe('Delete roles with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await boRolesPage.filterRoles(page, 'input', 'name', createRoleData.name);

      const textName = await boRolesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textName).to.contains(createRoleData.name);
    });

    it('should delete roles with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteProfile', baseContext);

      const deleteTextResult = await boRolesPage.deleteBulkActions(page);
      expect(boRolesPage.successfulDeleteMessage).to.be.contains(deleteTextResult);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfRolesAfterDelete = await boRolesPage.resetAndGetNumberOfLines(page);
      expect(numberOfRolesAfterDelete).to.be.equal(numberOfRoles);
    });
  });
});
