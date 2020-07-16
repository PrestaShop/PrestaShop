require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const employeesPage = require('@pages/BO/advancedParameters/team/index');
const profilesPage = require('@pages/BO/advancedParameters/team/profiles/index');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_team_profiles_sortProfiles';

let browserContext;
let page;

let numberOfProfiles = 0;

// Sort profiles by id, name
describe('Sort Profiles', async () => {
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

  it('should go to \'Advanced parameters > Team\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAdvancedParamsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.teamLink,
    );

    await dashboardPage.closeSfToolBar(page);

    const pageTitle = await employeesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(employeesPage.pageTitle);
  });

  it('should go to \'Profiles\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProfilesPage', baseContext);

    await employeesPage.goToProfilesPage(page);
    const pageTitle = await profilesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(profilesPage.pageTitle);
  });

  it('should reset all filters and get number of profiles', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfProfiles = await profilesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProfiles).to.be.above(0);
  });

  const tests = [
    {args: {testIdentifier: 'sortByIDDesc', sortBy: 'id_profile', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByIDAsc', sortBy: 'id_profile', sortDirection: 'asc'}},
  ];

  tests.forEach((test) => {
    it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

      let nonSortedTable = await profilesPage.getAllRowsColumnContent(page, test.args.sortBy);
      await profilesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

      let sortedTable = await profilesPage.getAllRowsColumnContent(page, test.args.sortBy);

      if (test.args.isFloat) {
        nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
        sortedTable = await sortedTable.map(text => parseFloat(text));
      }

      const expectedResult = await profilesPage.sortArray(nonSortedTable, test.args.isFloat);

      if (test.args.sortDirection === 'asc') {
        await expect(sortedTable).to.deep.equal(expectedResult);
      } else {
        await expect(sortedTable).to.deep.equal(expectedResult.reverse());
      }
    });
  });
});
