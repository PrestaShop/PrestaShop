require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const contactPage = require('@pages/BO/shopParameters/contact');
const storesPage = require('@pages/BO/shopParameters/stores');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_contact_store_sortStores';

// Import expect from chai
const {expect} = require('chai');


// Browser and tab
let browserContext;
let page;


let numberOfStores = 0;

describe('Sort stores by id, name, address, city, postal code, state and country', async () => {
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

  it('should go to contact page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToContactPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.contactLink,
    );

    await contactPage.closeSfToolBar(page);

    const pageTitle = await contactPage.getPageTitle(page);
    await expect(pageTitle).to.contains(contactPage.pageTitle);
  });

  it('should go to stores page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStoresPage', baseContext);

    await contactPage.goToStoresPage(page);

    const pageTitle = await storesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(storesPage.pageTitle);
  });

  it('should reset all filters and get number of stores in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfStores = await storesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfStores).to.be.above(0);
  });


  describe('Sort stores', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_store', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameAsc', sortBy: 'sl!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc', sortBy: 'sl!name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByAddressAsc', sortBy: 'sl!address1', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByAddressDesc', sortBy: 'sl!address1', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCityAsc', sortBy: 'city', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCityDesc', sortBy: 'city', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByPostCodeAsc', sortBy: 'postcode', sortDirection: 'up', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByPostCodeDesc', sortBy: 'postcode', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByStateAsc', sortBy: 'st!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByStateDesc', sortBy: 'st!name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCountryAsc', sortBy: 'cl!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCountryDesc', sortBy: 'city', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_store', sortDirection: 'up', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await storesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await storesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await storesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await storesPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'up') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });
});
