// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boMaintenancePage,
  boShopParametersPage,
  type BrowserContext,
  foClassicHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_general_maintenance_enableDisableShop';

/*
Disable shop
Update maintenance text
Update ip address in maintenance
Enable shop
 */
describe('BO - Shop Parameters - General - Maintenance : Enable/Disable shop', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const newMaintenanceText: string = 'Maintenance';

  // before and after functions
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

  it('should go to \'Shop parameters > General\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShopParamsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.shopParametersGeneralLink,
    );
    await boShopParametersPage.closeSfToolBar(page);

    const pageTitle = await boShopParametersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
  });

  it('should go to \'Maintenance\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToMaintenancePage', baseContext);

    await boShopParametersPage.goToSubTabMaintenance(page);

    const pageTitle = await boMaintenancePage.getPageTitle(page);
    expect(pageTitle).to.contains(boMaintenancePage.pageTitle);
  });

  it('should disable the shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableShop', baseContext);

    const result = await boMaintenancePage.changeShopStatus(page, false);
    expect(result).to.contains(boMaintenancePage.successfulUpdateMessage);
  });

  it('should enable store for logged-in employees', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableStoreForLoggedInEmployees', baseContext);

    const result = await boMaintenancePage.changeStoreForLoggedInEmployees(page, true);
    expect(result).to.contains(boMaintenancePage.successfulUpdateMessage);
  });

  it('should verify that the shop is enabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyEnabledShop0', baseContext);

    page = await boMaintenancePage.viewMyShop(page);

    const pageContent = await foClassicHomePage.getTextContent(page, foClassicHomePage.content);
    expect(pageContent).to.not.equal(boMaintenancePage.maintenanceText);

    const result = await foClassicHomePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should disable store for logged-in employees', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableStoreForLoggedInEmployees', baseContext);

    // Go back to BO
    page = await foClassicHomePage.closePage(browserContext, page, 0);

    const result = await boMaintenancePage.changeStoreForLoggedInEmployees(page, false);
    expect(result).to.contains(boMaintenancePage.successfulUpdateMessage);
  });

  it('should verify the existence of the maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyMaintenanceText', baseContext);

    page = await boMaintenancePage.viewMyShop(page);

    const pageContent = await foClassicHomePage.getTextContent(page, foClassicHomePage.content);
    expect(pageContent).to.equal(boMaintenancePage.maintenanceText);
  });

  it('should update the maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateMaintenanceText', baseContext);

    // Go back to BO
    page = await foClassicHomePage.closePage(browserContext, page, 0);

    const result = await boMaintenancePage.changeMaintenanceTextShopStatus(page, newMaintenanceText);
    expect(result).to.contains(boMaintenancePage.successfulUpdateMessage);
  });

  it('should verify that the maintenance text is updated successfully', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyNewMaintenanceText', baseContext);

    page = await boMaintenancePage.viewMyShop(page);

    const pageContent = await foClassicHomePage.getTextContent(page, foClassicHomePage.content);
    expect(pageContent).to.equal(newMaintenanceText);
  });

  it('should go back to the default maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'backToDefaultMaintenanceText', baseContext);

    page = await foClassicHomePage.closePage(browserContext, page, 0);

    const result = await boMaintenancePage.changeMaintenanceTextShopStatus(
      page,
      boMaintenancePage.maintenanceText,
    );
    expect(result).to.contains(boMaintenancePage.successfulUpdateMessage);
  });

  it('should add my IP address in Maintenance ip input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addMyIpAddress', baseContext);

    const result = await boMaintenancePage.addMyIpAddress(page);
    expect(result).to.contains(boMaintenancePage.successfulUpdateMessage);
  });

  it('should verify that the Home page is displayed successfully', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyFOHomePage', baseContext);

    page = await boMaintenancePage.viewMyShop(page);

    const pageContent = await foClassicHomePage.getTextContent(page, foClassicHomePage.content);
    expect(pageContent).to.not.equal(boMaintenancePage.maintenanceText);

    const result = await foClassicHomePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should delete the maintenance ip address', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteIpAddress', baseContext);

    page = await foClassicHomePage.closePage(browserContext, page, 0);

    const result = await boMaintenancePage.addMaintenanceIPAddress(page, '');
    expect(result).to.contains(boMaintenancePage.successfulUpdateMessage);
  });

  it('should enable the shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableShop', baseContext);

    const result = await boMaintenancePage.changeShopStatus(page);
    expect(result).to.contains(boMaintenancePage.successfulUpdateMessage);
  });

  it('should verify that the shop is enabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyEnabledShop1', baseContext);

    page = await boMaintenancePage.viewMyShop(page);

    const pageContent = await foClassicHomePage.getTextContent(page, foClassicHomePage.content);
    expect(pageContent).to.not.equal(boMaintenancePage.maintenanceText);

    const result = await foClassicHomePage.isHomePage(page);
    expect(result).to.eq(true);
  });
});
