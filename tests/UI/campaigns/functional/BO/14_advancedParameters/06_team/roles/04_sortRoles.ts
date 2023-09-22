// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import employeesPage from '@pages/BO/advancedParameters/team';
import rolesPage from '@pages/BO/advancedParameters/team/roles';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_team_roles_sortRoles';

// Sort roles by id, name
describe('BO - Advanced Parameters - Team : Sort Roles table', async () => {
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
    await dashboardPage.closeSfToolBar(page);

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

  const tests = [
    {args: {testIdentifier: 'sortByIDDesc', sortBy: 'id_profile', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByIDAsc', sortBy: 'id_profile', sortDirection: 'asc'}},
  ];

  tests.forEach((test) => {
    it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

      const nonSortedTable = await rolesPage.getAllRowsColumnContent(page, test.args.sortBy);
      await rolesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

      const sortedTable = await rolesPage.getAllRowsColumnContent(page, test.args.sortBy);

      const expectedResult = await basicHelper.sortArray(nonSortedTable);

      if (test.args.sortDirection === 'asc') {
        expect(sortedTable).to.deep.equal(expectedResult);
      } else {
        expect(sortedTable).to.deep.equal(expectedResult.reverse());
      }
    });
  });
});
