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

const baseContext: string = 'functional_BO_shopParameters_contact_stores_CRUDStores';

describe('BO - Shop Parameters - Contact : Create, update and delete Store in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfStores: number = 0;

  const createStoreData: FakerStore = new FakerStore();
  const editStoreData: FakerStore = new FakerStore();

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

  describe('Create store in BO', async () => {
    it('should go to add new store page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewStore', baseContext);

      await boStoresPage.goToNewStorePage(page);

      const pageTitle = await boStoresCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boStoresCreatePage.pageTitleCreate);
    });

    it('should create store and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStore', baseContext);

      const textResult = await boStoresCreatePage.createEditStore(page, createStoreData);
      expect(textResult).to.contains(boStoresPage.successfulCreationMessage);

      const numberOfStoresAfterCreation = await boStoresPage.getNumberOfElementInGrid(page);
      expect(numberOfStoresAfterCreation).to.be.equal(numberOfStores + 1);
    });
  });

  describe('Update store created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await boStoresPage.resetFilter(page);
      await boStoresPage.filterTable(page, 'input', 'sl!name', createStoreData.name);

      const textEmail = await boStoresPage.getTextColumn(page, 1, 'sl!name');
      expect(textEmail).to.contains(createStoreData.name);
    });

    it('should go to edit store page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditStorePage', baseContext);

      await boStoresPage.gotoEditStorePage(page, 1);

      const pageTitle = await boStoresCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boStoresCreatePage.pageTitleEdit);
    });

    it('should update store', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateStore', baseContext);

      const textResult = await boStoresCreatePage.createEditStore(page, editStoreData);
      expect(textResult).to.contains(boStoresPage.successfulUpdateMessage);

      const numberOfStoresAfterUpdate = await boStoresPage.resetAndGetNumberOfLines(page);
      expect(numberOfStoresAfterUpdate).to.be.equal(numberOfStores + 1);
    });
  });

  describe('Delete store', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await boStoresPage.resetFilter(page);
      await boStoresPage.filterTable(page, 'input', 'sl!name', editStoreData.name);

      const textEmail = await boStoresPage.getTextColumn(page, 1, 'sl!name');
      expect(textEmail).to.contains(editStoreData.name);
    });

    it('should delete store', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteStore', baseContext);

      const textResult = await boStoresPage.deleteStore(page, 1);
      expect(textResult).to.contains(boStoresPage.successfulDeleteMessage);

      const numberOfStoresAfterDelete = await boStoresPage.resetAndGetNumberOfLines(page);
      expect(numberOfStoresAfterDelete).to.be.equal(numberOfStores);
    });
  });
});
