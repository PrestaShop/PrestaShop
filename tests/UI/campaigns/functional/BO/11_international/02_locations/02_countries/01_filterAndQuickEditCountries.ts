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
    [
      {
        testIdentifier: 'filterId',
        filterType: 'input',
        filterBy: 'id_country',
        filterValue: dataCountries.france.id.toString(),
      },
      {
        testIdentifier: 'filterName',
        filterType: 'input',
        filterBy: 'name',
        filterValue: dataCountries.netherlands.name,
      },
      {
        testIdentifier: 'filterIsoCode',
        filterType: 'input',
        filterBy: 'iso_code',
        filterValue: dataCountries.netherlands.isoCode,
      },
      {
        testIdentifier: 'filterPrefix',
        filterType: 'input',
        filterBy: 'call_prefix',
        filterValue: dataCountries.unitedKingdom.callPrefix.toString(),
      },
      {
        testIdentifier: 'filterZone',
        filterType: 'input',
        filterBy: 'zone_name',
        filterValue: dataCountries.unitedKingdom.zone,
      },
      {
        testIdentifier: 'filterStatus',
        filterType: 'select',
        filterBy: 'active',
        filterValue: dataCountries.france.active ? '1' : '0',
      },
    ].forEach((test: {testIdentifier: string, filterType: string, filterBy: string, filterValue: string}) => {
      it(`should filter by ${test.filterBy} '${test.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.testIdentifier, baseContext);

        await boCountriesPage.filterTable(
          page,
          test.filterType,
          test.filterBy,
          test.filterValue,
        );

        const numberOfCountriesAfterFilter = await boCountriesPage.getNumberOfElementInGrid(page);
        expect(numberOfCountriesAfterFilter).to.be.at.most(numberOfCountries);

        if (test.filterBy === 'active') {
          const countryStatus = await boCountriesPage.getCountryStatus(page, 1);
          expect(countryStatus).to.equal(test.filterValue === '1');
        } else {
          const textColumn = await boCountriesPage.getTextColumnFromTable(
            page,
            1,
            test.filterBy,
          );
          expect(textColumn).to.contains(test.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.testIdentifier}Reset`, baseContext);

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
        'name',
        dataCountries.germany.name,
      );

      const numberOfCountriesAfterFilter = await boCountriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCountriesAfterFilter).to.be.below(numberOfCountries);

      const textColumn = await boCountriesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textColumn).to.contains(dataCountries.germany.name);
    });

    [
      {status: 'enable', enable: true},
      {status: 'disable', enable: false},
    ].forEach((status: {status: string, enable: boolean}) => {
      it(`should ${status.status} the first country`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${status.status}Zone`, baseContext);

        await boCountriesPage.setCountryStatus(
          page,
          1,
          status.enable,
        );

        const currentStatus = await boCountriesPage.getCountryStatus(page, 1);
        expect(currentStatus).to.be.equal(status.enable);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfCountriesAfterReset = await boCountriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCountriesAfterReset).to.equal(numberOfCountries);
    });
  });
});
