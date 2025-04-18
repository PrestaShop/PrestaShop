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
  modPsFacetedsearchBoMain,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_facetedsearch_configuration_categoryFilterDepthFieldConfiguration';

describe('Faceted search module - Category filter depth field configuration', async () => {
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

  [
    {
      categoryFilterDepthValue: 1,
      numCheckboxCategories: 3,
    },
    {
      categoryFilterDepthValue: 0,
      numCheckboxCategories: 7,
    },
  ].forEach((test: {
    categoryFilterDepthValue: number,
    numCheckboxCategories: number,
  }, index: number) => {
    it(`should set the Category filter depth value : "${test.categoryFilterDepthValue}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `setCategoryFilterDepthValue_${index}`, baseContext);

      const textResult = await modPsFacetedsearchBoMain.setCategoryFilterDepthValue(
        page,
        test.categoryFilterDepthValue.toString(),
      );
      expect(textResult).to.equal(modPsFacetedsearchBoMain.settingsSavedMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop_${index}`, baseContext);

      page = await modPsFacetedsearchBoMain.viewMyShop(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.be.eq(true);
    });

    it('should check the "All products" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToAllProductsPage_${index}`, baseContext);

      await foClassicHomePage.goToAllProductsBlockPage(page, 1);

      const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible).to.be.eq(true);

      const hasSearchFilters = await foClassicCategoryPage.hasSearchFilters(page);
      expect(hasSearchFilters).to.be.eq(true);

      const numSearchFiltersCheckbox = await foClassicCategoryPage.getNumSearchFiltersCheckbox(page, 'category');
      expect(numSearchFiltersCheckbox).to.be.eq(test.numCheckboxCategories);
    });

    it('should close the page and return to the backOffice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `closePageFo_${index}`, baseContext);

      page = await foClassicCategoryPage.closePage(browserContext, page, 0);

      const pageTitle = await modPsFacetedsearchBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsFacetedsearchBoMain.pageSubTitle);
    });
  });

  [
    '-2',
    '2',
    'LLOI',
    '9223372036854775807',
    '1,5',
    '1 500',
  ].forEach((value: string, index: number) => {
    // @todo : https://github.com/PrestaShop/PrestaShop/issues/36438
    it(`should set the Category filter depth value : "${value}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `setCategoryFilterDepthValueError_${index}`, baseContext);

      this.skip();

      const textResult = await modPsFacetedsearchBoMain.setCategoryFilterDepthValue(page, value);
      expect(textResult).to.equal(modPsFacetedsearchBoMain.settingsErrorMessage);
    });
  });

  it('should set the Category filter depth value : "1L"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setCategoryFilterDepthValue_1L', baseContext);

    const textResult = await modPsFacetedsearchBoMain.setCategoryFilterDepthValue(page, '1L');
    expect(textResult).to.equal(modPsFacetedsearchBoMain.settingsSavedMessage);

    const value = await modPsFacetedsearchBoMain.getCategoryFilterDepthValue(page);
    expect(value).to.equal(1);
  });
});
