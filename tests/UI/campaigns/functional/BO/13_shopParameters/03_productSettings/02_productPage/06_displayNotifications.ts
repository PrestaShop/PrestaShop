// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boProductSettingsPage,
  type BrowserContext,
  dataProducts,
  foClassicHomePage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productPage_displayNotifications';

describe('BO - Shop Parameters - Product Settings : Display notifications', async () => {
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

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.productSettingsLink,
    );
    await boProductSettingsPage.closeSfToolBar(page);

    const pageTitle = await boProductSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);

    const isChecked = await boProductSettingsPage.isDisplayNotificationIfProductInCartChecked(page);
    expect(isChecked).to.equals(true);
  });

  it('should view my shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

    page = await boProductSettingsPage.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Home page was not opened').to.eq(true);
  });

  it('should search product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

    await foClassicHomePage.searchProduct(page, dataProducts.demo_11.name);

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
  });

  it('should add the product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await foClassicSearchResultsPage.goToProductPage(page, 1);
    // Add the product to the cart
    await foClassicProductPage.addProductToTheCart(page, 1, [], false);

    const notificationsNumber = await foClassicProductPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.equal(1);

    const hasNotificationMessage = await foClassicProductPage.hasNotificationMessage(page);
    expect(hasNotificationMessage).to.equals(false);
  });

  it('should check, on front office, the notification', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkFrontOfficeEnabked', baseContext);

    await foClassicSearchResultsPage.reloadPage(page);

    const hasNotificationMessage = await foClassicProductPage.hasNotificationMessage(page);
    expect(hasNotificationMessage).to.equals(true);

    const notificationMessage = await foClassicProductPage.getNotificationMessage(page);
    expect(notificationMessage).to.equals(foClassicProductPage.messageCartContainsAlreadyProducts);
  });

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableConfiguration', baseContext);

    page = await foClassicProductPage.changePage(browserContext, 0);

    const result = await boProductSettingsPage.setDisplayNotificationIfProductInCartStatus(page, false);
    expect(result).to.equals(boProductSettingsPage.successfulUpdateMessage);
  });

  it('should check, on front office, the notification', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkFrontOfficeDisabled', baseContext);

    page = await boProductSettingsPage.changePage(browserContext, 1);
    await foClassicProductPage.reloadPage(page);

    const hasNotificationMessage = await foClassicProductPage.hasNotificationMessage(page);
    expect(hasNotificationMessage).to.equals(false);
  });

  it('should reset the configuration', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetConfiguration', baseContext);

    page = await foClassicProductPage.changePage(browserContext, 0);

    const result = await boProductSettingsPage.setDisplayNotificationIfProductInCartStatus(page, true);
    expect(result).to.equals(boProductSettingsPage.successfulUpdateMessage);
  });
});
