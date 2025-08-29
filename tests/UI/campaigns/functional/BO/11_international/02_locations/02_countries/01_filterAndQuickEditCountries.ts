import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCountriesPage,
  boDashboardPage,
  boLoginPage,
  boZonesPage,
  type BrowserContext,
  dataCountries,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  it('should go to \'Countries\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

    await boZonesPage.goToSubTabCountries(page);

    const pageTitle = await boCountriesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCountriesPage.pageTitle);
  });

  it('should reset all filters and get number of countries in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCountries = await boCountriesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCountries).to.be.above(0);
  });

  describe('Filter countries', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId',
          filterType: 'input',
          filterBy: 'id_country',
          filterValue: dataCountries.france.id.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterName',
          filterType: 'input',
          filterBy: 'b!name',
          filterValue: dataCountries.netherlands.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterIsoCode',
          filterType: 'input',
          filterBy: 'iso_code',
          filterValue: dataCountries.netherlands.isoCode,
        },
      },
      {
        args: {
          testIdentifier: 'filterPrefix',
          filterType: 'input',
          filterBy: 'call_prefix',
          filterValue: dataCountries.unitedKingdom.callPrefix.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterZone',
          filterType: 'select',
          filterBy: 'z!id_zone',
          filterValue: dataCountries.unitedKingdom.zone,
        },
      },
      {
        args: {
          testIdentifier: 'filterStatus',
          filterType: 'select',
          filterBy: 'a!active',
          filterValue: dataCountries.france.active ? '1' : '0',
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await boCountriesPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfCountriesAfterFilter = await boCountriesPage.getNumberOfElementInGrid(page);
        expect(numberOfCountriesAfterFilter).to.be.at.most(numberOfCountries);

        if (test.args.filterBy === 'a!active') {
          const countryStatus = await boCountriesPage.getCountryStatus(page, 1);
          expect(countryStatus).to.equal(test.args.filterValue === '1');
        } else {
          const textColumn = await boCountriesPage.getTextColumnFromTable(
            page,
            1,
            test.args.filterBy,
          );
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCountriesAfterReset = await boCountriesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCountriesAfterReset).to.equal(numberOfCountries);
      });
    });
  });

  describe('Quick edit zone', async () => {
    it('should filter by name \'Germany\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await boCountriesPage.filterTable(
        page,
        'input',
        'b!name',
        dataCountries.germany.name,
      );

      const numberOfCountriesAfterFilter = await boCountriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCountriesAfterFilter).to.be.below(numberOfCountries);

      const textColumn = await boCountriesPage.getTextColumnFromTable(page, 1, 'b!name');
      expect(textColumn).to.contains(dataCountries.germany.name);
    });

    [
      {args: {status: 'enable', enable: true}},
      {args: {status: 'disable', enable: false}},
    ].forEach((status) => {
      it(`should ${status.args.status} the first country`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${status.args.status}Zone`, baseContext);

        await boCountriesPage.setCountryStatus(
          page,
          1,
          status.args.enable,
        );

        const currentStatus = await boCountriesPage.getCountryStatus(page, 1);
        expect(currentStatus).to.be.equal(status.args.enable);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfCountriesAfterReset = await boCountriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCountriesAfterReset).to.equal(numberOfCountries);
    });
  });
});
