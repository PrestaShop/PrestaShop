// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataModules,
  foClassicHomePage,
  modPsNewProductsBoMain,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_newproducts_installation_resetModule';

describe('New products block module - Reset module', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let defaultValue: number;
  const numProducts: number = 10;

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

  it(`should search the module ${dataModules.psNewProducts.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psNewProducts);
    expect(isModuleVisible).to.eq(true);
  });

  it('should display the reset modal and cancel it', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModuleAndCancel', baseContext);

    const textResult = await boModuleManagerPage.setActionInModule(page, dataModules.psNewProducts, 'reset', true);
    expect(textResult).to.eq('');

    const isModuleVisible = await boModuleManagerPage.isModuleVisible(page, dataModules.psNewProducts);
    expect(isModuleVisible).to.eq(true);

    const isModalVisible = await boModuleManagerPage.isModalActionVisible(page, dataModules.psNewProducts, 'reset');
    expect(isModalVisible).to.eq(false);
  });

  it(`should go to the configuration page of the module '${dataModules.psNewProducts.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

    await boModuleManagerPage.goToConfigurationPage(page, dataModules.psNewProducts.tag);

    const pageTitle = await modPsNewProductsBoMain.getPageSubtitle(page);
    expect(pageTitle).to.eq(modPsNewProductsBoMain.pageSubTitle);

    defaultValue = parseInt(await modPsNewProductsBoMain.getNumProductsToDisplay(page), 10);
    expect(defaultValue).to.be.gt(0);
  });

  it('should change the configuration in the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeConfiguration', baseContext);

    const textResult = await modPsNewProductsBoMain.setNumProductsToDisplay(page, numProducts);
    expect(textResult).to.contains(modPsNewProductsBoMain.updateSettingsSuccessMessage);
  });

  it('should go to the front office', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTheFo', baseContext);

    page = await modPsNewProductsBoMain.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should check the number of products in the "New Products" block', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

    const numProductsInBlock = await foClassicHomePage.getProductsBlockNumber(page, 'newproducts');
    expect(numProductsInBlock).to.be.equal(numProducts);
  });

  it('should return to the back office', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'returnToBO', baseContext);

    page = await foClassicHomePage.closePage(browserContext, page, 0);

    const pageTitle = await modPsNewProductsBoMain.getPageSubtitle(page);
    expect(pageTitle).to.eq(modPsNewProductsBoMain.pageSubTitle);
  });

  it('should return to \'Modules > Module Manager\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'returnToModuleManagerPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.modulesParentLink,
      boDashboardPage.moduleManagerLink,
    );
    await boModuleManagerPage.closeSfToolBar(page);

    const pageTitle = await boModuleManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
  });

  it(`should search the module ${dataModules.psNewProducts.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModuleForReset', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psNewProducts);
    expect(isModuleVisible).to.eq(true);
  });

  it('should reset the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

    const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psNewProducts, 'reset');
    expect(successMessage).to.eq(boModuleManagerPage.resetModuleSuccessMessage(dataModules.psNewProducts.tag));
  });

  it(`should go to the configuration page of the module '${dataModules.psNewProducts.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPageAfterReset', baseContext);

    await boModuleManagerPage.goToConfigurationPage(page, dataModules.psNewProducts.tag);

    const pageTitle = await modPsNewProductsBoMain.getPageSubtitle(page);
    expect(pageTitle).to.eq(modPsNewProductsBoMain.pageSubTitle);
  });

  it('should check the configuration is reset', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeConfigurationReset', baseContext);

    const numProductsValue = await modPsNewProductsBoMain.getNumProductsToDisplay(page);
    expect(numProductsValue).to.be.equal(defaultValue.toString());
  });

  it('should go to the front office', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTheFoAfterReset', baseContext);

    page = await modPsNewProductsBoMain.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should check the number of products in the "New Products" block', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProductsAfterReset', baseContext);

    const numProductsInBlock = await foClassicHomePage.getProductsBlockNumber(page, 'newproducts');
    expect(numProductsInBlock).to.be.equal(defaultValue);
  });
});
