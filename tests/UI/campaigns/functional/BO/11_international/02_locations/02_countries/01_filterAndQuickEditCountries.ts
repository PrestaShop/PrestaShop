// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import zonesPage from '@pages/BO/international/locations';
import countriesPage from '@pages/BO/international/locations/countries';

// Import data
import Countries from '@data/demo/countries';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_locations_countries_filterAndQuickEditCountries';

/*
Filter countries by : id, name, iso code, call prefix, id zone, status
Quick Edit country
 */
describe('BO - International - Countries : Filter and quick edit', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCountries: number = 0;

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
    expect(pageTitle).to.contains(zonesPage.pageTitle);
  });

  it('should go to \'Countries\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

    await zonesPage.goToSubTabCountries(page);

    const pageTitle = await countriesPage.getPageTitle(page);
    expect(pageTitle).to.contains(countriesPage.pageTitle);
  });

  it('should reset all filters and get number of countries in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCountries = await countriesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCountries).to.be.above(0);
  });

  describe('Filter countries', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId',
          filterType: 'input',
          filterBy: 'id_country',
          filterValue: Countries.france.id.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterName',
          filterType: 'input',
          filterBy: 'b!name',
          filterValue: Countries.netherlands.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterIsoCode',
          filterType: 'input',
          filterBy: 'iso_code',
          filterValue: Countries.netherlands.isoCode,
        },
      },
      {
        args: {
          testIdentifier: 'filterPrefix',
          filterType: 'input',
          filterBy: 'call_prefix',
          filterValue: Countries.unitedKingdom.callPrefix.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterZone',
          filterType: 'select',
          filterBy: 'z!id_zone',
          filterValue: Countries.unitedKingdom.zone,
        },
      },
      {
        args: {
          testIdentifier: 'filterStatus',
          filterType: 'select',
          filterBy: 'a!active',
          filterValue: Countries.france.active ? '1' : '0',
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await countriesPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
        expect(numberOfCountriesAfterFilter).to.be.at.most(numberOfCountries);

        if (test.args.filterBy === 'a!active') {
          const countryStatus = await countriesPage.getCountryStatus(page, 1);
          expect(countryStatus).to.equal(test.args.filterValue === '1');
        } else {
          const textColumn = await countriesPage.getTextColumnFromTable(
            page,
            1,
            test.args.filterBy,
          );
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCountriesAfterReset = await countriesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCountriesAfterReset).to.equal(numberOfCountries);
      });
    });
  });

  describe('Quick edit zone', async () => {
    it('should filter by name \'Germany\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await countriesPage.filterTable(
        page,
        'input',
        'b!name',
        Countries.germany.name,
      );

      const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCountriesAfterFilter).to.be.below(numberOfCountries);

      const textColumn = await countriesPage.getTextColumnFromTable(page, 1, 'b!name');
      expect(textColumn).to.contains(Countries.germany.name);
    });

    [
      {args: {status: 'enable', enable: true}},
      {args: {status: 'disable', enable: false}},
    ].forEach((status) => {
      it(`should ${status.args.status} the first country`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${status.args.status}Zone`, baseContext);

        await countriesPage.setCountryStatus(
          page,
          1,
          status.args.enable,
        );

        const currentStatus = await countriesPage.getCountryStatus(page, 1);
        expect(currentStatus).to.be.equal(status.args.enable);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfCountriesAfterReset = await countriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCountriesAfterReset).to.equal(numberOfCountries);
    });
  });
});
