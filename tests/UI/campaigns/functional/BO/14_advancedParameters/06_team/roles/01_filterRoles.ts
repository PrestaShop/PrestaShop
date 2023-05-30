// Import utils
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

const baseContext: string = 'functional_BO_advancedParameters_team_roles_filterRoles';

/*
Filter roles table by ID and Name
 */
describe('BO - Advanced Parameters - Team : Filter roles table', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let numberOfProfiles: number = 0;

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
    await expect(pageTitle).to.contains(employeesPage.pageTitle);
  });

  it('should go to \'Roles\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToRolesPage', baseContext);

    await employeesPage.goToRolesPage(page);

    const pageTitle = await rolesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(rolesPage.pageTitle);
  });

  it('should reset all filters and get number of roles', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfProfiles = await rolesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProfiles).to.be.above(0);
  });

  // 1 : Filter roles table
  describe('Filter roles table in BO', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId', filterType: 'input', filterBy: 'id_profile', filterValue: '4',
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterType: 'input', filterBy: 'name', filterValue: 'Logistician',
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter list by ${test.args.filterBy}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        await rolesPage.filterRoles(page, test.args.filterType, test.args.filterBy, test.args.filterValue);

        const numberOfProfilesAfterFilter = await rolesPage.getNumberOfElementInGrid(page);
        await expect(numberOfProfilesAfterFilter).to.be.at.most(numberOfProfiles);

        for (let i = 1; i <= numberOfProfilesAfterFilter; i++) {
          const textName = await rolesPage.getTextColumnFromTable(page, i, test.args.filterBy);
          await expect(textName).to.contains(test.args.filterValue);
        }
      });

      it('should reset filter', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfProfilesAfterDelete = await rolesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProfilesAfterDelete).to.be.equal(numberOfProfiles);
      });
    });
  });
});
