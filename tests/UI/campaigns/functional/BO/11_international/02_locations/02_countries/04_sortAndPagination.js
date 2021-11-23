require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const zonesPage = require('@pages/BO/international/locations');
const countriesPage = require('@pages/BO/international/locations/countries');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_locations_countries_sortAndPagination';

// Using chai
const {expect} = require('chai');

let browserContext;
let page;

/*
Sort countries table
Paginate between pages
 */
describe('BO - International - Countries : Sort and pagination', async () => {
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

    const pageTitle = await zonesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(zonesPage.pageTitle);
  });

  it('should go to \'Countries\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

    await zonesPage.goToSubTabCountries(page);

    const pageTitle = await countriesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(countriesPage.pageTitle);
  });

  // 1 - Pagination next and previous
  describe('Pagination next and previous', async () => {
    it('should change the item number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await countriesPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await countriesPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await countriesPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the item number to 300 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo300', baseContext);

      const paginationNumber = await countriesPage.selectPaginationLimit(page, '300');
      expect(paginationNumber).to.equal('1');
    });
  });

  // 2 : Sort countries table
  describe('Sort countries table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_country', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByCountryAsc', sortBy: 'b!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCountryDesc', sortBy: 'b!name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIsoCodeAsc', sortBy: 'iso_code', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIsoCodeDesc', sortBy: 'iso_code', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCallPrefixAsc', sortBy: 'call_prefix', sortDirection: 'up', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByCallPrefixDesc', sortBy: 'call_prefix', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByZoneAsc', sortBy: 'z!id_zone', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByZoneDesc', sortBy: 'z!id_zone', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_country', sortDirection: 'up', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await countriesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await countriesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await countriesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await countriesPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'up') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await countriesPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.equal('1');
    });
  });
});
