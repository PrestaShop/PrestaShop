require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const GeneralPage = require('@pages/BO/shopParameters/general');
const MaintenancePage = require('@pages/BO/shopParameters/general/maintenance');
const HomePage = require('@pages/FO/home');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParams_general_maintenance_enbaleDisableShop';

let browser;
let page;
const newMaintenanceText = 'Maintenance';

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
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
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.shopParametersGeneralLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
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
    page = await this.pageObjects.boBasePage.viewMyShop();
    this.pageObjects = await init();
    const pageContent = await this.pageObjects.homePage.getTextContent(this.pageObjects.homePage.content);
    await expect(pageContent).to.equal(this.pageObjects.maintenancePage.maintenanceText);
    page = await this.pageObjects.homePage.closePage(browser, 1);
    this.pageObjects = await init();
  });

  it('should update the maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateMaintenanceText', baseContext);
    const result = await this.pageObjects.maintenancePage.changeMaintenanceTextShopStatus(newMaintenanceText);
    await expect(result).to.contains(this.pageObjects.maintenancePage.successfulUpdateMessage);
  });

  it('should verify the existence of the new maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyNewMaintenanceText', baseContext);
    page = await this.pageObjects.boBasePage.viewMyShop();
    this.pageObjects = await init();
    const pageContent = await this.pageObjects.homePage.getTextContent(this.pageObjects.homePage.content);
    await expect(pageContent).to.equal(newMaintenanceText);
    page = await this.pageObjects.homePage.closePage(browser, 1);
    this.pageObjects = await init();
  });

  it('should back to the default maintenance text', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'verifyNewMaintenanceText', baseContext);
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
    page = await this.pageObjects.boBasePage.viewMyShop();
    this.pageObjects = await init();
    const pageContent = await this.pageObjects.homePage.getTextContent(this.pageObjects.homePage.content);
    await expect(pageContent).to.not.equal(this.pageObjects.maintenancePage.maintenanceText);
    const result = await this.pageObjects.homePage.isHomePage();
    await expect(result).to.be.true;
    page = await this.pageObjects.homePage.closePage(browser, 1);
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
    page = await this.pageObjects.boBasePage.viewMyShop();
    this.pageObjects = await init();
    const pageContent = await this.pageObjects.homePage.getTextContent(this.pageObjects.homePage.content);
    await expect(pageContent).to.not.equal(this.pageObjects.maintenancePage.maintenanceText);
    const result = await this.pageObjects.homePage.isHomePage();
    await expect(result).to.be.true;
    page = await this.pageObjects.homePage.closePage(browser, 1);
    this.pageObjects = await init();
  });
});
