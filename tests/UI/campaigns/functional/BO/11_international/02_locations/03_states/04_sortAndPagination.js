require('module-alias/register');

const {expect} = require('chai');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const zonesPage = require('@pages/BO/international/locations');
const statesPage = require('@pages/BO/international/locations/states');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_locations_states_sortAndPagination';

let browserContext;
let page;

/*
Sort states table
Paginate between pages
 */
describe('BO - International - States : Sort and pagination', async () => {
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

  it('should go to \'States\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStatesPage', baseContext);

    await zonesPage.goToSubTabStates(page);

    const pageTitle = await statesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(statesPage.pageTitle);
  });

  // 1 - Pagination next and previous
  describe('Pagination next and previous', async () => {
    it('should change the item number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await statesPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await statesPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await statesPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the item number to 1000 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo1000', baseContext);

      const paginationNumber = await statesPage.selectPaginationLimit(page, '1000');
      expect(paginationNumber).to.equal('1');
    });
  });

  // 2 : Sort states table
  describe('Sort states table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_state', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByCountryAsc', sortBy: 'a!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCountryDesc', sortBy: 'a!name', sortDirection: 'down',
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
          testIdentifier: 'sortByCallPrefixAsc', sortBy: 'z!id_zone', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCallPrefixDesc', sortBy: 'z!id_zone', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByZoneAsc', sortBy: 'cl!id_country', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByZoneDesc', sortBy: 'cl!id_country', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_state', sortDirection: 'up', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await statesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await statesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await statesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await statesPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'up') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await statesPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.equal('1');
    });
  });
});
