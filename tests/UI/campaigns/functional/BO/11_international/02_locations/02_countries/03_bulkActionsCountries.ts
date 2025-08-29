import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCountriesPage,
  boCountriesCreatePage,
  boDashboardPage,
  boLoginPage,
  boZonesPage,
  type BrowserContext,
  FakerCountry,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_locations_countries_bulkActionsCountries';

/*
Create 2 countries
Bulk disable them
Bulk enable them
Bulk delete them
 */
describe('BO - International - Countries : Bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCountries: number = 0;

  const firstCountryToCreate: FakerCountry = new FakerCountry({
    name: 'todelete1',
    isoCode: 'CT',
    callPrefix: '216',
    currency: 'Euro',
    zipCodeFormat: 'NNNN',
    active: true,
  });
  const secondCountryToCreate: FakerCountry = new FakerCountry({
    name: 'todelete2',
    isoCode: 'JF',
    callPrefix: '333',
    currency: 'Euro',
    zipCodeFormat: 'NNNN',
    active: false,
  });

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

  describe('Create country', async () => {
    [firstCountryToCreate, secondCountryToCreate]
      .forEach((countryToCreate: FakerCountry, index: number) => {
        it('should go to add new country page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToAddNewCountryPage${index}`, baseContext);

          await boCountriesPage.goToAddNewCountryPage(page);

          const pageTitle = await boCountriesCreatePage.getPageTitle(page);
          expect(pageTitle).to.contains(boCountriesCreatePage.pageTitleCreate);
        });

        it('should create new country', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createNewCountry${index}`, baseContext);

          const textResult = await boCountriesCreatePage.createEditCountry(page, countryToCreate);
          expect(textResult).to.to.contains(boCountriesPage.successfulCreationMessage);

          const numberOfCountriesAfterCreation = await boCountriesPage.getNumberOfElementInGrid(page);
          expect(numberOfCountriesAfterCreation).to.be.equal(numberOfCountries + index + 1);
        });
      });
  });

  describe('Delete country by bulk actions', async () => {
    it('should filter country by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await boCountriesPage.filterTable(page, 'input', 'b!name', 'todelete');

      // Check number of countries
      const numberOfCountriesAfterFilter = await boCountriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCountriesAfterFilter).to.be.at.least(1);

      const textColumn = await boCountriesPage.getTextColumnFromTable(page, 1, 'b!name');
      expect(textColumn).to.contains('todelete');
    });

    [
      {action: 'enable', wantedStatus: true},
      {action: 'disable', wantedStatus: false},
    ].forEach((test) => {
      it(`should ${test.action} countries with bulk actions`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.action}Countries`, baseContext);

        await boCountriesPage.bulkSetStatus(page, test.wantedStatus);
        const numberOfZonesBulkActions = await boCountriesPage.getNumberOfElementInGrid(page);

        for (let row = 1; row <= numberOfZonesBulkActions; row++) {
          const rowStatus = await boCountriesPage.getCountryStatus(page, row);
          expect(rowStatus).to.equal(test.wantedStatus);
        }
      });
    });

    it('should bulk delete countries', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCountries', baseContext);

      const textResult = await boCountriesPage.deleteCountriesByBulkActions(page);
      expect(textResult).to.to.contains(boCountriesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfCountriesAfterReset = await boCountriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCountriesAfterReset).to.be.equal(numberOfCountries);
    });
  });
});
