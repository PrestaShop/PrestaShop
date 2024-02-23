// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
import psFacetedSearch from '@pages/BO/modules/psFacetedSearch';
import psFacetedSearchFilterTemplate from '@pages/BO/modules/psFacetedSearch/filterTemplate';
// Import FO pages
import {categoryPage as categoryPageFO} from '@pages/FO/classic/category';
import {homePage} from '@pages/FO/classic/home';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_facetedsearch_configuration_editTemplateProductStockFilter';

describe('Faceted search module - Edit template - Product stock filter', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile('module.zip');
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

  it(`should search the module ${Modules.psFacetedSearch.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psFacetedSearch);
    expect(isModuleVisible).to.be.eq(true);
  });

  it(`should go to the configuration page of the module '${Modules.psFacetedSearch.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

    await moduleManagerPage.goToConfigurationPage(page, Modules.psFacetedSearch.tag);

    const pageTitle = await psFacetedSearch.getPageSubtitle(page);
    expect(pageTitle).to.eq(psFacetedSearch.pageSubTitle);
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

      await psFacetedSearch.editFilterTemplate(page, 1);

      const pageTitle = await psFacetedSearchFilterTemplate.getPanelTitle(page);
      expect(pageTitle).to.eq(psFacetedSearchFilterTemplate.title);
    });

    it(
      `should ${test.filterStatus ? 'enable' : 'disable'} the filter "Product stock filter" `
      + `${test.filterType ? `with filter mode "${test.filterType}"` : ''}`,
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setProductStockFilter_${index}`, baseContext);

        await psFacetedSearchFilterTemplate.setTemplateFilterForm(
          page,
          'Product stock filter',
          test.filterStatus,
          test.filterType,
        );

        const textResult = await psFacetedSearchFilterTemplate.saveTemplate(page);
        expect(textResult).to.match(/× Your filter "[-A-Za-z0-9\s]+" was updated successfully./);
      });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop_${index}`, baseContext);

      page = await psFacetedSearch.viewMyShop(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.be.eq(true);
    });

    it('should check the "All products" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToAllProductsPage_${index}`, baseContext);

      await homePage.goToAllProductsBlockPage(page, 1);

      const isCategoryPageVisible = await categoryPageFO.isCategoryPage(page);
      expect(isCategoryPageVisible).to.be.eq(true);

      const hasSearchFilters = await categoryPageFO.hasSearchFilters(page);
      expect(hasSearchFilters).to.be.eq(test.expectedHasSearchFilters);

      const isSearchFilterRadio = await categoryPageFO.isSearchFilterRadio(page, 'availability');
      expect(isSearchFilterRadio).to.be.eq(test.expectedIsSearchFilterRadio);

      const isSearchFilterDropdown = await categoryPageFO.isSearchFilterDropdown(page, 'availability');
      expect(isSearchFilterDropdown).to.be.eq(test.expectedIsSearchFilterDropdown);

      const isSearchFiltersCheckbox = await categoryPageFO.isSearchFiltersCheckbox(page, 'availability');
      expect(isSearchFiltersCheckbox).to.be.eq(test.expectedIsSearchFilterCheckbox);
    });

    it('should close the page and return to the backOffice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `closePageFo_${index}`, baseContext);

      page = await categoryPageFO.closePage(browserContext, page, 0);

      const pageTitle = await psFacetedSearch.getPageSubtitle(page);
      expect(pageTitle).to.eq(psFacetedSearch.pageSubTitle);
    });
  });
});
