import {expect} from 'chai';
import testContext from '@utils/testContext';

// Import pages
import titlesPage from '@pages/BO/shopParameters/customerSettings/titles';
import addTitlePage from '@pages/BO/shopParameters/customerSettings/titles/add';
import addContactPage from '@pages/BO/shopParameters/contact/add';
import seoAndUrlsPage from '@pages/BO/shopParameters/trafficAndSeo/seoAndUrls';
import addSeoAndUrlPage from '@pages/BO/shopParameters/trafficAndSeo/seoAndUrls/add';
import searchEnginesPage from '@pages/BO/shopParameters/trafficAndSeo/searchEngines';
import addSearchEnginePage from '@pages/BO/shopParameters/trafficAndSeo/searchEngines/add';
import tagsPage from '@pages/BO/shopParameters/search/tags';
import addTagPage from '@pages/BO/shopParameters/search/tags/add';

import {
  boContactsPage,
  boCustomerSettingsPage,
  boCustomerGroupsPage,
  boCustomerGroupsCreatePage,
  boDashboardPage,
  boLoginPage,
  boShopParametersPage,
  boMaintenancePage,
  boOrderSettingsPage,
  boOrderStatusesPage,
  boOrderStatusesCreatePage,
  boProductSettingsPage,
  boReturnStatusesCreatePage,
  boSearchPage,
  boSearchAliasPage,
  boSearchAliasCreatePage,
  boStoresPage,
  boStoresCreatePage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'audit_BO_shopParameters';

describe('BO - Shop Parameters', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    utilsPlaywright.setErrorsCaptured(true);

    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  beforeEach(async () => {
    utilsPlaywright.resetJsErrors();
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop parameters > General\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToGeneralPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.shopParametersGeneralLink,
    );
    await boShopParametersPage.closeSfToolBar(page);

    const pageTitle = await boShopParametersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boShopParametersPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop parameters > General > Maintenance\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToMaintenancePage', baseContext);

    await boShopParametersPage.goToSubTabMaintenance(page);

    const pageTitle = await boMaintenancePage.getPageTitle(page);
    expect(pageTitle).to.contains(boMaintenancePage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Order Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.orderSettingsLink,
    );
    await boOrderSettingsPage.closeSfToolBar(page);

    const pageTitle = await boOrderSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Order Settings > Statuses\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStatusesPage', baseContext);

    await boOrderSettingsPage.goToStatusesPage(page);

    const pageTitle = await boOrderStatusesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrderStatusesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Order Settings > Statuses > New Order status\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddOrderStatusPage', baseContext);

    await boOrderStatusesPage.goToNewOrderStatusPage(page);

    const pageTitle = await boOrderStatusesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrderStatusesCreatePage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Order Settings > Statuses > Edit Order status\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditOrderStatusPage', baseContext);

    await boOrderSettingsPage.goToStatusesPage(page);
    await boOrderStatusesPage.goToEditPage(page, 'order', 1);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Order Settings > Statuses > New Order return status\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddOrderReturnStatusPage', baseContext);

    await boOrderSettingsPage.goToStatusesPage(page);
    await boOrderStatusesPage.goToNewOrderReturnStatusPage(page);

    const pageTitle = await boReturnStatusesCreatePage.getPageTitle(page);
    expect(pageTitle).to.eq(boReturnStatusesCreatePage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Order Settings > Statuses > Edit Order return status\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditOrderReturnStatusPage', baseContext);

    const tableName: string = 'order_return';

    await boOrderSettingsPage.goToStatusesPage(page);
    await boOrderStatusesPage.goToEditPage(page, tableName, 1);

    const pageTitle = await boReturnStatusesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boReturnStatusesCreatePage.pageTitleEdit('Waiting for confirmation'));

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.productSettingsLink,
    );
    await boProductSettingsPage.closeSfToolBar(page);

    const pageTitle = await boProductSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.customerSettingsLink,
    );
    await boCustomerSettingsPage.closeSfToolBar(page);

    const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop parameters > Customer Settings > Groups\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage', baseContext);

    await boCustomerSettingsPage.goToGroupsPage(page);

    const pageTitle = await boCustomerGroupsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerGroupsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop parameters > Customer Settings > Groups > New Group\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewGroup', baseContext);

    await boCustomerGroupsPage.goToNewGroupPage(page);

    const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop parameters > Customer Settings > Groups > Edit Group\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditGroupPage', baseContext);

    await boCustomerSettingsPage.goToGroupsPage(page);
    await boCustomerGroupsPage.gotoEditGroupPage(page, 1);

    const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleEdit);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop parameters > Customer Settings > Titles\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTitlesPage', baseContext);

    await boCustomerSettingsPage.goToTitlesPage(page);

    const pageTitle = await titlesPage.getPageTitle(page);
    expect(pageTitle).to.contains(titlesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop parameters > Customer Settings > Titles > New Title\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewTitle', baseContext);

    await titlesPage.goToAddNewTitle(page);

    const pageTitle = await addTitlePage.getPageTitle(page);
    expect(pageTitle).to.eq(addTitlePage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('sshould go to \'Shop parameters > Customer Settings > Titles > Edit Title\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditTitlePage', baseContext);

    await boCustomerSettingsPage.goToTitlesPage(page);
    await titlesPage.gotoEditTitlePage(page, 1);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop parameters > Contact\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToContactsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.contactLink,
    );
    await boContactsPage.closeSfToolBar(page);

    const pageTitle = await boContactsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boContactsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop parameters > Contacts > New Contact\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewContact', baseContext);

    await boContactsPage.goToAddNewContactPage(page);

    const pageTitle = await addContactPage.getPageTitle(page);
    expect(pageTitle).to.contains(addContactPage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop parameters > Contacts > Edit Contact\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditContactPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.contactLink,
    );

    await boContactsPage.goToEditContactPage(page, 1);

    const pageTitle = await addContactPage.getPageTitle(page);
    expect(pageTitle).to.contains(addContactPage.pageTitleEdit);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop parameters > Contacts > Stores\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStoresPage', baseContext);

    await boContactsPage.goToStoresPage(page);

    const pageTitle = await boStoresPage.getPageTitle(page);
    expect(pageTitle).to.contains(boStoresPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop parameters > Contacts > Stores > New Store\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewStore', baseContext);

    await boStoresPage.goToNewStorePage(page);

    const pageTitle = await boStoresCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boStoresCreatePage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop parameters > Contacts > Stores > Edit Store\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditStorePage', baseContext);

    await boContactsPage.goToStoresPage(page);
    await boStoresPage.gotoEditStorePage(page, 1);

    const pageTitle = await boStoresCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boStoresCreatePage.pageTitleEdit);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Traffic & SEO\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSeoAndUrlsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.trafficAndSeoLink,
    );
    await seoAndUrlsPage.closeSfToolBar(page);

    const pageTitle = await seoAndUrlsPage.getPageTitle(page);
    expect(pageTitle).to.contains(seoAndUrlsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Traffic & SEO > New page\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToNewSeoPage', baseContext);

    await seoAndUrlsPage.goToNewSeoUrlPage(page);

    const pageTitle = await addSeoAndUrlPage.getPageTitle(page);
    expect(pageTitle).to.contains(addSeoAndUrlPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Traffic & SEO > Edit page\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditSeoPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.trafficAndSeoLink,
    );
    await seoAndUrlsPage.closeSfToolBar(page);

    await seoAndUrlsPage.goToEditSeoUrlPage(page, 1);

    const pageTitle = await addSeoAndUrlPage.getPageTitle(page);
    expect(pageTitle).to.contains(addSeoAndUrlPage.editPageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Traffic & SEO > Search Engines\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchEnginesPage', baseContext);

    await seoAndUrlsPage.goToSearchEnginesPage(page);

    const pageTitle = await searchEnginesPage.getPageTitle(page);
    expect(pageTitle).to.contain(searchEnginesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Traffic & SEO > Search Engines > New Search engine\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToNewSearchEnginePage', baseContext);

    await searchEnginesPage.goToNewSearchEnginePage(page);

    const pageTitle = await addSearchEnginePage.getPageTitle(page);
    expect(pageTitle).to.contain(addSearchEnginePage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Traffic & SEO > Search Engines > Edit Search engine\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditSearchEngine', baseContext);

    await seoAndUrlsPage.goToSearchEnginesPage(page);
    await searchEnginesPage.goToEditSearchEnginePage(page, 1);

    const pageTitle = await addSearchEnginePage.getPageTitle(page);
    expect(pageTitle).to.contain(addSearchEnginePage.pageTitleEdit);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Search\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.searchLink,
    );

    const pageTitle = await boSearchPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSearchPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Search > Aliases\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAliasesTab', baseContext);

    await boSearchPage.goToAliasesPage(page);

    const pageTitle = await boSearchAliasPage.getPageTitle(page);
    expect(pageTitle).to.equals(boSearchAliasPage.pageTitle);
  });

  it('should go to \'Shop Parameters > Search > Aliases > New alias\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewAliasPage', baseContext);

    await boSearchAliasPage.goToAddNewAliasPage(page);

    const pageTitle = await boSearchAliasCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boSearchAliasCreatePage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Search > Aliases > Edit alias\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditAliasPage', baseContext);

    await boSearchPage.goToAliasesPage(page);
    await boSearchAliasPage.gotoEditAliasPage(page, 1);

    const pageTitle = await boSearchAliasCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boSearchAliasCreatePage.pageTitleEdit);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Search > Tags\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTagsPage', baseContext);

    await boSearchPage.goToTagsPage(page);

    const pageTitle = await tagsPage.getPageTitle(page);
    expect(pageTitle).to.contains(tagsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Shop Parameters > Search > Tags > New Tag\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddTagPage', baseContext);

    await tagsPage.goToAddNewTagPage(page);

    const pageTitle = await addTagPage.getPageTitle(page);
    expect(pageTitle).to.contains(addTagPage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });
});
