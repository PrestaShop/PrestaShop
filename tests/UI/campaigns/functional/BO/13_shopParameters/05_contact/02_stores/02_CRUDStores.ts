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
import StoreFaker from '@data/faker/store';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_contact_stores_CRUDStores';

describe('BO - Shop Parameters - Contact : Create, update and delete Store in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfStores: number = 0;

  const createStoreData: StoreFaker = new StoreFaker();
  const editStoreData: StoreFaker = new StoreFaker();

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
    expect(pageTitle).to.contains(contactPage.pageTitle);
  });

  it('should go to \'Stores\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStoresPage', baseContext);

    await contactPage.goToStoresPage(page);

    const pageTitle = await storesPage.getPageTitle(page);
    expect(pageTitle).to.contains(storesPage.pageTitle);
  });

  it('should reset all filters and get number of stores in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfStores = await storesPage.resetAndGetNumberOfLines(page);
    expect(numberOfStores).to.be.above(0);
  });

  describe('Create store in BO', async () => {
    it('should go to add new store page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewStore', baseContext);

      await storesPage.goToNewStorePage(page);

      const pageTitle = await addStorePage.getPageTitle(page);
      expect(pageTitle).to.contains(addStorePage.pageTitleCreate);
    });

    it('should create store and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStore', baseContext);

      const textResult = await addStorePage.createEditStore(page, createStoreData);
      expect(textResult).to.contains(storesPage.successfulCreationMessage);

      const numberOfStoresAfterCreation = await storesPage.getNumberOfElementInGrid(page);
      expect(numberOfStoresAfterCreation).to.be.equal(numberOfStores + 1);
    });
  });

  describe('Update store created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await storesPage.resetFilter(page);
      await storesPage.filterTable(page, 'input', 'sl!name', createStoreData.name);

      const textEmail = await storesPage.getTextColumn(page, 1, 'sl!name');
      expect(textEmail).to.contains(createStoreData.name);
    });

    it('should go to edit store page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditStorePage', baseContext);

      await storesPage.gotoEditStorePage(page, 1);

      const pageTitle = await addStorePage.getPageTitle(page);
      expect(pageTitle).to.contains(addStorePage.pageTitleEdit);
    });

    it('should update store', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateStore', baseContext);

      const textResult = await addStorePage.createEditStore(page, editStoreData);
      expect(textResult).to.contains(storesPage.successfulUpdateMessage);

      const numberOfStoresAfterUpdate = await storesPage.resetAndGetNumberOfLines(page);
      expect(numberOfStoresAfterUpdate).to.be.equal(numberOfStores + 1);
    });
  });

  describe('Delete store', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await storesPage.resetFilter(page);
      await storesPage.filterTable(page, 'input', 'sl!name', editStoreData.name);

      const textEmail = await storesPage.getTextColumn(page, 1, 'sl!name');
      expect(textEmail).to.contains(editStoreData.name);
    });

    it('should delete store', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteStore', baseContext);

      const textResult = await storesPage.deleteStore(page, 1);
      expect(textResult).to.contains(storesPage.successfulDeleteMessage);

      const numberOfStoresAfterDelete = await storesPage.resetAndGetNumberOfLines(page);
      expect(numberOfStoresAfterDelete).to.be.equal(numberOfStores);
    });
  });
});
