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
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_facetedsearch_configuration_editTemplateProductStockFilter';

describe('Faceted search module - Edit template - Product stock filter', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
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
      filterStatus: false,
      filterType: '',
      expectedHasSearchFilters: true,
      expectedIsSearchFilterRadio: false,
      expectedIsSearchFilterDropdown: false,
      expectedIsSearchFilterCheckbox: false,
    },
    {
      filterStatus: true,
      filterType: 'radio',
      expectedHasSearchFilters: true,
      expectedIsSearchFilterRadio: true,
      expectedIsSearchFilterDropdown: false,
      expectedIsSearchFilterCheckbox: false,
    },
    {
      filterStatus: true,
      filterType: 'dropdown',
      expectedHasSearchFilters: true,
      expectedIsSearchFilterRadio: false,
      expectedIsSearchFilterDropdown: true,
      expectedIsSearchFilterCheckbox: false,
    },
    {
      filterStatus: true,
      filterType: 'checkbox',
      expectedHasSearchFilters: true,
      expectedIsSearchFilterRadio: false,
      expectedIsSearchFilterDropdown: false,
      expectedIsSearchFilterCheckbox: true,
    },
  ].forEach((test: {
    filterStatus: boolean,
    filterType: string,
    expectedHasSearchFilters: boolean,
    expectedIsSearchFilterRadio: boolean,
    expectedIsSearchFilterDropdown: boolean,
    expectedIsSearchFilterCheckbox: boolean,
  }, index: number) => {
    it('should edit the filter template', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `editFilterTemplate_${index}`, baseContext);

      await modPsFacetedsearchBoMain.editFilterTemplate(page, 1);

      const pageTitle = await modPsFacetedsearchBoFilterTemplate.getPanelTitle(page);
      expect(pageTitle).to.eq(modPsFacetedsearchBoFilterTemplate.title);
    });

    it(
      `should ${test.filterStatus ? 'enable' : 'disable'} the filter "Product stock filter" `
      + `${test.filterType ? `with filter mode "${test.filterType}"` : ''}`,
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setProductStockFilter_${index}`, baseContext);

        await modPsFacetedsearchBoFilterTemplate.setTemplateFilterForm(
          page,
          'Product stock filter',
          test.filterStatus,
          test.filterType,
        );

        const textResult = await modPsFacetedsearchBoFilterTemplate.saveTemplate(page);
        expect(textResult).to.match(/× Your filter "[-A-Za-z0-9\s]+" was updated successfully./);
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
      expect(hasSearchFilters).to.be.eq(test.expectedHasSearchFilters);

      const isSearchFilterRadio = await foClassicCategoryPage.isSearchFilterRadio(page, 'availability');
      expect(isSearchFilterRadio).to.be.eq(test.expectedIsSearchFilterRadio);

      const isSearchFilterDropdown = await foClassicCategoryPage.isSearchFilterDropdown(page, 'availability');
      expect(isSearchFilterDropdown).to.be.eq(test.expectedIsSearchFilterDropdown);

      const isSearchFilterCheckbox = await foClassicCategoryPage.isSearchFilterCheckbox(page, 'availability');
      expect(isSearchFilterCheckbox).to.be.eq(test.expectedIsSearchFilterCheckbox);
    });

    it('should close the page and return to the backOffice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `closePageFo_${index}`, baseContext);

      page = await foClassicCategoryPage.closePage(browserContext, page, 0);

      const pageTitle = await modPsFacetedsearchBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsFacetedsearchBoMain.pageSubTitle);
    });
  });
});
