import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boEmployeesPage,
  boLoginPage,
  boRolesPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

    numberOfProfiles = await boRolesPage.resetAndGetNumberOfLines(page);
    expect(numberOfProfiles).to.be.above(0);
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

        await boRolesPage.filterRoles(page, test.args.filterType, test.args.filterBy, test.args.filterValue);

        const numberOfProfilesAfterFilter = await boRolesPage.getNumberOfElementInGrid(page);
        expect(numberOfProfilesAfterFilter).to.be.at.most(numberOfProfiles);

        for (let i = 1; i <= numberOfProfilesAfterFilter; i++) {
          const textName = await boRolesPage.getTextColumnFromTable(page, i, test.args.filterBy);
          expect(textName).to.contains(test.args.filterValue);
        }
      });

      it('should reset filter', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfProfilesAfterDelete = await boRolesPage.resetAndGetNumberOfLines(page);
        expect(numberOfProfilesAfterDelete).to.be.equal(numberOfProfiles);
      });
    });
  });
});
