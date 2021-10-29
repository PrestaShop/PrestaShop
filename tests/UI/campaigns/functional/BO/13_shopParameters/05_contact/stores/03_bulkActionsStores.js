require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
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

const baseContext = 'functional_BO_shopParameters_contact_store_bulkActionsStores';

// Browser and tab
let browserContext;
let page;

let numberOfStores = 0;

const storesToCreate = [
  new StoreFaker({name: 'todelete1'}),
  new StoreFaker({name: 'todelete2'}),
];

describe('BO - Shop Parameters - Contact : Enable/Disable/Delete with Bulk Actions store', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    storesToCreate.forEach(storeToCreate => files.deleteFile(storeToCreate.imageName));
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

  describe('Create 2 stores in BO', async () => {
    storesToCreate.forEach((storeToCreate, index) => {
      it('should go to add new store page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewStorePage${index + 1}`, baseContext);

        await storesPage.goToNewStorePage(page);
        const pageTitle = await addStorePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addStorePage.pageTitleCreate);
      });

      it('should create store and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateStore${index + 1}`, baseContext);

        const textResult = await addStorePage.createEditStore(page, storeToCreate);
        await expect(textResult).to.contains(storesPage.successfulCreationMessage);

        const numberOfStoresAfterCreation = await storesPage.getNumberOfElementInGrid(page);
        await expect(numberOfStoresAfterCreation).to.be.equal(numberOfStores + index + 1);
      });
    });
  });

  describe('Enable, disable and delete stores with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await storesPage.filterTable(
        page,
        'input',
        'sl!name',
        'todelete',
      );

      const numberOfStoresAfterFilter = await storesPage.getNumberOfElementInGrid(page);
      await expect(numberOfStoresAfterFilter).to.be.at.most(numberOfStores);

      for (let i = 1; i <= numberOfStoresAfterFilter; i++) {
        const textColumn = await storesPage.getTextColumn(page, i, 'sl!name');
        await expect(textColumn).to.contains('todelete');
      }
    });

    const tests = [
      {args: {action: 'disable', statusWanted: false}},
      {args: {action: 'enable', statusWanted: true}},
    ];

    tests.forEach((test) => {
      it(`should bulk ${test.args.action} elements in grid`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Stores`, baseContext);

        await storesPage.bulkUpdateStoreStatus(page, test.args.statusWanted);

        const numberOfStoresAfterBulkUpdateStatus = await storesPage.getNumberOfElementInGrid(page);

        for (let row = 1; row <= numberOfStoresAfterBulkUpdateStatus; row++) {
          const storeStatus = await storesPage.getStoreStatus(page, row);
          await expect(storeStatus).to.equal(test.args.statusWanted);
        }
      });
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
