import testContext from '@utils/testContext';
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';
import {expect} from 'chai';

import {
  boCustomerGroupsPage,
  boCustomerGroupsCreatePage,
  boCustomerSettingsPage,
  boDashboardPage,
  boLoginPage,
  boMultistorePage,
  boMultistoreShopPage,
  boMultistoreShopCreatePage,
  boMultistoreShopUrlCreatePage,
  boPaymentPreferencesPage,
  type BrowserContext,
  FakerGroup,
  FakerShop,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_payment_preferences_groupRestrictionsMultistore';

/*
Issue #9858: In a multishop context, the "Payment > Preferences" group restrictions table must only list the
customer groups of the currently selected shop, not the groups of every shop.

Scenario:
- Enable multistore and create a second shop
- Create a customer group while the second shop is the active context (so the group belongs to that shop only)
- Check that the group is listed in the group restrictions table when the second shop is selected
- Check that the group is NOT listed when the default shop is selected (regression of #9858)
 */
describe('BO - Payment - Preferences : Group restrictions are filtered by shop', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let shopID: number = 0;
  let groupsInNewShop: string[] = [];

  const createShopData: FakerShop = new FakerShop({name: 'newShop', shopGroup: 'Default', categoryRoot: 'Home'});
  const groupData: FakerGroup = new FakerGroup();

  // Pre-condition: Enable multistore
  setMultiStoreStatus(true, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Create a second shop', async () => {
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

    it('should go to add new shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopPage', baseContext);

      await boMultistorePage.goToNewShopPage(page);

      const pageTitle = await boMultistoreShopCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopCreatePage.pageTitleCreate);
    });

    it('should create shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createShop', baseContext);

      const textResult = await boMultistoreShopCreatePage.setShop(page, createShopData);
      expect(textResult).to.contains(boMultistorePage.successfulCreationMessage);
    });

    it('should get the id of the new shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getShopID', baseContext);

      const numberOfShops = await boMultistoreShopPage.getNumberOfElementInGrid(page);
      expect(numberOfShops).to.be.above(0);

      shopID = parseInt(await boMultistoreShopPage.getTextColumn(page, 1, 'id_shop'), 10);
    });

    it('should go to add URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL', baseContext);

      await boMultistoreShopPage.filterTable(page, 'a!name', createShopData.name);
      await boMultistoreShopPage.goToSetURL(page, 1);

      const pageTitle = await boMultistoreShopUrlCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopUrlCreatePage.pageTitleCreate);
    });

    it('should set URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addURL', baseContext);

      const textResult = await boMultistoreShopUrlCreatePage.setVirtualUrl(page, createShopData.name);
      expect(textResult).to.contains(boMultistoreShopUrlCreatePage.successfulCreationMessage);
    });
  });

  describe('Create a customer group that belongs to the new shop only', async () => {
    it('should select the new shop in the multistore header', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectNewShop', baseContext);

      await boMultistoreShopUrlCreatePage.clickOnMultiStoreHeader(page);
      await boMultistoreShopUrlCreatePage.chooseShop(page, 2);

      const shopName = await boMultistoreShopUrlCreatePage.getShopName(page);
      expect(shopName).to.eq(createShopData.name);
    });

    it('should go to \'Shop Parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

      await boMultistoreShopUrlCreatePage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.customerSettingsLink,
      );
      await boCustomerSettingsPage.closeSfToolBar(page);

      const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
    });

    it('should go to \'Groups\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage', baseContext);

      await boCustomerSettingsPage.goToGroupsPage(page);

      const pageTitle = await boCustomerGroupsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsPage.pageTitle);
    });

    it('should go to add new group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewGroup', baseContext);

      await boCustomerGroupsPage.goToNewGroupPage(page);

      const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleCreate);
    });

    it('should create the group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createGroup', baseContext);

      const textResult = await boCustomerGroupsCreatePage.createEditGroup(page, groupData);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulCreationMessage);
    });
  });

  describe('Check that the group restrictions table is filtered by shop', async () => {
    it('should go to \'Payment > Preferences\' page (new shop selected)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPageNewShop', baseContext);

      await boCustomerGroupsPage.goToSubMenu(
        page,
        boCustomerGroupsPage.paymentParentLink,
        boCustomerGroupsPage.preferencesLink,
      );

      const pageTitle = await boPaymentPreferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boPaymentPreferencesPage.pageTitle);
    });

    it('should check that the created group is listed for the new shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGroupVisibleNewShop', baseContext);

      groupsInNewShop = await boPaymentPreferencesPage.getGroupRestrictionNames(page);
      expect(groupsInNewShop).to.include(groupData.name);
    });

    it('should select the default shop in the multistore header', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectDefaultShop', baseContext);

      await boPaymentPreferencesPage.clickOnMultiStoreHeader(page);
      await boPaymentPreferencesPage.chooseShop(page, 1);

      const shopName = await boPaymentPreferencesPage.getShopName(page);
      expect(shopName).to.eq(global.INSTALL.SHOP_NAME);
    });

    it('should go to \'Payment > Preferences\' page (default shop selected)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPageDefaultShop', baseContext);

      await boPaymentPreferencesPage.goToSubMenu(
        page,
        boPaymentPreferencesPage.paymentParentLink,
        boPaymentPreferencesPage.preferencesLink,
      );

      const pageTitle = await boPaymentPreferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boPaymentPreferencesPage.pageTitle);
    });

    it('should check that the created group is NOT listed for the default shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGroupHiddenDefaultShop', baseContext);

      const groupsInDefaultShop = await boPaymentPreferencesPage.getGroupRestrictionNames(page);
      // The group belongs to the new shop only, so it must not appear in the default shop context (issue #9858)
      expect(groupsInDefaultShop).to.not.include(groupData.name);
      // Sanity check: the default shop still has its own groups, but fewer than the new shop
      expect(groupsInDefaultShop.length).to.be.above(0);
      expect(groupsInDefaultShop.length).to.be.below(groupsInNewShop.length);
    });
  });

  describe('Delete the created group', async () => {
    it('should select the new shop in the multistore header', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectNewShopToDelete', baseContext);

      await boPaymentPreferencesPage.clickOnMultiStoreHeader(page);
      await boPaymentPreferencesPage.chooseShop(page, 2);

      const shopName = await boPaymentPreferencesPage.getShopName(page);
      expect(shopName).to.eq(createShopData.name);
    });

    it('should go to \'Shop Parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPageToDelete', baseContext);

      await boPaymentPreferencesPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.customerSettingsLink,
      );

      const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
    });

    it('should go to \'Groups\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPageToDelete', baseContext);

      await boCustomerSettingsPage.goToGroupsPage(page);

      const pageTitle = await boCustomerGroupsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsPage.pageTitle);
    });

    it('should filter the list by the created group name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boCustomerGroupsPage.resetFilter(page);
      await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', groupData.name);

      const textColumn = await boCustomerGroupsPage.getTextColumn(page, 1, 'b!name');
      expect(textColumn).to.contains(groupData.name);
    });

    it('should delete the group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteGroup', baseContext);

      const textResult = await boCustomerGroupsPage.deleteGroup(page, 1);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulDeleteMessage);
    });
  });

  describe('Delete the created shop', async () => {
    it('should select the default shop in the multistore header', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectDefaultShopBeforeDeleteShop', baseContext);

      await boCustomerGroupsPage.clickOnMultiStoreHeader(page);
      await boCustomerGroupsPage.chooseShop(page, 1);

      const shopName = await boCustomerGroupsPage.getShopName(page);
      expect(shopName).to.eq(global.INSTALL.SHOP_NAME);
    });

    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToMultiStorePage', baseContext);

      await boCustomerGroupsPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.multistoreLink,
      );
      await boMultistorePage.closeSfToolBar(page);

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });

    it('should go to the created shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedShopPage', baseContext);

      await boMultistorePage.goToShopPage(page, shopID);

      const pageTitle = await boMultistoreShopPage.getPageTitle(page);
      expect(pageTitle).to.contains(createShopData.name);
    });

    it('should delete the shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShop', baseContext);

      await boMultistoreShopPage.filterTable(page, 'a!name', createShopData.name);

      const textResult = await boMultistoreShopPage.deleteShop(page, 1);
      expect(textResult).to.contains(boMultistoreShopPage.successfulDeleteMessage);
    });
  });

  // Post-condition: Disable multistore
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
