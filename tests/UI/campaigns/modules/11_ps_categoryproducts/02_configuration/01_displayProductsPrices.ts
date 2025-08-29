import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataModules,
  dataProducts,
  foClassicHomePage,
  foClassicProductPage,
  modPsCategoryProductsBoMain,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_categoryproducts_configuration_displayProductsPrices';

describe('Category products module - Display products\' prices', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPageForEnable', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.modulesParentLink,
      boDashboardPage.moduleManagerLink,
    );
    await boModuleManagerPage.closeSfToolBar(page);

    const pageTitle = await boModuleManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
  });

  it(`should search the module ${dataModules.psCategoryProducts.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModuleForDisable', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psCategoryProducts);
    expect(isModuleVisible).to.eq(true);
  });

  it(`should go to the configuration page of the module '${dataModules.psCategoryProducts.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

    await boModuleManagerPage.goToConfigurationPage(page, dataModules.psCategoryProducts.tag);

    const pageTitle = await modPsCategoryProductsBoMain.getPageSubtitle(page);
    expect(pageTitle).to.eq(modPsCategoryProductsBoMain.pageTitle);
  });

  it('should disable the Display products\' prices', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableDisplay', baseContext);

    const textResult = await modPsCategoryProductsBoMain.setDisplayProductsPriceStatus(page, false);
    expect(textResult).to.contains(modPsCategoryProductsBoMain.successfulUpdateMessage);
  });

  it('should go to the front office', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFOAfterDisable', baseContext);

    page = await boModuleManagerPage.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should go to the product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageAfterDisable', baseContext);

    await foClassicHomePage.goToProductPage(page, dataProducts.demo_6.id);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_6.name.toUpperCase());
  });

  it('should check if the price in the "Category Products" block is not visible', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNotVisible', baseContext);

    const hasProductsBlock = await foClassicProductPage.hasProductsBlock(page, 'categoryproducts');
    expect(hasProductsBlock).to.eq(true);

    const hasProductsBlockPrice = await foClassicProductPage.hasProductsBlockPrice(page, 'categoryproducts');
    expect(hasProductsBlockPrice).to.eq(false);
  });

  it('should enable the Display products\' prices', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableDisplay', baseContext);

    page = await foClassicProductPage.changePage(browserContext, 0);

    const textResult = await modPsCategoryProductsBoMain.setDisplayProductsPriceStatus(page, true);
    expect(textResult).to.contains(modPsCategoryProductsBoMain.successfulUpdateMessage);
  });

  it('should check if the price in the "Category Products" block is visible', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkVisible', baseContext);

    page = await modPsCategoryProductsBoMain.changePage(browserContext, 1);
    await foClassicProductPage.reloadPage(page);

    const hasProductsBlock = await foClassicProductPage.hasProductsBlock(page, 'categoryproducts');
    expect(hasProductsBlock).to.eq(true);

    const hasProductsBlockPrice = await foClassicProductPage.hasProductsBlockPrice(page, 'categoryproducts');
    expect(hasProductsBlockPrice).to.eq(true);
  });
});
