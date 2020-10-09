require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const taxesPage = require('@pages/BO/international/taxes');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_taxes_sortTaxes';

let browserContext;
let page;
let numberOfTaxes = 0;

describe('Sort taxes', async () => {
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

  it('should go to Taxes page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.taxesLink,
    );

    const pageTitle = await taxesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(taxesPage.pageTitle);
  });

  it('should reset all filters and get Number of Taxes in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTaxes = await taxesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfTaxes).to.be.above(0);
  });

  const tests = [
    {
      args:
        {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_tax', sortDirection: 'desc', isFloat: true,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc', isFloat: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc', isFloat: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByRateAsc', sortBy: 'rate', sortDirection: 'asc', isFloat: true,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByRateDesc', sortBy: 'rate', sortDirection: 'desc', isFloat: true,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_tax', sortDirection: 'asc', isFloat: true,
        },
    },
  ];

  tests.forEach((test) => {
    it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

      // Get non sorted table
      let nonSortedTable = await taxesPage.getAllRowsColumnContent(page, test.args.sortBy);

      // Get sorted table
      await taxesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);
      let sortedTable = await taxesPage.getAllRowsColumnContent(page, test.args.sortBy);

      if (test.args.isFloat) {
        nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
        sortedTable = await sortedTable.map(text => parseFloat(text));
      }

      // Sort Array with javascript
      const expectedResult = await taxesPage.sortArray(nonSortedTable, test.args.isFloat);

      if (test.args.sortDirection === 'asc') {
        await expect(sortedTable).to.deep.equal(expectedResult);
      } else {
        await expect(sortedTable).to.deep.equal(expectedResult.reverse());
      }
    });
  });
});
