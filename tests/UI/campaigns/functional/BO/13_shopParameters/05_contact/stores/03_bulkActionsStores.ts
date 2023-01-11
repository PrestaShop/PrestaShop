// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import contactPage from '@pages/BO/shopParameters/contact';
import storesPage from '@pages/BO/shopParameters/stores';
import addStorePage from '@pages/BO/shopParameters/stores/add';

// Import data
import StoreData from '@data/faker/store';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_contact_stores_bulkActionsStores';

describe('BO - Shop Parameters - Contact : Enable/Disable/Delete with Bulk Actions store', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfStores: number = 0;

  const storesToCreate: StoreData[] = [
    new StoreData({name: 'todelete1'}),
    new StoreData({name: 'todelete2'}),
  ];

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

  describe('Create 2 stores in BO', async () => {
    storesToCreate.forEach((storeToCreate: StoreData, index: number) => {
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
