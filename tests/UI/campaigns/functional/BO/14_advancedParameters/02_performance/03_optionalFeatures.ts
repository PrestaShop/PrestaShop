// Import utils
import testContext from '@utils/testContext';

import {
  boCustomerGroupsPage,
  boCustomerGroupsCreatePage,
  boCustomerSettingsPage,
  boDashboardPage,
  boLoginPage,
  boPerformancePage,
  boProductsPage,
  type BrowserContext,
  dataCustomers,
  dataGroups,
  dataProducts,
  FakerProduct,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_advancedParameters_performance_optionalFeatures';

describe('BO - Advanced Parameters - Performance : Optional features', async () => {
  const groupDiscount: number = 5;
  const productToDelete: FakerProduct[] = [
    dataProducts.demo_1,
    dataProducts.demo_3,
    dataProducts.demo_5,
    dataProducts.demo_6,
    dataProducts.demo_7,
    dataProducts.demo_8,
    dataProducts.demo_9,
    dataProducts.demo_10,
    dataProducts.demo_15,
    dataProducts.demo_16,
    dataProducts.demo_17,
  ];

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

  it('should go to \'Shop Parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.customerSettingsLink,
    );
    await boCustomerSettingsPage.closeSfToolBar(page);

    const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
  });

  it('should go to \'Groups\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage', baseContext);

    await boCustomerSettingsPage.goToGroupsPage(page);

    const pageTitle = await boCustomerGroupsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerGroupsPage.pageTitle);
  });

  it('should reset all filters and get number of groups in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    const numberOfGroups = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
    expect(numberOfGroups).to.be.above(0);
  });

  it(`should filter by '${dataGroups.customer.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByGroupName1', baseContext);

    await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', dataGroups.customer.name);

    const textColumn = await boCustomerGroupsPage.getTextColumn(page, 1, 'b!name');
    expect(textColumn).to.contains(dataGroups.customer.name);
  });

  it('should go to edit group page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditGroupPage1', baseContext);

    await boCustomerGroupsPage.gotoEditGroupPage(page, 1);

    const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleEdit);
  });

  it(`should update group with discount = ${groupDiscount}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setDiscount', baseContext);

    const textResult = await boCustomerGroupsCreatePage.setDiscount(page, groupDiscount);
    expect(textResult).to.contains(boCustomerGroupsPage.successfulUpdateMessage);
  });

  it('should view my shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

    page = await boCustomerGroupsPage.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it(`should search the product ${dataProducts.demo_6.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

    await foClassicHomePage.searchProduct(page, dataProducts.demo_6.name);

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
  });

  it('should go to the product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

    await foClassicSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_6.name);

    const productPrice = await foClassicProductPage.getProductPrice(page);
    expect(productPrice).to.contains(dataProducts.demo_6.combinations[0].price);
  });

  it('should check the product features list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'hasProductFeaturesList', baseContext);

    const hasProductFeaturesList = await foClassicProductPage.hasProductFeaturesList(page);
    expect(hasProductFeaturesList).to.equal(true);

    const productFeatures = await foClassicProductPage.getProductFeaturesList(page);
    expect(productFeatures).to.equal(
      `Data sheet ${dataProducts.demo_6.features[0].featureName} ${dataProducts.demo_6.features[0].preDefinedValue}`);
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

    page = await foClassicProductPage.changePage(browserContext, 1);
    await foClassicProductPage.goToLoginPage(page);

    const pageTitle = await foClassicLoginPage.getPageTitle(page);
    expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
  });

  it('should login on the Front Office', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginFrontOffice', baseContext);

    await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foClassicProductPage.isCustomerConnected(page);
    expect(isCustomerConnected).to.eq(true);
  });

  it('should check the product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductPage', baseContext);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_6.name);

    const productPrice = await foClassicProductPage.getProductPrice(page);
    const discountValue = utilsCore.percentage(dataProducts.demo_6.combinations[0].price, groupDiscount);
    expect(productPrice).to.contains((dataProducts.demo_6.combinations[0].price - discountValue).toFixed(2));
  });

  it('should go to \'Advanced Parameters > Performance\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPerformancePage', baseContext);

    page = await foClassicProductPage.changePage(browserContext, 0);
    await boCustomerGroupsPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.performanceLink,
    );

    const pageTitle = await boPerformancePage.getPageTitle(page);
    expect(pageTitle).to.contains(boPerformancePage.pageTitle);
  });

  it('should disable features', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableFeatures', baseContext);

    const result = await boPerformancePage.setFeatures(page, false);
    expect(result).to.contains(boPerformancePage.successUpdateMessage);
  });

  it('should check the product features list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'hasNotProductFeaturesList', baseContext);

    page = await boPerformancePage.changePage(browserContext, 1);
    await foClassicProductPage.reloadPage(page);

    const hasProductFeaturesList = await foClassicProductPage.hasProductFeaturesList(page);
    expect(hasProductFeaturesList).to.equal(false);
  });

  it('should disable Customer Groups', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableCustomerGroups', baseContext);

    page = await boPerformancePage.changePage(browserContext, 0);

    const result = await boPerformancePage.setCustomerGroups(page, false);
    expect(result).to.contains(boPerformancePage.successUpdateMessage);
  });

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await boPerformancePage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
    await boProductsPage.closeSfToolBar(page);

    const pageTitle = await boProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductsPage.pageTitle);
  });

  it('should select all the combination product and delete them', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteAllCombinationProducts', baseContext);

    const isBulkDeleteButtonEnabled = await boProductsPage.bulkSelectProducts(page, productToDelete);
    expect(isBulkDeleteButtonEnabled).to.eq(true);

    const clickBulkActionsMessage = await boProductsPage.clickOnBulkActionsProducts(page, 'delete');
    expect(clickBulkActionsMessage).to.equal(`Deleting ${productToDelete.length} products`);

    const bulkActionsMessage = await boProductsPage.bulkActionsProduct(page, 'delete');
    expect(bulkActionsMessage).to.equal(
      `Deleting ${productToDelete.length} / ${productToDelete.length} products`,
    );

    const isModalVisible = await boProductsPage.closeBulkActionsProgressModal(page, 'delete');
    expect(isModalVisible).to.eq(true);
  });

  it('should reset filter', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    const numberOfProductsAfterReset = await boProductsPage.resetAndGetNumberOfLines(page);
    expect(numberOfProductsAfterReset).to.equal(8);
  });

  it('should go to \'Advanced Parameters > Performance\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'returnToPerformancePage', baseContext);

    page = await foClassicProductPage.changePage(browserContext, 0);
    await boCustomerGroupsPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.performanceLink,
    );

    const pageTitle = await boPerformancePage.getPageTitle(page);
    expect(pageTitle).to.contains(boPerformancePage.pageTitle);
  });

  it('should disable combinations', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableFCombinations', baseContext);

    const result = await boPerformancePage.setCombinations(page, false);
    expect(result).to.contains(boPerformancePage.successUpdateMessage);
  });

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'returnToProductsPage', baseContext);

    await boPerformancePage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
    await boProductsPage.closeSfToolBar(page);

    const pageTitle = await boProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductsPage.pageTitle);
  });

  it('should click on \'New product\' button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

    await boProductsPage.clickOnNewProductButton(page);
    await boProductsPage.closeSfToolBar(page);

    const pageTitle = await boProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductsPage.pageTitle);
  });

  it('should check types of products', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkTypesOfProducts', baseContext);

    const hasProductTypeStandard = await boProductsPage.hasProductType(page, 'standard');
    expect(hasProductTypeStandard).to.equal(true);

    const hasProductTypePack = await boProductsPage.hasProductType(page, 'pack');
    expect(hasProductTypePack).to.equal(true);

    const hasProductTypeVirtual = await boProductsPage.hasProductType(page, 'virtual');
    expect(hasProductTypeVirtual).to.equal(true);

    const hasProductTypeCombinations = await boProductsPage.hasProductType(page, 'combinations');
    expect(hasProductTypeCombinations).to.equal(false);

    const isModalNotVisible = await boProductsPage.closeNewProductModal(page);
    expect(isModalNotVisible).to.be.equal(true);
  });

  // POST-TEST : Reset the "Optional features" configuration
  it('POST-TEST : should reset the "Optional features" configuration', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetOptionalFeatures', baseContext);

    await boProductsPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.performanceLink,
    );

    const pageTitle = await boPerformancePage.getPageTitle(page);
    expect(pageTitle).to.contains(boPerformancePage.pageTitle);

    const setCustomerGroupsMessage = await boPerformancePage.setCustomerGroups(page, true);
    expect(setCustomerGroupsMessage).to.contains(boPerformancePage.successUpdateMessage);

    const setCombinationsMessage = await boPerformancePage.setCombinations(page, true);
    expect(setCombinationsMessage).to.contains(boPerformancePage.successUpdateMessage);

    const setFeaturesMessage = await boPerformancePage.setFeatures(page, true);
    expect(setFeaturesMessage).to.contains(boPerformancePage.successUpdateMessage);
  });

  // POST-TEST : Reset the customer group discount
  it('POST-TEST : should reset the customer group discount', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetCustomerGroupDiscount', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.customerSettingsLink,
    );
    await boCustomerSettingsPage.closeSfToolBar(page);

    const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);

    await boCustomerSettingsPage.goToGroupsPage(page);

    const pageTitleGroups = await boCustomerGroupsPage.getPageTitle(page);
    expect(pageTitleGroups).to.contains(boCustomerGroupsPage.pageTitle);

    await boCustomerGroupsPage.gotoEditGroupPage(page, 1);

    const pageTitleEdit = await boCustomerGroupsCreatePage.getPageTitle(page);
    expect(pageTitleEdit).to.contains(boCustomerGroupsCreatePage.pageTitleEdit);

    const textResult = await boCustomerGroupsCreatePage.setDiscount(page, 0);
    expect(textResult).to.contains(boCustomerGroupsPage.successfulUpdateMessage);
  });
});
