// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import generalPage from '@pages/BO/shopParameters/general';
import maintenancePage from '@pages/BO/shopParameters/general/maintenance';
// Import FO pages
import {homePage} from '@pages/FO/classic/home';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Shop parameters > General\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShopParamsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.shopParametersGeneralLink,
    );
    await generalPage.closeSfToolBar(page);

    const pageTitle = await generalPage.getPageTitle(page);
    expect(pageTitle).to.contains(generalPage.pageTitle);
  });

  it('should go to \'Maintenance\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToMaintenancePage', baseContext);

    await generalPage.goToSubTabMaintenance(page);

    const pageTitle = await maintenancePage.getPageTitle(page);
    expect(pageTitle).to.contains(maintenancePage.pageTitle);
  });

  it('should disable the shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableShop', baseContext);

    const result = await maintenancePage.changeShopStatus(page, false);
    expect(result).to.contains(maintenancePage.successfulUpdateMessage);
  });

  it('should enable store for logged-in employees', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableStoreForLoggedInEmployees', baseContext);

    const result = await maintenancePage.changeStoreForLoggedInEmployees(page, true);
    expect(result).to.contains(maintenancePage.successfulUpdateMessage);
  });

  it('should verify that the shop is enabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyEnabledShop0', baseContext);

    page = await maintenancePage.viewMyShop(page);

    const pageContent = await homePage.getTextContent(page, homePage.content);
    expect(pageContent).to.not.equal(maintenancePage.maintenanceText);

    const result = await homePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should disable store for logged-in employees', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableStoreForLoggedInEmployees', baseContext);

    // Go back to BO
    page = await homePage.closePage(browserContext, page, 0);

    const result = await maintenancePage.changeStoreForLoggedInEmployees(page, false);
    expect(result).to.contains(maintenancePage.successfulUpdateMessage);
  });

  it('should verify the existence of the maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyMaintenanceText', baseContext);

    page = await maintenancePage.viewMyShop(page);

    const pageContent = await homePage.getTextContent(page, homePage.content);
    expect(pageContent).to.equal(maintenancePage.maintenanceText);
  });

  it('should update the maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateMaintenanceText', baseContext);

    // Go back to BO
    page = await homePage.closePage(browserContext, page, 0);

    const result = await maintenancePage.changeMaintenanceTextShopStatus(page, newMaintenanceText);
    expect(result).to.contains(maintenancePage.successfulUpdateMessage);
  });

  it('should verify that the maintenance text is updated successfully', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyNewMaintenanceText', baseContext);

    page = await maintenancePage.viewMyShop(page);

    const pageContent = await homePage.getTextContent(page, homePage.content);
    expect(pageContent).to.equal(newMaintenanceText);
  });

  it('should go back to the default maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'backToDefaultMaintenanceText', baseContext);

    page = await homePage.closePage(browserContext, page, 0);

    const result = await maintenancePage.changeMaintenanceTextShopStatus(
      page,
      maintenancePage.maintenanceText,
    );
    expect(result).to.contains(maintenancePage.successfulUpdateMessage);
  });

  it('should add my IP address in Maintenance ip input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addMyIpAddress', baseContext);

    const result = await maintenancePage.addMyIpAddress(page);
    expect(result).to.contains(maintenancePage.successfulUpdateMessage);
  });

  it('should verify that the Home page is displayed successfully', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyFOHomePage', baseContext);

    page = await maintenancePage.viewMyShop(page);

    const pageContent = await homePage.getTextContent(page, homePage.content);
    expect(pageContent).to.not.equal(maintenancePage.maintenanceText);

    const result = await homePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should delete the maintenance ip address', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteIpAddress', baseContext);

    page = await homePage.closePage(browserContext, page, 0);

    const result = await maintenancePage.addMaintenanceIPAddress(page, '');
    expect(result).to.contains(maintenancePage.successfulUpdateMessage);
  });

  it('should enable the shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableShop', baseContext);

    const result = await maintenancePage.changeShopStatus(page);
    expect(result).to.contains(maintenancePage.successfulUpdateMessage);
  });

  it('should verify that the shop is enabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyEnabledShop1', baseContext);

    page = await maintenancePage.viewMyShop(page);

    const pageContent = await homePage.getTextContent(page, homePage.content);
    expect(pageContent).to.not.equal(maintenancePage.maintenanceText);

    const result = await homePage.isHomePage(page);
    expect(result).to.eq(true);
  });
});
