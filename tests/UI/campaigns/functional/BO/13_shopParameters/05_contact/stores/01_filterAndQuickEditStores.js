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

// Import data
const {stores} = require('@data/demo/stores');

const baseContext = 'functional_BO_shopParameters_contact_stores_filterAndQuickEditStores';

// Browser and tab
let browserContext;
let page;

let numberOfStores = 0;

describe('BO - Shop Parameters - Contact : Filter and quick edit stores', async () => {
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

  it('should go to \'Shop Parameters > Contact\' page', async function () {
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

  it('should go to \'Stores\' page', async function () {
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

  describe('Filter stores', async () => {
    const tests = [
      {
        args:
          {
            column: 'Id',
            filterType: 'input',
            filterBy: 'id_store',
            filterValue: stores.first.id,
          },
      },
      {
        args:
          {
            column: 'Name',
            filterType: 'input',
            filterBy: 'sl!name',
            filterValue: stores.first.name,
          },
      },
      {
        args:
          {
            column: 'Address',
            filterType: 'input',
            filterBy: 'sl!address1',
            filterValue: stores.first.address1,
          },
      },
      {
        args:
          {
            column: 'City',
            filterType: 'input',
            filterBy: 'city',
            filterValue: stores.first.city,
          },
      },
      {
        args:
          {
            column: 'PostCode',
            filterType: 'input',
            filterBy: 'postcode',
            filterValue: stores.first.postCode,
          },
      },
      {
        args:
          {
            column: 'State',
            filterType: 'input',
            filterBy: 'st!name',
            filterValue: stores.first.state,
          },
      },
      {
        args:
          {
            column: 'country',
            filterType: 'input',
            filterBy: 'cl!name',
            filterValue: stores.first.country,
          },
      },
      {
        args:
          {
            column: 'Phone',
            filterType: 'input',
            filterBy: 'phone',
            filterValue: stores.first.phone,
          },
      },
      {
        args:
          {
            column: 'Fax',
            filterType: 'input',
            filterBy: 'fax',
            filterValue: stores.first.fax,
          },
      },
      {
        args:
          {
            column: 'Status',
            filterType: 'select',
            filterBy: 'active',
            filterValue: stores.first.enabled,
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.column} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filter${test.args.column}`, baseContext);

        await storesPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfStoresAfterFilter = await storesPage.getNumberOfElementInGrid(page);
        await expect(numberOfStoresAfterFilter).to.be.at.most(numberOfStores);

        for (let row = 1; row <= numberOfStoresAfterFilter; row++) {
          if (test.args.column === 'Status') {
            const storeStatus = await storesPage.getStoreStatus(page, row);
            await expect(storeStatus).to.equal(test.args.filterValue);
          } else {
            const textColumn = await storesPage.getTextColumn(page, row, test.args.filterBy);
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilter${test.args.column}`, baseContext);

        const numberOfStoresAfterReset = await storesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfStoresAfterReset).to.equal(numberOfStores);
      });
    });
  });

  describe('Quick edit stores', async () => {
    it(`should filter by name '${stores.second.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await storesPage.filterTable(page, 'input', 'sl!name', stores.second.name);

      const numberOfStoresAfterFilter = await storesPage.getNumberOfElementInGrid(page);
      await expect(numberOfStoresAfterFilter).to.be.at.most(numberOfStores);

      const textColumn = await storesPage.getTextColumn(page, 1, 'sl!name');
      await expect(textColumn).to.contains(stores.second.name);
    });

    const tests = [
      {args: {action: 'disable', statusWanted: false}},
      {args: {action: 'enable', statusWanted: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} first element in grid`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Store`, baseContext);

        await storesPage.setStoreStatus(page, 1, test.args.statusWanted);

        const storeStatus = await storesPage.getStoreStatus(page, 1);
        await expect(storeStatus).to.equal(test.args.statusWanted);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterQuickEdit', baseContext);

      const numberOfStoresAfterReset = await storesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfStoresAfterReset).to.equal(numberOfStores);
    });
  });
});
