require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const contactPage = require('@pages/BO/shopParameters/contact');
const storesPage = require('@pages/BO/shopParameters/stores');
const addStorePage = require('@pages/BO/shopParameters/stores/add');

// Import data
const StoreFaker = require('@data/faker/store');

const baseContext = 'functional_BO_shopParameters_contact_store_sortAndPagination';

// Browser and tab
let browserContext;
let page;

let numberOfStores = 0;

/*
Go to stores page
Sort stores by id, name, address, city, postal code, state and country
Create 16 store
Pagination stores
Delete created stores
 */
describe('BO - Shop Parameters - Contact : Sort and pagination stores', async () => {
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

  it('should go to \'Contact\' page', async function () {
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

  describe('Pagination stores', async () => {
    describe('Create 16 stores for pagination', async () => {
      const creationTests = new Array(16).fill(0, 0, 16);

      creationTests.forEach((test, index) => {
        describe(`Create store n°${index + 1} in BO`, async () => {
          const createStoreData = new StoreFaker({name: `todelete${index}`});

          it('should go to add new store page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `goToAddStorePage${index}`, baseContext);

            await storesPage.goToNewStorePage(page);
            const pageTitle = await addStorePage.getPageTitle(page);
            await expect(pageTitle).to.contains(addStorePage.pageTitleCreate);
          });

          it('should create store and check result', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `createStore${index}`, baseContext);

            const textResult = await addStorePage.createEditStore(page, createStoreData);
            await expect(textResult).to.contains(storesPage.successfulCreationMessage);

            const numberOfStoresAfterCreation = await storesPage.getNumberOfElementInGrid(page);
            await expect(numberOfStoresAfterCreation).to.be.equal(numberOfStores + 1 + index);
          });
        });
      });
    });

    describe('Paginate stores', async () => {
      it('should change the items number to 20 per page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

        const paginationNumber = await storesPage.selectPaginationLimit(page, '20');
        expect(paginationNumber).to.equal('1');
      });

      it('should click on next', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

        const paginationNumber = await storesPage.paginationNext(page);
        expect(paginationNumber).to.equal('2');
      });

      it('should click on previous', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

        const paginationNumber = await storesPage.paginationPrevious(page);
        expect(paginationNumber).to.equal('1');
      });

      it('should change the items number to 50 per page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

        const paginationNumber = await storesPage.selectPaginationLimit(page, '50');
        expect(paginationNumber).to.equal('1');
      });
    });

    describe('Delete created stores', async () => {
      it('should filter list by name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

        await storesPage.filterTable(page, 'input', 'sl!name', 'todelete');

        const numberOfStoresAfterFilter = await storesPage.getNumberOfElementInGrid(page);
        await expect(numberOfStoresAfterFilter).to.be.at.least(16);

        for (let i = 1; i <= numberOfStoresAfterFilter; i++) {
          const textColumn = await storesPage.getTextColumn(page, i, 'sl!name');
          await expect(textColumn).to.contains('todelete');
        }
      });

      it('should delete stores with Bulk Actions and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteStores', baseContext);

        const deleteTextResult = await storesPage.bulkDeleteStores(page);
        await expect(deleteTextResult).to.be.contains(storesPage.successfulMultiDeleteMessage);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

        const numberOfStoresAfterReset = await storesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfStoresAfterReset).to.be.equal(numberOfStores);
      });
    });
  });
});
