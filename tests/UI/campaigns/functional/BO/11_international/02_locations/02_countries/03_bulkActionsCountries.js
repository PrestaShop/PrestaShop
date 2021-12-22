require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const zonesPage = require('@pages/BO/international/locations');
const countriesPage = require('@pages/BO/international/locations/countries');
const addCountryPage = require('@pages/BO/international/locations/countries/add');

// Import data
const CountryFaker = require('@data/faker/country');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_locations_countries_bulkActionsCountries';

let browserContext;
let page;

const firstCountryToCreate = new CountryFaker(
  {
    name: 'todelete1',
    isoCode: 'CT',
    callPrefix: '216',
    currency: 'Euro',
    zipCodeFormat: 'NNNN',
    active: true,
  });

const secondCountryToCreate = new CountryFaker(
  {
    name: 'todelete2',
    isoCode: 'JF',
    callPrefix: '333',
    currency: 'Euro',
    zipCodeFormat: 'NNNN',
    active: false,
  });

let numberOfCountries = 0;

/*
Create 2 countries
Bulk disable them
Bulk enable them
Bulk delete them
 */
describe('BO - International - Countries : Bulk actions', async () => {
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

  describe('Create country', async () => {
    [firstCountryToCreate, secondCountryToCreate]
      .forEach((countryToCreate, index) => {
        it('should go to add new country page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToAddNewCountryPage${index}`, baseContext);

          await countriesPage.goToAddNewCountryPage(page);

          const pageTitle = await addCountryPage.getPageTitle(page);
          await expect(pageTitle).to.contains(addCountryPage.pageTitleCreate);
        });

        it('should create new country', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createNewCountry${index}`, baseContext);

          const textResult = await addCountryPage.createEditCountry(page, countryToCreate);
          await expect(textResult).to.to.contains(countriesPage.successfulCreationMessage);

          const numberOfCountriesAfterCreation = await countriesPage.getNumberOfElementInGrid(page);
          await expect(numberOfCountriesAfterCreation).to.be.equal(numberOfCountries + index + 1);
        });
      });
  });

  describe('Delete country by bulk actions', async () => {
    it('should filter country by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await countriesPage.filterTable(page, 'input', 'b!name', 'todelete');

      // Check number of countries
      const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
      await expect(numberOfCountriesAfterFilter).to.be.at.least(1);

      const textColumn = await countriesPage.getTextColumnFromTable(page, 1, 'b!name');
      await expect(textColumn).to.contains('todelete');
    });

    [
      {action: 'enable', wantedStatus: true},
      {action: 'disable', wantedStatus: false},
    ].forEach((test) => {
      it(`should ${test.action} countries with bulk actions`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.action}Countries`, baseContext);

        await countriesPage.bulkSetStatus(page, test.wantedStatus);

        const numberOfZonesBulkActions = await countriesPage.getNumberOfElementInGrid(page);

        for (let row = 1; row <= numberOfZonesBulkActions; row++) {
          const rowStatus = await countriesPage.getCountryStatus(page, row);
          await expect(rowStatus).to.equal(test.wantedStatus);
        }
      });
    });

    it('should bulk delete countries', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCountries', baseContext);

      const textResult = await countriesPage.deleteCountriesByBulkActions(page);
      await expect(textResult).to.to.contains(countriesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfCountriesAfterReset = await countriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCountriesAfterReset).to.be.equal(numberOfCountries);
    });
  });
});
