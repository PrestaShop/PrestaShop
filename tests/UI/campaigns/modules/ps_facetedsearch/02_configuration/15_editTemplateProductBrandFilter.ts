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
import categoryPageFO from '@pages/FO/category';
import {homePage} from '@pages/FO/home';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_facetedsearch_configuration_editTemplateProductBrandFilter';

describe('Faceted search module - Edit template - Product brand filter', async () => {
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
    await expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
  });

  it(`should search the module ${Modules.psFacetedSearch.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psFacetedSearch);
    await expect(isModuleVisible).to.be.true;
  });

  it(`should go to the configuration page of the module '${Modules.psFacetedSearch.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

    await moduleManagerPage.goToConfigurationPage(page, Modules.psFacetedSearch.tag);

    const pageTitle = await psFacetedSearch.getPageSubtitle(page);
    await expect(pageTitle).to.eq(psFacetedSearch.pageSubTitle);
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
      await expect(pageTitle).to.eq(psFacetedSearchFilterTemplate.title);
    });

    it(
      `should ${test.filterStatus ? 'enable' : 'disable'} the filter "Product brand filter" `
      + `${test.filterType ? `with filter mode "${test.filterType}"` : ''}`,
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setProductBrandFilter_${index}`, baseContext);

        const textResult = await psFacetedSearchFilterTemplate.setFilterForm(
          page,
          'Product brand filter',
          test.filterStatus,
          test.filterType,
        );
        await expect(textResult).to.match(/× Your filter "[-A-Za-z0-9\s]+" was updated successfully./);
      });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop_${index}`, baseContext);

      page = await psFacetedSearch.viewMyShop(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should check the "All products" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToAllProductsPage_${index}`, baseContext);

      await homePage.goToAllProductsBlockPage(page, 1);

      const isCategoryPageVisible = await categoryPageFO.isCategoryPage(page);
      await expect(isCategoryPageVisible).to.be.true;

      const hasSearchFilters = await categoryPageFO.hasSearchFilters(page);
      await expect(hasSearchFilters).to.be.eq(test.expectedHasSearchFilters);

      const isSearchFilterRadio = await categoryPageFO.isSearchFilterRadio(page, 'manufacturer');
      await expect(isSearchFilterRadio).to.be.eq(test.expectedIsSearchFilterRadio);

      const isSearchFilterDropdown = await categoryPageFO.isSearchFilterDropdown(page, 'manufacturer');
      await expect(isSearchFilterDropdown).to.be.eq(test.expectedIsSearchFilterDropdown);

      const isSearchFiltersCheckbox = await categoryPageFO.isSearchFiltersCheckbox(page, 'manufacturer');
      await expect(isSearchFiltersCheckbox).to.be.eq(test.expectedIsSearchFilterCheckbox);
    });

    it('should close the page and return to the backOffice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `closePageFo_${index}`, baseContext);

      page = await categoryPageFO.closePage(browserContext, page, 0);

      const pageTitle = await psFacetedSearch.getPageSubtitle(page);
      await expect(pageTitle).to.eq(psFacetedSearch.pageSubTitle);
    });
  });
});
