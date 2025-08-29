import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boEmployeesPage,
  boLoginPage,
  boRolesPage,
  type BrowserContext,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_team_roles_sortRoles';

// Sort roles by id, name
describe('BO - Advanced Parameters - Team : Sort Roles table', async () => {
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
    await boDashboardPage.closeSfToolBar(page);

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

  const tests = [
    {args: {testIdentifier: 'sortByIDDesc', sortBy: 'id_profile', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByIDAsc', sortBy: 'id_profile', sortDirection: 'asc'}},
  ];

  tests.forEach((test) => {
    it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

      const nonSortedTable = await boRolesPage.getAllRowsColumnContent(page, test.args.sortBy);
      await boRolesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

      const sortedTable = await boRolesPage.getAllRowsColumnContent(page, test.args.sortBy);

      const expectedResult = await utilsCore.sortArray(nonSortedTable);

      if (test.args.sortDirection === 'asc') {
        expect(sortedTable).to.deep.equal(expectedResult);
      } else {
        expect(sortedTable).to.deep.equal(expectedResult.reverse());
      }
    });
  });
});
