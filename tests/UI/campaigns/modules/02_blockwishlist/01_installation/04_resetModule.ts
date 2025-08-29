// Import utils
import testContext from '@utils/testContext';

import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataCustomers,
  dataModules,
  dataProducts,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicModalWishlistPage,
  foClassicMyWishlistsViewPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  modBlockwishlistBoMain,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'modules_blockwishlist_installation_resetModule';

describe('Wishlist module - Reset module', async () => {
  const labelButton: string = 'Test Label Button';

  let browserContext: BrowserContext;
  let page: Page;

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

  it('should go to \'Modules > Module Manager\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.modulesParentLink,
      boDashboardPage.moduleManagerLink,
    );
    await boModuleManagerPage.closeSfToolBar(page);

    const pageTitle = await boModuleManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
  });

  it(`should search the module ${dataModules.blockwishlist.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.blockwishlist);
    expect(isModuleVisible).to.eq(true);
  });

  it('should display the reset modal and cancel it', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModuleAndCancel', baseContext);

    const textResult = await boModuleManagerPage.setActionInModule(page, dataModules.blockwishlist, 'reset', true);
    expect(textResult).to.eq('');

    const isModuleVisible = await boModuleManagerPage.isModuleVisible(page, dataModules.blockwishlist);
    expect(isModuleVisible).to.eq(true);

    const isModalVisible = await boModuleManagerPage.isModalActionVisible(page, dataModules.blockwishlist, 'reset');
    expect(isModalVisible).to.eq(false);
  });

  it(`should go to the configuration page of the module '${dataModules.blockwishlist.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

    await boModuleManagerPage.goToConfigurationPage(page, dataModules.blockwishlist.tag);

    const pageTitle = await modBlockwishlistBoMain.getPageTitle(page);
    expect(pageTitle).to.eq(modBlockwishlistBoMain.pageTitle);

    const isConfigurationTabActive = await modBlockwishlistBoMain.isTabActive(page, 'Configuration');
    expect(isConfigurationTabActive).to.eq(true);

    const wishlistDefaultTitle = await modBlockwishlistBoMain.getInputValue(page, 'wishlistDefaultTitle');
    expect(wishlistDefaultTitle).to.equals(modBlockwishlistBoMain.defaultValueWishlistDefaultTitle);

    const createButtonLabel = await modBlockwishlistBoMain.getInputValue(page, 'createButtonLabel');
    expect(createButtonLabel).to.equals(modBlockwishlistBoMain.defaultValueCreateButtonLabel);

    const wishlistPageName = await modBlockwishlistBoMain.getInputValue(page, 'wishlistPageName');
    expect(wishlistPageName).to.equals(modBlockwishlistBoMain.defaultValueWishlistPageName);
  });

  it('should update the label', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateLabel', baseContext);

    const textResult = await modBlockwishlistBoMain.setFormWording(page, undefined, labelButton);
    expect(textResult).to.contains(modBlockwishlistBoMain.successfulUpdateMessage);
  });

  it('should go to Front Office', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    page = await modBlockwishlistBoMain.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

    await foClassicHomePage.goToLoginPage(page);

    const pageTitle = await foClassicLoginPage.getPageTitle(page);
    expect(pageTitle).to.contains(foClassicLoginPage.pageTitle);
  });

  it('should sign in with default customer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

    await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected).to.eq(true);
  });

  it(`should search the product ${dataProducts.demo_3.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchProductDemo3', baseContext);

    await foClassicMyWishlistsViewPage.searchProduct(page, dataProducts.demo_3.name);
    await foClassicSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).to.equal(dataProducts.demo_3.name);
  });

  it('should add to the wishlist and get the label', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addToWishlist1', baseContext);

    await foClassicProductPage.clickAddToWishlistButton(page);

    const textResult = await foClassicModalWishlistPage.getModalAddToCreateWislistLabel(page);
    expect(textResult).to.contains(labelButton);
  });

  it('should go to \'Modules > Module Manager\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPageForReset', baseContext);

    page = await foClassicModalWishlistPage.changePage(browserContext, 0);
    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.modulesParentLink,
      boDashboardPage.moduleManagerLink,
    );
    await boModuleManagerPage.closeSfToolBar(page);

    const pageTitle = await boModuleManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
  });

  it(`should search the module ${dataModules.blockwishlist.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModuleForReset', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.blockwishlist);
    expect(isModuleVisible).to.eq(true);
  });

  it('should reset the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

    const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.blockwishlist, 'reset');
    expect(successMessage).to.eq(boModuleManagerPage.resetModuleSuccessMessage(dataModules.blockwishlist.tag));
  });

  it('should add to the wishlist and select the first wishlist', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addToWishlist2', baseContext);

    page = await boModuleManagerPage.changePage(browserContext, 1);
    await foClassicProductPage.reloadPage(page);
    await foClassicProductPage.clickAddToWishlistButton(page);

    const textResult = await foClassicModalWishlistPage.getModalAddToCreateWislistLabel(page);
    expect(textResult).to.contains(modBlockwishlistBoMain.defaultValueCreateButtonLabel);
  });
});
