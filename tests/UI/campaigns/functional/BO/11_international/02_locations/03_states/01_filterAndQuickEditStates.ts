// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import zonesPage from '@pages/BO/international/locations';
import statesPage from '@pages/BO/international/locations/states';

// Import data
import {states} from '@data/demo/states';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_locations_states_filterAndQuickEditStates';

/*
Filter states by : id, name, iso code, id, country, id zone, status
Quick edit state
 */
describe('BO - International - States : Filter and quick edit', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfStates: number = 0;

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

  it('should go to \'International > Locations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocationsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.locationsLink,
    );
    await zonesPage.closeSfToolBar(page);

    const pageTitle = await zonesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(zonesPage.pageTitle);
  });

  it('should go to \'States\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStatesPage', baseContext);

    await zonesPage.goToSubTabStates(page);

    const pageTitle = await statesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(statesPage.pageTitle);
  });

  it('should reset all filters and get number of states in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfStates = await statesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfStates).to.be.above(0);
  });

  describe('Filter states', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId',
          filterType: 'input',
          filterBy: 'id_state',
          filterValue: states.california.id.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterName',
          filterType: 'input',
          filterBy: 'a!name',
          filterValue: states.bari.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterIsoCode',
          filterType: 'input',
          filterBy: 'iso_code',
          filterValue: states.california.isoCode,
        },
      },
      {
        args: {
          testIdentifier: 'filterZone',
          filterType: 'select',
          filterBy: 'z!id_zone',
          filterValue: states.bihar.zone,
        },
      },
      {
        args: {
          testIdentifier: 'filterCountry',
          filterType: 'select',
          filterBy: 'cl!id_country',
          filterValue: states.california.country,
        },
      },
      {
        args: {
          testIdentifier: 'filterStatus',
          filterType: 'select',
          filterBy: 'a!active',
          filterValue: states.bari.status ? '1' : '0',
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await statesPage.filterStates(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfStatesAfterFilter = await statesPage.getNumberOfElementInGrid(page);
        await expect(numberOfStatesAfterFilter).to.be.at.most(numberOfStates);

        if (test.args.filterBy === 'a!active') {
          const countryStatus = await statesPage.getStateStatus(page, 1);
          await expect(countryStatus).to.equal(test.args.filterValue === '1');
        } else {
          const textColumn = await statesPage.getTextColumn(
            page,
            1,
            test.args.filterBy,
          );
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfStatesAfterReset = await statesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfStatesAfterReset).to.equal(numberOfStates);
      });
    });
  });

  describe('Quick edit state', async () => {
    it('should filter by name \'California\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await statesPage.filterStates(
        page,
        'input',
        'a!name',
        states.california.name,
      );

      const numberOfStatesAfterFilter = await statesPage.getNumberOfElementInGrid(page);
      await expect(numberOfStatesAfterFilter).to.be.below(numberOfStates);

      const textColumn = await statesPage.getTextColumn(page, 1, 'a!name');
      await expect(textColumn).to.contains(states.california.name);
    });

    const statuses = [
      {args: {status: 'enable', enable: true}},
      {args: {status: 'disable', enable: false}},
    ];

    statuses.forEach((status) => {
      it(`should ${status.args.status} the first state`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${status.args.status}State`, baseContext);

        await statesPage.setStateStatus(
          page,
          1,
          status.args.enable,
        );

        const currentStatus = await statesPage.getStateStatus(page, 1);
        await expect(currentStatus).to.be.equal(status.args.enable);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfStatesAfterReset = await statesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfStatesAfterReset).to.equal(numberOfStates);
    });
  });
});
