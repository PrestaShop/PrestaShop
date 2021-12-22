require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const zonesPage = require('@pages/BO/international/locations');
const countriesPage = require('@pages/BO/international/locations/countries');

// Import data
const {countries} = require('@data/demo/countries');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_locations_countries_filterAndQuickEditCountries';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfCountries = 0;

/*
Filter countries by : id, name, iso code, call prefix, id zone, status
Quick Edit country
 */
describe('BO - International - Countries : Filter and quick edit', async () => {
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

  it('should go to \'Countries\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

    await zonesPage.goToSubTabCountries(page);
    const pageTitle = await countriesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(countriesPage.pageTitle);
  });

  it('should reset all filters and get number of countries in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCountries = await countriesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCountries).to.be.above(0);
  });

  describe('Filter countries', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId',
          filterType: 'input',
          filterBy: 'id_country',
          filterValue: countries.france.id,
        },
      },
      {
        args: {
          testIdentifier: 'filterName',
          filterType: 'input',
          filterBy: 'b!name',
          filterValue: countries.netherlands.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterIsoCode',
          filterType: 'input',
          filterBy: 'iso_code',
          filterValue: countries.netherlands.isoCode,
        },
      },
      {
        args: {
          testIdentifier: 'filterPrefix',
          filterType: 'input',
          filterBy: 'call_prefix',
          filterValue: countries.unitedKingdom.callPrefix,
        },
      },
      {
        args: {
          testIdentifier: 'filterZone',
          filterType: 'select',
          filterBy: 'z!id_zone',
          filterValue: countries.unitedKingdom.zone,
        },
      },
      {
        args: {
          testIdentifier: 'filterStatus',
          filterType: 'select',
          filterBy: 'a!active',
          filterValue: countries.france.status,
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
        await expect(numberOfCountriesAfterFilter).to.be.at.most(numberOfCountries);

        if (test.args.filterBy === 'a!active') {
          const countryStatus = await countriesPage.getCountryStatus(page, 1);
          await expect(countryStatus).to.equal(test.args.filterValue);
        } else {
          const textColumn = await countriesPage.getTextColumnFromTable(
            page,
            1,
            test.args.filterBy,
          );

          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCountriesAfterReset = await countriesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCountriesAfterReset).to.equal(numberOfCountries);
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
        countries.germany.name,
      );

      const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
      await expect(numberOfCountriesAfterFilter).to.be.below(numberOfCountries);

      const textColumn = await countriesPage.getTextColumnFromTable(page, 1, 'b!name');
      await expect(textColumn).to.contains(countries.germany.name);
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
        await expect(currentStatus).to.be.equal(status.args.enable);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfCountriesAfterReset = await countriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCountriesAfterReset).to.equal(numberOfCountries);
    });
  });
});
