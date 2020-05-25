require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const GeneralPage = require('@pages/BO/shopParameters/general');
const MaintenancePage = require('@pages/BO/shopParameters/general/maintenance');
const HomePage = require('@pages/FO/home');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_general_maintenance_enableDisableShop';

let browser;
let page;

const newMaintenanceText = 'Maintenance';

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    generalPage: new GeneralPage(page),
    maintenancePage: new MaintenancePage(page),
    homePage: new HomePage(page),
  };
};

/*
Disable shop
Update maintenance text
Update ip address in maintenance
Enable shop
 */
describe('Enable/Disable shop', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to maintenance page
  loginCommon.loginBO();

  it('should go to \'Shop parameters > General\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShopParamsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.shopParametersParentLink,
      this.pageObjects.dashboardPage.shopParametersGeneralLink,
    );

    await this.pageObjects.generalPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.generalPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.generalPage.pageTitle);
  });

  it('should go to \'Maintenance\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToMaintenancePage', baseContext);

    await this.pageObjects.generalPage.goToSubTabMaintenance();
    const pageTitle = await this.pageObjects.maintenancePage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.maintenancePage.pageTitle);
  });

  it('should disable the shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableShop', baseContext);

    const result = await this.pageObjects.maintenancePage.changeShopStatus(false);
    await expect(result).to.contains(this.pageObjects.maintenancePage.successfulUpdateMessage);
  });

  it('should verify the existence of the maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyMaintenanceText', baseContext);

    page = await this.pageObjects.maintenancePage.viewMyShop();
    this.pageObjects = await init();

    const pageContent = await this.pageObjects.homePage.getTextContent(this.pageObjects.homePage.content);
    await expect(pageContent).to.equal(this.pageObjects.maintenancePage.maintenanceText);

    // Go back to BO
    page = await this.pageObjects.homePage.closePage(browser, 0);
    this.pageObjects = await init();
  });

  it('should update the maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateMaintenanceText', baseContext);

    const result = await this.pageObjects.maintenancePage.changeMaintenanceTextShopStatus(newMaintenanceText);
    await expect(result).to.contains(this.pageObjects.maintenancePage.successfulUpdateMessage);
  });

  it('should verify the existence of the new maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyNewMaintenanceText', baseContext);

    page = await this.pageObjects.maintenancePage.viewMyShop();
    this.pageObjects = await init();

    const pageContent = await this.pageObjects.homePage.getTextContent(this.pageObjects.homePage.content);
    await expect(pageContent).to.equal(newMaintenanceText);

    page = await this.pageObjects.homePage.closePage(browser, 0);
    this.pageObjects = await init();
  });

  it('should back to the default maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'backToDefaultMaintenanceText', baseContext);

    const result = await this.pageObjects.maintenancePage.changeMaintenanceTextShopStatus(
      this.pageObjects.maintenancePage.maintenanceText,
    );

    await expect(result).to.contains(this.pageObjects.maintenancePage.successfulUpdateMessage);
  });

  it('should add my IP address in Maintenance ip input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addMyIpAddress', baseContext);

    const result = await this.pageObjects.maintenancePage.addMyIpAddress();
    await expect(result).to.contains(this.pageObjects.maintenancePage.successfulUpdateMessage);
  });

  it('should verify that the Home page is displayed successfully', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyFOHomePage', baseContext);

    page = await this.pageObjects.maintenancePage.viewMyShop();
    this.pageObjects = await init();

    const pageContent = await this.pageObjects.homePage.getTextContent(this.pageObjects.homePage.content);
    await expect(pageContent).to.not.equal(this.pageObjects.maintenancePage.maintenanceText);

    const result = await this.pageObjects.homePage.isHomePage();
    await expect(result).to.be.true;

    page = await this.pageObjects.homePage.closePage(browser, 0);
    this.pageObjects = await init();
  });

  it('should delete the maintenance ip address and enable the shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteIpAddressAndEnableShop', baseContext);

    let result = await this.pageObjects.maintenancePage.addMaintenanceIPAddress(' ');
    await expect(result).to.contains(this.pageObjects.maintenancePage.successfulUpdateMessage);

    result = await this.pageObjects.maintenancePage.changeShopStatus();
    await expect(result).to.contains(this.pageObjects.maintenancePage.successfulUpdateMessage);
  });

  it('should verify that the shop is enabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyEnabledShop', baseContext);

    page = await this.pageObjects.maintenancePage.viewMyShop();
    this.pageObjects = await init();

    const pageContent = await this.pageObjects.homePage.getTextContent(this.pageObjects.homePage.content);
    await expect(pageContent).to.not.equal(this.pageObjects.maintenancePage.maintenanceText);

    const result = await this.pageObjects.homePage.isHomePage();
    await expect(result).to.be.true;

    page = await this.pageObjects.homePage.closePage(browser, 0);
    this.pageObjects = await init();
  });
});
