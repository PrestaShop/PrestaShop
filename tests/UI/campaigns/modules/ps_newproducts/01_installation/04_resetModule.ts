// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import FO pages
import {homePage} from '@pages/FO/classic/home';
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
import psNewProducts from '@pages/BO/modules/psNewProducts';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_newproducts_installation_resetModule';

describe('New products block module - Reset module', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let defaultValue: number;
  const numProducts: number = 10;

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

  it('should go to \'Modules > Module Manager\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.modulesParentLink,
      dashboardPage.moduleManagerLink,
    );
    await moduleManagerPage.closeSfToolBar(page);

    const pageTitle = await moduleManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
  });

  it(`should search the module ${Modules.psNewProducts.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psNewProducts);
    expect(isModuleVisible).to.eq(true);
  });

  it('should display the reset modal and cancel it', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModuleAndCancel', baseContext);

    const textResult = await moduleManagerPage.setActionInModule(page, Modules.psNewProducts, 'reset', true);
    expect(textResult).to.eq('');

    const isModuleVisible = await moduleManagerPage.isModuleVisible(page, Modules.psNewProducts);
    expect(isModuleVisible).to.eq(true);

    const isModalVisible = await moduleManagerPage.isModalActionVisible(page, Modules.psNewProducts, 'reset');
    expect(isModalVisible).to.eq(false);
  });

  it(`should go to the configuration page of the module '${Modules.psNewProducts.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

    await moduleManagerPage.goToConfigurationPage(page, Modules.psNewProducts.tag);

    const pageTitle = await psNewProducts.getPageSubtitle(page);
    expect(pageTitle).to.eq(psNewProducts.pageSubTitle);

    defaultValue = parseInt(await psNewProducts.getNumProductsToDisplay(page), 10);
    expect(defaultValue).to.be.gt(0);
  });

  it('should change the configuration in the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeConfiguration', baseContext);

    const textResult = await psNewProducts.setNumProductsToDisplay(page, numProducts);
    expect(textResult).to.contains(psNewProducts.updateSettingsSuccessMessage);
  });

  it('should go to the front office', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTheFo', baseContext);

    page = await psNewProducts.viewMyShop(page);
    await homePage.changeLanguage(page, 'en');

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should check the number of products in the "New Products" block', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

    const numProductsInBlock = await homePage.getProductsBlockNumber(page, 'newproducts');
    expect(numProductsInBlock).to.be.equal(numProducts);
  });

  it('should return to the back office', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'returnToBO', baseContext);

    page = await homePage.closePage(browserContext, page, 0);

    const pageTitle = await psNewProducts.getPageSubtitle(page);
    expect(pageTitle).to.eq(psNewProducts.pageSubTitle);
  });

  it('should return to \'Modules > Module Manager\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'returnToModuleManagerPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.modulesParentLink,
      dashboardPage.moduleManagerLink,
    );
    await moduleManagerPage.closeSfToolBar(page);

    const pageTitle = await moduleManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
  });

  it(`should search the module ${Modules.psNewProducts.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModuleForReset', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psNewProducts);
    expect(isModuleVisible).to.eq(true);
  });

  it('should reset the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

    const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psNewProducts, 'reset');
    expect(successMessage).to.eq(moduleManagerPage.resetModuleSuccessMessage(Modules.psNewProducts.tag));
  });

  it(`should go to the configuration page of the module '${Modules.psNewProducts.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPageAfterReset', baseContext);

    await moduleManagerPage.goToConfigurationPage(page, Modules.psNewProducts.tag);

    const pageTitle = await psNewProducts.getPageSubtitle(page);
    expect(pageTitle).to.eq(psNewProducts.pageSubTitle);
  });

  it('should check the configuration is reset', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeConfigurationReset', baseContext);

    const numProductsValue = await psNewProducts.getNumProductsToDisplay(page);
    expect(numProductsValue).to.be.equal(defaultValue.toString());
  });

  it('should go to the front office', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTheFoAfterReset', baseContext);

    page = await psNewProducts.viewMyShop(page);
    await homePage.changeLanguage(page, 'en');

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should check the number of products in the "New Products" block', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProductsAfterReset', baseContext);

    const numProductsInBlock = await homePage.getProductsBlockNumber(page, 'newproducts');
    expect(numProductsInBlock).to.be.equal(defaultValue);
  });
});
