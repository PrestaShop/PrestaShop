// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataCategories,
  dataModules,
  foClassicCategoryPage,
  foClassicHomePage,
  modPsFacetedsearchBoMain,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_facetedsearch_configuration_showProductsOnlyFromDefaultCategorysFieldConfiguration';

describe('Faceted search module: Show products only from default category\'s field configuration', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    await utilsFile.deleteFile('module.zip');
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
    expect(isModuleVisible).to.be.eq(true);
  });

  it(`should go to the configuration page of the module '${dataModules.psFacetedSearch.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

    await boModuleManagerPage.goToConfigurationPage(page, dataModules.psFacetedSearch.tag);

    const pageTitle = await modPsFacetedsearchBoMain.getPageSubtitle(page);
    expect(pageTitle).to.eq(modPsFacetedsearchBoMain.pageSubTitle);
  });

  it('should enable the switch "Show products only from default category"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableSwitch', baseContext);

    const textResult = await modPsFacetedsearchBoMain.setShowProductsOnlyFromDefaultCategoryValue(page, true);
    expect(textResult).to.equal(modPsFacetedsearchBoMain.settingsSavedMessage);

    const isShowProductsFromSubcategoriesChecked = await modPsFacetedsearchBoMain.isShowProductsFromSubcategoriesChecked(page);
    expect(isShowProductsFromSubcategoriesChecked).to.equal(false);

    const isShowProductsFromSubcategoriesDisabled = await modPsFacetedsearchBoMain.isShowProductsFromSubcategoriesDisabled(page);
    expect(isShowProductsFromSubcategoriesDisabled).to.equal(true);
  });

  it('should view my shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

    page = await modPsFacetedsearchBoMain.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should go to the All products page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAllProductsPage', baseContext);

    await foClassicHomePage.goToAllProductsPage(page);

    const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
    expect(isCategoryPageVisible).to.equal(true);

    const numBlockCategories = await foClassicCategoryPage.getNumContentCategories(page);
    expect(numBlockCategories).to.equal(dataCategories.home.children.length);

    const productsNum = await foClassicCategoryPage.getNumberOfProductsDisplayed(page);
    expect(productsNum).to.equal(0);
  });

  it(`should go to the ${dataCategories.art.name} category page`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategoryPage', baseContext);

    await foClassicCategoryPage.clickBlockCategory(page, dataCategories.art.name);

    const pageTitle = await foClassicHomePage.getPageTitle(page);
    expect(pageTitle).to.equal(dataCategories.art.name);

    const productsNum = await foClassicCategoryPage.getProductsNumber(page);
    expect(productsNum).to.equal(6);
  });

  it('should return to the backoffice', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'returnToTheBO', baseContext);

    page = await foClassicCategoryPage.changePage(browserContext, 0);

    const pageTitle = await modPsFacetedsearchBoMain.getPageSubtitle(page);
    expect(pageTitle).to.equal(modPsFacetedsearchBoMain.pageSubTitle);
  });

  it('should disable the switch "Show products only from default category"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableSwitch', baseContext);

    const textResult = await modPsFacetedsearchBoMain.setShowProductsOnlyFromDefaultCategoryValue(page, false);
    expect(textResult).to.equal(modPsFacetedsearchBoMain.settingsSavedMessage);

    const isShowProductsFromSubcategoriesChecked = await modPsFacetedsearchBoMain.isShowProductsFromSubcategoriesChecked(page);
    expect(isShowProductsFromSubcategoriesChecked).to.equal(false);

    const isShowProductsFromSubcategoriesDisabled = await modPsFacetedsearchBoMain.isShowProductsFromSubcategoriesDisabled(page);
    expect(isShowProductsFromSubcategoriesDisabled).to.equal(false);
  });

  it('should check the frontoffice', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkFrontOffice', baseContext);

    page = await foClassicHomePage.changePage(browserContext, 1);
    await foClassicHomePage.reloadPage(page);

    const productsNum = await foClassicCategoryPage.getNumberOfProductsDisplayed(page);
    expect(productsNum).to.equal(7);
  });
});
