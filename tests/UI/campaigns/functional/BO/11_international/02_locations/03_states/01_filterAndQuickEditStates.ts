import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boStatesPage,
  boZonesPage,
  type BrowserContext,
  dataStates,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  it('should go to \'International > Locations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocationsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.locationsLink,
    );
    await boZonesPage.closeSfToolBar(page);

    const pageTitle = await boZonesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boZonesPage.pageTitle);
  });

  it('should go to \'States\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStatesPage', baseContext);

    await boZonesPage.goToSubTabStates(page);

    const pageTitle = await boStatesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boStatesPage.pageTitle);
  });

  it('should reset all filters and get number of states in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfStates = await boStatesPage.resetAndGetNumberOfLines(page);
    expect(numberOfStates).to.be.above(0);
  });

  describe('Filter states', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId',
          filterType: 'input',
          filterBy: 'id_state',
          filterValue: dataStates.california.id.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterName',
          filterType: 'input',
          filterBy: 'name',
          filterValue: dataStates.bari.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterIsoCode',
          filterType: 'input',
          filterBy: 'iso_code',
          filterValue: dataStates.california.isoCode,
        },
      },
      {
        args: {
          testIdentifier: 'filterZone',
          filterType: 'select',
          filterBy: 'id_zone',
          filterValue: dataStates.bihar.zone,
        },
      },
      {
        args: {
          testIdentifier: 'filterCountry',
          filterType: 'select',
          filterBy: 'id_country',
          filterValue: dataStates.california.country,
        },
      },
      {
        args: {
          testIdentifier: 'filterStatus',
          filterType: 'select',
          filterBy: 'active',
          filterValue: dataStates.bari.status ? '1' : '0',
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await boStatesPage.filterStates(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfStatesAfterFilter = await boStatesPage.getNumberOfElementInGrid(page);
        expect(numberOfStatesAfterFilter).to.be.at.most(numberOfStates);

        if (test.args.filterBy === 'active') {
          const countryStatus = await boStatesPage.getStateStatus(page, 1);
          expect(countryStatus).to.equal(test.args.filterValue === '1');
        } else {
          const textColumn = await boStatesPage.getTextColumn(
            page,
            1,
            test.args.filterBy,
          );
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfStatesAfterReset = await boStatesPage.resetAndGetNumberOfLines(page);
        expect(numberOfStatesAfterReset).to.equal(numberOfStates);
      });
    });
  });

  describe('Quick edit state', async () => {
    it('should filter by name \'California\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await boStatesPage.filterStates(
        page,
        'input',
        'name',
        dataStates.california.name,
      );

      const numberOfStatesAfterFilter = await boStatesPage.getNumberOfElementInGrid(page);
      expect(numberOfStatesAfterFilter).to.be.below(numberOfStates);

      const textColumn = await boStatesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(dataStates.california.name);
    });

    [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ].forEach((status) => {
      it(`should ${status.args.status} the first state`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${status.args.status}State`, baseContext);

        await boStatesPage.setStateStatus(
          page,
          1,
          status.args.enable,
        );

        const currentStatus = await boStatesPage.getStateStatus(page, 1);
        expect(currentStatus).to.be.equal(status.args.enable);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfStatesAfterReset = await boStatesPage.resetAndGetNumberOfLines(page);
      expect(numberOfStatesAfterReset).to.equal(numberOfStates);
    });
  });
});
