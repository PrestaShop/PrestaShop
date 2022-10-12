require('module-alias/register');
// Import utils
const helper = require('@utils/helpers');

// Import login test
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
// const countriesPage = require('@pages/BO/catalog/discounts');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const addNewQuickAccessPage = require('@pages/BO/catalog/discounts/index.js');
const zonesPage = require('@pages/BO/international/locations');
const countriesPage = require('@pages/BO/international/locations/countries');


// Import common tests

// Import data
const {countries} = require('@data/demo/countries');

// Import test context
const testContext = require('@utils/testContext');

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

/**
 * Function to remove country from Quick Access
 * @param baseContext {string} String to identify the test
 */
function removeCountryQuickAccessTest(baseContext = 'commonTests-removeCountryQuickAccessTest') {
  describe('POST-TEST: Delete Country Quick Access', async () => {
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

    it('should go to \'Countries\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

      await zonesPage.goToSubTabCountries(page);

      const pageTitle = await countriesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(countriesPage.pageTitle);
    });

    // it('should reset all filters and get number of countries in BO', async function () {
    //   await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    //   numberOfCountries = await countriesPage.resetAndGetNumberOfLines(page);
    //   await expect(numberOfCountries).to.be.above(0);
    // });

    // it(`should search for the country '${countries.unitedStates.name}'`, async function () {
    //   await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToEnable', baseContext);

    //   await countriesPage.filterTable(page, 'input', 'b!name', countries.unitedStates.name);

    //   const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
    //   await expect(numberOfCountriesAfterFilter).to.be.equal(1);

    //   const textColumn = await countriesPage.getTextColumnFromTable(page, 1, 'b!name', countries.afghanistan.name);
    //   await expect(textColumn).to.equal(countries.unitedStates.name);
    // });

    // it(`should check that this country is activated'`, async function () {
    //   await testContext.addContextItem(this, 'testIdentifier', 'verifyTheCountryStatus', baseContext);

    //   const countryStatus = await countriesPage.getCountryStatus(page);
    //   await expect(countryStatus).to.be.true;
    // });

    // it('should remove current page to Quick access', async function () {
    //   await testContext.addContextItem(this, 'testIdentifier', 'addCurrentPageToQuickAccess', baseContext);

    //   await addNewQuickAccessPage.removeQuickAccessPage(page);

    // });
  });
}

module.exports = {removeCountryQuickAccessTest};
