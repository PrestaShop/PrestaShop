import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';

import {
  boDashboardPage,
  boLoginPage,
  boMultistorePage,
  boMultistoreGroupCreatePage,
  boMultistoreShopPage,
  boMultistoreShopCreatePage,
  type BrowserContext,
  FakerShop,
  FakerShopGroup,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_multistore_CRUDShopGroups';

// Create, Read, Update and Delete shop groups in BO
describe('BO - Advanced Parameters - Multistore : Create, Read, Update and Delete shop groups in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let numberOfShopGroups: number = 0;
  let shopID: number = 0;

  const createShopGroupData: FakerShopGroup = new FakerShopGroup();
  const updateShopGroupData: FakerShopGroup = new FakerShopGroup();
  const shopData: FakerShop = new FakerShop({shopGroup: updateShopGroupData.name, categoryRoot: 'Home'});

  //Pre-condition: Enable multistore
  setMultiStoreStatus(true, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 2 : Create shop group
  describe('Create shop group', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.multistoreLink,
      );
      await boMultistorePage.closeSfToolBar(page);

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });

    it('should get number of shop group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfShopGroups', baseContext);

      numberOfShopGroups = await boMultistorePage.getNumberOfElementInGrid(page);
      expect(numberOfShopGroups).to.be.above(0);
    });

    it('should go to add new shop group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopGroupPage', baseContext);

      await boMultistorePage.goToNewShopGroupPage(page);

      const pageTitle = await boMultistoreGroupCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreGroupCreatePage.pageTitleCreate);
    });

    it('should create shop group and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createShopGroup', baseContext);

      const textResult = await boMultistoreGroupCreatePage.setShopGroup(page, createShopGroupData);
      expect(textResult).to.contains(boMultistoreGroupCreatePage.successfulCreationMessage);

      const numberOfShopGroupsAfterCreation = await boMultistorePage.getNumberOfElementInGrid(page);
      expect(numberOfShopGroupsAfterCreation).to.be.equal(numberOfShopGroups + 1);
    });
  });

  // 3 : Update shop group
  describe('Update shop group', async () => {
    it('should go to edit the created shop group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditShopGroupPage', baseContext);

      await boMultistorePage.filterTable(page, 'a!name', createShopGroupData.name);
      await boMultistorePage.gotoEditShopGroupPage(page, 1);

      const pageTitle = await boMultistoreGroupCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreGroupCreatePage.pageTitleEdit);
    });

    it('should edit shop group and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateShopGroup', baseContext);

      const textResult = await boMultistoreGroupCreatePage.setShopGroup(page, updateShopGroupData);
      expect(textResult).to.contains(boMultistoreGroupCreatePage.successfulUpdateMessage);

      const numberOfShopGroupsAfterUpdate = await boMultistorePage.resetAndGetNumberOfLines(page);
      expect(numberOfShopGroupsAfterUpdate).to.be.equal(numberOfShopGroups + 1);
    });
  });

  // 4 - Create shop related to the updated shop group
  describe('Create shop for the updated shop group', async () => {
    it('should go to add new shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopPage', baseContext);

      await boMultistorePage.goToNewShopPage(page);

      const pageTitle = await boMultistoreShopCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopCreatePage.pageTitleCreate);
    });

    it('should create shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createShop', baseContext);

      const textResult = await boMultistoreShopCreatePage.setShop(page, shopData);
      expect(textResult).to.contains(boMultistorePage.successfulCreationMessage);
    });

    it('should get the id of the new shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getShopID', baseContext);

      const numberOfShops = await boMultistoreShopPage.getNumberOfElementInGrid(page);
      expect(numberOfShops).to.be.above(0);

      shopID = parseInt(await boMultistoreShopPage.getTextColumn(page, 1, 'id_shop'), 10);
    });
  });

  // 5 : Check that we cannot delete a shop group that has a shop
  describe('Check that there is no delete button in the edited shop group', async () => {
    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePageToDeleteShopGroup', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.multistoreLink,
      );
      await boMultistorePage.closeSfToolBar(page);

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });

    it('should check that there is no delete button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoDeleteButton', baseContext);

      await boMultistorePage.filterTable(page, 'a!name', updateShopGroupData.name);

      const isVisible = await boMultistorePage.isActionToggleButtonVisible(page, 1);
      expect(isVisible).to.eq(false);
    });
  });

  // 6 : Delete the shop and the edited shop group
  describe('Delete shop then shop group', async () => {
    it('should go to the created shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedShopPage', baseContext);

      await boMultistorePage.goToShopPage(page, shopID);

      const pageTitle = await boMultistoreShopPage.getPageTitle(page);
      expect(pageTitle).to.contains(updateShopGroupData.name);
    });

    it('should delete the shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShop', baseContext);

      const numberOfShops = await boMultistoreShopPage.getNumberOfElementInGrid(page);
      expect(numberOfShops).to.be.above(1);

      await boMultistoreShopPage.filterTable(page, 'a!name', shopData.name);

      const textResult = await boMultistoreShopPage.deleteShop(page, 1);
      expect(textResult).to.contains(boMultistoreShopPage.successfulDeleteMessage);

      const numberOfShopsAfterDelete = await boMultistoreShopPage.resetAndGetNumberOfLines(page);
      expect(numberOfShopsAfterDelete).to.be.equal(1);
    });

    it('should go to \'Advanced parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePageToDeleteShopGroup2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.multistoreLink,
      );

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });

    it('should delete the shop group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteEditedSHopGroup', baseContext);

      await boMultistorePage.filterTable(page, 'a!name', updateShopGroupData.name);

      const textResult = await boMultistorePage.deleteShopGroup(page, 1);
      expect(textResult).to.contains(boMultistorePage.successfulDeleteMessage);

      const numberOfShopGroupsAfterDelete = await boMultistorePage.resetAndGetNumberOfLines(page);
      expect(numberOfShopGroupsAfterDelete).to.be.equal(numberOfShopGroups);
    });
  });

  // Post-condition : Disable multi store
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
