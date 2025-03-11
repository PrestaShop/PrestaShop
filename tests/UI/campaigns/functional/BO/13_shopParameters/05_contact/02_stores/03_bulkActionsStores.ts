import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boContactsPage,
  boDashboardPage,
  boLoginPage,
  boStoresPage,
  boStoresCreatePage,
  type BrowserContext,
  FakerStore,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_contact_stores_bulkActionsStores';

describe('BO - Shop Parameters - Contact : Enable/Disable/Delete with Bulk Actions store', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfStores: number = 0;

  const storesToCreate: FakerStore[] = [
    new FakerStore({name: 'todelete1'}),
    new FakerStore({name: 'todelete2'}),
  ];

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Shop Parameters > Contact\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToContactPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.contactLink,
    );
    await boContactsPage.closeSfToolBar(page);

    const pageTitle = await boContactsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boContactsPage.pageTitle);
  });

  it('should go to \'Stores\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStoresPage', baseContext);

    await boContactsPage.goToStoresPage(page);

    const pageTitle = await boStoresPage.getPageTitle(page);
    expect(pageTitle).to.contains(boStoresPage.pageTitle);
  });

  it('should reset all filters and get number of stores in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfStores = await boStoresPage.resetAndGetNumberOfLines(page);
    expect(numberOfStores).to.be.above(0);
  });

  describe('Create 2 stores in BO', async () => {
    storesToCreate.forEach((storeToCreate: FakerStore, index: number) => {
      it('should go to add new store page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewStorePage${index + 1}`, baseContext);

        await boStoresPage.goToNewStorePage(page);

        const pageTitle = await boStoresCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boStoresCreatePage.pageTitleCreate);
      });

      it('should create store and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateStore${index + 1}`, baseContext);

        const textResult = await boStoresCreatePage.createEditStore(page, storeToCreate);
        expect(textResult).to.contains(boStoresPage.successfulCreationMessage);

        const numberOfStoresAfterCreation = await boStoresPage.getNumberOfElementInGrid(page);
        expect(numberOfStoresAfterCreation).to.be.equal(numberOfStores + index + 1);
      });
    });
  });

  describe('Enable, disable and delete stores with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await boStoresPage.filterTable(
        page,
        'input',
        'sl!name',
        'todelete',
      );

      const numberOfStoresAfterFilter = await boStoresPage.getNumberOfElementInGrid(page);
      expect(numberOfStoresAfterFilter).to.be.at.most(numberOfStores);

      for (let i = 1; i <= numberOfStoresAfterFilter; i++) {
        const textColumn = await boStoresPage.getTextColumn(page, i, 'sl!name');
        expect(textColumn).to.contains('todelete');
      }
    });

    const tests = [
      {args: {action: 'disable', statusWanted: false}},
      {args: {action: 'enable', statusWanted: true}},
    ];

    tests.forEach((test) => {
      it(`should bulk ${test.args.action} elements in grid`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Stores`, baseContext);

        await boStoresPage.bulkUpdateStoreStatus(page, test.args.statusWanted);

        const numberOfStoresAfterBulkUpdateStatus = await boStoresPage.getNumberOfElementInGrid(page);

        for (let row = 1; row <= numberOfStoresAfterBulkUpdateStatus; row++) {
          const storeStatus = await boStoresPage.getStoreStatus(page, row);
          expect(storeStatus).to.equal(test.args.statusWanted);
        }
      });
    });

    it('should delete stores with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteStores', baseContext);

      const deleteTextResult = await boStoresPage.bulkDeleteStores(page);
      expect(deleteTextResult).to.be.contains(boStoresPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfStoresAfterReset = await boStoresPage.resetAndGetNumberOfLines(page);
      expect(numberOfStoresAfterReset).to.be.equal(numberOfStores);
    });
  });
});
