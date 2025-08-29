// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataModules,
  foClassicCategoryPage,
  foClassicHomePage,
  modPsFacetedsearchBoFilterTemplate,
  modPsFacetedsearchBoMain,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_facetedsearch_installation_resetModule';

describe('Faceted search module - Reset module', async () => {
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

  it(`should search the module ${dataModules.psFacetedSearch.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psFacetedSearch);
    expect(isModuleVisible).to.eq(true);
  });

  it('should display the reset modal and cancel it', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModuleAndCancel', baseContext);

    const textResult = await boModuleManagerPage.setActionInModule(page, dataModules.psFacetedSearch, 'reset', true);
    expect(textResult).to.eq('');

    const isModuleVisible = await boModuleManagerPage.isModuleVisible(page, dataModules.psFacetedSearch);
    expect(isModuleVisible).to.eq(true);

    const isModalVisible = await boModuleManagerPage.isModalActionVisible(page, dataModules.psFacetedSearch, 'reset');
    expect(isModalVisible).to.eq(false);
  });

  it(`should go to the configuration page of the module '${dataModules.psFacetedSearch.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

    await boModuleManagerPage.goToConfigurationPage(page, dataModules.psFacetedSearch.tag);

    const pageTitle = await modPsFacetedsearchBoMain.getPageSubtitle(page);
    expect(pageTitle).to.eq(modPsFacetedsearchBoMain.pageSubTitle);
  });

  it('should edit the filter template', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editFilterTemplate', baseContext);

    await modPsFacetedsearchBoMain.editFilterTemplate(page, 1);

    const pageTitle = await modPsFacetedsearchBoFilterTemplate.getPanelTitle(page);
    expect(pageTitle).to.eq(modPsFacetedsearchBoFilterTemplate.title);
  });

  it('should disable the filter "Product price filter" ', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setProductPriceFilter', baseContext);

    await modPsFacetedsearchBoFilterTemplate.setTemplateFilterForm(page, 'Product price filter', false);

    const textResult = await modPsFacetedsearchBoFilterTemplate.saveTemplate(page);
    expect(textResult).to.match(/× Your filter "[-A-Za-z0-9\s]+" was updated successfully./);
  });

  it('should view my shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

    page = await modPsFacetedsearchBoMain.viewMyShop(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.be.eq(true);
  });

  it('should check the "All products" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAllProductsPage', baseContext);

    await foClassicHomePage.goToAllProductsBlockPage(page, 1);

    const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
    expect(isCategoryPageVisible).to.be.eq(true);

    const hasSearchFilters = await foClassicCategoryPage.hasSearchFilters(page);
    expect(hasSearchFilters).to.be.eq(true);

    const hasSearchFilterType = await foClassicCategoryPage.hasSearchFilterType(page, 'price', 'Price');
    expect(hasSearchFilterType).to.be.eq(false);
  });

  it('should close the page and return to the backOffice', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closePageFo', baseContext);

    page = await foClassicCategoryPage.closePage(browserContext, page, 0);

    const pageTitle = await modPsFacetedsearchBoMain.getPageSubtitle(page);
    expect(pageTitle).to.eq(modPsFacetedsearchBoMain.pageSubTitle);
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

  it(`should search the module ${dataModules.psFacetedSearch.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule1', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psFacetedSearch);
    expect(isModuleVisible).to.eq(true);
  });

  it('should reset the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

    const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psFacetedSearch, 'reset');
    expect(successMessage).to.eq(boModuleManagerPage.resetModuleSuccessMessage(dataModules.psFacetedSearch.tag));
  });

  it(`should go to the configuration page of the module '${dataModules.psFacetedSearch.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'returnToConfigurationPage', baseContext);

    await boModuleManagerPage.goToConfigurationPage(page, dataModules.psFacetedSearch.tag);

    const pageTitle = await modPsFacetedsearchBoMain.getPageSubtitle(page);
    expect(pageTitle).to.eq(modPsFacetedsearchBoMain.pageSubTitle);
  });

  it('should edit the filter template', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editFilterTemplate1', baseContext);

    await modPsFacetedsearchBoMain.editFilterTemplate(page, 1);

    const pageTitle = await modPsFacetedsearchBoFilterTemplate.getPanelTitle(page);
    expect(pageTitle).to.eq(modPsFacetedsearchBoFilterTemplate.title);
  });

  it('should check the filter "Product price filter" ', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getProductPriceFilter', baseContext);

    const isTemplateFilterEnabled = await modPsFacetedsearchBoFilterTemplate.isTemplateFilterEnabled(
      page,
      'Product price filter',
    );
    expect(isTemplateFilterEnabled).to.equal(true);
  });

  it('should view my shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

    page = await modPsFacetedsearchBoMain.viewMyShop(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.be.eq(true);
  });

  it('should check the "All products" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAllProductsPage1', baseContext);

    await foClassicHomePage.goToAllProductsBlockPage(page, 1);

    const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
    expect(isCategoryPageVisible).to.be.eq(true);

    const hasSearchFilters = await foClassicCategoryPage.hasSearchFilters(page);
    expect(hasSearchFilters).to.be.eq(true);

    const hasSearchFilterType = await foClassicCategoryPage.hasSearchFilterType(page, 'price', 'Price');
    expect(hasSearchFilterType).to.be.eq(true);
  });
});
