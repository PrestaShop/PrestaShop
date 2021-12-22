require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const generalPage = require('@pages/BO/shopParameters/general');
const maintenancePage = require('@pages/BO/shopParameters/general/maintenance');

// Import FO pages
const homePage = require('@pages/FO/home');

const baseContext = 'functional_BO_shopParameters_general_maintenance_enableDisableShop';

let browserContext;
let page;

const newMaintenanceText = 'Maintenance';

/*
Disable shop
Update maintenance text
Update ip address in maintenance
Enable shop
 */
describe('BO - Shop Parameters - General - Maintenance : Enable/Disable shop', async () => {
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
    await expect(pageTitle).to.contains(generalPage.pageTitle);
  });

  it('should go to \'Maintenance\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToMaintenancePage', baseContext);

    await generalPage.goToSubTabMaintenance(page);
    const pageTitle = await maintenancePage.getPageTitle(page);
    await expect(pageTitle).to.contains(maintenancePage.pageTitle);
  });

  it('should disable the shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableShop', baseContext);

    const result = await maintenancePage.changeShopStatus(page, false);
    await expect(result).to.contains(maintenancePage.successfulUpdateMessage);
  });

  it('should verify the existence of the maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyMaintenanceText', baseContext);

    page = await maintenancePage.viewMyShop(page);

    const pageContent = await homePage.getTextContent(page, homePage.content);
    await expect(pageContent).to.equal(maintenancePage.maintenanceText);
  });

  it('should update the maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateMaintenanceText', baseContext);

    // Go back to BO
    page = await homePage.closePage(browserContext, page, 0);

    const result = await maintenancePage.changeMaintenanceTextShopStatus(page, newMaintenanceText);
    await expect(result).to.contains(maintenancePage.successfulUpdateMessage);
  });

  it('should verify that the maintenance text is updated successfully', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyNewMaintenanceText', baseContext);

    page = await maintenancePage.viewMyShop(page);

    const pageContent = await homePage.getTextContent(page, homePage.content);
    await expect(pageContent).to.equal(newMaintenanceText);
  });

  it('should go back to the default maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'backToDefaultMaintenanceText', baseContext);

    page = await homePage.closePage(browserContext, page, 0);

    const result = await maintenancePage.changeMaintenanceTextShopStatus(
      page,
      maintenancePage.maintenanceText,
    );

    await expect(result).to.contains(maintenancePage.successfulUpdateMessage);
  });

  it('should add my IP address in Maintenance ip input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addMyIpAddress', baseContext);

    const result = await maintenancePage.addMyIpAddress(page);
    await expect(result).to.contains(maintenancePage.successfulUpdateMessage);
  });

  it('should verify that the Home page is displayed successfully', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyFOHomePage', baseContext);

    page = await maintenancePage.viewMyShop(page);

    const pageContent = await homePage.getTextContent(page, homePage.content);
    await expect(pageContent).to.not.equal(maintenancePage.maintenanceText);

    const result = await homePage.isHomePage(page);
    await expect(result).to.be.true;
  });

  it('should delete the maintenance ip address and enable the shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteIpAddressAndEnableShop', baseContext);

    page = await homePage.closePage(browserContext, page, 0);

    let result = await maintenancePage.addMaintenanceIPAddress(page, ' ');
    await expect(result).to.contains(maintenancePage.successfulUpdateMessage);

    result = await maintenancePage.changeShopStatus(page);
    await expect(result).to.contains(maintenancePage.successfulUpdateMessage);
  });

  it('should verify that the shop is enabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyEnabledShop', baseContext);

    page = await maintenancePage.viewMyShop(page);

    const pageContent = await homePage.getTextContent(page, homePage.content);
    await expect(pageContent).to.not.equal(maintenancePage.maintenanceText);

    const result = await homePage.isHomePage(page);
    await expect(result).to.be.true;
  });
});
